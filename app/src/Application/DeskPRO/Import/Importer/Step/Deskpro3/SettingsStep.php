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
 * @subpackage Import
 */

namespace Application\DeskPRO\Import\Importer\Step\Deskpro3;

class SettingsStep extends AbstractDeskpro3Step
{
	/**
	 * @var \Application\DeskPRO\Import\Importer\Deskpro3Importer
	 */
	public $importer;

	public static function getTitle()
	{
		return 'Import Settings';
	}

	public function run($page = 1)
	{
		$dp3_settings = $this->getOldDb()->fetchAllKeyValue("SELECT name, value FROM settings");

		$timezone = \Orb\Util\Dates::timezoneOffsetToName($dp3_settings['timezone'], (bool)((int)$dp3_settings['dst']));
		if (!$timezone) {
			$timezone = 'UTC';
		}

		$reg_mode = 'open';
		if (!$dp3_settings['allow_registration']) {
			$reg_mode = 'closed';
		}

		$save_settings = array(
			'core.default_from_email' => $dp3_settings['email_from'],
			'core.site_url'           => $dp3_settings['site_url'],
			'core.site_name'          => $dp3_settings['site_name'],
			'core.deskpro_name'       => $dp3_settings['site_name'],
			'core.deskpro_url'        => rtrim(preg_replace('#index\.php/?$#', '', $dp3_settings['helpdesk_url']), '/') . '/',
			'core.date_fulltime'      => $dp3_settings['date_full'],
			'core.date_day'           => $dp3_settings['date_day'],
			'core.date_time'          => $dp3_settings['date_time'],
			'core.dp3_license'        => $dp3_settings['license'],
			'core.dp3_install_time'   => $dp3_settings['install_timestamp'],
			'core.reg_url'            => isset($dp3_settings['register_url']) ? $dp3_settings['register_url'] : '',
			'core.default_timezone'   => $timezone,
			'core.user_mode'          => $reg_mode,
			'user.portal_enabled'     => 1,
		);

		$this->getDb()->beginTransaction();
		try {
			foreach ($save_settings as $sk => $sv) {
				$this->getDb()->replace('settings', array(
					'name'       => $sk,
					'value'      => $sv,
				));
			}

			$this->importTransport($dp3_settings);

			$this->getDb()->commit();
		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}
	}

	public function importTransport($dp3_settings)
	{
		$settings = new \Orb\Util\OptionsArray($dp3_settings);

		if ($settings->get('use_smtp')) {
			$type = 'smtp';
			$transport_options = array(
				'host'     => $settings->get('smtp_host', 'localhost'),
				'secure'   => $settings->get('smtp_ssl') ? 'ssl' : false,
				'port'     => $settings->get('smtp_port', 25),
				'username' => $settings->get('smtp_user', false),
				'password' => $settings->get('smtp_pass', false),
			);
		} else {
			$type = 'mail';
			$transport_options = array();
		}

		$rec = array(
			'title'                      => 'Imported Transport',
			'match_type'                 => 'all',
			'match_pattern'              => '',
			'transport_type'             => $type,
			'transport_options'          => serialize($transport_options),
			'run_order'                  => 0
		);

		$this->getDb()->insert('email_transports', $rec);
	}
}
