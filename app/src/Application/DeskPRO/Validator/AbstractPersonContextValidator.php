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
 * @subpackage Validator
 */

namespace Application\DeskPRO\Validator;

use Application\DeskPRO\People\PersonContextInterface;
use Application\DeskPRO\Entity\Person;

use Orb\Validator\AbstractValidator;

abstract class AbstractPersonContextValidator extends AbstractValidator implements PersonContextInterface
{
	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person_context;

	public function init()
	{
		$this->setPersonContext($this->getOption('person_context'));
	}

	/**
	 * @param \Application\DeskPRO\Entity\Person|null $person_context
	 */
	public function setPersonContext(Person $person_context = null)
	{
		$this->person_context = $person_context;
	}


	/**
	 * @return bool
	 */
	public function hasPerson()
	{
		return $this->person_context !== null;
	}


	/**
	 * @return \Application\DeskPRO\Entity\Person
	 */
	public function getPerson()
	{
		return $this->person_context;
	}
}
