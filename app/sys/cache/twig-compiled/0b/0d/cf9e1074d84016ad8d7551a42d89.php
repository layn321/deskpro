<?php

/* AdminBundle:Languages:lang-layout.html.twig */
class __TwigTemplate_0b0dcf9e1074d84016ad8d7551a42d89 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("AdminBundle::layout.html.twig");

        $this->blocks = array(
            'page_nav' => array($this, 'block_page_nav'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "AdminBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 2
        $context["page_nav_fixed"] = 1;
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_page_nav($context, array $blocks = array())
    {
        // line 4
        echo "<div data-element-handler=\"DeskPRO.Admin.ElementHandler.PortalNav\" id=\"portal_nav\" class=\"fixed\">
\t<section class=\"top\">
\t\t<header>
\t\t\t<h4>";
        // line 7
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.language");
        echo "</h4>
\t\t</header>
\t\t<article>
\t\t\t<ul>
\t\t\t\t<li class=\"";
        // line 11
        if (isset($context["this_page"])) { $_this_page_ = $context["this_page"]; } else { $_this_page_ = null; }
        if (($_this_page_ == "edit_lang")) {
            echo "on";
        }
        echo "\"><a href=\"";
        if (isset($context["language"])) { $_language_ = $context["language"]; } else { $_language_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_langs_editlang", array("language_id" => $this->getAttribute($_language_, "id"))), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.languages.edit_details");
        echo "</a></li>
\t\t\t\t<li class=\"";
        // line 12
        if (isset($context["this_page"])) { $_this_page_ = $context["this_page"]; } else { $_this_page_ = null; }
        if (($_this_page_ == "phrase_group_CUSTOM")) {
            echo "on";
        }
        echo "\">
\t\t\t\t\t<a href=\"";
        // line 13
        if (isset($context["language"])) { $_language_ = $context["language"]; } else { $_language_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_langs_editphrases", array("language_id" => $this->getAttribute($_language_, "id"), "group" => "CUSTOM")), "html", null, true);
        echo "\">Custom Phrases</a>
\t\t\t\t</li>
\t\t\t</ul>
\t\t</article>
\t</section>

\t<section class=\"top\">
\t\t<header>
\t\t\t<h4>Translate Resources</h4>
\t\t</header>
\t\t<article>
\t\t\t<ul>
\t\t\t\t<li class=\"";
        // line 25
        if (isset($context["this_page"])) { $_this_page_ = $context["this_page"]; } else { $_this_page_ = null; }
        if (($_this_page_ == "tickets_departments")) {
            echo "on";
        }
        echo "\">
\t\t\t\t\t<a href=\"";
        // line 26
        if (isset($context["language"])) { $_language_ = $context["language"]; } else { $_language_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_langs_departments", array("language_id" => $this->getAttribute($_language_, "id"), "type" => "tickets")), "html", null, true);
        echo "\">Ticket Departments</a>
\t\t\t\t</li>
\t\t\t\t<li class=\"";
        // line 28
        if (isset($context["this_page"])) { $_this_page_ = $context["this_page"]; } else { $_this_page_ = null; }
        if (($_this_page_ == "chat_departments")) {
            echo "on";
        }
        echo "\">
\t\t\t\t\t<a href=\"";
        // line 29
        if (isset($context["language"])) { $_language_ = $context["language"]; } else { $_language_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_langs_departments", array("language_id" => $this->getAttribute($_language_, "id"), "type" => "chat")), "html", null, true);
        echo "\">Chat Departments</a>
\t\t\t\t</li>
\t\t\t\t";
        // line 31
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_product"), "method")) {
            // line 32
            echo "\t\t\t\t\t<li class=\"";
            if (isset($context["this_page"])) { $_this_page_ = $context["this_page"]; } else { $_this_page_ = null; }
            if (($_this_page_ == "products")) {
                echo "on";
            }
            echo "\">
\t\t\t\t\t\t<a href=\"";
            // line 33
            if (isset($context["language"])) { $_language_ = $context["language"]; } else { $_language_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_langs_products", array("language_id" => $this->getAttribute($_language_, "id"))), "html", null, true);
            echo "\">Products</a>
\t\t\t\t\t</li>
\t\t\t\t";
        }
        // line 36
        echo "\t\t\t\t";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_ticket_category"), "method")) {
            // line 37
            echo "\t\t\t\t\t<li class=\"";
            if (isset($context["this_page"])) { $_this_page_ = $context["this_page"]; } else { $_this_page_ = null; }
            if (($_this_page_ == "ticketpriorities")) {
                echo "on";
            }
            echo "\">
\t\t\t\t\t\t<a href=\"";
            // line 38
            if (isset($context["language"])) { $_language_ = $context["language"]; } else { $_language_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_langs_ticketpriorities", array("language_id" => $this->getAttribute($_language_, "id"))), "html", null, true);
            echo "\">Ticket Categories</a>
\t\t\t\t\t</li>
\t\t\t\t";
        }
        // line 41
        echo "\t\t\t\t";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_ticket_priority"), "method")) {
            // line 42
            echo "\t\t\t\t\t<li class=\"";
            if (isset($context["this_page"])) { $_this_page_ = $context["this_page"]; } else { $_this_page_ = null; }
            if (($_this_page_ == "ticketpriorities")) {
                echo "on";
            }
            echo "\">
\t\t\t\t\t\t<a href=\"";
            // line 43
            if (isset($context["language"])) { $_language_ = $context["language"]; } else { $_language_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_langs_ticketpriorities", array("language_id" => $this->getAttribute($_language_, "id"))), "html", null, true);
            echo "\">Ticket Priorities</a>
\t\t\t\t\t</li>
\t\t\t\t";
        }
        // line 46
        echo "\t\t\t\t";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_ticket_workflow"), "method")) {
            // line 47
            echo "\t\t\t\t\t<li class=\"";
            if (isset($context["this_page"])) { $_this_page_ = $context["this_page"]; } else { $_this_page_ = null; }
            if (($_this_page_ == "ticketworkflows")) {
                echo "on";
            }
            echo "\">
\t\t\t\t\t\t<a href=\"";
            // line 48
            if (isset($context["language"])) { $_language_ = $context["language"]; } else { $_language_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_langs_ticketworkflows", array("language_id" => $this->getAttribute($_language_, "id"))), "html", null, true);
            echo "\">Ticket Workflows</a>
\t\t\t\t\t</li>
\t\t\t\t";
        }
        // line 51
        echo "\t\t\t\t<li>
\t\t\t\t\t<a href=\"";
        // line 52
        if (isset($context["language"])) { $_language_ = $context["language"]; } else { $_language_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_langs_feedback", array("language_id" => $this->getAttribute($_language_, "id"))), "html", null, true);
        echo "\">Feedback</a>
\t\t\t\t</li>
\t\t\t\t<li>
\t\t\t\t\t<a href=\"";
        // line 55
        if (isset($context["language"])) { $_language_ = $context["language"]; } else { $_language_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_langs_kbcats", array("language_id" => $this->getAttribute($_language_, "id"))), "html", null, true);
        echo "\">Knowledgebase Categories</a>
\t\t\t\t</li>
\t\t\t\t<li>
\t\t\t\t\t<a href=\"";
        // line 58
        if (isset($context["language"])) { $_language_ = $context["language"]; } else { $_language_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_langs_customfields", array("language_id" => $this->getAttribute($_language_, "id"), "field_type" => "tickets")), "html", null, true);
        echo "\">Ticket Fields</a>
\t\t\t\t</li>
\t\t\t\t<li>
\t\t\t\t\t<a href=\"";
        // line 61
        if (isset($context["language"])) { $_language_ = $context["language"]; } else { $_language_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_langs_customfields", array("language_id" => $this->getAttribute($_language_, "id"), "field_type" => "people")), "html", null, true);
        echo "\">Person Fields</a>
\t\t\t\t</li>
\t\t\t\t<li>
\t\t\t\t\t<a href=\"";
        // line 64
        if (isset($context["language"])) { $_language_ = $context["language"]; } else { $_language_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_langs_customfields", array("language_id" => $this->getAttribute($_language_, "id"), "field_type" => "organizations")), "html", null, true);
        echo "\">Organization Fields</a>
\t\t\t\t</li>
\t\t\t</ul>
\t\t</article>
\t</section>

\t";
        // line 70
        if (isset($context["phrase_groups"])) { $_phrase_groups_ = $context["phrase_groups"]; } else { $_phrase_groups_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable(array("user" => $this->getAttribute($_phrase_groups_, "user", array(), "array")));
        foreach ($context['_seq'] as $context["group"] => $context["subgroups"]) {
            // line 71
            echo "\t\t<section class=\"top\">
\t\t\t<header>
\t\t\t\t<h4>";
            // line 73
            if (isset($context["group"])) { $_group_ = $context["group"]; } else { $_group_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, ("admin.languages.phrasegroup_" . $_group_));
            echo "</h4>
\t\t\t</header>
\t\t\t<article>
\t\t\t\t<ul>
\t\t\t\t\t";
            // line 77
            if (isset($context["subgroups"])) { $_subgroups_ = $context["subgroups"]; } else { $_subgroups_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_subgroups_);
            foreach ($context['_seq'] as $context["_key"] => $context["subgroup"]) {
                if (isset($context["subgroup"])) { $_subgroup_ = $context["subgroup"]; } else { $_subgroup_ = null; }
                if (($_subgroup_ != "lang")) {
                    // line 78
                    echo "\t\t\t\t\t<li class=\"";
                    if (isset($context["this_page"])) { $_this_page_ = $context["this_page"]; } else { $_this_page_ = null; }
                    if (isset($context["group"])) { $_group_ = $context["group"]; } else { $_group_ = null; }
                    if (isset($context["subgroup"])) { $_subgroup_ = $context["subgroup"]; } else { $_subgroup_ = null; }
                    if (($_this_page_ == ((("phrase_group_" . $_group_) . "_") . $_subgroup_))) {
                        echo "on";
                    }
                    echo "\">
\t\t\t\t\t\t<a href=\"";
                    // line 79
                    if (isset($context["language"])) { $_language_ = $context["language"]; } else { $_language_ = null; }
                    if (isset($context["group"])) { $_group_ = $context["group"]; } else { $_group_ = null; }
                    if (isset($context["subgroup"])) { $_subgroup_ = $context["subgroup"]; } else { $_subgroup_ = null; }
                    echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_langs_editphrases", array("language_id" => $this->getAttribute($_language_, "id"), "group" => (($_group_ . ".") . $_subgroup_))), "html", null, true);
                    echo "\">";
                    if (isset($context["group"])) { $_group_ = $context["group"]; } else { $_group_ = null; }
                    if (isset($context["subgroup"])) { $_subgroup_ = $context["subgroup"]; } else { $_subgroup_ = null; }
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, ((("admin.languages.phrasegroup_" . $_group_) . "_") . $_subgroup_));
                    echo "</a>
\t\t\t\t\t</li>
\t\t\t\t\t";
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subgroup'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 82
            echo "\t\t\t\t</ul>
\t\t\t</article>
\t\t</section>
\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['group'], $context['subgroups'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 86
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "AdminBundle:Languages:lang-layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  20 => 1,  659 => 274,  562 => 245,  548 => 238,  558 => 94,  479 => 82,  589 => 101,  457 => 211,  413 => 150,  953 => 406,  948 => 403,  935 => 394,  929 => 391,  916 => 382,  864 => 365,  844 => 348,  816 => 342,  807 => 339,  801 => 338,  774 => 337,  766 => 328,  737 => 314,  685 => 300,  664 => 294,  635 => 288,  593 => 269,  546 => 91,  532 => 231,  865 => 221,  852 => 216,  838 => 208,  820 => 201,  781 => 333,  764 => 178,  725 => 164,  632 => 141,  602 => 105,  565 => 117,  529 => 111,  505 => 207,  487 => 104,  473 => 221,  1853 => 842,  1849 => 841,  1839 => 837,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 813,  1778 => 812,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 782,  1705 => 781,  1688 => 770,  1681 => 769,  1677 => 768,  1667 => 764,  1656 => 762,  1648 => 757,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 714,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 695,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 651,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 610,  1289 => 607,  1276 => 604,  1263 => 601,  1244 => 596,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 565,  1181 => 561,  1171 => 557,  1158 => 550,  1114 => 530,  1104 => 526,  1072 => 512,  1062 => 508,  1024 => 494,  1014 => 490,  1000 => 482,  990 => 478,  980 => 474,  960 => 466,  918 => 448,  888 => 376,  834 => 346,  673 => 342,  636 => 323,  462 => 209,  454 => 192,  1144 => 542,  1139 => 399,  1131 => 399,  1127 => 397,  1110 => 396,  1092 => 391,  1089 => 390,  1086 => 520,  1084 => 388,  1063 => 387,  1060 => 386,  1055 => 385,  1050 => 384,  1035 => 372,  1019 => 267,  1003 => 263,  959 => 251,  900 => 377,  880 => 434,  870 => 430,  867 => 366,  859 => 362,  848 => 215,  839 => 197,  828 => 191,  823 => 188,  809 => 181,  800 => 178,  797 => 177,  794 => 176,  786 => 174,  740 => 162,  734 => 313,  703 => 354,  693 => 350,  630 => 412,  626 => 140,  614 => 138,  610 => 385,  581 => 124,  564 => 229,  525 => 236,  722 => 162,  697 => 256,  674 => 249,  671 => 425,  577 => 261,  569 => 248,  557 => 368,  502 => 12,  497 => 11,  445 => 205,  729 => 159,  684 => 261,  676 => 297,  669 => 254,  660 => 145,  647 => 243,  643 => 244,  601 => 175,  570 => 211,  522 => 200,  501 => 148,  296 => 104,  374 => 119,  631 => 111,  616 => 281,  608 => 137,  605 => 16,  596 => 15,  574 => 165,  561 => 209,  527 => 147,  433 => 93,  388 => 196,  426 => 177,  383 => 62,  461 => 18,  370 => 134,  395 => 166,  294 => 172,  223 => 97,  220 => 79,  492 => 210,  468 => 21,  444 => 131,  410 => 229,  397 => 117,  377 => 226,  262 => 126,  250 => 147,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 2108,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 1383,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 1303,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 1173,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 1041,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 970,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 538,  1126 => 916,  1106 => 900,  1100 => 897,  1026 => 841,  997 => 822,  983 => 816,  975 => 402,  956 => 800,  939 => 786,  902 => 753,  894 => 749,  879 => 373,  757 => 631,  727 => 608,  716 => 605,  670 => 296,  528 => 221,  476 => 140,  435 => 195,  354 => 172,  341 => 162,  192 => 79,  321 => 147,  243 => 97,  793 => 350,  780 => 348,  758 => 177,  700 => 154,  686 => 150,  652 => 274,  638 => 414,  620 => 139,  545 => 237,  523 => 110,  494 => 10,  459 => 99,  438 => 172,  351 => 131,  347 => 104,  402 => 168,  268 => 103,  430 => 201,  411 => 188,  379 => 158,  322 => 183,  315 => 110,  289 => 171,  284 => 128,  255 => 115,  234 => 96,  1133 => 400,  1124 => 534,  1121 => 56,  1116 => 55,  1113 => 403,  1108 => 51,  1103 => 394,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 500,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 329,  996 => 262,  989 => 316,  985 => 315,  981 => 257,  977 => 313,  970 => 91,  966 => 416,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 244,  919 => 290,  917 => 289,  908 => 444,  905 => 378,  896 => 280,  891 => 227,  877 => 270,  862 => 267,  857 => 265,  837 => 347,  832 => 250,  827 => 248,  821 => 247,  803 => 179,  778 => 389,  769 => 165,  765 => 386,  753 => 328,  746 => 175,  743 => 318,  735 => 170,  730 => 218,  720 => 363,  717 => 362,  712 => 156,  691 => 204,  678 => 149,  654 => 144,  587 => 14,  576 => 179,  539 => 241,  517 => 217,  471 => 190,  441 => 203,  437 => 202,  418 => 175,  386 => 195,  373 => 144,  304 => 151,  270 => 123,  265 => 161,  229 => 81,  477 => 138,  455 => 325,  448 => 164,  429 => 179,  407 => 119,  399 => 156,  389 => 176,  375 => 171,  358 => 220,  349 => 118,  335 => 128,  327 => 132,  298 => 144,  280 => 118,  249 => 94,  194 => 80,  142 => 37,  344 => 129,  318 => 181,  306 => 122,  295 => 124,  357 => 119,  300 => 96,  286 => 80,  276 => 87,  269 => 109,  254 => 97,  128 => 37,  237 => 83,  165 => 70,  122 => 66,  798 => 337,  770 => 179,  759 => 112,  748 => 337,  731 => 108,  721 => 305,  718 => 307,  708 => 295,  696 => 147,  617 => 107,  590 => 226,  553 => 225,  550 => 156,  540 => 84,  533 => 82,  500 => 206,  493 => 225,  489 => 343,  482 => 223,  467 => 210,  464 => 215,  458 => 193,  452 => 117,  449 => 132,  415 => 201,  382 => 172,  372 => 215,  361 => 141,  356 => 58,  339 => 139,  302 => 146,  285 => 113,  258 => 115,  123 => 50,  108 => 28,  424 => 198,  394 => 86,  380 => 121,  338 => 155,  319 => 146,  316 => 53,  312 => 87,  290 => 146,  267 => 119,  206 => 93,  110 => 16,  240 => 123,  224 => 80,  219 => 61,  217 => 100,  202 => 73,  186 => 68,  170 => 58,  100 => 29,  67 => 16,  14 => 1,  1096 => 524,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 836,  1013 => 275,  1007 => 274,  1002 => 823,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 402,  940 => 298,  937 => 395,  928 => 452,  926 => 243,  915 => 239,  912 => 236,  903 => 231,  898 => 440,  892 => 229,  889 => 748,  887 => 227,  884 => 374,  876 => 222,  874 => 215,  871 => 368,  863 => 214,  861 => 213,  858 => 212,  850 => 352,  843 => 206,  840 => 406,  815 => 245,  812 => 244,  808 => 199,  804 => 395,  799 => 196,  791 => 193,  785 => 238,  775 => 184,  771 => 183,  754 => 340,  728 => 309,  726 => 169,  723 => 168,  715 => 105,  711 => 152,  709 => 162,  706 => 155,  698 => 208,  694 => 153,  692 => 155,  689 => 254,  681 => 150,  677 => 149,  675 => 148,  663 => 276,  661 => 277,  650 => 246,  646 => 143,  629 => 129,  627 => 21,  625 => 236,  622 => 270,  598 => 174,  592 => 117,  586 => 170,  575 => 214,  566 => 241,  556 => 157,  554 => 240,  541 => 222,  536 => 225,  515 => 209,  511 => 108,  509 => 17,  488 => 208,  486 => 207,  483 => 341,  465 => 196,  463 => 216,  450 => 202,  432 => 211,  419 => 155,  371 => 183,  362 => 221,  353 => 219,  337 => 18,  333 => 134,  309 => 94,  303 => 177,  299 => 122,  291 => 92,  272 => 122,  261 => 87,  253 => 104,  239 => 77,  235 => 104,  213 => 91,  200 => 91,  198 => 58,  159 => 64,  149 => 74,  146 => 75,  131 => 47,  116 => 41,  79 => 17,  74 => 46,  71 => 15,  836 => 320,  817 => 398,  814 => 200,  811 => 317,  805 => 313,  787 => 657,  779 => 169,  776 => 305,  773 => 347,  761 => 296,  751 => 163,  747 => 325,  742 => 336,  739 => 171,  736 => 317,  724 => 214,  705 => 278,  702 => 601,  688 => 113,  680 => 278,  667 => 295,  662 => 146,  656 => 418,  649 => 291,  644 => 97,  641 => 241,  624 => 109,  613 => 106,  607 => 232,  597 => 221,  591 => 131,  584 => 259,  579 => 234,  563 => 162,  559 => 237,  551 => 235,  547 => 114,  537 => 90,  524 => 220,  512 => 227,  507 => 76,  504 => 213,  498 => 142,  485 => 230,  480 => 28,  472 => 139,  466 => 217,  460 => 215,  447 => 188,  442 => 185,  434 => 212,  428 => 11,  422 => 176,  404 => 149,  368 => 164,  364 => 133,  340 => 155,  334 => 157,  330 => 148,  325 => 126,  292 => 142,  287 => 131,  282 => 86,  279 => 147,  273 => 82,  266 => 88,  256 => 79,  252 => 119,  228 => 105,  218 => 32,  201 => 79,  64 => 13,  51 => 9,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 1705,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 737,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 731,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 725,  1559 => 601,  1552 => 720,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 708,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 645,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 624,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 1001,  1257 => 1000,  1254 => 479,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 569,  1196 => 958,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 938,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 388,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 898,  1099 => 393,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 880,  1076 => 359,  1070 => 875,  1057 => 352,  1052 => 504,  1045 => 382,  1040 => 377,  1036 => 283,  1032 => 496,  1028 => 274,  1023 => 393,  1020 => 392,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 821,  979 => 379,  974 => 256,  967 => 373,  962 => 803,  958 => 801,  954 => 253,  950 => 368,  945 => 367,  942 => 460,  938 => 365,  934 => 364,  927 => 361,  923 => 387,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 272,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 410,  841 => 210,  835 => 312,  830 => 249,  826 => 202,  822 => 308,  818 => 246,  813 => 183,  810 => 340,  806 => 180,  802 => 198,  795 => 336,  792 => 239,  789 => 349,  784 => 188,  782 => 187,  777 => 291,  772 => 289,  768 => 334,  763 => 327,  760 => 329,  756 => 230,  752 => 176,  745 => 281,  741 => 280,  738 => 379,  732 => 171,  719 => 279,  714 => 264,  710 => 310,  704 => 267,  699 => 303,  695 => 285,  690 => 263,  687 => 279,  683 => 346,  679 => 298,  672 => 255,  668 => 247,  665 => 201,  658 => 244,  645 => 270,  640 => 142,  634 => 413,  628 => 271,  623 => 238,  619 => 282,  611 => 268,  606 => 279,  603 => 267,  599 => 242,  595 => 132,  583 => 169,  580 => 100,  573 => 260,  560 => 101,  543 => 172,  538 => 232,  534 => 281,  530 => 202,  526 => 213,  521 => 146,  518 => 22,  514 => 183,  510 => 202,  503 => 75,  496 => 345,  490 => 83,  484 => 143,  474 => 25,  470 => 168,  446 => 318,  440 => 114,  436 => 113,  431 => 12,  425 => 193,  416 => 104,  412 => 98,  408 => 200,  403 => 182,  400 => 225,  396 => 299,  392 => 198,  385 => 117,  381 => 228,  367 => 182,  363 => 164,  359 => 153,  355 => 160,  350 => 94,  346 => 140,  343 => 143,  328 => 17,  324 => 164,  313 => 125,  307 => 108,  301 => 69,  288 => 91,  283 => 115,  271 => 97,  257 => 85,  251 => 112,  238 => 94,  233 => 100,  195 => 72,  191 => 55,  187 => 46,  183 => 71,  130 => 67,  88 => 27,  76 => 28,  115 => 37,  95 => 22,  655 => 275,  651 => 232,  648 => 231,  637 => 241,  633 => 272,  621 => 408,  618 => 269,  615 => 232,  604 => 186,  600 => 516,  588 => 305,  585 => 253,  582 => 203,  571 => 242,  567 => 95,  555 => 207,  552 => 190,  549 => 208,  544 => 230,  542 => 207,  535 => 112,  531 => 358,  519 => 87,  516 => 229,  513 => 216,  508 => 86,  506 => 83,  499 => 211,  495 => 147,  491 => 145,  481 => 229,  478 => 202,  475 => 184,  469 => 197,  456 => 136,  451 => 207,  443 => 132,  439 => 129,  427 => 155,  423 => 109,  420 => 192,  409 => 89,  405 => 148,  401 => 148,  391 => 230,  387 => 129,  384 => 160,  378 => 145,  365 => 289,  360 => 158,  348 => 191,  336 => 135,  332 => 150,  329 => 188,  323 => 130,  310 => 109,  305 => 125,  277 => 99,  274 => 90,  263 => 147,  259 => 100,  247 => 112,  244 => 84,  241 => 107,  222 => 70,  210 => 85,  207 => 80,  204 => 49,  184 => 52,  181 => 51,  167 => 57,  157 => 45,  96 => 25,  421 => 138,  417 => 71,  414 => 173,  406 => 170,  398 => 147,  393 => 177,  390 => 221,  376 => 110,  369 => 94,  366 => 156,  352 => 192,  345 => 138,  342 => 137,  331 => 127,  326 => 102,  320 => 130,  317 => 134,  314 => 136,  311 => 141,  308 => 178,  297 => 120,  293 => 119,  281 => 111,  278 => 164,  275 => 124,  264 => 109,  260 => 125,  248 => 127,  245 => 104,  242 => 101,  231 => 73,  227 => 71,  215 => 88,  212 => 64,  209 => 75,  197 => 133,  177 => 43,  171 => 64,  161 => 68,  132 => 53,  121 => 28,  105 => 29,  99 => 26,  81 => 31,  77 => 22,  180 => 70,  176 => 83,  156 => 53,  143 => 41,  139 => 46,  118 => 30,  189 => 85,  185 => 86,  173 => 64,  166 => 47,  152 => 61,  174 => 48,  164 => 63,  154 => 80,  150 => 55,  137 => 42,  133 => 70,  127 => 66,  107 => 35,  102 => 30,  83 => 25,  78 => 18,  53 => 15,  23 => 3,  42 => 16,  138 => 69,  134 => 41,  109 => 32,  103 => 23,  97 => 56,  94 => 46,  84 => 24,  75 => 17,  69 => 19,  66 => 15,  54 => 9,  44 => 7,  230 => 101,  226 => 92,  203 => 92,  193 => 90,  188 => 44,  182 => 73,  178 => 76,  168 => 71,  163 => 65,  160 => 54,  155 => 43,  148 => 60,  145 => 67,  140 => 51,  136 => 38,  125 => 32,  120 => 38,  113 => 17,  101 => 23,  92 => 22,  89 => 49,  85 => 19,  73 => 15,  62 => 12,  59 => 12,  56 => 10,  41 => 5,  126 => 39,  119 => 49,  111 => 58,  106 => 31,  98 => 39,  93 => 28,  86 => 22,  70 => 24,  60 => 40,  28 => 1,  36 => 4,  114 => 62,  104 => 57,  91 => 23,  80 => 25,  63 => 16,  58 => 10,  40 => 5,  34 => 3,  45 => 11,  61 => 23,  55 => 13,  48 => 8,  39 => 7,  35 => 4,  31 => 2,  26 => 1,  21 => 2,  46 => 7,  29 => 2,  57 => 12,  50 => 45,  47 => 10,  38 => 7,  33 => 4,  49 => 8,  32 => 3,  246 => 78,  236 => 116,  232 => 107,  225 => 59,  221 => 86,  216 => 77,  214 => 101,  211 => 87,  208 => 56,  205 => 61,  199 => 50,  196 => 81,  190 => 78,  179 => 69,  175 => 51,  172 => 81,  169 => 49,  162 => 46,  158 => 67,  153 => 59,  151 => 71,  147 => 42,  144 => 58,  141 => 54,  135 => 34,  129 => 43,  124 => 36,  117 => 33,  112 => 36,  90 => 53,  87 => 26,  82 => 20,  72 => 15,  68 => 44,  65 => 13,  52 => 9,  43 => 6,  37 => 6,  30 => 3,  27 => 2,  25 => 2,  24 => 3,  22 => 2,  19 => 1,);
    }
}
