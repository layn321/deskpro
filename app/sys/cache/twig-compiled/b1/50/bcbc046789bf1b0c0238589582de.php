<?php

/* BillingBundle::layout.html.twig */
class __TwigTemplate_b150bcbc046789bf1b0c0238589582de extends \Application\DeskPRO\Twig\Template
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
        echo "<!DOCTYPE HTML>
<html lang=\"en\">
<head>
\t<title>";
        // line 4
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "isCloud", array(), "method")) {
            echo "Billing";
        } else {
            echo "Licensing";
        }
        echo "</title>
\t<meta charset=\"utf-8\" />
\t<meta name=\"robots\" content=\"noindex,nofollow\" />
\t<meta http-equiv=\"X-UA-Compatible\" content=\"IE=100\" />
\t";
        // line 8
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.favicon_blob_id"), "method")) {
            // line 9
            echo "\t\t<link rel=\"shortcut icon\" id=\"favicon\" href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("favicon", array(), true), "html", null, true);
            echo "\" />
\t";
        } else {
            // line 11
            echo "\t\t<link rel=\"shortcut icon\" id=\"favicon\" href=\"";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "getRequest", array(), "method"), "getBasePath", array(), "method"), "html", null, true);
            echo "/favicon.ico\" />
\t";
        }
        // line 13
        echo "
\t<script type=\"text/javascript\">
\t\t";
        // line 15
        if ($this->env->getExtension('deskpro_templating')->getConstant("DP_DEBUG")) {
            // line 16
            echo "\t\t\tDP_DEBUG = true;
\t\t";
        } else {
            // line 18
            echo "\t\t\tDP_DEBUG = false;
\t\t";
        }
        // line 20
        echo "\t</script>

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
\t\tvar BASE_URL = '";
        // line 35
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
        echo "/';
\t\tvar BASE_PATH = '";
        // line 36
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, strtr($this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), array("/index.php" => "")), "html", null, true);
        echo "/';
\t\tvar ASSETS_BASE_URL = '";
        // line 37
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl(""), "html", null, true);
        echo "';

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
        // line 53
        $this->displayBlock('html_headjs_load', $context, $blocks);
        // line 54
        echo "
\t\t\$(document).ready(function() {
\t\t\t_win_exec();
\t\t});
\t</script>
\t";
        // line 59
        $this->displayBlock('html_head', $context, $blocks);
        // line 60
        echo "\t";
        $this->displayBlock('page_js_exec', $context, $blocks);
        // line 61
        echo "
\t<script type=\"text/javascript\" src=\"";
        // line 62
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("vendor/less/less.min.js"), "html", null, true);
        echo "\"></script>
</head>
<body class=\"billing-interface ";
        // line 64
        if (isset($context["body_class"])) { $_body_class_ = $context["body_class"]; } else { $_body_class_ = null; }
        echo twig_escape_filter($this->env, $_body_class_, "html", null, true);
        echo "\">

";
        // line 66
        $this->displayBlock('header', $context, $blocks);
        // line 81
        echo "
<!-- /BEGIN NAV BAR -->
<div id=\"dp_page_wrap\">

";
        // line 85
        ob_start();
        $this->displayBlock('pagebar', $context, $blocks);
        $context["pagebar"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 86
        if (isset($context["pagebar"])) { $_pagebar_ = $context["pagebar"]; } else { $_pagebar_ = null; }
        if (twig_length_filter($this->env, $this->env->getExtension('deskpro_templating')->strTrim($_pagebar_))) {
            // line 87
            echo "\t<div id=\"dp_admin_pagebar\" class=\"dp_admin_pagebar\">";
            if (isset($context["pagebar"])) { $_pagebar_ = $context["pagebar"]; } else { $_pagebar_ = null; }
            echo $_pagebar_;
            echo "</div>
";
        }
        // line 89
        echo "
";
        // line 90
        $this->displayBlock('pagebar_after', $context, $blocks);
        // line 91
        echo "
";
        // line 92
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "session"), "hasFlash", array(0 => "success_message"), "method")) {
            // line 93
            echo "\t<div class=\"alert-message success\">
\t\t";
            // line 94
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "session"), "getFlash", array(0 => "success_message"), "method"), "html", null, true);
            echo "
\t</div>
";
        }
        // line 97
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "session"), "hasFlash", array(0 => "error_message"), "method")) {
            // line 98
            echo "\t<div class=\"alert-message error\">
\t\t";
            // line 99
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "session"), "getFlash", array(0 => "error_message"), "method"), "html", null, true);
            echo "
\t</div>
";
        }
        // line 102
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "session"), "hasFlash", array(0 => "saved"), "method")) {
            // line 103
            echo "\t<div class=\"alert-message success\">
\t\t\"";
            // line 104
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "session"), "getFlash", array(0 => "saved"), "method"), "html", null, true);
            echo "\" was saved successfully
\t</div>
";
        }
        // line 107
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "session"), "hasFlash", array(0 => "deleted"), "method")) {
            // line 108
            echo "\t<div class=\"alert-message success\">
\t\t\"";
            // line 109
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "session"), "getFlash", array(0 => "deleted"), "method"), "html", null, true);
            echo "\" was deleted successfully
