<?php

/* AgentBundle:Ticket:newticket.html.twig */
class __TwigTemplate_8c5e7f30834c1049afde5768888683bf extends \Application\DeskPRO\Twig\Template
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
        $context["agentui"] = $this->env->loadTemplate("AgentBundle:Common:agent-macros.html.twig");
        // line 2
        echo "<script>
pageMeta.fragmentClass = 'DeskPRO.Agent.PageFragment.Page.NewTicket';
pageMeta.title = '";
        // line 4
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.new_ticket");
        echo "';
pageMeta.ticket_id = 0;

pageMeta.labelsAutocompleteUrl        = '";
        // line 7
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_ajax_labels_autocomplete", array("label_type" => "ticket")), "html", null, true);
        echo "';
pageMeta.uploadAttachUrl              = '";
        // line 8
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_accept_upload"), "html", null, true);
        echo "';

pageMeta.auto_start_bill = ";
        // line 10
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.billing_auto_timer_new"), "method")) {
            echo "true";
        } else {
            echo "false";
        }
        echo ";

";
        // line 12
        $context["baseId"] = $this->env->getExtension('deskpro_templating')->elUid();
        // line 13
        echo "pageMeta.baseId = '";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "';
</script>
";
        // line 15
        if (isset($context["agentui"])) { $_agentui_ = $context["agentui"]; } else { $_agentui_ = null; }
        echo $_agentui_->getscroll_containers("page-new-ticket page-new-content");
        echo "

<a class=\"tab-anchor\" href=\"#\">&nbsp;</a>

<form id=\"";
        // line 19
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_newticket\" class=\"keybound-submit\">

<input type=\"hidden\" name=\"for_comment_type\" id=\"";
        // line 21
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_for_comment_type\" value=\"\" />
<input type=\"hidden\" name=\"for_comment_id\" id=\"";
        // line 22
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_for_comment_id\" value=\"\" />
<input type=\"hidden\" name=\"for_chat_id\" id=\"";
        // line 23
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_for_chat_id\" value=\"\" />
<input type=\"hidden\" name=\"parent_ticket_id\" id=\"";
        // line 24
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_parent_ticket_id\" value=\"";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_ticket_, "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_ticket_, "id"), "0")) : ("0")), "html", null, true);
        echo "\" />
<input type=\"hidden\" id=\"";
        // line 25
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_action\" name=\"options[action]\" value=\"\" />

<div class=\"pending-info comment\" style=\"display: none;\">
\t<i class=\"reset\"></i>
\t<h3>";
        // line 29
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.creating_a_ticket_for");
        echo ": <span id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_comment_title\"></span></h3>
\t<div class=\"pending-item pending-ticket\">
\t\t<div class=\"about\">";
        // line 31
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.commented_on");
        echo ": <a data-route=\"\" class=\"with-route ticket-link\" id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_comment_object_link\"></a></div>
\t\t<div class=\"post-action\">
\t\t\t<select name=\"comment_action\" id=\"";
        // line 33
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_comment_action\">
\t\t\t\t<option value=\"delete\">";
        // line 34
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.delete");
        echo "</option>
\t\t\t\t<option value=\"approve\">";
        // line 35
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.approve");
        echo "</option>
\t\t\t</select>
\t\t\t";
        // line 37
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.coment_after_ticket_created");
        echo "
\t\t</div>
\t</div>
</div>

<div class=\"errors-section\" id=\"";
        // line 42
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_error_section\" style=\"display: none;\">
\t<div style=\"display:none;\" class=\"error-message subject\">&bull; ";
        // line 43
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.please_enter_subject");
        echo "</div>
\t<div style=\"display:none;\" class=\"error-message person_id person_no_user\">&bull; ";
        // line 44
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.please_choose_or_create_user");
        echo "</div>
\t<div style=\"display:none;\" class=\"error-message person_email_address\">&bull; ";
        // line 45
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.error_email_address");
        echo "</div>
\t<div style=\"display:none;\" class=\"error-message person_email_address_gateway\">&bull; ";
        // line 46
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.email_is_ticket_account");
        echo "</div>
\t<div style=\"display:none;\" class=\"error-message department_id\">&bull; ";
        // line 47
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.select_a_department");
        echo "</div>
\t<div style=\"display:none;\" class=\"error-message message\">&bull; ";
        // line 48
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.enter_a_message");
        echo "</div>
\t<div style=\"display:none;\" class=\"error-message person_disabled\">&bull; ";
        // line 49
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.user_is_disabled");
        echo "</div>
\t<div style=\"display:none;\" class=\"error-message free\" id=\"";
        // line 50
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_freemessage\"></div>
</div>

<div class=\"pending-info chat\" style=\"display: none; padding: 7px; background: #FFFEFE; border-bottom: 1px solid #9CA7B1; font-size: 11px;\">
\t";
        // line 54
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.linking_with_chat");
        echo " <span id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_chat_title\"></span>
</div>

<div class=\"profile-box-container reply-box-wrap\" style=\"position: relative\">
\t<header>
\t\t<div class=\"controls\">
\t\t\t<button class=\"switch-user\" id=\"";
        // line 60
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_switch_user\" style=\"display: none\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.change_user");
        echo "</button>
\t\t</div>
\t\t<nav data-element-handler=\"DeskPRO.ElementHandler.SimpleTabs\"><ul>
\t\t\t<li data-tab-for=\"#";
        // line 63
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_user_section\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.user");
        echo "</li>
\t\t\t<li data-tab-for=\"#";
        // line 64
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_cc_section\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ccs");
        echo "</li>
\t\t</ul></nav>
\t</header>
\t<section id=\"";
        // line 67
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_user_section\" class=\"user-section\">
\t\t<div id=\"";
        // line 68
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_choose_user\" style=\"padding: 10px\">
\t\t\t<div
\t\t\t\tid=\"";
        // line 70
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_user_searchbox\"
\t\t\t\tdata-element-handler=\"DeskPRO.Agent.ElementHandler.PersonSearchBox\"
\t\t\t\tdata-search-url=\"";
        // line 72
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_peoplesearch_performquick", array("format" => "json", "limit" => 10, "start_with" => "a", "with_agents" => 1)), "html", null, true);
        echo "\"
\t\t\t\tdata-highlight-term=\"1\"
\t\t\t\tdata-touch-focus=\"1\"
\t\t\t\tdata-search-param=\"term\"
\t\t\t\tdata-position-bound=\"#";
        // line 76
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_userselect\"
\t\t\t>
\t\t\t\t<input type=\"text\" name=\"newticket[person_input_choice]\" id=\"";
        // line 78
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_userselect\" class=\"select-user term\" placeholder=\"";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.choose_person_for_ticket");
        echo "\" />
\t\t\t\t<input type=\"hidden\" name=\"newticket[person][id]\" value=\"";
        // line 79
        if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_person_, "id"), "html", null, true);
        echo "\" class=\"person-id\" id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_person_id\" />

\t\t\t\t<script type=\"text/x-deskpro-tmpl\" class=\"user-row-tpl\">
\t\t\t\t\t<li>
\t\t\t\t\t\t<a>
\t\t\t\t\t\t<span class=\"user-name\"></span>
\t\t\t\t\t\t<address>&lt;<span class=\"user-email\"></span>&gt;</address>
\t\t\t\t\t\t<div class=\"bound-fade\"></div>
\t\t\t\t\t</a></li>
\t\t\t\t</script>
\t\t\t\t<div class=\"person-search-box\" style=\"display: none\">
\t\t\t\t\t<section>
\t\t\t\t\t\t<ul class=\"results-list\">

\t\t\t\t\t\t</ul>
\t\t\t\t\t\t<footer>
\t\t\t\t\t\t\t<span class=\"create-user\">";
        // line 95
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.create_a_new_person");
        echo "</span>
\t\t\t\t\t\t</footer>
\t\t\t\t\t</section>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<div id=\"";
        // line 101
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_user_choice\">

\t\t</div>
\t</section>
\t<section id=\"";
        // line 105
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_cc_section\" style=\"padding: 10px; display: none;\">
\t\t<div class=\"cc-input\">
\t\t\t<div
\t\t\t\tid=\"";
        // line 108
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_user_ccbox\"
\t\t\t\tdata-element-handler=\"DeskPRO.Agent.ElementHandler.PersonSearchBox\"
\t\t\t\tdata-search-url=\"";
        // line 110
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_peoplesearch_performquick", array("format" => "json", "limit" => 10, "start_with" => "a")), "html", null, true);
        echo "\"
