<?php

/* ReportBundle:AgentActivity:ticket-log-actiontext.html.twig */
class __TwigTemplate_4cd0bdace686b4d958e35e12508845e3 extends \Application\DeskPRO\Twig\Template
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
        if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
        if (isset($context["hide_unknown"])) { $_hide_unknown_ = $context["hide_unknown"]; } else { $_hide_unknown_ = null; }
        if (($this->getAttribute($_log_, "action_type") == "free")) {
            // line 2
            echo "    ";
            if (isset($context["hide_unknown"])) { $_hide_unknown_ = $context["hide_unknown"]; } else { $_hide_unknown_ = null; }
            if ($_hide_unknown_) {
            } else {
                // line 3
                echo "        ";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "message"), "html", null, true);
                echo "
    ";
            }
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_agent")) {
            // line 6
            echo "    ";
        } elseif (($this->getAttribute($_log_, "action_type") == "participant_added")) {
            // line 8
            echo "    <span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.participant_added");
            echo "</span>: <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "name"), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "participant_removed")) {
            // line 10
            echo "    <span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.participant_removed");
            echo "</span>: <span class=\"old-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "name"), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "ticket_created")) {
            // line 12
            echo "    ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ticket");
            echo " <span class=\"new-val\">#";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "ticket_id"), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ticket");
            echo "
";
        } elseif (($this->getAttribute($_log_, "action_type") == "subject")) {
            // line 14
            echo "    ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.subject_changed_from");
            echo " <span class=\"old-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "old_subject"), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "new_subject"), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "ticket_split")) {
            // line 16
            echo "    ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.ticket_split_from");
            echo " <span class=\"old-val\">#";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "from_ticket_id"), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "ticket_split_to")) {
            // line 18
            echo "    ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.ticket_split_to");
            echo " <span class=\"old-val\">ID ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "to_ticket_id"), "html", null, true);
            echo "</span> (";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.x_messages_moved", array("count" => (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "messages_moved", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "messages_moved"), 0)) : (0))));
            echo ")
";
        } elseif (($this->getAttribute($_log_, "action_type") == "merged")) {
            // line 20
            echo "    ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ticket");
            echo " <span class=\"old-val\">#";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_log_, "id_before"), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.merged_into_this");
            echo "
";
        } elseif (($this->getAttribute($_log_, "action_type") == "merged_message")) {
            // line 22
            echo "    ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.message");
            echo " <span class=\"old-val\">#";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "message_id"), "html", null, true);
            echo "</span> ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.into_this_from_ticket", array("id" => $this->getAttribute($this->getAttribute($_log_, "details"), "old_ticket_id")));
            echo "
";
        } elseif (($this->getAttribute($_log_, "action_type") == "merged_attach")) {
            // line 24
            echo "    ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.attachment");
            echo " <span class=\"old-val\">#";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_log_, "id_object"), "html", null, true);
            echo "</span> ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.into_this_from_ticket", array("id" => $this->getAttribute($_log_, "id_before")));
            echo "
";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_category")) {
            // line 26
            echo "    <span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.category");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
    <span class=\"old-val\">";
            // line 27
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_category_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_category_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_category_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_category_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.department"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.department"))), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_department")) {
            // line 29
            echo "    <span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.department");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
    <span class=\"old-val\">";
            // line 30
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_department_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_department_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_department_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_department_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.organization"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.organization"))), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_organization")) {
            // line 32
            echo "    <span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.organization");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
    <span class=\"old-val\">";
            // line 33
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_org_name", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_org_name"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_org_name", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_org_name"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ticket_triggers"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ticket_triggers"))), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_person")) {
            // line 35
            echo "    <span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.api.user_owner");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
    <span class=\"old-val\">#";
            // line 36
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "old_person_id"), "html", null, true);
            echo " ";
            if (isset($context["old_person_name"])) { $_old_person_name_ = $context["old_person_name"]; } else { $_old_person_name_ = null; }
            echo twig_escape_filter($this->env, $_old_person_name_, "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span class=\"new-val\">#";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "new_person_id"), "html", null, true);
            echo " ";
            if (isset($context["new_person_name"])) { $_new_person_name_ = $context["new_person_name"]; } else { $_new_person_name_ = null; }
            echo twig_escape_filter($this->env, $_new_person_name_, "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "executed_triggers")) {
            // line 38
            echo "    ";
        } elseif (($this->getAttribute($_log_, "action_type") == "message_created")) {
            // line 40
            echo "    Replied
";
        } elseif (($this->getAttribute($_log_, "action_type") == "message_removed")) {
            // line 42
            echo "    ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ($this->getAttribute($this->getAttribute($_log_, "details"), "is_agent_note")) {
                // line 43
                echo "\t\t<span class=\"type\">Note deleted</span>
\t";
            } else {
                // line 45
                echo "\t\t<span class=\"type\">Message deleted</span>
\t";
            }
            // line 47
            echo "\t<span class=\"old-val\">ID ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "message_id"), "html", null, true);
            echo "</span>
\t";
            // line 48
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ($this->getAttribute($this->getAttribute($_log_, "details"), "is_agent_message")) {
                // line 49
                echo "\t\t(written by agent ";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "person_name"), "html", null, true);
                echo ")
\t";
            } else {
                // line 51
                echo "\t\t(written by ";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "person_name"), "html", null, true);
                echo ", ID ";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "person_id"), "html", null, true);
                echo ")
