<?php

/* ReportBundle:ReportBuilder:query-editor.html.twig */
class __TwigTemplate_fc5281010729945c8f4f5bd97372229b extends \Application\DeskPRO\Twig\Template
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
        if (isset($context["readOnly"])) { $_readOnly_ = $context["readOnly"]; } else { $_readOnly_ = null; }
        $context["readOnlyHtml"] = (($_readOnly_) ? (" readonly=\"readonly\"") : (""));
        // line 2
        echo "<div class=\"query-editor-container ";
        if (isset($context["readOnly"])) { $_readOnly_ = $context["readOnly"]; } else { $_readOnly_ = null; }
        echo (($_readOnly_) ? ("query-read-only") : (""));
        echo "\">
<ul class=\"tabs\"
\tdata-element-handler=\"DeskPRO.Report.ElementHandler.Builder.BuilderTabs\"
\tdata-trigger-elements=\"> li\"
\tdata-input-type-input=\"#input-type-input\"
\tdata-query-switch-url=\"";
        // line 7
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_builder_parse"), "html", null, true);
        echo "\"
\tdata-query-error-container=\"#query-error-container\"
\tdata-query-error-message=\"#query-error-message\"
>
\t<li data-tab-for=\"#query-builder\" data-query-type=\"builder\" class=\"";
        // line 11
        if (isset($context["inputType"])) { $_inputType_ = $context["inputType"]; } else { $_inputType_ = null; }
        echo ((($_inputType_ == "builder")) ? ("current") : (""));
        echo "\"><a>Builder</a></li>
\t<li data-tab-for=\"#query-text\" data-query-type=\"query\" class=\"";
        // line 12
        if (isset($context["inputType"])) { $_inputType_ = $context["inputType"]; } else { $_inputType_ = null; }
        echo ((($_inputType_ == "query")) ? ("current") : (""));
        echo "\"><a>Query</a></li>
</ul>

<div id=\"query-builder\" class=\"query-editor-builder\" style=\"display: none\">
\t<dl><dt class=\"no-pad\">DISPLAY</dt> <dd>
\t\t<select name=\"parts[display][0]\" class=\"";
        // line 17
        if (isset($context["readOnly"])) { $_readOnly_ = $context["readOnly"]; } else { $_readOnly_ = null; }
        echo (($_readOnly_) ? ("readonly") : (""));
        echo "\">
\t\t\t<option value=\"BAR\"";
        // line 18
        if (isset($context["parts"])) { $_parts_ = $context["parts"]; } else { $_parts_ = null; }
        echo ((($this->getAttribute($this->getAttribute($_parts_, "display"), 0) == "BAR")) ? (" selected=\"selected\"") : (""));
        echo ">BAR</option>
\t\t\t<option value=\"LINE\"";
        // line 19
        if (isset($context["parts"])) { $_parts_ = $context["parts"]; } else { $_parts_ = null; }
        echo ((($this->getAttribute($this->getAttribute($_parts_, "display"), 0) == "LINE")) ? (" selected=\"selected\"") : (""));
        echo ">LINE</option>
\t\t\t<option value=\"AREA\"";
        // line 20
        if (isset($context["parts"])) { $_parts_ = $context["parts"]; } else { $_parts_ = null; }
        echo ((($this->getAttribute($this->getAttribute($_parts_, "display"), 0) == "AREA")) ? (" selected=\"selected\"") : (""));
        echo ">AREA</option>
\t\t\t<option value=\"PIE\"";
        // line 21
        if (isset($context["parts"])) { $_parts_ = $context["parts"]; } else { $_parts_ = null; }
        echo ((($this->getAttribute($this->getAttribute($_parts_, "display"), 0) == "PIE")) ? (" selected=\"selected\"") : (""));
        echo ">PIE</option>
\t\t\t<option value=\"TABLE\"";
        // line 22
        if (isset($context["parts"])) { $_parts_ = $context["parts"]; } else { $_parts_ = null; }
        echo (((($this->getAttribute($this->getAttribute($_parts_, "display"), 0) == "TABLE") || twig_test_empty($this->getAttribute($_parts_, "display")))) ? (" selected=\"selected\"") : (""));
        echo ">TABLE</option>
