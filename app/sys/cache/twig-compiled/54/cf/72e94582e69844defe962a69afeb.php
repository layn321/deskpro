<?php

/* AdminBundle::layout.html.twig */
class __TwigTemplate_54cf72e94582e69844defe962a69afeb extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'html_headjs_load' => array($this, 'block_html_headjs_load'),
            'html_head' => array($this, 'block_html_head'),
            'page_js_exec' => array($this, 'block_page_js_exec'),
            'header' => array($this, 'block_header'),
            'pagebar' => array($this, 'block_pagebar'),
            'pagetabs' => array($this, 'block_pagetabs'),
            'pagebar_after' => array($this, 'block_pagebar_after'),
            'sidebar' => array($this, 'block_sidebar'),
            'sidebar_right' => array($this, 'block_sidebar_right'),
            'dp_full_page' => array($this, 'block_dp_full_page'),
            'prepage' => array($this, 'block_prepage'),
            'content' => array($this, 'block_content'),
            'page' => array($this, 'block_page'),
            'page_nav' => array($this, 'block_page_nav'),
            'page_nav_inner' => array($this, 'block_page_nav_inner'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.setup_initial"), "method")) {
            $context["is_post_install"] = false;
        } else {
            $context["is_post_install"] = true;
        }
        // line 2
        echo "<!DOCTYPE HTML>
<html lang=\"en\">
<head>
\t<meta http-equiv=\"X-UA-Compatible\" content=\"IE=Edge,chrome=1\" />
\t<title>";
        // line 6
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.deskpro_admin_interface");
        echo "</title>
\t<meta charset=\"utf-8\" />
\t<meta name=\"robots\" content=\"noindex,nofollow\" />
\t<link rel=\"shortcut icon\" id=\"favicon\" href=\"";
        // line 9
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("images/admin/favicon-admin.ico"), "html", null, true);
        echo "\" />

\t<script type=\"text/javascript\">
\t\t";
        // line 12
        if ($this->env->getExtension('deskpro_templating')->getConstant("DP_DEBUG")) {
            // line 13
            echo "\t\t\tDP_DEBUG = true;
\t\t";
        } else {
            // line 15
            echo "\t\t\tDP_DEBUG = false;
\t\t";
        }
        // line 17
        echo "\t\t";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "isCloud", array(), "method")) {
            echo "DP_IS_CLOUD = true;";
        } else {
            echo "DP_IS_CLOUD = false;";
        }
        // line 18
        echo "\t</script>

\t<link rel=\"stylesheet\" href=\"";
        // line 20
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("fonts/font-awesome.min.css"), "html", null, true);
        echo "\" type=\"text/css\" />

\t";
        // line 22
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("agent_vendors_css");
        echo "
\t";
        // line 23
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("agent_interface_css1");
        echo "
\t";
        // line 24
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("agent_interface_css2");
        echo "
\t";
        // line 25
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("admin_interface_css");
        echo "

\t<script type=\"text/javascript\" src=\"";
        // line 27
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("vendor/html5shiv.min.js"), "html", null, true);
        echo "\"></script>
\t";
        // line 28
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("agent_vendors");
        echo "
\t";
        // line 29
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("agent_common");
        echo "
\t";
        // line 30
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("agent_deskpro_ui");
        echo "
    ";
        // line 31
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("admin_admin_ui");
        echo "
    ";
        // line 32
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("admin_admin_handlers");
        echo "

    <script type=\"text/javascript\" charset=\"utf-8\">

\t\tvar DP_REQUEST_TOKEN = '";
        // line 36
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->securityToken("request_token", 10800), "html", null, true);
        echo "';

\t\t\$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
\t\t\tvar url = options.url;
\t\t\tif (url.indexOf('?') == -1) {
\t\t\t\turl += '?';
\t\t\t} else {
\t\t\t\turl += '&';
\t\t\t}
\t\t\turl += '_rt=' + DP_REQUEST_TOKEN;

\t\t\toptions.url = url;
\t\t});

\t\t\$(document).ready(function() {
\t\t\t\$('form').each(function() {
\t\t\t\tvar form = \$(this);
\t\t\t\tvar method = (form.attr('method') || 'GET').toUpperCase();
\t\t\t\tvar tok = null;

\t\t\t\tif (method == 'POST') {
\t\t\t\t\ttok = form.find('input.dp_request_token');
\t\t\t\t\tif (!tok[0]) {
\t\t\t\t\t\ttok = \$('<input type=\"hidden\" name=\"_rt\" />');
\t\t\t\t\t\ttok.val(DP_REQUEST_TOKEN);
\t\t\t\t\t\ttok.addClass('dp_request_token');

\t\t\t\t\t\tform.append(tok);
\t\t\t\t\t}
\t\t\t\t}
\t\t\t}).on('submit', function() {
\t\t\t\tvar form = \$(this);
\t\t\t\tvar method = (form.attr('method') || 'GET').toUpperCase();
\t\t\t\tvar tok = null;

\t\t\t\tif (method == 'POST') {
\t\t\t\t\ttok = form.find('input.dp_request_token');
\t\t\t\t\tif (!tok[0]) {
\t\t\t\t\t\ttok = \$('<input type=\"hidden\" name=\"_rt\" />');
\t\t\t\t\t\ttok.val(DP_REQUEST_TOKEN);
\t\t\t\t\t\ttok.addClass('dp_request_token');

\t\t\t\t\t\tform.append(tok);
\t\t\t\t\t} else {
\t\t\t\t\t\ttok.val(DP_REQUEST_TOKEN);
\t\t\t\t\t}
\t\t\t\t}
\t\t\t});
\t\t});

\t\tvar DP_TINYMCE_URL = '";
        // line 86
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("javascripts/tiny_mce/tiny_mce.js"), "html", null, true);
        echo "';
\t\tvar BASE_URL = '";
        // line 87
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
        echo "/';
\t\tvar BASE_PATH = '";
        // line 88
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, strtr($this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), array("/index.php" => "")), "html", null, true);
        echo "/';
\t\tvar ASSETS_BASE_URL = '";
        // line 89
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl(""), "html", null, true);
        echo "';
\t\tif (!window.DESKPRO_URL_REGISTRY) window.DESKPRO_URL_REGISTRY = {};

\t\twindow.DeskPRO_Window = new DeskPRO.Admin.Window();

\t\tfunction _win_exec() {
\t\t\twindow.DeskPRO_Window.initPage();

\t\t\tif (window._page_exec) {
\t\t\t\t_page_exec();
\t\t\t}

\t\t\tif (window.DeskPRO_Page) {
\t\t\t\tDeskPRO_Page.initPage();
\t\t\t}
\t\t}

\t\t";
        // line 106
        $this->displayBlock('html_headjs_load', $context, $blocks);
        // line 107
        echo "
