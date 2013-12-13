<?php if (!defined('DP_ROOT')) exit('No access');

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

################################################################################
# Dashboard
################################################################################

$collection->add('admin', new Route(
	'/',
	array('_controller' => 'CloudAdminBundle:Main:index'),
	array(),
	array()
));

################################################################################
# Settings
################################################################################

$collection->add('admin_settings', new Route(
	'/settings',
	array('_controller' => 'CloudAdminBundle:Settings:settings'),
	array(),
	array()
));

$collection->add('admin_settings_setcustomdomain', new Route(
	'/settings/set-custom-domain.json',
	array('_controller' => 'CloudAdminBundle:Settings:setCustomDomain'),
	array(),
	array()
));

$collection->add('admin_settings_saveform', new Route(
	'/settings/save-settings/{type}/{auth}',
	array('_controller' => 'CloudAdminBundle:Settings:settingsSaveForm'),
	array(),
	array()
));

$collection->add('admin_settings_silent_settings', new Route(
	'/settings/welcome/set-settings-silent.json',
	array('_controller' => 'CloudAdminBundle:Settings:setSilentSettings'),
	array(),
	array()
));

$collection->add('admin_settings_cron_check', new Route(
	'/settings/cron/check.json',
	array('_controller' => 'CloudAdminBundle:Settings:checkCron'),
	array(),
	array()
));

$collection->add('admin_settings_cron', new Route(
	'/settings/cron',
	array('_controller' => 'CloudAdminBundle:Settings:cron'),
	array(),
	array()
));

$collection->add('admin_settings_set', new Route(
	'/settings/save-setting/{setting_name}/{security_token}',
	array('_controller' => 'CloudAdminBundle:Settings:saveSingleSetting'),
	array(),
	array()
));

$collection->add('admin_settings_adv', new Route(
	'/settings/advanced',
	array('_controller' => 'CloudAdminBundle:Settings:advanced'),
	array(),
	array()
));

$collection->add('admin_settings_adv_set', new Route(
	'/settings/advanced-set/{name}',
	array('_controller' => 'CloudAdminBundle:Settings:advancedSet'),
	array(),
	array()
));

$collection->add('admin_labels', new Route(
	'/settings/labels/{label_type}',
	array('_controller' => 'CloudAdminBundle:Settings:labels'),
	array('label_type' => '[a-z]+'),
	array()
));

$collection->add('admin_labels_rename', new Route(
	'/settings/labels/{label_type}/rename.json',
	array('_controller' => 'CloudAdminBundle:Settings:renameLabel'),
	array('label_type' => '[a-z]+'),
	array()
));

$collection->add('admin_labels_new', new Route(
	'/settings/labels/new.json',
	array('_controller' => 'CloudAdminBundle:Settings:labelsAjaxNew'),
	array('label_type' => '[a-z]+'),
	array()
));

$collection->add('admin_labels_del', new Route(
	'/settings/labels/{label_type}/delete.json',
	array('_controller' => 'CloudAdminBundle:Settings:labelsAjaxDelete'),
	array('label_type' => '[a-z]+'),
	array()
));

################################################################################
# Email Gatewayss
################################################################################

$collection->add('admin_emailgateways', new Route(
	'/email/incoming',
	array('_controller' => 'CloudAdminBundle:EmailGateways:list'),
	array(),
	array()
));

$collection->add('admin_emailgateways_savehdaddr', new Route(
	'/email/incoming/save-helpdesk-addresses',
	array('_controller' => 'CloudAdminBundle:EmailGateways:saveHelpdeskAddresses'),
	array(),
	array()
));

$collection->add('admin_emailgateways_new', new Route(
	'/email/incoming/new',
	array('_controller' => 'CloudAdminBundle:EmailGateways:editAccount', 'id' => 0),
	array(),
	array()
));

$collection->add('admin_emailgateways_edit', new Route(
	'/email/incoming/accounts/{id}/edit',
	array('_controller' => 'CloudAdminBundle:EmailGateways:editAccount'),
	array('id' => '\\d+'),
	array()
));

