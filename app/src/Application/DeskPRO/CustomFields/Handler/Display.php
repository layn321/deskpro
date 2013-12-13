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
 * @subpackage CustomFields
 */

namespace Application\DeskPRO\CustomFields\Handler;

use Symfony\Component\EventDispatcher\EventDispatcher;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

/**
 * A display field doesn't actually have any form or anything (unless of course a plugin
 * says it does).
 */
class Display extends HandlerAbstract
{
	public function getFormField($data = null)
	{
		$setData = '';
		$field = App::getFormFactory()->createNamedBuilder('hidden', $this->getFormFieldName(), $setData, array('required' => false));
		return $field;
	}

	function getDataFromForm(array $form_data)
	{
		return array();
	}

	/**
	 * Gets an array of search operation types we can perform against this
	 * field.
	 *
	 * @return array
	 */
	public function getSearchCapabilities()
	{
		// Not searchable by default
		return array();
	}


	/**
	 * Get the type of search this field sholud be on.
	 *
	 * - Text/input have 'input'
	 * - Dates/numric have 'value'
	 * - Fields that use an option go by 'id'
	 *
	 * @return string
	 */
	public function getSearchType()
	{
		return 'display';
	}
}
