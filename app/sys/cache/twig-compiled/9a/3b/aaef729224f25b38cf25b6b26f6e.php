<?php

/* AdminBundle:TicketTriggers:criteria.html.twig */
class __TwigTemplate_9a3baaef729224f25b38cf25b6b26f6e extends \Application\DeskPRO\Twig\Template
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
        if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
        if (isset($context["classname"])) { $_classname_ = $context["classname"]; } else { $_classname_ = null; }
        if (($_id_ || $_classname_)) {
            // line 2
            echo "\t";
            // line 3
            echo "\t<div ";
            if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
            if ($_id_) {
                echo "id=\"";
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                echo twig_escape_filter($this->env, $_id_, "html", null, true);
                echo "\"";
            }
            echo " class=\"";
            if (isset($context["classname"])) { $_classname_ = $context["classname"]; } else { $_classname_ = null; }
            echo twig_escape_filter($this->env, $_classname_, "html", null, true);
            echo "\" style=\"display:none\">
\t<div class=\"row\">
\t\t<div class=\"term\">
\t\t\t<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\" class=\"term-table\"><tbody><tr>
\t\t\t\t<td style=\"vertical-align: middle; text-align: center;\" width=\"11\"><div class=\"builder-remove\">-</div></td>
\t\t\t\t<td class=\"builder-controls\" style=\"vertical-align: middle;\">
\t\t\t\t\t<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\"><tbody><tr>
\t\t\t\t\t\t<td style=\"vertical-align: middle;\" width=\"10\" nowrap=\"nowrap\"><div class=\"builder-type-choice\"></div></td>
\t\t\t\t\t\t<td style=\"vertical-align: middle;\" width=\"10\" nowrap=\"nowrap\"><div class=\"builder-op\"></div></td>
\t\t\t\t\t\t<td style=\"vertical-align: middle;\"><div class=\"builder-options\"></div></td>
\t\t\t\t\t</tr></tbody></table>
\t\t\t\t</td>
\t\t\t</tr></tbody></table>
\t\t</div>
\t</div>
\t<div class=\"builder-type-choice\" title=\"";
            // line 18
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.choose_criteria");
            echo "\" data-rule-type=\"\">
\t</div>
";
        }
        // line 21
        echo "
";
        // line 25
        echo "
";
        // line 26
        if (isset($context["event_trigger_sub"])) { $_event_trigger_sub_ = $context["event_trigger_sub"]; } else { $_event_trigger_sub_ = null; }
        if (isset($context["trigger"])) { $_trigger_ = $context["trigger"]; } else { $_trigger_ = null; }
        if ((($_event_trigger_sub_ == "api") || ($this->getAttribute($_trigger_, "getEventTriggerPath", array(0 => 2), "method") == "api"))) {
            // line 27
            echo "\t<div class=\"builder-type\" title=\"API Key\" data-rule-type=\"api_key\" data-term-type=\"GenericMenuTerm\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"is\">";
            // line 30
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 31
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"api_key\">
\t\t\t\t";
            // line 36
            if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "api_keys"));
            foreach ($context['_seq'] as $context["id"] => $context["title"]) {
                // line 37
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
            // line 39
            echo "\t\t\t</select>
\t\t</div>
\t</div>
";
        }
        // line 43
        echo "
";
        // line 47
        echo "
";
        // line 48
        if (isset($context["event_trigger_sub"])) { $_event_trigger_sub_ = $context["event_trigger_sub"]; } else { $_event_trigger_sub_ = null; }
        if (isset($context["event_trigger_master"])) { $_event_trigger_master_ = $context["event_trigger_master"]; } else { $_event_trigger_master_ = null; }
        if (((($_event_trigger_sub_ == "web") || ($_event_trigger_sub_ == "")) || ($_event_trigger_master_ == "update"))) {
            // line 49
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.department");
            echo "\" data-rule-type=\"department\" data-term-type=\"GenericMenuTerm\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"is\">";
            // line 52
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 53
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t";
            // line 54
            if (isset($context["event_trigger_master"])) { $_event_trigger_master_ = $context["event_trigger_master"]; } else { $_event_trigger_master_ = null; }
            if (($_event_trigger_master_ == "update")) {
                // line 55
                echo "\t\t\t\t\t<option value=\"changed\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed");
                echo "</option>
\t\t\t\t\t<option value=\"changed_to\">";
                // line 56
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_to");
                echo "</option>
\t\t\t\t\t<option value=\"changed_from\">";
                // line 57
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_from");
                echo "</option>
\t\t\t\t";
            }
            // line 59
            echo "\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"department\">
\t\t\t\t";
            // line 63
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "departments"), "getRootNodes", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["dep"]) {
                if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                if ($this->getAttribute($_dep_, "is_tickets_enabled")) {
                    // line 64
                    echo "\t\t\t\t\t";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    if (twig_length_filter($this->env, $this->getAttribute($_dep_, "children"))) {
                        // line 65
                        echo "\t\t\t\t\t\t<optgroup label=\"";
                        if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                        echo "\">
\t\t\t\t\t\t\t";
                        // line 66
                        if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_dep_, "children"));
                        foreach ($context['_seq'] as $context["_key"] => $context["subdep"]) {
                            // line 67
                            echo "\t\t\t\t\t\t\t\t<option data-full-title=\"";
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
\t\t\t\t\t\t\t";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subdep'], $context['_parent'], $context['loop']);
                        $context = array_merge($_parent, array_intersect_key($context, $_parent));
                        // line 69
                        echo "\t\t\t\t\t\t</optgroup>
\t\t\t\t\t";
                    } else {
                        // line 71
                        echo "\t\t\t\t\t\t<option value=\"";
                        if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "id"), "html", null, true);
                        echo "\">";
                        if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                        echo "</option>
\t\t\t\t\t";
                    }
                    // line 73
                    echo "\t\t\t\t";
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['dep'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 74
            echo "\t\t\t</select>
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"";
            // line 78
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.subject");
            echo "\" data-rule-type=\"subject\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"contains\">";
            // line 81
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contains");
            echo "</option>
\t\t\t\t<option value=\"not_contains\">";
            // line 82
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_contain");
            echo "</option>
\t\t\t\t<option value=\"is\">";
            // line 83
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 84
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t<option value=\"is_regex\">";
            // line 85
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.matches_regex");
            echo "</option>
\t\t\t\t<option value=\"not_regex\">";
            // line 86
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_matches_regex");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"text\" name=\"subject\" value=\"\" />
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"Message\" data-rule-type=\"message\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"contains\">";
            // line 97
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contains");
            echo "</option>
\t\t\t\t<option value=\"not_contains\">";
            // line 98
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_contain");
            echo "</option>
\t\t\t\t<option value=\"is\">";
            // line 99
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 100
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t<option value=\"is_regex\">";
            // line 101
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.matches_regex");
            echo "</option>
\t\t\t\t<option value=\"not_regex\">";
            // line 102
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_matches_regex");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"text\" name=\"message\" value=\"\" />
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"URL of website\" data-rule-type=\"creation_system_option\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"contains\">";
            // line 113
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contains");
            echo "</option>
\t\t\t\t<option value=\"not_contains\">";
            // line 114
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_contain");
            echo "</option>
\t\t\t\t<option value=\"is\">";
            // line 115
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 116
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t<option value=\"is_regex\">";
            // line 117
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.matches_regex");
            echo "</option>
\t\t\t\t<option value=\"not_regex\">";
            // line 118
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_matches_regex");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"text\" name=\"website_url\" value=\"\" />
\t\t</div>
\t</div>

\t";
            // line 126
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_product"), "method")) {
                // line 127
                echo "\t\t<div class=\"builder-type\" title=\"";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.product");
                echo "\" data-rule-group=\"Ticket\" data-rule-type=\"product\" data-term-type=\"GenericMenuTerm\">
\t\t\t<div class=\"builder-op\">
\t\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t\t<option value=\"is\">";
                // line 130
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
                echo "</option>
\t\t\t\t\t<option value=\"not\">";
                // line 131
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
                echo "</option>
\t\t\t\t</select>
\t\t\t</div>
\t\t\t<div class=\"builder-options\">
\t\t\t\t<select name=\"category\">
\t\t\t\t\t<option value=\"0\">None</option>
\t\t\t\t\t";
                // line 137
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "products"), "getNames", array(), "method"));
                foreach ($context['_seq'] as $context["id"] => $context["title"]) {
                    // line 138
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
                // line 140
                echo "\t\t\t\t</select>
\t\t\t</div>
\t\t</div>
\t";
            }
        }
        // line 145
        echo "
";
        // line 149
        echo "
";
        // line 150
        if (isset($context["event_trigger_master"])) { $_event_trigger_master_ = $context["event_trigger_master"]; } else { $_event_trigger_master_ = null; }
        if (($_event_trigger_master_ == "update")) {
            // line 151
            echo "\t";
            if (isset($context["event_trigger_sub"])) { $_event_trigger_sub_ = $context["event_trigger_sub"]; } else { $_event_trigger_sub_ = null; }
            if (isset($context["event_trigger_master"])) { $_event_trigger_master_ = $context["event_trigger_master"]; } else { $_event_trigger_master_ = null; }
            if ((!((($_event_trigger_sub_ == "web") || ($_event_trigger_sub_ == "")) || ($_event_trigger_master_ == "update")))) {
                // line 152
                echo "\t<div class=\"builder-type\" title=\"";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.department");
                echo "\" data-rule-type=\"department\" data-term-type=\"GenericMenuTerm\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"is\">";
                // line 155
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
                echo "</option>
\t\t\t\t<option value=\"not\">";
                // line 156
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
                echo "</option>
\t\t\t\t<option value=\"changed\">";
                // line 157
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed");
                echo "</option>
\t\t\t\t<option value=\"changed_to\">";
                // line 158
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_to");
                echo "</option>
\t\t\t\t<option value=\"changed_from\">";
                // line 159
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_from");
                echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"department\">
\t\t\t\t";
                // line 164
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "departments"), "getRootNodes", array(), "method"));
                foreach ($context['_seq'] as $context["_key"] => $context["dep"]) {
                    // line 165
                    echo "\t\t\t\t\t";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    if (twig_length_filter($this->env, $this->getAttribute($_dep_, "children"))) {
                        // line 166
                        echo "\t\t\t\t\t\t<optgroup label=\"";
                        if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                        echo "\">
\t\t\t\t\t\t\t";
                        // line 167
                        if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_dep_, "children"));
                        foreach ($context['_seq'] as $context["_key"] => $context["subdep"]) {
                            // line 168
                            echo "\t\t\t\t\t\t\t\t<option data-full-title=\"";
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
\t\t\t\t\t\t\t";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subdep'], $context['_parent'], $context['loop']);
                        $context = array_merge($_parent, array_intersect_key($context, $_parent));
                        // line 170
                        echo "\t\t\t\t\t\t</optgroup>
\t\t\t\t\t";
                    } else {
                        // line 172
                        echo "\t\t\t\t\t\t<option value=\"";
                        if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "id"), "html", null, true);
                        echo "\">";
                        if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                        echo "</option>
\t\t\t\t\t";
                    }
                    // line 174
                    echo "\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['dep'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 175
                echo "\t\t\t</select>
\t\t</div>
\t</div>
\t";
            }
            // line 179
            echo "
