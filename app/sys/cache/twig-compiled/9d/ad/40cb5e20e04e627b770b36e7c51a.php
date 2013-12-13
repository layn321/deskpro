<?php

/* AdminBundle:TicketTriggers:actions.html.twig */
class __TwigTemplate_9dad40cb5e20e04e627b770b36e7c51a extends \Application\DeskPRO\Twig\Template
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
        echo "<div ";
        if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
        if ($_id_) {
            echo "id=\"";
            if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
            echo twig_escape_filter($this->env, $_id_, "html", null, true);
            echo "\"";
        }
        echo " class=\"";
        if (isset($context["classname"])) { $_classname_ = $context["classname"]; } else { $_classname_ = null; }
        echo twig_escape_filter($this->env, ((array_key_exists("classname", $context)) ? (_twig_default_filter($_classname_, "actions-tpl")) : ("actions-tpl")), "html", null, true);
        echo "\" style=\"display:none\">
<div class=\"row\">
\t<div class=\"term\">
\t\t<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\" class=\"term-table\"><tbody><tr>
\t\t\t<td style=\"vertical-align: middle; text-align: center;\" width=\"11\"><div class=\"builder-remove\">-</div></td>
\t\t\t<td class=\"builder-controls\" style=\"vertical-align: middle;\">
\t\t\t\t<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\"><tbody><tr>
\t\t\t\t\t<td style=\"vertical-align: middle;\" width=\"10\" nowrap=\"nowrap\"><div class=\"builder-type-choice\"></div></td>
\t\t\t\t\t<td style=\"vertical-align: middle;\" width=\"10\" nowrap=\"nowrap\"><div class=\"builder-op\"></div></td>
\t\t\t\t\t<td style=\"vertical-align: middle;\"><div class=\"builder-options\"></div></td>
\t\t\t\t</tr></tbody></table>
\t\t\t</td>
\t\t</tr></tbody></table>
\t</div>
</div>

";
        // line 20
        echo "
<div class=\"builder-type\" title=\"";
        // line 21
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.set_agent");
        echo "\" data-rule-type=\"agent\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\">
\t\t<select name=\"agent\">
\t\t\t<option value=\"0\">
\t\t\t\t";
        // line 25
        if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
        if (($_event_trigger_ == "new.email.agent")) {
            echo "No one
\t\t\t\t";
        } else {
            // line 26
            echo "Unassigned
\t\t\t\t";
        }
        // line 28
        echo "\t\t\t</option>
\t\t\t";
        // line 29
        if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
        if (($_event_trigger_ == "new.email.agent")) {
            echo "<option value=\"-1\">The agent who forwarded the email</option>";
        }
        // line 30
        echo "\t\t\t";
        if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
        if (($_event_trigger_ == "new.web.agent.portal")) {
            echo "<option value=\"-1\">The agent who created the ticket</option>";
        }
        // line 31
        echo "\t\t\t";
        if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
        if (($_event_trigger_ == "update.agent")) {
            echo "<option value=\"-1\">The agent who made the change</option>";
        }
        // line 32
        echo "\t\t\t";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "agents"), "getAgents", array(), "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["agent"]) {
            // line 33
            echo "\t\t\t\t<option value=\"";
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "id"), "html", null, true);
            echo "\">";
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "display_name"), "html", null, true);
            echo "</option>
\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['agent'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 35
        echo "\t\t</select>
\t</div>
</div>

<div class=\"builder-type\" title=\"Add agent followers\" data-rule-type=\"add_participants\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\">
\t\t<select name=\"add_participants[]\" multiple=\"multiple\">
\t\t\t";
        // line 42
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "agents"), "getAgents", array(), "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["agent"]) {
            // line 43
            echo "\t\t\t\t<option value=\"";
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "id"), "html", null, true);
            echo "\">";
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "display_name"), "html", null, true);
            echo "</option>
\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['agent'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 45
        echo "\t\t</select>
\t</div>
</div>

<div class=\"builder-type\" title=\"CC users\" data-rule-type=\"add_cc\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"add_emails\" placeholder=\"Enter email addresses separated by a comma\" />
\t</div>
</div>

";
        // line 55
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (twig_length_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "agent_teams"), "getTeamNames", array(), "method"))) {
            // line 56
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.set_agent_team");
            echo "\" data-rule-type=\"agent_team\" data-rule-group=\"Ticket\">
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"agent_team\">
\t\t\t\t<option value=\"0\">No Team</option>
\t\t\t\t";
            // line 60
            if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
            if (($_event_trigger_ == "new.email.agent")) {
                echo "<option value=\"-1\">The team of the agent who forwarded the email</option>";
            }
            // line 61
            echo "\t\t\t\t";
            if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
            if (($_event_trigger_ == "new.web.agent.portal")) {
                echo "<option value=\"-1\">The team of the agent who created the ticket</option>";
            }
            // line 62
            echo "\t\t\t\t";
            if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
            if (($_event_trigger_ == "update.agent")) {
                echo "<option value=\"-1\">The team of the agent who made the change</option>";
            }
            // line 63
            echo "\t\t\t\t";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "agent_teams"), "getTeamNames", array(), "method"));
            foreach ($context['_seq'] as $context["id"] => $context["title"]) {
                // line 64
                echo "\t\t\t\t\t<option value=\"";
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                echo twig_escape_filter($this->env, $_id_, "html", null, true);
                echo "\">";
                if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
                echo twig_escape_filter($this->env, $_title_, "html", null, true);
                echo "</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['id'], $context['title'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 66
            echo "\t\t\t</select>
\t\t</div>
\t</div>
";
        }
        // line 70
        echo "
<div class=\"builder-type\" title=\"";
        // line 71
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.set_status");
        echo "\" data-rule-type=\"status\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\">
\t\t<select name=\"status\">
\t\t\t<option value=\"awaiting_agent\">";
        // line 74
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_awaiting_agent");
        echo "</option>
\t\t\t<option value=\"awaiting_user\">";
        // line 75
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_awaiting_user");
        echo "</option>
\t\t\t<option value=\"resolved\">";
        // line 76
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_resolved");
        echo "</option>
\t\t</select>
\t</div>
</div>

<div class=\"builder-type\" title=\"Ticket hold status\" data-rule-type=\"hold\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\">
\t\t<select name=\"is_hold\">
\t\t\t<option value=\"1\">Put ticket on hold</option>
\t\t\t<option value=\"0\">Remove ticket from hold</option>
\t\t</select>
\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 90
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.delete_ticket");
        echo "\" data-rule-type=\"delete\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\">
\t\t<input type=\"hidden\" name=\"delete_ticket\" value=\"1\" />
\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 96
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.set_department");
        echo "\" data-rule-type=\"department\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\">
\t\t<select name=\"department\">
\t\t\t";
        // line 99
        if (isset($context["event_trigger_master"])) { $_event_trigger_master_ = $context["event_trigger_master"]; } else { $_event_trigger_master_ = null; }
        if (($_event_trigger_master_ != "sla")) {
            echo "<option value=\"email_account\">Linked department for email account</option>";
        }
        // line 100
        echo "\t\t\t";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "departments"), "getRootNodes", array(), "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["dep"]) {
            if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
            if ($this->getAttribute($_dep_, "is_tickets_enabled")) {
                // line 101
                echo "\t\t\t\t";
                if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                if (twig_length_filter($this->env, $this->getAttribute($_dep_, "children"))) {
                    // line 102
                    echo "\t\t\t\t\t<optgroup label=\"";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                    echo "\">
\t\t\t\t\t\t";
                    // line 103
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($_dep_, "children"));
                    foreach ($context['_seq'] as $context["_key"] => $context["subdep"]) {
                        // line 104
                        echo "\t\t\t\t\t\t\t<option data-full-title=\"";
                        if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                        echo " &gt; ";
                        if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_subdep_, "title"), "html", null, true);
                        echo "\" value=\"";
                        if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_subdep_, "id"), "html", null, true);
                        echo "\">";
                        if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_subdep_, "title"), "html", null, true);
                        echo "</option>
\t\t\t\t\t\t";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subdep'], $context['_parent'], $context['loop']);
                    $context = array_merge($_parent, array_intersect_key($context, $_parent));
                    // line 106
                    echo "\t\t\t\t\t</optgroup>
\t\t\t\t";
                } else {
                    // line 108
                    echo "\t\t\t\t\t<option value=\"";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "id"), "html", null, true);
                    echo "\">";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                    echo "</option>
\t\t\t\t";
                }
                // line 110
                echo "\t\t\t";
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['dep'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 111
        echo "\t\t</select>
\t</div>
</div>

";
        // line 115
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "Language"), "method"), "isLangSystemEnabled", array(), "method")) {
            // line 116
            echo "\t<div class=\"builder-type\" title=\"Set Language\" data-rule-type=\"language\" data-rule-group=\"Ticket\">
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"language\">
\t\t\t\t";
            // line 119
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "Language"), "method"), "getAll", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["lang"]) {
                // line 120
                echo "\t\t\t\t\t<option value=\"";
                if (isset($context["lang"])) { $_lang_ = $context["lang"]; } else { $_lang_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_lang_, "id"), "html", null, true);
                echo "\">";
                if (isset($context["lang"])) { $_lang_ = $context["lang"]; } else { $_lang_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPhraseObject($_lang_, "title"), "html", null, true);
                echo "</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['lang'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 122
            echo "\t\t\t</select>
