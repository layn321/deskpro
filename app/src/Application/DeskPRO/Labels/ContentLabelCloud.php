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
use Orb\Util\Arrays;

class ContentLabelCloud
{
	protected $cloud = null;
	
	public function getCloud()
	{
		if ($this->cloud !== null) return $this->cloud;

		$counts = array(
			'articles'     => App::getEntityRepository('DeskPRO:LabelDef')->getLabelCounts('articles', 25),
			'feedback'        => App::getEntityRepository('DeskPRO:LabelDef')->getLabelCounts('feedback', 25),
			'downloads'    => App::getEntityRepository('DeskPRO:LabelDef')->getLabelCounts('downloads', 25),
			'news'         => App::getEntityRepository('DeskPRO:LabelDef')->getLabelCounts('news', 25),
		);

		$label_counts = array();
		foreach ($counts as $type_counts) {
			foreach ($type_counts as $label => $count) {
				if (!isset($label_counts[$label])) $label_counts[$label] = 0;
				$label_counts[$label] += $count;
			}
		}

		asort($label_counts, SORT_NUMERIC);
		if (count($label_counts) > 25) {
			$label_counts = Arrays::spliceAssoc($label_counts, 0, 25);
		}

		$cloud_gen = new \Application\DeskPRO\UI\TagCloud($label_counts);
		$this->cloud = $cloud_gen->getCloud();

		return $this->cloud;
	}
}
