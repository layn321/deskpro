<?php

/* ReportBundle:ReportBuilder:edit.html.twig */
class __TwigTemplate_ee1b10302caa05498eab964deca20a5d extends \Application\DeskPRO\Twig\Template
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
        echo "<form action=\"";
        if (isset($context["report"])) { $_report_ = $context["report"]; } else { $_report_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_builder_edit", array("report_builder_id" => (($this->getAttribute($_report_, "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_report_, "id"), 0)) : (0)))), "html", null, true);
        echo "\" method=\"post\">

\t<div class=\"dp-form\">
\t\t";
        // line 6
        if (isset($context["errors"])) { $_errors_ = $context["errors"]; } else { $_errors_ = null; }
        if ($_errors_) {
            // line 7
            echo "\t\t\t<ul id=\"errors_container\">
\t\t\t";
            // line 8
            if (isset($context["errors"])) { $_errors_ = $context["errors"]; } else { $_errors_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_errors_);
            foreach ($context['_seq'] as $context["_key"] => $context["error"]) {
                // line 9
                echo "\t\t\t\t<li>";
                if (isset($context["error"])) { $_error_ = $context["error"]; } else { $_error_ = null; }
                echo twig_escape_filter($this->env, $_error_, "html", null, true);
                echo "</li>
\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['error'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 11
            echo "\t\t\t</ul>
\t\t";
        }
        // line 13
        echo "
\t\t<div class=\"dp-form-row\">
\t\t\t<div class=\"dp-form-label\">
\t\t\t\t<label>Title</label>
\t\t\t</div>
\t\t\t<div class=\"dp-form-input\">
\t\t\t\t<input type=\"text\" name=\"title\" value=\"";
        // line 19
        if (isset($context["report"])) { $_report_ = $context["report"]; } else { $_report_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_report_, "title"), "html", null, true);
        echo "\" maxlength=\"255\" />
\t\t\t</div>
\t\t</div>
\t\t<div class=\"dp-form-row\">
\t\t\t<div class=\"dp-form-label\">
\t\t\t\t<label>Description</label>
\t\t\t</div>
\t\t\t<div class=\"dp-form-input\">
\t\t\t\t<textarea name=\"description\" rows=\"3\">";
        // line 27
        if (isset($context["report"])) { $_report_ = $context["report"]; } else { $_report_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_report_, "description"), "html", null, true);
        echo "</textarea>
\t\t\t</div>
\t\t</div>

\t\t";
        // line 31
        $this->env->loadTemplate("ReportBundle:ReportBuilder:query-editor.html.twig")->display($context);
        // line 32
        echo "
\t\t";
        // line 33
        if (isset($context["canManageBuiltIn"])) { $_canManageBuiltIn_ = $context["canManageBuiltIn"]; } else { $_canManageBuiltIn_ = null; }
        if ($_canManageBuiltIn_) {
            // line 34
            echo "\t\t\t<div class=\"dp-form-row\">
\t\t\t\t<div class=\"dp-form-label\">
\t\t\t\t\t<label>Unique Key</label>
\t\t\t\t</div>
\t\t\t\t<div class=\"dp-form-input\">
\t\t\t\t\t<input type=\"text\" name=\"unique_key\" value=\"";
            // line 39
            if (isset($context["report"])) { $_report_ = $context["report"]; } else { $_report_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_report_, "unique_key"), "html", null, true);
            echo "\" maxlength=\"50\" />
\t\t\t\t\t<div>If a unique key is specified, this will become a built-in report.</div>
\t\t\t\t</div>
\t\t\t</div>

