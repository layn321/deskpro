<?php

/* AgentBundle:Person:view-content-col.html.twig */
class __TwigTemplate_803eb563b9d7586da79f3df2f4aed48c extends \Application\DeskPRO\Twig\Template
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
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "profile", "summary", "above", $_person_api_);
        echo "

";
        // line 4
        if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
        if (isset($context["perms"])) { $_perms_ = $context["perms"]; } else { $_perms_ = null; }
        if ((twig_length_filter($this->env, $this->getAttribute($_person_, "summary")) || $this->getAttribute($_perms_, "edit"))) {
            // line 5
            echo "\t<div class=\"profile-box-container summary\"
\t\tdata-element-handler=\"DeskPRO.Agent.ElementHandler.FormSaver\"
\t\tdata-form-save-url=\"";
            // line 7
            if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_people_ajaxsave", array("person_id" => $this->getAttribute($_person_, "id"))), "html", null, true);
            echo "\"
\t>
\t\t<header>
\t\t\t";
            // line 10
            if (isset($context["perms"])) { $_perms_ = $context["perms"]; } else { $_perms_ = null; }
            if ($this->getAttribute($_perms_, "edit")) {
                // line 11
                echo "\t\t\t\t<div class=\"controls\">
\t\t\t\t\t<div class=\"is-loading\">";
                // line 12
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.saving");
                echo "</div>
\t\t\t\t\t<div class=\"saved\" style=\"display: none\">";
                // line 13
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.saved");
                echo "</div>
\t\t\t\t\t<div class=\"save\" style=\"display: none\">";
                // line 14
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.save");
                echo "</div>
\t\t\t\t</div>
\t\t\t";
            }
            // line 17
            echo "\t\t\t";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getWidgetTabsHeader($_baseId_, "profile", "summary", array(($_baseId_ . "_summary_body") => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.summary")));
            // line 19
            echo "
\t\t</header>
\t\t<section id=\"";
            // line 21
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_summary_body\">
\t\t\t<input type=\"hidden\" name=\"action\" value=\"set-summary\" />
\t\t\t<div class=\"textarea-section\"><textarea class=\"integrated\" id=\"";
            // line 23
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_summary\" name=\"summary\" ";
            if (isset($context["perms"])) { $_perms_ = $context["perms"]; } else { $_perms_ = null; }
            if ((!$this->getAttribute($_perms_, "edit"))) {
                echo "onkeydown=\"return false;\"";
            }
            echo ">";
            if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_person_, "summary"), "html", null, true);
            echo "</textarea></div>
\t\t</section>
\t\t";
            // line 25
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getWidgetTabsBody($_baseId_, "profile", "summary", "section", $_person_api_);
            echo "
\t</div>
";
        }
        // line 28
        echo "
";
        // line 29
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "profile", "summary", "below", $_person_api_);
        echo "
";
        // line 31
        echo "
";
        // line 33
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "profile", "properties", "above", $_person_api_);
        echo "

<div class=\"profile-box-container properties\" id=\"";
        // line 35
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_properties_box\">
\t<header>
\t\t";
        // line 37
        if (isset($context["perms"])) { $_perms_ = $context["perms"]; } else { $_perms_ = null; }
        if ($this->getAttribute($_perms_, "edit")) {
            // line 38
            echo "\t\t\t<div class=\"controls\">
\t\t\t\t<span class=\"edit-gear prop-edit-trigger\"></span>
\t\t\t\t<div class=\"is-loading\">";
            // line 40
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.saving");
            echo "</div>
\t\t\t\t<button class=\"cancel\" style=\"display: none\">";
            // line 41
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.cancel");
            echo "</button>
\t\t\t\t<button class=\"save\" style=\"display: none\">";
            // line 42
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.save");
            echo "</button>
\t\t\t</div>
\t\t";
        }
        // line 45
        echo "\t\t";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgetTabsHeader($_baseId_, "profile", "properties", array(($_baseId_ . "_properties_body") => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.properties")));
        // line 47
        echo "
\t</header>
\t<section id=\"";
        // line 49
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_properties_body\">
\t\t<div class=\"table-list\" id=\"";
        // line 50
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_custom_fields_rendered\">
\t\t\t";
        // line 51
        $this->env->loadTemplate("AgentBundle:Person:view-customfields-rendered-rows.html.twig")->display($context);
        // line 52
        echo "\t\t</div>
\t\t";
        // line 53
        if (isset($context["perms"])) { $_perms_ = $context["perms"]; } else { $_perms_ = null; }
        if ($this->getAttribute($_perms_, "edit")) {
            // line 54
            echo "\t\t<div class=\"table-list\" id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_custom_fields_editable\" style=\"display: none\">
\t\t\t";
            // line 55
            $this->env->loadTemplate("AgentBundle:Person:view-customfields-edit-rows.html.twig")->display($context);
            // line 56
            echo "\t\t</div>
\t\t";
        }
        // line 58
        echo "\t</section>
\t";
        // line 59
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgetTabsBody($_baseId_, "profile", "properties", "section", $_person_api_);
        echo "
</div>

";
        // line 62
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "profile", "properties", "below", $_person_api_);
        echo "
";
        // line 64
        echo "
";
        // line 66
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "profile", "interactions", "above", $_person_api_);
        echo "

";
        // line 68
        if (isset($context["person_tickets_count"])) { $_person_tickets_count_ = $context["person_tickets_count"]; } else { $_person_tickets_count_ = null; }
        if (isset($context["person_chats_count"])) { $_person_chats_count_ = $context["person_chats_count"]; } else { $_person_chats_count_ = null; }
        if (isset($context["person_charges"])) { $_person_charges_ = $context["person_charges"]; } else { $_person_charges_ = null; }
        if ((($_person_tickets_count_ || $_person_chats_count_) || $_person_charges_)) {
            // line 69
            echo "<div class=\"profile-box-container tabbed\">
\t<header>
\t\t<nav>
\t\t\t<ul>
\t\t\t\t";
            // line 73
            if (isset($context["person_tickets_count"])) { $_person_tickets_count_ = $context["person_tickets_count"]; } else { $_person_tickets_count_ = null; }
            if ($_person_tickets_count_) {
                echo "<li data-tab-for=\"#";
                if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
                echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
                echo "_tickets_tab\" class=\"on\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.tickets");
                echo " <span class=\"count\">";
                if (isset($context["person_tickets_count"])) { $_person_tickets_count_ = $context["person_tickets_count"]; } else { $_person_tickets_count_ = null; }
                echo twig_escape_filter($this->env, $_person_tickets_count_, "html", null, true);
                echo "</span></li>";
            }
            // line 74
            echo "\t\t\t\t";
            if (isset($context["person_chats_count"])) { $_person_chats_count_ = $context["person_chats_count"]; } else { $_person_chats_count_ = null; }
            if ($_person_chats_count_) {
                echo "<li data-tab-for=\"#";
                if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
                echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
                echo "_chats_tab\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.chats");
                echo " <span class=\"count\">";
                if (isset($context["person_chats_count"])) { $_person_chats_count_ = $context["person_chats_count"]; } else { $_person_chats_count_ = null; }
                echo twig_escape_filter($this->env, $_person_chats_count_, "html", null, true);
                echo "</span></li>";
            }
            // line 75
            echo "\t\t\t\t";
            if (isset($context["person_charges"])) { $_person_charges_ = $context["person_charges"]; } else { $_person_charges_ = null; }
            if ($_person_charges_) {
                echo "<li data-tab-for=\"#";
                if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
                echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
                echo "_billing_tab\">Billing <span class=\"count\">";
                if (isset($context["person_charge_totals"])) { $_person_charge_totals_ = $context["person_charge_totals"]; } else { $_person_charge_totals_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_person_charge_totals_, "count"), "html", null, true);
                echo "</span></li>";
            }
            // line 76
            echo "\t\t\t\t";
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->env->getExtension('deskpro_templating')->getWidgetsRaw("profile", "interactions", "tab"));
            foreach ($context['_seq'] as $context["_key"] => $context["widget"]) {
                // line 77
                echo "\t\t\t\t\t<li data-tab-for=\"#";
                if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
                if (isset($context["widget"])) { $_widget_ = $context["widget"]; } else { $_widget_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getWidgetHtmlId($_baseId_, $_widget_), "html", null, true);
                echo "\">";
                if (isset($context["widget"])) { $_widget_ = $context["widget"]; } else { $_widget_ = null; }
                echo $this->getAttribute($_widget_, "title");
                echo "</li>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['widget'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 79
            echo "\t\t\t</ul>
\t\t</nav>
\t</header>
\t<section>
\t\t";
            // line 84
            echo "\t\t";
            if (isset($context["person_tickets_count"])) { $_person_tickets_count_ = $context["person_tickets_count"]; } else { $_person_tickets_count_ = null; }
            if ($_person_tickets_count_) {
                // line 85
                echo "\t\t<article id=\"";
                if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
                echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
                echo "_tickets_tab\" class=\"on\">
\t\t\t<table class=\"tickets-simple\" width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
\t\t\t\t<tbody id=\"";
                // line 87
                if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
                echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
                echo "_tickets_initial\">
\t\t\t\t\t";
                // line 88
                if (isset($context["person_tickets_initial"])) { $_person_tickets_initial_ = $context["person_tickets_initial"]; } else { $_person_tickets_initial_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($_person_tickets_initial_);
                foreach ($context['_seq'] as $context["_key"] => $context["ticket"]) {
                    // line 89
                    echo "\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t\t<div class=\"fade-container\">
\t\t\t\t\t\t\t\t\t<div class=\"line\">
\t\t\t\t\t\t\t\t\t\t<a data-route=\"ticket:";
                    // line 93
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_ticket_view", array("ticket_id" => $this->getAttribute($_ticket_, "id"))), "html", null, true);
                    echo "\" data-route-title=\"@text\"><span class=\"id-number\">#";
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "id"), "html", null, true);
                    echo "</span> ";
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "subject"), "html", null, true);
                    echo "</a>
