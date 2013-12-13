<?php

/* DeskPRO:Common:ticket-search-criteria.html.twig */
class __TwigTemplate_79dac98890cecaa08934e7082c3e13ec extends \Application\DeskPRO\Twig\Template
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
\t";
        // line 22
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if ((!twig_in_filter("action_performer", $_no_show_fields_) && 0)) {
            // line 23
            echo "\t\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.performer");
            echo "\" data-rule-type=\"action_performer\">
\t\t\t<div class=\"builder-op\">
\t\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t\t<option value=\"is\">";
            // line 26
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t\t<option value=\"not\">";
            // line 27
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t</select>
\t\t\t</div>
\t\t\t<div class=\"builder-options\">
\t\t\t\t<select name=\"action_performer\">
\t\t\t\t\t<option value=\"agent\">";
            // line 32
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.agent");
            echo "</option>
\t\t\t\t\t<option value=\"person\">";
            // line 33
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.user");
            echo "</option>
\t\t\t\t</select>
\t\t\t</div>
\t\t</div>
\t";
        }
        // line 38
        echo "
\t";
        // line 39
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (isset($context["event_group"])) { $_event_group_ = $context["event_group"]; } else { $_event_group_ = null; }
        if ((!twig_in_filter("is_new_user", $_no_show_fields_) && twig_in_filter("new_ticket", $_event_group_))) {
            // line 40
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_a_new_user");
            echo "\" data-rule-type=\"is_new_user\">
\t\t<div class=\"builder-op\">
\t\t\t<input type=\"hidden\" name=\"op\" value=\"is\" />
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"hidden\" name=\"is_new_user\" value=\"1\" />
\t\t</div>
\t</div>
\t<div class=\"builder-type\" title=\"";
            // line 48
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not_a_new_user");
            echo "\" data-rule-type=\"is_not_new_user\">
\t\t<div class=\"builder-op\">
\t\t\t<input type=\"hidden\" name=\"op\" value=\"is\" />
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"hidden\" name=\"is_not_new_user\" value=\"1\" />
\t\t</div>
\t</div>
\t";
        }
        // line 57
        echo "
\t";
        // line 58
        if (isset($context["event_group"])) { $_event_group_ = $context["event_group"]; } else { $_event_group_ = null; }
        if (twig_in_filter("new_ticket.gateway_person", $_event_group_)) {
            // line 59
            echo "\t\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.to_address");
            echo "\" data-rule-type=\"to_address\">
\t\t\t<div class=\"builder-op\">
\t\t\t\t<input type=\"hidden\" name=\"op\" value=\"is\" />
\t\t\t</div>
\t\t\t<div class=\"builder-options\">
\t\t\t\t<input type=\"text\" name=\"to_address\" value=\"\" />
\t\t\t</div>
\t\t</div>
\t\t<div class=\"builder-type\" title=\"";
            // line 67
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.cc_address");
            echo "\" data-rule-type=\"cc_address\">
\t\t\t<div class=\"builder-op\">
\t\t\t\t<input type=\"hidden\" name=\"op\" value=\"is\" />
\t\t\t</div>
\t\t\t<div class=\"builder-options\">
\t\t\t\t<input type=\"text\" name=\"cc_address\" value=\"\" />
\t\t\t</div>
\t\t</div>
\t";
        }
        // line 76
        echo "
\t";
        // line 77
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (isset($context["event_group"])) { $_event_group_ = $context["event_group"]; } else { $_event_group_ = null; }
        if ((!twig_in_filter("creation_system", $_no_show_fields_) && !twig_in_filter("new_ticket", $_event_group_))) {
            // line 78
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.originated_interface");
            echo "\" data-rule-type=\"creation_system\" data-term-type=\"GenericMenuTerm\" data-term-triggers=\"interface,orig-interface,original-interface\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"is\">";
            // line 81
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 82
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"creation_system\">
\t\t\t\t<option value=\"web\">";
            // line 87
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.web_interface");
            echo "</option>
\t\t\t\t<option value=\"web.person\">-- ";
            // line 88
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.by_user");
            echo "</option>
\t\t\t\t<option value=\"widget.person\">";
            // line 89
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.website_widget");
            echo "</option>
\t\t\t\t<option value=\"web.agent\">-- ";
            // line 90
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.by_agent_for_user");
            echo "</option>
\t\t\t\t<option value=\"gateway.person\">";
            // line 91
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.email");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t</div>
\t";
        }
        // line 96
        echo "
\t";
        // line 97
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        if ((!twig_in_filter("gateway_address", $_no_show_fields_) && $this->getAttribute($_term_options_, "gateway_addresses"))) {
            // line 98
            echo "\t\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ticket_gateway_address");
            echo "\" data-rule-type=\"gateway_address\" data-term-type=\"GenericMenuTerm\" data-term-triggers=\"gateway,address,gateway_address\">
\t\t\t<div class=\"builder-op\">
\t\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t\t<option value=\"is\">";
            // line 101
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t\t<option value=\"not\">";
            // line 102
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t</select>
\t\t\t</div>
\t\t\t<div class=\"builder-options\">
\t\t\t\t<select name=\"gateway_address\">
\t\t\t\t\t";
            // line 107
            if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "gateway_addresses"));
            foreach ($context['_seq'] as $context["id"] => $context["addr"]) {
                // line 108
                echo "\t\t\t\t\t\t<option value=\"";
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                echo twig_escape_filter($this->env, $_id_, "html", null, true);
                echo "\">";
                if (isset($context["addr"])) { $_addr_ = $context["addr"]; } else { $_addr_ = null; }
                echo twig_escape_filter($this->env, $_addr_, "html", null, true);
                echo "</option>
\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['id'], $context['addr'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 110
            echo "\t\t\t\t</select>
\t\t\t</div>
\t\t</div>
\t";
        }
        // line 114
        echo "
\t";
        // line 115
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        if ((!twig_in_filter("gateway_account", $_no_show_fields_) && $this->getAttribute($_term_options_, "gateway_accounts"))) {
            // line 116
            echo "\t\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ticket_gateway_account");
            echo "\" data-rule-type=\"gateway_account\" data-term-type=\"GenericMenuTerm\" data-term-triggers=\"gateway,account,gateway_account\">
\t\t\t<div class=\"builder-op\">
\t\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t\t<option value=\"is\">";
            // line 119
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t\t<option value=\"not\">";
            // line 120
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t</select>
\t\t\t</div>
\t\t\t<div class=\"builder-options\">
\t\t\t\t<select name=\"gateway_account\">
\t\t\t\t\t";
            // line 125
            if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "gateway_accounts"));
            foreach ($context['_seq'] as $context["id"] => $context["name"]) {
                // line 126
                echo "\t\t\t\t\t<option value=\"";
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
            // line 128
            echo "\t\t\t\t</select>
\t\t\t</div>
\t\t</div>
\t";
        }
        // line 132
        echo "
\t";
        // line 133
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (!twig_in_filter("subject", $_no_show_fields_)) {
            // line 134
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.subject");
            echo "\" data-rule-type=\"subject\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"contains\">";
            // line 137
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contains");
            echo "</option>
\t\t\t\t<option value=\"not_contains\">";
            // line 138
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_contain");
            echo "</option>
\t\t\t\t<option value=\"is\">";
            // line 139
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 140
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"text\" name=\"subject\" value=\"\" />
\t\t</div>
\t</div>
\t";
        }
        // line 148
        echo "
