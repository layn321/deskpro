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
 * @subpackage AgentBundle
 */

namespace Application\AgentBundle\Controller;

use Orb\Util\Arrays;

use Application\DeskPRO\Entity;
use Application\DeskPRO\Entity\Organization;
use Application\DeskPRO\Entity\OrganizationContactData;
use Application\DeskPRO\Entity\OrganizationNote;
use Application\DeskPRO\Searcher\TicketSearch;

use Application\DeskPRO\App;

/**
 * Handles viewing and editing an org
 */
class OrganizationController extends AbstractController
{
	############################################################################
	# view
	############################################################################

	public function viewAction($organization_id)
	{
		$org = $this->getOrgOr404($organization_id);

		#------------------------------
		# Custom fields
		#------------------------------

		$field_manager = $this->container->getSystemService('org_fields_manager');
		$custom_fields = $field_manager->getDisplayArrayForObject($org);

		#------------------------------
		# Misc info needed
		#------------------------------

		$notes = $this->em->getRepository('DeskPRO:OrganizationNote')->getNotesForOrganization($org);

		$search = new TicketSearch();
		$search->addTerm(TicketSearch::TERM_ORGANIZATION, 'is', $org->getId());
		$search->setOrderBy('ticket.status', 'DESC');

		$org_tickets = $search->getMatches(array('offset' => 0, 'limit' => 30));
		$org_tickets = $this->em->getRepository('DeskPRO:Ticket')->getByIds($org_tickets, true);
		$org_tickets_count = $search->getCount(1000);

		$org_chats = $this->em->getRepository('DeskPRO:ChatConversation')->getRecentForOrganization($org);
		$org_chats_count = $this->em->getRepository('DeskPRO:ChatConversation')->getCountForOrganization($org);

		$org_charges = $this->em->getRepository('DeskPRO:TicketCharge')->getChargesForOrganization($org, 20);
		$org_charge_totals = $this->em->getRepository('DeskPRO:TicketCharge')->getTotalChargesForOrganization($org);

		$activity_stream = $this->em->getRepository('DeskPRO:PersonActivity')->getForOrganization($org, 10);

		// Count members
		$members_count = $this->em->getRepository('DeskPRO:Organization')->countMembersFor($org);

		$usergroup_names = $this->em->getRepository('DeskPRO:Usergroup')->getUsergroupNames();
		$org_usergroups = $org->usergroups;

		$contact_data = array();
		foreach ($org->contact_data as $cd) {
			if (!isset($contact_data[$cd->contact_type])) {
				$contact_data[$cd->contact_type] = array();
			}
			$contact_data[$cd->contact_type][] = $cd->getTemplateVars();
		}

		$org_domain_data = $this->getOrgEmailDisplayData($org);

		$org_members = $this->em->getRepository('DeskPRO:Person')->getOrganizationMembers($org);

		$org_api = array();
		foreach (array('id', 'name', 'summary') AS $key) {
			$org_api[$key] = $org->$key;
		}
		$org_api['date_created'] = $org->date_created->getTimestamp();

		foreach ($custom_fields AS $field) {
			$org_api['custom'][$field['id']] = array(
				'id' => $field['id'],
				'title' => $field['title'],
				'value' => isset($field['value']['value']) ? $field['value']['value'] : false
			);
		}

		return $this->render('AgentBundle:Organization:view.html.twig', array(
			'org'                => $org,
			'org_email_domains'             => $org_domain_data['org_email_domains'],
			'org_count_domain_nonmembers'   => $org_domain_data['org_count_domain_nonmembers'],
			'org_count_domain_takenmembers' => $org_domain_data['org_count_domain_takenmembers'],
			'org_count_domain_members'      => $org_domain_data['org_count_domain_members'],
			'org_members'        => $org_members,
			'org_api'            => $org_api,
			'contact_data'       => $contact_data,
			'org_usergroups'     => $org_usergroups,
			'usergroup_names'    => $usergroup_names,
			'notes'              => $notes,
			'activity_stream'    => $activity_stream,
			'org_tickets'        => $org_tickets,
			'org_tickets_count'  => $org_tickets_count,
			'org_chats'          => $org_chats,
			'org_chats_count'    => $org_chats_count,
			'org_charges'        => $org_charges,
			'org_charge_totals'  => $org_charge_totals,
			'members_count'      => $members_count,
			'custom_fields'      => $custom_fields,
		));
	}

	############################################################################
	# ajax-save
	############################################################################