\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t<div class=\"bound-fade\"></div>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t<td ";
                    // line 98
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    if ((!$this->getAttribute($_ticket_, "agent"))) {
                        echo "colspan=\"2\"";
                    }
                    echo ">
\t\t\t\t\t\t\t\t<div class=\"status-pill status urgency-";
                    // line 99
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "urgency"), "html", null, true);
                    echo " ";
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    echo twig_escape_filter($this->env, strtr($this->getAttribute($_ticket_, "status_code"), array("." => "_")), "html", null, true);
                    echo "\" style=\"float: right; position: relative; top: 0px;\">
\t\t\t\t\t\t\t\t\t<label>";
                    // line 100
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, ("agent.tickets.status_" . strtr($this->getAttribute($_ticket_, "status_code"), array("." => "_"))));
                    echo "</label>
\t\t\t\t\t\t\t\t\t";
                    // line 101
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    if (($this->getAttribute($_ticket_, "status") == "awaiting_agent")) {
                        echo "<i class=\"ticket-urgency\" data-urgency=\"";
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "urgency"), "html", null, true);
                        echo "\">";
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "urgency"), "html", null, true);
                        echo "</i>";
                    }
                    // line 102
                    echo "\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t";
                    // line 104
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    if ($this->getAttribute($_ticket_, "agent")) {
                        // line 105
                        echo "\t\t\t\t\t\t\t\t<td width=\"16\" style=\"width: 16px; padding:0; padding-right: 4px;\">
\t\t\t\t\t\t\t\t\t<span
\t\t\t\t\t\t\t\t\t\tclass=\"tipped\"
\t\t\t\t\t\t\t\t\t\ttitle=\"";
                        // line 108
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "agent"), "display_name"), "html", null, true);
                        echo "\"
\t\t\t\t\t\t\t\t\t\tstyle=\"background: url('";
                        // line 109
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "agent"), "getPictureUrl", array(0 => 16), "method"), "html", null, true);
                        echo "') no-repeat 0 50%; height: 16px; width: 16px; overflow: hidden; display: block;\"
\t\t\t\t\t\t\t\t\t></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t";
                    }
                    // line 113
                    echo "\t\t\t\t\t\t</tr>
\t\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['ticket'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 115
                echo "\t\t\t\t\t";
                if (isset($context["person_tickets"])) { $_person_tickets_ = $context["person_tickets"]; } else { $_person_tickets_ = null; }
                if (twig_length_filter($this->env, $_person_tickets_)) {
                    // line 116
                    echo "\t\t\t\t\t\t";
                    if (isset($context["person_tickets"])) { $_person_tickets_ = $context["person_tickets"]; } else { $_person_tickets_ = null; }
                    if ((twig_length_filter($this->env, $_person_tickets_) >= 250)) {
                        // line 117
                        echo "\t\t\t\t\t\t\t<tr class=\"more\" >
\t\t\t\t\t\t\t\t<td colspan=\"100\">
\t\t\t\t\t\t\t\t\t<button class=\"clean-white\" data-route=\"listpane:";
                        // line 119
                        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_ticketsearch_runcustomfilter"), "html", null, true);
                        echo "?search_person_id=";
                        if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_person_, "id"), "html", null, true);
                        echo "\">
\t\t\t\t\t\t\t\t\t\t";
                        // line 120
                        if (isset($context["person_tickets_count"])) { $_person_tickets_count_ = $context["person_tickets_count"]; } else { $_person_tickets_count_ = null; }
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.view_all_x_tickets", array("count" => $_person_tickets_count_));
                        echo "
\t\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t";
                    } else {
                        // line 125
                        echo "\t\t\t\t\t\t\t<tr class=\"more\" >
\t\t\t\t\t\t\t\t<td colspan=\"100\">
\t\t\t\t\t\t\t\t\t<button class=\"clean-white\" id=\"";
                        // line 127
                        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
                        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
                        echo "_tickets_viewall\">
\t\t\t\t\t\t\t\t\t\t";
                        // line 128
                        if (isset($context["person_tickets_count"])) { $_person_tickets_count_ = $context["person_tickets_count"]; } else { $_person_tickets_count_ = null; }
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.view_all_x_tickets", array("count" => $_person_tickets_count_));
                        echo "
\t\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t";
                    }
                    // line 133
                    echo "\t\t\t\t\t";
                }
                // line 134
                echo "\t\t\t\t</tbody>
\t\t\t\t";
                // line 135
                if (isset($context["person_tickets"])) { $_person_tickets_ = $context["person_tickets"]; } else { $_person_tickets_ = null; }
                if ((twig_length_filter($this->env, $_person_tickets_) <= 250)) {
                    // line 136
                    echo "\t\t\t\t\t<tbody id=\"";
                    if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
                    echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
                    echo "_tickets_rest\" style=\"display: none;\">
\t\t\t\t\t\t";
                    // line 137
                    if (isset($context["person_tickets"])) { $_person_tickets_ = $context["person_tickets"]; } else { $_person_tickets_ = null; }
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($_person_tickets_);
                    foreach ($context['_seq'] as $context["_key"] => $context["ticket"]) {
                        // line 138
                        echo "\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t\t\t<div class=\"fade-container\">
\t\t\t\t\t\t\t\t\t\t<div class=\"line\">
\t\t\t\t\t\t\t\t\t\t\t<a data-route=\"ticket:";
                        // line 142
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_ticket_view", array("ticket_id" => $this->getAttribute($_ticket_, "id"))), "html", null, true);
                        echo "\" data-route-title=\"@text\"><span class=\"id-number\">#";
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "id"), "html", null, true);
                        echo "</span> ";
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "subject"), "html", null, true);
                        echo "</a>
\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t<div class=\"bound-fade\"></div>
\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td ";
                        // line 147
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if ((!$this->getAttribute($_ticket_, "agent"))) {
                            echo "colspan=\"2\"";
                        }
                        echo ">
\t\t\t\t\t\t\t\t\t<div class=\"status-pill status urgency-";
                        // line 148
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "urgency"), "html", null, true);
                        echo " ";
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, strtr($this->getAttribute($_ticket_, "status_code"), array("." => "_")), "html", null, true);
                        echo "\" style=\"float: right; position: relative; top: 0px;\">
\t\t\t\t\t\t\t\t\t\t<label>";
                        // line 149
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, ("agent.tickets.status_" . strtr($this->getAttribute($_ticket_, "status_code"), array("." => "_"))));
                        echo "</label>
\t\t\t\t\t\t\t\t\t\t";
                        // line 150
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if (($this->getAttribute($_ticket_, "status") == "awaiting_agent")) {
                            echo "<i class=\"ticket-urgency\" data-urgency=\"";
                            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "urgency"), "html", null, true);
                            echo "\">";
                            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "urgency"), "html", null, true);
                            echo "</i>";
                        }
                        // line 151
                        echo "\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t";
                        // line 153
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if ($this->getAttribute($_ticket_, "agent")) {
                            // line 154
                            echo "\t\t\t\t\t\t\t\t\t<td width=\"16\" style=\"width: 16px; padding:0; padding-right: 4px;\">
\t\t\t\t\t\t\t\t\t\t<span
\t\t\t\t\t\t\t\t\t\t\tclass=\"tipped\"
\t\t\t\t\t\t\t\t\t\t\ttitle=\"";
                            // line 157
                            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "agent"), "display_name"), "html", null, true);
                            echo "\"
\t\t\t\t\t\t\t\t\t\t\tstyle=\"background: url('";
                            // line 158
                            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "agent"), "getPictureUrl", array(0 => 16), "method"), "html", null, true);
                            echo "') no-repeat 0 50%; height: 16px; width: 16px; overflow: hidden; display: block;\"
\t\t\t\t\t\t\t\t\t\t></span>
\t\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t";
                        }
                        // line 162
                        echo "\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['ticket'], $context['_parent'], $context['loop']);
                    $context = array_merge($_parent, array_intersect_key($context, $_parent));
                    // line 164
                    echo "\t\t\t\t\t</tbody>
\t\t\t\t";
                }
                // line 166
                echo "\t\t\t</table>
\t\t</article>
\t\t";
            }
            // line 169
            echo "\t\t";
            // line 170
            echo "
\t\t";
            // line 171
            if (isset($context["person_chats_count"])) { $_person_chats_count_ = $context["person_chats_count"]; } else { $_person_chats_count_ = null; }
            if ($_person_chats_count_) {
                // line 172
                echo "\t\t<article id=\"";
                if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
                echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
                echo "_chats_tab\">
\t\t\t<table class=\"tickets-simple\" width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
\t\t\t\t<tbody>
\t\t\t\t\t";
                // line 175
                if (isset($context["person_chats"])) { $_person_chats_ = $context["person_chats"]; } else { $_person_chats_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($_person_chats_);
                foreach ($context['_seq'] as $context["_key"] => $context["chat"]) {
                    // line 176
                    echo "\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t\t<div class=\"fade-container\">
\t\t\t\t\t\t\t\t\t<div class=\"line\">
\t\t\t\t\t\t\t\t\t\t";
                    // line 180
                    if (isset($context["chat"])) { $_chat_ = $context["chat"]; } else { $_chat_ = null; }
                    echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_chat_, "date_created"), "day_short"), "html", null, true);
                    echo "
