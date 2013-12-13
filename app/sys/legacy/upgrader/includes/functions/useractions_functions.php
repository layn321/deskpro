<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

/**
Ticket actions handler.
*
* @package DeskPRO
*/

/**
* creates a user
*
* @access	public
*
* @param	array	array of details of user
* @param    bool    True if you want the user information from the database
*                   to be returned after creation.
*
* @return	 array|int	details of user or the new userid if you don't need the user info
*/
function create_user($values, $return_userinfo = true) {

	global $db, $cache2;

	if (!$values['language']) {
		$language = $cache2->getDefaultLanguageID();
	}

	// encrypted pass
	$values['password_cookie'] = make_randomstring(30);
	$values['password_url'] = make_randomstring(30);

	$values['validate_key'] = substr(md5(time()),0,6);

	// registered
	$values['date_registered'] = TIMENOW;

	(DpHooks::checkHook('create_user_before') ? eval(DpHooks::getHook()) : null);

	$db->query("
		INSERT INTO user SET " . array2sqlinsert($values)
	);

	$userid = $db->insert_id();

	(DpHooks::checkHook('create_user_after') ? eval(DpHooks::getHook()) : null);

	if ($return_userinfo) {
		$user_details = user_from_field('id', $userid);
		return $user_details;
	} else {
		return $userid;
	}

}

