<?php

/* AdminBundle:TicketPriorities:list.html.twig */
class __TwigTemplate_b9920295b3fb79188443c0ab5ce7efe6 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("AdminBundle::layout.html.twig");

        $this->blocks = array(
            'html_head' => array($this, 'block_html_head'),
            'pagebar' => array($this, 'block_pagebar'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "AdminBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 2
        $context["design"] = $this->env->loadTemplate("AdminBundle:Common:design-macros.html.twig");
        // line 3
        $context["admin"] = $this->env->loadTemplate("AdminBundle:Common:admin-macros.html.twig");
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 4
    public function block_html_head($context, array $blocks = array())
    {
        // line 5
        echo "<script type=\"text/javascript\" src=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("javascripts/DeskPRO/Admin/ElementHandler/TicketPriorityList.js"), "html", null, true);
        echo "\"></script>
";
    }

    // line 7
    public function block_pagebar($context, array $blocks = array())
    {
        // line 8
        echo "\t<nav>
\t\t<ul>
\t\t\t<li class=\"add\"><a id=\"newcat_open\">";
        // line 10
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.add_new_priority");
        echo "</a></li>
\t\t</ul>
\t</nav>
\t<ul>
\t\t<li>";
        // line 14
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.ticket_priorities");
        echo "</li>
\t</ul>
";
    }

    // line 17
    public function block_content($context, array $blocks = array())
    {
        // line 18
        echo "<div class=\"dp-page-box\">
<div
\tclass=\"page-content\"
\tdata-element-handler=\"DeskPRO.Admin.ElementHandler.TicketPriorityList\"
>

\t";
        // line 24
        if (isset($context["all_priorities"])) { $_all_priorities_ = $context["all_priorities"]; } else { $_all_priorities_ = null; }
        if (twig_length_filter($this->env, $_all_priorities_)) {
            // line 25
            echo "\t\t";
            if (isset($context["design"])) { $_design_ = $context["design"]; } else { $_design_ = null; }
            echo $_design_->getdpgrid(array("class" => "dep-list pri-list"));
            echo "
\t\t\t";
            // line 26
            if (isset($context["design"])) { $_design_ = $context["design"]; } else { $_design_ = null; }
            echo $_design_->getdpgrid_headerrow();
            echo "
\t\t\t\t";
            // line 27
            if (isset($context["design"])) { $_design_ = $context["design"]; } else { $_design_ = null; }
            echo $_design_->getdpgrid_headercol(array("class" => "l r tl tr", "style" => "width: 810px;"));
            echo "
\t\t\t\t\t<h1>";
            // line 28
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.title");
            echo "</h1>
\t\t\t\t";
            // line 29
            if (isset($context["design"])) { $_design_ = $context["design"]; } else { $_design_ = null; }
            echo $_design_->getdpgrid_headercol_end();
            echo "
\t\t\t\t";
            // line 30
            if (isset($context["design"])) { $_design_ = $context["design"]; } else { $_design_ = null; }
            echo $_design_->getdpgrid_headercol(array("class" => "l r tl tr l-margin r-margin", "style" => "width: 80px"));
            echo "
\t\t\t\t\t<h1>";
            // line 31
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.priority");
            echo "</h1>
\t\t\t\t";
            // line 32
            if (isset($context["design"])) { $_design_ = $context["design"]; } else { $_design_ = null; }
            echo $_design_->getdpgrid_headercol_end();
            echo "
\t\t\t";
            // line 33
            if (isset($context["design"])) { $_design_ = $context["design"]; } else { $_design_ = null; }
            echo $_design_->getdpgrid_headerrow_end();
            echo "

\t\t\t";
            // line 35
            if (isset($context["all_priorities"])) { $_all_priorities_ = $context["all_priorities"]; } else { $_all_priorities_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_all_priorities_);
            $context['loop'] = array(
              'parent' => $context['_parent'],
              'index0' => 0,
              'index'  => 1,
              'first'  => true,
            );
            if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
                $length = count($context['_seq']);
                $context['loop']['revindex0'] = $length - 1;
                $context['loop']['revindex'] = $length;
                $context['loop']['length'] = $length;
                $context['loop']['last'] = 1 === $length;
            }
            foreach ($context['_seq'] as $context["_key"] => $context["priority"]) {
                // line 36
                echo "\t\t\t\t";
                $this->env->loadTemplate("AdminBundle:TicketPriorities:list-row.html.twig")->display($context);
                // line 37
                echo "\t\t\t";
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
                if (isset($context['loop']['length'])) {
                    --$context['loop']['revindex0'];
                    --$context['loop']['revindex'];
                    $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['priority'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 38
            echo "\t\t";
            if (isset($context["design"])) { $_design_ = $context["design"]; } else { $_design_ = null; }
            echo $_design_->getdpgrid_end();
            echo "
\t";
        } else {
            // line 40
            echo "\t\t<div class=\"note-box new-arrow\">
\t\t\tYou have not created any ticket priorities yet. Click the \"";
            // line 41
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.add_new_priority");
            echo "\" button to create one now.
\t\t</div>

\t";
        }
        // line 45
        echo "
\t<div id=\"editcat_overlay\" style=\"width: 400px; height: 250px; display: none;\">
\t\t<div class=\"overlay-title\">
\t\t\t<span class=\"close-overlay\"></span>
\t\t\t<h4>";
        // line 49
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.edit_priority");
        echo "</h4>
\t\t</div>
\t\t<div class=\"overlay-content\">
\t\t\t<form class=\"dp-form\" method=\"POST\" action=\"";
        // line 52
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_ticketpris_savetitle"), "html", null, true);
        echo "\">
\t\t\t\t<div class=\"dp-form-row\">
\t\t\t\t\t<div class=\"dp-form-label\">
\t\t\t\t\t\t<label>";
        // line 55
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.title");
        echo "</label>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"dp-form-input\">
\t\t\t\t\t\t<input type=\"hidden\" name=\"priority_id\" id=\"editcat_catid\" value=\"\" />
\t\t\t\t\t\t<input type=\"text\" name=\"title\" id=\"editcat_title\" value=\"\" />
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t<div class=\"dp-form-row\">
\t\t\t\t\t<div class=\"dp-form-label\">
\t\t\t\t\t\t<label>";
        // line 64
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.priority");
        echo "</label>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"dp-form-input\">
\t\t\t\t\t\t<input type=\"text\" name=\"priority\" id=\"editcat_pri\" value=\"\" style=\"width:30px\" />
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</form>
\t\t</div>
\t\t<div class=\"overlay-footer\">
\t\t\t<button class=\"clean-white\" id=\"editcat_savebtn\">";
        // line 73
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.save");
        echo "</button>
\t\t</div>
\t</div>

\t<div id=\"newcat_overlay\" style=\"width: 400px; height: 250px; display: none;\">
\t\t<div class=\"overlay-title\">
\t\t\t<span class=\"close-overlay\"></span>
\t\t\t<h4>";
        // line 80
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.new_priority");
        echo "</h4>
\t\t</div>
\t\t<div class=\"overlay-content\">
\t\t\t<form class=\"dp-form\" method=\"POST\" action=\"";
        // line 83
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_ticketpris_savenew"), "html", null, true);
        echo "\">
\t\t\t\t<div class=\"dp-form-row\">
\t\t\t\t\t<div class=\"dp-form-label\">
\t\t\t\t\t\t<label>";
        // line 86
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.title");
        echo "</label>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"dp-form-input\">
\t\t\t\t\t\t<input type=\"text\" name=\"title\" id=\"newcat_title\" value=\"\" />
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<div class=\"dp-form-row\">
\t\t\t\t\t<div class=\"dp-form-label\">
\t\t\t\t\t\t<label>";
        // line 95
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.priority");
        echo "</label>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"dp-form-input\">
\t\t\t\t\t\t<input type=\"text\" name=\"priority\" id=\"newcat_pri\" value=\"\" style=\"width:30px\" />
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</form>
\t\t</div>
\t\t<div class=\"overlay-footer\">
\t\t\t<button class=\"clean-white\" id=\"newcat_savebtn\">";
        // line 104
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.add");
        echo "</button>
\t\t</div>
\t</div>
</div>

";
        // line 109
        if (isset($context["all_priorities"])) { $_all_priorities_ = $context["all_priorities"]; } else { $_all_priorities_ = null; }
        if ($_all_priorities_) {
            // line 110
            echo "<div style=\"padding: 0 10px 10px 15px;\">
\t<form action=\"";
            // line 111
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_ticketpris_setdefault"), "html", null, true);
            echo "\" method=\"POST\">
\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"This is the value selected by default on new ticket forms.\"></span> Default priority:
\t\t<select name=\"default_value\">
\t\t\t<option></option>
\t\t\t";
            // line 115
            if (isset($context["all_priorities"])) { $_all_priorities_ = $context["all_priorities"]; } else { $_all_priorities_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_all_priorities_);
            foreach ($context['_seq'] as $context["_key"] => $context["itm"]) {
                // line 116
                echo "\t\t\t\t<option value=\"";
                if (isset($context["itm"])) { $_itm_ = $context["itm"]; } else { $_itm_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_itm_, "id"), "html", null, true);
                echo "\" ";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                if (isset($context["itm"])) { $_itm_ = $context["itm"]; } else { $_itm_ = null; }
                if (($this->getAttribute($_app_, "getSetting", array(0 => "core.default_ticket_pri"), "method") == $this->getAttribute($_itm_, "id"))) {
                    echo "selected=\"selected\"";
                }
                echo ">";
                if (isset($context["itm"])) { $_itm_ = $context["itm"]; } else { $_itm_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_itm_, "title"), "html", null, true);
                echo "</option>
\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['itm'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 118
            echo "\t\t</select>
\t\t<button class=\"clean-white\">Update</button>
\t</form>
</div>

";
            // line 123
            $this->env->loadTemplate("AdminBundle:TicketFeatures:field-cat-validation-options.html.twig")->display(array_merge($context, array("field_id" => "ticket_pri")));
        }
        // line 125
        echo "
</div>
";
    }

    public function getTemplateName()
    {
        return "AdminBundle:TicketPriorities:list.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1163 => 568,  1143 => 553,  1087 => 526,  1077 => 509,  1051 => 488,  1037 => 480,  1010 => 476,  999 => 458,  932 => 414,  899 => 405,  895 => 404,  933 => 149,  914 => 133,  909 => 132,  833 => 359,  783 => 332,  755 => 320,  666 => 300,  453 => 187,  639 => 110,  568 => 254,  520 => 249,  657 => 287,  572 => 251,  609 => 459,  20 => 1,  659 => 274,  562 => 240,  548 => 165,  558 => 244,  479 => 206,  589 => 100,  457 => 145,  413 => 172,  953 => 430,  948 => 403,  935 => 394,  929 => 391,  916 => 382,  864 => 365,  844 => 348,  816 => 342,  807 => 370,  801 => 338,  774 => 349,  766 => 328,  737 => 318,  685 => 293,  664 => 194,  635 => 281,  593 => 445,  546 => 236,  532 => 68,  865 => 221,  852 => 216,  838 => 208,  820 => 201,  781 => 333,  764 => 321,  725 => 164,  632 => 283,  602 => 265,  565 => 76,  529 => 282,  505 => 267,  487 => 53,  473 => 221,  1853 => 842,  1849 => 841,  1839 => 837,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 813,  1778 => 812,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 782,  1705 => 781,  1688 => 770,  1681 => 769,  1677 => 768,  1667 => 764,  1656 => 762,  1648 => 757,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 714,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 695,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 651,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 610,  1289 => 607,  1276 => 604,  1263 => 601,  1244 => 596,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 565,  1181 => 561,  1171 => 557,  1158 => 550,  1114 => 534,  1104 => 526,  1072 => 512,  1062 => 508,  1024 => 494,  1014 => 490,  1000 => 482,  990 => 478,  980 => 474,  960 => 466,  918 => 448,  888 => 376,  834 => 346,  673 => 342,  636 => 185,  462 => 192,  454 => 103,  1144 => 542,  1139 => 399,  1131 => 399,  1127 => 397,  1110 => 396,  1092 => 391,  1089 => 390,  1086 => 520,  1084 => 388,  1063 => 387,  1060 => 386,  1055 => 385,  1050 => 384,  1035 => 372,  1019 => 267,  1003 => 263,  959 => 251,  900 => 377,  880 => 434,  870 => 430,  867 => 366,  859 => 362,  848 => 215,  839 => 376,  828 => 357,  823 => 374,  809 => 181,  800 => 178,  797 => 366,  794 => 336,  786 => 174,  740 => 162,  734 => 332,  703 => 354,  693 => 350,  630 => 278,  626 => 140,  614 => 275,  610 => 103,  581 => 247,  564 => 229,  525 => 235,  722 => 162,  697 => 256,  674 => 277,  671 => 425,  577 => 257,  569 => 243,  557 => 169,  502 => 229,  497 => 240,  445 => 197,  729 => 159,  684 => 261,  676 => 299,  669 => 254,  660 => 145,  647 => 286,  643 => 244,  601 => 178,  570 => 172,  522 => 200,  501 => 58,  296 => 67,  374 => 149,  631 => 111,  616 => 281,  608 => 266,  605 => 16,  596 => 102,  574 => 165,  561 => 209,  527 => 233,  433 => 190,  388 => 161,  426 => 177,  383 => 182,  461 => 44,  370 => 147,  395 => 221,  294 => 72,  223 => 80,  220 => 36,  492 => 395,  468 => 132,  444 => 193,  410 => 169,  397 => 141,  377 => 159,  262 => 97,  250 => 139,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 2108,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 1383,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 1303,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 1173,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 1041,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 970,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 538,  1126 => 916,  1106 => 528,  1100 => 897,  1026 => 841,  997 => 822,  983 => 816,  975 => 402,  956 => 800,  939 => 786,  902 => 130,  894 => 128,  879 => 400,  757 => 631,  727 => 316,  716 => 308,  670 => 297,  528 => 232,  476 => 253,  435 => 208,  354 => 153,  341 => 212,  192 => 30,  321 => 154,  243 => 54,  793 => 350,  780 => 348,  758 => 335,  700 => 221,  686 => 150,  652 => 274,  638 => 282,  620 => 265,  545 => 259,  523 => 66,  494 => 55,  459 => 191,  438 => 195,  351 => 135,  347 => 16,  402 => 29,  268 => 65,  430 => 141,  411 => 167,  379 => 23,  322 => 123,  315 => 119,  289 => 113,  284 => 110,  255 => 115,  234 => 83,  1133 => 400,  1124 => 551,  1121 => 56,  1116 => 549,  1113 => 403,  1108 => 51,  1103 => 394,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 483,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 461,  996 => 262,  989 => 454,  985 => 315,  981 => 257,  977 => 313,  970 => 91,  966 => 437,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 148,  919 => 290,  917 => 289,  908 => 411,  905 => 378,  896 => 280,  891 => 403,  877 => 270,  862 => 267,  857 => 380,  837 => 347,  832 => 250,  827 => 375,  821 => 247,  803 => 179,  778 => 389,  769 => 165,  765 => 386,  753 => 328,  746 => 319,  743 => 318,  735 => 170,  730 => 330,  720 => 363,  717 => 362,  712 => 156,  691 => 204,  678 => 149,  654 => 144,  587 => 14,  576 => 179,  539 => 241,  517 => 230,  471 => 47,  441 => 239,  437 => 39,  418 => 201,  386 => 106,  373 => 102,  304 => 114,  270 => 143,  265 => 72,  229 => 75,  477 => 49,  455 => 224,  448 => 41,  429 => 112,  407 => 109,  399 => 162,  389 => 170,  375 => 162,  358 => 110,  349 => 131,  335 => 139,  327 => 155,  298 => 144,  280 => 102,  249 => 205,  194 => 55,  142 => 36,  344 => 92,  318 => 86,  306 => 116,  295 => 74,  357 => 154,  300 => 77,  286 => 147,  276 => 105,  269 => 103,  254 => 101,  128 => 47,  237 => 63,  165 => 64,  122 => 101,  798 => 337,  770 => 179,  759 => 112,  748 => 337,  731 => 108,  721 => 305,  718 => 313,  708 => 309,  696 => 147,  617 => 461,  590 => 226,  553 => 264,  550 => 156,  540 => 234,  533 => 255,  500 => 397,  493 => 225,  489 => 257,  482 => 223,  467 => 45,  464 => 202,  458 => 220,  452 => 217,  449 => 132,  415 => 32,  382 => 24,  372 => 150,  361 => 129,  356 => 215,  339 => 89,  302 => 150,  285 => 115,  258 => 136,  123 => 31,  108 => 51,  424 => 187,  394 => 139,  380 => 151,  338 => 155,  319 => 125,  316 => 123,  312 => 152,  290 => 116,  267 => 96,  206 => 64,  110 => 44,  240 => 86,  224 => 95,  219 => 128,  217 => 94,  202 => 64,  186 => 70,  170 => 113,  100 => 25,  67 => 17,  14 => 1,  1096 => 524,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 478,  1013 => 477,  1007 => 274,  1002 => 823,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 402,  940 => 298,  937 => 395,  928 => 452,  926 => 413,  915 => 239,  912 => 236,  903 => 231,  898 => 440,  892 => 229,  889 => 748,  887 => 402,  884 => 374,  876 => 222,  874 => 215,  871 => 368,  863 => 214,  861 => 213,  858 => 212,  850 => 378,  843 => 206,  840 => 406,  815 => 372,  812 => 244,  808 => 199,  804 => 395,  799 => 196,  791 => 193,  785 => 238,  775 => 184,  771 => 183,  754 => 340,  728 => 317,  726 => 169,  723 => 168,  715 => 105,  711 => 152,  709 => 222,  706 => 302,  698 => 208,  694 => 308,  692 => 155,  689 => 302,  681 => 300,  677 => 149,  675 => 289,  663 => 276,  661 => 277,  650 => 246,  646 => 112,  629 => 183,  627 => 266,  625 => 180,  622 => 270,  598 => 174,  592 => 261,  586 => 264,  575 => 174,  566 => 242,  556 => 73,  554 => 240,  541 => 163,  536 => 241,  515 => 209,  511 => 269,  509 => 60,  488 => 155,  486 => 220,  483 => 341,  465 => 147,  463 => 216,  450 => 116,  432 => 211,  419 => 173,  371 => 182,  362 => 111,  353 => 141,  337 => 137,  333 => 156,  309 => 118,  303 => 70,  299 => 117,  291 => 176,  272 => 109,  261 => 141,  253 => 91,  239 => 82,  235 => 44,  213 => 139,  200 => 52,  198 => 85,  159 => 38,  149 => 36,  146 => 42,  131 => 36,  116 => 99,  79 => 25,  74 => 28,  71 => 27,  836 => 320,  817 => 398,  814 => 200,  811 => 317,  805 => 313,  787 => 333,  779 => 169,  776 => 305,  773 => 347,  761 => 296,  751 => 163,  747 => 325,  742 => 336,  739 => 333,  736 => 317,  724 => 214,  705 => 278,  702 => 601,  688 => 113,  680 => 278,  667 => 296,  662 => 271,  656 => 418,  649 => 285,  644 => 284,  641 => 268,  624 => 109,  613 => 264,  607 => 273,  597 => 270,  591 => 267,  584 => 259,  579 => 175,  563 => 96,  559 => 245,  551 => 243,  547 => 71,  537 => 90,  524 => 220,  512 => 227,  507 => 156,  504 => 213,  498 => 213,  485 => 153,  480 => 50,  472 => 205,  466 => 210,  460 => 221,  447 => 143,  442 => 40,  434 => 212,  428 => 36,  422 => 176,  404 => 149,  368 => 149,  364 => 101,  340 => 170,  334 => 129,  330 => 148,  325 => 134,  292 => 148,  287 => 67,  282 => 104,  279 => 109,  273 => 170,  266 => 142,  256 => 140,  252 => 95,  228 => 80,  218 => 73,  201 => 74,  64 => 14,  51 => 10,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 1705,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 737,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 731,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 725,  1559 => 601,  1552 => 720,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 708,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 645,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 624,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 1001,  1257 => 1000,  1254 => 479,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 569,  1196 => 958,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 938,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 561,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 550,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 898,  1099 => 393,  1095 => 419,  1091 => 418,  1082 => 525,  1079 => 524,  1076 => 359,  1070 => 875,  1057 => 491,  1052 => 504,  1045 => 484,  1040 => 377,  1036 => 283,  1032 => 496,  1028 => 274,  1023 => 393,  1020 => 392,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 821,  979 => 379,  974 => 256,  967 => 373,  962 => 803,  958 => 801,  954 => 253,  950 => 153,  945 => 152,  942 => 460,  938 => 150,  934 => 364,  927 => 147,  923 => 387,  920 => 412,  910 => 353,  901 => 347,  897 => 129,  890 => 343,  886 => 50,  883 => 401,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 377,  841 => 210,  835 => 312,  830 => 249,  826 => 202,  822 => 354,  818 => 246,  813 => 183,  810 => 345,  806 => 180,  802 => 198,  795 => 336,  792 => 239,  789 => 349,  784 => 188,  782 => 187,  777 => 291,  772 => 289,  768 => 334,  763 => 327,  760 => 329,  756 => 230,  752 => 176,  745 => 281,  741 => 280,  738 => 379,  732 => 171,  719 => 279,  714 => 264,  710 => 310,  704 => 267,  699 => 303,  695 => 297,  690 => 263,  687 => 279,  683 => 346,  679 => 298,  672 => 276,  668 => 247,  665 => 285,  658 => 244,  645 => 277,  640 => 285,  634 => 267,  628 => 466,  623 => 107,  619 => 298,  611 => 268,  606 => 263,  603 => 267,  599 => 262,  595 => 132,  583 => 263,  580 => 257,  573 => 274,  560 => 75,  543 => 235,  538 => 69,  534 => 233,  530 => 202,  526 => 229,  521 => 226,  518 => 233,  514 => 221,  510 => 227,  503 => 266,  496 => 226,  490 => 214,  484 => 394,  474 => 48,  470 => 231,  446 => 185,  440 => 102,  436 => 182,  431 => 37,  425 => 35,  416 => 231,  412 => 110,  408 => 141,  403 => 194,  400 => 225,  396 => 28,  392 => 139,  385 => 25,  381 => 133,  367 => 147,  363 => 18,  359 => 100,  355 => 326,  350 => 140,  346 => 140,  343 => 134,  328 => 135,  324 => 125,  313 => 124,  307 => 151,  301 => 124,  288 => 116,  283 => 111,  271 => 160,  257 => 148,  251 => 88,  238 => 103,  233 => 81,  195 => 121,  191 => 35,  187 => 57,  183 => 52,  130 => 52,  88 => 93,  76 => 24,  115 => 41,  95 => 28,  655 => 270,  651 => 232,  648 => 269,  637 => 273,  633 => 272,  621 => 462,  618 => 277,  615 => 268,  604 => 186,  600 => 271,  588 => 305,  585 => 260,  582 => 177,  571 => 242,  567 => 95,  555 => 239,  552 => 238,  549 => 237,  544 => 230,  542 => 290,  535 => 233,  531 => 158,  519 => 64,  516 => 63,  513 => 228,  508 => 230,  506 => 59,  499 => 241,  495 => 239,  491 => 54,  481 => 152,  478 => 235,  475 => 184,  469 => 196,  456 => 204,  451 => 42,  443 => 194,  439 => 183,  427 => 177,  423 => 175,  420 => 208,  409 => 179,  405 => 30,  401 => 164,  391 => 27,  387 => 134,  384 => 160,  378 => 154,  365 => 131,  360 => 17,  348 => 191,  336 => 132,  332 => 150,  329 => 127,  323 => 135,  310 => 123,  305 => 112,  277 => 170,  274 => 102,  263 => 97,  259 => 102,  247 => 138,  244 => 137,  241 => 133,  222 => 105,  210 => 122,  207 => 110,  204 => 124,  184 => 28,  181 => 110,  167 => 53,  157 => 114,  96 => 46,  421 => 33,  417 => 71,  414 => 142,  406 => 130,  398 => 165,  393 => 162,  390 => 153,  376 => 22,  369 => 19,  366 => 174,  352 => 140,  345 => 139,  342 => 160,  331 => 125,  326 => 87,  320 => 121,  317 => 125,  314 => 136,  311 => 85,  308 => 116,  297 => 112,  293 => 114,  281 => 146,  278 => 111,  275 => 110,  264 => 104,  260 => 107,  248 => 75,  245 => 104,  242 => 82,  231 => 52,  227 => 131,  215 => 88,  212 => 111,  209 => 125,  197 => 51,  177 => 118,  171 => 55,  161 => 68,  132 => 34,  121 => 46,  105 => 35,  99 => 29,  81 => 16,  77 => 19,  180 => 54,  176 => 45,  156 => 38,  143 => 50,  139 => 104,  118 => 33,  189 => 88,  185 => 46,  173 => 117,  166 => 40,  152 => 60,  174 => 66,  164 => 58,  154 => 113,  150 => 43,  137 => 49,  133 => 48,  127 => 102,  107 => 97,  102 => 34,  83 => 25,  78 => 20,  53 => 10,  23 => 6,  42 => 7,  138 => 86,  134 => 44,  109 => 31,  103 => 30,  97 => 95,  94 => 33,  84 => 9,  75 => 14,  69 => 16,  66 => 16,  54 => 9,  44 => 7,  230 => 74,  226 => 128,  203 => 86,  193 => 122,  188 => 52,  182 => 49,  178 => 49,  168 => 116,  163 => 115,  160 => 68,  155 => 55,  148 => 51,  145 => 37,  140 => 65,  136 => 53,  125 => 16,  120 => 38,  113 => 32,  101 => 33,  92 => 41,  89 => 27,  85 => 26,  73 => 18,  62 => 13,  59 => 10,  56 => 14,  41 => 5,  126 => 54,  119 => 65,  111 => 98,  106 => 30,  98 => 35,  93 => 31,  86 => 27,  70 => 13,  60 => 15,  28 => 3,  36 => 5,  114 => 33,  104 => 30,  91 => 29,  80 => 29,  63 => 30,  58 => 14,  40 => 7,  34 => 4,  45 => 8,  61 => 12,  55 => 11,  48 => 9,  39 => 5,  35 => 4,  31 => 2,  26 => 2,  21 => 2,  46 => 12,  29 => 3,  57 => 14,  50 => 9,  47 => 8,  38 => 5,  33 => 4,  49 => 8,  32 => 3,  246 => 140,  236 => 87,  232 => 129,  225 => 82,  221 => 76,  216 => 65,  214 => 126,  211 => 111,  208 => 67,  205 => 87,  199 => 123,  196 => 85,  190 => 101,  179 => 94,  175 => 76,  172 => 52,  169 => 41,  162 => 80,  158 => 28,  153 => 27,  151 => 112,  147 => 66,  144 => 51,  141 => 55,  135 => 35,  129 => 35,  124 => 35,  117 => 32,  112 => 40,  90 => 27,  87 => 21,  82 => 88,  72 => 19,  68 => 18,  65 => 17,  52 => 12,  43 => 6,  37 => 5,  30 => 2,  27 => 2,  25 => 65,  24 => 3,  22 => 34,  19 => 1,);
    }
}
