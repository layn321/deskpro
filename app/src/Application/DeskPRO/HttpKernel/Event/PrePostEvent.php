<?php


namespace Application\DeskPRO\HttpKernel\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\Event;

class PrePostEvent extends Event
{
	/**
	 * The response object
	 * @var \Symfony\Component\HttpFoundation\Response
	 */
	private $response;

	public $info = array();

	public function get($k)
	{
		return isset($this->info[$k]) ? $this->info[$k] : null;
	}

	public function __construct(array $info)
	{
		$this->info = $info;
	}

	public function getResponse()
	{
		return $this->response;
	}

	public function setResponse(Response $response)
	{
		$this->response = $response;

		$this->stopPropagation();
	}

	public function hasResponse()
	{
		return null !== $this->response;
	}
}