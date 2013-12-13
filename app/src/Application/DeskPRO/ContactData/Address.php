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

class Address extends AbstractContactData
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
		$contact_record->field_1 = isset($input['address']) ? $input['address'] : '';
		$contact_record->field_2 = isset($input['city']) ? $input['city'] : '';
		$contact_record->field_3 = isset($input['state']) ? $input['state'] : '';
		$contact_record->field_4 = isset($input['zip']) ? $input['zip'] : '';
		$contact_record->field_5 = isset($input['country']) ? $input['country'] : '';
	}

	/**
	 * Return an array of values that are useful in a template
	 *
	 * @return array
	 */
	public function getTemplateVars(ContactDataAbstract $contact_record)
	{
		$address_txt = array(
			$contact_record->field_1,
			$contact_record->field_2,
			$contact_record->field_3,
			$contact_record->field_4,
			$contact_record->field_5
		);
		$address_txt = Arrays::removeFalsey($address_txt);
		$address_txt = implode("\n", $address_txt);

		$params = array(
			'sensor' => 'false',
			'size' => '200x200',
			'center' => str_replace("\n", " ", Strings::standardEol($address_txt))
		);
		$google_url = 'https://maps.googleapis.com/maps/api/staticmap?' . http_build_query($params, null, '&amp;');

		return array(
			'comment'      => $contact_record->comment,
			'address'      => $contact_record->field_1,
			'city'         => $contact_record->field_2,
			'state'        => $contact_record->field_3,
			'zip'          => $contact_record->field_4,
			'country'      => $contact_record->field_5,
			'address_html' => nl2br(htmlentities($address_txt)),
			'map_url'      => $google_url,
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
			'address'      => $contact_record->field_1,
			'city'         => $contact_record->field_2,
			'state'        => $contact_record->field_3,
			'zip'          => $contact_record->field_4,
			'country'      => $contact_record->field_5,
		);
	}
}
