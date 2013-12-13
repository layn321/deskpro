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
 * @category Search
 */

namespace Application\DeskPRO\Search\ContentType\Mysql;

use Application\DeskPRO\Search\ContentType\AbstractContentType;
use Application\DeskPRO\Search\Adapter\MysqlAdapter;
use Application\DeskPRO\Search\SearcherResult\ResultInterface;
use Application\DeskPRO\Search\Indexer\Document;

class ChatConversation extends AbstractContentType
{
	const ENTITY_NAME = 'DeskPRO:ChatConversation';

	public function objectToDocument($convo)
	{
		$data = array();
		$data['id'] = $convo['id'];
		$data['content_type'] = 'chat_conversation';
		$data['content'] = $convo['subject'] . "\n" . $convo['content'] . "\n";

		$content = array();
		$content[] = $ticket['subject'];
		foreach ($convo->messages as $message) {
			if ($message->is_sys) continue;

			if ($message->is_html) {
				$content[] = strip_tags($message->content);
			} else {
				$content[] = $message->content;
			}
		}

		$data['content'] = implode(" ", $content);

		$doc = Document::newFromArray($data);

		return $doc;
	}
}
