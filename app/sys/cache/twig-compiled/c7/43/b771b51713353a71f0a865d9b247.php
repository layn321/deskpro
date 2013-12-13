<?php

/* UserBundle:News:filter.html.twig */
class __TwigTemplate_c743b771b51713353a71f0a865d9b247 extends \Application\DeskPRO\Twig\Template
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
        // line 6
        $context["this_section"] = "news";
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 8
    public function block_head($context, array $blocks = array())
    {
        // line 9
        echo "\t";
        if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
        if ($_category_) {
            // line 10
            echo "\t\t<link rel=\"alternate\" type=\"application/rss+xml\" title=\"";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "core.deskpro_name"), "method"), "html", null, true);
            echo ": ";
            if (isset($context["cagegory"])) { $_cagegory_ = $context["cagegory"]; } else { $_cagegory_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_cagegory_, "full_title"), "html", null, true);
            echo "\" href=\"";
            if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getUrl("user_news", array("slug" => $this->getAttribute($_category_, "url_slug"), "_format" => "rss")), "html", null, true);
            echo "\" />
\t";
        } else {
            // line 12
            echo "\t\t<link rel=\"alternate\" type=\"application/rss+xml\" title=\"";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "core.deskpro_name"), "method"), "html", null, true);
            echo "\" href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getUrl("user_news_home", array("_format" => "rss")), "html", null, true);
            echo "\" />
\t";
        }
    }

    // line 27
    public function block_breadcrumb($context, array $blocks = array())
    {
        // line 28
        echo "\t<li><span class=\"dp-divider\">";
        echo $this->env->getExtension('deskpro_templating')->getLanguageArrow("right");
        echo "</span> <a href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_news"), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.news.title");
        echo "</a></li>
\t";
        // line 29
        if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
        if ($_category_) {
            // line 30
            echo "\t\t";
            if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_category_, "getTreeParents", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["cat"]) {
                // line 31
                echo "\t\t\t<li><span class=\"dp-divider\">";
                echo $this->env->getExtension('deskpro_templating')->getLanguageArrow("right");
                echo "</span> <a href=\"";
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_news", array("slug" => $this->getAttribute($_cat_, "url_slug"))), "html", null, true);
                echo "\">";
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
                echo "</a></li>
\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['cat'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 33
            echo "\t\t<li><span class=\"dp-divider\">";
            echo $this->env->getExtension('deskpro_templating')->getLanguageArrow("right");
            echo "</span> <a href=\"";
            if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_news", array("slug" => $this->getAttribute($_category_, "url_slug"))), "html", null, true);
            echo "\">";
            if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_category_, "title"), "html", null, true);
            echo "</a></li>
