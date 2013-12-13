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

use Application\DeskPRO\Entity\CustomDefPerson;

/**
 * Handles manaing person fields
 */
class CustomDefTicketsController extends CustomDefAbstractController
{
	const API_NAME = 'custom_fields.tickets';

	public function _getEditFieldData($field)
	{
		if ($field->getId()) {
			return array();
		}

		$vars = array();

		$custom_form_dep_ids = $this->db->fetchAllCol("
			SELECT COALESCE(department_id, 0)
			FROM ticket_page_display
		");

		$custom_form_deps = array();
		foreach ($custom_form_dep_ids as $dep_id) {
			if (!$dep_id) {
				$vars['custom_form_deps_default'] = true;
			} else {
				$custom_form_deps[$dep_id] = App::getDataService('Department')->get($dep_id);
			}
		}

		$vars['custom_form_deps'] = $custom_form_deps;

		return $vars;
	}

	public function _postEditSave($field, $is_new)
	{
		if (!$is_new) {
			return;
		}

		$add_to_layouts = $this->in->getCleanValueArray('add_to_layouts', 'uint', 'discard');
		if (!$add_to_layouts) {
			return;
		}

		if (in_array('0', $add_to_layouts)) {
			$layouts = $this->em->createQuery("
				SELECT d
				FROM DeskPRO:TicketPageDisplay d
				WHERE d.department IN (?0) OR d.department IS NULL
			")->setParameter(0, $add_to_layouts)->execute();
		} else {
			$layouts = $this->em->createQuery("
				SELECT d
				FROM DeskPRO:TicketPageDisplay d
				WHERE d.department IN (?0)
			")->setParameter(0, $add_to_layouts)->execute();
		}

		foreach ($layouts as $layout) {
			$data = $layout->data;
			$data[] = array(
				'id'         => 'ticket_field['.$field->id.']',
				'field_type' => 'ticket_field',
				'field_id'   => $field->id
			);

			$layout->data = $data;
			$this->em->persist($layout);
		}

		$this->em->flush();
	}

	protected function getListingRoute()
	{
		return 'admin_tickets_fields';
	}
}
