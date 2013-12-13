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

namespace Application\DeskPRO\Entity;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

use Application\DeskPRO\App;
use Orb\Util\Strings;
use Orb\Util\Numbers;

use Application\DeskPRO\Entity\LabelBlob;

/**
 * A blob is just a pointer to data.
 *
 * @property int $id
 * @property int $sys_name
 * @property int $original_blob
 */
class Blob extends \Application\DeskPRO\Domain\DomainObject
{
	const STORAGE_LOC_DATABASE   = 'db';
	const STORAGE_LOC_FILESYSTEM = 'fs';
	const STORAGE_LOC_S3         = 's3';

	/**
	 * @var int
	 */
	protected $id = null;

	/**
	 * A unique system name for the blob.
	 *
	 * @var string
	 */
	protected $sys_name = null;

	/**
	 * Sometimes we might have multiple versions of a file. For example, if a file has been
	 * cropped then the cropped file is saved as its own blob, but the original
	 * is linked here.
	 *
	 * @var \Application\DeskPRO\Entity\Blob
	 */
	protected $original_blob;

	/**
	 * The storage adapter that knows how to load this file
	 *
	 * @var string
	 */
	protected $storage_loc = 'db';

	/**
	 * The preferred storage adapter. This is used to mark when we want to move
	 * a file from one storage location to another. For example, if an upload
	 * to S3 failed and we saved the file in the database instead,
	 * then the $storage_loc would be 'db' but $storage_loc_pref would be 's3'.
	 *
	 * The cron jobs will look for when these two values don't match and will
	 * attempt to move resources gradually.
	 *
	 * @var string
	 */
	protected $storage_loc_pref = null;

	/**
	 * The path to the file if it's not stored in the database.
	 *
	 * @var string
	 */
	protected $save_path = null;

	/**
	 * The HTTP link to download the file
	 *
	 * @var string
	 */
	protected $file_url = null;

	/**
	 * The original filename
	 *
	 * @var string
	 */
	protected $filename = null;

	/**
	 * The file size
	 *
	 * @var int
	 */
	protected $filesize;

	/**
	 * The files mimetype
	 *
	 * @var string
	 */
	protected $content_type = null;

	/**
	 * @var string
	 */
	protected $authcode;

	/**
	 * @var string
	 */
	protected $blob_hash;

	/**
	 * Is this a media upload (appears in the media browser etc). These are files that were
	 * uploaded and are attached to things.
	 *
	 */
	protected $is_media_upload = false;

	/**
	 * The title of this file used in interfaces if its a media upload
	 *
	 */
	protected $title = '';

	/**
	 * If this type of file has dimentions, the width
	 *
	 * @var int
	 */
	protected $dim_w = 0;

	/**
	 * If this type of file has dimentions, the height
	 *
	 * @var int
	 */
	protected $dim_h = 0;

	/**
	 * @var \DateTime
	 */
	protected $date_created;

	/**
	 * @var bool
	 */
	protected $is_temp = false;

	/**
	 * The date this blob should be automatically cleaned
	 */
	protected $date_cleanup;

	/**
	 */
	protected $labels;

	protected $_label_manager = null;

	public function __construct()
	{
		$this->date_created = new \DateTime();
		$this->authcode = Strings::random(20, Strings::CHARS_KEY_ALPHA);
		$this->labels = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}


	public function setFilename($filename)
	{
		if ($filename[0] == '.') {
			$filename = '_' . substr($filename, 1);
		}

		$old = $this->filename;
		$this->filename = $filename;
		$this->_onPropertyChanged('filename', $old, $this->filename);

		// Try to guess content typ based off of filename exts
		if (!$this->content_type) {
			$ct = \Orb\Data\ContentTypes::getContentTypeFromFilename($this->filename);
			if ($ct) {
				$this['content_type'] = $ct;
			}
		}
	}


	/**
	 * Get the file extension
	 *
	 * @return string
	 */
	public function getExtension()
	{
		$pos = strrpos($this->filename, '.');
		if ($pos === false) return '';

		return substr($this->filename, $pos+1);
	}


	/**
	 * Is the file an image?
	 *
	 * @return bool
	 */
	public function isImage()
	{
		switch ($this->content_type) {
			case 'image/jpg':
			case 'image/jpeg':
				return true;
			case 'image/gif':
				return true;
			case 'image/png':
				return true;
		}

		return false;
	}


	/**
	 * Get the type of image this is, or null if its not an image.
	 *
	 * @return string
	 */
	public function getImageType()
	{
		switch ($this->content_type) {
			case 'image/jpg':
			case 'image/jpeg':
				return 'jpeg';
			case 'image/gif':
				return 'gif';
			case 'image/png':
				return 'png';
		}

		return null;
	}


	/**
	 * Get the filesize with B, KB, GB etc suffix.
	 */
	public function getReadableFilesize()
	{
		return Numbers::filesizeDisplay($this->filesize);
	}


	/**
	 * Get the id-auth combo typically used in urls.
	 *
	 * @return string
	 */
	public function getAuthId()
	{
		return $this->authcode;
	}


	/**
	 * Get the standard download URL for this blob.
	 *
	 * @param bool $absolute
	 * @return string
	 */
	public function getDownloadUrl($absolute = false, $use_file_url = true)
	{
		if ($use_file_url && $this->file_url) {
			return $this->file_url;
		}

		return App::get('router')->generate('serve_blob', array('blob_auth_id' => $this->getAuthId(), 'filename' => $this->getFilenameSafe()), $absolute);
	}