\t</div>
";
        }
        // line 112
        echo "
";
        // line 113
        ob_start();
        $this->displayBlock('sidebar', $context, $blocks);
        $context["sidebar"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 114
        ob_start();
        $this->displayBlock('sidebar_right', $context, $blocks);
        $context["sidebar_right"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 115
        $this->displayBlock('dp_full_page', $context, $blocks);
        // line 135
        echo "
</div>

";
        // line 138
        ob_start();
        $this->displayBlock('page_nav', $context, $blocks);
        $context["page_nav"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 139
        ob_start();
        $this->displayBlock('page_nav_inner', $context, $blocks);
        $context["page_nav_inner"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 140
        if (isset($context["page_nav"])) { $_page_nav_ = $context["page_nav"]; } else { $_page_nav_ = null; }
        if (isset($context["page_nav_inner"])) { $_page_nav_inner_ = $context["page_nav_inner"]; } else { $_page_nav_inner_ = null; }
        if ((twig_length_filter($this->env, $this->env->getExtension('deskpro_templating')->strTrim($_page_nav_)) || twig_length_filter($this->env, $this->env->getExtension('deskpro_templating')->strTrim($_page_nav_inner_)))) {
            // line 141
            echo "\t<div id=\"dp_page_nav\" ";
            if (isset($context["page_nav_fixed"])) { $_page_nav_fixed_ = $context["page_nav_fixed"]; } else { $_page_nav_fixed_ = null; }
            if ($_page_nav_fixed_) {
                echo "class=\"fixed\"";
            }
            echo ">
\t\t";
            // line 142
            if (isset($context["page_nav_inner"])) { $_page_nav_inner_ = $context["page_nav_inner"]; } else { $_page_nav_inner_ = null; }
            if (twig_length_filter($this->env, $this->env->getExtension('deskpro_templating')->strTrim($_page_nav_inner_))) {
                // line 143
                echo "\t\t\t";
                if (isset($context["page_nav_inner"])) { $_page_nav_inner_ = $context["page_nav_inner"]; } else { $_page_nav_inner_ = null; }
                echo $_page_nav_inner_;
                echo "
\t\t";
            } else {
                // line 145
                echo "\t\t\t<div class=\"page-nav-block\">
\t\t\t\t<div class=\"inner-shadow\"></div>
\t\t\t\t";
                // line 147
                if (isset($context["page_nav"])) { $_page_nav_ = $context["page_nav"]; } else { $_page_nav_ = null; }
                echo $_page_nav_;
                echo "
\t\t\t</div>
\t\t";
            }
            // line 150
            echo "\t</div>
";
        }
        // line 152
        echo "
</body>
</html>
";
    }

    // line 53
    public function block_html_headjs_load($context, array $blocks = array())
    {
    }

    // line 59
    public function block_html_head($context, array $blocks = array())
    {
    }

    // line 60
    public function block_page_js_exec($context, array $blocks = array())
    {
    }

    // line 66
    public function block_header($context, array $blocks = array())
    {
        // line 67
        echo "
<!-- BEGIN NAV BAR -->
<div id=\"dp_admin_nav\" class=\"billing\">
\t<div id=\"dp_admin_nav_sections\" class=\"deskproPane\">
\t\t<div style=\"float:right\">
\t\t\t";
        // line 72
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "user"), "can_admin")) {
            // line 73
            echo "\t\t\t\t<a href=\"";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
            echo "/admin/\" style=\"color: #fff; text-decoration: underline; font-size: 135%; padding-top: 9px; display: block;\">Back to Admin Interface</a>
\t\t\t";
        }
        // line 75
        echo "\t\t</div>
\t\t<h1>";
        // line 76
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "isCloud", array(), "method")) {
            echo "Billing";
        } else {
            echo "Licensing";
        }
        echo "</h1>
\t</div>
</div>

