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

/**
 * Glossary
 *
 */
class GlossaryWordDefinition extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var int
	 */
	protected $id = null;

	/**
	 * @var string
	 */
	protected $definition;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $words;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function __construct()
	{
		$this->words    = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function addWord($word)
	{
		$word = trim(strval($word));
		if ($word === '') {
			return;
		}

		$existing = App::getEntityRepository('DeskPRO:GlossaryWord')->findByWord($word);
		if ($existing) {
			return;
		}
		foreach ($this->words AS $existing_word) {
			if (strtolower($word) == strtolower($existing_word->word)) {
				return;
			}
		}

		$obj = new GlossaryWord();
		$obj->word = $word;
		$obj->definition = $this;

		$this->words->add($obj);

		return $obj;
	}

	public function updateWords(array $words)
	{
		if (!$words) {
			throw new \InvalidArgumentException("Must provide some words");
		}

		$words_test = array_map('strtolower', $words);

		foreach ($this->words AS $existing_key => $existing_word) {
			$key = array_search(strtolower($existing_word->word), $words_test);
			if ($key !== false) {
				unset($words_test[$key]);
			} else {
				$this->words->remove($existing_key);
			}
		}

		foreach (array_keys($words_test) AS $key) {
			$this->addWord($words[$key]);
		}
	}

	public function toApiData($primary = true, $deep = true, array $visited = array())
	{
		$data = parent::toApiData($primary, $deep, $visited);
		$data['words'] = array();
		foreach ($this->words AS $word) {
			$data['words'][$word->id] = $word->word;
		}

		return $data;
	}

	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\Basic';
		$metadata->setPrimaryTable(array( 'name' => 'glossary_word_definitions', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'definition', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'definition', ));
		$metadata->mapOneToMany(array( 'fieldName' => 'words', 'targetEntity' => 'Application\\DeskPRO\\Entity\\GlossaryWord', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'definition', 'orphanRemoval' => true ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
	}
}