\t";
            // line 180
            if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
            if ((($_event_trigger_ == "update.agent") || ($_event_trigger_ == "update.user"))) {
                // line 181
                echo "\t\t<div class=\"builder-type\" title=\"Updated by email reply\" data-rule-type=\"is_via_email_reply\" data-term-type=\"GenericMenuTerm\">
\t\t\t<div class=\"builder-op\">
\t\t\t\t<input type=\"hidden\" name=\"op\" value=\"is\" />
\t\t\t</div>
\t\t\t<div class=\"builder-options\">
\t\t\t\t<input type=\"hidden\" name=\"do\" value=\"1\" />
\t\t\t\tUpdate is triggered by an email reply
\t\t\t</div>
\t\t</div>
\t\t<div class=\"builder-type\" title=\"Updated from the web interface\" data-rule-type=\"is_via_interface\" data-term-type=\"GenericMenuTerm\">
\t\t\t<div class=\"builder-op\">
\t\t\t\t<input type=\"hidden\" name=\"op\" value=\"is\" />
\t\t\t</div>
\t\t\t<div class=\"builder-options\">
\t\t\t\t<input type=\"hidden\" name=\"do\" value=\"1\" />
\t\t\t\tUpdate is triggered from the web interface
\t\t\t</div>
\t\t</div>
\t";
            }
            // line 200
            echo "
\t";
            // line 201
            if (isset($context["event_trigger_sub"])) { $_event_trigger_sub_ = $context["event_trigger_sub"]; } else { $_event_trigger_sub_ = null; }
            if (($_event_trigger_sub_ == "agent")) {
                // line 202
                echo "\t<div class=\"builder-type\" title=\"Has new agent reply\" data-rule-type=\"new_reply_agent\" data-term-type=\"GenericMenuTerm\">
\t\t<div class=\"builder-op\">
\t\t\t<input type=\"hidden\" name=\"op\" value=\"is\" />
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"hidden\" name=\"do\" value=\"1\" />
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"Has new agent note\" data-rule-type=\"new_reply_note\" data-term-type=\"GenericMenuTerm\">
\t\t<div class=\"builder-op\">
\t\t\t<input type=\"hidden\" name=\"op\" value=\"is\" />
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"hidden\" name=\"do\" value=\"1\" />
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"Agent Performer\" data-rule-type=\"agent_performer\" data-term-type=\"GenericMenuTerm\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"contains\">";
                // line 223
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
                echo "</option>
\t\t\t\t<option value=\"not_contains\">";
                // line 224
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
                echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"agent_ids[]\" multiple=\"multiple\">
\t\t\t\t";
                // line 229
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "agents"), "getAgents", array(), "method"));
                foreach ($context['_seq'] as $context["_key"] => $context["agent"]) {
                    // line 230
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
                // line 232
                echo "\t\t\t</select>
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"Performed on day of week\" data-rule-type=\"current_day\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"is\">";
                // line 239
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
                echo "</option>
\t\t\t\t<option value=\"not\">";
                // line 240
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
                echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t";
                // line 244
                $context["days"] = array(1 => "Monday", 2 => "Tuesday", 3 => "Wednesday", 4 => "Thursday", 5 => "Friday", 6 => "Saturday", 0 => "Sunday");
                // line 253
                echo "\t\t\t<select name=\"days[]\" multiple=\"multiple\">
\t\t\t\t";
                // line 254
                if (isset($context["days"])) { $_days_ = $context["days"]; } else { $_days_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($_days_);
                foreach ($context['_seq'] as $context["day"] => $context["name"]) {
                    // line 255
                    echo "\t\t\t\t<option value=\"";
                    if (isset($context["day"])) { $_day_ = $context["day"]; } else { $_day_ = null; }
                    echo twig_escape_filter($this->env, $_day_, "html", null, true);
                    echo "\">";
                    if (isset($context["name"])) { $_name_ = $context["name"]; } else { $_name_ = null; }
                    echo twig_escape_filter($this->env, $_name_, "html", null, true);
                    echo "</option>
\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['day'], $context['name'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 257
                echo "\t\t\t</select>
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"Performed during time of day\" data-rule-type=\"current_time\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"builder-op\">
\t\t\t\t<option value=\"before\">";
                // line 264
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.before");
                echo "</option>
\t\t\t\t<option value=\"after\">";
                // line 265
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.after");
                echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"hidden\" name=\"timezone\" value=\"";
                // line 269
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "user"), "timezone"), "html", null, true);
                echo "\" />
\t\t\t<label>";
                // line 270
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.hour");
                echo "</label>
\t\t\t<select name=\"hour1\">
\t\t\t\t";
                // line 272
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable(range(0, 23));
                foreach ($context['_seq'] as $context["_key"] => $context["hour"]) {
                    // line 273
                    echo "\t\t\t\t<option>";
                    if (isset($context["hour"])) { $_hour_ = $context["hour"]; } else { $_hour_ = null; }
                    echo twig_escape_filter($this->env, sprintf("%02s", $_hour_), "html", null, true);
                    echo "</option>
\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['hour'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 275
                echo "\t\t\t</select>
\t\t\t<label>";
                // line 276
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.minute");
                echo "</label>
\t\t\t<select name=\"minute1\">
\t\t\t\t";
                // line 278
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable(range(0, 59));
                foreach ($context['_seq'] as $context["_key"] => $context["minute"]) {
                    // line 279
                    echo "\t\t\t\t<option>";
                    if (isset($context["minute"])) { $_minute_ = $context["minute"]; } else { $_minute_ = null; }
                    echo twig_escape_filter($this->env, sprintf("%02s", $_minute_), "html", null, true);
                    echo "</option>
\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['minute'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 281
                echo "\t\t\t</select>
\t\t</div>
\t</div>
\t";
            }
            // line 285
            echo "
\t";
            // line 286
            if (isset($context["event_trigger_sub"])) { $_event_trigger_sub_ = $context["event_trigger_sub"]; } else { $_event_trigger_sub_ = null; }
            if (($_event_trigger_sub_ == "user")) {
                // line 287
                echo "\t<div class=\"builder-type\" title=\"Has new user reply\" data-rule-type=\"new_reply_user\" data-term-type=\"GenericMenuTerm\">
\t\t<div class=\"builder-op\">
\t\t\t<input type=\"hidden\" name=\"op\" value=\"id\" />
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"hidden\" name=\"do\" value=\"1\" />
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"User Performer Email\" data-rule-type=\"user_performer_email\" data-term-type=\"GenericMenuTerm\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"is\">";
                // line 299
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
                echo "</option>
\t\t\t\t<option value=\"not\">";
                // line 300
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
                echo "</option>
\t\t\t\t<option value=\"contains\">";
                // line 301
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contains");
                echo "</option>
\t\t\t\t<option value=\"not_contains\">";
                // line 302
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_contain");
                echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"text\" name=\"user_email\" value=\"\" placeholder=\"Enter an email address of a user to match\" />
\t\t</div>
\t</div>
\t";
            }
        }
        // line 311
        echo "
";
        // line 312
        if (isset($context["event_trigger_sub"])) { $_event_trigger_sub_ = $context["event_trigger_sub"]; } else { $_event_trigger_sub_ = null; }
        if (isset($context["event_trigger_master"])) { $_event_trigger_master_ = $context["event_trigger_master"]; } else { $_event_trigger_master_ = null; }
        if ((((($_event_trigger_sub_ == "web") || ($_event_trigger_sub_ == "")) || ($_event_trigger_master_ == "update")) || ($_event_trigger_master_ == "time"))) {
            // line 313
            echo "\t";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_ticket_category"), "method")) {
                // line 314
                echo "\t\t<div class=\"builder-type\" title=\"";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.category");
                echo "\" data-rule-group=\"Ticket\" data-rule-type=\"category\" data-term-type=\"GenericMenuTerm\">
\t\t\t<div class=\"builder-op\">
\t\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t\t<option value=\"is\">";
                // line 317
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
                echo "</option>
\t\t\t\t\t<option value=\"not\">";
                // line 318
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
                echo "</option>
\t\t\t\t\t<option value=\"changed\">";
                // line 319
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed");
                echo "</option>
\t\t\t\t\t<option value=\"changed_to\">";
                // line 320
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_to");
                echo "</option>
\t\t\t\t\t<option value=\"changed_from\">";
                // line 321
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_from");
                echo "</option>
\t\t\t\t</select>
\t\t\t</div>
\t\t\t<div class=\"builder-options\">
\t\t\t\t<select name=\"category\">
\t\t\t\t\t";
                // line 326
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "TicketCategory"), "method"), "getRootNodes", array(), "method"));
                foreach ($context['_seq'] as $context["_key"] => $context["cat"]) {
                    // line 327
                    echo "\t\t\t\t\t\t";
                    if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                    if (twig_length_filter($this->env, $this->getAttribute($_cat_, "children"))) {
                        // line 328
                        echo "\t\t\t\t\t\t\t<optgroup label=\"";
                        if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
                        echo "\">
\t\t\t\t\t\t\t\t";
                        // line 329
                        if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_cat_, "children"));
                        foreach ($context['_seq'] as $context["_key"] => $context["subcat"]) {
                            // line 330
                            echo "\t\t\t\t\t\t\t\t\t<option data-full-title=\"";
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
\t\t\t\t\t\t\t\t";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subcat'], $context['_parent'], $context['loop']);
                        $context = array_merge($_parent, array_intersect_key($context, $_parent));
                        // line 332
                        echo "\t\t\t\t\t\t\t</optgroup>
\t\t\t\t\t\t";
                    } else {
                        // line 334
                        echo "\t\t\t\t\t\t\t<option value=\"";
                        if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "id"), "html", null, true);
                        echo "\">";
                        if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
                        echo "</option>
\t\t\t\t\t\t";
                    }
                    // line 336
                    echo "\t\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['cat'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 337
                echo "\t\t\t\t</select>
\t\t\t</div>
\t\t</div>
\t";
            }
            // line 341
            echo "
\t";
            // line 342
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_ticket_priority"), "method")) {
                // line 343
                echo "\t\t<div class=\"builder-type\" title=\"";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.priority");
                echo "\" data-rule-group=\"Ticket\" data-rule-type=\"priority\" data-term-type=\"GenericMenuTerm\">
\t\t\t<div class=\"builder-op\">
\t\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t\t<option value=\"is\">";
                // line 346
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
                echo "</option>
\t\t\t\t\t<option value=\"not\">";
                // line 347
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
                echo "</option>
\t\t\t\t\t<option value=\"changed\">";
                // line 348
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed");
                echo "</option>
\t\t\t\t\t<option value=\"changed_to\">";
                // line 349
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_to");
                echo "</option>
\t\t\t\t\t<option value=\"changed_from\">";
                // line 350
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_from");
                echo "</option>
