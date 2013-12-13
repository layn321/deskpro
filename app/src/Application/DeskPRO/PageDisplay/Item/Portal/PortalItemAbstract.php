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
 * @subpackage PageDisplay
 */

namespace Application\DeskPRO\PageDisplay\Item\Portal;

use Application\DeskPRO\Entity\Person;

use Application\DeskPRO\PageDisplay\Item\ItemAbstract;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Application\DeskPRO\People\PersonContextInterface;

abstract class PortalItemAbstract extends ItemAbstract implements PersonContextInterface
{
	/**
	 * The section context. Many items can live either in the content column
	 * or the sidebar column, and they behave differently depending on where.
	 *
	 * @var string
	 */
	protected $section;

	/**
	 * The controller requesting the portal item
	 *
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	protected $container;

	/**
	 * The user who is viewing the item
	 *
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person_context;

	/**
	 * @var array
	 */
	protected $options = array();

	public function __construct($section, array $options, ContainerInterface $container, Person $person_context)
	{
		$this->section = $section;
		$this->container = $container;
		$this->person_context = $person_context;

		$this->options = $options;

		$this->init();
	}

	/**
	 * Hook method called from constructor
	 *
	 * @return void
	 */
	protected function init()
	{

	}

	/**
	 * @param \Application\DeskPRO\Entity\Person $person
	 * @return void
	 */
	public function setPersonContext(Person $person)
	{
		$this->person_context = $person;
	}


	/**
	 * Get the HTML for this item that'll be outputted into the page
	 *
	 * @return string
	 */
	abstract public function getHtml();


	/**
	 * Check the current person context to see if theyre allowed to see this block
	 *
	 * @return bool
	 */
	public function checkPermission()
	{
		return true;
	}


	/**
	 * Get an array of CSS assets that this item requires
	 *
	 * @return array
	 */
	public function getCssAssets()
	{
		return array();
	}


	/**
	 * Get an array of JS assets that this item requires
	 *
	 * @return array
	 */
	public function getJsAssets()
	{
		return array();
	}


	/**
	 * Render a view to string
	 *
	 * @param string $view
	 * @param array $parameters
	 * @return string
	 */
	public function renderView($view, array $parameters = array())
	{
		return $this->container->get('templating')->render($view, $parameters);
	}


	/**
	 * Execute a sub-request and then get the string result.
	 *
	 * @param string $controller
	 * @param array $path
	 * @param array $query
	 * @param null $response If provided, the Response object will be put into this
	 * @return string
	 */
	public function renderForward($controller, array $path = array(), array $query = array(), &$response = null)
	{
		$response = $this->container->get('http_kernel')->forward($controller, $path, $query);

		return $response->getContent();
	}


	public function getOption($name, $default = null)
	{
		return isset($this->options[$name]) ? $this->options[$name] : $default;
	}

	/**
	 * Gets an option but makes sure its not empty ('', 0, false etc), otherwise returns default.
	 *
	 * @param $name
	 * @param mixed $default
	 * @return mixed
	 */
	public function getValueOption($name, $default = null)
	{
		return !empty($this->options[$name]) ? $this->options[$name] : $default;
	}

	public function setOption($name, $value)
	{
		$this->options[$name] = $value;
	}

	public function hasOption($name)
	{
		return isset($this->options[$name]);
	}
}
