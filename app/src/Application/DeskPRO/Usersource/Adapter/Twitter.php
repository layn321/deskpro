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

class Twitter extends AbstractAdapter
{
	public function getFieldsFromIdentity(Identity $identity)
	{
		$info = $identity->getRawData();
		return array(
			'name' => $info['fullname'] ?: $info['identity_friendly'],
			'twitter' => array(
				'screen_name' => $info['identity_friendly'],
				'user_id' => $info['identity'],
				'oauth_token' => $info['access_token'],
				'oauth_token_secret' => $info['access_token_secret']
			)
		);
	}


	public function getDisplayName(array $info)
	{
		return '@' . $info['identity_friendly'];
	}


	public function getDisplayLink(array $info)
	{
		return 'htpt://twitter.com/' . $info['identity_friendly'];
	}


	/**
	 * @return \Orb\Auth\Adapter\Twitter
	 */
	protected function _createAuthAdapterObject()
	{
		return new \Orb\Auth\Adapter\Twitter(
			$this->usersource->getOption('consumer_key'),
			$this->usersource->getOption('consumer_secret')
		);
	}


	/**
	 * @return array
	 */
	public function getCapabilities()
	{
		return array(
			'tpl_login_pull_btn',
			'tpl_widget_overlay_btn',
			'tpl_newcomment_tab',
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
