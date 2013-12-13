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
 * @subpackage EmailGateway
 */

namespace Application\DeskPRO\EmailGateway;

use Symfony\Component\EventDispatcher\Event;

class GatewayEvent extends Event
{
	/**
	 * @var \Application\DeskPRO\EmailGateway\AbstractGatewayProcessor
	 */
	protected $gateway;

	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct(AbstractGatewayProcessor $gateway, array $data = array())
	{
		$this->gateway = $gateway;
		$this->data = $data;
	}


	/**
	 * @var \Application\DeskPRO\EmailGateway\AbstractGatewayProcessor
	 */
	public function getGateway()
	{
		return $this->gateway;
	}


	/**
	 * @param  $name
	 * @return array
	 */
	public function __get($name)
	{
		return $this->data[$name];
	}


	/**
	 * @param  $name
	 * @param  $value
	 * @return void
	 */
	public function __set($name, $value)
	{
		$this->data[$name] = $value;
	}


	/**
	 * @param  $name
	 * @return bool
	 */
	public function __isset($name)
	{
		return isset($this->data[$name]);
	}


	/**
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}
}
