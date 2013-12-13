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
 * @category DependencyInjection
 */

namespace Application\DeskPRO\Attachments;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Orb\Util\Strings;

/**
 * Various tests that can be run on a file to see if we should accept it.
 */
class RestrictionSet
{
	const ERR_SIZE = 'size';
	const ERR_FAIL_MUST_EXT = 'not_in_allowed_exts';
	const ERR_FAIL_NOT_EXT = 'not_allowed_exts';

	/**
	 * The max size to accept
	 *
	 * @var int
	 */
	protected $max_size = 5242880; // 5 MB

	/**
	 * Whitelist of extention to accept
	 *
	 * @var array
	 */
	protected $allowed_exts = null;

	/**
	 * Blacklist of extensions to reject
	 *
	 * @var array
	 */
	protected $disallowed_exts = null;


	/**
	 * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
	 * @return array|null
	 */
	public function getError(UploadedFile $file)
	{
		$size = $file->getSize();
		$ext  = Strings::getExtension($file->getClientOriginalName());

		if ($this->max_size && $size > $this->max_size) {
			return array(
				'error_code' => self::ERR_SIZE,
				'error_detail' => $this->max_size
			);
		}

		if ($this->allowed_exts && !in_array($ext, $this->allowed_exts)) {
			return array(
				'error_code' => self::ERR_FAIL_MUST_EXT,
				'error_detail' => implode(',', $this->allowed_exts)
			);
		}

		if ($this->disallowed_exts && in_array($ext, $this->disallowed_exts)) {
			return array(
				'error_code' => self::ERR_FAIL_NOT_EXT,
				'error_detail' => implode(',', $this->disallowed_exts)
			);
		}

		return null;
	}


	/**
	 * @param array $allowed_exts
	 */
	public function setAllowedExts(array $allowed_exts = null)
	{
		if ($allowed_exts) {
			$allowed_exts = \Orb\Util\Arrays::func($allowed_exts, 'trim');
		}
		$this->allowed_exts = $allowed_exts;
		return $this;
	}


	/**
	 * @return array
	 */
	public function getAllowedExts()
	{
		return $this->allowed_exts;
	}


	/**
	 * @param array $disallowed_exts
	 */
	public function setDisallowedExts(array $disallowed_exts = null)
	{
		if ($disallowed_exts) {
			$disallowed_exts = \Orb\Util\Arrays::func($disallowed_exts, 'trim');
		}
		$this->disallowed_exts = $disallowed_exts;
		return $this;
	}


	/**
	 * @return array
	 */
	public function getDisallowedExts()
	{
		return $this->disallowed_exts;
	}


	/**
	 * @param int $max_size
	 */
	public function setMaxSize($max_size = null)
	{
		$this->max_size = (int)$max_size;
		return $this;
	}


	/**
	 * @return int
	 */
	public function getMaxSize()
	{
		return $this->max_size;
	}
}
