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
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\PersonEmail;
use Application\DeskPRO\Entity\Organization;

class NewPerson
{
	public $name;
	public $email;

	public $organization_id;
	public $organization_position;

	public $new_organization;

	public $labels = array();
	public $usergroup_ids = array();
	public $custom_fields = array();

	public $timezone;

	protected $_person;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $_em;

	public function __construct(Person $person_context)
	{
		$this->_person_context = $person_context;

		$this->_em = App::getOrm();
	}

	public function setCustomFieldForm(array $form)
	{
		$this->custom_fields = isset($form['newperson']['custom_fields']) ? $form['newperson']['custom_fields'] : array();
	}

	public function save()
	{
		$this->_em->beginTransaction();

		$person = new Person();
		$person->getLabelManager()->setLabelsArray($this->labels);

		if ($this->name) {
			$person->name = $this->name;
		}

		if ($this->email) {
			$person->setEmail($this->email, true);
		}

		if ($this->timezone) {
			$person->timezone = $this->timezone;
		}

		if ($this->organization_id) {
			$org = $this->_em->find('DeskPRO:Organization', $this->organization_id);
			if ($org) {
				$person->organization = $org;
				$person->organization_position = $this->organization_position;
			}
		} elseif ($this->new_organization) {
			$org = new Organization();
			$org->name = $this->new_organization;
			$this->_em->persist($org);

			$person->organization = $org;
			$person->organization_position = $this->organization_position;

		}

		foreach ($this->usergroup_ids as $ug_id) {
			$ug = $this->_em->find('DeskPRO:Usergroup', $ug_id);
			if ($ug_id) {
				$person->usergroups->add($ug);
			}
		}

		$person->creation_system = Person::CREATED_WEB_AGENT;

		$this->_em->persist($person);
		$this->_em->flush();

		if ($this->custom_fields) {
			$user_field_defs = App::getApi('custom_fields.people')->getEnabledFields();
			foreach ($user_field_defs as $field_def) {
				foreach ($field_def->getHandler()->getDataFromForm($this->custom_fields) as $info) {
					$d = $person->setCustomData($info[0], $info[1], $info[2]);
					//$this->_em->persist($d);
				}
			}
		}

		$this->_em->flush();
		$this->_em->commit();

		$this->_person = $person;
	}

	public function getPerson()
	{
		return $this->_person;
	}
}
