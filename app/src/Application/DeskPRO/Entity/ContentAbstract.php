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
use Application\DeskPRO\Translate\HasPhraseName;
use Application\DeskPRO\Translate\Translate;

use Orb\Util\Strings;
use Orb\Util\Util;

/**
 * Basic properties on content
 *
 */
abstract class ContentAbstract extends \Application\DeskPRO\Domain\DomainObject
{
	const STATUS_PUBLISHED   = 'published';
	const STATUS_ARCHIVED    = 'archived';
	const STATUS_HIDDEN      = 'hidden';

	const HIDDEN_STATUS_UNPUBLISHED   = 'unpublished';
	const HIDDEN_STATUS_VALIDATING    = 'validating';
	const HIDDEN_STATUS_USER_VALIDATING = 'user_validating';
	const HIDDEN_STATUS_DELETED       = 'deleted';
	const HIDDEN_STATUS_SPAM          = 'spam';
	const HIDDEN_STATUS_DRAFT         = 'draft';
	const HIDDEN_STATUS_TEMP          = 'temp';

	/**
	 * @var int
	 */
	protected $id = null;

		/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person = null;

	/**
	 * @var Language
	 */
	protected $language = null;

	/**
	 * @var string
	 */
	protected $slug;

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * The main content for the item. This should be HTML!
	 *
	 * @var string
	 */
	protected $content;

	/**
	 * View counts
	 *
	 * @var string
	 */
	protected $view_count = 0;

	/**
	 * Total rating: This is a tally and must be updated when a rating is added
	 *
	 * @var string
	 */
	protected $total_rating = 0;

	/**
	 * Number of user-visible comments: This is a count that must be updated when a comment is added
	 *
	 * @var int
	 */
	protected $num_comments = 0;

	/**
	 * Total rating
	 *
	 * @var string
	 */
	protected $num_ratings = 0;

	/**
	 * @var string
	 */
	protected $status;

	/**
	 * @var string
	 */
	protected $hidden_status = null;

	/**
	 * @var \DateTime
	 */
	protected $date_created;

	/**
	 * @var \DateTime
	 */
	protected $date_published;

	// Implement in children
	///**
	// * @var \Doctrine\Common\Collections\ArrayCollection
	// */
	//protected $revisions;

	// Implement in children
	///**
	// */
	//protected $labels;

	/**
	 * An array of authors,
	 */
	protected $_authors = null;

	/**
	 * @var \Application\DeskPRO\Labels\LabelManager
	 */
	protected $_label_manager = null;

