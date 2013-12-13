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

use Application\DeskPRO\App;

use Application\DeskPRO\Entity\Language;
use DeskPRO\Kernel\KernelErrorHandler;
use Orb\Util\Strings;
use Orb\Util\Arrays;
use Orb\Util\Numbers;

use Application\DeskPRO\Translate\Loader\LoaderInterface;
use Application\DeskPRO\Entity\Language as LanguageEntity;
use Application\DeskPRO\People\PersonContextInterface;
use Application\DeskPRO\Entity\Person;

use Application\DeskPRO\EventDispatcher\DataEvent;

use Symfony\Component\EventDispatcher\EventDispatcher;

use Application\DeskPRO\HttpFoundation\Session;

/**
 * This class is responsible for loading phrases from a language stored in the database.
 *
 * <code>
 * $t = new Translate($language, $container);
 * $t->loadPhraseGroups(array('core', 'profile', 'tickets'));
 * echo $t['tickets.ask_a_question'];
 * echo $t->phrase('core.welcome_back_x', 'Christopher');
 * </code>
 *
 * @see Language
 * @see Phrase
 */
class Translate implements PersonContextInterface
{
	const EVENT_NO_PHRASE = 'DeskPRO_onTranslateNoPhrase';

	/**
	 * The phrases loaded so far
	 * @var array
	 */
	protected $_phrases = array();

	/**
	 * An array of groups that we need to load in the next batch
	 * @var array
	 */
	protected $_pending_groups = array();

	/**
	 * An array of groups we've already loaded
	 * @var array
	 */
	protected $_loaded_groups = array();

	/**
	 * An array of id=>entity of languages we've handled so far
	 * @var \Application\DeskPRO\Entity\Language[]
	 */
	protected $_loaded_languages = array();

	/**
	 * The default language, used when calling setLanguage with no argument
	 * This is the first language set
	 * @var \Application\DeskPRO\Entity\Language
	 */
	protected $_default_language = null;

	/**
	 * Set the language we're using right now by default
	 * @var \Application\DeskPRO\Entity\Language
	 */
	protected $_language = null;

	/**
	 * See getCountPhraseSelector()
	 * @var \Symfony\Component\Translation\MessageSelector
	 */
	protected $_phrase_selector = null;

	/**
	 * @var \Application\DeskPRO\Translate\ObjectPhraseNamer
	 */
	protected $_phrase_object_namer = null;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $_person_context;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $_default_person_context;

	/**
	 * @var \Symfony\Component\EventDispatcher\EventDispatcher
	 */
	protected $_event_dispatcher = null;
    /**
     * @var array
     */
    protected static $_missing_phrases = array();


	/**
	 * @param string $language The default language to use
	 * @param LoaderInterface $loader A loader that'll load phrases from somehwere
	 */
	public function __construct(LoaderInterface $loader, EventDispatcher $event_dispatcher = null)
	{
		$this->setLanguage(SystemLanguage::getInstance(), false);
		$this->loader = $loader;

        if(App::getConfig('debug.language_report_missing')) {
            \DpShutdown::add(array($this, 'reportMissingPhrases'));
        }

		$this->_event_dispatcher = $event_dispatcher;
	}


	/**
	 * @param Session $session
	 */
	public function setSession(Session $session = null)
	{
		if (!$session) {
			return;
		}

		$this->setLanguage($session->getLanguage());
		$this->setDefaultLanguage($session->getLanguage());
	}


	/**
	 * Set the current person context. This will also change the language to their preference.
	 *
	 * @var \Application\DeskPRO\Entity\Person
	 */
	public function setPersonContext(Person $person = null, $load_previous_groups = true)
	{
		if (!$person) {
			$person = $this->_default_person_context;
		}

		$this->_person_context = $person;

		if (!$this->_default_person_context) {
			$this->_default_person_context = $person;
		}

		if ($this->_language['id'] != $this->_person_context['id']) {
			$this->setLanguage($person->getLanguage(), $load_previous_groups);
		}
	}


