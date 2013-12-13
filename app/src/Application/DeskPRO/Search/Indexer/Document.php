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
 * @category Search
 */

namespace Application\DeskPRO\Search\Indexer;

/**
 * A document represents something that we'll insert into the index.
 */
class Document implements DocumentInterface
{
	/**
	 * @var int
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $content_type;

	/**
	 * Data array of properties
	 * @var array
	 */
	protected $data;

	/**
	 * @var bool
	 */
	protected $mark_removed = false;


	/**
	 * @param array $info
	 * @return \Application\DeskPRO\Search\Indexer\Document
	 */
	public static function newFromArray(array $info)
	{
		$id = $info['id'];
		$content_type = $info['content_type'];

		unset($info['id'], $info['content_type']);

		$do_remove = false;
		if (isset($info['remove'])) {
			$do_remove = true;
			unset($info['remove']);
		}

		$obj = new self($id, $content_type, $info);

		if ($do_remove) {
			$obj->markRemove();
		}

		return $obj;
	}


	public function __construct($id, $content_type, array $data = array())
	{
		$this->id           = $id;
		$this->content_type = $content_type;
		$this->data         = $data;
	}

	/**
	 * Get the unique ID for this document in the index
	 *
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * Get the type of document
	 *
	 * @return string
	 */
	public function getContentTypeName()
	{
		return $this->content_type;
	}


	/**
	 * Get the data to index
	 *
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}


	/**
	 * Mark document for removal
	 */
	public function markRemove()
	{
		$this->mark_removed = true;
	}


	/**
	 * @return bool
	 */
	public function isMarkedRemove()
	{
		return $this->mark_removed;
	}
}
