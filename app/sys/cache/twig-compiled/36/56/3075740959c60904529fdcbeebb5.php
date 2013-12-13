<?php

/* AdminBundle:Agents:edit-agent.html.twig */
class __TwigTemplate_36563075740959c60904529fdcbeebb5 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("AdminBundle::layout.html.twig");

        $this->blocks = array(
            'pagebar' => array($this, 'block_pagebar'),
            'html_head' => array($this, 'block_html_head'),
            'page' => array($this, 'block_page'),
            'agent_page_top' => array($this, 'block_agent_page_top'),
            'agent_form_bottom' => array($this, 'block_agent_form_bottom'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "AdminBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 2
        $context["design"] = $this->env->loadTemplate("AdminBundle:Common:design-macros.html.twig");
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_pagebar($context, array $blocks = array())
    {
        // line 4
        echo "\t";
        if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
        if ($this->getAttribute($_agent_, "id")) {
            // line 5
            echo "\t<nav>
\t\t<ul>
\t\t\t<li><a href=\"";
            // line 7
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_agents_edit_prefs", array("person_id" => $this->getAttribute($_agent_, "id"))), "html", null, true);
            echo "\">Profile Settings</a></li>
\t\t\t";
            // line 8
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if (($this->getAttribute($_agent_, "id") != $this->getAttribute($this->getAttribute($_app_, "user"), "id"))) {
                echo "<li class=\"tools\" id=\"tools_menu_trigger\"><a></a><em></em></li>";
            }
            // line 9
            echo "\t\t</ul>
\t</nav>
\t<ul id=\"tools_menu\" style=\"display: none\">
\t\t<li><a href=\"";
            // line 12
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_agents_remove", array("agent_id" => $this->getAttribute($_agent_, "id"))), "html", null, true);
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.delete_agent");
            echo "</a></li>
\t\t<li><a href=\"";
            // line 13
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_agents_loginas", array("agent_id" => $this->getAttribute($_agent_, "id"))), "html", null, true);
            echo "\">Login as this agent</a></li>
\t</ul>
\t";
        }
        // line 16
        echo "
\t<ul>
\t\t<li><a href=\"";
        // line 18
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_agents"), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.agents");
        echo "</a></li>
\t\t";
        // line 19
        if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
        if ($this->getAttribute($_agent_, "id")) {
            // line 20
            echo "\t\t\t<li>";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.edit_agent");
            echo "</li>
\t\t";
        } else {
            // line 22
            echo "\t\t\t<li>";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.new_agent");
            echo "</li>
\t\t";
        }
        // line 24
        echo "\t</ul>
";
    }

    // line 26
    public function block_html_head($context, array $blocks = array())
    {
        // line 27
        echo "<script type=\"text/javascript\" src=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("javascripts/DeskPRO/Admin/ElementHandler/AgentEditPage.js"), "html", null, true);
        echo "\"></script>
<style type=\"text/css\">
#more_emails_empty {
\tfont-size: 11px;
\tpadding: 6px;
}

#more_emails {
\tfont-size: 11px;
\tpadding: 6px;
}

#more_emails .add-row {
\tfont-size: 11px;
}

#more_emails .add-row input {
\tpadding: 0;
\tmargin: 0;
\tborder: none;
\tborder-bottom: 1px solid #888;
\twidth: 130px;
\tbox-shadow: none;
\tfont-size:11px;
}

#more_emails .add-row #more_emails_trigger {
\tcolor: #888;
\tborder: 1px solid #888;
\tborder-radius: 4px;
\tpadding: 1px 4px;
}

#more_emails .remove-trigger {
\tcolor: #999;
\tcursor: pointer;
}

#more_emails .remove-trigger:hover {
\tcolor: #A60003;
}
</style>
";
    }

    // line 70
    public function block_page($context, array $blocks = array())
    {
        // line 71
        echo "
";
        // line 72
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "getSession", array(), "method"), "getFlash", array(0 => "saved_agent"), "method")) {
            // line 73
            echo "\t<div class=\"alert-message block-message success\">
\t\tAgent was saved successfully.
\t</div>
";
        }
        // line 77
        echo "
";
        // line 78
        if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
        if ($this->getAttribute($_agent_, "is_vacation_mode")) {
            // line 79
            echo "\t<div class=\"alert-message block-message info\">
\t\t<h1>";
            // line 80
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.name_in_vacation", array("name" => $this->getAttribute($_agent_, "display_name")));
            echo "</h1>
\t\t<form action=\"";
            // line 81
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_agents_setvacation", array("person_id" => $this->getAttribute($_agent_, "id"), "set_to" => "0")), "html", null, true);
            echo "\" method=\"post\">
\t\t\t";
            // line 82
            echo $this->env->getExtension('deskpro_templating')->formToken();
            echo "
\t\t\t<p>
\t\t\t\t";
            // line 84
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.notice_effect_of_vacation");
            echo "
                ";
            // line 85
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.if_name_is_back", array("name" => $this->getAttribute($_agent_, "display_name")));
            echo " <button class=\"clean-white small\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.turn_off_vacation_mode");
            echo "</button>
\t\t\t</p>
\t\t</form>
\t</div>
";
        }
        // line 90
        echo "
";
        // line 91
        $this->displayBlock('agent_page_top', $context, $blocks);
        // line 92
        echo "
<form
\taction=\"";
        // line 94
        if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_agents_edit_save", array("person_id" => (($this->getAttribute($_agent_, "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_agent_, "id"), "0")) : ("0")))), "html", null, true);
        echo "\"
\tmethod=\"POST\"
\tdata-element-handler=\"DeskPRO.Admin.ElementHandler.AgentEditPage\"
\tdata-validate-url=\"";
        // line 97
        if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_agents_edit_formvalidate", array("person_id" => (($this->getAttribute($_agent_, "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_agent_, "id"), "0")) : ("0")))), "html", null, true);
        echo "\"
>

";
        // line 100
        if (isset($context["agent_base"])) { $_agent_base_ = $context["agent_base"]; } else { $_agent_base_ = null; }
        if ($_agent_base_) {
            // line 101
            echo "\t<div class=\"alert-message block-message info\">
\t\tYou are creating a new agent based off of <strong>";
            // line 102
            if (isset($context["agent_base"])) { $_agent_base_ = $context["agent_base"]; } else { $_agent_base_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_agent_base_, "display_name"), "html", null, true);
            echo "</strong>. Permissions and notification settings
