<?php

/* ReportBundle::layout.html.twig */
class __TwigTemplate_58864cfc9611ec8715ffc772d6a53aa1 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'html_headjs_load' => array($this, 'block_html_headjs_load'),
            'html_head' => array($this, 'block_html_head'),
            'page_js_exec' => array($this, 'block_page_js_exec'),
            'nav_block' => array($this, 'block_nav_block'),
            'all_page' => array($this, 'block_all_page'),
            'pagebar' => array($this, 'block_pagebar'),
            'content' => array($this, 'block_content'),
            'page' => array($this, 'block_page'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE HTML>
<html lang=\"en\">
<head>
\t<meta charset=\"utf-8\" />
\t<meta name=\"robots\" content=\"NOINDEX\" />
\t<meta http-equiv=\"X-UA-Compatible\" content=\"IE=100\" />
\t<title>DeskPRO Reports</title>

\t";
        // line 9
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("agent_vendors_css");
        echo "
\t";
        // line 10
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("agent_interface_css1");
        echo "
\t";
        // line 11
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("agent_interface_css2");
        echo "
\t";
        // line 12
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("admin_interface_css");
        echo "

\t<script type=\"text/javascript\" src=\"";
        // line 14
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("vendor/html5shiv.min.js"), "html", null, true);
        echo "\"></script>
\t";
        // line 15
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("agent_vendors");
        echo "
\t";
        // line 16
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("agent_common");
        echo "
\t";
        // line 17
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("agent_deskpro_ui");
        echo "
\t";
        // line 18
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("admin_admin_ui");
        echo "
\t";
        // line 19
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("admin_admin_handlers");
        echo "
\t";
        // line 20
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("report_report_ui");
        echo "

\t";
        // line 22
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("report_interface_css");
        echo "

\t<link rel=\"stylesheet\" href=\"";
        // line 24
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("fonts/font-awesome.min.css"), "html", null, true);
        echo "\" type=\"text/css\" />

\t<script type=\"text/javascript\" charset=\"utf-8\">
\t\tvar DP_TINYMCE_URL = '";
        // line 27
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("javascripts/tiny_mce/tiny_mce.js"), "html", null, true);
        echo "';
\t\tvar BASE_URL = '";
        // line 28
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
        echo "';
\t\tvar BASE_PATH = '";
        // line 29
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, strtr($this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), array("/index.php" => "")), "html", null, true);
        echo "/';
\t\tvar ASSETS_BASE_URL = '";
        // line 30
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl(""), "html", null, true);
        echo "';
\t\tif (!window.DESKPRO_URL_REGISTRY) window.DESKPRO_URL_REGISTRY = {};

\t\twindow._page_exec_stack = [];
\t\tfunction _win_exec() {
\t\t\twindow.DeskPRO_Window = new DeskPRO.Report.Window();
\t\t\twindow.DeskPRO_Window.initPage();

\t\t\tif (window._page_exec) {
\t\t\t\twindow._page_exec_stack.push(_page_exec);
\t\t\t}

\t\t\tfor (var i = 0; i < window._page_exec_stack.length; i++) {
\t\t\t\twindow._page_exec_stack[i]();
\t\t\t}

\t\t\tif (window.DeskPRO_Page) {
\t\t\t\tDeskPRO_Page.initPage();
\t\t\t}

\t\t\t// Init all others too
\t\t\tvar re = /^DeskPRO_Page_/;
\t\t\tfor (i in window) {
\t\t\t\tif (re.test(i)) {
\t\t\t\t\twindow[i].initPage();
\t\t\t\t}
\t\t\t}
\t\t}

\t\t";
        // line 59
        $this->displayBlock('html_headjs_load', $context, $blocks);
        // line 60
        echo "
\t\t\$(document).ready(function() {
\t\t\t_win_exec();
\t\t});
\t</script>
\t";
        // line 65
        $this->displayBlock('html_head', $context, $blocks);
        // line 66
        echo "\t";
        $this->displayBlock('page_js_exec', $context, $blocks);
        // line 67
        echo "
\t<script type=\"text/javascript\" src=\"";
        // line 68
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("vendor/less/less.min.js"), "html", null, true);
        echo "\"></script>