\t\t\t\t\t\t\t\t\t\t<a data-route=\"page:";
                    // line 181
                    if (isset($context["chat"])) { $_chat_ = $context["chat"]; } else { $_chat_ = null; }
                    echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_userchat_view", array("conversation_id" => $this->getAttribute($_chat_, "id"))), "html", null, true);
                    echo "\" data-route-notabreload=\"1\" data-route-title=\"@text\">";
                    if (isset($context["chat"])) { $_chat_ = $context["chat"]; } else { $_chat_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_chat_, "getSubjectLine", array(), "method"), "html", null, true);
                    echo "</a>
\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t<div class=\"bound-fade\"></div>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t</tr>
\t\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['chat'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 188
                echo "\t\t\t\t</tbody>
\t\t\t</table>
\t\t</article>
\t\t";
            }
            // line 192
            echo "
\t\t";
            // line 193
            if (isset($context["person_charges"])) { $_person_charges_ = $context["person_charges"]; } else { $_person_charges_ = null; }
            if ($_person_charges_) {
                // line 194
                echo "\t\t<article id=\"";
                if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
                echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
                echo "_billing_tab\">
\t\t\t<table class=\"tickets-simple\" width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
\t\t\t";
                // line 196
                if (isset($context["person_charges"])) { $_person_charges_ = $context["person_charges"]; } else { $_person_charges_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($_person_charges_);
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
                foreach ($context['_seq'] as $context["_key"] => $context["charge"]) {
                    // line 197
                    echo "\t\t\t\t";
                    $this->env->loadTemplate("AgentBundle:Common:billing-row-simple.html.twig")->display($context);
                    // line 198
                    echo "\t\t\t";
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
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['charge'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 199
                echo "\t\t\t<tr>
\t\t\t\t<th colspan=\"2\">
\t\t\t\t\tTotals:
\t\t\t\t\t";
                // line 202
                if (isset($context["person_charge_totals"])) { $_person_charge_totals_ = $context["person_charge_totals"]; } else { $_person_charge_totals_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_person_charge_totals_, "count"), "html", null, true);
                echo " charges &bull;
\t\t\t\t\t";
                // line 203
                if (isset($context["person_charge_totals"])) { $_person_charge_totals_ = $context["person_charge_totals"]; } else { $_person_charge_totals_ = null; }
                if ($this->getAttribute($_person_charge_totals_, "charge_time")) {
                    if (isset($context["person_charge_totals"])) { $_person_charge_totals_ = $context["person_charge_totals"]; } else { $_person_charge_totals_ = null; }
                    echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->timeLength($this->getAttribute($_person_charge_totals_, "charge_time")), "html", null, true);
                    if (isset($context["person_charge_totals"])) { $_person_charge_totals_ = $context["person_charge_totals"]; } else { $_person_charge_totals_ = null; }
                    if ($this->getAttribute($_person_charge_totals_, "charge")) {
                        echo " &bull; ";
                    }
                }
                // line 204
                echo "\t\t\t\t\t";
                if (isset($context["person_charge_totals"])) { $_person_charge_totals_ = $context["person_charge_totals"]; } else { $_person_charge_totals_ = null; }
                if ($this->getAttribute($_person_charge_totals_, "charge")) {
                    if (isset($context["person_charge_totals"])) { $_person_charge_totals_ = $context["person_charge_totals"]; } else { $_person_charge_totals_ = null; }
                    echo twig_escape_filter($this->env, twig_number_format_filter($this->env, $this->getAttribute($_person_charge_totals_, "charge"), 2), "html", null, true);
                    echo " ";
                    if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.billing_currency"), "method"), "html", null, true);
                }
                // line 205
                echo "\t\t\t\t</th>
\t\t\t</tr>
\t\t\t</table>
\t\t</article>
\t\t";
            }
            // line 210
            echo "
\t\t";
            // line 211
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getWidgetTabsBody($_baseId_, "profile", "interactions", "article", $_person_api_);
            echo "
\t</section>
</div>
";
        }
        // line 215
        echo "
";
        // line 216
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "profile", "interactions", "below", $_person_api_);
        echo "
";
        // line 218
        echo "
";
        // line 220
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "profile", "info", "above", $_person_api_);
        echo "

<div class=\"profile-box-container tabbed\">
\t<header>
\t\t<nav>
\t\t\t<ul>
\t\t\t\t";
        // line 226
        if (isset($context["notes"])) { $_notes_ = $context["notes"]; } else { $_notes_ = null; }
        if (isset($context["perms"])) { $_perms_ = $context["perms"]; } else { $_perms_ = null; }
        if ((twig_length_filter($this->env, $_notes_) || $this->getAttribute($_perms_, "notes"))) {
            echo "<li data-tab-for=\"#";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_notes_tab\" class=\"on\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.notes");
            echo " <span class=\"count\" id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_notes_count\">";
            if (isset($context["notes"])) { $_notes_ = $context["notes"]; } else { $_notes_ = null; }
            echo twig_escape_filter($this->env, twig_length_filter($this->env, $_notes_), "html", null, true);
            echo "</span></li>";
        }
        // line 227
        echo "\t\t\t\t";
        if (isset($context["activity_stream"])) { $_activity_stream_ = $context["activity_stream"]; } else { $_activity_stream_ = null; }
        if (twig_length_filter($this->env, $_activity_stream_)) {
            echo "<li data-tab-for=\"#";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_activity_tab\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.activity_stream");
            echo "</li>";
        }
        // line 228
        echo "\t\t\t\t<li data-tab-for=\"#";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_tracking_tab\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.detail");
        echo "</li>