\t\t\t\tdata-highlight-term=\"1\"
\t\t\t\tdata-touch-focus=\"1\"
\t\t\t\tdata-search-param=\"term\"
\t\t\t\tdata-position-bound=\"#";
        // line 114
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_userccselect\"
\t\t\t>
\t\t\t\t<input type=\"text\" id=\"";
        // line 116
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_userccselect\" class=\"cc-user term\" placeholder=\"";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.search_for_people_to_add");
        echo "\" />

\t\t\t\t<script type=\"text/x-deskpro-tmpl\" class=\"user-row-tpl\">
\t\t\t\t\t<li>
\t\t\t\t\t\t<a>
\t\t\t\t\t\t<span class=\"user-name\"></span>
\t\t\t\t\t\t<address>&lt;<span class=\"user-email\"></span>&gt;</address>
\t\t\t\t\t\t<div class=\"bound-fade\"></div>
\t\t\t\t\t</a></li>
\t\t\t\t</script>
\t\t\t\t<div class=\"person-search-box\" style=\"display: none\">
\t\t\t\t\t<section>
\t\t\t\t\t\t<ul class=\"results-list\">

\t\t\t\t\t\t</ul>
\t\t\t\t\t\t<footer>
\t\t\t\t\t\t\t<span class=\"create-user\">";
        // line 132
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.create_a_new_person");
        echo "</span>
\t\t\t\t\t\t</footer>
\t\t\t\t\t</section>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<ul id=\"";
        // line 138
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_cc_list\" class=\"cc-list\"></ul>
\t</section>
</div>

<div class=\"profile-box-container reply-box-wrap\" style=\"position: relative\">
\t<header>
\t\t<h4>";
        // line 144
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.properties");
        echo "</h4>
\t</header>
\t<section class=\"ticketreply\">
\t\t<article id=\"";
        // line 147
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_headerbox_box_props\" class=\"headerbox\">
\t\t\t<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" class=\"field-holders-table\" id=\"";
        // line 148
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_fields_container\">
\t\t\t\t<tbody>
\t\t\t\t\t<th width=\"80\">";
        // line 150
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.department");
        echo "</th>
\t\t\t\t\t<td>
\t\t\t\t\t\t";
        // line 152
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        $this->env->loadTemplate("AgentBundle:Common:select-department.html.twig")->display(array_merge($context, array("name" => "newticket[department_id]", "id" => ($_baseId_ . "_dep"), "departments" => $this->getAttribute($this->getAttribute($_app_, "departments"), "getPersonDepartments", array(0 => $this->getAttribute($_app_, "user"), 1 => "tickets", 2 => array(), 3 => "assign"), "method"), "selected" => $this->getAttribute($_ticket_, "department_id"), "with_blank" => true)));
        // line 159
        echo "\t\t\t\t\t</td>
\t\t\t\t</tbody>

\t\t\t\t";
        // line 162
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_ticket_category"), "method")) {
            // line 163
            echo "\t\t\t\t\t<tbody class=\"col fieldprop ticket-field item-on ticket_category inline-field\" style=\"display: none;\">
\t\t\t\t\t\t<th>";
            // line 164
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.category");
            echo "</th>
\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t<div>
\t\t\t\t\t\t\t\t";
            // line 167
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            if (isset($context["ticket_options"])) { $_ticket_options_ = $context["ticket_options"]; } else { $_ticket_options_ = null; }
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            $this->env->loadTemplate("AgentBundle:Common:select-category.html.twig")->display(array_merge($context, array("name" => "newticket[category_id]", "id" => ($_baseId_ . "_cat"), "categories" => $this->getAttribute($_ticket_options_, "ticket_categories_hierarchy"), "selected" => $this->getAttribute($_ticket_, "category_id"), "with_blank" => true)));
            // line 174
            echo "\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</td>
\t\t\t\t\t</tbody>
\t\t\t\t";
        }
        // line 178
        echo "
\t\t\t\t";
        // line 179
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_ticket_priority"), "method")) {
            // line 180
            echo "\t\t\t\t\t<tbody class=\"col fieldprop ticket-field item-on ticket_priority inline-field\" style=\"display: none;\">
\t\t\t\t\t\t<th>";
            // line 181
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.priority");
            echo "</th>
\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t";
            // line 183
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if (isset($context["ticket_options"])) { $_ticket_options_ = $context["ticket_options"]; } else { $_ticket_options_ = null; }
            $this->env->loadTemplate("AgentBundle:Common:select-priority.html.twig")->display(array_merge($context, array("name" => "newticket[priority_id]", "id" => ($_baseId_ . "_pri"), "add_classname" => "ticket_priority", "selected" => $this->getAttribute($_ticket_, "priority_id"), "priorities" => $this->getAttribute($_ticket_options_, "ticket_priorities"), "with_blank" => true)));
            // line 191
            echo "\t\t\t\t\t\t</td>
\t\t\t\t\t</tbody>
\t\t\t\t";
        }
        // line 194
        echo "
\t\t\t\t";
        // line 195
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_ticket_workflow"), "method")) {
            // line 196
            echo "\t\t\t\t\t<tbody class=\"col fieldprop ticket-field item-on ticket_workflow inline-field\" style=\"clear:both;display: none;\">
\t\t\t\t\t\t<th>";
            // line 197
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.workflow");
            echo "</th>
\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t";
            // line 199
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if (isset($context["ticket_options"])) { $_ticket_options_ = $context["ticket_options"]; } else { $_ticket_options_ = null; }
            $this->env->loadTemplate("AgentBundle:Common:select-workflow.html.twig")->display(array_merge($context, array("name" => "newticket[workflow_id]", "id" => ($_baseId_ . "_work"), "add_classname" => "ticket_workflow", "selected" => $this->getAttribute($_ticket_, "workflow_id"), "workflows" => $this->getAttribute($_ticket_options_, "ticket_workflows"), "with_blank" => true)));
            // line 207
            echo "\t\t\t\t\t\t</td>
\t\t\t\t\t</tbody>
\t\t\t\t";
        }
        // line 210
        echo "
\t\t\t\t";
        // line 211
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_product"), "method")) {
            // line 212
            echo "\t\t\t\t\t<tbody class=\"col fieldprop ticket-field item-on ticket_product inline-field\" style=\"clear:both;display: none;\">
\t\t\t\t\t\t<th>";
            // line 213
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.product");
            echo "</th>
\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t";
            // line 215
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            if (isset($context["ticket_options"])) { $_ticket_options_ = $context["ticket_options"]; } else { $_ticket_options_ = null; }
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            $this->env->loadTemplate("AgentBundle:Common:select-product.html.twig")->display(array_merge($context, array("name" => "newticket[product_id]", "id" => ($_baseId_ . "_prod"), "add_classname" => "ticket_product", "products" => $this->getAttribute($_ticket_options_, "products_hierarchy"), "selected" => $this->getAttribute($_ticket_, "product_id"), "with_blank" => true)));
            // line 223
            echo "\t\t\t\t\t\t</td>
\t\t\t\t\t</tbody>
\t\t\t\t";
        }
        // line 226
        echo "
\t\t\t\t";
        // line 227
        if (isset($context["custom_fields"])) { $_custom_fields_ = $context["custom_fields"]; } else { $_custom_fields_ = null; }
        if ($_custom_fields_) {
            // line 228
            echo "\t\t\t\t\t";
            if (isset($context["custom_fields"])) { $_custom_fields_ = $context["custom_fields"]; } else { $_custom_fields_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_custom_fields_);
            foreach ($context['_seq'] as $context["_key"] => $context["f"]) {
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                if (($this->getAttribute($this->getAttribute($_f_, "field_def"), "getTypeName", array(), "method") != "hidden")) {
                    // line 229
                    echo "\t\t\t\t\t\t<tbody class=\"col fieldprop ticket-field item-on ticket-field-";
                    if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_f_, "field_def"), "id"), "html", null, true);
                    echo " item-on-";
                    if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_f_, "field_def"), "id"), "html", null, true);
                    echo " inline-field\" style=\"clear:both;display: none;\">
\t\t\t\t\t\t\t<th>";
                    // line 230
                    if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_f_, "title"), "html", null, true);
                    echo "</th>
\t\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t\t";
                    // line 232
                    if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                    echo $this->env->getExtension('deskpro_templating')->renderCustomFieldForm($_f_);
                    echo "
\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t</tbody>
\t\t\t\t\t";
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['f'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 236
            echo "\t\t\t\t";
        }
        // line 237
        echo "\t\t\t</table>
\t\t</article>
\t</section>
</div>

