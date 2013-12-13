<?php

/* ReportBundle:Main:error-permission.html.twig */
class __TwigTemplate_a9ccb75abf011fd72d9089a6d172b989 extends \Application\DeskPRO\Twig\Template
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
\t<title>Billing Interface</title>
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
        echo "\t</script>

\t";
        // line 19
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("agent_vendors_css");
        echo "
\t";
        // line 20
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("agent_interface_css1");
        echo "
\t";
        // line 21
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("agent_interface_css2");
        echo "
\t";
        // line 22
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("admin_interface_css");
        echo "

\t<script type=\"text/javascript\" src=\"";
        // line 24
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("vendor/html5shiv.min.js"), "html", null, true);
        echo "\"></script>
\t";
        // line 25
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("agent_vendors");
        echo "
\t";
        // line 26
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("agent_common");
        echo "
\t";
        // line 27
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("agent_deskpro_ui");
        echo "
    ";
        // line 28
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("admin_admin_ui");
        echo "
    ";
        // line 29
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("admin_admin_handlers");
        echo "

    <script type=\"text/javascript\" charset=\"utf-8\">
\t\tvar DP_TINYMCE_URL = '";
        // line 32
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("javascripts/tiny_mce/tiny_mce.js"), "html", null, true);
        echo "';
\t\tvar BASE_URL = '";
        // line 33
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
        echo "/';
\t\tvar BASE_PATH = '";
        // line 34
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, strtr($this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), array("/index.php" => "")), "html", null, true);
        echo "/';
\t\tvar ASSETS_BASE_URL = '";
        // line 35
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
        // line 52
        $this->displayBlock('html_headjs_load', $context, $blocks);
        // line 53
        echo "
\t\t\$(document).ready(function() {
\t\t\t_win_exec();
\t\t});
\t</script>
\t";
        // line 58
        $this->displayBlock('html_head', $context, $blocks);
        // line 59
        echo "\t";
        $this->displayBlock('page_js_exec', $context, $blocks);
        // line 60
        echo "
\t<script type=\"text/javascript\" src=\"";
        // line 61
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("vendor/less/less.min.js"), "html", null, true);
        echo "\"></script>

\t";
        // line 63
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "isDebug", array(), "method")) {
            // line 64
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
            // line 66
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
        // line 68
        echo "</head>
<body class=\"dp\">

";
        // line 71
        $this->displayBlock('header', $context, $blocks);
        // line 164
        echo "
<!-- /BEGIN NAV BAR -->
<div id=\"dp_page_wrap\">

<div id=\"dp_admin_page\" class=\"dp_admin_page with-pagetabs\">
\t<div id=\"dp_admin_page_inner\" class=\"dp_admin_page_inner\">
\t\t<div class=\"dp-page-box\">
\t\t\t<div class=\"page-content\">
\t\t\t\t";
        // line 172
        if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
        if (isset($context["error_message"])) { $_error_message_ = $context["error_message"]; } else { $_error_message_ = null; }
        if ($_message_) {
            // line 173
            echo "\t\t\t\t\t";
            if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
            echo twig_escape_filter($this->env, $_message_, "html", null, true);
            echo "
\t\t\t\t";
        } elseif ($_error_message_) {
            // line 175
            echo "\t\t\t\t\t";
            if (isset($context["error_message"])) { $_error_message_ = $context["error_message"]; } else { $_error_message_ = null; }
            echo twig_escape_filter($this->env, $_error_message_, "html", null, true);
            echo "
\t\t\t\t";
        } else {
            // line 177
            echo "\t\t\t\t\t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.menu.error_no_permission");
            echo "
\t\t\t\t";
        }
        // line 179
        echo "\t\t\t</div>
\t\t</div>
\t</div>
</div>

</div>

</body>
</html>
";
    }

    // line 52
    public function block_html_headjs_load($context, array $blocks = array())
    {
    }

    // line 58
    public function block_html_head($context, array $blocks = array())
    {
    }

    // line 59
    public function block_page_js_exec($context, array $blocks = array())
    {
    }

    // line 71
    public function block_header($context, array $blocks = array())
    {
        // line 72
        echo "<!-- BEGIN HEADER BAR -->
<div id=\"dp_header\">
\t";
        // line 74
        if (isset($context["is_post_install"])) { $_is_post_install_ = $context["is_post_install"]; } else { $_is_post_install_ = null; }
        if ($_is_post_install_) {
            // line 75
            echo "\t\t<h3>Administration</h3>
\t";
        } else {
            // line 77
            echo "\t\t";
            // line 78
            echo "\t\t<div id=\"dp_header_user\">
\t\t\t<div class=\"button-wrap\" id=\"userSetting_trigger\">
\t\t\t\t<h1>
\t\t\t\t\t<cite style=\"background-image: url(";
            // line 81
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "user"), "getPictureUrl", array(0 => 24), "method"), "html", null, true);
            echo ");\"></cite>
