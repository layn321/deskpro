<?php

/* DeskPRO:emails_common:ticket-props-table.html.twig */
class __TwigTemplate_030341cf69efb39211a7a89ebe46831c extends \Application\DeskPRO\Twig\Template
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
        echo "<table class=\"dp-props-table\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
\t<tr>
\t\t<td class=\"dp-td-heading\" colspan=\"2\">
\t\t\t";
        // line 4
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.emails.view_ticket_online");
        echo ": <a href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->urlFull("agent"), "html", null, true);
        echo "#app.tickets,t:";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "id"), "html", null, true);
        echo "\" title=\"(#";
        if (isset($context["tac"])) { $_tac_ = $context["tac"]; } else { $_tac_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_tac_, "access_code"), "html", null, true);
        echo ")\">";
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->urlFull("agent"), "html", null, true);
        echo "#app.tickets,t:";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "id"), "html", null, true);
        echo "</a>
\t\t</td>
\t</tr>
\t<tr>
\t\t<td class=\"dp-td-heading\">";
        // line 8
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.id");
        echo "</td>
\t\t<td>";
        // line 9
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "id"), "html", null, true);
        echo "</td>
\t</tr>
\t";
        // line 11
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.tickets.use_ref"), "method")) {
            // line 12
            echo "\t\t<tr>
\t\t\t<td class=\"dp-td-heading\">";
            // line 13
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ref");
            echo "</td>
\t\t\t<td>";
            // line 14
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "ref"), "html", null, true);
            echo "</td>
\t\t</tr>
\t";
        }
        // line 17
        echo "\t<tr>
\t\t<td class=\"dp-td-heading\">";
        // line 18
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.status");
        echo "</td>
\t\t<td>
\t\t\t";
        // line 20
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, ("agent.tickets.status_" . $this->getAttribute($_ticket_, "status")));
        echo "
\t\t</td>
\t</tr>
\t<tr>
\t\t<td class=\"dp-td-heading\">";
        // line 24
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.agent");
        echo "</td>
\t\t<td>";
        // line 25
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_ticket_, "agent", array(), "any", false, true), "display_name", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "agent", array(), "any", false, true), "display_name"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.unassigned"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.unassigned"))), "html", null, true);
        echo "</td>
\t</tr>
\t";
        // line 27
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ($this->getAttribute($_ticket_, "agent_team")) {
            // line 28
            echo "\t<tr>
\t\t<td class=\"dp-td-heading\">";
            // line 29
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.team");
            echo "</td>
\t\t<td>";
            // line 30
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_ticket_, "agent_team", array(), "any", false, true), "name", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "agent_team", array(), "any", false, true), "name"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</td>
\t</tr>
\t";
        }
        // line 33
        echo "
\t";
        // line 34
        if (isset($context["page_display"])) { $_page_display_ = $context["page_display"]; } else { $_page_display_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_page_display_);
        foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
            // line 35
            echo "\t\t";
            if (isset($context["item"])) { $_item_ = $context["item"]; } else { $_item_ = null; }
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if (($this->getAttribute($_item_, "field_type") == "ticket_department")) {
                // line 36
                echo "\t\t\t<tr>
\t\t\t\t<td class=\"dp-td-heading\">";
                // line 37
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.department");
                echo "</td>
\t\t\t\t<td>";
                // line 38
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_ticket_, "department", array(), "any", false, true), "full_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "department", array(), "any", false, true), "full_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
                echo "</td>
\t\t\t</tr>
\t\t";
            } elseif ((($this->getAttribute($_item_, "field_type") == "ticket_category") && $this->getAttribute($_ticket_, "category"))) {
                // line 41
                echo "\t\t\t<tr>
\t\t\t\t<td class=\"dp-td-heading\">";
                // line 42
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.category");
                echo "</td>
\t\t\t\t<td>";
                // line 43
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_ticket_, "category", array(), "any", false, true), "full_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "category", array(), "any", false, true), "full_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
                echo "</td>
\t\t\t</tr>
\t\t";
            } elseif ((($this->getAttribute($_item_, "field_type") == "ticket_product") && $this->getAttribute($_ticket_, "product"))) {
                // line 46
                echo "\t\t\t<tr>
\t\t\t\t<td class=\"dp-td-heading\">";
                // line 47
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.product");
                echo "</td>
\t\t\t\t<td>";
                // line 48
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_ticket_, "product", array(), "any", false, true), "title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "product", array(), "any", false, true), "title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
                echo "</td>
\t\t\t</tr>
\t\t";
            } elseif ((($this->getAttribute($_item_, "field_type") == "ticket_priority") && $this->getAttribute($_ticket_, "priority"))) {
                // line 51
                echo "\t\t\t<tr>
\t\t\t\t<td class=\"dp-td-heading\">";
                // line 52
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.priority");
                echo "</td>
\t\t\t\t<td>";
                // line 53
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_ticket_, "priority", array(), "any", false, true), "title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "priority", array(), "any", false, true), "title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
                echo "</td>
