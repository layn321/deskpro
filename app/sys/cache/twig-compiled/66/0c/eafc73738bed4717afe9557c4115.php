<?php

/* DeskPRO:Auth:twitter-login-tab.html.twig */
class __TwigTemplate_660ceafc73738bed4717afe9557c4115 extends \Application\DeskPRO\Twig\Template
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
        echo "<div class=\"twitter-login-tab\" data-element-handler=\"DeskPRO.User.ElementHandler.TwitterLogin\">
\t<div class=\"not-logged-in\">
\t\t<a href=\"\"></a>
\t</div>
\t<div class=\"logged-in\">
\t\t<div class=\"dp-form-row\">
\t\t\t<div class=\"dp-form-label\">
\t\t\t\t<label>";
        // line 8
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.logged_in_as");
        echo "</label>
\t\t\t</div>
\t\t\t<div class=\"dp-form-input\">
\t\t\t\t<span class=\"twitter-link\"></span>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"dp-form-row\">
\t\t\t<div class=\"dp-form-label\">
\t\t\t\t<label>";
        // line 16
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.display_name");
        echo " *</label>
\t\t\t</div>
\t\t\t<div class=\"dp-form-input\">
\t\t\t\t<input type=\"text\" name=\"new_comment[name]\" class=\"name-field\" />
\t\t\t</div>
\t\t</div>
\t</div>
</div>
";
    }

    public function getTemplateName()
    {
        return "DeskPRO:Auth:twitter-login-tab.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  42 => 6,  138 => 41,  134 => 39,  109 => 34,  103 => 33,  97 => 29,  94 => 28,  84 => 25,  75 => 20,  69 => 17,  66 => 16,  54 => 11,  44 => 9,  230 => 84,  226 => 82,  203 => 76,  193 => 70,  188 => 67,  182 => 63,  178 => 61,  168 => 58,  163 => 57,  160 => 56,  155 => 55,  148 => 44,  145 => 49,  140 => 46,  136 => 44,  125 => 40,  120 => 39,  113 => 36,  101 => 33,  92 => 28,  89 => 27,  85 => 25,  73 => 19,  62 => 15,  59 => 14,  56 => 13,  41 => 8,  126 => 24,  119 => 36,  111 => 20,  106 => 34,  98 => 32,  93 => 17,  86 => 14,  70 => 9,  60 => 8,  28 => 8,  36 => 5,  114 => 21,  104 => 19,  91 => 16,  80 => 15,  63 => 15,  58 => 12,  40 => 8,  34 => 5,  45 => 7,  61 => 12,  55 => 6,  48 => 7,  39 => 16,  35 => 4,  31 => 6,  26 => 4,  21 => 1,  46 => 6,  29 => 3,  57 => 9,  50 => 10,  47 => 6,  38 => 5,  33 => 4,  49 => 10,  32 => 4,  246 => 90,  236 => 84,  232 => 82,  225 => 78,  221 => 77,  216 => 79,  214 => 73,  211 => 78,  208 => 77,  205 => 70,  199 => 66,  196 => 71,  190 => 61,  179 => 57,  175 => 55,  172 => 54,  169 => 53,  162 => 49,  158 => 48,  153 => 45,  151 => 44,  147 => 42,  144 => 41,  141 => 40,  135 => 39,  129 => 38,  124 => 37,  117 => 32,  112 => 29,  90 => 16,  87 => 26,  82 => 24,  72 => 22,  68 => 18,  65 => 14,  52 => 11,  43 => 13,  37 => 5,  30 => 3,  27 => 3,  25 => 3,  24 => 2,  22 => 2,  19 => 1,);
    }
}
