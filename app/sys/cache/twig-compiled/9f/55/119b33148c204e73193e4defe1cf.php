<?php

/* AgentBundle:Ticket:view.html.twig */
class __TwigTemplate_9f55119b33148c204e73193e4defe1cf extends \Application\DeskPRO\Twig\Template
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
        $context["flag"] = $this->env->loadTemplate("AgentBundle:Common:macro-flagname.html.twig");
        // line 2
        $context["agentui"] = $this->env->loadTemplate("AgentBundle:Common:agent-macros.html.twig");
        // line 3
        echo "<script>
pageMeta.fragmentClass = 'DeskPRO.Agent.PageFragment.Page.Ticket';
pageMeta.title         = ";
        // line 5
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_jsonencode_filter($this->getAttribute($_ticket_, "subject"));
        echo ";
pageMeta.ticket_id     = ";
        // line 6
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "id"), "html", null, true);
        echo ";
pageMeta.alert_id      = 'ticket-";
        // line 7
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "id"), "html", null, true);
        echo "';
pageMeta.url_fragment  = '";
        // line 8
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->urlFragment("agent_ticket_view", array("ticket_id" => $this->getAttribute($_ticket_, "id"))), "html", null, true);
        echo "';

pageMeta.labelsAutocompleteUrl        = '";
        // line 10
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_ajax_labels_autocomplete", array("label_type" => "tickets")), "html", null, true);
        echo "';
pageMeta.labelsSaveUrl                = '";
        // line 11
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_ticket_ajax_labels_save", array("ticket_id" => $this->getAttribute($_ticket_, "id"))), "html", null, true);
        echo "';
pageMeta.getMacroUrl                  = '";
        // line 12
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_ticket_ajax_get_macro", array("ticket_id" => $this->getAttribute($_ticket_, "id"))), "html", null, true);
        echo "?macro_id=\$macro_id';
pageMeta.saveActionsUrl               = '";
        // line 13
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_ticket_ajax_save_actions", array("ticket_id" => $this->getAttribute($_ticket_, "id"))), "html", null, true);
        echo "';
pageMeta.deleteTicketUrl              = '";
        // line 14
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_ticketsearch_ajax_delete_tickets"), "html", null, true);
        echo "';
pageMeta.uploadAttachUrl              = '";
        // line 15
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_accept_upload"), "html", null, true);
        echo "';
pageMeta.getMessageQuoteUrl           = '";
        // line 16
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, strtr($this->env->getExtension('routing')->getPath("agent_ticket_message_ajax_getquote", array("ticket_id" => $this->getAttribute($_ticket_, "id"), "message_id" => "000")), array("000" => "{message_id}")), "html", null, true);
        echo "';
pageMeta.viewPersonUrl                = '";
        // line 17
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_people_view", array("person_id" => $this->getAttribute($this->getAttribute($_ticket_, "person"), "id"))), "html", null, true);
        echo "';
pageMeta.lastMessageId = ";
        // line 18
        if (isset($context["last_message_id"])) { $_last_message_id_ = $context["last_message_id"]; } else { $_last_message_id_ = null; }
        echo twig_escape_filter($this->env, ((array_key_exists("last_message_id", $context)) ? (_twig_default_filter($_last_message_id_, 0)) : (0)), "html", null, true);
        echo ";
pageMeta.lastLogId = ";
        // line 19
        if (isset($context["last_log_id"])) { $_last_log_id_ = $context["last_log_id"]; } else { $_last_log_id_ = null; }
        echo twig_escape_filter($this->env, ((array_key_exists("last_log_id", $context)) ? (_twig_default_filter($_last_log_id_, 0)) : (0)), "html", null, true);
        echo ";
pageMeta.ticket_reverse_order = ";
        // line 20
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent.ticket_reverse_order"), "method")) {
            echo "true";
        } else {
            echo "false";
        }
        echo ";

pageMeta.last_activity = ";
        // line 22
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "getLastActivityDate", array(), "method"), "getTimestamp", array(), "method"), "html", null, true);
        echo ";

pageMeta.person_id    = ";
        // line 24
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "person"), "id"), "html", null, true);
        echo ";
pageMeta.person_email = ";
        // line 25
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_jsonencode_filter($this->getAttribute($this->getAttribute($_ticket_, "person"), "primary_email_address"));
        echo ";
pageMeta.person_name  = ";
        // line 26
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_jsonencode_filter($this->getAttribute($this->getAttribute($_ticket_, "person"), "display_name"));
        echo ";

pageMeta.ticket = ";
        // line 28
        if (isset($context["ticket_api"])) { $_ticket_api_ = $context["ticket_api"]; } else { $_ticket_api_ = null; }
        echo twig_jsonencode_filter($_ticket_api_);
        echo ";
pageMeta.api_data = ";
        // line 29
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_jsonencode_filter($this->getAttribute($_ticket_, "toApiData", array(), "method"));
        echo ";

pageMeta.ticket_perms = ";
        // line 31
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        echo twig_jsonencode_filter($_ticket_perms_);
        echo ";
";
        // line 32
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_ticket_, "isLocked", array(0 => $this->getAttribute($_app_, "user")), "method")) {
            // line 33
            echo "\tpageMeta.isLocked = true;
";
        } else {
            // line 35
            echo "\tpageMeta.isLocked = false;
";
        }
        // line 37
        echo "pageMeta.unlockOnClose = ";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo (($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.unlock_on_close"), "method")) ? ("true") : ("false"));
        echo ";

";
        // line 39
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ($this->getAttribute($this->getAttribute($_ticket_, "person"), "organization")) {
            // line 40
            echo "\tpageMeta.viewOrgUrl = '";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_org_view", array("organization_id" => $this->getAttribute($this->getAttribute($this->getAttribute($_ticket_, "person"), "organization"), "id"))), "html", null, true);
            echo "';
";
        }
        // line 42
        echo "
";
        // line 43
        $context["baseId"] = $this->env->getExtension('deskpro_templating')->elUid();
        // line 44
        echo "pageMeta.baseId = '";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "';

pageMeta.isDeleted = ";
        // line 46
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (($this->getAttribute($_ticket_, "hidden_status") == "deleted")) {
            echo "true";
        } else {
            echo "false";
        }
        echo ";
pageMeta.isClosed = ";
        // line 47
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (($this->getAttribute($_ticket_, "status") == "closed")) {
            echo "true";
        } else {
            echo "false";
        }
        echo ";
pageMeta.isSpam = ";
        // line 48
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (($this->getAttribute($_ticket_, "hidden_status") == "spam")) {
            echo "true";
        } else {
            echo "false";
        }
        echo ";
";
        // line 50
        echo "pageMeta.auto_start_bill = ";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.billing_auto_timer"), "method")) {
            echo "true";
        } else {
            echo "false";
        }
        echo ";

pageMeta.agentMap = ";
        // line 52
        if (isset($context["agent_map"])) { $_agent_map_ = $context["agent_map"]; } else { $_agent_map_ = null; }
        echo twig_jsonencode_filter($_agent_map_);
        echo ";
pageMeta.goNextOnReply = ";
        // line 53
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo (($this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent.ticket_go_next_reply"), "method")) ? ("true") : ("false"));
        echo ";
pageMeta.lang = ";
        // line 54
        echo twig_jsonencode_filter(array("find_ticket" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.find_ticket"), "users_tickets" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.users_tickets"), "open_tickets" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.open_tickets"), "filter_results" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.filter_results"), "search" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.search")));
        // line 60
        echo ";
</script>
<div class=\"layout-content with-scrollbar page-ticket ";
        // line 62
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_ticket_, "isLocked", array(0 => $this->getAttribute($_app_, "user")), "method")) {
            echo "locked";
        }
        echo " ";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, strtr($this->getAttribute($_ticket_, "status_code"), array("." => "_")), "html", null, true);
        echo " urgency-";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "urgency"), "html", null, true);
        echo "\">
<div class=\"scrollbar disable\"><div class=\"track\"><div class=\"thumb\"><div class=\"end\"></div></div></div></div>
<div class=\"scroll-viewport\"><div class=\"scroll-content\">

";
        // line 66
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $context["locked_self"] = ($this->getAttribute($_ticket_, "locked_by_agent") && ($this->getAttribute($this->getAttribute($_ticket_, "locked_by_agent"), "id") == $this->getAttribute($this->getAttribute($_app_, "user"), "id")));
        // line 67
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ($this->getAttribute($_ticket_, "is_locked")) {
            echo "<div class=\"locked-overlay\" id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_locked_overlay\"></div>";
        }
        // line 68
        echo "
<form class=\"value-form\" id=\"";
        // line 69
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_value_form\" style=\"display:none\">
\t<input type=\"hidden\" name=\"department_id\" class=\"department_id\" value=\"";
        // line 70
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_ticket_, "department", array(), "any", false, true), "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "department", array(), "any", false, true), "id"), 0)) : (0)), "html", null, true);
        echo "\" />
\t<input type=\"hidden\" name=\"agent_id\" class=\"agent_id\" value=\"";
        // line 71
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_ticket_, "agent", array(), "any", false, true), "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "agent", array(), "any", false, true), "id"), 0)) : (0)), "html", null, true);
        echo "\" />
\t<input type=\"hidden\" name=\"agent_team_id\" class=\"agent_team_id\" value=\"";
        // line 72
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_ticket_, "agent_team", array(), "any", false, true), "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "agent_team", array(), "any", false, true), "id"), 0)) : (0)), "html", null, true);
        echo "\" />
\t<input type=\"hidden\" name=\"product_id\" class=\"product_id\" value=\"";
        // line 73
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_ticket_, "product", array(), "any", false, true), "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "product", array(), "any", false, true), "id"), 0)) : (0)), "html", null, true);
        echo "\" />
\t<input type=\"hidden\" name=\"category_id\" class=\"category_id\" value=\"";
        // line 74
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_ticket_, "category", array(), "any", false, true), "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "category", array(), "any", false, true), "id"), 0)) : (0)), "html", null, true);
        echo "\" />
\t<input type=\"hidden\" name=\"workflow_id\" class=\"workflow_id\" value=\"";
        // line 75
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_ticket_, "workflow", array(), "any", false, true), "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "workflow", array(), "any", false, true), "id"), 0)) : (0)), "html", null, true);
        echo "\" />
\t<input type=\"hidden\" name=\"priority_id\" class=\"priority_id\" value=\"";
        // line 76
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_ticket_, "priority", array(), "any", false, true), "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "priority", array(), "any", false, true), "id"), 0)) : (0)), "html", null, true);
        echo "\" />
\t<input type=\"hidden\" name=\"language_id\" class=\"language_id\" value=\"";
        // line 77
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_ticket_, "language", array(), "any", false, true), "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "language", array(), "any", false, true), "id"), $this->getAttribute($this->getAttribute($_app_, "languages"), "getDefaultId", array(), "method"))) : ($this->getAttribute($this->getAttribute($_app_, "languages"), "getDefaultId", array(), "method"))), "html", null, true);
        echo "\" />
\t<input type=\"hidden\" name=\"is_hold\" class=\"is_hold\" value=\"";
        // line 78
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_ticket_, "is_hold", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_ticket_, "is_hold"), 0)) : (0)), "html", null, true);
        echo "\" />
\t<input type=\"hidden\" name=\"status\" class=\"status\" value=\"";
        // line 79
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "status"), "html", null, true);
        echo "\" />
\t<input type=\"hidden\" name=\"hidden_status\" class=\"hidden_status\" value=\"";
        // line 80
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "hidden_status"), "html", null, true);
        echo "\" />
</form>

";
        // line 83
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (($this->getAttribute($_ticket_, "status") == "hidden")) {
            // line 84
            echo "\t";
            $this->env->loadTemplate("AgentBundle:Ticket:view-hidden-bar.html.twig")->display($context);
        }
        // line 86
        echo "
<header class=\"main-header\" id=\"";
        // line 87
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_page_header\">
\t<div id=\"";
        // line 88
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_locked_message\" ";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ((!$this->getAttribute($_ticket_, "locked_by_agent"))) {
            echo "style=\"display: none\"";
        }
        echo " ";
        if (isset($context["locked_self"])) { $_locked_self_ = $context["locked_self"]; } else { $_locked_self_ = null; }
        if ($_locked_self_) {
            echo "data-locked-self=\"1\"";
        }
        echo ">
\t\t<div class=\"locked-message\">
\t\t\t<button class=\"dp-btn\" id=\"";
        // line 90
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_unlock_ticket2\" style=\"float:right\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.unlock");
        echo "</button>
\t\t\t<div style=\"margin-right: 100px;\">
\t\t\t\t<div id=\"";
        // line 92
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_locked_message_self\" ";
        if (isset($context["locked_self"])) { $_locked_self_ = $context["locked_self"]; } else { $_locked_self_ = null; }
        if ((!$_locked_self_)) {
            echo "style=\"display: none\"";
        }
        echo ">
\t\t\t\t\t";
        // line 93
        if (isset($context["locked_self"])) { $_locked_self_ = $context["locked_self"]; } else { $_locked_self_ = null; }
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($_locked_self_ && $this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.lock_on_view"), "method"))) {
            // line 94
            echo "\t\t\t\t\t\t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.you_have_auto_lock");
            echo "
\t\t\t\t\t";
        } else {
            // line 96
            echo "\t\t\t\t\t\t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.you_have_locked");
            echo "
\t\t\t\t\t";
        }
        // line 98
        echo "\t\t\t\t\t";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.unlock_on_close"), "method")) {
            echo "<br/>";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.lock_auto_release");
        }
        // line 99
        echo "\t\t\t\t</div>
\t\t\t\t";
        // line 100
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (isset($context["locked_self"])) { $_locked_self_ = $context["locked_self"]; } else { $_locked_self_ = null; }
        if (($this->getAttribute($_ticket_, "locked_by_agent") && (!$_locked_self_))) {
            // line 101
            echo "\t\t\t\t\t<div id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_locked_message_other\">
\t\t\t\t\t\t";
            // line 102
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.agent_has_lock", array("agent" => $this->getAttribute($this->getAttribute($_ticket_, "locked_by_agent"), "display_name")));
            echo "
\t\t\t\t\t</div>
\t\t\t\t";
        }
        // line 105
        echo "\t\t\t</div>
\t\t</div>
\t</div>
\t<div id=\"";
        // line 108
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_hold_message\" ";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ((!$this->getAttribute($_ticket_, "is_hold"))) {
            echo "style=\"display: none\"";
        }
        echo ">
\t\t<div class=\"hold-message\">
\t\t\t<button class=\"dp-btn set-hold unhold\" id=\"";
        // line 110
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_unhold_btn\" style=\"float:right\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.unhold_ticket");
        echo "</button>
\t\t\t<div style=\"margin-right: 100px;\">
\t\t\t\t";
        // line 112
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.currently_on_hold");
        echo "
\t\t\t</div>
\t\t</div>
\t</div>
\t<div class=\"row title first\">
\t\t<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\"><tr><td width=\"10\" nowrap=\"nowrap\">
\t\t\t<div class=\"id-number\" id=\"";
        // line 118
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_idref_switch\">
\t\t\t\t<span id=\"";
        // line 119
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_id_num\" title=\"Ticket ID\">
\t\t\t\t\t<span class=\"copy-btn-outer\"><span class=\"copy-btn\" data-clipboard-text=\"";
        // line 120
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "id"), "html", null, true);
        echo "\" title=\"";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.copy_to_clipboard");
        echo "\"><i class=\"icon-paste\"></i></span></span>
\t\t\t\t\t";
        // line 121
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.id");
        echo ": ";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "id"), "html", null, true);
        echo "
\t\t\t\t</span>
\t\t\t\t<span id=\"";
        // line 123
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_ref_num\" style=\"display: none;\" title=\"Ticket Reference\">
\t\t\t\t\t<span class=\"copy-btn-outer\"><span class=\"copy-btn\" data-clipboard-text=\"";
        // line 124
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "ref"), "html", null, true);
        echo "\" title=\"";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.copy_to_clipboard");
        echo "\"><i class=\"icon-paste\"></i></span></span>
\t\t\t\t\t";
        // line 125
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.id");
        echo ": ";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "ref"), "html", null, true);
        echo "
\t\t\t\t</span>
\t\t\t</div>
\t\t</td><td>
\t\t\t<h1>
\t\t\t\t<span id=\"";
        // line 130
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_showname\">";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "subject"), "html", null, true);
        echo "</span>
