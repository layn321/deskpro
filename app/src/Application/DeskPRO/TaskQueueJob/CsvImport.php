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
 * @category TaskQueueJob
 */

namespace Application\DeskPRO\TaskQueueJob;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Person;

class CsvImport extends AbstractJob
{
	/**
	 * @var \Application\DeskPRO\Entity\CustomDefPerson[]
	 */
	protected $_custom_fields;

	public function getTitle()
	{
		if ($this->_data['user_filename']) {
			return 'CSV Import: ' . $this->_data['user_filename'];
		} else {
			return 'CSV Import';
		}
	}

	protected function _getDefaultData()
	{
		return array(
			'blob_id' => false,
			'field_maps' => false,
			'new_custom_map' => false,
			'skip_first' => true,
			'welcome_email' => false,
			'welcome_from_name' => '',
			'welcome_from_email' => '',
			'welcome_subject' => '',
			'welcome_message' => '',
			'imported' => 0,
			'lines_done' => 0,
			'fseek' => 0,
			'user_filename' => ''
		);
	}

	public function run($max_time)
	{
		$blob = App::getOrm()->find('DeskPRO:Blob', $this->_data['blob_id']);

		$csv_file = dp_get_tmp_dir() . '/blob-' . $blob->getId() . '.csv';

		if (!file_exists($csv_file) || !is_readable($csv_file)) {
			file_put_contents($csv_file, App::getContainer()->getBlobStorage()->copyBlobRecordToString($blob));
		}

		if (!file_exists($csv_file) || !is_readable($csv_file)) {
			throw new \Exception("CSV file $csv_file does not exist or is not readable");
		}

		if ($this->_data['new_custom_map'] === false) {
			$this->_createNewCustomFields();
		}

		$this->_custom_fields = App::getEntityRepository('DeskPRO:CustomDefPerson')->getTopFields();

		$start_time = microtime(true);

		$fp = fopen($csv_file, 'r');
		fseek($fp, $this->_data['fseek']);

		if ($this->_data['fseek'] == 0 && $this->_data['skip_first']) {
			// skip the first row - it's labels
			fgetcsv($fp);
		}

		$complete = false;
		$imported = 0;

		while (microtime(true) - $start_time < $max_time) {
			if (feof($fp)) {
				$complete = true;
				break;
			}

			$row = fgetcsv($fp);
			if (!$row) {
				$complete = true;
				break;
			}

			$this->_data['lines_done']++;

			if ($this->_importRow($row)) {
				$this->_data['imported']++;
				$imported++;
			}
		}

		$this->_data['fseek'] = ftell($fp);

		fclose($fp);

		if ($this->getLogger()) {
			$this->getLogger()->logDebug("Imported $imported people");
		}

		$this->getTask()->run_status = "Processed " . $this->_data['lines_done']
			. " entries, imported " . $this->_data['imported'] . " people";

		if ($complete) {
			@unlink($csv_file);
			try {
				App::getContainer()->getBlobStorage()->deleteBlobRecord($blob);
			} catch (\Exception $e) {}
			return self::TASK_COMPLETED;
		} else {
			return self::TASK_CONTINUING;
		}
	}

	protected function _createNewCustomFields()
	{
		$this->_data['new_custom_map'] = array();

		foreach ($this->_data['field_maps'] AS $column_id => $info) {
			if ($info['map'] == 'new_custom') {
				$field = new \Application\DeskPRO\Entity\CustomDefPerson();
				$field->title = $info['title'];
				$field->handler_class = $info['handler_class'];
				$field->display_order = $column_id;

				App::getOrm()->persist($field);
				App::getOrm()->flush();

				$this->_data['new_custom_map'][$column_id] = $field->id;
			}
		}
	}

