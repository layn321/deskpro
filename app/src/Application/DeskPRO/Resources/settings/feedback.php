<?php return array(

	/**
	 * How many votes each person gets to spend
	 */
	'core_feedback.votes_per_person' => 10,

	/**
	 * The max votes a user can spend on one feedback
	 */
	'core_feedback.max_votes_feedback' => 3,

	/**
	 * Return votes to a user when an feedback is accepted?
	 * Otherwise, return when the feedback is marked completed/deleted.
	 */
	'core_feedback.return_on_accept' => false,

	/**
	 * Require a user to login before an feedback can be submitted
	 */
	'core_feedback.require_user' => false,

	/**
	 * Require an agent to manually validate eacy submission
	 */
	'core_feedback.require_validation' => false,

	/**
	 * How many votes until an feedback is 'popular'?
	 */
	'core_feedback.popular_votes' => 10,

	/**
	 * Show feedback publicly even when they're validating?
	 */
	'core_feedback.show_validating' => 1,
);
