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
 * @category Tickets
 */

namespace Application\DeskPRO\People\PersonMerge\Property;


use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Person;

use Orb\Util\Arrays;

/**
 * A property is something that can be merged in a person
 */
abstract class PropertyAbstract
{
	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $other_person;

	/**
	 * @var string
	 */
	protected $strategy = null;

	/**
	 * @var array
	 */
	protected $strategy_options = array();

	const STRATEGY_LEFT     = 'left';
	const STRATEGY_RIGHT    = 'right';
	const STRATEGY_COMBINE  = 'merge';


	public function __construct(Person $person, Person $other_person)
	{
		$this->person = $person;
		$this->other_person = $other_person;
	}

	
	/**
	 * Merge the two people
	 */
	abstract public function merge();


	/**
	 * Set the merge strategy (how to handle conflicts)
	 * 
	 * @param string $strategy
	 * @return void
	 */
	public function setStrategy($strategy, array $options = array())
	{
		$this->strategy = $strategy;
		$this->options = $options;
	}

	
	/**
	 * @return string
	 */
	public function getStrategy()
	{
		return $this->strategy;
	}


	/**
	 * Get a strategy option
	 *
	 * @param string $name Name of the option
	 * @param string $default The default value if it wasnt set
	 * @return mixed
	 */
	public function getStrategyOption($name, $default = null)
	{
		return isset($this->strategy_options[$name]) ? $this->strategy_options[$name] : $default;
	}
}
