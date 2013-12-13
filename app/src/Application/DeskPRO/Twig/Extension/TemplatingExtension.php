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
 * @category Templating
 */

namespace Application\DeskPRO\Twig\Extension;

use Orb\Data\Countries;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Application\DeskPRO\App;

use Orb\Util\Util;
use Orb\Util\Strings;
use Orb\Util\Dates;

class TemplatingExtension extends \Twig_Extension
{
    protected $container;
	protected $counter_registry;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function getTemplating()
    {
        return $this->container->get('templating');
    }

    public function getFunctions()
    {
        return array(
			'constant'                         => new \Twig_Function_Method($this, 'getConstant', array()),
			'phrase'                           => new \Twig_Function_Method($this, 'getPhrase', array('is_safe' => array('html'), 'needs_context' => true)),
			'phrase_code'                      => new \Twig_Function_Method($this, 'getPhraseText', array()),
			'has_phrase'                       => new \Twig_Function_Method($this, 'hasPhrase', array('is_safe' => array('html'))),
			'phrase_object'                    => new \Twig_Function_Method($this, 'getPhraseObject'),
			'phrase_dev'                       => new \Twig_Function_Method($this, 'getPhraseDev'),
			'language_html_attr'               => new \Twig_Function_Method($this, 'getLanguageHtmlAttributes', array('is_safe' => array('html'))),
			'language_arrow'                   => new \Twig_Function_Method($this, 'getLanguageArrow', array('is_safe' => array('html'))),
			'is_rtl'                           => new \Twig_Function_Method($this, 'isRtl'),
			'url_fragment'                     => new \Twig_Function_Method($this, 'urlFragment'),
			'asset_full'                       => new \Twig_Function_Method($this, 'assetFull'),
			'asset_url'                        => new \Twig_Function_Method($this, 'assetFull'),
			'url_full'                         => new \Twig_Function_Method($this, 'urlFull'),
			'url_display'                      => new \Twig_Function_Method($this, 'urlDisplay'),
			'helpdesk_url'                     => new \Twig_Function_Method($this, 'helpdeskUrl'),
			'deskpro_debug'                    => new \Twig_Function_Method($this, 'isDebugMode'),
			'render_custom_field'              => new \Twig_Function_Method($this, 'renderCustomField', array('is_safe' => array('html'))),
			'render_custom_field_text'         => new \Twig_Function_Method($this, 'renderCustomFieldText'),
			'render_custom_field_form'         => new \Twig_Function_Method($this, 'renderCustomFieldForm', array('is_safe' => array('html'))),
			'el_uid'                           => new \Twig_Function_Method($this, 'elUid', array('is_safe' => array('html'))),
			'rand'                             => new \Twig_Function_Method($this, 'rand', array('is_safe' => array('html'))),
			'is_partial_request'               => new \Twig_Function_Method($this, 'isPartialRequest'),
			'str_repeat'                       => new \Twig_Function_Method($this, 'strRepeat'),
			'is_user_guest'                    => new \Twig_Function_Method($this, 'isUserGuest'),
			'is_user_loggedin'                 => new \Twig_Function_Method($this, 'isUserUser'),
			'is_user_agent'                    => new \Twig_Function_Method($this, 'isUserAgent'),
			'is_user_admin'                    => new \Twig_Function_Method($this, 'isUserAdmin'),
			'flash_message'                    => new \Twig_Function_Method($this, 'flashMessage'),
			'compare_type'                     => new \Twig_Function_Method($this, 'compareType'),
			'object_path'                      => new \Twig_Function_Method($this, 'getObjectPath'),
			'object_path_agent'                => new \Twig_Function_Method($this, 'getObjectPathAgent'),
			'get_type'                         => new \Twig_Function_Method($this, 'getType'),
			'debug_var'                        => new \Twig_Function_Method($this, 'debugVar'),
			'security_token'                   => new \Twig_Function_Method($this, 'securityToken'),
			'static_security_token'            => new \Twig_Function_Method($this, 'staticSecurityToken'),
			'static_security_token_secret'     => new \Twig_Function_Method($this, 'staticSecurityTokenSecret'),
			'render_usersource'                => new \Twig_Function_Method($this, 'renderUsersource', array('is_safe' => array('html'))),
			'get_data'                         => new \Twig_Function_Method($this, 'getData'),
			'dp_asset'                         => new \Twig_Function_Method($this, 'getAssetic'),
			'dp_asset_raw'                     => new \Twig_Function_Method($this, 'getAsseticRaw'),
			'dp_asset_html'                    => new \Twig_Function_Method($this, 'htmlGetAssetic', array('is_safe' => array('html'))),
			'start_counter'                    => new \Twig_Function_Method($this, 'startCounter'),
			'get_counter'                      => new \Twig_Function_Method($this, 'getCounter'),
			'inc_counter'                      => new \Twig_Function_Method($this, 'incCounter'),
			'form_token'                       => new \Twig_Function_Method($this, 'formToken', array('is_safe' => array('html'))),
			'relative_time'                    => new \Twig_Function_Method($this, 'relativeTime', array('is_safe' => array('html'))),
			'get_service_url'                  => new \Twig_Function_Method($this, 'getServiceUrl', array('is_safe' => array('html'))),
			'get_service_url_raw'              => new \Twig_Function_Method($this, 'getServiceUrlRaw', array('is_safe' => array('html'))),
			'get_instance_ability'             => new \Twig_Function_Method($this, 'getInstanceAbility'),
			'is_array'                         => new \Twig_Function_Method($this, 'isArray'),
			'gravatar_for_email'               => new \Twig_Function_Method($this, 'gravatar'),
			'time_group_phrase'                => new \Twig_Function_Method($this, 'getTimeGroupPhrase'),
			'captcha_html'                     => new \Twig_Function_Method($this, 'captchaHtml', array('is_safe' => array('html'))),
			'include_file'                     => new \Twig_Function_Method($this, 'includeFile', array('is_safe' => array('html'))),
			'include_php_file'                 => new \Twig_Function_Method($this, 'includePhpFile', array('is_safe' => array('html'))),
			'var_dump'                         => new \Twig_Function_Method($this, 'dumpVar'),
			'dp_copyright'                     => new \Twig_Function_Function('DeskPRO\\Kernel\\License::staticGetUserCopyrightHtml', array('is_safe' => array('html'))),
			'dp_widgets'                       => new \Twig_Function_Method($this, 'getWidgets', array('is_safe' => array('html'))),
			'dp_widgets_raw'                   => new \Twig_Function_Method($this, 'getWidgetsRaw'),
			'dp_widget_id'                     => new \Twig_Function_Method($this, 'getWidgetHtmlId'),
			'dp_widget_tabs_header'            => new \Twig_Function_Method($this, 'getWidgetTabsHeader', array('is_safe' => array('html'))),
			'dp_widget_tabs'                   => new \Twig_Function_Method($this, 'getWidgetTabsBody', array('is_safe' => array('html'))),
			'dp_js_sso_loader'                 => new \Twig_Function_Method($this, 'getJsSsoLoader', array('is_safe' => array('html'))),
			'dp_js_sso_share'                  => new \Twig_Function_Method($this, 'getJsSsoShare', array('is_safe' => array('html'))),
			'base_template_name'               => new \Twig_Function_Method($this, 'getBaseTemplateName', array('is_safe' => array('html'))),
			'array_attr'                       => new \Twig_Function_Method($this, 'getArrayAttribute'),
			'min'                              => new \Twig_Function_Method($this, 'min'),
			'max'                              => new \Twig_Function_Method($this, 'max'),
			'match'                            => new \Twig_Function_Method($this, 'match'),
			'set_tplvar'                       => new \Twig_Function_Method($this, 'set_tplvar', array('is_safe' => array('html'), 'needs_context' => true)),
			'tpl_source'                       => new \Twig_Function_Method($this, 'getTplSourceTemplate', array('is_safe' => array('html'))),

			// override so we can suppress errors where templates are out of date
			'url'  => new \Twig_Function_Method($this, 'getUrl'),
            'path' => new \Twig_Function_Method($this, 'getPath'),
        );
    }

