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

use Application\DeskPRO\App;

/**
 * Base labels associations class
 *
 */
abstract class LabelAssocAbstract extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * The 'type' of label this is for, as it could be found in the
	 * LabelDef.
	 */
	const LABEL_TYPENAME = 'OVERRIDE';

	/**
	 * @var string
	 */
	protected $label;



	/**
	 * After a new association is made, we need to make sure the def table has this
	 * record.
	 *
	 */
	public function syncWithDef()
	{
		App::getDb()->executeUpdate("
			INSERT INTO label_defs (label_type, label, total)
			VALUES (?, ?, 1)
			ON DUPLICATE KEY UPDATE total = total + 1
		", array(static::LABEL_TYPENAME, $this->label));
	}


	public function __toString()
	{
		return $this->label;
	}
}
