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
 * Orb
 *
 * @package Orb
 * @category Input
 */

namespace Orb\Input\Reader\Source;

/**
 * A reader source is a thing that reads variables from somewhere for use with the
 * input reader.
 */
interface SourceInterface
{
	/**
	 * Get the value of some variable in the source.
	 *
	 * If $name is an array, then each item if the name and subsequent
	 * keys of an array in the source. For example, if:
	 * <var>$name = array('user', 'name');</var>
	 * Then:
	 * <var>$value = $mysource['user']['name'];</var>
	 *
	 * @param   string|array  $name     The name of the variable
	 * @return  mixed
	 */
	public function getValue($name);



	/**
	 * Check if a value of some variable is set in the source.
	 *
	 * $name follows same rules as getValue().
	 *
	 * @param   string|array  $name     The name of the variable
	 * @return  bool
	 */
	public function checkIsset($name);
}
