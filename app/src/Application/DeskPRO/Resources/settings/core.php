<?php return array(

	/**
	 * When there's a problem where we cant get user input any other way, this address will be displayed.
	 */
	'core.emergency_contact' => 'emergency-contact@example.com',

	/**
	 * User registration mode: open, require_reg, require_reg_agent_validation, closed
	 * See AdminBundle:UserReg
	 */
	'core.user_mode' => 'open',

	/**
	 * Enable URL rewriting (removes index.php/ from URLs)
	 */
	'core.rewrite_urls' => false,

	/**
	 * Is multi-language features enabeld?
	 */
	'core.enable_languages' => true,

	/**#@+
	 * Date formats
	 */
	'core.date_fulltime' => 'D, jS M Y g:ia',
	'core.date_full' => 'D, jS M Y',
	'core.date_day'  => 'M j Y',
	'core.date_day_short'  => 'M j',
	'core.date_time' => 'g:i a',
	/**#@-*/

	/**
	 * Are ticket categories enabled?
	 */
	'core.use_ticket_category' => false,

	/**
	 * Are ticket priorities enabled?
	 */
	'core.use_ticket_priority' => false,

	/**
	 * Are ticket workflows enabled?
	 */
	'core.use_ticket_workflow' => false,

	/**
	 * Are agent teams enabled?
	 */
	'core.use_agent_team' => false,

	/**
	 * Are products enabled?
	 */
	'core.use_product' => false,

	/**
	 * Are custom fields on products enabled?
	 */
	'core.use_product_fields' => false,

	/**
	 * Is the helpdesk disabled? A disabled helpdesk doesn't fully boot and shows a maintenance message.
	 */
	'core.helpdesk_disabled' => false,

	/**
	 * The default message to show when helpdesk is disabled
	 * Note that this is actually written to a file when updated, this record is kept just
	 * as the default.
	 */
	'core.helpdesk_disabled_message' => 'Our helpdesk is temporarily offline for maintenance. We will be back up in about 10 minutes.',

	/**
	 * The URL to the DeskPRO installation.
	 * Should include trailing slash. Should NOT include index.php portion.
	 */
	'core.deskpro_url' => '',

	/**
	 * The name of the DeskPRO helpdesk
	 */
	'core.deskpro_name' => 'DeskPRO',

	/**
	 * Redirect to correct URL (domain and https)
	 */
	'core.redirect_correct_url' => false,

	/**
	 * The optional tagline or sub-title of the helpdesk
	 */
	'core.deskpro_tagline' => '',

	/**
	 * The blobid of the uploaded logo image
	 */
	'core.deskpro_logo_blob' => 0,

	/**
	 * The optional URL to a main website
	 */
	'core.site_url' => '',

	/**
	 * The optional title for the site URL
	 */
	'core.site_name' => '',

	/**
	 * The full URL to assets. These are hot-linked from remote sources like widgets.
	 * If not specified, the /web/ directory of deskpro_url above is used.
	 */
	'core.deskpro_assets_full_url' => '',

	/**
	 * The secret string used for this installation used when generating various hashes
	 */
	'core.app_secret' => 'APP_SECRET',

	/**
	 * The default "from" address to send all email from
	 */
	'core.default_from_email' => '',

	/**
	 * Optionally BCC all emails to this address
	 */
	'core.bcc_all_emails' => '',

	/**
	 * How long, in days, to save successfully sent emails to
	 */
	'core.store_sent_mail_days' => 0,

	/**
	 * When the installation took place
	 */
	'core.install_time' => 0,

	/**
	 * The site ID. Only used when multiple helpdesks need to talk to eachother.
	 */
	'core.site_id' => '1',

	/**
	 * When enabled, error reports and heartbeats dont send server stats,
	 * and heartbeats dont send db stats
	 */
	'core.enable_reduced_lic_reports' => false,

	/**
	 * The cookie path
	 */
	'core.cookie_path' => '/',

	/**
	 * The cookie domains
	 */
	'core.cookie_domain' => '',

	/**
	 * The language to use by default when a user has no selection
	 */
	'core.default_language_id' => 1,

	/**
	 * The style to use
	 */
	'core.default_style_id' => 1,

	/**
	 * The timezone to use by default when a user has no selection
	 */
	'core.default_timezone' => 'UTC',

	/**
	 * Use gravatar for default avatars?
	 */
	'core.use_gravatar' => 1,
	'core.use_default_gravatar' => 1,

	/**
	 * How long, in seconds, are sessions valid for?
	 */
	'core.sessions_lifetime' => 3600,

	/**
	 * How long, in seconds, are email sources kept for before being cleaned up
	 */
	'core.email_source_storetime' => 5184000, // 60 days
	'core.email_source_storetime_error' => 15552000, // 180 days
	'core.email_source_storetime_rejection' => 1296000, // 15 days

	/**
	 * When to use the mail queue: never, hint, always
	 */
	'core.use_mail_queue'  => 'hint',

	//'core.disqus_shortname' => '',
	//'core.facebook_comments_num_posts' => 10,
	//'core.facebook_admins' => '',
	//'core.facebook_like' => false,

	/**
	 * Which comment adapter to use: false=deskpro, disqus or facebook
	 */
	'core.comments_adapter' => false,

	/**
	 * Recaptcha public key
	 */
	'core.recaptcha_public_key'  => '6LcWL8YSAAAAAJu1CrtS9RdOJyKd_NbArNgUFWV9',

	/**
	 * Recaptcha private key
	 */
	'core.recaptcha_private_key' => '6LcWL8YSAAAAAJVZ5AEMb5Vq7wkZoGHfKfAqvB2U',

	/**
	 * Default storage method for blobs is the db. Other values: fs
	 */
	'core.filestorage_method' => 'db',

	/**
	 * Require a user to log in if they enter an email address that is owned by an existing accounts
	 */
	'core.existing_account_login' => false,

	/**
	 * The max attachment an agent can upload
	 */
	'core.attach_agent_maxsize' => '5242880',

	/**
	 * The only file extensions that an agent can upload (whitelist)
	 */
	'core.attach_agent_must_exts' => null,

	/**
	 * File extensions that an agent is forbidden from uploading (blacklist)
	 */
	'core.attach_agent_not_exts' => null,

	/**
	 * The max attachment a user can upload
	 */
	'core.attach_user_maxsize' => '5242880',

	/**
	 * The only file extensions that users can upload (whitelist)
	 */
	'core.attach_user_must_exts' => null,

	/**
	 * File extensions that a user is forbidden from uploading (blacklist)
	 */
	'core.attach_user_not_exts' => null,

	/**
	 * True to have the DeskPRO local user source enabled
	 */
	'core.deskpro_source_enabled' => true,

	/**
	 * True to have links from chat intercepted and sent through the security page
	 */
	'core.agent_intercept_external_link' => false,

	/**
	 * Show the share widget (twitter/facebook/linkedin/gplus)
	 */
	'core.show_share_widget' => true,
	'core.show_share_facebook' => true,
	'core.show_share_twitter' => true,
	'core.show_share_linkedin' => true,
	'core.show_share_gplus' => true,

	/**
	 * Enable the KB?
	 */
	'core.apps_kb' => 1,

	/**
	 * Enable feedback?
	 */
	'core.apps_feedback' => 1,

	/**
	 * Enable chat?
	 */
	'core.apps_chat' => 1,

	/**
	 * Enable agent tasks?
	 */
	'core.apps_tasks' => 1,

	/**
	 * Enable news
	 */
	'core.apps_news' => 1,

	/**
	 * Enable downloads
	 */
	'core.apps_downloads' => 1,

	/**
	 * Max email size to read from gateways
	 */
	'core.gateway_max_email' => 0,

	/**
	 * True to disable floodchecking in gateway
	 */
	'core.disable_gateway_floodcheck' => false,

	/**
	 * Show suggestions newticket
	 */
	'core.show_ticket_suggestions' => true,

	/**
	 * Max size of attachments to send in email notifications
	 */
	'core.sendemail_attach_maxsize' => 10485760,

	/**
	 * Max size of an embedded image in an email
	 */
	'core.sendemail_embed_maxsize' => 256000,

	/**
	 * Automatically install new languages on upgrade
	 */
	'core.lang_auto_install' => false,

	/**
	 * How long, in seconds, are drafts valid for?
	 */
	'core.drafts_lifetime' => 604800, // 1 week

	/**
	 * A Google Analytics property ID (UA-XXXXX-Y). If added, GA will be enabled on user pages.
	 */
	'core.ga_property_id' => '',

	/**
	 * Number of allowed requests to the API per hour. 0 to disable.
	 */
	'core.api_rate_limit' => defined('DPC_IS_CLOUD') ? 1000 : 0,

	'core.twitter_agent_consumer_key' => '',
	'core.twitter_agent_consumer_secret' => '',
	'core.twitter_user_consumer_key' => '',
	'core.twitter_user_consumer_secret' => '',
	'core.twitter_auto_remove_time' => 1209600,

	/**
	 * Time in H:i format for when to send task notifs.
	 * The time is considered to be of the default timezone.
	 */
	'core.task_reminder_time' => '09:00',

	/**
	 * How long to keep visitor tracks around for
	 */
	'core.visitor_cleanup_time' => 604800,

	/**
	 * How long to keep visitor tracks around for
	 * when they are suspected dupes/bots/unconnected
	 */
	'core.visitor_cleanup_bogus_time' => 1800,

	'core.agent_translate_debug' => false,
	'core.agent_enable_kb_shortcuts' => true,
);
