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

namespace Application\DeskPRO\DependencyInjection\SystemServices;

use Application\DeskPRO\DependencyInjection\DeskproContainer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

/**
 * A base service wrapper around a repository. Mostly just to cache query results
 * so things like getting an array of titles dont get executed multiple times, but can be subclassed for more
 * advanced stuff.
 */
class BaseRepositoryService
{
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $db;

	/**
	 * @var \Doctrine\ORM\EntityRepository
	 */
	protected $repos;

	/**
	 * @var string
	 */
	protected $entity_name = null;

	/**
	 * @var array
	 */
	protected $call_result = array();

	/**
	 * @var \Orb\Util\OptionsArray
	 */
	protected $options;

	public static function create(DeskproContainer $container, array $options = null)
	{
		$em = $container->getEm();
		$o = new static($em, $options);
		return $o;
	}


	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em, array $options = null)
	{
		$this->options = new \Orb\Util\OptionsArray($options);

		if ($this->options->get('entity')) {
			$this->entity_name = $this->options->get('entity');
		}

		$this->em = $em;
		$this->db = $em->getConnection();

		$this->repos = $this->em->getRepository($this->getEntityName());
		$this->init();
	}

	protected function init()
	{

	}


	/**
	 * The entity class
	 *
	 * @return string
	 */
	public function getEntityName()
	{
		return $this->entity_name;
	}


	/**
	 * Reset the saved state
	 */
	public function reset()
	{
		$this->call_result = array();
	}


	public function __call($method, array $args = array())
	{
		$hash_seg = array($method);

		if ($args) {
			foreach ($args as $k => $a) {
				if (is_scalar($a)) {
					$hash_seg[] = $k.':';
					$hash_seg[] = (string)$a;
				} else {
					return call_user_func_array(array($this->repos, $method), $args);
				}
			}
		}

		$hash = md5(implode('', $hash_seg));
		if (isset($this->call_result[$hash])) {
			return $this->call_result[$hash];
		}

		$this->call_result[$hash] = call_user_func_array(array($this->repos, $method), $args);
		return $this->call_result[$hash];
	}
}