	/**
	 * Temporarily resets the language to $language and runs $func, and then
	 * resets the language after.
	 *
	 * This will attempt to catch exceptions so the language is always reset
	 * afterwards.
	 *
	 * @param LanguageEntity $language
	 * @param callback     $func
	 */
	public function setTemporaryPersonContext(Person $person, $func)
	{
		$this->setPersonContext($person);

		$e = null;
		try {
			$func($this, $person->getLanguage());
		} catch (\Exception $e) {}

		$this->setPersonContext();

		if ($e) {
			throw $e;
		}
	}


	/**
	 * Reset to the default person context
	 */
	public function resetToDefaultPersonContext()
	{
		$this->setPersonContext(null);
	}


	/**
	 * Sets the default person context
	 *
	 * @var \Application\DeskPRO\Entity\Person
	 */
	public function setDefaultPersonContext(Person $person)
	{
		$this->_default_person_context = $person;
	}


	/**
	 * Get the current person context
	 *
	 * @return \Application\DeskPRO\Entity\Person
	 */
	public function getPersonContext()
	{
		return $this->_person_context;
	}


	/**
	 * Set the default language to used when fetching phrases. You can override this in phrase(),
	 * so setting the default here just makes those calls cleaner.
	 *
	 * $load_previous_groups means that all the phrase groups loaded so far are loaded for this
	 * new language. Thought being that this group will probably need the same phrases as the other.
	 *
	 * @param LanguageEntity $language
	 * @param bool $load_previous_groups
	 * @return void
	 */
	public function setLanguage(LanguageEntity $language = null, $load_previous_groups = true)
	{
		// If this is the first language, we'll consider it the "default"
		if ($language AND $this->_language === null) {
			$this->_default_language = $language;
		}

		if (!$language) {
			$language = $this->_default_language;
		}

		$last_id = null;
		if ($this->_language) {
			$last_id = $this->_language['id'];
		}

		$this->_language = $language;
		$this->_loaded_languages[$language['id']] = $language;

		if ($last_id AND $load_previous_groups AND isset($this->_loaded_groups[$last_id])) {
			$this->loadPhraseGroups($this->_loaded_groups[$last_id], $language);
		}
	}


	/**
	 * Resets the current language to the default.
	 *
	 * This is an alias of setLanguage(null)
	 */
	public function resetToDefaultLanguage()
	{
		return $this->setLanguage(null);
	}


	/**
	 * Temporarily resets the language to $language and runs $func, and then
	 * resets the language after.
	 *
	 * This will attempt to catch exceptions so the language is always reset
	 * afterwards.
	 *
	 * @param LanguageEntity $language
	 * @param callback     $func
	 */
	public function setTemporaryLanguage(LanguageEntity $language = null, $func)
	{
		$this->setLanguage($language);

		$e = null;
		try {
			$func($this, $language);
		} catch (\Exception $e) {}

		$this->setLanguage();

		if ($e) {
			throw $e;
		}
	}


	/**
	 * Set the default language. This just makes it easier to switch "back" to it when
	 * using setLanguage(null).
	 */
	public function setDefaultLanguage(LanguageEntity $language)
	{
		$this->_default_language = $language;
	}



	/**
	 * Get the currently set language.
	 *
	 * @return \Application\DeskPRO\Entity\Language
	 */
	public function getLanguage()
	{
		return $this->_language;
	}



	/**
	 * Add a group of phrases we want to load.
	 *
	 * @param  $group
	 */
	public function loadPhraseGroups($groups, LanguageEntity $language = null)
	{
		if (!is_array($groups)) {
			$groups = array($groups);
		}

		if (!$language) {
			$language = $this->_language;
		}

		$language_id = $language->id;

		if (!isset($this->_pending_groups[$language_id])) $this->_pending_groups[$language_id] = array();

		foreach ($groups as $group) {
			if (!in_array($group, $this->_loaded_groups)) {
				$this->_pending_groups[$language_id][] = $group;
			}
		}
	}