\t\t\t<div class=\"dp-form-row\">
\t\t\t\t<div class=\"dp-form-label\">
\t\t\t\t\t<label>Built-in Report Category</label>
\t\t\t\t</div>
\t\t\t\t<div class=\"dp-form-input\">
\t\t\t\t\t<select name=\"category\">
\t\t\t\t\t\t<option value=\"\">&nbsp;</option>
\t\t\t\t\t";
            // line 51
            if (isset($context["builtInCategories"])) { $_builtInCategories_ = $context["builtInCategories"]; } else { $_builtInCategories_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_builtInCategories_);
            foreach ($context['_seq'] as $context["categoryId"] => $context["categoryName"]) {
                // line 52
                echo "\t\t\t\t\t\t<option value=\"";
                if (isset($context["categoryId"])) { $_categoryId_ = $context["categoryId"]; } else { $_categoryId_ = null; }
                echo twig_escape_filter($this->env, $_categoryId_, "html", null, true);
                echo "\"";
                if (isset($context["categoryId"])) { $_categoryId_ = $context["categoryId"]; } else { $_categoryId_ = null; }
                if (isset($context["report"])) { $_report_ = $context["report"]; } else { $_report_ = null; }
                echo ((($_categoryId_ == $this->getAttribute($_report_, "category"))) ? (" selected=\"selected\"") : (""));
                echo "
\t\t\t\t\t\t\t>";
                // line 53
                if (isset($context["categoryName"])) { $_categoryName_ = $context["categoryName"]; } else { $_categoryName_ = null; }
                echo twig_escape_filter($this->env, $_categoryName_, "html", null, true);
                echo "</option>
\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['categoryId'], $context['categoryName'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 55
            echo "\t\t\t\t\t</select>
\t\t\t\t\t<div>If this is a built-in report, select the category it applies to.</div>
\t\t\t\t</div>
\t\t\t</div>

\t\t\t<div class=\"dp-form-row\">
\t\t\t\t<div class=\"dp-form-label\">
\t\t\t\t\t<label>Display Order</label>
\t\t\t\t</div>
\t\t\t\t<div class=\"dp-form-input\">
\t\t\t\t\t<input type=\"text\" name=\"display_order\" value=\"";
            // line 65
            if (isset($context["report"])) { $_report_ = $context["report"]; } else { $_report_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($_report_, "display_order", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_report_, "display_order"), 0)) : (0)), "html", null, true);
            echo "\"  />
\t\t\t\t</div>
\t\t\t</div>
\t\t";
        } else {
            // line 69
            echo "\t\t\t<input type=\"hidden\" name=\"unique_key\" value=\"\" />
\t\t\t<input type=\"hidden\" name=\"category\" value=\"\" />
\t\t";
        }
        // line 72
        echo "\t</div>

\t<button class=\"btn primary save-trigger\">Save Report</button>

\t<input type=\"hidden\" name=\"parent_id\" value=\"";
        // line 76
        if (isset($context["report"])) { $_report_ = $context["report"]; } else { $_report_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_report_, "parent")) ? ($this->getAttribute($this->getAttribute($_report_, "parent"), "id")) : ("")), "html", null, true);
        echo "\" />
\t<input type=\"hidden\" name=\"process\" value=\"1\" />
\t";
        // line 78
        echo $this->env->getExtension('deskpro_templating')->formToken();
        echo "

