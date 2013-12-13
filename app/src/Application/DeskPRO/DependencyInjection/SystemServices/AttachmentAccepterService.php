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
 * @category DependencyInjection
 */

namespace Application\DeskPRO\DependencyInjection\SystemServices;

use Application\DeskPRO\DependencyInjection\DeskproContainer;
use Application\DeskPRO\Attachments\AcceptAttachment;

use Application\DeskPRO\App;

use Orb\Util\Env as EnvUtil;

class AttachmentAccepterService
{
	public static function create(DeskproContainer $container)
	{
		$accepter = new AcceptAttachment(
			$container->getEm(),
			$container->getBlobStorage()
		);

		$effective_max_size = EnvUtil::getEffectiveMaxUploadSize();

		foreach (array('agent', 'user') as $type) {
			$res = new \Application\DeskPRO\Attachments\RestrictionSet();

			$max_size  = $container->getSetting('core.attach_'.$type.'_maxsize');
			$max_size  = min($effective_max_size, $max_size);

			$must_exts = $container->getSetting('core.attach_'.$type.'_must_exts');
			$not_exts  = $container->getSetting('core.attach_'.$type.'_not_exts');

			if ($must_exts) {
				$must_exts = explode(',', strtolower($must_exts));
				array_walk($must_exts, 'trim');
			} else {
				$must_exts = null;
			}

			if ($not_exts) {
				$not_exts = explode(',', strtolower($not_exts));
				array_walk($not_exts, 'trim');
			} else {
				$not_exts = null;
			}

			$res->setMaxSize($max_size)->setAllowedExts($must_exts)->setDisallowedExts($not_exts);

			$accepter->addRestrictionSet($type, $res);
		}

		return $accepter;
	}
}