\t\t</div>
\t</div>
";
        }
        // line 126
        echo "
";
        // line 127
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_ticket_category"), "method")) {
            // line 128
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.set_category");
            echo "\" data-rule-type=\"category\" data-rule-group=\"Ticket\">
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"category\">
\t\t\t\t<option value=\"0\">None</option>
\t\t\t\t";
            // line 132
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "TicketCategory"), "method"), "getRootNodes", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["cat"]) {
                // line 133
                echo "\t\t\t\t\t";
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                if (twig_length_filter($this->env, $this->getAttribute($_cat_, "children"))) {
                    // line 134
                    echo "\t\t\t\t\t\t<optgroup label=\"";
                    if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
                    echo "\">
\t\t\t\t\t\t\t";
                    // line 135
                    if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($_cat_, "children"));
                    foreach ($context['_seq'] as $context["_key"] => $context["subcat"]) {
                        // line 136
                        echo "\t\t\t\t\t\t\t\t<option data-full-title=\"";
                        if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
                        echo " &gt; ";
                        if (isset($context["subcat"])) { $_subcat_ = $context["subcat"]; } else { $_subcat_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_subcat_, "title"), "html", null, true);
                        echo "\" value=\"";
                        if (isset($context["subcat"])) { $_subcat_ = $context["subcat"]; } else { $_subcat_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_subcat_, "id"), "html", null, true);
                        echo "\">";
                        if (isset($context["subcat"])) { $_subcat_ = $context["subcat"]; } else { $_subcat_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_subcat_, "title"), "html", null, true);
                        echo "</option>
\t\t\t\t\t\t\t";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subcat'], $context['_parent'], $context['loop']);
                    $context = array_merge($_parent, array_intersect_key($context, $_parent));
                    // line 138
                    echo "\t\t\t\t\t\t</optgroup>
\t\t\t\t\t";
                } else {
                    // line 140
                    echo "\t\t\t\t\t\t<option value=\"";
                    if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "id"), "html", null, true);
                    echo "\">";
                    if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
                    echo "</option>
\t\t\t\t\t";
                }
                // line 142
                echo "\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['cat'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 143
            echo "\t\t\t</select>
\t\t</div>
\t</div>
";
        }
        // line 147
        echo "
";
        // line 148
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_product"), "method")) {
            // line 149
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.set_product");
            echo "\" data-rule-type=\"product\" data-rule-group=\"Ticket\">
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"product\">
\t\t\t\t<option value=\"0\">None</option>
\t\t\t\t";
            // line 153
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "products"), "getNames", array(), "method"));
            foreach ($context['_seq'] as $context["id"] => $context["title"]) {
                // line 154
                echo "\t\t\t\t\t<option value=\"";
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                echo twig_escape_filter($this->env, $_id_, "html", null, true);
                echo "\">";
                if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
                echo twig_escape_filter($this->env, $_title_, "html", null, true);
                echo "</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['id'], $context['title'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 156
            echo "\t\t\t</select>
\t\t</div>
\t</div>
";
        }
        // line 160
        echo "
";
        // line 161
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_ticket_priority"), "method")) {
            // line 162
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.set_priority");
            echo "\" data-rule-type=\"priority\" data-rule-group=\"Ticket\">
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"priority\">
\t\t\t\t<option value=\"0\">None</option>
\t\t\t\t";
            // line 166
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "TicketPriority"), "method"), "getNames", array(), "method"));
            foreach ($context['_seq'] as $context["id"] => $context["name"]) {
                // line 167
                echo "\t\t\t\t\t<option value=\"";
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                echo twig_escape_filter($this->env, $_id_, "html", null, true);
                echo "\">";
                if (isset($context["name"])) { $_name_ = $context["name"]; } else { $_name_ = null; }
                echo twig_escape_filter($this->env, $_name_, "html", null, true);
                echo "</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['id'], $context['name'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 169
            echo "\t\t\t</select>
\t\t</div>
\t</div>
";
        }
        // line 173
        echo "
";
        // line 174
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_ticket_workflow"), "method")) {
            // line 175
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.set_workflow");
            echo "\" data-rule-type=\"workflow\" data-rule-group=\"Ticket\">
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"workflow\">
\t\t\t\t<option value=\"0\">None</option>
\t\t\t\t";
            // line 179
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "TicketWorkflow"), "method"), "getAll", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["work"]) {
                // line 180
                echo "\t\t\t\t\t<option value=\"";
                if (isset($context["work"])) { $_work_ = $context["work"]; } else { $_work_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_work_, "id"), "html", null, true);
                echo "\">";
                if (isset($context["work"])) { $_work_ = $context["work"]; } else { $_work_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_work_, "title"), "html", null, true);
                echo "</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['work'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 182
            echo "\t\t\t</select>
\t\t</div>
\t</div>
";
        }
        // line 186
        echo "
";
        // line 187
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        if ($this->getAttribute($_term_options_, "slas")) {
            // line 188
            echo "\t";
            if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
            if (!twig_in_filter("add_sla", $_no_show_fields_)) {
                // line 189
                echo "\t\t<div class=\"builder-type\" title=\"Add SLA\" data-rule-type=\"add_sla\" data-rule-group=\"Ticket\">
\t\t\t<div class=\"builder-options\">
\t\t\t\t<select name=\"sla_id\">
\t\t\t\t";
                // line 192
                if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "slas"));
                foreach ($context['_seq'] as $context["id"] => $context["title"]) {
                    // line 193
                    echo "\t\t\t\t\t<option value=\"";
                    if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                    echo twig_escape_filter($this->env, $_id_, "html", null, true);
                    echo "\">";
                    if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
                    echo twig_escape_filter($this->env, $_title_, "html", null, true);
                    echo "</option>
\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['id'], $context['title'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 195
                echo "\t\t\t\t</select>
\t\t\t</div>
\t\t</div>
\t";
            }
            // line 199
            echo "
\t";
            // line 200
            if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
            if (!twig_in_filter("remove_sla", $_no_show_fields_)) {
                // line 201
                echo "\t\t<div class=\"builder-type\" title=\"Remove SLA\" data-rule-type=\"remove_sla\" data-rule-group=\"Ticket\">
\t\t\t<div class=\"builder-options\">
\t\t\t\t<select name=\"sla_id\">
\t\t\t\t\t<option value=\"0\">All</option>
\t\t\t\t";
                // line 205
                if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "slas"));
                foreach ($context['_seq'] as $context["id"] => $context["title"]) {
                    // line 206
                    echo "\t\t\t\t\t<option value=\"";
                    if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                    echo twig_escape_filter($this->env, $_id_, "html", null, true);
                    echo "\">";
                    if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
                    echo twig_escape_filter($this->env, $_title_, "html", null, true);
                    echo "</option>
\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['id'], $context['title'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 208
                echo "\t\t\t\t</select>
\t\t\t</div>
\t\t</div>
\t";
            }
            // line 212
            echo "
\t";
            // line 213
            if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
            if (!twig_in_filter("set_sla_status", $_no_show_fields_)) {
                // line 214
                echo "\t\t<div class=\"builder-type\" title=\"Set SLA Status\" data-rule-type=\"set_sla_status\" data-rule-group=\"Ticket\">
\t\t\t<div class=\"builder-options\">
\t\t\t\t<select name=\"sla_status\">
\t\t\t\t\t<option value=\"ok\">OK</option>
\t\t\t\t\t<option value=\"warn\">Warning</option>
\t\t\t\t\t<option value=\"fail\">Fail</option>
\t\t\t\t</select>
\t\t\t\tfor SLA
\t\t\t\t<select name=\"sla_id\">
\t\t\t\t\t<option value=\"0\">All</option>
\t\t\t\t";
                // line 224
                if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "slas"));
                foreach ($context['_seq'] as $context["id"] => $context["title"]) {
                    // line 225
                    echo "\t\t\t\t\t<option value=\"";
                    if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                    echo twig_escape_filter($this->env, $_id_, "html", null, true);
                    echo "\">";
                    if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
                    echo twig_escape_filter($this->env, $_title_, "html", null, true);
                    echo "</option>
\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['id'], $context['title'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 227
                echo "\t\t\t\t</select>
\t\t\t</div>
\t\t</div>
\t";
            }
            // line 231
            echo "
\t";
            // line 232
            if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
            if (!twig_in_filter("set_sla_complete", $_no_show_fields_)) {
                // line 233
                echo "\t\t<div class=\"builder-type\" title=\"Set SLA Requirements\" data-rule-type=\"set_sla_complete\" data-rule-group=\"Ticket\">
\t\t\t<div class=\"builder-options\">
\t\t\t\t<select name=\"sla_complete\">
\t\t\t\t\t<option value=\"1\">Complete</option>
\t\t\t\t\t<option value=\"0\">Incomplete</option>
\t\t\t\t</select>
\t\t\t\tfor SLA
\t\t\t\t<select name=\"sla_id\">
\t\t\t\t\t<option value=\"0\">All</option>
\t\t\t\t";
                // line 242
                if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "slas"));
                foreach ($context['_seq'] as $context["id"] => $context["title"]) {
                    // line 243
                    echo "\t\t\t\t\t<option value=\"";
                    if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                    echo twig_escape_filter($this->env, $_id_, "html", null, true);
                    echo "\">";
                    if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
                    echo twig_escape_filter($this->env, $_title_, "html", null, true);
                    echo "</option>
\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['id'], $context['title'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 245
                echo "\t\t\t\t</select>
\t\t\t</div>
\t\t</div>
\t";
            }
            // line 249
            echo "
\t";
            // line 250
            if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
            if (isset($context["event_trigger_master"])) { $_event_trigger_master_ = $context["event_trigger_master"]; } else { $_event_trigger_master_ = null; }
            if ((!twig_in_filter("recalculate_sla_status", $_no_show_fields_) && ($_event_trigger_master_ == "sla"))) {
                // line 251
                echo "\t\t<div class=\"builder-type\" title=\"Recalculate SLA Status\" data-rule-type=\"recalculate_sla_status\" data-rule-group=\"Ticket\">
\t\t\t<div class=\"builder-options\">
\t\t\t\t<input type=\"hidden\" name=\"x\" value=\"DP_ALLOW_BLANK\" />
\t\t\t</div>
\t\t</div>
\t";
            }
        }
        // line 258
        echo "
