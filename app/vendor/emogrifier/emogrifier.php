<?php
/**
 * Source: http://www.pelagodesign.com/sidecar/emogrifier/
 * Modified:
 * - CSS blocks are not extracted from HTML. You are expected to pass in CSS explicitly
 * - Removed support for nth-child
 * - No cleaning of @import type rules
 * - Removed cache
 * - libxml_use_internal_errors(true)
 * - always assume utf8
 * - use of preg_replace_callback instead of /e
 */

class Emogrifier {

    // for calculating nth-of-type and nth-child selectors
    const INDEX = 0;
    const MULTIPLIER = 1;

    private $html = '';
    private $css = '';
    private $unprocessableHTMLTags = array('wbr');
    private $caches = array();

    // this attribute applies to the case where you want to preserve your original text encoding.
    // by default, emogrifier translates your text into HTML entities for two reasons:
    // 1. because of client incompatibilities, it is better practice to send out HTML entities rather than unicode over email
    // 2. it translates any illegal XML characters that DOMDocument cannot work with
    // if you would like to preserve your original encoding, set this attribute to true.
    public $preserveEncoding = false;

    public function __construct($html = '', $css = '') {
        $this->html = $html;
        $this->css  = $css;
    }

    // there are some HTML tags that DOMDocument cannot process, and will throw an error if it encounters them.
    // in particular, DOMDocument will complain if you try to use HTML5 tags in an XHTML document.
    // these functions allow you to add/remove them if necessary.
    // it only strips them from the code (does not remove actual nodes).
    public function addUnprocessableHTMLTag($tag) { $this->unprocessableHTMLTags[] = $tag; }
    public function removeUnprocessableHTMLTag($tag) {
        if (($key = array_search($tag,$this->unprocessableHTMLTags)) !== false)
            unset($this->unprocessableHTMLTags[$key]);
    }

    // applies the CSS you submit to the html you submit. places the css inline
    public function emogrify() {
        $body = $this->html;

        // remove any unprocessable HTML tags (tags that DOMDocument cannot parse; this includes wbr and many new HTML5 tags)
        if (count($this->unprocessableHTMLTags)) {
            $unprocessableHTMLTags = implode('|',$this->unprocessableHTMLTags);
            $body = preg_replace("/<\/?($unprocessableHTMLTags)[^>]*>/i",'',$body);
        }

        $encoding = "UTF-8";
		libxml_use_internal_errors(true);
        $xmldoc = new DOMDocument;
        $xmldoc->encoding = $encoding;
        $xmldoc->strictErrorChecking = false;
        $xmldoc->formatOutput = true;
        $xmldoc->loadHTML('<?xml version="1.0" encoding="UTF-8" ?>'.$body);
        $xmldoc->normalizeDocument();

        $xpath = new DOMXPath($xmldoc);

        // before be begin processing the CSS file, parse the document and normalize all existing CSS attributes (changes 'DISPLAY: none' to 'display: none');
        // we wouldn't have to do this if DOMXPath supported XPath 2.0.
        // also store a reference of nodes with existing inline styles so we don't overwrite them
        $vistedNodes = $vistedNodeRef = array();
        $nodes = @$xpath->query('//*[@style]');
        foreach ($nodes as $node) {
  			$normalizedOrigStyle = preg_replace_callback('/[A-z\-]+(?=\:)/S', function($match) {
				return strtolower($match[0]);
			}, $node->getAttribute('style'));

            // in order to not overwrite existing style attributes in the HTML, we have to save the original HTML styles
            $nodeKey = md5($node->getNodePath());
            if (!isset($vistedNodeRef[$nodeKey])) {
                $vistedNodeRef[$nodeKey] = $this->cssStyleDefinitionToArray($normalizedOrigStyle);
                $vistedNodes[$nodeKey]   = $node;
            }

            $node->setAttribute('style', $normalizedOrigStyle);
        }

        $css = $this->css;

		// process the CSS file for selectors and definitions
		preg_match_all('/(^|[^{}])\s*([^{]+){([^}]*)}/mis', $css, $matches, PREG_SET_ORDER);

		$all_selectors = array();
		foreach ($matches as $key => $selectorString) {
			// if there is a blank definition, skip
			if (!strlen(trim($selectorString[3]))) continue;

			// else split by commas and duplicate attributes so we can sort by selector precedence
			$selectors = explode(',',$selectorString[2]);
			foreach ($selectors as $selector) {

				// don't process pseudo-elements and behavioral (dynamic) pseudo-classes; ONLY allow structural pseudo-classes
				if (strpos($selector, ':') !== false && !preg_match('/:\S+\-(child|type)\(/i', $selector)) continue;

				$all_selectors[] = array('selector' => trim($selector),
										 'attributes' => trim($selectorString[3]),
										 'line' => $key, // keep track of where it appears in the file, since order is important
				);
			}
		}

		// now sort the selectors by precedence
		usort($all_selectors, array($this,'sortBySelectorPrecedence'));

		$css_info = $all_selectors;

        foreach ($css_info as $value) {

            // query the body for the xpath selector
            $nodes = $xpath->query($this->translateCSStoXpath(trim($value['selector'])));

            foreach($nodes as $node) {
                // if it has a style attribute, get it, process it, and append (overwrite) new stuff
                if ($node->hasAttribute('style')) {
                    // break it up into an associative array
                    $oldStyleArr = $this->cssStyleDefinitionToArray($node->getAttribute('style'));
                    $newStyleArr = $this->cssStyleDefinitionToArray($value['attributes']);

                    // new styles overwrite the old styles (not technically accurate, but close enough)
                    $combinedArr = array_merge($oldStyleArr,$newStyleArr);
                    $style = '';
                    foreach ($combinedArr as $k => $v) $style .= (strtolower($k) . ':' . $v . ';');
                } else {
                    // otherwise create a new style
                    $style = trim($value['attributes']);
                }
                $node->setAttribute('style', $style);
            }
        }

        // now iterate through the nodes that contained inline styles in the original HTML
        foreach ($vistedNodeRef as $nodeKey => $origStyleArr) {
            $node = $vistedNodes[$nodeKey];
            $currStyleArr = $this->cssStyleDefinitionToArray($node->getAttribute('style'));

            $combinedArr = array_merge($currStyleArr, $origStyleArr);
            $style = '';
            foreach ($combinedArr as $k => $v) $style .= (strtolower($k) . ':' . $v . ';');

            $node->setAttribute('style', $style);
        }

		$html = $xmldoc->saveHTML();
		$html = str_replace('<?xml version="1.0" encoding="UTF-8" ?>', '', $html);

        return $html;
    }

