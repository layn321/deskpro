<?php

/* ReportBundle:ReportBuilder:results.html.twig */
class __TwigTemplate_957d96dbf341530fdd142f16aae8c5c6 extends \Application\DeskPRO\Twig\Template
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
        if (isset($context["error"])) { $_error_ = $context["error"]; } else { $_error_ = null; }
        if ($_error_) {
            // line 2
            echo "<div class=\"query-error\">";
            if (isset($context["error"])) { $_error_ = $context["error"]; } else { $_error_ = null; }
            echo twig_escape_filter($this->env, $_error_, "html", null, true);
            echo "</div>
";
        } else {
            // line 4
            echo "<div class=\"report-builder-results\">
\t";
            // line 5
            if (isset($context["includeQuery"])) { $_includeQuery_ = $context["includeQuery"]; } else { $_includeQuery_ = null; }
            if ($_includeQuery_) {
                echo "<div class=\"query\">";
                if (isset($context["query"])) { $_query_ = $context["query"]; } else { $_query_ = null; }
                echo twig_escape_filter($this->env, $_query_, "html", null, true);
                echo "</div>";
            }
            // line 6
            echo "\t<div>Generated: ";
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, "now", "fulltime"), "html", null, true);
            echo " (";
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, "now", "P"), "html", null, true);
            echo " GMT)</div>
\t";
            // line 7
            if (isset($context["results"])) { $_results_ = $context["results"]; } else { $_results_ = null; }
            if ($_results_) {
                // line 8
                echo "\t\t";
                if (isset($context["results"])) { $_results_ = $context["results"]; } else { $_results_ = null; }
                echo $_results_;
                echo "
\t";
            } else {
                // line 10
                echo "\t\t<div class=\"report-no-results\">No results for this report.</div>
\t";
            }
            // line 12
            echo "</div>