\t\t\t\t\t<span class=\"drop\"></span>
\t\t\t\t</h1>
\t\t\t</div>
\t\t</div>
\t\t<!-- Header - User Control Dropdown -->
\t\t<div id=\"userSetting\" style=\"display:none;\">
\t\t\t<div class=\"userProfileDropdown\" >
\t\t\t\t<div class=\"userProfileDropdownHeader\">
\t\t\t\t\t<a><em style=\"background-image: url(";
            // line 90
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "user"), "getPictureUrl", array(0 => 24), "method"), "html", null, true);
            echo ");\"></em></a>
\t\t\t\t\t<span class=\"drop\"></span>
\t\t\t\t</div>
\t\t\t\t<ul>
\t\t\t\t\t<li class=\"currentUsername\"><p>";
            // line 94
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "user"), "display_name"), "html", null, true);
            echo "</p></li>
\t\t\t\t\t<li class=\"help\"><a href=\"http://support.deskpro.com/\">";
            // line 95
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.help");
            echo "</a></li>
\t\t\t\t\t<li class=\"logout\"><a href=\"";
            // line 96
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("user_logout", array("auth" => $this->env->getExtension('deskpro_templating')->staticSecurityToken("user_logout", 0))), "html", null, true);
            echo "?to=admin\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.log_out");
            echo "</a></li>
\t\t\t\t</ul>
\t\t\t</div>
\t\t</div>


\t\t";
            // line 103
            echo "\t\t<div id=\"dp_logo_wrap\">
\t\t\t<div class=\"button-wrap\">
\t\t\t\t<h1>
\t\t\t\t\t";
            // line 106
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.deskpro");
            echo "
\t\t\t\t\t<span class=\"drop\"></span>
\t\t\t\t</h1>
\t\t\t</div>
\t\t</div>
\t\t<div id=\"dp_logo_expand_wrap\">
\t\t\t<div class=\"dropdown-header\">
\t\t\t\t<h1>
\t\t\t\t\t";
            // line 114
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.deskpro");
            echo "
\t\t\t\t\t<span class=\"drop\"></span>
\t\t\t\t</h1>
\t\t\t</div>
\t\t\t<div class=\"dropdown-content\">
\t\t\t\t<a href=\"http://www.deskpro.com/\" target=\"_blank\">";
            // line 119
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.interface.powered_by");
            echo " <span>";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.interface.deskpro_website_host");
            echo "</span></a>
\t\t\t</div>
\t\t</div>

\t\t<!-- BEGIN INTERFACE SECTION -->
\t\t<div id=\"DP-InterfaceSwitcher\">
\t\t\t<div class=\"DP-adminSwitch\">
\t\t\t\t<div class=\"adminSwitcher\">
\t\t\t\t\t<a class=\"admin\">";
            // line 127
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.reports_interface");
            echo "<span class=\"dropdownIcon\"></span></a>
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<!-- Header - Switch Button Interfaces- Dropdown -->
\t\t\t<div id=\"interfacesToggle\" style=\"display:none;\">
\t\t\t\t<div class=\"dropdownSwitcherWrap\">
\t\t\t\t\t<div class=\"switcherHeader\">
\t\t\t\t\t\t<a class=\"admin\"><span>";
            // line 134
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.reports_interface");
            echo "</span></a>