\t";
        // line 149
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (isset($context["event_group"])) { $_event_group_ = $context["event_group"]; } else { $_event_group_ = null; }
        if ((!twig_in_filter("sent_to_address", $_no_show_fields_) && twig_in_filter("new_ticket", $_event_group_))) {
            // line 150
            echo "\t<div class=\"builder-type\" title=\"Ticket sent in to address\" data-rule-type=\"sent_to_address\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"is\">";
            // line 153
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 154
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t<option value=\"contains\">";
            // line 155
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contains");
            echo "</option>
\t\t\t\t<option value=\"not_contains\">";
            // line 156
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_contain");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"text\" name=\"sent_to_address\" value=\"\" />
\t\t</div>
\t</div>
\t";
        }
        // line 164
        echo "
\t";
        // line 165
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!twig_in_filter("department", $_no_show_fields_) && $this->getAttribute($this->getAttribute($_app_, "departments"), "getPersonDepartments", array(0 => $this->getAttribute($_app_, "user"), 1 => "tickets"), "method"))) {
            // line 166
            echo "\t\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.department");
            echo "\" data-rule-type=\"department\" data-term-type=\"GenericMenuTerm\" data-term-triggers=\"dep,department\">
\t\t\t<div class=\"builder-op\">
\t\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t\t<option value=\"is\">";
            // line 169
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t\t<option value=\"not\">";
            // line 170
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t\t";
            // line 171
            if (isset($context["with_change_terms"])) { $_with_change_terms_ = $context["with_change_terms"]; } else { $_with_change_terms_ = null; }
            if ($_with_change_terms_) {
                // line 172
                echo "\t\t\t\t\t\t<option value=\"changed\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed");
                echo "</option>
\t\t\t\t\t\t<option value=\"changed_to\">";
                // line 173
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_to");
                echo "</option>
\t\t\t\t\t\t<option value=\"changed_from\">";
                // line 174
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_from");
                echo "</option>
\t\t\t\t\t\t<option value=\"not_changed_to\">";
                // line 175
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_changed_to");
                echo "</option>
\t\t\t\t\t\t<option value=\"not_changed_from\">";
                // line 176
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_changed_from");
                echo "</option>
\t\t\t\t\t";
            }
            // line 178
            echo "\t\t\t\t</select>
\t\t\t</div>
\t\t\t<div class=\"builder-options\">
\t\t\t\t<select name=\"department[]\" multiple=\"multiple\">
\t\t\t\t\t";
            // line 182
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "departments"), "getPersonDepartments", array(0 => $this->getAttribute($_app_, "user"), 1 => "tickets"), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["dep"]) {
                // line 183
                echo "\t\t\t\t\t\t";
                if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                if (twig_length_filter($this->env, $this->getAttribute($_dep_, "children"))) {
                    // line 184
                    echo "\t\t\t\t\t\t\t<optgroup label=\"";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                    echo "\">
\t\t\t\t\t\t\t\t";
                    // line 185
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($_dep_, "children"));
                    foreach ($context['_seq'] as $context["_key"] => $context["subdep"]) {
                        // line 186
                        echo "\t\t\t\t\t\t\t\t\t<option data-full-title=\"";
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
\t\t\t\t\t\t\t\t";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subdep'], $context['_parent'], $context['loop']);
                    $context = array_merge($_parent, array_intersect_key($context, $_parent));
                    // line 188
                    echo "\t\t\t\t\t\t\t</optgroup>
\t\t\t\t\t\t";
                } else {
                    // line 190
                    echo "\t\t\t\t\t\t\t<option value=\"";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "id"), "html", null, true);
                    echo "\">";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                    echo "</option>
\t\t\t\t\t\t";
                }
                // line 192
                echo "\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['dep'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 193
            echo "\t\t\t\t</select>
\t\t\t</div>
\t\t</div>
\t";
        }
        // line 197
        echo "
\t";
        // line 198
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
        if ((!twig_in_filter("agent", $_no_show_fields_) && ((!$_event_trigger_) || ($_event_trigger_ != "new_ticket")))) {
            // line 199
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.assigned_agent");
            echo "\" data-rule-type=\"agent\" data-term-type=\"GenericMenuTerm\" data-term-triggers=\"agent\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"is\">";
            // line 202
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 203
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t";
            // line 204
            if (isset($context["with_change_terms"])) { $_with_change_terms_ = $context["with_change_terms"]; } else { $_with_change_terms_ = null; }
            if ($_with_change_terms_) {
                // line 205
                echo "\t\t\t\t\t<option value=\"changed\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed");
                echo "</option>
\t\t\t\t\t<option value=\"changed_to\">";
                // line 206
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_to");
                echo "</option>
\t\t\t\t\t<option value=\"changed_from\">";
                // line 207
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_from");
                echo "</option>
\t\t\t\t\t<option value=\"not_changed_to\">";
                // line 208
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_changed_to");
                echo "</option>
\t\t\t\t\t<option value=\"not_changed_from\">";
                // line 209
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_changed_from");
                echo "</option>
\t\t\t\t";
            }
            // line 211
            echo "\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"agent[]\" multiple=\"multiple\">
\t\t\t\t<option value=\"0\">";
            // line 215
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.unassigned");
            echo "</option>
\t\t\t\t<option value=\"-1\">Me</option>
\t\t\t\t<optgroup label=\"Agents\">
\t\t\t\t\t";
            // line 218
            if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "agents"));
            foreach ($context['_seq'] as $context["id"] => $context["label"]) {
                // line 219
                echo "\t\t\t\t\t\t<option value=\"";
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                echo twig_escape_filter($this->env, $_id_, "html", null, true);
                echo "\">";
                if (isset($context["label"])) { $_label_ = $context["label"]; } else { $_label_ = null; }
                echo twig_escape_filter($this->env, $_label_, "html", null, true);
                echo "</option>
\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['id'], $context['label'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 221
            echo "\t\t\t\t</optgroup>
\t\t\t</select>
\t\t</div>
\t</div>
\t";
        }
        // line 226
        echo "
\t";
        // line 227
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
        if ((($this->getAttribute($this->getAttribute($_app_, "agent_teams"), "getTeamNames", array(), "method") && !twig_in_filter("agent_team", $_no_show_fields_)) && ((!$_event_trigger_) || ($_event_trigger_ != "new_ticket")))) {
            // line 228
            echo "\t\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.agent_team");
            echo "\" data-rule-type=\"agent_team\" data-term-type=\"GenericMenuTerm\" data-term-triggers=\"team,agent-team\">
\t\t\t<div class=\"builder-op\">
\t\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t\t<option value=\"is\">";
            // line 231
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t\t<option value=\"not\">";
            // line 232
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t\t";
            // line 233
            if (isset($context["with_change_terms"])) { $_with_change_terms_ = $context["with_change_terms"]; } else { $_with_change_terms_ = null; }
            if ($_with_change_terms_) {
                // line 234
                echo "\t\t\t\t\t\t<option value=\"changed\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed");
                echo "</option>
\t\t\t\t\t\t<option value=\"changed_to\">";
                // line 235
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_to");
                echo "</option>
\t\t\t\t\t\t<option value=\"changed_from\">";
                // line 236
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_from");
                echo "</option>
\t\t\t\t\t\t<option value=\"not_changed_to\">";
                // line 237
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_changed_to");
                echo "</option>
\t\t\t\t\t\t<option value=\"not_changed_from\">";
                // line 238
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_changed_from");
                echo "</option>
\t\t\t\t\t";
            }
            // line 240
            echo "\t\t\t\t</select>
\t\t\t</div>
\t\t\t<div class=\"builder-options\">
\t\t\t\t<select name=\"agent_team[]\" multiple=\"multiple\">
\t\t\t\t\t<option value=\"0\">";
            // line 244
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.no_team");
            echo "</option>
