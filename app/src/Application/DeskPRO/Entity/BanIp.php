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
use Orb\Util\Strings;
use Orb\Util\Numbers;

/**
 * Ban an IP addresses and ranges
 *
 */
class BanIp extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * The banned IP address (human readable)
	 *
	 * @var string
	 */
	protected $banned_ip;

	/**
	 * Start of the IP range
	 *
	 * @var int
	 */
	protected $ip_start;

	/**
	 * End of the IP range
	 *
	 * @var int
	 */
	protected $ip_end;

	public function setBannedIp($ip)
	{
		// Dont include wildcard at the ned
		$ip = preg_replace('#\.\*$#', '', $ip);

		// Remove bad chars
		$ip = preg_replace('#[^0-9\.]#', '', $ip);

		// Remove trailin dots
		$ip = trim($ip, '.');

		$parts = explode('.', $ip);
		if (count($parts) < 1 OR count($parts) > 4) {
			throw new \InvalidArgumentException('Invalid IP address: `'.$ip.'`');
		}

		$start = array();
		$end = array();

		foreach ($parts as $part) {
			$start[] = $part;
			$end[] = $part;
		}

		// For wildcarded parts we're missing some octets,
		// so we'll fill them in automatically
		while (count($start) < 4) {
			$start[] = 0;
			$end[] = 255;
		}

		if (count($parts) < 4) {
			$human = implode('.', $parts) . '.*';
		} else {
			$human = implode('.', $parts);
		}

		$this->banned_ip = $human;
		$this->ip_start = sprintf("%u", ip2long(implode('.', $start)));
		$this->ip_end = sprintf("%u", ip2long(implode('.', $end)));
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\BanIp';
		$metadata->setPrimaryTable(array( 'name' => 'ban_ips', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'banned_ip', 'type' => 'string', 'length' => 100, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'banned_ip', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'ip_start', 'type' => 'bigint', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'ip_start', ));
		$metadata->mapField(array( 'fieldName' => 'ip_end', 'type' => 'bigint', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'ip_end', ));
	}
}
