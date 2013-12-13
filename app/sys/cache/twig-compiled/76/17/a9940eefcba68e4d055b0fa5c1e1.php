<?php

/* ReportBundle:Overview:index.html.twig */
class __TwigTemplate_7617a9940eefcba68e4d055b0fa5c1e1 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("ReportBundle::layout.html.twig");

        $this->blocks = array(
            'html_head' => array($this, 'block_html_head'),
            'nav_block' => array($this, 'block_nav_block'),
            'pagebar' => array($this, 'block_pagebar'),
            'page' => array($this, 'block_page'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "ReportBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        $context["this_page"] = "overview";
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 4
    public function block_html_head($context, array $blocks = array())
    {
        // line 5
        echo "\t<script type=\"text/javascript\" src=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("javascripts/DeskPRO/Report/ElementHandler/OverviewStat/StandardWithGroup.js"), "html", null, true);
        echo "\"></script>
";
    }

    // line 8
    public function block_nav_block($context, array $blocks = array())
    {
    }

    // line 10
    public function block_pagebar($context, array $blocks = array())
    {
    }

    // line 13
    public function block_page($context, array $blocks = array())
    {
        // line 14
        echo "\t<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\"><tr><td style=\"padding-right: 6px;\" width=\"50%\">

\t\t<div>
\t\t\t";
        // line 17
        if (isset($context["tickets_status_data"])) { $_tickets_status_data_ = $context["tickets_status_data"]; } else { $_tickets_status_data_ = null; }
        $this->env->loadTemplate("ReportBundle:Overview:tickets-status.html.twig")->display(array_merge($context, array("data" => $_tickets_status_data_, "initial_display" => true)));
        // line 18
        echo "\t\t</div>

\t\t<br />

\t\t<div data-element-handler=\"DeskPRO.Admin.ElementHandler.OverviewStat.StandardWithGroup\"\tdata-update-url=\"";
        // line 22
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_overview_update_stat", array("type" => "tickets_awaiting_agent")), "html", null, true);
        echo "\">
\t\t\t";
        // line 23
        if (isset($context["tickets_awaiting_agent_data"])) { $_tickets_awaiting_agent_data_ = $context["tickets_awaiting_agent_data"]; } else { $_tickets_awaiting_agent_data_ = null; }
        $this->env->loadTemplate("ReportBundle:Overview:tickets-awaiting-agent.html.twig")->display(array_merge($context, array("data" => $_tickets_awaiting_agent_data_, "initial_display" => true)));
        // line 24
        echo "\t\t</div>

\t\t<br />

\t\t<div data-element-handler=\"DeskPRO.Admin.ElementHandler.OverviewStat.StandardWithGroup\"\tdata-update-url=\"";
        // line 28
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_overview_update_stat", array("type" => "tickets_user_waiting_time")), "html", null, true);
        echo "\">
\t\t\t";
        // line 29
        if (isset($context["tickets_user_waiting_time_data"])) { $_tickets_user_waiting_time_data_ = $context["tickets_user_waiting_time_data"]; } else { $_tickets_user_waiting_time_data_ = null; }
        $this->env->loadTemplate("ReportBundle:Overview:tickets-user-waiting-time.html.twig")->display(array_merge($context, array("data" => $_tickets_user_waiting_time_data_, "initial_display" => true)));
        // line 30
        echo "\t\t</div>

\t</td><td style=\"padding-left: 6px\" width=\"50%\">

\t\t<div data-element-handler=\"DeskPRO.Admin.ElementHandler.OverviewStat.StandardWithGroup\"\tdata-update-url=\"";
        // line 34
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_overview_update_stat", array("type" => "tickets_resolved")), "html", null, true);
        echo "\">
\t\t\t";
        // line 35
        if (isset($context["tickets_resolved_data"])) { $_tickets_resolved_data_ = $context["tickets_resolved_data"]; } else { $_tickets_resolved_data_ = null; }
        $this->env->loadTemplate("ReportBundle:Overview:tickets-resolved.html.twig")->display(array_merge($context, array("data" => $_tickets_resolved_data_, "initial_display" => true)));
        // line 36
        echo "\t\t</div>

\t\t<br />

\t\t<div data-element-handler=\"DeskPRO.Admin.ElementHandler.OverviewStat.StandardWithGroup\"\tdata-update-url=\"";
        // line 40
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_overview_update_stat", array("type" => "tickets_response_time")), "html", null, true);
        echo "\">
\t\t\t";
        // line 41
        if (isset($context["tickets_response_time_data"])) { $_tickets_response_time_data_ = $context["tickets_response_time_data"]; } else { $_tickets_response_time_data_ = null; }
        $this->env->loadTemplate("ReportBundle:Overview:tickets-response-time.html.twig")->display(array_merge($context, array("data" => $_tickets_response_time_data_, "initial_display" => true)));
        // line 42
        echo "\t\t</div>

\t\t";
        // line 44
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (twig_length_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "Sla"), "method"), "getAllSlas", array(), "method"))) {
            // line 45
            echo "\t\t\t<br />

\t\t\t<div data-element-handler=\"DeskPRO.Admin.ElementHandler.OverviewStat.StandardWithGroup\"\tdata-update-url=\"";
            // line 47
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_overview_update_stat", array("type" => "tickets_sla_status")), "html", null, true);
            echo "\">
\t\t\t\t";
            // line 48
            if (isset($context["tickets_sla_status"])) { $_tickets_sla_status_ = $context["tickets_sla_status"]; } else { $_tickets_sla_status_ = null; }
            $this->env->loadTemplate("ReportBundle:Overview:tickets-sla-status.html.twig")->display(array_merge($context, array("data" => $_tickets_sla_status_, "initial_display" => true)));
            // line 49
            echo "\t\t\t</div>
\t\t";
        }
        // line 51
        echo "
