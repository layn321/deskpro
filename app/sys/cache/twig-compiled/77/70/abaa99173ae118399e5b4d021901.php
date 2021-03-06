<?php

/* DeskPRO:emails_common:ticket-log-actiontext.html.twig */
class __TwigTemplate_7770abaa99173ae118399e5b4d021901 extends \Application\DeskPRO\Twig\Template
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
        // line 1
        $context["layout"] = $this->env->loadTemplate("DeskPRO:emails_common:layout-macros.html.twig");
        // line 2
        ob_start();
        // line 3
        echo "\t";
        if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
        if (isset($context["hide_unknow"])) { $_hide_unknow_ = $context["hide_unknow"]; } else { $_hide_unknow_ = null; }
        if (($this->getAttribute($_log_, "action_type") == "free")) {
            // line 4
            echo "\t\t";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "message"), "html", null, true);
            echo "
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_agent")) {
            // line 6
            echo "\t\t<span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("type");
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.assigned_agent");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t\t<span style=\"";
            // line 7
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("old-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_agent_name", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_agent_name"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.unassigned"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.unassigned"))), "html", null, true);
            echo "</span>
\t\t";
            // line 8
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("new-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_agent_name", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_agent_name"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.unassigned"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.unassigned"))), "html", null, true);
            echo "</span>
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_agent_team")) {
            // line 10
            echo "\t\t<span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("type");
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.assigned_team");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t\t<span style=\"";
            // line 11
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("old-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_agent_team_name", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_agent_team_name"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span>
\t\t";
            // line 12
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("new-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_agent_team_name", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_agent_team_name"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span>
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "participant_added")) {
            // line 14
            echo "\t\t<span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("type");
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.participant_added");
            echo "</span>: <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("new-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "email"), "html", null, true);
            echo "</span>
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "participant_removed")) {
            // line 16
            echo "\t\t<span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("type");
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.participant_removed");
            echo "</span>: <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("old-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "email"), "html", null, true);
            echo "</span>
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "ticket_created")) {
            // line 18
            echo "\t\t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ticket");
            echo " <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("new-val");
            echo "\">ID ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "ticket_id"), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.lc_created");
            echo "
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "subject")) {
            // line 20
            echo "\t\t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.subject_changed_from");
            echo " <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("old-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "old_subject"), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("new-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "new_subject"), "html", null, true);
            echo "</span>
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "ticket_split")) {
            // line 22
            echo "\t\t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.ticket_split_from");
            echo " <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("old-val");
            echo "\">ID ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "from_ticket_id"), "html", null, true);
            echo "</span>
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "ticket_split_to")) {
            // line 24
            echo "    \t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.ticket_split_to");
            echo " <span class=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("old-val");
            echo "\">ID ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "to_ticket_id"), "html", null, true);
            echo "</span> (";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.x_messages_moved", array("count" => (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "messages_moved", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "messages_moved"), 0)) : (0))));
            echo ")
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "merged")) {
            // line 26
            echo "\t\t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ticket");
            echo " <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("old-val");
            echo "\">ID ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_log_, "id_before"), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.merged_into_this");
            echo "
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "merged_message")) {
            // line 28
            echo "\t\t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.message");
            echo " <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("old-val");
            echo "\">ID ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "message_id"), "html", null, true);
            echo "</span> ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.into_this_from_ticket", array("id" => $this->getAttribute($this->getAttribute($_log_, "details"), "old_ticket_id")));
            echo "
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "merged_attach")) {
            // line 30
            echo "\t\t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.attachment");
            echo " <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("old-val");
            echo "\">ID ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_log_, "id_object"), "html", null, true);
            echo "</span> ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.into_this_from_ticket", array("id" => $this->getAttribute($_log_, "id_before")));
            echo "
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "labels_added")) {
            // line 32
            echo "\t\tLabels added: <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("new-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->implodeArray($this->getAttribute($this->getAttribute($_log_, "details"), "labels"), ", "), "html", null, true);
            echo "</span>
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "labels_removed")) {
            // line 34
            echo "\t\tLabels removed: <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("new-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->implodeArray($this->getAttribute($this->getAttribute($_log_, "details"), "labels"), ", "), "html", null, true);
            echo "</span>
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_category")) {
            // line 36
            echo "\t\t<span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("type");
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.category");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t\t<span style=\"";
            // line 37
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("old-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_category_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_category_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("new-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_category_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_category_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.department"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.department"))), "html", null, true);
            echo "</span>
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_department")) {
            // line 39
            echo "\t\t<span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("type");
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.department");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t\t<span style=\"";
            // line 40
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("old-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_department_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_department_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("new-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_department_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_department_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.organization"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.organization"))), "html", null, true);
            echo "</span>
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_language")) {
            // line 42
            echo "\t\t<span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("type");
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.language");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t\t<span style=\"";
            // line 43
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("old-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_language_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_language_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("new-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_language_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_language_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.organization"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.organization"))), "html", null, true);
            echo "</span>
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_organization")) {
            // line 45
            echo "\t\t<span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("type");
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.organization");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t\t<span style=\"";
            // line 46
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("old-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_org_name", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_org_name"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("new-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_org_name", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_org_name"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span>
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_subject")) {
            // line 48
            echo "\t\t<span class=\"type\">Subject</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t\t<span class=\"old-val\">";
            // line 49
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_subject", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_subject"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_subject", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_subject"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span>
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_person")) {
            // line 51
            echo "\t\t<span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("type");
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.api.user_owner");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t\t<span style=\"";
            // line 52
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("old-val");
            echo "\">ID ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "old_person_id"), "html", null, true);
            echo " ";
            if (isset($context["old_person_name"])) { $_old_person_name_ = $context["old_person_name"]; } else { $_old_person_name_ = null; }
            echo twig_escape_filter($this->env, $_old_person_name_, "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("new-val");
            echo "\">ID ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "new_person_id"), "html", null, true);
            echo " ";
            if (isset($context["new_person_name"])) { $_new_person_name_ = $context["new_person_name"]; } else { $_new_person_name_ = null; }
            echo twig_escape_filter($this->env, $_new_person_name_, "html", null, true);
            echo "</span>
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "executed_triggers")) {
            // line 54
            echo "\t\t<span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("type");
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ticket_triggers");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.applied");
            echo ":
