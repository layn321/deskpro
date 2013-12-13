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

namespace Application\UserBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\ChatConversation;
use Application\DeskPRO\Entity\ChatMessage;

class ChatLogController extends AbstractController implements RequireUserInterface
{
	############################################################################
	# list
	############################################################################

	public function listAction()
	{
		$chat_conversations = $this->em->createQuery("
			SELECT c
			FROM DeskPRO:ChatConversation c
			WHERE
				c.is_agent = 0
				AND c.status = 'ended'
				AND c.person = ?0
			ORDER BY c.id DESC
		")->execute(array($this->person));

		return $this->render('UserBundle:ChatLog:list.html.twig', array(
			'chat_conversations' => $chat_conversations,
		));
	}

	############################################################################
	# view
	############################################################################

	public function viewAction($conversation_id)
	{
		$convo = $this->em->find('DeskPRO:ChatConversation', $conversation_id);

		if (!$convo || $convo->is_agent || !$convo->person || $convo->person->getId() != $this->person->getId()) {
			throw $this->createNotFoundException();
		}

		$convo_messages = $this->em->createQuery("
			SELECT m
			FROM DeskPRO:ChatMessage m
			WHERE m.conversation = ?0 AND m.is_user_hidden = false
			ORDER BY m.id ASC
		")->execute(array($convo));

		$field_manager = $this->container->getSystemService('chat_fields_manager');
		$custom_fields = $field_manager->getDisplayArrayForObject($convo);

		return $this->render('UserBundle:ChatLog:view.html.twig', array(
			'convo'           => $convo,
			'custom_fields'   => $custom_fields,
			'convo_messages'  => $convo_messages,
		));
	}
}