";
        // line 242
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.enable_billing"), "method") && $this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.billing_on_new"), "method"))) {
            // line 243
            echo "<div class=\"profile-box-container reply-box-wrap\" style=\"position: relative\">
\t<header>
\t\t<h4>";
            // line 245
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.billing");
            echo "</h4>
\t</header>
\t<section class=\"ticketreply\">
\t\t<article id=\"";
            // line 248
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_headerbox_box_billing\" class=\"headerbox\">
\t\t\t<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" class=\"field-holders-table billing-form\" id=\"";
            // line 249
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_billing_form\">
\t\t\t<tr>
\t\t\t\t<th width=\"80\">";
            // line 251
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.billing_charge");
            echo "</th>
\t\t\t\t<td>
\t\t\t\t\t<ul class=\"billing-types\">
\t\t\t\t\t\t<li>
\t\t\t\t\t\t\t<label><input type=\"radio\" name=\"";
            // line 255
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_billing_type\" value=\"amount\" />";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.billing_amount");
            echo ":</label>
\t\t\t\t\t\t\t<input type=\"text\" id=\"";
            // line 256
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_billing_amount\" name=\"newticket[billing_amount]\" size=\"7\" placeholder=\"0.00\" class=\"billing-form-amount\" /> ";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.billing_currency"), "method"), "html", null, true);
            echo "
\t\t\t\t\t\t</li>
\t\t\t\t\t\t<li>
\t\t\t\t\t\t\t<label><input type=\"radio\" name=\"";
            // line 259
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_billing_type\" value=\"time\" checked=\"checked\" />";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.billing_time");
            echo ":</label>
\t\t\t\t\t\t\t<span id=\"";
            // line 260
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_billing_inputs\" class=\"billing-form-time\">
\t\t\t\t\t\t\t\t<input type=\"text\" id=\"";
            // line 261
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_billing_hours\" name=\"newticket[billing_hours]\" size=\"2\" placeholder=\"H\" />:<input type=\"text\" id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_billing_minutes\" name=\"newticket[billing_minutes]\" size=\"2\" placeholder=\"M\" maxlength=\"2\" />:<input type=\"text\" id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_billing_seconds\" name=\"newticket[billing_seconds]\" size=\"2\" placeholder=\"S\" maxlength=\"2\" />
\t\t\t\t\t\t\t</span>
\t\t\t\t\t\t\t<span class=\"billing-form-buttons\">
\t\t\t\t\t\t\t\t<button class=\"clean-white\" id=\"";
            // line 264
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_billing_start\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.start");
            echo "</button>
\t\t\t\t\t\t\t\t<button class=\"clean-white\" id=\"";
            // line 265
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_billing_stop\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.pause");
            echo "</button>
\t\t\t\t\t\t\t\t<button class=\"clean-white\" id=\"";
            // line 266
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_billing_reset\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.reset");
            echo "</button>
\t\t\t\t\t\t\t</span>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t<input type=\"hidden\" id=\"";
            // line 269
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_billing_type_hidden\" name=\"newticket[billing_type]\" value=\"time\" />
\t\t\t\t\t</ul>
\t\t\t\t</td>
\t\t\t</tr>
\t\t\t<tr>
\t\t\t\t<th>";
            // line 274
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.comment");
            echo "</th>
\t\t\t\t<td><input type=\"text\" id=\"";
            // line 275
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_billing_comment\" name=\"newticket[billing_comment]\" style=\"width: 98%\" maxlength=\"255\" /></td>
\t\t\t</tr>
\t\t\t</table>
\t\t</article>
\t</section>
</div>
";
        }
        // line 282
        echo "
<div class=\"profile-box-container reply-box-wrap\" style=\"position: relative\" id=\"";
        // line 283
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_message_box\">
\t<div class=\"ticket-sending-overlay\" style=\"display: none\">
\t\t<strong>";
        // line 285
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.sending_your_message");
        echo "</strong>
\t</div>
\t<header>
\t\t<h4>";
        // line 288
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ticket");
        echo "</h4>
\t</header>
\t<section class=\"ticketreply\">
\t\t<article>
\t\t\t<section>
\t\t\t\t<div class=\"option-rows\">
\t\t\t\t\t<ul id=\"";
        // line 294
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_option_rows\">
\t\t\t\t\t\t<li class=\"with-select2\">
\t\t\t\t\t\t\t<label>
\t\t\t\t\t\t\t\t<span class=\"expander\" data-target=\"#";
        // line 297
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_cc_row\">+/-</span>
\t\t\t\t\t\t\t\t";
        // line 298
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.agent");
        echo ":
\t\t\t\t\t\t\t</label>
\t\t\t\t\t\t\t<div>
\t\t\t\t\t\t\t\t";
        // line 301
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((($this->getAttribute($this->getAttribute($_ticket_, "agent"), "id") == $this->getAttribute($this->getAttribute($_app_, "user"), "id")) || (!$this->getAttribute($_ticket_, "agent")))) {
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context["agent_id"] = $this->getAttribute($this->getAttribute($_app_, "user"), "id");
            // line 302
            echo "\t\t\t\t\t\t\t\t";
        } else {
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            $context["agent_id"] = $this->getAttribute($this->getAttribute($_ticket_, "agent"), "id");
        }
        // line 303
        echo "
\t\t\t\t\t\t\t\t<select name=\"newticket[agent_id]\" class=\"dpe_select\" data-style-type=\"icons\" data-select-icon-size=\"22\">
\t\t\t\t\t\t\t\t\t<option ";
        // line 305
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.new_assign"), "method"))) {
            echo "selected=\"selected\"";
        }
        echo " value=\"0\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.unassigned");
        echo "</option>
\t\t\t\t\t\t\t\t\t";
        // line 306
        if (isset($context["agents"])) { $_agents_ = $context["agents"]; } else { $_agents_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_agents_);
        foreach ($context['_seq'] as $context["_key"] => $context["agent"]) {
            // line 307
            echo "\t\t\t\t\t\t\t\t\t\t<option ";
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            if (isset($context["agent_id"])) { $_agent_id_ = $context["agent_id"]; } else { $_agent_id_ = null; }
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ((($this->getAttribute($_agent_, "id") == $_agent_id_) && ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.new_assign"), "method") == "assign"))) {
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
\t\t\t\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['agent'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 309
        echo "\t\t\t\t\t\t\t\t</select>

\t\t\t\t\t\t\t\t<select name=\"newticket[agent_team_id]\" id=\"";
        // line 311
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_agent_team_sel\" class=\"dpe_select x\" data-select-nogrouptitle=\"1\">
\t\t\t\t\t\t\t\t\t<option value=\"0\" ";
        // line 312
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.new_assignteam"), "method"))) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none");
        echo "</option>
\t\t\t\t\t\t\t\t\t";
        // line 313
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (twig_length_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_app_, "user"), "getAgent", array(), "method"), "getTeams", array(), "method"))) {
            // line 314
            echo "\t\t\t\t\t\t\t\t\t<optgroup label=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.your_teams");
            echo "\">
\t\t\t\t\t\t\t\t\t\t";
            // line 315
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($this->getAttribute($_app_, "user"), "getAgent", array(), "method"), "getTeams", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["team"]) {
                // line 316
                echo "\t\t\t\t\t\t\t\t\t\t\t<option value=\"";
                if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_team_, "id"), "html", null, true);
                echo "\" ";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                if ((($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.new_assignteam"), "method") == "assign") && (($this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent.ticket_default_team_id"), "method") == $this->getAttribute($_team_, "id")) || (!$this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent.ticket_default_team_id"), "method"))))) {
                    echo "selected=\"selected\"";
                }
                echo ">";
                if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_team_, "name"), "html", null, true);
                echo "</option>
\t\t\t\t\t\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['team'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 318
            echo "\t\t\t\t\t\t\t\t\t</optgroup>
\t\t\t\t\t\t\t\t\t";
        }
        // line 320
        echo "
