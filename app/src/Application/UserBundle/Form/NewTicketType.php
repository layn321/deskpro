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
 * The new ticket form
 */
class NewTicketType extends AbstractType
{
	const MODE_NORMAL = 'normal';
	const MODE_WIDGE = 'widget';

	/**
	 * The actual person (logged in)
	 */
	protected $person;

	/**
	 * A person object we'll use for things like permissions.
	 * So if the person is a guest, then this is a guest object
	 * with basic properties.
	 */
	protected $mock_person;

	protected $ticket_options;
	protected $ticket_fields = array();

	protected $mode;

	public function __construct($person, $mode = self::MODE_NORMAL)
	{
		$this->person = $person;
		$this->mode = $mode;
	}

	public function buildForm(FormBuilder $builder, array $options)
	{
		$this->buildPersonForm($builder);
		$this->buildTicketForm($builder);
	}



	/**
	 * Configures the person form
	 */
	protected function buildPersonForm(FormBuilder $builder)
	{
		if ($this->person AND $this->person['id']) {
			$this->mock_person = $this->person;
		} else {
			$this->person = null;

			// We need this for some things to get basic permissions
			$this->mock_person = Entity\Person::newContactPerson();
		}

		$person_builder = $builder->create('person', 'form')
			->add('name', 'text', array('data' => $this->mock_person['name']));

		$person_builder->add('email', 'text', array('data' => $this->mock_person['primary_email_address']));

		$builder->add($person_builder);
	}


	/**
	 * Configures the ticket form
	 */
	protected function buildTicketForm(FormBuilder $builder)
	{
		$ticket_builder = $builder->create('ticket', 'form');

		#------------------------------
		# Standard fields
		#------------------------------

		$ticket_options = App::getApi('tickets')->getTicketOptions($this->mock_person);
		$this->ticket_options = $ticket_options;

		$this->ticket_options = $ticket_options;

		$ticket_builder->add('subject', 'text');
		$ticket_builder->add('message', 'textarea');

		if ($deps = App::getDataService('Department')->getPersonDepartments(App::getCurrentPerson(), 'tickets')) {
			$ticket_builder->add('department_id', 'choice', array(
				'choices' => Arrays::selectArrayFromHierarchy($deps, 'id', 'title'),
				'required' => false
			));
		}

		$ticket_builder->add('category_id', 'hidden', array('required' => false));
		$ticket_builder->add('product_id', 'hidden', array('required' => false));
		$ticket_builder->add('priority_id', 'hidden', array('required' => false));
		$ticket_builder->add('cc_emails', 'text', array('required' => false));

		#------------------------------
		# Custom fields
		#------------------------------

		if ($this->mode == self::MODE_NORMAL) {
			$ticket_field_defs = App::getApi('custom_fields.tickets')->getEnabledFields();
			$custom_fields = App::getApi('custom_fields.tickets')->getFieldsDisplayArray($ticket_field_defs, array());
			$this->ticket_fields = $custom_fields;

			$builder->add($ticket_builder);
		}
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