\t\t\t\t<span id=\"";
        // line 131
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_editname\" style=\"display: none\"><input type=\"text\" placeholder=\"Enter subject\" name=\"name\" value=\"";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "subject"), "html", null, true);
        echo "\" /></span>
\t\t\t\t<a id=\"";
        // line 132
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_editname_end\" class=\"edit-name-save clean-white\" style=\"display: none\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.done");
        echo "</a>
\t\t\t</h1>
\t\t</td></tr></table>
\t</div>
\t<div class=\"row author last\">
\t\t<div class=\"dp-btn-group dp-dropdown\">
\t\t\t<a id=\"";
        // line 138
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_profile_link\" class=\"dp-btn as-popover preload user-btn\" data-route=\"person:";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_people_view", array("person_id" => $this->getAttribute($_ticket_, "person_id"))), "html", null, true);
        echo "\">
\t\t\t\t<span class=\"text\" style=\"background-image: url(";
        // line 139
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "person"), "getPictureUrl", array(0 => 25), "method"), "html", null, true);
        echo ");\">
\t\t\t\t\t";
        // line 140
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "person"), "getNameWithTitle", array(), "method"), "html", null, true);
        echo "
\t\t\t\t\t<span class=\"email\" id=\"";
        // line 141
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_user_email_text\">(";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_ticket_, "person_email", array(), "any", false, true), "email", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "person_email", array(), "any", false, true), "email"), $this->getAttribute($_ticket_, "person_email_address"))) : ($this->getAttribute($_ticket_, "person_email_address"))), "html", null, true);
        echo ")</span>
\t\t\t\t</span>
\t\t\t</a>
\t\t\t";
        // line 144
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ((twig_length_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "person"), "emails")) > 1)) {
            // line 145
            echo "\t\t\t\t<a class=\"dp-btn dp-dropdown-toggle\" style=\"padding: 7px;\" id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_user_email_menu_trigger\">
\t\t\t\t\t<span class=\"dp-caret dp-caret-down\"></span>
\t\t\t\t</a>
\t\t\t\t<div id=\"";
            // line 148
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_user_email_menu\" class=\"dp-menu\" style=\"display: none;\">
\t\t\t\t\t<section>
\t\t\t\t\t\t<div class=\"dp-menu-area\">
\t\t\t\t\t\t\t<label>";
            // line 151
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.change_user_ticket_email");
            echo "</label>
\t\t\t\t\t\t\t<ul>
\t\t\t\t\t\t\t\t";
            // line 153
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_ticket_, "person"), "emails"));
            foreach ($context['_seq'] as $context["_key"] => $context["email"]) {
                // line 154
                echo "\t\t\t\t\t\t\t\t\t<li data-email-id=\"";
                if (isset($context["email"])) { $_email_ = $context["email"]; } else { $_email_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_email_, "id"), "html", null, true);
                echo "\">";
                if (isset($context["email"])) { $_email_ = $context["email"]; } else { $_email_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_email_, "email"), "html", null, true);
                echo "</li>
\t\t\t\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['email'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 156
            echo "\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t</div>
\t\t\t\t\t</section>
\t\t\t\t</div>
\t\t\t";
        }
        // line 161
        echo "\t\t</div>

\t\t";
        // line 163
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ($this->getAttribute($this->getAttribute($_ticket_, "person"), "organization")) {
            // line 164
            echo "\t\t\t&nbsp;&nbsp;
\t\t\t<a id=\"";
            // line 165
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_org_link\" class=\"dp-btn as-popover\" data-route=\"page:";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_org_view", array("organization_id" => $this->getAttribute($this->getAttribute($this->getAttribute($_ticket_, "person"), "organization"), "id"))), "html", null, true);
            echo "\"><i class=\"icon-briefcase\"></i>
\t\t\t\t";
            // line 166
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "person"), "organization"), "html", null, true);
            echo "
\t\t\t\t";
            // line 167
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if ($this->getAttribute($this->getAttribute($_ticket_, "person"), "organization_position")) {
                echo "<span class=\"email\">(";
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "person"), "organization_position"), "html", null, true);
                echo ")</span>";
            }
            // line 168
            echo "\t\t\t</a>
\t\t";
        }
        // line 170
        echo "
\t\t";
        // line 171
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if (isset($context["user_parts"])) { $_user_parts_ = $context["user_parts"]; } else { $_user_parts_ = null; }
        if (($this->getAttribute($_ticket_perms_, "modify_cc") && (!twig_length_filter($this->env, $_user_parts_)))) {
            // line 172
            echo "\t\t\t<a class=\"cc-btn cc-btn-main\" id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_cc_list_btn\"><i class=\"icon-envelope\"></i> <em>";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.add_cc");
            echo "</em></a>
\t\t";
        }
        // line 174
        echo "
\t\t";
        // line 175
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if (isset($context["user_parts"])) { $_user_parts_ = $context["user_parts"]; } else { $_user_parts_ = null; }
        if (($this->getAttribute($_ticket_perms_, "modify_cc") || twig_length_filter($this->env, $_user_parts_))) {
            // line 176
            echo "\t\t<div
\t\t\tid=\"";
            // line 177
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_cc_list\"
\t\t\tclass=\"cc-row ";
            // line 178
            if (isset($context["user_parts"])) { $_user_parts_ = $context["user_parts"]; } else { $_user_parts_ = null; }
            if (twig_length_filter($this->env, $_user_parts_)) {
                echo "cc-open";
            }
            echo "\"
\t\t\tdata-element-handler=\"DeskPRO.Agent.ElementHandler.TicketCcManage\"
\t\t\tdata-add-url=\"";
            // line 180
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_ticket_addpart", array("ticket_id" => $this->getAttribute($_ticket_, "id"))), "html", null, true);
            echo "\"
\t\t\tdata-delete-url=\"";
            // line 181
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_ticket_delpart", array("ticket_id" => $this->getAttribute($_ticket_, "id"))), "html", null, true);
            echo "\"
\t\t\tdata-replybox-container=\"#";
            // line 182
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_replybox_wrap\"
\t\t\t";
            // line 183
            if (isset($context["user_parts"])) { $_user_parts_ = $context["user_parts"]; } else { $_user_parts_ = null; }
            if ((!twig_length_filter($this->env, $_user_parts_))) {
                echo "style=\"display: none;\"";
            }
            // line 184
            echo "\t\t>
\t\t\t<ul class=\"cc-row-list\" id=\"";
            // line 185
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_cc_row_list\">
\t\t\t\t";
            // line 186
            if (isset($context["user_parts"])) { $_user_parts_ = $context["user_parts"]; } else { $_user_parts_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_user_parts_);
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
            foreach ($context['_seq'] as $context["_key"] => $context["part"]) {
                // line 187
                echo "\t\t\t\t\t";
                if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
                if (($this->getAttribute($_loop_, "index0") < 2)) {
                    // line 188
                    echo "\t\t\t\t\t\t";
                    if (isset($context["part"])) { $_part_ = $context["part"]; } else { $_part_ = null; }
                    if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
                    $this->env->loadTemplate("AgentBundle:Ticket:view-user-cc-row.html.twig")->display(array_merge($context, array("person" => $this->getAttribute($_part_, "person"), "ticket_perms" => $_ticket_perms_, "preload" => true)));
                    // line 189
                    echo "\t\t\t\t\t";
                } else {
                    // line 190
                    echo "\t\t\t\t\t\t";
                    if (isset($context["part"])) { $_part_ = $context["part"]; } else { $_part_ = null; }
                    if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
                    $this->env->loadTemplate("AgentBundle:Ticket:view-user-cc-row.html.twig")->display(array_merge($context, array("person" => $this->getAttribute($_part_, "person"), "ticket_perms" => $_ticket_perms_)));
                    // line 191
                    echo "\t\t\t\t\t";
                }
                // line 192
                echo "\t\t\t\t";
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
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['part'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 193
            echo "\t\t\t</ul>
\t\t\t<div class=\"cc-btn-holder\">
\t\t\t\t";
            // line 195
            if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
            if (isset($context["user_parts"])) { $_user_parts_ = $context["user_parts"]; } else { $_user_parts_ = null; }
            if (($this->getAttribute($_ticket_perms_, "modify_cc") && twig_length_filter($this->env, $_user_parts_))) {
                // line 196
                echo "\t\t\t\t\t<a class=\"cc-btn cc-btn-main\" id=\"";
                if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
                echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
                echo "_cc_list_btn\"><i class=\"icon-envelope\"></i> <em>";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.add_cc");
                echo "</em></a>
\t\t\t\t";
            }
            // line 198
            echo "\t\t\t</div>

\t\t\t";
            // line 200
            if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
            if ($this->getAttribute($_ticket_perms_, "modify_cc")) {
                // line 201
                echo "\t\t\t\t<div class=\"cc-autocomplete addrow noedit-hide\" style=\"display: none; clear:left;\">
\t\t\t\t\t<input type=\"text\" class=\"user-part cc-people-search-trigger\" placeholder=\"";
                // line 202
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.choose_a_person");
                echo "\" />
\t\t\t\t\t<div class=\"person-search-box\" style=\"display: none\">
\t\t\t\t\t\t<section>
\t\t\t\t\t\t\t<ul class=\"results-list\">
\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t</section>
\t\t\t\t\t</div>
\t\t\t\t\t<span class=\"is-not-loading\">
\t\t\t\t\t\t<button class=\"clean-white small cc-saverow-trigger\">";
                // line 210
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.add");
                echo "</button>
\t\t\t\t\t</span>
\t\t\t\t\t<span class=\"is-loading\">";
                // line 212
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.saving");
                echo "</span>
\t\t\t\t</div>
\t\t\t";
            }
            // line 215
            echo "\t\t\t<div style=\"clear:left;\"></div>
\t\t\t<script type=\"text/x-deskpro-tmpl\" class=\"user-row-tpl\">
\t\t\t\t<li>
\t\t\t\t\t<a>
\t\t\t\t\t<span class=\"user-name\"></span>
\t\t\t\t\t<address>&lt;<span class=\"user-email\"></span>&gt;</address>
\t\t\t\t\t<div class=\"bound-fade\"></div>
\t\t\t\t</a></li>
\t\t\t</script>
\t\t</div>
\t\t";
        }
        // line 226
        echo "\t</div>
\t<div class=\"margin\"></div>
</header>

<nav class=\"header-nav\">
\t<ul>
\t\t<li title=\"";
        // line 232
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.department");
        echo "\">
\t\t\t";
        // line 233
        if (isset($context["blank_option"])) { $_blank_option_ = $context["blank_option"]; } else { $_blank_option_ = null; }
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $this->env->loadTemplate("AgentBundle:Common:select-department.html.twig")->display(array_merge($context, array("name" => "department_id", "with_blank" => $_blank_option_, "blank_title" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"), "selected" => (($this->getAttribute($this->getAttribute($_ticket_, "department", array(), "any", false, true), "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "department", array(), "any", false, true), "id"), 0)) : (0)), "id" => ($_baseId_ . "_department_id"), "add_attr" => "data-invisible-trigger=\"1\" style=\"visibility: hidden\" data-dropdown-css-class=\"ticket-header\"", "departments" => $this->getAttribute($this->getAttribute($_app_, "departments"), "getPersonDepartments", array(0 => $this->getAttribute($_app_, "user"), 1 => "tickets", 2 => array(0 => (($this->getAttribute($this->getAttribute($_ticket_, "department", array(), "any", false, true), "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "department", array(), "any", false, true), "id"), 0)) : (0))), 3 => "assign"), "method"))));
        // line 242
        echo "
\t\t\t<span id=\"";
        // line 243
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_department_txt\">";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_ticket_, "department", array(), "any", false, true), "full_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "department", array(), "any", false, true), "full_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
        echo "</span>

\t\t\t<span class=\"dp-caret\"></span>
\t\t</li>
\t\t<li title=\"";
        // line 247
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.status");
        echo "\">
\t\t\t<select name=\"status_code\" class=\"status_code\" id=\"";
        // line 248
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_status_code\" data-invisible-trigger=\"1\" style=\"display: none\" data-dropdown-css-class=\"ticket-header\" data-dropdown-nosearch=\"1\">
\t\t\t\t";
        // line 249
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (($this->getAttribute($_ticket_, "status") == "hidden")) {
            // line 250
            echo "\t\t\t\t\t<option value=\"\" selected=\"selected\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.hidden");
            echo "</option>
\t\t\t\t";
        }
        // line 252
        echo "\t\t\t\t";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if ((($this->getAttribute($_ticket_, "status_code") == "awaiting_user") || $this->getAttribute($_ticket_perms_, "modify_set_awaiting_agent"))) {
            // line 253
            echo "\t\t\t\t\t<option value=\"awaiting_agent\" ";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if (($this->getAttribute($_ticket_, "status_code") == "awaiting_agent")) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_awaiting_agent");
            echo "</option>
\t\t\t\t";
        }
        // line 255
        echo "\t\t\t\t";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if ((($this->getAttribute($_ticket_, "status_code") == "awaiting_user") || $this->getAttribute($_ticket_perms_, "modify_set_awaiting_user"))) {
            // line 256
            echo "\t\t\t\t\t<option value=\"awaiting_user\" ";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if (($this->getAttribute($_ticket_, "status_code") == "awaiting_user")) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_awaiting_user");
            echo "</option>
\t\t\t\t";
        }
        // line 258
        echo "\t\t\t\t";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if ((($this->getAttribute($_ticket_, "status_code") == "resolved") || $this->getAttribute($_ticket_perms_, "modify_set_resolved"))) {
            // line 259
            echo "\t\t\t\t\t<option value=\"resolved\" ";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if (($this->getAttribute($_ticket_, "status_code") == "resolved")) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_resolved");
            echo "</option>
\t\t\t\t";
        }
        // line 261
        echo "\t\t\t\t";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (($this->getAttribute($_ticket_, "status_code") == "closed")) {
            // line 262
            echo "\t\t\t\t\t<option value=\"closed\" ";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if (($this->getAttribute($_ticket_, "status_code") == "closed")) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_closed");
            echo "</option>
\t\t\t\t";
        }
        // line 264
        echo "\t\t\t</select>

\t\t\t<span id=\"";
        // line 266
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_status_txt\" class=\"urgency-text\">
\t\t\t\t";
        // line 267
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, ("agent.tickets.status_" . strtr($this->getAttribute($_ticket_, "status_code"), array("." => "_"))));
        echo "
\t\t\t</span>

\t\t\t<span class=\"dp-caret\"></span>
\t\t</li>
\t\t<li title=\"";
        // line 272
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.urgency");
        echo "\" class=\"urgency-item\">
\t\t\t<select name=\"urgency\" class=\"urgency\" data-style-type=\"urgency\" id=\"";
        // line 273
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_urgency\" style=\"display: none\" data-invisible-trigger=\"1\" data-dropdown-css-class=\"ticket-header\" data-dropdown-nosearch=\"1\">
\t\t\t\t";
        // line 274
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable(range(1, 10));
        foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
            // line 275
            echo "\t\t\t\t\t<option value=\"";
            if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
            echo twig_escape_filter($this->env, $_i_, "html", null, true);
            echo "\" ";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
            if (($this->getAttribute($_ticket_, "urgency") == $_i_)) {
                echo "selected=\"selected\"";
            }
            echo ">";
            if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
            echo twig_escape_filter($this->env, $_i_, "html", null, true);
            echo "</option>
\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 277
        echo "\t\t\t</select>

\t\t\t<span id=\"";
        // line 279
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_urgency_txt\" class=\"urgency-value-";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "urgency"), "html", null, true);
        echo "\">";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "urgency"), "html", null, true);
        echo "</span>

\t\t\t<span class=\"dp-caret\"></span>
\t\t</li>
\t\t<li>
\t\t\t<input type=\"hidden\" id=\"";
        // line 284
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_flag_old\" value=\"";
        if (isset($context["ticket_flagged"])) { $_ticket_flagged_ = $context["ticket_flagged"]; } else { $_ticket_flagged_ = null; }
        echo twig_escape_filter($this->env, $_ticket_flagged_, "html", null, true);
        echo "\" />
\t\t\t<select name=\"flag\" class=\"flag\" id=\"";
        // line 285
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_flag\" class=\"dpe_select\" data-style-type=\"icons\" data-invisible-trigger=\"1\" data-dropdown-css-class=\"ticket-header\" data-dropdown-nosearch=\"1\">
\t\t\t\t<option
\t\t\t\t\tvalue=\"\"
\t\t\t\t\tdata-icon=\"";
        // line 288
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("images/agent/icons/flag-gray.png"), "html", null, true);
        echo "\"
