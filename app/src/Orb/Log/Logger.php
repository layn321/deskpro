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

namespace Orb\Log;



/**
 * A logger class.
 *
 * Implements standard logging of message, priority and time. But also extable
 * through custom LogItem class, writers and filters to support additional
 * fields for enhanced information tracking.
 */
class Logger
{
	/**@#+ Standard log levels */
	const EMERG   = 0;
    const ALERT   = 1;
    const CRIT    = 2;
    const ERR     = 3;
    const WARN    = 4;
    const NOTICE  = 5;
    const INFO    = 6;
    const DEBUG   = 7;
    const STRICT   = 8;
	/**@#-*/

	/**
	 * Priority number => name
     * @var array
     */
    protected $_priorities = array(
		self::EMERG    => 'EMERG',
		self::ALERT    => 'ALERT',
		self::CRIT     => 'CRIT',
		self::ERR      => 'ERR',
		self::WARN     => 'WARN',
		self::NOTICE   => 'NOTICE',
		self::INFO     => 'INFO',
		self::DEBUG    => 'DEBUG',
		self::STRICT    => 'STRICT'
	);

	/**
	 * Main filter chain that will apply to all writers
	 * @var \Orb\Log\Writer\WriterChain
	 */
	protected $_writer_chain = null;

	/**
	 * A session name
	 * @var string
	 */
	protected $_session_name = null;

	/**
	 * @var array
	 */
	protected $_timers = array();

	/**
	 * True to disable logger.
	 *
	 * Defaults to disabled with default_disabled until a writer is added.
	 *
	 * @var bool
	 */
	public $disabled = true;

	/**
	 * @var bool
	 */
	protected $default_disabled = true;



	public function __construct()
	{
		$this->_writer_chain = new Writer\WriterChain();
	}


	/**
	 * Disable logger
	 */
	public function disable()
	{
		$this->disabled	= true;
		$this->default_disabled = false;
	}


	/**
	 * Enable logger
	 */
	public function enable()
	{
		$this->disabled = true;
		$this->default_disabled = false;
	}


	/**
	 * Is the logger enabled?
	 * @return bool
	 */
	public function isEnabled()
	{
		return $this->disabled;
	}


	/**
	 * Add a priroty
	 *
	 * @param string $name
	 * @param int    $priority
	 * @return Logger
	 */
	public function addPriority($name, $priority)
	{
		$name = strtoupper($name);

		if (isset($this->_priorities[$priority])) {
			throw new \InvalidArgumentException('Priority already exists');
		}

		$this->_priorities[$priority] = $name;
		return $this;
	}



	/**
	 * Add a filter to be applied to every item.
	 *
	 * A filter must return the event object (modified or not), or null if the event
	 * should not be logged. So in this way filters act dually to transform or actually
	 * filter out items.
	 *
	 * @param \Zend\Filter\Filter $filter
	 */
	public function addFilter(\Orb\Filter\FilterInterface $filter)
	{
		$this->_writer_chain->addFilter($filter);
	}



	/**
	 * Add a new writer to this logger.
	 *
	 * @param \Orb\Log\Writer\AbstractWriter $writer
	 */
	public function addWriter(\Orb\Log\Writer\AbstractWriter $writer)
	{
		// If its disabled because of default, we'll enable
		// it because this is the first writer
		if ($this->disabled && $this->default_disabled) {
			$this->disabled = false;
			$this->default_disabled = false;
		}

		$this->_writer_chain->addWriter($writer);
	}


	/**
	 * @param Writer\AbstractWriter $writer
	 */
	public function removeWriter(\Orb\Log\Writer\AbstractWriter $writer)
	{
		$this->_writer_chain->removeWriter($writer);
	}


	/**
	 * @return null|Writer\WriterChain
	 */
	public function getWriterChain()
	{
		return $this->_writer_chain;
	}



	/**
	 * Some writers are able to use a sesson name or ID to group a number of related
	 * log events together. For example, to log the process through a single execution.
	 *
	 * @param string $session_name
	 */
	public function setSessionName($session_name)
	{
		$this->_session_name = $session_name;
	}



	/**
	 * Log a new message
	 *
	 * @param string $message
	 * @param int $priority
	 * @param array $info
	 */
	public function log($message, $priority, array $info = array())
	{
		if ($this->disabled) {
			return;
		}

		if (is_string($priority)) {
			if ($priority == 'ERROR') {
				$priority = 'ERR';
			}
			$priority = constant('Orb\\Log\\Logger::' . strtoupper($priority));
		}

		$info[LogItem::MESSAGE] = $message;
		$info[LogItem::PRIORITY] = $priority;
		$info[LogItem::PRIORITY_NAME] = $this->_priorities[$priority];

		$log_item = $this->createLogInfoObject($info);
		$this->logItem($log_item);
	}


	/**
	 * Shortcut to log a DEBUG message.
	 *
	 * @param $message
	 * @param array $info
	 */
	public function logDebug($message, array $info = array())
	{
		$this->log($message, self::DEBUG, $info);
	}