	public function ajaxSaveAction($organization_id)
	{
		$org = $this->getOrgOr404($organization_id);

		$this->em->beginTransaction();
		$data = array(
			'success' => true
		);

		$action = $this->in->getString('action');
		if (!$this->person->hasPerm('agent_org.edit') && ($action != 'add-person' && $action != 'remove-person' && $action != 'get-person-row')) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		switch ($action) {
			case 'name':
				if ($this->in->getString('name')) {
					$org->name = $this->in->getString('name');
					$this->em->persist($org);
				}
				break;

			case 'set-summary':
				$org->summary = $this->in->getString('summary');
				$this->em->persist($org);
				break;

			case 'delete-picture':
				$org->picture_blob = null;
				$this->em->persist($org);
				break;

			case 'set-picture':
				$blob = $this->em->find('DeskPRO:Blob', $this->in->getUint('blob_id'));
				if ($blob) {
					$org->picture_blob = $blob;
					$this->em->persist($org);
				}
				break;

			case 'add-person':
				if (!$this->person->hasPerm('agent_people.edit')) {
					throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
				}
				$person = $this->em->find('DeskPRO:Person', $this->in->getUint('person_id'));
				if ($person->organization) {
					$data['already_in_organization'] = true;
				} elseif ($person) {
					$person->organization = $org;
					$person->organization_position = $this->in->getString('position');
					$this->em->persist($person);
					$data['add_person_id'] = $person['id'];
					$data['row_html'] = $this->renderView('AgentBundle:Organization:view-members-row.html.twig', array('person' => $person, 'org' => $org));
				}
				break;

			case 'get-person-row':
				$person = $this->em->find('DeskPRO:Person', $this->in->getUint('person_id'));
				if ($person->organization->id = $org->id) {
					$data['row_html'] = $this->renderView('AgentBundle:Organization:view-members-row.html.twig', array('person' => $person));
				}
				break;

			case 'remove-person':
				if (!$this->person->hasPerm('agent_people.edit')) {
					throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
				}
				$person = $this->em->find('DeskPRO:Person', $this->in->getUint('person_id'));
				if ($person && $person->organization && $person->organization->id == $org->id) {
					$person->organization = null;
					$this->em->persist($person);
					$data['remove_person_id'] = $person['id'];
				}
				break;

			case 'set-usergroups':
				$usergroup_ids = $this->in->getCleanValueArray('usergroup_ids', 'uint', 'discard');
				$usergroup_ids = Arrays::removeFalsey($usergroup_ids);

				if ($usergroup_ids) {
					$usergroup_ids = array_unique($usergroup_ids);

					// Make sure only valid ones are set
					$usergroup_ids = $this->container->getDb()->fetchAllCol("
						SELECT id
						FROM usergroups
						WHERE id IN (" . implode(',', $usergroup_ids).")
							AND sys_name IS NULL
					");
				}

				$this->container->getDb()->delete('organization2usergroups', array('organization_id' => $org->id));

				if ($usergroup_ids) {
					$inserts = array();
					foreach ($usergroup_ids as $uid) {
						$inserts[] = array('organization_id' => $org->getId(), 'usergroup_id' => $uid);
					}

					$this->container->getDb()->batchInsert('organization2usergroups', $inserts);
				}
				break;

			case 'set-slas':
				$sla_ids = $this->in->getCleanValueArray('sla_ids', 'uint', 'discard');
				$slas = $this->em->getRepository('DeskPRO:Sla')->getAllSlas();


				foreach ($slas AS $sla) {
					if (in_array($sla->id, $sla_ids)) {
						$sla->addOrganization($org);
					} else {
						$sla->removeOrganization($org);
					}
					$this->em->persist($sla);
				}

				break;

			default:
				return $this->createJsonResponse(array('error' => true, 'message' => 'Unknown action'));
				break;
		}

		$this->em->flush();
		$this->em->commit();

		return $this->createJsonResponse($data);
	}

	public function ajaxSaveCustomFieldsAction($organization_id)
	{
		if (!$this->person->hasPerm('agent_org.edit')) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$org = $this->getOrgOr404($organization_id);

		$this->em->beginTransaction();

		try {
			$field_manager = $this->container->getSystemService('org_fields_manager');
			$post_custom_fields = $this->request->request->get('custom_fields', array());
			if (!empty($post_custom_fields)) {
				$field_manager->saveFormToObject($post_custom_fields, $org);
			}

			$this->em->flush();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}

		$custom_fields = $field_manager->getDisplayArrayForObject($org);

		return $this->render('AgentBundle:Organization:view-customfields-rendered-rows.html.twig', array(
			'org' => $org,
			'custom_fields' => $custom_fields,
		));
	}

	public function changePictureOverlayAction($organization_id)
	{
		$org = $this->getOrgOr404($organization_id);

		return $this->render('AgentBundle:Organization:change-org-picture.html.twig', array(
			'org' => $org
		));
	}

	public function saveContactDataAction($organization_id)
	{
		if (!$this->person->hasPerm('agent_org.edit')) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$org = $this->getOrgOr404($organization_id);

		// Build contact_data before modifying collection,
		// adding to the collection causes dupe values to be added (doctrine bug w/ add() on collection indexed by id?)
		$contact_data_array = array();
		foreach ($org->contact_data as $cd) {
			if (!isset($contact_data_array[$cd->contact_type])) {
				$contact_data_array[$cd->contact_type] = array();
			}
			$contact_data_array[$cd->contact_type][$cd->getId()] = $cd->getTemplateVars();
		}

		// Adding contact data
		$added = array();
		foreach ($this->in->getCleanValueArray('new_contact_data') as $type => $inputs) {
			foreach ($inputs as $input) {
				try {
					$contact_data = new OrganizationContactData();
					$contact_data->contact_type = $type;
					$contact_data->applyFormData($input);

					$contact_data->organization = $org;

					$this->em->persist($contact_data);

					$added[] = $contact_data;
				} catch (\Exception $e) {
					throw $e;
				}
			}
		}

		// Adding org emails
		foreach ($this->in->getCleanValueArray('new_org_email_domain') as $domain) {
			$check = $this->em->getRepository('DeskPRO:OrganizationEmailDomain')->find($domain);
			if (!$check) {

				$domain = ltrim($domain, '@');

				$org_email_domain = new \Application\DeskPRO\Entity\OrganizationEmailDomain();
				$org_email_domain->organization = $org;
				$org_email_domain->domain = $domain;

				$this->em->persist($org_email_domain);
			}
		}

		//remove_org_email
		foreach ($this->in->getCleanValueArray('remove_org_email') as $domain) {
			$check = $this->em->getRepository('DeskPRO:OrganizationEmailDomain')->find($domain);
			if ($check && $check->organization->id == $org->id) {
				$this->em->remove($check);
			}
		}

		// Editing values
		foreach ($this->in->getCleanValueArray('contact_data') as $id => $input) {
			if (!isset($org->contact_data[$id])) {
				continue;
			}

			$org->contact_data[$id]->applyFormData($input);
			$this->em->persist($org->contact_data[$id]);
		}

		// Removing values
		foreach ($this->in->getCleanValueArray('remove_contact_data', 'uint') as $id) {
			if (isset($org->contact_data[$id])) {
				$cd = $org->contact_data[$id];
				$this->em->remove($org->contact_data[$id]);
				$org->contact_data->remove($id);

				if (isset($contact_data_array[$cd->contact_type][$cd->id])) {
					unset($contact_data_array[$cd->contact_type][$cd->id]);
				}
			}
		}

		$this->em->beginTransaction();
		$this->em->flush();
		$this->em->commit();

		// Reset display array
		$contact_data_array = array();
		foreach ($org->contact_data as $cd) {
			if (!isset($contact_data_array[$cd->contact_type])) {
				$contact_data_array[$cd->contact_type] = array();
			}
			$contact_data_array[$cd->contact_type][$cd->getId()] = $cd->getTemplateVars();
		}

		foreach ($added as $cd) {
			if (!isset($contact_data_array[$cd->contact_type])) {
				$contact_data_array[$cd->contact_type] = array();
			}
			$contact_data_array[$cd->contact_type][$cd->getId()] = $cd->getTemplateVars();
		}

		$org_email_domains = $this->em->getRepository('DeskPRO:OrganizationEmailDomain')->getDomainsForOrganization($org);

		$display_html = $this->renderView('AgentBundle:Organization:view-contact-display.html.twig', array(
			'org_email_domains' => $org_email_domains,
			'org' => $org,
			'contact_data' => $contact_data_array,
		));
		$editor_overlay_html = $this->renderView('AgentBundle:Organization:contact-overlay.html.twig', array(
			'org_email_domains' => $org_email_domains,
			'org' => $org,
			'contact_data' => $contact_data_array,
		));

		return $this->createJsonResponse(array(
			'success' => 1,
			'display_html' => $display_html,
			'editor_overlay_html' => $editor_overlay_html
		));
	}

	public function savePositionAction($organization_id, $person_id)
	{
		if (!$this->person->hasPerm('agent_people.edit')) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$person = $this->em->find('DeskPRO:Person', $person_id);
		if ($person) {
			$person->organization_position = $this->in->getString('organization_position');

			$this->em->persist($person);
			$this->em->flush();
		}

		return $this->createJsonResponse(array('success' => true));
	}

	public function saveManagerAction($organization_id, $person_id)
	{
		if (!$this->person->hasPerm('agent_people.edit')) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$this->db->executeUpdate("
			UPDATE people SET organization_manager = ?
			WHERE id = ? AND organization_id = ?
		", array((int)$this->in->getBool('organization_manager'), $person_id, $organization_id));

		return $this->createJsonResponse(array('success' => true));
	}

	############################################################################
	# ajax-save-note
	############################################################################

	public function ajaxSaveNoteAction($organization_id)
	{
		if (!$this->person->hasPerm('agent_org.notes')) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$org = $this->getOrgOr404($organization_id);

		$note_txt = $this->in->getString('note');

		$em = App::getOrm();
		$em->beginTransaction();

		$note = new OrganizationNote();
		$note['agent'] = $this->person;
		$note['organization'] = $org;
		$note['note'] = $note_txt;
		$em->persist($note);

		$em->flush();
		$em->commit();

		return $this->createJsonResponse(array(
			'success' => true,
			'organization_id' => $org['id'],
			'note_li_html' => $this->renderView('AgentBundle:Organization:note-li.html.twig', array('note' => $note))
		));
	}

	############################################################################
	# ajax-save-labels
	############################################################################

	public function ajaxSaveLabelsAction($organization_id)
	{
		if (!$this->person->hasPerm('agent_org.edit')) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$org = $this->getOrgOr404($organization_id);

		$labels = $this->in->getCleanValueArray('labels', 'string', 'discard');

		$org->getLabelManager()->setLabelsArray($labels);

		$this->em->persist($org);
		$this->em->flush();

		return $this->createJsonResponse(array('success' => 1));
	}

	############################################################################
	# org domains
	############################################################################

	protected  function getOrgEmailDisplayData($org)
	{
		$org_email_domains = $this->em->getRepository('DeskPRO:OrganizationEmailDomain')->getDomainsForOrganization($org);

		$org_count_domain_nonmembers   = $this->em->getRepository('DeskPRO:PersonEmail')->countDomainsWithNoCompany($org_email_domains, $org);
		$org_count_domain_takenmembers = $this->em->getRepository('DeskPRO:PersonEmail')->countDomainsWithOtherCompany($org_email_domains, $org);
		$org_count_domain_members      = $this->em->getRepository('DeskPRO:OrganizationEmailDomain')->countMembersAtDomains($org, $org_email_domains);

		return array(
			'org'                => $org,
			'org_email_domains'  => $org_email_domains,
			'org_count_domain_nonmembers'   => $org_count_domain_nonmembers,
			'org_count_domain_takenmembers' => $org_count_domain_takenmembers,
			'org_count_domain_members'      => $org_count_domain_members,
		);
	}

	public function assignDomainAction($organization_id)
	{
		if (!$this->person->hasPerm('agent_org.edit')) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$org = $this->getOrgOr404($organization_id);

		$org_domain_manager = $this->container->getSystemService('org_email_domain_manager');
		$domain = $this->in->getString('domain');

		if ($org_domain_manager->isInUse($domain)) {
			return $this->createResponse('<div class="error" data-error-code="in_use" />');
		}

		$org_domain_manager->assignDomain($domain, $org);

		$data = $this->getOrgEmailDisplayData($org);
		return $this->render('AgentBundle:Organization:orgemail-display.html.twig', $data);
	}

	public function unassignDomainAction($organization_id)
	{
		if (!$this->person->hasPerm('agent_org.edit')) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$org = $this->getOrgOr404($organization_id);

		$domain = $this->in->getString('domain');
		$orgdomain = $this->em->getRepository('DeskPRO:OrganizationEmailDomain')->find(array('organization' => $org, 'domain' => $domain));

		if ($orgdomain) {
			$org_domain_manager = $this->container->getSystemService('org_email_domain_manager');
			$org_domain_manager->unassignDomain($orgdomain, $this->in->getBool('remove_users'));
		}

		$data = $this->getOrgEmailDisplayData($org);
		return $this->render('AgentBundle:Organization:orgemail-display.html.twig', $data);
	}

	public function moveDomainUsersAction($organization_id)
	{
		if (!$this->person->hasPerm('agent_org.edit')) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$org = $this->getOrgOr404($organization_id);

		$org_domain_manager = $this->container->getSystemService('org_email_domain_manager');
		$domain = $this->in->getString('domain');

		$orgdomain = $this->em->getRepository('DeskPRO:OrganizationEmailDomain')->find(array('organization' => $org, 'domain' => $domain));

		if ($orgdomain) {
			$count = $org_domain_manager->moveNonCompanyUsers($orgdomain);
		}

		$data = $this->getOrgEmailDisplayData($org);
		return $this->render('AgentBundle:Organization:orgemail-display.html.twig', $data);
	}

	public function moveTakenDomainUsersAction($organization_id)
	{
		if (!$this->person->hasPerm('agent_org.edit')) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$org = $this->getOrgOr404($organization_id);

		$org_domain_manager = $this->container->getSystemService('org_email_domain_manager');

		$domain = $this->in->getString('domain');
		$orgdomain = $this->em->getRepository('DeskPRO:OrganizationEmailDomain')->find(array('organization' => $org, 'domain' => $domain));

		if (!$orgdomain) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$count = $org_domain_manager->moveOtherCompanyUsers($orgdomain);

		$data = $this->getOrgEmailDisplayData($org);
		return $this->render('AgentBundle:Organization:orgemail-display.html.twig', $data);
	}

	############################################################################
	# delete
	############################################################################

	public function deleteOrganizationAction($organization_id, $security_token)
	{
		if (!$this->person->hasPerm('agent_org.delete')) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		if (!$this->session->getEntity()->checkSecurityToken('delete_org', $security_token)) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$org = $this->getOrgOr404($organization_id);

		$edit_manager = $this->container->getSystemService('org_edit_manager');
		$edit_manager->deleteOrganization($org);

		return $this->createJsonResponse(array('success' => true));
	}


	############################################################################
	# New person
	############################################################################

	public function newOrganizationAction()
	{
		if (!$this->person->hasPerm('agent_org.create')) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$state = $this->em->getRepository('DeskPRO:PersonPref')->getPrefForPersonId('agent.ui.state.neworg', $this->person->id);

		#------------------------------
		# Custom fields
		#------------------------------

		// Custom fields
		$field_defs = App::getApi('custom_fields.organizations')->getEnabledFields();
		$data_structured = App::getApi('custom_fields.util')->createDataHierarchy(array(), $field_defs);

		$custom_fields_form = $this->get('form.factory')->createNamedBuilder('form', 'org_custom_fields');
		$custom_fields = App::getApi('custom_fields.organizations')->getFieldsDisplayArray($field_defs, $data_structured, $custom_fields_form);

		return $this->render('AgentBundle:Organization:neworganization.html.twig', array(
			'state' => $state,
			'custom_fields' => $custom_fields,
		));
	}

	public function newOrganizationSaveAction()
	{
		if (!$this->person->hasPerm('agent_org.create')) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$neworg = new \Application\AgentBundle\Form\Model\NewOrganization($this->person);

		$formType = new \Application\AgentBundle\Form\Type\NewOrganization();
		$form = $this->get('form.factory')->create($formType, $neworg);

		if ($this->get('request')->getMethod() == 'POST') {
			$form->bindRequest($this->get('request'));
			$form->isValid();

			if (!$neworg->name) {
				return $this->createJsonResponse(array('error' => true, 'error_code' => 'invalid_name'));
			}

			$neworg->setCustomFieldForm($_POST);
			$neworg->save();

			$org = $neworg->getOrganization();

			$this->em->getRepository('DeskPRO:PersonPref')->deletePrefForPersonId('agent.ui.state.neworg', $this->person->id);

			return $this->createJsonResponse(array(
				'success' => true,
				'org_id' => $org['id']
			));
		} else {
			return $this->createJsonResponse(array(
				'success' => false,
			));
		}
	}


	/**
	 * @return \Application\DeskPRO\Entity\Organization
	 */
	protected function getOrgOr404($organization_id)
	{
		$org = $this->em->find('DeskPRO:Organization', $organization_id);

		if (!$org) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no organization with ID $organization_id");
		}

		return $org;
	}
}