	protected function _importRow(array $row)
	{
		$field_maps = $this->_data['field_maps'];

		if (isset($row[0]) && $row[0] === null) {
			return false;
		}

		$em = App::getOrm();

		/** @var $person_em \Application\DeskPRO\EntityRepository\Person */
		$person_em = App::getEntityRepository('DeskPRO:Person');

		/** @var $organization_em \Application\DeskPRO\EntityRepository\Organization */
		$organization_em = App::getEntityRepository('DeskPRO:Organization');

		$person = new Person();

		$primary_email = false;
		$password = false;
		$secondary_emails = array();
		$addresses = array();

		foreach ($field_maps AS $column_id => $info) {
			if (!$info['map']) {
				continue;
			}

			$column_value = $row[$column_id];
			if ($column_value === '') {
				continue;
			}

			if ($info['map'] == 'primary_email') {

				if (!\Orb\Validator\StringEmail::isValueValid($column_value) || App::getSystemService('gateway_address_matcher')->isManagedAddress($column_value)) {
					continue;
				}
				if ($person_em->findOneByEmail($column_value)) {
					continue;
				}

				$primary_email = strtolower($column_value);
			} else if ($info['map'] == 'secondary_email') {
				if (!\Orb\Validator\StringEmail::isValueValid($column_value) || App::getSystemService('gateway_address_matcher')->isManagedAddress($column_value)) {
					break;
				}
				if ($person_em->findOneByEmail($column_value)) {
					break;
				}

				$secondary_emails[] = strtolower($column_value);
			}
		}

		if (!$primary_email) {
			$primary_email = array_shift($secondary_emails);
		}

		if (!$primary_email) {
			return false;
		}

		$person->addEmailAddressString($primary_email);

		array_unique($secondary_emails);
		foreach ($secondary_emails AS $secondary_email) {
			if ($secondary_email == $primary_email) {
				continue;
			}
			$person->addEmailAddressString($secondary_email);
		}

		foreach ($field_maps AS $column_id => $info) {
			if (!$info['map']) {
				continue;
			}

			$column_value = $row[$column_id];
			if ($column_value === '') {
				continue;
			}

			$map_field = $info['map'];
			$label = isset($info['label']) ? $info['label'] : '';

			switch ($map_field) {
				case 'first_name':
				case 'last_name':
				case 'name':
				case 'title_prefix':
				case 'organization_position':
					$person->$map_field = $column_value;
					break;

				case 'password':
					$password = $column_value;
					break;

				case 'organization':
					$organization = $organization_em->findOneByName($column_value);
					if ($organization) {
						$person->setOrganization($organization);
					} else if (!empty($info['create_auto'])) {
						$organization = new \Application\DeskPRO\Entity\Organization();
						$organization->name = $column_value;

						$em->persist($organization);

						$person->setOrganization($organization);
					}
					break;

				case 'website':
					$this->_addContactData($person, 'website', array('url' => $column_value), $label);
					break;

				case 'twitter':
					$this->_addContactData($person, 'twitter', array('username' => $column_value), $label);
					break;

				case 'linkedin':
					$this->_addContactData($person, 'linkedin', array('profile_url' => $column_value), $label);
					break;

				case 'facebook':
					$this->_addContactData($person, 'facebook', array('profile_url' => $column_value), $label);
					break;

				case 'phone':
					$this->_addContactData($person, 'phone', array('type' => $info['type'], 'number' => $column_value), $label);
					break;

				case 'im':
					$this->_addContactData($person, 'instant_message', array('service' => $info['type'], 'username' => $column_value), $label);
					break;

				case 'address':
				case 'address1':
				case 'address2':
				case 'city':
				case 'state':
				case 'post_code':
				case 'country':
					if (!isset($addresses[$info['label']])) {
						$addresses[$info['label']] = array('label' => $info['label']);
					}
					$addresses[$info['label']][$map_field] = $column_value;
					break;

				default:
					$custom_field_id = false;
					if (preg_match('/^custom_(\d+)$/', $map_field, $match)) {
						$custom_field_id = $match[1];
						$new_on_unknown = !empty($info['new_on_unknown']);
					} else if ($map_field == 'new_custom') {
						$custom_field_id = $this->_data['new_custom_map'][$column_id];
						$new_on_unknown = true;
					}

					if ($custom_field_id && isset($this->_custom_fields[$custom_field_id])) {
						$custom_field = $this->_custom_fields[$custom_field_id];
						if ($custom_field->isChoiceType()) {
							$selected_child = false;
							$test_value = strtolower($column_value);

							// find an existing option by title
							foreach ($custom_field->getAllChildren() AS $child_field) {
								if (strtolower($child_field->getTitle()) == $test_value) {
									$selected_child = $child_field;
									break;
								}
							}

							// create a new one if necessary
							if (!$selected_child && $new_on_unknown) {
								$selected_child = new \Application\DeskPRO\Entity\CustomDefPerson();
								$selected_child->title = $column_value;
								$selected_child->display_order = count($custom_field->getAllChildren()) + 1;
								$custom_field->addChild($selected_child);

								$em->persist($selected_child);
							}

							// associate it
							if ($selected_child) {
								$custom_data = new \Application\DeskPRO\Entity\CustomDataPerson();
								$custom_data->person = $person;
								$custom_data->field = $selected_child;
								$custom_data->root_field = $custom_field;
								$custom_data->value = 1;

								$em->persist($custom_data);
								$person->addCustomData($custom_data);
							}
						} if ($custom_field->getTypeName() == 'date') {
							if (ctype_digit($column_value)) {
								// assume timestamp
								$set_field = true;
							} else if (preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $column_value)) {
								$set_field = true;

								$date = \DateTime::createFromFormat('Y-m-d', $column_value,
									new \DateTimeZone(App::getSetting('core.default_timezone'))
								);
								$date = \Orb\Util\Dates::convertToUtcDateTime($date);

								$column_value = $date->getTimestamp();
							} else {
								$set_field = false;
							}

							if ($set_field) {
								$custom_data = new \Application\DeskPRO\Entity\CustomDataPerson();
								$custom_data->person = $person;
								$custom_data->field = $custom_field;
								$custom_data->root_field = $custom_field;
								$custom_data->value = $column_value;

								$em->persist($custom_data);
								$person->addCustomData($custom_data);
							}
						} else {
							$custom_data = new \Application\DeskPRO\Entity\CustomDataPerson();
							$custom_data->person = $person;
							$custom_data->field = $custom_field;
							$custom_data->root_field = $custom_field;
							$custom_data->value = 0;
							$custom_data->input = $column_value;

							$em->persist($custom_data);
							$person->addCustomData($custom_data);
						}
					}
			}
		}

