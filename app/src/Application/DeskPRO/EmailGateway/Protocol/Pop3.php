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

namespace Application\DeskPRO\EmailGateway\Protocol;

use Orb\Log\Logger;
use Zend\Mail\Protocol\Exception;

class Pop3 extends \Zend\Mail\Protocol\Pop3
{
	/**
	 * @var \Orb\Log\Logger
	 */
	protected $logger;

	/**
	 * @var int
	 */
	protected $connect_timeout = 8;

	/**
	 * @var int
	 */
	protected $stream_timeout = 15;


	/**
	 * @param string $host
	 * @param null $port
	 * @param bool $ssl
	 * @param Logger $logger
	 * @param int $connect_timeout
	 * @param int $stream_timeout
	 */
	public function __construct($host = '', $port = null, $ssl = false, Logger $logger = null, $connect_timeout = 8, $stream_timeout = 15)
	{
		$this->logger = $logger;
		$this->connect_timeout = $connect_timeout;
		$this->stream_timeout = $stream_timeout;
		parent::__construct($host, $port, $ssl);
	}


	/**
	 * @param string $host
	 * @param null $port
	 * @param bool $ssl
	 * @return string
	 * @throws \Zend\Mail\Protocol\Exception\RuntimeException
	 */
	public function connect($host, $port = null, $ssl = false)
    {
        if ($ssl == 'SSL') {
            $host = 'ssl://' . $host;
        }

        if ($port === null) {
            $port = $ssl == 'SSL' ? 995 : 110;
        }

        $errno  =  0;
        $errstr = '';
        $this->_socket = fsockopen($host, $port, $errno, $errstr, $this->connect_timeout);
        if (!$this->_socket) {
            throw new Exception\RuntimeException('cannot connect to host; error = ' . $errstr . ' (errno = ' . $errno . ' )');
        }
		stream_set_timeout($this->_socket, $this->stream_timeout);

        $welcome = $this->readResponse();

        strtok($welcome, '<');
        $this->_timestamp = strtok('>');
        if (!strpos($this->_timestamp, '@')) {
            $this->_timestamp = null;
        } else {
            $this->_timestamp = '<' . $this->_timestamp . '>';
        }

        if ($ssl === 'TLS') {
            $this->request('STLS');
            $result = stream_socket_enable_crypto($this->_socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            if (!$result) {
                throw new Exception\RuntimeException('cannot enable TLS');
            }
        }

        return $welcome;
    }


	/**
	 * @param Logger $logger
	 */
	public function setLogger(Logger $logger = null)
	{
		$this->logger = $logger;
	}


	/**
     * Make a RETR call for retrieving a full message with headers and body
     *
     * @param  int $msgno  message number
     * @return string message
     */
    public function retrieveToStream($msgno, $stream)
    {
        $result = $this->requestToStream("RETR $msgno", $stream);
        return $result;
    }


	/**
     * Send request and get resposne
     *
     * @see sendRequest(), readResponse()
     *
     * @param  string $request    request
     * @param  resource $stream stream
     * @return int Number of bytes read to stream
     */
    public function requestToStream($request, $stream)
    {
        $this->sendRequest($request);
        return $this->readResponseToStream($stream);
    }


	/**
	 * @param string $request
	 * @return null
	 */
	public function sendRequest($request)
	{
		if ($this->logger) {
			if (strpos($request, 'PASS ') === 0) {
				$this->logger->logDebug("[Request] PASS xxxxxx");
			} else {
				$this->logger->logDebug("[Request] " . $request);
			}
		}

		return parent::sendRequest($request);
	}


	/**
	 * @param bool $multiline
	 * @return string
	 * @throws \Zend\Mail\Protocol\Exception\RuntimeException
	 */
	public function readResponse($multiline = false)
	{
		$result = @fgets($this->_socket);
        if (!is_string($result)) {
			if ($this->logger) $this->logger->logDebug("[Response] read failed - connection closed?");
            throw new Exception\RuntimeException('read failed - connection closed?');
        }

        $result = trim($result);
        if (strpos($result, ' ')) {
            list($status, $message) = explode(' ', $result, 2);
        } else {
            $status = $result;
            $message = '';
        }

        if ($status != '+OK') {
			if ($this->logger) $this->logger->logDebug("[Response] $status");
            throw new Exception\RuntimeException('last request failed');
        }

        if ($multiline) {
            $message = '';
            $line = fgets($this->_socket);
			$log_msg = '';
            while ($line && rtrim($line, "\r\n") != '.') {
                if ($line[0] == '.') {
                    $line = substr($line, 1);
                }
                $message .= $line;
                $line = fgets($this->_socket);
				if ($this->logger && !isset($log_msg[350])) {
					$log_msg .= $line;
				}
            }
			if ($this->logger) $this->logger->logDebug("[Response] $status $log_msg");
        }

        return $message;
	}


	/**
	 * This reads a multi-line response to a stream and returns the number of bytes read.
	 *
	 * @param $stream
	 * @return int
	 * @throws \Zend\Mail\Protocol\Exception\RuntimeException
	 */
	public function readResponseToStream($stream)
	{
		$result = @fgets($this->_socket);
        if (!is_string($result)) {
			if ($this->logger) $this->logger->logDebug("[Response] read failed - connection closed?");
            throw new Exception\RuntimeException('read failed - connection closed?');
        }

        $result = trim($result);
        if (strpos($result, ' ')) {
            list($status, ) = explode(' ', $result, 2);
        } else {
            $status = $result;
        }

        if ($status != '+OK') {
			if ($this->logger) $this->logger->logDebug("[Response] $status");
            throw new Exception\RuntimeException('last request failed');
        }

		$bytes = 0;
		$line = fgets($this->_socket);
		$log_msg = '';
		while ($line && rtrim($line, "\r\n") != '.') {
			if ($line[0] == '.') {
				$line = substr($line, 1);
			}
			$bytes += fwrite($stream, $line);
			$line = fgets($this->_socket);
			if ($this->logger && !isset($log_msg[350])) {
				$log_msg .= $line;
			}
		}
		if ($this->logger) $this->logger->logDebug("[Response] $status $log_msg");

        return $bytes;
	}
}