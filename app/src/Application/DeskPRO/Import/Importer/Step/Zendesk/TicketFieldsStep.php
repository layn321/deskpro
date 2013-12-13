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
 * @subpackage Import
 */

namespace Application\DeskPRO\Import\Importer\Step\Zendesk;

use Application\DeskPRO\Entity\CustomDefTicket;
use Orb\Util\Arrays;
use Orb\Util\Strings;

class TicketFieldsStep extends AbstractZendeskStep
{
	public $on_rerun = false;

	public static function getTitle()
	{
		return 'Import Ticket Fields';
	}

	public function run($page = 1)
	{
		$sub_start_time = microtime(true);

		#------------------------------
		# Priority
		#------------------------------

		$this->db->exec("DELETE FROM ticket_priorities");

		$pris = array();
		$pris[] = array('id' => 1, 'title' => 'Low',    'priority' => 0);
		$pris[] = array('id' => 2, 'title' => 'Normal', 'priority' => 10);
		$pris[] = array('id' => 3, 'title' => 'High',   'priority' => 20);
		$pris[] = array('id' => 4, 'title' => 'Urgent', 'priority' => 30);

		$this->db->batchInsert('ticket_priorities', $pris);

		#------------------------------
		# Custom Fields
		#------------------------------

		$fields = $this->zd->sendGetAll('ticket_fields', 'ticket_fields', array('per_page' => 100));

		// There are built-in types that we need to skip importing
		$skip_types = array(
			'subject'     => true,
			'description' => true,
			'status'      => true,
			'tickettype'  => true,
			'priority'    => true,
			'group'       => true,
			'assignee'    => true
		);

		$this->db->beginTransaction();
		try {
			foreach ($fields as $f) {
				if (isset($skip_types[$f['type']])) {
					continue;
				}

				$this->processField($f);
			}
			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		$sub_end_time = microtime(true);
		$this->logMessage(sprintf("-- Done. Took %.3f seconds.", $sub_end_time-$sub_start_time));
	}

	public function processField($field_info)
	{
		$field_id = $field_info['id'];

		$new_field = new CustomDefTicket();
		$new_field->display_order = $field_info['position'];
		$new_field->title         = $field_info['title'];
		$new_field->description   = $field_info['description'];

		if (!$field_info['active']) {
			$new_field->is_enabled = false;
		}

		switch ($field_info['type']) {
			case 'text':
			case 'regexp':
			case 'decimal':
			case 'integer':
				$new_field->handler_class = 'Application\\DeskPRO\\CustomFields\\Handler\\Text';

				if ($field_info['type'] == 'regexp') {
					$new_field->setOption('regex', Strings::getInputRegexPattern($field_info['regexp_for_validation']));
				} elseif ($field_info['type'] == 'integer') {
					$new_field->setOption('regex', '/^[0-9]+$/');
				} elseif ($field_info['type'] == 'decimal') {
					$new_field->setOption('regex', '/^([0-9]+)\.([0-9]+)$/');
				}

				if ($field_info['required']) {
					$new_field->setOption('agent_required', true);
					$new_field->setOption('agent_min_length', 1);
				}
				if ($field_info['required_in_portal']) {
					$new_field->setOption('required', true);
					$new_field->setOption('min_length', 1);
				}

				break;

			case 'textarea':
				$new_field->handler_class = 'Application\\DeskPRO\\CustomFields\\Handler\\Textarea';
				if ($field_info['required']) {
					$new_field->setOption('agent_required', true);
					$new_field->setOption('agent_min_length', 1);
				}
				if ($field_info['required_in_portal']) {
					$new_field->setOption('required', true);
					$new_field->setOption('min_length', 1);
				}
				break;

			case 'checkbox':
				$new_field->handler_class = 'Application\\DeskPRO\\CustomFields\\Handler\\Toggle';
				break;

			case 'tagger':
				$new_field->handler_class = 'Application\\DeskPRO\\CustomFields\\Handler\\Choice';
				if ($field_info['required']) {
					$new_field->setOption('agent_required', true);
					$new_field->setOption('agent_min_length', 1);
				}
				if ($field_info['required_in_portal']) {
					$new_field->setOption('required', true);
					$new_field->setOption('min_length', 1);
				}
				break;
		}

		$new_field->setOption('zd_type', $field_info['type']);
		$this->getEm()->persist($new_field);
		$this->getEm()->flush();

		$field_id = $new_field->getId();
		$this->saveMappedId('zd_ticket_field_id', $field_info['id'], $field_id);

		#------------------------------
		# May need to process sub-options now
		#------------------------------

		if (!empty($field_info['custom_field_options'])) {

			// Process raw options into an array where a parent_id is set

			$raw_options = Arrays::keyFromData($field_info['custom_field_options'], 'name');
			$k = 1;
			foreach (array_keys($raw_options) as $id) {
				$opt = &$raw_options[$id];

				$opt['display_order'] = $k++;

				$name = $opt['name'];
				$parent_id = null;

				if (($pos = strpos($name, '::')) !== false) {
					$parent_id = trim(substr($name, 0, $pos));
				}

				if ($parent_id) {
					if (!isset($raw_options[$parent_id])) {
						$raw_options[$parent_id] = array(
							'name'          => $parent_id,
							'value'         => null,
							'display_order' => $opt['display_order'],
							'parent_id'     => null,
						);
					}

					$opt['name'] = trim(substr($name, $pos+2));
					$opt['name'] = preg_replace('#\s*::\s*#', ' > ', $opt['name']);
				}

				$opt['parent_id'] = $parent_id;
			}
			unset($opt);

			$id_map = array();

			// First process the parent options
			foreach ($raw_options as $id => $opt) {
				if ($opt['parent_id']) {
					continue;
				}

				$child = $new_field->createChild();
				$child->title = $opt['name'];
				$child->display_order = $opt['display_order'];
				$child->setOption('parent_id', 0);

				$this->getEm()->persist($child);
				$this->getEm()->flush();

				if ($opt['value']) {
					$this->saveMappedId('zd_tagger_id', $field_id . '_' . $opt['value'], $child->getId());
				}

				$id_map[$id] = $child->getId();
			}

			// And now the children
			foreach ($raw_options as $id => $opt) {
				if (!$opt['parent_id']) {
					continue;
				}

				$sub_child = $new_field->createChild();
				$sub_child->title = $opt['name'];
				$sub_child->display_order = $opt['display_order'];
				$sub_child->setOption('parent_id', $id_map[$opt['parent_id']]);

				$this->getEm()->persist($sub_child);
				$this->getEm()->flush();

				$this->saveMappedId('zd_tagger_id', $field_id . '_' . $opt['value'], $sub_child->getId());
			}
		}

		$this->logMessage(sprintf("-- Saved %d as %s", $field_id, $new_field->getTypeName()));
	}
}