\t\t\$(document).ready(function() {
\t\t\t_win_exec();
\t\t});
\t</script>
\t";
        // line 112
        $this->displayBlock('html_head', $context, $blocks);
        // line 113
        echo "\t";
        $this->displayBlock('page_js_exec', $context, $blocks);
        // line 114
        echo "
\t<script type=\"text/javascript\" src=\"";
        // line 115
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("vendor/less/less.min.js"), "html", null, true);
        echo "\"></script>

\t";
        // line 117
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "isDebug", array(), "method")) {
            // line 118
            echo "\t\t<script src=\"";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, strtr($this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), array("/index.php" => "")), "html", null, true);
            echo "/dp.php/agent-lang-";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "language"), "id"), "html", null, true);
            echo ".js?nocache=1&v=";
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->securityToken("lang"), "html", null, true);
            echo "\"></script>
\t";
        } else {
            // line 120
            echo "\t\t<script src=\"";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, strtr($this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), array("/index.php" => "")), "html", null, true);
            echo "/dp.php/agent-lang-";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "language"), "id"), "html", null, true);
            echo ".js?v=";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "core.deskpro_build"), "method"), "html", null, true);
            echo "\"></script>
\t";
        }
        // line 122
        echo "</head>
<body class=\"dp ";
        // line 123
        if (isset($context["is_demo"])) { $_is_demo_ = $context["is_demo"]; } else { $_is_demo_ = null; }
        if ($_is_demo_) {
            echo "is-demo";
        }
        echo " ";
        if (isset($context["is_post_install"])) { $_is_post_install_ = $context["is_post_install"]; } else { $_is_post_install_ = null; }
        if ($_is_post_install_) {
            echo "post-install";
        }
        echo " ";
        if (isset($context["body_class"])) { $_body_class_ = $context["body_class"]; } else { $_body_class_ = null; }
        echo twig_escape_filter($this->env, $_body_class_, "html", null, true);
        echo "\" ";
        if (isset($context["page_handler"])) { $_page_handler_ = $context["page_handler"]; } else { $_page_handler_ = null; }
        if ($_page_handler_) {
            echo "data-element-handler=\"";
            if (isset($context["page_handler"])) { $_page_handler_ = $context["page_handler"]; } else { $_page_handler_ = null; }
            echo twig_escape_filter($this->env, $_page_handler_, "html", null, true);
            echo "\"";
        }
        echo ">
<div id=\"please_wait_overlay\" style=\"display: none;\">
\t<article>
\t\tLoading
\t</article>
</div>

";
        // line 130
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.setup_initial"), "method")) {
            // line 131
            echo "\t";
            if (isset($context["is_post_install"])) { $_is_post_install_ = $context["is_post_install"]; } else { $_is_post_install_ = null; }
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if (((!$_is_post_install_) && $this->getAttribute($_app_, "helpdesk_is_offline"))) {
                // line 132
                echo "\t\t<div class=\"helpdesk-disabled-notice alt\">
\t\t\t<form action=\"";
                // line 133
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_settings_set", array("setting_name" => "core.helpdesk_disabled", "security_token" => $this->env->getExtension('deskpro_templating')->securityToken("set_setting"))), "html", null, true);
                echo "\" method=\"POST\">
\t\t\t\t";
                // line 134
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.helpdesk_currently_disabled");
                echo "
\t\t\t\t<button class=\"btn primary\">";
                // line 135
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.click_to_reenable_helpdesk");
                echo "</button>
\t\t\t</form>
\t\t</div>
\t";
            } elseif ($this->getAttribute($_app_, "getVariable", array(0 => "cron_is_problem"), "method")) {
                // line 139
                echo "\t\t<div class=\"helpdesk-disabled-notice\">
\t\t\tThe scheduled tasks are either not running or the time between executions is too high.
\t\t\t<a class=\"btn primary\" href=\"";
                // line 141
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_settings_cron"), "html", null, true);
                echo "\">Fix This</a>
\t\t</div>
\t";
            }
        }
        // line 145
        echo "
";
        // line 146
        $this->displayBlock('header', $context, $blocks);
        // line 278
        echo "
<!-- /BEGIN NAV BAR -->
<div id=\"dp_page_wrap\">

";
        // line 282
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (isset($context["this_page"])) { $_this_page_ = $context["this_page"]; } else { $_this_page_ = null; }
        if (($this->getAttribute($this->getAttribute($_app_, "getLicense", array(), "method"), "isDemo", array(), "method") && (($_this_page_ == "dashboard") || ($this->getAttribute($this->getAttribute($_app_, "getLicense", array(), "method"), "getExpireDays", array(), "method") <= 3)))) {
            // line 283
            echo "\t<section class=\"demo-notice\">
\t\t<article>
\t\t\t";
            // line 285
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($_app_, "isCloud", array(), "method")) {
                // line 286
                echo "\t\t\t\tYour DeskPRO demo expires
\t\t\t\t<em class=\"days\">
\t\t\t\t\t";
                // line 288
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                if ((($this->getAttribute($this->getAttribute($_app_, "getLicense", array(), "method"), "getExpireDays", array(), "method") == 0) && ($this->getAttribute($this->getAttribute($_app_, "getLicense", array(), "method"), "getExpireTime", array(0 => "hours"), "method") == 0))) {
                    // line 289
                    echo "\t\t\t\t\t\tin ";
                    if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "getLicense", array(), "method"), "getExpireTime", array(0 => "mins"), "method"), "html", null, true);
                    echo " minutes
\t\t\t\t\t";
                } elseif (($this->getAttribute($this->getAttribute($_app_, "getLicense", array(), "method"), "getExpireDays", array(), "method") < 3)) {
                    // line 291
                    echo "\t\t\t\t\t\tin ";
                    if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "getLicense", array(), "method"), "getExpireTime", array(0 => "hours"), "method"), "html", null, true);
                    echo " hours
\t\t\t\t\t";
                } else {
                    // line 293
                    echo "\t\t\t\t\t\tin ";
                    if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "getLicense", array(), "method"), "getExpireDays", array(), "method"), "html", null, true);
                    echo " days
\t\t\t\t\t";
                }
                // line 295
                echo "\t\t\t\t</em>.

