<?php

/* TwigBundle:Exception:error.html.twig */
class __TwigTemplate_26555b4d25ab58310a05c866edf84930 extends \Application\DeskPRO\Twig\Template
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
        $this->env->loadTemplate("UserBundle:Main:error-standard.html.twig")->display($context);
    }

    public function getTemplateName()
    {
        return "TwigBundle:Exception:error.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  22 => 2,  19 => 1,);
    }
}