$collection->add('admin_emailgateways_quicktoggle', new Route(
	'/email/incoming/accounts/{id}/quick-toggle.json',
	array('_controller' => 'CloudAdminBundle:EmailGateways:quickToggle'),
	array('id' => '\\d+'),
	array()
));

$collection->add('admin_emailgateways_setlinkeddep', new Route(
	'/email/incoming/accounts/set-linked-department.json',
	array('_controller' => 'CloudAdminBundle:EmailGateways:setLinkedDepartment'),
	array('id' => '\\d+', '_method' => 'POST'),
	array()
));

$collection->add('admin_emailgateways_del', new Route(
	'/email/incoming/accounts/{id}/delete/{security_token}',
	array('_controller' => 'CloudAdminBundle:EmailGateways:delete'),
	array('id' => '\\d+'),
	array()
));

$collection->add('admin_emailgateways_testaccount', new Route(
	'/email/incoming/accounts/test-account.json',
	array('_controller' => 'CloudAdminBundle:EmailGateways:ajaxTest'),
	array('id' => '\\d+'),
	array()
));


// Custom cloud routes
$collection->add('admin_emailgateways_newcloud', new Route(
	'/email/incoming/new-cloud',
	array('_controller' => 'CloudAdminBundle:EmailGateways:newCloudEmail'),
	array(),
	array()
));

$collection->add('admin_emailgateways_cloud_alias', new Route(
	'/email/incoming/accounts/set-cloud-alias',
	array('_controller' => 'CloudAdminBundle:EmailGateways:setCloudAlias'),
	array('id' => '\\d+'),
	array()
));

$collection->add('admin_emailgateways_cloud_setoutgoing', new Route(
	'/email/incoming/accounts/set-cloud-outgoing',
	array('_controller' => 'CloudAdminBundle:EmailGateways:setCloudOutgoingAccount'),
	array('id' => '\\d+'),
	array()
));

$collection->add('admin_emailgateways_cloud_setoutgoing_form', new Route(
	'/email/incoming/accounts/set-cloud-outgoing/form',
	array('_controller' => 'CloudAdminBundle:EmailGateways:getCloudOutgoingAccountForm'),
	array('id' => '\\d+'),
	array()
));

$collection->add('admin_emailgateways_cloud_editaccount', new Route(
	'/email/incoming/cloud-edit-account/{gateway_id}',
	array('_controller' => 'CloudAdminBundle:EmailGateways:cloudEditAccount'),
	array(),
	array()
));

$collection->add('admin_emailgateways_cloud_editaccount_save', new Route(
	'/email/incoming/cloud-edit-account/{gateway_id}/save',
	array('_controller' => 'CloudAdminBundle:EmailGateways:cloudEditAccountSave'),
	array(),
	array()
));

################################################################################
# Email Transports
################################################################################

$collection->add('admin_emailtrans_set_default_from', new Route(
	'/email/outgoing/update-default-from',
	array('_controller' => 'CloudAdminBundle:EmailTransports:setDefaultFrom'),
	array(),
	array()
));

$collection->add('admin_emailtrans_list', new Route(
	'/email/outgoing',
	array('_controller' => 'CloudAdminBundle:EmailTransports:list'),
	array(),
	array()
));

$collection->add('admin_emailtrans_setup', new Route(
	'/setup/default-smtp',
	array('_controller' => 'CloudAdminBundle:EmailTransports:setup'),
	array(),
	array()
));

$collection->add('admin_emailtrans_newaccount', new Route(
	'/email/outgoing/accounts/new',
	array('_controller' => 'CloudAdminBundle:EmailTransports:editAccount', 'id' => 0),
	array(),
	array()
));

$collection->add('admin_emailtrans_editaccount', new Route(
	'/email/outgoing/accounts/{id}/edit',
	array('_controller' => 'CloudAdminBundle:EmailTransports:editAccount'),
	array(),
	array()
));

$collection->add('admin_emailtrans_del', new Route(
	'/email/outgoing/accounts/{id}/delete/{security_token}',
	array('_controller' => 'CloudAdminBundle:EmailTransports:delete'),
	array('id' => '\\d+'),
	array()
));

