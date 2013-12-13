<?php

/* UserBundle:Register:register.html.twig */
class __TwigTemplate_7ac2402bdf686280c9ca3f26a136d41f extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("UserBundle::layout.html.twig");

        $this->blocks = array(
            'breadcrumb' => array($this, 'block_breadcrumb'),
            'head' => array($this, 'block_head'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "UserBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 2
        $context["formmacro"] = $this->env->loadTemplate("UserBundle:Common:macros-form.html.twig");
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_breadcrumb($context, array $blocks = array())
    {
        // line 4
        echo "\t";
        if (isset($context["this_page"])) { $_this_page_ = $context["this_page"]; } else { $_this_page_ = null; }
        if (($_this_page_ == "register")) {
            // line 5
            echo "\t\t<li><span class=\"dp-divider\">";
            echo $this->env->getExtension('deskpro_templating')->getLanguageArrow("right");
            echo "</span> <a href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_register"), "html", null, true);
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.profile.register");
            echo "</a></li>
\t";
        } else {
            // line 7
            echo "\t\t<li><span class=\"dp-divider\">";
            echo $this->env->getExtension('deskpro_templating')->getLanguageArrow("right");
            echo "</span> <a href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_register"), "html", null, true);
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.general.log_in");
            echo "</a></li>
\t";
        }
    }

    // line 10
    public function block_head($context, array $blocks = array())
    {
        // line 11
        echo "<script type=\"text/javascript\">
\t\$(document).ready(function() {
\t\t\$('#user_type_exist').on('click', function() {
\t\t\t\$('#user_type_new_form').hide();
\t\t\t\$('#user_type_exist_pass_form').hide();
\t\t\t\$('#user_type_exist_form').show();
\t\t});
\t\t\$('#user_type_new').on('click', function() {
\t\t\t\$('#user_type_exist_form').hide();
\t\t\t\$('#user_type_exist_pass_form').hide();
\t\t\t\$('#user_type_new_form').show();
\t\t});
\t\t\$('#user_type_exist_pass').on('click', function() {
\t\t\t\$('#user_type_exist_form').hide();
\t\t\t\$('#user_type_exist_pass_form').show();
\t\t\t\$('#user_type_new_form').hide();
\t\t});
\t});
</script>
";
    }

    // line 31
    public function block_content($context, array $blocks = array())
    {
        // line 32
        echo "
<div class=\"dp-register-page\">

\t";
        // line 35
        if (isset($context["from_ticket"])) { $_from_ticket_ = $context["from_ticket"]; } else { $_from_ticket_ = null; }
        if ($this->getAttribute($_from_ticket_, "id")) {
            // line 36
            echo "\t\t<div class=\"dp-alert dp-alert-success\">
\t\t\t<p>
\t\t\t\t";
            // line 38
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.profile.explain_register_for_online_tickets");
            echo "
\t\t\t<p>
\t\t</div>
\t";
        }
        // line 42
        echo "
\t";
        // line 43
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (twig_length_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "getSession", array(), "method"), "get", array(0 => "submitted_feedback"), "method"))) {
            // line 44
            echo "\t\t<div class=\"dp-alert dp-alert-success\">
\t\t\t<p>
\t\t\t\t";
            // line 46
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.profile.explain_login_feedback");
            echo "
\t\t\t<p>
\t\t</div>
\t";
        }
        // line 50
        echo "
\t";
        // line 51
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "getSession", array(), "method"), "get", array(0 => "login_validate_comments"), "method")) {
            // line 52
            echo "\t\t<div class=\"dp-alert dp-alert-error\">
\t\t\t";
            // line 53
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.profile.explain_login_comment");
            echo "
\t\t</div>
\t";
        }
        // line 56
        echo "
\t";
        // line 58
        echo "