	public function __construct()
	{
		$this['date_created'] = new \DateTime();
		$this->revisions = new \Doctrine\Common\Collections\ArrayCollection();
		$this->labels = new \Doctrine\Common\Collections\ArrayCollection();

		$this['status'] = self::STATUS_HIDDEN;
		$this['hidden_status'] = self::HIDDEN_STATUS_DRAFT;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function setTitle($title)
	{
		$this->setModelField('title', $title);

		if (!$this->slug) {
			$this['slug']  = Strings::slugifyTitle($title);
			if (!$this['slug']) {
				$this['slug'] = 'view';
			}
		}
	}

	public function getLanguage()
	{
		if ($this->language) {
			return $this->language;
		}

		return App::getContainer()->getLanguageData()->getDefault();
	}

	public function getRealLanguage()
	{
		return $this->language;
	}

	public function setStatus($status)
	{
		$this->setStatusCode($status);
	}

	public function setStatusCode($status_code)
	{
		if (strpos($status_code, 'hidden.') === 0) {
			$status_code = str_replace('hidden.', '', $status_code);
			$this->setModelField('status', 'hidden');
			$this->setModelField('hidden_status', $status_code);

			$this->setModelField('date_published', null);
		} else {
			$this->setModelField('status', $status_code);
			$this->setModelField('hidden_status', null);

			if (!$this->date_published) {
				$this->setModelField('date_published', new \DateTime());
			}
		}
	}

	public function getStatusCode()
	{
		if ($this->hidden_status) {
			return 'hidden.' . $this->hidden_status;
		} else {
			return $this->status;
		}
	}

	public function contentModifier($content)
	{
		// Find attach replacements: ![attach:{$blob['authcode']}:{$blob['filename']}]
		$fn = function($m) {
			return App::getSetting('core.deskpro_url') . 'file.php/' . $m[1] . '/' . urlencode($m[2]);
		};
		$content = preg_replace_callback('#!\[attach:([0-9A-Z]+):(.*?)\]#', $fn, $content);

		return $content;
	}

	public function getContentHtml()
	{
		return $this['content'];
	}

	public function getContentPlainHtml()
	{
		$content = htmlspecialchars($this['content']);
		$content = nl2br($content);

		return $content;
	}

	public function getContentPlain()
	{
		$content = $this['content'];
		if (!$content) {
			return '';
		}
		$content = Strings::standardEol($this['content']);
		$content = preg_replace("#<br\s*/?><p>#", "<p>", $content);
		$content = preg_replace("#<p></p><br\s*/?>#", "<p>", $content);
		$content = preg_replace("#</p><br\s*/?>#", "</p>", $content);
		$content = preg_replace("#<br\s*/?></p>#", "</p>", $content);
		$content = preg_replace("#<br\s*/?>?#", "\n", $content);
		$content = preg_replace("#<p>\n?#", "\n", $content);
		$content = preg_replace("#\n?</p>#", "\n", $content);
		$content = html_entity_decode(strip_tags($content), \ENT_QUOTES, 'UTF-8');
		$content = str_replace('&nbsp;', ' ', $content);
		$content = trim($content);

		$lines_raw = explode("\n", $content);
		$lines = array();
		foreach ($lines_raw as $l) {
			$lines[] = trim($l);
		}

		$content = implode("\n", $lines);
		$content = preg_replace("#\n{3,}#", "\n\n", $content);

		return $content;
	}

	/**
	 * Get an excerpt of the content suitable for display in a search listing. So this means
	 * no html, and collapsed whitespace.
	 */
	public function getSearchSummary($length = 100)
	{
		$content = $this->getContentPlain();
		$content = str_replace(array("\r\n", "\n"), " ", $content);

		if (Strings::utf8_strlen($content) > $length) {
			$content = Strings::utf8_substr($content, 0, $length) . '...';
		}

		return $content;
	}

	public function getUrlSlug()
	{
		return $this->id . '-' . $this->slug;
	}

	abstract public function getLink();

	abstract public function getPermalink();

	/**
	 * Get an array of authors
	 *
	 * @return array
	 */
	public function getAuthors()
	{
		if ($this->_authors !== null) {
			return $this->_authors;
		}

		$this->_authors = array();

		if ($this->person) {
			$this->_authors[$this->person['id']] = $this->person;
		}

		$ent = $this->getEntityName() . 'Revision';
		$field = strtolower(str_replace('DeskPRO:', '', $this->getEntityName()));

		$revs = App::getOrm()->createQuery("
			SELECT r, p
			FROM $ent r
			LEFT JOIN r.person p
			WHERE r.$field = ?1 AND r.person IS NOT NULL
			ORDER BY r.date_created DESC
		")->setParameter(1, $this)->execute();

		foreach ($revs as $r) {
			if ($r->person) {
				$this->_authors[$r->person->id] = $r->person;
			}
		}

		return $this->_authors;
	}

	public function getByLine($sep = ', ')
	{
		$names = array();
		foreach ($this->getAuthors() as $a) {
			$names[] = $a->getDisplayName();
		}

		return implode($sep, $names);
	}

	public function getVoteStats()
	{
		$x = $this->num_ratings - abs($this->total_rating);

		if ($x % 2 == 1) {
			$x++; // never happens with correct data, this just error corrects
		}

		if ($this->total_rating >= 0) {
			$up   = ($x / 2) + $this->total_rating;
			$down =  ($x / 2);
		} else {
			$up   = ($x / 2);
			$down = ($x / 2) + abs($this->total_rating);
		}

		return array('up' => $up, 'down' => $down);
	}

	public function getUpVotes()
	{
		$stats = $this->getVoteStats();
		return $stats['up'];
	}

	public function getDownVotes()
	{
		$stats = $this->getVoteStats();
		return $stats['down'];
	}

	public function getRatingPercent()
	{
		if (!$this->num_ratings) {
			return 0;
		}

		return min(100, ceil(($this->total_rating / $this->num_ratings) * 100));
	}

	public function addRating($rating)
	{
		$this['num_ratings'] = $this->num_ratings + 1;
		$this['total_rating'] = $this->total_rating + $rating->rating;
		$rating->setContentObject($this);
	}

	public function removeRating($rating)
	{
		$this['num_ratings']   = $this->num_ratings - 1;
		$this['total_rating'] = $this->total_rating - $rating->rating;
	}

	public function addComment($comment)
	{
		$this->num_comments++;
		$comment->setObject($this);
	}

	public function removeComment($comment)
	{
		$this->num_comments--;
	}

	/**
	 * @return \Application\DeskPRO\Labels\LabelManager
	 */
	public function getLabelManager()
	{
		if ($this->_label_manager === null) {
			$name = Util::getBaseClassname($this);
			$this->_label_manager = new \Application\DeskPRO\Labels\LabelManager($this, 'DeskPRO:Label' . $name);
		}

		return $this->_label_manager;
	}


	/**
	 * @return string
	 */
	public static function getContentType()
	{
		static $name = null;

		if ($name === null) {
			$name = self::getEntityName();
			$name = strtolower(str_replace('DeskPRO:', '', $name));
		}

		return $name;
	}


	/**
	 * @return string
	 */
	public function getRealTitle()
	{
		return $this->title;
	}


	/**
	 * @return string
	 */
	public function getRealContent()
	{
		return $this->content;
	}

	/**
	 * @return string
	 */
	public function setRealTitle($title)
	{
		$this->setModelField('title', $title);
	}


	/**
	 * @return string
	 */
	public function setRealContent($content)
	{
		$this->setModelField('content', $content);
	}
}
