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
 */

namespace Application\DeskPRO\ContactData;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\ContactDataAbstract;

use Orb\Util\Arrays;
use Orb\Util\Strings;
use Orb\Util\Util;

class Facebook extends AbstractContactData
{
	/**
	 * Apply form data to a contact record
	 *
	 * @param array $input
	 * @param \Application\DeskPRO\Entity\ContactDataAbstract $contact_record
	 */
	public function applyFormData(array $input, ContactDataAbstract $contact_record)
	{
		$contact_record->comment = isset($input['comment']) ? $input['comment'] : '';
		$contact_record->field_1 = $input['profile_url'];

		if (preg_match('#/profile\.php?id=([0-9]+)#', $input['profile_url'], $m)) {
			$contact_record->field_2 = $m[1];
		} elseif (preg_match('#facebook\.com/([a-zA-Z0-9\.\-_]+)#', $input['profile_url'], $m)) {
			$contact_record->field_2 = $m[1];
		} elseif (preg_match('#facebook\.com/people/([a-zA-Z0-9\.\-_]+)#', $input['profile_url'], $m)) {
			$contact_record->field_2 = $m[1];
		} else {
			$contact_record->field_2 = Strings::extractRegexMatch('#(facebook\.com.*?)$#', $input['profile_url'], $m);
		}

		if (!$contact_record->field_2) {
			$contact_record->field_2 = '';
		}
	}

	/**
	 * Return an array of values that are useful in a template
	 *
	 * @return array
	 */
	public function getTemplateVars(ContactDataAbstract $contact_record)
	{
		return array(
			'comment'     => $contact_record->comment,
			'profile_url' => $contact_record->field_1,
			'display'     => $contact_record->field_2 ?: $contact_record->field_1
		);
	}

	/**
	 * Return an array of values that are useful to the API
	 *
	 * @return array
	 */
	public function getApiVars(ContactDataAbstract $contact_record)
	{
		return array(
			'profile_url' => $contact_record->field_1,
			'display'     => $contact_record->field_2 ?: $contact_record->field_1
		);
	}
}
