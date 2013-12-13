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

namespace Application\DeskPRO\HttpKernel\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\ContainerAware;
use Symfony\Bundle\FrameworkBundle\Controller\ContainerAwareInterface;


/**
 * This controller resolver changes instantiation of controllers to pass in the container
 * to the constructor.
 */
class ControllerResolver extends \Symfony\Bundle\FrameworkBundle\Controller\ControllerResolver
{
    protected function createController($controller)
    {
        if (false === strpos($controller, '::')) {
            $count = substr_count($controller, ':');
            if (2 == $count) {
                                $controller = $this->parser->parse($controller);
            } elseif (1 == $count) {
                                list($service, $method) = explode(':', $controller);
                return array($this->container->get($service), $method);
            } else {
                throw new \LogicException(sprintf('Unable to parse the controller name "%s".', $controller));
            }
        }

        list($class, $method) = explode('::', $controller);

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

		if (is_subclass_of($class, 'Application\\DeskPRO\\HttpKernel\\Controller\\Controller')) {
			$controller = new $class($this->container);
		} else {
			$controller = new $class();
			if (is_subclass_of($class, 'Symfony\\Component\\DependencyInjection\\ContainerAwareInterface')) {
			//if ($controller instanceof ContainerAwareInterface OR $controller instanceof ContainerAware) {
				$controller->setContainer($this->container);
			} else {
				die($class);
			}
		}

        return array($controller, $method);
    }
}
