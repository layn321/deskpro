<?php return array(
	'core_tickets.lock_timeout' => 600,
	'core_tickets.hard_delete_time' => 2419000,
	'core_tickets.spam_delete_time' => 172800,

	'core_tickets.tac_auth_code_len' => 15,
	'core_tickets.ptac_auth_code_len' => 15,

	'core_tickets.enable_feedback' => 1,
	'core_tickets.feedback_agents_read' => 1,

	'core_tickets.enable_billing' => 0,
	'core_tickets.billing_on_reply' => 0,
	'core_tickets.billing_auto_timer' => 0,
	'core_tickets.billing_on_new' => 0,
	'core_tickets.billing_auto_timer_new' => 0,
	'core_tickets.billing_currency' => 'USD',

	'core_tickets.enable_agent_rte' => true,
	'core_tickets.agent_rte_button_html' => false,
	'core_tickets.agent_rte_button_bold' => true,
	'core_tickets.agent_rte_button_italic' => true,
	'core_tickets.agent_rte_button_underline' => true,
	'core_tickets.agent_rte_button_strike' => false,
	'core_tickets.agent_rte_button_color' => true,
	'core_tickets.agent_rte_button_list' => true,
	'core_tickets.agent_rte_button_image' => true,
	'core_tickets.agent_rte_button_link' => true,
	'core_tickets.agent_rte_button_table' => false,
	'core_tickets.agent_rte_button_hr' => true,
	'core_tickets.agent_rte_button_alignment' => false,

	'core_tickets.use_ref' => false,

	'core_tickets.gateway_enable_subject_match' => true,

	'core_tickets.email_history_limit' => 11, // 10 + 1 for the original message at top

	'core.allow_arbitrary_gateway_address' => 1,

	'core_tickets.use_archive' => 0,
	'core_tickets.auto_archive_time' => 2419000,

	'core_tickets.enable_like_search_mode' => 'auto',
	'core_tickets.enable_like_search_auto' => true,

	// True to force agent emails to have the marker line
	'core_tickets.gateway_agent_require_marker' => true,

	'core_tickets.reply_status' => 'awaiting_user',
	'core_tickets.reply_assign_unassigned' => 'assign',
	'core_tickets.reply_assign_assigned' => false,
	'core_tickets.reply_assignteam_unassigned' => false,
	'core_tickets.reply_assignteam_assigned' => false,
	'core_tickets.reassign_auto_change_status' => false,
	'core_tickets.resolve_auto_close_tab' => false,
	'core_tickets.new_status' => 'awaiting_user',
	'core_tickets.new_assign' => 'assign',
	'core_tickets.new_assignteam' => false,
	'core_tickets.default_send_user_notify' => true,
	'core_tickets.new_default_send_user_notify' => true,

	/**
	 * True to add agents CC's in emails as followers
	 */
	'core_tickets.add_agent_ccs' => false,

	/**
	 * Process FW agent emails as forwards
	 */
	'core_tickets.process_agent_fwd' => true,

	'core_tickets.lock_on_view' => false,
	'core_tickets.unlock_on_close' => false,
	'core_tickets.lock_lifetime' => 3600,

	'core_tickets.default_ticket_reverse_order' => true,

	'core_tickets.work_hours' => 'a:8:{s:11:"active_time";s:3:"all";s:10:"start_hour";i:9;s:12:"start_minute";i:0;s:8:"end_hour";i:17;s:10:"end_minute";i:0;s:4:"days";a:5:{i:1;b:1;i:2;b:1;i:3;b:1;i:4;b:1;i:5;b:1;}s:8:"timezone";N;s:8:"holidays";a:0:{}}',
);
