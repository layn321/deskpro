<?php

/* UserBundle::layout.html.twig */
class __TwigTemplate_2b00b6f781220ef48d3c6506c5e1c342 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'page_title' => array($this, 'block_page_title'),
            'head' => array($this, 'block_head'),
            'breadcrumb' => array($this, 'block_breadcrumb'),
            'sidebar' => array($this, 'block_sidebar'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 4
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
<html id=\"dp_html\" xmlns=\"http://www.w3.org/1999/xhtml\" class=\"";
        // line 5
        if ($this->env->getExtension('deskpro_templating')->isRtl()) {
            echo "rtl";
        }
        echo "\" ";
        echo $this->env->getExtension('deskpro_templating')->getLanguageHtmlAttributes();
        echo ">
<head>
\t<meta http-equiv=\"X-UA-Compatible\" content=\"IE=Edge,chrome=1\" />
\t<meta name=\"viewport\" content=\"width=device-width\" />

\t";
        // line 10
        if (isset($context["page_noindex"])) { $_page_noindex_ = $context["page_noindex"]; } else { $_page_noindex_ = null; }
        if ($_page_noindex_) {
            echo "<meta name=\"robots\" content=\"noindex,nofollow\" />";
        }
        // line 11
        echo "
\t";
        // line 12
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("user_helpdeskwin");
        echo "

\t";
        // line 14
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.favicon_blob_url"), "method")) {
            // line 15
            echo "\t\t";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if (twig_in_filter("http", $this->getAttribute($_app_, "getSetting", array(0 => "core.favicon_blob_url"), "method"))) {
                // line 16
                echo "\t\t\t<link rel=\"shortcut icon\" id=\"favicon\" href=\"";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "core.favicon_blob_url"), "method"), "html", null, true);
                echo "\" />
\t\t";
            } else {
                // line 18
                echo "\t\t\t<link rel=\"shortcut icon\" id=\"favicon\" href=\"";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo twig_escape_filter($this->env, strtr($this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), array("/index.php" => "")), "html", null, true);
                echo "/";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "core.favicon_blob_url"), "method"), "html", null, true);
                echo "\" />
\t\t";
            }
            // line 20
            echo "\t";
        }
        // line 21
        echo "\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
\t<title>
\t\t";
        // line 23
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "is_homepage")) {
            // line 24
            echo "\t\t\t";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "core.deskpro_name"), "method"), "html", null, true);
            echo "
\t\t";
        } else {
            // line 26
            echo "\t\t\t";
            $this->displayBlock('page_title', $context, $blocks);
            echo " - ";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "core.deskpro_name"), "method"), "html", null, true);
            echo "
\t\t";
        }
        // line 28
        echo "\t</title>

\t<meta property=\"og:site_name\" content=\"";
        // line 30
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_app_, "getSetting", array(0 => "core.site_name"), "method", true, true)) ? (_twig_default_filter($this->getAttribute($_app_, "getSetting", array(0 => "core.site_name"), "method"), $this->getAttribute($_app_, "getSetting", array(0 => "core.core.helpdesk_name"), "method"))) : ($this->getAttribute($_app_, "getSetting", array(0 => "core.core.helpdesk_name"), "method"))), "html", null, true);
        echo "\" />
\t";
        // line 31
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.facebook_admins"), "method")) {
            echo "<meta property=\"fb:admins\" content=\"";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "core.facebook_admins"), "method"), "html", null, true);
            echo "\" />";
        }
        // line 32
        echo "
\t";
        // line 33
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "admin_portal_controls")) {
            // line 34
            echo "\t\t<script type=\"text/javascript\">var IS_ADMIN_CONTROLS = true;</script>
\t\t";
            // line 35
            echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("agent_vendors");
            echo "
\t\t";
            // line 36
            echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("agent_common");
            echo "
\t\t";
            // line 37
            echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("user_portaladmin");
            echo "
\t\t";
            // line 38
            echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("user_portaladmin_css");
            echo "
\t";
        }
        // line 40
        echo "
\t";
        // line 41
        $this->env->loadTemplate("UserBundle::layout-resources.html.twig")->display($context);
        // line 42
        echo "
\t";
        // line 43
        $this->displayBlock('head', $context, $blocks);
        // line 44
        echo "\t";
        $this->env->loadTemplate("UserBundle::custom-headinclude.html.twig")->display($context);
        // line 45
        echo "</head>
<body>
<div id=\"fb-root\"></div>
<script type=\"text/javascript\">document.body.className += \" dp-with-js\";</script>

";
        // line 51
        ob_start();
        $this->displayBlock('breadcrumb', $context, $blocks);
        $context["breadcrumb"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 52
        if (isset($context["breadcrumb"])) { $_breadcrumb_ = $context["breadcrumb"]; } else { $_breadcrumb_ = null; }
        if ((!$this->env->getExtension('deskpro_templating')->strTrim($_breadcrumb_))) {
            $context["breadcrumb"] = false;
        }
        // line 53
        ob_start();
        // line 54
        echo "\t";
        $this->displayBlock('sidebar', $context, $blocks);
        $context["sidebar"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 66
        if (isset($context["sidebar"])) { $_sidebar_ = $context["sidebar"]; } else { $_sidebar_ = null; }
        if ((!$this->env->getExtension('deskpro_templating')->strTrim($_sidebar_))) {
            $context["sidebar"] = false;
        }
        // line 67
        echo "
";
        // line 68
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "admin_portal_controls")) {
            // line 69
            echo "\t<div id=\"dp_custom_head_wrap\" class=\"dp-portal-block\" style=\"display: none\">
\t\t<div id=\"dp_custom_head\" class=\"dp-portal-block-content\"></div>
\t</div>
\t<div id=\"dp_custom_head_placeholder\" class=\"dp-portal-placeholder dp-portal-placeholder-head ";
            // line 72
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($this->getAttribute($_app_, "custom_templates"), "UserBundle::custom-headinclude.html.twig", array(), "array")) {
                echo "dp-portal-placeholder-highlight";
            }
            echo "\" data-portal-block=\"head_include\" data-portal-for=\"#dp_custom_head\" data-mode=\"head\">
\t\t<em>";
            // line 73
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.designer.edit_page_head");
            echo "</em>
\t</div>
\t<div id=\"dp_custom_header_wrap\" class=\"dp-portal-block\" ";
            // line 75
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ((!$this->getAttribute($this->getAttribute($_app_, "custom_templates"), "UserBundle::custom-header.html.twig", array(), "array"))) {
                echo "style=\"display: none\"";
            }
            echo ">