\t\t\t\t\t\t\t\t\t";
        // line 321
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "agent_teams"), "getTeamNames", array(), "method"));
        foreach ($context['_seq'] as $context["id"] => $context["name"]) {
            // line 322
            echo "\t\t\t\t\t\t\t\t\t\t";
            if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if (!twig_in_filter($_id_, $this->getAttribute($this->getAttribute($this->getAttribute($_app_, "user"), "getAgent", array(), "method"), "getTeamIds", array(), "method"))) {
                // line 323
                echo "\t\t\t\t\t\t\t\t\t\t\t<option value=\"";
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                echo twig_escape_filter($this->env, $_id_, "html", null, true);
                echo "\">";
                if (isset($context["name"])) { $_name_ = $context["name"]; } else { $_name_ = null; }
                echo twig_escape_filter($this->env, $_name_, "html", null, true);
                echo "</option>
\t\t\t\t\t\t\t\t\t\t";
            }
            // line 325
            echo "\t\t\t\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['id'], $context['name'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 326
        echo "\t\t\t\t\t\t\t\t</select>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t<li id=\"";
        // line 329
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_cc_row\" class=\"cc-row is-hidden\" style=\"display: none;\">
\t\t\t\t\t\t\t<label style=\"padding-top: 6px;\">";
        // line 330
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.followers");
        echo ":</label>
\t\t\t\t\t\t\t<div style=\"margin-left: 65px;\">
\t\t\t\t\t\t\t\t<select name=\"add_followers[]\" id=\"";
        // line 332
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_followers_sel\" class=\"dpe_select dpe_select_noborder\" multiple=\"multiple\" data-style-type=\"icons\" data-select-width=\"auto\" data-select-icon-size=\"16\" data-placeholder=\"";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.add_follower");
        echo "\">
\t\t\t\t\t\t\t\t\t";
        // line 333
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "agents"), "getAgents", array(), "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["agent"]) {
            // line 334
            echo "\t\t\t\t\t\t\t\t\t\t";
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if (($this->getAttribute($_agent_, "id") != $this->getAttribute($this->getAttribute($_app_, "user"), "id"))) {
                // line 335
                echo "\t\t\t\t\t\t\t\t\t\t\t<option data-icon=\"";
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "getPictureUrl", array(0 => 16), "method"), "html", null, true);
                echo "\" value=\"";
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "id"), "html", null, true);
                echo "\">";
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "display_name"), "html", null, true);
                echo "</option>
\t\t\t\t\t\t\t\t\t\t";
            }
            // line 337
            echo "\t\t\t\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['agent'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 338
        echo "\t\t\t\t\t\t\t\t</select>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t<li id=\"";
        // line 341
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_actions_row\" class=\"actions-row\" style=\"display: none;\">
\t\t\t\t\t\t\t<label><span>";
        // line 342
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.actions");
        echo "</span></label>
\t\t\t\t\t\t\t<ul></ul>
\t\t\t\t\t\t\t<br class=\"clear\"/>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t";
        // line 346
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "TicketMessageTemplate"), "method"), "getTitles", array(), "method")) {
            // line 347
            echo "\t\t\t\t\t\t<li class=\"with-select2\" id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_message_template_holder_row\">
\t\t\t\t\t\t\t<label>";
            // line 348
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.template");
            echo ":</label>
\t\t\t\t\t\t\t<div id=\"";
            // line 349
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_message_template_holder\" style=\"visibility: hidden;\">
\t\t\t\t\t\t\t\t<div style=\"display: none\">
\t\t\t\t\t\t\t\t\t<select id=\"";
            // line 351
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_message_template_orig\">
\t\t\t\t\t\t\t\t\t\t<option value=\"0\">";
            // line 352
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.blank");
            echo "</option>
\t\t\t\t\t\t\t\t\t\t";
            // line 353
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "TicketMessageTemplate"), "method"), "getAll", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["tpl"]) {
                // line 354
                echo "\t\t\t\t\t\t\t\t\t\t\t<option value=\"";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_tpl_, "id"), "html", null, true);
                echo "\" class=\"department_";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_tpl_, "department_id"), "html", null, true);
                echo "\">";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo twig_escape_filter($this->env, (($this->getAttribute($_tpl_, "title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_tpl_, "title"), ("Untitled #" . $this->getAttribute($_tpl_, "id")))) : (("Untitled #" . $this->getAttribute($_tpl_, "id")))), "html", null, true);
                echo "</option>
\t\t\t\t\t\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tpl'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 356
            echo "\t\t\t\t\t\t\t\t\t</select>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t<select id=\"";
            // line 358
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_message_template\" name=\"using_template\">
\t\t\t\t\t\t\t\t\t<option value=\"0\">";
            // line 359
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.blank");
            echo "</option>
\t\t\t\t\t\t\t\t\t";
            // line 360
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "TicketMessageTemplate"), "method"), "getAll", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["tpl"]) {
                // line 361
                echo "\t\t\t\t\t\t\t\t\t\t<option value=\"";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_tpl_, "id"), "html", null, true);
                echo "\" class=\"department_";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_tpl_, "department_id"), "html", null, true);
                echo "\">";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo twig_escape_filter($this->env, (($this->getAttribute($_tpl_, "title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_tpl_, "title"), ("Untitled #" . $this->getAttribute($_tpl_, "id")))) : (("Untitled #" . $this->getAttribute($_tpl_, "id")))), "html", null, true);
                echo "</option>
\t\t\t\t\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tpl'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 363
            echo "\t\t\t\t\t\t\t\t</select>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t";
        }
        // line 367
        echo "\t\t\t\t\t\t<li class=\"subject-row\">
\t\t\t\t\t\t\t<label>";
        // line 368
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.subject");
        echo ":</label>
\t\t\t\t\t\t\t<div>
\t\t\t\t\t\t\t\t<input type=\"text\" name=\"newticket[subject]\" class=\"subject\" id=\"";
        // line 370
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_subject\" value=\"";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ($this->getAttribute($_ticket_, "id")) {
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "subject"), "html", null, true);
        }
        echo "\" />
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t<li id=\"";
        // line 373
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_attach_row\" class=\"attach-row is-hidden\" style=\"display: none\">
\t\t\t\t\t\t\t<label><span>";
        // line 374
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.attachments");
        echo "</span></label>
\t\t\t\t\t\t\t<div>
\t\t\t\t\t\t\t\t<ul class=\"files\"></ul>
\t\t\t\t\t\t\t\t<br class=\"clear\"/>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</li>
\t\t\t\t\t</ul>
\t\t\t\t\t<div class=\"drop-file-zone\"><h1>";
        // line 381
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.drop_here_to_attach");
        echo "</h1></div>
\t\t\t\t</div>
\t\t\t\t<div class=\"input-wrap unreset\">
\t\t\t\t\t<textarea id=\"";
        // line 384
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_message\" name=\"newticket[message]\">";
        if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_message_, "getMessageHtml", array(), "method"), "html", null, true);
        echo "</textarea>
\t\t\t\t\t<textarea id=\"";
        // line 385
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_signature_value\" class=\"signature-value\" style=\"display: none;\">";
        if (isset($context["agent_signature"])) { $_agent_signature_ = $context["agent_signature"]; } else { $_agent_signature_ = null; }
        echo twig_escape_filter($this->env, $_agent_signature_, "html", null, true);
        echo "</textarea>
\t\t\t\t\t<textarea id=\"";
        // line 386
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_signature_value_html\" class=\"signature-value-html\" style=\"display: none;\">";
        if (isset($context["agent_signature_html"])) { $_agent_signature_html_ = $context["agent_signature_html"]; } else { $_agent_signature_html_ = null; }
        echo twig_escape_filter($this->env, $_agent_signature_html_, "html", null, true);
        echo "</textarea>
\t\t\t\t\t<input type=\"hidden\" id=\"";
        // line 387
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_is_html_reply\" name=\"newticket[is_html_reply]\" value=\"0\" />
\t\t\t\t\t<div class=\"drop-file-zone drop-file-zone-rte\"><h1>";
        // line 388
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.drop_here_to_insert_image");
        echo "</h1></div>
