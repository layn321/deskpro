<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

/**
* data retrival from database
*
* @package DeskPRO
*/

class Store {

	var $help;

	function addHelp($section, $row) {
		$this->help[$section][] = $row;
	}

}

/**
* Cache
*
* data retrival from database
*
* @package DeskPRO
* @version $Id: class_Cache.php 7094 2011-10-14 10:17:47Z chroder $
*/
class Cache {

	/**
	* array of categories details
	* @var array
	* @access private
	*/
	var $categories;

	/**
	 * Array of category parent=>children
	 * @var array
	 * @access private
	 */
	var $category_parents;

	/**
	 * Array of catid=>array(usergroups) who have permission.
	 * @var array
	 * @access private
	 */
	var $category_user_permissions;

	/**
	* array of priorities details
	* @var array
	* @access private
	*/
	var $priorities;

	/**
	 * Array of priid=>array(usergroups) who have permissions
	 * @var array
	 * @access private
	 */
	var $priorities_user_permissions;

	/**
	* array of workflows details
	* @var array
	* @access private
	*/
	var $workflows;

	/**
	* array of techs details
	* @var array
	* @access private
	*/
	var $techs;

	/**
	* array of ticket fields details
	* @var array
	* @access private
	*/
	var $ticketfields;

	/**
	 * Array of ticket fields and which categories they should be displayed in
	 * @var array
	 * @access private
	 */
	var $ticketfield_cats;

	/**
	 * Array of usergroup permissions
	 * @var array
	 * @access private
	 */
	var $ticketfields_perms;

	/**
	* array of user fields details
	* @var array
	* @access private
	*/
	var $userfields;

	/**
	 * Array of usergroup permissions
	 * @var array
	 * @access private
	 */
	var $userfields_perms;

	/**
	* array of faq fields details
	* @var array
	* @access private
	*/
	var $faqfields;

	/**
	 * Array of usergroup permissions
	 * @var array
	 * @access private
	 */
	var $faqfields_perms;

	/**
	* array of mail filters details
	* @var array
	* @access private
	*/
	var $rules_mail;

	/**
	* array of mail filters details
	* @var array
	* @access private
	*/
	var $rules_web;

	/**
	 * Array of user rules for registration
	 * @var array
	 * @access private
	 */
	var $user_rules;

	/**
	* array of languages details
	* @var array
	* @access private
	*/
	var $languages;

	/**
	* array of ticket filters details
	* @var array
	* @access private
	*/
	var $ticketfilters;

	/**
	* array of user groups details
	* @var array
	* @access private
	*/
	var $usergroups;

	/**
	 * Array of user group names.
	 * @var array
	 * @access private
	 */
	var $usergroup_names;


	/**
	 * Array of cached companies.
	 * @var array
	 * @access private
	 */
	var $companies = array();

	/**
	 * Array of cached company names.
	 * @var array
	 * @access private
	 */
	var $company_names;

	/**
	 * Array of cached company roles
	 * @var array
	 * @access private
	 */
	var $company_roles;

	/**
	 * Array keeping IDs of named groups
	 * @var array
	 * @access private
	 */
	var $namedusergroups;

	/**
	* array of ticket views details
	* @var array
	* @access private
	*/
	var $ticketviews;

	/**
	* array of tickets details
	* @var array
	* @access private
	*/
	var $tickets;

	/**
	* array of ticket refs details
	* @var array
	* @access private
	*/
	var $ticketrefs;

	/**
	* array of words details
	* @var array
	* @access private
	*/
	var $words;

	/**
	* array of spam rules
	* @var array
	* @access private
	*/
	var $spams;

	/**
	 * Array of user sources
	 *
	 * @var array
	 */
	var $usersources;

	/**
	 * Array of enabled user source types.
	 *
	 * @var array
	 */
	var $usersourcetypes;

	/**
	 * @var Zend_Cache
	 */
	var $cache;

	/**
	 * @var Zend_Log
	 */
	var $cache_logger = false;
	var $cache_logger_arrwriter = null;

	/**
	 * Gets a cache object we can use to cache data instead of fetching
	 * it from the DB
	 *
	 * @return Zend_Cache
	 */
	function getCache()
	{
		if ($this->cache) {
			return $this->cache;
		}

		// If no cache is enabled, we just use a blackholed backend
		if (!defined('DP_ENABLE_CACHE') OR !DP_ENABLE_CACHE) {
			$this->cache = Zend_Cache::factory(
				'Core',
				'Black-Hole', // zend_cache does funky name normalizing, so we need the dash so autoloading works
				array('caching' => false),
				array(),
				false, false, true /* no naming, no naming, yes autoloading */
			);

		// Otherwise we'll fetch it from the conf file
		} else {

			$cache_options = include(ROOT . '/includes/config_cache.php');

			if (defined('DESKPRO_DEBUG_DEVELOPERMODE_FOOTER')) {
				$this->cache_logger = new Zend_Log();
				$this->cache_logger_arrwriter = new Orb_Log_Writer_Array();
				$this->cache_logger->addWriter($this->cache_logger_arrwriter);

				$cache_options['backend_options']['logging'] = true;
				$cache_options['backend_options']['logger'] = $this->cache_logger;
			}

			$this->cache = Zend_Cache::factory(
				'Core',
				$cache_options['backend_name'],
				array(
					'caching' => true,
					'cache_id_prefix' => 'dp',
					'lifetime' => null,
					'write_control' => true,
					'automatic_serialization' => true,
					'automatic_cleaning_factor' => 0,
					'ignore_user_abort' => true,
					'logging' => (bool)$this->cache_logger,
					'logger' => $this->cache_logger
				),
				$cache_options['backend_options'],
				false, false, true /* no naming, no naming, yes autoloading */
			);
		}

		return $this->cache;
	}

	/**
	* get spam rles
	*
	* @access	Public
	*
	* @param	int	$language	language id
	*
	* @return	 string	array	return the merged array for this language
	*/
	function getSpamRules() {

		global $db;

		static $complete = false;

		if (!$complete) {
			$this->spams = $db->query_return_array("SELECT * FROM gateway_spam", 'id');
			$complete = true;
		}

		return $this->spams;
	}

	// set a word in $dplang that overwrites everything else
	function setWord($wordref, $text, $language = 0) {

		if ($language) {

			$this->words[$langage][$wordref] = $text;

		} else {

			$language = $this->getDefaultLanguageID();
			$this->words[$language][$wordref] = $text;

		}

		$this->savedwords[$language][$wordref] = $text;

	}

	function updateSavedWords() {

		if (is_array($this->savedwords)) {
			foreach ($this->savedwords AS $language => $words) {
				foreach ($words AS $ref => $text) {
					$this->words[$language][$ref] = $text;
				}
			}
		}
	}

