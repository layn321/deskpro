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
 * @subpackage HttpKernel
 */

namespace Application\DeskPRO\HttpKernel;

use Application\DeskPRO\App;

use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Application\DeskPRO\HttpKernel\Event\PrePostEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * This HttpKernel changes how controllers are executed, adding features of pre and post action calls
 * that can be used to perform actions before or after an action, and can override the response object
 * in those cases.
 */
class HttpKernel extends \Symfony\Bundle\FrameworkBundle\HttpKernel
{
	protected $dispatcher;
	protected $resolver;
	protected $container;
    protected $esiSupport;

	public function __construct(EventDispatcherInterface $dispatcher, ContainerInterface $container, ControllerResolverInterface $controllerResolver)
    {
        $this->dispatcher = $dispatcher;
        $this->resolver = $controllerResolver;
        $this->container = $container;
    }

	public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $this->container->enterScope('request');
        $this->container->set('request', $request, 'request');

        try {
			 try {
				$response = $this->handleRaw($request, $type);
			} catch (\Exception $e) {
				if (false === $catch) {
					throw $e;
				}
				return $this->handleException($e, $request, $type);
			}
        } catch (\Exception $e) {
            $this->container->leaveScope('request');

            throw $e;
        }

        $this->container->leaveScope('request');

        return $response;
    }

	protected function handleRaw(Request $request, $type = self::MASTER_REQUEST)
	{
		$event = new GetResponseEvent($this, $request, $type);
		$this->dispatcher->dispatch(\Symfony\Component\HttpKernel\KernelEvents::REQUEST, $event);
		if ($event->hasResponse()) {
			return $this->filterResponse($event->getResponse(), $request, $type);
		}

		if (false === $controller = $this->resolver->getController($request)) {
			throw new NotFoundHttpException(sprintf('Unable to find the controller for path "%s". Maybe you forgot to add the matching route in your routing configuration?', $request->getPathInfo()));
		}

		$event = new FilterControllerEvent($this, $controller, $request, $type);
		$this->dispatcher->dispatch(\Symfony\Component\HttpKernel\KernelEvents::CONTROLLER, $event);
		$controller = $event->getController();
		$arguments = $this->resolver->getArguments($request, $controller);

		if (isset($controller[0]) AND $controller[0] instanceof \Application\DeskPRO\HttpKernel\Controller\Controller) {
			// Run pre event
			$event = new PrePostEvent(array(
				'request_type' => $type,
				'request' => $request,
				'controller' => $controller[0],
				'action' => $controller[1],
				'arguments' => $arguments
			));
			$this->dispatcher->dispatch('DeskPRO_onControllerPreAction', $event);
			$response = null;
			if ($event->hasResponse()) {
				$response = $event->getResponse();
			}

			// call controller if preaction didnt return a response
			if (!$response) {
				$response = call_user_func_array($controller, $arguments);
			}

			// Run post event
			$event = new PrePostEvent(array(
				'request_type' => $type,
				'request' => $request,
				'controller' => $controller[0],
				'action' => $controller[1],
				'arguments' => $arguments,
				'response' => $response
			));

			$this->dispatcher->dispatch('DeskPRO_onControllerPostAction', $event);
			if ($event->hasResponse()) {
				$response = $event->getResponse();
			}
		} else {
			$response = call_user_func_array($controller, $arguments);
		}

		if (!$response instanceof Response) {
			$event = new GetResponseForControllerResultEvent($this, $request, $type, $response);
			$this->dispatcher->dispatch(\Symfony\Component\HttpKernel\KernelEvents::VIEW, $event);
			if ($event->hasResponse()) {
				$response = $event->getResponse();
			}
			if (!$response instanceof Response) {
				throw new \LogicException(sprintf('The controller must return a response (%s given).', $this->varToString($response)));
			}
		}
		return $this->filterResponse($response, $request, $type);
	}

	/**
     * Forwards the request to another controller.
     *
     * @param  string  $controller The controller name (a string like BlogBundle:Post:index)
     * @param  array   $attributes An array of request attributes
     * @param  array   $query      An array of request query parameters
     *
     * @return Response A Response instance
     */
    public function forward($controller, array $attributes = array(), array $query = array())
    {
        $attributes['_controller'] = $controller;
        $subRequest = $this->container->get('request')->duplicate($query, null, $attributes);

        return $this->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }

    /**
     * Renders a Controller and returns the Response content.
     *
     * Note that this method generates an esi:include tag only when both the standalone
     * option is set to true and the request has ESI capability (@see Symfony\Component\HttpKernel\HttpCache\ESI).
     *
     * Available options:
     *
     *  * attributes: An array of request attributes (only when the first argument is a controller)
     *  * query: An array of request query parameters (only when the first argument is a controller)
     *  * ignore_errors: true to return an empty string in case of an error
     *  * alt: an alternative controller to execute in case of an error (can be a controller, a URI, or an array with the controller, the attributes, and the query arguments)
     *  * standalone: whether to generate an esi:include tag or not when ESI is supported
     *  * comment: a comment to add when returning an esi:include tag
     *
     * @param string $controller A controller name to execute (a string like BlogBundle:Post:index), or a relative URI
     * @param array  $options    An array of options
     *
     * @return string The Response content
     */
    public function render($controller, array $options = array())
    {
        $options = array_merge(array(
            'attributes'    => array(),
            'query'         => array(),
            'ignore_errors' => !$this->container->getParameter('kernel.debug'),
            'alt'           => array(),
            'standalone'    => false,
            'comment'       => '',
        ), $options);

        if (!is_array($options['alt'])) {
            $options['alt'] = array($options['alt']);
        }

        if (null === $this->esiSupport) {
            $this->esiSupport = $this->container->has('esi') && $this->container->get('esi')->hasSurrogateEsiCapability($this->container->get('request'));
        }

        if ($this->esiSupport && $options['standalone']) {
            $uri = $this->generateInternalUri($controller, $options['attributes'], $options['query']);

            $alt = '';
            if ($options['alt']) {
                $alt = $this->generateInternalUri($options['alt'][0], isset($options['alt'][1]) ? $options['alt'][1] : array(), isset($options['alt'][2]) ? $options['alt'][2] : array());
            }

            return $this->container->get('esi')->renderIncludeTag($uri, $alt, $options['ignore_errors'], $options['comment']);
        }

        $request = $this->container->get('request');

        // controller or URI?
        if (0 === strpos($controller, '/')) {
            $subRequest = Request::create($controller, 'get', array(), $request->cookies->all(), array(), $request->server->all());
            $subRequest->setSession($request->getSession());
        } else {
            $options['attributes']['_controller'] = $controller;
            $options['attributes']['_format'] = $request->getRequestFormat();
            $subRequest = $request->duplicate($options['query'], null, $options['attributes']);
        }

        try {
            $response = $this->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);

            if (!$response->isSuccessful()) {
                throw new \RuntimeException(sprintf('Error when rendering "%s" (Status code is %s).', $request->getUri(), $response->getStatusCode()));
            }

            return $response->getContent();
        } catch (\Exception $e) {
            if ($options['alt']) {
                $alt = $options['alt'];
                unset($options['alt']);
                $options['attributes'] = isset($alt[1]) ? $alt[1] : array();
                $options['query'] = isset($alt[2]) ? $alt[2] : array();

                return $this->render($alt[0], $options);
            }

            if (!$options['ignore_errors']) {
                throw $e;
            }
        }
    }

    /**
     * Generates an internal URI for a given controller.
     *
     * This method uses the "_internal" route, which should be available.
     *
     * @param string $controller A controller name to execute (a string like BlogBundle:Post:index), or a relative URI
     * @param array  $attributes An array of request attributes
     * @param array  $query      An array of request query parameters
     *
     * @return string An internal URI
     */
    public function generateInternalUri($controller, array $attributes = array(), array $query = array())
    {
        if (0 === strpos($controller, '/')) {
            return $controller;
        }

        $uri = $this->container->get('router')->generate('_internal', array(
            'controller' => $controller,
            'path'       => $attributes ? http_build_query($attributes) : 'none',
            '_format'    => $this->container->get('request')->getRequestFormat(),
        ), true);

        if ($query) {
            $uri = $uri.'?'.http_build_query($query);
        }

        return $uri;
    }


	private function filterResponse(Response $response, Request $request, $type)
    {
        $event = new FilterResponseEvent($this, $request, $type, $response);
        $this->dispatcher->dispatch(\Symfony\Component\HttpKernel\KernelEvents::RESPONSE, $event);
        return $event->getResponse();
    }

    private function handleException(\Exception $e, $request, $type)
    {
        $event = new GetResponseForExceptionEvent($this, $request, $type, $e);
        $this->dispatcher->dispatch(\Symfony\Component\HttpKernel\KernelEvents::EXCEPTION, $event);
        if (!$event->hasResponse()) {
            throw $e;
        }
        return $this->filterResponse($event->getResponse(), $request, $type);
    }
    private function varToString($var)
    {
        if (is_object($var)) {
            return sprintf('[object](%s)', get_class($var));
        }
        if (is_array($var)) {
            $a = array();
            foreach ($var as $k => $v) {
                $a[] = sprintf('%s => %s', $k, $this->varToString($v));
            }
            return sprintf("[array](%s)", implode(', ', $a));
        }
        if (is_resource($var)) {
            return '[resource]';
        }
        return str_replace("\n", '', var_export((string) $var, true));
    }
}
