<?php

/* AgentBundle:Person:view-property-col.html.twig */
class __TwigTemplate_cdcc6daa22d466f0d78cb9317d187da7 extends \Application\DeskPRO\Twig\Template
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
        // line 2
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "profile", "agent", "above", $_person_api_);
        echo "

";
        // line 4
        if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
        if ($this->getAttribute($_person_, "is_agent")) {
            // line 5
            echo "<div class=\"profile-box-container highlighted\">
\t<header>
\t\t";
            // line 7
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($this->getAttribute($_app_, "user"), "can_admin")) {
                echo "<a class=\"edit-gear\" href=\"";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
                echo "/admin/agents/";
                if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_person_, "id"), "html", null, true);
                echo "/edit\" target=\"_blank\"></a>";
            }
            // line 8
            echo "\t\t";
            $context["agentTabs"] = $this->env->getExtension('deskpro_templating')->getWidgetsRaw("profile", "agent", "tab");
            // line 9
            echo "\t\t";
            if (isset($context["agentTabs"])) { $_agentTabs_ = $context["agentTabs"]; } else { $_agentTabs_ = null; }
            if (twig_test_empty($_agentTabs_)) {
                // line 10
                echo "\t\t\t<h4 class=\"agent-icon\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.agent");
                echo "</h4>
\t\t";
            } else {
                // line 12
                echo "\t\t\t<nav data-element-handler=\"DeskPRO.ElementHandler.SimpleTabs\">
\t\t\t\t<ul>
\t\t\t\t\t<li class=\"on\" data-tab-for=\"#";
                // line 14
                if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
                echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
                echo "_agent_tab\">
\t\t\t\t\t\t<span class=\"agent-icon\">";
                // line 15
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.agent");
                echo "</span>
\t\t\t\t\t</li>
\t\t\t\t\t";
                // line 17
                if (isset($context["agentTabs"])) { $_agentTabs_ = $context["agentTabs"]; } else { $_agentTabs_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($_agentTabs_);
                foreach ($context['_seq'] as $context["_key"] => $context["widget"]) {
                    // line 18
                    echo "\t\t\t\t\t\t<li data-tab-for=\"#";
                    if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
                    if (isset($context["widget"])) { $_widget_ = $context["widget"]; } else { $_widget_ = null; }
                    echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getWidgetHtmlId($_baseId_, $_widget_), "html", null, true);
                    echo "\">";
                    if (isset($context["widget"])) { $_widget_ = $context["widget"]; } else { $_widget_ = null; }
                    echo $this->getAttribute($_widget_, "title");
                    echo "</li>
\t\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['widget'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 20
                echo "\t\t\t\t</ul>
\t\t\t</nav>
\t\t";
            }
            // line 23
            echo "\t</header>
\t<section id=\"";
            // line 24
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_agent_tab\">
\t\t<p>";
            // line 25
            if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.people.user_is_agent", array("name" => $this->getAttribute($_person_, "display_name")));
            echo "</p>
\t\t";
            // line 26
            if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
            if ($this->getAttribute($this->getAttribute($_person_, "Agent"), "getTeams", array(), "method")) {
                // line 27
                echo "\t\t\t<p>";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.people.member_of_teams");
                echo "</p>
\t\t\t<ul class=\"standard\">
\t\t\t\t";
                // line 29
                if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_person_, "Agent"), "getTeams", array(), "method"));
                foreach ($context['_seq'] as $context["_key"] => $context["team"]) {
                    // line 30
                    echo "\t\t\t\t\t<li>";
                    if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_team_, "name"), "html", null, true);
                    echo "</li>
\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['team'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 32
                echo "\t\t\t</ul>
\t\t";
            }
            // line 34
            echo "\t</section>
\t";
            // line 35
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getWidgetTabsBody($_baseId_, "profile", "agent", "section", $_person_api_);
            echo "
</div>
";
        }
        // line 38
        echo "
";
        // line 39
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "profile", "agent", "below", $_person_api_);
        echo "
";
        // line 41
        echo "
";
        // line 43
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "profile", "contact", "above", $_person_api_);
        echo "

<div class=\"profile-box-container summary\">
\t<header>
\t\t";
        // line 47
        if (isset($context["perms"])) { $_perms_ = $context["perms"]; } else { $_perms_ = null; }
        if ($this->getAttribute($_perms_, "edit")) {
            echo "<span class=\"edit-gear contact-edit\"></span>";
        }
        // line 48
        echo "\t\t";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgetTabsHeader($_baseId_, "profile", "contact", array(($_baseId_ . "_contact_body") => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.people.contact_info")));
        // line 50
        echo "
\t</header>
\t<section id=\"";
        // line 52
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_contact_body\">
\t\t<div class=\"table-content contact-list contact-list-wrapper\" id=\"";
        // line 53
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_contact_display\">
\t\t\t";
        // line 54
        $this->env->loadTemplate("AgentBundle:Person:view-contact-display.html.twig")->display($context);
        // line 55
        echo "\t\t</div>
\t</section>
\t";
        // line 57
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgetTabsBody($_baseId_, "profile", "contact", "section", $_person_api_);
        echo "
</div>
";
        // line 60
        echo "
<div id=\"";
        // line 61
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_contact_outside\"></div>

";
        // line 63
        if (isset($context["validating_emails"])) { $_validating_emails_ = $context["validating_emails"]; } else { $_validating_emails_ = null; }
        if ($_validating_emails_) {
            // line 64
            echo "\t<div class=\"profile-box-container summary\">
\t\t<header>
\t\t\t<h4>";
            // line 66
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.people.email_awaiting_validation");
            echo "</h4>
\t\t</header>
\t\t<section>
\t\t\t<div class=\"table-content contact-list contact-list-wrapper\">
\t\t\t\t<ul class=\"contact-data-list\" id=\"";
            // line 70
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_validating_emails\">
\t\t\t\t\t";
            // line 71
            if (isset($context["validating_emails"])) { $_validating_emails_ = $context["validating_emails"]; } else { $_validating_emails_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_validating_emails_);
            foreach ($context['_seq'] as $context["_key"] => $context["vemail"]) {
                // line 72
                echo "\t\t\t\t\t\t<li class=\"email\">
\t\t\t\t\t\t\t";
                // line 73
                if (isset($context["vemail"])) { $_vemail_ = $context["vemail"]; } else { $_vemail_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_vemail_, "email"), "html", null, true);
                echo "
\t\t\t\t\t\t\t<span class=\"set-primary validate-trigger\" data-token=\"";
                // line 74
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->securityToken("validate_email"), "html", null, true);
                echo "\" data-email-id=\"";
                if (isset($context["vemail"])) { $_vemail_ = $context["vemail"]; } else { $_vemail_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_vemail_, "id"), "html", null, true);
                echo "\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.validate");
                echo "</span>
