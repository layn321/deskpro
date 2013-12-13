<?php

/* AdminBundle:TicketFeatures:index.html.twig */
class __TwigTemplate_5606916e046564c00a491bb39167a997 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("AdminBundle::layout.html.twig");

        $this->blocks = array(
            'pagebar' => array($this, 'block_pagebar'),
            'page' => array($this, 'block_page'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "AdminBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_pagebar($context, array $blocks = array())
    {
        // line 3
        echo "<ul>
\t<li>";
        // line 4
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.ticket_features_settings");
        echo "</li>
</ul>
";
    }

    // line 7
    public function block_page($context, array $blocks = array())
    {
        // line 8
        echo "
";
        // line 9
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "getSession", array(), "method"), "getFlash", array(0 => "saved_settings"), "method")) {
            // line 10
            echo "\t<div class=\"alert-message block-message success\">
\t\tSettings were saved successfully.
\t</div>
";
        }
        // line 14
        echo "
<form action=\"";
        // line 15
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_settings_saveform", array("type" => "ticket_features", "auth" => $this->env->getExtension('deskpro_templating')->securityToken("settings_ticket_features"))), "html", null, true);
        echo "\" method=\"post\" id=\"settings_form\">
<input type=\"hidden\" name=\"return\" value=\"";
        // line 16
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_features"), "html", null, true);
        echo "\" />

<input type=\"hidden\" name=\"set_settings[]\" value=\"core.tickets.enable_feedback\" />
<input type=\"hidden\" name=\"set_settings[]\" value=\"core.tickets.feedback_agents_read\" />
<input type=\"hidden\" name=\"set_settings_falseable[]\" value=\"core.show_ticket_suggestions\" />
<input type=\"hidden\" name=\"set_settings[]\" value=\"core.ref_pattern\" />
<input type=\"hidden\" name=\"set_settings[]\" value=\"core.tickets.use_ref\" />
<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.use_archive\" />
<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.auto_archive_time\" />
<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.reply_status\" />
<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.reply_assign_unassigned\" />
<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.reply_assign_assigned\" />
<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.reply_assignteam_unassigned\" />
<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.reply_assignteam_assigned\" />
<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.new_status\" />
<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.new_assign\" />
<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.new_assignteam\" />
<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.reassign_auto_change_status\" />
<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.resolve_auto_close_tab\" />
<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.default_send_user_notify\" />
<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.new_default_send_user_notify\" />
<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.default_ticket_reverse_order\" />
<input type=\"hidden\" name=\"set_settings_falseable[]\" value=\"core_tickets.default_ticket_reverse_order\" />

<div class=\"setting_field_row\">
\t<h4>";
        // line 41
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.feedback");
        echo "</h4>
\t<div class=\"setting_fields\">
\t\t<div class=\"field_row\">
\t\t\t<label><input type=\"checkbox\" name=\"settings[core.tickets.enable_feedback]\" ";
        // line 44
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.tickets.enable_feedback"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\t";
        // line 45
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.enable_ticket_feedback");
        echo "</label>
\t\t</div>
\t\t<div class=\"field_row\">
\t\t\t<label><input type=\"checkbox\" name=\"settings[core.tickets.feedback_agents_read]\" ";
        // line 48
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.tickets.feedback_agents_read"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\t";
        // line 49
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.choice_agents_read_ticket_feedback");
        echo "</label>
\t\t</div>
\t</div>
</div>

";
        // line 54
        $this->env->loadTemplate("AdminBundle:TicketFeatures:message-templates-bit.html.twig")->display($context);
        // line 55
        echo "
<div class=\"setting_field_row\">
\t<h4>Ticket Defaults</h4>
\t<div class=\"setting_fields\">
\t\t<strong>Ticket Replies</strong>
\t\t<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
\t\t\t<tr>
\t\t\t\t<td width=\"200\">Default Status</td>
\t\t\t\t<td>
\t\t\t\t\t<select name=\"settings[core_tickets.reply_status]\">
\t\t\t\t\t\t<option ";
        // line 65
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.reply_status"), "method"))) {
            echo "selected=\"selected\"";
        }
        echo " value=\"0\">Do not change status</option>
\t\t\t\t\t\t<option ";
        // line 66
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.reply_status"), "method") == "awaiting_user")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"awaiting_user\">Awaiting User</option>
\t\t\t\t\t\t<option ";
        // line 67
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.reply_status"), "method") == "awaiting_agent")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"awaiting_agent\">Awaiting Agent</option>
\t\t\t\t\t\t<option ";
        // line 68
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.reply_status"), "method") == "resolved")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"resolved\">Resolved</option>
\t\t\t\t\t</select>
\t\t\t\t</td>
\t\t\t</tr>
\t\t\t<tr>
\t\t\t\t<td>Default Agent - Unassigned</td>
\t\t\t\t<td>
\t\t\t\t\t<select name=\"settings[core_tickets.reply_assign_unassigned]\">
\t\t\t\t\t\t<option ";
        // line 76
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.reply_assign_unassigned"), "method"))) {
            echo "selected=\"selected\"";
        }
        echo " value=\"0\">Keep unassigned</option>
\t\t\t\t\t\t<option ";
        // line 77
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.reply_assign_unassigned"), "method") == "assign")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"assign\">Assign to self</option>
\t\t\t\t\t</select>
\t\t\t\t</td>
\t\t\t</tr>
\t\t\t<tr>
\t\t\t\t<td>Default Agent - Assigned</td>
\t\t\t\t<td>
\t\t\t\t\t<select name=\"settings[core_tickets.reply_assign_assigned]\">
\t\t\t\t\t\t<option ";
        // line 85
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.reply_assign_assigned"), "method"))) {
            echo "selected=\"selected\"";
        }
        echo " value=\"0\">Keep assignment</option>
\t\t\t\t\t\t<option ";
        // line 86
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.reply_assign_assigned"), "method") == "assign")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"assign\">Reassign to self</option>
\t\t\t\t\t</select>
\t\t\t\t</td>
\t\t\t</tr>
\t\t\t<tr>
\t\t\t\t<td>Default Team - No Team</td>
\t\t\t\t<td>
\t\t\t\t\t<select name=\"settings[core_tickets.reply_assignteam_unassigned]\">
\t\t\t\t\t\t<option ";
        // line 94
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.reply_assignteam_unassigned"), "method"))) {
            echo "selected=\"selected\"";
        }
        echo " value=\"0\">Keep no team</option>
\t\t\t\t\t\t<option ";
        // line 95
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.reply_assignteam_unassigned"), "method") == "assign")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"assign\">Assign to own team</option>
\t\t\t\t\t</select>
\t\t\t\t</td>
\t\t\t</tr>
\t\t\t<tr>
\t\t\t\t<td>Default Team - With Team</td>
\t\t\t\t<td>
\t\t\t\t\t<select name=\"settings[core_tickets.reply_assignteam_assigned]\">
\t\t\t\t\t\t<option ";
        // line 103
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.reply_assignteam_assigned"), "method"))) {
            echo "selected=\"selected\"";
        }
        echo " value=\"0\">Keep team</option>
\t\t\t\t\t\t<option ";
        // line 104
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.reply_assignteam_assigned"), "method") == "assign")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"assign\">Reassign to own team</option>
\t\t\t\t\t</select>
\t\t\t\t</td>
\t\t\t</tr>
\t\t\t<tr>
\t\t\t\t<td>Send User Notification</td>
\t\t\t\t<td>
\t\t\t\t\t<select name=\"settings[core_tickets.default_send_user_notify]\">
\t\t\t\t\t\t<option ";
        // line 112
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.default_send_user_notify"), "method")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"1\">Default on - user notifications are sent</option>
\t\t\t\t\t\t<option ";
        // line 113
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.default_send_user_notify"), "method"))) {
            echo "selected=\"selected\"";
        }
        echo " value=\"0\">Default off - no user notifications are sent</option>
