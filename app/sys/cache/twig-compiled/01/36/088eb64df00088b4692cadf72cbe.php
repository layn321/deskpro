<?php

/* TwigBundle:Exception:exception.xml.twig */
class __TwigTemplate_0136088eb64df00088b4692cadf72cbe extends \Application\DeskPRO\Twig\Template
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
        echo "<?xml version=\"1.0\" encoding=\"";
        echo twig_escape_filter($this->env, $this->env->getCharset(), "html", null, true);
        echo "\" ?>

<error code=\"";
        // line 3
        if (isset($context["status_code"])) { $_status_code_ = $context["status_code"]; } else { $_status_code_ = null; }
        echo twig_escape_filter($this->env, $_status_code_, "html", null, true);
        echo "\" message=\"";
        if (isset($context["status_text"])) { $_status_text_ = $context["status_text"]; } else { $_status_text_ = null; }
        echo twig_escape_filter($this->env, $_status_text_, "html", null, true);
        echo "\">
";
        // line 4
        if (isset($context["exception"])) { $_exception_ = $context["exception"]; } else { $_exception_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_exception_, "toarray"));
        foreach ($context['_seq'] as $context["_key"] => $context["e"]) {
            // line 5
            echo "    <exception class=\"";
            if (isset($context["e"])) { $_e_ = $context["e"]; } else { $_e_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_e_, "class"), "html", null, true);
            echo "\" message=\"";
            if (isset($context["e"])) { $_e_ = $context["e"]; } else { $_e_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_e_, "message"), "html", null, true);
            echo "\">
";
            // line 6
            if (isset($context["e"])) { $_e_ = $context["e"]; } else { $_e_ = null; }
            $this->env->loadTemplate("TwigBundle:Exception:traces.xml.twig")->display(array("exception" => $_e_));
            // line 7
            echo "    </exception>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['e'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 9
        echo "</error>
";
    }

    public function getTemplateName()
    {
        return "TwigBundle:Exception:exception.xml.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  57 => 9,  50 => 7,  47 => 6,  38 => 5,  33 => 4,  49 => 5,  32 => 3,  246 => 90,  236 => 84,  232 => 82,  225 => 78,  221 => 77,  216 => 74,  214 => 73,  211 => 72,  208 => 71,  205 => 70,  199 => 66,  196 => 65,  190 => 61,  179 => 57,  175 => 55,  172 => 54,  169 => 53,  162 => 49,  158 => 48,  153 => 45,  151 => 44,  147 => 42,  144 => 41,  141 => 40,  135 => 39,  129 => 38,  124 => 37,  117 => 32,  112 => 29,  90 => 26,  87 => 25,  82 => 24,  72 => 22,  68 => 21,  65 => 20,  52 => 6,  43 => 13,  37 => 4,  30 => 6,  27 => 2,  25 => 3,  24 => 4,  22 => 2,  19 => 1,);
    }
}
