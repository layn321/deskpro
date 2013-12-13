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

namespace Application\DeskPRO\Translate\Loader;

use Orb\Util\Arrays;

/**
 * Loads phrases from filesystem and then database
 */
class DeskproLoader implements LoaderInterface
{
	/**
	 * @var SystemLoader
	 */
	protected $sys_loader;

	/**
	 * @var DbLoader
	 */
	protected $db_loader;


	/**
	 * @param SystemLoader $sys_loader
	 */
	public function setSystemLoader(SystemLoader $sys_loader)
	{
		$this->sys_loader = $sys_loader;
	}


	/**
	 * @param DbLoader $db_loader
	 */
	public function setDbLoader(DbLoader $db_loader)
	{
		$this->db_loader = $db_loader;
	}


	/**
	 * Loads phrase groups
	 *
	 * @param array $groups Groups to load
	 * @param \Application\DeskPRO\Entity\Language $language
	 * @return array
	 */
	public function load($groups, $language)
	{
		$phrases = array();

		if ($this->sys_loader) {
			$phrases = $this->sys_loader->load($groups, $language);
		}

		if ($this->db_loader) {
			$phrases = array_merge($phrases, $this->db_loader->load($groups, $language));
		}

		return $phrases;
	}
}