\t\t\t\t";
                // line 297
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                if (($this->getAttribute($this->getAttribute($_app_, "getLicense", array(), "method"), "getExpireDays", array(), "method") >= 13)) {
                    // line 298
                    echo "\t\t\t\t\t<div style=\"font-size: 11px; padding-top: 10px;\">
\t\t\t\t\t\tReady to sign up? Go to the <a href=\"";
                    // line 299
                    if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
                    echo "/billing/\">Billing Interface</a> to enter your billing information. You will not be billed until your trial ends.
\t\t\t\t\t</div>
\t\t\t\t";
                } else {
                    // line 302
                    echo "\t\t\t\t\tTo dismiss this notice and ensure uninterrupted access, please enter your billing information.

\t\t\t\t\t<div class=\"enter-wrap\">
\t\t\t\t\t\t<a href=\"";
                    // line 305
                    if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
                    echo "/billing/\" class=\"btn primary\">Enter your billing information now &rarr;</a>
\t\t\t\t\t</div>
\t\t\t\t";
                }
                // line 308
                echo "\t\t\t";
            } else {
                // line 309
                echo "\t\t\t\tYour DeskPRO demo expires
\t\t\t\t<em class=\"days\">
\t\t\t\t\t";
                // line 311
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                if ((($this->getAttribute($this->getAttribute($_app_, "getLicense", array(), "method"), "getExpireDays", array(), "method") == 0) && ($this->getAttribute($this->getAttribute($_app_, "getLicense", array(), "method"), "getExpireTime", array(0 => "hours"), "method") == 0))) {
                    // line 312
                    echo "\t\t\t\t\t\tin ";
                    if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "getLicense", array(), "method"), "getExpireTime", array(0 => "mins"), "method"), "html", null, true);
                    echo " minutes
\t\t\t\t\t";
                } elseif (($this->getAttribute($this->getAttribute($_app_, "getLicense", array(), "method"), "getExpireDays", array(), "method") < 3)) {
                    // line 314
                    echo "\t\t\t\t\t\tin ";
                    if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "getLicense", array(), "method"), "getExpireTime", array(0 => "hours"), "method"), "html", null, true);
                    echo " hours
\t\t\t\t\t";
                } else {
                    // line 316
                    echo "\t\t\t\t\t\tin ";
                    if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "getLicense", array(), "method"), "getExpireDays", array(), "method"), "html", null, true);
                    echo " days
\t\t\t\t\t";
                }
                // line 318
                echo "\t\t\t\t</em>.
\t\t\t\tTo dismiss this notice and keep your helpdesk operational, you need to purchase a license.

\t\t\t\t<div class=\"enter-wrap\">
\t\t\t\t\t<a href=\"https://www.deskpro.com/members/buy\" class=\"btn primary\">Purchase a license now &rarr;</a>
\t\t\t\t</div>
\t\t\t";
            }
            // line 325
            echo "\t\t</article>
\t</section>
";
        }
        // line 328
        echo "
";
        // line 329
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($this->getAttribute($_app_, "isCloud", array(), "method") && $this->env->getExtension('deskpro_templating')->getConstant("DPC_BILL_OVERDUE"))) {
            // line 330
            echo "\t<section class=\"demo-notice failed-bill\">
\t\t<article>
\t\t\tWe failed to charge your credit-card for another month of service. To ensure uninterupted service, please go to the billing interface
\t\t\tto settle the overdue bill.

\t\t\t<div class=\"enter-wrap\">
\t\t\t\t<a href=\"";
            // line 336
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
            echo "/billing/\" class=\"btn primary warn\">Enter your billing information now &rarr;</a>
\t\t\t</div>
\t\t</article>
\t</section>
";
        }
        // line 341
        echo "
";
        // line 342
        if (isset($context["is_post_install"])) { $_is_post_install_ = $context["is_post_install"]; } else { $_is_post_install_ = null; }
        if ($_is_post_install_) {
            // line 343
            if (isset($context["is_cron_page"])) { $_is_cron_page_ = $context["is_cron_page"]; } else { $_is_cron_page_ = null; }
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ((((((!$_is_cron_page_) && $this->getAttribute($_app_, "getSetting", array(0 => "core.setup_initial"), "method")) && (!$this->getAttribute($_app_, "getSetting", array(0 => "core.last_cron_run"), "method"))) && $this->getAttribute($_app_, "getSetting", array(0 => "core.install_timestamp"), "method")) && (($this->env->getExtension('deskpro_templating')->userDate($context, "now", "U") - 600) > $this->getAttribute($_app_, "getSetting", array(0 => "core.install_timestamp"), "method")))) {
                // line 344
                echo "\t<div class=\"cron-warn\">
\t\t<p>";
                // line 345
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.error_cron_inactive");
                echo "</p>
\t\t<a class=\"clean-white\" href=\"";
                // line 346
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_settings_cron"), "html", null, true);
                echo "\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.error_cron_inactive_go");
                echo " &rarr;</a>
\t</div>
";
            }
        }
        // line 350
        echo "
";
        // line 351
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "session"), "hasFlash", array(0 => "success_message"), "method")) {
            // line 352
            echo "\t<div class=\"alert-message success\">
\t\t";
            // line 353
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "session"), "getFlash", array(0 => "success_message"), "method"), "html", null, true);
            echo "
\t</div>
";
        }
        // line 356
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "session"), "hasFlash", array(0 => "error_message"), "method")) {
            // line 357
            echo "\t<div class=\"alert-message error\">
\t\t";
            // line 358
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "session"), "getFlash", array(0 => "error_message"), "method"), "html", null, true);
            echo "
\t</div>
";
        }
        // line 361
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "session"), "hasFlash", array(0 => "saved"), "method")) {
            // line 362
            echo "\t<div class=\"alert-message success\">
\t\t\"";
            // line 363
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "session"), "getFlash", array(0 => "saved"), "method"), "html", null, true);
            echo "\" was saved successfully
\t</div>
";
        }
        // line 366
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "session"), "hasFlash", array(0 => "deleted"), "method")) {
            // line 367
            echo "\t<div class=\"alert-message success\">
\t\t\"";
            // line 368
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "session"), "getFlash", array(0 => "deleted"), "method"), "html", null, true);
            echo "\" was deleted successfully
\t</div>
";
        }
        // line 371
        echo "
