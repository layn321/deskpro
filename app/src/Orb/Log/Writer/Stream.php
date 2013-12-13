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
 * This writer writes to any stream
 */
class Stream extends AbstractWriter
{
 /**
	 * Holds the PHP stream to log to.
	 * @var null|stream
	 */
	protected $_stream = null;

	/**
	 * If we opened the stream ourselves
	 * @var bool
	 */
	protected $_did_open_stream = false;

	/**
	 * Chmod the file if we created it
	 *
	 * @var null
	 */
	protected $chmod_mode = 0777;

	protected $stream_url = null;
	protected $stream_mode = 'a';
	protected $close_after_write = false;

	public function enableNewStreamPerWrite()
	{
		$this->close_after_write = true;
	}

	/**
	 * If we created a new file, chmod it to this mode. Set to null
	 * to not chmod the file (in which case the default mask is used, typically 0755).
	 *
	 * @param $chmod
	 */
	public function setChmod($chmod)
	{
		$this->chmod_mode = $chmod;
	}

	/**
	 * @param  mixed  streamOrUrl     Stream or URL to open as a stream
	 * @param  string mode            Mode, only applicable if a URL is given
	 */
	public function __construct($stream_or_url, $mode = null, $add_lineformatter = true)
	{
		// Setting the default
		if ($mode === null) {
			$mode = 'a';
		}

		if (is_resource($stream_or_url)) {
			if (get_resource_type($stream_or_url) != 'stream') {
				throw new \InvalidArgumentException('Resource is not a stream');
			}

			if ($mode != 'a') {
				throw new \InvalidArgumentException('Mode cannot be changed on existing streams');
			}

			$this->_stream = $stream_or_url;
		} else {
			$this->stream_url = $stream_or_url;
			$this->stream_mode = $mode;
		}

		if ($add_lineformatter) {
			$this->addFilter(new \Orb\Log\Filter\SimpleLineFormatter());
		}
	}

	public function getStream()
	{
		if (!$this->_stream) {
			$mode = $this->stream_mode;
			$stream_or_url = $this->stream_url;
			$is_made = false;
			if (!file_exists($stream_or_url)) {
				$is_made = true;
			}
			if (!($this->_stream = @fopen($stream_or_url, $mode, false))) {
				$msg = "\"$stream_or_url\" cannot be opened with mode \"$mode\"";
				throw new \RuntimeException($msg);
			}

			if ($is_made && $this->chmod_mode !== null) {
				@chmod($stream_or_url, $this->chmod_mode);
			}

			$this->_did_open_stream = true;
		}

		return $this->_stream;
	}

	public function closeStream()
	{
		if ($this->_did_open_stream AND is_resource($this->_stream)) {
			fclose($this->_stream);
			$this->_stream = null;
			$this->_did_open_stream = false;
		}
	}


	public function shutdown()
	{
		$this->closeStream();
	}

	/**
	 * Write a message to the log.
	 */
	public function _write(LogItem $log_item)
	{
		$stream = $this->getStream();

		if (false === @fwrite($stream, $log_item[LogItem::MESSAGE_LINE] . "\n")) {
			throw new \RuntimeException("Unable to write to stream");
		}

		if ($this->close_after_write) {
			$this->closeStream();
		}
	}
}
