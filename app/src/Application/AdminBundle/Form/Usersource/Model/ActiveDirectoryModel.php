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

class ActiveDirectoryModel
{
	protected $_usersource = null;

	public $title;
	public $secure;
	public $port;
	public $host;
	public $baseDn;
	public $username;
	public $password;
	public $accountDomainName;
	public $accountDomainNameShort;
	public $accountFilterFormat;
	public $lost_password_url;


	public function __construct(Usersource $usersource = null)
	{
		if ($usersource) {
			$this->_usersource = $usersource;

			$this->title = $usersource->title;
			$this->lost_password_url = $usersource->lost_password_url;

			$fields = array('port', 'host', 'baseDn', 'username', 'password', 'accountDomainName', 'accountDomainNameShort', 'accountFilterFormat');
			foreach ($fields as $f) {
				$this->$f = $usersource->getOption($f, null);
			}

			if ($usersource->getOption('useSsl')) {
				$this->secure = 'useSsl';
			} elseif ($usersource->getOption('useStartTls')) {
				$this->secure = 'useStartTls';
			} else {
				$this->secure = false;
			}
		}
	}

	public function save(\Application\DeskPRO\ORM\EntityManager $em)
	{
		$this->_usersource->title = $this->title;
		$this->_usersource->lost_password_url = $this->lost_password_url ?: '';

		$options = array(
			'host'                   => $this->host,
			'port'                   => $this->port,
			'baseDn'                 => $this->baseDn,
			'username'               => $this->username,
			'password'               => $this->password ?: $this->_usersource->getOption('password'),
			'accountDomainName'      => $this->accountDomainName,
			'accountDomainNameShort' => $this->accountDomainNameShort,
			'accountFilterFormat'    => $this->accountFilterFormat
		);

		if ($this->secure == 'useSsl') {
			$options['useSsl'] = true;
		} elseif ($this->secure == 'useStartTls') {
			$options['useStartTls'] = true;
		}

		$this->_usersource->setOptions($options, true);

		$em->persist($this->_usersource);
		$em->flush();
	}
}