";
        // line 372
        ob_start();
        $this->displayBlock('pagebar', $context, $blocks);
        $context["pagebar"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 373
        if (isset($context["pagebar"])) { $_pagebar_ = $context["pagebar"]; } else { $_pagebar_ = null; }
        if (twig_length_filter($this->env, $this->env->getExtension('deskpro_templating')->strTrim($_pagebar_))) {
            // line 374
            echo "\t<div id=\"dp_admin_pagebar\" class=\"dp_admin_pagebar\">";
            if (isset($context["pagebar"])) { $_pagebar_ = $context["pagebar"]; } else { $_pagebar_ = null; }
            echo $_pagebar_;
            echo "</div>
";
        }
        // line 376
        echo "
";
        // line 377
        ob_start();
        $this->displayBlock('pagetabs', $context, $blocks);
        $context["pagetabs"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 378
        if (isset($context["pagetabs"])) { $_pagetabs_ = $context["pagetabs"]; } else { $_pagetabs_ = null; }
        if (twig_length_filter($this->env, $this->env->getExtension('deskpro_templating')->strTrim($_pagetabs_))) {
            // line 379
            echo "\t<div id=\"dp_admin_pagetabs\" class=\"dp_admin_pagetabs\">";
            if (isset($context["pagetabs"])) { $_pagetabs_ = $context["pagetabs"]; } else { $_pagetabs_ = null; }
            echo $_pagetabs_;
            echo "</div>
";
        }
        // line 381
        echo "
";
        // line 382
        $this->displayBlock('pagebar_after', $context, $blocks);
        // line 383
        echo "
";
        // line 384
        ob_start();
        $this->displayBlock('sidebar', $context, $blocks);
        $context["sidebar"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 385
        ob_start();
        $this->displayBlock('sidebar_right', $context, $blocks);
        $context["sidebar_right"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 386
        $this->displayBlock('dp_full_page', $context, $blocks);
        // line 406
        echo "
";
        // line 407
        $this->env->loadTemplate("AdminBundle:Main:layout-menus.html.twig")->display($context);
        // line 408
        echo "
</div>

";
        // line 411
        ob_start();
        $this->displayBlock('page_nav', $context, $blocks);
        $context["page_nav"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 412
        ob_start();
        $this->displayBlock('page_nav_inner', $context, $blocks);
        $context["page_nav_inner"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 413
        if (isset($context["page_nav"])) { $_page_nav_ = $context["page_nav"]; } else { $_page_nav_ = null; }
        if (isset($context["page_nav_inner"])) { $_page_nav_inner_ = $context["page_nav_inner"]; } else { $_page_nav_inner_ = null; }
        if ((twig_length_filter($this->env, $this->env->getExtension('deskpro_templating')->strTrim($_page_nav_)) || twig_length_filter($this->env, $this->env->getExtension('deskpro_templating')->strTrim($_page_nav_inner_)))) {
            // line 414
            echo "\t<div id=\"dp_page_nav\" ";
            if (isset($context["page_nav_fixed"])) { $_page_nav_fixed_ = $context["page_nav_fixed"]; } else { $_page_nav_fixed_ = null; }
            if ($_page_nav_fixed_) {
                echo "class=\"fixed\"";
            }
            echo ">
\t\t";
            // line 415
            if (isset($context["page_nav_inner"])) { $_page_nav_inner_ = $context["page_nav_inner"]; } else { $_page_nav_inner_ = null; }
            if (twig_length_filter($this->env, $this->env->getExtension('deskpro_templating')->strTrim($_page_nav_inner_))) {
                // line 416
                echo "\t\t\t";
                if (isset($context["page_nav_inner"])) { $_page_nav_inner_ = $context["page_nav_inner"]; } else { $_page_nav_inner_ = null; }
                echo $_page_nav_inner_;
                echo "
\t\t";
            } else {
                // line 418
                echo "\t\t\t<div class=\"page-nav-block\">
\t\t\t\t<div class=\"inner-shadow\"></div>
\t\t\t\t";
                // line 420
                if (isset($context["page_nav"])) { $_page_nav_ = $context["page_nav"]; } else { $_page_nav_ = null; }
                echo $_page_nav_;
                echo "
\t\t\t</div>
\t\t";
            }
            // line 423
            echo "\t</div>
";
        }
        // line 425
        echo "
</body>
</html>
";
    }

    // line 106
    public function block_html_headjs_load($context, array $blocks = array())
    {
    }

    // line 112
    public function block_html_head($context, array $blocks = array())
    {
    }

    // line 113
    public function block_page_js_exec($context, array $blocks = array())
    {
    }

    // line 146
    public function block_header($context, array $blocks = array())
    {
        // line 147
        echo "<!-- BEGIN HEADER BAR -->
<div id=\"dp_header\">
\t";
        // line 149
        if (isset($context["is_post_install"])) { $_is_post_install_ = $context["is_post_install"]; } else { $_is_post_install_ = null; }
        if ($_is_post_install_) {
            // line 150
            echo "\t\t<div class=\"user-profile pull-left\">
\t\t\t<img class=\"gravatar pull-left\" src=\"";
            // line 151
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "user"), "getPictureUrl", array(0 => 31), "method"), "html", null, true);
            echo "\">
\t\t\t<div class=\"username\">";
            // line 152
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.chrome.hello-user", array("name" => $this->getAttribute($this->getAttribute($_app_, "user"), "name")));
            echo "</div>
\t\t\t<ul class=\"nav-profile\">
\t\t\t\t<li><i class=\"icon-map-marker\"></i> <a>Initial Setup</a></li>
\t\t\t</ul>
\t\t</div>
\t";
        } else {
            // line 158
            echo "\t\t<div class=\"user-profile pull-left\" ";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($this->getAttribute($_app_, "user"), "can_billing")) {
                echo "style=\"width:266px\"";
            }
            echo ">
\t\t\t<img class=\"gravatar pull-left\" src=\"";
            // line 159
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "user"), "getPictureUrl", array(0 => 31), "method"), "html", null, true);
            echo "\">
\t\t\t<div class=\"username\">";
            // line 160
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.chrome.hello-user", array("name" => $this->getAttribute($this->getAttribute($_app_, "user"), "name")));
            echo "</div>
\t\t\t<ul class=\"nav-profile\">
\t\t\t\t";
            // line 162
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($this->getAttribute($_app_, "user"), "can_reports")) {
                echo "<li><i class=\"icon-dashboard\"></i> <a class=\"report\" href=\"";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
                echo "/reports/login?dpsid-agent=";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "getSession", array(), "method"), "getId", array(), "method"), "html", null, true);
                echo "\"><span>Reports</span></a></li>";
            }
            // line 163
            echo "\t\t\t\t";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($this->getAttribute($_app_, "user"), "can_billing")) {
                echo "<li><i class=\"icon-credit-card\"></i> <a class=\"billing\" href=\"";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
                echo "/billing/\"><span>Billing</span></a></li>";
            }
            // line 164
            echo "\t\t\t\t";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($this->getAttribute($_app_, "user"), "can_agent")) {
                echo "<li><i class=\"icon-inbox\"></i> <a class=\"agent\" href=\"";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
                echo "/agent/\"><span class=\"check\">Agent</span></a></li>";
            }
            // line 165
            echo "\t\t\t\t<li onclick=\"window.location='";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_logout", array("auth" => $this->env->getExtension('deskpro_templating')->staticSecurityToken("user_logout", 0))), "html", null, true);
            echo "?to=agent'; return false;\"><i class=\"icon-signout\"></i> <a>";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.chrome.link_logout");
            echo "</a></li>
\t\t\t</ul>
\t\t</div>
\t";
        }
        // line 169
        echo "</div>
<!-- /BEGIN HEADER BAR -->

