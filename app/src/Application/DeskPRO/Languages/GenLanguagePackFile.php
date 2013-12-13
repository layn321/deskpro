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

use Orb\Util\DOMDocument;

class GenLanguagePackFile
{
	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var string
	 */
	protected $locale;

	/**
	 * @var string
	 */
	protected $lang_code;

	/**
	 * @var string[]
	 */
	protected $phrases;

	public function __construct($title, $locale, $lang_code, $phrases = array())
	{
		$this->title     = $title;
		$this->locale    = $locale;
		$this->lang_code = $lang_code;
		$this->phrases   = $phrases;
	}

	/**
	 * @param string $id
	 * @param string $phrase
	 */
	public function addPhrase($id, $phrase)
	{
		$this->phrases[$id] = $phrase;
	}


	/**
	 * Add an array of phrases
	 *
	 * @param string[] $phrases
	 */
	public function addPhrases(array $phrases)
	{
		$this->phrases = array_merge($this->phrases, $phrases);
	}


	/**
	 * Get the generated document as a string
	 *
	 * @return string
	 */
	public function getXml()
	{
		$dom = $this->getDomDocument();
		return $dom->saveXML();
	}


	/**
	 * Write the generated XML document to a file
	 *
	 * @param string $path
	 * @throws \RuntimeException
	 */
	public function writeXml($path)
	{
		$xml = $this->getXml();

		if (!file_put_contents($path, $xml)) {
			throw new \RuntimeException("Failed to write XML to file");
		}
	}


	/**
	 * Get the generated DOMDocuemtn
	 *
	 * @return \DOMDocument
	 */
	public function getDomDocument()
	{
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->formatOutput = true;

		$pack = $dom->createElement('pack');
		$dom->appendChild($pack);

		$lang = $dom->createElement('language');
		$pack->appendChild($lang);

		#------------------------------
		# <language> header
		#------------------------------

		$title = $dom->createElement('title');
		$title->appendChild($dom->createTextNode($this->title));
		$lang->appendChild($title);

		$locale = $dom->createElement('locale');
		$locale->appendChild($dom->createTextNode($this->locale));
		$lang->appendChild($locale);

		$locale = $dom->createElement('lang');
		$locale->appendChild($dom->createTextNode($this->lang));
		$lang->appendChild($locale);

		#------------------------------
		# <phrases>
		#------------------------------

		$phrases = $dom->createElement('phrases');
		$pack->appendChild($phrases);

		ksort($this->phrases, \SORT_STRING);

		foreach ($this->phrases as $id => $phrase) {
			$p = $dom->createElement('phrase');
			$p->appendChild($dom->createTextNode($phrase));
			$p->setAttribute('id', $id);
			$phrases->appendChild($p);
		}

		return $dom;
	}
}