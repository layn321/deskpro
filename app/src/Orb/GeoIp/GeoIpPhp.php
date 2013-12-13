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

class GeoIpPhp extends AbstractGeoIp
{
	/**
	 * @var null
	 */
	private $last = null;

	/**
	 * @var array
	 */
	private $dbs = array();

	/**
	 * @var array
	 */
	private $db_handles = array();

	public function __construct()
	{
		if (defined('GEOIP_API_INC_PATH')) {
			require_once GEOIP_API_INC_PATH.'/geoip.inc';
		} else {
			require_once 'geoip.inc';
		}
	}


	/**
	 * Add a database file
	 *
	 * @param string $type
	 * @param string $path
	 */
	public function addDatabase($type, $path)
	{
		$this->dbs[$type] = $path;
	}


	/**
	 * @param string $type
	 * @return \GeoIP
	 */
	public function getDbHandle($type)
	{
		if (!isset($this->db_handles[$type])) {
			$this->db_handles[$type] = geoip_open($this->dbs[$type], \GEOIP_STANDARD);
		}

		return $this->db_handles[$type];
	}


	/**
	 * @param string $type
	 * @return bool
	 */
	public function hasDb($type)
	{
		return isset($this->dbs[$type]);
	}


	/**
	 * @param string $host
	 * @param array $what
	 * @return array
	 */
	public function lookup($host, array $what = null)
	{
		if ($this->last && $this->last[0] == $host) {
			$rec = $this->last[1];
		} else {
			if ($this->hasDb(\GEOIP_CITY_EDITION_REV0) || $this->hasDb(\GEOIP_CITY_EDITION_REV1)) {
				if ($this->hasDb(\GEOIP_CITY_EDITION_REV1)) {
					$db = $this->getDbHandle(\GEOIP_CITY_EDITION_REV1);
				} else {
					$db = $this->getDbHandle(\GEOIP_CITY_EDITION_REV0);
				}

				$rec_obj = \GeoIP_record_by_addr($db, $host);
				$rec = array();
				$map = array(
					'continent_code',
					'country_code',
					'region',
					'city',
					'latitude',
					'longitutde',
				);

				foreach ($map as $prop) {
					if (!empty($rec_obj->$prop)) {
						$rec[$prop] = $rec_obj->$prop;
					} else {
						$rec[$prop] = null;
					}
				}

			} elseif ($this->hasDb(\GEOIP_COUNTRY_EDITION)) {
				$db = $this->getDbHandle(\GEOIP_COUNTRY_EDITION);

				$country_id = geoip_country_id_by_addr($db, $host);
				$country    = null;
				$continent  = null;

				if ($country_id !== false) {
					if (isset($db->GEOIP_CONTINENT_CODES[$country_id])) {
						$continent = $db->GEOIP_CONTINENT_CODES[$country_id];
					}
					if (isset($db->GEOIP_COUNTRY_CODES[$country_id])) {
						$country = $db->GEOIP_COUNTRY_CODES[$country_id];
					}
				}

				$rec = array(
					'continent_code' => $continent,
					'country_code'   => $country,
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

		$return = array_fill_keys($what, null);
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