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
use Application\DeskPRO\Markdown;

/**
 * Ticket messages
 *
 */
class TicketMessage extends \Application\DeskPRO\Domain\DomainObject
{
	const CREATED_WEB_PERSON        = 'web.person';
	const CREATED_WEB_PERSON_PORTAL = 'web.person.portal';
	const CREATED_WEB_AGENT         = 'web.agent';
	const CREATED_WEB_AGENT_PORTAL  = 'web.agent.portal';
	const CREATED_WEB_API           = 'web.api';
	const CREATED_MOBILE_AGENT      = 'web.api.mobile.agent';
	const CREATED_MOBILE_PERSON     = 'web.api.mobile.person';
	const CREATED_GATEWAY_PERSON    = 'gateway.person';
	const CREATED_GATEWAY_AGENT     = 'gateway.agent';

	/**
	 * @var int
	 *
	 */
	protected $id = null;

	/**
	 * @var \Application\DeskPRO\Entity\Ticket
	 */
	protected $ticket = null;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person = null;

	/**
	 * @var \Application\DeskPRO\Entity\EmailSource
	 */
	protected $email_source = null;

	/**
	 * @var \Application\DeskPRO\Entity\Visitor
	 */
	protected $visitor = null;

	/**
	 */
	protected $attachments;

	/**
	 * @var \DateTime
	 */
	protected $date_created;

	/**
	 * @var bool
	 */
	protected $is_agent_note = false;

	/**
	 * @var string
	 */
	protected $creation_system = 'web';

	/**
	 * @var string
	 */
	protected $ip_address = '';

	/**
	 * @var string
	 */
	protected $geo_country = null;

	/**
	 * The email address the user sent the email from (gateway messages only).
	 * This is a perm record and doesnt change even if the user changes/deletes their email
	 * address.
	 *
	 * @var string
	 */
	protected $email = '';

	/**
	 * @var string
	 */
	protected $message_hash;

	/**
	 * The primary translation is the one sent to the user.
	 *
	 * @var TicketMessageTranslated
	 */
	protected $primary_translation;

	/**
	 * The message, will be in HTML!
	 * @var string
	 */
	protected $message;

	/**
	 * This is the full message, including all quotes/cut content.
	 * This will still be the HTMLPurifier'ed content (so it's safe),
	 * it's just the message before it's been run through the cutter.
	 *
	 * @var string
	 */
	protected $message_full = null;

	/**
	 * This is the full raw message content. It has not been passed through
	 * any HTML cleaning process.s
	 *
	 * @var string
	 */
	protected $message_raw = null;

	/**
	 * A hint to say if we should show message_full by default. We do this when
	 * we detect that the user has replied to a message inline rather than above the cut line.
	 *
	 * @var bool
	 */
	protected $show_full_hint = false;

	/**
	 * The set/detected lang code
	 *
	 * @var string
	 */
	protected $lang_code = null;

	/**
	 * If the message was created from an email just now, then this is the reader
	 * @var \Application\DeskPRO\EmailGateway\Reader\AbstractReader
	 */
	public $email_reader;

	/**
	 * @var string
	 */
	public $withNewSubject = '';

	protected $_message_length = null;

