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
 * Feedback (feedback)
 */
class Feedback extends ContentAbstract
{
	const STATUS_NEW      = 'new';
	const STATUS_ACTIVE   = 'active';
	const STATUS_CLOSED   = 'closed';
	const STATUS_HIDDEN   = 'hidden';

	/**
	 * @var \Application\DeskPRO\Entity\FeedbackStatusCategory
	 */
	protected $status_category = null;

	/**
	 * @var string
	 */
	protected $hidden_status = null;

	/**
	 * @var string
	 */
	protected $validating = null;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $category;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $revisions;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $comments;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $labels;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $custom_data;

	/**
	 * Popularity (see recalculatePopularity).
	 *
	 * @var string
	 */
	protected $popularity = 0;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $attachments;

	protected $_is_new = false;

	public function __construct()
	{
		parent::__construct();

		$this->_is_new = true;

		$this->comments    = new \Doctrine\Common\Collections\ArrayCollection();
		$this->custom_data = new \Doctrine\Common\Collections\ArrayCollection();
		$this->attachments = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * Set the validating status
	 *
	 * @param string $validating
	 */
	public function setValidating($validating)
	{
		if (!$validating) {
			$this->setModelField('validating', null);
		} else {
			$this->setModelField('validating', $validating);
		}
	}


	/**
	 * @param CustomDataFeedback $data
	 */
	public function addCustomData(CustomDataFeedback $data)
	{
		$this->custom_data->add($data);
		$data['feedback'] = $this;
	}


	/**
	 * @param $rating
	 */
	public function addRating($rating)
	{
		parent::addRating($rating);
		$this->recalculatePopularity();
	}


	public function recalculatePopularity()
	{
		$days = (time() - $this->date_created->getTimestamp()) / 86400;
		if (!$days) $days = 1;

		$pop = ceil($this->total_rating / sqrt($days));

		$this->setModelField('popularity', $pop);
	}

	public function recalculateVoteStats(array $votes)
	{
		$this->num_ratings = count($votes);
		$this->total_rating = 0;
		foreach ($votes as $v) {
			$this->total_rating += $v->getRating();
		}
		$this->recalculatePopularity();
	}

	public function getCategoryId()
	{
		return $this->category['id'];
	}

	public function setCategoryId($id)
	{
		$this->setModelField('category', App::getEntityRepository('DeskPRO:FeedbackCategory')->find($id));
	}

	public function getLink($absolute = true)
	{
		$url = App::getRouter()->generate('user_feedback_view', array('slug' => $this->getUrlSlug()), $absolute);

		return $url;
	}

	public function getPermalink($absolute = true)
	{
		$url = App::getRouter()->generate('user_feedback_view', array('slug' => $this->id), $absolute);

		return $url;
	}

	public function getCategoryName()
	{
		return $this->category->getFullTitle();
	}

	public function setStatus($status)
	{
		$last_status = $this->status;

		$this->_onPropertyChanged('status', $this->status, $status);
		$this->status = $status;

		if ($status == 'approve') {
			$status = self::STATUS_NEW;
		}

		switch ($status) {
			case self::STATUS_NEW:
				$this['hidden_status'] = null;
				$this['status_category'] = null;
				break;

			case self::STATUS_ACTIVE:
			case self::STATUS_CLOSED:
				$this['hidden_status'] = null;
				break;

			case self::STATUS_HIDDEN:
				$this['status_category'] = null;
				break;
		}

		if ($this->status != 'hidden' && $last_status == 'hidden') {
			$this->date_published = new \DateTime();
		}
	}

	public function setStatusCode($status_code)
	{
		if (strpos($status_code, '.') !== false) {
			list ($status, $sub_status) = explode('.', $status_code, 2);
		} else {
			$status = $status_code;
			$sub_status = null;
		}

		switch ($status) {
			case self::STATUS_NEW:
				$this['status'] = $status;
				break;

			case self::STATUS_ACTIVE:
			case self::STATUS_CLOSED:
				$this['status'] = $status;
				if ($sub_status) {
					$status_cat = App::findEntity('DeskPRO:FeedbackStatusCategory', $sub_status);
					$this->setModelField('status_category', $status_cat);
				} else {
					$this->setModelField('status_category', null);
				}
				break;

			case self::STATUS_HIDDEN:
				$this['status'] = $status;
				$this['hidden_status'] = $sub_status;
				break;
		}
	}

	public function getStatusCode()
	{
		if ($this->status == self::STATUS_ACTIVE OR $this->status == self::STATUS_CLOSED) {
            if($this->status_category) {
                return $this->status . '.' . $this->status_category->id;
            }
            else {
                return $this->status;
            }
		} elseif ($this->status == self::STATUS_HIDDEN) {
			return $this->status . '.' . $this->hidden_status;
		} else {
			return $this->status;
		}
	}

	public function isValidating()
	{
		return ($this->hidden_status == self::HIDDEN_STATUS_VALIDATING);
	}

	public function getCategoryPath()
	{
		$path = array();

		$cat = $this->category;

		if ($cat) {
			$path[] = $cat;
			while ($cat['parent']) {
				$cat = $cat['parent'];
				$path[] = $cat;
			}
		}

		return $path;
	}


	public function addLabel($label)
	{
		$label['feedback'] = $this;
		$this->labels->add($label);
	}


	/**
	 * @return \Application\DeskPRO\Labels\LabelManager
	 */
	public function getLabelManager()
	{
		if ($this->_label_manager === null) {
			$this->_label_manager = new \Application\DeskPRO\Labels\LabelManager($this, 'DeskPRO:LabelFeedback');
		}

		return $this->_label_manager;
	}


	/**
	 * Add an attachment
	 *
	 * @param FeedbackAttachment $attach
	 */
	public function addAttachment(FeedbackAttachment $attach)
	{
		$this->attachments->add($attach);
		$attach->feedback = $this;
	}

	public function _invalidatePageCache()
	{
		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->invalidateRegex('/_feedback(-|_)/');
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
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\Feedback';
		$metadata->setPrimaryTable(array(
			'name' => 'feedback',
			'indexes' => array(
				'date_published_idx' => array('columns' => array( 0 => 'date_published' )),
				'status_idx' => array('columns' => array('status')),
			),
		));
		$metadata->addLifecycleCallback('_invalidatePageCache', 'preFlush');
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'hidden_status', 'type' => 'string', 'length' => 15, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'hidden_status', ));
		$metadata->mapField(array( 'fieldName' => 'validating', 'type' => 'string', 'length' => 35, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'validating', ));
		$metadata->mapField(array( 'fieldName' => 'popularity', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'popularity', ));
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'slug', 'type' => 'string', 'length' => 100, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'slug', ));
		$metadata->mapField(array( 'fieldName' => 'title', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'title', ));
		$metadata->mapField(array( 'fieldName' => 'content', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'content', ));
		$metadata->mapField(array( 'fieldName' => 'view_count', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'view_count', ));
		$metadata->mapField(array( 'fieldName' => 'total_rating', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'total_rating', ));
		$metadata->mapField(array( 'fieldName' => 'num_comments', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'num_comments', ));
		$metadata->mapField(array( 'fieldName' => 'num_ratings', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'num_ratings', ));
		$metadata->mapField(array( 'fieldName' => 'status', 'type' => 'string', 'length' => 15, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'status', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->mapField(array( 'fieldName' => 'date_published', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_published', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'status_category', 'targetEntity' => 'Application\\DeskPRO\\Entity\\FeedbackStatusCategory', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'status_category_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ), 'dpApi' => true ));
		$metadata->mapManyToOne(array( 'fieldName' => 'category', 'targetEntity' => 'Application\\DeskPRO\\Entity\\FeedbackCategory', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'category_id', 'referencedColumnName' => 'id', ), ), 'dpApi' => true ));
		$metadata->mapOneToMany(array( 'fieldName' => 'revisions', 'targetEntity' => 'Application\\DeskPRO\\Entity\\FeedbackRevision', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'feedback',  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'comments', 'targetEntity' => 'Application\\DeskPRO\\Entity\\FeedbackComment', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'feedback',  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'labels', 'targetEntity' => 'Application\\DeskPRO\\Entity\\LabelFeedback', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'feedback', 'orphanRemoval' => true, ));
		$metadata->mapOneToMany(array( 'fieldName' => 'custom_data', 'targetEntity' => 'Application\\DeskPRO\\Entity\\CustomDataFeedback', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'feedback', 'orphanRemoval' => true, 'dpApi' => true ));
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ), 'dpApi' => true  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'language', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Language', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'language_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), 'dpApi' => true ));
		$metadata->mapOneToMany(array( 'fieldName' => 'attachments', 'targetEntity' => 'Application\\DeskPRO\\Entity\\FeedbackAttachment', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'feedback', 'dpApi' => true, 'dpApiDeep' => true, 'dpApiPrimary' => true ));
	}
}