\t\t\t\t\t<option value=\"-1\">Any of my teams</option>
\t\t\t\t\t<optgroup label=\"Teams\">
\t\t\t\t\t\t";
            // line 247
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "agent_teams"), "getTeamNames", array(), "method"));
            foreach ($context['_seq'] as $context["id"] => $context["title"]) {
                // line 248
                echo "\t\t\t\t\t\t\t<option value=\"";
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                echo twig_escape_filter($this->env, $_id_, "html", null, true);
                echo "\">";
                if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
                echo twig_escape_filter($this->env, $_title_, "html", null, true);
                echo "</option>
\t\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['id'], $context['title'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 250
            echo "\t\t\t\t\t</optgroup>
\t\t\t\t</select>
\t\t\t</div>
\t\t</div>
\t";
        }
        // line 255
        echo "
\t";
        // line 256
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
        if ((!twig_in_filter("participant", $_no_show_fields_) && ((!$_event_trigger_) || ($_event_trigger_ != "new_ticket")))) {
            // line 257
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.followers");
            echo "\" data-rule-type=\"participant\" data-term-type=\"GenericMenuTerm\" data-term-triggers=\"followers\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"is\">";
            // line 260
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.include");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 261
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_include");
            echo "</option>
\t\t\t\t";
            // line 262
            if (isset($context["with_change_terms"])) { $_with_change_terms_ = $context["with_change_terms"]; } else { $_with_change_terms_ = null; }
            if ($_with_change_terms_) {
                // line 263
                echo "\t\t\t\t\t<option value=\"changed\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed");
                echo "</option>
\t\t\t\t\t<option value=\"changed_to\">";
                // line 264
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_to_include");
                echo "</option>
\t\t\t\t\t<option value=\"changed_from\">";
                // line 265
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_to_remove");
                echo "</option>
\t\t\t\t";
            }
            // line 267
            echo "\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"agent\">
\t\t\t\t<option value=\"-1\">";
            // line 271
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.current_agent");
            echo "</option>
\t\t\t\t";
            // line 272
            if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "agents"));
            foreach ($context['_seq'] as $context["id"] => $context["label"]) {
                // line 273
                echo "\t\t\t\t\t<option value=\"";
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                echo twig_escape_filter($this->env, $_id_, "html", null, true);
                echo "\">";
                if (isset($context["label"])) { $_label_ = $context["label"]; } else { $_label_ = null; }
                echo twig_escape_filter($this->env, $_label_, "html", null, true);
                echo "</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['id'], $context['label'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 275
            echo "\t\t\t</select>
\t\t</div>
\t</div>
\t";
        }
        // line 279
        echo "
\t";
        // line 280
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
        if (((!twig_in_filter("label", $_no_show_fields_) && (!$_event_trigger_)) || ($_event_trigger_ != "new_ticket"))) {
            // line 281
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.labels");
            echo "\" data-rule-type=\"label\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.LabelsTerm\" data-label-type=\"tickets\" data-term-type=\"GenericInputTerm\" data-term-triggers=\"label,labelled,labels\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"is\">";
            // line 284
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 285
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t";
            // line 286
            if (isset($context["with_change_terms"])) { $_with_change_terms_ = $context["with_change_terms"]; } else { $_with_change_terms_ = null; }
            if ($_with_change_terms_) {
                // line 287
                echo "\t\t\t\t\t<option value=\"changed\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed");
                echo "</option>
\t\t\t\t\t<option value=\"changed_to\">";
                // line 288
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_to_include");
                echo "</option>
\t\t\t\t\t<option value=\"changed_from\">";
                // line 289
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_to_remove");
                echo "</option>
\t\t\t\t";
            }
            // line 291
            echo "\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t";
            // line 294
            $this->env->loadTemplate("DeskPRO:Common:rulebuilder-labels-options.html.twig")->display($context);
            // line 295
            echo "\t\t</div>
\t</div>
\t";
        }
        // line 298
        echo "
\t";
        // line 299
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (!twig_in_filter("status", $_no_show_fields_)) {
            // line 300
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.status");
            echo "\" data-rule-type=\"status\" data-term-type=\"GenericMenuTerm\" data-term-triggers=\"status\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"is\">";
            // line 303
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 304
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t";
            // line 305
            if (isset($context["with_change_terms"])) { $_with_change_terms_ = $context["with_change_terms"]; } else { $_with_change_terms_ = null; }
            if ($_with_change_terms_) {
                // line 306
                echo "\t\t\t\t\t<option value=\"changed\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed");
                echo "</option>
\t\t\t\t\t<option value=\"changed_to\">";
                // line 307
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_to");
                echo "</option>
\t\t\t\t\t<option value=\"changed_from\">";
                // line 308
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_from");
                echo "</option>
\t\t\t\t\t<option value=\"not_changed_to\">";
                // line 309
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_changed_to");
                echo "</option>
\t\t\t\t\t<option value=\"not_changed_from\">";
                // line 310
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_changed_from");
                echo "</option>
\t\t\t\t";
            }
            // line 312
            echo "\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"status[]\" multiple=\"multiple\">
\t\t\t\t<option value=\"awaiting_agent\">";
            // line 316
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_awaiting_agent");
            echo "</option>
\t\t\t\t<option value=\"awaiting_user\">";
            // line 317
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_awaiting_user");
            echo "</option>
\t\t\t\t<option value=\"resolved\">";
            // line 318
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_resolved");
            echo "</option>
\t\t\t\t";
            // line 319
            if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
            if (($_search_context_ != "filter")) {
                // line 320
                echo "\t\t\t\t\t<option value=\"closed\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_closed");
                echo "</option>
\t\t\t\t\t<optgroup label=\"Hidden Tickets\">
\t\t\t\t\t\t<option value=\"hidden\">Any Hidden Ticket</option>
\t\t\t\t\t\t<option value=\"hidden.deleted\">Deleted</option>
\t\t\t\t\t\t<option value=\"hidden.spam\">Spam</option>
\t\t\t\t\t\t<option value=\"hidden.validating\">Validating</option>
\t\t\t\t\t</optgroup>
\t\t\t\t";
            }
            // line 328
            echo "\t\t\t</select>
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"Ticket is on hold\" data-rule-type=\"is_hold\" data-term-type=\"GenericMenuTerm\" data-term-triggers=\"is_hold\">
\t\t<div class=\"builder-op\">
\t\t\t<input type=\"hidden\" name=\"op\" value=\"is\" />
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"hidden\" name=\"is_hold\" value=\"1\" />
\t\t</div>
\t</div>
\t";
        }
        // line 341
        echo "
\t";
        // line 342
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!twig_in_filter("organization", $_no_show_fields_) && $this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "Organization"), "method"), "getOrganizationNames", array(), "method"))) {
            // line 343
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.organization");
            echo "\" data-term-type=\"GenericMenuTerm\" data-term-triggers=\"org,organization,company\" data-rule-type=\"organization\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\">
\t\t\t\t<option value=\"is\">";
            // line 346
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 347
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"text\" class=\"select2\" name=\"organization\"
\t\t\t\tdata-multiple=\"true\"
\t\t\t\tdata-autocomplete-url=\"";
            // line 353
            if (isset($context["organization_autocomplete_path"])) { $_organization_autocomplete_path_ = $context["organization_autocomplete_path"]; } else { $_organization_autocomplete_path_ = null; }
            echo twig_escape_filter($this->env, (($_organization_autocomplete_path_) ? ($_organization_autocomplete_path_) : ($this->env->getExtension('routing')->getPath("agent_orgsearch_quicknamesearch"))), "html", null, true);
            echo "\"
\t\t\t\tdata-select-width=\"auto\"
\t\t\t/>
\t\t</div>
\t</div>
\t";
        }
        // line 359
        echo "
\t";
        // line 360
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        if ((!twig_in_filter("product", $_no_show_fields_) && $this->getAttribute($_term_options_, "products"))) {
            // line 361
            echo "\t\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.product");
            echo "\" data-rule-type=\"product\" data-term-type=\"GenericMenuTerm\" data-term-triggers=\"prod,product\">