\t\twill be copied.
\t\t<input type=\"hidden\" name=\"base_agent_id\" value=\"";
            // line 104
            if (isset($context["agent_base"])) { $_agent_base_ = $context["agent_base"]; } else { $_agent_base_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_agent_base_, "id"), "html", null, true);
            echo "\" />
\t</div>
";
        }
        // line 107
        echo "
<div id=\"errors_container\" style=\"display: none;\">
\t<h3>Please correct the following errors:</h3>
\t<ul></ul>
\t<div id=\"dupe_confirm\" style=\"display: none\">
\t\t&bull; The email address you entered is already in use.
\t\tDo you want to merge the existing account into this agent?<br />
\t\t<label>
\t\t\t<input type=\"checkbox\" name=\"confirm_email_dupe\" value=\"1\" ";
        // line 115
        if (isset($context["confirm_email_dupe"])) { $_confirm_email_dupe_ = $context["confirm_email_dupe"]; } else { $_confirm_email_dupe_ = null; }
        if ($_confirm_email_dupe_) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\tYes, merge this agent account into the existing account
\t\t</label>
\t</div>
</div>

<div class=\"dp-form\">
\t<div class=\"dp-form-section\">
\t\t<div class=\"dp-form-row\">
\t\t\t<div class=\"dp-form-label\">
\t\t\t\t<label>";
        // line 125
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.name");
        echo "</label>
\t\t\t</div>
\t\t\t<div class=\"dp-form-input\">
\t\t\t\t<input style=\"width:422px;\" type=\"text\" name=\"agent[name]\" value=\"";
        // line 128
        if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "name"), "html", null, true);
        echo "\" />
\t\t\t</div>
\t\t</div>

\t\t<div class=\"dp-form-row\">
\t\t\t<div class=\"dp-form-label\">
\t\t\t\t<label>Override Name</label>
\t\t\t</div>
\t\t\t<div class=\"dp-form-input\">
\t\t\t\t<input style=\"width:422px;\" type=\"text\" name=\"agent[override_display_name]\" value=\"";
        // line 137
        if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "override_display_name"), "html", null, true);
        echo "\" />
\t\t\t\t<div>If entered, this will be displayed to users instead of the agent's real name.</div>
\t\t\t</div>
\t\t</div>

\t\t<div class=\"dp-form-row\">
\t\t\t<div class=\"dp-form-label\">
\t\t\t\t<label>";
        // line 144
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.email");
        echo "</label>
\t\t\t</div>
\t\t\t<div class=\"dp-form-input\">
\t\t\t\t<input style=\"width:422px;\" type=\"text\" name=\"agent[email]\" value=\"";
        // line 147
        if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "primary_email_address"), "html", null, true);
        echo "\" />

\t\t\t\t<div id=\"more_emails_empty\" ";
        // line 149
        if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
        if ((twig_length_filter($this->env, $this->getAttribute($_agent_, "emails")) != 1)) {
            echo "style=\"display: none;\"";
        }
        echo ">
\t\t\t\t\t<a href=\"#\">Add additional emails &rarr;</a>
\t\t\t\t</div>
\t\t\t\t<div id=\"more_emails\" ";
        // line 152
        if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
        if ((twig_length_filter($this->env, $this->getAttribute($_agent_, "emails")) == 1)) {
            echo "style=\"display: none;\"";
        }
        echo ">
\t\t\t\t\t<strong>Additional Emails:</strong>
\t\t\t\t\t<ul>
\t\t\t\t\t\t";
        // line 155
        if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_agent_, "emails"));
        foreach ($context['_seq'] as $context["_key"] => $context["email"]) {
            if (isset($context["email"])) { $_email_ = $context["email"]; } else { $_email_ = null; }
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            if (($this->getAttribute($_email_, "id") != $this->getAttribute($this->getAttribute($_agent_, "primary_email"), "id"))) {
                // line 156
                echo "\t\t\t\t\t\t\t<li data-email-id=\"";
                if (isset($context["email"])) { $_email_ = $context["email"]; } else { $_email_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_email_, "id"), "html", null, true);
                echo "\">&bull; <span>";
                if (isset($context["email"])) { $_email_ = $context["email"]; } else { $_email_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_email_, "email"), "html", null, true);
                echo "</span>&nbsp;&nbsp;&nbsp;<i class=\"icon-trash remove-trigger\" title=\"Remove email\"></i></li>
