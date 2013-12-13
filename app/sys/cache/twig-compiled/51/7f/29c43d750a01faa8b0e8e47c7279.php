<?php

/* UserBundle:Downloads:file.html.twig */
class __TwigTemplate_517f29c43d750a01faa8b0e8e47c7279 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("UserBundle::layout.html.twig");

        $this->blocks = array(
            'head' => array($this, 'block_head'),
            'breadcrumb' => array($this, 'block_breadcrumb'),
            'page_title' => array($this, 'block_page_title'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "UserBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 5
        $context["social"] = $this->env->loadTemplate("UserBundle:Common:macros-social.html.twig");
        // line 6
        $context["this_section"] = "downloads";
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 8
    public function block_head($context, array $blocks = array())
    {
        // line 9
        echo "\t<meta property=\"og:url\" content=\"";
        if (isset($context["download"])) { $_download_ = $context["download"]; } else { $_download_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_downloads_file", array("slug" => $this->getAttribute($_download_, "url_slug"))), "html", null, true);
        echo "\" />
\t<meta property=\"og:title\" content=\"";
        // line 10
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_article_, "title"), "html", null, true);
        echo "\" />
\t<meta property=\"og:type\" content=\"article\" />
\t<link rel=\"canonical\" href=\"";
        // line 12
        if (isset($context["download"])) { $_download_ = $context["download"]; } else { $_download_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getUrl("user_downloads_file", array("slug" => $this->getAttribute($_download_, "url_slug"))), "html", null, true);
        echo "\" />
";
    }

    // line 14
    public function block_breadcrumb($context, array $blocks = array())
    {
        // line 15
        echo "\t<li><span class=\"dp-divider\">";
        echo $this->env->getExtension('deskpro_templating')->getLanguageArrow("right");
        echo "</span> <a href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_downloads"), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.downloads.title");
        echo "</a></li>
\t";
        // line 16
        if (isset($context["category_path"])) { $_category_path_ = $context["category_path"]; } else { $_category_path_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_category_path_);
        foreach ($context['_seq'] as $context["_key"] => $context["cat"]) {
            // line 17
            echo "\t\t<li><span class=\"dp-divider\">";
            echo $this->env->getExtension('deskpro_templating')->getLanguageArrow("right");
            echo "</span> <a href=\"";
            if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_downloads", array("slug" => $this->getAttribute($_cat_, "url_slug"))), "html", null, true);
            echo "\">";
            if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
            echo "</a></li>
\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['cat'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 19
        echo "\t<li><span class=\"dp-divider\">";
        echo $this->env->getExtension('deskpro_templating')->getLanguageArrow("right");
        echo "</span> <a href=\"";
        if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_downloads", array("slug" => $this->getAttribute($_category_, "url_slug"))), "html", null, true);
        echo "\">";
        if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_category_, "title"), "html", null, true);
        echo "</a></li>
\t<li><span class=\"dp-divider\">";
        // line 20
        echo $this->env->getExtension('deskpro_templating')->getLanguageArrow("right");
        echo "</span> <a href=\"";
        if (isset($context["download"])) { $_download_ = $context["download"]; } else { $_download_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_downloads_file", array("slug" => $this->getAttribute($_download_, "url_slug"))), "html", null, true);
        echo "\">";
        if (isset($context["download"])) { $_download_ = $context["download"]; } else { $_download_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_download_, "title"), "html", null, true);
        echo "</a></li>
";
    }

    // line 22
    public function block_page_title($context, array $blocks = array())
    {
        // line 23
        echo "\t";
        if (isset($context["download"])) { $_download_ = $context["download"]; } else { $_download_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_download_, "filename"), "html", null, true);
        echo "
\t-
\t";
        // line 25
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.downloads.title");
        echo "
\t";
        // line 26
        if (isset($context["category_path"])) { $_category_path_ = $context["category_path"]; } else { $_category_path_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_category_path_);
        foreach ($context['_seq'] as $context["_key"] => $context["cat"]) {
            // line 27
            echo "\t\t/ ";
            if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
            echo "
\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['cat'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 29
        echo "\t";
        if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_category_, "title"), "html", null, true);
        echo "
