<?php

/* DeskPRO:Common:ticket-trigger-actions.html.twig */
class __TwigTemplate_39b97074a5406d7f2e01bf0168df356e extends \Application\DeskPRO\Twig\Template
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
\t\t\t<option value=\"-1\">Me</option>
\t\t\t<optgroup label=\"Agents\">
\t\t\t\t";
        // line 26
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "agents"), "getAgents", array(), "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["agent"]) {
            // line 27
            echo "\t\t\t\t\t<option value=\"";
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "id"), "html", null, true);
            echo "\">";
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "display_name"), "html", null, true);
            echo "</option>
\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['agent'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 29
        echo "\t\t\t</optgroup>
\t\t</select>
\t</div>
</div>

<div class=\"builder-type\" title=\"Add agent followers\" data-rule-type=\"add_participants\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\">
\t\t<select name=\"add_participants[]\" multiple=\"multiple\">
\t\t\t";
        // line 37
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "agents"), "getAgents", array(), "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["agent"]) {
            // line 38
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
        // line 40
        echo "\t\t</select>
\t</div>
</div>

<div class=\"builder-type\" title=\"CC users\" data-rule-type=\"add_cc\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"add_emails\" placeholder=\"Enter email addresses separated by a comma\" />
\t</div>
</div>

";
        // line 50
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (twig_length_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "agent_teams"), "getTeamNames", array(), "method"))) {
            // line 51
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.set_agent_team");
            echo "\" data-rule-type=\"agent_team\" data-rule-group=\"Ticket\">
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"agent_team\">
\t\t\t\t<option value=\"-1\">My Team</option>
\t\t\t\t<optgroup label=\"Teams\">
\t\t\t\t\t";
            // line 56
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "agent_teams"), "getTeamNames", array(), "method"));
            foreach ($context['_seq'] as $context["id"] => $context["title"]) {
                // line 57
                echo "\t\t\t\t\t\t<option value=\"";
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                echo twig_escape_filter($this->env, $_id_, "html", null, true);
                echo "\">";
                if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
                echo twig_escape_filter($this->env, $_title_, "html", null, true);
                echo "</option>
\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['id'], $context['title'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 59
            echo "\t\t\t\t</optgroup>
\t\t\t</select>
\t\t</div>
\t</div>
";
        }
        // line 64
        echo "
<div class=\"builder-type\" title=\"";
        // line 65
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.set_status");
        echo "\" data-rule-type=\"status\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\">
\t\t<select name=\"status\">
\t\t\t<option value=\"awaiting_agent\">";
        // line 68
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_awaiting_agent");
        echo "</option>
\t\t\t<option value=\"awaiting_user\">";
        // line 69
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_awaiting_user");
        echo "</option>
\t\t\t<option value=\"resolved\">";
        // line 70
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

";
        // line 84
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (((!$this->getAttribute($this->getAttribute($this->getAttribute($_app_, "user"), "getHelper", array(0 => "PermissionsManager"), "method"), "get", array(0 => "TicketChecker"), "method")) || $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($_app_, "user"), "getHelper", array(0 => "PermissionsManager"), "method"), "get", array(0 => "TicketChecker"), "method"), "canDeleteAny", array(), "method"))) {
            // line 85
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.delete_ticket");
            echo "\" data-rule-type=\"delete\" data-rule-group=\"Ticket\">
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"hidden\" name=\"delete_ticket\" value=\"1\" />
\t\t</div>
\t</div>
";
        }
        // line 91
        echo "
<div class=\"builder-type\" title=\"";
        // line 92
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.set_department");
        echo "\" data-rule-type=\"department\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\">
\t\t<select name=\"department\">
\t\t\t<option value=\"email_account\">Linked department for email account</option>
\t\t\t";
        // line 96
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "departments"), "getRootNodes", array(), "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["dep"]) {
            if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
            if ($this->getAttribute($_dep_, "is_tickets_enabled")) {
                // line 97
                echo "\t\t\t\t";
                if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                if (twig_length_filter($this->env, $this->getAttribute($_dep_, "children"))) {
                    // line 98
                    echo "\t\t\t\t\t<optgroup label=\"";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                    echo "\">
\t\t\t\t\t\t";
                    // line 99
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($_dep_, "children"));
                    foreach ($context['_seq'] as $context["_key"] => $context["subdep"]) {
                        // line 100
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
                    // line 102
                    echo "\t\t\t\t\t</optgroup>
\t\t\t\t";
                } else {
                    // line 104
                    echo "\t\t\t\t\t<option value=\"";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "id"), "html", null, true);
                    echo "\">";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                    echo "</option>