\t<section class=\"dp-well\">
\t\t<header>
\t\t\t";
        // line 61
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "usersourceManager"), "getWithCapability", array(0 => "tpl_login_pull_btn"), "method")) {
            // line 62
            echo "\t\t\t\t<div class=\"dp-connect-with\">
\t\t\t\t\t";
            // line 63
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.profile.or_connect_with");
            echo "
\t\t\t\t\t";
            // line 64
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "usersourceManager"), "getWithCapability", array(0 => "tpl_login_pull_btn"), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["usersource"]) {
                // line 65
                echo "\t\t\t\t\t\t";
                if (isset($context["usersource"])) { $_usersource_ = $context["usersource"]; } else { $_usersource_ = null; }
                echo $this->env->getExtension('deskpro_templating')->renderUsersource($_usersource_, "login_pull_btn");
                echo "
\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['usersource'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 67
            echo "\t\t\t\t</div>
\t\t\t";
        }
        // line 69
        echo "\t\t\t<label>
\t\t\t\t<h3><input type=\"radio\" name=\"user_type\" id=\"user_type_exist\" ";
        // line 70
        if (isset($context["this_page"])) { $_this_page_ = $context["this_page"]; } else { $_this_page_ = null; }
        if (($_this_page_ == "login")) {
            echo "checked=\"checked\"";
        }
        echo " /> ";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.profile.im_already_registered");
        echo "</h3>
\t\t\t</label>
\t\t</header>
\t\t<article id=\"user_type_exist_form\" class=\"dp-well dp-well-light\" ";
        // line 73
        if (isset($context["this_page"])) { $_this_page_ = $context["this_page"]; } else { $_this_page_ = null; }
        if (($_this_page_ != "login")) {
            echo "style=\"display:none\"";
        }
        echo ">
\t\t\t";
        // line 74
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "session"), "hasFlash", array(0 => "password_reset"), "method")) {
            // line 75
            echo "\t\t\t\t<div class=\"dp-alert dp-alert-success\">
\t\t\t\t\t";
            // line 76
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.profile.password_updated");
            echo "
\t\t\t\t</div>
\t\t\t";
        }
        // line 79
        echo "\t\t\t";
        if (isset($context["failed_login_name"])) { $_failed_login_name_ = $context["failed_login_name"]; } else { $_failed_login_name_ = null; }
        if (isset($context["account_disabled"])) { $_account_disabled_ = $context["account_disabled"]; } else { $_account_disabled_ = null; }
        if ($_failed_login_name_) {
            // line 80
            echo "\t\t\t\t<div class=\"dp-alert dp-alert-error\">
\t\t\t\t\t";
            // line 81
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.profile.login_error");
            echo "
\t\t\t\t</div>
\t\t\t";
        } elseif ($_account_disabled_) {
            // line 84
            echo "\t\t\t\t<div class=\"dp-alert dp-alert-error\">
\t\t\t\t\t";
            // line 85
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.profile.account_disabled_message");
            echo "
\t\t\t\t</div>
\t\t\t";
        }
        // line 88
        echo "\t\t\t<form action=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_login_authenticate_local"), "html", null, true);
        echo "\" class=\"dp-layout\" method=\"post\">
\t\t\t\t";
        // line 89
        echo $this->env->getExtension('deskpro_templating')->formToken("user_login");
        echo "

\t\t\t\t";
        // line 91
        if (isset($context["return"])) { $_return_ = $context["return"]; } else { $_return_ = null; }
        if ($_return_) {
            // line 92
            echo "\t\t\t\t\t<input type=\"hidden\" name=\"return\" value=\"";
            if (isset($context["return"])) { $_return_ = $context["return"]; } else { $_return_ = null; }
            echo twig_escape_filter($this->env, $_return_, "html", null, true);
            echo "\" />
\t\t\t\t";
        } else {
            // line 94
            echo "\t\t\t\t\t<input type=\"hidden\" name=\"return\" value=\"";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getReturnUrl", array(), "method"), "html", null, true);
            echo "\" />
\t\t\t\t";
        }
        // line 96
        echo "\t\t\t\t<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">
\t\t\t\t\t<tr>
\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t<div style=\"padding-bottom: 5px;\">
\t\t\t\t\t\t\t\t";
        // line 100
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.general.email_address");
        echo "
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t<div>
\t\t\t\t\t\t\t\t<input type=\"text\" name=\"email\" size=\"20\" value=\"";
        // line 103
        if (isset($context["failed_login_name"])) { $_failed_login_name_ = $context["failed_login_name"]; } else { $_failed_login_name_ = null; }
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, ((array_key_exists("failed_login_name", $context)) ? (_twig_default_filter($_failed_login_name_, $this->getAttribute($_app_, "getVariable", array(0 => "login_with_email"), "method"))) : ($this->getAttribute($_app_, "getVariable", array(0 => "login_with_email"), "method"))), "html", null, true);
        echo "\" />
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</td>
\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t<div style=\"padding-bottom: 5px;\">
\t\t\t\t\t\t\t\t";
        // line 108
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.profile.password");
        echo "
\t\t\t\t\t\t\t\t";
        // line 109
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($this->getAttribute($_app_, "getSetting", array(0 => "core.user_mode"), "method") != "closed")) {
            echo "<a style=\"margin-";
            if ($this->env->getExtension('deskpro_templating')->isRtl()) {
                echo "right";
            } else {
                echo "left";
            }
            echo ": 9px;\" href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_login_resetpass"), "html", null, true);
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.profile.lost_qm");
            echo "</a>";
        }
        // line 110
        echo "\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t<div>
\t\t\t\t\t\t\t\t<input type=\"password\" value=\"\" name=\"password\" size=\"20\" />
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</td>
\t\t\t\t\t\t<td style=\"text-align: center; vertical-align: middle;\" valign=\"middle\">
\t\t\t\t\t\t\t<input class=\"dp-btn dp-btn-primary\" type=\"submit\" value=\"";
        // line 116
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.general.log_in");
        echo "\" />
\t\t\t\t\t\t\t<label style=\"margin: 5px 0 0\"><input type=\"checkbox\" name=\"remember_me\" value=\"1\" style=\"display:inline; position: relative; top: -2px;\" /> ";
        // line 117
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.general.remember_me");
        echo "</label>
\t\t\t\t\t\t</td>
\t\t\t\t\t</tr>
\t\t\t\t</table>
\t\t\t</form>
\t\t</article>
\t</section>

