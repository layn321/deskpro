<?php
/**
 * This file is a set of asset bundles. The files and bundles
 * listed here declare how the 'assetic' build works.
 *
 * In templates, you can the template function dp_asset_html() to include the HTML
 * to add an asset to the page. For example, to include vendors (jquery, mootools etc):
 *
 *     {{ dp_asset_html('agent_vendors') }}
 *
 * You can of course use any asset in the templates, they dont need to be defined here:
 *
 *    <script src="{{ asset('javascripts/something.js') }}"></script>
 *
 * ... it just wont be compiled/minified etc.
 *
 *
 * == Syntax ==
 *
 * Eac
 */

$CONFIG = array();

$CONFIG['OPTIONS'] = array(
	'java_path'       => '/usr/bin/java',
	'yui_compressor'  => '/usr/local/bin/yuicompressor.jar',
	'nodejs'          => '/usr/local/bin/node',
	'less'            => '/usr/local/lib/node_modules/less/bin/lessc',
	'smartsprites'    => '/usr/local/bin/smartsprites-0.2.8/smartsprites.sh',
);

if (isset($GLOBALS['DP_CONFIG']['assetic_config'])) {
	$CONFIG['OPTIONS'] = array_merge($CONFIG['OPTIONS'], $GLOBALS['DP_CONFIG']['assetic_config']);
}

###############################################################################
# JAVASCRIPTS
###############################################################################

$CONFIG['agent'] = array(
	'out' => 'js/agent-all.js',
	'post_filters' => array('yui_simple'),
	'references' => array(
		'agent_vendors',
		'agent_common',
		'agent_deskpro_ui',
		'agent_misc',
		'agent_agent_ui',
		'agent_window_sections',
		'agent_settingswin',
		'agent_pages',
		'agent_pages_lists',
		'agent_element_handlers',
	)
);

$CONFIG['agent_vendors'] = array(
	'out' => 'js/agent-vendors.js',
	'files' => array(
		'vendor/tracekit.js',
		'vendor/modernizr.min.js',
		'javascripts/Orb/modernizr-ext.js',

		'vendor/JSON-js/json2.js',

		'vendor/jquery/jquery.min.js',
		'vendor/jquery/jquery.resize.min.js',
		'vendor/jquery/jquery-ui/jquery-ui.min.js',
		'vendor/jquery/jquery-tmpl/jquery.tmpl.min.js',
		'vendor/jquery/jquery.cookie.js',
		'vendor/jquery/jquery.history.js',
		'vendor/jquery/tmpl.min.js',

		'vendor/jquery/jquery.localscroll.js',
		'vendor/jquery/jquery.mousewheel.js',
		'vendor/jquery/jquery.scrollTo.js',
		'vendor/jquery/jquery.sizes.min.js',
		'vendor/jquery/jquery.tinyscrollbar.js',
		'vendor/jquery/jquery.hotkeys.js',
		'vendor/jquery/jquery.textarea-expander.js',
		'vendor/jqTree/tree.jquery.js',

		'vendor/jquery/jquery-checkbox/jquery.checkbox.js',

		'vendor/tiny_mce/jquery.tinymce.js',

		'vendor/redactor/redactor.js',

		'vendor/jquery/colorbox/jquery.colorbox-min.js',

		'vendor/jquery/fileupload/jquery.fileupload.js',
		'vendor/jquery/fileupload/jquery.fileupload-ui.js',
		'vendor/jquery/fileupload/jquery.iframe-transport.js',

		'vendor/jquery/qtip/jquery.qtip.min.js',
		'vendor/mootools/mootools-core.min.js',
		'vendor/tinycon/tinycon.min.js',

		'vendor/select2/select2.js',
		'vendor/ZeroClipboard/ZeroClipboard.min.js',
		'vendor/idbstore/idbstore.min.js',
		'vendor/twig/twig.js',
	)
);

