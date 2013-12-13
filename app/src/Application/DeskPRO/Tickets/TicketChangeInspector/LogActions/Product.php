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

class Product extends AbstractLogAction
{
	protected $old_product;
	protected $new_product;

	public function __construct($old_product, $new_product)
	{
		$this->old_product = $old_product;
		$this->new_product = $new_product;
	}

	public function getLogName()
	{
		return 'changed_product';
	}

	public function getLogDetails()
	{
		if ($this->old_product and $this->new_product) {
			if ($this->old_product->getId() == $this->new_product->getId()) {
				return array();
			}
		} else if (!$this->old_product and !$this->new_product) {
			return array();
		}

		return array(
			'id_before' => $this->old_product['id'] ?: null,
			'id_after'  => $this->new_product['id'] ?: null,

			'old_product_id'    => $this->old_product ? $this->old_product['id'] : 0,
			'old_product_title' => $this->old_product ? $this->old_product['title'] : '',
			'new_product_id'    => $this->new_product ? $this->new_product['id'] : 0,
			'new_product_title' => $this->new_product ? $this->new_product['title'] : '',
		);
	}

	public function getEventType()
	{
		return 'property';
	}
}