\t";
            }
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_priority")) {
            // line 54
            echo "    <span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.priority");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
    <span class=\"old-val\">";
            // line 55
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_priority_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_priority_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_priority_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_priority_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.workflow"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.workflow"))), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_workflow")) {
            // line 57
            echo "    <span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.workflow");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
    <span class=\"old-val\">";
            // line 58
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_workflow_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_workflow_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_workflow_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_workflow_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.urgency"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.urgency"))), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_urgency")) {
            // line 60
            echo "    <span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.urgency");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
    <span class=\"old-val\">";
            // line 61
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "old_urgency"), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo " <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "new_urgency"), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_product")) {
            // line 63
            echo "    <span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.product");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
    <span class=\"old-val\">";
            // line 64
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_product_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_product_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_product_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_product_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.status"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.status"))), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "attach_added")) {
            // line 66
            echo "    <span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.new_attachment");
            echo "</span> <span class=\"new-val\">#";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "attach_id"), "html", null, true);
            echo " ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "filename"), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "attach_removed")) {
            // line 68
            echo "\t<span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.attachment_deleted");
            echo "</span> <span class=\"old-val\">ID ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "old_attach_id"), "html", null, true);
            echo " ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "filename"), "html", null, true);
            echo "</span>
\t";
            // line 69
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ($this->getAttribute($this->getAttribute($_log_, "details"), "message_id")) {
                // line 70
                echo "\t\tfrom message <span class=\"old-val\">ID ";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "message_id"), "html", null, true);
                echo "</span>
\t\t";
                // line 71
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                if ($this->getAttribute($this->getAttribute($_log_, "details"), "message_person_name")) {
                    echo "written by ";
                    if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "message_person_name"), "html", null, true);
                }
                // line 72
                echo "\t";
            }
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_status")) {
            // line 74
            echo "    ";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_custom_field")) {
            // line 76
            echo "    <span class=\"type\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "field_name"), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
    <span class=\"old-val\">";
            // line 77
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "value_before", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "value_before"), "(no value)")) : ("(no value)")), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "value_after", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "value_after"), "(no value)")) : ("(no value)")), "html", null, true);
            echo "</span>
";
        } elseif ((!$_hide_unknown_)) {
            // line 79
            echo "    ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_log_, "action_type"), "html", null, true);
            echo "
";
        } else {
        }
    }

    public function getTemplateName()
    {
        return "ReportBundle:AgentActivity:ticket-log-actiontext.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  399 => 79,  388 => 77,  380 => 76,  377 => 74,  373 => 72,  357 => 69,  346 => 68,  334 => 66,  316 => 63,  305 => 61,  298 => 60,  287 => 58,  251 => 51,  244 => 49,  241 => 48,  235 => 47,  231 => 45,  227 => 43,  219 => 40,  199 => 36,  192 => 35,  181 => 33,  174 => 32,  156 => 29,  145 => 27,  138 => 26,  126 => 24,  114 => 22,  91 => 18,  82 => 16,  68 => 14,  57 => 12,  48 => 10,  39 => 8,  36 => 6,  28 => 3,  23 => 2,  19 => 1,  436 => 155,  430 => 156,  428 => 155,  424 => 153,  421 => 152,  416 => 146,  410 => 159,  408 => 152,  401 => 151,  391 => 148,  387 => 147,  383 => 146,  379 => 144,  376 => 143,  371 => 141,  366 => 71,  361 => 65,  356 => 59,  349 => 162,  347 => 143,  344 => 142,  342 => 141,  336 => 137,  330 => 134,  318 => 129,  315 => 128,  308 => 126,  297 => 121,  290 => 119,  278 => 114,  272 => 111,  260 => 106,  257 => 105,  250 => 103,  239 => 98,  232 => 96,  221 => 91,  210 => 86,  201 => 85,  190 => 84,  184 => 82,  179 => 81,  167 => 75,  157 => 68,  154 => 67,  151 => 66,  149 => 65,  142 => 60,  140 => 59,  108 => 30,  103 => 20,  98 => 28,  94 => 27,  88 => 24,  83 => 22,  78 => 20,  74 => 19,  70 => 18,  66 => 17,  58 => 15,  54 => 14,  49 => 12,  45 => 11,  41 => 10,  37 => 9,  673 => 200,  669 => 198,  664 => 195,  649 => 193,  634 => 191,  631 => 190,  617 => 189,  603 => 188,  599 => 186,  587 => 184,  581 => 183,  569 => 181,  563 => 180,  551 => 178,  545 => 177,  533 => 175,  527 => 174,  515 => 172,  506 => 171,  503 => 170,  497 => 169,  494 => 168,  481 => 165,  473 => 164,  467 => 163,  463 => 162,  460 => 161,  457 => 160,  454 => 159,  450 => 158,  446 => 157,  442 => 156,  423 => 155,  419 => 154,  398 => 150,  392 => 152,  389 => 151,  386 => 150,  368 => 149,  363 => 148,  360 => 70,  343 => 146,  338 => 143,  328 => 141,  323 => 64,  317 => 136,  314 => 135,  280 => 57,  269 => 55,  262 => 54,  237 => 85,  230 => 81,  223 => 42,  216 => 38,  209 => 69,  202 => 65,  172 => 37,  169 => 36,  163 => 30,  155 => 31,  152 => 30,  147 => 29,  141 => 28,  136 => 25,  132 => 23,  112 => 21,  107 => 20,  104 => 19,  100 => 18,  90 => 15,  77 => 14,  72 => 13,  62 => 16,  55 => 7,  52 => 6,  47 => 3,  29 => 5,  27 => 1,);
    }
}