\t\t</select>
\t\t<select name=\"parts[display][1]\" class=\"";
        // line 24
        if (isset($context["readOnly"])) { $_readOnly_ = $context["readOnly"]; } else { $_readOnly_ = null; }
        echo (($_readOnly_) ? ("readonly") : (""));
        echo "\">
\t\t\t<option value=\"\">&nbsp;</option>
\t\t\t<option value=\"BAR\"";
        // line 26
        if (isset($context["parts"])) { $_parts_ = $context["parts"]; } else { $_parts_ = null; }
        echo ((($this->getAttribute($this->getAttribute($_parts_, "display"), 1) == "BAR")) ? (" selected=\"selected\"") : (""));
        echo ">BAR</option>
\t\t\t<option value=\"LINE\"";
        // line 27
        if (isset($context["parts"])) { $_parts_ = $context["parts"]; } else { $_parts_ = null; }
        echo ((($this->getAttribute($this->getAttribute($_parts_, "display"), 1) == "LINE")) ? (" selected=\"selected\"") : (""));
        echo ">LINE</option>
\t\t\t<option value=\"AREA\"";
        // line 28
        if (isset($context["parts"])) { $_parts_ = $context["parts"]; } else { $_parts_ = null; }
        echo ((($this->getAttribute($this->getAttribute($_parts_, "display"), 1) == "AREA")) ? (" selected=\"selected\"") : (""));
        echo ">AREA</option>
\t\t\t<option value=\"PIE\"";
        // line 29
        if (isset($context["parts"])) { $_parts_ = $context["parts"]; } else { $_parts_ = null; }
        echo ((($this->getAttribute($this->getAttribute($_parts_, "display"), 1) == "PIE")) ? (" selected=\"selected\"") : (""));
        echo ">PIE</option>
\t\t\t<option value=\"TABLE\"";
        // line 30
        if (isset($context["parts"])) { $_parts_ = $context["parts"]; } else { $_parts_ = null; }
        echo ((($this->getAttribute($this->getAttribute($_parts_, "display"), 1) == "TABLE")) ? (" selected=\"selected\"") : (""));
        echo ">TABLE</option>
\t\t</select>
\t</dd></dl>
\t<dl><dt>SELECT</dt> <dd><textarea name=\"parts[select]\" class=\"expander\"";
        // line 33
        if (isset($context["readOnlyHtml"])) { $_readOnlyHtml_ = $context["readOnlyHtml"]; } else { $_readOnlyHtml_ = null; }
        echo $_readOnlyHtml_;
        echo ">";
        if (isset($context["parts"])) { $_parts_ = $context["parts"]; } else { $_parts_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_parts_, "select"), "html", null, true);
        echo "</textarea></dd></dl>
\t<dl><dt>FROM</dt> <dd><input name=\"parts[from]\" type=\"text\" value=\"";
        // line 34
        if (isset($context["parts"])) { $_parts_ = $context["parts"]; } else { $_parts_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_parts_, "from"), "html", null, true);
        echo "\"";
        if (isset($context["readOnlyHtml"])) { $_readOnlyHtml_ = $context["readOnlyHtml"]; } else { $_readOnlyHtml_ = null; }
        echo $_readOnlyHtml_;
        echo " /></dd></dl>
\t<dl><dt>WHERE</dt> <dd><textarea name=\"parts[where]\" class=\"expander\"";
        // line 35
        if (isset($context["readOnlyHtml"])) { $_readOnlyHtml_ = $context["readOnlyHtml"]; } else { $_readOnlyHtml_ = null; }
        echo $_readOnlyHtml_;
        echo ">";
        if (isset($context["parts"])) { $_parts_ = $context["parts"]; } else { $_parts_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_parts_, "where"), "html", null, true);
        echo "</textarea></dd></dl>
\t<dl><dt>SPLIT BY</dt> <dd><input name=\"parts[splitBy]\" type=\"text\" value=\"";
        // line 36
        if (isset($context["parts"])) { $_parts_ = $context["parts"]; } else { $_parts_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_parts_, "splitBy"), "html", null, true);
        echo "\"";
        if (isset($context["readOnlyHtml"])) { $_readOnlyHtml_ = $context["readOnlyHtml"]; } else { $_readOnlyHtml_ = null; }
        echo $_readOnlyHtml_;
        echo " /></dd></dl>