\t\t\t\t\t</div>
\t\t\t\t\t<ul>
\t\t\t\t\t\t";
            // line 137
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($this->getAttribute($_app_, "user"), "can_admin")) {
                echo "<li><a class=\"admin\" href=\"";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
                echo "/admin/\"><span class=\"check\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.admin_interface");
                echo "</span></a></li>";
            }
            // line 138
            echo "\t\t\t\t\t\t";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($this->getAttribute($_app_, "user"), "can_agent")) {
                echo "<li><a class=\"agent\" href=\"";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
                echo "/agent/\"><span class=\"check\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.agent_interface");
                echo "</span></a></li>";
            }
            // line 139
            echo "\t\t\t\t\t\t";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($this->getAttribute($_app_, "user"), "can_billing")) {
                echo "<li><a class=\"billing\" href=\"";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
                echo "/billing\" style=\"margin-right: -3px;\"><span class=\"check\">";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                if ($this->getAttribute($_app_, "isCloud", array(), "method")) {
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.billing_interface");
                } else {
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.license_interface");
                }
                echo "</span></a></li>";
            }
            // line 140
            echo "\t\t\t\t\t\t";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($this->getAttribute($_app_, "user"), "can_reports")) {
                echo "<li><a class=\"reports\" href=\"";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
                echo "/reports\"><span class=\"check\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.reports_interface");
                echo "</span></a></li>";
            }
            // line 141
            echo "\t\t\t\t\t\t";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($_app_, "getSetting", array(0 => "user.portal_enabled"), "method")) {
                echo "<li><a class=\"user\" href=\"";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
                echo "/\"><span class=\"check\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.user_interface");
                echo "</span></a></li>";
            }
            // line 142
            echo "\t\t\t\t\t</ul>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<!-- END INTERFACE SECTION -->
\t";
        }
        // line 148
        echo "</div>
<!-- /BEGIN HEADER BAR -->

<!-- BEGIN NAV BAR -->
<div id=\"dp_admin_nav\">
\t<div id=\"dp_admin_nav_sections\" class=\"deskproPane\">
\t\t<ul class=\"start-ul\">
\t\t\t<li>
\t\t\t\t<a href=\"";
        // line 156
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report"), "html", null, true);
        echo "\"><span class=\"start\">Reports</span></a>
\t\t\t</li>
\t\t</ul>
\t\t<div class=\"sep\">-</div>
\t</div>
</div>

