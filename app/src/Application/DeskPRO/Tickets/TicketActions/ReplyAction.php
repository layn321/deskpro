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
 * @subpackage Tickets
 */

namespace Application\DeskPRO\Tickets\TicketActions;

use Application\DeskPRO\App;

use Application\DeskPRO\Tickets\SnippetFormatter;
use Application\DeskPRO\Tickets\TicketActions\ActionInterface;
use Application\DeskPRO\People\PersonContextInterface;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\TicketMessage;
use Application\DeskPRO\Entity\TicketAttachment;
use Application\DeskPRO\Entity\Person;

class ReplyAction extends AbstractAction implements PersonContextInterface, PermissionableAction
{
	protected $reply_text;
	protected $reply_pos;
	protected $attach_ids = array();
	protected $person_context;
	protected $is_html = false;
	protected $person_id = null;

	public function __construct($reply_text, array $attach_ids = array(), $reply_pos = null, $is_html = false, $person_id = null)
	{
		$this->reply_text = $reply_text;
		$this->attach_ids = $attach_ids;
		$this->reply_pos  = $reply_pos;
		$this->is_html    = $is_html;
		$this->person_id  = $person_id;
	}


	/**
	 * @param \Application\DeskPRO\Entity\Person $person
	 * @return void
	 */
	public function setPersonContext(Person $person)
	{
		$this->person_context = $person;
	}


	public function checkPermission(Ticket $ticket, Person $person)
	{
		if (!$person->PermissionsManager->TicketChecker->canModify($ticket, 'reply')) {
			return false;
		}

		return true;
	}

	/**
	 * Apply the property to the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function apply(Ticket $ticket)
	{
		$person = null;

		if ($this->person_id) {
			$person = App::getDataService('Agent')->get($this->person_id);
		}

		if (!$person) {
			if ($this->person_context && $this->person_context->getId()) {
				$person = $this->person_context;
			} else {
				if ($ticket->agent) {
					$person = $ticket->agent;
				} else {
					// Try to find last agent to replied in tikcet
					$agent_id = App::getDb()->fetchColumn("
						SELECT tickets_messages.person_id
						FROM tickets_messages
						LEFT JOIN people ON (people.id = tickets_messages.person_id)
						WHERE tickets_messages.ticket_id = 1 AND people.is_agent = 1
						ORDER BY tickets_messages.id DESC
					");

					if ($agent_id) {
						$person = App::getDataService('Agent')->get($agent_id);
					}
				}
			}
		}

		if (!$person || !$person->getId()) {
			return;
		}

		$message = new TicketMessage();
		$message->person = $person;
		$message->date_created = new \DateTime('+1 second');

		if ($this->is_html) {
			$message->message_text = $this->reply_text;
		} else {
			$reply_text = $this->reply_text;

			$formatter = new SnippetFormatter(App::getContainer()->get('twig'));
			$reply_text = $formatter->formatText($reply_text, $ticket);

			$message->setMessageHtml($reply_text);
		}
		$ticket->addMessage($message);

		if ($this->attach_ids) {
			foreach ($this->attach_ids as $blob_id) {
				$blob = App::getOrm()->getRepository('DeskPRO:Blob')->find($blob_id);

				if ($blob) {
					$attach = new TicketAttachment();
					$attach['blob'] = $blob;
					$attach['person'] = $this->person_context;

					$message->addAttachment($attach);
					App::getOrm()->persist($attach);
				}
			}
		}
	}


	/**
	 * Get an array of actions that would be performed on the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function getApplyActions(Ticket $ticket)
	{
		return array(
			array('action' => 'reply', 'reply_text' => $this->reply_text, 'attach_ids' => $this->attach_ids, 'is_html' => $this->is_html, 'person_id' => $this->person_id)
		);
	}


	/**
	 * Get reply text
	 *
	 * @return int
	 */
	public function getReplyText()
	{
		return $this->reply_text;
	}


	/**
	 * Get attach ids
	 *
	 * @return array
	 */
	public function getAttachIds()
	{
		return $this->attach_ids;
	}


	/**
	 * @return string
	 */
	public function getReplyPos()
	{
		return $this->reply_pos;
	}


	/**
	 * @param \Application\DeskPRO\Tickets\TicketActions\ActionInterface $other_action
	 * @return \Application\DeskPRO\Tickets\TicketActions\ActionInterface
	 */
	public function merge(ActionInterface $other_action)
	{
		return $other_action;
	}

	/**
	 * @return string
	 */
	public function getDescription($as_html = true)
	{
		$tr = App::getTranslator();

		if ($as_html) {
			$flat = str_replace(array("\r\n", "\n"), ' ', $this->reply_text);
			if (strlen($flat) > 80) $flat = substr($flat, 0, 80) . '...';

			$desc = '<span class="highlight-description">'.htmlspecialchars($flat).'</span>';

			if (isset($_GET['macro_reply_context'])) {
				if ($this->reply_pos == 'overwrite') {
					$ret = "Set reply text";
				} elseif ($this->reply_pos == 'append') {
					$ret = "Append reply text";
				} else {
					$ret = "Prepend reply text";
				}

				if (!$this->is_html) {
					$html = $this->reply_text;
				} else {
					$html = '<p>' . nl2br(htmlspecialchars(trim($this->reply_text), \ENT_QUOTES)) . '</p>';
				}

				$ret = '<span class="with-reply" data-reply-pos="' . $this->reply_pos . '">' . $ret . '<script type="text/x-deskpro-plain" class="reply-text">' . $html . '</script></span>';
				return $ret;
			}

			$ret = $tr->phrase('agent.tickets.add_reply_x_action', array('desc' => $desc));
		}

		return $tr->phrase('agent.tickets.add_reply_action');
	}
}