\t\t<div id=\"dp_custom_header\" class=\"dp-portal-block-content\">
\t\t\t";
            // line 77
            $this->env->loadTemplate("UserBundle::custom-header.html.twig")->display($context);
            // line 78
            echo "\t\t</div>
\t</div>
\t<div id=\"dp_custom_header_placeholder\" class=\"dp-portal-placeholder dp-portal-placeholder-header\" data-portal-block=\"header\" data-portal-for=\"#dp_custom_header\" ";
            // line 80
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($this->getAttribute($_app_, "custom_templates"), "UserBundle::custom-header.html.twig", array(), "array")) {
                echo "style=\"display: none\"";
            }
            echo ">
\t\t<em>";
            // line 81
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.designer.edit_header_section");
            echo "</em>
\t</div>
";
        } else {
            // line 84
            echo "\t";
            $this->env->loadTemplate("UserBundle::custom-header.html.twig")->display($context);
        }
        // line 86
        echo "
<div id=\"dp\">
\t<div id=\"dp_main\">

\t\t";
        // line 90
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "languages"), "isMultiLang", array(), "method")) {
            // line 91
            echo "\t\t\t<!--DP_OFFLINE_CACHE_REMOVE_START-->
\t\t\t<div id=\"dp_lang_chooser\">
\t\t\t\t<form id=\"dp_lang_chooser_form\" action=\"";
            // line 93
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_profile_setlang"), "html", null, true);
            echo "\" method=\"POST\">
\t\t\t\t\t<input type=\"hidden\" name=\"return\" value=\"";
            // line 94
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getReturnUrl", array(), "method"), "html", null, true);
            echo "\" />
\t\t\t\t\t";
            // line 95
            echo $this->env->getExtension('deskpro_templating')->formToken("lang_chooser");
            echo "
\t\t\t\t\t<label>";
            // line 96
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.portal.language_chooser");
            echo "</label>
\t\t\t\t\t<select name=\"language_id\" onchange=\"\$('#dp_lang_chooser_form').submit();\">
\t\t\t\t\t\t";
            // line 98
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "languages"), "getAll", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["lang"]) {
                // line 99
                echo "\t\t\t\t\t\t\t<option value=\"";
                if (isset($context["lang"])) { $_lang_ = $context["lang"]; } else { $_lang_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_lang_, "id"), "html", null, true);
                echo "\" data-flag=\"";
                if (isset($context["lang"])) { $_lang_ = $context["lang"]; } else { $_lang_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_lang_, "flag_image"), "html", null, true);
                echo "\" ";
                if (isset($context["lang"])) { $_lang_ = $context["lang"]; } else { $_lang_ = null; }
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                if (($this->getAttribute($_lang_, "id") == $this->getAttribute($this->getAttribute($this->getAttribute($_app_, "session"), "getLanguage", array(), "method"), "getId", array(), "method"))) {
                    echo "selected=\"selected\"";
                }
                echo ">";
                if (isset($context["lang"])) { $_lang_ = $context["lang"]; } else { $_lang_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_lang_, "title"), "html", null, true);
                echo "</option>
\t\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['lang'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 101
            echo "\t\t\t\t\t</select>
\t\t\t\t</form>
\t\t\t</div>
\t\t\t<!--DP_OFFLINE_CACHE_REMOVE_END-->
\t\t";
        }
        // line 106
        echo "
\t<div id=\"dp_main_inner\">

\t\t";
        // line 109
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "admin_portal_controls")) {
            // line 110
            echo "\t\t\t<div id=\"dp_header\" class=\"";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($_app_, "getLogoBlob", array(), "method")) {
                echo "dp-with-logo";
            }
            echo " ";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ((!$this->getAttribute($_app_, "getSetting", array(0 => "user.portal_simpleheader"), "method"))) {
                echo "disabled";
            }
            echo "\">
\t\t\t\t<a href=\"";
            // line 111
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user"), "html", null, true);
            echo "\"><img class=\"logo\" ";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($_app_, "getLogoBlob", array(), "method")) {
                echo "src=\"";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "getLogoBlob", array(), "method"), "getDownloadUrl", array(), "method"), "html", null, true);
                echo "\"";
            }
            echo " /></a>

\t\t\t\t<h1><a href=\"";
            // line 113
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user"), "html", null, true);
            echo "\">";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "user.portal_header"), "method"), "html", null, true);
            echo "</a></h1>
\t\t\t\t<h2 ";
            // line 114
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ((!$this->getAttribute($_app_, "getSetting", array(0 => "user.portal_tagline"), "method"))) {
                echo "style=\"display: none\"";
            }
            echo ">";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "user.portal_tagline"), "method"), "html", null, true);
            echo "</h2>
\t\t\t\t";
            // line 115
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($_app_, "admin_portal_controls")) {
                // line 116
                echo "\t\t\t\t\t<div id=\"dp_header_portal_off\" style=\"float:";
                if ($this->env->getExtension('deskpro_templating')->isRtl()) {
                    echo "right";
                } else {
                    echo "left";
                }
                echo "\">
\t\t\t\t\t\t";
                // line 117
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.designer.reenable_simple_header");
                echo "
\t\t\t\t\t</div>
\t\t\t\t";
            }
            // line 120
            echo "\t\t\t\t<br style=\"clear:both;height: 1px;overflow: hidden;\" />
\t\t\t</div>
\t\t";
        } else {
            // line 123
            echo "\t\t\t";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($_app_, "getSetting", array(0 => "user.portal_simpleheader"), "method")) {
                // line 124
                echo "\t\t\t\t";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                if ($this->getAttribute($_app_, "getLogoBlob", array(), "method")) {
                    // line 125
                    echo "\t\t\t\t\t<div id=\"dp_header\" class=\"dp-with-logo\">
\t\t\t\t\t\t<a href=\"";
                    // line 126
                    echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user"), "html", null, true);
                    echo "\"><img class=\"logo\" src=\"";
                    if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "getLogoBlob", array(), "method"), "getDownloadUrl", array(), "method"), "html", null, true);
                    echo "\" /></a>
\t\t\t\t\t</div>
\t\t\t\t";
                } else {
                    // line 129
                    echo "\t\t\t\t\t<h1><a href=\"";
                    echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user"), "html", null, true);
                    echo "\">";
                    if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "user.portal_header"), "method"), "html", null, true);
                    echo "</a></h1>
\t\t\t\t\t<h2 ";
                    // line 130
                    if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                    if ((!$this->getAttribute($_app_, "getSetting", array(0 => "user.portal_tagline"), "method"))) {
                        echo "style=\"display: none\"";
                    }
                    echo ">";
                    if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "user.portal_tagline"), "method"), "html", null, true);
                    echo "</h2>