$CONFIG['agent_settingswin'] = array(
	'out' => 'js/agent-settingswin.js',
	'files' => array(
		'javascripts/DeskPRO/Agent/ElementHandler/SettingsWindow.js',
		'javascripts/DeskPRO/Agent/PageFragment/SettingsPage/Profile.js',
		'javascripts/DeskPRO/Agent/PageFragment/SettingsPage/Signature.js',
		'javascripts/DeskPRO/Agent/PageFragment/SettingsPage/TicketNotifications.js',
		'javascripts/DeskPRO/Agent/PageFragment/SettingsPage/OtherNotifications.js',
		'javascripts/DeskPRO/Agent/PageFragment/SettingsPage/Macros.js',
		'javascripts/DeskPRO/Agent/PageFragment/SettingsPage/MacroEdit.js',
		'javascripts/DeskPRO/Agent/PageFragment/SettingsPage/Filters.js',
		'javascripts/DeskPRO/Agent/PageFragment/SettingsPage/FilterEdit.js',
		'javascripts/DeskPRO/Agent/PageFragment/SettingsPage/TicketSlas.js',

		'javascripts/DeskPRO/Agent/ElementHandler/MediaManagerWindow.js',
		'javascripts/DeskPRO/Agent/PageFragment/MediaManagerPage/Upload.js',
		'javascripts/DeskPRO/Agent/PageFragment/MediaManagerPage/Browse.js',
	)
);

$CONFIG['agent_window_sections'] = array(
	'out' => 'js/agent-window-sections.js',
	'files' => array(
		'javascripts/DeskPRO/Agent/WindowElement/Section/AbstractSection.js',
		'javascripts/DeskPRO/Agent/WindowElement/Section/Tickets.js',
		'javascripts/DeskPRO/Agent/WindowElement/Section/People.js',
		'javascripts/DeskPRO/Agent/WindowElement/Section/Publish.js',
		'javascripts/DeskPRO/Agent/WindowElement/Section/AgentChat.js',
		'javascripts/DeskPRO/Agent/WindowElement/Section/UserChat.js',
		'javascripts/DeskPRO/Agent/WindowElement/Section/Feedback.js',
        'javascripts/DeskPRO/Agent/WindowElement/Section/Tasks.js',
		'javascripts/DeskPRO/Agent/WindowElement/Section/Twitter.js',
        'javascripts/DeskPRO/Agent/WindowElement/Section/Deals.js',
	)
);

$CONFIG['agent_pages_lists'] = array(
	'out' => 'js/agent-pages-lists.js',
	'files' => array(
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/Basic.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/BasicTicketResults.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/TicketFilter.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/OrganizationList.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/PeopleList.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/TicketFlagged.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/TicketDeletedList.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/TicketCustomFilter.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/TicketCustomFilterForm.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/TicketSla.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/RecycleBin.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/KbPendingArticles.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/KbValidatingArticles.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/KbList.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/AgentChatHistory.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/AgentTeamChatHistory.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/OpenChats.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/UserChatFilter.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/FeedbackFilter.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/NewCustomFilter.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/NewsList.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/DownloadList.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/PublishListComments.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/PublishValidatingComments.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/PublishValidatingContent.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/PublishDraftsList.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/PublishSearch.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/FeedbackSearch.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/PublishSearchLog.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/FeedbackCommentsValidating.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/FeedbackContentValidating.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/TaskList.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/Search.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/DealList.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/TwitterFollowers.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/TwitterStatus.js',
		'javascripts/DeskPRO/Agent/PageFragment/ListPane/TwitterSearch.js',
	)
);

