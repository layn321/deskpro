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
 * @category Entities
 */

namespace Application\DeskPRO\Entity;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Base class used for storing custom field data.
 *
 */
abstract class CustomDataAbstract extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * The unique ID.
	 *
	 * @var int
	 *
	 */
	protected $id = null;

	/**
	 * IMPLEMENT IN CHILD CLASS
	 * The form field this is attached to
	 *
	 * @var \Application\DeskPRO\Entity\CustomDefXXX
	 */
	//protected $field = null;

	/**
	 * IMPLEMENT IN CHILD CLASS
	 * The root custom field this is attached to
	 *
	 * @var \Application\DeskPRO\Entity\CustomDefXXX
	 */
	//protected $root_field = null;

	/**
	 * IMPLEMENT IN CHILD CLASS
	 *
	 * @var \Application\DeskPRO\Entity\Xxx
	 */
	//protected $xxx;

	/**
	 * User numeric data
	 *
	 * @var int
	 */
	protected $value = 0;

	/**
	 * User string data
	 *
	 * @var string
	 */
	protected $input = '';

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}



	/**
	 * Get the value or input.
	 *
	 * @return mixed
	 */
	public function getData()
	{
		return $this->value ? $this->value : $this->input;
	}


	public function getFieldId()
	{
		return $this->field->getId();
	}

	public function toApiData($primary = true, $deep = true, array $visited = array())
	{
		$data = parent::toApiData($primary, $deep, $visited);

		// record isn't useful without these, so always include them
		if ($this->field) {
			$data['field'] = $this->field->toApiData(false, false, $visited);
		}
		if ($this->root_field) {
			$data['root_field'] = $this->root_field->toApiData(false, false, $visited);
		}

		return $data;
	}
}
