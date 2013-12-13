<?php if (!defined('DP_ROOT')) exit('No access');

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('proxy', new Route(
	'/proxy/{key}',
	array('_controller' => 'DeskPRO:Widget:proxy'),
	array(),
	array()
));

################################################################################
# File serving
################################################################################

$collection->add('serve_blob', new Route(
	'/file.php/{blob_auth_id}/{filename}',
	array('_controller' => '(see: serve_file.php)'),
	array(),
	array()
));

$collection->add('serve_blob_size', new Route(
	'/file.php/size/{s}/{blob_auth_id}/{filename}',
	array('_controller' => '(see: serve_file.php)'),
	array(),
	array()
));

$collection->add('serve_blob_sizefit', new Route(
	'/file.php/size/{s}/size-fit/{blob_auth_id}/{filename}',
	array('_controller' => '(see: serve_file.php)'),
	array(),
	array()
));

$collection->add('serve_person_picture', new Route(
	'/file.php/avatar/{person_id}',
	array('_controller' => '(see: serve_file.php)', 'size' => 0),
	array('person_id' => '\\d+'),
	array()
));

$collection->add('serve_person_picture_size', new Route(
	'/file.php/avatar/{person_id}',
	array('_controller' => '(see: serve_file.php)'),
	array('person_id' => '\\d+', 'size' => '\\d+'),
	array()
));

$collection->add('serve_default_picture', new Route(
	'/file.php/avatar/{s}/default.jpg',
	array('_controller' => '(see: serve_file.php)', 'name' => 'default_picture', 's' => '0'),
	array(),
	array()
));


$collection->add('favicon', new Route(
	'/favicon.ico',
	array('_controller' => 'DeskPRO:Blob:favicon'),
	array(),
	array()
));

$collection->add('serve_org_picture_default', new Route(
	'/file.php/o-avatar/default',
	array('_controller' => '(see: serve_file.php)'),
	array(),
	array()
));

$collection->add('serve_org_picture', new Route(
	'/file.php/o-avatar/{org_id}',
	array('_controller' => '(see: serve_file.php)'),
	array('person_id' => '\\d+'),
	array()
));

################################################################################
# Error Logging
################################################################################

$collection->add('sys_log_js_error', new Route(
	'/dp/log-js-error.json',
	array('_controller' => 'DeskPRO:Data:logJsError'),
	array(),
	array()
));

$collection->add('sys_report_error', new Route(
	'/dp/report-error.json',
	array('_controller' => 'DeskPRO:Data:sendErrorReport'),
	array(),
	array()
));

################################################################################
# Data
################################################################################

$collection->add('data_interface_data', new Route(
	'/data/interface-data.{_format}',
	array('_controller' => 'DeskPRO:Data:interfaceData', '_format' => 'js'),
	array('_format' => 'js'),
	array()
));

################################################################################
# DeskPRO 3 Redirects
################################################################################

$collection->add('dp3_redirect_files_php', new Route(
	'/files.php',
	array('_controller' => 'DeskPRO:Deskpro3Redirect:downloadCat'),
	array(),
	array()
));

$collection->add('dp3_redirect_attachment_files_php', new Route(
	'/attachment_files.php',
	array('_controller' => 'DeskPRO:Deskpro3Redirect:downloadView'),
	array(),
	array()
));

$collection->add('dp3_redirect_ideas_php', new Route(
	'/ideas.php',
	array('_controller' => 'DeskPRO:Deskpro3Redirect:feedback'),
	array(),
	array()
));

$collection->add('dp3_redirect_kb_article_php', new Route(
	'/kb_article.php',
	array('_controller' => 'DeskPRO:Deskpro3Redirect:articleView'),
	array(),
	array()
));

$collection->add('dp3_redirect_kb_cat_php', new Route(
	'/kb_cat.php',
	array('_controller' => 'DeskPRO:Deskpro3Redirect:articleCat'),
	array(),
	array()
));