\t\t\t\t\t<br style=\"clear:both; height: 1px;overflow: hidden;\" />
\t\t\t\t";
                }
                // line 133
                echo "\t\t\t";
            }
            // line 134
            echo "\t\t";
        }
        // line 135
        echo "
\t\t<div id=\"dp_header_bar\">
\t\t\t<section id=\"dp_header_title\">
\t\t\t\t<h1>";
        // line 138
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "user.portal_title"), "method"), "html", null, true);
        echo "</h1>
\t\t\t</section>
\t\t\t<!--DP_OFFLINE_CACHE_REMOVE_START-->
\t\t\t<section id=\"dp_header_search\" class=\"with-handler\" data-element-handler=\"DeskPRO.User.ElementHandler.OmniSearch\">
\t\t\t\t<form action=\"";
        // line 142
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_search"), "html", null, true);
        echo "\" class=\"dp-form-search\" id=\"dp_omnisearch\">
\t\t\t\t\t<input type=\"text\" name=\"q\" id=\"dp_search\" value=\"";
        // line 143
        if (isset($context["query"])) { $_query_ = $context["query"]; } else { $_query_ = null; }
        echo twig_escape_filter($this->env, $_query_, "html", null, true);
        echo "\" autocomplete=\"off\" />
\t\t\t\t\t<button class=\"dp-btn\" type=\"submit\">";
        // line 144
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.portal.search");
        echo "</button>
\t\t\t\t</form>
\t\t\t</section>
\t\t\t<!--DP_OFFLINE_CACHE_REMOVE_END-->
\t\t</div>

\t\t<div id=\"dp_page_wrapper\" ";
        // line 150
        if (isset($context["any_tabs"])) { $_any_tabs_ = $context["any_tabs"]; } else { $_any_tabs_ = null; }
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (((!$_any_tabs_) && (!$this->getAttribute($_app_, "admin_portal_controls")))) {
            echo "class=\"dp-no-tabs\"";
        }
        echo ">

\t\t\t";
        // line 152
        if ($this->env->getExtension('deskpro_user_templating')->portalHasBlock("sidebar", "userinfo")) {
            // line 153
            echo "\t\t\t\t<div class=\"dp-device-small\">
\t\t\t\t\t";
            // line 154
            $this->env->loadTemplate("UserBundle:Portal:userinfo-topbar.html.twig")->display($context);
            // line 155
            echo "\t\t\t\t</div>
\t\t\t";
        }
        // line 157
        echo "
\t\t\t<div id=\"dp_sidebar_wrapper\">
\t\t\t\t<section id=\"dp_sidebar\">
\t\t\t\t\t";
        // line 160
        if (isset($context["sidebar"])) { $_sidebar_ = $context["sidebar"]; } else { $_sidebar_ = null; }
        echo twig_escape_filter($this->env, $_sidebar_, "html", null, true);
        echo "
