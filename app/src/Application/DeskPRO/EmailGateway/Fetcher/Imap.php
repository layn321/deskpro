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

namespace Application\DeskPRO\EmailGateway\Fetcher;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

/**
 * Fetches mail from an imap server
 */
class Imap extends AbstractFetcher
{
	/**
	 * Initiates the connection
	 *
	 * @return \Zend\Mail\Storage\Imap
	 */
	protected function _initConnection()
	{
		$storage = new Imap($this->gateway['connection_options']);
		return $storage;
	}

	/**
	 * Reads the next message in the inbox
	 *
	 * @return \Application\DeskPRO\EmailGateway\Fetcher\RawMessage
	 */
	protected function _readNext()
	{
		try {
			$headers = $this->getStorage()->getRawHeader(1);
		} catch (\Zend\Mail\Storage\Exception $e) {
			// means there is none
			return null;
		}

		$raw_message = new RawMessage();
		$raw_message->id = 1;
		$raw_message->headers = $headers;
		$raw_message->content = $headers . "\n\n" . $this->storage->getRawContent(1);

		return $raw_message;
	}

	/**
	 * Deletes the message from the server.
	 *
	 * @param  $id
	 */
	protected function _doneRead($id)
	{
		$move_to = isset($this->gateway['connection_options']['delete_move']) ? $this->gateway['connection_options']['delete_move'] : false;
		if ($move_to) {
			$this->getStorage()->moveMessage($id, $move_to);
		} else {
			$this->getStorage()->removeMessage($id);
		}
	}
}