";
        }
    }

    public function getTemplateName()
    {
        return "ReportBundle:ReportBuilder:results.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  180 => 55,  148 => 39,  206 => 61,  189 => 49,  177 => 54,  117 => 33,  97 => 33,  268 => 29,  252 => 21,  234 => 87,  213 => 60,  205 => 81,  178 => 76,  164 => 69,  135 => 60,  121 => 30,  86 => 27,  119 => 36,  93 => 27,  144 => 37,  129 => 44,  471 => 106,  462 => 105,  439 => 98,  431 => 95,  426 => 92,  420 => 91,  417 => 90,  382 => 86,  378 => 85,  372 => 84,  350 => 78,  335 => 73,  327 => 71,  303 => 64,  300 => 63,  296 => 62,  292 => 61,  281 => 58,  275 => 32,  264 => 53,  253 => 52,  220 => 45,  208 => 43,  194 => 41,  171 => 51,  166 => 36,  118 => 29,  105 => 29,  79 => 22,  22 => 2,  130 => 59,  85 => 23,  162 => 48,  143 => 54,  131 => 48,  127 => 26,  123 => 51,  120 => 35,  109 => 40,  43 => 6,  217 => 63,  211 => 59,  168 => 42,  165 => 64,  160 => 47,  134 => 38,  115 => 34,  110 => 30,  89 => 27,  73 => 18,  42 => 7,  447 => 156,  437 => 148,  429 => 142,  418 => 141,  407 => 140,  370 => 137,  364 => 134,  354 => 127,  341 => 119,  333 => 114,  306 => 96,  289 => 60,  276 => 81,  271 => 78,  265 => 75,  258 => 72,  255 => 22,  240 => 52,  214 => 175,  207 => 173,  193 => 59,  186 => 54,  173 => 72,  161 => 64,  158 => 62,  116 => 34,  111 => 32,  106 => 26,  84 => 24,  80 => 36,  122 => 33,  113 => 41,  76 => 23,  59 => 14,  21 => 2,  69 => 22,  65 => 16,  61 => 12,  53 => 17,  64 => 15,  51 => 12,  31 => 3,  153 => 42,  150 => 44,  133 => 32,  102 => 25,  87 => 29,  75 => 23,  67 => 21,  50 => 8,  38 => 5,  35 => 3,  30 => 1,  1374 => 443,  1370 => 441,  1365 => 438,  1350 => 436,  1345 => 433,  1330 => 431,  1315 => 429,  1312 => 428,  1305 => 427,  1302 => 426,  1298 => 424,  1295 => 423,  1283 => 422,  1276 => 421,  1268 => 420,  1265 => 419,  1247 => 418,  1236 => 417,  1218 => 416,  1211 => 414,  1208 => 413,  1201 => 411,  1198 => 410,  1180 => 409,  1177 => 408,  1168 => 404,  1164 => 403,  1158 => 399,  1151 => 397,  1129 => 393,  1112 => 392,  1101 => 390,  1096 => 389,  1092 => 387,  1087 => 386,  1084 => 385,  1066 => 382,  1058 => 381,  1041 => 380,  1035 => 376,  1030 => 373,  1015 => 371,  1010 => 368,  995 => 366,  981 => 365,  967 => 364,  953 => 363,  938 => 361,  935 => 360,  932 => 359,  928 => 357,  925 => 356,  913 => 355,  906 => 354,  898 => 353,  895 => 352,  877 => 351,  858 => 350,  839 => 349,  821 => 348,  810 => 347,  792 => 346,  782 => 344,  779 => 343,  772 => 341,  769 => 340,  751 => 339,  748 => 338,  742 => 337,  736 => 336,  726 => 331,  720 => 330,  714 => 329,  709 => 328,  703 => 324,  696 => 322,  682 => 321,  676 => 320,  654 => 316,  635 => 315,  629 => 314,  623 => 313,  620 => 312,  602 => 311,  591 => 309,  586 => 308,  582 => 306,  577 => 305,  574 => 304,  560 => 303,  554 => 302,  544 => 299,  540 => 297,  536 => 295,  532 => 294,  528 => 293,  524 => 292,  520 => 291,  516 => 290,  512 => 289,  508 => 288,  504 => 287,  500 => 286,  496 => 285,  490 => 284,  475 => 281,  469 => 280,  432 => 272,  329 => 209,  284 => 59,  267 => 54,  259 => 26,  238 => 155,  204 => 129,  175 => 53,  101 => 29,  95 => 27,  92 => 31,  81 => 22,  71 => 23,  46 => 9,  40 => 6,  32 => 5,  477 => 191,  434 => 181,  405 => 172,  331 => 160,  322 => 106,  311 => 154,  302 => 95,  266 => 145,  263 => 28,  245 => 58,  242 => 72,  196 => 138,  191 => 71,  188 => 58,  183 => 56,  99 => 49,  96 => 47,  56 => 14,  34 => 4,  24 => 3,  313 => 150,  310 => 66,  307 => 207,  304 => 147,  295 => 146,  291 => 145,  282 => 143,  279 => 185,  254 => 131,  249 => 130,  233 => 123,  226 => 120,  222 => 118,  218 => 116,  215 => 88,  203 => 56,  195 => 56,  187 => 111,  182 => 110,  159 => 65,  146 => 92,  128 => 52,  125 => 34,  63 => 19,  60 => 11,  44 => 12,  33 => 7,  399 => 79,  388 => 77,  380 => 138,  377 => 74,  373 => 72,  357 => 80,  346 => 76,  334 => 66,  316 => 67,  305 => 61,  298 => 60,  287 => 144,  251 => 51,  244 => 49,  241 => 124,  235 => 70,  231 => 45,  227 => 98,  219 => 140,  199 => 36,  192 => 35,  181 => 61,  174 => 32,  156 => 43,  145 => 62,  138 => 53,  126 => 31,  114 => 39,  91 => 24,  82 => 24,  68 => 17,  57 => 10,  48 => 11,  39 => 11,  36 => 4,  28 => 2,  23 => 1,  19 => 1,  436 => 273,  430 => 156,  428 => 155,  424 => 178,  421 => 152,  416 => 175,  410 => 159,  408 => 152,  401 => 151,  391 => 139,  387 => 171,  383 => 169,  379 => 144,  376 => 143,  371 => 221,  366 => 83,  361 => 82,  356 => 59,  349 => 219,  347 => 163,  344 => 142,  342 => 75,  336 => 137,  330 => 72,  318 => 208,  315 => 128,  308 => 126,  297 => 94,  290 => 148,  278 => 114,  272 => 55,  260 => 106,  257 => 105,  250 => 59,  239 => 20,  232 => 153,  221 => 177,  210 => 59,  201 => 79,  190 => 39,  184 => 78,  179 => 57,  167 => 49,  157 => 38,  154 => 33,  151 => 66,  149 => 37,  142 => 40,  140 => 43,  108 => 51,  103 => 50,  98 => 35,  94 => 24,  88 => 26,  83 => 26,  78 => 22,  74 => 23,  70 => 22,  66 => 16,  58 => 18,  54 => 13,  49 => 11,  45 => 10,  41 => 10,  37 => 5,  673 => 200,  669 => 198,  664 => 195,  649 => 193,  634 => 191,  631 => 190,  617 => 189,  603 => 188,  599 => 186,  587 => 184,  581 => 183,  569 => 181,  563 => 180,  551 => 178,  545 => 177,  533 => 175,  527 => 174,  515 => 172,  506 => 171,  503 => 170,  497 => 169,  494 => 168,  481 => 282,  473 => 164,  467 => 163,  463 => 279,  460 => 278,  457 => 187,  454 => 159,  450 => 102,  446 => 157,  442 => 99,  423 => 155,  419 => 154,  398 => 88,  392 => 152,  389 => 151,  386 => 150,  368 => 167,  363 => 148,  360 => 220,  343 => 146,  338 => 74,  328 => 141,  323 => 69,  317 => 103,  314 => 135,  280 => 57,  269 => 77,  262 => 74,  237 => 85,  230 => 122,  223 => 64,  216 => 38,  209 => 69,  202 => 65,  172 => 72,  169 => 55,  163 => 48,  155 => 66,  152 => 65,  147 => 55,  141 => 36,  136 => 33,  132 => 45,  112 => 27,  107 => 39,  104 => 38,  100 => 28,  90 => 26,  77 => 35,  72 => 19,  62 => 15,  55 => 17,  52 => 8,  47 => 7,  29 => 4,  27 => 1,);
    }
}