\t\t\t\t</section>
\t\t\t</div>
\t\t\t<div id=\"dp_content_wrapper\">
\t\t\t\t";
        // line 164
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (twig_length_filter($this->env, $this->getAttribute($_app_, "getVariable", array(0 => "portal_tabs_order"), "method"))) {
            // line 165
            echo "\t\t\t\t\t<nav id=\"dp_content_tabs\">
\t\t\t\t\t\t<ul>
\t\t\t\t\t\t\t";
            // line 167
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_app_, "getVariable", array(0 => "portal_tabs_order"), "method"));
            $context['loop'] = array(
              'parent' => $context['_parent'],
              'index0' => 0,
              'index'  => 1,
              'first'  => true,
            );
            if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
                $length = count($context['_seq']);
                $context['loop']['revindex0'] = $length - 1;
                $context['loop']['revindex'] = $length;
                $context['loop']['length'] = $length;
                $context['loop']['last'] = 1 === $length;
            }
            foreach ($context['_seq'] as $context["_key"] => $context["tabtype"]) {
                // line 168
                echo "\t\t\t\t\t\t\t\t";
                if (isset($context["tabtype"])) { $_tabtype_ = $context["tabtype"]; } else { $_tabtype_ = null; }
                if (($_tabtype_ == "news")) {
                    // line 169
                    echo "\t\t\t\t\t\t\t\t\t<li data-tabtype=\"news\" class=\"dp-tab-news ";
                    if (isset($context["this_section"])) { $_this_section_ = $context["this_section"]; } else { $_this_section_ = null; }
                    if (($_this_section_ == "news")) {
                        echo "on";
                    }
                    echo " ";
                    if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                    if ((!$this->getAttribute($_app_, "getSetting", array(0 => "user.portal_tab_news"), "method"))) {
                        echo "disabled";
                    }
                    echo "\" data-tab=\"news\">
\t\t\t\t\t\t\t\t\t\t<a href=\"";
                    // line 170
                    if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
                    if (($this->getAttribute($_loop_, "index") == 1)) {
                        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user"), "html", null, true);
                    } else {
                        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_news"), "html", null, true);
                    }
                    echo "\">
\t\t\t\t\t\t\t\t\t\t\t<i class=\"dp-icon-tasks\"></i> <h3>";
                    // line 171
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.portal.tab_news");
                    echo "</h3>
\t\t\t\t\t\t\t\t\t\t\t<label>";
                    // line 172
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.portal.tab_news-description");
                    echo "</label>
\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t";
                }
                // line 176
                echo "\t\t\t\t\t\t\t\t";
                if (isset($context["tabtype"])) { $_tabtype_ = $context["tabtype"]; } else { $_tabtype_ = null; }
                if (($_tabtype_ == "articles")) {
                    // line 177
                    echo "\t\t\t\t\t\t\t\t\t<li data-tabtype=\"articles\" class=\"dp-tab-articles ";
                    if (isset($context["this_section"])) { $_this_section_ = $context["this_section"]; } else { $_this_section_ = null; }
                    if (($_this_section_ == "articles")) {
                        echo "on";
                    }
                    echo " ";
                    if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                    if ((!$this->getAttribute($_app_, "getSetting", array(0 => "user.portal_tab_articles"), "method"))) {
                        echo "disabled";
                    }
                    echo "\" data-tab=\"articles\">
\t\t\t\t\t\t\t\t\t\t<a href=\"";
                    // line 178
                    if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
                    if (($this->getAttribute($_loop_, "index") == 1)) {
                        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user"), "html", null, true);
                    } else {
                        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_articles"), "html", null, true);
                    }
                    echo "\">
\t\t\t\t\t\t\t\t\t\t\t<i class=\"dp-icon-folder-open\"></i> <h3>";
                    // line 179
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.portal.tab_knowledgebase");
                    echo "</h3>
\t\t\t\t\t\t\t\t\t\t\t<label>";
                    // line 180
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.portal.tab_knowledgebase-description");
                    echo "</label>
\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t";
                }
                // line 184
                echo "\t\t\t\t\t\t\t\t";
                if (isset($context["tabtype"])) { $_tabtype_ = $context["tabtype"]; } else { $_tabtype_ = null; }
                if (($_tabtype_ == "feedback")) {
                    // line 185
                    echo "\t\t\t\t\t\t\t\t\t<li data-tabtype=\"feedback\" class=\"dp-tab-feedback ";
                    if (isset($context["this_section"])) { $_this_section_ = $context["this_section"]; } else { $_this_section_ = null; }
                    if (($_this_section_ == "feedback")) {
                        echo "on";
                    }
                    echo " ";
                    if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                    if ((!$this->getAttribute($_app_, "getSetting", array(0 => "user.portal_tab_feedback"), "method"))) {
                        echo "disabled";
                    }
                    echo "\" data-tab=\"feedback\">
\t\t\t\t\t\t\t\t\t\t<a href=\"";
                    // line 186
                    if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
                    if (($this->getAttribute($_loop_, "index") == 1)) {
                        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user"), "html", null, true);
                    } else {
                        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_feedback"), "html", null, true);
                    }
                    echo "\">
\t\t\t\t\t\t\t\t\t\t\t<i class=\"dp-icon-pencil\"></i> <h3>";
                    // line 187
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.portal.tab_feedback");
                    echo "</h3>
\t\t\t\t\t\t\t\t\t\t\t<label>";
                    // line 188
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.portal.tab_feedback-description");
                    echo "</label>
\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t";
                }
                // line 192
                echo "\t\t\t\t\t\t\t\t";
                if (isset($context["tabtype"])) { $_tabtype_ = $context["tabtype"]; } else { $_tabtype_ = null; }
                if (($_tabtype_ == "downloads")) {
                    // line 193
                    echo "\t\t\t\t\t\t\t\t\t<li data-tabtype=\"downloads\" class=\"dp-tab-downloads ";
                    if (isset($context["this_section"])) { $_this_section_ = $context["this_section"]; } else { $_this_section_ = null; }
                    if (($_this_section_ == "downloads")) {
                        echo "on";
                    }
                    echo " ";
                    if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                    if ((!$this->getAttribute($_app_, "getSetting", array(0 => "user.portal_tab_downloads"), "method"))) {
                        echo "disabled";
                    }
                    echo "\" data-tab=\"downloads\">
\t\t\t\t\t\t\t\t\t\t<a href=\"";
                    // line 194
                    if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
                    if (($this->getAttribute($_loop_, "index") == 1)) {
                        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user"), "html", null, true);
                    } else {
                        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_downloads_home"), "html", null, true);
                    }
                    echo "\">
\t\t\t\t\t\t\t\t\t\t\t<i class=\"dp-icon-file\"></i> <h3>";
                    // line 195
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.portal.tab_downloads");
                    echo "</h3>
\t\t\t\t\t\t\t\t\t\t\t<label>";
                    // line 196
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.portal.tab_downloads-description");
                    echo "</label>
\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t";
                }
                // line 200
                echo "\t\t\t\t\t\t\t\t";
                if (isset($context["tabtype"])) { $_tabtype_ = $context["tabtype"]; } else { $_tabtype_ = null; }
                if (($_tabtype_ == "newticket")) {
                    // line 201
                    echo "\t\t\t\t\t\t\t\t\t<li data-tabtype=\"newticket\" class=\"dp-tab-contact ";
                    if (isset($context["this_section"])) { $_this_section_ = $context["this_section"]; } else { $_this_section_ = null; }
                    if (($_this_section_ == "tickets")) {
                        echo "on";
                    }
                    echo " ";
                    if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                    if ((!$this->getAttribute($_app_, "getSetting", array(0 => "user.portal_tab_tickets"), "method"))) {
                        echo "disabled";
                    }
                    echo "\" data-tab=\"tickets\">
\t\t\t\t\t\t\t\t\t\t<a href=\"";
                    // line 202
                    if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
                    if (($this->getAttribute($_loop_, "index") == 1)) {
                        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user"), "html", null, true);
                    } else {
                        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_tickets_new"), "html", null, true);
                    }
                    echo "\">
\t\t\t\t\t\t\t\t\t\t\t<i class=\"dp-icon-inbox\"></i> <h3>";
                    // line 203
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.portal.tab_tickets");
                    echo "</h3>
\t\t\t\t\t\t\t\t\t\t\t<label>";
                    // line 204
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.portal.tab_tickets-description");
                    echo "</label>
\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t";
                }
                // line 208
                echo "\t\t\t\t\t\t\t";
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
                if (isset($context['loop']['length'])) {
                    --$context['loop']['revindex0'];
                    --$context['loop']['revindex'];
                    $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tabtype'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 209
            echo "\t\t\t\t\t\t</ul>
\t\t\t\t\t</nav>
\t\t\t\t";
        }
        // line 212
        echo "                <section id=\"dp_content\">
\t\t\t\t\t";
        // line 213
        if (isset($context["breadcrumb"])) { $_breadcrumb_ = $context["breadcrumb"]; } else { $_breadcrumb_ = null; }
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($_breadcrumb_ && (!$this->getAttribute($_app_, "getVariable", array(0 => "is_homepage"), "method")))) {
            // line 214
            echo "\t\t\t\t\t\t<nav id=\"dp_breadcrumb_wrap\">
\t\t\t\t\t\t\t<div class=\"dp-breadcrumb-fade\"></div>
\t\t\t\t\t\t\t<ul class=\"dp-breadcrumb\">
\t\t\t\t\t\t\t\t<li>
\t\t\t\t\t\t\t\t\t<a href=\"";
            // line 218
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user"), "html", null, true);
            echo "\"><i class=\"dp-icon-home\"></i></a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t";
            // line 220
            if (isset($context["breadcrumb"])) { $_breadcrumb_ = $context["breadcrumb"]; } else { $_breadcrumb_ = null; }
            echo twig_escape_filter($this->env, $_breadcrumb_, "html", null, true);
            echo "
\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t</nav>
\t\t\t\t\t";
        }
        // line 224
        echo "
\t\t\t\t\t";
        // line 225
        $this->env->loadTemplate("UserBundle::layout-flashes.html.twig")->display($context);
        // line 226
        echo "
\t\t\t\t\t<!--DP_OFFLINE_CACHE_PAGE_NOTE-->