";
    }

    // line 31
    public function block_content($context, array $blocks = array())
    {
        // line 32
        echo "
<section class=\"dp-article-post dp-content-post dp-content-page\">
\t<header>
\t\t<h3 style=\"margin-bottom: 0\">";
        // line 35
        if (isset($context["download"])) { $_download_ = $context["download"]; } else { $_download_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_download_, "title"), "html", null, true);
        echo "</h3>
\t\t<ul class=\"dp-post-info\">
\t\t\t<li class=\"dp-author\"><i class=\"dp-icon-user\"></i> ";
        // line 37
        if (isset($context["download"])) { $_download_ = $context["download"]; } else { $_download_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_download_, "person"), "display_name_user"), "html", null, true);
        echo "</li>
\t\t\t<li class=\"dp-date\"><i class=\"dp-icon-calendar\"></i> ";
        // line 38
        if (isset($context["download"])) { $_download_ = $context["download"]; } else { $_download_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_download_, "date_created"), "full"), "html", null, true);
        echo "</li>
\t\t</ul>
\t</header>
\t<article id=\"dp_article_content\" style=\"overflow: hidden\">
\t\t<div class=\"dp-download-btn-wrap\">
\t\t\t<div class=\"dp-download-btn\">
\t\t\t\t<a class=\"dp-btn dp-btn-primary\" href=\"";
        // line 44
        if (isset($context["download"])) { $_download_ = $context["download"]; } else { $_download_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_downloads_file_download", array("slug" => $this->getAttribute($_download_, "url_slug"))), "html", null, true);
        echo "\">
\t\t\t\t\t";
        // line 45
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.downloads.button-download");
        echo ": ";
        if (isset($context["download"])) { $_download_ = $context["download"]; } else { $_download_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_download_, "filename"), "html", null, true);
        echo " <small style=\"color: #fff;\">(";
        if (isset($context["download"])) { $_download_ = $context["download"]; } else { $_download_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_download_, "readable_filesize"), "html", null, true);
        echo ")</small>
\t\t\t\t</a>
\t\t\t</div>
\t\t</div>

\t\t";
        // line 50
        if (isset($context["download"])) { $_download_ = $context["download"]; } else { $_download_ = null; }
        echo $this->getAttribute($_download_, "content_html");
        echo "
\t\t<br style=\"clear: ";
        // line 51
        if ($this->env->getExtension('deskpro_templating')->isRtl()) {
            echo "left";
        } else {
            echo "right";
        }
        echo "; height: 1px;overflow: hidden\" />
\t</article>
\t<footer>
\t\t";
        // line 54
        if (isset($context["download"])) { $_download_ = $context["download"]; } else { $_download_ = null; }
        $this->env->loadTemplate("UserBundle:Common:labels-box.html.twig")->display(array_merge($context, array("labels" => $this->getAttribute($_download_, "labels"))));
        // line 55
        echo "\t\t";
        if (isset($context["download"])) { $_download_ = $context["download"]; } else { $_download_ = null; }
        if (isset($context["related_content"])) { $_related_content_ = $context["related_content"]; } else { $_related_content_ = null; }
        $this->env->loadTemplate("UserBundle:Common:related-box.html.twig")->display(array_merge($context, array("object" => $_download_, "type" => "download", "related_content" => $_related_content_)));
        // line 56
        echo "
