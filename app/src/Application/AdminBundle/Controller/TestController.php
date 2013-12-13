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
 * @subpackage AdminBundle
 */

namespace Application\AdminBundle\Controller;

use Application\DeskPRO\App;
use Orb\Util\Web;

class TestController extends AbstractController
{
	public function indexAction()
	{
		$page_html = '';

		if (isset($_GET['langdebug'])) {
			$page_html = $this->_langDebugPage();
		}

		return $this->render('AdminBundle:Main:test.html.twig', array(
			'page_html' => $page_html
		));
	}

	private function _langDebugPage()
	{
		if (isset($_GET['opt_enable'])) {
			Web::setCookie('dp_dev_langdebug', 'japanese', null, true, false, '/');
			return "Option enabled";
		} else if (isset($_GET['opt_disable'])) {
			Web::setCookie('dp_dev_langdebug', null, -1, true, false, '/');
			return "Option disabled";
		}

		$html = <<<HTML
<a href="?langdebug&amp;opt_enable">Enable Language Debug</a> &bull;
<a href="?langdebug&amp;opt_disable">Disable Language Debug</a>
HTML;

		return $html;
	}
}