\t\t\t\t\t\t";
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['email'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 158
        echo "\t\t\t\t\t\t<li class=\"add-row\">Add another email: <input type=\"text\" id=\"more_emails_txt\" /> <a id=\"more_emails_trigger\" href=\"#\">Add</a></li>
\t\t\t\t\t</ul>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>

\t\t";
        // line 164
        if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
        if ((!$this->getAttribute($_agent_, "id"))) {
            // line 165
            echo "\t\t\t<div class=\"dp-form-row\">
\t\t\t\t<div class=\"dp-form-label\">
\t\t\t\t\t<label>";
            // line 167
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.initial_password");
            echo "</label>
\t\t\t\t</div>
\t\t\t\t<div class=\"dp-form-input\">
\t\t\t\t\t<input style=\"width:422px;\" type=\"text\" name=\"agent[password]\" value=\"";
            // line 170
            if (isset($context["random_password"])) { $_random_password_ = $context["random_password"]; } else { $_random_password_ = null; }
            echo twig_escape_filter($this->env, $_random_password_, "html", null, true);
            echo "\" />
\t\t\t\t</div>
\t\t\t</div>
\t\t";
        } else {
            // line 174
            echo "\t\t\t";
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            if ($this->getAttribute($_agent_, "getPasswordHash", array(), "method")) {
                // line 175
                echo "\t\t\t\t<div class=\"dp-form-row\">
\t\t\t\t\t<div class=\"dp-form-label\">
\t\t\t\t\t\t<label>Reset Password</label>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"dp-form-input\">
\t\t\t\t\t\t<input style=\"width:422px;\" type=\"text\" name=\"agent[password]\" value=\"\" placeholder=\"Leave blank to not reset password\" />
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t";
            }
            // line 184
            echo "\t\t\t";
            if (isset($context["user_usersources"])) { $_user_usersources_ = $context["user_usersources"]; } else { $_user_usersources_ = null; }
            if (twig_length_filter($this->env, $_user_usersources_)) {
                // line 185
                echo "\t\t\t\t<div class=\"dp-form-row\">
\t\t\t\t\t<div class=\"dp-form-label\">
\t\t\t\t\t\t<label>Usersources</label>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"dp-form-input\">
\t\t\t\t\t\tThis agent exists through the following <a href=\"";
                // line 190
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_userreg_options"), "html", null, true);
                echo "\">usersources</a>:
\t\t\t\t\t\t<ul>
\t\t\t\t\t\t\t";
                // line 192
                if (isset($context["user_usersources"])) { $_user_usersources_ = $context["user_usersources"]; } else { $_user_usersources_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($_user_usersources_);
                foreach ($context['_seq'] as $context["_key"] => $context["us"]) {
                    // line 193
                    echo "\t\t\t\t\t\t\t\t<li>&bull; ";
                    if (isset($context["us"])) { $_us_ = $context["us"]; } else { $_us_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_us_, "usersource"), "title"), "html", null, true);
                    echo "</li>
\t\t\t\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['us'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 195
                echo "\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t";
            }
            // line 199
            echo "\t\t";
        }
        // line 200
        echo "
\t\t";
        // line 201
        if (isset($context["all_teams"])) { $_all_teams_ = $context["all_teams"]; } else { $_all_teams_ = null; }
        if ($_all_teams_) {
            // line 202
            echo "\t\t\t<div class=\"dp-form-row\">
\t\t\t\t<div class=\"dp-form-label\">
\t\t\t\t\t<label>";
            // line 204
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.teams");
            echo "</label>
\t\t\t\t</div>
\t\t\t\t<div class=\"dp-form-input\">
\t\t\t\t\t<div class=\"checkbox-rows\"  style=\"width:417px;\">
\t\t\t\t\t\t<ul style=\"max-height: 200px;\">
\t\t\t\t\t\t\t";
            // line 209
            if (isset($context["all_teams"])) { $_all_teams_ = $context["all_teams"]; } else { $_all_teams_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_all_teams_);
            foreach ($context['_seq'] as $context["_key"] => $context["team"]) {
                // line 210
                echo "\t\t\t\t\t\t\t<li>
\t\t\t\t\t\t\t\t<label>
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"agent[teams][]\" value=\"";
                // line 212
                if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_team_, "id"), "html", null, true);
                echo "\" ";
                if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                if (isset($context["agent_teams"])) { $_agent_teams_ = $context["agent_teams"]; } else { $_agent_teams_ = null; }
                if (twig_in_filter($this->getAttribute($_team_, "id"), $_agent_teams_)) {
                    echo "checked=\"checked\"";
                }
                echo " />
\t\t\t\t\t\t\t\t\t";
                // line 213
                if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_team_, "name"), "html", null, true);
                echo "
\t\t\t\t\t\t\t\t</label>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['team'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 217
            echo "\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t";
        }
        // line 222
        echo "\t</div>

\t<div class=\"dp-form-section\">
\t\t<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\"><tr>
\t\t\t<td width=\"35%\" style=\"padding-right: 10px\">
\t\t\t\t<div class=\"dp-form-row\">
\t\t\t\t\t<div class=\"dp-form-label\">
\t\t\t\t\t\t<label>";
        // line 229
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.zone_access");
        echo "</label>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"dp-form-input\">
\t\t\t\t\t\t<input type=\"hidden\" name=\"agent[can_agent]\" value=\"1\" />

\t\t\t\t\t\t<label style=\"line-height: 24px\">
\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"agent[can_admin]\" value=\"1\" ";
        // line 235
        if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
        if ($this->getAttribute($_agent_, "can_admin")) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\t\t\t\t<span class=\"with-icon zone-admin\">";
        // line 236
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.can_access_admin");
        echo "</span>
\t\t\t\t\t\t</label><br />

\t\t\t\t\t\t<label style=\"line-height: 24px\">
\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"agent[can_reports]\" value=\"1\" ";
        // line 240
        if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
        if ($this->getAttribute($_agent_, "can_reports")) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\t\t\t\t<span class=\"with-icon zone-reports\">";
        // line 241
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.can_access_reports");
        echo "</span>
\t\t\t\t\t\t</label><br />

\t\t\t\t\t\t<label style=\"line-height: 24px\">
\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"agent[can_billing]\" value=\"1\" ";
        // line 245
        if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
        if ($this->getAttribute($_agent_, "can_billing")) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\t\t\t\t<span class=\"with-icon zone-billing\">";
        // line 246
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.can_access_billing");
        echo "</span>
\t\t\t\t\t\t</label><br />
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</td>
\t\t\t<td width=\"65%\" style=\"padding-left: 10px\">
\t\t\t\t<div class=\"dp-form-row\">
\t\t\t\t\t<div class=\"dp-form-label\">
\t\t\t\t\t\t<label>";
        // line 254
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.department_access_permissions");
        echo "</label>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"dp-form-input\" id=\"dep_perms\">

\t\t\t\t\t\t<nav class=\"check-grid-tabs\" style=\"height: 24px;\">
\t\t\t\t\t\t\t<ul data-element-handler=\"DeskPRO.ElementHandler.SimpleTabs\" data-trigger-elements=\"> li\">
\t\t\t\t\t\t\t\t<li class=\"on\" data-tab-for=\"#depperm_tickets\"><span>";
        // line 260
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.tickets");
        echo "</span></li>
\t\t\t\t\t\t\t\t";
        // line 261
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.apps_chat"), "method")) {
            echo "<li data-tab-for=\"#depperm_chat\"><span>";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.chat");
            echo "</span></li>";
        }
        // line 262
        echo "\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t</nav>

\t\t\t\t\t\t<div class=\"check-grid\" style=\"max-height: 200px; overflow: auto;\" id=\"depperm_tickets\">
\t\t\t\t\t\t\t<table width=\"100%\">
\t\t\t\t\t\t\t\t<thead>
\t\t\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t\t\t<th style=\"text-align: left;\">";
        // line 269
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.department");
        echo "</th>
