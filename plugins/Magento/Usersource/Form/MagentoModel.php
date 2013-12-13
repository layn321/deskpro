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

namespace Magento\Usersource\Form;

use Application\DeskPRO\Entity\Usersource;

class MagentoModel
{
	public $title;
	public $lost_password_url;
	public $sso_cookie;
	public $magento_path;
	public $sso_js;

	protected $_usersource = null;

	public function __construct(Usersource $usersource)
	{
		if ($usersource) {
			$this->_usersource = $usersource;

			$this->title = $usersource->title;
			$this->lost_password_url = $usersource->lost_password_url;
		}

		$this->sso_cookie = $usersource->getOption('sso_cookie', 0);
		$this->magento_path = $usersource->getOption('magento_path', '');
		$this->sso_js = $usersource->getOption('sso_js', 0);
	}

	public function save(\Application\DeskPRO\ORM\EntityManager $em)
	{
		$this->_usersource->title = $this->title;
		$this->_usersource->lost_password_url = $this->lost_password_url ?: '';

		$this->_usersource->setOption('sso_cookie', intval($this->sso_cookie));
		$this->_usersource->setOption('magento_path', $this->magento_path);
		$this->_usersource->setOption('sso_js', intval($this->sso_js));

		$em->persist($this->_usersource);
		$em->flush();
	}
}