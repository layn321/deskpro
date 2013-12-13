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
 * @subpackage ApiBundle
 */

namespace Application\ApiBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Searcher\OrganizationSearch;
use Application\DeskPRO\Entity\Organization;
use Orb\Util\Numbers;

class OrganizationController extends AbstractController
{
	public function searchAction()
	{
		$search_map = array(
			'address' => OrganizationSearch::TERM_CONTACT_ADDRESS,
			'im' => OrganizationSearch::TERM_CONTACT_IM,
			'label' => OrganizationSearch::TERM_LABEL,
			'name' => OrganizationSearch::TERM_NAME,
			'phone' => OrganizationSearch::TERM_CONTACT_PHONE,
		);

		$terms = array();

		foreach ($search_map AS $input => $search_key) {
			$value = $this->in->getCleanValueArray($input, 'raw', 'discard');
			if ($value) {
				$terms[] = array('type' => $search_key, 'op' => 'contains', 'options' => $value);
			}
		}

		foreach ($this->container->getSystemService('org_fields_manager')->getFields() as $field) {
			if ($this->in->checkIsset("field." . $field->getId())) {
				$in_val = $this->in->getString('field.'.$field->getId());
				if ($in_val) {
					$terms[] = array('type' => 'org_field[' . $field->getId() . ']', 'op' => 'is', 'options' => array('value' => $in_val));
				}
			}
		}

		if ($this->in->checkIsset('order')) {
			$order_by = $this->in->getString('order');
		} else {
			$order_by = $this->person->getPref('agent.ui.org-filter-order-by.0');
			if (!$order_by) {
				$order_by = 'organization.name:asc';
			}
		}

		$extra = array();
		if ($order_by !== null) {
			$extra['order_by'] = $order_by;
		}

		$result_cache = $this->getApiSearchResult('organization', $terms, $extra, $this->in->getUint('cache_id'), new OrganizationSearch());

		$page = $this->in->getUint('page');
		if (!$page) $page = 1;

		$per_page = Numbers::bound($this->in->getUint('per_page') ?: 25, 1, 250);

		$person_ids = $result_cache->results;

		$page_ids = \Orb\Util\Arrays::getPageChunk($person_ids, $page, $per_page);
		$orgs = App::getEntityRepository('DeskPRO:Organization')->getByIds($page_ids, true);

		return $this->createApiResponse(array(
			'page' => $page,
			'per_page' => $per_page,
			'total' => count($person_ids),
			'cache_id' => $result_cache->id,
			'organizations' => $this->getApiData($orgs)
		));
	}