$CONFIG['agent_pages'] = array(
	'out' => 'js/agent-pages.js',
	'files' => array(
		'javascripts/DeskPRO/Agent/PageHelper/NewUserOverlay.js',
		'javascripts/DeskPRO/Agent/PageHelper/ListColDrag.js',
		'javascripts/DeskPRO/Agent/PageHelper/ListColResize.js',
		'javascripts/DeskPRO/Agent/PageHelper/TicketFields.js',
		'javascripts/DeskPRO/Agent/PageHelper/TicketFieldDisplay.js',
		'javascripts/DeskPRO/Agent/PageHelper/ChatFields.js',
		'javascripts/DeskPRO/Agent/PageHelper/ChatFieldDisplay.js',
		'javascripts/DeskPRO/Agent/PageHelper/ListSearchForm.js',
		'javascripts/DeskPRO/Agent/PageHelper/CategoryEdit.js',
		'javascripts/DeskPRO/Agent/PageHelper/DisplayOptions.js',
		'javascripts/DeskPRO/Agent/PageHelper/SelectionBar.js',
		'javascripts/DeskPRO/Agent/PageHelper/Popover.js',
		'javascripts/DeskPRO/Agent/PageHelper/FragmentOverlay.js',
		'javascripts/DeskPRO/Agent/PageHelper/ValidatingEdit.js',
		'javascripts/DeskPRO/Agent/PageHelper/RelatedContent.js',
		'javascripts/DeskPRO/Agent/PageHelper/RelatedContentList.js',
		'javascripts/DeskPRO/Agent/PageHelper/Comments.js',
		'javascripts/DeskPRO/Agent/PageHelper/MiscContent.js',
		'javascripts/DeskPRO/Agent/PageHelper/ListNav.js',
		'javascripts/DeskPRO/Agent/PageHelper/AutoSave.js',
		'javascripts/DeskPRO/Agent/PageHelper/StateSaver.js',
		'javascripts/DeskPRO/Agent/PageHelper/Results.js',
		'javascripts/DeskPRO/Agent/PageHelper/MassActions.js',
		'javascripts/DeskPRO/Agent/PageHelper/AcceptContentLink.js',
		'javascripts/DeskPRO/Agent/PageHelper/SendContentLink.js',
		'javascripts/DeskPRO/Agent/PageHelper/EditTitle.js',
		'javascripts/DeskPRO/Agent/PageHelper/TaskListControl.js',
		'javascripts/DeskPRO/Agent/PageHelper/TicketBilling.js',
		'javascripts/DeskPRO/Agent/PageHelper/Twitter.js',

		'javascripts/DeskPRO/Agent/PageFragment/Page/SnippetViewer.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/Ticket.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/Ticket/TicketLocked.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/Ticket/TicketActions.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/PersonHelper/ChangePic.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/PersonHelper/ContactEditor.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/Content/DeleteControl.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/Content/StickyWords.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/NewArticle.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/NewPerson.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/NewOrganization.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/NewDownload.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/NewNews.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/NewTicket.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/PublishNewCat.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/NewFeedback.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/Organization.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/Person.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/PersonSession.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/Visitor.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/PersonPopout.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/KbViewArticle.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/AgentChatTranscript.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/UserChat.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/FeedbackView.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/NewsView.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/DownloadsView.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/NewTask.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/NewTweet.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/Test.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/Deal.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/NewDeal.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/TwitterUser.js',
		'javascripts/DeskPRO/Agent/PageFragment/Page/TwitterStatusOverlay.js',

	)
);

$CONFIG['agent_element_handlers'] = array(
	'out' => 'js/agent-element-handlers.js',
	'files' => array(
		'javascripts/DeskPRO/Agent/ElementHandler/FormSaver.js',
		'javascripts/DeskPRO/Agent/ElementHandler/TicketReplyBox.js',
		'javascripts/DeskPRO/Agent/ElementHandler/TicketCcManage.js',
		'javascripts/DeskPRO/Agent/ElementHandler/SimpleAutoComplete.js',
		'javascripts/DeskPRO/Agent/ElementHandler/TabBox.js',
		'javascripts/DeskPRO/Agent/ElementHandler/PersonSearchBox.js',
		'javascripts/DeskPRO/Agent/ElementHandler/OrgSearchBox.js',
		'javascripts/DeskPRO/Agent/ElementHandler/TicketSearchBox.js',
		'javascripts/DeskPRO/Agent/ElementHandler/PhoneCountryCode.js',
		'javascripts/DeskPRO/Agent/ElementHandler/QuickSearch.js',
		'javascripts/DeskPRO/Agent/ElementHandler/PasswordPrompt.js',
		'javascripts/DeskPRO/Agent/ElementHandler/TimezoneSwitch.js',
		'javascripts/DeskPRO/Admin/ElementHandler/RadioExpander.js',
		'javascripts/DeskPRO/Agent/ElementHandler/FirstLogin.js',
		'javascripts/DeskPRO/Agent/ElementHandler/TwitterFeed.js',

		'javascripts/DeskPRO/Agent/SourcePane/SearchForm.js',
	)
);