    private function sortBySelectorPrecedence($a, $b) {
        $precedenceA = $this->getCSSSelectorPrecedence($a['selector']);
        $precedenceB = $this->getCSSSelectorPrecedence($b['selector']);

        // we want these sorted ascendingly so selectors with lesser precedence get processed first and
        // selectors with greater precedence get sorted last
        return ($precedenceA == $precedenceB) ? ($a['line'] < $b['line'] ? -1 : 1) : ($precedenceA < $precedenceB ? -1 : 1);
    }

    private function getCSSSelectorPrecedence($selector) {
        $selectorkey = md5($selector);
        if (!isset($this->caches[0][$selectorkey])) {
            $precedence = 0;
            $value = 100;
            $search = array('\#','\.',''); // ids: worth 100, classes: worth 10, elements: worth 1

            foreach ($search as $s) {
                if (trim($selector == '')) break;
                $num = 0;
                $selector = preg_replace('/'.$s.'\w+/','',$selector,-1,$num);
                $precedence += ($value * $num);
                $value /= 10;
            }
            $this->caches[0][$selectorkey] = $precedence;
        }

        return $this->caches[0][$selectorkey];
    }

    // right now we support all CSS 1 selectors and most CSS2/3 selectors.
    // http://plasmasturm.org/log/444/
    private function translateCSStoXpath($css_selector) {

        $css_selector = trim($css_selector);
        $xpathkey = md5($css_selector);
        if (!isset($this->caches[1][$xpathkey])) {
            // returns an Xpath selector
            $search = array(
                               '/\s+>\s+/', // Matches any element that is a child of parent.
                               '/\s+\+\s+/', // Matches any element that is an adjacent sibling.
                               '/\s+/', // Matches any element that is a descendant of an parent element element.
                               '/([^\/]+):first-child/i', // first-child pseudo-selector
                               '/([^\/]+):last-child/i', // last-child pseudo-selector
                               '/(\w)\[(\w+)\]/', // Matches element with attribute
                               '/(\w)\[(\w+)\=[\'"]?(\w+)[\'"]?\]/', // Matches element with EXACT attribute
            );
            $replace = array(
                               '/',
                               '/following-sibling::*[1]/self::',
                               '//',
                               '*[1]/self::\\1',
                               '*[last()]/self::\\1',
                               '\\1[@\\2]',
                               '\\1[@\\2="\\3"]',
            );

            $css_selector = preg_replace($search, $replace, $css_selector);

			// Matches id attributes
			$css_selector = preg_replace_callback('/(\w+)?\#([\w\-]+)/', function($match) {
				return (strlen($match[1]) ? $match[1] : '*') . '[@id="' . $match[2] . '"]';
			}, $css_selector);

			// Matches class attributes
			$css_selector = preg_replace_callback('/(\w+|[\*\]])?((\.[\w\-]+)+)/', function($match) {
				return (strlen($match[1]) ? $match[1] : '*')
					. '[contains(concat(" ",@class," "),concat(" ","'
					. implode('"," "))][contains(concat(" ",@class," "),concat(" ","', explode('.', substr($match[2], 1)))
					. '"," "))]';
			}, $css_selector);

			$css_selector = '//'.$css_selector;

            $this->caches[0][$xpathkey] = $css_selector;
        }
        return $this->caches[0][$xpathkey];
    }

    private function cssStyleDefinitionToArray($style) {
        $definitions = explode(';',$style);
        $retArr = array();
        foreach ($definitions as $def) {
            if (empty($def) || strpos($def, ':') === false) continue;
            list($key,$value) = explode(':',$def,2);
            if (empty($key) || strlen(trim($value)) === 0) continue;
            $retArr[trim($key)] = trim($value);
        }
        return $retArr;
    }
}