<?php return array(

	'core_email.max_email_size' => 31457280, // 30 MB

	/**
	 * How many times an email fails to send before it results
	 * in a notification
	 */
	'core_email.failed_email_attempts_notify' => 4,


	'core_email.antiflood_newtickets' => 10,
	'core_email.antiflood_newtickets_warn' => 5,
	'core_email.antiflood_newtickets_time' => 900,

	'core_email.antiflood_newreplies' => 10,
	'core_email.antiflood_newreplies_warn' => 5,
	'core_email.antiflood_newreplies_time' => 900,

	'core_email.enable_date_limit_rejection' => true,

);