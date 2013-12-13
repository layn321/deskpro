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

use Application\DeskPRO\App;

use Orb\Util\Strings;

class RandomRef implements RefGeneratorInterface
{
	/**
	 * @var \Application\DeskPRO\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Application\DeskPRO\DBAL
	 */
	protected $db;

	public function __construct(\Doctrine\ORM\EntityManager $em)
	{
		$this->em = $em;
		$this->db = $em->getConnection();
	}

	public function generateReference($entity_name)
	{
		$table = $this->em->getClassMetadata(App::getEntityClass($entity_name))->getTableName();
		$field = 'ref';

		$stmt = $this->db->prepare("SELECT COUNT(*) FROM `$table` WHERE `$field` = ? LIMIT 1");

		do {
			$ref = Strings::random(4, Strings::CHARS_ALPHA_IU) . '-' . Strings::random(4, Strings::CHARS_NUM) . '-' . Strings::random(4, Strings::CHARS_ALPHA_IU);

			$stmt->execute(array($ref));
			$count = $stmt->fetchColumn();
		} while ($count > 0);

		return $ref;
	}

	/**
	 * Check if a string is a valid ref format. This only checks
	 * the format, no checking if it exists or anything like that.
	 *
	 * @param string $ref
	 * @return bool
	 */
	public function isRefMatch($ref)
	{
		return preg_match('#^([A-Z0-9]{4}\-[A-Z0-9]{4}\-[A-Z0-9]{4})$#', $ref);
	}

	/**
	 * Try to find all refs in a body of text and return an array of
	 * found matches.
	 *
	 * The order doesnt matter. But usually implementations will check refs
	 * in the order they appear in the array. So if there is such thing as priority,
	 * the first one should be the most likely match.
	 *
	 * @param $string
	 * @return string[]
	 */
	public function extractRefs($string, $ldelim = '\b', $rdelim = '\b')
	{
		$m = null;
		if (preg_match_all('#('.$ldelim.')([A-Z0-9]{4}\-[A-Z0-9]{4}\-[A-Z0-9]{4})('.$rdelim.')#', $string, $m, \PREG_PATTERN_ORDER)) {
			return $m[2];
		}

		return array();
	}
}