\t\t<span style=\"";
            // line 55
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("new-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "trigger_titles"), "html", null, true);
            echo "</span>
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "executed_escalations")) {
            // line 57
            echo "\t\t<span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("type");
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ticket_escalations");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.applied");
            echo ":
\t\t<span style=\"";
            // line 58
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("new-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "trigger_titles"), "html", null, true);
            echo "</span>
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "message_created")) {
            // line 60
            echo "\t\t<span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("type");
            echo "\">
\t\t\t";
            // line 61
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ($this->getAttribute($this->getAttribute($_log_, "details"), "is_agent_note")) {
                // line 62
                echo "\t\t\t\t";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.new_note");
                echo "
\t\t\t";
            } else {
                // line 64
                echo "\t\t\t\t";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.new_message");
                echo "
\t\t\t";
            }
            // line 66
            echo "\t\t</span>
\t\t";
            // line 67
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ($this->getAttribute($this->getAttribute($_log_, "details"), "message_id")) {
                echo "<span style=\"";
                if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
                echo $_layout_->getactiontext_style("new-val");
                echo "\">ID ";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "message_id"), "html", null, true);
                echo "</span>";
            }
            // line 68
            echo "\t\t";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ($this->getAttribute($this->getAttribute($_log_, "details"), "ip_address")) {
                // line 69
                echo "\t\t";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.from_web", array("address" => $this->getAttribute($this->getAttribute($_log_, "details"), "ip_address")));
                echo "
\t\t";
            }
            // line 71
            echo "\t\t";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ($this->getAttribute($this->getAttribute($_log_, "details"), "email")) {
                // line 72
                echo "\t\t";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.from_email", array("address" => $this->getAttribute($this->getAttribute($_log_, "details"), "email")));
                echo "
\t\t";
            }
            // line 74
            echo "\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "message_removed")) {
            // line 75
            echo "\t\t";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ($this->getAttribute($this->getAttribute($_log_, "details"), "is_agent_note")) {
                // line 76
                echo "\t\t\t<span class=\"";
                if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
                echo $_layout_->getactiontext_style("type");
                echo "\">Note deleted</span>
\t\t";
            } else {
                // line 78
                echo "\t\t\t<span class=\"";
                if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
                echo $_layout_->getactiontext_style("type");
                echo "\">Message deleted</span>
\t\t";
            }
            // line 80
            echo "\t\t<span class=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("old-val");
            echo "l\">ID ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "message_id"), "html", null, true);
            echo "</span>
