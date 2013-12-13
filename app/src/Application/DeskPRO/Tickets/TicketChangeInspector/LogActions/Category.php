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

class Category extends AbstractLogAction
{
	protected $old_cat;
	protected $new_cat;

	public function __construct($old_cat, $new_cat)
	{
		$this->old_cat = $old_cat;
		$this->new_cat = $new_cat;
	}

	public function getLogName()
	{
		return 'changed_category';
	}

	public function getLogDetails()
	{
		if ($this->old_cat and $this->new_cat) {
			if ($this->old_cat->getId() == $this->new_cat->getId()) {
				return array();
			}
		} else if (!$this->old_cat and !$this->new_cat) {
			return array();
		}

		return array(
			'id_before' => $this->old_cat['id'] ?: null,
			'id_after'  => $this->new_cat['id'] ?: null,

			'old_category_id' => $this->old_cat ? $this->old_cat['id'] : 0,
			'old_category_title' => $this->old_cat ? $this->old_cat['title'] : '',
			'new_category_id' => $this->new_cat ? $this->new_cat['id'] : 0,
			'new_category_title' => $this->new_cat ? $this->new_cat['title'] : '',
		);
	}

	public function getEventType()
	{
		return 'property';
	}
}