\t\t";
        // line 57
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "user"), "hasPerm", array(0 => "downloads.rate"), "method")) {
            // line 58
            echo "\t\t\t";
            if (isset($context["download"])) { $_download_ = $context["download"]; } else { $_download_ = null; }
            $this->env->loadTemplate("UserBundle:Common:rating-bar.html.twig")->display(array_merge($context, array("content_object" => $_download_)));
            // line 59
            echo "
\t\t\t";
            // line 60
            if (isset($context["download"])) { $_download_ = $context["download"]; } else { $_download_ = null; }
            if ($this->getAttribute($_download_, "num_downloads")) {
                // line 61
                echo "\t\t\t\t<div class=\"dp-view-wrap\">
\t\t\t\t\t";
                // line 62
                if (isset($context["download"])) { $_download_ = $context["download"]; } else { $_download_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.downloads.downloads-count", array("count" => $this->getAttribute($_download_, "num_downloads")));
                echo "
\t\t\t\t</div>
\t\t\t";
            }
            // line 65
            echo "\t\t";
        }
        // line 66
        echo "
\t\t";
        // line 67
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.show_share_widget"), "method")) {
            // line 68
            echo "\t\t\t";
            if (isset($context["social"])) { $_social_ = $context["social"]; } else { $_social_ = null; }
            if (isset($context["download"])) { $_download_ = $context["download"]; } else { $_download_ = null; }
            echo $_social_->getsocial_block($this->env->getExtension('routing')->getUrl("user_downloads_file", array("slug" => $this->getAttribute($_download_, "url_slug"))), $this->getAttribute($_download_, "title"));
            echo "
\t\t";
        }
        // line 70
        echo "\t</footer>
</section>

";
        // line 73
        if (isset($context["comments"])) { $_comments_ = $context["comments"]; } else { $_comments_ = null; }
        if (isset($context["download"])) { $_download_ = $context["download"]; } else { $_download_ = null; }
        $this->env->loadTemplate("UserBundle:Common:comments-box.html.twig")->display(array_merge($context, array("type" => "feedback", "comments" => $_comments_, "form_action" => $this->env->getExtension('routing')->getPath("user_downloads_newcomment", array("download_id" => $this->getAttribute($_download_, "id"))), "check_perm" => "downloads.comment")));
        // line 79
        echo "
