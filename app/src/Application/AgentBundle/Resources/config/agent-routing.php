<?php if (!defined('DP_ROOT')) exit('No access');

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

################################################################################
# Misc
################################################################################

$collection->add('agent', new Route(
	'/',
	array('_controller' => 'AgentBundle:Main:index'),
	array(),
	array()
));

$collection->add('agent_savedom', new Route(
	'/save-dom.json',
	array('_controller' => 'AgentBundle:Misc:saveDom'),
	array(),
	array()
));

$collection->add('agent_combined_sectiondata', new Route(
	'/get-combined-section-data.json',
	array('_controller' => 'AgentBundle:Main:getCombinedSectionData'),
	array(),
	array()
));

$collection->add('agent_load_recent_tabs', new Route(
	'/ui/load-recent-tabs.json',
	array('_controller' => 'AgentBundle:Main:loadRecentTabs'),
	array(),
	array()
));

$collection->add('agent_accept_upload', new Route(
	'/misc/accept-upload',
	array('_controller' => 'AgentBundle:Misc:acceptTempUpload'),
	array(),
	array()
));

$collection->add('agent_accept_redactor_image_upload', new Route(
	'/misc/accept-redactor-image-upload',
	array('_controller' => 'AgentBundle:Misc:acceptRedactorImageUpload'),
	array(),
	array()
));

$collection->add('agent_redactor_autosave', new Route(
	'/misc/redactor-autosave/{content_type}/{content_id}',
	array('_controller' => 'AgentBundle:Misc:redactorAutosave'),
	array('content_id' => '\\d+'),
	array()
));

$collection->add('agent_submit_deskpro_feedback', new Route(
	'/misc/submit-deskpro-feedback.json',
	array('_controller' => 'AgentBundle:Misc:submitDeskproFeedback'),
	array(),
	array()
));

$collection->add('agent_parse_vcard', new Route(
    '/misc/parse-vcard',
    array('_controller' => 'AgentBundle:Misc:parseVCard'),
    array(),
    array()
));

$collection->add('agent_get_server_time', new Route(
    '/misc/get-server-time',
    array('_controller' => 'AgentBundle:Misc:getServerTime'),
    array(),
    array()
));

$collection->add('agent_ajax_save_prefs', new Route(
	'/misc/ajax-save-prefs',
	array('_controller' => 'AgentBundle:Misc:ajaxSavePrefs'),
	array(),
	array()
));

$collection->add('agent_ajax_labels_autocomplete', new Route(
	'/misc/ajax-labels/{label_type}',
	array('_controller' => 'AgentBundle:Misc:ajaxLabelsAutocomplete'),
	array('label_type' => '[a-z]+'),
	array()
));

$collection->add('agent_interface_data_js', new Route(
	'/misc/interface-data.js',
	array('_controller' => 'AgentBundle:Misc:getInterfaceData'),
	array(),
	array()
));

$collection->add('agent_dismiss_help_message', new Route(
	'/misc/dismiss-help-message/{id}',
	array('_controller' => 'AgentBundle:Misc:dismissHelpMessage'),
	array(),
	array()
));

$collection->add('agent_set_agent_status', new Route(
	'/misc/set-agent-status/{status}',
	array('_controller' => 'AgentBundle:Misc:setAgentStatus'),
	array(),
	array()
));

$collection->add('agent_proxy', new Route(
	'/misc/proxy',
	array('_controller' => 'AgentBundle:Misc:proxy'),
	array(),
	array()
));

$collection->add('agent_load_version_notice', new Route(
	'/misc/version-notices/{id}/log.html',
	array('_controller' => 'AgentBundle:Main:loadVersionNotice'),
	array(),
	array()
));

$collection->add('agent_dismiss_version_notice', new Route(
	'/misc/version-notices/{id}/dismiss.json',
	array('_controller' => 'AgentBundle:Main:dismissVersionNotice'),
	array(),
	array()
));

$collection->add('agent_redirect_out', new Route(
	'/redirect-out/{url}',
	array('_controller' => 'AgentBundle:Misc:redirectExternal'),
	array('url' => '.+'),
	array()
));

$collection->add('agent_redirect_out_info', new Route(
	'/redirect-out-info/{url}',
	array('_controller' => 'AgentBundle:Misc:redirectExternalInfo'),
	array('url' => '.+'),
	array()
));

$collection->add('agent_password_confirm_code', new Route(
	'/password-confirm-code.json',
	array('_controller' => 'AgentBundle:Misc:getPasswordConfirmCode'),
	array(),
	array()
));

$collection->add('agent_quicksearch', new Route(
	'/quick-search.json',
	array('_controller' => 'AgentBundle:Main:quickSearch'),
	array(),
	array()
));

$collection->add('agent_search', new Route(
	'/search/search',
	array('_controller' => 'AgentBundle:Search:search'),
	array(),
	array()
));

$collection->add('agent_search_json', new Route(
	'/search/search.json',
	array('_controller' => 'AgentBundle:Search:searchResults'),
	array(),
	array()
));


$collection->add('agent_recyclebin', new Route(
	'/recycle-bin',
	array('_controller' => 'AgentBundle:RecycleBin:list'),
	array(),
	array('fragment_name' => 'recycle-bin', 'fragment_type' => 'list')
));

$collection->add('agent_recyclebin_more', new Route(
	'/recycle-bin/{type}/{page}',
	array('_controller' => 'AgentBundle:RecycleBin:listMore'),
	array(),
	array()
));



################################################################################
# Login
################################################################################

$collection->add('agent_login_preload_sources', new Route(
	'/login/preload-sources',
	array('_controller' => 'AgentBundle:Login:preloadSources'),
	array(),
	array()
));

$collection->add('agent_browser_requirements', new Route(
	'/browser-requirements',
	array('_controller' => 'AgentBundle:Login:browserRequirements'),
	array(),
	array()
));

$collection->add('agent_browser_requirements_ie_compat', new Route(
	'/browser-requirements/ie-compat-mode',
	array('_controller' => 'AgentBundle:Login:ieCompatMode'),
	array(),
	array()
));

$collection->add('agent_login', new Route(
	'/login',
	array('_controller' => 'AgentBundle:Login:index'),
	array(),
	array()
));

$collection->add('agent_login_authenticate_local', new Route(
	'/login/authenticate-password',
	array('_controller' => 'AgentBundle:Login:authenticateLocal', 'usersource_id' => 0),
	array(),
	array()
));

$collection->add('agent_login_adminlogin', new Route(
	'/login/admin-login/{code}',
	array('_controller' => 'AgentBundle:Login:authAdminLogin'),
	array(),
	array()
));


$collection->add('agent_send_lost', new Route(
	'/login/send-lost.json',
	array('_controller' => 'AgentBundle:Login:sendResetPassword', '_format' => 'json'),
	array(),
	array()
));


################################################################################
# Settings
################################################################################

$collection->add('agent_settings', new Route(
	'/settings',
	array('_controller' => 'AgentBundle:Settings:profile'),
	array(),
	array()
));

$collection->add('agent_settings_profile_save', new Route(
	'/settings/profile/save.json',
	array('_controller' => 'AgentBundle:Settings:profileSave'),
	array(),
	array()
));

$collection->add('agent_settings_profile_savewelcome', new Route(
	'/settings/profile/save-welcome.json',
	array('_controller' => 'AgentBundle:Settings:profileSaveWelcome'),
	array(),
	array()
));

$collection->add('agent_settings_signature', new Route(
	'/settings/signature',
	array('_controller' => 'AgentBundle:Settings:signature'),
	array(),
	array()
));

$collection->add('agent_settings_signature_save', new Route(
	'/settings/signature/save.json',
	array('_controller' => 'AgentBundle:Settings:signatureSave'),
	array(),
	array()
));

$collection->add('agent_settings_profile_updatetimezone', new Route(
	'/settings/profile/update-timezone.json',
	array('_controller' => 'AgentBundle:Settings:updateTimezone'),
	array(),
	array()
));

$collection->add('agent_settings_ticketnotif', new Route(
	'/settings/ticket-notifications',
	array('_controller' => 'AgentBundle:Settings:ticketNotifications'),
	array(),
	array()
));

$collection->add('agent_settings_ticketnotif_save', new Route(
	'/settings/ticket-notifications/save.json',
	array('_controller' => 'AgentBundle:Settings:ticketNotificationsSave'),
	array(),
	array()
));

$collection->add('agent_settings_othernotif', new Route(
	'/settings/other-notifications',
	array('_controller' => 'AgentBundle:Settings:otherNotifications'),
	array(),
	array()
));

$collection->add('agent_settings_othernotif_save', new Route(
	'/settings/other-notifications/save.json',
	array('_controller' => 'AgentBundle:Settings:otherNotificationsSave'),
	array(),
	array()
));

$collection->add('agent_settings_ticketmacros', new Route(
	'/settings/ticket-macros',
	array('_controller' => 'AgentBundle:Settings:ticketMacros'),
	array(),
	array()
));

$collection->add('agent_settings_ticketmacros_edit', new Route(
	'/settings/ticket-macros/{macro_id}/edit',
	array('_controller' => 'AgentBundle:Settings:ticketMacroEdit'),
	array('macro_id' => '\\d+'),
	array()
));

$collection->add('agent_settings_ticketmacros_edit_save', new Route(
	'/settings/ticket-macros/{macro_id}/save',
	array('_controller' => 'AgentBundle:Settings:ticketMacroEditSave'),
	array('macro_id' => '\\d+'),
	array()
));

$collection->add('agent_settings_ticketmacros_new', new Route(
	'/settings/ticket-macros/new',
	array('_controller' => 'AgentBundle:Settings:ticketMacroEdit', 'macro_id' => 0),
	array(),
	array()
));

$collection->add('agent_settings_ticketmacros_del', new Route(
	'/settings/ticket-macros/{macro_id}/delete',
	array('_controller' => 'AgentBundle:Settings:ticketMacroDelete'),
	array('macro_id' => '\\d+'),
	array()
));

$collection->add('agent_settings_ticketfilters', new Route(
	'/settings/ticket-filters',
	array('_controller' => 'AgentBundle:Settings:ticketFilters'),
	array(),
	array()
));

$collection->add('agent_settings_ticketfilters_edit', new Route(
	'/settings/ticket-filters/{filter_id}/edit',
	array('_controller' => 'AgentBundle:Settings:ticketFilterEdit'),
	array('filter_id' => '\\d+'),
	array()
));

$collection->add('agent_settings_ticketfilters_edit_save', new Route(
	'/settings/ticket-filters/{filter_id}/edit/save',
	array('_controller' => 'AgentBundle:Settings:ticketFilterEditSave'),
	array('filter_id' => '\\d+'),
	array()
));

$collection->add('agent_settings_ticketfilters_del', new Route(
	'/settings/ticket-filters/{filter_id}/delete',
	array('_controller' => 'AgentBundle:Settings:ticketFilterDelete'),
	array('filter_id' => '\\d+'),
	array()
));