\t\t\t\t>";
        // line 289
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none");
        echo "</option>
\t\t\t\t";
        // line 290
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable(array(0 => "blue", 1 => "green", 2 => "orange", 3 => "pink", 4 => "purple", 5 => "red", 6 => "yellow"));
        foreach ($context['_seq'] as $context["_key"] => $context["color"]) {
            // line 291
            echo "\t\t\t\t\t<option
\t\t\t\t\t\tvalue=\"";
            // line 292
            if (isset($context["color"])) { $_color_ = $context["color"]; } else { $_color_ = null; }
            echo twig_escape_filter($this->env, $_color_, "html", null, true);
            echo "\"
\t\t\t\t\t\t";
            // line 293
            if (isset($context["ticket_flagged"])) { $_ticket_flagged_ = $context["ticket_flagged"]; } else { $_ticket_flagged_ = null; }
            if (isset($context["color"])) { $_color_ = $context["color"]; } else { $_color_ = null; }
            if (($_ticket_flagged_ == $_color_)) {
                echo "selected=\"selected\"";
            }
            // line 294
            echo "\t\t\t\t\t\tdata-icon=\"";
            if (isset($context["color"])) { $_color_ = $context["color"]; } else { $_color_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl((("images/agent/icons/flag-" . $_color_) . ".png")), "html", null, true);
            echo "\"
\t\t\t\t\t>";
            // line 295
            if (isset($context["flag"])) { $_flag_ = $context["flag"]; } else { $_flag_ = null; }
            if (isset($context["color"])) { $_color_ = $context["color"]; } else { $_color_ = null; }
            echo $_flag_->getflag_name($_color_);
            echo "</option>
\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['color'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 297
        echo "\t\t\t</select>

\t\t\t<i id=\"";
        // line 299
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_flagicon\" class=\"icon-flag flag-color-";
        if (isset($context["ticket_flagged"])) { $_ticket_flagged_ = $context["ticket_flagged"]; } else { $_ticket_flagged_ = null; }
        echo twig_escape_filter($this->env, ((array_key_exists("ticket_flagged", $context)) ? (_twig_default_filter($_ticket_flagged_, "none")) : ("none")), "html", null, true);
        echo "\" style=\"margin:0; padding: 0;\"></i>
\t\t\t<span id=\"";
        // line 300
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_flagtext\">";
        if (isset($context["ticket_flagged"])) { $_ticket_flagged_ = $context["ticket_flagged"]; } else { $_ticket_flagged_ = null; }
        if ($_ticket_flagged_) {
            if (isset($context["flag"])) { $_flag_ = $context["flag"]; } else { $_flag_ = null; }
            if (isset($context["ticket_flagged"])) { $_ticket_flagged_ = $context["ticket_flagged"]; } else { $_ticket_flagged_ = null; }
            echo $_flag_->getflag_name($_ticket_flagged_);
        }
        echo "</span>

\t\t\t<span class=\"dp-caret\"></span>
\t\t</li>
\t</ul>
</nav>

";
        // line 307
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["ticket_api"])) { $_ticket_api_ = $context["ticket_api"]; } else { $_ticket_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "ticket", "header", "below", $_ticket_api_);
        echo "
";
        // line 308
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["ticket_api"])) { $_ticket_api_ = $context["ticket_api"]; } else { $_ticket_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "ticket", "properties", "above", $_ticket_api_);
        echo "

<div class=\"header-profile-box\">
<div class=\"profile-box-container\">
\t<header id=\"";
        // line 312
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_properties_header\">
\t\t";
        // line 313
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if ($this->getAttribute($_ticket_perms_, "modify_fields")) {
            // line 314
            echo "\t\t\t<div class=\"controls ";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($_ticket_, "isLocked", array(0 => $this->getAttribute($_app_, "user")), "method")) {
                echo "hide-locked";
            }
            echo "\" id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_field_edit_controls\">
\t\t\t\t<div class=\"is-not-loading\">
\t\t\t\t\t<span class=\"edit-gear\" id=\"";
            // line 316
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_field_edit_start\"></span>
\t\t\t\t\t<button class=\"cancel\" id=\"";
            // line 317
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_field_edit_cancel\" style=\"display: none\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.cancel");
            echo "</button>
\t\t\t\t\t<button class=\"save\" id=\"";
            // line 318
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_field_edit_save\" style=\"display: none\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.save");
            echo "</button>
\t\t\t\t</div>
\t\t\t\t<span class=\"is-loading\">";
            // line 320
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.saving");
            echo "</span>
\t\t\t</div>
\t\t";
        }
        // line 323
        echo "\t\t";
        ob_start();
        // line 324
        echo "\t\t\t";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_ticket_, "ticket_slas"));
        foreach ($context['_seq'] as $context["_key"] => $context["ticket_sla"]) {
            echo "<span class=\"sla-pip ";
            if (isset($context["ticket_sla"])) { $_ticket_sla_ = $context["ticket_sla"]; } else { $_ticket_sla_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_ticket_sla_, "sla_status"), "html", null, true);
            echo "\" data-sla-id=\"";
            if (isset($context["ticket_sla"])) { $_ticket_sla_ = $context["ticket_sla"]; } else { $_ticket_sla_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_ticket_sla_, "sla_id"), "html", null, true);
            echo "\"></span>";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['ticket_sla'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 325
        echo "\t\t";
        $context["sla_pips"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 326
        echo "\t\t";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["linked_tickets"])) { $_linked_tickets_ = $context["linked_tickets"]; } else { $_linked_tickets_ = null; }
        if (isset($context["tasks"])) { $_tasks_ = $context["tasks"]; } else { $_tasks_ = null; }
        if (isset($context["addable_slas"])) { $_addable_slas_ = $context["addable_slas"]; } else { $_addable_slas_ = null; }
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (isset($context["sla_pips"])) { $_sla_pips_ = $context["sla_pips"]; } else { $_sla_pips_ = null; }
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgetTabsHeader($_baseId_, "ticket", "properties", array(($_baseId_ . "_fields_display_main_wrap") => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.properties"), ($_baseId_ . "_linked_wrap") => (((((((("<i class=\"icon-link\"></i> " . $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.linked_tickets")) . " (<span id=\"") . $_baseId_) . "_linked_count\" data-count=\"") . $this->getAttribute($_linked_tickets_, "count")) . "\">") . $this->getAttribute($_linked_tickets_, "count")) . "</span>)"), ($_baseId_ . "_tasks_wrap") => ((((($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.tasks") . " (<span id=\"") . $_baseId_) . "_task_count\">") . twig_length_filter($this->env, $_tasks_)) . "</span>)"), ($_baseId_ . "_sla_wrap") => (((((!twig_test_empty($_addable_slas_)) && $this->getAttribute($_ticket_perms_, "modify_slas")) || (!twig_test_empty($this->getAttribute($_ticket_, "ticket_slas"))))) ? ((($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.slas") . " ") . $_sla_pips_)) : (false)), ($_baseId_ . "_billing_wrap") => (($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.enable_billing"), "method")) ? ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.billing")) : (false))));
        // line 332
        echo "
\t</header>
\t<section class=\"description-area\" style=\"padding:0\">
\t\t<div class=\"error-list ";
        // line 335
        if (isset($context["validator_errors"])) { $_validator_errors_ = $context["validator_errors"]; } else { $_validator_errors_ = null; }
        if ($_validator_errors_) {
            echo "on";
        }
        echo "\" id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_field_errors\" ";
        if (isset($context["validator_errors"])) { $_validator_errors_ = $context["validator_errors"]; } else { $_validator_errors_ = null; }
        if ((!$_validator_errors_)) {
            echo "style=\"display:none\"";
        }
        echo ">
\t\t\t<span class=\"title\">";
        // line 336
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.save_errors");
        echo ":</span>
\t\t\t<ul>
\t\t\t\t";
        // line 338
        if (isset($context["validator_errors"])) { $_validator_errors_ = $context["validator_errors"]; } else { $_validator_errors_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_validator_errors_);
        foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
            // line 339
            echo "\t\t\t\t\t<li>";
            if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
            echo twig_escape_filter($this->env, $_i_, "html", null, true);
            echo "</li>
\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 341
        echo "\t\t\t</ul>
\t\t</div>
\t\t<article id=\"";
        // line 343
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_fields_display_main_wrap\" class=\"headerbox\" style=\"position: relative;\">
\t\t\t";
        // line 344
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_ticket_, "isLocked", array(0 => $this->getAttribute($_app_, "user")), "method")) {
            echo "<div class=\"lock-overlay\"></div>";
        }
        // line 345
        echo "\t\t\t<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\"><tr><td style=\"padding-right: 5px;\">
\t\t\t\t\t<label class=\"field-label-title\">";
        // line 346
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.agent");
        echo "</label>
\t\t\t\t\t<select id=\"";
        // line 347
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_agent_sel\" class=\"dpe_select\" data-style-type=\"icons\" data-select-icon-size=\"22\">
\t\t\t\t\t\t";
        // line 348
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (($this->getAttribute($_ticket_perms_, "modify_assign_agent") || (!$this->getAttribute($_ticket_, "agent")))) {
            echo "<option ";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if ((!$this->getAttribute($this->getAttribute($_ticket_, "agent"), "id"))) {
                echo "selected=\"selected\"";
            }
            echo " value=\"0\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.unassigned");
            echo "</option>";
        }
        // line 349
        echo "\t\t\t\t\t\t";
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($this->getAttribute($_ticket_perms_, "modify_assign_self") || ($this->getAttribute($this->getAttribute($_ticket_, "agent"), "id") == $this->getAttribute($this->getAttribute($_app_, "user"), "id")))) {
            // line 350
            echo "\t\t\t\t\t\t\t<option ";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if (($this->getAttribute($this->getAttribute($_app_, "user"), "id") == $this->getAttribute($this->getAttribute($_ticket_, "agent"), "id"))) {
                echo "selected=\"selected\"";
            }
            echo " data-icon=\"";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "user"), "getPictureUrl", array(0 => 20), "method"), "html", null, true);
            echo "\" value=\"";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "user"), "id"), "html", null, true);
            echo "\">";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "user"), "display_name"), "html", null, true);
            echo "</option>
\t\t\t\t\t\t";
        }
        // line 352
        echo "\t\t\t\t\t\t";
        if (isset($context["agents"])) { $_agents_ = $context["agents"]; } else { $_agents_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_agents_);
        foreach ($context['_seq'] as $context["_key"] => $context["agent"]) {
            // line 353
            echo "\t\t\t\t\t\t\t";
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if ((($this->getAttribute($_agent_, "id") != $this->getAttribute($this->getAttribute($_app_, "user"), "id")) && ($this->getAttribute($_ticket_perms_, "modify_assign_agent") || ($this->getAttribute($this->getAttribute($_ticket_, "agent"), "id") == $this->getAttribute($_agent_, "id"))))) {
                // line 354
                echo "\t\t\t\t\t\t\t\t<option ";
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                if (($this->getAttribute($_agent_, "id") == $this->getAttribute($this->getAttribute($_ticket_, "agent"), "id"))) {
                    echo "selected=\"selected\"";
                }
                echo " data-icon=\"";
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "getPictureUrl", array(0 => 20), "method"), "html", null, true);
                echo "\" value=\"";
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "id"), "html", null, true);
                echo "\">";
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "display_name"), "html", null, true);
                echo "</option>
\t\t\t\t\t\t\t";
            }
            // line 356
            echo "\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['agent'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 357
        echo "\t\t\t\t\t</select>
\t\t\t</td><td style=\"padding-right: 5px;\">
\t\t\t\t<label class=\"field-label-title\">";
        // line 359
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.team");
        echo "</label>
\t\t\t\t<select id=\"";
        // line 360
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_agent_team_sel\" class=\"dpe_select\" data-select-nogrouptitle=\"1\">
\t\t\t\t\t";
        // line 361
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (($this->getAttribute($_ticket_perms_, "modify_assign_team") || (!$this->getAttribute($_ticket_, "agent_team")))) {
            // line 362
            echo "\t\t\t\t\t\t<option value=\"0\" ";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if ((!$this->getAttribute($_ticket_, "agent_team"))) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none");
            echo "</option>
\t\t\t\t\t";
        }
        // line 364
        echo "
\t\t\t\t\t";
        // line 365
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
        if ((twig_length_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_app_, "user"), "getAgent", array(), "method"), "getTeams", array(), "method")) && ($this->getAttribute($_ticket_perms_, "modify_assign_self") || ($this->getAttribute($this->getAttribute($_ticket_, "agent_team"), "id") == $this->getAttribute($_team_, "id"))))) {
            // line 366
            echo "\t\t\t\t\t<optgroup label=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.your_teams");
            echo "\">
\t\t\t\t\t\t";
            // line 367
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($this->getAttribute($_app_, "user"), "getAgent", array(), "method"), "getTeams", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["team"]) {
                // line 368
                echo "\t\t\t\t\t\t\t<option value=\"";
                if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_team_, "id"), "html", null, true);
                echo "\" ";
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                if (($this->getAttribute($this->getAttribute($_ticket_, "agent_team"), "id") == $this->getAttribute($_team_, "id"))) {
                    echo "selected=\"selected\"";
                }
                echo ">";
                if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_team_, "name"), "html", null, true);
                echo "</option>
\t\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['team'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 370
            echo "\t\t\t\t\t</optgroup>
\t\t\t\t\t";
        }
        // line 372
        echo "
\t\t\t\t\t";
        // line 373
        if (isset($context["agent_teams"])) { $_agent_teams_ = $context["agent_teams"]; } else { $_agent_teams_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_agent_teams_);
        foreach ($context['_seq'] as $context["_key"] => $context["team"]) {
            // line 374
            echo "\t\t\t\t\t\t";
            if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ((($this->getAttribute($_ticket_perms_, "modify_assign_team") || ($this->getAttribute($this->getAttribute($_ticket_, "agent_team"), "id") == $this->getAttribute($_team_, "id"))) && !twig_in_filter($this->getAttribute($_team_, "id"), $this->getAttribute($this->getAttribute($this->getAttribute($_app_, "user"), "getAgent", array(), "method"), "getTeamIds", array(), "method")))) {
                // line 375
                echo "\t\t\t\t\t\t\t<option value=\"";
                if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_team_, "id"), "html", null, true);
                echo "\" ";
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                if (($this->getAttribute($this->getAttribute($_ticket_, "agent_team"), "id") == $this->getAttribute($_team_, "id"))) {
                    echo "selected=\"selected\"";
                }
                echo ">";
                if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_team_, "name"), "html", null, true);
                echo "</option>
\t\t\t\t\t\t";
            }
            // line 377
            echo "\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['team'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 378
        echo "\t\t\t\t</select>
\t\t\t</td><td width=\"100%\">
\t\t\t\t<label class=\"field-label-title\">";
        // line 380
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.followers");
        echo "</label>
\t\t\t\t<div class=\"followers-list\">
\t\t\t\t\t<ul id=\"";
        // line 382
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_followers_list\">
\t\t\t\t\t\t";
        // line 383
        if (isset($context["agent_parts"])) { $_agent_parts_ = $context["agent_parts"]; } else { $_agent_parts_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_agent_parts_);
        foreach ($context['_seq'] as $context["_key"] => $context["a"]) {
            // line 384
            echo "\t\t\t\t\t\t\t<li class=\"agent-";
            if (isset($context["a"])) { $_a_ = $context["a"]; } else { $_a_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_a_, "person"), "id"), "html", null, true);
            echo "\" data-agent-id=\"";
            if (isset($context["a"])) { $_a_ = $context["a"]; } else { $_a_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_a_, "person"), "id"), "html", null, true);
            echo "\"><a class=\"dp-btn dp-btn-small agent-link\" data-agent-id=\"";
            if (isset($context["a"])) { $_a_ = $context["a"]; } else { $_a_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_a_, "person"), "id"), "html", null, true);
            echo "\"><span class=\"text\" style=\"background-image: url(";
            if (isset($context["a"])) { $_a_ = $context["a"]; } else { $_a_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_a_, "person"), "getPictureUrl", array(0 => 15), "method"), "html", null, true);
            echo ")\">";
            if (isset($context["a"])) { $_a_ = $context["a"]; } else { $_a_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_a_, "person"), "display_name"), "html", null, true);
            echo "</span><span class=\"remove-row-trigger\"> <i class=\"icon-remove\"></i></span></a></li>
