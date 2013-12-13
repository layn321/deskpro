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

namespace Application\DeskPRO\Languages\Build;

use Zend\Http\Client as HttpClient;
use Zend\Http\Request as HttpRequest;

class TransifexBuild extends AbstractBuild
{
	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @var string
	 */
	protected $username;

	/**
	 * @var string
	 */
	protected $password;

	/**
	 * @var \Zend\Http\Client
	 */
	protected $http;

	/**
	 * @var array
	 */
	protected $resources;

	/**
	 * @param string $url
	 * @param string $username
	 * @param string $password
	 */
	public function __construct($url, $username, $password)
	{
		$this->url      = rtrim($url, '/') . '/api/2';
		$this->username = $username;
		$this->password = $password;
	}


	/**
	 * @return \Zend\Http\Client
	 */
	public function getHttpClient()
	{
		if (!$this->http) {
			$http = new \Zend\Http\Client(null, array('strictredirects' => true));
			$http->setAuth($this->username, $this->password);
		}

		return $http;
	}


	/**
	 * @param string $path
	 * @return array
	 * @throws \RuntimeException
	 */
	public function restGet($path, $silent = false)
	{
		$http = $this->getHttpClient();
		$http->setUri($this->url . '/' . ltrim($path, '/'));

		$req = new HttpRequest();
		$req->setMethod(HttpRequest::METHOD_GET);
		$req->setUri($http->getUri());

		$res = $http->send($req);

		if (!$res->isSuccess()) {
			if ($silent) {
				return false;
			}
			throw new \RuntimeException("Server error : {$res->getStatusCode()}: {$res->getBody()}", $res->getStatusCode());
		}

		$body = $res->getBody();
		$data = json_decode($body, true);

		return $data;
	}


	/**
	 * @param string $path
	 * @param array $data
	 * @return array
	 * @throws \RuntimeException
	 */
	public function restPostJson($path, array $data)
	{
		$http = $this->getHttpClient();
		$http->setUri($this->url . '/' . ltrim($path, '/'));

		$req = new HttpRequest();
		$req->setMethod(HttpRequest::METHOD_POST);
		$req->setUri($http->getUri());
		$req->headers()->addHeaderLine('Content-Type', 'application/json');
		$req->setContent(json_encode($data));

		$res = $http->send($req);

		if (!$res->isSuccess()) {
			throw new \RuntimeException("Server error : {$res->getStatusCode()}: {$res->getBody()}", $res->getStatusCode());
		}

		$body = $res->getBody();
		$data = json_decode($body, true);

		return $data;
	}


	/**
	 * @param string $path
	 * @param array $data
	 * @return array
	 * @throws \RuntimeException
	 */
	public function restPutJson($path, array $data)
	{
		$http = $this->getHttpClient();
		$http->setUri($this->url . '/' . ltrim($path, '/'));

		$req = new HttpRequest();
		$req->setMethod(HttpRequest::METHOD_PUT);
		$req->setUri($http->getUri());
		$req->headers()->addHeaderLine('Content-Type', 'application/json');
		$req->setContent(json_encode($data));

		$res = $http->send($req);

		if (!$res->isSuccess()) {
			throw new \RuntimeException("Server error : {$res->getStatusCode()}: {$res->getBody()}", $res->getStatusCode());
		}

		$body = $res->getBody();
		$data = json_decode($body, true);

		return $data;
	}


	/**
	 * @param string $id
	 * @return array
	 */
	public function getCategoryWords($id, $section, $category)
	{
		$locale = $this->getLangPackInfo()->getLangInfo($id, 'locale');

		$project = $this->getProjectName($section);

		$category_url = str_replace('_', '-', $category);

		try {
			$data = $this->restGet("project/$project/resource/$category_url/translation/$locale");
		} catch (\RuntimeException $e) {
			if ($e->getCode() == 404) {
				$this->getLogger()->logInfo("$id is missing $section.$category");
				return array();
			}

			throw $e;
		}

		$po = $data['content'];

		$stream = fopen('php://memory', 'w+');
		fwrite($stream, $po);
		rewind($stream);

		$store  = new \TempPoMsgStore();
		$parser = new \POParser($store);
		$parser->parseEntriesFromStream($stream);

		fclose($stream);

		$words = array();
		foreach ($store->read() as $line) {
			if (empty($line['msgid']) || empty($line['msgstr'])) {
				continue;
			}

			$words[trim($line['msgid'])] = trim($line['msgstr']);
		}

		return $words;
	}


	/**
	 * Update a source phrase with the PO file from $source_file
	 *
	 * @param string $section
	 * @param string $category
	 * @param string $source_file  If not specified, the default file from the default export dir will be used
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public function updateSourcePhrases($section, $category, $source_file = null)
	{
		$category_url = str_replace('_', '-', $category);
		$project_url  = $this->getProjectName($section);

		if (!$source_file) {
			$source_file = $this->getLangPackInfo()->getLangDir() . '/default/' . $section . '/export/' . $category . '.po';
		}

		if (!file_exists($source_file)) {
			$this->getLogger()->logDebug("$section.$category invalid source file: " . $source_file);
			throw new \InvalidArgumentException("PO file does not exist: " . $source_file);
		}

		$this->getLogger()->logDebug("$section.$category source file: $source_file");
		$this->getLogger()->logDebug("$section.$category project slug: $project_url");
		$this->getLogger()->logDebug("$section.$category resource slug: $category_url");

		#-------------------------
		# May need to init it instead of update
		#-------------------------

		$cat_exists = $this->restGet("project/$project_url/resource/$category_url", true);

		if (!$cat_exists) {
			$this->getLogger()->logDebug("$section.$category does not exist, creating it instead");
			return $this->restPostJson("/project/$project_url/resources/", array(
				'slug'                 => $category_url,
				'name'                 => ucfirst($category),
				'accept_translations'  => true,
				'content'              => file_get_contents($source_file),
				'i18n_type'            => 'PO'
			));
		}

		#-------------------------
		# Update it
		#-------------------------

		$this->getLogger()->logDebug("$section.$category already exists, updating it");

		return $this->restPutJson("/project/$project_url/resource/$category_url/content/", array(
			'content' => file_get_contents($source_file),
		));
	}


	/**
	 * Gets the project name used in transifex
	 *
	 * @param string $section
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function getProjectName($section)
	{
		switch ($section) {
			case 'user':  return 'dpuser';
			case 'agent': return 'dpagent';
			case 'admin': return 'dpadmin';
		}

		throw new \InvalidArgumentException("Invalid section $section");
	}
}