\t\t\t</tr>
\t\t";
            } elseif ((($this->getAttribute($_item_, "field_type") == "ticket_workflow") && $this->getAttribute($_ticket_, "workflow"))) {
                // line 56
                echo "\t\t\t<tr>
\t\t\t\t<td class=\"dp-td-heading\">";
                // line 57
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.workflow");
                echo "</td>
\t\t\t\t<td>";
                // line 58
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_ticket_, "workflow", array(), "any", false, true), "title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "workflow", array(), "any", false, true), "title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
                echo "</td>
\t\t\t</tr>
\t\t";
            } else {
                // line 61
                echo "\t\t\t\t";
                if (isset($context["custom_fields"])) { $_custom_fields_ = $context["custom_fields"]; } else { $_custom_fields_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($_custom_fields_);
                foreach ($context['_seq'] as $context["_key"] => $context["f"]) {
                    // line 62
                    echo "\t\t\t\t\t";
                    if (isset($context["item"])) { $_item_ = $context["item"]; } else { $_item_ = null; }
                    if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                    if ((($this->getAttribute($_item_, "field_type") == "ticket_field") && ($this->getAttribute($_item_, "field_id") == $this->getAttribute($_f_, "id")))) {
                        // line 63
                        echo "\t\t\t\t\t\t";
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                        if ($this->getAttribute($_ticket_, "hasCustomField", array(0 => $this->getAttribute($this->getAttribute($_f_, "field_def"), "id")), "method")) {
                            // line 64
                            echo "\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td class=\"dp-th-heading\">";
                            // line 65
                            if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_f_, "title"), "html", null, true);
                            echo "</td>
\t\t\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t\t\t";
                            // line 67
                            if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                            echo $this->env->getExtension('deskpro_templating')->renderCustomField($_f_);
                            echo "
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t";
                        }
                        // line 71
                        echo "\t\t\t\t\t";
                    }
                    // line 72
                    echo "\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['f'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 73
                echo "\t\t";
            }
            // line 74
            echo "\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 75
        echo "</table>";
    }

    public function getTemplateName()
    {
        return "DeskPRO:emails_common:ticket-props-table.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  122 => 35,  798 => 119,  770 => 113,  759 => 112,  748 => 110,  731 => 108,  721 => 107,  718 => 106,  708 => 104,  696 => 102,  617 => 94,  590 => 91,  553 => 87,  550 => 86,  540 => 84,  533 => 82,  500 => 74,  493 => 72,  489 => 71,  482 => 69,  467 => 67,  464 => 66,  458 => 64,  452 => 62,  449 => 61,  415 => 55,  382 => 52,  372 => 51,  361 => 49,  356 => 48,  339 => 46,  302 => 42,  285 => 40,  258 => 37,  123 => 18,  108 => 16,  424 => 57,  394 => 86,  380 => 80,  338 => 67,  319 => 66,  316 => 65,  312 => 43,  290 => 60,  267 => 57,  206 => 43,  110 => 22,  240 => 60,  224 => 54,  219 => 51,  217 => 50,  202 => 44,  186 => 57,  170 => 34,  100 => 28,  67 => 10,  14 => 1,  1096 => 291,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 276,  1013 => 275,  1007 => 274,  1002 => 272,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 251,  940 => 249,  937 => 248,  928 => 244,  926 => 243,  915 => 237,  912 => 236,  903 => 231,  898 => 230,  892 => 229,  889 => 228,  887 => 227,  884 => 226,  876 => 222,  874 => 221,  871 => 220,  863 => 214,  861 => 213,  858 => 212,  850 => 208,  843 => 206,  840 => 205,  815 => 201,  812 => 200,  808 => 199,  804 => 120,  799 => 196,  791 => 190,  785 => 188,  775 => 184,  771 => 183,  754 => 176,  728 => 170,  726 => 169,  723 => 168,  715 => 105,  711 => 163,  709 => 162,  706 => 161,  698 => 157,  694 => 156,  692 => 155,  689 => 154,  681 => 150,  677 => 149,  675 => 148,  663 => 143,  661 => 99,  650 => 137,  646 => 136,  629 => 129,  627 => 128,  625 => 127,  622 => 126,  598 => 119,  592 => 117,  586 => 115,  575 => 108,  566 => 103,  556 => 100,  554 => 99,  541 => 92,  536 => 91,  515 => 86,  511 => 85,  509 => 84,  488 => 79,  486 => 78,  483 => 77,  465 => 73,  463 => 72,  450 => 65,  432 => 60,  419 => 57,  371 => 46,  362 => 43,  353 => 39,  337 => 37,  333 => 35,  309 => 63,  303 => 31,  299 => 30,  291 => 28,  272 => 21,  261 => 16,  253 => 14,  239 => 73,  235 => 6,  213 => 64,  200 => 50,  198 => 28,  159 => 204,  149 => 187,  146 => 29,  131 => 44,  116 => 32,  79 => 20,  74 => 18,  71 => 17,  836 => 320,  817 => 319,  814 => 318,  811 => 317,  805 => 313,  787 => 115,  779 => 306,  776 => 305,  773 => 301,  761 => 296,  751 => 175,  747 => 293,  742 => 292,  739 => 291,  736 => 287,  724 => 282,  705 => 278,  702 => 277,  688 => 265,  680 => 263,  667 => 261,  662 => 260,  656 => 259,  649 => 258,  644 => 97,  641 => 134,  624 => 240,  613 => 231,  607 => 93,  597 => 225,  591 => 222,  584 => 218,  579 => 216,  563 => 88,  559 => 208,  551 => 98,  547 => 205,  537 => 201,  524 => 191,  512 => 185,  507 => 76,  504 => 182,  498 => 178,  485 => 176,  480 => 175,  472 => 171,  466 => 169,  460 => 71,  447 => 163,  442 => 162,  434 => 58,  428 => 156,  422 => 152,  404 => 149,  368 => 136,  364 => 134,  340 => 131,  334 => 130,  330 => 129,  325 => 128,  292 => 116,  287 => 115,  282 => 112,  279 => 111,  273 => 107,  266 => 106,  256 => 15,  252 => 102,  228 => 32,  218 => 287,  201 => 91,  64 => 14,  51 => 13,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 793,  2042 => 790,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 774,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 745,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 707,  1829 => 706,  1827 => 705,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 689,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 676,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 666,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 638,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 617,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 611,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 602,  1559 => 601,  1552 => 598,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 584,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 573,  1477 => 567,  1473 => 566,  1467 => 562,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 520,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 511,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 481,  1257 => 480,  1254 => 479,  1250 => 478,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 460,  1196 => 458,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 452,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 445,  1146 => 444,  1137 => 437,  1123 => 426,  1119 => 425,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 421,  1099 => 420,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 412,  1076 => 411,  1070 => 407,  1057 => 405,  1052 => 404,  1045 => 399,  1040 => 397,  1036 => 283,  1032 => 395,  1028 => 394,  1023 => 393,  1020 => 392,  1016 => 391,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 381,  979 => 379,  974 => 261,  967 => 373,  962 => 371,  958 => 370,  954 => 253,  950 => 368,  945 => 367,  942 => 250,  938 => 365,  934 => 364,  927 => 361,  923 => 242,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 341,  868 => 328,  856 => 323,  853 => 319,  849 => 318,  845 => 207,  841 => 321,  835 => 312,  830 => 310,  826 => 309,  822 => 308,  818 => 307,  813 => 306,  810 => 305,  806 => 304,  802 => 197,  795 => 118,  792 => 311,  789 => 298,  784 => 295,  782 => 187,  777 => 291,  772 => 289,  768 => 182,  763 => 287,  760 => 178,  756 => 111,  752 => 284,  745 => 281,  741 => 280,  738 => 279,  732 => 171,  719 => 273,  714 => 280,  710 => 279,  704 => 267,  699 => 103,  695 => 264,  690 => 263,  687 => 262,  683 => 261,  679 => 101,  672 => 147,  668 => 256,  665 => 255,  658 => 141,  645 => 248,  640 => 247,  634 => 96,  628 => 240,  623 => 238,  619 => 237,  611 => 235,  606 => 234,  603 => 120,  599 => 232,  595 => 231,  583 => 114,  580 => 90,  573 => 221,  560 => 101,  543 => 204,  538 => 209,  534 => 208,  530 => 81,  526 => 89,  521 => 80,  518 => 204,  514 => 78,  510 => 202,  503 => 75,  496 => 197,  490 => 193,  484 => 192,  474 => 190,  470 => 188,  446 => 64,  440 => 184,  436 => 61,  431 => 157,  425 => 178,  416 => 175,  412 => 55,  408 => 173,  403 => 172,  400 => 53,  396 => 51,  392 => 169,  385 => 166,  381 => 48,  367 => 45,  363 => 155,  359 => 154,  355 => 153,  350 => 150,  346 => 149,  343 => 148,  328 => 139,  324 => 138,  313 => 134,  307 => 132,  301 => 61,  288 => 27,  283 => 125,  271 => 119,  257 => 114,  251 => 13,  238 => 34,  233 => 72,  195 => 42,  191 => 45,  187 => 42,  183 => 56,  130 => 37,  88 => 18,  76 => 14,  115 => 27,  95 => 16,  655 => 233,  651 => 232,  648 => 231,  637 => 223,  633 => 130,  621 => 219,  618 => 218,  615 => 121,  604 => 209,  600 => 226,  588 => 116,  585 => 204,  582 => 203,  571 => 104,  567 => 210,  555 => 207,  552 => 190,  549 => 215,  544 => 186,  542 => 185,  535 => 181,  531 => 90,  519 => 189,  516 => 176,  513 => 175,  508 => 172,  506 => 83,  499 => 198,  495 => 166,  491 => 165,  481 => 162,  478 => 68,  475 => 160,  469 => 170,  456 => 154,  451 => 186,  443 => 60,  439 => 147,  427 => 89,  423 => 58,  420 => 176,  409 => 54,  405 => 54,  401 => 132,  391 => 129,  387 => 49,  384 => 139,  378 => 138,  365 => 78,  360 => 120,  348 => 114,  336 => 111,  332 => 140,  329 => 45,  323 => 105,  310 => 133,  305 => 62,  277 => 23,  274 => 91,  263 => 54,  259 => 82,  247 => 79,  244 => 53,  241 => 77,  222 => 67,  210 => 46,  207 => 96,  204 => 92,  184 => 26,  181 => 52,  167 => 212,  157 => 22,  96 => 26,  421 => 153,  417 => 152,  414 => 151,  406 => 148,  398 => 144,  393 => 144,  390 => 143,  376 => 79,  369 => 137,  366 => 136,  352 => 115,  345 => 132,  342 => 72,  331 => 129,  326 => 128,  320 => 137,  317 => 124,  314 => 33,  311 => 122,  308 => 123,  297 => 97,  293 => 29,  281 => 93,  278 => 110,  275 => 39,  264 => 58,  260 => 115,  248 => 75,  245 => 96,  242 => 74,  231 => 87,  227 => 86,  215 => 83,  212 => 82,  209 => 81,  197 => 61,  177 => 41,  171 => 70,  161 => 60,  132 => 27,  121 => 23,  105 => 40,  99 => 114,  81 => 15,  77 => 11,  180 => 38,  176 => 53,  156 => 64,  143 => 30,  139 => 175,  118 => 25,  189 => 40,  185 => 236,  173 => 35,  166 => 68,  152 => 34,  174 => 40,  164 => 211,  154 => 33,  150 => 55,  137 => 20,  133 => 26,  127 => 36,  107 => 30,  102 => 22,  83 => 19,  78 => 21,  53 => 8,  23 => 3,  42 => 3,  138 => 28,  134 => 38,  109 => 25,  103 => 29,  97 => 27,  94 => 19,  84 => 15,  75 => 17,  69 => 11,  66 => 18,  54 => 11,  44 => 8,  230 => 71,  226 => 68,  203 => 62,  193 => 242,  188 => 84,  182 => 235,  178 => 71,  168 => 64,  163 => 35,  160 => 77,  155 => 46,  148 => 43,  145 => 43,  140 => 28,  136 => 27,  125 => 24,  120 => 51,  113 => 17,  101 => 32,  92 => 25,  89 => 17,  85 => 12,  73 => 13,  62 => 10,  59 => 9,  56 => 6,  41 => 3,  126 => 160,  119 => 147,  111 => 20,  106 => 24,  98 => 20,  93 => 14,  86 => 25,  70 => 12,  60 => 13,  28 => 2,  36 => 3,  114 => 33,  104 => 27,  91 => 25,  80 => 15,  63 => 7,  58 => 8,  40 => 82,  34 => 59,  45 => 9,  61 => 9,  55 => 7,  48 => 9,  39 => 4,  35 => 6,  31 => 48,  26 => 2,  21 => 2,  46 => 4,  29 => 5,  57 => 12,  50 => 4,  47 => 4,  38 => 2,  33 => 1,  49 => 5,  32 => 1,  246 => 90,  236 => 59,  232 => 54,  225 => 3,  221 => 288,  216 => 65,  214 => 98,  211 => 272,  208 => 63,  205 => 269,  199 => 91,  196 => 71,  190 => 58,  179 => 66,  175 => 220,  172 => 52,  169 => 51,  162 => 48,  158 => 47,  153 => 45,  151 => 193,  147 => 31,  144 => 42,  141 => 41,  135 => 51,  129 => 25,  124 => 41,  117 => 34,  112 => 26,  90 => 21,  87 => 24,  82 => 12,  72 => 13,  68 => 10,  65 => 9,  52 => 7,  43 => 5,  37 => 7,  30 => 3,  27 => 2,  25 => 30,  24 => 4,  22 => 18,  19 => 1,);
    }
}