	/**
	 * Shortcut for logging a dump of a value
	 *
	 * @param string $name    The name/message that precedes the dump
	 * @param mixed $var      The variable to be dumped
	 * @param int $priority   The log priority
	 */
	public function logVarDump($name, $var, $priority = self::DEBUG)
	{
		if (ini_get('xdebug.overload_var_dump')) {
			ob_start();
			var_dump($var);
			$dump = ob_get_clean();
		} else {
			$dump = self::varToString($var);
		}

		$this->log($name . ": " . $dump, $priority);
	}


	/**
	 * @param mixed $var
	 * @param int $_depth
	 * @return string
	 */
	public static function varToString($var, $_depth = 0)
    {
        if (is_object($var)) {
            return sprintf('[object](%s)', get_class($var));
        }
        if (is_array($var)) {
            $a = array();
            foreach ($var as $k => $v) {
				if ($_depth > 8) {
					$a[] = sprintf('%s => %s', $k, '(string)');
				} else {
					$a[] = sprintf('%s => %s', $k, self::varToString($v, $_depth+1));
				}
            }
            return sprintf("[array](%s)", implode(', ', $a));
        }
        if (is_resource($var)) {
            return '[resource]';
        }
		$str = (string)$var;
		if (strlen($str) > 1000) {
			$str = substr($str, 0, 1000) . "...(clipped)";
		}

		return $str;
    }


	/**
	 * Shortcut to log an INFO message.
	 *
	 * @param $message
	 * @param array $info
	 */
	public function logInfo($message, array $info = array())
	{
		$this->log($message, self::INFO, $info);
	}


	/**
	 * Shortcut to log an ERROR message.
	 *
	 * @param $message
	 * @param array $info
	 */
	public function logError($message, array $info = array())
	{
		$this->log($message, self::ERR, $info);
	}


	/**
	 * Shortcut to log an EMERG message.
	 *
	 * @param $message
	 * @param array $info
	 */
	public function logEmergency($message, array $info = array())
	{
		$this->log($message, self::EMERG, $info);
	}


	/**
	 * Shortcut to log a CRIT message.
	 *
	 * @param $message
	 * @param array $info
	 */
	public function logCritical($message, array $info = array())
	{
		$this->log($message, self::CRIT, $info);
	}


	/**
	 * Shortcut to log an ALERT message.
	 *
	 * @param $message
	 * @param array $info
	 */
	public function logAlert($message, array $info = array())
	{
		$this->log($message, self::ALERT, $info);
	}


	/**
	 * Shortcut to log an WARN message.
	 *
	 * @param $message
	 * @param array $info
	 */
	public function logWarn($message, array $info = array())
	{
		$this->log($message, self::WARN, $info);
	}


	/**
	 * Shortcut to log an NOTICE message.
	 *
	 * @param $message
	 * @param array $info
	 */
	public function logNotice($message, array $info = array())
	{
		$this->log($message, self::NOTICE, $info);
	}


	/**
	 * @param array $info
	 * @return LogItem
	 */
	public function createLogInfoObject(array $info)
	{
		$log_item = new LogItem($info);
		return $log_item;
	}



	/**
	 * Write a log item
	 * @param LogItem $log_item
	 */
	public function logItem(LogItem $log_item)
	{
		if ($this->disabled) {
			return;
		}

		if ($this->_session_name AND !$log_item[LogItem::SESSION_NAME]) {
			$log_item[LogItem::SESSION_NAME] = $this->_session_name;
		}

		$this->_writer_chain->write($log_item);
	}


	/**
	 * @param string $name
	 */
	public function startTimer($name = 'default')
	{
		return $this->_timers[$name] = microtime(true);
	}


	/**
	 * @param $name
	 * @return mixed
	 * @throws \InvalidArgumentException
	 */
	public function getStartTime($name)
	{
		if (!isset($this->_timers[$name])) {
			throw new \InvalidArgumentException("Timer not started: $name");
		}

		return $this->_timers[$name];
	}


	/**
	 * @param string $name
	 * @param bool $reset
	 * @return mixed
	 * @throws \InvalidArgumentException
	 */
	public function getTotalTime($name = 'default', $reset = true)
	{
		$name_e = $name . '__end';

		if (!isset($this->_timers[$name])) {
			throw new \InvalidArgumentException("Timer not started: $name");
		}

		if ($reset || !isset($this->_timers[$name_e])) {
			$this->_timers[$name_e] = microtime(true) - $this->_timers[$name];
		}

		return $this->_timers[$name_e];
	}


	/**
	 * @param string $name
	 * @param $message
	 * @param string $level
	 */
	public function logToatlTime($name = 'default', $message = null, $level = 'DEBUG')
	{
		if (!$message) {
			$message = "$name time: {{TIME}}";
		}

		if (strpos($message, '{{TIME}}') === false) {
			$message .= ' {{TIME}}';
		}

		$message = str_replace('{{TIME}}', sprintf("%.5fs", $this->getTotalTime($name)), $message);

		$this->log($message, $level);
	}
}
