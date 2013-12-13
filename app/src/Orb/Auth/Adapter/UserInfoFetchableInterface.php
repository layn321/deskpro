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

namespace Orb\Auth\Adapter;

/**
 * Auth adapters that implement this usersource can return raw userinfo given an ID.
 * For example, DbTable can return a database row which has nothing to do with authenticating.
 */
interface UserInfoFetchableInterface
{
	/**
	 * Fetch userinfo based on $id. $id must be something unique, but the field itself
	 * is unknown. It might be a userid, a username or an email address, or something else.
	 *
	 * $id_type is used to specify the specific type $id is. If it is null, then the implementation
	 * must guest or simply return null for no-match.
	 *
	 * Standard strings for $id_type are: id, username, email.
	 *
	 * This method must return null if no match was found.
	 *
	 * @param  mixed $id
	 * @param  mixed $id_type
	 * @return mixed
	 */
	public function getUserInfoFromIdentity($id, $id_type = null);
}