$collection->add('admin_emailtrans_testaccount', new Route(
	'/email/outgoing/accounts/test-account.json',
	array('_controller' => 'CloudAdminBundle:EmailTransports:ajaxTest'),
	array(),
	array()
));


################################################################################
# Server related stuff
################################################################################

$collection->add('admin_server_cron', new Route(
	'/server/cron',
	array('_controller' => 'CloudAdminBundle:Cron:list'),
	array(),
	array()
));

$collection->add('admin_server_cron_logs', new Route(
	'/server/cron/logs',
	array('_controller' => 'CloudAdminBundle:Cron:logs'),
	array(),
	array()
));

$collection->add('admin_server_cron_logs_clear', new Route(
	'/server/cron/logs/clear',
	array('_controller' => 'CloudAdminBundle:Cron:clearLogs'),
	array(),
	array()
));

$collection->add('admin_server_checks', new Route(
	'/server/checks',
	array('_controller' => 'CloudAdminBundle:Server:serverChecks'),
	array(),
	array()
));

$collection->add('admin_server_file_checks', new Route(
	'/server/file-integrity-checks',
	array('_controller' => 'CloudAdminBundle:Server:fileChecks'),
	array(),
	array()
));

$collection->add('admin_server_file_checks_do', new Route(
	'/server/file-integrity-checks/do/{batch}',
	array('_controller' => 'CloudAdminBundle:Server:fileChecksDo', 'batch' => '0'),
	array(),
	array()
));

$collection->add('admin_server_phpinfo', new Route(
	'/server/phpinfo',
	array('_controller' => 'CloudAdminBundle:Server:phpinfo'),
	array(),
	array()
));

$collection->add('admin_server_mysqlinfo', new Route(
	'/server/mysqlinfo',
	array('_controller' => 'CloudAdminBundle:Server:mysqlinfo'),
	array(),
	array()
));

$collection->add('admin_server_mysqlstatus', new Route(
	'/server/mysqlstatus',
	array('_controller' => 'CloudAdminBundle:Server:mysqlstatus'),
	array(),
	array()
));

$collection->add('admin_server_error_logs', new Route(
	'/server/error-logs',
	array('_controller' => 'CloudAdminBundle:Server:errorLogs'),
	array(),
	array()
));

$collection->add('admin_server_error_logs_clear', new Route(
	'/server/error-logs/clear-all',
	array('_controller' => 'CloudAdminBundle:Server:errorLogsClearAll'),
	array(),
	array()
));

$collection->add('admin_server_error_logs_view', new Route(
	'/server/error-logs/{log_id}',
	array('_controller' => 'CloudAdminBundle:Server:viewErrorLog'),
	array(),
	array()
));

$collection->add('admin_server_attach', new Route(
	'/server/attachments',
	array('_controller' => 'CloudAdminBundle:Server:attachments'),
	array(),
	array()
));

$collection->add('admin_server_attach_switch', new Route(
	'/server/attachments/switch',
	array('_controller' => 'CloudAdminBundle:Server:attachmentsSwitch'),
	array(),
	array()
));

################################################################################
# Agents
################################################################################

$collection->add('admin_agents', new Route(
	'/agents',
	array('_controller' => 'CloudAdminBundle:Agents:agents'),
	array(),
	array()
));

$collection->add('admin_mass_add', new Route(
	'/agents/mass-add-agents.json',
	array('_controller' => 'CloudAdminBundle:Agents:massAddAgents'),
	array('_method' => 'POST'),
	array()
));

$collection->add('admin_agents_deleted', new Route(
	'/agents/deleted',
	array('_controller' => 'CloudAdminBundle:Agents:deletedAgents'),
	array(),
	array()
));

$collection->add('admin_agents_new', new Route(
	'/agents/new',
	array('_controller' => 'CloudAdminBundle:Agents:editAgent', 'person_id' => '0'),
	array(),
	array()
));

$collection->add('admin_agents_new_fromusersource', new Route(
	'/agents/new-from-usersource/{usersource_id}',
	array('_controller' => 'CloudAdminBundle:Agents:newFromUsersource', 'usersource_id' => '0'),
	array(),
	array()
));

