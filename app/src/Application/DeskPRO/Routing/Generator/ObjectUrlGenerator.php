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
 * @subpackage Util
 */

namespace Application\DeskPRO\Routing\Generator;

use Symfony\Component\Routing\Generator\UrlGenerator as BaseUrlGenerator;

use Orb\Util\Strings;
use Orb\Util\Arrays;

/**
 * Gets the URL to the page for a resource given a context
 */
class ObjectUrlGenerator
{
	const CONTEXT_AGENT = 'agent';
	const CONTEXT_USER  = 'user';

	protected $generator;

	public function __construct(BaseUrlGenerator $generator)
	{
		$this->generator = $generator;
	}

	public function generateObjectUrl($object, array $params = array(), $context = null)
	{
		$typename = get_class($object);

		if ($object instanceof \Application\DeskPRO\Entity\Article) {
			if ($context == 'agent') {
				$params['article_id'] = $object['id'];
				return $this->generator->generate('agent_kb_article', $params);
			}
			return $object->getUrlSlug();
		} elseif ($object instanceof \Application\DeskPRO\Entity\Download) {
			if ($context == 'agent') {
				$params['download_id'] = $object['id'];
				return $this->generator->generate('agent_downloads_view', $params);
			}
			return $object->getUrlSlug();
		} elseif ($object instanceof \Application\DeskPRO\Entity\Feedback) {
			if ($context == 'agent') {
				$params['feedback_id'] = $object['id'];
				return $this->generator->generate('agent_feedback_view', $params);
			}
			return $object->getUrlSlug();
		} elseif ($object instanceof \Application\DeskPRO\Entity\News) {
			if ($context == 'agent') {
				$params['news_id'] = $object['id'];
				return $this->generator->generate('agent_news_view', $params);
			}
			return $object->getUrlSlug();
		} elseif ($object instanceof \Application\DeskPRO\Entity\Person) {
			if ($context == 'agent') {
				$params['person_id'] = $object['id'];
				return $this->generator->generate('agent_people_view', $params);
			}
		} elseif ($object instanceof \Application\DeskPRO\Entity\Ticket) {
			if ($context == 'agent') {
				$params['ticket_id'] = $object['id'];
				return $this->generator->generate('agent_ticket_view', $params);
			}
		}

		return null;
	}
}