\t\t\t\t\t</select>
\t\t\t\t</td>
\t\t\t</tr>
\t\t</table>
\t\t<div class=\"field_row\" style=\"margin-top: 10px;\">
\t\t\t<label>
\t\t\t\t<input type=\"checkbox\" name=\"settings[core_tickets.reassign_auto_change_status]\" value=\"1\" ";
        // line 120
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.reassign_auto_change_status"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\tWhen changing the assigned agent from the reply box, automatically set the status to Awaiting Agent
\t\t\t</label>
\t\t\t<label>
\t\t\t\t<input type=\"checkbox\" name=\"settings[core_tickets.resolve_auto_close_tab]\" value=\"1\" ";
        // line 124
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.resolve_auto_close_tab"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\tWhen changing the status to Resolved from the reply box, automatically check the \"close tab after reply\" option
\t\t\t</label>
\t\t</div>
\t\t<div class=\"field_row\" style=\"margin-top: 10px;\">
\t\t\t<strong>Ticket Message Ordering</strong>
\t\t\t<label>
\t\t\t\t<input type=\"checkbox\" name=\"settings[core_tickets.default_ticket_reverse_order]\" value=\"1\" ";
        // line 131
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.default_ticket_reverse_order"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\tShow newest messages first (reverse chronological order) with the replybox at the top. This setting can be overriden by each agent from their profile settings.
\t\t\t</label>
\t\t</div>
\t\t<div class=\"field_row\" style=\"margin-top: 10px;\">
\t\t\t<strong>New Ticket</strong>
\t\t\t<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
\t\t\t\t<tr>
\t\t\t\t\t<td width=\"200\">Default Status</td>
\t\t\t\t\t<td>
\t\t\t\t\t\t<select name=\"settings[core_tickets.new_status]\">
\t\t\t\t\t\t\t<option ";
        // line 142
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.new_status"), "method") == "awaiting_user")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"awaiting_user\">Awaiting User</option>
\t\t\t\t\t\t\t<option ";
        // line 143
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.new_status"), "method") == "awaiting_agent")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"awaiting_agent\">Awaiting Agent</option>
\t\t\t\t\t\t\t<option ";
        // line 144
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.new_status"), "method") == "resolved")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"resolved\">Resolved</option>
\t\t\t\t\t\t</select>
\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t\t<tr>
\t\t\t\t\t<td width=\"200\">Default Agent</td>
\t\t\t\t\t<td>
\t\t\t\t\t\t<select name=\"settings[core_tickets.new_assign]\">
\t\t\t\t\t\t\t<option ";
        // line 152
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.new_assign"), "method"))) {
            echo "selected=\"selected\"";
        }
        echo " value=\"0\">Unassigned</option>
\t\t\t\t\t\t\t<option ";
        // line 153
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.new_assign"), "method") == "assign")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"assign\">Assign to self</option>
\t\t\t\t\t\t</select>
\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t\t<tr>
\t\t\t\t\t<td width=\"200\">Default Team</td>
\t\t\t\t\t<td>
\t\t\t\t\t\t<select name=\"settings[core_tickets.new_assignteam]\">
\t\t\t\t\t\t\t<option ";
        // line 161
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.new_assignteam"), "method"))) {
            echo "selected=\"selected\"";
        }
        echo " value=\"0\">Unassigned</option>
\t\t\t\t\t\t\t<option ";
        // line 162
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.new_assignteam"), "method") == "assign")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"assign\">Assign to own team</option>
\t\t\t\t\t\t</select>
\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t\t<tr>
\t\t\t\t\t<td width=\"200\">Send User Notification</td>
\t\t\t\t\t<td>
\t\t\t\t\t\t<select name=\"settings[core_tickets.new_default_send_user_notify]\">
\t\t\t\t\t\t\t<option ";
        // line 170
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.new_default_send_user_notify"), "method")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"1\">Default on - user notification is sent</option>
\t\t\t\t\t\t\t<option ";
        // line 171
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.new_default_send_user_notify"), "method"))) {
            echo "selected=\"selected\"";
        }
        echo " value=\"0\">Default off - no user notification is sent</option>
\t\t\t\t\t\t</select>
\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t</table>
\t\t</div>
\t</div>
</div>

<div class=\"setting_field_row\">
\t<h4>Ticket Billing</h4>
\t<div class=\"setting_fields\">
\t\t<div class=\"field_row\">
\t\t\t<label><input type=\"checkbox\" name=\"settings[core_tickets.enable_billing]\" ";
        // line 184
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.enable_billing"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " id=\"billing_enable\" />
\t\t\t\tEnable ticket billing</label>
\t\t</div>
\t\t<div id=\"billing_dependencies\" style=\"";
        // line 187
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.enable_billing"), "method"))) {
            echo "display: none;";
        }
        echo "\">
\t\t\t<div class=\"field_row\">
\t\t\t\t<label>
\t\t\t\t\t<input type=\"checkbox\" name=\"settings[core_tickets.billing_auto_timer]\" ";
        // line 190
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.billing_auto_timer"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " id=\"bill_auto_opt\" />
\t\t\t\t\tAutomatically start billing timer when viewing a ticket
\t\t\t\t</label>

\t\t\t\t<div id=\"show_reply_bill\" style=\"margin-left: 15px; ";
        // line 194
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.billing_auto_timer"), "method"))) {
            echo "display: none;";
        }
        echo "\">
\t\t\t\t\t<label><input type=\"checkbox\" name=\"settings[core_tickets.billing_on_reply]\" ";
        // line 195
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.billing_on_reply"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\t\tShow billing on reply form</label>
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<div class=\"field_row\">
\t\t\t\t<label>
\t\t\t\t\t<input type=\"checkbox\" name=\"settings[core_tickets.billing_on_new]\" ";
        // line 201
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.billing_on_new"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " id=\"billing_on_new\" />
\t\t\t\t\tShow billing on new ticket
\t\t\t\t</label>

\t\t\t\t<div id=\"billing_auto_timer_new\" style=\"margin-left: 15px; ";
        // line 205
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.billing_on_new"), "method"))) {
            echo "display: none;";
        }
        echo "\">
\t\t\t\t\t<label><input type=\"checkbox\" name=\"settings[core_tickets.billing_auto_timer_new]\" ";
        // line 206
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.billing_auto_timer_new"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\t\tAutomatically start billing timer</label>
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<div class=\"field_row\">
\t\t\t\t<label>Billing currency name</label>
\t\t\t\t<input type=\"text\" name=\"settings[core_tickets.billing_currency]\" value=\"";
        // line 212
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.billing_currency"), "method"), "html", null, true);
        echo "\" />
\t\t\t</div>
\t\t</div>
\t</div>
\t<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.enable_billing\" />
\t<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.billing_on_reply\" />
\t<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.billing_currency\" />
\t<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.billing_auto_timer\" />
\t<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.billing_on_new\" />
\t<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.billing_auto_timer_new\" />
</div>

