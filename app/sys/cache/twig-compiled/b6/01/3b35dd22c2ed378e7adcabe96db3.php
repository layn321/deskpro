<?php

/* AdminBundle:Main:onboard-macro.html.twig */
class __TwigTemplate_b6013b35dd22c2ed378e7adcabe96db3 extends \Application\DeskPRO\Twig\Template
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
        // line 11
        echo "
";
    }

    // line 1
    public function getbegin($_onboard = null, $_id = null, $_title = null)
    {
        $context = $this->env->mergeGlobals(array(
            "onboard" => $_onboard,
            "id" => $_id,
            "title" => $_title,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 2
            echo "\t<li class=\"onboard-";
            if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
            echo twig_escape_filter($this->env, $_id_, "html", null, true);
            echo " ";
            if (isset($context["onboard"])) { $_onboard_ = $context["onboard"]; } else { $_onboard_ = null; }
            if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
            if ($this->getAttribute($_onboard_, "isDismissed", array(0 => $_id_), "method")) {
                echo "dismissed";
            } elseif ($this->getAttribute($_onboard_, "isFinished", array(0 => $_id_), "method")) {
                echo "finished";
            }
            echo "\" data-task-id=\"";
            if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
            echo twig_escape_filter($this->env, $_id_, "html", null, true);
            echo "\">
\t\t<header>
\t\t\t<div class=\"title\">
\t\t\t\t<h3>I want to...</h3>
\t\t\t\t<h2><i></i>";
            // line 6
            if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
            echo twig_escape_filter($this->env, $_title_, "html", null, true);
            echo "</h2>
\t\t\t</div>
\t\t</header>
\t\t<article>
";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 12
    public function getend()
    {
        $context = $this->env->getGlobals();

        $blocks = array();

        ob_start();
        try {
            // line 13
            echo "\t\t\t<nav>
\t\t\t\t<button class=\"btn-dismiss\"><span>Not Interested</span></button>
\t\t\t\t<button class=\"btn-done\"><span>Done</span></button>
\t\t\t</nav>
\t\t</article>
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
        return "AdminBundle:Main:onboard-macro.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  657 => 287,  572 => 251,  609 => 459,  20 => 1,  659 => 274,  562 => 246,  548 => 238,  558 => 94,  479 => 82,  589 => 443,  457 => 211,  413 => 150,  953 => 406,  948 => 403,  935 => 394,  929 => 391,  916 => 382,  864 => 365,  844 => 348,  816 => 342,  807 => 339,  801 => 338,  774 => 337,  766 => 328,  737 => 314,  685 => 300,  664 => 294,  635 => 281,  593 => 445,  546 => 414,  532 => 236,  865 => 221,  852 => 216,  838 => 208,  820 => 201,  781 => 333,  764 => 178,  725 => 164,  632 => 141,  602 => 265,  565 => 117,  529 => 111,  505 => 207,  487 => 213,  473 => 221,  1853 => 842,  1849 => 841,  1839 => 837,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 813,  1778 => 812,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 782,  1705 => 781,  1688 => 770,  1681 => 769,  1677 => 768,  1667 => 764,  1656 => 762,  1648 => 757,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 714,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 695,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 651,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 610,  1289 => 607,  1276 => 604,  1263 => 601,  1244 => 596,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 565,  1181 => 561,  1171 => 557,  1158 => 550,  1114 => 530,  1104 => 526,  1072 => 512,  1062 => 508,  1024 => 494,  1014 => 490,  1000 => 482,  990 => 478,  980 => 474,  960 => 466,  918 => 448,  888 => 376,  834 => 346,  673 => 342,  636 => 323,  462 => 198,  454 => 192,  1144 => 542,  1139 => 399,  1131 => 399,  1127 => 397,  1110 => 396,  1092 => 391,  1089 => 390,  1086 => 520,  1084 => 388,  1063 => 387,  1060 => 386,  1055 => 385,  1050 => 384,  1035 => 372,  1019 => 267,  1003 => 263,  959 => 251,  900 => 377,  880 => 434,  870 => 430,  867 => 366,  859 => 362,  848 => 215,  839 => 197,  828 => 191,  823 => 188,  809 => 181,  800 => 178,  797 => 177,  794 => 176,  786 => 174,  740 => 162,  734 => 313,  703 => 354,  693 => 350,  630 => 278,  626 => 140,  614 => 138,  610 => 385,  581 => 440,  564 => 229,  525 => 236,  722 => 162,  697 => 256,  674 => 249,  671 => 425,  577 => 257,  569 => 250,  557 => 368,  502 => 218,  497 => 246,  445 => 205,  729 => 159,  684 => 261,  676 => 299,  669 => 254,  660 => 145,  647 => 243,  643 => 244,  601 => 175,  570 => 211,  522 => 200,  501 => 148,  296 => 67,  374 => 178,  631 => 111,  616 => 281,  608 => 266,  605 => 16,  596 => 264,  574 => 165,  561 => 209,  527 => 233,  433 => 93,  388 => 89,  426 => 177,  383 => 182,  461 => 18,  370 => 176,  395 => 189,  294 => 120,  223 => 49,  220 => 79,  492 => 395,  468 => 201,  444 => 193,  410 => 229,  397 => 174,  377 => 84,  262 => 113,  250 => 91,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 2108,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 1383,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 1303,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 1173,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 1041,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 970,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 538,  1126 => 916,  1106 => 900,  1100 => 897,  1026 => 841,  997 => 822,  983 => 816,  975 => 402,  956 => 800,  939 => 786,  902 => 753,  894 => 749,  879 => 373,  757 => 631,  727 => 608,  716 => 605,  670 => 297,  528 => 221,  476 => 393,  435 => 195,  354 => 172,  341 => 162,  192 => 52,  321 => 147,  243 => 106,  793 => 350,  780 => 348,  758 => 177,  700 => 312,  686 => 150,  652 => 274,  638 => 282,  620 => 139,  545 => 237,  523 => 110,  494 => 10,  459 => 226,  438 => 382,  351 => 153,  347 => 163,  402 => 367,  268 => 77,  430 => 201,  411 => 201,  379 => 181,  322 => 74,  315 => 110,  289 => 129,  284 => 128,  255 => 115,  234 => 55,  1133 => 400,  1124 => 534,  1121 => 56,  1116 => 55,  1113 => 403,  1108 => 51,  1103 => 394,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 500,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 329,  996 => 262,  989 => 316,  985 => 315,  981 => 257,  977 => 313,  970 => 91,  966 => 416,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 244,  919 => 290,  917 => 289,  908 => 444,  905 => 378,  896 => 280,  891 => 227,  877 => 270,  862 => 267,  857 => 265,  837 => 347,  832 => 250,  827 => 248,  821 => 247,  803 => 179,  778 => 389,  769 => 165,  765 => 386,  753 => 328,  746 => 175,  743 => 318,  735 => 170,  730 => 218,  720 => 363,  717 => 362,  712 => 156,  691 => 204,  678 => 149,  654 => 144,  587 => 14,  576 => 179,  539 => 241,  517 => 217,  471 => 190,  441 => 203,  437 => 189,  418 => 175,  386 => 195,  373 => 144,  304 => 151,  270 => 123,  265 => 161,  229 => 81,  477 => 138,  455 => 224,  448 => 164,  429 => 179,  407 => 119,  399 => 156,  389 => 176,  375 => 166,  358 => 79,  349 => 118,  335 => 128,  327 => 132,  298 => 144,  280 => 124,  249 => 94,  194 => 78,  142 => 38,  344 => 162,  318 => 181,  306 => 71,  295 => 124,  357 => 154,  300 => 135,  286 => 80,  276 => 87,  269 => 61,  254 => 98,  128 => 29,  237 => 83,  165 => 70,  122 => 33,  798 => 337,  770 => 179,  759 => 112,  748 => 337,  731 => 108,  721 => 305,  718 => 307,  708 => 295,  696 => 147,  617 => 461,  590 => 226,  553 => 225,  550 => 156,  540 => 84,  533 => 82,  500 => 397,  493 => 225,  489 => 242,  482 => 210,  467 => 210,  464 => 215,  458 => 193,  452 => 390,  449 => 132,  415 => 181,  382 => 172,  372 => 215,  361 => 141,  356 => 170,  339 => 139,  302 => 125,  285 => 259,  258 => 71,  123 => 43,  108 => 28,  424 => 198,  394 => 86,  380 => 121,  338 => 155,  319 => 145,  316 => 131,  312 => 87,  290 => 146,  267 => 116,  206 => 82,  110 => 35,  240 => 105,  224 => 87,  219 => 50,  217 => 102,  202 => 73,  186 => 68,  170 => 58,  100 => 32,  67 => 19,  14 => 1,  1096 => 524,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 836,  1013 => 275,  1007 => 274,  1002 => 823,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 402,  940 => 298,  937 => 395,  928 => 452,  926 => 243,  915 => 239,  912 => 236,  903 => 231,  898 => 440,  892 => 229,  889 => 748,  887 => 227,  884 => 374,  876 => 222,  874 => 215,  871 => 368,  863 => 214,  861 => 213,  858 => 212,  850 => 352,  843 => 206,  840 => 406,  815 => 245,  812 => 244,  808 => 199,  804 => 395,  799 => 196,  791 => 193,  785 => 238,  775 => 184,  771 => 183,  754 => 340,  728 => 309,  726 => 169,  723 => 168,  715 => 105,  711 => 152,  709 => 162,  706 => 155,  698 => 208,  694 => 308,  692 => 155,  689 => 302,  681 => 300,  677 => 149,  675 => 148,  663 => 276,  661 => 277,  650 => 246,  646 => 143,  629 => 129,  627 => 21,  625 => 272,  622 => 270,  598 => 174,  592 => 117,  586 => 170,  575 => 214,  566 => 241,  556 => 244,  554 => 240,  541 => 239,  536 => 225,  515 => 209,  511 => 108,  509 => 17,  488 => 208,  486 => 207,  483 => 341,  465 => 196,  463 => 216,  450 => 194,  432 => 211,  419 => 155,  371 => 165,  362 => 80,  353 => 78,  337 => 18,  333 => 134,  309 => 72,  303 => 70,  299 => 68,  291 => 66,  272 => 103,  261 => 72,  253 => 59,  239 => 56,  235 => 53,  213 => 89,  200 => 79,  198 => 58,  159 => 40,  149 => 34,  146 => 33,  131 => 35,  116 => 41,  79 => 24,  74 => 12,  71 => 20,  836 => 320,  817 => 398,  814 => 200,  811 => 317,  805 => 313,  787 => 657,  779 => 169,  776 => 305,  773 => 347,  761 => 296,  751 => 163,  747 => 325,  742 => 336,  739 => 171,  736 => 317,  724 => 214,  705 => 278,  702 => 601,  688 => 113,  680 => 278,  667 => 296,  662 => 293,  656 => 418,  649 => 285,  644 => 284,  641 => 241,  624 => 109,  613 => 460,  607 => 232,  597 => 221,  591 => 263,  584 => 259,  579 => 234,  563 => 162,  559 => 245,  551 => 235,  547 => 241,  537 => 90,  524 => 220,  512 => 227,  507 => 76,  504 => 213,  498 => 142,  485 => 230,  480 => 28,  472 => 139,  466 => 229,  460 => 391,  447 => 188,  442 => 185,  434 => 212,  428 => 11,  422 => 176,  404 => 149,  368 => 81,  364 => 173,  340 => 149,  334 => 154,  330 => 148,  325 => 126,  292 => 142,  287 => 63,  282 => 62,  279 => 147,  273 => 59,  266 => 102,  256 => 79,  252 => 109,  228 => 72,  218 => 86,  201 => 58,  64 => 20,  51 => 9,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 1705,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 737,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 731,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 725,  1559 => 601,  1552 => 720,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 708,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 645,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 624,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 1001,  1257 => 1000,  1254 => 479,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 569,  1196 => 958,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 938,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 388,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 898,  1099 => 393,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 880,  1076 => 359,  1070 => 875,  1057 => 352,  1052 => 504,  1045 => 382,  1040 => 377,  1036 => 283,  1032 => 496,  1028 => 274,  1023 => 393,  1020 => 392,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 821,  979 => 379,  974 => 256,  967 => 373,  962 => 803,  958 => 801,  954 => 253,  950 => 368,  945 => 367,  942 => 460,  938 => 365,  934 => 364,  927 => 361,  923 => 387,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 272,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 410,  841 => 210,  835 => 312,  830 => 249,  826 => 202,  822 => 308,  818 => 246,  813 => 183,  810 => 340,  806 => 180,  802 => 198,  795 => 336,  792 => 239,  789 => 349,  784 => 188,  782 => 187,  777 => 291,  772 => 289,  768 => 334,  763 => 327,  760 => 329,  756 => 230,  752 => 176,  745 => 281,  741 => 280,  738 => 379,  732 => 171,  719 => 279,  714 => 264,  710 => 310,  704 => 267,  699 => 303,  695 => 285,  690 => 263,  687 => 279,  683 => 346,  679 => 298,  672 => 255,  668 => 247,  665 => 201,  658 => 244,  645 => 270,  640 => 142,  634 => 413,  628 => 466,  623 => 238,  619 => 282,  611 => 268,  606 => 279,  603 => 267,  599 => 242,  595 => 132,  583 => 169,  580 => 100,  573 => 260,  560 => 422,  543 => 172,  538 => 232,  534 => 405,  530 => 202,  526 => 213,  521 => 226,  518 => 22,  514 => 222,  510 => 202,  503 => 75,  496 => 216,  490 => 214,  484 => 394,  474 => 202,  470 => 231,  446 => 318,  440 => 218,  436 => 113,  431 => 186,  425 => 193,  416 => 104,  412 => 98,  408 => 200,  403 => 177,  400 => 225,  396 => 299,  392 => 198,  385 => 170,  381 => 169,  367 => 182,  363 => 164,  359 => 79,  355 => 326,  350 => 94,  346 => 140,  343 => 143,  328 => 152,  324 => 164,  313 => 143,  307 => 108,  301 => 69,  288 => 116,  283 => 62,  271 => 99,  257 => 112,  251 => 58,  238 => 94,  233 => 52,  195 => 82,  191 => 81,  187 => 46,  183 => 45,  130 => 45,  88 => 28,  76 => 24,  115 => 43,  95 => 38,  655 => 275,  651 => 232,  648 => 231,  637 => 241,  633 => 272,  621 => 462,  618 => 269,  615 => 268,  604 => 186,  600 => 516,  588 => 305,  585 => 261,  582 => 260,  571 => 242,  567 => 95,  555 => 207,  552 => 190,  549 => 208,  544 => 230,  542 => 207,  535 => 237,  531 => 358,  519 => 87,  516 => 229,  513 => 216,  508 => 219,  506 => 401,  499 => 211,  495 => 396,  491 => 145,  481 => 229,  478 => 235,  475 => 184,  469 => 197,  456 => 197,  451 => 223,  443 => 132,  439 => 129,  427 => 185,  423 => 109,  420 => 208,  409 => 178,  405 => 148,  401 => 148,  391 => 173,  387 => 129,  384 => 160,  378 => 145,  365 => 161,  360 => 171,  348 => 191,  336 => 135,  332 => 150,  329 => 73,  323 => 135,  310 => 130,  305 => 69,  277 => 99,  274 => 120,  263 => 59,  259 => 97,  247 => 112,  244 => 84,  241 => 52,  222 => 96,  210 => 100,  207 => 80,  204 => 54,  184 => 47,  181 => 40,  167 => 38,  157 => 35,  96 => 25,  421 => 182,  417 => 71,  414 => 173,  406 => 170,  398 => 147,  393 => 177,  390 => 90,  376 => 85,  369 => 83,  366 => 174,  352 => 192,  345 => 152,  342 => 74,  331 => 140,  326 => 102,  320 => 130,  317 => 134,  314 => 136,  311 => 142,  308 => 141,  297 => 68,  293 => 119,  281 => 111,  278 => 106,  275 => 124,  264 => 109,  260 => 99,  248 => 95,  245 => 57,  242 => 94,  231 => 100,  227 => 50,  215 => 88,  212 => 83,  209 => 88,  197 => 41,  177 => 44,  171 => 40,  161 => 63,  132 => 47,  121 => 51,  105 => 32,  99 => 29,  81 => 21,  77 => 26,  180 => 76,  176 => 70,  156 => 55,  143 => 41,  139 => 52,  118 => 39,  189 => 68,  185 => 86,  173 => 37,  166 => 41,  152 => 35,  174 => 41,  164 => 64,  154 => 80,  150 => 52,  137 => 48,  133 => 70,  127 => 53,  107 => 35,  102 => 41,  83 => 13,  78 => 20,  53 => 7,  23 => 6,  42 => 9,  138 => 48,  134 => 46,  109 => 33,  103 => 25,  97 => 22,  94 => 31,  84 => 30,  75 => 25,  69 => 22,  66 => 20,  54 => 22,  44 => 7,  230 => 90,  226 => 97,  203 => 92,  193 => 90,  188 => 75,  182 => 74,  178 => 76,  168 => 42,  163 => 59,  160 => 39,  155 => 66,  148 => 58,  145 => 52,  140 => 56,  136 => 38,  125 => 48,  120 => 43,  113 => 39,  101 => 24,  92 => 25,  89 => 23,  85 => 21,  73 => 18,  62 => 19,  59 => 9,  56 => 8,  41 => 18,  126 => 44,  119 => 35,  111 => 46,  106 => 35,  98 => 31,  93 => 25,  86 => 22,  70 => 23,  60 => 17,  28 => 16,  36 => 6,  114 => 34,  104 => 36,  91 => 27,  80 => 20,  63 => 19,  58 => 19,  40 => 12,  34 => 11,  45 => 7,  61 => 13,  55 => 14,  48 => 12,  39 => 7,  35 => 5,  31 => 17,  26 => 7,  21 => 2,  46 => 15,  29 => 8,  57 => 6,  50 => 10,  47 => 9,  38 => 5,  33 => 9,  49 => 21,  32 => 4,  246 => 107,  236 => 91,  232 => 107,  225 => 59,  221 => 64,  216 => 65,  214 => 47,  211 => 46,  208 => 56,  205 => 61,  199 => 84,  196 => 91,  190 => 78,  179 => 39,  175 => 41,  172 => 65,  169 => 42,  162 => 69,  158 => 67,  153 => 60,  151 => 39,  147 => 61,  144 => 51,  141 => 53,  135 => 34,  129 => 54,  124 => 52,  117 => 29,  112 => 27,  90 => 22,  87 => 26,  82 => 27,  72 => 18,  68 => 20,  65 => 14,  52 => 16,  43 => 4,  37 => 2,  30 => 2,  27 => 1,  25 => 3,  24 => 1,  22 => 2,  19 => 11,);
    }
}
