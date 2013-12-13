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

namespace Application\AgentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class NewTicket extends AbstractType
{
	public function buildForm(FormBuilder $builder, array $options)
    {
		#------------------------------
		# User fields
		#------------------------------

		$user_builder = $builder->create('person', 'form', array('data_class' => 'Application\\AgentBundle\\Form\\Model\\NewTicketPerson'));
		$user_builder->add('id', 'hidden');
		$user_builder->add('name', 'text', array('required' => false));
		$user_builder->add('email_address', 'text', array('required' => false));
		$user_builder->add('organization', 'text', array('required' => false));
		$user_builder->add('organization_position', 'text', array('required' => false));
		$user_builder->add('language_id', 'text', array('required' => false));

		$builder->add($user_builder);

		#------------------------------
		# Ticket fields
		#------------------------------

		$builder->add('subject', 'text');
		$builder->add('notify_template', 'hidden');
		$builder->add('message', 'textarea');
	    $builder->add('is_html_reply', 'hidden');

		$builder->add('department_id', 'text');
		$builder->add('status', 'text');
		$builder->add('agent_id', 'text', array('required' => false));
		$builder->add('agent_team_id', 'text', array('required' => false));

		$builder->add('category_id', 'text', array('required' => false));
		$builder->add('priority_id', 'text', array('required' => false));
		$builder->add('workflow_id', 'text', array('required' => false));
		$builder->add('product_id', 'text', array('required' => false));

		$builder->add('billing_type', 'hidden', array('required' => false));
		$builder->add('billing_amount', 'hidden', array('required' => false));
		$builder->add('billing_hours', 'hidden', array('required' => false));
		$builder->add('billing_minutes', 'hidden', array('required' => false));
		$builder->add('billing_seconds', 'hidden', array('required' => false));
		$builder->add('billing_comment', 'hidden', array('required' => false));

		$builder->add('add_cc_person', 'collection', array(
			'type' => 'hidden',
			'required' => false,
			'allow_add' => true,
			'allow_delete' => true
		));

		$builder->add('add_cc_newperson', 'collection', array(
			'type' => 'hidden',
			'required' => false,
			'allow_add' => true,
			'allow_delete' => true
		));

		$builder->add('attach', 'collection', array(
			'type' => 'hidden',
			'required' => false,
			'allow_add' => true,
			'allow_delete' => true
		));
    }

	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class' => 'Application\\AgentBundle\\Form\\Model\\NewTicket',
		);
	}

    public function getName()
    {
        return 'newticket';
    }
}