	/**
	 * When an unknown phrase is encountered in a group we haven't loaded yet,
	 * we'll load all pending phrase groups.
	 */
	protected function _loadPendingPhraseGroups()
	{
		if (!$this->_pending_groups) {
			return;
		}

		foreach ($this->_pending_groups as $language_id => $groups) {
			$groups = array_unique($groups);
			$groups = Arrays::removeFalsey($groups);

			if (!isset($this->_loaded_languages[$language_id])) {
				$this->_loaded_languages[$language_id] = App::getEntityRepository('DeskPRO:Language')->find($language_id);
			}
			$language = $this->_loaded_languages[$language_id];

			if (!isset($this->_phrases[$language_id])) $this->_phrases[$language_id] = array();
			$this->_phrases[$language_id] = array_merge($this->_phrases[$language_id], $this->loader->load($groups, $language));

			if (!isset($this->_loaded_groups[$language_id])) $this->_loaded_groups[$language_id] = array();
			$this->_loaded_groups[$language_id] = array_merge($this->_loaded_groups[$language_id], $groups);
		}

		$this->_pending_groups = array();
	}



	/**
	 * Get the phrase group from the name of a phrase. The phrase "deskpro.example_phrase"
	 * has the group named "deskpro".
	 *
	 * @param  string $phrase_name The name of the phrase
	 * @return string
	 */
	public function getPhraseGroupFromName($phrase_name)
	{
		if (!is_string($phrase_name)) {
			return false;
		}

		$pos = strpos($phrase_name, '.');
		if ($pos === false) {
			return '__default__';
		}

		$parts = Strings::rexplode('.', $phrase_name, 2);

		return $parts[0];
	}



	/**
	 * Get the phrase text for a given name.
	 *
	 * @param  string $phrase_name The phrase you want to fetch
	 * @param  Language|int $language The Language entity to use, or its id
	 * @return string
	 */
	public function getPhraseText($phrase_name, $language = null, $null_on_notfound = false)
	{
		if ($language === null) $language = $this->_language;
		if (!$phrase_name) return '';
		if (!is_string($phrase_name)) return '(' . gettype($phrase_name) . ')';

		if (Numbers::isInteger($language)) {
			$language_id = $language;
		} else {
			$language_id = $language['id'];
		}

		if (!isset($this->_phrases[$language_id][$phrase_name])) {
			$check_group = $this->getPhraseGroupFromName($phrase_name);

			if (!isset($this->_loaded_groups[$language_id]) OR !in_array($check_group, $this->_loaded_groups[$language_id])) {
				if (!isset($this->_pending_groups[$language_id])) $this->_pending_groups[$language_id] = array();
				$this->_pending_groups[$language_id][] = $check_group;

				$this->_loadPendingPhraseGroups();
				return $this->getPhraseText($phrase_name, $language_id, $null_on_notfound);
			}

			if ($null_on_notfound) {
				return null;
			} else {
				if (strpos($phrase_name, 'obj') === false) {
					$e = new \InvalidArgumentException("Missing phrase: $phrase_name");

					if (dp_get_config('debug.dev')) {
						KernelErrorHandler::logException($e, false);
					} else {
						KernelErrorHandler::logException($e, true, 'missing_phrase_' . $phrase_name);
					}
				}
			}

			return $this->_noPhrase($phrase_name, $language);
		}

		return $this->_phrases[$language_id][$phrase_name];
	}

    public static function reportMissingPhrases() {
        if(count(self::$_missing_phrases)) {
			try {
				$logger = App::createNewLogger('missing_phrase_logger', null);
				$message = "'The following phrases are missing:\n";

				foreach(self::$_missing_phrases as $phrase) {
					$message .= "{$phrase}\n";
				}

				$logger->log($message, 'WARN', array(
					'subject'    => '[DeskPRO Missing Phrases]',
					'message'    => $message
				));
			} catch(\Exception $e) {}
        }
    }