		if ($password === false) {
			$password = \Orb\Util\Strings::random(10);
		}
		$person->setPassword($password);

		foreach ($addresses AS $address) {
			if (!isset($address['address'])) {
				$address['address'] = trim((isset($address['address1']) ? $address['address1'] : '') . "\n" . (isset($address['address2']) ? $address['address2'] : ''));
			}
			$this->_addContactData($person, 'address', $address, $address['label']);
		}

		$em->persist($person);
		$em->flush();

		if ($this->_data['welcome_email'] && !defined('DPC_IS_CLOUD')) {
			$mailer = App::getContainer()->getMailer();

			$message = $mailer->createMessage();
			$message->setToPerson($person);
			$message->setFrom($this->_data['welcome_from_email'], $this->_data['welcome_from_name']);
			$message->setSubject($this->_data['welcome_subject']);
			$message->setBody($this->_replaceMessagePlaceholders($this->_data['welcome_message'], $person));

			$mailer->send($message);
		}

		return $person->id;
	}

	protected function _replaceMessagePlaceholders($message, Person $person)
	{
		$message = preg_replace_callback('/\{\{\s*([a-z0-9_-]+)\s*\}\}/i', function ($match) use ($person) {
			switch (strtolower($match[1])) {
				case 'name': return $person->getDisplayName();
				case 'email': return $person->getPrimaryEmailAddress();
				case 'password': return $person->getPlaintextPassword();
				default: return $match[0];
			}
		}, $message);

		return $message;
	}

	protected function _addContactData(Person $person, $type, array $data, $comment = null)
	{
		if ($comment !== null) {
			$data['comment'] = $comment;
		}

		$contact = new \Application\DeskPRO\Entity\PersonContactData();
		$contact->contact_type = $type;
		$contact->applyFormData($data);
		$contact->person = $person;

		App::getOrm()->persist($contact);

		return $contact;
	}
}