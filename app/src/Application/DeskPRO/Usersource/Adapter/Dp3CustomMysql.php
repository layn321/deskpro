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

namespace Application\DeskPRO\Usersource\Adapter;

use Orb\Auth\Identity;

class Dp3CustomMysql extends DbTablePhpPasswordCheck
{
	/**
	 * @return \Orb\Auth\Adapter\DbTablePhpPasswordCheck
	 */
	protected function _createAuthAdapterObject()
	{
		$options = $this->usersource->options;

		// Bit of adapter code to convert Dp3 eval code format into the new one
		$options['password_php'] =  '
			$password_check = $input = $password_input;
			'.$options['password_php'].'
			$pass = ($password_check == $userinfo_password);
		';

		return new \Orb\Auth\Adapter\DbTablePhpPasswordCheck($this->getDb(), $options);
	}
}