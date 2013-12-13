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

use Doctrine\ORM\EntityManager;
use Application\DeskPRO\BlobStorage\DeskproBlobStorage;

use Orb\Util\Numbers;
use Orb\Data\ContentTypes;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Application\DeskPRO\App;

class AcceptAttachment
{
	const ERR_SIZE    = 'size';
	const ERR_FAILED  = 'failed_upload';
	const ERR_NO_FILE = 'no_file';
	const ERR_SERVER  = 'server_error';

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Application\DeskPRO\BlobStorage\DeskproBlobStorage
	 */
	protected $blobstorage;

	/**
	 * @var \Application\DeskPRO\Attachments\RestrictionSet[]
	 */
	protected $restriction_sets = array();

	public function __construct(EntityManager $em, DeskproBlobStorage $blobstorage)
	{
		$this->em = $em;
		$this->blobstorage = $blobstorage;
	}


	/**
	 * @param $id
	 * @param \Application\DeskPRO\Attachments\RestrictionSet $set
	 */
	public function addRestrictionSet($id, RestrictionSet $set)
	{
		$this->restriction_sets[$id] = $set;
	}


	/**
	 * @param $id
	 * @return \Application\DeskPRO\Attachments\RestrictionSet
	 * @throws \InvalidArgumentException
	 */
	public function getRestrictionSet($id)
	{
		if (!isset($this->restriction_sets[$id])) {
			throw new \InvalidArgumentException("No set with id `$id`");
		}

		return $this->restriction_sets[$id];
	}


	/**
	 * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
	 * @param $restriction_set_id
	 * @return array|null
	 */
	public function getError(UploadedFile $file = null, $restriction_set_id = null)
	{
		$restriction = null;
		if ($restriction_set_id) {
			$restriction = $this->getRestrictionSet($restriction_set_id);
		}

		$max_size = min(\Orb\Util\Env::getEffectiveMaxUploadSize(), $restriction->getMaxSize());

		if ($file === null) {
			// This means the file is too big and PHP basically rejected the whole request data
			if (isset($_SERVER['CONTENT_LENGTH']) && empty($_POST) && empty($_FILES)) {
				return array('error_code' => self::ERR_SIZE, 'error_detail' => Numbers::filesizeDisplay($max_size));
			}

			return array('error_code' => self::ERR_NO_FILE, 'error_detail' => 'null_file');
		}

		$log_error = false;
		$error = array(
			'error_code' => null,
			'error_detail' => null
		);

		if (!$file->isValid()) {
			switch ($file->getError()) {
				case \UPLOAD_ERR_INI_SIZE:
					$error['error_code'] = self::ERR_SIZE;
					$error['error_detail'] = Numbers::filesizeDisplay($max_size);
					break;

				case \UPLOAD_ERR_PARTIAL:
					$error['error_code'] = self::ERR_FAILED;
					$error['error_detail'] = '';
					break;

				case \UPLOAD_ERR_NO_FILE:
					$error['error_code'] = self::ERR_NO_FILE;
					$error['error_detail'] = '';
					break;

				case \UPLOAD_ERR_NO_TMP_DIR:
					$log_error = true;
					$error['error_code'] = self::ERR_SERVER;
					$error['error_detail'] = 'bad_tmp_dir';
					break;

				case \UPLOAD_ERR_CANT_WRITE:
					$log_error = true;
					$error['error_code'] = self::ERR_SERVER;
					$error['error_detail'] = 'failed_write';
					break;

				case \UPLOAD_ERR_EXTENSION:
					$log_error = true;
					$error['error_code'] = self::ERR_SERVER;
					$error['error_detail'] = 'ext_stopped';
					break;

				default:
					$log_error = true;
					$error['error_code'] = self::ERR_SERVER;
					$error['error_detail'] = 'unknown';
					break;
			}
		}

		if (!$error['error_code']) {
			if (!is_uploaded_file($file->getRealPath()) || !file_exists($file->getRealPath())) {
				$error['error_code'] = self::ERR_NO_FILE;
				$error['error_detail'] = '';
			}
		}

		if (!$error['error_code']) {
			$error = null;
		}

		if (!$error && $restriction) {
			$error = $restriction->getError($file);
		}

		if (!$error || !$error['error_code']) {
			return null;
		}

		if ($log_error) {
			App::logErrorMessage('failed_upload', 'INFO', "Upload of {$file->getClientOriginalName()} failed because {$error['error_code']}", array(
				'error_code' => $error['error_code'],
				'error_detail' => $error['error_detail'],
				'filename' => $file->getClientOriginalName(),
				'type' => $file->getClientMimeType(),
				'size' => $file->getClientSize(),
				'file_err_code' => $file->getError()
			));
		}

		return $error;
	}


	/**
	 * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
	 * @param bool $is_temp
	 * @return \Application\DeskPRO\Entity\Blob
	 */
	public function accept(UploadedFile $file, $is_temp = false)
	{
		try {
			$mime_type = $file->getMimeType();
		} catch (\Exception $e) {
			$mime_type = $file->getClientMimeType();
		}

		if (!$mime_type) {
			$mime_type = ContentTypes::getContentTypeFromFilename($file->getClientOriginalName());
		}

		if (!$mime_type) {
			$mime_type = 'application/octet-stream';
		}

		$filename = $file->getClientOriginalName();
		if (!$filename) {
			$filename = crc32(mt_rand(1111,9999) . mt_rand(1111,9999) . mt_rand(1111,9999) . mt_rand(1111,9999));
			$ext = ContentTypes::findExtensionForContentType($mime_type);
			if ($ext) {
				$filename .= '.' . $ext;
			}
		}

		$blob = $this->blobstorage->createBlobRecordFromFile(
			$file->getRealPath(),
			$filename,
			$mime_type,
			array('is_temp' => $is_temp)
		);

		return $blob;
	}
}
