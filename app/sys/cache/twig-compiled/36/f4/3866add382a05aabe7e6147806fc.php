<?php

/* TwigBundle:Exception:trace.txt.twig */
class __TwigTemplate_36f43866add382a05aabe7e6147806fc extends \Application\DeskPRO\Twig\Template
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
        if (isset($context["trace"])) { $_trace_ = $context["trace"]; } else { $_trace_ = null; }
        if ($this->getAttribute($_trace_, "function")) {
            // line 2
            echo "                at ";
            if (isset($context["trace"])) { $_trace_ = $context["trace"]; } else { $_trace_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($_trace_, "class") . $this->getAttribute($_trace_, "type")) . $this->getAttribute($_trace_, "function")), "html", null, true);
            echo "(";
            if (isset($context["trace"])) { $_trace_ = $context["trace"]; } else { $_trace_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('code')->formatArgsAsText($this->getAttribute($_trace_, "args")), "html", null, true);
            echo ")
";
        } else {
            // line 4
            echo "                at n/a
";
        }
        // line 6
        if (isset($context["trace"])) { $_trace_ = $context["trace"]; } else { $_trace_ = null; }
        if (($this->getAttribute($_trace_, "file", array(), "any", true, true) && $this->getAttribute($_trace_, "line", array(), "any", true, true))) {
            // line 7
            echo "                    in ";
            if (isset($context["trace"])) { $_trace_ = $context["trace"]; } else { $_trace_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_trace_, "file"), "html", null, true);
            echo " line ";
            if (isset($context["trace"])) { $_trace_ = $context["trace"]; } else { $_trace_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_trace_, "line"), "html", null, true);
            echo "
";
        }
    }

    public function getTemplateName()
    {
        return "TwigBundle:Exception:trace.txt.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  36 => 6,  114 => 20,  104 => 19,  91 => 16,  80 => 15,  63 => 13,  58 => 12,  40 => 7,  34 => 5,  45 => 7,  61 => 12,  55 => 6,  48 => 13,  39 => 7,  35 => 4,  31 => 6,  26 => 4,  21 => 1,  46 => 9,  29 => 3,  57 => 9,  50 => 7,  47 => 6,  38 => 5,  33 => 4,  49 => 10,  32 => 4,  246 => 90,  236 => 84,  232 => 82,  225 => 78,  221 => 77,  216 => 74,  214 => 73,  211 => 72,  208 => 71,  205 => 70,  199 => 66,  196 => 65,  190 => 61,  179 => 57,  175 => 55,  172 => 54,  169 => 53,  162 => 49,  158 => 48,  153 => 45,  151 => 44,  147 => 42,  144 => 41,  141 => 40,  135 => 39,  129 => 38,  124 => 37,  117 => 32,  112 => 29,  90 => 26,  87 => 25,  82 => 24,  72 => 22,  68 => 21,  65 => 14,  52 => 11,  43 => 13,  37 => 4,  30 => 6,  27 => 3,  25 => 3,  24 => 4,  22 => 2,  19 => 1,);
    }
}