	public function getFilters()
    {
        return array(
			'safe_link_urls'         => new \Twig_Filter_Method($this, 'safeLinkUrls', array('is_safe' => array('html'))),
			'safe_link_urls_html'    => new \Twig_Filter_Method($this, 'safeLinkUrlsHtml', array('is_safe' => array('html'))),
			'link_agent_short_code_html'  => new \Twig_Filter_Method($this, 'linkAgentShortCodeHtml', array('is_safe' => array('html'))),
			'raw_url_encode'         => new \Twig_Filter_Method($this, 'rawUrlEncode', array('is_safe' => array('html'))),
			'repeat'                 => new \Twig_Filter_Method($this, 'strRepeat'),
			'trim'                   => new \Twig_Filter_Method($this, 'strTrim'),
			'encode_number'          => new \Twig_Filter_Method($this, 'encNum', array('is_safe' => array('html'))),
			'decode_number'          => new \Twig_Filter_Method($this, 'decNum', array('is_safe' => array('html'))),
			'md5_hash'               => new \Twig_Filter_Method($this, 'getMd5', array('is_safe' => array('html'))),
			'date'                   => new \Twig_Filter_Method($this, 'userDate', array('needs_context' => true)),
			'time_length'            => new \Twig_Filter_Method($this, 'timeLength'),
			'slugify'                => new \Twig_Filter_Method($this, 'slugify'),
			'emphasize_words'        => new \Twig_Filter_Method($this, 'emphasizeWords', array('is_safe' => array('html'))),
			'strip_linebreaks'       => new \Twig_Filter_Method($this, 'stripLinebreaks'),
			'explode'                => new \Twig_Filter_Method($this, 'explodeString'),
			'split'                  => new \Twig_Filter_Method($this, 'explodeString'),
			'join'                   => new \Twig_Filter_Method($this, 'implodeArray'),
			'implode'                => new \Twig_Filter_Method($this, 'implodeArray'),
			'crc32'                  => new \Twig_Filter_Method($this, 'crc32'),
			'url_domain'             => new \Twig_Filter_Method($this, 'getUrlDomain'),
			'truncate'               => new \Twig_Filter_Method($this, 'strTruncate'),
			'first'                  => new \Twig_Filter_Method($this, 'getFirst'),
			'last'                   => new \Twig_Filter_Method($this, 'getLast'),
			'filesize_display'       => new \Twig_Filter_Method($this, 'filesizeDisplay'),
			'url_trim_scheme'        => new \Twig_Filter_Method($this, 'urlTrimScheme'),
			'country_name'           => new \Twig_Filter_Method($this, 'countryName'),
			'count_lines'            => new \Twig_Filter_Method($this, 'countLines'),
			'smart_wrap'             => new \Twig_Filter_Method($this, 'smartWrap'),

			'hex2rgb'                => new \Twig_Filter_Method($this, 'hex2rgb'),

			'trans'                  => new \Twig_Filter_Function('\\Application\\DeskPRO\\Twig\\Extension\\deskpro_twig_filter_dummy'),
			'transchoice'            => new \Twig_Filter_Function('\\Application\\DeskPRO\\Twig\\Extension\\deskpro_twig_filter_dummy'),
			'plain_template_filter'  => new \Twig_Filter_Method($this, 'plain_template_filter'),

			// Override for custom UTF-8 handling
			'upper'                  => new \Twig_Filter_Method($this, 'strUpper'),
			'lower'                  => new \Twig_Filter_Method($this, 'strLower'),
        );
    }

