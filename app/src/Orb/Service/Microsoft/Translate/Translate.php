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

namespace Orb\Service\Microsoft\Translate;

use Guzzle\Http\Client;

class Translate
{
	const OAUTH_AUTH       = 'https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/';
	const OAUTH_SCOPE_URL  = 'http://api.microsofttranslator.com';
	const API_URL          = 'http://api.microsofttranslator.com/V2/Http.svc/';

	const FORMAT_WAV       = 'audio/wav';
	const FORMAT_MP3       = 'audio/mp3';

	const OPT_MINSIZE       = 'MinSize';
	const OPT_MAXQUALITY    = 'MaxQuality';

	const TYPE_TEXT         = 'text/plain';
	const TYPE_HTML         = 'text/html';

	const CAT_GENERAL       = 'general';

	/**
	 * @var string
	 */
	protected $client_id;

	/**
	 * @var string
	 */
	protected $client_secret;

	/**
	 * @var string
	 */
	protected $access_token;

	/**
	 * @var \Guzzle\Http\Client
	 */
	protected $oauth_http_client;

	/**
	 * @var \Guzzle\Http\Client
	 */
	protected $service_http_client;


	/**
	 * @param string        $client_id
	 * @param string        $client_secret
	 * @param null|string   $access_token    Optional existing access token to use. This prevents having to make the additional request to fetch a new one.
	 */
	public function __construct($client_id, $client_secret, $access_token = null)
	{
		$this->client_id     = $client_id;
		$this->client_secret = $client_secret;
		$this->access_token  = $access_token;
	}


	/**
	 * Set an existing access token. Set null to clear the current
	 * access token so a new one is fetched.
	 *
	 * @param string $access_token
	 */
	public function setAccessToken($access_token)
	{
		$this->access_token = $access_token;
	}


	/**
	 * Get the current access token. If no access token is set, a new one will
	 * be fetched.
	 *
	 * @return string
	 */
	public function getAccessToken()
	{
		if ($this->access_token !== null) {
			return $this->access_token;
		}

		$request  = $this->getOauthHttpClient()->post()->addPostFields(array(
			'grant_type'    => 'client_credentials',
			'scope'         => self::OAUTH_SCOPE_URL,
			'client_id'     => $this->client_id,
			'client_secret' => $this->client_secret,
		));

		$response = $request->send();
		$data = $response->json();

		$this->access_token = $data['access_token'];
		return $this->access_token;
	}


	/**
	 * Translates a text string from one language to another
	 *
	 * @param string|string[]  $text          A string or array of strings
	 * @param string|null      $from          Language to translate from, or null to auto-detect
	 * @param string           $to            Language to translate to
	 * @param string           $content_type  Content type of the string. HTML must be well-formed
	 * @return string|string[]
	 */
	public function translate($text, $from, $to, $content_type = self::TYPE_TEXT, $category = self::CAT_GENERAL)
	{
		$from = $this->getNearestTranslateLocale($from);
		$to = $this->getNearestTranslateLocale($to);

		if (is_array($text)) {

			$post_body = array();
			$post_body[] = '<TranslateArrayRequest>';
			$post_body[] = "\t<AppId/>";
			if ($from) {
				$post_body[] = "\t<From>$from</From>";
			}

			$post_body[] = "\t<Options>";
			$post_body[] = "\t\t<Category xmlns=\"http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2\">$category</Category>";
			$post_body[] = "\t\t<ContentType xmlns=\"http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2\">$content_type</ContentType>";
			$post_body[] = "\t\t<ReservedFlags xmlns=\"http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2\"/>";
			$post_body[] = "\t\t<State xmlns=\"http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2\"/>";
			$post_body[] = "\t\t<Uri xmlns=\"http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2\"/>";
			$post_body[] = "\t\t<User xmlns=\"http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2\"/>";
			$post_body[] = "\t</Options>";

			$post_body[] = "\t<Texts>";
			foreach ($text as $t) {
				$post_body[] = "\t\t<string xmlns=\"http://schemas.microsoft.com/2003/10/Serialization/Arrays\">".$this->escapeXml($t)."</string>";
			}
			$post_body[] = "\t</Texts>";
			$post_body[] = "\t<To>$to</To>";
			$post_body[] = '</TranslateArrayRequest>';
			$post_body = implode("\n", $post_body);

			$request = $this->getServiceHttpClient()->post(
				'TranslateArray',
				null,
				$post_body
			);

			$response = $request->send();

			$raw_data = $response->xml();
			$data = array();
			foreach ($raw_data as $l) {
				$data[] = (string)$l->TranslatedText;
			}

			return $data;

		} else {
			$request = $this->getServiceHttpClient()->get(array('Translate{?text,from,to,contentType,category}', array(
				'text'        => $text,
				'from'        => $from ?: '',
				'to'          => $to,
				'contentType' => $content_type,
				'category'    => $category
			)));

			$response = $request->send();

			$raw_data = $response->xml();

			$lang = (string)$raw_data;
			return $lang;
		}
	}


