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


namespace Application\AgentBundle\Form\Model;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Deal;
use Application\DeskPRO\Entity\DealNote;
use Application\DeskPRO\Entity\DealAttachment;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\Organization;


class NewDeal
{
    public $person;
    public $title;
    public $agent_id;    
    public $deal_type;
    public $deal_stage;
    public $organizations;
    public $deal_currency;
    public $probability;
    public $deal_value;
    public $visibility;

    public $attach = array();

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $_em;
    protected $_person_context;
    protected $_deal;

    public function __construct(Person $person_context)
    {
            $this->person = new NewDealPerson();
            $this->organizations = new NewDealOrganization();
            $this->_person_context = $person_context;
    }

    public function save()
    {

        $em = App::getOrm();
		//$em->beginTransaction();

		#------------------------------
		# The user owner
		#------------------------------
 
		if ($this->person->id) {
                    
			$person = $em->find('DeskPRO:Person', $this->person->id);
		} else {
			$person = $em->getRepository('DeskPRO:Person')->findOneByEmail($this->person->email_address);
		}

		if (!$person) {
			$person = new Person();
			$person->addEmailAddressString($this->person->email_address);
		}

                if (!$person->name && $this->person->name) {
			$person->name = $this->person->name;
		}

		$em->persist($person);
                //$em->flush();

                if($this->organizations->id)
                {
                    $org = $em->find('DeskPRO:Organization',$this->organizations->id);
                }else if($this->organizations->name){
                
                    $org = $em->getRepository('DeskPRO:Organization')->findOneByName($this->organizations->name);
                }
                if (!$org) {
                        $org = new Organization();
                        $org['name'] = $this->organizations->name;
                }

                $em->persist($org);
                //$em->flush();
                
                
                #------------------------------
		# Deal
		#------------------------------

                $deal = new Deal();
                
                $deal->setDealTypeId($this->deal_type);
                $deal->setDealStageId($this->deal_stage);
                $deal->setPersonId($this->_person_context->id);
                $deal->setAsignedAgentId($this->agent_id);
                $deal['title'] = $this->title;
                $deal->setDealCurrencyId($this->deal_currency);
                $deal['probability'] = $this->probability;
                $deal['deal_value'] = $this->deal_value;
                $deal['visibility'] = $this->visibility;
                $deal->addOrganizations($org);
                $deal->addPeoples($person);

                $em->persist($deal);
                

                // Deal Attachments
		foreach ($this->attach as $blob_id) {

			$blob = App::getOrm()->getRepository('DeskPRO:Blob')->find($blob_id);

			$attach = new DealAttachment();
			$attach['blob'] = $blob;
			$attach['person'] = $this->_person_context;
                        $attach['deal'] = $deal;

                        $em->persist($attach);
		}
                
                $em->flush();
                $this->_deal = $deal;
    }

    public function getDeal()
    {
        return $this->_deal;
    }
}
