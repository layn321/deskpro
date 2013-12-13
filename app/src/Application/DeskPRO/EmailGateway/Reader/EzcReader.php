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

namespace Application\DeskPRO\EmailGateway\Reader;

use Application\DeskPRO\App;
use Application\DeskPRO\EmailGateway\Reader\Item;

use Orb\Data\ContentTypes;
use Orb\Util\Strings;
use Orb\Util\Arrays;

/**
 * The ezcParser uses the ezcMailParser class from ezComponents
 * to parse emails.
 *
 * @see http://ezcomponents.org/docs/api/trunk/introduction_Mail.html#mail-retrieval-and-parsing
 */
class EzcReader extends AbstractReader
{
	/**
	 * @var \ezcMailParser|null
	 */
	protected $parser = null;

	/**
	 * @var \ezcMail
	 */
	protected $mail = null;

	function __construct()
	{
		$opt = new \ezcMailParserOptions();

		$this->parser = new \ezcMailParser($opt);
		\ezcMailParser::setTmpDir(dp_get_tmp_dir() . DIRECTORY_SEPARATOR);

		// Dont have ezc try and convert charsets, we'll handle that ourselves tyvm
		static $has_set_convert = false;
		if (!$has_set_convert) {
			$has_set_convert = true;
			\ezcMailCharsetConverter::setConvertMethod(function($text) {
				return $text;
			});
		}
	}

	public function _kill()
	{
		parent::_kill();

		$this->parser = null;
		$this->mail   = null;
	}

	protected function _setRawSource($source)
	{
		$set = new \ezcMailVariableSet($source);
		$this->mail = $this->parser->parseMail($set);

		if (!$this->mail || !isset($this->mail[0])) {
			throw new \InvalidArgumentException("Bad mail source, could not decode");
		}

		$this->mail = $this->mail[0];
	}

	protected function _getHeader($name)
	{
		$name = strtolower($name);
		$header = new Item\Header();
		$header->name = $name;

		$parts = (array)$this->mail->getHeader($name, true);
		if ($parts) {
			foreach ($parts as $p) {
				$header->header_parts[] = \ezcMailTools::mimeDecode($p, 'utf-8');
			}
		}

		return $header;
	}

	protected function _getCcAddresses()
	{
		$emails = array();

		foreach ($this->mail->cc as $cc) {
			$charset = $cc->charset;
			if (!$charset) $charset = 'us-ascii';

			$email = new Item\EmailAddress();
			$email->name = $cc->name;
			$email->name_utf8 = Strings::convertToUtf8($cc->name, $charset);
			$email->email = $cc->email;
			$email->original_charset = $charset;

			$emails[] = $email;
		}

		return $emails;
	}

	protected function _getToAddresses()
	{
		$emails = array();

		foreach ($this->mail->to as $to) {
			$charset = $to->charset;
			if (!$charset) $charset = 'us-ascii';

			$email = new Item\EmailAddress();
			$email->name = $to->name;
			$email->name_utf8 = Strings::convertToUtf8($to->name, $charset);
			$email->email = $to->email;
			$email->original_charset = $charset;

			$emails[] = $email;
		}

		return $emails;
	}

	protected function _getFromAddress()
	{
		if (!$this->mail->from || !$this->mail->from->email) {
			$email = new Item\EmailAddress();
			$email->name = '';
			$email->name_utf8 = '';
			$email->email = '';
			return $email;
		}

		$charset = $this->mail->from->charset;
		if (!$charset) $charset = 'us-ascii';

		$email = new Item\EmailAddress();
		$email->name = $this->mail->from->name;
		$email->name_utf8 = Strings::convertToUtf8($this->mail->from->name, $charset);
		$email->email = $this->mail->from->email;

		return $email;
	}

	protected function _getSubject()
	{
		if (!$this->mail->subject) {
			$subject = new Item\Subject();
			$subject->subject = '';
			$subject->subject_utf8 = '';
			$subject->original_charset = 'UTF-8';
			return $subject;
		}

		$charset = $this->mail->subjectCharset;
		if (!$charset) $charset = 'us-ascii';

		$subject = new Item\Subject();
		$subject->subject = $this->mail->subject;
		$subject->subject_utf8 = Strings::convertToUtf8($this->mail->subject, $charset);
		$subject->original_charset = $charset;

		return $subject;
	}

	protected function _getOriginalSubject()
	{
		$header = $this->getHeader('Thread-Topic');
		if (!$header || empty($header->header_parts)) {
			return null;
		}

		$subject = new Item\Subject();
		$subject->subject = $header->getHeader();
		$subject->subject_utf8 = $subject->subject;
		$subject->original_charset = 'UTF-8';

		return $subject;
	}

