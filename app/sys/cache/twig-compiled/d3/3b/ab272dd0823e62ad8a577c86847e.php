<?php

/* AdminBundle:Agents:edit-agent-permtable.html.twig */
class __TwigTemplate_d33bab272dd0823e62ad8a577c86847e extends \Application\DeskPRO\Twig\Template
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
        if (isset($context["is_team_editor"])) { $_is_team_editor_ = $context["is_team_editor"]; } else { $_is_team_editor_ = null; }
        if ($_is_team_editor_) {
            // line 2
            echo "\t";
            $context["col"] = $this->env->loadTemplate("AdminBundle:Agents:permtable-team-col.html.twig");
        } else {
            // line 4
            echo "\t";
            $context["col"] = $this->env->loadTemplate("AdminBundle:Agents:permtable-agent-col.html.twig");
        }
        // line 6
        echo "
<nav class=\"check-grid-tabs\">
\t<ul data-element-handler=\"DeskPRO.ElementHandler.SimpleTabs\" data-trigger-elements=\"> li\">
\t\t<li class=\"on\" data-tab-for=\"#permgroup_tickets\"><span>";
        // line 9
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.tickets");
        echo "</span></li>
\t\t<li data-tab-for=\"#permgroup_people\"><span>";
        // line 10
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.people_and_organizations");
        echo "</span></li>
\t\t";
        // line 11
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.apps_chat"), "method")) {
            echo "<li data-tab-for=\"#permgroup_chat\"><span>";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.chat");
            echo "</span></li>";
        }
        // line 12
        echo "\t\t<li data-tab-for=\"#permgroup_publish\"><span>";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.publish");
        echo "</span></li>
\t\t<li data-tab-for=\"#permgroup_general\"><span>General</span></li>
\t</ul>
</nav>

";
        // line 18
        echo "<div id=\"permgroup_tickets\" style=\"display: none\">
\t<div class=\"check-grid\">
\t\t<table width=\"100%\">
\t\t\t<thead>
\t\t\t\t<tr>
\t\t\t\t\t<th colspan=\"2\" style=\"text-align: left\">";
        // line 23
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.description");
        echo "</th>
\t\t\t\t\t<th width=\"60\">";
        // line 24
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.overrides");
        echo "</th>