<div class=\"setting_field_row\">
\t<h4>Working Hours</h4>
\t<div class=\"setting_fields\">
\t\t";
        // line 227
        if (isset($context["work_hours"])) { $_work_hours_ = $context["work_hours"]; } else { $_work_hours_ = null; }
        if (($this->getAttribute($_work_hours_, "active_time") == "all")) {
            // line 228
            echo "\t\t\t24 hours a day, 7 days a week
\t\t";
        } else {
            // line 230
            echo "\t\t\t";
            if (isset($context["work_hours"])) { $_work_hours_ = $context["work_hours"]; } else { $_work_hours_ = null; }
            echo twig_escape_filter($this->env, sprintf("%02d:%02d - %02d:%02d", $this->getAttribute($_work_hours_, "start_hour"), $this->getAttribute($_work_hours_, "start_minute"), $this->getAttribute($_work_hours_, "end_hour"), $this->getAttribute($_work_hours_, "end_minute")), "html", null, true);
            echo "
\t\t\t(time zone: ";
            // line 231
            if (isset($context["work_hours"])) { $_work_hours_ = $context["work_hours"]; } else { $_work_hours_ = null; }
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($_work_hours_, "timezone", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_work_hours_, "timezone"), $this->getAttribute($_app_, "getSetting", array(0 => "core.default_timezone"), "method"))) : ($this->getAttribute($_app_, "getSetting", array(0 => "core.default_timezone"), "method"))), "html", null, true);
            echo ")";
            // line 232
            if (isset($context["work_hours"])) { $_work_hours_ = $context["work_hours"]; } else { $_work_hours_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_work_hours_, "days"));
            foreach ($context['_seq'] as $context["day"] => $context["null"]) {
                echo ",
\t\t\t\t";
                // line 233
                if (isset($context["day"])) { $_day_ = $context["day"]; } else { $_day_ = null; }
                if (($_day_ == 0)) {
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.time.long-day_sunday");
                } elseif (($_day_ == 1)) {
                    // line 234
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.time.long-day_monday");
                } elseif (($_day_ == 2)) {
                    // line 235
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.time.long-day_tuesday");
                } elseif (($_day_ == 3)) {
                    // line 236
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.time.long-day_wednesday");
                } elseif (($_day_ == 4)) {
                    // line 237
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.time.long-day_thursday");
                } elseif (($_day_ == 5)) {
                    // line 238
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.time.long-day_friday");
                } elseif (($_day_ == 6)) {
                    // line 239
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.time.long-day_saturday");
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['day'], $context['null'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 240
            echo " (excluding holidays)
\t\t";
        }
        // line 242
        echo "\t\t<div>
\t\t\t<a class=\"clean-white\" style=\"padding: 1px 6px\" href=\"";
        // line 243
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_features_work_hours"), "html", null, true);
        echo "\">Edit Working Hours</a>
\t\t</div>
\t</div>
</div>

<div class=\"setting_field_row\">
\t<h4>Rich Text Editor</h4>
\t<div class=\"setting_fields\">
\t\t";
        // line 257
        echo "\t\t<div class=\"field_row\">
\t\t\t<strong>Available Text Editor Buttons:</strong>
\t\t\t<ul>
\t\t\t\t<li><label><input type=\"checkbox\" name=\"settings[core_tickets.agent_rte_button_html]\" value=\"1\" ";
        // line 260
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.agent_rte_button_html"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " /> View HTML Source</label></li>
\t\t\t\t<li><label><input type=\"checkbox\" name=\"settings[core_tickets.agent_rte_button_bold]\" value=\"1\" ";
        // line 261
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.agent_rte_button_bold"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " /> Bold</label></li>
\t\t\t\t<li><label><input type=\"checkbox\" name=\"settings[core_tickets.agent_rte_button_italic]\" value=\"1\" ";
        // line 262
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.agent_rte_button_italic"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " /> Italics</label></li>
\t\t\t\t<li><label><input type=\"checkbox\" name=\"settings[core_tickets.agent_rte_button_underline]\" value=\"1\" ";
        // line 263
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.agent_rte_button_underline"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " /> Underline</label></li>
\t\t\t\t<li><label><input type=\"checkbox\" name=\"settings[core_tickets.agent_rte_button_strike]\" value=\"1\" ";
        // line 264
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.agent_rte_button_strike"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " /> Strikethrough</label></li>
\t\t\t\t<li><label><input type=\"checkbox\" name=\"settings[core_tickets.agent_rte_button_color]\" value=\"1\" ";
        // line 265
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.agent_rte_button_color"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " /> Change Color</label></li>
\t\t\t\t<li><label><input type=\"checkbox\" name=\"settings[core_tickets.agent_rte_button_alignment]\" value=\"1\" ";
        // line 266
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.agent_rte_button_alignment"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " /> Change Alignment</label></li>
\t\t\t\t<li><label><input type=\"checkbox\" name=\"settings[core_tickets.agent_rte_button_list]\" value=\"1\" ";
        // line 267
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.agent_rte_button_list"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " /> Insert List</label></li>
\t\t\t\t<li><label><input type=\"checkbox\" name=\"settings[core_tickets.agent_rte_button_image]\" value=\"1\" ";
        // line 268
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.agent_rte_button_image"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " /> Insert Image</label></li>
\t\t\t\t<li><label><input type=\"checkbox\" name=\"settings[core_tickets.agent_rte_button_link]\" value=\"1\" ";
        // line 269
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.agent_rte_button_link"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " /> Insert Link</label></li>
\t\t\t\t<li><label><input type=\"checkbox\" name=\"settings[core_tickets.agent_rte_button_table]\" value=\"1\" ";
        // line 270
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.agent_rte_button_table"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " /> Insert Table</label></li>
\t\t\t\t<li><label><input type=\"checkbox\" name=\"settings[core_tickets.agent_rte_button_hr]\" value=\"1\" ";
        // line 271
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.agent_rte_button_hr"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " /> Insert Horizontal Rule</label></li>
\t\t\t</ul>
\t\t</div>
\t</div>
\t";
        // line 276
        echo "\t";
        // line 277
        echo "\t<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.agent_rte_button_html\" />
\t<input type=\"hidden\" name=\"set_settings_falseable[]\" value=\"core_tickets.agent_rte_button_html\" />
\t<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.agent_rte_button_bold\" />
\t<input type=\"hidden\" name=\"set_settings_falseable[]\" value=\"core_tickets.agent_rte_button_bold\" />
\t<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.agent_rte_button_italic\" />
\t<input type=\"hidden\" name=\"set_settings_falseable[]\" value=\"core_tickets.agent_rte_button_italic\" />
\t<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.agent_rte_button_italic\" />
\t<input type=\"hidden\" name=\"set_settings_falseable[]\" value=\"core_tickets.agent_rte_button_italic\" />
\t<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.agent_rte_button_underline\" />
\t<input type=\"hidden\" name=\"set_settings_falseable[]\" value=\"core_tickets.agent_rte_button_underline\" />
\t<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.agent_rte_button_strike\" />
\t<input type=\"hidden\" name=\"set_settings_falseable[]\" value=\"core_tickets.agent_rte_button_strike\" />
\t<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.agent_rte_button_color\" />
\t<input type=\"hidden\" name=\"set_settings_falseable[]\" value=\"core_tickets.agent_rte_button_color\" />
\t<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.agent_rte_button_list\" />
\t<input type=\"hidden\" name=\"set_settings_falseable[]\" value=\"core_tickets.agent_rte_button_list\" />
\t<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.agent_rte_button_image\" />
\t<input type=\"hidden\" name=\"set_settings_falseable[]\" value=\"core_tickets.agent_rte_button_image\" />
\t<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.agent_rte_button_link\" />
\t<input type=\"hidden\" name=\"set_settings_falseable[]\" value=\"core_tickets.agent_rte_button_link\" />
\t<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.agent_rte_button_alignment\" />
\t<input type=\"hidden\" name=\"set_settings_falseable[]\" value=\"core_tickets.agent_rte_button_alignment\" />
\t<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.agent_rte_button_table\" />
\t<input type=\"hidden\" name=\"set_settings_falseable[]\" value=\"core_tickets.agent_rte_button_table\" />
\t<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.agent_rte_button_hr\" />
\t<input type=\"hidden\" name=\"set_settings_falseable[]\" value=\"core_tickets.agent_rte_button_hr\" />
</div>

<div class=\"setting_field_row\">
\t<h4>Ticket Locking</h4>
\t<div class=\"setting_fields\">
\t\t<div class=\"field_row\">
\t\t\t<label><input type=\"checkbox\" name=\"settings[core_tickets.lock_on_view]\" ";
        // line 309
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.lock_on_view"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\tAutomatically lock tickets when agents view them</label>
\t\t</div>
\t\t<div class=\"field_row\">
\t\t\t<label><input type=\"checkbox\" name=\"settings[core_tickets.unlock_on_close]\" ";
        // line 313
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.unlock_on_close"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\tAutomatically unlock tickets when the lock owner stops viewing the ticket</label>
\t\t</div>
\t\t<div class=\"field_row\">
\t\t\t";
        // line 317
        $context["options"] = array(900 => "15 minutes", 1800 => "30 minutes", 3600 => "1 hour", 7200 => "2 hours", 14400 => "4 hours", 28000 => "8 hours", 86400 => "1 day", 259200 => "3 days", 432000 => "5 days", 604800 => "1 week", 1209600 => "2 weeks");
        // line 330
        echo "\t\t\t<label>Automatically unlock tickets after:</label>
\t\t\t<select name=\"settings[core_tickets.lock_lifetime]\">
\t\t\t\t";
        // line 332
        if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_options_);
        foreach ($context['_seq'] as $context["k"] => $context["v"]) {
            // line 333
            echo "\t\t\t\t\t<option value=\"";
            if (isset($context["k"])) { $_k_ = $context["k"]; } else { $_k_ = null; }
            echo twig_escape_filter($this->env, $_k_, "html", null, true);
            echo "\" ";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if (isset($context["k"])) { $_k_ = $context["k"]; } else { $_k_ = null; }
            if (($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.lock_lifetime"), "method") == $_k_)) {
                echo "selected=\"selected\"";
            }
            echo ">";
            if (isset($context["v"])) { $_v_ = $context["v"]; } else { $_v_ = null; }
            echo twig_escape_filter($this->env, $_v_, "html", null, true);
            echo "</option>
\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['k'], $context['v'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 335
        echo "\t\t\t</select>
\t\t</div>
\t</div>
\t<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.lock_on_view\" />
\t<input type=\"hidden\" name=\"set_settings_falseable[]\" value=\"core_tickets.lock_on_view\" />
\t<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.unlock_on_close\" />
\t<input type=\"hidden\" name=\"set_settings_falseable[]\" value=\"core_tickets.unlock_on_close\" />
\t<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.lock_lifetime\" />
</div>

<div class=\"setting_field_row\">
\t<h4>Suggestions</h4>
\t<div class=\"setting_fields\">
\t\t<div class=\"field_row\">
\t\t\t<label><input type=\"checkbox\" name=\"settings[core.show_ticket_suggestions]\" ";
        // line 349
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.show_ticket_suggestions"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\tShow automatic article suggestions on new ticket form</label>
\t\t</div>
\t</div>
</div>

<div class=\"setting_field_row\">
\t<h4>Ticket Refs</h4>
\t<div class=\"setting_fields\">
\t\t<div class=\"field_row\">
\t\t\t<p>
\t\t\t\tEach ticket is given a unique numeric ID that identifies it. You may also add \"ref codes\" to
\t\t\t\tenable a reference string that your end-users will see instead of the ID. These ref codes are generally more readable
\t\t\t\tand user-friendly. By default, ref codes look like <em>ABCD-1234-EFGH</em> but you can customize it to include other tokens.
\t\t\t</p>
\t\t</div>
\t\t<div class=\"field_row\">
\t\t\t<label><input id=\"ref_options_toggle\" type=\"checkbox\" name=\"settings[core.tickets.use_ref]\" ";
        // line 366
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.tickets.use_ref"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\tUse ref codes
\t\t\t</label>

\t\t\t<div id=\"ref_options\" ";
        // line 370
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core.tickets.use_ref"), "method"))) {
            echo "style=\"display: none\"";
        }
        echo ">
\t\t\t\t<div style=\"padding-top: 5px; padding-bottom: 5px;\">
\t\t\t\t\t<input type=\"text\" name=\"settings[core.ref_pattern]\" id=\"ref_format\" value=\"";
        // line 372
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "core.ref_pattern"), "method"), "html", null, true);
        echo "\" placeholder=\"";
        echo twig_escape_filter($this->env, "<A><A><A><A>-<#><#><#><#>-<A><A><A><A>");
        echo "\" style=\"width: 350px; font-family: 'Monaco', 'Courier New', monospaced; color: #000;\" />
