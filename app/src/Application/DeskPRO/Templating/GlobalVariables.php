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
 * @subpackage Templating
 */

namespace Application\DeskPRO\Templating;

use Application\DeskPRO\App;

use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables as BaseGlobalVariables;

class GlobalVariables extends BaseGlobalVariables
{
	protected $variables = array();

	public function setVariable($name, $value)
	{
		$this->variables[$name] = $value;
	}

	public function getLicense()
	{
		return \DeskPRO\Kernel\License::getLicense();
	}

	public function getVariable($name)
	{
		return isset($this->variables[$name]) ? $this->variables[$name] : null;
	}

	public function getUser()
	{
		return App::getCurrentPerson();
	}

	public function getSetting($name)
	{
		return App::getSetting($name);
	}

	public function getSettingGroup($group)
	{
		$group_vars = App::get('deskpro.core.settings')->getGroup($group);

		if ($group == 'user_style') {
			if (defined('DPC_IS_CLOUD')) {
				// Always use https URLs on cloud
				$group_vars['static_path'] = 'https://' . DPC_SITE_DOMAIN . '/web';
			} else {
				// External blob storage means we need ot use a full URL for assets
				if (!App::getConfig('static_path') && App::getContainer()->getBlobStorage()->getPreferredAdapterId() == 's3') {
					$url = App::getSetting('core.deskpro_url');
					$url = str_replace('index.php', '', $url);
					$url = trim($url, '/');

					$group_vars['static_path'] = $url . '/web';
				} else {
					// A custom defined static URL
					if (App::getConfig('static_path')) {
						$group_vars['static_path'] = rtrim(App::getConfig('static_path'), '/');

					// Default static path relative to current
					} else {
						$group_vars['static_path'] = rtrim('../..' . (App::getConfig('static_path') ?: '/web/'), '/');
					}
				}
			}
		}

		return $group_vars;
	}

	public function getConfig($name, $default = null)
	{
		return App::getConfig($name, $default);
	}

	public function getSession()
	{
		return App::getSession();
	}

	public function getVisitor()
	{
		return App::getSession()->getVisitor();
	}

	public function getLanguage()
	{
		return App::getLanguage();
	}

	public function isDebug()
	{
		return App::isDebug();
	}

	public function getStyle()
	{
		return App::getSystemService('style');
	}

	public function getLogoBlob()
	{
		return App::getSystemService('logo_blob');
	}

	public function getUsersourceManager()
	{
		return App::getSystemService('UsersourceManager');
	}

	public function getTicketFieldManager()
	{
		return App::getSystemService('TicketFieldsManager');
	}

	public function getPersonFieldManager()
	{
		return App::getSystemService('PersonFieldsManager');
	}

	public function getOrgFieldManager()
	{
		return App::getSystemService('OrgFieldsManager');
	}

	/**
	 * Used only for backwards comptat
	 * @deprecated
	 */
	public function getDataRepository($ent)
	{
		return App::getSystemService("{$ent}Data");
	}

	public function getDataService($ent)
	{
		return App::getDataService($ent);
	}

	public function getDepartments()
	{
		return App::getDataService('Department');
	}

	public function getAgents()
	{
		return App::getDataService('Agent');
	}

	public function agent_teams()
	{
		return App::getDataService('AgentTeam');
	}
	public function getAgentTeams()
	{
		return App::getDataService('AgentTeam');
	}

	public function getUsersources()
	{
		return App::getDataService('Usersource');
	}

	public function getUsergroups()
	{
		return App::getDataService('Usergroup');
	}

	public function getLanguages()
	{
		return App::getDataService('Language');
	}

	public function getProducts()
	{
		return App::getDataService('Product');
	}

	public function getCustomFieldManager($type)
	{
		switch ($type) {
			case 'tickets':
				return App::getSystemService('ticket_fields_manager');
			case 'people':
				return App::getSystemService('person_fields_manager');
		}

		return null;
	}

	public function getBrowserSniffer()
	{
		return App::get('browser_sniffer');
	}

	public function get($name)
	{
		return $this->__get($name);
	}

	public function __get($name)
	{
		if (isset($this->variables[$name])) {
			return $this->variables[$name];
		}

		if (method_exists($this, $name)) {
			return $this->$name;
		}
		if (method_exists($this, "get$name")) {
			return $this->{"get$name"};
		}

		if ($ent = \Orb\Util\Strings::extractRegexMatch('#^(.*?)Data$#', $name, 1)) {
			return App::getContainer()->getSystemService(ucfirst($ent) . 'Data');
		}

		return null;
	}

	public function __call($method, $args)
	{
		if ($var = \Orb\Util\Strings::extractRegexMatch('#^get(.*?)$#', $method, 1)) {
			return $this->__get(ucfirst($method));
		}

		return null;
	}

	public function __isset($name)
	{
		return isset($this->variables[$name]);
	}

	public function getLastException()
	{
		if (!App::has('deskpro.exception_logger')) {
			return null;
		}

		$logger = App::get('deskpro.exception_logger');
		return $logger->getLastException();
	}

	public function getTimezoneList()
	{
		static $tz = null;

		if ($tz === null) {
			$tz = array_combine(\DateTimeZone::listIdentifiers(), \DateTimeZone::listIdentifiers());
		}

		return $tz;
	}

	public function getReturnUrl()
	{
		$request = App::getRequest();

		// Cant recreate a post, so back to home
		if ($request->getMethod() == 'POST') {
			return App::getSetting('core.deskpro_url');
		}

		return $request->getRequestUri();
	}

	public function isCloud()
	{
		return defined('DPC_IS_CLOUD');
	}

	public function isPluginInstalled($id)
	{
		return App::getContainer()->getPlugins()->isPluginInstalled($id);
	}

	public function getPluginService($id)
	{
		return App::getContainer()->getPlugins()->getPluginService($id);
	}

	public function __toString()
	{
		return '[app]';
	}
}
