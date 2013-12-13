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

namespace Application\DeskPRO\Form;

use Orb\Util\Arrays;

/**
 * A helper that helps build and respond to the DeskPRO/InlineEdit.js system.
 */
class InlineEditHandler
{
	/**
	 * Input data
	 * @var array
	 */
	protected $input_data;

	/**
	 * A flat array of names we got from input.
	 * array(person => array(basic => array(fullname => xxx, nickname => xxx)))
	 * becomes
	 * array(person.basic.full_name, person.basic.nickname)
	 * @var array
	 */
	protected $got_fields = array();

	/**
	 * @param array $input_data This is the 'data' item of the incoming request
	 */
	public function __construct(array $input_data = array())
	{
		$this->input_data = $input_data;
	}

	protected function _scanInputFieldNames(array $input, $key_parts = array())
	{
		foreach ($input as $k => $v) {
			$key_parts[] = $k;
			if (is_array($v)) {
				$this->_scanInputFieldNames($v, $key_parts);
			} else {
				$this->got_fields[] = implode('.', $key_parts);
			}
			array_pop($key_parts);
		}
	}



	/**
	 * Apply input to a form
	 *
	 * @param \Orb\Form\Field\FieldGroup $form
	 */
	public function applyToForm(\Orb\Form\Field\FieldGroup $form)
	{
		$form->setFormData($this->input_data);
	}
}