	public function getPath($name, $parameters = array())
    {
		try {
        	return App::getRouter()->generate($name, $parameters, false);
		} catch (\Symfony\Component\Routing\Exception\RouteNotFoundException $e) {
			if (App::isDebug()) {
				throw $e;
			}
			return '';
		}
    }

    public function getUrl($name, $parameters = array())
    {
		try {
        	return App::getRouter()->generate($name, $parameters, true);
		} catch (\Symfony\Component\Routing\Exception\RouteNotFoundException $e) {
			if (App::isDebug()) {
				throw $e;
			}
			return '';
		}
    }

	public function getBaseTemplateName($name)
	{
		$parts = explode(':', $name);
		$name = array_pop($parts);
		$name = str_replace('.html.twig', '', $name);
		return $name;
	}

	public function getConstant($name = '')
	{
		static $whitelist = array(
			'DP_BUILD_NUM'          => true,
			'DP_BUILD_TIME'         => true,
			'DPC_SITE_ID'           => true,
			'DPC_SITE_DOMAIN'       => true,
			'DPC_SITE_DOMAIN_ALT'   => true,
			'DPC_SITE_BUILD_NUM'    => true,
			'DPC_ACCOUNT_ID'        => true,
			'DPC_BILL_OVERDUE'      => true,
			'DPC_BILL_DATE'         => true,
			'DP_ENABLE_AGENT_LANG'  => true,
		);

		if (!$name || !defined($name) || !isset($whitelist[$name])) {
			return '';
		}

		return constant($name);
	}

	public function filesizeDisplay($size)
	{
		if ($size < 0) {
			return 'n/a';
		}

		return \Orb\Util\Numbers::filesizeDisplay($size);
	}

	public function getTimeGroupPhrase($time)
	{
		static $time_phrases = array(
			300        => '< 5 minutes',
			900        => '5 - 15 minutes',
			1800       => '15 - 30 minutes',
			3600       => '30 - 60 minutes',
			7200       => '1 - 2 hours',
			10800      => '2 - 3 hours',
			14400      => '3 - 4 hours',
			21600      => '4 - 6 hours',
			43200      => '6 - 12 hours',
			86400      => '12 - 24 hours',
			172800     => '1 - 2 days',
			259200     => '2 - 3 days',
			345600     => '3 - 4 days',
			432000     => '4 - 5 days',
			518400     => '5 - 6 days',
			604800     => '6 - 7 days',
			1209600    => '1 - 2 weeks',
			1814400    => '2 - 3 weeks',
			2419200    => '3 - 4 weeks',
			4838400    => '1 - 2 months',
			7257600    => '2 - 3 months',
			9676800    => '3 - 4 months',
			12096000   => '4 - 5 months',
			14515200   => '5 - 6 months',
		);

		foreach ($time_phrases as $min => $phrase) {
			if ($time <= $min) {
				return $phrase;
			}
		}

		return '> 6 months';
	}

	public function gravatar($email, $size = 80)
	{
		$hash = strtolower(md5($email));
		$url = 'http://www.gravatar.com/avatar/' . $hash . '?';
		$url .= '&d=' . App::get('router')->generate('serve_default_picture', array('s' => $size), true);

		return $url;
	}

	public function getFirst($var)
	{
		if (!$var) return null;
		return \Orb\Util\Arrays::getFirstItem($var);
	}

	public function getLast($var)
	{
		if (!$var) return null;
		return \Orb\Util\Arrays::getLastItem($var);
	}

	public function isArray($var)
	{
		return is_array($var);
	}

	public function getInstanceAbility($method)
	{
		$method = Strings::underscoreToCamelCase($method);
		return $this->container->getSystemService('instance_ability')->$method();
	}

	public function getServiceUrl($name, $params = null, $named_params = null, $html = true)
	{
		if (!$params || !is_array($params)) {
			$params = null;
		}
		if (!$named_params || !is_array($named_params)) {
			$params = null;
		}

		return $this->container->get('deskpro.service_urls')->get($name, $params, $named_params, $html);
	}

	public function getServiceUrlRaw($name, $params = null, $named_params = null)
	{
		return $this->getServiceUrl($name, $params, $named_params, false);
	}

	public function relativeTime($secs, $detail = 2)
	{
		return Dates::secsToReadable($secs, $detail);
	}

	public function strTruncate($str, $width = 80, $dots = true)
	{
		if (strlen($str) <= $width) {
			return $str;
		}

		if ($dots) {
			if ($dots === true) {
				$dots = '...';
			}
			return trim(substr($str, 0, $width). $dots);
		} else {
			return trim(substr($str, 0, $width));
		}
	}

	public function startCounter($name = 'default', $start = 1)
	{
		$this->counter_registry[$name] = 1;
		return '';
	}

	public function getCounter($name = 'default')
	{
		return isset($this->counter_registry[$name]) ? $this->counter_registry[$name] : 0;
	}

	public function incCounter($name = 'default')
	{
		if (!isset($this->counter_registry[$name])) {
			$this->counter_registry[$name] = 0;
		}

		$v = $this->counter_registry[$name]++;
		return $v;
	}

	public function getUrlDomain($string)
	{
		$urlinfo = @parse_url($string);
		if (!$urlinfo) {
			return $string;
		}

		return @$urlinfo['host'];
	}

	public function crc32($string)
	{
		$string = (string)$string;

		return sprintf("%u", crc32($string));
	}

	public function safeLinkUrlsHtml($text)
	{
		return Strings::linkifyHtml($text, true);
	}

	public function safeLinkUrls($text)
	{
		$text = htmlspecialchars($text);
		return Strings::linkifyHtml($text, true);
	}

