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
 * @subpackage
 */

namespace Application\DeskPRO\Mail;

use Application\DeskPRO\Entity;
use DeskPRO\Kernel\KernelErrorHandler;
use Orb\Html\Html2Text;
use Orb\Util\Arrays;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\Blob;
use Application\DeskPRO\App;

class Message extends \Orb\Mail\Message
{
	/**
	 * @var string
	 */
	protected $track_code;

	/**
	 * @var string
	 */
	protected $context_id;

	/**
	 * @var \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
	 */
	protected $template_engine;

	/**
	 * @var string
	 */
	protected $template;

	/**
	 * @var array
	 */
	protected $template_vars;

	/**
	 * @var null
	 */
	protected $set_to = null;

	/**
	 * @var Person
	 */
	protected $set_to_person = null;

	/**
	 * @var \Application\DeskPRO\Entity\Blob[]
	 */
	protected $attach_blobs = array();

	/**
	 * @var array
	 */
	protected $embed_only = array();

	/**
	 * Is the message being re-sent?
	 *
	 * @var bool
	 */
	protected $is_retrying = false;

	/**
	 * @var array
	 */
	protected $log_messages = array();


	/**
	 * Set a context about this message. The mailer might treat it differently.
	 */
	public function setContextId($context_id)
	{
		$this->context_id = $context_id;
	}


	/**
	 * @return string
	 */
	public function getContextId()
	{
		return $this->context_id;
	}