\t\t\t\t";
        // line 229
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->env->getExtension('deskpro_templating')->getWidgetsRaw("profile", "info", "tab"));
        foreach ($context['_seq'] as $context["_key"] => $context["widget"]) {
            // line 230
            echo "\t\t\t\t\t<li data-tab-for=\"#";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            if (isset($context["widget"])) { $_widget_ = $context["widget"]; } else { $_widget_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getWidgetHtmlId($_baseId_, $_widget_), "html", null, true);
            echo "\">";
            if (isset($context["widget"])) { $_widget_ = $context["widget"]; } else { $_widget_ = null; }
            echo $this->getAttribute($_widget_, "title");
            echo "</li>
\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['widget'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 232
        echo "\t\t\t</ul>
\t\t</nav>
\t</header>
\t<section>
\t\t";
        // line 237
        echo "\t\t";
        if (isset($context["notes"])) { $_notes_ = $context["notes"]; } else { $_notes_ = null; }
        if (isset($context["perms"])) { $_perms_ = $context["perms"]; } else { $_perms_ = null; }
        if ((twig_length_filter($this->env, $_notes_) || $this->getAttribute($_perms_, "notes"))) {
            // line 238
            echo "\t\t\t<article id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_notes_tab\" class=\"on\">
\t\t\t\t<div class=\"notes-wrap\">
\t\t\t\t\t<ul>
\t\t\t\t\t\t";
            // line 241
            if (isset($context["notes"])) { $_notes_ = $context["notes"]; } else { $_notes_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable(twig_reverse_filter($this->env, $_notes_));
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
            foreach ($context['_seq'] as $context["_key"] => $context["note"]) {
                // line 242
                echo "\t\t\t\t\t\t\t";
                if (isset($context["note"])) { $_note_ = $context["note"]; } else { $_note_ = null; }
                $this->env->loadTemplate("AgentBundle:Person:note-li.html.twig")->display(array_merge($context, array("note" => $_note_)));
                // line 243
                echo "\t\t\t\t\t\t";
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
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['note'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 244
            echo "\t\t\t\t\t\t";
            if (isset($context["perms"])) { $_perms_ = $context["perms"]; } else { $_perms_ = null; }
            if ($this->getAttribute($_perms_, "notes")) {
                // line 245
                echo "\t\t\t\t\t\t\t<li class=\"new-note\"
\t\t\t\t\t\t\t\tdata-element-handler=\"DeskPRO.Agent.ElementHandler.FormSaver\"
\t\t\t\t\t\t\t\tdata-form-list-selector=\"ul\"
\t\t\t\t\t\t\t\tdata-form-result-html-key=\"note_li_html\"
\t\t\t\t\t\t\t\tdata-form-save-url=\"";
                // line 249
                if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_people_ajaxsave_note", array("person_id" => $this->getAttribute($_person_, "id"))), "html", null, true);
                echo "\"
\t\t\t\t\t\t\t\tdata-form-count-el=\"#";
                // line 250
                if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
                echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
                echo "_notes_count\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t\t\t<header>
\t\t\t\t\t\t\t\t\t<div class=\"controls\">
\t\t\t\t\t\t\t\t\t\t<div class=\"is-loading\">";
                // line 254
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.saving");
                echo "</div>
\t\t\t\t\t\t\t\t\t\t<div class=\"saved\" style=\"display: none\">";
                // line 255
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.saved");
                echo "</div>
\t\t\t\t\t\t\t\t\t\t<div class=\"save\" style=\"display: none\">";
                // line 256
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.save");
                echo "</div>
\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t<h3>";
                // line 258
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.new_note");
                echo "</h3>
\t\t\t\t\t\t\t\t</header>
\t\t\t\t\t\t\t\t<div class=\"textarea-section\"><textarea class=\"integrated\" name=\"note\" placeholder=\"\" data-not-blank=\"1\"></textarea></div>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t";
            }
            // line 263
            echo "\t\t\t\t\t</ul>
\t\t\t\t</div>
\t\t\t</article>
\t\t";
        }
        // line 267
        echo "\t\t";
        // line 268
        echo "
\t\t";
        // line 270
        echo "\t\t";
        if (isset($context["activity_stream"])) { $_activity_stream_ = $context["activity_stream"]; } else { $_activity_stream_ = null; }
        if (twig_length_filter($this->env, $_activity_stream_)) {
            // line 271
            echo "\t\t<article id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_activity_tab\">
\t\t\t<table class=\"tickets-simple\" width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
\t\t\t\t";
            // line 273
            if (isset($context["activity_stream"])) { $_activity_stream_ = $context["activity_stream"]; } else { $_activity_stream_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_activity_stream_);
            foreach ($context['_seq'] as $context["_key"] => $context["action"]) {
                // line 274
                echo "\t\t\t\t\t<tr>
\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t<div class=\"fade-container\">
\t\t\t\t\t\t\t\t<div ";
                // line 277
                if (isset($context["action"])) { $_action_ = $context["action"]; } else { $_action_ = null; }
                if (($this->getAttribute($_action_, "action_type") != "agent_login_as")) {
                    echo "class=\"line\"";
                }
                echo ">
\t\t\t\t\t\t\t\t\t<span class=\"what\">
\t\t\t\t\t\t\t\t\t\t";
                // line 279
                if (isset($context["action"])) { $_action_ = $context["action"]; } else { $_action_ = null; }
                if (($this->getAttribute($_action_, "action_type") == "registered")) {
                    // line 280
                    echo "\t\t\t\t\t\t\t\t\t\t\t";
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.people.act_reg");
                    echo "
\t\t\t\t\t\t\t\t\t\t";
                } elseif (($this->getAttribute($_action_, "action_type") == "new_ticket")) {
                    // line 282
                    echo "\t\t\t\t\t\t\t\t\t\t\t";
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.people.act_submitted_ticket");
                    echo "
\t\t\t\t\t\t\t\t\t\t\t<a class=\"with-route\" data-route=\"ticket:";
                    // line 283
                    if (isset($context["action"])) { $_action_ = $context["action"]; } else { $_action_ = null; }
                    echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_ticket_view", array("ticket_id" => $this->getAttribute($this->getAttribute($_action_, "details"), "ticket_id"))), "html", null, true);
                    echo "\">
\t\t\t\t\t\t\t\t\t\t\t\t";
                    // line 284
                    if (isset($context["action"])) { $_action_ = $context["action"]; } else { $_action_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_action_, "details"), "subject"), "html", null, true);
                    echo "
\t\t\t\t\t\t\t\t\t\t\t\t";
                    // line 285
                    if (isset($context["action"])) { $_action_ = $context["action"]; } else { $_action_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_action_, "details"), "subject"), "html", null, true);
                    echo "
\t\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t\t";
                } elseif (($this->getAttribute($_action_, "action_type") == "new_chat")) {
                    // line 288
                    echo "\t\t\t\t\t\t\t\t\t\t\tCreated a chat:
\t\t\t\t\t\t\t\t\t\t\t";
                    // line 289
                    if (isset($context["person_chats"])) { $_person_chats_ = $context["person_chats"]; } else { $_person_chats_ = null; }
                    if (isset($context["action"])) { $_action_ = $context["action"]; } else { $_action_ = null; }
                    $context["chat"] = $this->getAttribute($_person_chats_, $this->getAttribute($this->getAttribute($_action_, "details"), "convo_id"), array(), "array");
                    // line 290
                    echo "\t\t\t\t\t\t\t\t\t\t\t<a data-route=\"page:";
                    if (isset($context["chat"])) { $_chat_ = $context["chat"]; } else { $_chat_ = null; }
                    echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_userchat_view", array("conversation_id" => $this->getAttribute($_chat_, "id"))), "html", null, true);
                    echo "\" data-route-notabreload=\"1\" data-route-title=\"@text\">";
                    if (isset($context["chat"])) { $_chat_ = $context["chat"]; } else { $_chat_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_chat_, "getSubjectLine", array(), "method"), "html", null, true);
                    echo "</a>
\t\t\t\t\t\t\t\t\t\t";
                } elseif (($this->getAttribute($_action_, "action_type") == "new_ticket_reply")) {
                    // line 292
                    echo "\t\t\t\t\t\t\t\t\t\t\t";
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.people.act_replied_ticket");
                    echo "
\t\t\t\t\t\t\t\t\t\t\t<a class=\"with-route\" data-route=\"ticket:";
                    // line 293
                    if (isset($context["action"])) { $_action_ = $context["action"]; } else { $_action_ = null; }
                    echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_ticket_view", array("ticket_id" => $this->getAttribute($this->getAttribute($_action_, "details"), "ticket_id"))), "html", null, true);
                    echo "\">
\t\t\t\t\t\t\t\t\t\t\t\t";
                    // line 294
                    if (isset($context["action"])) { $_action_ = $context["action"]; } else { $_action_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_action_, "details"), "subject"), "html", null, true);
                    echo "
\t\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t\t";
                } elseif (($this->getAttribute($_action_, "action_type") == "agent_login_as")) {
                    // line 297
                    echo "\t\t\t\t\t\t\t\t\t\t\tAgent logged in as this user:<br/>
\t\t\t\t\t\t\t\t\t\t\t<span style=\"font-size: 10px;\">#";
                    // line 298
                    if (isset($context["action"])) { $_action_ = $context["action"]; } else { $_action_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_action_, "details"), "agent_id"), "html", null, true);
                    echo " ";
                    if (isset($context["action"])) { $_action_ = $context["action"]; } else { $_action_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_action_, "details"), "agent_name"), "html", null, true);
                    echo " (";
                    if (isset($context["action"])) { $_action_ = $context["action"]; } else { $_action_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_action_, "details"), "agent_email"), "html", null, true);
                    echo ")</span>
\t\t\t\t\t\t\t\t\t\t";
                }
                // line 300
                echo "\t\t\t\t\t\t\t\t\t</span>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t<div class=\"bound-fade\"></div>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t<time class=\"timeago\" datetime=\"";
                // line 304
                if (isset($context["action"])) { $_action_ = $context["action"]; } else { $_action_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_action_, "date_created"), "c", "UTC"), "html", null, true);
                echo "\">";
                if (isset($context["action"])) { $_action_ = $context["action"]; } else { $_action_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_action_, "date_created"), "day"), "html", null, true);
                echo "</time>
\t\t\t\t\t\t</td>
\t\t\t\t\t</tr>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['action'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 308
            echo "\t\t\t</table>
\t\t</article>
\t\t";
        }
        // line 311
        echo "\t\t";
        // line 312
        echo "
\t\t";
        // line 314
        echo "\t\t<article id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_tracking_tab\">
\t\t\t<table class=\"info-simple\" width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
\t\t\t\t<tr>
\t\t\t\t\t<td class=\"label\" width=\"80\">";
        // line 317
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.id");
        echo "</td>
\t\t\t\t\t<td class=\"value\">
\t\t\t\t\t\t";
        // line 319
        if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_person_, "id"), "html", null, true);
        echo "
\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t\t<tr>
\t\t\t\t\t<td class=\"label\" width=\"80\">";
        // line 323
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.created");
        echo "</td>
\t\t\t\t\t<td class=\"value\">
\t\t\t\t\t\t<time>";
        // line 325
        if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_person_, "date_created"), "fulltime"), "html", null, true);
        echo "</time>
\t\t\t\t\t\t(<time class=\"timeago\" datetime=\"";
        // line 326
        if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_person_, "date_created"), "c", "UTC"), "html", null, true);
        echo "\"></time>)
\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t\t<tr>
\t\t\t\t\t<td class=\"label\">";
        // line 330
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.people.creation_by");
        echo "</td>
\t\t\t\t\t<td class=\"value\">
\t\t\t\t\t\t";
        // line 332
        if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
        if (($this->getAttribute($_person_, "creation_system") == "web.person")) {
            // line 333
            echo "\t\t\t\t\t\t\t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.people.reg_website");
            echo "
\t\t\t\t\t\t";
        } elseif (($this->getAttribute($_person_, "creation_system") == "web.agent")) {
            // line 335
            echo "\t\t\t\t\t\t\t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.people.reg_agent");
            echo "
\t\t\t\t\t\t";
        } elseif (($this->getAttribute($_person_, "creation_system") == "gateway.person")) {
            // line 337
            echo "\t\t\t\t\t\t\t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.people.reg_gateway");
            echo "
\t\t\t\t\t\t";
        } else {
            // line 339
            echo "\t\t\t\t\t\t\t";
            if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_person_, "creation_system"), "html", null, true);
            echo "
\t\t\t\t\t\t";
        }
        // line 341
        echo "\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t\t<tr>
\t\t\t\t\t<td class=\"label\">";
        // line 344
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.last_login");
        echo "</td>
\t\t\t\t\t<td class=\"value\">
\t\t\t\t\t\t";
        // line 346
        if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
        if ($this->getAttribute($_person_, "date_last_login")) {
            // line 347
            echo "\t\t\t\t\t\t\t<time>";
            if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_person_, "date_last_login"), "fulltime"), "html", null, true);
            echo "</time>