	public function linkAgentShortCodeHtml($html)
	{
		$id_map = array(
			't' => array('Ticket', 'agent/#app.tickets,t.o:'),
			'p' => array('Person', 'agent/#app.people,p.o:'),
			'o' => array('Organization', 'agent/#app.people.orgs,o.o:'),
			'a' => array('Article', 'agent/#app.publish,a.o:'),
			'n' => array('News', 'agent/#app.publish,n.o:'),
			'd' => array('Download', 'agent/#app.publish,d.o:'),
			'i' => array('Feedback', 'agent/#app.feedback,i.o:'),
			'tw' => array('Tweet', 'agent/#app.twitter,tw.o:'),
		);

		$url = App::getSetting('core.deskpro_url');

		foreach ($id_map AS $prefix => $info) {
			$html = preg_replace(
				'/\{\{\s*' . $prefix . '-(\d+)\s*\}\}/',
				'<a href="' . $url . $info[1] . '$1">' . $info[0] . ' #$1</a>',
				$html
			);
		}

		return $html;
	}

	public function getAssetic($name)
	{
		$assetic_manager = $this->container->getSystemService('assetic_manager');
		return $assetic_manager->getUrl($name);
	}

	public function getAsseticRaw($name)
	{
		$assetic_manager = $this->container->getSystemService('assetic_manager');
		return $assetic_manager->getRawUrls($name);
	}

	public function implodeArray($array, $sep = ', ')
	{
		if (!$array || !is_array($array)) {
			return '';
		}
		return implode($array, $sep);
	}

	public function explodeString($string, $del = ',') {
		$ret = array();
		$string = (string)$string;

		foreach (explode($del, $string) as $p) {
			$ret[] = trim($p);
		}

		return $ret;
	}

	public function stripLinebreaks($str)
	{
		$str = str_replace(array("\r\n", "\n"), " ", $str);
		$str = str_replace(array("<br />", "<br/>", "<br>"), " ", $str);
		$str = str_replace(array("<p>", "</p>", "<p />", "<p/>"), " ", $str);

		return $str;
	}

	public function htmlGetAssetic($name, $options = array())
	{
		$raw_packs = App::getConfig('debug.raw_assets', array());
		$less_use_css = App::getConfig('debug.less_use_css_dir', false);
		$disable_client_cache = App::getConfig('debug.disable_client_cache', false);

		if ($raw_packs && (in_array($name, $raw_packs) OR in_array('all', $raw_packs) OR (in_array('all -vendors', $raw_packs) && $name != 'agent_vendors'))) {
			$urls = $this->getAsseticRaw($name);
		} else {
			$urls = array($this->getAssetic($name));
		}

		$qs_append = ($disable_client_cache ? time() : DP_BUILD_TIME);
		$html = array();

		foreach ($urls as $url) {
			$type = Strings::getExtension($url);

			$url .= '?' . $qs_append;

			switch ($type) {
				case 'js':
					$html[] = '<script type="text/javascript" src="' . $url . '"></script>';
					break;
				case 'css':
					if (!isset($options['media'])) {
						$options['media'] = 'screen,print';
					}
					$html[] = '<link rel="stylesheet" type="text/css" media="' . $options['media'] .'" href="' . $url .'" />';
					break;
				case 'less':
					if (!isset($options['media'])) {
						$options['media'] = 'screen,print';
					}

					if ($less_use_css) {
						$url = str_replace('/stylesheets-less/', '/stylesheets/', $url);
						$url = str_replace('.less', '.css', $url);
						$html[] = '<link rel="stylesheet" type="text/css" media="' . $options['media'] .'" href="' . $url .'" />';
					} else {
						$html[] = '<link rel="stylesheet/less" type="text/css" media="' . $options['media'] .'" href="' . $url .'" />';
					}
					break;
			}
		}

		return implode("\n", $html);
	}

	public function getData($id)
	{
		switch ($id) {
			case 'country_names':
				return \Orb\Data\Countries::getCountryNames();
				break;
			default:
				return null;
		}
	}

	public function emphasizeWords($string, $words)
	{
		if (!is_array($words)) {
			$words = Strings::splitWords($words);
		}

		if (!$words) {
			return $string;
		}

		$string = htmlspecialchars($string);
		foreach ($words as $w) {
			$w = htmlspecialchars($w);
			$string = preg_replace('#(\\b)(' . preg_quote($w, '#') . ')(\\b)#iu', '$1<em>$2</em>$3', $string);
		}

		return $string;
	}

	public function renderUsersource($usersource, $type, array $params = array())
	{
		return App::getSystemService('usersource_manager')->renderView($usersource, $type, $params);
	}

	public function slugify($str)
	{
		return Strings::slugifyTitle($str);
	}

	public function userDate($context, $date, $format = 'F j, Y H:i', $timezone = null)
	{
		// Backwards compat calls: args shifted back one
		if (!is_array($context)) {
			$args = func_get_args();
			if (!isset($args[1])) $args[1] = 'F j, Y H:i';
			if (!isset($args[2])) $args[2] = null;

			list ($date, $format, $timezone) = $args;
			$context = null;
		}

		switch ($format) {
			case 'full':
				//D, jS M Y
				$format = App::getSetting('core.date_full');
				break;

			case 'fulltime':
				//D, jS M Y g:ia
				$format = App::getSetting('core.date_fulltime');
				break;

			case 'day':
				//M j Y
				$format = App::getSetting('core.date_day');
				break;

			case 'day_short':
				//M j
				$format = App::getSetting('core.date_day_short');
				break;

			case 'time':
				//g:i a
				$format = App::getSetting('core.date_time');
				break;
		}

		if (!($date instanceof \DateTime)) {
			if (ctype_digit((string) $date)) {
				$date = new \DateTime('@'.$date);
				$date->setTimezone(new \DateTimeZone(date_default_timezone_get()));
			} else {
				try {
					$date_str = $date;
					$date = new \DateTime($date_str);
				} catch (\Exception $e) {}
			}
		}

		if (!($date instanceof \DateTime)) {
			$date_str = (string)$date;
			return "invalid_date($date_str)";
		}

		if ($timezone === null && $context && isset($context['context']['person_timezone'])) {
			$timezone = $context['context']['person_timezone'];
		}

		if ($timezone === null && App::getCurrentPerson()) {
			$timezone = App::getCurrentPerson();
		}

		if ($timezone instanceof \Application\DeskPRO\Entity\Person) {
			$timezone = $timezone->getDateTimezone();
		}

		if (null !== $timezone) {
			if (!($timezone instanceof \DateTimeZone)) {
				$timezone = new \DateTimeZone($timezone);
			}
		}

		if (!$timezone || $timezone == 'UTC') {
			$timezone = new \DateTimeZone('UTC');
		}

		$date->setTimezone($timezone);

		$prefix = 'user.time.';
		if (DP_INTERFACE == 'admin' || DP_INTERFACE == 'agent') {
			$prefix = 'agent.time.';
		}

		return $this->container->getTranslator()->date($format, $date, $prefix);
	}

