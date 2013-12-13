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
 */

namespace Application\DeskPRO\People\PersonMerge;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\People\PersonContextInterface;

use Orb\Util\Arrays;

/**
 * Handles merging of one person into the other
 */
class PersonMerge implements PersonContextInterface
{
	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person_context;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $other_person;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @throws \InvalidArgumentException
	 * @param \Application\DeskPRO\Entity\Person $person_performer
	 * @param \Application\DeskPRO\Entity\Person $person         The base person, this is the one that will still exist at the end
	 * @param \Application\DeskPRO\Entity\Feedback $other_person   The other person, the one that will be merged into $person and then deleted
	 */
	public function __construct(Person $person_performer, Person $person, Person $other_person)
	{
		$this->em = App::getOrm();

		$this->person = $person;
		$this->other_person = $other_person;
		$this->setPersonContext($person_performer);

		if ($person == $other_person) {
			throw new \InvalidArgumentException("You cannot merge a person with itself");
		}
	}

	public function setPersonContext(Person $person)
	{
		$this->person_context = $person;
	}

	public function merge()
	{
		$this->em->beginTransaction();

		try {

			// todo: organizations cc?
			$standard_prop_names = array(
				'gravatar_url',
				'language',
				'organization',
				'organization_position',
				'picture_blob',
				'summary'
			);
			foreach ($standard_prop_names as $prop_name) {
				$prop_standard = new Property\StandardProperty($this->person, $this->other_person);
				$prop_standard->setProperty($prop_name);
				$prop_standard->setStrategy(Property\StandardProperty::STRATEGY_COMBINE);
				$prop_standard->merge();
			}

			if ($this->other_person->date_created < $this->person->date_created) {
				$this->person->date_created = $this->other_person->date_created;
			}

			foreach (array('is_agent', 'can_agent', 'can_admin', 'can_billing', 'can_reports') as $attr) {
				if ($this->person[$attr] || $this->other_person[$attr]) {
					$this->person[$attr] = true;
				}
			}

			$this->_mergeCustomFields();

			$this->_mergeContactData();
			$this->_mergeOtherPersonData();
			$this->_mergeArticles();
			$this->_mergeChats();
			$this->_mergeDownloads();
			$this->_mergeFeedback();
			$this->_mergeNews();
			$this->_mergeTasks();
			$this->_mergeTickets();
			$this->_mergeOther();

			$this->em->persist($this->person);
			$this->em->flush();

			$this->em->remove($this->other_person);
			$this->em->flush();

			$this->em->commit();

		} catch (\Exception $e) {
			$this->em->rollback();

			throw $e;
		}

		return true;
	}

	protected function _mergeContactData()
	{
		$simple_tables = array(
			'people_contact_data',
			'people_emails',
			'people_emails_validating',
			'people_twitter_users'
		);

		foreach ($simple_tables AS $table) {
			$this->_updateTablePersonId($table, 'person_id');
		}
	}

	protected function _mergeCustomFields()
	{
		$field_defs = App::getApi('custom_fields.people')->getEnabledFields();
		foreach ($field_defs as $f) {
			$prop_field = new Property\CustomField($this->person, $this->other_person);
			$prop_field->setField($f);
			$prop_field->setStrategy(Property\StandardProperty::STRATEGY_COMBINE);
			$prop_field->merge();
		}
	}

	protected function _mergeOtherPersonData()
	{
		$simple_tables = array(
			'labels_people',
			'people_notes',
			'people_prefs',
			'person2usergroups',
			'person_activity',
			'person_usersource_assoc'
		);

		foreach ($simple_tables AS $table) {
			$this->_updateTablePersonId($table, 'person_id');
		}
	}

	protected function _mergeArticles()
	{
		$simple_tables = array(
			'articles',
			'article_attachments',
			'article_comments',
			'article_pending_create',
			'article_revisions'
		);

		foreach ($simple_tables AS $table) {
			$this->_updateTablePersonId($table, 'person_id');
		}
	}

	protected function _mergeChats()
	{
		$simple_tables = array(
			'chat_conversations',
			'chat_conversation_to_person'
		);

		foreach ($simple_tables AS $table) {
			$this->_updateTablePersonId($table, 'person_id');
		}

		$this->_updateTablePersonId('chat_blocks', 'by_person_id');
		$this->_updateTablePersonId('chat_messages', 'author_id');
	}

	protected function _mergeDownloads()
	{
		$simple_tables = array(
			'downloads',
			'download_comments',
			'download_revisions'
		);

		foreach ($simple_tables AS $table) {
			$this->_updateTablePersonId($table, 'person_id');
		}
	}

	protected function _mergeFeedback()
	{
		$simple_tables = array(
			'feedback',
			'feedback_attachments',
			'feedback_comments',
			'feedback_revisions'
		);

		foreach ($simple_tables AS $table) {
			$this->_updateTablePersonId($table, 'person_id');
		}
	}

	protected function _mergeNews()
	{
		$simple_tables = array(
			'news',
			'news_comments',
			'news_revisions'
		);

		foreach ($simple_tables AS $table) {
			$this->_updateTablePersonId($table, 'person_id');
		}
	}

	protected function _mergeTasks()
	{
		$simple_tables = array(
			'tasks',
			'task_associations',
			'task_comments'
		);

		foreach ($simple_tables AS $table) {
			$this->_updateTablePersonId($table, 'person_id');
		}
	}

	protected function _mergeTickets()
	{
		$simple_tables = array(
			'pretickets_content',
			'tickets',
			'tickets_attachments',
			'tickets_logs',
			'tickets_messages',
			'tickets_participants',
			'tickets_search_active',
			'tickets_search_message',
			'tickets_search_message_active',
			'ticket_access_codes',
			'ticket_charges',
			'ticket_feedback',
		);
		$complex_tables = array(
			'tickets_deleted' => array('by_person_id')
		);

		foreach ($simple_tables AS $table) {
			$this->_updateTablePersonId($table, 'person_id');
		}
		foreach ($complex_tables AS $table => $columns) {
			foreach ($columns AS $column) {
				$this->_updateTablePersonId($table, $column);
			}
		}
	}

	protected function _mergeOther()
	{
		$simple_tables = array(
			'login_log',
			'page_view_log',
			'ratings',
			'searchlog',
			'visitors'
		);

		foreach ($simple_tables AS $table) {
			$this->_updateTablePersonId($table, 'person_id');
		}
	}

	protected function _updateTablePersonId($table, $column)
	{
		// update ignore lets this work like a "combine" where needed
		App::getDb()->executeUpdate("
			UPDATE IGNORE $table
			SET $column = ?
			WHERE $column = ?
		", array($this->person['id'], $this->other_person['id']));
	}
}
