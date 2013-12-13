<?php

/* UserBundle:Chat:chat.html.twig */
class __TwigTemplate_e65f235f392c2d03e2ee228bc34aaead extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 4
        $context["formmacro"] = $this->env->loadTemplate("UserBundle:Common:macros-form.html.twig");
        // line 5
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html class=\"";
        // line 6
        if ($this->env->getExtension('deskpro_templating')->isRtl()) {
            echo "rtl";
        }
        echo "\" ";
        echo $this->env->getExtension('deskpro_templating')->getLanguageHtmlAttributes();
        echo ">
<head>
\t<script type=\"text/javascript\">
\t\tvar BASE_URL = '";
        // line 9
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user"), "html", null, true);
        echo "';
\t\tvar BASE_PATH = '";
        // line 10
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, strtr($this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), array("/index.php" => "")), "html", null, true);
        echo "/';
\t\tvar ASSETS_BASE_URL = '";
        // line 11
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl(""), "html", null, true);
        echo "';
\t\tvar DESKPRO_PERSON_ID = ";
        // line 12
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_app_, "user", array(), "any", false, true), "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_app_, "user", array(), "any", false, true), "id"), 0)) : (0)), "html", null, true);
        echo ";
\t\tvar DESKPRO_SESSION_ID = '";
        // line 13
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "session"), "getId", array(), "method"), "html", null, true);
        echo "';
\t\tvar DP_REQUEST_TOKEN = '";
        // line 14
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->securityToken("request_token", 10800), "html", null, true);
        echo "';

\t\t";
        // line 16
        if ($this->env->getExtension('deskpro_templating')->getConstant("DP_DEBUG")) {
            // line 17
            echo "\t\t\tDP_DEBUG = true;
\t\t";
        } else {
            // line 19
            echo "\t\t\tDP_DEBUG = false;
\t\t";
        }
        // line 21
        echo "\t</script>

\t";
        // line 23
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "isDebug", array(), "method")) {
            // line 24
            echo "\t\t";
            echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("user_vendors");
            echo "
\t\t";
            // line 25
            echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("user_common");
            echo "
\t\t<link id=\"dp_stylesheet\" rel=\"stylesheet\" type=\"text/css\" href=\"";
            // line 26
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBasePath", array(), "method"), "html", null, true);
            echo "/file.php/res-user/main.css?";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($this->getAttribute($_app_, "style"), "css_updated"), "U"), "html", null, true);
            echo "&amp;reload=1";
            if ($this->env->getExtension('deskpro_templating')->isRtl()) {
                echo "&amp;rtl=1";
            }
            echo "\" media=\"screen,print\" />
\t";
        } else {
            // line 28
            echo "\t\t";
            echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("user");
            echo "
\t\t";
            // line 29
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if (((($this->env->getExtension('deskpro_templating')->isRtl() && (!$this->getAttribute($this->getAttribute($_app_, "style"), "css_blob_rtl"))) || ((!$this->env->getExtension('deskpro_templating')->isRtl()) && (!$this->getAttribute($this->getAttribute($_app_, "style"), "css_blob")))) || $this->getAttribute($_app_, "admin_portal_controls"))) {
                // line 30
                echo "\t\t\t<link id=\"dp_stylesheet\" rel=\"stylesheet\" type=\"text/css\" href=\"";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBasePath", array(), "method"), "html", null, true);
                echo "/file.php/res-user/main.css?";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($this->getAttribute($_app_, "style"), "css_updated"), "U"), "html", null, true);
                echo "&amp;reload=1";
                if ($this->env->getExtension('deskpro_templating')->isRtl()) {
                    echo "&amp;rtl=1";
                }
                echo "\" media=\"screen,print\" />
\t\t";
            } else {
                // line 32
                echo "\t\t\t";
                if ($this->env->getExtension('deskpro_templating')->isRtl()) {
                    // line 33
                    echo "\t\t\t\t<link id=\"dp_stylesheet\" rel=\"stylesheet\" type=\"text/css\" href=\"";
                    if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_app_, "style"), "css_blob_rtl"), "getDownloadUrl", array(), "method"), "html", null, true);
                    echo "?";
                    if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_app_, "style"), "css_blob_rtl"), "id"), "html", null, true);
                    echo "\" media=\"screen,print\" />
\t\t\t";
                } else {
                    // line 35
                    echo "\t\t\t\t<link id=\"dp_stylesheet\" rel=\"stylesheet\" type=\"text/css\" href=\"";
                    if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_app_, "style"), "css_blob"), "getDownloadUrl", array(), "method"), "html", null, true);
                    echo "?";
                    if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_app_, "style"), "css_blob"), "id"), "html", null, true);
                    echo "\" media=\"screen,print\" />
\t\t\t";
                }
                // line 37
                echo "\t\t";
            }
            // line 38
            echo "\t";
        }
        // line 39
        echo "
\t";
        // line 40
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "isDebug", array(), "method")) {
            // line 41
            echo "\t\t<script src=\"";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, strtr($this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), array("/index.php" => "")), "html", null, true);
            echo "/dp.php/user-lang-";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "language"), "id"), "html", null, true);
            echo ".js?nocache=1&v=";
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->securityToken("lang"), "html", null, true);
            echo "\"></script>
