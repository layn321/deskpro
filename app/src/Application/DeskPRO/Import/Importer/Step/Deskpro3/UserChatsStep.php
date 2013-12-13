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

namespace Application\DeskPRO\Import\Importer\Step\Deskpro3;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\TicketMessage;
use Application\DeskPRO\Entity\TicketAttachment;
use Application\DeskPRO\Entity\TicketParticipant;

use Application\DeskPRO\Entity\ChatConversation;
use Application\DeskPRO\Entity\ChatMessage;

class UserChatsStep extends AbstractDeskpro3Step
{
	public $first_dep_id;
	public $person_cache;

	public static function getTitle()
	{
		return 'Import User Chats';
	}

	public function countPages()
	{
		$count = $this->getOldDb()->fetchColumn("SELECT COUNT(*) FROM chat_chat");
		if (!$count) {
			return 1;
		}

		return ceil($count / 50);
	}

	public function preRunAll()
	{
		$this->importer->removeTableIndexes('chat_conversations');
		$this->importer->removeTableIndexes('chat_messages');
	}

	public function postRunAll()
	{
		$this->importer->restoreTableIndexes('chat_conversations');
		$this->importer->restoreTableIndexes('chat_messages');
	}

	public function run($page = 1)
	{
		if ($page == 1) {
			$this->preRunAll();
		}

		$this->first_dep_id = $this->getDb()->fetchColumn("SELECT id FROM departments WHERE is_chat_enabled AND parent_id IS NULL ORDER BY display_order ASC LIMIT 1");

		$sub_start_time = microtime(true);
		$batch = $this->getBatch($page - 1);
		$this->logMessage("-- Processing batch {$page}");

		$this->getDb()->beginTransaction();
		try {
			foreach ($batch as $c) {
				$this->processChat($c);
			}
			$this->importer->flushSaveMappedIdBuffer();
			$this->getDb()->commit();
		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}

		$sub_end_time = microtime(true);
		$this->logMessage(sprintf("-- Done. Took %.3f seconds.", $sub_end_time-$sub_start_time));

		if ($page >= $this->countPages()) {
			$this->postRunAll();
		}
	}