\t\t\t\t\tand append <select name=\"settings[core.ref_append_counter]\" id=\"ref_format_digits\">
\t\t\t\t\t\t";
        // line 374
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable(array(0 => 0, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8));
        foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
            // line 375
            echo "\t\t\t\t\t\t\t<option value=\"";
            if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
            echo twig_escape_filter($this->env, $_i_, "html", null, true);
            echo "\" ";
            if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if (($_i_ == $this->getAttribute($_app_, "getSetting", array(0 => "core.ref_append_counter"), "method"))) {
                echo "selected=\"selected\"";
            }
            echo ">
\t\t\t\t\t\t\t\t";
            // line 376
            if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
            if (($_i_ == 1)) {
                echo "1 Digit
\t\t\t\t\t\t\t\t";
            } else {
                // line 377
                if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
                echo twig_escape_filter($this->env, $_i_, "html", null, true);
                echo " Digits";
            }
            // line 378
            echo "\t\t\t\t\t\t\t</option>
\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 380
        echo "\t\t\t\t\t</select>
\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"This will append a counter to the end of your pattern. For example, if you based it on the year, you might choose 5 digits and get refs like 2012-00001 and 2012-00002, counting upwards.<br /><br />If your pattern has a low number of possibilities, then it is highly recommended to use a counter to avoid collisions.\"></span>
\t\t\t\t</div>

\t\t\t\t<div class=\"alert-message block-message error\" id=\"ref_format_err_len\" style=\"display: none;\">
\t\t\t\t\tThis ref code is too long. Remove some segments and try again.
\t\t\t\t</div>
\t\t\t\t<div class=\"alert-message block-message error\" id=\"ref_format_err_regex\" style=\"display: none;\">
\t\t\t\t\tRef codes can only contain:<br/>
\t\t\t\t\t&middot; The tokens below<br/>
\t\t\t\t\t&middot; Letters and numbers (A-Z and 0-9)<br/>
\t\t\t\t\t&middot; Dashes and underscores (- and _)
\t\t\t\t</div>

\t\t\t\t<p>Your pattern can use tokens surrounded by &lt;brackets&gt;. Here are the available tokens:</p>

\t\t\t\t<ul class=\"token-list inline\">
\t\t\t\t\t<li><em>&lt;A&gt;</em> - A random letter</li>
\t\t\t\t\t<li><em>&lt;#&gt;</em> - A random number</li>
\t\t\t\t\t<li><em>&lt;?&gt;</em> - A random letter or number</li>
\t\t\t\t\t<li><em>&lt;YEAR&gt;</em> - Year as a four-digit number (";
        // line 400
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, "now", "Y"), "html", null, true);
        echo ")</li>
\t\t\t\t\t<li><em>&lt;MONTH&gt;</em> - Month as a two-digit number (";
        // line 401
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, "now", "m"), "html", null, true);
        echo ")</li>
\t\t\t\t\t<li><em>&lt;DAY&gt;</em> - Day as a two-digit number (";
        // line 402
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, "now", "d"), "html", null, true);
        echo ")</li>
\t\t\t\t\t<li><em>&lt;HOUR&gt;</em> - Hour in 24h time (";
        // line 403
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, "now", "H"), "html", null, true);
        echo ")</li>
\t\t\t\t\t<li><em>&lt;MIN&gt;</em> - Minute (";
        // line 404
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, "now", "i"), "html", null, true);
        echo ")</li>
\t\t\t\t\t<li><em>&lt;SEC&gt;</em> - Second (";
        // line 405
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, "now", "s"), "html", null, true);
        echo ")</li>
\t\t\t\t</ul>

\t\t\t\t<br />
\t\t\t\t<p>Here are some examples:</p>
\t\t\t\t<ul class=\"token-list\">
\t\t\t\t\t<li><em>";
        // line 411
        echo twig_escape_filter($this->env, "<YEAR>-<MONTH>");
        echo "- and 3 digits</em> creates <em>";
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, "now", "Y"), "html", null, true);
        echo "-";
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, "now", "m"), "html", null, true);
        echo "-001</em>, then <em>";
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, "now", "Y"), "html", null, true);
        echo "-";
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, "now", "m"), "html", null, true);
        echo "-002</em></li>