\t\t\t\t</div>
\t\t\t</section>
\t\t</article>
\t</section>
\t<footer id=\"";
        // line 393
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_message_footer\">
\t\t<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr>
\t\t\t<td valign=\"middle\" style=\"vertical-align: middle;\">
\t\t\t\t<div id=\"";
        // line 396
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_send_btn\">
\t\t\t\t\t<div class=\"dp-btn-group dp-dropup\" id=\"";
        // line 397
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_reply_btn_group\">
\t\t\t\t\t\t<a class=\"dp-btn dp-btn-primary submit-trigger\">
\t\t\t\t\t\t\t<i class=\"icon-share-alt\"></i>&nbsp;
\t\t\t\t\t\t\t";
        // line 400
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.new_status"), "method") == "awaiting_user")) {
            // line 401
            echo "\t\t\t\t\t\t\t\t<span id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_reply_as_type\" data-type=\"awaiting_user\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.send_reply_as_x", array("status" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_awaiting_user")));
            echo "</span>
\t\t\t\t\t\t\t";
        } elseif (($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.new_status"), "method") == "awaiting_agent")) {
            // line 403
            echo "\t\t\t\t\t\t\t\t<span id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_reply_as_type\" data-type=\"awaiting_agent\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.send_reply_as_x", array("status" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_awaiting_agent")));
            echo "</span>
\t\t\t\t\t\t\t";
        } elseif (($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.new_status"), "method") == "resolved")) {
            // line 405
            echo "\t\t\t\t\t\t\t\t<span id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_reply_as_type\" data-type=\"resolved\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.send_reply_as_x", array("status" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_resolved")));
            echo "</span>
\t\t\t\t\t\t\t";
        } else {
            // line 407
            echo "\t\t\t\t\t\t\t\t<span id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_reply_as_type\" data-type=\"awaiting_user\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.send_reply_as_x", array("status" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_awaiting_user")));
            echo "</span>
\t\t\t\t\t\t\t";
        }
        // line 409
        echo "\t\t\t\t\t\t</a>
\t\t\t\t\t\t<a class=\"dp-btn dp-btn-primary dp-dropdown-toggle status-menu-trigger\"><span class=\"dp-caret dp-caret-up\"></span></a>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t<div id=\"";
        // line 413
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_send_loading\" style=\"display: none;\">
\t\t\t\t\t<span class=\"flat-spinner\"></span>
\t\t\t\t</div>
\t\t\t</td><td valign=\"middle\" style=\"vertical-align: middle;\">
\t\t\t\t<div style=\"padding-top: 1px; line-height: 100%; margin-left: 10px; float:left;\">
\t\t\t\t\t<label class=\"hide-note\">
\t\t\t\t\t\t<input type=\"checkbox\" name=\"options[notify_user]\" ";
        // line 419
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.new_default_send_user_notify"), "method") || $this->getAttribute($_ticket_, "id"))) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\t\t\t<span class=\"opt-label-text\">
\t\t\t\t\t\t\t";
        // line 421
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.email_user");
        echo "
\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"";
        // line 422
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.email_user_newticket_info");
        echo "\"></span>
\t\t\t\t\t\t</span>
\t\t\t\t\t</label>
\t\t\t\t</div>
\t\t\t\t<div style=\"padding-top: 1px; line-height: 100%; margin-left: 10px; float:left;\">
\t\t\t\t\t<label>
\t\t\t\t\t\t<input type=\"checkbox\" id=\"";
        // line 428
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_opt_open_tab\" name=\"options[open_tab]\" checked=\"checked\" />
\t\t\t\t\t\t<span class=\"opt-label-text\">
\t\t\t\t\t\t\t";
        // line 430
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.open_tab");
        echo "
\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"";
        // line 431
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.open_tab_info");
        echo "\"></span>
\t\t\t\t\t\t</span>
\t\t\t\t\t</label>
\t\t\t\t</div>
\t\t\t</td>
\t\t</tr></table>
\t</footer>
</div>

<div class=\"dp-menu replybox\" id=\"";
        // line 440
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_status_menu\" style=\"min-width: 225px;\">
\t<section>
\t\t<div class=\"dp-menu-area small\">
\t\t\t<input type=\"text\" class=\"label-input macro-filter\" placeholder=\"";
        // line 443
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.apply_a_macro");
        echo "\" />
\t\t\t<ul class=\"macro-list\">
\t\t\t\t";
        // line 445
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($this->getAttribute($_app_, "user"), "getHelper", array(0 => "Agent"), "method"), "getMacros", array(), "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["macro"]) {
            // line 446
            echo "\t\t\t\t\t<li class=\"res-ticketmacro-";
            if (isset($context["macro"])) { $_macro_ = $context["macro"]; } else { $_macro_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_macro_, "id"), "html", null, true);
            echo "\" data-type=\"macro:";
            if (isset($context["macro"])) { $_macro_ = $context["macro"]; } else { $_macro_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_macro_, "id"), "html", null, true);
            echo "\" data-label=\"";
            if (isset($context["macro"])) { $_macro_ = $context["macro"]; } else { $_macro_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.send_newticket_and_apply_x", array("name" => $this->getAttribute($_macro_, "title")));
            echo "\" data-get-macro-url=\"";
            if (isset($context["macro"])) { $_macro_ = $context["macro"]; } else { $_macro_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_ticket_ajax_get_macro", array("ticket_id" => "0", "macro_id" => $this->getAttribute($_macro_, "id"), "macro_reply_context" => 1)), "html", null, true);
            echo "\">
\t\t\t\t\t\t<div class=\"on-icon\"><i class=\"icon-ok\"></i></div>
\t\t\t\t\t\t<span class=\"macro-title\">";
            // line 448
            if (isset($context["macro"])) { $_macro_ = $context["macro"]; } else { $_macro_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_macro_, "title"), "html", null, true);
            echo "</span>
\t\t\t\t\t</li>
\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['macro'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 451
        echo "\t\t\t</ul>
\t\t</div>
\t</section>
\t<section>
\t\t<div class=\"dp-menu-area dp-menu-area-primary\">
\t\t\t<label>";
        // line 456
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.newticket_and_set_status");
        echo "</label>
\t\t\t<ul>
\t\t\t\t<li data-type=\"awaiting_user\" data-label=\"";
        // line 458
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.send_reply_as_x", array("status" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_awaiting_user")));
        echo "\">
\t\t\t\t\t<div class=\"on-icon\"><i class=\"icon-ok\"></i></div>
\t\t\t\t\t";
        // line 460
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.kbd_awaiting_user");
        echo "
\t\t\t\t</li>
\t\t\t\t<li data-type=\"awaiting_agent\" data-label=\"";
        // line 462
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.send_reply_as_x", array("status" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_awaiting_agent")));
        echo "\">
\t\t\t\t\t<div class=\"on-icon\"><i class=\"icon-ok\"></i></div>
\t\t\t\t\t";
        // line 464
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.kbd_awaiting_agent");
        echo "
\t\t\t\t</li>
\t\t\t\t<li data-type=\"resolved\" data-label=\"";
        // line 466
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.send_reply_as_x", array("status" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_resolved")));
        echo "\">
\t\t\t\t\t<div class=\"on-icon\"><i class=\"icon-ok\"></i></div>
\t\t\t\t\t";
        // line 468
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.kbd_resolved");
        echo "
\t\t\t\t</li>
\t\t\t</ul>
\t\t</div>
\t</section>
</div>

";
        // line 475
        $this->env->loadTemplate("AgentBundle:Common:attach-row-tmpl.html.twig")->display(array_merge($context, array("formname" => "newticket[attach][]")));
        // line 476
        echo "
</form>