<!-- BEGIN NAV BAR -->
<div id=\"dp_admin_nav\">
\t";
        // line 174
        if (isset($context["is_post_install"])) { $_is_post_install_ = $context["is_post_install"]; } else { $_is_post_install_ = null; }
        if ($_is_post_install_) {
            echo "<div class=\"capture-click\"></div>";
        }
        // line 175
        echo "\t<div id=\"dp_admin_nav_sections\" class=\"deskproPane\">
\t\t";
        // line 176
        if (isset($context["is_post_install"])) { $_is_post_install_ = $context["is_post_install"]; } else { $_is_post_install_ = null; }
        if ((false && (!$_is_post_install_))) {
            // line 177
            echo "\t\t\t";
            $this->env->loadTemplate("AdminBundle:Main:layout-setupguide.html.twig")->display($context);
            // line 178
            echo "\t\t";
        }
        // line 179
        echo "
\t\t";
        // line 180
        if (isset($context["is_quick_setup"])) { $_is_quick_setup_ = $context["is_quick_setup"]; } else { $_is_quick_setup_ = null; }
        if ($_is_quick_setup_) {
            // line 181
            echo "\t\t\t<ul class=\"start-ul\">
\t\t\t\t<li>
\t\t\t\t\t<a href=\"";
            // line 183
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_welcome"), "html", null, true);
            echo "\"><span class=\"start\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.start");
            echo "</span></a>
\t\t\t\t</li>
\t\t\t</ul>
\t\t\t<div class=\"sep\">-</div>
\t\t";
        }
        // line 188
        echo "
\t\t<ul>
\t\t\t<li>
\t\t\t\t<a href=\"";
        // line 191
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin"), "html", null, true);
        echo "\"><span class=\"home\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.home");
        echo "</span></a>
\t\t\t</li>
\t\t</ul>
\t\t<div class=\"sep\">-</div>
\t\t<ul>
\t\t\t<li>
\t\t\t\t<a href=\"";
        // line 197
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_settings"), "html", null, true);
        echo "\"><span class=\"setup\">Settings</span></a>
\t\t\t</li>
\t\t</ul>
\t\t<div class=\"sep\">-</div>
\t\t<ul>
\t\t\t<li>
\t\t\t\t<a href=\"";
        // line 203
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_agents"), "html", null, true);
        echo "\"><span class=\"agents\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.agents");
        echo "</span></a>
\t\t\t</li>
\t\t</ul>
\t\t<div class=\"sep\">-</div>
\t\t<ul>
\t\t\t<li data-menu=\"#portal_menu\">
\t\t\t\t<a href=\"";
        // line 209
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_portal"), "html", null, true);
        echo "\"><span class=\"publish\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.portal");
        echo "</span></a>
\t\t\t</li>
\t\t</ul>
\t\t";
        // line 212
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "isCloud"))) {
            // line 213
            echo "\t\t\t<div class=\"sep\">-</div>
\t\t\t<ul>
\t\t\t\t<li data-menu=\"#server_menu\"><a><span class=\"server\">";
            // line 215
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.server");
            echo "</span></a></li>
\t\t\t</ul>
\t\t";
        }
        // line 218
        echo "\t\t<div class=\"sep\">-</div>
\t\t<ul>
\t\t\t<li data-menu=\"#integrate_menu\">
\t\t\t\t<a href=\"#\"><span class=\"apps\">Integrate</span></a>
\t\t\t</li>
\t\t</ul>
\t\t<div class=\"sep\">-</div>
\t\t<ul>
\t\t\t<li data-menu=\"#tickets_menu\">
\t\t\t\t<a><span class=\"tickets\">";
        // line 227
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.tickets");
        echo "</span></a>
\t\t\t</li>
\t\t</ul>
\t\t<div class=\"sep\">-</div>
\t\t<ul>
\t\t\t<li data-menu=\"#users_menu\">
\t\t\t\t<a><span class=\"users\">";
        // line 233
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.menu.crm");
        echo "</span></a>
\t\t\t</li>
\t\t</ul>
\t\t<div class=\"sep\">-</div>
\t\t<ul>
\t\t\t<li data-menu=\"#kb_menu\" ";
        // line 238
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core.apps_kb"), "method"))) {
            echo "class=\"off\"";
        }
        echo ">
\t\t\t\t<a ";
        // line 239
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core.apps_kb"), "method"))) {
            echo "href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_apps"), "html", null, true);
            echo "\"";
        }
        echo "><span class=\"kb\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.kb");
        echo "</span></a>
\t\t\t</li>
\t\t</ul>
\t\t<div class=\"sep\">-</div>
\t\t<ul>
\t\t\t<li data-menu=\"#feedback_menu\" ";
        // line 244
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core.apps_feedback"), "method"))) {
            echo "class=\"off\"";
        }
        echo ">
\t\t\t\t<a ";
        // line 245
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core.apps_feedback"), "method"))) {
            echo "href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_apps"), "html", null, true);
            echo "\"";
        }
        echo "><span class=\"feedback\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.feedback");
        echo "</span></a>
\t\t\t</li>
\t\t</ul>
\t\t<div class=\"sep\">-</div>
\t\t<ul>
\t\t\t<li data-menu=\"#downloads_menu\"";
        // line 250
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core.apps_downloads"), "method"))) {
            echo "class=\"off\"";
        }
        echo ">
\t\t\t\t<a ";
        // line 251
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core.apps_downloads"), "method"))) {
            echo "href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_apps"), "html", null, true);
            echo "\"";
        }
        echo "><span class=\"downloads\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.downloads");
        echo "</span></a>
\t\t\t</li>
\t\t</ul>
\t\t<div class=\"sep\">-</div>
\t\t<ul>
\t\t\t<li data-menu=\"#news_menu\"";
        // line 256
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core.apps_news"), "method"))) {
            echo "class=\"off\"";
        }
        echo ">
\t\t\t\t<a ";
        // line 257
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core.apps_news"), "method"))) {
            echo "href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_apps"), "html", null, true);
            echo "\"";
        }
        echo "><span class=\"news\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.news");
        echo "</span></a>
\t\t\t</li>
\t\t</ul>
\t\t<div class=\"sep\">-</div>
\t\t<ul>
\t\t\t<li data-menu=\"#chat_menu\"";
        // line 262
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core.apps_chat"), "method"))) {
            echo "class=\"off\"";
        }
        echo ">
\t\t\t\t<a ";
        // line 263
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core.apps_chat"), "method"))) {
            echo "href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_apps"), "html", null, true);
            echo "\"";
        }
        echo "><span class=\"chat\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.chat");
        echo "</span></a>