<div class=\"builder-type\" title=\"";
        // line 259
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.add_labels");
        echo "\" data-rule-type=\"add_labels\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.LabelsTerm\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\">
\t\t";
        // line 261
        $this->env->loadTemplate("DeskPRO:Common:rulebuilder-labels-options.html.twig")->display($context);
        // line 262
        echo "\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 265
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.remove_labels");
        echo "\" data-rule-type=\"remove_labels\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.LabelsTerm\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\">
\t\t";
        // line 267
        $this->env->loadTemplate("DeskPRO:Common:rulebuilder-labels-options.html.twig")->display($context);
        // line 268
        echo "\t</div>
</div>

";
        // line 271
        if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
        if (twig_in_filter("new.", $_event_trigger_)) {
            // line 272
            echo "\t<div class=\"builder-type\" title=\"Send user an email notification about the new ticket\" data-rule-group=\"User Email Templates\"  data-rule-type=\"enable_new_ticket_confirmation\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.SelectNewOption\">
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"hidden\" name=\"enabled\" value=\"1\" />
\t\t</div>
\t</div>
";
        }
        // line 278
        echo "
<div class=\"builder-type\" title=\"Send an email to the user\" data-rule-group=\"User Email Templates\"  data-rule-type=\"send_user_email\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.SelectNewOption\">
\t<div class=\"builder-options\">
\t\t<select class=\"template_name\" name=\"template_name\">
\t\t\t<option value=\"NEW\">Create a new template</option>
\t\t\t";
        // line 283
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "Template"), "method"), "getCustomEmailsOfType", array(0 => "DeskPRO:emails_user:blank.html.twig"), "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["tpl"]) {
            // line 284
            echo "\t\t\t\t<option value=\"";
            if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
            echo twig_escape_filter($this->env, $_tpl_, "html", null, true);
            echo "\">";
            if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getBaseTemplateName($_tpl_);
            echo "</option>
\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tpl'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 286
        echo "\t\t</select>
\t\t<input type=\"text\" class=\"new_option\" name=\"new_option\" value=\"\" style=\"display: none;\" placeholder=\"Enter a unique name for your new template\" />
\t</div>
</div>
<div class=\"builder-type\" title=\"Send an email to agents\" data-rule-group=\"Agent Email Templates\"  data-rule-type=\"send_agent_email\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.SelectNewOption\">
\t<div class=\"builder-options\">
\t\t<select class=\"template_name\" name=\"template_name\">
\t\t\t<option value=\"NEW\">Create a new template</option>
\t\t\t";
        // line 294
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "Template"), "method"), "getCustomEmailsOfType", array(0 => "DeskPRO:emails_agent:blank.html.twig"), "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["tpl"]) {
            // line 295
            echo "\t\t\t\t<option value=\"";
            if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
            echo twig_escape_filter($this->env, $_tpl_, "html", null, true);
            echo "\">";
            if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getBaseTemplateName($_tpl_);
            echo "</option>
\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tpl'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 297
        echo "\t\t</select>
\t\t<input type=\"text\" class=\"new_option\" name=\"new_option\" value=\"\" style=\"display: none; margin-top: 3px; margin-bottom: 3px;\" placeholder=\"Enter a unique name for your new template\" />
\t\t<br />
\t\tSend to:<br /><select name=\"agents[]\" class=\"agents\" multiple=\"multiple\" style=\"width: 80%;\">
\t\t\t";
        // line 301
        if (isset($context["event_trigger_master"])) { $_event_trigger_master_ = $context["event_trigger_master"]; } else { $_event_trigger_master_ = null; }
        if (($_event_trigger_master_ != "time")) {
            // line 302
            echo "\t\t\t\t<option value=\"-1\">The agent that initiated the action</option>
\t\t\t";
        }
        // line 304
        echo "\t\t\t<option value=\"assigned_agent\">Assigned agent</option>
\t\t\t<option value=\"assigned_agent_team\">All agents of the assigned team</option>
\t\t\t";
        // line 306
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "agents"));
        foreach ($context['_seq'] as $context["id"] => $context["title"]) {
            // line 307
            echo "\t\t\t\t<option value=\"";
            if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
            echo twig_escape_filter($this->env, $_id_, "html", null, true);
            echo "\">";
            if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
            echo twig_escape_filter($this->env, $_title_, "html", null, true);
            echo "</option>
\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['id'], $context['title'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 309
        echo "\t\t</select>
\t</div>
</div>

<div class=\"builder-type\" title=\"Send the user an email requesting feedback\" data-rule-type=\"send_feedback_email\" data-rule-group=\"User Email Templates\">
\t<div class=\"builder-options\">
\t\t<input type=\"hidden\" name=\"do\" value=\"1\" />
\t</div>
</div>

<div class=\"builder-type\" title=\"Add organization managers to ticket\" data-rule-type=\"add_org_managers\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\" style=\"display:block\">
\t\t<input type=\"hidden\" name=\"x\" value=\"DP_ALLOW_BLANK\" />
\t</div>
</div>

<div class=\"builder-type\" title=\"Send the organization managers an email\" data-rule-type=\"send_org_managers_email\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\" style=\"display:block\">
\t\t<textarea name=\"message\" cols=\"80\" rows=\"4\"></textarea>
\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 331
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.set_flag");
        echo "\" data-rule-type=\"flag\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\">
\t\t<select name=\"flag\">
\t\t\t<option value=\"none\">";
        // line 334
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none");
        echo "</option>
\t\t\t<option value=\"blue\">";
        // line 335
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.flag_blue");
        echo "</option>
\t\t\t<option value=\"green\">";
        // line 336
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.flag_green");
        echo "</option>
\t\t\t<option value=\"orange\">";
        // line 337
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.flag_orange");
        echo "</option>
\t\t\t<option value=\"pink\">";
        // line 338
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.flag_pink");
        echo "</option>
\t\t\t<option value=\"purple\">";
        // line 339
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.flag_purple");
        echo "</option>
\t\t\t<option value=\"red\">";
        // line 340
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.flag_red");
        echo "</option>
\t\t\t<option value=\"yellow\">";
        // line 341
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.flag_yellow");
        echo "</option>
\t\t</select>
\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 346
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.increase_decrease_urgency");
        echo "\" data-rule-type=\"urgency\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"num\" /><small>";
        // line 348
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.urgency_enter_negative_number");
        echo "</small>
\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 352
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.set_urgency");
        echo "\" data-rule-type=\"urgency_set\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"num\" style=\"width: 50px\" />
\t\t";
        // line 355
        if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
        if (($_event_trigger_ != "new_ticket")) {
            // line 356
            echo "\t\t<select name=\"allow_lower\">
\t\t\t<option value=\"0\">";
            // line 357
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.notice_only_when_urgency_lower");
            echo "</option>
\t\t\t<option value=\"1\">";
            // line 358
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.always_set_this_urgency");
            echo "</option>
\t\t</select>
\t\t";
        }
        // line 361
        echo "\t</div>
</div>

<div class=\"builder-type\" title=\"Set Subject\" data-rule-type=\"subject\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"subject\" />
\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 370
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.add_ticket_reply");
        echo "\" data-rule-type=\"reply\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\" style=\"display:block\">
\t\t<textarea name=\"reply_text\" cols=\"80\" rows=\"4\"></textarea>
\t\t<br/>By: <select name=\"person_id\">
\t\t\t<option value=\"0\">Assigned agent</option>
\t\t\t";
        // line 375
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "agents"), "getAgents", array(), "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["agent"]) {
            // line 376
            echo "\t\t\t\t<option value=\"";
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "id"), "html", null, true);
            echo "\">";
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "display_name"), "html", null, true);
            echo "</option>
