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
use Orb\Util\Strings;
use Orb\Util\Arrays;
use Orb\Util\Util;

use Application\DeskPRO\Entity\Organization;

class OrganizationSearch extends SearcherAbstract
{
	const TERM_ID                   = 'org_id';
	const TERM_NAME                 = 'org_name';
	const TERM_ORGANIZATION_FIELD   = 'org_field';
	const TERM_LABEL                = 'org_label';
	const TERM_CONTACT_PHONE        = 'org_contact_phone';
	const TERM_CONTACT_ADDRESS      = 'org_contact_address';
	const TERM_CONTACT_IM           = 'org_contact_im';
	const TERM_EMAIL_DOMAIN         = 'org_email_domain';

	/**
	 * Summary of terms in phrases
	 * @var array
	 */
	protected $summary = array();


	/**
	 * Run the search and return an array of matching ID's.
	 *
	 * @param int $limit
	 * @return array
	 */
	public function getMatches()
	{
		$db = App::getDbRead();

		$org_ids = $db->fetchAllCol($this->getSql());

		return $org_ids;
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
	 * Get the SQL query that'll fetch the results
	 * @return string
	 */
	public function getSql()
	{
		$sql = "SELECT organizations.id FROM organizations ";

		$parts = $this->getSqlParts();
		$order_by = $this->getOrderByPart();


		#------------------------------
		# Add joins
		#------------------------------

		foreach ($parts['joins'] as $j) {
			if (is_array($j)) {
				$sql .= $j[1] . " ";
			} else {
				$sql .= "LEFT JOIN $j ON $j.organization_id = organizations.id ";
			}
		}

		// An array means the sort needs a join
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

		$sql .= " GROUP BY organizations.id ";
		if ($order_by) {
			$sql .= " ORDER BY $order_by ";
		}
		$sql .= " LIMIT 1000";

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
			$this->order_by = array('organizations.name', 'ASC');
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
			case 'organization.name':
				$order_by = "organizations.name $dir";
				break;

			case 'organization.num_members':
				$order_by = array(
					"INNER JOIN people AS sort_table ON (sort_table.organization_id = organizations.id)",
					"COUNT(sort_table.id) $dir, organizations.name DESC"
				);
				break;

			case 'organization.organization_field':
				$field = App::getEntityRepository('DeskPRO:CustomDefOrganization')->find($term_id);
				if (!$field) break;

				$search_type = $field->getHandler()->getSearchType();

				switch ($search_type) {
					case 'input':
					case 'value':
						$order_by = arary(
							"INNER JOIN custom_data_organizationss AS sort_table ON (sort_table.organization_id = organizations.id AND sort_table.id = $term_id)",
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
		$org_table = 'organizations';

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
					$wheres[] = $this->_rangeMatch("$org_table.id", $op, $choice, true);
					$this->summary[] = $this->_rangeSummary($tr->phrase('agent.general.id'), $op, $choice);
					break;

				case self::TERM_NAME:
					$wheres[] = $this->_stringMatch("organizations.name", $op, $choice);
					break;

				case self::TERM_CONTACT_PHONE:

					$choice = preg_replace('#[^0-9A-Za-z]#', '', $choice);

					$joins[] = array(
						'organizations_contact_data',
						"LEFT JOIN organizations_contact_data AS $join_name ON ($join_name.organization_id = organizations.id AND $join_name.contact_type = 'phone')"
					);
					$wheres[] = $this->_stringMatch("$join_name.field_2", $op, $choice, false, true);

					$this->summary[] = $this->_choiceSummary('Phone Number', $op, $choice);
					break;

				case self::TERM_CONTACT_ADDRESS:

					$joins[] = array(
						'organizations_contact_data',
						"LEFT JOIN organizations_contact_data AS $join_name ON ($join_name.organization_id = organizations.id AND $join_name.contact_type = 'address')"
					);
					$wheres[] = $this->_stringMatch("$join_name.field_1", $op, $choice, false, true);

					$this->summary[] = $this->_choiceSummary('Address', $op, $choice);
					break;

				case self::TERM_CONTACT_IM:

					$joins[] = array(
						'organizations_contact_data',
						"LEFT JOIN organizations_contact_data AS $join_name ON ($join_name.organization_id = organizations.id AND $join_name.contact_type = 'instant_message')"
					);
					$wheres[] = $this->_stringMatch("$join_name.field_1", $op, $choice, false, true);

					$this->summary[] = $this->_choiceSummary('IM', $op, $choice);
					break;

				case self::TERM_EMAIL_DOMAIN:
					$joins[] = array(
						'organization_email_domains',
						"LEFT JOIN organization_email_domains AS $join_name ON ($join_name.organization_id = organizations.id)"
					);
					$wheres[] = $this->_stringMatch("$join_name.domain", $op, $choice, false);

					$choice = implode(' or ', (array)$choice);
					$this->summary[] = "Email domain is " . $choice;
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
								'labels_organizations',
								"LEFT JOIN labels_organizations AS $join_name ON ($join_name.organization_id = organizations.id)"
							);
							$wheres[] = "$join_name.label = " . $db->quote($choice);
							break;
						case self::OP_NOT:
							$joins[] = array(
								'labels_organizations',
								"LEFT JOIN labels_organizations AS $join_name ON ($join_name.organization_id = organizations.id AND $join_name.label = ".$db->quote($choice).")"
							);
							$wheres[] = "$join_name.person_id IS NULL";
							break;
						case self::OP_CONTAINS:
							$joins[] = array(
								'labels_organizations',
								"LEFT JOIN labels_organizations AS $join_name ON ($join_name.organization_id = organizations.id)"
							);
							$wheres[] = "$join_name.label IN ($choices_in)";
							break;

						case self::OP_NOTCONTAINS:
							$joins[] = array(
								'labels_organizations',
								"LEFT JOIN labels_organizations AS $join_name ON ($join_name.organization_id = organizations.id AND $join_name.label IN ($choices_in)"
							);
							$wheres[] = "$join_name.person_id IS NULL";
							break;
					}
					break;

				case self::TERM_ORGANIZATION_FIELD:


					$field = App::getEntityRepository('DeskPRO:CustomDefOrganization')->find($term_id);
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
								'custom_data_organizations',
								"LEFT JOIN custom_data_organizations AS custom_data_organizations_$join_id ON (custom_data_organizations_$join_id.organization_id = organizations.id AND custom_data_organizations_$join_id.field_id = $term_id)"
							);

							if (is_array($choice)) {
								$choice = array_pop($choice);
							}

							$field = 'custom_data_organizations_'.$join_id.'.'.$search_type;
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

							$field = 'custom_data_organizations_'.$join_id.'.field_id';
							switch ($op) {
								case self::OP_CONTAINS:
								case self::OP_IS:
									$joins[] = array(
										'custom_data_organizations',
										"LEFT JOIN custom_data_organizations AS custom_data_organizations_$join_id ON (custom_data_organizations_$join_id.organization_id = organizations.id AND $field IN ($choices_in))"
									);
									$wheres[] = "custom_data_organizations_$join_id.id IS NOT NULL";
									break;

								case self::OP_NOTCONTAINS:
								case self::OP_NOT:
									$joins[] = array(
										'custom_data_organizations',
										"LEFT JOIN custom_data_organizations AS custom_data_organizations_$join_id ON (custom_data_organizations_$join_id.organization_id = organizations.id AND $field IN ($choices_in))"
									);
									$wheres[] = "custom_data_organizations_$join_id.id IS NULL";
									break;
							}
							break;
					}
					break; // end TERM_ORGANIZATION_FIELD
			}
		}

		return array(
			'joins' => $joins,
			'wheres' => $wheres
		);
	}


	public function doesOrganizationMatch(Organization $org)
	{
		foreach ($this->terms as $info) {
			list($term, $op, $choice) = $info;

			if (count($choice) == 1) {
				$choice = array_pop($choice);
			}

			switch ($term) {
				case self::TERM_NAME:
					switch ($op) {
						case self::OP_IS:
							if (strtolower($org['name']) != strtolower($choice)) return false;
							break;
						case self::OP_NOT:
							if (strtolower($org['name']) == strtolower($choice)) return false;
							break;
						case self::OP_CONTAINS:
							if (strpos(strtolower($org['name']), strtolower($choice)) === false) return false;
							break;
						case self::OP_NOTCONTAINS:
							if (strpos(strtolower($org['name']), strtolower($choice)) !== false) return false;
							break;
					}
					break;

				case self::TERM_EMAIL_DOMAIN:
					$any = false;
					if (is_array($choice)) {
						$choice = array_pop($choice);
					}
					foreach ($org->email_domains as $domain) {
						if (strpos(strtolower($domain['domain']), strtolower($choice)) !== false) {
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

				case self::TERM_CONTACT_ADDRESS:
				case self::TERM_CONTACT_IM:
				case self::TERM_CONTACT_PHONE:
					if ($term == self::TERM_CONTACT_ADDRESS) $field = 'addresss';
					if ($term == self::TERM_CONTACT_IM)      $field = 'instant_message';
					if ($term == self::TERM_CONTACT_PHONE)   $field = 'phone';

					$any = false;
					foreach ($org->getContactData('address') as $cd) {
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
					$any = false;
					if (isset($choice['label'])) {
						$choice = $choice['label'];
					}

					foreach ($org->getLabelManager()->getLabelsArray() as $label) {
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