\t\t\t<div class=\"builder-op\">
\t\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t\t<option value=\"is\">";
            // line 364
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t\t<option value=\"not\">";
            // line 365
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t\t";
            // line 366
            if (isset($context["with_change_terms"])) { $_with_change_terms_ = $context["with_change_terms"]; } else { $_with_change_terms_ = null; }
            if ($_with_change_terms_) {
                // line 367
                echo "\t\t\t\t\t\t<option value=\"changed\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed");
                echo "</option>
\t\t\t\t\t\t<option value=\"changed_to\">";
                // line 368
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_to");
                echo "</option>
\t\t\t\t\t\t<option value=\"changed_from\">";
                // line 369
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_from");
                echo "</option>
\t\t\t\t\t\t<option value=\"not_changed_to\">";
                // line 370
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_changed_to");
                echo "</option>
\t\t\t\t\t\t<option value=\"not_changed_from\">";
                // line 371
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_changed_from");
                echo "</option>
\t\t\t\t\t";
            }
            // line 373
            echo "\t\t\t\t</select>
\t\t\t</div>
\t\t\t<div class=\"builder-options\">
\t\t\t\t<select name=\"product\">
\t\t\t\t\t<option value=\"0\">None</option>
\t\t\t\t\t";
            // line 378
            if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "products"));
            foreach ($context['_seq'] as $context["id"] => $context["label"]) {
                // line 379
                echo "\t\t\t\t\t\t<option value=\"";
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                echo twig_escape_filter($this->env, $_id_, "html", null, true);
                echo "\">";
                if (isset($context["label"])) { $_label_ = $context["label"]; } else { $_label_ = null; }
                echo twig_escape_filter($this->env, $_label_, "html", null, true);
                echo "</option>
\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['id'], $context['label'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 381
            echo "\t\t\t\t</select>
\t\t\t</div>
\t\t</div>
\t";
        }
        // line 385
        echo "
\t";
        // line 386
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        if ((!twig_in_filter("category", $_no_show_fields_) && $this->getAttribute($_term_options_, "ticket_categories"))) {
            // line 387
            echo "\t\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.category");
            echo "\" data-rule-type=\"category\" data-term-type=\"GenericMenuTerm\" data-term-triggers=\"cat,category\">
\t\t\t<div class=\"builder-op\">
\t\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t\t<option value=\"is\">";
            // line 390
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t\t<option value=\"not\">";
            // line 391
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t\t";
            // line 392
            if (isset($context["with_change_terms"])) { $_with_change_terms_ = $context["with_change_terms"]; } else { $_with_change_terms_ = null; }
            if ($_with_change_terms_) {
                // line 393
                echo "\t\t\t\t\t\t<option value=\"changed\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed");
                echo "</option>
\t\t\t\t\t\t<option value=\"changed_to\">";
                // line 394
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_to");
                echo "</option>
\t\t\t\t\t\t<option value=\"changed_from\">";
                // line 395
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_from");
                echo "</option>
\t\t\t\t\t\t<option value=\"not_changed_to\">";
                // line 396
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_changed_to");
                echo "</option>
\t\t\t\t\t\t<option value=\"not_changed_from\">";
                // line 397
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_changed_from");
                echo "</option>
\t\t\t\t\t";
            }
            // line 399
            echo "\t\t\t\t</select>
\t\t\t</div>
\t\t\t<div class=\"builder-options\">
\t\t\t\t<select name=\"category\">
\t\t\t\t\t<option value=\"0\">None</option>
\t\t\t\t\t";
            // line 404
            if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "ticket_categories"));
            foreach ($context['_seq'] as $context["id"] => $context["label"]) {
                // line 405
                echo "\t\t\t\t\t\t<option value=\"";
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                echo twig_escape_filter($this->env, $_id_, "html", null, true);
                echo "\">";
                if (isset($context["label"])) { $_label_ = $context["label"]; } else { $_label_ = null; }
                echo twig_escape_filter($this->env, $_label_, "html", null, true);
                echo "</option>
\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['id'], $context['label'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 407
            echo "\t\t\t\t</select>
\t\t\t</div>
\t\t</div>
\t";
        }
        // line 411
        echo "
\t";
        // line 412
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (!twig_in_filter("urgency", $_no_show_fields_)) {
            // line 413
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.urgency");
            echo "\" data-rule-type=\"urgency\" data-term-type=\"GenericInputTerm\" data-term-triggers=\"urgency\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"gte\">&gt;=</option>
\t\t\t\t<option value=\"lte\">&lt;=</option>
\t\t\t\t<option value=\"is\">";
            // line 418
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 419
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t";
            // line 420
            if (isset($context["with_change_terms"])) { $_with_change_terms_ = $context["with_change_terms"]; } else { $_with_change_terms_ = null; }
            if ($_with_change_terms_) {
                // line 421
                echo "\t\t\t\t\t<optgroup label=\"";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed");
                echo "\">
\t\t\t\t\t\t<option value=\"changed\">";
                // line 422
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed");
                echo "</option>
\t\t\t\t\t\t<option value=\"changed_to\">";
                // line 423
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_to");
                echo "</option>
\t\t\t\t\t\t<option value=\"changed_from\">";
                // line 424
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_from");
                echo "</option>
\t\t\t\t\t\t<option value=\"not_changed_to\">";
                // line 425
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_changed_to");
                echo "</option>
\t\t\t\t\t\t<option value=\"not_changed_from\">";
                // line 426
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
            // line 437
            echo "\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"text\" size=\"4\" value=\"\" name=\"num\" />
\t\t</div>
\t</div>
\t";
        }
        // line 444
        echo "
\t";
        // line 445
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        if ((!twig_in_filter("priority", $_no_show_fields_) && $this->getAttribute($_term_options_, "priorities"))) {
            // line 446
            echo "\t\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.priority");
            echo "\" data-rule-type=\"priority\" data-term-type=\"GenericMenuTerm\" data-term-triggers=\"pri,priority\">
\t\t\t<div class=\"builder-op\">
\t\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t\t<option value=\"is\">";
            // line 449
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t\t<option value=\"not\">";
            // line 450
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t\t<option value=\"lte\">";
            // line 451
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_less_than");
            echo "</option>
\t\t\t\t\t<option value=\"gte\">";
            // line 452
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_greater_than");
            echo "</option>
\t\t\t\t\t";
            // line 453
            if (isset($context["with_change_terms"])) { $_with_change_terms_ = $context["with_change_terms"]; } else { $_with_change_terms_ = null; }
            if ($_with_change_terms_) {
                // line 454
                echo "\t\t\t\t\t\t<option value=\"changed\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed");
                echo "</option>
\t\t\t\t\t\t<option value=\"changed_to\">";
                // line 455
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_to");
                echo "</option>
\t\t\t\t\t\t<option value=\"changed_from\">";
                // line 456
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_from");
                echo "</option>
\t\t\t\t\t\t<option value=\"not_changed_to\">";
                // line 457
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_changed_to");
                echo "</option>
\t\t\t\t\t\t<option value=\"not_changed_from\">";
                // line 458
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_changed_from");
                echo "</option>
\t\t\t\t\t";
            }
            // line 460
            echo "\t\t\t\t</select>
\t\t\t</div>
\t\t\t<div class=\"builder-options\">
\t\t\t\t<select name=\"priority\">
\t\t\t\t\t<option value=\"0\">None</option>
\t\t\t\t\t";
            // line 465
            if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "priorities"));
            foreach ($context['_seq'] as $context["id"] => $context["label"]) {
                // line 466
                echo "\t\t\t\t\t\t<option value=\"";
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                echo twig_escape_filter($this->env, $_id_, "html", null, true);
                echo "\">";
                if (isset($context["label"])) { $_label_ = $context["label"]; } else { $_label_ = null; }
                echo twig_escape_filter($this->env, $_label_, "html", null, true);
                echo "</option>
\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['id'], $context['label'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 468
            echo "\t\t\t\t</select>
\t\t\t</div>
\t\t</div>
\t";
        }
        // line 472
        echo "
\t";
        // line 473
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        if ((!twig_in_filter("workflow", $_no_show_fields_) && $this->getAttribute($_term_options_, "ticket_workflows"))) {
            // line 474
            echo "\t\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.workflow");
            echo "\" data-rule-type=\"workflow\" data-term-type=\"GenericMenuTerm\" data-term-triggers=\"work,workflow\">
\t\t\t<div class=\"builder-op\">
\t\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t\t<option value=\"is\">";
            // line 477
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t\t<option value=\"not\">";
            // line 478
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t\t\t";
            // line 479
            if (isset($context["with_change_terms"])) { $_with_change_terms_ = $context["with_change_terms"]; } else { $_with_change_terms_ = null; }
            if ($_with_change_terms_) {
                // line 480
                echo "\t\t\t\t\t\t<option value=\"changed\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed");
                echo "</option>
\t\t\t\t\t\t<option value=\"changed_to\">";
                // line 481
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_to");
                echo "</option>
\t\t\t\t\t\t<option value=\"changed_from\">";
                // line 482
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.changed_from");
                echo "</option>
\t\t\t\t\t\t<option value=\"not_changed_to\">";
                // line 483
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_changed_to");
                echo "</option>
\t\t\t\t\t\t<option value=\"not_changed_from\">";
                // line 484
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.not_changed_from");
                echo "</option>
\t\t\t\t\t";
            }
            // line 486
            echo "\t\t\t\t</select>