\t\t<br />

\t\t<div data-element-handler=\"DeskPRO.Admin.ElementHandler.OverviewStat.StandardWithGroup\"\tdata-update-url=\"";
        // line 54
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_overview_update_stat", array("type" => "chats_created")), "html", null, true);
        echo "\">
\t\t\t";
        // line 55
        if (isset($context["chats_created_data"])) { $_chats_created_data_ = $context["chats_created_data"]; } else { $_chats_created_data_ = null; }
        $this->env->loadTemplate("ReportBundle:Overview:chats-created.html.twig")->display(array_merge($context, array("data" => $_chats_created_data_, "initial_display" => true)));
        // line 56
        echo "\t\t</div>

\t</td></tr></table>

\t<br />

\t<div data-element-handler=\"DeskPRO.Admin.ElementHandler.OverviewStat.StandardWithGroup\"\tdata-update-url=\"";
        // line 62
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_overview_update_stat", array("type" => "tickets_opened_hour")), "html", null, true);
        echo "\">
\t\t";
        // line 63
        if (isset($context["tickets_opened_hour_data"])) { $_tickets_opened_hour_data_ = $context["tickets_opened_hour_data"]; } else { $_tickets_opened_hour_data_ = null; }
        $this->env->loadTemplate("ReportBundle:Overview:tickets-opened-hour.html.twig")->display(array_merge($context, array("data" => $_tickets_opened_hour_data_, "initial_display" => true)));
        // line 64
        echo "\t</div>

