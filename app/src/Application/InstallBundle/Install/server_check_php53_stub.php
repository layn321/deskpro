<?php

function deskpro_handle_boot_db_exception($e)
{
	if (dp_get_config('is_installed_flag')) {
		$error_info = '';
		if (!defined('DP_NO_SHOW_DB_ERR')) {
			$error_info = "<hr />" . $e->getCode() . ' ' . $e->getMessage();

			$error_info = str_replace(DP_DATABASE_HOST, '...', $error_info);
			$error_info = str_replace(DP_DATABASE_NAME, '...', $error_info);
			$error_info = str_replace(DP_DATABASE_USER, '...', $error_info);
			$error_info = str_replace(DP_DATABASE_PASSWORD, '...', $error_info);

			switch($e->getCode()) {
				case 1049:
					$error_info .= '<hr />The database could not be found. Please ensure the correct database is listed in config.php.';
					break;

				case 1044:
				case 1045:
					$error_info .= '<hr />Please ensure the correct database details are listed in config.php. If you are sure they are correct, you may not have sufficient permissions to access the database.';
					break;

				case 2002:
					$error_info .= '<hr />Could not connect to the database server. Please ensure the correct database details are listed in config.php. If you are sure they are correct, your database server may not be configured to accept connections from this server.';
					break;
			}

			$error_info .= "<hr />Refer to the <var>data/logs/error.log</var> file for more information.";
		}

		if (class_exists('DeskPRO\Kernel\KernelErrorHandler')) {
			$e_info = \DeskPRO\Kernel\KernelErrorHandler::getExceptionInfo($e);
			\DeskPRO\Kernel\KernelErrorHandler::logToFile($e_info);
		}

		echo deskpro_install_basic_error("There was a problem connecting to the database. Please try again.$error_info", 'Error');
		exit;
	}
}
