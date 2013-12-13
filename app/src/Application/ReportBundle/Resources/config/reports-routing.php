<?php if (!defined('DP_ROOT')) exit('No access');

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('report_login', new Route(
	'/login',
	array('_controller' => 'ReportBundle:Login:index'),
	array(),
	array()
));

$collection->add('report_logout', new Route(
	'/logout/{auth}',
	array('_controller' => 'ReportBundle:Login:logout'),
	array(),
	array()
));

$collection->add('report_login_authenticate_local', new Route(
	'/login/authenticate-password',
	array('_controller' => 'ReportBundle:Login:authenticateLocal', 'usersource_id' => 0),
	array(),
	array()
));

$collection->add('report', new Route(
	'/',
	array('_controller' => 'ReportBundle:Overview:index'),
	array(),
	array()
));

$collection->add('report_overview_update_stat', new Route(
	'/overview/update-stat/{type}',
	array('_controller' => 'ReportBundle:Overview:updateStat'),
	array(),
	array()
));

################################################################################
# Agent Hours
################################################################################

$collection->add('report_agent_hours_index', new Route(
    '/agent-hours',
    array('_controller' => 'ReportBundle:AgentHours:index'),
    array(),
    array()
));

$collection->add('report_agent_hours_list_date', new Route(
    '/agent-hours/{date}/{date2}',
    array('_controller' => 'ReportBundle:AgentHours:list', 'date2' => ''),
    array(),
    array()
));

################################################################################
# Agent Activity
################################################################################

$collection->add('report_agent_activity_index', new Route(
    '/agent-activity',
    array('_controller' => 'ReportBundle:AgentActivity:index'),
    array(),
    array()
));

$collection->add('report_agent_activity_list', new Route(
    '/agent-activity/list/{agent_or_team_id}/{date}',
    array('_controller' => 'ReportBundle:AgentActivity:list'),
    array(),
    array()
));

################################################################################
# Agent Feedback
################################################################################

$collection->add('report_agent_feedback_summary', new Route(
    '/agent-feedback/summary/{date}',
    array('_controller' => 'ReportBundle:AgentFeedback:summary', 'date' => ''),
    array(),
    array()
));

$collection->add('report_agent_feedback_feed', new Route(
	'/agent-feedback/{page}',
	array('_controller' => 'ReportBundle:AgentFeedback:feed', 'page' => '0'),
	array(),
	array()
));

################################################################################
# Publish
################################################################################

$collection->add('report_publish', new Route(
	'/publish',
	array('_controller' => 'ReportBundle:ReportBuilder:index'),
	array(),
	array()
));

################################################################################
# Report Builder
################################################################################

$collection->add('report_builder', new Route(
	'/report-builder',
	array('_controller' => 'ReportBundle:ReportBuilder:index'),
	array(),
	array()
));

$collection->add('report_builder_query', new Route(
	'/report-builder/query',
	array('_controller' => 'ReportBundle:ReportBuilder:query'),
	array(),
	array()
));

$collection->add('report_builder_parse', new Route(
	'/report-builder/parse',
	array('_controller' => 'ReportBundle:ReportBuilder:parse'),
	array(),
	array()
));

$collection->add('report_builder_new', new Route(
	'/report-builder/new',
	array('_controller' => 'ReportBundle:ReportBuilder:edit', 'report_builder_id' => 0),
	array(),
	array()
));

$collection->add('report_builder_report', new Route(
	'/report-builder/{report_builder_id}/',
	array('_controller' => 'ReportBundle:ReportBuilder:report'),
	array('report_builder_id' => '\\d+'),
	array()
));

$collection->add('report_builder_edit', new Route(
	'/report-builder/{report_builder_id}/edit',
	array('_controller' => 'ReportBundle:ReportBuilder:edit'),
	array('report_builder_id' => '\\d+'),
	array()
));

$collection->add('report_builder_delete', new Route(
	'/report-builder/{report_builder_id}/delete',
	array('_controller' => 'ReportBundle:ReportBuilder:delete'),
	array('report_builder_id' => '\\d+'),
	array()
));

$collection->add('report_builder_favorite', new Route(
	'/report-builder/{report_builder_id}/favorite',
	array('_controller' => 'ReportBundle:ReportBuilder:favorite'),
	array('report_builder_id' => '\\d+'),
	array()
));

################################################################################
# Billing
################################################################################

$collection->add('report_billing', new Route(
	'/billing',
	array('_controller' => 'ReportBundle:Billing:index'),
	array(),
	array()
));

$collection->add('report_billing_report', new Route(
	'/billing/{report_id}',
	array('_controller' => 'ReportBundle:Billing:report'),
	array(),
	array()
));

return $collection;