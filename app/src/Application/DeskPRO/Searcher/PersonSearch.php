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
*/

namespace Application\DeskPRO\Searcher;

use Application\DeskPRO\App;

use Application\DeskPRO\BigMode;
use Orb\Util\Util;
use Orb\Util\Strings;
use Orb\Util\Arrays;

use Application\DeskPRO\Entity;

class PersonSearch extends SearcherAbstract
{
	const MODE_ANY = 'any';
	const MODE_USER = 'user';
	const MODE_AGENT = 'agent';

	// These are all prefixed with person_ because this searcher
	// can be combined with the TicketSearch, so we need to namespace
	// these term names.

	const TERM_ID                 = 'person_id';
	const TERM_ORGANIZATION       = 'person_organization';
	const TERM_ORGANIZATION_NAME  = 'person_organization_name';
	const TERM_LANGUAGE           = 'person_language';
	const TERM_USERGROUP          = 'person_usergroup';
	const TERM_EMAIL              = 'person_email';
	const TERM_EMAIL_DOMAIN       = 'person_email_domain';
	const TERM_NAME               = 'person_name';
	const TERM_USERNAME           = 'person_username';
	const TERM_PERSON_FIELD       = 'person_field';
	const TERM_LABEL              = 'person_label';
	const TERM_DATE_CREATED       = 'person_date_created';
	const TERM_DIRECTORY_NAME     = 'person_directory_name';
	const TERM_CONTACT_PHONE      = 'person_contact_phone';
	const TERM_CONTACT_ADDRESS    = 'person_contact_address';
	const TERM_CONTACT_IM         = 'person_contact_im';
	const TERM_ALPHA              = 'alphabetical';
	const TERM_IS_AGENT_CONFIRMED = 'is_agent_confirmed';
	const TERM_IS_CONFIRMED       = 'is_confirmed';
	const TERM_AGENT_TEAM         = 'person_agent_team';
	const TERM_AGENT_MODE         = 'agent_mode';
	const TERM_USER_MODE          = 'user_mode';
	const TERM_ANY_MODE           = 'any_mode';
	const TERM_IP_ADDRESS         = 'person_ip';

	/**
	 * From getSqlParts()
	 * @var array
	 */
	protected $sql_parts = null;

	/**
	 * Summary of terms in phrases
	 * @var array
	 */
	protected $summary = array();

	/**
	 * @var string
	 */
	protected $mode = 'user';


	/**
	 * Run the search and return an array of matching ID's.
	 *
	 * @param int $limit
	 * @return array
	 */
	public function getMatches()
	{
		$db = App::getDbRead();

		$people_ids = $db->fetchAllCol($this->getSql());

		return $people_ids;
	}



	/**
	 * Get the summary of crtiera
	 *
	 * @array
	 */
	public function getSummary()
	{
		$this->getSqlParts();
		return $this->summary;
	}


	/**
	 * @param string $mode MODE_USER or MODE_AGETN
	 */
	public function setMode($mode)
	{
		$this->mode = $mode;
	}



	/**
	 * Get the SQL query that'll fetch the results
	 * @return string
	 */
	public function getSql()
	{
		$sql = "SELECT people.id FROM people ";

		$parts = $this->getSqlParts();
		$order_by = $this->getOrderByPart();

		#------------------------------
		# Add joins
		#------------------------------

		foreach ($parts['joins'] as $j) {
			if (is_array($j)) {
				$sql .= $j[1] . " ";
			} else {
				$sql .= "LEFT JOIN $j ON $j.person_id = people.id ";
			}
		}

		if (is_array($order_by)) {
			$sql .= " {$order_by[0]} ";
			$order_by = $order_by[1];
		}

		#------------------------------
		# Add wheres
		#------------------------------

		if ($parts['wheres']) {
			$sql .= "WHERE ";
			$sql .= implode(" AND ", $parts['wheres']);
		}

		$sql .= " GROUP BY people.id ";
		if ($order_by) {
			$sql .= " ORDER BY $order_by ";
		}
		$sql .= " LIMIT 10000";

		return $sql;
	}