$collection->add('agent_settings_ticketfilters_new', new Route(
	'/settings/ticket-filters/new-filter',
	array('_controller' => 'AgentBundle:Settings:ticketFilterEdit', 'filter_id' => 0),
	array(),
	array()
));

$collection->add('agent_settings_ticketslas', new Route(
	'/settings/ticket-slas',
	array('_controller' => 'AgentBundle:Settings:ticketSlas'),
	array(),
	array()
));

################################################################################
# People and People Search
################################################################################

$collection->add('agent_people_validate_email', new Route(
	'/people/validate-email/{id}/{security_token}',
	array('_controller' => 'AgentBundle:Person:validateEmailAddress'),
	array('id' => '\\d+'),
	array()
));

$collection->add('agent_people_view', new Route(
	'/people/{person_id}',
	array('_controller' => 'AgentBundle:Person:view'),
	array('person_id' => '\\d+'),
	array('fragment_name' => 'p')
));

$collection->add('agent_people_view_basicjson', new Route(
	'/people/{person_id}/basic.json',
	array('_controller' => 'AgentBundle:Person:getBasicInfo'),
	array('person_id' => '\\d+'),
	array()
));

$collection->add('agent_people_viewsession', new Route(
	'/people/session/{session_id}',
	array('_controller' => 'AgentBundle:Person:viewSession'),
	array('session_id' => '\\d+'),
	array()
));

$collection->add('agent_people_validate_list', new Route(
	'/people/validate/list',
	array('_controller' => 'AgentBundle:PeopleSearch:validateList'),
	array(),
	array()
));

$collection->add('agent_people_validate_approve', new Route(
	'/people/validate/approve',
	array('_controller' => 'AgentBundle:PeopleSearch:validateApprove'),
	array(),
	array()
));

$collection->add('agent_people_validate_delete', new Route(
	'/people/validate/delete',
	array('_controller' => 'AgentBundle:PeopleSearch:validateDelete'),
	array(),
	array()
));

$collection->add('agent_people_new', new Route(
	'/people/new',
	array('_controller' => 'AgentBundle:Person:newPerson'),
	array(),
	array()
));

$collection->add('agent_people_new_save', new Route(
	'/people/new/save',
	array('_controller' => 'AgentBundle:Person:newPersonSave'),
	array(),
	array()
));

$collection->add('agent_people_ajaxsave', new Route(
	'/people/{person_id}/ajax-save',
	array('_controller' => 'AgentBundle:Person:ajaxSave'),
	array('person_id' => '\\d+'),
	array()
));

$collection->add('agent_people_savecontactdata', new Route(
	'/people/{person_id}/save-contact-data.json',
	array('_controller' => 'AgentBundle:Person:saveContactData'),
	array(),
	array()
));

$collection->add('agent_people_unban_email', new Route(
	'/people/{person_id}/unban-email/{email_id}.json',
	array('_controller' => 'AgentBundle:Person:unbanEmail'),
	array(),
	array()
));

$collection->add('agent_people_merge_overlay', new Route(
	'/people/{person_id}/merge-overlay/{other_person_id}',
	array('_controller' => 'AgentBundle:Person:mergeOverlay'),
	array('person_id' => '\\d+', 'other_person_id' => '\\d+'),
	array()
));

$collection->add('agent_people_merge', new Route(
	'/people/{person_id}/merge/{other_person_id}',
	array('_controller' => 'AgentBundle:Person:merge'),
	array('person_id' => '\\d+', 'other_person_id' => '\\d+'),
	array()
));

$collection->add('agent_people_delete', new Route(
	'/people/{person_id}/delete/{security_token}',
	array('_controller' => 'AgentBundle:Person:deletePerson'),
	array(),
	array()
));

$collection->add('agent_people_login_as', new Route(
	'/people/{person_id}/login-as',
	array('_controller' => 'AgentBundle:Person:loginAs'),
	array(),
	array()
));

$collection->add('agent_people_changepicoverlay', new Route(
	'/people/{person_id}/change-picture-overlay',
	array('_controller' => 'AgentBundle:Person:changePictureOverlay'),
	array('person_id' => '\\d+'),
	array()
));

$collection->add('agent_people_ajaxsave_note', new Route(
	'/people/{person_id}/ajax-save-note',
	array('_controller' => 'AgentBundle:Person:ajaxSaveNote'),
	array('person_id' => '\\d+'),
	array()
));

$collection->add('agent_people_ajaxsave_organization', new Route(
	'/people/{person_id}/ajax-save-organization',
	array('_controller' => 'AgentBundle:Person:ajaxSaveOrganization'),
	array('person_id' => '\\d+'),
	array()
));

$collection->add('agent_person_ajax_labels_save', new Route(
	'/person/{person_id}/ajax-save-labels',
	array('_controller' => 'AgentBundle:Person:ajaxSaveLabels'),
	array('person_id' => '\\d+'),
	array()
));

$collection->add('agent_person_ajaxsavecustomfields', new Route(
	'/person/{person_id}/ajax-save-custom-fields',
	array('_controller' => 'AgentBundle:Person:ajaxSaveCustomFields'),
	array('person_id' => '\\d+'),
	array()
));

$collection->add('agent_peoplesearch_usergroup', new Route(
	'/people-search/usergroup/{id}',
	array('_controller' => 'AgentBundle:PeopleSearch:showUsergroup'),
	array(),
	array('fragment_name' => 'usergroup', 'fragment_type' => 'list')
));

$collection->add('agent_peoplesearch_organization', new Route(
	'/people-search/organization/{id}',
	array('_controller' => 'AgentBundle:PeopleSearch:showOrganizationMembers'),
	array(),
	array('fragment_name' => 'organization-members', 'fragment_type' => 'list')
));


$collection->add('agent_peoplesearch_customfilter', new Route(
	'/people-search/search/{letter}',
	array('_controller' => 'AgentBundle:PeopleSearch:search', 'letter' => '*'),
	array(),
	array('fragment_name' => 'people', 'fragment_type' => 'list')
));

$collection->add('agent_peoplesearch_getpage', new Route(
	'/people-search/get-page',
	array('_controller' => 'AgentBundle:PeopleSearch:getPeoplePage'),
	array(),
	array()
));

$collection->add('agent_peoplesearch_performquick', new Route(
	'/people-search/search-quick',
	array('_controller' => 'AgentBundle:PeopleSearch:performQuickSearch'),
	array(),
	array()
));

$collection->add('agent_peoplesearch_quickfind', new Route(
	'/people-search/quick-find',
	array('_controller' => 'AgentBundle:PeopleSearch:quickFind'),
	array(),
	array()
));

$collection->add('agent_peoplesearch_quickfind_search', new Route(
	'/people-search/quick-find-search.json',
	array('_controller' => 'AgentBundle:PeopleSearch:quickFindSearch'),
	array(),
	array()
));

$collection->add('agent_peoplesearch_getsectiondata', new Route(
	'/people/get-section-data.json',
	array('_controller' => 'AgentBundle:PeopleSearch:getSectionData'),
	array(),
	array()
));

$collection->add('agent_peoplesearch_getsectiondata_reloadcounts', new Route(
	'/people/get-section-data/reload-counts.json',
	array('_controller' => 'AgentBundle:PeopleSearch:reloadCounts'),
	array(),
	array()
));

$collection->add('agent_peoplesearch_reload_label_sectiondata', new Route(
	'/people/get-section-data/labels.json',
	array('_controller' => 'AgentBundle:PeopleSearch:reloadLabelData'),
	array(),
	array()
));


################################################################################
# Organizations and Organizations Search
################################################################################

$collection->add('agent_org_view', new Route(
	'/organizations/{organization_id}',
	array('_controller' => 'AgentBundle:Organization:view'),
	array('organization_id' => '\\d+'),
	array('fragment_name' => 'o')
));

$collection->add('agent_org_new', new Route(
	'/organizations/new',
	array('_controller' => 'AgentBundle:Organization:newOrganization'),
	array(),
	array()
));

$collection->add('agent_org_new_save', new Route(
	'/organizations/new/save',
	array('_controller' => 'AgentBundle:Organization:newOrganizationSave'),
	array(),
	array()
));

$collection->add('agent_org_ajaxsave', new Route(
	'/organizations/{organization_id}/ajax-save',
	array('_controller' => 'AgentBundle:Organization:ajaxSave'),
	array('organization_id' => '\\d+'),
	array()
));

$collection->add('agent_org_domain_assign', new Route(
	'/organizations/{organization_id}/assign-domain',
	array('_controller' => 'AgentBundle:Organization:assignDomain'),
	array('organization_id' => '\\d+'),
	array()
));

$collection->add('agent_org_domain_unassign', new Route(
	'/organizations/{organization_id}/unassign-domain',
	array('_controller' => 'AgentBundle:Organization:unassignDomain'),
	array('organization_id' => '\\d+'),
	array()
));

$collection->add('agent_org_domain_moveusers', new Route(
	'/organizations/{organization_id}/domain/move-users',
	array('_controller' => 'AgentBundle:Organization:moveDomainUsers'),
	array('organization_id' => '\\d+'),
	array()
));

$collection->add('agent_org_domain_moveusers_exist', new Route(
	'/organizations/{organization_id}/domain/reassign-users',
	array('_controller' => 'AgentBundle:Organization:moveTakenDomainUsers'),
	array('organization_id' => '\\d+'),
	array()
));

$collection->add('agent_org_save_member_pos', new Route(
	'/organizations/{organization_id}/save-member-pos/{person_id}',
	array('_controller' => 'AgentBundle:Organization:savePosition'),
	array('organization_id' => '\\d+'),
	array()
));

$collection->add('agent_org_save_member_manager', new Route(
	'/organizations/{organization_id}/save-member-manager/{person_id}',
	array('_controller' => 'AgentBundle:Organization:saveManager'),
	array('organization_id' => '\\d+'),
	array()
));

$collection->add('agent_org_savecontactdata', new Route(
	'/organizations/{organization_id}/save-contact-data.json',
	array('_controller' => 'AgentBundle:Organization:saveContactData'),
	array('person_id' => '\\d+'),
	array()
));

$collection->add('agent_org_delete', new Route(
	'/organizations/{organization_id}/delete/{security_token}',
	array('_controller' => 'AgentBundle:Organization:deleteOrganization'),
	array(),
	array()
));

$collection->add('agent_org_ajaxsave_note', new Route(
	'/organizations/{organization_id}/ajax-save-note',
	array('_controller' => 'AgentBundle:Organization:ajaxSaveNote'),
	array('organization_id' => '\\d+'),
	array()
));

$collection->add('agent_org_ajax_labels_save', new Route(
	'/organizations/{organization_id}/ajax-save-labels',
	array('_controller' => 'AgentBundle:Organization:ajaxSaveLabels'),
	array('organization_id' => '\\d+'),
	array()
));

$collection->add('agent_org_ajaxsavecustomfields', new Route(
	'/organizations/{organization_id}/ajax-save-custom-fields',
	array('_controller' => 'AgentBundle:Organization:ajaxSaveCustomFields'),
	array('organization_id' => '\\d+'),
	array()
));

