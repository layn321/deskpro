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
 * @category Translate
 */

namespace Application\DeskPRO\Translate;

/**
 * A fake language in the translate class etc
 */
class SystemLanguage extends \Application\DeskPRO\Entity\Language
{
	protected static $instance = null;
	public static function getInstance()
	{
		if (self::$instance !== null) return self::$instance;

		self::$instance = new self();

		return self::$instance;
	}

	protected function __construct()
	{
		$this->id            = 0;
		$this->sys_name      = 'default';
		$this->lang_code     = 'eng';
		$this->locale        = 'en_US';
		$this->title         = "English";
		$this->base_filepath = DP_ROOT.'/languages/default';
		$this->has_user      = true;
		$this->has_admin     = true;
		$this->has_agent     = true;
	}
}
