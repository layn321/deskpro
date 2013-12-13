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
 * @subpackage ApiBundle
 */

namespace Application\ApiBundle\Controller;

use Application\DeskPRO\App;

/**
 * Base API controller.
 */
abstract class AbstractController extends \Application\DeskPRO\Controller\AbstractController
{
	/**
	 * The API key making this request
	 *
	 * @var \Application\DeskPRO\Entity\ApiKey|null
	 */
	public $apikey;

	/**
	 * @var \Application\DeskPRO\Entity\ApiToken|null
	 */
	public $api_token;

	/**
	 * The user context (user making the request, or the one the API key says to use)
	 * @var \Application\DeskPRO\Entity\Person
	 */
	public $person;

	/**
	 * Entity manager
	 * @var \Doctrine\ORM\EntityManager
	 */
	public $em;

	/**
	 * Plain database connection for raw queries
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	public $db;

	/**
	 * Input reader
	 * @var \Orb\Input\Reader\Reader
	 */
	public $in;

	/**
	 * A generic value cleaner
	 * @var \Orb\Input\Cleaner\Cleaner
	 */
	public $cleaner;

	/**
	 * Shared template vars
	 * @var \ArrayObject
	 */
	public $tplvars;

	/**
	 * @var \Application\DeskPRO\Templating\Engine
	 */
	public $tpl;

	/**
	 * Fetch settings
	 * @var \Application\DeskPRO\Settings\Settings
	 */
	public $settings;

	public $rate_info = null;



	protected function init()
	{
		$this->em       = $this->get('doctrine.orm.entity_manager');
		$this->db       = $this->get('database_connection');
		$this->in       = $this->get('deskpro.core.input_reader');
		$this->cleaner  = $this->get('deskpro.core.input_cleaner');
		$this->settings = $this->get('deskpro.core.settings');

		$this->apikey = $this->get('deskpro.api.request_key');
		$person = null;

		if ($this->apikey) {
			$person = false;

			if (!$this->apikey->person) {
				$as_agent_id = $this->getRequest()->headers->get('X-DeskPRO-Agent-ID', null, true);
				if (!$as_agent_id) {
					$as_agent_id = isset($_REQUEST['DP-AGENT-ID']) ? $_REQUEST['DP-AGENT-ID'] : 0;
				}
				$as_agent_id = intval($as_agent_id);

				$agent = $this->em->getRepository('DeskPRO:Person')->find($as_agent_id);
				if ($agent && $agent->is_agent) {
					$person = $agent;
				}
			} else {
				$person = $this->apikey->person;
			}
		}

		if (!$this->apikey) {
			$this->api_token = $this->get('deskpro.api.request_token');
			if ($this->api_token) {
				$person = $this->api_token->person;
			}
		}

		if ($person && $person->is_agent && !$person->is_deleted && !$person->is_disabled) {
			App::setCurrentPerson($person);

			$this->person = $person;

			$this->person->loadHelper('Agent');
			$this->person->loadHelper('AgentTeam');
			$this->person->loadHelper('AgentPermissions');
			$this->person->loadHelper('PermissionsManager');
			$this->person->loadHelper('HelpMessages');
			$this->person->loadHelper('AgentPrefs');
		}
	}



	/**
	 * Always require a valid API key.
	 */
	public function preAction($action, $arguments = null)
	{
		if (!$this->apikey && !$this->api_token) {
			$response = $this->createApiErrorResponse('invalid_auth', 'Please provide a valid API key or token', 401);
			$response->headers->add(array(
				'WWW-Authenticate' => 'Basic realm="API"'
			));

			return $response;
		}

		if ($this->api_token && $this->api_token->date_expires && $this->api_token->date_expires->getTimestamp() < time()) {
			return $this->createApiErrorResponse('token_expired', 'Your API token has expired. Please login again.', 403);
		}

		if (!$this->person) {
			return $this->createApiErrorResponse('invalid_person', 'Please provide a valid agent for this request', 403);
		}

		if (App::getSetting('core.api_rate_limit')) {
			$error = $this->_checkRateLimit($action, $arguments);
			if ($error) {
				return $error;
			}
			$this->_updateRateLimit($action, $arguments);
		}
	}

	protected function _checkRateLimit($action, $arguments = null)
	{
		if ($this->apikey) {
			$this->rate_info = $this->em->getRepository('DeskPRO:ApiKey')->getRateLimitInfo($this->apikey);
		} else {
			$this->rate_info = $this->em->getRepository('DeskPRO:ApiToken')->getRateLimitInfo($this->api_token);
		}

		if ($this->rate_info['hits'] >= App::getSetting('core.api_rate_limit')) {
			return $this->createApiErrorResponse('rate_limit_exceeded', 'Rate Limit Exceeded', 429);
		}

		return null;
	}

	protected function _updateRateLimit($action, $arguments = null)
	{
		if ($this->apikey) {
			$this->em->getRepository('DeskPRO:ApiKey')->updateRateLimit($this->apikey);
		} else {
			$this->em->getRepository('DeskPRO:ApiToken')->updateRateLimit($this->api_token);
		}

		if ($this->rate_info) {
			$this->rate_info['hits']++;
		}
	}



	/**
	 * Create an API error response
	 *
	 * @param string $error_code The short error code
	 * @param string $error_message The error message
	 * @param int $status The HTTP status to return
	 * @return Response
	 */
	public function createApiErrorResponse($error_code, $error_message, $status = 400)
	{
		return $this->createApiResponse(array(
			'error_code' => $error_code,
			'error_message' => $error_message
		), $status);
	}

