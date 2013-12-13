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
 * @subpackage UserBundle
 */

namespace Application\UserBundle\Form;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Orb\Util\Arrays;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * The edit ticket form
 */
class EditTicketType extends AbstractType
{
	protected $person;
	protected $ticket_options;
	protected $ticket_fields = array();

	public function __construct($person)
	{
		$this->person = $person;
	}

	public function buildForm(FormBuilder $builder, array $options)
	{
		$ticket_builder = $builder->create('ticket', 'form');

		#------------------------------
		# Standard fields
		#------------------------------

		$ticket_options = App::getApi('tickets')->getTicketOptions(App::getCurrentPerson());

		$this->ticket_options = $ticket_options;

		$ticket_builder->add('subject', 'text');

		if ($deps = App::getDataService('Department')->getPersonDepartments(App::getCurrentPerson(), 'tickets')) {
			$ticket_builder->add('department_id', 'choice', array(
				'choices' => Arrays::selectArrayFromHierarchy($deps, 'id', 'title'),
				'required' => false
			));
		}

		if (!empty($ticket_options['ticket_categories_hierarchy'])) {
			$ticket_builder->add('category_id', 'choice', array(
				'choices' => Arrays::selectArrayFromHierarchy($ticket_options['ticket_categories_hierarchy'], 'id', 'title'),
				'required' => false
			));
		}

		if (!empty($ticket_options['priorities'])) {
			$ticket_builder->add('priority_id', 'choice', array(
				'choices' => Arrays::unshiftAssocReturn($ticket_options['priorities'], '', ''),
				'required' => false
			));
		}

		if (!empty($ticket_options['products'])) {
			$ticket_builder->add('product_id', 'choice', array(
				'choices' => Arrays::unshiftAssocReturn($ticket_options['products'], '', ''),
				'required' => false
			));
		}

		$ticket_builder->add('cc_emails', 'text', array('required' => false));
		$ticket_builder->add('remove_ccs', 'collection', array(
			'type' => 'hidden',
			'required' => false,
			'allow_add' => true,
			'allow_delete' => true
		));

		$builder->add($ticket_builder);

		#------------------------------
		# Custom fields
		#------------------------------

		$ticket_field_defs = App::getApi('custom_fields.tickets')->getEnabledFields();

		$ticket_fields_builder = $ticket_builder->create('custom_ticket_fields', 'form');

		$custom_fields = App::getApi('custom_fields.tickets')->getFieldsDisplayArray($ticket_field_defs, array(), $ticket_fields_builder);
		$this->ticket_fields = $custom_fields;

		$builder->add($ticket_fields_builder);
	}

	public function getTicketOptions()
	{
		return $this->ticket_options;
	}

	public function getTicketFields()
	{
		return $this->ticket_fields;
	}

	public function getName()
	{
		return 'newticket';
	}
}