\t\t\t\t</select>
\t\t\t</div>
\t\t\t<div class=\"builder-options\">
\t\t\t\t<select name=\"priority\">
\t\t\t\t\t";
                // line 355
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "TicketPriority"), "method"), "getNames", array(), "method"));
                foreach ($context['_seq'] as $context["id"] => $context["name"]) {
                    // line 356
                    echo "\t\t\t\t\t\t<option value=\"";
                    if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                    echo twig_escape_filter($this->env, $_id_, "html", null, true);
                    echo "\">";
                    if (isset($context["name"])) { $_name_ = $context["name"]; } else { $_name_ = null; }
                    echo twig_escape_filter($this->env, $_name_, "html", null, true);
                    echo "</option>
\t\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['id'], $context['name'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 358
                echo "\t\t\t\t</select>
\t\t\t</div>
\t\t</div>
\t";
            }
            // line 362
            echo "
\t<div class=\"builder-type\" title=\"";
            // line 363
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.urgency");
            echo "\" data-rule-type=\"urgency\" data-term-type=\"GenericInputTerm\" data-term-triggers=\"urgency\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"gte\">&gt;=</option>
\t\t\t\t<option value=\"lte\">&lt;=</option>
\t\t\t\t<option value=\"is\">";
            // line 368
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 369
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t";
            // line 370
            if (isset($context["with_change_terms"])) { $_with_change_terms_ = $context["with_change_terms"]; } else { $_with_change_terms_ = null; }
            if ($_with_change_terms_) {
                // line 371
                echo "\t\t\t\t\t<optgroup label=\"";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed");
                echo "\">
\t\t\t\t\t\t<option value=\"changed\">";
                // line 372
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed");
                echo "</option>
\t\t\t\t\t\t<option value=\"changed_to\">";
                // line 373
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_to");
                echo "</option>
\t\t\t\t\t\t<option value=\"changed_from\">";
                // line 374
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_from");
                echo "</option>
\t\t\t\t\t\t<option value=\"not_changed_to\">";
                // line 375
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_changed_to");
                echo "</option>
\t\t\t\t\t\t<option value=\"not_changed_from\">";
                // line 376
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_changed_from");
                echo "</option>
\t\t\t\t\t</optgroup>
\t\t\t\t\t<optgroup label=\"Changed (ranged)\">
\t\t\t\t\t\t<option value=\"changed_to_gte\">Changed to &gt;=</option>
\t\t\t\t\t\t<option value=\"changed_to_lte\">Changed to &lt;=</option>
\t\t\t\t\t\t<option value=\"changed_from_gte\">Changed from &gt;=</option>
\t\t\t\t\t\t<option value=\"changed_from_lte\">Changed from &lt;=</option>
\t\t\t\t\t\t<option value=\"not_changed_to_gte\">Not changed to &gt;=</option>
\t\t\t\t\t\t<option value=\"not_changed_from_lte\">Not changed from &lt;=</option>
\t\t\t\t\t</optgroup>
\t\t\t\t";
            }
            // line 387
            echo "\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"num\">
\t\t\t\t";
            // line 391
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable(range(1, 10));
            foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
                // line 392
                echo "\t\t\t\t\t<option value=\"";
                if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
                echo twig_escape_filter($this->env, $_i_, "html", null, true);
                echo "\">";
                if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
                echo twig_escape_filter($this->env, $_i_, "html", null, true);
                echo "</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 394
            echo "\t\t\t</select>
\t\t</div>
\t</div>

\t";
            // line 398
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_ticket_workflow"), "method")) {
                // line 399
                echo "\t\t<div class=\"builder-type\" title=\"";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.workflow");
                echo "\" data-rule-group=\"Ticket\" data-rule-type=\"workflow\" data-term-type=\"GenericMenuTerm\">
\t\t\t<div class=\"builder-op\">
\t\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t\t<option value=\"is\">";
                // line 402
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
                echo "</option>
\t\t\t\t\t<option value=\"not\">";
                // line 403
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
                echo "</option>
\t\t\t\t\t<option value=\"changed\">";
                // line 404
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed");
                echo "</option>
\t\t\t\t\t<option value=\"changed_to\">";
                // line 405
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_to");
                echo "</option>
\t\t\t\t\t<option value=\"changed_from\">";
                // line 406
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_from");
                echo "</option>
\t\t\t\t</select>
\t\t\t</div>
\t\t\t<div class=\"builder-options\">
\t\t\t\t<select name=\"workflow\">
\t\t\t\t\t";
                // line 411
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "TicketWorkflow"), "method"), "getAll", array(), "method"));
                foreach ($context['_seq'] as $context["_key"] => $context["work"]) {
                    // line 412
                    echo "\t\t\t\t\t\t<option value=\"";
                    if (isset($context["work"])) { $_work_ = $context["work"]; } else { $_work_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_work_, "id"), "html", null, true);
                    echo "\">";
                    if (isset($context["work"])) { $_work_ = $context["work"]; } else { $_work_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_work_, "title"), "html", null, true);
                    echo "</option>
\t\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['work'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 414
                echo "\t\t\t\t</select>
\t\t\t</div>
\t\t</div>
\t";
            }
            // line 418
            echo "
\t<div class=\"builder-type\" title=\"";
            // line 419
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.labels");
            echo "\" data-rule-group=\"Ticket\" data-rule-type=\"label\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.LabelsTerm\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\">
\t\t\t\t<option value=\"is\">";
            // line 422
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.includes_label");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 423
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_include_label");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"text\" name=\"label\" value=\"\" />
\t\t</div>
\t</div>

\t";
            // line 431
            if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ((!twig_in_filter("ticket_field", $_no_show_fields_) && $this->getAttribute($this->getAttribute($_app_, "getTicketFieldManager", array(), "method"), "getFields", array(), "method"))) {
                // line 432
                echo "\t\t";
                $context["op_lang"] = array("is" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is"), "not" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not"), "contains" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contains"), "notcontains" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_contain"), "gt" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_greater_than"), "lt" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_less_than"));
                // line 433
                echo "\t\t";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getTicketFieldManager", array(), "method"), "getDisplayArray", array(), "method"));
                foreach ($context['_seq'] as $context["_key"] => $context["field"]) {
                    // line 434
                    echo "\t\t\t";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
                    if ($this->getAttribute($this->getAttribute($_field_, "field_def"), "getFilterCapabilities", array(0 => $_search_context_), "method")) {
                        // line 435
                        echo "\t\t\t<div class=\"builder-type\" title=\"";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_field_, "field_def"), "title"), "html", null, true);
                        echo "\" data-rule-group=\"Ticket\" data-rule-type=\"ticket_field[";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_field_, "field_def"), "id"), "html", null, true);
                        echo "]\">
\t\t\t\t<div class=\"builder-op\">
\t\t\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t\t\t";
                        // line 438
                        if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
                        if (($_search_context_ == "filter")) {
                            // line 439
                            echo "\t\t\t\t\t\t\t";
                            if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                            if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
                            $context['_parent'] = (array) $context;
                            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_field_, "field_def"), "getFilterCapabilities", array(0 => $_search_context_), "method"));
                            foreach ($context['_seq'] as $context["_key"] => $context["op"]) {
                                // line 440
                                echo "\t\t\t\t\t\t\t\t<option value=\"";
                                if (isset($context["op"])) { $_op_ = $context["op"]; } else { $_op_ = null; }
                                echo twig_escape_filter($this->env, $_op_, "html", null, true);
                                echo "\">";
                                if (isset($context["op_lang"])) { $_op_lang_ = $context["op_lang"]; } else { $_op_lang_ = null; }
                                if (isset($context["op"])) { $_op_ = $context["op"]; } else { $_op_ = null; }
                                echo twig_escape_filter($this->env, $this->getAttribute($_op_lang_, $_op_, array(), "array"), "html", null, true);
                                echo "</option>
\t\t\t\t\t\t\t";
                            }
                            $_parent = $context['_parent'];
                            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['op'], $context['_parent'], $context['loop']);
                            $context = array_merge($_parent, array_intersect_key($context, $_parent));
                            // line 442
                            echo "\t\t\t\t\t\t";
                        } else {
                            // line 443
                            echo "\t\t\t\t\t\t\t";
                            if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                            if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
                            $context['_parent'] = (array) $context;
                            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_field_, "field_def"), "getSearchCapabilities", array(0 => $_search_context_), "method"));
                            foreach ($context['_seq'] as $context["_key"] => $context["op"]) {
                                // line 444
                                echo "\t\t\t\t\t\t\t\t<option value=\"";
                                if (isset($context["op"])) { $_op_ = $context["op"]; } else { $_op_ = null; }
                                echo twig_escape_filter($this->env, $_op_, "html", null, true);
                                echo "\">";
                                if (isset($context["op_lang"])) { $_op_lang_ = $context["op_lang"]; } else { $_op_lang_ = null; }
                                if (isset($context["op"])) { $_op_ = $context["op"]; } else { $_op_ = null; }
                                echo twig_escape_filter($this->env, $this->getAttribute($_op_lang_, $_op_, array(), "array"), "html", null, true);
                                echo "</option>
\t\t\t\t\t\t\t";
                            }
                            $_parent = $context['_parent'];
                            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['op'], $context['_parent'], $context['loop']);
                            $context = array_merge($_parent, array_intersect_key($context, $_parent));
                            // line 446
                            echo "\t\t\t\t\t\t";
                        }
                        // line 447
                        echo "\t\t\t\t\t</select>
\t\t\t\t</div>
\t\t\t\t<div class=\"builder-options\">
\t\t\t\t\t";
                        // line 450
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        echo $this->env->getExtension('form')->renderWidget($this->getAttribute($_field_, "formView"));
                        echo "
\t\t\t\t</div>
\t\t\t</div>
\t\t\t";
                    }
                    // line 454
                    echo "\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['field'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 455
                echo "\t";
            }
        }
        // line 457
        echo "
";
        // line 461
        echo "
";
        // line 462
        if (isset($context["event_trigger_master"])) { $_event_trigger_master_ = $context["event_trigger_master"]; } else { $_event_trigger_master_ = null; }
        if (($_event_trigger_master_ == "time")) {
            // line 463
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.department");
            echo "\" data-rule-type=\"department\" data-term-type=\"GenericMenuTerm\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"is\">";
            // line 466
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 467
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"department\">
\t\t\t\t";
            // line 472
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "departments"), "getRootNodes", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["dep"]) {
                // line 473
                echo "\t\t\t\t\t";
                if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                if (twig_length_filter($this->env, $this->getAttribute($_dep_, "children"))) {
                    // line 474
                    echo "\t\t\t\t\t\t<optgroup label=\"";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                    echo "\">
\t\t\t\t\t\t\t";
                    // line 475
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($_dep_, "children"));
                    foreach ($context['_seq'] as $context["_key"] => $context["subdep"]) {
                        // line 476
                        echo "\t\t\t\t\t\t\t\t<option data-full-title=\"";
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
\t\t\t\t\t\t\t";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subdep'], $context['_parent'], $context['loop']);
                    $context = array_merge($_parent, array_intersect_key($context, $_parent));
                    // line 478
                    echo "\t\t\t\t\t\t</optgroup>
\t\t\t\t\t";
                } else {
                    // line 480
                    echo "\t\t\t\t\t\t<option value=\"";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "id"), "html", null, true);
                    echo "\">";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                    echo "</option>
\t\t\t\t\t";
                }
                // line 482
                echo "\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['dep'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 483
            echo "\t\t\t</select>
\t\t</div>
\t</div>
";
        }
        // line 487
        echo "
";
        // line 491
        echo "
";
        // line 492
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "usergroups"), "getUserUsergroups", array(), "method")) {
            // line 493
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.usergroup");
            echo "\" data-rule-group=\"Person\" data-term-type=\"GenericMenuTerm\" data-rule-type=\"person_usergroup\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\">
\t\t\t\t<option value=\"is\">";
            // line 496
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 497
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"usergroup\">
\t\t\t\t";
            // line 502
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "usergroups"), "getUserUsergroups", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["ug"]) {
                // line 503
                echo "\t\t\t\t\t<option value=\"";
                if (isset($context["ug"])) { $_ug_ = $context["ug"]; } else { $_ug_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_ug_, "id"), "html", null, true);
                echo "\">";
                if (isset($context["ug"])) { $_ug_ = $context["ug"]; } else { $_ug_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_ug_, "title"), "html", null, true);
                echo "</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['ug'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 505
            echo "\t\t\t</select>
\t\t</div>
\t</div>
";
        }
        // line 509
        echo "
