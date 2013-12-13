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
 * @category ContentSearch
 */

namespace Application\DeskPRO\ContentSearch;

use Application\DeskPRO\App;

/**
 * When an entity is updated, this should be called from a post* method to update its index
 */
class IndexUpdater
{
	/**
	 * @var \Application\DeskPRO\ContentSearch\ContentSearchable
	 */
	protected $entity;

	public function __construct(\Application\DeskPRO\ContentSearch\ContentSearchable $entity)
	{
		$this->entity = $entity;
	}

	public function updateIndex()
	{
		App::getDb()->beginTransaction();

		App::getDb()->delete('content_search', array('id' => $this->entity->getSearchId()));
		App::getDb()->delete('content_search_attributes', array('id' => $this->entity->getSearchId()));

		App::getDb()->insert('content_search', array(
			'id' => $this->entity->getSearchId(),
			'content' => $this->entity->getSearchContent()
		));

		$attr = $this->entity->getSearchAttributes();
		if ($attr) {
			foreach ($attr as $k => $v) {
				App::getDb()->insert('content_search', array(
					'search_id' => $this->entity->getSearchId(),
					'attribute_id' => $k,
					'content' => $v
				));
			}
		}

		App::getDb()->commit();
	}
}
