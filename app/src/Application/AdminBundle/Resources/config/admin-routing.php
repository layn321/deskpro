<?php if (!defined('DP_ROOT')) exit('No access');

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('admin_test', new Route(
	'/test',
	array('_controller' => 'AdminBundle:Test:index'),
	array(),
	array()
));

$collection->add('admin_submit_deskpro_feedback', new Route(
	'/submit-deskpro-feedback.json',
	array('_controller' => 'AdminBundle:Main:submitDeskproFeedback'),
	array(),
	array()
));

$collection->add('admin_onboard_complete', new Route(
	'/onboard-mark-complete/{type}/{id}.json',
	array('_controller' => 'AdminBundle:Main:onboardMarkComplete'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_welcome', new Route(
	'/welcome',
	array('_controller' => 'AdminBundle:Settings:quickSetup'),
	array(),
	array()
));

$collection->add('admin_apps', new Route(
	'/apps',
	array('_controller' => 'AdminBundle:Settings:apps'),
	array(),
	array()
));

$collection->add('admin_apps_toggle', new Route(
	'/apps/toggle',
	array('_controller' => 'AdminBundle:Settings:appToggle'),
	array(),
	array()
));

$collection->add('admin_change_picture', new Route(
	'/misc/change-picture',
	array('_controller' => 'AdminBundle:Main:changePicture'),
	array(),
	array()
));

$collection->add('admin_change_picture_save', new Route(
	'/misc/change-picture/save',
	array('_controller' => 'AdminBundle:Main:changePictureSave'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_networkcheck', new Route(
	'/misc/network',
	array('_controller' => 'AdminBundle:Settings:quickSetup'),
	array(),
	array()
));

$collection->add('admin_check_task_queue', new Route(
	'/misc/check-task/{task_queue_id}',
	array('_controller' => 'AdminBundle:Main:checkTaskQueue'),
	array(),
	array()
));

$collection->add('admin_check_task_queue_group', new Route(
	'/misc/check-task/group/{task_group}',
	array('_controller' => 'AdminBundle:Main:checkTaskQueueGroup'),
	array(),
	array()
));

$collection->add('admin_quick_person_search', new Route(
	'/misc/quick-person-search',
	array('_controller' => 'AdminBundle:Main:quickPersonSearch'),
	array(),
	array()
));

$collection->add('admin_quick_organization_search', new Route(
	'/misc/quick-organization-search',
	array('_controller' => 'AdminBundle:Main:quickOrganizationSearch'),
	array(),
	array()
));

$collection->add('admin_skip_setup_todo', new Route(
	'/misc/skip-setup-todo',
	array('_controller' => 'AdminBundle:Main:skipSetupStep'),
	array(),
	array()
));

################################################################################
# Dashboard
################################################################################

$collection->add('admin', new Route(
	'/',
	array('_controller' => 'AdminBundle:Main:index'),
	array(),
	array()
));

$collection->add('admin_dash_versioninfo', new Route(
	'/dashboard/load-version-info.html',
	array('_controller' => 'AdminBundle:Main:dashVersionInfo'),
	array(),
	array()
));

$collection->add('admin_dash_versionnotice', new Route(
	'/dashboard/load-version-notice.html',
	array('_controller' => 'AdminBundle:Main:dashVersionNotice'),
	array(),
	array()
));

################################################################################
# Upgrade
################################################################################

$collection->add('admin_upgrade', new Route(
	'/upgrade',
	array('_controller' => 'AdminBundle:Upgrade:start'),
	array(),
	array()
));

$collection->add('admin_upgrade_abort', new Route(
	'/upgrade/abort',
	array('_controller' => 'AdminBundle:Upgrade:stop'),
	array(),
	array()
));

$collection->add('admin_upgrade_watch', new Route(
	'/upgrade/watch',
	array('_controller' => 'AdminBundle:Upgrade:watch'),
	array(),
	array()
));

$collection->add('admin_upgrade_watch_checkstarted', new Route(
	'/upgrade/watch/check-started.json',
	array('_controller' => 'AdminBundle:Upgrade:checkStarted'),
	array(),
	array()
));

################################################################################
# License
################################################################################

$collection->add('admin_license_reqdemo', new Route(
	'/license/generate-demo',
	array('_controller' => 'AdminBundle:License:requestDemo'),
	array(),
	array()
));

$collection->add('admin_license_input_save', new Route(
	'/license/input/save',
	array('_controller' => 'AdminBundle:License:saveNewLicense'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_license_keyfile', new Route(
	'/license/download/deskpro-license-sign.key',
	array('_controller' => 'AdminBundle:License:keyFile'),
	array(),
	array()
));


################################################################################
# Tickets
################################################################################

$collection->add('admin_tickets_fields', new Route(
	'/tickets/fields',
	array('_controller' => 'AdminBundle:TicketProperties:list'),
	array(),
	array()
));

$collection->add('admin_tickets_editor_reset', new Route(
	'/tickets/editor/reset-all/{security_token}',
	array('_controller' => 'AdminBundle:TicketProperties:resetEditor'),
	array(),
	array()
));

$collection->add('admin_tickets_editor', new Route(
	'/tickets/editor/{department_id}/{section}',
	array('_controller' => 'AdminBundle:TicketProperties:editor', 'department_id' => 0, 'section' => 'create'),
	array('department_id' => '\d+'),
	array()
));

$collection->add('admin_tickets_editor_toggleper', new Route(
	'/tickets/editor/toggle-per-department',
	array('_controller' => 'AdminBundle:TicketProperties:togglePerDepartment'),
	array(),
	array()
));

$collection->add('admin_tickets_editor_dep_init', new Route(
	'/tickets/editor/{department_id}/{section}/init',
	array('_controller' => 'AdminBundle:TicketProperties:initEditor', 'section' => 'create'),
	array(),
	array()
));

$collection->add('admin_tickets_editor_dep_revert', new Route(
	'/tickets/editor/{department_id}/{section}/revert',
	array('_controller' => 'AdminBundle:TicketProperties:revertEditor', 'section' => 'create'),
	array(),
	array()
));

$collection->add('admin_tickets_editor_dep', new Route(
	'/tickets/editor/{department_id}/{section}',
	array('_controller' => 'AdminBundle:TicketProperties:editor', 'section' => 'create'),
	array(),
	array()
));

$collection->add('admin_tickets_editor_dep_save', new Route(
	'/tickets/editor/{department_id}/{section}/save',
	array('_controller' => 'AdminBundle:TicketProperties:saveEditor'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_tickets_filters', new Route(
	'/tickets/filters',
	array('_controller' => 'AdminBundle:TicketFilters:index'),
	array(),
	array()
));

$collection->add('admin_tickets_filters_edit', new Route(
	'/tickets/filters/{filter_id}',
	array('_controller' => 'AdminBundle:TicketFilters:edit'),
	array('filter_id' => '\\d+'),
	array()
));

$collection->add('admin_tickets_filters_delete', new Route(
	'/tickets/filters/{filter_id}/delete/{security_token}',
	array('_controller' => 'AdminBundle:TicketFilters:delete'),
	array('filter_id' => '\\d+'),
	array()
));

$collection->add('admin_tickets_filters_new', new Route(
	'/tickets/filters/new',
	array('_controller' => 'AdminBundle:TicketFilters:edit', 'filter_id' => '0'),
	array(),
	array()
));

$collection->add('admin_ticketcats', new Route(
	'/tickets/categories',
	array('_controller' => 'AdminBundle:TicketCategories:list'),
	array(),
	array()
));

$collection->add('admin_ticketcats_setdefault', new Route(
	'/tickets/categories/set-default',
	array('_controller' => 'AdminBundle:TicketCategories:setDefault'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_ticketcats_toggle', new Route(
	'/tickets/categories/toggle-feature/{enable}',
	array('_controller' => 'AdminBundle:TicketCategories:toggleFeature'),
	array(),
	array()
));

$collection->add('admin_ticketcats_savenew', new Route(
	'/tickets/categories/save-new',
	array('_controller' => 'AdminBundle:TicketCategories:saveNew'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_ticketcats_savetitle', new Route(
	'/tickets/categories/save-title',
	array('_controller' => 'AdminBundle:TicketCategories:saveTitle'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_ticketcats_updateorders', new Route(
	'/tickets/categories/update-orders',
	array('_controller' => 'AdminBundle:TicketCategories:updateOrders'),
	array(),
	array()
));

$collection->add('admin_ticketcats_del', new Route(
	'/tickets/categories/{category_id}/delete',
	array('_controller' => 'AdminBundle:TicketCategories:delete'),
	array('category_id' => '\\d+'),
	array()
));

$collection->add('admin_ticketcats_dodel', new Route(
	'/tickets/categories/{category_id}/delete/{security_token}',
	array('_controller' => 'AdminBundle:TicketCategories:doDelete'),
	array('category_id' => '\\d+', 'security_token' => '[a-zA-Z0-9\\-]+'),
	array()
));

$collection->add('admin_ticketpris', new Route(
	'/tickets/priorities',
	array('_controller' => 'AdminBundle:TicketPriorities:list'),
	array(),
	array()
));

$collection->add('admin_ticketpris_toggle', new Route(
	'/tickets/priorities/toggle-feature/{enable}',
	array('_controller' => 'AdminBundle:TicketPriorities:toggleFeature'),
	array(),
	array()
));

$collection->add('admin_ticketpris_savenew', new Route(
	'/tickets/priorities/save-new',
	array('_controller' => 'AdminBundle:TicketPriorities:saveNew'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_ticketpris_setdefault', new Route(
	'/tickets/priorities/set-default',
	array('_controller' => 'AdminBundle:TicketPriorities:setDefault'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_ticketpris_savetitle', new Route(
	'/tickets/priorities/save-title',
	array('_controller' => 'AdminBundle:TicketPriorities:saveTitle'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_ticketpris_del', new Route(
	'/tickets/priorities/{priority_id}/delete',
	array('_controller' => 'AdminBundle:TicketPriorities:delete'),
	array('priority_id' => '\\d+'),
	array()
));

$collection->add('admin_ticketpris_dodel', new Route(
	'/tickets/priorities/{priority_id}/delete/{security_token}',
	array('_controller' => 'AdminBundle:TicketPriorities:doDelete'),
	array('priority_id' => '\\d+', 'security_token' => '[a-zA-Z0-9\\-]+'),
	array()
));

$collection->add('admin_ticketworks', new Route(
	'/tickets/workflows',
	array('_controller' => 'AdminBundle:TicketWorkflows:list'),
	array(),
	array()
));

$collection->add('admin_ticketworks_setdefault', new Route(
	'/tickets/workflows/set-default',
	array('_controller' => 'AdminBundle:TicketWorkflows:setDefault'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_ticketworks_toggle', new Route(
	'/tickets/workflows/toggle-feature/{enable}',
	array('_controller' => 'AdminBundle:TicketWorkflows:toggleFeature'),
	array(),
	array()
));

$collection->add('admin_ticketworks_savenew', new Route(
	'/tickets/workflows/save-new',
	array('_controller' => 'AdminBundle:TicketWorkflows:saveNew'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_ticketworks_savetitle', new Route(
	'/tickets/workflows/save-title',
	array('_controller' => 'AdminBundle:TicketWorkflows:saveTitle'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_ticketworks_del', new Route(
	'/tickets/workflows/{workflow_id}/delete',
	array('_controller' => 'AdminBundle:TicketWorkflows:delete'),
	array('workflow_id' => '\\d+'),
	array()
));

$collection->add('admin_ticketworks_dodel', new Route(
	'/tickets/workflows/{workflow_id}/delete/{security_token}',
	array('_controller' => 'AdminBundle:TicketWorkflows:doDelete'),
	array('workflow_id' => '\\d+', 'security_token' => '[a-zA-Z0-9\\-]+'),
	array()
));

$collection->add('admin_ticketworks_updateorders', new Route(
	'/tickets/workflows/update-orders',
	array('_controller' => 'AdminBundle:TicketWorkflows:updateOrders'),
	array(),
	array()
));

$collection->add('admin_tickets_slas', new Route(
	'/tickets/slas',
	array('_controller' => 'AdminBundle:TicketSlas:list'),
	array(),
	array()
));

$collection->add('admin_tickets_slas_new', new Route(
	'/tickets/slas/new',
	array('_controller' => 'AdminBundle:TicketSlas:edit', 'sla_id' => 0),
	array(),
	array()
));

$collection->add('admin_tickets_sla_edit', new Route(
	'/tickets/slas/{sla_id}/edit',
	array('_controller' => 'AdminBundle:TicketSlas:edit'),
	array('sla_id' => '\\d+'),
	array()
));

$collection->add('admin_tickets_sla_delete', new Route(
	'/tickets/slas/{sla_id}/delete',
	array('_controller' => 'AdminBundle:TicketSlas:delete'),
	array('sla_id' => '\\d+'),
	array()
));

################################################################################
# Portal
################################################################################

$collection->add('admin_accept_upload', new Route(
	'/misc/accept-upload',
	array('_controller' => 'AdminBundle:Main:acceptTempUpload'),
	array(),
	array()
));

$collection->add('admin_portal', new Route(
	'/portal',
	array('_controller' => 'AdminBundle:Portal:index'),
	array(),
	array()
));

$collection->add('admin_portal_updateblockorder', new Route(
	'/portal/update-block-orders.json',
	array('_controller' => 'AdminBundle:Portal:updateBlockOrders'),
	array(),
	array()
));

$collection->add('admin_portal_blocktoggle', new Route(
	'/portal/blocks/{pid}/toggle.json',
	array('_controller' => 'AdminBundle:Portal:blockToggle'),
	array(),
	array()
));

$collection->add('admin_portal_custom_block_delete', new Route(
	'/portal/blocks/{pid}/delete-template-block.json',
	array('_controller' => 'AdminBundle:Portal:deleteTemplateBlock'),
	array(),
	array()
));

$collection->add('admin_portal_custom_sideblock_simple_get', new Route(
	'/portal/sideblock-simple/{pid}.json',
	array('_controller' => 'AdminBundle:Portal:getCustomBlockSimple'),
	array(),
	array()
));

$collection->add('admin_portal_custom_sideblock_simple_save', new Route(
	'/portal/sideblock-simple/{pid}/save.json',
	array('_controller' => 'AdminBundle:Portal:saveCustomBlockSimple', 'pid' => '0'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_portal_custom_sideblock_simple_delete', new Route(
	'/portal/sideblock-simple/{pid}/delete.json',
	array('_controller' => 'AdminBundle:Portal:deleteCustomBlockSimple'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_portal_toggle', new Route(
	'/portal/toggle-portal',
	array('_controller' => 'AdminBundle:Portal:togglePortal'),
	array(),
	array()
));

$collection->add('admin_portal_get_editor', new Route(
	'/portal/get-editor/{type}',
	array('_controller' => 'AdminBundle:Portal:getEditor'),
	array(),
	array()
));

$collection->add('admin_portal_save_editor', new Route(
	'/portal/save-editor/{type}',
	array('_controller' => 'AdminBundle:Portal:saveEditor'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_portal_twitter_oauth', new Route(
	'/portal/twitter-oauth',
	array('_controller' => 'AdminBundle:Portal:twitterOauth'),
	array(),
	array()
));

$collection->add('admin_website_embeds', new Route(
	'/website-embeds',
	array('_controller' => 'AdminBundle:Portal:widgets'),
	array(),
	array()
));

$collection->add('admin_portal_uploadfavicon', new Route(
	'/portal/upload-favicon',
	array('_controller' => 'AdminBundle:Portal:uploadFavicon'),
	array(),
	array()
));

$collection->add('admin_login', new Route(
	'/login',
	array('_controller' => 'AdminBundle:Login:index'),
	array(),
	array()
));

$collection->add('admin_login_authenticate_local', new Route(
	'/login/authenticate-password',
	array('_controller' => 'AdminBundle:Login:authenticateLocal', 'usersource_id' => 0),
	array(),
	array()
));

$collection->add('admin_login_logoupload', new Route(
	'/login/accept-logo-upload',
	array('_controller' => 'AdminBundle:Login:acceptLogoUpload'),
	array(),
	array()
));


################################################################################
# Settings
################################################################################

$collection->add('admin_settings', new Route(
	'/settings',
	array('_controller' => 'AdminBundle:Settings:settings'),
	array(),
	array()
));

$collection->add('admin_settings_saveform', new Route(
	'/settings/save-settings/{type}/{auth}',
	array('_controller' => 'AdminBundle:Settings:settingsSaveForm'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_settings_silent_settings', new Route(
	'/settings/welcome/set-settings-silent.json',
	array('_controller' => 'AdminBundle:Settings:setSilentSettings'),
	array(),
	array()
));

$collection->add('admin_settings_cron_check', new Route(
	'/settings/cron/check.json',
	array('_controller' => 'AdminBundle:Settings:checkCron'),
	array(),
	array()
));

$collection->add('admin_settings_cron', new Route(
	'/settings/cron',
	array('_controller' => 'AdminBundle:Settings:cron'),
	array(),
	array()
));

$collection->add('admin_settings_set', new Route(
	'/settings/save-setting/{setting_name}/{security_token}',
	array('_controller' => 'AdminBundle:Settings:saveSingleSetting'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_settings_adv', new Route(
	'/settings/advanced',
	array('_controller' => 'AdminBundle:Settings:advanced'),
	array(),
	array()
));

$collection->add('admin_settings_adv_set', new Route(
	'/settings/advanced-set/{name}',
	array('_controller' => 'AdminBundle:Settings:advancedSet'),
	array(),
	array()
));

$collection->add('admin_labels', new Route(
	'/settings/labels/{label_type}',
	array('_controller' => 'AdminBundle:Settings:labels'),
	array('label_type' => '[a-z]+'),
	array()
));

$collection->add('admin_labels_rename', new Route(
	'/settings/labels/{label_type}/rename.json',
	array('_controller' => 'AdminBundle:Settings:renameLabel'),
	array('label_type' => '[a-z]+'),
	array()
));

$collection->add('admin_labels_new', new Route(
	'/settings/labels/new.json',
	array('_controller' => 'AdminBundle:Settings:labelsAjaxNew'),
	array('label_type' => '[a-z]+'),
	array()
));

$collection->add('admin_labels_del', new Route(
	'/settings/labels/{label_type}/delete.json',
	array('_controller' => 'AdminBundle:Settings:labelsAjaxDelete'),
	array('label_type' => '[a-z]+'),
	array()
));

################################################################################
# User Registration
################################################################################

$collection->add('admin_userreg_options', new Route(
	'/settings/user-registration',
	array('_controller' => 'AdminBundle:UserReg:options'),
	array(),
	array()
));

$collection->add('admin_userreg_options_save', new Route(
	'/settings/user-registration/save.json',
	array('_controller' => 'AdminBundle:UserReg:saveOptions'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_userreg_facebook_toggle', new Route(
	'/settings/user-registration/facebook/toggle',
	array('_controller' => 'AdminBundle:UserReg:facebookToggle'),
	array(),
	array()
));

$collection->add('admin_userreg_facebook_edit', new Route(
	'/settings/user-registration/facebook',
	array('_controller' => 'AdminBundle:UserReg:facebookEdit'),
	array(),
	array()
));

$collection->add('admin_userreg_twitter_toggle', new Route(
	'/settings/user-registration/twitter/toggle',
	array('_controller' => 'AdminBundle:UserReg:twitterToggle'),
	array(),
	array()
));

$collection->add('admin_userreg_twitter_edit', new Route(
	'/settings/user-registration/twitter',
	array('_controller' => 'AdminBundle:UserReg:twitterEdit'),
	array(),
	array()
));

$collection->add('admin_userreg_google_toggle', new Route(
	'/settings/user-registration/google/toggle',
	array('_controller' => 'AdminBundle:UserReg:googleToggle'),
	array(),
	array()
));

$collection->add('admin_userreg_deskpro_source_toggle', new Route(
	'/settings/user-registration/deskpro-source/toggle',
	array('_controller' => 'AdminBundle:UserReg:deskproSourceToggle'),
	array(),
	array()
));

$collection->add('admin_userreg_usersource_choose', new Route(
	'/settings/usersources/new/choose-type',
	array('_controller' => 'AdminBundle:UserReg:usersourceNewChoose'),
	array(),
	array()
));

$collection->add('admin_userreg_usersource_edit', new Route(
	'/settings/usersources/edit/{id}',
	array('_controller' => 'AdminBundle:UserReg:usersourceEdit', 'id' => '0'),
	array(),
	array()
));

$collection->add('admin_userreg_usersource_test', new Route(
	'/settings/usersources/test/{id}',
	array('_controller' => 'AdminBundle:UserReg:usersourceTest'),
	array(),
	array()
));

$collection->add('admin_userreg_usersource_toggle', new Route(
	'/settings/usersources/toggle/{id}',
	array('_controller' => 'AdminBundle:UserReg:usersourceToggle'),
	array(),
	array()
));

$collection->add('admin_userreg_usersource_delete', new Route(
	'/settings/usersources/delete/{id}/{security_token}',
	array('_controller' => 'AdminBundle:UserReg:usersourceDelete'),
	array(),
	array()
));

################################################################################
# User : Banning
################################################################################

$collection->add('admin_banning_emails', new Route(
	'/banning/emails',
	array('_controller' => 'AdminBundle:Banning:listEmails'),
	array(),
	array()
));

$collection->add('admin_banning_ips', new Route(
	'/banning/ips',
	array('_controller' => 'AdminBundle:Banning:listIps'),
	array(),
	array()
));

$collection->add('admin_banning_newip', new Route(
	'/banning/ips/new',
	array('_controller' => 'AdminBundle:Banning:newIpBan'),
	array(),
	array()
));

$collection->add('admin_banning_newemail', new Route(
	'/banning/emails/new',
	array('_controller' => 'AdminBundle:Banning:newEmailBan'),
	array(),
	array()
));

$collection->add('admin_banning_delip', new Route(
	'/banning/ips/remove',
	array('_controller' => 'AdminBundle:Banning:removeIpBan'),
	array(),
	array()
));

$collection->add('admin_banning_delemail', new Route(
	'/banning/emails/remove',
	array('_controller' => 'AdminBundle:Banning:removeEmailBan'),
	array(),
	array()
));

################################################################################
# Agents
################################################################################

$collection->add('admin_agents', new Route(
	'/agents',
	array('_controller' => 'AdminBundle:Agents:agents'),
	array(),
	array()
));

$collection->add('admin_mass_add', new Route(
	'/agents/mass-add-agents.json',
	array('_controller' => 'AdminBundle:Agents:massAddAgents'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_agents_killsession', new Route(
	'/agents/kill-session/{agent_id}',
	array('_controller' => 'AdminBundle:Agents:killAgentSession'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_agents_deleted', new Route(
	'/agents/deleted',
	array('_controller' => 'AdminBundle:Agents:deletedAgents'),
	array(),
	array()
));

$collection->add('admin_agents_new', new Route(
	'/agents/new',
	array('_controller' => 'AdminBundle:Agents:editAgent', 'person_id' => '0'),
	array(),
	array()
));

$collection->add('admin_agents_newpre', new Route(
	'/agents/new-pre',
	array('_controller' => 'AdminBundle:Agents:newAgentPre'),
	array(),
	array()
));

$collection->add('admin_agents_remove', new Route(
	'/agents/{agent_id}/remove',
	array('_controller' => 'AdminBundle:Agents:removeAgent'),
	array(),
	array('agent_id' => '\\d+')
));

$collection->add('admin_agents_loginas', new Route(
	'/agents/{agent_id}/login-as',
	array('_controller' => 'AdminBundle:Agents:adminLoginAs'),
	array(),
	array('agent_id' => '\\d+')
));

$collection->add('admin_agents_login_logs', new Route(
	'/agents/login-log/{agent_id}',
	array('_controller' => 'AdminBundle:Agents:loginLogs', 'agent_id' => '0'),
	array(),
	array('agent_id' => '\\d+')
));

$collection->add('admin_agents_new_fromusersource', new Route(
	'/agents/new-from-usersource/{usersource_id}',
	array('_controller' => 'AdminBundle:Agents:newFromUsersource', 'usersource_id' => '0'),
	array(),
	array()
));

$collection->add('admin_agents_new_fromusersource_make', new Route(
	'/agents/new-from-usersource/{usersource_id}/make',
	array('_controller' => 'AdminBundle:Agents:newFromUsersourceMake', 'usersource_id' => '0', '_method' => 'POST'),
	array(),
	array()
));

$collection->add('admin_agents_new_fromusersource_search', new Route(
	'/agents/new-from-usersource/{usersource_id}/search',
	array('_controller' => 'AdminBundle:Agents:newFromUsersourceSearch', 'usersource_id' => '0'),
	array(),
	array()
));

$collection->add('admin_agents_edit', new Route(
	'/agents/{person_id}/edit',
	array('_controller' => 'AdminBundle:Agents:editAgent'),
	array('person_id' => '\\d+'),
	array()
));

$collection->add('admin_agents_edit_prefs', new Route(
	'/agents/{person_id}/edit-prefs',
	array('_controller' => 'AdminBundle:Agents:agentPrefs'),
	array('person_id' => '\\d+'),
	array()
));

$collection->add('admin_agents_edit_formvalidate', new Route(
	'/agents/{person_id}/edit/validate-form.json',
	array('_controller' => 'AdminBundle:Agents:quickEditFormValidate'),
	array('person_id' => '\\d+'),
	array()
));

$collection->add('admin_agents_setvacation', new Route(
	'/agents/{person_id}/set-vacation-mode/{set_to}',
	array('_controller' => 'AdminBundle:Agents:setVacationMode', 'set_to' => '0'),
	array('person_id' => '\\d+', 'set_to' => '(1|0)'),
	array()
));

$collection->add('admin_agents_setdeleted', new Route(
	'/agents/{person_id}/set-deleted/{set_to}',
	array('_controller' => 'AdminBundle:Agents:setDeleted', 'set_to' => '0'),
	array('person_id' => '\\d+', 'set_to' => '(1|0)'),
	array()
));

$collection->add('admin_agents_convertuser', new Route(
	'/agents/{agent_id}/convert-user',
	array('_controller' => 'AdminBundle:Agents:convertToUser'),
	array('agent_id' => '\\d+'),
	array()
));

$collection->add('admin_agents_getperms', new Route(
	'/agents/{person_id}/get-perms.json',
	array('_controller' => 'AdminBundle:Agents:getAgentPermissions'),
	array('person_id' => '\\d+'),
	array()
));

$collection->add('admin_agents_edit_save', new Route(
	'/agents/{person_id}/edit/save',
	array('_controller' => 'AdminBundle:Agents:editAgentSave'),
	array('person_id' => '\\d+', '_method' => 'POST'),
	array()
));

$collection->add('admin_agents_teams_edit', new Route(
	'/agents/teams/{team_id}/edit',
	array('_controller' => 'AdminBundle:Agents:editTeam'),
	array('team_id' => '\\d+'),
	array()
));

$collection->add('admin_agents_teams_del', new Route(
	'/agents/teams/{team_id}/delete/{security_token}',
	array('_controller' => 'AdminBundle:Agents:deleteTeam'),
	array('team_id' => '\\d+'),
	array()
));

$collection->add('admin_agents_teams_new', new Route(
	'/agents/teams/new',
	array('_controller' => 'AdminBundle:Agents:editTeam', 'team_id' => 0),
	array(),
	array()
));

$collection->add('admin_agents_groups_edit', new Route(
	'/agents/groups/{usergroup_id}/edit',
	array('_controller' => 'AdminBundle:Agents:editGroup'),
	array('usergroup_id' => '\\d+'),
	array()
));

$collection->add('admin_agents_groups_del', new Route(
	'/agents/groups/{usergroup_id}/delete/{security_token}',
	array('_controller' => 'AdminBundle:Agents:deleteGroup'),
	array('usergroup_id' => '\\d+'),
	array()
));

$collection->add('admin_agents_groups_new', new Route(
	'/agents/groups/new',
	array('_controller' => 'AdminBundle:Agents:editGroup', 'usergroup_id' => 0),
	array(),
	array()
));

$collection->add('admin_agents_notifications', new Route(
	'/agents/notifications',
	array('_controller' => 'AdminBundle:Agents:notifications'),
	array(),
	array()
));

$collection->add('admin_agents_notifications_getagent', new Route(
	'/agents/{person_id}/notifications/get-agent-options.json',
	array('_controller' => 'AdminBundle:Agents:notificationsGet'),
	array('person_id' => '\\d+'),
	array()
));

$collection->add('admin_agents_notifications_saveagent', new Route(
	'/agents/{person_id}/notifications/save-agent-options.json',
	array('_controller' => 'AdminBundle:Agents:notificationsSave'),
	array('person_id' => '\\d+', '_method' => 'POST'),
	array()
));

$collection->add('admin_login_logs', new Route(
	'/login-logs',
	array('_controller' => 'AdminBundle:Agents:loginLogs'),
	array(),
	array()
));

################################################################################
# Templates
################################################################################

$collection->add('admin_templates_user', new Route(
	'/templates/portal',
	array('_controller' => 'AdminBundle:Templates:userList'),
	array(),
	array()
));

$collection->add('admin_templates_search', new Route(
	'/templates/search.json',
	array('_controller' => 'AdminBundle:Templates:searchTemplates'),
	array(),
	array()
));

$collection->add('admin_templates_email', new Route(
	'/templates/email/{list_type}',
	array('_controller' => 'AdminBundle:Templates:emailList', 'list_type' => 'layout'),
	array(),
	array()
));

$collection->add('admin_templates_editemail', new Route(
	'/templates/email/edit/{name}',
	array('_controller' => 'AdminBundle:Templates:emailEdit'),
	array(),
	array()
));

$collection->add('admin_templates_deletecustom', new Route(
	'/templates/email/delete-custom/{name}',
	array('_controller' => 'AdminBundle:Templates:deleteCustomTemplate'),
	array(),
	array()
));

$collection->add('admin_templates_other', new Route(
	'/templates/other',
	array('_controller' => 'AdminBundle:Templates:otherList'),
	array(),
	array()
));

$collection->add('admin_templates_createtpl', new Route(
	'/templates/create-template',
	array('_controller' => 'AdminBundle:Templates:createTemplate'),
	array(),
	array()
));

$collection->add('admin_templates_getcode', new Route(
	'/templates/get-template-code',
	array('_controller' => 'AdminBundle:Templates:getTemplateCode'),
	array(),
	array()
));

$collection->add('admin_templates_save', new Route(
	'/templates/save-template.json',
	array('_controller' => 'AdminBundle:Templates:saveTemplate'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_templates_revert', new Route(
	'/templates/revert-template.json',
	array('_controller' => 'AdminBundle:Templates:revertTemplate'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_templates_minimanager', new Route(
	'/templates/mini-manager/{dirname}/{prefix}',
	array('_controller' => 'AdminBundle:Templates:miniManager'),
	array(),
	array()
));

$collection->add('admin_templates_previewemail', new Route(
	'/templates/preview-email-template/{tpl}',
	array('_controller' => 'AdminBundle:Templates:previewEmailTemplate'),
	array(),
	array('tpl' => '[A-Za-z:\-_\.]+')
));

################################################################################
# Language
################################################################################

$collection->add('admin_langs', new Route(
	'/languages',
	array('_controller' => 'AdminBundle:Languages:index'),
	array(),
	array()
));

$collection->add('admin_langs_mass_update_tickets', new Route(
	'/languages/mass-update-tickets',
	array('_controller' => 'AdminBundle:Languages:massUpdateTickets'),
	array(),
	array()
));

$collection->add('admin_langs_mass_update_people', new Route(
	'/languages/mass-update-people',
	array('_controller' => 'AdminBundle:Languages:massUpdatePeople'),
	array(),
	array()
));

$collection->add('admin_langs_toggle_auto', new Route(
	'/languages/settings/toggle-auto-install',
	array('_controller' => 'AdminBundle:Languages:toggleAutoInstall'),
	array(),
	array()
));

$collection->add('admin_langs_editlang', new Route(
	'/languages/{language_id}/edit',
	array('_controller' => 'AdminBundle:Languages:editLanguage'),
	array('language_id' => '\\d+'),
	array()
));

$collection->add('admin_langs_dellang', new Route(
	'/languages/{language_id}/delete/{security_token}',
	array('_controller' => 'AdminBundle:Languages:deleteLanguage'),
	array('language_id' => '\\d+'),
	array()
));

$collection->add('admin_langs_install_pack', new Route(
	'/languages/install-pack/{id}',
	array('_controller' => 'AdminBundle:Languages:installPack'),
	array(),
	array()
));

$collection->add('admin_langs_newphrase', new Route(
	'/languages/{language_id}/add-custom',
	array('_controller' => 'AdminBundle:Languages:addCustomPhrase'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_langs_getphrasetext', new Route(
	'/languages/get-phrase-text.json',
	array('_controller' => 'AdminBundle:Languages:getPhraseText'),
	array(),
	array()
));

$collection->add('admin_langs_departments', new Route(
	'/languages/{language_id}/phrases/departments/{type}',
	array('_controller' => 'AdminBundle:Languages:departments'),
	array('language_id' => '\\d+', 'type' => '(tickets|chat)'),
	array()
));

$collection->add('admin_langs_ticketpriorities', new Route(
	'/languages/{language_id}/phrases/ticket-priorities',
	array('_controller' => 'AdminBundle:Languages:ticketPriorities'),
	array('language_id' => '\\d+'),
	array()
));

$collection->add('admin_langs_ticketworkflows', new Route(
	'/languages/{language_id}/phrases/ticket-workflows',
	array('_controller' => 'AdminBundle:Languages:ticketWorkflows'),
	array('language_id' => '\\d+'),
	array()
));

$collection->add('admin_langs_products', new Route(
	'/languages/{language_id}/phrases/products',
	array('_controller' => 'AdminBundle:Languages:products'),
	array('language_id' => '\\d+'),
	array()
));

$collection->add('admin_langs_ticketcategories', new Route(
	'/languages/{language_id}/phrases/ticket-categories',
	array('_controller' => 'AdminBundle:Languages:ticketCategories'),
	array('language_id' => '\\d+'),
	array()
));

$collection->add('admin_langs_feedback', new Route(
	'/languages/{language_id}/phrases/feedback',
	array('_controller' => 'AdminBundle:Languages:feedback'),
	array('language_id' => '\\d+'),
	array()
));

$collection->add('admin_langs_kbcats', new Route(
	'/languages/{language_id}/phrases/kb-cats',
	array('_controller' => 'AdminBundle:Languages:kbCats'),
	array('language_id' => '\\d+'),
	array()
));

$collection->add('admin_langs_customfields', new Route(
	'/languages/{language_id}/phrases/fields/{field_type}',
	array('_controller' => 'AdminBundle:Languages:customFields'),
	array('language_id' => '\\d+'),
	array()
));

$collection->add('admin_langs_editphrases', new Route(
	'/languages/{language_id}/phrases/{group}',
	array('_controller' => 'AdminBundle:Languages:editPhrases'),
	array('language_id' => '\\d+', 'group' => '[a-zA-Z0-9\\.\\-_]+'),
	array()
));

$collection->add('admin_langs_editphrases_save', new Route(
	'/languages/{language_id}/phrases-save',
	array('_controller' => 'AdminBundle:Languages:savePhrases'),
	array('language_id' => '\\d+', '_method' => 'POST'),
	array()
));

$collection->add('admin_langs_editphrases_savearray', new Route(
	'/languages/phrases-save-array.json',
	array('_controller' => 'AdminBundle:Languages:savePhraseArray'),
	array('_method' => 'POST'),
	array()
));

################################################################################
# API
################################################################################

$collection->add('admin_api_keylist', new Route(
	'/api',
	array('_controller' => 'AdminBundle:Api:index'),
	array(),
	array()
));

$collection->add('admin_api_delkey', new Route(
	'/api/key/{id}/delete/{security_token}',
	array('_controller' => 'AdminBundle:Api:delKey'),
	array('id' => '\\d+'),
	array()
));

$collection->add('admin_api_editkey', new Route(
	'/api/key/{id}/edit',
	array('_controller' => 'AdminBundle:Api:editKey'),
	array('id' => '\\d+'),
	array()
));

$collection->add('admin_api_newkey', new Route(
	'/api/key/new',
	array('_controller' => 'AdminBundle:Api:editKey', 'id' => 0),
	array(),
	array()
));


################################################################################
# Users
################################################################################

$collection->add('admin_customdefpeople', new Route(
	'/people-fields',
	array('_controller' => 'AdminBundle:CustomDefPeople:index'),
	array(),
	array()
));

$collection->add('admin_customdefpeople_new_choosetype', new Route(
	'/people-fields/new-choose-type',
	array('_controller' => 'AdminBundle:CustomDefPeople:newChooseType', 'field_id' => 0),
	array(),
	array()
));

$collection->add('admin_customdefpeople_edit', new Route(
	'/people-fields/{field_id}/edit',
	array('_controller' => 'AdminBundle:CustomDefPeople:edit'),
	array('field_id' => '\\d+'),
	array()
));

$collection->add('admin_customdefpeople_delete', new Route(
	'/people-fields/{field_id}/delete/{security_token}',
	array('_controller' => 'AdminBundle:CustomDefPeople:delete'),
	array('field_id' => '\\d+'),
	array()
));

$collection->add('admin_customdefpeople_setenabled', new Route(
	'/people-fields/{field_id}/set-enabled',
	array('_controller' => 'AdminBundle:CustomDefPeople:setEnabled'),
	array('field_id' => '\\d+'),
	array()
));

$collection->add('admin_features', new Route(
	'/tickets/features',
	array('_controller' => 'AdminBundle:TicketFeatures:index'),
	array(),
	array()
));

$collection->add('admin_features_work_hours', new Route(
	'/tickets/features/work-hours',
	array('_controller' => 'AdminBundle:TicketFeatures:workHours'),
	array(),
	array()
));

$collection->add('admin_features_work_hours_save', new Route(
	'/tickets/features/work-hours/save',
	array('_controller' => 'AdminBundle:TicketFeatures:workHoursSave'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_ticketfeatures_regensearch', new Route(
	'/tickets/features/regenerate-search',
	array('_controller' => 'AdminBundle:TicketFeatures:regenSearch'),
	array(),
	array()
));

$collection->add('admin_ticketfeatures_purgetrash', new Route(
	'/tickets/features/purge-trash/{security_token}',
	array('_controller' => 'AdminBundle:TicketFeatures:purgeTrash'),
	array(),
	array()
));

$collection->add('admin_customdeftickets', new Route(
	'/ticket-fields',
	array('_controller' => 'AdminBundle:CustomDefTickets:index'),
	array(),
	array()
));

$collection->add('admin_customdeftickets_new_choosetype', new Route(
	'/ticket-fields/new-choose-type',
	array('_controller' => 'AdminBundle:CustomDefTickets:newChooseType', 'field_id' => 0),
	array(),
	array()
));

$collection->add('admin_customdeftickets_edit', new Route(
	'/ticket-fields/{field_id}/edit',
	array('_controller' => 'AdminBundle:CustomDefTickets:edit'),
	array('field_id' => '\\d+'),
	array()
));

$collection->add('admin_customdeftickets_delete', new Route(
	'/ticket-fields/{field_id}/delete/{security_token}',
	array('_controller' => 'AdminBundle:CustomDefTickets:delete'),
	array('field_id' => '\\d+'),
	array()
));

$collection->add('admin_customdeftickets_setenabled', new Route(
	'/ticket-fields/{field_id}/set-enabled',
	array('_controller' => 'AdminBundle:CustomDefTickets:setEnabled'),
	array('field_id' => '\\d+'),
	array()
));

$collection->add('admin_ticketwidgets', new Route(
	'/tickets/widgets',
	array('_controller' => 'AdminBundle:TicketWidgets:list'),
	array(),
	array()
));

$collection->add('admin_ticketwidgets_new_choosetype', new Route(
	'/tickets/widgets/new-choose-type',
	array('_controller' => 'AdminBundle:TicketWidgets:newChooseType'),
	array(),
	array()
));

$collection->add('admin_ticketwidgets_edit', new Route(
	'/tickets/widgets/{widget_id}/edit',
	array('_controller' => 'AdminBundle:TicketWidgets:edit'),
	array('widget_id' => '\\d+'),
	array()
));

$collection->add('admin_tickettriggers_export', new Route(
	'/tickets/triggers/export',
	array('_controller' => 'AdminBundle:TicketTriggers:exportTriggers'),
	array(),
	array()
));

$collection->add('admin_tickettriggers_export_download', new Route(
	'/tickets/triggers/export/{type}',
	array('_controller' => 'AdminBundle:TicketTriggers:exportTriggersDownload'),
	array(),
	array()
));

$collection->add('admin_tickettriggers_import', new Route(
	'/tickets/triggers/process-import',
	array('_controller' => 'AdminBundle:TicketTriggers:importTriggers'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_tickettriggers_new', new Route(
	'/tickets/triggers/new-trigger/{trigger_type}',
	array('_controller' => 'AdminBundle:TicketTriggers:editTrigger', 'id' => '0'),
	array(),
	array()
));

$collection->add('admin_ticketescalations_new', new Route(
	'/tickets/escalations/new-escalation/{trigger_type}',
	array('_controller' => 'AdminBundle:TicketTriggers:editEscalation', 'id' => '0'),
	array(),
	array()
));

$collection->add('admin_tickettriggers_edit', new Route(
	'/tickets/triggers/{id}/edit',
	array('_controller' => 'AdminBundle:TicketTriggers:editTrigger'),
	array('id' => '\\d+'),
	array()
));

$collection->add('admin_ticketescalations_edit', new Route(
	'/tickets/escalations/{id}/edit',
	array('_controller' => 'AdminBundle:TicketTriggers:editEscalation'),
	array('id' => '\\d+'),
	array()
));

$collection->add('admin_tickettriggers_save', new Route(
	'/tickets/triggers/{id}/save',
	array('_controller' => 'AdminBundle:TicketTriggers:saveTrigger'),
	array('id' => '\\d+', '_method' => 'POST'),
	array()
));

$collection->add('admin_tickettriggers_updateorder', new Route(
	'/tickets/triggers/update-orders.json',
	array('_controller' => 'AdminBundle:TicketTriggers:updateOrder'),
	array(),
	array()
));

$collection->add('admin_tickettriggers_toggle', new Route(
	'/tickets/triggers/toggle-enabled.json',
	array('_controller' => 'AdminBundle:TicketTriggers:toggleEnabled'),
	array(),
	array()
));

$collection->add('admin_tickettriggers', new Route(
	'/tickets/triggers/{list_type}',
	array('_controller' => 'AdminBundle:TicketTriggers:listTriggers', 'list_type' => ''),
	array(),
	array()
));

$collection->add('admin_ticketescalations', new Route(
	'/tickets/escalations',
	array('_controller' => 'AdminBundle:TicketTriggers:listEscalations'),
	array(),
	array()
));

$collection->add('admin_tickettriggers_delete', new Route(
	'/tickets/triggers/delete/{id}/{auth}',
	array('_controller' => 'AdminBundle:TicketTriggers:delete'),
	array('id' => '[0-9]+'),
	array()
));

$collection->add('admin_customdeforganizations', new Route(
	'/organization-fields',
	array('_controller' => 'AdminBundle:CustomDefOrganizations:index'),
	array(),
	array()
));

$collection->add('admin_customdeforganizations_new_choosetype', new Route(
	'/organization-fields/new-choose-type',
	array('_controller' => 'AdminBundle:CustomDefOrganizations:newChooseType', 'field_id' => 0),
	array(),
	array()
));

$collection->add('admin_customdeforganizations_edit', new Route(
	'/organization-fields/{field_id}/edit',
	array('_controller' => 'AdminBundle:CustomDefOrganizations:edit'),
	array('field_id' => '\\d+'),
	array()
));

$collection->add('admin_customdeforganizations_delete', new Route(
	'/organization-fields/{field_id}/delete/{security_token}',
	array('_controller' => 'AdminBundle:CustomDefOrganizations:delete'),
	array('field_id' => '\\d+'),
	array()
));

$collection->add('admin_customdeforganizations_setenabled', new Route(
	'/organization-fields/{field_id}/set-enabled',
	array('_controller' => 'AdminBundle:CustomDefOrganizations:setEnabled'),
	array('field_id' => '\\d+'),
	array()
));

################################################################################
# Importing
################################################################################

$collection->add('admin_import', new Route(
	'/import',
	array('_controller' => 'AdminBundle:Import:index'),
	array('field_id' => '\\d+'),
	array()
));

$collection->add('admin_import_csv_configure', new Route(
	'/import/csv-configure',
	array('_controller' => 'AdminBundle:Import:csvConfigure'),
	array('field_id' => '\\d+'),
	array()
));

$collection->add('admin_import_csv_import', new Route(
	'/import/csv-import',
	array('_controller' => 'AdminBundle:Import:csvImport'),
	array('field_id' => '\\d+'),
	array()
));

################################################################################
# User Rules
################################################################################

$collection->add('admin_userrules', new Route(
	'/user-rules',
	array('_controller' => 'AdminBundle:UserRules:list'),
	array(),
	array()
));

$collection->add('admin_userrules_new', new Route(
	'/user-rules/new',
	array('_controller' => 'AdminBundle:UserRules:edit', 'rule_id' => 0),
	array(),
	array()
));

$collection->add('admin_userrules_edit', new Route(
	'/user-rules/{rule_id}',
	array('_controller' => 'AdminBundle:UserRules:edit'),
	array('rule_id' => '\\d+'),
	array()
));

$collection->add('admin_userrules_apply', new Route(
	'/user-rules/{rule_id}/apply',
	array('_controller' => 'AdminBundle:UserRules:apply'),
	array('rule_id' => '\\d+'),
	array()
));

$collection->add('admin_userrules_applyrun', new Route(
	'/user-rules/{rule_id}/apply-run',
	array('_controller' => 'AdminBundle:UserRules:applyRun'),
	array('rule_id' => '\\d+'),
	array()
));

$collection->add('admin_userrules_delete', new Route(
	'/user-rules/{rule_id}/delete',
	array('_controller' => 'AdminBundle:UserRules:delete'),
	array('rule_id' => '\\d+'),
	array()
));

################################################################################
# Users : Usergroups
################################################################################

$collection->add('admin_usergroups', new Route(
	'/usergroups',
	array('_controller' => 'AdminBundle:Usergroups:list'),
	array(),
	array()
));

$collection->add('admin_usergroups_new', new Route(
	'/usergroups/new',
	array('_controller' => 'AdminBundle:Usergroups:edit', 'id' => 0),
	array('usergroup_id' => '\\d+'),
	array()
));

$collection->add('admin_usergroups_edit', new Route(
	'/usergroups/{id}/edit',
	array('_controller' => 'AdminBundle:Usergroups:edit'),
	array('id' => '\\d+'),
	array()
));

$collection->add('admin_usergroups_delete', new Route(
	'/usergroups/{id}/delete/{auth}',
	array('_controller' => 'AdminBundle:Usergroups:delete'),
	array('id' => '\\d+'),
	array()
));

$collection->add('admin_usergroups_toggle', new Route(
	'/agents/groups/{id}/toggle',
	array('_controller' => 'AdminBundle:Usergroups:toggleGroup'),
	array('id' => '\\d+'),
	array()
));

################################################################################
# Products
################################################################################

$collection->add('admin_products', new Route(
	'/products',
	array('_controller' => 'AdminBundle:Products:list'),
	array(),
	array()
));

$collection->add('admin_products_toggle', new Route(
	'/products/toggle-feature/{enable}',
	array('_controller' => 'AdminBundle:Products:toggleFeature'),
	array(),
	array()
));

$collection->add('admin_products_savenew', new Route(
	'/products/save-new',
	array('_controller' => 'AdminBundle:Products:saveNew'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_products_setdefault', new Route(
	'/products/set-default',
	array('_controller' => 'AdminBundle:Products:setDefault'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_products_savetitle', new Route(
	'/products/save-title',
	array('_controller' => 'AdminBundle:Products:saveTitle'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_products_updateorders', new Route(
	'/products/update-orders',
	array('_controller' => 'AdminBundle:Products:updateOrders'),
	array(),
	array()
));

$collection->add('admin_products_edit', new Route(
	'/products/{product_id}/edit',
	array('_controller' => 'AdminBundle:Products:edit'),
	array('product_id' => '\\d+'),
	array()
));

$collection->add('admin_products_del', new Route(
	'/products/{product_id}/delete',
	array('_controller' => 'AdminBundle:Products:delete'),
	array('product_id' => '\\d+'),
	array()
));

$collection->add('admin_products_dodel', new Route(
	'/products/{product_id}/delete/{security_token}',
	array('_controller' => 'AdminBundle:Products:doDelete'),
	array('product_id' => '\\d+', 'security_token' => '[a-zA-Z0-9\\-]+'),
	array()
));

################################################################################
# Product Fields
################################################################################

$collection->add('admin_customdefproducts', new Route(
	'/product-fields',
	array('_controller' => 'AdminBundle:CustomDefProducts:index'),
	array(),
	array()
));

$collection->add('admin_customdefproducts_new_choosetype', new Route(
	'/product-fields/new-choose-type',
	array('_controller' => 'AdminBundle:CustomDefProducts:newChooseType', 'field_id' => 0),
	array(),
	array()
));

$collection->add('admin_customdefproducts_edit', new Route(
	'/product-fields/{field_id}/edit',
	array('_controller' => 'AdminBundle:CustomDefProducts:edit'),
	array('field_id' => '\\d+'),
	array()
));

$collection->add('admin_customdefproducts_delete', new Route(
	'/product-fields/{field_id}/delete/{security_token}',
	array('_controller' => 'AdminBundle:CustomDefProducts:delete'),
	array('field_id' => '\\d+'),
	array()
));

$collection->add('admin_customdefproducts_setenabled', new Route(
	'/product-fields/{field_id}/set-enabled',
	array('_controller' => 'AdminBundle:CustomDefProducts:setEnabled'),
	array('field_id' => '\\d+'),
	array()
));

################################################################################
# Departments
################################################################################

$collection->add('admin_departments', new Route(
	'/departments/{type}',
	array('_controller' => 'AdminBundle:Departments:list', 'type' => ''),
	array('type' => '(tickets|chat|)'),
	array()
));

$collection->add('admin_departments_saveagents', new Route(
	'/departments/{department_id}/save-agents.json',
	array('_controller' => 'AdminBundle:Departments:saveAgents'),
	array('department_id' => '\\d+', '_method' => 'POST'),
	array()
));

$collection->add('admin_departments_savegateway', new Route(
	'/departments/{department_id}/save-gateway-account.json',
	array('_controller' => 'AdminBundle:Departments:saveGatewayAccount'),
	array('department_id' => '\\d+', '_method' => 'POST'),
	array()
));

$collection->add('admin_departments_setdefault', new Route(
	'/departments/{type}/set-default',
	array('_controller' => 'AdminBundle:Departments:setDefault'),
	array('department_id' => '\\d+'),
	array()
));

$collection->add('admin_departments_setphrase', new Route(
	'/departments/set-phrase',
	array('_controller' => 'AdminBundle:Departments:setPhrase'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_departments_savenew', new Route(
	'/departments/{type}/save-new',
	array('_controller' => 'AdminBundle:Departments:saveNew'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_departments_savetitle', new Route(
	'/departments/save-title',
	array('_controller' => 'AdminBundle:Departments:saveTitle'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_departments_del', new Route(
	'/departments/{department_id}/delete',
	array('_controller' => 'AdminBundle:Departments:delete'),
	array('department_id' => '\\d+'),
	array()
));

$collection->add('admin_departments_dodel', new Route(
	'/departments/{department_id}/delete/{security_token}',
	array('_controller' => 'AdminBundle:Departments:doDelete'),
	array('department_id' => '\\d+', 'security_token' => '[a-zA-Z0-9\\-]+'),
	array()
));

$collection->add('admin_departments_updateorders', new Route(
	'/departments/update-orders',
	array('_controller' => 'AdminBundle:Departments:updateOrders'),
	array(),
	array()
));


################################################################################
# Twitter
################################################################################

$collection->add('admin_twitter_accounts', new Route(
	'/twitter/accounts',
	array('_controller' => 'AdminBundle:TwitterAccount:list'),
	array(),
	array()
));

$collection->add('admin_twitter_apps', new Route(
	'/twitter/apps',
	array('_controller' => 'AdminBundle:TwitterAccount:apps'),
	array(),
	array()
));

$collection->add('admin_twitter_set_cleanup', new Route(
	'/twitter/set-cleanup',
	array('_controller' => 'AdminBundle:TwitterAccount:setCleanup'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_twitter_accounts_new', new Route(
	'/twitter/accounts/new',
	array('_controller' => 'AdminBundle:TwitterAccount:new'),
	array(),
	array()
));

$collection->add('admin_twitter_accounts_edit', new Route(
	'/twitter/accounts/{account_id}/edit',
	array('_controller' => 'AdminBundle:TwitterAccount:edit'),
	array(),
	array()
));

$collection->add('admin_twitter_accounts_delete', new Route(
	'/twitter/accounts/{account_id}/delete/{security_token}',
	array('_controller' => 'AdminBundle:TwitterAccount:delete'),
	array(),
	array()
));

################################################################################
# Plugins
################################################################################

$collection->add('admin_plugins', new Route(
	'/plugins',
	array('_controller' => 'AdminBundle:Plugins:list'),
	array(),
	array()
));

$collection->add('admin_plugins_toggle', new Route(
	'/plugins/toggle',
	array('_controller' => 'AdminBundle:Plugins:toggle'),
	array(),
	array()
));

$collection->add('admin_plugins_install', new Route(
	'/plugins/{plugin_id}/install',
	array('_controller' => 'AdminBundle:Plugins:install'),
	array(),
	array()
));

$collection->add('admin_plugins_install_step', new Route(
	'/plugins/{plugin_id}/install/{step}',
	array('_controller' => 'AdminBundle:Plugins:install'),
	array(),
	array()
));

$collection->add('admin_plugins_uninstall', new Route(
	'/plugins/{plugin_id}/uninstall',
	array('_controller' => 'AdminBundle:Plugins:uninstall'),
	array(),
	array()
));

$collection->add('admin_plugins_plugin', new Route(
	'/plugins/{plugin_id}/config',
	array('_controller' => 'AdminBundle:Plugins:config'),
	array(),
	array()
));

$collection->add('admin_plugins_run', new Route(
	'/plugins/{plugin_id}/run/{action}',
	array('_controller' => 'AdminBundle:Plugins:run'),
	array(),
	array()
));


################################################################################
# Publish
################################################################################

$collection->add('admin_feedback_statuses', new Route(
	'/portal/feedback/statuses',
	array('_controller' => 'AdminBundle:Feedback:statuses'),
	array(),
	array()
));

$collection->add('admin_feedback_statuses_ajaxadd', new Route(
	'/portal/feedback/statuses/new',
	array('_controller' => 'AdminBundle:Feedback:ajaxNewStatus'),
	array(),
	array()
));

$collection->add('admin_feedback_statuses_edit', new Route(
	'/portal/feedback/statuses/{category_id}/edit',
	array('_controller' => 'AdminBundle:Feedback:editStatus'),
	array(),
	array()
));

$collection->add('admin_feedback_statuses_del', new Route(
	'/portal/feedback/statuses/{category_id}/delete',
	array('_controller' => 'AdminBundle:Feedback:deleteStatus'),
	array(),
	array()
));

$collection->add('admin_feedback_status_updateorders', new Route(
	'/portal/feedback/statuses/update-orders',
	array('_controller' => 'AdminBundle:Feedback:updateStatusOrders'),
	array(),
	array()
));

$collection->add('admin_feedback_cats', new Route(
	'/portal/feedback/types',
	array('_controller' => 'AdminBundle:Feedback:categories'),
	array(),
	array()
));

$collection->add('admin_feedback_cats_edit', new Route(
	'/portal/feedback/types/{category_id}/edit',
	array('_controller' => 'AdminBundle:Feedback:editCategory'),
	array(),
	array()
));

$collection->add('admin_feedback_cats_del', new Route(
	'/portal/feedback/types/{category_id}/delete',
	array('_controller' => 'AdminBundle:Feedback:deleteCategory'),
	array(),
	array()
));

$collection->add('admin_feedback_cats_updateorders', new Route(
	'/portal/feedback/types/update-orders',
	array('_controller' => 'AdminBundle:Feedback:updateCategoryOrders'),
	array(),
	array()
));

$collection->add('admin_feedback_usercats', new Route(
	'/portal/feedback/categories',
	array('_controller' => 'AdminBundle:Feedback:userCategory'),
	array(),
	array()
));

$collection->add('admin_customdefarticles', new Route(
	'/article-fields',
	array('_controller' => 'AdminBundle:CustomDefArticles:index'),
	array(),
	array()
));

$collection->add('admin_customdefarticles_new_choosetype', new Route(
	'/article-fields/new-choose-type',
	array('_controller' => 'AdminBundle:CustomDefArticles:newChooseType', 'field_id' => 0),
	array(),
	array()
));

$collection->add('admin_customdefarticles_edit', new Route(
	'/article-fields/{field_id}/edit',
	array('_controller' => 'AdminBundle:CustomDefArticles:edit'),
	array('field_id' => '\\d+'),
	array()
));

$collection->add('admin_customdefarticles_delete', new Route(
	'/article-fields/{field_id}/delete/{security_token}',
	array('_controller' => 'AdminBundle:CustomDefArticles:delete'),
	array('field_id' => '\\d+'),
	array()
));

$collection->add('admin_customdefarticles_setenabled', new Route(
	'/article-fields/{field_id}/set-enabled',
	array('_controller' => 'AdminBundle:CustomDefArticles:setEnabled'),
	array('field_id' => '\\d+'),
	array()
));

$collection->add('admin_customdeffeedback', new Route(
	'/feedback-fields',
	array('_controller' => 'AdminBundle:CustomDefFeedback:index'),
	array(),
	array()
));

$collection->add('admin_customdeffeedback_new_choosetype', new Route(
	'/feedback-fields/new-choose-type',
	array('_controller' => 'AdminBundle:CustomDefFeedback:newChooseType', 'field_id' => 0),
	array(),
	array()
));

$collection->add('admin_customdeffeedback_edit', new Route(
	'/feedback-fields/{field_id}/edit',
	array('_controller' => 'AdminBundle:CustomDefFeedback:edit'),
	array('field_id' => '\\d+'),
	array()
));

$collection->add('admin_customdeffeedback_delete', new Route(
	'/feedback-fields/{field_id}/delete/{security_token}',
	array('_controller' => 'AdminBundle:CustomDefFeedback:delete'),
	array('field_id' => '\\d+'),
	array()
));

$collection->add('admin_customdeffeedback_setenabled', new Route(
	'/feedback-fields/{field_id}/set-enabled',
	array('_controller' => 'AdminBundle:CustomDefFeedback:setEnabled'),
	array('field_id' => '\\d+'),
	array()
));

################################################################################
# Email Gatewayss
################################################################################

$collection->add('admin_emailgateways', new Route(
	'/email/incoming',
	array('_controller' => 'AdminBundle:EmailGateways:list'),
	array(),
	array()
));

$collection->add('admin_emailgateways_savehdaddr', new Route(
	'/email/incoming/save-helpdesk-addresses',
	array('_controller' => 'AdminBundle:EmailGateways:saveHelpdeskAddresses'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_emailgateways_new', new Route(
	'/email/incoming/new',
	array('_controller' => 'AdminBundle:EmailGateways:editAccount', 'id' => 0),
	array(),
	array()
));

$collection->add('admin_emailgateways_edit', new Route(
	'/email/incoming/accounts/{id}/edit',
	array('_controller' => 'AdminBundle:EmailGateways:editAccount'),
	array('id' => '\\d+'),
	array()
));

$collection->add('admin_emailgateways_quicktoggle', new Route(
	'/email/incoming/accounts/{id}/quick-toggle.json',
	array('_controller' => 'AdminBundle:EmailGateways:quickToggle'),
	array('id' => '\\d+'),
	array()
));

$collection->add('admin_emailgateways_setlinkeddep', new Route(
	'/email/incoming/accounts/set-linked-department.json',
	array('_controller' => 'AdminBundle:EmailGateways:setLinkedDepartment'),
	array('id' => '\\d+', '_method' => 'POST'),
	array()
));

$collection->add('admin_emailgateways_del', new Route(
	'/email/incoming/accounts/{id}/delete/{security_token}',
	array('_controller' => 'AdminBundle:EmailGateways:delete'),
	array('id' => '\\d+'),
	array()
));

$collection->add('admin_emailgateways_testaccount', new Route(
	'/email/incoming/accounts/test-account.json',
	array('_controller' => 'AdminBundle:EmailGateways:ajaxTest'),
	array('id' => '\\d+'),
	array()
));

################################################################################
# Ticket message templates
################################################################################

$collection->add('admin_ticket_msgtpl', new Route(
	'/tickets/message-templates',
	array('_controller' => 'AdminBundle:TicketMessageTemplates:index'),
	array(),
	array()
));

$collection->add('admin_ticket_msgtpl_new', new Route(
	'/tickets/message-templates/new',
	array('_controller' => 'AdminBundle:TicketMessageTemplates:edit', 'id' => 0),
	array(),
	array()
));

$collection->add('admin_ticket_msgtpl_edit', new Route(
	'/tickets/message-templates/{id}',
	array('_controller' => 'AdminBundle:TicketMessageTemplates:edit'),
	array(),
	array()
));

$collection->add('admin_ticket_msgtpl_delete', new Route(
	'/tickets/message-templates/{id}/{security_token}',
	array('_controller' => 'AdminBundle:TicketMessageTemplates:delete'),
	array(),
	array()
));

################################################################################
# Email Transports
################################################################################

$collection->add('admin_emailtrans_set_default_from', new Route(
	'/email/outgoing/update-default-from',
	array('_controller' => 'AdminBundle:EmailTransports:setDefaultFrom'),
	array(),
	array()
));

$collection->add('admin_emailtrans_list', new Route(
	'/email/outgoing',
	array('_controller' => 'AdminBundle:EmailTransports:list'),
	array(),
	array()
));

$collection->add('admin_emailtrans_setup', new Route(
	'/setup/default-smtp',
	array('_controller' => 'AdminBundle:EmailTransports:setup'),
	array(),
	array()
));

$collection->add('admin_emailtrans_newaccount', new Route(
	'/email/outgoing/accounts/new',
	array('_controller' => 'AdminBundle:EmailTransports:editAccount', 'id' => 0),
	array(),
	array()
));

$collection->add('admin_emailtrans_editaccount', new Route(
	'/email/outgoing/accounts/{id}/edit',
	array('_controller' => 'AdminBundle:EmailTransports:editAccount'),
	array(),
	array()
));

$collection->add('admin_emailtrans_del', new Route(
	'/email/outgoing/accounts/{id}/delete/{security_token}',
	array('_controller' => 'AdminBundle:EmailTransports:delete'),
	array('id' => '\\d+'),
	array()
));

$collection->add('admin_emailtrans_testaccount', new Route(
	'/email/outgoing/accounts/test-account.json',
	array('_controller' => 'AdminBundle:EmailTransports:ajaxTest'),
	array(),
	array()
));

################################################################################
# Server related stuff
################################################################################

$collection->add('admin_server_cron', new Route(
	'/server/cron',
	array('_controller' => 'AdminBundle:Cron:list'),
	array(),
	array()
));

$collection->add('admin_server_cron_logs', new Route(
	'/server/cron/logs',
	array('_controller' => 'AdminBundle:Cron:logs'),
	array(),
	array()
));

$collection->add('admin_server_cron_logs_clear', new Route(
	'/server/cron/logs/clear',
	array('_controller' => 'AdminBundle:Cron:clearLogs'),
	array(),
	array()
));

$collection->add('admin_server_checks', new Route(
	'/server/checks',
	array('_controller' => 'AdminBundle:Server:serverChecks'),
	array(),
	array()
));

$collection->add('admin_server_file_checks', new Route(
	'/server/file-integrity-checks',
	array('_controller' => 'AdminBundle:Server:fileChecks'),
	array(),
	array()
));

$collection->add('admin_server_file_checks_do', new Route(
	'/server/file-integrity-checks/do/{batch}',
	array('_controller' => 'AdminBundle:Server:fileChecksDo', 'batch' => '0'),
	array(),
	array()
));

$collection->add('admin_server_phpinfo', new Route(
	'/server/phpinfo',
	array('_controller' => 'AdminBundle:Server:phpinfo'),
	array(),
	array()
));

$collection->add('admin_server_phpinfo_download', new Route(
	'/server/phpinfo/download',
	array('_controller' => 'AdminBundle:Server:phpinfoDownload'),
	array(),
	array()
));

$collection->add('admin_server_mysqlinfo', new Route(
	'/server/mysqlinfo',
	array('_controller' => 'AdminBundle:Server:mysqlinfo'),
	array(),
	array()
));

$collection->add('admin_server_mysqlstatus', new Route(
	'/server/mysqlstatus',
	array('_controller' => 'AdminBundle:Server:mysqlstatus'),
	array(),
	array()
));

$collection->add('admin_server_mysql_sorting', new Route(
	'/server/mysql-sorting',
	array('_controller' => 'AdminBundle:Server:mysqlSorting'),
	array(),
	array()
));

$collection->add('admin_server_mysql_sorting_save', new Route(
	'/server/mysql-sorting/save',
	array('_controller' => 'AdminBundle:Server:mysqlSortingSave'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_server_mysql_sorting_status', new Route(
	'/server/mysql-sorting/status',
	array('_controller' => 'AdminBundle:Server:mysqlSortingStatus'),
	array(),
	array()
));

$collection->add('admin_server_downloadschema', new Route(
	'/server/database-schema.sql',
	array('_controller' => 'AdminBundle:Server:downloadDatabaseSchema'),
	array(),
	array()
));

$collection->add('admin_server_testemail', new Route(
	'/server/test-email',
	array('_controller' => 'AdminBundle:Server:testEmail'),
	array(),
	array()
));

$collection->add('admin_server_error_logs', new Route(
	'/server/error-logs',
	array('_controller' => 'AdminBundle:Server:errorLogs'),
	array(),
	array()
));

$collection->add('admin_server_error_logs_clear', new Route(
	'/server/error-logs/clear-all',
	array('_controller' => 'AdminBundle:Server:errorLogsClearAll'),
	array(),
	array()
));

$collection->add('admin_server_error_logs_view', new Route(
	'/server/error-logs/{log_id}',
	array('_controller' => 'AdminBundle:Server:viewErrorLog'),
	array(),
	array()
));

$collection->add('admin_server_attach', new Route(
	'/server/attachments',
	array('_controller' => 'AdminBundle:Server:attachments'),
	array(),
	array()
));

$collection->add('admin_server_attach_switch', new Route(
	'/server/attachments/switch',
	array('_controller' => 'AdminBundle:Server:attachmentsSwitch'),
	array(),
	array()
));

$collection->add('admin_server_task_queue_logs', new Route(
	'/server/task-queue/logs',
	array('_controller' => 'AdminBundle:TaskQueue:logs'),
	array(),
	array()
));


################################################################################
# Email gateway errors
################################################################################

$collection->add('admin_emailgateway_errors', new Route(
	'/email/gateway-errors/{object_type}',
	array('_controller' => 'AdminBundle:EmailGatewayErrors:index', 'type' => 'errors', 'object_type' => 'ticket'),
	array(),
	array()
));

$collection->add('admin_emailgateway_all', new Route(
	'/email/list-sources/{object_type}',
	array('_controller' => 'AdminBundle:EmailGatewayErrors:index', 'type' => 'all', 'object_type' => 'ticket'),
	array(),
	array()
));

$collection->add('admin_emailgateway_rejections', new Route(
	'/email/gateway-rejections/{object_type}',
	array('_controller' => 'AdminBundle:EmailGatewayErrors:index', 'type' => 'rejections', 'object_type' => 'ticket'),
	array(),
	array()
));

$collection->add('admin_emailgateway_errors_clear', new Route(
	'/email/gateway-errors/clear/{security_token}/{object_type}',
	array('_controller' => 'AdminBundle:EmailGatewayErrors:clear', 'type' => 'errors', 'object_type' => 'ticket'),
	array(),
	array()
));

$collection->add('admin_emailgateway_rejections_clear', new Route(
	'/email/gateway-rejections/clear/{security_token}/{object_type}',
	array('_controller' => 'AdminBundle:EmailGatewayErrors:clear', 'type' => 'rejections', 'object_type' => 'ticket'),
	array(),
	array()
));

$collection->add('admin_emailgateway_errors_view', new Route(
	'/email/gateway-sources/{id}',
	array('_controller' => 'AdminBundle:EmailGatewayErrors:view'),
	array(),
	array()
));

$collection->add('admin_emailgateway_errors_delete', new Route(
	'/email/gateway-sources/{id}/delete/{security_token}',
	array('_controller' => 'AdminBundle:EmailGatewayErrors:delete'),
	array(),
	array()
));

$collection->add('admin_emailgateway_reprocess', new Route(
	'/email/gateway-sources/{id}/reprocess/{security_token}',
	array('_controller' => 'AdminBundle:EmailGatewayErrors:reprocess'),
	array(),
	array()
));

################################################################################
# Sendmail Queue
################################################################################

$collection->add('admin_sendmail_queue_index', new Route(
	'/email/sendmail-queue',
	array('_controller' => 'AdminBundle:SendmailQueue:index'),
	array(),
	array()
));

$collection->add('admin_sendmail_queue_massactions', new Route(
	'/email/sendmail-queue/mass-actions',
	array('_controller' => 'AdminBundle:SendmailQueue:massActions'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_sendmail_queue_view', new Route(
	'/email/sendmail-queue/{id}',
	array('_controller' => 'AdminBundle:SendmailQueue:view'),
	array('id' => '\d+'),
	array()
));

################################################################################
# Widgets
################################################################################

$collection->add('admin_widgets', new Route(
	'/widgets',
	array('_controller' => 'AdminBundle:Widgets:index'),
	array(),
	array()
));

$collection->add('admin_widgets_new', new Route(
	'/widgets/new',
	array('_controller' => 'AdminBundle:Widgets:edit', 'widget_id' => 0),
	array(),
	array()
));

$collection->add('admin_widgets_edit', new Route(
	'/widgets/{widget_id}/edit',
	array('_controller' => 'AdminBundle:Widgets:edit'),
	array(),
	array()
));

$collection->add('admin_widgets_delete', new Route(
	'/widgets/{widget_id}/delete',
	array('_controller' => 'AdminBundle:Widgets:delete'),
	array(),
	array()
));

$collection->add('admin_widgets_toggle', new Route(
	'/widgets/toggle',
	array('_controller' => 'AdminBundle:Widgets:toggle'),
	array(),
	array()
));

################################################################################
# Web Hooks
################################################################################

$collection->add('admin_webhooks', new Route(
	'/web-hooks',
	array('_controller' => 'AdminBundle:WebHook:index'),
	array(),
	array()
));

$collection->add('admin_webhooks_new', new Route(
	'/web-hooks/new',
	array('_controller' => 'AdminBundle:WebHook:edit', 'webhook_id' => 0),
	array(),
	array()
));

$collection->add('admin_webhooks_edit', new Route(
	'/web-hooks/{webhook_id}/edit',
	array('_controller' => 'AdminBundle:WebHook:edit'),
	array(),
	array()
));

$collection->add('admin_webhooks_delete', new Route(
	'/web-hooks/{webhook_id}/delete/{security_token}',
	array('_controller' => 'AdminBundle:WebHook:delete'),
	array(),
	array()
));

$collection->add('admin_webhooks_test', new Route(
	'/web-hooks/{webhook_id}/test/{security_token}',
	array('_controller' => 'AdminBundle:WebHook:test'),
	array(),
	array()
));

################################################################################
# KB/Articles
################################################################################

$collection->add('admin_kb_gateways', new Route(
	'/kb/gateways',
	array('_controller' => 'AdminBundle:Kb:gateways'),
	array(),
	array()
));

$collection->add('admin_kb_gateways_set_category', new Route(
	'/kb/gateways/set-category.json',
	array('_controller' => 'AdminBundle:Kb:setGatewayCategory'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_kb_gateways_new', new Route(
	'/kb/gateways/new',
	array('_controller' => 'AdminBundle:Kb:editGateway', 'id' => 0),
	array(),
	array()
));

$collection->add('admin_kb_gateways_edit', new Route(
	'/kb/gateways/{id}/edit',
	array('_controller' => 'AdminBundle:Kb:editGateway'),
	array('id' => '\\d+'),
	array()
));

$collection->add('admin_kb_gateways_quicktoggle', new Route(
	'/kb/gateways/{id}/quick-toggle.json',
	array('_controller' => 'AdminBundle:Kb:quickToggleGateway'),
	array('id' => '\\d+'),
	array()
));

$collection->add('admin_kb_gateways_del', new Route(
	'/kb/gateways/{id}/delete/{security_token}',
	array('_controller' => 'AdminBundle:Kb:deleteGateway'),
	array('id' => '\\d+'),
	array()
));


################################################################################
# Chat
################################################################################

$collection->add('admin_customdefchat', new Route(
	'/chat-fields',
	array('_controller' => 'AdminBundle:CustomDefChat:index'),
	array(),
	array()
));

$collection->add('admin_customdefchat_new_choosetype', new Route(
	'/chat-fields/new-choose-type',
	array('_controller' => 'AdminBundle:CustomDefChat:newChooseType', 'field_id' => 0),
	array(),
	array()
));

$collection->add('admin_customdefchat_edit', new Route(
	'/chat-fields/{field_id}/edit',
	array('_controller' => 'AdminBundle:CustomDefChat:edit'),
	array('field_id' => '\\d+'),
	array()
));

$collection->add('admin_customdefchat_delete', new Route(
	'/chat-fields/{field_id}/delete/{security_token}',
	array('_controller' => 'AdminBundle:CustomDefChat:delete'),
	array('field_id' => '\\d+'),
	array()
));

$collection->add('admin_customdefchat_setenabled', new Route(
	'/chat-fields/{field_id}/set-enabled',
	array('_controller' => 'AdminBundle:CustomDefChat:setEnabled'),
	array('field_id' => '\\d+'),
	array()
));


$collection->add('admin_chat_editor_reset', new Route(
	'/chat/editor/reset-all/{security_token}',
	array('_controller' => 'AdminBundle:Chat:resetEditor'),
	array(),
	array()
));

$collection->add('admin_chat_editor', new Route(
	'/chat/editor/{department_id}/{section}',
	array('_controller' => 'AdminBundle:Chat:editor', 'department_id' => 0, 'section' => 'create'),
	array('department_id' => '\d+'),
	array()
));

$collection->add('admin_chat_editor_toggleper', new Route(
	'/chat/editor/toggle-per-department',
	array('_controller' => 'AdminBundle:Chat:togglePerDepartment'),
	array(),
	array()
));

$collection->add('admin_chat_editor_dep_init', new Route(
	'/chat/editor/{department_id}/{section}/init',
	array('_controller' => 'AdminBundle:Chat:initEditor', 'section' => 'create'),
	array(),
	array()
));

$collection->add('admin_chat_editor_dep_revert', new Route(
	'/chat/editor/{department_id}/{section}/revert',
	array('_controller' => 'AdminBundle:Chat:revertEditor', 'section' => 'create'),
	array(),
	array()
));

$collection->add('admin_chat_editor_dep', new Route(
	'/chat/editor/{department_id}/{section}',
	array('_controller' => 'AdminBundle:Chat:editor', 'section' => 'create'),
	array(),
	array()
));

$collection->add('admin_chat_editor_dep_save', new Route(
	'/chat/editor/{department_id}/{section}/save',
	array('_controller' => 'AdminBundle:Chat:saveEditor'),
	array('_method' => 'POST'),
	array()
));

return $collection;