\t";
        // line 126
        echo "\t";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.deskpro_source_enabled"), "method")) {
            // line 127
            echo "\t\t<section class=\"dp-well\">
\t\t\t<header>
\t\t\t\t<label>
\t\t\t\t\t<h3><input type=\"radio\" name=\"user_type\" id=\"user_type_exist_pass\" ";
            // line 130
            if (isset($context["this_page"])) { $_this_page_ = $context["this_page"]; } else { $_this_page_ = null; }
            if (($_this_page_ == "reset")) {
                echo "checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.profile.ive_lost_password");
            echo "</h3>
\t\t\t\t</label>
\t\t\t</header>
\t\t\t<article id=\"user_type_exist_pass_form\" class=\"dp-well dp-well-light\" ";
            // line 133
            if (isset($context["this_page"])) { $_this_page_ = $context["this_page"]; } else { $_this_page_ = null; }
            if (($_this_page_ != "reset")) {
                echo "style=\"display:none\"";
            }
            echo ">
\t\t\t\t<form action=\"";
            // line 134
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_login_resetpass_send"), "html", null, true);
            echo "\" class=\"form login\" method=\"post\" class=\"with-form-validator\">
\t\t\t\t\t";
            // line 135
            echo $this->env->getExtension('deskpro_templating')->formToken();
            echo "
\t\t\t\t\t<table cellspacing=\"0\" cellpadding=\"0\">
\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t<td width=\"40%\">
\t\t\t\t\t\t\t\t<div class=\"dp-control-group ";
            // line 139
            if (isset($context["invalid"])) { $_invalid_ = $context["invalid"]; } else { $_invalid_ = null; }
            if ($_invalid_) {
                echo "dp-error";
            }
            echo "\">
\t\t\t\t\t\t\t\t\t<div style=\"padding-bottom: 5px;\">
\t\t\t\t\t\t\t\t\t\t";
            // line 141
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.general.email_address");
            echo "
\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t<div class=\"dp-controls\">
\t\t\t\t\t\t\t\t\t\t<input type=\"text\" value=\"";
            // line 144
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getVariable", array(0 => "login_with_email"), "method"), "html", null, true);
            echo "\" name=\"email\" id=\"email\" size=\"40\" data-field-validators=\"DeskPRO.Form.EmailValidator\" />
\t\t\t\t\t\t\t\t\t\t<div class=\"dp-help-inline dp-error-explain\">
\t\t\t\t\t\t\t\t\t\t\t";
            // line 146
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.general.form_fix_email");
            echo "
\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t<td style=\"text-align: center; vertical-align: middle;\" valign=\"middle\">
\t\t\t\t\t\t\t\t<input class=\"dp-btn dp-btn-primary\" type=\"submit\" value=\"";
            // line 152
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.profile.continue");
            echo "\" />
\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t</tr>
\t\t\t\t\t</table>
\t\t\t\t\t";
            // line 156
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($this->getAttribute($_app_, "user"), "id")) {
                // line 157
                echo "\t\t\t\t\t\t<p style=\"font-size: 90%\">";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.profile.reset_password_current_logged_in", array("email" => $this->getAttribute($this->getAttribute($_app_, "user"), "getEmailAddress", array(), "method")));
                echo "</p>
\t\t\t\t\t";
            }
            // line 159
            echo "\t\t\t\t</form>
\t\t\t</article>
\t\t</section>
\t";
        }
        // line 163
        echo "
