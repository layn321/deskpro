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
 * @subpackage Form
 */

namespace Application\DeskPRO\CustomFields\Handler;

use Application\DeskPRO\Entity;
use Application\DeskPRO\App;
use Orb\Util\Dates;


/**
 * Handles the date field
 */
class Date extends HandlerAbstract
{
	public function renderHtml($data = null, array $template_vars = array())
	{
		if ($data === null) return '';

		if (!ctype_digit($data['value'])) {
			$data['value'] = time();
		}

		$data['value'] = new \DateTime('@' . $data['value']);
		return parent::renderText($data, $template_vars);
	}

	public function renderText($data = null, array $template_vars = array())
	{
		if ($data === null) return '';

		if (!ctype_digit($data['value'])) {
			$data['value'] = time();
		}

		$data['value'] = new \DateTime('@' . $data['value']);
		return  parent::renderText($data, $template_vars);
	}

	function getDataFromForm(array $form_data)
	{
		$name = $this->getFormFieldName();

		$value = null;
		if (!empty($form_data[$name])) {
			$value = $form_data[$name];
		}

		if (!$value) {
			return array();
		}

		$date = \DateTime::createFromFormat('Y-m-d', $value, App::getCurrentPerson()->getDateTimezone());
		if (!$date) {
			return array();
		}

		$date = \Orb\Util\Dates::convertToUtcDateTime($date);

		return array(
			array($this->field_def['id'], 'value', $date->getTimestamp())
		);
	}

	public function getFormField($data = null)
	{
		$setData = null;
		if ($data AND !empty($data['value'])) {
			try {
				$date = new \DateTime('@' . $data['value']);
				$date->setTimezone(App::getCurrentPerson()->getDateTimezone());
				//$setData = $date->getTimestamp();
				$setData = $date->format('Y-m-d');
			} catch (\Exception $e) {
				$setData = null;
			}
		}

		$field = App::getFormFactory()->createNamedBuilder('text', $this->getFormFieldName(), $setData, array(
			'required' => false
		));

		return $field;
	}

	public function validateFormData(array $form_data, $context = self::CONTEXT_USER, $context_data = null)
	{
		$data = isset($form_data[$this->getFormFieldName()]) ? $form_data[$this->getFormFieldName()] : '';

		if ($data && !is_scalar($data)) {
			return $this->makeErrorArray(array('invalid_input'));
		}

		// Timestamp value
		if (strlen($data) == 10 && ctype_digit($data)) {
			$data = date('Y-m-d', $data);
		}

		#------------------------------
		# Validate options
		#------------------------------

		$opt_prefix = '';
		if ($context == self::CONTEXT_AGENT) {
			$opt_prefix = 'agent_';
		}

		$options = array();
		foreach (array('required') as $k) {
			$options[$k] = $this->field_def->getOption($opt_prefix . $k);
		}

		if ($options['required']) {
			if (!$data) {
				return $this->makeErrorArray(array('required'));
			}
		}

		if ($data) {
			$date = \DateTime::createFromFormat('Y-m-d', $data);
			if (!$date) {
				return $this->makeErrorArray(array('invalid_input'));
			}
		}

		#------------------------------
		# Validate ranges
		#------------------------------

		if ($data) {
			try {
				$admin_tz = new \DateTimeZone($this->field_def->getOption('date_valid_timezone'));
			} catch (\Exception $e) {
				$admin_tz = App::getCurrentPerson()->getDateTimezone();
			}
			$date = \DateTime::createFromFormat('Y-m-d', $data, App::getCurrentPerson()->getDateTimezone());
			$date_admin = clone $date;
			$date_admin->setTimezone($admin_tz);

			$dow = intval($date_admin->format('N')) - 1;

			// Days of week
			if ($valid_dow = $this->field_def->getOption('date_valid_dow')) {
				if (!in_array($dow, $valid_dow)) {
					return $this->makeErrorArray(array('invalid_date_dow'));
				}
			}

			// Specific date ranges
			if ($this->field_def->getOption('date_valid_type') == 'date') {
				$d1 = $this->field_def->getOption('date_valid_date1');
				$d2 = $this->field_def->getOption('date_valid_date2');

				if ($d1) {
					$d1 = \DateTime::createFromFormat('Y-m-d', $d1, $admin_tz);
					$d1->setTime(0,0,0);

					if ($date_admin < $d1) {
						return $this->makeErrorArray(array('invalid_date_range'));
					}
				}
				if ($d2) {
					$d2 = \DateTime::createFromFormat('Y-m-d', $d2, $admin_tz);
					$d2->setTime(23,59,59);

					if ($date_admin > $d2) {
						return $this->makeErrorArray(array('invalid_date_range'));
					}
				}

			// "Days from now"
			} elseif ($this->field_def->getOption('date_valid_type') == 'range') {
				if ($context_data && isset($context_data['exist_ticket'])) {
					$now = clone $context_data['exist_ticket']->date_created;
					$now->setTimezone($admin_tz);
				} else {
					$now = new \DateTime('now', $admin_tz);
				}

				$days1 = $this->field_def->getOption('date_valid_range1');
				$days2 = $this->field_def->getOption('date_valid_range2');

				if ($days1) {
					$d1 = clone $now;
					$d1->modify("{$days1} days");
					$d1->setTime(0,0,0);

					// Go back if we hit on a unselectable date
					if ($valid_dow) {
						$x = 0;
						while ($x++ < 5000) {
							$check_dow = intval($d1->format('N')) - 1;
							if (in_array($check_dow, $valid_dow)) {
								break;
							}

							$d1->modify('-1 day');
						}
					}

					if ($date_admin < $d1) {
						return $this->makeErrorArray(array('invalid_date_range'));
					}
				}
				if ($days2) {
					$d2 = clone $now;
					$d2->modify("{$days1} days");
					$d2->setTime(23,59,59);

					// Go back if we hit on a unselectable date
					if ($valid_dow) {
						$x = 0;
						while ($x++ < 5000) {
							$check_dow = intval($d2->format('N')) - 1;
							if (in_array($check_dow, $valid_dow)) {
								break;
							}

							$d2->modify(\DateInterval::createFromDateString('1 day'));
						}
					}

					if ($date_admin > $d2) {
						return $this->makeErrorArray(array('invalid_date_range'));
					}
				}
			}
		}

		return array();
	}
}