	/**
	 * Get the ORDER BY clause based on order info set.
	 *
	 * @return string
	 */
	public function getOrderByPart()
	{
		// Set a default if none
		if (!$this->order_by) {
			$this->order_by = array('people.id', 'DESC');
		}

		list($type, $dir) = $this->order_by;

		$dir = strtoupper($dir);
		if ($dir != self::ORDER_ASC AND $dir != self::ORDER_DESC) {
			$dir = self::ORDER_DESC;
		}

		$term_id = null;
		$m = null;
		if (preg_match('#^(.*?)\[(.*?)\]$#', $type, $m)) {
			$type = $m[1];
			$term_id = $m[2];
		}


		$order_by = '';

		switch ($type) {
			case 'people.name':
			case 'person.name':
				$order_by = "people.name $dir";
				break;

			case 'people.date_created':
				$order_by = "people.id $dir";
				break;

			case 'people.email':
				$order_by = array(
					"LEFT JOIN people_emails AS sort_table ON (sort_table.id = people.primary_email_id)",
					"sort_table.email $dir"
				);
				break;

			case 'people.organization':
				$order_by = array(
					"LEFT JOIN organizations AS sort_table ON (sort_table.id = people.organization_id)",
					"sort_table.name $dir"
				);
				break;

			case 'people.num_tickets':
				$order_by = array(
					"LEFT JOIN tickets AS sort_table ON (sort_table.person_id = people.id)",
					"COUNT(sort_table.id) $dir, people.id DESC"
				);
				break;

			case 'people.date_last_login':
				$order_by = "people.date_last_login $dir, people.id DESC";
				break;

			case 'people.people_field':
				$field = App::getEntityRepository('DeskPRO:CustomDefPerson')->find($term_id);
				if (!$field) break;

				$search_type = $field->getHandler()->getSearchType();

				switch ($search_type) {
					case 'input':
					case 'value':
						$order_by = arary(
							"INNER JOIN custom_data_person AS sort_table ON (sort_table.person_id = people.id AND sort_table.id = $term_id)",
							"sort_table.$search_type $dir"
						);
						break;
				}
				break;
		}

		return $order_by;
	}



