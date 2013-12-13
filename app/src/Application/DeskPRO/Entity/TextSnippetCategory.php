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

use Application\DeskPRO\App;
use Application\DeskPRO\Domain\ObjectTranslatable;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

use Orb\Util\Arrays;

/**
 */
class TextSnippetCategory extends \Application\DeskPRO\Domain\DomainObject
{
	const TPYE_TICKET  = 'tickets';
	const TPYE_CHAT    = 'chat';

	/**
	 * @var int
	 */
	protected $id = null;

	/**
	 * The type of snippets this cat contains
	 *
	 * @var string
	 */
	protected $typename;

	/**
	 * Who created the cat
	 *
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person = null;

	/**
	 * Everyone can see it?
	 *
	 * @var bool
	 */
	protected $is_global = false;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function getPermType()
	{
		if ($this->is_global) {
			return 'global';
		} else {
			return 'me';
		}
	}


	public function toApiData($primary = true, $deep = true, array $visited = array())
	{
		$data = parent::toApiData($primary, $deep, $visited);
		$data['title'] = array();

		foreach (App::getContainer()->getLanguageData()->getAll() as $lang) {
			$title   = $this->getObjectTranslatable()->getObjectProp('title', $lang);
			$data['title'][] = array('language_id' => $lang->getId(), 'locale' => $lang->getLocale(), 'value' => $title);
		}

		return $data;
	}


	############################################################################
	# Doctrine Metadata
	############################################################################

	public function getObjectTranslatable()
	{
		return ObjectTranslatable::loadObjectTranslatable($this);
	}

	public static function loadObjectTranslatableMetadata()
	{
		return array('fields' => array('title'));
	}

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\TextSnippetCategory';
		$metadata->setPrimaryTable(array( 'name' => 'text_snippet_categories', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'typename', 'type' => 'string', 'length' => 30, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'typename', ));
		$metadata->mapField(array( 'fieldName' => 'is_global', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_global', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));

		ObjectTranslatable::loadEntityMetadata($metadata);
	}
}