\t";
        // line 165
        echo "\t";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($this->getAttribute($_app_, "getSetting", array(0 => "core.user_mode"), "method") != "closed")) {
            // line 166
            echo "\t\t<section class=\"dp-well\">
\t\t\t<header>
\t\t\t\t<label>
\t\t\t\t\t<h3><input type=\"radio\" name=\"user_type\" id=\"user_type_new\" ";
            // line 169
            if (isset($context["this_page"])) { $_this_page_ = $context["this_page"]; } else { $_this_page_ = null; }
            if (($_this_page_ == "register")) {
                echo "checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.profile.im_new_user");
            echo "</h3>
\t\t\t\t</label>
\t\t\t</header>
\t\t\t<article id=\"user_type_new_form\" class=\"dp-well dp-well-light\" ";
            // line 172
            if (isset($context["this_page"])) { $_this_page_ = $context["this_page"]; } else { $_this_page_ = null; }
            if (($_this_page_ != "register")) {
                echo "style=\"display:none\"";
            }
            echo ">
\t\t\t\t<form action=\"";
            // line 173
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_register"), "html", null, true);
            echo "\" method=\"post\">
\t\t\t\t\t";
            // line 174
            echo $this->env->getExtension('deskpro_templating')->formToken();
            echo "
\t\t\t\t\t<input type=\"hidden\" name=\"process\" value=\"1\" />

\t\t\t\t\t<div class=\"dp-control-group ";
            // line 177
            if (isset($context["error_fields"])) { $_error_fields_ = $context["error_fields"]; } else { $_error_fields_ = null; }
            if ($this->getAttribute($_error_fields_, "name", array(), "array")) {
                echo "dp-error";
            }
            echo "\">
\t\t\t\t\t\t<div class=\"dp-control-label\">
\t\t\t\t\t\t\t<label>";
            // line 179
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.general.your_name");
            echo " *</label>
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"dp-controls dp-fill\">
\t\t\t\t\t\t\t";
            // line 182
            if (isset($context["form"])) { $_form_ = $context["form"]; } else { $_form_ = null; }
            echo $this->env->getExtension('form')->renderWidget($this->getAttribute($_form_, "name"));
            echo "
\t\t\t\t\t\t\t<div class=\"dp-help-inline dp-error-explain\">
\t\t\t\t\t\t\t\t";
            // line 184
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.general.please_enter_your_name");
            echo "
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>

\t\t\t\t\t<div class=\"dp-control-group ";
            // line 189
            if (isset($context["error_fields"])) { $_error_fields_ = $context["error_fields"]; } else { $_error_fields_ = null; }
            if ($this->getAttribute($_error_fields_, "email", array(), "array")) {
                echo "dp-error";
            }
            echo "\">
\t\t\t\t\t\t<div class=\"dp-control-label\">
\t\t\t\t\t\t\t<label>";
            // line 191
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.general.your_email_address");
            echo " *</label>
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"dp-controls dp-fill\">
\t\t\t\t\t\t\t";
            // line 194
            if (isset($context["form"])) { $_form_ = $context["form"]; } else { $_form_ = null; }
            echo $this->env->getExtension('form')->renderWidget($this->getAttribute($_form_, "email"));
            echo "
\t\t\t\t\t\t\t";
            // line 195
            if (isset($context["errors"])) { $_errors_ = $context["errors"]; } else { $_errors_ = null; }
            if ($this->getAttribute($_errors_, "email.invalid", array(), "array")) {
                echo "<div class=\"dp-help-inline dp-error-explain\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.general.please_enter_your_email");
                echo "</div>";
            }
            // line 196
            echo "\t\t\t\t\t\t\t";
            if (isset($context["errors"])) { $_errors_ = $context["errors"]; } else { $_errors_ = null; }
            if ($this->getAttribute($_errors_, "email.in_use", array(), "array")) {
                echo "<div class=\"dp-help-inline dp-error-explain\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.profile.register_email_exists");
                echo "</div>";
            }
            // line 197
            echo "\t\t\t\t\t\t</div>
\t\t\t\t\t</div>

\t\t\t\t\t<div class=\"dp-control-group ";
            // line 200
            if (isset($context["error_fields"])) { $_error_fields_ = $context["error_fields"]; } else { $_error_fields_ = null; }
            if ($this->getAttribute($_error_fields_, "password", array(), "array")) {
                echo "dp-error";
            }
            echo "\">
\t\t\t\t\t\t<div class=\"dp-control-label\">
\t\t\t\t\t\t\t<label>";
            // line 202
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.profile.password");
            echo " *</label>
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"dp-controls dp-fill\">
\t\t\t\t\t\t\t";
            // line 205
            if (isset($context["form"])) { $_form_ = $context["form"]; } else { $_form_ = null; }
            echo $this->env->getExtension('form')->renderWidget($this->getAttribute($_form_, "password"));
            echo "
\t\t\t\t\t\t</div>
\t\t\t\t\t\t";
            // line 207
            if (isset($context["errors"])) { $_errors_ = $context["errors"]; } else { $_errors_ = null; }
            if ($this->getAttribute($_errors_, "password.short", array(), "array")) {
                echo "<div class=\"dp-help-inline dp-error-explain\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.profile.register_password_min_length");
                echo "</div>";
            }
            // line 208
            echo "\t\t\t\t\t\t";
            if (isset($context["errors"])) { $_errors_ = $context["errors"]; } else { $_errors_ = null; }
            if ($this->getAttribute($_errors_, "password.mismatch", array(), "array")) {
                echo "<div class=\"dp-help-inline dp-error-explain\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.profile.register_password_mismatch");
                echo "</div>";
            }
            // line 209
            echo "\t\t\t\t\t</div>

\t\t\t\t\t<div class=\"dp-control-group\">
\t\t\t\t\t\t<div class=\"dp-control-label\">
\t\t\t\t\t\t\t<label>";
            // line 213
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.profile.label_repeat_password");
            echo " *</label>
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"dp-controls dp-fill\">
\t\t\t\t\t\t\t";
            // line 216
            if (isset($context["form"])) { $_form_ = $context["form"]; } else { $_form_ = null; }
            echo $this->env->getExtension('form')->renderWidget($this->getAttribute($_form_, "password2"));
            echo "
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>

\t\t\t\t\t";
            // line 220
            if (isset($context["form"])) { $_form_ = $context["form"]; } else { $_form_ = null; }
            if ($this->getAttribute($_form_, "language_id")) {
                // line 221
                echo "\t\t\t\t\t<div class=\"dp-control-group\">
\t\t\t\t\t\t<div class=\"dp-control-label\">
\t\t\t\t\t\t\t<label>";
                // line 223
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.profile.language");
                echo "</label>
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"dp-controls dp-fill\">
\t\t\t\t\t\t\t";
                // line 226
                if (isset($context["form"])) { $_form_ = $context["form"]; } else { $_form_ = null; }
                echo $this->env->getExtension('form')->renderWidget($this->getAttribute($_form_, "language_id"));
                echo "
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t\t";
            }
            // line 230
            echo "
