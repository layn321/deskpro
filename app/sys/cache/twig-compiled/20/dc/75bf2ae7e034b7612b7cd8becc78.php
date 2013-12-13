<?php

/* UserBundle:Search:label-search.html.twig */
class __TwigTemplate_20dc75bf2ae7e034b7612b7cd8becc78 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("UserBundle::layout.html.twig");

        $this->blocks = array(
            'breadcrumb' => array($this, 'block_breadcrumb'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "UserBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 6
    public function block_breadcrumb($context, array $blocks = array())
    {
        // line 7
        echo "\t<li><a href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_search_labels"), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.portal.search_labels_title");
        echo "</a></li>
";
    }

    // line 9
    public function block_content($context, array $blocks = array())
    {
        // line 10
        echo "
<section class=\"dp-portal-section dp-labels-search\">
\t<div class=\"dp-content-block dp-search-block\">
\t\t<div class=\"dp-search-bar\">
\t\t\t<form action=\"";
        // line 14
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_search_labels"), "html", null, true);
        echo "\" method=\"get\">
\t\t\t<table class=\"dp-layout\" width=\"100%\"><tr>
\t\t\t\t<td style=\"vertical-align: top; white-space: nowrap;\" width=\"100%\" class=\"";
        // line 16
        if (isset($context["error_fields"])) { $_error_fields_ = $context["error_fields"]; } else { $_error_fields_ = null; }
        if ($this->getAttribute($_error_fields_, "title", array(), "array")) {
            echo "dp-error";
        }
        echo "\">
\t\t\t\t\t<div class=\"dp-input-prepend\">
\t\t\t\t\t\t<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\"><tr>
\t\t\t\t\t\t\t<td width=\"10\" class=\"dp-msg\">
\t\t\t\t\t\t\t\t<span style=\"white-space: nowrap;\">";
        // line 20
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.portal.search_label-search_show");
        echo "</span>
\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t<td width=\"10\" class=\"dp-cat dp-cat2\">
\t\t\t\t\t\t\t\t<div style=\"position: relative;\" class=\"dp-inplace-drop\">
\t\t\t\t\t\t\t\t\t<em><span class=\"dp-opt-label\">&nbsp;</span> <i></i></em>
\t\t\t\t\t\t\t\t\t<select name=\"type\">
\t\t\t\t\t\t\t\t\t\t<option ";
        // line 26
        if (isset($context["type"])) { $_type_ = $context["type"]; } else { $_type_ = null; }
        if (($_type_ == "all")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"all\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.portal.search-everything");
        echo "</option>
\t\t\t\t\t\t\t\t\t\t<option ";
        // line 27
        if (isset($context["type"])) { $_type_ = $context["type"]; } else { $_type_ = null; }
        if (($_type_ == "articles")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"articles\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.knowledgebase.title");
        echo "</option>
\t\t\t\t\t\t\t\t\t\t<option ";
        // line 28
        if (isset($context["type"])) { $_type_ = $context["type"]; } else { $_type_ = null; }
        if (($_type_ == "feedback")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"feedback\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.feedback.title");
        echo "</option>
\t\t\t\t\t\t\t\t\t\t<option ";
        // line 29
        if (isset($context["type"])) { $_type_ = $context["type"]; } else { $_type_ = null; }
        if (($_type_ == "news")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"news\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.news.title");
        echo "</option>
\t\t\t\t\t\t\t\t\t\t<option ";
        // line 30
        if (isset($context["type"])) { $_type_ = $context["type"]; } else { $_type_ = null; }
        if (($_type_ == "downloads")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"downloads\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.downloads.title");
        echo "</option>
\t\t\t\t\t\t\t\t\t</select>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t<td width=\"10\" class=\"dp-msg dp-msg2\">
\t\t\t\t\t\t\t\t<span style=\"white-space: nowrap;\">";
        // line 35
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.portal.search_label-search_with");
        echo "</span>
\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t<td class=\"dp-title\">
\t\t\t\t\t\t\t\t<input type=\"text\" class=\"dp-suggest-title\" name=\"label\" value=\"";
        // line 38
        if (isset($context["label"])) { $_label_ = $context["label"]; } else { $_label_ = null; }
        echo twig_escape_filter($this->env, $_label_, "html", null, true);
        echo "\" />
\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t</tr></table>
\t\t\t\t\t</div>
\t\t\t\t</td>
\t\t\t\t<td class=\"dp-go\"><button class=\"dp-btn dp-go-btn dp-btn-success\">";
        // line 43
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.general.go");
        echo "</button></td>
\t\t\t</tr></table>
\t\t\t</form>
\t\t</div>
\t\t<div class=\"dp-tag-cloud dp-well dp-well-small dp-well-light\">
\t\t\t<h4>";
        // line 48
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.portal.more_labels");
        echo "</h4>
\t\t\t<ul>
\t\t\t\t";
        // line 50
        if (isset($context["cloud"])) { $_cloud_ = $context["cloud"]; } else { $_cloud_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_cloud_);
        foreach ($context['_seq'] as $context["c_label"] => $context["css_class"]) {
            // line 51
            echo "\t\t\t\t\t<li class=\"dp-";
            if (isset($context["css_class"])) { $_css_class_ = $context["css_class"]; } else { $_css_class_ = null; }
            echo twig_escape_filter($this->env, $_css_class_, "html", null, true);
            echo "\"><a href=\"";
            if (isset($context["c_label"])) { $_c_label_ = $context["c_label"]; } else { $_c_label_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_search_labels", array("label" => $_c_label_)), "html", null, true);
            echo "\">";
            if (isset($context["c_label"])) { $_c_label_ = $context["c_label"]; } else { $_c_label_ = null; }
            echo twig_escape_filter($this->env, $_c_label_, "html", null, true);
            echo "</a></li>
\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['c_label'], $context['css_class'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 53
        echo "\t\t\t</ul>
\t\t</div>
\t</div>
</section>

";
        // line 58
        if (isset($context["label"])) { $_label_ = $context["label"]; } else { $_label_ = null; }
        if ($_label_) {
            // line 59
            echo "\t<section class=\"dp-portal-section\">
\t\t<header>
\t\t\t<h1>";
            // line 61
            if (isset($context["num_results"])) { $_num_results_ = $context["num_results"]; } else { $_num_results_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.portal.search_result-count", array("count" => $_num_results_));
            echo "</h1>
\t\t</header>
\t\t<div class=\"dp-content-block dp-search-block\">
\t\t\t<div class=\"dp-content-wrapper\">
\t\t\t\t";
            // line 65
            if (isset($context["results"])) { $_results_ = $context["results"]; } else { $_results_ = null; }
            if (isset($context["sticky_results"])) { $_sticky_results_ = $context["sticky_results"]; } else { $_sticky_results_ = null; }
            if (((!$_results_) && (!$_sticky_results_))) {
                // line 66
                echo "\t\t\t\t\t<p>";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.portal.no_results_help");
                echo "</p>
\t\t\t\t";
            } else {
                // line 68
                echo "\t\t\t\t\t<ul class=\"dp-content-list\">
\t\t\t\t\t\t";
                // line 69
                if (isset($context["sticky_results"])) { $_sticky_results_ = $context["sticky_results"]; } else { $_sticky_results_ = null; }
                if ($_sticky_results_) {
                    // line 70
                    echo "\t\t\t\t\t\t\t";
                    if (isset($context["sticky_results"])) { $_sticky_results_ = $context["sticky_results"]; } else { $_sticky_results_ = null; }
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($_sticky_results_);
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
                    foreach ($context['_seq'] as $context["_key"] => $context["res"]) {
                        // line 71
                        echo "\t\t\t\t\t\t\t\t";
                        $this->env->loadTemplate("UserBundle:Search:search-result-item.html.twig")->display(array_merge($context, array("is_sticky" => true)));
                        // line 72
                        echo "\t\t\t\t\t\t\t";
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
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['res'], $context['_parent'], $context['loop']);
                    $context = array_merge($_parent, array_intersect_key($context, $_parent));
                    // line 73
                    echo "\t\t\t\t\t\t";
                }
                // line 74
                echo "\t\t\t\t\t\t";
                if (isset($context["results"])) { $_results_ = $context["results"]; } else { $_results_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($_results_);
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
                foreach ($context['_seq'] as $context["_key"] => $context["res"]) {
                    // line 75
                    echo "\t\t\t\t\t\t\t";
                    $this->env->loadTemplate("UserBundle:Search:search-result-item.html.twig")->display($context);
                    // line 76
                    echo "\t\t\t\t\t\t";
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
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['res'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 77
                echo "\t\t\t\t\t</ul>
\t\t\t\t";
            }
            // line 79
            echo "\t\t\t</div>
\t\t</div>
\t</section>

\t";
            // line 83
            if (isset($context["pageinfo"])) { $_pageinfo_ = $context["pageinfo"]; } else { $_pageinfo_ = null; }
            if (($this->getAttribute($_pageinfo_, "last") != 1)) {
                // line 84
                echo "\t\t";
                if (isset($context["query"])) { $_query_ = $context["query"]; } else { $_query_ = null; }
                $context["page_url"] = ($this->env->getExtension('routing')->getPath("user_search", array("q" => $_query_)) . "&p=");
                // line 85
                echo "\t\t<div class=\"dp-pagination\">
\t\t\t<ul>
\t\t\t\t";
                // line 87
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
                // line 88
                echo "\t\t\t\t";
                if (isset($context["pageinfo"])) { $_pageinfo_ = $context["pageinfo"]; } else { $_pageinfo_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($_pageinfo_, "pages"));
                foreach ($context['_seq'] as $context["_key"] => $context["p"]) {
                    // line 89
                    echo "\t\t\t\t\t<li class=\"";
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
\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['p'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 91
                echo "\t\t\t\t";
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
                // line 92
                echo "\t\t\t</ul>
\t\t</div>
\t";
            }
        }
        // line 96
        echo "
";
    }

    public function getTemplateName()
    {
        return "UserBundle:Search:label-search.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  722 => 269,  697 => 256,  674 => 249,  671 => 248,  577 => 220,  569 => 216,  557 => 209,  502 => 195,  497 => 194,  445 => 173,  729 => 286,  684 => 261,  676 => 256,  669 => 254,  660 => 250,  647 => 243,  643 => 244,  601 => 231,  570 => 211,  522 => 200,  501 => 179,  296 => 149,  374 => 137,  631 => 239,  616 => 19,  608 => 17,  605 => 16,  596 => 15,  574 => 13,  561 => 209,  527 => 147,  433 => 160,  388 => 142,  426 => 177,  383 => 146,  461 => 167,  370 => 113,  395 => 144,  294 => 76,  223 => 84,  220 => 54,  492 => 127,  468 => 121,  444 => 33,  410 => 105,  397 => 101,  377 => 144,  262 => 95,  250 => 129,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 2108,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 1383,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 1303,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 1173,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 1041,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 970,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 920,  1126 => 916,  1106 => 900,  1100 => 897,  1026 => 841,  997 => 822,  983 => 816,  975 => 812,  956 => 800,  939 => 786,  902 => 753,  894 => 749,  879 => 746,  757 => 631,  727 => 608,  716 => 605,  670 => 582,  528 => 447,  476 => 123,  435 => 31,  354 => 110,  341 => 97,  192 => 73,  321 => 163,  243 => 67,  793 => 351,  780 => 348,  758 => 341,  700 => 257,  686 => 292,  652 => 274,  638 => 266,  620 => 254,  545 => 218,  523 => 203,  494 => 183,  459 => 163,  438 => 172,  351 => 116,  347 => 109,  402 => 157,  268 => 75,  430 => 120,  411 => 120,  379 => 96,  322 => 92,  315 => 110,  289 => 79,  284 => 93,  255 => 92,  234 => 88,  1133 => 64,  1124 => 57,  1121 => 56,  1116 => 55,  1113 => 54,  1108 => 51,  1103 => 43,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 346,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 329,  996 => 320,  989 => 316,  985 => 315,  981 => 314,  977 => 313,  970 => 311,  966 => 310,  955 => 303,  952 => 302,  943 => 299,  936 => 296,  930 => 293,  919 => 290,  917 => 289,  908 => 287,  905 => 286,  896 => 280,  891 => 278,  877 => 270,  862 => 267,  857 => 265,  837 => 253,  832 => 250,  827 => 248,  821 => 247,  803 => 243,  778 => 235,  769 => 233,  765 => 232,  753 => 229,  746 => 225,  743 => 224,  735 => 613,  730 => 218,  720 => 213,  717 => 212,  712 => 209,  691 => 204,  678 => 586,  654 => 196,  587 => 14,  576 => 179,  539 => 171,  517 => 197,  471 => 155,  441 => 131,  437 => 142,  418 => 165,  386 => 160,  373 => 95,  304 => 151,  270 => 80,  265 => 83,  229 => 71,  477 => 138,  455 => 177,  448 => 164,  429 => 159,  407 => 95,  399 => 156,  389 => 99,  375 => 123,  358 => 103,  349 => 99,  335 => 84,  327 => 93,  298 => 84,  280 => 75,  249 => 74,  194 => 65,  142 => 50,  344 => 83,  318 => 162,  306 => 87,  295 => 83,  357 => 119,  300 => 150,  286 => 145,  276 => 87,  269 => 66,  254 => 94,  128 => 32,  237 => 69,  165 => 72,  122 => 25,  798 => 242,  770 => 113,  759 => 112,  748 => 337,  731 => 108,  721 => 606,  718 => 106,  708 => 271,  696 => 102,  617 => 234,  590 => 226,  553 => 87,  550 => 466,  540 => 84,  533 => 82,  500 => 129,  493 => 72,  489 => 181,  482 => 69,  467 => 67,  464 => 120,  458 => 166,  452 => 117,  449 => 174,  415 => 163,  382 => 124,  372 => 107,  361 => 104,  356 => 135,  339 => 124,  302 => 85,  285 => 77,  258 => 64,  123 => 35,  108 => 35,  424 => 156,  394 => 86,  380 => 80,  338 => 107,  319 => 88,  316 => 91,  312 => 109,  290 => 146,  267 => 100,  206 => 51,  110 => 30,  240 => 122,  224 => 51,  219 => 81,  217 => 53,  202 => 75,  186 => 61,  170 => 60,  100 => 30,  67 => 15,  14 => 1,  1096 => 291,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 836,  1013 => 275,  1007 => 274,  1002 => 823,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 300,  940 => 298,  937 => 248,  928 => 244,  926 => 243,  915 => 237,  912 => 236,  903 => 231,  898 => 230,  892 => 229,  889 => 748,  887 => 227,  884 => 747,  876 => 222,  874 => 269,  871 => 220,  863 => 214,  861 => 213,  858 => 212,  850 => 263,  843 => 206,  840 => 205,  815 => 245,  812 => 244,  808 => 199,  804 => 120,  799 => 196,  791 => 190,  785 => 238,  775 => 184,  771 => 183,  754 => 340,  728 => 170,  726 => 169,  723 => 168,  715 => 105,  711 => 263,  709 => 162,  706 => 260,  698 => 208,  694 => 255,  692 => 155,  689 => 254,  681 => 150,  677 => 149,  675 => 148,  663 => 251,  661 => 277,  650 => 246,  646 => 136,  629 => 129,  627 => 21,  625 => 236,  622 => 126,  598 => 230,  592 => 117,  586 => 115,  575 => 214,  566 => 210,  556 => 100,  554 => 177,  541 => 216,  536 => 205,  515 => 86,  511 => 85,  509 => 196,  488 => 126,  486 => 78,  483 => 189,  465 => 73,  463 => 179,  450 => 65,  432 => 179,  419 => 155,  371 => 141,  362 => 151,  353 => 129,  337 => 18,  333 => 122,  309 => 127,  303 => 76,  299 => 105,  291 => 111,  272 => 137,  261 => 96,  253 => 58,  239 => 89,  235 => 88,  213 => 52,  200 => 43,  198 => 66,  159 => 64,  149 => 61,  146 => 48,  131 => 44,  116 => 25,  79 => 26,  74 => 26,  71 => 23,  836 => 320,  817 => 319,  814 => 318,  811 => 317,  805 => 313,  787 => 657,  779 => 306,  776 => 305,  773 => 347,  761 => 296,  751 => 175,  747 => 293,  742 => 336,  739 => 291,  736 => 287,  724 => 214,  705 => 278,  702 => 601,  688 => 262,  680 => 263,  667 => 261,  662 => 246,  656 => 249,  649 => 258,  644 => 97,  641 => 241,  624 => 236,  613 => 233,  607 => 232,  597 => 221,  591 => 220,  584 => 223,  579 => 234,  563 => 213,  559 => 208,  551 => 202,  547 => 200,  537 => 201,  524 => 191,  512 => 185,  507 => 76,  504 => 143,  498 => 142,  485 => 176,  480 => 124,  472 => 171,  466 => 153,  460 => 119,  447 => 388,  442 => 162,  434 => 110,  428 => 29,  422 => 166,  404 => 149,  368 => 136,  364 => 133,  340 => 19,  334 => 130,  330 => 94,  325 => 89,  292 => 102,  287 => 109,  282 => 119,  279 => 98,  273 => 103,  266 => 98,  256 => 76,  252 => 67,  228 => 85,  218 => 81,  201 => 93,  64 => 11,  51 => 14,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 1705,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 707,  1829 => 706,  1827 => 705,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 689,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 676,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 666,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 638,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 617,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 611,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 602,  1559 => 601,  1552 => 598,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 584,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 573,  1477 => 567,  1473 => 566,  1467 => 562,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 520,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 511,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 1001,  1257 => 1000,  1254 => 479,  1250 => 478,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 460,  1196 => 958,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 938,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 924,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 425,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 898,  1099 => 420,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 880,  1076 => 359,  1070 => 875,  1057 => 352,  1052 => 404,  1045 => 854,  1040 => 852,  1036 => 283,  1032 => 395,  1028 => 394,  1023 => 393,  1020 => 392,  1016 => 391,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 821,  979 => 379,  974 => 312,  967 => 373,  962 => 803,  958 => 801,  954 => 253,  950 => 368,  945 => 367,  942 => 250,  938 => 365,  934 => 364,  927 => 361,  923 => 292,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 272,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 207,  841 => 321,  835 => 312,  830 => 249,  826 => 309,  822 => 308,  818 => 246,  813 => 306,  810 => 305,  806 => 304,  802 => 197,  795 => 118,  792 => 239,  789 => 350,  784 => 295,  782 => 187,  777 => 291,  772 => 289,  768 => 345,  763 => 343,  760 => 231,  756 => 230,  752 => 284,  745 => 281,  741 => 280,  738 => 335,  732 => 171,  719 => 279,  714 => 264,  710 => 310,  704 => 267,  699 => 103,  695 => 264,  690 => 263,  687 => 203,  683 => 251,  679 => 288,  672 => 255,  668 => 247,  665 => 201,  658 => 244,  645 => 270,  640 => 247,  634 => 238,  628 => 193,  623 => 238,  619 => 237,  611 => 18,  606 => 234,  603 => 467,  599 => 242,  595 => 231,  583 => 216,  580 => 221,  573 => 221,  560 => 101,  543 => 172,  538 => 209,  534 => 208,  530 => 202,  526 => 187,  521 => 146,  518 => 185,  514 => 183,  510 => 202,  503 => 75,  496 => 128,  490 => 193,  484 => 125,  474 => 137,  470 => 168,  446 => 133,  440 => 114,  436 => 113,  431 => 146,  425 => 126,  416 => 104,  412 => 98,  408 => 149,  403 => 170,  400 => 169,  396 => 133,  392 => 152,  385 => 24,  381 => 139,  367 => 134,  363 => 139,  359 => 92,  355 => 88,  350 => 128,  346 => 127,  343 => 20,  328 => 17,  324 => 164,  313 => 122,  307 => 88,  301 => 90,  288 => 27,  283 => 108,  271 => 76,  257 => 93,  251 => 76,  238 => 88,  233 => 68,  195 => 42,  191 => 68,  187 => 66,  183 => 64,  130 => 32,  88 => 32,  76 => 29,  115 => 44,  95 => 35,  655 => 275,  651 => 232,  648 => 231,  637 => 241,  633 => 130,  621 => 234,  618 => 233,  615 => 232,  604 => 186,  600 => 516,  588 => 116,  585 => 204,  582 => 203,  571 => 104,  567 => 178,  555 => 207,  552 => 190,  549 => 208,  544 => 199,  542 => 207,  535 => 212,  531 => 189,  519 => 189,  516 => 199,  513 => 168,  508 => 145,  506 => 83,  499 => 198,  495 => 141,  491 => 191,  481 => 162,  478 => 172,  475 => 184,  469 => 182,  456 => 136,  451 => 186,  443 => 132,  439 => 162,  427 => 169,  423 => 109,  420 => 176,  409 => 159,  405 => 148,  401 => 147,  391 => 129,  387 => 129,  384 => 132,  378 => 138,  365 => 96,  360 => 132,  348 => 21,  336 => 123,  332 => 138,  329 => 127,  323 => 81,  310 => 116,  305 => 94,  277 => 103,  274 => 91,  263 => 70,  259 => 133,  247 => 92,  244 => 91,  241 => 71,  222 => 55,  210 => 70,  207 => 69,  204 => 68,  184 => 46,  181 => 70,  167 => 38,  157 => 31,  96 => 20,  421 => 124,  417 => 137,  414 => 152,  406 => 171,  398 => 129,  393 => 100,  390 => 162,  376 => 110,  369 => 94,  366 => 91,  352 => 134,  345 => 91,  342 => 109,  331 => 106,  326 => 102,  320 => 129,  317 => 90,  314 => 117,  311 => 78,  308 => 60,  297 => 89,  293 => 85,  281 => 71,  278 => 140,  275 => 116,  264 => 87,  260 => 73,  248 => 75,  245 => 90,  242 => 90,  231 => 111,  227 => 85,  215 => 64,  212 => 78,  209 => 63,  197 => 71,  177 => 34,  171 => 66,  161 => 57,  132 => 52,  121 => 24,  105 => 29,  99 => 38,  81 => 22,  77 => 26,  180 => 35,  176 => 62,  156 => 51,  143 => 30,  139 => 49,  118 => 73,  189 => 101,  185 => 67,  173 => 61,  166 => 41,  152 => 62,  174 => 67,  164 => 65,  154 => 30,  150 => 33,  137 => 62,  133 => 29,  127 => 42,  107 => 40,  102 => 38,  83 => 27,  78 => 21,  53 => 14,  23 => 6,  42 => 15,  138 => 43,  134 => 31,  109 => 42,  103 => 39,  97 => 32,  94 => 27,  84 => 21,  75 => 20,  69 => 17,  66 => 19,  54 => 15,  44 => 10,  230 => 67,  226 => 85,  203 => 51,  193 => 56,  188 => 68,  182 => 59,  178 => 69,  168 => 56,  163 => 32,  160 => 35,  155 => 63,  148 => 34,  145 => 52,  140 => 47,  136 => 30,  125 => 43,  120 => 27,  113 => 37,  101 => 29,  92 => 28,  89 => 29,  85 => 31,  73 => 14,  62 => 13,  59 => 18,  56 => 17,  41 => 9,  126 => 50,  119 => 46,  111 => 41,  106 => 35,  98 => 36,  93 => 35,  86 => 18,  70 => 21,  60 => 15,  28 => 2,  36 => 6,  114 => 38,  104 => 32,  91 => 26,  80 => 18,  63 => 11,  58 => 12,  40 => 12,  34 => 13,  45 => 16,  61 => 10,  55 => 16,  48 => 17,  39 => 5,  35 => 4,  31 => 3,  26 => 9,  21 => 5,  46 => 9,  29 => 6,  57 => 20,  50 => 14,  47 => 16,  38 => 9,  33 => 10,  49 => 7,  32 => 7,  246 => 73,  236 => 121,  232 => 72,  225 => 84,  221 => 104,  216 => 80,  214 => 102,  211 => 79,  208 => 76,  205 => 76,  199 => 74,  196 => 59,  190 => 84,  179 => 58,  175 => 53,  172 => 53,  169 => 43,  162 => 89,  158 => 49,  153 => 46,  151 => 50,  147 => 28,  144 => 58,  141 => 56,  135 => 53,  129 => 38,  124 => 75,  117 => 37,  112 => 43,  90 => 32,  87 => 31,  82 => 26,  72 => 23,  68 => 13,  65 => 20,  52 => 10,  43 => 13,  37 => 7,  30 => 9,  27 => 2,  25 => 6,  24 => 7,  22 => 6,  19 => 4,);
    }
}
