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
 * @subpackage Data
 */

namespace Orb\Data;

class ContentTypes
{
	protected static $ext_to_contenttype = array(
		'acx'      => 'application/internet-property-stream',
		'ai'       => 'application/postscript',
		'aif'      => 'audio/x-aiff',
		'aifc'     => 'audio/x-aiff',
		'aiff'     => 'audio/x-aiff',
		'asf'      => 'video/x-ms-asf',
		'asr'      => 'video/x-ms-asf',
		'asx'      => 'video/x-ms-asf',
		'au'       => 'audio/basic',
		'avi'      => 'video/x-msvideo',
		'axs'      => 'application/olescript',
		'bas'      => 'text/plain',
		'bcpio'    => 'application/x-bcpio',
		'bin'      => 'application/octet-stream',
		'bmp'      => 'image/bmp',
		'c'        => 'text/plain',
		'cat'      => 'application/vnd.ms-pkiseccat',
		'cdf'      => 'application/x-cdf',
		'cer'      => 'application/x-x509-ca-cert',
		'class'    => 'application/octet-stream',
		'clp'      => 'application/x-msclip',
		'cmx'      => 'image/x-cmx',
		'cod'      => 'image/cis-cod',
		'cpio'     => 'application/x-cpio',
		'crd'      => 'application/x-mscardfile',
		'crl'      => 'application/pkix-crl',
		'crt'      => 'application/x-x509-ca-cert',
		'csh'      => 'application/x-csh',
		'css'      => 'text/css',
		'csv'      => 'text/csv',
		'dcr'      => 'application/x-director',
		'der'      => 'application/x-x509-ca-cert',
		'dir'      => 'application/x-director',
		'dll'      => 'application/x-msdownload',
		'dms'      => 'application/octet-stream',
		'doc'      => 'application/msword',
		'docx'     => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'dot'      => 'application/msword',
		'dotx'     => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
		'dvi'      => 'application/x-dvi',
		'dxr'      => 'application/x-director',
		'eps'      => 'application/postscript',
		'etx'      => 'text/x-setext',
		'evy'      => 'application/envoy',
		'exe'      => 'application/octet-stream',
		'fif'      => 'application/fractals',
		'flr'      => 'x-world/x-vrml',
		'gif'      => 'image/gif',
		'gtar'     => 'application/x-gtar',
		'gz'       => 'application/x-gzip',
		'h'        => 'text/plain',
		'hdf'      => 'application/x-hdf',
		'hlp'      => 'application/winhlp',
		'hqx'      => 'application/mac-binhex40',
		'hta'      => 'application/hta',
		'htc'      => 'text/x-component',
		'htm'      => 'text/html',
		'html'     => 'text/html',
		'htt'      => 'text/webviewhtml',
		'ico'      => 'image/x-icon',
		'ief'      => 'image/ief',
		'iii'      => 'application/x-iphone',
		'ins'      => 'application/x-internet-signup',
		'isp'      => 'application/x-internet-signup',
		'jfif'     => 'image/pipeg',
		'jpe'      => 'image/jpeg',
		'jpeg'     => 'image/jpeg',
		'jpg'      => 'image/jpeg',
		'js'       => 'application/x-javascript',
		'latex'    => 'application/x-latex',
		'lha'      => 'application/octet-stream',
		'lsf'      => 'video/x-la-asf',
		'lsx'      => 'video/x-la-asf',
		'lzh'      => 'application/octet-stream',
		'm13'      => 'application/x-msmediaview',
		'm14'      => 'application/x-msmediaview',
		'm3u'      => 'audio/x-mpegurl',
		'man'      => 'application/x-troff-man',
		'markdown' => 'text/x-markdown',
		'md'       => 'text/x-markdown',
		'mdb'      => 'application/x-msaccess',
		'me'       => 'application/x-troff-me',
		'mht'      => 'message/rfc822',
		'mhtml'    => 'message/rfc822',
		'mid'      => 'audio/mid',
		'mny'      => 'application/x-msmoney',
		'mov'      => 'video/quicktime',
		'movie'    => 'video/x-sgi-movie',
		'mp2'      => 'video/mpeg',
		'mp3'      => 'audio/mpeg',
		'mpa'      => 'video/mpeg',
		'mpe'      => 'video/mpeg',
		'mpeg'     => 'video/mpeg',
		'mpg'      => 'video/mpeg',
		'mpp'      => 'application/vnd.ms-project',
		'mpv2'     => 'video/mpeg',
		'ms'       => 'application/x-troff-ms',
		'mvb'      => 'application/x-msmediaview',
		'nws'      => 'message/rfc822',
		'oda'      => 'application/oda',
		'p10'      => 'application/pkcs10',
		'p12'      => 'application/x-pkcs12',
		'p7b'      => 'application/x-pkcs7-certificates',
		'p7c'      => 'application/x-pkcs7-mime',
		'p7m'      => 'application/x-pkcs7-mime',
		'p7r'      => 'application/x-pkcs7-certreqresp',
		'p7s'      => 'application/x-pkcs7-signature',
		'pbm'      => 'image/x-portable-bitmap',
		'pdf'      => 'application/pdf',
		'pfx'      => 'application/x-pkcs12',
		'pgm'      => 'image/x-portable-graymap',
		'pko'      => 'application/ynd.ms-pkipko',
		'pma'      => 'application/x-perfmon',
		'pmc'      => 'application/x-perfmon',
		'pml'      => 'application/x-perfmon',
		'pmr'      => 'application/x-perfmon',
		'pmw'      => 'application/x-perfmon',
		'png'      => 'image/png',
		'pnm'      => 'image/x-portable-anymap',
		'pot'      => 'application/vnd.ms-powerpoint',
		'potx'     => 'application/vnd.openxmlformats-officedocument.presentationml.template',
		'ppm'      => 'image/x-portable-pixmap',
		'pps'      => 'application/vnd.ms-powerpoint',
		'ppsx'     => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
		'ppt'      => 'application/vnd.ms-powerpoint',
		'pptx'     => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'prf'      => 'application/pics-rules',
		'ps'       => 'application/postscript',
		'pub'      => 'application/x-mspublisher',
		'qt'       => 'video/quicktime',
		'ra'       => 'audio/x-pn-realaudio',
		'ram'      => 'audio/x-pn-realaudio',
		'ras'      => 'image/x-cmu-raster',
		'rgb'      => 'image/x-rgb',
		'rmi'      => 'audio/mid',
		'roff'     => 'application/x-troff',
		'rtf'      => 'application/rtf',
		'rtx'      => 'text/richtext',
		'scd'      => 'application/x-msschedule',
		'sct'      => 'text/scriptlet',
		'setpay'   => 'application/set-payment-initiation',
		'setreg'   => 'application/set-registration-initiation',
		'sh'       => 'application/x-sh',
		'shar'     => 'application/x-shar',
		'sit'      => 'application/x-stuffit',
		'sldx'     => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
		'snd'      => 'audio/basic',
		'spc'      => 'application/x-pkcs7-certificates',
		'spl'      => 'application/futuresplash',
		'src'      => 'application/x-wais-source',
		'sst'      => 'application/vnd.ms-pkicertstore',
		'stl'      => 'application/vnd.ms-pkistl',
		'stm'      => 'text/html',
		'sv4cpio'  => 'application/x-sv4cpio',
		'sv4crc'   => 'application/x-sv4crc',
		'svg'      => 'image/svg+xml',
		't'        => 'application/x-troff',
		'tar'      => 'application/x-tar',
		'tcl'      => 'application/x-tcl',
		'tex'      => 'application/x-tex',
		'texi'     => 'application/x-texinfo',
		'texinfo'  => 'application/x-texinfo',
		'tgz'      => 'application/x-compressed',
		'tif'      => 'image/tiff',
		'tiff'     => 'image/tiff',
		'tr'       => 'application/x-troff',
		'trm'      => 'application/x-msterminal',
		'tsv'      => 'text/tab-separated-values',
		'txt'      => 'text/plain',
		'uls'      => 'text/iuls',
		'ustar'    => 'application/x-ustar',
		'vcf'      => 'text/x-vcard',
		'vrml'     => 'x-world/x-vrml',
		'wav'      => 'audio/x-wav',
		'wcm'      => 'application/vnd.ms-works',
		'wdb'      => 'application/vnd.ms-works',
		'wks'      => 'application/vnd.ms-works',
		'wmf'      => 'application/x-msmetafile',
		'wps'      => 'application/vnd.ms-works',
		'wri'      => 'application/x-mswrite',
		'wrl'      => 'x-world/x-vrml',
		'wrz'      => 'x-world/x-vrml',
		'xaf'      => 'x-world/x-vrml',
		'xbm'      => 'image/x-xbitmap',
		'xla'      => 'application/vnd.ms-excel',
		'xlam'     => 'application/vnd.ms-excel.addin.macroEnabled.12',
		'xlc'      => 'application/vnd.ms-excel',
		'xlm'      => 'application/vnd.ms-excel',
		'xls'      => 'application/vnd.ms-excel',
		'xlsb'     => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
		'xlsx'     => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'xlt'      => 'application/vnd.ms-excel',
		'xltx'     => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
		'xlw'      => 'application/vnd.ms-excel',
		'xof'      => 'x-world/x-vrml',
		'xpm'      => 'image/x-xpixmap',
		'xwd'      => 'image/x-xwindowdump',
		'z'        => 'application/x-compress',
		'zip'      => 'application/zip',
		'323'      => 'text/h323',
	);