\t<dl><dt>GROUP BY</dt> <dd><input name=\"parts[groupBy]\" type=\"text\" value=\"";
        // line 37
        if (isset($context["parts"])) { $_parts_ = $context["parts"]; } else { $_parts_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_parts_, "groupBy"), "html", null, true);
        echo "\"";
        if (isset($context["readOnlyHtml"])) { $_readOnlyHtml_ = $context["readOnlyHtml"]; } else { $_readOnlyHtml_ = null; }
        echo $_readOnlyHtml_;
        echo " /></dd></dl>
\t<dl><dt>ORDER BY</dt> <dd><input name=\"parts[orderBy]\" type=\"text\" value=\"";
        // line 38
        if (isset($context["parts"])) { $_parts_ = $context["parts"]; } else { $_parts_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_parts_, "orderBy"), "html", null, true);
        echo "\"";
        if (isset($context["readOnlyHtml"])) { $_readOnlyHtml_ = $context["readOnlyHtml"]; } else { $_readOnlyHtml_ = null; }
        echo $_readOnlyHtml_;
        echo " /></dd></dl>
\t<dl class=\"limit\">
\t\t<dt>LIMIT</dt>
\t\t<dd>
\t\t\t<input name=\"parts[limit]\" type=\"text\" value=\"";
        // line 42
        if (isset($context["parts"])) { $_parts_ = $context["parts"]; } else { $_parts_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_parts_, "limit"), "html", null, true);
        echo "\" ";
        if (isset($context["readOnlyHtml"])) { $_readOnlyHtml_ = $context["readOnlyHtml"]; } else { $_readOnlyHtml_ = null; }
        echo $_readOnlyHtml_;
        echo " />
\t\t\t<span>OFFSET</span>
\t\t\t<input name=\"parts[offset]\" type=\"text\" value=\"";
        // line 44
        if (isset($context["parts"])) { $_parts_ = $context["parts"]; } else { $_parts_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_parts_, "offset"), "html", null, true);
        echo "\" ";
        if (isset($context["readOnlyHtml"])) { $_readOnlyHtml_ = $context["readOnlyHtml"]; } else { $_readOnlyHtml_ = null; }
        echo $_readOnlyHtml_;
        echo " />
\t\t</dd>
\t</dl>
</div>
<div class=\"query-editor-text\" id=\"query-text\" style=\"display: none\">
\t<textarea name=\"query\" class=\"expander\" data-expander-min-height=\"100\"";
        // line 49
        if (isset($context["readOnlyHtml"])) { $_readOnlyHtml_ = $context["readOnlyHtml"]; } else { $_readOnlyHtml_ = null; }
        echo $_readOnlyHtml_;
        echo ">";
        if (isset($context["query"])) { $_query_ = $context["query"]; } else { $_query_ = null; }
        echo twig_escape_filter($this->env, $_query_, "html", null, true);
        echo "</textarea>
</div>

<div id=\"query-error-container\" class=\"query-error\" style=\"display: none\">
\tThe following error occurred: <span id=\"query-error-message\"></span>
</div>

";
        // line 56
        if (isset($context["readOnly"])) { $_readOnly_ = $context["readOnly"]; } else { $_readOnly_ = null; }
        if ($_readOnly_) {
            // line 57
            echo "\t<div class=\"query-read-only-notice\">This is a built-in query and cannot be modified. To modify this query, please clone it.</div>
";
        }
        // line 59
        echo "
<input type=\"hidden\" name=\"inputType\" id=\"input-type-input\" value=\"";
        // line 60
        if (isset($context["inputType"])) { $_inputType_ = $context["inputType"]; } else { $_inputType_ = null; }
        echo twig_escape_filter($this->env, $_inputType_, "html", null, true);
        echo "\" />
