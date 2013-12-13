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
use Application\DeskPRO\Entity\EmailSource;
use Application\DeskPRO\EmailGateway\AbstractGatewayProcessor;
use Application\DeskPRO\EmailGateway\Reader\AbstractReader;

class PreProcessor extends AbstractGatewayProcessor
{
	protected $error = null;
	protected $source_info = null;

	public function run()
	{
		#------------------------------
		# Empty From
		#------------------------------

		$from = $this->reader->getFromAddress()->getEmail();
		if (!$from) {
			$this->error = EmailSource::ERR_FROM_MISSING;
			return;
		}

		#------------------------------
		# Invalid From
		#------------------------------

		$validator = new \Orb\Validator\StringEmail();

		if (!$validator->isValid($from)) {
			$this->error = EmailSource::ERR_FROM_INVALID;
			$this->source_info = array();
			$this->source_info[] = "Read from address: " . $from;
			$this->source_info[] = "Errors:\n\n" . $validator->getErrorsDebug();
			return;
		}

		#------------------------------
		# From is a know gateway address
		#------------------------------

		$gateway_matcher = App::getSystemService('gateway_address_matcher');
		$match_address_id = null;
		if ($found_gateway = $gateway_matcher->getMatchingAddress($from, null, $match_address_id)) {
			$this->error = EmailSource::ERR_FROM_GATEWAY;
			$this->source_info[] = "Read from address: " . $from;
			$this->source_info[] = "Matched gateway: " . $found_gateway->id;
			$this->source_info[] = "Matched gateway pattern: " . $match_address_id;
			return;
		}

		if ($gateway_matcher->isManagedAddress($from)) {
			$this->error = EmailSource::ERR_FROM_GATEWAY;
			$this->source_info[] = "Read from address: " . $from;
			$this->source_info[] = "Is a registered helpdesk address";
			return;
		}

		#------------------------------
		# From is a banned address
		#------------------------------

		$match = null;
		if (App::getOrm()->getRepository('DeskPRO:BanEmail')->isEmailBanned($from, $match)) {
			$this->error = EmailSource::ERR_FROM_BANNED;
			$this->source_info[] = "Read from address: " . $from;
			$this->source_info[] = "Matched banned email: " . $match;
			return;
		}

		#------------------------------
		# Check for empty message
		#------------------------------

		$subj = trim($this->reader->getSubject()->getSubject());
		$message = trim($this->reader->getBodyHtml()->getBody());
		$message2 = trim($this->reader->getBodyText()->getBody());
		$attach = $this->reader->getAttachments();

		if (!$subj && !$message && !$message2 && !$attach) {
			$this->error = EmailSource::ERR_EMPTY;
			return;
		}

		#------------------------------
		# Check if date is older than start_date_limit
		# on the account
		#------------------------------

		if ($this->gateway->start_date_limit && $email_date = $this->reader->getDate() && App::getSetting('core_email.enable_date_limit_rejection')) {
			if ($email_date < $this->gateway->start_date_limit) {
				$this->error = EmailSource::ERR_DATE_LIMIT;
				$this->source_info[] = "Gateway date limit: " . $this->gateway->start_date_limit->format(\DateTime::RFC2822);
				$this->source_info[] = "Message date: " . $email_date->format(\DateTime::RFC2822);
				return;
			}
		}

		unset($subj, $message, $message2, $attach);
	}

	public function isValid()
	{
		return $this->error === null;
	}

	public function getErrorCode()
	{
		return $this->error;
	}

	public function getSourceInfo()
	{
		if (!$this->source_info) {
			return null;
		}

		if (!is_array($this->source_info)) {
			$this->source_info = array($this->source_info);
		}
		return $this->source_info;
	}
}