<?php

/* ReportBundle:Overview:tickets-resolved.html.twig */
class __TwigTemplate_50818bdf4dcd292230b5ff31c7eac497 extends \Application\DeskPRO\Twig\Template
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
        $context["macros"] = $this->env->loadTemplate("ReportBundle:Overview:macros.html.twig");
        // line 2
        echo "<section class=\"report-wrapper ";
        if (isset($context["initial_display"])) { $_initial_display_ = $context["initial_display"]; } else { $_initial_display_ = null; }
        if ($_initial_display_) {
            echo "initial-display";
        }
        echo "\">
\t<header>
\t\t<h2>
\t\t\t<span class=\"label-sum\">";
        // line 5
        if (isset($context["data"])) { $_data_ = $context["data"]; } else { $_data_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_data_, "sum"), "html", null, true);
        echo "</span> Tickets were 'Resolved'
\t\t\t<span class=\"drop-option\">
\t\t\t\t<select class=\"grouping-field\" name=\"date_choice\">
\t\t\t\t\t";
        // line 8
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable(array("today" => "Today", "this_week" => "This Week", "this_month" => "This Month", "this_year" => "This Year"));
        foreach ($context['_seq'] as $context["field"] => $context["title"]) {
            // line 14
            echo "\t\t\t\t\t\t<option ";
            if (isset($context["data"])) { $_data_ = $context["data"]; } else { $_data_ = null; }
            if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
            if (($this->getAttribute($_data_, "date_choice") == $_field_)) {
                echo "selected=\"selected\"";
            }
            echo " value=\"";
            if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
            echo twig_escape_filter($this->env, $_field_, "html", null, true);
            echo "\">";
            if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
            echo twig_escape_filter($this->env, $_title_, "html", null, true);
            echo "</option>
\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['field'], $context['title'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 16
        echo "\t\t\t\t</select>
\t\t\t\t<i></i>
\t\t\t</span>
\t\t</h2>
\t\t<h3>Showing Grouped by ";
        // line 20
        if (isset($context["macros"])) { $_macros_ = $context["macros"]; } else { $_macros_ = null; }
        if (isset($context["data"])) { $_data_ = $context["data"]; } else { $_data_ = null; }
        echo $_macros_->getgrouping_field_select($this->getAttribute($_data_, "grouping_field"));
        echo "</h3>
\t</header>
\t<article>
\t\t<div class=\"loading-overlay\" ";
        // line 23
        if (isset($context["initial_display"])) { $_initial_display_ = $context["initial_display"]; } else { $_initial_display_ = null; }
        if ((!$_initial_display_)) {
            echo "style=\"display: none\"";
        }
        echo "><strong>Loading</strong></div>
\t\t\t";
        // line 24
        if (isset($context["data"])) { $_data_ = $context["data"]; } else { $_data_ = null; }
        if ($this->getAttribute($_data_, "values")) {
            // line 25
            echo "\t\t\t\t<table class=\"stat-table\" width=\"100%\">
\t\t\t\t";
            // line 26
            if (isset($context["data"])) { $_data_ = $context["data"]; } else { $_data_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_data_, "titles"));
            foreach ($context['_seq'] as $context["id"] => $context["title"]) {
                // line 27
                echo "\t\t\t\t\t";
                if (isset($context["data"])) { $_data_ = $context["data"]; } else { $_data_ = null; }
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                if ($this->getAttribute($this->getAttribute($_data_, "values"), $_id_, array(), "array")) {
                    // line 28
                    echo "\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t<td class=\"title\"><span>";
                    // line 29
                    if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
                    echo twig_escape_filter($this->env, $_title_, "html", null, true);
                    echo "</span></td>
\t\t\t\t\t\t\t<td width=\"100%\">
\t\t\t\t\t\t\t\t<table width=\"100%\" class=\"stat-value\"><tr>
\t\t\t\t\t\t\t\t\t";
                    // line 32
                    if (isset($context["data"])) { $_data_ = $context["data"]; } else { $_data_ = null; }
                    if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                    $context["perc"] = (($this->getAttribute($this->getAttribute($_data_, "values"), $_id_, array(), "array") / (($this->getAttribute($_data_, "max", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_data_, "max"), 1)) : (1))) * 100);
                    // line 33
                    echo "\t\t\t\t\t\t\t\t\t";
                    if (isset($context["perc"])) { $_perc_ = $context["perc"]; } else { $_perc_ = null; }
                    if (($_perc_ < 1)) {
                        // line 34
                        echo "\t\t\t\t\t\t\t\t\t\t<td class=\"value\" width=\"1%\"><div>&nbsp;</div></td>
\t\t\t\t\t\t\t\t\t\t<td width=\"";
                        // line 35
                        echo twig_escape_filter($this->env, (100 - 1), "html", null, true);
                        echo "%\">";
                        if (isset($context["data"])) { $_data_ = $context["data"]; } else { $_data_ = null; }
                        if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_data_, "values"), $_id_, array(), "array"), "html", null, true);
                        echo "</td>
\t\t\t\t\t\t\t\t\t";
                    } else {
                        // line 37
                        echo "\t\t\t\t\t\t\t\t\t\t<td class=\"value\" width=\"";
                        if (isset($context["perc"])) { $_perc_ = $context["perc"]; } else { $_perc_ = null; }
                        echo twig_escape_filter($this->env, $_perc_, "html", null, true);
                        echo "%\"><div>&nbsp;</div></td>
\t\t\t\t\t\t\t\t\t\t<td width=\"";
                        // line 38
                        if (isset($context["perc"])) { $_perc_ = $context["perc"]; } else { $_perc_ = null; }
                        echo twig_escape_filter($this->env, (100 - $_perc_), "html", null, true);
                        echo "%\">";
                        if (isset($context["data"])) { $_data_ = $context["data"]; } else { $_data_ = null; }
                        if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_data_, "values"), $_id_, array(), "array"), "html", null, true);
                        echo "</td>
