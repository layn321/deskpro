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
 * @subpackage Templating
 */

namespace Application\DeskPRO\Templating\Asset;

use Symfony\Component\Templating\Asset\UrlPackage as BaseUrlPackage;

use Application\DeskPRO\App;

class UrlPackage extends BaseUrlPackage
{
	public function __construct($baseUrls = array(), $version = null, $format = null)
    {
		$real = array();
		foreach ((array)$baseUrls as $burl) {
			if (!$burl OR $burl == 'CONFIG_HTTP' OR $burl == 'CONFIG_SSL') {
				$type = $burl;
				$burl = false;
				if (!$type) {
					$type = 'CONFIG_HTTP';
				}

				if ($type == 'CONFIG_SSL') {
					$burl = App::getConfig('static_ssl_path');
				}

				if (!$burl) {
					$burl = App::getConfig('static_path');
				}
			}

			if (!$burl AND App::has('request')) {
				$request = App::get('request');
				$burl = $request->getBasePath() . '/web';
			}

			if ($burl) {
				$real[] = $burl;
			}
		}

        parent::__construct($real, $version, $format);
    }
}
