<?php
/**************************************************************************\
| DeskPRO (r) has been developed by DeskPRO Ltd. http://www.deskpro.com/   |
| a British company located in London, England.                            |
|                                                                          |
| All source code and content Copyright (c) 2012, DeskPRO Ltd.             |
|                                                                          |
| The license agreement under which this software is released              |
| can be found at http://www.deskpro.com/license                           |
|                                                                          |
| By using this software, you acknowledge having read the license          |
| and agree to be bound thereby.                                           |
|                                                                          |
| Please note that DeskPRO is not free software. We release the full       |
| source code for our software because we trust our users to pay us for    |
| the huge investment in time and energy that has gone into both creating  |
| this software and supporting our customers. By providing the source code |
| we preserve our customers' ability to modify, audit and learn from our   |
| work. We have been developing DeskPRO since 2001, please help us make it |
| another decade.                                                          |
|                                                                          |
| Like the work you see? Think you could make it better? We are always     |
| looking for great developers to join us: http://www.deskpro.com/jobs/    |
|                                                                          |
| ~ Thanks, Everyone at Team DeskPRO                                       |
\**************************************************************************/

/**
 * DeskPRO
 *
 * @package DeskPRO
 * @subpackage Twig
 */

namespace Application\DeskPRO\Twig;

use Application\DeskPRO\Twig\Loader\HybridLoader;
use DeskPRO\Kernel\KernelErrorHandler;

class Environment extends \Twig_Environment
{
	protected $ext_dirty = false;

	public function __construct(\Twig_LoaderInterface $loader = null, $options = array())
	{
		static $has_done = false;

		if (defined('DP_DEBUG') && (empty($options['auto_reload']) || $options['auto_reload'] === null)) {
			if (DP_DEBUG) {
				$options['auto_reload'] = true;
			} else {
				$options['auto_reload'] = false;
			}
		}

		if (!$has_done) {
			stream_wrapper_register('dptpl', 'Application\\DeskPRO\\Twig\\Loader\\DbStreamWrapper', 0);
		}

		$options['base_template_class'] = '\\Application\\DeskPRO\\Twig\\Template';

		parent::__construct($loader, $options);
	}

	public function addExtension(\Twig_ExtensionInterface $extension)
	{
		parent::addExtension($extension);
		$this->ext_dirty = true;
	}

	public function setExtensions(array $extensions)
	{
		parent::setExtensions($extensions);
		$this->ext_dirty = true;
	}

	public function getExtensions()
	{
		// Ensures the DeskPRO filters and functions are always used over the default
		if ($this->ext_dirty) {
			$this->ext_dirty = false;

			$set_ext = array();
			$append_ext = array();

			foreach ($this->extensions as $k => $ext) {
				if ($ext instanceof \Application\DeskPRO\Twig\Extension\TemplatingExtension || $ext instanceof \Application\UserBundle\Twig\Extension\UserTemplatingExtension) {
					$append_ext[$k] = $ext;
				} else {
					$set_ext[$k] = $ext;
				}
			}
			foreach ($append_ext as $k => $ext) {
				$set_ext[$k] = $ext;
			}

			$this->extensions = $set_ext;
		}

		return $this->extensions;
	}

	public function loadTemplate($name, $index = null)
	{
		$name_str = (string)$name;
		if (!$this->isCustomTemplate($name_str)) {
			return $this->doLoadTemplate($name, $index);
		} else {
			try {
				return $this->doLoadTemplate($name, $index);
			} catch (\Exception $e) {
				$errinfo = \DeskPRO\Kernel\KernelErrorHandler::getExceptionInfo($e);
				$errinfo['no_send_error'] = true;
				\DeskPRO\Kernel\KernelErrorHandler::logErrorInfo($errinfo);

				$this->markCustomTemplateAsCrashed($name_str);

				return $this->loadTemplate($name, $index);
			}
		}
	}

