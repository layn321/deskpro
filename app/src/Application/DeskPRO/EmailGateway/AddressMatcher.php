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
use Application\DeskPRO\DBAL\Connection;
use Doctrine\ORM\EntityManager;

use Application\DeskPRO\EmailGateway\Reader\AbstractReader;

use Application\DeskPRO\Entity\EmailGatewayAddress;
use Application\DeskPRO\Entity\EmailGateway;

use Orb\Util\Strings;

/**
 * This looks at an email address ('to') and tries to match it against an EmailGatewayAddress.
 * If no address matches, then the default address for a gateway is returned.
 *
 */
class AddressMatcher
{
	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $db;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var array
	 */
	protected $patterns = null;

	/**
	 * @var array
	 */
	protected $helpdesk_addresses = array();


	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
		$this->db = $em->getConnection();

		$aliases = App::getSetting('core.helpdesk_emails');
		$aliases = explode(',', $aliases);
		foreach ($aliases as $a) {
			$this->helpdesk_addresses[] = strtolower($a);
		}

		$this->helpdesk_addresses = \Orb\Util\Arrays::removeFalsey($this->helpdesk_addresses);
	}


	/**
	 * Check an email address to see if its a helpdesk managed address.
	 * That is, does it match any helpdesk account or is it a know helpdesk address?
	 *
	 * @param string $addr
	 * @return bool
	 */
	public function isManagedAddress($addr)
	{
		if ($this->isHelpdeskAddress($addr)) {
			return true;
		}

		if ($this->getMatchingAddress($addr)) {
			return true;
		}

		return false;
	}


	/**
	 * Checks if an address is a known helpdesk address
	 */
	public function isHelpdeskAddress($addr)
	{
		if (!$this->helpdesk_addresses) {
			return false;
		}

		$addr = strtolower($addr);

		foreach ($this->helpdesk_addresses as $hd_addr) {
			if (strpos($hd_addr, '@') === false) {
				if (strpos($addr, '@' . $hd_addr) !== false) {
					return true;
				}
			} else {
				if ($hd_addr == $addr) {
					return true;
				}
			}
		}

		return false;
	}


	/**
	 * Load all the email address patterns from the databaase
	 *
	 * @return array
	 */
	public function getPatterns()
	{
		if ($this->patterns !== null) return $this->patterns;

		$patterns = $this->db->fetchAll("
			SELECT a.id, a.email_gateway_id, a.match_type, a.match_pattern
			FROM email_gateway_addresses a
			LEFT JOIN email_gateways g ON (g.id = a.email_gateway_id)
			WHERE g.is_enabled AND g.gateway_type = 'tickets'
			ORDER BY run_order ASC
		");

		// Order them into exact, domain, pattern
		$this->patterns = array();
		foreach ($patterns as $p) if ($p['match_type'] == 'exact')  $this->patterns[] = $p;
		foreach ($patterns as $p) if ($p['match_type'] == 'domain') $this->patterns[] = $p;
		foreach ($patterns as $p) if ($p['match_type'] == 'regex')  $this->patterns[] = $p;

		return $this->patterns;
	}


	/**
	 * Given an email address, run through the known email address to try and find its gateway.
	 * If no address matches then null is returned unless $gateway was specified, in which case it's default address is returned.
	 *
	 * @param string $address
	 * @param \Application\DeskPRO\Entity\EmailGateway $gateway  The gateway the message was found in. The default address will be used if none matching.
	 * @return \Application\DeskPRO\Entity\EmailGatewayAddress
	 */
	public function getMatchingAddress($address, EmailGateway $gateway = null, &$match_address_id = null)
	{
		$this->getPatterns();

		$address = Strings::utf8_strtolower($address);

		if (isset($this->aliases[$address])) {
			return $this->aliases[$address];
		}

		$match_address_id = null;
		foreach ($this->patterns as $pattern) {
			switch ($pattern['match_type']) {
				case 'exact':
					if ($address == Strings::utf8_strtolower($pattern['match_pattern'])) {
						$match_address_id = $pattern['id'];
						break 2;
					}
					break;

				case 'domain':
					list (, $domain) = explode('@', $address);
					if ($domain == Strings::utf8_strtolower($pattern['match_pattern'])) {
						$match_address_id = $pattern['id'];
						break 2;
					}
					break;

				case 'regex':
					$regex = trim($pattern['match_pattern'], '/');
					if (preg_match('/' . $regex . '/i', $address)) {
						$match_address_id = $pattern['id'];
						break 2;
					}
			}
		}

		if (!$match_address_id) {
			return null;
		}

		$matched_address = $this->em->find('DeskPRO:EmailGatewayAddress', $match_address_id);
		return $matched_address;
	}


	/**
	 * given a mail reader, search through To and Cc address to find a matching helpdesk address.
	 *
	 * @param Reader\AbstractReader $reader
	 * @param \Application\DeskPRO\Entity\EmailGateway $gateway
	 * @return \Application\DeskPRO\Entity\EmailGatewayAddress
	 */
	public function getMatchingAddressFromReader(AbstractReader $reader, Emailgateway $gateway = null)
	{
		foreach ($reader->getToAddresses() as $email) {
			$address = $email->getEmail();

			$matched_address = $this->getMatchingAddress($address, $gateway);
			if ($matched_address) {
				return $matched_address;
			}
		}

		foreach ($reader->getCcAddresses() as $email) {
			$address = $email->getEmail();

			$matched_address = $this->getMatchingAddress($address, $gateway);
			if ($matched_address) {
				return $matched_address;
			}
		}

		if ($gateway && $gateway->getPrimaryEmailAddress()) {
			return $gateway->getPrimaryEmailAddress(true);
		}

		return null;
	}


	/**
	 * Get a default ticket account for cases when none can be located.
	 * This only happens if the original account was deleted, or if a ticket never
	 * had a proper one set (eg bad import).
	 *
	 * Right now this simply fetches the first account.
	 */
	public function getDefaultTicketAccountFrom()
	{
		$this->getPatterns();

		foreach ($this->patterns as $p) {
			if ($p['match_type'] == 'exact') {
				return $p['match_pattern'];
			}
		}

		// Cant find one still, maybe no accounts enabled anymore
		return null;
	}


	/**
	 * Try to match a From email address against a gateway account to see if we want to use
	 * an outgoing alias. E.g., support@someaccount.deskpro.com to send from support@mydomain.com.
	 *
	 * @param string $from_email
	 * @return string
	 */
	public function getOutgoingEmailAliasAddress($from_email)
	{
		if ($gateway_address = $this->getMatchingAddress($from_email)) {
			$gateway = $gateway_address->gateway;
			if ($gateway && $gateway->linked_transport) {
				$new_address = $gateway->getPrimaryEmailAddress();
				if ($gateway->getAliasEmailAddress()) {
					$new_address = $gateway->getAliasEmailAddress();
				}

				return $new_address;
			}
		}

		return $from_email;
	}
}
