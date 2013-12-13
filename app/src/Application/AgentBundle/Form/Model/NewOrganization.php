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
use Application\DeskPRO\Entity\Organization;

class NewOrganization
{
	public $name;

	public $labels = array();
	public $usergroup_ids = array();
	public $custom_fields = array();

	protected $_org;

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
		$this->custom_fields = isset($form['org_custom_fields']) ? $form['org_custom_fields'] : array();
	}

	public function save()
	{
		$this->_em->beginTransaction();

		$org = new Organization();
		$org->getLabelManager()->setLabelsArray($this->labels);

		$org->name = $this->name;

		foreach ($this->usergroup_ids as $ug_id) {
			$ug = $this->_em->find('DeskPRO:Usergroup', $ug_id);
			if ($ug_id) {
				$org->usergroups->add($ug);
			}
		}

		$this->_em->persist($org);
		$this->_em->flush();

		if ($this->custom_fields) {
			$field_manager = App::getSystemService('org_fields_manager');
			$field_manager->saveFormToObject($this->custom_fields, $org);
		}

		$this->_em->flush();
		$this->_em->commit();

		$this->_org = $org;
	}

	public function getOrganization()
	{
		return $this->_org;
	}
}
