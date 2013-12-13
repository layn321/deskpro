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

/**
 * Loads phrases from the database
 */
class DbLoader implements LoaderInterface
{
	/**
	 * Plain database connection for raw queries
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $dbconn;

	/**
	 * @var array
	 */
	protected $loaded_langs = array();

	/**
	 * @param \Application\DeskPRO\DBAL\Connection $dbconn
	 */
	public function __construct(\Application\DeskPRO\DBAL\Connection $dbconn)
	{
		$this->dbconn = $dbconn;
	}

	public function load($groups, $language)
	{
		// No lang means we have nothing to do here,
		// usually means we're in an area without db yet (pre install?)
		if (!$language OR !$language['id']) {
			return array();
		}

		// The LoaderInterface expects to load groups as they're needed,
		// but thats expensive in the db so we load then entire thing in one query
		// - This check prevents the query from re-running when another call is made
		if (isset($this->loaded_langs[$language['id']])) {
			return $this->loaded_langs[$language['id']];
		}

		$this->loaded_langs[$language['id']] = array();

		$langs = array();
		$langs[] = 1; // default deskpro lang

		if ($language) {
			$langs[] = $language->getId(); // the chosen lang
		}

		// null contains non-language language like cat names and such
		$langs[] = '0';

		$specific_lang_ids = array(0);
		if ($language) {
			$specific_lang_ids[] = $language->getId();
		}
		$specific_lang_ids = implode(',', $specific_lang_ids);

		$langs = array_unique($langs, \SORT_STRING);

		$lang_in = implode(',', $langs);

		// Depending on the interface, we load user, user+agent or user+agent+admin
		if (DP_INTERFACE == 'admin' || DP_INTERFACE == 'cron' || DP_INTERFACE == 'cli' || (defined('DP_BOOT_MODE') && DP_BOOT_MODE == 'dp')) {
			$sql = "
				SELECT name, phrase, original_phrase
				FROM phrases
				WHERE language_id IN ($lang_in)
				ORDER BY language_id ASC
			";
		} elseif (DP_INTERFACE == 'agent') {
			$sql = "
				SELECT name, phrase, original_phrase
				FROM phrases
				WHERE
					language_id IN ($lang_in) AND (
						groupname LIKE 'agent.%' OR groupname LIKE 'user.%' OR groupname LIKE \"obj_%\" OR groupname = \"custom\"
					)
				ORDER BY language_id ASC
			";
		} else {
			$sql = "
				SELECT name, phrase, original_phrase
				FROM phrases
				WHERE
					language_id IN ($lang_in) AND (
						groupname LIKE 'agent.%' OR groupname LIKE 'user.%' OR groupname LIKE \"obj_%\" OR groupname = \"custom\"
					)
				ORDER BY language_id ASC
			";
		}

		$q = $this->dbconn->query($sql);

		$phrases = array();
		while ($r = $q->fetch()) {

			if ($r['name'] == 'user.general.helpdesk_by') {
				continue;
			}

			$phrase_text = $r['phrase'];
			if (empty($phrase_text)) {
				$phrase_text = $r['original_phrase'];
			}

			$phrases[$r['name']] = $phrase_text;
		}

		$this->loaded_langs[$language['id']] = $phrases;

		return $phrases;
	}
}
