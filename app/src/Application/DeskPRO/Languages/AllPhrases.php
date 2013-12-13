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
 */

namespace Application\DeskPRO\Languages;

use Symfony\Component\Finder\Finder;

/**
 * This is a simple fileystem reader that loads all phrases from all files under a directory
 */
class AllPhrases
{
	/**
	 * @var string
	 */
	protected $dir;

	/**
	 * @var string[]
	 */
	protected $phrases = null;

	/**
	 * @var string[]
	 */
	protected $phrase_ids = null;

	/**
	 * @var callback
	 */
	protected $callback = null;

	public function __construct($dir)
	{
		$this->dir = $dir;
	}


	/**
	 * Set a callback function to be called for each phrase.
	 *
	 * The function will be passed $id and $phrase. You should accept the vars by reference and modify them directly.
	 *
	 * @param callback $callback
	 */
	public function setCallback($callback)
	{
		$this->callback = $callback;
	}


	/**
	 * @return string[]
	 */
	public function getPhrases()
	{
		if ($this->phrases !== null) {
			return $this->phrases;
		}

		$this->phrases = array();

		$finder = Finder::create()->files()->name('*.php')->in(array($this->dir));

		foreach ($finder as $file) {
			/** @var $file \SplFileInfo */
			$path = $file->getRealPath();

			$phrase_group = include($path);
			if ($phrase_group && is_array($phrase_group)) {
				foreach ($phrase_group as $id => $phrase) {
					if ($this->callback) {
						$callback = $this->callback;
						$callback($id, $phrase);
					}

					if ($id) {
						$this->phrases[$id] = $phrase;
					}
				}
			}
		}

		return $this->phrases;
	}


	/**
	 * @return string[]
	 */
	public function getPhraseIds()
	{
		if ($this->phrase_ids !== null) {
			return $this->phrase_ids;
		}

		$this->phrase_ids = array_keys($this->getPhrases());

		return $this->phrase_ids;
	}


	/**
	 * @return int
	 */
	public function count()
	{
		return count($this->getPhrases());
	}
}