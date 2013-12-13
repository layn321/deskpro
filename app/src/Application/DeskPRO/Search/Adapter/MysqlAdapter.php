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

namespace Application\DeskPRO\Search\Adapter;

use Orb\Util\CapabilityInformerInterface;

use Application\DeskPRO\App;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Application\DeskPRO\Search\Searcher\Mysql\ContentSearcher;
use Application\DeskPRO\Search\Searcher\Mysql\TicketSearcher;
use Application\DeskPRO\Search\Searcher\Mysql\ChatConversationSearcher;
use Application\DeskPRO\Search\Searcher\Mysql\AgentCombinedSearcher;

use Application\DeskPRO\Search\SearcherResult\ResultSet;
use Application\DeskPRO\Search\SearcherResult\ResultInterface;

use Orb\Util\Strings;

/**
 * Search adapter
 */
class MysqlAdapter extends AbstractAdapter
{
	public static $capabilities = array(
		'searcher_content', 'searcher_content_labels',
		'searcher_tickets',
	);

	public function __construct()
	{
		$this->addContentTypeMap('Application\\DeskPRO\\Entity\\Article', 'article');
		$this->addContentTypeMap('Application\\DeskPRO\\Entity\\Download', 'download');
		$this->addContentTypeMap('Application\\DeskPRO\\Entity\\Feedback', 'feedback');
		$this->addContentTypeMap('Application\\DeskPRO\\Entity\\News', 'news');
		$this->addContentTypeMap('Application\\DeskPRO\\Entity\\Ticket', 'ticket');
		$this->addContentTypeMap('Application\\DeskPRO\\Entity\\TicketMessage', 'ticket_message');
		$this->addContentTypeMap('Application\\DeskPRO\\Entity\\ChatConversation', 'chat_conversation');
	}


	/**
	 * Delete the specified docs from the index
	 *
	 * @param  $documents
	 * @return void
	 */
	public function deleteDocumentsFromIndex(array $documents)
	{
		foreach ($documents as $doc) {
			App::getDb()->delete('content_search', array(
				'object_type' => $doc->getContentTypeName(),
				'object_id'   => $doc->getId()
			));
			App::getDb()->delete('content_search_attribute', array(
				'object_type' => $doc->getContentTypeName(),
				'object_id'   => $doc->getId()
			));
		}
	}


	/**
	 * Update the search index with the specified docs
	 *
	 * @param  $documents
	 * @return void
	 */
	public function updateDocumentsInIndex(array $documents)
	{
		foreach ($documents as $doc) {

			if ($doc->isMarkedRemove()) {
				$this->deleteDocumentsFromIndex(array($doc));
				continue;
			}

			$data = $doc->getData();

			App::getDb()->executeUpdate("
				REPLACE INTO content_search
				SET object_type = ?, object_id = ?, content = ?
			", array($doc->getContentTypeName(), $doc->getId(), $data['content']));

			unset($data['content']);

			foreach ($data as $k => $v) {
				App::getDb()->executeUpdate("
					DELETE FROM content_search_attribute WHERE object_type = ? AND object_id = ?
				", array($doc->getContentTypeName(), $doc->getId()));
				App::getDb()->executeUpdate("
					REPLACE INTO content_search_attribute
					SET object_type = ?, object_id = ?, attribute_id = ?, content = ?
				", array($doc->getContentTypeName(), $doc->getId(), $k, $v));
			}
		}
	}


	/**
	 * Create a new instance of a contenttype object.
	 *
	 * Factory method.
	 *
	 * @param string $type_name
	 * @return \Application\DeskPRO\Search\ContentType\ContentTypeInterface
	 */
	protected function createContentType($type_name)
	{
		// All of our contenttype classes are dumb, so they dont need
		// any special initialization. We can simply initialize them with the classname.

		$type_name = str_replace('_', '-', $type_name);
		$type_name = ucfirst(Strings::dashToCamelCase($type_name));

		$classname = 'Application\\DeskPRO\\Search\\ContentType\\Mysql\\' . $type_name;
		$obj = new $classname();

		return $obj;
	}

	/**
	 * Get a ticket searcher.
	 *
	 * Factory method.
	 *
	 * @return \Application\DeskPRO\Search\Searcher\Mysql\TicketSearcher
	 */
	public function getTicketSearcher()
	{
		$searcher = new TicketSearcher();
		$searcher->setPersonContext($this->getPersonContext());

		return $searcher;
	}


	/**
	 * Get a content searcher.
	 *
	 * Factory method.
	 *
	 * @return \Application\DeskPRO\Search\Searcher\ContentSearcherInterface
	 */
	public function getContentSearcher()
	{
		$searcher = new ContentSearcher();
		$searcher->setPersonContext($this->getPersonContext());

		return $searcher;
	}


	/**
	 * Get the combined agent searcher.
	 *
	 * Factory method.
	 *
	 * @return \Application\DeskPRO\Search\Searcher\Mysql\AgentCombinedSearcher
	 */
	public function getAgentCombinedSearcher()
	{
		$searcher = new AgentCombinedSearcher();
		$searcher->setPersonContext($this->getPersonContext());

		return $searcher;
	}


	/**
	 * Get the combined agent searcher.
	 *
	 * Factory method.
	 *
	 * @return \Application\DeskPRO\Search\Searcher\Mysql\ChatConversationSearcher
	 */
	public function getChatConversationSearcher()
	{
		$searcher = new ChatConversationSearcher();
		$searcher->setPersonContext($this->getPersonContext());

		return $searcher;
	}


	/**
	 * Delete all objects from the index of a particular content type.
	 *
	 * @param string $type_name
	 */
	public function deleteContentTypeFromIndex($type_name)
	{
		App::getDb()->executeUpdate("
			DELETE FROM content_search WHERE object_type = ?
		", array($type_name));
	}


	/**
	 * Labels are added to the fulltext index and then fetched with a fulltext match
	 * in "boolean" mode, which is one of the only ways to efficiently fetch labels
	 * with intersections or unions (ie. content with two labels, or with one label but without another).
	 *
	 * But certain words are stripped for stop words, and mysql doesn't handle dashes very well,
	 * and when fetching labels we don't want to confuse them with other words. So
	 * we "encode" them as these hashes, so we can search for "+lbl1232984rf" specifically.
	 *
	 * @param  $label
	 * @return string
	 */
	public static function encodeLabel($label)
	{
		$label = "lbl" . md5(strtolower(trim($label)));
		return $label;
	}


	/**
	 * @param  $label
	 * @return string
	 */
	public static function encodeProperty($k, $v)
	{
		$label = md5(strtolower($k . $v));
		return $label;
	}
}