	public function processChat($chat_info)
	{
		$chat_id = $chat_info['id'];

		#------------------------------
		# Make sure we havent already done it
		#------------------------------

		$check_exist = $this->getMappedNewId('chat', $chat_id);
		if ($check_exist) {
			$this->getLogger()->log("{$chat_id} already mapped, skipping", 'DEBUG');
			return;
		}

		#------------------------------
		# Make the chat
		#------------------------------

		$convo = new ChatConversation();
		$convo->status = 'ended';

		// Department
		if ($chat_info['depid']) {
			$dep_id = $this->getMappedNewId('chat_dep', $chat_info['depid']);
			if ($dep_id) {
				$convo->department = $this->getEm()->find('DeskPRO:Department', $dep_id);
			}
		} else {
			$convo->department = $this->getEm()->find('DeskPRO:Department', $this->first_dep_id);
		}

		// User
		if ($chat_info['userid']) {
			$person_id = $this->getMappedNewId('user', $chat_info['userid']);
			if ($person_id) {
				$convo->person = $this->getPerson($person_id);
			}
		}

		// Agent
		if ($chat_info['techid']) {
			$person_id = $this->getMappedNewId('tech', $chat_info['techid']);
			if ($person_id) {
				$convo->agent = $this->getPerson($person_id);
			}
		}

		$convo->subject = $chat_info['subject'];
		if (!$convo->subject) {
			$convo->subject = '(Untitled)';
		}

		if ($chat_info['rating'] >= 1) {
			$convo->rating_response_time = $chat_info['rating'];
			$convo->rating_overall = $chat_info['rating'];
		}

		if ($chat_info['feedback']) {
			$convo->rating_comment = $chat_info['feedback'];
		}

		if ($chat_info['useremail']) {
			$convo->person_email = $chat_info['useremail'];
		}
		if ($chat_info['userdisplayname']) {
			$convo->person_name = $chat_info['userdisplayname'];
		}

		$convo->date_created = new \DateTime('@' . (int)$chat_info['timestamp_start']);
		if ($chat_info['timestamp_assigned']) {
			$convo->date_assigned = new \DateTime('@' . (int)$chat_info['timestamp_assigned']);
		}
		$convo->date_ended = new \DateTime('@' . (int)($chat_info['timestamp_assigned'] ?: $chat_info['timestamp_start']));

		$this->getEm()->persist($convo);
		$this->getEm()->flush();

		$this->saveMappedId('chat', $chat_info['id'], $convo->id, true);

		#------------------------------
		# Attachments
		#------------------------------

		$chat_attachments = $this->getOldDb()->fetchAll("SELECT * FROM chat_attachment WHERE chatid = ?", array($chat_info['id']));

		$chat_attach_info = array();

		foreach ($chat_attachments as $attach_info) {
			$blob_id = $this->getMappedNewId('chat_attachment-blob', $attach_info['id']);
			if (!$blob_id) {
				continue;
			}

			$chat_attach_info[$attach_info['id']] = array('blob_id' => $blob_id, 'filename' => $attach_info['filename'], 'filesize' => 0);
		}


		#------------------------------
		# Add messages
		#------------------------------

		$all_message_info = $this->getOldDb()->fetchAll("
			SELECT * FROM chat_message
			WHERE chatid = ?
			ORDER BY id ASC
		", array($chat_info['id']));

		$first_agent_date = null;
		foreach ($all_message_info as $message_info) {
			$add_end = false;

			$message = array();
			$message['metadata'] = 'a:0:{}';
			$message['conversation_id'] = $convo->id;
			$message['date_created'] = date('Y-m-d H:i:s', (int)$message_info['timestamp_sent']);
			if ($message_info['visibility'] == 'tech') {
				$message['is_user_hidden'] = 1;
			}

			// Tech message
			if ($message_info['authortype'] == 'tech') {
				if (!$first_agent_date) {
					$first_agent_date = new \DateTime('@' . (int)$message_info['timestamp_sent']);
				}
				$agent = $this->getPerson($this->getMappedNewId('tech', $message_info['authorid']));
				if (!$agent) {
					continue;
				}
				$message['origin']      = 'agent';
				$message['author_id']   = $agent->id;
				$message['person_name'] = $agent->getDisplayName();
				$message['content']     = $message_info['message'];

				if (!$message_info['message']) continue;

			// User message
			} elseif ($message_info['authortype'] == 'user') {
				if ($message_info['authorid']) {
					$person = $this->getPerson($this->getMappedNewId('user', $message_info['authorid']));
					if (!$person) {
						continue;
					}

					$message['origin']      = 'user';
					$message['author_id']   = $person->id;
					$message['person_name'] = $person->getDisplayName();
				}
				$message['content']     = $message_info['message'];

				if (!$message_info['message']) continue;

			// System message
			} else {
				// assign:from:0:to:8
				if (preg_match('#^assign:from:([0-9]+):to:([0-9]+)$#', $message_info['message'], $m)) {
					if ($m[1]) {
						$old_agent = $this->getPerson($this->getMappedNewId('tech', $m[1]));
						if (!$old_agent) {
							continue;
						}
					} else {
						$old_agent = null;
					}

					if ($m[2]) {
						$new_agent = $this->getPerson($this->getMappedNewId('tech', $m[2]));
						if (!$new_agent) {
							continue;
						}
					} else {
						$new = null;
					}

					$message['is_sys'] = true;
					$message['person_name'] = 'sys';

					if ($old_agent && !$new_agent) {
						$message['content'] = App::getTranslator()->phrase('user.chat.message_unassigned');
					} elseif (!$old_agent && $new_agent) {
						$message['content'] = App::getTranslator()->phrase('user.chat.message_assigned', array(
							'name' => $new_agent->getDisplayName()
						));
					} else {
						continue;
					}

				// attachment:attachmentId:1:fileName:Front Page.bmml:techId:1
				} elseif (preg_match('#^attachment:attachmentId:([0-9]+):fileName:(.*?):techId:(.*?)$#', $message_info['message'], $m)) {

					$attach_id = $m[1];
					$filename = $m[2];
					$tech_id = $m[3];

					if (!isset($chat_attach_info[$attach_id])) {
						continue;
					}

					$new_agent = $this->getPerson($this->getMappedNewId('tech', $tech_id));
					if (!$new_agent) {
						continue;
					}

					$blob = $this->getEm()->find('DeskPRO:Blob', $chat_attach_info[$attach_id]['blob_id']);
					if (!$blob) {
						continue;
					}

					$message['author_id'] = $new_agent->id;
					$message['person_name'] = $new_agent->getDisplayName();
					$message['content'] = '<a href="' . $blob->getDownloadUrl() . '">' . htmlspecialchars($filename) . '</a>';
					$message['is_html'] = true;

				// end:comment:xxxxx:tech:8
				} elseif (preg_match('#^end:comment:(.*?)$#', $message_info['message'], $m)) {
					if (strpos($message_info['message'], '::') !== false) {
						$end_comment = '';
						$agent_id = \Orb\Util\Strings::extractRegexMatch('#tech:([0-9]+)$#', $message_info['message'], 1);
					} else {
						$parts = explode(':', $m[1]);
						$agent_id = array_pop($parts);
						array_pop($parts);

						$end_comment = implode(':', $parts);
					}

					$agent = $this->getPerson($this->getMappedNewId('tech', $agent_id));
					if (!$agent) {
						continue;
					}

					$message['author_id'] = $agent->id;
					$message['person_name'] = $agent->getDisplayName();
					$message['content'] = str_replace('\\:', ':', $end_comment);

					$add_end = array();
					$add_end['conversation_id'] = $convo->id;
					$add_end['date_created'] = date('Y-m-d H:i:s', $message_info['timestamp_sent']);
					$add_end['is_sys'] = true;
					$add_end['person_name'] = 'sys';
					$add_end['metadata'] = 'a:0:{}';
					$add_end['content'] = App::getTranslator()->phrase('agent.general.chat_ended');

				} elseif (preg_match('#^end:who:user$#', $message_info['message']) || preg_match('#^end:who:user:timeout:#', $message_info['message'])) {
					$message['person_name'] = 'sys';
					$message['content'] = 'Chat ended';
				} else {
					continue;
				}
			}

			$this->getDb()->insert('chat_messages', $message);
			if ($add_end) {
				$this->getDb()->insert('chat_messages', $add_end);
			}
		}

		if ($first_agent_date) {
			$convo->date_first_agent_message = $first_agent_date;
			$this->getEm()->persist($convo);
			$this->getEm()->flush();
		}
	}

	public function getPerson($id)
	{
		if (isset($this->person_cache[$id])) {
			if ($this->person_cache[$id] == 'NOT EXIST') {
				return null;
			}
			return $this->person_cache[$id];
		}

		$this->person_cache[$id] = $this->getEm()->find('DeskPRO:Person', $id);
		if (!$this->person_cache[$id]) {
			$this->person_cache[$id] = 'NOT EXIST';
			return null;
		}
		return $this->person_cache[$id];
	}

	/**
	 * @param $page
	 * @return array
	 */
	public function getBatch($page)
	{
		$start = $page * 50;
		$ids = $this->getOldDb()->fetchAll("SELECT * FROM chat_chat ORDER BY id ASC LIMIT $start, 50");

		return $ids;
	}
}
