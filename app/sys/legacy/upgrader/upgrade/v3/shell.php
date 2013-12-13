<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id: index.php 3518 2007-03-19 23:37:30Z chroder $
// +-------------------------------------------------------------+
// | File Details:
// | -
// +-------------------------------------------------------------+

install_check();

$settings = legacy_get_settings();

/*
	UPGRADE SYSTEM

	- Handles the v3 Internal Upgrades
	- Each file can have multiple steps
	- A core set of functions are run when we are finished
*/

/**********************
* List of all the upgrades we have
**********************/

require_once(INC . 'classes/class_DpBuilds.php');

/**********************
* Disable helpdesk and remember it was disabled
**********************/

if ($settings['helpdesk_enabled']) {

	$helpdesk_was_on = true;
	legacy_update_setting('helpdesk_enabled', 0);

}

/**********************
* Get the next upgrade to do
**********************/

$dpbuilds = new DpBuilds();

// Attempt to fix legacy builds and catch invalid ones
if (!$dpbuilds->isBuild($settings['deskpro_version_internal'])) {
	$real_build = $dpbuilds->convertLegacyBuild($settings['deskpro_version_internal']);

	// Shouldn't happen unless user fiddled with their build number
	if (!$real_build) {
		die('
			There was an error determining which build you are currently running.
			Please contact support@deskpro.com with the following information:

			Build: ' . $settings['deskpro_version_internal'] . '
		');
	}

	legacy_update_setting('deskpro_version_internal', $real_build);
}

$current_build = $settings['deskpro_version_internal'];

/**********************
* Do all the upgrades we have left
**********************/

while ($current_build = $dpbuilds->getNextBuild($current_build)) {
	require_once(INSTALL . 'upgrade/v3/' . $dpbuilds->getVersion($current_build) . '.php');
}

?>