\t\t\t</li>
\t\t</ul>
\t\t";
        // line 266
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($this->getAttribute($_app_, "getConfig", array(0 => "enable_twitter"), "method") && ((!$this->getAttribute($_app_, "isCloud", array(), "method")) || $this->getAttribute($_app_, "getConfig", array(0 => "twitter.agent_consumer_key"), "method")))) {
            // line 267
            echo "\t\t\t<div class=\"sep\">-</div>
\t\t\t<ul>
\t\t\t\t<li data-menu=\"#twitter_menu\">
\t\t\t\t\t<a><span class=\"twitter\">Twitter</span></a>
\t\t\t\t</li>
\t\t\t</ul>
\t\t";
        }
        // line 274
        echo "\t</div>
</div>

";
    }

    // line 372
    public function block_pagebar($context, array $blocks = array())
    {
    }

    // line 377
    public function block_pagetabs($context, array $blocks = array())
    {
    }

    // line 382
    public function block_pagebar_after($context, array $blocks = array())
    {
    }

    // line 384
    public function block_sidebar($context, array $blocks = array())
    {
    }

    // line 385
    public function block_sidebar_right($context, array $blocks = array())
    {
    }

    // line 386
    public function block_dp_full_page($context, array $blocks = array())
    {
        // line 387
        echo "<div id=\"dp_admin_page\" class=\"dp_admin_page ";
        if (isset($context["sidebar"])) { $_sidebar_ = $context["sidebar"]; } else { $_sidebar_ = null; }
        if (twig_length_filter($this->env, $this->env->getExtension('deskpro_templating')->strTrim($_sidebar_))) {
            echo "with-sidebar";
        }
        echo " ";
        if (isset($context["sidebar_right"])) { $_sidebar_right_ = $context["sidebar_right"]; } else { $_sidebar_right_ = null; }
        if (twig_length_filter($this->env, $this->env->getExtension('deskpro_templating')->strTrim($_sidebar_right_))) {
            echo "with-sidebar-right";
        }
        echo " ";
        if (isset($context["admin_page_class"])) { $_admin_page_class_ = $context["admin_page_class"]; } else { $_admin_page_class_ = null; }
        echo twig_escape_filter($this->env, $_admin_page_class_, "html", null, true);
        echo " ";
        if (isset($context["pagetabs"])) { $_pagetabs_ = $context["pagetabs"]; } else { $_pagetabs_ = null; }
        if (twig_length_filter($this->env, $this->env->getExtension('deskpro_templating')->strTrim($_pagetabs_))) {
            echo "with-pagetabs";
        }
        echo "\">
\t";
        // line 388
        $this->displayBlock('prepage', $context, $blocks);
        // line 389
        echo "\t<div id=\"dp_admin_page_inner\" class=\"dp_admin_page_inner\">
\t\t";
        // line 390
        if (isset($context["sidebar"])) { $_sidebar_ = $context["sidebar"]; } else { $_sidebar_ = null; }
        if (twig_length_filter($this->env, $this->env->getExtension('deskpro_templating')->strTrim($_sidebar_))) {
            // line 391
            echo "\t\t\t<div id=\"dp_admin_page_sidebar\" class=\"dp_admin_page_sidebar dp-sidebar\">";
            if (isset($context["sidebar"])) { $_sidebar_ = $context["sidebar"]; } else { $_sidebar_ = null; }
            echo $_sidebar_;
            echo "</div>
\t\t";
        }
        // line 393
        echo "\t\t";
        if (isset($context["sidebar_right"])) { $_sidebar_right_ = $context["sidebar_right"]; } else { $_sidebar_right_ = null; }
        if (twig_length_filter($this->env, $this->env->getExtension('deskpro_templating')->strTrim($_sidebar_right_))) {
            // line 394
            echo "\t\t\t<div id=\"dp_admin_page_sidebar_right\" class=\"dp_admin_page_sidebar_right dp-sidebar\">";
            if (isset($context["sidebar_right"])) { $_sidebar_right_ = $context["sidebar_right"]; } else { $_sidebar_right_ = null; }
            echo $_sidebar_right_;
            echo "</div>
\t\t";
        }
        // line 396
        echo "\t\t";
        $this->displayBlock('content', $context, $blocks);
        // line 403
        echo "\t</div>
</div>
";
    }

    // line 388
    public function block_prepage($context, array $blocks = array())
    {
    }

    // line 396
    public function block_content($context, array $blocks = array())
    {
        // line 397
        echo "\t\t\t<div class=\"dp-page-box\">
\t\t\t\t<div class=\"page-content\">
\t\t\t\t\t";
        // line 399
        $this->displayBlock('page', $context, $blocks);
        // line 400
        echo "\t\t\t\t</div>
\t\t\t</div>
\t\t";
    }

    // line 399
    public function block_page($context, array $blocks = array())
    {
    }

    // line 411
    public function block_page_nav($context, array $blocks = array())
    {
    }

    // line 412
    public function block_page_nav_inner($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "AdminBundle::layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1144 => 411,  1139 => 399,  1131 => 399,  1127 => 397,  1110 => 396,  1092 => 391,  1089 => 390,  1086 => 389,  1084 => 388,  1063 => 387,  1060 => 386,  1055 => 385,  1050 => 384,  1035 => 372,  1019 => 267,  1003 => 263,  959 => 251,  900 => 233,  880 => 218,  870 => 213,  867 => 212,  859 => 209,  848 => 203,  839 => 197,  828 => 191,  823 => 188,  809 => 181,  800 => 178,  797 => 177,  794 => 176,  786 => 174,  740 => 162,  734 => 160,  703 => 150,  693 => 146,  630 => 412,  626 => 411,  614 => 386,  610 => 385,  581 => 376,  564 => 371,  525 => 356,  722 => 269,  697 => 256,  674 => 249,  671 => 425,  577 => 220,  569 => 216,  557 => 368,  502 => 195,  497 => 194,  445 => 173,  729 => 159,  684 => 261,  676 => 256,  669 => 254,  660 => 420,  647 => 243,  643 => 244,  601 => 382,  570 => 211,  522 => 200,  501 => 179,  296 => 149,  374 => 137,  631 => 239,  616 => 406,  608 => 17,  605 => 16,  596 => 15,  574 => 374,  561 => 209,  527 => 147,  433 => 160,  388 => 142,  426 => 177,  383 => 146,  461 => 167,  370 => 113,  395 => 144,  294 => 76,  223 => 79,  220 => 59,  492 => 127,  468 => 121,  444 => 33,  410 => 105,  397 => 101,  377 => 144,  262 => 105,  250 => 147,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 2108,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 1383,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 1303,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 1173,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 1041,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 970,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 920,  1126 => 916,  1106 => 900,  1100 => 897,  1026 => 841,  997 => 822,  983 => 816,  975 => 812,  956 => 800,  939 => 786,  902 => 753,  894 => 749,  879 => 746,  757 => 631,  727 => 608,  716 => 605,  670 => 582,  528 => 357,  476 => 123,  435 => 31,  354 => 110,  341 => 278,  192 => 88,  321 => 163,  243 => 92,  793 => 351,  780 => 348,  758 => 341,  700 => 149,  686 => 292,  652 => 274,  638 => 414,  620 => 254,  545 => 218,  523 => 203,  494 => 183,  459 => 163,  438 => 172,  351 => 283,  347 => 282,  402 => 157,  268 => 75,  430 => 120,  411 => 120,  379 => 293,  322 => 92,  315 => 110,  289 => 113,  284 => 93,  255 => 24,  234 => 115,  1133 => 400,  1124 => 396,  1121 => 56,  1116 => 55,  1113 => 403,  1108 => 51,  1103 => 394,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 346,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 329,  996 => 262,  989 => 316,  985 => 315,  981 => 257,  977 => 313,  970 => 311,  966 => 310,  955 => 303,  952 => 250,  943 => 299,  936 => 296,  930 => 244,  919 => 290,  917 => 289,  908 => 238,  905 => 286,  896 => 280,  891 => 227,  877 => 270,  862 => 267,  857 => 265,  837 => 253,  832 => 250,  827 => 248,  821 => 247,  803 => 179,  778 => 235,  769 => 165,  765 => 232,  753 => 229,  746 => 225,  743 => 224,  735 => 613,  730 => 218,  720 => 213,  717 => 212,  712 => 209,  691 => 204,  678 => 106,  654 => 196,  587 => 14,  576 => 179,  539 => 171,  517 => 197,  471 => 155,  441 => 131,  437 => 142,  418 => 309,  386 => 295,  373 => 95,  304 => 151,  270 => 123,  265 => 157,  229 => 81,  477 => 138,  455 => 325,  448 => 164,  429 => 159,  407 => 95,  399 => 156,  389 => 99,  375 => 123,  358 => 286,  349 => 118,  335 => 84,  327 => 93,  298 => 84,  280 => 85,  249 => 147,  194 => 65,  142 => 68,  344 => 83,  318 => 135,  306 => 87,  295 => 83,  357 => 119,  300 => 150,  286 => 145,  276 => 87,  269 => 66,  254 => 120,  128 => 34,  237 => 138,  165 => 90,  122 => 42,  798 => 242,  770 => 113,  759 => 112,  748 => 337,  731 => 108,  721 => 158,  718 => 106,  708 => 271,  696 => 147,  617 => 234,  590 => 226,  553 => 87,  550 => 466,  540 => 84,  533 => 82,  500 => 346,  493 => 344,  489 => 343,  482 => 69,  467 => 67,  464 => 120,  458 => 166,  452 => 117,  449 => 174,  415 => 308,  382 => 124,  372 => 291,  361 => 104,  356 => 122,  339 => 146,  302 => 131,  285 => 77,  258 => 64,  123 => 32,  108 => 35,  424 => 156,  394 => 86,  380 => 80,  338 => 107,  319 => 101,  316 => 91,  312 => 109,  290 => 146,  267 => 122,  206 => 51,  110 => 31,  240 => 93,  224 => 60,  219 => 107,  217 => 106,  202 => 52,  186 => 47,  170 => 82,  100 => 29,  67 => 17,  14 => 1,  1096 => 291,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 836,  1013 => 275,  1007 => 274,  1002 => 823,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 300,  940 => 298,  937 => 245,  928 => 244,  926 => 243,  915 => 239,  912 => 236,  903 => 231,  898 => 230,  892 => 229,  889 => 748,  887 => 227,  884 => 747,  876 => 222,  874 => 215,  871 => 220,  863 => 214,  861 => 213,  858 => 212,  850 => 263,  843 => 206,  840 => 205,  815 => 245,  812 => 244,  808 => 199,  804 => 120,  799 => 196,  791 => 175,  785 => 238,  775 => 184,  771 => 183,  754 => 340,  728 => 170,  726 => 169,  723 => 168,  715 => 105,  711 => 152,  709 => 162,  706 => 151,  698 => 208,  694 => 255,  692 => 155,  689 => 254,  681 => 150,  677 => 149,  675 => 148,  663 => 251,  661 => 277,  650 => 246,  646 => 415,  629 => 129,  627 => 21,  625 => 236,  622 => 126,  598 => 381,  592 => 117,  586 => 115,  575 => 214,  566 => 210,  556 => 100,  554 => 367,  541 => 362,  536 => 205,  515 => 352,  511 => 85,  509 => 350,  488 => 126,  486 => 342,  483 => 341,  465 => 73,  463 => 329,  450 => 65,  432 => 314,  419 => 155,  371 => 141,  362 => 288,  353 => 129,  337 => 18,  333 => 122,  309 => 94,  303 => 76,  299 => 130,  291 => 111,  272 => 82,  261 => 156,  253 => 58,  239 => 117,  235 => 63,  213 => 84,  200 => 43,  198 => 110,  159 => 43,  149 => 79,  146 => 39,  131 => 35,  116 => 57,  79 => 25,  74 => 15,  71 => 22,  836 => 320,  817 => 319,  814 => 318,  811 => 317,  805 => 313,  787 => 657,  779 => 169,  776 => 305,  773 => 347,  761 => 296,  751 => 163,  747 => 293,  742 => 336,  739 => 291,  736 => 287,  724 => 214,  705 => 278,  702 => 601,  688 => 113,  680 => 263,  667 => 423,  662 => 246,  656 => 418,  649 => 416,  644 => 97,  641 => 241,  624 => 236,  613 => 233,  607 => 232,  597 => 221,  591 => 379,  584 => 377,  579 => 234,  563 => 213,  559 => 208,  551 => 366,  547 => 200,  537 => 201,  524 => 191,  512 => 351,  507 => 76,  504 => 143,  498 => 142,  485 => 176,  480 => 124,  472 => 171,  466 => 330,  460 => 328,  447 => 388,  442 => 162,  434 => 110,  428 => 29,  422 => 311,  404 => 149,  368 => 136,  364 => 127,  340 => 19,  334 => 130,  330 => 94,  325 => 139,  292 => 102,  287 => 109,  282 => 119,  279 => 98,  273 => 103,  266 => 106,  256 => 71,  252 => 100,  228 => 113,  218 => 81,  201 => 72,  64 => 18,  51 => 9,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 1705,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 707,  1829 => 706,  1827 => 705,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 689,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 676,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 666,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 638,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 617,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 611,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 602,  1559 => 601,  1552 => 598,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 584,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 573,  1477 => 567,  1473 => 566,  1467 => 562,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 520,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 511,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 1001,  1257 => 1000,  1254 => 479,  1250 => 478,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 460,  1196 => 958,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 938,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 388,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 898,  1099 => 393,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 880,  1076 => 359,  1070 => 875,  1057 => 352,  1052 => 404,  1045 => 382,  1040 => 377,  1036 => 283,  1032 => 395,  1028 => 274,  1023 => 393,  1020 => 392,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 821,  979 => 379,  974 => 256,  967 => 373,  962 => 803,  958 => 801,  954 => 253,  950 => 368,  945 => 367,  942 => 250,  938 => 365,  934 => 364,  927 => 361,  923 => 292,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 272,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 207,  841 => 321,  835 => 312,  830 => 249,  826 => 309,  822 => 308,  818 => 246,  813 => 183,  810 => 305,  806 => 180,  802 => 197,  795 => 118,  792 => 239,  789 => 350,  784 => 295,  782 => 187,  777 => 291,  772 => 289,  768 => 345,  763 => 343,  760 => 164,  756 => 230,  752 => 284,  745 => 281,  741 => 280,  738 => 335,  732 => 171,  719 => 279,  714 => 264,  710 => 310,  704 => 267,  699 => 103,  695 => 264,  690 => 263,  687 => 203,  683 => 112,  679 => 288,  672 => 255,  668 => 247,  665 => 201,  658 => 244,  645 => 270,  640 => 247,  634 => 413,  628 => 193,  623 => 238,  619 => 407,  611 => 18,  606 => 384,  603 => 383,  599 => 242,  595 => 231,  583 => 216,  580 => 221,  573 => 221,  560 => 101,  543 => 172,  538 => 361,  534 => 208,  530 => 202,  526 => 187,  521 => 146,  518 => 353,  514 => 183,  510 => 202,  503 => 75,  496 => 345,  490 => 193,  484 => 125,  474 => 336,  470 => 168,  446 => 318,  440 => 114,  436 => 113,  431 => 146,  425 => 312,  416 => 104,  412 => 98,  408 => 305,  403 => 302,  400 => 169,  396 => 299,  392 => 152,  385 => 24,  381 => 139,  367 => 134,  363 => 139,  359 => 92,  355 => 285,  350 => 128,  346 => 127,  343 => 115,  328 => 17,  324 => 164,  313 => 122,  307 => 132,  301 => 40,  288 => 88,  283 => 86,  271 => 76,  257 => 76,  251 => 76,  238 => 92,  233 => 68,  195 => 69,  191 => 106,  187 => 87,  183 => 86,  130 => 36,  88 => 35,  76 => 18,  115 => 30,  95 => 29,  655 => 275,  651 => 232,  648 => 231,  637 => 241,  633 => 130,  621 => 408,  618 => 233,  615 => 232,  604 => 186,  600 => 516,  588 => 378,  585 => 204,  582 => 203,  571 => 373,  567 => 372,  555 => 207,  552 => 190,  549 => 208,  544 => 363,  542 => 207,  535 => 212,  531 => 358,  519 => 189,  516 => 199,  513 => 168,  508 => 145,  506 => 83,  499 => 198,  495 => 141,  491 => 191,  481 => 162,  478 => 172,  475 => 184,  469 => 182,  456 => 136,  451 => 186,  443 => 132,  439 => 316,  427 => 169,  423 => 109,  420 => 176,  409 => 159,  405 => 148,  401 => 147,  391 => 129,  387 => 129,  384 => 132,  378 => 138,  365 => 289,  360 => 132,  348 => 21,  336 => 145,  332 => 107,  329 => 141,  323 => 81,  310 => 133,  305 => 94,  277 => 84,  274 => 91,  263 => 70,  259 => 72,  247 => 21,  244 => 94,  241 => 20,  222 => 17,  210 => 115,  207 => 53,  204 => 74,  184 => 44,  181 => 97,  167 => 80,  157 => 37,  96 => 32,  421 => 124,  417 => 137,  414 => 152,  406 => 171,  398 => 129,  393 => 298,  390 => 297,  376 => 110,  369 => 94,  366 => 91,  352 => 134,  345 => 91,  342 => 109,  331 => 106,  326 => 102,  320 => 129,  317 => 90,  314 => 134,  311 => 78,  308 => 60,  297 => 89,  293 => 89,  281 => 107,  278 => 140,  275 => 34,  264 => 31,  260 => 73,  248 => 75,  245 => 90,  242 => 118,  231 => 114,  227 => 87,  215 => 64,  212 => 54,  209 => 74,  197 => 89,  177 => 34,  171 => 49,  161 => 43,  132 => 41,  121 => 29,  105 => 49,  99 => 27,  81 => 20,  77 => 18,  180 => 35,  176 => 98,  156 => 51,  143 => 38,  139 => 67,  118 => 53,  189 => 48,  185 => 104,  173 => 45,  166 => 40,  152 => 40,  174 => 39,  164 => 94,  154 => 87,  150 => 40,  137 => 36,  133 => 36,  127 => 34,  107 => 28,  102 => 34,  83 => 34,  78 => 27,  53 => 9,  23 => 6,  42 => 11,  138 => 76,  134 => 31,  109 => 57,  103 => 27,  97 => 28,  94 => 24,  84 => 26,  75 => 22,  69 => 17,  66 => 19,  54 => 10,  44 => 10,  230 => 18,  226 => 112,  203 => 12,  193 => 49,  188 => 105,  182 => 47,  178 => 49,  168 => 46,  163 => 44,  160 => 38,  155 => 41,  148 => 39,  145 => 36,  140 => 37,  136 => 34,  125 => 43,  120 => 27,  113 => 32,  101 => 37,  92 => 27,  89 => 27,  85 => 31,  73 => 20,  62 => 19,  59 => 12,  56 => 5,  41 => 2,  126 => 59,  119 => 31,  111 => 29,  106 => 30,  98 => 25,  93 => 28,  86 => 22,  70 => 23,  60 => 7,  28 => 8,  36 => 9,  114 => 32,  104 => 33,  91 => 28,  80 => 30,  63 => 18,  58 => 17,  40 => 10,  34 => 1,  45 => 11,  61 => 13,  55 => 13,  48 => 14,  39 => 9,  35 => 9,  31 => 3,  26 => 2,  21 => 1,  46 => 6,  29 => 7,  57 => 10,  50 => 12,  47 => 6,  38 => 3,  33 => 2,  49 => 11,  32 => 9,  246 => 97,  236 => 49,  232 => 135,  225 => 87,  221 => 78,  216 => 75,  214 => 122,  211 => 78,  208 => 76,  205 => 73,  199 => 71,  196 => 70,  190 => 84,  179 => 68,  175 => 34,  172 => 48,  169 => 95,  162 => 44,  158 => 42,  153 => 37,  151 => 39,  147 => 28,  144 => 37,  141 => 37,  135 => 70,  129 => 45,  124 => 37,  117 => 32,  112 => 27,  90 => 23,  87 => 26,  82 => 20,  72 => 20,  68 => 20,  65 => 15,  52 => 15,  43 => 6,  37 => 5,  30 => 4,  27 => 3,  25 => 7,  24 => 6,  22 => 2,  19 => 1,);
    }
}
