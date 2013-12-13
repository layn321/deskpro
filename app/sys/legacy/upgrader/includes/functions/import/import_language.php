<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id: import_language.php 6820 2010-04-22 20:45:25Z chroder $
// +-------------------------------------------------------------+
// | File Details:
// | - import settings
// +-------------------------------------------------------------+

require_once(INC . 'classes/class_XMLDecode.php');

/**
* Exports languages words to xml
*	LOGIC:
*		- 1. If word does not exist, create it
*		- 2. If word exists and has not been modified, update it
*		- 3. If word exists, and has been modified, upgrade the backup and mark the change.
*
* @access	public
*
* @param	int	id of language to export
* @param	boolean	flag whether to save to file or not
* if True saves to file
*
* @return	 string	xml contents
*/
function import_english() {

	return;

	global $db, $cache2, $settings;

	// need language to start with for other languages
	$english = $db->query_return("SELECT * FROM languages WHERE id = 1");
	$master_start = $english['master'];

	$settings = get_settings();

	give_default($master_start, 3);

	require_once(INC . 'classes/class_DpBuilds.php');
	$dpbuilds = new DpBuilds();

	/************
	* Decode the Language File
	************/

	$xml_parser = new class_XMLDecode();
	$xml = $xml_parser->parse_file('install/data/languages/english/' . $settings['deskpro_version_internal'] . '.xml');

	$xml['version'] = $dpbuilds->convertIfLegacy($xml['version']);
	$xml['master'] = $dpbuilds->convertIfLegacy($xml['master']);

	/************
	* Force setup of English as Language ID : 1
	************/

	$db->query("
		DELETE FROM languages
		WHERE id = 1
	");

	$db->query("
		INSERT INTO languages SET
			id = 1,
			name = '" . $db->escape($xml['name']) . "',
			direction = '" . $db->escape($xml['direction']) . "',
			isocode = '" . $db->escape($xml['isocode']) . "',
			contenttype = '" . $db->escape($xml['contenttype']) . "',
			deskproid = '" . $db->escape($xml['deskproid']) . "',
			version = '" . $db->escape($xml['version']) . "',
			master = " . intval($xml['master']) . ",
			flag = 'us'
	");

	if (!$db->query_amatch('languages', 'base = 1')) {

		$db->query("
			UPDATE languages SET
				base = 1,
				is_selectable = 1
			WHERE id = 1
		");
	}

	/************
	* Delete categories & wordref
	************/

	$db->query("DELETE FROM template_words_cat");
	$db->query("DELETE FROM template_words_cat_link");

	/************
	* Get current words
	************/

	$words = $db->query_return_array("
		SELECT wordref, text, backuptext
		FROM template_words
		WHERE
			language = 1
		AND cust = 0
	", 'wordref');

	/************
	* Create the category
	************/

	foreach ($xml['words']['category'] AS $category) {

		$db->query("
			INSERT INTO template_words_cat SET
				intname = '" . $db->escape($category['intname']) . "',
				name = '" . $db->escape($category['name']) . "',
				displayorder = '" . $db->escape($category['displayorder']) . "'
		");

		/************
		* Link wordref to this category
		************/

		foreach ($category['word'] AS $word) {

			$ref = $word['name'];

			$db->query("
				INSERT INTO template_words_cat_link SET
					wordref = '" . $db->escape($word['name']) . "',
					category = '" . $db->escape($category['intname']) . "'
			");

			/***************
			* Has the word actually changed? We need this so we can determine how it affects
			other languages
			***************/

			if ($word['value'] != $words[$ref]['backuptext']) {
				$changed_words[] = $ref;
			}

			/***************
			* Update word : WORD CHANGED
			***************/
			if (is_array($words[$ref]) AND ($words[$ref]['text'] != $words[$ref]['backuptext'])) {

				$db->query("
					UPDATE template_words SET
						backuptext = '" . $db->escape($word['value']) . "',
						version = " . intval($xml['version']) . "
					WHERE wordref = '" . $db->escape($word['name']) . "'
						AND language = 1
				");

			/***************
			* Update word : WORD NOT CHANGED
				- also update the text
			***************/
			} else if (is_array($words[$ref])) {

				$db->query("
					UPDATE template_words SET
						text = '" . $db->escape($word['value']) . "',
						backuptext = '" . $db->escape($word['value']) . "',
						version = " . intval($xml['version']) . "
					WHERE wordref = '" . $db->escape($word['name']) . "'
						AND language = 1
				");

			/***************
			* Insert word
			***************/
			} else {

				$db->query("
					INSERT INTO template_words SET
						language = 1,
						wordref = '" . $db->escape($word['name']) . "',
						text = '" . $db->escape($word['value']) . "',
						backuptext = '" . $db->escape($word['value']) . "',
						version = " . intval($xml['version']) . "
				");
			}
		}
	}

	/************
	* Delete any words that do not exist inside template_words_cat_link, clearly ignoring any custom ones
	************/

	$db->query("
		SELECT wordref FROM template_words_cat_link
	");

	unset($words);
	while ($result = $db->row_array()) {
		$words[] = $result['wordref'];
	}

	$db->query("
		DELETE FROM template_words
		WHERE wordref NOT IN " . array2sql($words) . "
			AND cust = 0
	");

	/************
	* Update all words in other languages to this version where their version number is > than what we started with.
	************/

	if (is_array($changed_words)) {

		$db->query("
			UPDATE template_words
			SET version = $settings[deskpro_version_internal]
			WHERE
				wordref NOT IN " . array2sql($changed_words) . "
			AND language != 1
			AND version >= $master_start
			AND cust = 0
		");

	}

	// maybe there are no changed_words, then we want to upgrade the language file itself do we not?
	// don't do if we are installing
	if (!is_array($changed_words) AND is_array($settings) AND $settings['deskpro_version_internal']) {

		$db->query("
			UPDATE languages
			SET version = $settings[deskpro_version_internal]
			WHERE master >= $master_start
		");

	}

	################### HELP ###################

	/*******************************
	* Delete all the user_help entries (that are not custom)
	*******************************/

	// delete all the entries from user_help
	$db->query("
		DELETE FROM user_help WHERE is_custom = 0
	");

	// get all the changed user_help_entries
	$db->query("
		SELECT * FROM user_help_entries
		WHERE language = 1
		AND changed = 1
	");

	while ($result = $db->row_array()) {
		$help_entry_current[$result['helpname']] = $result['helpname'];
	}

	// delete all the user_help_entries
	$db->query("
		DELETE FROM user_help_entries
		WHERE language = 1
	");

	foreach ($xml['help']['category'] AS $category) {

		/***********************
		Deal with the category
		***********************/

		// make sure we have the key category (only one per language)
		$db->query("
			REPLACE INTO user_help_cats SET
				name = '" . $db->escape($category['name']) . "',
				displayorder = " . intval($category['displayorder']) . "
		");

		// add the new category entry
		$db->query("
			REPLACE INTO user_help_cats_entries SET
				catname = '" . $db->escape($category['name']) . "',
				entry = '" . $db->escape($category['category_entry']['value']) . "',
				language = 1
		");

		/***********************
		Deal with the entries
		***********************/

		// loop on entries
		foreach ($category['entry'] AS $entry) {

			// update the master entry (1 per language)
			$db->query("
				INSERT INTO user_help SET
					name = '" . $db->escape($entry['name']) . "',
					displayorder = '" . $db->escape($entry['displayorder']) . "',
					category = '" . $db->escape($category['name']) . "'
			");

			// replace into user_help_entries
			$db->query("
				INSERT INTO user_help_entries SET
					language = 1,
					title = '" . $db->escape($entry['title']['value']) . "',
					helpentry = '" . $db->escape(ifr($help_entry_current[$entry['name']], $entry['helpentry']['value'])) . "',
					helpname = '" . $db->escape($entry['name']) . "',
					backup = '" . $db->escape($entry['helpentry']['value']) . "'
			");

		}
	}
}

/*
	LOGIC:
		- 1. If word does not exist, create it
		- 2. If word exists and has not been modified, update it
		- 3. If word exists, and has been modified, upgrade the backup and mark the change.

	LANGUAGE:
		- If language does not exist, create it.

	$id allows to set the id to overwrite.
	Should only overwrite custom languages otherwise we should just be doing a standard import to overwrite two deskproid languages
*/

// $file: filename to import
// $id: id of the current language we are importing
// $nodeskproid: we are using the deskproid from the file to work out which language to upgrade
// $xml - not a file, we pass the XML content instead

/*
	- Handling version number

	When we import a language there is a good chance the language will be an older version than the english
	language currently installed. What we want however is to set the phrases that have not changed since the
	version this language was created against to the current version so they do not need to be re-translated.
	We do this using the files in /install/data/languages/english as a comparison
*/

function import_language($file, $id=0, $nodeskproid=false, $xml=false) {

	global $db, $settings;

	require_once(INC . 'classes/class_DpBuilds.php');
	$dpbuilds = new DpBuilds();

	if (!$xml) {

		// check for utf-8 in first 200 chars
		$data = file_get_contents(ROOT . '/install/data/languages/' . $file . '.xml');

		$count = stripos($data, 'UTF-8');

		if (in_string('UTF-8', $file) OR ($count > 1 AND $count < 200)) {
			$xml_parser = new class_XMLDecode('UTF-8');
		} else {
			$xml_parser = new class_XMLDecode();
		}

		$xml = $xml_parser->parse_file('install/data/languages/' . $file . '.xml');

	}

	$xml['master'] = $dpbuilds->convertIfLegacy($xml['master']);

	if ($nodeskproid === 'auto') {
		if ($xml['deskproid']) {
			$nodeskproid = false;
		} else {
			$nodeskproid = true;
		}
	}


	/**************
	* If we are overwriting a specific language
		- can only overwrite custom languages
	**************/
	if ($id) {

		$db_language = $db->query_return("
			SELECT * FROM languages WHERE id = '" . $db->escape($id) . "'
		");

		if ($db_language['deskproid']) {
			mistake('Language is not a custom language. Overwrite is not possible');
		}

	/**************
	* If we we are basing on the deskproid
	**************/
	} else if (!$nodeskproid) {

		$db_language = $db->query_return("
			SELECT * FROM languages WHERE deskproid = '" . $db->escape($xml['deskproid']) . "'
		");
	}

	if (!is_array($db_language)) {

		/**************
		* Create the language
		**************/

		// If its a translator lang we're importing (ie from upload)
		// then we should make sure the 'translator' features are enabled
		if (!$xml['deskproid'] AND !$settings['language_translate_on']) {
			update_setting('language_translate_on', 1);
		}

		if ($nodeskproid) {
			unset($xml['deskproid']);
		}

		$db->query("
			INSERT INTO languages SET
				name = '" . $db->escape($xml['name']) . "',
				is_selectable = 1,
				isocode = '" . $db->escape($xml['isocode']) . "',
				contenttype = '" . $db->escape($xml['contenttype']) . "',
				direction = '" . $db->escape($xml['direction']) . "',
				deskproid = '" . $db->escape($xml['deskproid']) . "',
				version = '" . $db->escape($xml['version']) . "',
				flag = '" . $db->escape($xml['flag']) . "',
				master = '" . $db->escape($xml['master']) . "'
				" . iff($id, ', id = ' . intval($id)) . "
		");

		$id = $db->insert_id();

	} else {

		$id = $db_language['id'];

		/**************
		* Update the language
		**************/

		$db->query("
			UPDATE languages SET
				name = '" . $db->escape($xml['name']) . "',
				isocode = '" . $db->escape($xml['isocode']) . "',
				contenttype = '" . $db->escape($xml['contenttype']) . "',
				direction = '" . $db->escape($xml['direction']) . "',
				version = '" . $db->escape($xml['version']) . "',
				flag = '" . $db->escape($xml['flag']) . "',
				master = '" . $db->escape($xml['master']) . "'
			WHERE deskproid = '" . $db->escape($xml['deskproid']) . "'
		");

	}

	/**************
	* Remove any old entries from data
	**************/

	$data = get_data('language_upgrade');
	$actions = $data[$id];

	// we want to loop and remove anything that is this version or less
	if (is_array($actions)) {

		foreach ($actions AS $key => $var) {
			if ($key <= $xml['master']) {
				unset($actions[$key]);
			}
		}

		$data[$id] = $actions;
		update_data('language_upgrade', $data);

	}

	/**************
	* Get the english file this was translated against
	**************/

	$old_english = array();

	if ($xml['master']) {
		$xml_parser = new class_XMLDecode();
		$xml_old_english = $xml_parser->parse_file('install/data/languages/english/' . $xml['master'] . '.xml');

		foreach ($xml_old_english['words']['category'] AS $category) {
			foreach ($category['word'] AS $word) {
				$old_english[$word['name']] = $word['value'];
			}
		}
	}

	// now let's make a list of words that have changed since then (we check the english backup text entries for this)
	$english_now = $db->query_return_array_id("SELECT wordref, backuptext FROM template_words WHERE language = 1", 'backuptext', 'wordref');

	/**************
	* If a deskproid, check if any langs
	* are linked to it. Unchanged words
	* will need to be updated.
	**************/

	if ($xml['deskproid']) {
		$linked_langs = $db->query_return_array_id("
			SELECT id
			FROM languages
			WHERE linked_deskproid = '" . $db->escape($xml['deskproid']) . "'
		", 'id', '');
	}

	if ($linked_langs) {
		$linked_langs_sql = array2sql($linked_langs);
	} else {
		$linked_langs = array();
		$linked_langs_sql = '';
	}

	/**************
	* Get all the words
	**************/

	$words = $db->query_return_array("
		SELECT wordref, text, backuptext
		FROM template_words
		WHERE
			language = " . intval($id) . "
		AND cust = 0
	", 'wordref');

	/**************
	* Loop the words
	**************/

	$xml_words = array();

	foreach ($xml['words']['category'] AS $category) {

		if (!is_array($category['word'])) {
			continue;
		}

		foreach ($category['word'] AS $word) {

			$ref = $word['name'];

			$xml_words[] = $ref;

			/***************
			* We need to work out version of this word in relation to english
			***************/

			if ($xml['master'] == $settings['deskpro_version_internal']) {
				$version = $settings['deskpro_version_internal'];

			} else {

				// has the english word changed?
				if ($old_english[$ref] == $english_now[$ref]) {
					$version = $settings['deskpro_version_internal'];
				} else {
					$version = $xml['master'];
				}

			}

			/***************
			* Update word : WORD CHANGED
			***************/
			if (is_array($words[$ref]) AND ($words[$ref]['text'] != $words[$ref]['backuptext'])) {

				$db->query("
					UPDATE template_words SET
						backuptext = '" . $db->escape($word['value']) . "',
						version = " . $version . "
					WHERE wordref = '" . $db->escape($word['name']) . "'
						AND language = " . intval($id) . "
				");

				if ($linked_langs_sql) {
					$db->query("
						UPDATE template_words
						SET
							backuptext = '" . $db->escape($word['value']) . "',
							version = " . $version . "
						WHERE
							wordref = '" . $db->escape($word['name']) . "'
							AND language IN $linked_langs_sql
					");
				}


			/***************
			* Update word : WORD NOT CHANGED
				- also update the text
			***************/
			} else if (is_array($words[$ref])) {

				$db->query("
					UPDATE template_words SET
						text = '" . $db->escape($word['value']) . "',
						backuptext = '" . $db->escape($word['value']) . "',
						version = " . $version . "
					WHERE wordref = '" . $db->escape($word['name']) . "'
						AND language = " . intval($id) . "
				");

				if ($linked_langs_sql) {
					// Words that havent changed can be updated now
					$db->query("
						UPDATE template_words
						SET
							text = '" . $db->escape($word['value']) . "',
							backuptext = '" . $db->escape($word['value']) . "',
							version = " . $version . "
						WHERE
							wordref = '" . $db->escape($word['name']) . "'
							AND text = backuptext
							AND language IN $linked_langs_sql
					");

					// Else update the backuptext
					$db->query("
						UPDATE template_words
						SET
							backuptext = '" . $db->escape($word['value']) . "'
						WHERE
							wordref = '" . $db->escape($word['name']) . "'
							AND text != backuptext
							AND language IN $linked_langs_sql
					");
				}

			/***************
			* Insert word
			***************/
			} else if (!$word['translate']) {

				$db->query("
					INSERT INTO template_words SET
						language = " . intval($id) . ",
						wordref = '" . $db->escape($word['name']) . "',
						text = '" . $db->escape($word['value']) . "',
						version = " . $version . ",
						backuptext = '" . $db->escape($word['value']) . "'
				");

				if ($linked_langs_sql) {

					foreach ($linked_langs as $linked_lang_id) {
						$db->query("
							INSERT INTO template_words SET
								language = " . intval($linked_lang_id) . ",
								wordref = '" . $db->escape($word['name']) . "',
								text = '" . $db->escape($word['value']) . "',
								version = " . $version . ",
								backuptext = '" . $db->escape($word['value']) . "'
						");
					}
				}
			}
		}
	}

	// Any linked langs that had no custom words changed are now up to date to $version
	if ($linked_langs) {
		foreach ($linked_langs as $linked_lang_id) {
			$num = $db->query_return_first("
				SELECT COUNT(*)
				FROM template_words
				WHERE
					version < $version
					AND language = $linked_lang_id
			");

			if (!$num) {
				$db->query("
					UPDATE languages
					SET master = '{$xml['master']}'
					WHERE id = $linked_lang_id
				");
			}
		}
	}


	/************
	* Delete any words that arent in the XML file
	* - Ie. upgrading a lang from the site that we reverted to a
	*   previous revision
	************/

	if ($xml['deskproid'] AND $words) {
		$lang_ids = $linked_langs;
		$lang_ids[] = $id;
		$remove_words = array();

		foreach ($words as $ref => $wordinfo) {
			if (!in_array($ref, $xml_words)) {
				$remove_words[] = $ref;
			}
		}

		if ($remove_words) {
			$lang_ids = array2sql($lang_ids);

			// Do it in chunks of 20 so SQL doenst get insanely long
			$remove_words = array_chunk($remove_words, 20);

			foreach ($remove_words as $chunk) {
				$db->query("
					DELETE FROM template_words
					WHERE wordref IN " . array2sql($chunk) . "
					AND cust = 0
					AND language IN $lang_ids
				");
			}
		}
	}

	/************
	* Delete any words that do not exist inside template_words_cat_link, clearly ignoring any custom ones
	************/

	$db->query("
		SELECT wordref FROM template_words_cat_link
	");

	unset($words);
	while ($result = $db->row_array()) {
		$words[] = $result['wordref'];
	}

	$db->query("
		DELETE FROM template_words
		WHERE wordref NOT IN " . array2sql($words) . "
			AND cust = 0
	");


	################### HELP ###################

	// get all the changed user_help_entries
	$db->query("
		SELECT * FROM user_help_entries
		WHERE language = " . intval($id) . "
		AND changed = 1
	");

	while ($result = $db->row_array()) {
		$help_entry_current[$result['helpname']] = $result['helpname'];
	}

	// delete all the user_help_entries
	$db->query("
		DELETE FROM user_help_entries
		WHERE language = " . intval($id) . "
	");

	if (is_array($xml['help']['category'])) {
		foreach ($xml['help']['category'] AS $category) {

			/***********************
			Replace the category
			***********************/

			$db->query("
				REPLACE INTO user_help_cats_entries SET
					catname = '" . $db->escape($category['name']) . "',
					entry = '" . $db->escape($category['category_entry']['value']) . "',
					language = " . intval($id) . "
			");

			/***********************
			Deal with the entries
			***********************/

			// loop on entries
			foreach ($category['entry'] AS $entry) {

				// replace into user_help_entries
				$db->query("
					INSERT INTO user_help_entries SET
						language = " . intval($id) . ",
						title = '" . $db->escape($entry['title']['value']) . "',
						helpentry = '" . $db->escape(ifr($help_entry_current[$entry['name']], $entry['helpentry']['value'])) . "',
						helpname = '" . $db->escape($entry['name']) . "',
						backup = '" . $db->escape($entry['helpentry']['value']) . "'
				");

			}
		}
	}

	return $id;
}

/*
	if $full is 1 complete removal. if no, then we don't update users for example, because the language will be replaced.
*/
function delete_language($id, $full=1) {

	global $db, $cache2;

	if ($id == 1) {
		trigger_error('Can not delete English Language');
	}

	$db->query("
		DELETE FROM languages WHERE id = " . intval($id) . "
	");

	$db->query("
		DELETE FROM template_words WHERE language = " . intval($id) . "
	");

	$db->query("
		DELETE FROM user_help_entries WHERE language = " . intval($id) . "
	");

	$db->query("
		DELETE FROM user_help_cats_entries WHERE language = " . intval($id) . "
	");

	/**************
	* Need to Update the language in case we deleted the default language
	**************/

	if (!$db->query_amatch('languages', 'base = 1')) {

		$db->query("
			UPDATE languages SET
				base = 1
			WHERE id = 1
		");
	}

	/**************
	* Full delete of language
	**************/

	if ($full) {

		$db->query("
			UPDATE user SET
				language = " . $cache2->getDefaultLanguageID() . "
			WHERE language = " . intval($id) . "
		");

		$db->query("
			UPDATE ticket SET
				language = " . $cache2->getDefaultLanguageID() . "
			WHERE language = " . intval($id) . "
		");
	}
}