\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['agent'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 378
        echo "\t\t</select>
\t</div>
</div>

";
        // line 382
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getTicketFieldManager", array(), "method"), "getDisplayArray", array(), "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["field"]) {
            // line 383
            echo "\t";
            if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
            if ($this->getAttribute($this->getAttribute($_field_, "field_def"), "isFormField", array(), "method")) {
                // line 384
                echo "\t\t<div class=\"builder-type\" title=\"";
                if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.set_field_name", array("field" => $this->getAttribute($this->getAttribute($_field_, "field_def"), "title")));
                echo "\" data-rule-group=\"Ticket\" data-rule-type=\"ticket_field[";
                if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_field_, "field_def"), "id"), "html", null, true);
                echo "]\">
\t\t\t<div class=\"builder-options\">
\t\t\t\t";
                // line 386
                if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                echo $this->env->getExtension('form')->renderWidget($this->getAttribute($_field_, "formView"));
                echo "
\t\t\t</div>
\t\t</div>
\t";
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['field'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 391
        echo "
";
        // line 395
        echo "
";
        // line 396
        if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
        if ((($_event_trigger_ == "new.email.user") || ($_event_trigger_ == "new.web.user"))) {
            // line 397
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.force_email_validation");
            echo "\" data-rule-type=\"force_email_validation\" data-rule-group=\"Person\">
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"hidden\" name=\"force_email_validation\" value=\"1\" />
\t\t</div>
\t</div>
";
        }
        // line 403
        echo "
";
        // line 404
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        if ($this->getAttribute($_term_options_, "custom_people_fields")) {
            // line 405
            echo "\t";
            if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "custom_people_fields"));
            foreach ($context['_seq'] as $context["_key"] => $context["field"]) {
                // line 406
                echo "\t\t";
                if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                if ($this->getAttribute($this->getAttribute($_field_, "field_def"), "isFormField", array(), "method")) {
                    // line 407
                    echo "\t\t<div class=\"builder-type\" title=\"";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.set_user_field_name", array("field" => $this->getAttribute($this->getAttribute($_field_, "field_def"), "title")));
                    echo "\" data-rule-group=\"Person\"  data-rule-type=\"people_field[";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_field_, "field_def"), "id"), "html", null, true);
                    echo "]\">
\t\t\t<div class=\"builder-options\">
\t\t\t\t";
                    // line 409
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    echo $this->env->getExtension('form')->renderWidget($this->getAttribute($_field_, "formView"));
                    echo "
\t\t\t</div>
\t\t</div>
\t\t";
                }
                // line 413
                echo "\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['field'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
        }
        // line 415
        echo "
";
        // line 419
        echo "
";
        // line 420
        if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
        if ((($_event_trigger_ == "new.email.user") || ($_event_trigger_ == "new.web.user"))) {
            // line 421
            echo "\t<div class=\"builder-type\" title=\"Change 'New Ticket Confirmation' user email\" data-rule-group=\"User Email Templates\"  data-rule-type=\"set_user_email_template_newticket\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.SelectNewOption\">
\t\t<div class=\"builder-options\">
\t\t\t<select class=\"template_name\" name=\"template_name\">
\t\t\t\t<option value=\"NEW\">Create a new template</option>
\t\t\t\t";
            // line 425
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "Template"), "method"), "getCustomEmailsOfType", array(0 => "DeskPRO:emails_user:new-ticket.html.twig"), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["tpl"]) {
                // line 426
                echo "\t\t\t\t\t<option value=\"";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo twig_escape_filter($this->env, $_tpl_, "html", null, true);
                echo "\">";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getBaseTemplateName($_tpl_);
                echo "</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tpl'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 428
            echo "\t\t\t</select>
\t\t\t<input type=\"text\" class=\"new_option\" name=\"new_option\" value=\"\" style=\"display: none;\" placeholder=\"Enter a unique name for your new template\" />
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"Change 'New Ticket Confirmation (Awaiting Validation)' user email\" data-rule-group=\"User Email Templates\"  data-rule-type=\"set_user_email_template_newticket_validate\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.SelectNewOption\">
\t\t<div class=\"builder-options\">
\t\t\t<select class=\"template_name\" name=\"template_name\">
\t\t\t\t<option value=\"NEW\">Create a new template</option>
\t\t\t\t";
            // line 437
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "Template"), "method"), "getCustomEmailsOfType", array(0 => "DeskPRO:emails_user:new-ticket-validate.html.twig"), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["tpl"]) {
                // line 438
                echo "\t\t\t\t\t<option value=\"";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo twig_escape_filter($this->env, $_tpl_, "html", null, true);
                echo "\">";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getBaseTemplateName($_tpl_);
                echo "</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tpl'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 440
            echo "\t\t\t</select>
\t\t\t<input type=\"text\" class=\"new_option\" name=\"new_option\" value=\"\" style=\"display: none;\" placeholder=\"Enter a unique name for your new template\" />
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"Change 'New Ticket' agent email\" data-rule-group=\"Agent Email Templates\"  data-rule-type=\"set_agent_email_template_newticket\">
\t\t<div class=\"builder-options\">
\t\t\t<select class=\"template_name\" name=\"template_name\">
\t\t\t\t<option value=\"NEW\">Create a new template</option>
\t\t\t\t";
            // line 449
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "Template"), "method"), "getCustomEmailsOfType", array(0 => "DeskPRO:emails_agent:new-ticket.html.twig"), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["tpl"]) {
                // line 450
                echo "\t\t\t\t\t<option value=\"";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo twig_escape_filter($this->env, $_tpl_, "html", null, true);
                echo "\">";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getBaseTemplateName($_tpl_);
                echo "</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tpl'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 452
            echo "\t\t\t</select>
\t\t\t<input type=\"text\" class=\"new_option\" name=\"new_option\" value=\"\" style=\"display: none;\" placeholder=\"Enter a unique name for your new template\" />
\t\t</div>
\t</div>
";
        }
        // line 457
        echo "
";
        // line 458
        if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
        if (($_event_trigger_ == "new.web.agent.portal")) {
            // line 459
            echo "\t<div class=\"builder-type\" title=\"Change 'New Ticket Created By Agent' user email\" data-rule-group=\"User Email Templates\"  data-rule-type=\"set_user_email_template_newticket_agent\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.SelectNewOption\">
\t\t<div class=\"builder-options\">
\t\t\t<select class=\"template_name\" name=\"template_name\">
\t\t\t\t<option value=\"NEW\">Create a new template</option>
\t\t\t\t";
            // line 463
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "Template"), "method"), "getCustomEmailsOfType", array(0 => "DeskPRO:emails_user:new-ticket-agent.html.twig"), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["tpl"]) {
                // line 464
                echo "\t\t\t\t\t<option value=\"";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo twig_escape_filter($this->env, $_tpl_, "html", null, true);
                echo "\">";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getBaseTemplateName($_tpl_);
                echo "</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tpl'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 466
            echo "\t\t\t</select>
\t\t\t<input type=\"text\" class=\"new_option\" name=\"new_option\" value=\"\" style=\"display: none;\" placeholder=\"Enter a unique name for your new template\" />
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"Change 'New Ticket' agent email\" data-rule-group=\"Agent Email Templates\"  data-rule-type=\"set_agent_email_template_newticket\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.SelectNewOption\">
\t\t<div class=\"builder-options\">
\t\t\t<select class=\"template_name\" name=\"template_name\">
\t\t\t\t<option value=\"NEW\">Create a new template</option>
\t\t\t\t";
            // line 475
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "Template"), "method"), "getCustomEmailsOfType", array(0 => "DeskPRO:emails_agent:new-ticket.html.twig"), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["tpl"]) {
                // line 476
                echo "\t\t\t\t\t<option value=\"";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo twig_escape_filter($this->env, $_tpl_, "html", null, true);
                echo "\">";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getBaseTemplateName($_tpl_);
                echo "</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tpl'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 478
            echo "\t\t\t</select>
\t\t\t<input type=\"text\" class=\"new_option\" name=\"new_option\" value=\"\" style=\"display: none;\" placeholder=\"Enter a unique name for your new template\" />
\t\t</div>
\t</div>
";
        }
        // line 483
        echo "
