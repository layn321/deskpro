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

class TextField extends CustomFieldAbstract
{
	public $min_length;
	public $max_length;
	public $regex;

	public $default_value = '';

	public $agent_min_length;
	public $agent_max_length;
	public $agent_regex;

	public function init()
	{
		$this->default_value = $this->_field->default_value;

		if ($this->_field->getOption('min_length')) {
			$this->validation_type = 'required';
			$this->min_length = $this->_field->getOption('min_length');
		}
		if ($this->_field->getOption('max_length')) {
			$this->validation_type = 'required';
			$this->max_length = $this->_field->getOption('max_length');
		}
		if ($this->_field->getOption('regex')) {
			$this->validation_type = 'regex';
			$this->regex = $this->_field->getOption('regex');
		}

		if ($this->_field->getOption('agent_min_length')) {
			$this->agent_validation_type = 'required';
			$this->agent_min_length = $this->_field->getOption('agent_min_length');
		}
		if ($this->_field->getOption('agent_max_length')) {
			$this->agent_validation_type = 'required';
			$this->agent_max_length = $this->_field->getOption('agent_max_length');
		}
		if ($this->_field->getOption('agent_regex')) {
			$this->agent_validation_type = 'regex';
			$this->agent_regex = $this->_field->getOption('agent_regex');
		}
	}

	protected function setFieldProperties()
	{
		$field = $this->_field;

		$field->default_value = $this->default_value;

		if ($this->validation_type == 'required') {
			$field->setOption('required', true);
			$field->setOption('min_length', $this->min_length);
			$field->setOption('max_length', $this->max_length);
		} elseif ($this->validation_type == 'regex') {
			$field->setOption('required', false);

			// No delims
			if ($this->regex[0] != substr($this->regex, -1, 1)) {
				$this->regex = '/' . $this->regex . '/';
			}

			$field->setOption('regex', $this->regex);
		} else {
			$field->setOption('required', null);
			$field->setOption('regex', null);
			$field->setOption('min_length', null);
			$field->setOption('max_length', null);
		}

		if ($this->agent_validation_type == 'required') {
			$field->setOption('agent_required', true);
			$field->setOption('agent_min_length', $this->agent_min_length);
			$field->setOption('agent_max_length', $this->agent_max_length);
		} elseif ($this->agent_validation_type == 'regex') {
			$field->setOption('agent_required', false);

			// No delims
			if ($this->agent_regex[0] != substr($this->agent_regex, -1, 1)) {
				$this->agent_regex = '/' . $this->agent_regex . '/';
			}

			$field->setOption('agent_regex', $this->agent_regex);
		} else {
			$field->setOption('agent_required', null);
			$field->setOption('agent_regex', null);
			$field->setOption('agent_min_length', null);
			$field->setOption('agent_max_length', null);
		}
	}
}
