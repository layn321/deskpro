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
 * Orb
 *
 * @package Orb
 * @category Input
 */

namespace Orb\Input\Cleaner\CleanerPlugin;

use Orb\Input\Cleaner\Cleaner;
use Orb\Util\Strings;

/**
 * Uses HTMLPurifier to clean HTML input
 */
class HtmlPurifier implements CleanerPlugin
{
	public function getCleanerId()
	{
		return 'html_purifier';
	}

	public function getCleanerTypes()
	{
		return array(
			'html',
			'simple_html',
			'html_core',
			'html_email',
			'html_email_basicclean',
			'html_email_preclean',
			'html_email_postclean',
			'html_fix',
		);
	}

	public function cleanValue($value, $type, array $options, Cleaner $cleaner)
	{
		$value = $cleaner->getCleaner('basic')->cleanValue($value, 'string', array(), $cleaner);

		if ($type == 'html_email_postclean') {
			$value = Strings::postDomDocument($value);
			return $value;
		}

		if (!$value || strpos($value, '<') === false) {
			return $value;
		}

		#------------------------------
		# Basic email clean
		#------------------------------

		if ($type == 'html_email_preclean') {

			// This bit normalises the HTML document. Some clients quote an original
			// HTML email message, but add their own HTML document as well. So you end up
			// with two <html>..</html> documents in one message. This screws up the cleaner.
			// This just moves the tags around so the body wraps the entire document

			$value = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $value);
			$value = preg_replace('#<html[^>]*>#i', '<html>', $value);
			if (substr_count($value, '<html>') > 1) {
				$value = str_replace('<html>', '', $value);
				$value = str_ireplace('</html>', '', $value);
				$value = preg_replace('#<body[^>]*>#i', '', $value);
				$value = str_ireplace('</body>', '', $value);
			}

			$value = preg_replace('#<(head|body|style|script)[^>]*/>#i', '', $value);
			$value = preg_replace('#<(head|body|style|script)[^>]*>\s*</\\1>#i', '', $value);

			$value = preg_replace('#<!DOCTYPE.*?>#is', '', $value);
			if (strpos($value, '<html') !== false) {
				$value = preg_replace('#<html[^>]*>#i', '', $value);
				$value = "<html>$value";
			} else {
				$value = "<html>$value</html>";
			}

			// Set a HTML 4.01 transitional doctype
			$value = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">' . "\n" . $value;

			$m = null;
			if (preg_match('#<head[^>]*>(.*?)</head>#is', $value, $m)) {
				$value = str_replace($m[0], '', $value);
				$value = str_replace('<html>', '<html>' . $m[0], $value);
			} else {
				$value = str_replace('<html>', '<html><head></head>', $value);
			}

			if (strpos($value, '<body') !== false) {
				$value = preg_replace('#<body[^>]*>#i', '', $value);
				$value = str_replace('</body>', '', $value);
			}
			$value = str_replace('</head>', '</head><body>', $value);
			$value = str_replace('</html>', '</body></html>', $value);

			// The message has been converted to UTF8 by now, and is HTML,
			// so discard the meta content-type tag that can interfere with
			// DOMDocument (esp on windows)
			$value = preg_replace('#<meta[^>]*Content-Type[^>]*>#i', '', $value);

			// Remove <a name="_MailEndCompose">. HTMLPurifier will clean up the </a> automatically
			$value = str_replace('<a name="_MailEndCompose">', '', $value);

			$value = str_replace(array('<o:p>', '</o:p>'), array('', ''), $value);
			$value = Strings::extractBodyTag($value);
			$value = Strings::decodeWhitespaceHtmlEntities($value);

			// Recreate a full, basic document
			// Just the body will used by the time we insert the message into the db,
			// but we need a full document like this so that DOMDocument "cleans" bad HTML properly.
			// E.g., a malformed meta tag could result in a whole paragraph erroneously being moved
			// under a <head> tag if we dont explicitly put them all under body
			$value = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">' . "\n<html><head></head><body>" . $value . '</body></html>';

			// Replace Wingdings characters with UTF-8 characters
			$map = array(
				'J' => ':)',
				'L' => ':(',
				'K' => ':|',
				'ß' => '<-',
				'ç' => '<=',
				'ó' => '<=>',
				'è' => '=>',
				'à' => '->',
			);

			$m = null;
			if (preg_match_all('#<span\s*style=(?:\'|")[^"\'>]+font-family\s*:\s*Wingdings[^"\'>]+(?:\'|")>([^<>]+)</span>#', $value, $m, \PREG_SET_ORDER)) {
				foreach ($m as $match) {
					$replace = $match[1];
					foreach ($map as $f => $r) {
						$replace = str_replace($f, $r, $replace);
					}
					$value = str_replace($match[0], $replace, $value);
				}
			}

			// Email do a bunch of processing with DOMDocument which messes with HTML Entities
			// There are bugs with different versions of libxml where entites are not properly
			// decoded, or the DOMDocument->substituteEntities not being honoured etc.
			// Easiest solution is to hack around entiites altogether so DOMDocument doesnt mess them up
			$value = Strings::preDomDocument($value);

			return $value;
		}

