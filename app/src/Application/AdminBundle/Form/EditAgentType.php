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
 * @subpackage AdminBundle
 */

namespace Application\AdminBundle\Form;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Orb\Util\Arrays;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use Application\AdminBundle\Form\CustomField\Type\PasswordValueType;

class EditAgentType extends AbstractType
{
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder->add('name', 'text');
		$builder->add('password', new PasswordValueType(), array('required' => false, 'always_empty' => false));
		$builder->add('email', 'text');

		$zones = array('access_agent' => 'Agent', 'access_admin' => 'Admin', 'access_reports' => 'Reports', 'access_billing' => 'Billing');
		$builder->add('access_zones', 'choice', array(
			'choices' => $zones,
			'expanded' => true,
			'multiple' => true
		));

		$usergroup_names = App::getEntityRepository('DeskPRO:Usergroup')->getAgentUsergroupNames();
		if ($usergroup_names) {
			$builder->add('usergroups', 'choice', array(
				'choices' => $usergroup_names,
				'expanded' => true,
				'multiple' => true
			));
		}

		$team_names = App::getEntityRepository('DeskPRO:AgentTeam')->getTeamNames();
		$builder->add('agent_teams', 'choice', array(
			'choices' => $team_names,
			'expanded' => true,
			'multiple' => true
		));

		$department_names = App::getEntityRepository('DeskPRO:Department')->getFullNames();
		$builder->add('allowed_departments', 'choice', array(
			'choices' => $department_names,
			'expanded' => true,
			'multiple' => true
		));
	}

	public function getName()
	{
		return 'agent';
	}
}