	/**
	 * Use the Detect Method to identify the language of a selected piece of text.
	 *
	 * @see http://msdn.microsoft.com/en-us/library/ff512411.aspx
	 * @see http://msdn.microsoft.com/en-us/library/ff512412.aspx
	 * @param  string|string $text   A string or array of strings to detect
	 * @return string|array          The lang or array of lang IDs
	 * @throws \InvalidArgumentException
	 */
	public function detect($text)
	{
		if (is_array($text)) {

			$request = $this->getServiceHttpClient()->post(
				'DetectArray',
				null,
				$this->createArrayOfStringXmlBody($text)
			);

			$response = $request->send();

			$raw_data = $response->xml();

			$langs = array();

			foreach ($raw_data->string as $l) {
				$langs[] = (string)$l;
			}

			return $langs;

		} else {
			$request = $this->getServiceHttpClient()->get(array('Detect{?text}', array('text' => $text)));
			$response = $request->send();

			$raw_data = $response->xml();

			$lang = (string)$raw_data;
			return $lang;
		}
	}


	/**
	 * Returns a wave or mp3 stream of the passed-in text being spoken in the desired language.
	 *
	 * @see http://msdn.microsoft.com/en-us/library/ff512420.aspx
	 * @param string $text    A string containing a sentence or sentences of the specified language to be spoken for the wave stream. The size of the text to speak must not exceed 2000 characters.
	 * @param string $lang    A string representing the supported language code to speak the text in.
	 * @param string $format  A string specifying the content-type ID
	 * @param string $opt     A string specifying the quality of the audio signals
	 * @return string
	 */
	public function speak($text, $lang, $format = self::FORMAT_WAV, $opt = self::OPT_MINSIZE)
	{
		$request = $this->getServiceHttpClient()->get(array('Speak{?text,language,format,options}',
			'text'     => $text,
			'language' => $lang,
			'format'   => $format,
			'options'  => $opt,
		));

		$response = $request->send();

		return $response->getBody(true);
	}


	/**
	 * Obtain a list of language codes representing languages that are supported by the Translation Service.
	 *
	 * @see http://msdn.microsoft.com/en-us/library/ff512416.aspx
	 * @param bool $use_local  True to use the local cache (dont do a service request)
	 * @return array
	 */
	public function getLanguagesForTranslate($use_local = true)
	{
		if ($use_local) {
			$file = __DIR__.'/data/langs_for_translate.php';
			if (file_exists($file)) {
				return require($file);
			}
		}

		$request = $this->getServiceHttpClient()->get('GetLanguagesForTranslate');
		$response = $request->send();

		$raw_data = $response->xml();

		$data = array();
		foreach ($raw_data->string as $r) {
			$data[] = (string)$r;
		}

		return $data;
	}


	/**
	 * Retrieves the languages available for speech synthesis.
	 *
	 * @see http://msdn.microsoft.com/en-us/library/ff512415.aspx
	 * @param bool $use_local  True to use the local cache (dont do a service request)
	 * @return array
	 */
	public function getLanguagesForSpeak($use_local = true)
	{
		if ($use_local) {
			$file = __DIR__.'/data/langs_for_speak.php';
			if (file_exists($file)) {
				return require($file);
			}
		}

		$request = $this->getServiceHttpClient()->get('GetLanguagesForSpeak');
		$response = $request->send();

		$raw_data = $response->xml();

		$data = array();
		foreach ($raw_data->string as $r) {
			$data[] = (string)$r;
		}

		return $data;
	}


