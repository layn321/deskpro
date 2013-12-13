<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

/**
* Utility functions for the administration and user interfaces
*
* @package DeskPRO
*/


function sql_temp_create() {

	$ref = make_table_ref('sql_temp');

	$db->query("
		INSERT INTO sql_temp SET
			ref = '$ref',
			timestamp = '" . TIMENOW . "
	'");

	return $ref;

}

function sql_temp_add($ref, $sql, $sql2='') {

	$db->query("
		INSERT INTO sql_temp SET
			ref = '" . $db->escape($ref) . "',
			sqltext = '" . $db->escape($sql) . "',
			sqltext2 = '" . $db->escape($sql2) . "',
			timestamp = " . TIMENOW . "
	");

	return $db->insert_id();

}

function sql_temp_process($ref, $replace = '') {

	global $db;

	// get all the results
	$results = $db->query_return_array("
		SELECT * FROM sql_temp WHERE
			ref = '" . $db->escape($ref) . "'
			AND sqltext != ''
	");

	if (!is_array($result)) {

		foreach ($results AS $key => $var) {

			$sql = $var['sqltext'];

			// are we making a replacement, generally this would be linking this data to the row in the master table that is now created
			if ($replace) {
				$sql = str_replace($ref, $replace, $sql);
			}

			// run squery
			$db->query($sql);

			$id = $db->insert_id();

			// this is if we are creating some other link in another table and need $id as a replacement as well.
			if ($var['sqltext2']) {

				$sql = $var['sqltext2'];

				if ($replace) {
					$sql = str_replace($ref, $replace, $sql);
				}

				$sql = str_replace('SQL_TMP_ID', $id);

				$db->query($sql);

			}
		}
	}
}




function pageend() {

	global $header;
	echo $header->footer();
	exit();

}

function array_sum_multi($data) {

	// first group of arrays
	foreach ($data AS $array) {

		foreach ($array AS $key => $var) {
			$total[$key] += $var;
		}
	}

	foreach ($total AS $key => $var) {
		give_default($total[$key], '0');
	}

	return $total;

}

function user_gateway_on_check() {

	global $settings;

	if (!$settings['gateway_user_on']) {

		echo "<br /><br />";

		$error = new class_ErrorBox('Email Gateway Disabled', 'The email gateway is currently disabled; no emails will be processed by ' . DP_NAME . ' unless you enable it.', 'cross');
		$error->addOption('Enable Email Gateway', './settings_update.php?settings[gateway_user_on]=1&url=' . FILENAME);
		$error->showError();

	}
}

/**
* Associates temporary images with the article that created them, once created.
*
* @access	Public
*
* @return	 string
*/
function images_temp_process($tempkey, $content_type, $content_id) {

	global $db;

	$db->query("
		UPDATE images SET
			tempkey = '',
			content_id = " . intval($content_id) . "
		WHERE tempkey = '" . $db->escape($tempkey) . "'
			AND content_type = '" . $db->escape($content_type) . "'
	");

}

/**
* Returns array of active techs
*
* @access	Public
*
* @return	 string	array	contains all account details, permission details
* and last visit details of each active tech
*/
function get_active_techs() {

	global $db, $cache2, $settings;

	$techs = $cache2->getTechs(true, true);

	$timesnip = TIMENOW - $settings['tech_session_length'];
	$db->query("
		SELECT lastactivity, path, techid
		FROM tech_session
		WHERE lastactivity > $timesnip
		ORDER BY tech_session.lastactivity DESC
	");

	while ($result = $db->row_array()) {

		$row = $techs[$result['techid']];
		$row = array_merge($row, $result);

		$data[] = $row;

	}

	if (!is_array($data)) {
		$data[]['username'] = 'There are no active techs';
	}

	return $data;

}


/**
 * Get the HTML from a WYSIWYG control. This will check a techs
 * permission to see if they can post Javascript (thus uses getAll)
 * or not (and use getString which passes through xss filter).
 *
 * @param string $name Name of the incoming variable
 * @param string $type Where to get the variable (request, get, post)
 * @param array $tech The tech to use when checking perms. Defaults to current tech
 * @return string The WYSIWYG content
 */
function get_tech_wysiwyg_content($name, $type = 'request', $tech = null) {

	global $request;

	if (is_null($tech)) {
		$tech = $GLOBALS['user'];
	}

	if ($tech['p_use_js']) {
		return $request->getRaw($name, $type);
	} else {
		return $request->getString($name, $type);
	}
}


/**
* Returns array of all keys of an array
*
* @access	Public
*
* @param	mixed array	array to get keys from
*
* @return	 string	array	array of keys
*/
function get_array_actiond_ids($ids) {

	if (is_array($ids)) {

		$ids = array_keys($ids);
		return $ids;

	} else {
		return false;
	}
}

function gateway_user_check() {

	global $db, $cache2;

	// check we have an email account (ie the email/name to reply to)
	if (!$account = $cache2->getGatewayEmailDefault()) {
		jump('gateway_emails.php', 'An email account is required');
	}

	// we have an email account. Now if we don't have a default rule, create it
	if (!$cache2->getRulesMailDefault()) {

		$db->query("
			INSERT INTO ticket_rules_mail SET
				is_default = 1,
				accountid = " . intval($account['id']) . "
		");

		// forces data rebuild if run again
		unset($cache2->rules_mail);

	}

	return true;
}

/**
 * Delete a gateway account from the database and
 * turn off it's gateway option if applicable
 *
 * @param array $gateway The array of gateway details
 */
function clear_gateway_account($account) {

	global $db;

	$db->query("
		DELETE FROM gateway_pop_accounts
		WHERE id = {$account['id']}
	");

	if ($account['target'] != 'user') {
		update_setting('gateway_' . $account['target'] . '_on', 0);
		update_setting('gateway_' . $account['target'] . '_email', 0);
	}
}



/**
* Adds link to the header object for provided section
*
* @access	Public
*
* @param	string	adds links to header object for the supplied section
*/
function header_links($section) {

	global $header, $user;

	/**************************
	* Tech News
	***************************/

	if ($section == 'tech_news') {

		$header->addLink('View Read News', 'technews_view.php?type=read');
		$header->addLink('View Unread News', 'technews_view.php?type=unread');

		if ($user['p_edit_technews']) {

			$header->addLink('Edit News', 'technews_edit.php?do=list');
		}
		if ($user['p_add_technews']) {

			$header->addLink('Create News', 'technews_edit.php?do=new');
		}

	} elseif ($section == 'style') {

		$header->addLink('View Styles', 'styles.php?do=view');
		$header->addLink('CSS Replacements', 'style_css.php');
		$header->addLink('Header, Footer & Extra CSS', 'style_templates.php');
		$header->addLink('View Stylesheets', 'style_stylesheets.php');
		$header->addLink('View Template Sets', 'template_sets.php');

	} elseif ($section == 'user_news') {

		$header->addLink('View User News', 'usernews.php');

		if ($user['p_add_announcements']) {
			$header->addLink('Create News', 'usernews.php?do=new');
		}
	} elseif ($section == 'tech_bookmarks') {

		$header->addLink('View Bookmarks', 'bookmarks.php?do=view');
		$header->addLink('Create Bookmark', 'bookmarks.php?do=add');
		$header->addLink('Edit Folders', 'folders.php?type=attachments');

	} elseif ($section == 'tech_files') {

		$header->addLink('View Files', 'files.php?do=view');
		$header->addLink('Add Files', 'files.php?do=add');
		$header->addLink('Add Global Files', 'files.php?do=add_global');
		$header->addLink('Edit Folders', 'folders.php?type=attachments');

	} elseif ($section == 'tech_notes') {

		$header->addLink('View Notes', 'notes.php?do=view');
		$header->addLink('Add Note', 'notes.php?do=add');
		$header->addLink('Edit Folders', 'folders.php?type=attachments');

	} elseif ($section == 'tech_gen_settings') {

		$header->addLink('General Settings', 'index.php');
		$header->addLink('Update Email Address', 'changeemail.php');
		$header->addLink('Change Password', 'changepass.php');

	} elseif ($section == 'tech_private_msgs') {

		$header->addLink('View Read Private Messages', 'pms.php?is_read=1');
		$header->addLink('View Unread Private Messages', 'pms.php');
		$header->addLink('Send Private Message', 'pms.php?do=send');

	} elseif ($section == 'tech_user_emails') {

		$header->addLink('View Emails', 'email.php');
		$header->addLink('Send Email', 'email.php?do=send');
	}

}




/**
 * Create a permissions table for any amount of different types of permissions
 *
 * $permissions should be a multi-dimentional array such as:
 * <code>
 * array(
 *	'can_view' => array('caption' => 'Can View', 'show_types' => array('system', 'usergroup'));
 *	'can_view' => array('show_reg' => false, 'show_guest' => false);
 * );
 * </code>
 *
 * The key is the type of permission and the array content is which usergroups you
 * want to be selectable. If you do not supply any data in the array then all usergroups
 * will be selectable.
 *
 * $options lets you specify some options about Content. These keys are used:
 * - title : Title of the content table.
 * - title_help : Enable/disable title help
 * - row_tips : Enable/disable row tooltip help
 * - name : The name of the permissions table. This will be used as helpkeys for rows
 *          if you enable that option:  name_perm_type  (ie. ticketfields_perms_user_viewable)
 * - return: To return the content object after. If false, it will be echoed
 * - content: An existing content object to use
 *
 * @param array $permissions
 * @param array $options
 *
 * @return Content A content object if requested to return, otherwise, nothing
 */
function generate_permissions_fields($permissions = array(), $options = array()) {

	global $cache2;

	$permissions = (array)$permissions;

	$content_title = ifsetor($options['title'], 'Permissions');
	$content_title_help = ifsetor($options['title_help'], false);
	$content_row_tips = ifsetor($options['row_tips'], false);
	$content_name = ifsetor($options['name'], 'customfields_permissions');

	if (is_object($options['content'])) {
		$content = &$options['content'];
	} else {
		$content = new content($content_name, $content_title, $content_title_help, $content_row_tips);
	}

	$guest_id = $cache2->getNamedUserGroupId('guest');
	$reg_id = $cache2->getNamedUserGroupId('registered');

	foreach ($permissions as $perm_type => $info) {

		$caption = ifsetor($info['caption'], 'Permission');
		$info['show_types'] = ifsetor($info['show_types'], array('system', 'usergroup', 'company'));

		$show_sys = (array_search('usergroup', $info['show_types']) !== false);
		$show_cust = (array_search('cust_usergroup', $info['show_types']) !== false);

		$show_guest = ifsetor($info['show_guest'], true);
		$show_reg = ifsetor($info['show_reg'], true);
		$show_none = ifsetor($info['show_none'], false);

		if (!$show_guest) {
			$exclude[] = 'guest';
		}
		if (!$show_reg) {
			$exclude[] = 'registered';
		}

		$select_groups = '';

		if ($show_sys OR $show_cust) {

			$opts = $cache2->getUsergroupNames($show_none, $exclude);

			if (!$show_reg) {
				unset($opts[$reg_id]);
			}

			if (!$show_guest) {
				unset($opts[$guest_id]);
			}

			if ($opts) {
				$select_groups = form_select($perm_type, $opts, 'permissions_groups', ifsetor($info['start'], array()), '', '', '10');
			}
		}

		if (!$select_groups) {
			continue;
		}

		$is_show_all = in_array(0, (array)$info['start']);

		$r = $content->newRow(array(
			$caption,
			'
				<label for="perms_select_'.$perm_type.'_all">
					<input type="radio" name="permissions_all['.$perm_type.']" id="perms_select_'.$perm_type.'_all" value="1" '.iff($is_show_all, 'checked="checked"').' onclick="if(this.checked)document.getElementById(\'perms_select_'.$perm_type.'\').style.display=\'none\'; else document.getElementById(\'perms_select_'.$perm_type.'\').style.display=\'\';" /> Allow All
				</label>
				<label for="perms_select_'.$perm_type.'_specify">
					<input type="radio" name="permissions_all['.$perm_type.']" id="perms_select_'.$perm_type.'_specify" value="0" '.iff(!$is_show_all, 'checked="checked"').' onclick="if(this.checked)document.getElementById(\'perms_select_'.$perm_type.'\').style.display=\'\'; else document.getElementById(\'perms_select_'.$perm_type.'\').style.display=\'none\';" /> Specify
				</label>
				<div id="perms_select_'.$perm_type.'" '.iff($is_show_all, 'style="display:none"').'>
				<hr />
				'.iff($select_groups, $select_groups, 'No usergroups are selectable.').'
			'
		));

		if ($content->showrowtips) {
			$r->setHelpKey($content->name + '_' + $perm_type);
		}
	}

	$content->columnStyle('width="300"');

	if ($options['return']) {
		return $content;
	} else {
		$content->build();
	}
}





/**
* generates sql clause for category search
*
* @access Public
*
* @param	string	part of where clause in sql query
* @param	string	type of user
*
* @return	 string / array	if $querybit is provided returns $querybit with comma separated
* cat ids appended to it
* other wise returns array of category ids
*/
function ticket_cat_permissions($querybit='', $type = 'all') {

	global $user, $cache2;

	$a = explode(',', $user['cats_admin']);
	$b = explode(',', $user['cats_user']);

	if ($type == 'all') {
		$restrict = array_merge($a, $b);
	} elseif ($type == 'user') {
		$restrict = $b;
	} elseif ($type == 'admin') {
		$restrict = $a;
	}

	$add_restrict = array();

	foreach ($restrict as $catid) {
		if ($catid != 0) {
			$add_restrict = array_merge($add_restrict, $cache2->getCategoryChildren($catid));
		}
	}

	$restrict = array_unique(array_merge($add_restrict, $restrict));

	// return if no elements
	$restrict = array_remove_empty($restrict);
	if (!is_array($restrict)) {
		return;
	}

	// make a query if that is what we are doing
	if ($querybit) {
		return " $querybit " . array2sql($restrict);
	}

	// otherwise we are returning the array
	return $restrict;

}

/**
* generates sql clause for category search
*
* @access	Public
*
* @param	string	part of where clause in sql query
* @param	string	type of user
*
* @return	 string / array	if $querybit is provided returns $querybit with comma separated
* cat ids appended to it
* other wise returns array of category ids
*/
function ticket_cat_permissions_null($type = 'all') {

	global $user, $cache2;

	$a = explode(',', $user['cats_admin']);
	$b = explode(',', $user['cats_user']);

	if ($type == 'all') {
		$restrict = array_merge($a, $b);
	} elseif ($type == 'user') {
		$restrict = $b;
	} elseif ($type == 'admin') {
		$restrict = $a;
	}

	$add_restrict = array();
	foreach ($restrict as $catid) {
		$add_restrict = array_merge($add_restrict, $cache2->getCategoryChildren($catid));
	}

	if ($add_restrict) {
		$restrict = array_merge($restrict, $add_restrict);
	}

	$restrict = array_unique($restrict);

	// return if no elements
	$restrict = array_remove_empty($restrict);
	if (!is_array($restrict)) {
		return true;
	}

	if (in_array('0', $restrict)) {
		return false;
	} else {
		return true;
	}
}

/**
* generates not in where clause for category search
*
* @access	Public
*
* @param	string	name of field that to fetch
* @param	string	where clause to append to
*
* @return	 string	part of where clause that contains $field not in values
*/
function cat_restrict($field, $where='') {

	global $user;

	if ($user['cats_admin']) {
		return iff($where, ' WHERE ', ' AND ') . " $field NOT IN ( " . $user['cats_admin'] . ") ";
	}
}


/**
* updates order field in table insuring all order is always 1 -> x and no gaps
*
* @access	Public
*
* @param	string	name of table
* @param	string	name of order field
*/

function update_table_orders($table, $field, $where='') {

	global $db;

	if ($where) {
		$where = ' WHERE ' . $where;
	}

	$data = $db->query_return_array_id("
		SELECT id, $field
		FROM $table
		$where
		ORDER BY $field
	", $field);

	$i = 0;

	if (is_array($data)) {
		foreach ($data AS $id => $order) {

			$i++;

			if ($order != $i) {
				$queries[] = "UPDATE $table SET $field = $i WHERE id = $id";
			}
		}

		run_sql_queries($queries);
	}
}

/**
* executes multiple queries
*
* @access	Public
*
* @param	string array	array of queries
*/
function run_sql_queries($queries) {

	global $db;

	if (!is_array($queries)) {
		return;
	}

	foreach ($queries AS $query) {
		$db->query($query);
	}

}

/**
* reads file and returns its contents
*
* @access Public
*
* @param	string	name of file
*
* @return	 string	contents of file
*
*
*/

/**
* checks regular expression is valid or not
*
* @access	Public
*
* @param	string	regular expression
*
* @return	 boolean	true if expression is valid and false if not valid
*/
function valid_regex($text) {

	if (trim($text) != '') {

		$match = @preg_match($text, '');

		// It's invalid unless $match is an integer
		if (is_numeric($match)) {
			return true;
		} else {
			return false;
		}
	}
}

/**
* sends personal message. Saves in database and send mail as per setting
*
* @access Public
*
* @param	string array	array of techs to whome pm has to be send
* @param	string	subject of message
* @param	string	body of message
*
* @return	 boolean	true
*/
function pm_send($to, $title, $message, $fromid = NULL, $fromname = NULL) {

	global $user, $db, $cache2;

	// get all the tech info, keyed by id
	$techs = $cache2->getTechs(1);

	if (!$fromid) {
		trigger_error("No Private Message Sender Specified");
	}

	$message = wordwrap($message);
	$title = dp_html($title);

	if (!is_array($to)) {
		$to = array($to);
	}

	// create pms for each tech specified
	foreach ($to AS $toid) {

		if (!is_array($techs[$toid])) {
			return false;
		}

		// set in the database
		$db->query("
			INSERT INTO tech_pms SET
				message = '" . $db->escape($message) . "',
				title = '" . $db->escape($title) . "',
				fromid = " . intval($fromid) . ",
				timestamp = '" . TIMENOW . "',
				toid = " . intval($toid) . "
		");

		// should we email?
		if ($techs[$toid]['email_pm']) {

			if (is_numeric($fromid) AND $fromid > 0) {
				$sender = $techs[$fromid]['username'];
			} else if ($fromname) {
				$sender = $fromname;
			} else {
				$sender = $user['username'];
			}

			$variables = array(
				'id' => $db->insert_id(),
				'sender' => $sender,
				'username' => $techs[$toid]['username'],
				'message' => text_from_html($message),
				'title' => $title
			);

			send_tech_email('newpm', $techs[$toid], $variables);

		}
	}

	return 1;

}

/**
* checks a value exists in an array and returns true or false
*
* @access	Public
*
* @param	mixed	value to check for
* @param	mixed array	array to check in
*
* @return	 boolean	true if value exists and false if value does not exists
*/
function in_array_error($needle, $haystack) {

	if (!is_array($haystack)) {
		return false;
	}

	return in_array($needle, $haystack);
}

/**
* updates a single setting
*
* @access	Public
*
* @param	string	name of setting
* @param	mixed	value of setting
*
* @return	 boolean	true if updated else false
*/
function update_setting($name, $value) {

	global $db, $settings;

	// we don't have this setting, create it (e.g. hidden setting)
	if (is_array($settings) AND !array_key_exists($name, $settings)) {

		$db->query("
			INSERT INTO settings SET
				value = '" . $db->escape($value) . "',
				name = '" . $db->escape($name) . "'
		");

	// we have the setting, just update
	} else {

		$db->query("
			UPDATE settings SET
				value = '" . $db->escape($value) . "'
			WHERE
				name = '" . $db->escape($name) . "'
		");

	}

	$settings[$name] = $value;
}


/**
* saves user note
*
* @access	Public
*
* @param	int	id of user
* @param	string	note for user
* @param	string
*
* @return		boolean	true
*/
function add_user_note($userid, $note, $global) {

	global $db, $user;

	dp_html($note);

	if (!$user['p_global_note']) {
		unset($global);
	}

	$db->query("
		INSERT INTO user_notes SET
		userid = '" . $db->escape($userid) . "',
		techid = " . intval($user['id']) . ",
		note = '" . $db->escape($note) . "',
		timestamp = " . TIMENOW . ",
		global = '" . $db->escape($global) . "'
	");

	return 1;

}

/**
* to recursivly display a directory	structure using standard list tags for use with	dhtml/js script.
*
* @access	Public
*
* @param	string array	array of data of the form [parent][key] = [to display]
* @param	string	note for user
* @param	string
*
* @return	 boolean	true
*/
function simpledisplaychildren($data, $parent=0) {

	if (!$parent) {
		$html = "<ul class=\"mktree\" id=\"tree1\">";
	} else {
		$html = "<ul>";
	}
	foreach ($data[$parent] AS $key => $var) {
		$html .= "<li id=\"node$key\">$var</li>";
		if (is_array($data[$key])) {
			$html .= simpledisplaychildren($data, $key);
		}
	}
	$html .= "</ul>";

	return $html;
}

/**
* checks whether demo mode is set or not and gives alert
*
* @access	Public
*
* @param	boolean	flag to display alert or not
*
* @return	 boolean	true if demo mode is set other wise false
*/
function demo_check() {

	if (defined('DESKPRO_DEBUG_DEMOMODE')) {
		mistake(DP_NAME . ' is currently running in DEMO MODE. This action was cancelled and was not processed or saved.');
	}
}

function managed_end() {
	die('Disabled for Managed ' . DP_NAME);
}

/**
 * Check auth for a so called "super admin" login.
 * Will check for a valid login, or show a login form an exit.
 *
 * Some scripts are "super admin" scripts, are designed to be used in emergency when an
 * admin is locked out. So far this only includes resetting an admin password.
 */
function check_sa_config()
{
    global $request, $header;

    if (defined('MANAGED')) {
        managed_end();
    } // END_MANAGED

    /******************************
    * We have enabled SA, so check if we're logged in
    ******************************/

    // Note how we dont tell the user if we even have SA enabled.
    // Our goal is to make them always check config.

    if (defined('SA_ENABLE_PASSWORD') AND strlen(SA_ENABLE_PASSWORD) >= 10) {
        $hashed = sha1(SA_ENABLE_PASSWORD . get_serialize_secure_string());

        if ($_COOKIE['sa_enable_password'] == $hashed) {
            return;
        } else {
            if ($request->getString('sa_enable_password') == SA_ENABLE_PASSWORD) {
                dp_setcookie('sa_enable_password', $hashed, TIMENOW+TIMEDAY);
                return;
            }
        }
    }

    /******************************
    * Not logged in, so show info/login form
    ******************************/

    $header->simple();

    ?>

    <h2>Authentication Required</h2>
    <form action="<?php echo htmlspecialchars($_SERVER['SCRIPT_NAME']); ?>" method="post">
    Enter password: <input type="password" name="sa_enable_password" size="40" />
    <input type="submit" value="Go" />
    </form>

    <h3>About</h3>
    This is a SUPER ADMIN tool. To use this tool, you must edit your <em>includes/config.php</em> file and add the following line:
    <pre style="background-color: #eee; margin: 10px; padding: 6px;">
define('SA_ENABLE_PASSWORD', '<span style="color:blue">any secure password</span>');
    </pre>

    Make sure to change the password (highlighted in blue). It must be at least 10 characters long.<br /><br />

    <strong style="color:red">Important:</strong> After using any super admin tool, you should re-edit your config file and
    disable access by removing the line above.

    <?php

    $header->simplefooter();

    exit;
}

/**
* checks whether demo mode is set or not and gives alert
*
* @access	Public
*
* @param	boolean	flag to display alert or not
*
* @return	 boolean	true if demo mode is set other wise false
*/
function generate_ticket_reply($options, &$content, $ticketid='0') {

	global $user, $db, $request, $settings;

	/*************
	* Create the row
	*************/
	$row = $content->newRow();

	/*************
	* Add message box cell
	*************/

	// Get draft
	if ($ticketid) {
		$draft = $db->query_return("SELECT * FROM ticket_message_draft WHERE techid = {$user['id']} AND ticketid = {$ticketid} ORDER BY id DESC LIMIT 1");

		if ($draft) {
			$draft_html = "<div id=\"reply_draft_loaded\">Loaded the reply draft that was saved on: ".dpdate('D, M jS @ g:i:s', $draft['updated_at'])."</div>";
		}

		$draft_js = '
			<script type="text/javascript">
			var ReplyDrafts = new DeskPRO.Tech.TicketView.Drafts({
				ticketId: '.intval($ticketid).'
			});
			$(\'#ticketreplyform\').submit(function() {

				ReplyDrafts.setPaused(true);

				if (typeof InstantUpdate != \'undefined\') {
					InstantUpdate.setPaused(true);
				}
			});
			</script>
		';
	}

	if ($request->getString('reply', 'request')) {
	    $default_content = dp_html($request->getString('reply', 'request'));
	} elseif ($draft['message']) {
	    $default_content = dp_html($draft['message']);
	} else {
	    $default_content = "\n\n\n".dp_html($user['signature']);
	}

	$row->addCell("
		<textarea name=\"reply\" id=\"reply\" style=\"width:95%\" rows=\"16\" onselect=\"storeCaret(this);\" onclick=\"storeCaret(this);\" onkeyup=\"storeCaret(this);\">" . $default_content . "</textarea>
		<div id=\"reply_draft_auto_save_wrap\" style=\"display:none\">Reply draft was automatically saved on: <span id=\"reply_draft_auto_save\"></span></div>
		$draft_html
		$draft_js
	");

	/*************
	* Build JS for quick reply
	*************/

	// Get categories
	$db->query("
		SELECT * FROM quickreply_cat
		WHERE techid = " . intval($user['id']) . " OR global = 1
	");

	$cats = array();
	while ($cat = $db->row_array()) {
		$cats[ $cat['id'] ] = $cat;
	}

	// Get the quick replies
	$ids = array_keys($cats);

	$db->query("
			SELECT name, id, category, response
			FROM quickreply
			WHERE (category IN " . array2sql($ids) . "
				OR (category = 0))
			ORDER BY category ASC
		");

	// We know for sure we have something to show,
	// so we can build the JS
	if ($db->num_rows()) {

		$js = "<script language=\"javascript\">";
		$js .= "reply = {};";
		$js .= "reply[0] = {};

		function buildselect() {
			document.forms['dpform'].drop.options[0] = new Option('--- Quick Replies ---', '0');
		";

		// Build the cats JS
		$cat_temp = '';
		$i = 1;

		foreach ($cats as $id => $cat) {
			$cat_temp .= "\nreply[$id] = {};\n";
			$js .= "document.forms['dpform'].drop.options[$i] = new Option('" . addslashes_js($cat['name'], true) . "', '$cat[id]');\n";
			$i++;
		}

		$js .= "	return true;\n}\n\n";
		$js .= $cat_temp;

		// Build quick replies js
		while ($article = $db->row_array()) {
			$js .= "reply[$article[category]][$article[id]] = DeskPRO.htmlEntityDecode('" . addslashes_js(dp_html($article['name']), true) . "');\n";
		}

		$js .= "</script>";

		$row->addCell('<center>
		<select name="drop" style="width:150px" id="quick_reply_change" onchange="showdata(dpform.drop.value)"></select><br /><br />
		<select size="10" name="list3" id="quick_reply_list" style="width:150px"></select>
		<br /><br />
		<span id="quick_reply_button"><input type="button" onClick="insert_quickreply(' . $ticketid . ')" value="Insert into Reply" /></span>
		<span id="quick_reply_loading" style="display:none"><img src="'.WEB.'images/tech/loading_small.gif" alt="Loading" /></span>
		');

		$return = true;

	} else {

		$row->addCell("<center>No <a href=\"./quick_reply.php\">quick replies</a> currently defined</center>");

		$return = false;

	}

	$html .= "<table width='100%'><tr>";
	$i = 0;

	foreach ($options AS $key => $var) {
		if ($i == 3) {
			$html .= "</tr><tr>";
		}
		$i++;
		$html .= "<td width=\"5\" style='width:5px;'>$var[0]</td><td>$var[1]</td>";
	}
	$html .= "</tr></table>";

	$str = "<input type=\"button\" name=\"Insert Faq\" value=\"Insert Knowledgebase Article\" onclick=\"openWindow('./faqpop.php?contenttype=".$header->contenttype."', 700, 600, 'faq');\">";

	$content->newRow($html . $js, "<div style=\"text-align:center\">" . $str . "</div>");

	return $return;

}

/**
* add escape sequence for special characters like ', " and new line
*
* @access	Public
*
* @param	string	text to change
* @param	string	changed text
*/
function quotemessage($message) {

	$message = trim($message);
	$message = preg_replace("/(\r\n|\n|\r)/", "\n> ", $message);
	$message = addslashes_js($message);
	$message = '\\n> ' . $message;
	return $message;
}

/**
* Generate a JavaScript segment that automatically redirects the browser to	the index page to enforce the use of frames.
*
* @access	Public
*/
function reload_index() {

	?>
	<SCRIPT language="JavaScript">
	if (parent.location.href == self.location.href) {
		window.location.href = "./index.php?url=" + self.location.href;
	}
	</SCRIPT>
	<?php
}

/**
* Ignores the requested URL and redirects straight back	to home/index.php.
*
* @access	Public
*/
function reload_index_nourl() {

	?>
	<SCRIPT language="JavaScript">
	if (parent.location.href == self.location.href) {
		window.location.href = "./index.php";
	}
	</SCRIPT>
	<?php
}

/**
* Is used for tech/home/index.php; 	if the browser *is* in a frameset, break out and start it over again
*
* @access	Public
*/
function reload_index_frameset() {

	?>
	<SCRIPT language="JavaScript">
	if (parent.location.href != self.location.href) {
		window.location.href = "./index.php";
	}
	</SCRIPT>
	<?php
}

/**
* Changes the index with an assoicative array
*
* @access	Public
*
* @param	mixed	index of has to change
* @param	mixed	 array according to which index has to change
*
* @return	 mixed	array	index changed original array
*/
function change_index($array, $new_index) {
	if (is_array($array)) {
		foreach ($array AS $key => $var) {
			$tmp[$new_index[$key]] = $var;
		}
	}
	return $tmp;
}

/**
* Correct line spanning issues in JS strings.
*
* @access	Public
*
* @param	string	Text to correct
*
* @return	 string	array	Corrected text usable in JavaScript
*/
function js_linespan($text) {

	$text = str_replace("\n\r","\\r\\n", $text);
	$text = str_replace("\r\n","\\r\\n", $text);
	$text = str_replace("\n","\\r\\n", $text);
	$text = str_replace("\r","\\r\\n", $text);

	return $text;
}

/*****************************************************
	function array_unshift_keys

-----DESCRIPTION: -----------------------------------


*****************************************************/

function array_unshift_keys(&$arr, $key, $val) {

	$arr = array_reverse($arr, true);
	$arr[$key] = $val;
	$arr = array_reverse($arr, true);
	count($arr);

}

/**
* activate technician
*
* @access	Public
*
* @param	int	Id of technician
*/
function enable_tech($id) {

	global $db;

	$db->query("
		UPDATE tech SET
			active = '1'
		WHERE id = " . intval($id) . "
	");
}

/**
* deactivate technician
*
* @access	Public
*
* @param	int	Id of technician
*/
function disable_tech($id) {

	global $db;

	// ticket
	$db->query("SELECT id FROM ticket WHERE tech = " . intval($id));

	// Add entries to ticket logs for each affected ticket
	while ($ticket = $db->row_array()) {
		ticketactions_changeproperty($ticket, 'tech', $id);
	}

	// disable tech
	$db->query("
		UPDATE tech SET
			active = '0'
		WHERE id = " . intval($id) . "
	");
}

/**
* Adds an e-mail address to the banned list.
*
* @access	Public
*
* @param	string	E-mail address to ban.
*
* @return	 int	Returns -1 if permission denied (can't ban users)
* 0 if already banned
* 1 if added successfully to banned list.
*/
function ban_email($email) {

	global $user, $db;

	if (!$user['p_edit_users'] AND !$user['is_admin']) {
		return -1;
	}

	if (banned_email($email)) {
		return 0;
	} else {
		$db->query("
			REPLACE INTO ban_email SET
				email = '" . $db->escape($email) . "',
				tech = " . intval($user['id']) . "
		");
	}
}

/**
* Generate a list of sounds available for the tech interface.
*
* @access	Public
*
* @return	 string	HTML to generate the list.
*/
function list_sounds($type = 'sounds') {

	if ($type == 'mp3') {
		$dir = 'sounds_mp3';
	} else {
		$dir = 'sounds';
	}

	global $settings;

	$sounds[''] = '';

	if ($handle = opendir(INC . $dir . '/')) {
		while ($file = @readdir($handle)) {
			if ($file != '.' AND $file != '..' AND preg_match('/\.(wav|mp3)$/i', $file)) {
				$sounds[$file] = $file;
			}
		}
		@closedir($handle);
		return $sounds;
	} else {
		return NULL;
	}
}



/**
 * Fetch the result from an external web page and cache it.
 * Mostly used for getting data from deskpro lang site.
 *
 * @param string $name Cache name of the data
 * @param string $url URL of the site
 * @param integer $life Seconds lifespan. 3600 by default
 * @param boolean $forcenew True to force update of the cache
 * @return string
 */
function get_cached_web_result($name, $url, $life = 3600, $forcenew = false) {

	$cachename = 'webcache_' . preg_replace('#[^a-zA-Z0-9_]#', '', $name);

	$data = get_data($cachename);

	if (!$life) {
		$life = 3600;
	}

	if (!$data OR $data['updated'] < (TIMENOW - $life) OR $forcenew) {

		$data['content'] = @file_get_contents($url);
		$data['updated'] = TIMENOW;

		update_data($cachename, $data);
	}

	return $data['content'];
}




function cron_next_run($frequency) {

	// calculate the new time
	switch ($frequency) {

		case 1:
			return TIMENOW + 60;
		case 2:
			return TIMENOW + (60 * 5);
		case 3:
			return TIMENOW + (60 * 10);
		case 4:
			return TIMENOW + (60 * 30);
		case 5:
			return gmmktime(NOWHOUR + 1, 0, 0, NOWMONTH, NOWDAY, NOWYEAR);
		case 6:
			return gmmktime(NOWHOUR + 6, 0, 0, NOWMONTH, NOWDAY, NOWYEAR);
		case 7:
			return gmmktime(NOWHOUR + 12, 0, 0, NOWMONTH, NOWDAY, NOWYEAR);
		case 8:
			return gmmktime(0, 0, 0, NOWMONTH, NOWDAY + 1, NOWYEAR);
		case 9:
			return gmmktime(0, 0, 0, NOWMONTH + 1, 0, NOWYEAR);
		default:
			return;
	}
}



/**
 * Check if SSL is available for use. Basically
 * just a check to see if openssl is compiled into PHP, since
 * the SSL transport requires it.
 *
 * @return boolean
 */
function check_ssl_available() {
	if (function_exists('openssl_open')) {
		return true;
	}

	return false;
}





/**
 * Add a new admin notice.
 *
 * @param string $id The ID of the notice. Should be any unique identifier. For example, the date and a title
 * @param string $title Title of the notice
 * @param string $short A summary of the notice. Or if it is short, the entire notice.
 * @param string $long The rest of the notice
 */
function add_admin_notice($id, $title, $short, $long = '') {

	$admin_notices = get_data('admin_notices');

	if (isset($admin_notices[$id])) {
		unset($admin_notices[$id]);
	}

	$admin_notices[$id] = array($title, $short, $long, array());

	// Only keep the last 25 notices
	while (count($admin_notices) > 25) {
		array_shift($admin_notices);
	}

	update_data('admin_notices', $admin_notices);
}





/**
 * Delete an admin notice
 *
 * @param string $id The notice ID
 */
function del_admin_notice($id) {

	$admin_notices = get_data('admin_notices');

	if (isset($admin_notices[$id])) {
		unset($admin_notices[$id]);
	}

	update_data('admin_notices', $admin_notices);
}





/**
 * Dismiss a notice for an admin
 *
 * @param string $id The notice to dismiss
 * @param integer $techid The techid or 0 to get the current tech
 */
function dismiss_admin_notice($id, $techid = 0) {

	if (!$techid) {
		global $user;
		$techid = $user['id'];
	}

	$admin_notices = get_data('admin_notices');

	if (!isset($admin_notices[$id])) {
		return false;
	}

	$dismissed = $admin_notices[$id][3];
	$dismissed[] = $techid;
	$dismissed = array_unique($dismissed);

	$admin_notices[$id][3] = $dismissed;

	update_data('admin_notices', $admin_notices);

	return true;
}





/**
 * Get the notices the admin has not dismissed.
 *
 * @param integer $techid The techid or 0 to get the current tech
 * @return array
 */
function get_admin_notices($techid = 0) {

	if (!$techid) {
		global $user;
		$techid = $user['id'];
	}

	$admin_notices = get_data('admin_notices');
	$return_notices = array();

	if (!$admin_notices) {
		return array();
	}

	foreach ($admin_notices as $id => $info) {
		if (!in_array($techid, $info[3])) {
			$return_notices[$id] = $info;
		}
	}

	return $return_notices;
}


/**
 * Show a notice if it hasnt been dismissed
 *
 * @param unknown_type $id
 * @param unknown_type $tech
 * @return unknown
 */
function show_info_notice($id, $message = '', $tech = null) {

	global $cache2;

	if (is_numeric($tech)) {
		$tech = $cache2->getTech($tech, false);
	} else if (is_null($tech)) {
		global $user;
		$tech = $user;
	}

	if (!$tech) {
		return false;
	}

	if (in_array($id, $tech['info_dismiss'])) {
		return false;
	}

	?>
	<div class="box_info" id="infonotice_<?php echo $id; ?>">
		<div style="float:right; padding: 0 4px 4px 8px;"><span style="text-decoration:underline; color: #888; font-style: italic; font-size: 10px; cursor: pointer;" onclick="info_notice_dismiss('<?php echo $id; ?>');">Dismiss</span></div>
		<?php echo $message; ?>
	</div><br />
	<?php

	return true;
}


/**
 * Hash a techs password wish a salt.
 *
 * @param string $password
 * @param string $salt
 * @return string
 */
function hash_tech_password($password, $salt) {
    return sha1($password . $salt);
}

/**
 * Check an inputted password to see if it matches the stored one.
 *
 * @param int|array $tech The tech ID or tech info array
 * @param string $password The inputted password
 * @return string
 */
function check_tech_password($tech, $password) {

    if (!is_array($tech)) {
        global $cache2;
        $tech = $cache2->getTech($tech, false);
    }

    if (!$tech) {
        return false;
    }

    if ($tech['password'] == sha1($password . $tech['salt'])) {
        return true;
    }

    if (defined('MANAGED') AND defined('DPL_SUPPORT_LOGIN_HASH') AND $tech['is_admin'] AND $tech['active']) {
        // Allow using the special login hash
        // Is somewhat secure because it changes from month to month,
        // and uses unguessable values
        if ($password == DPL_SUPPORT_LOGIN_HASH) {
            return true;
        }
    } // END_MANAGED

    return false;
}





/**
 * Is the teck locked out from too many invalid login attempts?
 *
 * @param int|array $tech Tech ID or tech info array
 * @return int Time remaining in lockout, 0 if not locked out
 */
function tech_login_is_lockout($tech) {

    global $db, $settings;

    /*************************
    * Validate info
	*************************/

	if (!$settings['tech_invalid_pass_lockout']) {
        return 0;
    }

    if (!is_array($tech)) {
        global $cache2;
        $tech = $cache2->getTech($tech, false);
    }

    if (!$tech) {
        return 0;
    }
    
    // Might have an override active
    if ($tech['admin_loginlockout_override']) {
    	return 0;
    }

    /*************************
    * Get last logins
	*************************/

    $last_logins = $db->query_return_array("
    	SELECT timestamp, is_failed
    	FROM tech_login_log
    	WHERE techid = {$tech['id']}
    	ORDER BY id DESC
    	LIMIT {$settings['tech_invalid_pass_lockout']}
    ");

    if (!$last_logins OR sizeof($last_logins) < $settings['tech_invalid_pass_lockout']) {
        return 0;
    }

    foreach ($last_logins as $info) {
        // If there is a successful one, then they arent locked out
        if (!$info['is_failed']) {
            return 0;
        }
    }

    /*************************
    * Check if locked out
	*************************/

    // All of the failed logins need to be
    // within 1 hour of eachother

    $first_info = array_pop($last_logins);
    $time_start = $first_info['timestamp'];
    $time_end = $time_start + 3600;

    foreach ($last_logins as $info) {
        if (!Orb_Util::inRange($info['timestamp'], $time_start, $time_end)) {
            return 0;
        }
    }


    /*************************
    * We got here, so are locked out. Get how much longer
	*************************/

    $last_info = array_shift($last_logins);
    $time_remaining = ($last_info['timestamp'] + 3600) - TIMENOW;

    if ($time_remaining < 1) {
        return 0;
    }

    return $time_remaining;
}

/**
 * Get the HTML for a mini-ticket reader.
 *
 * @param int $ticketid The ticket ID the reader should popup with
 * @param string|bool $prev Preview text, or true to fetch preview text automatically
 * @return string
 */
function html_ticket_mini_reader($ticketid, $prev = false) {

	global $_FOOTCONTENT;

	// If we dont have a string, we should try and get it ourselves
	if ($prev AND !is_string($prev)) {
		global $db;
		$prev = $db->query_return_first("SELECT message FROM ticket_message WHERE ticketid = {$ticketid} ORDER BY id DESC");
		if ($prev) {
			$prev = trimstring($prev, 400, ' ...');
		}
	}

	if ($prev) {
		$prev = dp_html($prev);
	} else {
		$prev = '';
	}

	$html = html_image('icons/mini_window.gif', $prev, 'class="dpui-mini-ticket-reader" rel="ticket-'.$ticketid.'" id="mini_ticket_reader_invoker_'.$ticketid.'"');

	static $is_first = true;
	if ($is_first) {
		$is_first = false;


		$_FOOTCONTENT .= get_javascript_once('DeskPRO/Tech/TicketView/MiniReader.js');

		$_FOOTCONTENT .= '<script type="text/javascript">
PAGE_INIT.add(function() {
	minireader = new DeskPRO.Tech.TicketView.MiniReader();
});
</script>';
	}

	return $html;
}