\t\t\t\t\t";
            // line 231
            if (isset($context["custom_fields"])) { $_custom_fields_ = $context["custom_fields"]; } else { $_custom_fields_ = null; }
            if ((!$_custom_fields_)) {
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                $context["custom_fields"] = $this->getAttribute($this->getAttribute($_app_, "getPersonFieldManager", array(), "method"), "getDisplayArray", array(), "method");
            }
            // line 232
            echo "\t\t\t\t\t";
            if (isset($context["custom_fields"])) { $_custom_fields_ = $context["custom_fields"]; } else { $_custom_fields_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_custom_fields_);
            foreach ($context['_seq'] as $context["_key"] => $context["f"]) {
                // line 233
                echo "\t\t\t\t\t\t";
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                if (($this->getAttribute($this->getAttribute($_f_, "field_def"), "getTypeName", array(), "method") == "hidden")) {
                    // line 234
                    echo "\t\t\t\t\t\t\t<div style=\"display: none;\">";
                    if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                    echo $this->env->getExtension('deskpro_templating')->renderCustomFieldForm($_f_);
                    echo "</div>
\t\t\t\t\t\t";
                } else {
                    // line 236
                    echo "\t\t\t\t\t\t\t<div class=\"dp-control-group ";
                    if (isset($context["error_fields"])) { $_error_fields_ = $context["error_fields"]; } else { $_error_fields_ = null; }
                    if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                    if ($this->getAttribute($_error_fields_, ("field_" . $this->getAttribute($this->getAttribute($_f_, "field_def"), "id")), array(), "array")) {
                        echo "dp-error";
                    }
                    echo "\">
\t\t\t\t\t\t\t\t<div class=\"dp-control-label\">
\t\t\t\t\t\t\t\t\t<label>";
                    // line 238
                    if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_f_, "field_def"), "title"), "html", null, true);
                    echo "</label>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t<div class=\"dp-controls dp-fill\">
\t\t\t\t\t\t\t\t\t";
                    // line 241
                    if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                    echo $this->env->getExtension('deskpro_templating')->renderCustomFieldForm($_f_);
                    echo "
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t";
                    // line 243
                    if (isset($context["error_fields"])) { $_error_fields_ = $context["error_fields"]; } else { $_error_fields_ = null; }
                    if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                    if ($this->getAttribute($_error_fields_, ("field_" . $this->getAttribute($this->getAttribute($_f_, "field_def"), "id")), array(), "array")) {
                        echo "<div class=\"dp-help-inline dp-error-explain\">";
                        if (isset($context["formmacro"])) { $_formmacro_ = $context["formmacro"]; } else { $_formmacro_ = null; }
                        if (isset($context["errors"])) { $_errors_ = $context["errors"]; } else { $_errors_ = null; }
                        if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                        echo $_formmacro_->getcustom_field_errors($_errors_, $this->getAttribute($_f_, "field_def"), ("field_" . $this->getAttribute($this->getAttribute($_f_, "field_def"), "id")));
                        echo "</div>";
                    }
                    // line 244
                    echo "\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t";
                }
                // line 246
                echo "\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['f'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 247
            echo "
\t\t\t\t\t";
            // line 248
            if (isset($context["captcha"])) { $_captcha_ = $context["captcha"]; } else { $_captcha_ = null; }
            if ($_captcha_) {
                // line 249
                echo "\t\t\t\t\t\t<div class=\"dp-control-group ";
                if (isset($context["error_fields"])) { $_error_fields_ = $context["error_fields"]; } else { $_error_fields_ = null; }
                if ($this->getAttribute($_error_fields_, "captcha", array(), "array")) {
                    echo "dp-error dp-error-static";
                }
                echo "\">
\t\t\t\t\t\t\t<div class=\"dp-control-label\">
\t\t\t\t\t\t\t\t<label>";
                // line 251
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.general.label_captcha");
                echo " *</label>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t<div class=\"dp-controls dp-fill\">
\t\t\t\t\t\t\t\t";
                // line 254
                if (isset($context["captcha"])) { $_captcha_ = $context["captcha"]; } else { $_captcha_ = null; }
                echo $this->getAttribute($_captcha_, "getHtml", array(), "method");
                echo "
\t\t\t\t\t\t\t\t";
                // line 255
                if (isset($context["error_fields"])) { $_error_fields_ = $context["error_fields"]; } else { $_error_fields_ = null; }
                if ($this->getAttribute($_error_fields_, "captcha", array(), "array")) {
                    // line 256
                    echo "\t\t\t\t\t\t\t\t\t<div class=\"dp-help-inline dp-error-explain\">
\t\t\t\t\t\t\t\t\t\t";
                    // line 257
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.tickets.form_error_captcha_invalid");
                    echo "
\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t";
                }
                // line 260
                echo "\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>
\t\t\t\t\t";
            }
            // line 263
            echo "
\t\t\t\t\t<input class=\"dp-btn dp-btn-primary\" type=\"submit\" value=\"";
            // line 264
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.tickets.submit");
            echo "\" />