	/**
	 * Get an array of file extensions.
	 *
	 * @return array
	 */
	public static function getExtensions()
	{
		return array_keys(self::$ext_to_contenttype);
	}



	/**
	 * Get an array of ext=>contenttype
	 *
	 * @return array
	 */
	public static function getContentTypeArray()
	{
		return self::$ext_to_contenttype;
	}



	/**
	 * Get the contenttype for a given extension.
	 *
	 * Returns null if no contenttype could be found.
	 *
	 * @param  string  $ext  The file extension
	 * @return string
	 */
	public static function getContentTypeFromExtension($ext)
	{
		$ext = strtolower(rtrim($ext, '.'));

		if (!isset(self::$ext_to_contenttype[$ext])) {
			return null;
		}

		return self::$ext_to_contenttype[$ext];
	}



	/**
	 * Get the contenttype for a given filename or path.
	 *
	 * Returns null if no contenttype could be found.
	 *
	 * @param  string $filename  The filename
	 * @return string
	 */
	public static function getContentTypeFromFilename($filename)
	{
		$dot_pos = strrpos($filename, '.');
		if (!$dot_pos) {
			return null;
		}

		$ext = substr($filename, $dot_pos+1);

		return self::getContentTypeFromExtension($ext);
	}



	/**
	 * Search for a suitable file extension for a given contenttype.
	 *
	 * There can be multiple extensions for a given content-type (htm or html, jpg or jpeg etc). Only the
	 * first one will be returned unless $find_all is true, in which case an array
	 * of all suitable extensions are returned.
	 *
	 * @param  string  $content_type  The content-type to look up
	 * @param  bool    $find_all      When true, an array of extensions will be returned.
	 * @return string
	 */
	public static function findExtensionForContentType($content_type, $find_all = false)
	{
		// If we only want one, we can use array_search
		if (!$find_all) {
			$ext = array_search($content_type, self::$ext_to_contenttype);
			return $ext ? $ext : null;
		}

		return self::_arraySearchAll(self::$ext_to_contenttype, $content_type, false);
	}

