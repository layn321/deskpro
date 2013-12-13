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
 * @subpackage ApiBundle
 */

namespace Application\ApiBundle\Controller;

/**
 * A remote resource pings this script to notify the system that some record was updated.
 *
 * The resource and record are added to a worker queue, and then it will be processed
 * later (hopefully in a few seconds).
 */
class ResourcePingController extends AbstractController
{
	/**
	 * A remote site will ping us when one of their objects has been updated.
	 *
	 * @param int $resource_id
	 * @param mixed $record_id
	 */
	public function postObjectUpdated($resource_id, $record_id)
	{
		$filter = $this['deskpro.core.filter_factory']->createForFilter('object_updated');
		$filter->send("$resource_id:$record_id");

		return $this->createApiResponse(array('success' => true));
	}
}