	public function timeLength($length, $max_unit = null)
	{
		return \Application\DeskPRO\Util::getPrintableTimeLength($length, $max_unit);
	}

	public function formToken($name = '', $field_name = '_dp_security_token')
	{
		$html = '<input type="hidden" name="'.$field_name.'" value="' . App::getSession()->getEntity()->generateSecurityToken($name, 43200) . '" />';
		$html .= '<input type="hidden" name="_rt" value="' . App::getSession()->getEntity()->generateSecurityToken('request_token', 10800) . '" class="dp_request_token" />';

		return $html;
	}

	public function securityToken($name = '', $timeout = 43200)
	{
		return App::getSession()->getEntity()->generateSecurityToken($name, $timeout);
	}

	public function staticSecurityToken($name = '', $timeout = 43200)
	{
		return Util::generateStaticSecurityToken(md5(App::getAppSecret() . $name), $timeout);
	}

	public function staticSecurityTokenSecret($secret, $timeout = 43200)
	{
		return Util::generateStaticSecurityToken($secret, $timeout);
	}

	public function debugVar($var)
	{
		ob_start();
		var_dump($var, true);
		$str = ob_end_clean();

		return $str;
	}

	public function getType($var, $basename = true)
	{
		// Primitive types
		if (!is_object($var)) {
			$var_type = gettype($var);
		// Classes
		} else {
			$var_type = get_class($var);

			if ($basename) {
				$var_type = Util::getBaseClassname($var_type);
			}

			if ($var instanceof \Doctrine\ORM\Proxy\Proxy) {
				$var_type = preg_replace('#(^|\\\\)ApplicationDeskPROEntity(.*?)Proxy$#', '$2', $var_type);
			}
		}

		return $var_type;
	}

	public function getObjectPath($object, array $params = array(), $context = 'user')
	{
		$generator = $this->container->get('router')->getGenerator();
		return $generator->generateObjectUrl($object, $params, $context);
	}

	public function getObjectPathAgent($object, array $params = array())
	{
		return $this->getObjectPath($object, $params, 'agent');
	}

	public function compareType($var, $type)
	{
		// Primitive types
		if (!is_object($var)) {
			$var_type = gettype($var);
			return strpos($var_type, $type) !== false;

		// Classes
		} else {
			$var_type = get_class($var);

			// Passes Some\MyClass as well as just MyClass, but not SomeOther\MyClass against Some\MyClass
			return (strpos($var_type, $type) !== false AND Util::getBaseClassname($var_type) == Util::getBaseClassname($type));
		}
	}

	public function flashMessage($name)
	{
		$session = $this->container->get('session');
		return $session->getFlash($name, null);
	}

	public function encNum($num)
	{
		return Util::baseEncode((int)$num, Util::LETTERS_ALPHABET);
	}

	public function decNum($num)
	{
		return Util::baseDecode((int)$num, Util::LETTERS_ALPHABET);
	}

	public function rand($min = 1, $max = 10)
	{
		return mt_rand((int)$min, (int)$max);
	}

	public function isUserGuest($person = null)
	{
		if (!$person) {
			$person = $this->container->get('deskpro.session_person');
		}

		if (!$person['id']) {
			return true;
		}

		return false;
	}

	public function isUserUser($person = null)
	{
		if (!$person) {
			$person = $this->container->get('deskpro.session_person');
		}

		if ($person['id']) {
			return true;
		}

		return false;
	}

	public function isUserAgent($person)
	{
		if (!$person) {
			$person = $this->container->get('deskpro.session_person');
		}

		if ($person['is_agent']) {
			return true;
		}

		return false;
	}

	public function isUserAdmin($person)
	{
		if (!$person) {
			$person = $this->container->get('deskpro.session_person');
		}

		if ($person['is_admin']) {
			return true;
		}

		return false;
	}

	/**
	 * Checks the special _partial flag in incoming requests to see if the user wants a partial
	 *
	 * @return bool
	 */
	public function isPartialRequest()
	{
		return $this->container->get('request')->isPartialRequest();
	}

	public function strRepeat($str, $count = 1)
	{
		return str_repeat($str, $count);
	}

	public function strTrim($str)
	{
		return trim($str);
	}

	/**
	 * A unique ID generator usually used to generate unique element ID's. Unique
	 * ID's are generally needed only in the agent interface where things share the same dom.
	 *
	 * @param string $prefix
	 * @return string
	 */
	public function elUid($prefix = 'dp_')
	{
		return $prefix
			   . Util::baseEncode(time() - strtotime('-15 days'), 'base36') // 4 digits. 15 days to save a few digits
			   . Util::baseEncode(mt_rand(36, 1295), 'base36') // 2 digits
			   . Util::baseEncode(Util::requestUniqueId(), 'base36'); // 1-2 digits
	}