	/**
	 * Called when there is no such phrase name. By default this simply
	 * returns null. But an event might change this.
	 *
	 * @param string $phrase_name
	 * @param Language $language
	 * @return string
	 */
	protected function _noPhrase($phrase_name, $language)
	{
		$phrase = null;

        if(!isset(self::$_missing_phrases[$phrase_name])) {
            self::$_missing_phrases[$phrase_name] = $phrase_name;
        }

		if ($this->_event_dispatcher) {
			$evdata = new DataEvent(array(
				'phrase_name' => $phrase_name,
				'language' => $language,
				'return' => $phrase,
			));
			$this->_event_dispatcher->dispatch(self::EVENT_NO_PHRASE, $evdata);

			$phrase = $evdata->return;
		}

		return $phrase;
	}


	/**
	 * Same as getPhraseText, except we run the phrase through the selector
	 * to fetch the correct plural phrase for the given $count.
	 *
	 * @param  string $phrase_name The phrase you want to fetch
	 * @param  int $count The count
	 * @param  Language|int $language The Language entity to use, or its id
	 * @return string
	 */
	public function getPhraseTextCount($phrase_name, $count, $language = null)
	{
		$phrase_text = $this->getPhraseText($phrase_name);
		if (!$phrase_text) {
			return null;
		}

		if ($language === null) {
			$language = $this->_language;
		} elseif (Numbers::isInteger($language) && isset($this->_loaded_languages[$language])) {
			$language = $this->_loaded_languages[$language];
		}

		try {
			$phrase = $this->getCountPhraseSelector()->choose($phrase_text, $count, $language->getLocale());
		} catch (\InvalidArgumentException $e) {
			try {
				// Try again with en_US locale in case
				// Could be an untranslated phrase (which defaults to eng), but then the locale would be passed as the lang,
				// which could use different rules and cause the chooser to fail.
				$phrase = $this->getCountPhraseSelector()->choose($phrase_text, $count, 'en_US');
			} catch (\InvalidArgumentException $e) {
				$phrase = $e->getMessage();
			}
		}

		return $phrase;
	}



	/**
	 * Get a phrase from a compatible object that knows how to describe a phrase ID.
	 *
	 * $property may be null, in which case it's expected to be a 'title' or 'name',
	 * or the only item on the object that is translatable.
	 *
	 * @param stdObject $object The object to get a phrase for
	 * @param string $property A specific thing in the object to translate
	 * @param  Language|int $language The Language entity to use, or its id
	 * @return string
	 */
	public function getPhraseObject($object, $property = null, $language = null)
	{
		#------------------------------
		# Standard translation interfaces
		#------------------------------

		if ($object instanceof DelegatePhraseInterface) {
			return $object->getPhrase($this, $language);

		} else if ($object instanceof HasPhraseName) {
			$phrase_name = $object->getPhraseName($property, $this);
			$phrase_text = false;

			if ($phrase_name && $this->hasPhrase($phrase_name, $language)) {
				$phrase_text = $this->phrase($phrase_name, array(), $language);
			}

			if (!$phrase_text) {
				$phrase_text = $object->getPhraseDefault($property, $this);
			}

			if ($phrase_text) return $phrase_text;

			return '';
		}

		#------------------------------
		# Phrase namer inspects objects..
		#------------------------------

		$namer = $this->getObjectPhraseNamer();
		$phrase_name = $namer->getPhraseName($object, $property);
		$phrase_text = false;

		if ($phrase_name && $this->hasPhrase($phrase_name, $language)) {
			$phrase_text = $this->phrase($phrase_name, array(), $language);
		}

		if (!$phrase_text) {
			$phrase_text = $this->getObjectPhraseNamer()->getPhraseDefault($object, $property);
		}

		if ($phrase_text) return $phrase_text;

		// No phrase
		return '';
	}



