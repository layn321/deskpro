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

namespace Application\DeskPRO\Style;

use Application\DeskPRO\DependencyInjection\DeskproContainer;

class RefreshStylesheets
{
	/**
	 * Refreshes the saved CSS file when the template has changed, or a stylevar has changed.
	 *
	 * "Refreshing" just means deleting the CSS blob. The serve_file will re-create it as needed.
	 *
	 * @param \Application\DeskPRO\DependencyInjection\DeskproContainer $container
	 */
	public static function refresh(DeskproContainer $container)
	{
		$style = $container->getSystemService('style');

		if ($style) {
			if ($style->css_blob && $style->css_blob->getId()) {
				$container->getBlobStorage()->deleteBlobRecord($style->css_blob);
			}

			if ($style->css_blob_rtl && $style->css_blob_rtl->getId()) {
				$container->getBlobStorage()->deleteBlobRecord($style->css_blob_rtl);
			}

			$style->css_blob = null;
			$style->css_blob_rtl = null;
			$container->getDb()->update('styles', array(
				'css_blob_id' => null,
				'css_blob_rtl_id' => null,
				'css_updated' => date('Y-m-d H:i:s')
			), array('id' => $style->getId()));
		}
	}
}