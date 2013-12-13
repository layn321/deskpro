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
use Orb\Util\Arrays;

/**
 * News
 */
class News extends ContentAbstract
{
	/**
	 * @var \Application\DeskPRO\Entity\NewsCategory
	 */
	protected $category;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $revisions;

	/**
	 */
	protected $labels;

	public function getContentHtml()
	{
		$content = $this->getContent();

		// Remove the intro separator
		$content = preg_replace('#[\r\n]+\-{3,}[\r\n]+#', "\n", $content);

		return $content;
	}

	public function getExcerptHtml()
	{
		$content = Strings::standardEol($this->getContent());
		if ($pos = strpos($content, '![more]')) {
			$excerpt = substr($content, $pos);
		} elseif ($pos = strpos($content, "\n\n")) {
			$excerpt = substr($content, 0, $pos);
		} else {
			$excerpt = $content;
		}

		if (str_word_count($excerpt) > 50) {
			$words = str_word_count($excerpt, 2);
			$pos = Arrays::getNthKey($words, 50);
			$excerpt = substr($excerpt, 0, $pos);
			$excerpt = preg_replace('#[^a-zA-Z0-9]$#', '', $excerpt);
			$excerpt .= '...';
		}

		return $excerpt;
	}

	public function getCountWordsAfterExcerpt()
	{
		$content = strip_tags($this->getContentHtml());
		$exceprt = strip_tags($this->getExcerptHtml());

		$diff = str_word_count($content) - str_word_count($exceprt);

		return $diff;
	}

	public function getLink()
	{
		$url = App::getRouter()->generate('user_news_view', array('slug' => $this->getUrlSlug()), true);

		return $url;
	}

	public function getPermalink()
	{
		$url = App::getRouter()->generate('user_news_view', array('slug' => $this->id), true);

		return $url;
	}

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

	public function addLabel(LabelNews $label)
	{
		$label['news'] = $this;
		$this->labels->add($label);
	}

	public function _invalidatePageCache()
	{
		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->invalidateRegex('/_news(-|_view_' . intval($this->getId()) . '-|_\d+)/');
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

		return $data;
	}

	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\News';
		$metadata->setPrimaryTable(array(
			'name' => 'news',
			'indexes' => array(
				'date_published_idx' => array( 'columns' => array( 0 => 'date_published', ), ),
				'status_idx' => array('columns' => array('status')),
			)
		));
		$metadata->addLifecycleCallback('_invalidatePageCache', 'preFlush');
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'slug', 'type' => 'string', 'length' => 100, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'slug', ));
		$metadata->mapField(array( 'fieldName' => 'title', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'title', ));
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
		$metadata->mapManyToOne(array( 'fieldName' => 'category', 'targetEntity' => 'Application\\DeskPRO\\Entity\\NewsCategory', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'category_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), 'dpApi' => true ));
		$metadata->mapOneToMany(array( 'fieldName' => 'revisions', 'targetEntity' => 'Application\\DeskPRO\\Entity\\NewsRevision', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'news',  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'labels', 'targetEntity' => 'Application\\DeskPRO\\Entity\\LabelNews', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'news', 'orphanRemoval' => true, ));
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ), 'dpApi' => true ));
		$metadata->mapManyToOne(array( 'fieldName' => 'language', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Language', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'language_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), 'dpApi' => true ));
	}
}
