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
 */

namespace Application\DeskPRO\EmailGateway;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;
use Application\DeskPRO\EmailGateway\AbstractGatewayProcessor;
use Application\DeskPRO\EmailGateway\Cutter\CutterDefFactory;
use Application\DeskPRO\EmailGateway\Cutter\ForwardCutter;

class ArticleGatewayProcessor extends AbstractGatewayProcessor
{
	const EVENT_EVENT                    = 'DeskPRO_onArticleGatewayInit';
	const EVENT_BEFORE_RUN_ACTION        = 'DeskPRO_onBeforeArticleGatewayRunAction';
	const EVENT_RUN_ACTION               = 'DeskPRO_onArticleGatewayRunAction';
	const EVENT_BEFORE_NEWARTICLE        = 'DeskPRO_onBeforeArticleGatewayNewArticle';
	const EVENT_NEWARTICLE               = 'DeskPRO_onArticleGatewayNewArticle';
	const EVENT_BEFORE_FWD_NEWARTICLE    = 'DeskPRO_onBeforeArticleGatewayNewFwdArticle';
	const EVENT_FWD_NEWARTICLE           = 'DeskPRO_onArticleGatewayNewFwdArticle';

	/**
	 * @var \Application\DeskPRO\EmailGateway\Cutter\Def\Generic
	 */
	protected $cutterDef;

	/**
	 * True when there was an error converting an incoming charset to utf8.
	 * When this happens, the standard is to use the original string (unconverted)
	 * and save an original version of the message.
	 *
	 * @var bool
	 */
	protected $charset_error = false;