function merge_user($masterid, $mergeid, $quick = false) {

	global $db;

	$masterid = intval($masterid);
	$mergeid = intval($mergeid);

	/********************************
	* Need to handle unique index situations
	********************************/

	$emails = $db->query_return_array("
		SELECT email FROM user_email WHERE (userid = $masterid OR userid = $mergeid) AND validated = 1
	");

	$db->query("
		DELETE FROM user_email
		WHERE userid = $masterid
		OR userid = $mergeid
	");

	$done_emails = array();

	if (is_array($emails)) {
		foreach ($emails AS $email) {

			if (in_array($email['email'], $done_emails)) {
				continue;
			}
			$done_emails[] = $email['email'];

			$db->query("
				INSERT INTO user_email SET
					email = '" . $db->escape($email['email']) . "',
					userid = $masterid,
					validated = 1
			");
		}
	}

	$db->query("UPDATE ticket_message SET userid = $masterid WHERE userid = $mergeid");
	$db->query("UPDATE ticket SET userid = $masterid WHERE userid = $mergeid");

	// Usergroups
	$groupids = array();

	$db->query("SELECT usergroup FROM user_member_groups WHERE user = $masterid OR user = $mergeid");

	while ($group = $db->row_array()) {
		$groupids[] = $group['usergroup'];
	}

	$db->query("DELETE FROM user_member_groups WHERE user= $masterid OR user = $mergeid");

	$groupids = array_unique($groupids);

	foreach ($groupids as $gid) {
		$db->query("INSERT INTO user_member_groups SET user = $masterid, usergroup = $gid");
	}

	// update the ids
	if (!$quick) {

		$db->query("UPDATE ticket_log SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE user_bill SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE user_notes SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE faq_articles SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE faq_comments SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE faq_rating SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE faq_subscriptions SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE faq_searchlog SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE email_send SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE email_send_log SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE faq_searchlog_solved SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE ticket_attachments SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE trouble_comments SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE trouble_rating SET userid = $masterid WHERE userid = $mergeid");


		// some deletes necessary
		$db->query("DELETE FROM user_session WHERE userid = $mergeid");
		$db->query("DELETE FROM tech_start_tickets WHERE userid = $mergeid");

	}

	// delete the old user
	$db->query("DELETE FROM user WHERE id = $mergeid");

}

/**
* delete users
*
* @access	public
*
* @param	array	array of ids of users
*
* @return	 int	number of deleted users
*/
function user_delete($id, $ban_email = false) {

	global $user, $db, $cache2;

	if (!$user['p_delete_users']) {
		return 0;
	}

	// turn single ticket into array
	if (!is_array($id)) {
		$id = array($id);
	}

	$where = array2sql($id);
	
	$emails = array();
	$db->query("
		SELECT user.*, user_email.email AS email
		FROM user
		LEFT JOIN user_email ON (user_email.userid = user.id AND user_email.id = user.default_emailid)
		WHERE user.id IN $where
	");
	while ($result = $db->row_array()) {
		$emails[] = $result['email'];
	}

	// ban emails?
	if ($ban_email) {
		if (is_array($emails)) {
			foreach ($emails AS $email) {
				ban_email($email);
			}
		}

	}
	
	// Delete temp tickets
	foreach ($emails as $email) {
		$db->query("DELETE FROM ticket_temp WHERE email = '" . $db->escape($email) . "'");
	}

	// delete the user and get count
	$db->query("DELETE FROM user WHERE id IN $where");
	$count = $db->affected_rows();

	if (!$count) {
		return 0;
	}

	// Delete the deskpro records as well
	$dpsource = $cache2->getDeskproUsersource();

	$deskpro_ids = $db->query_return_array_id("
		SELECT user_deskpro.id
		FROM user_deskpro
		LEFT JOIN user_map ON (user_map.remoteid = user_deskpro.id)
		WHERE
			user_map.localid IN $where
			AND user_map.sourceid = $dpsource[id]
	", 'id', '');

	if ($deskpro_ids) {
		$db->query("DELETE FROM user_deskpro WHERE id IN ". array2sql($deskpro_ids));
	}

	$db->query("DELETE FROM user_member_groups WHERE user IN $where");
	$db->query("DELETE FROM user_member_company WHERE user IN $where");
	$db->query("DELETE FROM user_member_company_role WHERE user IN $where");
	$db->query("DELETE FROM user_session WHERE userid IN $where");
	$db->query("DELETE FROM user_bill WHERE userid IN $where");
	$db->query("DELETE FROM user_email WHERE userid IN $where");
	$db->query("DELETE FROM user_notes WHERE userid IN $where");
	$db->query("DELETE FROM user_map WHERE localid IN $where");
	$db->query("DELETE FROM email_send WHERE userid IN $where");
	$db->query("DELETE FROM email_send_log WHERE userid IN $where");
	$db->query("DELETE FROM faq_searchlog_solved WHERE userid IN $where");
	$db->query("DELETE FROM trouble_comments WHERE userid IN $where");
	$db->query("DELETE FROM trouble_rating WHERE userid IN $where");
	$db->query("DELETE FROM faq_comments WHERE userid IN $where");
	$db->query("DELETE FROM faq_rating WHERE userid IN $where");
	$db->query("DELETE FROM faq_subscriptions WHERE userid IN $where");
	$db->query("DELETE FROM faq_searchlog WHERE userid IN $where");
	$db->query("DELETE FROM user_plan_subscriptions WHERE user_id IN $where");
	$db->query("DELETE FROM billing_order WHERE user_id IN $where");
	$db->query("DELETE FROM billing_transaction WHERE user_id IN $where");
	$db->query("DELETE FROM chat_chat WHERE userid IN $where");
	$db->query("DELETE FROM chat_message WHERE authortype = 'user' AND authorid IN $where");

	$db->query("UPDATE faq_articles SET userid = 0 WHERE userid IN $where");
	$db->query("UPDATE user_ideas SET user_id = 0, tracking_id = 'unknown".TIMENOW."' WHERE user_id IN $where");
	$db->query("UPDATE user_idea_comments SET user_id = 0 WHERE user_id IN $where");
	$db->query("UPDATE user_idea_votes SET user_id = 0, tracking_id = 'unknown".TIMENOW."' WHERE user_id IN $where");

	$db->query("DELETE FROM tech_start_tickets WHERE userid IN $where");

	$db->query("SELECT id FROM ticket WHERE userid IN $where");
	while ($ticket = $db->row_array()) {
		$tickets[] = $ticket[id];
	}

	// delete all the users tickets
	ticketactions_delete($tickets, true);

	// Delete all notebook pages
	$notebook_pages = $db->query_return_col("SELECT id FROM notebook_page WHERE user_id IN $where");

	if ($notebook_pages) {
		$notebook_attach_ids = $db->query_return_col("SELECT id FROM notebook_page_attach WHERE notebook_page_id IN ".array2sql($notebook_pages));
		if ($notebook_attach_ids) {
			delete_attachments('notebook_page_attach', $notebook_attach_ids);
		}

		$db->query("DELETE FROM notebook_page WHERE id IN ".array2sql($notebook_pages));
	}

	return $count;

}

/**
* delete a user
*
* @access	public
*
* @param	int	 user id
*/
function useractions_delban($userid) {

	global $db;

	$user = user_from_field('id', $userid);

	ban_email($user['email']);
	return user_delete($userid);
}

/**
* get user details by user id or user name or email id
*
* @access	public
*
* @param	mixed	value to search by
* @param	string	field name - id / username / email
*
* @return	 array	details of user
*/
function useractions_getuser($value, $type='id') {

	global $db;

	$types = array('id', 'username', 'email');
	if (!in_array($type, $types)) {
		$type = 'id';
	}

	// check in alternative email table
	if ($type == 'email') {
		$result == $db->query_return("SELECT * FROM user_email WHERE email = '" . $db->escape($value) . "'");
		if ($result) {
			$type = 'id';
			$value = $result['userid'];
		}
	}

	return user_from_field($type, $value);
}



/**
 * Get which user rules match the data provided. The rules returned
 * will be in the order in which they are meant to be applied.
 *
 * @param array $data Array of data to use as userinfo
 * @return array Array of matching rules (may be empty if no matches)
 */
function get_matching_user_rules($user_info) {

	global $cache2;

	$matching_rules = array();

	foreach ($cache2->getUserRules() as $rule) {

	    // If there is a time when we haveother rules that must be matched,
	    // this should be set to null so its not the only thing tested
		$email_match = false;

		if (is_array($rule['criteria']['email_match'])) {

			$email_match = false;

			foreach ($rule['criteria']['email_match'] as $email_pattern) {
				if (wildcard_pattern_match($email_pattern, $user_info['email'], true)) {
					$email_match = true;
					break;
				}
			}
		}

		if (is_null($email_match) OR $email_match) {
			$matching_rules[$rule['id']] = $rule;
		}
	}

	return $matching_rules;
}



/**
 * Get the changes that should be made by a set of user rules. The rules
 * are applied in the order in which they appear in the array.
 *
 * @param array $rules The rules to get the changes for
 * @return array Array of final changes
 */
function get_rule_changes($rules) {

	$changes = array();

	foreach ($rules as $rule) {
		if ($rule['actions']['email_validate'] == 'force_yes') {
			$changes['register_validate_user'] = 1;
		}
		if ($rule['actions']['email_validate'] == 'force_no') {
			$changes['register_validate_user'] = 0;
		}

		if ($rule['actions']['tech_validate'] == 'force_yes') {
			$changes['register_validate_tech'] = 1;
		}
		if ($rule['actions']['tech_validate'] == 'force_no') {
			$changes['register_validate_tech'] = 0;
		}

		if (is_array($rule['actions']['add_usergroups'])) {
			$changes['add_usergroups'] = array_merge((array)$changes['add_usergroups'], $rule['actions']['add_usergroups']);
		}

		if (is_array($rule['actions']['add_companies'])) {
			$changes['add_companies'] = array_merge((array)$changes['add_companies'], $rule['actions']['add_companies']);
		}
	}

	return $changes;
}


/**
 * Process a user and see if they can now access tickets they are participants in.
 *
 * @param array|int|User $user_details The user to work on. Either array of userinfo, User object or user ID
 * @return bool
 */
function update_ticket_participants_validated($user) {

    global $db;

    if (is_array($user)) {
        $userobj = new User();
        $userobj->setUser($user);
    } elseif (is_object($user)) {
        $userobj = $user;
        $user_details = $userobj->getUser();
    } elseif (is_numeric($user)) {
        $userobj = new User($user);
        $user_details = $userobj->getUser();
    } else {
        return false;
    }

    if (!$userobj->isUser()) {
        return false;
    }

    if ($user_details['awaiting_register_validate_tech'] OR $user_details['awaiting_register_validate_user']) {
        return false;
    }

    $default = $userobj->getEmailAddress('default');
    foreach ($userobj->getEmails(true) as $email) {
        $default_bit = '';
        if ($email == $default) {
            $default_bit = ', email = \'\'';
        }

        $db->query("
        	UPDATE ticket_participant
        	SET user = {$user_details['id']}, code = null $default_bit
        	WHERE user = 0 AND user_type = 'user' AND email = '" . $db->escape($email) . "'
        ");
    }

    return true;
}