	private function doLoadTemplate($name, $index = null)
    {
		if (!isset($GLOBALS['DP_RENDERED_TEMPLATES'])) {
			$GLOBALS['DP_RENDERED_TEMPLATES'] = array();
		}

		$GLOBALS['DP_RENDERED_TEMPLATES'][(string)$name] = true;

        $cls = $this->getTemplateClass($name, $index);

        if (isset($this->loadedTemplates[$cls])) {
            return $this->loadedTemplates[$cls];
        }

        if (!class_exists($cls, false)) {
            if (false === $cache = $this->getCacheFilename($name)) {
                eval('?>'.$this->compileSource($this->loader->getSource($name), $name));
            } else {
				if (strpos($cache, 'dptpl://') === 0) {
					$tplinfo = \Application\DeskPRO\Twig\Loader\DbStreamWrapper::getTemplateInfo(str_replace('dptpl://load/', '', $cache));
					eval('?>'.$tplinfo['template_compiled']);
				} else {
					if (!is_file($cache) || ($this->isAutoReload() && !$this->isTemplateFresh($name, filemtime($cache)))) {
						$fallback = false;
						$e = null;
						try {
							$this->writeCacheFile($cache, $this->compileSource($this->loader->getSource($name), $name));
							require_once $cache;
						} catch (\Exception $e) {
							$fallback = true;
						}

						if ($fallback) {

							if (!isset($GLOBALS['DP_NOLOG_TPL_CACHE_ERR']) || !$GLOBALS['DP_NOLOG_TPL_CACHE_ERR']) {
								// Fallback on just evalling the template so everything
								$prev = null;
								if ($e) {
									$prev = $e;
								}

								$name_str = (string)$name;
								if (preg_match('#^(UserBundle|AgentBundle|DeskPRO|BillingBundle|InstallBundle|ReportBundle|CloudAdminBundle|CloudBillingBundle):#', $name_str)) {
									if (defined('DP_BUILD_NUM') && !defined('DP_BUILDING')) {
										$e = new \Exception("IMPORTANT: Could not write twig template file for template $name. You should re-download the DeskPRO source files. Contact support@deskpro.com for assistance.", 0, $prev);
										KernelErrorHandler::logException($e, false, 'twig_write_failed');
									}
								}
							}

							$source = $this->compileSource($this->loader->getSource($name), $name);
							eval('?>'.$source);
						}
					} else {
						require_once $cache;
					}
				}
            }
        }

        if (!$this->runtimeInitialized) {
            $this->initRuntime();
        }

        return $this->loadedTemplates[$cls] = new $cls($this);
    }


	/**
	 * If theres a custom template with an error, then
	 * we'll try and use the default template instead.
	 *
	 * @param $name
	 * @return string
	 */
	public function markCustomTemplateAsCrashed($name)
	{
		if ($this->loader->dbHasTemplate($name)) {
			$this->loader->markCustomTemplateAsCrashed($name);
		}
	}

	/**
	 * Check if a particular template is a custom template
	 *
	 * @param $name
	 * @return mixed
	 */
	public function isCustomTemplate($name)
	{
		if ($this->loader instanceof HybridLoader && $this->loader->dbHasTemplate($name)) {
			return true;
		}

		return false;
	}


	public function getCacheFilename($name)
	{
		if (!($this->loader instanceof HybridLoader) || !$this->loader->dbHasTemplate($name)) {
			return parent::getCacheFilename($name);
		}

		return 'dptpl://load/' . $name;
	}

	public function isTemplateFresh($name, $time)
	{
		if ($this->loader->dbHasTemplate($name)) {
			return true;
		}

		return $this->loader->isFresh($name, $time);
	}


	/**
	 * @param $template_code
	 * @param array $vars
	 * @return null|string
	 * @throws \Exception|null
	 */
	public function renderStringTemplate($template_code, array $vars = array())
	{
		$old_loader = $this->getLoader();
		$old_cache  = $this->getCache();

		$arr_loader = new \Twig_Loader_Array(array(
			'template' => $template_code
		));

		$this->setLoader($arr_loader);
		$this->setCache(false);

		$result = null;
		$exception = null;
		try {
			$result = $this->render('template', $vars);
		} catch (\Exception $e) {
			$exception = $e;
		}

		$this->setLoader($old_loader);
		$this->setCache($old_cache);

		if ($exception) {
			throw $exception;
		}

		return $result;
	}
}