	public function newOrganizationAction()
	{
		if (!$this->person->hasPerm('agent_org.create')) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$org = new Organization();
		$errors = array();

		$name = $this->in->getString('name');
		if (!$name) {
			$errors['name'] = array('required_field.name', 'name is empty or missing');
		}

		$org->name = $name;

		$bulk_set = array(
			'summary' => 'String',
		);
		foreach ($bulk_set AS $input => $type) {
			if ($this->in->checkIsset($input)) {
				$org->$input = $this->in->{'get' . $type}($input);
			}
		}

		if ($errors) {
			return $this->createApiMultipleErrorResponse($errors);
		}

		foreach ($this->in->getArrayValue('contact_data') AS $contact) {
			$contact_type = isset($contact['type']) ? $contact['type'] : false;
			$data = (isset($contact['data']) && is_array($contact['data'])) ? $contact['data'] : false;

			if (!$contact_type || !$data) {
				continue;
			}

			$data['comment'] = isset($contact['comment']) ? $contact['comment'] : '';

			$contact_data = new \Application\DeskPRO\Entity\OrganizationContactData();
			$contact_data->contact_type = $contact_type;
			try {
				$contact_data->applyFormData($data);
			} catch (\InvalidArgumentException $e) {
				// invalid type
				continue;
			}

			$all_empty = true;
			for ($i = 1; $i <= 10; $i++) {
				if ($contact_data->{'field_' . $i}) {
					$all_empty = false;
					break;
				}
			}

			if (!$all_empty) {
				$org->addContactData($contact_data);
			}
		}

		$this->db->beginTransaction();

		try {
			foreach ($this->in->getCleanValueArray('group_id', 'int') as $ug_id) {
				$ug = $this->em->find('DeskPRO:Usergroup', $ug_id);
				if ($ug && !$ug->is_agent_group && !$ug->sys_name) {
					$org->usergroups->add($ug);
				}
			}

			$this->em->persist($org);

			$field_manager = $this->container->getSystemService('org_fields_manager');
			$post_custom_fields = $this->getCustomFieldInput();
			if (!empty($post_custom_fields)) {
				$field_manager->saveFormToObject($post_custom_fields, $org, true);
			}
			$this->em->flush();

			$labels = $this->in->getCleanValueArray('label', 'string', 'discard');
			if ($labels) {
				$org->getLabelManager()->setLabelsArray($labels, $this->em);
				$this->em->flush();
			}

			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		return $this->createApiCreateResponse(
			array('id' => $org->id),
			$this->generateUrl('api_organizations_organization', array('organization_id' => $org->id), true)
		);
	}

	public function getOrganizationAction($organization_id)
	{
		$org = $this->_getOrganizationOr404($organization_id);

		return $this->createApiResponse(array('organization' => $org->toApiData()));
	}

	public function postOrganizationAction($organization_id)
	{
		$org = $this->_getOrganizationOr404($organization_id, 'edit');

		$name = $this->in->getString('name');
		if ($name) {
			$org->name = $name;
		}

		$bulk_set = array(
			'summary' => 'String',
		);
		foreach ($bulk_set AS $input => $type) {
			if ($this->in->checkIsset($input)) {
				$org->$input = $this->in->{'get' . $type}($input);
			}
		}

		$this->db->beginTransaction();

		try {
			$this->em->persist($org);

			$field_manager = $this->container->getSystemService('org_fields_manager');
			$post_custom_fields = $this->getCustomFieldInput();
			if (!empty($post_custom_fields)) {
				$field_manager->saveFormToObject($post_custom_fields, $org, true);
			}
			$this->em->flush();

			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		return $this->createSuccessResponse();
	}

	public function deleteOrganizationAction($organization_id)
	{
		$org = $this->_getOrganizationOr404($organization_id, 'delete');

		$edit_manager = $this->container->getSystemService('org_edit_manager');
		$edit_manager->deleteOrganization($org);

		return $this->createSuccessResponse();
	}


	public function getOrganizationPictureAction($organization_id)
	{
		$org = $this->_getOrganizationOr404($organization_id);

		$size = $this->in->getUint('size');
		if (!$size) {
			$size = 80;
		}

		return $this->createApiResponse(array(
			'has_picture' => $org->hasPicture(),
			'picture_url' => $org->getPictureUrl($size),
			'size' => $size
		));
	}

	public function postOrganizationPictureAction($organization_id)
	{
		$org = $this->_getOrganizationOr404($organization_id, 'edit');

		$file = $this->request->files->get('file');
		$accept = $this->container->getAttachmentAccepter();

		if ($file) {
			$error = $accept->getError($file, 'agent');
			if (!$error) {
				$set = new \Application\DeskPRO\Attachments\RestrictionSet();
				$set->setAllowedExts(array('gif', 'png', 'jpg', 'jpeg'));
				$accept->addRestrictionSet('only_images', $set);
				$error = $accept->getError($file, 'only_images');
			}
			if ($error) {
				$message = $this->container->getTranslator()->phrase('agent.general.attach_error_' . $error['error_code'], $error);
				return $this->createApiErrorResponse($error['error_code'], $message);
			}

			$blob = $accept->accept($file);
		} else {
			$blob_id = $this->in->getUint('blob_id');
			$blob = $this->em->find('DeskPRO:Blob', $blob_id);
			if (!$blob) {
				return $this->createApiErrorResponse('invalid_argument.blob_id', 'blob_id not found');
			}
		}

		$org->picture_blob = $blob;
		$this->em->persist($org);
		$this->em->flush();

		return $this->createSuccessResponse();
	}

	public function deleteOrganizationPictureAction($organization_id)
	{
		$org = $this->_getOrganizationOr404($organization_id, 'edit');

		$org->picture_blob = null;
		$this->em->persist($org);
		$this->em->flush();

		return $this->createSuccessResponse();
	}

	public function getOrganizationActivityStreamAction($organization_id)
	{
		$org = $this->_getOrganizationOr404($organization_id);

		$page = $this->in->getUint('page');
		if (!$page) $page = 1;

		$per_page = Numbers::bound($this->in->getUint('per_page') ?: 25, 1, 250);
		$offset = $per_page * ($page - 1);

		$activity = $this->em->getRepository('DeskPRO:PersonActivity')->getForOrganization($org, $per_page, $offset);
		$total = $this->em->getRepository('DeskPRO:PersonActivity')->countForOrganization($org);

		return $this->createApiResponse(array(
			'page' => $page,
			'per_page' => $per_page,
			'total' => $total,
			'activity' => $this->getApiData($activity)
		));
	}

	public function getOrganizationMembersAction($organization_id)
	{
		$org = $this->_getOrganizationOr404($organization_id);

		$terms = array(
			array(
				'type' => \Application\DeskPRO\Searcher\PersonSearch::TERM_ORGANIZATION,
				'op' => 'contains',
				'options' => array($org->id)
			)
		);

		if ($this->in->checkIsset('order')) {
			$order_by = $this->in->getString('order');
		} else {
			$order_by = 'person.name:asc';
		}

		$extra = array();
		if ($order_by !== null) {
			$extra['order_by'] = $order_by;
		}

		$result_cache = $this->getApiSearchResult('person', $terms, $extra, $this->in->getUint('cache_id'), new \Application\DeskPRO\Searcher\PersonSearch());

		$page = $this->in->getUint('page');
		if (!$page) $page = 1;

		$per_page = Numbers::bound($this->in->getUint('per_page') ?: 25, 1, 250);

		$person_ids = $result_cache->results;

		$page_ids = \Orb\Util\Arrays::getPageChunk($person_ids, $page, $per_page);
		$people = App::getEntityRepository('DeskPRO:Person')->getByIds($page_ids, true);

		return $this->createApiResponse(array(
			'page' => $page,
			'per_page' => $per_page,
			'total' => count($person_ids),
			'cache_id' => $result_cache->id,
			'people' => $this->getApiData($people)
		));
	}

	public function getOrganizationTicketsAction($organization_id)
	{
		$org = $this->_getOrganizationOr404($organization_id);

		$terms = array(
			array(
				'type' => \Application\DeskPRO\Searcher\TicketSearch::TERM_ORGANIZATION,
				'op' => 'contains',
				'options' => array($org->id)
			)
		);

		if ($this->in->checkIsset('order')) {
			$order_by = $this->in->getString('order');
		} else {
			$order_by = 'ticket.date_created:desc';
		}

		$extra = array();
		if ($order_by !== null) {
			$extra['order_by'] = $order_by;
		}

		$result_cache = $this->getApiSearchResult('ticket', $terms, $extra, $this->in->getUint('cache_id'), new \Application\DeskPRO\Searcher\TicketSearch());

		$page = $this->in->getUint('page');
		if (!$page) $page = 1;

		$per_page = Numbers::bound($this->in->getUint('per_page') ?: 25, 1, 250);

		$person_ids = $result_cache->results;

		$page_ids = \Orb\Util\Arrays::getPageChunk($person_ids, $page, $per_page);
		$tickets = App::getEntityRepository('DeskPRO:Ticket')->getByIds($page_ids, true);

		return $this->createApiResponse(array(
			'page' => $page,
			'per_page' => $per_page,
			'total' => count($person_ids),
			'cache_id' => $result_cache->id,
			'tickets' => $this->getApiData($tickets)
		));
	}

	public function getOrganizationChatsAction($organization_id)
	{
		$org = $this->_getOrganizationOr404($organization_id);

		$member_ids = App::getEntityRepository('DeskPRO:Person')->getOrganizationMemberIds($org);
		if ($member_ids)
		{
			$terms = array(
				array(
					'type' => \Application\DeskPRO\Searcher\ChatConversationSearch::TERM_PERSON_ID,
					'op' => 'contains',
					'options' => $member_ids
				)
			);

			$order_by = 'chat_conversations.id:desc';

			$extra = array();
			if ($order_by !== null) {
				$extra['order_by'] = $order_by;
			}

			$result_cache = $this->getApiSearchResult('chat', $terms, $extra, $this->in->getUint('cache_id'), new \Application\DeskPRO\Searcher\ChatConversationSearch());

			$ids = $result_cache->results;
			$cache_id = $result_cache->id;
		} else {
			$ids = array();
			$cache_id = 0;
		}

		$page = $this->in->getUint('page');
		if (!$page) $page = 1;

		$per_page = Numbers::bound($this->in->getUint('per_page') ?: 25, 1, 250);

		$page_ids = \Orb\Util\Arrays::getPageChunk($ids, $page, $per_page);
		$chats = App::getEntityRepository('DeskPRO:ChatConversation')->getByIds($page_ids, true);

		return $this->createApiResponse(array(
			'page' => $page,
			'per_page' => $per_page,
			'total' => count($ids),
			'cache_id' => $cache_id,
			'chats' => $this->getApiData($chats)
		));
	}

	public function getOrganizationSlasAction($organization_id)
	{
		$org = $this->_getOrganizationOr404($organization_id);

		return $this->createApiResponse(array(
			'slas' => $this->getApiData($org->slas)
		));
	}

	public function postOrganizationSlasAction($organization_id)
	{
		$org = $this->_getOrganizationOr404($organization_id, 'edit');

		$sla_id = $this->in->getUint('sla_id');
		$sla = $this->em->getRepository('DeskPRO:Sla')->find($sla_id);
		if (!$sla) {
			return $this->createApiErrorResponse('invalid_argument.sla_id', 'SLA not found');
		}

		$sla->addOrganization($org);
		$this->em->persist($sla);
		$this->em->flush();

		return $this->createApiCreateResponse(
			array('id' => $sla->id),
			$this->generateUrl('api_organizations_organization_sla', array('organization_id' => $org->id, 'sla_id' => $sla->id), true)
		);
	}

	public function getOrganizationSlaAction($organization_id, $sla_id)
	{
		$org = $this->_getOrganizationOr404($organization_id);

		$sla = $this->em->getRepository('DeskPRO:Sla')->find($sla_id);
		if (!$sla) {
			return $this->createApiErrorResponse('invalid_argument.sla_id', 'SLA not found');
		}

		$exists = false;

		foreach ($org->slas AS $sla) {
			if ($sla->id == $sla_id) {
				$exists = true;
				break;
			}
		}

		return $this->createApiResponse(array('exists' => $exists));
	}

	public function deleteOrganizationSlaAction($organization_id, $sla_id)
	{
		$org = $this->_getOrganizationOr404($organization_id, 'edit');

		$sla = $this->em->getRepository('DeskPRO:Sla')->find($sla_id);
		if (!$sla) {
			return $this->createApiErrorResponse('invalid_argument.sla_id', 'SLA not found');
		}

		$sla->removeOrganization($org);
		$this->em->persist($sla);
		$this->em->flush();

		return $this->createSuccessResponse();
	}

	public function getOrganizationNotesAction($organization_id)
	{
		$org = $this->_getOrganizationOr404($organization_id);

		$notes = $this->em->getRepository('DeskPRO:OrganizationNote')->getNotesForOrganization($org);

		return $this->createApiResponse(array('notes' => $this->getApiData($notes)));
	}

	public function postOrganizationNotesAction($organization_id)
	{
		$org = $this->_getOrganizationOr404($organization_id, 'note');

		$note_text = $this->in->getString('note');
		if (!$note_text) {
			return $this->createApiErrorResponse('required_field', 'note field is empty or missing');
		}

		$note = new \Application\DeskPRO\Entity\OrganizationNote();
		$note['agent'] = $this->person;
		$note['organization'] = $org;
		$note['note'] = $note_text;

		$this->em->persist($note);
		$this->em->flush();

		return $this->createApiCreateResponse(
			array('id' => $note->id),
			$this->generateUrl('api_organizations_organization_notes_note', array('organization_id' => $org->id, 'note_id' => $note->id), true)
		);
	}

	public function getOrganizationNoteAction($organization_id, $note_id)
	{
		$org = $this->_getOrganizationOr404($organization_id);

		$note = $this->em->getRepository('DeskPRO:OrganizationNote')->find($note_id);
		if (!$note || $note->organization->id != $org->id) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		return $this->createApiResponse(array('note' => $note->toApiData()));
	}

	public function getOrganizationBillingChargesAction($organization_id)
	{
		$organization = $this->_getOrganizationOr404($organization_id);

		$per_page = Numbers::bound($this->in->getUint('per_page') ?: 25, 1, 250);

		$page = $this->in->getUint('page');
		if (!$page) $page = 1;

		$offset = ($page - 1) * $per_page;

		$charges = $this->em->getRepository('DeskPRO:TicketCharge')->getChargesForOrganization($organization, $per_page, $offset);
		$charge_totals = $this->em->getRepository('DeskPRO:TicketCharge')->getTotalChargesForOrganization($organization);

		return $this->createApiResponse(array(
			'total_charge_time' => $charge_totals['charge_time'],
			'total_charge_amount' => $charge_totals['charge'],
			'total' => $charge_totals['count'],
			'per_page' => $per_page,
			'page' => $page,
			'charges' => $this->getApiData($charges)
		));
	}

	public function getOrganizationEmailDomainsAction($organization_id)
	{
		$org = $this->_getOrganizationOr404($organization_id);

		$org_email_domains = $this->em->getRepository('DeskPRO:OrganizationEmailDomain')->getDomainsForOrganization($org);

		$org_count_domain_nonmembers   = $this->em->getRepository('DeskPRO:PersonEmail')->countDomainsWithNoCompany($org_email_domains, $org);
		$org_count_domain_takenmembers = $this->em->getRepository('DeskPRO:PersonEmail')->countDomainsWithOtherCompany($org_email_domains, $org);
		$org_count_domain_members      = $this->em->getRepository('DeskPRO:OrganizationEmailDomain')->countMembersAtDomains($org, $org_email_domains);

		$domains = array();
		foreach ($org_email_domains AS $domain) {
			$domains[] = array(
				'domain' => $domain,
				'members' => isset($org_count_domain_members[$domain]) ? $org_count_domain_members[$domain] : 0,
				'nonmembers' => isset($org_count_domain_nonmembers[$domain]) ? $org_count_domain_nonmembers[$domain] : 0,
				'taken_members' => isset($org_count_domain_takenmembers[$domain]) ? $org_count_domain_takenmembers[$domain] : 0,
			);
		}

		return $this->createApiResponse(array('domains' => $domains));
	}

	public function postOrganizationEmailDomainsAction($organization_id)
	{
		$org = $this->_getOrganizationOr404($organization_id, 'edit');

		$domain = $this->in->getString('domain');
		if (!$domain) {
			return $this->createApiErrorResponse('required_field.domain', 'domain is missing');
		}

		$org_domain_manager = $this->container->getSystemService('org_email_domain_manager');

		if ($org_domain_manager->isInUse($domain)) {
			return $this->createApiErrorResponse('invalid_argument.domain', 'domain is in use');
		}

		$domain_rec = $org_domain_manager->assignDomain($domain, $org);

		return $this->createApiCreateResponse(
			array('domain' => $domain_rec->domain),
			$this->generateUrl('api_organizations_organization_email_domain', array('organization_id' => $org->id, 'domain' => $domain_rec->domain), true)
		);
	}

	public function getOrganizationEmailDomainAction($organization_id, $domain)
	{
		$org = $this->_getOrganizationOr404($organization_id);

		$exists = false;
		foreach ($org->email_domains AS $email_domain) {
			if ($email_domain->domain == $domain) {
				$exists = true;
				break;
			}
		}

		return $this->createApiResponse(array('exists' => $exists));
	}

	public function postOrganizationEmailDomainMoveUsersAction($organization_id, $domain)
	{
		$org = $this->_getOrganizationOr404($organization_id, 'edit');
		$orgdomain = $this->em->getRepository('DeskPRO:OrganizationEmailDomain')->find(array('organization' => $org, 'domain' => $domain));

		if ($orgdomain) {
			$org_domain_manager = $this->container->getSystemService('org_email_domain_manager');
			$org_domain_manager->moveNonCompanyUsers($orgdomain);
		}

		return $this->createSuccessResponse();
	}

	public function postOrganizationEmailDomainMoveTakenUsersAction($organization_id, $domain)
	{
		$org = $this->_getOrganizationOr404($organization_id, 'edit');
		$orgdomain = $this->em->getRepository('DeskPRO:OrganizationEmailDomain')->find(array('organization' => $org, 'domain' => $domain));

		if ($orgdomain) {
			$org_domain_manager = $this->container->getSystemService('org_email_domain_manager');
			$org_domain_manager->moveOtherCompanyUsers($orgdomain);
		}

		return $this->createSuccessResponse();
	}

	public function deleteOrganizationEmailDomainAction($organization_id, $domain)
	{
		$org = $this->_getOrganizationOr404($organization_id, 'edit');
		$orgdomain = $this->em->getRepository('DeskPRO:OrganizationEmailDomain')->find(array('organization' => $org, 'domain' => $domain));

		if ($orgdomain) {
			$org_domain_manager = $this->container->getSystemService('org_email_domain_manager');
			$org_domain_manager->unassignDomain($orgdomain, $this->in->getBool('remove_users'));
		}

		return $this->createSuccessResponse();
	}

	public function getOrganizationContactDetailsAction($organization_id)
	{
		$org = $this->_getOrganizationOr404($organization_id);

		return $this->createApiResponse(array('details' => $this->getApiData($org->contact_data)));
	}

	public function postOrganizationContactDetailsAction($organization_id)
	{
		$org = $this->_getOrganizationOr404($organization_id, 'edit');

		$type = $this->in->getString('type');
		$data = $this->in->getArrayValue('data');
		$comment = $this->in->getString('comment');

		if (!$type) {
			return $this->createApiErrorResponse('required_field.type', 'type is empty or missing');
		}
		if (!$data) {
			return $this->createApiErrorResponse('required_field.data', 'data is empty or missing');
		}

		$data['comment'] = $comment;

		$contact_data = new \Application\DeskPRO\Entity\OrganizationContactData();
		$contact_data->contact_type = $type;
		try {
			$contact_data->applyFormData($data);
		} catch (\InvalidArgumentException $e) {
			return $this->createApiErrorResponse('invalid_argument.type', 'type is invalid');
		}

		$all_empty = true;
		for ($i = 1; $i <= 10; $i++) {
			if ($contact_data->{'field_' . $i}) {
				$all_empty = false;
				break;
			}
		}

		if ($all_empty) {
			return $this->createApiErrorResponse('invalid_argument.data', 'data contains invalid data');
		}

		$contact_data->organization = $org;

		$this->em->persist($contact_data);
		$this->em->flush();

		return $this->createApiCreateResponse(
			array('id' => $contact_data->id),
			$this->generateUrl('api_organizations_organization_contact_detail', array('organization_id' => $org->id, 'contact_id' => $contact_data->id), true)
		);
	}

	public function getOrganizationContactDetailAction($organization_id, $contact_id)
	{
		$org = $this->_getOrganizationOr404($organization_id);

		foreach ($org->contact_data AS $contact) {
			if ($contact->id == $contact_id) {
				return $this->createApiResponse(array('exists' => true));
			}
		}

		return $this->createApiResponse(array('exists' => false));
	}

	public function deleteOrganizationContactDetailAction($organization_id, $contact_id)
	{
		$org = $this->_getOrganizationOr404($organization_id, 'edit');

		foreach ($org->contact_data AS $key => $contact) {
			if ($contact->id == $contact_id) {
				unset($org->contact_data[$key]);
				$this->em->persist($org);
				$this->em->flush();
				break;
			}
		}

		return $this->createSuccessResponse();
	}

	public function getOrganizationGroupsAction($organization_id)
	{
		$org = $this->_getOrganizationOr404($organization_id);

		return $this->createApiResponse(array('groups' => $this->getApiData($org->usergroups)));
	}

	public function postOrganizationGroupsAction($organization_id)
	{
		$org = $this->_getOrganizationOr404($organization_id, 'edit');

		$group_id = $this->in->getUint('id');

		$match = $this->db->fetchColumn('
			SELECT id
			FROM usergroups
			WHERE id = ?
				AND sys_name IS NULL
				AND is_agent_group = 0
		', array($group_id));
		if (!$match) {
			return $this->createApiErrorResponse('required_field', 'id must be specified as a non-system group');
		}

		$exists = false;
		foreach ($org->usergroups AS $group) {
			if ($group->id == $group_id) {
				$exists = true;
			}
		}

		if (!$exists) {
			$this->db->insert('organization2usergroups', array(
				'organization_id' => $org->id,
				'usergroup_id' => $group_id
			));
		}

		return $this->createApiCreateResponse(
			array('id' => $group_id),
			$this->generateUrl('api_organizations_organization_group', array('organization_id' => $org->id, 'usergroup_id' => $group_id), true)
		);
	}

	public function getOrganizationGroupAction($organization_id, $usergroup_id)
	{
		$org = $this->_getOrganizationOr404($organization_id);

		foreach ($org->usergroups AS $group) {
			if ($group->id == $usergroup_id) {
				return $this->createApiResponse(array('exists' => true));
			}
		}

		return $this->createApiResponse(array('exists' => false));
	}

	public function deleteOrganizationGroupAction($organization_id, $usergroup_id)
	{
		$org = $this->_getOrganizationOr404($organization_id, 'edit');

		foreach ($org->usergroups AS $key => $group) {
			if ($group->id == $usergroup_id) {
				if ($group->is_agent_group) {
					return $this->createApiErrorResponse('invalid_group', 'Group is an agent group');
				}
				unset($org->usergroups[$key]);
				$this->em->persist($org);
				$this->em->flush();
				break;
			}
		}

		return $this->createSuccessResponse();
	}

	public function getOrganizationLabelsAction($organization_id)
	{
		$org = $this->_getOrganizationOr404($organization_id);

		return $this->createApiResponse(array('labels' => $this->getApiData($org->labels)));
	}

	public function postOrganizationLabelsAction($organization_id)
	{
		$org = $this->_getOrganizationOr404($organization_id, 'edit');
		$label = $this->in->getString('label');

		if ($label === '') {
			return $this->createApiErrorResponse('required_field.label', "Field 'label' missing or empty");
		}

		$org->getLabelManager()->addLabel($label);
		$this->em->persist($org);
		$this->em->flush();

		return $this->createApiCreateResponse(
			array('label' => $label),
			$this->generateUrl('api_organizations_organization_label', array('organization_id' => $org->id, 'label' => $label), true)
		);
	}

	public function getOrganizationLabelAction($organization_id, $label)
	{
		$org = $this->_getOrganizationOr404($organization_id);

		if ($org->getLabelManager()->hasLabel($label)) {
			return $this->createApiResponse(array('exists' => true));
		} else {
			return $this->createApiResponse(array('exists' => false));
		}
	}

	public function deleteOrganizationLabelAction($organization_id, $label)
	{
		$org = $this->_getOrganizationOr404($organization_id, 'edit');

		$org->getLabelManager()->removeLabel($label);
		$this->em->persist($org);
		$this->em->flush();

		return $this->createSuccessResponse();
	}

	public function getFieldsAction()
	{
		$field_manager = $this->container->getSystemService('org_fields_manager');
		$fields = $field_manager->getFields();

		return $this->createApiResponse(array('fields' => $this->getApiData($fields)));
	}

	public function getGroupsAction()
	{
		$groups = $this->em->createQuery('
			SELECT g
			FROM DeskPRO:Usergroup g INDEX BY g.id
			WHERE g.is_agent_group = false AND g.sys_name IS NULL
			ORDER BY g.id
		')->execute();

		return $this->createApiResponse(array('groups' => $this->getApiData($groups)));
	}

	/**
	 * @param integer $id
	 * @param string $check_perm
	 * @return \Application\DeskPRO\Entity\Organization
	 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	protected function _getOrganizationOr404($id, $check_perm = '')
	{
		$org = $this->em->getRepository('DeskPRO:Organization')->findOneById($id);

		if (!$org) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no organization with ID $id");
		}

		if ($check_perm) {
			switch ($check_perm) {
				case 'edit':
				case 'delete':
				case 'create':
				case 'note':
					if (!$this->person->hasPerm('agent_org.' . $check_perm)) {
						throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
					}
					break;

				default:
					throw new \Exception("Uknown perm type $check_perm");
			}
		}

		return $org;
	}
}