	public function doPrepare()
	{
		if ($this->template) {
			if ($this->set_to) {
				$this->template_vars['to_email']   = $this->set_to['email'];
				$this->template_vars['to_name']    = !empty($this->set_to['name']) ? $this->set_to['name'] : $this->set_to['email'];
				$this->template_vars['to_contact'] = !empty($this->set_to['name']) ? $this->set_to['name'] . ' <' . $this->set_to['email'] . '>' : $this->set_to['email'];

				$skip_check = array(
					// Agent email sent to an unknown email address for agent ticket replies
					// ("your reply was not accepted because it was sent from an unknown address")
					'DeskPRO:emails_agent:error-unknown-from.html.twig' => 1,

					// If you created a new agent after calling the AgentDataServer,
					// then the repository wont contain the new agent when sending this welcome.
					'DeskPRO:emails_agent:agent-welcome.html.twig' => 1,

					// Server / Test email can be sent to anyone
					'DeskPRO:emails_agent:test-email.html.twig' => 1,
				);

				if (strpos($this->template, ':emails_agent:') !== false && !isset($skip_check[$this->template])) {
					$agent = App::getContainer()->getAgentData()->getByEmail($this->set_to['email']);
					if (!$agent) {

						// Not an agent
						// - Generate error log warning
						// - Send in error report to us
						// - Blank out email. We need to send a blank email because
						// there is no way to "stop" at this late stage (it's too "late" by the time this code gets run)
						// and if we were to throw an exception, it would cause rollbacks to happen.
						// - TO DO: Can implement custom swiftmailer classes to allow cancelling of messages so the blank
						// email isn't sent.

						$e = new \InvalidArgumentException("Agent email being sent to a non-agent. Template: {$this->template}, Person: {$this->template_vars['to_contact']}");
						KernelErrorHandler::logException($e, true);

						$this->template        = null;
						$this->template_vars   = null;
						$this->template_engine = null;
						$this->set_to_person   = null;
						$this->attach_blobs    = null;
						$this->embed_only      = true;
						$this->setBody('');
						$this->setSubject('');
						$this->getHeaders()->addTextHeader('X-DeskPRO-Error', "Agent email being sent to a non-agent. Template: {$this->template}, Person: {$this->template_vars['to_contact']}");
						return;
					}
				}
			}

			if (!$this->set_to_person && $this->template_vars['to_email']) {
				$this->set_to_person = App::getOrm()->getRepository('DeskPRO:Person')->findOneByEmail($this->template_vars['to_email']);
			}

			$this->template_vars['to_person'] = $this->set_to_person;
			$this->template_vars['person_timezone'] = $this->set_to_person ? $this->set_to_person->getDateTimezone() : App::getContainer()->getSettingsHandler()->getDefaultTimezone();

			$this->template_vars['site_url']    = App::getSetting('core.site_url');
			$this->template_vars['site_name']   = App::getSetting('core.site_name');
			$this->template_vars['deskpro_url'] = App::getSetting('core.deskpro_url');

			$content = $this->template_engine->render($this->template, $this->template_vars);
			if (strpos($content, '___DP___SUBJECT___SEP___') !== false) {
				list ($subject, $body) = explode('___DP___SUBJECT___SEP___', $content, 2);

				// Try to clean up subject from whitespace
				$subject = \Orb\Util\Strings::removeEmptyLines($subject);
				$subject = \Orb\Util\Strings::trimLines($subject);
				$subject = str_replace(array("\r\n", "\n"), ' ', $subject);
				$subject = trim($subject);

				// Subjects from the template will be escaped due to auto-escaping in twig
				$subject = html_entity_decode($subject, \ENT_QUOTES, 'UTF-8');

				$body = trim($body);
			} else {
				$subject = '';
				$body = $content;
			}

			if ($subject) {
				$this->setSubject($subject);
			}

			if (isset($this->template_vars['tracking_object']) && dp_get_config('enable_smtp_tracking')) {
				$obj = $this->template_vars['tracking_object'];
				$tos = array();

				if ($this->getTo()) {
					foreach ($this->getTo() as $addr => $x) {
						$tos[] = $addr;
					}
				}
				if ($this->getCc()) {
					foreach ($this->getCc() as $addr => $x) {
						$tos[] = $addr;
					}
				}
				if ($this->getBcc()) {
					foreach ($this->getBcc() as $addr => $x) {
						$tos[] = $addr;
					}
				}

				$from = $this->getFrom();
				$from = Arrays::getFirstKey($from);

				if ($tos && $from) {
					if ($obj instanceof Entity\TicketMessage) {
						$this->track_code = Entity\SendmailLog::insertTicketMessageLog($obj, $tos, $subject, $from);
					} elseif ($obj instanceof Entity\Ticket) {
						$this->track_code = Entity\SendmailLog::insertTicketLog($obj, $tos, $subject, $from);
					} else {
						$this->track_code = Entity\SendmailLog::insertLog($tos, $subject, $from);
					}
				}
			}

			$body = $this->replaceEmbeds($body);
			$this->setBody($body, 'text/html');

			try {
				try {
					$plaintext = Html2Text::convertHtml($body);
				} catch (\Exception $e) {
					$plaintext = null;
				}
				if ($body) {
					$this->addPart($plaintext, 'text/plain');
				}
			} catch (\Exception $e) {}
		}

		// These need to be unset so the message can be properly serialized
		// if it needs to be inserted as a queued message
		$this->template        = null;
		$this->template_vars   = null;
		$this->template_engine = null;
		$this->set_to_person   = null;

		// Attach blobs
		foreach ($this->attach_blobs as $src => $blob) {
			if (isset($this->embed_only[$src])) {
				continue;
			}

			$type = $blob->content_type;

			// Bug in attaching message/rfc822 messages
			// results in invalid emails.
			// See open bug: https://github.com/swiftmailer/swiftmailer/issues/258
			if ($type == 'message/rfc822') {
				$type = 'application/octet-stream';
			}

			$this->attach(\Swift_Attachment::newInstance(
				App::getContainer()->getBlobStorage()->copyBlobRecordToString($blob),
				$blob->filename,
				$type
			));
		}
		$this->attach_blobs = null;
		$this->embed_only = true;

		$this->getHeaders()->addTextHeader('X-DeskPRO-Build', defined('DP_BUILD_TIME') ? DP_BUILD_TIME : 1);

		if ($this->track_code) {
			$data = array(
				'unique_args' => array(
					'dp_code' => $this->track_code
				)
			);
			if (defined('DPC_SITE_ID')) {
				$data['unique_args']['dpc_site_id'] = DPC_SITE_ID;
			}
			$this->getHeaders()->addTextHeader('X-SMTPAPI', json_encode($data));
		}
	}

	/**
	 * Replaces embeddable attachments with their embedded version.
	 *
	 * @param string $body
	 *
	 * @return string
	 */
	public function replaceEmbeds($body)
	{
		$self = $this;

		$embed_map = array();
		foreach ($this->attach_blobs AS $src => $blob) {
			if (is_int($src)) {
				continue;
			}

			$regex = '#(<img[^>]+src=")' . preg_quote($src, '#') . '(\?s=\d+)?("[^>]*>)#i';
			$body = preg_replace_callback($regex, function($match) use($self, &$embed_map, $src, $blob) {
				if (!isset($embed_map[$src])) {
					// in case the src is referenced twice
					$embed_map[$src] = $self->embed(\Swift_Image::newInstance(
						App::getContainer()->getBlobStorage()->copyBlobRecordToString($blob),
						$blob->filename,
						$blob->content_type
					));
				}

				return $match[1] . $embed_map[$src] . $match[3];
			}, $body);
		}

		foreach ($embed_map AS $src => $null) {
			// already embedded, don't need to attach again
			unset($self->attach_blobs[$src]);
		}

		return $body;
	}