\t<style type=\"text/css\">
\t\t#dp_header {
\t\t\toverflow: hidden;
\t\t}
\t</style>
</head>
<body class=\"reports-interface ";
        // line 75
        if (isset($context["is_demo"])) { $_is_demo_ = $context["is_demo"]; } else { $_is_demo_ = null; }
        if ($_is_demo_) {
            echo "is-demo";
        }
        echo "\">

<!-- BEGIN HEADER BAR -->
<div id=\"dp_header\">

\t<div class=\"user-profile pull-left\">
\t\t<img class=\"gravatar pull-left\" src=\"";
        // line 81
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "user"), "getPictureUrl", array(0 => 31), "method"), "html", null, true);
        echo "\">
\t\t<div class=\"username\">";
        // line 82
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.chrome.hello-user", array("name" => $this->getAttribute($this->getAttribute($_app_, "user"), "name")));
        echo "</div>
\t\t<ul class=\"nav-profile\">
\t\t\t";
        // line 84
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "user"), "can_admin")) {
            echo "<li><i class=\"icon-cogs\"></i> <a class=\"admin\" href=\"";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
            echo "/admin/login?dpsid-agent=";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "getSession", array(), "method"), "getId", array(), "method"), "html", null, true);
            echo "\"><span>Admin</span></a></li>";
        }
        // line 85
        echo "\t\t\t";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "user"), "can_agent")) {
            echo "<li><i class=\"icon-inbox\"></i> <a class=\"agent\" href=\"";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
            echo "/agent/\"><span class=\"check\">Agent</span></a></li>";
        }
        // line 86
        echo "\t\t\t<li onclick=\"window.location='";
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("user_logout", array("auth" => $this->env->getExtension('deskpro_templating')->staticSecurityToken("user_logout", 0))), "html", null, true);
        echo "?to=agent'; return false;\"><i class=\"icon-signout\"></i> <a>";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.chrome.link_logout");
        echo "</a></li>
\t\t</ul>
\t</div>

\t<ul class=\"reports-nav\">
\t\t<li class=\"trends ";
        // line 91
        if (isset($context["this_page"])) { $_this_page_ = $context["this_page"]; } else { $_this_page_ = null; }
        if (($_this_page_ == "overview")) {
            echo "on";
        }
        echo "\">
\t\t\t<span
\t\t\t\tclass=\"tipped\"
\t\t\t\tdata-tipped-options=\"hook: 'topleft', maxWidth: 500, skin: 'cloud', border: 1, shadow: {opacity:  0.5}\"
\t\t\t\ttitle=\"Overview gives you a clear, simple, interactive dashboard into the key metrics from your helpdesk. You can modify most statistics to sub-group the data by a particular property you are interested in or change the date range a statistic is generated for.\"
\t\t\t><a href=\"";
        // line 96
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report"), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.report.overview");
        echo "</a></span>
\t\t</li>
\t\t<li class=\"builder ";
        // line 98
        if (isset($context["this_page"])) { $_this_page_ = $context["this_page"]; } else { $_this_page_ = null; }
        if (($_this_page_ == "report_builder")) {
            echo "on";
        }
        echo "\">
\t\t\t<span
\t\t\t\tclass=\"tipped\"
\t\t\t\tdata-tipped-options=\"hook: 'topleft', maxWidth: 500, skin: 'cloud', border: 1, shadow: {opacity:  0.5}\"
\t\t\t\ttitle=\"Report Builder is our powerful custom statistic and graph generator. With this tool you can generate almost any statistic about your helpdesk. The tools comes with a library of pre-built statistics and an editor to allow you to create your own.\"
\t\t\t><a href=\"";
        // line 103
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_builder"), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.report.report_builder");
        echo "</a></span>
\t\t</li>
\t\t";
        // line 105
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.enable_billing"), "method")) {
            // line 106
            echo "\t\t<li class=\"billing ";
            if (isset($context["this_page"])) { $_this_page_ = $context["this_page"]; } else { $_this_page_ = null; }
            if (($_this_page_ == "report_billing")) {
                echo "on";
            }
            echo "\">
