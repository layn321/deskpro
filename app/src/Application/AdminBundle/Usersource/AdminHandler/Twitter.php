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
 * @category Usersources
 */

namespace Application\AdminBundle\Usersource\AdminHandler;

use Orb\Util\Strings;
use Orb\Util\Arrays;

/**
 * The setup classes handle showing the user a wizard, and then taking input and
 * transforming it (if necessary) into adapter options.
 */
class Twitter extends AbstractAdminHandler
{
	/**
	 * Return an array of fields we need to add to the form.
	 *
	 * @return array
	 */
	protected function buildFormFields()
	{
		$fields = array();

		$f = new \Orb\Form\Field\Text(array(
			'name' => 'consumer_key',
			'attributes' => array('size' => 50)
		));
		$fields[] = $f;

		$f = new \Orb\Form\Field\Text(array(
			'name' => 'consumer_secret',
			'attributes' => array('size' => 50)
		));
		$fields[] = $f;

		return $fields;
	}
}