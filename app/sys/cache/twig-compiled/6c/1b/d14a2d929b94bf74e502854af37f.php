<?php

/* BillingBundle:Main:error-permission.html.twig */
class __TwigTemplate_6c1bd14a2d929b94bf74e502854af37f extends \Application\DeskPRO\Twig\Template
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
\t\t\t\t<h3 style=\"padding-bottom: 5px;\">You do not have permission to use the billing interface</h3>
\t\t\t\t<p>
\t\t\t\t\tYour agent account does not have permission to use the billing interface.
\t\t\t\t</p>

\t\t\t\t<p>
\t\t\t\t\tTry contacting one of the following agents who are configured to handle billing for your helpdesk:
\t\t\t\t</p>

\t\t\t\t<div class=\"dp-icon-list\" style=\"padding: 3px; padding-bottom: 0;\">
\t\t\t\t\t<ul>
\t\t\t\t\t\t";
        // line 183
        if (isset($context["billing_agents"])) { $_billing_agents_ = $context["billing_agents"]; } else { $_billing_agents_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_billing_agents_);
        foreach ($context['_seq'] as $context["_key"] => $context["agent"]) {
            // line 184
            echo "\t\t\t\t\t\t\t<li style=\"line-height: 40px;\">
\t\t\t\t\t\t\t\t<div style=\"background: url(";
            // line 185
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "getPictureUrl", array(0 => 35), "method"), "html", null, true);
            echo ") no-repeat 0 50%; padding-left: 40px;\">
\t\t\t\t\t\t\t\t\t";
            // line 186
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "display_name"), "html", null, true);
            echo "
\t\t\t\t\t\t\t\t\t&lt;<a href=\"mailto:";
            // line 187
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "email_address"), "html", null, true);
            echo "\">";
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "email_address"), "html", null, true);
            echo "</a>&gt;
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['agent'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 191
        echo "\t\t\t\t\t</ul>
\t\t\t\t\t<br class=\"clear\" />
\t\t\t\t</div>
\t\t\t</div>
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
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.billing_interface");
            echo "<span class=\"dropdownIcon\"></span></a>
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<!-- Header - Switch Button Interfaces- Dropdown -->
\t\t\t<div id=\"interfacesToggle\" style=\"display:none;\">
\t\t\t\t<div class=\"dropdownSwitcherWrap\">
\t\t\t\t\t<div class=\"switcherHeader\">
\t\t\t\t\t\t<a class=\"admin\"><span>";
            // line 134
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.billing_interface");
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
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("billing"), "html", null, true);
        echo "\"><span class=\"start\">Billing</span></a>
\t\t\t</li>
\t\t</ul>
\t\t<div class=\"sep\">-</div>
\t</div>
</div>

";
    }

    public function getTemplateName()
    {
        return "BillingBundle:Main:error-permission.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  469 => 156,  459 => 148,  451 => 142,  440 => 141,  429 => 140,  413 => 139,  402 => 138,  392 => 137,  386 => 134,  376 => 127,  363 => 119,  355 => 114,  344 => 106,  339 => 103,  328 => 96,  324 => 95,  319 => 94,  311 => 90,  298 => 81,  293 => 78,  291 => 77,  287 => 75,  284 => 74,  280 => 72,  277 => 71,  272 => 59,  267 => 58,  262 => 52,  246 => 191,  232 => 187,  227 => 186,  222 => 185,  219 => 184,  193 => 164,  191 => 71,  186 => 68,  173 => 66,  161 => 64,  158 => 63,  153 => 61,  150 => 60,  147 => 59,  145 => 58,  136 => 52,  116 => 35,  111 => 34,  106 => 33,  102 => 32,  96 => 29,  92 => 28,  88 => 27,  84 => 26,  80 => 25,  76 => 24,  71 => 22,  67 => 21,  63 => 20,  59 => 19,  55 => 17,  51 => 15,  47 => 13,  45 => 12,  39 => 9,  30 => 2,  23 => 1,  254 => 132,  248 => 129,  237 => 123,  233 => 122,  214 => 183,  197 => 94,  192 => 92,  183 => 87,  180 => 86,  175 => 85,  171 => 84,  165 => 80,  157 => 79,  149 => 78,  142 => 77,  138 => 53,  134 => 75,  131 => 74,  123 => 73,  117 => 71,  114 => 70,  108 => 67,  104 => 65,  101 => 64,  89 => 55,  36 => 5,  32 => 3,  29 => 2,);
    }
}
