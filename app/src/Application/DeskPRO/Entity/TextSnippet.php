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

use Application\DeskPRO\Domain\ObjectTranslatable;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

use Orb\Util\Arrays;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

class TextSnippet extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var int
	 */
	protected $id = null;

	/**
	 * Who created the snippet
	 *
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person = null;

	/**
	 * @var \Application\DeskPRO\Entity\TextSnippetCategory
	 */
	protected $category;

	/**
	 * @var string
	 */
	protected $shortcut_code = '';

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var string
	 */
	protected $snippet;

	public function __construct()
	{
		$this->getObjectTranslatable();
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * @param string $sc
	 */
	public function setShortcutCode($sc)
	{
		if (!$sc) {
			$this->setModelField('shortcut_code', '');
		} else {
			$this->setModelField('shortcut_code', $sc);
		}
	}


	public function toApiData($primary = true, $deep = true, array $visited = array())
	{
		$data = parent::toApiData($primary, $deep, $visited);
		$data['category_id'] = $this->category ? $this->category->getId() : 0;
		$data['title'] = array();
		$data['snippet'] = array();

		foreach (App::getContainer()->getLanguageData()->getAll() as $lang) {
			$title   = $this->getObjectTranslatable()->getObjectProp('title', $lang);
			$snippet = $this->getObjectTranslatable()->getObjectProp('snippet', $lang);

			$data['title'][] = array('language_id' => $lang->getId(), 'locale' => $lang->getLocale(), 'value' => $title);
			$data['snippet'][] = array('language_id' => $lang->getId(), 'locale' => $lang->getLocale(), 'value' => $snippet);
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
		return array('fields' => array('title', 'snippet'));
	}

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\TextSnippet';
		$metadata->setPrimaryTable(array( 'name' => 'text_snippets', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'shortcut_code', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'shortcut_code', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'category', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TextSnippetCategory', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'category_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));

		ObjectTranslatable::loadEntityMetadata($metadata);
	}
}