$collection->add('dp3_redirect_kb_php', new Route(
	'/kb.php',
	array('_controller' => 'DeskPRO:Deskpro3Redirect:articlesHome'),
	array(),
	array()
));

$collection->add('dp3_redirect_login_php', new Route(
	'/login.php',
	array('_controller' => 'DeskPRO:Deskpro3Redirect:login'),
	array(),
	array()
));

$collection->add('dp3_redirect_manual_php', new Route(
	'/manual.php',
	array('_controller' => 'DeskPRO:Deskpro3Redirect:manuals'),
	array(),
	array()
));

$collection->add('dp3_redirect_manual_rewritten', new Route(
	'/manual/{manual_bit}/{page_bit}',
	array('_controller' => 'DeskPRO:Deskpro3Redirect:rewrittenManuals', 'page_bit' => ''),
	array(),
	array()
));

$collection->add('dp3_redirect_manual_download_php', new Route(
	'/manual_download.php',
	array('_controller' => 'DeskPRO:Deskpro3Redirect:manuals'),
	array(),
	array()
));

$collection->add('dp3_redirect_news_archive_php', new Route(
	'/news_archive.php',
	array('_controller' => 'DeskPRO:Deskpro3Redirect:newsArchive'),
	array(),
	array()
));

$collection->add('dp3_redirect_news_full_php', new Route(
	'/news_full.php',
	array('_controller' => 'DeskPRO:Deskpro3Redirect:newsView'),
	array(),
	array()
));

$collection->add('dp3_redirect_news_php', new Route(
	'/news.php',
	array('_controller' => 'DeskPRO:Deskpro3Redirect:newsView'),
	array(),
	array()
));

$collection->add('dp3_redirect_newticket_php', new Route(
	'/newticket.php',
	array('_controller' => 'DeskPRO:Deskpro3Redirect:newTicket'),
	array(),
	array()
));

$collection->add('dp3_redirect_profile_email_php', new Route(
	'/profile_email.php',
	array('_controller' => 'DeskPRO:Deskpro3Redirect:profile'),
	array(),
	array()
));

$collection->add('dp3_redirect_profile_password_php', new Route(
	'/profile_password.php',
	array('_controller' => 'DeskPRO:Deskpro3Redirect:profile'),
	array(),
	array()
));

$collection->add('dp3_redirect_profile_php', new Route(
	'/profile.php',
	array('_controller' => 'DeskPRO:Deskpro3Redirect:profile'),
	array(),
	array()
));

$collection->add('dp3_redirect_register_php', new Route(
	'/register.php',
	array('_controller' => 'DeskPRO:Deskpro3Redirect:register'),
	array(),
	array()
));

$collection->add('dp3_redirect_reset_php', new Route(
	'/reset.php',
	array('_controller' => 'DeskPRO:Deskpro3Redirect:login'),
	array(),
	array()
));

$collection->add('dp3_redirect_ticketlist_php', new Route(
	'/ticketlist.php',
	array('_controller' => 'DeskPRO:Deskpro3Redirect:ticketList'),
	array(),
	array()
));

$collection->add('dp3_redirect_ticketlist_company_php', new Route(
	'/ticketlist_company.php',
	array('_controller' => 'DeskPRO:Deskpro3Redirect:ticketList'),
	array(),
	array()
));

$collection->add('dp3_redirect_ticketlist_participate_php', new Route(
	'/ticketlist_participate.php',
	array('_controller' => 'DeskPRO:Deskpro3Redirect:ticketList'),
	array(),
	array()
));

$collection->add('dp3_redirect_troubleshooter_php', new Route(
	'/troubleshooter.php',
	array('_controller' => 'DeskPRO:Deskpro3Redirect:troubles'),
	array(),
	array()
));

$collection->add('dp3_redirect_view_php', new Route(
	'/view.php',
	array('_controller' => 'DeskPRO:Deskpro3Redirect:ticketView'),
	array(),
	array()
));

return $collection;