	/**
	 * Get the SQL parts we need in the query.
	 *
	 * @return array
	 */
	public function getSqlParts()
	{
		if ($this->sql_parts !== null) return $this->sql_parts;

		$people_table = 'people';

		$db = App::getDbRead();
		$tr = App::getTranslator();

		$wheres = array();
		$joins = array();

		foreach ($this->terms as $info) {
			$join_id = Util::requestUniqueId();
			$join_name = "j_$join_id";

			list($term, $op, $choice) = $info;

			$term_id = null;

			// $term of people_field[12] becomes $term=people_field, $term_id=12
			$m = null;
			if (preg_match('#^(.*?)\[(.*?)\]$#', $term, $m)) {
				$term = $m[1];
				$term_id = $m[2];
			}

			switch ($term) {
                case self::TERM_ID:
					$wheres[] = $this->_rangeMatch("$people_table.id", $op, $choice, true);
					$this->summary[] = $this->_rangeSummary($tr->phrase('agent.general.id'), $op, $choice);
					break;
                case self::TERM_LANGUAGE:
					$this->summary[] = $this->_choiceSummary($tr->phrase('agent.general.language'), $op, $choice, function($choice) {
						$titles = App::getEntityRepository('DeskPRO:Language')->getTitles((array)$choice);
						return $titles;
					});

					$wheres[] = $this->_choiceMatch("$people_table.language_id", $op, $choice, true);
					break;
				case self::TERM_ORGANIZATION:
                    $this->summary[] = $this->_choiceSummary($tr->phrase('agent.general.organization'), $op, $choice, function($choice) {
						$titles = App::getEntityRepository('DeskPRO:Organization')->getOrganizationNames((array)$choice);
						return $titles;
					});
					$wheres[] = $this->_choiceMatch("$people_table.organization_id", $op, $choice);
					break;
				case self::TERM_ORGANIZATION_NAME:
					$joins[] = array(
						'organizations',
						"LEFT JOIN organizations AS $join_name ON ($join_name.id = people.organization_id)"
					);
					$wheres[] = $this->_stringMatch("$join_name.name", $op, $choice);
					break;
                case self::TERM_USERGROUP:
                    $this->summary[] = $this->_choiceSummary($tr->phrase('agent.general.usergroup'), $op, $choice, function($choice) {
						$titles = App::getEntityRepository('DeskPRO:Usergroup')->getUsergroupNames((array)$choice);
						return $titles;
					});

					$choice = array_map('intval', (array)$choice);
					$person_ids = App::getDbRead()->fetchAllCol("
						SELECT person_id
						FROM person2usergroups
						WHERE usergroup_id IN (" . implode(',', $choice) . ")
						LIMIT 1001
					");
					if (!$person_ids) {
						$person_ids = array(0);
					}
					$org_ids = App::getDbRead()->fetchAllCol("
						SELECT organization_id
						FROM organization2usergroups
						WHERE usergroup_id IN (" . implode(',', $choice) . ")
					");
					if (count($person_ids) == 1001) {
						// too many, need to do the join method
						$joins[] = array(
							'person2usergroups',
							"LEFT JOIN person2usergroups AS $join_name ON ($join_name.person_id = $people_table.id)"
						);
						if ($org_ids) {
							$wheres[] = '(' . $this->_choiceMatch("$join_name.usergroup_id", $op, $choice) . " OR $people_table.organization_id IN (" . implode(',', $org_ids) . "))";
						} else {
							$wheres[] = $this->_choiceMatch("$join_name.usergroup_id", $op, $choice);
						}
					} else {
						if ($org_ids) {
							$wheres[] = "($people_table.id IN (" .  implode(',', $person_ids) . ") OR $people_table.organization_id IN (" . implode(',', $org_ids) . "))";
						} else {
							$wheres[] = "$people_table.id IN (" .  implode(',', $person_ids) . ")";
						}
					}

					$this->mode = self::MODE_ANY;

					break;
				case self::TERM_EMAIL:
					$joins[] = array(
						'people_emails',
						"LEFT JOIN people_emails AS $join_name ON ($join_name.person_id = people.id)"
					);

					$suffix_only = false;
					if (BigMode::isBigMode(BigMode::PERSON_SEARCH_PREFIX_WILDCARD)) {
						$suffix_only = true;
					}

					$wheres[] = $this->_stringMatch("$join_name.email", $op, $choice, $suffix_only);

					$choice = implode(' or ', (array)$choice);
					$this->summary[] = "Email is " . $choice;

					break;
				case self::TERM_EMAIL_DOMAIN:

					if (is_array($choice) AND count($choice) == 1) {
						$choice = Arrays::getFirstItem($choice);
					}

					if (is_array($choice)) {
						foreach ($choice as &$x) {
							$x = ltrim($x, '@');
						}
						unset($x);
					} else {
						$choice = ltrim($choice, '@');
					}

					$suffix_only = false;
					if (BigMode::isBigMode(BigMode::PERSON_SEARCH_PREFIX_WILDCARD)) {
						$suffix_only = true;
					}

					$joins[] = array(
						'people_emails',
						"LEFT JOIN people_emails AS $join_name ON ($join_name.person_id = people.id)"
					);
					$wheres[] = $this->_stringMatch("$join_name.email_domain", $op, $choice, $suffix_only);

					$choice = implode(' or ', (array)$choice);
					$this->summary[] = "Email domain is " . $choice;
					break;

				case self::TERM_DATE_CREATED:
					$wheres[] = $this->_dateMatch("$people_table.date_created", $op, $choice);
					$this->summary[] = $this->_dateRangeSummary('User created', $op, $choice);
					break;

				case self::TERM_NAME:
					$w = '(';
					$w .= $this->_stringMatch("people.name", $op, $choice);
					$w .= " OR ";
					$w .= $this->_stringMatch("people.first_name", $op, $choice);
					$w .= " OR ";
					$w .= $this->_stringMatch("people.last_name", $op, $choice);
					$w .= ')';

					$wheres[] = $w;

					$choice = implode(' or ', (array)$choice);
					$this->summary[] = "Name is " . $choice;

					break;

				case self::TERM_USERNAME:
					$choice = (array)$choice;
					$choice = array_pop($choice);
					$this->summary[] = "Username is " . $choice;

					$joins[] = array(
						'person_usersource_assoc',
						"LEFT JOIN person_usersource_assoc AS $join_name ON ($join_name.person_id = people.id)"
					);
					$wheres[] = $this->_stringMatch("$join_name.identity_friendly", $op, $choice, true, true);

					break;

				case self::TERM_ALPHA:

					$wheres[] = $this->_stringMatch("people.last_name", $op, $choice, true, true);
					$this->summary[] = 'Name begins with ' . implode(', ', $choice);

					break;

				case self::TERM_DIRECTORY_NAME:

					if ($choice == 'OTHER') {
						$where[] = "people.last_name RLIKE '^[^A-Za-z]'";
					} else {
						$letter = $choice[0];
						if (!preg_match('#^[a-zA-Z]#', $letter)) {
							$letter = 'A';
						}

						$where[] = "people.last_name LIKE '%$letter'";
					}

					break;

				case self::TERM_CONTACT_PHONE:

					$choice = preg_replace('#[^0-9A-Za-z]#', '', $choice);
					$joins[] = array(
						'people_contact_data',
						"LEFT JOIN people_contact_data AS $join_name ON ($join_name.person_id = people.id AND $join_name.contact_type = 'phone')"
					);
					$wheres[] = $this->_stringMatch("$join_name.field_10", $op, $choice, false, true);

					$this->summary[] = $this->_choiceSummary('Phone Number', $op, $choice);
					break;

				case self::TERM_CONTACT_ADDRESS:
					$joins[] = array(
						'people_contact_data',
						"LEFT JOIN people_contact_data AS $join_name ON ($join_name.person_id = people.id AND $join_name.contact_type = 'address')"
					);
					$wheres[] = $this->_stringMatch("$join_name.field_1", $op, $choice, false, true);

					$this->summary[] = $this->_choiceSummary('Address', $op, $choice);
					break;

				case self::TERM_CONTACT_IM:

					$joins[] = array(
						'people_contact_data',
						"LEFT JOIN people_contact_data AS $join_name ON ($join_name.person_id = people.id AND $join_name.contact_type = 'instant_message')"
					);
					$wheres[] = $this->_stringMatch("$join_name.field_1", $op, $choice, false, true);

					$this->summary[] = $this->_choiceSummary('IM', $op, $choice);
					break;

				case self::TERM_LABEL:
					$this->_normalizeOpAndChoice($op, $choice);

					$choices_in = array();
					if (is_array($choice)) {
						foreach ((array)$choice as $c) {
							$choices_in[] = $db->quote($c);
						}
						$choices_in = implode(',', $choices_in);
					}

					$this->summary[] = $this->_choiceSummary($tr->phrase('agent.general.label'), $op, $choice);

					switch ($op) {
						case self::OP_IS:
							$joins[] = array(
								'labels_people',
								"LEFT JOIN labels_people AS $join_name ON ($join_name.person_id = people.id)"
							);
							$wheres[] = "$join_name.label = " . $db->quote($choice);
							break;
						case self::OP_NOT:
							$joins[] = array(
								'labels_people',
								"LEFT JOIN labels_people AS $join_name ON ($join_name.person_id = people.id AND $join_name.label = ".$db->quote($choice).")"
							);
							$wheres[] = "$join_name.person_id IS NULL";
							break;
						case self::OP_CONTAINS:
							$joins[] = array(
								'labels_people',
								"LEFT JOIN labels_people AS $join_name ON ($join_name.person_id = people.id)"
							);
							$wheres[] = "$join_name.label IN ($choices_in)";
							break;

						case self::OP_NOTCONTAINS:
							$joins[] = array(
								'labels_people',
								"LEFT JOIN labels_people AS $join_name ON ($join_name.person_id = people.id AND $join_name.label IN ($choices_in))"
							);
							$wheres[] = "$join_name.person_id IS NULL";
							break;
					}
					break;

				case self::TERM_IS_AGENT_CONFIRMED:

					if (is_array($choice)) {
						$choice = array_pop($choice);
					}

					if ($choice) {
						$choice = 1;
					} else {
						$choice = 0;
					}

					$wheres[] = $this->_choiceMatch("$people_table.is_agent_confirmed", $op, $choice, false);
					break;

				case self::TERM_IS_CONFIRMED:

					if (is_array($choice)) {
						$choice = array_pop($choice);
					}

					if ($choice) {
						$choice = 1;
					} else {
						$choice = 0;
					}

					$wheres[] = $this->_choiceMatch("$people_table.is_confirmed", $op, $choice, false);
					break;

				case self::TERM_PERSON_FIELD:

					$field = App::getEntityRepository('DeskPRO:CustomDefPerson')->find($term_id);
					if (!$field) break;

					$search_type = $field->getHandler()->getSearchType();

					if (isset($choice['custom_fields']['field_' . $term_id])) {
						$choice = $choice['custom_fields']['field_' . $term_id];
					}

					switch ($search_type) {
						case 'input':
						case 'value':

							$join_id = Util::requestUniqueId();
							$joins[] = array(
								'custom_data_person',
								"LEFT JOIN custom_data_person AS custom_data_person_$join_id ON (custom_data_person_$join_id.person_id = people.id AND custom_data_person_$join_id.field_id = $term_id)"
							);

							if (is_array($choice)) {
								$choice = array_pop($choice);
							}

							$field = 'custom_data_person_'.$join_id.'.'.$search_type;
							switch ($op) {
								case self::OP_IS:
									$wheres[] = "$field = " . $db->quote($choice);
									break;
								case self::OP_NOT:
									$wheres[] = "$field != " . $db->quote($choice);
									break;
								case self::OP_CONTAINS:
								case self::OP_NOTCONTAINS:
									$op = 'LIKE';
									if ($op == self::OP_NOTCONTAINS) $op = 'NOT LIKE';
									$wheres[] = "$field $op " . $db->quote('%'.$choice.'%');
									break;
							}
							break;

						case 'id':
							$join_id = Util::requestUniqueId();
							$choices_in = array();
							foreach ((array)$choice as $c) {
								$choices_in[] = (int)$c;
							}
							$choices_in = implode(',', $choices_in);

							$field = 'custom_data_person_'.$join_id.'.field_id';
							switch ($op) {
								case self::OP_CONTAINS:
								case self::OP_IS:
									$joins[] = array(
										'custom_data_person',
										"LEFT JOIN custom_data_person AS custom_data_person_$join_id ON (custom_data_person_$join_id.person_id = people.id AND $field IN ($choices_in))"
									);
									$wheres[] = "custom_data_person_$join_id.id IS NOT NULL";
									break;

								case self::OP_NOTCONTAINS:
								case self::OP_NOT:
									$joins[] = array(
										'custom_data_person',
										"LEFT JOIN custom_data_person AS custom_data_person_$join_id ON (custom_data_person_$join_id.person_id = people.id AND $field IN ($choices_in))"
									);
									$wheres[] = "custom_data_person_$join_id.id IS NULL";
									break;
							}
							break;
					}
					break; // end TERM_PERSON_FIELD

				case self::TERM_AGENT_TEAM:

					$this->setMode(self::MODE_AGENT);

					$joins[] = array(
						'agent_team_members',
						"LEFT JOIN agent_team_members AS $join_name ON ($join_name.person_id = people.id)"
					);

					$this->summary[] = $this->_choiceSummary("Agent Team", $op, $choice, function($choice) {
						$titles = App::getEntityRepository('DeskPRO:AgentTeam')->getTeamNames((array)$choice);
						return $titles;
					});

					$wheres[] = $this->_choiceMatch("$join_name.team_id", $op, $choice, true);

					break;

				case self::TERM_AGENT_MODE:
					$this->setMode(self::MODE_AGENT);
					break;

				case self::TERM_USER_MODE:
					$this->setMode(self::MODE_USER);
					break;

				case self::TERM_ANY_MODE:
					$this->setMode(self::MODE_ANY);
					break;

				case self::TERM_IP_ADDRESS:
					$joins[] = array(
						'tickets_messages',
						"LEFT JOIN tickets_messages AS $join_name ON ($join_name.person_id = people.id)"
					);

					$field = "$join_name.ip_address";

					$choice = is_array($choice) ? array_pop($choice) : $choice;
					$choice = preg_replace('#[^0-9\.]#', '', $choice);

					// If last char is a dot, then do a wildcard suffix search
					if (substr($choice, -1, 1) == '.') {
						$wheres[] = $this->_stringMatch($field, $op, $choice, true, true);
					} else {
						$wheres[] = $this->_stringMatch($field, $op, $choice);
					}
					break;

				default:
					throw new \InvalidArgumentException("Unknown term: $term");
			}
		}

		if ($this->mode != self::MODE_ANY) {
			if ($this->mode == self::MODE_AGENT) {
				$wheres[] = "people.is_agent = 1";
			} else {
				$wheres[] = "people.is_agent = 0";
			}
		}

		$wheres[] = 'people.is_deleted = 0';

		$this->sql_parts = array(
			'joins' => $joins,
			'wheres' => $wheres
		);

		return $this->sql_parts;
	}



	/**
	 * Check a specific person against these terms to see if it matches.
	 *
	 * @param Person $person
	 * @return bool
	 */
	public function doesPersontMatch(Entity\Person $person)
	{
		foreach ($this->terms as $info) {
			list($term, $op, $choice) = $info;

			switch ($term) {

				case self::TERM_DATE_CREATED:
					if (!$this->_testDateMatch($person['date_created'], $op, $choice)) return false;
					break;

				case self::TERM_ORGANIZATION:
					if (!$this->_testChoiceMatch($person['organization_id'], $op, $choice)) return false;
					break;

				case self::TERM_LANGUAGE:
					if (!$this->_testChoiceMatch($person['language_id'], $op, $choice)) return false;
					break;

				case self::TERM_NAME:
					if (is_array($choice)) {
						$choice = array_pop($choice);
					}

					switch ($op) {
						case self::OP_CONTAINS:
							if (strpos(strtolower($person['name']), strtolower($choice)) === false) return false;
							break;
						case self::OP_NOTCONTAINS:
							if (strpos(strtolower($person['name']), strtolower($choice)) !== false) return false;
							break;
					}
					break;

				case self::TERM_EMAIL:

					if ($op == self::OP_IS) $op = self::OP_CONTAINS;
					if ($op == self::OP_NOT) $op = self::OP_NOTCONTAINS;

					$any = false;
					if (is_array($choice)) {
						$choice = array_pop($choice);
					}
					foreach ($person['emails'] as $email) {
						if (strpos(strtolower($email['email']), strtolower($choice)) !== false) {
							$any = true;
							if ($op == self::OP_NOTCONTAINS) {
								return false;
							}
						}
					}

					if ($op == self::OP_CONTAINS AND !$any) {
						return false;
					}
					break;

				case self::TERM_EMAIL_DOMAIN:

					if ($op == self::OP_IS) $op = self::OP_CONTAINS;
					if ($op == self::OP_NOT) $op = self::OP_NOTCONTAINS;

					$any = false;
					if (is_array($choice)) {
						$choice = array_pop($choice);
					}
					$choice = ltrim($choice, '@');
					foreach ($person['emails'] as $email) {
						if (strpos(strtolower($email['email_domain']), strtolower($choice)) !== false) {
							$any = true;
							if ($op == self::OP_NOTCONTAINS) {
								return false;
							}
						}
					}

					if ($op == self::OP_CONTAINS AND !$any) {
						return false;
					}
					break;

				case self::TERM_USERGROUP:
					$any = false;

					$choice = isset($choice['usergroup']) ? (array)$choice['usergroup'] : array();

					foreach ($person->getUsergroupIds() as $ug_id) {
						if (in_array($ug_id, $choice)) {
							$any = true;
							break;
						}
					}

					if (($op == self::OP_IS || $op == self::OP_CONTAINS) AND !$any) {
						return false;
					}
					if (($op == self::OP_NOT || $op == self::OP_NOTCONTAINS) AND $any) {
							return false;
					}
					break;

				case self::TERM_ORGANIZATION:
					$name = $person->organization ? $person->organization->name : '';
					$choice = isset($choice['name']) ? (array)$choice['name'] : array();
					if (!$this->_testStringMatch($name, $op, $choice)) {
						return false;
					}

					break;

				case self::TERM_CONTACT_ADDRESS:
				case self::TERM_CONTACT_IM:
				case self::TERM_CONTACT_PHONE:
					if ($term == self::TERM_CONTACT_ADDRESS) $field = 'addresss';
					if ($term == self::TERM_CONTACT_IM)      $field = 'instant_message';
					if ($term == self::TERM_CONTACT_PHONE)   $field = 'phone';

					$any = false;
					foreach ($person->getContactData('address') as $cd) {
						if ($cd->checkStringMatch($choice)) {
							$any = true;
							if ($op == self::OP_NOTCONTAINS) {
								return false;
							}
						}
					}

					if ($op == self::OP_CONTAINS AND !$any) {
						return false;
					}
					break;

				case self::TERM_LABEL:

					if ($op == self::OP_IS) $op = self::OP_CONTAINS;
					elseif ($op == self::OP_NOT) $op = self::OP_NOTCONTAINS;

					$any = false;
					if (isset($choice['label'])) {
						$choice = $choice['label'];
					}

					foreach ($person->getLabelManager()->getLabelsArray() as $label) {
						if (strpos(strtolower($label), strtolower($choice)) !== false) {
							$any = true;
							if ($op == self::OP_NOTCONTAINS) {
								return false;
							}
						}
					}

					if ($op == self::OP_CONTAINS AND !$any) {
						return false;
					}
					break;
			}
		}

		return true;
	}
}
