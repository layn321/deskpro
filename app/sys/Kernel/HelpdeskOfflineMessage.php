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

namespace DeskPRO\Kernel;

use Application\DeskPRO\App;

class HelpdeskOfflineMessage
{
	public static function getOfflinePage($message = null)
	{
		$page_html = file_get_contents(DP_ROOT . '/src/Application/DeskPRO/Resources/views/helpdesk-disabled.html');

		if ($message === null) {
			$page_html = str_replace('{{ OFFLINE_MESSAGE }}', self::getOfflineMessage(), $page_html);
		} else {
			$page_html = str_replace('{{ OFFLINE_MESSAGE }}', $message, $page_html);
		}

		return $page_html;
	}

	public static function getLicenseErrorPage($type, $base_url)
	{
		$vars = array(
			'type'        => $type,
			'base_url'    => $base_url,
			'asset_url'   => str_replace('/index.php', '', $base_url) . '/web',
			'billing_url' => $base_url . '/billing/',
			'license_id'  => License::getLicense()->getLicenseId(),
			'title'       => 'License Error',
			'message'     => ''
		);

		$title = $message = null;

		$subtype = null;
		if (strpos($type, '.') !== false) {
			list ($type, $subtype) = explode('.', $type, 2);
		}

		switch ($type) {
			case 'agents':
				$tpl_file = 'license-error';
				$message = 'You have more agents than your license allows.';
				break;

			case 'copyright':
				$tpl_file = 'license-error';
				$message = 'You are not allowed to remove the copyright without copyright removal.';
				break;

			case 'expired':
				$tpl_file = 'license-error';
				$days = License::getLicense()->isPastExpireDate();
				if ($days == 1) {
					$message = 'Your license has expired 1 day ago.';
				} else {
					$message = 'Your license has expired ' . $days . ' days ago.';
				}
				break;

			case 'cloud_off_user':
			case 'cloud_off_agent':
			case 'cloud_off_admin':
				$tpl_file = 'cloud-off';
				$title = 'Offline';
				$message = DPC_OFF_REASON;
				break;

			case 'cloud_billfail_admin':
			case 'cloud_billfail_agent':
			case 'cloud_billfail_user':
				$tpl_file = 'cloud-billfail';
				$title = 'Billing Failed';
				break;

			case 'cloud_demo_expired':
				$tpl_file = 'cloud-demo-expired';
				$now  = new \DateTime('now');
				$date = new \DateTime('@' . DPC_DEMO_EXPIRE);

				$days = $date->diff($now)->format('%a');
				if ($days == 1) {
					$days = 'today';
				} elseif ($days == 2) {
					$days = 'yesterday';
				} else {
					$days = "$days days ago";
				}

				$message = "Your demo expired $days.";
				$title = "Demo Expired";
				break;

			case 'sys_disabled':
				$title = 'Account Offline';
				$message = $subtype;
				$tpl_file = 'cloud-sys-disabled';
				break;

			default: trigger_error('getLicenseErrorPage called with bad $type: ' . $type, E_USER_ERROR); return '';
		}

		if ($title) {
			$vars['title'] = $title;
		}
		if ($message) {
			$vars['message'] = $message;
		}

		$fn_scope = function() use ($vars, $tpl_file) {
			extract($vars);

			$is_widget = false;
			if (defined('DP_REQUEST_URL')) {
				if (strpos(DP_REQUEST_URL, '/widget/overlay.html')) {
					$is_widget = 'overlay';
				}
			}

			ob_start();
			include(DP_ROOT . '/src/Application/DeskPRO/Resources/views/offline_pages/' . $tpl_file . '.html.php');
			$page_html = ob_get_clean();

			return $page_html;
		};

		$page_html = $fn_scope();

		return $page_html;
	}

	public static function getOfflineMessage()
	{
		$offline_message = null;
		if (file_exists(dp_get_data_dir() . '/helpdesk-offline-message.txt')) {
			$offline_message = file_get_contents(dp_get_data_dir() . '/helpdesk-offline-message.txt');
		} elseif (class_exists('Application\DeskPRO\App', false)) {
			try {
				$offline_message = \Application\DeskPRO\App::getSetting('core.helpdesk_disabled_message');
			} catch (\Exception $e) {}
		}

		if (!$offline_message) {
			$offline_message = 'The helpdesk is currently offline for maintenance. Please try again soon.';
		}

		return $offline_message;
	}
}