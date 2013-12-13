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

namespace Application\DeskPRO\Labels;

use Application\DeskPRO\App;
use Orb\Util\Strings;

class LabelLister
{
	protected $label_type;

	/**
	 * @param string $label_type
	 */
	public function __construct($label_type)
	{
		$this->label_type = $label_type;
	}



	/**
	 * Gets an index of labels by index=>array(lables). THe index is usually the
	 * letter, but maybe not depending on language. (?)
	 *
	 * @return array
	 */
	public function getIndexList()
	{
		$index = array();

		$statement = App::getDb()->executeQuery("
			SELECT DISTINCT(label)
			FROM labels_{$this->label_type}
			ORDER BY label ASC
		");

		while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
			$label = $row['label'];

			$first = Strings::utf8_substr($label, 0, 1);
			$first = Strings::utf8_accents_to_ascii($first);
			$first = Strings::utf8_strtoupper($first);

			if (is_numeric($first)) {
				$first = '#';
			} elseif (!preg_match('#[A-Z]#', $first)) {
				$first = '@';
			}

			if (!isset($index[$first])) {
				$index[$first] = array();
			}

			$index[$first][] = $label;
		}

		return $index;
	}
}