";
        // line 484
        if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
        if (($_event_trigger_ == "update.agent")) {
            // line 485
            echo "\t<div class=\"builder-type\" title=\"Change 'New Agent Reply' user email\" data-rule-group=\"User Email Templates\"  data-rule-type=\"set_user_email_template_newreply_agent\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.SelectNewOption\">
\t\t<div class=\"builder-options\">
\t\t\t<select class=\"template_name\" name=\"template_name\">
\t\t\t\t<option value=\"NEW\">Create a new template</option>
\t\t\t\t";
            // line 489
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "Template"), "method"), "getCustomEmailsOfType", array(0 => "DeskPRO:emails_user:new-reply-agent.html.twig"), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["tpl"]) {
                // line 490
                echo "\t\t\t\t\t<option value=\"";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo twig_escape_filter($this->env, $_tpl_, "html", null, true);
                echo "\">";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getBaseTemplateName($_tpl_);
                echo "</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tpl'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 492
            echo "\t\t\t</select>
\t\t\t<input type=\"text\" class=\"new_option\" name=\"new_option\" value=\"\" style=\"display: none;\" placeholder=\"Enter a unique name for your new template\" />
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"Change 'New Agent Reply' agent email\" data-rule-group=\"Agent Email Templates\"  data-rule-type=\"set_agent_email_template_newreply_agent\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.SelectNewOption\">
\t\t<div class=\"builder-options\">
\t\t\t<select class=\"template_name\" name=\"template_name\">
\t\t\t\t<option value=\"NEW\">Create a new template</option>
\t\t\t\t";
            // line 501
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "Template"), "method"), "getCustomEmailsOfType", array(0 => "DeskPRO:emails_agent:new-reply-agent.html.twig"), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["tpl"]) {
                // line 502
                echo "\t\t\t\t\t<option value=\"";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo twig_escape_filter($this->env, $_tpl_, "html", null, true);
                echo "\">";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getBaseTemplateName($_tpl_);
                echo "</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tpl'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 504
            echo "\t\t\t</select>
\t\t\t<input type=\"text\" class=\"new_option\" name=\"new_option\" value=\"\" style=\"display: none;\" placeholder=\"Enter a unique name for your new template\" />
\t\t</div>
\t</div>
";
        }
        // line 509
        echo "
";
        // line 510
        if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
        if (($_event_trigger_ == "update.user")) {
            // line 511
            echo "\t<div class=\"builder-type\" title=\"Change 'New User Reply' user email\" data-rule-group=\"User Email Templates\"  data-rule-type=\"set_user_email_template_newreply_user\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.SelectNewOption\">
\t\t<div class=\"builder-options\">
\t\t\t<select class=\"template_name\" name=\"template_name\">
\t\t\t\t<option value=\"NEW\">Create a new template</option>
\t\t\t\t";
            // line 515
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "Template"), "method"), "getCustomEmailsOfType", array(0 => "DeskPRO:emails_user:new-reply-user.html.twig"), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["tpl"]) {
                // line 516
                echo "\t\t\t\t\t<option value=\"";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo twig_escape_filter($this->env, $_tpl_, "html", null, true);
                echo "\">";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getBaseTemplateName($_tpl_);
                echo "</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tpl'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 518
            echo "\t\t\t</select>
\t\t\t<input type=\"text\" class=\"new_option\" name=\"new_option\" value=\"\" style=\"display: none;\" placeholder=\"Enter a unique name for your new template\" />
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"Change 'New User Reply' agent email\" data-rule-group=\"Agent Email Templates\"  data-rule-type=\"set_agent_email_template_newreply_user\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.SelectNewOption\">
\t\t<div class=\"builder-options\">
\t\t\t<select class=\"template_name\" name=\"template_name\">
\t\t\t\t<option value=\"NEW\">Create a new template</option>
\t\t\t\t";
            // line 527
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "Template"), "method"), "getCustomEmailsOfType", array(0 => "DeskPRO:emails_agent:new-reply-user.html.twig"), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["tpl"]) {
                // line 528
                echo "\t\t\t\t\t<option value=\"";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo twig_escape_filter($this->env, $_tpl_, "html", null, true);
                echo "\">";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getBaseTemplateName($_tpl_);
                echo "</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tpl'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 530
            echo "\t\t\t</select>
\t\t\t<input type=\"text\" class=\"new_option\" name=\"new_option\" value=\"\" style=\"display: none;\" placeholder=\"Enter a unique name for your new template\" />
\t\t</div>
\t</div>
";
        }
        // line 535
        echo "
";
        // line 539
        echo "
";
        // line 540
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "EmailGatewayAddress"), "method"), "getEmailAddresses", array(), "method")) {
            // line 541
            echo "\t<div class=\"builder-type\" title=\"Send notifications from email account\" data-rule-type=\"set_gateway_address\" data-rule-group=\"Email Notification Options\">
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"gateway_address_id\">
\t\t\t\t<option value=\"department\">Linked account for department</option>
\t\t\t\t";
            // line 545
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "EmailGatewayAddress"), "method"), "getEmailAddresses", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["addr"]) {
                // line 546
                echo "\t\t\t\t\t<option value=\"";
                if (isset($context["addr"])) { $_addr_ = $context["addr"]; } else { $_addr_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_addr_, "id"), "html", null, true);
                echo "\">";
                if (isset($context["addr"])) { $_addr_ = $context["addr"]; } else { $_addr_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_addr_, "match_pattern"), "html", null, true);
                echo "</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['addr'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 548
            echo "\t\t\t</select>
\t\t</div>
\t</div>
";
        }
        // line 552
        echo "
<div class=\"builder-type\" title=\"Send notifications from name\" data-rule-type=\"set_initial_from_name\" data-rule-group=\"Email Notification Options\">
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"from_name\" />
\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"This sets the default From name on email notifications sent during the current update if one has not already been set before.<br/><br/>You can use variables in this name such as: ";
        // line 556
        echo "{{";
        echo "ticket.department";
        echo "}}";
        echo " or ";
        echo "{{";
        echo "agent.name";
        echo "}}";
        echo " or ";
        echo "{{";
        echo "performer.name";
        echo "}}";
        echo "\"></span>
\t\t<br />
\t\t<select name=\"to_whom\">
\t\t\t<option value=\"0\">On both user and agent emails</option>
\t\t\t<option value=\"agent\">On agent emails only</option>
\t\t\t<option value=\"user\">On user emails only</option>
\t\t</select>
\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 566
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.disable_all_notifications");
        echo "\" data-rule-type=\"disable_notifications\" data-rule-group=\"Email Notification Options\">
\t<div class=\"builder-options\" style=\"display:block\">
\t\t<input type=\"hidden\" name=\"disable_notifications\" value=\"1\" />
\t</div>
</div>

";
        // line 572
        if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
        if (($_event_trigger_ == "update.user")) {
            // line 573
            echo "\t<div class=\"builder-type\" title=\"Send email confirmation auto-reply\" data-rule-type=\"enable_user_notification_new_reply_user\" data-rule-group=\"Email Notification Options\">
\t\t<div class=\"builder-options\" style=\"display:block\">
\t\t\t<input type=\"hidden\" name=\"enablde\" value=\"1\" />
\t\t</div>
\t</div>
";
        }
        // line 579
        echo "