\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['a'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 386
        echo "\t\t\t\t\t</ul>
\t\t\t\t\t<div class=\"new-follower\" id=\"";
        // line 387
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_followers_sel_wrap\">
\t\t\t\t\t\t<select id=\"";
        // line 388
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_followers_sel\" class=\"dpe_select\" data-style-type=\"icons\" data-select-icon-size=\"22\">
\t\t\t\t\t\t\t<option value=\"0\">";
        // line 389
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.select_agent");
        echo "</option>
\t\t\t\t\t\t\t";
        // line 390
        if (isset($context["agents"])) { $_agents_ = $context["agents"]; } else { $_agents_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_agents_);
        foreach ($context['_seq'] as $context["_key"] => $context["agent"]) {
            // line 391
            echo "\t\t\t\t\t\t\t\t<option data-icon=\"";
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "getPictureUrl", array(0 => 20), "method"), "html", null, true);
            echo "\" value=\"";
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "id"), "html", null, true);
            echo "\" data-icon-small=\"";
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "getPictureUrl", array(0 => 15), "method"), "html", null, true);
            echo "\">";
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "display_name"), "html", null, true);
            echo "</option>
\t\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['agent'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 393
        echo "\t\t\t\t\t\t</select>
\t\t\t\t\t</div>
\t\t\t\t\t<a class=\"new-follower-btn\" id=\"";
        // line 395
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_add_follower_btn\">
\t\t\t\t\t\t<i class=\"icon-signin\"></i>
\t\t\t\t\t\t";
        // line 397
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.add_follower");
        echo "
\t\t\t\t\t</a>
\t\t\t\t</div>
\t\t\t</td></tr></table>
\t\t\t<div class=\"sep\">-</div>
\t\t\t<div id=\"";
        // line 402
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_field_holders\">
\t\t\t\t<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" class=\"field-holders-table\">
\t\t\t\t\t";
        // line 404
        $this->env->loadTemplate("AgentBundle:Ticket:view-page-display-holders.html.twig")->display($context);
        // line 405
        echo "\t\t\t\t\t";
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (($this->getAttribute($_ticket_perms_, "modify_labels") || twig_length_filter($this->env, $this->getAttribute($_ticket_, "labels")))) {
            // line 406
            echo "\t\t\t\t\t\t<tbody class=\"always-bottom\">
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<th width=\"80\" style=\"vertical-align: middle; border-bottom: none; padding-top: 0; padding-bottom: 0;\">";
            // line 408
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.labels");
            echo ":</th>
\t\t\t\t\t\t\t\t<td style=\"vertical-align: middle; border-bottom: none; padding-top: 0; padding-bottom: 0; padding-right: 0;\">
\t\t\t\t\t\t\t\t\t<div style=\"width: 98%\" id=\"";
            // line 410
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_labels_wrap\">
\t\t\t\t\t\t\t\t\t\t<input
\t\t\t\t\t\t\t\t\t\t\ttype=\"hidden\"
\t\t\t\t\t\t\t\t\t\t\tid=\"";
            // line 413
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_labels_input\"
\t\t\t\t\t\t\t\t\t\t\tclass=\"dpe_select dpe_select_noborder\"
\t\t\t\t\t\t\t\t\t\t\tdata-placeholder=\"Add a label\"
\t\t\t\t\t\t\t\t\t\t\tvalue=\"";
            // line 416
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_ticket_, "labels"));
            foreach ($context['_seq'] as $context["_key"] => $context["label"]) {
                if (isset($context["label"])) { $_label_ = $context["label"]; } else { $_label_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_label_, "label"), "html", null, true);
                echo ",";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['label'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            echo "\"
\t\t\t\t\t\t\t\t\t\t/>
\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t</tbody>
\t\t\t\t\t";
        }
        // line 423
        echo "\t\t\t\t\t";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((($this->getAttribute($_ticket_, "linked_chat") && $this->getAttribute($_app_, "getSetting", array(0 => "core.apps_chat"), "method")) && $this->getAttribute($this->getAttribute($_app_, "user"), "hasPerm", array(0 => "agent_chat.use"), "method"))) {
            // line 424
            echo "\t\t\t\t\t\t<tbody>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<th>
\t\t\t\t\t\t\t\t\t";
            // line 427
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.linked_chat");
            echo "
\t\t\t\t\t\t\t\t</th>
\t\t\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t\t\t<a data-route=\"page:";
            // line 430
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_userchat_view", array("conversation_id" => $this->getAttribute($this->getAttribute($_ticket_, "linked_chat"), "id"))), "html", null, true);
            echo "\">";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "linked_chat"), "getSubjectLine", array(), "method"), "html", null, true);
            echo "</a>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t</tbody>
\t\t\t\t\t";
        }
        // line 435
        echo "\t\t\t\t</table>
\t\t\t</div>
\t\t</article>
\t\t<article id=\"";
        // line 438
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_linked_wrap\">
\t\t\t";
        // line 439
        $this->env->loadTemplate("AgentBundle:Ticket:linked-tickets.html.twig")->display($context);
        // line 440
        echo "\t\t</article>
\t\t<article id=\"";
        // line 441
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_tasks_wrap\">
\t\t\t";
        // line 442
        $this->env->loadTemplate("AgentBundle:Ticket:view-tasks.html.twig")->display($context);
        // line 443
        echo "\t\t</article>
\t\t";
        // line 444
        if (isset($context["addable_slas"])) { $_addable_slas_ = $context["addable_slas"]; } else { $_addable_slas_ = null; }
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ((((!twig_test_empty($_addable_slas_)) && $this->getAttribute($_ticket_perms_, "modify_slas")) || (!twig_test_empty($this->getAttribute($_ticket_, "ticket_slas"))))) {
            // line 445
            echo "\t\t\t<article id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_sla_wrap\">
\t\t\t\t";
            // line 446
            $this->env->loadTemplate("AgentBundle:Ticket:view-slas.html.twig")->display($context);
            // line 447
            echo "\t\t\t</article>
\t\t";
        }
        // line 449
        echo "\t\t";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.enable_billing"), "method")) {
            // line 450
            echo "\t\t\t<article id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_billing_wrap\">
\t\t\t\t";
            // line 451
            $this->env->loadTemplate("AgentBundle:Ticket:view-billing.html.twig")->display($context);
            // line 452
            echo "\t\t\t</article>
\t\t";
        }
        // line 454
        echo "\t\t";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["ticket_api"])) { $_ticket_api_ = $context["ticket_api"]; } else { $_ticket_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgetTabsBody($_baseId_, "ticket", "properties", "article", $_ticket_api_);
        echo "
\t</section>
</div>
</div>

<nav class=\"header-nav\">
\t<ul>
\t\t<li id=\"";
        // line 461
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_unlock_ticket\" ";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ((!$this->getAttribute($_ticket_, "hasLock", array(), "method"))) {
            echo "style=\"display: none\"";
        }
        echo ">
\t\t\t<i class=\"icon-unlock\"></i>
\t\t\t";
        // line 463
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.unlock");
        echo "
\t\t</li>
\t\t<li id=\"";
        // line 465
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_lock_ticket\" ";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ($this->getAttribute($_ticket_, "hasLock", array(), "method")) {
            echo "style=\"display: none\"";
        }
        echo ">
\t\t\t<i class=\"icon-lock\"></i>
\t\t\t";
        // line 467
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.lock");
        echo "
\t\t</li>
\t\t";
        // line 469
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if ($this->getAttribute($_ticket_perms_, "modify_merge")) {
            // line 470
            echo "\t\t\t<li class=\"merge-menu-trigger noedit-hide\">
\t\t\t\t<i class=\"icon-columns\"></i>
\t\t\t\t";
            // line 472
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.merge");
            echo "
\t\t\t\t<span class=\"dp-caret\"></span>
\t\t\t</li>
\t\t";
        }
        // line 476
        echo "\t\t<li id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_macros_menu_trigger\">
\t\t\t<i class=\"icon-tasks\"></i>
\t\t\t";
        // line 478
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.macros");
        echo "
\t\t\t<span class=\"dp-caret\"></span>
\t\t</li>
\t\t";
        // line 481
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if ($this->getAttribute($_ticket_perms_, "delete")) {
            // line 482
            echo "\t\t\t";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if ((!twig_in_filter($this->getAttribute($_ticket_, "hidden_status"), array(0 => "deleted", 1 => "spam")))) {
                // line 483
                echo "\t\t\t\t<li id=\"";
                if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
                echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
                echo "_remove_menu_trigger\">
\t\t\t\t\t<i class=\"icon-trash\"></i>
\t\t\t\t\t";
                // line 485
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.remove");
                echo "
\t\t\t\t\t<span class=\"dp-caret\"></span>
\t\t\t\t</li>
\t\t\t";
            }
            // line 489
            echo "\t\t";
        }
        // line 490
        echo "\t\t<li id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_actions_menu_trigger\">
\t\t\t<i class=\"icon-edit\"></i>
\t\t\t";
        // line 492
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.actions");
        echo "
\t\t\t<span class=\"dp-caret\"></span>
\t\t</li>
\t</ul>
</nav>

<ul style=\"display:none\" id=\"";
        // line 498
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_remove_menu\">
\t<li data-action=\"spam\">";
        // line 499
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.set_as_spam");
        echo "</li>
\t<li data-action=\"delete\">";
        // line 500
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.delete_ticket");
        echo "</li>
\t";
        // line 501
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "user"), "hasPerm", array(0 => "agent_people.delete"), "method")) {
            // line 502
            echo "\t\t";
            if (isset($context["person_object_counts"])) { $_person_object_counts_ = $context["person_object_counts"]; } else { $_person_object_counts_ = null; }
            if ((($this->getAttribute($_person_object_counts_, "tickets") > 1) || ($this->getAttribute($_person_object_counts_, "chats") > 0))) {
                // line 503
                echo "\t\t\t";
                if (isset($context["person_object_counts"])) { $_person_object_counts_ = $context["person_object_counts"]; } else { $_person_object_counts_ = null; }
                if (($this->getAttribute($_person_object_counts_, "tickets") > 1)) {
                    // line 504
                    echo "\t\t\t\t<li data-action=\"delete.ban\">";
                    if (isset($context["person_object_counts"])) { $_person_object_counts_ = $context["person_object_counts"]; } else { $_person_object_counts_ = null; }
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.perm_del_user_and_other_tickets", array("ticket_count" => $this->getAttribute($_person_object_counts_, "tickets")));
                    echo "</li>
\t\t\t";
                } else {
                    // line 506
                    echo "\t\t\t\t<li data-action=\"delete.ban\">";
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.perm_del_user_and_content");
                    echo "</li>
\t\t\t";
                }
                // line 508
                echo "\t\t";
            } else {
                // line 509
                echo "\t\t\t<li data-action=\"delete.ban\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.perm_del_user_and_ticket");
                echo "</li>
\t\t";
            }
            // line 511
            echo "\t";
        }
        // line 512
        echo "</ul>

<ul style=\"display:none\" id=\"";
        // line 514
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_actions_menu\">
\t<li data-action=\"change-user\">";
        // line 515
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.change_user_btn");
        echo "</li>
\t<li style=\"";
        // line 516
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ($this->getAttribute($_ticket_, "is_hold")) {
            echo "display: none;";
        }
        echo "\" id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_menu_set_hold\" data-action=\"set-hold\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.hold_btn");
        echo "</li>
\t<li style=\"";
        // line 517
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ((!$this->getAttribute($_ticket_, "is_hold"))) {
            echo "display: none;";
        }
        echo "\" id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_menu_unset_hold\" data-action=\"unset-hold\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.unhold_btn");
        echo "</li>
\t";
        // line 518
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if ($this->getAttribute($_ticket_perms_, "modify_merge")) {
            echo "<li data-action=\"split\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.split_messages_into_ticket");
            echo "</li>";
        }
        // line 519
        echo "\t";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "user"), "hasPerm", array(0 => "agent_tickets.create"), "method")) {
            echo "<li data-action=\"linked_ticket\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.create_linked_ticket");
            echo "</li>";
        }
        // line 520
        echo "\t<li data-action=\"kb-pending\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.create_new_pending_article");
        echo "</li>
\t<li><a target=\"_blank\" href=\"";
        // line 521
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_ticket_view", array("ticket_id" => $this->getAttribute($_ticket_, "id"), "view_print" => 1, "_rt" => $this->env->getExtension('deskpro_templating')->securityToken("request_token", 10800))), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.print");
        echo "</a></li>
\t<li><a target=\"_blank\" href=\"";
        // line 522
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_ticket_view", array("ticket_id" => $this->getAttribute($_ticket_, "id"), "pdf" => 1, "_rt" => $this->env->getExtension('deskpro_templating')->securityToken("request_token", 10800))), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.download_pdf");
        echo "</a></li>
</ul>

<div class=\"pagecontent\">

";
        // line 527
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["ticket_api"])) { $_ticket_api_ = $context["ticket_api"]; } else { $_ticket_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "ticket", "properties", "below", $_ticket_api_);
        echo "
";
        // line 528
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["ticket_api"])) { $_ticket_api_ = $context["ticket_api"]; } else { $_ticket_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "ticket", "messages", "above", $_ticket_api_);
        echo "

";
        // line 530
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent.ticket_reverse_order"), "method")) {
            // line 531
            echo "\t";
            if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
            if (($this->getAttribute($_ticket_perms_, "reply") || $this->getAttribute($_ticket_perms_, "modify_notes"))) {
                // line 532
                echo "\t<div style=\"position:relative;\" class=\"ticket_reverse_order\">
\t\t";
                // line 533
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                if ($this->getAttribute($_ticket_, "isLocked", array(0 => $this->getAttribute($_app_, "user")), "method")) {
                    echo "<div class=\"lock-overlay\"></div>";
                }
                // line 534
                echo "\t\t<div id=\"";
                if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
                echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
                echo "_replybox_wrap\" class=\"replybox-wrap\">
\t\t\t";
                // line 535
                $this->env->loadTemplate("AgentBundle:Ticket:replybox.html.twig")->display($context);
                // line 536
                echo "\t\t</div>
\t</div>
\t";
            }
        }
        // line 540
        echo "
<div class=\"profile-box-container ticket-messages\">
\t<header>
\t\t";
        // line 543
        $context["messageTabs"] = $this->env->getExtension('deskpro_templating')->getWidgetsRaw("ticket", "messages", "tab");
        // line 544
        echo "\t\t<nav id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_messagebox_tabs\" data-element-handler=\"DeskPRO.ElementHandler.SimpleTabs\">
\t\t\t<ul>
\t\t\t\t<li data-list-type=\"messages\" class=\"on\" data-tab-for=\"#";
        // line 546
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_messages_wrap\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.messages");
        echo "</li>
\t\t\t\t<li class=\"logs\" data-list-type=\"log\" data-tab-for=\"#";
        // line 547
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_logs_wrap\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.full_log");
        echo "</li>
\t\t\t\t";
        // line 548
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ((($this->getAttribute($_app_, "getSetting", array(0 => "core.tickets.enable_feedback"), "method") && $this->getAttribute($_app_, "getSetting", array(0 => "core.tickets.feedback_agents_read"), "method")) && $this->getAttribute($_ticket_, "date_feedback_rating"))) {
            // line 549
            echo "\t\t\t\t\t<li data-list-type=\"feedback\" data-tab-for=\"#";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_messages_wrap\">
\t\t\t\t\t\t";
            // line 550
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.ticket_feedback");
            echo " <span class=\"feedback-rating feedback-";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "feedback_rating_type"), "html", null, true);
            echo "\">";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if (($this->getAttribute($_ticket_, "feedback_rating") == 1)) {
                echo "☺";
            } elseif (($this->getAttribute($_ticket_, "feedback_rating") == (-1))) {
                echo "☹";
            } else {
                echo "&mdash;";
            }
            echo "</span>
\t\t\t\t\t</li>
\t\t\t\t";
        }
        // line 553
        echo "\t\t\t\t<li data-tab-for=\"#";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_times\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.dates_and_times");
        echo "</li>
