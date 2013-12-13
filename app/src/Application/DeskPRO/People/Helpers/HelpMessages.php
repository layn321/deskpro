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
 * @category Entities
 */

namespace Application\DeskPRO\People\Helpers;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

/**
 * Helper keeps track of "help messages" which are displayed until dismissed, and never seen again.
 */
class HelpMessages implements \Orb\Helper\ShortCallableInterface
{
	const ALL = '__ALL__';

	protected $person;
	protected $pref;
	protected $pref_name;

	public function __construct(Entity\Person $person)
	{
		$this->person = $person;
		$this->pref_name = 'ui.dismissed-help-messages';
	}

	public function setPrefName($pref_name)
	{
		$this->pref_name = $pref_name;
	}

	protected function _initPref()
	{
		if ($this->pref !== null) return;

		$this->pref = $this->person->getPref($this->pref_name);
		if (!$this->pref) {
			$this->pref = $this->person->addPreference($this->pref_name);
			$this->pref['value'] = array();

			App::getOrm()->transactional(function ($em) use ($person) {
				$em->persist($person);
				$em->flush();
			});
		}
	}

	public function _getThis()
	{
		return $this;
	}

	public function getShortCallableNames()
	{
		return array(
			'getHelpMessages' => '_getThis',
			'shouldShowMessage' => 'shouldShowMessage'
		);
	}

	public function getDismissedIds()
	{
		$this->_initPref();
		return $this->pref['value'];
	}

	public function shouldShowMessage($id)
	{
		$this->_initPref();
		return !$this->isDismissed($id);
	}

	public function isDismissed($id)
	{
		$this->_initPref();
		return (in_array(self::ALL, $this->pref['value']) OR in_array($id, $this->pref['value']));
	}

	public function dismiss($id)
	{
		if ($this->isDismissed($id)) {
			return;
		}

		$this->_initPref();

		$val = $this->pref['value'];
		$val[] = $id;

		$this->pref['value'] = $val;

		$pref = $this->pref;
		App::getOrm()->transactional(function ($em) use ($pref) {
			$em->persist($pref);
			$em->flush();
		});
	}

	public function undismiss($id)
	{
		$this->_initPref();

		$val = $this->pref['value'];

		// Not in here
		if (($key = array_search($id, $val)) === false) {
			return;
		}

		unset($val[$key]);
		$val = array_values($val); // rekey numerically

		$this->pref['value'] = $val;

		$pref = $this->pref;
		App::getOrm()->transactional(function ($em) use ($pref) {
			$em->persist($pref);
			$em->flush();
		});
	}

	public function reset()
	{
		$this->_initPref();

		$this->pref['value'] = array();

		$pref = $this->pref;
		App::getOrm()->transactional(function ($em) use ($pref) {
			$em->persist($pref);
			$em->flush();
		});
	}
}