\t\t\t\t\t";
        // line 229
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getVariable", array(0 => "is_homepage"), "method")) {
            // line 230
            echo "\t\t\t\t\t\t";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($_app_, "admin_portal_controls")) {
                // line 231
                echo "\t\t\t\t\t\t\t";
                ob_start();
                $this->env->loadTemplate("UserBundle:Portal:welcome-block.html.twig")->display($context);
                $context["welcome_block"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
                // line 232
                echo "\t\t\t\t\t\t\t";
                if (isset($context["welcome_block"])) { $_welcome_block_ = $context["welcome_block"]; } else { $_welcome_block_ = null; }
                $context["welcome_block"] = $this->env->getExtension('deskpro_templating')->strTrim($_welcome_block_);
                // line 233
                echo "\t\t\t\t\t\t\t<div id=\"dp_custom_welcome_wrap\" class=\"dp-portal-block\" ";
                if (isset($context["welcome_block"])) { $_welcome_block_ = $context["welcome_block"]; } else { $_welcome_block_ = null; }
                if ((!$_welcome_block_)) {
                    echo "style=\"display: none\"";
                }
                echo ">
\t\t\t\t\t\t\t\t<div id=\"dp_custom_welcome\" class=\"dp-portal-block-content\">
\t\t\t\t\t\t\t\t\t";
                // line 235
                if (isset($context["welcome_block"])) { $_welcome_block_ = $context["welcome_block"]; } else { $_welcome_block_ = null; }
                echo $_welcome_block_;
                echo "
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t<div id=\"dp_custom_welcome_placeholder\" class=\"dp-portal-placeholder dp-portal-placeholder-header\" data-portal-block=\"welcome\" data-portal-for=\"#dp_custom_welcome\" ";
                // line 238
                if (isset($context["welcome_block"])) { $_welcome_block_ = $context["welcome_block"]; } else { $_welcome_block_ = null; }
                if ($_welcome_block_) {
                    echo "style=\"display: none\"";
                }
                echo ">
\t\t\t\t\t\t\t\t<em>";
                // line 239
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.designer.edit_header_section");
                echo "</em>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t";
            } else {
                // line 242
                echo "\t\t\t\t\t\t\t";
                ob_start();
                $this->env->loadTemplate("UserBundle:Portal:welcome-block.html.twig")->display($context);
                $context["welcome_block"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
                // line 243
                echo "\t\t\t\t\t\t\t";
                if (isset($context["welcome_block"])) { $_welcome_block_ = $context["welcome_block"]; } else { $_welcome_block_ = null; }
                if ($this->env->getExtension('deskpro_templating')->strTrim($_welcome_block_)) {
                    if (isset($context["welcome_block"])) { $_welcome_block_ = $context["welcome_block"]; } else { $_welcome_block_ = null; }
                    echo $_welcome_block_;
                } else {
                    echo "<br/>";
                }
                // line 244
                echo "\t\t\t\t\t\t";
            }
            // line 245
            echo "\t\t\t\t\t";
        }
        // line 246
        echo "
\t\t\t\t\t";
        // line 247
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (isset($context["breadcrumb"])) { $_breadcrumb_ = $context["breadcrumb"]; } else { $_breadcrumb_ = null; }
        if (($this->getAttribute($_app_, "getVariable", array(0 => "is_homepage"), "method") && (!$_breadcrumb_))) {
            echo "<br/>";
        }
        // line 248
        echo "
\t\t\t\t\t";
        // line 249
        $this->displayBlock('content', $context, $blocks);
        // line 250
        echo "\t\t\t\t</section>
\t\t\t</div>
\t\t\t<footer id=\"dp_footer\">
\t\t\t\t";
        // line 253
        echo DeskPRO\Kernel\License::staticGetUserCopyrightHtml();
        echo "
\t\t\t</footer>
\t\t</div>
\t</div>
\t</div>

\t<div id=\"dp_search_assist\" style=\"display: none;\">
\t\t<div class=\"results\"></div>
\t\t<div class=\"foot\">
\t\t\t<div class=\"dp-more-link\">
\t\t\t\t<button class=\"dp-btn dp-btn-success\">";
        // line 263
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.portal.search_more-results");
        echo " ";
        echo $this->env->getExtension('deskpro_templating')->getLanguageArrow("right");
        echo "</button>
\t\t\t</div>
\t\t\t<h4>";
        // line 265
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.portal.search.no_matches");
        echo "</h4>
\t\t\t<ul>
\t\t\t\t<li class=\"ticket\"><a href=\"";
        // line 267
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_tickets_new"), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.portal.search.new-ticket");
        echo "</a></li>
\t\t\t\t<li class=\"feedback\"><a href=\"";
        // line 268
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_feedback_new"), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.portal.search.new-feedback");
        echo "</a></li>
\t\t\t\t";
        // line 269
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((((!$this->getAttribute($_app_, "getSetting", array(0 => "user.disable_chat_element"), "method")) && (!$this->getAttribute($_app_, "admin_portal_controls"))) && $this->getAttribute($this->getAttribute($_app_, "user"), "hasPerm", array(0 => "chat.use"), "method"))) {
            // line 270
            echo "\t\t\t\t\t<li class=\"chat no-omni-trigger\"><a href=\"#\" class=\"dp-chat-trigger\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.portal.search.new-chat");
            echo "</a></li>
\t\t\t\t";
        }
        // line 272
        echo "\t\t\t</ul>
\t\t</div>
\t</div>

\t<div style=\"display:none\" id=\"auto-sign-in-overlay\" class=\"dp-overlay-outer\">
\t\t<div class=\"dp-overlay dp-with-title\">
\t\t\t<div class=\"dp-title\"><h3>";
        // line 278
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.general.signing_in");
        echo "</h3></div>
\t\t\t<div class=\"dp-content\">
\t\t\t\t<div class=\"dp-loading-msg\">";
        // line 280
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.general.signing_in_please_wait");
        echo "</div>
\t\t\t</div>
\t\t</div>
\t</div>
</div>

";
        // line 286
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "admin_portal_controls")) {
            // line 287
            echo "\t<div id=\"dp_custom_footer_wrap\" class=\"dp-portal-block\" ";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ((!$this->getAttribute($this->getAttribute($_app_, "custom_templates"), "UserBundle::custom-footer.html.twig", array(), "array"))) {
                echo "style=\"display: none\"";
            }
            echo ">
\t\t<div id=\"dp_custom_footer\" class=\"dp-portal-block-content\">
\t\t\t";
            // line 289
            $this->env->loadTemplate("UserBundle::custom-footer.html.twig")->display($context);
            // line 290
            echo "\t\t</div>
\t</div>
\t<div id=\"dp_custom_footer_placeholder\" class=\"dp-portal-placeholder dp-portal-placeholder-footer\" data-portal-block=\"footer\" data-portal-for=\"#dp_custom_footer\" ";
            // line 292
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($this->getAttribute($_app_, "custom_templates"), "UserBundle::custom-footer.html.twig", array(), "array")) {
                echo "style=\"display: none\"";
            }
            echo ">
