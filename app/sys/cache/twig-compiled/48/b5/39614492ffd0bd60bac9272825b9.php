<?php

/* DeskPRO:Common:rulebuilder-date-options.html.twig */
class __TwigTemplate_48b539614492ffd0bd60bac9272825b9 extends \Application\DeskPRO\Twig\Template
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
        echo "<span class=\"status-value-outer\"><span class=\"status-value\"></span> <i class=\"icon-caret-down\"></i></span>
<input type=\"hidden\" class=\"date1-input\" name=\"date1\" value=\"\" />
<input type=\"hidden\" class=\"date2-input\" name=\"date2\" value=\"\" />
<input type=\"hidden\" class=\"date1-relative-input\" name=\"date1_relative\" value=\"\" />
<input type=\"hidden\" class=\"date1-relative-type\" name=\"date1_relative_type\" value=\"\" />
<input type=\"hidden\" class=\"date2-relative-input\" name=\"date2_relative\" value=\"\" />
<input type=\"hidden\" class=\"date2-relative-type\" name=\"date2_relative_type\" value=\"\" />
<div class=\"date-wrap two\" style=\"display: none\">
\t<div class=\"date1\">
\t\t<div class=\"date\">
\t\t\t<span class=\"switcher\">";
        // line 11
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.relative_time");
        echo "</span>
\t\t\t<input type=\"text\" class=\"date1-display\" />
\t\t\t<div class=\"widget\"></div>
\t\t</div>
\t\t<div class=\"relative relative1\" style=\"display: none;\">
\t\t\t<span class=\"switcher\">";
        // line 16
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.calendar");
        echo "</span>
\t\t\t<input type=\"text\" class=\"relative1-input\" /><br />
\t\t\t<select class=\"relative1-type\">
\t\t\t\t<option value=\"minutes\">";
        // line 19
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.minutes");
        echo "</option>
\t\t\t\t<option value=\"hours\">";
        // line 20
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.hours");
        echo "</option>
\t\t\t\t<option value=\"days\">";
        // line 21
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.days");
        echo "</option>
\t\t\t\t<option value=\"weeks\">";
        // line 22
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.weeks");
        echo "</option>
\t\t\t\t<option value=\"months\">";
        // line 23
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.months");
        echo "</option>
\t\t\t\t<option value=\"years\">";
        // line 24
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.years");
        echo "</option>
\t\t\t</select> ";
        // line 25
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ago");
        echo "
\t\t</div>
\t</div>
\t<div class=\"date2\">
\t\t<div class=\"date\">
\t\t\t<span class=\"switcher\">";
        // line 30
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.relative_time");
        echo "</span>
\t\t\t<input type=\"text\" class=\"date2-display\" />
\t\t\t<div class=\"widget\"></div>
\t\t</div>
\t\t<div class=\"relative relative2\" style=\"display: none;\">
\t\t\t<span class=\"switcher\">";
        // line 35
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.calendar");
        echo "</span>
\t\t\t<input type=\"text\" class=\"relative2-input\" /><br />
\t\t\t<select class=\"relative2-type\">
\t\t\t\t<option value=\"minutes\">";
        // line 38
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.minutes");
        echo "</option>
\t\t\t\t<option value=\"hours\">";
        // line 39
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.hours");
        echo "</option>
\t\t\t\t<option value=\"days\">";
        // line 40
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.days");
        echo "</option>
\t\t\t\t<option value=\"weeks\">";
        // line 41
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.weeks");
        echo "</option>
\t\t\t\t<option value=\"months\">";
        // line 42
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.months");
        echo "</option>
\t\t\t\t<option value=\"years\">";
        // line 43
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.years");
        echo "</option>
\t\t\t</select> ";
        // line 44
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ago");
        echo "