\t";
    }

    public function getTemplateName()
    {
        return "ReportBundle:Overview:index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  162 => 63,  143 => 54,  131 => 48,  127 => 47,  123 => 45,  120 => 44,  109 => 40,  43 => 8,  217 => 63,  211 => 59,  168 => 48,  165 => 64,  160 => 46,  134 => 49,  115 => 34,  110 => 33,  89 => 30,  73 => 26,  42 => 14,  447 => 156,  437 => 148,  429 => 142,  418 => 141,  407 => 140,  370 => 137,  364 => 134,  354 => 127,  341 => 119,  333 => 114,  306 => 96,  289 => 90,  276 => 81,  271 => 78,  265 => 75,  258 => 72,  255 => 71,  240 => 52,  214 => 175,  207 => 173,  193 => 164,  186 => 54,  173 => 66,  161 => 64,  158 => 62,  116 => 42,  111 => 34,  106 => 33,  84 => 26,  80 => 27,  122 => 50,  113 => 41,  76 => 24,  59 => 19,  21 => 2,  69 => 19,  65 => 18,  61 => 17,  53 => 13,  64 => 18,  51 => 15,  31 => 5,  153 => 61,  150 => 56,  133 => 46,  102 => 32,  87 => 29,  75 => 19,  67 => 21,  50 => 8,  38 => 8,  35 => 12,  30 => 2,  1374 => 443,  1370 => 441,  1365 => 438,  1350 => 436,  1345 => 433,  1330 => 431,  1315 => 429,  1312 => 428,  1305 => 427,  1302 => 426,  1298 => 424,  1295 => 423,  1283 => 422,  1276 => 421,  1268 => 420,  1265 => 419,  1247 => 418,  1236 => 417,  1218 => 416,  1211 => 414,  1208 => 413,  1201 => 411,  1198 => 410,  1180 => 409,  1177 => 408,  1168 => 404,  1164 => 403,  1158 => 399,  1151 => 397,  1129 => 393,  1112 => 392,  1101 => 390,  1096 => 389,  1092 => 387,  1087 => 386,  1084 => 385,  1066 => 382,  1058 => 381,  1041 => 380,  1035 => 376,  1030 => 373,  1015 => 371,  1010 => 368,  995 => 366,  981 => 365,  967 => 364,  953 => 363,  938 => 361,  935 => 360,  932 => 359,  928 => 357,  925 => 356,  913 => 355,  906 => 354,  898 => 353,  895 => 352,  877 => 351,  858 => 350,  839 => 349,  821 => 348,  810 => 347,  792 => 346,  782 => 344,  779 => 343,  772 => 341,  769 => 340,  751 => 339,  748 => 338,  742 => 337,  736 => 336,  726 => 331,  720 => 330,  714 => 329,  709 => 328,  703 => 324,  696 => 322,  682 => 321,  676 => 320,  654 => 316,  635 => 315,  629 => 314,  623 => 313,  620 => 312,  602 => 311,  591 => 309,  586 => 308,  582 => 306,  577 => 305,  574 => 304,  560 => 303,  554 => 302,  544 => 299,  540 => 297,  536 => 295,  532 => 294,  528 => 293,  524 => 292,  520 => 291,  516 => 290,  512 => 289,  508 => 288,  504 => 287,  500 => 286,  496 => 285,  490 => 284,  475 => 281,  469 => 280,  432 => 272,  329 => 209,  284 => 187,  267 => 175,  259 => 171,  238 => 155,  204 => 129,  175 => 51,  101 => 34,  95 => 32,  92 => 28,  81 => 24,  71 => 22,  46 => 9,  40 => 4,  32 => 9,  477 => 191,  434 => 181,  405 => 172,  331 => 160,  322 => 106,  311 => 154,  302 => 95,  266 => 145,  263 => 144,  245 => 58,  242 => 72,  196 => 138,  191 => 71,  188 => 134,  183 => 53,  99 => 49,  96 => 34,  56 => 14,  34 => 4,  24 => 4,  313 => 150,  310 => 149,  307 => 207,  304 => 147,  295 => 146,  291 => 145,  282 => 143,  279 => 185,  254 => 131,  249 => 130,  233 => 123,  226 => 120,  222 => 118,  218 => 116,  215 => 115,  203 => 172,  195 => 56,  187 => 111,  182 => 110,  159 => 97,  146 => 92,  128 => 83,  125 => 82,  63 => 20,  60 => 21,  44 => 5,  33 => 4,  399 => 79,  388 => 77,  380 => 138,  377 => 74,  373 => 72,  357 => 69,  346 => 68,  334 => 66,  316 => 63,  305 => 61,  298 => 60,  287 => 144,  251 => 51,  244 => 49,  241 => 124,  235 => 70,  231 => 45,  227 => 66,  219 => 140,  199 => 36,  192 => 35,  181 => 33,  174 => 32,  156 => 29,  145 => 58,  138 => 51,  126 => 57,  114 => 47,  91 => 34,  82 => 16,  68 => 14,  57 => 16,  48 => 10,  39 => 9,  36 => 5,  28 => 1,  23 => 1,  19 => 1,  436 => 273,  430 => 156,  428 => 155,  424 => 178,  421 => 152,  416 => 175,  410 => 159,  408 => 152,  401 => 151,  391 => 139,  387 => 171,  383 => 169,  379 => 144,  376 => 143,  371 => 221,  366 => 71,  361 => 65,  356 => 59,  349 => 219,  347 => 163,  344 => 142,  342 => 141,  336 => 137,  330 => 134,  318 => 208,  315 => 128,  308 => 126,  297 => 94,  290 => 148,  278 => 114,  272 => 147,  260 => 106,  257 => 105,  250 => 59,  239 => 98,  232 => 153,  221 => 177,  210 => 86,  201 => 57,  190 => 84,  184 => 82,  179 => 52,  167 => 75,  157 => 68,  154 => 96,  151 => 66,  149 => 43,  142 => 42,  140 => 59,  108 => 30,  103 => 36,  98 => 36,  94 => 35,  88 => 27,  83 => 28,  78 => 20,  74 => 23,  70 => 22,  66 => 27,  58 => 26,  54 => 14,  49 => 14,  45 => 12,  41 => 8,  37 => 7,  673 => 200,  669 => 198,  664 => 195,  649 => 193,  634 => 191,  631 => 190,  617 => 189,  603 => 188,  599 => 186,  587 => 184,  581 => 183,  569 => 181,  563 => 180,  551 => 178,  545 => 177,  533 => 175,  527 => 174,  515 => 172,  506 => 171,  503 => 170,  497 => 169,  494 => 168,  481 => 282,  473 => 164,  467 => 163,  463 => 279,  460 => 278,  457 => 187,  454 => 159,  450 => 158,  446 => 157,  442 => 277,  423 => 155,  419 => 154,  398 => 150,  392 => 152,  389 => 151,  386 => 150,  368 => 167,  363 => 148,  360 => 220,  343 => 146,  338 => 143,  328 => 141,  323 => 64,  317 => 103,  314 => 135,  280 => 57,  269 => 77,  262 => 74,  237 => 85,  230 => 122,  223 => 64,  216 => 38,  209 => 69,  202 => 65,  172 => 37,  169 => 36,  163 => 30,  155 => 45,  152 => 44,  147 => 55,  141 => 28,  136 => 52,  132 => 85,  112 => 46,  107 => 20,  104 => 38,  100 => 35,  90 => 30,  77 => 24,  72 => 31,  62 => 15,  55 => 17,  52 => 9,  47 => 13,  29 => 1,  27 => 1,);
    }
}