\t";
        } else {
            // line 43
            echo "\t\t<script src=\"";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, strtr($this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), array("/index.php" => "")), "html", null, true);
            echo "/dp.php/user-lang-";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "language"), "id"), "html", null, true);
            echo ".js?v=";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "core.deskpro_build"), "method"), "html", null, true);
            echo "\"></script>
\t";
        }
        // line 45
        echo "
\t<link rel=\"stylesheet\" type=\"text/css\" href=\"";
        // line 46
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("stylesheets/user/main-print.css"), "html", null, true);
        echo "\" media=\"print\" />
\t<!--[if IE ]><link href=\"";
        // line 47
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("stylesheets/user/main-ie.css"), "html", null, true);
        echo "\" rel=\"stylesheet\" type=\"text/css\"><![endif]-->

\t<script src=\"";
        // line 49
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("javascripts/DeskPRO/TouchCaller.js"), "html", null, true);
        echo "\" type=\"text/javascript\"></script>
\t<script src=\"";
        // line 50
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("javascripts/DeskPRO/WordHighlighter.js"), "html", null, true);
        echo "\" type=\"text/javascript\"></script>
\t<script src=\"";
        // line 51
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("javascripts/DeskPRO/User/ChatWidget/ChatWin.js"), "html", null, true);
        echo "\" type=\"text/javascript\"></script>

\t<script type=\"text/javascript\">
\t";
        // line 54
        if (isset($context["chat_display_js"])) { $_chat_display_js_ = $context["chat_display_js"]; } else { $_chat_display_js_ = null; }
        echo $_chat_display_js_;
        echo "
\t</script>

\t<script type=\"text/javascript\">
\t\twindow.DeskPRO_Window = new DeskPRO.User.Window();
\t\tvar ChatWin = new DeskPRO.User.WebsiteWidget.ChatWin({
\t\t\tparentUrl: ";
        // line 60
        if (isset($context["parent_url"])) { $_parent_url_ = $context["parent_url"]; } else { $_parent_url_ = null; }
        echo twig_jsonencode_filter($_parent_url_);
        echo ",
\t\t\tsessionCode: '";
        // line 61
        if (isset($context["session_code"])) { $_session_code_ = $context["session_code"]; } else { $_session_code_ = null; }
        echo twig_escape_filter($this->env, $_session_code_, "html", null, true);
        echo "',
\t\t\tconversationId: ";
        // line 62
        if (isset($context["convo"])) { $_convo_ = $context["convo"]; } else { $_convo_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_convo_, "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_convo_, "id"), 0)) : (0)), "html", null, true);
        echo ",
\t\t\tassignedAgentId: ";
        // line 63
        if (isset($context["convo"])) { $_convo_ = $context["convo"]; } else { $_convo_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_convo_, "agent", array(), "any", false, true), "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_convo_, "agent", array(), "any", false, true), "id"), 0)) : (0)), "html", null, true);
        echo ",
\t\t\tassignedAgentName: '";
        // line 64
        if (isset($context["convo"])) { $_convo_ = $context["convo"]; } else { $_convo_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_convo_, "agent", array(), "any", false, true), "display_name_user", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_convo_, "agent", array(), "any", false, true), "display_name_user"), 0)) : (0)), "html", null, true);
        echo "',
\t\t\tassignedAgentAvatar: '";
        // line 65
        if (isset($context["convo"])) { $_convo_ = $context["convo"]; } else { $_convo_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_convo_, "agent", array(), "any", false, true), "getPictureUrl", array(0 => 30), "method", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_convo_, "agent", array(), "any", false, true), "getPictureUrl", array(0 => 30), "method"), 0)) : (0)), "html", null, true);
        echo "',
\t\t\tassignedAgentAvatarIcon: '";
        // line 66
        if (isset($context["convo"])) { $_convo_ = $context["convo"]; } else { $_convo_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_convo_, "agent", array(), "any", false, true), "getPictureUrl", array(0 => 16), "method", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_convo_, "agent", array(), "any", false, true), "getPictureUrl", array(0 => 16), "method"), 0)) : (0)), "html", null, true);
        echo "',
\t\t\tinitialMessages: ";
        // line 67
        if (isset($context["convo_messages"])) { $_convo_messages_ = $context["convo_messages"]; } else { $_convo_messages_ = null; }
        echo twig_jsonencode_filter($_convo_messages_);
        echo ",
\t\t\tuploadTo: '";
        // line 68
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_accept_upload"), "html", null, true);
        echo "',
\t\t\tuploadSecurityToken: '";
        // line 69
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->securityToken("attach_temp"), "html", null, true);
        echo "',
\t\t\tautoStart: ";
        // line 70
        if (isset($context["auto_start"])) { $_auto_start_ = $context["auto_start"]; } else { $_auto_start_ = null; }
        if ($_auto_start_) {
            echo "true";
        } else {
            echo "false";
        }
        echo ",
\t\t\tisWindowMode: ";
        // line 71
        if (isset($context["is_window_mode"])) { $_is_window_mode_ = $context["is_window_mode"]; } else { $_is_window_mode_ = null; }
        if ($_is_window_mode_) {
            echo "true";
        } else {
            echo "false";
        }
        // line 72
        echo "\t\t});
