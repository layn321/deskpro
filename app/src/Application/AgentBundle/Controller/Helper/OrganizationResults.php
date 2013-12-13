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
 * @subpackage AgentBundle
 */

namespace Application\AgentBundle\Controller\Helper;

use Application\DeskPRO\Searcher\OrganizationSearch;
use Application\DeskPRO\Entity\ResultCache;
use Application\DeskPRO\Entity\Organization;
use Application\DeskPRO\Entity;
use Application\DeskPRO\App;
use Orb\Util\Strings;
use Orb\Util\Arrays;

/**
 * Handles org searches
 */
class OrganizationResults
{
	/**
	 * @var \Application\AgentBundle\Controller\AbstractController
	 */
	protected $controller;

	/**
	 * @var array
	 */
	protected $organization_ids = array();

	/**
	 * @var array
	 */
	protected $order_by = null;

	/**
	 * @return \Application\AgentBundle\Controller\Helper\OrganizationResults
	 */
	public static function newFromResultCache($controller, ResultCache $result_cache)
	{
		$helper = new self($controller);
		$helper->setOrganizationIds($result_cache['results']);

		return $helper;
	}



	public function __construct($controller)
	{
		$this->controller = $controller;
	}



	/**
	 * Set people IDs for the search results
	 * @param array $people_ids
	 */
	public function setOrganizationIds(array $people_ids)
	{
		$this->organization_ids = $people_ids;
	}



	/**
	 * Get people IDs
	 *
	 * @return array
	 */
	public function getOrganizationIds()
	{
		return $this->organization_ids;
	}


	/**
	 * Get people for a particular page
	 *
	 * @return array
	 */
	public function getOrgsForPage($page, $per_page = 50)
	{
		return $this->_getPageFromOrgsIds($this->getOrganizationIds(), $page, $per_page);
	}



	protected function _getPageFromOrgsIds(array $org_ids, $page, $per_page)
	{
		$page_org_ids = Arrays::getPageChunk($org_ids, $page, $per_page);
		$orgs_raw = App::getEntityRepository('DeskPRO:Organization')->getOrganizationsFromIds($page_org_ids);

		// - We'll get a page of results, but that actual page isn't going to be
		// sorted the way we want, because MySQL was just sent a list of ID's.
		// - So we'll re-create the array here according to the order they're supposed to be in.
		$orgs = array();
		foreach ($org_ids as $tid) {
			if (isset($orgs_raw[$tid])) {
				$orgs[$tid] = $orgs_raw[$tid];
			}
		}

		return $orgs;
	}
}