\t";
        }
    }

    // line 36
    public function block_page_title($context, array $blocks = array())
    {
        // line 37
        echo "\t";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.news.title");
        echo "
\t";
        // line 38
        if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
        if ($_category_) {
            // line 39
            echo "\t\t";
            if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_category_, "getTreeParents", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["cat"]) {
                // line 40
                echo "\t\t\t- ";
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
                echo "
\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['cat'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 42
            echo "\t\t- ";
            if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_category_, "title"), "html", null, true);
            echo "
\t";
        }
    }

    // line 45
    public function block_content($context, array $blocks = array())
    {
        // line 46
        echo "
<section class=\"dp-portal-section dp-new-feedback-section\">
\t<header>
\t\t<h1>";
        // line 49
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.news.title");
        echo "</h1>
\t</header>
</section>

<section class=\"dp-portal-section dp-feedback-section\">
\t<div class=\"dp-content-block dp-articles-block\">
\t\t<div class=\"dp-content-wrapper\">
\t\t\t<div style=\"float:";
        // line 56
        if ($this->env->getExtension('deskpro_templating')->isRtl()) {
            echo "right";
        } else {
            echo "left";
        }
        echo "; width: 100%;\">
\t\t\t\t";
        // line 57
        $this->env->loadTemplate("UserBundle:News:filter-nav.html.twig")->display($context);
        // line 58
        echo "\t\t\t</div>
\t\t\t<br style=\"clear:";
        // line 59
        if ($this->env->getExtension('deskpro_templating')->isRtl()) {
            echo "right";
        } else {
            echo "left";
        }
        echo ";height: 1px;\" />

\t\t\t<ul class=\"dp-content-list\">
\t\t\t\t";
        // line 62
        if (isset($context["news_entries"])) { $_news_entries_ = $context["news_entries"]; } else { $_news_entries_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_news_entries_);
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
        foreach ($context['_seq'] as $context["_key"] => $context["news"]) {
            // line 63
            echo "\t\t\t\t\t";
            $this->env->loadTemplate("UserBundle:News:filter-list-item.html.twig")->display($context);
            // line 64
            echo "\t\t\t\t";
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
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['news'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 65
        echo "\t\t\t</ul>

\t\t\t";
        // line 67
        if (isset($context["pageinfo"])) { $_pageinfo_ = $context["pageinfo"]; } else { $_pageinfo_ = null; }
        if (($this->getAttribute($_pageinfo_, "last") != 1)) {
            // line 68
            echo "\t\t\t\t";
            if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
            $context["page_url"] = ($this->env->getExtension('routing')->getPath("user_news", array("slug" => $this->getAttribute($_category_, "url_slug"))) . "?p=");
            // line 69
            echo "\t\t\t\t<div class=\"dp-pagination\">
\t\t\t\t\t<ul>
\t\t\t\t\t\t";
            // line 71
            if (isset($context["pageinfo"])) { $_pageinfo_ = $context["pageinfo"]; } else { $_pageinfo_ = null; }
            if ($this->getAttribute($_pageinfo_, "prev")) {
                echo "<li><a href=\"";
                if (isset($context["page_url"])) { $_page_url_ = $context["page_url"]; } else { $_page_url_ = null; }
                if (isset($context["pageinfo"])) { $_pageinfo_ = $context["pageinfo"]; } else { $_pageinfo_ = null; }
                echo twig_escape_filter($this->env, ($_page_url_ . $this->getAttribute($_pageinfo_, "prev")), "html", null, true);
                echo "\">";
                echo $this->env->getExtension('deskpro_templating')->getLanguageArrow("left");
                echo " ";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.general.page_prev");
                echo "</a></li>";
            }
            // line 72
            echo "\t\t\t\t\t\t";
            if (isset($context["pageinfo"])) { $_pageinfo_ = $context["pageinfo"]; } else { $_pageinfo_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_pageinfo_, "pages"));
            foreach ($context['_seq'] as $context["_key"] => $context["p"]) {
                // line 73
                echo "\t\t\t\t\t\t\t<li class=\"";
                if (isset($context["pageinfo"])) { $_pageinfo_ = $context["pageinfo"]; } else { $_pageinfo_ = null; }
                if (isset($context["p"])) { $_p_ = $context["p"]; } else { $_p_ = null; }
                if (($this->getAttribute($_pageinfo_, "cursor") == $_p_)) {
                    echo "dp-active";
                }
                echo "\"><a href=\"";
                if (isset($context["page_url"])) { $_page_url_ = $context["page_url"]; } else { $_page_url_ = null; }
                if (isset($context["p"])) { $_p_ = $context["p"]; } else { $_p_ = null; }
                echo twig_escape_filter($this->env, ($_page_url_ . $_p_), "html", null, true);
                echo "\">";
                if (isset($context["p"])) { $_p_ = $context["p"]; } else { $_p_ = null; }
                echo twig_escape_filter($this->env, $_p_, "html", null, true);
                echo "</a>
\t\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['p'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 75
            echo "\t\t\t\t\t\t";
            if (isset($context["pageinfo"])) { $_pageinfo_ = $context["pageinfo"]; } else { $_pageinfo_ = null; }
            if ($this->getAttribute($_pageinfo_, "next")) {
                echo "<li><a href=\"";
                if (isset($context["page_url"])) { $_page_url_ = $context["page_url"]; } else { $_page_url_ = null; }
                if (isset($context["pageinfo"])) { $_pageinfo_ = $context["pageinfo"]; } else { $_pageinfo_ = null; }
                echo twig_escape_filter($this->env, ($_page_url_ . $this->getAttribute($_pageinfo_, "next")), "html", null, true);
                echo "\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.general.next");
                echo " ";
                echo $this->env->getExtension('deskpro_templating')->getLanguageArrow("right");
                echo "</a></li>";
            }
            // line 76
            echo "\t\t\t\t\t</ul>
\t\t\t\t</div>
\t\t\t";
        }
        // line 79
        echo "\t\t</div>
\t</div>
</section>
";
    }

    // line 15
    public function getcat_row($_cat = null, $_status = null, $_selected_cat = null)
    {
        $context = $this->env->mergeGlobals(array(
            "cat" => $_cat,
            "status" => $_status,
            "selected_cat" => $_selected_cat,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 16
            echo "\t<li ";
            if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
            if (isset($context["selected_cat"])) { $_selected_cat_ = $context["selected_cat"]; } else { $_selected_cat_ = null; }
            if (($this->getAttribute($_cat_, "id") == $this->getAttribute($_selected_cat_, "id"))) {
                echo "class=\"on\"";
            }
            echo ">
\t\t<a href=\"";
            // line 17
            if (isset($context["status"])) { $_status_ = $context["status"]; } else { $_status_ = null; }
            if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_feedback", array("status" => $_status_, "slug" => $this->getAttribute($_cat_, "url_slug"))), "html", null, true);
            echo "\">";
            if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
            echo "</a>
\t\t";
            // line 18
            if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
            if (twig_length_filter($this->env, $this->getAttribute($_cat_, "children"))) {
                // line 19
                echo "\t\t\t<ul>
\t\t\t\t";
                // line 20
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($_cat_, "children"));
                foreach ($context['_seq'] as $context["_key"] => $context["subcat"]) {
                    // line 21
                    echo "\t\t\t\t\t";
                    if (isset($context["subcat"])) { $_subcat_ = $context["subcat"]; } else { $_subcat_ = null; }
                    if (isset($context["status"])) { $_status_ = $context["status"]; } else { $_status_ = null; }
                    echo $this->getAttribute($this, "cat_row", array(0 => $_subcat_, 1 => $_status_), "method");
                    echo "
\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subcat'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 23
                echo "\t\t\t</ul>
\t\t";
            }
            // line 25
            echo "\t</li>
";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    public function getTemplateName()
    {
        return "UserBundle:News:filter.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  296 => 149,  374 => 93,  631 => 23,  616 => 19,  608 => 17,  605 => 16,  596 => 15,  574 => 13,  561 => 150,  527 => 147,  433 => 130,  388 => 112,  426 => 177,  383 => 103,  461 => 36,  370 => 113,  395 => 113,  294 => 76,  223 => 114,  220 => 67,  492 => 127,  468 => 121,  444 => 33,  410 => 105,  397 => 101,  377 => 96,  262 => 65,  250 => 129,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 2108,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 1383,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 1303,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 1173,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 1041,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 970,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 920,  1126 => 916,  1106 => 900,  1100 => 897,  1026 => 841,  997 => 822,  983 => 816,  975 => 812,  956 => 800,  939 => 786,  902 => 753,  894 => 749,  879 => 746,  757 => 631,  727 => 608,  716 => 605,  670 => 582,  528 => 447,  476 => 123,  435 => 31,  354 => 110,  341 => 97,  192 => 54,  321 => 163,  243 => 72,  793 => 351,  780 => 348,  758 => 341,  700 => 600,  686 => 292,  652 => 274,  638 => 266,  620 => 254,  545 => 218,  523 => 203,  494 => 183,  459 => 163,  438 => 182,  351 => 116,  347 => 109,  402 => 142,  268 => 80,  430 => 120,  411 => 120,  379 => 96,  322 => 92,  315 => 77,  289 => 73,  284 => 93,  255 => 126,  234 => 104,  1133 => 64,  1124 => 57,  1121 => 56,  1116 => 55,  1113 => 54,  1108 => 51,  1103 => 43,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 346,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 329,  996 => 320,  989 => 316,  985 => 315,  981 => 314,  977 => 313,  970 => 311,  966 => 310,  955 => 303,  952 => 302,  943 => 299,  936 => 296,  930 => 293,  919 => 290,  917 => 289,  908 => 287,  905 => 286,  896 => 280,  891 => 278,  877 => 270,  862 => 267,  857 => 265,  837 => 253,  832 => 250,  827 => 248,  821 => 247,  803 => 243,  778 => 235,  769 => 233,  765 => 232,  753 => 229,  746 => 225,  743 => 224,  735 => 613,  730 => 218,  720 => 213,  717 => 212,  712 => 209,  691 => 204,  678 => 586,  654 => 196,  587 => 14,  576 => 179,  539 => 171,  517 => 169,  471 => 155,  441 => 131,  437 => 142,  418 => 123,  386 => 160,  373 => 95,  304 => 151,  270 => 80,  265 => 83,  229 => 69,  477 => 138,  455 => 393,  448 => 188,  429 => 178,  407 => 95,  399 => 93,  389 => 99,  375 => 123,  358 => 103,  349 => 99,  335 => 84,  327 => 93,  298 => 112,  280 => 75,  249 => 123,  194 => 48,  142 => 50,  344 => 83,  318 => 162,  306 => 15,  295 => 74,  357 => 119,  300 => 150,  286 => 145,  276 => 87,  269 => 66,  254 => 72,  128 => 32,  237 => 69,  165 => 72,  122 => 38,  798 => 242,  770 => 113,  759 => 112,  748 => 337,  731 => 108,  721 => 606,  718 => 106,  708 => 104,  696 => 102,  617 => 188,  590 => 91,  553 => 87,  550 => 466,  540 => 84,  533 => 82,  500 => 129,  493 => 72,  489 => 181,  482 => 69,  467 => 67,  464 => 120,  458 => 35,  452 => 117,  449 => 156,  415 => 122,  382 => 124,  372 => 107,  361 => 104,  356 => 108,  339 => 85,  302 => 42,  285 => 94,  258 => 64,  123 => 42,  108 => 35,  424 => 135,  394 => 86,  380 => 80,  338 => 107,  319 => 16,  316 => 91,  312 => 76,  290 => 146,  267 => 135,  206 => 51,  110 => 32,  240 => 122,  224 => 51,  219 => 50,  217 => 103,  202 => 51,  186 => 100,  170 => 49,  100 => 33,  67 => 28,  14 => 1,  1096 => 291,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 836,  1013 => 275,  1007 => 274,  1002 => 823,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 300,  940 => 298,  937 => 248,  928 => 244,  926 => 243,  915 => 237,  912 => 236,  903 => 231,  898 => 230,  892 => 229,  889 => 748,  887 => 227,  884 => 747,  876 => 222,  874 => 269,  871 => 220,  863 => 214,  861 => 213,  858 => 212,  850 => 263,  843 => 206,  840 => 205,  815 => 245,  812 => 244,  808 => 199,  804 => 120,  799 => 196,  791 => 190,  785 => 238,  775 => 184,  771 => 183,  754 => 340,  728 => 170,  726 => 169,  723 => 168,  715 => 105,  711 => 604,  709 => 162,  706 => 603,  698 => 208,  694 => 156,  692 => 155,  689 => 154,  681 => 150,  677 => 149,  675 => 148,  663 => 143,  661 => 277,  650 => 195,  646 => 136,  629 => 129,  627 => 21,  625 => 127,  622 => 126,  598 => 119,  592 => 117,  586 => 115,  575 => 233,  566 => 153,  556 => 100,  554 => 177,  541 => 216,  536 => 91,  515 => 86,  511 => 85,  509 => 84,  488 => 126,  486 => 78,  483 => 77,  465 => 73,  463 => 397,  450 => 65,  432 => 179,  419 => 105,  371 => 152,  362 => 151,  353 => 103,  337 => 18,  333 => 95,  309 => 127,  303 => 76,  299 => 79,  291 => 96,  272 => 137,  261 => 77,  253 => 58,  239 => 62,  235 => 53,  213 => 86,  200 => 43,  198 => 92,  159 => 36,  149 => 84,  146 => 42,  131 => 40,  116 => 57,  79 => 30,  74 => 20,  71 => 41,  836 => 320,  817 => 319,  814 => 318,  811 => 317,  805 => 313,  787 => 657,  779 => 306,  776 => 305,  773 => 347,  761 => 296,  751 => 175,  747 => 293,  742 => 336,  739 => 291,  736 => 287,  724 => 214,  705 => 278,  702 => 601,  688 => 265,  680 => 263,  667 => 261,  662 => 260,  656 => 259,  649 => 258,  644 => 97,  641 => 194,  624 => 255,  613 => 187,  607 => 93,  597 => 225,  591 => 185,  584 => 218,  579 => 234,  563 => 230,  559 => 208,  551 => 221,  547 => 149,  537 => 201,  524 => 191,  512 => 185,  507 => 76,  504 => 143,  498 => 142,  485 => 176,  480 => 124,  472 => 122,  466 => 153,  460 => 119,  447 => 388,  442 => 162,  434 => 110,  428 => 29,  422 => 106,  404 => 149,  368 => 136,  364 => 105,  340 => 19,  334 => 95,  330 => 94,  325 => 79,  292 => 74,  287 => 115,  282 => 119,  279 => 88,  273 => 83,  266 => 79,  256 => 76,  252 => 67,  228 => 108,  218 => 64,  201 => 93,  64 => 27,  51 => 14,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 1705,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 707,  1829 => 706,  1827 => 705,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 689,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 676,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 666,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 638,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 617,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 611,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 602,  1559 => 601,  1552 => 598,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 584,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 573,  1477 => 567,  1473 => 566,  1467 => 562,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 520,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 511,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 1001,  1257 => 1000,  1254 => 479,  1250 => 478,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 460,  1196 => 958,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 938,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 924,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 425,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 898,  1099 => 420,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 880,  1076 => 359,  1070 => 875,  1057 => 352,  1052 => 404,  1045 => 854,  1040 => 852,  1036 => 283,  1032 => 395,  1028 => 394,  1023 => 393,  1020 => 392,  1016 => 391,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 821,  979 => 379,  974 => 312,  967 => 373,  962 => 803,  958 => 801,  954 => 253,  950 => 368,  945 => 367,  942 => 250,  938 => 365,  934 => 364,  927 => 361,  923 => 292,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 272,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 207,  841 => 321,  835 => 312,  830 => 249,  826 => 309,  822 => 308,  818 => 246,  813 => 306,  810 => 305,  806 => 304,  802 => 197,  795 => 118,  792 => 239,  789 => 350,  784 => 295,  782 => 187,  777 => 291,  772 => 289,  768 => 345,  763 => 343,  760 => 231,  756 => 230,  752 => 284,  745 => 281,  741 => 280,  738 => 335,  732 => 171,  719 => 273,  714 => 280,  710 => 310,  704 => 267,  699 => 103,  695 => 264,  690 => 263,  687 => 203,  683 => 587,  679 => 288,  672 => 284,  668 => 256,  665 => 201,  658 => 141,  645 => 270,  640 => 247,  634 => 96,  628 => 193,  623 => 238,  619 => 237,  611 => 18,  606 => 234,  603 => 467,  599 => 242,  595 => 231,  583 => 114,  580 => 180,  573 => 221,  560 => 101,  543 => 172,  538 => 209,  534 => 208,  530 => 450,  526 => 89,  521 => 146,  518 => 204,  514 => 78,  510 => 202,  503 => 75,  496 => 128,  490 => 193,  484 => 125,  474 => 137,  470 => 168,  446 => 133,  440 => 114,  436 => 113,  431 => 146,  425 => 126,  416 => 104,  412 => 98,  408 => 119,  403 => 170,  400 => 169,  396 => 133,  392 => 169,  385 => 24,  381 => 109,  367 => 106,  363 => 25,  359 => 23,  355 => 88,  350 => 107,  346 => 99,  343 => 20,  328 => 17,  324 => 164,  313 => 122,  307 => 88,  301 => 90,  288 => 27,  283 => 243,  271 => 107,  257 => 78,  251 => 76,  238 => 72,  233 => 68,  195 => 42,  191 => 62,  187 => 41,  183 => 56,  130 => 32,  88 => 24,  76 => 29,  115 => 44,  95 => 53,  655 => 275,  651 => 232,  648 => 231,  637 => 223,  633 => 130,  621 => 219,  618 => 218,  615 => 121,  604 => 186,  600 => 516,  588 => 116,  585 => 204,  582 => 203,  571 => 104,  567 => 178,  555 => 207,  552 => 190,  549 => 215,  544 => 186,  542 => 185,  535 => 212,  531 => 90,  519 => 189,  516 => 199,  513 => 168,  508 => 145,  506 => 83,  499 => 198,  495 => 141,  491 => 139,  481 => 162,  478 => 68,  475 => 157,  469 => 154,  456 => 136,  451 => 186,  443 => 132,  439 => 147,  427 => 89,  423 => 109,  420 => 176,  409 => 54,  405 => 118,  401 => 116,  391 => 129,  387 => 129,  384 => 132,  378 => 153,  365 => 93,  360 => 89,  348 => 21,  336 => 111,  332 => 138,  329 => 165,  323 => 81,  310 => 100,  305 => 94,  277 => 23,  274 => 91,  263 => 70,  259 => 133,  247 => 126,  244 => 74,  241 => 71,  222 => 55,  210 => 63,  207 => 98,  204 => 96,  184 => 46,  181 => 59,  167 => 74,  157 => 31,  96 => 27,  421 => 124,  417 => 137,  414 => 126,  406 => 171,  398 => 129,  393 => 100,  390 => 162,  376 => 110,  369 => 94,  366 => 91,  352 => 87,  345 => 98,  342 => 109,  331 => 106,  326 => 102,  320 => 129,  317 => 90,  314 => 97,  311 => 78,  308 => 60,  297 => 89,  293 => 85,  281 => 71,  278 => 140,  275 => 116,  264 => 87,  260 => 73,  248 => 75,  245 => 106,  242 => 55,  231 => 111,  227 => 68,  215 => 64,  212 => 64,  209 => 63,  197 => 85,  177 => 34,  171 => 66,  161 => 48,  132 => 77,  121 => 24,  105 => 29,  99 => 35,  81 => 36,  77 => 24,  180 => 35,  176 => 57,  156 => 28,  143 => 64,  139 => 27,  118 => 73,  189 => 101,  185 => 67,  173 => 44,  166 => 41,  152 => 62,  174 => 43,  164 => 62,  154 => 30,  150 => 45,  137 => 62,  133 => 29,  127 => 25,  107 => 56,  102 => 33,  83 => 49,  78 => 19,  53 => 12,  23 => 6,  42 => 11,  138 => 80,  134 => 31,  109 => 32,  103 => 30,  97 => 26,  94 => 25,  84 => 21,  75 => 21,  69 => 13,  66 => 17,  54 => 15,  44 => 19,  230 => 67,  226 => 65,  203 => 51,  193 => 56,  188 => 68,  182 => 83,  178 => 58,  168 => 56,  163 => 32,  160 => 69,  155 => 67,  148 => 34,  145 => 33,  140 => 63,  136 => 30,  125 => 39,  120 => 27,  113 => 37,  101 => 54,  92 => 30,  89 => 51,  85 => 31,  73 => 34,  62 => 30,  59 => 16,  56 => 17,  41 => 15,  126 => 37,  119 => 33,  111 => 36,  106 => 23,  98 => 27,  93 => 28,  86 => 28,  70 => 20,  60 => 16,  28 => 6,  36 => 9,  114 => 36,  104 => 55,  91 => 24,  80 => 18,  63 => 23,  58 => 22,  40 => 10,  34 => 8,  45 => 13,  61 => 18,  55 => 9,  48 => 22,  39 => 7,  35 => 9,  31 => 3,  26 => 5,  21 => 5,  46 => 12,  29 => 2,  57 => 17,  50 => 17,  47 => 13,  38 => 10,  33 => 8,  49 => 11,  32 => 8,  246 => 65,  236 => 121,  232 => 70,  225 => 78,  221 => 104,  216 => 63,  214 => 102,  211 => 106,  208 => 46,  205 => 103,  199 => 60,  196 => 59,  190 => 84,  179 => 94,  175 => 53,  172 => 76,  169 => 43,  162 => 89,  158 => 49,  153 => 46,  151 => 66,  147 => 28,  144 => 82,  141 => 42,  135 => 40,  129 => 38,  124 => 75,  117 => 37,  112 => 71,  90 => 19,  87 => 37,  82 => 38,  72 => 23,  68 => 19,  65 => 31,  52 => 14,  43 => 13,  37 => 11,  30 => 7,  27 => 6,  25 => 1,  24 => 6,  22 => 5,  19 => 4,);
    }
}
