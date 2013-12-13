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
 * @subpackage
 */

namespace Application\DevBundle\Controller;

use Symfony\Component\Finder\Finder;

class EmailDecodeTestController extends \Application\DeskPRO\HttpKernel\Controller\Controller
{
	public function indexAction()
	{
		$email_sources = array();
		$email_sources_dir = DP_ROOT.'/src/Application/DevBundle/Resources/email-sources';
		foreach (Finder::create()->files()->name('*.txt')->in($email_sources_dir) as $f) {
			/** @var $f \SplFileInfo */
			$email_sources[$f->getFilename()] = $f->getRealPath();
		}

		return $this->render('DevBundle:EmailDecodeTest:index.html.php', array(
			'email_sources_dir' => $email_sources_dir,
			'email_sources'     => $email_sources
		));
	}

	public function runAction()
	{
		if ($_REQUEST['source_from'] == 'input') {
			$email_source = $_REQUEST['email_source'];
		} else {
			$email_source_file = $_REQUEST['source_from'];
			if (!is_file($email_source_file)) {
				die("Source file does not exist: " . $email_source_file);
			}

			$email_source = file_get_contents($email_source_file);
		}

		if (!$email_source) {
			die('Blank email source');
		}

		#------------------------------
		# Decode the email
		#------------------------------

		$reader = new \Application\DeskPRO\EmailGateway\Reader\EzcReader();
		$reader->setRawSource($email_source);

		#------------------------------
		# Cut the email
		#------------------------------

		if ($reader->getBodyHtml()->getBodyUtf8()) {
			$body = $reader->getBodyHtml()->getBodyUtf8();
			$body_is_html = true;
		} else {
			$body = $reader->getBodyText()->getBodyUtf8();
			$body_is_html = false;
		}

		$body_clean = $body;
		if ($body_is_html) {
			$body_clean = $this->container->getIn()->getCleaner()->clean($body, 'html_email');
			$body_clean = $this->trimHtmlWhitespace($body_clean);
		}

		$cutter_type = $_REQUEST['cutter_type'];
		$cutter_data = null;

		if ($cutter_type) {
			$generic_cutter = new \Application\DeskPRO\EmailGateway\Cutter\Def\Generic();
			if ($cutter_type == 'normal') {
				$cutter_data['cut_quote_block'] = $generic_cutter->cutQuoteBlock($body_clean, $body_is_html);
				if ($body_is_html) {
					$cutter_data['cut_quote_block'] = $this->container->getIn()->getCleaner()->clean($cutter_data['cut_quote_block'], 'html_email');
				}
			} else {
				$body = $reader->getBodyText()->getBodyUtf8();
				$body_is_html = false;
				if (!$body) {
					$body = strip_tags($reader->getBodyHtml()->getBodyUtf8());
					$body_is_html = true;
				}

				$cutter = new \Application\DeskPRO\EmailGateway\Cutter\ForwardCutter($body_clean, false, $generic_cutter);

				$cutter_data = $cutter->getData();
			}
		}

		return $this->render('DevBundle:EmailDecodeTest:run.html.php', array(
			'email_source' => $email_source,
			'cutter_type'  => $cutter_type,
			'cutter_data'  => $cutter_data,
			'reader'       => $reader,
			'body_is_html' => $body_is_html
		));
	}

	public function trimHtmlWhitespace($html)
	{
		return \Orb\Util\Strings::trimHtmlAdvanced($html);

		return $html;
	}
}