$CONFIG['agent_common'] = array(
	'out' => 'js/agent-common.js',
	'files' => array(
		'javascripts/DeskPRO/DP.js',
		'javascripts/DeskPRO/ErrorLogger.js',
		'javascripts/Orb/Orb.js',
		'javascripts/Orb/Class.js',
		'javascripts/Orb/Util/Options.js',
		'javascripts/Orb/Util/Events.js',
		'javascripts/Orb/Util/EventObj.js',
		'javascripts/Orb/Util/TimeAgo.js',
		'javascripts/Orb/Util/CallQueue.js',
		'javascripts/Orb/Compat.js',
		'javascripts/DeskPRO/ElementHandler.js',
		'javascripts/DeskPRO/ElementHandler/ListRadio.js',
		'javascripts/DeskPRO/ElementHandler/SimpleTabs.js',
		'javascripts/DeskPRO/ElementHandler/CheckboxToggle.js',
		'javascripts/DeskPRO/ElementHandler/CheckboxCallUrl.js',
		'javascripts/DeskPRO/MessageBroker.js',
		'javascripts/DeskPRO/IntervalCaller.js',
		'javascripts/DeskPRO/TouchCaller.js',
		'javascripts/DeskPRO/WordHighlighter.js',
		'javascripts/DeskPRO/AjaxPoller/Poller.js',
		'javascripts/DeskPRO/AjaxPoller/MessagePoller.js',
		'javascripts/DeskPRO/MessageChanneler/AbstractChanneler.js',
		'javascripts/DeskPRO/MessageChanneler/AjaxChanneler.js',
		'javascripts/DeskPRO/Translate.js',
		'javascripts/DeskPRO/Agent/RteEditor.js',
		'javascripts/DeskPRO/TextExpander.js',
	)
);

$CONFIG['agent_agent_ui'] = array(
	'out' => 'js/agent-ui.js',
	'files' => array(
		'javascripts/DeskPRO/BasicWindow.js',
		'javascripts/DeskPRO/Agent/Window.js',
		'javascripts/DeskPRO/Agent/Layout/DeskproWindow.js',
		'javascripts/DeskPRO/Agent/TabWatcher.js',
		'javascripts/DeskPRO/Agent/ScrollerHandler.js',
		'javascripts/DeskPRO/Agent/KeyboardShortcuts.js',
		'javascripts/DeskPRO/Agent/Notifications.js',
		'javascripts/DeskPRO/Agent/RecentTabs.js',

		'javascripts/DeskPRO/Agent/PageFragment/Basic.js',
		'javascripts/DeskPRO/Agent/PageFragment/Loading.js',

		'javascripts/DeskPRO/Agent/WindowElement/TabWatcher/Tickets.js',
		'javascripts/DeskPRO/Agent/WindowElement/TabWatcher/UserChat.js',
		'javascripts/DeskPRO/Agent/WindowElement/TabBar.js',
		'javascripts/DeskPRO/Agent/WindowElement/TabBarOverflow.js',
		'javascripts/DeskPRO/Agent/TextSnippetClientDbDriver.js',
		'javascripts/DeskPRO/Agent/TextSnippetAjaxDriver.js',
	)
);