	/**
	 * Fetch a phrase from the currently set language, and insert the passed variables into the placeholders.
	 *
	 * If $vars contains a 'count' value, then the phrase is expected to be a pluralized and will be passed
	 * through the message selector.
	 *
	 * If $phrase_name is an object, it should be a compatible phrase object (see ObjectPhraseNamer).
	 * If it's an array, the first item should be an object and the second a string, which is used as the
	 * 'property'.
	 *
	 * <code>
	 * echo $translate->phrase('core.welcome_back_x', array('name' => 'Christopher'));
	 * </code>
	 *
	 * @param  string $phrase_name  The phrase to fetch
	 * @param  array  $vars         Variables to place into the phrase
	 * @param  Language|int $language The Language entity to use, or its id
	 * @return string
	 */
	public function phrase($phrase_name, array $vars = array(), $language = null)
	{
        $debug = App::getConfig('debug.language_test_mode');

		if (!$debug && defined('DP_INTERFACE') && DP_INTERFACE == 'agent' && strpos($phrase_name, 'agent') === 0) {
			try {
				$debug = App::getSetting('core.agent_translate_debug');
			} catch (\Exception $e) {}
		}

		if (!$debug && isset($_COOKIE['dp_dev_langdebug'])) {
			$debug = $_COOKIE['dp_dev_langdebug'];
		}

		if ($debug == 'user' AND $phrase_name != 'agent.general.x_is_y') {
			if (substr($phrase_name, 0, 4) != 'user') {
				echo $phrase_name;
				die();
			}
		}

		if ($debug == 'japanese') {

			// Don't try this as single string array. PHP can't handle UTF8 like that :).
			$chars = array(
				'一', '丁', '丂', '七', '丄', '丅', '万', '丈', '三', '上', '下', '丌', '不', '与', '丏', '丐', '丑', '丒',
				'且', '丕', '世', '丗', '丘', '丙', '丞', '丟', '両', '丣', '两', '並', '丨', '丩', '个', '丫', '丬', '中',
				'丮', '丯', '丰', '丱', '串', '丳', '临', '丵', '丶', '丸', '丹', '主', '丼', '丿', '乀', '乁', '乂', '乃',
				'乄', '久', '乇', '么', '之', '乍', '乎', '乏', '乑', '乕', '乖', '乗', '乘', '乙', '乚', '乜', '九', '乞',
				'也', '乢', '乣', '乨', '乩', '乱', '乳', '乴', '乵', '乹', '乾', '乿', '亀', '亂', '了', '予', '争', '亊',
				'事', '二', '亍', '于');

			$output = '';

			for($i = 0; $i < 4; $i++) {
				$output .= $chars[rand(0, count($chars) - 1)];
			}

			return $output;
		}

		if (is_object($phrase_name) OR (is_array($phrase_name) AND is_object($phrase_name[0]))) {
			if (is_array($phrase_name)) {
				list ($object, $property) = $phrase_name;
			} else {
				$object = $phrase_name;
				$property = null;
			}

			$phrase_text = $this->getPhraseObject($object, $property, $language);
		} elseif (isset($vars['count'])) {
			try {
				$phrase_text = $this->getPhraseTextCount($phrase_name, $vars['count'], $language);
			} catch (\Exception $e) {
				// Fall back on just using a normal phrase without any pluralising
				// In case user modified phrase to remove the plural syntax
				$phrase_text = $this->getPhraseText($phrase_name, $language);
			}
		} else {
			$phrase_text = $this->getPhraseText($phrase_name, $language);
		}

		if (!$phrase_text) $phrase_text = '';

		$phrase_text = $this->replaceVarsInString($phrase_text, $vars);

		// A second pass detects phrase. replacements that might've been put in by replacements themselves
		$m = null;
		if (preg_match_all('#{{phrase\.([a-zA-Z0-9\-_\.]+)}}#', $phrase_text, $m)) {
			foreach ($m[1] as $sub_phrase_name) {
				if ($sub_phrase_name == $phrase_name) continue; //prevent loops
				$sub_phrase_text = $this->phrase($sub_phrase_name, $vars, $language);
				$phrase_text = str_replace("{{phrase.$sub_phrase_name}}", $sub_phrase_text, $phrase_text);
			}
		}

		if ($debug == 'double_length') {
			return $phrase_text . ' ' . $phrase_text;
		} else if ($debug == 'half_length') {
			$length = strlen($phrase_text);
			return substr($phrase_text, round($length / 2));
		} else if($debug == 'package') {
			if(!is_string($phrase_name)) {
				return '!'.strtoupper(typeof($phrase_name)).'!';
			}
			else {
				$parts = explode('.', $phrase_name);
				return '!'.strtoupper($parts[0]).'!';
			}
		} else if($debug == 'package_prefix') {
			$p_prefixes = array(
				'agent' => 'Ѯ',
				'admin' => 'Ѿ',
				'user' => 'Ѱ',
				'object' => 'Ѳ',
				'resource' => 'Ѻ',
				'array' => 'Г',
				'unknown' => 'Ц',
			);

			if(!is_scalar($phrase_name)) {
				$package = typeof($phrase_name);
			}
			else {
				$parts = explode('.', $phrase_name);
				$package = $parts[0];
			}

			if(!isset($p_prefixes[$package])) {
				$package = 'unknown';
			}

			return $p_prefixes[$package].$phrase_text;
		} else if ($debug == 'prefix') {
			return '@'.$phrase_text;
		} else if ($debug == 'wrap') {
			return '^'.$phrase_text . '^';
		}

		return $phrase_text;
	}


