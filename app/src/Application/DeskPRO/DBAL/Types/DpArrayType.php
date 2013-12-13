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

use Doctrine\DBAL\Types\ArrayType;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class DpArrayType extends ArrayType
{
	public function getSQLDeclaration(array $fieldDeclaration, \Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return 'LONGBLOB';
    }

	public function convertToPHPValue($value, \Doctrine\DBAL\Platforms\AbstractPlatform $platform)
	{
		try {
			if ($value === null) {
				return null;
			}

			$value = (is_resource($value)) ? stream_get_contents($value) : $value;
			$val = @unserialize($value);
			if ($val === false && $value != 'b:0;') {
				throw ConversionException::conversionFailed($value, $this->getName());
			}
			return $val;
		} catch (ConversionException $e) {
			$errinfo = \DeskPRO\Kernel\KernelErrorHandler::getExceptionInfo($e);
			\DeskPRO\Kernel\KernelErrorHandler::logErrorInfo($errinfo);
			return array();
		}
	}

	public function getName()
	{
		return Type::TARRAY;
	}
}
