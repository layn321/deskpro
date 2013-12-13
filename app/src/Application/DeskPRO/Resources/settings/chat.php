<?php return array(

	/**
	 * round_robin: Agents are assigned chats in round robin, least number of chats
	 * everyone: Everyone sees the notification at the same time
	 */
	'core_chat.assign_mode' => 'everyone', // round_robin, everyone

	/**
	 * Number of seconds after an auto-assignment that the agent
	 * has to acknowledge the chat until it's broadcast to everyone/next agent.
	 */
	'core_chat.assign_ack_timeout' => 20,

	/**
	 * Number of seconds until the agent times out and the chat is unassigned
	 */
	'core_chat.agent_timeout' => 20,

	/**
	 * Number of seconds until the user times out
	 */
	'core_chat.user_timeout' => 15,

	/**
	 * When enabled, the user has to chose a department to start a chat
	 */
	'core_chat.require_department' => 1,

	/**
	 * Pageloads until proactive popup
	 */
	'core_chat.proactive_pages' => 0,

	/**
	 * Time until proactive popup
	 */
	'core_chat.proactive_time' => 0,

	/**
	 * The max amount of time a chat remains in un-assigned
	 * state before we kill the chat and show user link to ticket
	 */
	'core_chat.max_wait_time' => 900,

	/**
	 * Max amount of time after a user timeout before the chat is closed for real
	 */
	'core_chat.abandoned_time' => 480,

	/**
	 * Time until we stop showing a user as "online" in online users list
	 */
	'core_chat.user_online_time' => 600,
);