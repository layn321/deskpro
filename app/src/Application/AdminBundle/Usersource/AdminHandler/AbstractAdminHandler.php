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
 * @category Usersources
 */

namespace Application\AdminBundle\Usersource\AdminHandler;

use Application\DeskPRO\Entity\Usersource;

use Application\DeskPRO\App;

use Orb\Util\Strings;
use Orb\Util\Arrays;

/**
 * The setup classes handle showing the user a wizard, and then taking input and
 * transforming it (if necessary) into adapter options.
 */
abstract class AbstractAdminHandler
{
	protected $usersource;

	/**
	 * Entity manager
	 * @var Doctrine\ORM\EntityManager
	 */
	protected $em;

	public function __construct(Usersource $usersource)
	{
		$this->usersource = $usersource;
		$this->em = App::getOrm();

		$this->init();
	}



	/**
	 * Empty hook method
	 */
	protected function init() {}



	/**
	 * Get an array of additional fields to add to the Form object in the controller.
	 * This must always be called 'type_form'.
	 *
	 * @return \Orb\Form\Field\FieldGroup
	 */
	public function buildFormGroup()
	{
		$formgroup = new \Orb\Form\Field\FieldGroup(array('name' => 'type_form'));

		foreach ($this->buildFormFields() as $f) {
			$formgroup->addField($f);
		}

		$this->setFormDataFromUsersource($formgroup);

		return $formgroup;
	}



	/**
	 * Return an array of fields we need to add to the form.
	 *
	 * @return array
	 */
	abstract protected function buildFormFields();



	/**
	 * Set data/options based on the current field defition.
	 *
	 * @param \Orb\Form\Field\FieldGroup $formgroup
	 */
	public function setFormDataFromUsersource(\Orb\Form\Field\FieldGroup $formgroup)
	{
		$formgroup->setData($this->usersource['options']);
	}



	/**
	 * This renders the HTML for a particular field types options. Standard options
	 * always exist, like a title, but the rest is per-custom field type.
	 *
	 * @return string
	 */
	public function renderFormPartial($controller, $form)
	{
		$classname = get_class($this);
		$parts = explode('\\', $classname);
		$basename = strtolower(array_pop($parts));

		$tplname = 'AgentBundle:Usersources:edit-form-.html.twig' . $basename;

		return $controller->renderView($tplname, array(
			'usersource' => $this->usersource,
			'form' => $form['type_form'],
			'full_form' => $form
		));
	}



	/**
	 * Save the usersource
	 *
	 * @param \Orb\Form\Field\Form $form
	 */
	public function saveUsersource(\Orb\Form\Field\Form $form)
	{
		$this->em->beginTransaction();

		$is_new = ((bool)$this->usersource['id']);

		$this->usersource['title'] = $form['basic_properties']['title']->getData();

		$this->handleSave($form['type_form']);

		$this->em->persist($this->usersource);

		#------------------------------
		# Save
		#------------------------------

		$this->em->flush();
		$this->em->commit();
	}



	/**
	 * Save options
	 *
	 * @param Orb\Form\Field\FieldGroup $formgroup This is the form fragment for this type
	 */
	protected function handleSave(\Orb\Form\Field\FieldGroup $formgroup)
	{
		$this->usersource['options'] = $formgroup->getData();
	}
}