\t\t";
            // line 81
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ($this->getAttribute($this->getAttribute($_log_, "details"), "is_agent_message")) {
                // line 82
                echo "\t\t\t(written by agent ";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "person_name"), "html", null, true);
                echo ")
\t\t";
            } else {
                // line 84
                echo "\t\t\t(written by ";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "person_name"), "html", null, true);
                echo ", ID ";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "person_id"), "html", null, true);
                echo ")
\t\t";
            }
            // line 86
            echo "\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_priority")) {
            // line 87
            echo "\t\t<span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("type");
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.priority");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t\t<span style=\"";
            // line 88
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("old-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_priority_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_priority_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("new-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_priority_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_priority_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.workflow"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.workflow"))), "html", null, true);
            echo "</span>
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_workflow")) {
            // line 90
            echo "\t\t<span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("type");
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.workflow");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t\t<span style=\"";
            // line 91
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("old-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_workflow_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_workflow_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("new-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_workflow_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_workflow_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.urgency"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.urgency"))), "html", null, true);
            echo "</span>
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_urgency")) {
            // line 93
            echo "\t\t<span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("type");
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.urgency");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t\t<span style=\"";
            // line 94
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("old-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "old_urgency"), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("new-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "new_urgency"), "html", null, true);
            echo "</span>
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_product")) {
            // line 96
            echo "\t\t<span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("type");
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.product");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t\t<span style=\"";
            // line 97
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("old-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_product_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_product_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("new-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_product_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_product_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.status"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.status"))), "html", null, true);
            echo "</span>
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "attach_added")) {
            // line 99
            echo "\t\t<span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("type");
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.new_attachment");
            echo "</span> <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("new-val");
            echo "\">ID ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "attach_id"), "html", null, true);
            echo " ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "filename"), "html", null, true);
            echo "</span>
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "attach_removed")) {
            // line 101
            echo "\t\t<span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("type");
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.attachment_deleted");
            echo "</span> <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("old-val");
            echo "\">ID ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "old_attach_id"), "html", null, true);
            echo " ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "filename"), "html", null, true);
            echo "</span>
\t\t";
            // line 102
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ($this->getAttribute($this->getAttribute($_log_, "details"), "message_id")) {
                // line 103
                echo "\t\t\tfrom message <span class=\"";
                if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
                echo $_layout_->getactiontext_style("old-val");
                echo "\">ID ";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "message_id"), "html", null, true);
                echo "</span>