	protected function _getAttachments()
	{
		$attachments = array();

		foreach ($this->mail->fetchParts() as $part) {
			if (
				$part instanceof \ezcMailFile
				|| ($part->contentDisposition && $part->contentDisposition->disposition == 'attachment')
				|| ($part instanceof \ezcMailText && $part->subType == 'calendar')
				|| ($part instanceof \ezcMailRfc822Digest)
			) {

				$attach = new Item\Attachment();

				if ($part instanceof \ezcMailText) {
					$attach->tmp_file = tempnam(dp_get_tmp_dir(), 'dpm');
					file_put_contents($attach->tmp_file, $part->text);

					if (isset($part->contentDisposition) && isset($part->contentDisposition->displayFileName)) {
						try {
							$attach->file_name = basename($part->contentDisposition->displayFileName);
							$attach->mime_type = \Orb\Data\ContentTypes::getContentTypeFromFilename($part->contentDisposition->displayFileName);
						} catch (\Exception $e) {}
					}

					if (!$attach->file_name) {
						if ($part instanceof \ezcMailText && $part->subType == 'calendar') {
							$attach->file_name = 'icalendar.ics';
							$attach->mime_type = 'text/calendar';
						} else {
							$attach->file_name = 'file.txt';
							$attach->mime_type = 'text/plain';
						}
					}

					if (!$attach->mime_type) {
						$attach->mime_type = 'application/octet-stream';
					}

				} elseif ($part instanceof \ezcMailRfc822Digest) {
					$attach->tmp_file = tempnam(dp_get_tmp_dir(), 'eml');
					file_put_contents($attach->tmp_file, $part->generateBody());

					$attach->tmp_file = $attach->tmp_file;
					$attach->file_name = 'email.eml';
					$attach->mime_type = 'message/rfc822';
				} else {
					$attach->tmp_file   = $part->fileName;

					if (isset($part->contentDisposition) && isset($part->contentDisposition->displayFileName)) {
						try {
							$attach->file_name = basename($part->contentDisposition->displayFileName);
							$attach->mime_type = \Orb\Data\ContentTypes::getContentTypeFromFilename($part->contentDisposition->displayFileName);
						} catch (\Exception $e) {}
					} elseif (!empty($part->fileName)) {
						try {
							$attach->file_name = basename($part->fileName);
							$attach->mime_type = \Orb\Data\ContentTypes::getContentTypeFromFilename($part->fileName);
						} catch (\Exception $e) {}
					}

					if (!$attach->file_name) {
						$attach->file_name = 'file.txt';
						$attach->mime_type = 'plain/text';
					}

					if (!$attach->mime_type) {
						if (isset($part->contentType) && isset($part->mimeType) && $part->contentType && $part->mimeType) {
							$attach->mime_type = "{$part->contentType}/{$part->mimeType}";
						} else {
							$attach->mime_type = 'application/octet-stream';
						}
					}

					if (!Strings::getExtension($attach->file_name)) {
						$ext = ContentTypes::findExtensionForContentType($attach->mime_type);
						if ($ext) {
							$attach->file_name .= ".$ext";
						}
					}
				}

				$attach->content_id = $part->getHeader('Content-ID');
				if ($attach->content_id) {
					// Content-ID is enclosed in brackets, remove those
					$attach->content_id = preg_replace('#^<(.*?)>$#', '$1', $attach->content_id);
				}

				$attachments[] = $attach;
			}
		}

		$set_attachments = array();

		foreach ($attachments as $attach) {
			if ($attach->file_name != 'winmail.dat') {
				$set_attachments[] = $attach;
				continue;
			}
		}

		return $attachments;
	}

	protected function _getBodyHtml()
	{
		foreach ($this->mail->fetchParts(array('ezcMailText')) as $part) {
			if ($part->subType == 'html') {
				$originalCharset = $part->originalCharset;
				if (!$originalCharset) $originalCharset = 'us-ascii';

				$body = new Item\BodyHtml();
				$body->body = Strings::standardEol($part->text);
				$body->body_utf8 = Strings::convertToUtf8(Strings::standardEol($part->text), $originalCharset);
				$body->original_charset = $part->originalCharset;

				return $body;
			}
		}

		// Default to a blank body
		$body = new Item\BodyHtml();
		$body->body = '';
		$body->body_utf8 = '';
		$body->original_charset = 'UTF-8';
		return $body;
	}

	protected function _getBodyText()
	{
		foreach ($this->mail->fetchParts(array('ezcMailText')) as $part) {
			if ($part->subType == 'plain') {
				$originalCharset = $part->originalCharset;
				if (!$originalCharset) $originalCharset = 'us-ascii';

				$body = new Item\BodyText();
				$body->body = $part->text;
				$body->body_utf8 = Strings::convertToUtf8($part->text, $originalCharset);
				$body->original_charset = $originalCharset;

				return $body;
			}
		}

		// Default to a blank body
		$body = new Item\BodyText();
		$body->body = '';
		$body->body_utf8 = '';
		$body->original_charset = 'UTF-8';
		return $body;
	}
}
