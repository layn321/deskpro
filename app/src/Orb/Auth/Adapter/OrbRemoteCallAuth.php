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
 * Orb
 *
 * @package Orb
 * @category Auth
 */

namespace Orb\Auth\Adapter;

use \Orb\Auth\Result;

/**
 * OrbRemoteCallAuth is a very simple protocol where the software sends a POST request to a remote web
 * with form data (credentials) and the remote page must return a JSON formatted message.
 *
 * Note that this protocol does not care about security, it does not encrypt transmission of messages.
 * If you require security you should make sure the remote URL is HTTPS.
 *
 * The following data is sent to the remote server:
 * - orba_consumer_key: A key that identifies this consumer
 * - orba_data[xxx]: An array of form values (like credentials) to pass to the service
 * - orba_with_userinfo: When is 1, the service should return `userinfo` as well. Otherwise, simple
 * success and identity are needed. The difference is first login we want info to configure a user,
 * then subsuent logins we may only need to confirm their password.
 *
 * The following data is returned on success (JSON encoded):
 * - is_success: 1
 * - identity: Any unique identifier for this user (user ID for example)
 * - userinfo: (object of user data, only set if orba_with_userinfo was 1)
 *
 * `userinfo` if possible should have keys named such (standard naming for standard fields) so generic components
 * can use your service with no interaction:
 * - orba_identity: A unique identity of this user on your system such as numeric ID
 * - orba_username: A preferred username, or an array of preferred usernames in order of priority
 * - orba_email: A preferred email address, or an array of preferred email addresses in order of priority
 * - orba_name: The users real name
 *
 * The following data is returned on failure (JSON encoded):
 * - is_error: 1
 * - error_code: Any error code. Usually numeric, but doesn't have to be
 * - error_message: An English explanation of the error
 */
class OrbRemoteCallAuth implements AdapterInterface
{
	const ERR_SERVICE_ERR = -11;

	/**
	 * The key that identifies this service
	 * @var string
	 */
	protected $consumer_key = null;


	/**
	 * The URL to call
	 * @var string
	 */
	protected $service_url = null;

	/**
	 * HTTP client
	 * @var \Zend\Http\Client
	 */
	protected $http;

	/**
	 * Form data
	 * @var array
	 */
	protected $form_data = array();

	/**
	 * Do we request userinfo as well?
	 * @var int
	 */
	protected $with_userinfo = 1;

	/**
	 * $consumer_key is the key that identifies this consumer. The remote service must know about us.
	 *
	 * $service_url is the URL of the service. Note that since the protocol does not include
	 * any kind of encryption, it's recommended you use an HTTPS URL.
	 *
	 * @param string $consumer_key
	 * @param string $service_url
	 */
	public function __construct($consumer_key, $service_url)
	{
		$this->consumer_key = $consumer_key;
		$this->service_url = $service_url;
	}


	
	/**
	 * Set the form data we'll pass to the remote provider.
	 * 
	 * @param array $form_data 
	 */
	public function setFormData(array $form_data)
	{
		$this->form_data = $form_data;
	}



	/**
	 * Set if we want userinfo or not
	 * 
	 * @param bool $yes_or_no
	 */
	public function setWithUserinfo($yes_or_no)
	{
		$this->with_userinfo = (int)((bool)$yes_or_no);
	}


	/**
	 * Authenticate a user.
	 *
	 * @return
	 */
	public function authenticate()
	{
		$http = $this->getHttpClient();
		$http->resetParameters();

		$http->setUri($this->service_url);

		$http->setParameterPost('orba_consumer_key', $this->consumer_key);

		if ($this->with_userinfo) {
			$http->setParameterGet('orba_with_userinfo', 1);
		}

		foreach ($this->form_data as $k=>$v) {
			$http->setParameterPost('orba_data['.$k.']', $v);
		}

		$http_result = $http->request(\Zend\Http\Client::POST);

		$data = @json_decode($http_result->getBody(), true);
		if (!$userdata) {
			throw \UnexpectedValueException('Invalid JSON returned from service');
		}

		if (isset($data['is_error'])) {
			return new Result(Result::FAILURE, null, array('error_code' => self::ERR_SERVICE_ERR, 'error_message' => 'Service reported error', 'service_data' => $data));
		}

		$identity = new \Orb\Auth\Identity($data['identity'], isset($userdata['userinfo']) ? $userdata : array());
		$result = new Result(Result::SUCCESS, $identity);

		return $result;
	}



	/**
	 * Set a custom HTTP client. If one is not set, a default client will be used automatically.
	 * 
	 * @param \Zend\Http\Client $http
	 */
	public function setHttpClient(\Zend\Http\Client $http)
	{
		$this->http = $http;
	}


	/**
	 * Get the HTTP client.
	 *
	 * @return \Zend\Http\Client
	 */
	public function getHttpClient()
	{
		if ($this->http !== null) return $this->http;

		$this->http = new \Zend\Http\Client();

		return $this->http;
	}
}
