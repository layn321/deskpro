<?php return array(

	'agent.max_login_attempts' => 5,
	'agent.login_lockout_time' => 3600,
	'agent.login_logo_blob_id' => null,

	'agent.notify_self_login' => false,
	'agent.notify_self_failed_login' => true,

	'agent.notify_login_emaillist' => '',
	'agent_notify_list_login' => false,
	'agent_notify_list_failed_login' => false,
	'agent_notify_list_adminlogin' => false,
	'agent_notify_list_failed_adminlogin' => true,

	'agent.ui_snippets_use_client_db' => false,

	/**
	 * How long in seconds before old alerts are cleaned up
	 */
	'agent.alerts_cleanup_time' => 129600,
);