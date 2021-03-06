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
 * @subpackage
 */

namespace Application\AdminBundle\Form\Usersource\Model;

use Application\DeskPRO\Entity\Usersource;

class DbTablePhpPasswordCheckModel
{
	protected $_usersource = null;

	public $title;
	public $table;
	public $db_dsn;
	public $db_username;
	public $db_password;
	public $field_id;
	public $field_username;
	public $field_email;
	public $field_password;
	public $field_first_name;
	public $field_last_name;
	public $field_name;
	public $password_php;
	public $lost_password_url;

	public function __construct(Usersource $usersource = null)
	{
		if ($usersource) {
			$this->_usersource = $usersource;

			$this->title = $usersource->title;
			$this->lost_password_url = $usersource->lost_password_url;

			$fields = array(
				'db_dsn',
				'db_username',
				'db_password',
				'table',
				'field_id',
				'field_username',
				'field_email',
				'field_password',
				'field_first_name',
				'field_last_name',
				'field_name',
				'password_php'
			);

			foreach ($fields as $f) {
				$this->$f = $usersource->getOption($f, null);
			}

			if (!$usersource->id) {
				if (!$this->db_dsn) {
					$this->db_dsn = 'mysql:host=localhost;dbname=mydb';
				}
				if (!$this->db_username) {
					$this->db_username = 'root';
					$this->db_password = 'root';
				}
				if (!$this->table) {
					$this->table = 'users';
				}
				if (!$this->password_php || defined('DPC_IS_CLOUD')) {
					$this->password_php = '$pass = ($password_input == $userinfo_password);';
				}
			}
		}
	}

	public function save(\Application\DeskPRO\ORM\EntityManager $em)
	{
		$this->_usersource->title = $this->title;
		$this->_usersource->lost_password_url = $this->lost_password_url ?: '';

		if (!$this->password_php || defined('DPC_IS_CLOUD')) {
			$this->password_php = '$pass = ($password_input == $userinfo_password);';
		}

		$options = array(
			'db_dsn'           => $this->db_dsn,
			'db_username'      => $this->db_username,
			'db_password'      => $this->db_password ?: $this->_usersource->getOption('db_password'),
			'table'            => $this->table,
			'field_id'         => $this->field_id,
			'field_username'   => $this->field_username,
			'field_email'      => $this->field_email,
			'field_password'   => $this->field_password,
			'field_first_name' => $this->field_first_name,
			'field_last_name'  => $this->field_last_name,
			'field_name'       => $this->field_name,
			'password_php'     => $this->password_php
		);

		$this->_usersource->options = $options;

		$em->persist($this->_usersource);
		$em->flush();
	}
}