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
 * @category Entities
 */

namespace Application\DeskPRO\EntityRepository;

use Application\DeskPRO\App;
use \Doctrine\ORM\EntityRepository;

class LabelDef extends AbstractEntityRepository
{
	/**
	 * Count how many different labels exist
	 *
	 * @param string $type Type or null to count all [distinct] labels across every type
	 * @return int
	 */
	public function countLabels($type = null)
	{
		if ($type) {
			return App::getDb()->fetchColumn("
				SELECT COUNT(*)
				FROM label_defs
				WHERE label_type = ?
			", array($type));
		} else {
			return App::getDb()->fetchColumn("
				SELECT COUNT(DISTINCT label)
				FROM label_defs
			");
		}
	}

	/**
	 * Fetch all labels from the db and which types they're set for.
	 * This returns an array of array('label' => array('type1', 'type2'))
	 *
	 * @return array
	 */
	public function getAllLabelsToTyped()
	{
		$all = App::getDb()->fetchAll("SELECT * FROM label_defs");

		$ret = array();

		foreach ($all as $x) {
			if (!isset($x['label'])) {
				$ret[$x['label']] = array();
			}

			$ret[$x['label']][] = $x['label_type'];
		}

		return $ret;
	}


	/**
	 * Get the top counts for labels of a certain type.
	 *
	 * @return array
	 */
	public function getLabelCounts($type, $limit = 25)
	{
		switch ($type) {
			case 'tickets':
			case 'ticket':
				$label_type = 'tickets';
				break;

			case 'people':
				$label_type = 'people';
				break;

			case 'feedback':
				$label_type = 'feedbacks';
				break;

			case 'news':
				$label_type = 'newss';
				break;

			case 'organizations':
			case 'chat_conversations':
			case 'articles':
			case 'downloads':
				$label_type = $type;
				break;

			default:
				throw new \InvalidArgumentException("`$type` is an invalid label type");
				break;
		}

		return $this->getEntityManager()->getConnection()->fetchAllKeyValue("
			SELECT label, total
			FROM label_defs
			WHERE label_type = ?
			ORDER BY total DESC
			" . ($limit ? "LIMIT $limit" : '') . "
		", array($label_type));
	}

	/**
	 * Get the name of the label entity given a type.
	 *
	 * @static
	 * @param  $label_type
	 * @return null|string
	 */
	public function getLabelEntityFromType($label_type)
	{
		switch ($label_type) {
			case 'organizations':
				return 'DeskPRO:LabelOrganization';
				break;

			case 'people':
				return 'DeskPRO:LabelPerson';
				break;

			case 'tickets':
				return 'DeskPRO:LabelTicket';
				break;

			case 'articles':
				return 'DeskPRO:LabelArticle';
				break;

			case 'feedback':
				return 'DeskPRO:LabelFeedback';
				break;

			case 'downloads':
				return 'DeskPRO:LabelDownload';
				break;

			case 'news':
				return 'DeskPRO:LabelNews';
				break;
		}

		return null;
	}

	/**
	 * A tablename=>entityname array of objects that have label capabiltiies.
	 *
	 * @static
	 * @return array
	 */
	public function getLabelEntities()
	{
		return array(
			'labels_organizations' => 'DeskPRO:LabelOrganization',
			'labels_people'        => 'DeskPRO:LabelPerson',
			'labels_tickets'       => 'DeskPRO:LabelTicket',
			'labels_articles'      => 'DeskPRO:LabelArticle',
			'labels_feedback'         => 'DeskPRO:LabelFeedback',
			'labels_downloads'     => 'DeskPRO:LabelDownload',
			'labels_news'          => 'DeskPRO:LabelNews',
		);
	}
}
