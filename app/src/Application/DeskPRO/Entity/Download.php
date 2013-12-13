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

use Orb\Data\ContentTypes;
use Orb\Util\Numbers;
use Orb\Util\Strings;

/**
 * A download/file available from the protal
 */
class Download extends ContentAbstract
{
	/**
	 * @var \Application\DeskPRO\Entity\TicketCategory
	 */
	protected $category;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $revisions;

	/**
	 * @var \Application\DeskPRO\Entity\Blob
	 */
	protected $blob;

	/**
	 * @var string
	 */
	protected $fileurl;

	/**
	 * @var string
	 */
	protected $filesize;

	/**
	 * @var string
	 */
	protected $filename;

	/**
	 * Total number of downloads
	 *
	 * @var string
	 */
	protected $num_downloads = 0;

	/**
	 */
	protected $labels;

	/**
	 * @var \Application\DeskPRO\Labels\LabelManager
	 */
	protected $_label_manager = null;

	/**
	 * @param Blob $blob
	 */
	public function setBlob(Blob $blob = null)
	{
		if ($blob) {
			$this->setModelField('blob', $blob);
			$this->setModelField('fileurl', null);
			$this->setModelField('filesize', null);
			$this->setModelField('filename', null);
		} else {
			$this->setModelField('blob', null);
		}
	}


	/**
	 * @param $url
	 * @param $filesize
	 * @param null $filename
	 */
	public function setFileUrl($url, $filesize, $filename = null)
	{
		if (!$filename) {
			$last_bit = str_replace(array('/', ':', '\\'), '/', $url);
			$last_bit = explode('/', $last_bit);
			$last_bit = array_pop($last_bit);

			if ($last_bit) {
				$filename = $last_bit;
			}
		}

		if (!$filename) {
			$filename = 'file';
		}

		if ($filesize && !ctype_digit($filesize)) {
			$filesize = strtolower($filesize);
			$filesize = str_replace(array('bytes', 'kilobytes', 'megabytes', 'gigabytes'), array('b', 'kb', 'mb', 'gb'), $filesize);
			foreach (array('k', 'm', 'g') as $l) {
				$filesize = preg_replace("#\b$l\b#", "{$l}b", $filesize);
			}

			$num = preg_replace('#[^0-9]#', '', $filesize);
			if (strpos($filesize, 'tb') !== false) {
				$filesize = $num * 1099511627776;
			} elseif (strpos($filesize, 'gb') !== false) {
				$filesize = $num * 1073741824;
			} elseif (strpos($filesize, 'mb') !== false) {
				$filesize = $num * 1048576;
			} elseif (strpos($filesize, 'kb') !== false) {
				$filesize = $num * 1024;
			} else {
				$filesize = $num;
			}
		}

		$this->setModelField('blob', null);
		$this->setModelField('fileurl', $url);
		$this->setModelField('filesize', $filesize);
		$this->setModelField('filename', $filename);
	}


	/**
	 * @return string
	 */
	public function getFileName()
	{
		if ($this->filename) {
			return $this->filename;
		}

		if (!$this->blob) {
			return '';
		}
		return $this->blob['filename'];
	}


	/**
	 * @return int|string
	 */
	public function getFileSize()
	{
		if ($this->filesize) {
			return $this->filesize;
		}

		if (!$this->blob) {
			return 0;
		}
		return $this->blob['filesize'];
	}


	/**
	 * @return string
	 */
	public function getReadableFileSize()
	{
		if ($this->filesize) {
			return Numbers::filesizeDisplay($this->filesize);
		}

		if (!$this->blob) {
			return '0 B';
		}
		return $this->blob->getReadableFilesize();
	}


	/**
	 * @return string
	 */
	public function getLink()
	{
		$url = App::getRouter()->generate('user_downloads_file', array('slug' => $this->getUrlSlug()), true);

		return $url;
	}


	/**
	 * @return string
	 */
	public function getPermalink()
	{
		$url = App::getRouter()->generate('user_downloads_file', array('slug' => $this->id), true);

		return $url;
	}