\t\t\t\t";
                }
                // line 106
                echo "\t\t\t";
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['dep'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 107
        echo "\t\t</select>
\t</div>
</div>

";
        // line 111
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "Language"), "method"), "isLangSystemEnabled", array(), "method")) {
            // line 112
            echo "\t<div class=\"builder-type\" title=\"Set Language\" data-rule-type=\"language\" data-rule-group=\"Ticket\">
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"language\">
\t\t\t\t";
            // line 115
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "Language"), "method"), "getAll", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["lang"]) {
                // line 116
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
            // line 118
            echo "\t\t\t</select>
\t\t</div>
\t</div>
";
        }
        // line 122
        echo "
";
        // line 123
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_ticket_category"), "method")) {
            // line 124
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.set_category");
            echo "\" data-rule-type=\"category\" data-rule-group=\"Ticket\">
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"category\">
\t\t\t\t<option value=\"0\">None</option>
\t\t\t\t";
            // line 128
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "TicketCategory"), "method"), "getRootNodes", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["cat"]) {
                // line 129
                echo "\t\t\t\t\t";
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                if (twig_length_filter($this->env, $this->getAttribute($_cat_, "children"))) {
                    // line 130
                    echo "\t\t\t\t\t\t<optgroup label=\"";
                    if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
                    echo "\">
\t\t\t\t\t\t\t";
                    // line 131
                    if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($_cat_, "children"));
                    foreach ($context['_seq'] as $context["_key"] => $context["subcat"]) {
                        // line 132
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
                    // line 134
                    echo "\t\t\t\t\t\t</optgroup>
\t\t\t\t\t";
                } else {
                    // line 136
                    echo "\t\t\t\t\t\t<option value=\"";
                    if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "id"), "html", null, true);
                    echo "\">";
                    if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
                    echo "</option>
\t\t\t\t\t";
                }
                // line 138
                echo "\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['cat'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 139
            echo "\t\t\t</select>
\t\t</div>
\t</div>
";
        }
        // line 143
        echo "
";
        // line 144
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_product"), "method")) {
            // line 145
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.set_product");
            echo "\" data-rule-type=\"product\" data-rule-group=\"Ticket\">
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"product\">
\t\t\t\t<option value=\"0\">None</option>
\t\t\t\t";
            // line 149
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "products"), "getNames", array(), "method"));
            foreach ($context['_seq'] as $context["id"] => $context["title"]) {
                // line 150
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
            // line 152
            echo "\t\t\t</select>
\t\t</div>
\t</div>
";
        }
        // line 156
        echo "
";
        // line 157
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_ticket_priority"), "method")) {
            // line 158
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.set_priority");
            echo "\" data-rule-type=\"priority\" data-rule-group=\"Ticket\">
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"priority\">
\t\t\t\t<option value=\"0\">None</option>
\t\t\t\t";
            // line 162
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "TicketPriority"), "method"), "getNames", array(), "method"));
            foreach ($context['_seq'] as $context["id"] => $context["name"]) {
                // line 163
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
            // line 165
            echo "\t\t\t</select>
\t\t</div>
\t</div>
";
        }
        // line 169
        echo "
";
        // line 170
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_ticket_workflow"), "method")) {
            // line 171
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.set_workflow");
            echo "\" data-rule-type=\"workflow\" data-rule-group=\"Ticket\">
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"workflow\">
\t\t\t\t<option value=\"0\">None</option>
\t\t\t\t";
            // line 175
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "TicketWorkflow"), "method"), "getAll", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["work"]) {
                // line 176
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
            // line 178
            echo "\t\t\t</select>
\t\t</div>
\t</div>
";
        }
        // line 182
        echo "
<div class=\"builder-type\" title=\"";
        // line 183
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.add_labels");
        echo "\" data-rule-type=\"add_labels\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.LabelsTerm\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\">
\t\t";
        // line 185
        $this->env->loadTemplate("DeskPRO:Common:rulebuilder-labels-options.html.twig")->display($context);
        // line 186
        echo "\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 189
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.remove_labels");
        echo "\" data-rule-type=\"remove_labels\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.LabelsTerm\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\">
\t\t";
        // line 191
        $this->env->loadTemplate("DeskPRO:Common:rulebuilder-labels-options.html.twig")->display($context);
        // line 192
        echo "\t</div>