	/**
	 * Just gets a full helpdesk URL minus the http:// and www bits.
	 * Makes it prettier when displaying links in emails.
	 *
	 * @param $name
	 * @param array $parameters
	 * @return mixed|string
	 */
	public function urlDisplay($name, array $parameters = array())
	{
		$url = $this->urlFull($name, $parameters);
		$url = preg_replace('#^https?://(www\.)?#i', '', $url);

		return $url;
	}

	public function urlFull($name, array $parameters = array())
	{
		return $this->container->get('router')->getGenerator()->generateUrl($name, $parameters, false);
	}

	public function helpdeskUrl($path)
	{
		return App::getSetting('core.deskpro_url') . ltrim($path, '/');
	}

	public function urlFragment($name, array $parameters = array())
	{
		return $this->container->get('router')->getGenerator()->generateFragment($name, $parameters, false);
	}

	public function renderCustomField($display_array, array $vars = array())
	{
		$handler = $display_array['handler'];

		if (is_object($display_array)) {
			$display_array = $display_array->toArray();
		}
		$vars = array_merge($display_array, $vars);

		return $handler->renderHtml($display_array['value'], $vars);
	}

	public function renderCustomFieldForm($display_array, array $vars = array())
	{
		$handler = $display_array['handler'];
		$formView = $display_array['formView'];

		if (is_object($display_array)) {
			$display_array = $display_array->toArray();
		}
		$vars = array_merge($display_array, $vars);

		return $handler->renderFormHtml($formView, $vars);
	}

	public function renderCustomFieldText($display_array, array $vars = array())
	{
		$handler = $display_array['handler'];

		if (is_object($display_array)) {
			$display_array = $display_array->toArray();
		}
		$vars = array_merge($display_array, $vars);

		return $handler->renderText($display_array['value'], $vars);
	}

	public function getLanguageHtmlAttributes($language = null)
	{
		if (!($language instanceof \Application\DeskPRO\Entity\Language)) {
			$language = App::getLanguage();
		}

		$attributes = array(
			'dir' => ($language->is_rtl ? 'dir="rtl"' : 'dir="ltr"'),
			'lang' => 'lang="' . htmlspecialchars(substr($language->locale, 0, 2), \ENT_QUOTES, 'UTF-8') . '"'
		);

		return implode(' ', $attributes);
	}

	public function getLanguageArrow($ltr, $rtl = null, $language = null)
	{
		if ($rtl === null) {
			switch ($ltr) {
				case 'right': $ltr = '&rarr;'; $rtl = '&larr;'; break;
				case 'left': $ltr = '&larr;'; $rtl = '&rarr;'; break;
				default: return 'unknown';
			}
		}

		if (!($language instanceof \Application\DeskPRO\Entity\Language)) {
			$language = App::getLanguage();
		}

		if ($language->is_rtl) {
			return $rtl;
		} else {
			return $ltr;
		}
	}

	public function isRtl($language = null)
	{
		if (!($language instanceof \Application\DeskPRO\Entity\Language)) {
			$language = App::getLanguage();
		}

		return $language->is_rtl;
	}

	public function getPhraseDev($phrase_name, array $vars = array())
	{
		return $this->container->get('deskpro.core.translate')->replaceVarsInString($phrase_name, $vars);
	}

	public function hasPhrase($phrase_name)
	{
		return $this->container->get('deskpro.core.translate')->hasPhrase($phrase_name);
	}

	public function getPhraseText($phrase_name)
	{
		$p = $this->container->get('deskpro.core.translate')->getPhraseText($phrase_name);
		if ($p && dp_get_config('debug.language_test_mode')) {
			$p = "^$p^";
		}

		return $p;
	}

	public function getPhrase($context, $phrase_name, $vars = null, $raw = false)
	{
		if (!$vars || !is_array($vars)) {
			$vars = array();
		}

		if (!$raw) {
			foreach ($vars as &$v) {
				$v = htmlspecialchars($v, \ENT_QUOTES, 'UTF-8');
			}
		}

		$vars['_context'] = $context;

		return $this->container->get('deskpro.core.translate')->phrase($phrase_name, $vars);
	}

	public function getPhraseObject($phrase_name, $property = null)
	{
		return $this->container->get('deskpro.core.translate')->getPhraseObject($phrase_name, $property);
	}

	public function isDebugMode()
	{
		return App::isDebug();
	}

	public function getMd5($string)
	{
		return md5($string);
	}

	public function assetFull($location)
	{
		$url = App::getSetting('core.deskpro_assets_full_url');
		if (!$url) {
			$url = App::getSetting('core.deskpro_url');
			$url = trim(str_replace('/index.php', '', $url), '/');
			$url .= (App::getConfig('static_path') ?: '/web') . '/';
		}
		return $url . ltrim($location, '/');
	}

	public function rawUrlEncode($str)
	{
		return rawurlencode($str);
	}

	public function strUpper($str)
	{
		return Strings::utf8_strtoupper($str);
	}

	public function strLower($str)
	{
		return Strings::utf8_strtolower($str);
	}

	public function hex2rgb($hex)
	{
		$hex = preg_replace("/[^0-9A-Fa-f]/", '', $hex);
		$rgb = array();
		if (strlen($hex) == 6) {
			$color_val = hexdec($hex);
			$rgb['red'] = 0xFF & ($color_val >> 0x10);
			$rgb['green'] = 0xFF & ($color_val >> 0x8);
			$rgb['blue'] = 0xFF & $color_val;
		} elseif (strlen($hex) == 3) {
			$rgb['red'] = hexdec(str_repeat(substr($hex, 0, 1), 2));
			$rgb['green'] = hexdec(str_repeat(substr($hex, 1, 1), 2));
			$rgb['blue'] = hexdec(str_repeat(substr($hex, 2, 1), 2));
		} else {
			return false;
		}

		return $rgb;
	}