$CONFIG['agent_deskpro_ui'] = array(
	'out' => 'js/agent-deskpro-ui.js',
	'files' => array(
		'javascripts/DeskPRO/UI/LabelsInput.js',
		'javascripts/DeskPRO/UI/Overlay.js',
		'javascripts/DeskPRO/UI/OptionBox.js',
		'javascripts/DeskPRO/UI/OptionBoxRevertable.js',
		'javascripts/DeskPRO/UI/OptionBoxBuilder.js',
		'javascripts/DeskPRO/UI/Menu.js',
		'javascripts/DeskPRO/UI/SimpleTabs.js',
		'javascripts/DeskPRO/UI/DateChooser.js',
		'javascripts/DeskPRO/UI/CatListEditor.js',
		'javascripts/DeskPRO/UI/Select/Widget.js',
		'javascripts/DeskPRO/UI/Select/Menu.js',
		'javascripts/DeskPRO/UI/Select/WidgetSimple.js',
		'javascripts/DeskPRO/UI/Select/MenuHtml.js',
	)
);

$CONFIG['agent_misc'] = array(
	'out' => 'js/agent-misc.js',
	'files' => array(
		'javascripts/DeskPRO/Form/InlineEdit.js',
		'javascripts/DeskPRO/Form/RuleBuilder.js',
		'javascripts/DeskPRO/FaviconBadge.js',
		'javascripts/DeskPRO/Agent/InterfaceEffects.js',

		'javascripts/DeskPRO/Agent/Widget/FindPerson.js',
		'javascripts/DeskPRO/Agent/Widget/AgentSelector.js',
		'javascripts/DeskPRO/Agent/Widget/SnippetViewer.js',
		'javascripts/DeskPRO/Agent/Widget/TicketChangeUser.js',
		'javascripts/DeskPRO/Agent/Widget/Merge.js',
		'javascripts/DeskPRO/Agent/Widget/AgentChatWin.js',

		'javascripts/DeskPRO/Agent/Widget/BackgroundPopout.js',

		'javascripts/DeskPRO/Agent/RuleBuilder/TermAbstract.js',
		'javascripts/DeskPRO/Agent/RuleBuilder/DateTerm.js',
		'javascripts/DeskPRO/Agent/RuleBuilder/LabelsTerm.js',
		'javascripts/DeskPRO/Agent/RuleBuilder/SelectNewOption.js',

		'javascripts/DeskPRO/Agent/Ticket/ChangeManager.js',
		'javascripts/DeskPRO/Agent/Ticket/Property/Abstract.js',
		'javascripts/DeskPRO/Agent/Ticket/Property/Agent.js',
		'javascripts/DeskPRO/Agent/Ticket/Property/Department.js',
		'javascripts/DeskPRO/Agent/Ticket/Property/AgentTeam.js',
		'javascripts/DeskPRO/Agent/Ticket/Property/StandardOption.js',
		'javascripts/DeskPRO/Agent/Ticket/Property/Status.js',
		'javascripts/DeskPRO/Agent/Ticket/Property/Reply.js',
		'javascripts/DeskPRO/Agent/Ticket/Property/TicketField.js',
		'javascripts/DeskPRO/Agent/Ticket/Property/Urgency.js',
		'javascripts/DeskPRO/Agent/Ticket/Property/Flag.js',
		'javascripts/DeskPRO/Agent/Ticket/Property/Labels.js',
		'javascripts/DeskPRO/Agent/Ticket/Property/Hold.js',

		'javascripts/DeskPRO/Agent/TicketList/MassActions.js',
		'javascripts/DeskPRO/Agent/TicketList/ListView.js',
		'javascripts/DeskPRO/Agent/PageHelper/PeopleList/ListView.js',

		'javascripts/DeskPRO/Agent/TicketList/ChangeManager.js',
		'javascripts/DeskPRO/Agent/TicketList/Property/Abstract.js',
		'javascripts/DeskPRO/Agent/TicketList/Property/StandardOption.js',
		'javascripts/DeskPRO/Agent/TicketList/Property/NewReply.js',
		'javascripts/DeskPRO/Agent/TicketList/Property/TicketField.js',
		'javascripts/DeskPRO/Agent/TicketList/Property/Flag.js',
		'javascripts/DeskPRO/Agent/TicketList/Property/Labels.js',
	)
);