$collection->add('agent_org_changepicoverlay', new Route(
	'/organizations/{organization_id}/change-picture-overlay',
	array('_controller' => 'AgentBundle:Organization:changePictureOverlay'),
	array('organization_id' => '\\d+'),
	array()
));

$collection->add('agent_orgsearch_getpage', new Route(
	'/organization-search/get-page',
	array('_controller' => 'AgentBundle:OrganizationSearch:getOrgPage'),
	array(),
	array()
));

$collection->add('agent_orgsearch_customfilter', new Route(
	'/organization-search/search',
	array('_controller' => 'AgentBundle:OrganizationSearch:search'),
	array(),
	array('fragment_name' => 'orgs', 'fragment_type' => 'list')
));

$collection->add('agent_orgsearch_quicknamesearch', new Route(
	'/organization-search/quick-name-search.json',
	array('_controller' => 'AgentBundle:OrganizationSearch:performQuickNameSearch'),
	array(),
	array()
));

$collection->add('agent_orgsearch_namelookup', new Route(
	'/organization-search/name-lookup.json',
	array('_controller' => 'AgentBundle:OrganizationSearch:checkName'),
	array(),
	array()
));


################################################################################
# Tickets and Ticket Search
################################################################################

$collection->add('agent_ticketsearch_getsectiondata', new Route(
	'/ticket-search/get-section-data.json',
	array('_controller' => 'AgentBundle:TicketSearch:getSectionData'),
	array(),
	array()
));

$collection->add('agent_ticketsearch_getsection_reloadarchive', new Route(
	'/ticket-search/get-section-data/reload-archive-section',
	array('_controller' => 'AgentBundle:TicketSearch:reloadArchiveSection'),
	array(),
	array()
));

$collection->add('agent_ticketsearch_refreshsectiondata', new Route(
	'/ticket-search/refresh-section-data/{section}.json',
	array('_controller' => 'AgentBundle:TicketSearch:refreshSectionData'),
	array(),
	array()
));

$collection->add('agent_ticketsearch_getlabelssection', new Route(
	'/ticket-search/get-section/labels',
	array('_controller' => 'AgentBundle:TicketSearch:getLabelsSection'),
	array(),
	array()
));

$collection->add('agent_ticketsearch_getfiltercounts', new Route(
	'/ticket-search/get-filter-counts.json',
	array('_controller' => 'AgentBundle:TicketSearch:getFilterCounts'),
	array(),
	array()
));

$collection->add('agent_ticketsearch_getslacounts', new Route(
	'/ticket-search/get-sla-counts.json',
	array('_controller' => 'AgentBundle:TicketSearch:getSlaCounts'),
	array(),
	array()
));

$collection->add('agent_ticketsearch_grouptickets', new Route(
	'/ticket-search/group-tickets.json',
	array('_controller' => 'AgentBundle:TicketSearch:groupTickets'),
	array(),
	array()
));

$collection->add('agent_ticketsearch_getpage', new Route(
	'/ticket-search/get-page',
	array('_controller' => 'AgentBundle:TicketSearch:getTicketPage'),
	array(),
	array()
));

$collection->add('agent_ticketsearch_getflaggedsectiondata', new Route(
	'/tickets/get-flagged-section-data.json',
	array('_controller' => 'AgentBundle:TicketSearch:getFlaggedSectionData'),
	array(),
	array()
));

$collection->add('agent_ticketsearch_runcustomfilter', new Route(
	'/ticket-search/custom-filter/run',
	array('_controller' => 'AgentBundle:TicketSearch:runCustomFilter'),
	array(),
	array()
));

$collection->add('agent_ticketsearch_quicksearch', new Route(
	'/ticket-search/quick-search',
	array('_controller' => 'AgentBundle:TicketSearch:quickSearch'),
	array(),
	array()
));

$collection->add('agent_ticketsearch_singleticketrow', new Route(
	'/ticket-search/single-ticket-row/{content_type}/{content_id}',
	array('_controller' => 'AgentBundle:TicketSearch:getSingleTicketRow'),
	array('content_id' => '\\d+'),
	array()
));

$collection->add('agent_ticketsearch_runfilter', new Route(
	'/ticket-search/filter/{filter_id}',
	array('_controller' => 'AgentBundle:TicketSearch:runFilter'),
	array('filter_id' => '\\d+'),
	array('fragment_name' => 'filter', 'fragment_type' => 'list')
));

$collection->add('agent_ticketsearch_runnamedfilter', new Route(
	'/ticket-search/filter/{filter_name}',
	array('_controller' => 'AgentBundle:TicketSearch:runNamedFilter'),
	array(),
	array('fragment_name' => 'inbox', 'fragment_type' => 'list')
));

$collection->add('agent_ticketsearch_runsla', new Route(
	'/ticket-search/sla/{sla_id}/{sla_status}',
	array('_controller' => 'AgentBundle:TicketSearch:runSla', 'sla_status' => ''),
	array('sla_id' => '\\d+'),
	array('fragment_name' => 'sla', 'fragment_type' => 'list')
));

$collection->add('agent_ticketsearch_ajax_get_macro', new Route(
	'/ticket-search/ajax-get-macro',
	array('_controller' => 'AgentBundle:TicketSearch:ajaxGetMacro'),
	array(),
	array()
));

$collection->add('agent_ticketsearch_ajax_get_macro_actions', new Route(
	'/ticket-search/ajax-get-macro-actions',
	array('_controller' => 'AgentBundle:TicketSearch:ajaxGetMacroActions'),
	array(),
	array()
));

$collection->add('agent_ticketsearch_ajax_save_actions', new Route(
	'/ticket-search/ajax-save-actions',
	array('_controller' => 'AgentBundle:TicketSearch:ajaxSaveActions'),
	array(),
	array()
));

$collection->add('agent_ticketsearch_ajax_delete_tickets', new Route(
	'/ticket-search/ajax-delete-tickets',
	array('_controller' => 'AgentBundle:TicketSearch:ajaxDeleteTickets'),
	array(),
	array()
));

$collection->add('agent_ticketsearch_ajax_release_locks', new Route(
	'/ticket-search/ajax-release-locks',
	array('_controller' => 'AgentBundle:TicketSearch:ajaxReleaseLocks'),
	array(),
	array()
));

$collection->add('agent_ticket_new', new Route(
	'/tickets/new',
	array('_controller' => 'AgentBundle:Ticket:new'),
	array(),
	array('fragment_name' => 'nt')
));

$collection->add('agent_ticket_new_save', new Route(
	'/tickets/new/save',
	array('_controller' => 'AgentBundle:Ticket:newSave'),
	array(),
	array()
));

$collection->add('agent_ticket_new_getpersonrow', new Route(
	'/tickets/new/get-person-row/{person_id}',
	array('_controller' => 'AgentBundle:Ticket:newticketGetPersonRow'),
	array(),
	array()
));

$collection->add('agent_ticket_getmessagetpl', new Route(
	'/tickets/get-message-template/{id}.json',
	array('_controller' => 'AgentBundle:Ticket:getTicketMessageTemplate'),
	array(),
	array()
));

$collection->add('agent_ticket_update_drafts', new Route(
	'/tickets/update-drafts',
	array('_controller' => 'AgentBundle:Ticket:updateDrafts'),
	array(),
	array()
));

$collection->add('agent_ticket_getmessagetext', new Route(
	'/tickets/messages/{message_id}/get-message-text.json',
	array('_controller' => 'AgentBundle:Ticket:ajaxGetMessageText'),
	array(),
	array()
));

$collection->add('agent_ticket_getfullmessage', new Route(
	'/tickets/messages/{message_id}/get-full-message.json',
	array('_controller' => 'AgentBundle:Ticket:ajaxGetFullMessage'),
	array(),
	array()
));

$collection->add('agent_ticket_savemessagetext', new Route(
	'/tickets/messages/{message_id}/save-message-text.json',
	array('_controller' => 'AgentBundle:Ticket:ajaxSaveMessageText'),
	array(),
	array()
));

$collection->add('agent_ticket_setmessagenote', new Route(
	'/tickets/messages/{message_id}/set-message-note.json',
	array('_controller' => 'AgentBundle:Ticket:ajaxSetNote'),
	array(),
	array()
));

$collection->add('agent_ticket_message_attachments', new Route(
	'/tickets/messages/{message_id}/attachments',
	array('_controller' => 'AgentBundle:Ticket:getMessageAttachments'),
	array(),
	array()
));

$collection->add('agent_ticket_message_attachment_delete', new Route(
	'/tickets/messages/{message_id}/attachments/{attachment_id}/delete',
	array('_controller' => 'AgentBundle:Ticket:deleteMessageAttachment'),
	array('_method' => 'POST'),
	array()
));

$collection->add('agent_ticket_message_delete', new Route(
	'/tickets/messages/{message_id}/delete',
	array('_controller' => 'AgentBundle:Ticket:deleteMessage'),
	array('_method' => 'POST'),
	array()
));

$collection->add('agent_ticket_view', new Route(
	'/tickets/{ticket_id}',
	array('_controller' => 'AgentBundle:Ticket:view'),
	array('ticket_id' => '\\d+'),
	array('fragment_name' => 't')
));

$collection->add('agent_ticket_loadlogs', new Route(
	'/tickets/{ticket_id}/load-logs',
	array('_controller' => 'AgentBundle:Ticket:loadTicketLogs'),
	array('ticket_id' => '\\d+')
));

$collection->add('agent_ticket_loadattachlist', new Route(
	'/tickets/{ticket_id}/load-attach-list',
	array('_controller' => 'AgentBundle:Ticket:loadAttachList'),
	array('ticket_id' => '\\d+')
));

