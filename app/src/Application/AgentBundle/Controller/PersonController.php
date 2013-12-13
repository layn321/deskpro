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
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\PersonEmail;
use Application\DeskPRO\Entity\PersonContactData;
use Application\DeskPRO\Entity\PersonNote;
use Application\DeskPRO\Entity\Organization;

use Application\DeskPRO\App;

/**
 * Handles viewing and editing a person
 */
class PersonController extends AbstractController
{
	############################################################################
	# /agent/people/:person_id                                   agent_people_view
	############################################################################

	public function viewAction($person_id, $with_warn_for_email = false)
	{
		$person = $this->getPersonOr404($person_id);

		if (!$person['first_name'] && !$person['last_name'] && $person['name']) {
			$parts = explode(' ', $person['name'], 2);
			$parts = Arrays::removeFalsey($parts);

			if ($parts) {
				$person['first_name'] = $parts[0];
				if (isset($parts[1])) {
					$person['last_name'] = $parts[1];
				}
			}
		}

		if ($person->is_agent) {
			$person->loadHelper('Agent');
		}

		#------------------------------
		# Custom fields
		#------------------------------

		$field_manager = $this->container->getSystemService('person_fields_manager');
		$custom_fields = $field_manager->getDisplayArrayForObject($person);

		#------------------------------
		# Misc info needed
		#------------------------------

		$notes = $this->em->getRepository('DeskPRO:PersonNote')->getNotesForPerson($person);
		$person_tickets = $this->em->getRepository('DeskPRO:Ticket')->getPersonTickets($person, 251, true);
		$person_tickets_count = $this->em->getRepository('DeskPRO:Ticket')->countTicketsForPerson($person);

		$max = 5;
		$person_tickets_initial = array();
		foreach ($person_tickets as $t) {
			if ($t->status == 'open') {
				$person_tickets_initial[$t->id] = $t;
				unset($person_tickets[$t->id]);
				if (count($person_tickets_initial) >= $max) break;
			}
		}
		if (count($person_tickets_initial) < $max) {
			foreach ($person_tickets as $t) {
				if ($t->status == 'pending') {
					$person_tickets_initial[$t->id] = $t;
					unset($person_tickets[$t->id]);
					if (count($person_tickets_initial) >= $max) break;
				}
			}
			if (count($person_tickets_initial) < $max) {
				foreach ($person_tickets as $t) {
					$person_tickets_initial[$t->id] = $t;
					unset($person_tickets[$t->id]);
					if (count($person_tickets_initial) >= $max) break;
				}
			}
		}

		$person_charges = $this->em->getRepository('DeskPRO:TicketCharge')->getChargesForPerson($person, 20);
		$person_charge_totals = $this->em->getRepository('DeskPRO:TicketCharge')->getTotalChargesForPerson($person);

		$activity_stream = $this->em->getRepository('DeskPRO:PersonActivity')->getForPerson($person, 50);

		$contact_data = array();
		foreach ($person->contact_data as $cd) {
			if (!isset($contact_data[$cd->contact_type])) {
				$contact_data[$cd->contact_type] = array();
			}
			$contact_data[$cd->contact_type][] = $cd->getTemplateVars();
		}

		$session = $this->em->getRepository('DeskPRO:Session')->getSessionForPerson($person);
		if ($session) {
			$visitor = $session->visitor;
		} else {
			$visitor = $this->em->getRepository('DeskPRO:Visitor')->getVisitorForPerson($person);
		}

		$timezone_options = \DateTimeZone::listIdentifiers();
		$usergroup_names = $this->em->getRepository('DeskPRO:Usergroup')->getUsergroupNames();
		$reg_group = $this->em->getRepository('DeskPRO:Usergroup')->find(\Application\DeskPRO\Entity\Usergroup::REG_ID);

		$person->loadHelper('PermissionsManager');
		$person_usergroups_ids = $person->getPermissionsManager()->getUsergroupIds();
		$person_org_usergroups_ids = $person->getPermissionsManager()->getOrganizationUsergroupIds();

		// Org stuff
		$org_members_count = null;
		$org_contact_data = null;
		if ($person->organization) {
			$org_members_count = $this->em->getRepository('DeskPRO:Organization')->countMembersFor($person->organization);

			$org_contact_data = array();
			foreach ($person->organization->contact_data as $cd) {
				if (!isset($org_contact_data[$cd->contact_type])) {
					$org_contact_data[$cd->contact_type] = array();
				}
				$org_contact_data[$cd->contact_type][] = $cd->getTemplateVars();
			}
		}

		$person_chats = $this->em->getRepository('DeskPRO:ChatConversation')->getPastChatsForPerson($person);
		$person_chats_count = count($person_chats);

		$is_editable = $this->isPersonEditable($person);
		$perms = array(
			'edit'             => $is_editable && $this->person->hasPerm('agent_people.edit'),
			'delete'           => $is_editable && $this->person->hasPerm('agent_people.delete'),
			'merge'            => $is_editable && $this->person->hasPerm('agent_people.merge'),
			'disable'          => $is_editable && !$person->is_agent && $this->person->hasPerm('agent_people.disable'),
			'manage_emails'    => $is_editable && $this->person->hasPerm('agent_people.manage_emails'),
			'reset_password'   => $is_editable && !$person->is_agent && $this->person->hasPerm('agent_people.reset_password'),
			'notes'            => $is_editable && $this->person->hasPerm('agent_people.notes'),
			'org_create'       => $is_editable && $this->person->hasPerm('agent_org.create'),
			'login_as'         => !$person->is_agent && $this->person->hasPerm('agent_people.login_as')
		);

		$person_api = $person->getDataForWidget();

        $is_vcf = $this->in->getBool('vcf');

        if($is_vcf) {
            $response = new \Symfony\Component\HttpFoundation\Response();
            $response->headers->set('Content-Type', 'text/vcf');

            if($person->getName()) {
                $filename = $person->getName();
            }
            else {
                $filename = $person->getEmailAddress();
            }

            $filename = str_replace(' ', '_', $filename);
            $filename = preg_replace('[^a-zA-Z0-9_.@-]' , '', $filename);

            if(strlen($filename) == 0) {
                $filename = 'Unknown_'.$person->id;
            }

            if(strlen($filename) > 128) {
                $filename = substr($filename, 0, 128);
            }

            $response->headers->set('Content-Disposition', 'attachment; filename='.$filename.'.vcf');
            $vcard = \File_IMC::build('vCard');

            $vcard->setFormattedName($person->name);
            $vcard->setName($person->last_name, $person->first_name, '', '', '');
            //$vcard->setPhoto($person->gravatar_url);


            if($person->organization) {
				$vcard->addOrganization($person->organization->name);
            }

            if(!empty($person['organization_position'])) {

                $vcard->setTitle($person['organization_position']);
            }

            foreach($person->emails as $email) {
                $vcard->addEmail($email->email);
            }

            foreach($contact_data as $c_data)
            {
                foreach($c_data as $data) {
                    switch($data['contact_type']) {
                        case 'phone':
                            if(empty($data['number']))
                                break;

                            $tel = '';

                            if(!empty($data['country_calling_code']))
                                $tel .= '+'.$data['country_calling_code'].'-';


                            $tel .= $data['number'];

                            $vcard->addTelephone($tel);
                            break;
                        case 'website':
                            $vcard->setURL($data['url']);
                            break;
                        case 'address':
                            $vcard->addAddress(
                                '',
                                '',
                                $data['address'],
                                $data['city'],
                                $data['state'],
                                $data['zip'],
                                $data['country']
                            );
                            break;
                    }
                }
            }

            $response->setContent($vcard->fetch());
            return $response;
        }

		$validating_emails = $this->em->getRepository('DeskPRO:PersonEmailValidating')->getForPerson($person);

		$has_email_validating = false;
		foreach ($person->emails as $e) {
			if (!$e->is_validated) {
				$has_email_validating = true;
				break;
			}
		}

		$banned_emails = array();
		foreach ($person->getEmailAddresses() as $eml) {
			$match = null;
			if (App::getOrm()->getRepository('DeskPRO:BanEmail')->isEmailBanned($eml, $match)) {
				$banned_emails[$eml] = $eml;
			}
		}

		return $this->render('AgentBundle:Person:view.html.twig', array(
			'with_warn_for_email'       => $with_warn_for_email,
			'person'                    => $person,
			'banned_emails'             => $banned_emails,
			'validating_emails'         => $validating_emails,
			'has_email_validating'      => $has_email_validating,
			'person_api'                => $person_api,
			'person_usergroups_ids'     => $person_usergroups_ids,
			'person_org_usergroups_ids' => $person_org_usergroups_ids,
			'session'                   => $session,
			'visitor'                   => $visitor,
			'timezone_options'          => $timezone_options,
			'usergroup_names'           => $usergroup_names,
			'contact_data'              => $contact_data,
			'activity_stream'           => $activity_stream,
			'custom_fields'             => $custom_fields,
			'notes'                     => $notes,
			'person_tickets'            => $person_tickets,
			'person_chats'              => $person_chats,
			'person_chats_count'        => $person_chats_count,
			'person_tickets_initial'    => $person_tickets_initial,
			'person_tickets_count'      => $person_tickets_count,
			'person_charges'            => $person_charges,
			'person_charge_totals'      => $person_charge_totals,
			'org_members_count'         => $org_members_count,
			'org_contact_data'          => $org_contact_data,
			'perms'                     => $perms,
			'is_person_editable'        => $is_editable,
			'reg_group'                 => $reg_group,
			'person_object_counts'      => $this->em->getRepository('DeskPRO:Person')->getPersonObjectCounts($person)
		));
	}