\t\t\$(document).ready(function() {

\t\t\tif (\$(window).height() > \$('#dp_chat_start').find('article').height()) {

\t\t\t}

\t\t\twindow.DeskPRO_Window.initPage();
\t\t\tChatWin.initPage();

\t\t\t\$('.dp-copy a').on('click', function(ev) {
\t\t\t\tev.preventDefault();

\t\t\t\tvar href = \$(this).attr('href');
\t\t\t\twindow.open(href);
\t\t\t})
\t\t});
\t</script>
</head>
<body id=\"dp_chat\" ";
        // line 91
        if (isset($context["is_window_mode"])) { $_is_window_mode_ = $context["is_window_mode"]; } else { $_is_window_mode_ = null; }
        if ($_is_window_mode_) {
            echo "class=\"window-mode\"";
        }
        echo ">
<div id=\"dp\">
<div id=\"dp_chat_wrap\">

<div id=\"dp_chat_end_confirm\" style=\"display: none\">
\t<article>
\t\t<p>
\t\t\t";
        // line 98
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.window_cancel-confirm");
        echo "
\t\t</p>
\t\t<p>
\t\t\t<button class=\"dp-btn\" id=\"dp_chat_end_no\">";
        // line 101
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.window_cancel");
        echo "</button>
\t\t\t<button class=\"dp-btn\" id=\"dp_chat_end_yes\">";
        // line 102
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.window_end-chat");
        echo "</button>
\t\t</p>
\t</article>
</div>

<section class=\"dp-chat-active-state\" id=\"dp_chat_start\">
\t<header>
\t\t<h3>";
        // line 109
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.form_create_title");
        echo "</h3>
\t</header>
\t<article id=\"dp_chat_start_form\">
\t\t<div class=\"inner-wrapper\">
\t\t\t";
        // line 113
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "user"), "id")) {
            // line 114
            echo "\t\t\t\t<div class=\"dp-control-group\">
\t\t\t\t\t<div class=\"dp-control-label\">
\t\t\t\t\t\t<label>";
            // line 116
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.name");
            echo "</label>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"dp-controls\">
\t\t\t\t\t\t";
            // line 119
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ((!$this->getAttribute($this->getAttribute($_app_, "user"), "name"))) {
                // line 120
                echo "\t\t\t\t\t\t\t<input type=\"text\" name=\"name\" placeholder=\"";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.name");
                echo "\" value=\"";
                if (isset($context["initial_name"])) { $_initial_name_ = $context["initial_name"]; } else { $_initial_name_ = null; }
                echo twig_escape_filter($this->env, $_initial_name_, "html", null, true);
                echo "\" /><br />
\t\t\t\t\t\t";
            } else {
                // line 122
                echo "\t\t\t\t\t\t\t<strong style=\"font-size: 110%;\">";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "user"), "name"), "html", null, true);
                echo "</strong>
\t\t\t\t\t\t\t<input type=\"hidden\" name=\"name\" value=\"";
                // line 123
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "user"), "name"), "html", null, true);
                echo "\" /><br />
\t\t\t\t\t\t";
            }
            // line 125
            echo "\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t<div class=\"dp-control-group\">
\t\t\t\t\t<div class=\"dp-control-label\">
\t\t\t\t\t\t<label>";
            // line 129
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.email");
            echo "</label>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"dp-controls\">
\t\t\t\t\t\t";
            // line 132
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ((!$this->getAttribute($this->getAttribute($_app_, "user"), "email_address"))) {
                // line 133
                echo "\t\t\t\t\t\t\t<input type=\"text\" name=\"email\" placeholder=\"";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.email");
                echo "\" value=\"";
                if (isset($context["initial_email"])) { $_initial_email_ = $context["initial_email"]; } else { $_initial_email_ = null; }
                echo twig_escape_filter($this->env, $_initial_email_, "html", null, true);
                echo "\" />
\t\t\t\t\t\t";
            } else {
                // line 135
                echo "\t\t\t\t\t\t\t<strong style=\"font-size: 110%;\">";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "user"), "email_address"), "html", null, true);
                echo "</strong>
\t\t\t\t\t\t\t&nbsp; (<a href=\"";
                // line 136
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_logout", array("auth" => $this->env->getExtension('deskpro_templating')->staticSecurityToken("user_logout", 0), "via" => "user_chat")), "html", null, true);
                echo "\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.general.log_out");
                echo "</a>)
\t\t\t\t\t\t\t<input type=\"hidden\" name=\"email\" value=\"";
                // line 137
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "user"), "email_address"), "html", null, true);
                echo "\" />
\t\t\t\t\t\t";
            }
            // line 139
            echo "\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t";
        }
        // line 144
        echo "<div id=\"fields_container\">
\t\t\t\t";
        // line 146
        echo "\t\t\t\t";
        if (isset($context["default_page_data"])) { $_default_page_data_ = $context["default_page_data"]; } else { $_default_page_data_ = null; }
        if (isset($context["page_data_field_ids"])) { $_page_data_field_ids_ = $context["page_data_field_ids"]; } else { $_page_data_field_ids_ = null; }
        $this->env->loadTemplate("UserBundle:Chat:field-list-loop.html.twig")->display(array_merge($context, array("page_data" => $_default_page_data_, "mode" => "default", "page_data_field_ids" => $_page_data_field_ids_)));
        // line 147
        echo "\t\t\t</div>";
        // line 150
        echo "<div style=\"vertical-align: middle\">