\t\t\t\t\t\t\t\t\t";
                    }
                    // line 40
                    echo "\t\t\t\t\t\t\t\t</tr></table>
\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t</tr>
\t\t\t\t\t";
                }
                // line 44
                echo "\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['id'], $context['title'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 45
            echo "\t\t\t\t<tr class=\"total-row\">
\t\t\t\t\t<td class=\"title\">Total:</td>
\t\t\t\t\t<td class=\"val\">";
            // line 47
            if (isset($context["data"])) { $_data_ = $context["data"]; } else { $_data_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_data_, "sum"), "html", null, true);
            echo "</td>
\t\t\t\t</tr>
\t\t\t\t</table>
\t\t\t";
        } else {
            // line 51
            echo "\t\t\t\t";
            if (isset($context["macros"])) { $_macros_ = $context["macros"]; } else { $_macros_ = null; }
            echo $_macros_->getno_data();
            echo "
\t\t\t";
        }
        // line 53
        echo "\t</article>
</section>";
    }

    public function getTemplateName()
    {
        return "ReportBundle:Overview:tickets-resolved.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  119 => 35,  93 => 27,  144 => 40,  129 => 39,  471 => 106,  462 => 105,  439 => 98,  431 => 95,  426 => 92,  420 => 91,  417 => 90,  382 => 86,  378 => 85,  372 => 84,  350 => 78,  335 => 73,  327 => 71,  303 => 64,  300 => 63,  296 => 62,  292 => 61,  281 => 58,  275 => 56,  264 => 53,  253 => 52,  220 => 45,  208 => 43,  194 => 41,  171 => 37,  166 => 36,  118 => 35,  105 => 21,  79 => 24,  22 => 104,  130 => 27,  85 => 25,  162 => 48,  143 => 54,  131 => 48,  127 => 26,  123 => 45,  120 => 35,  109 => 40,  43 => 8,  217 => 63,  211 => 59,  168 => 51,  165 => 64,  160 => 47,  134 => 38,  115 => 34,  110 => 32,  89 => 30,  73 => 18,  42 => 14,  447 => 156,  437 => 148,  429 => 142,  418 => 141,  407 => 140,  370 => 137,  364 => 134,  354 => 127,  341 => 119,  333 => 114,  306 => 96,  289 => 60,  276 => 81,  271 => 78,  265 => 75,  258 => 72,  255 => 71,  240 => 52,  214 => 175,  207 => 173,  193 => 164,  186 => 54,  173 => 66,  161 => 64,  158 => 62,  116 => 34,  111 => 32,  106 => 31,  84 => 22,  80 => 21,  122 => 33,  113 => 41,  76 => 23,  59 => 19,  21 => 2,  69 => 22,  65 => 16,  61 => 16,  53 => 13,  64 => 18,  51 => 15,  31 => 5,  153 => 61,  150 => 44,  133 => 41,  102 => 32,  87 => 29,  75 => 23,  67 => 20,  50 => 8,  38 => 8,  35 => 12,  30 => 2,  1374 => 443,  1370 => 441,  1365 => 438,  1350 => 436,  1345 => 433,  1330 => 431,  1315 => 429,  1312 => 428,  1305 => 427,  1302 => 426,  1298 => 424,  1295 => 423,  1283 => 422,  1276 => 421,  1268 => 420,  1265 => 419,  1247 => 418,  1236 => 417,  1218 => 416,  1211 => 414,  1208 => 413,  1201 => 411,  1198 => 410,  1180 => 409,  1177 => 408,  1168 => 404,  1164 => 403,  1158 => 399,  1151 => 397,  1129 => 393,  1112 => 392,  1101 => 390,  1096 => 389,  1092 => 387,  1087 => 386,  1084 => 385,  1066 => 382,  1058 => 381,  1041 => 380,  1035 => 376,  1030 => 373,  1015 => 371,  1010 => 368,  995 => 366,  981 => 365,  967 => 364,  953 => 363,  938 => 361,  935 => 360,  932 => 359,  928 => 357,  925 => 356,  913 => 355,  906 => 354,  898 => 353,  895 => 352,  877 => 351,  858 => 350,  839 => 349,  821 => 348,  810 => 347,  792 => 346,  782 => 344,  779 => 343,  772 => 341,  769 => 340,  751 => 339,  748 => 338,  742 => 337,  736 => 336,  726 => 331,  720 => 330,  714 => 329,  709 => 328,  703 => 324,  696 => 322,  682 => 321,  676 => 320,  654 => 316,  635 => 315,  629 => 314,  623 => 313,  620 => 312,  602 => 311,  591 => 309,  586 => 308,  582 => 306,  577 => 305,  574 => 304,  560 => 303,  554 => 302,  544 => 299,  540 => 297,  536 => 295,  532 => 294,  528 => 293,  524 => 292,  520 => 291,  516 => 290,  512 => 289,  508 => 288,  504 => 287,  500 => 286,  496 => 285,  490 => 284,  475 => 281,  469 => 280,  432 => 272,  329 => 209,  284 => 59,  267 => 54,  259 => 171,  238 => 155,  204 => 129,  175 => 53,  101 => 29,  95 => 32,  92 => 18,  81 => 20,  71 => 22,  46 => 10,  40 => 4,  32 => 9,  477 => 191,  434 => 181,  405 => 172,  331 => 160,  322 => 106,  311 => 154,  302 => 95,  266 => 145,  263 => 144,  245 => 58,  242 => 72,  196 => 138,  191 => 71,  188 => 134,  183 => 53,  99 => 49,  96 => 26,  56 => 14,  34 => 4,  24 => 4,  313 => 150,  310 => 66,  307 => 207,  304 => 147,  295 => 146,  291 => 145,  282 => 143,  279 => 185,  254 => 131,  249 => 130,  233 => 123,  226 => 120,  222 => 118,  218 => 116,  215 => 44,  203 => 172,  195 => 56,  187 => 111,  182 => 110,  159 => 97,  146 => 92,  128 => 37,  125 => 37,  63 => 20,  60 => 15,  44 => 5,  33 => 4,  399 => 79,  388 => 77,  380 => 138,  377 => 74,  373 => 72,  357 => 80,  346 => 76,  334 => 66,  316 => 67,  305 => 61,  298 => 60,  287 => 144,  251 => 51,  244 => 49,  241 => 124,  235 => 70,  231 => 45,  227 => 66,  219 => 140,  199 => 36,  192 => 35,  181 => 33,  174 => 32,  156 => 45,  145 => 58,  138 => 29,  126 => 36,  114 => 33,  91 => 24,  82 => 24,  68 => 12,  57 => 14,  48 => 7,  39 => 2,  36 => 5,  28 => 1,  23 => 1,  19 => 1,  436 => 273,  430 => 156,  428 => 155,  424 => 178,  421 => 152,  416 => 175,  410 => 159,  408 => 152,  401 => 151,  391 => 139,  387 => 171,  383 => 169,  379 => 144,  376 => 143,  371 => 221,  366 => 83,  361 => 82,  356 => 59,  349 => 219,  347 => 163,  344 => 142,  342 => 75,  336 => 137,  330 => 72,  318 => 208,  315 => 128,  308 => 126,  297 => 94,  290 => 148,  278 => 114,  272 => 55,  260 => 106,  257 => 105,  250 => 59,  239 => 47,  232 => 153,  221 => 177,  210 => 86,  201 => 42,  190 => 39,  184 => 82,  179 => 57,  167 => 52,  157 => 68,  154 => 33,  151 => 66,  149 => 46,  142 => 40,  140 => 43,  108 => 32,  103 => 36,  98 => 28,  94 => 28,  88 => 26,  83 => 26,  78 => 20,  74 => 23,  70 => 17,  66 => 27,  58 => 14,  54 => 13,  49 => 11,  45 => 6,  41 => 8,  37 => 7,  673 => 200,  669 => 198,  664 => 195,  649 => 193,  634 => 191,  631 => 190,  617 => 189,  603 => 188,  599 => 186,  587 => 184,  581 => 183,  569 => 181,  563 => 180,  551 => 178,  545 => 177,  533 => 175,  527 => 174,  515 => 172,  506 => 171,  503 => 170,  497 => 169,  494 => 168,  481 => 282,  473 => 164,  467 => 163,  463 => 279,  460 => 278,  457 => 187,  454 => 159,  450 => 102,  446 => 157,  442 => 99,  423 => 155,  419 => 154,  398 => 88,  392 => 152,  389 => 151,  386 => 150,  368 => 167,  363 => 148,  360 => 220,  343 => 146,  338 => 74,  328 => 141,  323 => 69,  317 => 103,  314 => 135,  280 => 57,  269 => 77,  262 => 74,  237 => 85,  230 => 122,  223 => 64,  216 => 38,  209 => 69,  202 => 65,  172 => 55,  169 => 36,  163 => 35,  155 => 46,  152 => 44,  147 => 42,  141 => 30,  136 => 38,  132 => 36,  112 => 33,  107 => 30,  104 => 38,  100 => 26,  90 => 30,  77 => 19,  72 => 31,  62 => 15,  55 => 17,  52 => 9,  47 => 12,  29 => 1,  27 => 1,);
    }
}