	protected static function _arraySearchAll($array, $search, $strict = false)
	{
		$found_keys = array();

		if ($strict) {
			foreach ($array as $k => $v) {
				if ($search === $v) {
					$found_keys[] = $k;
				}
			}
		} else {
			foreach ($array as $k => $v) {
				if ($search == $v) {
					$found_keys[] = $k;
				}
			}
		}

		return $found_keys;
	}


	/**
	 * Get an array of image types
	 *
	 * @return array
	 */
	public static function getImageContentTypes()
	{
		return array(
			'image/png',
			'image/gif',
			'image/jpg',
			'image/jpeg',
		);
	}


	/**
	 * Check to see if a content type is an image type
	 *
	 * @param $content_type
	 * @return bool
	 */
	public static function isImageContentType($content_type)
	{
		return in_array($content_type, self::getImageContentTypes());
	}


	/**
	 * @static
	 * @param $content_type
	 * @param bool $safe
	 * @return bool
	 */
	public static function isInlineContentType($content_type, $safe = true)
	{
		if (self::isImageContentType($content_type)) {
			return true;
		}

		switch ($content_type) {
			case 'text/css':
			case 'text/javascript':
			case 'text/plain':
			case 'text/x-markdown':
				return true;

			case 'text/html':
				if ($safe) {
					return false;
				}
				return true;
		}

		return false;
	}



	/**
	 * Checks a filename to see if its a file that hsould be displaeyd inline (images, mostly).
	 *
	 * @return bool
	 */
	public static function showFileInline($filename)
	{
		static $inline_ext = array('png', 'gif', 'jpeg', 'jpg', 'ico', 'txt');

		$dot_pos = strrpos($filename, '.');
		if (!$dot_pos) {
			return false;
		}

		$ext = substr($filename, $dot_pos+1);

		if (in_array($ext, $inline_ext)) {
			return true;
		}

		return false;
	}
}