	/**
	 * Retrieves friendly names for the languages passed in as the parameter languageCodes, and localized using the passed locale language.
	 *
	 * This will use the local data cache unless a lang code could not be found, then a request against the service is made.
	 *
	 * @see http://msdn.microsoft.com/en-us/library/ff512414.aspx
	 * @param string[] $lang_codes An array of lang codes
	 * @param string   $locale     The locale to get names for
	 * @param bool     $use_local  True to use the local cache of names (dont do a service request)
	 * @return array
	 */
	public function getLanguageNames(array $lang_codes, $locale = 'en', $use_local = true)
	{
		if ($use_local) {
			$file = __DIR__.'/data/' . $locale . '.php';
			if (file_exists($file)) {
				$all_names = require($file);

				$names = array();
				foreach ($lang_codes as $code) {
					if (isset($all_names[$code])) {
						$names[$code] = $all_names[$code];
					}
				}

				if (count($names) == count($lang_codes)) {
					return $names;
				}
			}
		}

		$request = $this->getServiceHttpClient()->post(array('GetLanguageNames{?locale}', array('locale' => $locale)));
		$request->setBody($this->createArrayOfStringXmlBody($lang_codes));

		$response = $request->send();

		$raw_data = $response->xml();

		$data = array();
		foreach ($raw_data as $k => $r) {
			$data[$lang_codes[$k]] = (string)$r;
		}

		return $data;
	}

	/**
	 * Just like getLanguageNames except returns just a string for a single lang code.
	 *
	 * @param string[] $lang_codes An array of lang codes
	 * @param string   $locale     The locale to get names for
	 * @param bool     $use_local  True to use the local cache of names (dont do a service request)
	 * @return array
	 */
	public function getSingleLanguageName($lang_code, $locale = 'en', $use_local = true)
	{
		$names = $this->getLanguageNames(array($lang_code), $locale, $use_local);
		return array_pop($names);
	}


	/**
	 * @return Client
	 */
	protected function getOauthHttpClient()
	{
		if ($this->oauth_http_client !== null) {
			return $this->oauth_http_client;
		}

		$this->oauth_http_client = new Client(self::OAUTH_AUTH, array(
			'ssl.certificate_authority' => false
		));
		return $this->oauth_http_client;
	}


	/**
	 * @return Client
	 */
	protected function getServiceHttpClient()
	{
		if ($this->service_http_client !== null) {
			return $this->service_http_client;
		}

		$this->service_http_client = new Client(self::API_URL, array(
			'ssl.certificate_authority' => false
		));
		$this->service_http_client->setDefaultHeaders(array(
			'Authorization' => 'Bearer ' . $this->getAccessToken(),
			'Content-Type'  => 'text/xml'
		));
		return $this->service_http_client;
	}


	/**
	 * @param array $strings
	 * @return string
	 */
	public function createArrayOfStringXmlBody(array $strings)
	{
		$body  = '<ArrayOfstring xmlns="http://schemas.microsoft.com/2003/10/Serialization/Arrays" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">';
		$body .= "\n";
		foreach ($strings as $s) {
			$body .= "\t<string>" . $this->escapeXml($s) . "</string>\n";
		}
		$body .= '</ArrayOfstring>';

		return $body;
	}


	/**
	 * @param string $str
	 * @return string
	 */
	protected function escapeXml($str)
	{
		return str_replace(
			array("&",     "<",    ">",    '"',      "'"),
			array("&amp;", "&lt;", "&gt;", "&quot;", "&apos;"),
			$str
		);
	}


	/**
	 * Checks locale to see if its supported and gets the nearest if its not. For example, 'en_US' is not supported
	 * specifically but 'en' is.
	 *
	 * @param string $locale The locale to check
	 * @return string The locale replaced
	 */
	public function getNearestTranslateLocale($locale)
	{
		$avail = $this->getLanguagesForTranslate();

		if (in_array($locale, $avail)) {
			return $locale;
		}

		if (strpos($locale, '_')) {
			list ($top, ) = explode('_', $locale, 2);
			// Try again with just the first part
			return $this->getNearestTranslateLocale($top);
		}

		// Try to a case-i match
		$locale_i = strtolower($locale);
		foreach ($avail as $test) {
			if (strtolower($test) == $locale_i) {
				return $test;
			}
		}

		// No matches, return original which will probably fail
		return $locale;
	}
}