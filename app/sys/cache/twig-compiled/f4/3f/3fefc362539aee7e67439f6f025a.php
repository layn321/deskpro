<?php

/* TwigBundle:Exception:logs.html.twig */
class __TwigTemplate_f43f3fefc362539aee7e67439f6f025a extends \Application\DeskPRO\Twig\Template
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
        echo "<ol class=\"traces logs\">
    ";
        // line 2
        if (isset($context["logs"])) { $_logs_ = $context["logs"]; } else { $_logs_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_logs_);
        foreach ($context['_seq'] as $context["_key"] => $context["log"]) {
            // line 3
            echo "        <li";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if (twig_in_filter($this->getAttribute($_log_, "priorityName"), array(0 => "EMERG", 1 => "ERR", 2 => "CRIT", 3 => "ALERT", 4 => "ERROR", 5 => "CRITICAL"))) {
                echo " class=\"error\"";
            }
            echo ">
            ";
            // line 4
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_log_, "message"), "html", null, true);
            echo "
        </li>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['log'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 7
        echo "</ol>
";
    }

    public function getTemplateName()
    {
        return "TwigBundle:Exception:logs.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  45 => 7,  61 => 12,  55 => 6,  48 => 13,  39 => 8,  35 => 4,  31 => 6,  26 => 4,  21 => 1,  46 => 12,  29 => 3,  57 => 9,  50 => 7,  47 => 6,  38 => 5,  33 => 4,  49 => 8,  32 => 4,  246 => 90,  236 => 84,  232 => 82,  225 => 78,  221 => 77,  216 => 74,  214 => 73,  211 => 72,  208 => 71,  205 => 70,  199 => 66,  196 => 65,  190 => 61,  179 => 57,  175 => 55,  172 => 54,  169 => 53,  162 => 49,  158 => 48,  153 => 45,  151 => 44,  147 => 42,  144 => 41,  141 => 40,  135 => 39,  129 => 38,  124 => 37,  117 => 32,  112 => 29,  90 => 26,  87 => 25,  82 => 24,  72 => 22,  68 => 21,  65 => 20,  52 => 6,  43 => 13,  37 => 4,  30 => 6,  27 => 3,  25 => 3,  24 => 4,  22 => 2,  19 => 1,);
    }
}
