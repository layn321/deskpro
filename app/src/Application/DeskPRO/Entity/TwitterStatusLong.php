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
use Application\DeskPRO\Entity;

/**
 * Twitter Status
 *
 * Long Reply/Message w/ URL Shortener.
 *
 */
class TwitterStatusLong extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var \Application\DeskPRO\Entity\TwitterStatus
	 */
	protected $status;

	/**
	 * @var TwitterUser
	 */
	protected $for_user;

	/**
	 * @var string
	 */
	protected $text;

	/**
	 * @var Boolean
	 */
	protected $is_public = false;

	/**
	 * @var \DateTime
	 */
	protected $date_created;

	/**
	 * @var Boolean
	 */
	protected $is_read = false;

	/**
	 * @var \DateTime
	 */
	protected $date_read = null;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->date_created = new \DateTime();
	}

	/**
	 * @return integer
	 */
	public function getStatusId()
	{
		if (null !== $this->status) {
			return $this->status->getId();
		}

		return 0;
	}

	/**
	 * @param integer $id
	 */
	public function setStatusId($id)
	{
		if ($id && $status = App::getOrm()->getRepository('DeskPRO:TwitterStatus')->find($id)) {
			$this->status = $status;
		} else {
			$this->status = null;
		}
	}

	public function getParsedText()
	{
		$text = htmlspecialchars($this->text, ENT_COMPAT, 'utf-8');
		$text = preg_replace('/@([a-z0-9_]+)/i', '<a href="https://twitter.com/$1" target="_blank">$0</a>', $text);
		$text = \Orb\Util\Strings::linkifyHtml($text, true);

		return nl2br($text);
	}

	/**
	 * @return Boolean
	 */
	public function isPublic()
	{
		return (Boolean) $this->is_public;
	}

	/**
	 * @return Boolean
	 */
	public function isRead()
	{
		return (Boolean) $this->is_read;
	}



	############################################################################
	# Doctrine Metadata
	############################################################################


	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\Basic';
		$metadata->setPrimaryTable(array( 'name' => 'twitter_statuses_long', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'text', 'type' => 'string', 'length' => 4000, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'text', ));
		$metadata->mapField(array( 'fieldName' => 'is_public', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_public', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->mapField(array( 'fieldName' => 'is_read', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_read', ));
		$metadata->mapField(array( 'fieldName' => 'date_read', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_read', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'status', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterStatus', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'status_id', 'referencedColumnName' => 'id', 'unique' => false, 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'for_user', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterUser', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'for_user_id', 'referencedColumnName' => 'id', 'unique' => false, 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
	}
}