</div>

<div class=\"builder-type\" title=\"Add organization managers to ticket\" data-rule-type=\"add_org_managers\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\" style=\"display:block\">
\t\t<input type=\"hidden\" name=\"x\" value=\"DP_ALLOW_BLANK\" />
\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 201
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.set_flag");
        echo "\" data-rule-type=\"flag\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\">
\t\t<select name=\"flag\">
\t\t\t<option value=\"none\">";
        // line 204
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none");
        echo "</option>
\t\t\t<option value=\"blue\">";
        // line 205
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.flag_blue");
        echo "</option>
\t\t\t<option value=\"green\">";
        // line 206
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.flag_green");
        echo "</option>
\t\t\t<option value=\"orange\">";
        // line 207
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.flag_orange");
        echo "</option>
\t\t\t<option value=\"pink\">";
        // line 208
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.flag_pink");
        echo "</option>
\t\t\t<option value=\"purple\">";
        // line 209
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.flag_purple");
        echo "</option>
\t\t\t<option value=\"red\">";
        // line 210
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.flag_red");
        echo "</option>
\t\t\t<option value=\"yellow\">";
        // line 211
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.flag_yellow");
        echo "</option>
\t\t</select>
\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 216
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.increase_decrease_urgency");
        echo "\" data-rule-type=\"urgency\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"num\" /><small>";
        // line 218
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.urgency_enter_negative_number");
        echo "</small>
\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 222
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.set_urgency");
        echo "\" data-rule-type=\"urgency_set\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"num\" style=\"width: 50px\" />
\t\t";
        // line 225
        if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
        if (($_event_trigger_ != "new_ticket")) {
            // line 226
            echo "\t\t<select name=\"allow_lower\">
\t\t\t<option value=\"0\">";
            // line 227
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.notice_only_when_urgency_lower");
            echo "</option>
\t\t\t<option value=\"1\">";
            // line 228
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.always_set_this_urgency");
            echo "</option>
\t\t</select>
\t\t";
        }
        // line 231
        echo "\t</div>
</div>

<div class=\"builder-type\" title=\"Set Subject\" data-rule-type=\"subject\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"subject\" />
\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 240
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.add_ticket_reply");
        echo "\" data-rule-type=\"reply\" data-rule-group=\"Ticket\">
\t<div class=\"builder-options\">
\t\t<textarea name=\"reply_text\" cols=\"80\" rows=\"4\" style=\"width: 90%;\"></textarea>
\t\t<div style=\"padding-top: 3px;\">
\t\t\t<select name=\"reply_pos\">
\t\t\t\t<option value=\"append\">Append to existing reply text</option>
\t\t\t\t<option value=\"prepend\">Prepend to existing reply text</option>
\t\t\t\t<option value=\"overwrite\">Overwrite existing reply text</option>
\t\t\t</select>
\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"This defines where the above message is set if this macro is used during a reply and you have already entered text into the reply box\"></span>
\t\t</div>
\t</div>
</div>

";
        // line 254
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (twig_length_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_app_, "user"), "Agent"), "getGroupedSnippets", array(), "method"))) {
            // line 255
            echo "\t<div class=\"builder-type\" title=\"Add reply from snippet\" data-rule-type=\"reply_snippet\" data-rule-group=\"Ticket\">
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"snippet_id\" class=\"select2\">
\t\t\t\t";
            // line 258
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($this->getAttribute($_app_, "user"), "Agent"), "getGroupedSnippets", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["info"]) {
                if (isset($context["info"])) { $_info_ = $context["info"]; } else { $_info_ = null; }
                if ($this->getAttribute($_info_, "snippets")) {
                    // line 259
                    echo "\t\t\t\t\t<optgroup label=\"";
                    if (isset($context["info"])) { $_info_ = $context["info"]; } else { $_info_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_info_, "category"), "title"), "html", null, true);
                    echo "\">
\t\t\t\t\t\t";
                    // line 260
                    if (isset($context["info"])) { $_info_ = $context["info"]; } else { $_info_ = null; }
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($_info_, "snippets"));
                    foreach ($context['_seq'] as $context["_key"] => $context["snippet"]) {
                        // line 261
                        echo "\t\t\t\t\t\t\t<option value=\"";
                        if (isset($context["snippet"])) { $_snippet_ = $context["snippet"]; } else { $_snippet_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_snippet_, "id"), "html", null, true);
                        echo "\">";
                        if (isset($context["snippet"])) { $_snippet_ = $context["snippet"]; } else { $_snippet_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_snippet_, "title"), "html", null, true);
                        echo "</option>
