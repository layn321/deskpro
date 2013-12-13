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
 * @category Entities
 */

namespace Application\DeskPRO\EntityRepository;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\ChatPageDisplay as ChatPageDisplayEntity;

class ChatPageDisplay extends AbstractEntityRepository
{
	public function getFromZone($zone, $department_context = null)
	{
		if ($department_context) {
			if (is_object($department_context)) {
				$department_context = $department_context['id'];
			}

			return $this->getEntityManager()->createQuery("
				SELECT d
				FROM DeskPRO:ChatPageDisplay d
				WHERE d.zone = :zone AND d.department = :department
			")->setParameters(array('zone' => $zone, 'department' => $department_context))->execute();
		} else {
			return $this->getEntityManager()->createQuery("
				SELECT d
				FROM DeskPRO:ChatPageDisplay d
				WHERE d.zone = :zone
			")->setParameters(array('zone' => $zone))->execute();
		}
	}

	public function getSection($department, $zone, $section)
	{
		try {
			$d = $this->findOneBy(array('department' => $department ? $department['id'] : null, 'zone' => $zone, 'section' => $section));
			return $d;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Just like getSectionData except it resolves to the best default match when a custom layout doesnt exist.
	 *
	 * @param $department
	 * @param $zone
	 * @param string $section
	 */
	public function getSectionDataResolve($department, $zone, $section = 'default', &$is_resolved = null)
	{
		$page_data = App::getEntityRepository('DeskPRO:ChatPageDisplay')->getSectionData($department, $zone, $section);

		if ($page_data === null) {
			$is_resolved = true;
			if ($zone == 'create') {
				$page_data = App::getEntityRepository('DeskPRO:ChatPageDisplay')->getSectionData(null, $zone, $section);
			} else {
				// Default for view/modify is the create form from the same department,
				// or the default form from the default, or the default create if even that doesnt exist
				if ($department) {
					$page_data = App::getEntityRepository('DeskPRO:ChatPageDisplay')->getSectionData($department, 'create', $section);
				}
			}
		}

		// If we get here with still nothing,
		// then we're auto-generating a 'create' form for everything there is in the system (oh, lawdy!)
		if ($page_data === null) {
			$is_resolved = true;
			$page_data = $this->generateFullSectionData($zone);
		}

		return $page_data;
	}


	/**
	 * Generates a page data array that has all enable-able components on it.
	 *
	 * @return array
	 */
	public function generateFullSectionData($zone = 'create')
	{
		$page_data = array();

		if ($zone == 'create') {
			$page_data[] = array(
				'id' => 'person_name',
				'field_type' => 'person_name'
			);
			$page_data[] = array(
				'id' => 'person_email',
				'field_type' => 'person_email'
			);
		}

		$page_data[] = array(
			'id' => 'chat_department',
			'field_type' => 'chat_department'
		);

		// Custom fields
		$fields = App::getSystemService('chat_fields_manager')->getFields();
		foreach ($fields as $f) {
			$page_data[] = array(
				'id' => 'chat_field['.$f->getId().']',
				'field_type' => 'chat_field',
				'field_id' => $f->getId()
			);
		}

		return $page_data;
	}


	public function getSectionData($department, $zone, $section = 'default')
	{
		if ($department === null) {
			$data = App::getDb()->fetchColumn("
				SELECT data
				FROM chat_page_display
				WHERE department_id IS NULL AND zone = ? AND section = ?
			", array($zone, $section));
		} else {
			if (is_array($department) || is_object($department)) {
				$department = $department['id'];
			}
			$data = App::getDb()->fetchColumn("
				SELECT data
				FROM chat_page_display
				WHERE department_id = ? AND zone = ? AND section = ?
			", array($department, $zone, $section));
		}

		if (!$data) {
			return null;
		}

		if ($data) {
			$data = unserialize($data);
		}

		if (!$data) {
			return array();
		}

		return $data;
	}

	public function getOrCreate($department, $zone, $section)
	{
		$d = null;
		try {
			$d = $this->findOneBy(array('department' => $department ? $department['id'] : null, 'zone' => $zone, 'section' => $section));
		} catch (\Exception $e) {}

		if (!$d) {
			$d = new ChatPageDisplayEntity();
			$d->department = $department;
			$d['zone'] = $zone;
			$d['section'] = $section;
		}

		return $d;
	}
}
