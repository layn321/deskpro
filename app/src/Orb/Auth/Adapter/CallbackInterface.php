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
 * Orb
 *
 * @package Orb
 * @category Auth
 */

namespace Orb\Auth\Adapter;

/**
 * Adapters that use a two-step authentication scheme with a callback (such as OpenID)
 * should implement this interface.
 *
 * When the callback page is called up, the context is set which should change the operations
 * used in the authenticate() method of AdapterInterface.
 */
interface CallbackInterface extends AdapterInterface
{
	/**
	 * Switches the adapter to the callback context using form data $data.
	 *
	 * @param array $data Form data or other callback data
	 * @return void
	 */
	public function setCallbackContext(array $data);


	/**
	 * Set the callback URL the user should return to when the remote service is finished.
	 *
	 * @param string $url The URL
	 */
	public function setCallbackUrl($url);
}
