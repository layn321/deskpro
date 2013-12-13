<?php return array(

	/**
	 * How often to clean up Visitor records
	 */
	'core_misc.cleanup_visitors' => 864000, // 10 days

	/**
	 * How often to clean up login logs
	 */
	'core_misc.cleanup_login_logs' => 2592000, // 30 days

	/**
	 * How often to clean up old email gateway raw sources
	 */
	'core_misc.cleanup_gateway_sources' => 2592000, // 30 days

	/**
	 * Only clean up raw sources of closed tickets
	 */
	'core_misc.cleanup_gateway_sources_onlyclosed' => true,

	/**
	 * How often to clean up scheduled task log
	 */
	'core_misc.cleanup_task_logs' => 604800, // 7 days
);