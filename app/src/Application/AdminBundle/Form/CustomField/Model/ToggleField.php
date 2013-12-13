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

class ToggleField extends CustomFieldAbstract
{
	public $default_value = '';
	public $label_text = '';

	public function init()
	{
		$this->default_value = $this->_field->default_value == '1' ? true : false;
		$this->label_text = $this->_field->getOption('label_text') ?: '';
	}

	protected function setFieldProperties()
	{
		$field = $this->_field;

		$field->default_value = $this->default_value;

		if ($this->label_text) {
			$field->setOption('label_text', $this->label_text);
		} else {
			$field->setOption('label_text', null);
		}
	}
}
