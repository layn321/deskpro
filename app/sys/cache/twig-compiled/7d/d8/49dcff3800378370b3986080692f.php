<?php

/* DeskPRO:Common:org-search-criteria.html.twig */
class __TwigTemplate_7dd849dcff3800378370b3986080692f extends \Application\DeskPRO\Twig\Template
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
        // line 22
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (!twig_in_filter("org_date_created", $_no_show_fields_)) {
            // line 23
            echo "<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.date_created");
            echo "\" ";
            if (isset($context["with_rule_group"])) { $_with_rule_group_ = $context["with_rule_group"]; } else { $_with_rule_group_ = null; }
            if ($_with_rule_group_) {
                echo "data-rule-group=\"Organization\"";
            }
            echo " data-rule-type=\"org_date_created\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.DateTerm\" data-term-triggers=\"created\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\" class=\"op\">
\t\t\t<option value=\"lte\">";
            // line 26
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.before");
            echo "</option>
\t\t\t<option value=\"gte\">";
            // line 27
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.after");
            echo "</option>
\t\t\t<option value=\"between\">";
            // line 28
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.between");
            echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t";
            // line 32
            $this->env->loadTemplate("DeskPRO:Common:rulebuilder-date-options.html.twig")->display($context);
            // line 33
            echo "\t</div>
</div>
";
        }
        // line 36
        echo "
";
        // line 37
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (!twig_in_filter("org_email_domain", $_no_show_fields_)) {
            // line 38
            echo "<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.email_domains");
            echo "\" ";
            if (isset($context["with_rule_group"])) { $_with_rule_group_ = $context["with_rule_group"]; } else { $_with_rule_group_ = null; }
            if ($_with_rule_group_) {
                echo "data-rule-group=\"Organization\"";
            }
            echo " data-rule-type=\"org_email_domain\" data-term-type=\"GenericInputTerm\" data-term-triggers=\"emaildomain\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\">
\t\t\t<option value=\"is\">";
            // line 41
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t<option value=\"not\">";
            // line 42
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"email_domain\" value=\"\" />
\t</div>
</div>
";
        }
        // line 50
        echo "
";
        // line 51
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (!twig_in_filter("org_label", $_no_show_fields_)) {
            // line 52
            echo "<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.labels");
            echo "\" ";
            if (isset($context["with_rule_group"])) { $_with_rule_group_ = $context["with_rule_group"]; } else { $_with_rule_group_ = null; }
            if ($_with_rule_group_) {
                echo "data-rule-group=\"Organization\"";
            }
            echo " data-rule-type=\"org_label\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.LabelsTerm\" data-term-triggers=\"label,labelled,labels\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\" class=\"op\">
\t\t\t<option value=\"is\">";
            // line 55
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t<option value=\"not\">";
            // line 56
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t";
            // line 60
            $this->env->loadTemplate("DeskPRO:Common:rulebuilder-labels-options.html.twig")->display($context);
            // line 61
            echo "\t</div>
</div>
";
        }
        // line 64
        echo "
";
        // line 65
        if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
        if (($_search_context_ != "filter")) {
            // line 66
            if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
            if (!twig_in_filter("org_name", $_no_show_fields_)) {
                // line 67
                echo "<div class=\"builder-type\" title=\"";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.name");
                echo "\" ";
                if (isset($context["with_rule_group"])) { $_with_rule_group_ = $context["with_rule_group"]; } else { $_with_rule_group_ = null; }
                if ($_with_rule_group_) {
                    echo "data-rule-group=\"Organization\"";
                }
                echo " data-rule-type=\"org_name\" data-term-type=\"GenericInputTerm\" data-term-triggers=\"name\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\">
\t\t\t<option value=\"is\">";
                // line 70
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
                echo "</option>
\t\t\t<option value=\"contains\">";
                // line 71
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contains");
                echo "</option>
\t\t\t<option value=\"notcontains\">";
                // line 72
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_contain");
                echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"name\" value=\"\" />
\t</div>
</div>
";
            }
        }
        // line 81
        echo "
