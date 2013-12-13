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
 * Orb
 *
 * @package Orb
 * @subpackage Mail
 */

namespace Cloud\Mail\Transport;

use Application\DeskPRO\App;

use Application\DeskPRO\Mail\QueueProcessor\Database as DatabaseQueueProcessor;
use Application\DeskPRO\Mail\Transport\DelegatingTransport as BaseDelegatingTransport;
use Orb\Mail\Transport\QueueTransport;
use Orb\Mail\Message;
use Orb\Util\Strings;
use Orb\Util\Util;
use Orb\Log\Logger;
use Orb\Log\Loggable;

class DelegatingTransport extends BaseDelegatingTransport
{
	/**
	 * @param string $from_address
	 * @param bool $get_backup_transport
	 * @return null|\Swift_MailTransport
	 */
	public function getTransportForFromAddress($from_address, $no_default = false)
	{
		// Always use our default transport from the @xxx.deskpro.com addresses
		$re_domain = preg_quote(DPC_SITE_DOMAIN, '#');
		if (preg_match("#@$re_domain$#", $from_address)) {
			$tr = App::getEntityRepository('DeskPRO:EmailTransport')->getDefaultTransport();
			if (!$tr) {
				$tr = new \Application\DeskPRO\Entity\EmailTransport();
				$tr->match_type = 'all';
				$tr->title = 'contact';
				$tr->transport_type = 'mail';
			}

			return $tr->getTransport();
		}

		return parent::getTransportForFromAddress($from_address, $no_default);
	}
}