\t\t\t</div>
\t\t\t<div class=\"builder-options\">
\t\t\t\t<select name=\"workflow[]\" multiple=\"multiple\">
\t\t\t\t\t<option value=\"0\">None</option>
\t\t\t\t\t";
            // line 491
            if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "ticket_workflows"));
            foreach ($context['_seq'] as $context["id"] => $context["label"]) {
                // line 492
                echo "\t\t\t\t\t\t<option value=\"";
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                echo twig_escape_filter($this->env, $_id_, "html", null, true);
                echo "\">";
                if (isset($context["label"])) { $_label_ = $context["label"]; } else { $_label_ = null; }
                echo twig_escape_filter($this->env, $_label_, "html", null, true);
                echo "</option>
\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['id'], $context['label'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 494
            echo "\t\t\t\t</select>
\t\t\t</div>
\t\t</div>
\t";
        }
        // line 498
        echo "
\t";
        // line 499
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        if ((!twig_in_filter("language", $_no_show_fields_) && $this->getAttribute($_term_options_, "languages"))) {
            // line 500
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.language");
            echo "\" data-term-type=\"GenericMenuTerm\" data-rule-type=\"language\" data-term-triggers=\"lang,language\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\">
\t\t\t\t<option value=\"is\">";
            // line 503
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t\t<option value=\"not\">";
            // line 504
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<select name=\"language\">
\t\t\t\t<option value=\"0\">None</option>
\t\t\t\t";
            // line 510
            if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "languages"));
            foreach ($context['_seq'] as $context["id"] => $context["title"]) {
                // line 511
                echo "\t\t\t\t<option value=\"";
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
            // line 513
            echo "\t\t\t</select>
\t\t</div>
\t</div>
\t";
        }
        // line 517
        echo "
";
        // line 518
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        if ($this->getAttribute($_term_options_, "slas")) {
            // line 519
            echo "\t";
            if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
            if (!twig_in_filter("sla", $_no_show_fields_)) {
                // line 520
                echo "\t\t<div class=\"builder-type\" title=\"SLA\" data-rule-type=\"sla\" data-term-type=\"GenericMenuTerm\" data-term-triggers=\"sla\">
\t\t\t<div class=\"builder-op\">
\t\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t\t<option value=\"is\">";
                // line 523
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
                echo "</option>
\t\t\t\t\t<option value=\"not\">";
                // line 524
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
                echo "</option>
\t\t\t\t</select>
\t\t\t</div>
\t\t\t<div class=\"builder-options\">
\t\t\t\t<select name=\"sla_id\">
\t\t\t\t\t";
                // line 529
                if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "slas"));
                foreach ($context['_seq'] as $context["id"] => $context["title"]) {
                    // line 530
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
                // line 532
                echo "\t\t\t\t</select>
\t\t\t</div>
\t\t</div>
\t";
            }
            // line 536
            echo "
\t";
            // line 537
            if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
            if (!twig_in_filter("sla_status", $_no_show_fields_)) {
                // line 538
                echo "\t\t<div class=\"builder-type\" title=\"SLA Status\" data-rule-type=\"sla_status\" data-term-type=\"GenericMenuTerm\" data-term-triggers=\"sla_status,slastatus\">
\t\t\t<div class=\"builder-op\">
\t\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t\t<option value=\"is\">";
                // line 541
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
                echo "</option>
\t\t\t\t\t<option value=\"not\">";
                // line 542
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
                // line 554
                if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "slas"));
                foreach ($context['_seq'] as $context["id"] => $context["title"]) {
                    // line 555
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
                // line 557
                echo "\t\t\t\t</select>
\t\t\t</div>
\t\t</div>
\t";
            }
        }
        // line 562
        echo "
\t<div class=\"builder-type\" title=\"User Waiting Time\" data-rule-type=\"user_waiting\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"gte\">";
        // line 566
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_greater_than");
        echo "</option>
\t\t\t\t<option value=\"lte\">";
        // line 567
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_less_than");
        echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"text\" name=\"waiting_time\" value=\"\" style=\"width:50px\" />