\t\t\t\t\t\t";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['snippet'], $context['_parent'], $context['loop']);
                    $context = array_merge($_parent, array_intersect_key($context, $_parent));
                    // line 263
                    echo "\t\t\t\t\t</optgroup>
\t\t\t\t";
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['info'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 265
            echo "\t\t\t</select>
\t\t\t<div style=\"padding-top: 3px;\">
\t\t\t\t<select name=\"reply_pos\">
\t\t\t\t\t<option value=\"append\">Append to existing reply text</option>
\t\t\t\t\t<option value=\"prepend\">Prepend to existing reply text</option>
\t\t\t\t\t<option value=\"overwrite\">Overwrite existing reply text</option>
\t\t\t\t</select>
\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"This defines where the above message is set if this macro is used during a reply and you have already entered text into the reply box\"></span>
\t\t\t</div>
\t\t</div>
\t</div>
";
        }
        // line 277
        echo "
";
        // line 278
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getTicketFieldManager", array(), "method"), "getDisplayArray", array(), "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["field"]) {
            // line 279
            echo "\t";
            if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
            if ($this->getAttribute($this->getAttribute($_field_, "field_def"), "isFormField", array(), "method")) {
                // line 280
                echo "\t\t<div class=\"builder-type\" title=\"";
                if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.set_field_name", array("field" => $this->getAttribute($this->getAttribute($_field_, "field_def"), "title")));
                echo "\" data-rule-group=\"Ticket\" data-rule-type=\"ticket_field[";
                if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_field_, "field_def"), "id"), "html", null, true);
                echo "]\">
\t\t\t<div class=\"builder-options\">
\t\t\t\t";
                // line 282
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
        // line 287
        echo "
";
        // line 291
        echo "
";
        // line 292
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getPersonFieldManager", array(), "method"), "getDisplayArray", array(), "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["field"]) {
            // line 293
            echo "\t";
            if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
            if ($this->getAttribute($this->getAttribute($_field_, "field_def"), "isFormField", array(), "method")) {
                // line 294
                echo "\t\t<div class=\"builder-type\" title=\"";
                if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.set_user_field_name", array("field" => $this->getAttribute($this->getAttribute($_field_, "field_def"), "title")));
                echo "\" data-rule-group=\"Person\"  data-rule-type=\"people_field[";
                if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_field_, "field_def"), "id"), "html", null, true);
                echo "]\">
\t\t\t<div class=\"builder-options\">
\t\t\t\t";
                // line 296
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
        // line 301
        echo "
";
        // line 305
        echo "
";
        // line 306
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        if ($this->getAttribute($_term_options_, "web_hooks")) {
            // line 307
            echo "\t<div class=\"builder-type\" title=\"Call Web Hook\" data-rule-type=\"call_webhook\" data-rule-group=\"Integrations\">
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"webhook_id\">
\t\t\t";
            // line 310
            if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "web_hooks"));
            foreach ($context['_seq'] as $context["id"] => $context["title"]) {
                // line 311
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
            // line 313
            echo "\t\t\t</select>
\t\t</div>
\t</div>
";
        }
        // line 317
        echo "