/**
 * Admin UI specific
 */
$CONFIG['admin_admin_ui'] = array(
	'out' => 'js/admin-ui.js',
	'files' => array(
		'javascripts/DeskPRO/BasicWindow.js',
		'javascripts/DeskPRO/Admin/Window.js',
		'javascripts/DeskPRO/Admin/PopoutWindow.js',
		'javascripts/DeskPRO/Admin/PopoutWindow.js',
		'javascripts/DeskPRO/Admin/PageHandler/Basic.js',
		'javascripts/DeskPRO/Admin/TableReorder.js',
		'javascripts/DeskPRO/Form/RuleBuilder.js',
		'javascripts/DeskPRO/Agent/RuleBuilder/TermAbstract.js',
		'javascripts/DeskPRO/Admin/RuleBuilder/TemplateEdit.js',
		'javascripts/DeskPRO/Agent/RuleBuilder/DateTerm.js',
		'javascripts/DeskPRO/Agent/RuleBuilder/LabelsTerm.js',
		'javascripts/DeskPRO/Agent/RuleBuilder/SelectNewOption.js',
		'javascripts/DeskPRO/FormValidator/FormValidator.js',
		'javascripts/DeskPRO/FormValidator/FieldValidator.js',
		'javascripts/DeskPRO/FormValidator/LengthValidator.js',
		'javascripts/DeskPRO/FormValidator/EmailValidator.js',
		'javascripts/DeskPRO/FormValidator/RegexValidator.js',
	)
);

/**
 * Admin UI specific
 */
$CONFIG['admin_admin_handlers'] = array(
	'out' => 'js/admin-handlers.js',
	'files' => array(
		'javascripts/DeskPRO/Admin/Departments/AjaxSave.js',
		'javascripts/DeskPRO/Admin/Departments/AgentSelector.js',
		'javascripts/DeskPRO/Admin/Departments/UsergroupSelector.js',
		'javascripts/DeskPRO/Admin/ElementHandler/HeaderSetupGuide.js',
		'javascripts/DeskPRO/Admin/ElementHandler/TicketPropertiesList.js',
		'javascripts/DeskPRO/Admin/ElementHandler/TaskQueueStatus.js',
		'javascripts/DeskPRO/Admin/ElementHandler/CustomFieldList.js',
		'javascripts/DeskPRO/Admin/ElementHandler/ChoiceBuilder.js',
		'javascripts/DeskPRO/Admin/ElementHandler/SimpleHierarchyBuilder.js',
		'javascripts/DeskPRO/Admin/ElementHandler/PortalNav.js',
		'javascripts/DeskPRO/Admin/ElementHandler/PortalToggle.js',
		'javascripts/DeskPRO/Admin/ElementHandler/PortalEditor.js',
		'javascripts/DeskPRO/Admin/ElementHandler/LabelsPage.js',
		'javascripts/DeskPRO/Admin/ElementHandler/RadioExpander.js',
		'javascripts/DeskPRO/Admin/ElementHandler/DashVersion.js',
		'javascripts/DeskPRO/Admin/ElementHandler/DashNotice.js',
	)
);

/**
 * Report specific
 */
$CONFIG['report_graphs'] = array(
	'out' 	=> 'js/report_graphs.js',
	'files'	=> array(
		'vendor/jquery/sparkline/jquery.sparkline.min.js',
	),
);

/**
 * Report builder specific
 */
$CONFIG['report_builder'] = array(
	'out' 	=> 'js/report_builder.js',
	'files'	=> array(
		'javascripts/DeskPRO/Report/ElementHandler/Builder/BuilderTabs.js',
		'javascripts/DeskPRO/Report/ElementHandler/Builder/ListCollapse.js',
		'javascripts/DeskPRO/Report/ElementHandler/Builder/ReportList.js',
		'javascripts/DeskPRO/Report/ElementHandler/Builder/ReportList.js',
		'javascripts/DeskPRO/Report/PageHandler/ReportBuilder.js',
		'vendor/jquery/jquery.history.js',
		'vendor/jquery/jquery.scrollTo.js',
		'vendor/jquery/jquery.textarea-expander.js',
		//'vendor/amcharts/javascript/amcharts.js',
		//'vendor/amcharts/javascript/amfallback.js',
		//'vendor/amcharts/javascript/raphael.js',
		'vendor/amcharts/javascript/amcharts27.js',
	),
);


