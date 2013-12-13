<?php

/* UserBundle:Common:related-box.html.twig */
class __TwigTemplate_fcb1cbc4cbca6659c11bda4d8dea719b extends \Application\DeskPRO\Twig\Template
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
        // line 4
        if (isset($context["related_content"])) { $_related_content_ = $context["related_content"]; } else { $_related_content_ = null; }
        if ((((twig_length_filter($this->env, $this->getAttribute($_related_content_, "articles", array(), "array")) || twig_length_filter($this->env, $this->getAttribute($_related_content_, "feedback", array(), "array"))) || twig_length_filter($this->env, $this->getAttribute($_related_content_, "news", array(), "array"))) || twig_length_filter($this->env, $this->getAttribute($_related_content_, "downloads", array(), "array")))) {
            // line 5
            echo "<div class=\"dp-related-list dp-well dp-well-small\">
\t<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tr>
\t\t";
            // line 7
            $context["count"] = 0;
            // line 8
            echo "\t\t";
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable(array(0 => "articles", 1 => "feedback", 2 => "news", 3 => "downloads"));
            foreach ($context['_seq'] as $context["_key"] => $context["itemtype"]) {
                // line 9
                echo "\t\t\t";
                if (isset($context["related_content"])) { $_related_content_ = $context["related_content"]; } else { $_related_content_ = null; }
                if (isset($context["itemtype"])) { $_itemtype_ = $context["itemtype"]; } else { $_itemtype_ = null; }
                $context["items"] = $this->getAttribute($_related_content_, $_itemtype_, array(), "array");
                // line 10
                echo "\t\t\t";
                if (isset($context["items"])) { $_items_ = $context["items"]; } else { $_items_ = null; }
                if (twig_length_filter($this->env, $_items_)) {
                    // line 11
                    echo "\t\t\t\t<td ";
                    if (isset($context["related_content"])) { $_related_content_ = $context["related_content"]; } else { $_related_content_ = null; }
                    if ((twig_length_filter($this->env, $_related_content_) > 1)) {
                        echo "width=\"50%\"";
                    }
                    echo " valign=\"top\">
\t\t\t\t\t";
                    // line 12
                    if (isset($context["itemtype"])) { $_itemtype_ = $context["itemtype"]; } else { $_itemtype_ = null; }
                    if (($_itemtype_ == "articles")) {
                        // line 13
                        echo "\t\t\t\t\t\t<h5>";
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.knowledgebase.related_articles");
                        echo "</h5>
\t\t\t\t\t\t<ul>
\t\t\t\t\t\t\t";
                        // line 15
                        if (isset($context["items"])) { $_items_ = $context["items"]; } else { $_items_ = null; }
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($_items_);
                        foreach ($context['_seq'] as $context["_key"] => $context["rel"]) {
                            // line 16
                            echo "\t\t\t\t\t\t\t\t<li> <a href=\"";
                            if (isset($context["rel"])) { $_rel_ = $context["rel"]; } else { $_rel_ = null; }
                            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_articles_article", array("slug" => $this->getAttribute($_rel_, "url_slug"))), "html", null, true);
                            echo "\">";
                            if (isset($context["rel"])) { $_rel_ = $context["rel"]; } else { $_rel_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_rel_, "title"), "html", null, true);
                            echo "</a></li>
\t\t\t\t\t\t\t";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['rel'], $context['_parent'], $context['loop']);
                        $context = array_merge($_parent, array_intersect_key($context, $_parent));
                        // line 18
                        echo "\t\t\t\t\t\t</ul>
\t\t\t\t\t";
                    } elseif (($_itemtype_ == "downloads")) {
                        // line 20
                        echo "\t\t\t\t\t\t<h5>";
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.downloads.related_downloads");
                        echo "</h5>
\t\t\t\t\t\t<ul>
\t\t\t\t\t\t\t";
                        // line 22
                        if (isset($context["items"])) { $_items_ = $context["items"]; } else { $_items_ = null; }
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($_items_);
                        foreach ($context['_seq'] as $context["_key"] => $context["rel"]) {
                            // line 23
                            echo "\t\t\t\t\t\t\t\t<li> <a href=\"";
                            if (isset($context["rel"])) { $_rel_ = $context["rel"]; } else { $_rel_ = null; }
                            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_downloads_file", array("slug" => $this->getAttribute($_rel_, "url_slug"))), "html", null, true);
                            echo "\">";
                            if (isset($context["rel"])) { $_rel_ = $context["rel"]; } else { $_rel_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_rel_, "title"), "html", null, true);
                            echo "</a></li>
\t\t\t\t\t\t\t";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['rel'], $context['_parent'], $context['loop']);
                        $context = array_merge($_parent, array_intersect_key($context, $_parent));
                        // line 25
                        echo "\t\t\t\t\t\t</ul>
\t\t\t\t\t";
                    } elseif (($_itemtype_ == "news")) {
                        // line 27
                        echo "\t\t\t\t\t\t<h5>";
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.news.related_news");
                        echo "</h5>
\t\t\t\t\t\t<ul>
\t\t\t\t\t\t\t";
                        // line 29
                        if (isset($context["items"])) { $_items_ = $context["items"]; } else { $_items_ = null; }
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($_items_);
                        foreach ($context['_seq'] as $context["_key"] => $context["rel"]) {
                            // line 30
                            echo "\t\t\t\t\t\t\t\t<li> <a href=\"";
                            if (isset($context["rel"])) { $_rel_ = $context["rel"]; } else { $_rel_ = null; }
                            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_news_view", array("slug" => $this->getAttribute($_rel_, "url_slug"))), "html", null, true);
                            echo "\">";
                            if (isset($context["rel"])) { $_rel_ = $context["rel"]; } else { $_rel_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_rel_, "title"), "html", null, true);
                            echo "</a></li>
\t\t\t\t\t\t\t";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['rel'], $context['_parent'], $context['loop']);
                        $context = array_merge($_parent, array_intersect_key($context, $_parent));
                        // line 32
                        echo "\t\t\t\t\t\t</ul>
\t\t\t\t\t";
                    } elseif (($_itemtype_ == "feedback")) {
                        // line 34
                        echo "\t\t\t\t\t\t<h5>";
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.feedback.related_feedback");
                        echo "</h5>
\t\t\t\t\t\t<ul>
\t\t\t\t\t\t\t";
                        // line 36
                        if (isset($context["items"])) { $_items_ = $context["items"]; } else { $_items_ = null; }
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($_items_);
                        foreach ($context['_seq'] as $context["_key"] => $context["rel"]) {
                            // line 37
                            echo "\t\t\t\t\t\t\t\t<li> <a href=\"";
                            if (isset($context["rel"])) { $_rel_ = $context["rel"]; } else { $_rel_ = null; }
                            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_feedback_view", array("slug" => $this->getAttribute($_rel_, "url_slug"))), "html", null, true);
                            echo "\">";
                            if (isset($context["rel"])) { $_rel_ = $context["rel"]; } else { $_rel_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_rel_, "title"), "html", null, true);
                            echo "</a></li>
\t\t\t\t\t\t\t";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['rel'], $context['_parent'], $context['loop']);
                        $context = array_merge($_parent, array_intersect_key($context, $_parent));
                        // line 39
                        echo "\t\t\t\t\t\t</ul>
\t\t\t\t\t";
                    }
                    // line 41
                    echo "\t\t\t\t</td>

\t\t\t\t";
                    // line 43
                    if (isset($context["count"])) { $_count_ = $context["count"]; } else { $_count_ = null; }
                    $context["count"] = ($_count_ + 1);
                    // line 44
                    echo "\t\t\t\t";
                    if (isset($context["count"])) { $_count_ = $context["count"]; } else { $_count_ = null; }
                    if ((($_count_ % 2) == 0)) {
                        echo "</tr><tr>";
                    }
                    // line 45
                    echo "\t\t\t";
                }
                // line 46
                echo "\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['itemtype'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 47
            echo "\t</tr></table>
</div>
";
        }
    }

    public function getTemplateName()
    {
        return "UserBundle:Common:related-box.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  341 => 133,  192 => 54,  321 => 107,  243 => 78,  793 => 351,  780 => 348,  758 => 341,  700 => 303,  686 => 292,  652 => 274,  638 => 266,  620 => 254,  545 => 218,  523 => 203,  494 => 183,  459 => 163,  438 => 150,  351 => 116,  347 => 114,  402 => 142,  268 => 67,  430 => 120,  411 => 136,  379 => 101,  322 => 94,  315 => 92,  289 => 84,  284 => 93,  255 => 65,  234 => 63,  1133 => 64,  1124 => 57,  1121 => 56,  1116 => 55,  1113 => 54,  1108 => 51,  1103 => 43,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 346,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 329,  996 => 320,  989 => 316,  985 => 315,  981 => 314,  977 => 313,  970 => 311,  966 => 310,  955 => 303,  952 => 302,  943 => 299,  936 => 296,  930 => 293,  919 => 290,  917 => 289,  908 => 287,  905 => 286,  896 => 280,  891 => 278,  877 => 270,  862 => 267,  857 => 265,  837 => 253,  832 => 250,  827 => 248,  821 => 247,  803 => 243,  778 => 235,  769 => 233,  765 => 232,  753 => 229,  746 => 225,  743 => 224,  735 => 220,  730 => 218,  720 => 213,  717 => 212,  712 => 209,  691 => 204,  678 => 202,  654 => 196,  587 => 239,  576 => 179,  539 => 171,  517 => 169,  471 => 155,  441 => 151,  437 => 142,  418 => 115,  386 => 125,  373 => 120,  304 => 102,  270 => 80,  265 => 77,  229 => 74,  477 => 135,  455 => 150,  448 => 164,  429 => 138,  407 => 95,  399 => 93,  389 => 126,  375 => 123,  358 => 116,  349 => 138,  335 => 131,  327 => 102,  298 => 112,  280 => 56,  249 => 99,  194 => 50,  142 => 24,  344 => 113,  318 => 106,  306 => 107,  295 => 98,  357 => 119,  300 => 130,  286 => 101,  276 => 108,  269 => 53,  254 => 67,  128 => 32,  237 => 44,  165 => 41,  122 => 30,  798 => 242,  770 => 113,  759 => 112,  748 => 337,  731 => 108,  721 => 107,  718 => 106,  708 => 104,  696 => 102,  617 => 188,  590 => 91,  553 => 87,  550 => 176,  540 => 84,  533 => 82,  500 => 186,  493 => 72,  489 => 181,  482 => 69,  467 => 67,  464 => 165,  458 => 64,  452 => 62,  449 => 156,  415 => 55,  382 => 124,  372 => 126,  361 => 82,  356 => 48,  339 => 97,  302 => 42,  285 => 40,  258 => 37,  123 => 32,  108 => 29,  424 => 135,  394 => 86,  380 => 80,  338 => 113,  319 => 66,  316 => 65,  312 => 110,  290 => 102,  267 => 88,  206 => 83,  110 => 25,  240 => 95,  224 => 35,  219 => 71,  217 => 73,  202 => 82,  186 => 46,  170 => 43,  100 => 17,  67 => 20,  14 => 1,  1096 => 291,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 332,  1013 => 275,  1007 => 274,  1002 => 272,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 300,  940 => 298,  937 => 248,  928 => 244,  926 => 243,  915 => 237,  912 => 236,  903 => 231,  898 => 230,  892 => 229,  889 => 228,  887 => 227,  884 => 226,  876 => 222,  874 => 269,  871 => 220,  863 => 214,  861 => 213,  858 => 212,  850 => 263,  843 => 206,  840 => 205,  815 => 245,  812 => 244,  808 => 199,  804 => 120,  799 => 196,  791 => 190,  785 => 238,  775 => 184,  771 => 183,  754 => 340,  728 => 170,  726 => 169,  723 => 168,  715 => 105,  711 => 163,  709 => 162,  706 => 161,  698 => 208,  694 => 156,  692 => 155,  689 => 154,  681 => 150,  677 => 149,  675 => 148,  663 => 143,  661 => 277,  650 => 195,  646 => 136,  629 => 129,  627 => 128,  625 => 127,  622 => 126,  598 => 119,  592 => 117,  586 => 115,  575 => 233,  566 => 103,  556 => 100,  554 => 177,  541 => 216,  536 => 91,  515 => 86,  511 => 85,  509 => 84,  488 => 164,  486 => 78,  483 => 77,  465 => 73,  463 => 72,  450 => 65,  432 => 60,  419 => 105,  371 => 46,  362 => 43,  353 => 73,  337 => 109,  333 => 105,  309 => 109,  303 => 115,  299 => 30,  291 => 96,  272 => 54,  261 => 95,  253 => 82,  239 => 64,  235 => 84,  213 => 86,  200 => 64,  198 => 53,  159 => 47,  149 => 45,  146 => 22,  131 => 34,  116 => 39,  79 => 18,  74 => 20,  71 => 11,  836 => 320,  817 => 319,  814 => 318,  811 => 317,  805 => 313,  787 => 115,  779 => 306,  776 => 305,  773 => 347,  761 => 296,  751 => 175,  747 => 293,  742 => 336,  739 => 291,  736 => 287,  724 => 214,  705 => 278,  702 => 277,  688 => 265,  680 => 263,  667 => 261,  662 => 260,  656 => 259,  649 => 258,  644 => 97,  641 => 194,  624 => 255,  613 => 187,  607 => 93,  597 => 225,  591 => 185,  584 => 218,  579 => 234,  563 => 230,  559 => 208,  551 => 221,  547 => 205,  537 => 201,  524 => 191,  512 => 185,  507 => 76,  504 => 182,  498 => 178,  485 => 176,  480 => 175,  472 => 171,  466 => 153,  460 => 71,  447 => 163,  442 => 162,  434 => 110,  428 => 144,  422 => 106,  404 => 149,  368 => 136,  364 => 83,  340 => 131,  334 => 111,  330 => 129,  325 => 100,  292 => 116,  287 => 115,  282 => 124,  279 => 109,  273 => 107,  266 => 105,  256 => 94,  252 => 93,  228 => 71,  218 => 78,  201 => 91,  64 => 16,  51 => 15,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 793,  2042 => 790,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 774,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 745,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 707,  1829 => 706,  1827 => 705,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 689,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 676,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 666,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 638,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 617,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 611,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 602,  1559 => 601,  1552 => 598,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 584,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 573,  1477 => 567,  1473 => 566,  1467 => 562,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 520,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 511,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 481,  1257 => 480,  1254 => 479,  1250 => 478,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 460,  1196 => 458,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 452,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 445,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 425,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 421,  1099 => 420,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 412,  1076 => 359,  1070 => 407,  1057 => 352,  1052 => 404,  1045 => 347,  1040 => 397,  1036 => 283,  1032 => 395,  1028 => 394,  1023 => 393,  1020 => 392,  1016 => 391,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 381,  979 => 379,  974 => 312,  967 => 373,  962 => 371,  958 => 304,  954 => 253,  950 => 368,  945 => 367,  942 => 250,  938 => 365,  934 => 364,  927 => 361,  923 => 292,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 272,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 207,  841 => 321,  835 => 312,  830 => 249,  826 => 309,  822 => 308,  818 => 246,  813 => 306,  810 => 305,  806 => 304,  802 => 197,  795 => 118,  792 => 239,  789 => 350,  784 => 295,  782 => 187,  777 => 291,  772 => 289,  768 => 345,  763 => 343,  760 => 231,  756 => 230,  752 => 284,  745 => 281,  741 => 280,  738 => 335,  732 => 171,  719 => 273,  714 => 280,  710 => 310,  704 => 267,  699 => 103,  695 => 264,  690 => 263,  687 => 203,  683 => 261,  679 => 288,  672 => 284,  668 => 256,  665 => 201,  658 => 141,  645 => 270,  640 => 247,  634 => 96,  628 => 193,  623 => 238,  619 => 237,  611 => 248,  606 => 234,  603 => 243,  599 => 242,  595 => 231,  583 => 114,  580 => 180,  573 => 221,  560 => 101,  543 => 172,  538 => 209,  534 => 208,  530 => 170,  526 => 89,  521 => 80,  518 => 204,  514 => 78,  510 => 202,  503 => 75,  496 => 197,  490 => 193,  484 => 192,  474 => 190,  470 => 168,  446 => 144,  440 => 184,  436 => 147,  431 => 146,  425 => 117,  416 => 104,  412 => 98,  408 => 112,  403 => 172,  400 => 111,  396 => 133,  392 => 169,  385 => 166,  381 => 125,  367 => 117,  363 => 155,  359 => 118,  355 => 115,  350 => 112,  346 => 71,  343 => 70,  328 => 127,  324 => 138,  313 => 122,  307 => 132,  301 => 101,  288 => 27,  283 => 72,  271 => 107,  257 => 84,  251 => 64,  238 => 34,  233 => 94,  195 => 49,  191 => 62,  187 => 47,  183 => 72,  130 => 28,  88 => 29,  76 => 22,  115 => 29,  95 => 31,  655 => 275,  651 => 232,  648 => 231,  637 => 223,  633 => 130,  621 => 219,  618 => 218,  615 => 121,  604 => 186,  600 => 226,  588 => 116,  585 => 204,  582 => 203,  571 => 104,  567 => 178,  555 => 207,  552 => 190,  549 => 215,  544 => 186,  542 => 185,  535 => 212,  531 => 90,  519 => 189,  516 => 199,  513 => 168,  508 => 172,  506 => 83,  499 => 198,  495 => 167,  491 => 165,  481 => 162,  478 => 68,  475 => 157,  469 => 154,  456 => 154,  451 => 186,  443 => 161,  439 => 147,  427 => 89,  423 => 139,  420 => 176,  409 => 54,  405 => 135,  401 => 132,  391 => 129,  387 => 129,  384 => 132,  378 => 123,  365 => 122,  360 => 120,  348 => 136,  336 => 111,  332 => 140,  329 => 134,  323 => 101,  310 => 92,  305 => 132,  277 => 23,  274 => 90,  263 => 104,  259 => 68,  247 => 110,  244 => 65,  241 => 62,  222 => 63,  210 => 85,  207 => 96,  204 => 56,  184 => 46,  181 => 46,  167 => 39,  157 => 65,  96 => 15,  421 => 144,  417 => 137,  414 => 151,  406 => 143,  398 => 129,  393 => 132,  390 => 135,  376 => 127,  369 => 122,  366 => 136,  352 => 139,  345 => 98,  342 => 109,  331 => 66,  326 => 96,  320 => 137,  317 => 124,  314 => 33,  311 => 104,  308 => 60,  297 => 101,  293 => 128,  281 => 92,  278 => 110,  275 => 68,  264 => 87,  260 => 103,  248 => 70,  245 => 97,  242 => 74,  231 => 36,  227 => 42,  215 => 83,  212 => 69,  209 => 54,  197 => 31,  177 => 44,  171 => 66,  161 => 39,  132 => 39,  121 => 48,  105 => 25,  99 => 34,  81 => 20,  77 => 18,  180 => 71,  176 => 69,  156 => 28,  143 => 36,  139 => 51,  118 => 39,  189 => 30,  185 => 67,  173 => 35,  166 => 67,  152 => 23,  174 => 42,  164 => 62,  154 => 55,  150 => 42,  137 => 34,  133 => 32,  127 => 33,  107 => 30,  102 => 34,  83 => 25,  78 => 19,  53 => 13,  23 => 6,  42 => 11,  138 => 30,  134 => 56,  109 => 27,  103 => 25,  97 => 31,  94 => 27,  84 => 24,  75 => 16,  69 => 19,  66 => 20,  54 => 14,  44 => 12,  230 => 93,  226 => 92,  203 => 51,  193 => 52,  188 => 68,  182 => 29,  178 => 45,  168 => 44,  163 => 79,  160 => 77,  155 => 36,  148 => 37,  145 => 54,  140 => 38,  136 => 36,  125 => 31,  120 => 30,  113 => 31,  101 => 29,  92 => 23,  89 => 22,  85 => 28,  73 => 17,  62 => 17,  59 => 15,  56 => 13,  41 => 11,  126 => 33,  119 => 33,  111 => 37,  106 => 35,  98 => 26,  93 => 23,  86 => 28,  70 => 34,  60 => 14,  28 => 8,  36 => 6,  114 => 29,  104 => 28,  91 => 17,  80 => 23,  63 => 16,  58 => 17,  40 => 8,  34 => 8,  45 => 10,  61 => 18,  55 => 11,  48 => 11,  39 => 10,  35 => 9,  31 => 4,  26 => 7,  21 => 5,  46 => 7,  29 => 7,  57 => 16,  50 => 12,  47 => 12,  38 => 10,  33 => 9,  49 => 16,  32 => 7,  246 => 79,  236 => 76,  232 => 43,  225 => 64,  221 => 89,  216 => 34,  214 => 98,  211 => 61,  208 => 33,  205 => 32,  199 => 56,  196 => 55,  190 => 51,  179 => 28,  175 => 67,  172 => 44,  169 => 43,  162 => 48,  158 => 25,  153 => 41,  151 => 63,  147 => 32,  144 => 42,  141 => 38,  135 => 51,  129 => 38,  124 => 36,  117 => 32,  112 => 30,  90 => 25,  87 => 22,  82 => 20,  72 => 20,  68 => 10,  65 => 18,  52 => 10,  43 => 13,  37 => 8,  30 => 9,  27 => 5,  25 => 6,  24 => 5,  22 => 5,  19 => 4,);
    }
}
