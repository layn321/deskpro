<?php

/* DeskPRO:Common:kb-search-criteria.html.twig */
class __TwigTemplate_4ee1b11948ac6735fdedd39d539248aa extends \Application\DeskPRO\Twig\Template
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
        echo "<div class=\"builder-type\" title=\"";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.category");
        echo "\" data-rule-type=\"category\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\" class=\"op\">
\t\t\t<option value=\"is\">";
        // line 24
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
        echo "</option>
\t\t\t<option value=\"not\">";
        // line 25
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
        echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<select name=\"category\">
\t\t\t";
        // line 30
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "categories"));
        foreach ($context['_seq'] as $context["_key"] => $context["cat"]) {
            // line 31
            echo "\t\t\t\t<option value=\"";
            if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "id"), "html", null, true);
            echo "\">";
            if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
            if ($this->getAttribute($_cat_, "depth")) {
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->strRepeat("--", $this->getAttribute($_cat_, "depth")), "html", null, true);
                echo " ";
            }
            if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
            echo "</option>
\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['cat'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 33
        echo "\t\t</select>
\t</div>
</div>
<div class=\"builder-type\" title=\"";
        // line 36
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.status");
        echo "\" data-rule-type=\"status\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\" class=\"op\">
\t\t\t<option value=\"is\">";
        // line 39
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
        echo "</option>
\t\t\t<option value=\"not\">";
        // line 40
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
        echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<select name=\"status\">
\t\t\t<option value=\"published\">";
        // line 45
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.status_published");
        echo "</option>
\t\t\t<option value=\"archived\">";
        // line 46
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.status_archived");
        echo "</option>
\t\t\t<option value=\"hidden\">";
        // line 47
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.hidden");
        echo "</option>
\t\t\t<option value=\"hidden.unpublished\">";
        // line 48
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.hidden_unpublished");
        echo "</option>
\t\t\t<option value=\"hidden.validating\">";
        // line 49
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.hidden_validating");
        echo "</option>
\t\t\t<option value=\"hidden.deleted\">";
        // line 50
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.hidden_deleted");
        echo "</option>
\t\t</select>
\t</div>
</div>
<div class=\"builder-type\" title=\"";
        // line 54
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.date_created");
        echo "\" data-rule-type=\"date_created\" data-rule-handler=\"DeskPRO.Agent.RuleBuilder.DateTerm\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\" class=\"op\">
\t\t\t<option value=\"lte\">";
        // line 57
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.before");
        echo "</option>
\t\t\t<option value=\"gte\">";
        // line 58
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.after");
        echo "</option>
\t\t\t<option value=\"between\">";
        // line 59
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.between");
        echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t";
        // line 63
        $this->env->loadTemplate("DeskPRO:Common:rulebuilder-date-options.html.twig")->display($context);
        // line 64
        echo "\t</div>
</div>
<div class=\"builder-type\" title=\"";
        // line 66
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.labels");
        echo "\" data-rule-type=\"label\">
\t<div class=\"builder-op\">
\t\t<select name=\"op\" class=\"op\">
\t\t\t<option value=\"is\">";
        // line 69
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.include");
        echo "</option>
\t\t\t<option value=\"not\">";
        // line 70
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.does_not_include");
        echo "</option>
\t\t</select>
\t</div>
\t<div class=\"builder-options\">
\t\t<input type=\"text\" name=\"label\" value=\"\" />
\t</div>
</div>
";
        // line 77
        if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
        if (isset($context["classname"])) { $_classname_ = $context["classname"]; } else { $_classname_ = null; }
        if (($_id_ || $_classname_)) {
            // line 78
            echo "</div>
";
        }
    }

    public function getTemplateName()
    {
        return "DeskPRO:Common:kb-search-criteria.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  189 => 70,  185 => 69,  173 => 63,  166 => 59,  152 => 54,  174 => 70,  164 => 63,  154 => 59,  150 => 57,  137 => 48,  133 => 47,  127 => 47,  107 => 36,  102 => 33,  83 => 31,  78 => 30,  53 => 18,  23 => 2,  42 => 6,  138 => 41,  134 => 39,  109 => 34,  103 => 33,  97 => 29,  94 => 28,  84 => 25,  75 => 20,  69 => 17,  66 => 24,  54 => 11,  44 => 21,  230 => 84,  226 => 82,  203 => 78,  193 => 70,  188 => 67,  182 => 63,  178 => 71,  168 => 58,  163 => 57,  160 => 62,  155 => 55,  148 => 56,  145 => 50,  140 => 46,  136 => 44,  125 => 45,  120 => 39,  113 => 39,  101 => 33,  92 => 28,  89 => 27,  85 => 25,  73 => 19,  62 => 15,  59 => 21,  56 => 13,  41 => 8,  126 => 24,  119 => 36,  111 => 20,  106 => 34,  98 => 32,  93 => 17,  86 => 14,  70 => 25,  60 => 8,  28 => 8,  36 => 5,  114 => 21,  104 => 19,  91 => 16,  80 => 15,  63 => 15,  58 => 12,  40 => 8,  34 => 14,  45 => 7,  61 => 12,  55 => 6,  48 => 7,  39 => 16,  35 => 4,  31 => 6,  26 => 4,  21 => 1,  46 => 6,  29 => 3,  57 => 9,  50 => 10,  47 => 6,  38 => 5,  33 => 4,  49 => 10,  32 => 4,  246 => 90,  236 => 84,  232 => 82,  225 => 78,  221 => 77,  216 => 79,  214 => 73,  211 => 78,  208 => 77,  205 => 70,  199 => 77,  196 => 71,  190 => 61,  179 => 66,  175 => 64,  172 => 54,  169 => 53,  162 => 58,  158 => 57,  153 => 45,  151 => 44,  147 => 42,  144 => 41,  141 => 49,  135 => 39,  129 => 46,  124 => 37,  117 => 40,  112 => 29,  90 => 16,  87 => 26,  82 => 24,  72 => 22,  68 => 18,  65 => 14,  52 => 11,  43 => 13,  37 => 5,  30 => 3,  27 => 3,  25 => 3,  24 => 2,  22 => 2,  19 => 1,);
    }
}
