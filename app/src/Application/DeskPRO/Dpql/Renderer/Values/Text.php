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
 * @subpackage Dpql
 */

namespace Application\DeskPRO\Dpql\Renderer\Values;

/**
 * Value renderer for text-based output.
 */
class Text extends AbstractValues
{
	/**
	 * Renders a null value.
	 *
	 * @return string
	 */
	protected function _renderNull()
	{
		return 'None';
	}

	/**
	 * Renders a boolean value.
	 *
	 * @param boolean $value
	 *
	 * @return string
	 */
	protected function _renderBoolean($value)
	{
		return ($value ? 'Y' : 'N');
	}

	/**
	 * Escapes the value for direct output.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public function escapeValue($value)
	{
		return $value;
	}
}