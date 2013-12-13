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
 * @subpackage Usersource
 */

namespace Application\DeskPRO\Usersource\Adapter;

use Application\DeskPRO\Entity\Usersource;
use Symfony\Component\Templating\EngineInterface;

use Orb\Util\CapabilityInformerInterface;
use Orb\Auth\Identity;
use Orb\Util\Util;

abstract class AbstractAdapter implements CapabilityInformerInterface
{
	/**
	 * @var \Application\DeskPRO\Entity\Usersource
	 */
	protected $usersource;

	/**
	 * @var \Orb\Auth\Adapter\AdapterInterface
	 */
	protected $_auth_adapter;

	public function __construct(Usersource $usersource)
	{
		$this->usersource = $usersource;
		$this->init();
	}

	protected function init()
	{

	}


	/**
	 * Find a user identity just by an email address.
	 *
	 * @param string $input
	 * @return \Orb\Auth\Identity|null
	 */
	public function findIdentityByInput($input)
	{
		return null;
	}


	/**
	 * Given an identity returned from an auth adapter, get the mapped fields that we can apply
	 * to a Person record. For example, email addresses or names.
	 *
	 * @param \Orb\Auth\Identity $identity
	 * @return array
	 */
	public function getFieldsFromIdentity(Identity $identity)
	{
		return array();
	}


	/**
	 * @param array $info
	 * @return string
	 */
	public function getDisplayName(array $info)
	{
		$order = array('display_name', 'username', 'name', 'email');
		foreach ($order as $k) {
			if (!empty($info[$k])) {
				return $info[$k];
			}
		}

		return '';
	}


	/**
	 * @param array $info
	 * @return string
	 */
	public function getDisplayLink(array $info)
	{
		return '';
	}


	/**
	 * Get the adapter.
	 *
	 * @return \Orb\Auth\Adapter\AdapterInterface
	 */
	public function getAuthAdapter()
	{
		if ($this->_auth_adapter !== null) {
			return $this->_auth_adapter;
		}

		$this->_auth_adapter = $this->_createAuthAdapterObject();

		return $this->_auth_adapter;
	}


	public function applyResultToUser()
	{

	}


	/**
	 * Create a new instance of the adapter interface, using the usersource info
	 * for options etc.
	 *
	 * @return \Orb\Auth\Adapter\AdapterInterface
	 */
	abstract protected function _createAuthAdapterObject();

	public function getTypename()
	{
		return strtolower(Util::getBaseClassname($this));
	}


	/**
	 * @param  mixed $capability
	 * @return bool
	 */
	public function isCapable($capability)
	{
		return in_array($capability, $this->getCapabilities());
	}
}
