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
 * @category Types
 */

namespace Application\DeskPRO\DBAL\Types;

use Doctrine\DBAL\Types\BlobType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Some enhancements to Doctrine's connection class.
 */
class DpBlobType extends BlobType
{
	public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
	{
		switch ($fieldDeclaration['length']) {
			case -1: return 'BINARY';
			case -2: return 'TINYBLOB';
			case -3: return 'BLOB';
			case -4: return 'MEDIUMBLOB';
			case -5: return 'LONGBLOB';
		}

		$type = $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);

		$type = str_replace(
			array('VARCHAR(', 'CHAR(', 'TINYTEXT', 'TEXT', 'MEDIUMTEXT', 'LONGTEXT'),
			array('VARBINARY(', 'BINARY(', 'TINYBLOB', 'BLOB', 'MEDIUMBLOB', 'LONGBLOB'),
			$type
		);

		return $type;
	}

	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		return ($value === null) ? null : $value;
	}

	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		return ($value === null) ? null : $value;
	}

	public function getName()
	{
		return 'dpblob';
	}
}
