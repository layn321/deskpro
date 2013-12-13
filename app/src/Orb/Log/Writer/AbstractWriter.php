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
 * @subpackage Log
 */

namespace Orb\Log\Writer;
use \Orb\Log\LogItem;



/**
 * A writer saves data somewhere
 */
abstract class AbstractWriter
{
	/**
	 * Filter chain applied to the writer
	 * @var Orb\Filter\FilterChain
	 */
	protected $_filter_chain = null;


	
	/**
	 * Get the filter chain instance
	 *
	 * @return Orb\Filter\FilterChain
	 */
	public function getFilterChain()
	{
		if ($this->_filter_chain === null) {
			$this->_filter_chain = new \Orb\Filter\FilterChain();
		}
		return $this->_filter_chain;
	}


	
	/**
	 * Add a filter to be applied to every item.
	 * 
	 * @param \Zend\Filter\Filter $filter
	 * @return AbstractWriter
	 */
	public function addFilter(\Orb\Filter\FilterInterface $filter)
	{
		$this->getFilterChain()->addFilter($filter);
		return $this;
	}

	

	/**
	 * Run filters on the log items
	 *
	 * @param LogItem $log_item
	 * @return LogItem
	 */
	public function filterLogItem(LogItem $log_item)
	{
		// Not initialized, means no filters
		if ($this->_filter_chain === null) return $log_item;

		$log_item = $this->_filter_chain->filter($log_item);

		return $log_item;
	}


	
	/**
	 * Write a log message
	 *
	 * @param  LogItem $event
	 * @return bool
	 */
	public function write(LogItem $log_item)
	{
		$log_item = $this->filterLogItem($log_item);

		if (!$log_item) {
			return false;
		}

		$this->_write($log_item);

		return true;
	}


	
    /**
     * Write a log message
     *
     * @param  LogItem $event
     * @return Writer
     */
    abstract protected function _write(LogItem $log_item);



    /**
     * Perform shutdown activities
     *
     * @return void
     */
    public function shutdown()
	{
		
	}
}