\t\t\t";
                // line 104
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                if ($this->getAttribute($this->getAttribute($_log_, "details"), "message_person_name")) {
                    echo "written by ";
                    if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "message_person_name"), "html", null, true);
                }
                // line 105
                echo "\t\t";
            }
            // line 106
            echo "\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_status")) {
            // line 107
            echo "\t\t<span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("type");
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.status");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t\t<span style=\"";
            // line 108
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("old-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, ("agent.tickets.status_" . strtr($this->getAttribute($this->getAttribute($_log_, "details"), "old_status"), array("." => "_"))));
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("new-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, ("agent.tickets.status_" . strtr($this->getAttribute($this->getAttribute($_log_, "details"), "new_status"), array("." => "_"))));
            echo "</span>
\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_hold")) {
            // line 110
            echo "\t\t";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ($this->getAttribute($this->getAttribute($_log_, "details"), "is_hold")) {
                echo "Ticket put on hold";
            } else {
                echo "Ticket no longer on hold";
            }
            // line 111
            echo "\t";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_custom_field")) {
            // line 112
            echo "\t\t<span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("type");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "field_name"), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t\t<span style=\"";
            // line 113
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("old-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "value_before", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "value_before"), "(no value)")) : ("(no value)")), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("new-val");
            echo "\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "value_after", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "value_after"), "(no value)")) : ("(no value)")), "html", null, true);
            echo "</span>
\t";
        } elseif ($_hide_unknow_) {
            // line 115
            echo "\t\t";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_log_, "action_type"), "html", null, true);
            echo "
\t";
        }
        $context["log_row"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 118
        if (isset($context["log_row"])) { $_log_row_ = $context["log_row"]; } else { $_log_row_ = null; }
        if (twig_length_filter($this->env, $this->env->getExtension('deskpro_templating')->strTrim($_log_row_))) {
            // line 119
            echo "<div style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo $_layout_->getactiontext_style("row");
            echo "\">
\t&mdash; ";
            // line 120
            if (isset($context["log_row"])) { $_log_row_ = $context["log_row"]; } else { $_log_row_ = null; }
            echo $_log_row_;
            echo "
</div>
";
        }
    }

    public function getTemplateName()
    {
        return "DeskPRO:emails_common:ticket-log-actiontext.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  798 => 119,  770 => 113,  759 => 112,  748 => 110,  731 => 108,  721 => 107,  718 => 106,  708 => 104,  696 => 102,  617 => 94,  590 => 91,  553 => 87,  550 => 86,  540 => 84,  533 => 82,  500 => 74,  493 => 72,  489 => 71,  482 => 69,  467 => 67,  464 => 66,  458 => 64,  452 => 62,  449 => 61,  415 => 55,  382 => 52,  372 => 51,  361 => 49,  356 => 48,  339 => 46,  302 => 42,  285 => 40,  258 => 37,  123 => 18,  108 => 16,  424 => 57,  394 => 86,  380 => 80,  338 => 67,  319 => 66,  316 => 65,  312 => 43,  290 => 60,  267 => 57,  206 => 43,  110 => 22,  240 => 60,  224 => 54,  219 => 51,  217 => 50,  202 => 44,  186 => 39,  170 => 34,  100 => 22,  67 => 10,  14 => 1,  1096 => 291,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 276,  1013 => 275,  1007 => 274,  1002 => 272,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 251,  940 => 249,  937 => 248,  928 => 244,  926 => 243,  915 => 237,  912 => 236,  903 => 231,  898 => 230,  892 => 229,  889 => 228,  887 => 227,  884 => 226,  876 => 222,  874 => 221,  871 => 220,  863 => 214,  861 => 213,  858 => 212,  850 => 208,  843 => 206,  840 => 205,  815 => 201,  812 => 200,  808 => 199,  804 => 120,  799 => 196,  791 => 190,  785 => 188,  775 => 184,  771 => 183,  754 => 176,  728 => 170,  726 => 169,  723 => 168,  715 => 105,  711 => 163,  709 => 162,  706 => 161,  698 => 157,  694 => 156,  692 => 155,  689 => 154,  681 => 150,  677 => 149,  675 => 148,  663 => 143,  661 => 99,  650 => 137,  646 => 136,  629 => 129,  627 => 128,  625 => 127,  622 => 126,  598 => 119,  592 => 117,  586 => 115,  575 => 108,  566 => 103,  556 => 100,  554 => 99,  541 => 92,  536 => 91,  515 => 86,  511 => 85,  509 => 84,  488 => 79,  486 => 78,  483 => 77,  465 => 73,  463 => 72,  450 => 65,  432 => 60,  419 => 57,  371 => 46,  362 => 43,  353 => 39,  337 => 37,  333 => 35,  309 => 63,  303 => 31,  299 => 30,  291 => 28,  272 => 21,  261 => 16,  253 => 14,  239 => 7,  235 => 6,  213 => 30,  200 => 50,  198 => 28,  159 => 204,  149 => 187,  146 => 29,  131 => 44,  116 => 32,  79 => 15,  74 => 11,  71 => 9,  836 => 320,  817 => 319,  814 => 318,  811 => 317,  805 => 313,  787 => 115,  779 => 306,  776 => 305,  773 => 301,  761 => 296,  751 => 175,  747 => 293,  742 => 292,  739 => 291,  736 => 287,  724 => 282,  705 => 278,  702 => 277,  688 => 265,  680 => 263,  667 => 261,  662 => 260,  656 => 259,  649 => 258,  644 => 97,  641 => 134,  624 => 240,  613 => 231,  607 => 93,  597 => 225,  591 => 222,  584 => 218,  579 => 216,  563 => 88,  559 => 208,  551 => 98,  547 => 205,  537 => 201,  524 => 191,  512 => 185,  507 => 76,  504 => 182,  498 => 178,  485 => 176,  480 => 175,  472 => 171,  466 => 169,  460 => 71,  447 => 163,  442 => 162,  434 => 58,  428 => 156,  422 => 152,  404 => 149,  368 => 136,  364 => 134,  340 => 131,  334 => 130,  330 => 129,  325 => 128,  292 => 116,  287 => 115,  282 => 112,  279 => 111,  273 => 107,  266 => 106,  256 => 15,  252 => 102,  228 => 32,  218 => 287,  201 => 91,  64 => 10,  51 => 6,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 793,  2042 => 790,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 774,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 745,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 707,  1829 => 706,  1827 => 705,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 689,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 676,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 666,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 638,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 617,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 611,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 602,  1559 => 601,  1552 => 598,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 584,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 573,  1477 => 567,  1473 => 566,  1467 => 562,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 520,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 511,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 481,  1257 => 480,  1254 => 479,  1250 => 478,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 460,  1196 => 458,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 452,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 445,  1146 => 444,  1137 => 437,  1123 => 426,  1119 => 425,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 421,  1099 => 420,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 412,  1076 => 411,  1070 => 407,  1057 => 405,  1052 => 404,  1045 => 399,  1040 => 397,  1036 => 283,  1032 => 395,  1028 => 394,  1023 => 393,  1020 => 392,  1016 => 391,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 381,  979 => 379,  974 => 261,  967 => 373,  962 => 371,  958 => 370,  954 => 253,  950 => 368,  945 => 367,  942 => 250,  938 => 365,  934 => 364,  927 => 361,  923 => 242,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 341,  868 => 328,  856 => 323,  853 => 319,  849 => 318,  845 => 207,  841 => 321,  835 => 312,  830 => 310,  826 => 309,  822 => 308,  818 => 307,  813 => 306,  810 => 305,  806 => 304,  802 => 197,  795 => 118,  792 => 311,  789 => 298,  784 => 295,  782 => 187,  777 => 291,  772 => 289,  768 => 182,  763 => 287,  760 => 178,  756 => 111,  752 => 284,  745 => 281,  741 => 280,  738 => 279,  732 => 171,  719 => 273,  714 => 280,  710 => 279,  704 => 267,  699 => 103,  695 => 264,  690 => 263,  687 => 262,  683 => 261,  679 => 101,  672 => 147,  668 => 256,  665 => 255,  658 => 141,  645 => 248,  640 => 247,  634 => 96,  628 => 240,  623 => 238,  619 => 237,  611 => 235,  606 => 234,  603 => 120,  599 => 232,  595 => 231,  583 => 114,  580 => 90,  573 => 221,  560 => 101,  543 => 204,  538 => 209,  534 => 208,  530 => 81,  526 => 89,  521 => 80,  518 => 204,  514 => 78,  510 => 202,  503 => 75,  496 => 197,  490 => 193,  484 => 192,  474 => 190,  470 => 188,  446 => 64,  440 => 184,  436 => 61,  431 => 157,  425 => 178,  416 => 175,  412 => 55,  408 => 173,  403 => 172,  400 => 53,  396 => 51,  392 => 169,  385 => 166,  381 => 48,  367 => 45,  363 => 155,  359 => 154,  355 => 153,  350 => 150,  346 => 149,  343 => 148,  328 => 139,  324 => 138,  313 => 134,  307 => 132,  301 => 61,  288 => 27,  283 => 125,  271 => 119,  257 => 114,  251 => 13,  238 => 34,  233 => 52,  195 => 42,  191 => 45,  187 => 42,  183 => 87,  130 => 58,  88 => 18,  76 => 14,  115 => 27,  95 => 16,  655 => 233,  651 => 232,  648 => 231,  637 => 223,  633 => 130,  621 => 219,  618 => 218,  615 => 121,  604 => 209,  600 => 226,  588 => 116,  585 => 204,  582 => 203,  571 => 104,  567 => 210,  555 => 207,  552 => 190,  549 => 215,  544 => 186,  542 => 185,  535 => 181,  531 => 90,  519 => 189,  516 => 176,  513 => 175,  508 => 172,  506 => 83,  499 => 198,  495 => 166,  491 => 165,  481 => 162,  478 => 68,  475 => 160,  469 => 170,  456 => 154,  451 => 186,  443 => 60,  439 => 147,  427 => 89,  423 => 58,  420 => 176,  409 => 54,  405 => 54,  401 => 132,  391 => 129,  387 => 49,  384 => 139,  378 => 138,  365 => 78,  360 => 120,  348 => 114,  336 => 111,  332 => 140,  329 => 45,  323 => 105,  310 => 133,  305 => 62,  277 => 23,  274 => 91,  263 => 54,  259 => 82,  247 => 79,  244 => 53,  241 => 77,  222 => 98,  210 => 46,  207 => 96,  204 => 92,  184 => 26,  181 => 52,  167 => 212,  157 => 22,  96 => 26,  421 => 153,  417 => 152,  414 => 151,  406 => 148,  398 => 144,  393 => 144,  390 => 143,  376 => 79,  369 => 137,  366 => 136,  352 => 115,  345 => 132,  342 => 72,  331 => 129,  326 => 128,  320 => 137,  317 => 124,  314 => 33,  311 => 122,  308 => 123,  297 => 97,  293 => 29,  281 => 93,  278 => 110,  275 => 39,  264 => 58,  260 => 115,  248 => 36,  245 => 96,  242 => 95,  231 => 87,  227 => 86,  215 => 83,  212 => 82,  209 => 81,  197 => 49,  177 => 41,  171 => 70,  161 => 60,  132 => 27,  121 => 23,  105 => 40,  99 => 114,  81 => 15,  77 => 11,  180 => 38,  176 => 41,  156 => 64,  143 => 30,  139 => 175,  118 => 25,  189 => 40,  185 => 236,  173 => 35,  166 => 68,  152 => 34,  174 => 40,  164 => 211,  154 => 33,  150 => 55,  137 => 20,  133 => 26,  127 => 42,  107 => 42,  102 => 22,  83 => 19,  78 => 21,  53 => 8,  23 => 3,  42 => 3,  138 => 28,  134 => 40,  109 => 25,  103 => 22,  97 => 20,  94 => 19,  84 => 15,  75 => 17,  69 => 11,  66 => 11,  54 => 7,  44 => 4,  230 => 5,  226 => 68,  203 => 260,  193 => 242,  188 => 84,  182 => 235,  178 => 71,  168 => 64,  163 => 35,  160 => 77,  155 => 55,  148 => 56,  145 => 43,  140 => 28,  136 => 27,  125 => 24,  120 => 51,  113 => 17,  101 => 32,  92 => 25,  89 => 17,  85 => 17,  73 => 13,  62 => 5,  59 => 8,  56 => 6,  41 => 3,  126 => 160,  119 => 147,  111 => 20,  106 => 24,  98 => 20,  93 => 14,  86 => 25,  70 => 12,  60 => 9,  28 => 4,  36 => 3,  114 => 141,  104 => 27,  91 => 19,  80 => 15,  63 => 7,  58 => 8,  40 => 82,  34 => 59,  45 => 7,  61 => 9,  55 => 7,  48 => 5,  39 => 4,  35 => 6,  31 => 48,  26 => 2,  21 => 2,  46 => 4,  29 => 4,  57 => 5,  50 => 4,  47 => 4,  38 => 2,  33 => 1,  49 => 5,  32 => 1,  246 => 90,  236 => 59,  232 => 54,  225 => 3,  221 => 288,  216 => 52,  214 => 98,  211 => 272,  208 => 46,  205 => 269,  199 => 91,  196 => 71,  190 => 241,  179 => 66,  175 => 220,  172 => 219,  169 => 24,  162 => 35,  158 => 33,  153 => 45,  151 => 193,  147 => 31,  144 => 182,  141 => 29,  135 => 51,  129 => 25,  124 => 41,  117 => 36,  112 => 26,  90 => 21,  87 => 17,  82 => 12,  72 => 13,  68 => 8,  65 => 23,  52 => 7,  43 => 5,  37 => 74,  30 => 3,  27 => 2,  25 => 30,  24 => 2,  22 => 18,  19 => 1,);
    }
}