\t\t\t";
        // line 554
        if (isset($context["messageTabs"])) { $_messageTabs_ = $context["messageTabs"]; } else { $_messageTabs_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_messageTabs_);
        foreach ($context['_seq'] as $context["_key"] => $context["widget"]) {
            // line 555
            echo "\t\t\t\t<li data-tab-for=\"#";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            if (isset($context["widget"])) { $_widget_ = $context["widget"]; } else { $_widget_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getWidgetHtmlId($_baseId_, $_widget_), "html", null, true);
            echo "\">";
            if (isset($context["widget"])) { $_widget_ = $context["widget"]; } else { $_widget_ = null; }
            echo $this->getAttribute($_widget_, "title");
            echo "</li>
\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['widget'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 557
        echo "\t\t\t</ul>
\t\t</nav>
\t</header>
\t<section class=\"messages-wrap\" id=\"";
        // line 560
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_messages_wrap\" data-page=\"";
        if (isset($context["message_page"])) { $_message_page_ = $context["message_page"]; } else { $_message_page_ = null; }
        echo twig_escape_filter($this->env, $_message_page_, "html", null, true);
        echo "\" data-page-count=\"";
        if (isset($context["message_page_count"])) { $_message_page_count_ = $context["message_page_count"]; } else { $_message_page_count_ = null; }
        echo twig_escape_filter($this->env, $_message_page_count_, "html", null, true);
        echo "\">
\t\t<div class=\"message-page-prev\" ";
        // line 561
        if (isset($context["message_page"])) { $_message_page_ = $context["message_page"]; } else { $_message_page_ = null; }
        if (isset($context["message_page_count"])) { $_message_page_count_ = $context["message_page_count"]; } else { $_message_page_count_ = null; }
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((($_message_page_ == $_message_page_count_) || $this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent.ticket_reverse_order"), "method"))) {
            echo "style=\"display: none\"";
        }
        echo " id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_message_prev_page\">
\t\t\t<strong>";
        // line 562
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent.ticket_reverse_order"), "method"))) {
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.view_older_messages");
        } else {
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.view_newer_messages");
        }
        echo "</strong>
\t\t</div>
\t\t";
        // line 564
        if (isset($context["active_drafts"])) { $_active_drafts_ = $context["active_drafts"]; } else { $_active_drafts_ = null; }
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($_active_drafts_ && $this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent.ticket_reverse_order"), "method"))) {
            // line 565
            echo "\t\t\t";
            if (isset($context["active_drafts"])) { $_active_drafts_ = $context["active_drafts"]; } else { $_active_drafts_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_active_drafts_);
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
            foreach ($context['_seq'] as $context["_key"] => $context["active_draft"]) {
                // line 566
                echo "\t\t\t\t";
                if (isset($context["active_draft"])) { $_active_draft_ = $context["active_draft"]; } else { $_active_draft_ = null; }
                if ($this->getAttribute($_active_draft_, "message_html")) {
                    // line 567
                    echo "\t\t\t\t\t";
                    if (isset($context["active_draft"])) { $_active_draft_ = $context["active_draft"]; } else { $_active_draft_ = null; }
                    $this->env->loadTemplate("AgentBundle:Ticket:ticket-message-draft.html.twig")->display(array_merge($context, array("draft" => $_active_draft_)));
                    // line 568
                    echo "\t\t\t\t";
                }
                // line 569
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
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['active_draft'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 570
            echo "\t\t";
        }
        // line 571
        echo "\t\t<div id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_message_page_wrap\">
\t\t\t";
        // line 572
        if (isset($context["ticket_messages_block"])) { $_ticket_messages_block_ = $context["ticket_messages_block"]; } else { $_ticket_messages_block_ = null; }
        echo $_ticket_messages_block_;
        echo "
\t\t</div>
\t\t";
        // line 574
        if (isset($context["active_drafts"])) { $_active_drafts_ = $context["active_drafts"]; } else { $_active_drafts_ = null; }
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($_active_drafts_ && (!$this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent.ticket_reverse_order"), "method")))) {
            // line 575
            echo "\t\t\t";
            if (isset($context["active_drafts"])) { $_active_drafts_ = $context["active_drafts"]; } else { $_active_drafts_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_active_drafts_);
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
            foreach ($context['_seq'] as $context["_key"] => $context["active_draft"]) {
                // line 576
                echo "\t\t\t\t";
                if (isset($context["active_draft"])) { $_active_draft_ = $context["active_draft"]; } else { $_active_draft_ = null; }
                if ($this->getAttribute($_active_draft_, "message_html")) {
                    // line 577
                    echo "\t\t\t\t\t";
                    if (isset($context["active_draft"])) { $_active_draft_ = $context["active_draft"]; } else { $_active_draft_ = null; }
                    $this->env->loadTemplate("AgentBundle:Ticket:ticket-message-draft.html.twig")->display(array_merge($context, array("draft" => $_active_draft_)));
                    // line 578
                    echo "\t\t\t\t";
                }
                // line 579
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
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['active_draft'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 580
            echo "\t\t";
        }
        // line 581
        echo "\t\t<div class=\"message-page-next\" ";
        if (isset($context["message_page"])) { $_message_page_ = $context["message_page"]; } else { $_message_page_ = null; }
        if (isset($context["message_page_count"])) { $_message_page_count_ = $context["message_page_count"]; } else { $_message_page_count_ = null; }
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((($_message_page_ == $_message_page_count_) || (!$this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent.ticket_reverse_order"), "method")))) {
            echo "style=\"display: none\"";
        }
        echo " id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_message_next_page\">
\t\t\t<strong>";
        // line 582
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent.ticket_reverse_order"), "method")) {
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.view_older_messages");
        } else {
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.view_newer_messages");
        }
        echo "</strong>
\t\t</div>
\t</section>
\t<section id=\"";
        // line 585
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_logs_wrap\" style=\"margin: 0;padding: 0;\">
\t\t";
        // line 586
        if (isset($context["logs_block"])) { $_logs_block_ = $context["logs_block"]; } else { $_logs_block_ = null; }
        echo $_logs_block_;
        echo "
\t</section>
\t<section id=\"";
        // line 588
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_times\" style=\"display: none; padding: 0;\">
\t\t<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" class=\"field-holders-table th-ra\">
\t\t\t<tbody><tr>
\t\t\t\t<th width=\"200\">";
        // line 591
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.created");
        echo ":</th>
\t\t\t\t<td>
\t\t\t\t\t<time class=\"timeago\" datetime=\"";
        // line 593
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_created"), "c", "UTC"), "html", null, true);
        echo "\"></time>
\t\t\t\t\t(";
        // line 594
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_created"), "fulltime"), "html", null, true);
        echo ")
\t\t\t\t</td>
\t\t\t</tr></tbody>
\t\t\t";
        // line 597
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        $context["times_work_hours"] = ($this->getAttribute($this->getAttribute($_ticket_, "getWorkHoursSet", array(), "method"), "getActiveTime", array(), "method") != "all");
        // line 598
        echo "\t\t\t";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ((($this->getAttribute($_ticket_, "status") == "awaiting_agent") && $this->getAttribute($_ticket_, "date_user_waiting"))) {
            // line 599
            echo "\t\t\t<tbody><tr>
\t\t\t\t<th>";
            // line 600
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.current_user_waiting_time");
            echo ":</th>
\t\t\t\t<td>
\t\t\t\t\t";
            // line 602
            if (isset($context["times_work_hours"])) { $_times_work_hours_ = $context["times_work_hours"]; } else { $_times_work_hours_ = null; }
            if ($_times_work_hours_) {
                // line 603
                echo "\t\t\t\t\t\t";
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->timeLength($this->getAttribute($_ticket_, "current_user_waiting_work_time"), "hours"), "html", null, true);
                echo "
\t\t\t\t\t\t<span class=\"tipped small-light-icon\" title=\"Work hours only, real time: ";
                // line 604
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->timeLength($this->getAttribute($_ticket_, "current_user_waiting_time")), "html", null, true);
                echo "\"></span>
\t\t\t\t\t";
            } else {
                // line 606
                echo "\t\t\t\t\t\t<time class=\"timeago\" data-timeago-no-ago=\"1\" datetime=\"";
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_user_waiting"), "c", "UTC"), "html", null, true);
                echo "\"></time>
\t\t\t\t\t";
            }
            // line 608
            echo "\t\t\t\t\t(";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.since_time", array("time" => $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_user_waiting"), "fulltime")));
            echo ")
\t\t\t\t</td>
\t\t\t</tr></tbody>
\t\t\t";
        }
        // line 612
        echo "\t\t\t";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (($this->getAttribute($_ticket_, "real_total_user_waiting") && ($this->getAttribute($_ticket_, "real_total_user_waiting") > 0))) {
            // line 613
            echo "\t\t\t<tbody><tr>
\t\t\t\t<th>";
            // line 614
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.total_user_waiting_time");
            echo ":</th>
\t\t\t\t<td>
\t\t\t\t\t";
            // line 616
            if (isset($context["times_work_hours"])) { $_times_work_hours_ = $context["times_work_hours"]; } else { $_times_work_hours_ = null; }
            if ($_times_work_hours_) {
                // line 617
                echo "\t\t\t\t\t\t";
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->timeLength($this->getAttribute($_ticket_, "total_user_waiting_work_time"), "hours"), "html", null, true);
                echo "
\t\t\t\t\t\t<span class=\"tipped small-light-icon\" title=\"Work hours only, real time: ";
                // line 618
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->timeLength($this->getAttribute($_ticket_, "real_total_user_waiting")), "html", null, true);
                echo "\"></span>
\t\t\t\t\t";
            } else {
                // line 620
                echo "\t\t\t\t\t\t<time class=\"timeago\" data-timeago-no-ago=\"1\" datetime=\"";
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, (("- " . (($this->getAttribute($_ticket_, "real_total_user_waiting", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_ticket_, "real_total_user_waiting"), "1")) : ("1"))) . " seconds"), "c", "UTC"), "html", null, true);
                echo "\"></time>
\t\t\t\t\t";
            }
            // line 622
            echo "\t\t\t\t</td>
\t\t\t</tr></tbody>
\t\t\t";
        }
        // line 625
        echo "\t\t\t";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (($this->getAttribute($_ticket_, "total_to_first_reply") && ($this->getAttribute($_ticket_, "total_to_first_reply") > 0))) {
            // line 626
            echo "\t\t\t\t<tbody><tr>
\t\t\t\t\t<th>";
            // line 627
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.waiting_til_first_reply");
            echo ":</th>
\t\t\t\t\t<td>
\t\t\t\t\t\t";
            // line 629
            if (isset($context["times_work_hours"])) { $_times_work_hours_ = $context["times_work_hours"]; } else { $_times_work_hours_ = null; }
            if ($_times_work_hours_) {
                // line 630
                echo "\t\t\t\t\t\t\t";
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->timeLength($this->getAttribute($_ticket_, "work_time_to_first_reply"), "hours"), "html", null, true);
                echo "
\t\t\t\t\t\t\t<span class=\"tipped small-light-icon\" title=\"Work hours only, real time: ";
                // line 631
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->timeLength($this->getAttribute($_ticket_, "total_to_first_reply")), "html", null, true);
                echo "\"></span>
\t\t\t\t\t\t";
            } else {
                // line 633
                echo "\t\t\t\t\t\t\t<time class=\"timeago\" data-timeago-no-ago=\"1\" datetime=\"";
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_first_agent_reply"), "c", "UTC"), "html", null, true);
                echo "\"></time>
\t\t\t\t\t\t";
            }
            // line 635
            echo "\t\t\t\t\t</td>
\t\t\t\t</tr></tbody>
\t\t\t";
        }
        // line 638
        echo "\t\t\t";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ((($this->getAttribute($_ticket_, "status") == "awaiting_user") && $this->getAttribute($_ticket_, "date_agent_waiting"))) {
            // line 639
            echo "\t\t\t<tbody><tr>
\t\t\t\t<th>";
            // line 640
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.current_agent_waiting_time");
            echo ":</th>
\t\t\t\t<td>
\t\t\t\t\t<time class=\"timeago\" data-timeago-no-ago=\"1\" datetime=\"";
            // line 642
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_agent_waiting"), "c", "UTC"), "html", null, true);
            echo "\"></time>
\t\t\t\t\t(";
            // line 643
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.since_time", array("time" => $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_agent_waiting"), "fulltime")));
            echo ")
\t\t\t\t</td>
\t\t\t</tr></tbody>
\t\t\t";
        }
        // line 647
        echo "\t\t\t";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ($this->getAttribute($_ticket_, "date_resolved")) {
            // line 648
            echo "\t\t\t<tbody><tr>
\t\t\t\t<th>";
            // line 649
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.date_resolved");
            echo ":</th>
\t\t\t\t<td>
\t\t\t\t\t<time class=\"timeago\" datetime=\"";
            // line 651
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_resolved"), "c", "UTC"), "html", null, true);
            echo "\"></time>
\t\t\t\t\t(";
            // line 652
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_resolved")), "html", null, true);
            echo ")
\t\t\t\t</td>
\t\t\t</tr></tbody>
\t\t\t<tbody><tr>
\t\t\t\t<th>";
            // line 656
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.time_til_resolve");
            echo "</th>:</th>
\t\t\t\t<td>
\t\t\t\t\t";
            // line 658
            if (isset($context["times_work_hours"])) { $_times_work_hours_ = $context["times_work_hours"]; } else { $_times_work_hours_ = null; }
            if ($_times_work_hours_) {
                // line 659
                echo "\t\t\t\t\t\t";
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->timeLength($this->getAttribute($_ticket_, "work_time_until_resolution"), "hours"), "html", null, true);
                echo "
\t\t\t\t\t\t<span class=\"tipped small-light-icon\" title=\"Work hours only, real time: ";
                // line 660
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->timeLength($this->getAttribute($_ticket_, "time_until_resolution")), "html", null, true);
                echo "\"></span>
\t\t\t\t\t";
            } else {
                // line 662
                echo "\t\t\t\t\t\t<time class=\"timeago\" data-timeago-no-ago=\"1\" datetime=\"";
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, (("- " . (($this->getAttribute($_ticket_, "time_until_resolution", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_ticket_, "time_until_resolution"), "1")) : ("1"))) . " seconds"), "c", "UTC"), "html", null, true);
                echo "\"></time>
\t\t\t\t\t";
            }
            // line 664
            echo "\t\t\t\t</td>
\t\t\t</tr></tbody>
\t\t\t";
        }
        // line 667
        echo "\t\t\t";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ($this->getAttribute($_ticket_, "date_closed")) {
            // line 668
            echo "\t\t\t<tbody><tr>
\t\t\t\t<th>";
            // line 669
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.date_archived");
            echo ":</th>
\t\t\t\t<td>
\t\t\t\t\t<time class=\"timeago\" datetime=\"";
            // line 671
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_closed"), "c", "UTC"), "html", null, true);
            echo "\"></time>
\t\t\t\t\t(";
            // line 672
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_closed")), "html", null, true);
            echo ")
\t\t\t\t</td>
\t\t\t</tr></tbody>
\t\t\t";
        }
        // line 676
        echo "\t\t\t";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ($this->getAttribute($_ticket_, "date_first_agent_assign")) {
            // line 677
            echo "\t\t\t<tbody><tr>
\t\t\t\t<th>";
            // line 678
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.first_assignment");
            echo ":</th>
\t\t\t\t<td>
\t\t\t\t\t<time class=\"timeago\" datetime=\"";
            // line 680
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_first_agent_assign"), "c", "UTC"), "html", null, true);
            echo "\"></time>
\t\t\t\t\t(";
            // line 681
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_first_agent_assign"), "fulltime"), "html", null, true);
            echo ")
\t\t\t\t</td>
\t\t\t</tr></tbody>
\t\t\t";
        }
        // line 685
        echo "\t\t\t";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ($this->getAttribute($_ticket_, "date_first_agent_reply")) {
            // line 686
            echo "\t\t\t<tbody><tr>
\t\t\t\t<th>";
            // line 687
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.first_agent_reply");
            echo ":</th>
\t\t\t\t<td>
\t\t\t\t\t<time class=\"timeago\" datetime=\"";
            // line 689
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_first_agent_reply"), "c", "UTC"), "html", null, true);
            echo "\"></time>
\t\t\t\t\t(";
            // line 690
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_first_agent_reply"), "fulltime"), "html", null, true);
            echo ")
\t\t\t\t</td>
\t\t\t</tr></tbody>
\t\t\t";
        }
        // line 694
        echo "\t\t\t";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ($this->getAttribute($_ticket_, "date_last_agent_reply")) {
            // line 695
            echo "\t\t\t<tbody><tr>
\t\t\t\t<th>";
            // line 696
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.last_agent_reply");
            echo ":</th>
\t\t\t\t<td>
\t\t\t\t\t<time class=\"timeago\" datetime=\"";
            // line 698
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_last_agent_reply"), "c", "UTC"), "html", null, true);
            echo "\"></time>
\t\t\t\t\t(";
            // line 699
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_last_agent_reply"), "fulltime"), "html", null, true);
            echo ")
\t\t\t\t</td>
\t\t\t</tr></tbody>
\t\t\t";
        }
        // line 703
        echo "\t\t\t";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ($this->getAttribute($_ticket_, "date_last_user_reply")) {
            // line 704
            echo "\t\t\t<tbody><tr>
\t\t\t\t<th>";
            // line 705
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.last_user_reply");
            echo ":</th>
\t\t\t\t<td>
\t\t\t\t\t<time class=\"timeago\" datetime=\"";
            // line 707
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_last_user_reply"), "c", "UTC"), "html", null, true);
            echo "\"></time>
\t\t\t\t\t(";
            // line 708
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_last_user_reply"), "fulltime"), "html", null, true);
            echo ")
\t\t\t\t</td>
\t\t\t</tr></tbody>
\t\t\t";
        }
        // line 712
        echo "\t\t</table>
\t</section>
\t";
        // line 714
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["ticket_api"])) { $_ticket_api_ = $context["ticket_api"]; } else { $_ticket_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgetTabsBody($_baseId_, "ticket", "messages", "section", "article", $_ticket_api_);
        echo "
</div>

";
        // line 717
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["ticket_api"])) { $_ticket_api_ = $context["ticket_api"]; } else { $_ticket_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "ticket", "messages", "below", $_ticket_api_);
        echo "
";
        // line 718
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["ticket_api"])) { $_ticket_api_ = $context["ticket_api"]; } else { $_ticket_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "ticket", "reply", "above", $_ticket_api_);
        echo "

