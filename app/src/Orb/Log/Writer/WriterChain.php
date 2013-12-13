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
 * A writer that calls other writers
 */
class WriterChain extends AbstractWriter implements \Countable, \IteratorAggregate
{
	/**
	 * An array of writers
	 * @var array
	 */
	protected $_writers = array();



	/**
	 * Add a new filter to the chain
	 *
	 * @param FilterInterface $writer
	 */
	public function addWriter(AbstractWriter $writer)
	{
		$this->_writers[] = $writer;
	}



	/**
	 * Add a new filter to the chain
	 *
	 * @param FilterInterface $writer
	 */
	public function removeWriter(AbstractWriter $writer)
	{
		if (($k = array_search($writer, $this->_writers, true)) !== false) {
			array_splice($this->_writers, $k, 1);
		}
	}



	/**
	 * Get the writers currently set.
	 *
	 * @return array
	 */
	public function getWriters()
	{
		return $this->_writers;
	}



	/**
     * Write a log message
     *
     * @param  LogItem $event
     * @return bool
     */
    public function _write(LogItem $log_item)
	{
		foreach ($this->_writers as $writer) {
			$writer->write($log_item);
		}

		return true;
	}



    /**
     * Perform shutdown activities
     *
     * @return void
     */
    public function shutdown()
	{
		foreach ($this->_writers as $writer) {
			$writer->shutdown();
		}
	}



	/**
	 * Count how many writers there are.
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->_writers);
	}



	/**
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->_writers);
	}
}
