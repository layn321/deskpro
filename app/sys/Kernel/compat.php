<?php

// Bug in a version of PHP 5.2.x has some missing curl constants
if (function_exists('curl_init')) {
	foreach (array(
		'CURLOPT_CAINFO',
		'CURLOPT_SSL_VERIFYPEER',
		'CURLOPT_SSL_VERIFYHOST',
	) as $name) {
		if (!defined($name)) {
			define($name, '');
		}
	}
}