\t\t\t\t\t\t</li>
\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['vemail'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 77
            echo "\t\t\t\t</ul>
\t\t\t</div>
\t\t</section>
\t</div>
";
        }
        // line 82
        echo "
";
        // line 83
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "profile", "contact", "below", $_person_api_);
        echo "

";
        // line 86
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "profile", "organization", "above", $_person_api_);
        echo "

";
        // line 88
        if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
        if (isset($context["perms"])) { $_perms_ = $context["perms"]; } else { $_perms_ = null; }
        if (($this->getAttribute($_person_, "organization") || $this->getAttribute($_perms_, "edit"))) {
            // line 89
            echo "\t<div class=\"profile-box-container summary\" id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_org_box\">
\t\t<header id=\"";
            // line 90
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_org_display_header\">
\t\t\t";
            // line 91
            if (isset($context["perms"])) { $_perms_ = $context["perms"]; } else { $_perms_ = null; }
            if ($this->getAttribute($_perms_, "edit")) {
                // line 92
                echo "\t\t\t\t<div class=\"controls\">
\t\t\t\t\t<span class=\"edit-gear org-edit-trigger\"></span>
\t\t\t\t\t<div class=\"is-loading\">";
                // line 94
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.saving");
                echo "</div>
\t\t\t\t\t<button class=\"cancel\" style=\"display: none\">";
                // line 95
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.cancel");
                echo "</button>
\t\t\t\t\t<button class=\"save\" id=\"";
                // line 96
                if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
                echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
                echo "_org_edit_save\" style=\"display: none\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.save");
                echo "</button>
\t\t\t\t\t<button class=\"remove-org\" id=\"";
                // line 97
                if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
                echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
                echo "_org_edit_remove_org\" style=\"display: none\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.remove");
                echo "</button>
\t\t\t\t\t<button class=\"is-loading\">";
                // line 98
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.saving");
                echo "</button>
\t\t\t\t\t<button class=\"saved\" style=\"display: none\">";
                // line 99
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.saved");
                echo "</button>
\t\t\t\t</div>
\t\t\t";
            }
            // line 102
            echo "\t\t\t";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getWidgetTabsHeader($_baseId_, "profile", "organization", array(($_baseId_ . "_organization_body") => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.organization")));
            // line 104
            echo "
\t\t</header>
\t\t<section id=\"";
            // line 106
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_organization_body\">
\t\t\t<div class=\"org-info\" id=\"";
            // line 107
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_org_display_wrap\">
\t\t\t\t";
            // line 108
            if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
            if ($this->getAttribute($_person_, "organization")) {
                // line 109
                echo "\t\t\t\t\t";
                $this->env->loadTemplate("AgentBundle:Person:view-org-info.html.twig")->display($context);
                // line 110
                echo "\t\t\t\t";
            }
            // line 111
            echo "\t\t\t</div>
\t\t\t<div class=\"org-edit\" style=\"display: none\" id=\"";
            // line 112
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_org_edit_wrap\">
\t\t\t\t<div
\t\t\t\t\tclass=\"org-input\"
\t\t\t\t\tid=\"";
            // line 115
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_org_searchbox\"
\t\t\t\t\tdata-element-handler=\"DeskPRO.Agent.ElementHandler.OrgSearchBox\"
\t\t\t\t\tdata-search-url=\"";
            // line 117
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_orgsearch_quicknamesearch", array("format" => "json", "limit" => 10, "start_with" => "a")), "html", null, true);
            echo "\"
\t\t\t\t\tdata-highlight-term=\"1\"
\t\t\t\t\tdata-touch-focus=\"1\"
\t\t\t\t\tdata-search-param=\"term\"
\t\t\t\t\tdata-position-bound=\"@parent(.org-input)\"
\t\t\t\t>
\t\t\t\t\t<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\"><tr><td style=\"white-space: nowrap;\" valign=\"middle\">
\t\t\t\t\t\t<label>";
            // line 124
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.organization");
            echo ":</label>
\t\t\t\t\t</td><td width=\"100%\">
\t\t\t\t\t\t<input type=\"text\" class=\"org-name\" placeholder=\"";
            // line 126
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.enter_organization_name");
            echo "\" value=\"";
            if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_person_, "organization"), "name"), "html", null, true);
            echo "\" />
\t\t\t\t\t</td></tr></table>

\t\t\t\t\t<input type=\"hidden\" class=\"org-id\" value=\"";
            // line 129
            if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_person_, "organization", array(), "any", false, true), "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_person_, "organization", array(), "any", false, true), "id"), 0)) : (0)), "html", null, true);
            echo "\" />
\t\t\t\t\t<script type=\"text/x-deskpro-tmpl\" class=\"user-row-tpl\">
\t\t\t\t\t\t<li><a>
\t\t\t\t\t\t\t<span class=\"org-name\"></span>
\t\t\t\t\t\t\t<div class=\"bound-fade\"></div>
\t\t\t\t\t\t</a></li>
\t\t\t\t\t</script>
\t\t\t\t\t<div class=\"person-search-box org\" style=\"display: none\">
\t\t\t\t\t\t<section>
\t\t\t\t\t\t\t<ul class=\"results-list\">

\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t\t";
            // line 141
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($this->getAttribute($_app_, "user"), "hasPerm", array(0 => "agent_org.create"), "method")) {
                // line 142
                echo "\t\t\t\t\t\t\t\t<footer>
\t\t\t\t\t\t\t\t\t<span class=\"create-org\">";
                // line 143
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.create_a_new_organization");
                echo "</span>
\t\t\t\t\t\t\t\t</footer>
\t\t\t\t\t\t\t";
            }
            // line 146
            echo "\t\t\t\t\t\t</section>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t<div class=\"extra-input\" ";
            // line 149
            if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
            if ((!$this->getAttribute($_person_, "organization"))) {
                echo "style=\"display: none\"";
            }
            echo ">
\t\t\t\t\t<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\"><tr><td style=\"white-space: nowrap;\" valign=\"middle\">
\t\t\t\t\t\t<label>";
            // line 151
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.position");
            echo ":</label>
\t\t\t\t\t</td><td width=\"100%\">
\t\t\t\t\t\t<input type=\"text\" class=\"org-pos-set\" placeholder=\"";
            // line 153
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.set_a_position");
            echo "\" value=\"";
            if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_person_, "organization_position"), "html", null, true);
            echo "\" />