\t\t</div>
\t</div>
</div>";
    }

    public function getTemplateName()
    {
        return "DeskPRO:Common:rulebuilder-date-options.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  115 => 44,  95 => 39,  655 => 233,  651 => 232,  648 => 231,  637 => 223,  633 => 222,  621 => 219,  618 => 218,  615 => 217,  604 => 209,  600 => 208,  588 => 205,  585 => 204,  582 => 203,  571 => 195,  567 => 194,  555 => 191,  552 => 190,  549 => 189,  544 => 186,  542 => 185,  535 => 181,  531 => 180,  519 => 177,  516 => 176,  513 => 175,  508 => 172,  506 => 171,  499 => 167,  495 => 166,  491 => 165,  481 => 162,  478 => 161,  475 => 160,  469 => 156,  456 => 154,  451 => 153,  443 => 148,  439 => 147,  427 => 144,  423 => 143,  420 => 142,  409 => 134,  405 => 133,  401 => 132,  391 => 129,  387 => 128,  384 => 127,  378 => 123,  365 => 121,  360 => 120,  348 => 114,  336 => 111,  332 => 110,  329 => 109,  323 => 105,  310 => 103,  305 => 102,  277 => 92,  274 => 91,  263 => 83,  259 => 82,  247 => 79,  244 => 78,  241 => 77,  222 => 67,  210 => 64,  207 => 63,  204 => 62,  184 => 53,  181 => 52,  167 => 50,  157 => 48,  96 => 36,  421 => 153,  417 => 152,  414 => 151,  406 => 148,  398 => 144,  393 => 141,  390 => 140,  376 => 138,  369 => 137,  366 => 136,  352 => 115,  345 => 133,  342 => 132,  331 => 129,  326 => 128,  320 => 127,  317 => 126,  314 => 125,  311 => 124,  308 => 123,  297 => 97,  293 => 96,  281 => 93,  278 => 110,  275 => 109,  264 => 101,  260 => 100,  248 => 97,  245 => 96,  242 => 95,  231 => 87,  227 => 86,  215 => 83,  212 => 82,  209 => 81,  197 => 60,  177 => 67,  171 => 65,  161 => 60,  132 => 50,  121 => 42,  105 => 38,  99 => 40,  81 => 27,  77 => 30,  180 => 76,  176 => 75,  156 => 64,  143 => 46,  139 => 56,  118 => 44,  189 => 56,  185 => 69,  173 => 63,  166 => 68,  152 => 62,  174 => 66,  164 => 63,  154 => 56,  150 => 55,  137 => 48,  133 => 44,  127 => 47,  107 => 42,  102 => 37,  83 => 31,  78 => 30,  53 => 21,  23 => 2,  42 => 6,  138 => 52,  134 => 39,  109 => 34,  103 => 41,  97 => 29,  94 => 33,  84 => 25,  75 => 20,  69 => 25,  66 => 24,  54 => 11,  44 => 21,  230 => 69,  226 => 68,  203 => 78,  193 => 71,  188 => 67,  182 => 63,  178 => 71,  168 => 64,  163 => 61,  160 => 49,  155 => 55,  148 => 56,  145 => 50,  140 => 46,  136 => 45,  125 => 45,  120 => 39,  113 => 39,  101 => 33,  92 => 32,  89 => 27,  85 => 35,  73 => 19,  62 => 22,  59 => 21,  56 => 13,  41 => 8,  126 => 24,  119 => 36,  111 => 43,  106 => 39,  98 => 32,  93 => 17,  86 => 14,  70 => 25,  60 => 8,  28 => 8,  36 => 5,  114 => 21,  104 => 19,  91 => 38,  80 => 15,  63 => 15,  58 => 12,  40 => 8,  34 => 14,  45 => 19,  61 => 23,  55 => 6,  48 => 7,  39 => 16,  35 => 4,  31 => 11,  26 => 6,  21 => 1,  46 => 6,  29 => 3,  57 => 22,  50 => 10,  47 => 6,  38 => 5,  33 => 4,  49 => 20,  32 => 8,  246 => 90,  236 => 84,  232 => 82,  225 => 78,  221 => 77,  216 => 79,  214 => 73,  211 => 78,  208 => 77,  205 => 70,  199 => 77,  196 => 71,  190 => 61,  179 => 66,  175 => 64,  172 => 54,  169 => 53,  162 => 67,  158 => 57,  153 => 45,  151 => 44,  147 => 42,  144 => 41,  141 => 49,  135 => 51,  129 => 52,  124 => 37,  117 => 41,  112 => 40,  90 => 16,  87 => 26,  82 => 24,  72 => 22,  68 => 18,  65 => 24,  52 => 11,  43 => 13,  37 => 5,  30 => 3,  27 => 3,  25 => 3,  24 => 2,  22 => 2,  19 => 1,);
    }
}
