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
 * @category ORM
 */

namespace Application\DeskPRO\ORM\Util;

use Doctrine\ORM\PersistentCollection;
use Application\DeskPRO\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Application\DeskPRO\App;

/**
 * Simple utility methods for working with the ORM
 */
class Util
{
	private function __construct() { /* Static class, no instances */ }



	/**
	 * Checks to see if $collection is a valid PersistentCollection, and if it's
	 * been initialized yet.
	 *
	 * @param mixed $collection
	 * @return bool
	 */
	public static function isCollectionInitialized($collection)
	{
		if ($collection instanceof PersistentCollection AND $collection->isInitialized()) {
			return true;
		}

		return false;
	}


	/**
	 * @param \Application\DeskPRO\ORM\EntityManager $em
	 * @return array
	 */
	public static function getUpdateSchemaSql(EntityManager $em = null)
	{
		if ($em === null) {
			$em = App::getOrm();
		}

		$metadata = $em->getMetadataFactory()->getAllMetadata();
		$tool = new SchemaTool($em);

		$arr = $tool->getUpdateSchemaSql($metadata, true);
		$lines = array();
		foreach ($arr as $a) {
			// Doctrine doesnt seem to detect this properly and always thinks this is needed
			if ($a != 'ALTER TABLE email_uids CHANGE id id VARCHAR(100) NOT NULL') {
				if (strpos($a, 'CREATE TABLE') !== false) {
					$a .= ' DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
				}

				$lines[] = $a;
			}
		}

		return $lines;
	}
}
