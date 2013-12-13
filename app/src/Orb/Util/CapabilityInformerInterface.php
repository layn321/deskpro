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
 * @category Util
 */

namespace Orb\Util;

/**
 * An object that implements this interface is able to tell about its own capabilities.
 * 
 * For example, useful with adapters when various features might not be supported between each adapter
 * and implementation code will need to check.
 *
 * Here's an example usage:
 * <code>
 * $searcher = $this->getSearchAdapter();
 * if (!$searcher->isCapable(Searcher::FIND_TAGS)) {
 *     die('Sorry, this feature is not available');
 * }
 * </code>
 *
 * Actual capabilities are usually represented using strings, but it's recommended
 * these strings be defined as class constants.
 */
interface CapabilityInformerInterface
{
	/**
	 * Returns an array of all capabilities
	 *
	 * @return array
	 */
	public function getCapabilities();


	/**
	 * Check if this object is capable of a specific thing
	 * 
	 * @param  mixed $capability
	 * @return bool
	 */
	public function isCapable($capability);
}