";
        // line 479
        if (isset($context["agentui"])) { $_agentui_ = $context["agentui"]; } else { $_agentui_ = null; }
        echo $_agentui_->getscroll_containers_end();
    }

    public function getTemplateName()
    {
        return "AgentBundle:Ticket:newticket.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1264 => 464,  1259 => 462,  1227 => 448,  1211 => 446,  1399 => 568,  1387 => 560,  1358 => 555,  1334 => 553,  1316 => 529,  1298 => 520,  1195 => 478,  1148 => 464,  1120 => 453,  1117 => 452,  1093 => 440,  788 => 316,  612 => 203,  1394 => 446,  1385 => 442,  1382 => 441,  1369 => 433,  1363 => 432,  1360 => 431,  1350 => 429,  1305 => 523,  1300 => 409,  1281 => 404,  1277 => 403,  1265 => 398,  1255 => 396,  1247 => 393,  1234 => 387,  1202 => 375,  1199 => 374,  1187 => 372,  1162 => 365,  1136 => 461,  1128 => 352,  1122 => 350,  1069 => 332,  968 => 387,  846 => 250,  1183 => 449,  1132 => 460,  1097 => 341,  957 => 356,  907 => 277,  875 => 263,  653 => 236,  1329 => 405,  1309 => 401,  1303 => 399,  1297 => 398,  1292 => 397,  1280 => 394,  1249 => 458,  1237 => 451,  1205 => 409,  1200 => 408,  1194 => 440,  1178 => 430,  1175 => 447,  1170 => 375,  1135 => 362,  1105 => 428,  1078 => 335,  1068 => 388,  1048 => 417,  961 => 358,  922 => 280,  750 => 245,  842 => 263,  1038 => 319,  904 => 322,  882 => 301,  831 => 267,  860 => 314,  790 => 284,  733 => 296,  707 => 283,  744 => 220,  873 => 349,  824 => 323,  762 => 230,  713 => 235,  578 => 158,  2739 => 1153,  2735 => 1152,  2732 => 1151,  2725 => 1146,  2712 => 1144,  2707 => 1143,  2692 => 1131,  2688 => 1130,  2683 => 1127,  2680 => 1126,  2677 => 1125,  2671 => 1121,  2658 => 1119,  2653 => 1118,  2645 => 1113,  2641 => 1112,  2636 => 1109,  2632 => 1108,  2629 => 1107,  2626 => 1106,  2606 => 1089,  2602 => 1088,  2598 => 1087,  2594 => 1086,  2590 => 1085,  2586 => 1084,  2572 => 1073,  2568 => 1072,  2564 => 1071,  2560 => 1070,  2556 => 1069,  2552 => 1068,  2537 => 1056,  2533 => 1055,  2529 => 1054,  2525 => 1053,  2521 => 1052,  2503 => 1040,  2499 => 1039,  2495 => 1038,  2491 => 1037,  2487 => 1036,  2483 => 1035,  2469 => 1024,  2465 => 1023,  2461 => 1022,  2457 => 1021,  2453 => 1020,  2449 => 1019,  2426 => 999,  2422 => 998,  2418 => 997,  2414 => 996,  2410 => 995,  2406 => 994,  2392 => 983,  2388 => 982,  2384 => 981,  2380 => 980,  2376 => 979,  2372 => 978,  2358 => 967,  2354 => 966,  2350 => 965,  2346 => 964,  2342 => 963,  2338 => 962,  2324 => 951,  2320 => 950,  2316 => 949,  2312 => 948,  2308 => 947,  2304 => 946,  2295 => 939,  2282 => 937,  2277 => 936,  2269 => 931,  2265 => 930,  2260 => 927,  2256 => 926,  2253 => 925,  2230 => 901,  2220 => 899,  2216 => 898,  2211 => 896,  2208 => 895,  2198 => 893,  2194 => 892,  2189 => 890,  2184 => 889,  2177 => 885,  2173 => 884,  2164 => 877,  2151 => 875,  2146 => 874,  2143 => 873,  2141 => 864,  2134 => 860,  2130 => 859,  2122 => 853,  2120 => 852,  2113 => 848,  2105 => 846,  2096 => 839,  2083 => 837,  2078 => 836,  2073 => 834,  2067 => 830,  2062 => 828,  2058 => 827,  2054 => 826,  2050 => 825,  2045 => 824,  2038 => 822,  2034 => 821,  2022 => 814,  2009 => 812,  1999 => 809,  1993 => 805,  1988 => 803,  1984 => 802,  1980 => 801,  1976 => 800,  1971 => 799,  1968 => 798,  1964 => 797,  1960 => 796,  1954 => 793,  1946 => 788,  1942 => 787,  1938 => 786,  1932 => 782,  1927 => 780,  1919 => 778,  1915 => 777,  1911 => 776,  1906 => 775,  1903 => 774,  1899 => 773,  1895 => 772,  1886 => 768,  1883 => 764,  1871 => 759,  1866 => 756,  1863 => 755,  1842 => 752,  1825 => 749,  1818 => 748,  1815 => 747,  1800 => 743,  1793 => 741,  1772 => 729,  1761 => 721,  1757 => 720,  1751 => 717,  1740 => 709,  1719 => 697,  1715 => 696,  1686 => 673,  1676 => 669,  1665 => 661,  1661 => 660,  1645 => 651,  1638 => 649,  1630 => 645,  1625 => 642,  1622 => 641,  1598 => 637,  1577 => 634,  1558 => 629,  1549 => 627,  1546 => 626,  1535 => 618,  1531 => 617,  1514 => 606,  1510 => 605,  1504 => 602,  1493 => 594,  1483 => 590,  1464 => 580,  1458 => 577,  1443 => 568,  1437 => 565,  1426 => 557,  1405 => 545,  1401 => 544,  1395 => 541,  1390 => 538,  1381 => 533,  1373 => 531,  1348 => 521,  1343 => 520,  1335 => 515,  1321 => 510,  1318 => 549,  1299 => 503,  1294 => 407,  1282 => 496,  1269 => 466,  1260 => 397,  1240 => 478,  1221 => 484,  1216 => 378,  1210 => 410,  1206 => 445,  1193 => 467,  1189 => 466,  1155 => 468,  1150 => 419,  1022 => 312,  1006 => 404,  988 => 398,  969 => 392,  965 => 391,  921 => 349,  878 => 275,  866 => 349,  854 => 254,  819 => 322,  796 => 286,  1713 => 683,  1700 => 681,  1685 => 674,  1662 => 670,  1643 => 669,  1640 => 668,  1631 => 663,  1618 => 661,  1613 => 660,  1608 => 639,  1605 => 656,  1602 => 655,  1589 => 645,  1586 => 644,  1583 => 643,  1554 => 613,  1547 => 608,  1521 => 602,  1508 => 600,  1499 => 598,  1491 => 593,  1482 => 587,  1472 => 582,  1468 => 581,  1465 => 579,  1457 => 573,  1445 => 566,  1422 => 556,  1416 => 553,  1410 => 548,  1397 => 546,  1392 => 545,  1386 => 541,  1377 => 532,  1370 => 530,  1357 => 528,  1352 => 554,  1341 => 428,  1314 => 510,  1311 => 509,  1275 => 493,  1248 => 484,  1238 => 488,  1225 => 476,  1220 => 412,  1209 => 466,  1185 => 385,  1182 => 431,  1159 => 421,  1154 => 449,  1130 => 438,  1125 => 407,  1101 => 426,  1074 => 432,  1056 => 326,  1046 => 323,  1043 => 417,  1030 => 397,  1027 => 412,  947 => 361,  925 => 352,  913 => 324,  893 => 357,  881 => 267,  847 => 343,  829 => 336,  825 => 259,  1083 => 396,  995 => 399,  984 => 350,  963 => 292,  941 => 354,  851 => 367,  682 => 270,  1365 => 556,  1359 => 545,  1349 => 539,  1336 => 427,  1331 => 424,  1323 => 420,  1319 => 530,  1312 => 402,  1284 => 475,  1272 => 401,  1268 => 509,  1261 => 501,  1251 => 485,  1245 => 483,  1231 => 496,  1207 => 376,  1197 => 491,  1180 => 484,  1173 => 457,  1169 => 472,  1157 => 363,  1147 => 438,  1109 => 440,  1065 => 440,  1059 => 423,  1047 => 385,  1044 => 424,  1033 => 381,  1009 => 357,  991 => 363,  987 => 404,  973 => 294,  931 => 355,  924 => 282,  911 => 347,  906 => 81,  885 => 337,  872 => 335,  855 => 332,  749 => 264,  701 => 239,  594 => 180,  1163 => 422,  1143 => 440,  1087 => 420,  1077 => 433,  1051 => 325,  1037 => 480,  1010 => 405,  999 => 407,  932 => 352,  899 => 306,  895 => 404,  933 => 387,  914 => 133,  909 => 323,  833 => 284,  783 => 315,  755 => 303,  666 => 263,  453 => 168,  639 => 209,  568 => 176,  520 => 232,  657 => 216,  572 => 201,  609 => 202,  20 => 1,  659 => 217,  562 => 164,  548 => 185,  558 => 197,  479 => 157,  589 => 223,  457 => 199,  413 => 174,  953 => 290,  948 => 379,  935 => 394,  929 => 283,  916 => 382,  864 => 365,  844 => 338,  816 => 342,  807 => 318,  801 => 268,  774 => 257,  766 => 312,  737 => 297,  685 => 225,  664 => 225,  635 => 249,  593 => 199,  546 => 201,  532 => 236,  865 => 296,  852 => 241,  838 => 285,  820 => 201,  781 => 327,  764 => 252,  725 => 250,  632 => 268,  602 => 261,  565 => 183,  529 => 171,  505 => 229,  487 => 101,  473 => 212,  1853 => 842,  1849 => 753,  1839 => 751,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 733,  1778 => 732,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 693,  1705 => 781,  1688 => 675,  1681 => 769,  1677 => 768,  1667 => 671,  1656 => 762,  1648 => 655,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 606,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 593,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 540,  1379 => 557,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 522,  1289 => 406,  1276 => 604,  1263 => 601,  1244 => 456,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 373,  1181 => 561,  1171 => 446,  1158 => 550,  1114 => 428,  1104 => 465,  1072 => 333,  1062 => 7,  1024 => 395,  1014 => 406,  1000 => 368,  990 => 303,  980 => 393,  960 => 466,  918 => 280,  888 => 80,  834 => 325,  673 => 64,  636 => 198,  462 => 207,  454 => 138,  1144 => 463,  1139 => 356,  1131 => 399,  1127 => 434,  1110 => 347,  1092 => 459,  1089 => 426,  1086 => 520,  1084 => 337,  1063 => 387,  1060 => 425,  1055 => 386,  1050 => 373,  1035 => 372,  1019 => 330,  1003 => 401,  959 => 387,  900 => 366,  880 => 276,  870 => 277,  867 => 334,  859 => 294,  848 => 215,  839 => 376,  828 => 302,  823 => 374,  809 => 316,  800 => 241,  797 => 267,  794 => 257,  786 => 283,  740 => 78,  734 => 307,  703 => 228,  693 => 297,  630 => 247,  626 => 19,  614 => 275,  610 => 236,  581 => 206,  564 => 268,  525 => 195,  722 => 226,  697 => 282,  674 => 270,  671 => 285,  577 => 180,  569 => 222,  557 => 179,  502 => 187,  497 => 228,  445 => 163,  729 => 306,  684 => 237,  676 => 65,  669 => 268,  660 => 203,  647 => 274,  643 => 229,  601 => 306,  570 => 169,  522 => 156,  501 => 163,  296 => 108,  374 => 115,  631 => 207,  616 => 283,  608 => 194,  605 => 193,  596 => 188,  574 => 180,  561 => 181,  527 => 165,  433 => 183,  388 => 98,  426 => 172,  383 => 105,  461 => 184,  370 => 147,  395 => 166,  294 => 106,  223 => 55,  220 => 67,  492 => 129,  468 => 124,  444 => 149,  410 => 150,  397 => 136,  377 => 116,  262 => 92,  250 => 78,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 1051,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 680,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 638,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 572,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 526,  1301 => 1037,  1252 => 392,  1243 => 994,  1223 => 978,  1212 => 494,  1167 => 366,  1161 => 933,  1145 => 923,  1140 => 413,  1134 => 409,  1126 => 443,  1106 => 528,  1100 => 897,  1026 => 418,  997 => 367,  983 => 298,  975 => 361,  956 => 318,  939 => 285,  902 => 274,  894 => 364,  879 => 76,  757 => 309,  727 => 293,  716 => 303,  670 => 204,  528 => 187,  476 => 213,  435 => 177,  354 => 138,  341 => 135,  192 => 64,  321 => 89,  243 => 85,  793 => 266,  780 => 256,  758 => 229,  700 => 193,  686 => 294,  652 => 185,  638 => 269,  620 => 216,  545 => 243,  523 => 169,  494 => 227,  459 => 156,  438 => 191,  351 => 123,  347 => 122,  402 => 164,  268 => 71,  430 => 118,  411 => 117,  379 => 138,  322 => 80,  315 => 106,  289 => 101,  284 => 74,  255 => 66,  234 => 48,  1133 => 444,  1124 => 455,  1121 => 430,  1116 => 405,  1113 => 429,  1108 => 376,  1103 => 444,  1098 => 401,  1081 => 425,  1073 => 432,  1067 => 356,  1064 => 330,  1061 => 421,  1053 => 318,  1049 => 419,  1042 => 313,  1039 => 384,  1025 => 314,  1021 => 310,  1015 => 308,  1008 => 461,  996 => 406,  989 => 454,  985 => 395,  981 => 257,  977 => 321,  970 => 360,  966 => 359,  955 => 303,  952 => 464,  943 => 299,  936 => 353,  930 => 148,  919 => 314,  917 => 348,  908 => 346,  905 => 363,  896 => 341,  891 => 338,  877 => 334,  862 => 333,  857 => 271,  837 => 261,  832 => 260,  827 => 349,  821 => 66,  803 => 179,  778 => 314,  769 => 253,  765 => 297,  753 => 54,  746 => 244,  743 => 297,  735 => 295,  730 => 251,  720 => 305,  717 => 362,  712 => 251,  691 => 233,  678 => 275,  654 => 199,  587 => 210,  576 => 171,  539 => 200,  517 => 210,  471 => 125,  441 => 162,  437 => 138,  418 => 142,  386 => 152,  373 => 109,  304 => 108,  270 => 92,  265 => 99,  229 => 91,  477 => 167,  455 => 125,  448 => 143,  429 => 141,  407 => 120,  399 => 163,  389 => 106,  375 => 148,  358 => 99,  349 => 137,  335 => 118,  327 => 98,  298 => 98,  280 => 95,  249 => 95,  194 => 81,  142 => 42,  344 => 121,  318 => 113,  306 => 102,  295 => 64,  357 => 136,  300 => 118,  286 => 105,  276 => 100,  269 => 97,  254 => 100,  128 => 40,  237 => 72,  165 => 49,  122 => 39,  798 => 319,  770 => 309,  759 => 278,  748 => 298,  731 => 294,  721 => 293,  718 => 301,  708 => 218,  696 => 236,  617 => 204,  590 => 259,  553 => 177,  550 => 157,  540 => 161,  533 => 182,  500 => 171,  493 => 160,  489 => 202,  482 => 198,  467 => 210,  464 => 170,  458 => 139,  452 => 197,  449 => 196,  415 => 152,  382 => 132,  372 => 137,  361 => 100,  356 => 124,  339 => 120,  302 => 74,  285 => 104,  258 => 67,  123 => 34,  108 => 28,  424 => 130,  394 => 109,  380 => 2,  338 => 135,  319 => 79,  316 => 113,  312 => 115,  290 => 106,  267 => 91,  206 => 84,  110 => 24,  240 => 86,  224 => 58,  219 => 54,  217 => 80,  202 => 82,  186 => 62,  170 => 28,  100 => 38,  67 => 19,  14 => 1,  1096 => 345,  1090 => 339,  1088 => 397,  1085 => 456,  1066 => 423,  1034 => 282,  1031 => 281,  1018 => 373,  1013 => 405,  1007 => 408,  1002 => 403,  993 => 305,  986 => 264,  982 => 401,  976 => 399,  971 => 295,  964 => 255,  949 => 289,  946 => 288,  940 => 388,  937 => 374,  928 => 371,  926 => 318,  915 => 279,  912 => 82,  903 => 231,  898 => 440,  892 => 303,  889 => 277,  887 => 302,  884 => 79,  876 => 75,  874 => 215,  871 => 331,  863 => 345,  861 => 270,  858 => 255,  850 => 330,  843 => 206,  840 => 326,  815 => 251,  812 => 343,  808 => 323,  804 => 258,  799 => 312,  791 => 310,  785 => 262,  775 => 313,  771 => 253,  754 => 267,  728 => 317,  726 => 72,  723 => 238,  715 => 105,  711 => 220,  709 => 222,  706 => 232,  698 => 298,  694 => 199,  692 => 189,  689 => 234,  681 => 224,  677 => 288,  675 => 234,  663 => 218,  661 => 263,  650 => 213,  646 => 231,  629 => 266,  627 => 218,  625 => 266,  622 => 265,  598 => 199,  592 => 212,  586 => 175,  575 => 189,  566 => 251,  556 => 219,  554 => 188,  541 => 176,  536 => 224,  515 => 138,  511 => 208,  509 => 165,  488 => 200,  486 => 223,  483 => 149,  465 => 191,  463 => 153,  450 => 182,  432 => 147,  419 => 178,  371 => 154,  362 => 144,  353 => 98,  337 => 124,  333 => 91,  309 => 84,  303 => 81,  299 => 108,  291 => 103,  272 => 99,  261 => 38,  253 => 96,  239 => 36,  235 => 94,  213 => 74,  200 => 52,  198 => 39,  159 => 46,  149 => 57,  146 => 34,  131 => 51,  116 => 32,  79 => 16,  74 => 45,  71 => 18,  836 => 262,  817 => 243,  814 => 321,  811 => 320,  805 => 244,  787 => 257,  779 => 169,  776 => 232,  773 => 322,  761 => 311,  751 => 302,  747 => 298,  742 => 243,  739 => 296,  736 => 215,  724 => 259,  705 => 69,  702 => 216,  688 => 226,  680 => 205,  667 => 273,  662 => 282,  656 => 215,  649 => 285,  644 => 220,  641 => 220,  624 => 206,  613 => 166,  607 => 200,  597 => 260,  591 => 49,  584 => 236,  579 => 190,  563 => 212,  559 => 68,  551 => 190,  547 => 188,  537 => 160,  524 => 112,  512 => 174,  507 => 237,  504 => 164,  498 => 162,  485 => 158,  480 => 198,  472 => 169,  466 => 165,  460 => 152,  447 => 150,  442 => 162,  434 => 133,  428 => 181,  422 => 179,  404 => 113,  368 => 136,  364 => 144,  340 => 69,  334 => 123,  330 => 48,  325 => 115,  292 => 106,  287 => 101,  282 => 103,  279 => 70,  273 => 100,  266 => 68,  256 => 97,  252 => 87,  228 => 90,  218 => 87,  201 => 70,  64 => 13,  51 => 10,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 847,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 823,  2037 => 789,  2031 => 788,  2028 => 818,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 811,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 779,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 769,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 744,  1801 => 695,  1798 => 694,  1795 => 742,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 708,  1730 => 705,  1720 => 688,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 672,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 659,  1651 => 656,  1641 => 631,  1637 => 667,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 651,  1592 => 614,  1587 => 613,  1584 => 635,  1581 => 731,  1576 => 608,  1574 => 633,  1567 => 603,  1563 => 630,  1559 => 601,  1552 => 628,  1548 => 597,  1542 => 593,  1529 => 605,  1525 => 614,  1516 => 708,  1512 => 583,  1503 => 599,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 569,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 564,  1388 => 537,  1380 => 539,  1376 => 436,  1371 => 645,  1367 => 527,  1364 => 518,  1361 => 523,  1355 => 543,  1342 => 624,  1337 => 510,  1328 => 552,  1324 => 550,  1317 => 511,  1313 => 499,  1310 => 526,  1304 => 504,  1291 => 479,  1286 => 476,  1279 => 486,  1274 => 468,  1270 => 483,  1266 => 487,  1262 => 490,  1257 => 500,  1254 => 460,  1250 => 394,  1246 => 477,  1239 => 389,  1235 => 498,  1232 => 416,  1226 => 383,  1213 => 466,  1208 => 481,  1201 => 443,  1196 => 464,  1192 => 490,  1188 => 456,  1184 => 485,  1179 => 448,  1176 => 461,  1172 => 428,  1168 => 451,  1164 => 450,  1160 => 469,  1153 => 561,  1149 => 359,  1146 => 444,  1137 => 249,  1123 => 442,  1119 => 354,  1115 => 424,  1111 => 377,  1107 => 403,  1102 => 344,  1099 => 347,  1095 => 400,  1091 => 418,  1082 => 455,  1079 => 524,  1076 => 393,  1070 => 431,  1057 => 491,  1052 => 406,  1045 => 484,  1040 => 416,  1036 => 283,  1032 => 415,  1028 => 312,  1023 => 374,  1020 => 311,  1016 => 266,  1012 => 390,  1005 => 370,  1001 => 304,  998 => 308,  992 => 406,  979 => 297,  974 => 400,  967 => 399,  962 => 397,  958 => 383,  954 => 293,  950 => 393,  945 => 391,  942 => 288,  938 => 375,  934 => 284,  927 => 351,  923 => 382,  920 => 369,  910 => 365,  901 => 342,  897 => 273,  890 => 271,  886 => 270,  883 => 353,  868 => 375,  856 => 293,  853 => 341,  849 => 264,  845 => 329,  841 => 249,  835 => 335,  830 => 333,  826 => 282,  822 => 281,  818 => 327,  813 => 242,  810 => 290,  806 => 270,  802 => 339,  795 => 241,  792 => 335,  789 => 233,  784 => 286,  782 => 237,  777 => 255,  772 => 289,  768 => 81,  763 => 327,  760 => 305,  756 => 248,  752 => 247,  745 => 245,  741 => 218,  738 => 241,  732 => 171,  719 => 288,  714 => 251,  710 => 302,  704 => 301,  699 => 279,  695 => 66,  690 => 226,  687 => 210,  683 => 271,  679 => 223,  672 => 179,  668 => 264,  665 => 283,  658 => 178,  645 => 253,  640 => 227,  634 => 206,  628 => 174,  623 => 217,  619 => 78,  611 => 282,  606 => 234,  603 => 231,  599 => 229,  595 => 213,  583 => 192,  580 => 256,  573 => 255,  560 => 249,  543 => 175,  538 => 174,  534 => 189,  530 => 213,  526 => 170,  521 => 287,  518 => 194,  514 => 230,  510 => 154,  503 => 133,  496 => 202,  490 => 159,  484 => 128,  474 => 174,  470 => 211,  446 => 195,  440 => 130,  436 => 176,  431 => 135,  425 => 180,  416 => 168,  412 => 117,  408 => 167,  403 => 161,  400 => 119,  396 => 162,  392 => 143,  385 => 97,  381 => 150,  367 => 112,  363 => 79,  359 => 125,  355 => 76,  350 => 143,  346 => 73,  343 => 140,  328 => 116,  324 => 118,  313 => 112,  307 => 111,  301 => 119,  288 => 105,  283 => 88,  271 => 69,  257 => 79,  251 => 100,  238 => 93,  233 => 92,  195 => 60,  191 => 64,  187 => 63,  183 => 54,  130 => 38,  88 => 24,  76 => 29,  115 => 58,  95 => 25,  655 => 202,  651 => 275,  648 => 215,  637 => 218,  633 => 197,  621 => 462,  618 => 241,  615 => 264,  604 => 201,  600 => 233,  588 => 206,  585 => 222,  582 => 225,  571 => 187,  567 => 194,  555 => 248,  552 => 171,  549 => 245,  544 => 230,  542 => 242,  535 => 237,  531 => 159,  519 => 167,  516 => 218,  513 => 154,  508 => 207,  506 => 188,  499 => 209,  495 => 150,  491 => 226,  481 => 215,  478 => 171,  475 => 155,  469 => 182,  456 => 138,  451 => 135,  443 => 194,  439 => 178,  427 => 155,  423 => 142,  420 => 141,  409 => 140,  405 => 218,  401 => 138,  391 => 159,  387 => 133,  384 => 138,  378 => 131,  365 => 153,  360 => 125,  348 => 97,  336 => 94,  332 => 129,  329 => 119,  323 => 116,  310 => 110,  305 => 111,  277 => 102,  274 => 94,  263 => 105,  259 => 66,  247 => 84,  244 => 76,  241 => 62,  222 => 60,  210 => 85,  207 => 49,  204 => 63,  184 => 71,  181 => 77,  167 => 48,  157 => 35,  96 => 25,  421 => 143,  417 => 150,  414 => 145,  406 => 139,  398 => 159,  393 => 99,  390 => 134,  376 => 149,  369 => 148,  366 => 127,  352 => 128,  345 => 132,  342 => 126,  331 => 122,  326 => 68,  320 => 114,  317 => 114,  314 => 86,  311 => 105,  308 => 111,  297 => 105,  293 => 104,  281 => 101,  278 => 93,  275 => 39,  264 => 92,  260 => 98,  248 => 54,  245 => 63,  242 => 83,  231 => 70,  227 => 88,  215 => 86,  212 => 77,  209 => 73,  197 => 67,  177 => 57,  171 => 49,  161 => 46,  132 => 61,  121 => 45,  105 => 19,  99 => 51,  81 => 43,  77 => 16,  180 => 47,  176 => 44,  156 => 30,  143 => 24,  139 => 45,  118 => 38,  189 => 80,  185 => 79,  173 => 76,  166 => 38,  152 => 40,  174 => 59,  164 => 74,  154 => 41,  150 => 68,  137 => 33,  133 => 43,  127 => 44,  107 => 26,  102 => 23,  83 => 23,  78 => 22,  53 => 13,  23 => 3,  42 => 11,  138 => 36,  134 => 37,  109 => 42,  103 => 26,  97 => 22,  94 => 21,  84 => 17,  75 => 24,  69 => 24,  66 => 17,  54 => 12,  44 => 11,  230 => 80,  226 => 80,  203 => 75,  193 => 72,  188 => 75,  182 => 46,  178 => 71,  168 => 75,  163 => 47,  160 => 72,  155 => 45,  148 => 48,  145 => 52,  140 => 46,  136 => 63,  125 => 34,  120 => 33,  113 => 45,  101 => 37,  92 => 19,  89 => 18,  85 => 21,  73 => 21,  62 => 14,  59 => 15,  56 => 15,  41 => 9,  126 => 33,  119 => 59,  111 => 34,  106 => 41,  98 => 63,  93 => 25,  86 => 22,  70 => 16,  60 => 22,  28 => 6,  36 => 9,  114 => 41,  104 => 29,  91 => 34,  80 => 21,  63 => 20,  58 => 12,  40 => 10,  34 => 7,  45 => 7,  61 => 11,  55 => 19,  48 => 12,  39 => 9,  35 => 8,  31 => 7,  26 => 4,  21 => 2,  46 => 10,  29 => 6,  57 => 16,  50 => 12,  47 => 11,  38 => 10,  33 => 3,  49 => 11,  32 => 8,  246 => 94,  236 => 93,  232 => 91,  225 => 68,  221 => 78,  216 => 53,  214 => 74,  211 => 64,  208 => 72,  205 => 72,  199 => 48,  196 => 77,  190 => 71,  179 => 58,  175 => 50,  172 => 70,  169 => 35,  162 => 48,  158 => 47,  153 => 69,  151 => 44,  147 => 43,  144 => 46,  141 => 65,  135 => 39,  129 => 35,  124 => 35,  117 => 32,  112 => 31,  90 => 31,  87 => 29,  82 => 31,  72 => 18,  68 => 21,  65 => 30,  52 => 13,  43 => 9,  37 => 8,  30 => 7,  27 => 3,  25 => 4,  24 => 4,  22 => 1,  19 => 1,);
    }
}
