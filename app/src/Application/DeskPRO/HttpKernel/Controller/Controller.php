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
 * @category Controller
 */

namespace Application\DeskPRO\HttpKernel\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * The base controller
 */
abstract class Controller extends \Symfony\Bundle\FrameworkBundle\Controller\Controller
{
	/**
	 * @var \Application\DeskPRO\DependencyInjection\DeskproContainer
	 */
	protected $container;

	/**
	 * The request
	 * @var \Symfony\Component\HttpFoundation\Request
	 */
	public $request;

	/**
	 * The response
	 * @var \Symfony\Component\HttpFoundation\Response
	 */
	public $response;

	/**
	 * Event dispatcher
	 * @var \Symfony\Component\EventDispatcher\EventDispatcher
	 */
	protected $event_dispatcher;

	/**
	 * @var int
	 */
	public $request_type = HttpKernelInterface::MASTER_REQUEST;


	public function __construct(ContainerInterface $container)
	{
		$this->setContainer($container);

		$this->request           = $this->get('request');
		$this->response          = $this->get('response');
		$this->event_dispatcher  = $this->get('event_dispatcher');

		$self=$this;
		$this->event_dispatcher->addListener('DeskPRO_onControllerPreAction', function($ev) use ($self) {
			$self->request_type = $ev->get('request_type') ?: HttpKernelInterface::MASTER_REQUEST;
			$self->DeskPRO_onControllerPreAction($ev);
		});
		$this->event_dispatcher->addListener('DeskPRO_onControllerPostAction', function($ev) use ($self) {
			$self->DeskPRO_onControllerPostAction($ev);
		});

		$this->init();
	}


	/**
	 * @return \Application\DeskPRO\DependencyInjection\DeskproContainer
	 */
	public function getContainer()
	{
		return $this->container;
	}


	/**
	 * An empty callback function
	 */
	protected function init()
	{

	}



	public function DeskPRO_onControllerPreAction($event)
	{
		$ret = $this->preAction($event->get('action'), $event->get('arguments'));
		if ($ret) {
			$event->setResponse($ret);
		}
	}

	/**
	 * Called by the HttpKernel before a specific action is executed.
	 *
	 * If this method returns a response object, then that repsonse is used and
	 * the original action is NOT called. Any other return value is discarded.
	 *
	 * @param string $action      The action that will be called
	 * @param array  $arguments   The arguments that will be passed in
	 */
	public function preAction($action, $arguments = null)
	{

	}



	public function DeskPRO_onControllerPostAction($event)
	{
		$ret = $this->postAction($event->get('response'));
		if ($ret) {
			$event->setResponse($ret);
		}
	}

	/**
	 * Called by the HttpKernel after an action has been executed.
	 *
	 * If this method returns a response object, then that response is used
	 * and the original discarded. Any other return value will be discarded and result
	 * in the original response being used.
	 *
	 * @param Symfony\Component\HttpFoundation\Response $response
	 */
	public function postAction($response)
	{

	}



	/**
	 * Redirect to a named route.
	 *
	 * @param string $route
	 * @param array $parameters
	 * @param int $status
	 * @return Response
	 */
	public function redirectRoute($route, array $parameters = array(), $status = 302)
	{
		$url = $this->generateUrl($route, $parameters, true);
		return $this->redirect($url, $status);
	}



	/**
	 * Create a regular html response
	 *
	 * @param string $content
	 * @param int $status_code
	 * @return Response
	 */
	public function createResponse($content, $status_code = 200)
	{
		$response = $this->container->get('response');
		$response->headers->set('Content-Type', 'text/html');
		$response->setStatusCode($status_code);

		$response->setContent($content);

		return $response;
	}



	/**
	 * Create a JSON response.
	 *
	 * @param string $content
	 * @param int $status_code
	 * @return Response
	 */
	public function createJsonResponse($content, $status_code = 200)
	{
		$response = $this->container->get('response');

		// Because IE will sometimes prompt to download json when using iframe transport for ajax if we dont do this
		if ($this->request->isXmlHttpRequest() || isset($_SERVER['HTTP_ACCEPT']) && (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
			$response->headers->set('Content-Type', 'application/json');
		} else {
			$response->headers->set('Content-Type', 'text/plain');
		}

		$response->setStatusCode($status_code);

		if (is_array($content)) {
			$content = json_encode($content);
		}

		$response->setContent($content);

		return $response;
	}



	/**
	 * Create a JSONP response.
	 *
	 * @param string $content
	 * @param int $status_code
	 * @return Response
	 */
	public function createJsonpResponse($content, $status_code = 200, $callback_name = null)
	{
		if (!$callback_name) {
			$callback_name = preg_replace('#[^a-zA-Z0-9_]#', '', @$_GET['callback']);
		}
		if (!$callback_name) {
			$callback_name = 'jsonp_callback';
		}

		$response = $this->container->get('response');
		$response->headers->set('Content-Type', 'text/javascript');
		$response->setStatusCode($status_code);

		if (is_array($content)) {
			$content = json_encode($content);
		}

		$response->setContent("$callback_name($content);");

		return $response;
	}



	/**
	 * Render a template and create a JSON response with it.
	 *
	 * @param string $view
	 * @param array $parameters
	 * @param Response $response
	 * @return Response
	 */
	public function renderJson($view, array $parameters = array(), Response $response = null)
	{
		if ($response === null) {
			$response = $this->container->get('response');
			$response->headers->set('Content-Type', 'application/json');
			$response->setStatusCode(200);
		}
		$response = $this->container->get('templating')->renderResponse($view, $parameters, $response);

		return $response;
	}
}