\t\t\t\t<button class=\"dp-btn dp-btn-success\" id=\"dp_chat_start_go\">";
        // line 151
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.form_create_button-submit");
        echo "</button>
\t\t\t</div>
\t\t</div>

\t\t<footer id=\"dp_chat_start_footer\" class=\"dp-chat-footer dp-relative\">
\t\t\t";
        // line 156
        echo DeskPRO\Kernel\License::staticGetUserCopyrightHtml();
        echo "
\t\t</footer>
\t</article>
</section>

<section class=\"dp-chat-active-state\" id=\"dp_chat_finding_agent\" style=\"display: none\">
\t<header>
\t\t<div class=\"dp_chatwin_close\"><i class=\"dp-icon-remove-sign\"></i> ";
        // line 163
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.window_end-chat");
        echo "</div>

\t\t<h3>";
        // line 165
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.message_finding-agent");
        echo "</h3>
\t</header>
\t<article>
\t\t<p>";
        // line 168
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.message_wait");
        echo "</p>
\t\t<div id=\"dp_chat_finding_agent_loading\">
\t\t\t<div class=\"dp-progress dp-progress-striped dp-active\">
\t\t\t\t<div class=\"dp-bar\" style=\"width: 1%;\"></div>
\t\t\t</div>
\t\t</div>
\t\t<div id=\"dp_chat_finding_agent_more\" style=\"display: none\">
\t\t\t";
        // line 175
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.message_long-wait");
        echo "
\t\t</div>
\t</article>
</section>

<section class=\"dp-chat-active-state\" id=\"dp_chat_active\" style=\"display: none\">
\t<div id=\"dp_chat_filedrag\">";
        // line 181
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.window_upload-drag");
        echo "</div>
\t<header>
\t\t<div class=\"dp_chatwin_close\"><i class=\"dp-icon-remove-sign\"></i> ";
        // line 183
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.window_end-chat");
        echo "</div>

\t\t<div id=\"dpchat_with_agent\" style=\"display: none\"></div>
\t\t<h3 id=\"dpchat_without_agent\">";
        // line 186
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.message_finding-agent");
        echo "</h3>
\t</header>
\t<article>
\t\t<div id=\"dp_chat_messages_pane\">
\t\t\t<div id=\"uploading_list\" class=\"uploading-list\">
\t\t\t\t<ul class=\"files\"></ul>
\t\t\t</div>
\t\t</div>
\t\t<div id=\"dp_chat_input_pane\">
\t\t\t<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\"><tr>
\t\t\t\t<td width=\"100%\" style=\"padding: 8px\"><textarea id=\"dp_chat_message_input\"></textarea></td>
\t\t\t\t<td width=\"10\">&nbsp;</td>
\t\t\t\t<td nowrap=\"nowrap\" style=\"padding: 8px;\">
\t\t\t\t\t<button id=\"dp_chat_message_send\" class=\"dp-btn\">";
        // line 199
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.form_chat_button-submit");
        echo "</button>
\t\t\t\t</td>
\t\t\t</tr></table>
\t\t\t<div id=\"dp_chat_message_upload\">
\t\t\t\t<a id=\"dp_chat_upload_link\"><i class=\"dp-icon-file\"></i><span class=\"dp-link\">";
        // line 203
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.form_chat_send-file");
        echo "</span></a>
\t\t\t\t<input type=\"file\" name=\"attach\" id=\"dp_chat_message_upload_file\" />
\t\t\t</div>
\t\t</div>
\t</article>
</section>

<section class=\"dp-chat-active-state\" id=\"dp_chat_done\" style=\"display: none\">
\t<header>
\t\t<h3>";
        // line 212
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.form_feedback_title");
        echo "</h3>
\t</header>
\t<article>
\t\t";
        // line 216
        echo "\t\t<div class=\"dp-control-group form-row email-field\">
\t\t\t<div class=\"dp-control-label\">
\t\t\t\t<label>";
        // line 218
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.form_feedback_transcript-email");
        echo "</label>
\t\t\t</div>
\t\t\t<div class=\"dp-controls\">
\t\t\t\t<input type=\"text\" name=\"email\" value=\"\" placeholder=\"";
        // line 221
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.email");
        echo "\" />
\t\t\t</div>
\t\t</div>

\t\t<div class=\"dp-control-group\">
\t\t\t<div class=\"dp-controls\">
\t\t\t\t<input type=\"hidden\" id=\"no_feedback\" name=\"no_feedback\" value=\"0\" />
\t\t\t\t<table cellspacing=\"0\" cellpadding=\"0\" class=\"dp-layout\">
\t\t\t\t\t<tr>
\t\t\t\t\t\t<td style=\"padding-";
        // line 230
        if ($this->env->getExtension('deskpro_templating')->isRtl()) {
            echo "left";
        } else {
            echo "right";
        }
        echo ": 5px; padding-bottom: 4px;\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.form_feedback_rate-time");
        echo "</td>
\t\t\t\t\t\t<td style=\"padding-bottom: 4px;\">
\t\t\t\t\t\t\t<select name=\"rating_response_time\">
\t\t\t\t\t\t\t\t<option value=\"1\">";
        // line 233
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.form_feedback_rate-satisfied");
        echo "</option>
