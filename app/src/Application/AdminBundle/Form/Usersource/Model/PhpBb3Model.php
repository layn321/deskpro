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

class PhpBb3Model extends BaseDbTableModel
{
	public $table_prefix;
	public $check_service_url;
	public $check_service_key;

	protected function init()
	{
		$this->table_prefix = $this->_usersource->getOption('table_prefix', '');
		$this->check_service_url = $this->_usersource->getOption('check_service_url', '');
		$this->check_service_key = $this->_usersource->getOption('check_service_key', '');
	}

	protected function saveApply(\Application\DeskPRO\ORM\EntityManager $em)
	{
		$this->_usersource->setOption('table_prefix', $this->table_prefix);
		$this->_usersource->setOption('check_service_url', $this->check_service_url);
		$this->_usersource->setOption('check_service_key', $this->check_service_key);
	}
}