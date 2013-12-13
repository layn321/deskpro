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
*/

namespace Cloud\AdminBundle\Controller;

use Application\AdminBundle\Controller\AgentsController as BaseAgentsController;

class AgentsController extends BaseAgentsController
{
	public function canAddAgent($context)
	{
		if ($context == 'view' && $this->person->can_billing) {
			return true;
		}

		if ($context == 'save' && $this->person->can_billing) {
			$this->authorizePlanIncrease();
			return true;
		}

		return parent::canAddAgent($context);
	}

	protected function _preMassAddAgents(array $emails)
	{
		$new_total = $this->num_agents + count($emails);

		if ($this->person->can_billing) {
			if ($new_total > $this->max_agents) {
				$diff = $new_total - $this->max_agents;

				try {
					$this->authorizePlanIncrease($diff);
				} catch (\Exception $e) {
					return parent::_preMassAddAgents($emails);
				}
			}
		}

		return null;
	}

	public function authorizePlanIncrease($num_agents = 1)
	{
		if (!$this->person->can_billing) {
			return;
		}

		if ($this->num_agents < $this->max_agents) {
			return;
		}

		$set = max($this->num_agents + $num_agents, $this->max_agents + $num_agents);

		$tmpdata = new \Application\DeskPRO\Entity\TmpData();
		$tmpdata->setType('dpc_set_plan');
		$tmpdata->setData('by_person', $this->person->getId());
		$tmpdata->setData('set_plan', $set);
		$tmpdata->date_expire = new \DateTime('+30 minutes');

		$this->em->persist($tmpdata);
		$this->em->flush();

		$url = DP_MA_SERVER . '/cloud/call/'.DPC_SITE_ID.'/'. $tmpdata->getCode();

		try {
			$client = new \Zend\Http\Client(null, array('timeout' => 10));
			$client->setMethod(\Zend\Http\Request::METHOD_GET);
			$client->setUri($url);
			$r = $client->send();
		} catch (\Exception $e) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
		}
	}
}