	public function captchaHtml($type = 'default')
	{
		$captcha = $this->container->getSystemObject('form_captcha', array('type' => $type));
		return $captcha->getHtml();
	}

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'deskpro_templating';
    }

	public function includeFile($path)
	{
		if (!dp_get_config('enable_include_file')) {
			return '';
		}

		if (!file_exists($path)) {
			$e = new \Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException("File does not exist: " . $path);
			\DeskPRO\Kernel\KernelErrorHandler::logErrorInfo($e);
			return '';
		}

		return file_get_contents($path);
	}

	public function includePhpFile($path, array $with = null)
	{
		if (!dp_get_config('enable_include_file')) {
			return '';
		}

		if ($with !== null) {
			extract($with, \EXTR_SKIP);
		}

		if (!file_exists($path)) {
			$e = new \Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException("File does not exist: " . $path);
			\DeskPRO\Kernel\KernelErrorHandler::logException($e, false, 'tpl_include_php_file');
			return '';
		}

		ob_start();
		include($path);
		$content = ob_get_clean();

		return $content;
	}

	public function dumpVar($var)
	{
		return \DeskPRO\Kernel\KernelErrorHandler::varToString($var);
	}

	public function urlTrimScheme($url, $trim_adv = false)
	{
		$ret = preg_replace('#^https?://#i', '', $url);

		if ($trim_adv) {
			$ret = preg_replace('#^www\.#i', '', $ret);

			// Hashes/tokens
			$ret = preg_replace('#([a-zA-Z0-9\-_]+)=([a-zA-Z0-9]{32})&?#', '', $ret);
			$ret = preg_replace('#([a-zA-Z0-9\-_]+)=([a-zA-Z0-9]{40})&?#', '', $ret);
			$ret = preg_replace('#([a-zA-Z0-9\-_]+)=([a-zA-Z0-9]{6})\-([a-zA-Z]{10})\-([a-zA-Z0-9]{40})&?#', '', $ret);

			$ret = trim($ret, '/?#&');

			// Trailing index.php, index.html
			$ret = preg_replace('#/index\.(html|php)#', '', $ret);
		}

		return $ret;
	}

	public function countryName($code)
	{
		$name = Countries::getCountryFromCode($code);
		if (!$name) {
			return $code;
		}

		return $name;
	}

	protected $_widgetCache = array();

	public function getWidgets($baseId, $page, $location, $position = '*', $data = array())
	{
		$widgets = $this->_getPageLocationWidgets($page, $location, $position);
		if (!$widgets) {
			return '';
		}

		$output = '';
		foreach ($widgets AS $widget) {
			$output .= $this->_insertWidget($baseId, $widget,
				'<div class="profile-box-container" id="{id}_container">'
					. '<header><h4 id="{id}_tab">{title}</h4></header>'
					. '<section class="widget-content" id="{id}" data-widget="{widget}">{html}</section>'
				. '</div>',
				$data
			);
		}

		return $output;
	}

	public function getWidgetsRaw($page, $location, $position = '')
	{
		return $this->_getPageLocationWidgets($page, $location, $position);
	}

	protected function _getPageLocationWidgets($page, $location, $position = '')
	{
		if (!array_key_exists($page, $this->_widgetCache)) {
			$this->_widgetCache[$page] = App::getEntityRepository('DeskPRO:Widget')->getEnabledPageWidgetsGrouped($page);
		}

		if (empty($this->_widgetCache[$page][$location])) {
			return array();
		} else {
			if ($position === '') {
				$output = array();
				foreach ($this->_widgetCache[$page][$location] AS $widgets) {
					foreach ($widgets AS $widget) {
						$output[] = $widget;
					}
				}
				return $output;
			} else if (!empty($this->_widgetCache[$page][$location][$position])) {
				return $this->_widgetCache[$page][$location][$position];
			} else {
				return array();
			}
		}
	}

	public function getWidgetHtmlId($baseId, \Application\DeskPRO\Entity\Widget $widget)
	{
		return "{$baseId}-widget-{$widget->id}";
	}

	protected function _insertWidget($baseId, \Application\DeskPRO\Entity\Widget $widget, $wrapper, $data = array())
	{
		$jsOnly = !$widget->page_location;
		$htmlId = ($jsOnly ? '' : $this->getWidgetHtmlId($baseId, $widget));

		if (!is_array($data) && !($data instanceof \ArrayAccess)) {
			$data = array();
		}
		$data['base_id'] = $baseId;
		$data['html_id'] = $htmlId;
		$data['settings'] = App::get(App::SERVICE_SETTINGS);

		if ($jsOnly) {
			$output = '';
		} else {
			$output = strtr($wrapper, array(
				'{id}' => $htmlId,
				'{widget}' => $widget->id,
				'{html}' => $this->_replaceWidgetPlaceholders($widget->html, $data, 'html'),
				'{title}' => $widget->title
			));
		}

		if ($widget->css) {
			$css = $this->_replaceWidgetPlaceholders($widget->css, $data, 'css');
			$hash = md5($css);
			$output .= '<style type="text/css" data-widget="' . $widget->id . '" data-hash="' . $hash . '">' . $css . '</style>';
		}
		if ($widget->js) {
			$js = $this->_replaceWidgetPlaceholders($widget->js, $data, 'js');
			$output .= '<script type="text/javascript" data-widget="' . $widget->id . '" data-html-id="' . $htmlId . '">'
				. $js . '</script>';
		}

		return $output;
	}

	protected function _replaceWidgetPlaceholders($content, $data, $context)
	{
		return preg_replace_callback('/\{\{\s*([a-z0-9_.]+)\s*\}\}/i', function (array $match) use ($data, $context) {
			$parts = explode('.', $match[1]);
			$reference = $data;
			while (($part = array_shift($parts)) !== null) {
				if ($part == '') {
					continue;
				}

				if (!is_array($reference) && !($reference instanceof \ArrayAccess)) {
					$reference = '';
					break;
				}

				if (isset($reference[$part])) {
					$reference = $reference[$part];

					if ($reference instanceof \Application\DeskPRO\Settings\Settings) {
						$reference = ($parts ? $reference[implode('.', $parts)] : '');
						break;
					}
				} else {
					$reference = '';
					break;
				}
			}

			$reference = strval($reference);

			switch ($context) {
				case 'html':
					return htmlspecialchars($reference);

				case 'js':
					return strtr($reference, array(
						'"' => '\\"',
						"'" => "\\'",
						"\n" => '\n',
						"\r" => '\r',
						'\\' => '\\\\',
						'</script>' => '<\\/script>'
					));

				default:
					return $reference;
			}
		}, $content);
	}

	public function getWidgetTabsHeader($baseId, $page, $location, array $tabs)
	{
		$originalCount = count($tabs);

		foreach ($this->_getPageLocationWidgets($page, $location, 'tab') AS $widget) {
			$htmlId = $this->getWidgetHtmlId($baseId, $widget);
			$tabs[$htmlId] = $widget->title;
		}

		foreach ($tabs AS $key => $title) {
			if ($title === false) {
				unset($tabs[$key]);
			}
		}

		if (!$tabs) {
			return '';
		} else if (count($tabs) == 1 && $originalCount == 1) {
			return '<h4>' . reset($tabs) . '</h4>';
		} else {
			$tabHtml = array();
			$on = false;
			foreach ($tabs AS $id => $title) {
				if (!$on) {
					$onHtml = ' class="on"';
					$on = true;
				} else {
					$onHtml = '';
				}
				$tabHtml[] = '<li data-tab-for="#' . $id . '" id="' . $id . '_tab"' . $onHtml . '>' . $title . '</li>';
			}
			return '<nav data-element-handler="DeskPRO.ElementHandler.SimpleTabs"><ul>' . implode('', $tabHtml) . '</ul></nav>';
		}
	}

	public function getWidgetTabsBody($baseId, $page, $location, $wrapper, $data = array())
	{
		$output = '';
		foreach ($this->_getPageLocationWidgets($page, $location, 'tab') AS $widget) {
			$output .= $this->_insertWidget($baseId, $widget,
				'<' . $wrapper . ' class="widget-content" id="{id}" data-widget="{widget}" style="display: none">{html}</' . $wrapper . '>',
				$data
			);
		}

		return $output;
	}

	public function getJsSsoLoader()
	{
		$person = App::getCurrentPerson();
		$is_first_page = App::getSession()->isFirstPage();

		$sources = App::getEntityRepository('DeskPRO:Usersource')->getJsSsoUsersources();
		$output = array();
		foreach ($sources AS $source) {
			$adapter = $source->getAdapter()->getAuthAdapter();
			$output[] = $adapter->getSsoHtmlLoaderOutput($source, $this, $person, $is_first_page);
		}

		return implode("\n\n", $output);
	}

	public function getJsSsoShare()
	{
		$person = App::getCurrentPerson();

		if (!$person || $person->isGuest()) {
			return '';
		}

		$output = array();
		foreach ($person->usersource_assoc as $assoc) {
			$us = $assoc->usersource;
			if (!$us->isCapable('share_session')) {
				continue;
			}

			$adapter = $us->getAdapter()->getAuthAdapter();
			$output[] = $adapter->getSsoShareSessionHtml($assoc->identity);
		}

		return implode("\n\n", $output);
	}

	/**
	 * This gets an attribute from an array without casting the key to
	 * an int or a string. This is useful when the key is a number that
	 * is larger than what an int can hold.
	 *
	 * @param array $array
	 * @param mixed $key
	 *
	 * @return mixed
	 */
	public function getArrayAttribute($array, $key)
	{
		return array_key_exists($key, $array) ? $array[$key] : null;
	}


	public function countLines($str)
	{
		if (is_object($str) && method_exists($str, '__toString')) {
			$str = (string)$str;
		}
		if (!is_scalar($str)) {
			return 0;
		}

		$str = Strings::standardEol($str);
		return substr_count($str, "\n") + 1;
	}

	public function min()
	{
		$args = func_get_args();
		return call_user_func_array('min', $args);
	}

	public function max()
	{
		$args = func_get_args();
		return call_user_func_array('max', $args);
	}

	public function plain_template_filter($content)
	{
		$content = preg_replace('#<\s*script#i', '<deskpro_script', $content);
		$content = preg_replace('#<\s*/\s*script#i', '</deskpro_script', $content);
		return $content;
	}

	public function match($str, $regex)
	{
		$regex = Strings::getInputRegexPattern($regex);
		if (!$regex) {
			return false;
		}

		return preg_match($regex, $str);
	}

	public function set_tplvar($context, $k, $v)
	{
		if (!isset($context['tplvars'])) {
			$context['tplvars'] = new \stdClass();
		}

		$context['tplvars']->$k = $v;
		return;
	}

	public function getTplSourceTemplate($id, $name)
	{
		$source = App::getContainer()->getTemplating()->getSource($name);
		$source = str_replace('<script>',  '%startScript%', $source);
		$source = str_replace('</script>',  '%endScript%', $source);
		$source = '<script type="text/x-deskpro-tmpl" id="'.$id.'">' . $source . '</script>';
		return $source;
	}

	public function smartWrap($string, $len = 50, $break = null)
	{
		if ($break === null) {
			$break = Strings::ZERO_WIDTH_SPACE;
		}
		return Strings::smartWordWrap($string, $len, $break);
	}
}

function deskpro_twig_filter_dummy($ret) {
	return $ret;
}