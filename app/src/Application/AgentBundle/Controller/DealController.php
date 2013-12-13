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
 * @category Entities
 * @copyright Copyright (c) 2011 DeskPRO (http://www.deskpro.com/)
 */

namespace Application\AgentBundle\Controller;

use Application\DeskPRO\App;
use Orb\Util\Arrays;
use Application\DeskPRO\Entity;

use Application\DeskPRO\Entity\Deal;
use Application\DeskPRO\Entity\DealNote;
use Application\DeskPRO\Entity\DealStage;

use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\PersonEmail;
use Application\DeskPRO\Entity\PersonContactData;
use Application\DeskPRO\Entity\PersonNote;
use Application\DeskPRO\Entity\Organization;


use Application\DeskPRO\Entity\TaskComment;
use Application\AgentBundle\Form\Type\NewTask;
use Symfony\Component\HttpFoundation\Response;

use Application\DeskPRO\ContentSearch\RelatedContentFinder;
use Application\DeskPRO\Publish\RelatedContentUpdate;

/**
 * Handles viewing and editing deals
 */
class DealController extends AbstractController
{
    public function newAction()
    {

        $deal = new Deal();
        $deal_type = $this->em->getRepository('DeskPRO:DealType')->findAll();
        $deal_stage = $this->em->getRepository('DeskPRO:DealStage')->getDealStagesByDealType(0);
        $agents = $this->em->getRepository('DeskPRO:Person')->getAgents();
        $deal_currency = $this->em->getRepository('DeskPRO:Currency')->findAll();


        return $this->render('AgentBundle:Deal:newdeal.html.twig', array(
           'deal_type' => $deal_type,
           'deal_stage' => $deal_stage,
           'agents' => $agents,
           'person' => $this->person,
           'deal_currency' => $deal_currency,
        ));
    }


    public function newSaveAction() {
        $success = false;
        $newdeal = new \Application\AgentBundle\Form\Model\NewDeal($this->person);

        $formtype = new \Application\AgentBundle\Form\Type\NewDeal();
        $form = $this->get('form.factory')->create($formtype, $newdeal);

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bindRequest($this->get('request'));
            $form->isValid();
            $newdeal->save();

            $deal = $newdeal->getDeal();
            $success = true;
        }
        return $this->createJsonResponse(array(
            'success' => $success,
            'deal_id' => $deal['id']
        ));
    }

    /**
     * Generate the category wise list gor task.
     * @return html
     */

    public function getSectionDataAction()
    {

        $deal_repository = $this->em->getRepository('DeskPRO:Deal');
        $person = $this->person;

        $my_open_deals = $deal_repository->findDealsForPerson($person);
        $my_total_opendeals = $deal_repository->countDealsForPerson($person);

        $my_won_deals = $deal_repository->findDealsForPerson($person, 1);
        $my_total_wondeals = $deal_repository->countDealsForPerson($person, 1);

        $my_lost_deals = $deal_repository->findDealsForPerson($person, 2);
        $my_total_lostdeals = $deal_repository->countDealsForPerson($person, 2);

        $other_open_deals = $deal_repository->findDealsForOther($person);
        $other_total_opendeals = $deal_repository->countDealsForOther($person);

        $other_won_deals = $deal_repository->findDealsForOther($person, 1);
        $other_total_wondeals = $deal_repository->countDealsForOther($person, 1);

        $other_lost_deals = $deal_repository->findDealsForOther($person, 2);
        $other_total_lostdeals = $deal_repository->countDealsForOther($person, 2);


        $section_html = $this->renderView('AgentBundle:Deal:window-section.html.twig',array(
            'myopendeals' => $my_open_deals,
            'my_total_opendeals' => $my_total_opendeals,
            'otheropendeals' => $other_open_deals,
            'other_total_opendeals' => $other_total_opendeals,
            'my_won_deals' => $my_total_wondeals,
            'my_lost_deals' => $my_total_lostdeals,
            'my_close_total_deals' => $my_total_wondeals + $my_total_lostdeals,
            'other_won_deals' => $other_total_wondeals,
            'other_lost_deals' => $other_total_lostdeals,
            'other_close_total_deals' => $other_total_wondeals + $other_total_lostdeals,

        ));

        return $this->createJsonResponse(array(
            'section_html' => $section_html,
        ));
    }


    public function dealListAction($owner_type = null, $deal_status = null, $deal_type_id = null)
    {
        $deal_repository = $this->em->getRepository('DeskPRO:Deal');
        $person = $this->person;
        $order_by = $this->in->getString('order_by');
        $group_by = $this->in->getString('group_by');
        $set_group_option = $this->in->getString('set_group_option');

        $deal_group_info = '';
        $statusArr = array(
                    'open' => 0,
                    'close' => -1,
                    'won' => 1,
                    'lost' => 2
                    );

        if($owner_type == 'my')
        {
            $deals = $deal_repository->filterDealsForPerson($person, $statusArr[$deal_status], $deal_type_id, $order_by);

            if($group_by){
                $deal_group_info = $deal_repository->groupByDealsForPerson($person, $statusArr[$deal_status], $deal_type_id, $group_by);
                if($set_group_option)
                {
                    $deals = $deal_repository->filterGroupByDealsForPerson($person, $statusArr[$deal_status], $deal_type_id, $group_by, $set_group_option);
                }
            }

        } else if($owner_type == 'other'){

            $deals = $deal_repository->filterDealsForOther($person, $statusArr[$deal_status], $deal_type_id, $order_by);

            if($group_by){

                $deal_group_info = $deal_repository->groupByDealsForOther($person, $statusArr[$deal_status], $deal_type_id, $group_by);
                if($deal_group_info){
                    $deals = $deal_repository->filterGroupByDealsForOther($person, $statusArr[$deal_status], $deal_type_id, $group_by, $set_group_option);
                }
            }

        }
        $filter['id'] = 0; //To Do (Will be dynamic later)
        $pref_display_fields = $this->person->getPref('agent.ui.deal-filter-display-fields.' . $filter['id']);

        $tpl = 'AgentBundle:Deal:deal-list.html.twig';
        return $this->render($tpl, array(
            'deals' => $deals,
            'owner_type' => $owner_type,
            'deal_status' => $deal_status,
            'deal_type_id' => $deal_type_id,
            'order_by' => $order_by,
            'group_by' => $group_by,
            'deal_group_info' => $deal_group_info,
            'group_total' => $this->_getTotalGroupCount($deal_group_info),
            'set_group_option' => $set_group_option,
            'display_fields' => $pref_display_fields
        ));
    }

    protected function _getTotalGroupCount($deal_group_info)
    {
        $total = 0;
        if(\is_array($deal_group_info)){
            foreach($deal_group_info as $group){
                $total += $group[1];
            }
        }
        return $total;
    }

    public function viewAction($deal_id = null)
    {
        if($deal_id)
        {
            $deal = $this->getDealOr404($deal_id);
        } else{
            $deal = new Deal();
        }

        $notes = $this->em->getRepository('DeskPRO:DealNote')->getNotesForDeal($deal);
        $agents = $this->em->getRepository('DeskPRO:Person')->getAgents();
        $deal_type = $this->em->getRepository('DeskPRO:DealType')->findAll();
        $deal_stage = $this->em->getRepository('DeskPRO:DealStage')->getDealStagesByDealType($deal->getDealType()->getId());

        $deal_attachments = $this->em->getRepository('DeskPRO:DealAttachment')->findByDeal($deal);
        $assoceated_tasks = $this->em->getRepository('DeskPRO:TaskAssociatedDeal')->findByDeal($deal);
        $deal_currency  = $this->em->getRepository('DeskPRO:Currency')->findAll();

        $related_finder = new RelatedContentFinder($this->person, $deal);
        $related_content = $related_finder->getRelatedEntities();
//        if(!empty($related_content)){
//        print \Doctrine\Common\Util\Debug::dump($related_content);exit;}

        $field_manager = $this->container->getSystemService('deal_fields_manager');
        $custom_fields = $field_manager->getDisplayArrayForObject($deal);

        $participant_person_ids = array();
        $participant_org_ids = array();

        foreach($deal->getPeoples() as $person)
        {
            $participant_person_ids[] = $person->id;
        }

        foreach($deal->getOrganizations() as $organization)
        {
            $participant_org_ids[] = $organization->id;
        }

        $tpl = 'AgentBundle:Deal:deal-view.html.twig';
        return $this->render($tpl, array(
            'deal' => $deal,
            'notes' => $notes,
            'agents' => $agents,
            'deal_types' => $deal_type,
            'deal_stage' => $deal_stage,
            'participant_person_ids' => $participant_person_ids,
            'participant_org_ids' => $participant_org_ids,
            'person' => $this->person,
            'deal_attachments' => $deal_attachments,
            'assoceated_tasks' => $assoceated_tasks,
            'deal_currencys' => $deal_currency,
            'related_content' => $related_content,
            'custom_fields' => $custom_fields,

        ));
    }

    // TODO error checking
	public function ajaxSaveNoteAction($deal_id)
	{
		if($deal_id)
                {
                    $deal = $this->getDealOr404($deal_id);
                } else{
                    $deal = new Deal();
                }

		$note_txt = $this->in->getString('note');

		$em = $this->em;

		$note = new DealNote();
		$note['agent'] = $this->person;
		$note['deal'] = $deal;
		$note['note'] = $note_txt;
		$em->persist($note);

		$em->flush();

		return $this->createJsonResponse(array(
			'success' => true,
			'deal_id' => $deal['id'],
			'note_li_html' => $this->renderView('AgentBundle:Person:note-li.html.twig', array('note' => $note))
		));
	}

        ############################################################################
	# ajax-save-labels
	############################################################################

	public function ajaxSaveLabelsAction($deal_id)
	{
		if($deal_id)
                {
                    $deal = $this->getDealOr404($deal_id);
                } else{
                    $deal = new Deal();
                }

		$labels = $this->in->getCleanValueArray('labels', 'string', 'discard');

		$deal->getLabelManager()->setLabelsArray($labels);

		$this->em->persist($deal);
		$this->em->flush();

		return $this->createJsonResponse(array('success' => 1));
	}


        public function ajaxSaveCustomFieldsAction($deal_id)
	{
		$deal = $this->getDealOr404($deal_id);

		$this->em->beginTransaction();

		try {
			$field_manager = $this->container->getSystemService('deal_fields_manager');
			$post_custom_fields = $this->request->request->get('custom_fields', array());
			if (!empty($post_custom_fields)) {
				$field_manager->saveFormToObject($post_custom_fields, $deal);
			}

			$this->em->flush();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}

		$custom_fields = $field_manager->getDisplayArrayForObject($deal);

		return $this->render('AgentBundle:Person:view-customfields-rendered-rows.html.twig', array(
			'deal' => $deal,
			'custom_fields' => $custom_fields,
		));
	}


        public function setAgentParticipantsAction($deal_id, $agent_id)
	{
		$deal = $this->getDealOr404($deal_id);
                $agent_id = ($agent_id == 0) ? null : $agent_id;

		$this->db->beginTransaction();

		try {
			$deal->setAsignedAgentId($agent_id);
			$this->em->persist($deal);
			$this->em->flush();
			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		return $this->createJsonResponse(array('sucess' => true));
	}

        public function ajaxSaveAction($deal_id) {

            $deal = $this->getDealOr404($deal_id);
            $this->em->beginTransaction();
            $data = array(
                'success' => true
            );
            switch ($this->in->getString('action')) {
                case 'remove-person':
                    $person = $this->em->find('DeskPRO:Person', $this->in->getUint('person_id'));
                    if ($person) {
                        $deal->deletePeople($person);
                        $data['remove_person_id'] = $person['id'];
                    }
                    break;
                case 'remove-organization':
                    $organization = $this->em->find('DeskPRO:Organization', $this->in->getUint('organization_id'));
                    if ($organization) {
                        $deal->deleteOrganization($organization);
                        $data['remove_organization_id'] = $organization['id'];
                    }
                    break;
                case 'change-dealtype':

                    $deal_type = $this->em->find('DeskPRO:DealType', $this->in->getUint('deal_type_id'));
                    if($deal_id){
                        $deal->setDealTypeId($this->in->getUint('deal_type_id'));
                        $deal->setDealStageId(null);
                    }

                    $data['change_deal_type_id'] = $deal_type['id'];
                    $deal_stage = $this->em->getRepository('DeskPRO:DealStage')->getDealStagesByDealType($deal_type->getId());


                    $tpl = $this->renderView('AgentBundle:Deal:select-deal-options.html.twig', array(
                        'name'=> 'newdeal[deal_stage]',
                        'id' => uniqid().'_select_deal_stage',
                        'with_blank'=> true,
                        'with_blank2'=> true,
                        'blank_title'=> 'Set Deal Stage',
                        'options'=> $deal_stage,
                        'selected'=> '',
                        'add_classname'=> 'select-deal-stage'
                    ));

                    $data['deal_stage'] = $tpl;

                    break;

                case 'change-dealstage':
                    if($deal_id){
                        $deal->setDealStageId($this->in->getUint('deal_stage_id'));
                        $data['change_deal_stage_id'] = $this->in->getUint('deal_stage_id');
                    }
                    break;
                case 'change-status':
                    $deal['status'] = $this->in->getString('status');
                    break;
                case 'change-visibility':
                    $deal['visibility'] = $this->in->getString('visibility');
                    break;
                case 'change_title':
                    $deal['title'] = $this->in->getString('title');
                    break;
                case 'change_probability':
                    if(($this->in->getString('probability') <= 100))
                    $deal['probability'] =  $this->in->getString('probability');
                    break;
                case 'change_deal_value':
                    $deal['deal_value'] = $this->in->getString('deal_value');
                    break;
                case 'change_deal_currency':
                    $deal->setDealCurrencyId($this->in->getString('deal_currency'));
                    break;
                case 'add-related':
                        $updater = new RelatedContentUpdate($deal);
                        $updater->addRelated(
                                $this->in->getString('content_type'),
                                $this->in->getString('content_id')
                        );
                        break;

                case 'remove-related':
                        $updater = new RelatedContentUpdate($deal);
                        $updater->removeRelated(
                                $this->in->getString('content_type'),
                                $this->in->getString('content_id')
                        );
                        break;

            }

            if($deal_id){
                $this->em->persist($deal);
                $this->em->flush();
                $this->em->commit();
            }

            return $this->createJsonResponse($data);
        }

        public function newdealGetPersonRowAction($person_id)
	{
		if (!$person_id && $this->in->getUint('person_id')) {
			$person_id = $this->in->getUint('person_id');
		}

		$person = false;
		if ($person_id) {
			$person = $this->em->find('DeskPRO:Person', $person_id);
		}
		if (!$person && $this->in->getString('email')) {
			$person = $this->em->getRepository('DeskPRO:Person')->findOneByEmail($this->in->getString('email'));
		}

		$session = null;
		if ($this->in->getUint('session_id')) {
			$session = $this->em->find('DeskPRO:Session', $this->in->getUint('session_id'));
		}
		if ($session && $session->person) {
			$person = $session;
		}

		if (!$person) {
			$person = new Person();
			if ($session) {
				$person->name = $session->visitor->name;
				if ($session->visitor->email) {
					$person->setEmail($session->visitor->email);
				}
			}
		}

		return $this->render('AgentBundle:Deal:newdeal-person-row.html.twig', array(
			'person' => $person
		));
	}

        public function newdealGetOrganizationRowAction($org_id)
	{
		$organization = false;
		if ($org_id) {
			$organization = $this->em->find('DeskPRO:Organization', $org_id);
		}

		return $this->render('AgentBundle:Deal:newdeal-organization-row.html.twig', array(
			'organization' => $organization
		));
        }

        public function newdealCreatePersonRowAction($person_id)
        {
                if ($person_id)
                {
			$person = $this->em->find('DeskPRO:Person', $person_id);
		} else{
                    $person = new Person();
                }

                return $this->render('AgentBundle:Deal:create-person-row.html.twig', array(
			'person' => $person
		));
        }

        public function newdealSetPersonRowAction($person_id)
	{
                $deal_repository = $this->em->getRepository('DeskPRO:Deal');
                $deal = $this->getDealOr404($this->in->getString('deal_id'));

                $email = $this->in->getString('email');
                $name = $this->in->getString('name');

                if ($person_id) {
                    $person = $this->em->find('DeskPRO:Person', $person_id);

		} else if($email){
                    $person = $this->em->getRepository('DeskPRO:Person')->findOneByEmail($email);
                }

                if (!$person) {
			$person = new Person();
			$person->addEmailAddressString($email);
		}

                if (!$person->name && $name) {
			$person->name = $name;
		}

                $this->em->persist($person);
                $this->em->flush();

              // Checked if the person already added to the deal.
              if($deal_repository->findPersonInDeal($person, $this->in->getString('deal_id')) <= 0)
              {
                  $deal->addPeoples($person);
                  $this->em->persist($deal);
                  $this->em->flush();

                  return $this->render('AgentBundle:Deal:person-li.html.twig', array(
			'person' => $person
                  ));
              }else{
                  return new Response('failed');
              }
        }

        public function newdealCreateOrganizationRowAction($org_id)
        {
            $organization = false;
            if ($org_id) {
                    $organization = $this->em->find('DeskPRO:Organization', $org_id);
            }

            return $this->render('AgentBundle:Deal:create-organization-row.html.twig', array(
                    'organization' => $organization
            ));
        }

        public function newdealSetOrganizationRowAction($org_id)
	{
		$deal_repository = $this->em->getRepository('DeskPRO:Deal');
                $deal = $this->getDealOr404($this->in->getString('deal_id'));
                $name = $this->in->getString('name');
                $organization = false;

		if ($org_id) {
			$organization = $this->em->find('DeskPRO:Organization', $org_id);
		}else if($name){
                    $organization = $this->em->getRepository('DeskPRO:Organization')->findOneByName($name);

                }

                if(!$organization)
                {
                    $organization = new Organization();
                    $organization->name = $name;
                }

                $this->em->persist($organization);

		// Checked if the person already added to the deal.
              if($deal_repository->findOrganizationInDeal($organization, $this->in->getString('deal_id')) <= 0)
              {
                  $deal->addOrganizations($organization);
                  $this->em->persist($deal);
                  $this->em->flush();

                  return $this->render('AgentBundle:Deal:org-li.html.twig', array(
			'organization' => $organization
                  ));
              }else{
                  return new Response('failed');
              }
        }


        /**
	 * @return Application\DeskPRO\Entity\Deal
	 */
	protected function getDealOr404($deal_id)
	{
		try {
			$deal = $this->em->find('DeskPRO:Deal', $deal_id);
		} catch (\Doctrine\ORM\NoResultException $e) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no deal with ID $deal_id");
		}

		return $deal;
	}

}
