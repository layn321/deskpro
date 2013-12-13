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
 * @subpackage AdminBundle
 */

namespace Application\AdminBundle\Form\CustomField\Model;

use Application\DeskPRO\App;

class DateField extends CustomFieldAbstract
{
    public $default_value = '';
    public $default_mode = 'current';
	public $required = false;
	public $agent_required  = false;
	public $date_timezone   = null;

	public $date_valid_type       = null;
	public $date_valid_date1      = null;
	public $date_valid_date2      = null;
	public $date_valid_range1     = null;
	public $date_valid_range2     = null;
	public $date_valid_dow        = null;

    public function init()
    {
        $this->default_value = $this->_field->default_value;
        $this->default_mode = $this->_field->getOption('default_mode');

        if(empty($this->default_value)) {
            $this->default_value = date('m/d/Y');
        }

        if(empty($this->default_mode)) {
            $this->default_mode = 'current';
        }

		if ($this->_field->getOption('required')) {
			$this->required = true;
		}
		if ($this->_field->getOption('agent_required')) {
			$this->agent_required = true;
		}

		if ($this->_field->getOption('date_valid_dow')) {
			$this->date_valid_dow = $this->_field->getOption('date_valid_dow');
		} else {
			$this->date_valid_dow = range(0, 6);
		}

		if ($this->_field->getOption('date_valid_type')) {
			$this->date_valid_type = $this->_field->getOption('date_valid_type');

			if ($this->date_valid_type == 'date' && ($this->_field->getOption('date_valid_date1') || $this->_field->getOption('date_valid_date2'))) {
				$this->date_valid_date1  = $this->_field->getOption('date_valid_date1') ?: null;
				$this->date_valid_date2  = $this->_field->getOption('date_valid_date2') ?: null;
			} elseif ($this->date_valid_type == 'range' && ($this->_field->getOption('date_valid_range1') || $this->_field->getOption('date_valid_range2'))) {
				$this->date_valid_range1 = $this->_field->getOption('date_valid_range1') ?: null;
				$this->date_valid_range2 = $this->_field->getOption('date_valid_range2') ?: null;
			} else {
				$this->date_valid_type = null;
			}
		}
    }

    protected function setFieldProperties()
    {
        $field = $this->_field;
        $field->default_value = $this->default_value;
        $field->setOption('default_mode', $this->default_mode);

		$field->setOption('required', (bool)$this->required);
		$field->setOption('agent_required', (bool)$this->agent_required);

		if ($this->date_valid_dow && count($this->date_valid_dow) != 7) {
			$field->setOption('date_valid_dow', $this->date_valid_dow);
		} else {
			$field->setOption('date_valid_dow', null);
		}

		$field->setOption('date_valid_type', null);
		$field->setOption('date_valid_date1', null);
		$field->setOption('date_valid_date2', null);
		$field->setOption('date_valid_range1', null);
		$field->setOption('date_valid_range2', null);

		$this->date_valid_range1 = (int)$this->date_valid_range1;
		$this->date_valid_range2 = (int)$this->date_valid_range2;

		// Date range
		if ($this->date_valid_type == 'date' && ($this->date_valid_date1 || $this->date_valid_date2)) {
			$d1 = $d2 = null;

			// Verify dates
			if ($this->date_valid_date1) {
				try {
					$d1 = \DateTime::createFromFormat('Y-m-d', $this->date_valid_date1);
					if (!$d1) {
						$this->date_valid_date1 = null;
					}
				} catch (\Exception $e) { $this->date_valid_date1 = null; }
			}

			if ($this->date_valid_date2) {
				try {
					$d2 = \DateTime::createFromFormat('Y-m-d', $this->date_valid_date2);
					if (!$d2) {
						$this->date_valid_date2 = null;
					}
				} catch (\Exception $e) { $this->date_valid_date2 = null; }
			}

			if ($this->date_valid_date1 || $this->date_valid_date2) {

				if ($this->date_valid_date1 && $this->date_valid_date2) {
					if ($d1 > $d2) {
						$tmp = $this->date_valid_date1;
						$this->date_valid_date1 = $this->date_valid_date2;
						$this->date_valid_date2 = $tmp;
					}
				}

				$field->setOption('date_valid_type', 'date');
				$field->setOption('date_valid_date1', $this->date_valid_date1);
				$field->setOption('date_valid_date2', $this->date_valid_date2);
			}

		// Day ranges
		} elseif ($this->date_valid_type == 'range' && ($this->date_valid_range1 || $this->date_valid_range2)) {
			if ($this->date_valid_range1 && $this->date_valid_range2) {
				if ($this->date_valid_range1 > $this->date_valid_range2) {
					$tmp = $this->date_valid_range1;
					$this->date_valid_range1 = $this->date_valid_range2;
					$this->date_valid_range2 = $tmp;
				}
			}

			$field->setOption('date_valid_type', 'range');
			$field->setOption('date_valid_range1', $this->date_valid_range1);
			$field->setOption('date_valid_range2', $this->date_valid_range2);
		}

		$field->setOption('date_valid_timezone', App::getCurrentPerson()->getTimezone());
    }
}
