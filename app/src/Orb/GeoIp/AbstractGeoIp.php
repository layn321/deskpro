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

abstract class AbstractGeoIp
{
	const CONTINENT  = 'continent';
	const COUNTRY    = 'country';
	const REGION     = 'region';
	const CITY       = 'city';
	const LATITUDE   = 'latitude';
	const LONGITUDE  = 'longitude';

	/**
	 * @param array $what
	 * @param string $host
	 * @return array
	 */
	abstract public function lookup($host, array $what = null);


	/**
	 * @param string $host
	 * @return string|null
	 */
	public function lookupContinent($host)
	{
		$ret = $this->lookup(array(self::CONTINENT), $host);
		return !empty($ret[self::CONTINENT]) ? $ret[self::CONTINENT] : null;
	}


	/**
	 * @param string $host
	 * @return string|null
	 */
	public function lookupCountry($host)
	{
		$ret = $this->lookup(array(self::COUNTRY), $host);
		return !empty($ret[self::COUNTRY]) ? $ret[self::COUNTRY] : null;
	}


	/**
	 * @param string $host
	 * @return string|null
	 */
	public function lookupRegion($host)
	{
		$ret = $this->lookup(array(self::REGION), $host);
		return !empty($ret[self::REGION]) ? $ret[self::REGION] : null;
	}


	/**
	 * @param string $host
	 * @return string|null
	 */
	public function lookupCity($host)
	{
		$ret = $this->lookup(array(self::CITY), $host);
		return !empty($ret[self::CITY]) ? $ret[self::CITY] : null;
	}

	/**
	 * @param string $host
	 * @return string|null
	 */
	public function lookupLatitude($host)
	{
		$ret = $this->lookup(array(self::LATITUDE), $host);
		return !empty($ret[self::LATITUDE]) ? $ret[self::LATITUDE] : null;
	}


	/**
	 * @param string $host
	 * @return string|null
	 */
	public function lookupLongitude($host)
	{
		$ret = $this->lookup(array(self::LONGITUDE), $host);
		return !empty($ret[self::LONGITUDE]) ? $ret[self::LONGITUDE] : null;
	}


	/**
	 * @return array
	 */
	public function getEmptyRecord()
	{
		return array(
			self::CONTINENT  => null,
			self::COUNTRY    => null,
			self::REGION     => null,
			self::CITY       => null,
			self::LATITUDE   => null,
			self::LONGITUDE  => null,
		);
	}
}