";
    }

    public function getTemplateName()
    {
        return "UserBundle:Downloads:file.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  395 => 114,  294 => 91,  223 => 57,  220 => 56,  492 => 127,  468 => 121,  444 => 115,  410 => 105,  397 => 101,  377 => 96,  262 => 65,  250 => 62,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 2108,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 1383,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 1303,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 1173,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 1041,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 970,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 920,  1126 => 916,  1106 => 900,  1100 => 897,  1026 => 841,  997 => 822,  983 => 816,  975 => 812,  956 => 800,  939 => 786,  902 => 753,  894 => 749,  879 => 746,  757 => 631,  727 => 608,  716 => 605,  670 => 582,  528 => 447,  476 => 123,  435 => 380,  354 => 310,  341 => 133,  192 => 54,  321 => 107,  243 => 75,  793 => 351,  780 => 348,  758 => 341,  700 => 600,  686 => 292,  652 => 274,  638 => 266,  620 => 254,  545 => 218,  523 => 203,  494 => 183,  459 => 163,  438 => 150,  351 => 116,  347 => 114,  402 => 142,  268 => 73,  430 => 120,  411 => 136,  379 => 101,  322 => 100,  315 => 79,  289 => 84,  284 => 93,  255 => 68,  234 => 70,  1133 => 64,  1124 => 57,  1121 => 56,  1116 => 55,  1113 => 54,  1108 => 51,  1103 => 43,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 346,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 329,  996 => 320,  989 => 316,  985 => 315,  981 => 314,  977 => 313,  970 => 311,  966 => 310,  955 => 303,  952 => 302,  943 => 299,  936 => 296,  930 => 293,  919 => 290,  917 => 289,  908 => 287,  905 => 286,  896 => 280,  891 => 278,  877 => 270,  862 => 267,  857 => 265,  837 => 253,  832 => 250,  827 => 248,  821 => 247,  803 => 243,  778 => 235,  769 => 233,  765 => 232,  753 => 229,  746 => 225,  743 => 224,  735 => 613,  730 => 218,  720 => 213,  717 => 212,  712 => 209,  691 => 204,  678 => 586,  654 => 196,  587 => 239,  576 => 179,  539 => 171,  517 => 169,  471 => 155,  441 => 151,  437 => 142,  418 => 107,  386 => 125,  373 => 95,  304 => 102,  270 => 67,  265 => 83,  229 => 74,  477 => 449,  455 => 393,  448 => 116,  429 => 138,  407 => 95,  399 => 93,  389 => 99,  375 => 123,  358 => 116,  349 => 138,  335 => 84,  327 => 82,  298 => 112,  280 => 56,  249 => 66,  194 => 48,  142 => 35,  344 => 113,  318 => 106,  306 => 107,  295 => 74,  357 => 119,  300 => 93,  286 => 71,  276 => 240,  269 => 53,  254 => 80,  128 => 32,  237 => 44,  165 => 41,  122 => 25,  798 => 242,  770 => 113,  759 => 112,  748 => 337,  731 => 108,  721 => 606,  718 => 106,  708 => 104,  696 => 102,  617 => 188,  590 => 91,  553 => 87,  550 => 466,  540 => 84,  533 => 82,  500 => 129,  493 => 72,  489 => 181,  482 => 69,  467 => 67,  464 => 120,  458 => 64,  452 => 117,  449 => 156,  415 => 55,  382 => 124,  372 => 126,  361 => 82,  356 => 108,  339 => 85,  302 => 42,  285 => 40,  258 => 64,  123 => 28,  108 => 29,  424 => 135,  394 => 86,  380 => 80,  338 => 113,  319 => 80,  316 => 65,  312 => 110,  290 => 89,  267 => 88,  206 => 51,  110 => 27,  240 => 95,  224 => 35,  219 => 71,  217 => 65,  202 => 51,  186 => 46,  170 => 43,  100 => 20,  67 => 20,  14 => 1,  1096 => 291,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 836,  1013 => 275,  1007 => 274,  1002 => 823,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 300,  940 => 298,  937 => 248,  928 => 244,  926 => 243,  915 => 237,  912 => 236,  903 => 231,  898 => 230,  892 => 229,  889 => 748,  887 => 227,  884 => 747,  876 => 222,  874 => 269,  871 => 220,  863 => 214,  861 => 213,  858 => 212,  850 => 263,  843 => 206,  840 => 205,  815 => 245,  812 => 244,  808 => 199,  804 => 120,  799 => 196,  791 => 190,  785 => 238,  775 => 184,  771 => 183,  754 => 340,  728 => 170,  726 => 169,  723 => 168,  715 => 105,  711 => 604,  709 => 162,  706 => 603,  698 => 208,  694 => 156,  692 => 155,  689 => 154,  681 => 150,  677 => 149,  675 => 148,  663 => 143,  661 => 277,  650 => 195,  646 => 136,  629 => 129,  627 => 128,  625 => 127,  622 => 126,  598 => 119,  592 => 117,  586 => 115,  575 => 233,  566 => 103,  556 => 100,  554 => 177,  541 => 216,  536 => 91,  515 => 86,  511 => 85,  509 => 84,  488 => 126,  486 => 78,  483 => 77,  465 => 73,  463 => 397,  450 => 65,  432 => 112,  419 => 105,  371 => 46,  362 => 43,  353 => 73,  337 => 106,  333 => 104,  309 => 109,  303 => 76,  299 => 75,  291 => 96,  272 => 79,  261 => 95,  253 => 82,  239 => 62,  235 => 84,  213 => 86,  200 => 64,  198 => 55,  159 => 140,  149 => 31,  146 => 36,  131 => 27,  116 => 39,  79 => 18,  74 => 17,  71 => 11,  836 => 320,  817 => 319,  814 => 318,  811 => 317,  805 => 313,  787 => 657,  779 => 306,  776 => 305,  773 => 347,  761 => 296,  751 => 175,  747 => 293,  742 => 336,  739 => 291,  736 => 287,  724 => 214,  705 => 278,  702 => 601,  688 => 265,  680 => 263,  667 => 261,  662 => 260,  656 => 259,  649 => 258,  644 => 97,  641 => 194,  624 => 255,  613 => 187,  607 => 93,  597 => 225,  591 => 185,  584 => 218,  579 => 234,  563 => 230,  559 => 208,  551 => 221,  547 => 205,  537 => 201,  524 => 191,  512 => 185,  507 => 76,  504 => 182,  498 => 178,  485 => 176,  480 => 124,  472 => 122,  466 => 153,  460 => 119,  447 => 388,  442 => 162,  434 => 110,  428 => 111,  422 => 106,  404 => 149,  368 => 136,  364 => 83,  340 => 131,  334 => 111,  330 => 129,  325 => 100,  292 => 116,  287 => 115,  282 => 70,  279 => 109,  273 => 87,  266 => 66,  256 => 94,  252 => 67,  228 => 71,  218 => 54,  201 => 91,  64 => 16,  51 => 14,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 1705,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 707,  1829 => 706,  1827 => 705,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 689,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 676,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 666,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 638,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 617,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 611,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 602,  1559 => 601,  1552 => 598,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 584,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 573,  1477 => 567,  1473 => 566,  1467 => 562,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 520,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 511,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 1001,  1257 => 1000,  1254 => 479,  1250 => 478,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 460,  1196 => 958,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 938,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 924,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 425,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 898,  1099 => 420,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 880,  1076 => 359,  1070 => 875,  1057 => 352,  1052 => 404,  1045 => 854,  1040 => 852,  1036 => 283,  1032 => 395,  1028 => 394,  1023 => 393,  1020 => 392,  1016 => 391,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 821,  979 => 379,  974 => 312,  967 => 373,  962 => 803,  958 => 801,  954 => 253,  950 => 368,  945 => 367,  942 => 250,  938 => 365,  934 => 364,  927 => 361,  923 => 292,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 272,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 207,  841 => 321,  835 => 312,  830 => 249,  826 => 309,  822 => 308,  818 => 246,  813 => 306,  810 => 305,  806 => 304,  802 => 197,  795 => 118,  792 => 239,  789 => 350,  784 => 295,  782 => 187,  777 => 291,  772 => 289,  768 => 345,  763 => 343,  760 => 231,  756 => 230,  752 => 284,  745 => 281,  741 => 280,  738 => 335,  732 => 171,  719 => 273,  714 => 280,  710 => 310,  704 => 267,  699 => 103,  695 => 264,  690 => 263,  687 => 203,  683 => 587,  679 => 288,  672 => 284,  668 => 256,  665 => 201,  658 => 141,  645 => 270,  640 => 247,  634 => 96,  628 => 193,  623 => 238,  619 => 237,  611 => 248,  606 => 234,  603 => 467,  599 => 242,  595 => 231,  583 => 114,  580 => 180,  573 => 221,  560 => 101,  543 => 172,  538 => 209,  534 => 208,  530 => 450,  526 => 89,  521 => 443,  518 => 204,  514 => 78,  510 => 202,  503 => 75,  496 => 128,  490 => 193,  484 => 125,  474 => 190,  470 => 168,  446 => 144,  440 => 114,  436 => 113,  431 => 146,  425 => 117,  416 => 104,  412 => 98,  408 => 112,  403 => 172,  400 => 111,  396 => 133,  392 => 169,  385 => 98,  381 => 97,  367 => 117,  363 => 155,  359 => 118,  355 => 115,  350 => 107,  346 => 71,  343 => 86,  328 => 127,  324 => 138,  313 => 122,  307 => 77,  301 => 101,  288 => 27,  283 => 243,  271 => 107,  257 => 84,  251 => 64,  238 => 72,  233 => 60,  195 => 54,  191 => 62,  187 => 47,  183 => 45,  130 => 32,  88 => 29,  76 => 22,  115 => 23,  95 => 31,  655 => 275,  651 => 232,  648 => 231,  637 => 223,  633 => 130,  621 => 219,  618 => 218,  615 => 121,  604 => 186,  600 => 516,  588 => 116,  585 => 204,  582 => 203,  571 => 104,  567 => 178,  555 => 207,  552 => 190,  549 => 215,  544 => 186,  542 => 185,  535 => 212,  531 => 90,  519 => 189,  516 => 199,  513 => 168,  508 => 434,  506 => 83,  499 => 198,  495 => 167,  491 => 165,  481 => 162,  478 => 68,  475 => 157,  469 => 154,  456 => 118,  451 => 186,  443 => 161,  439 => 147,  427 => 89,  423 => 109,  420 => 176,  409 => 54,  405 => 135,  401 => 118,  391 => 129,  387 => 129,  384 => 132,  378 => 123,  365 => 93,  360 => 91,  348 => 88,  336 => 111,  332 => 140,  329 => 103,  323 => 81,  310 => 92,  305 => 94,  277 => 23,  274 => 68,  263 => 70,  259 => 68,  247 => 110,  244 => 65,  241 => 62,  222 => 55,  210 => 63,  207 => 62,  204 => 56,  184 => 46,  181 => 46,  167 => 42,  157 => 35,  96 => 21,  421 => 144,  417 => 137,  414 => 126,  406 => 121,  398 => 129,  393 => 100,  390 => 111,  376 => 110,  369 => 94,  366 => 136,  352 => 89,  345 => 98,  342 => 109,  331 => 83,  326 => 102,  320 => 137,  317 => 124,  314 => 97,  311 => 78,  308 => 60,  297 => 92,  293 => 128,  281 => 92,  278 => 88,  275 => 68,  264 => 87,  260 => 82,  248 => 76,  245 => 97,  242 => 60,  231 => 36,  227 => 68,  215 => 55,  212 => 54,  209 => 54,  197 => 50,  177 => 46,  171 => 66,  161 => 39,  132 => 122,  121 => 48,  105 => 25,  99 => 34,  81 => 20,  77 => 18,  180 => 47,  176 => 69,  156 => 28,  143 => 33,  139 => 51,  118 => 29,  189 => 164,  185 => 67,  173 => 44,  166 => 41,  152 => 32,  174 => 43,  164 => 62,  154 => 38,  150 => 36,  137 => 34,  133 => 32,  127 => 33,  107 => 30,  102 => 25,  83 => 25,  78 => 19,  53 => 14,  23 => 6,  42 => 10,  138 => 34,  134 => 31,  109 => 27,  103 => 25,  97 => 31,  94 => 23,  84 => 25,  75 => 17,  69 => 16,  66 => 16,  54 => 13,  44 => 10,  230 => 59,  226 => 58,  203 => 51,  193 => 52,  188 => 68,  182 => 45,  178 => 44,  168 => 38,  163 => 37,  160 => 77,  155 => 35,  148 => 37,  145 => 33,  140 => 38,  136 => 36,  125 => 31,  120 => 27,  113 => 31,  101 => 22,  92 => 23,  89 => 19,  85 => 18,  73 => 17,  62 => 15,  59 => 15,  56 => 56,  41 => 11,  126 => 26,  119 => 33,  111 => 24,  106 => 26,  98 => 24,  93 => 20,  86 => 21,  70 => 17,  60 => 15,  28 => 5,  36 => 6,  114 => 28,  104 => 28,  91 => 17,  80 => 18,  63 => 19,  58 => 15,  40 => 8,  34 => 8,  45 => 10,  61 => 18,  55 => 11,  48 => 13,  39 => 17,  35 => 8,  31 => 7,  26 => 6,  21 => 5,  46 => 11,  29 => 13,  57 => 14,  50 => 12,  47 => 11,  38 => 9,  33 => 9,  49 => 16,  32 => 8,  246 => 65,  236 => 61,  232 => 43,  225 => 64,  221 => 89,  216 => 34,  214 => 64,  211 => 61,  208 => 33,  205 => 32,  199 => 56,  196 => 55,  190 => 51,  179 => 28,  175 => 67,  172 => 44,  169 => 43,  162 => 40,  158 => 39,  153 => 137,  151 => 63,  147 => 32,  144 => 42,  141 => 29,  135 => 51,  129 => 30,  124 => 36,  117 => 32,  112 => 22,  90 => 19,  87 => 26,  82 => 17,  72 => 21,  68 => 14,  65 => 18,  52 => 10,  43 => 13,  37 => 16,  30 => 6,  27 => 6,  25 => 5,  24 => 11,  22 => 5,  19 => 4,);
    }
}