";
        // line 318
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        if ((!twig_test_empty($this->getAttribute($_term_options_, "plugin_actions")))) {
            // line 319
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
                // line 320
                echo "\t\t";
                if (isset($context["object"])) { $_object_ = $context["object"]; } else { $_object_ = null; }
                $template = $this->env->resolveTemplate($this->getAttribute($_object_, "getActionTemplate", array(), "method"));
                $template->display($context);
                // line 321
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
        // line 323
        echo "
<div class=\"builder-type\" title=\"Close ticket tab\" data-rule-type=\"close_ticket_tab\">
\t<div class=\"builder-options\">
\t\t<input type=\"hidden\" name=\"do\" value=\"1\" />
\t\tClose the ticket tab after applying macro
\t</div>
</div>

</div>
";
    }

    public function getTemplateName()
    {
        return "DeskPRO:Common:ticket-trigger-actions.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  836 => 320,  817 => 319,  814 => 318,  811 => 317,  805 => 313,  787 => 310,  779 => 306,  776 => 305,  773 => 301,  761 => 296,  751 => 294,  747 => 293,  742 => 292,  739 => 291,  736 => 287,  724 => 282,  705 => 278,  702 => 277,  688 => 265,  680 => 263,  667 => 261,  662 => 260,  656 => 259,  649 => 258,  644 => 255,  641 => 254,  624 => 240,  613 => 231,  607 => 228,  597 => 225,  591 => 222,  584 => 218,  579 => 216,  563 => 209,  559 => 208,  551 => 206,  547 => 205,  537 => 201,  524 => 191,  512 => 185,  507 => 183,  504 => 182,  498 => 178,  485 => 176,  480 => 175,  472 => 171,  466 => 169,  460 => 165,  447 => 163,  442 => 162,  434 => 158,  428 => 156,  422 => 152,  404 => 149,  368 => 136,  364 => 134,  340 => 131,  334 => 130,  330 => 129,  325 => 128,  292 => 116,  287 => 115,  282 => 112,  279 => 111,  273 => 107,  266 => 106,  256 => 104,  252 => 102,  228 => 99,  218 => 97,  201 => 91,  64 => 27,  51 => 21,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 793,  2042 => 790,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 774,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 745,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 707,  1829 => 706,  1827 => 705,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 689,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 676,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 666,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 638,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 617,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 611,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 602,  1559 => 601,  1552 => 598,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 584,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 573,  1477 => 567,  1473 => 566,  1467 => 562,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 520,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 511,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 481,  1257 => 480,  1254 => 479,  1250 => 478,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 460,  1196 => 458,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 452,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 445,  1146 => 444,  1137 => 437,  1123 => 426,  1119 => 425,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 421,  1099 => 420,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 412,  1076 => 411,  1070 => 407,  1057 => 405,  1052 => 404,  1045 => 399,  1040 => 397,  1036 => 396,  1032 => 395,  1028 => 394,  1023 => 393,  1020 => 392,  1016 => 391,  1012 => 390,  1005 => 387,  1001 => 386,  998 => 385,  992 => 381,  979 => 379,  974 => 378,  967 => 373,  962 => 371,  958 => 370,  954 => 369,  950 => 368,  945 => 367,  942 => 366,  938 => 365,  934 => 364,  927 => 361,  923 => 360,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 341,  868 => 328,  856 => 323,  853 => 319,  849 => 318,  845 => 317,  841 => 321,  835 => 312,  830 => 310,  826 => 309,  822 => 308,  818 => 307,  813 => 306,  810 => 305,  806 => 304,  802 => 303,  795 => 300,  792 => 311,  789 => 298,  784 => 295,  782 => 307,  777 => 291,  772 => 289,  768 => 288,  763 => 287,  760 => 286,  756 => 285,  752 => 284,  745 => 281,  741 => 280,  738 => 279,  732 => 275,  719 => 273,  714 => 280,  710 => 279,  704 => 267,  699 => 265,  695 => 264,  690 => 263,  687 => 262,  683 => 261,  679 => 260,  672 => 257,  668 => 256,  665 => 255,  658 => 250,  645 => 248,  640 => 247,  634 => 244,  628 => 240,  623 => 238,  619 => 237,  611 => 235,  606 => 234,  603 => 227,  599 => 232,  595 => 231,  583 => 227,  580 => 226,  573 => 221,  560 => 219,  543 => 204,  538 => 209,  534 => 208,  530 => 207,  526 => 192,  521 => 205,  518 => 204,  514 => 186,  510 => 202,  503 => 199,  496 => 197,  490 => 193,  484 => 192,  474 => 190,  470 => 188,  446 => 185,  440 => 184,  436 => 183,  431 => 157,  425 => 178,  416 => 175,  412 => 174,  408 => 173,  403 => 172,  400 => 171,  396 => 145,  392 => 169,  385 => 166,  381 => 165,  367 => 156,  363 => 155,  359 => 154,  355 => 153,  350 => 150,  346 => 149,  343 => 148,  328 => 139,  324 => 138,  313 => 134,  307 => 132,  301 => 128,  288 => 126,  283 => 125,  271 => 119,  257 => 114,  251 => 110,  238 => 108,  233 => 100,  195 => 90,  191 => 85,  187 => 88,  183 => 87,  130 => 58,  88 => 33,  76 => 27,  115 => 48,  95 => 39,  655 => 233,  651 => 232,  648 => 231,  637 => 223,  633 => 222,  621 => 219,  618 => 218,  615 => 236,  604 => 209,  600 => 226,  588 => 228,  585 => 204,  582 => 203,  571 => 211,  567 => 210,  555 => 207,  552 => 190,  549 => 215,  544 => 186,  542 => 185,  535 => 181,  531 => 180,  519 => 189,  516 => 176,  513 => 175,  508 => 172,  506 => 171,  499 => 198,  495 => 166,  491 => 165,  481 => 162,  478 => 161,  475 => 160,  469 => 170,  456 => 154,  451 => 186,  443 => 148,  439 => 147,  427 => 144,  423 => 143,  420 => 176,  409 => 150,  405 => 133,  401 => 132,  391 => 129,  387 => 128,  384 => 139,  378 => 138,  365 => 121,  360 => 120,  348 => 114,  336 => 111,  332 => 140,  329 => 109,  323 => 105,  310 => 133,  305 => 118,  277 => 92,  274 => 91,  263 => 83,  259 => 82,  247 => 79,  244 => 78,  241 => 77,  222 => 98,  210 => 97,  207 => 96,  204 => 92,  184 => 53,  181 => 52,  167 => 69,  157 => 65,  96 => 38,  421 => 153,  417 => 152,  414 => 151,  406 => 148,  398 => 144,  393 => 144,  390 => 143,  376 => 138,  369 => 137,  366 => 136,  352 => 115,  345 => 132,  342 => 132,  331 => 129,  326 => 128,  320 => 137,  317 => 124,  314 => 123,  311 => 122,  308 => 123,  297 => 97,  293 => 96,  281 => 93,  278 => 110,  275 => 120,  264 => 116,  260 => 115,  248 => 97,  245 => 96,  242 => 95,  231 => 87,  227 => 86,  215 => 83,  212 => 82,  209 => 81,  197 => 60,  177 => 67,  171 => 70,  161 => 60,  132 => 50,  121 => 42,  105 => 40,  99 => 39,  81 => 27,  77 => 29,  180 => 76,  176 => 75,  156 => 64,  143 => 46,  139 => 56,  118 => 44,  189 => 56,  185 => 69,  173 => 63,  166 => 68,  152 => 62,  174 => 66,  164 => 78,  154 => 64,  150 => 55,  137 => 48,  133 => 59,  127 => 57,  107 => 42,  102 => 37,  83 => 31,  78 => 30,  53 => 18,  23 => 2,  42 => 6,  138 => 52,  134 => 57,  109 => 34,  103 => 40,  97 => 29,  94 => 33,  84 => 32,  75 => 20,  69 => 25,  66 => 24,  54 => 11,  44 => 21,  230 => 69,  226 => 68,  203 => 78,  193 => 71,  188 => 84,  182 => 63,  178 => 71,  168 => 64,  163 => 68,  160 => 77,  155 => 55,  148 => 56,  145 => 67,  140 => 46,  136 => 45,  125 => 45,  120 => 51,  113 => 39,  101 => 33,  92 => 38,  89 => 27,  85 => 35,  73 => 19,  62 => 22,  59 => 26,  56 => 13,  41 => 8,  126 => 24,  119 => 36,  111 => 43,  106 => 39,  98 => 32,  93 => 17,  86 => 14,  70 => 25,  60 => 8,  28 => 8,  36 => 5,  114 => 21,  104 => 19,  91 => 38,  80 => 15,  63 => 15,  58 => 12,  40 => 8,  34 => 14,  45 => 19,  61 => 23,  55 => 6,  48 => 20,  39 => 16,  35 => 4,  31 => 11,  26 => 6,  21 => 1,  46 => 6,  29 => 3,  57 => 22,  50 => 10,  47 => 6,  38 => 5,  33 => 4,  49 => 20,  32 => 8,  246 => 90,  236 => 84,  232 => 82,  225 => 102,  221 => 101,  216 => 79,  214 => 98,  211 => 96,  208 => 77,  205 => 70,  199 => 91,  196 => 71,  190 => 61,  179 => 66,  175 => 82,  172 => 54,  169 => 53,  162 => 67,  158 => 57,  153 => 45,  151 => 44,  147 => 59,  144 => 41,  141 => 49,  135 => 51,  129 => 56,  124 => 37,  117 => 50,  112 => 40,  90 => 16,  87 => 37,  82 => 24,  72 => 26,  68 => 18,  65 => 23,  52 => 11,  43 => 13,  37 => 5,  30 => 10,  27 => 3,  25 => 3,  24 => 2,  22 => 2,  19 => 1,);
    }
}
