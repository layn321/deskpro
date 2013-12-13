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

namespace Application\DeskPRO\Feedback;

use Application\DeskPRO\Entity\CustomDefFeedback;

class UserCategory
{
	/**
	 * @var \Application\DeskPRO\Entity\CustomDefFeedback
	 */
	protected $field;

	/**
	 * @var \Application\DeskPRO\Entity\CustomDefFeedback|null
	 */
	protected $sub_field;

	public function __construct(CustomDefFeedback $field, CustomDefFeedback $sub_field = null)
	{
		$this->field = $field;
		$this->sub_field = $sub_field;
	}


	/**
	 * @return \Application\DeskPRO\Entity\CustomDefFeedback
	 */
	public function getField()
	{
		return $this->field;
	}


	/**
	 * @return \Application\DeskPRO\Entity\CustomDefFeedback|null
	 */
	public function getSubField()
	{
		return $this->sub_field;
	}


	/**
	 * Get the category ID
	 *
	 * @return mixed
	 */
	public function getCategoryId()
	{
		if ($this->sub_field) {
			return $this->sub_field->getId();
		}

		return $this->field->getId();
	}


	/**
	 * Get the category title
	 *
	 * @param string $sep
	 * @return string
	 */
	public function getTitle($sep = ' > ')
	{
		$parts = array();
		$parts[] = $this->field->getTitle();

		if ($this->sub_field) {
			$parts[] = $this->sub_field->getTitle();
		}

		return implode($sep, $parts);
	}


	/**
	 * Is there a sub-category?
	 *
	 * @return bool
	 */
	public function hasSub()
	{
		return $this->sub_field !== null;
	}
}