\t\t\t\t</tr>
\t\t\t</thead>
\t\t\t<tbody>
\t\t\t\t<!-- PERMISSION:agent_tickets.use -->";
        // line 28
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.use_tickets"), $_usergroup_values_, "agent_tickets", "use");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.create -->";
        // line 29
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.create_tickets"), $_usergroup_values_, "agent_tickets", "create", "agent_tickets_use");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.reply_mass -->";
        // line 30
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.widget_use_mass_actions_to_reply"), $_usergroup_values_, "agent_tickets", "reply_mass", "agent_tickets_use");
        echo "
                ";
        // line 31
        ob_start();
        echo "<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.warning_perm_close_tickets");
        echo "\"></span>";
        $context["phrase_part"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 32
        echo "\t\t\t\t<!-- PERMISSION:agent_tickets.modify_set_closed -->";
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["phrase_part"])) { $_phrase_part_ = $context["phrase_part"]; } else { $_phrase_part_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.widget_close_unclose_tickets", array("subphrase" => $_phrase_part_), true), $_usergroup_values_, "agent_tickets", "modify_set_closed", "agent_tickets_use");
        echo "
\t\t\t</tbody>
\t\t\t<tbody>
\t\t\t\t<!-- PERMISSION:agent_tickets.reply_own -->";
        // line 35
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.reply_to_own_tickets"), $_usergroup_values_, "agent_tickets", "reply_own", "agent_tickets_use");
        echo "
                ";
        // line 36
        $context["phrase_part"] = ('' === $tmp = "<i class=\"expand-toggle\" data-expand=\"subperm-modify_own\"></i>") ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 37
        echo "\t\t\t\t<!-- PERMISSION:agent_tickets.modify_own -->";
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["phrase_part"])) { $_phrase_part_ = $context["phrase_part"]; } else { $_phrase_part_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.widget_modify_own_tickets", array("subphrase" => $_phrase_part_), true), $_usergroup_values_, "agent_tickets", "modify_own", "agent_tickets_use", "parentperm");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_department_own -->";
        // line 38
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.change_department"), $_usergroup_values_, "agent_tickets", "modify_department_own", "agent_tickets_use", "subperm subperm-modify_own");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_fields_own -->";
        // line 39
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.change_fields"), $_usergroup_values_, "agent_tickets", "modify_fields_own", "agent_tickets_use", "subperm subperm-modify_own");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_assign_agent_own -->";
        // line 40
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.assign_agents"), $_usergroup_values_, "agent_tickets", "modify_assign_agent_own", "agent_tickets_use", "subperm subperm-modify_own");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_assign_team_own -->";
        // line 41
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.assign_teams"), $_usergroup_values_, "agent_tickets", "modify_assign_team_own", "agent_tickets_use", "subperm subperm-modify_own");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_assign_self_own -->";
        // line 42
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.assign_self"), $_usergroup_values_, "agent_tickets", "modify_assign_self_own", "agent_tickets_use", "subperm subperm-modify_own");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_cc_own -->";
        // line 43
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.edit_cc"), $_usergroup_values_, "agent_tickets", "modify_cc_own", "agent_tickets_use", "subperm subperm-modify_own");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_merge_own -->";
        // line 44
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.merge"), $_usergroup_values_, "agent_tickets", "modify_merge_own", "agent_tickets_use", "subperm subperm-modify_own");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_labels_own -->";
        // line 45
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.edit_labels"), $_usergroup_values_, "agent_tickets", "modify_labels_own", "agent_tickets_use", "subperm subperm-modify_own");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_slas_own -->";
        // line 46
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.edit_slas"), $_usergroup_values_, "agent_tickets", "modify_slas_own", "agent_tickets_use", "subperm subperm-modify_own");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_notes_own -->";
        // line 47
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.add_notes"), $_usergroup_values_, "agent_tickets", "modify_notes_own", "agent_tickets_use", "subperm subperm-modify_own");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_set_hold_own -->";
        // line 48
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.set_onhold"), $_usergroup_values_, "agent_tickets", "modify_set_hold_own", "agent_tickets_use", "subperm subperm-modify_own");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_set_awaiting_user_own -->";
        // line 49
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.set_status_awaiting_user"), $_usergroup_values_, "agent_tickets", "modify_set_awaiting_user_own", "agent_tickets_use", "subperm subperm-modify_own");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_set_awaiting_agent_own -->";
        // line 50
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.set_status_awaiting_agent"), $_usergroup_values_, "agent_tickets", "modify_set_awaiting_agent_own", "agent_tickets_use", "subperm subperm-modify_own");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_set_resolved_own -->";
        // line 51
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.set_status_resolved"), $_usergroup_values_, "agent_tickets", "modify_set_resolved_own", "agent_tickets_use", "subperm subperm-modify_own");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_messages_own -->";
        // line 52
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col("Can edit and delete messages in own tickets", $_usergroup_values_, "agent_tickets", "modify_messages_own", "agent_tickets_use");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.delete_own -->";
        // line 53
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.delete_own_and_team_tickets"), $_usergroup_values_, "agent_tickets", "delete_own", "agent_tickets_use");
        echo "
\t\t\t</tbody>
            <tbody>
            <!-- PERMISSION:agent_tickets.reply_to_followed -->";
        // line 56
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.reply_to_followed_tickets"), $_usergroup_values_, "agent_tickets", "reply_to_followed", "agent_tickets_use");
        echo "
            ";
        // line 57
        $context["phrase_part"] = ('' === $tmp = "<i class=\"expand-toggle\" data-expand=\"subperm-modify_followed\"></i>") ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 58
        echo "            <!-- PERMISSION:agent_tickets.modify_followed -->";
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["phrase_part"])) { $_phrase_part_ = $context["phrase_part"]; } else { $_phrase_part_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.widget_modify_followed_tickets", array("subphrase" => $_phrase_part_), true), $_usergroup_values_, "agent_tickets", "modify_followed", "agent_tickets_use");
        echo "
            <!-- PERMISSION:agent_tickets.modify_department_followed -->";
        // line 59
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.change_department"), $_usergroup_values_, "agent_tickets", "modify_department_followed", "agent_tickets_use", "subperm subperm-modify_followed");
        echo "
            <!-- PERMISSION:agent_tickets.modify_fields_followed -->";
        // line 60
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.change_fields"), $_usergroup_values_, "agent_tickets", "modify_fields_followed", "agent_tickets_use", "subperm subperm-modify_followed");
        echo "
            <!-- PERMISSION:agent_tickets.modify_assign_agent_followed -->";
        // line 61
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.assign_agents"), $_usergroup_values_, "agent_tickets", "modify_assign_agent_followed", "agent_tickets_use", "subperm subperm-modify_followed");
        echo "
            <!-- PERMISSION:agent_tickets.modify_assign_team_followed -->";
        // line 62
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.assign_teams"), $_usergroup_values_, "agent_tickets", "modify_assign_team_followed", "agent_tickets_use", "subperm subperm-modify_followed");
        echo "
            <!-- PERMISSION:agent_tickets.modify_assign_self_followed -->";
        // line 63
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.assign_self"), $_usergroup_values_, "agent_tickets", "modify_assign_self_followed", "agent_tickets_use", "subperm subperm-modify_followed");
        echo "
            <!-- PERMISSION:agent_tickets.modify_cc_followed -->";
        // line 64
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.edit_cc"), $_usergroup_values_, "agent_tickets", "modify_cc_followed", "agent_tickets_use", "subperm subperm-modify_followed");
        echo "
            <!-- PERMISSION:agent_tickets.modify_merge_followed -->";
        // line 65
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.merge"), $_usergroup_values_, "agent_tickets", "modify_merge_followed", "agent_tickets_use", "subperm subperm-modify_followed");
        echo "
            <!-- PERMISSION:agent_tickets.modify_labels_followed -->";
        // line 66
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.edit_labels"), $_usergroup_values_, "agent_tickets", "modify_labels_followed", "agent_tickets_use", "subperm subperm-modify_followed");
        echo "
\t\t\t<!-- PERMISSION:agent_tickets.modify_slas_followed -->";
        // line 67
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.edit_slas"), $_usergroup_values_, "agent_tickets", "modify_slas_followed", "agent_tickets_use", "subperm subperm-modify_followed");
        echo "
            <!-- PERMISSION:agent_tickets.modify_notes_followed -->";
        // line 68
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.add_notes"), $_usergroup_values_, "agent_tickets", "modify_notes_followed", "agent_tickets_use", "subperm subperm-modify_followed");
        echo "
            <!-- PERMISSION:agent_tickets.modify_set_hold_followed -->";
        // line 69
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.set_onhold"), $_usergroup_values_, "agent_tickets", "modify_set_hold_followed", "agent_tickets_use", "subperm subperm-modify_followed");
        echo "
            <!-- PERMISSION:agent_tickets.modify_set_awaiting_user_followed -->";
        // line 70
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.set_status_awaiting_user"), $_usergroup_values_, "agent_tickets", "modify_set_awaiting_user_followed", "agent_tickets_use", "subperm subperm-modify_followed");
        echo "
            <!-- PERMISSION:agent_tickets.modify_set_awaiting_agent_followed -->";
        // line 71
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.set_status_awaiting_agent"), $_usergroup_values_, "agent_tickets", "modify_set_awaiting_agent_followed", "agent_tickets_use", "subperm subperm-modify_followed");
        echo "
            <!-- PERMISSION:agent_tickets.modify_set_resolved_followed -->";
        // line 72
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.set_status_resolved"), $_usergroup_values_, "agent_tickets", "modify_set_resolved_followed", "agent_tickets_use", "subperm subperm-modify_followed");
        echo "
\t\t\t<!-- PERMISSION:agent_tickets.modify_messages_followed -->";
        // line 73
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col("Can edit and delete messages in followed tickets", $_usergroup_values_, "agent_tickets", "modify_messages_followed", "agent_tickets_use");
        echo "
            <!-- PERMISSION:agent_tickets.delete_followed -->";
        // line 74
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.delete_followed_tickets"), $_usergroup_values_, "agent_tickets", "delete_followed", "agent_tickets_use");
        echo "
            </tbody>
\t\t\t<tbody>
\t\t\t\t<!-- PERMISSION:agent_tickets.view_unassigned -->";
        // line 77
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.view_unassigned_tickets"), $_usergroup_values_, "agent_tickets", "view_unassigned", "agent_tickets_use");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.reply_unassigned -->";
        // line 78
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.reply_to_unassigned_tickets"), $_usergroup_values_, "agent_tickets", "reply_unassigned", "agent_tickets_view_unassigned");
        echo "
                ";
        // line 79
        $context["phrase_part"] = ('' === $tmp = "<i class=\"expand-toggle\" data-expand=\"subperm-modify_unassigned\"></i>") ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 80
        echo "\t\t\t\t<!-- PERMISSION:agent_tickets.modify_unassigned -->";
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["phrase_part"])) { $_phrase_part_ = $context["phrase_part"]; } else { $_phrase_part_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.widget_modify_unassigned_tickets", array("subphrase" => $_phrase_part_), true), $_usergroup_values_, "agent_tickets", "modify_unassigned", "agent_tickets_view_unassigned", "parentperm");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_department_unassigned -->";
        // line 81
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.change_department"), $_usergroup_values_, "agent_tickets", "modify_department_unassigned", "agent_tickets_view_unassigned", "subperm subperm-modify_unassigned");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_fields_unassigned -->";
        // line 82
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.change_fields"), $_usergroup_values_, "agent_tickets", "modify_fields_unassigned", "agent_tickets_view_unassigned", "subperm subperm-modify_unassigned");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_assign_agent_unassigned -->";
        // line 83
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.assign_agents"), $_usergroup_values_, "agent_tickets", "modify_assign_agent_unassigned", "agent_tickets_view_unassigned", "subperm subperm-modify_unassigned");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_assign_team_unassigned -->";
        // line 84
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.assign_teams"), $_usergroup_values_, "agent_tickets", "modify_assign_team_unassigned", "agent_tickets_view_unassigned", "subperm subperm-modify_unassigned");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_assign_self_unassigned -->";
        // line 85
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.assign_self"), $_usergroup_values_, "agent_tickets", "modify_assign_self_unassigned", "agent_tickets_view_unassigned", "subperm subperm-modify_unassigned");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_cc_unassigned -->";
        // line 86
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.edit_cc"), $_usergroup_values_, "agent_tickets", "modify_cc_unassigned", "agent_tickets_view_unassigned", "subperm subperm-modify_own");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_merge_unassigned -->";
        // line 87
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.merge"), $_usergroup_values_, "agent_tickets", "modify_merge_unassigned", "agent_tickets_view_unassigned", "subperm subperm-modify_unassigned");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_labels_unassigned -->";
        // line 88
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.edit_labels"), $_usergroup_values_, "agent_tickets", "modify_labels_unassigned", "agent_tickets_view_unassigned", "subperm subperm-modify_unassigned");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_slas_unassigned -->";
        // line 89
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.edit_slas"), $_usergroup_values_, "agent_tickets", "modify_slas_unassigned", "agent_tickets_view_unassigned", "subperm subperm-modify_unassigned");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_notes_unassigned -->";
        // line 90
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.add_notes"), $_usergroup_values_, "agent_tickets", "modify_notes_unassigned", "agent_tickets_view_unassigned", "subperm subperm-modify_unassigned");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_set_hold_unassigned -->";
        // line 91
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.set_onhold"), $_usergroup_values_, "agent_tickets", "modify_set_hold_unassigned", "agent_tickets_view_unassigned", "subperm subperm-modify_unassigned");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_set_awaiting_user_unassigned -->";
        // line 92
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.set_status_awaiting_user"), $_usergroup_values_, "agent_tickets", "modify_set_awaiting_user_unassigned", "agent_tickets_view_unassigned", "subperm subperm-modify_unassigned");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_set_awaiting_agent_unassigned -->";
        // line 93
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.set_status_awaiting_agent"), $_usergroup_values_, "agent_tickets", "modify_set_awaiting_agent_unassigned", "agent_tickets_view_unassigned", "subperm subperm-modify_unassigned");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_set_resolved_unassigned -->";
        // line 94
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.set_status_resolved"), $_usergroup_values_, "agent_tickets", "modify_set_resolved_unassigned", "agent_tickets_view_unassigned", "subperm subperm-modify_unassigned");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_messages_unassigned -->";
        // line 95
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col("Can edit and delete messages in unassigned tickets", $_usergroup_values_, "agent_tickets", "modify_messages_unassigned", "agent_tickets_use");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.delete_unassigned -->";
        // line 96
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.delete_unassigned_tickets"), $_usergroup_values_, "agent_tickets", "delete_unassigned", "agent_tickets_view_unassigned");
        echo "
\t\t\t</tbody>
\t\t\t<tbody>
\t\t\t\t<!-- PERMISSION:agent_tickets.view_others -->";
        // line 99
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.view_others_tickets"), $_usergroup_values_, "agent_tickets", "view_others", "agent_tickets_use");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.reply_others -->";
        // line 100
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.reply_to_others_tickets"), $_usergroup_values_, "agent_tickets", "reply_others", "agent_tickets_use");
        echo "
                ";
        // line 101
        $context["phrase_part"] = ('' === $tmp = "<i class=\"expand-toggle\" data-expand=\"subperm-modify_others\"></i>") ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 102
        echo "\t\t\t\t<!-- PERMISSION:agent_tickets.modify_others -->";
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["phrase_part"])) { $_phrase_part_ = $context["phrase_part"]; } else { $_phrase_part_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.widget_modify_others_tickets", array("subphrase" => $_phrase_part_), true), $_usergroup_values_, "agent_tickets", "modify_others", "agent_tickets_use", "parentperm");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_department_others -->";
        // line 103
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.change_department"), $_usergroup_values_, "agent_tickets", "modify_department_others", "agent_tickets_use", "subperm subperm-modify_others");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_fields_others -->";
        // line 104
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.change_fields"), $_usergroup_values_, "agent_tickets", "modify_fields_others", "agent_tickets_use", "subperm subperm-modify_others");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_assign_agent_others -->";
        // line 105
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.assign_agents"), $_usergroup_values_, "agent_tickets", "modify_assign_agent_others", "agent_tickets_use", "subperm subperm-modify_others");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_assign_team_others -->";
        // line 106
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.assign_teams"), $_usergroup_values_, "agent_tickets", "modify_assign_team_others", "agent_tickets_use", "subperm subperm-modify_others");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_assign_self_others -->";
        // line 107
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.assign_self"), $_usergroup_values_, "agent_tickets", "modify_assign_self_others", "agent_tickets_use", "subperm subperm-modify_others");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_cc_others -->";
        // line 108
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.edit_cc"), $_usergroup_values_, "agent_tickets", "modify_cc_others", "agent_tickets_view_others", "subperm subperm-modify_own");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_merge_others -->";
        // line 109
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.merge"), $_usergroup_values_, "agent_tickets", "modify_merge_others", "agent_tickets_use", "subperm subperm-modify_others");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_labels_others -->";
        // line 110
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.edit_labels"), $_usergroup_values_, "agent_tickets", "modify_labels_others", "agent_tickets_use", "subperm subperm-modify_others");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_slas_others -->";
        // line 111
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.edit_slas"), $_usergroup_values_, "agent_tickets", "modify_slas_others", "agent_tickets_use", "subperm subperm-modify_others");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_notes_others -->";
        // line 112
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.add_notes"), $_usergroup_values_, "agent_tickets", "modify_notes_others", "agent_tickets_use", "subperm subperm-modify_others");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_set_hold_others -->";
        // line 113
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.set_onhold"), $_usergroup_values_, "agent_tickets", "modify_set_hold_others", "agent_tickets_use", "subperm subperm-modify_others");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_set_awaiting_user_others -->";
        // line 114
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.set_status_awaiting_user"), $_usergroup_values_, "agent_tickets", "modify_set_awaiting_user_others", "agent_tickets_use", "subperm subperm-modify_others");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_set_awaiting_agent_others -->";
        // line 115
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.set_status_awaiting_agent"), $_usergroup_values_, "agent_tickets", "modify_set_awaiting_agent_others", "agent_tickets_use", "subperm subperm-modify_others");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_set_resolved_others -->";
        // line 116
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.set_status_resolved"), $_usergroup_values_, "agent_tickets", "modify_set_resolved_others", "agent_tickets_use", "subperm subperm-modify_others");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.modify_messages_others -->";
        // line 117
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col("Can edit and delete messages in tickets assigned to others", $_usergroup_values_, "agent_tickets", "modify_messages_others", "agent_tickets_use");
        echo "
\t\t\t\t<!-- PERMISSION:agent_tickets.delete_others -->";
        // line 118
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.delete_others_tickets"), $_usergroup_values_, "agent_tickets", "delete_others", "agent_tickets_use");
        echo "
\t\t\t</tbody>
\t\t</table>
\t</div>
</div>
";
        // line 124
        echo "
";
        // line 126
        echo "<div id=\"permgroup_people\" style=\"display: none\">
\t<div class=\"check-grid\">
\t\t<table width=\"100%\">
\t\t\t<thead>
\t\t\t\t<tr>
\t\t\t\t\t<th colspan=\"2\" style=\"text-align: left\">";
        // line 131
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.description");
        echo "</th>
\t\t\t\t\t<th width=\"60\">";
        // line 132
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.overrides");
        echo "</th>
\t\t\t\t</tr>
\t\t\t</thead>
\t\t\t<tbody>
\t\t\t\t<!-- PERMISSION:agent_people.use -->";
        // line 136
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col("Browse the people and organizations directory", $_usergroup_values_, "agent_people", "use");
        echo "
\t\t\t\t<!-- PERMISSION:agent_people.create -->";
        // line 137
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.create_people"), $_usergroup_values_, "agent_people", "create");
        echo "
\t\t\t\t<!-- PERMISSION:agent_people.edit -->";
        // line 138
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.edit_people"), $_usergroup_values_, "agent_people", "edit");
        echo "
\t\t\t\t<!-- PERMISSION:agent_people.validate -->";
        // line 139
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.validate_users_and_emails"), $_usergroup_values_, "agent_people", "validate", "agent_people_edit");
        echo "
                ";
        // line 140
        ob_start();
        echo "<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.warning_keep_manage_email_secure");
        echo "\"></span>";
        $context["phrase_part"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 141
        echo "\t\t\t\t<!-- PERMISSION:agent_people.manage_emails -->";
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["phrase_part"])) { $_phrase_part_ = $context["phrase_part"]; } else { $_phrase_part_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.widget_edit_email_addresses", array("subphrase" => $_phrase_part_), true), $_usergroup_values_, "agent_people", "manage_emails", "agent_people_edit");
        echo "
                ";
        // line 142
        ob_start();
        echo "<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.warning_keep_reset_user_secure");
        echo "\"></span>";
        $context["phrase_part"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 143
        echo "\t\t\t\t<!-- PERMISSION:agent_people.reset_password -->";
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["phrase_part"])) { $_phrase_part_ = $context["phrase_part"]; } else { $_phrase_part_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.widget_reset_passwords", array("subphrase" => $_phrase_part_), true), $_usergroup_values_, "agent_people", "reset_password", "agent_people_edit");
        echo "
\t\t\t\t<!-- PERMISSION:agent_people.notes -->";
        // line 144
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.add_notes"), $_usergroup_values_, "agent_people", "notes");
        echo "
\t\t\t\t";
        // line 145
        $context["phrase_part"] = ('' === $tmp = " <span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Deleting people is a very powerful operation. When a person is deleted, all of their associated objects such as tickets and messages are deleted too. If you grant this permission, you are essentially giving the agent the ability to delete many other things as well. It is usually desired to leave this permission disabled.\"></span>") ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 146
        echo "\t\t\t\t<!-- PERMISSION:agent_people.delete -->";
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["phrase_part"])) { $_phrase_part_ = $context["phrase_part"]; } else { $_phrase_part_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col(($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.delete_people") . $_phrase_part_), $_usergroup_values_, "agent_people", "delete");
        echo "
\t\t\t\t<!-- PERMISSION:agent_people.disable -->";
        // line 147
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col("Disable people", $_usergroup_values_, "agent_people", "disable");
        echo "
\t\t\t\t";
        // line 148
        $context["phrase_part"] = ('' === $tmp = " <span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"This shows the 'Login as User' buttin in the agent interface when viewing a user profile. An agent can click the button to log in to the User Interface as the user.\"></span>") ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 149
        echo "\t\t\t\t<!-- PERMISSION:agent_people.login_as -->";
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["phrase_part"])) { $_phrase_part_ = $context["phrase_part"]; } else { $_phrase_part_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col(($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.login_as") . $_phrase_part_), $_usergroup_values_, "agent_people", "login_as");
        echo "
\t\t\t\t<!-- PERMISSION:agent_people.merge -->";
        // line 150
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col("Merge people", $_usergroup_values_, "agent_people", "merge");
        echo "
\t\t\t</tbody>
\t\t\t<tbody>
\t\t\t\t<!-- PERMISSION:agent_org.create -->";
        // line 153
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.create_organizations"), $_usergroup_values_, "agent_org", "create");
        echo "
\t\t\t\t<!-- PERMISSION:agent_org.edit -->";
        // line 154
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.edit_organizations"), $_usergroup_values_, "agent_org", "edit");
        echo "
\t\t\t\t<!-- PERMISSION:agent_org.notes -->";
        // line 155
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.add_notes_organization"), $_usergroup_values_, "agent_org", "notes");
        echo "
\t\t\t\t<!-- PERMISSION:agent_org.delete -->";
        // line 156
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.delete_organizations"), $_usergroup_values_, "agent_org", "delete");
        echo "
\t\t\t</tbody>
\t\t</table>
\t</div>
</div>
";
        // line 162
        echo "
";
        // line 164
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.apps_chat"), "method")) {
            // line 165
            echo "\t<div id=\"permgroup_chat\" style=\"display: none\">
\t\t<div class=\"check-grid\">
\t\t\t<table width=\"100%\">
\t\t\t\t<thead>
\t\t\t\t\t<tr>
\t\t\t\t\t\t<th colspan=\"2\" style=\"text-align: left\">";
            // line 170
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.description");
            echo "</th>
\t\t\t\t\t\t<th width=\"60\">";
            // line 171
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.overrides");
            echo "</th>
\t\t\t\t\t</tr>
\t\t\t\t</thead>
\t\t\t\t<tbody>
\t\t\t\t\t<!-- PERMISSION:agent_chat.use -->";
            // line 175
            if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
            if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
            echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.use_chat"), $_usergroup_values_, "agent_chat", "use");
            echo "
\t\t\t\t\t<!-- PERMISSION:agent_chat.view_transcripts -->";
            // line 176
            if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
            if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
            echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.view_chat_transcripts"), $_usergroup_values_, "agent_chat", "view_transcripts");
            echo "
\t\t\t\t\t<!-- PERMISSION:agent_chat.view_unassigned -->";
            // line 177
            if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
            if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
            echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.view_new_unassigned_chats"), $_usergroup_values_, "agent_chat", "view_unassigned");
            echo "
\t\t\t\t\t<!-- PERMISSION:agent_chat.view_others -->";
            // line 178
            if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
            if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
            echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.view_others_chats"), $_usergroup_values_, "agent_chat", "view_others");
            echo "
\t\t\t\t\t<!-- PERMISSION:agent_chat.delete -->";
            // line 179
            if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
            if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
            echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.delete_chat_transcripts"), $_usergroup_values_, "agent_chat", "delete");
            echo "
\t\t\t\t</tbody>
\t\t\t</table>
\t\t</div>
\t</div>
";
        }
        // line 186
        echo "
";
        // line 188
        echo "<div id=\"permgroup_publish\" style=\"display: none\">
\t<div class=\"check-grid\">
\t\t<table width=\"100%\">
\t\t\t<thead>
\t\t\t\t<tr>
\t\t\t\t\t<th colspan=\"2\" style=\"text-align: left\">";
        // line 193
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.description");
        echo "</th>
\t\t\t\t\t<th width=\"60\">";
        // line 194
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.overrides");
        echo "</th>
\t\t\t\t</tr>
\t\t\t</thead>
\t\t\t<tbody>
\t\t\t\t<!-- PERMISSION:agent_publish.create -->";
        // line 198
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.create_content"), $_usergroup_values_, "agent_publish", "create");
        echo "
\t\t\t\t<!-- PERMISSION:agent_publish.delete -->";
        // line 199
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.delete_content"), $_usergroup_values_, "agent_publish", "delete");
        echo "
\t\t\t\t<!-- PERMISSION:agent_publish.edit -->";
        // line 200
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.edit_content"), $_usergroup_values_, "agent_publish", "edit");
        echo "
                ";
        // line 201
        ob_start();
        echo "<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.notice_agent_without_permission");
        echo "\"></span>";
        $context["phrase_part"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 202
        echo "\t\t\t\t<!-- PERMISSION:agent_publish.validate -->";
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["phrase_part"])) { $_phrase_part_ = $context["phrase_part"]; } else { $_phrase_part_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col($this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.widget_validate_content_and_comments", array("subphrase" => $_phrase_part_), true), $_usergroup_values_, "agent_publish", "validate");
        echo "
\t\t\t</tbody>
\t\t</table>
\t</div>
</div>
";
        // line 208
        echo "
";
        // line 210
        echo "<div id=\"permgroup_general\" style=\"display: none\">
\t<div class=\"check-grid\">
\t\t<table width=\"100%\">
\t\t\t<thead>
\t\t\t\t<tr>
\t\t\t\t\t<th colspan=\"2\" style=\"text-align: left\">";
        // line 215
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.description");
        echo "</th>
\t\t\t\t\t<th width=\"60\">";
        // line 216
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.agents.overrides");
        echo "</th>
\t\t\t\t</tr>
\t\t\t</thead>
\t\t\t<tbody>
\t\t\t\t<!-- PERMISSION:agent_general.picture -->";
        // line 220
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col("Set a picture", $_usergroup_values_, "agent_general", "picture");
        echo "
\t\t\t\t<!-- PERMISSION:agent_general.signature -->";
        // line 221
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col("Set a signature", $_usergroup_values_, "agent_general", "signature");
        echo "
\t\t\t\t<!-- PERMISSION:agent_general.signature_rte -->";
        // line 222
        if (isset($context["col"])) { $_col_ = $context["col"]; } else { $_col_ = null; }
        if (isset($context["usergroup_values"])) { $_usergroup_values_ = $context["usergroup_values"]; } else { $_usergroup_values_ = null; }
        echo $_col_->getperm_col("Use the rich text editor for a signature", $_usergroup_values_, "agent_general", "signature_rte");
        echo "
\t\t\t</tbody>
\t\t</table>
\t</div>
</div>
";
    }

    public function getTemplateName()
    {
        return "AdminBundle:Agents:edit-agent-permtable.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  865 => 221,  852 => 216,  838 => 208,  820 => 201,  781 => 186,  764 => 178,  725 => 164,  632 => 141,  602 => 136,  565 => 117,  529 => 111,  505 => 107,  487 => 104,  473 => 102,  1853 => 842,  1849 => 841,  1839 => 837,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 813,  1778 => 812,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 782,  1705 => 781,  1688 => 770,  1681 => 769,  1677 => 768,  1667 => 764,  1656 => 762,  1648 => 757,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 714,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 695,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 651,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 610,  1289 => 607,  1276 => 604,  1263 => 601,  1244 => 596,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 565,  1181 => 561,  1171 => 557,  1158 => 550,  1114 => 530,  1104 => 526,  1072 => 512,  1062 => 508,  1024 => 494,  1014 => 490,  1000 => 482,  990 => 478,  980 => 474,  960 => 466,  918 => 448,  888 => 436,  834 => 402,  673 => 342,  636 => 323,  462 => 251,  454 => 249,  1144 => 542,  1139 => 399,  1131 => 399,  1127 => 397,  1110 => 396,  1092 => 391,  1089 => 390,  1086 => 520,  1084 => 388,  1063 => 387,  1060 => 386,  1055 => 385,  1050 => 384,  1035 => 372,  1019 => 267,  1003 => 263,  959 => 251,  900 => 233,  880 => 434,  870 => 430,  867 => 212,  859 => 220,  848 => 215,  839 => 197,  828 => 191,  823 => 188,  809 => 181,  800 => 178,  797 => 177,  794 => 176,  786 => 174,  740 => 162,  734 => 160,  703 => 354,  693 => 350,  630 => 412,  626 => 140,  614 => 138,  610 => 385,  581 => 124,  564 => 293,  525 => 356,  722 => 162,  697 => 256,  674 => 249,  671 => 425,  577 => 220,  569 => 216,  557 => 368,  502 => 267,  497 => 194,  445 => 95,  729 => 159,  684 => 261,  676 => 148,  669 => 254,  660 => 145,  647 => 243,  643 => 244,  601 => 382,  570 => 211,  522 => 200,  501 => 179,  296 => 149,  374 => 137,  631 => 239,  616 => 315,  608 => 137,  605 => 16,  596 => 15,  574 => 297,  561 => 209,  527 => 147,  433 => 93,  388 => 142,  426 => 177,  383 => 146,  461 => 167,  370 => 113,  395 => 144,  294 => 76,  223 => 132,  220 => 59,  492 => 263,  468 => 121,  444 => 245,  410 => 229,  397 => 87,  377 => 144,  262 => 105,  250 => 147,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 2108,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 1383,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 1303,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 1173,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 1041,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 970,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 538,  1126 => 916,  1106 => 900,  1100 => 897,  1026 => 841,  997 => 822,  983 => 816,  975 => 812,  956 => 800,  939 => 786,  902 => 753,  894 => 749,  879 => 746,  757 => 631,  727 => 608,  716 => 605,  670 => 147,  528 => 357,  476 => 123,  435 => 31,  354 => 110,  341 => 278,  192 => 88,  321 => 163,  243 => 143,  793 => 351,  780 => 348,  758 => 177,  700 => 154,  686 => 150,  652 => 274,  638 => 414,  620 => 139,  545 => 218,  523 => 110,  494 => 183,  459 => 99,  438 => 172,  351 => 79,  347 => 282,  402 => 157,  268 => 75,  430 => 237,  411 => 120,  379 => 84,  322 => 92,  315 => 110,  289 => 67,  284 => 93,  255 => 24,  234 => 136,  1133 => 400,  1124 => 534,  1121 => 56,  1116 => 55,  1113 => 403,  1108 => 51,  1103 => 394,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 500,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 329,  996 => 262,  989 => 316,  985 => 315,  981 => 257,  977 => 313,  970 => 470,  966 => 310,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 244,  919 => 290,  917 => 289,  908 => 444,  905 => 286,  896 => 280,  891 => 227,  877 => 270,  862 => 267,  857 => 265,  837 => 253,  832 => 250,  827 => 248,  821 => 247,  803 => 179,  778 => 389,  769 => 165,  765 => 386,  753 => 229,  746 => 175,  743 => 380,  735 => 170,  730 => 218,  720 => 363,  717 => 362,  712 => 156,  691 => 204,  678 => 149,  654 => 144,  587 => 14,  576 => 179,  539 => 171,  517 => 109,  471 => 101,  441 => 131,  437 => 142,  418 => 309,  386 => 295,  373 => 83,  304 => 151,  270 => 123,  265 => 63,  229 => 81,  477 => 138,  455 => 325,  448 => 164,  429 => 159,  407 => 95,  399 => 156,  389 => 99,  375 => 123,  358 => 286,  349 => 118,  335 => 84,  327 => 93,  298 => 84,  280 => 85,  249 => 147,  194 => 65,  142 => 68,  344 => 83,  318 => 135,  306 => 87,  295 => 68,  357 => 119,  300 => 150,  286 => 145,  276 => 87,  269 => 66,  254 => 120,  128 => 34,  237 => 138,  165 => 90,  122 => 42,  798 => 242,  770 => 179,  759 => 112,  748 => 337,  731 => 108,  721 => 158,  718 => 106,  708 => 271,  696 => 147,  617 => 234,  590 => 226,  553 => 115,  550 => 466,  540 => 84,  533 => 82,  500 => 346,  493 => 105,  489 => 343,  482 => 259,  467 => 67,  464 => 120,  458 => 166,  452 => 117,  449 => 174,  415 => 90,  382 => 219,  372 => 215,  361 => 81,  356 => 122,  339 => 77,  302 => 131,  285 => 77,  258 => 64,  123 => 32,  108 => 35,  424 => 156,  394 => 86,  380 => 80,  338 => 107,  319 => 72,  316 => 91,  312 => 109,  290 => 146,  267 => 122,  206 => 51,  110 => 31,  240 => 93,  224 => 60,  219 => 107,  217 => 53,  202 => 52,  186 => 47,  170 => 82,  100 => 29,  67 => 23,  14 => 1,  1096 => 524,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 836,  1013 => 275,  1007 => 274,  1002 => 823,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 300,  940 => 298,  937 => 245,  928 => 452,  926 => 243,  915 => 239,  912 => 236,  903 => 231,  898 => 440,  892 => 229,  889 => 748,  887 => 227,  884 => 747,  876 => 222,  874 => 215,  871 => 222,  863 => 214,  861 => 213,  858 => 212,  850 => 413,  843 => 206,  840 => 406,  815 => 245,  812 => 244,  808 => 199,  804 => 395,  799 => 196,  791 => 193,  785 => 238,  775 => 184,  771 => 183,  754 => 340,  728 => 165,  726 => 169,  723 => 168,  715 => 105,  711 => 152,  709 => 162,  706 => 155,  698 => 208,  694 => 153,  692 => 155,  689 => 254,  681 => 150,  677 => 149,  675 => 148,  663 => 251,  661 => 277,  650 => 246,  646 => 143,  629 => 129,  627 => 21,  625 => 236,  622 => 126,  598 => 309,  592 => 117,  586 => 115,  575 => 214,  566 => 210,  556 => 100,  554 => 289,  541 => 113,  536 => 205,  515 => 352,  511 => 108,  509 => 350,  488 => 126,  486 => 342,  483 => 341,  465 => 100,  463 => 329,  450 => 65,  432 => 314,  419 => 155,  371 => 141,  362 => 288,  353 => 80,  337 => 18,  333 => 122,  309 => 94,  303 => 76,  299 => 130,  291 => 111,  272 => 82,  261 => 156,  253 => 61,  239 => 117,  235 => 63,  213 => 84,  200 => 43,  198 => 110,  159 => 43,  149 => 79,  146 => 39,  131 => 35,  116 => 57,  79 => 29,  74 => 15,  71 => 22,  836 => 320,  817 => 398,  814 => 200,  811 => 317,  805 => 313,  787 => 657,  779 => 169,  776 => 305,  773 => 347,  761 => 296,  751 => 163,  747 => 293,  742 => 336,  739 => 171,  736 => 287,  724 => 214,  705 => 278,  702 => 601,  688 => 113,  680 => 263,  667 => 423,  662 => 146,  656 => 418,  649 => 416,  644 => 97,  641 => 241,  624 => 236,  613 => 233,  607 => 232,  597 => 221,  591 => 131,  584 => 126,  579 => 234,  563 => 213,  559 => 116,  551 => 366,  547 => 114,  537 => 201,  524 => 191,  512 => 351,  507 => 76,  504 => 143,  498 => 142,  485 => 176,  480 => 124,  472 => 255,  466 => 330,  460 => 328,  447 => 388,  442 => 162,  434 => 110,  428 => 29,  422 => 311,  404 => 149,  368 => 136,  364 => 127,  340 => 189,  334 => 130,  330 => 94,  325 => 73,  292 => 163,  287 => 162,  282 => 119,  279 => 98,  273 => 103,  266 => 106,  256 => 71,  252 => 146,  228 => 113,  218 => 81,  201 => 72,  64 => 18,  51 => 9,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 1705,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 737,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 731,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 725,  1559 => 601,  1552 => 720,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 708,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 645,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 624,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 1001,  1257 => 1000,  1254 => 479,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 569,  1196 => 958,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 938,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 388,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 898,  1099 => 393,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 880,  1076 => 359,  1070 => 875,  1057 => 352,  1052 => 504,  1045 => 382,  1040 => 377,  1036 => 283,  1032 => 496,  1028 => 274,  1023 => 393,  1020 => 392,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 821,  979 => 379,  974 => 256,  967 => 373,  962 => 803,  958 => 801,  954 => 253,  950 => 368,  945 => 367,  942 => 460,  938 => 365,  934 => 364,  927 => 361,  923 => 292,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 272,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 410,  841 => 210,  835 => 312,  830 => 249,  826 => 202,  822 => 308,  818 => 246,  813 => 183,  810 => 305,  806 => 180,  802 => 198,  795 => 194,  792 => 239,  789 => 350,  784 => 188,  782 => 187,  777 => 291,  772 => 289,  768 => 345,  763 => 343,  760 => 164,  756 => 230,  752 => 176,  745 => 281,  741 => 280,  738 => 379,  732 => 171,  719 => 279,  714 => 264,  710 => 310,  704 => 267,  699 => 103,  695 => 264,  690 => 263,  687 => 203,  683 => 346,  679 => 288,  672 => 255,  668 => 247,  665 => 201,  658 => 244,  645 => 270,  640 => 142,  634 => 413,  628 => 193,  623 => 238,  619 => 407,  611 => 18,  606 => 311,  603 => 383,  599 => 242,  595 => 132,  583 => 216,  580 => 221,  573 => 221,  560 => 101,  543 => 172,  538 => 361,  534 => 281,  530 => 202,  526 => 279,  521 => 146,  518 => 353,  514 => 183,  510 => 202,  503 => 75,  496 => 345,  490 => 193,  484 => 125,  474 => 336,  470 => 168,  446 => 318,  440 => 114,  436 => 113,  431 => 146,  425 => 312,  416 => 104,  412 => 98,  408 => 305,  403 => 88,  400 => 225,  396 => 299,  392 => 152,  385 => 85,  381 => 139,  367 => 82,  363 => 139,  359 => 92,  355 => 285,  350 => 128,  346 => 127,  343 => 115,  328 => 17,  324 => 164,  313 => 71,  307 => 70,  301 => 69,  288 => 88,  283 => 66,  271 => 64,  257 => 76,  251 => 76,  238 => 92,  233 => 58,  195 => 69,  191 => 106,  187 => 48,  183 => 86,  130 => 36,  88 => 35,  76 => 18,  115 => 30,  95 => 31,  655 => 275,  651 => 232,  648 => 231,  637 => 241,  633 => 130,  621 => 408,  618 => 233,  615 => 232,  604 => 186,  600 => 516,  588 => 305,  585 => 204,  582 => 203,  571 => 118,  567 => 372,  555 => 207,  552 => 190,  549 => 208,  544 => 285,  542 => 207,  535 => 112,  531 => 358,  519 => 189,  516 => 275,  513 => 168,  508 => 145,  506 => 83,  499 => 106,  495 => 141,  491 => 191,  481 => 103,  478 => 172,  475 => 184,  469 => 182,  456 => 136,  451 => 96,  443 => 132,  439 => 94,  427 => 92,  423 => 109,  420 => 233,  409 => 89,  405 => 148,  401 => 147,  391 => 86,  387 => 129,  384 => 132,  378 => 138,  365 => 289,  360 => 132,  348 => 21,  336 => 188,  332 => 107,  329 => 184,  323 => 81,  310 => 133,  305 => 165,  277 => 65,  274 => 151,  263 => 147,  259 => 62,  247 => 60,  244 => 94,  241 => 59,  222 => 17,  210 => 115,  207 => 53,  204 => 74,  184 => 44,  181 => 47,  167 => 80,  157 => 43,  96 => 33,  421 => 91,  417 => 137,  414 => 152,  406 => 171,  398 => 129,  393 => 298,  390 => 221,  376 => 110,  369 => 94,  366 => 91,  352 => 198,  345 => 78,  342 => 109,  331 => 74,  326 => 102,  320 => 129,  317 => 90,  314 => 172,  311 => 78,  308 => 60,  297 => 89,  293 => 89,  281 => 107,  278 => 140,  275 => 34,  264 => 31,  260 => 73,  248 => 144,  245 => 90,  242 => 118,  231 => 57,  227 => 87,  215 => 64,  212 => 131,  209 => 74,  197 => 89,  177 => 34,  171 => 49,  161 => 43,  132 => 95,  121 => 29,  105 => 49,  99 => 27,  81 => 20,  77 => 28,  180 => 35,  176 => 98,  156 => 51,  143 => 38,  139 => 40,  118 => 53,  189 => 48,  185 => 104,  173 => 45,  166 => 40,  152 => 40,  174 => 39,  164 => 94,  154 => 87,  150 => 101,  137 => 36,  133 => 39,  127 => 38,  107 => 28,  102 => 34,  83 => 29,  78 => 27,  53 => 14,  23 => 6,  42 => 11,  138 => 76,  134 => 31,  109 => 57,  103 => 27,  97 => 28,  94 => 24,  84 => 30,  75 => 22,  69 => 17,  66 => 23,  54 => 10,  44 => 10,  230 => 18,  226 => 112,  203 => 128,  193 => 49,  188 => 119,  182 => 47,  178 => 115,  168 => 111,  163 => 44,  160 => 38,  155 => 41,  148 => 39,  145 => 41,  140 => 97,  136 => 96,  125 => 43,  120 => 27,  113 => 32,  101 => 32,  92 => 27,  89 => 30,  85 => 31,  73 => 26,  62 => 19,  59 => 18,  56 => 5,  41 => 11,  126 => 93,  119 => 37,  111 => 35,  106 => 30,  98 => 25,  93 => 28,  86 => 22,  70 => 24,  60 => 7,  28 => 8,  36 => 10,  114 => 32,  104 => 33,  91 => 28,  80 => 30,  63 => 18,  58 => 17,  40 => 10,  34 => 1,  45 => 11,  61 => 13,  55 => 13,  48 => 14,  39 => 10,  35 => 9,  31 => 3,  26 => 4,  21 => 1,  46 => 13,  29 => 7,  57 => 10,  50 => 12,  47 => 6,  38 => 3,  33 => 3,  49 => 11,  32 => 9,  246 => 97,  236 => 49,  232 => 135,  225 => 56,  221 => 78,  216 => 75,  214 => 122,  211 => 52,  208 => 129,  205 => 51,  199 => 50,  196 => 70,  190 => 84,  179 => 68,  175 => 46,  172 => 48,  169 => 45,  162 => 44,  158 => 104,  153 => 102,  151 => 42,  147 => 100,  144 => 37,  141 => 37,  135 => 70,  129 => 94,  124 => 37,  117 => 36,  112 => 27,  90 => 23,  87 => 26,  82 => 20,  72 => 20,  68 => 20,  65 => 15,  52 => 15,  43 => 11,  37 => 5,  30 => 6,  27 => 3,  25 => 7,  24 => 4,  22 => 2,  19 => 1,);
    }
}