\t\t\t\t</form>
\t\t\t</article>
\t\t</section>
\t";
        }
        // line 269
        echo "</div>

";
    }

    public function getTemplateName()
    {
        return "UserBundle:Register:register.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  722 => 269,  697 => 256,  674 => 249,  671 => 248,  577 => 220,  569 => 216,  557 => 209,  502 => 195,  497 => 194,  445 => 173,  729 => 286,  684 => 261,  676 => 256,  669 => 254,  660 => 250,  647 => 243,  643 => 244,  601 => 231,  570 => 211,  522 => 200,  501 => 179,  296 => 149,  374 => 137,  631 => 239,  616 => 19,  608 => 17,  605 => 16,  596 => 15,  574 => 13,  561 => 209,  527 => 147,  433 => 160,  388 => 142,  426 => 177,  383 => 146,  461 => 167,  370 => 113,  395 => 144,  294 => 76,  223 => 84,  220 => 54,  492 => 127,  468 => 121,  444 => 33,  410 => 105,  397 => 101,  377 => 144,  262 => 95,  250 => 129,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 2108,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 1383,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 1303,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 1173,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 1041,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 970,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 920,  1126 => 916,  1106 => 900,  1100 => 897,  1026 => 841,  997 => 822,  983 => 816,  975 => 812,  956 => 800,  939 => 786,  902 => 753,  894 => 749,  879 => 746,  757 => 631,  727 => 608,  716 => 605,  670 => 582,  528 => 447,  476 => 123,  435 => 31,  354 => 110,  341 => 97,  192 => 73,  321 => 163,  243 => 67,  793 => 351,  780 => 348,  758 => 341,  700 => 257,  686 => 292,  652 => 274,  638 => 266,  620 => 254,  545 => 218,  523 => 203,  494 => 183,  459 => 163,  438 => 172,  351 => 116,  347 => 109,  402 => 157,  268 => 97,  430 => 120,  411 => 120,  379 => 96,  322 => 92,  315 => 110,  289 => 101,  284 => 93,  255 => 92,  234 => 88,  1133 => 64,  1124 => 57,  1121 => 56,  1116 => 55,  1113 => 54,  1108 => 51,  1103 => 43,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 346,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 329,  996 => 320,  989 => 316,  985 => 315,  981 => 314,  977 => 313,  970 => 311,  966 => 310,  955 => 303,  952 => 302,  943 => 299,  936 => 296,  930 => 293,  919 => 290,  917 => 289,  908 => 287,  905 => 286,  896 => 280,  891 => 278,  877 => 270,  862 => 267,  857 => 265,  837 => 253,  832 => 250,  827 => 248,  821 => 247,  803 => 243,  778 => 235,  769 => 233,  765 => 232,  753 => 229,  746 => 225,  743 => 224,  735 => 613,  730 => 218,  720 => 213,  717 => 212,  712 => 209,  691 => 204,  678 => 586,  654 => 196,  587 => 14,  576 => 179,  539 => 171,  517 => 197,  471 => 155,  441 => 131,  437 => 142,  418 => 165,  386 => 160,  373 => 95,  304 => 151,  270 => 80,  265 => 83,  229 => 86,  477 => 138,  455 => 177,  448 => 164,  429 => 159,  407 => 95,  399 => 156,  389 => 99,  375 => 123,  358 => 103,  349 => 99,  335 => 84,  327 => 93,  298 => 112,  280 => 75,  249 => 123,  194 => 48,  142 => 50,  344 => 83,  318 => 162,  306 => 15,  295 => 103,  357 => 119,  300 => 150,  286 => 145,  276 => 87,  269 => 66,  254 => 94,  128 => 32,  237 => 69,  165 => 72,  122 => 25,  798 => 242,  770 => 113,  759 => 112,  748 => 337,  731 => 108,  721 => 606,  718 => 106,  708 => 271,  696 => 102,  617 => 234,  590 => 226,  553 => 87,  550 => 466,  540 => 84,  533 => 82,  500 => 129,  493 => 72,  489 => 181,  482 => 69,  467 => 67,  464 => 120,  458 => 166,  452 => 117,  449 => 174,  415 => 163,  382 => 124,  372 => 107,  361 => 104,  356 => 135,  339 => 124,  302 => 110,  285 => 94,  258 => 64,  123 => 41,  108 => 35,  424 => 156,  394 => 86,  380 => 80,  338 => 107,  319 => 16,  316 => 91,  312 => 109,  290 => 146,  267 => 100,  206 => 51,  110 => 35,  240 => 122,  224 => 51,  219 => 81,  217 => 53,  202 => 75,  186 => 69,  170 => 60,  100 => 30,  67 => 15,  14 => 1,  1096 => 291,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 836,  1013 => 275,  1007 => 274,  1002 => 823,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 300,  940 => 298,  937 => 248,  928 => 244,  926 => 243,  915 => 237,  912 => 236,  903 => 231,  898 => 230,  892 => 229,  889 => 748,  887 => 227,  884 => 747,  876 => 222,  874 => 269,  871 => 220,  863 => 214,  861 => 213,  858 => 212,  850 => 263,  843 => 206,  840 => 205,  815 => 245,  812 => 244,  808 => 199,  804 => 120,  799 => 196,  791 => 190,  785 => 238,  775 => 184,  771 => 183,  754 => 340,  728 => 170,  726 => 169,  723 => 168,  715 => 105,  711 => 263,  709 => 162,  706 => 260,  698 => 208,  694 => 255,  692 => 155,  689 => 254,  681 => 150,  677 => 149,  675 => 148,  663 => 251,  661 => 277,  650 => 246,  646 => 136,  629 => 129,  627 => 21,  625 => 236,  622 => 126,  598 => 230,  592 => 117,  586 => 115,  575 => 214,  566 => 210,  556 => 100,  554 => 177,  541 => 216,  536 => 205,  515 => 86,  511 => 85,  509 => 196,  488 => 126,  486 => 78,  483 => 189,  465 => 73,  463 => 179,  450 => 65,  432 => 179,  419 => 155,  371 => 141,  362 => 151,  353 => 129,  337 => 18,  333 => 122,  309 => 127,  303 => 76,  299 => 105,  291 => 111,  272 => 137,  261 => 96,  253 => 58,  239 => 89,  235 => 88,  213 => 52,  200 => 43,  198 => 92,  159 => 64,  149 => 61,  146 => 31,  131 => 44,  116 => 25,  79 => 26,  74 => 25,  71 => 23,  836 => 320,  817 => 319,  814 => 318,  811 => 317,  805 => 313,  787 => 657,  779 => 306,  776 => 305,  773 => 347,  761 => 296,  751 => 175,  747 => 293,  742 => 336,  739 => 291,  736 => 287,  724 => 214,  705 => 278,  702 => 601,  688 => 262,  680 => 263,  667 => 261,  662 => 246,  656 => 249,  649 => 258,  644 => 97,  641 => 241,  624 => 236,  613 => 233,  607 => 232,  597 => 221,  591 => 220,  584 => 223,  579 => 234,  563 => 213,  559 => 208,  551 => 202,  547 => 200,  537 => 201,  524 => 191,  512 => 185,  507 => 76,  504 => 143,  498 => 142,  485 => 176,  480 => 124,  472 => 171,  466 => 153,  460 => 119,  447 => 388,  442 => 162,  434 => 110,  428 => 29,  422 => 166,  404 => 149,  368 => 136,  364 => 133,  340 => 19,  334 => 130,  330 => 94,  325 => 126,  292 => 102,  287 => 109,  282 => 119,  279 => 98,  273 => 103,  266 => 98,  256 => 76,  252 => 67,  228 => 85,  218 => 81,  201 => 93,  64 => 11,  51 => 14,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 1705,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 707,  1829 => 706,  1827 => 705,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 689,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 676,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 666,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 638,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 617,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 611,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 602,  1559 => 601,  1552 => 598,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 584,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 573,  1477 => 567,  1473 => 566,  1467 => 562,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 520,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 511,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 1001,  1257 => 1000,  1254 => 479,  1250 => 478,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 460,  1196 => 958,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 938,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 924,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 425,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 898,  1099 => 420,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 880,  1076 => 359,  1070 => 875,  1057 => 352,  1052 => 404,  1045 => 854,  1040 => 852,  1036 => 283,  1032 => 395,  1028 => 394,  1023 => 393,  1020 => 392,  1016 => 391,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 821,  979 => 379,  974 => 312,  967 => 373,  962 => 803,  958 => 801,  954 => 253,  950 => 368,  945 => 367,  942 => 250,  938 => 365,  934 => 364,  927 => 361,  923 => 292,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 272,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 207,  841 => 321,  835 => 312,  830 => 249,  826 => 309,  822 => 308,  818 => 246,  813 => 306,  810 => 305,  806 => 304,  802 => 197,  795 => 118,  792 => 239,  789 => 350,  784 => 295,  782 => 187,  777 => 291,  772 => 289,  768 => 345,  763 => 343,  760 => 231,  756 => 230,  752 => 284,  745 => 281,  741 => 280,  738 => 335,  732 => 171,  719 => 279,  714 => 264,  710 => 310,  704 => 267,  699 => 103,  695 => 264,  690 => 263,  687 => 203,  683 => 251,  679 => 288,  672 => 255,  668 => 247,  665 => 201,  658 => 244,  645 => 270,  640 => 247,  634 => 238,  628 => 193,  623 => 238,  619 => 237,  611 => 18,  606 => 234,  603 => 467,  599 => 242,  595 => 231,  583 => 216,  580 => 221,  573 => 221,  560 => 101,  543 => 172,  538 => 209,  534 => 208,  530 => 202,  526 => 187,  521 => 146,  518 => 185,  514 => 183,  510 => 202,  503 => 75,  496 => 128,  490 => 193,  484 => 125,  474 => 137,  470 => 168,  446 => 133,  440 => 114,  436 => 113,  431 => 146,  425 => 126,  416 => 104,  412 => 98,  408 => 149,  403 => 170,  400 => 169,  396 => 133,  392 => 152,  385 => 24,  381 => 139,  367 => 134,  363 => 139,  359 => 23,  355 => 88,  350 => 128,  346 => 127,  343 => 20,  328 => 17,  324 => 164,  313 => 122,  307 => 88,  301 => 90,  288 => 27,  283 => 108,  271 => 107,  257 => 93,  251 => 76,  238 => 88,  233 => 68,  195 => 42,  191 => 68,  187 => 66,  183 => 64,  130 => 32,  88 => 32,  76 => 29,  115 => 44,  95 => 35,  655 => 275,  651 => 232,  648 => 231,  637 => 241,  633 => 130,  621 => 234,  618 => 233,  615 => 232,  604 => 186,  600 => 516,  588 => 116,  585 => 204,  582 => 203,  571 => 104,  567 => 178,  555 => 207,  552 => 190,  549 => 208,  544 => 199,  542 => 207,  535 => 212,  531 => 189,  519 => 189,  516 => 199,  513 => 168,  508 => 145,  506 => 83,  499 => 198,  495 => 141,  491 => 191,  481 => 162,  478 => 172,  475 => 184,  469 => 182,  456 => 136,  451 => 186,  443 => 132,  439 => 162,  427 => 169,  423 => 109,  420 => 176,  409 => 159,  405 => 148,  401 => 147,  391 => 129,  387 => 129,  384 => 132,  378 => 138,  365 => 93,  360 => 132,  348 => 21,  336 => 123,  332 => 138,  329 => 127,  323 => 81,  310 => 116,  305 => 94,  277 => 103,  274 => 91,  263 => 70,  259 => 133,  247 => 92,  244 => 91,  241 => 71,  222 => 55,  210 => 51,  207 => 80,  204 => 96,  184 => 46,  181 => 70,  167 => 38,  157 => 31,  96 => 20,  421 => 124,  417 => 137,  414 => 152,  406 => 171,  398 => 129,  393 => 100,  390 => 162,  376 => 110,  369 => 94,  366 => 91,  352 => 134,  345 => 133,  342 => 109,  331 => 106,  326 => 102,  320 => 129,  317 => 90,  314 => 117,  311 => 78,  308 => 60,  297 => 89,  293 => 85,  281 => 71,  278 => 140,  275 => 116,  264 => 87,  260 => 73,  248 => 75,  245 => 90,  242 => 90,  231 => 111,  227 => 85,  215 => 64,  212 => 78,  209 => 63,  197 => 71,  177 => 34,  171 => 66,  161 => 57,  132 => 52,  121 => 24,  105 => 29,  99 => 38,  81 => 22,  77 => 26,  180 => 35,  176 => 62,  156 => 28,  143 => 30,  139 => 49,  118 => 73,  189 => 101,  185 => 67,  173 => 61,  166 => 41,  152 => 62,  174 => 67,  164 => 65,  154 => 30,  150 => 33,  137 => 62,  133 => 29,  127 => 42,  107 => 40,  102 => 38,  83 => 17,  78 => 21,  53 => 14,  23 => 6,  42 => 15,  138 => 80,  134 => 31,  109 => 42,  103 => 39,  97 => 32,  94 => 27,  84 => 21,  75 => 20,  69 => 17,  66 => 19,  54 => 15,  44 => 13,  230 => 67,  226 => 85,  203 => 51,  193 => 56,  188 => 68,  182 => 68,  178 => 69,  168 => 56,  163 => 32,  160 => 35,  155 => 63,  148 => 34,  145 => 52,  140 => 47,  136 => 30,  125 => 43,  120 => 27,  113 => 37,  101 => 30,  92 => 30,  89 => 29,  85 => 31,  73 => 14,  62 => 13,  59 => 18,  56 => 17,  41 => 8,  126 => 50,  119 => 46,  111 => 41,  106 => 35,  98 => 36,  93 => 35,  86 => 18,  70 => 21,  60 => 15,  28 => 2,  36 => 6,  114 => 38,  104 => 32,  91 => 26,  80 => 18,  63 => 11,  58 => 12,  40 => 12,  34 => 13,  45 => 16,  61 => 10,  55 => 11,  48 => 17,  39 => 5,  35 => 4,  31 => 3,  26 => 9,  21 => 5,  46 => 9,  29 => 8,  57 => 20,  50 => 15,  47 => 16,  38 => 9,  33 => 10,  49 => 7,  32 => 3,  246 => 65,  236 => 121,  232 => 87,  225 => 84,  221 => 104,  216 => 80,  214 => 102,  211 => 79,  208 => 76,  205 => 76,  199 => 74,  196 => 59,  190 => 84,  179 => 94,  175 => 53,  172 => 76,  169 => 43,  162 => 89,  158 => 49,  153 => 46,  151 => 66,  147 => 28,  144 => 58,  141 => 56,  135 => 53,  129 => 51,  124 => 75,  117 => 37,  112 => 43,  90 => 32,  87 => 31,  82 => 26,  72 => 23,  68 => 13,  65 => 22,  52 => 10,  43 => 13,  37 => 7,  30 => 9,  27 => 2,  25 => 6,  24 => 7,  22 => 6,  19 => 4,);
    }
}