";
        // line 82
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (!twig_in_filter("org_contact_phone", $_no_show_fields_)) {
            // line 83
            echo "<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contact_phone");
            echo "\" ";
            if (isset($context["with_rule_group"])) { $_with_rule_group_ = $context["with_rule_group"]; } else { $_with_rule_group_ = null; }
            if ($_with_rule_group_) {
                echo "data-rule-group=\"Organization\"";
            }
            echo " data-rule-type=\"org_contact_phone\" data-term-type=\"GenericInputTerm\" data-term-triggers=\"phone\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\">
\t\t\t<option value=\"contains\">";
            // line 86
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t<option value=\"notcontains\">";
            // line 87
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"phone\" value=\"\" />
\t</div>
</div>
";
        }
        // line 95
        echo "
";
        // line 96
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (!twig_in_filter("org_contact_address", $_no_show_fields_)) {
            // line 97
            echo "<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contact_address");
            echo "\" ";
            if (isset($context["with_rule_group"])) { $_with_rule_group_ = $context["with_rule_group"]; } else { $_with_rule_group_ = null; }
            if ($_with_rule_group_) {
                echo "data-rule-group=\"Organization\"";
            }
            echo " data-rule-type=\"org_contact_address\" data-term-type=\"GenericInputTerm\" data-term-triggers=\"address\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\">
\t\t\t<option value=\"contains\">";
            // line 100
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t<option value=\"notcontains\">";
            // line 101
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"address\" value=\"\" />
\t</div>
</div>
";
        }
        // line 109
        echo "
";
        // line 110
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (!twig_in_filter("org_contact_im", $_no_show_fields_)) {
            // line 111
            echo "<div class=\"builder-type\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contact_instant_messaging");
            echo "\" ";
            if (isset($context["with_rule_group"])) { $_with_rule_group_ = $context["with_rule_group"]; } else { $_with_rule_group_ = null; }
            if ($_with_rule_group_) {
                echo "data-rule-group=\"Organization\"";
            }
            echo " data-rule-type=\"org_contact_im\" data-term-type=\"GenericInputTerm\" data-term-triggers=\"messenger\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\">
\t\t\t<option value=\"contains\">";
            // line 114
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
            echo "</option>
\t\t\t<option value=\"notcontains\">";
            // line 115
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
            echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"im\" value=\"\" />
\t</div>
</div>
";
        }
        // line 123
        echo "
";
        // line 124
        if (isset($context["no_show_fields"])) { $_no_show_fields_ = $context["no_show_fields"]; } else { $_no_show_fields_ = null; }
        if (!twig_in_filter("org_field", $_no_show_fields_)) {
            // line 125
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($this->getAttribute($_app_, "getOrgFieldManager", array(), "method"), "getFields", array(), "method")) {
                // line 126
                echo "\t";
                $context["op_lang"] = array("is" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is"), "not" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not"), "contains" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.contains"), "notcontains" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_contain"), "gt" => "is greater than", "lt" => "is less than");
                // line 127
                echo "\t";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getOrgFieldManager", array(), "method"), "getDisplayArray", array(), "method"));
                foreach ($context['_seq'] as $context["_key"] => $context["field"]) {
                    // line 128
                    echo "\t";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
                    if ($this->getAttribute($this->getAttribute($_field_, "field_def"), "getFilterCapabilities", array(0 => $_search_context_), "method")) {
                        // line 129
                        echo "\t<div class=\"builder-type\" title=\"";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_field_, "field_def"), "title"), "html", null, true);
                        echo "\" data-rule-group=\"Organization Fields\" data-rule-type=\"org_field[";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_field_, "field_def"), "id"), "html", null, true);
                        echo "]\">
\t\t<div class=\"builder-op\">
\t\t\t<select name=\"op\">
\t\t\t\t";
                        // line 132
                        if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
                        if (($_search_context_ == "filter")) {
                            // line 133
                            echo "\t\t\t\t\t";
                            if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                            if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
                            $context['_parent'] = (array) $context;
                            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_field_, "field_def"), "getFilterCapabilities", array(0 => $_search_context_), "method"));
                            foreach ($context['_seq'] as $context["_key"] => $context["op"]) {
                                // line 134
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
                            // line 136
                            echo "\t\t\t\t";
                        } else {
                            // line 137
                            echo "\t\t\t\t\t";
                            if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                            if (isset($context["search_context"])) { $_search_context_ = $context["search_context"]; } else { $_search_context_ = null; }
                            $context['_parent'] = (array) $context;
                            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_field_, "field_def"), "getSearchCapabilities", array(0 => $_search_context_), "method"));
                            foreach ($context['_seq'] as $context["_key"] => $context["op"]) {
                                // line 138
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
                            // line 140
                            echo "\t\t\t\t";
                        }
                        // line 141
                        echo "\t\t\t</select>
