<?php

/* AgentBundle:Twitter:run-search.html.twig */
class __TwigTemplate_1dd93035f982230f51a50cf194e1a243 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("AgentBundle::list-pane-layout.html.twig");

        $this->blocks = array(
            'dp_declare' => array($this, 'block_dp_declare'),
            'top' => array($this, 'block_top'),
            'pane_header' => array($this, 'block_pane_header'),
            'pane_content' => array($this, 'block_pane_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "AgentBundle::list-pane-layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_dp_declare($context, array $blocks = array())
    {
        // line 3
        echo "\t";
        echo $this->env->getExtension('deskpro_templating')->set_tplvar($context, "baseId", $this->env->getExtension('deskpro_templating')->elUid());
        echo "
";
    }

    // line 9
    public function block_top($context, array $blocks = array())
    {
        // line 10
        echo "\t<script>
\t\tpageMeta.fragmentClass = 'DeskPRO.Agent.PageFragment.ListPane.TwitterSearch';

\t\tpageMeta.url_fragment = '";
        // line 13
        if (isset($context["account"])) { $_account_ = $context["account"]; } else { $_account_ = null; }
        if (isset($context["search"])) { $_search_ = $context["search"]; } else { $_search_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->urlFragment("agent_twitter_run_search", array("account_id" => $this->getAttribute($_account_, "id"), "search_id" => $this->getAttribute($_search_, "id"))), "html", null, true);
        echo "';
\t\tpageMeta.accountId = '";
        // line 14
        if (isset($context["account"])) { $_account_ = $context["account"]; } else { $_account_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_account_, "id"));
        echo "';
\t\tpageMeta.currentPage = ";
        // line 15
        if (isset($context["page"])) { $_page_ = $context["page"]; } else { $_page_ = null; }
        echo twig_escape_filter($this->env, $_page_, "html", null, true);
        echo ";
\t\tpageMeta.perPage = ";
        // line 16
        if (isset($context["per_page"])) { $_per_page_ = $context["per_page"]; } else { $_per_page_ = null; }
        echo twig_escape_filter($this->env, $_per_page_, "html", null, true);
        echo ";
\t\tpageMeta.totalCount = ";
        // line 17
        if (isset($context["total_count"])) { $_total_count_ = $context["total_count"]; } else { $_total_count_ = null; }
        echo twig_escape_filter($this->env, $_total_count_, "html", null, true);
        echo ";

\t\tpageMeta.newSearchUrl = '";
        // line 19
        if (isset($context["account"])) { $_account_ = $context["account"]; } else { $_account_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_twitter_new_search", array("account_id" => $this->getAttribute($_account_, "id"))), "html", null, true);
        echo "';
\t\tpageMeta.statusListUrl = '";
        // line 20
        if (isset($context["account"])) { $_account_ = $context["account"]; } else { $_account_ = null; }
        if (isset($context["search"])) { $_search_ = $context["search"]; } else { $_search_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_twitter_run_search", array("account_id" => $this->getAttribute($_account_, "id"), "search_id" => $this->getAttribute($_search_, "id"))), "html", null, true);
        echo "?partial=1';
\t\tpageMeta.listRoute = 'agent_twitter_run_search';
\t\t";
        // line 22
        $this->env->loadTemplate("AgentBundle:Twitter:list-urls.js.twig")->display($context);
        // line 23
        echo "\t</script>
";
    }

    // line 30
    public function block_pane_header($context, array $blocks = array())
    {
        // line 31
        echo "\t<h1><i class=\"icon-dp-twitter\"></i> ";
        if (isset($context["total_count"])) { $_total_count_ = $context["total_count"]; } else { $_total_count_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.twitter.list_title", array("count" => $_total_count_));
        echo "</h1>
";
    }

    // line 38
    public function block_pane_content($context, array $blocks = array())
    {
        // line 39
        echo "
\t<div class=\"layout-content twitter\">
\t\t<header class=\"header\">
\t\t\t<div class=\"list-control-bar\">
\t\t\t\t<ul class=\"btn-controls\">
\t\t\t\t\t<li class=\"display-options display-options-trigger\"><a></a></li>
\t\t\t\t\t<li class=\"right\"><span class=\"sep\"></span></li>
\t\t\t\t</ul>

\t\t\t\t<ul style=\"display:none\" class=\"display-options-menu\">
\t\t\t\t\t<li><label><input name=\"archived\" type=\"checkbox\" /> ";
        // line 49
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.twitter.show_archived_tweets");
        echo "</label></li>
\t\t\t\t</ul>
\t\t\t</div>
\t\t</header>
\t\t";
        // line 53
        $this->env->loadTemplate("AgentBundle:TwitterStatus:list-massactions-bar.html.twig")->display($context);
        // line 54
        echo "
\t\t<div class=\"new-tweet-list-indicator\"></div>

\t\t<div class=\"content\">
\t\t\t";
        // line 58
        $this->env->loadTemplate("AgentBundle:Twitter:part-search.html.twig")->display($context);
        // line 59
        echo "\t\t</div>

\t\t";
        // line 61
        $this->env->loadTemplate("AgentBundle:TwitterStatus:list-footer.html.twig")->display($context);
        // line 62
        echo "\t</div>

";
    }

    public function getTemplateName()
    {
        return "AgentBundle:Twitter:run-search.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1017 => 203,  994 => 192,  951 => 186,  1071 => 285,  1058 => 278,  1054 => 221,  1041 => 273,  1011 => 199,  869 => 223,  767 => 187,  3071 => 913,  3059 => 903,  3052 => 899,  3047 => 898,  3043 => 897,  3037 => 894,  3028 => 891,  3019 => 887,  3016 => 886,  3014 => 885,  3006 => 880,  2996 => 873,  2989 => 870,  2979 => 866,  2970 => 860,  2964 => 858,  2958 => 855,  2954 => 854,  2945 => 848,  2939 => 845,  2929 => 838,  2922 => 834,  2912 => 827,  2906 => 824,  2900 => 820,  2896 => 818,  2888 => 817,  2883 => 816,  2879 => 815,  2872 => 813,  2869 => 812,  2864 => 810,  2857 => 806,  2848 => 801,  2832 => 799,  2827 => 798,  2817 => 797,  2814 => 796,  2812 => 795,  2802 => 794,  2786 => 792,  2779 => 791,  2775 => 790,  2770 => 789,  2762 => 788,  2755 => 787,  2751 => 786,  2747 => 785,  2741 => 784,  2729 => 776,  2721 => 772,  2711 => 767,  2698 => 757,  2690 => 752,  2684 => 749,  2651 => 732,  2642 => 730,  2634 => 725,  2628 => 724,  2622 => 723,  2619 => 722,  2615 => 721,  2612 => 720,  2605 => 718,  2599 => 717,  2591 => 714,  2587 => 712,  2579 => 708,  2574 => 707,  2569 => 705,  2566 => 704,  2562 => 703,  2554 => 699,  2549 => 698,  2544 => 696,  2541 => 695,  2524 => 689,  2519 => 687,  2516 => 686,  2512 => 685,  2504 => 681,  2494 => 678,  2479 => 672,  2474 => 671,  2466 => 668,  2462 => 667,  2444 => 660,  2438 => 659,  2435 => 658,  2430 => 656,  2417 => 651,  2412 => 649,  2409 => 648,  2405 => 647,  2397 => 643,  2387 => 640,  2375 => 635,  2368 => 633,  2362 => 631,  2356 => 630,  2353 => 629,  2348 => 627,  2345 => 626,  2341 => 625,  2336 => 622,  2329 => 620,  2323 => 618,  2317 => 617,  2314 => 616,  2309 => 614,  2306 => 613,  2302 => 612,  2286 => 606,  2280 => 604,  2274 => 603,  2271 => 602,  2266 => 600,  2263 => 599,  2259 => 598,  2249 => 594,  2244 => 593,  2239 => 591,  2232 => 588,  2226 => 586,  2221 => 585,  2210 => 582,  2197 => 581,  2180 => 579,  2169 => 576,  2150 => 575,  2140 => 572,  2131 => 570,  2110 => 567,  2106 => 566,  2061 => 561,  2026 => 554,  2018 => 553,  2000 => 550,  1990 => 548,  1962 => 540,  1956 => 536,  1939 => 532,  1935 => 531,  1896 => 520,  1888 => 519,  1881 => 518,  1869 => 517,  1857 => 516,  1848 => 514,  1844 => 512,  1832 => 508,  1811 => 502,  1808 => 501,  1786 => 492,  1779 => 490,  1769 => 485,  1762 => 483,  1758 => 482,  1735 => 472,  1731 => 470,  1712 => 465,  1707 => 463,  1696 => 461,  1683 => 454,  1679 => 452,  1655 => 445,  1650 => 444,  1647 => 443,  1635 => 439,  1607 => 427,  1597 => 423,  1541 => 402,  1523 => 393,  1495 => 389,  1485 => 387,  1455 => 383,  1450 => 382,  1441 => 378,  1435 => 377,  1419 => 375,  1407 => 373,  1362 => 364,  1347 => 361,  1296 => 352,  1258 => 348,  1253 => 347,  1177 => 325,  1151 => 320,  1118 => 314,  1330 => 4,  1327 => 3,  1322 => 2,  1290 => 297,  1283 => 295,  1278 => 292,  1271 => 349,  1230 => 277,  1190 => 265,  1166 => 258,  1138 => 251,  642 => 270,  1264 => 464,  1259 => 462,  1227 => 448,  1211 => 336,  1399 => 568,  1387 => 560,  1358 => 555,  1334 => 357,  1316 => 529,  1298 => 520,  1195 => 478,  1148 => 253,  1120 => 453,  1117 => 245,  1093 => 440,  788 => 201,  612 => 256,  1394 => 446,  1385 => 442,  1382 => 441,  1369 => 433,  1363 => 432,  1360 => 431,  1350 => 429,  1305 => 523,  1300 => 409,  1281 => 404,  1277 => 350,  1265 => 398,  1255 => 396,  1247 => 393,  1234 => 387,  1202 => 270,  1199 => 374,  1187 => 264,  1162 => 365,  1136 => 317,  1128 => 352,  1122 => 248,  1069 => 299,  968 => 387,  846 => 188,  1183 => 449,  1132 => 460,  1097 => 341,  957 => 272,  907 => 278,  875 => 263,  653 => 274,  1329 => 405,  1309 => 354,  1303 => 399,  1297 => 398,  1292 => 397,  1280 => 394,  1249 => 346,  1237 => 279,  1205 => 409,  1200 => 408,  1194 => 440,  1178 => 430,  1175 => 447,  1170 => 375,  1135 => 362,  1105 => 428,  1078 => 335,  1068 => 388,  1048 => 417,  961 => 273,  922 => 182,  750 => 138,  842 => 263,  1038 => 212,  904 => 198,  882 => 227,  831 => 267,  860 => 314,  790 => 286,  733 => 230,  707 => 185,  744 => 137,  873 => 349,  824 => 267,  762 => 243,  713 => 225,  578 => 239,  2739 => 1153,  2735 => 1152,  2732 => 1151,  2725 => 1146,  2712 => 1144,  2707 => 1143,  2692 => 1131,  2688 => 1130,  2683 => 1127,  2680 => 748,  2677 => 1125,  2671 => 743,  2658 => 735,  2653 => 1118,  2645 => 731,  2641 => 1112,  2636 => 726,  2632 => 1108,  2629 => 1107,  2626 => 1106,  2606 => 1089,  2602 => 1088,  2598 => 1087,  2594 => 1086,  2590 => 1085,  2586 => 1084,  2572 => 1073,  2568 => 1072,  2564 => 1071,  2560 => 1070,  2556 => 1069,  2552 => 1068,  2537 => 694,  2533 => 1055,  2529 => 690,  2525 => 1053,  2521 => 1052,  2503 => 1040,  2499 => 680,  2495 => 1038,  2491 => 677,  2487 => 676,  2483 => 1035,  2469 => 669,  2465 => 1023,  2461 => 1022,  2457 => 664,  2453 => 1020,  2449 => 1019,  2426 => 999,  2422 => 652,  2418 => 997,  2414 => 996,  2410 => 995,  2406 => 994,  2392 => 642,  2388 => 982,  2384 => 639,  2380 => 638,  2376 => 979,  2372 => 978,  2358 => 967,  2354 => 966,  2350 => 965,  2346 => 964,  2342 => 963,  2338 => 962,  2324 => 951,  2320 => 950,  2316 => 949,  2312 => 948,  2308 => 947,  2304 => 946,  2295 => 939,  2282 => 937,  2277 => 936,  2269 => 931,  2265 => 930,  2260 => 927,  2256 => 597,  2253 => 925,  2230 => 901,  2220 => 899,  2216 => 898,  2211 => 896,  2208 => 895,  2198 => 893,  2194 => 580,  2189 => 890,  2184 => 889,  2177 => 578,  2173 => 577,  2164 => 877,  2151 => 875,  2146 => 574,  2143 => 873,  2141 => 864,  2134 => 571,  2130 => 859,  2122 => 853,  2120 => 852,  2113 => 848,  2105 => 846,  2096 => 839,  2083 => 564,  2078 => 836,  2073 => 562,  2067 => 830,  2062 => 828,  2058 => 827,  2054 => 826,  2050 => 560,  2045 => 557,  2038 => 822,  2034 => 821,  2022 => 814,  2009 => 812,  1999 => 809,  1993 => 805,  1988 => 803,  1984 => 802,  1980 => 801,  1976 => 546,  1971 => 799,  1968 => 798,  1964 => 797,  1960 => 796,  1954 => 535,  1946 => 788,  1942 => 533,  1938 => 786,  1932 => 530,  1927 => 780,  1919 => 527,  1915 => 777,  1911 => 776,  1906 => 775,  1903 => 774,  1899 => 773,  1895 => 772,  1886 => 768,  1883 => 764,  1871 => 759,  1866 => 756,  1863 => 755,  1842 => 752,  1825 => 749,  1818 => 748,  1815 => 503,  1800 => 499,  1793 => 741,  1772 => 729,  1761 => 721,  1757 => 720,  1751 => 717,  1740 => 709,  1719 => 697,  1715 => 696,  1686 => 673,  1676 => 669,  1665 => 661,  1661 => 446,  1645 => 442,  1638 => 649,  1630 => 438,  1625 => 435,  1622 => 641,  1598 => 637,  1577 => 416,  1558 => 408,  1549 => 405,  1546 => 626,  1535 => 618,  1531 => 617,  1514 => 606,  1510 => 605,  1504 => 391,  1493 => 594,  1483 => 590,  1464 => 580,  1458 => 577,  1443 => 568,  1437 => 565,  1426 => 557,  1405 => 545,  1401 => 544,  1395 => 541,  1390 => 538,  1381 => 368,  1373 => 531,  1348 => 521,  1343 => 520,  1335 => 515,  1321 => 510,  1318 => 549,  1299 => 503,  1294 => 298,  1282 => 496,  1269 => 466,  1260 => 397,  1240 => 344,  1221 => 339,  1216 => 338,  1210 => 272,  1206 => 445,  1193 => 467,  1189 => 466,  1155 => 468,  1150 => 419,  1022 => 312,  1006 => 299,  988 => 398,  969 => 187,  965 => 253,  921 => 286,  878 => 275,  866 => 222,  854 => 254,  819 => 322,  796 => 144,  1713 => 683,  1700 => 681,  1685 => 674,  1662 => 670,  1643 => 669,  1640 => 441,  1631 => 663,  1618 => 661,  1613 => 430,  1608 => 639,  1605 => 656,  1602 => 424,  1589 => 645,  1586 => 644,  1583 => 643,  1554 => 406,  1547 => 404,  1521 => 602,  1508 => 600,  1499 => 390,  1491 => 593,  1482 => 386,  1472 => 582,  1468 => 581,  1465 => 579,  1457 => 573,  1445 => 380,  1422 => 556,  1416 => 553,  1410 => 548,  1397 => 546,  1392 => 545,  1386 => 541,  1377 => 532,  1370 => 530,  1357 => 528,  1352 => 554,  1341 => 428,  1314 => 510,  1311 => 1,  1275 => 493,  1248 => 284,  1238 => 488,  1225 => 476,  1220 => 275,  1209 => 466,  1185 => 385,  1182 => 431,  1159 => 421,  1154 => 254,  1130 => 438,  1125 => 407,  1101 => 308,  1074 => 286,  1056 => 326,  1046 => 216,  1043 => 293,  1030 => 397,  1027 => 289,  947 => 247,  925 => 242,  913 => 259,  893 => 231,  881 => 253,  847 => 158,  829 => 209,  825 => 259,  1083 => 237,  995 => 399,  984 => 257,  963 => 292,  941 => 354,  851 => 367,  682 => 170,  1365 => 365,  1359 => 545,  1349 => 539,  1336 => 427,  1331 => 424,  1323 => 420,  1319 => 530,  1312 => 402,  1284 => 475,  1272 => 401,  1268 => 509,  1261 => 288,  1251 => 285,  1245 => 483,  1231 => 341,  1207 => 271,  1197 => 267,  1180 => 326,  1173 => 457,  1169 => 259,  1157 => 323,  1147 => 438,  1109 => 330,  1065 => 297,  1059 => 7,  1047 => 274,  1044 => 215,  1033 => 381,  1009 => 357,  991 => 363,  987 => 404,  973 => 294,  931 => 202,  924 => 261,  911 => 347,  906 => 81,  885 => 337,  872 => 335,  855 => 219,  749 => 240,  701 => 172,  594 => 180,  1163 => 257,  1143 => 318,  1087 => 420,  1077 => 300,  1051 => 325,  1037 => 223,  1010 => 301,  999 => 407,  932 => 352,  899 => 306,  895 => 404,  933 => 185,  914 => 238,  909 => 323,  833 => 284,  783 => 193,  755 => 303,  666 => 214,  453 => 174,  639 => 209,  568 => 176,  520 => 200,  657 => 184,  572 => 201,  609 => 232,  20 => 1,  659 => 217,  562 => 158,  548 => 180,  558 => 197,  479 => 88,  589 => 154,  457 => 175,  413 => 224,  953 => 249,  948 => 267,  935 => 394,  929 => 243,  916 => 180,  864 => 365,  844 => 214,  816 => 342,  807 => 291,  801 => 145,  774 => 257,  766 => 312,  737 => 297,  685 => 218,  664 => 163,  635 => 102,  593 => 199,  546 => 153,  532 => 206,  865 => 166,  852 => 241,  838 => 233,  820 => 149,  781 => 198,  764 => 278,  725 => 250,  632 => 268,  602 => 170,  565 => 145,  529 => 153,  505 => 123,  487 => 89,  473 => 212,  1853 => 515,  1849 => 753,  1839 => 751,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 733,  1778 => 732,  1770 => 807,  1749 => 478,  1738 => 793,  1728 => 469,  1721 => 788,  1717 => 787,  1709 => 693,  1705 => 781,  1688 => 675,  1681 => 769,  1677 => 451,  1667 => 449,  1656 => 762,  1648 => 655,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 413,  1545 => 719,  1534 => 606,  1527 => 395,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 593,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 372,  1400 => 370,  1383 => 540,  1379 => 557,  1356 => 632,  1351 => 362,  1338 => 359,  1332 => 617,  1315 => 613,  1302 => 353,  1289 => 406,  1276 => 604,  1263 => 601,  1244 => 456,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 332,  1181 => 561,  1171 => 446,  1158 => 550,  1114 => 428,  1104 => 465,  1072 => 333,  1062 => 7,  1024 => 267,  1014 => 265,  1000 => 368,  990 => 303,  980 => 393,  960 => 466,  918 => 285,  888 => 80,  834 => 153,  673 => 190,  636 => 145,  462 => 118,  454 => 127,  1144 => 463,  1139 => 356,  1131 => 316,  1127 => 434,  1110 => 312,  1092 => 459,  1089 => 239,  1086 => 238,  1084 => 337,  1063 => 232,  1060 => 231,  1055 => 295,  1050 => 275,  1035 => 291,  1019 => 266,  1003 => 401,  959 => 387,  900 => 178,  880 => 276,  870 => 250,  867 => 249,  859 => 164,  848 => 271,  839 => 155,  828 => 150,  823 => 208,  809 => 179,  800 => 241,  797 => 267,  794 => 177,  786 => 285,  740 => 136,  734 => 268,  703 => 297,  693 => 297,  630 => 166,  626 => 176,  614 => 257,  610 => 100,  581 => 143,  564 => 138,  525 => 138,  722 => 226,  697 => 282,  674 => 270,  671 => 165,  577 => 97,  569 => 187,  557 => 179,  502 => 187,  497 => 198,  445 => 78,  729 => 306,  684 => 290,  676 => 178,  669 => 282,  660 => 105,  647 => 271,  643 => 229,  601 => 195,  570 => 129,  522 => 132,  501 => 91,  296 => 103,  374 => 88,  631 => 242,  616 => 152,  608 => 150,  605 => 193,  596 => 134,  574 => 163,  561 => 126,  527 => 165,  433 => 104,  388 => 92,  426 => 97,  383 => 105,  461 => 176,  370 => 87,  395 => 151,  294 => 81,  223 => 67,  220 => 67,  492 => 129,  468 => 186,  444 => 173,  410 => 72,  397 => 90,  377 => 89,  262 => 66,  250 => 79,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 793,  2649 => 2234,  2625 => 2213,  2517 => 1051,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 662,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 608,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 568,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 543,  1826 => 506,  1819 => 504,  1732 => 1414,  1723 => 467,  1695 => 680,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 638,  1533 => 397,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 572,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 526,  1301 => 1037,  1252 => 392,  1243 => 283,  1223 => 978,  1212 => 494,  1167 => 366,  1161 => 933,  1145 => 923,  1140 => 413,  1134 => 409,  1126 => 443,  1106 => 528,  1100 => 897,  1026 => 220,  997 => 367,  983 => 298,  975 => 361,  956 => 318,  939 => 264,  902 => 274,  894 => 177,  879 => 76,  757 => 185,  727 => 267,  716 => 226,  670 => 204,  528 => 93,  476 => 123,  435 => 121,  354 => 121,  341 => 127,  192 => 45,  321 => 75,  243 => 62,  793 => 287,  780 => 140,  758 => 229,  700 => 262,  686 => 294,  652 => 160,  638 => 269,  620 => 259,  545 => 220,  523 => 140,  494 => 274,  459 => 156,  438 => 104,  351 => 57,  347 => 83,  402 => 99,  268 => 49,  430 => 103,  411 => 101,  379 => 95,  322 => 70,  315 => 73,  289 => 78,  284 => 73,  255 => 60,  234 => 60,  1133 => 444,  1124 => 455,  1121 => 430,  1116 => 405,  1113 => 429,  1108 => 376,  1103 => 444,  1098 => 401,  1081 => 320,  1073 => 235,  1067 => 283,  1064 => 330,  1061 => 421,  1053 => 318,  1049 => 294,  1042 => 313,  1039 => 384,  1025 => 205,  1021 => 204,  1015 => 308,  1008 => 284,  996 => 406,  989 => 277,  985 => 395,  981 => 296,  977 => 321,  970 => 275,  966 => 274,  955 => 293,  952 => 464,  943 => 266,  936 => 353,  930 => 289,  919 => 241,  917 => 348,  908 => 236,  905 => 363,  896 => 275,  891 => 338,  877 => 334,  862 => 248,  857 => 271,  837 => 154,  832 => 260,  827 => 184,  821 => 266,  803 => 179,  778 => 191,  769 => 253,  765 => 201,  753 => 139,  746 => 182,  743 => 297,  735 => 168,  730 => 187,  720 => 305,  717 => 165,  712 => 186,  691 => 292,  678 => 257,  654 => 199,  587 => 98,  576 => 167,  539 => 200,  517 => 208,  471 => 187,  441 => 171,  437 => 170,  418 => 74,  386 => 107,  373 => 67,  304 => 70,  270 => 69,  265 => 123,  229 => 55,  477 => 167,  455 => 70,  448 => 173,  429 => 165,  407 => 120,  399 => 111,  389 => 87,  375 => 83,  358 => 62,  349 => 130,  335 => 41,  327 => 60,  298 => 98,  280 => 90,  249 => 39,  194 => 91,  142 => 61,  344 => 117,  318 => 57,  306 => 52,  295 => 51,  357 => 101,  300 => 82,  286 => 63,  276 => 95,  269 => 97,  254 => 118,  128 => 53,  237 => 76,  165 => 34,  122 => 26,  798 => 288,  770 => 279,  759 => 278,  748 => 270,  731 => 180,  721 => 227,  718 => 120,  708 => 185,  696 => 295,  617 => 258,  590 => 245,  553 => 145,  550 => 157,  540 => 161,  533 => 182,  500 => 199,  493 => 196,  489 => 202,  482 => 117,  467 => 113,  464 => 129,  458 => 83,  452 => 81,  449 => 112,  415 => 73,  382 => 90,  372 => 138,  361 => 101,  356 => 132,  339 => 114,  302 => 67,  285 => 99,  258 => 48,  123 => 36,  108 => 24,  424 => 164,  394 => 89,  380 => 144,  338 => 71,  319 => 79,  316 => 53,  312 => 104,  290 => 101,  267 => 124,  206 => 36,  110 => 32,  240 => 77,  224 => 33,  219 => 38,  217 => 56,  202 => 93,  186 => 33,  170 => 79,  100 => 26,  67 => 21,  14 => 1,  1096 => 345,  1090 => 339,  1088 => 397,  1085 => 456,  1066 => 233,  1034 => 210,  1031 => 290,  1018 => 303,  1013 => 302,  1007 => 408,  1002 => 193,  993 => 279,  986 => 212,  982 => 211,  976 => 399,  971 => 254,  964 => 208,  949 => 289,  946 => 288,  940 => 388,  937 => 245,  928 => 262,  926 => 318,  915 => 284,  912 => 82,  903 => 179,  898 => 233,  892 => 176,  889 => 277,  887 => 230,  884 => 79,  876 => 225,  874 => 193,  871 => 331,  863 => 345,  861 => 220,  858 => 247,  850 => 216,  843 => 270,  840 => 186,  815 => 204,  812 => 294,  808 => 323,  804 => 201,  799 => 198,  791 => 143,  785 => 141,  775 => 313,  771 => 245,  754 => 267,  728 => 167,  726 => 126,  723 => 177,  715 => 175,  711 => 174,  709 => 222,  706 => 173,  698 => 182,  694 => 116,  692 => 161,  689 => 291,  681 => 224,  677 => 167,  675 => 285,  663 => 279,  661 => 162,  650 => 248,  646 => 231,  629 => 154,  627 => 262,  625 => 266,  622 => 202,  598 => 157,  592 => 148,  586 => 175,  575 => 238,  566 => 216,  556 => 95,  554 => 227,  541 => 208,  536 => 207,  515 => 79,  511 => 208,  509 => 206,  488 => 119,  486 => 145,  483 => 183,  465 => 185,  463 => 112,  450 => 107,  432 => 125,  419 => 65,  371 => 128,  362 => 126,  353 => 73,  337 => 126,  333 => 112,  309 => 55,  303 => 102,  299 => 69,  291 => 96,  272 => 93,  261 => 88,  253 => 30,  239 => 111,  235 => 75,  213 => 44,  200 => 55,  198 => 92,  159 => 36,  149 => 36,  146 => 67,  131 => 43,  116 => 25,  79 => 22,  74 => 20,  71 => 19,  836 => 262,  817 => 243,  814 => 295,  811 => 320,  805 => 244,  787 => 257,  779 => 169,  776 => 281,  773 => 280,  761 => 199,  751 => 271,  747 => 191,  742 => 190,  739 => 189,  736 => 215,  724 => 266,  705 => 69,  702 => 216,  688 => 113,  680 => 107,  667 => 273,  662 => 282,  656 => 276,  649 => 272,  644 => 181,  641 => 246,  624 => 101,  613 => 151,  607 => 171,  597 => 99,  591 => 170,  584 => 242,  579 => 132,  563 => 215,  559 => 137,  551 => 135,  547 => 134,  537 => 160,  524 => 201,  512 => 137,  507 => 237,  504 => 149,  498 => 129,  485 => 126,  480 => 134,  472 => 114,  466 => 138,  460 => 183,  447 => 107,  442 => 128,  434 => 75,  428 => 102,  422 => 118,  404 => 155,  368 => 136,  364 => 75,  340 => 94,  334 => 163,  330 => 61,  325 => 117,  292 => 50,  287 => 51,  282 => 62,  279 => 96,  273 => 73,  266 => 89,  256 => 73,  252 => 83,  228 => 56,  218 => 53,  201 => 63,  64 => 18,  51 => 7,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 569,  2109 => 847,  2104 => 802,  2101 => 801,  2087 => 565,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 823,  2037 => 789,  2031 => 555,  2028 => 818,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 811,  2001 => 1668,  1996 => 771,  1994 => 549,  1987 => 766,  1983 => 547,  1979 => 764,  1972 => 761,  1969 => 544,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 534,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 528,  1923 => 779,  1916 => 736,  1912 => 735,  1908 => 522,  1901 => 521,  1897 => 730,  1894 => 729,  1889 => 769,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 511,  1835 => 509,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 500,  1801 => 695,  1798 => 694,  1795 => 498,  1789 => 814,  1776 => 489,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 481,  1750 => 668,  1745 => 667,  1742 => 476,  1739 => 665,  1736 => 708,  1730 => 705,  1720 => 688,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 672,  1678 => 644,  1671 => 450,  1666 => 640,  1663 => 447,  1660 => 763,  1657 => 659,  1651 => 656,  1641 => 631,  1637 => 440,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 651,  1592 => 614,  1587 => 613,  1584 => 635,  1581 => 731,  1576 => 608,  1574 => 633,  1567 => 603,  1563 => 410,  1559 => 601,  1552 => 628,  1548 => 597,  1542 => 593,  1529 => 605,  1525 => 614,  1516 => 708,  1512 => 583,  1503 => 599,  1490 => 388,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 384,  1447 => 569,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 374,  1406 => 532,  1393 => 564,  1388 => 537,  1380 => 539,  1376 => 367,  1371 => 366,  1367 => 527,  1364 => 518,  1361 => 523,  1355 => 543,  1342 => 360,  1337 => 510,  1328 => 356,  1324 => 550,  1317 => 511,  1313 => 499,  1310 => 526,  1304 => 504,  1291 => 479,  1286 => 476,  1279 => 486,  1274 => 468,  1270 => 483,  1266 => 487,  1262 => 490,  1257 => 500,  1254 => 286,  1250 => 394,  1246 => 345,  1239 => 389,  1235 => 343,  1232 => 416,  1226 => 383,  1213 => 273,  1208 => 481,  1201 => 443,  1196 => 335,  1192 => 490,  1188 => 456,  1184 => 263,  1179 => 448,  1176 => 261,  1172 => 428,  1168 => 451,  1164 => 450,  1160 => 324,  1153 => 561,  1149 => 359,  1146 => 444,  1137 => 249,  1123 => 442,  1119 => 354,  1115 => 313,  1111 => 377,  1107 => 242,  1102 => 344,  1099 => 347,  1095 => 307,  1091 => 321,  1082 => 289,  1079 => 524,  1076 => 393,  1070 => 431,  1057 => 313,  1052 => 220,  1045 => 484,  1040 => 213,  1036 => 283,  1032 => 209,  1028 => 312,  1023 => 288,  1020 => 311,  1016 => 285,  1012 => 390,  1005 => 263,  1001 => 304,  998 => 262,  992 => 261,  979 => 256,  974 => 255,  967 => 399,  962 => 397,  958 => 252,  954 => 293,  950 => 292,  945 => 391,  942 => 290,  938 => 375,  934 => 244,  927 => 183,  923 => 201,  920 => 369,  910 => 365,  901 => 234,  897 => 256,  890 => 271,  886 => 270,  883 => 353,  868 => 273,  856 => 163,  853 => 162,  849 => 264,  845 => 329,  841 => 249,  835 => 268,  830 => 333,  826 => 226,  822 => 281,  818 => 265,  813 => 215,  810 => 202,  806 => 261,  802 => 289,  795 => 241,  792 => 335,  789 => 249,  784 => 286,  782 => 284,  777 => 255,  772 => 190,  768 => 195,  763 => 327,  760 => 305,  756 => 274,  752 => 198,  745 => 245,  741 => 218,  738 => 135,  732 => 171,  719 => 288,  714 => 251,  710 => 264,  704 => 119,  699 => 183,  695 => 195,  690 => 226,  687 => 210,  683 => 156,  679 => 191,  672 => 255,  668 => 187,  665 => 280,  658 => 277,  645 => 103,  640 => 159,  634 => 206,  628 => 166,  623 => 260,  619 => 236,  611 => 158,  606 => 252,  603 => 228,  599 => 250,  595 => 169,  583 => 169,  580 => 240,  573 => 237,  560 => 231,  543 => 219,  538 => 217,  534 => 216,  530 => 215,  526 => 214,  521 => 139,  518 => 194,  514 => 92,  510 => 196,  503 => 141,  496 => 202,  490 => 129,  484 => 128,  474 => 87,  470 => 131,  446 => 108,  440 => 106,  436 => 168,  431 => 113,  425 => 102,  416 => 117,  412 => 115,  408 => 150,  403 => 92,  400 => 153,  396 => 110,  392 => 71,  385 => 97,  381 => 85,  367 => 86,  363 => 64,  359 => 79,  355 => 76,  350 => 120,  346 => 129,  343 => 72,  328 => 93,  324 => 59,  313 => 81,  307 => 103,  301 => 74,  288 => 72,  283 => 67,  271 => 58,  257 => 68,  251 => 59,  238 => 46,  233 => 74,  195 => 65,  191 => 64,  187 => 20,  183 => 85,  130 => 54,  88 => 22,  76 => 19,  115 => 34,  95 => 30,  655 => 104,  651 => 275,  648 => 171,  637 => 180,  633 => 265,  621 => 462,  618 => 241,  615 => 235,  604 => 251,  600 => 233,  588 => 206,  585 => 225,  582 => 153,  571 => 236,  567 => 96,  555 => 125,  552 => 141,  549 => 154,  544 => 179,  542 => 94,  535 => 133,  531 => 139,  519 => 80,  516 => 218,  513 => 207,  508 => 117,  506 => 131,  499 => 139,  495 => 186,  491 => 90,  481 => 215,  478 => 124,  475 => 188,  469 => 178,  456 => 182,  451 => 111,  443 => 118,  439 => 76,  427 => 60,  423 => 96,  420 => 109,  409 => 157,  405 => 99,  401 => 56,  391 => 149,  387 => 68,  384 => 130,  378 => 84,  365 => 79,  360 => 133,  348 => 170,  336 => 113,  332 => 79,  329 => 119,  323 => 116,  310 => 109,  305 => 108,  277 => 89,  274 => 94,  263 => 105,  259 => 67,  247 => 63,  244 => 114,  241 => 63,  222 => 74,  210 => 65,  207 => 71,  204 => 52,  184 => 45,  181 => 84,  167 => 38,  157 => 55,  96 => 47,  421 => 101,  417 => 150,  414 => 145,  406 => 113,  398 => 152,  393 => 150,  390 => 109,  376 => 108,  369 => 148,  366 => 127,  352 => 131,  345 => 65,  342 => 64,  331 => 55,  326 => 54,  320 => 77,  317 => 112,  314 => 86,  311 => 69,  308 => 84,  297 => 51,  293 => 65,  281 => 50,  278 => 59,  275 => 71,  264 => 55,  260 => 81,  248 => 47,  245 => 58,  242 => 78,  231 => 42,  227 => 45,  215 => 60,  212 => 72,  209 => 97,  197 => 61,  177 => 57,  171 => 49,  161 => 56,  132 => 34,  121 => 49,  105 => 29,  99 => 24,  81 => 20,  77 => 15,  180 => 58,  176 => 58,  156 => 83,  143 => 39,  139 => 27,  118 => 35,  189 => 44,  185 => 42,  173 => 42,  166 => 50,  152 => 74,  174 => 56,  164 => 86,  154 => 38,  150 => 38,  137 => 46,  133 => 33,  127 => 31,  107 => 29,  102 => 26,  83 => 19,  78 => 9,  53 => 16,  23 => 3,  42 => 13,  138 => 59,  134 => 34,  109 => 39,  103 => 23,  97 => 17,  94 => 27,  84 => 23,  75 => 14,  69 => 13,  66 => 20,  54 => 16,  44 => 10,  230 => 106,  226 => 105,  203 => 70,  193 => 61,  188 => 88,  182 => 42,  178 => 45,  168 => 57,  163 => 28,  160 => 75,  155 => 72,  148 => 52,  145 => 37,  140 => 70,  136 => 58,  125 => 57,  120 => 28,  113 => 56,  101 => 49,  92 => 21,  89 => 24,  85 => 22,  73 => 35,  62 => 14,  59 => 11,  56 => 10,  41 => 9,  126 => 33,  119 => 35,  111 => 25,  106 => 38,  98 => 31,  93 => 13,  86 => 25,  70 => 17,  60 => 15,  28 => 3,  36 => 5,  114 => 34,  104 => 49,  91 => 43,  80 => 34,  63 => 7,  58 => 17,  40 => 6,  34 => 3,  45 => 6,  61 => 17,  55 => 14,  48 => 8,  39 => 4,  35 => 6,  31 => 2,  26 => 5,  21 => 2,  46 => 8,  29 => 9,  57 => 16,  50 => 15,  47 => 14,  38 => 12,  33 => 3,  49 => 13,  32 => 7,  246 => 80,  236 => 62,  232 => 56,  225 => 40,  221 => 54,  216 => 73,  214 => 45,  211 => 98,  208 => 49,  205 => 64,  199 => 43,  196 => 62,  190 => 60,  179 => 39,  175 => 44,  172 => 41,  169 => 29,  162 => 40,  158 => 39,  153 => 54,  151 => 26,  147 => 28,  144 => 62,  141 => 32,  135 => 45,  129 => 31,  124 => 30,  117 => 51,  112 => 27,  90 => 23,  87 => 19,  82 => 11,  72 => 14,  68 => 13,  65 => 16,  52 => 9,  43 => 10,  37 => 6,  30 => 7,  27 => 4,  25 => 4,  24 => 2,  22 => 2,  19 => 1,);
    }
}