\t\t\t\t\t\t\t\t<option value=\"-1\">";
        // line 234
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.form_feedback_rate-unsatisfied");
        echo "</option>
\t\t\t\t\t\t\t</select>
\t\t\t\t\t\t</td>
\t\t\t\t\t</tr>
\t\t\t\t\t<tr>
\t\t\t\t\t\t<td style=\"padding-";
        // line 239
        if ($this->env->getExtension('deskpro_templating')->isRtl()) {
            echo "left";
        } else {
            echo "right";
        }
        echo ": 5px\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.form_feedback_rate-satisfaction");
        echo "</td>
\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t<select name=\"rating_overall\">
\t\t\t\t\t\t\t\t<option value=\"1\">";
        // line 242
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.form_feedback_rate-satisfied");
        echo "</option>
\t\t\t\t\t\t\t\t<option value=\"-1\">";
        // line 243
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.form_feedback_rate-unsatisfied");
        echo "</option>
\t\t\t\t\t\t\t</select>
\t\t\t\t\t\t</td>
\t\t\t\t\t</tr>
\t\t\t\t</table>
\t\t\t\t<textarea name=\"comments\" style=\"width: 95%; margin-top: 3px;\" placeholder=\"";
        // line 248
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.form_feedback_comments");
        echo "\"></textarea>
\t\t\t</div>
\t\t</div>

\t\t<div style=\"padding-bottom: 20px;\">
\t\t\t<div id=\"send_feedback_controls\">
\t\t\t\t<button class=\"dp-btn dp-btn-success\" id=\"dp_chat_end_real\">";
        // line 254
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.form_feedback_button-submit");
        echo "</button>
\t\t\t\t<span class=\"dp-link\" id=\"dp_chat_end_real_alt\">";
        // line 255
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.window_close");
        echo "</span>
\t\t\t</div>
\t\t\t<div id=\"send_feedback_loading\" style=\"display: none;\">
\t\t\t\t<i class=\"spinner-flat\"></i>
\t\t\t</div>
\t\t</div>
\t</article>
</section>

<section class=\"dp-chat-active-state\" id=\"dp_chat_done_unassigned\" style=\"display: none\">
\t<header>
\t\t<h3>";
        // line 266
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.submit-ticket-title");
        echo "</h3>
\t</header>
\t<article>
\t\t<p>
\t\t\t";
        // line 270
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.ended-no-agent");
        echo "
\t\t</p>

\t\t<div style=\"padding-bottom: 20px;\">
\t\t\t";
        // line 274
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "user.portal_enabled"), "method")) {
            // line 275
            echo "\t\t\t\t<button class=\"dp-btn dp-btn-success cancel-and-newticket-trigger\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.submit-ticket-button");
            echo "</button>
\t\t\t";
        }
        // line 277
        echo "\t\t\t<span class=\"dp-link\" id=\"dp_chat_end_real_unassigned\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.window_close");
        echo "</span>
\t\t</div>
\t</article>
</section>

<section class=\"dp-chat-active-state\" id=\"dp_chat_error\" style=\"display: none\">
\t<header>
\t\t<h3>";
        // line 284
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.error");
        echo "</h3>
\t</header>
\t<article>
\t\t<p id=\"dp_chat_error_text\">
\t\t\t";
        // line 288
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.profile.account_disabled_message");
        echo "
\t\t</p>

\t\t<div style=\"padding-bottom: 20px;\">
\t\t\t<span class=\"dp-link\" id=\"dp_chat_end_error\">";
        // line 292
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.window_close_only");
        echo "</span>
\t\t</div>
\t</article>
</section>

</div>
</div>

<script type=\"text/x-deskpro-plain\" id=\"dp_chat_tpl_agent_header\">
\t<div class=\"dp-chatting-with\">
\t\t<div class=\"avatar\"><img src=\"%avatar_url%\" /></div>
\t\t<h3 class=\"title\">";
        // line 303
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.message_chatting-with");
        echo "</h3>
\t\t<h3 class=\"name\">%name%</h3>
\t</div>
</script>
<script type=\"text/x-deskpro-plain\" id=\"dp_chat_tpl_user_message\">
\t<div class=\"row message user\">
\t\t<div class=\"time\"></div>
\t\t<div class=\"author\">";
        // line 310
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.general.you");
        echo "</div>
\t\t<div class=\"message\">%message%</div>
\t</div>
</script>
<script type=\"text/x-deskpro-plain\" id=\"dp_chat_tpl_agent_message\">
\t<div class=\"row message agent\">
\t\t<div class=\"time\"></div>
\t\t<div class=\"avatar\"><img src=\"%avatar_url_icon%\" /></div>
\t\t<div class=\"author\">%name%</div>
\t\t<div class=\"message\">%message%</div>
\t</div>
</script>
<script type=\"text/x-deskpro-plain\" id=\"dp_chat_tpl_sys_message\">
\t<div class=\"row message sys\">
\t\t<div class=\"author\">*</div>
\t\t<div class=\"message\">%message%</div>
\t</div>
</script>
<script type=\"text/x-deskpro-plain\" id=\"dp_chat_tpl_sys_message_prompt\">
\t<div class=\"row message sys-prompt\">
\t\t<div class=\"message\">%message%</div>
\t</div>
</script>

