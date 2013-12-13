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

use Application\DeskPRO\Entity\Usersource;

class UsersourcesStep extends AbstractDeskpro3Step
{
	/**
	 * @var \Application\DeskPRO\Import\Importer\Deskpro3Importer
	 */
	public $importer;

	public $order = 0;

	public static function getTitle()
	{
		return 'Import User Sources';
	}

	public function run($page = 1)
	{
		$all_usersources = $this->olddb->fetchAll("
			SELECT *
			FROM user_source
			WHERE `module` != 'Dp'
			ORDER BY runorder ASC
		");

		$this->getDb()->beginTransaction();
		try {
			foreach ($all_usersources as $usersource) {
				$this->processUsersource($usersource);
			}
			$this->getDb()->commit();
		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}
	}

	public function processUsersource(array $usersource)
	{
		$this->order += 10;

		#------------------------------
		# Make sure we havent already done it
		#------------------------------

		$check_exist = $this->getMappedNewId('usersource', $usersource['id']);
		if ($check_exist) {
			$this->getLogger()->log("{$usersource['id']} already mapped, skipping", 'DEBUG');
			return;
		}

		#------------------------------
		# Create it
		#------------------------------

		$usersource['config'] = @unserialize($usersource['config']);

		$new_usersource = new Usersource();
		$new_usersource->is_enabled     = (bool)$usersource['enabled'];
		$new_usersource->display_order  = $this->order;
		$new_usersource->title          = $usersource['title'];

		switch ($usersource['module']) {

			#---
			# Custom => DbTablePhpPasswordCheck
			#---

			case 'Custom':

				$new_usersource->source_type = 'db_table_php_password_check';
				$new_usersource->options = array(
					'db_dsn'            => 'mysql:host=' . $usersource['config']['db_host'] . ';dbname=' . $usersource['config']['db_name'],
					'db_username'       => $usersource['config']['db_user'],
					'db_password'       => $usersource['config']['db_pass'],
					'table'             => $usersource['config']['table'],
					'field_id'          => $usersource['config']['field_id'],
					'field_username'    => $usersource['config']['field_username'],
					'field_email'       => $usersource['config']['field_email'],
					'field_password'    => $usersource['config']['field_password'],
					'password_php'      => '
						$password = $userinfo_password;
						$password_check = $input = $password_input;
						'.$usersource['config']['password_php'].'
						$pass = ($password_check == $userinfo_password);
					'
				);

				break;

			#---
			# CustomMsSQL => DbTablePhpPasswordCheck
			#---

			case 'CustomMsSQL':

				$new_usersource->source_type = 'db_table_php_password_check';
				$new_usersource->options = array(
					'db_dsn'            => 'sqlsrv:Server=' . $usersource['config']['db_host'] . ';Database=' . $usersource['config']['db_name'],
					'db_username'       => $usersource['config']['db_user'],
					'db_password'       => $usersource['config']['db_pass'],
					'table'             => $usersource['config']['table'],
					'field_id'          => $usersource['config']['field_id'],
					'field_username'    => $usersource['config']['field_username'],
					'field_email'       => $usersource['config']['field_email'],
					'field_password'    => $usersource['config']['field_password'],
					'password_php'      => '
						$password = $userinfo_password;
						$password_check = $input = $password_input;
						'.$usersource['config']['password_php'].'
						$pass = ($password_check == $userinfo_password);
					'
				);

				break;

			#---
			# eZPublish => EzPublish
			#---

			case 'eZPublish':

				$new_usersource->source_type = 'ez_publish';
				$new_usersource->options = array(
					'db_dsn'            => 'mysql:host=' . $usersource['config']['db_host'] . ';dbname=' . $usersource['config']['db_name'],
					'db_username'       => $usersource['config']['db_user'],
					'db_password'       => $usersource['config']['db_pass'],
				);

				break;

			#---
			# LDAP => Dp3Ldap
			#---

			case 'LDAP':

				// Active Directory: Use new AD usersource type
				if ($usersource['config']['ldap_attr_uid'] == 'sAMAccountName') {
					$new_usersource->source_type = 'active_directory';
					$new_usersource->options = array(
						'host'                     => $usersource['config']['ldap_host'],
						'port'                     => $usersource['config']['ldap_port'],
						'baseDn'                   => $usersource['config']['ldap_base_dn'],
						'username'                 => $usersource['config']['ldap_service_dn'],
						'password'                 => $usersource['config']['ldap_service_pass'],
						'accountDomainName'        => '',
						'accountDomainNameShort'   => ''
					);
				} else {
					$new_usersource->source_type = 'dp3_ldap';
					$new_usersource->options = array(
						'host'                     => $usersource['config']['ldap_host'],
						'port'                     => $usersource['config']['ldap_port'],
						'baseDn'                   => $usersource['config']['ldap_base_dn'],
						'username'                 => $usersource['config']['ldap_service_dn'],
						'password'                 => $usersource['config']['ldap_service_pass'],
						'field_id'                 => 'dn',
						'field_username'           => $usersource['config']['ldap_attr_uid'],
						'field_email'              => $usersource['config']['ldap_attr_mail'],
					);
				}

				break;

			#---
			# osCommerce => OsCommerce
			#---

			case 'osCommerce':

				$new_usersource->source_type = 'os_commerce';
				$new_usersource->options = array(
					'db_dsn'            => 'mysql:host=' . $usersource['config']['db_host'] . ';dbname=' . $usersource['config']['db_name'],
					'db_username'       => $usersource['config']['db_user'],
					'db_password'       => $usersource['config']['db_pass'],
				);

				break;

			#---
			# osCommerce => OsCommerce
			#---

			case 'osCommerce':

				$new_usersource->source_type = 'os_commerce';
				$new_usersource->options = array(
					'db_dsn'            => 'mysql:host=' . $usersource['config']['db_host'] . ';dbname=' . $usersource['config']['db_name'],
					'db_username'       => $usersource['config']['db_user'],
					'db_password'       => $usersource['config']['db_pass'],
				);

				break;

			#---
			# phpBB => PhpBb2
			#---

			case 'phpBB':

				$new_usersource->source_type = 'php_bb_2';
				$new_usersource->options = array(
					'db_dsn'            => 'mysql:host=' . $usersource['config']['db_host'] . ';dbname=' . $usersource['config']['db_name'],
					'db_username'       => $usersource['config']['db_user'],
					'db_password'       => $usersource['config']['db_pass'],
					'table_prefix'      => $usersource['config']['table_prefix'],
				);

				break;

			#---
			# phpBB3 => PhpBb3
			#---

			case 'phpBB3':

				$new_usersource->source_type = 'php_bb_3';
				$new_usersource->options = array(
					'db_dsn'            => 'mysql:host=' . $usersource['config']['db_host'] . ';dbname=' . $usersource['config']['db_name'],
					'db_username'       => $usersource['config']['db_user'],
					'db_password'       => $usersource['config']['db_pass'],
					'table_prefix'      => $usersource['config']['table_prefix'],
					'check_service_url' => $usersource['config']['check_login_url'],
					'check_service_key' => '',
				);

				break;

			#---
			# vBulletin => Vbulletin
			#---

			case 'vBulletin':

				$new_usersource->source_type = 'vbulletin';
				$new_usersource->options = array(
					'db_dsn'            => 'mysql:host=' . $usersource['config']['db_host'] . ';dbname=' . $usersource['config']['db_name'],
					'db_username'       => $usersource['config']['db_user'],
					'db_password'       => $usersource['config']['db_pass'],
					'table_prefix'      => $usersource['config']['table_prefix'],
				);

				break;


			#---
			# Unsupported: Joomla, SugarCRM
			#---

			default:
				$this->getLogger()->log("{$usersource['id']} {$usersource['module']} unsupported", 'DEBUG');
				return;
		}

		$this->getEm()->persist($new_usersource);
		$this->getEm()->flush();

		$this->saveMappedId('usersource', $usersource['id'], $new_usersource->id);
	}
}