\t\t\t\t\t\t\t\t\t\t<th width=\"80\">
\t\t\t\t\t\t\t\t\t\t\t<a href=\"#\" id=\"ticketdep_toggle_all_full\" class=\"tipped\" title=\"Provides the agent full access to the department's tickets. Click to toggle all.\">Full Access</a>
\t\t\t\t\t\t\t\t\t\t</th>
\t\t\t\t\t\t\t\t\t\t<th width=\"80\">
\t\t\t\t\t\t\t\t\t\t\t<a href=\"#\" id=\"ticketdep_toggle_all_assign\" class=\"tipped\" title=\"If full access is not provided, this controls whether agents can create or assign tickets to this department. Click to toggle all.\">Assignment</a>
\t\t\t\t\t\t\t\t\t\t</th>
\t\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t\t</thead>
\t\t\t\t\t\t\t\t<tbody>
\t\t\t\t\t\t\t\t\t";
        // line 279
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "departments"), "getRootNodes", array(), "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["dep"]) {
            if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
            if ($this->getAttribute($_dep_, "is_tickets_enabled")) {
                // line 280
                echo "\t\t\t\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t\t\t\t";
                // line 281
                if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                if (twig_length_filter($this->env, $this->getAttribute($_dep_, "children"))) {
                    // line 282
                    echo "\t\t\t\t\t\t\t\t\t\t\t\t<td class=\"title title-group\">";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                    echo "</td>
\t\t\t\t\t\t\t\t\t\t\t\t<td class=\"prop\">&nbsp;</td>
\t\t\t\t\t\t\t\t\t\t\t\t<td class=\"prop\">&nbsp;</td>
\t\t\t\t\t\t\t\t\t\t\t";
                } else {
                    // line 286
                    echo "\t\t\t\t\t\t\t\t\t\t\t\t<td class=\"title\">";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                    echo "</td>
\t\t\t\t\t\t\t\t\t\t\t\t<td class=\"prop ticket-choice\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<input class=\"onoff-slider departments\" type=\"checkbox\" ";
                    // line 288
                    if (isset($context["agent_deps"])) { $_agent_deps_ = $context["agent_deps"]; } else { $_agent_deps_ = null; }
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                    if (($this->getAttribute($this->getAttribute($_agent_deps_, $this->getAttribute($_dep_, "id"), array(), "array"), "tickets") || (!$this->getAttribute($_agent_, "id")))) {
                        echo "checked=\"checked\"";
                    }
                    echo " name=\"agent[departments][";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "id"), "html", null, true);
                    echo "][tickets]\" value=\"1\" />
\t\t\t\t\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t\t\t\t\t<td class=\"prop ticket-assign-choice\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<input class=\"onoff-slider departments_assign\" type=\"checkbox\" ";
                    // line 291
                    if (isset($context["agent_deps"])) { $_agent_deps_ = $context["agent_deps"]; } else { $_agent_deps_ = null; }
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    if (isset($context["agent_deps_assign"])) { $_agent_deps_assign_ = $context["agent_deps_assign"]; } else { $_agent_deps_assign_ = null; }
                    if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                    if ((($this->getAttribute($this->getAttribute($_agent_deps_, $this->getAttribute($_dep_, "id"), array(), "array"), "tickets") || $this->getAttribute($this->getAttribute($_agent_deps_assign_, $this->getAttribute($_dep_, "id"), array(), "array"), "tickets")) || (!$this->getAttribute($_agent_, "id")))) {
                        echo "checked=\"checked\"";
                    }
                    echo " name=\"agent[departments_assign][";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "id"), "html", null, true);
                    echo "][tickets]\" value=\"1\" />
\t\t\t\t\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t\t\t\t";
                }
                // line 294
                echo "\t\t\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t\t\t\t";
                // line 295
                if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                if ($this->getAttribute($_dep_, "children")) {
                    // line 296
                    echo "\t\t\t\t\t\t\t\t\t\t\t";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($_dep_, "children"));
                    foreach ($context['_seq'] as $context["_key"] => $context["subdep"]) {
                        // line 297
                        echo "\t\t\t\t\t\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t\t\t\t\t\t<td class=\"title\">&nbsp;&nbsp;&nbsp;&nbsp; ";
                        // line 298
                        if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_subdep_, "title"), "html", null, true);
                        echo "</td>
\t\t\t\t\t\t\t\t\t\t\t\t\t<td class=\"prop ticket-choice\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<input class=\"onoff-slider departments\" type=\"checkbox\" ";
                        // line 300
                        if (isset($context["agent_deps"])) { $_agent_deps_ = $context["agent_deps"]; } else { $_agent_deps_ = null; }
                        if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                        if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                        if (($this->getAttribute($this->getAttribute($_agent_deps_, $this->getAttribute($_subdep_, "id"), array(), "array"), "tickets") || (!$this->getAttribute($_agent_, "id")))) {
                            echo "checked=\"checked\"";
                        }
                        echo " name=\"agent[departments][";
                        if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_subdep_, "id"), "html", null, true);
                        echo "][tickets]\" value=\"1\" />
\t\t\t\t\t\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t\t\t\t\t\t<td class=\"prop ticket-assign-choice\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<input class=\"onoff-slide departments_assignr\" type=\"checkbox\" ";
                        // line 303
                        if (isset($context["agent_deps"])) { $_agent_deps_ = $context["agent_deps"]; } else { $_agent_deps_ = null; }
                        if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                        if (isset($context["agent_deps_assign"])) { $_agent_deps_assign_ = $context["agent_deps_assign"]; } else { $_agent_deps_assign_ = null; }
                        if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                        if ((($this->getAttribute($this->getAttribute($_agent_deps_, $this->getAttribute($_subdep_, "id"), array(), "array"), "tickets") || $this->getAttribute($this->getAttribute($_agent_deps_assign_, $this->getAttribute($_subdep_, "id"), array(), "array"), "tickets")) || (!$this->getAttribute($_agent_, "id")))) {
                            echo "checked=\"checked\"";
                        }
                        echo " name=\"agent[departments_assign][";
                        if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_subdep_, "id"), "html", null, true);
                        echo "][tickets]\" value=\"1\" />
