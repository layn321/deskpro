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

class ReplySnippetAction extends AbstractAction implements PersonContextInterface, PermissionableAction
{
	/**
	 * @var \Application\DeskPRO\Entity\TextSnippet
	 */
	protected $snippet;

	/**
	 * @var int
	 */
	protected $snippet_id;

	/**
	 * Possible values: append, prepend, overwrite
	 * @var string
	 */
	protected $reply_pos;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person_context;

	public function __construct($snippet_id, $reply_pos = null)
	{
		$this->snippet_id = $snippet_id;
		$this->reply_pos  = $reply_pos;

		$this->snippet = App::getOrm()->find('DeskPRO:TextSnippet', $snippet_id);
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
		if (!$this->snippet) {
			return;
		}

		if (!$this->person_context) {
			if ($ticket->agent) {
				$this->person_context = $ticket->agent;
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
					$this->person_context = App::getDataService('Agent')->get($agent_id);
				}
			}
		}

		if (!$this->person_context) {
			return;
		}

		$message = new TicketMessage();
		$message->person = $this->person_context;

		$snippet_text = App::getTranslator()->objectChoosePhraseText(
			$this->snippet,
			'snippet',
			array(
				$ticket->language,
				$this->person_context->getRealLanguage()
			)
		);

		$formatter = new SnippetFormatter(App::getContainer()->get('twig'));
		$message->setMessageHtml($formatter->formatText($snippet_text, $ticket));
		$ticket->addMessage($message);
	}


	/**
	 * Get an array of actions that would be performed on the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function getApplyActions(Ticket $ticket)
	{
		if (!$this->snippet) {
			return array();
		}

		return array(
			array('action' => 'reply_snippet', 'snippet_id' => $this->snippet_id, 'reply_pos' => $this->reply_pos)
		);
	}


	/**
	 * Get reply text
	 *
	 * @return int
	 */
	public function getSnippetId()
	{
		return $this->snippet_id;
	}


	/**
	 * @return \Application\DeskPRO\Entity\TextSnippet
	 */
	public function getSnippet()
	{
		return $this->snippet;
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
		if (!$this->snippet) {
			return "<error>Unknown Snippet #{$this->snippet}</error>";
		}

		// Hack ot append the proper position
		// when being viewed from replybox
		// See TicketController::ajaxGetMacroAction
		if (isset($_GET['macro_reply_context'])) {
			if ($this->reply_pos == 'overwrite') {
				$ret = "Reply with snippet: " . $this->snippet->title;
			} elseif ($this->reply_pos == 'append') {
				$ret = "Append snippet to reply: " . $this->snippet->title;
			} else {
				$ret ="Prepend snippet to reply: " . $this->snippet->title;
			}

			$html = '';
			if (!empty($GLOBALS['DP_ACTIVE_TICKET'])) {

				$snippet_text = App::getTranslator()->objectChoosePhraseText(
					$this->snippet,
					'snippet',
					array($GLOBALS['DP_ACTIVE_TICKET']->language)
				);

				$formatter = new SnippetFormatter(App::getContainer()->get('twig'));
				$html = $formatter->formatText($snippet_text, $GLOBALS['DP_ACTIVE_TICKET']);
			}

			$ret = '<span class="with-reply" data-reply-pos="' . $this->reply_pos . '">' . $ret . '<script type="text/x-deskpro-plain" class="reply-text">' . $html . '</script></span>';
			return $ret;
		}

		return "Reply with snippet: " . $this->snippet->title;
	}
}