	public function getEmbedCode($for_ticket = false, $type = 'image')
	{
		if ($for_ticket) {
			return '[attach:' . $type . ':' . $this->getAuthId() . ':' . $this->filename . ']';
		} else {
			return '[attach:' . $this->getAuthId() . ':' . $this->filename . ']';
		}
	}


	/**
	 * Get a thumbnail for this blob (if its an image)
	 *
	 * @param int $size
	 * @param bool $absolute
	 * @return string
	 */
	public function getThumbnailUrl($size = 50, $absolute = false)
	{
		if (!$this->isImage()) {
			return null;
		}
		return App::get('router')->generate('serve_blob', array('blob_auth_id' => $this->getAuthId(), 'filename' => $this->filename, 's' => $size), $absolute);
	}

	/**
	 * Get a safe version of a filename. That is the same filename with all "weird" characters removed.
	 *
	 * @return string
	 */
	public function getFilenameSafe()
	{
		$filename_safe = Strings::utf8_accents_to_ascii($this->filename);
		$filename_safe = preg_replace('#[^a-zA-Z0-9\-_\.]#', '-', $filename_safe);
		$filename_safe = preg_replace('#\-{2,}#', '-', $filename_safe);

		return $filename_safe;
	}


	/**
	 * The name hash is 6 chars long that represents the original filename
	 *
	 * @return string
	 */
	public function getNameHash()
	{
		$namehash = strtoupper(substr(sha1($this->getFilenameSafe() . $this->id), 0, 3));
		$namehash .= strtoupper(substr(md5($this->getFilenameSafe() . $this->id), 0, 3));

		return $namehash;
	}


	/**
	 * @return string
	 */
	public function getDisplayTitle()
	{
		if ($this->title) {
			return $this->title;
		}

		return $this->filename;
	}

	public function addLabel(LabelBlob $label)
	{
		$label['blob'] = $this;
		$this->labels->add($label);
		$this->_onPropertyChanged('labels', $this->labels, $this->labels);
	}


	/**
	 * @param string $storage_loc
	 */
	public function setStorageLocPref($storage_loc)
	{
		if (!$storage_loc) {
			$this->setModelField('storage_loc_pref', null);
		} else {
			$this->setModelField('storage_loc_pref', $storage_loc);
		}
	}


	/**
	 * @return \Application\DeskPRO\Labels\LabelManager
	 */
	public function getLabelManager()
	{
		if ($this->_label_manager === null) {
			$this->_label_manager = new \Application\DeskPRO\Labels\LabelManager($this, 'DeskPRO:LabelBlob');
		}

		return $this->_label_manager;
	}

	public function toApiData($primary = true, $deep = true, array $visited = array())
	{
		return array(
			'id' => $this->id,
			'authcode' => $this->authcode,
			'filename' => $this->filename,
			'filesize' => $this->filesize,
			'filesize_display' => $this->getReadableFilesize(),
			'content_type' => $this->content_type,
			'download_url' => $this->getDownloadUrl(true)
		);
	}

	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\Blob';
		$metadata->setPrimaryTable(array(
			'name' => 'blobs',
			'indexes' => array(
				'authcode_idx' => array('columns' => array('authcode')),
				'storage_loc_idx' => array('columns' => array('storage_loc', 'storage_loc_pref'))
			)
		));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'sys_name', 'type' => 'string', 'length' => 100, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'sys_name', ));
		$metadata->mapField(array( 'fieldName' => 'storage_loc', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'storage_loc', ));
		$metadata->mapField(array( 'fieldName' => 'storage_loc_pref', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'storage_loc_pref', ));
		$metadata->mapField(array( 'fieldName' => 'save_path', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'save_path', ));
		$metadata->mapField(array( 'fieldName' => 'file_url', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'file_url', ));
		$metadata->mapField(array( 'fieldName' => 'filename', 'type' => 'string', 'length' => 120, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'filename', ));
		$metadata->mapField(array( 'fieldName' => 'filesize', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'filesize', ));
		$metadata->mapField(array( 'fieldName' => 'content_type', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'content_type', ));
		$metadata->mapField(array( 'fieldName' => 'authcode', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'authcode', ));
		$metadata->mapField(array( 'fieldName' => 'blob_hash', 'type' => 'string', 'length' => 40, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'blob_hash', ));
		$metadata->mapField(array( 'fieldName' => 'is_media_upload', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_media_upload', ));
		$metadata->mapField(array( 'fieldName' => 'title', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'title', ));
		$metadata->mapField(array( 'fieldName' => 'dim_w', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'dim_w', ));
		$metadata->mapField(array( 'fieldName' => 'dim_h', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'dim_h', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->mapField(array( 'fieldName' => 'is_temp', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_temp', ));
		$metadata->mapField(array( 'fieldName' => 'date_cleanup', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_cleanup', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'original_blob', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Blob', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'original_blob_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'labels', 'targetEntity' => 'Application\\DeskPRO\\Entity\\LabelBlob', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'blob', 'orphanRemoval' => true, ));
	}

	public function __getPropValue__($k) { return $this->$k; }
	public function __setPropValue__($k, $v) { $this->$k = $v; }
}