\t\t<em>";
            // line 293
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.designer.edit_footer_section");
            echo "</em>
\t</div>
";
        } else {
            // line 296
            echo "\t";
            $this->env->loadTemplate("UserBundle::custom-footer.html.twig")->display($context);
        }
        // line 298
        echo "
";
        // line 299
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "admin_portal_controls")) {
            // line 300
            echo "\t<script type=\"text/javascript\" src=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("vendor/less/less.min.js"), "html", null, true);
            echo "\"></script>
";
        }
        // line 302
        echo "
";
        // line 303
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (((((!$this->getAttribute($_app_, "getSetting", array(0 => "user.disable_chat_element"), "method")) && (!$this->getAttribute($_app_, "admin_portal_controls"))) && $this->getAttribute($this->getAttribute($_app_, "user"), "hasPerm", array(0 => "chat.use"), "method")) && (!$this->getAttribute($this->getAttribute($this->getAttribute($_app_, "getRequest", array(), "method"), "query"), "has", array(0 => "nochat"), "method")))) {
            // line 304
            echo "\t<!--DP_OFFLINE_CACHE_REMOVE_START-->
\t<!-- DeskPRO Chat Loader -->
\t<script type=\"text/javascript\">
\tif (!DpChatWidget_Options) {
\t\tvar DpChatWidget_Options = DpChatWidget_Options || {};
\t\tDpChatWidget_Options.protocol        = 'https:' == document.location.protocol ? 'https' : 'http';
\t\tDpChatWidget_Options.deskproUrl      = DpChatWidget_Options.protocol + '://";
            // line 310
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->urlTrimScheme($this->env->getExtension('routing')->getUrl("user")), "html", null, true);
            echo "';
\t\t";
            // line 311
            if ($this->env->getExtension('deskpro_templating')->getConstant("DP_DEBUG")) {
                echo "var DpChatWidget_EnableDebug = true;";
            }
            // line 312
            echo "
\t\tDpChatWidget_Options.startPhrase = ";
            // line 313
            echo twig_jsonencode_filter($this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.window_start-button"));
            echo ";
\t\tDpChatWidget_Options.resumePhrase = ";
            // line 314
            echo twig_jsonencode_filter($this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.window_resume-button"));
            echo ";
\t\tDpChatWidget_Options.offlinePhrase = ";
            // line 315
            echo twig_jsonencode_filter($this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.window_offline-button"));
            echo ";
\t\tDpChatWidget_Options.openInWindowPhrase = ";
            // line 316
            echo twig_jsonencode_filter($this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.window_open-new"));
            echo ";

\t\t(function() {
\t\t\tvar scr = document.createElement('script');
\t\t\tscr.src = '//";
            // line 320
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->urlTrimScheme($this->env->getExtension('deskpro_templating')->assetFull("javascripts/DeskPRO/User/ChatWidget/ChatWidget.js")), "html", null, true);
            echo "';
\t\t\tscr.setAttribute('async', 'true');
\t\t\tdocument.documentElement.firstChild.appendChild(scr);
\t\t})();
\t}
\t</script>
\t<!-- /DeskPRO Chat Loader -->
\t<!--DP_OFFLINE_CACHE_REMOVE_END-->
";
        } else {
            // line 329
            echo "\t<script type=\"text/javascript\" src=\"";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, strtr($this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), array("/index.php" => "")), "html", null, true);
            echo "/dp.php/vis.js\"></script>
";
        }
        // line 331
        echo "
";
        // line 332
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.ga_property_id"), "method")) {
            // line 333
            echo "\t<script type=\"text/javascript\">
\tvar _gaq = _gaq || [];
\t_gaq.push(['_setAccount', '";
            // line 335
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "core.ga_property_id"), "method"), "html", null, true);
            echo "']);
\t_gaq.push(['_trackPageview']);

\t(function() {
\t\tvar ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
\t\tga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
\t\tvar s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
\t})();
\t</script>
";
        }
        // line 345
        echo "
";
        // line 346
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($this->getAttribute($_app_, "getSetting", array(0 => "core.show_share_widget"), "method") && (!$this->getAttribute($_app_, "admin_portal_controls")))) {
            // line 347
            echo "\t";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($this->getAttribute($_app_, "getRequest", array(), "method"), "isSecure", array(), "method")) {
                // line 348
                echo "\t\t<script type=\"text/javascript\" src=\"https://ws.sharethis.com/button/buttons.js\" id=\"share-this-js\"></script>
\t";
            } else {
                // line 350
                echo "\t\t<script type=\"text/javascript\" src=\"http://w.sharethis.com/button/buttons.js\" id=\"share-this-js\"></script>
\t";
            }
            // line 352
            echo "\t<script type=\"text/javascript\">stLight.options({publisher: \"ur-b3c949e0-ec53-eea-51a4-2f4f16925438\"}); </script>
";
        }
        // line 354
        echo "
";
        // line 355
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($this->getAttribute($_app_, "user"), "id"))) {
            // line 356
            echo "\t";
            echo $this->env->getExtension('deskpro_templating')->getJsSsoLoader();
            echo "
";
        } else {
            // line 358
            echo "\t<!-- SHARE -->
\t";
            // line 359
            echo $this->env->getExtension('deskpro_templating')->getJsSsoShare();
            echo "
";
        }
        // line 361
        echo "<script type=\"text/javascript\">
