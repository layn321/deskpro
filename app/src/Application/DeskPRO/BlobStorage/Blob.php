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
 */

namespace Application\DeskPRO\BlobStorage;

use Application\DeskPRO\Dpql\Statement\Part\String;
use Orb\Data\ContentTypes;
use Orb\Util\Strings;

class Blob
{
	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var string
	 */
	protected $filename;

	/**
	 * @var string
	 */
	protected $filename_safe = null;

	/**
	 * @var string
	 */
	protected $content_type;

	/**
	 * @var array
	 */
	protected $meta;

	public function __construct($filename, $content_type, array $meta = array())
	{
		$this->filename = $filename;
		$this->content_type = $content_type;
		$this->meta = $meta;

		// Automatically detect disposition if none provided
		if (!isset($this->meta['content_disposition'])) {
			if (ContentTypes::isInlineContentType($content_type, true)) {
				$this->meta['content_disposition'] = 'inline';
			} else {
				$this->meta['content_disposition'] = 'attachment';
			}
		}
	}


	/**
	 * @param $path
	 */
	public function setPath($path)
	{
		$this->path = $path;
	}


	/**
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}


	/**
	 * @return string
	 */
	public function getContentType()
	{
		return $this->content_type;
	}


	/**
	 * @return string
	 */
	public function getFilename()
	{
		return $this->filename;
	}


	/**
	 * @return string
	 */
	public function getFilenameSafe()
	{
		if ($this->filename_safe !== null) {
			return $this->filename_safe;
		}

		$this->filename_safe = $this->filename;
		$this->filename_safe = Strings::utf8_accents_to_ascii($this->filename_safe);
		$this->filename_safe = preg_replace('#[^a-zA-Z0-9\.\-_]#', '-', $this->filename_safe);
		$this->filename_safe = preg_replace('#\-{2,}#', '-', $this->filename_safe);
		return $this->filename_safe;
	}


	/**
	 * @param string $id
	 * @param mixed $value
	 */
	public function setMeta($id, $value)
	{
		if ($value === null) {
			unset($this->meta[$value]);
		} else {
			$this->meta[$id] = $value;
		}
	}

	/**
	 * @param $id
	 * @param null $default
	 * @return null
	 */
	public function getMeta($id, $default = null)
	{
		return isset($this->meta[$id]) ? $this->meta[$id] : $default;
	}


	/**
	 * @return array
	 */
	public function getAllMeta()
	{
		return $this->meta;
	}
}