";
        // line 580
        if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
        if (isset($context["event_trigger_master"])) { $_event_trigger_master_ = $context["event_trigger_master"]; } else { $_event_trigger_master_ = null; }
        if ((!twig_in_filter("time.", $_event_trigger_) && ($_event_trigger_master_ != "sla"))) {
            // line 581
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.disable_user_notifications");
            echo "\" data-rule-type=\"disable_user_notifications\" data-rule-group=\"Email Notification Options\">
\t\t<div class=\"builder-options\" style=\"display:block\">
\t\t\t<input type=\"hidden\" name=\"disable_user_notifications\" value=\"1\" />
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"";
            // line 587
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.disable_agent_notifications");
            echo "\" data-rule-type=\"disable_agent_notifications\" data-rule-group=\"Email Notification Options\">
\t\t<div class=\"builder-options\" style=\"display:block\">
\t\t\t<input type=\"hidden\" name=\"disable_agent_notifications\" value=\"1\" />
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"";
            // line 593
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.always_notify_agents");
            echo "\" data-rule-type=\"add_agent_notify\" data-rule-group=\"Email Notification Options\">
\t\t<div class=\"builder-options\" style=\"display:block\">
\t\t\t<select name=\"codes[]\" multiple=\"multiple\" style=\"display: block\">
\t\t\t\t<optgroup label=\"Agents\">
\t\t\t\t\t<option value=\"all_agents\">All Agents</option>
\t\t\t\t\t<option value=\"assigned_agent\">";
            // line 598
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.assigned_agent");
            echo "</option>
\t\t\t\t\t";
            // line 599
            if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "agents"));
            foreach ($context['_seq'] as $context["k"] => $context["v"]) {
                // line 600
                echo "\t\t\t\t\t\t<option value=\"agent.";
                if (isset($context["k"])) { $_k_ = $context["k"]; } else { $_k_ = null; }
                echo twig_escape_filter($this->env, $_k_, "html", null, true);
                echo "\">";
                if (isset($context["v"])) { $_v_ = $context["v"]; } else { $_v_ = null; }
                echo twig_escape_filter($this->env, $_v_, "html", null, true);
                echo "</option>
\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['k'], $context['v'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 602
            echo "\t\t\t\t</optgroup>
\t\t\t\t<optgroup label=\"Teams\">
\t\t\t\t\t<option value=\"assigned_agent_team\">";
            // line 604
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.assigned_team");
            echo "</option>
\t\t\t\t\t";
            // line 605
            if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "agent_teams"));
            foreach ($context['_seq'] as $context["k"] => $context["v"]) {
                // line 606
                echo "\t\t\t\t\t\t<option value=\"agent_team.";
                if (isset($context["k"])) { $_k_ = $context["k"]; } else { $_k_ = null; }
                echo twig_escape_filter($this->env, $_k_, "html", null, true);
                echo "\">";
                if (isset($context["v"])) { $_v_ = $context["v"]; } else { $_v_ = null; }
                echo twig_escape_filter($this->env, $_v_, "html", null, true);
                echo "</option>
\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['k'], $context['v'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 608
            echo "\t\t\t\t</optgroup>
\t\t\t</select>
\t\t</div>
\t</div>
";
        }
        // line 613
        echo "
<div class=\"builder-type\" title=\"Assign a specific 'From' address on all user emails\" data-rule-type=\"set_from_address\" data-rule-group=\"Email Notification Options\">
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"email_address\" />
\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"This assigns a 'From' email address on the ticket. The name will be used for all future emails relating to the ticket unless another trigger assigns a different name.\"></span>
\t</div>
</div>
<div class=\"builder-type\" title=\"Assign a specific 'From' name on all user emails\" data-rule-type=\"set_from_name\" data-rule-group=\"Email Notification Options\">
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"name\" />
\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"This assigns a 'From' name on the ticket. The name will be used for all future emails relating to the ticket unless another trigger assigns a different name.\"></span>
\t</div>
</div>

<div class=\"builder-type\" title=\"Assign a specific 'From' address on all agent emails\" data-rule-type=\"set_from_address_agent\" data-rule-group=\"Email Notification Options\">
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"email_address\" />
\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"This assigns a 'From' address on the ticket. The name will be used for all future emails relating to the ticket unless another trigger assigns a different name.\"></span>
\t</div>
</div>
<div class=\"builder-type\" title=\"Assign a specific 'From' name on all agent emails\" data-rule-type=\"set_from_name_agent\" data-rule-group=\"Email Notification Options\">
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"name\" />
\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"This assigns a 'From' name on the ticket. The name will be used for all future emails relating to the ticket unless another trigger assigns a different name.\"></span>
\t</div>
</div>

";
        // line 643
        echo "
";
        // line 644
        if (isset($context["event_group"])) { $_event_group_ = $context["event_group"]; } else { $_event_group_ = null; }
        if (($_event_group_ != "macro")) {
            // line 645
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.stop_processing_other_triggers");
            echo "\" data-rule-type=\"stop_actions\" data-rule-group=\"Trigger Control\">
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"hidden\" name=\"stop_actions\" value=\"1\" />
\t\t</div>
\t</div>
";
        }
        // line 651
        echo "
";
        // line 655
        echo "
";
        // line 656
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        if ($this->getAttribute($_term_options_, "web_hooks")) {
            // line 657
            echo "\t<div class=\"builder-type\" title=\"Call Web Hook\" data-rule-type=\"call_webhook\" data-rule-group=\"Integrations\">
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"webhook_id\">
\t\t\t";
            // line 660
            if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "web_hooks"));
            foreach ($context['_seq'] as $context["id"] => $context["title"]) {
                // line 661
                echo "\t\t\t\t<option value=\"";
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                echo twig_escape_filter($this->env, $_id_, "html", null, true);
                echo "\">";
                if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
                echo twig_escape_filter($this->env, $_title_, "html", null, true);
                echo "</option>
\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['id'], $context['title'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 663
            echo "\t\t\t</select>
\t\t</div>
\t</div>
";
        }
        // line 667
        echo "
";
        // line 668
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        if ((!twig_test_empty($this->getAttribute($_term_options_, "plugin_actions")))) {
            // line 669
            echo "\t";
            if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "plugin_actions"));
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
            foreach ($context['_seq'] as $context["plugin_event_type"] => $context["object"]) {
                // line 670
                echo "\t\t";
                if (isset($context["object"])) { $_object_ = $context["object"]; } else { $_object_ = null; }
                $template = $this->env->resolveTemplate($this->getAttribute($_object_, "getActionTemplate", array(), "method"));
                $template->display($context);
                // line 671
                echo "\t";
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
            unset($context['_seq'], $context['_iterated'], $context['plugin_event_type'], $context['object'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
        }
        // line 673
        echo "
";
        // line 674
        if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
        if ((($_event_trigger_ == "time.user_waiting") || ($_event_trigger_ == "time.agent_waiting"))) {
            // line 675
            echo "\t<div class=\"builder-type\" title=\"Send auto-close warning email\" data-rule-type=\"send_autoclose_warn_email\" data-rule-group=\"Send Email\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.SelectNewOption\">
\t\t<div class=\"builder-options\">
\t\t\t<select class=\"template_name\" name=\"template_name\">
\t\t\t\t<option value=\"DeskPRO:emails_user:ticket-autoclose-warn.html.twig\">Default Template</option>
\t\t\t\t<option value=\"NEW\">Create a new template</option>
\t\t\t\t";
            // line 680
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "Template"), "method"), "getCustomEmailsOfType", array(0 => "DeskPRO:emails_user:ticket-autoclose-warn.html.twig"), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["tpl"]) {
                // line 681
                echo "\t\t\t\t\t<option value=\"";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo twig_escape_filter($this->env, $_tpl_, "html", null, true);
                echo "\">";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getBaseTemplateName($_tpl_);
                echo "</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tpl'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 683
            echo "\t\t\t</select>
\t\t\t<input type=\"text\" class=\"new_option\" name=\"new_option\" value=\"\" style=\"display: none;\" placeholder=\"Enter a unique name for your new template\" />
\t\t</div>
\t</div>
";
        }
        // line 688
        echo "
