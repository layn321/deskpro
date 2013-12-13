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

namespace Orb\Html;

use DOMDocument, DOMNode, DOMText, DOMDocumentType, DOMElement;
use Orb\Util\Strings;

/**
 * Class Html2Text.
 *
 * Converts HTML documents into plaintext.
 *
 * Based on html2text by Jeven Wright: https://code.google.com/p/iaml/source/browse/trunk/org.openiaml.model.runtime/src/include/html2text/html2text.php
 *
 * @package Orb\Html
 */
class Html2Text
{
	/**
	 * @param string $html
	 * @return string
	 */
	public static function convertHtml($html)
	{
		$h2t = new Html2Text();
		return $h2t->convert($html);
	}


	/**
	 * Convert an HTML string into plaintext
	 *
	 * @param string $html
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function convert($html)
	{
		$html = Strings::standardEol($html);

		// nbsp's
		$html = str_replace('&nbsp;', ' ', $html);
		$html = preg_replace('#\x{00a0}#u', ' ', $html);

		$html = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $html;

		$doc = new DOMDocument('1.0', 'UTF-8');
        if (!@$doc->loadHTML($html)) {
			throw new \InvalidArgumentException("Error loading HTML into DOMDocument");
		}

		$txt = $this->convertNode($doc);
		$txt = Strings::trimLines($txt);
		$txt = trim($txt);

		return $txt;
	}


	/**
	 * Convert a DOMNode/DOMDocument into plaintext
	 *
	 * @param DOMNode $node
	 * @return string
	 */
	public function convertNode(DOMNode $node, $_depth = 0)
	{
		if ($node instanceof DOMText) {
			return trim($node->wholeText);
		}
		if ($node instanceof DOMDocumentType) {
			return '';
		}

		$nextName = $this->getNextChildName($node);
        //$prevName = $this->getPrevChildName($node);

        $name = strtolower($node->nodeName);

        $output = '';
        switch ($name) {
			case 'hr':
				return "<DP_BR>------<DP_BR>";

			case 'style':
			case 'head':
			case 'title':
			case 'meta':
			case 'script':
			case 'object':
				return '';

			case 'h1':
			case 'h2':
			case 'h3':
			case 'h4':
			case 'h5':
			case 'h6':
				$output = '<DP_BR>';
				break;

			case 'td':
				$output = '<DP_SP>';
				break;

			case 'p':
			case 'div':
			case 'tr':
			case 'thead':
			case 'tbody':
			case 'tfoot':
				$output = '<DP_BR>';
				break;
        }

		if (!empty($node->childNodes)) {
			$len = $node->childNodes->length;
			for ($i = 0; $i < $len; $i++) {
				$n = $node->childNodes->item($i);
				if ($n) {
					$text = $this->convertNode($n, $_depth+1);
					$output .= $text;
				}
			}
		}

		// end whitespace
		switch ($name) {
			case 'style':
			case 'head':
			case 'title':
			case 'meta':
			case 'script':
				// ignore these tags
				return '';

			case 'h1':
			case 'h2':
			case 'h3':
			case 'h4':
			case 'h5':
			case 'h6':
				$output .= '<DP_BR>';
				break;

			case 'td':
				$output .= '<DP_SP>';
				break;

			case 'p':
			case 'br':
				if ($nextName != "div") {
					$output .= '<DP_BR>';
				}
				break;

			case 'div':
				// add one line only if the next child isn't a div
				if ($nextName != "div" && $nextName != null) {
					$output .= '<DP_BR>';
				}
				break;

			case 'a':
				if (!trim(str_replace('<DP_BR>', '', $output))) {
					$output = '';
				} else {
					$href = $node->getAttribute("href");
					if ($href == null) {
						// it doesn't link anywhere
						if ($node->getAttribute("name") != null) {
							$output = "[$output]";
						}
					} else {
						if ($href == $output) {
							// link to the same address: just use link
						} else {
							// replace it
							$output = "[$output]($href)";
						}
					}

					// does the next node require additional whitespace?
					switch ($nextName) {
						case "h1": case "h2": case "h3": case "h4": case "h5": case "h6":
							$output .= '<DP_BR>';
							break;
					}
				}
				break;
		}

		$output = Strings::trimLines($output);
		$output = str_replace("\n", ' ', $output);
		$output = preg_replace('#[ ]+#', ' ', $output); // multiple spaces to single
		if ($_depth == 0) {
			$output = str_replace('<DP_BR>', "\n", $output);
			$output = str_replace('<DP_SP>', " ", $output);
		}

		$output = trim($output);
		return $output;
	}


	/**
	 * @param DOMNode $node
	 * @return null|string
	 */
	protected function getNextChildName(DOMNode $node)
	{
		// get the next child
		$nextNode = $node->nextSibling;
		while ($nextNode != null) {
			if ($nextNode instanceof DOMElement) {
				break;
			}
			$nextNode = $nextNode->nextSibling;
		}
		$nextName = null;
		if ($nextNode instanceof DOMElement && $nextNode != null) {
			$nextName = strtolower($nextNode->nodeName);
		}

		return $nextName;
	}


	/**
	 * @param DOMNode $node
	 * @return null|string
	 */
	protected function getPrevChildName(DOMNode $node)
	{
		$nextNode = $node->previousSibling;
		while ($nextNode != null) {
			if ($nextNode instanceof DOMElement) {
					break;
			}
			$nextNode = $nextNode->previousSibling;
		}
		$nextName = null;
		if ($nextNode instanceof DOMElement && $nextNode != null) {
			$nextName = strtolower($nextNode->nodeName);
		}

		return $nextName;
	}
}