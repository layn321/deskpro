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
 * @subpackage
 */

namespace Application\DeskPRO\Settings;

/**
 * Simple wrapper around fetching URLs eg. to a helpdesk article.
 * Removes the URLs from templates and files and puts them into a config instead
 * so it's easier to edit.
 */
class ServiceUrls
{
	protected $urls = array();


	/**
	 * @param $file
	 */
	public function loadPack($file)
	{
		$pack_urls = require($file);
		if (!$pack_urls) {
			$pack_urls = array();
		}

		$this->urls = array_merge($this->urls, $pack_urls);
	}


	/**
	 * @param string $name            Name of the URL
	 * @param array  $params          Query params to append to the URL
	 * @param array  $named_params    Named parameters in the URL {{somevar}}
	 * @param bool   $html            True if this is going to be used in HTML. Arg separater becomes &amp;
	 * @return string
	 */
	public function get($name, array $params = null, array $named_params = null, $html = true)
	{
		$url = isset($this->urls[$name]) ? $this->urls[$name] : '';

		if ($params) {
			if (strpos($url, '?') === false) {
				$url .= '?';
			} else {
				$url .= $html ? '&amp;' : '&';
			}

			$url .= http_build_query($params, null, $html ? '&amp;' : '&');
		}

		if ($named_params) {
			foreach ($named_params as $k => $v) {
				$url = str_replace(array('{{'.$k.'}}', '{{ '.$k.' }}'), $v, $url);
			}
		}

		return $url;
	}


	/**
	 * @param $name
	 * @return bool
	 */
	public function has($name)
	{
		return isset($this->urls[$name]);
	}
}