</div>
";
    }

    public function getTemplateName()
    {
        return "AdminBundle:TicketTriggers:actions.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1713 => 683,  1700 => 681,  1685 => 674,  1662 => 670,  1643 => 669,  1640 => 668,  1631 => 663,  1618 => 661,  1613 => 660,  1608 => 657,  1605 => 656,  1602 => 655,  1589 => 645,  1586 => 644,  1583 => 643,  1554 => 613,  1547 => 608,  1521 => 602,  1508 => 600,  1499 => 598,  1491 => 593,  1482 => 587,  1472 => 581,  1468 => 580,  1465 => 579,  1457 => 573,  1445 => 566,  1422 => 556,  1416 => 552,  1410 => 548,  1397 => 546,  1392 => 545,  1386 => 541,  1377 => 535,  1370 => 530,  1357 => 528,  1352 => 527,  1341 => 518,  1314 => 510,  1311 => 509,  1275 => 492,  1248 => 484,  1238 => 478,  1225 => 476,  1220 => 475,  1209 => 466,  1185 => 459,  1182 => 458,  1159 => 450,  1154 => 449,  1130 => 438,  1125 => 437,  1101 => 426,  1074 => 413,  1056 => 407,  1046 => 405,  1043 => 404,  1030 => 397,  1027 => 396,  947 => 361,  925 => 352,  913 => 346,  893 => 338,  881 => 335,  847 => 309,  829 => 306,  825 => 304,  1083 => 357,  995 => 383,  984 => 378,  963 => 319,  941 => 358,  851 => 271,  682 => 209,  1365 => 549,  1359 => 545,  1349 => 539,  1336 => 537,  1331 => 536,  1323 => 515,  1319 => 530,  1312 => 527,  1284 => 519,  1272 => 510,  1268 => 509,  1261 => 506,  1251 => 485,  1245 => 483,  1231 => 496,  1207 => 493,  1197 => 491,  1180 => 484,  1173 => 481,  1169 => 480,  1157 => 475,  1147 => 473,  1109 => 466,  1065 => 440,  1059 => 349,  1047 => 428,  1044 => 424,  1033 => 420,  1009 => 386,  991 => 405,  987 => 404,  973 => 395,  931 => 355,  924 => 373,  911 => 298,  906 => 370,  885 => 336,  872 => 354,  855 => 348,  749 => 279,  701 => 237,  594 => 164,  1163 => 476,  1143 => 440,  1087 => 420,  1077 => 509,  1051 => 430,  1037 => 480,  1010 => 476,  999 => 384,  932 => 414,  899 => 405,  895 => 404,  933 => 149,  914 => 133,  909 => 132,  833 => 329,  783 => 235,  755 => 320,  666 => 300,  453 => 187,  639 => 110,  568 => 254,  520 => 249,  657 => 287,  572 => 251,  609 => 459,  20 => 1,  659 => 207,  562 => 192,  548 => 165,  558 => 174,  479 => 206,  589 => 200,  457 => 145,  413 => 172,  953 => 430,  948 => 403,  935 => 394,  929 => 391,  916 => 382,  864 => 365,  844 => 348,  816 => 342,  807 => 370,  801 => 338,  774 => 234,  766 => 283,  737 => 318,  685 => 293,  664 => 231,  635 => 281,  593 => 185,  546 => 236,  532 => 68,  865 => 221,  852 => 347,  838 => 208,  820 => 201,  781 => 333,  764 => 232,  725 => 256,  632 => 283,  602 => 167,  565 => 176,  529 => 282,  505 => 267,  487 => 53,  473 => 221,  1853 => 842,  1849 => 841,  1839 => 837,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 813,  1778 => 812,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 782,  1705 => 781,  1688 => 675,  1681 => 769,  1677 => 768,  1667 => 671,  1656 => 762,  1648 => 757,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 606,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 695,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 540,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 522,  1289 => 520,  1276 => 604,  1263 => 601,  1244 => 596,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 463,  1181 => 561,  1171 => 557,  1158 => 550,  1114 => 428,  1104 => 465,  1072 => 512,  1062 => 439,  1024 => 395,  1014 => 490,  1000 => 328,  990 => 382,  980 => 474,  960 => 466,  918 => 348,  888 => 376,  834 => 307,  673 => 342,  636 => 185,  462 => 192,  454 => 103,  1144 => 542,  1139 => 399,  1131 => 399,  1127 => 397,  1110 => 396,  1092 => 459,  1089 => 390,  1086 => 520,  1084 => 419,  1063 => 387,  1060 => 386,  1055 => 385,  1050 => 384,  1035 => 372,  1019 => 330,  1003 => 263,  959 => 251,  900 => 366,  880 => 276,  870 => 430,  867 => 353,  859 => 362,  848 => 215,  839 => 376,  828 => 357,  823 => 374,  809 => 181,  800 => 315,  797 => 366,  794 => 294,  786 => 174,  740 => 162,  734 => 332,  703 => 354,  693 => 350,  630 => 278,  626 => 195,  614 => 275,  610 => 169,  581 => 247,  564 => 229,  525 => 235,  722 => 162,  697 => 256,  674 => 277,  671 => 425,  577 => 257,  569 => 243,  557 => 189,  502 => 229,  497 => 132,  445 => 197,  729 => 261,  684 => 261,  676 => 299,  669 => 254,  660 => 145,  647 => 198,  643 => 244,  601 => 178,  570 => 156,  522 => 200,  501 => 58,  296 => 67,  374 => 149,  631 => 111,  616 => 208,  608 => 266,  605 => 16,  596 => 102,  574 => 165,  561 => 175,  527 => 142,  433 => 147,  388 => 136,  426 => 142,  383 => 135,  461 => 44,  370 => 147,  395 => 109,  294 => 119,  223 => 65,  220 => 94,  492 => 395,  468 => 132,  444 => 148,  410 => 169,  397 => 135,  377 => 134,  262 => 107,  250 => 139,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 2108,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 680,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 1303,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 572,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 526,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 494,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 538,  1126 => 916,  1106 => 528,  1100 => 897,  1026 => 418,  997 => 822,  983 => 816,  975 => 402,  956 => 318,  939 => 786,  902 => 130,  894 => 364,  879 => 400,  757 => 288,  727 => 316,  716 => 308,  670 => 233,  528 => 180,  476 => 253,  435 => 33,  354 => 126,  341 => 129,  192 => 30,  321 => 154,  243 => 54,  793 => 350,  780 => 311,  758 => 335,  700 => 212,  686 => 243,  652 => 274,  638 => 188,  620 => 174,  545 => 259,  523 => 179,  494 => 55,  459 => 191,  438 => 195,  351 => 104,  347 => 16,  402 => 136,  268 => 103,  430 => 141,  411 => 140,  379 => 23,  322 => 115,  315 => 119,  289 => 113,  284 => 88,  255 => 105,  234 => 70,  1133 => 400,  1124 => 469,  1121 => 56,  1116 => 549,  1113 => 467,  1108 => 376,  1103 => 368,  1098 => 26,  1081 => 415,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 483,  1039 => 345,  1025 => 335,  1021 => 391,  1015 => 412,  1008 => 461,  996 => 406,  989 => 454,  985 => 315,  981 => 257,  977 => 321,  970 => 320,  966 => 375,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 148,  919 => 290,  917 => 289,  908 => 411,  905 => 341,  896 => 280,  891 => 360,  877 => 334,  862 => 267,  857 => 273,  837 => 347,  832 => 250,  827 => 322,  821 => 302,  803 => 179,  778 => 389,  769 => 233,  765 => 297,  753 => 328,  746 => 319,  743 => 268,  735 => 226,  730 => 330,  720 => 363,  717 => 362,  712 => 251,  691 => 233,  678 => 149,  654 => 199,  587 => 14,  576 => 158,  539 => 172,  517 => 140,  471 => 160,  441 => 239,  437 => 39,  418 => 201,  386 => 106,  373 => 133,  304 => 114,  270 => 80,  265 => 102,  229 => 81,  477 => 162,  455 => 36,  448 => 41,  429 => 143,  407 => 138,  399 => 142,  389 => 170,  375 => 128,  358 => 110,  349 => 131,  335 => 120,  327 => 155,  298 => 144,  280 => 88,  249 => 205,  194 => 84,  142 => 46,  344 => 92,  318 => 86,  306 => 104,  295 => 106,  357 => 127,  300 => 121,  286 => 90,  276 => 104,  269 => 103,  254 => 101,  128 => 43,  237 => 71,  165 => 64,  122 => 46,  798 => 337,  770 => 179,  759 => 278,  748 => 271,  731 => 262,  721 => 258,  718 => 313,  708 => 250,  696 => 147,  617 => 188,  590 => 226,  553 => 188,  550 => 187,  540 => 234,  533 => 255,  500 => 397,  493 => 225,  489 => 257,  482 => 223,  467 => 153,  464 => 202,  458 => 123,  452 => 154,  449 => 35,  415 => 32,  382 => 24,  372 => 150,  361 => 129,  356 => 105,  339 => 89,  302 => 150,  285 => 115,  258 => 136,  123 => 36,  108 => 42,  424 => 187,  394 => 139,  380 => 151,  338 => 112,  319 => 125,  316 => 111,  312 => 152,  290 => 118,  267 => 96,  206 => 60,  110 => 53,  240 => 86,  224 => 95,  219 => 63,  217 => 94,  202 => 71,  186 => 70,  170 => 55,  100 => 28,  67 => 18,  14 => 1,  1096 => 425,  1090 => 421,  1088 => 358,  1085 => 456,  1066 => 409,  1034 => 282,  1031 => 281,  1018 => 416,  1013 => 477,  1007 => 274,  1002 => 823,  993 => 266,  986 => 264,  982 => 401,  976 => 399,  971 => 376,  964 => 255,  949 => 388,  946 => 402,  940 => 384,  937 => 357,  928 => 452,  926 => 413,  915 => 299,  912 => 236,  903 => 231,  898 => 440,  892 => 229,  889 => 337,  887 => 281,  884 => 374,  876 => 222,  874 => 215,  871 => 331,  863 => 352,  861 => 274,  858 => 349,  850 => 378,  843 => 206,  840 => 406,  815 => 372,  812 => 297,  808 => 199,  804 => 395,  799 => 295,  791 => 236,  785 => 312,  775 => 184,  771 => 284,  754 => 340,  728 => 317,  726 => 225,  723 => 168,  715 => 105,  711 => 152,  709 => 222,  706 => 302,  698 => 208,  694 => 308,  692 => 155,  689 => 302,  681 => 242,  677 => 149,  675 => 289,  663 => 276,  661 => 200,  650 => 246,  646 => 112,  629 => 183,  627 => 266,  625 => 213,  622 => 212,  598 => 205,  592 => 201,  586 => 199,  575 => 174,  566 => 242,  556 => 73,  554 => 240,  541 => 182,  536 => 241,  515 => 175,  511 => 166,  509 => 173,  488 => 155,  486 => 220,  483 => 341,  465 => 156,  463 => 216,  450 => 116,  432 => 32,  419 => 173,  371 => 127,  362 => 111,  353 => 141,  337 => 137,  333 => 156,  309 => 110,  303 => 122,  299 => 108,  291 => 92,  272 => 109,  261 => 101,  253 => 100,  239 => 82,  235 => 70,  213 => 139,  200 => 52,  198 => 85,  159 => 71,  149 => 56,  146 => 55,  131 => 44,  116 => 42,  79 => 32,  74 => 28,  71 => 29,  836 => 262,  817 => 398,  814 => 319,  811 => 317,  805 => 313,  787 => 333,  779 => 169,  776 => 305,  773 => 347,  761 => 296,  751 => 272,  747 => 325,  742 => 336,  739 => 333,  736 => 265,  724 => 259,  705 => 249,  702 => 601,  688 => 232,  680 => 278,  667 => 232,  662 => 271,  656 => 418,  649 => 285,  644 => 284,  641 => 268,  624 => 109,  613 => 264,  607 => 273,  597 => 270,  591 => 163,  584 => 178,  579 => 159,  563 => 96,  559 => 154,  551 => 243,  547 => 186,  537 => 145,  524 => 141,  512 => 174,  507 => 165,  504 => 164,  498 => 213,  485 => 166,  480 => 50,  472 => 205,  466 => 38,  460 => 152,  447 => 153,  442 => 40,  434 => 212,  428 => 31,  422 => 176,  404 => 149,  368 => 132,  364 => 126,  340 => 170,  334 => 101,  330 => 119,  325 => 116,  292 => 94,  287 => 67,  282 => 104,  279 => 109,  273 => 81,  266 => 78,  256 => 98,  252 => 104,  228 => 80,  218 => 78,  201 => 74,  64 => 26,  51 => 21,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 1705,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 688,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 673,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 667,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 651,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 731,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 725,  1559 => 601,  1552 => 720,  1548 => 597,  1542 => 593,  1529 => 605,  1525 => 604,  1516 => 708,  1512 => 583,  1503 => 599,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 539,  1376 => 523,  1371 => 645,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 543,  1342 => 624,  1337 => 510,  1328 => 516,  1324 => 503,  1317 => 511,  1313 => 499,  1310 => 498,  1304 => 504,  1291 => 502,  1286 => 501,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 490,  1257 => 489,  1254 => 479,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 498,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 492,  1196 => 464,  1192 => 490,  1188 => 456,  1184 => 485,  1179 => 457,  1176 => 453,  1172 => 452,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 561,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 468,  1115 => 424,  1111 => 377,  1107 => 422,  1102 => 898,  1099 => 393,  1095 => 419,  1091 => 418,  1082 => 455,  1079 => 524,  1076 => 359,  1070 => 875,  1057 => 491,  1052 => 406,  1045 => 484,  1040 => 403,  1036 => 283,  1032 => 496,  1028 => 337,  1023 => 393,  1020 => 392,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 821,  979 => 400,  974 => 256,  967 => 391,  962 => 803,  958 => 370,  954 => 389,  950 => 153,  945 => 387,  942 => 460,  938 => 150,  934 => 356,  927 => 147,  923 => 387,  920 => 412,  910 => 353,  901 => 340,  897 => 339,  890 => 343,  886 => 50,  883 => 401,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 340,  841 => 338,  835 => 312,  830 => 249,  826 => 202,  822 => 354,  818 => 301,  813 => 183,  810 => 317,  806 => 180,  802 => 198,  795 => 314,  792 => 239,  789 => 313,  784 => 286,  782 => 187,  777 => 291,  772 => 289,  768 => 334,  763 => 327,  760 => 329,  756 => 230,  752 => 176,  745 => 281,  741 => 267,  738 => 379,  732 => 171,  719 => 279,  714 => 264,  710 => 310,  704 => 267,  699 => 245,  695 => 234,  690 => 263,  687 => 210,  683 => 346,  679 => 298,  672 => 276,  668 => 201,  665 => 285,  658 => 227,  645 => 225,  640 => 224,  634 => 267,  628 => 214,  623 => 107,  619 => 298,  611 => 268,  606 => 263,  603 => 206,  599 => 262,  595 => 132,  583 => 263,  580 => 195,  573 => 157,  560 => 75,  543 => 147,  538 => 69,  534 => 233,  530 => 168,  526 => 229,  521 => 226,  518 => 233,  514 => 221,  510 => 227,  503 => 169,  496 => 226,  490 => 167,  484 => 394,  474 => 161,  470 => 231,  446 => 185,  440 => 146,  436 => 148,  431 => 37,  425 => 35,  416 => 30,  412 => 110,  408 => 141,  403 => 194,  400 => 225,  396 => 28,  392 => 139,  385 => 25,  381 => 133,  367 => 147,  363 => 18,  359 => 100,  355 => 132,  350 => 140,  346 => 130,  343 => 134,  328 => 135,  324 => 125,  313 => 105,  307 => 151,  301 => 124,  288 => 116,  283 => 111,  271 => 103,  257 => 148,  251 => 75,  238 => 71,  233 => 90,  195 => 121,  191 => 35,  187 => 53,  183 => 52,  130 => 49,  88 => 32,  76 => 30,  115 => 41,  95 => 28,  655 => 270,  651 => 232,  648 => 269,  637 => 273,  633 => 196,  621 => 462,  618 => 277,  615 => 268,  604 => 186,  600 => 271,  588 => 162,  585 => 161,  582 => 160,  571 => 242,  567 => 193,  555 => 239,  552 => 238,  549 => 237,  544 => 230,  542 => 290,  535 => 171,  531 => 143,  519 => 64,  516 => 63,  513 => 228,  508 => 230,  506 => 59,  499 => 241,  495 => 239,  491 => 54,  481 => 161,  478 => 235,  475 => 184,  469 => 196,  456 => 204,  451 => 149,  443 => 194,  439 => 149,  427 => 143,  423 => 141,  420 => 140,  409 => 137,  405 => 30,  401 => 164,  391 => 134,  387 => 132,  384 => 131,  378 => 154,  365 => 131,  360 => 128,  348 => 122,  336 => 132,  332 => 127,  329 => 109,  323 => 135,  310 => 124,  305 => 112,  277 => 170,  274 => 102,  263 => 97,  259 => 102,  247 => 138,  244 => 137,  241 => 87,  222 => 79,  210 => 122,  207 => 88,  204 => 74,  184 => 28,  181 => 60,  167 => 53,  157 => 60,  96 => 46,  421 => 142,  417 => 139,  414 => 138,  406 => 130,  398 => 165,  393 => 162,  390 => 153,  376 => 138,  369 => 19,  366 => 174,  352 => 140,  345 => 113,  342 => 160,  331 => 125,  326 => 87,  320 => 121,  317 => 100,  314 => 126,  311 => 85,  308 => 116,  297 => 112,  293 => 114,  281 => 146,  278 => 111,  275 => 113,  264 => 104,  260 => 107,  248 => 99,  245 => 73,  242 => 96,  231 => 52,  227 => 96,  215 => 88,  212 => 75,  209 => 125,  197 => 51,  177 => 118,  171 => 55,  161 => 68,  132 => 34,  121 => 43,  105 => 50,  99 => 46,  81 => 23,  77 => 19,  180 => 64,  176 => 45,  156 => 70,  143 => 50,  139 => 104,  118 => 41,  189 => 88,  185 => 61,  173 => 117,  166 => 73,  152 => 60,  174 => 63,  164 => 58,  154 => 113,  150 => 68,  137 => 49,  133 => 48,  127 => 102,  107 => 35,  102 => 34,  83 => 25,  78 => 23,  53 => 10,  23 => 6,  42 => 7,  138 => 45,  134 => 45,  109 => 39,  103 => 29,  97 => 27,  94 => 33,  84 => 24,  75 => 23,  69 => 16,  66 => 16,  54 => 9,  44 => 7,  230 => 74,  226 => 80,  203 => 86,  193 => 66,  188 => 57,  182 => 56,  178 => 59,  168 => 62,  163 => 115,  160 => 68,  155 => 55,  148 => 48,  145 => 47,  140 => 65,  136 => 39,  125 => 16,  120 => 38,  113 => 43,  101 => 37,  92 => 41,  89 => 27,  85 => 26,  73 => 20,  62 => 21,  59 => 15,  56 => 14,  41 => 5,  126 => 47,  119 => 65,  111 => 98,  106 => 38,  98 => 35,  93 => 42,  86 => 27,  70 => 13,  60 => 15,  28 => 2,  36 => 4,  114 => 54,  104 => 30,  91 => 26,  80 => 29,  63 => 30,  58 => 25,  40 => 7,  34 => 4,  45 => 7,  61 => 16,  55 => 11,  48 => 20,  39 => 5,  35 => 2,  31 => 2,  26 => 2,  21 => 2,  46 => 7,  29 => 3,  57 => 14,  50 => 9,  47 => 8,  38 => 3,  33 => 3,  49 => 11,  32 => 3,  246 => 102,  236 => 87,  232 => 129,  225 => 82,  221 => 63,  216 => 76,  214 => 77,  211 => 111,  208 => 74,  205 => 87,  199 => 70,  196 => 57,  190 => 54,  179 => 79,  175 => 76,  172 => 77,  169 => 41,  162 => 61,  158 => 50,  153 => 49,  151 => 43,  147 => 66,  144 => 51,  141 => 55,  135 => 35,  129 => 35,  124 => 42,  117 => 32,  112 => 31,  90 => 36,  87 => 25,  82 => 31,  72 => 19,  68 => 28,  65 => 17,  52 => 12,  43 => 6,  37 => 5,  30 => 6,  27 => 2,  25 => 65,  24 => 3,  22 => 34,  19 => 1,);
    }
}
