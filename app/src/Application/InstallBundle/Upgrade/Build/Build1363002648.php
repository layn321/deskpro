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

namespace Application\InstallBundle\Upgrade\Build;

class Build1363002648 extends AbstractBuild
{
	public function run()
	{
		$this->out("Add fields to visitors table");
		$this->execMutateSql("
			ALTER TABLE visitors
				ADD page_title VARCHAR(255) NOT NULL,
				ADD page_url VARCHAR(255) NOT NULL,
				ADD ref_page_url VARCHAR(255) DEFAULT NULL,
				ADD user_agent VARCHAR(255) NOT NULL,
				ADD user_browser VARCHAR(255) NOT NULL,
				ADD user_os VARCHAR(255) NOT NULL,
				ADD ip_address VARCHAR(80) NOT NULL,
				ADD geo_continent VARCHAR(2) DEFAULT NULL,
				ADD geo_country VARCHAR(2) DEFAULT NULL,
				ADD geo_region VARCHAR(2) DEFAULT NULL,
				ADD geo_city VARCHAR(2) DEFAULT NULL,
				ADD geo_long NUMERIC(16, 8) DEFAULT NULL,
				ADD geo_lat NUMERIC(16, 8) DEFAULT NULL
			");
		$this->execMutateSql("
			UPDATE visitors
			LEFT JOIN visitor_tracks ON (visitor_tracks.id = visitors.last_track_id)
			SET
				visitors.page_title = visitor_tracks.page_title,
				visitors.page_url = visitor_tracks.page_url,
				visitors.user_agent = visitor_tracks.user_agent,
				visitors.user_browser = visitor_tracks.user_browser,
				visitors.user_os = visitor_tracks.user_os,
				visitors.ip_address = visitor_tracks.ip_address,
				visitors.geo_continent = visitor_tracks.geo_continent,
				visitors.geo_country = visitor_tracks.geo_country,
				visitors.geo_region = visitor_tracks.geo_region,
				visitors.geo_city = visitor_tracks.geo_city,
				visitors.geo_long = visitor_tracks.geo_long,
				visitors.geo_lat = visitor_tracks.geo_lat
			WHERE visitor_tracks.id IS NOT NULL
		");
	}
}