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

namespace MicrosoftTranslator\Controller;

use Application\DeskPRO\Controller\AbstractController;
use Application\DeskPRO\Entity\TicketMessageTranslated;

class TicketController extends AbstractController
{
	/**
	 * @var \Application\DeskPRO\Entity\Plugin
	 */
	public $plugin;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	public $person;

	/**
	 * Translates a ticket message
	 *
	 * @param int $message_id
	 * @param string $from
	 * @param string $to
	 * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	public function translateMessageAction($message_id, $from, $to)
	{
		#------------------------------
		# Get ticket message
		#------------------------------

		/** @var \Application\DeskPRO\Entity\TicketMessage $message */
		$message = $this->em->find('DeskPRO:TicketMessage', $message_id);

		if (!$message) {
			return $this->createNotFoundException();
		}

		/** @var \Application\DeskPRO\Entity\Ticket $ticket */
		$ticket = $message->ticket;

		if (!$this->person->PermissionsManager->TicketChecker->canView($ticket)) {
			return $this->createNotFoundException();
		}

		$message_text = $message->message;

		#------------------------------
		# Translate it
		#------------------------------

		$message_translated = $this->em->getRepository('DeskPRO:TicketMessageTranslated')->getForMessage($message, $to);

		if ($message_translated) {

			// The translated text is only good if it matches the 'from' or if the user chose 'auto'
			if ($from == 'auto' || $from == $message_translated->from_lang_code) {
				return $this->createJsonResponse(array(
					'ticket_id'             => $ticket->getId(),
					'message_id'            => $message->getId(),
					'message_translated_id' => $message_translated->getId(),
					'message'               => $message_translated->message,
					'from_lang_code'        => $message_translated->from_lang_code,
					'to_lang_code'          => $message_translated->lang_code,
				));
			}
		}

		/** @var \Orb\Service\Microsoft\Translate\Translate $api */
		$api = $this->plugins->getPluginService('MicrosoftTranslator.tr_api');

		if ($from == 'auto') {
			try {
				$from = $api->detect($message_text);
			} catch (\Exception $e) {
				return $this->createJsonResponse(array('error_code' => 'no_detect', 'message' => 'Could not detect language', 'exception' => $e->getMessage()));
			}

			if (!$message->lang_code) {
				$message->lang_code = $from;
				$this->em->persist($message);
				$this->em->flush();
			}
		}

		$message_translated = new TicketMessageTranslated();
		$message_translated->setTicketMessage($message);
		$message_translated->from_lang_code = $from;
		$message_translated->lang_code = $to;

		try {
			$message_translated->message = $api->translate($message_text, $from, $to, 'text/html');
		} catch (\Exception $e) {
			return $this->createJsonResponse(array('error_code' => 'no_translate', 'message' => 'Could not translate message', 'exception' => $e->getMessage()));
		}

		$this->em->persist($message_translated);
		$this->em->flush();

		return $this->createJsonResponse(array(
			'ticket_id'             => $ticket->getId(),
			'message_id'            => $message->getId(),
			'message_translated_id' => $message_translated->getId(),
			'message'               => $message_translated->message,
			'from_lang_code'        => $message_translated->from_lang_code,
			'to_lang_code'          => $message_translated->lang_code,
		));
	}


	/**
	 * Translates arbitrary text
	 *
	 * @param string $message_text
	 * @param string $from
	 * @param string $to
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function translateTextAction($message_text, $from, $to)
	{
		/** @var \Orb\Service\Microsoft\Translate\Translate $api */
		$api = $this->plugins->getPluginService('MicrosoftTranslator.tr_api');

		if ($from == 'me') {
			$from = $this->person->getLanguage()->getLocale();
		} else if ($from == 'auto') {
			try {
				$from = $api->detect($message_text);
			} catch (\Exception $e) {
				return $this->createJsonResponse(array('error_code' => 'no_detect', 'message' => 'Could not detect language', 'exception' => $e->getMessage()));
			}
		}

		try {
			$trans_text = $api->translate($message_text, $from, $to, 'text/html');
		} catch (\Exception $e) {
			return $this->createJsonResponse(array('error_code' => 'no_translate', 'message' => 'Could not translate message', 'exception' => $e->getMessage()));
		}

		return $this->createJsonResponse(array(
			'message'               => $trans_text,
			'from_lang_code'        => $from,
			'to_lang_code'          => $to,
		));
	}
}