\t\t\t\t\t</td></tr></table>
\t\t\t\t</div>
\t\t\t\t<div class=\"extra-input\" ";
            // line 156
            if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
            if ((!$this->getAttribute($_person_, "organization"))) {
                echo "style=\"display: none\"";
            }
            echo ">
\t\t\t\t\t<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\"><tr><td style=\"white-space: nowrap;\" valign=\"middle\">
\t\t\t\t\t\t<label>";
            // line 158
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.people.manager");
            echo ":</label>
\t\t\t\t\t</td><td width=\"100%\">
\t\t\t\t\t\t<input type=\"checkbox\" class=\"org-manager-set\" value=\"1\" ";
            // line 160
            if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
            if ($this->getAttribute($_person_, "organization_manager")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t</td></tr></table>
\t\t\t\t</div>
\t\t\t</div>
\t\t</section>
\t\t";
            // line 165
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getWidgetTabsBody($_baseId_, "profile", "organization", "section", $_person_api_);
            echo "
\t</div>
";
        }
        // line 168
        echo "
";
        // line 169
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "profile", "organization", "below", $_person_api_);
        echo "
";
        // line 171
        echo "
";
        // line 173
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "profile", "slas", "above", $_person_api_);
        echo "

";
        // line 175
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (isset($context["perms"])) { $_perms_ = $context["perms"]; } else { $_perms_ = null; }
        if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
        if (($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "Sla"), "method"), "getPersonOrgAssociableSlas", array(), "method") && ($this->getAttribute($_perms_, "edit") || twig_length_filter($this->env, $this->getAttribute($_person_, "slas"))))) {
            // line 176
            echo "\t<div class=\"profile-box-container summary\" id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_sla_box\">
\t\t<header>
\t\t\t";
            // line 178
            if (isset($context["perms"])) { $_perms_ = $context["perms"]; } else { $_perms_ = null; }
            if ($this->getAttribute($_perms_, "edit")) {
                // line 179
                echo "\t\t\t<div class=\"controls\">
\t\t\t\t<span class=\"edit-gear edit-trigger\"></span>
\t\t\t\t<div class=\"is-loading\">";
                // line 181
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.saving");
                echo "</div>
\t\t\t\t<button class=\"cancel cancel-trigger\" style=\"display: none\">";
                // line 182
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.cancel");
                echo "</button>
\t\t\t\t<button class=\"save save-trigger\" style=\"display: none\">";
                // line 183
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.save");
                echo "</button>
\t\t\t</div>
\t\t\t";
            }
            // line 186
            echo "\t\t\t";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getWidgetTabsHeader($_baseId_, "profile", "slas", array(($_baseId_ . "_sla_body") => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.slas")));
            // line 188
            echo "
\t\t</header>
\t\t<section id=\"";
            // line 190
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_sla_body\">
\t\t\t<div id=\"";
            // line 191
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_sla_display_box\" class=\"contact-list\">
\t\t\t\t<ul>
\t\t\t\t";
            // line 193
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "Sla"), "method"), "getPersonOrgAssociableSlas", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["sla"]) {
                // line 194
                echo "\t\t\t\t\t<li class=\"sla-row sla-row-";
                if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_sla_, "id"), "html", null, true);
                echo "\" ";
                if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
                if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
                if ((!$this->getAttribute($_person_, "hasSla", array(0 => $_sla_), "method"))) {
                    echo "style=\"display: none\"";
                }
                echo ">";
                if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_sla_, "title"), "html", null, true);
                echo "</li>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['sla'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 196
            echo "\t\t\t\t<li class=\"no-slas\" style=\"padding-left: 0; ";
            if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
            if (twig_length_filter($this->env, $this->getAttribute($_person_, "slas"))) {
                echo "display: none;";
            }
            echo "}\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.people.no_slas");
            echo "</li>
\t\t\t\t</ul>
\t\t\t</div>
\t\t\t<div id=\"";
            // line 199
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_sla_edit_box\" class=\"contact-list\" style=\"display: none;\">
\t\t\t\t<ul>
\t\t\t\t";
            // line 201
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "Sla"), "method"), "getPersonOrgAssociableSlas", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["sla"]) {
                // line 202
                echo "\t\t\t\t\t<li class=\"sla-row\">
\t\t\t\t\t\t<label><input type=\"checkbox\" value=\"";
                // line 203
                if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_sla_, "id"), "html", null, true);
                echo "\" name=\"sla_ids[]\" class=\"sla-check sla-check-";
                if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_sla_, "id"), "html", null, true);
                echo "\" ";
                if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
                if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
                if ($this->getAttribute($_person_, "hasSla", array(0 => $_sla_), "method")) {
                    echo "checked=\"checked\"";
                }
                echo " /> ";
                if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_sla_, "title"), "html", null, true);
                echo "</label>
\t\t\t\t\t</li>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['sla'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 206
            echo "\t\t\t\t</ul>
\t\t\t</div>
\t\t</section>
\t\t";
            // line 209
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getWidgetTabsBody($_baseId_, "profile", "slas", "section", $_person_api_);
            echo "
\t</div>
";
        }
        // line 212
        echo "
";
        // line 213
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "profile", "slas", "below", $_person_api_);
        echo "
";
        // line 215
        echo "
";
        // line 217
        if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
        if (twig_length_filter($this->env, $this->getAttribute($_person_, "usersource_assoc"))) {
            // line 218
            echo "\t<div class=\"profile-box-container summary\" id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_us_box\">
\t\t<header>
\t\t\t<h4>";
            // line 220
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.usersources");
            echo "</h4>
\t\t</header>
\t\t<section id=\"";
            // line 222
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_us_body\">
\t\t\t<div id=\"";
            // line 223
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_us_display_box\" class=\"contact-list\">
\t\t\t\t<ul>
\t\t\t\t\t";
            // line 225
            if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_person_, "usersource_assoc"));
            foreach ($context['_seq'] as $context["_key"] => $context["as"]) {
                // line 226
                echo "\t\t\t\t\t\t<li style=\"padding-left: 0;\">";
                if (isset($context["as"])) { $_as_ = $context["as"]; } else { $_as_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_as_, "usersource"), "title"), "html", null, true);
                echo ": ";
                if (isset($context["as"])) { $_as_ = $context["as"]; } else { $_as_ = null; }
                echo twig_escape_filter($this->env, (($this->getAttribute($_as_, "identity_friendly", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_as_, "identity_friendly"), $this->getAttribute($_as_, "identity"))) : ($this->getAttribute($_as_, "identity"))), "html", null, true);
                echo "</li>
\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['as'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 228
            echo "\t\t\t\t</ul>
\t\t\t</div>
\t\t</section>
\t</div>
";
        }
        // line 233
        echo "