<script class=\"dptpl-attach-upload\" type=\"text/x-tmpl\">
\t";
        // line 335
        echo "{% for (var i=0, file; file=o.files[i]; i++) { %}";
        echo "
\t\t<li class=\"uploading\">";
        // line 336
        echo "{%=file.name%}";
        echo " (";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.message_uploading");
        echo ")</li>
\t";
        // line 337
        echo "{% } %}";
        echo "
</script>
<script class=\"dptpl-attach-download\" type=\"text/x-tmpl\">
\t";
        // line 340
        echo "{% for (var i=0, file; file=o.files[i]; i++) { %}";
        echo "
\t";
        // line 341
        echo "{% if (file.error) { %}";
        echo "
\t<li class=\"error\">
\t\t";
        // line 343
        echo "{%=file.error%}";
        echo "
\t</li>
\t";
        // line 345
        echo "{% } else { %}";
        echo "
\t<li>
\t\t<input type=\"hidden\" name=\"";
        // line 347
        if (isset($context["blob_field_name"])) { $_blob_field_name_ = $context["blob_field_name"]; } else { $_blob_field_name_ = null; }
        echo twig_escape_filter($this->env, ((array_key_exists("blob_field_name", $context)) ? (_twig_default_filter($_blob_field_name_, "attach_ids[]")) : ("attach_ids[]")), "html", null, true);
        echo "\" value=\"";
        echo "{%=file.blob_auth_id%}";
        echo "\" />
\t\t<label><a href=\"";
        // line 348
        echo "{%=file.download_url%}";
        echo "\" target=\"_blank\">";
        echo "{%=file.filename%}";
        echo "</a><span>";
        echo "{%=file.filesize_readable%}";
        echo "</span></label>
\t</li>
\t";
        // line 350
        echo "{% } %}";
        echo "
\t";
        // line 351
        echo "{% } %}";
        echo "
</script>