\t\t\t\t\t\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t\t\t\t\t";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subdep'], $context['_parent'], $context['loop']);
                    $context = array_merge($_parent, array_intersect_key($context, $_parent));
                    // line 307
                    echo "\t\t\t\t\t\t\t\t\t\t";
                }
                // line 308
                echo "\t\t\t\t\t\t\t\t\t";
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['dep'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 309
        echo "\t\t\t\t\t\t\t\t</tbody>
\t\t\t\t\t\t\t</table>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t";
        // line 313
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.apps_chat"), "method")) {
            // line 314
            echo "\t\t\t\t\t\t\t<div class=\"check-grid\" style=\"max-height: 200px; overflow: auto; display: none;\" id=\"depperm_chat\">
\t\t\t\t\t\t\t\t<table width=\"100%\">
\t\t\t\t\t\t\t\t\t<thead>
\t\t\t\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t\t\t\t<th style=\"text-align: left;\">";
            // line 318
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.department");
            echo "</th>
\t\t\t\t\t\t\t\t\t\t\t<th width=\"60\">
\t\t\t\t\t\t\t\t\t\t\t\t<a href=\"#\" id=\"chatdep_toggle_all\" style=\"font-size:9px\">Toggle All</a>
\t\t\t\t\t\t\t\t\t\t\t</th>
\t\t\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t\t\t</thead>
\t\t\t\t\t\t\t\t\t<tbody>
\t\t\t\t\t\t\t\t\t\t";
            // line 325
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "departments"), "getRootNodes", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["dep"]) {
                if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                if ($this->getAttribute($_dep_, "is_chat_enabled")) {
                    // line 326
                    echo "\t\t\t\t\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t\t\t\t\t";
                    // line 327
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    if (twig_length_filter($this->env, $this->getAttribute($_dep_, "children"))) {
                        // line 328
                        echo "\t\t\t\t\t\t\t\t\t\t\t\t\t<td class=\"title title-group\">";
                        if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                        echo "</td>
\t\t\t\t\t\t\t\t\t\t\t\t\t<td class=\"prop\">&nbsp;</td>
\t\t\t\t\t\t\t\t\t\t\t\t";
                    } else {
                        // line 331
                        echo "\t\t\t\t\t\t\t\t\t\t\t\t\t<td class=\"title\">";
                        if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                        echo "</td>
\t\t\t\t\t\t\t\t\t\t\t\t\t<td class=\"prop chat-choice\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<input class=\"onoff-slider\" type=\"checkbox\" ";
                        // line 333
                        if (isset($context["agent_deps"])) { $_agent_deps_ = $context["agent_deps"]; } else { $_agent_deps_ = null; }
                        if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                        if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                        if (($this->getAttribute($this->getAttribute($_agent_deps_, $this->getAttribute($_dep_, "id"), array(), "array"), "chat") || (!$this->getAttribute($_agent_, "id")))) {
                            echo "checked=\"checked\"";
                        }
                        echo " name=\"agent[departments][";
                        if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "id"), "html", null, true);
                        echo "][chat]\" value=\"1\" />
\t\t\t\t\t\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t\t\t\t\t";
                    }
                    // line 336
                    echo "\t\t\t\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t\t\t\t\t";
                    // line 337
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    if ($this->getAttribute($_dep_, "children")) {
                        // line 338
                        echo "\t\t\t\t\t\t\t\t\t\t\t\t";
                        if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_dep_, "children"));
                        foreach ($context['_seq'] as $context["_key"] => $context["subdep"]) {
                            // line 339
                            echo "\t\t\t\t\t\t\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<td class=\"title\">&nbsp;&nbsp;&nbsp;&nbsp; ";
                            // line 340
                            if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_subdep_, "title"), "html", null, true);
                            echo "</td>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<td class=\"prop chat-choice\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<input class=\"onoff-slider\" type=\"checkbox\" ";
                            // line 342
                            if (isset($context["agent_deps"])) { $_agent_deps_ = $context["agent_deps"]; } else { $_agent_deps_ = null; }
                            if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                            if (($this->getAttribute($this->getAttribute($_agent_deps_, $this->getAttribute($_subdep_, "id"), array(), "array"), "chat") || (!$this->getAttribute($_agent_, "id")))) {
                                echo "checked=\"checked\"";
                            }
                            echo " name=\"agent[departments][";
                            if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_subdep_, "id"), "html", null, true);
                            echo "][chat]\" value=\"1\" />
\t\t\t\t\t\t\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t\t\t\t\t\t";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subdep'], $context['_parent'], $context['loop']);
                        $context = array_merge($_parent, array_intersect_key($context, $_parent));
                        // line 346
                        echo "\t\t\t\t\t\t\t\t\t\t\t";
                    }
                    // line 347
                    echo "\t\t\t\t\t\t\t\t\t\t";
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['dep'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 348
            echo "\t\t\t\t\t\t\t\t\t</tbody>
\t\t\t\t\t\t\t\t</table>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t";
        }
        // line 352
        echo "
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</td>
\t\t</tr></table>
\t</div>

\t";
        // line 362
        echo "\t<div class=\"dp-form-section\">
\t\t<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\"><tr>
\t\t\t<td width=\"35%\" style=\"padding-right: 10px\">
\t\t\t\t";
        // line 365
        if (isset($context["all_usergroups"])) { $_all_usergroups_ = $context["all_usergroups"]; } else { $_all_usergroups_ = null; }
        if ($_all_usergroups_) {
            // line 366
            echo "\t\t\t\t\t<div class=\"dp-form-row\">
\t\t\t\t\t\t<div class=\"dp-form-label\">
\t\t\t\t\t\t\t<label>";
            // line 368
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.permission_groups");
            echo "</label>
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"dp-form-input\">
\t\t\t\t\t\t\t<div class=\"checkbox-rows\" id=\"usergroup_checks\">
\t\t\t\t\t\t\t\t<ul style=\"max-height: 200px;\">
\t\t\t\t\t\t\t\t\t";
            // line 373
            if (isset($context["all_usergroups"])) { $_all_usergroups_ = $context["all_usergroups"]; } else { $_all_usergroups_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_all_usergroups_);
            foreach ($context['_seq'] as $context["_key"] => $context["usergroup"]) {
                // line 374
                echo "\t\t\t\t\t\t\t\t\t<li>
\t\t\t\t\t\t\t\t\t\t<label>
\t\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"agent[usergroups][]\" value=\"";
                // line 376
                if (isset($context["usergroup"])) { $_usergroup_ = $context["usergroup"]; } else { $_usergroup_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_usergroup_, "id"), "html", null, true);
                echo "\" ";
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                if (isset($context["usergroup"])) { $_usergroup_ = $context["usergroup"]; } else { $_usergroup_ = null; }
                if (isset($context["agent_usergroups"])) { $_agent_usergroups_ = $context["agent_usergroups"]; } else { $_agent_usergroups_ = null; }
                if ((((!$this->getAttribute($_agent_, "id")) && ($this->getAttribute($_usergroup_, "id") == 2)) || twig_in_filter($this->getAttribute($_usergroup_, "id"), $_agent_usergroups_))) {
                    echo "checked=\"checked\"";
                }
                echo " />
\t\t\t\t\t\t\t\t\t\t\t";
                // line 377
                if (isset($context["usergroup"])) { $_usergroup_ = $context["usergroup"]; } else { $_usergroup_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_usergroup_, "title"), "html", null, true);
                echo "