\t\t\t<span
\t\t\t\tclass=\"tipped\"
\t\t\t\tdata-tipped-options=\"hook: 'topleft', maxWidth: 500, skin: 'cloud', border: 1, shadow: {opacity:  0.5}\"
\t\t\t\ttitle=\"Billing gives you access to reports on charges that agents have made to tickets. Billing reports include lists of charges and totals for charges per person, agent and organization.\"
\t\t\t><a href=\"";
            // line 111
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_billing"), "html", null, true);
            echo "\">Billing</a></span>
\t\t</li>
\t\t";
        }
        // line 114
        echo "\t\t<li class=\"tech-activity ";
        if (isset($context["this_page"])) { $_this_page_ = $context["this_page"]; } else { $_this_page_ = null; }
        if (($_this_page_ == "agent_activity_index")) {
            echo "on";
        }
        echo "\">
\t\t\t<span
\t\t\t\tclass=\"tipped\"
\t\t\t\tdata-tipped-options=\"hook: 'topleft', maxWidth: 500, skin: 'cloud', border: 1, shadow: {opacity:  0.5}\"
\t\t\t\ttitle=\"Agent Activity gives you a detailed report into the activity of your agents on a specified date.\"
\t\t\t><a href=\"";
        // line 119
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_agent_activity_index"), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.report.agent_activity");
        echo "</a></span>
\t\t</li>
\t\t<li class=\"tech-hours ";
        // line 121
        if (isset($context["this_page"])) { $_this_page_ = $context["this_page"]; } else { $_this_page_ = null; }
        if (($_this_page_ == "report_agent_hours_index")) {
            echo "on";
        }
        echo "\">
\t\t\t<span
\t\t\t\tclass=\"tipped\"
\t\t\t\tdata-tipped-options=\"hook: 'topleft', maxWidth: 500, skin: 'cloud', border: 1, shadow: {opacity:  0.5}\"
\t\t\t\ttitle=\"Agent Hours gives you an aggregate view of the time your agents are logged into the helpdesk on a specified date.\"
\t\t\t><a href=\"";
        // line 126
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_agent_hours_index"), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.report.agent_hours");
        echo "</a></span>
\t\t</li>
\t\t";
        // line 128
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.tickets.enable_feedback"), "method")) {
            // line 129
            echo "\t\t\t<li class=\"feedback ";
            if (isset($context["this_page"])) { $_this_page_ = $context["this_page"]; } else { $_this_page_ = null; }
            if ((($_this_page_ == "report_agent_feedback_feed") || ($_this_page_ == "report_agent_feedback_index"))) {
                echo "on";
            }
            echo "\">
\t\t\t\t<span
\t\t\t\t\tclass=\"tipped\"
\t\t\t\t\tdata-tipped-options=\"hook: 'topleft', maxWidth: 500, skin: 'cloud', border: 1, shadow: {opacity:  0.5}\"
\t\t\t\t\ttitle=\"Feedback gives you a feed and aggregate view of the ticket feedback ratings from your customers.\"
\t\t\t\t><a href=\"";
            // line 134
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_agent_feedback_feed", array("page" => 0)), "html", null, true);
            echo "\">Ticket Feedback</a></span>
\t\t\t</li>
\t\t";
        }
        // line 137
        echo "\t</ul>
</div>
<!-- /BEGIN HEADER BAR -->

";
        // line 141
        $this->displayBlock('nav_block', $context, $blocks);
        // line 142
        echo "
";
        // line 143
        $this->displayBlock('all_page', $context, $blocks);
        // line 162
        echo "