";
        // line 235
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "profile", "usergroups", "above", $_person_api_);
        echo "

";
        // line 237
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "usergroups"), "getUsergroupNames", array(), "method")) {
            // line 238
            echo "\t<div class=\"profile-box-container summary ";
            if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
            if ((twig_length_filter($this->env, $this->getAttribute($_person_, "getUsergroupIds", array(), "method")) < 2)) {
                echo "no-section";
            }
            echo "\" id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_ug_box\">
\t\t<header>
\t\t\t";
            // line 240
            if (isset($context["perms"])) { $_perms_ = $context["perms"]; } else { $_perms_ = null; }
            if ($this->getAttribute($_perms_, "edit")) {
                // line 241
                echo "\t\t\t<div class=\"controls\">
\t\t\t\t<span class=\"edit-gear edit-trigger\"></span>
\t\t\t\t<div class=\"is-loading\">";
                // line 243
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.saving");
                echo "</div>
\t\t\t\t<button class=\"cancel cancel-trigger\" style=\"display: none\">";
                // line 244
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.cancel");
                echo "</button>
\t\t\t\t<button class=\"save save-trigger\" style=\"display: none\">";
                // line 245
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.save");
                echo "</button>
\t\t\t</div>
\t\t\t";
            }
            // line 248
            echo "\t\t\t";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getWidgetTabsHeader($_baseId_, "profile", "usergroups", array(($_baseId_ . "_usergroups_body") => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.usergroups")));
            // line 250
            echo "
\t\t</header>
\t\t<section id=\"";
            // line 252
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_usergroups_body\">
\t\t\t<div id=\"";
            // line 253
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_ug_display_box\" class=\"contact-list\">
\t\t\t\t<ul>
\t\t\t\t\t<li style=\"padding-left: 0;\">";
            // line 255
            if (isset($context["reg_group"])) { $_reg_group_ = $context["reg_group"]; } else { $_reg_group_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_reg_group_, "title"), "html", null, true);
            echo "</li>
\t\t\t\t\t";
            // line 256
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "usergroups"), "getUsergroupNames", array(), "method"));
            foreach ($context['_seq'] as $context["id"] => $context["name"]) {
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                if ((($_id_ != 2) && ($_id_ != 1))) {
                    // line 257
                    echo "\t\t\t\t\t\t<li class=\"ug-row ug-row-";
                    if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                    echo twig_escape_filter($this->env, $_id_, "html", null, true);
                    echo "\" ";
                    if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
                    if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                    if ((!$this->getAttribute($_person_, "isMemberOfUsergroup", array(0 => $_id_), "method"))) {
                        echo "style=\"display: none\"";
                    }
                    echo ">";
                    if (isset($context["name"])) { $_name_ = $context["name"]; } else { $_name_ = null; }
                    echo twig_escape_filter($this->env, $_name_, "html", null, true);
                    echo "</li>
\t\t\t\t\t";
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['id'], $context['name'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 259
            echo "\t\t\t\t</ul>
\t\t\t</div>
\t\t\t<div id=\"";
            // line 261
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_ug_edit_box\" class=\"contact-list\" style=\"display: none;\">
\t\t\t\t<ul>
\t\t\t\t\t<div>
\t\t\t\t\t\t<label><input type=\"checkbox\" checked=\"checked\" onclick=\"this.checked=true;\" /> ";
            // line 264
            if (isset($context["reg_group"])) { $_reg_group_ = $context["reg_group"]; } else { $_reg_group_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_reg_group_, "title"), "html", null, true);
            echo "</label>
\t\t\t\t\t</div>
\t\t\t\t\t";
            // line 266
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "usergroups"), "getUsergroupNames", array(), "method"));
            foreach ($context['_seq'] as $context["id"] => $context["name"]) {
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                if ((($_id_ != 2) && ($_id_ != 1))) {
                    // line 267
                    echo "\t\t\t\t\t\t<div>
\t\t\t\t\t\t\t<label><input type=\"checkbox\" value=\"";
                    // line 268
                    if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                    echo twig_escape_filter($this->env, $_id_, "html", null, true);
                    echo "\" name=\"usergroup_ids[]\" class=\"ug-check ug-check-";
                    if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                    echo twig_escape_filter($this->env, $_id_, "html", null, true);
                    echo "\" ";
                    if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
                    if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                    if ($this->getAttribute($_person_, "isMemberOfUsergroup", array(0 => $_id_), "method")) {
                        echo "checked=\"checked\"";
                    }
                    echo " /> ";
                    if (isset($context["name"])) { $_name_ = $context["name"]; } else { $_name_ = null; }
                    echo twig_escape_filter($this->env, $_name_, "html", null, true);
                    echo "</label>
\t\t\t\t\t\t</div>
\t\t\t\t\t";
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['id'], $context['name'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 271
            echo "\t\t\t\t</ul>
\t\t\t</div>
\t\t</section>
\t\t";
            // line 274
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getWidgetTabsBody($_baseId_, "profile", "usergroups", "section", $_person_api_);
            echo "
\t</div>
";
        }
        // line 277
        echo "
";
        // line 278
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "profile", "usergroups", "below", $_person_api_);
        echo "
";
    }

    public function getTemplateName()
    {
        return "AgentBundle:Person:view-property-col.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  612 => 203,  1394 => 446,  1385 => 442,  1382 => 441,  1369 => 433,  1363 => 432,  1360 => 431,  1350 => 429,  1305 => 412,  1300 => 409,  1281 => 404,  1277 => 403,  1265 => 398,  1255 => 396,  1247 => 393,  1234 => 387,  1202 => 375,  1199 => 374,  1187 => 372,  1162 => 365,  1136 => 355,  1128 => 352,  1122 => 350,  1069 => 332,  968 => 293,  846 => 250,  1183 => 449,  1132 => 354,  1097 => 341,  957 => 394,  907 => 277,  875 => 263,  653 => 236,  1329 => 405,  1309 => 401,  1303 => 399,  1297 => 398,  1292 => 397,  1280 => 394,  1249 => 391,  1237 => 390,  1205 => 409,  1200 => 408,  1194 => 407,  1178 => 380,  1175 => 447,  1170 => 375,  1135 => 362,  1105 => 428,  1078 => 335,  1068 => 325,  1048 => 417,  961 => 294,  922 => 280,  750 => 245,  842 => 263,  1038 => 319,  904 => 322,  882 => 301,  831 => 267,  860 => 314,  790 => 284,  733 => 296,  707 => 206,  744 => 220,  873 => 278,  824 => 266,  762 => 230,  713 => 235,  578 => 158,  2739 => 1153,  2735 => 1152,  2732 => 1151,  2725 => 1146,  2712 => 1144,  2707 => 1143,  2692 => 1131,  2688 => 1130,  2683 => 1127,  2680 => 1126,  2677 => 1125,  2671 => 1121,  2658 => 1119,  2653 => 1118,  2645 => 1113,  2641 => 1112,  2636 => 1109,  2632 => 1108,  2629 => 1107,  2626 => 1106,  2606 => 1089,  2602 => 1088,  2598 => 1087,  2594 => 1086,  2590 => 1085,  2586 => 1084,  2572 => 1073,  2568 => 1072,  2564 => 1071,  2560 => 1070,  2556 => 1069,  2552 => 1068,  2537 => 1056,  2533 => 1055,  2529 => 1054,  2525 => 1053,  2521 => 1052,  2503 => 1040,  2499 => 1039,  2495 => 1038,  2491 => 1037,  2487 => 1036,  2483 => 1035,  2469 => 1024,  2465 => 1023,  2461 => 1022,  2457 => 1021,  2453 => 1020,  2449 => 1019,  2426 => 999,  2422 => 998,  2418 => 997,  2414 => 996,  2410 => 995,  2406 => 994,  2392 => 983,  2388 => 982,  2384 => 981,  2380 => 980,  2376 => 979,  2372 => 978,  2358 => 967,  2354 => 966,  2350 => 965,  2346 => 964,  2342 => 963,  2338 => 962,  2324 => 951,  2320 => 950,  2316 => 949,  2312 => 948,  2308 => 947,  2304 => 946,  2295 => 939,  2282 => 937,  2277 => 936,  2269 => 931,  2265 => 930,  2260 => 927,  2256 => 926,  2253 => 925,  2230 => 901,  2220 => 899,  2216 => 898,  2211 => 896,  2208 => 895,  2198 => 893,  2194 => 892,  2189 => 890,  2184 => 889,  2177 => 885,  2173 => 884,  2164 => 877,  2151 => 875,  2146 => 874,  2143 => 873,  2141 => 864,  2134 => 860,  2130 => 859,  2122 => 853,  2120 => 852,  2113 => 848,  2105 => 846,  2096 => 839,  2083 => 837,  2078 => 836,  2073 => 834,  2067 => 830,  2062 => 828,  2058 => 827,  2054 => 826,  2050 => 825,  2045 => 824,  2038 => 822,  2034 => 821,  2022 => 814,  2009 => 812,  1999 => 809,  1993 => 805,  1988 => 803,  1984 => 802,  1980 => 801,  1976 => 800,  1971 => 799,  1968 => 798,  1964 => 797,  1960 => 796,  1954 => 793,  1946 => 788,  1942 => 787,  1938 => 786,  1932 => 782,  1927 => 780,  1919 => 778,  1915 => 777,  1911 => 776,  1906 => 775,  1903 => 774,  1899 => 773,  1895 => 772,  1886 => 768,  1883 => 764,  1871 => 759,  1866 => 756,  1863 => 755,  1842 => 752,  1825 => 749,  1818 => 748,  1815 => 747,  1800 => 743,  1793 => 741,  1772 => 729,  1761 => 721,  1757 => 720,  1751 => 717,  1740 => 709,  1719 => 697,  1715 => 696,  1686 => 673,  1676 => 669,  1665 => 661,  1661 => 660,  1645 => 651,  1638 => 649,  1630 => 645,  1625 => 642,  1622 => 641,  1598 => 637,  1577 => 634,  1558 => 629,  1549 => 627,  1546 => 626,  1535 => 618,  1531 => 617,  1514 => 606,  1510 => 605,  1504 => 602,  1493 => 594,  1483 => 590,  1464 => 580,  1458 => 577,  1443 => 568,  1437 => 565,  1426 => 557,  1405 => 545,  1401 => 544,  1395 => 541,  1390 => 538,  1381 => 533,  1373 => 531,  1348 => 521,  1343 => 520,  1335 => 515,  1321 => 510,  1318 => 418,  1299 => 503,  1294 => 407,  1282 => 496,  1269 => 491,  1260 => 397,  1240 => 478,  1221 => 381,  1216 => 378,  1210 => 410,  1206 => 473,  1193 => 467,  1189 => 466,  1155 => 439,  1150 => 447,  1022 => 312,  1006 => 404,  988 => 398,  969 => 392,  965 => 391,  921 => 370,  878 => 275,  866 => 349,  854 => 254,  819 => 279,  796 => 286,  1713 => 683,  1700 => 681,  1685 => 674,  1662 => 670,  1643 => 669,  1640 => 668,  1631 => 663,  1618 => 661,  1613 => 660,  1608 => 639,  1605 => 656,  1602 => 655,  1589 => 645,  1586 => 644,  1583 => 643,  1554 => 613,  1547 => 608,  1521 => 602,  1508 => 600,  1499 => 598,  1491 => 593,  1482 => 587,  1472 => 582,  1468 => 581,  1465 => 579,  1457 => 573,  1445 => 566,  1422 => 556,  1416 => 553,  1410 => 548,  1397 => 546,  1392 => 545,  1386 => 541,  1377 => 532,  1370 => 530,  1357 => 528,  1352 => 527,  1341 => 428,  1314 => 510,  1311 => 509,  1275 => 493,  1248 => 484,  1238 => 478,  1225 => 476,  1220 => 412,  1209 => 466,  1185 => 385,  1182 => 370,  1159 => 367,  1154 => 449,  1130 => 438,  1125 => 437,  1101 => 426,  1074 => 432,  1056 => 326,  1046 => 323,  1043 => 404,  1030 => 397,  1027 => 412,  947 => 361,  925 => 352,  913 => 324,  893 => 338,  881 => 267,  847 => 343,  829 => 336,  825 => 259,  1083 => 434,  995 => 300,  984 => 350,  963 => 292,  941 => 324,  851 => 367,  682 => 270,  1365 => 549,  1359 => 545,  1349 => 539,  1336 => 427,  1331 => 424,  1323 => 420,  1319 => 530,  1312 => 402,  1284 => 519,  1272 => 401,  1268 => 509,  1261 => 506,  1251 => 485,  1245 => 483,  1231 => 496,  1207 => 376,  1197 => 491,  1180 => 484,  1173 => 457,  1169 => 455,  1157 => 363,  1147 => 438,  1109 => 440,  1065 => 440,  1059 => 423,  1047 => 428,  1044 => 424,  1033 => 317,  1009 => 357,  991 => 351,  987 => 404,  973 => 294,  931 => 355,  924 => 282,  911 => 298,  906 => 81,  885 => 276,  872 => 317,  855 => 72,  749 => 264,  701 => 239,  594 => 109,  1163 => 440,  1143 => 440,  1087 => 420,  1077 => 433,  1051 => 325,  1037 => 480,  1010 => 405,  999 => 407,  932 => 326,  899 => 306,  895 => 404,  933 => 387,  914 => 133,  909 => 323,  833 => 284,  783 => 306,  755 => 248,  666 => 263,  453 => 158,  639 => 209,  568 => 176,  520 => 110,  657 => 216,  572 => 201,  609 => 202,  20 => 1,  659 => 217,  562 => 193,  548 => 185,  558 => 197,  479 => 145,  589 => 211,  457 => 153,  413 => 140,  953 => 290,  948 => 290,  935 => 394,  929 => 283,  916 => 382,  864 => 365,  844 => 338,  816 => 342,  807 => 259,  801 => 268,  774 => 257,  766 => 229,  737 => 297,  685 => 225,  664 => 225,  635 => 281,  593 => 209,  546 => 227,  532 => 223,  865 => 296,  852 => 241,  838 => 285,  820 => 201,  781 => 327,  764 => 252,  725 => 250,  632 => 268,  602 => 192,  565 => 197,  529 => 181,  505 => 156,  487 => 101,  473 => 87,  1853 => 842,  1849 => 753,  1839 => 751,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 733,  1778 => 732,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 693,  1705 => 781,  1688 => 675,  1681 => 769,  1677 => 768,  1667 => 671,  1656 => 762,  1648 => 655,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 606,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 593,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 540,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 522,  1289 => 406,  1276 => 604,  1263 => 601,  1244 => 480,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 373,  1181 => 561,  1171 => 446,  1158 => 550,  1114 => 428,  1104 => 465,  1072 => 333,  1062 => 7,  1024 => 395,  1014 => 406,  1000 => 328,  990 => 303,  980 => 297,  960 => 466,  918 => 280,  888 => 80,  834 => 268,  673 => 64,  636 => 198,  462 => 92,  454 => 138,  1144 => 358,  1139 => 356,  1131 => 399,  1127 => 434,  1110 => 347,  1092 => 459,  1089 => 426,  1086 => 520,  1084 => 337,  1063 => 387,  1060 => 321,  1055 => 422,  1050 => 373,  1035 => 372,  1019 => 330,  1003 => 309,  959 => 387,  900 => 366,  880 => 276,  870 => 277,  867 => 258,  859 => 294,  848 => 215,  839 => 376,  828 => 302,  823 => 374,  809 => 316,  800 => 241,  797 => 267,  794 => 257,  786 => 283,  740 => 78,  734 => 261,  703 => 228,  693 => 236,  630 => 278,  626 => 19,  614 => 275,  610 => 201,  581 => 206,  564 => 268,  525 => 179,  722 => 226,  697 => 282,  674 => 222,  671 => 221,  577 => 180,  569 => 233,  557 => 229,  502 => 99,  497 => 207,  445 => 151,  729 => 209,  684 => 237,  676 => 65,  669 => 220,  660 => 203,  647 => 212,  643 => 229,  601 => 306,  570 => 165,  522 => 178,  501 => 158,  296 => 157,  374 => 115,  631 => 207,  616 => 283,  608 => 194,  605 => 193,  596 => 188,  574 => 180,  561 => 231,  527 => 165,  433 => 190,  388 => 115,  426 => 143,  383 => 135,  461 => 156,  370 => 155,  395 => 126,  294 => 87,  223 => 94,  220 => 66,  492 => 175,  468 => 162,  444 => 153,  410 => 143,  397 => 134,  377 => 161,  262 => 104,  250 => 86,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 1051,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 680,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 638,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 572,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 526,  1301 => 1037,  1252 => 392,  1243 => 994,  1223 => 978,  1212 => 494,  1167 => 366,  1161 => 933,  1145 => 923,  1140 => 364,  1134 => 538,  1126 => 443,  1106 => 528,  1100 => 897,  1026 => 418,  997 => 822,  983 => 298,  975 => 296,  956 => 318,  939 => 285,  902 => 274,  894 => 364,  879 => 76,  757 => 269,  727 => 295,  716 => 235,  670 => 204,  528 => 187,  476 => 253,  435 => 150,  354 => 127,  341 => 104,  192 => 62,  321 => 114,  243 => 96,  793 => 266,  780 => 256,  758 => 229,  700 => 193,  686 => 238,  652 => 185,  638 => 226,  620 => 216,  545 => 166,  523 => 158,  494 => 169,  459 => 156,  438 => 146,  351 => 126,  347 => 127,  402 => 150,  268 => 95,  430 => 188,  411 => 182,  379 => 134,  322 => 127,  315 => 170,  289 => 130,  284 => 99,  255 => 77,  234 => 126,  1133 => 444,  1124 => 357,  1121 => 430,  1116 => 348,  1113 => 429,  1108 => 376,  1103 => 368,  1098 => 26,  1081 => 425,  1073 => 424,  1067 => 356,  1064 => 330,  1061 => 421,  1053 => 318,  1049 => 419,  1042 => 313,  1039 => 345,  1025 => 314,  1021 => 310,  1015 => 308,  1008 => 461,  996 => 406,  989 => 454,  985 => 315,  981 => 257,  977 => 321,  970 => 320,  966 => 375,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 148,  919 => 314,  917 => 279,  908 => 411,  905 => 310,  896 => 358,  891 => 378,  877 => 334,  862 => 274,  857 => 271,  837 => 261,  832 => 260,  827 => 349,  821 => 66,  803 => 179,  778 => 324,  769 => 253,  765 => 297,  753 => 54,  746 => 244,  743 => 298,  735 => 240,  730 => 251,  720 => 237,  717 => 362,  712 => 251,  691 => 233,  678 => 275,  654 => 199,  587 => 210,  576 => 201,  539 => 116,  517 => 144,  471 => 160,  441 => 195,  437 => 149,  418 => 138,  386 => 154,  373 => 109,  304 => 89,  270 => 106,  265 => 92,  229 => 91,  477 => 167,  455 => 125,  448 => 143,  429 => 141,  407 => 120,  399 => 138,  389 => 145,  375 => 141,  358 => 109,  349 => 162,  335 => 120,  327 => 98,  298 => 91,  280 => 115,  249 => 88,  194 => 82,  142 => 51,  344 => 133,  318 => 114,  306 => 115,  295 => 111,  357 => 136,  300 => 118,  286 => 151,  276 => 86,  269 => 83,  254 => 100,  128 => 22,  237 => 72,  165 => 58,  122 => 30,  798 => 281,  770 => 302,  759 => 278,  748 => 298,  731 => 260,  721 => 293,  718 => 301,  708 => 218,  696 => 236,  617 => 204,  590 => 207,  553 => 263,  550 => 157,  540 => 258,  533 => 182,  500 => 171,  493 => 155,  489 => 161,  482 => 148,  467 => 158,  464 => 209,  458 => 147,  452 => 145,  449 => 137,  415 => 83,  382 => 135,  372 => 131,  361 => 110,  356 => 102,  339 => 131,  302 => 104,  285 => 109,  258 => 76,  123 => 32,  108 => 26,  424 => 148,  394 => 214,  380 => 117,  338 => 137,  319 => 216,  316 => 123,  312 => 116,  290 => 153,  267 => 141,  206 => 57,  110 => 19,  240 => 73,  224 => 92,  219 => 73,  217 => 80,  202 => 84,  186 => 53,  170 => 28,  100 => 29,  67 => 23,  14 => 1,  1096 => 345,  1090 => 339,  1088 => 435,  1085 => 456,  1066 => 423,  1034 => 282,  1031 => 281,  1018 => 416,  1013 => 358,  1007 => 408,  1002 => 403,  993 => 305,  986 => 264,  982 => 401,  976 => 399,  971 => 295,  964 => 255,  949 => 289,  946 => 288,  940 => 388,  937 => 374,  928 => 385,  926 => 318,  915 => 279,  912 => 82,  903 => 231,  898 => 440,  892 => 303,  889 => 277,  887 => 302,  884 => 79,  876 => 75,  874 => 215,  871 => 331,  863 => 373,  861 => 270,  858 => 255,  850 => 291,  843 => 206,  840 => 406,  815 => 251,  812 => 343,  808 => 246,  804 => 258,  799 => 312,  791 => 310,  785 => 262,  775 => 255,  771 => 253,  754 => 267,  728 => 317,  726 => 72,  723 => 238,  715 => 105,  711 => 220,  709 => 222,  706 => 232,  698 => 229,  694 => 199,  692 => 189,  689 => 234,  681 => 224,  677 => 229,  675 => 234,  663 => 218,  661 => 200,  650 => 213,  646 => 231,  629 => 267,  627 => 218,  625 => 266,  622 => 285,  598 => 199,  592 => 212,  586 => 196,  575 => 232,  566 => 242,  556 => 191,  554 => 188,  541 => 176,  536 => 224,  515 => 176,  511 => 166,  509 => 179,  488 => 152,  486 => 147,  483 => 165,  465 => 198,  463 => 148,  450 => 153,  432 => 146,  419 => 143,  371 => 151,  362 => 129,  353 => 196,  337 => 102,  333 => 153,  309 => 95,  303 => 161,  299 => 88,  291 => 89,  272 => 97,  261 => 138,  253 => 109,  239 => 70,  235 => 94,  213 => 63,  200 => 82,  198 => 64,  159 => 43,  149 => 52,  146 => 21,  131 => 64,  116 => 35,  79 => 16,  74 => 26,  71 => 17,  836 => 262,  817 => 243,  814 => 319,  811 => 261,  805 => 244,  787 => 257,  779 => 169,  776 => 232,  773 => 322,  761 => 296,  751 => 302,  747 => 265,  742 => 243,  739 => 227,  736 => 215,  724 => 259,  705 => 69,  702 => 216,  688 => 226,  680 => 205,  667 => 273,  662 => 218,  656 => 215,  649 => 285,  644 => 220,  641 => 220,  624 => 206,  613 => 166,  607 => 200,  597 => 275,  591 => 49,  584 => 236,  579 => 181,  563 => 175,  559 => 68,  551 => 190,  547 => 188,  537 => 183,  524 => 112,  512 => 174,  507 => 237,  504 => 159,  498 => 213,  485 => 172,  480 => 50,  472 => 160,  466 => 149,  460 => 142,  447 => 156,  442 => 40,  434 => 133,  428 => 149,  422 => 145,  404 => 129,  368 => 108,  364 => 111,  340 => 100,  334 => 135,  330 => 115,  325 => 98,  292 => 150,  287 => 89,  282 => 108,  279 => 104,  273 => 120,  266 => 82,  256 => 101,  252 => 87,  228 => 93,  218 => 62,  201 => 66,  64 => 7,  51 => 10,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 847,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 823,  2037 => 789,  2031 => 788,  2028 => 818,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 811,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 779,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 769,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 744,  1801 => 695,  1798 => 694,  1795 => 742,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 708,  1730 => 705,  1720 => 688,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 672,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 659,  1651 => 656,  1641 => 631,  1637 => 667,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 651,  1592 => 614,  1587 => 613,  1584 => 635,  1581 => 731,  1576 => 608,  1574 => 633,  1567 => 603,  1563 => 630,  1559 => 601,  1552 => 628,  1548 => 597,  1542 => 593,  1529 => 605,  1525 => 614,  1516 => 708,  1512 => 583,  1503 => 599,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 569,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 537,  1380 => 539,  1376 => 436,  1371 => 645,  1367 => 527,  1364 => 518,  1361 => 523,  1355 => 543,  1342 => 624,  1337 => 510,  1328 => 516,  1324 => 511,  1317 => 511,  1313 => 499,  1310 => 414,  1304 => 504,  1291 => 502,  1286 => 405,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 487,  1262 => 490,  1257 => 489,  1254 => 482,  1250 => 394,  1246 => 477,  1239 => 389,  1235 => 498,  1232 => 416,  1226 => 383,  1213 => 466,  1208 => 465,  1201 => 472,  1196 => 464,  1192 => 490,  1188 => 456,  1184 => 485,  1179 => 448,  1176 => 461,  1172 => 367,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 561,  1149 => 359,  1146 => 444,  1137 => 249,  1123 => 442,  1119 => 354,  1115 => 424,  1111 => 377,  1107 => 346,  1102 => 344,  1099 => 347,  1095 => 419,  1091 => 418,  1082 => 455,  1079 => 524,  1076 => 359,  1070 => 431,  1057 => 491,  1052 => 406,  1045 => 484,  1040 => 416,  1036 => 283,  1032 => 415,  1028 => 312,  1023 => 393,  1020 => 311,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 304,  998 => 308,  992 => 406,  979 => 297,  974 => 400,  967 => 399,  962 => 397,  958 => 336,  954 => 293,  950 => 393,  945 => 391,  942 => 288,  938 => 323,  934 => 284,  927 => 282,  923 => 382,  920 => 412,  910 => 278,  901 => 340,  897 => 273,  890 => 271,  886 => 270,  883 => 268,  868 => 375,  856 => 293,  853 => 319,  849 => 264,  845 => 290,  841 => 249,  835 => 245,  830 => 249,  826 => 282,  822 => 281,  818 => 264,  813 => 242,  810 => 290,  806 => 270,  802 => 339,  795 => 241,  792 => 335,  789 => 233,  784 => 286,  782 => 237,  777 => 255,  772 => 289,  768 => 81,  763 => 327,  760 => 250,  756 => 248,  752 => 247,  745 => 245,  741 => 218,  738 => 241,  732 => 171,  719 => 245,  714 => 251,  710 => 233,  704 => 281,  699 => 215,  695 => 66,  690 => 226,  687 => 210,  683 => 346,  679 => 223,  672 => 179,  668 => 264,  665 => 229,  658 => 178,  645 => 221,  640 => 227,  634 => 206,  628 => 174,  623 => 217,  619 => 78,  611 => 282,  606 => 280,  603 => 199,  599 => 194,  595 => 213,  583 => 159,  580 => 45,  573 => 157,  560 => 267,  543 => 186,  538 => 164,  534 => 189,  530 => 174,  526 => 221,  521 => 287,  518 => 157,  514 => 202,  510 => 175,  503 => 173,  496 => 163,  490 => 149,  484 => 146,  474 => 127,  470 => 168,  446 => 122,  440 => 135,  436 => 189,  431 => 145,  425 => 128,  416 => 125,  412 => 76,  408 => 165,  403 => 134,  400 => 119,  396 => 117,  392 => 116,  385 => 146,  381 => 113,  367 => 112,  363 => 105,  359 => 147,  355 => 108,  350 => 107,  346 => 124,  343 => 140,  328 => 118,  324 => 120,  313 => 96,  307 => 115,  301 => 92,  288 => 152,  283 => 88,  271 => 102,  257 => 100,  251 => 100,  238 => 95,  233 => 94,  195 => 30,  191 => 54,  187 => 61,  183 => 51,  130 => 35,  88 => 36,  76 => 18,  115 => 28,  95 => 23,  655 => 202,  651 => 176,  648 => 215,  637 => 218,  633 => 197,  621 => 462,  618 => 179,  615 => 196,  604 => 201,  600 => 233,  588 => 206,  585 => 295,  582 => 205,  571 => 179,  567 => 194,  555 => 172,  552 => 171,  549 => 170,  544 => 230,  542 => 166,  535 => 177,  531 => 162,  519 => 173,  516 => 218,  513 => 154,  508 => 215,  506 => 151,  499 => 177,  495 => 150,  491 => 168,  481 => 161,  478 => 128,  475 => 147,  469 => 182,  456 => 159,  451 => 195,  443 => 136,  439 => 152,  427 => 155,  423 => 142,  420 => 141,  409 => 160,  405 => 218,  401 => 176,  391 => 138,  387 => 334,  384 => 250,  378 => 205,  365 => 153,  360 => 104,  348 => 116,  336 => 130,  332 => 99,  329 => 125,  323 => 118,  310 => 93,  305 => 94,  277 => 79,  274 => 107,  263 => 77,  259 => 77,  247 => 99,  244 => 72,  241 => 129,  222 => 69,  210 => 32,  207 => 61,  204 => 60,  184 => 59,  181 => 52,  167 => 66,  157 => 71,  96 => 34,  421 => 147,  417 => 146,  414 => 145,  406 => 141,  398 => 158,  393 => 125,  390 => 124,  376 => 138,  369 => 124,  366 => 150,  352 => 135,  345 => 106,  342 => 122,  331 => 99,  326 => 137,  320 => 97,  317 => 171,  314 => 112,  311 => 122,  308 => 116,  297 => 117,  293 => 90,  281 => 106,  278 => 145,  275 => 103,  264 => 92,  260 => 96,  248 => 97,  245 => 74,  242 => 84,  231 => 125,  227 => 70,  215 => 83,  212 => 86,  209 => 111,  197 => 57,  177 => 50,  171 => 71,  161 => 51,  132 => 32,  121 => 32,  105 => 32,  99 => 52,  81 => 35,  77 => 22,  180 => 72,  176 => 49,  156 => 41,  143 => 45,  139 => 35,  118 => 65,  189 => 72,  185 => 79,  173 => 48,  166 => 53,  152 => 69,  174 => 41,  164 => 94,  154 => 90,  150 => 39,  137 => 42,  133 => 41,  127 => 62,  107 => 24,  102 => 26,  83 => 22,  78 => 27,  53 => 21,  23 => 3,  42 => 9,  138 => 34,  134 => 46,  109 => 33,  103 => 25,  97 => 25,  94 => 15,  84 => 19,  75 => 18,  69 => 21,  66 => 15,  54 => 13,  44 => 8,  230 => 64,  226 => 90,  203 => 66,  193 => 55,  188 => 80,  182 => 78,  178 => 76,  168 => 47,  163 => 52,  160 => 72,  155 => 45,  148 => 67,  145 => 30,  140 => 27,  136 => 34,  125 => 38,  120 => 39,  113 => 26,  101 => 36,  92 => 42,  89 => 25,  85 => 13,  73 => 9,  62 => 11,  59 => 10,  56 => 19,  41 => 10,  126 => 42,  119 => 61,  111 => 27,  106 => 31,  98 => 24,  93 => 18,  86 => 16,  70 => 13,  60 => 13,  28 => 2,  36 => 11,  114 => 37,  104 => 37,  91 => 24,  80 => 12,  63 => 22,  58 => 12,  40 => 8,  34 => 5,  45 => 8,  61 => 14,  55 => 10,  48 => 9,  39 => 5,  35 => 5,  31 => 4,  26 => 4,  21 => 2,  46 => 9,  29 => 5,  57 => 12,  50 => 8,  47 => 9,  38 => 4,  33 => 7,  49 => 10,  32 => 3,  246 => 75,  236 => 95,  232 => 71,  225 => 59,  221 => 87,  216 => 64,  214 => 114,  211 => 84,  208 => 68,  205 => 83,  199 => 58,  196 => 64,  190 => 79,  179 => 76,  175 => 55,  172 => 56,  169 => 54,  162 => 42,  158 => 92,  153 => 24,  151 => 49,  147 => 38,  144 => 82,  141 => 41,  135 => 65,  129 => 40,  124 => 31,  117 => 29,  112 => 29,  90 => 20,  87 => 23,  82 => 21,  72 => 15,  68 => 8,  65 => 19,  52 => 9,  43 => 8,  37 => 5,  30 => 4,  27 => 3,  25 => 4,  24 => 4,  22 => 2,  19 => 2,);
    }
}
