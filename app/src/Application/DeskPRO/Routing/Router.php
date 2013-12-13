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

namespace Application\DeskPRO\Routing;

use Orb\Util\Strings;

class Router extends \Symfony\Bundle\FrameworkBundle\Routing\Router
{
	public function setOptions(array $options)
	{
		if (isset($options['debug'])) {
			$options['debug'] = false;
		}

		return parent::setOptions($options);
	}

	public function generateUrl($name, $parameters = array())
	{
		return $this->getGenerator()->generateUrl($name, $parameters);
	}


	/**
	 * Read the ID in a slug: 123-some-title will return 123
	 *
	 * If no ID couldbe found, then 0 is returned.
	 *
	 * @param $slug
	 * @return int
	 */
	public function getIdFromSlug($slug)
	{
		$id = Strings::extractRegexMatch('#^([0-9]+)#', $slug, 1);

		if (!$id) {
			return 0;
		}

		return (int)$id;
	}
}