$CONFIG['report_report_ui'] = array(
	'out' => 'js/report-ui.js',
	'files' => array(
		'javascripts/DeskPRO/Report/Window.js',
		'javascripts/DeskPRO/Report/PageHandler/Basic.js',
	)
);

$CONFIG['user_helpdeskwin'] = array(
	'out' => 'js/HelpdeskWin.min.js',
	'post_filters' => array('yui_simple'),
	'files' => array(
		'javascripts/DeskPRO/User/HelpdeskWidget/HelpdeskWin.js',
	)
);

$CONFIG['user'] = array(
	'out' => 'js/user-all.js',
	'post_filters' => array('yui_simple'),
	'references' => array(
		'user_vendors',
		'user_common',
	)
);

$CONFIG['user_portaladmin'] = array(
	'out' => 'js/user-portaladmin.js',
	'files' => array(
		'javascripts/DeskPRO/UserPortalAdmin/PortalAdmin.js',
	)
);

$CONFIG['user_portaladmin_css'] = array(
	'out' => 'css/user-portaladmin.css',
	'filters' => array('less', 'css'),
	'files' => array(
		'stylesheets-less/admin/portal-admin.less',
	)
);

$CONFIG['user_common'] = array(
	'out' => 'js/user-common.js',
	'files' => array(
		'javascripts/Orb/Orb.js',
		'javascripts/DeskPRO/DP.js',
		'javascripts/Orb/Class.js',
		'javascripts/Orb/Util/Options.js',
		'javascripts/Orb/Util/Events.js',
		'javascripts/Orb/Util/TimeAgo.js',
		'javascripts/Orb/Compat.js',
		'javascripts/DeskPRO/IntervalCaller.js',
		'javascripts/DeskPRO/MessageBroker.js',
		'javascripts/DeskPRO/BasicWindow.js',
		'javascripts/DeskPRO/UI/SimpleTabs.js',
		'javascripts/DeskPRO/UI/TwoLevelSelect.js',
		'javascripts/DeskPRO/UI/Overlay.js',
		'javascripts/DeskPRO/User/Window.js',
		'javascripts/DeskPRO/TouchCaller.js',
		'javascripts/DeskPRO/Translate.js',

		'javascripts/DeskPRO/User/ElementHandler/ElementHandlerAbstract.js',
		'javascripts/DeskPRO/User/ElementHandler/MoreLoader.js',
		'javascripts/DeskPRO/User/ElementHandler/LoginBox.js',
		'javascripts/DeskPRO/User/ElementHandler/NewTicket.js',
		'javascripts/DeskPRO/User/ElementHandler/FormUploadHandler.js',
		'javascripts/DeskPRO/User/ElementHandler/TicketList.js',
		'javascripts/DeskPRO/User/ElementHandler/TicketView.js',
		'javascripts/DeskPRO/User/ElementHandler/InlineEmailManage.js',
		'javascripts/DeskPRO/User/ElementHandler/CommentFormLogin.js',
		'javascripts/DeskPRO/User/ElementHandler/FeedbackAgreeBtn.js',
		'javascripts/DeskPRO/User/ElementHandler/OmniSearch.js',

		'javascripts/DeskPRO/User/SuggestedContentOverlay.js',
		'javascripts/DeskPRO/User/InlineSuggestions.js',
		'javascripts/DeskPRO/User/InlineLoginForm.js',

		'javascripts/DeskPRO/FormValidator/FormValidator.js',
		'javascripts/DeskPRO/FormValidator/FieldValidator.js',
		'javascripts/DeskPRO/FormValidator/LengthValidator.js',
		'javascripts/DeskPRO/FormValidator/EmailValidator.js',
		'javascripts/DeskPRO/FormValidator/RegexValidator.js',
		'javascripts/DeskPRO/FormValidator/TwoLevelSelectValidator.js',
	)
);