";
        // line 720
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent.ticket_reverse_order"), "method"))) {
            // line 721
            echo "\t";
            if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
            if (($this->getAttribute($_ticket_perms_, "reply") || $this->getAttribute($_ticket_perms_, "modify_notes"))) {
                // line 722
                echo "\t<div style=\"position:relative;\">
\t\t";
                // line 723
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                if ($this->getAttribute($_ticket_, "isLocked", array(0 => $this->getAttribute($_app_, "user")), "method")) {
                    echo "<div class=\"lock-overlay\"></div>";
                }
                // line 724
                echo "\t\t<div id=\"";
                if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
                echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
                echo "_replybox_wrap\" class=\"replybox-wrap\">
\t\t\t";
                // line 725
                $this->env->loadTemplate("AgentBundle:Ticket:replybox.html.twig")->display($context);
                // line 726
                echo "\t\t</div>
\t</div>
\t";
            }
        }
        // line 730
        echo "
";
        // line 731
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["ticket_api"])) { $_ticket_api_ = $context["ticket_api"]; } else { $_ticket_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "ticket", "reply", "below", $_ticket_api_);
        echo "
";
        // line 732
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["ticket_api"])) { $_ticket_api_ = $context["ticket_api"]; } else { $_ticket_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "ticket", "footer", "above", $_ticket_api_);
        echo "

";
        // line 735
        echo "<script type=\"text/x-deskpro-plain\" id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_fields_display_main_wrap_tpl\">
\t<table class=\"prop-table display-item\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
\t\t<tr>
\t\t\t<th class=\"prop-title display-title\"></th>
\t\t\t<td class=\"prop-val display-content\"></td>
\t\t</tr>
\t</table>
</script>
<script type=\"text/x-deskpro-plain\" id=\"";
        // line 743
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_fields_display_main_edit_tpl\">
\t<div class=\"fields-edit-container\">
\t\t<div class=\"fields-edit-rows\"></div>
\t\t<div class=\"controls\">
\t\t\t<div class=\"is-not-loading\">
\t\t\t\t<button class=\"clean-white save-trigger\">";
        // line 748
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.save");
        echo "</button>
\t\t\t\t<button class=\"clean-white close-trigger\">";
        // line 749
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.cancel");
        echo "</button>
\t\t\t</div>
\t\t\t<div class=\"is-loading\">
\t\t\t\t";
        // line 752
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.saving");
        echo "
\t\t\t</div>
\t\t</div>
\t</div>
</script>
<script type=\"text/x-deskpro-plain\" id=\"";
        // line 757
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_fields_display_main_edit_row_tpl\">
\t<table class=\"prop-table display-item\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
\t\t<tr>
\t\t\t<th class=\"display-title\"></th>
\t\t\t<td class=\"display-content\"></td>
\t\t</tr>
\t</table>
</script>

";
        // line 767
        echo "<script type=\"text/x-deskpro-plain\" class=\"tpl-fields-maintabs-tab\" id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_fields_display_tabs_tab_tpl\">
\t<li class=\"tab-trigger {id} field-tab\" data-tab-for=\".{id}\" data-field-tab-id=\"{id}\">
\t\t<a>{title}</a>
\t</li>
</script>
<script type=\"text/x-deskpro-plain\" class=\"tpl-fields-maintabs-content\" id=\"";
        // line 772
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_fields_display_tabs_content_tpl\">
\t<div class=\"full-container-tabbed-contents tab-content field-tab-content {id}\">
\t</div>
</script>
<script type=\"text/x-deskpro-plain\" id=\"";
        // line 776
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_fields_display_tabs_wrap_tpl\">
\t<dl class=\"info-list display-item\">
\t\t<dt class=\"display-title\"></dt>
\t\t<dd class=\"display-content\"></dd>
\t</dl>
</script>

<ul class=\"ticket-message-edit-menu\" style=\"display:none\">
\t<li data-option-id=\"window\" data-url=\"";
        // line 784
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_ticket_message_window", array("ticket_id" => "00000", "message_id" => "11111", "_rt" => $this->env->getExtension('deskpro_templating')->securityToken("request_token", 10800))), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.view_message_in_window");
        echo "</li>
\t<li data-option-id=\"quote\">";
        // line 785
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.quote");
        echo "</li>
\t<li class=\"forward-message\" data-option-id=\"fwd\">";
        // line 786
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.forward_message");
        echo "</li>
\t";
        // line 787
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if ($this->getAttribute($_ticket_perms_, "modify_messages")) {
            echo "<li class=\"delete-link\" data-option-id=\"delete\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.delete_message");
            echo "</li>";
        }
        // line 788
        echo "\t";
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if ($this->getAttribute($_ticket_perms_, "delete")) {
            echo "<li class=\"delete-attachments-link\" data-option-id=\"delete-attachments\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.delete_attach");
            echo "</li>";
        }
        // line 789
        echo "\t<li class=\"set-as-note\" data-option-id=\"setnote.note\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.set_as_note");
        echo "</li>
\t<li class=\"set-as-message\" data-option-id=\"setnote.message\">";
        // line 790
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.set_as_normal_message");
        echo "</li>
\t";
        // line 791
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if ($this->getAttribute($_ticket_perms_, "modify_merge")) {
            echo "<li data-option-id=\"split\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.split_message_btn");
            echo "</li>";
        }
        // line 792
        echo "\t";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "user"), "hasPerm", array(0 => "agent_tickets.create"), "method")) {
            echo "<li data-option-id=\"linked_ticket\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.create_linked_ticket");
            echo "</li>";
        }
        // line 793
        echo "\t";
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if ($this->getAttribute($_ticket_perms_, "modify_messages")) {
            echo "<li data-option-id=\"edit\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.edit");
            echo "</li>";
        }
        // line 794
        echo "\t";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "user"), "can_admin")) {
            echo "<li data-option-id=\"debug\" data-url=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_ticket_download_debug_report", array("ticket_id" => "00000", "_rt" => $this->env->getExtension('deskpro_templating')->securityToken("request_token", 10800))), "html", null, true);
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.debug_file_download");
            echo "</li>";
        }
        // line 795
        echo "\t";
        // line 796
        echo "</ul>
<ul class=\"ticket-macros-menu\" style=\"display:none\" ";
        // line 797
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if ($_baseId_) {
            echo "id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_macros_menu\"";
        }
        echo ">
\t";
        // line 798
        if (isset($context["macros"])) { $_macros_ = $context["macros"]; } else { $_macros_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_macros_);
        foreach ($context['_seq'] as $context["_key"] => $context["macro"]) {
            // line 799
            echo "\t\t<li class=\"res-ticketmacro-";
            if (isset($context["macro"])) { $_macro_ = $context["macro"]; } else { $_macro_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_macro_, "id"), "html", null, true);
            echo "\" data-macro-id=\"";
            if (isset($context["macro"])) { $_macro_ = $context["macro"]; } else { $_macro_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_macro_, "id"), "html", null, true);
            echo "\"><span class=\"macro-title\">";
            if (isset($context["macro"])) { $_macro_ = $context["macro"]; } else { $_macro_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_macro_, "title"), "html", null, true);
            echo "</span></li>
\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['macro'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 801
        echo "\t<li class=\"open-settings-trigger\" style=\"background-color: #E4E4E4; border-top: 1px solid #C1C1C1;\"><i class=\"icon-cog\"></i> ";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.manage_macros");
        echo "</li>
</ul>

<div class=\"delete-ticket-overlay overlay\" style=\"display:none\">
\t<div class=\"overlay-title\">
\t\t<h4>";
        // line 806
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.delete");
        echo "</h4>
\t</div>

\t<div class=\"overlay-content\">
\t\t";
        // line 810
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.deleted_reason");
        echo ": <input type=\"text\" value=\"\" class=\"delete-reason\" style=\"width: 200px;\" />

\t\t";
        // line 812
        if (isset($context["person_object_counts"])) { $_person_object_counts_ = $context["person_object_counts"]; } else { $_person_object_counts_ = null; }
        if ((($this->getAttribute($_person_object_counts_, "tickets") > 1) || $this->getAttribute($_person_object_counts_, "chats"))) {
            // line 813
            echo "\t\t<div id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_delete_user_list\" style=\"display: none;\">
\t\t\t<br/>
\t\t\t";
            // line 815
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.people.other_user_content_being_deleted");
            echo "<br />
\t\t\t&bull; ";
            // line 816
            if (isset($context["person_object_counts"])) { $_person_object_counts_ = $context["person_object_counts"]; } else { $_person_object_counts_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.count_tickets", array("count" => $this->getAttribute($_person_object_counts_, "tickets")));
            echo "
\t\t\t";
            // line 817
            if (isset($context["person_object_counts"])) { $_person_object_counts_ = $context["person_object_counts"]; } else { $_person_object_counts_ = null; }
            if ($this->getAttribute($_person_object_counts_, "chats")) {
                echo "&bull; ";
                if (isset($context["person_object_counts"])) { $_person_object_counts_ = $context["person_object_counts"]; } else { $_person_object_counts_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.count_chat", array("count" => $this->getAttribute($_person_object_counts_, "chats")));
                echo "<br/>";
            }
            // line 818
            echo "\t\t</div>
\t\t";
        }
        // line 820
        echo "\t</div>

\t<div class=\"overlay-footer\">
\t\t<div class=\"loading-off\">
\t\t\t<button class=\"save-trigger clean-white\">";
        // line 824
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.delete");
        echo "</button>
\t\t</div>
\t\t<div class=\"loading-on\" style=\"display:none\">
\t\t\t<span>";
        // line 827
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.saving");
        echo "... <span class=\"flat-spinner\"></span></span>
\t\t</div>
\t</div>
</div>

<div class=\"delete-message-overlay overlay\" style=\"display:none\">
\t<div class=\"overlay-title\">
\t\t<h4>";
        // line 834
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.delete");
        echo "</h4>
\t</div>

\t<div class=\"overlay-content\">
\t\t";
        // line 838
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.delete_message_confirm");
        echo "
\t\t<div class=\"ticket-messages\"></div>
\t\t<input type=\"hidden\" class=\"message-id\" value=\"\" />
\t</div>

\t<div class=\"overlay-footer\">
\t\t<div class=\"loading-off\">
\t\t\t<button class=\"save-trigger clean-white\">";
        // line 845
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.delete");
        echo "</button>
\t\t</div>
\t\t<div class=\"loading-on\" style=\"display:none\">
\t\t\t<span>";
        // line 848
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.saving");
        echo "... <span class=\"flat-spinner\"></span></span>
\t\t</div>
\t</div>
</div>

<div class=\"macro-controls\" style=\"display: none;\">
\t<button class=\"clean-white save\" id=\"macro_apply_btn\">";
        // line 854
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.apply");
        echo "</button>
\t<button class=\"clean-white cancel\" id=\"macro_cancel_btn\">";
        // line 855
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.cancel");
        echo "</button>
</div>

<div class=\"confirm-macro-overlay\" id=\"";
        // line 858
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_confirm_macro_overlay\" style=\"width: 800px; height: 400px; display: none;\">
\t<div class=\"overlay-title\">
\t\t<h4>";
        // line 860
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.confirm");
        echo "</h4>
\t</div>
\t<div class=\"overlay-content\">
\t\t<ul class=\"actions-list\"></ul>
\t</div>
\t<div class=\"overlay-footer\">
\t\t<button class=\"clean-white\" id=\"";
        // line 866
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_apply_macro_btn\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.apply");
        echo "</button>
\t</div>
</div>

<section id=\"";
        // line 870
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_message_edit_overlay\" style=\"display: none;\">
\t<div class=\"overlay-title\">
\t\t<span class=\"close-overlay close-trigger\"></span>
\t\t<h4>";
        // line 873
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.edit");
        echo "</h4>
\t</div>
\t<div class=\"overlay-content\">
\t\t<input type=\"hidden\" name=\"message_id\" class=\"message_id\" value=\"0\" />
\t\t<textarea rows=\"10\" cols=\"60\" style=\"width: 98%; height: 200px; color: #000; size: 10px; font-family: Monaco, Consolas, monospace;\" name=\"message_text\" class=\"message_text\"></textarea>
\t</div>
\t<div class=\"overlay-footer\">
\t\t<button class=\"clean-white save-text-trigger\">";
        // line 880
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.save");
        echo "</button>
\t\t<span class=\"flat-spinner save-text-loading\" style=\"display: none\"></span>
\t</div>
</section>

";
        // line 885
        $this->env->loadTemplate("AgentBundle:Ticket:custom-view-include.html.twig")->display($context);
        // line 886
        echo "
";
        // line 887
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["ticket_api"])) { $_ticket_api_ = $context["ticket_api"]; } else { $_ticket_api_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getWidgets($_baseId_, "ticket", "", "", $_ticket_api_);
        echo "

</div>

<div id=\"";
        // line 891
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_pending_add\" style=\"display:none;\" data-save-url=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_kb_newpending"), "html", null, true);
        echo "\">
\t<div class=\"mass-actions-overlay pending-article\" style=\"width: 550px; height: 400px;\">
\t\t<div class=\"overlay-title\">
\t\t\t<h4>";
        // line 894
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.create_new_pending_article");
        echo "</h4>
\t\t</div>
\t\t<div class=\"overlay-content\">
\t\t\t<div style=\"font-size: 12px; padding-bottom: 9px;\">";
        // line 897
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.pending_article_info");
        echo "</div>
\t\t\t<input type=\"hidden\" name=\"ticket_id\" value=\"";
        // line 898
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "id"), "html", null, true);
        echo "\" />
\t\t\t<textarea name=\"comment\" placeholder=\"";
        // line 899
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.pending_article_desc");
        echo "\" style=\"width: 98%; height: 260px;\"></textarea>
\t\t</div>
\t\t<div class=\"overlay-footer\">
\t\t\t<div class=\"is-not-loading\">
\t\t\t\t<button class=\"save-new-trigger clean-white\">";
        // line 903
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.add");
        echo "</button>
\t\t\t</div>
\t\t\t<div class=\"is-loading\">
\t\t\t\t<i class=\"flat-spinner\"></i>
\t\t\t</div>
\t\t</div>
\t</div>
</div>