	/**
	* gets templat words for a language
	*
	* @access	Public
	*
	* @param	int	$language	language id
	*
	* @return	 string	array	return the merged array for this language
	*/
	function getWords($language) {

		global $db, $settings, $cache;

		if (defined('DESKPRO_DEBUG_WORDFILES')) {

			$language = $this->getLanguage($language);

			if (!is_array($language)) {
				echo "<h1>Language not found</h1>";
			}

			require_once(INC . 'classes/class_XMLDecode.php');

			if (in_string('UTF-8', $language['deskproid'])) {
				$xml_parser = new class_XMLDecode('UTF-8');
			} else {
				$xml_parser = new class_XMLDecode();
			}

			if ($language['deskproid'] == 'en_ISO-8859-1') {
				$xml = $xml_parser->parse_file('install/data/languages/english/' . $settings['deskpro_version_internal'] . '.xml');
			} else {
				$xml = $xml_parser->parse_file('install/data/languages/' . $language['deskproid'] . '.xml');
			}

			foreach ($xml['words']['category'] AS $category) {
				foreach ($category['word'] AS $word) {
					$returnarray[$word['name']] = $word['value'];
				}
			}

			if ($language['deskproid'] == 'en_ISO-8859-1') {
				$xml_parser = new class_XMLDecode();
				$xml = $xml_parser->parse_file('install/data/languages/english/dev_extra.xml');
				foreach ($xml['words']['category'] AS $category) {
					foreach ($category['word'] AS $word) {
						$returnarray[$word['name']] = $word['value'];
					}
				}
			}

			if (defined('DESKPRO_DEBUG_FINDWORDS')) {
				foreach ($returnarray AS $key => $var) {
					$returnarray[$key] = preg_replace("#[a-zA-Z]#", 'X', $var);
				}
			}

			$this->updateSavedWords();

			return $returnarray;

		} else {

			/*********
			* Already cached the words
			*********/

			if (is_array($this->words[$language])) {

				return $this->words[$language];

			/*********
			* No words so get default as well
			*********/

			} elseif (!is_array($this->words)) {

				// languages to get
				$languages = array(1, $language);

				$db->query("
					SELECT wordref, language, text
					FROM template_words
					WHERE language IN " . array2sql($languages) . "
				");

				while ($result = $db->row_array()) {

					if ($result['language'] == 1) {
						$this->words['default'][$result['wordref']] = trim($result['text']);

						// set for this language is not already set
						if (!isset($this->words[$language][$result['wordref']])) {
							$this->words[$language][$result['wordref']] = trim($result['text']);
						}

					// set for the language (if language = default, we never get here so no problem)
					} else {
						$this->words[$language][$result['wordref']] = trim($result['text']);
					}
				}

			/*********
			* Already have default, just get changed words.
			*********/

			} else {

				$this->words[$language] = $this->words['default'];

				$db->query("
					SELECT wordref, language, text
					FROM template_words
					WHERE language = " . intval($language) . "
				");

				while ($result = $db->row_array()) {
					$this->words[$language][$result['wordref']] = trim($result['text']);
				}
			}

			if (defined('DESKPRO_DEBUG_FINDWORDS')) {
				foreach ($this->words[$language] AS $key => $var) {
					$this->words[$language][$key] = preg_replace("#[a-zA-Z]+#", 'X', $var);
				}
			}

			$this->updateSavedWords();

			return $this->words[$language];

		}
	}

	/**
	 * Get a ticket by ID or Ref. Returns false when no ticket could be found.
	 *
	 * @param int|string $id_or_ref The ID or ref
	 * @param bool $force Force fetching the ticket again, even if it was cached
	 * @return array
	 */
	function getTicket($id_or_ref, $force = false)
	{
		global $db;

		if (is_ref($id_or_ref)) {
			if ($this->ticketrefs[$id_or_ref] AND !$force) {
				return $this->tickets[$this->ticketrefs[$id_or_ref]];
			}

			$ticket = $db->query_return("SELECT * FROM ticket WHERE ref = '" . $db->escape($id_or_ref) . "'");
		} else {
			$id_or_ref = (int)$id_or_ref;
			if ($this->tickets[$id_or_ref] AND !$force) {
				return $this->tickets[$id_or_ref];
			}

			$ticket = $db->query_return("SELECT * FROM ticket WHERE id = $id_or_ref");
		}

		if (!$ticket) {
			return false;
		}

		$ticket['@participants'] = $this->getTicketParticipants($ticket);

		$this->ticketrefs[$ticket['ref']] = $ticket['id'];
		$this->tickets[$ticket['id']] = $ticket;

		return $ticket;
	}

	/**
	* get ticket details by ticket ref
	*
	* @access	public
	*
	* @param	string	ticket ref
	* @param boolean $force True to force the update of a cache if it exists
	*
	* @return	 mixed|array	details of ticket
	*/
	function getTicketRef($ref, $force = false) {
		return $this->getTicket($ref, $force);
	}

	/**
	* get ticket details by ticket id
	*
	* @access	public
	*
	* @param	int	id of ticket
	* @param boolean $force Force the update of a cache (runs the query regardless of a cached ticket)
	*
	* @return	 mixed|array	details of ticket
	*/
	function getTicketId($id, $force = false) {
		return $this->getTicket($id, $force);
	}

	/**
	 * Get the ticket participants
	 *
	 * @param int|array $ticket Ticket ID or ticket info array
	 * @param string $type 'tech' or 'user' or false for both
	 * @return array
	 */
	function getTicketParticipants($ticket, $type = false) {

	    global $db;

	    if (is_array($ticket)) {
	        $ticket = $ticket['id'];
	    }
	    $ticket = (int)$ticket;

	    if (in_array($type, array('user', 'tech'))) {
	        $type_key = $type.'s';
	        $type = "AND user_type = '$type'";
	    } else {
	        $type_key = false;
	        $type = '';
	    }

	    $ticket_participants = array('techs' => array(), 'techs_ids' => array(), 'users' => array(), 'users_ids' => array());
        $participant_userids = array();
        $res = $db->query("
            SELECT *
            FROM ticket_participant
            WHERE ticket = $ticket $type
        ");

        while ($participant = $db->row_array($res)) {
            if ($participant['user_type'] == 'tech') {
				if ($p = $this->getTech($participant['user'], false)) {
					$ticket_participants['techs'][] = array_merge(array('partid' => $participant['id'], 'ticketid' => $participant['ticket']), $p);
					$ticket_participants['techs_ids'][] = $participant['user'];
				}
            } else {
                // Is a user
                if ($participant['user']) {
                    $participant_userids[$participant['user']] = array('partid' => $participant['id'], 'ticketid' => $participant['ticket'], 'email' => $participant['email']);
                    $ticket_participants['users_ids'][] = $participant['user'];
                // Is a invite
                } else {
                    $ticket_participants['users'][] = array('partid' => $participant['id'], 'id' => 0, 'email' => $participant['email'], 'ticketid' => $participant['ticket']);
                }
            }
        }

        if ($participant_userids) {
            $more = $db->query_return_array("
            	SELECT user.id, user.name, user.username, user_email.email AS primary_email
            	FROM user
            	LEFT JOIN user_email ON (user_email.userid = user.id AND user_email.id = user.default_emailid)
            	WHERE user.id IN " . array2sql(array_keys($participant_userids)) . "
            ");

            if ($more) {
                $ticket_participants['users'] = array_merge($ticket_participants['users'], $more);
            }

            foreach ($ticket_participants['users'] as $k => $v) {
                $userid = $v['id'];
                if (!$userid) continue;

                $email = Orb_Util::ifvalor($participant_userids[$userid]['email'], $v['primary_email']);

                $ticket_participants['users'][$k]['email'] = $email;
                $ticket_participants['users'][$k]['partid'] = $participant_userids[$userid]['partid'];
                $ticket_participants['users'][$k]['ticketid'] = $participant_userids[$userid]['ticketid'];
            }
        }

        if ($type_key) {
            return $ticket_participants[$type_key];
        }

        return $ticket_participants;
	}

	/**
	* get ticket details by ticket id
	*
	* @access	public
	*
	* @param	int	id of ticket
	*
	* @return	 mixed|array	details of ticket
	*/
	function cacheGatewayEmails() {

		global $db;

		static $complete = false;

		if (!$complete) {
			$this->gateway_emails = $db->query_return_array("SELECT * FROM gateway_emails", 'id');
			$complete = true;
		}

		return $this->gateway_emails;

	}


	/**
	 * Get user permissions for custom fields.
	 *
	 * @param string $type The type of custom field. This is 'ticket' or 'user'
	 * @param integer|bool $fieldid A fieldid to get permissions for specifically
	 * @return unknown
	 */
	function getFieldsPermissions($fieldtype, $fieldid = false) {

		switch ($fieldtype) {
			case 'ticket':
				$table = 'ticket_def';
				$perms = $this->ticketfields_perms;
				$var = 'ticketfields_perms';
				break;

			case 'user':
				$table = 'user_def';
				$perms = $this->userfields_perms;
				$var = 'userfields_perms';
				break;

			case 'faq':
				$table = 'faq_def';
				$perms = $this->faqfields_perms;
				$var = 'faqfields_perms';
				break;

			default:
				return false;
		}

		if (!is_array($perms)) {

			global $db;

			$db->query("
				SELECT id, usergroup, perm_type
				FROM def_permissions
				WHERE tablename = '$table'
			");

			$perms = array();
			while($pinfo = $db->row_array()) {
				$perms[$pinfo['id']][$pinfo['perm_type']][] = $pinfo['usergroup'];
			}

			$this->$var = $perms;
		}

		if ($fieldid !== false) {
			if (isset($perms[$fieldid])) {
				return $perms[$fieldid];
			} else {
				return array();
			}
		}

		return $perms;
	}

	/**
	 * Return the fields the user can see.
	 *
	 * @param string $fieldtype Type of fields : 'ticket' or 'user'
	 * @param string $permtype Type of permissions. Ex: 'user_viewable'
	 * @param User|false $userobj A user object to use. If false, the global user object will be used
	 * @return array
	 */
	function returnFieldsPermissions($fieldtype, $permtype, $userobj = false) {

		if (!$userobj) {
			global $userobj;
		}

		switch ($fieldtype) {
			case 'ticket':
				$this->getTicketFields(false);
				$this->getFieldsPermissions('ticket');
				$allfields = $this->ticketfields;
				$allperms = $this->ticketfields_perms;
				break;

			case 'user':
				$this->getUserFields(false);
				$this->getFieldsPermissions('user');
				$allfields = $this->userfields;
				$allperms = $this->userfields_perms;
				break;

			case 'faq':
				$this->getFaqFields();
				$this->getFieldsPermissions('faq');
				$allfields = $this->faqfields;
				$allperms = $this->faqfields_perms;
				break;

			default:
				return false;
		}

		$output = array();

		foreach ($allfields AS $name => $field) {

			$perms = $allperms[$field['id']][$permtype];
			if ($perms) {

				// 0 means 'all'
				if (in_array(0, $perms)) {
					$output[$name] = $field;

				// No all, we have to check specifics
				} else {
					foreach ($userobj->getMemberGroups() as $groupid) {
						if (in_array($groupid, $perms)) {
							$output[$name] = $field;
							break;
						}
					}
				}
			}
		}

		(DpHooks::checkHook('cache_returnFieldsPermissions_beforereturn') ? eval(DpHooks::getHook()) : null);

		return $output;
	}

	/**
	 * Get all the company roles. You can optionally supply
	 * ID's or arrays of IDs and only return those specified.
	 *
	 * @return array of compamy roles
	 */
	function getCompanyRoles() {

		$this->_initBasicUserProperties();

		/******************************
		* No args, fetch all of them
		******************************/

		if (func_num_args() == 0) {
			return $this->company_roles;
		}

		/******************************
		* Else get those specified
		******************************/

		$ids = array();

		foreach (func_get_args() as $arg) {
			$arg = (array)$arg;
			$ids = array_merge($ids, $arg);
		}

		$ids = array_unique($ids);

		$ret = array();

		if ($ids) {
			foreach ($ids as $id) {
				if (isset($this->company_roles[$id])) {
					$ret[$id] = $this->company_roles[$id];
				}
			}
		}

		return $ret;
	}

	/**
	 * Get a single company role name
	 *
	 * @param integer $id The ID of the company role
	 * @return array The company role name
	 */
	function getCompanyRoleName($id) {
		if (!is_array($this->company_roles)) {
			$this->getCompanyRoles();
		}

		return $this->company_roles[$id]['name'];
	}

	/**
	 * Get a single company role
	 *
	 * @param integer $id The ID of the company role
	 * @return array The company role
	 */
	function getCompanyRole($id) {
		if (!is_array($this->company_roles)) {
			$this->getCompanyRoles();
		}

		return $this->company_roles[$id];
	}

	/**
	 * Get the names of the company roles
	 *
	 * @param array $add_top Optionally add options to the top
	 * @param array $which Specify which roles to include/exclude
	 * @param bool $include If false, $which acts as exclude
	 * @return array Company names
	 */
	function getCompanyRoleNames($add_top = array(), $which = null, $include = false) {

		if (!is_array($this->company_roles)) {
			$this->getCompanyRoles();
		}


		/******************************
		* Exclude options if requested
		******************************/

		$ret = array();

		if (!is_null($which)) {
			$which = (array)$which;
		} else {
			$which = false;
		}

		foreach ($this->company_roles as $id => $role) {

			if ($which) {
				$in_array = in_array($id, $which);

				if ((!$include AND !$in_array) OR ($include AND $in_array)) {
					$ret[$id] = $role['name'];
				}
			} else {
				$ret[$id] = $role['name'];
			}
		}


		/******************************
		* Add options to the top if requested
		******************************/

		if ($add_top) {
			if (is_array($add_top)) {
				$ret = array_merge_assoc($add_top, $ret);
			} else {
				array_unshift_assoc($ret, '0', $add_top);
			}
		}

		return $ret;
	}

	/**
	* return all user groups
	*
	* @access	public
	*
	* @return	 mixed|array	user group details
	*/
	function getUsergroups() {

		$this->_initBasicUserProperties();

		return $this->usergroups;
	}

	/**
	 * Get a single company
	 *
	 * @param integer $id The company id
	 * @return array The comapny
	 */
	function getCompany($id) {

		$id = (int)$id;

		if (!isset($this->companies[$id])) {
			global $db;

			$this->companies[$id] = $db->query_return("
				SELECT * FROM user_company
				WHERE id = $id
			");
		}

		return $this->companies[$id];
	}

	/**
	 * Get an array of emails we should CC for company tickets.
	 *
	 * @param integer $id The company ID
	 * @return array
	 */
	function getCompanyCcEmails($id)
	{
		$comp = $this->getCompany($id);
		if (!$comp) {
			return array();
		}

		$ret = explode("\n", standard_eol($comp['cc_emails']));
		$ret = Orb_Array::removeFalsey($ret);
		$ret = Orb_Array::removeEmptyString($ret);

		return $ret;
	}

	/**
	 * Get multiple companies (or just use this to cache multiple
	 * companies).
	 *
	 * You can call this function with multiple IDs as parameters,
	 * or arrays of IDs, or a mixture of both.
	 *
	 * @param int|array An ID or array of IDs
	 * @param int|array An ID or array of IDs ...
	 *
	 * @return array Array of company info
	 */
	function getCompanies() {

		/******************************
		* Get which fields to fetch
		******************************/

		$ids = array();

		for($i = 0, $num = func_num_args(); $i < $num; $i++) {
			$arg = func_get_arg($i);

			if (is_array($arg)) {
				$ids = array_merge($ids, $arg);
			} else {
				$ids[] = $arg;
			}
		}

		$ids = array_unique($ids);
		$got_ids = array_keys($this->companies);

		// Only get the ones that aren't already cachced
		$fetch_ids = array_diff($ids, $got_ids);


		/******************************
		* Get the comps
		******************************/

		if ($fetch_ids) {
			global $db;

			$db->query("
				SELECT * FROM user_company
				WHERE id IN " . array2sql($fetch_ids) . "
			");

			while ($comp = $db->row_array()) {
				$this->companies[$comp['id']] = $comp;
			}
		}


		/******************************
		* Return them
		******************************/

		$ret = array();

		foreach ($ids as $id) {
			$ret[$id] = $this->companies[$id];
		}

		return $ret;
	}

	/**
	 * Return which usergroups are linked with certain companies
	 *
	 * @param array|int $companies An array of company IDs or a single company ID to fetch
	 *                             linked usergroups for.
	 *
	 * @return array The linked usergroups
	 */
	function getCompanyUsergroupLinks($companies = array()) {

		global $db;

		$companies = (array)$companies;

		$linked = (array)$db->query_return_array_id("
			SELECT groupid FROM user_company2group
			WHERE companyid IN " . array2sql($companies) . "
		", 'groupid', '');

		$linked = array_unique($linked);

		return $linked;
	}

	/**
	 * Return which companies are linked with certain usergroups
	 *
	 * @param array|int $usergroups An array of usergroup IDs or a single usergroup ID to fetch
	 *                             linked companies for for.
	 *
	 * @return array The linked companies
	 */
	function getUsergroupCompanyLinks($usergroups = array()) {

		global $db;

		$usergroups = (array)$usergroups;

		$linked = (array)$db->query_return_array_id("
			SELECT companyid FROM user_company2group
			WHERE groupid IN " . array2sql($usergroups) . "
		", 'companyid', '');

		$linked = array_unique($linked);

		return $linked;
	}

	/**
	 * Return information about a single usergroup
	 *
	 * @param integer $id The id of the usergroup
	 *
	 * @return array|bool Array of details or false if it doesnt exist
	 */
	function getUsergroup($id) {

		if (!$this->usergroups) {
			$this->getUsergroups();
		}

		if (isset($this->usergroups[$id])) {
			return $this->usergroups[$id];
		} else {
			return false;
		}
	}

	/**
	 * Get one of the special system user groups
	 *
	 * @param string $system_name The system name of the group (guest, registered etc)
	 *
	 * @return array|bool Array of details or false if it doesnt exist
	 */
	function getNamedUsergroup($system_name) {

		if (!$this->usergroups) {
			$this->getUsergroups();
		}

		if (isset($this->namedusergroups[$system_name])) {
			$id = $this->namedusergroups[$system_name];

			return $this->getUsergroup($id);
		}

		return false;
	}





	/**
	 * Get the ID of a special system group
	 *
	 * @param string $system_name The system name of the group (guest, registered etc)
	 *
	 * @return integer|bool The ID or false if it doesnt exist
	 */
	function getNamedUsergroupId($system_name) {

		$group = $this->getNamedUsergroup($system_name);

		if (!$group) {
			return false;
		}

		return $group['id'];
	}





	/**
	 * Get an array of user group names, keyed by user group ID.
	 *
	 * $add_top can be an array of items to add to the beginning. If it is simply a string,
	 * then it will be added to the beginning with 0 as the key (useful for 'None' kind of options).
	 *
	 * $which can be an array of items to exclude from the array or a single item to exclude. Items can be
	 * numeric (user group ID's) or strings (user group system names). You can use 'guestreg' as a shortcut for
	 * the guests and registered usergroup since it is so common to exclude.
	 *
	 * If $include is true, then $which switches from being a list of ones to include to a list of specific
	 * ones to return.
	 *
	 * @param array|string $add_top Options to add to the beginning of the array.
	 * @param array|int|string $which Options to exclude. Can be a single option or an array of options.
	 * @return unknown
	 */
	function getUsergroupNames($add_top = array(), $which = null, $include = false) {

		if (!is_array($this->usergroup_names)) {
			$this->getUsergroups();
		}


		/******************************
		* Exclude options if requested
		******************************/

		if (!is_null($which)) {
			$ret = array();
			$which = (array)$which;

			// 'guestreg' is very common to exclude, so lets have a shortcut
			if (!$include AND ($key = array_search('guestreg', $which)) !== false) {
				unset($which[$key]);
				$which[] = 'guest';
				$which[] = 'registered';
			}

			foreach ($which as $key => $id) {
				if (!is_numeric($id)) {
					if ($id = $this->getNamedUsergroupId($id)) {
						$which[$key] = $id;
					} else {
						unset($which[$key]);
					}
				}
			}

			foreach ($this->usergroup_names as $id => $name) {

				$in_array = in_array($id, $which);

				if ((!$include AND !$in_array) OR ($include AND $in_array)) {
					$ret[$id] = $name;
				}
			}
		} else {
			$ret = $this->usergroup_names;
		}


		/******************************
		* Add options to the top if requested
		******************************/

		if ($add_top) {
			if (is_array($add_top)) {
				$ret = array_merge_assoc($add_top, $ret);
			} else {
				array_unshift_assoc($ret, '0', $add_top);
			}
		}

		return $ret;
	}

	function getUsergroupName($id) {

		if (!is_array($this->usergroup_names)) {
			$this->getUsergroups();
		}

		return $this->usergroup_names[$id];
	}

	/**
	 * Get the options that go into a select
	 */
	function getUsergroupOptions($show_none = false, $show_sys = true, $show_cust = true, $show_comp = true, $show_guestreg = false, $show_optgroups = false) {

		$allgroups = $this->getUsergroups();

		if (!$show_guestreg) {
			unset($allgroups[ $this->namedusergroups['guest'] ]);
			unset($allgroups[ $this->namedusergroups['registered'] ]);
		}

		$sys_groups = array();
		$cust_groups = array();
		$comp_groups = array();

		foreach ($allgroups as $group) {

			if ($group['is_system']) {
				$sys_groups[$group['id']] = $group['name'];
			} else if ($group['is_company']) {
				$comp_groups[$group['id']] = $group['name'];
			} else {
				$cust_groups[$group['id']] = $group['name'];
			}

		}

		unset($allgroups);

		$groups = array();

		if ($show_none) {
			$groups[0] = 'None';
		}

		$optgroups = 0;

		if ($show_sys AND $sys_groups) {
			$groups['opt1'] = array('OPTGROUP', 'System User Groups');
			foreach ($sys_groups as $id => $name) {
				$groups[$id] = $name;
			}
			$optgroups++;
		}

		if ($show_cust AND $cust_groups) {
			$groups['opt2'] = array('OPTGROUP', 'User Groups');
			foreach ($cust_groups as $id => $name) {
				$groups[$id] = $name;
			}
			$optgroups++;
		}

		if ($show_comp AND $comp_groups) {
			$groups['opt3'] = array('OPTGROUP', 'Companies');
			foreach ($comp_groups as $id => $name) {
				$groups[$id] = $name;
			}
			$optgroups++;
		}

		// Don't include optgroups if only showing one kind
		if (!$show_optgroups) {
			unset($groups['opt1'], $groups['opt2'], $groups['opt3']);
		}

		return $groups;
	}

	/**
	* get saved ticket search
	*
	* @access	public
	*
	* @return	 mixed|array	details of search
	*/
	function getTicketFilters($all = 1) {

		static $run;

		global $db, $user;

		if (!$run) {

			$run = 1;

			$this->ticketfilters = $db->query_return_array("
				SELECT id, save_name, techid
				FROM ticket_filters
					WHERE techid = " . intval($user['id']) . "
					OR isglobal = 1
				ORDER BY isglobal ASC, save_name
			", 'save_name');
		}

		if (!is_array($this->ticketfilters)) {
			return;
		}

		if ($all) {
			foreach ($this->ticketfilters AS $filter) {
				$array[$filter['id']] = $filter['save_name'];
			}
		} else {
			foreach ($this->ticketfilters AS $filter) {
				if ($filter['techid'] == $user['id']) {
					$array[$filter['id']] = $filter['save_name'];
				}
			}
		}

		return $array;

	}

	/**
	* get ticket views details
	*
	* @access	public
	*
	* @return	 mixed|array	details of ticket view
	*/
	function getTicketViews() {

		global $db, $user;

		if (!is_array($this->ticketviews)) {

			$this->ticketviews = (array)$db->query_return_array_id("
				SELECT * FROM ticket_views
				WHERE techid = " . intval($user['id']) . "
					OR isglobal = 1
				ORDER BY isglobal ASC
			");
		}

		return $this->ticketviews;

	}

	function getDefaultTemplateSet() {

		global $db;

		$result = $db->query_return("SELECT id FROM template_set WHERE ref = 'default'");
		return $result['id'];

	}

	/**
	* get default mail filter
	*
	* @access	public
	*
	* @return	 mixed|array	 mail filter details
	*/
	function getRulesMailDefault() {

		$this->getRulesMail();

		if (is_array($this->rules_mail)) {
			foreach ($this->rules_mail AS $rule) {
				if ($rule['is_default']) {
					return $rule;
				}
			}
		}

		return false;
	}

	/**
	* get default mail filter
	*
	* @access	public
	*
	* @return	 mixed|array	 mail filter details
	*/
	function getRulesWebDefault() {

		$this->getRulesWeb();

		if (is_array($this->rules_web)) {
			foreach ($this->rules_web AS $rule) {
				if ($rule['is_default']) {
					return $rule;
				}
			}
		}

		return false;
	}

	/**
	* get mail filters
	*
	* @access	public
	*
	* @return	 mixed|array	details of mail filters
	*/
	function getRulesMail($id = false) {

		global $db;

		if (!is_array($this->rules_mail)) {

			$this->rules_mail = $db->query_return_array_id("
				SELECT * FROM ticket_rules_mail ORDER BY displayorder
			");

			if (is_array($this->rules_mail)) {
				foreach ($this->rules_mail AS $key => $result) {

					if ($result['is_default']) {
						$this->rules_mail_default = $key;
					}

					$this->rules_mail[$key]['actions'] = unserialize($this->rules_mail[$key]['actions']);
					$this->rules_mail[$key]['criteria'] = unserialize($this->rules_mail[$key]['criteria']);
				}
			}
		}

		$rules = $this->rules_mail;
		unset($rules[$this->rules_mail_default]);

		if ($id) {
			return $rules[$id];
		} else {
			return $rules;
		}

	}

	/**
	* get mail filters
	*
	* @access	public
	*
	* @return	 mixed|array	details of mail filters
	*/
	function getRulesWeb($id = '') {

		global $db;

		if (!is_array($this->rules_web)) {

			$this->rules_web = $db->query_return_array_id("
				SELECT * FROM ticket_rules_web ORDER BY displayorder
			");

			if (is_array($this->rules_web)) {
				foreach ($this->rules_web AS $key => $result) {

					if ($result['is_default']) {
						$this->rules_web_default = $key;
					}

					$this->rules_web[$key]['actions'] = unserialize($this->rules_web[$key]['actions']);
					$this->rules_web[$key]['criteria'] = unserialize($this->rules_web[$key]['criteria']);
				}
			}
		}

		$rules = $this->rules_web;
		unset($rules[$this->rules_web_default]);

		if ($id) {
			return $rules[$id];
		} else {
			return $rules;
		}

	}

	/**
	 * Get user rules for registration
	 *
	 * @param integer $id The ID of a single rule you want
	 * @param bool $inactive Get inactive rules too?
	 * @return array
	 */
	function getUserRules($id = false, $inactive = false) {

		if (!is_array($this->user_rules)) {

			global $db;

			$this->user_rules = array();

			$usergroup_ids = array_keys($this->getUsergroups());
			$company_ids = array_keys($this->getCompanyNames());

			$db->query("
				SELECT * FROM user_rules
				ORDER BY run_order ASC
			");

			while ($rule = $db->row_array()) {

				$rule['criteria'] = unserialize($rule['criteria']);
				$rule['actions'] = unserialize($rule['actions']);

				// Verify usergroups to add
				if ($rule['actions']['add_usergroups']) {
					foreach ($rule['actions']['add_usergroups'] as $k => $v) {
						if (!in_array($v, $usergroup_ids)) {
							unset($rule['actions']['add_usergroups'][$k]);
						}
					}

					if (!count($rule['actions']['add_usergroups'])) {
						unset($rule['actions']['add_usergroups']);
					}
				}

				// Verify companies to add
				if ($rule['actions']['add_companies']) {
					foreach ($rule['actions']['add_companies'] as $k => $v) {
						if (!in_array($v, $company_ids)) {
							unset($rule['actions']['add_companies'][$k]);
						}
					}

					if (!count($rule['actions']['add_companies'])) {
						unset($rule['actions']['add_companies']);
					}
				}

				// If its linked, add it to the beginning
				if ($rule['link_company']) {
					array_unshift_assoc($this->user_rules, $rule['id'], $rule);

				// Just add it, should already be in order
				} else {
					$this->user_rules[$rule['id']] = $rule;
				}
			}
		}

		if ($id) {
		    $rule = $this->user_rules[$id];

		    if ($inactive) {
		        return $rule;
		    } elseif ($rule['run_web']) {
		        return $rule;
		    } else {
		        return false;
		    }
		}

		if (!$inactive) {
		    $ret = array();

		    foreach ($this->user_rules as $k => $rule) {
		        if ($rule['run_web']) {
		            $ret[$k] = $rule;
		        }
		    }
		} else {
		    $ret = $this->user_rules;
		}

		return $ret;
	}

	function getEmailAccounts() {

		global $db;

		if (!is_array($this->email_accounts)) {

			$this->email_accounts = $db->query_return_array_id("
				SELECT * FROM gateway_emails
			");
		}

		return $this->email_accounts;

	}

	function getEmailAccountDefault() {

		$this->getEmailAccounts();

		if (!is_array($this->email_accounts)) {
			return;
		}

		foreach ($this->email_accounts AS $account) {
			if ($account['is_default']) {
				return $account;
			}
		}
	}

	/**
	* get styles
	*
	* @access	public
	*
	* @return	 mixed|array	details of styles
	*/
	function getStyles() {

		global $db;

		if (!is_array($this->styles)) {
			$this->styles = (array)$db->query_return_array_id("
				SELECT style.*, template_set.parent AS template_set_parent, template_stylesheets.name AS template_stylesheets_name
				FROM style
				LEFT JOIN template_set ON style.templateset = template_set.id
				LEFT JOIN template_stylesheets ON style.cssstyle = template_stylesheets.id
			");
		}

		return $this->styles;

	}






	/**
	 * Get an array of all the names of companies (useful for use in a select), keyed by company ID.
	 *
	 * $add_top can be an array of items to add to the beginning. If it is simply a string,
	 * then it will be added to the beginning with 0 as the key (useful for 'None' kind of options).
	 *
	 * $exclude can be an array of items to exclude from the array or a single item to exclude.
	 *
	 * @param array|string $add_top Options to add to the beginning of the array.
	 * @param array|int|string $which Options to exclude/include. Can be a single option or an array of options.
	 * @param boolean $include When true, the $which param defines which options to be included. Otherwise the $which
	 *                         param defines which should be excluded.
	 *
	 * @return unknown
	 */
	function getCompanyNames($add_top = array(), $which = null, $include = false) {

		$this->_initCompanyNamesData();


		/******************************
		* Exclude options if requested
		******************************/

		if (!is_null($which)) {
			$ret = array();
			$which = (array)$which;

			foreach ($this->company_names as $id => $name) {
				$in_array = in_array($id, $which);

				if ((!$include AND !$in_array) OR ($include AND $in_array)) {
					$ret[$id] = $name;
				}
			}
		} else {
			$ret = $this->company_names;
		}




		/******************************
		* Add options to the top if requested
		******************************/

		if ($add_top) {
			if (is_array($add_top)) {
				$ret = array_merge_assoc($add_top, $ret);
			} else {
				array_unshift_assoc($ret, '0', $add_top);
			}
		}

		return $ret;
	}

	protected function _initCompanyNamesData()
	{
		if (is_array($this->company_names)) {
			return;
		}

		$this->company_names = $this->getCache()->load('company_names');
		if (is_array($this->company_names)) {
			return;
		}

		global $db;

		$this->company_names = array();

		$db->query("SELECT id, name FROM user_company");

		while ($comp = $db->row_array()) {
			$this->company_names[$comp['id']] = $comp['name'];
		}

		asort($this->company_names);

		$this->getCache()->save($this->company_names, 'company_names');
	}





	/**
	 * Get the name of a company
	 *
	 * @param int $id The company ID
	 * @return string
	 */
	function getCompanyName($id) {

		// Use company names if cached
		if (isset($this->company_names[$id])) {
			return $this->company_names[$id];
		}

		// Otherwise get it normally (may require new query if not cached)
		$comp = $this->getCompany($id);
		return $comp['name'];
	}





	/**
	* get ticket categories
	*
	* @access	public
	*
	* @return	 mixed|array	details of ticket categories
	*/
	function getCategories() {

		$this->_initBasicTicketProperties();

		return $this->categories;

	}

	/**
	 * Get user permissions for categories
	 *
	 * @param integer Optionally provide the ID of a category to get permisison for. Else, all permissions are returned
	 * @return array Permissions
	 */
	function getUserCategoryPermissions($get_catid = 0) {

		static $complete = false;

		if (!$complete) {

			global $db;

			$complete = true;

			$this->getCategories();

			$this->category_user_permissions = array();

			$groups = $this->getUsergroups();
			$all_group_ids = array_keys($groups);

			$perms = $db->query_return_group("SELECT * FROM ticket_cat_permissions", 'category');

			$inherit_cats = array();

			foreach ($this->categories as $catid => $cat) {

				if (!is_array($this->category_user_permissions[$catid])) {
					$this->category_user_permissions[$catid] = array();
				}

				// Inherit / All option
				if ($cat['perm_inherit']) {
					if ($cat['parent']) {
						$inherit_cats[$catid] = $cat['parent'];
					} else {
						$this->category_user_permissions[$catid] = $all_group_ids;
					}

				// Specific groups
				} else if ($perms[$catid]) {
					foreach ($perms[$catid] as $perminfo) {
						$this->category_user_permissions[$catid][] = $perminfo['usergroup'];
					}
				}
			}

			foreach ($inherit_cats as $catid => $parent_catid) {
				$this->category_user_permissions[$catid] = $this->category_user_permissions[$parent_catid];
			}
		}

		if ($get_catid) {
			return $this->category_user_permissions[$get_catid];
		} else {
			return $this->category_user_permissions;
		}
	}

	/**
	 * Get user permissions for priorities
	 *
	 * @param integer Optionally provide the ID of a priority to get permisison for. Else, all permissions are returned
	 * @return array Permissions
	 */
	function getUserPrioritiesPermissions($get_pri = 0) {

		static $complete = false;

		if (!$complete) {

			global $db;

			$complete = true;

			$this->getPriorities();
			$this->priorities_user_permissions = array();

			$groups = $this->getUsergroups();
			$all_group_ids = array_keys($groups);

			$perms = $db->query_return_group("SELECT * FROM ticket_pri_permissions", 'priority');

			foreach ($this->priorities as $priid => $pri) {

				if ($pri['perm_all']) {
					$this->priorities_user_permissions[$priid] = $all_group_ids;
				} else {
					$this->priorities_user_permissions[$priid] = array();

					if ($perms[$priid]) {
						foreach ($perms[$priid] as $perminfo) {
							$this->priorities_user_permissions[$priid][] = $perminfo['usergroup'];
						}
					}
				}
			}
		}

		if ($get_pri) {
			return $this->priorities_user_permissions[$get_pri];
		} else {
			return $this->priorities_user_permissions;
		}
	}

	/**
	 * get a single priority.
	 *
	 * @param integer $id The pri id
	 * @return array
	 */
	function getPriority($id) {
		$this->getPriorities();

		return $this->priorities[$id];
	}

	/**
	* get ticket priorities
	*
	* @access	public
	*
	* @return	 mixed|array	details of ticket priorities
	*/
	function getPriorities() {

		$this->_initBasicTicketProperties();

		return $this->priorities;

	}

	/**
	* get ticket work flows
	*
	* @access	public
	*
	* @return	 mixed|array	details of ticket workflows
	*/
	function getWorkflows() {

		$this->_initBasicTicketProperties();

		return $this->workflows;
	}
	/**
	* get technicians
	*
	* @access	public
	*
	* @return	 mixed|array	details of technicians
	*/
	function getTechs($inactive = false, $simpletechs = false) {

		$this->_initTechsData();

		$ret = array();
		if ($inactive) {
			$ret = $this->techs;
		} else {
			$ret = $this->techs_active;
		}

		if (!$simpletechs) {
			$techs = array();
			foreach ($ret as $k => $v) {
				if ($v['deny_normal_access']) {
					continue;
				}

				$techs[$k] = $v;
			}
			$ret = $techs;
		}

		return $ret;
	}

	protected function _initTechsData()
	{
		if (is_array($this->techs)) {
			return;
		}

		if ($this->techs = $this->getCache()->load('techs')) {
			$this->techs_active = array();
			foreach ($this->techs as $t) {
				if ($t['active']) {
					$this->techs_active[$t['id']] = $t;
				}
			}
			return;
		}

		global $db, $settings;

		$this->techs = array();
		$this->techs_active = array();

		$db->query("
			SELECT * FROM tech
			ORDER BY display_name, username
		");

		if (!$db->num_rows()) {
			trigger_error('There are no technicians in the database. Was the ' . DP_NAME . ' installation completed?', E_USER_ERROR);
		}

		while ($tech = $db->row_array()) {

			$tech['info_dismiss'] = @unserialize($tech['info_dismiss']);

			if (!$tech['info_dismiss']) {
				$tech['info_dismiss'] = array();
			}

			$tech['cats_admin_array'] = explode(',', $tech['cats_admin']);
			$tech['cats_user_array'] = explode(',', $tech['cats_user']);

			$this->techs[$tech['id']] = $tech;

			if ($tech['active']) {
				$this->techs_active[$tech['id']] = $tech;
			}
		}

		$this->getCache()->save($this->techs, 'techs');
	}

	/**
	* get ticket fields
	*
	* @access	public
	*
	* @param	string	user type - user / tech
	* @param	string	user permission
	*
	* @return	 mixed|array	details of ticket
	*/
	function getFaqFields($type = 'tech', $permission = false) {

		global $db;

		$output = array();

		if (!is_array($this->faqfields)) {
			$this->faqfields = $db->query_return_array_id("
				SELECT * FROM faq_def ORDER BY displayorder
			");
		}

		// check we have a array now, or just return empty array
		if (!is_array($this->faqfields)) {
			return array();
		}

		if (!$permission) {
			return $this->faqfields;
		}

		if ($type == 'user') {

			return $this->returnFieldsPermissions('faq', $permission);

		} elseif ($type == 'tech') {

			foreach ($this->faqfields AS $key => $var) {
				if ($var[$permission]) {
					$output[$key] = $var;
				}
			}

			return $output;
		}
	}

	/**
	 * Get calendar fields
	 *
	 * @return array The ticket fields
	 */
	function getCalendarFields() {
		global $db;

		static $complete = false;

		if (!$complete) {
			$this->calfields = $db->query_return_array_id("SELECT * FROM calendar_def ORDER BY displayorder", '', 'name');
			$complete = true;
		}

		return $this->calfields;
	}

	/**
	* get ticket fields
	*
	* @access	public
	*
	* @param	string	user type - user / tech
	* @param	string	user permission
	*
	* @return	 mixed|array	details of ticket
	*/
	function getTicketFields($permission = FALSE) {

		global $db;

		$this->_initTicketFieldsData();

		/*************************
		* Return the data
		*************************/

		// check we have a array now, or just return empty array
		if (!is_array($this->ticketfields)) {
			return array();
		}

		// permission restriction (user view/edit)
		if ($permission) {

			return $this->returnFieldsPermissions('ticket', $permission);

		// no permission
		} else {
			return $this->ticketfields;
		}
	}

	protected function _initTicketFieldsData()
	{
		static $complete = false;
		if ($complete) {
			return;
		}

		$complete = true;

		$this->getCategories();

		// from the cache
		if ($fielddata = $this->getCache()->load('ticket_def')) {
			$this->ticketfields = $fielddata['ticketfields'];
			$all_fieldids = array_keys($this->ticketfields);
			$cat_to_fields = $fielddata['cat_to_fields'];
		} else {

			global $db;

			/*************************
			* Get the fields
			*************************/

			$db->query("SELECT * FROM ticket_def ORDER BY displayorder");

			$all_fieldids = array();

			$this->ticketfields = array();
			while ($field = $db->row_array()) {
				$this->ticketfields[$field['name']] = $field;
				if (!$field['is_global']) {
					$all_fieldids[] = $field['id'];
				}
			}

			/*************************
			* Get the cat->fields relationships
			*************************/

			$cat_to_fields = array();

			$db->query("SELECT * FROM ticket_def_cat", 'fieldid', 'catid');
			while ($result = $db->row_array()) {
				if (!$this->ticketfield_cats[$result['fieldid']]['is_global']) {
					$cat_to_fields[$result['catid']][] = $result['fieldid'];
				}
			}

			$this->getCache()->save(array('ticketfields' => $this->ticketfields, 'cat_to_fields' => $cat_to_fields), 'ticket_def');
		}

		// $all_fieldids and $cat_to_fields do not include global fields.
		// Global fields are handled a little differently in the system, they dont apply
		// to all the various per-category rules (they are like oldschool fields). So we dont
		// include them in the array :)

		/*************************
		* No handle categories
			- we do categories with no parents first so that we can handle inheritance
		*************************/

		$this->ticketfield_cats = array();

		foreach ($this->getCategories() as $catid => $cat) {
			if ($cat['parent']) {
				continue;
			}

			// all fields for this category
			if ($cat['custom_all']) {
				$this->ticketfield_cats[$catid] = $all_fieldids;

			// specified fields
			} else {
				$this->ticketfield_cats[$catid] = $cat_to_fields[$catid];
			}
		}

		foreach ($this->getCategories() as $catid => $cat) {
			if (!$cat['parent']) {
				continue;
			}
			// all fields for this category
			if ($cat['custom_all']) {
				$this->ticketfield_cats[$catid] = $all_fieldids;

			// inherit from higher category
			} else if ($cat['custom_inherit']) {
				$this->ticketfield_cats[$catid] = $this->ticketfield_cats[$cat['parent']];

			// specified fields
			} else {
				$this->ticketfield_cats[$catid] = $cat_to_fields[$catid];
			}
		}
	}




	/**
	 * Get an array of global ticket fields
	 *
	 * @param unknown_type $permission
	 * @return unknown
	 */
	function getGlobalTicketFields($permission = false) {
		$fields = $this->getTicketFields($permission);

		$ret = array();

		foreach ($fields as $k => $field) {
			if ($field['is_global']) {
				$ret[$k] = $field;
			}
		}

		return $ret;
	}





	/**
	 * Get which fields are available for which cats.
	 *
	 * Most useful when you need to figure out which fields for which cats
	 * with a specific permission instead of a specific cat.
	 *
	 * @param unknown_type $permission
	 */
	function getTicketFieldsForCategories($permission = false) {

		$fields = $this->getTicketFields($permission);

		// Need to get ids, $fields is keyed by name
		$fieldids = array();
		foreach ($fields as $f) {
			$fieldids[] = $f['id'];
		}

		$fields_for_category = $this->ticketfield_cats;

		foreach ($fields_for_category as $catid => $fields) {
			$fields_for_category[$catid] = array_intersect($fieldids, (array)$fields);

			// Dont add it to the array if they dont need to
			if (!$fields_for_category[$catid]) {
				unset($fields_for_category[$catid]);
			}
		}

		return $fields_for_category;
	}

	/**
	 * Get the ticket fields that are set to display for a particular category
	 *
	 * @param integer $catid The category to get fields for
	 * @param string $type User type: tech, user, admin
	 * @param string $user_permission If a tech/user, what permission
	 * @param boolean $get_global To also add global fields
	 * @return unknown
	 */
	function getTicketFieldsCat($catid, $permission = FALSE, $include_global = false) {

		$fields = $this->getTicketFields($permission);
		$fields_for_category = $this->ticketfield_cats[$catid];

		if (is_array($fields_for_category)) {

			if ($include_global) {
				$return = $this->getGlobalTicketFields($permission);
			} else {
				$return = array();
			}

			foreach ($fields AS $name => $field) {
				if (in_array($field['id'], $fields_for_category)) {
					$return[$name] = $field;
				}
			}

			return $return;

		} else {

			if ($include_global) {
				return $this->getGlobalTicketFields($permission);
			} else {
				return array();
			}

		}
	}

	/**
	 * Get which categories have a ticket field enabled.
	 *
	 * @param integer $fieldid The field to get cats for
	 * @return array
	 */
	function getCatsForTicketField($fieldid) {

		$fields = $this->getTicketFields();
		$enabled_cats = array();

		foreach ($this->ticketfield_cats as $catid => $catfields) {
			if (is_array($catfields) AND in_array($fieldid, $catfields)) {
				$enabled_cats[] = $catid;
			}
		}

		return $enabled_cats;
	}

	/**
	* get user details fields
	*
	* @access	public
	*
	* @param	string	user type - user / tech
	*
	* @return	 mixed|array	details of user
	*/
	function getUserFields($permission = FALSE) {

		$this->_initUserFieldsData();

		$output = array();

		// check we have a array now, or just return empty array
		if (!is_array($this->userfields)) {
			return array();
		}

		// permission restriction (user view/edit)
		if ($permission) {

			return $this->returnFieldsPermissions('user', $permission);

		// no permission
		} else {
			return $this->userfields;
		}
	}

	protected function _initUserFieldsData()
	{
		static $complete = false;
		if ($complete) {
			return;
		}

		$complete = true;

		$this->userfields = $this->getCache()->load('user_def');
		if (is_array($this->userfields)) {
			return;
		}

		global $db;

		$this->userfields = $db->query_return_array_id("
			SELECT * FROM user_def ORDER BY displayorder
		", '', 'name');
		if (!$this->userfields) {
			$this->userfields = array();
		}

		$this->getCache()->save($this->userfields, 'user_def');
	}


	/**
	 * Get company fields
	 */
	function getCompanyFields() {

		$this->_initCompanyFieldsData();

	    return $this->companyfields;
	}

	protected function _initCompanyFieldsData()
	{
		static $complete = false;
		if ($complete) {
			return;
		}

		global $db;

		$complete = true;

		$this->companyfields = $this->getCache()->load('user_company_def');
		if (is_array($this->companyfields)) {
			return;
		}

		$this->companyfields = $db->query_return_array_id("
        	SELECT * FROM user_company_def ORDER BY displayorder
        ", '', 'id');

		$this->getCache()->save($this->companyfields, 'user_company_def');
	}


	/**
	 * Get which fields the user has permission to see or edit
	 *
	 * @param string $permission Either 'edit' or 'view'
	 * @param array|id $company The company id
	 * @param User $userobj The user object of the user
	 */
	function getCompanyFieldsPermission($permission = 'view', $company, $userobj) {

	    $this->getCompanyFields();

	    if (!$this->companyfields) {
	        return array();
	    }

	    $ret = array();

	    if (is_array($company)) {
	        $company = $company['id'];
	    }

	    if ($permission == 'view') {
	        $permission = 'p_company_field_view';
	        $field = 'perm_user_view';
	    } else {
	        $permission = 'p_company_field_edit';
	        $field = 'perm_user_edit';
	    }

	    foreach ($this->companyfields as $id => $company) {
	        if ($company[$field] == 'user') {
	            $ret[$id] = $company;
	        } elseif ($company[$field] == 'role' AND $userobj->companyPerm($id, $permission)) {
	            $ret[$id] = $company;
	        }
	    }

	    return $ret;
	}





	/**
	 * Get the chat departments
	 *
	 * @return array
	 */
	function getChatDeps() {

		if (!is_array($this->chat_deps)) {

			global $db;

			$this->chat_deps = $db->query_return_array_id("
				SELECT * FROM chat_dep
				ORDER BY displayorder ASC
			");
		}

		return $this->chat_deps;
	}





	/**
	 * Get a chat department
	 *
	 * @param integer $depid The department to get
	 * @return array|boolean Array of details or false if it doesnt exist
	 */
	function getChatDep($depid) {

		if (!is_array($this->chat_deps)) {
			$this->getChatDeps();
		}

		if (isset($this->chat_deps[$depid])) {
			return $this->chat_deps[$depid];
		}

		return false;
	}




	/**
	 * Get the name of a chat department
	 *
	 * @param integer $depid The department to get
	 * @return string|bool The name or false if it doesnt exist
	 */
	function getChatDepName($depid) {

		$dep = $this->getChatDep($depid);

		if (!$dep) {
			return false;
		}

		return $dep['name'];
	}





	/**
	 * Get the names for chat departments (ie for use in a select)
	 *
	 * @param string|array Optionally add an item (string will mean index 0) or items (keys will be indexes).
	 * @return array Array of names
	 */
	function getChatDepNames($add_top = array()) {

		if (!is_array($this->chat_deps)) {
			$this->getChatDeps();
		}

		if ($add_top) {
			$ret = (array)$add_top;
		} else {
			$ret = array();
		}

		if ($this->chat_deps) {
			foreach ($this->chat_deps as $depid => $dep) {
				$ret[$depid] = $dep['name'];
			}
		}

		return $ret;
	}





	/**
	* get all languages
	*
	* @access	public
	*
	* @param	boolean	flag to return all langauges or only selectable languages
	*
	* @return	 mixed|array	languages
	*/
	function getLanguages($restrict='') {

		$this->_initLanguagesData();

		if (!$restrict) {

			return $this->languages;

		// return only use languages
		} else {

			foreach ($this->languages AS $key => $var) {
				if ($var['is_selectable'] OR $var['base']) {
					$data[] = $var;
				}
			}
			return $data;
		}
	}

	protected function _initLanguagesData()
	{
		global $db;

		if (is_array($this->languages)) {
			return;
		}

		if ($this->languages = $this->getCache()->load('languages')) {
			return;
		}

		$this->languages = $db->query_return_array_id("
			SELECT * FROM languages
		");

		if (!is_array($this->languages)) {
			$this->languages = array();
		}

		$this->getCache()->save($this->languages, 'languages');
	}

	/**
	* get the id for the default language
	*
	* @access	public
	*
	* @return	int	language id
	*/
	function getDefaultLanguageID() {

		$this->getLanguages();

		foreach ($this->languages AS $key => $var) {
			if ($var['base'] == 1) {
				return $key;
			}
		}

		// if we got here we have no default language. Fatal error
		trigger_error('No default language defined');

	}

	/**
	* get all fields names to display about a ticket
	*
	* @access	public
	*
	* @return	 string|array	field names
	*/
	function getDisplayFields() {

		$this->_initDisplayFieldsData();

		return $this->displayfields;

	}

	protected function _initDisplayFieldsData()
	{
		global $db;

		if (is_array($this->displayfields)) {
			return;
		}

		$this->displayfields = $this->getCache()->load('ticket_fielddisplay');
		if (is_array($this->displayfields)) {
			return;
		}

		$this->displayfields = $db->query_return_array_id("
			SELECT * FROM ticket_fielddisplay
		");
		if (!is_array($this->displayfields)) $this->displayfields = array();

		$this->getCache()->save($this->displayfields, 'ticket_fielddisplay');
	}

	/**
	* get all fields names to display about a ticket
	*
	* @access	public
	*
	* @param	string	type of user - user / tech
	*
	* @return	 string|array	field names
	*/
	function getDisplayFieldsNames($type = '') {

		$this->getDisplayFields();

		if (is_array($this->displayfields)) {
			foreach ($this->displayfields AS $result) {
				$data[$result['name']] = $result['code'];
			}
		}

		return $data;

	}

	/**
	* get all fields names about a ticket details
	*
	* @access	public
	*
	* @param	string	user permission to get
	*
	* @return	 string|array	field names
	*/
	function getTicketFieldsNames($permission = null) {

		$fields = $this->getTicketFields($permission);

		if (is_array($fields)) {
			foreach ($fields AS $result) {
				$data[$result['name']] = $result;
			}
		}

		return $data;
	}

	/**
	* get all fields names about a user details
	*
	* @access	public
	*
	* @param	string	type of user - user / tech
	*
	* @return	 string|array	field names
	*/
	function getUserFieldsNames() {

		$fields = $this->getUserFields();

		if (is_array($fields)) {
			foreach ($fields AS $result) {
				$data[$result['name']] = $result;
			}
		}

		return $data;

	}

	/**
	* get status names
	*
	* @access	public
	*
	* @param	string|array	status that should be at top of status array
	*
	* @return	 string|array	all status
	*/
	function getStatusNames($top = '') {

		// add top entry
		if (is_array($top)) {
			$data[$top['0']] = $top['1'];
		}

		$data['awaiting_tech'] = 'Awaiting Tech';
		$data['awaiting_user'] = 'Awaiting User';
		$data['closed'] = 'Closed';
		$data['nodisplay'] = 'Hidden/Pending';

		return $data;

	}

	/**
	* get status names
	*
	* @access	public
	*
	* @param	string|array	status that should be at top of status array
	*
	* @return	 string|array	all status
	*/
	function getNodisplayNames($top = '') {

		// add top entry
		if (is_array($top)) {
			$data[$top['0']] = $top['1'];
		}

		$data['spam'] = 'Spam';
		$data['spam_auto'] = 'Ticket must be spam that was classified using the spam filter';
		$data['validate_user'] = 'Awaiting User Validation';
		$data['validate_tech'] = 'Awaiting Tech Validation';
		$data['kb_suggest'] = 'Waiting for user to go through knowledgebase suggestions';
		$data['buy_ticket'] = 'Waiting for user to pay ticket invoice';

		return $data;

	}

	/**
	* get all allowed categories only
	*
	* @access	public
	*
	* @param	boolean	flag whether to add "None" as first in list
	* @param	string|array	 category that has to be first in list
	*
	* @return	 string|array	categories
	*/
	function getCategoryNamesPermission($none = true, $top = '', $parent = -1, $full = false, $full_sep = ' &gt; ', $which = 'both', $allow_catids = array()) {

		global $user;

		// not allowed categories
		if ($which == 'both') {
			$notallowed = array_merge(explode(',', $user['cats_admin']), explode(',', $user['cats_user']));
		} else {
			$notallowed = explode(',', $user['cats_admin']);
		}

		$add_notallowed = array();

		foreach ($notallowed as $catid) {
			if ($catid != 0) {
				$add_notallowed = array_merge($add_notallowed, $this->getCategoryChildren($catid));
			}
		}

		$notallowed = array_unique(array_merge($notallowed, $add_notallowed));

		// if 0 is not allowed, we unset any $none option
		if (in_array('0', $notallowed) AND !in_array('0', $allow_catids)) {
			$none = FALSE;
		}

		// get all category names
		$categories = $this->getCategoryNames($none, $top, $parent, $full, $full_sep);

		$allow_these = array();
		$allow_catids = (array)$allow_catids;

		if ($allow_catids) {
			foreach ($allow_catids as $catid) {
				$cat = $this->getCategory($catid);

				$allow_these[] = $catid;

				if ($cat['parent']) {
					$allow_these[] = $cat['parent'];
				}
			}
		}

		// remove them
		foreach ($notallowed AS $var) {
			if (!in_array($var, $allow_these)) {
				unset($categories[$var]);
			}
		}

		return $categories;

	}

	/*
		var $none:
			TRUE / FALSE / KEY / ARRAY
				if TRUE we show 0 => 'None'
				if FALSE we show nothing
				if string we use it as key
				if array we replace both
	*/

	/**
	* get all categories
	*
	* @access	public
	*
	* @param	boolean	flag whether to add "None" as first in list
	* @param	string|array	 category that has to be first in list
	*
	* @return	 string|array	categories
	*/
	function getCategoryNames($none = true, $top = '', $parent = -1, $full = false, $full_sep = ' &gt; ') {

		$this->getCategories();

		// add more top entries
		if (is_array($top)) {
			$data[$top['0']] = $top['1'];
		}

		// first entry
		if ($none === TRUE) {
			$data['0'] = 'None';
		} elseif ($none == FALSE) {

		} elseif (is_array($none)) {
			$data[$none['0']] = $none['1'];
		} else {
			$data[$none] = 'None';
		}

		foreach ($this->categories AS $result) {
			if ($parent > -1 AND $result['parent'] != $parent) {
				continue;
			}

			if ($full AND $result['parent']) {
				$parent_cat = $this->getCategory($result['parent']);
				$result['name'] = $parent_cat['name'] . $full_sep . $result['name'];
			}
			$data[$result['id']] = $result['name'];
		}

		return $data;

	}


	/**
	 * Get a category by ID. If the category is a subcategory, the 'full name' will
	 * be placed into the array with the key 'full_name'.
	 *
	 * @param int $id The ID of the category
	 * @param string $fullname_sep If the category is a subcategory, the full name
	 *                             is automatically placed into index full_name. You can specify the separator
	 *
	 * @return array|bool The array of cat info or false if it doesn't exist
	 */
	function getCategory($id, $fullname_sep = '&gt;') {

		if (!is_array($this->categories)) {
			$this->getCategories();
		}

		if (isset($this->categories[$id])) {
			$cat = $this->categories[$id];
			$cat['full_name'] = $cat['name'];

			if ($cat['parent']) {
				$parent_cat = $this->getCategory($cat['parent']);
				$cat['full_name'] = $parent_cat['name'] . " $fullname_sep " . $cat['name'];
			}

			return $cat;
		} else {
			return false;
		}
	}

	/**
	 * Get the children of a category
	 *
	 * @param integer $id The parent category to get children for
	 * @return array
	 */
	function getCategoryChildren($id) {

		if (!is_array($this->categories)) {
			$this->getCategories();
		}

		if (isset($this->category_parents[$id])) {
			return $this->category_parents[$id];
		}

		return array();
	}


	/**
	* get priority names
	*
	* @access	public
	*
	* @param	boolean	flag whether to add "None" as first in list
	* @param	string|array	 category that has to be first in list
	*
	* @return	 string|array	 priorities
	*/
	function getPriorityNames($none = true, $top = '') {

		$this->getPriorities();

		// add more top entries
		if (is_array($top)) {
			$data[$top['0']] = $top['1'];
		}

		// first entry
		if ($none === TRUE) {
			$data['0'] = 'None';
		} elseif ($none == FALSE) {

		} elseif (is_array($none)) {
			$data[$none['0']] = $none['1'];
		} else {
			$data[$none] = 'None';
		}

		foreach ($this->priorities AS $result) {
			$data[$result['id']] = $result['name'];
		}

		return $data;

	}

	/**
	* get all priority colors
	*
	* @access	public
	*
	* @param	boolean	flag whether to add "None" as first in list
	* @param	string|array	 category that has to be first in list
	*
	* @return	 string|array	 priority colors
	*/
	function getPriorityColors($none = true, $top = '') {

		$this->getPriorities();

		foreach ($this->priorities AS $result) {
			$data[$result['id']] = $result['color'];
		}

		return $data;

	}

	/**
	* get all workflow names
	*
	* @access	public
	*
	* @param	boolean	flag whether to add "None" as first in list
	* @param	string|array	 category that has to be first in list
	*
	* @return	 string|array	 workflow names
	*/
	function getWorkflowNames($none = true, $top = '') {

		$this->getWorkflows();

		// add more top entries
		if (is_array($top)) {
			$data[$top['0']] = $top['1'];
		}

		// first entry
		if ($none === TRUE) {
			$data['0'] = 'None';
		} elseif ($none == FALSE) {

		} elseif (is_array($none)) {
			$data[$none['0']] = $none['1'];
		} else {
			$data[$none] = 'None';
		}

		foreach ($this->workflows AS $result) {
			$data[$result['id']] = $result['name'];
		}

		return $data;

	}

	function getTechName($id, $active = true, $display_name = null) {

		global $settings;

		$this->getTechs();

		if ($id == -1) {
			return 'Helpdesk System';
		}
		if ($id == 0) {
			return 'None';
		}

		if ($active) {
			$techs = $this->techs_active;
		} else {
			$techs = $this->techs;
		}

		if (!isset($techs[$id])) {
			return null;
		}

		$tech = $techs[$id];

		if ($settings['tech_display_name'] AND is_null($display_name)) {
			$display_name = true;
		}

		if (!$display_name) {
			return $tech['username'];
		} else {
			return $tech['display_name'];
		}

	}

	/**
	* get all technician names
	*
	* @access	public
	*
	* @param	boolean	flag whether to add "None" as first in list
	* @param	string|array	$top	 category that has to be first in list
	*
	* @return	 string|array	 technician names
	*/
	function getTechNames($none = true, $top = '', $active = true, $display_name = null, $simpletechs = false) {

		global $settings;

		$this->getTechs();

		// add more top entries
		if (is_array($top)) {
			$data[$top['0']] = $top['1'];
		}

		// first entry
		if ($none === TRUE) {
			$data['0'] = 'None';
		} elseif ($none == FALSE) {

		} elseif (is_array($none)) {
			$data[$none['0']] = $none['1'];
		} else {
			$data[$none] = 'None';
		}

		if ($active) {
			$techs = $this->techs_active;
		} else {
			$techs = $this->techs;
		}

		if (defined('TECHZONE') or defined('ADMINZONE') AND $settings['tech_display_name'] AND is_null($display_name)) {
			$display_name = true;
		}

		foreach ($techs AS $result) {
			if (!$simpletechs AND $result['deny_normal_access']) {
				continue;
			}

			if (!$display_name) {
				$data[$result['id']] = $result['username'];
			} else {
				$data[$result['id']] = $result['display_name'];
			}
		}

		return $data;

	}

	/**
	* get technician names for ticketsearching for tech
	*
	* @access	public
	*
	* @param	boolean	flag whether to add "None" as first in list
	*
	* @param	string|array	 category that has to be first in list
	*
	* @return	 string|array	 technician names
	*/
	function getTechNamesP($none = true, $top = '', $active = true, $simpletechs = false) {

		global $user;

		$this->getTechs();

		// add more top entries
		if (is_array($top)) {
			$data[$top['0']] = $top['1'];
		}

		if ($user['p_unassigned_view']) {

			// first entry
			if ($none === TRUE) {
				$data['0'] = 'None';
			} elseif ($none == FALSE) {

			} elseif (is_array($none)) {
				$data[$none['0']] = $none['1'];
			} else {
				$data[$none] = 'None';
			}

		}

		if ($user['p_tech_view']) {

			if ($active) {
				$techs = $this->techs_active;
			} else {
				$techs = $this->techs;
			}

			foreach ($techs AS $result) {
				if (!$simpletechs AND $result['deny_normal_access']) {
					continue;
				}
				$data[$result['id']] = $result['username'];
			}

		} else {
			$data[$user['id']] = $user['username'];
		}

		return $data;

	}

	/**
	* get ticket view names
	*
	* @access	public
	*
	* @return	 string|array	 view names
	*/
	function getTicketViewNames() {

		$this->getTicketViews();

		$data = array('-1' => DP_NAME . ' Standard');

		foreach ($this->ticketviews AS $result) {
			$data[$result['id']] = $result['name'];
		}

		return $data;

	}

	/**
	* get language names
	*
	* @access	public
	*
	* @return	 string|array	 language names
	*/
	function getLanguageNames($none = false, $exclude_translator = true) {

		$this->getLanguages();

		// first entry
		if ($none === TRUE) {
			$data['0'] = '';
		}

		foreach ($this->languages AS $result) {
			if ($exclude_translator AND ($result['deskproid'] AND !$result['is_selectable'])) {
				continue;
			}

			$data[$result['id']] = $result['name'];
		}

		return $data;

	}

	/**
	* get language based on id
	*
	* @access	public
	*
	* @param	int	id of langauge
	*
	* @return	 mixed|array	 language details
	*/
	function getLanguage($id) {

		$this->getLanguages();
		return $this->languages[$id];

	}

	/**
	* get style based on id
	*
	* @access	public
	*
	* @param	int	id of style
	*
	* @return	 mixed|array	 style details
	*/
	function getStyle($id) {

		$this->getStyles();
		return $this->styles[$id];

	}

	function getDefaultStyle() {

		$this->getStyles();
		foreach ($this->styles AS $style) {
			if ($style['is_default']) {
				return $style;
			}
		}

		// no default style
		trigger_error('There is no default style');

	}

	/**
	* get technician based on id
	*
	* @access	public
	*
	* @param	int	id of tech
	*
	* @return	 mixed|array	 tech details
	*/
	function getTech($id, $active = true) {

		$this->getTechs();

		if ($active) {
			$techs = $this->techs_active;
		} else {
			$techs = $this->techs;
		}

		return $techs[$id];

	}

	/**
	* get category name based on id
	*
	* @access	public
	*
	* @param	int	id of category
	*
	* @return	 string	category name
	*/
	function categoryName($id, $none= false, $full = false, $full_sep = ' &gt; ') {

		if ($none AND !$id) {
			return 'None';
		}

		if (!is_array($this->categories)) {
			$this->getCategories();
		}

		if ($full) {
			$cat = $this->getCategory($id);
			if ($cat['parent']) {
				$parent = $this->getCategory($cat['parent']);
				return $parent['name'] . $full_sep . $cat['name'];
			} else {
				return $cat['name'];
			}
		} else {
			return $this->categories[$id]['name'];
		}
	}

	/**
	* get priority name based on id
	*
	* @access	public
	*
	* @param	int	id of priority
	*
	* @return	string	priority name
	*/
	function priorityName($id, $none='') {

		if ($none AND !$id) {
			return 'None';
		}

		if (!is_array($this->priorities)) {
			$this->getPriorities();
		}

		return $this->priorities[$id]['name'];
	}

	/**
	* get work flow name based on id
	*
	* @access	public
	*
	* @param	int	id of work flow
	*
	* @return	string	workflow name
	*/
	function workflowName($id, $none='') {

		if ($none AND !$id) {
			return 'None';
		}

		if (!is_array($this->workflows)) {
			$this->getWorkflows();
		}

		return $this->workflows[$id]['name'];
	}

	/**
	* get tech name based on id
	*
	* @access	public
	*
	* @param	int	id of tech
	*
	* @return	string	tech name
	*/
	function techName($id, $none='', $active = true) {

		if ($none AND !$id) {
			return 'None';
		}

		$this->getTechs();

		if ($active) {
			$techs = $this->techs_active;
		} else {
			$techs = $this->techs;
		}

		return $techs[$id]['username'];
	}


	/**
	* get all bad words
	*
	* @access	public
	*
	* @return	string|array	bad words
	*/
	function getBadwords() {

		require(INC . 'data/badwords.php');
		return $badwords;

	}

	/**
	*
	*
	* @access	public
	*
	* @param	int
	* @param	int
	*
	* @return
	*/
	function getCalendarData($key, $key2='') {

		require_once(INC . 'data/calendar.php');

		if (is_numeric($key2)) {
			$key2 = intval($key2);
			return($this->calendardata[$key][$key2]);
		} else {
			return $this->calendardata[$key];
		}
	}

	/**
	*
	*
	* @access	public
	*
	*/
	function getWeekDays($start) {

		require_once(INC . 'data/calendar.php');

	}

	/**
	*
	*
	* @access	public
	*
	*/
	function getGatewayEmail($id) {

		$this->cacheGatewayEmails();
		return $this->gateway_emails[$id];

	}

	/**
	*
	*
	* @access	public
	*
	*/
	function getGatewayEmailDefault() {

		$this->cacheGatewayEmails();

		if (!is_array($this->gateway_emails)) {
			return false;
		}

		foreach ($this->gateway_emails AS $email) {
			if ($email['is_default']) {
				return $email;
			}
		}

		return false;

	}





	/**
	 * Get all of the active user sources in the database.
	 *
	 * @return array
	 */
	function getUsersources($active = true) {

		$this->_initUserSourcesData();

		if ($active) {
			$ret = array();

			foreach ($this->usersources as $source) {
				if ($source['enabled']) {
					$ret[$source['id']] = $source;
				}
			}
		} else {
			$ret = $this->usersources;
		}

		return $ret;
	}

	protected function _initUserSourcesData()
	{
		if (is_array($this->usersources)) {
			return;
		}

		// Fetched it from cache
		if ($this->usersources = $this->getCache()->load('user_sources')) {
			$this->usersourcetypes = array();
			foreach ($this->usersources as $s) {
				if ($s['enabled']) {
					$type = strtolower($s['module']);
					$this->usersourcetypes[] = $type;
				}
			}
			return;
		}

		global $db;

		$db->query("
			SELECT *
			FROM user_source
			ORDER BY runorder ASC
		");

		$this->usersources = array();
		$this->usersourcetypes = array();

		while ($source = $db->row_array()) {

			$source['config'] = trim($source['config']);
			$source['config'] = ($source['config'] ? unserialize($source['config']) : array());

			$this->usersources[$source['id']] = $source;

			if ($source['enabled']) {
				$type = strtolower($source['module']);
				$this->usersourcetypes[] = $type;
			}
		}

		$this->usersourcetypes = array_unique($this->usersourcetypes);

		$this->getCache()->save($this->usersources, 'user_sources');
	}





	/**
	 * Get a specific user source from the name of the source.
	 *
	 * @param integer $name The ID of the source to get
	 * @return array|boolean The array of source details or false if it doesnt exist
	 */
	function getUsersource($id) {

		$this->getUsersources(false);

		if (isset($this->usersources[$id])) {
			return $this->usersources[$id];
		}

		return false;
	}





	/**
	 * Get the DeskPRO usersource
	 *
	 * @param boolean $active Only return it if its active?
	 * @return array
	 */
	function getDeskproUsersource($active = true) {

		$dpsource = array_shift($this->getUsersourcesOfType('dp', $active));

		if ($dpsource) {
			return $dpsource;
		}

		return false;
	}





	/**
	 * Get all of the sources of a certain type.
	 *
	 * @param string $type The type
	 * @return array
	 */
	function getUsersourcesOfType($type, $active = true) {

		$type = strtolower($type);
		$ret = array();

		foreach ($this->getUsersources($active) as $info) {
			if (strtolower($info['module']) == $type) {
				$ret[] = $info;
			}
		}

		return $ret;
	}





	/**
	 * Check to see if a usersouce type is enabled.
	 *
	 * @param string $type The type (module). Ie 'dp' or 'custom'
	 * @return boolean
	 */
	function usersourceTypeEnabled($type) {
		$this->getUsersources(false);
		return in_array(strtolower($type), $this->usersourcetypes);
	}

	protected function _initBasicTicketProperties()
	{
		// Meaves we've already done this
		if (is_array($this->categories)) {
			return;
		}

		global $db;

		$data = $this->getCache()->load('basic_ticket_props');
		if ($data) {
			$this->categories = $data['ticket_cat'];
			$this->workflows = $data['ticket_workflow'];
			$this->priorities = $data['ticket_pri'];
		}

		// CATEGORIES
		if (!is_array($this->categories)) {
			$this->categories = $db->query_return_array_id("
				SELECT * FROM ticket_cat ORDER BY displayorder
			");

			if (!$this->categories) $this->categories = array();
		}
		$this->category_parents = array();
		foreach ($this->categories as $cat) {
			$this->category_parents[$cat['parent']][] = $cat['id'];
		}

		if (defined('USERZONE')) $this->getUserCategoryPermissions();

		// WORKFLOWS
		if (!is_array($this->workflows)) {
			$this->workflows = $db->query_return_array_id("
				SELECT * FROM ticket_workflow ORDER BY displayorder
			");

			if (!$this->workflows)  $this->workflows = array();
		}

		// PRIORITIES
		if (!is_array($this->priorities)) {
			$this->priorities = $db->query_return_array_id("
				SELECT * FROM ticket_pri ORDER BY displayorder
			");

			if (!$this->priorities) $this->priorities = array();
		}
		if (defined('USERZONE')) $this->getUserPrioritiesPermissions();

		// save cache if we didnt have it
		if (!$data) {
			$this->getCache()->save(
				array(
					'ticket_cat' => $this->categories,
					'ticket_workflow' => $this->workflows,
					'ticket_pri' => $this->priorities,
				),
				'basic_ticket_props'
			);
		}
	}


	protected function _initBasicUserProperties()
	{
		// Means we've already done this
		if (is_array($this->usergroups)) {
			return;
		}

		global $db;

		$data = $this->getCache()->load('basic_user_props');
		if ($data) {
			$this->usergroups = $data['user_groups'];
			//$this->companies = $data['user_company'];
			$this->company_roles = $data['user_company_role'];
		}

		// USERGROUPS
		if (!is_array($this->usergroups)) {
			$this->usergroups = $db->query_return_array_id("SELECT * FROM user_groups");
			if (!$this->usergroups) $this->usergroups = array();
		}
		$this->usergroup_names = array();
		$this->namedusergroups = array();
		foreach ($this->usergroups as $group) {
			$this->usergroup_names[$group['id']] = $group['name'];

			if ($group['system_name']) {
				$this->namedusergroups[$group['system_name']] = $group['id'];
			}
		}

		// COMPANY ROLES
		if (!is_array($this->company_roles)) {
			$db->query("
				SELECT *
				FROM user_company_role
				ORDER BY name ASC
			");

			$this->company_roles = array();
			while ($r = $db->row_array()) {
				$r['overrides'] = unserialize($r['overrides']);
				$this->company_roles[$r['id']] = $r;
			}
		}

		// Set the cache if we didnt load from it
		if (!$data) {
			$this->getCache()->save(array('user_groups' => $this->usergroups, 'user_company_role' => $this->company_roles), 'basic_user_props');
		}
	}
}
