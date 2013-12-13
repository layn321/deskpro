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
 * @subpackage UserBundle
 */

namespace Application\UserBundle\Controller\Helper\CommentsAdapter;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Orb\Util\Arrays;
use Orb\Util\Util;

class FacebookComments extends AbstractComments
{
	/**
	 * Get the HTML block for disqus templates
	 *
	 * @return string
	 */
	public function getHtml()
	{
		$html = App::get('templating')->render('UserBundle:Common:comments-facebook.html.twig', array(
			'entity'           => $this->entity,
			'entity_type'      => get_class($this->entity),
			'entity_basetype'  => Util::getBaseClassname($this->entity),

			'facebook_num_posts' => App::getSetting('core.facebook_comments_num_posts'),
			'facebook_admins'    => App::getSetting('core.facebook_comments_admins'),
			'page_permalink'     => $this->page_url
		));

		return $html;
	}
}
