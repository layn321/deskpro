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
 * Orb
 *
 * @package Orb
 * @subpackage GeoIp
 */

namespace Orb\GeoIp;

class GeoIpExtension extends AbstractGeoIp
{
	private $last = null;

	/**
	 * @param string $host
	 * @param array $what
	 * @return array
	 */
	public function lookup($host, array $what = null)
	{
		if (!function_exists('geoip_db_avail')) {
			return $this->getEmptyRecord();
		}

		if ($this->last && $this->last[0] == $host) {
			$rec = $this->last[1];
		} else {
			if (geoip_db_avail(\GEOIP_CITY_EDITION_REV0) || geoip_db_avail(\GEOIP_CITY_EDITION_REV1)) {
				$rec = @geoip_record_by_name($host);
			} elseif (geoip_db_avail(\GEOIP_COUNTRY_EDITION)) {
				$continent = @geoip_continent_code_by_name($host);
				$country   = @geoip_country_code_by_name($host);

				$rec = array(
					self::CONTINENT => $continent,
					self::COUNTRY   => $country,
				);
			} else {
				$rec = array();
			}
		}

		$this->last = array(
			$host,
			$rec
		);

		if ($what === null) {
			$what = array_keys($this->getEmptyRecord());
		}

		foreach ($what as $w) {
			switch ($w) {
				case self::CONTINENT:
					$return[self::CONTINENT] = !empty($rec['continent_code']) ? $rec['continent_code'] : null;
					break;

				case self::COUNTRY:
					$return[self::COUNTRY] = !empty($rec['country_code']) ? $rec['country_code'] : null;
					break;

				case self::REGION:
					$return[self::REGION] = !empty($rec['region']) ? $rec['region'] : null;
					break;

				case self::CITY:
					$return[self::CITY] = !empty($rec['city']) ? $rec['city'] : null;
					break;

				case self::LATITUDE:
					$return[self::LATITUDE] = !empty($rec['latitude']) ? $rec['latitude'] : null;
					break;

				case self::LONGITUDE:
					$return[self::LATITUDE] = !empty($rec['longitude']) ? $rec['longitude'] : null;
					break;
			}
		}

		return $return;
	}
}