\t\t\t<select name=\"waiting_time_unit\">
\t\t\t\t";
        // line 573
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable(array("minutes" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.minutes"), "hours" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.hours"), "days" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.days"), "weeks" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.weeks"), "months" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.months")));
        foreach ($context['_seq'] as $context["scale"] => $context["label"]) {
            // line 574
            echo "\t\t\t\t\t<option value=\"";
            if (isset($context["scale"])) { $_scale_ = $context["scale"]; } else { $_scale_ = null; }
            echo twig_escape_filter($this->env, $_scale_, "html", null, true);
            echo "\">";
            if (isset($context["label"])) { $_label_ = $context["label"]; } else { $_label_ = null; }
            echo twig_escape_filter($this->env, $_label_, "html", null, true);
            echo "</option>
\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['scale'], $context['label'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 576
        echo "\t\t\t</select>
\t\t</div>
\t</div>

\t<div class=\"builder-type\" title=\"User Total Waiting Time\" data-rule-type=\"total_user_waiting\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"lte\">";
        // line 583
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_less_than");
        echo "</option>
\t\t\t\t<option value=\"gte\">";
        // line 584
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_greater_than");
        echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"text\" name=\"waiting_time\" value=\"\" style=\"width:50px\" />
\t\t\t<select name=\"waiting_time_unit\">
\t\t\t\t";
        // line 590
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable(array("minutes" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.minutes"), "hours" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.hours"), "days" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.days"), "weeks" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.weeks"), "months" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.months")));
        foreach ($context['_seq'] as $context["scale"] => $context["label"]) {
            // line 591
            echo "\t\t\t\t\t<option value=\"";
            if (isset($context["scale"])) { $_scale_ = $context["scale"]; } else { $_scale_ = null; }
            echo twig_escape_filter($this->env, $_scale_, "html", null, true);
            echo "\">";
            if (isset($context["label"])) { $_label_ = $context["label"]; } else { $_label_ = null; }
            echo twig_escape_filter($this->env, $_label_, "html", null, true);
            echo "</option>
\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['scale'], $context['label'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 593
        echo "\t\t\t</select>
\t\t</div>
\t</div>

\t";
        // line 597
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
        if ((!twig_in_filter("date_created", $_no_show_fields_) && ((!$_event_trigger_) || ($_event_trigger_ != "new_ticket")))) {
            // line 598
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.date_created");
            echo "\" data-rule-type=\"date_created\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.DateTerm\" data-term-type=\"GenericDateTerm\" data-term-triggers=\"date-created\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"lte\">";
            // line 601
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.before");
            echo "</option>
\t\t\t\t<option value=\"gte\">";
            // line 602
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.after");
            echo "</option>
\t\t\t\t<option value=\"between\">";
            // line 603
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.between");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t";
            // line 607
            $this->env->loadTemplate("DeskPRO:Common:rulebuilder-date-options.html.twig")->display($context);
            // line 608
            echo "\t\t</div>
\t</div>
\t";
        }
        // line 611
        echo "
\t";
        // line 612
        if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
        if (($_search_context_ != "filter")) {
            // line 613
            echo "    ";
            if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
            if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
            if ((!twig_in_filter("time_created", $_no_show_fields_) && ((!$_event_trigger_) || ($_event_trigger_ != "new_ticket")))) {
                // line 614
                echo "    <div class=\"builder-type\" title=\"";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.time_created");
                echo "\" data-rule-type=\"time_created\">
        <div class=\"builder-op\">
            <select name=\"op\" class=\"op\">
                <option value=\"before\">";
                // line 617
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.before");
                echo "</option>
                <option value=\"after\">";
                // line 618
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.after");
                echo "</option>
            </select>
        </div>
        <div class=\"builder-options\">
            <label>";
                // line 622
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.hour");
                echo "</label>
            <select name=\"hour1\">
                ";
                // line 624
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable(range(0, 23));
                foreach ($context['_seq'] as $context["_key"] => $context["hour"]) {
                    // line 625
                    echo "                <option>";
                    if (isset($context["hour"])) { $_hour_ = $context["hour"]; } else { $_hour_ = null; }
                    echo twig_escape_filter($this->env, sprintf("%02s", $_hour_), "html", null, true);
                    echo "</option>
                ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['hour'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 627
                echo "            </select>
            <label>";
                // line 628
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.minute");
                echo "</label>
            <select name=\"minute1\">
                ";
                // line 630
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable(range(0, 59));
                foreach ($context['_seq'] as $context["_key"] => $context["minute"]) {
                    // line 631
                    echo "                <option>";
                    if (isset($context["minute"])) { $_minute_ = $context["minute"]; } else { $_minute_ = null; }
                    echo twig_escape_filter($this->env, sprintf("%02s", $_minute_), "html", null, true);
                    echo "</option>
                ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['minute'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 633
                echo "            </select>
        </div>
    </div>
    ";
            }
            // line 637
            echo "    ";
        }
        // line 638
        echo "
\t";
        // line 639
        if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
        if (($_search_context_ != "filter")) {
            // line 640
            echo "    ";
            if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
            if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
            if ((!twig_in_filter("time_user_reply", $_no_show_fields_) && ((!$_event_trigger_) || ($_event_trigger_ != "new_ticket")))) {
                // line 641
                echo "    <div class=\"builder-type\" title=\"";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.time_of_last_user_reply");
                echo "\" data-rule-type=\"time_user_replied\">
        <div class=\"builder-op\">
            <select name=\"op\" class=\"op\">
                <option value=\"before\">";
                // line 644
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.before");
                echo "</option>
                <option value=\"after\">";
                // line 645
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.after");
                echo "</option>
            </select>
        </div>
        <div class=\"builder-options\">
            <label>";
                // line 649
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.hour");
                echo "</label>
            <select name=\"hour1\">
                ";
                // line 651
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable(range(0, 23));
                foreach ($context['_seq'] as $context["_key"] => $context["hour"]) {
                    // line 652
                    echo "                <option>";
                    if (isset($context["hour"])) { $_hour_ = $context["hour"]; } else { $_hour_ = null; }
                    echo twig_escape_filter($this->env, sprintf("%02s", $_hour_), "html", null, true);
                    echo "</option>
                ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['hour'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 654
                echo "            </select>
            <label>";
                // line 655
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.minute");
                echo "</label>
            <select name=\"minute1\">
                ";
                // line 657
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable(range(0, 59));
                foreach ($context['_seq'] as $context["_key"] => $context["minute"]) {
                    // line 658
                    echo "                <option>";
                    if (isset($context["minute"])) { $_minute_ = $context["minute"]; } else { $_minute_ = null; }
                    echo twig_escape_filter($this->env, sprintf("%02s", $_minute_), "html", null, true);
                    echo "</option>
                ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['minute'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 660
                echo "            </select>
        </div>
    </div>
    ";
            }
            // line 664
            echo "    ";
        }
        // line 665
        echo "
\t";
        // line 666
        if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
        if (($_search_context_ != "filter")) {
            // line 667
            echo "    ";
            if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
            if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
            if ((!twig_in_filter("day_created", $_no_show_fields_) && ((!$_event_trigger_) || ($_event_trigger_ != "new_ticket")))) {
                // line 668
                echo "    <div class=\"builder-type\" title=\"Week Day\" data-rule-type=\"day_created\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"is\">";
                // line 671
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
                echo "</option>
\t\t\t\t<option value=\"not\">";
                // line 672
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
                echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t";
                // line 676
                $context["days"] = array(1 => "Monday", 2 => "Tuesday", 3 => "Wednesday", 4 => "Thursday", 5 => "Friday", 6 => "Saturday", 0 => "Sunday");
                // line 685
                echo "\t\t\t<select name=\"days[]\" multiple=\"multiple\">
\t\t\t\t";
                // line 686
                if (isset($context["days"])) { $_days_ = $context["days"]; } else { $_days_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($_days_);
                foreach ($context['_seq'] as $context["day"] => $context["name"]) {
                    // line 687
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
                // line 689
                echo "\t\t\t</select>
\t\t</div>
\t</div>
    ";
            }
            // line 693
            echo "\t";
        }
        // line 694
        echo "
\t";
        // line 695
        if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
        if (($_search_context_ != "filter")) {
            // line 696
            echo "    ";
            if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
            if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
            if ((!twig_in_filter("day_last_user_reply", $_no_show_fields_) && ((!$_event_trigger_) || ($_event_trigger_ != "new_ticket")))) {
                // line 697
                echo "    <div class=\"builder-type\" title=\"";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.day_last_user_reply");
                echo "\" data-rule-type=\"day_last_user_reply\">
        <div class=\"builder-op\">
            <select name=\"op\" class=\"op\">
                <option value=\"is\">";
                // line 700
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
                echo "</option>
                <option value=\"not\">";
                // line 701
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
                echo "</option>
            </select>
        </div>
        <div class=\"builder-options\">
            ";
                // line 705
                $context["days"] = array(0 => "Monday", 1 => "Tuesday", 2 => "Wednesday", 3 => "Thursday", 4 => "Friday", 5 => "Saturday", 6 => "Sunday");
                // line 706
                echo "            ";
                if (isset($context["days"])) { $_days_ = $context["days"]; } else { $_days_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($_days_);
                foreach ($context['_seq'] as $context["_key"] => $context["day"]) {
                    // line 707
                    echo "                <label>";
                    if (isset($context["day"])) { $_day_ = $context["day"]; } else { $_day_ = null; }
                    echo twig_escape_filter($this->env, $_day_, "html", null, true);
                    echo "</label>
                <input type=\"checkbox\" value=\"";
                    // line 708
                    if (isset($context["day"])) { $_day_ = $context["day"]; } else { $_day_ = null; }
                    echo twig_escape_filter($this->env, $_day_, "html", null, true);
                    echo "\" name=\"days[]\"/>
            ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['day'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 710
                echo "        </div>
    </div>
    ";
            }
            // line 713
            echo "\t";
        }
        // line 714
        echo "
\t";
        // line 715
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
        if ((!twig_in_filter("date_resolved", $_no_show_fields_) && ((!$_event_trigger_) || ($_event_trigger_ == "time_resolved")))) {
            // line 716
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.date_resolved");
            echo "\" data-rule-type=\"date_resolved\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.DateTerm\" data-term-triggers=\"date-resolved\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"lte\">";
            // line 719
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.before");
            echo "</option>
\t\t\t\t<option value=\"gte\">";
            // line 720
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.after");
            echo "</option>
\t\t\t\t<option value=\"between\">";
            // line 721
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.between");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t";
            // line 725
            $this->env->loadTemplate("DeskPRO:Common:rulebuilder-date-options.html.twig")->display($context);
            // line 726
            echo "\t\t</div>
\t</div>
\t";
        }
        // line 729
        echo "
\t";
        // line 730
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
        if ((!twig_in_filter("date_closed", $_no_show_fields_) && (!$_event_trigger_))) {
            // line 731
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.date_closed");
            echo "\" data-rule-type=\"date_closed\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.DateTerm\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"lte\">";
            // line 734
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.before");
            echo "</option>
\t\t\t\t<option value=\"gte\">";
            // line 735
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.after");
            echo "</option>
\t\t\t\t<option value=\"between\">";
            // line 736
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.between");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t";
            // line 740
            $this->env->loadTemplate("DeskPRO:Common:rulebuilder-date-options.html.twig")->display($context);
            // line 741
            echo "\t\t</div>
\t</div>
\t";
        }
        // line 744
        echo "
\t";
        // line 745
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
        if ((!twig_in_filter("date_last_agent_reply", $_no_show_fields_) && ((!$_event_trigger_) || ($_event_trigger_ != "new_ticket")))) {
            // line 746
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.date_of_last_agent_reply");
            echo "\" data-rule-type=\"date_last_agent_reply\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.DateTerm\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"lte\">";
            // line 749
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.before");
            echo "</option>
\t\t\t\t<option value=\"gte\">";
            // line 750
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.after");
            echo "</option>
\t\t\t\t<option value=\"between\">";
            // line 751
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.between");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t";
            // line 755
            $this->env->loadTemplate("DeskPRO:Common:rulebuilder-date-options.html.twig")->display($context);
            // line 756
            echo "\t\t</div>
\t</div>
\t";
        }
        // line 759
        echo "
\t";
        // line 760
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (!twig_in_filter("date_last_user_reply", $_no_show_fields_)) {
            // line 761
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.date_of_last_user_reply");
            echo "\" data-rule-type=\"date_last_user_reply\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.DateTerm\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t<option value=\"lte\">";
            // line 764
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.before");
            echo "</option>
\t\t\t\t<option value=\"gte\">";
            // line 765
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.after");
            echo "</option>
\t\t\t\t<option value=\"between\">";
            // line 766
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.between");
            echo "</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t";
            // line 770
            $this->env->loadTemplate("DeskPRO:Common:rulebuilder-date-options.html.twig")->display($context);
            // line 771
            echo "\t\t</div>
\t</div>
\t";
        }
        // line 774
        echo "
\t";
        // line 775
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (isset($context["event_trigger"])) { $_event_trigger_ = $context["event_trigger"]; } else { $_event_trigger_ = null; }
        if (((!twig_in_filter("robot_email", $_no_show_fields_) && ($_event_trigger_ == "new_ticket")) || ($_event_trigger_ == "new_reply"))) {
            // line 776
            echo "\t<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.email_sent_by_robot");
            echo "\" data-rule-type=\"robot_email\">
\t\t<div class=\"builder-op\">
\t\t\t<input type=\"hidden\" name=\"op\" value=\"is\" />
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t<input type=\"hidden\" name=\"is_robot_email\" value=\"1\" />
\t\t</div>
\t</div>
\t";
        }
        // line 785
        echo "
\t";
        // line 786
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!twig_in_filter("ticket_field", $_no_show_fields_) && $this->getAttribute($this->getAttribute($_app_, "getTicketFieldManager", array(), "method"), "getFields", array(), "method"))) {
            // line 787
            echo "\t\t";
            $context["op_lang"] = array("is" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is"), "not" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not"), "contains" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contains"), "notcontains" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_contain"), "gt" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_greater_than"), "lt" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_less_than"));
            // line 788
            echo "\t\t";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getTicketFieldManager", array(), "method"), "getDisplayArray", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["field"]) {
                // line 789
                echo "\t\t\t";
                if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
                if ($this->getAttribute($this->getAttribute($_field_, "field_def"), "getFilterCapabilities", array(0 => $_search_context_), "method")) {
                    // line 790
                    echo "\t\t\t<div class=\"builder-type\" title=\"";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_field_, "field_def"), "title"), "html", null, true);
                    echo "\" data-rule-group=\"Ticket Fields\" data-rule-type=\"ticket_field[";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_field_, "field_def"), "id"), "html", null, true);
                    echo "]\">
\t\t\t\t<div class=\"builder-op\">
\t\t\t\t\t<select name=\"op\" class=\"op\">
\t\t\t\t\t\t";
                    // line 793
                    if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
                    if (($_search_context_ == "filter")) {
                        // line 794
                        echo "\t\t\t\t\t\t\t";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_field_, "field_def"), "getFilterCapabilities", array(0 => $_search_context_), "method"));
                        foreach ($context['_seq'] as $context["_key"] => $context["op"]) {
                            // line 795
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
                        // line 797
                        echo "\t\t\t\t\t\t";
                    } else {
                        // line 798
                        echo "\t\t\t\t\t\t\t";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_field_, "field_def"), "getSearchCapabilities", array(0 => $_search_context_), "method"));
                        foreach ($context['_seq'] as $context["_key"] => $context["op"]) {
                            // line 799
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
                        // line 801
                        echo "\t\t\t\t\t\t";
                    }
                    // line 802
                    echo "\t\t\t\t\t</select>
\t\t\t\t</div>
\t\t\t\t<div class=\"builder-options\">
\t\t\t\t\t";
                    // line 805
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    echo $this->env->getExtension('form')->renderWidget($this->getAttribute($_field_, "formView"));
                    echo "
\t\t\t\t</div>
\t\t\t</div>
\t\t\t";
                }
                // line 809
                echo "\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['field'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 810
            echo "\t";
        }
        // line 811
        echo "
\t";
        // line 812
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (!twig_in_filter("person", $_no_show_fields_)) {
            // line 813
            echo "\t\t";
            if (isset($context["people_term_options"])) { $_people_term_options_ = $context["people_term_options"]; } else { $_people_term_options_ = null; }
            $this->env->loadTemplate("DeskPRO:Common:people-search-criteria.html.twig")->display(array_merge($context, array("with_rule_group" => true, "term_options" => $_people_term_options_, "id" => false, "classname" => false)));
            // line 814
            echo "\t";
        }
        // line 815
        echo "
\t";
        // line 816
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (!twig_in_filter("org", $_no_show_fields_)) {
            // line 817
            echo "\t\t";
            if (isset($context["people_term_options"])) { $_people_term_options_ = $context["people_term_options"]; } else { $_people_term_options_ = null; }
            $this->env->loadTemplate("DeskPRO:Common:org-search-criteria.html.twig")->display(array_merge($context, array("with_rule_group" => true, "term_options" => $_people_term_options_, "id" => false, "classname" => false)));
            // line 818
            echo "\t";
        }
        // line 819
        echo "
";
        // line 820
        if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
        if (isset($context["classname"])) { $_classname_ = $context["classname"]; } else { $_classname_ = null; }
        if (($_id_ || $_classname_)) {
            // line 821
            echo "</div>
";
        }
    }

    public function getTemplateName()
    {
        return "DeskPRO:Common:ticket-search-criteria.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 793,  2042 => 790,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 774,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 745,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 707,  1829 => 706,  1827 => 705,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 689,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 676,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 666,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 638,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 617,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 611,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 602,  1559 => 601,  1552 => 598,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 584,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 573,  1477 => 567,  1473 => 566,  1467 => 562,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 520,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 511,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 481,  1257 => 480,  1254 => 479,  1250 => 478,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 460,  1196 => 458,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 452,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 445,  1146 => 444,  1137 => 437,  1123 => 426,  1119 => 425,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 421,  1099 => 420,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 412,  1076 => 411,  1070 => 407,  1057 => 405,  1052 => 404,  1045 => 399,  1040 => 397,  1036 => 396,  1032 => 395,  1028 => 394,  1023 => 393,  1020 => 392,  1016 => 391,  1012 => 390,  1005 => 387,  1001 => 386,  998 => 385,  992 => 381,  979 => 379,  974 => 378,  967 => 373,  962 => 371,  958 => 370,  954 => 369,  950 => 368,  945 => 367,  942 => 366,  938 => 365,  934 => 364,  927 => 361,  923 => 360,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 341,  868 => 328,  856 => 320,  853 => 319,  849 => 318,  845 => 317,  841 => 316,  835 => 312,  830 => 310,  826 => 309,  822 => 308,  818 => 307,  813 => 306,  810 => 305,  806 => 304,  802 => 303,  795 => 300,  792 => 299,  789 => 298,  784 => 295,  782 => 294,  777 => 291,  772 => 289,  768 => 288,  763 => 287,  760 => 286,  756 => 285,  752 => 284,  745 => 281,  741 => 280,  738 => 279,  732 => 275,  719 => 273,  714 => 272,  710 => 271,  704 => 267,  699 => 265,  695 => 264,  690 => 263,  687 => 262,  683 => 261,  679 => 260,  672 => 257,  668 => 256,  665 => 255,  658 => 250,  645 => 248,  640 => 247,  634 => 244,  628 => 240,  623 => 238,  619 => 237,  611 => 235,  606 => 234,  603 => 233,  599 => 232,  595 => 231,  583 => 227,  580 => 226,  573 => 221,  560 => 219,  543 => 211,  538 => 209,  534 => 208,  530 => 207,  526 => 206,  521 => 205,  518 => 204,  514 => 203,  510 => 202,  503 => 199,  496 => 197,  490 => 193,  484 => 192,  474 => 190,  470 => 188,  446 => 185,  440 => 184,  436 => 183,  431 => 182,  425 => 178,  416 => 175,  412 => 174,  408 => 173,  403 => 172,  400 => 171,  396 => 170,  392 => 169,  385 => 166,  381 => 165,  367 => 156,  363 => 155,  359 => 154,  355 => 153,  350 => 150,  346 => 149,  343 => 148,  328 => 139,  324 => 138,  313 => 134,  307 => 132,  301 => 128,  288 => 126,  283 => 125,  271 => 119,  257 => 114,  251 => 110,  238 => 108,  233 => 107,  195 => 90,  191 => 89,  187 => 88,  183 => 87,  130 => 58,  88 => 33,  76 => 27,  115 => 48,  95 => 39,  655 => 233,  651 => 232,  648 => 231,  637 => 223,  633 => 222,  621 => 219,  618 => 218,  615 => 236,  604 => 209,  600 => 208,  588 => 228,  585 => 204,  582 => 203,  571 => 195,  567 => 194,  555 => 218,  552 => 190,  549 => 215,  544 => 186,  542 => 185,  535 => 181,  531 => 180,  519 => 177,  516 => 176,  513 => 175,  508 => 172,  506 => 171,  499 => 198,  495 => 166,  491 => 165,  481 => 162,  478 => 161,  475 => 160,  469 => 156,  456 => 154,  451 => 186,  443 => 148,  439 => 147,  427 => 144,  423 => 143,  420 => 176,  409 => 134,  405 => 133,  401 => 132,  391 => 129,  387 => 128,  384 => 127,  378 => 164,  365 => 121,  360 => 120,  348 => 114,  336 => 111,  332 => 140,  329 => 109,  323 => 105,  310 => 133,  305 => 102,  277 => 92,  274 => 91,  263 => 83,  259 => 82,  247 => 79,  244 => 78,  241 => 77,  222 => 67,  210 => 97,  207 => 96,  204 => 62,  184 => 53,  181 => 52,  167 => 50,  157 => 76,  96 => 38,  421 => 153,  417 => 152,  414 => 151,  406 => 148,  398 => 144,  393 => 141,  390 => 140,  376 => 138,  369 => 137,  366 => 136,  352 => 115,  345 => 133,  342 => 132,  331 => 129,  326 => 128,  320 => 137,  317 => 126,  314 => 125,  311 => 124,  308 => 123,  297 => 97,  293 => 96,  281 => 93,  278 => 110,  275 => 120,  264 => 116,  260 => 115,  248 => 97,  245 => 96,  242 => 95,  231 => 87,  227 => 86,  215 => 83,  212 => 82,  209 => 81,  197 => 60,  177 => 67,  171 => 81,  161 => 60,  132 => 50,  121 => 42,  105 => 38,  99 => 39,  81 => 27,  77 => 30,  180 => 76,  176 => 75,  156 => 64,  143 => 46,  139 => 56,  118 => 44,  189 => 56,  185 => 69,  173 => 63,  166 => 68,  152 => 62,  174 => 66,  164 => 78,  154 => 56,  150 => 55,  137 => 48,  133 => 59,  127 => 57,  107 => 42,  102 => 37,  83 => 31,  78 => 30,  53 => 18,  23 => 2,  42 => 6,  138 => 52,  134 => 39,  109 => 34,  103 => 40,  97 => 29,  94 => 33,  84 => 32,  75 => 20,  69 => 25,  66 => 24,  54 => 11,  44 => 21,  230 => 69,  226 => 68,  203 => 78,  193 => 71,  188 => 67,  182 => 63,  178 => 71,  168 => 64,  163 => 61,  160 => 77,  155 => 55,  148 => 56,  145 => 67,  140 => 46,  136 => 45,  125 => 45,  120 => 39,  113 => 39,  101 => 33,  92 => 32,  89 => 27,  85 => 35,  73 => 19,  62 => 22,  59 => 21,  56 => 13,  41 => 8,  126 => 24,  119 => 36,  111 => 43,  106 => 39,  98 => 32,  93 => 17,  86 => 14,  70 => 25,  60 => 8,  28 => 8,  36 => 5,  114 => 21,  104 => 19,  91 => 38,  80 => 15,  63 => 15,  58 => 12,  40 => 8,  34 => 14,  45 => 19,  61 => 23,  55 => 6,  48 => 7,  39 => 16,  35 => 4,  31 => 11,  26 => 6,  21 => 1,  46 => 6,  29 => 3,  57 => 22,  50 => 10,  47 => 6,  38 => 5,  33 => 4,  49 => 20,  32 => 8,  246 => 90,  236 => 84,  232 => 82,  225 => 102,  221 => 101,  216 => 79,  214 => 98,  211 => 78,  208 => 77,  205 => 70,  199 => 91,  196 => 71,  190 => 61,  179 => 66,  175 => 82,  172 => 54,  169 => 53,  162 => 67,  158 => 57,  153 => 45,  151 => 44,  147 => 42,  144 => 41,  141 => 49,  135 => 51,  129 => 52,  124 => 37,  117 => 41,  112 => 40,  90 => 16,  87 => 26,  82 => 24,  72 => 26,  68 => 18,  65 => 23,  52 => 11,  43 => 13,  37 => 5,  30 => 10,  27 => 3,  25 => 3,  24 => 2,  22 => 2,  19 => 1,);
    }
}