</div>";
    }

    public function getTemplateName()
    {
        return "ReportBundle:ReportBuilder:query-editor.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  206 => 57,  189 => 49,  177 => 44,  117 => 33,  97 => 33,  268 => 29,  252 => 21,  234 => 87,  213 => 60,  205 => 81,  178 => 76,  164 => 69,  135 => 60,  121 => 42,  86 => 27,  119 => 36,  93 => 27,  144 => 40,  129 => 44,  471 => 106,  462 => 105,  439 => 98,  431 => 95,  426 => 92,  420 => 91,  417 => 90,  382 => 86,  378 => 85,  372 => 84,  350 => 78,  335 => 73,  327 => 71,  303 => 64,  300 => 63,  296 => 62,  292 => 61,  281 => 58,  275 => 32,  264 => 53,  253 => 52,  220 => 45,  208 => 43,  194 => 41,  171 => 37,  166 => 36,  118 => 53,  105 => 29,  79 => 22,  22 => 2,  130 => 59,  85 => 23,  162 => 48,  143 => 54,  131 => 48,  127 => 26,  123 => 51,  120 => 35,  109 => 40,  43 => 8,  217 => 63,  211 => 59,  168 => 42,  165 => 64,  160 => 47,  134 => 38,  115 => 34,  110 => 30,  89 => 27,  73 => 21,  42 => 7,  447 => 156,  437 => 148,  429 => 142,  418 => 141,  407 => 140,  370 => 137,  364 => 134,  354 => 127,  341 => 119,  333 => 114,  306 => 96,  289 => 60,  276 => 81,  271 => 78,  265 => 75,  258 => 72,  255 => 22,  240 => 52,  214 => 175,  207 => 173,  193 => 76,  186 => 54,  173 => 72,  161 => 64,  158 => 62,  116 => 34,  111 => 32,  106 => 37,  84 => 24,  80 => 36,  122 => 33,  113 => 41,  76 => 23,  59 => 14,  21 => 2,  69 => 22,  65 => 16,  61 => 16,  53 => 17,  64 => 13,  51 => 12,  31 => 3,  153 => 61,  150 => 44,  133 => 35,  102 => 32,  87 => 29,  75 => 23,  67 => 16,  50 => 9,  38 => 4,  35 => 3,  30 => 1,  1374 => 443,  1370 => 441,  1365 => 438,  1350 => 436,  1345 => 433,  1330 => 431,  1315 => 429,  1312 => 428,  1305 => 427,  1302 => 426,  1298 => 424,  1295 => 423,  1283 => 422,  1276 => 421,  1268 => 420,  1265 => 419,  1247 => 418,  1236 => 417,  1218 => 416,  1211 => 414,  1208 => 413,  1201 => 411,  1198 => 410,  1180 => 409,  1177 => 408,  1168 => 404,  1164 => 403,  1158 => 399,  1151 => 397,  1129 => 393,  1112 => 392,  1101 => 390,  1096 => 389,  1092 => 387,  1087 => 386,  1084 => 385,  1066 => 382,  1058 => 381,  1041 => 380,  1035 => 376,  1030 => 373,  1015 => 371,  1010 => 368,  995 => 366,  981 => 365,  967 => 364,  953 => 363,  938 => 361,  935 => 360,  932 => 359,  928 => 357,  925 => 356,  913 => 355,  906 => 354,  898 => 353,  895 => 352,  877 => 351,  858 => 350,  839 => 349,  821 => 348,  810 => 347,  792 => 346,  782 => 344,  779 => 343,  772 => 341,  769 => 340,  751 => 339,  748 => 338,  742 => 337,  736 => 336,  726 => 331,  720 => 330,  714 => 329,  709 => 328,  703 => 324,  696 => 322,  682 => 321,  676 => 320,  654 => 316,  635 => 315,  629 => 314,  623 => 313,  620 => 312,  602 => 311,  591 => 309,  586 => 308,  582 => 306,  577 => 305,  574 => 304,  560 => 303,  554 => 302,  544 => 299,  540 => 297,  536 => 295,  532 => 294,  528 => 293,  524 => 292,  520 => 291,  516 => 290,  512 => 289,  508 => 288,  504 => 287,  500 => 286,  496 => 285,  490 => 284,  475 => 281,  469 => 280,  432 => 272,  329 => 209,  284 => 59,  267 => 54,  259 => 26,  238 => 155,  204 => 129,  175 => 53,  101 => 29,  95 => 27,  92 => 31,  81 => 25,  71 => 23,  46 => 9,  40 => 6,  32 => 7,  477 => 191,  434 => 181,  405 => 172,  331 => 160,  322 => 106,  311 => 154,  302 => 95,  266 => 145,  263 => 28,  245 => 58,  242 => 72,  196 => 138,  191 => 71,  188 => 63,  183 => 74,  99 => 49,  96 => 47,  56 => 14,  34 => 9,  24 => 3,  313 => 150,  310 => 66,  307 => 207,  304 => 147,  295 => 146,  291 => 145,  282 => 143,  279 => 185,  254 => 131,  249 => 130,  233 => 123,  226 => 120,  222 => 118,  218 => 116,  215 => 88,  203 => 56,  195 => 56,  187 => 111,  182 => 110,  159 => 65,  146 => 92,  128 => 52,  125 => 34,  63 => 19,  60 => 11,  44 => 12,  33 => 7,  399 => 79,  388 => 77,  380 => 138,  377 => 74,  373 => 72,  357 => 80,  346 => 76,  334 => 66,  316 => 67,  305 => 61,  298 => 60,  287 => 144,  251 => 51,  244 => 49,  241 => 124,  235 => 70,  231 => 45,  227 => 98,  219 => 140,  199 => 36,  192 => 35,  181 => 61,  174 => 32,  156 => 45,  145 => 62,  138 => 53,  126 => 57,  114 => 39,  91 => 24,  82 => 24,  68 => 20,  57 => 16,  48 => 11,  39 => 11,  36 => 10,  28 => 2,  23 => 1,  19 => 1,  436 => 273,  430 => 156,  428 => 155,  424 => 178,  421 => 152,  416 => 175,  410 => 159,  408 => 152,  401 => 151,  391 => 139,  387 => 171,  383 => 169,  379 => 144,  376 => 143,  371 => 221,  366 => 83,  361 => 82,  356 => 59,  349 => 219,  347 => 163,  344 => 142,  342 => 75,  336 => 137,  330 => 72,  318 => 208,  315 => 128,  308 => 126,  297 => 94,  290 => 148,  278 => 114,  272 => 55,  260 => 106,  257 => 105,  250 => 59,  239 => 20,  232 => 153,  221 => 177,  210 => 59,  201 => 79,  190 => 39,  184 => 78,  179 => 57,  167 => 69,  157 => 38,  154 => 33,  151 => 66,  149 => 37,  142 => 40,  140 => 43,  108 => 51,  103 => 50,  98 => 35,  94 => 32,  88 => 26,  83 => 26,  78 => 22,  74 => 23,  70 => 20,  66 => 16,  58 => 18,  54 => 13,  49 => 13,  45 => 8,  41 => 10,  37 => 7,  673 => 200,  669 => 198,  664 => 195,  649 => 193,  634 => 191,  631 => 190,  617 => 189,  603 => 188,  599 => 186,  587 => 184,  581 => 183,  569 => 181,  563 => 180,  551 => 178,  545 => 177,  533 => 175,  527 => 174,  515 => 172,  506 => 171,  503 => 170,  497 => 169,  494 => 168,  481 => 282,  473 => 164,  467 => 163,  463 => 279,  460 => 278,  457 => 187,  454 => 159,  450 => 102,  446 => 157,  442 => 99,  423 => 155,  419 => 154,  398 => 88,  392 => 152,  389 => 151,  386 => 150,  368 => 167,  363 => 148,  360 => 220,  343 => 146,  338 => 74,  328 => 141,  323 => 69,  317 => 103,  314 => 135,  280 => 57,  269 => 77,  262 => 74,  237 => 85,  230 => 122,  223 => 64,  216 => 38,  209 => 69,  202 => 65,  172 => 72,  169 => 55,  163 => 54,  155 => 66,  152 => 65,  147 => 55,  141 => 36,  136 => 38,  132 => 45,  112 => 33,  107 => 39,  104 => 38,  100 => 28,  90 => 26,  77 => 35,  72 => 19,  62 => 15,  55 => 17,  52 => 14,  47 => 7,  29 => 4,  27 => 1,);
    }
}
