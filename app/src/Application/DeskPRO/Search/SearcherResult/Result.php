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

namespace Application\DeskPRO\Search\SearcherResult;

/**
 * Search adapter
 */
class Result implements ResultInterface
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
	 * @var string
	 */
	protected $highlighed;

	/**
	 * @var array
	 */
	protected $data = array();

	public static function newFromArray(array $info)
	{
		$id = $info['id'];
		$content_type = $info['content_type'];
		$highlighted = !empty($info['highlighted']) ? $info['highlighted'] : null;

		unset($info['id'], $info['content_type'], $info['highlighted']);

		return new self($id, $content_type, $info, $highlighted);
	}

	public function __construct($id, $content_type, array $data = array(), $highlighted = null)
	{
		$this->id           = $id;
		$this->content_type = $content_type;
		$this->highlighed   = $highlighted;
		$this->data         = $data;
	}
	
	/**
	 * Get a preview that highlights the search term, or null if there is no highlight.
	 * (Either unspoorted, or the kind of search doesn't have a highlight).
	 *
	 * @return string
	 */
	public function getHighlight()
	{
		return $this->highlighed;
	}

	/**
	 * Get all result data, generally used with transformers to fetch a real object.
	 *
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Get the type of result this is
	 *
	 * @return string
	 */
	public function getContentTypeName()
	{
		return $this->content_type;
	}

	/**
	 * Get the result ID
	 *
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}
}