\t\t\t\t\t\t\t(<time class=\"timeago\" datetime=\"";
            // line 348
            if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_person_, "date_last_login"), "c", "UTC"), "html", null, true);
            echo "\"></time>)
\t\t\t\t\t\t";
        } else {
            // line 350
            echo "\t\t\t\t\t\t\t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.never");
            echo "
\t\t\t\t\t\t";
        }
        // line 352
        echo "\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t\t";
        // line 354
        if (isset($context["session"])) { $_session_ = $context["session"]; } else { $_session_ = null; }
        if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
        if ($_session_) {
            // line 355
            echo "\t\t\t\t\t<tr>
\t\t\t\t\t\t<td class=\"label\">";
            // line 356
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.online_now");
            echo "</td>
\t\t\t\t\t\t<td class=\"value\">
\t\t\t\t\t\t\t<time>";
            // line 358
            if (isset($context["session"])) { $_session_ = $context["session"]; } else { $_session_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_session_, "date_created"), "fulltime"), "html", null, true);
            echo "</time>
\t\t\t\t\t\t\t(<time class=\"timeago\" datetime=\"";
            // line 359
            if (isset($context["session"])) { $_session_ = $context["session"]; } else { $_session_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_session_, "date_created"), "c", "UTC"), "html", null, true);
            echo "\"></time>)
\t\t\t\t\t\t</td>
\t\t\t\t\t</tr>
\t\t\t\t\t<tr>
\t\t\t\t\t\t<td class=\"label\">";
            // line 363
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.last_activity");
            echo "</td>
\t\t\t\t\t\t<td class=\"value\">
\t\t\t\t\t\t\t<time>";
            // line 365
            if (isset($context["session"])) { $_session_ = $context["session"]; } else { $_session_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_session_, "date_last"), "fulltime"), "html", null, true);
            echo "</time>
\t\t\t\t\t\t\t(<time class=\"timeago\" datetime=\"";
            // line 366
            if (isset($context["session"])) { $_session_ = $context["session"]; } else { $_session_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_session_, "date_last"), "c", "UTC"), "html", null, true);
            echo "\"></time>)
\t\t\t\t\t\t\ton page <em class=\"short-url-prev tipped\" title=\"";
            // line 367
            if (isset($context["session"])) { $_session_ = $context["session"]; } else { $_session_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_session_, "visitor"), "last_track"), "page_url"), "html", null, true);
            echo "\">";
            if (isset($context["session"])) { $_session_ = $context["session"]; } else { $_session_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getUrlDomain($this->getAttribute($this->getAttribute($this->getAttribute($_session_, "visitor"), "last_track"), "page_url")), "html", null, true);
            echo "</em>
\t\t\t\t\t</tr>
\t\t\t\t\t<tr>
\t\t\t\t\t\t<td class=\"label\">";
            // line 370
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.landing_page");
            echo "</td>
\t\t\t\t\t\t<td class=\"value\">
\t\t\t\t\t\t\t";
            // line 372
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.people.user_landed_on_page");
            echo "
\t\t\t\t\t\t\t<em class=\"short-url-prev tipped\" title=\"";
            // line 373
            if (isset($context["session"])) { $_session_ = $context["session"]; } else { $_session_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_session_, "visitor"), "initial_track"), "page_url"), "html", null, true);
            echo "\">";
            if (isset($context["session"])) { $_session_ = $context["session"]; } else { $_session_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getUrlDomain($this->getAttribute($this->getAttribute($this->getAttribute($_session_, "visitor"), "initial_track"), "page_url")), "html", null, true);
            echo "</em>
\t\t\t\t\t\t\t";
            // line 374
            if (isset($context["session"])) { $_session_ = $context["session"]; } else { $_session_ = null; }
            if ($this->getAttribute($this->getAttribute($this->getAttribute($_session_, "visitor"), "visit_track"), "ref_page_url")) {
                // line 375
                echo "\t\t\t\t\t\t\t\t";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.people.and_came_from");
                echo "
\t\t\t\t\t\t\t\t<em class=\"short-url-prev tipped\" title=\"";
                // line 376
                if (isset($context["session"])) { $_session_ = $context["session"]; } else { $_session_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_session_, "visitor"), "visit_track"), "ref_page_url"), "html", null, true);
                echo "\">";
                if (isset($context["session"])) { $_session_ = $context["session"]; } else { $_session_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getUrlDomain($this->getAttribute($this->getAttribute($this->getAttribute($_session_, "visitor"), "visit_track"), "ref_page_url")), "html", null, true);
                echo "</em>
\t\t\t\t\t\t\t";
            }
            // line 378
            echo "\t\t\t\t\t\t</td>
\t\t\t\t\t</tr>
\t\t\t\t\t<tr>
\t\t\t\t\t\t<td class=\"label\">";
            // line 381
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ip_address");
            echo "</td>
\t\t\t\t\t\t<td class=\"value\">
\t\t\t\t\t\t\t";
            // line 383
            if (isset($context["session"])) { $_session_ = $context["session"]; } else { $_session_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_session_, "visitor"), "ip_address"), "html", null, true);
            echo "
\t\t\t\t\t\t</td>
\t\t\t\t\t</tr>
\t\t\t\t\t<tr>
\t\t\t\t\t\t<td class=\"label\">";
            // line 387
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.browser_agent");
            echo "</td>
\t\t\t\t\t\t<td class=\"value\">
\t\t\t\t\t\t\t";
            // line 389
            if (isset($context["session"])) { $_session_ = $context["session"]; } else { $_session_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_session_, "visitor"), "last_track"), "user_agent"), "html", null, true);
            echo "
\t\t\t\t\t\t</td>
\t\t\t\t\t</tr>
\t\t\t\t";
        } elseif ($_visitor_) {
            // line 393
            echo "\t\t\t\t\t<tr>
\t\t\t\t\t\t<td class=\"label\">";
            // line 394
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.last_activity");
            echo "</td>
\t\t\t\t\t\t<td class=\"value\">
\t\t\t\t\t\t\t<time>";
            // line 396
            if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_visitor_, "date_last"), "fulltime"), "html", null, true);
            echo "</time>
\t\t\t\t\t\t\t(<time class=\"timeago\" datetime=\"";
            // line 397
            if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_visitor_, "date_last"), "c", "UTC"), "html", null, true);
            echo "\"></time>)
\t\t\t\t\t\t\ton page <em class=\"tipped\" title=\"";
            // line 398
            if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_visitor_, "last_track"), "page_url"), "html", null, true);
            echo "\">...</em>
\t\t\t\t\t</tr>
\t\t\t\t\t<tr>
\t\t\t\t\t\t<td class=\"label\">";
            // line 401
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.landing_page");
            echo "</td>
\t\t\t\t\t\t<td class=\"value\">
\t\t\t\t\t\t\t";
            // line 403
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.people.user_landed_on_page");
            echo "
\t\t\t\t\t\t\t<em class=\"tipped\" title=\"";
            // line 404
            if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_visitor_, "initial_track"), "page_url"), "html", null, true);
            echo "\">...</em>
\t\t\t\t\t\t\t";
            // line 405
            if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
            if ($this->getAttribute($this->getAttribute($_visitor_, "visit_track"), "ref_page_url")) {
                // line 406
                echo "\t\t\t\t\t\t\t\t";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.people.and_came_from");
                echo "
\t\t\t\t\t\t\t\t<em class=\"tipped\" title=\"";
                // line 407
                if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_visitor_, "visit_track"), "ref_page_url"), "html", null, true);
                echo "\">...</em>
\t\t\t\t\t\t\t";
            }
            // line 409
            echo "\t\t\t\t\t\t</td>
\t\t\t\t\t</tr>
\t\t\t\t\t<tr>
\t\t\t\t\t\t<td class=\"label\">";
            // line 412
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ip_address");
            echo "</td>
\t\t\t\t\t\t<td class=\"value\">
\t\t\t\t\t\t\t";
            // line 414
            if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_visitor_, "ip_address"), "html", null, true);
            echo "
\t\t\t\t\t\t</td>
\t\t\t\t\t</tr>
\t\t\t\t\t<tr>
\t\t\t\t\t\t<td class=\"label\">";
            // line 418
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.browser_agent");
            echo "</td>
\t\t\t\t\t\t<td class=\"value\">
\t\t\t\t\t\t\t";
            // line 420
            if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_visitor_, "last_track"), "user_agent"), "html", null, true);
            echo "
\t\t\t\t\t\t</td>
\t\t\t\t\t</tr>
\t\t\t\t";
        }
        // line 424
        echo "\t\t\t\t<tr>
\t\t\t\t\t<td class=\"label\">Auto-responder?</td>
\t\t\t\t\t<td class=\"value\">
\t\t\t\t\t\t<select id=\"";
        // line 427
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_disable_autoresponses\">
\t\t\t\t\t\t\t<option value=\"0\" ";
        // line 428
        if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
        if ((!$this->getAttribute($_person_, "disable_autoresponses"))) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.people.user_not_autoresponder");
        echo "</option>
\t\t\t\t\t\t\t<option value=\"1\" ";
        // line 429
        if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
        if ($this->getAttribute($_person_, "disable_autoresponses")) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.people.user_is_autoresponder");
        echo "</option>
