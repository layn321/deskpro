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
 */

namespace Application\DeskPRO\Routing\Generator;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Generator\UrlGenerator as BaseUrlGenerator;
use Application\DeskPRO\Routing\Generator\ObjectUrlGenerator;
use Orb\Util\Strings;

use Application\DeskPRO\App;
use Symfony\Component\Routing\RequestContext;

/**
 * This URL generator sets a default _locale part with the current Translator locale.
 */
class UrlGenerator extends BaseUrlGenerator
{
	protected $object_url_generator = null;

	public function setContext(RequestContext $context)
    {
		if (defined('DP_INTERFACE') && DP_INTERFACE == 'cli') {
			$deskpro_url = rtrim(App::getSetting('core.deskpro_url'), '/');
			if (!isset($GLOBALS['DP_CONFIG']['rewrite_urls'])) {
				$GLOBALS['DP_CONFIG']['rewrite_urls'] = App::getSetting('core.rewrite_urls');
			}
			if (!$GLOBALS['DP_CONFIG']['rewrite_urls'] && !preg_match('#index\.php$#', $deskpro_url)) {
				$deskpro_url .= '/index.php';
			}

			$info = parse_url($deskpro_url);
			$context->setScheme($info['scheme']);
			if (!empty($info['path'])) {
				$context->setBaseUrl($info['path']);
			} else {
				$context->setBaseUrl('');
			}
			$context->setHost($info['host']);
			$context->setMethod('GET');
			if (!empty($info['port'])) {
				$context->setHttpPort($info['port']);
			}
		}

        $this->context = $context;
    }

	public function generate($name, $parameters = array(), $absolute = false)
	{
		if (App::getEnvironment() == 'dev') {
			return parent::generate($name, $parameters, $absolute);
		}

		// When in prod, eat route not found exceptions because
		// users can mistype them when editing templates
		try {
			return parent::generate($name, $parameters, $absolute);
		} catch (\Symfony\Component\Routing\Exception\RouteNotFoundException $e) {
			return null;
		}
	}

	public function generateUrl($name, $parameters = array())
	{
		$url = $this->generatePath($name, $parameters, false);

		// Make sure index.php is in links
		$deskpro_url = rtrim(App::getSetting('core.deskpro_url'), '/');
		if (!App::getSetting('core.rewrite_urls') && !preg_match('#index\.php$#', $deskpro_url)) {
			$deskpro_url .= '/index.php';
		}

		return $deskpro_url . $url;
	}

	/**
	 * This is like generate() except it returns JUST the route. Nothing to do with the current base
	 * path etc is added. This will begin with a slash.
	 *
	 * @param $name
	 * @param array $parameters
	 * @param bool $absolute
	 */
	public function generatePath($name, $parameters = array(), $absolute = false)
	{
		$url = $this->generate($name, $parameters, $absolute);

		$with_file = false;
		$with_widget = false;
		if (strpos($url, '/file.php/') !== false) {
			$with_file = true;
			$url = str_replace('/file.php/', '/index.php/', $url);
		} elseif (strpos($url, '/dp.php/') !== false) {
			$with_file = true;
			$url = str_replace('/dp.php/', '/index.php/', $url);
		}

		$url = preg_replace('#^' . preg_quote($this->context->getBaseUrl(), '#') . '#', '', $url);

		if ($with_file) {
			$url = '/file.php' . $url;
			$url = str_replace('/file.php/index.php/', '/file.php/', $url);
		} elseif ($with_widget) {
			$url = '/dp.php' . $url;
			$url = str_replace('/dp.php/index.php/', '/dp.php/', $url);
		}

		return $url;
	}

	protected function doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $absolute)
	{
		$url = parent::doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $absolute);

		// /file.php/ is a hint to say that we want to serve through the file loader,
		// Any route that is prefixed with /file.php/ has this magic below applied
		if (strpos($url, '/file.php/') !== false) {
			$url = str_replace('/index.php', '', $url);
		} elseif (strpos($url, '/dp.php/') !== false) {
			$url = str_replace('/dp.php', '', $url);
		}

		return $url;
	}

	public function getObjectUrlGenerator()
	{
		if ($this->object_url_generator !== null) return $this->object_url_generator;

		$this->object_url_generator = new ObjectUrlGenerator($this);
		return $this->object_url_generator;
	}

	public function generateObjectUrl($object, array $params = array(), $context = null)
	{
		return $this->getObjectUrlGenerator()->generateObjectUrl($object, $params, $context);
	}

	/*
    protected function doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $absolute)
    {
		if (isset($variables['_locale']) && empty($defaults['_locale'])) {
			App::getTranslator()->getLocale()->getLocale();

			// Default
			if ($defaults['_locale'] == 'en_US') {
				unset($defaults['_locale']);
			}
		}

		return parent::doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $absolute);
    }
	*/
}