$collection->add('agent_ticket_download_debug_report', new Route(
	'/tickets/{ticket_id}/download-debug-report',
	array('_controller' => 'AgentBundle:Ticket:downloadTicketDebug'),
	array('ticket_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_messagepage', new Route(
	'/tickets/{ticket_id}/message-page/{page}',
	array('_controller' => 'AgentBundle:Ticket:getMessagePage'),
	array('ticket_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_doupdate', new Route(
	'/tickets/{ticket_id}/update-views.json',
	array('_controller' => 'AgentBundle:Ticket:updateViews'),
	array('ticket_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_lock', new Route(
	'/tickets/{ticket_id}/lock-ticket.json',
	array('_controller' => 'AgentBundle:Ticket:lockTicket'),
	array('ticket_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_unlock', new Route(
	'/tickets/{ticket_id}/unlock-ticket.json',
	array('_controller' => 'AgentBundle:Ticket:unlockTicket'),
	array('ticket_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_release_lock', new Route(
	'/tickets/{ticket_id}/release-lock.json',
	array('_controller' => 'AgentBundle:Ticket:releaseLock'),
	array('ticket_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_split', new Route(
	'/tickets/{ticket_id}/split/{message_id}',
	array('_controller' => 'AgentBundle:Ticket:split', 'message_id' => 0),
	array('ticket_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_split_save', new Route(
	'/tickets/{ticket_id}/split-save',
	array('_controller' => 'AgentBundle:Ticket:splitSave'),
	array('ticket_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_merge_overlay', new Route(
	'/tickets/{ticket_id}/merge-overlay/{other_ticket_id}',
	array('_controller' => 'AgentBundle:Ticket:mergeOverlay'),
	array('ticket_id' => '\\d+', 'other_ticket_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_fwd_overlay', new Route(
	'/tickets/{ticket_id}/forward/{message_id}',
	array('_controller' => 'AgentBundle:Ticket:forwardOverlay'),
	array('ticket_id' => '\\d+', 'message_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_fwd_send', new Route(
	'/tickets/{ticket_id}/forward/{message_id}/send',
	array('_controller' => 'AgentBundle:Ticket:forwardSend'),
	array('ticket_id' => '\\d+', 'message_id' => '\\d+', '_method' => 'POST'),
	array()
));

$collection->add('agent_ticket_merge', new Route(
	'/tickets/{ticket_id}/merge/{other_ticket_id}',
	array('_controller' => 'AgentBundle:Ticket:merge'),
	array('ticket_id' => '\\d+', 'other_ticket_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_changeuser_overlay', new Route(
	'/tickets/{ticket_id}/change-user-overlay',
	array('_controller' => 'AgentBundle:Ticket:changeUserOverlay'),
	array('ticket_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_changeuser_overlay_preview', new Route(
	'/tickets/{ticket_id}/change-user-overlay/preview/{new_person_id}',
	array('_controller' => 'AgentBundle:Ticket:changeUserOverlayPreview'),
	array('ticket_id' => '\\d+', 'new_person_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_changeuser', new Route(
	'/tickets/{ticket_id}/change-user',
	array('_controller' => 'AgentBundle:Ticket:changeUser'),
	array('ticket_id' => '\\d+', 'new_person_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_ajaxsavecustomfields', new Route(
	'/tickets/{ticket_id}/ajax-save-custom-fields',
	array('_controller' => 'AgentBundle:Ticket:ajaxSaveCustomFields'),
	array('ticket_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_ajaxsavereply', new Route(
	'/tickets/{ticket_id}/ajax-save-reply',
	array('_controller' => 'AgentBundle:Ticket:ajaxSaveReply'),
	array('ticket_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_ajaxsavesubject', new Route(
	'/tickets/{ticket_id}/ajax-save-subject.json',
	array('_controller' => 'AgentBundle:Ticket:ajaxSaveSubject'),
	array('ticket_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_ajaxchangeuseremail', new Route(
	'/tickets/{ticket_id}/ajax-change-email.json',
	array('_controller' => 'AgentBundle:Ticket:ajaxChangeUserEmail'),
	array('ticket_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_ajaxsaveoptions', new Route(
	'/tickets/{ticket_id}/ajax-save-options',
	array('_controller' => 'AgentBundle:Ticket:ajaxSaveOptions'),
	array('ticket_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_ajaxsaveflagged', new Route(
	'/tickets/{ticket_id}/ajax-save-flagged',
	array('_controller' => 'AgentBundle:Ticket:ajaxSaveFlagged'),
	array('ticket_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_addpart', new Route(
	'/tickets/{ticket_id}/add-part',
	array('_controller' => 'AgentBundle:Ticket:addParticipant'),
	array(),
	array()
));

$collection->add('agent_ticket_set_agent_parts', new Route(
	'/tickets/{ticket_id}/set-agent-parts.json',
	array('_controller' => 'AgentBundle:Ticket:setAgentParticipants'),
	array(),
	array()
));

$collection->add('agent_ticket_delpart', new Route(
	'/tickets/{ticket_id}/remove-part.json',
	array('_controller' => 'AgentBundle:Ticket:removeParticipant'),
	array(),
	array()
));

$collection->add('agent_ticket_ajaxtab_releated_content', new Route(
	'/tickets/{ticket_id}/ajax-tab-related-content',
	array('_controller' => 'AgentBundle:Ticket:ajaxTabRelatedContent'),
	array('ticket_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_ajax_labels_save', new Route(
	'/tickets/{ticket_id}/ajax-save-labels',
	array('_controller' => 'AgentBundle:Ticket:ajaxSaveLabels'),
	array('ticket_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_ajax_get_macro', new Route(
	'/tickets/{ticket_id}/ajax-get-macro',
	array('_controller' => 'AgentBundle:Ticket:ajaxGetMacro'),
	array('ticket_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_ajax_apply_macro', new Route(
	'/tickets/{ticket_id}/{macro_id}/apply-macro.json',
	array('_controller' => 'AgentBundle:Ticket:applyMacro'),
	array('ticket_id' => '\\d+', 'macro_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_ajax_save_actions', new Route(
	'/tickets/{ticket_id}/ajax-save-actions',
	array('_controller' => 'AgentBundle:Ticket:ajaxSaveActions'),
	array('ticket_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_message_raw', new Route(
	'/tickets/{ticket_id}/message-details/{message_id}/view-raw',
	array('_controller' => 'AgentBundle:Ticket:viewRawMessage'),
	array('ticket_id' => '\\d+', 'message_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_message_window', new Route(
	'/tickets/{ticket_id}/message-details/{message_id}/window/{type}',
	array('_controller' => 'AgentBundle:Ticket:viewMessageWindow', 'type' => 'normal'),
	array('ticket_id' => '\\d+', 'message_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_message_ajax_getquote', new Route(
	'/tickets/{ticket_id}/message-details/{message_id}/ajax-get-quote',
	array('_controller' => 'AgentBundle:Ticket:ajaxGetMessageQuote'),
	array('ticket_id' => '\\d+', 'message_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_saveagentparts', new Route(
	'/ticket/{ticket_id}/save-agent-parts',
	array('_controller' => 'AgentBundle:Ticket:saveAgentParts'),
	array('ticket_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_addcharge', new Route(
	'/ticket/{ticket_id}/add-charge',
	array('_controller' => 'AgentBundle:Ticket:addCharge'),
	array('ticket_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_chargedelete', new Route(
	'/ticket/{ticket_id}/charge/{charge_id}/delete/{security_token}',
	array('_controller' => 'AgentBundle:Ticket:deleteCharge'),
	array('ticket_id' => '\\d+', 'charge_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_addsla', new Route(
	'/ticket/{ticket_id}/add-sla',
	array('_controller' => 'AgentBundle:Ticket:addSla'),
	array('ticket_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_sladelete', new Route(
	'/ticket/{ticket_id}/sla/{sla_id}/delete/{security_token}',
	array('_controller' => 'AgentBundle:Ticket:deleteSla'),
	array('ticket_id' => '\\d+', 'sla_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_delete', new Route(
	'/tickets/{ticket_id}/delete',
	array('_controller' => 'AgentBundle:Ticket:delete'),
	array('ticket_id' => '\\d+'),
	array()
));

$collection->add('agent_ticket_spam', new Route(
	'/tickets/{ticket_id}/spam',
	array('_controller' => 'AgentBundle:Ticket:spam'),
	array('ticket_id' => '\\d+'),
	array()
));

################################################################################
# Twitter
################################################################################

$collection->add('agent_twitter_new', new Route(
	'/twitter/new',
	array('_controller' => 'AgentBundle:Twitter:newTweet'),
	array(),
	array()
));

$collection->add('agent_twitter_new_save', new Route(
	'/twitter/new/save',
	array('_controller' => 'AgentBundle:Twitter:newTweetSave'),
	array('_method' => 'POST'),
	array()
));

$collection->add('agent_twitter_mine_list', new Route(
	'/twitter/mine/{account_id}/{group}/{group_value}',
	array('_controller' => 'AgentBundle:TwitterStatus:listMine', 'group' => '', 'group_value' => ''),
	array('account_id' => '\\d+'),
	array('fragment_name' => 'tw-own', 'fragment_type' => 'list')
));

$collection->add('agent_twitter_team_list', new Route(
	'/twitter/team/{account_id}/{group}/{group_value}',
	array('_controller' => 'AgentBundle:TwitterStatus:listTeam', 'group' => '', 'group_value' => ''),
	array('account_id' => '\\d+'),
	array('fragment_name' => 'tw-team', 'fragment_type' => 'list')
));

$collection->add('agent_twitter_unassigned_list', new Route(
	'/twitter/unassigned/{account_id}/{group}/{group_value}',
	array('_controller' => 'AgentBundle:TwitterStatus:listUnassigned', 'group' => '', 'group_value' => ''),
	array('account_id' => '\\d+'),
	array('fragment_name' => 'tw-unassigned', 'fragment_type' => 'list')
));

$collection->add('agent_twitter_all_list', new Route(
	'/twitter/all/{account_id}/{group}/{group_value}',
	array('_controller' => 'AgentBundle:TwitterStatus:listAll', 'group' => '', 'group_value' => ''),
	array('account_id' => '\\d+'),
	array('fragment_name' => 'tw-all', 'fragment_type' => 'list')
));

$collection->add('agent_twitter_sent_list', new Route(
	'/twitter/sent/{account_id}/{group}/{group_value}',
	array('_controller' => 'AgentBundle:TwitterStatus:listSent', 'group' => '', 'group_value' => ''),
	array('account_id' => '\\d+'),
	array('fragment_name' => 'tw-sent', 'fragment_type' => 'list')
));

$collection->add('agent_twitter_timeline_list', new Route(
	'/twitter/timeline/{account_id}/{group}/{group_value}',
	array('_controller' => 'AgentBundle:TwitterStatus:listTimeline', 'group' => '', 'group_value' => ''),
	array('account_id' => '\\d+'),
	array('fragment_name' => 'tw-timeline', 'fragment_type' => 'list')
));


$collection->add('agent_twitter_followers_list', new Route(
	'/twitter/followers/{account_id}',
	array('_controller' => 'AgentBundle:TwitterUser:listFollowers'),
	array('account_id' => '\\d+'),
	array('fragment_name' => 'tw-followers', 'fragment_type' => 'list')
));

$collection->add('agent_twitter_followers_list_new', new Route(
	'/twitter/followers/{account_id}/new',
	array('_controller' => 'AgentBundle:TwitterUser:listNewFollowers'),
	array('account_id' => '\\d+'),
	array('fragment_name' => 'tw-newfollowers', 'fragment_type' => 'list')
));

$collection->add('agent_twitter_following_list', new Route(
	'/twitter/following/{account_id}',
	array('_controller' => 'AgentBundle:TwitterUser:listFollowing'),
	array('account_id' => '\\d+'),
	array('fragment_name' => 'tw-following', 'fragment_type' => 'list')
));


$collection->add('agent_twitter_status_ajaxmasssave', new Route(
	'/twitter/status/ajax-mass-save.json',
	array('_controller' => 'AgentBundle:TwitterStatus:ajaxMassSave'),
	array('_method' => 'POST'),
	array()
));

$collection->add('agent_twitter_status_ajaxsave_note', new Route(
	'/twitter/status/ajax-note.json',
	array('_controller' => 'AgentBundle:TwitterStatus:ajaxSaveNote'),
	array('_method' => 'POST'),
	array()
));

$collection->add('agent_twitter_status_ajaxsave_retweet', new Route(
	'/twitter/status/ajax-retweet.json',
	array('_controller' => 'AgentBundle:TwitterStatus:ajaxSaveRetweet'),
	array('_method' => 'POST'),
	array()
));

$collection->add('agent_twitter_status_ajaxsave_unretweet', new Route(
	'/twitter/status/ajax-unretweet.json',
	array('_controller' => 'AgentBundle:TwitterStatus:ajaxSaveUnretweet'),
	array('_method' => 'POST'),
	array()
));

$collection->add('agent_twitter_status_ajaxsave_reply', new Route(
	'/twitter/status/ajax-reply.json',
	array('_controller' => 'AgentBundle:TwitterStatus:ajaxSaveReply'),
	array('_method' => 'POST'),
	array()
));

$collection->add('agent_twitter_status_ajaxsave_archive', new Route(
	'/twitter/status/ajax-archive.json',
	array('_controller' => 'AgentBundle:TwitterStatus:ajaxSaveArchive'),
	array('_method' => 'POST'),
	array()
));

$collection->add('agent_twitter_status_ajaxsave_delete', new Route(
	'/twitter/status/ajax-delete.json',
	array('_controller' => 'AgentBundle:TwitterStatus:ajaxSaveDelete'),
	array('_method' => 'POST'),
	array()
));

$collection->add('agent_twitter_status_ajaxsave_edit', new Route(
	'/twitter/status/ajax-edit',
	array('_controller' => 'AgentBundle:TwitterStatus:ajaxSaveEdit'),
	array(),
	array()
));

$collection->add('agent_twitter_status_ajaxsave_favorite', new Route(
	'/twitter/status/ajax-favorite.json',
	array('_controller' => 'AgentBundle:TwitterStatus:ajaxSaveFavorite'),
	array('_method' => 'POST'),
	array()
));

$collection->add('agent_twitter_status_ajaxsave_assign', new Route(
	'/twitter/status/ajax-assign.json',
	array('_controller' => 'AgentBundle:TwitterStatus:ajaxSaveAssign'),
	array('_method' => 'POST'),
	array()
));

$collection->add('agent_twitter_status_tweet_overlay', new Route(
	'/twitter/status/tweet-overlay',
	array('_controller' => 'AgentBundle:TwitterStatus:tweetOverlay'),
	array(),
	array()
));

$collection->add('agent_twitter_user', new Route(
	'/twitter/user/{user_id}',
	array('_controller' => 'AgentBundle:TwitterUser:view'),
	array('user_id' => '\\d+'),
	array('fragment_name' => 'twitter')
));

$collection->add('agent_twitter_user_statuses', new Route(
	'/twitter/user/{user_id}/statuses',
	array('_controller' => 'AgentBundle:TwitterUser:viewUserStatuses'),
	array('user_id' => '\\d+'),
	array()
));

$collection->add('agent_twitter_user_following', new Route(
	'/twitter/user/{user_id}/following',
	array('_controller' => 'AgentBundle:TwitterUser:viewUserFollowing'),
	array('user_id' => '\\d+'),
	array()
));

$collection->add('agent_twitter_user_followers', new Route(
	'/twitter/user/{user_id}/followers',
	array('_controller' => 'AgentBundle:TwitterUser:viewUserFollowers'),
	array('user_id' => '\\d+'),
	array()
));

$collection->add('agent_twitter_user_find', new Route(
	'/twitter/user/find',
	array('_controller' => 'AgentBundle:TwitterUser:find'),
	array(),
	array()
));

$collection->add('agent_twitter_user_message_overlay', new Route(
	'/twitter/user/{user_id}/message-overlay',
	array('_controller' => 'AgentBundle:TwitterUser:messageOverlay'),
	array('user_id' => '\\d+'),
	array()
));

$collection->add('agent_twitter_user_ajaxsave_follow', new Route(
	'/twitter/user/ajax-follow.json',
	array('_controller' => 'AgentBundle:TwitterUser:ajaxSaveFollow'),
	array('_method' => 'POST'),
	array()
));

$collection->add('agent_twitter_user_ajaxsave_unfollow', new Route(
	'/twitter/user/ajax-unfollow.json',
	array('_controller' => 'AgentBundle:TwitterUser:ajaxSaveUnfollow'),
	array('_method' => 'POST'),
	array()
));

$collection->add('agent_twitter_user_ajaxsave_message', new Route(
	'/twitter/user/ajax-message.json',
	array('_controller' => 'AgentBundle:TwitterUser:ajaxSaveMessage'),
	array('_method' => 'POST'),
	array()
));

$collection->add('agent_twitter_user_ajaxsave_archive', new Route(
	'/twitter/user/ajax-archive.json',
	array('_controller' => 'AgentBundle:TwitterUser:ajaxSaveArchive'),
	array('_method' => 'POST'),
	array()
));

$collection->add('agent_twitter_user_ajaxsave_person', new Route(
	'/twitter/user/ajax-person.json',
	array('_controller' => 'AgentBundle:TwitterUser:ajaxSavePerson'),
	array('_method' => 'POST'),
	array()
));

$collection->add('agent_twitter_user_ajaxsave_organization', new Route(
	'/twitter/user/ajax-organization.json',
	array('_controller' => 'AgentBundle:TwitterUser:ajaxSaveOrganization'),
	array('_method' => 'POST'),
	array()
));


$collection->add('agent_twitter_getsectiondata', new Route(
	'/twitter/get-section-data.json',
	array('_controller' => 'AgentBundle:Twitter:getSectionData'),
	array(),
	array()
));

$collection->add('agent_twitter_updategrouping', new Route(
	'/twitter/update-grouping.json',
	array('_controller' => 'AgentBundle:Twitter:updateGrouping'),
	array(),
	array()
));

$collection->add('agent_twitter_run_search', new Route(
	'/twitter/{account_id}/search/{search_id}',
	array('_controller' => 'AgentBundle:Twitter:runSearch'),
	array('account_id' => '\\d+', 'search_id' => '\\d+'),
	array('fragment_name' => 'searches', 'fragment_type' => 'list')
));

$collection->add('agent_twitter_search_delete', new Route(
	'/twitter/{account_id}/search/delete/{security_token}',
	array('_controller' => 'AgentBundle:Twitter:deleteSearch'),
	array('account_id' => '\\d+'),
	array()
));

$collection->add('agent_twitter_new_search', new Route(
	'/twitter/{account_id}/search/new',
	array('_controller' => 'AgentBundle:Twitter:newSearch'),
	array('account_id' => '\\d+'),
	array()
));


################################################################################
# Tasks
################################################################################

$collection->add('agent_task_new', new Route(
	'/tasks/new',
	array('_controller' => 'AgentBundle:Task:new'),
	array(),
	array('fragment_name' => 'nt')
));

$collection->add('agent_task_save', new Route(
	'/tasks/save',
	array('_controller' => 'AgentBundle:Task:create'),
	array('_method' => 'POST'),
	array()
));

$collection->add('agent_task_delete', new Route(
	'/tasks/{task_id}/delete',
	array('_controller' => 'AgentBundle:Task:deleteTask'),
	array('task_id' => '\\d+'),
	array()
));

$collection->add('agent_tasksearch_getsectiondata', new Route(
	'/tasks/get-section-data.json',
	array('_controller' => 'AgentBundle:Task:getSectionData'),
	array(),
	array()
));

$collection->add('agent_task_list', new Route(
	'/tasks/list/{search_type}/{search_categoty}',
	array('_controller' => 'AgentBundle:Task:taskList', 'search_type' => NULL, 'search_categoty' => NULL),
	array(),
	array('fragment_name' => 'tasks', 'fragment_type' => 'list')
));

$collection->add('agent_task_ajax_labels_save', new Route(
	'/tasks/{task_id}/ajax-save-labels',
	array('_controller' => 'AgentBundle:Task:ajaxSaveLabels'),
	array('task_id' => '\\d+'),
	array()
));

$collection->add('agent_task_ajaxsave_comment', new Route(
	'/tasks/{task_id}/ajax-save-comment',
	array('_controller' => 'AgentBundle:Task:ajaxSaveComment'),
	array('task_id' => '\\d+', 'person_id' => '\\d+'),
	array()
));

$collection->add('agent_task_ajaxsave', new Route(
	'/tasks/{task_id}/ajax-save',
	array('_controller' => 'AgentBundle:Task:ajaxSave'),
	array(),
	array()
));


################################################################################
# Deals
################################################################################

$collection->add('agent_dealearch_getsectiondata', new Route(
	'/deal/get-section-data.json',
	array('_controller' => 'AgentBundle:Deal:getSectionData'),
	array(),
	array()
));

$collection->add('agent_deal_list', new Route(
	'/deals/list/{owner_type}/{deal_status}/{deal_type_id}',
	array('_controller' => 'AgentBundle:Deal:dealList', 'owner_type' => NULL, 'deal_status' => NULL, 'deal_type_id' => NULL),
	array('deal_type_id' => '\\d+'),
	array()
));

$collection->add('agent_deal_view', new Route(
	'/deal/{deal_id}',
	array('_controller' => 'AgentBundle:Deal:view'),
	array('deal_id' => '\\d+'),
	array('fragment_name' => 'd')
));

$collection->add('agent_deal_ajaxsave_note', new Route(
	'/deal/{deal_id}/ajax-save-note',
	array('_controller' => 'AgentBundle:Deal:ajaxSaveNote'),
	array('deal_id' => '\\d+'),
	array()
));

$collection->add('agent_deal_ajax_labels_save', new Route(
	'/deal/{deal_id}/ajax-save-labels',
	array('_controller' => 'AgentBundle:Deal:ajaxSaveLabels'),
	array('deal_id' => '\\d+'),
	array()
));

$collection->add('agent_deal_ajaxsavecustomfields', new Route(
	'/deal/{deal_id}/ajax-save-custom-fields',
	array('_controller' => 'AgentBundle:Deal:ajaxSaveCustomFields'),
	array('deal_id' => '\\d+'),
	array()
));

$collection->add('agent_deal_set_agent_parts', new Route(
	'/deals/{deal_id}/{agent_id}/set-agent-parts.json',
	array('_controller' => 'AgentBundle:Deal:setAgentParticipants'),
	array(),
	array()
));

$collection->add('agent_deal_ajaxsave', new Route(
	'/deals/{deal_id}/ajax-save',
	array('_controller' => 'AgentBundle:Deal:ajaxSave'),
	array('deal_id' => '\\d+'),
	array()
));

$collection->add('agent_deal_new', new Route(
	'/deals/new',
	array('_controller' => 'AgentBundle:Deal:new'),
	array(),
	array('fragment_name' => 'nt')
));

$collection->add('agent_deal_new_save', new Route(
	'/deals/new/save',
	array('_controller' => 'AgentBundle:Deal:newSave'),
	array(),
	array()
));

$collection->add('agent_deal_new_getpersonrow', new Route(
	'/deals/new/get-person-row/{person_id}',
	array('_controller' => 'AgentBundle:Deal:newdealGetPersonRow'),
	array(),
	array()
));

$collection->add('agent_deal_new_getorganizationrow', new Route(
	'/deals/new/get-organization-row/{org_id}',
	array('_controller' => 'AgentBundle:Deal:newdealGetOrganizationRow'),
	array(),
	array()
));

$collection->add('agent_deal_create_setpersonrow', new Route(
	'/deals/new/create-person-row/{person_id}',
	array('_controller' => 'AgentBundle:Deal:newdealCreatePersonRow'),
	array(),
	array()
));

$collection->add('agent_deal_new_setpersonrow', new Route(
	'/deals/new/set-person-row/{person_id}',
	array('_controller' => 'AgentBundle:Deal:newdealSetPersonRow'),
	array(),
	array()
));

$collection->add('agent_deal_new_setorganizationrow', new Route(
	'/deals/new/set-organization-row/{org_id}',
	array('_controller' => 'AgentBundle:Deal:newdealSetOrganizationRow'),
	array(),
	array()
));

$collection->add('agent_deal_create_setorganizationrow', new Route(
	'/deals/new/create-organization-row/{org_id}',
	array('_controller' => 'AgentBundle:Deal:newdealCreateOrganizationRow'),
	array(),
	array()
));


################################################################################
# Publish
################################################################################

$collection->add('agent_publish_getsectiondata', new Route(
	'/publish/get-section-data.json',
	array('_controller' => 'AgentBundle:Publish:getSectionData'),
	array(),
	array()
));

$collection->add('agent_publish_ratingwhovoted', new Route(
	'/publish/rating-who-voted/{object_type}/{object_id}',
	array('_controller' => 'AgentBundle:Publish:ratingWhoVoted'),
	array(),
	array()
));

$collection->add('agent_publish_whoviewed', new Route(
	'/publish/who-viewed/{object_type}/{object_id}/{view_action}',
	array('_controller' => 'AgentBundle:Publish:whoViewed', 'view_action' => 1),
	array(),
	array()
));

$collection->add('agent_publish_save_stickysearchwords', new Route(
	'/publish/save-sticky-search-words/{type}/{content_id}',
	array('_controller' => 'AgentBundle:Publish:saveStickySearchWords'),
	array(),
	array()
));

$collection->add('agent_publish_validatingcontent', new Route(
	'/publish/content/validating',
	array('_controller' => 'AgentBundle:Publish:listValidatingContent'),
	array(),
	array('fragment_type' => 'list', 'fragment_name' => 'validating_content')
));

$collection->add('agent_feedback_validatingcontent', new Route(
	'/feedback/content/validating',
	array('_controller' => 'AgentBundle:Publish:listValidatingFeedbackContent'),
	array(),
	array('fragment_type' => 'list', 'fragment_name' => 'fb_content')
));

$collection->add('agent_feedback_validatingcomments', new Route(
	'/feedback/comments/validating',
	array('_controller' => 'AgentBundle:Publish:listValidatingFeedbackComments'),
	array(),
	array('fragment_type' => 'list', 'fragment_name' => 'fb_comments')
));

$collection->add('agent_publish_validatingcontent_approve', new Route(
	'/publish/content/approve/{type}/{content_id}.json',
	array('_controller' => 'AgentBundle:Publish:approveContent'),
	array(),
	array()
));

$collection->add('agent_publish_validatingcontent_disapprove', new Route(
	'/publish/content/disapprove/{type}/{content_id}.json',
	array('_controller' => 'AgentBundle:Publish:disapproveContent'),
	array(),
	array()
));

$collection->add('agent_publish_validatingcontent_mass', new Route(
	'/publish/content/validating-mass-actions/{action}',
	array('_controller' => 'AgentBundle:Publish:validatingMassActions'),
	array(),
	array()
));

$collection->add('agent_publish_validatingcontent_next', new Route(
	'/publish/content/get-next-validating/{type}/{content_id}.json',
	array('_controller' => 'AgentBundle:Publish:nextValidatingContent'),
	array(),
	array('fragment_name' => 'pending', 'fragment_type' => 'list')
));

$collection->add('agent_publish_listcomments', new Route(
	'/publish/comments/list/{type}',
	array('_controller' => 'AgentBundle:Publish:listComments'),
	array(),
	array('fragment_type' => 'list', 'fragment_name' => 'list_comments')
));

$collection->add('agent_publish_validatingcomments', new Route(
	'/publish/comments/validating',
	array('_controller' => 'AgentBundle:Publish:listValidatingComments'),
	array(),
	array('fragment_type' => 'list', 'fragment_name' => 'validating_comments')
));

$collection->add('agent_publish_approve_comment', new Route(
	'/publish/comments/approve/{typename}/{comment_id}',
	array('_controller' => 'AgentBundle:Publish:approveComment'),
	array(),
	array()
));

$collection->add('agent_publish_delete_comment', new Route(
	'/publish/comments/delete/{typename}/{comment_id}',
	array('_controller' => 'AgentBundle:Publish:deleteComment'),
	array(),
	array()
));

$collection->add('agent_publish_comment_info', new Route(
	'/publish/comments/info/{typename}/{comment_id}',
	array('_controller' => 'AgentBundle:Publish:commentInfo'),
	array(),
	array()
));

$collection->add('agent_publish_comment_save', new Route(
	'/publish/comments/save-comment/{typename}/{comment_id}',
	array('_controller' => 'AgentBundle:Publish:saveComment'),
	array(),
	array()
));

$collection->add('agent_public_comment_newticketinfo', new Route(
	'/publish/comments/new-ticket-info/{typename}/{comment_id}.json',
	array('_controller' => 'AgentBundle:Publish:getNewTicketCommentInfo'),
	array(),
	array()
));

$collection->add('agent_publish_validatingcomments_mass', new Route(
	'/publish/comments/validating-mass-actions/{action}',
	array('_controller' => 'AgentBundle:Publish:validatingCommentsMassActions'),
	array(),
	array()
));

$collection->add('agent_publish_savecats', new Route(
	'/publish/save-categories/{type}',
	array('_controller' => 'AgentBundle:Publish:saveCategories'),
	array(),
	array()
));

$collection->add('agent_publish_cats_adddel', new Route(
	'/publish/categories/{type}/delete-category',
	array('_controller' => 'AgentBundle:Publish:deleteCategory'),
	array(),
	array()
));

$collection->add('agent_publish_cats_addcat', new Route(
	'/publish/categories/{type}/add-category',
	array('_controller' => 'AgentBundle:Publish:addCategory'),
	array(),
	array()
));

$collection->add('agent_publish_cats_updateorders', new Route(
	'/publish/categories/{type}/update-orders',
	array('_controller' => 'AgentBundle:Publish:updateCategoryOrders'),
	array(),
	array()
));

$collection->add('agent_publish_cats_updatetitles', new Route(
	'/publish/categories/{type}/update-titles',
	array('_controller' => 'AgentBundle:Publish:updateCategoryTitles'),
	array(),
	array()
));

$collection->add('agent_publish_cats_update', new Route(
	'/publish/categories/{type}/update/{category_id}',
	array('_controller' => 'AgentBundle:Publish:updateCategory'),
	array(),
	array()
));

$collection->add('agent_publish_cats_updatestructure', new Route(
	'/publish/categories/{type}/update-structure',
	array('_controller' => 'AgentBundle:Publish:updateCategoryStructure'),
	array(),
	array()
));

$collection->add('agent_publish_cats_newform', new Route(
	'/publish/categories/{type}/new-form',
	array('_controller' => 'AgentBundle:Publish:addCategoryForm'),
	array(),
	array()
));

$collection->add('agent_publish_cats_newform_save', new Route(
	'/publish/categories/{type}/new-form/save',
	array('_controller' => 'AgentBundle:Publish:addCategoryFormSave'),
	array(),
	array()
));

$collection->add('agent_public_drafts', new Route(
	'/publish/drafts/{type}',
	array('_controller' => 'AgentBundle:Publish:listDrafts'),
	array(),
	array('fragment_name' => 'drafts', 'fragment_type' => 'list')
));

$collection->add('agent_public_drafts_mass', new Route(
	'/publish/drafts/mass-actions/{action}',
	array('_controller' => 'AgentBundle:Publish:draftsMassActions'),
	array(),
	array()
));

$collection->add('agent_publish_search', new Route(
	'/publish/search',
	array('_controller' => 'AgentBundle:Publish:search'),
	array(),
	array()
));

$collection->add('agent_kb_newarticle_save', new Route(
	'/kb/article/new/save',
	array('_controller' => 'AgentBundle:Kb:newArticleSave'),
	array(),
	array()
));

$collection->add('agent_kb_newarticle', new Route(
	'/kb/article/new',
	array('_controller' => 'AgentBundle:Kb:newArticle'),
	array(),
	array()
));

$collection->add('agent_kb_article', new Route(
	'/kb/article/{article_id}',
	array('_controller' => 'AgentBundle:Kb:viewArticle'),
	array('article_id' => '\\d+'),
	array('fragment_name' => 'a')
));

$collection->add('agent_kb_ajaxsavecustomfields', new Route(
	'/kb/article/{article_id}/ajax-save-custom-fields',
	array('_controller' => 'AgentBundle:Kb:ajaxSaveCustomFields'),
	array('article_id' => '\\d+'),
	array()
));

$collection->add('agent_kb_article_info', new Route(
	'/kb/article/{article_id}/info',
	array('_controller' => 'AgentBundle:Kb:articleInfo'),
	array('article_id' => '\\d+'),
	array()
));

$collection->add('agent_kb_article_revisionstab', new Route(
	'/kb/article/{article_id}/view-revisions',
	array('_controller' => 'AgentBundle:Kb:viewRevisions'),
	array('article_id' => '\\d+'),
	array()
));

$collection->add('agent_kb_article_ajaxsave', new Route(
	'/kb/article/{article_id}/ajax-save',
	array('_controller' => 'AgentBundle:Kb:ajaxSave'),
	array('article_id' => '\\d+'),
	array()
));

$collection->add('agent_kb_ajax_save_comment', new Route(
	'/kb/article/{article_id}/ajax-save-comment',
	array('_controller' => 'AgentBundle:Kb:ajaxSaveComment'),
	array('article_id' => '\\d+'),
	array()
));

$collection->add('agent_kb_ajax_labels_save', new Route(
	'/kb/article/{article_id}/ajax-save-labels',
	array('_controller' => 'AgentBundle:Kb:ajaxSaveLabels'),
	array('article_id' => '\\d+'),
	array()
));

$collection->add('agent_kb_comparerevs', new Route(
	'/kb/compare-revs/{rev_old_id}/{rev_new_id}',
	array('_controller' => 'AgentBundle:Kb:compareRevisions'),
	array(),
	array()
));

$collection->add('agent_kb_newpending', new Route(
	'/kb/pending-articles/new',
	array('_controller' => 'AgentBundle:Kb:newPendingArticle'),
	array(),
	array()
));

$collection->add('agent_kb_pending_remove', new Route(
	'/kb/pending-articles/{pending_article_id}/remove',
	array('_controller' => 'AgentBundle:Kb:removePendingArticle'),
	array(),
	array()
));

$collection->add('agent_kb_pending_info', new Route(
	'/kb/pending-articles/{pending_article_id}/info',
	array('_controller' => 'AgentBundle:Kb:pendingArticleInfo'),
	array(),
	array()
));

$collection->add('agent_kb_pending', new Route(
	'/kb/pending-articles',
	array('_controller' => 'AgentBundle:Kb:listPendingArticles'),
	array(),
	array('fragment_name' => 'pending', 'fragment_type' => 'list')
));

$collection->add('agent_kb_pending_massactions', new Route(
	'/kb/pending-articles/mass-actions/{action}',
	array('_controller' => 'AgentBundle:Kb:pendingArticlesMassActions'),
	array(),
	array()
));

$collection->add('agent_kb_list', new Route(
	'/kb/list/{category_id}',
	array('_controller' => 'AgentBundle:Kb:list', 'category_id' => '0'),
	array(),
	array('fragment_name' => 'knowledgebase', 'fragment_type' => 'list')
));

$collection->add('agent_kb_cat', new Route(
	'/kb/category/{category_id}',
	array('_controller' => 'AgentBundle:Kb:list'),
	array(),
	array()
));

$collection->add('agent_kb_mass_save', new Route(
	'/kb/article/ajax-mass-save',
	array('_controller' => 'AgentBundle:Kb:ajaxMassSave'),
	array(),
	array()
));

$collection->add('agent_glossary_newword_json', new Route(
	'/glossary/new-word.json',
	array('_controller' => 'AgentBundle:Glossary:glossaryNewWordJson'),
	array(),
	array()
));

$collection->add('agent_glossary_word_json', new Route(
	'/glossary/{word_id}.json',
	array('_controller' => 'AgentBundle:Glossary:glossaryWordJson'),
	array(),
	array()
));

$collection->add('agent_glossary_saveword_json', new Route(
	'/glossary/{word_id}/edit.json',
	array('_controller' => 'AgentBundle:Glossary:glossarySaveWordJson'),
	array(),
	array()
));

$collection->add('agent_glossary_delword_json', new Route(
	'/glossary/{word_id}/delete.json',
	array('_controller' => 'AgentBundle:Glossary:glossaryDeleteWordJson'),
	array(),
	array()
));

$collection->add('agent_glossary_word_tip', new Route(
	'/glossary/{word}/tip',
	array('_controller' => 'AgentBundle:Glossary:tip'),
	array(),
	array()
));

$collection->add('agent_news_list', new Route(
	'/news/list/{category_id}',
	array('_controller' => 'AgentBundle:News:list', 'category_id' => '0'),
	array(),
	array('fragment_name' => 'news', 'fragment_type' => 'list')
));

$collection->add('agent_news_view', new Route(
	'/news/post/{news_id}',
	array('_controller' => 'AgentBundle:News:view'),
	array('news_id' => '\\d+'),
	array('fragment_name' => 'n')
));

$collection->add('agent_news_revisionstab', new Route(
	'/news/post/{news_id}/view-revisions',
	array('_controller' => 'AgentBundle:News:viewRevisions'),
	array('news_id' => '\\d+'),
	array()
));

$collection->add('agent_news_save', new Route(
	'/news/post/{news_id}/ajax-save',
	array('_controller' => 'AgentBundle:News:ajaxSave'),
	array('news_id' => '\\d+'),
	array()
));

$collection->add('agent_news_ajax_labels_save', new Route(
	'/news/{news_id}/ajax-save-labels',
	array('_controller' => 'AgentBundle:News:ajaxSaveLabels'),
	array('news_id' => '\\d+'),
	array()
));

$collection->add('agent_news_ajax_save_comment', new Route(
	'/news/post/{news_id}/ajax-save-comment',
	array('_controller' => 'AgentBundle:News:ajaxSaveComment'),
	array('news_id' => '\\d+'),
	array()
));

$collection->add('agent_news_new_save', new Route(
	'/news/new/save',
	array('_controller' => 'AgentBundle:News:newNewsSave'),
	array(),
	array()
));

$collection->add('agent_news_new', new Route(
	'/news/new',
	array('_controller' => 'AgentBundle:News:newNews'),
	array(),
	array()
));

$collection->add('agent_news_comparerevs', new Route(
	'/news/compare-revs/{rev_old_id}/{rev_new_id}',
	array('_controller' => 'AgentBundle:News:compareRevisions'),
	array(),
	array()
));

$collection->add('agent_downloads_list', new Route(
	'/downloads/list/{category_id}',
	array('_controller' => 'AgentBundle:Downloads:list', 'category_id' => '0'),
	array(),
	array('fragment_name' => 'downloads', 'fragment_type' => 'list')
));

$collection->add('agent_downloads_view', new Route(
	'/downloads/file/{download_id}',
	array('_controller' => 'AgentBundle:Downloads:view'),
	array('download_id' => '\\d+'),
	array('fragment_name' => 'd')
));

$collection->add('agent_downloads_info', new Route(
	'/downloads/file/{download_id}/info',
	array('_controller' => 'AgentBundle:Downloads:info'),
	array('download_id' => '\\d+'),
	array()
));

$collection->add('agent_kb_downloads_revisionstab', new Route(
	'/downloads/file/{download_id}/view-revisions',
	array('_controller' => 'AgentBundle:Downloads:viewRevisions'),
	array('article_id' => '\\d+'),
	array()
));

$collection->add('agent_downloads_save', new Route(
	'/downloads/file/{download_id}/ajax-save',
	array('_controller' => 'AgentBundle:Downloads:ajaxSave'),
	array('download_id' => '\\d+'),
	array()
));

$collection->add('agent_downloads_ajax_labels_save', new Route(
	'/downloads/file/{download_id}/ajax-save-labels',
	array('_controller' => 'AgentBundle:Downloads:ajaxSaveLabels'),
	array('download_id' => '\\d+'),
	array()
));

$collection->add('agent_downloads_ajax_save_comment', new Route(
	'/downloads/file/{download_id}/ajax-save-comment',
	array('_controller' => 'AgentBundle:Downloads:ajaxSaveComment'),
	array('download_id' => '\\d+'),
	array()
));

$collection->add('agent_downloads_new_save', new Route(
	'/downloads/new/save',
	array('_controller' => 'AgentBundle:Downloads:newDownloadSave'),
	array(),
	array()
));

$collection->add('agent_downloads_new', new Route(
	'/downloads/new',
	array('_controller' => 'AgentBundle:Downloads:newDownload'),
	array(),
	array()
));

$collection->add('agent_downloads_comparerevs', new Route(
	'/downloads/compare-revs/{rev_old_id}/{rev_new_id}',
	array('_controller' => 'AgentBundle:Downloads:compareRevisions'),
	array(),
	array()
));

$collection->add('agent_feedback_category', new Route(
	'/feedback/category/{category_id}',
	array('_controller' => 'AgentBundle:Feedback:categoryList'),
	array(),
    array('fragment_name' => 'category', 'fragment_type' => 'list')
));

$collection->add('agent_feedback_status', new Route(
	'/feedback/status/{status}',
	array('_controller' => 'AgentBundle:Feedback:statusList'),
	array(),
	array('fragment_name' => 'status', 'fragment_type' => 'list')
));

$collection->add('agent_feedback_label', new Route(
	'/feedback/label/{label}',
	array('_controller' => 'AgentBundle:Feedback:labelList'),
	array(),
    array('fragment_name' => 'label', 'fragment_type' => 'list')
));

$collection->add('agent_feedback_filter', new Route(
	'/feedback/filter',
	array('_controller' => 'AgentBundle:Feedback:filterList'),
	array(),
	array()
));

$collection->add('agent_feedback_massactions', new Route(
	'/feedback/filter/mass-actions/{action}',
	array('_controller' => 'AgentBundle:Feedback:massActions'),
	array(),
	array()
));

$collection->add('agent_feedback_getsectiondata', new Route(
	'/feedback/get-section-data.json',
	array('_controller' => 'AgentBundle:Feedback:getSectionData'),
	array(),
	array()
));

$collection->add('agent_feedback_new', new Route(
	'/feedback/new',
	array('_controller' => 'AgentBundle:Feedback:newFeedback'),
	array(),
	array()
));

$collection->add('agent_feedback_new_save', new Route(
	'/feedback/new/save',
	array('_controller' => 'AgentBundle:Feedback:newFeedbackSave'),
	array(),
	array()
));

$collection->add('agent_feedback_view', new Route(
	'/feedback/view/{feedback_id}',
	array('_controller' => 'AgentBundle:Feedback:view'),
	array(),
	array('fragment_name' => 'i')
));

$collection->add('agent_feedback_comparerevs', new Route(
	'/feedback/compare-revs/{rev_old_id}/{rev_new_id}',
	array('_controller' => 'AgentBundle:Feedback:compareRevisions'),
	array(),
	array()
));

$collection->add('agent_feedback_ajaxsavecustomfields', new Route(
	'/feedback/view/{feedback_id}/ajax-save-custom-fields',
	array('_controller' => 'AgentBundle:Feedback:ajaxSaveCustomFields'),
	array('feedback_id' => '\\d+'),
	array()
));

$collection->add('agent_feedback_who_voted', new Route(
	'/feedback/view/{feedback_id}/who-voted',
	array('_controller' => 'AgentBundle:Feedback:whoVoted'),
	array(),
	array()
));

$collection->add('agent_feedback_save', new Route(
	'/feedback/view/{feedback_id}/ajax-save',
	array('_controller' => 'AgentBundle:Feedback:ajaxSave'),
	array('news_id' => '\\d+'),
	array()
));

$collection->add('agent_feedback_ajax_labels_save', new Route(
	'/feedback/view/{feedback_id}/ajax-save-labels',
	array('_controller' => 'AgentBundle:Feedback:ajaxSaveLabels'),
	array('news_id' => '\\d+'),
	array()
));

$collection->add('agent_feedback_ajax_save_comment', new Route(
	'/feedback/view/{feedback_id}/ajax-save-comment',
	array('_controller' => 'AgentBundle:Feedback:ajaxSaveComment'),
	array('news_id' => '\\d+'),
	array()
));

$collection->add('agent_feedback_ajaxsavecomment', new Route(
	'/feedback/view/{feedback_id}/ajax-save-comment',
	array('_controller' => 'AgentBundle:Feedback:ajaxSaveComment'),
	array(),
	array()
));

$collection->add('agent_feedback_ajaxsaveeditables', new Route(
	'/feedback/view/{feedback_id}/ajax-save-editables',
	array('_controller' => 'AgentBundle:Feedback:ajaxSaveEditables'),
	array(),
	array()
));

$collection->add('agent_feedback_ajaxupdatecat', new Route(
	'/feedback/view/{feedback_id}/ajax-update-category/{category_id}',
	array('_controller' => 'AgentBundle:Feedback:ajaxUpdateCategory'),
	array(),
	array()
));

$collection->add('agent_feedback_ajaxupdatestatus', new Route(
	'/feedback/view/{feedback_id}/ajax-update-status/{status_code}',
	array('_controller' => 'AgentBundle:Feedback:ajaxUpdateStatus'),
	array(),
	array()
));

$collection->add('agent_feedback_merge_overlay', new Route(
	'/feedback/merge-overlay/{feedback_id}/{other_feedback_id}',
	array('_controller' => 'AgentBundle:Feedback:mergeOverlay'),
	array('feedback_id' => '\\d+', 'other_feedback_id' => '\\d+'),
	array()
));

$collection->add('agent_feedback_merge', new Route(
	'/feedback/merge/{feedback_id}/{other_feedback_id}',
	array('_controller' => 'AgentBundle:Feedback:merge'),
	array('feedback_id' => '\\d+', 'other_feedback_id' => '\\d+'),
	array()
));


################################################################################
# Agent and User Chat
################################################################################

$collection->add('agent_agentchat_getonlineagents', new Route(
	'/agent-chat/get-online-agents.json',
	array('_controller' => 'AgentBundle:AgentChat:getOnlineAgents'),
	array(),
	array()
));

$collection->add('agent_agentchat_get_last_convo', new Route(
	'/agent-chat/get-last-convo',
	array('_controller' => 'AgentBundle:AgentChat:loadConvoMessages'),
	array(),
	array()
));

$collection->add('agent_agentchat_send_message', new Route(
	'/agent-chat/send-message/{conversation_id}',
	array('_controller' => 'AgentBundle:AgentChat:sendMessage'),
	array(),
	array()
));

$collection->add('agent_agentchat_send_agent_message', new Route(
	'/agent-chat/send-agent-message/{convo_id}',
	array('_controller' => 'AgentBundle:AgentChat:sendAgentMessage'),
	array(),
	array()
));

$collection->add('agent_agentchat_history', new Route(
	'/agent-chat/agent-history/{agent_id}',
	array('_controller' => 'AgentBundle:AgentChat:agentHistory'),
	array(),
	array()
));

$collection->add('agent_agentchat_history_team', new Route(
	'/agent-chat/agent-history/team/{agent_team_id}',
	array('_controller' => 'AgentBundle:AgentChat:agentTeamHistory'),
	array(),
	array()
));

$collection->add('agent_agentchat_view', new Route(
	'/agent-chat/agent-transcript/{conversation_id}',
	array('_controller' => 'AgentBundle:AgentChat:agentChatTranscript'),
	array(),
	array()
));

$collection->add('agent_agentchat_getsectiondata', new Route(
	'/agent-chat/get-section-data.json',
	array('_controller' => 'AgentBundle:AgentChat:getSectionData'),
	array(),
	array()
));

$collection->add('agent_userchat_view', new Route(
	'/chat/view/{conversation_id}',
	array('_controller' => 'AgentBundle:UserChat:view'),
	array(),
	array('fragment_name' => 'c')
));

$collection->add('agent_userchat_save_fields', new Route(
	'/chat/{conversation_id}/save-fields',
	array('_controller' => 'AgentBundle:UserChat:saveFields'),
	array('_method' => 'POST'),
	array()
));

$collection->add('agent_userchat_blockuser', new Route(
	'/chat/block-user/{conversation_id}',
	array('_controller' => 'AgentBundle:UserChat:blockUser'),
	array(),
	array()
));

$collection->add('agent_userchat_unblockuser', new Route(
	'/chat/unblock-user/{conversation_id}',
	array('_controller' => 'AgentBundle:UserChat:unblockUser'),
	array(),
	array()
));

$collection->add('agent_userchat_ajax_labels_save', new Route(
	'/chat/{conversation_id}/ajax-save-labels',
	array('_controller' => 'AgentBundle:UserChat:ajaxSaveLabels'),
	array('conversation_id' => '\\d+'),
	array()
));

$collection->add('agent_userchat_open_counts', new Route(
	'/chat/open-counts.json',
	array('_controller' => 'AgentBundle:UserChat:getOpenCounts'),
	array(),
	array()
));

$collection->add('agent_userchat_filterlist_group_counts', new Route(
	'/chat/group-count.json',
	array('_controller' => 'AgentBundle:UserChat:getGroupByCounts'),
	array(),
	array()
));

$collection->add('agent_userchat_filterlist', new Route(
	'/chat/filter/{filter_id}',
	array('_controller' => 'AgentBundle:UserChat:filter'),
	array(),
	array('fragment_name' => 'ended', 'fragment_type' => 'list')
));

$collection->add('agent_userchat_list_new', new Route(
	'/chat/list-new/{department_id}',
	array('_controller' => 'AgentBundle:UserChat:listNewChats', 'department_id' => '-1'),
	array(),
	array('fragment_name' => 'new', 'fragment_type' => 'list')
));

$collection->add('agent_userchat_list_active', new Route(
	'/chat/list-active/{agent_id}',
	array('_controller' => 'AgentBundle:UserChat:listActiveChats', 'agent_id' => '-1'),
	array(),
	array('fragment_name' => 'active', 'fragment_type' => 'list')
));

$collection->add('agent_userchat_send_messageview', new Route(
	'/chat/send-message/{conversation_id}',
	array('_controller' => 'AgentBundle:UserChat:sendMessage'),
	array(),
	array()
));

$collection->add('agent_userchat_send_filemessage', new Route(
	'/chat/send-file-message/{conversation_id}',
	array('_controller' => 'AgentBundle:UserChat:sendFile'),
	array(),
	array()
));

$collection->add('agent_userchat_assign', new Route(
	'/chat/assign/{conversation_id}/{agent_id}',
	array('_controller' => 'AgentBundle:UserChat:assignChat'),
	array(),
	array()
));

$collection->add('agent_userchat_syncpart', new Route(
	'/chat/sync-parts/{conversation_id}',
	array('_controller' => 'AgentBundle:UserChat:syncParts'),
	array(),
	array()
));

$collection->add('agent_userchat_addpart', new Route(
	'/chat/add-part/{conversation_id}/{agent_id}',
	array('_controller' => 'AgentBundle:UserChat:addPart'),
	array(),
	array()
));

$collection->add('agent_userchat_end', new Route(
	'/chat/end-chat/{conversation_id}',
	array('_controller' => 'AgentBundle:UserChat:endChat'),
	array(),
	array()
));

$collection->add('agent_userchat_leave', new Route(
	'/chat/leave/{conversation_id}',
	array('_controller' => 'AgentBundle:UserChat:leaveChat'),
	array(),
	array()
));

$collection->add('agent_userchat_invite', new Route(
	'/chat/invite/{conversation_id}/{agent_id}',
	array('_controller' => 'AgentBundle:UserChat:sendInvite'),
	array(),
	array()
));

$collection->add('agent_userchat_changeprop', new Route(
	'/chat/change-props/{conversation_id}',
	array('_controller' => 'AgentBundle:UserChat:changeProperties'),
	array(),
	array()
));

$collection->add('agent_userchat_getsectiondata', new Route(
	'/chat/get-section-data.json',
	array('_controller' => 'AgentBundle:UserChat:getSectionData'),
	array(),
	array()
));

################################################################################
# User Tracking
################################################################################

$collection->add('agent_usertrack_winheadertable', new Route(
	'/user-track/win-header-table.html',
	array('_controller' => 'AgentBundle:UserTrack:winHeaderTable'),
	array(),
	array()
));

$collection->add('agent_usertrack_view', new Route(
	'/user-track/{visitor_id}',
	array('_controller' => 'AgentBundle:UserTrack:view'),
	array('visitor_id' => '\d+', '_method' => 'GET'),
	array()
));

################################################################################
# Media Manager
################################################################################

$collection->add('agent_mediamanager', new Route(
	'/media-manager',
	array('_controller' => 'AgentBundle:MediaManager:window'),
	array(),
	array()
));

$collection->add('agent_mediamanager_upload', new Route(
	'/media-manager/upload',
	array('_controller' => 'AgentBundle:MediaManager:upload'),
	array(),
	array()
));

$collection->add('agent_mediamanager_browse', new Route(
	'/media-manager/browse',
	array('_controller' => 'AgentBundle:MediaManager:browse'),
	array(),
	array()
));

################################################################################
# Text Snippets
################################################################################

$collection->add('agent_textsnippets_widget_shell', new Route(
	'/text-snippets/{typename}/widget-shell.txt',
	array('_controller' => 'AgentBundle:TextSnippets:getWidgetShell'),
	array(),
	array()
));

$collection->add('agent_textsnippets_reloadclient', new Route(
	'/text-snippets/{typename}/reload-client.json',
	array('_controller' => 'AgentBundle:TextSnippets:reloadClient'),
	array(),
	array()
));

$collection->add('agent_textsnippets_reloadclient_batch', new Route(
	'/text-snippets/{typename}/reload-client/{batch}.json',
	array('_controller' => 'AgentBundle:TextSnippets:reloadClientBatch'),
	array(),
	array()
));

$collection->add('agent_textsnippets_filtersnippets', new Route(
	'/text-snippets/{typename}/filter.json',
	array('_controller' => 'AgentBundle:TextSnippets:filterSnippets'),
	array(),
	array()
));

$collection->add('agent_textsnippets_getsnippet', new Route(
	'/text-snippets/{typename}/{id}.json',
	array('_controller' => 'AgentBundle:TextSnippets:getSnippet'),
	array(),
	array()
));

$collection->add('agent_textsnippets_savesnippet', new Route(
	'/text-snippets/{typename}/{id}/save.json',
	array('_controller' => 'AgentBundle:TextSnippets:saveSnippet'),
	array(),
	array()
));

$collection->add('agent_textsnippets_delsnippet', new Route(
	'/text-snippets/{typename}/{id}/delete.json',
	array('_controller' => 'AgentBundle:TextSnippets:deleteSnippet'),
	array(),
	array()
));

$collection->add('agent_textsnippets_savecat', new Route(
	'/text-snippets/{typename}/categories/{id}/save.json',
	array('_controller' => 'AgentBundle:TextSnippets:saveCategory'),
	array(),
	array()
));

$collection->add('agent_textsnippets_delcat', new Route(
	'/text-snippets/{typename}/categories/{id}/delete.json',
	array('_controller' => 'AgentBundle:TextSnippets:deleteCategory'),
	array(),
	array()
));

################################################################################
# Plugins
################################################################################

$collection->add('agent_plugins_run', new Route(
	'/plugins/{plugin_id}/run/{action}',
	array('_controller' => 'AgentBundle:Plugins:run'),
	array(),
	array()
));

return $collection;