</body>
</html>
";
    }

    public function getTemplateName()
    {
        return "UserBundle:Chat:chat.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  793 => 351,  780 => 348,  758 => 341,  700 => 303,  686 => 292,  652 => 274,  638 => 266,  620 => 254,  545 => 218,  523 => 203,  494 => 183,  459 => 163,  438 => 150,  351 => 116,  347 => 114,  402 => 142,  268 => 67,  430 => 120,  411 => 136,  379 => 101,  322 => 94,  315 => 92,  289 => 84,  284 => 83,  255 => 65,  234 => 63,  1133 => 64,  1124 => 57,  1121 => 56,  1116 => 55,  1113 => 54,  1108 => 51,  1103 => 43,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 346,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 329,  996 => 320,  989 => 316,  985 => 315,  981 => 314,  977 => 313,  970 => 311,  966 => 310,  955 => 303,  952 => 302,  943 => 299,  936 => 296,  930 => 293,  919 => 290,  917 => 289,  908 => 287,  905 => 286,  896 => 280,  891 => 278,  877 => 270,  862 => 267,  857 => 265,  837 => 253,  832 => 250,  827 => 248,  821 => 247,  803 => 243,  778 => 235,  769 => 233,  765 => 232,  753 => 229,  746 => 225,  743 => 224,  735 => 220,  730 => 218,  720 => 213,  717 => 212,  712 => 209,  691 => 204,  678 => 202,  654 => 196,  587 => 239,  576 => 179,  539 => 171,  517 => 169,  471 => 155,  441 => 151,  437 => 142,  418 => 115,  386 => 125,  373 => 120,  304 => 91,  270 => 80,  265 => 77,  229 => 62,  477 => 135,  455 => 150,  448 => 164,  429 => 138,  407 => 95,  399 => 93,  389 => 126,  375 => 123,  358 => 116,  349 => 72,  335 => 68,  327 => 102,  298 => 58,  280 => 56,  249 => 66,  194 => 50,  142 => 24,  344 => 113,  318 => 96,  306 => 107,  295 => 57,  357 => 119,  300 => 130,  286 => 101,  276 => 71,  269 => 53,  254 => 67,  128 => 35,  237 => 44,  165 => 51,  122 => 33,  798 => 242,  770 => 113,  759 => 112,  748 => 337,  731 => 108,  721 => 107,  718 => 106,  708 => 104,  696 => 102,  617 => 188,  590 => 91,  553 => 87,  550 => 176,  540 => 84,  533 => 82,  500 => 186,  493 => 72,  489 => 181,  482 => 69,  467 => 67,  464 => 165,  458 => 64,  452 => 62,  449 => 156,  415 => 55,  382 => 124,  372 => 126,  361 => 82,  356 => 48,  339 => 97,  302 => 42,  285 => 40,  258 => 37,  123 => 32,  108 => 63,  424 => 135,  394 => 86,  380 => 80,  338 => 113,  319 => 66,  316 => 65,  312 => 110,  290 => 102,  267 => 70,  206 => 43,  110 => 25,  240 => 82,  224 => 61,  219 => 60,  217 => 73,  202 => 53,  186 => 46,  170 => 43,  100 => 20,  67 => 32,  14 => 1,  1096 => 291,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 332,  1013 => 275,  1007 => 274,  1002 => 272,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 300,  940 => 298,  937 => 248,  928 => 244,  926 => 243,  915 => 237,  912 => 236,  903 => 231,  898 => 230,  892 => 229,  889 => 228,  887 => 227,  884 => 226,  876 => 222,  874 => 269,  871 => 220,  863 => 214,  861 => 213,  858 => 212,  850 => 263,  843 => 206,  840 => 205,  815 => 245,  812 => 244,  808 => 199,  804 => 120,  799 => 196,  791 => 190,  785 => 238,  775 => 184,  771 => 183,  754 => 340,  728 => 170,  726 => 169,  723 => 168,  715 => 105,  711 => 163,  709 => 162,  706 => 161,  698 => 208,  694 => 156,  692 => 155,  689 => 154,  681 => 150,  677 => 149,  675 => 148,  663 => 143,  661 => 277,  650 => 195,  646 => 136,  629 => 129,  627 => 128,  625 => 127,  622 => 126,  598 => 119,  592 => 117,  586 => 115,  575 => 233,  566 => 103,  556 => 100,  554 => 177,  541 => 216,  536 => 91,  515 => 86,  511 => 85,  509 => 84,  488 => 164,  486 => 78,  483 => 77,  465 => 73,  463 => 72,  450 => 65,  432 => 60,  419 => 105,  371 => 46,  362 => 43,  353 => 73,  337 => 109,  333 => 105,  309 => 109,  303 => 88,  299 => 30,  291 => 82,  272 => 54,  261 => 95,  253 => 47,  239 => 64,  235 => 84,  213 => 60,  200 => 50,  198 => 52,  159 => 78,  149 => 38,  146 => 37,  131 => 55,  116 => 32,  79 => 24,  74 => 21,  71 => 19,  836 => 320,  817 => 319,  814 => 318,  811 => 317,  805 => 313,  787 => 115,  779 => 306,  776 => 305,  773 => 347,  761 => 296,  751 => 175,  747 => 293,  742 => 336,  739 => 291,  736 => 287,  724 => 214,  705 => 278,  702 => 277,  688 => 265,  680 => 263,  667 => 261,  662 => 260,  656 => 259,  649 => 258,  644 => 97,  641 => 194,  624 => 255,  613 => 187,  607 => 93,  597 => 225,  591 => 185,  584 => 218,  579 => 234,  563 => 230,  559 => 208,  551 => 221,  547 => 205,  537 => 201,  524 => 191,  512 => 185,  507 => 76,  504 => 182,  498 => 178,  485 => 176,  480 => 175,  472 => 171,  466 => 153,  460 => 71,  447 => 163,  442 => 162,  434 => 110,  428 => 144,  422 => 106,  404 => 149,  368 => 136,  364 => 83,  340 => 131,  334 => 114,  330 => 129,  325 => 100,  292 => 116,  287 => 115,  282 => 124,  279 => 82,  273 => 107,  266 => 91,  256 => 94,  252 => 93,  228 => 32,  218 => 78,  201 => 91,  64 => 17,  51 => 10,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 793,  2042 => 790,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 774,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 745,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 707,  1829 => 706,  1827 => 705,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 689,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 676,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 666,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 638,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 617,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 611,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 602,  1559 => 601,  1552 => 598,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 584,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 573,  1477 => 567,  1473 => 566,  1467 => 562,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 520,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 511,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 481,  1257 => 480,  1254 => 479,  1250 => 478,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 460,  1196 => 458,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 452,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 445,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 425,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 421,  1099 => 420,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 412,  1076 => 359,  1070 => 407,  1057 => 352,  1052 => 404,  1045 => 347,  1040 => 397,  1036 => 283,  1032 => 395,  1028 => 394,  1023 => 393,  1020 => 392,  1016 => 391,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 381,  979 => 379,  974 => 312,  967 => 373,  962 => 371,  958 => 304,  954 => 253,  950 => 368,  945 => 367,  942 => 250,  938 => 365,  934 => 364,  927 => 361,  923 => 292,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 272,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 207,  841 => 321,  835 => 312,  830 => 249,  826 => 309,  822 => 308,  818 => 246,  813 => 306,  810 => 305,  806 => 304,  802 => 197,  795 => 118,  792 => 239,  789 => 350,  784 => 295,  782 => 187,  777 => 291,  772 => 289,  768 => 345,  763 => 343,  760 => 231,  756 => 230,  752 => 284,  745 => 281,  741 => 280,  738 => 335,  732 => 171,  719 => 273,  714 => 280,  710 => 310,  704 => 267,  699 => 103,  695 => 264,  690 => 263,  687 => 203,  683 => 261,  679 => 288,  672 => 284,  668 => 256,  665 => 201,  658 => 141,  645 => 270,  640 => 247,  634 => 96,  628 => 193,  623 => 238,  619 => 237,  611 => 248,  606 => 234,  603 => 243,  599 => 242,  595 => 231,  583 => 114,  580 => 180,  573 => 221,  560 => 101,  543 => 172,  538 => 209,  534 => 208,  530 => 170,  526 => 89,  521 => 80,  518 => 204,  514 => 78,  510 => 202,  503 => 75,  496 => 197,  490 => 193,  484 => 192,  474 => 190,  470 => 168,  446 => 144,  440 => 184,  436 => 147,  431 => 146,  425 => 117,  416 => 104,  412 => 98,  408 => 112,  403 => 172,  400 => 111,  396 => 133,  392 => 169,  385 => 166,  381 => 125,  367 => 117,  363 => 155,  359 => 118,  355 => 115,  350 => 112,  346 => 71,  343 => 70,  328 => 139,  324 => 138,  313 => 93,  307 => 132,  301 => 85,  288 => 27,  283 => 72,  271 => 94,  257 => 114,  251 => 64,  238 => 34,  233 => 72,  195 => 49,  191 => 69,  187 => 47,  183 => 45,  130 => 28,  88 => 26,  76 => 23,  115 => 23,  95 => 42,  655 => 275,  651 => 232,  648 => 231,  637 => 223,  633 => 130,  621 => 219,  618 => 218,  615 => 121,  604 => 186,  600 => 226,  588 => 116,  585 => 204,  582 => 203,  571 => 104,  567 => 178,  555 => 207,  552 => 190,  549 => 215,  544 => 186,  542 => 185,  535 => 212,  531 => 90,  519 => 189,  516 => 199,  513 => 168,  508 => 172,  506 => 83,  499 => 198,  495 => 167,  491 => 165,  481 => 162,  478 => 68,  475 => 157,  469 => 154,  456 => 154,  451 => 186,  443 => 161,  439 => 147,  427 => 89,  423 => 139,  420 => 176,  409 => 54,  405 => 135,  401 => 132,  391 => 129,  387 => 129,  384 => 132,  378 => 123,  365 => 122,  360 => 120,  348 => 136,  336 => 111,  332 => 140,  329 => 134,  323 => 101,  310 => 92,  305 => 132,  277 => 23,  274 => 91,  263 => 69,  259 => 68,  247 => 110,  244 => 65,  241 => 62,  222 => 63,  210 => 59,  207 => 96,  204 => 94,  184 => 46,  181 => 45,  167 => 39,  157 => 36,  96 => 26,  421 => 144,  417 => 137,  414 => 151,  406 => 143,  398 => 129,  393 => 132,  390 => 135,  376 => 127,  369 => 122,  366 => 136,  352 => 115,  345 => 98,  342 => 109,  331 => 66,  326 => 96,  320 => 137,  317 => 98,  314 => 33,  311 => 90,  308 => 60,  297 => 101,  293 => 128,  281 => 93,  278 => 110,  275 => 68,  264 => 117,  260 => 76,  248 => 70,  245 => 90,  242 => 74,  231 => 60,  227 => 42,  215 => 83,  212 => 82,  209 => 54,  197 => 34,  177 => 43,  171 => 41,  161 => 36,  132 => 36,  121 => 48,  105 => 21,  99 => 34,  81 => 25,  77 => 20,  180 => 66,  176 => 54,  156 => 28,  143 => 30,  139 => 175,  118 => 25,  189 => 70,  185 => 67,  173 => 35,  166 => 68,  152 => 39,  174 => 42,  164 => 65,  154 => 35,  150 => 42,  137 => 33,  133 => 29,  127 => 27,  107 => 30,  102 => 28,  83 => 23,  78 => 20,  53 => 14,  23 => 6,  42 => 12,  138 => 30,  134 => 56,  109 => 30,  103 => 44,  97 => 19,  94 => 18,  84 => 25,  75 => 16,  69 => 15,  66 => 21,  54 => 10,  44 => 13,  230 => 72,  226 => 59,  203 => 51,  193 => 242,  188 => 68,  182 => 235,  178 => 30,  168 => 64,  163 => 79,  160 => 77,  155 => 40,  148 => 41,  145 => 40,  140 => 38,  136 => 35,  125 => 34,  120 => 51,  113 => 17,  101 => 28,  92 => 20,  89 => 17,  85 => 13,  73 => 13,  62 => 16,  59 => 13,  56 => 11,  41 => 4,  126 => 33,  119 => 32,  111 => 31,  106 => 29,  98 => 26,  93 => 26,  86 => 16,  70 => 34,  60 => 14,  28 => 4,  36 => 8,  114 => 49,  104 => 45,  91 => 17,  80 => 4,  63 => 15,  58 => 9,  40 => 12,  34 => 9,  45 => 14,  61 => 15,  55 => 15,  48 => 9,  39 => 10,  35 => 8,  31 => 6,  26 => 7,  21 => 5,  46 => 7,  29 => 8,  57 => 14,  50 => 11,  47 => 12,  38 => 10,  33 => 3,  49 => 8,  32 => 7,  246 => 63,  236 => 61,  232 => 43,  225 => 64,  221 => 58,  216 => 57,  214 => 98,  211 => 56,  208 => 75,  205 => 66,  199 => 50,  196 => 72,  190 => 47,  179 => 66,  175 => 61,  172 => 60,  169 => 52,  162 => 48,  158 => 41,  153 => 43,  151 => 56,  147 => 32,  144 => 42,  141 => 58,  135 => 51,  129 => 22,  124 => 26,  117 => 50,  112 => 20,  90 => 24,  87 => 16,  82 => 12,  72 => 21,  68 => 19,  65 => 9,  52 => 13,  43 => 11,  37 => 10,  30 => 7,  27 => 5,  25 => 5,  24 => 6,  22 => 5,  19 => 4,);
    }
}