		if ($type == 'html_email_basicclean') {
			return $value;
		}

		#------------------------------
		# HTML Purifier cleaners
		#------------------------------

		require_once DP_ROOT.'/vendor/htmlpurifier/HTMLPurifier.standalone.php';

		$purifier = new \HTMLPurifier();
		$config = $this->getConfigForType($type);

		if ($type == 'html_email') {
			// Cut to the body, also cuts out multiple xml decls
			// Even if the client didnt send it, DOMDocument from cutter etc wraps body wanyway
			$value = Strings::extractBodyTag($value);
		}

		$value = $purifier->purify($value, $config);
		$value = preg_replace('#class="([a-zA-Z0-9]*)MsoNormal([a-zA-Z0-9]*)"#iu', 'class="$1MsoNormal$2" style="margin:0;"', $value);
		$value = preg_replace_callback('#<[^>]*>#u', function ($m) {
			return preg_replace('#(.*?)style="(.*?)"(.*?)style="(.*?)"#u', '$1style="$2;$3"$4', $m[0]);
		}, $value);

		if ($type == 'html_email') {
			$value = $this->cleanValue($value, 'html_email_basicclean', $options, $cleaner);
			$value = Strings::decodeWhitespaceHtmlEntities($value);
			$value = Strings::trimHtmlAdvanced($value);
		}

		return $value;
	}

	/**
	 * @param string $type
	 * @return \HTMLPurifier_Config
	 */
	public function getConfigForType($type)
	{
		$config = \HTMLPurifier_Config::createDefault();
		$config->set('Cache.DefinitionImpl', null);
		$config->set('Core.Encoding', 'UTF-8');

		switch ($type) {
			case 'html':
				$config->set('HTML.Allowed', "
					*[style|title|class|id],
					a[rel|rev|name|href|target|title|class]
					strong,b,em,i,strike,u,
					p[align],ol[type|compact],ul,li,br,img[src|width|height|alt|title],
					sub,sup,blockquote,
					table[border|cellspacing|cellpadding|width|align|summary],
					tr,tbody,thead,tfoot,
					td[colspan|rowspan|width|height|align|valign|scope]
					th[colspan|rowspan|width|height|align|valign|scope],
					caption,div, span, code, pre,address, h1, h2, h3, h4, h5, h6, hr[size|noshade],
					font[face|size|color],dd,dl,dt,cite,abbr,acronym,del[cite],ins[cite],
					col[align|span|valign|width],colgroup[align|span|valign|width],
					dfn,kbd,
					q[cite],small,
					tt,var,big
				");
				$config->set('HTML.Doctype', 'HTML 4.01 Transitional');
				$config->set('HTML.TidyLevel', 'medium');
				$config->set('AutoFormat.RemoveEmpty', false);
				$config->set('Attr.EnableID', true);
				$config->set('Attr.IDPrefix', 'dp-user-');
				$config->set('Attr.AllowedFrameTargets', array('_blank'));
				break;

			case 'html_core':
				$config->set('HTML.Allowed', '*[style],em,i,strong,b,u,strike,a[href],img[src|class|title|alt],ul,li,dd,dt,dl,ol,table,thead,tbody,tfoot,tr,td,th,pre,div[align|class],p[align|class],blockquote,span[class],font[color|face|size],br,hr');
				$config->set('AutoFormat.AutoParagraph', true);
				$config->set('AutoFormat.Linkify', true);
				$config->set('AutoFormat.RemoveSpansWithoutAttributes', true);
				$config->set('AutoFormat.RemoveEmpty', true);
				break;

			case 'simple_html':
				$config->set('HTML.Allowed', 'em,i,strong,b,u,strike,a[href],img[src],ul,li,dd,dt,dl,ol,p[align],span,br');
				$config->set('AutoFormat.AutoParagraph', true);
				$config->set('AutoFormat.Linkify', true);
				$config->set('URI.DisableExternalResources', true);
				$config->set('AutoFormat.RemoveEmpty', true);
				$config->set('AutoFormat.RemoveSpansWithoutAttributes', true);
				$config->set('AutoFormat.RemoveEmpty', true);
				break;

			case 'html_email':
				$config->set('HTML.AllowedElements', 'em,strong,a,ul,li,dd,dt,dl,ol,p,span,br,hr,table,thead,tbody,tfoot,tr,td,th,pre,code,div,blockquote,sup,sub,font,u,i,b');
				$config->set('HTML.AllowedAttributes', 'a.href,*.style,*.class,font.color,font.face,font.size');
				$config->set('Attr.AllowedClasses', 'MsoNormal');
				$config->set('URI.DisableExternalResources', true);
				$config->set('AutoFormat.RemoveEmpty', false);
				$config->set('CSS.AllowedProperties', array('font', 'font-weight', 'font-style', 'font-size', 'color', 'background-color', 'background'));
				$config->set('HTML.Doctype', 'XHTML 1.0 Transitional');
				$config->set('HTML.TidyLevel', 'medium');
				break;

			case 'html_fix':
				$config->set('HTML.Doctype', 'XHTML 1.0 Transitional');
				$config->set('HTML.TidyLevel', 'none');
				break;
		}

		return $config;
	}
}
