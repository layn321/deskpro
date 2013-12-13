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

namespace Application\DeskPRO\Usersource\Adapter;

use Orb\Auth\Identity;

use Orb\Util\Arrays;

class Ldap extends AbstractAdapter
{
	public function getFieldsFromIdentity(Identity $identity)
	{
		$info = $identity->getRawData();
		return array(
			'name'             => isset($info['name']) ? $info['name'] : '',
			'first_name'       => isset($info['first_name']) ? $info['first_name'] : '',
			'last_name'        => isset($info['last_name']) ? $info['last_name'] : '',
			'email'            => isset($info['email_address']) ? $info['email_address'] : '',
			'email_confirmed'  => true,
			'picture_data'     => isset($info['picture_data']) ? $info['picture_data'] : null,
		);
	}


	/**
	 * @return \Orb\Auth\Adapter\ActiveDirectory
	 */
	protected function _createAuthAdapterObject()
	{
		$options = $this->usersource->options;
		if (!isset($options['accountFilterFormat'])) {
			$options['accountFilterFormat'] = '(|(dn=%s)(mail=%s)(uid=%s))';
		}
		return new \Orb\Auth\Adapter\LdapRaw($this->usersource->options);
	}


	/**
	 * Find a user identity just by an email address.
	 *
	 * @param string $id_input Username or email address
	 * @return \Orb\Auth\Identity|null
	 */
	public function findIdentityByInput($id_input)
	{
		$usersource = clone $this->usersource;
		$usersource->setOption('bindRequiresDn', true);

		/** @var \Orb\Auth\Adapter\LdapRaw $adapter */
		$adapter = $usersource->getAdapter()->getAuthAdapter();

		if ($adapter->getLogger()) $adapter->getLogger()->logDebug("findIdentityByInput: $id_input");

		$adapter->setFormData(array(
			'username' => $id_input,
			'password' => '',
		));
		$rec = null;
		$rec_arr = $adapter->findRecordViaEmail($id_input);

		if (!$rec_arr || !isset($rec_arr['dn'])) {
			$rec_arr = $adapter->findRecordViaUsername($id_input);
		}

		$raw_info = array();
		if ($rec_arr && isset($rec_arr['dn'])) {

			if ($adapter->getLogger()) $adapter->getLogger()->logDebug("findRecordViaEmail result: " . print_r($rec_arr,1));

			$raw_info = $rec_arr;
			$raw_info['identity'] = $rec_arr['dn'];

			$auth = $this->getAuthAdapter()->getZendAuthAdapter();

			// Bogus because zend only creates ldap obj when its needed,
			// so this is a hack to get it to set all the correct options
			// for us
			try {
				$auth->setUsername('__bogus__');
				$auth->setPassword('__bogus__');
				$auth->authenticate();
			} catch (\Exception $e) {}

			/** @var $ldap \Zend\Ldap\Ldap */
			$ldap = $auth->getLdap();

			/** @var $rec \Zend\Ldap\Node */
			$rec = $ldap->getNode($rec_arr['dn']);

			if ($adapter->getLogger()) $adapter->getLogger()->logDebug("getNode result: " . print_r($rec,1));
		} else {
			if ($adapter->getLogger()) $adapter->getLogger()->logDebug("findRecordViaEmail result: null");
		}

		if ($rec) {
			$raw_info = array_merge($raw_info, $rec->getAttributes());

			if ($rec->getAttribute('givenName')) {
				$raw_info['first_name'] = $rec->getAttribute('givenName', 0);
			}
			if ($rec->getAttribute('sn')) {
				$raw_info['last_name'] = $rec->getAttribute('sn', 0);
			}

			if (isset($raw_info['first_name']) && isset($raw_info['last_name'])) {
				$raw_info['name'] = $raw_info['first_name'] . ' ' . $raw_info['last_name'];
			} elseif ($rec->getAttribute('name')) {
				$raw_info['name'] = $rec->getAttribute('name', 0);
			} elseif ($rec->getAttribute('cn')) {
				$raw_info['name'] = $rec->getAttribute('cn', 0);
			}

			if ($rec->getAttribute('mail')) {
				$raw_info['email_address'] = $rec->getAttribute('mail', 0);
			} elseif (\Orb\Validator\StringEmail::isValueValid($rec->getAttribute('userPrincipalName', 0))) {
				$raw_info['email_address'] = $rec->getAttribute('userPrincipalName', 0);
			}

			if ($rec->getAttribute('jpegPhoto')) {
				$raw_info['picture_data'] = $rec->getAttribute('jpegPhoto', 0);
			} else if ($rec->getAttribute('thumbnailPhoto')) {
				$raw_info['picture_data'] =$rec->getAttribute('thumbnailPhoto', 0);
			}

			if ($rec->getAttribute('telephoneNumber')) {
				$raw_info['phone'] = $rec->getAttribute('telephoneNumber', 0);
			}
		}

		if ($raw_info) {
			if ($adapter->getLogger()) $adapter->getLogger()->logDebug("RESULT: " . print_r($raw_info,1));

			$identity = new Identity($raw_info['identity'], $raw_info);
			return $identity;
		}

		return null;
	}


	/**
	 * @return array
	 */
	public function getCapabilities()
	{
		return array(
			'form_login',
			'find_identity'
		);
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