	/**
	 * Replaces {{vars}} form $vars in $phrase_text.
	 *
	 * @param $phrase_text
	 * @param array $vars
	 * @return string
	 */
	public function replaceVarsInString($phrase_text, array $vars = array())
	{
		if ($vars) {
			$phrase_text = preg_replace_callback('#\{\{\s*([a-zA-Z0-9_]+)\s*\}\}#', function ($m) use ($vars) {
				$name = $m[1];

				if (isset($vars[$name])) {
					return $vars[$name];
				} elseif (isset($vars['_context'][$name])) {
					return $vars['_context'][$name];
				}

				return '';
			}, $phrase_text);

			$phrase_text = preg_replace_callback('#\{\{\s*([a-zA-Z0-9_]+)\.([a-zA-Z0-9_]+)\s*\}\}#', function ($m) use ($vars) {
				$name = $m[1];
				$prop = $m[2];

				if (isset($vars[$name])) {
					if (isset($vars[$name][$prop])) {
						return $vars[$name][$prop];
					} elseif (isset($vars[$name]->$prop)) {
						return $vars[$name]->$prop;
					}
				} elseif (isset($vars['_context'][$name])) {
					if (isset($vars['_context'][$name][$prop])) {
						return $vars['_context'][$name][$prop];
					} elseif (isset($vars['_context'][$name]->$prop)) {
						return $vars['_context'][$name]->$prop;
					}
				}

				return '';
			}, $phrase_text);
		}

		return $phrase_text;
	}


