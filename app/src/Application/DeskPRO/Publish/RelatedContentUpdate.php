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
 * @subpackage Addons
 */

namespace Application\DeskPRO\Publish;

use Application\DeskPRO\App;

class RelatedContentUpdate
{
	protected $entity;
	protected $type;

	protected $db;

	public function __construct($entity)
	{
		$this->entity = $entity;
		$this->type = $entity->getTableName();

		$this->db = App::getDb();
	}

	public function addRelated($type, $id)
	{
		$this->removeRelated($type, $id);
		$this->db->insert('related_content', array(
			'object_type' => $this->type,
			'object_id' => $this->entity->id,
			'rel_object_type' => $type,
			'rel_object_id' => $id
		));
	}

	public function removeRelated($type, $id)
	{
		$params = array(
			// For checking other object linked to this object
			$type,
			$id,
			$this->type,
			$this->entity->id,

			// For checking this object linked to other
			$this->type,
			$this->entity->id,
			$type,
			$id
		);

		$this->db->executeUpdate("
			DELETE FROM related_content
			WHERE
				(
					object_type = ?
					AND object_id = ?
					AND rel_object_type = ?
					AND rel_object_id = ?
				)
				OR
				(
					object_type = ?
					AND object_id = ?
					AND rel_object_type = ?
					AND rel_object_id = ?
				)
			LIMIT 1
		", $params);
	}
}
