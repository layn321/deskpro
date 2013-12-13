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
 * @subpackage RefGenerator
 */

namespace Application\DeskPRO\RefGenerator;

interface RefGeneratorInterface
{

	/**
	 * Generates a new reference number for the supplied object type.
	 * Reference numbers must be at MOST 25 characters, and must be unique.
	 *
	 * @param  $object_type
	 * @return string
	 */
	public function generateReference($object_type);

	/**
	 * Check if a string is a valid ref format. This only checks
	 * the format, no checking if it exists or anything like that.
	 * 
	 * @param string $ref
	 * @return bool
	 */
	public function isRefMatch($ref);

	/**
	 * Try to find all refs in a body of text and return an array of
	 * found matches.
	 *
	 * The order doesnt matter. But usually implementations will check refs
	 * in the order they appear in the array. So if there is such thing as priority,
	 * the first one should be the most likely match.
	 *
	 * @param string $string
	 * @param string $ldelim The left delimeter that wraps the ref
	 * @param string $rdelim The right delimeter that wraps the ref
	 * @return string[]
	 */
	public function extractRefs($string, $ldelim = '\b', $rdelim = '\b');
}