	/**
	 * Just like date() except D, l, F and M are replaced by translated strings.
	 *
	 * @param string $format A date format string
	 * @param int|\DateTime $date_or_ts A DateTime object or a timestamp
	 */
	public function date($format, $date_or_ts = null, $prefix = 'user.time.')
	{
		if (!$date_or_ts) {
			$date_or_ts = time();
		}

		$ts = $date_or_ts;
		if ($ts instanceof \DateTime) {
			$tz_offset = $ts->format('P');

			// getTimestamp will return the underlaying timestamp of the Date,
			// it doesnt apply any timezone offsets. So we'll need to convert it now
			$ts = \Orb\Util\Dates::makeUtcDateTime($date_or_ts);
			$ts = $ts->getTimestamp();
		} else {
			$tz_offset = '+00:00';
		}

		// D: Mon
		// l: Monday
		// F: January
		// M: Jan

		$format = preg_replace('#(?<!\\\\)([DlFMP])#', '\\\\D\\\\P-\\\\$1', $format);
		$date = date($format, $ts);

		$tr = $this;
		$date = preg_replace_callback('#DP\-([DlFMP])#', function($m) use ($prefix, $tr, $ts, $tz_offset) {

			switch ($m[1]) {
				case 'D':
					$phrase_name = $prefix . 'short-day_' . strtolower(date('l', $ts));
					break;
				case 'l':
					$phrase_name = $prefix . 'long-day_' . strtolower(date('l', $ts));
					break;
				case 'F':
					$phrase_name = $prefix . 'long-month_' . strtolower(date('F', $ts));
					break;
				case 'M':
					$phrase_name = $prefix . 'short-month_' . strtolower(date('F', $ts));
					break;
				case 'P':
					return $tz_offset;
				default:
					// never matches
					return 'unkown segment';
			}

			return $tr->getPhraseText($phrase_name);
		},  $date);

		return $date;
	}


	/**
	 * Check to see if a phrase exists
	 *
	 * @param string $phrase_name
	 * @return bool
	 */
	public function hasPhrase($phrase_name, $language = null)
	{
		if ($this->getPhraseText($phrase_name, $language, true) !== null) {
			return true;
		}

		return false;
	}



	/**
	 * @return \Symfony\Component\Translation\MessageSelector
	 */
	public function getCountPhraseSelector()
	{
		if ($this->_phrase_selector !== null) return $this->_phrase_selector;

		$this->_phrase_selector = new \Symfony\Component\Translation\MessageSelector();
		return $this->_phrase_selector;
	}

	/**
	 * @return \Application\DeskPRO\Translate\ObjectPhraseNamer
	 */
	public function getObjectPhraseNamer()
	{
		if ($this->_phrase_object_namer !== null) return $this->_phrase_object_namer;

		$this->_phrase_object_namer = new \Application\DeskPRO\Translate\ObjectPhraseNamer();

		return $this->_phrase_object_namer;
	}


	/**
	 * Given an Entity this returns the phrase for the first lang in $lang_priority.
	 *
	 * $lang_priority is an array of languages or lang ID's.
	 * Or you can pass multiple values (variable number of args) and all
	 * trailing args will be considered lang Ids.
	 *
	 * @param mixed  $object
	 * @param string $property
	 * @param array  $lang_priority
	 * @return string
	 */
	public function objectChoosePhraseText($object, $property, $lang_priority)
	{
		$args = func_get_args();
		array_shift($args);
		array_shift($args);

		#------------------------------
		# Build priority array
		#------------------------------

		// Verifies lang params, converts lang IDs to objects

		$lang_priority = array();
		foreach ($args as $arg) {
			if (!is_array($arg)) {
				$arg = array($arg);
			}

			foreach ($arg as $l) {
				if (!$l) continue;

				if (is_numeric($l)) {
					$l = App::getContainer()->getLanguageData()->get($l);
				}

				if ($l instanceof Language) {
					$lang_priority[] = $l;
				}
			}
		}

		#------------------------------
		# Pick the lang text
		#------------------------------

		$obj_lang_repos = App::getContainer()->getObjectLangRepository();

		foreach ($lang_priority as $lang) {
			$obj_lang_repos->preloadObject($lang, $object);
		}

		$rec = $obj_lang_repos->getRec($lang_priority, $object, $property, true);

		if (!$rec) {
			return '';
		}

		return $rec->value;
	}
}