";
    }

    public function getTemplateName()
    {
        return "ReportBundle:Main:error-permission.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  447 => 156,  437 => 148,  429 => 142,  418 => 141,  407 => 140,  370 => 137,  364 => 134,  354 => 127,  341 => 119,  333 => 114,  306 => 96,  289 => 90,  276 => 81,  271 => 78,  265 => 75,  258 => 72,  255 => 71,  240 => 52,  214 => 175,  207 => 173,  193 => 164,  186 => 68,  173 => 66,  161 => 64,  158 => 63,  116 => 35,  111 => 34,  106 => 33,  84 => 26,  80 => 25,  122 => 50,  113 => 44,  76 => 24,  59 => 19,  21 => 1,  69 => 19,  65 => 18,  61 => 17,  53 => 24,  64 => 16,  51 => 15,  31 => 3,  153 => 61,  150 => 60,  133 => 46,  102 => 32,  87 => 35,  75 => 19,  67 => 21,  50 => 8,  38 => 4,  35 => 12,  30 => 2,  1374 => 443,  1370 => 441,  1365 => 438,  1350 => 436,  1345 => 433,  1330 => 431,  1315 => 429,  1312 => 428,  1305 => 427,  1302 => 426,  1298 => 424,  1295 => 423,  1283 => 422,  1276 => 421,  1268 => 420,  1265 => 419,  1247 => 418,  1236 => 417,  1218 => 416,  1211 => 414,  1208 => 413,  1201 => 411,  1198 => 410,  1180 => 409,  1177 => 408,  1168 => 404,  1164 => 403,  1158 => 399,  1151 => 397,  1129 => 393,  1112 => 392,  1101 => 390,  1096 => 389,  1092 => 387,  1087 => 386,  1084 => 385,  1066 => 382,  1058 => 381,  1041 => 380,  1035 => 376,  1030 => 373,  1015 => 371,  1010 => 368,  995 => 366,  981 => 365,  967 => 364,  953 => 363,  938 => 361,  935 => 360,  932 => 359,  928 => 357,  925 => 356,  913 => 355,  906 => 354,  898 => 353,  895 => 352,  877 => 351,  858 => 350,  839 => 349,  821 => 348,  810 => 347,  792 => 346,  782 => 344,  779 => 343,  772 => 341,  769 => 340,  751 => 339,  748 => 338,  742 => 337,  736 => 336,  726 => 331,  720 => 330,  714 => 329,  709 => 328,  703 => 324,  696 => 322,  682 => 321,  676 => 320,  654 => 316,  635 => 315,  629 => 314,  623 => 313,  620 => 312,  602 => 311,  591 => 309,  586 => 308,  582 => 306,  577 => 305,  574 => 304,  560 => 303,  554 => 302,  544 => 299,  540 => 297,  536 => 295,  532 => 294,  528 => 293,  524 => 292,  520 => 291,  516 => 290,  512 => 289,  508 => 288,  504 => 287,  500 => 286,  496 => 285,  490 => 284,  475 => 281,  469 => 280,  432 => 272,  329 => 209,  284 => 187,  267 => 175,  259 => 171,  238 => 155,  204 => 129,  175 => 104,  101 => 34,  95 => 32,  92 => 28,  81 => 24,  71 => 22,  46 => 9,  40 => 4,  32 => 9,  477 => 191,  434 => 181,  405 => 172,  331 => 160,  322 => 106,  311 => 154,  302 => 95,  266 => 145,  263 => 144,  245 => 58,  242 => 156,  196 => 138,  191 => 71,  188 => 134,  183 => 131,  99 => 49,  96 => 29,  56 => 11,  34 => 4,  24 => 4,  313 => 150,  310 => 149,  307 => 207,  304 => 147,  295 => 146,  291 => 145,  282 => 143,  279 => 185,  254 => 131,  249 => 130,  233 => 123,  226 => 120,  222 => 118,  218 => 116,  215 => 115,  203 => 172,  195 => 112,  187 => 111,  182 => 110,  159 => 97,  146 => 92,  128 => 83,  125 => 82,  63 => 20,  60 => 21,  44 => 5,  33 => 2,  399 => 79,  388 => 77,  380 => 138,  377 => 74,  373 => 72,  357 => 69,  346 => 68,  334 => 66,  316 => 63,  305 => 61,  298 => 60,  287 => 144,  251 => 51,  244 => 49,  241 => 124,  235 => 47,  231 => 45,  227 => 179,  219 => 140,  199 => 36,  192 => 35,  181 => 33,  174 => 32,  156 => 29,  145 => 58,  138 => 53,  126 => 57,  114 => 47,  91 => 34,  82 => 16,  68 => 14,  57 => 16,  48 => 7,  39 => 9,  36 => 3,  28 => 8,  23 => 1,  19 => 1,  436 => 273,  430 => 156,  428 => 155,  424 => 178,  421 => 152,  416 => 175,  410 => 159,  408 => 152,  401 => 151,  391 => 139,  387 => 171,  383 => 169,  379 => 144,  376 => 143,  371 => 221,  366 => 71,  361 => 65,  356 => 59,  349 => 219,  347 => 163,  344 => 142,  342 => 141,  336 => 137,  330 => 134,  318 => 208,  315 => 128,  308 => 126,  297 => 94,  290 => 148,  278 => 114,  272 => 147,  260 => 106,  257 => 105,  250 => 59,  239 => 98,  232 => 153,  221 => 177,  210 => 86,  201 => 139,  190 => 84,  184 => 82,  179 => 81,  167 => 75,  157 => 68,  154 => 96,  151 => 66,  149 => 65,  142 => 60,  140 => 59,  108 => 30,  103 => 20,  98 => 36,  94 => 35,  88 => 27,  83 => 33,  78 => 20,  74 => 21,  70 => 18,  66 => 27,  58 => 26,  54 => 14,  49 => 14,  45 => 12,  41 => 8,  37 => 7,  673 => 200,  669 => 198,  664 => 195,  649 => 193,  634 => 191,  631 => 190,  617 => 189,  603 => 188,  599 => 186,  587 => 184,  581 => 183,  569 => 181,  563 => 180,  551 => 178,  545 => 177,  533 => 175,  527 => 174,  515 => 172,  506 => 171,  503 => 170,  497 => 169,  494 => 168,  481 => 282,  473 => 164,  467 => 163,  463 => 279,  460 => 278,  457 => 187,  454 => 159,  450 => 158,  446 => 157,  442 => 277,  423 => 155,  419 => 154,  398 => 150,  392 => 152,  389 => 151,  386 => 150,  368 => 167,  363 => 148,  360 => 220,  343 => 146,  338 => 143,  328 => 141,  323 => 64,  317 => 103,  314 => 135,  280 => 57,  269 => 77,  262 => 74,  237 => 85,  230 => 122,  223 => 42,  216 => 38,  209 => 69,  202 => 65,  172 => 37,  169 => 36,  163 => 30,  155 => 31,  152 => 30,  147 => 59,  141 => 28,  136 => 52,  132 => 85,  112 => 46,  107 => 20,  104 => 38,  100 => 18,  90 => 22,  77 => 20,  72 => 31,  62 => 15,  55 => 17,  52 => 9,  47 => 13,  29 => 1,  27 => 1,);
    }
}
