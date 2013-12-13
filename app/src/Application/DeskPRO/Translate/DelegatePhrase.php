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
 * @subpackage Translate
 */

namespace Application\DeskPRO\Translate;

use Application\DeskPRO\Entity\Language;

class DelegatePhrase implements DelegatePhraseInterface
{
	protected $phrase_name;
	protected $phrase_vars = array();

	public function __construct($phrase_name, array $phrase_vars = array())
	{
		$this->phrase_name = $phrase_name;
		$this->phrase_vars = $phrase_vars;
	}


	/**
	 * Get the phrase text.
	 *
	 * @param  $translator
	 * @return string
	 */
	public function getPhrase(Translate $translator, Language $language = null)
	{
		return $translator->phrase($this->phrase_name, $this->phrase_vars, $language);
	}


	/**
	 * @return string
	 */
	public function getPhraseName()
	{
		return $this->phrase_name;
	}


	/**
	 * @return array
	 */
	public function getPhraseVars()
	{
		return $this->phrase_vars;
	}


	public function __toString()
	{
		return $this->phrase_name;
	}
}
