<?php

/* ReportBundle:Billing:report.html.twig */
class __TwigTemplate_93e7567b8a44125e9970851d32156053 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("ReportBundle:Billing:layout.html.twig");

        $this->blocks = array(
            'inner_page' => array($this, 'block_inner_page'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "ReportBundle:Billing:layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_inner_page($context, array $blocks = array())
    {
        // line 3
        echo "\t<h1>
\t\t";
        // line 4
        if (isset($context["report"])) { $_report_ = $context["report"]; } else { $_report_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_report_, "title_final"), "html", null, true);
        echo "
\t</h1>
\t";
        // line 6
        if (isset($context["report"])) { $_report_ = $context["report"]; } else { $_report_ = null; }
        if ($this->getAttribute($_report_, "description")) {
            echo "<p>";
            if (isset($context["report"])) { $_report_ = $context["report"]; } else { $_report_ = null; }
            echo nl2br(twig_escape_filter($this->env, $this->getAttribute($_report_, "description"), "html", null, true));
            echo "</p>";
        }
        // line 7
        echo "
\t<form action=\"";
        // line 8
        if (isset($context["report"])) { $_report_ = $context["report"]; } else { $_report_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_billing_report", array("report_id" => $this->getAttribute($_report_, "id"))), "html", null, true);
        echo "\" class=\"dp-form\" method=\"post\">

\t\t<div class=\"report-actions\">
\t\t\t<button class=\"btn\" name=\"output\" value=\"csv\">CSV</button>
\t\t\t<button class=\"btn\" name=\"run\" value=\"1\" onclick=\"window.print(); return false;\">Print</button>
\t\t</div>

\t\t";
        // line 15
        $this->env->loadTemplate("ReportBundle:ReportBuilder:results.html.twig")->display($context);
        // line 16
        echo "
\t\t";
        // line 17
        echo $this->env->getExtension('deskpro_templating')->formToken();
        echo "

\t\t";
        // line 19
        if (isset($context["params"])) { $_params_ = $context["params"]; } else { $_params_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_params_);
        foreach ($context['_seq'] as $context["key"] => $context["val"]) {
            // line 20
            echo "\t\t\t<input type=\"hidden\" name=\"params[";
            if (isset($context["key"])) { $_key_ = $context["key"]; } else { $_key_ = null; }
            echo twig_escape_filter($this->env, $_key_, "html", null, true);
            echo "]\" value=\"";
            if (isset($context["val"])) { $_val_ = $context["val"]; } else { $_val_ = null; }
            echo twig_escape_filter($this->env, $_val_, "html", null, true);
            echo "\" />
\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['key'], $context['val'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 22
        echo "\t</form>
";
    }

    public function getTemplateName()
    {
        return "ReportBundle:Billing:report.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  64 => 16,  51 => 8,  31 => 3,  153 => 21,  150 => 20,  133 => 46,  102 => 38,  87 => 35,  75 => 25,  67 => 17,  50 => 8,  38 => 4,  35 => 3,  30 => 1,  1374 => 443,  1370 => 441,  1365 => 438,  1350 => 436,  1345 => 433,  1330 => 431,  1315 => 429,  1312 => 428,  1305 => 427,  1302 => 426,  1298 => 424,  1295 => 423,  1283 => 422,  1276 => 421,  1268 => 420,  1265 => 419,  1247 => 418,  1236 => 417,  1218 => 416,  1211 => 414,  1208 => 413,  1201 => 411,  1198 => 410,  1180 => 409,  1177 => 408,  1168 => 404,  1164 => 403,  1158 => 399,  1151 => 397,  1129 => 393,  1112 => 392,  1101 => 390,  1096 => 389,  1092 => 387,  1087 => 386,  1084 => 385,  1066 => 382,  1058 => 381,  1041 => 380,  1035 => 376,  1030 => 373,  1015 => 371,  1010 => 368,  995 => 366,  981 => 365,  967 => 364,  953 => 363,  938 => 361,  935 => 360,  932 => 359,  928 => 357,  925 => 356,  913 => 355,  906 => 354,  898 => 353,  895 => 352,  877 => 351,  858 => 350,  839 => 349,  821 => 348,  810 => 347,  792 => 346,  782 => 344,  779 => 343,  772 => 341,  769 => 340,  751 => 339,  748 => 338,  742 => 337,  736 => 336,  726 => 331,  720 => 330,  714 => 329,  709 => 328,  703 => 324,  696 => 322,  682 => 321,  676 => 320,  654 => 316,  635 => 315,  629 => 314,  623 => 313,  620 => 312,  602 => 311,  591 => 309,  586 => 308,  582 => 306,  577 => 305,  574 => 304,  560 => 303,  554 => 302,  544 => 299,  540 => 297,  536 => 295,  532 => 294,  528 => 293,  524 => 292,  520 => 291,  516 => 290,  512 => 289,  508 => 288,  504 => 287,  500 => 286,  496 => 285,  490 => 284,  475 => 281,  469 => 280,  432 => 272,  329 => 209,  284 => 187,  267 => 175,  259 => 171,  238 => 155,  204 => 129,  175 => 104,  101 => 34,  95 => 32,  92 => 36,  81 => 24,  71 => 20,  46 => 9,  40 => 6,  32 => 4,  477 => 191,  434 => 181,  405 => 172,  331 => 160,  322 => 157,  311 => 154,  302 => 151,  266 => 145,  263 => 144,  245 => 143,  242 => 156,  196 => 138,  191 => 135,  188 => 134,  183 => 131,  99 => 49,  96 => 48,  56 => 15,  34 => 4,  24 => 4,  313 => 150,  310 => 149,  307 => 207,  304 => 147,  295 => 146,  291 => 145,  282 => 143,  279 => 185,  254 => 131,  249 => 130,  233 => 123,  226 => 120,  222 => 118,  218 => 116,  215 => 115,  203 => 113,  195 => 112,  187 => 111,  182 => 110,  159 => 97,  146 => 92,  128 => 83,  125 => 82,  63 => 22,  60 => 21,  44 => 6,  33 => 5,  399 => 79,  388 => 77,  380 => 76,  377 => 74,  373 => 72,  357 => 69,  346 => 68,  334 => 66,  316 => 63,  305 => 61,  298 => 60,  287 => 144,  251 => 51,  244 => 49,  241 => 124,  235 => 47,  231 => 45,  227 => 43,  219 => 140,  199 => 36,  192 => 35,  181 => 33,  174 => 32,  156 => 29,  145 => 27,  138 => 19,  126 => 57,  114 => 47,  91 => 18,  82 => 16,  68 => 14,  57 => 12,  48 => 7,  39 => 8,  36 => 5,  28 => 2,  23 => 2,  19 => 1,  436 => 273,  430 => 156,  428 => 155,  424 => 178,  421 => 152,  416 => 175,  410 => 159,  408 => 152,  401 => 151,  391 => 148,  387 => 171,  383 => 169,  379 => 144,  376 => 143,  371 => 221,  366 => 71,  361 => 65,  356 => 59,  349 => 219,  347 => 163,  344 => 142,  342 => 141,  336 => 137,  330 => 134,  318 => 208,  315 => 128,  308 => 126,  297 => 121,  290 => 148,  278 => 114,  272 => 147,  260 => 106,  257 => 105,  250 => 103,  239 => 98,  232 => 153,  221 => 91,  210 => 86,  201 => 139,  190 => 84,  184 => 82,  179 => 81,  167 => 75,  157 => 68,  154 => 96,  151 => 66,  149 => 65,  142 => 60,  140 => 59,  108 => 30,  103 => 20,  98 => 33,  94 => 27,  88 => 24,  83 => 22,  78 => 20,  74 => 19,  70 => 18,  66 => 17,  58 => 15,  54 => 14,  49 => 12,  45 => 11,  41 => 8,  37 => 7,  673 => 200,  669 => 198,  664 => 195,  649 => 193,  634 => 191,  631 => 190,  617 => 189,  603 => 188,  599 => 186,  587 => 184,  581 => 183,  569 => 181,  563 => 180,  551 => 178,  545 => 177,  533 => 175,  527 => 174,  515 => 172,  506 => 171,  503 => 170,  497 => 169,  494 => 168,  481 => 282,  473 => 164,  467 => 163,  463 => 279,  460 => 278,  457 => 187,  454 => 159,  450 => 158,  446 => 157,  442 => 277,  423 => 155,  419 => 154,  398 => 150,  392 => 152,  389 => 151,  386 => 150,  368 => 167,  363 => 148,  360 => 220,  343 => 146,  338 => 143,  328 => 141,  323 => 64,  317 => 136,  314 => 135,  280 => 57,  269 => 55,  262 => 132,  237 => 85,  230 => 122,  223 => 42,  216 => 38,  209 => 69,  202 => 65,  172 => 37,  169 => 36,  163 => 30,  155 => 31,  152 => 30,  147 => 29,  141 => 28,  136 => 25,  132 => 85,  112 => 46,  107 => 20,  104 => 35,  100 => 18,  90 => 22,  77 => 20,  72 => 19,  62 => 15,  55 => 18,  52 => 6,  47 => 7,  29 => 1,  27 => 1,);
    }
}