\t<p style=\"margin-top: 1em; padding-top: 1em; border-top: 1px solid #DEDEDE\">Further information about the report builder system is available in our knowledge base:</p>
\t<ul>
\t\t<li><a href=\"http://support.deskpro.com/kb/articles/87-using-the-report-builder\" target=\"_blank\">Using the Report Builder</a></li>
\t\t<li><a href=\"http://support.deskpro.com/kb/articles/86-dpql-reference\" target=\"_blank\">DeskPRO Query Language Reference</a></li>
\t\t<li><a href=\"http://support.deskpro.com/kb/articles/85-dpql-field-reference\" target=\"_blank\">DeskPRO Query Language Field Reference</a></li>
\t</ul>
</form>
";
    }

    public function getTemplateName()
    {
        return "ReportBundle:ReportBuilder:edit.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  97 => 33,  268 => 29,  252 => 21,  234 => 87,  213 => 87,  205 => 81,  178 => 76,  164 => 69,  135 => 60,  121 => 42,  86 => 27,  119 => 36,  93 => 27,  144 => 40,  129 => 44,  471 => 106,  462 => 105,  439 => 98,  431 => 95,  426 => 92,  420 => 91,  417 => 90,  382 => 86,  378 => 85,  372 => 84,  350 => 78,  335 => 73,  327 => 71,  303 => 64,  300 => 63,  296 => 62,  292 => 61,  281 => 58,  275 => 32,  264 => 53,  253 => 52,  220 => 45,  208 => 43,  194 => 41,  171 => 37,  166 => 36,  118 => 53,  105 => 21,  79 => 22,  22 => 104,  130 => 59,  85 => 23,  162 => 48,  143 => 54,  131 => 48,  127 => 26,  123 => 51,  120 => 35,  109 => 40,  43 => 8,  217 => 63,  211 => 59,  168 => 71,  165 => 64,  160 => 47,  134 => 38,  115 => 34,  110 => 32,  89 => 27,  73 => 18,  42 => 7,  447 => 156,  437 => 148,  429 => 142,  418 => 141,  407 => 140,  370 => 137,  364 => 134,  354 => 127,  341 => 119,  333 => 114,  306 => 96,  289 => 60,  276 => 81,  271 => 78,  265 => 75,  258 => 72,  255 => 22,  240 => 52,  214 => 175,  207 => 173,  193 => 76,  186 => 54,  173 => 72,  161 => 64,  158 => 62,  116 => 34,  111 => 32,  106 => 37,  84 => 27,  80 => 36,  122 => 33,  113 => 41,  76 => 23,  59 => 14,  21 => 2,  69 => 22,  65 => 16,  61 => 16,  53 => 11,  64 => 13,  51 => 12,  31 => 3,  153 => 61,  150 => 44,  133 => 41,  102 => 32,  87 => 29,  75 => 23,  67 => 16,  50 => 9,  38 => 4,  35 => 3,  30 => 1,  1374 => 443,  1370 => 441,  1365 => 438,  1350 => 436,  1345 => 433,  1330 => 431,  1315 => 429,  1312 => 428,  1305 => 427,  1302 => 426,  1298 => 424,  1295 => 423,  1283 => 422,  1276 => 421,  1268 => 420,  1265 => 419,  1247 => 418,  1236 => 417,  1218 => 416,  1211 => 414,  1208 => 413,  1201 => 411,  1198 => 410,  1180 => 409,  1177 => 408,  1168 => 404,  1164 => 403,  1158 => 399,  1151 => 397,  1129 => 393,  1112 => 392,  1101 => 390,  1096 => 389,  1092 => 387,  1087 => 386,  1084 => 385,  1066 => 382,  1058 => 381,  1041 => 380,  1035 => 376,  1030 => 373,  1015 => 371,  1010 => 368,  995 => 366,  981 => 365,  967 => 364,  953 => 363,  938 => 361,  935 => 360,  932 => 359,  928 => 357,  925 => 356,  913 => 355,  906 => 354,  898 => 353,  895 => 352,  877 => 351,  858 => 350,  839 => 349,  821 => 348,  810 => 347,  792 => 346,  782 => 344,  779 => 343,  772 => 341,  769 => 340,  751 => 339,  748 => 338,  742 => 337,  736 => 336,  726 => 331,  720 => 330,  714 => 329,  709 => 328,  703 => 324,  696 => 322,  682 => 321,  676 => 320,  654 => 316,  635 => 315,  629 => 314,  623 => 313,  620 => 312,  602 => 311,  591 => 309,  586 => 308,  582 => 306,  577 => 305,  574 => 304,  560 => 303,  554 => 302,  544 => 299,  540 => 297,  536 => 295,  532 => 294,  528 => 293,  524 => 292,  520 => 291,  516 => 290,  512 => 289,  508 => 288,  504 => 287,  500 => 286,  496 => 285,  490 => 284,  475 => 281,  469 => 280,  432 => 272,  329 => 209,  284 => 59,  267 => 54,  259 => 26,  238 => 155,  204 => 129,  175 => 53,  101 => 29,  95 => 25,  92 => 31,  81 => 25,  71 => 23,  46 => 9,  40 => 4,  32 => 9,  477 => 191,  434 => 181,  405 => 172,  331 => 160,  322 => 106,  311 => 154,  302 => 95,  266 => 145,  263 => 28,  245 => 58,  242 => 72,  196 => 138,  191 => 71,  188 => 63,  183 => 74,  99 => 49,  96 => 47,  56 => 14,  34 => 9,  24 => 4,  313 => 150,  310 => 66,  307 => 207,  304 => 147,  295 => 146,  291 => 145,  282 => 143,  279 => 185,  254 => 131,  249 => 130,  233 => 123,  226 => 120,  222 => 118,  218 => 116,  215 => 88,  203 => 172,  195 => 56,  187 => 111,  182 => 110,  159 => 65,  146 => 92,  128 => 52,  125 => 43,  63 => 18,  60 => 11,  44 => 6,  33 => 7,  399 => 79,  388 => 77,  380 => 138,  377 => 74,  373 => 72,  357 => 80,  346 => 76,  334 => 66,  316 => 67,  305 => 61,  298 => 60,  287 => 144,  251 => 51,  244 => 49,  241 => 124,  235 => 70,  231 => 45,  227 => 98,  219 => 140,  199 => 36,  192 => 35,  181 => 61,  174 => 32,  156 => 45,  145 => 62,  138 => 53,  126 => 57,  114 => 39,  91 => 24,  82 => 24,  68 => 12,  57 => 16,  48 => 11,  39 => 6,  36 => 10,  28 => 2,  23 => 1,  19 => 1,  436 => 273,  430 => 156,  428 => 155,  424 => 178,  421 => 152,  416 => 175,  410 => 159,  408 => 152,  401 => 151,  391 => 139,  387 => 171,  383 => 169,  379 => 144,  376 => 143,  371 => 221,  366 => 83,  361 => 82,  356 => 59,  349 => 219,  347 => 163,  344 => 142,  342 => 75,  336 => 137,  330 => 72,  318 => 208,  315 => 128,  308 => 126,  297 => 94,  290 => 148,  278 => 114,  272 => 55,  260 => 106,  257 => 105,  250 => 59,  239 => 20,  232 => 153,  221 => 177,  210 => 86,  201 => 79,  190 => 39,  184 => 78,  179 => 57,  167 => 69,  157 => 50,  154 => 33,  151 => 66,  149 => 64,  142 => 40,  140 => 43,  108 => 51,  103 => 50,  98 => 35,  94 => 32,  88 => 26,  83 => 26,  78 => 24,  74 => 23,  70 => 20,  66 => 16,  58 => 12,  54 => 13,  49 => 13,  45 => 8,  41 => 10,  37 => 7,  673 => 200,  669 => 198,  664 => 195,  649 => 193,  634 => 191,  631 => 190,  617 => 189,  603 => 188,  599 => 186,  587 => 184,  581 => 183,  569 => 181,  563 => 180,  551 => 178,  545 => 177,  533 => 175,  527 => 174,  515 => 172,  506 => 171,  503 => 170,  497 => 169,  494 => 168,  481 => 282,  473 => 164,  467 => 163,  463 => 279,  460 => 278,  457 => 187,  454 => 159,  450 => 102,  446 => 157,  442 => 99,  423 => 155,  419 => 154,  398 => 88,  392 => 152,  389 => 151,  386 => 150,  368 => 167,  363 => 148,  360 => 220,  343 => 146,  338 => 74,  328 => 141,  323 => 69,  317 => 103,  314 => 135,  280 => 57,  269 => 77,  262 => 74,  237 => 85,  230 => 122,  223 => 64,  216 => 38,  209 => 69,  202 => 65,  172 => 72,  169 => 55,  163 => 54,  155 => 66,  152 => 65,  147 => 55,  141 => 47,  136 => 38,  132 => 45,  112 => 33,  107 => 39,  104 => 38,  100 => 34,  90 => 44,  77 => 35,  72 => 19,  62 => 15,  55 => 17,  52 => 14,  47 => 7,  29 => 8,  27 => 1,);
    }
}