$CONFIG['user_vendors'] = array(
	'out' => 'js/user-vendors.js',
	'files' => array(
		'vendor/modernizr.min.js',
		'vendor/html5shiv.min.js',
		'javascripts/Orb/modernizr-ext.js',

		'vendor/jquery/jquery.min.js',
		'vendor/jquery/jquery-ui/jquery-ui.min.js',
		'vendor/jquery/jquery.cookie.js',
		'vendor/jquery/jquery.history.js',
		'vendor/jquery/tmpl.min.js',

		'vendor/jquery/jquery.sizes.min.js',

		'vendor/jquery/jquery.uniform.min.js',

		'vendor/jquery/fileupload/jquery.fileupload.js',
		'vendor/jquery/fileupload/jquery.fileupload-ui.js',
		'vendor/jquery/fileupload/jquery.iframe-transport.js',

		'vendor/mootools/mootools-core.min.js',
		'vendor/PIE/PIE.js',
		'vendor/bootstrap/bootstrap-custom.js',
	)
);



###############################################################################
# CSS
###############################################################################

$CONFIG['agent_css1'] = array(
	'out' => 'css/agent-pack1.css',
	'post_filters' => array('smartsprites', 'css', 'image_gradients'),
	'references' => array(
		'agent_interface_css1',
	)
);

$CONFIG['agent_css2'] = array(
	'out' => 'css/agent-pack2.css',
	'post_filters' => array('smartsprites', 'css', 'image_gradients'),
	'references' => array(
		'agent_interface_css2',
	)
);

$CONFIG['agent_vendor_out_css'] = array(
	'out' => 'css/agent-vendors-all.css',
	'post_filters' => array('css'),
	'references' => array(
		'agent_vendors_css',
	)
);

$CONFIG['admin_interface_css'] = array(
	'out' => 'css/admin-interface.css',
	'filters' => array('less'),
	'files' => array(
		'stylesheets-less/admin/main.less',
	)
);

$CONFIG['agent_interface_css1'] = array(
	'out' => 'css/agent-interface1.css',
	'filters' => array('less'),
	'files' => array(
		'stylesheets-less/agent/dp-interface.less',
		'stylesheets-less/agent/dp-agent-chat.less',
		'stylesheets-less/agent/overlayCreateTicket.less',
	)
);

$CONFIG['agent_interface_css2'] = array(
	'out' => 'css/agent-interface2.css',
	'filters' => array('less'),
	'files' => array(
		'stylesheets-less/agent/dp-source-pane.less',
		'stylesheets-less/agent/dp-list-pane.less',
		'stylesheets-less/agent/dp-content-pane.less',
		'stylesheets-less/agent/agent.less',
	)
);

$CONFIG['agent_interface_ie_css'] = array(
	'out' => 'css/agent-interface-ie.css',
	'filters' => array('less'),
	'files' => array(
		'stylesheets-less/agent/agent-ie.less',
	)
);


$CONFIG['agent_interface_print_css'] = array(
	'out' => 'css/agent-interface-print.css',
	'filters' => array('less'),
	'media' => 'print',
	'files' => array(
		'stylesheets-less/agent/print.less',
	)
);

$CONFIG['agent_vendors_css'] = array(
	'out' => 'css/agent-vendors.css',
	'filters' => array('css_path'),
	'files' => array(
		'stylesheets/vendor/jquery-ui/dp-theme/jquery-ui.css',
		'vendor/jquery/qtip/jquery.qtip.min.css',
		'vendor/jquery/colorbox/colorbox.css',
		'vendor/select2/select2.css',
		'vendor/redactor/redactor.css',
	)
);

$CONFIG['report_interface_css'] = array(
	'out' => 'css/report-interface.css',
	'filters' => array('less'),
	'media' => 'screen',
	'files' => array(
		'stylesheets-less/report/main.less',
	)
);