</body>
</html>
";
    }

    // line 59
    public function block_html_headjs_load($context, array $blocks = array())
    {
    }

    // line 65
    public function block_html_head($context, array $blocks = array())
    {
    }

    // line 66
    public function block_page_js_exec($context, array $blocks = array())
    {
    }

    // line 141
    public function block_nav_block($context, array $blocks = array())
    {
    }

    // line 143
    public function block_all_page($context, array $blocks = array())
    {
        // line 144
        echo "<div id=\"dp_page_wrap\" class=\"dp_page_wrap\">

\t";
        // line 146
        ob_start();
        $this->displayBlock('pagebar', $context, $blocks);
        $context["pagebar"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 147
        echo "\t";
        if (isset($context["pagebar"])) { $_pagebar_ = $context["pagebar"]; } else { $_pagebar_ = null; }
        if (twig_length_filter($this->env, $this->env->getExtension('deskpro_templating')->strTrim($_pagebar_))) {
            // line 148
            echo "\t\t<div id=\"dp_admin_pagebar\" class=\"dp_admin_pagebar\">";
            if (isset($context["pagebar"])) { $_pagebar_ = $context["pagebar"]; } else { $_pagebar_ = null; }
            echo $_pagebar_;
            echo "</div>
\t";
        }
        // line 150
        echo "
\t<div id=\"dp_admin_page\" class=\"dp_admin_page ";
        // line 151
        if (isset($context["pagebar"])) { $_pagebar_ = $context["pagebar"]; } else { $_pagebar_ = null; }
        if ((!twig_length_filter($this->env, $this->env->getExtension('deskpro_templating')->strTrim($_pagebar_)))) {
            echo "no-header";
        }
        echo "\">
\t\t";
        // line 152
        $this->displayBlock('content', $context, $blocks);
        // line 159
        echo "\t</div>
</div>
";
    }

    // line 146
    public function block_pagebar($context, array $blocks = array())
    {
    }

    // line 152
    public function block_content($context, array $blocks = array())
    {
        // line 153
        echo "\t\t\t<div class=\"dp-page-box\">
\t\t\t\t<div class=\"page-content\">
\t\t\t\t\t";
        // line 155
        $this->displayBlock('page', $context, $blocks);
        // line 156
        echo "\t\t\t\t</div>
\t\t\t</div>
\t\t";
    }

    // line 155
    public function block_page($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "ReportBundle::layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  436 => 155,  430 => 156,  428 => 155,  424 => 153,  421 => 152,  416 => 146,  410 => 159,  408 => 152,  401 => 151,  391 => 148,  387 => 147,  383 => 146,  379 => 144,  376 => 143,  371 => 141,  366 => 66,  361 => 65,  356 => 59,  349 => 162,  347 => 143,  344 => 142,  342 => 141,  336 => 137,  330 => 134,  318 => 129,  315 => 128,  308 => 126,  297 => 121,  290 => 119,  278 => 114,  272 => 111,  260 => 106,  257 => 105,  250 => 103,  239 => 98,  232 => 96,  221 => 91,  210 => 86,  201 => 85,  190 => 84,  184 => 82,  179 => 81,  167 => 75,  157 => 68,  154 => 67,  151 => 66,  149 => 65,  142 => 60,  140 => 59,  108 => 30,  103 => 29,  98 => 28,  94 => 27,  88 => 24,  83 => 22,  78 => 20,  74 => 19,  70 => 18,  66 => 17,  58 => 15,  54 => 14,  49 => 12,  45 => 11,  41 => 10,  37 => 9,  673 => 200,  669 => 198,  664 => 195,  649 => 193,  634 => 191,  631 => 190,  617 => 189,  603 => 188,  599 => 186,  587 => 184,  581 => 183,  569 => 181,  563 => 180,  551 => 178,  545 => 177,  533 => 175,  527 => 174,  515 => 172,  506 => 171,  503 => 170,  497 => 169,  494 => 168,  481 => 165,  473 => 164,  467 => 163,  463 => 162,  460 => 161,  457 => 160,  454 => 159,  450 => 158,  446 => 157,  442 => 156,  423 => 155,  419 => 154,  398 => 150,  392 => 152,  389 => 151,  386 => 150,  368 => 149,  363 => 148,  360 => 147,  343 => 146,  338 => 143,  328 => 141,  323 => 140,  317 => 136,  314 => 135,  280 => 111,  269 => 110,  262 => 107,  237 => 85,  230 => 81,  223 => 77,  216 => 73,  209 => 69,  202 => 65,  172 => 37,  169 => 36,  163 => 33,  155 => 31,  152 => 30,  147 => 29,  141 => 28,  136 => 25,  132 => 23,  112 => 21,  107 => 20,  104 => 19,  100 => 18,  90 => 15,  77 => 14,  72 => 13,  62 => 16,  55 => 7,  52 => 6,  47 => 3,  29 => 5,  27 => 1,);
    }
}