";
        // line 510
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "languages"), "isMultiLang", array(), "method")) {
            // line 511
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.language");
            echo "\" data-rule-group=\"Person\" data-term-type=\"GenericMenuTerm\" data-rule-type=\"person_language\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\">
\t\t\t\t<option value=\"is\">";
            // line 514
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 515
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"language\">
\t\t\t\t";
            // line 520
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "languages"), "getTitles", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["lang"]) {
                // line 521
                echo "\t\t\t\t<option value=\"";
                if (isset($context["lang"])) { $_lang_ = $context["lang"]; } else { $_lang_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_lang_, "id"), "html", null, true);
                echo "\">";
                if (isset($context["lang"])) { $_lang_ = $context["lang"]; } else { $_lang_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_lang_, "title"), "html", null, true);
                echo "</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['lang'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 523
            echo "\t\t\t</select>
\t\t</div>
\t</div>
";
        }
        // line 527
        echo "
<div class=\"builder-type\" title=\"Person Created Date\" data-rule-group=\"Person\" data-rule-type=\"person_date_created\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.DateTerm\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\" class=\"builder-op\">
\t\t\t<option value=\"lte\">";
        // line 531
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.before");
        echo "</option>
\t\t\t<option value=\"gte\">";
        // line 532
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.after");
        echo "</option>
\t\t\t<option value=\"between\">";
        // line 533
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.between");
        echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t";
        // line 537
        $this->env->loadTemplate("DeskPRO:Common:rulebuilder-date-options.html.twig")->display($context);
        // line 538
        echo "\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 541
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.email_address");
        echo "\" data-rule-group=\"Person\" data-rule-type=\"person_email\" data-term-type=\"GenericInputTerm\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\">
\t\t\t<option value=\"contains\">";
        // line 544
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contains");
        echo "</option>
\t\t\t<option value=\"notcontains\">";
        // line 545
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_contain");
        echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"email\" value=\"\" />
\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 553
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.email_domain");
        echo "\" data-rule-group=\"Person\" data-rule-type=\"person_email_domain\" data-term-type=\"GenericInputTerm\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\">
\t\t\t<option value=\"is\">";
        // line 556
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
        echo "</option>
\t\t\t<option value=\"not\">";
        // line 557
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
        echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"email_domain\" value=\"\" />
\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 565
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.labels");
        echo "\" data-rule-group=\"Person\" data-rule-type=\"person_label\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.LabelsTerm\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\">
\t\t\t<option value=\"is\">";
        // line 568
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.includes_label");
        echo "</option>
\t\t\t<option value=\"not\">";
        // line 569
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_include_label");
        echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"label\" value=\"\" />
\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 577
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.name");
        echo "\" data-rule-group=\"Person\" data-rule-type=\"person_name\" data-term-type=\"GenericInputTerm\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\">
\t\t\t<option value=\"is\">";
        // line 580
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
        echo "</option>
\t\t\t<option value=\"contains\">";
        // line 581
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contains");
        echo "</option>
\t\t\t<option value=\"notcontains\">";
        // line 582
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_contain");
        echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"name\" value=\"\" />
\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 590
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contact_phone");
        echo "\" data-rule-group=\"Person\" data-rule-type=\"person_contact_phone\" data-term-type=\"GenericInputTerm\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\">
\t\t\t<option value=\"contains\">";
        // line 593
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
        echo "</option>
\t\t\t<option value=\"notcontains\">";
        // line 594
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
        echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"phone\" value=\"\" />
\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 602
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contact_address");
        echo "\" data-rule-group=\"Person\" data-rule-type=\"person_contact_address\" data-term-type=\"GenericInputTerm\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\">
\t\t\t<option value=\"contains\">";
        // line 605
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
        echo "</option>
\t\t\t<option value=\"notcontains\">";
        // line 606
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
        echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"address\" value=\"\" />
\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 614
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contact_instant_messaging");
        echo "\" data-rule-group=\"Person\" data-rule-type=\"person_contact_im\" data-term-type=\"GenericInputTerm\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\">
\t\t\t<option value=\"contains\">";
        // line 617
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
        echo "</option>
\t\t\t<option value=\"notcontains\">";
        // line 618
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
        echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"im\" value=\"\" />
\t</div>
</div>

";
        // line 626
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "getPersonFieldManager"), "getFields", array(), "method")) {
            // line 627
            echo "\t";
            $context["op_lang"] = array("is" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is"), "not" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not"), "contains" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contains"), "notcontains" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_contain"), "gt" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.greater_than"), "lt" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.less_than"));
            // line 628
            echo "\t";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getPersonFieldManager", array(), "method"), "getDisplayArray", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["field"]) {
                // line 629
                echo "\t";
                if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
                if ($this->getAttribute($this->getAttribute($_field_, "field_def"), "getFilterCapabilities", array(0 => $_search_context_), "method")) {
                    // line 630
                    echo "\t<div class=\"builder-type\" title=\"";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.field_name", array("field" => $this->getAttribute($this->getAttribute($_field_, "field_def"), "title")));
                    echo "\" data-rule-group=\"Person\" data-rule-type=\"person_field[";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_field_, "field_def"), "id"), "html", null, true);
                    echo "]\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\">
\t\t\t\t";
                    // line 633
                    if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
                    if (($_search_context_ == "filter")) {
                        // line 634
                        echo "\t\t\t\t\t";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_field_, "field_def"), "getFilterCapabilities", array(0 => $_search_context_), "method"));
                        foreach ($context['_seq'] as $context["_key"] => $context["op"]) {
                            // line 635
                            echo "\t\t\t\t\t\t<option value=\"";
                            if (isset($context["op"])) { $_op_ = $context["op"]; } else { $_op_ = null; }
                            echo twig_escape_filter($this->env, $_op_, "html", null, true);
                            echo "\">";
                            if (isset($context["op_lang"])) { $_op_lang_ = $context["op_lang"]; } else { $_op_lang_ = null; }
                            if (isset($context["op"])) { $_op_ = $context["op"]; } else { $_op_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_op_lang_, $_op_, array(), "array"), "html", null, true);
                            echo "</option>
\t\t\t\t\t";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['op'], $context['_parent'], $context['loop']);
                        $context = array_merge($_parent, array_intersect_key($context, $_parent));
                        // line 637
                        echo "\t\t\t\t";
                    } else {
                        // line 638
                        echo "\t\t\t\t\t";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_field_, "field_def"), "getSearchCapabilities", array(0 => $_search_context_), "method"));
                        foreach ($context['_seq'] as $context["_key"] => $context["op"]) {
                            // line 639
                            echo "\t\t\t\t\t\t<option value=\"";
                            if (isset($context["op"])) { $_op_ = $context["op"]; } else { $_op_ = null; }
                            echo twig_escape_filter($this->env, $_op_, "html", null, true);
                            echo "\">";
                            if (isset($context["op_lang"])) { $_op_lang_ = $context["op_lang"]; } else { $_op_lang_ = null; }
                            if (isset($context["op"])) { $_op_ = $context["op"]; } else { $_op_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_op_lang_, $_op_, array(), "array"), "html", null, true);
                            echo "</option>
\t\t\t\t\t";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['op'], $context['_parent'], $context['loop']);
                        $context = array_merge($_parent, array_intersect_key($context, $_parent));
                        // line 641
                        echo "\t\t\t\t";
                    }
                    // line 642
                    echo "\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t";
                    // line 645
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    echo $this->env->getExtension('form')->renderWidget($this->getAttribute($_field_, "formView"));
                    echo "
\t\t</div>
\t</div>
\t";
                }
                // line 649
                echo "\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['field'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
        }
        // line 651
        echo "
";
        // line 655
        echo "
<div class=\"builder-type\" title=\"";
        // line 656
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.name");
        echo "\" data-rule-group=\"Organization\" data-rule-type=\"org_name\" data-term-type=\"GenericInputTerm\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\">
\t\t\t<option value=\"is\">";
        // line 659
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
        echo "</option>
\t\t\t<option value=\"contains\">";
        // line 660
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contains");
        echo "</option>
\t\t\t<option value=\"notcontains\">";
        // line 661
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_contain");
        echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"name\" value=\"\" />
\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 669
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.email_domains");
        echo "\" data-rule-group=\"Organization\" data-rule-type=\"org_email_domain\" data-term-type=\"GenericInputTerm\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\">
\t\t\t<option value=\"is\">";
        // line 672
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
        echo "</option>
\t\t\t<option value=\"not\">";
        // line 673
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
        echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"email_domain\" value=\"\" />
\t</div>
</div>

<div class=\"builder-type\" title=\"Organization Manager\" data-rule-group=\"Organization\" data-rule-type=\"org_manager\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\" class=\"op\">
\t\t\t<option value=\"is\">exists</option>
\t\t\t<option value=\"not\">does not exist</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<input type=\"hidden\" name=\"org_manager\" value=\"1\" />
\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 693
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.labels");
        echo "\" data-rule-group=\"Organization\" data-rule-type=\"org_label\" data-term-type=\"GenericInputTerm\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\">
\t\t\t<option value=\"is\">";
        // line 696
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.includes_label");
        echo "</option>
\t\t\t<option value=\"not\">";
        // line 697
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_include_label");
        echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"label\" value=\"\" />
\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 705
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contact_phone");
        echo "\" data-rule-group=\"Organization\" data-rule-type=\"org_contact_phone\" data-term-type=\"GenericInputTerm\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\">
\t\t\t<option value=\"contains\">";
        // line 708
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
        echo "</option>
\t\t\t<option value=\"notcontains\">";
        // line 709
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
        echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"phone\" value=\"\" />
\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 717
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contact_address");
        echo "\" data-rule-group=\"Organization\" data-rule-type=\"org_contact_address\" data-term-type=\"GenericInputTerm\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\">
\t\t\t<option value=\"contains\">";
        // line 720
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
        echo "</option>
\t\t\t<option value=\"notcontains\">";
        // line 721
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
        echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"address\" value=\"\" />
\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 729
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contact_instant_messaging");
        echo "\" data-rule-group=\"Organization\" data-rule-type=\"org_contact_im\" data-term-type=\"GenericInputTerm\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\">
\t\t\t<option value=\"contains\">";
        // line 732
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
        echo "</option>
\t\t\t<option value=\"notcontains\">";
        // line 733
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
        echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"im\" value=\"\" />
\t</div>
</div>

";
        // line 741
        $context["op_lang"] = array("is" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is"), "not" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not"), "contains" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contains"), "notcontains" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_contain"), "gt" => "is greater than", "lt" => "is less than");
        // line 742
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "get", array(0 => "OrgFieldsManager"), "method"), "getDisplayArray", array(), "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["field"]) {
            // line 743
            if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
            if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
            if ($this->getAttribute($this->getAttribute($_field_, "field_def"), "getFilterCapabilities", array(0 => $_search_context_), "method")) {
                // line 744
                echo "\t<div class=\"builder-type\" title=\"";
                if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_field_, "field_def"), "title"), "html", null, true);
                echo "\" data-rule-group=\"Organization\" data-rule-type=\"org_field[";
                if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_field_, "field_def"), "id"), "html", null, true);
                echo "]\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\">
\t\t\t\t";
                // line 747
                if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
                if (($_search_context_ == "filter")) {
                    // line 748
                    echo "\t\t\t\t\t";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_field_, "field_def"), "getFilterCapabilities", array(0 => $_search_context_), "method"));
                    foreach ($context['_seq'] as $context["_key"] => $context["op"]) {
                        // line 749
                        echo "\t\t\t\t\t\t<option value=\"";
                        if (isset($context["op"])) { $_op_ = $context["op"]; } else { $_op_ = null; }
                        echo twig_escape_filter($this->env, $_op_, "html", null, true);
                        echo "\">";
                        if (isset($context["op_lang"])) { $_op_lang_ = $context["op_lang"]; } else { $_op_lang_ = null; }
                        if (isset($context["op"])) { $_op_ = $context["op"]; } else { $_op_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_op_lang_, $_op_, array(), "array"), "html", null, true);
                        echo "</option>
