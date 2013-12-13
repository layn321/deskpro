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
 * @subpackage Form
 */

namespace Application\DeskPRO\Form\Captcha;

use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class CaptchaAbstract
{
	/**
	 * The service container
	 *
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	protected $container;

	/**
	 * Array of options
	 *
	 * @var array
	 */
	protected $options = array();


	/**
	 * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
	 * @param array $options
	 */
	final public function __construct(ContainerInterface $container, array $options = array())
	{
		$this->container   = $container;
		$this->options     = $options;

		$this->init();
	}

	protected function init()
	{
		// empty construct hook
	}


	/**
	 * Get the captcha HTML to render into the form page
	 *
	 * @return string
	 */
	abstract function getHtml();

	/**
	 * Validate an incoming and make sure the captcha is correct
	 *
	 * @return bool
	 */
	abstract function validate();


	/**
	 * Get an option
	 *
	 * @param string $name
	 * @param mixed  $default
	 * @return mixed
	 */
	public function getOption($name, $default = null)
	{
		return isset($this->options[$name]) ? $this->options[$name] : null;
	}


	/**
	 * Set an options
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return string
	 */
	public function setOption($name, $value)
	{
		$this->options[$name] = $value;
	}


	/**
	 * Set many options at once
	 *
	 * @param array $options
	 * @return void
	 */
	public function setOptions(array $options)
	{
		$this->options = array_merge($this->options, $options);
	}


	/**
	 * Check if an option is set
	 *
	 * @param string $name
	 * @return bool
	 */
	protected function hasOption($name)
	{
		return isset($this->options[$name]);
	}


	/**
	 * @return \Symfony\Component\Templating\EngineInterface
	 */
	protected function getTemplating()
	{
		return $this->container->get('templating');
	}


	/**
	 * @return \Symfony\Component\HttpFoundation\Request
	 */
	protected function getRequest()
	{
		return $this->container->get('request');
	}


	/**
	 * @return \Application\DeskPRO\HttpFoundation\Session
	 */
	protected function getSession()
	{
		return $this->container->get('session');
	}


	/**
	 * @throws \RunTimeException
	 * @param string $name           The option to try and get first
	 * @param string $setting_name   If $name option doesnt exist, try to fetch it from settings
	 * @return mixed
	 */
	protected function getOptionOrSetting($name, $setting_name)
	{
		if ($this->hasOption($name)) {
			return $this->getOption($name);
		} else {
			if (!$this->container->has('deskpro.core.settings')) {
				throw new \RunTimeException("No option `$name` provided, and there is no `deskpro.core.settings` to get setting `$setting_name`", 10);
			}

			$val = $this->container->get('deskpro.core.settings')->get($setting_name);
			if ($val === null) {
				throw new \RunTimeException("No option `$name` and `deskpro.core.settings` has no value for `$setting_name`", 20);
			}

			return $val;
		}
	}
}