\t\t</div>
\t\t<div class=\"builder-options\">
\t\t\t";
                        // line 144
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        echo $this->env->getExtension('form')->renderWidget($this->getAttribute($_field_, "formView"));
                        echo "
\t\t</div>
\t</div>
\t";
                    }
                    // line 148
                    echo "\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['field'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
            }
        }
        // line 151
        echo "
";
        // line 152
        if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
        if (isset($context["classname"])) { $_classname_ = $context["classname"]; } else { $_classname_ = null; }
        if (($_id_ || $_classname_)) {
            // line 153
            echo "\t</div>
";
        }
    }

    public function getTemplateName()
    {
        return "DeskPRO:Common:org-search-criteria.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  421 => 153,  417 => 152,  414 => 151,  406 => 148,  398 => 144,  393 => 141,  390 => 140,  376 => 138,  369 => 137,  366 => 136,  352 => 134,  345 => 133,  342 => 132,  331 => 129,  326 => 128,  320 => 127,  317 => 126,  314 => 125,  311 => 124,  308 => 123,  297 => 115,  293 => 114,  281 => 111,  278 => 110,  275 => 109,  264 => 101,  260 => 100,  248 => 97,  245 => 96,  242 => 95,  231 => 87,  227 => 86,  215 => 83,  212 => 82,  209 => 81,  197 => 72,  177 => 67,  171 => 65,  161 => 60,  132 => 50,  121 => 42,  105 => 38,  99 => 36,  81 => 27,  77 => 26,  180 => 76,  176 => 75,  156 => 64,  143 => 57,  139 => 56,  118 => 44,  189 => 70,  185 => 69,  173 => 63,  166 => 68,  152 => 62,  174 => 66,  164 => 63,  154 => 56,  150 => 55,  137 => 48,  133 => 47,  127 => 47,  107 => 36,  102 => 37,  83 => 31,  78 => 30,  53 => 18,  23 => 2,  42 => 6,  138 => 52,  134 => 39,  109 => 34,  103 => 33,  97 => 29,  94 => 33,  84 => 25,  75 => 20,  69 => 17,  66 => 24,  54 => 11,  44 => 21,  230 => 84,  226 => 82,  203 => 78,  193 => 71,  188 => 67,  182 => 63,  178 => 71,  168 => 64,  163 => 61,  160 => 62,  155 => 55,  148 => 56,  145 => 50,  140 => 46,  136 => 44,  125 => 45,  120 => 39,  113 => 39,  101 => 33,  92 => 32,  89 => 27,  85 => 28,  73 => 19,  62 => 22,  59 => 21,  56 => 13,  41 => 8,  126 => 24,  119 => 36,  111 => 20,  106 => 34,  98 => 32,  93 => 17,  86 => 14,  70 => 25,  60 => 8,  28 => 8,  36 => 5,  114 => 21,  104 => 19,  91 => 16,  80 => 15,  63 => 15,  58 => 12,  40 => 8,  34 => 14,  45 => 7,  61 => 12,  55 => 6,  48 => 7,  39 => 16,  35 => 4,  31 => 6,  26 => 4,  21 => 1,  46 => 6,  29 => 3,  57 => 9,  50 => 10,  47 => 6,  38 => 5,  33 => 4,  49 => 10,  32 => 4,  246 => 90,  236 => 84,  232 => 82,  225 => 78,  221 => 77,  216 => 79,  214 => 73,  211 => 78,  208 => 77,  205 => 70,  199 => 77,  196 => 71,  190 => 61,  179 => 66,  175 => 64,  172 => 54,  169 => 53,  162 => 67,  158 => 57,  153 => 45,  151 => 44,  147 => 42,  144 => 41,  141 => 49,  135 => 51,  129 => 52,  124 => 37,  117 => 41,  112 => 29,  90 => 16,  87 => 26,  82 => 24,  72 => 22,  68 => 18,  65 => 23,  52 => 11,  43 => 13,  37 => 5,  30 => 3,  27 => 3,  25 => 3,  24 => 2,  22 => 2,  19 => 1,);
    }
}