</div></div> ";
        // line 913
        echo "</div>";
    }

    public function getTemplateName()
    {
        return "AgentBundle:Ticket:view.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  3071 => 913,  3059 => 903,  3052 => 899,  3047 => 898,  3043 => 897,  3037 => 894,  3028 => 891,  3019 => 887,  3016 => 886,  3014 => 885,  3006 => 880,  2996 => 873,  2989 => 870,  2979 => 866,  2970 => 860,  2964 => 858,  2958 => 855,  2954 => 854,  2945 => 848,  2939 => 845,  2929 => 838,  2922 => 834,  2912 => 827,  2906 => 824,  2900 => 820,  2896 => 818,  2888 => 817,  2883 => 816,  2879 => 815,  2872 => 813,  2869 => 812,  2864 => 810,  2857 => 806,  2848 => 801,  2832 => 799,  2827 => 798,  2817 => 797,  2814 => 796,  2812 => 795,  2802 => 794,  2786 => 792,  2779 => 791,  2775 => 790,  2770 => 789,  2762 => 788,  2755 => 787,  2751 => 786,  2747 => 785,  2741 => 784,  2729 => 776,  2721 => 772,  2711 => 767,  2698 => 757,  2690 => 752,  2684 => 749,  2651 => 732,  2642 => 730,  2634 => 725,  2628 => 724,  2622 => 723,  2619 => 722,  2615 => 721,  2612 => 720,  2605 => 718,  2599 => 717,  2591 => 714,  2587 => 712,  2579 => 708,  2574 => 707,  2569 => 705,  2566 => 704,  2562 => 703,  2554 => 699,  2549 => 698,  2544 => 696,  2541 => 695,  2524 => 689,  2519 => 687,  2516 => 686,  2512 => 685,  2504 => 681,  2494 => 678,  2479 => 672,  2474 => 671,  2466 => 668,  2462 => 667,  2444 => 660,  2438 => 659,  2435 => 658,  2430 => 656,  2417 => 651,  2412 => 649,  2409 => 648,  2405 => 647,  2397 => 643,  2387 => 640,  2375 => 635,  2368 => 633,  2362 => 631,  2356 => 630,  2353 => 629,  2348 => 627,  2345 => 626,  2341 => 625,  2336 => 622,  2329 => 620,  2323 => 618,  2317 => 617,  2314 => 616,  2309 => 614,  2306 => 613,  2302 => 612,  2286 => 606,  2280 => 604,  2274 => 603,  2271 => 602,  2266 => 600,  2263 => 599,  2259 => 598,  2249 => 594,  2244 => 593,  2239 => 591,  2232 => 588,  2226 => 586,  2221 => 585,  2210 => 582,  2197 => 581,  2180 => 579,  2169 => 576,  2150 => 575,  2140 => 572,  2131 => 570,  2110 => 567,  2106 => 566,  2061 => 561,  2026 => 554,  2018 => 553,  2000 => 550,  1990 => 548,  1962 => 540,  1956 => 536,  1939 => 532,  1935 => 531,  1896 => 520,  1888 => 519,  1881 => 518,  1869 => 517,  1857 => 516,  1848 => 514,  1844 => 512,  1832 => 508,  1811 => 502,  1808 => 501,  1786 => 492,  1779 => 490,  1769 => 485,  1762 => 483,  1758 => 482,  1735 => 472,  1731 => 470,  1712 => 465,  1707 => 463,  1696 => 461,  1683 => 454,  1679 => 452,  1655 => 445,  1650 => 444,  1647 => 443,  1635 => 439,  1607 => 427,  1597 => 423,  1541 => 402,  1523 => 393,  1495 => 389,  1485 => 387,  1455 => 383,  1450 => 382,  1441 => 378,  1435 => 377,  1419 => 375,  1407 => 373,  1362 => 364,  1347 => 361,  1296 => 352,  1258 => 348,  1253 => 347,  1177 => 325,  1151 => 320,  1118 => 314,  1330 => 4,  1327 => 3,  1322 => 2,  1290 => 297,  1283 => 295,  1278 => 292,  1271 => 349,  1230 => 277,  1190 => 265,  1166 => 258,  1138 => 251,  642 => 147,  1264 => 464,  1259 => 462,  1227 => 448,  1211 => 336,  1399 => 568,  1387 => 560,  1358 => 555,  1334 => 357,  1316 => 529,  1298 => 520,  1195 => 478,  1148 => 253,  1120 => 453,  1117 => 245,  1093 => 440,  788 => 201,  612 => 203,  1394 => 446,  1385 => 442,  1382 => 441,  1369 => 433,  1363 => 432,  1360 => 431,  1350 => 429,  1305 => 523,  1300 => 409,  1281 => 404,  1277 => 350,  1265 => 398,  1255 => 396,  1247 => 393,  1234 => 387,  1202 => 270,  1199 => 374,  1187 => 264,  1162 => 365,  1136 => 317,  1128 => 352,  1122 => 248,  1069 => 299,  968 => 387,  846 => 188,  1183 => 449,  1132 => 460,  1097 => 341,  957 => 272,  907 => 278,  875 => 263,  653 => 176,  1329 => 405,  1309 => 354,  1303 => 399,  1297 => 398,  1292 => 397,  1280 => 394,  1249 => 346,  1237 => 279,  1205 => 409,  1200 => 408,  1194 => 440,  1178 => 430,  1175 => 447,  1170 => 375,  1135 => 362,  1105 => 428,  1078 => 335,  1068 => 388,  1048 => 417,  961 => 273,  922 => 280,  750 => 192,  842 => 263,  1038 => 292,  904 => 198,  882 => 194,  831 => 267,  860 => 314,  790 => 284,  733 => 230,  707 => 185,  744 => 220,  873 => 349,  824 => 267,  762 => 243,  713 => 225,  578 => 158,  2739 => 1153,  2735 => 1152,  2732 => 1151,  2725 => 1146,  2712 => 1144,  2707 => 1143,  2692 => 1131,  2688 => 1130,  2683 => 1127,  2680 => 748,  2677 => 1125,  2671 => 743,  2658 => 735,  2653 => 1118,  2645 => 731,  2641 => 1112,  2636 => 726,  2632 => 1108,  2629 => 1107,  2626 => 1106,  2606 => 1089,  2602 => 1088,  2598 => 1087,  2594 => 1086,  2590 => 1085,  2586 => 1084,  2572 => 1073,  2568 => 1072,  2564 => 1071,  2560 => 1070,  2556 => 1069,  2552 => 1068,  2537 => 694,  2533 => 1055,  2529 => 690,  2525 => 1053,  2521 => 1052,  2503 => 1040,  2499 => 680,  2495 => 1038,  2491 => 677,  2487 => 676,  2483 => 1035,  2469 => 669,  2465 => 1023,  2461 => 1022,  2457 => 664,  2453 => 1020,  2449 => 1019,  2426 => 999,  2422 => 652,  2418 => 997,  2414 => 996,  2410 => 995,  2406 => 994,  2392 => 642,  2388 => 982,  2384 => 639,  2380 => 638,  2376 => 979,  2372 => 978,  2358 => 967,  2354 => 966,  2350 => 965,  2346 => 964,  2342 => 963,  2338 => 962,  2324 => 951,  2320 => 950,  2316 => 949,  2312 => 948,  2308 => 947,  2304 => 946,  2295 => 939,  2282 => 937,  2277 => 936,  2269 => 931,  2265 => 930,  2260 => 927,  2256 => 597,  2253 => 925,  2230 => 901,  2220 => 899,  2216 => 898,  2211 => 896,  2208 => 895,  2198 => 893,  2194 => 580,  2189 => 890,  2184 => 889,  2177 => 578,  2173 => 577,  2164 => 877,  2151 => 875,  2146 => 574,  2143 => 873,  2141 => 864,  2134 => 571,  2130 => 859,  2122 => 853,  2120 => 852,  2113 => 848,  2105 => 846,  2096 => 839,  2083 => 564,  2078 => 836,  2073 => 562,  2067 => 830,  2062 => 828,  2058 => 827,  2054 => 826,  2050 => 560,  2045 => 557,  2038 => 822,  2034 => 821,  2022 => 814,  2009 => 812,  1999 => 809,  1993 => 805,  1988 => 803,  1984 => 802,  1980 => 801,  1976 => 546,  1971 => 799,  1968 => 798,  1964 => 797,  1960 => 796,  1954 => 535,  1946 => 788,  1942 => 533,  1938 => 786,  1932 => 530,  1927 => 780,  1919 => 527,  1915 => 777,  1911 => 776,  1906 => 775,  1903 => 774,  1899 => 773,  1895 => 772,  1886 => 768,  1883 => 764,  1871 => 759,  1866 => 756,  1863 => 755,  1842 => 752,  1825 => 749,  1818 => 748,  1815 => 503,  1800 => 499,  1793 => 741,  1772 => 729,  1761 => 721,  1757 => 720,  1751 => 717,  1740 => 709,  1719 => 697,  1715 => 696,  1686 => 673,  1676 => 669,  1665 => 661,  1661 => 446,  1645 => 442,  1638 => 649,  1630 => 438,  1625 => 435,  1622 => 641,  1598 => 637,  1577 => 416,  1558 => 408,  1549 => 405,  1546 => 626,  1535 => 618,  1531 => 617,  1514 => 606,  1510 => 605,  1504 => 391,  1493 => 594,  1483 => 590,  1464 => 580,  1458 => 577,  1443 => 568,  1437 => 565,  1426 => 557,  1405 => 545,  1401 => 544,  1395 => 541,  1390 => 538,  1381 => 368,  1373 => 531,  1348 => 521,  1343 => 520,  1335 => 515,  1321 => 510,  1318 => 549,  1299 => 503,  1294 => 298,  1282 => 496,  1269 => 466,  1260 => 397,  1240 => 344,  1221 => 339,  1216 => 338,  1210 => 272,  1206 => 445,  1193 => 467,  1189 => 466,  1155 => 468,  1150 => 419,  1022 => 312,  1006 => 299,  988 => 398,  969 => 392,  965 => 294,  921 => 286,  878 => 275,  866 => 349,  854 => 254,  819 => 322,  796 => 286,  1713 => 683,  1700 => 681,  1685 => 674,  1662 => 670,  1643 => 669,  1640 => 441,  1631 => 663,  1618 => 661,  1613 => 430,  1608 => 639,  1605 => 656,  1602 => 424,  1589 => 645,  1586 => 644,  1583 => 643,  1554 => 406,  1547 => 404,  1521 => 602,  1508 => 600,  1499 => 390,  1491 => 593,  1482 => 386,  1472 => 582,  1468 => 581,  1465 => 579,  1457 => 573,  1445 => 380,  1422 => 556,  1416 => 553,  1410 => 548,  1397 => 546,  1392 => 545,  1386 => 541,  1377 => 532,  1370 => 530,  1357 => 528,  1352 => 554,  1341 => 428,  1314 => 510,  1311 => 1,  1275 => 493,  1248 => 284,  1238 => 488,  1225 => 476,  1220 => 275,  1209 => 466,  1185 => 385,  1182 => 431,  1159 => 421,  1154 => 254,  1130 => 438,  1125 => 407,  1101 => 308,  1074 => 432,  1056 => 326,  1046 => 323,  1043 => 293,  1030 => 397,  1027 => 289,  947 => 361,  925 => 352,  913 => 259,  893 => 196,  881 => 253,  847 => 243,  829 => 336,  825 => 259,  1083 => 237,  995 => 399,  984 => 350,  963 => 292,  941 => 354,  851 => 367,  682 => 217,  1365 => 365,  1359 => 545,  1349 => 539,  1336 => 427,  1331 => 424,  1323 => 420,  1319 => 530,  1312 => 402,  1284 => 475,  1272 => 401,  1268 => 509,  1261 => 288,  1251 => 285,  1245 => 483,  1231 => 341,  1207 => 271,  1197 => 267,  1180 => 326,  1173 => 457,  1169 => 259,  1157 => 323,  1147 => 438,  1109 => 330,  1065 => 297,  1059 => 423,  1047 => 385,  1044 => 424,  1033 => 381,  1009 => 357,  991 => 363,  987 => 404,  973 => 294,  931 => 202,  924 => 261,  911 => 347,  906 => 81,  885 => 337,  872 => 335,  855 => 332,  749 => 240,  701 => 221,  594 => 180,  1163 => 257,  1143 => 318,  1087 => 420,  1077 => 300,  1051 => 325,  1037 => 223,  1010 => 301,  999 => 407,  932 => 352,  899 => 306,  895 => 404,  933 => 387,  914 => 133,  909 => 323,  833 => 284,  783 => 315,  755 => 303,  666 => 214,  453 => 168,  639 => 209,  568 => 176,  520 => 232,  657 => 216,  572 => 201,  609 => 202,  20 => 1,  659 => 217,  562 => 144,  548 => 180,  558 => 197,  479 => 157,  589 => 154,  457 => 199,  413 => 174,  953 => 206,  948 => 267,  935 => 394,  929 => 283,  916 => 382,  864 => 365,  844 => 242,  816 => 342,  807 => 212,  801 => 268,  774 => 257,  766 => 312,  737 => 297,  685 => 218,  664 => 175,  635 => 249,  593 => 199,  546 => 201,  532 => 236,  865 => 191,  852 => 241,  838 => 233,  820 => 182,  781 => 198,  764 => 193,  725 => 250,  632 => 268,  602 => 175,  565 => 145,  529 => 153,  505 => 147,  487 => 101,  473 => 212,  1853 => 515,  1849 => 753,  1839 => 751,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 733,  1778 => 732,  1770 => 807,  1749 => 478,  1738 => 793,  1728 => 469,  1721 => 788,  1717 => 787,  1709 => 693,  1705 => 781,  1688 => 675,  1681 => 769,  1677 => 451,  1667 => 449,  1656 => 762,  1648 => 655,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 413,  1545 => 719,  1534 => 606,  1527 => 395,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 593,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 372,  1400 => 370,  1383 => 540,  1379 => 557,  1356 => 632,  1351 => 362,  1338 => 359,  1332 => 617,  1315 => 613,  1302 => 353,  1289 => 406,  1276 => 604,  1263 => 601,  1244 => 456,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 332,  1181 => 561,  1171 => 446,  1158 => 550,  1114 => 428,  1104 => 465,  1072 => 333,  1062 => 7,  1024 => 395,  1014 => 218,  1000 => 368,  990 => 303,  980 => 393,  960 => 466,  918 => 285,  888 => 80,  834 => 232,  673 => 178,  636 => 145,  462 => 142,  454 => 120,  1144 => 463,  1139 => 356,  1131 => 316,  1127 => 434,  1110 => 312,  1092 => 459,  1089 => 239,  1086 => 238,  1084 => 337,  1063 => 232,  1060 => 231,  1055 => 295,  1050 => 227,  1035 => 291,  1019 => 330,  1003 => 401,  959 => 387,  900 => 366,  880 => 276,  870 => 250,  867 => 249,  859 => 294,  848 => 271,  839 => 376,  828 => 302,  823 => 183,  809 => 179,  800 => 241,  797 => 267,  794 => 177,  786 => 283,  740 => 194,  734 => 188,  703 => 228,  693 => 297,  630 => 166,  626 => 142,  614 => 163,  610 => 161,  581 => 206,  564 => 149,  525 => 138,  722 => 226,  697 => 282,  674 => 270,  671 => 177,  577 => 180,  569 => 187,  557 => 179,  502 => 187,  497 => 76,  445 => 163,  729 => 306,  684 => 180,  676 => 178,  669 => 268,  660 => 203,  647 => 175,  643 => 229,  601 => 195,  570 => 129,  522 => 132,  501 => 147,  296 => 108,  374 => 100,  631 => 207,  616 => 198,  608 => 194,  605 => 193,  596 => 134,  574 => 180,  561 => 126,  527 => 165,  433 => 183,  388 => 98,  426 => 172,  383 => 105,  461 => 137,  370 => 105,  395 => 166,  294 => 88,  223 => 71,  220 => 67,  492 => 129,  468 => 120,  444 => 149,  410 => 104,  397 => 136,  377 => 121,  262 => 80,  250 => 47,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 793,  2649 => 2234,  2625 => 2213,  2517 => 1051,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 662,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 608,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 568,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 543,  1826 => 506,  1819 => 504,  1732 => 1414,  1723 => 467,  1695 => 680,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 638,  1533 => 397,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 572,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 526,  1301 => 1037,  1252 => 392,  1243 => 283,  1223 => 978,  1212 => 494,  1167 => 366,  1161 => 933,  1145 => 923,  1140 => 413,  1134 => 409,  1126 => 443,  1106 => 528,  1100 => 897,  1026 => 220,  997 => 367,  983 => 298,  975 => 361,  956 => 318,  939 => 264,  902 => 274,  894 => 364,  879 => 76,  757 => 309,  727 => 293,  716 => 226,  670 => 204,  528 => 187,  476 => 213,  435 => 112,  354 => 89,  341 => 86,  192 => 17,  321 => 79,  243 => 75,  793 => 266,  780 => 247,  758 => 229,  700 => 193,  686 => 294,  652 => 172,  638 => 269,  620 => 165,  545 => 243,  523 => 82,  494 => 142,  459 => 156,  438 => 113,  351 => 84,  347 => 83,  402 => 99,  268 => 71,  430 => 108,  411 => 101,  379 => 95,  322 => 89,  315 => 38,  289 => 80,  284 => 86,  255 => 66,  234 => 60,  1133 => 444,  1124 => 455,  1121 => 430,  1116 => 405,  1113 => 429,  1108 => 376,  1103 => 444,  1098 => 401,  1081 => 320,  1073 => 235,  1067 => 314,  1064 => 330,  1061 => 421,  1053 => 318,  1049 => 294,  1042 => 313,  1039 => 384,  1025 => 304,  1021 => 219,  1015 => 308,  1008 => 284,  996 => 406,  989 => 277,  985 => 395,  981 => 296,  977 => 321,  970 => 275,  966 => 274,  955 => 293,  952 => 464,  943 => 266,  936 => 353,  930 => 289,  919 => 314,  917 => 348,  908 => 258,  905 => 363,  896 => 275,  891 => 338,  877 => 334,  862 => 248,  857 => 271,  837 => 261,  832 => 260,  827 => 184,  821 => 266,  803 => 179,  778 => 175,  769 => 253,  765 => 201,  753 => 171,  746 => 196,  743 => 297,  735 => 168,  730 => 187,  720 => 305,  717 => 165,  712 => 186,  691 => 219,  678 => 275,  654 => 199,  587 => 191,  576 => 167,  539 => 200,  517 => 210,  471 => 125,  441 => 110,  437 => 114,  418 => 102,  386 => 94,  373 => 120,  304 => 108,  270 => 69,  265 => 81,  229 => 42,  477 => 167,  455 => 125,  448 => 131,  429 => 124,  407 => 120,  399 => 105,  389 => 114,  375 => 148,  358 => 98,  349 => 137,  335 => 41,  327 => 106,  298 => 98,  280 => 71,  249 => 69,  194 => 81,  142 => 32,  344 => 95,  318 => 39,  306 => 102,  295 => 74,  357 => 101,  300 => 75,  286 => 79,  276 => 77,  269 => 97,  254 => 61,  128 => 27,  237 => 64,  165 => 40,  122 => 14,  798 => 256,  770 => 309,  759 => 278,  748 => 298,  731 => 191,  721 => 227,  718 => 188,  708 => 185,  696 => 236,  617 => 164,  590 => 154,  553 => 145,  550 => 157,  540 => 161,  533 => 182,  500 => 135,  493 => 160,  489 => 202,  482 => 198,  467 => 70,  464 => 170,  458 => 118,  452 => 197,  449 => 112,  415 => 152,  382 => 93,  372 => 92,  361 => 100,  356 => 124,  339 => 120,  302 => 36,  285 => 72,  258 => 40,  123 => 34,  108 => 26,  424 => 108,  394 => 115,  380 => 2,  338 => 42,  319 => 79,  316 => 78,  312 => 39,  290 => 73,  267 => 68,  206 => 36,  110 => 31,  240 => 74,  224 => 33,  219 => 39,  217 => 80,  202 => 48,  186 => 42,  170 => 37,  100 => 24,  67 => 14,  14 => 1,  1096 => 345,  1090 => 339,  1088 => 397,  1085 => 456,  1066 => 233,  1034 => 282,  1031 => 290,  1018 => 303,  1013 => 302,  1007 => 408,  1002 => 403,  993 => 279,  986 => 212,  982 => 211,  976 => 399,  971 => 209,  964 => 208,  949 => 289,  946 => 288,  940 => 388,  937 => 374,  928 => 262,  926 => 318,  915 => 284,  912 => 82,  903 => 231,  898 => 440,  892 => 255,  889 => 277,  887 => 302,  884 => 79,  876 => 252,  874 => 193,  871 => 331,  863 => 345,  861 => 270,  858 => 247,  850 => 189,  843 => 270,  840 => 186,  815 => 264,  812 => 263,  808 => 323,  804 => 258,  799 => 312,  791 => 202,  785 => 200,  775 => 313,  771 => 245,  754 => 267,  728 => 167,  726 => 190,  723 => 189,  715 => 105,  711 => 220,  709 => 222,  706 => 232,  698 => 182,  694 => 182,  692 => 161,  689 => 181,  681 => 224,  677 => 288,  675 => 234,  663 => 213,  661 => 174,  650 => 213,  646 => 231,  629 => 266,  627 => 180,  625 => 266,  622 => 202,  598 => 157,  592 => 155,  586 => 175,  575 => 189,  566 => 251,  556 => 146,  554 => 158,  541 => 144,  536 => 142,  515 => 79,  511 => 208,  509 => 150,  488 => 124,  486 => 145,  483 => 123,  465 => 110,  463 => 119,  450 => 182,  432 => 125,  419 => 178,  371 => 154,  362 => 144,  353 => 98,  337 => 124,  333 => 83,  309 => 38,  303 => 81,  299 => 89,  291 => 36,  272 => 99,  261 => 49,  253 => 30,  239 => 29,  235 => 65,  213 => 14,  200 => 55,  198 => 54,  159 => 46,  149 => 35,  146 => 31,  131 => 48,  116 => 25,  79 => 16,  74 => 45,  71 => 15,  836 => 262,  817 => 243,  814 => 321,  811 => 320,  805 => 244,  787 => 257,  779 => 169,  776 => 232,  773 => 322,  761 => 199,  751 => 302,  747 => 191,  742 => 190,  739 => 189,  736 => 215,  724 => 259,  705 => 69,  702 => 216,  688 => 226,  680 => 205,  667 => 273,  662 => 282,  656 => 215,  649 => 285,  644 => 174,  641 => 168,  624 => 162,  613 => 166,  607 => 137,  597 => 260,  591 => 170,  584 => 236,  579 => 132,  563 => 212,  559 => 183,  551 => 190,  547 => 140,  537 => 160,  524 => 164,  512 => 137,  507 => 237,  504 => 149,  498 => 162,  485 => 158,  480 => 126,  472 => 111,  466 => 138,  460 => 152,  447 => 107,  442 => 128,  434 => 133,  428 => 181,  422 => 119,  404 => 106,  368 => 136,  364 => 90,  340 => 86,  334 => 94,  330 => 93,  325 => 90,  292 => 92,  287 => 101,  282 => 103,  279 => 85,  273 => 33,  266 => 68,  256 => 73,  252 => 87,  228 => 90,  218 => 57,  201 => 51,  64 => 6,  51 => 10,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 569,  2109 => 847,  2104 => 802,  2101 => 801,  2087 => 565,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 823,  2037 => 789,  2031 => 555,  2028 => 818,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 811,  2001 => 1668,  1996 => 771,  1994 => 549,  1987 => 766,  1983 => 547,  1979 => 764,  1972 => 761,  1969 => 544,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 534,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 528,  1923 => 779,  1916 => 736,  1912 => 735,  1908 => 522,  1901 => 521,  1897 => 730,  1894 => 729,  1889 => 769,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 511,  1835 => 509,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 500,  1801 => 695,  1798 => 694,  1795 => 498,  1789 => 814,  1776 => 489,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 481,  1750 => 668,  1745 => 667,  1742 => 476,  1739 => 665,  1736 => 708,  1730 => 705,  1720 => 688,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 672,  1678 => 644,  1671 => 450,  1666 => 640,  1663 => 447,  1660 => 763,  1657 => 659,  1651 => 656,  1641 => 631,  1637 => 440,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 651,  1592 => 614,  1587 => 613,  1584 => 635,  1581 => 731,  1576 => 608,  1574 => 633,  1567 => 603,  1563 => 410,  1559 => 601,  1552 => 628,  1548 => 597,  1542 => 593,  1529 => 605,  1525 => 614,  1516 => 708,  1512 => 583,  1503 => 599,  1490 => 388,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 384,  1447 => 569,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 374,  1406 => 532,  1393 => 564,  1388 => 537,  1380 => 539,  1376 => 367,  1371 => 366,  1367 => 527,  1364 => 518,  1361 => 523,  1355 => 543,  1342 => 360,  1337 => 510,  1328 => 356,  1324 => 550,  1317 => 511,  1313 => 499,  1310 => 526,  1304 => 504,  1291 => 479,  1286 => 476,  1279 => 486,  1274 => 468,  1270 => 483,  1266 => 487,  1262 => 490,  1257 => 500,  1254 => 286,  1250 => 394,  1246 => 345,  1239 => 389,  1235 => 343,  1232 => 416,  1226 => 383,  1213 => 273,  1208 => 481,  1201 => 443,  1196 => 335,  1192 => 490,  1188 => 456,  1184 => 263,  1179 => 448,  1176 => 261,  1172 => 428,  1168 => 451,  1164 => 450,  1160 => 324,  1153 => 561,  1149 => 359,  1146 => 444,  1137 => 249,  1123 => 442,  1119 => 354,  1115 => 313,  1111 => 377,  1107 => 242,  1102 => 344,  1099 => 347,  1095 => 307,  1091 => 321,  1082 => 455,  1079 => 524,  1076 => 393,  1070 => 431,  1057 => 313,  1052 => 406,  1045 => 484,  1040 => 224,  1036 => 283,  1032 => 415,  1028 => 312,  1023 => 288,  1020 => 311,  1016 => 285,  1012 => 390,  1005 => 216,  1001 => 304,  998 => 308,  992 => 406,  979 => 297,  974 => 400,  967 => 399,  962 => 397,  958 => 383,  954 => 293,  950 => 292,  945 => 391,  942 => 290,  938 => 375,  934 => 203,  927 => 288,  923 => 201,  920 => 369,  910 => 365,  901 => 197,  897 => 256,  890 => 271,  886 => 270,  883 => 353,  868 => 273,  856 => 293,  853 => 341,  849 => 264,  845 => 329,  841 => 249,  835 => 268,  830 => 333,  826 => 226,  822 => 281,  818 => 265,  813 => 215,  810 => 290,  806 => 261,  802 => 210,  795 => 241,  792 => 335,  789 => 249,  784 => 286,  782 => 237,  777 => 255,  772 => 196,  768 => 195,  763 => 327,  760 => 305,  756 => 248,  752 => 198,  745 => 245,  741 => 218,  738 => 241,  732 => 171,  719 => 288,  714 => 251,  710 => 164,  704 => 184,  699 => 183,  695 => 66,  690 => 226,  687 => 210,  683 => 156,  679 => 155,  672 => 153,  668 => 176,  665 => 151,  658 => 177,  645 => 170,  640 => 184,  634 => 206,  628 => 166,  623 => 179,  619 => 78,  611 => 158,  606 => 234,  603 => 156,  599 => 174,  595 => 156,  583 => 169,  580 => 151,  573 => 148,  560 => 160,  543 => 175,  538 => 174,  534 => 138,  530 => 213,  526 => 170,  521 => 287,  518 => 194,  514 => 131,  510 => 154,  503 => 133,  496 => 202,  490 => 129,  484 => 128,  474 => 71,  470 => 139,  446 => 116,  440 => 130,  436 => 62,  431 => 113,  425 => 105,  416 => 168,  412 => 101,  408 => 100,  403 => 117,  400 => 119,  396 => 104,  392 => 96,  385 => 97,  381 => 150,  367 => 51,  363 => 89,  359 => 125,  355 => 76,  350 => 96,  346 => 73,  343 => 87,  328 => 93,  324 => 91,  313 => 81,  307 => 75,  301 => 119,  288 => 87,  283 => 88,  271 => 86,  257 => 48,  251 => 69,  238 => 62,  233 => 69,  195 => 47,  191 => 23,  187 => 20,  183 => 21,  130 => 31,  88 => 34,  76 => 14,  115 => 34,  95 => 20,  655 => 148,  651 => 275,  648 => 171,  637 => 210,  633 => 167,  621 => 462,  618 => 241,  615 => 178,  604 => 201,  600 => 233,  588 => 206,  585 => 153,  582 => 153,  571 => 187,  567 => 162,  555 => 125,  552 => 141,  549 => 123,  544 => 179,  542 => 139,  535 => 237,  531 => 139,  519 => 80,  516 => 218,  513 => 154,  508 => 117,  506 => 130,  499 => 209,  495 => 125,  491 => 146,  481 => 215,  478 => 72,  475 => 121,  469 => 182,  456 => 135,  451 => 139,  443 => 118,  439 => 178,  427 => 60,  423 => 59,  420 => 109,  409 => 107,  405 => 99,  401 => 56,  391 => 159,  387 => 133,  384 => 112,  378 => 94,  365 => 93,  360 => 102,  348 => 88,  336 => 84,  332 => 40,  329 => 119,  323 => 116,  310 => 77,  305 => 76,  277 => 34,  274 => 94,  263 => 105,  259 => 67,  247 => 38,  244 => 76,  241 => 59,  222 => 52,  210 => 65,  207 => 23,  204 => 56,  184 => 46,  181 => 77,  167 => 36,  157 => 32,  96 => 22,  421 => 143,  417 => 150,  414 => 145,  406 => 139,  398 => 98,  393 => 53,  390 => 98,  376 => 108,  369 => 148,  366 => 99,  352 => 128,  345 => 49,  342 => 126,  331 => 108,  326 => 80,  320 => 77,  317 => 86,  314 => 86,  311 => 85,  308 => 111,  297 => 81,  293 => 34,  281 => 78,  278 => 71,  275 => 70,  264 => 74,  260 => 80,  248 => 76,  245 => 68,  242 => 72,  231 => 61,  227 => 53,  215 => 60,  212 => 26,  209 => 73,  197 => 61,  177 => 44,  171 => 49,  161 => 51,  132 => 29,  121 => 26,  105 => 22,  99 => 21,  81 => 43,  77 => 16,  180 => 20,  176 => 16,  156 => 30,  143 => 32,  139 => 16,  118 => 30,  189 => 80,  185 => 48,  173 => 43,  166 => 19,  152 => 44,  174 => 37,  164 => 33,  154 => 28,  150 => 43,  137 => 33,  133 => 28,  127 => 28,  107 => 43,  102 => 41,  83 => 20,  78 => 31,  53 => 7,  23 => 3,  42 => 8,  138 => 31,  134 => 30,  109 => 24,  103 => 25,  97 => 23,  94 => 37,  84 => 18,  75 => 16,  69 => 7,  66 => 19,  54 => 7,  44 => 10,  230 => 80,  226 => 56,  203 => 12,  193 => 47,  188 => 33,  182 => 54,  178 => 44,  168 => 49,  163 => 50,  160 => 38,  155 => 37,  148 => 45,  145 => 52,  140 => 38,  136 => 28,  125 => 47,  120 => 23,  113 => 27,  101 => 37,  92 => 11,  89 => 19,  85 => 18,  73 => 15,  62 => 13,  59 => 8,  56 => 15,  41 => 11,  126 => 29,  119 => 30,  111 => 24,  106 => 41,  98 => 63,  93 => 21,  86 => 18,  70 => 20,  60 => 9,  28 => 2,  36 => 5,  114 => 36,  104 => 20,  91 => 16,  80 => 17,  63 => 11,  58 => 17,  40 => 6,  34 => 4,  45 => 5,  61 => 17,  55 => 11,  48 => 10,  39 => 8,  35 => 4,  31 => 4,  26 => 4,  21 => 2,  46 => 9,  29 => 3,  57 => 12,  50 => 10,  47 => 9,  38 => 3,  33 => 6,  49 => 9,  32 => 6,  246 => 94,  236 => 28,  232 => 54,  225 => 63,  221 => 78,  216 => 68,  214 => 53,  211 => 50,  208 => 58,  205 => 48,  199 => 48,  196 => 50,  190 => 56,  179 => 58,  175 => 43,  172 => 42,  169 => 8,  162 => 39,  158 => 17,  153 => 69,  151 => 35,  147 => 33,  144 => 31,  141 => 35,  135 => 27,  129 => 34,  124 => 35,  117 => 26,  112 => 25,  90 => 19,  87 => 15,  82 => 12,  72 => 28,  68 => 13,  65 => 49,  52 => 11,  43 => 4,  37 => 7,  30 => 6,  27 => 5,  25 => 4,  24 => 2,  22 => 2,  19 => 1,);
    }
}
