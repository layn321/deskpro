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
 * @subpackage Addons
 */

namespace Application\DeskPRO\Publish;

use Application\DeskPRO\App;

use Doctrine\ORM\EntityManager;
use Application\DeskPRO\DBAL\Connection;

use Orb\Util\Arrays;
use Orb\Util\Util;

/**
 * Handles linking glossary words in texts
 */
class GlossaryHandler
{
	/**
	 * Entity manager
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * Plain database connection for raw queries
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $db;

	/**
	 * All words defined
	 * @var array
	 */
	protected $_words = null;

	protected $_defs = array();

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
		$this->db = $em->getConnection();
	}

	protected function _initWords()
	{
		if ($this->_words !== null) return;

		$this->_words = $this->db->fetchAllCol("
			SELECT word
			FROM glossary_words
		");
	}

	public function clear()
	{
		$this->_defs = array();
	}

	public function loadWords(array $words)
	{
		if (!$words) {
			return;
		}

		$load = array_diff($words, array_keys($this->_defs));
		if ($load) {
			$in_q = array_fill(0, count($load), '?');
			$in_q = implode(',', $in_q);

			$words = $this->db->fetchAllKeyValue("
				SELECT word, glossary_word_definitions.definition
				FROM glossary_words
				INNER JOIN glossary_word_definitions ON (glossary_words.definition_id = glossary_word_definitions.id)
				WHERE word IN ($in_q)
			", $load);

			$this->_defs = array_merge($this->_defs, $words);
		}
	}

	public function getWordDefs(array $words = array())
	{
		$this->loadWords($words);
		return $this->_defs;
	}

	/**
	 * @param $text
	 * @return array
	 */
	public function findWords($text)
	{
		$this->_initWords();

		$load = array();
		foreach ($this->_words as $word) {
			if (preg_match('#\b' . preg_quote($word, '#') . '\b#i', $text)) {
				$load[] = $word;
			}
		}

		return $load;
	}

	/**
	 * @param $text
	 * @return mixed
	 */
	public function processText($text)
	{
		$this->_initWords();

		$load = array();
		foreach ($this->_words as $word) {
			if (preg_match('#\b' . preg_quote($word, '#') . '\b#i', $text)) {
				$load[] = $word;
			}
		}

		$url_base = App::getRouter()->generate('agent_glossary_word_tip', array('word' => '__DP_WORD__'));

		foreach ($load as $word) {
			$word_h = htmlentities($word);
			$word_u = urlencode($word);

			$text = preg_replace_callback(
				'#(\b)(' . preg_quote($word, '#') . ')(\b)#i',
				function($m) use ($word_h, $word_u, $url_base) {
					$url = str_replace('__DP_WORD__', $word_u, $url_base);

					return $m[1]
						. '<span class="embedded-glossary-word tipped" data-glossary-word="'.$word_h.'" data-tipped="'.$url.'" data-tipped-options="ajax:true">'
						. $m[2]
						. '</span>'
						. $m[3];
				},
				$text,
				1
			);
		}

		return $text;
	}
}
