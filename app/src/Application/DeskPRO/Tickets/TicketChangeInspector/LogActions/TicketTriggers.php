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
 * @category Tickets
 */

namespace Application\DeskPRO\Tickets\TicketChangeInspector\LogActions;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

class TicketTriggers extends AbstractLogAction
{
	protected $triggers;

	public function __construct(array $triggers)
	{
		$this->triggers = $triggers;
	}

	public function getLogName()
	{
		return 'executed_triggers';
	}

	public function getLogDetails()
	{
		$tr = array();
		$tr_names = array();

		foreach ($this->triggers as $t) {
			if (strpos($t->event_trigger, 'time.') === 0) {
				continue;
			}

			if ($t->id) {
				$tr[] = $t->id;

				if ($t->title) {
					if ($t->sys_name && App::getTranslator()->hasPhrase($t->getSysPhraseName())) {
						$tr_names[] = App::getTranslator()->phrase($t->getSysPhraseName()) . " ({$t->id})";
					} else {
						$tr_names[] =  "{$t->title} ({$t->id})";
					}
				} else {
					$tr_names[] = $t->id;
				}
			}
		}

		if (!$tr) {
			return array();
		}

		$tr = array_unique($tr);
		$tr_names = array_unique($tr);

		return array(
			'trigger_ids'    => implode(', ', $tr),
			'trigger_titles' => implode(', ', $tr_names)
		);
	}

	public function getEventType()
	{
		return 'property';
	}
}