	/**
	 * @return array
	 */
	public function getCategoryPath()
	{
		$path = array();

		$cat = $this->category;
		$path[] = $cat;
		while ($cat['parent']) {
			$cat = $cat['parent'];
			$path[] = $cat;
		}

		return $path;
	}


	/**
	 * Add a label
	 * @param \Application\DeskPRO\Entity\LabelDownload $label
	 */
	public function addLabel(LabelDownload $label)
	{
		$label['download'] = $this;
		$this->labels->add($label);
	}


	/**
	 * @return string
	 */
	public function getContentDesc()
	{
		$content = $this->content;
		$content = strip_tags($content);
		$content = str_replace("\n", ' ', $content);
		$content = preg_replace('# {2,}#', ' ', $content);

		if (strlen($content) > 120) {
			$content = substr($content, 0, 120) . '...';
		}

		return $content;
	}

	public function _invalidatePageCache()
	{
		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->invalidateRegex('/_downloads(-|_files_' . intval($this->getId()) . '-|_\d+)/');
	}



	public function toApiData($primary = true, $deep = true, array $visited = array())
	{
		$data = parent::toApiData($primary, $deep, $visited);
		if ($deep) {
			$data['labels'] = array();
			foreach ($this->labels AS $label) {
				$data['labels'][] = $label['label'];
			}
		}

		$data['filename'] = $this->getFileName();
		$data['filesize'] = $this->getFileSize();
		if ($this->blob) {
			$data['downloadurl'] = App::getRouter()->generate(
				'serve_blob', array('blob_auth_id' => $this->blob->auth_id, 'filename' => $this->filename), true
			);
		}

		return $data;
	}

	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\Download';
		$metadata->setPrimaryTable(array(
			'name' => 'downloads',
			'indexes' => array(
				'date_published_idx' => array( 'columns' => array( 0 => 'date_published', )),
				'status_idx' => array('columns' => array('status')),
			)
		));
		$metadata->addLifecycleCallback('_invalidatePageCache', 'preFlush');
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'num_downloads', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'num_downloads', ));
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'slug', 'type' => 'string', 'length' => 100, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'slug', ));
		$metadata->mapField(array( 'fieldName' => 'title', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'title', ));
		$metadata->mapField(array( 'fieldName' => 'fileurl', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'fileurl', ));
		$metadata->mapField(array( 'fieldName' => 'filename', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'filename', ));
		$metadata->mapField(array( 'fieldName' => 'filesize', 'type' => 'integer', 'nullable' => true, 'columnName' => 'filesize', ));
		$metadata->mapField(array( 'fieldName' => 'content', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'content', ));
		$metadata->mapField(array( 'fieldName' => 'view_count', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'view_count', ));
		$metadata->mapField(array( 'fieldName' => 'total_rating', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'total_rating', ));
		$metadata->mapField(array( 'fieldName' => 'num_comments', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'num_comments', ));
		$metadata->mapField(array( 'fieldName' => 'num_ratings', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'num_ratings', ));
		$metadata->mapField(array( 'fieldName' => 'status', 'type' => 'string', 'length' => 15, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'status', ));
		$metadata->mapField(array( 'fieldName' => 'hidden_status', 'type' => 'string', 'length' => 15, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'hidden_status', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->mapField(array( 'fieldName' => 'date_published', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_published', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'category', 'targetEntity' => 'Application\\DeskPRO\\Entity\\DownloadCategory', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'category_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ), 'dpApi' => true ));
		$metadata->mapOneToMany(array( 'fieldName' => 'revisions', 'targetEntity' => 'Application\\DeskPRO\\Entity\\DownloadRevision', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'download',  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'blob', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Blob', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'blob_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => NULL, 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'labels', 'targetEntity' => 'Application\\DeskPRO\\Entity\\LabelDownload', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'download', 'orphanRemoval' => true, ));
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ), 'dpApi' => true ));
		$metadata->mapManyToOne(array( 'fieldName' => 'language', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Language', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'language_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), 'dpApi' => true ));
	}
}