\t\t\t\t\t";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['op'], $context['_parent'], $context['loop']);
                    $context = array_merge($_parent, array_intersect_key($context, $_parent));
                    // line 751
                    echo "\t\t\t\t";
                } else {
                    // line 752
                    echo "\t\t\t\t\t";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_field_, "field_def"), "getSearchCapabilities", array(0 => $_search_context_), "method"));
                    foreach ($context['_seq'] as $context["_key"] => $context["op"]) {
                        // line 753
                        echo "\t\t\t\t\t\t<option value=\"";
                        if (isset($context["op"])) { $_op_ = $context["op"]; } else { $_op_ = null; }
                        echo twig_escape_filter($this->env, $_op_, "html", null, true);
                        echo "\">";
                        if (isset($context["op_lang"])) { $_op_lang_ = $context["op_lang"]; } else { $_op_lang_ = null; }
                        if (isset($context["op"])) { $_op_ = $context["op"]; } else { $_op_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_op_lang_, $_op_, array(), "array"), "html", null, true);
                        echo "</option>
\t\t\t\t\t";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['op'], $context['_parent'], $context['loop']);
                    $context = array_merge($_parent, array_intersect_key($context, $_parent));
                    // line 755
                    echo "\t\t\t\t";
                }
                // line 756
                echo "\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t";
                // line 759
                if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                echo $this->env->getExtension('form')->renderWidget($this->getAttribute($_field_, "formView"));
                echo "
\t\t</div>
\t</div>
";
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['field'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 764
        echo "
";
        // line 768
        echo "
<div class=\"builder-type\" title=\"";
        // line 769
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.status");
        echo "\" data-rule-type=\"status\" data-term-type=\"GenericMenuTerm\" data-term-triggers=\"status\" data-rule-group=\"Ticket\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\" class=\"op\">
\t\t\t<option value=\"is\">";
        // line 772
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
        echo "</option>
\t\t\t<option value=\"not\">";
        // line 773
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
        echo "</option>
\t\t\t";
        // line 774
        if (isset($context["event_trigger_master"])) { $_event_trigger_master_ = $context["event_trigger_master"]; } else { $_event_trigger_master_ = null; }
        if (($_event_trigger_master_ == "update")) {
            // line 775
            echo "\t\t\t\t<option value=\"changed\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed");
            echo "</option>
\t\t\t\t<option value=\"changed\">";
            // line 776
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed");
            echo "</option>
\t\t\t\t<option value=\"changed_to\">";
            // line 777
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_to");
            echo "</option>
\t\t\t\t<option value=\"changed_from\">";
            // line 778
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_from");
            echo "</option>
\t\t\t\t<option value=\"not_changed_to\">";
            // line 779
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_changed_to");
            echo "</option>
\t\t\t\t<option value=\"not_changed_from\">";
            // line 780
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_changed_from");
            echo "</option>
\t\t\t";
        }
        // line 782
        echo "\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<select name=\"status[]\" multiple=\"multiple\">
\t\t\t<option value=\"awaiting_agent\">";
        // line 786
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_awaiting_agent");
        echo "</option>
\t\t\t<option value=\"awaiting_user\">";
        // line 787
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_awaiting_user");
        echo "</option>
\t\t\t<option value=\"resolved\">";
        // line 788
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_resolved");
        echo "</option>
\t\t</select>
\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 793
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.assigned_agent");
        echo "\" data-rule-type=\"agent\" data-term-type=\"GenericMenuTerm\" data-term-triggers=\"agent\" data-rule-group=\"Ticket\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\" class=\"op\">
\t\t\t<option value=\"is\">";
        // line 796
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
        echo "</option>
\t\t\t<option value=\"not\">";
        // line 797
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
        echo "</option>
\t\t\t";
        // line 798
        if (isset($context["event_trigger_master"])) { $_event_trigger_master_ = $context["event_trigger_master"]; } else { $_event_trigger_master_ = null; }
        if (($_event_trigger_master_ == "update")) {
            // line 799
            echo "\t\t\t\t<option value=\"changed\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed");
            echo "</option>
\t\t\t\t<option value=\"changed_to\">";
            // line 800
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_to");
            echo "</option>
\t\t\t\t<option value=\"changed_from\">";
            // line 801
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_from");
            echo "</option>
\t\t\t\t<option value=\"not_changed_to\">";
            // line 802
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_changed_to");
            echo "</option>
\t\t\t\t<option value=\"not_changed_from\">";
            // line 803
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_changed_from");
            echo "</option>
\t\t\t";
        }
        // line 805
        echo "\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<select name=\"agent\">
\t\t\t<option value=\"0\">";
        // line 809
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.unassigned");
        echo "</option>
\t\t\t<option value=\"-1\">Performer</option>
\t\t\t";
        // line 811
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "agents"));
        foreach ($context['_seq'] as $context["id"] => $context["label"]) {
            // line 812
            echo "\t\t\t\t<option value=\"";
            if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
            echo twig_escape_filter($this->env, $_id_, "html", null, true);
            echo "\">";
            if (isset($context["label"])) { $_label_ = $context["label"]; } else { $_label_ = null; }
            echo twig_escape_filter($this->env, $_label_, "html", null, true);
            echo "</option>
\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['id'], $context['label'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 814
        echo "\t\t</select>
\t</div>
</div>

<div class=\"builder-type\" title=\"";
        // line 818
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.agent_team");
        echo "\" data-rule-type=\"agent_team\" data-term-type=\"GenericMenuTerm\" data-term-triggers=\"team,agent-team\" data-rule-group=\"Ticket\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\" class=\"op\">
\t\t\t<option value=\"is\">";
        // line 821
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
        echo "</option>
\t\t\t<option value=\"not\">";
        // line 822
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
        echo "</option>
\t\t\t";
        // line 823
        if (isset($context["with_change_terms"])) { $_with_change_terms_ = $context["with_change_terms"]; } else { $_with_change_terms_ = null; }
        if ($_with_change_terms_) {
            // line 824
            echo "\t\t\t\t<option value=\"changed\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed");
            echo "</option>
\t\t\t\t<option value=\"changed_to\">";
            // line 825
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_to");
            echo "</option>
\t\t\t\t<option value=\"changed_from\">";
            // line 826
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_from");
            echo "</option>
\t\t\t\t<option value=\"not_changed_to\">";
            // line 827
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_changed_to");
            echo "</option>
\t\t\t\t<option value=\"not_changed_from\">";
            // line 828
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_changed_from");
            echo "</option>
\t\t\t";
        }
        // line 830
        echo "\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<select name=\"agent_team\">
\t\t\t<option value=\"0\">";
        // line 834
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.no_team");
        echo "</option>
\t\t\t<option value=\"-1\">Performer's Team</option>
\t\t\t";
        // line 836
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "agent_teams"), "getTeamNames", array(), "method"));
        foreach ($context['_seq'] as $context["id"] => $context["title"]) {
            // line 837
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
        // line 839
        echo "\t\t</select>
\t</div>
</div>

<div class=\"builder-type\" title=\"Ticket Created Date\" data-rule-group=\"Ticket\" data-rule-type=\"date_created\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.DateTerm\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\" class=\"builder-op\">
\t\t\t<option value=\"lte\">";
        // line 846
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.before");
        echo "</option>
\t\t\t<option value=\"gte\">";
        // line 847
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.after");
        echo "</option>
\t\t\t<option value=\"between\">";
        // line 848
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.between");
        echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t";
        // line 852
        $this->env->loadTemplate("DeskPRO:Common:rulebuilder-date-options.html.twig")->display($context);
        // line 853
        echo "\t</div>
</div>

<div class=\"builder-type\" title=\"Created on day\" data-rule-type=\"day_created\" data-rule-group=\"Ticket\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\" class=\"op\">
\t\t\t<option value=\"is\">";
        // line 859
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
        echo "</option>
\t\t\t<option value=\"not\">";
        // line 860
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
        echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t";
        // line 864
        $context["days"] = array(1 => "Monday", 2 => "Tuesday", 3 => "Wednesday", 4 => "Thursday", 5 => "Friday", 6 => "Saturday", 0 => "Sunday");
        // line 873
        echo "\t\t<select name=\"days[]\" multiple=\"multiple\">
\t\t\t";
        // line 874
        if (isset($context["days"])) { $_days_ = $context["days"]; } else { $_days_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_days_);
        foreach ($context['_seq'] as $context["day"] => $context["name"]) {
            // line 875
            echo "\t\t\t<option value=\"";
            if (isset($context["day"])) { $_day_ = $context["day"]; } else { $_day_ = null; }
            echo twig_escape_filter($this->env, $_day_, "html", null, true);
            echo "\">";
            if (isset($context["name"])) { $_name_ = $context["name"]; } else { $_name_ = null; }
            echo twig_escape_filter($this->env, $_name_, "html", null, true);
            echo "</option>
\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['day'], $context['name'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 877
        echo "\t\t</select>
\t</div>
</div>

<div class=\"builder-type\" title=\"Created time of day\" data-rule-type=\"time_created\" data-rule-group=\"Ticket\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\" class=\"op\">
\t\t\t<option value=\"before\">";
        // line 884
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.before");
        echo "</option>
\t\t\t<option value=\"after\">";
        // line 885
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.after");
        echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<input type=\"hidden\" name=\"timezone\" value=\"";
        // line 889
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "user"), "timezone"), "html", null, true);
        echo "\" />
\t\t<label>";
        // line 890
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.hour");
        echo "</label>