$collection->add('admin_agents_new_fromusersource_make', new Route(
	'/agents/new-from-usersource/{usersource_id}/make',
	array('_controller' => 'CloudAdminBundle:Agents:newFromUsersourceMake', 'usersource_id' => '0'),
	array(),
	array()
));

$collection->add('admin_agents_new_fromusersource_search', new Route(
	'/agents/new-from-usersource/{usersource_id}/search',
	array('_controller' => 'CloudAdminBundle:Agents:newFromUsersourceSearch', 'usersource_id' => '0'),
	array(),
	array()
));

$collection->add('admin_agents_edit', new Route(
	'/agents/{person_id}/edit',
	array('_controller' => 'CloudAdminBundle:Agents:editAgent'),
	array('person_id' => '\\d+'),
	array()
));

$collection->add('admin_agents_edit_formvalidate', new Route(
	'/agents/{person_id}/edit/validate-form.json',
	array('_controller' => 'CloudAdminBundle:Agents:quickEditFormValidate'),
	array('person_id' => '\\d+'),
	array()
));

$collection->add('admin_agents_setvacation', new Route(
	'/agents/{person_id}/set-vacation-mode/{set_to}',
	array('_controller' => 'CloudAdminBundle:Agents:setVacationMode', 'set_to' => '0'),
	array('person_id' => '\\d+', 'set_to' => '(1|0)'),
	array()
));

$collection->add('admin_agents_setdeleted', new Route(
	'/agents/{person_id}/set-deleted/{set_to}',
	array('_controller' => 'CloudAdminBundle:Agents:setDeleted', 'set_to' => '0'),
	array('person_id' => '\\d+', 'set_to' => '(1|0)'),
	array()
));

$collection->add('admin_agents_getperms', new Route(
	'/agents/{person_id}/get-perms.json',
	array('_controller' => 'CloudAdminBundle:Agents:getAgentPermissions'),
	array('person_id' => '\\d+'),
	array()
));

$collection->add('admin_agents_edit_save', new Route(
	'/agents/{person_id}/edit/save',
	array('_controller' => 'CloudAdminBundle:Agents:editAgentSave'),
	array('person_id' => '\\d+'),
	array()
));

$collection->add('admin_agents_teams_edit', new Route(
	'/agents/teams/{team_id}/edit',
	array('_controller' => 'CloudAdminBundle:Agents:editTeam'),
	array('team_id' => '\\d+'),
	array()
));

$collection->add('admin_agents_teams_del', new Route(
	'/agents/teams/{team_id}/delete/{security_token}',
	array('_controller' => 'CloudAdminBundle:Agents:deleteTeam'),
	array('team_id' => '\\d+'),
	array()
));

$collection->add('admin_agents_teams_new', new Route(
	'/agents/teams/new',
	array('_controller' => 'CloudAdminBundle:Agents:editTeam', 'team_id' => 0),
	array(),
	array()
));

$collection->add('admin_agents_groups_edit', new Route(
	'/agents/groups/{usergroup_id}/edit',
	array('_controller' => 'CloudAdminBundle:Agents:editGroup'),
	array('usergroup_id' => '\\d+'),
	array()
));

$collection->add('admin_agents_groups_del', new Route(
	'/agents/groups/{usergroup_id}/delete/{security_token}',
	array('_controller' => 'CloudAdminBundle:Agents:deleteGroup'),
	array('usergroup_id' => '\\d+'),
	array()
));

$collection->add('admin_agents_groups_new', new Route(
	'/agents/groups/new',
	array('_controller' => 'CloudAdminBundle:Agents:editGroup', 'usergroup_id' => 0),
	array(),
	array()
));

################################################################################
# Twitter override
################################################################################

$collection->add('admin_twitter_accounts_new', new Route(
	'/twitter/accounts/new',
	array('_controller' => 'CloudAdminBundle:TwitterAccount:new'),
	array(),
	array()
));

$collection->add('admin_twitter_accounts_delete', new Route(
	'/twitter/accounts/{account_id}/delete/{security_token}',
	array('_controller' => 'CloudAdminBundle:TwitterAccount:delete'),
	array(),
	array()
));

return $collection;