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
 * @subpackage AdminBundle
 */

namespace Application\AdminBundle\FormModel;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\EmailGateway;

use Orb\Util\Arrays;

class EditEmailGateway
{
	public $connection_type = 'pop3';
	public $define_transport = false;
	public $pop3_options = array();
	public $gmail_options = array();

	public $gateway_type = 'tickets';
	public $is_enabled = true;
	public $keep_read = false;
	public $address = '';

	/**
	 * @var \Application\DeskPRO\Entity\EmailGateway
	 */
	protected $gateway;

	protected $new_addresses;
	protected $remove_addresses;
	protected $set_default_address = null;

	protected $persist_objs = array();
	protected $remove_objs = array();

	protected $has_applied = false;

	public function __construct(EmailGateway $gateway)
	{
		$this->gateway = $gateway;

		$this->connection_type = $gateway->connection_type;
		if (!$this->connection_type || $this->connection_type == 'pop3') {
			$this->pop3_options = $gateway->connection_options;

			if (!isset($this->pop3_options['port'])) {
				$this->pop3_options['port'] = '110';
			}
		} elseif ($this->connection_type == 'gmail') {
			$this->gmail_options = $gateway->connection_options;
		}

		$this->gateway_type = $gateway->gateway_type;
		$this->is_enabled = $gateway->is_enabled;
		$this->keep_read = $gateway->keep_read;

		if ($gateway->linked_transport) {
			$this->define_transport = true;
			if ($this->connection_type == 'gmail' && $gateway->linked_transport->transport_type == 'gmail') {
				if ($this->gmail_options['username'] == $gateway->linked_transport->transport_options['username'] && $this->gmail_options['password'] == $gateway->linked_transport->transport_options['password']) {
					$this->define_transport = false;
				}
			}
		}

		foreach ($gateway->addresses as $adr) {
			$this->address = $adr->match_pattern;
			break;
		}
	}

	public function setNewAddresses(array $new_addresses)
	{
		$this->new_addresses = $new_addresses;
	}

	public function setRemoveAddressIds(array $remove_addresses)
	{
		$this->remove_addresses = $remove_addresses;
	}

	public function apply()
	{
		if ($this->has_applied) {
			return;
		}

		$this->has_applied = true;

		$this->gateway->connection_type = $this->connection_type;
		if ($this->connection_type == 'pop3') {
			$this->gateway->connection_options = $this->pop3_options;
			$this->gateway->title = "{$this->pop3_options['host']} : {$this->pop3_options['username']}";
		} elseif ($this->connection_type == 'gmail') {
			$this->gateway->connection_options = $this->gmail_options;
			$this->gateway->title = "Gmail / Google Apps : {$this->gmail_options['username']}";
		}

		$this->gateway->gateway_type = $this->gateway_type;
		$this->gateway->is_enabled = $this->is_enabled;
		$this->gateway->keep_read = $this->keep_read;
	}


	public function save()
	{
		$this->apply();

		if ($this->remove_addresses) {
			foreach ($this->remove_addresses as $address_id) {
				if (!isset($this->gateway->addresses)) {
					continue;
				}

				$this->remove_objs[] = $this->gateway->addresses[$address_id];
				$this->gateway->addresses->remove($address_id);
			}
		}

		if ($this->new_addresses) {
			foreach ($this->new_addresses as $address) {
				$address->gateway = $this->gateway;
				$this->gateway->addresses->add($address);
			}
		}

		App::getOrm()->persist($this->gateway);
		App::getOrm()->flush();

		foreach ($this->remove_objs as $obj) {
			App::getOrm()->remove($obj);
		}

		foreach ($this->new_addresses as $address) {
			$address->gateway = $this->gateway;
			$this->gateway->addresses->add($address);

			App::getOrm()->persist($address);
		}

		App::getOrm()->flush();
	}
}
