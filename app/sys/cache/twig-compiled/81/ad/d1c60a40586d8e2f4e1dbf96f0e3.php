<?php

/* ReportBundle:ReportBuilder:layout.html.twig */
class __TwigTemplate_81add1c60a40586d8e2f4e1dbf96f0e3 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("ReportBundle::layout.html.twig");

        $this->blocks = array(
            'html_head' => array($this, 'block_html_head'),
            'page_js_exec' => array($this, 'block_page_js_exec'),
            'nav_block' => array($this, 'block_nav_block'),
            'pagebar' => array($this, 'block_pagebar'),
            'all_page' => array($this, 'block_all_page'),
            'inner_page' => array($this, 'block_inner_page'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "ReportBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        $context["this_page"] = "report_builder";
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_html_head($context, array $blocks = array())
    {
        // line 4
        echo $this->env->getExtension('deskpro_templating')->htmlGetAssetic("report_builder");
        echo "
";
    }

    // line 6
    public function block_page_js_exec($context, array $blocks = array())
    {
        // line 7
        echo "<script type=\"text/javascript\" >
    window._dpRbGroupParams = ";
        // line 8
        if (isset($context["reportGroupParams"])) { $_reportGroupParams_ = $context["reportGroupParams"]; } else { $_reportGroupParams_ = null; }
        echo twig_jsonencode_filter($_reportGroupParams_);
        echo ";

    function _page_exec() {
\t\twindow.DeskPRO_Page = new DeskPRO.Report.PageHandler.ReportBuilder();
\t\twindow.DeskPRO_Page.updateFavorites(";
        // line 12
        if (isset($context["favoritesJs"])) { $_favoritesJs_ = $context["favoritesJs"]; } else { $_favoritesJs_ = null; }
        echo twig_jsonencode_filter($_favoritesJs_);
        echo ");
\t}
</script>
";
    }

    // line 16
    public function block_nav_block($context, array $blocks = array())
    {
    }

    // line 18
    public function block_pagebar($context, array $blocks = array())
    {
    }

    // line 35
    public function block_all_page($context, array $blocks = array())
    {
        // line 36
        echo "<div id=\"report-container\">
\t<div id=\"report-list\" data-element-handler=\"DeskPRO.Report.ElementHandler.Builder.ReportList\"
\t\tdata-cookie=\"dp_report_list_width\"
\t\tdata-min=\"120\"
\t\tdata-max=\"900\"
\t>
\t\t<div id=\"report-container-inner\">
\t\t<div class=\"top-btn\">
\t\t\t<a rel=\"report-page-body\" href=\"";
        // line 44
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_builder_new"), "html", null, true);
        echo "\" class=\"btn\">Create Custom Report</a>
\t\t</div>

\t\t<div id=\"report-favorites\" ";
        // line 47
        if (isset($context["favoriteReports"])) { $_favoriteReports_ = $context["favoriteReports"]; } else { $_favoriteReports_ = null; }
        echo (($_favoriteReports_) ? ("") : ("style=\"display: none\""));
        echo ">
\t\t\t<h3>Favorite</h3>
\t\t\t<ul class=\"report-list-ungroupable\">
\t\t\t";
        // line 50
        if (isset($context["favoriteReports"])) { $_favoriteReports_ = $context["favoriteReports"]; } else { $_favoriteReports_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_favoriteReports_);
        foreach ($context['_seq'] as $context["_key"] => $context["fav"]) {
            // line 51
            echo "\t\t\t\t";
            if (isset($context["fav"])) { $_fav_ = $context["fav"]; } else { $_fav_ = null; }
            echo $this->getAttribute($this, "report_list_item", array(0 => $this->getAttribute($_fav_, "report_builder"), 1 => "printable", 2 => $this->getAttribute($_fav_, "params")), "method");
            echo "
\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['fav'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 53
        echo "\t\t\t</ul>
\t\t</div>

\t\t";
        // line 56
        if (isset($context["customReports"])) { $_customReports_ = $context["customReports"]; } else { $_customReports_ = null; }
        if ($_customReports_) {
            // line 57
            echo "\t\t<h3>Custom</h3>
\t\t<ul class=\"report-list-groupable\">
\t\t";
            // line 59
            if (isset($context["customReports"])) { $_customReports_ = $context["customReports"]; } else { $_customReports_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_customReports_);
            foreach ($context['_seq'] as $context["_key"] => $context["report"]) {
                // line 60
                echo "\t\t\t";
                if (isset($context["report"])) { $_report_ = $context["report"]; } else { $_report_ = null; }
                echo $this->getAttribute($this, "report_list_item", array(0 => $_report_, 1 => "raw"), "method");
                echo "
\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['report'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 62
            echo "\t\t</ul>
\t\t";
        }
        // line 64
        echo "
\t\t";
        // line 65
        if (isset($context["builtInReports"])) { $_builtInReports_ = $context["builtInReports"]; } else { $_builtInReports_ = null; }
        if ($_builtInReports_) {
            // line 66
            echo "\t\t<h3>Built-In</h3>
\t\t<ul>
\t\t\t";
            // line 68
            if (isset($context["builtInReports"])) { $_builtInReports_ = $context["builtInReports"]; } else { $_builtInReports_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_builtInReports_);
            foreach ($context['_seq'] as $context["categoryName"] => $context["reports"]) {
                // line 69
                echo "\t\t\t\t<li><h4 data-element-handler=\"DeskPRO.Report.ElementHandler.Builder.ListCollapse\"
\t\t\t\t\tdata-cookie=\"dp_report_collapsed\"
\t\t\t\t\t><span></span>";
                // line 71
                if (isset($context["categoryName"])) { $_categoryName_ = $context["categoryName"]; } else { $_categoryName_ = null; }
                echo twig_escape_filter($this->env, $_categoryName_, "html", null, true);
                echo "</h4>
\t\t\t\t\t<ul data-collapse-id=\"";
                // line 72
                if (isset($context["categoryName"])) { $_categoryName_ = $context["categoryName"]; } else { $_categoryName_ = null; }
                echo twig_escape_filter($this->env, $_categoryName_, "html", null, true);
                echo "\" class=\"report-list-groupable\">
\t\t\t\t\t";
                // line 73
                if (isset($context["reports"])) { $_reports_ = $context["reports"]; } else { $_reports_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($_reports_);
                foreach ($context['_seq'] as $context["_key"] => $context["report"]) {
                    // line 74
                    echo "\t\t\t\t\t\t";
                    if (isset($context["report"])) { $_report_ = $context["report"]; } else { $_report_ = null; }
                    echo $this->getAttribute($this, "report_list_item", array(0 => $_report_, 1 => "raw"), "method");
                    echo "
\t\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['report'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 76
                echo "\t\t\t\t\t</ul>
\t\t\t\t</li>
\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['categoryName'], $context['reports'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 79
            echo "\t\t</ul>
\t\t";
        }
        // line 81
        echo "\t\t</div>
\t</div>

\t<div id=\"report-page-body\">
\t\t<!--dp:report-page-body-->
\t\t<div id=\"report-page-body-inner\">
\t\t";
        // line 87
        $this->displayBlock('inner_page', $context, $blocks);
        // line 88
        echo "\t\t</div>
\t\t<!--/dp:report-page-body-->
\t</div>
\t<div id=\"report-failed-block\">
\t\t<div class=\"query-error\">
\t\t\tThe report failed to load. Please try again.
\t\t</div>
\t</div>
</div>
<div id=\"report-loading-block\">
\tLoading... <img src=\"";
        // line 98
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("images/spinners/loading-small.gif"), "html", null, true);
        echo "\" alt=\"\" />
</div>
";
    }

    // line 87
    public function block_inner_page($context, array $blocks = array())
    {
    }

    // line 20
    public function getreport_list_item($_report = null, $_titleType = null, $_params = null)
    {
        $context = $this->env->mergeGlobals(array(
            "report" => $_report,
            "titleType" => $_titleType,
            "params" => $_params,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 21
            echo "\t<li>
\t\t<a href=\"";
            // line 22
            if (isset($context["report"])) { $_report_ = $context["report"]; } else { $_report_ = null; }
            if (isset($context["params"])) { $_params_ = $context["params"]; } else { $_params_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_builder_favorite", array("report_builder_id" => $this->getAttribute($_report_, "id"), "token" => $this->env->getExtension('deskpro_templating')->securityToken("report_builder_favorite"), "params" => $_params_)), "html", null, true);
            // line 26
            echo "\"
\t\t   class=\"report-favorite-toggle report-favorite\"
\t\t   data-report-id=\"";
            // line 28
            if (isset($context["report"])) { $_report_ = $context["report"]; } else { $_report_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_report_, "id"), "html", null, true);
            echo "\"
\t\t   data-report-params=\"";
            // line 29
            if (isset($context["params"])) { $_params_ = $context["params"]; } else { $_params_ = null; }
            echo twig_escape_filter($this->env, $_params_, "html", null, true);
            echo "\"
\t\t   title=\"Toggle favorite status\"
\t\t>Toggle favorite status</a>
\t\t<a rel=\"report-page-body\" class=\"report-list-title\" href=\"";
            // line 32
            if (isset($context["report"])) { $_report_ = $context["report"]; } else { $_report_ = null; }
            if (isset($context["params"])) { $_params_ = $context["params"]; } else { $_params_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_builder_report", array("report_builder_id" => $this->getAttribute($_report_, "id"), "params" => $_params_)), "html", null, true);
            echo "\" title=\"";
            if (isset($context["report"])) { $_report_ = $context["report"]; } else { $_report_ = null; }
            if (isset($context["params"])) { $_params_ = $context["params"]; } else { $_params_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_report_, "getTitle", array(0 => "placeholder", 1 => $_params_), "method"), "html", null, true);
            echo "\">";
            if (isset($context["report"])) { $_report_ = $context["report"]; } else { $_report_ = null; }
            if (isset($context["titleType"])) { $_titleType_ = $context["titleType"]; } else { $_titleType_ = null; }
            if (isset($context["params"])) { $_params_ = $context["params"]; } else { $_params_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_report_, "getTitle", array(0 => $_titleType_, 1 => $_params_), "method"), "html", null, true);
            echo "</a>
\t</li>
";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    public function getTemplateName()
    {
        return "ReportBundle:ReportBuilder:layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  268 => 29,  252 => 21,  234 => 87,  213 => 87,  205 => 81,  178 => 73,  164 => 69,  135 => 60,  121 => 42,  86 => 27,  119 => 36,  93 => 27,  144 => 40,  129 => 44,  471 => 106,  462 => 105,  439 => 98,  431 => 95,  426 => 92,  420 => 91,  417 => 90,  382 => 86,  378 => 85,  372 => 84,  350 => 78,  335 => 73,  327 => 71,  303 => 64,  300 => 63,  296 => 62,  292 => 61,  281 => 58,  275 => 32,  264 => 53,  253 => 52,  220 => 45,  208 => 43,  194 => 41,  171 => 37,  166 => 36,  118 => 53,  105 => 21,  79 => 22,  22 => 104,  130 => 59,  85 => 23,  162 => 48,  143 => 54,  131 => 48,  127 => 26,  123 => 56,  120 => 35,  109 => 40,  43 => 8,  217 => 63,  211 => 59,  168 => 71,  165 => 64,  160 => 47,  134 => 38,  115 => 34,  110 => 32,  89 => 27,  73 => 18,  42 => 14,  447 => 156,  437 => 148,  429 => 142,  418 => 141,  407 => 140,  370 => 137,  364 => 134,  354 => 127,  341 => 119,  333 => 114,  306 => 96,  289 => 60,  276 => 81,  271 => 78,  265 => 75,  258 => 72,  255 => 22,  240 => 52,  214 => 175,  207 => 173,  193 => 76,  186 => 54,  173 => 72,  161 => 64,  158 => 62,  116 => 34,  111 => 32,  106 => 37,  84 => 22,  80 => 36,  122 => 33,  113 => 41,  76 => 23,  59 => 14,  21 => 2,  69 => 22,  65 => 16,  61 => 16,  53 => 11,  64 => 18,  51 => 12,  31 => 3,  153 => 61,  150 => 44,  133 => 41,  102 => 32,  87 => 29,  75 => 23,  67 => 16,  50 => 8,  38 => 4,  35 => 3,  30 => 1,  1374 => 443,  1370 => 441,  1365 => 438,  1350 => 436,  1345 => 433,  1330 => 431,  1315 => 429,  1312 => 428,  1305 => 427,  1302 => 426,  1298 => 424,  1295 => 423,  1283 => 422,  1276 => 421,  1268 => 420,  1265 => 419,  1247 => 418,  1236 => 417,  1218 => 416,  1211 => 414,  1208 => 413,  1201 => 411,  1198 => 410,  1180 => 409,  1177 => 408,  1168 => 404,  1164 => 403,  1158 => 399,  1151 => 397,  1129 => 393,  1112 => 392,  1101 => 390,  1096 => 389,  1092 => 387,  1087 => 386,  1084 => 385,  1066 => 382,  1058 => 381,  1041 => 380,  1035 => 376,  1030 => 373,  1015 => 371,  1010 => 368,  995 => 366,  981 => 365,  967 => 364,  953 => 363,  938 => 361,  935 => 360,  932 => 359,  928 => 357,  925 => 356,  913 => 355,  906 => 354,  898 => 353,  895 => 352,  877 => 351,  858 => 350,  839 => 349,  821 => 348,  810 => 347,  792 => 346,  782 => 344,  779 => 343,  772 => 341,  769 => 340,  751 => 339,  748 => 338,  742 => 337,  736 => 336,  726 => 331,  720 => 330,  714 => 329,  709 => 328,  703 => 324,  696 => 322,  682 => 321,  676 => 320,  654 => 316,  635 => 315,  629 => 314,  623 => 313,  620 => 312,  602 => 311,  591 => 309,  586 => 308,  582 => 306,  577 => 305,  574 => 304,  560 => 303,  554 => 302,  544 => 299,  540 => 297,  536 => 295,  532 => 294,  528 => 293,  524 => 292,  520 => 291,  516 => 290,  512 => 289,  508 => 288,  504 => 287,  500 => 286,  496 => 285,  490 => 284,  475 => 281,  469 => 280,  432 => 272,  329 => 209,  284 => 59,  267 => 54,  259 => 26,  238 => 155,  204 => 129,  175 => 53,  101 => 29,  95 => 25,  92 => 18,  81 => 25,  71 => 23,  46 => 9,  40 => 4,  32 => 9,  477 => 191,  434 => 181,  405 => 172,  331 => 160,  322 => 106,  311 => 154,  302 => 95,  266 => 145,  263 => 28,  245 => 58,  242 => 72,  196 => 138,  191 => 71,  188 => 63,  183 => 74,  99 => 49,  96 => 47,  56 => 14,  34 => 9,  24 => 4,  313 => 150,  310 => 66,  307 => 207,  304 => 147,  295 => 146,  291 => 145,  282 => 143,  279 => 185,  254 => 131,  249 => 130,  233 => 123,  226 => 120,  222 => 118,  218 => 116,  215 => 88,  203 => 172,  195 => 56,  187 => 111,  182 => 110,  159 => 68,  146 => 92,  128 => 37,  125 => 43,  63 => 18,  60 => 15,  44 => 6,  33 => 7,  399 => 79,  388 => 77,  380 => 138,  377 => 74,  373 => 72,  357 => 80,  346 => 76,  334 => 66,  316 => 67,  305 => 61,  298 => 60,  287 => 144,  251 => 51,  244 => 49,  241 => 124,  235 => 70,  231 => 45,  227 => 98,  219 => 140,  199 => 36,  192 => 35,  181 => 61,  174 => 32,  156 => 45,  145 => 62,  138 => 29,  126 => 57,  114 => 39,  91 => 24,  82 => 24,  68 => 12,  57 => 16,  48 => 11,  39 => 11,  36 => 10,  28 => 2,  23 => 1,  19 => 1,  436 => 273,  430 => 156,  428 => 155,  424 => 178,  421 => 152,  416 => 175,  410 => 159,  408 => 152,  401 => 151,  391 => 139,  387 => 171,  383 => 169,  379 => 144,  376 => 143,  371 => 221,  366 => 83,  361 => 82,  356 => 59,  349 => 219,  347 => 163,  344 => 142,  342 => 75,  336 => 137,  330 => 72,  318 => 208,  315 => 128,  308 => 126,  297 => 94,  290 => 148,  278 => 114,  272 => 55,  260 => 106,  257 => 105,  250 => 59,  239 => 20,  232 => 153,  221 => 177,  210 => 86,  201 => 79,  190 => 39,  184 => 82,  179 => 57,  167 => 52,  157 => 50,  154 => 33,  151 => 66,  149 => 64,  142 => 40,  140 => 43,  108 => 51,  103 => 50,  98 => 35,  94 => 28,  88 => 26,  83 => 26,  78 => 24,  74 => 23,  70 => 20,  66 => 16,  58 => 12,  54 => 13,  49 => 13,  45 => 6,  41 => 10,  37 => 7,  673 => 200,  669 => 198,  664 => 195,  649 => 193,  634 => 191,  631 => 190,  617 => 189,  603 => 188,  599 => 186,  587 => 184,  581 => 183,  569 => 181,  563 => 180,  551 => 178,  545 => 177,  533 => 175,  527 => 174,  515 => 172,  506 => 171,  503 => 170,  497 => 169,  494 => 168,  481 => 282,  473 => 164,  467 => 163,  463 => 279,  460 => 278,  457 => 187,  454 => 159,  450 => 102,  446 => 157,  442 => 99,  423 => 155,  419 => 154,  398 => 88,  392 => 152,  389 => 151,  386 => 150,  368 => 167,  363 => 148,  360 => 220,  343 => 146,  338 => 74,  328 => 141,  323 => 69,  317 => 103,  314 => 135,  280 => 57,  269 => 77,  262 => 74,  237 => 85,  230 => 122,  223 => 64,  216 => 38,  209 => 69,  202 => 65,  172 => 55,  169 => 55,  163 => 54,  155 => 66,  152 => 65,  147 => 48,  141 => 47,  136 => 38,  132 => 45,  112 => 33,  107 => 30,  104 => 38,  100 => 26,  90 => 44,  77 => 35,  72 => 18,  62 => 15,  55 => 17,  52 => 14,  47 => 7,  29 => 8,  27 => 1,);
    }
}