\t\t\t\t\t\t</select>
\t\t\t\t\t\t";
        // line 431
        if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
        if (($this->getAttribute($_person_, "disable_autoresponses") && $this->getAttribute($_person_, "disable_autoresponses_log"))) {
            // line 432
            echo "\t\t\t\t\t\t\t<div style=\"font-size:10px;\" id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_disable_autoresponses_reason\">
\t\t\t\t\t\t\t\t";
            // line 433
            if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_person_, "disable_autoresponses_log"), "html", null, true);
            echo "
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t";
        }
        // line 436
        echo "\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t</table>
\t\t</article>
\t\t";
        // line 441
        echo "
\t\t";
        // line 442
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgetTabsBody($_baseId_, "profile", "info", "article", $_person_api_);
        echo "
\t</section>
</div>

";
        // line 446
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["person_api"])) { $_person_api_ = $context["person_api"]; } else { $_person_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "profile", "info", "below", $_person_api_);
        echo "
";
    }

    public function getTemplateName()
    {
        return "AgentBundle:Person:view-content-col.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1394 => 446,  1385 => 442,  1382 => 441,  1369 => 433,  1363 => 432,  1360 => 431,  1350 => 429,  1305 => 412,  1300 => 409,  1281 => 404,  1277 => 403,  1265 => 398,  1255 => 396,  1247 => 393,  1234 => 387,  1202 => 375,  1199 => 374,  1187 => 372,  1162 => 365,  1136 => 355,  1128 => 352,  1122 => 350,  1069 => 332,  968 => 293,  846 => 250,  1183 => 449,  1132 => 354,  1097 => 341,  957 => 394,  907 => 277,  875 => 263,  653 => 236,  1329 => 405,  1309 => 401,  1303 => 399,  1297 => 398,  1292 => 397,  1280 => 394,  1249 => 391,  1237 => 390,  1205 => 409,  1200 => 408,  1194 => 407,  1178 => 380,  1175 => 447,  1170 => 375,  1135 => 362,  1105 => 428,  1078 => 335,  1068 => 325,  1048 => 417,  961 => 294,  922 => 280,  750 => 228,  842 => 263,  1038 => 319,  904 => 322,  882 => 301,  831 => 244,  860 => 314,  790 => 284,  733 => 296,  707 => 206,  744 => 220,  873 => 74,  824 => 256,  762 => 230,  713 => 242,  578 => 158,  2739 => 1153,  2735 => 1152,  2732 => 1151,  2725 => 1146,  2712 => 1144,  2707 => 1143,  2692 => 1131,  2688 => 1130,  2683 => 1127,  2680 => 1126,  2677 => 1125,  2671 => 1121,  2658 => 1119,  2653 => 1118,  2645 => 1113,  2641 => 1112,  2636 => 1109,  2632 => 1108,  2629 => 1107,  2626 => 1106,  2606 => 1089,  2602 => 1088,  2598 => 1087,  2594 => 1086,  2590 => 1085,  2586 => 1084,  2572 => 1073,  2568 => 1072,  2564 => 1071,  2560 => 1070,  2556 => 1069,  2552 => 1068,  2537 => 1056,  2533 => 1055,  2529 => 1054,  2525 => 1053,  2521 => 1052,  2503 => 1040,  2499 => 1039,  2495 => 1038,  2491 => 1037,  2487 => 1036,  2483 => 1035,  2469 => 1024,  2465 => 1023,  2461 => 1022,  2457 => 1021,  2453 => 1020,  2449 => 1019,  2426 => 999,  2422 => 998,  2418 => 997,  2414 => 996,  2410 => 995,  2406 => 994,  2392 => 983,  2388 => 982,  2384 => 981,  2380 => 980,  2376 => 979,  2372 => 978,  2358 => 967,  2354 => 966,  2350 => 965,  2346 => 964,  2342 => 963,  2338 => 962,  2324 => 951,  2320 => 950,  2316 => 949,  2312 => 948,  2308 => 947,  2304 => 946,  2295 => 939,  2282 => 937,  2277 => 936,  2269 => 931,  2265 => 930,  2260 => 927,  2256 => 926,  2253 => 925,  2230 => 901,  2220 => 899,  2216 => 898,  2211 => 896,  2208 => 895,  2198 => 893,  2194 => 892,  2189 => 890,  2184 => 889,  2177 => 885,  2173 => 884,  2164 => 877,  2151 => 875,  2146 => 874,  2143 => 873,  2141 => 864,  2134 => 860,  2130 => 859,  2122 => 853,  2120 => 852,  2113 => 848,  2105 => 846,  2096 => 839,  2083 => 837,  2078 => 836,  2073 => 834,  2067 => 830,  2062 => 828,  2058 => 827,  2054 => 826,  2050 => 825,  2045 => 824,  2038 => 822,  2034 => 821,  2022 => 814,  2009 => 812,  1999 => 809,  1993 => 805,  1988 => 803,  1984 => 802,  1980 => 801,  1976 => 800,  1971 => 799,  1968 => 798,  1964 => 797,  1960 => 796,  1954 => 793,  1946 => 788,  1942 => 787,  1938 => 786,  1932 => 782,  1927 => 780,  1919 => 778,  1915 => 777,  1911 => 776,  1906 => 775,  1903 => 774,  1899 => 773,  1895 => 772,  1886 => 768,  1883 => 764,  1871 => 759,  1866 => 756,  1863 => 755,  1842 => 752,  1825 => 749,  1818 => 748,  1815 => 747,  1800 => 743,  1793 => 741,  1772 => 729,  1761 => 721,  1757 => 720,  1751 => 717,  1740 => 709,  1719 => 697,  1715 => 696,  1686 => 673,  1676 => 669,  1665 => 661,  1661 => 660,  1645 => 651,  1638 => 649,  1630 => 645,  1625 => 642,  1622 => 641,  1598 => 637,  1577 => 634,  1558 => 629,  1549 => 627,  1546 => 626,  1535 => 618,  1531 => 617,  1514 => 606,  1510 => 605,  1504 => 602,  1493 => 594,  1483 => 590,  1464 => 580,  1458 => 577,  1443 => 568,  1437 => 565,  1426 => 557,  1405 => 545,  1401 => 544,  1395 => 541,  1390 => 538,  1381 => 533,  1373 => 531,  1348 => 521,  1343 => 520,  1335 => 515,  1321 => 510,  1318 => 418,  1299 => 503,  1294 => 407,  1282 => 496,  1269 => 491,  1260 => 397,  1240 => 478,  1221 => 381,  1216 => 378,  1210 => 410,  1206 => 473,  1193 => 467,  1189 => 466,  1155 => 439,  1150 => 447,  1022 => 312,  1006 => 404,  988 => 398,  969 => 392,  965 => 391,  921 => 370,  878 => 275,  866 => 349,  854 => 254,  819 => 279,  796 => 286,  1713 => 683,  1700 => 681,  1685 => 674,  1662 => 670,  1643 => 669,  1640 => 668,  1631 => 663,  1618 => 661,  1613 => 660,  1608 => 639,  1605 => 656,  1602 => 655,  1589 => 645,  1586 => 644,  1583 => 643,  1554 => 613,  1547 => 608,  1521 => 602,  1508 => 600,  1499 => 598,  1491 => 593,  1482 => 587,  1472 => 582,  1468 => 581,  1465 => 579,  1457 => 573,  1445 => 566,  1422 => 556,  1416 => 553,  1410 => 548,  1397 => 546,  1392 => 545,  1386 => 541,  1377 => 532,  1370 => 530,  1357 => 528,  1352 => 527,  1341 => 428,  1314 => 510,  1311 => 509,  1275 => 493,  1248 => 484,  1238 => 478,  1225 => 476,  1220 => 412,  1209 => 466,  1185 => 385,  1182 => 370,  1159 => 367,  1154 => 449,  1130 => 438,  1125 => 437,  1101 => 426,  1074 => 432,  1056 => 326,  1046 => 323,  1043 => 404,  1030 => 397,  1027 => 412,  947 => 361,  925 => 352,  913 => 324,  893 => 338,  881 => 267,  847 => 343,  829 => 336,  825 => 259,  1083 => 434,  995 => 300,  984 => 350,  963 => 292,  941 => 324,  851 => 367,  682 => 270,  1365 => 549,  1359 => 545,  1349 => 539,  1336 => 427,  1331 => 424,  1323 => 420,  1319 => 530,  1312 => 402,  1284 => 519,  1272 => 401,  1268 => 509,  1261 => 506,  1251 => 485,  1245 => 483,  1231 => 496,  1207 => 376,  1197 => 491,  1180 => 484,  1173 => 457,  1169 => 455,  1157 => 363,  1147 => 438,  1109 => 440,  1065 => 440,  1059 => 423,  1047 => 428,  1044 => 424,  1033 => 317,  1009 => 357,  991 => 351,  987 => 404,  973 => 294,  931 => 355,  924 => 282,  911 => 298,  906 => 81,  885 => 276,  872 => 317,  855 => 72,  749 => 264,  701 => 239,  594 => 109,  1163 => 440,  1143 => 440,  1087 => 420,  1077 => 433,  1051 => 325,  1037 => 480,  1010 => 405,  999 => 407,  932 => 326,  899 => 306,  895 => 404,  933 => 387,  914 => 133,  909 => 323,  833 => 284,  783 => 306,  755 => 248,  666 => 263,  453 => 158,  639 => 269,  568 => 176,  520 => 110,  657 => 216,  572 => 201,  609 => 216,  20 => 1,  659 => 207,  562 => 185,  548 => 185,  558 => 197,  479 => 145,  589 => 211,  457 => 153,  413 => 140,  953 => 290,  948 => 290,  935 => 394,  929 => 283,  916 => 382,  864 => 365,  844 => 338,  816 => 342,  807 => 341,  801 => 268,  774 => 257,  766 => 229,  737 => 297,  685 => 186,  664 => 225,  635 => 281,  593 => 209,  546 => 227,  532 => 223,  865 => 296,  852 => 241,  838 => 285,  820 => 201,  781 => 327,  764 => 274,  725 => 250,  632 => 268,  602 => 192,  565 => 197,  529 => 62,  505 => 156,  487 => 101,  473 => 87,  1853 => 842,  1849 => 753,  1839 => 751,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 733,  1778 => 732,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 693,  1705 => 781,  1688 => 675,  1681 => 769,  1677 => 768,  1667 => 671,  1656 => 762,  1648 => 655,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 606,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 593,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 540,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 522,  1289 => 406,  1276 => 604,  1263 => 601,  1244 => 480,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 373,  1181 => 561,  1171 => 446,  1158 => 550,  1114 => 428,  1104 => 465,  1072 => 333,  1062 => 7,  1024 => 395,  1014 => 406,  1000 => 328,  990 => 303,  980 => 297,  960 => 466,  918 => 280,  888 => 80,  834 => 307,  673 => 64,  636 => 198,  462 => 92,  454 => 138,  1144 => 358,  1139 => 356,  1131 => 399,  1127 => 434,  1110 => 347,  1092 => 459,  1089 => 426,  1086 => 520,  1084 => 337,  1063 => 387,  1060 => 321,  1055 => 422,  1050 => 373,  1035 => 372,  1019 => 330,  1003 => 309,  959 => 387,  900 => 366,  880 => 276,  870 => 297,  867 => 258,  859 => 294,  848 => 215,  839 => 376,  828 => 302,  823 => 374,  809 => 316,  800 => 241,  797 => 267,  794 => 257,  786 => 283,  740 => 78,  734 => 261,  703 => 238,  693 => 236,  630 => 278,  626 => 19,  614 => 275,  610 => 201,  581 => 206,  564 => 268,  525 => 186,  722 => 226,  697 => 282,  674 => 274,  671 => 221,  577 => 180,  569 => 233,  557 => 229,  502 => 99,  497 => 207,  445 => 196,  729 => 209,  684 => 237,  676 => 65,  669 => 220,  660 => 203,  647 => 198,  643 => 229,  601 => 306,  570 => 165,  522 => 220,  501 => 158,  296 => 157,  374 => 152,  631 => 207,  616 => 283,  608 => 194,  605 => 193,  596 => 188,  574 => 180,  561 => 231,  527 => 165,  433 => 190,  388 => 115,  426 => 147,  383 => 135,  461 => 156,  370 => 155,  395 => 131,  294 => 87,  223 => 94,  220 => 90,  492 => 175,  468 => 162,  444 => 153,  410 => 143,  397 => 134,  377 => 161,  262 => 104,  250 => 86,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 1051,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 680,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 638,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 572,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 526,  1301 => 1037,  1252 => 392,  1243 => 994,  1223 => 978,  1212 => 494,  1167 => 366,  1161 => 933,  1145 => 923,  1140 => 364,  1134 => 538,  1126 => 443,  1106 => 528,  1100 => 897,  1026 => 418,  997 => 822,  983 => 298,  975 => 296,  956 => 318,  939 => 285,  902 => 274,  894 => 364,  879 => 76,  757 => 269,  727 => 295,  716 => 235,  670 => 204,  528 => 187,  476 => 253,  435 => 150,  354 => 127,  341 => 187,  192 => 62,  321 => 114,  243 => 96,  793 => 266,  780 => 261,  758 => 229,  700 => 193,  686 => 238,  652 => 185,  638 => 226,  620 => 216,  545 => 166,  523 => 158,  494 => 134,  459 => 159,  438 => 146,  351 => 126,  347 => 127,  402 => 150,  268 => 95,  430 => 188,  411 => 182,  379 => 134,  322 => 127,  315 => 170,  289 => 130,  284 => 99,  255 => 77,  234 => 126,  1133 => 444,  1124 => 357,  1121 => 430,  1116 => 348,  1113 => 429,  1108 => 376,  1103 => 368,  1098 => 26,  1081 => 425,  1073 => 424,  1067 => 356,  1064 => 330,  1061 => 421,  1053 => 318,  1049 => 419,  1042 => 313,  1039 => 345,  1025 => 314,  1021 => 310,  1015 => 308,  1008 => 461,  996 => 406,  989 => 454,  985 => 315,  981 => 257,  977 => 321,  970 => 320,  966 => 375,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 148,  919 => 314,  917 => 279,  908 => 411,  905 => 310,  896 => 358,  891 => 378,  877 => 334,  862 => 256,  857 => 269,  837 => 261,  832 => 260,  827 => 349,  821 => 66,  803 => 179,  778 => 324,  769 => 58,  765 => 297,  753 => 54,  746 => 52,  743 => 298,  735 => 75,  730 => 251,  720 => 363,  717 => 362,  712 => 251,  691 => 233,  678 => 275,  654 => 199,  587 => 210,  576 => 201,  539 => 116,  517 => 144,  471 => 160,  441 => 195,  437 => 134,  418 => 138,  386 => 154,  373 => 109,  304 => 89,  270 => 106,  265 => 92,  229 => 91,  477 => 167,  455 => 125,  448 => 143,  429 => 141,  407 => 120,  399 => 138,  389 => 145,  375 => 141,  358 => 198,  349 => 162,  335 => 120,  327 => 130,  298 => 158,  280 => 115,  249 => 88,  194 => 82,  142 => 51,  344 => 133,  318 => 114,  306 => 115,  295 => 111,  357 => 136,  300 => 118,  286 => 151,  276 => 68,  269 => 108,  254 => 100,  128 => 22,  237 => 127,  165 => 58,  122 => 37,  798 => 281,  770 => 302,  759 => 278,  748 => 298,  731 => 260,  721 => 293,  718 => 301,  708 => 218,  696 => 236,  617 => 204,  590 => 207,  553 => 263,  550 => 157,  540 => 258,  533 => 254,  500 => 233,  493 => 155,  489 => 161,  482 => 148,  467 => 258,  464 => 209,  458 => 147,  452 => 145,  449 => 137,  415 => 83,  382 => 135,  372 => 131,  361 => 199,  356 => 102,  339 => 131,  302 => 104,  285 => 109,  258 => 76,  123 => 32,  108 => 39,  424 => 148,  394 => 214,  380 => 143,  338 => 137,  319 => 216,  316 => 123,  312 => 116,  290 => 153,  267 => 141,  206 => 57,  110 => 19,  240 => 83,  224 => 92,  219 => 73,  217 => 80,  202 => 84,  186 => 45,  170 => 28,  100 => 29,  67 => 23,  14 => 1,  1096 => 345,  1090 => 339,  1088 => 435,  1085 => 456,  1066 => 423,  1034 => 282,  1031 => 281,  1018 => 416,  1013 => 358,  1007 => 408,  1002 => 403,  993 => 305,  986 => 264,  982 => 401,  976 => 399,  971 => 295,  964 => 255,  949 => 289,  946 => 288,  940 => 388,  937 => 374,  928 => 385,  926 => 318,  915 => 279,  912 => 82,  903 => 231,  898 => 440,  892 => 303,  889 => 277,  887 => 302,  884 => 79,  876 => 75,  874 => 215,  871 => 331,  863 => 373,  861 => 270,  858 => 255,  850 => 291,  843 => 206,  840 => 406,  815 => 251,  812 => 343,  808 => 246,  804 => 258,  799 => 312,  791 => 310,  785 => 262,  775 => 82,  771 => 253,  754 => 267,  728 => 317,  726 => 72,  723 => 237,  715 => 105,  711 => 220,  709 => 222,  706 => 232,  698 => 229,  694 => 199,  692 => 189,  689 => 234,  681 => 224,  677 => 229,  675 => 234,  663 => 218,  661 => 200,  650 => 199,  646 => 231,  629 => 267,  627 => 218,  625 => 266,  622 => 285,  598 => 232,  592 => 212,  586 => 182,  575 => 232,  566 => 242,  556 => 230,  554 => 188,  541 => 176,  536 => 224,  515 => 183,  511 => 166,  509 => 179,  488 => 152,  486 => 147,  483 => 171,  465 => 198,  463 => 148,  450 => 157,  432 => 129,  419 => 143,  371 => 151,  362 => 129,  353 => 196,  337 => 112,  333 => 153,  309 => 121,  303 => 161,  299 => 88,  291 => 89,  272 => 97,  261 => 138,  253 => 109,  239 => 70,  235 => 94,  213 => 69,  200 => 82,  198 => 64,  159 => 44,  149 => 52,  146 => 21,  131 => 64,  116 => 35,  79 => 45,  74 => 26,  71 => 19,  836 => 262,  817 => 243,  814 => 319,  811 => 235,  805 => 244,  787 => 238,  779 => 169,  776 => 232,  773 => 322,  761 => 296,  751 => 302,  747 => 265,  742 => 336,  739 => 227,  736 => 215,  724 => 259,  705 => 69,  702 => 216,  688 => 226,  680 => 205,  667 => 273,  662 => 242,  656 => 418,  649 => 285,  644 => 220,  641 => 220,  624 => 206,  613 => 166,  607 => 200,  597 => 275,  591 => 49,  584 => 236,  579 => 181,  563 => 175,  559 => 68,  551 => 243,  547 => 169,  537 => 115,  524 => 112,  512 => 174,  507 => 237,  504 => 159,  498 => 213,  485 => 172,  480 => 50,  472 => 96,  466 => 149,  460 => 142,  447 => 156,  442 => 40,  434 => 133,  428 => 149,  422 => 145,  404 => 177,  368 => 108,  364 => 200,  340 => 100,  334 => 135,  330 => 115,  325 => 98,  292 => 150,  287 => 85,  282 => 108,  279 => 104,  273 => 120,  266 => 104,  256 => 101,  252 => 87,  228 => 93,  218 => 62,  201 => 66,  64 => 7,  51 => 13,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 847,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 823,  2037 => 789,  2031 => 788,  2028 => 818,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 811,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 779,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 769,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 744,  1801 => 695,  1798 => 694,  1795 => 742,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 708,  1730 => 705,  1720 => 688,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 672,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 659,  1651 => 656,  1641 => 631,  1637 => 667,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 651,  1592 => 614,  1587 => 613,  1584 => 635,  1581 => 731,  1576 => 608,  1574 => 633,  1567 => 603,  1563 => 630,  1559 => 601,  1552 => 628,  1548 => 597,  1542 => 593,  1529 => 605,  1525 => 614,  1516 => 708,  1512 => 583,  1503 => 599,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 569,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 537,  1380 => 539,  1376 => 436,  1371 => 645,  1367 => 527,  1364 => 518,  1361 => 523,  1355 => 543,  1342 => 624,  1337 => 510,  1328 => 516,  1324 => 511,  1317 => 511,  1313 => 499,  1310 => 414,  1304 => 504,  1291 => 502,  1286 => 405,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 487,  1262 => 490,  1257 => 489,  1254 => 482,  1250 => 394,  1246 => 477,  1239 => 389,  1235 => 498,  1232 => 416,  1226 => 383,  1213 => 466,  1208 => 465,  1201 => 472,  1196 => 464,  1192 => 490,  1188 => 456,  1184 => 485,  1179 => 448,  1176 => 461,  1172 => 367,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 561,  1149 => 359,  1146 => 444,  1137 => 249,  1123 => 442,  1119 => 354,  1115 => 424,  1111 => 377,  1107 => 346,  1102 => 344,  1099 => 347,  1095 => 419,  1091 => 418,  1082 => 455,  1079 => 524,  1076 => 359,  1070 => 431,  1057 => 491,  1052 => 406,  1045 => 484,  1040 => 416,  1036 => 283,  1032 => 415,  1028 => 312,  1023 => 393,  1020 => 311,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 304,  998 => 308,  992 => 406,  979 => 297,  974 => 400,  967 => 399,  962 => 397,  958 => 336,  954 => 293,  950 => 393,  945 => 391,  942 => 288,  938 => 323,  934 => 284,  927 => 282,  923 => 382,  920 => 412,  910 => 278,  901 => 340,  897 => 273,  890 => 271,  886 => 270,  883 => 268,  868 => 375,  856 => 293,  853 => 319,  849 => 264,  845 => 290,  841 => 249,  835 => 245,  830 => 249,  826 => 282,  822 => 281,  818 => 65,  813 => 242,  810 => 290,  806 => 270,  802 => 339,  795 => 241,  792 => 335,  789 => 233,  784 => 286,  782 => 237,  777 => 255,  772 => 289,  768 => 81,  763 => 327,  760 => 256,  756 => 255,  752 => 247,  745 => 245,  741 => 218,  738 => 254,  732 => 171,  719 => 245,  714 => 251,  710 => 200,  704 => 281,  699 => 215,  695 => 66,  690 => 211,  687 => 210,  683 => 346,  679 => 298,  672 => 179,  668 => 264,  665 => 229,  658 => 178,  645 => 221,  640 => 227,  634 => 218,  628 => 174,  623 => 217,  619 => 78,  611 => 282,  606 => 280,  603 => 199,  599 => 194,  595 => 213,  583 => 159,  580 => 45,  573 => 157,  560 => 267,  543 => 146,  538 => 164,  534 => 189,  530 => 174,  526 => 221,  521 => 287,  518 => 157,  514 => 202,  510 => 153,  503 => 59,  496 => 163,  490 => 149,  484 => 146,  474 => 127,  470 => 168,  446 => 122,  440 => 135,  436 => 189,  431 => 145,  425 => 128,  416 => 125,  412 => 76,  408 => 165,  403 => 134,  400 => 119,  396 => 117,  392 => 116,  385 => 146,  381 => 113,  367 => 123,  363 => 105,  359 => 147,  355 => 146,  350 => 120,  346 => 124,  343 => 140,  328 => 118,  324 => 120,  313 => 122,  307 => 115,  301 => 119,  288 => 152,  283 => 84,  271 => 102,  257 => 100,  251 => 100,  238 => 95,  233 => 94,  195 => 30,  191 => 81,  187 => 61,  183 => 51,  130 => 35,  88 => 36,  76 => 44,  115 => 20,  95 => 29,  655 => 202,  651 => 176,  648 => 215,  637 => 218,  633 => 197,  621 => 462,  618 => 179,  615 => 196,  604 => 214,  600 => 233,  588 => 206,  585 => 295,  582 => 205,  571 => 179,  567 => 200,  555 => 172,  552 => 171,  549 => 170,  544 => 230,  542 => 166,  535 => 177,  531 => 162,  519 => 173,  516 => 218,  513 => 154,  508 => 215,  506 => 151,  499 => 177,  495 => 150,  491 => 163,  481 => 161,  478 => 128,  475 => 147,  469 => 182,  456 => 159,  451 => 195,  443 => 136,  439 => 152,  427 => 155,  423 => 114,  420 => 127,  409 => 160,  405 => 218,  401 => 176,  391 => 138,  387 => 334,  384 => 250,  378 => 205,  365 => 153,  360 => 104,  348 => 116,  336 => 130,  332 => 99,  329 => 125,  323 => 118,  310 => 93,  305 => 120,  277 => 79,  274 => 107,  263 => 77,  259 => 102,  247 => 99,  244 => 72,  241 => 129,  222 => 69,  210 => 32,  207 => 67,  204 => 64,  184 => 59,  181 => 58,  167 => 66,  157 => 71,  96 => 34,  421 => 147,  417 => 146,  414 => 145,  406 => 141,  398 => 158,  393 => 125,  390 => 212,  376 => 138,  369 => 124,  366 => 150,  352 => 135,  345 => 101,  342 => 122,  331 => 126,  326 => 137,  320 => 117,  317 => 171,  314 => 112,  311 => 122,  308 => 116,  297 => 117,  293 => 114,  281 => 106,  278 => 145,  275 => 103,  264 => 92,  260 => 96,  248 => 97,  245 => 91,  242 => 84,  231 => 125,  227 => 92,  215 => 83,  212 => 86,  209 => 111,  197 => 83,  177 => 56,  171 => 71,  161 => 51,  132 => 44,  121 => 44,  105 => 32,  99 => 52,  81 => 35,  77 => 22,  180 => 72,  176 => 49,  156 => 50,  143 => 45,  139 => 39,  118 => 65,  189 => 72,  185 => 79,  173 => 56,  166 => 53,  152 => 69,  174 => 41,  164 => 94,  154 => 90,  150 => 47,  137 => 42,  133 => 41,  127 => 62,  107 => 24,  102 => 17,  83 => 24,  78 => 27,  53 => 21,  23 => 3,  42 => 9,  138 => 34,  134 => 46,  109 => 33,  103 => 39,  97 => 28,  94 => 15,  84 => 25,  75 => 23,  69 => 21,  66 => 13,  54 => 13,  44 => 11,  230 => 64,  226 => 90,  203 => 66,  193 => 80,  188 => 80,  182 => 78,  178 => 76,  168 => 74,  163 => 52,  160 => 72,  155 => 45,  148 => 67,  145 => 30,  140 => 27,  136 => 53,  125 => 38,  120 => 39,  113 => 26,  101 => 36,  92 => 42,  89 => 25,  85 => 13,  73 => 9,  62 => 11,  59 => 10,  56 => 19,  41 => 10,  126 => 42,  119 => 61,  111 => 39,  106 => 31,  98 => 53,  93 => 18,  86 => 16,  70 => 15,  60 => 31,  28 => 2,  36 => 11,  114 => 37,  104 => 37,  91 => 24,  80 => 12,  63 => 22,  58 => 12,  40 => 8,  34 => 7,  45 => 15,  61 => 17,  55 => 14,  48 => 15,  39 => 9,  35 => 6,  31 => 4,  26 => 4,  21 => 2,  46 => 9,  29 => 2,  57 => 6,  50 => 8,  47 => 12,  38 => 4,  33 => 10,  49 => 10,  32 => 3,  246 => 75,  236 => 95,  232 => 74,  225 => 59,  221 => 87,  216 => 88,  214 => 114,  211 => 84,  208 => 68,  205 => 83,  199 => 58,  196 => 64,  190 => 79,  179 => 76,  175 => 55,  172 => 56,  169 => 54,  162 => 42,  158 => 92,  153 => 24,  151 => 49,  147 => 47,  144 => 82,  141 => 41,  135 => 65,  129 => 40,  124 => 31,  117 => 43,  112 => 29,  90 => 17,  87 => 25,  82 => 21,  72 => 15,  68 => 8,  65 => 19,  52 => 18,  43 => 8,  37 => 5,  30 => 5,  27 => 3,  25 => 4,  24 => 4,  22 => 2,  19 => 2,);
    }
}
