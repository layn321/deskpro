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

use Application\DeskPRO\Markdown;
use Application\DeskPRO\App;

use Orb\Util\Util;
use Orb\Util\Strings;
use Orb\Util\Arrays;

/**
 * Base comments
 *
 */
abstract class CommentAbstract extends \Application\DeskPRO\Domain\DomainObject
{
	const OBJ_PROP = '__abstract__';

	const STATUS_VISIBLE    = 'visible';
	const STATUS_VALIDATING = 'validating';
	const STATUS_USER_VALIDATING = 'user_validating';
	const STATUS_TEMP       = 'temp';
	const STATUS_DELETED    = 'deleted';
	const STATUS_AGENT      = 'agent';

	/**
	 * The unique ID.
	 *
	 * @var int
	 */
	protected $id = null;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person = null;

	/**
	 * @var \Application\DeskPRO\Entity\Visitor
	 */
	protected $visitor = null;

	/**
	 * @var string
	 */
	protected $ip_address = '';

	/**
	 * @var string
	 */
	protected $email = null;

	/**
	 * @var string
	 */
	protected $name = null;

	/**
	 * @var string
	 */
	protected $website = null;

	/**
	 * @var string
	 */
	protected $content;

	/**
	 * @var string
	 */
	protected $status = 'visible';

	/**
	 * @var string
	 */
	protected $validating = null;

	/**
	 * Has this comment been reviewed? Either validated, or
	 * if it was published, seen to.
	 *
	 * @var bool
	 */
	protected $is_reviewed = false;

	/**
	 * @var \DateTime
	 */
	protected $date_created;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @static
	 * @param Person $person
	 * @param bool $use_request Use the current request to set visitor (and thus ip etc)
	 * @return \Application\DeskPRO\Entity\CommentAbstract
	 */
	public static function newForPerson(Person $person, $use_request = true)
	{
		$comment = new static();
		$comment->person = $person;

		if ($use_request) {
			$comment->visitor = App::getSession()->getVisitor();
		}

		return $comment;
	}

	public function __construct()
	{
		$this['date_created'] = new \DateTime();
	}


	/**
	 * Get the email address for the person who made the comment, trying
	 * the person record first if it exists.
	 *
	 * @return string
	 */
	public function getUserEmail()
	{
		if ($this->person) {
			return $this->person->getPrimaryEmailAddress();
		} elseif ($this->email) {
			return $this->email;
		} else {
			return '';
		}
	}


	/**
	 * Get the name for the person who made the comment, trying
	 * the person record first if it exists.
	 *
	 * @param $force_user If true, forces the user display name
	 *
	 * @return string
	 */
	public function getUserName($force_user = false)
	{
		if ($this->person) {
			if (DP_INTERFACE == 'user' || $force_user) {
				return $this->person->getDisplayNameUser();
			} else {
				return $this->person->getDisplayName();
			}
		} elseif ($this->name) {
			return $this->name;
		} else {
			return '';
		}
	}


	/**
	 * @return string
	 */
	public function getUserDisplayContact()
	{
		if ($this->person) {
			return $this->person->getDisplayContact();
		} else {
			$display = $this->getUserName();
			if ($this->getUserEmail()) {
				$display .= ' <'. $this->getUserEmail() . '>';
			}

			return $display;
		}
	}


	/**
	 * Set the visitor of the person who made this comment. If the name
	 * and email arent set they will be set to values of the visitor.
	 *
	 * @return string
	 */
	public function setVisitor(Visitor $visitor = null)
	{
		$this->setModelField('visitor', $visitor);

		if ($visitor === null) return;

		$this['ip_address'] = $visitor['ip_address'];

		if (!$this->name AND $visitor['name']) {
			$this['name'] = $visitor['name'];
		}
		if (!$this->email AND $visitor['email']) {
			$this['email'] = $visitor['email'];
		}
	}


	/**
	 * Set the Status
	 *
	 * @param $new_status
	 */
	public function setStatus($new_status)
	{
		// any time after its created and the status is set
		// to visible means someone has reviewed its
		if ($this->id && $new_status == 'visible') {
			$this->setModelField('is_reviewed' , true);
		}

		$this->setModelField('status', $new_status);
	}

	/**
	 * @return string
	 */
	public function getContentHtml()
	{
		return Strings::linkify(nl2br(htmlspecialchars($this->content, \ENT_NOQUOTES, 'UTF-8')));
	}


	public function getContentReal()
	{
		return $this->content;
	}


	/**
	 * @return string
	 */
	public function getContentHtmlPlain()
	{
		return nl2br(htmlspecialchars($this->content));
	}


	/**
	 * Strip all HTML from the content and convert breaks and paragraphs to linebreaks.
	 * Suitable for showing a "plain text" version of the content.
	 *
	 * @return string
	 */
	public function getContentPlain()
	{
		if (!$this->content) {
			return '';
		}
		$content = Strings::standardEol($this->content);
		$content = preg_replace("#<br\s*/?><p>#", "<p>", $content);
		$content = preg_replace("#<p></p><br\s*/?>#", "<p>", $content);
		$content = preg_replace("#</p><br\s*/?>#", "</p>", $content);
		$content = preg_replace("#<br\s*/?></p>#", "</p>", $content);
		$content = preg_replace("#<br\s*/?>?#", "\n", $content);
		$content = preg_replace("#<p>\n?#", "\n", $content);
		$content = preg_replace("#\n?</p>#", "\n", $content);
		$content = html_entity_decode(strip_tags($content), \ENT_QUOTES, 'UTF-8');
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
	 * Get the author ID
	 *
	 * @return int
	 */
	public function getPersonId()
	{
		if ($this->person) {
			return $this->person->getId();
		}

		return 0;
	}


	/**
	 * Get the entity this comment is attached to. This is a standardized way to fetch the
	 * entity when you might not know the $comment->XXX to use.
	 *
	 * @return mixed
	 */
	public function getObject()
	{
		$prop = static::OBJ_PROP;
		return $this->$prop;
	}


	/**
	 * Set the content object
	 *
	 * @param mixed $obj
	 */
	public function setObject($obj)
	{
		$prop = static::OBJ_PROP;
		$this[$prop] = $obj;
	}


	/**
	 * Get the base clasname of the object
	 *
	 * @return string
	 */
	public function getObjectType()
	{
		return Util::getBaseClassname($this->getObject());
	}


	/**
	 * Get the "content-type" of the object on this comment
	 *
	 * @return string
	 */
	public function getObjectContentType()
	{
		return $this->getObject()->getTableName();
	}
}
