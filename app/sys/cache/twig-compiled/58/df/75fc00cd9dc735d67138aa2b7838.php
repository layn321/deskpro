<?php

/* ReportBundle:ReportBuilder:query.html.twig */
class __TwigTemplate_58df75fc00cd9dc735d67138aa2b7838 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("ReportBundle:ReportBuilder:layout.html.twig");

        $this->blocks = array(
            'inner_page' => array($this, 'block_inner_page'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "ReportBundle:ReportBuilder:layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_inner_page($context, array $blocks = array())
    {
        // line 3
        echo "\t<form action=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_builder_query"), "html", null, true);
        echo "\" method=\"post\" class=\"dp-form\">
\t\t";
        // line 4
        $this->env->loadTemplate("ReportBundle:ReportBuilder:query-editor.html.twig")->display($context);
        // line 5
        echo "
\t\t<div class=\"report-actions\">
\t\t\t<button class=\"btn primary\">Run Report</button>
\t\t</div>

\t\t";
        // line 10
        if (isset($context["statement"])) { $_statement_ = $context["statement"]; } else { $_statement_ = null; }
        if (isset($context["errors"])) { $_errors_ = $context["errors"]; } else { $_errors_ = null; }
        if (($_statement_ && (!$_errors_))) {
            // line 11
            echo "\t\t\t<div class=\"report-actions\">
\t\t\t\t<button class=\"btn\" name=\"output\" value=\"csv\">CSV</button>
\t\t\t\t<button class=\"btn\" name=\"output\" value=\"pdf\">PDF</button>
\t\t\t\t<button class=\"btn\" name=\"run\" value=\"1\" onclick=\"window.print(); return false;\">Print</button>
\t\t\t\t<button class=\"btn\" name=\"save\" value=\"1\">Save as Report</button>
\t\t\t</div>
\t\t";
        }
        // line 18
        echo "
\t\t";
        // line 19
        if (isset($context["query"])) { $_query_ = $context["query"]; } else { $_query_ = null; }
        if ($_query_) {
            // line 20
            echo "\t\t\t";
            $this->env->loadTemplate("ReportBundle:ReportBuilder:results.html.twig")->display(array_merge($context, array("includeQuery" => true)));
            // line 21
            echo "\t\t";
        }
        // line 22
        echo "\t</form>
";
    }

    public function getTemplateName()
    {
        return "ReportBundle:ReportBuilder:query.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  206 => 57,  189 => 49,  177 => 44,  117 => 33,  97 => 33,  268 => 29,  252 => 21,  234 => 87,  213 => 60,  205 => 81,  178 => 76,  164 => 69,  135 => 60,  121 => 42,  86 => 27,  119 => 36,  93 => 27,  144 => 40,  129 => 44,  471 => 106,  462 => 105,  439 => 98,  431 => 95,  426 => 92,  420 => 91,  417 => 90,  382 => 86,  378 => 85,  372 => 84,  350 => 78,  335 => 73,  327 => 71,  303 => 64,  300 => 63,  296 => 62,  292 => 61,  281 => 58,  275 => 32,  264 => 53,  253 => 52,  220 => 45,  208 => 43,  194 => 41,  171 => 37,  166 => 36,  118 => 53,  105 => 29,  79 => 22,  22 => 2,  130 => 59,  85 => 23,  162 => 48,  143 => 54,  131 => 48,  127 => 26,  123 => 51,  120 => 35,  109 => 40,  43 => 8,  217 => 63,  211 => 59,  168 => 42,  165 => 64,  160 => 47,  134 => 38,  115 => 34,  110 => 30,  89 => 27,  73 => 21,  42 => 7,  447 => 156,  437 => 148,  429 => 142,  418 => 141,  407 => 140,  370 => 137,  364 => 134,  354 => 127,  341 => 119,  333 => 114,  306 => 96,  289 => 60,  276 => 81,  271 => 78,  265 => 75,  258 => 72,  255 => 22,  240 => 52,  214 => 175,  207 => 173,  193 => 76,  186 => 54,  173 => 72,  161 => 64,  158 => 62,  116 => 34,  111 => 32,  106 => 37,  84 => 24,  80 => 36,  122 => 33,  113 => 41,  76 => 23,  59 => 14,  21 => 2,  69 => 22,  65 => 16,  61 => 19,  53 => 17,  64 => 20,  51 => 12,  31 => 3,  153 => 61,  150 => 44,  133 => 35,  102 => 32,  87 => 29,  75 => 23,  67 => 21,  50 => 9,  38 => 5,  35 => 3,  30 => 1,  1374 => 443,  1370 => 441,  1365 => 438,  1350 => 436,  1345 => 433,  1330 => 431,  1315 => 429,  1312 => 428,  1305 => 427,  1302 => 426,  1298 => 424,  1295 => 423,  1283 => 422,  1276 => 421,  1268 => 420,  1265 => 419,  1247 => 418,  1236 => 417,  1218 => 416,  1211 => 414,  1208 => 413,  1201 => 411,  1198 => 410,  1180 => 409,  1177 => 408,  1168 => 404,  1164 => 403,  1158 => 399,  1151 => 397,  1129 => 393,  1112 => 392,  1101 => 390,  1096 => 389,  1092 => 387,  1087 => 386,  1084 => 385,  1066 => 382,  1058 => 381,  1041 => 380,  1035 => 376,  1030 => 373,  1015 => 371,  1010 => 368,  995 => 366,  981 => 365,  967 => 364,  953 => 363,  938 => 361,  935 => 360,  932 => 359,  928 => 357,  925 => 356,  913 => 355,  906 => 354,  898 => 353,  895 => 352,  877 => 351,  858 => 350,  839 => 349,  821 => 348,  810 => 347,  792 => 346,  782 => 344,  779 => 343,  772 => 341,  769 => 340,  751 => 339,  748 => 338,  742 => 337,  736 => 336,  726 => 331,  720 => 330,  714 => 329,  709 => 328,  703 => 324,  696 => 322,  682 => 321,  676 => 320,  654 => 316,  635 => 315,  629 => 314,  623 => 313,  620 => 312,  602 => 311,  591 => 309,  586 => 308,  582 => 306,  577 => 305,  574 => 304,  560 => 303,  554 => 302,  544 => 299,  540 => 297,  536 => 295,  532 => 294,  528 => 293,  524 => 292,  520 => 291,  516 => 290,  512 => 289,  508 => 288,  504 => 287,  500 => 286,  496 => 285,  490 => 284,  475 => 281,  469 => 280,  432 => 272,  329 => 209,  284 => 59,  267 => 54,  259 => 26,  238 => 155,  204 => 129,  175 => 53,  101 => 29,  95 => 27,  92 => 31,  81 => 25,  71 => 23,  46 => 9,  40 => 6,  32 => 7,  477 => 191,  434 => 181,  405 => 172,  331 => 160,  322 => 106,  311 => 154,  302 => 95,  266 => 145,  263 => 28,  245 => 58,  242 => 72,  196 => 138,  191 => 71,  188 => 63,  183 => 74,  99 => 49,  96 => 47,  56 => 14,  34 => 9,  24 => 3,  313 => 150,  310 => 66,  307 => 207,  304 => 147,  295 => 146,  291 => 145,  282 => 143,  279 => 185,  254 => 131,  249 => 130,  233 => 123,  226 => 120,  222 => 118,  218 => 116,  215 => 88,  203 => 56,  195 => 56,  187 => 111,  182 => 110,  159 => 65,  146 => 92,  128 => 52,  125 => 34,  63 => 19,  60 => 11,  44 => 12,  33 => 7,  399 => 79,  388 => 77,  380 => 138,  377 => 74,  373 => 72,  357 => 80,  346 => 76,  334 => 66,  316 => 67,  305 => 61,  298 => 60,  287 => 144,  251 => 51,  244 => 49,  241 => 124,  235 => 70,  231 => 45,  227 => 98,  219 => 140,  199 => 36,  192 => 35,  181 => 61,  174 => 32,  156 => 45,  145 => 62,  138 => 53,  126 => 57,  114 => 39,  91 => 24,  82 => 24,  68 => 20,  57 => 16,  48 => 11,  39 => 11,  36 => 4,  28 => 2,  23 => 1,  19 => 1,  436 => 273,  430 => 156,  428 => 155,  424 => 178,  421 => 152,  416 => 175,  410 => 159,  408 => 152,  401 => 151,  391 => 139,  387 => 171,  383 => 169,  379 => 144,  376 => 143,  371 => 221,  366 => 83,  361 => 82,  356 => 59,  349 => 219,  347 => 163,  344 => 142,  342 => 75,  336 => 137,  330 => 72,  318 => 208,  315 => 128,  308 => 126,  297 => 94,  290 => 148,  278 => 114,  272 => 55,  260 => 106,  257 => 105,  250 => 59,  239 => 20,  232 => 153,  221 => 177,  210 => 59,  201 => 79,  190 => 39,  184 => 78,  179 => 57,  167 => 69,  157 => 38,  154 => 33,  151 => 66,  149 => 37,  142 => 40,  140 => 43,  108 => 51,  103 => 50,  98 => 35,  94 => 32,  88 => 26,  83 => 26,  78 => 22,  74 => 23,  70 => 22,  66 => 16,  58 => 18,  54 => 13,  49 => 11,  45 => 10,  41 => 10,  37 => 7,  673 => 200,  669 => 198,  664 => 195,  649 => 193,  634 => 191,  631 => 190,  617 => 189,  603 => 188,  599 => 186,  587 => 184,  581 => 183,  569 => 181,  563 => 180,  551 => 178,  545 => 177,  533 => 175,  527 => 174,  515 => 172,  506 => 171,  503 => 170,  497 => 169,  494 => 168,  481 => 282,  473 => 164,  467 => 163,  463 => 279,  460 => 278,  457 => 187,  454 => 159,  450 => 102,  446 => 157,  442 => 99,  423 => 155,  419 => 154,  398 => 88,  392 => 152,  389 => 151,  386 => 150,  368 => 167,  363 => 148,  360 => 220,  343 => 146,  338 => 74,  328 => 141,  323 => 69,  317 => 103,  314 => 135,  280 => 57,  269 => 77,  262 => 74,  237 => 85,  230 => 122,  223 => 64,  216 => 38,  209 => 69,  202 => 65,  172 => 72,  169 => 55,  163 => 54,  155 => 66,  152 => 65,  147 => 55,  141 => 36,  136 => 38,  132 => 45,  112 => 33,  107 => 39,  104 => 38,  100 => 28,  90 => 26,  77 => 35,  72 => 19,  62 => 15,  55 => 17,  52 => 14,  47 => 7,  29 => 4,  27 => 1,);
    }
}