\t\t\t\t\t<li><em>";
        // line 412
        echo twig_escape_filter($this->env, "<?><?><?><?><?><?>");
        echo "</em> creates <em>";
        echo twig_escape_filter($this->env, ((((($this->env->getExtension('deskpro_templating')->rand() . $this->env->getExtension('deskpro_templating')->rand()) . $this->env->getExtension('deskpro_templating')->rand()) . $this->env->getExtension('deskpro_templating')->rand()) . $this->env->getExtension('deskpro_templating')->rand()) . $this->env->getExtension('deskpro_templating')->rand()), "html", null, true);
        echo "</em></li>
\t\t\t\t\t<li><em>";
        // line 413
        echo twig_escape_filter($this->env, "<YEAR>-<A><A><?><?><?>");
        echo "</em> creates <em>";
        echo twig_escape_filter($this->env, ((($this->env->getExtension('deskpro_templating')->userDate($context, "now", "Y") . "-AB") . $this->env->getExtension('deskpro_templating')->rand()) . $this->env->getExtension('deskpro_templating')->rand()), "html", null, true);
        echo "</em></li>
\t\t\t\t\t<li><em>";
        // line 414
        echo twig_escape_filter($this->env, "TICKET-<A><A><?><?><?><?><?>");
        echo "</em> creates <em>";
        echo twig_escape_filter($this->env, ((((("TICKET" . "-AB") . $this->env->getExtension('deskpro_templating')->rand()) . $this->env->getExtension('deskpro_templating')->rand()) . $this->env->getExtension('deskpro_templating')->rand()) . $this->env->getExtension('deskpro_templating')->rand()), "html", null, true);
        echo "</em></li>
\t\t\t\t</ul>
\t\t\t</div>
\t\t</div>
\t</div>
</div>

<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.add_agent_ccs\" />
<input type=\"hidden\" name=\"set_settings_falseable[]\" value=\"core_tickets.add_agent_ccs\" />
<input type=\"hidden\" name=\"set_settings[]\" value=\"core_tickets.process_agent_fwd\" />
<input type=\"hidden\" name=\"set_settings_falseable[]\" value=\"core_tickets.process_agent_fwd\" />
<div class=\"setting_field_row\">
\t<h4>Email Gateway</h4>
\t<div class=\"setting_fields\">
\t\t<div class=\"field_row\">
\t\t\t<label>
\t\t\t\t<input type=\"checkbox\" name=\"settings[core_tickets.add_agent_ccs]\" ";
        // line 430
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.add_agent_ccs"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\tIf an agent email address is CC'd in an email to the helpdesk, add the agent as a follower.
\t\t\t\t<span class=\"small-light-icon tipped\" title=\"When this option is disabled, any CC'd agent email addresses will be ignored. This means agents must be assigned or added as followers using triggers or manually by another agent. When this option is disabled there is no way for end-users to add specific agents to tickets.\"></span>
\t\t\t</label>
\t\t</div>
\t\t<div class=\"field_row\">
\t\t\t<label>
\t\t\t\t<input type=\"checkbox\" name=\"settings[core_tickets.process_agent_fwd]\" ";
        // line 437
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.process_agent_fwd"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\tWhen an agent forwards an email to helpdesk, attempt to extract and process original email and add the agents reply as a reply. <a href=\"https://support.deskpro.com/kb/articles/106\" target=\"_blank\">Visit our helpdesk for more information</a>.
\t\t\t</label>
\t\t</div>
\t</div>
</div>

<div class=\"setting_field_row\">
\t<h4>Archiving</h4>
\t<div class=\"setting_fields\">
\t\t<div class=\"field_row\">
\t\t\tFor very large helpdesks with millions of tickets, archiving can greatly improve performance of the agent interace and filters.
\t\t\tWhen an old ticket is archived it is moved out of your filters and into the 'Archived' section. You can still view archived
\t\t\ttickets and agents with permission can restore archived tickets.
\t\t\t<br />
\t\t\t<br />

\t\t\t<label><input id=\"archive_toggle\" type=\"checkbox\" name=\"settings[core_tickets.use_archive]\" ";
        // line 454
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.use_archive"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " value=\"1\" />
\t\t\tUse ticket archiving
\t\t\t</label>

\t\t\t<div id=\"archive_options\" ";
        // line 458
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.use_archive"), "method"))) {
            echo "style=\"display: none\"";
        }
        echo ">
\t\t\t\t<div style=\"padding-top: 5px; padding-bottom: 5px;\">
\t\t\t\t\tHow long until resolved tickets are automatically sent to the archive?
\t\t\t\t\t";
        // line 461
        $context["options"] = array(86400 => "1 day", 259200 => "3 days", 432000 => "5 days", 604800 => "1 week", 1209600 => "2 weeks", 1814400 => "3 weeks", 2419000 => "1 month", 4838000 => "2 months", 7257000 => "3 months", 14514000 => "6 months", 21771000 => "9 months", 29028000 => "1 year", 58056000 => "2 years");
        // line 476
        echo "\t\t\t\t\t<select name=\"settings[core_tickets.auto_archive_time]\">