	public function createApiMultipleErrorResponse(array $errors, $status = 400)
	{
		return $this->createApiResponse(array(
			'error_code' => 'multiple',
			'errors' => $errors
		), $status);
	}



	/**
	 * Create an API response.
	 *
	 * @param array $data
	 * @param int $status
	 * @return Response
	 */
	public function createApiResponse(array $data, $status = 200)
	{
		$_SERVER['HTTP_ACCEPT'] = 'application/json';

		$response = $this->createJsonResponse($data, $status);

		if ($this->rate_info) {
			$response->headers->set('X-RateLimit-Limit', App::getSetting('core.api_rate_limit'));
			$response->headers->set('X-RateLimit-Remaining', max(0, App::getSetting('core.api_rate_limit') - $this->rate_info['hits']));
			$response->headers->set('X-RateLimit-Reset', $this->rate_info['reset_stamp']);
		}

		return $response;
	}



	/**
	 * Creates an API success response
	 *
	 * @param array $extra
	 * @param integer $status
	 *
	 * @return Response
	 */
	public function createSuccessResponse(array $extra = array(), $status = 200)
	{
		return $this->createApiResponse(array('success' => true) + $extra, $status);
	}



	public function createApiCreateResponse(array $data, $url)
	{
		$response = $this->createApiResponse($data, 201);
		$response->headers->add(array('Location' => $url));

		return $response;
	}



	public function getApiData($input, $deep = true)
	{
		if (is_array($input) || $input instanceof \Traversable) {
			$output = array();
			foreach ($input AS $key => $value) {
				if ($value instanceof \Application\DeskPRO\Domain\DomainObject) {
					$output[$key] = $value->toApiData(false, $deep);
				}
			}

			return $output;
		} else if ($input instanceof \Application\DeskPRO\Domain\DomainObject) {
			return $input->toApiData(true, $deep);
		}

		return false;
	}



	public function getApiSearchResult($type, array $terms, array $extra, $cache_id, \Application\DeskPRO\Searcher\SearcherAbstract $searcher)
	{
		if ($cache_id) {
			$result_cache = $this->em->createQuery('
				SELECT r
				FROM DeskPRO:ResultCache r
				WHERE r.id = ?0
			')->setParameters(array($cache_id))->getOneOrNullResult();
			if ($result_cache) {
				if ($result_cache->person->id != $this->person->id || $result_cache->results_type !== $type) {
					$result_cache = null;
				}
			}
		} else {
			$result_cache = null;
		}

		if (!$result_cache) {
			$searcher->setPerson($this->person);
			foreach ($terms AS $term) {
				$searcher->addTerm($term['type'], $term['op'], $term['options']);
			}
			if (isset($extra['order_by'])) {
				$searcher->setOrderByCode($extra['order_by']);
			}

			$results = $searcher->getMatches();

			$result_cache = new \Application\DeskPRO\Entity\ResultCache();
			$result_cache->person = $this->person;
			$result_cache->results = $results;
			$result_cache->results_type = $type;
			$result_cache->criteria = $terms;
			$result_cache->num_results = count($results);
			foreach ($extra AS $key => $value) {
				$result_cache->setExtraData($key, $value);
			}

			$this->em->persist($result_cache);
			$this->em->flush();
		}

		return $result_cache;
	}


	public function getCustomFieldInput($input_name = 'field')
	{
		$custom_fields = $this->request->request->get($input_name, array());
		if (!is_array($custom_fields) || empty($custom_fields)) {
			$custom_fields = $this->request->query->get($input_name, array());
			if (!is_array($custom_fields) || empty($custom_fields)) {
				return array();
			}
		}

		$output = array();
		foreach ($custom_fields AS $key => $value) {
			if (is_int($key)) {
				$output['field_' . $key] = $value;
			} else if (preg_match('/^field_\d+/', $key)) {
				$output[$key] = $value;
			}
		}

		return $output;
	}

	protected function _sendCommentApprovedNotification($comment)
	{
		if ($comment->getUserEmail()) {
			$message = $this->container->getMailer()->createMessage();
			if ($comment->person) {
				$message->setTo($comment->person->getPrimaryEmailAddress(), $comment->person->getDisplayName());
			} else {
				$message->setTo($comment->getUserEmail());
			}
			$message->setTemplate('DeskPRO:emails_user:comment-approved.html.twig', array(
				'comment' => $comment
			));
			$message->enableQueueHint();
			$this->container->getMailer()->send($message);
		}

		// For feedback we also notify everyone involved
		if ($comment instanceof \Application\DeskPRO\Entity\FeedbackComment) {
			$commenting = new \Application\DeskPRO\Feedback\FeedbackCommenting($this->container, $this->person);
			$commenting->newCommentNotify($comment);
		}
	}

	protected function _sendCommentDeletedNotification($comment)
	{
		if ($comment->getUserEmail()) {
			$message = $this->container->getMailer()->createMessage();
			if ($comment->person) {
				$message->setTo($comment->person->getPrimaryEmailAddress(), $comment->person->getDisplayName());
			} else {
				$message->setTo($comment->getUserEmail());
			}
			$message->setTemplate('DeskPRO:emails_user:comment-deleted.html.twig', array(
				'comment' => $comment
			));
			$message->enableQueueHint();
			$this->container->getMailer()->send($message);
		}
	}
}