";
    }

    // line 85
    public function block_pagebar($context, array $blocks = array())
    {
    }

    // line 90
    public function block_pagebar_after($context, array $blocks = array())
    {
    }

    // line 113
    public function block_sidebar($context, array $blocks = array())
    {
    }

    // line 114
    public function block_sidebar_right($context, array $blocks = array())
    {
    }

    // line 115
    public function block_dp_full_page($context, array $blocks = array())
    {
        // line 116
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
        echo " with_top_border\">
\t";
        // line 117
        $this->displayBlock('prepage', $context, $blocks);
        // line 118
        echo "\t<div id=\"dp_admin_page_inner\" class=\"dp_admin_page_inner\">
\t\t";
        // line 119
        if (isset($context["sidebar"])) { $_sidebar_ = $context["sidebar"]; } else { $_sidebar_ = null; }
        if (twig_length_filter($this->env, $this->env->getExtension('deskpro_templating')->strTrim($_sidebar_))) {
            // line 120
            echo "\t\t\t<div id=\"dp_admin_page_sidebar\" class=\"dp_admin_page_sidebar dp-sidebar\">";
            if (isset($context["sidebar"])) { $_sidebar_ = $context["sidebar"]; } else { $_sidebar_ = null; }
            echo $_sidebar_;
            echo "</div>
\t\t";
        }
        // line 122
        echo "\t\t";
        if (isset($context["sidebar_right"])) { $_sidebar_right_ = $context["sidebar_right"]; } else { $_sidebar_right_ = null; }
        if (twig_length_filter($this->env, $this->env->getExtension('deskpro_templating')->strTrim($_sidebar_right_))) {
            // line 123
            echo "\t\t\t<div id=\"dp_admin_page_sidebar_right\" class=\"dp_admin_page_sidebar_right dp-sidebar\">";
            if (isset($context["sidebar_right"])) { $_sidebar_right_ = $context["sidebar_right"]; } else { $_sidebar_right_ = null; }
            echo $_sidebar_right_;
            echo "</div>
\t\t";
        }
        // line 125
        echo "\t\t";
        $this->displayBlock('content', $context, $blocks);
        // line 132
        echo "\t</div>
</div>
";
    }

    // line 117
    public function block_prepage($context, array $blocks = array())
    {
    }

    // line 125
    public function block_content($context, array $blocks = array())
    {
        // line 126
        echo "\t\t\t<div class=\"dp-page-box\">
\t\t\t\t<div class=\"page-content\">
\t\t\t\t\t";
        // line 128
        $this->displayBlock('page', $context, $blocks);
        // line 129
        echo "\t\t\t\t</div>
\t\t\t</div>
\t\t";
    }

    // line 128
    public function block_page($context, array $blocks = array())
    {
    }

    // line 138
    public function block_page_nav($context, array $blocks = array())
    {
    }

    // line 139
    public function block_page_nav_inner($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "BillingBundle::layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  492 => 139,  487 => 138,  482 => 128,  476 => 129,  474 => 128,  470 => 126,  467 => 125,  462 => 117,  456 => 132,  453 => 125,  446 => 123,  442 => 122,  435 => 120,  432 => 119,  427 => 117,  411 => 116,  408 => 115,  403 => 114,  398 => 113,  393 => 90,  388 => 85,  374 => 76,  371 => 75,  364 => 73,  361 => 72,  354 => 67,  351 => 66,  346 => 60,  341 => 59,  336 => 53,  325 => 150,  318 => 147,  314 => 145,  307 => 143,  304 => 142,  296 => 141,  292 => 140,  288 => 139,  279 => 135,  273 => 114,  269 => 113,  266 => 112,  259 => 109,  256 => 108,  253 => 107,  243 => 103,  240 => 102,  230 => 98,  217 => 93,  211 => 91,  209 => 90,  206 => 89,  199 => 87,  196 => 86,  184 => 66,  178 => 64,  170 => 61,  167 => 60,  137 => 37,  127 => 35,  121 => 32,  113 => 30,  109 => 29,  105 => 28,  72 => 16,  70 => 15,  66 => 13,  53 => 9,  50 => 8,  38 => 4,  33 => 1,  345 => 177,  340 => 176,  334 => 175,  329 => 152,  316 => 164,  309 => 160,  282 => 143,  265 => 132,  260 => 129,  251 => 125,  244 => 123,  241 => 122,  224 => 115,  220 => 94,  205 => 111,  202 => 110,  179 => 96,  164 => 94,  156 => 53,  152 => 89,  141 => 84,  132 => 36,  73 => 39,  469 => 156,  459 => 148,  451 => 142,  440 => 141,  429 => 118,  413 => 139,  402 => 138,  392 => 137,  386 => 134,  376 => 127,  363 => 119,  355 => 114,  344 => 106,  339 => 103,  328 => 96,  324 => 95,  319 => 94,  311 => 90,  298 => 81,  293 => 148,  291 => 77,  287 => 145,  284 => 138,  280 => 72,  277 => 115,  272 => 137,  267 => 58,  262 => 52,  246 => 104,  232 => 187,  227 => 97,  222 => 185,  219 => 184,  193 => 164,  191 => 71,  186 => 81,  173 => 62,  161 => 93,  158 => 54,  153 => 61,  150 => 60,  147 => 59,  145 => 86,  136 => 52,  116 => 35,  111 => 34,  106 => 33,  102 => 32,  96 => 25,  92 => 24,  88 => 23,  84 => 22,  80 => 20,  76 => 18,  71 => 22,  67 => 21,  63 => 20,  59 => 11,  55 => 17,  51 => 15,  47 => 13,  45 => 12,  39 => 9,  30 => 2,  23 => 1,  254 => 132,  248 => 129,  237 => 123,  233 => 99,  214 => 92,  197 => 107,  192 => 85,  183 => 87,  180 => 86,  175 => 85,  171 => 84,  165 => 59,  157 => 79,  149 => 78,  142 => 77,  138 => 83,  134 => 75,  131 => 74,  123 => 73,  117 => 31,  114 => 70,  108 => 65,  104 => 63,  101 => 27,  89 => 55,  36 => 5,  32 => 3,  29 => 2,);
    }
}
