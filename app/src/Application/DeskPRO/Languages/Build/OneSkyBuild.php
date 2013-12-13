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

use Guzzle\Http\Client as HttpClient;

class OneSkyBuild extends AbstractBuild
{
	const API_URL = 'http://api.oneskyapp.com/2';
	const API_SECURE_URL = 'https://api.oneskyapp.com/2';

	/**
	 * @var string
	 */
	private $api_url;

	/**
	 * @var string
	 */
	private $api_key;

	/**
	 * @var string
	 */
	private $secret_key;

	/**
	 * @var \Guzzle\Http\Client
	 */
	private $http_client;

	/**
	 * @param string $api_key     The API key (called "public key" in settings)
	 * @param string $secret_key  The secret key
	 */
	public function __construct($api_key, $secret_key)
	{
		$this->api_url = self::API_URL;
		$this->api_key = $api_key;
		$this->secret_key = $secret_key;
	}


	/**
	 * Enable secure API usage over HTTPS
	 */
	public function enableSecureApiUrl()
	{
		$this->api_url = self::API_SECURE_URL;
	}


	/**
	 * Set a specific API url
	 *
	 * @param string $url
	 */
	public function setApiUrl($url)
	{
		$this->api_url = $url;
	}


	/**
	 * Get the API url
	 *
	 * @return string
	 */
	public function getApiUrl()
	{
		return $this->api_url;
	}


	/**
	 * @param string $id
	 * @param $section
	 * @param $category
	 * @return array
	 * @throws \Exception|\RuntimeException
	 */
	public function getCategoryWords($id, $section, $category)
	{
		$locale = $this->getLangPackInfo()->getLangInfo($id, 'locale');

		$platform_id = $this->getPlatformId($section);
		$tag = $category . '.php';

		try {
			$words = $this->restGet('string/download', array(
				'locale'      => $locale,
				'platform-id' => $platform_id,
				'format'      => 'RESJSON',
				'tag'         => $tag,
			));

			if (isset($words['response']) && isset($words['error'])) {
				throw new \Exception("Error: " . $words['error'], strpos($words['error'], 'does not exist') !== false ? 404 : 200);
			}

		} catch (\Exception $e) {
			if ($e->getCode() == 404) {
				$this->getLogger()->logInfo("$id is missing $section.$category");
				return array();
			}

			throw $e;
		}

		return $words;
	}


	/**
	 * Update the tracked project on OneSky with the phrases from the source file
	 *
	 * @param string $section
	 * @param string $category
	 * @param string $source_file  If not specified, the default file is the default lang file for the category
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public function updateSourcePhrases($section, $category, $source_file = null)
	{
		$tag = "$category.php";
		$platform_id  = $this->getPlatformId($section);

		if (!$source_file) {
			$source_file = $this->getLangPackInfo()->getLangDir() . '/default/'.$section.'/'.$category.'.php';
		}

		if (!file_exists($source_file)) {
			$this->getLogger()->logDebug("$section.$category invalid source file: " . $source_file);
			throw new \InvalidArgumentException("Source file does not exist: " . $source_file);
		}

		$this->getLogger()->logDebug("$section.$category source file: $source_file");
		$this->getLogger()->logDebug("$section.$category platform id: $platform_id");
		$this->getLogger()->logDebug("$section.$category tag: $tag");

		#-------------------------
		# Generate post data for source phrases
		#-------------------------

		$phrases = include($source_file);
		$source_phrases = array();

		foreach ($phrases as $phrase_id => $phrasetext) {
			$source_phrases[] = array(
				'string' => $phrasetext,
				'string-key' => $phrase_id
			);
		}

		#-------------------------
		# Update it
		#-------------------------

		// First send along the full file of strings
		$post_data = array();
		$post_data['platform-id'] = $platform_id;
		$post_data['tag'] = $tag;
		$post_data['is-allow-update'] = 1;
		$post_data['input'] = $source_phrases;

		$input_result = $this->restPost('string/input', $post_data);

		#-------------------------
		# Need to also delete old phrases that are no longer used
		#-------------------------

		$got_phrases = $this->getCategoryWords('default', $section, $category);
		if ($got_phrases) {
			$delete_phrase_ids = array();

			foreach ($got_phrases as $phrase_id => $phrasetext) {
				if (!isset($phrases[$phrase_id])) {
					$delete_phrase_ids[] = $phrase_id;
				}
			}

			if ($delete_phrase_ids) {
				$this->getLogger()->logDebug("$section.$category removing old phrases: " . implode(', ', $delete_phrase_ids));

				$post_data = array();
				$post_data['platform-id'] = $platform_id;
				$post_data['to-delete'] = array();

				foreach ($delete_phrase_ids as $phrase_id) {
					$post_data['to-delete'][] = array(
						'string-key' => $phrase_id
					);
				}

				$this->restPost('string/delete', $post_data);
			}
		}

		return $input_result;
	}


	/**
	 * @param string $path
	 * @return array
	 * @throws \RuntimeException
	 */
	public function restGet($path, array $vars = array())
	{
		$vars['api-key']   = $this->api_key;
		$vars['timestamp'] = time();
		$vars['dev-hash']  = md5($vars['timestamp'] . $this->secret_key);

		$request = $this->getHttpClient()->get($path);
		$request->getQuery()->merge($vars);

		$response = $request->send();

		$body = $response->getBody(true);
		$data = json_decode($body, true);

		return $data;
	}

	/**
	 * @param string $path
	 * @return array
	 * @throws \RuntimeException
	 */
	public function restPost($path, array $post_vars = array())
	{
		$vars = array();
		$vars['api-key']   = $this->api_key;
		$vars['timestamp'] = time();
		$vars['dev-hash']  = md5($vars['timestamp'] . $this->secret_key);

		$request = $this->getHttpClient()->post($path);
		$request->getQuery()->merge($vars);

		if ($post_vars) {

			if ($path == 'string/input') {
				$post_vars['input'] = json_encode($post_vars['input']);
			}

			if ($path == 'string/delete') {
				$post_vars['to-delete'] = json_encode($post_vars['to-delete']);
			}

			$request->addPostFields($post_vars);
		}

		$response = $request->send();

		$body = $response->getBody(true);
		$data = json_decode($body, true);

		return $data;
	}


	/**
	 * Get the OneSky platform ID for the section.
	 *
	 * @param string $section
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function getPlatformId($section)
	{
		switch ($section) {
			case 'user':  return '11467';
			case 'agent': return '11470';
			case 'admin': return '0';
		}

		throw new \InvalidArgumentException("Invalid platform $section");
	}


	/**
	 * @return \Guzzle\Http\Client
	 */
	protected function getHttpClient()
	{
		if ($this->http_client !== null) {
			return $this->http_client;
		}

		$this->http_client = new HttpClient($this->api_url, array(
			'ssl.certificate_authority' => false
		));
		return $this->http_client;
	}
}