(function() {
\tif (window.self !== window.top) {
\t\treturn;
\t}
\tvar els = document.getElementsByClassName('dp_website_url');
\tfor (var i = 0; i < els.length; i++) {
\t\tels[i].value = 'DP_UNSET';
\t}
})();
</script>
</body>
</html>
";
    }

    // line 26
    public function block_page_title($context, array $blocks = array())
    {
    }

    // line 43
    public function block_head($context, array $blocks = array())
    {
    }

    // line 51
    public function block_breadcrumb($context, array $blocks = array())
    {
    }

    // line 54
    public function block_sidebar($context, array $blocks = array())
    {
        // line 55
        echo "\t\t";
        echo $this->env->getExtension('deskpro_user_templating')->portalSection("sidebar");
        echo "
\t\t";
        // line 56
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "admin_portal_controls")) {
            // line 57
            echo "\t\t\t<div id=\"dp_custom_sidebar_add_simple\">
\t\t\t\t<a>Add sidebar block</a>
\t\t\t</div>
\t\t\t<div id=\"dp_custom_sidebar_add\">
\t\t\t\t<a>Add sidebar HTML</a>
\t\t\t</div>
\t\t";
        }
        // line 64
        echo "\t";
    }

    // line 249
    public function block_content($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "UserBundle::layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1133 => 64,  1124 => 57,  1121 => 56,  1116 => 55,  1113 => 54,  1108 => 51,  1103 => 43,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 346,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 329,  996 => 320,  989 => 316,  985 => 315,  981 => 314,  977 => 313,  970 => 311,  966 => 310,  955 => 303,  952 => 302,  943 => 299,  936 => 296,  930 => 293,  919 => 290,  917 => 289,  908 => 287,  905 => 286,  896 => 280,  891 => 278,  877 => 270,  862 => 267,  857 => 265,  837 => 253,  832 => 250,  827 => 248,  821 => 247,  803 => 243,  778 => 235,  769 => 233,  765 => 232,  753 => 229,  746 => 225,  743 => 224,  735 => 220,  730 => 218,  720 => 213,  717 => 212,  712 => 209,  691 => 204,  678 => 202,  654 => 196,  587 => 184,  576 => 179,  539 => 171,  517 => 169,  471 => 155,  441 => 143,  437 => 142,  418 => 133,  386 => 125,  373 => 120,  304 => 106,  270 => 98,  265 => 96,  229 => 81,  477 => 135,  455 => 150,  448 => 112,  429 => 138,  407 => 95,  399 => 93,  389 => 126,  375 => 85,  358 => 116,  349 => 72,  335 => 68,  327 => 64,  298 => 58,  280 => 56,  249 => 46,  194 => 33,  142 => 24,  344 => 117,  318 => 110,  306 => 107,  295 => 57,  357 => 141,  300 => 130,  286 => 101,  276 => 122,  269 => 53,  254 => 112,  128 => 35,  237 => 44,  165 => 51,  122 => 33,  798 => 242,  770 => 113,  759 => 112,  748 => 226,  731 => 108,  721 => 107,  718 => 106,  708 => 104,  696 => 102,  617 => 188,  590 => 91,  553 => 87,  550 => 176,  540 => 84,  533 => 82,  500 => 74,  493 => 72,  489 => 71,  482 => 69,  467 => 67,  464 => 152,  458 => 64,  452 => 62,  449 => 61,  415 => 55,  382 => 124,  372 => 84,  361 => 82,  356 => 48,  339 => 46,  302 => 42,  285 => 40,  258 => 37,  123 => 68,  108 => 63,  424 => 135,  394 => 86,  380 => 80,  338 => 113,  319 => 66,  316 => 65,  312 => 110,  290 => 102,  267 => 57,  206 => 43,  110 => 25,  240 => 82,  224 => 78,  219 => 51,  217 => 73,  202 => 44,  186 => 57,  170 => 82,  100 => 44,  67 => 32,  14 => 1,  1096 => 291,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 332,  1013 => 275,  1007 => 274,  1002 => 272,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 300,  940 => 298,  937 => 248,  928 => 244,  926 => 243,  915 => 237,  912 => 236,  903 => 231,  898 => 230,  892 => 229,  889 => 228,  887 => 227,  884 => 226,  876 => 222,  874 => 269,  871 => 220,  863 => 214,  861 => 213,  858 => 212,  850 => 263,  843 => 206,  840 => 205,  815 => 245,  812 => 244,  808 => 199,  804 => 120,  799 => 196,  791 => 190,  785 => 238,  775 => 184,  771 => 183,  754 => 176,  728 => 170,  726 => 169,  723 => 168,  715 => 105,  711 => 163,  709 => 162,  706 => 161,  698 => 208,  694 => 156,  692 => 155,  689 => 154,  681 => 150,  677 => 149,  675 => 148,  663 => 143,  661 => 200,  650 => 195,  646 => 136,  629 => 129,  627 => 128,  625 => 127,  622 => 126,  598 => 119,  592 => 117,  586 => 115,  575 => 108,  566 => 103,  556 => 100,  554 => 177,  541 => 92,  536 => 91,  515 => 86,  511 => 85,  509 => 84,  488 => 164,  486 => 78,  483 => 77,  465 => 73,  463 => 72,  450 => 65,  432 => 60,  419 => 105,  371 => 46,  362 => 43,  353 => 73,  337 => 37,  333 => 35,  309 => 109,  303 => 31,  299 => 30,  291 => 28,  272 => 54,  261 => 95,  253 => 47,  239 => 86,  235 => 84,  213 => 36,  200 => 50,  198 => 71,  159 => 78,  149 => 187,  146 => 55,  131 => 55,  116 => 26,  79 => 21,  74 => 21,  71 => 19,  836 => 320,  817 => 319,  814 => 318,  811 => 317,  805 => 313,  787 => 115,  779 => 306,  776 => 305,  773 => 301,  761 => 296,  751 => 175,  747 => 293,  742 => 292,  739 => 291,  736 => 287,  724 => 214,  705 => 278,  702 => 277,  688 => 265,  680 => 263,  667 => 261,  662 => 260,  656 => 259,  649 => 258,  644 => 97,  641 => 194,  624 => 192,  613 => 187,  607 => 93,  597 => 225,  591 => 185,  584 => 218,  579 => 216,  563 => 88,  559 => 208,  551 => 98,  547 => 205,  537 => 201,  524 => 191,  512 => 185,  507 => 76,  504 => 182,  498 => 178,  485 => 176,  480 => 160,  472 => 171,  466 => 153,  460 => 71,  447 => 163,  442 => 162,  434 => 110,  428 => 156,  422 => 106,  404 => 149,  368 => 136,  364 => 83,  340 => 131,  334 => 114,  330 => 129,  325 => 111,  292 => 116,  287 => 115,  282 => 124,  279 => 98,  273 => 107,  266 => 91,  256 => 94,  252 => 93,  228 => 32,  218 => 78,  201 => 91,  64 => 13,  51 => 5,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 793,  2042 => 790,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 774,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 745,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 707,  1829 => 706,  1827 => 705,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 689,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 676,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 666,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 638,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 617,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 611,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 602,  1559 => 601,  1552 => 598,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 584,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 573,  1477 => 567,  1473 => 566,  1467 => 562,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 520,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 511,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 481,  1257 => 480,  1254 => 479,  1250 => 478,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 460,  1196 => 458,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 452,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 445,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 425,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 421,  1099 => 420,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 412,  1076 => 359,  1070 => 407,  1057 => 352,  1052 => 404,  1045 => 347,  1040 => 397,  1036 => 283,  1032 => 395,  1028 => 394,  1023 => 393,  1020 => 392,  1016 => 391,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 381,  979 => 379,  974 => 312,  967 => 373,  962 => 371,  958 => 304,  954 => 253,  950 => 368,  945 => 367,  942 => 250,  938 => 365,  934 => 364,  927 => 361,  923 => 292,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 272,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 207,  841 => 321,  835 => 312,  830 => 249,  826 => 309,  822 => 308,  818 => 246,  813 => 306,  810 => 305,  806 => 304,  802 => 197,  795 => 118,  792 => 239,  789 => 298,  784 => 295,  782 => 187,  777 => 291,  772 => 289,  768 => 182,  763 => 287,  760 => 231,  756 => 230,  752 => 284,  745 => 281,  741 => 280,  738 => 279,  732 => 171,  719 => 273,  714 => 280,  710 => 279,  704 => 267,  699 => 103,  695 => 264,  690 => 263,  687 => 203,  683 => 261,  679 => 101,  672 => 147,  668 => 256,  665 => 201,  658 => 141,  645 => 248,  640 => 247,  634 => 96,  628 => 193,  623 => 238,  619 => 237,  611 => 235,  606 => 234,  603 => 120,  599 => 232,  595 => 231,  583 => 114,  580 => 180,  573 => 221,  560 => 101,  543 => 172,  538 => 209,  534 => 208,  530 => 170,  526 => 89,  521 => 80,  518 => 204,  514 => 78,  510 => 202,  503 => 75,  496 => 197,  490 => 193,  484 => 192,  474 => 190,  470 => 188,  446 => 144,  440 => 184,  436 => 61,  431 => 157,  425 => 178,  416 => 104,  412 => 98,  408 => 173,  403 => 172,  400 => 53,  396 => 92,  392 => 169,  385 => 166,  381 => 48,  367 => 117,  363 => 155,  359 => 154,  355 => 115,  350 => 121,  346 => 71,  343 => 70,  328 => 139,  324 => 138,  313 => 134,  307 => 132,  301 => 106,  288 => 27,  283 => 125,  271 => 94,  257 => 114,  251 => 13,  238 => 34,  233 => 72,  195 => 42,  191 => 69,  187 => 42,  183 => 87,  130 => 37,  88 => 18,  76 => 20,  115 => 21,  95 => 42,  655 => 233,  651 => 232,  648 => 231,  637 => 223,  633 => 130,  621 => 219,  618 => 218,  615 => 121,  604 => 186,  600 => 226,  588 => 116,  585 => 204,  582 => 203,  571 => 104,  567 => 178,  555 => 207,  552 => 190,  549 => 215,  544 => 186,  542 => 185,  535 => 181,  531 => 90,  519 => 189,  516 => 176,  513 => 168,  508 => 172,  506 => 83,  499 => 198,  495 => 167,  491 => 165,  481 => 162,  478 => 68,  475 => 157,  469 => 154,  456 => 154,  451 => 186,  443 => 60,  439 => 147,  427 => 89,  423 => 58,  420 => 176,  409 => 54,  405 => 54,  401 => 132,  391 => 129,  387 => 49,  384 => 139,  378 => 123,  365 => 78,  360 => 120,  348 => 136,  336 => 111,  332 => 140,  329 => 134,  323 => 133,  310 => 133,  305 => 132,  277 => 23,  274 => 91,  263 => 51,  259 => 49,  247 => 110,  244 => 53,  241 => 77,  222 => 80,  210 => 67,  207 => 96,  204 => 94,  184 => 31,  181 => 64,  167 => 212,  157 => 36,  96 => 26,  421 => 134,  417 => 152,  414 => 151,  406 => 130,  398 => 129,  393 => 91,  390 => 143,  376 => 79,  369 => 137,  366 => 136,  352 => 115,  345 => 114,  342 => 72,  331 => 66,  326 => 128,  320 => 137,  317 => 61,  314 => 33,  311 => 122,  308 => 60,  297 => 101,  293 => 128,  281 => 93,  278 => 110,  275 => 99,  264 => 117,  260 => 115,  248 => 91,  245 => 90,  242 => 74,  231 => 87,  227 => 42,  215 => 83,  212 => 82,  209 => 81,  197 => 34,  177 => 84,  171 => 69,  161 => 64,  132 => 36,  121 => 48,  105 => 24,  99 => 34,  81 => 26,  77 => 36,  180 => 66,  176 => 54,  156 => 28,  143 => 30,  139 => 175,  118 => 25,  189 => 70,  185 => 67,  173 => 35,  166 => 68,  152 => 27,  174 => 53,  164 => 65,  154 => 35,  150 => 42,  137 => 33,  133 => 31,  127 => 29,  107 => 30,  102 => 28,  83 => 23,  78 => 20,  53 => 14,  23 => 3,  42 => 12,  138 => 57,  134 => 56,  109 => 25,  103 => 44,  97 => 18,  94 => 42,  84 => 38,  75 => 16,  69 => 15,  66 => 18,  54 => 10,  44 => 11,  230 => 72,  226 => 68,  203 => 73,  193 => 242,  188 => 68,  182 => 235,  178 => 30,  168 => 64,  163 => 79,  160 => 77,  155 => 44,  148 => 41,  145 => 40,  140 => 38,  136 => 37,  125 => 34,  120 => 51,  113 => 17,  101 => 22,  92 => 20,  89 => 17,  85 => 13,  73 => 13,  62 => 30,  59 => 16,  56 => 11,  41 => 4,  126 => 29,  119 => 32,  111 => 31,  106 => 30,  98 => 43,  93 => 26,  86 => 24,  70 => 34,  60 => 14,  28 => 4,  36 => 4,  114 => 49,  104 => 45,  91 => 17,  80 => 4,  63 => 31,  58 => 9,  40 => 11,  34 => 7,  45 => 14,  61 => 15,  55 => 15,  48 => 12,  39 => 10,  35 => 7,  31 => 6,  26 => 5,  21 => 4,  46 => 7,  29 => 5,  57 => 13,  50 => 11,  47 => 12,  38 => 8,  33 => 3,  49 => 8,  32 => 7,  246 => 45,  236 => 59,  232 => 43,  225 => 3,  221 => 40,  216 => 77,  214 => 98,  211 => 272,  208 => 75,  205 => 66,  199 => 65,  196 => 72,  190 => 58,  179 => 66,  175 => 61,  172 => 60,  169 => 52,  162 => 48,  158 => 45,  153 => 43,  151 => 56,  147 => 73,  144 => 42,  141 => 58,  135 => 51,  129 => 22,  124 => 52,  117 => 50,  112 => 20,  90 => 41,  87 => 16,  82 => 12,  72 => 14,  68 => 27,  65 => 9,  52 => 14,  43 => 12,  37 => 10,  30 => 5,  27 => 5,  25 => 5,  24 => 4,  22 => 2,  19 => 1,);
    }
}