	public function getBasicInfoAction($person_id)
	{
		$person = $this->getPersonOr404($person_id);

		return $this->createJsonResponse(array(
			'person_id' => $person,
			'name' => $person->getDisplayName(),
			'email' => $person->getPrimaryEmailAddress(),
			'contact_name' => $person->getDisplayContact(),
			'url' => $this->generateUrl('agent_people_view', array('person_id' => $person->id))
		));
	}

	public function validateEmailAddressAction($id, $security_token)
	{
		$this->ensureAuthToken('validate_email', $security_token);

		$email_validating = $this->em->find('DeskPRO:PersonEmailValidating', $id);
		if (!$email_validating) {
			throw $this->createNotFoundException();
		}

		$validator = new \Application\DeskPRO\People\EmailValidator($email_validating);

		$email_exists = $this->em->getRepository('DeskPRO:PersonEmail')->getEmail($email_validating->getEmail());
		if ($email_exists) {
			return $this->createJsonResponse(array('error' => true, 'message' => 'Email already exists on another account'));
		}

		$email = $validator->validate();

		return $this->createJsonResponse(array('success' => true));
	}

	############################################################################
	# viewSession
	############################################################################

	public function viewSessionAction($session_id)
	{
		$session = $this->em->find('DeskPRO:Session', $session_id);

		if ($session->is_person) {
			return $this->viewAction($session->person->id);
		}

		$visitor = $session->visitor;
		$related_person = null;
		if ($session->visitor && $session->visitor->email) {
			$related_person = $this->em->getRepository('DeskPRO:Person')->findOneByEmail($session->visitor->email);

			if ($related_person) {
				return $this->viewAction($related_person->id, $session->visitor->email);
			}
		}

		$person_chats = $this->em->getRepository('DeskPRO:ChatConversation')->getPastChatsForVisitor($session->visitor);
		$person_chats_count = count($person_chats);

		return $this->render('AgentBundle:Person:view-session.html.twig', array(
			'person_chats'       => $person_chats,
			'person_chats_count' => $person_chats_count,
			'session'            => $session,
			'visitor'            => $visitor,
		));
	}

