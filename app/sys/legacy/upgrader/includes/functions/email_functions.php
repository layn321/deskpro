<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);



/**
* @access	public

* @param	array	details of user that just registered
* @param	bool	does the user need to be validated

* @return	bool
*/
function send_tech_email_user_new($user_details, $awaiting_tech_validation) {

	global $db, $cache2;

	/**********************
	* Email awaiting validation techs
	**********************/

	$techs = $cache2->getTechs(1);

	if ($awaiting_tech_validation) {

		foreach ($techs AS $tech_details) {

			if ($tech_details['email_user_registered_validation'] AND $tech_details['p_approve_new_registrations']) {
				send_tech_email('newuser_validate', $tech_details, array('user_details' => $user_details));
			}
		}

	} else {

		if ($tech_details['email_user_registered']) {
			send_tech_email('newuser', $tech_details, array('user_details' => $user_details));
		}
	}
}

/**
* raw function for sending an email. It is a wrapper to the phpmailer class
* generally this function should be called by other functions, e.g. send_user_email(), notify_user(), send_tech_email() etc.
*
* @access	public
*
* @param	string	email to send to
* @param	string	name of person being sent email
* @param	string	email subject
* @param	string	email message
* @param	string	from_email address
* @param	string	from_name
* @param	array	array of attachments to add to email
* @param	string	charset of email
* @param	string	return email address
* @param    array   $extraheaders Any extra headers to add. Key=>value
*
* @return	bool True on success false on error
*/
function dp_mail($to_email, $to_name='', $subject, $message, $from_email='', $from_name='', $attachments='', $charset='', $return='', $extraheaders = array(), &$error_msg = null) {

	$debug_fields = func_get_args();

	require_once(INC . '3rdparty/phpmailer/class.phpmailer.php');
	require_once(INC . 'classes/class_DpMail.php');

	global $settings, $dplang;

	// Make sure it's only a single line
	$to_email = get_first_line($to_email);
	$to_name = get_first_line($to_name);
	$subject = get_first_line($subject);
	$from_email = get_first_line($from_email);
	$from_name = get_first_line($from_name);
	$charset = get_first_line($charset);

	// set username as email if we have no name
	if (trim($to_name) == '') {
		$to_name = $to_email;
	}

	$mail = new DpMail($settings);

	// Leave wrapping up to the client
	$mail->WordWrap = -1;

	// override the default from name
	if (trim($from_name) != '') {
		$mail->FromName = trim($from_name);
	}
	// override the default from email
	if (trim($from_email) != '') {
		$mail->From = trim($from_email);
	}

	$mail->LE = get_line_ending();

	// using english; but have browser submited UTF-8 characters, convert to UTF-8
	if ((strtolower($charset) == 'iso-8859-1' OR !$charset) AND preg_match('/&#\d+;/', $message)) {
		$message = utf8_encode($message);
		$subject = utf8_encode($subject);
		$charset = 'UTF-8';
		$message = un_dp_html($message);
	}

	// charset
	if (trim($charset) != '') {
		$mail->CharSet = trim($charset);
	}

	// valid email
	if (!is_email(trim($to_email))) {
		$mail->error_handler("Email address <cite><strong>$to_email</strong></cite> is not valid", $debug_fields);
		return;
	}

	$mail->AddAddress($to_email, $to_name);

	// subject
	$mail->Subject = trim($subject);

	// set the return email address
	if (trim($return) != '') {
		$mail->Sender = trim($return);
	}

	// attachments
	if (is_array($attachments)) {
		foreach ($attachments AS $key => $var) {

			if (!$var['filename']) {
				trigger_error("No filename provided for attachment");
			}

			// get_mimetype($var['extension']
			$mail->AddStringAttachment($var['data'], $var['filename'], 'base64');
		}
	}

	// any extra headers
	if (is_array($extraheaders)) {
		foreach ($extraheaders AS $key => $var) {
			if (strpos($key, 'X-debug') !== false AND !defined(DESKPRO_DEBUG_DEVELOPERMODE)) {
				continue;
			}
			$mail->AddCustomHeader("$key:$var");
		}
	}

	if (defined('GATEWAYZONE') OR defined('CRONZONE')) {

		if ($cwd = @getcwd()) {
			$filepath = $cwd;
		} else {
			$filepath = @realpath(@dirname(__FILE__));
		}

	} else {
		$filepath = PATH;
	}

	if (!defined('DESKPRO_DEBUG_DEVELOPERMODE') AND !isset($extraheaders['X-debug-filepath'])) {
		$filepath = simple_encrypt(PATH);
	}

	$mail->AddCustomHeader("X-debug-filepath:$filepath");

	// message
	$mail->Body = $message;

	// output mode
	if (defined('DESKPRO_DEBUG_EMAIL_OUTPUT')) {

		// Tech/admin logins can send login notification emails,
		// this output will stop a login (headers already sent)
		if ((!$GLOBALS['session'] OR !$GLOBALS['session']->newsession) AND !defined('IS_AJAX')) {
			echo "<input type=\"text\" style=\"width:90%\" value=\"" . dp_html($to_email) . " :: " . dp_html($to_name) . "\"><br />";
			echo "<input type=\"text\" style=\"width:90%\" value=\"".dp_html($subject)."\"><br />";
			if ($attachments) {
				$alist = array();
				foreach ($attachments as $a) {
					$alist[] = "{$a['filename']} (" . strlen($a['data']) . " B)";
				}
				echo "<input type=\"text\" style=\"width:90%\" value=\"".dp_html(implode(', ', $alist))."\"><br />";
			}
			echo "<textarea style=\"width:90%; height:250px\">" . dp_html($message) . "</textarea><br /><br /><br />";
		}

	// do debug mode
	} else if (defined('DESKPRO_DEBUG_EMAIL')) {

		$res = $mail->Send();
		print__rr($mail);

		if (!$res) {
			return false;
		}

	// send & handle error
	} else {

		if (!$mail->Send()) {

			if (!defined('DESKPRO_NOLOG_EMAIL_ERR') AND !defined('INSTALLER')) {
				$msg = $mail->error_handler('', $debug_fields);

				global $db;

				// Log the failed email
				if (!defined('DESKPRO_EMAIL_ERR_NORETRY') AND is_object($db)) {

					$data = serialize(func_get_args());

					$db->query("
						INSERT INTO failed_email
						SET timestamp = " . TIMENOW . ", failcount = 1
					");

					$failed_email_id = $db->insert_id();

					$max_packet = floor($db->max_allowed_packet() / 2);
					$length = strlen($data);
					$repeat = ceil($length / $max_packet);

					for ($i = 0; $i < $repeat; $i++) {
					    $db->query("
					        INSERT INTO failed_email_part SET
					            failed_email_id = " . intval($failed_email_id) . ",
					            data = '" . $db->escape(substr($data, $i * $max_packet, $max_packet)) . "'
					    ");
					}
				}
			}

			if ($error_msg) {
				$error_msg = ($mail->ErrorInfo ? $mail->ErrorInfo : 'Unknown Error');
			}

			return false;
		}
	}

	return true;
}


/**
* send email to technician email address
*
* @access	public
*
* @param	string	email template name
* @param	string|array	details of technician
* @param	string
* @param	string	contents of attachment
* @param	string	details of sender
*/
function send_tech_email($template_name, $tech_details, $variables='', $attachmentblobs='', $from='', $noheader = false, $charset = '') {

	global $settings, $cache, $cache2;

	// if not array, just send directly to email address
	if (!is_array($tech_details)) {

		// dealing with user id
		if (is_numeric($tech_details)) {

			$tech_details = $cache2->getTech($tech_details);

		// being passed the email address
		} else {
			$tech_details = array('email' => $tech_details);
		}
	}

	$email = $tech_details['email'];

	// Give charset from options if none specified
	if (!$charset) {
		$charset = 'utf-8';
	}

	$DP_NAME = DP_NAME;

	// extract variables we have passed
	if (is_array($variables)) {
		extract($variables, EXTR_SKIP);
	}

	// get tech information
	$tech_details = get_tech_info($tech_details);

	// get the template and parse it
	$template = get_template_email_tech($template_name);

	if (!$template['body']) {
		trigger_error("Template $template_name not found");
		return NULL;
	}

	eval('$subject = ' . $template['subject'] . ';');
	eval('$body = ' . str_replace('<%line%>', get_line_ending(), $template['body']) . ';');

	// parses the footer
	if ($settings['email_footer_user'] AND !$noheader) {
		$footer = get_template_email_tech('footer');
		eval('$body .= ' . str_replace('<%line%>', get_line_ending(), $footer['body']) . ';');
	}

	$extraheaders = array('X-debug-template-name' => preg_replace("#\n#", '\\n', $template_name));

	dp_mail($email, $tech_details['greeting'], $subject, $body, $from['email'], $from['name'], $attachmentblobs, $charset, '', $extraheaders);

}

/**
*	Important varialbes in $user_details array:
*		a) email [required]
*		b) language [reverts to default]
*		c) username/name [falls back on email]
*	- A wrapper function to send emails to users
*	- Based on emails sent using templates
*	- Relies on the $user_details array, this ['email'] for that array will be where the email is sent.
*	- Handles automatically from
*	- Handles working out the language words to eval with (ie $dplang)
*	- Variables need to be passed if they are to be used in the template
*	- Emails are then sent to base function, dp_mail();
*	- used in user/tech zone
*
* @access	public
*
* @param	string	the template to use
* @param	string|array	array of user details, or just an email
* @param	array	array of variables for replacement
* @param	array		array of attachments
*
* @return	bool
*/
function send_user_email($template_name, $user_details, $variables='', $attachments='') {

	global $settings, $cache2;

	// if not array, just send directly to email address
	if (!is_array($user_details)) {

		// dealing with user id
		if (is_numeric($user_details)) {

			$db =& database_object_factory();
			$user_details = user_from_field('id', $user_details);

		// being passed the email address
		} else {
			$user_details = array('email' => $user_details);
		}
	}

	// get more details
	$user_details = get_user_info($user_details);

	// sort language
	if (!$language = $user_details['language']) {
		$language = $cache2->getDefaultLanguageID();
	}

	// get the template and parse it
	$template = parse_user_email($template_name, $variables, $user_details, $language);

	$extraheaders = array('X-debug-template-name' => preg_replace("#\n#", '\\n', $template_name));

	dp_mail(
		$user_details['email'],
		$user_details['greeting'],
		$template['subject'],
		$template['body'],
		'',
		'',
		$attachments,
		get_charset($language),
		'',
		$extraheaders
	);
}

function parse_user_email($template_name, $variables, $user_details='', $language='') {

	global $settings, $cache2;

	$user_details = get_user_info($user_details);

	if (!$language) {
		$language = $cache2->getDefaultLanguageID();
	}

	$DP_NAME = DP_NAME;

	// extract variables we have passed
	if (is_array($variables)) {
		extract($variables, EXTR_SKIP);
	}

	$dplang = $cache2->getWords($language);

	$template = get_template_email_user($template_name);

	eval('$subject = ' . $template['subject'] . ';');
	eval('$body = ' . str_replace('<%line%>', get_line_ending(), $template['body']) . ';');

	// parses the footer
	if ($settings['email_footer_user']) {
		$footer = get_template_email_user('footer');
		eval('$body .= ' . str_replace('<%line%>', get_line_ending(), $footer['body']) . ';');
	}

	return array('subject' => $subject, 'body' => $body);

}

###############################################################################################
####################################   DATA PROCESSOR FUNCTIONS  ######################################
###############################################################################################

/**
* Gets the line breaks that should be used for email sending
*
* @access	public
*
* @return	string	The line break
*/
function get_line_ending() {

	global $settings;
	static $ending;

	if (!isset($ending)) {

		$linebreaks = array(
			'unix' => "\n",
			'windows' => "\r\n",
			'mac' => "\r"
		);

		if (!($ending = $linebreaks[strtolower($settings['email_linereturns'])])) {
			$ending = "\n";
		}
	}

	return $ending;
}

/**
* get details of ticket
*
* @access	public
*
* @param	int	id of ticket
* @param	string	type of ticket
* @param	array
*
* @return	string|array	details of ticket
*/
function get_ticket_info($ticket, $type, $user_details='') {

	global $db, $cache2;

	// if we already have a field set; don't bother
	if ($ticket['category_name'] OR $ticket['priority_name'] OR $ticket['workflow_name'] OR $ticket['tech_name']) {
		$ticket['subject'] = un_dp_html($ticket['subject']);
		return $ticket;
	}

	if (!is_array($ticket)) {
		$ticket = $cache2->getTicketId($ticket);
		if (!is_array($ticket)) {
			return;
		}
	}

	/****
	* Tech just get data
	****/

	if ($type == 'tech') {

		// category
		$ticket['category_name'] = $cache2->categoryName($ticket['category'], true, true, ' > ');

		// priority
		$ticket['priority_name'] = $cache2->priorityName($ticket['priority']);

	/****
	* Deal with language issues and permissions
	****/

	} elseif ($type == 'user') {

		// category
		$categories = user_category_array('view', $ticket['category'], true, $user_details['language'], true, -1);

		$cat = $this->getCategory($ticket['category']);
		if ($cat AND $cat['parent']) {
			$ticket['category_name'] = $categories[$cat['parent']] . ' > ' . $categories[$ticket['category']];
		} else {
			$ticket['category_name'] = $categories[$ticket['category']];
		}

		// priority
		$priorities = user_priority_array('view', $ticket['priority'], true, $user_details['language']);
		$ticket['priority_name'] = $priorities[$ticket['priority']];

	}

	/****
	* Standard for user/tech
	****/

	$ticket['workflow_name'] = $cache2->workflowName($ticket['workflow']);
	$ticket['tech_name'] = $cache2->techName($ticket['tech']);

	// custom fields
	$cache2->getTicketFields();

	if (is_array($cache2->ticketfields)) {
		foreach ($cache2->ticketfields AS $result) {

			$custom_field = new customFieldDisplay($result, $ticket[$result['name']]);
			$ticket[$result['name'] . '_value'] = $custom_field->buildText();

		}
	}

	// If it was tech created, get the tech creator name and id
	if ($ticket['tech_creator']) {
		$ticket['tech_creator_name'] = $cache2->getTechName($ticket['tech_creator'], false);
	}

	$ticket['subject'] = un_dp_html($ticket['subject']);
	return $ticket;

}

/**
* get details of a user
*
* @access	public
*
* @param	int	id of user
*
* @return	 string|array	details of user
*/
function get_user_info($user_details) {

	global $db, $cache2;

	if ($user_details['greeting']) {
		return $user_details;
	}

	if (!is_array($user_details)) {
		$user_details = user_from_field('id', $user_details);
		if (!is_array($user_details)) {
			return;
		}
	}

	/****
	* Greeting
	****/

	$user_details['greeting'] = ifr($user_details['name'], ifr($user_details['username'], $user_details['email']));

	/****
	* Custom Fields
	****/

	$cache2->getUserFields();

	if (is_array($cache2->userfields)) {
		foreach ($cache2->userfields AS $result) {

			$custom_field = new customFieldDisplay($result, $user_details[$result['name']]);
			$user_details[$result['name'] . '_value'] = $custom_field->buildText();

		}
	}

	return $user_details;

}

/**
* get details of a technician
*
* @access	public
*
* @param	int	id of technician
*
* @return	 string|array	details of technician
*/
function get_tech_info($tech_details) {

	/****
	* Greeting
	****/

	$tech_details['greeting'] = ifr($tech_details['name'], ifr($tech_details['username'], $tech_details['email']));

	return $tech_details;

}

/***************************************
- This class creates and sends emails specific to a particular ticket to techs
- It takes account of tickets that techs should not be notified for
***************************************/

class sendTechTicketEmail {

	/**
	* the template name
	* @var array
	* @access private
	*/
	var $template_name;

	/**
	* array of subject/message
	* @var array
	* @access private
	*/
	var $template;

	/**
	* ticket array
	* @var array
	* @access private
	*/
	var $ticket;

	/**
	 * Charset to send mail in (dependant on ticket lang)
	 * @var string
	 * @access private
	 */
	var $charset;

	/**
	* user array
	* @var array
	* @access private
	*/
	var $user_details;

	/**
	* message by tech
	* @var array
	* @access private
	*/
	var $tech_message;

	/**
	* message by user
	* @var array
	* @access private
	*/
	var $user_message;

	/**
	* any error that would prevent sending the email
	* @var string
	* @access private
	*/
	var $error;

	/**
	 * Array of custom fields for this ticket
	 * @var array
	 * @access protected
	 */
	var $ticketfields;




	/**
	 * Constructor: set up this notification sender
	 *
	 * @param array|int $ticket Array of ticket details, or ticket id
	 * @param array|int $user_details Array of user details, or user id
	 * @return sendTechTicketEmail
	 */
	function sendTechTicketEmail($ticket, $user_details) {

		global $db, $cache2, $settings;

		/*****************
		* Get user details
		*****************/

		if (!is_array($user_details)) {
			$user_details = user_from_field('id', $user_details);
		}

		$this->user_details = get_user_info($user_details);

		/*****************
		* Get ticket
		*****************/

		$this->ticket = get_ticket_info($ticket, 'tech');

		$this->ticketfields = array();

		if ($settings['email_ticketfields_tech']) {
			$catfields = $cache2->getTicketFieldsCat($this->ticket['category']);

			if ($catfields) {
				foreach ($catfields as $fieldname => $field) {

					$customfield = new customFieldDisplay($field, $this->ticket[$fieldname]);
					$customfield_language = $customfield->getLanguage();

					$value = trim($customfield->buildPlaintext());

					$this->ticketfields[$field['id']] = array(
						'name' => $result['name'],
						'display_name' => $customfield_language['display_name'],
						'description' => $customfield_language['description'],
						'value' => $value,
					);
				}
			}
		}

		/*****************
		* Get charset
		*****************/

		if ($this->ticket['language']) {
			$lang = $cache2->getLanguage($this->ticket['language']);
		}

		if (!$lang) {
			$lang = $cache2->getLanguage($cache2->getDefaultLanguageID());
		}

		$this->charset = $lang['contenttype'];

	}





	/**
	 * Check if any emails should be sent. Users awaiting validations shouldn't produce
	 * notifications for techs.
	 *
	 * @return bool True to send notifications, false not to
	 */
	function isValid() {

		// Don't notify for tickets from non-validated users
		if ($this->user_details['awaiting_register_validate_user'] OR $this->user_details['awaiting_register_validate_tech']) {
			return false;
		}

		return true;

	}





	/**
	 * Get the techs to notify for newticket or newreply.
	 *
	 * Returned array in the form of:
	 * <code>array( techid => array(email => true, sms => false) )</code>
	 * So, keyed by techid and has boolean email/sms items.
	 *
	 * @param string $type Kind of notificaiton: 'newticket' or 'newreply'
	 * @param array $explicit An array of techids to explicitly set, in addition to those that would be fetched anyway
	 * @return array An array of techs who need notifying
	 */
	function getTechsNotify($type = 'newticket', $explicit = array()) {

		global $db, $cache2;

		$active_techs = $cache2->getTechs(false);

		if (!$active_techs OR !in_array($type, array('newticket', 'newreply'))) {
			return array();
		}

		$notify_techs = array();



		/*************************
		* If ticket is assigned, check tech options
		*************************/

		if ($this->ticket['tech']) {
			$tech = $active_techs[$this->ticket['tech']];

			if ($tech AND ($type == 'newticket' AND $tech['email_assigned']) OR ($type == 'newreply' AND $tech['email_own_email'])) {
				$notify_techs[$tech['id']]['email'] = true;
			}
		}


		/*************************
		* Check 'all tickets' settings for techs
		*************************/

		foreach ($active_techs as $tech) {
			if (($type == 'newticket' AND $tech['email_new_email']) OR ($type == 'newreply' AND $tech['email_reply_email']) OR in_array($tech['id'], $explicit)) {
				$notify_techs[$tech['id']]['email'] = true;
			}
		}

		/*************************
		* Check based on participants
		*************************/

		if ($type == 'newreply') {
		    foreach ($cache2->getTicketParticipants($this->ticket, 'tech') as $tech_details) {
		        if ($tech_details['active']  AND $tech_details['email_reply_participant']) {
		            $notify_techs[$tech_details['id']]['email'] = true;
		        }
		    }
		}


		/*************************
		* Get other notify for specific cat/pri/lang
		*************************/

		$active_tech_ids = array2sql(array_keys($active_techs));

		$db->query("
			SELECT
				techid, email, sms
			FROM tech_email
			WHERE
				techid IN $active_tech_ids
				AND $type = 1
				AND	(
					(fieldname = 'category' AND value = {$this->ticket['category']}) OR
					(fieldname = 'priority' AND value = {$this->ticket['priority']}) OR
					(fieldname = 'language' AND value = {$this->ticket['language']})
				)
		");

		while ($tech = $db->row_array()) {
			$notify_techs[$tech['techid']]['email'] = (bool)$tech['email'];
			$notify_techs[$tech['techid']]['sms'] = (bool)$tech['sms'];
		}

		/*************************
		* Remove based on ignored/permissions
		*************************/

		foreach (array_keys($notify_techs) as $techid) {
			$tech = $cache2->getTech($techid);
			if (in_array($this->ticket['category'], $tech['cats_admin_array']) OR in_array($this->ticket['category'], $tech['cats_user_array'])) {
				unset($notify_techs[$techid]);
			}
		}

		return $notify_techs;
	}





	/**
 	 * set the attachments
	 * @var	array	array of attachment ids
	 * @access	public
	 */
	function setAttachments($attachmentids) {

		global $db, $settings;

		if (!is_array($attachmentids) OR empty($attachmentids)) {
			return;
		}

		$db->query("
			SELECT * FROM ticket_attachments WHERE id IN " . array2sql($attachmentids)
		);

		while ($result = $db->row_array()) {
			$result['filesize_display'] = filesize_display($result['filesize']);
			$this->attachments[] = $result;
		}
	}





	/**
	 * Get the maximum attachment size in displayable format. Will
	 * also check if the tech wants attachments emailed in the first place.
	 *
	 * If null is returned, then the tech doesn't want attachments.
	 *
	 * @param array $tech_details The tech details
	 * @param string $check Which option to check for: email_attachments or email_own_attachments
	 * @return string The displayable max filesize
	 */
	function attachmentSize($tech_details, $check) {

		global $settings;

		if (!is_array($this->attachments)) {
			return;
		}

		// check the tech actually wants attachments emailed
		if (!$tech_details[$check]) {
			return;
		}

		return filesize_display($settings['attachment_max_tech_email'] * 1024 * 1025);

	}





	/**
	 * Get the attachment data.Will
	 * also check if the tech wants attachments emailed in the first place.
	 *
	 * Returned array is in the format of:
	 * <code>array( array(filename => img.gif, data => *data*) )</code>
	 *
	 * If null is returned, then the tech doesn't want attachments.
	 *
	 * @param array $tech_details The tech details
	 * @param string $check Which option to check for: email_attachments or email_own_attachments
	 * @return array The array of blobs
	 */
	function getBlobs($tech_details, $check) {

		global $cache2, $settings;

		if (!is_array($this->attachments)) {
			return;
		}

		// check the tech actually wants attachments emailed
		if (!$tech_details[$check]) {
			return;
		}

		foreach ($this->attachments AS $array) {

			// breach tech limit?
			if ($array['filesize'] > ($settings['attachment_max_tech_email'] * 1024 * 1024)) {
				continue;
			}

			$blobs[] = array(
				'filename' => $array['filename'],
				'data' => get_attachment_blob($array['blobid'])
			);
		}

		return $blobs;
	}




	/**
	 * Get the header text of the email where techs can manage tickets
	 * via email (reply, take ownership and close tickets).
	 *
	 * If the tech gateway is disabled, null is returned.
	 *
	 * @param array $tech Array of tech details
	 * @param string $tplname Name of the template. If ends in _short, then will use short header
	 * @return string The email header
	 */
	function replyHeader($tech, $tplname = '') {

	    if (Orb_String::endsWith('_short', $tplname)) {
	        return $this->replyHeaderShort($tech);
	    }

		global $settings;

		if (!$settings['gateway_tech_on']) {
			return;
		}

		$header = "<CLOSE TICKET>No</CLOSE TICKET>\n";

		// only show this if they don't already own it
		if ($tech['id'] != $ticket['tech']) {
			$header .= "<TAKE OWNERSHIP>Yes</TAKE OWNERSHIP>\n";
			$header .= "\n";
		}

		$header .="=== Enter your reply below this line ===\n";
		$header .= "\n";
		$header .= "=== Enter your reply above this line ===\n";

		return $header;
	}





	/**
	 * Get the short header text for emails. This is simply the message
	 * 'ente rreply above this line'.
	 *
	 * If the tech geteway is disabled, nill is reutned
	 *
	 * @param array $tech Array of tech details
	 * @return string The email header
	 */
	function replyHeaderShort($tech) {

		global $settings;

		if (!$settings['gateway_tech_on']) {
			return;
		}

		$header .= "=== Enter your reply above this line ===";

		return $header;

	}





	/**
	 * Returns the 'from' email address for the notification.
	 *
	 * @return string The from email address
	 */
	function getFrom() {

		global $settings;

		if ($settings['gateway_tech_on']) {
			return array('email' => $settings['gateway_tech_email']);
		}

	}





	/**
	 * Get the subject code.
	 *
	 * The format is: 1111-XXXX-1111-techid-md5(password,ticketauthcode)8
	 *
	 * @param array $tech Tech details
	 * @return string The subject codes
	 */
	function subjectCodes($tech) {
		return '[' . $this->ticket['ref'] . '-' . $tech['id'] . '-' . substr(md5($tech['password'] . $this->ticket['authcode']), 0, 8) . ']';
	}





	/**
	 * Send notifications for new tickets
	 *
	 * @param string $user_message
	 * @param string $tech_message
	 * @param array $exclude_ids Array of techids to exclude form sending to
	 */
	function SendUserNew($user_message_info, $tech_message_info = '', $exclude_ids = array()) {

		global $db, $cache2;

		// ticket is nodisplay
		if (!$this->isValid()) {
			return;
		}

		// Get messages
		if (!is_array($user_message_info)) {
			$user_message_info = $db->query_return("
				SELECT *
				FROM ticket_message
				WHERE id = " . intval($user_message_info)
			);
		}

		if (!is_array($tech_message_info) AND $tech_message_info) {
			$tech_message_info = $db->query_return("
				SELECT *
				FROM ticket_message
				WHERE id = " . intval($tech_message_info)
			);
		}

		$user_message = $user_message_info['message'];
		$tech_message = $tech_message_info['message'];

		$sent_email_techids = array();
		$sent_email_techids_short = array();

		$notify_techs = $this->getTechsNotify('newticket');

		if (!$notify_techs) {
			return;
		}

		foreach ($notify_techs as $techid => $send_what) {

			if ($exclude_ids AND in_array($techid, $exclude_ids)) {
				continue;
			}

			$tech_details = $cache2->getTech($techid);

			// check permission
			if (!p_ticket('view', $this->ticket, $tech_details)) {
				continue;
			}

			$attach_check = ($this->ticket['tech'] == $techid ? 'email_own_attachments' : 'email_attachments');

			if ($send_what['email']) {
				$sent_email_techids[] = $tech_details['id'];

				$tplname = $this->getTemplateName($tech_details, 'question_user_new', 'primary');

				send_tech_email(
					$tplname,
					$tech_details,
					array(
						'subject_codes' => $this->subjectCodes($tech_details),
						'reply_header' => $this->replyHeader($tech_details, $tplname),
						'attachment_size' => $this->attachmentSize($tech_details, $attach_check),
						'ticket' => $this->ticket,
						'ticketfields' => $this->ticketfields,
						'attachments' => $this->attachments,
						'user_details' => $this->user_details,
						'user_message' => $user_message,
						'tech_message' => $tech_message,
						'user_message_info' => $user_message_info,
						'tech_message_info' => $tech_message_info,
					),
					$this->getBlobs($tech_details, $attach_check),
					$this->getFrom(),
					false,
					$this->charset
				);

				// Send secondary email
				if ($tech_details['email_secondary']) {

				    $tplname = $this->getTemplateName($tech_details, 'question_user_new', 'secondary');

					$sent_email_techids_short[] = $tech_details['id'];

					// tmp fake email
					$tech_details['email'] = $tech_details['email_secondary'];

					send_tech_email(
						$tplname,
						$tech_details,
						array(
							'subject_codes' => $this->subjectCodes($tech_details),
							'reply_header' => $this->replyHeader($tech_details, $tplname),
							'attachment_size' => $this->attachmentSize($tech_details, 'email_attachments'),
							'ticket' => $this->ticket,
							'attachments' => $this->attachments,
							'user_details' => $this->user_details,
							'user_message' => $user_message,
							'tech_message' => $tech_message
						),
						FALSE,
						$this->getFrom(),
						TRUE,
						$this->charset
					);
				}
			}
		}

		// ticketlog send user emails
		if ($sent_email_techids) {
			ticketlog($this->ticket['id'], 'email_tech', NULL, NULL, 'question_user_new', NULL, $sent_email_techids);
		}

		if ($sent_email_techids_short) {
			ticketlog($this->ticket['id'], 'email_tech', NULL, NULL, 'question_user_new_short', NULL, $sent_email_techids_short);
		}
	}



	/**
	 * Send notifications for tech notes (uses same options as user replies).
	 *
	 * @param string $user_message
	 */
	function SendTechNote($note, $tech, $explicit = array()) {

		global $db, $cache2;

		$sent_email_techids = array();
		$sent_email_techids_short = array();

		// we use these options, but only for techs that have the setting enable
		$notify_techs = $this->getTechsNotify('newreply', $explicit);

		if (!$notify_techs) {
			return;
		}

		foreach ($notify_techs as $techid => $send_what) {

			$tech_details = $cache2->getTech($techid);

			// check they get notes sent to them
			if (!$tech_details['email_note'] AND !in_array($techid, $explicit)) {
				continue;
			}

			// make sure we're not sending to same tech that made the note
			if ($techid == $tech['id']) {
				continue;
			}

			// check permission
			if (!p_ticket('view', $this->ticket, $tech_details)) {
				continue;
			}

			if ($send_what['email']) {
				$sent_email_techids[] = $tech_details['id'];

				$tplname = $this->getTemplateName($tech_details, 'question_tech_note', 'primary');

				send_tech_email(
					$tplname,
					$tech_details,
					array(
						'subject_codes' => $this->subjectCodes($tech_details),
						'reply_header' => $this->replyHeader($tech_details, $tplname),
						'ticket' => $this->ticket,
						'user_details' => $this->user_details,
						'tech' => $tech,
						'note' => $note,
					),
					$this->getBlobs($tech_details, $attach_check),
					$this->getFrom(),
					false,
					$this->charset
				);

				// Send secondary email
				if ($tech_details['email_secondary']) {

				    $tplname = $this->getTemplateName($tech_details, 'question_tech_note', 'secondary');

					$sent_email_techids_short[] = $tech_details['id'];

					// tmp fake email
					$tech_details['email'] = $tech_details['email_secondary'];

					send_tech_email(
						$tplname,
						$tech_details,
						array(
							'charset' => $this->charset,
							'subject_codes' => $this->subjectCodes($tech_details),
							'reply_header' => $this->replyHeader($tech_details, $tplname),
							'ticket' => $this->ticket,
							'tech' => $tech,
							'user_details' => $this->user_details,
							'note' => $note
						),
						FALSE,
						$this->getFrom(),
						TRUE,
						$this->charset
					);
				}
			}
		}

		// ticketlog send user emails
		if (is_array($sent_email_userids)) {
			ticketlog($this->ticket['id'], 'email_tech', NULL, NULL, 'question_tech_note', NULL, $sent_email_techids);
		}

		if (is_array($sent_email_userids_short)) {
			ticketlog($this->ticket['id'], 'email_tech', NULL, NULL, 'question_tech_note_short', NULL, $sent_email_techids_short);
		}
	}



	/**
	 * Send notifications for user replies.
	 *
	 * @param string $user_message
	 */
	function SendUserReply($user_message_info) {

		global $db, $cache2;

		// Get messages
		if (!is_array($user_message_info)) {
			$user_message_info = $db->query_return("
				SELECT *
				FROM ticket_message
				WHERE id = " . intval($user_message_info)
			);
		}
		$user_message = $user_message_info['message'];

		$sent_email_techids = array();
		$sent_email_techids_short = array();

		$notify_techs = $this->getTechsNotify('newreply');

		if (!$notify_techs) {
			return;
		}

		foreach ($notify_techs as $techid => $send_what) {
			$tech_details = $cache2->getTech($techid);

			// check permission
			if (!p_ticket('view', $this->ticket, $tech_details)) {
				continue;
			}

			$attach_check = ($this->ticket['tech'] == $techid ? 'email_own_attachments' : 'email_attachments');

			if ($send_what['email']) {
				$sent_email_techids[] = $tech_details['id'];

				$tplname = $this->getTemplateName($tech_details, 'question_user_reply', 'primary');

				send_tech_email(
					$tplname,
					$tech_details,
					array(
						'subject_codes' => $this->subjectCodes($tech_details),
						'reply_header' => $this->replyHeader($tech_details, $tplname),
						'attachment_size' => $this->attachmentSize($tech_details, $attach_check),
						'ticket' => $this->ticket,
						'ticketfields' => $this->ticketfields,
						'user_details' => $this->user_details,
						'attachments' => $this->attachments,
						'user_message' => $user_message,
						'user_message_info' => $user_message_info,
					),
					$this->getBlobs($tech_details, $attach_check),
					$this->getFrom(),
					false,
					$this->charset
				);

				// Send secondary email
				if ($tech_details['email_secondary']) {

					$sent_email_techids_short[] = $tech_details['id'];

					// tmp fake email
					$tech_details['email'] = $tech_details['email_secondary'];

					$tplname = $this->getTemplateName($tech_details, 'question_user_reply', 'secondary');

					send_tech_email(
						$tplname,
						$tech_details,
						array(
							'charset' => $this->charset,
							'subject_codes' => $this->subjectCodes($tech_details),
							'reply_header' => $this->replyHeader($tech_details, $tplname),
							'attachment_size' => $this->attachmentSize($tech_details, $attach_check),
							'ticket' => $this->ticket,
							'user_details' => $this->user_details,
							'attachments' => $this->attachments,
							'user_message' => $user_message
						),
						FALSE,
						$this->getFrom(),
						TRUE,
						$this->charset
					);
				}
			}
		}

		// ticketlog send user emails
		if ($sent_email_techids) {
			ticketlog($this->ticket['id'], 'email_tech', NULL, NULL, 'question_user_reply', NULL, $sent_email_techids);
		}

		if ($sent_email_techids_short) {
			ticketlog($this->ticket['id'], 'email_tech', NULL, NULL, 'question_user_reply_short', NULL, $sent_email_techids_short);
		}
	}





	/**
	 * Send notifications for when a ticket has been assigned.
	 */
	function SendTechAssigned() {

		global $db;

		$tech_details = $db->query_return("
			SELECT tech.*, email_own_attachments AS sendattachment
			FROM tech
			WHERE
				id = " . intval($this->ticket['tech']) . "
				AND	email_assigned = 1
		");

		if (!is_array($tech_details)) {
			return;
		}

		// Get the first user message
		$user_message = '';
		$user_message_info = $db->query_return("
			SELECT *
			FROM ticket_message
			WHERE ticketid = {$this->ticket['id']} AND techid = 0
			ORDER BY id ASC
			LIMIT 1
		");

		if ($user_message_info) {
			$user_message = $user_message_info['message'];
		}

		$tplname = $this->getTemplateName($tech_details, 'question_tech_assigned', 'primary');

		send_tech_email(
			$tplname,
			$tech_details,
			array(
				'subject_codes' => $this->subjectCodes($tech_details),
				'reply_header' => $this->replyHeader($tech_details, $tplname),
				'attachment_size' => $this->attachmentSize($tech_details, 'email_own_attachments'),
				'ticket' => $this->ticket,
				'ticketfields' => $this->ticketfields,
				'newtech' => $tech_details,
				'user_details' => $this->user_details,
				'attachments' => $this->attachments,
				'user_message' => $user_message,
				'user_message_info' => $user_message_info,
			),
			$this->getBlobs($tech_details, 'email_own_attachments'),
			$this->getFrom(),
			false,
			$this->charset
		);

		ticketlog($this->ticket['id'], 'email_tech', NULL, NULL, 'question_tech_assigned', NULL, array($tech_details['id']));

		if ($tech_details['email_secondary']) {

		    $tplname = $this->getTemplateName($tech_details, 'question_tech_assigned', 'secondary');

			// tmp fake email
			$tech_details['email'] = $tech_details['email_secondary'];

			send_tech_email(
				$tplname,
				$tech_details,
				array(
					'subject_codes' => $this->subjectCodes($tech_details),
					'reply_header' => $this->replyHeader($tech_details, $tplname),
					'attachment_size' => $this->attachmentSize($tech_details, 'email_own_attachments'),
					'ticket' => $this->ticket,
					'newtech' => $tech_details,
					'user_details' => $this->user_details,
					'attachments' => $this->attachments,
					'user_message' => $user_message,
					'user_message_info' => $user_message_info,
				),
				FALSE,
				$this->getFrom(),
				TRUE,
				$this->charset
			);

			ticketlog($this->ticket['id'], 'email_tech', NULL, NULL, 'question_tech_assigned_short', NULL, array($tech_details['id']));
		}

	}

	function SendTechParticipate($tech_details) {

	    global $cache2;

	    if (!is_array($tech_details)) {
	        $tech_details = $cache2->getTech($tech_details);
	    }

	    if (!$tech_details OR !$tech_details['email_add_participant'] OR $tech_details['id'] == $this->ticket['tech']) {
	        return;
	    }

	    $tplname = $this->getTemplateName($tech_details, 'question_tech_participate', 'primary');

	    send_tech_email(
			$tplname,
			$tech_details,
			array(
				'subject_codes' => $this->subjectCodes($tech_details),
				'reply_header' => $this->replyHeader($tech_details, $tplname),
				'attachment_size' => $this->attachmentSize($tech_details, 'email_own_attachments'),
				'ticket' => $this->ticket,
				'ticketfields' => $this->ticketfields,
				'newtech' => $tech_details,
				'user_details' => $this->user_details,
				'attachments' => $this->attachments
			),
			$this->getBlobs($tech_details, 'email_own_attachments'),
			$this->getFrom(),
			false,
			$this->charset
		);

		ticketlog($this->ticket['id'], 'email_tech', NULL, NULL, 'question_tech_participate', NULL, array($tech_details['id']));


	    if ($tech_details['email_secondary']) {

	        $tplname = $this->getTemplateName($tech_details, 'question_tech_participate', 'secondary');

			// tmp fake email
			$tech_details['email'] = $tech_details['email_secondary'];

			send_tech_email(
				$tplname,
				$tech_details,
				array(
					'subject_codes' => $this->subjectCodes($tech_details),
					'reply_header' => $this->replyHeader($tech_details, $tplname),
					'attachment_size' => $this->attachmentSize($tech_details, 'email_own_attachments'),
					'ticket' => $this->ticket,
					'newtech' => $tech_details,
					'user_details' => $this->user_details,
					'attachments' => $this->attachments
				),
				FALSE,
				$this->getFrom(),
				TRUE,
				$this->charset
			);

			ticketlog($this->ticket['id'], 'email_tech', NULL, NULL, 'question_tech_participate_short', NULL, array($tech_details['id']));
		}
	}





	/**
	 * Send notificaitons for when a tech has replied to a ticket.
	 *
	 * @param string $tech_message
	 * @param array $tech
	 */
	function SendTechReply($tech_message_info, $tech) {

		global $db, $cache2;

		// Get messages
		if (!is_array($tech_message_info) AND $tech_message_info) {
			$tech_message_info = $db->query_return("
				SELECT *
				FROM ticket_message
				WHERE id = " . intval($tech_message_info)
			);
		}

		$tech_message = $tech_message_info['message'];

		// can pass an id or an array
		if (!is_array($tech)) {
			$tech = $cache2->getTech($tech);
		}

		$sent_email_techids = array();
		$sent_email_techids_short = array();

		$notify_techs = $this->getTechsNotify('newreply');

        $notify_techs_enabled = $db->query_return_col("
			SELECT id
			FROM tech
			WHERE email_tech_reply = 1
		");

		if (!$notify_techs OR !$notify_techs_enabled) {
			return;
		}

		foreach ($notify_techs as $techid => $send_what) {

			$tech_details = $cache2->getTech($techid);

		    // make sure we're not sending to same tech that made the reply
			if ($techid == $tech['id']) {
				continue;
			}

			// Tech doesnt want tech replies
			if (!in_array($techid, $notify_techs_enabled)) {
			    continue;
			}

			// check permission
			if (!p_ticket('view', $this->ticket, $tech_details)) {
				continue;
			}

			if ($send_what['email']) {

    			$tplname = $this->getTemplateName($tech_details, 'question_tech_reply', 'primary');

    			$sent_email_techids[] = $tech_details['id'];

    			send_tech_email(
    				$tplname,
    				$tech_details,
    				array(
    					'subject_codes' => $this->subjectCodes($tech_details),
    					'tech' => $tech,
    					'reply_header' => $this->replyHeader($tech_details, $tplname),
    					'ticket' => $this->ticket,
    					'user_details' => $this->user_details,
    					'tech_message' => $tech_message,
    					'tech_message_info' => $tech_message_info
    				),
    				$this->getBlobs($tech_details, 'email_own_attachments'),
    				$this->getFrom(),
    				false,
    				$this->charset
    			);

    			if ($tech_details['email_secondary']) {

    			    $tplname = $this->getTemplateName($tech_details, 'question_tech_reply', 'secondary');

    				$sent_email_techids_short[] = $tech_details['id'];

    				// tmp fake email
    				$tech_details['email'] = $tech_details['email_secondary'];

    				send_tech_email(
    					$tplname,
    					$tech_details,
    					array(
    						'charset' => $this->charset,
    						'subject_codes' => $this->subjectCodes($tech_details),
    						'tech' => $tech,
    						'reply_header' => $this->replyHeader($tech_details, $tplname),
    						'ticket' => $this->ticket,
    						'user_details' => $this->user_details,
    						'tech_message' => $tech_message
    					),
    					FALSE,
    					$this->getFrom(),
    					TRUE,
    					$this->charset
    				);
    			}
			}
		}

		if ($sent_email_techids) {
			ticketlog($this->ticket['id'], 'email_tech', NULL, NULL, 'question_tech_reply', NULL, $sent_email_techids);
		}
		if ($sent_email_techids_short) {
			ticketlog($this->ticket['id'], 'email_tech', NULL, NULL, 'question_tech_reply_short', NULL, $sent_email_techids_short);
		}
	}

	function SendCustom($type) {
		$data = func_get_args();
		array_shift($data);

		(DpHooks::checkHook('sendtechticketemail_sendcustom') ? eval(DpHooks::getHook()) : null);
	}

	function getTemplateName($tech_details, $basename, $email_type = 'primary') {

	    if ($email_type == 'primary') {
	        if ($tech_details['email_tpl'] == 'short') {
	            $basename .= '_short';
	        }
	    } elseif ($email_type == 'secondary') {
	        if ($tech_details['email2_tpl'] != 'normal') {
	            $basename .= '_short';
	        }
	    }

	    return $basename;
	}
}

/***************************************
- This class creates and sends emails specific to a particular ticket
- It does *not* care about the status of the user, whether they are valiadted etc. This needs to have been handled previously.
- It does *not* know if this is the first ticket sent or the 100th. If you want account details sent as well, this needs to happen elsewhere.
- It does *not* check to see if we should be sending a response. This also needs to be handled elesewhere.
***************************************/

class sendUserTicketEmail {

	/**
	* the template name
	* @var array
	* @access private
	*/
	var $template_name;

	/**
	* array of subject/message
	* @var array
	* @access private
	*/
	var $template;

	/**
	* ticket array
	* @var array
	* @access private
	*/
	var $ticket;

	/**
	* user array
	* @var array
	* @access private
	*/
	var $user_details;

	/**
	 * User object
	 * @var User
	 * @access private
	 */
	var $userobj;

	/**
	* language to send email in
	* @var array
	* @access private
	*/
	var $language;

	/**
	* email to send to
	* @var array
	* @access private
	*/
	var $to;

	/**
	 * Charset to send mail in (dependant on ticket lang)
	 * @var string
	 * @access private
	 */
	var $charset;

	/**
	* message by tech
	* @var array
	* @access private
	*/
	var $tech_message;

	/**
	* message by user
	* @var array
	* @access private
	*/
	var $user_message;

	/**
	* array of the from email address (email/name from gateway_emails table)
	* @var array
	* @access private
	*/
	var $gateway_email;

	/**
	* any error that would prevent sending the email
	* @var string
	* @access private
	*/
	var $error;

	/**
	 * Array of extra headers to send with the email
	 * @var array
	 * @access protected
	 */
	var $extraheaders = array();

	/**
	 * True when we want to auto-send emails for participants too.
	 */
	var $send_participants = false;


	function returnEmail() {

		global $settings;

		if ($settings['gateway_bounce_on']) {
			return $settings['gateway_bounce_email'];
		}
	}

	/**
	 * Send user notification
	 *
	 * @param array|integer $ticket A ticket ID or an array of ticket information
	 * @param array|integer $user A user_details array, a userid
	 * @param string $template_name Which email to send
	 * @return sendUserTicketEmail
	 */
	function sendUserTicketEmail($ticket, $user_details, $template_name = '') {

		global $cache2, $db;

		/*****************
		* Get user details
		*****************/

		// Passed a normal user_details array
		if (is_array($user_details)) {
			$userid = $user_details['id'];
			$this->userobj = new User();
			$this->userobj->setUser($userid);
			$user_details = $this->userobj->getUser();

		// Passed just a userid
		} else if (is_numeric($user_details)) {
			$userid = $user_details;
			$this->userobj = new User();
			$this->userobj->setUser($userid);
			$user_details = $this->userobj->getUser();
		} else {
			ob_start();
			var_dump($user_details);
			$what = ob_get_clean();

			trigger_error('Invalid user_details were provided. Got: ' . $what);
		}

		$this->user_details = get_user_info($user_details);

		/*****************
		* Get ticket
		*****************/

		$this->ticket = get_ticket_info($ticket, 'tech', $this->user_details);

		$this->template_name = $template_name;

		$this->extraheaders['X-debug-template-name'] = preg_replace("#\n#", '\\n', $template_name);

		// get the gateway email
		$this->gateway_email = $cache2->getGatewayEmail($this->ticket['accountid']);
		if (!is_array($this->gateway_email)) {
			$this->gateway_email = $cache2->getGatewayEmailDefault();
		}
		if (!is_array($this->gateway_email)) {
			trigger_error('No default mail account', E_USER_ERROR);
		}
		if (!$this->gateway_email) {
			$this->error = 'gateway_email';
		}

		/*****************
		* Get charset
		*****************/

		if ($this->ticket['language']) {
			$lang = $cache2->getLanguage($this->ticket['language']);
		}

		if (!$lang) {
			$lang = $cache2->getLanguage($cache2->getDefaultLanguageID());
		}

		$this->charset = $lang['contenttype'];
	}

	function setup() {

		global $cache2;

		// determine where we are sending
		$this->to = $this->user_details['email'];

		// Set ticketemail if same user and there is a ticketemail
		if ($this->ticket['userid'] == $this->user_details['id'] AND $this->ticket['ticketemail']) {
			$this->to = $this->ticket['ticketemail'];
		}
	}

	/**
	* set the attachments
	* @var	array	array of attachment ids
	* @access	public
	*/
	function setAttachments($attachmentids) {

		global $db, $settings;

		if (!is_array($attachmentids) OR empty($attachmentids)) {
			return;
		}

		$db->query("
			SELECT * FROM ticket_attachments WHERE id IN " . array2sql($attachmentids)
		);

		while ($result = $db->row_array()) {
			$result['filesize_display'] = filesize_display($result['filesize']);
			$this->attachments[] = $result;
		}

	}

	function getBlobs() {

		global $cache2, $settings;

		if (!is_array($this->attachments)) {
			return;
		}

		foreach ($this->attachments AS $array) {

			// breach tech limit?
			if ($array['filesize'] > ($settings['attachment_max_user_email'] * 1024 * 1024)) {
				continue;
			}

			$blobs[] = array(
				'filename' => $array['filename'],
				'data' => get_attachment_blob($array['blobid'])
			);
		}

		return $blobs;
	}

	function setReplyCodes($nocodes = false) {

		global $settings;

		// remove any old codes that might be there
		$this->subject = preg_replace('#\[[a-zA-Z0-9]{8}\]#', '', $this->subject);
		$this->subject = preg_replace('#\[[0-9]{4}\-[A-Za-z]{4}\-[0-9]{4}\]#', '', $this->subject);

		// set the correct codes if the email gateway is on
		if ($settings['gateway_user_on'] AND !$nocodes) {
			$this->template['body'] .= "\n\r<=== " . $this->ticket['ref'] . " --- " . $this->ticket['authcode'] . " ===>";
			$this->template['subject'] .= ' - [' . $this->ticket['ref'] . '] [' . $this->ticket['authcode'] . ']';
		}

		(DpHooks::checkHook('senduserticketemail_reply_codes') ? eval(DpHooks::getHook()) : null);
	}

	function SendSplit($other_ticket) {

		$this->setup();

		// create the email
		$this->template = parse_user_email($this->template_name, array(
			'ticket' => $this->ticket,
			'other_ticket' => $other_ticket
		), $this->user_details, $this->user_details['language']);

		$this->setReplyCodes();

		// send the email
		dp_mail(
			$this->to,
			$this->user_details['name'],
			$this->template['subject'],
			$this->template['body'],
			$this->gateway_email['email'],
			$this->gateway_email['name'],
			NULL,
			$this->charset,
			$this->returnEmail(),
			$this->extraheaders
		);

		ticketlog($this->ticket['id'], 'email_user', $this->user_details['id'], NULL, $this->template_name, $this->to);
	}

	function SendTechOpen() {

		$this->setup();

		// create the email
		$this->template = parse_user_email($this->template_name, array(
			'ticket' => $this->ticket
		), $this->user_details, $this->user_details['language']);

		$this->setReplyCodes();

		// send the email
		dp_mail(
			$this->to,
			$this->user_details['name'],
			$this->template['subject'],
			$this->template['body'],
			$this->gateway_email['email'],
			$this->gateway_email['name'],
			NULL,
			$this->charset,
			$this->returnEmail(),
			$this->extraheaders
		);

		ticketlog($this->ticket['id'], 'email_user', $this->user_details['id'], NULL, $this->template_name, $this->to);
	}

	function SendCronClose($time) {

		global $settings;

		$this->setup();

		$time = convert_timestamp_to_human_readable($time);

		// create the email
		$this->template = parse_user_email($this->template_name, array(
			'ticket' => $this->ticket,
			'time' => $time,
			'can_reopen' => $this->userobj->permTickets('reopen')
		), $this->user_details, $this->user_details['language']);

		// only set reply codes if the user can reply
		if ($this->userobj->permTickets('reopen')) {
			$this->setReplyCodes();
		} else {
			$this->setReplyCodes(FALSE);
		}

		// send the email
		dp_mail(
			$this->to,
			$this->user_details['name'],
			$this->template['subject'],
			$this->template['body'],
			$this->gateway_email['email'],
			$this->gateway_email['name'],
			NULL,
			$this->charset,
			$this->returnEmail(),
			$this->extraheaders
		);

		ticketlog($this->ticket['id'], 'email_user', $this->user_details['id'], NULL, $this->template_name, $this->to);
	}

	function SendParticipate() {

	}

	function SendTechClose() {

		global $settings;

		// setup some variables
		$this->setup();

		// create the email
		$this->template = parse_user_email($this->template_name, array(
			'ticket' => $this->ticket,
			'can_reopen' => $this->userobj->permTickets('reopen')
		), $this->user_details, $this->user_details['language']);

		// ticket auth/ref for subject/body (do after the template has been parsed)
		$this->setReplyCodes();

		// send the email
		dp_mail(
			$this->to,
			$this->user_details['name'],
			$this->template['subject'],
			$this->template['body'],
			$this->gateway_email['email'],
			$this->gateway_email['name'],
			NULL,
			$this->charset,
			$this->returnEmail(),
			$this->extraheaders
		);

		ticketlog($this->ticket['id'], 'email_user', $this->user_details['id'], NULL, $this->template_name, $this->to);
	}

	function SendTechReply($tech_message_info) {

		global $db;

		// Get messages
		if (!is_array($tech_message_info) AND $tech_message_info) {
			$tech_message_info = $db->query_return("
				SELECT *
				FROM ticket_message
				WHERE id = " . intval($tech_message_info)
			);
		}

		$tech_message = $tech_message_info['message'];

		$this->setup();

		// create the email
		$this->template = parse_user_email($this->template_name, array(
			'tech_message' => $tech_message,
			'tech_message_info' => $tech_message_info,
			'ticket' => $this->ticket
		), $this->user_details, $this->user_details['language']);

		$this->setReplyCodes();

		// send the email
		dp_mail(
			$this->to,
			$this->user_details['name'],
			$this->template['subject'],
			$this->template['body'],
			$this->gateway_email['email'],
			$this->gateway_email['name'],
			$this->getBlobs(),
			$this->charset,
			$this->returnEmail(),
			$this->extraheaders
		);

		ticketlog($this->ticket['id'], 'email_user', $this->user_details['id'], NULL, $this->template_name, $this->to);
	}

	function SendTechNew($tech_message_info, $user_message_info = '', $newuser = false) {

		global $db, $cache2;

		// Get messages
		if (!is_array($user_message_info) AND $user_message_info) {
			$user_message_info = $db->query_return("
				SELECT *
				FROM ticket_message
				WHERE id = " . intval($user_message_info)
			);
		}

		if (!is_array($tech_message_info) AND $tech_message_info) {
			$tech_message_info = $db->query_return("
				SELECT *
				FROM ticket_message
				WHERE id = " . intval($tech_message_info)
			);
		}

		$user_message = $user_message_info['message'];
		$tech_message = $tech_message_info['message'];

		$this->setup();

		// create the email
		$this->template = parse_user_email($this->template_name, array(
			'tech_message' => $tech_message,
			'user_message' => $user_message,
			'tech_message_info' => $tech_message_info,
			'user_message_info' => $user_message_info,
			'ticket' => $this->ticket,
			'newuser' => $newuser
		), $this->user_details, $this->user_details['language']);

		$this->setReplyCodes();

		// send the email
		dp_mail(
			$this->to,
			$this->user_details['name'],
			$this->template['subject'],
			$this->template['body'],
			$this->gateway_email['email'],
			$this->gateway_email['name'],
			$this->getBlobs(),
			$this->charset,
			$this->returnEmail(),
			$this->extraheaders
		);

		ticketlog($this->ticket['id'], 'email_user', $this->user_details['id'], NULL, $this->template_name, $this->to);

		/**********
		 * Automatically send participants
		 **********/

		if ($this->send_participants) {
		    $ticket_participants = $cache2->getTicketParticipants($this->ticket, 'user');

		    foreach ($ticket_participants as $part) {
		        if (!$part['user'] OR $part['user'] == $this->ticket['user'] OR $part['id'] == $this->user_details['id']) continue;

		        $email = new sendUserTicketEmail($this->ticket, $part['user'], 'question_tech_reply_participate');
		        $email->SendTechNew($tech_message_info, $user_message_info, $newuser);
		    }
		}
	}

	function SendUserReply($user_message_info) {

		global $db, $cache2;

		// Get messages
		if (!is_array($user_message_info)) {
			$user_message_info = $db->query_return("
				SELECT *
				FROM ticket_message
				WHERE id = " . intval($user_message_info)
			);
		}

		$user_message = $user_message_info['message'];

		$this->setup();

		// create the email
		$this->template = parse_user_email($this->template_name, array(
			'user_message' => $user_message,
			'user_message_info' => $user_message_info,
			'ticket' => $this->ticket
		), $this->user_details, $this->user_details['language']);

		$this->setReplyCodes();

		// send the email
		dp_mail(
			$this->to,
			$this->user_details['name'],
			$this->template['subject'],
			$this->template['body'],
			$this->gateway_email['email'],
			$this->gateway_email['name'],
			NULL,
			$this->charset,
			$this->returnEmail(),
			$this->extraheaders
		);

		ticketlog($this->ticket['id'], 'email_user', $this->user_details['id'], NULL, $this->template_name, $this->to);


		/**********
		 * Automatically send participants
		 **********/

		if ($this->send_participants) {
		    $ticket_participants = $cache2->getTicketParticipants($this->ticket, 'user');

		    foreach ($ticket_participants as $part) {
		        if (!$part['id'] OR $part['id'] == $this->ticket['userid'] OR $part['id'] == $this->user_details['id']) continue;

		        $email = new sendUserTicketEmail($this->ticket, $part['id'], 'question_user_reply_participate');
		        $email->SendUserReply($user_message_info);
		    }
		}
	}

	function SendUserNew($user_message_info) {

		global $db;

		// Get messages
		if (!is_array($user_message_info)) {
			$user_message_info = $db->query_return("
				SELECT *
				FROM ticket_message
				WHERE id = " . intval($user_message_info)
			);
		}

		$user_message = $user_message_info['message'];

		$this->setup();

		// create the email
		$this->template = parse_user_email($this->template_name, array(
			'user_message' => $user_message,
			'user_message_info' => $user_message_info,
			'ticket' => $this->ticket
		), $this->user_details, $this->user_details['language']);

		$this->setReplyCodes();

		// send the email
		dp_mail(
			$this->to,
			$this->user_details['name'],
			$this->template['subject'],
			$this->template['body'],
			$this->gateway_email['email'],
			$this->gateway_email['name'],
			NULL,
			$this->charset,
			$this->returnEmail(),
			$this->extraheaders
		);

		ticketlog($this->ticket['id'], 'email_user', $this->user_details['id'], NULL, $this->template_name, $this->to);

	}

	function SendUserNewSuggest($articles, $is_required = false) {
		$this->setup();

		// create the email
		$this->template = parse_user_email($this->template_name, array(
			'ticket' => $this->ticket,
			'articles' => $articles,
			'is_required' => $is_required,
		), $this->user_details, $this->user_details['language']);

		$this->setReplyCodes(true);

		// send the email
		dp_mail(
			$this->to,
			$this->user_details['name'],
			$this->template['subject'],
			$this->template['body'],
			'',
			'',
			NULL,
			$this->charset,
			$this->returnEmail(),
			$this->extraheaders
		);

		ticketlog($this->ticket['id'], 'email_user', $this->user_details['id'], NULL, $this->template_name, $this->to);
	}

	function SendCustom($type) {
		$data = func_get_args();
		array_shift($data);

		(DpHooks::checkHook('senduserticketemail_sendcustom') ? eval(DpHooks::getHook()) : null);
	}

	/**
	 * Toggle automatic sending to ticket participants for:
	 * - SendTechReply
	 * - SendUserReply
	 */
	function setSendParticipants($send_participants = true) {
	    $this->send_participants = $send_participants;
	}
}

###############################################################################################
####################################   MAIL BUILDER FUNCTIONS  #########################################
###############################################################################################

/**
* get charset for a language
*
* @access	public
*
* @param	int	language id
*
* @return	 string	charset
*/
function get_charset($language) {

	global $db, $cache2;

	$language = $cache2->getLanguage($language);
	return ifr($language['contenttype'], 'ISO-8859-1');

}

?>