\t\t\t\t\t\t\t\t\t\t\t<a href=\"";
                // line 378
                if (isset($context["usergroup"])) { $_usergroup_ = $context["usergroup"]; } else { $_usergroup_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_agents_groups_edit", array("usergroup_id" => $this->getAttribute($_usergroup_, "id"))), "html", null, true);
                echo "\" class=\"tools-menu-icon\" style=\"position: relative; top: 2px;\"></a>
\t\t\t\t\t\t\t\t\t\t</label>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['usergroup'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 382
            echo "\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t";
        }
        // line 387
        echo "\t\t\t</td>
\t\t\t<td width=\"65%\" style=\"padding-left: 10px\">
\t\t\t\t<div class=\"dp-form-row\">
\t\t\t\t\t<div class=\"dp-form-label\">
\t\t\t\t\t\t<label>";
        // line 391
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.permissions");
        echo "</label>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"dp-form-input\" id=\"permgroup_table\">
\t\t\t\t\t\t";
        // line 394
        $this->env->loadTemplate("AdminBundle:Agents:edit-agent-permtable.html.twig")->display($context);
        // line 395
        echo "\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</td>
\t\t</table>
\t</div>
</div>

";
        // line 402
        $this->displayBlock('agent_form_bottom', $context, $blocks);
        // line 403
        echo "
<footer class=\"controls\" id=\"save_control\">
\t<div class=\"is-not-loading\">
\t\t<button class=\"clean-white\">";
        // line 406
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.save_agent");
        echo "</button>
\t</div>
\t<div class=\"is-loading\">
\t\t<div class=\"loading-icon-flat\">&nbsp;</div>
\t</div>
</footer>

<br class=\"clear\" />
</form>

";
        // line 416
        $this->env->loadTemplate("AdminBundle:Agents:edit-agent-overlays.html.twig")->display($context);
    }

    // line 91
    public function block_agent_page_top($context, array $blocks = array())
    {
    }

    // line 402
    public function block_agent_form_bottom($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "AdminBundle:Agents:edit-agent.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  953 => 406,  948 => 403,  935 => 394,  929 => 391,  916 => 382,  864 => 365,  844 => 348,  816 => 342,  807 => 339,  801 => 338,  774 => 331,  766 => 328,  737 => 314,  685 => 300,  664 => 294,  635 => 288,  593 => 269,  546 => 245,  532 => 240,  865 => 221,  852 => 216,  838 => 208,  820 => 201,  781 => 333,  764 => 178,  725 => 164,  632 => 141,  602 => 136,  565 => 117,  529 => 111,  505 => 107,  487 => 104,  473 => 102,  1853 => 842,  1849 => 841,  1839 => 837,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 813,  1778 => 812,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 782,  1705 => 781,  1688 => 770,  1681 => 769,  1677 => 768,  1667 => 764,  1656 => 762,  1648 => 757,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 714,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 695,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 651,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 610,  1289 => 607,  1276 => 604,  1263 => 601,  1244 => 596,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 565,  1181 => 561,  1171 => 557,  1158 => 550,  1114 => 530,  1104 => 526,  1072 => 512,  1062 => 508,  1024 => 494,  1014 => 490,  1000 => 482,  990 => 478,  980 => 474,  960 => 466,  918 => 448,  888 => 376,  834 => 346,  673 => 342,  636 => 323,  462 => 209,  454 => 204,  1144 => 542,  1139 => 399,  1131 => 399,  1127 => 397,  1110 => 396,  1092 => 391,  1089 => 390,  1086 => 520,  1084 => 388,  1063 => 387,  1060 => 386,  1055 => 385,  1050 => 384,  1035 => 372,  1019 => 267,  1003 => 263,  959 => 251,  900 => 377,  880 => 434,  870 => 430,  867 => 366,  859 => 362,  848 => 215,  839 => 197,  828 => 191,  823 => 188,  809 => 181,  800 => 178,  797 => 177,  794 => 176,  786 => 174,  740 => 162,  734 => 313,  703 => 354,  693 => 350,  630 => 412,  626 => 140,  614 => 138,  610 => 385,  581 => 124,  564 => 254,  525 => 236,  722 => 162,  697 => 256,  674 => 249,  671 => 425,  577 => 261,  569 => 216,  557 => 368,  502 => 267,  497 => 194,  445 => 95,  729 => 159,  684 => 261,  676 => 297,  669 => 254,  660 => 145,  647 => 243,  643 => 244,  601 => 382,  570 => 211,  522 => 200,  501 => 179,  296 => 149,  374 => 137,  631 => 239,  616 => 281,  608 => 137,  605 => 16,  596 => 15,  574 => 297,  561 => 209,  527 => 147,  433 => 93,  388 => 142,  426 => 177,  383 => 146,  461 => 167,  370 => 113,  395 => 144,  294 => 76,  223 => 132,  220 => 59,  492 => 263,  468 => 121,  444 => 200,  410 => 229,  397 => 87,  377 => 144,  262 => 115,  250 => 147,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 2108,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 1383,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 1303,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 1173,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 1041,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 970,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 538,  1126 => 916,  1106 => 900,  1100 => 897,  1026 => 841,  997 => 822,  983 => 816,  975 => 402,  956 => 800,  939 => 786,  902 => 753,  894 => 749,  879 => 373,  757 => 631,  727 => 608,  716 => 605,  670 => 296,  528 => 357,  476 => 123,  435 => 195,  354 => 110,  341 => 278,  192 => 88,  321 => 163,  243 => 143,  793 => 351,  780 => 348,  758 => 177,  700 => 154,  686 => 150,  652 => 274,  638 => 414,  620 => 139,  545 => 218,  523 => 110,  494 => 183,  459 => 99,  438 => 172,  351 => 79,  347 => 282,  402 => 157,  268 => 75,  430 => 237,  411 => 120,  379 => 84,  322 => 92,  315 => 110,  289 => 67,  284 => 128,  255 => 24,  234 => 136,  1133 => 400,  1124 => 534,  1121 => 56,  1116 => 55,  1113 => 403,  1108 => 51,  1103 => 394,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 500,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 329,  996 => 262,  989 => 316,  985 => 315,  981 => 257,  977 => 313,  970 => 91,  966 => 416,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 244,  919 => 290,  917 => 289,  908 => 444,  905 => 378,  896 => 280,  891 => 227,  877 => 270,  862 => 267,  857 => 265,  837 => 347,  832 => 250,  827 => 248,  821 => 247,  803 => 179,  778 => 389,  769 => 165,  765 => 386,  753 => 325,  746 => 175,  743 => 318,  735 => 170,  730 => 218,  720 => 363,  717 => 362,  712 => 156,  691 => 204,  678 => 149,  654 => 144,  587 => 14,  576 => 179,  539 => 241,  517 => 109,  471 => 212,  441 => 199,  437 => 142,  418 => 309,  386 => 295,  373 => 83,  304 => 151,  270 => 123,  265 => 63,  229 => 81,  477 => 138,  455 => 325,  448 => 164,  429 => 159,  407 => 95,  399 => 156,  389 => 174,  375 => 167,  358 => 286,  349 => 118,  335 => 84,  327 => 93,  298 => 84,  280 => 85,  249 => 147,  194 => 65,  142 => 52,  344 => 83,  318 => 135,  306 => 87,  295 => 68,  357 => 119,  300 => 150,  286 => 145,  276 => 87,  269 => 66,  254 => 120,  128 => 34,  237 => 138,  165 => 73,  122 => 42,  798 => 337,  770 => 179,  759 => 112,  748 => 337,  731 => 108,  721 => 308,  718 => 307,  708 => 271,  696 => 147,  617 => 234,  590 => 226,  553 => 246,  550 => 466,  540 => 84,  533 => 82,  500 => 222,  493 => 217,  489 => 343,  482 => 213,  467 => 210,  464 => 120,  458 => 166,  452 => 117,  449 => 174,  415 => 190,  382 => 219,  372 => 215,  361 => 81,  356 => 122,  339 => 77,  302 => 131,  285 => 77,  258 => 64,  123 => 32,  108 => 27,  424 => 156,  394 => 86,  380 => 80,  338 => 155,  319 => 72,  316 => 91,  312 => 109,  290 => 146,  267 => 122,  206 => 51,  110 => 31,  240 => 93,  224 => 60,  219 => 94,  217 => 84,  202 => 52,  186 => 47,  170 => 82,  100 => 24,  67 => 16,  14 => 1,  1096 => 524,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 836,  1013 => 275,  1007 => 274,  1002 => 823,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 402,  940 => 298,  937 => 395,  928 => 452,  926 => 243,  915 => 239,  912 => 236,  903 => 231,  898 => 440,  892 => 229,  889 => 748,  887 => 227,  884 => 374,  876 => 222,  874 => 215,  871 => 368,  863 => 214,  861 => 213,  858 => 212,  850 => 352,  843 => 206,  840 => 406,  815 => 245,  812 => 244,  808 => 199,  804 => 395,  799 => 196,  791 => 193,  785 => 238,  775 => 184,  771 => 183,  754 => 340,  728 => 309,  726 => 169,  723 => 168,  715 => 105,  711 => 152,  709 => 162,  706 => 155,  698 => 208,  694 => 153,  692 => 155,  689 => 254,  681 => 150,  677 => 149,  675 => 148,  663 => 251,  661 => 277,  650 => 246,  646 => 143,  629 => 129,  627 => 21,  625 => 236,  622 => 126,  598 => 309,  592 => 117,  586 => 115,  575 => 214,  566 => 210,  556 => 100,  554 => 289,  541 => 113,  536 => 205,  515 => 352,  511 => 108,  509 => 229,  488 => 126,  486 => 342,  483 => 341,  465 => 100,  463 => 329,  450 => 202,  432 => 314,  419 => 155,  371 => 165,  362 => 288,  353 => 80,  337 => 18,  333 => 122,  309 => 94,  303 => 76,  299 => 130,  291 => 111,  272 => 82,  261 => 156,  253 => 61,  239 => 102,  235 => 63,  213 => 91,  200 => 43,  198 => 110,  159 => 71,  149 => 79,  146 => 39,  131 => 35,  116 => 57,  79 => 18,  74 => 15,  71 => 22,  836 => 320,  817 => 398,  814 => 200,  811 => 317,  805 => 313,  787 => 657,  779 => 169,  776 => 305,  773 => 347,  761 => 296,  751 => 163,  747 => 293,  742 => 336,  739 => 171,  736 => 287,  724 => 214,  705 => 278,  702 => 601,  688 => 113,  680 => 263,  667 => 295,  662 => 146,  656 => 418,  649 => 291,  644 => 97,  641 => 241,  624 => 236,  613 => 280,  607 => 232,  597 => 221,  591 => 131,  584 => 262,  579 => 234,  563 => 213,  559 => 116,  551 => 366,  547 => 114,  537 => 201,  524 => 191,  512 => 351,  507 => 76,  504 => 143,  498 => 142,  485 => 176,  480 => 124,  472 => 255,  466 => 330,  460 => 328,  447 => 201,  442 => 162,  434 => 110,  428 => 29,  422 => 311,  404 => 184,  368 => 164,  364 => 127,  340 => 189,  334 => 130,  330 => 94,  325 => 73,  292 => 163,  287 => 162,  282 => 119,  279 => 98,  273 => 103,  266 => 106,  256 => 71,  252 => 107,  228 => 113,  218 => 81,  201 => 72,  64 => 18,  51 => 9,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 1705,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 737,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 731,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 725,  1559 => 601,  1552 => 720,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 708,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 645,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 624,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 1001,  1257 => 1000,  1254 => 479,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 569,  1196 => 958,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 938,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 388,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 898,  1099 => 393,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 880,  1076 => 359,  1070 => 875,  1057 => 352,  1052 => 504,  1045 => 382,  1040 => 377,  1036 => 283,  1032 => 496,  1028 => 274,  1023 => 393,  1020 => 392,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 821,  979 => 379,  974 => 256,  967 => 373,  962 => 803,  958 => 801,  954 => 253,  950 => 368,  945 => 367,  942 => 460,  938 => 365,  934 => 364,  927 => 361,  923 => 387,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 272,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 410,  841 => 210,  835 => 312,  830 => 249,  826 => 202,  822 => 308,  818 => 246,  813 => 183,  810 => 340,  806 => 180,  802 => 198,  795 => 336,  792 => 239,  789 => 350,  784 => 188,  782 => 187,  777 => 291,  772 => 289,  768 => 345,  763 => 327,  760 => 326,  756 => 230,  752 => 176,  745 => 281,  741 => 280,  738 => 379,  732 => 171,  719 => 279,  714 => 264,  710 => 310,  704 => 267,  699 => 303,  695 => 264,  690 => 263,  687 => 203,  683 => 346,  679 => 298,  672 => 255,  668 => 247,  665 => 201,  658 => 244,  645 => 270,  640 => 142,  634 => 413,  628 => 286,  623 => 238,  619 => 282,  611 => 18,  606 => 279,  603 => 383,  599 => 242,  595 => 132,  583 => 216,  580 => 221,  573 => 260,  560 => 101,  543 => 172,  538 => 361,  534 => 281,  530 => 202,  526 => 279,  521 => 146,  518 => 235,  514 => 183,  510 => 202,  503 => 75,  496 => 345,  490 => 193,  484 => 125,  474 => 336,  470 => 168,  446 => 318,  440 => 114,  436 => 113,  431 => 146,  425 => 193,  416 => 104,  412 => 98,  408 => 185,  403 => 88,  400 => 225,  396 => 299,  392 => 152,  385 => 85,  381 => 170,  367 => 82,  363 => 139,  359 => 92,  355 => 285,  350 => 128,  346 => 156,  343 => 115,  328 => 17,  324 => 164,  313 => 71,  307 => 70,  301 => 69,  288 => 88,  283 => 66,  271 => 64,  257 => 76,  251 => 76,  238 => 92,  233 => 100,  195 => 84,  191 => 106,  187 => 48,  183 => 86,  130 => 36,  88 => 20,  76 => 18,  115 => 30,  95 => 31,  655 => 275,  651 => 232,  648 => 231,  637 => 241,  633 => 130,  621 => 408,  618 => 233,  615 => 232,  604 => 186,  600 => 516,  588 => 305,  585 => 204,  582 => 203,  571 => 118,  567 => 372,  555 => 207,  552 => 190,  549 => 208,  544 => 285,  542 => 207,  535 => 112,  531 => 358,  519 => 189,  516 => 275,  513 => 168,  508 => 145,  506 => 83,  499 => 106,  495 => 141,  491 => 191,  481 => 103,  478 => 172,  475 => 184,  469 => 182,  456 => 136,  451 => 96,  443 => 132,  439 => 94,  427 => 92,  423 => 109,  420 => 192,  409 => 89,  405 => 148,  401 => 147,  391 => 86,  387 => 129,  384 => 132,  378 => 138,  365 => 289,  360 => 158,  348 => 21,  336 => 188,  332 => 107,  329 => 152,  323 => 81,  310 => 133,  305 => 165,  277 => 65,  274 => 151,  263 => 147,  259 => 62,  247 => 60,  244 => 94,  241 => 59,  222 => 85,  210 => 90,  207 => 80,  204 => 74,  184 => 66,  181 => 47,  167 => 80,  157 => 43,  96 => 33,  421 => 91,  417 => 137,  414 => 152,  406 => 171,  398 => 129,  393 => 175,  390 => 221,  376 => 110,  369 => 94,  366 => 91,  352 => 198,  345 => 78,  342 => 109,  331 => 74,  326 => 102,  320 => 149,  317 => 90,  314 => 147,  311 => 78,  308 => 144,  297 => 137,  293 => 89,  281 => 107,  278 => 125,  275 => 34,  264 => 31,  260 => 73,  248 => 144,  245 => 104,  242 => 118,  231 => 57,  227 => 87,  215 => 92,  212 => 82,  209 => 74,  197 => 89,  177 => 79,  171 => 77,  161 => 43,  132 => 95,  121 => 29,  105 => 26,  99 => 27,  81 => 20,  77 => 28,  180 => 80,  176 => 98,  156 => 70,  143 => 38,  139 => 40,  118 => 53,  189 => 48,  185 => 81,  173 => 63,  166 => 40,  152 => 40,  174 => 78,  164 => 94,  154 => 56,  150 => 101,  137 => 36,  133 => 39,  127 => 38,  107 => 28,  102 => 34,  83 => 29,  78 => 27,  53 => 14,  23 => 6,  42 => 11,  138 => 51,  134 => 31,  109 => 31,  103 => 27,  97 => 28,  94 => 22,  84 => 30,  75 => 16,  69 => 17,  66 => 23,  54 => 10,  44 => 10,  230 => 18,  226 => 97,  203 => 128,  193 => 49,  188 => 67,  182 => 47,  178 => 115,  168 => 61,  163 => 59,  160 => 38,  155 => 41,  148 => 53,  145 => 41,  140 => 97,  136 => 96,  125 => 43,  120 => 27,  113 => 32,  101 => 32,  92 => 27,  89 => 30,  85 => 19,  73 => 26,  62 => 19,  59 => 18,  56 => 9,  41 => 5,  126 => 43,  119 => 39,  111 => 35,  106 => 30,  98 => 25,  93 => 28,  86 => 22,  70 => 17,  60 => 7,  28 => 8,  36 => 10,  114 => 32,  104 => 33,  91 => 28,  80 => 30,  63 => 18,  58 => 11,  40 => 10,  34 => 3,  45 => 7,  61 => 12,  55 => 10,  48 => 14,  39 => 10,  35 => 9,  31 => 3,  26 => 2,  21 => 1,  46 => 13,  29 => 2,  57 => 10,  50 => 8,  47 => 6,  38 => 3,  33 => 3,  49 => 11,  32 => 9,  246 => 97,  236 => 101,  232 => 135,  225 => 56,  221 => 78,  216 => 75,  214 => 122,  211 => 52,  208 => 129,  205 => 51,  199 => 85,  196 => 70,  190 => 82,  179 => 68,  175 => 46,  172 => 48,  169 => 45,  162 => 72,  158 => 57,  153 => 102,  151 => 42,  147 => 100,  144 => 37,  141 => 37,  135 => 70,  129 => 94,  124 => 37,  117 => 36,  112 => 27,  90 => 29,  87 => 26,  82 => 20,  72 => 20,  68 => 13,  65 => 15,  52 => 15,  43 => 6,  37 => 4,  30 => 6,  27 => 3,  25 => 7,  24 => 4,  22 => 2,  19 => 1,);
    }
}
