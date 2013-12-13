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

use Application\DeskPRO\Search\EntityListener;

use Application\DeskPRO\Search\Searcher\Elastic\ContentSearcher;
use Application\DeskPRO\Search\Searcher\Elastic\TicketSearcher;

use Application\DeskPRO\Search\SearcherResult\ResultSet;
use Application\DeskPRO\Search\SearcherResult\ResultInterface;

use Orb\Util\Strings;

/**
 * Search adapter
 */
abstract class ElasticAdapter extends AbstractAdapter
{
	public static $capabilities = array(
		'searcher_content', 'searcher_content_labels', 'searcher_content_similar',
		'searcher_tickets',
	);

	/**
	 * @var \Elastica_Client
	 */
	protected $client;

	/**
	 * Maps a content-type to an index
	 * @var array
	 */
	protected $contenttype_to_index = array();

	/**
	 * Create a new ElasticAdapter with a client connection to the host/port
	 *
	 * @param string $host  The Elastic Search server host
	 * @param int    $port  The Elastic Search server port
	 * @return \Application\DeskPRO\Search\Adapter\ElasticAdapter
	 */
	public static function create($host, $port = \Elastica_Client::DEFAULT_PORT)
	{
		$client = new \Elastica_Client(array(
			'host' => $host,
			'port' => $port
		));

		return new self($client);
	}

	public function __construct(\Elastica_Client $client)
	{
		$this->client = $client;

		$this->addContentTypeMap('Application\\DeskPRO\\Entity\\Article',         'article');
		$this->addContentTypeMap('Application\\DeskPRO\\Entity\\Download',        'download');
		$this->addContentTypeMap('Application\\DeskPRO\\Entity\\Feedback',            'feedback');
		$this->addContentTypeMap('Application\\DeskPRO\\Entity\\News',            'news');
		$this->addContentTypeMap('Application\\DeskPRO\\Entity\\Ticket',          'ticket');
		$this->addContentTypeMap('Application\\DeskPRO\\Entity\\TicketMessage',   'ticket_message');

		$this->addIndexMap('article',   'content');
		$this->addIndexMap('download',  'content');
		$this->addIndexMap('feedback',      'content');
		$this->addIndexMap('news',      'content');
		$this->addIndexMap('ticket',         'tickets');
		$this->addIndexMap('ticket_message', 'tickets');
	}


	/**
	 * Delete the specified docs from the index
	 *
	 * @param  $documents
	 * @return void
	 */
	public function deleteDocumentsFromIndex(array $documents)
	{
		$sorted_types = array();

		foreach ($documents as $doc) {
			$type_name = $doc->getType();

			if (!isset($sorted_types[$type])) {
				$sorted_types[$type_name] = array();
			}

			$sorted_types[$type_name][] = $doc->getId();
		}

		foreach ($sorted_types as $type_name => $ids) {
			$index_name = $this->getIndexNameForContentType($type_name);
			$this->client->deleteIds($ids, $index_name, $type_name);
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
		$elastica_docs = array();

		foreach ($documents as $doc) {
			$e_doc = new \Elastica_Document($doc->getId());
			$e_doc->setType($doc->getType());
			$e_doc->setIndex($this->getIndexNameForContentType($doc->getType()));
			$e_doc->setData($doc->getData());

			$elastica_docs[] = $e_doc;
		}

		$this->client->addDocuments($elastica_docs);
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

		$classname = 'Application\\DeskPRO\\Search\\ContentType\\Elastic\\' . $type_name;
		$obj = new $classname();

		return $obj;
	}

	/**
	 * Get a ticket searcher.
	 *
	 * Factory method.
	 *
	 * @return \Application\DeskPRO\Search\Searcher\ContentSearcherInterface
	 */
	public function getTicketSearcher()
	{
		$searcher = new ContentSearcher($this);
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
		$searcher = new ContentSearcher($this);
		$searcher->setPersonContext($this->getPersonContext());

		return $searcher;
	}


	/**
	 * Get the Elastica client to communicate with the server.
	 *
	 * @return \Elastica_Client
	 */
	public function getClient()
	{
		return $this->client;
	}


	/**
	 * Get an index
	 *
	 * @param $index
	 * @return \Elastica_Index
	 */
	public function getIndex($index)
	{
		return $this->getClient()->getIndex($index);
	}


	/**
	 * Maps a contenttype to an Elastic index
	 *
	 * @param string $type_name The type name
	 * @param string $index_name The name of the index
	 */
	public function addIndexMap($type_name, $index_name)
	{
		$this->contenttype_to_index[$type_name] = $index_name;
	}


	/**
	 * Get the index name for a content type
	 *
	 * @param $type_name
	 * @return string
	 */
	public function getIndexNameForContentType($type_name)
	{
		return $this->contenttype_to_index[$type_name];
	}


	/**
	 * Get the index for a contenttype
	 *
	 * @param $type_name
	 * @return \Elastica_Index
	 */
	public function getIndexForContentType($type_name)
	{
		$index = $this->contenttype_to_index[$type_name];
		return $this->getIndex($index);
	}
}
