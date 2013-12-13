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
 * @subpackage Scraper
 */

namespace Orb\Scraper;

/**
 * A scraper is something that fetches data from some remote source. The method of fetching
 * the data, or the format of that data, is not relevant (that'll matter for whatever
 * system implements the system).
 */
abstract class AbstractScraper
{
	/**
	 * Array of options
	 * @var array
	 */
	protected $_options;

	public function __construct(array $options = array())
	{
		$this->_options = $options;
	}
	


	/**
	 * Get the value of an option
	 *
	 * @param string $key The option to get
	 * @param mixed $default What to return if the option doesnt exist
	 */
	public function getOption($key, $default = null)
	{
		return isset($this->_options[$key]) ? $this->_options[$key] : $default;
	}

	

	/**
	 * Check to see if an option exists
	 * 
	 * @param string $key
	 * @return bool
	 */
	public function hasOption($key)
	{
		return isset($this->_options[$key]);
	}


	
	/**
	 * @param mixed $identity Info we're requesting. A URL, an ID, etc. Depends on the scraper.
	 * @return ItemInterface
	 */
	abstract function getData($identity = null);
}