	public function __construct()
	{
		$this->setModelField('date_created', new \DateTime());
		$this->attachments = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function setTicketId($id)
	{
		$this->setModelField('ticket', App::getEntityRepository('DeskPRO:Ticket')->find($id));
	}

	public function getTicketId()
	{
		return $this->ticket['id'];
	}

	public function setPersonId($id)
	{
		$this->setModelField('person', App::getEntityRepository('DeskPRO:Person')->find($id));
	}

	public function getPersonId()
	{
		return $this->person['id'];
	}

	public function getMessageLength()
	{
		if ($this->_message_length !== null) {
			return $this->_message_length;
		}

		$this->_message_length = strlen(strip_tags($this->message));
		return $this->_message_length;
	}

	public function getMessageHtmlClipped($max_length)
	{
		$message = $this->getMessageHtml();
		if (strlen($message) <= $max_length) {
			return $message;
		}

		$message = substr($message, 0, $max_length);

		// Just closes tags we might have chopped up
		$message = App::getContainer()->getInputCleaner()->clean($message, 'html_fix');
		return $message;
	}

	public function getMessageHtml()
	{
		return $this->procInlineAttach($this->message);
	}

	/**
	 * @param string $message
	 * @return string
	 */
	public function procInlineAttach($message)
	{
		// An email might have inline attachments and we tokenize them with these
		// codes so we can now turn them into inline images or attachment links
		$fn = function($m, $before = '') {
			$download_url = App::getSetting('core.deskpro_url');
			$download_url .= ltrim(App::getRouter()->getGenerator()->generatePath('serve_blob', array('blob_auth_id' => $m[2], 'filename' => $m[3]), false), '/');

			// Add a sign code
			// There was a bug briefly in the wild where a blob that failed to save using its primary fs
			// adapter could have the wrong authcode and be served from the db, which means any embedded images saved in the text of messages
			// (rather than being output by the router) will be incorrect
			if (substr($m[2], -1, 1) == '0') {
				$aids = App::getContainer()->getBlobStorage()->getAdapterIds();
				if (in_array('fs', $aids)) {
					$download_url .= '?sc=' . \Orb\Util\Util::generateStaticSecurityToken(App::getSetting('core.install_token') . $m[2]);
				}
			}

			if ($m[1] == 'signature_image') {
				$url = App::getSetting('core.deskpro_url');
				$url .= ltrim(App::getRouter()->getGenerator()->generatePath('serve_blob', array('blob_auth_id' => $m[2], 'filename' => $m[3]), false), '/');

				$replace = sprintf('<img src="%s" title="%s" />', $url, $m[3]);
			} else if ($m[1] == 'image') {
				$url = App::getSetting('core.deskpro_url');
				$url .= ltrim(App::getRouter()->getGenerator()->generatePath('serve_blob', array('blob_auth_id' => $m[2], 'filename' => $m[3], 's' => 350), false), '/');

				$do_link = true;

				// If we arent balanced, then it means the image is within an <a>, so
				// we shouldnt link the image ourselves
				if (substr_count($before, '<a') != substr_count($before, '</a>')) {
					$do_link = false;
				}

				if (!$do_link) {
					$replace = sprintf('<img src="%s" title="%s" />', $url, $m[3]);
				} else {
					$replace = sprintf('<a href="%s" target="_blank" class="dp-is-image"><img src="%s" title="%s" /></a>', $download_url, $url, $m[3]);
				}
			} else {
				$replace = sprintf('<a href="%s" target="_blank" class="dp-is-image">%s</a>', $download_url, $m[3]);
			}

			return $replace;
		};

		$changed = true;
		while ($changed) {
			$m = null;
			$changed = false;

			if (preg_match('#\[attach:([a-zA-Z0-9\-_\.]+):([a-zA-Z0-9\-_\.]+):([a-zA-Z0-9\-_\. ]+)\]#', $message, $m)) {
				$changed = true;
				$pos = strpos($message, $m[0]);
				$before = substr($message, 0, $pos);
				$message = str_replace($m[0], $fn($m, $before), $message);
			}
		}

		return $message;
	}

	public function convertEmbeddedImagesToInlineAttach()
	{
		$message_text = $this->message;

		foreach ($this->attachments AS $attachment) {
			if ($attachment->is_inline) {
				$blob = $attachment->blob;

				$regex = '#(<img[^>]+src=")' . preg_quote($blob->getDownloadUrl(true), '#') . '("[^>]*>)#i';
				$replace = $blob->getEmbedCode(true);
				$message_text = preg_replace($regex, $replace, $message_text);
			}
		}

		// signature images - alt contains the original text
		$regex = '#<img[^>]+class="dp-signature-image" alt="([^"]+)"[^>]*>#i';
		$message_text = preg_replace($regex, '$1', $message_text);

		$this->message = $message_text;

		return $message_text;
	}

	public function getUsedSignatureImageBlobs()
	{
		preg_match_all('#\[attach:signature_image:([a-zA-Z0-9\-_\.]+):([a-zA-Z0-9\-_\. ]+)\]#', $this->message, $matches, PREG_SET_ORDER);
		$auth_codes = array();
		foreach ($matches AS $match) {
			$auth_codes[] = $match[1];
		}

		if ($auth_codes) {
			return App::getEntityRepository('DeskPRO:Blob')->getByAuthCodes($auth_codes);
		} else {
			return array();
		}
	}

	public function getMessageText()
	{
		$message = $this->message;
		$message = strip_tags($message);

		// Decode entities in the HTML back to characters,
		// This is needed so when outputting, they arent double-encoded by twig
		// (And its just proper!)
		$message = \Orb\Util\Strings::htmlEntityDecodeUtf8($message);

		return $message;
	}

	public function getMessageFull()
	{
		return $this->procInlineAttach($this->message_full);
	}

	public function getMessagePlainHtml()
	{
		return nl2br($this->getMessageText());
	}

	public function setMessageHtml($message)
	{
		$this->setMessage($message);
	}

	/**
	 * Get a plain-text "quoted" version of the message. This is the message
	 * wrapped to 75 characters and each line preceded with a >.
	 *
	 * @return string
	 */
	public function getMessageQuote()
	{
		$message_quote = wordwrap($this->getMessageText(), 75, "\n", true);
		$message_quote = preg_replace('#^#m', "> ", $message_quote);

		return $message_quote;
	}

	public function setMessageText($message)
	{
		$this->setMessage(nl2br(htmlspecialchars($message)));
	}

	public function setMessage($message)
	{
		$message = trim((string)$message);
		if (!$message) {
			$message = App::getTranslator()->getPhraseText('user.tickets.empty_message');
		}

		$this->setModelField('message', $message);
	}

	public function addAttachment(TicketAttachment $attach)
	{
		$this->attachments->add($attach);
		$attach['ticket'] = $this->ticket;
		$attach['message'] = $this;
	}

	public function setVisitor(Visitor $visitor = null)
	{
		$this->_onPropertyChanged('visitor', $this->visitor, $visitor);
		$this->setModelField('visitor', $visitor);

		if ($visitor === null) return;

		if (!$this->ip_address && $visitor->getIpAddress()) {
			$this['ip_address'] = $visitor->getIpAddress();
		}

		if ($visitor && $visitor->last_track && $visitor->last_track->geo_country) {
			$this['geo_country'] = $visitor->last_track->geo_country;
		}

		if ($this->ip_address && !$this->geo_country) {
			$geoip = App::getSystemService('geo_ip');
			$geo = $geoip->lookup($this->ip_address);
			$this['geo_country'] = !empty($geo['country']) ? $geo['country'] : '';
		}
	}


	/**
	 * @param string $geo_country Two-letter country code or null
	 */
	public function setGeoCountry($geo_country)
	{
		$this->setModelField('geo_country', $geo_country ?: null);
	}


	/**
	 * @return string
	 */
	public function getGeoCountry()
	{
		return $this->geo_country;
	}


	public function setVisitorFromRequest()
	{
		if (App::has('session')) {
			$v = App::getSession()->getVisitor();
			$this->setVisitor($v);
		}
	}

	/**
	 * Did this message originate from a gateway?
	 *
	 * @return bool
	 */
	public function isFromGateway()
	{
		if (strpos($this->creation_system, 'gateway') === 0) {
			return true;
		}

		return false;
	}

	public function getMessageHash()
	{
		if (!$this->message_hash) {
			$this->initHashCode();
		}

		return $this->message_hash;
	}

	/**
	 * Inits the hash code for this message
	 *
	 */
	public function initHashCode()
	{
		if ($this->message_hash) {
			return;
		}

		$hashes = array();
		$hashes[] = sha1($this->message . ($this->person ? $this->person->id : 'noperson'));

		foreach ($this->attachments as $a) {
			$hashes[] = $a->blob['blob_hash'];
		}

		// Sort hashes so theyre always the same order
		sort($hashes, \SORT_STRING);

		$this->message_hash = sha1(implode('', $hashes));
		$this->_onPropertyChanged('message_hash', '', $this->message_hash);
	}

	/**
	 * When a new message is added to a ticket, make sure the person has
	 * their own access code ready to use.
	 *
	 */
	public function initPersonAccessCode()
	{
		if ($this->id) {
			App::getEntityRepository('DeskPRO:Cache')->delete("ticket_messages.{$this->ticket['id']}");
		}

		$this->ticket->addAccessCodeForPerson($this->person);
	}

	public function incTicketCount()
	{
		if (!$this->ticket) {
			return;
		}

		if (!$this->is_agent_note && $this->person->is_agent) {
			$this->ticket->count_agent_replies++;
		} else {
			$this->ticket->count_user_replies++;
		}
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\TicketMessage';
		$metadata->setPrimaryTable(array( 'name' => 'tickets_messages', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->addLifecycleCallback('initHashCode', 'prePersist');
		$metadata->addLifecycleCallback('incTicketCount', 'prePersist');
		$metadata->addLifecycleCallback('initPersonAccessCode', 'postPersist');
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->mapField(array( 'fieldName' => 'is_agent_note', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_agent_note', ));
		$metadata->mapField(array( 'fieldName' => 'creation_system', 'type' => 'string', 'length' => 20, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'creation_system', ));
		$metadata->mapField(array( 'fieldName' => 'ip_address', 'type' => 'string', 'length' => 30, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'ip_address', ));
		$metadata->mapField(array( 'fieldName' => 'geo_country', 'type' => 'string', 'length' => 10, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'geo_country', ));
		$metadata->mapField(array( 'fieldName' => 'email', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'email', ));
		$metadata->mapField(array( 'fieldName' => 'message_hash', 'type' => 'string', 'length' => 40, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'message_hash', ));
		$metadata->mapField(array( 'fieldName' => 'message', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'message', ));
		$metadata->mapField(array( 'fieldName' => 'message_full', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'message_full', ));
		$metadata->mapField(array( 'fieldName' => 'message_raw', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'message_raw', ));
		$metadata->mapField(array( 'fieldName' => 'lang_code', 'type' => 'string', 'length' => 80, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'lang_code', ));
		$metadata->mapField(array( 'fieldName' => 'show_full_hint', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'show_full_hint', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'ticket', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Ticket', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'ticket_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ), 'dpApi' => true  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'email_source', 'targetEntity' => 'Application\\DeskPRO\\Entity\\EmailSource', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'email_source_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'primary_translation', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TicketMessageTranslated', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'message_translated_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'visitor', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Visitor', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'visitor_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'attachments', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TicketAttachment', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'message', 'dpApi' => true, 'dpApiDeep' => true  ));
	}
}