\t\t<select name=\"hour1\">
\t\t\t";
        // line 892
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable(range(0, 23));
        foreach ($context['_seq'] as $context["_key"] => $context["hour"]) {
            // line 893
            echo "\t\t\t<option>";
            if (isset($context["hour"])) { $_hour_ = $context["hour"]; } else { $_hour_ = null; }
            echo twig_escape_filter($this->env, sprintf("%02s", $_hour_), "html", null, true);
            echo "</option>
\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['hour'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 895
        echo "\t\t</select>
\t\t<label>";
        // line 896
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.minute");
        echo "</label>
\t\t<select name=\"minute1\">
\t\t\t";
        // line 898
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable(range(0, 59));
        foreach ($context['_seq'] as $context["_key"] => $context["minute"]) {
            // line 899
            echo "\t\t\t<option>";
            if (isset($context["minute"])) { $_minute_ = $context["minute"]; } else { $_minute_ = null; }
            echo twig_escape_filter($this->env, sprintf("%02s", $_minute_), "html", null, true);
            echo "</option>
\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['minute'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 901
        echo "\t\t</select>
\t</div>
</div>

<div class=\"builder-type\" title=\"Feedback Rating\" data-rule-type=\"feedback_rating\" data-rule-group=\"Ticket\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\" class=\"op\">
\t\t\t<option value=\"is\">is</option>
\t\t\t<option value=\"not\">is not</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<select name=\"rating\">
\t\t\t<option value=\"set\">submitted</option>
\t\t\t<option value=\"set\">positive</option>
\t\t\t<option value=\"set\">neutral</option>
\t\t\t<option value=\"set\">negative</option>
\t\t</select>
\t</div>
</div>

";
        // line 925
        echo "
";
        // line 926
        if (isset($context["event_trigger_sub"])) { $_event_trigger_sub_ = $context["event_trigger_sub"]; } else { $_event_trigger_sub_ = null; }
        if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
        if (((($_event_trigger_sub_ == "email") || ($_event_trigger_ == "update.agent")) || ($_event_trigger_ == "update.user"))) {
            // line 927
            echo "\t<div class=\"builder-type\" title=\"Email Account\" data-rule-type=\"gateway_account\" data-term-type=\"GenericMenuTerm\" data-rule-group=\"Email\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"is\">";
            // line 930
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 931
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"gateway_account\">
\t\t\t\t";
            // line 936
            if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "gateway_accounts"));
            foreach ($context['_seq'] as $context["id"] => $context["name"]) {
                // line 937
                echo "\t\t\t\t<option value=\"";
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
            // line 939
            echo "\t\t\t</select>
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"To Email Address\" data-rule-type=\"email_to_email\" data-rule-group=\"Email\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"is\">";
            // line 946
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 947
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t<option value=\"contains\">";
            // line 948
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contains");
            echo "</option>
\t\t\t\t<option value=\"not_contains\">";
            // line 949
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_contain");
            echo "</option>
\t\t\t\t<option value=\"is_regex\">";
            // line 950
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.matches_regex");
            echo "</option>
\t\t\t\t<option value=\"not_regex\">";
            // line 951
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_matches_regex");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"text\" name=\"email_address\" value=\"\" />
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"To Name\" data-rule-type=\"email_to_name\" data-rule-group=\"Email\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"is\">";
            // line 962
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 963
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t<option value=\"contains\">";
            // line 964
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contains");
            echo "</option>
\t\t\t\t<option value=\"not_contains\">";
            // line 965
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_contain");
            echo "</option>
\t\t\t\t<option value=\"is_regex\">";
            // line 966
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.matches_regex");
            echo "</option>
\t\t\t\t<option value=\"not_regex\">";
            // line 967
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_matches_regex");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"text\" name=\"name\" value=\"\" />
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"CC'd Email Address\" data-rule-type=\"email_cc_email\" data-rule-group=\"Email\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"is\">";
            // line 978
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 979
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t<option value=\"contains\">";
            // line 980
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contains");
            echo "</option>
\t\t\t\t<option value=\"not_contains\">";
            // line 981
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_contain");
            echo "</option>
\t\t\t\t<option value=\"is_regex\">";
            // line 982
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.matches_regex");
            echo "</option>
\t\t\t\t<option value=\"not_regex\">";
            // line 983
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_matches_regex");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"text\" name=\"email_address\" value=\"\" />
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"CC'd Name\" data-rule-type=\"email_cc_name\" data-rule-group=\"Email\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"is\">";
            // line 994
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 995
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t<option value=\"contains\">";
            // line 996
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contains");
            echo "</option>
\t\t\t\t<option value=\"not_contains\">";
            // line 997
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_contain");
            echo "</option>
\t\t\t\t<option value=\"is_regex\">";
            // line 998
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.matches_regex");
            echo "</option>
\t\t\t\t<option value=\"not_regex\">";
            // line 999
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_matches_regex");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"text\" name=\"name\" value=\"\" />
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"Email Account was BCC\" data-rule-type=\"email_account_bcc\" data-rule-group=\"Email\">
\t\t<div class=\"builder-op\">
\t\t\t<input type=\"hidden\" name=\"op\" value=\"is\" />
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"hidden\" name=\"do\" value=\"1\" />
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"Email Subject\" data-rule-type=\"email_subject\" data-rule-group=\"Email\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"contains\">";
            // line 1019
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contains");
            echo "</option>
\t\t\t\t<option value=\"not_contains\">";
            // line 1020
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_contain");
            echo "</option>
\t\t\t\t<option value=\"is\">";
            // line 1021
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 1022
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t<option value=\"is_regex\">";
            // line 1023
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.matches_regex");
            echo "</option>
\t\t\t\t<option value=\"not_regex\">";
            // line 1024
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_matches_regex");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"text\" name=\"subject\" value=\"\" />
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"Email Body\" data-rule-type=\"email_body\" data-rule-group=\"Email\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"contains\">";
            // line 1035
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contains");
            echo "</option>
\t\t\t\t<option value=\"not_contains\">";
            // line 1036
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_contain");
            echo "</option>
\t\t\t\t<option value=\"is\">";
            // line 1037
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 1038
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t<option value=\"is_regex\">";
            // line 1039
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.matches_regex");
            echo "</option>
\t\t\t\t<option value=\"not_regex\">";
            // line 1040
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_matches_regex");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"text\" name=\"message\" value=\"\" />
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"Email Headers\" data-rule-type=\"email_header\" data-rule-group=\"Email\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"contains\">";
            // line 1051
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contains");
            echo "</option>
\t\t\t\t<option value=\"not_contains\">";
            // line 1052
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_contain");
            echo "</option>
\t\t\t\t<option value=\"is\">";
            // line 1053
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 1054
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t<option value=\"is_regex\">";
            // line 1055
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.matches_regex");
            echo "</option>
\t\t\t\t<option value=\"not_regex\">";
            // line 1056
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_matches_regex");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"text\" name=\"header_name\" value=\"\" placeholder=\"Name\" style=\"width: 95%; margin-bottom: 3px;\" />
\t\t\t<input type=\"text\" name=\"header_value\" value=\"\" placeholder=\"Value\" style=\"width: 95%;\" />
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"From Email Address\" data-rule-type=\"email_from_email\" data-rule-group=\"Email\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"contains\">";
            // line 1068
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contains");
            echo "</option>
\t\t\t\t<option value=\"not_contains\">";
            // line 1069
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_contain");
            echo "</option>
\t\t\t\t<option value=\"is\">";
            // line 1070
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 1071
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t<option value=\"is_regex\">";
            // line 1072
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.matches_regex");
            echo "</option>
\t\t\t\t<option value=\"not_regex\">";
            // line 1073
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_matches_regex");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"text\" name=\"email_address\" value=\"\" />
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"From Email Name\" data-rule-type=\"email_from_name\" data-rule-group=\"Email\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"contains\">";
            // line 1084
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contains");
            echo "</option>
\t\t\t\t<option value=\"not_contains\">";
            // line 1085
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_contain");
            echo "</option>
\t\t\t\t<option value=\"is\">";
            // line 1086
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 1087
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t<option value=\"is_regex\">";
            // line 1088
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.matches_regex");
            echo "</option>
\t\t\t\t<option value=\"not_regex\">";
            // line 1089
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_matches_regex");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"text\" name=\"email_name\" value=\"\" />
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"Email Has Attachment\" data-rule-type=\"email_has_attach\" data-rule-group=\"Email\">
\t\t<div class=\"builder-op\">
\t\t\t<input type=\"hidden\" name=\"op\" value=\"is\" />
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"hidden\" name=\"do\" value=\"1\" />
\t\t</div>
\t</div>
";
        }
        // line 1106
        echo "
";
        // line 1107
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        if ($this->getAttribute($_term_options_, "slas")) {
            // line 1108
            echo "\t";
            if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
            if (!twig_in_filter("sla", $_no_show_fields_)) {
                // line 1109
                echo "\t\t<div class=\"builder-type\" title=\"SLA\" data-rule-group=\"Ticket\" data-rule-type=\"sla\" data-term-type=\"GenericMenuTerm\">
\t\t\t<div class=\"builder-op\">
\t\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t\t<option value=\"is\">";
                // line 1112
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
                echo "</option>
\t\t\t\t\t<option value=\"not\">";
                // line 1113
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
                echo "</option>
\t\t\t\t</select>
\t\t\t</div>
\t\t\t<div class=\"builder-options\">
\t\t\t\t<select name=\"sla_id\">
\t\t\t\t\t";
                // line 1118
                if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "slas"));
                foreach ($context['_seq'] as $context["id"] => $context["title"]) {
                    // line 1119
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
                // line 1121
                echo "\t\t\t\t</select>
\t\t\t</div>
\t\t</div>
\t";
            }
            // line 1125
            echo "
\t";
            // line 1126
            if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
            if (!twig_in_filter("sla_status", $_no_show_fields_)) {
                // line 1127
                echo "\t\t<div class=\"builder-type\" title=\"SLA Status\" data-rule-group=\"Ticket\" data-rule-type=\"sla_status\" data-term-type=\"GenericMenuTerm\">
\t\t\t<div class=\"builder-op\">
\t\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t\t<option value=\"is\">";
                // line 1130
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
                echo "</option>
\t\t\t\t\t<option value=\"not\">";
                // line 1131
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
                echo "</option>
\t\t\t\t</select>
\t\t\t</div>
\t\t\t<div class=\"builder-options\">
\t\t\t\t<select name=\"sla_status\">
\t\t\t\t\t<option value=\"ok\">OK</option>
\t\t\t\t\t<option value=\"warn\">Warning</option>
\t\t\t\t\t<option value=\"fail\">Fail</option>
\t\t\t\t</select>
\t\t\t\tfor SLA
\t\t\t\t<select name=\"sla_id\">
\t\t\t\t\t<option value=\"0\">Any</option>
\t\t\t\t";
                // line 1143
                if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "slas"));
                foreach ($context['_seq'] as $context["id"] => $context["title"]) {
                    // line 1144
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
                // line 1146
                echo "\t\t\t\t</select>
\t\t\t</div>
\t\t</div>
\t";
            }
        }
        // line 1151
        echo "
";
        // line 1152
        if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
        if (isset($context["classname"])) { $_classname_ = $context["classname"]; } else { $_classname_ = null; }
        if (($_id_ || $_classname_)) {
            // line 1153
            echo "</div>
";
        }
    }

    public function getTemplateName()
    {
        return "AdminBundle:TicketTriggers:criteria.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  2739 => 1153,  2735 => 1152,  2732 => 1151,  2725 => 1146,  2712 => 1144,  2707 => 1143,  2692 => 1131,  2688 => 1130,  2683 => 1127,  2680 => 1126,  2677 => 1125,  2671 => 1121,  2658 => 1119,  2653 => 1118,  2645 => 1113,  2641 => 1112,  2636 => 1109,  2632 => 1108,  2629 => 1107,  2626 => 1106,  2606 => 1089,  2602 => 1088,  2598 => 1087,  2594 => 1086,  2590 => 1085,  2586 => 1084,  2572 => 1073,  2568 => 1072,  2564 => 1071,  2560 => 1070,  2556 => 1069,  2552 => 1068,  2537 => 1056,  2533 => 1055,  2529 => 1054,  2525 => 1053,  2521 => 1052,  2503 => 1040,  2499 => 1039,  2495 => 1038,  2491 => 1037,  2487 => 1036,  2483 => 1035,  2469 => 1024,  2465 => 1023,  2461 => 1022,  2457 => 1021,  2453 => 1020,  2449 => 1019,  2426 => 999,  2422 => 998,  2418 => 997,  2414 => 996,  2410 => 995,  2406 => 994,  2392 => 983,  2388 => 982,  2384 => 981,  2380 => 980,  2376 => 979,  2372 => 978,  2358 => 967,  2354 => 966,  2350 => 965,  2346 => 964,  2342 => 963,  2338 => 962,  2324 => 951,  2320 => 950,  2316 => 949,  2312 => 948,  2308 => 947,  2304 => 946,  2295 => 939,  2282 => 937,  2277 => 936,  2269 => 931,  2265 => 930,  2260 => 927,  2256 => 926,  2253 => 925,  2230 => 901,  2220 => 899,  2216 => 898,  2211 => 896,  2208 => 895,  2198 => 893,  2194 => 892,  2189 => 890,  2184 => 889,  2177 => 885,  2173 => 884,  2164 => 877,  2151 => 875,  2146 => 874,  2143 => 873,  2141 => 864,  2134 => 860,  2130 => 859,  2122 => 853,  2120 => 852,  2113 => 848,  2105 => 846,  2096 => 839,  2083 => 837,  2078 => 836,  2073 => 834,  2067 => 830,  2062 => 828,  2058 => 827,  2054 => 826,  2050 => 825,  2045 => 824,  2038 => 822,  2034 => 821,  2022 => 814,  2009 => 812,  1999 => 809,  1993 => 805,  1988 => 803,  1984 => 802,  1980 => 801,  1976 => 800,  1971 => 799,  1968 => 798,  1964 => 797,  1960 => 796,  1954 => 793,  1946 => 788,  1942 => 787,  1938 => 786,  1932 => 782,  1927 => 780,  1919 => 778,  1915 => 777,  1911 => 776,  1906 => 775,  1903 => 774,  1899 => 773,  1895 => 772,  1886 => 768,  1883 => 764,  1871 => 759,  1866 => 756,  1863 => 755,  1842 => 752,  1825 => 749,  1818 => 748,  1815 => 747,  1800 => 743,  1793 => 741,  1772 => 729,  1761 => 721,  1757 => 720,  1751 => 717,  1740 => 709,  1719 => 697,  1715 => 696,  1686 => 673,  1676 => 669,  1665 => 661,  1661 => 660,  1645 => 651,  1638 => 649,  1630 => 645,  1625 => 642,  1622 => 641,  1598 => 637,  1577 => 634,  1558 => 629,  1549 => 627,  1546 => 626,  1535 => 618,  1531 => 617,  1514 => 606,  1510 => 605,  1504 => 602,  1493 => 594,  1483 => 590,  1464 => 580,  1458 => 577,  1443 => 568,  1437 => 565,  1426 => 557,  1405 => 545,  1401 => 544,  1395 => 541,  1390 => 538,  1381 => 533,  1373 => 531,  1348 => 521,  1343 => 520,  1335 => 515,  1321 => 510,  1318 => 509,  1299 => 503,  1294 => 502,  1282 => 496,  1269 => 491,  1260 => 483,  1240 => 478,  1221 => 476,  1216 => 475,  1210 => 474,  1206 => 473,  1193 => 467,  1189 => 466,  1155 => 450,  1150 => 447,  1022 => 411,  1006 => 404,  988 => 398,  969 => 392,  965 => 391,  921 => 370,  878 => 355,  866 => 349,  854 => 346,  819 => 334,  796 => 330,  1713 => 683,  1700 => 681,  1685 => 674,  1662 => 670,  1643 => 669,  1640 => 668,  1631 => 663,  1618 => 661,  1613 => 660,  1608 => 639,  1605 => 656,  1602 => 655,  1589 => 645,  1586 => 644,  1583 => 643,  1554 => 613,  1547 => 608,  1521 => 602,  1508 => 600,  1499 => 598,  1491 => 593,  1482 => 587,  1472 => 582,  1468 => 581,  1465 => 579,  1457 => 573,  1445 => 566,  1422 => 556,  1416 => 553,  1410 => 548,  1397 => 546,  1392 => 545,  1386 => 541,  1377 => 532,  1370 => 530,  1357 => 528,  1352 => 527,  1341 => 518,  1314 => 510,  1311 => 509,  1275 => 493,  1248 => 484,  1238 => 478,  1225 => 476,  1220 => 475,  1209 => 466,  1185 => 459,  1182 => 463,  1159 => 450,  1154 => 449,  1130 => 438,  1125 => 437,  1101 => 426,  1074 => 432,  1056 => 407,  1046 => 418,  1043 => 404,  1030 => 397,  1027 => 412,  947 => 361,  925 => 352,  913 => 368,  893 => 338,  881 => 335,  847 => 343,  829 => 336,  825 => 304,  1083 => 434,  995 => 383,  984 => 378,  963 => 319,  941 => 375,  851 => 271,  682 => 209,  1365 => 549,  1359 => 545,  1349 => 539,  1336 => 537,  1331 => 514,  1323 => 515,  1319 => 530,  1312 => 505,  1284 => 519,  1272 => 492,  1268 => 509,  1261 => 506,  1251 => 485,  1245 => 483,  1231 => 496,  1207 => 493,  1197 => 491,  1180 => 484,  1173 => 457,  1169 => 455,  1157 => 475,  1147 => 446,  1109 => 440,  1065 => 440,  1059 => 423,  1047 => 428,  1044 => 424,  1033 => 420,  1009 => 386,  991 => 399,  987 => 404,  973 => 395,  931 => 355,  924 => 371,  911 => 298,  906 => 370,  885 => 336,  872 => 354,  855 => 348,  749 => 279,  701 => 237,  594 => 164,  1163 => 454,  1143 => 440,  1087 => 420,  1077 => 433,  1051 => 430,  1037 => 480,  1010 => 405,  999 => 384,  932 => 414,  899 => 405,  895 => 404,  933 => 373,  914 => 133,  909 => 132,  833 => 329,  783 => 235,  755 => 320,  666 => 300,  453 => 187,  639 => 110,  568 => 254,  520 => 249,  657 => 287,  572 => 251,  609 => 459,  20 => 1,  659 => 207,  562 => 230,  548 => 165,  558 => 174,  479 => 206,  589 => 200,  457 => 145,  413 => 172,  953 => 430,  948 => 403,  935 => 394,  929 => 372,  916 => 382,  864 => 365,  844 => 342,  816 => 342,  807 => 370,  801 => 338,  774 => 234,  766 => 283,  737 => 312,  685 => 293,  664 => 231,  635 => 281,  593 => 185,  546 => 236,  532 => 68,  865 => 221,  852 => 347,  838 => 208,  820 => 201,  781 => 327,  764 => 320,  725 => 256,  632 => 283,  602 => 167,  565 => 176,  529 => 282,  505 => 267,  487 => 53,  473 => 221,  1853 => 842,  1849 => 753,  1839 => 751,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 733,  1778 => 732,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 693,  1705 => 781,  1688 => 675,  1681 => 769,  1677 => 768,  1667 => 671,  1656 => 762,  1648 => 655,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 606,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 593,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 540,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 522,  1289 => 520,  1276 => 604,  1263 => 601,  1244 => 480,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 463,  1181 => 561,  1171 => 557,  1158 => 550,  1114 => 428,  1104 => 465,  1072 => 512,  1062 => 439,  1024 => 395,  1014 => 406,  1000 => 328,  990 => 382,  980 => 474,  960 => 466,  918 => 348,  888 => 376,  834 => 307,  673 => 342,  636 => 185,  462 => 192,  454 => 103,  1144 => 542,  1139 => 399,  1131 => 399,  1127 => 397,  1110 => 396,  1092 => 459,  1089 => 390,  1086 => 520,  1084 => 419,  1063 => 387,  1060 => 386,  1055 => 422,  1050 => 384,  1035 => 372,  1019 => 330,  1003 => 263,  959 => 387,  900 => 366,  880 => 276,  870 => 350,  867 => 353,  859 => 362,  848 => 215,  839 => 376,  828 => 357,  823 => 374,  809 => 181,  800 => 315,  797 => 366,  794 => 294,  786 => 174,  740 => 162,  734 => 311,  703 => 354,  693 => 286,  630 => 278,  626 => 195,  614 => 275,  610 => 169,  581 => 247,  564 => 229,  525 => 235,  722 => 302,  697 => 256,  674 => 279,  671 => 425,  577 => 257,  569 => 243,  557 => 229,  502 => 229,  497 => 132,  445 => 197,  729 => 261,  684 => 281,  676 => 299,  669 => 254,  660 => 145,  647 => 198,  643 => 270,  601 => 178,  570 => 156,  522 => 202,  501 => 58,  296 => 67,  374 => 149,  631 => 265,  616 => 208,  608 => 266,  605 => 255,  596 => 102,  574 => 165,  561 => 175,  527 => 142,  433 => 166,  388 => 151,  426 => 142,  383 => 135,  461 => 44,  370 => 147,  395 => 109,  294 => 119,  223 => 65,  220 => 74,  492 => 180,  468 => 132,  444 => 168,  410 => 169,  397 => 135,  377 => 134,  262 => 107,  250 => 139,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 1051,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 680,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 638,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 572,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 526,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 494,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 538,  1126 => 443,  1106 => 528,  1100 => 897,  1026 => 418,  997 => 822,  983 => 816,  975 => 402,  956 => 318,  939 => 786,  902 => 362,  894 => 364,  879 => 400,  757 => 288,  727 => 316,  716 => 308,  670 => 278,  528 => 180,  476 => 253,  435 => 33,  354 => 137,  341 => 130,  192 => 30,  321 => 154,  243 => 54,  793 => 350,  780 => 311,  758 => 335,  700 => 212,  686 => 243,  652 => 273,  638 => 269,  620 => 174,  545 => 223,  523 => 179,  494 => 55,  459 => 191,  438 => 195,  351 => 104,  347 => 16,  402 => 136,  268 => 103,  430 => 141,  411 => 140,  379 => 145,  322 => 115,  315 => 119,  289 => 113,  284 => 88,  255 => 105,  234 => 70,  1133 => 444,  1124 => 469,  1121 => 56,  1116 => 549,  1113 => 467,  1108 => 376,  1103 => 368,  1098 => 26,  1081 => 415,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 419,  1042 => 483,  1039 => 345,  1025 => 335,  1021 => 391,  1015 => 412,  1008 => 461,  996 => 406,  989 => 454,  985 => 315,  981 => 257,  977 => 321,  970 => 320,  966 => 375,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 148,  919 => 290,  917 => 369,  908 => 411,  905 => 363,  896 => 358,  891 => 360,  877 => 334,  862 => 348,  857 => 273,  837 => 347,  832 => 250,  827 => 322,  821 => 302,  803 => 179,  778 => 389,  769 => 233,  765 => 297,  753 => 328,  746 => 319,  743 => 268,  735 => 226,  730 => 330,  720 => 363,  717 => 362,  712 => 251,  691 => 233,  678 => 149,  654 => 199,  587 => 14,  576 => 158,  539 => 172,  517 => 140,  471 => 160,  441 => 239,  437 => 39,  418 => 201,  386 => 106,  373 => 133,  304 => 114,  270 => 98,  265 => 102,  229 => 81,  477 => 174,  455 => 36,  448 => 41,  429 => 165,  407 => 138,  399 => 142,  389 => 170,  375 => 128,  358 => 110,  349 => 131,  335 => 120,  327 => 155,  298 => 144,  280 => 88,  249 => 205,  194 => 84,  142 => 46,  344 => 92,  318 => 86,  306 => 104,  295 => 106,  357 => 127,  300 => 113,  286 => 102,  276 => 104,  269 => 103,  254 => 101,  128 => 43,  237 => 71,  165 => 64,  122 => 46,  798 => 337,  770 => 179,  759 => 278,  748 => 271,  731 => 262,  721 => 258,  718 => 301,  708 => 250,  696 => 287,  617 => 188,  590 => 226,  553 => 188,  550 => 187,  540 => 234,  533 => 255,  500 => 397,  493 => 225,  489 => 179,  482 => 223,  467 => 172,  464 => 202,  458 => 123,  452 => 154,  449 => 35,  415 => 32,  382 => 149,  372 => 140,  361 => 129,  356 => 105,  339 => 89,  302 => 150,  285 => 115,  258 => 136,  123 => 36,  108 => 42,  424 => 164,  394 => 139,  380 => 151,  338 => 112,  319 => 125,  316 => 117,  312 => 116,  290 => 118,  267 => 96,  206 => 60,  110 => 43,  240 => 83,  224 => 95,  219 => 63,  217 => 94,  202 => 71,  186 => 70,  170 => 55,  100 => 28,  67 => 18,  14 => 1,  1096 => 425,  1090 => 421,  1088 => 435,  1085 => 456,  1066 => 409,  1034 => 282,  1031 => 281,  1018 => 416,  1013 => 477,  1007 => 274,  1002 => 403,  993 => 266,  986 => 264,  982 => 394,  976 => 399,  971 => 376,  964 => 255,  949 => 388,  946 => 402,  940 => 384,  937 => 374,  928 => 452,  926 => 413,  915 => 299,  912 => 236,  903 => 231,  898 => 440,  892 => 229,  889 => 337,  887 => 281,  884 => 374,  876 => 222,  874 => 215,  871 => 331,  863 => 352,  861 => 274,  858 => 347,  850 => 378,  843 => 206,  840 => 406,  815 => 332,  812 => 297,  808 => 199,  804 => 395,  799 => 295,  791 => 329,  785 => 328,  775 => 184,  771 => 284,  754 => 340,  728 => 317,  726 => 225,  723 => 168,  715 => 105,  711 => 152,  709 => 222,  706 => 302,  698 => 208,  694 => 308,  692 => 155,  689 => 302,  681 => 242,  677 => 149,  675 => 289,  663 => 276,  661 => 200,  650 => 246,  646 => 112,  629 => 183,  627 => 264,  625 => 213,  622 => 212,  598 => 205,  592 => 201,  586 => 199,  575 => 232,  566 => 242,  556 => 73,  554 => 240,  541 => 182,  536 => 241,  515 => 175,  511 => 166,  509 => 173,  488 => 155,  486 => 220,  483 => 175,  465 => 156,  463 => 170,  450 => 116,  432 => 32,  419 => 173,  371 => 127,  362 => 111,  353 => 141,  337 => 137,  333 => 156,  309 => 110,  303 => 122,  299 => 108,  291 => 92,  272 => 109,  261 => 101,  253 => 100,  239 => 82,  235 => 70,  213 => 73,  200 => 52,  198 => 85,  159 => 71,  149 => 56,  146 => 55,  131 => 53,  116 => 48,  79 => 32,  74 => 30,  71 => 29,  836 => 262,  817 => 398,  814 => 319,  811 => 317,  805 => 313,  787 => 333,  779 => 169,  776 => 326,  773 => 347,  761 => 296,  751 => 272,  747 => 325,  742 => 336,  739 => 333,  736 => 265,  724 => 259,  705 => 249,  702 => 601,  688 => 232,  680 => 278,  667 => 232,  662 => 275,  656 => 418,  649 => 285,  644 => 284,  641 => 268,  624 => 109,  613 => 264,  607 => 273,  597 => 253,  591 => 163,  584 => 239,  579 => 159,  563 => 96,  559 => 154,  551 => 243,  547 => 186,  537 => 145,  524 => 141,  512 => 174,  507 => 165,  504 => 164,  498 => 213,  485 => 166,  480 => 50,  472 => 205,  466 => 38,  460 => 152,  447 => 153,  442 => 40,  434 => 212,  428 => 31,  422 => 176,  404 => 156,  368 => 132,  364 => 126,  340 => 170,  334 => 127,  330 => 119,  325 => 116,  292 => 94,  287 => 67,  282 => 101,  279 => 109,  273 => 81,  266 => 97,  256 => 98,  252 => 86,  228 => 80,  218 => 78,  201 => 74,  64 => 26,  51 => 21,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 847,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 823,  2037 => 789,  2031 => 788,  2028 => 818,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 811,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 779,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 769,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 744,  1801 => 695,  1798 => 694,  1795 => 742,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 708,  1730 => 705,  1720 => 688,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 672,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 659,  1651 => 656,  1641 => 631,  1637 => 667,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 651,  1592 => 614,  1587 => 613,  1584 => 635,  1581 => 731,  1576 => 608,  1574 => 633,  1567 => 603,  1563 => 630,  1559 => 601,  1552 => 628,  1548 => 597,  1542 => 593,  1529 => 605,  1525 => 614,  1516 => 708,  1512 => 583,  1503 => 599,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 569,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 537,  1380 => 539,  1376 => 523,  1371 => 645,  1367 => 527,  1364 => 518,  1361 => 523,  1355 => 543,  1342 => 624,  1337 => 510,  1328 => 516,  1324 => 511,  1317 => 511,  1313 => 499,  1310 => 498,  1304 => 504,  1291 => 502,  1286 => 497,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 487,  1262 => 490,  1257 => 489,  1254 => 482,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 498,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 472,  1196 => 464,  1192 => 490,  1188 => 456,  1184 => 485,  1179 => 462,  1176 => 461,  1172 => 452,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 561,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 442,  1119 => 468,  1115 => 424,  1111 => 377,  1107 => 422,  1102 => 439,  1099 => 438,  1095 => 419,  1091 => 418,  1082 => 455,  1079 => 524,  1076 => 359,  1070 => 431,  1057 => 491,  1052 => 406,  1045 => 484,  1040 => 414,  1036 => 283,  1032 => 496,  1028 => 337,  1023 => 393,  1020 => 392,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 402,  992 => 821,  979 => 400,  974 => 256,  967 => 391,  962 => 803,  958 => 370,  954 => 389,  950 => 153,  945 => 376,  942 => 460,  938 => 150,  934 => 356,  927 => 147,  923 => 387,  920 => 412,  910 => 353,  901 => 340,  897 => 339,  890 => 343,  886 => 50,  883 => 356,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 340,  841 => 341,  835 => 337,  830 => 249,  826 => 202,  822 => 354,  818 => 301,  813 => 183,  810 => 317,  806 => 180,  802 => 198,  795 => 314,  792 => 239,  789 => 313,  784 => 286,  782 => 187,  777 => 291,  772 => 289,  768 => 321,  763 => 327,  760 => 319,  756 => 318,  752 => 317,  745 => 314,  741 => 313,  738 => 379,  732 => 171,  719 => 279,  714 => 300,  710 => 299,  704 => 267,  699 => 245,  695 => 234,  690 => 285,  687 => 210,  683 => 346,  679 => 298,  672 => 276,  668 => 201,  665 => 276,  658 => 227,  645 => 225,  640 => 224,  634 => 267,  628 => 214,  623 => 107,  619 => 298,  611 => 268,  606 => 263,  603 => 206,  599 => 262,  595 => 244,  583 => 263,  580 => 195,  573 => 157,  560 => 75,  543 => 147,  538 => 69,  534 => 233,  530 => 168,  526 => 229,  521 => 226,  518 => 233,  514 => 221,  510 => 227,  503 => 169,  496 => 226,  490 => 167,  484 => 394,  474 => 161,  470 => 231,  446 => 185,  440 => 146,  436 => 148,  431 => 37,  425 => 35,  416 => 159,  412 => 158,  408 => 157,  403 => 194,  400 => 155,  396 => 28,  392 => 139,  385 => 150,  381 => 133,  367 => 147,  363 => 18,  359 => 138,  355 => 132,  350 => 140,  346 => 130,  343 => 134,  328 => 135,  324 => 125,  313 => 105,  307 => 151,  301 => 124,  288 => 116,  283 => 111,  271 => 103,  257 => 148,  251 => 75,  238 => 71,  233 => 90,  195 => 121,  191 => 35,  187 => 53,  183 => 52,  130 => 49,  88 => 32,  76 => 30,  115 => 41,  95 => 28,  655 => 270,  651 => 232,  648 => 272,  637 => 273,  633 => 196,  621 => 462,  618 => 257,  615 => 268,  604 => 186,  600 => 254,  588 => 240,  585 => 161,  582 => 160,  571 => 242,  567 => 193,  555 => 239,  552 => 238,  549 => 224,  544 => 230,  542 => 290,  535 => 171,  531 => 143,  519 => 201,  516 => 200,  513 => 228,  508 => 230,  506 => 59,  499 => 241,  495 => 181,  491 => 54,  481 => 161,  478 => 235,  475 => 184,  469 => 196,  456 => 204,  451 => 149,  443 => 194,  439 => 167,  427 => 143,  423 => 141,  420 => 140,  409 => 137,  405 => 30,  401 => 164,  391 => 134,  387 => 132,  384 => 131,  378 => 154,  365 => 131,  360 => 128,  348 => 122,  336 => 132,  332 => 127,  329 => 109,  323 => 135,  310 => 124,  305 => 112,  277 => 170,  274 => 99,  263 => 97,  259 => 102,  247 => 138,  244 => 84,  241 => 87,  222 => 79,  210 => 122,  207 => 88,  204 => 74,  184 => 28,  181 => 60,  167 => 53,  157 => 60,  96 => 46,  421 => 142,  417 => 139,  414 => 138,  406 => 130,  398 => 165,  393 => 152,  390 => 153,  376 => 138,  369 => 19,  366 => 174,  352 => 140,  345 => 131,  342 => 160,  331 => 126,  326 => 87,  320 => 118,  317 => 100,  314 => 126,  311 => 85,  308 => 115,  297 => 112,  293 => 114,  281 => 146,  278 => 100,  275 => 113,  264 => 104,  260 => 107,  248 => 85,  245 => 73,  242 => 96,  231 => 52,  227 => 96,  215 => 88,  212 => 75,  209 => 125,  197 => 51,  177 => 118,  171 => 55,  161 => 68,  132 => 34,  121 => 43,  105 => 50,  99 => 46,  81 => 23,  77 => 19,  180 => 67,  176 => 45,  156 => 70,  143 => 56,  139 => 104,  118 => 41,  189 => 88,  185 => 61,  173 => 117,  166 => 73,  152 => 59,  174 => 63,  164 => 58,  154 => 113,  150 => 68,  137 => 49,  133 => 48,  127 => 52,  107 => 35,  102 => 34,  83 => 25,  78 => 31,  53 => 18,  23 => 2,  42 => 7,  138 => 55,  134 => 45,  109 => 39,  103 => 29,  97 => 27,  94 => 33,  84 => 24,  75 => 23,  69 => 27,  66 => 16,  54 => 9,  44 => 7,  230 => 74,  226 => 78,  203 => 71,  193 => 66,  188 => 57,  182 => 56,  178 => 59,  168 => 62,  163 => 115,  160 => 68,  155 => 55,  148 => 48,  145 => 47,  140 => 65,  136 => 39,  125 => 16,  120 => 49,  113 => 47,  101 => 37,  92 => 41,  89 => 27,  85 => 26,  73 => 20,  62 => 25,  59 => 21,  56 => 14,  41 => 5,  126 => 47,  119 => 65,  111 => 98,  106 => 38,  98 => 35,  93 => 42,  86 => 36,  70 => 13,  60 => 15,  28 => 2,  36 => 4,  114 => 54,  104 => 39,  91 => 37,  80 => 29,  63 => 30,  58 => 25,  40 => 7,  34 => 4,  45 => 7,  61 => 16,  55 => 11,  48 => 20,  39 => 5,  35 => 2,  31 => 2,  26 => 2,  21 => 2,  46 => 7,  29 => 4,  57 => 14,  50 => 9,  47 => 8,  38 => 3,  33 => 3,  49 => 11,  32 => 3,  246 => 102,  236 => 82,  232 => 81,  225 => 82,  221 => 63,  216 => 76,  214 => 77,  211 => 111,  208 => 74,  205 => 87,  199 => 69,  196 => 57,  190 => 54,  179 => 79,  175 => 66,  172 => 77,  169 => 65,  162 => 61,  158 => 63,  153 => 49,  151 => 43,  147 => 57,  144 => 51,  141 => 55,  135 => 54,  129 => 35,  124 => 42,  117 => 32,  112 => 31,  90 => 36,  87 => 25,  82 => 31,  72 => 19,  68 => 28,  65 => 26,  52 => 12,  43 => 6,  37 => 5,  30 => 6,  27 => 2,  25 => 3,  24 => 3,  22 => 34,  19 => 1,);
    }
}
