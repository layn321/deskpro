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
 * @subpackage CustomFields
 */

namespace Application\DeskPRO\CustomFields\Handler;

use Application\DeskPRO\EventDispatcher\DataEvent;
use Application\DeskPRO\EventDispatcher\FilterPluginInterface;

class DisplayEvent extends DataEvent implements FilterPluginInterface
{
	protected $field_def;

	public function __construct($field_def, $data = array())
	{
		parent::__construct($data);
		$this->field_def = $field_def;
	}

	public function getField()
	{
		return $this->field_def;
	}

	/**
	 * @param Plugin $plugins
	 * @return bool
	 */
	public function filterPlugins($plugin)
	{
		if (isset($plugin['event_options']['field_table'])) {
			if ($plugin['event_options']['field_table'] != $this->field_def->getTableName()) {
				return false;
			}
		}

		if (isset($plugin['event_options']['field_id'])) {
			if ($plugin['event_options']['field_id'] != $this->field_def['id']) {
				return false;
			}
		}

		return true;
	}
}