	/**
	 * Attach a blob to the message. If you want it to be embedded,
	 * pass the src value of an <img> tag that will hold it. It is recommended
	 * that the embed image src is a URL, as it will be left if the embed
	 * cannot happen for any reason.
	 *
	 * @param Blob $blob
	 * @param string|null $embedImageSrc If non-null/integer, will search the body for this image src to embed
	 * @param bool $includeEmbeddedOnly If true, the file will only be attached if embedded
	 */
	public function attachBlob(Blob $blob, $embedImageSrc = null, $includeEmbeddedOnly = false)
	{
		if ($embedImageSrc && !ctype_digit($embedImageSrc)) {
			$this->attach_blobs[$embedImageSrc] = $blob;
			if ($includeEmbeddedOnly) {
				$this->embed_only[$embedImageSrc] = true;
			}
		} else {
			$this->attach_blobs[] = $blob;
		}
	}

	/**
	 * Brings in a blob that will be embedded
	 *
	 * @param string $src The image src attribute that will be replaced
	 * @param \Application\DeskPRO\Entity\Blob $blob
	 */
	public function embedImage($src, Blob $blob)
	{
		$this->embed_images[$src] = $blob;
	}


	/**
	 * @param \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface $templating
	 */
	public function setTemplateEngine(EngineInterface $template_engine)
	{
		$this->template_engine = $template_engine;
	}


	/**
	 * Set the template we'll use to fetch the subject and body from
	 *
	 * @param $name
	 * @param array $vars
	 */
	public function setTemplate($name, array $vars = array())
	{
		$this->template = $name;
		$this->template_vars = $vars;
	}


	/**
	 * A shortcut to set to and name
	 *
	 * @param Person $person
	 */
	public function setToPerson(Person $person)
	{
		$this->setTo($person->getPrimaryEmailAddress(), $person->getDisplayName());
		$this->set_to_person = $person;
	}


	/**
	 * @param array $addresses
	 * @param null $name
	 * @return \Swift_Mime_SimpleMessage|void
	 */
	public function setTo($addresses, $name = null)
	{
		if (is_array($addresses)) {
			reset($addresses);
			$this->set_to = array(
				'name'  => \Orb\Util\Arrays::getFirstKey($addresses),
				'email' =>\Orb\Util\Arrays::getFirstItem($addresses),
			);
		} else {
			$this->set_to = array(
				'name'  => $name,
				'email' => $addresses,
			);
		}

		return parent::setTo($addresses, $name);
	}


	/**
	 * @static
	 * @param null $subject
	 * @param null $body
	 * @param null $contentType
	 * @param null $charset
	 * @return \Application\DeskPRO\Mail\Message
	 */
	public static function newInstance($subject = null, $body = null, $contentType = null, $charset = null)
	{
		return new static($subject, $body, $contentType, $charset);
	}


	/**
	 * Get is retrying flag. A message is set as retrying when it is being sent
	 * after being queued (either because it was deemed unimportant, or because of an error).
	 *
	 * @return bool
	 */
	public function getIsRetrying()
	{
		return $this->is_retrying;
	}


	/**
	 * Sets is retrying flag
	 */
	public function setIsRetrying()
	{
		$this->is_retrying = true;
	}


	/**
	 * @param string $track_code
	 */
	public function setTrackCode($track_code)
	{
		$this->track_code = $this->track_code;
	}


	/**
	 * @return string
	 */
	public function getTrackCode()
	{
		return $this->track_code;
	}

	/**
	 * Add a log message. These messages are meant to be for the current invocation (eg to track sendmail errors).
	 *
	 * @param string $msg
	 */
	public function addLogMessage($msg)
	{
		$this->log_messages[] = $msg;
	}


	/**
	 * @param string[] $msgs
	 */
	public function addLogMessages(array $msgs)
	{
		$this->log_messages = array_merge($this->log_messages, $msgs);
	}


	/**
	 * @param bool $as_string
	 * @return string[]|string
	 */
	public function getLogMessages($as_string = true)
	{
		if ($as_string) {
			return implode("\n", $this->log_messages);
		}

		return $this->log_messages;
	}

	/**
	 * @return void
	 */
	public function clearLogMessages()
	{
		$this->log_messages = array();
	}
}