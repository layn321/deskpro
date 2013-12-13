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

namespace Application\AdminBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Application\AdminBundle\Form\EditTicketPriorityType;

/**
 * Management of widgets
 */
class TicketWidgetsController extends AbstractController
{
	############################################################################
	# list
	############################################################################

	/**
	 * Shows the main listing of priorities
	 */
	public function listAction()
	{
		$all_widgets = $this->em->createQuery("
			SELECT w
			FROM DeskPRO:Widget w
			WHERE w.section LIKE ?1 OR w.section LIKE ?2
		")->execute(array(1=> 'agent.ticket.%', 2=> 'ticket.%'));

		return $this->render('AdminBundle:TicketWidgets:list.html.twig', array(
			'all_widgets' => $all_widgets
		));
	}



	############################################################################
	# new-choose-type
	############################################################################

	public function newChooseTypeAction()
	{
		return $this->render('AdminBundle:TicketWidgets:edit-choosetype.html.twig', array(

		));
	}



	############################################################################
	# edit
	############################################################################

	/**
	 * Edit a priority
	 */
	public function editAction($widget_id)
	{
		if (!$widget_id) {
			$widget = new Entity\Widget();
		} else {
			$widget = $this->em->getRepository('DeskPRO:Widget')->find($widget_id);
		}

		$form = \Symfony\Component\Form\Form::create($this->get('form.context'), 'widget');

		$form->add(new \Symfony\Component\Form\HiddenField('name_id'));
		$form->add(new \Symfony\Component\Form\HiddenField('section'));
		$form->add(new \Symfony\Component\Form\HiddenField('template_name'));
		$form->add(new \Symfony\Component\Form\TextField('note'));

		$form_data = new \Symfony\Component\Form\Form('data');
		$form_data->add(new \Symfony\Component\Form\TextareaField('content'));

		$form->add($form_data);
		$form->bind($this->get('request'), $widget);

		$is_edited = false;
		$row_html = false;
		if ($this->in->getBool('process')) {
			$is_edited = true;
			$this->em->persist($widget);
			$this->em->flush();

			$row_html = $this->renderView('AdminBundle:TicketWidgets:list-row.html.twig', array('widget' => $widget));
		}

		return $this->render('AdminBundle:TicketWidgets:edit-content.html.twig', array(
			'widget'  => $widget,
			'form'      => $form,
			'is_edited' => $is_edited,
			'row_html'  => $row_html
		));
	}
}