	/**
	 * The person  submitting the article draft.
	 *
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	protected $error;
	protected $source_info;
	protected $inline_blobs = array();
	protected $dupe_inline_blobs = array();

	protected function init()
	{
		$this->cutterDef = CutterDefFactory::getDef($this->reader);
	}

	public function run()
	{
		// Better dupe checking based on the actual email being submitted.
		if($this->reader->hasProperty('email_source') && $this->reader->getProperty('email_source')->uid && $this->gateway) {
			$has_processed = App::getDb()->fetchColumn("
				SELECT id
				FROM email_sources
				WHERE uid = ? AND gateway_id = ? AND status = 'complete'
				LIMIT 1
			", array($this->reader->getProperty('email_source')->uid, $this->gateway->getId()));

			if ($has_processed) {
				$this->error = \Application\DeskPRO\Entity\EmailSource::ERR_DUPE;
				$this->logMessage(sprintf("[ArticleGatewayProcessor] Detected duplicate for source %d", $this->reader->getProperty('email_source')->uid));
				return null;
			}
		}

		$person_processor = new PersonFromEmailProcessor();

		#-------------------------
		# Run detectors to see if its a reply
		#-------------------------

		$person = null;

		$bounce_detector = new \Application\DeskPRO\EmailGateway\BounceDetector($this->reader, App::getOrm());
		$bounce_detector->setLogger($this->logger);

		if ($bounce_detector->isBounced()) {
			$this->logMessage("[ArticleGatewayProcessor] Is bounced");
			return null;
		}

		// If the detector didnt find a person, doesnt mean they dont exist
		$person = $person_processor->findPerson($this->reader->getFromAddress());
		if (!$person || !$person->is_agent || $person->is_deleted) {
			$this->logMessage('[ArticleGatewayProcessor] No person or not an agent for email: ' . $this->reader->getFromAddress()->getEmail());
			$this->error = \Application\DeskPRO\Entity\EmailSource::ERR_PERM_INSUFFICIENT;

			if ($this->gateway) {
				$cutoff_date = gmdate('Y-m-d H:i:s', time() - 86400);

				$has_processed = App::getDb()->fetchColumn("
					SELECT id
					FROM email_sources
					WHERE gateway_id = ?
						AND date_created > ?
						AND header_from LIKE ?
						AND status = 'error'
						AND error_code = 'perm_insufficient'
					LIMIT 1
				", array($this->gateway->getId(), $cutoff_date, '%' . $this->reader->getFromAddress()->getEmail() . '%'));

				if ($has_processed) {
					return null;
				}
			}

			$message = App::getMailer()->createMessage();
			$message->setTemplate('DeskPRO:emails_agent:error-agent-only.html.twig', array(
				'subject' => $this->reader->getSubject()->getSubjectUtf8(),
				'name'    => $this->reader->getFromAddress()->getName() ?: $this->reader->getFromAddress()->getEmail(),
			));
			$message->setTo($this->reader->getFromAddress()->getEmail());
			App::getMailer()->send($message);

			return null;
		}

		$ev = $this->createGatewayEvent(array(
			'person' => $person,
			'cancel' => false
		));
		$this->event_dispatcher->dispatch(self::EVENT_BEFORE_RUN_ACTION, $ev);

		if ($ev->cancel) {
			return null;
		}

		if (ForwardCutter::subjectIsForward($this->reader->getSubject()->subject)) {
			$this->logMessage('[ArticleGatewayProcessor] runNewForwardedArticle');
			$ret = $this->runNewForwardedArticle($person);
		} else {
			$this->logMessage('[ArticleGatewayProcessor] runNewArticle');
			$ret = $this->runNewArticle($person);
		}

		$ev = $this->createGatewayEvent(array(
			'person' => $person,
			'return' => $ret
		));
		$this->event_dispatcher->dispatch(self::EVENT_RUN_ACTION, $ev);

		return $ret;
	}

	public function trimHtmlWhitespace($html)
	{
		return \Orb\Util\Strings::trimHtmlAdvanced($html);
	}

	protected function runNewArticle(Entity\Person $person)
	{
		$this->person = $person;

		#------------------------------
		# Read email body/subject
		#------------------------------

		$email_info = array();

		$this->processBlobs();
		$inline_images = new InlineImageTokens($this->reader);

		$email_info['subject'] = $this->reader->getSubject()->getSubjectUtf8();
		if (!$email_info['subject'] && $this->reader->getSubject()->getSubject()) {
			$email_info['subject'] = $this->reader->getSubject()->getSubject();
		}

		if ($this->reader->getBodyHtml()->getBody()) {
			$this->logMessage('[ArticleGatewayProcessor] runNewArticle read HTML email');
			$email_info['body'] = $this->reader->getBodyHtml()->getBodyUtf8();
			if (!$email_info['body']) {
				$email_info['body'] = $this->reader->getBodyHtml()->getBody();
				$this->charset_error = $this->reader->getBodyHtml()->getOriginalCharset();
			}

			$email_info['body_is_html'] = true;
		} else {
			$this->logMessage('[ArticleGatewayProcessor] runNewArticle read text email');
			$txt = $this->reader->getBodyText()->getBodyUtf8();
			if (!$txt && $this->reader->getBodyText()->getBody()) {
				$txt = $this->reader->getBodyText()->getBody();
				$this->charset_error = $this->reader->getBodyText()->getOriginalCharset();
			}

			$email_info['body'] = str_replace(array("\n", "\r"), '', nl2br(@htmlspecialchars($txt, \ENT_QUOTES, 'UTF-8')));
			$email_info['body_is_html'] = false;
		}

		// Replace inline image tags with tokens
		$email_info['body_raw'] = $email_info['body'];
		$email_info['body'] = $inline_images->processTokens($email_info['body']);
		$email_info['body_full'] = '';

		if ($email_info['body_is_html']) {
			// The basic cleaner cleans out outlook type stuff like empty <p>'s that cause whitespace
			$email_info['body'] = $this->cleaner->clean($email_info['body'], 'html_email_preclean');
			$email_info['body'] = $this->cleaner->clean($email_info['body'], 'html_email_basicclean');
			$email_info['body'] = $this->cleaner->clean($email_info['body'], 'html_email');
		}

		$email_info['body'] = \Orb\Util\Strings::trimHtml($email_info['body']);

		$ev = $this->createGatewayEvent(array(
			'person' => $person,
			'email_info' => $email_info,
			'cancel' => false,
		));
		$this->event_dispatcher->dispatch(self::EVENT_BEFORE_NEWARTICLE, $ev);

		if ($ev->cancel) {
			return null;
		}

		$email_info = $ev->email_info;

		$email_info['body'] = $this->cleaner->clean($email_info['body'], 'html_email_postclean');
		$email_info['body'] = $this->replaceInlineAttachTokens($email_info['body'], $inline_images);

		#------------------------------
		# Create the article
		#------------------------------

		$article = new \Application\DeskPRO\Entity\Article();
		$article->title = $email_info['subject'];
		$article->content = $email_info['body'];
		$article->setStatusCode('hidden.draft');
		$article->person = $person;
		if ($this->gateway->getProcessorExtra('category_id')) {
			$category = App::getOrm()->find('DeskPRO:ArticleCategory', $this->gateway->getProcessorExtra('category_id'));
			if ($category) {
				$article->addToCategory($category);
			}
		}

		App::getDb()->beginTransaction();

		try {
			if ($this->processBlobs()) {
				foreach ($this->processBlobs() AS $blob) {
					$attach = new \Application\DeskPRO\Entity\ArticleAttachment();
					$attach->blob = $blob;
					$attach->person = $person;
					$article->addAttachment($attach);
				}
			}

			App::getOrm()->persist($article);
			App::getOrm()->flush();

			$this->logMessage('[ArticleGatewayProcessor] Created article ' . $article['id']);

			App::getDb()->commit();
		} catch (\Exception $e) {
			App::getDb()->rollback();
			throw $e;
		}

		$ev = $this->createGatewayEvent(array(
			'article' => $article,
			'person' => $person,
		));
		$this->event_dispatcher->dispatch(self::EVENT_NEWARTICLE, $ev);

		return $article;
	}

	protected function runNewForwardedArticle(Entity\Person $agent)
	{
		$this->person = $agent;

		$this->logMessage('[ArticleGatewayProcessor] Forwarded article by ' . $agent->getId() . ' ' . $agent->getDisplayContact());

		#------------------------------
		# Read in email props and create cutter
		#------------------------------

		$email_info = array();
		$email_info['subject'] = $this->reader->getSubject()->subject;
		if ($email_info['body'] = $this->getBodyPlain()) {
			$email_info['body_is_html'] = false;
		} else {
			$email_info['body'] = $this->reader->getBodyHtml()->getBodyUtf8();
			$email_info['body_is_html'] = false;
			$email_info['body'] = \Orb\Util\Strings::html2Text($email_info['body']);
		}

		$fwd_cutter = new ForwardCutter($email_info['body'], $email_info['body_is_html'], $this->cutterDef);

		$ev = $this->createGatewayEvent(array(
			'email_info' => $email_info,
			'fwd_cutter' => $fwd_cutter,
			'cancel' => false,
		));

		$this->event_dispatcher->dispatch(self::EVENT_BEFORE_FWD_NEWARTICLE, $ev);

		if ($ev->cancel OR !$fwd_cutter->isValid()) {
			$this->logMessage('[ArticleGatewayProcessor] Invalid forward');
			$this->error = \Application\DeskPRO\Entity\EmailSource::ERR_INVALID_FWD;

			$message = App::getMailer()->createMessage();
			$message->setTemplate('DeskPRO:emails_agent:error-invalid-forward.html.twig', array(
				'subject' => $this->reader->getSubject()->getSubjectUtf8(),
				'name'    => $this->reader->getFromAddress()->getName() ?: $this->reader->getFromAddress()->getEmail(),
			));
			$message->setTo($this->reader->getFromAddress()->getEmail());
			$message->attach(\Swift_Attachment::newInstance(
				$this->reader->getRawSource(),
				'message.eml',
				'message/rfc822'
			));

			App::getMailer()->send($message);

			return null;
		}

		$email_info['subject'] = ForwardCutter::cutSubjectForwardPrefix($email_info['subject']);

		#------------------------------
		# Create article
		#------------------------------

		$article = new \Application\DeskPRO\Entity\Article();
		$article->title = $email_info['subject'];
		$article->content = $email_info['body'];
		$article->setStatusCode('hidden.draft');
		$article->person = $agent;
		if ($this->gateway->getProcessorExtra('category_id')) {
			$category = App::getOrm()->find('DeskPRO:ArticleCategory', $this->gateway->getProcessorExtra('category_id'));
			if ($category) {
				$article->addToCategory($category);
			}
		}

		App::getDb()->beginTransaction();

		try {
			if ($this->processBlobs()) {
				foreach ($this->processBlobs() AS $blob) {
					$attach = new \Application\DeskPRO\Entity\ArticleAttachment();
					$attach->blob = $blob;
					$attach->person = $agent;
					$article->addAttachment($attach);
				}
			}

			App::getOrm()->persist($article);
			App::getOrm()->flush();

			$this->logMessage('[ArticleGatewayProcessor] Created article ' . $article['id']);

			App::getDb()->commit();
		} catch (\Exception $e) {
			App::getDb()->rollback();
			throw $e;
		}

		$ev = $this->createGatewayEvent(array(
			'article' => $article,
			'person' => $agent,
		));
		$this->event_dispatcher->dispatch(self::EVENT_FWD_NEWARTICLE, $ev);

		return $article;
	}

	public function replaceInlineAttachTokens($body, InlineImageTokens $inline_images)
	{
		$exist_inline_blobs = array();

		foreach ($inline_images->getCids() as $cid) {
			if (!isset($this->processed_blobs_cid[$cid])) {
				continue;
			}

			$blob = $this->processed_blobs_cid[$cid];

			// If this article already has a blob like this,
			// then mark it as a dupe and rewrite the inline reference
			// to the one we've already saved
			if (isset($exist_inline_blobs[$blob->blob_hash])) {
				$this->logMessage(sprintf("Duplicate inline blob %s is being discarded, existing blob %s will be used", $blob->getFilenameSafe(), $blob->getId()));
				$this->dupe_inline_blobs[$blob->getId()] = $blob;
				$blob = $exist_inline_blobs[$blob->blob_hash];
			}

			if ($blob->isImage()) {
				$this->inline_blobs[$blob->getId()] = $blob;
				$replace = '<img src="' . $blob->getDownloadUrl() . '" alt="" />';

				$body = $inline_images->replaceToken($cid, $replace, $body);
			}
		}

		return $body;
	}

	public function getErrorCode()
	{
		return $this->error;
	}

	public function getSourceInfo()
	{
		if ($this->source_info) {
			$messages = is_array($this->source_info) ? $this->source_info : array($this->source_info);
		} else {
			$messages = array();
		}

		if (isset($this->options['logger_messages'])) {
			$messages = array_merge($messages, $this->options['logger_messages']->getMessages());
		}

		return $messages;
	}

	public function getBodyPlain()
	{
		$plain = $this->reader->getBodyText()->getBodyUtf8();
		if (!$plain) {
			return false;
		}

		// Outlook enters two line breaks for every one the user actually entered
		// because its stupid and selfish.
		// Lets clean up that superfluous whitespace now.
		$is_outlook = false;

		$mailer = $this->reader->getHeader('X-Mailer');
		if ($mailer && strpos($mailer->getHeader(), 'Outlook') !== false) {
			$is_outlook = true;
		}
		if (!$is_outlook) {
			$headers = $this->reader->getRawHeaders();
			if (preg_match('#^X\-MS\-#', $headers)) {
				$is_outlook = true;
			}
		}

		if ($is_outlook) {
			$plain = \Orb\Util\Strings::standardEol($plain);
			$plain = str_replace("\n\n", "\n", $plain);
		}

		return $plain;
	}
}