	############################################################################
	# /agent/people/:person_id/ajax-save                     agent_people_ajaxsave
	############################################################################

	public function ajaxSaveAction($person_id)
	{
		$person = $this->getPersonOr404($person_id);

		$data = array(
			'success' => true
		);

		$action = $this->in->getString('action');

		if (!$this->person->hasPerm('agent_people.edit') || !$this->isPersonEditable($person)) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		switch ($action) {
			case 'name':
				if ($this->in->getString('name')) {
					$person->name = $this->in->getString('name');
					$this->em->persist($person);
				}
				break;

			case 'quick-edit-name':
				$person->name = $this->in->getString('name');
				$person->title_prefix = $this->in->getString('title_prefix');
				if ($person->organization) {
					$person->organization_position = $this->in->getString('organization_position');
				}
				$this->em->persist($person);
				break;

			case 'timezone':
				if (in_array($this->in->getString('timezone'), \DateTimeZone::listIdentifiers())) {
					$person->timezone = $this->in->getString('timezone');
					$this->em->persist($person);
				}

				$data['bit_html'] = $this->renderView('AgentBundle:Person:view-bit-timezoneinfo.html.twig', array('person' => $person));

				break;

			case 'set-is-disabled':
				if (!$this->person->hasPerm('agent_people.disable') || !$this->isPersonEditable($person)) {
					throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
				}
				$person->is_disabled = $this->in->getBool('is_disabled');
				$this->em->persist($person);
				break;

			case 'disable_autoresponses':
				$person->setDisableAutoresponses(
					$this->in->getBool('disable_autoresponses'),
					'Disabled by ' . $this->person->getDisplayContact()
				);

				$this->em->persist($person);
				break;

			case 'set-primary-email':
				if (!$this->person->hasPerm('agent_people.manage_emails')) {
					throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
				}

				$email_id = $this->in->getUint('email_id');
				$set_email = $person->getEmailId($email_id);
				if ($set_email) {
					$person->primary_email = $set_email;
					$this->em->persist($person);
				}

				$data['primary_email_address'] = $person->primary_email->email;
				break;

			case 'delete-picture':
				$person->setDisablePicture(false);
				$person->setPictureBlob(null);

				if ($this->in->getBool('disable_picture')) {
					$person->setDisablePicture(true);
				}

				$this->em->persist($person);
				break;

			case 'set-picture':
				$person->setDisablePicture(false);
				$blob = $this->em->find('DeskPRO:Blob', $this->in->getUint('blob_id'));
				if ($blob) {
					$person->setPictureBlob($blob);
					$this->em->persist($person);
				}
				break;

			case 'set-summary':
				$person->summary = $this->in->getString('summary');
				$this->em->persist($person);
				break;

			case 'set-organization':

				$name = $this->in->getString('name');
				$id = $this->in->getUint('id');
				$org = null;

				if ($id) {
					$org = $this->em->getRepository('DeskPRO:Organization')->find($id);
				} elseif ($name) {
					$org = $this->em->getRepository('DeskPRO:Organization')->getByName($name);

					if (!$org) {
						$org = new Organization();
						$org->name = $name;

						$this->em->persist($org);
						$this->em->flush();
					}
				}

				$old_org = $person->organization;

				if ($org) {
					$add = 0;
					if ($org && $person->organization && $person->organization->getId() != $org->getId()) {
						$add = 1;
					}

					$person->setOrganization($org, $this->in->getString('position'), $this->in->getBool('manager'));

					// Org stuff
					$org_members_count = null;
					$org_contact_data = null;
					if ($person->organization) {
						$org_members_count = $this->em->getRepository('DeskPRO:Organization')->countMembersFor($person->organization) + $add;

						$org_contact_data = array();
						foreach ($person->organization->contact_data as $cd) {
							if (!isset($contact_data[$cd->contact_type])) {
								$contact_data[$cd->contact_type] = array();
							}
							$org_contact_data[$cd->contact_type][] = $cd->getTemplateVars();
						}
					}

					// Regenerate the HTML block
					$html = $this->renderView('AgentBundle:Person:view-org-info.html.twig', array(
						'org' => $org,
						'person' => $person,
						'org_members_count' => $org_members_count,
						'org_contact_data' => $org_contact_data,
					));

					$data['organization_id'] = $org->id;
					$data['html'] = $html;
				} else {

					$person->setOrganization(null);

					// Regenerate the HTML block
					$html = $this->renderView('AgentBundle:Person:view-org-info.html.twig', array(
						'person' => $person,
					));

					$data['organization_id'] = 0;
					$data['html'] = $html;
				}

				if ($person->organization) {
					$tickets = $this->em->createQuery("
						SELECT t
						FROM DeskPRO:Ticket t
						WHERE t.person = ?0 AND t.organization IS NULL
						ORDER BY t.id DESC
					")->setMaxResults(250)->execute(array($person));

					foreach ($tickets as $t) {
						$t->organization = $person->organization;
						$this->em->persist($t);
						$this->em->flush();
					}
				} elseif ($old_org) {
					$tickets = $this->em->createQuery("
						SELECT t
						FROM DeskPRO:Ticket t
						WHERE t.person = ?0 AND t.organization = ?1
						ORDER BY t.id DESC
					")->setMaxResults(250)->execute(array($person, $old_org));
					foreach ($tickets as $t) {
						$t->organization = null;
						$this->em->persist($t);
						$this->em->flush();
					}
				}

				break;

			case 'set-usergroups':
				$usergroup_ids = $this->in->getCleanValueArray('usergroup_ids', 'uint', 'discard');
				$usergroup_ids = Arrays::removeFalsey($usergroup_ids);

				if ($usergroup_ids) {
					$usergroup_ids = array_unique($usergroup_ids);

					// Make sure only valid ones are set
					$usergroup_ids = $this->db->fetchAllCol("
						SELECT id
						FROM usergroups
						WHERE id IN (" . implode(',', $usergroup_ids).")
							AND sys_name IS NULL
					");
				}

				$this->container->getDb()->executeUpdate("
					DELETE person2usergroups
					FROM person2usergroups
					LEFT JOIN usergroups ON (usergroups.id = person2usergroups.usergroup_id)
					WHERE usergroups.is_agent_group = 0 AND person2usergroups.person_id = ?
				", array($person->getId()));

				if ($usergroup_ids) {
					$inserts = array();
					foreach ($usergroup_ids as $uid) {
						$inserts[] = array('person_id' => $person->getId(), 'usergroup_id' => $uid);
					}

					$this->db->batchInsert('person2usergroups', $inserts);
				}
				break;

			case 'set-slas':
				$sla_ids = $this->in->getCleanValueArray('sla_ids', 'uint', 'discard');
				$slas = $this->em->getRepository('DeskPRO:Sla')->getAllSlas();


				foreach ($slas AS $sla) {
					if (in_array($sla->id, $sla_ids)) {
						$sla->addPerson($person);
					} else {
						$sla->removePerson($person);
					}
					$this->em->persist($sla);
				}

				break;

			case 'password':
				if (!$this->person->hasPerm('agent_people.reset_password')) {
					throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
				}
				if ($this->in->getString('password')) {
					$person->setPassword($this->in->getString('password'));
					$this->em->persist($person);

					$this->db->delete('sessions', array('person_id' => $person->id));

					$email = $person->getPrimaryEmailAddress();
					if (!$email) {
						// We are implicitly validating the account when we set a password
						$validating_emails = $validating_emails = $this->em->getRepository('DeskPRO:PersonEmailValidating')->getForPerson($person);

						foreach ($validating_emails as $v_eml) {
							$validator = new \Application\DeskPRO\People\EmailValidator($v_eml);

							$email_exists = $this->em->getRepository('DeskPRO:PersonEmail')->getEmail($v_eml->getEmail());
							if ($email_exists) {
								continue;
							}

							$email = $validator->validate();
							break;
						}
					}

					if ($email) {
						$message = $this->container->getMailer()->createMessage();
						$message->setTo($person->getPrimaryEmailAddress(), $person->getDisplayName());
						$message->setTemplate('DeskPRO:emails_user:agent-changed-password.html.twig', array(
							'person' => $person
						));
						$message->enableQueueHint();
						$this->container->getMailer()->send($message);
					}
				}
				break;

			default:
				return $this->createJsonResponse(array('error' => true, 'message' => 'Unknown action'));
				break;
		}

		$this->em->persist($person);
		$this->em->flush();

		$this->db->executeUpdate("
			UPDATE people
			SET
				organization_id = ?, organization_position = ?, organization_manager = ?
			WHERE id = ?
		", array(
			$person->getOrganizationId() ?: null,
			$person->organization_position ?: '',
			$person->organization_manager ?: 0,
			$person->getId()
		));

		return $this->createJsonResponse($data);
	}

	public function ajaxSaveCustomFieldsAction($person_id)
	{
		$person = $this->getPersonOr404($person_id);

		if (!$this->person->hasPerm('agent_people.edit') || !$this->isPersonEditable($person)) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$timezone_options = \DateTimeZone::listIdentifiers();

		$timezone = $this->in->getString('timezone');
		if (!$timezone || !in_array($timezone, $timezone_options)) {
			$timezone = null;
		}

		$language = $this->in->getUint('language');
		if ($language) {
			$language = $this->container->getDataService('Language')->get($language);
		} else {
			$language = null;
		}

		$this->em->beginTransaction();

		try {
			$field_manager = $this->container->getSystemService('person_fields_manager');
			$post_custom_fields = $this->request->request->get('custom_fields', array());
			if (!empty($post_custom_fields)) {
				$field_manager->saveFormToObject($post_custom_fields, $person);
			}

			if ($timezone) {
				$person->timezone = $timezone;
				$this->em->persist($person);
			}

			$person->language = $language;

			$this->em->flush();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}

		$custom_fields = $field_manager->getDisplayArrayForObject($person);

		return $this->render('AgentBundle:Person:view-customfields-rendered-rows.html.twig', array(
			'timezone_options' => $timezone_options,
			'person' => $person,
			'custom_fields' => $custom_fields,
		));
	}

	public function changePictureOverlayAction($person_id)
	{
		$person = $this->getPersonOr404($person_id);

		return $this->render('AgentBundle:Person:change-person-picture.html.twig', array(
			'person' => $person
		));
	}

	############################################################################
	# unban-email
	############################################################################

	public function unbanEmailAction($person_id, $email_id)
	{
		$person = $this->getPersonOr404($person_id);

		if (!$this->person->hasPerm('agent_people.edit') || !$this->isPersonEditable($person)) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$email = $person->getEmailId($email_id);

		if (!$email) {
			throw $this->createNotFoundException();
		}

		$banned_pattern = null;
		if (App::getOrm()->getRepository('DeskPRO:BanEmail')->isEmailBanned($email->email, $banned_pattern)) {
			App::getDb()->delete('ban_emails', array('banned_email' => $banned_pattern));
		}

		return $this->createJsonResponse(array(
			'success' => true
		));
	}

	############################################################################
	# save-contact-data
	############################################################################

	public function saveContactDataAction($person_id)
	{
		$person = $this->getPersonOr404($person_id);

		if (!$this->person->hasPerm('agent_people.edit') || !$this->isPersonEditable($person)) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$this->em->beginTransaction();

		$errors = array();

		$changed_primary_email = false;

		$contact_data_array = array();
		foreach ($person->contact_data as $cd) {
			if (!isset($contact_data_array[$cd->contact_type])) {
				$contact_data_array[$cd->contact_type] = array();
			}
			$contact_data_array[$cd->contact_type][$cd->getId()] = $cd->getTemplateVars();
		}
		$added = array();

		try {

			if ($this->person->hasPerm('agent_people.manage_emails')) {
				// Editing emails
				$email_comments = $this->in->getCleanValueArray('emails_comment', 'string', 'uint');

				// Setting comment
				foreach ($email_comments as $email_id => $comment) {
					if (isset($person->emails[$email_id])) {
						$person->emails[$email_id]->comment = $comment;
						$this->em->persist($person->emails[$email_id]);
					}
				}

				// Adding emails
				$email_comments = $this->in->getCleanValueArray('new_emails_comment', 'string', 'uint');
				foreach ($this->in->getCleanValueArray('new_emails', 'string', 'discard') as $k => $email) {

					if (!\Orb\Validator\StringEmail::isValueValid($email)) {
						$errors[] = "\"$email\" was not saved because it is an invalid email address";
						continue;
					}

					if (App::getSystemService('gateway_address_matcher')->isManagedAddress($email)) {
						$errors[] = "\"$email\" was not saved because it belongs to a ticket account";
						continue;
					}

					$check = $this->em->getRepository('DeskPRO:PersonEmail')->getEmail($email);
					if ($check) {
						if ($check->person->id == $person->id) {
							// silent discard
						} else {
							$errors[] = "\"$email\" was not saved because it is already added to another user";
						}
						continue;
					}

					$email_rec = $person->addEmailAddressString($email);
					$email_rec->comment = isset($email_comments[$k]) ? $email_comments[$k] : '';
					$this->em->persist($email_rec);
				}

				// Removing emails
				foreach ($this->in->getCleanValueArray('remove_emails', 'uint') as $email_id) {
					$email_rec = $person->getEmailId($email_id);
					if ($email_rec) {

						if (count($person->emails) == 1) {
							$errors[] = "You cannot remove the users only email address ({$email_rec->email})";
							continue;
						}

						if ($person->primary_email && $person->primary_email->id == $email_id) {
							$changed_primary_email = true;
							$person->primary_email = null;
						}

						$this->em->remove($email_rec);
						$person->removeEmailAddressId($email_id);
					}
				}

				if ($changed_primary_email && count($person->emails)) {
					foreach ($person->emails as $e) {
						$person->primary_email = $e;
						break;
					}
				}
			} // email perm

			// Adding contact data
			foreach ($this->in->getCleanValueArray('new_contact_data') as $type => $inputs) {
				foreach ($inputs as $input) {
					$contact_data = new PersonContactData();
					$contact_data->contact_type = $type;
					$contact_data->applyFormData($input);

					$contact_data->person = $person;

					$this->em->persist($contact_data);

					$added[] = $contact_data;
				}
			}

			// Editing values
			foreach ($this->in->getCleanValueArray('contact_data') as $id => $input) {
				if (!isset($person->contact_data[$id])) {
					continue;
				}

				$person->contact_data[$id]->applyFormData($input);
				$this->em->persist($person->contact_data[$id]);
			}

			// Removing values
			foreach ($this->in->getCleanValueArray('remove_contact_data', 'uint') as $id) {
				if (isset($person->contact_data[$id])) {
					$cd = $person->contact_data[$id];
					$this->em->remove($person->contact_data[$id]);
					$person->contact_data->remove($id);

					if (isset($contact_data_array[$cd->contact_type][$cd->id])) {
						unset($contact_data_array[$cd->contact_type][$cd->id]);
					}
				}
			}

			$this->em->flush();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}

		// Reset display array
		$contact_data_array = array();
		foreach ($person->contact_data as $cd) {
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

		$is_editable = $this->isPersonEditable($person);
		$perms = array(
			'edit'             => $is_editable && $this->person->hasPerm('agent_people.edit'),
			'delete'           => $is_editable && $this->person->hasPerm('agent_people.delete'),
			'manage_emails'    => $is_editable && $this->person->hasPerm('agent_people.manage_emails'),
			'reset_password'   => $is_editable && $this->person->hasPerm('agent_people.reset_password'),
			'notes'            => $is_editable && $this->person->hasPerm('agent_people.notes'),
			'org_create'       => $is_editable && $this->person->hasPerm('agent_org.create')
		);

		$display_html = $this->renderView('AgentBundle:Person:view-contact-display.html.twig', array(
			'person' => $person,
			'contact_data' => $contact_data_array,
			'perms' => $perms,
		));
		$editor_overlay_html = $this->renderView('AgentBundle:Person:contact-overlay.html.twig', array(
			'person' => $person,
			'contact_data' => $contact_data_array,
			'perms' => $perms,
		));

		return $this->createJsonResponse(array(
			'success' => 1,
			'display_html' => $display_html,
			'editor_overlay_html' => $editor_overlay_html,
			'errors' => $errors ? $errors : false,
			'primary_email_address' => $person->getPrimaryEmailAddress(),
			'changed_primary_email' => $changed_primary_email
		));
	}


	############################################################################
	# /agent/people/:person_id/ajax-save-organization        agent_people_ajaxsave_organization
	############################################################################

	public function ajaxSaveOrganizationAction($person_id)
	{
		if (!$this->person->hasPerm('agent_people.manage_emails')) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$person = $this->getPersonOr404($person_id);

		$org_id = $this->in->getUint('organization_id');
		if (!$org_id) {
			$person['organization_id'] = 0;
			$person['organization'] = null;
			$person['organization_position'] = '';

			$em = App::getOrm();
			$em->persist($person);
			$em->flush();
			return $this->createJsonResponse(array(
				'success' => true,
				'person_id' => $person['id'],
				'organization_name' => '',
				'organization_position' => '',
			));
		}

		$org = Organization::getRepository()->find($org_id);

		$person['organization'] = $org;
		$person['organization_position'] = $this->in->getString('organization_position');

		$em = App::getOrm();
		$em->persist($person);
		$em->flush();

		return $this->createJsonResponse(array(
			'success' => true,
			'person_id' => $person['id'],
			'organization_name' => $org['name'],
			'organization_position' => $person['organization_position'],
		));
	}

	############################################################################
	# /agent/people/:person_id/ajax-save-note           agent_people_ajaxsave_note
	############################################################################

	public function ajaxSaveNoteAction($person_id)
	{
		if (!$this->person->hasPerm('agent_people.notes')) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$person = $this->getPersonOr404($person_id);

		$note_txt = $this->in->getString('note');

		if (!$note_txt) {
			return $this->createJsonResponse(array(
				'error' => true,
				'error_code' => 'no_message',
				'person_id' => $person->id,
			));
		}

		$em = App::getOrm();
		$em->beginTransaction();

		$note = new PersonNote();
		$note['agent'] = $this->person;
		$note['person'] = $person;
		$note['note'] = $note_txt;
		$em->persist($note);

		$em->flush();
		$em->commit();

		return $this->createJsonResponse(array(
			'success' => true,
			'person_id' => $person['id'],
			'note_li_html' => $this->renderView('AgentBundle:Person:note-li.html.twig', array('note' => $note))
		));
	}

	############################################################################
	# ajax-save-labels
	############################################################################

	public function ajaxSaveLabelsAction($person_id)
	{
		$person = $this->getPersonOr404($person_id);

		if (!$this->person->hasPerm('agent_people.edit') || !$this->isPersonEditable($person)) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$labels = $this->in->getCleanValueArray('labels', 'string', 'discard');

		$person->getLabelManager()->setLabelsArray($labels);

		$this->em->persist($person);
		$this->em->flush();

		return $this->createJsonResponse(array('success' => 1));
	}

	############################################################################
	# merge
	############################################################################

	public function mergeOverlayAction($person_id, $other_person_id = 0)
	{
		$person = $this->getPersonOr404($person_id);

		$field_manager = $this->container->getSystemService('person_fields_manager');
		$person_custom_fields = $field_manager->getDisplayArrayForObject($person);

		if ($other_person_id && $other_person_id != $person_id) {
			$other_person = $this->getPersonOr404($other_person_id);
			$other_custom_fields = $field_manager->getDisplayArrayForObject($other_person);
		} else {
			$other_person = false;
			$other_custom_fields = false;
		}

		return $this->render('AgentBundle:Person:merge-overlay.html.twig', array(
			'person' => $person,
			'person_custom_fields' => $person_custom_fields,
			'other_person' => $other_person,
			'other_custom_fields' => $other_custom_fields
		));
	}

	public function mergeAction($person_id, $other_person_id)
	{
		$person = $this->getPersonOr404($person_id);
		$other_person = $this->getPersonOr404($other_person_id);

		if (!$person || !$other_person) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		if (!$this->person->hasPerm('agent_people.merge') || !$this->isPersonEditable($person)) {
			return $this->createJsonResponse(array('success' => false));
		}

		if (!$this->person->hasPerm('agent_people.merge') || !$this->isPersonEditable($other_person)) {
			return $this->createJsonResponse(array('success' => false));
		}

		$old_person_id = $other_person['id'];

		$merge = new \Application\DeskPRO\People\PersonMerge\PersonMerge($this->person, $person, $other_person);
		$merge->merge();

		return $this->createJsonResponse(array(
			'success' => true,
			'id' => $person['id'],
			'old_id' => $old_person_id
		));
	}

	############################################################################
	# delete
	############################################################################

	public function deletePersonAction($person_id, $security_token)
	{
		$person = $this->getPersonOr404($person_id);

		if ($person->is_agent || !$this->person->hasPerm('agent_people.delete') || !$this->isPersonEditable($person)) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		if (!$this->session->getEntity()->checkSecurityToken('delete_person', $security_token)) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		if ($this->in->getBool('ban')) {
			foreach ($person->emails as $email) {
				$email_addy = strtolower($email->email);
				App::getDb()->replace('ban_emails', array(
					'banned_email' => $email_addy,
					'is_pattern' => 0
				));
			}
		}

		$edit_manager = $this->container->getSystemService('person_edit_manager');
		$edit_manager->setPersonContext($this->person);
		$edit_manager->deleteUser($person);

		return $this->createJsonResponse(array('success' => true));
	}

	############################################################################
	# login-as
	############################################################################

	public function loginAsAction($person_id)
	{
		$person = $this->getPersonOr404($person_id);

		if (!$this->person->hasPerm('agent_people.login_as') || !$person || $person->is_agent) {
			return $this->createNotFoundException();
		}

		foreach (array('dpsid') as $cookie_name) {
			if (!empty($_COOKIE[$cookie_name])) {
				$sess2 = $this->em->getRepository('DeskPRO:Session')->getSessionFromCode($_COOKIE[$cookie_name]);
				if ($sess2) {
					$this->em->remove($sess2);
					$this->em->flush();
				}
			}

			$cookie = \Application\DeskPRO\HttpFoundation\Cookie::makeDeleteCookie($cookie_name);
			$cookie->send();
		}

		$tmp = Entity\TmpData::create('agent_user_login', array(
			'agent_id' => $this->person->getId(),
			'person_id' => $person->id
		), '+5 minutes');
		$this->em->persist($tmp);
		$this->em->flush();

		return $this->redirectRoute('user_login_agentlogin', array('code' => $tmp->getCode()));
	}

	############################################################################
	# New person
	############################################################################

	public function newPersonAction()
	{
		if (!$this->person->hasPerm('agent_people.create')) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$state = $this->em->getRepository('DeskPRO:PersonPref')->getPrefForPersonId('agent.ui.state.newperson', $this->person->id);

		#------------------------------
		# Custom fields
		#------------------------------

		// Custom fields
		$user_field_defs = App::getApi('custom_fields.people')->getEnabledFields();
		$user_data_structured = App::getApi('custom_fields.util')->createDataHierarchy(array(), $user_field_defs);

		// We use this fieldgroup so the form names are part of custom_fields array: custom_fields[field_1] etc
		// So dont remove it even though it looks like it's not used! :-)
		$custom_fields_form = $this->get('form.factory')->createNamedBuilder('form', 'newperson[custom_fields]');
		$custom_fields = App::getApi('custom_fields.people')->getFieldsDisplayArray($user_field_defs, $user_data_structured, $custom_fields_form);

		$timezone_options = \DateTimeZone::listIdentifiers();
		$usergroup_names = $this->em->getRepository('DeskPRO:Usergroup')->getUsergroupNames();

		return $this->render('AgentBundle:Person:newperson.html.twig', array(
			'state' => $state,
			'custom_fields' => $custom_fields,
			'timezone_options' => $timezone_options,
			'usergroup_names' => $usergroup_names,
		));
	}

	public function newPersonSaveAction()
	{
		if (!$this->person->hasPerm('agent_people.create')) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$newperson = new \Application\AgentBundle\Form\Model\NewPerson($this->person);

		// Check for dupe email address
		$new_email = $this->in->getString('newperson.email');
		if (!$new_email || !\Orb\Validator\StringEmail::isValueValid($new_email)) {
			return $this->createJsonResponse(array(
				'success' => false,
				'error_messages' => array('Please enter a valid email address'),
			));
		} elseif (App::getSystemService('gateway_address_matcher')->isManagedAddress($new_email)) {
			return $this->createJsonResponse(array(
				'success' => false,
				'error_messages' => array('That email address is in use by a ticket account'),
			));
		} else {
			$check_exists = $this->em->getRepository('DeskPRO:Person')->findOneByEmail($new_email);
			if ($check_exists) {
				return $this->createJsonResponse(array(
					'success' => false,
					'error_messages' => array('The email address you entered already belongs to an existing user'),
				));
			}
		}

		$formType = new \Application\AgentBundle\Form\Type\NewPerson();
		$form = $this->get('form.factory')->create($formType, $newperson);

		if ($this->get('request')->getMethod() == 'POST') {
			$form->bindRequest($this->get('request'));
			$form->isValid();

			$newperson->setCustomFieldForm($_POST);
			$newperson->save();

			$person = $newperson->getPerson();

			$this->em->getRepository('DeskPRO:PersonPref')->deletePrefForPersonId('agent.ui.state.newperson', $this->person->id);

			return $this->createJsonResponse(array(
				'success' => true,
				'person_id' => $person['id']
			));
		} else {
			return $this->createJsonResponse(array(
				'success' => false,
			));
		}
	}

	public function isPersonEditable($person)
	{
		if ($this->person->can_admin) {
			return true;
		}

		if ($person->is_agent && $person->getId() != $this->person->getId()) {
			return false;
		}

		return true;
	}

	/**
	 * @return \Application\DeskPRO\Entity\Person
	 */
	protected function getPersonOr404($person_id)
	{
		$person = $this->em->find('DeskPRO:Person', $person_id);

		if (!$person) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no person with ID $person_id");
		}

		return $person;
	}
}