\t\t\t\t\t\t";
        // line 477
        if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_options_);
        foreach ($context['_seq'] as $context["k"] => $context["v"]) {
            // line 478
            echo "\t\t\t\t\t\t\t<option value=\"";
            if (isset($context["k"])) { $_k_ = $context["k"]; } else { $_k_ = null; }
            echo twig_escape_filter($this->env, $_k_, "html", null, true);
            echo "\" ";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if (isset($context["k"])) { $_k_ = $context["k"]; } else { $_k_ = null; }
            if (($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.auto_archive_time"), "method") == $_k_)) {
                echo "selected=\"selected\"";
            }
            echo ">";
            if (isset($context["v"])) { $_v_ = $context["v"]; } else { $_v_ = null; }
            echo twig_escape_filter($this->env, $_v_, "html", null, true);
            echo "</option>
\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['k'], $context['v'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 480
        echo "\t\t\t\t\t</select>
\t\t\t\t</div>

\t\t\t\t";
        // line 483
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.use_archive"), "method")) {
            // line 484
            echo "\t\t\t\t\t<div class=\"alert-message block-message\" style=\"margin: 4px 25px 0 25px\">
\t\t\t\t\t\tIf there has been an error and the tickets you see in the agent interface do not seem to be correct,
\t\t\t\t\t\tthere may be a problem with your search tables containing invalid records.
\t\t\t\t\t\t<br /><br />
\t\t\t\t\t\t<a class=\"clean-white\" href=\"";
            // line 488
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_ticketfeatures_regensearch"), "html", null, true);
            echo "\">Click here to regenerate the ticket search tables</a>
\t\t\t\t\t</div>
\t\t\t\t";
        }
        // line 491
        echo "\t\t\t</div>
\t\t</div>
\t</div>
</div>

<div class=\"setting_field_row\">
\t<h4>Recycle Bin</h4>
\t<div class=\"setting_fields\">
\t\t<div class=\"field_row\">
\t\t\tWhen agents delete or spam a ticket, it is sent to the Recycle Bin. Tickets in the Recycle Bin can be viewed by from the Agent Interface by clicking on the button in the Tickets section.

\t\t\t<br />
\t\t\t<br />

\t\t\t<table cellspacing=\"0\" cellpadding=\"2\">
\t\t\t\t<tr>
\t\t\t\t\t<td>How long until deleted tickets are permanently purged from the system?</td>
\t\t\t\t\t<td>
\t\t\t\t\t\t";
        // line 509
        $context["options"] = array(86400 => "1 day", 259200 => "3 days", 432000 => "5 days", 604800 => "1 week", 172800 => "2 weeks", 1814400 => "3 weeks", 2419000 => "1 month", 4838000 => "2 months", 7257000 => "3 months", 14514000 => "6 months", 21771000 => "9 months", 29028000 => "1 year", 58056000 => "2 years");
        // line 524
        echo "\t\t\t\t\t\t<select name=\"settings[core_tickets.hard_delete_time]\">
\t\t\t\t\t\t\t";
        // line 525
        if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_options_);
        foreach ($context['_seq'] as $context["k"] => $context["v"]) {
            // line 526
            echo "\t\t\t\t\t\t\t\t<option value=\"";
            if (isset($context["k"])) { $_k_ = $context["k"]; } else { $_k_ = null; }
            echo twig_escape_filter($this->env, $_k_, "html", null, true);
            echo "\" ";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if (isset($context["k"])) { $_k_ = $context["k"]; } else { $_k_ = null; }
            if (($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.hard_delete_time"), "method") == $_k_)) {
                echo "selected=\"selected\"";
            }
            echo ">";
            if (isset($context["v"])) { $_v_ = $context["v"]; } else { $_v_ = null; }
            echo twig_escape_filter($this->env, $_v_, "html", null, true);
            echo "</option>
\t\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['k'], $context['v'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 528
        echo "\t\t\t\t\t\t</select><br />
\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t\t<tr>
\t\t\t\t\t<td>How long until spammed tickets are permanently purged from the system?&nbsp;&nbsp;</td>
\t\t\t\t\t<td>
\t\t\t\t\t\t";
        // line 534
        $context["options"] = array(86400 => "1 day", 259200 => "3 days", 432000 => "5 days", 604800 => "1 week", 172800 => "2 weeks", 1814400 => "3 weeks", 2419000 => "1 month", 4838000 => "2 months", 7257000 => "3 months", 14514000 => "6 months", 21771000 => "9 months", 29028000 => "1 year", 58056000 => "2 years");
        // line 549
        echo "\t\t\t\t\t\t<select name=\"settings[core_tickets.spam_delete_time]\">
\t\t\t\t\t\t\t";
        // line 550
        if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_options_);
        foreach ($context['_seq'] as $context["k"] => $context["v"]) {
            // line 551
            echo "\t\t\t\t\t\t\t\t<option value=\"";
            if (isset($context["k"])) { $_k_ = $context["k"]; } else { $_k_ = null; }
            echo twig_escape_filter($this->env, $_k_, "html", null, true);
            echo "\" ";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if (isset($context["k"])) { $_k_ = $context["k"]; } else { $_k_ = null; }
            if (($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.spam_delete_time"), "method") == $_k_)) {
                echo "selected=\"selected\"";
            }
            echo ">";
            if (isset($context["v"])) { $_v_ = $context["v"]; } else { $_v_ = null; }
            echo twig_escape_filter($this->env, $_v_, "html", null, true);
            echo "</option>
\t\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['k'], $context['v'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 553
        echo "\t\t\t\t\t\t</select>
\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t</table>

\t\t\t<div class=\"alert-message block-message\" style=\"margin: 4px 25px 0 25px\">
\t\t\t\tClick the button below to run the cleanup now to delete everything in the Recycle Bin.
\t\t\t\t<br /><br />
\t\t\t\t<a class=\"clean-white\" href=\"";
        // line 561
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_ticketfeatures_purgetrash", array("security_token" => $this->env->getExtension('deskpro_templating')->securityToken("purge_trash"))), "html", null, true);
        echo "\">Click here to permanently purge everything in the Recycle Bin</a>
\t\t\t</div>
\t\t</div>
\t</div>
</div>

<div class=\"setting_field_row\" style=\"text-align: center\">
\t<button class=\"btn primary\">";
        // line 568
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.save_settings");
        echo "</button>
</div>

</form>

<script type=\"text/javascript\">
\$(document).ready(function() {
\t\$('#ref_options_toggle').on('click', function() {
\t\tif (\$(this).is(':checked')) {
\t\t\t\$('#ref_options').slideDown();
\t\t} else {
\t\t\t\$('#ref_options').slideUp();
\t\t}
\t});

\t\$('#archive_toggle').on('click', function() {
\t\tif (\$(this).is(':checked')) {
\t\t\t\$('#archive_options').slideDown();
\t\t} else {
\t\t\t\$('#archive_options').slideUp();
\t\t}
\t});

\t\$('#bill_auto_opt').on('click', function() {
\t\tif (this.checked) {
\t\t\t\$('#show_reply_bill').slideDown();
\t\t} else {
\t\t\t\$('#show_reply_bill').slideUp();
\t\t\t\$('#show_reply_bill').find(':checkbox').prop('checked', false);
\t\t}
\t});

\t\$('#billing_on_new').on('click', function() {
\t\tif (this.checked) {
\t\t\t\$('#billing_auto_timer_new').slideDown();
\t\t} else {
\t\t\t\$('#billing_auto_timer_new').slideUp();
\t\t\t\$('#billing_auto_timer_new').find(':checkbox').prop('checked', false);
\t\t}
\t});

\t\$('#billing_enable').on('click', function() {
\t\tif (this.checked) {
\t\t\t\$('#billing_dependencies').slideDown();
\t\t} else {
\t\t\t\$('#billing_dependencies').slideUp();
\t\t}
\t});

\tfunction validateRefFormat()
\t{
\t\tif (!\$('#ref_options_toggle').is(':checked')) {
\t\t\t\$('#ref_format').val('');
\t\t\treturn true;
\t\t}

\t\tvar format   = \$.trim(\$('#ref_format').val());
\t\tvar digitLen = parseInt(\$('#ref_format_digits').find(':selected').val() || 0);

\t\tif (!format) {
\t\t\t\$('#ref_options_toggle').prop('checked', false);
\t\t\treturn true;
\t\t}

\t\t\$('#ref_format_err_len').hide();
\t\t\$('#ref_format_err_regex').hide();

\t\tif (format.length + digitLen > 80) {
\t\t\tconsole.log(\"Ref: Bad length\");
\t\t\t\$('#ref_format_err_len').show();
\t\t\treturn false;
\t\t}

\t\tif (!format.match(/^[a-zA-Z0-9_\\-<>#\\?]+\$/)) {
\t\t\tconsole.log(\"Ref: Bad format\");
\t\t\t\$('#ref_format_err_regex').show();
\t\t\treturn false;
\t\t}

\t\treturn true;
\t}

\t\$('#ref_format').on('blur', validateRefFormat);
\t\$('#ref_format_digits').on('change', validateRefFormat);

\t\$('#settings_form').on('submit', function(ev) {
\t\tif (!validateRefFormat()) {
\t\t\tev.preventDefault();
\t\t\tvar offset = \$('#ref_format').offset();
\t\t\toffset.top -= 30;

\t\t\t\$('html, body').animate({
\t\t\t\tscrollTop: offset.top
\t\t\t});

\t\t\treturn;
\t\t}
\t});
});
</script>
";
    }

    public function getTemplateName()
    {
        return "AdminBundle:TicketFeatures:index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1163 => 568,  1143 => 553,  1087 => 526,  1077 => 509,  1051 => 488,  1037 => 480,  1010 => 476,  999 => 458,  932 => 414,  899 => 405,  895 => 404,  933 => 149,  914 => 133,  909 => 132,  833 => 359,  783 => 332,  755 => 320,  666 => 300,  453 => 203,  639 => 110,  568 => 254,  520 => 249,  657 => 287,  572 => 251,  609 => 459,  20 => 1,  659 => 274,  562 => 240,  548 => 165,  558 => 244,  479 => 206,  589 => 100,  457 => 145,  413 => 172,  953 => 430,  948 => 403,  935 => 394,  929 => 391,  916 => 382,  864 => 365,  844 => 348,  816 => 342,  807 => 370,  801 => 338,  774 => 349,  766 => 328,  737 => 318,  685 => 293,  664 => 194,  635 => 281,  593 => 445,  546 => 236,  532 => 240,  865 => 221,  852 => 216,  838 => 208,  820 => 201,  781 => 333,  764 => 321,  725 => 164,  632 => 283,  602 => 265,  565 => 171,  529 => 282,  505 => 267,  487 => 213,  473 => 221,  1853 => 842,  1849 => 841,  1839 => 837,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 813,  1778 => 812,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 782,  1705 => 781,  1688 => 770,  1681 => 769,  1677 => 768,  1667 => 764,  1656 => 762,  1648 => 757,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 714,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 695,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 651,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 610,  1289 => 607,  1276 => 604,  1263 => 601,  1244 => 596,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 565,  1181 => 561,  1171 => 557,  1158 => 550,  1114 => 534,  1104 => 526,  1072 => 512,  1062 => 508,  1024 => 494,  1014 => 490,  1000 => 482,  990 => 478,  980 => 474,  960 => 466,  918 => 448,  888 => 376,  834 => 346,  673 => 342,  636 => 185,  462 => 201,  454 => 103,  1144 => 542,  1139 => 399,  1131 => 399,  1127 => 397,  1110 => 396,  1092 => 391,  1089 => 390,  1086 => 520,  1084 => 388,  1063 => 387,  1060 => 386,  1055 => 385,  1050 => 384,  1035 => 372,  1019 => 267,  1003 => 263,  959 => 251,  900 => 377,  880 => 434,  870 => 430,  867 => 366,  859 => 362,  848 => 215,  839 => 376,  828 => 357,  823 => 374,  809 => 181,  800 => 178,  797 => 366,  794 => 336,  786 => 174,  740 => 162,  734 => 332,  703 => 354,  693 => 350,  630 => 278,  626 => 140,  614 => 275,  610 => 103,  581 => 247,  564 => 229,  525 => 235,  722 => 162,  697 => 256,  674 => 277,  671 => 425,  577 => 257,  569 => 243,  557 => 169,  502 => 229,  497 => 240,  445 => 197,  729 => 159,  684 => 261,  676 => 299,  669 => 254,  660 => 145,  647 => 286,  643 => 244,  601 => 178,  570 => 172,  522 => 200,  501 => 265,  296 => 67,  374 => 149,  631 => 111,  616 => 281,  608 => 266,  605 => 16,  596 => 102,  574 => 165,  561 => 209,  527 => 233,  433 => 190,  388 => 137,  426 => 177,  383 => 182,  461 => 246,  370 => 147,  395 => 221,  294 => 72,  223 => 78,  220 => 36,  492 => 395,  468 => 201,  444 => 193,  410 => 169,  397 => 141,  377 => 159,  262 => 113,  250 => 55,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 2108,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 1383,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 1303,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 1173,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 1041,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 970,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 538,  1126 => 916,  1106 => 528,  1100 => 897,  1026 => 841,  997 => 822,  983 => 816,  975 => 402,  956 => 800,  939 => 786,  902 => 130,  894 => 128,  879 => 400,  757 => 631,  727 => 316,  716 => 308,  670 => 297,  528 => 232,  476 => 253,  435 => 208,  354 => 153,  341 => 212,  192 => 30,  321 => 122,  243 => 54,  793 => 350,  780 => 348,  758 => 335,  700 => 221,  686 => 150,  652 => 274,  638 => 282,  620 => 265,  545 => 259,  523 => 231,  494 => 10,  459 => 226,  438 => 195,  351 => 135,  347 => 152,  402 => 222,  268 => 65,  430 => 141,  411 => 201,  379 => 219,  322 => 123,  315 => 119,  289 => 124,  284 => 102,  255 => 115,  234 => 81,  1133 => 400,  1124 => 551,  1121 => 56,  1116 => 549,  1113 => 403,  1108 => 51,  1103 => 394,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 483,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 461,  996 => 262,  989 => 454,  985 => 315,  981 => 257,  977 => 313,  970 => 91,  966 => 437,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 148,  919 => 290,  917 => 289,  908 => 411,  905 => 378,  896 => 280,  891 => 403,  877 => 270,  862 => 267,  857 => 380,  837 => 347,  832 => 250,  827 => 375,  821 => 247,  803 => 179,  778 => 389,  769 => 165,  765 => 386,  753 => 328,  746 => 319,  743 => 318,  735 => 170,  730 => 330,  720 => 363,  717 => 362,  712 => 156,  691 => 204,  678 => 149,  654 => 144,  587 => 14,  576 => 179,  539 => 241,  517 => 230,  471 => 212,  441 => 239,  437 => 101,  418 => 201,  386 => 164,  373 => 149,  304 => 114,  270 => 101,  265 => 163,  229 => 75,  477 => 138,  455 => 224,  448 => 242,  429 => 179,  407 => 119,  399 => 162,  389 => 170,  375 => 162,  358 => 110,  349 => 131,  335 => 139,  327 => 124,  298 => 144,  280 => 102,  249 => 205,  194 => 112,  142 => 32,  344 => 140,  318 => 86,  306 => 116,  295 => 74,  357 => 154,  300 => 111,  286 => 80,  276 => 105,  269 => 103,  254 => 56,  128 => 55,  237 => 118,  165 => 42,  122 => 33,  798 => 337,  770 => 179,  759 => 112,  748 => 337,  731 => 108,  721 => 305,  718 => 313,  708 => 309,  696 => 147,  617 => 461,  590 => 226,  553 => 264,  550 => 156,  540 => 234,  533 => 255,  500 => 397,  493 => 225,  489 => 257,  482 => 223,  467 => 210,  464 => 202,  458 => 220,  452 => 217,  449 => 132,  415 => 184,  382 => 162,  372 => 157,  361 => 129,  356 => 215,  339 => 132,  302 => 131,  285 => 105,  258 => 136,  123 => 31,  108 => 51,  424 => 187,  394 => 139,  380 => 151,  338 => 155,  319 => 142,  316 => 131,  312 => 118,  290 => 105,  267 => 96,  206 => 75,  110 => 44,  240 => 93,  224 => 95,  219 => 128,  217 => 94,  202 => 109,  186 => 70,  170 => 113,  100 => 25,  67 => 17,  14 => 1,  1096 => 524,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 478,  1013 => 477,  1007 => 274,  1002 => 823,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 402,  940 => 298,  937 => 395,  928 => 452,  926 => 413,  915 => 239,  912 => 236,  903 => 231,  898 => 440,  892 => 229,  889 => 748,  887 => 402,  884 => 374,  876 => 222,  874 => 215,  871 => 368,  863 => 214,  861 => 213,  858 => 212,  850 => 378,  843 => 206,  840 => 406,  815 => 372,  812 => 244,  808 => 199,  804 => 395,  799 => 196,  791 => 193,  785 => 238,  775 => 184,  771 => 183,  754 => 340,  728 => 317,  726 => 169,  723 => 168,  715 => 105,  711 => 152,  709 => 222,  706 => 302,  698 => 208,  694 => 308,  692 => 155,  689 => 302,  681 => 300,  677 => 149,  675 => 289,  663 => 276,  661 => 277,  650 => 246,  646 => 112,  629 => 183,  627 => 266,  625 => 180,  622 => 270,  598 => 174,  592 => 261,  586 => 264,  575 => 174,  566 => 242,  556 => 244,  554 => 240,  541 => 163,  536 => 241,  515 => 209,  511 => 269,  509 => 244,  488 => 155,  486 => 220,  483 => 341,  465 => 147,  463 => 216,  450 => 195,  432 => 211,  419 => 100,  371 => 182,  362 => 111,  353 => 141,  337 => 126,  333 => 144,  309 => 117,  303 => 70,  299 => 148,  291 => 176,  272 => 93,  261 => 91,  253 => 161,  239 => 82,  235 => 44,  213 => 139,  200 => 45,  198 => 85,  159 => 39,  149 => 36,  146 => 43,  131 => 36,  116 => 21,  79 => 17,  74 => 22,  71 => 24,  836 => 320,  817 => 398,  814 => 200,  811 => 317,  805 => 313,  787 => 333,  779 => 169,  776 => 305,  773 => 347,  761 => 296,  751 => 163,  747 => 325,  742 => 336,  739 => 333,  736 => 317,  724 => 214,  705 => 278,  702 => 601,  688 => 113,  680 => 278,  667 => 296,  662 => 271,  656 => 418,  649 => 285,  644 => 284,  641 => 268,  624 => 109,  613 => 264,  607 => 273,  597 => 270,  591 => 267,  584 => 259,  579 => 175,  563 => 96,  559 => 245,  551 => 243,  547 => 242,  537 => 90,  524 => 220,  512 => 227,  507 => 156,  504 => 213,  498 => 213,  485 => 153,  480 => 254,  472 => 205,  466 => 210,  460 => 221,  447 => 143,  442 => 196,  434 => 212,  428 => 185,  422 => 176,  404 => 149,  368 => 161,  364 => 156,  340 => 170,  334 => 129,  330 => 148,  325 => 134,  292 => 105,  287 => 67,  282 => 104,  279 => 120,  273 => 170,  266 => 113,  256 => 135,  252 => 109,  228 => 79,  218 => 77,  201 => 117,  64 => 16,  51 => 10,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 1705,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 737,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 731,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 725,  1559 => 601,  1552 => 720,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 708,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 645,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 624,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 1001,  1257 => 1000,  1254 => 479,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 569,  1196 => 958,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 938,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 561,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 550,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 898,  1099 => 393,  1095 => 419,  1091 => 418,  1082 => 525,  1079 => 524,  1076 => 359,  1070 => 875,  1057 => 491,  1052 => 504,  1045 => 484,  1040 => 377,  1036 => 283,  1032 => 496,  1028 => 274,  1023 => 393,  1020 => 392,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 821,  979 => 379,  974 => 256,  967 => 373,  962 => 803,  958 => 801,  954 => 253,  950 => 153,  945 => 152,  942 => 460,  938 => 150,  934 => 364,  927 => 147,  923 => 387,  920 => 412,  910 => 353,  901 => 347,  897 => 129,  890 => 343,  886 => 50,  883 => 401,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 377,  841 => 210,  835 => 312,  830 => 249,  826 => 202,  822 => 354,  818 => 246,  813 => 183,  810 => 345,  806 => 180,  802 => 198,  795 => 336,  792 => 239,  789 => 349,  784 => 188,  782 => 187,  777 => 291,  772 => 289,  768 => 334,  763 => 327,  760 => 329,  756 => 230,  752 => 176,  745 => 281,  741 => 280,  738 => 379,  732 => 171,  719 => 279,  714 => 264,  710 => 310,  704 => 267,  699 => 303,  695 => 297,  690 => 263,  687 => 279,  683 => 346,  679 => 298,  672 => 276,  668 => 247,  665 => 285,  658 => 244,  645 => 277,  640 => 285,  634 => 267,  628 => 466,  623 => 107,  619 => 298,  611 => 268,  606 => 263,  603 => 267,  599 => 262,  595 => 132,  583 => 263,  580 => 257,  573 => 274,  560 => 268,  543 => 235,  538 => 162,  534 => 233,  530 => 202,  526 => 229,  521 => 226,  518 => 233,  514 => 221,  510 => 227,  503 => 266,  496 => 226,  490 => 214,  484 => 394,  474 => 202,  470 => 231,  446 => 241,  440 => 102,  436 => 183,  431 => 186,  425 => 193,  416 => 231,  412 => 230,  408 => 141,  403 => 194,  400 => 225,  396 => 171,  392 => 139,  385 => 152,  381 => 133,  367 => 147,  363 => 164,  359 => 136,  355 => 326,  350 => 108,  346 => 140,  343 => 134,  328 => 135,  324 => 125,  313 => 81,  307 => 108,  301 => 124,  288 => 116,  283 => 167,  271 => 160,  257 => 148,  251 => 88,  238 => 103,  233 => 81,  195 => 121,  191 => 35,  187 => 33,  183 => 52,  130 => 52,  88 => 44,  76 => 30,  115 => 45,  95 => 23,  655 => 270,  651 => 232,  648 => 269,  637 => 273,  633 => 272,  621 => 462,  618 => 277,  615 => 268,  604 => 186,  600 => 271,  588 => 305,  585 => 260,  582 => 177,  571 => 242,  567 => 95,  555 => 239,  552 => 238,  549 => 237,  544 => 230,  542 => 290,  535 => 233,  531 => 158,  519 => 87,  516 => 248,  513 => 228,  508 => 230,  506 => 217,  499 => 241,  495 => 239,  491 => 212,  481 => 152,  478 => 235,  475 => 184,  469 => 203,  456 => 204,  451 => 243,  443 => 194,  439 => 129,  427 => 177,  423 => 187,  420 => 208,  409 => 179,  405 => 94,  401 => 148,  391 => 173,  387 => 134,  384 => 160,  378 => 91,  365 => 131,  360 => 89,  348 => 191,  336 => 132,  332 => 150,  329 => 127,  323 => 135,  310 => 114,  305 => 112,  277 => 170,  274 => 102,  263 => 97,  259 => 112,  247 => 160,  244 => 51,  241 => 91,  222 => 105,  210 => 122,  207 => 110,  204 => 89,  184 => 28,  181 => 110,  167 => 50,  157 => 94,  96 => 46,  421 => 147,  417 => 71,  414 => 142,  406 => 130,  398 => 165,  393 => 177,  390 => 153,  376 => 115,  369 => 90,  366 => 174,  352 => 140,  345 => 213,  342 => 127,  331 => 125,  326 => 143,  320 => 121,  317 => 82,  314 => 136,  311 => 85,  308 => 116,  297 => 111,  293 => 119,  281 => 106,  278 => 71,  275 => 107,  264 => 103,  260 => 107,  248 => 75,  245 => 104,  242 => 89,  231 => 52,  227 => 131,  215 => 88,  212 => 111,  209 => 89,  197 => 129,  177 => 49,  171 => 46,  161 => 68,  132 => 34,  121 => 24,  105 => 45,  99 => 45,  81 => 31,  77 => 19,  180 => 54,  176 => 109,  156 => 38,  143 => 71,  139 => 25,  118 => 49,  189 => 88,  185 => 46,  173 => 54,  166 => 36,  152 => 54,  174 => 55,  164 => 58,  154 => 67,  150 => 89,  137 => 87,  133 => 64,  127 => 36,  107 => 50,  102 => 41,  83 => 26,  78 => 37,  53 => 10,  23 => 6,  42 => 7,  138 => 86,  134 => 44,  109 => 40,  103 => 30,  97 => 43,  94 => 28,  84 => 9,  75 => 25,  69 => 32,  66 => 16,  54 => 27,  44 => 7,  230 => 74,  226 => 141,  203 => 86,  193 => 58,  188 => 29,  182 => 77,  178 => 66,  168 => 62,  163 => 78,  160 => 68,  155 => 55,  148 => 33,  145 => 47,  140 => 65,  136 => 18,  125 => 16,  120 => 30,  113 => 14,  101 => 33,  92 => 41,  89 => 27,  85 => 23,  73 => 19,  62 => 16,  59 => 15,  56 => 11,  41 => 14,  126 => 54,  119 => 65,  111 => 48,  106 => 37,  98 => 44,  93 => 31,  86 => 27,  70 => 18,  60 => 15,  28 => 114,  36 => 5,  114 => 33,  104 => 37,  91 => 31,  80 => 25,  63 => 30,  58 => 29,  40 => 6,  34 => 4,  45 => 8,  61 => 12,  55 => 13,  48 => 9,  39 => 6,  35 => 4,  31 => 6,  26 => 1,  21 => 2,  46 => 10,  29 => 2,  57 => 14,  50 => 11,  47 => 8,  38 => 8,  33 => 13,  49 => 19,  32 => 3,  246 => 140,  236 => 87,  232 => 43,  225 => 82,  221 => 69,  216 => 65,  214 => 135,  211 => 111,  208 => 67,  205 => 87,  199 => 65,  196 => 85,  190 => 101,  179 => 94,  175 => 76,  172 => 52,  169 => 37,  162 => 80,  158 => 28,  153 => 27,  151 => 24,  147 => 66,  144 => 51,  141 => 55,  135 => 35,  129 => 35,  124 => 70,  117 => 32,  112 => 40,  90 => 19,  87 => 21,  82 => 20,  72 => 33,  68 => 18,  65 => 17,  52 => 12,  43 => 8,  37 => 5,  30 => 1,  27 => 2,  25 => 65,  24 => 3,  22 => 34,  19 => 1,);
    }
}
