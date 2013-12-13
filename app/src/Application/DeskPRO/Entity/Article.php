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

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;
use Application\DeskPRO\Markdown;

use Orb\Util\Strings;

/**
 * Article
 */
class Article extends ContentAbstract
{
	const END_ACTION_DELETE  = 'delete';
	const END_ACTION_ARCHIVE = 'archive';

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $categories;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $products;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $revisions;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $attachments;

	/**
	 * @var \DateTime
	 */
	protected $date_updated;

	/**
	 * @var \DateTime
	 */
	protected $date_last_comment;

	/**
	 * @var \DateTime
	 */
	protected $date_end;

	/**
	 * @var string
	 */
	protected $end_action = null;

	/**
	 */
	protected $custom_data;

	/**
	 */
	protected $labels;

	public function __construct()
	{
		parent::__construct();

		$this->products    = new \Doctrine\Common\Collections\ArrayCollection();
		$this->categories  = new \Doctrine\Common\Collections\ArrayCollection();
		$this->attachments = new \Doctrine\Common\Collections\ArrayCollection();
		$this->custom_data = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function getLink()
	{
		$url = App::getRouter()->generate('user_articles_article', array('slug' => $this->getUrlSlug()), true);

		return $url;
	}

	public function getPermalink()
	{
		$url = App::getRouter()->generate('user_articles_article', array('slug' => $this->id), true);

		return $url;
	}

	public function setStatus($status)
	{
		if ($status == 'approve') {
			$status = self::STATUS_PUBLISHED;
		}

		if ($status == self::STATUS_PUBLISHED) {
			$this->setModelField('status', self::STATUS_PUBLISHED);
			$this->setModelField('hidden_status', null);
		} else {
			$this->setModelField('status', $status);
		}
	}

	/**
	 * Add a label
	 * @param \Application\DeskPRO\Entity\LabelTicket $label
	 */
	public function addLabel(LabelArticle $label)
	{
		$label['article'] = $this;
		$this->labels->add($label);
	}

	public function addCustomData(CustomDataArticle $data)
	{
		$this->custom_data->add($data);
		$data['article'] = $this;
	}

	public function isInCategory(ArticleCategory $cat)
	{
		return $this->categories->contains($cat);
	}

	public function addToCategory(ArticleCategory $cat)
	{
		$this->categories->add($cat);
	}

	public function removeFromCategory(ArticleCategory $cat)
	{
		$this->categories->removeElement($cat);
	}

	public function setCategories(array $cats)
	{
		$helper = new \Application\DeskPRO\ORM\CollectionHelper($this, 'categories');
		$helper->setCollection($cats);
	}

	public function setProducts(array $prods)
	{
		$helper = new \Application\DeskPRO\ORM\CollectionHelper($this, 'products');
		$helper->setCollection($prods);
	}

	public function getCategoryNames($sep = ', ', $full = true)
	{
		$cats = array();
		foreach ($this->categories as $cat) {
			if ($full) {
				if ($full !== true) {
					// If its not a boolean, then its a string separator
					$cats[] = $cat->getFullTitle($full);
				} else {
					$cats[] = $cat->getFullTitle();
				}

			} else {
				$cats[] = $cat['title'];
			}
		}

		return implode($sep, $cats);
	}

	public function getCategoryPath($index = 0)
	{
		$path = array();

		$cat = $this->categories[$index];
		$path[] = $cat;
		while ($cat['parent']) {
			$cat = $cat['parent'];
			$path[] = $cat;
		}

		return $path;
	}

	public function getPrimaryCategory()
	{
		if (!$this->categories) {
			return null;
		}

		foreach ($this->categories as $c) {
			return $c;
		}
	}

	public function addAttachment(ArticleAttachment $attach)
	{
		$this->attachments->add($attach);
		$attach['article'] = $this;
	}

	public function _invalidatePageCache()
	{
		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->invalidateRegex('/_kb(-|_articles_' . intval($this->getId()) . '-|_\d+)/');
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

	public function getObjectTranslatable()
	{
		return ObjectTranslatable::loadObjectTranslatable($this);
	}

	public static function loadObjectTranslatableMetadata()
	{
		return array(
			'with_lang_prop' => 'language',
			'fields' => array('title', 'content')
		);
	}

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\Article';
		$metadata->setPrimaryTable(array(
			'name' => 'articles',
			'indexes' => array(
				'date_published_idx'    => array('columns' => array('date_published')),
				'date_updated_idx'      => array('columns' => array('date_updated')),
				'date_last_comment_idx' => array('columns' => array('date_last_comment')),
				'status_idx'            => array('columns' => array('status')),
			),
		));
		$metadata->addLifecycleCallback('_invalidatePageCache', 'preFlush');
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'date_end', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_end', ));
		$metadata->mapField(array( 'fieldName' => 'end_action', 'type' => 'string', 'length' => 10, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'end_action', ));
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
		$metadata->mapField(array( 'fieldName' => 'date_updated', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_updated', ));
		$metadata->mapField(array( 'fieldName' => 'date_last_comment', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_last_comment', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToMany(array( 'fieldName' => 'categories', 'targetEntity' => 'Application\\DeskPRO\\Entity\\ArticleCategory', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'joinTable' => array( 'name' => 'article_to_categories', 'schema' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'article_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), 'inverseJoinColumns' => array( 0 => array( 'name' => 'category_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), ), 'dpApi' => true ));
		$metadata->mapManyToMany(array( 'fieldName' => 'products', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Product', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'joinTable' => array( 'name' => 'article_to_product', 'schema' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'article_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), 'inverseJoinColumns' => array( 0 => array( 'name' => 'product_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), ), 'dpApi' => true ));
		$metadata->mapOneToMany(array( 'fieldName' => 'revisions', 'targetEntity' => 'Application\\DeskPRO\\Entity\\ArticleRevision', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'article',  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'attachments', 'targetEntity' => 'Application\\DeskPRO\\Entity\\ArticleAttachment', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'article',  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'custom_data', 'targetEntity' => 'Application\\DeskPRO\\Entity\\CustomDataArticle', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'article', 'orphanRemoval' => true, 'dpApi' => true ));
		$metadata->mapOneToMany(array( 'fieldName' => 'labels', 'targetEntity' => 'Application\\DeskPRO\\Entity\\LabelArticle', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'article', 'orphanRemoval' => true, ));
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ), 'dpApi' => true ));
		$metadata->mapManyToOne(array( 'fieldName' => 'language', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Language', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'language_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), 'dpApi' => true ));

		ObjectTranslatable::loadEntityMetadata($metadata);
	}
}