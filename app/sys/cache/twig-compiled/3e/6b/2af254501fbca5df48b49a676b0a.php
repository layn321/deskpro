<?php

/* ReportBundle:AgentFeedback:nav.html.twig */
class __TwigTemplate_3e6b2af254501fbca5df48b49a676b0a extends \Application\DeskPRO\Twig\Template
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
        echo "<div id=\"dp_admin_nav\" class=\"dp_admin_nav\">
    <nav>
        <ul>
            <li ";
        // line 4
        if (isset($context["this_page"])) { $_this_page_ = $context["this_page"]; } else { $_this_page_ = null; }
        if (($_this_page_ == "report_agent_feedback_feed")) {
            echo "class=\"on\"";
        }
        echo "><a href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_agent_feedback_feed", array("page" => 0)), "html", null, true);
        echo "\">Feed</a></li>
            <li ";
        // line 5
        if (isset($context["this_page"])) { $_this_page_ = $context["this_page"]; } else { $_this_page_ = null; }
        if (($_this_page_ == "report_agent_feedback_index")) {
            echo "class=\"on\"";
        }
        echo "><a href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_agent_feedback_summary", array("date" => $this->env->getExtension('deskpro_templating')->userDate($context, "now", "Y-m"))), "html", null, true);
        echo "\">Summary</a></li>
        </ul>
    </nav>
</div>";
    }

    public function getTemplateName()
    {
        return "ReportBundle:AgentFeedback:nav.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  24 => 4,  313 => 150,  310 => 149,  307 => 148,  304 => 147,  295 => 146,  291 => 145,  282 => 143,  279 => 142,  254 => 131,  249 => 130,  233 => 123,  226 => 120,  222 => 118,  218 => 116,  215 => 115,  203 => 113,  195 => 112,  187 => 111,  182 => 110,  159 => 97,  146 => 92,  128 => 83,  125 => 82,  63 => 22,  60 => 21,  44 => 9,  33 => 5,  399 => 79,  388 => 77,  380 => 76,  377 => 74,  373 => 72,  357 => 69,  346 => 68,  334 => 66,  316 => 63,  305 => 61,  298 => 60,  287 => 144,  251 => 51,  244 => 49,  241 => 124,  235 => 47,  231 => 45,  227 => 43,  219 => 40,  199 => 36,  192 => 35,  181 => 33,  174 => 32,  156 => 29,  145 => 27,  138 => 86,  126 => 24,  114 => 22,  91 => 18,  82 => 16,  68 => 14,  57 => 12,  48 => 10,  39 => 8,  36 => 5,  28 => 1,  23 => 2,  19 => 1,  436 => 155,  430 => 156,  428 => 155,  424 => 153,  421 => 152,  416 => 146,  410 => 159,  408 => 152,  401 => 151,  391 => 148,  387 => 147,  383 => 146,  379 => 144,  376 => 143,  371 => 141,  366 => 71,  361 => 65,  356 => 59,  349 => 162,  347 => 143,  344 => 142,  342 => 141,  336 => 137,  330 => 134,  318 => 129,  315 => 128,  308 => 126,  297 => 121,  290 => 119,  278 => 114,  272 => 111,  260 => 106,  257 => 105,  250 => 103,  239 => 98,  232 => 96,  221 => 91,  210 => 86,  201 => 85,  190 => 84,  184 => 82,  179 => 81,  167 => 75,  157 => 68,  154 => 96,  151 => 66,  149 => 65,  142 => 60,  140 => 59,  108 => 30,  103 => 20,  98 => 28,  94 => 27,  88 => 24,  83 => 22,  78 => 20,  74 => 19,  70 => 18,  66 => 17,  58 => 15,  54 => 14,  49 => 12,  45 => 11,  41 => 8,  37 => 9,  673 => 200,  669 => 198,  664 => 195,  649 => 193,  634 => 191,  631 => 190,  617 => 189,  603 => 188,  599 => 186,  587 => 184,  581 => 183,  569 => 181,  563 => 180,  551 => 178,  545 => 177,  533 => 175,  527 => 174,  515 => 172,  506 => 171,  503 => 170,  497 => 169,  494 => 168,  481 => 165,  473 => 164,  467 => 163,  463 => 162,  460 => 161,  457 => 160,  454 => 159,  450 => 158,  446 => 157,  442 => 156,  423 => 155,  419 => 154,  398 => 150,  392 => 152,  389 => 151,  386 => 150,  368 => 149,  363 => 148,  360 => 70,  343 => 146,  338 => 143,  328 => 141,  323 => 64,  317 => 136,  314 => 135,  280 => 57,  269 => 55,  262 => 132,  237 => 85,  230 => 122,  223 => 42,  216 => 38,  209 => 69,  202 => 65,  172 => 37,  169 => 36,  163 => 30,  155 => 31,  152 => 30,  147 => 29,  141 => 28,  136 => 25,  132 => 85,  112 => 21,  107 => 20,  104 => 19,  100 => 18,  90 => 15,  77 => 14,  72 => 13,  62 => 16,  55 => 18,  52 => 6,  47 => 3,  29 => 5,  27 => 1,);
    }
}
