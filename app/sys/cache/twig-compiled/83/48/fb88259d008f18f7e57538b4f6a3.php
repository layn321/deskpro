<?php

/* AgentBundle:Common:select-standard.html.twig */
class __TwigTemplate_8348fb88259d008f18f7e57538b4f6a3 extends \Application\DeskPRO\Twig\Template
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
        // line 53
        echo "<select ";
        if (isset($context["name"])) { $_name_ = $context["name"]; } else { $_name_ = null; }
        if ($_name_) {
            echo "name=\"";
            if (isset($context["name"])) { $_name_ = $context["name"]; } else { $_name_ = null; }
            echo twig_escape_filter($this->env, $_name_, "html", null, true);
            echo "\"";
        }
        echo " ";
        if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
        if ($_id_) {
            echo "id=\"";
            if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
            echo twig_escape_filter($this->env, $_id_, "html", null, true);
            echo "\"";
        }
        echo " class=\"";
        if (isset($context["add_classname"])) { $_add_classname_ = $context["add_classname"]; } else { $_add_classname_ = null; }
        echo twig_escape_filter($this->env, $_add_classname_, "html", null, true);
        echo "\" ";
        if (isset($context["add_attr"])) { $_add_attr_ = $context["add_attr"]; } else { $_add_attr_ = null; }
        echo $_add_attr_;
        echo ">
\t";
        // line 54
        if (isset($context["with_blank"])) { $_with_blank_ = $context["with_blank"]; } else { $_with_blank_ = null; }
        if ($_with_blank_) {
            echo "<option value=\"";
            if (isset($context["with_blank_value"])) { $_with_blank_value_ = $context["with_blank_value"]; } else { $_with_blank_value_ = null; }
            echo twig_escape_filter($this->env, ((array_key_exists("with_blank_value", $context)) ? (_twig_default_filter($_with_blank_value_, "0")) : ("0")), "html", null, true);
            echo "\">";
            if (isset($context["blank_title"])) { $_blank_title_ = $context["blank_title"]; } else { $_blank_title_ = null; }
            echo twig_escape_filter($this->env, ((array_key_exists("blank_title", $context)) ? (_twig_default_filter($_blank_title_, "")) : ("")), "html", null, true);
            echo "</option>";
        }
        // line 55
        echo "\t";
        if (isset($context["categories"])) { $_categories_ = $context["categories"]; } else { $_categories_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_categories_);
        foreach ($context['_seq'] as $context["k"] => $context["cat"]) {
            // line 56
            echo "\t\t";
            if (isset($context["k"])) { $_k_ = $context["k"]; } else { $_k_ = null; }
            if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
            if (isset($context["selected"])) { $_selected_ = $context["selected"]; } else { $_selected_ = null; }
            if (isset($context["title_prop"])) { $_title_prop_ = $context["title_prop"]; } else { $_title_prop_ = null; }
            if (isset($context["child_prop"])) { $_child_prop_ = $context["child_prop"]; } else { $_child_prop_ = null; }
            if (isset($context["allow_parent_sel"])) { $_allow_parent_sel_ = $context["allow_parent_sel"]; } else { $_allow_parent_sel_ = null; }
            echo $this->getAttribute($this, "render_options", array(0 => $_k_, 1 => $_cat_, 2 => $_selected_, 3 => 0, 4 => "", 5 => $_title_prop_, 6 => $_child_prop_, 7 => $_allow_parent_sel_), "method");
            echo "
\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['k'], $context['cat'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 58
        echo "</select>
";
    }

    // line 1
    public function getrender_options($_k = null, $_cat = null, $_selected = null, $_depth = null, $_parent_title = null, $_title_prop = null, $_child_prop = null, $_allow_parent_sel = null)
    {
        $context = $this->env->mergeGlobals(array(
            "k" => $_k,
            "cat" => $_cat,
            "selected" => $_selected,
            "depth" => $_depth,
            "parent_title" => $_parent_title,
            "title_prop" => $_title_prop,
            "child_prop" => $_child_prop,
            "allow_parent_sel" => $_allow_parent_sel,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 2
            echo "\t";
            if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
            if (isset($context["k"])) { $_k_ = $context["k"]; } else { $_k_ = null; }
            $context["id"] = (($this->getAttribute($_cat_, "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_cat_, "id"), $_k_)) : ($_k_));
            // line 3
            echo "\t";
            if (isset($context["title_prop"])) { $_title_prop_ = $context["title_prop"]; } else { $_title_prop_ = null; }
            if ($_title_prop_) {
                // line 4
                echo "\t\t";
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                $context["title"] = $this->env->getExtension('deskpro_templating')->getPhraseObject($_cat_, "title");
                // line 5
                echo "\t\t";
                if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
                if ((!$_title_)) {
                    // line 6
                    echo "\t\t\t";
                    if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                    if (isset($context["title_prop"])) { $_title_prop_ = $context["title_prop"]; } else { $_title_prop_ = null; }
                    $context["title"] = $this->getAttribute($_cat_, $_title_prop_, array(), "array");
                    // line 7
                    echo "\t\t";
                }
                // line 8
                echo "\t";
            } else {
                // line 9
                echo "\t\t";
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                if ($this->getAttribute($_cat_, "title", array(), "any", true, true)) {
                    // line 10
                    echo "\t\t\t";
                    if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                    $context["title"] = $this->getAttribute($_cat_, "title");
                    // line 11
                    echo "\t\t";
                }
                // line 12
                echo "\t\t";
                if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                if (((!$_title_) && $this->getAttribute($_cat_, "name", array(), "any", true, true))) {
                    // line 13
                    echo "\t\t\t";
                    if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                    $context["title"] = $this->getAttribute($_cat_, "name");
                    // line 14
                    echo "\t\t";
                }
                // line 15
                echo "\t\t";
                if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                if (((!$_title_) && ($this->env->getExtension('deskpro_templating')->getType($_cat_) == "string"))) {
                    // line 16
                    echo "\t\t\t";
                    if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                    $context["title"] = $_cat_;
                    // line 17
                    echo "\t\t";
                }
                // line 18
                echo "\t\t";
                if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
                if ((!$_title_)) {
                    // line 19
                    echo "\t\t\t";
                    $context["title"] = "(Untitled)";
                    // line 20
                    echo "\t\t";
                }
                // line 21
                echo "\t";
            }
            // line 22
            echo "\t";
            if (isset($context["parent_title"])) { $_parent_title_ = $context["parent_title"]; } else { $_parent_title_ = null; }
            if ($_parent_title_) {
                // line 23
                echo "\t\t";
                if (isset($context["parent_title"])) { $_parent_title_ = $context["parent_title"]; } else { $_parent_title_ = null; }
                if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
                $context["full_title"] = (($_parent_title_ . " > ") . $_title_);
                // line 24
                echo "\t";
            } else {
                // line 25
                echo "\t\t";
                if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
                $context["full_title"] = $_title_;
                // line 26
                echo "\t";
            }
            // line 27
            echo "\t";
            if (isset($context["child_prop"])) { $_child_prop_ = $context["child_prop"]; } else { $_child_prop_ = null; }
            if ((!$_child_prop_)) {
                $context["child_prop"] = "children";
            }
            // line 28
            echo "\t";
            if (isset($context["allow_parent_sel"])) { $_allow_parent_sel_ = $context["allow_parent_sel"]; } else { $_allow_parent_sel_ = null; }
            if ($_allow_parent_sel_) {
                // line 29
                echo "\t\t<option data-full-title=\"";
                if (isset($context["full_title"])) { $_full_title_ = $context["full_title"]; } else { $_full_title_ = null; }
                echo twig_escape_filter($this->env, $_full_title_, "html", null, true);
                echo "\" value=\"";
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                echo twig_escape_filter($this->env, $_id_, "html", null, true);
                echo "\" ";
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                if (isset($context["selected"])) { $_selected_ = $context["selected"]; } else { $_selected_ = null; }
                if (($_id_ == $_selected_)) {
                    echo "selected=\"selected\"";
                }
                echo ">";
                if (isset($context["depth"])) { $_depth_ = $context["depth"]; } else { $_depth_ = null; }
                if (($_depth_ > 0)) {
                    if (isset($context["depth"])) { $_depth_ = $context["depth"]; } else { $_depth_ = null; }
                    echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->strRepeat("--", $_depth_), "html", null, true);
                    echo " ";
                }
                if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
                echo twig_escape_filter($this->env, $_title_, "html", null, true);
                echo "</option>
\t\t";
                // line 30
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                if (isset($context["child_prop"])) { $_child_prop_ = $context["child_prop"]; } else { $_child_prop_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($_cat_, $_child_prop_, array(), "array"));
                foreach ($context['_seq'] as $context["subk"] => $context["subcat"]) {
                    // line 31
                    echo "\t\t\t";
                    if (isset($context["subk"])) { $_subk_ = $context["subk"]; } else { $_subk_ = null; }
                    if (isset($context["subcat"])) { $_subcat_ = $context["subcat"]; } else { $_subcat_ = null; }
                    if (isset($context["selected"])) { $_selected_ = $context["selected"]; } else { $_selected_ = null; }
                    if (isset($context["depth"])) { $_depth_ = $context["depth"]; } else { $_depth_ = null; }
                    if (isset($context["full_title"])) { $_full_title_ = $context["full_title"]; } else { $_full_title_ = null; }
                    if (isset($context["title_prop"])) { $_title_prop_ = $context["title_prop"]; } else { $_title_prop_ = null; }
                    if (isset($context["child_prop"])) { $_child_prop_ = $context["child_prop"]; } else { $_child_prop_ = null; }
                    echo $this->getAttribute($this, "render_options", array(0 => $_subk_, 1 => $_subcat_, 2 => $_selected_, 3 => ($_depth_ + 1), 4 => $_full_title_, 5 => $_title_prop_, 6 => $_child_prop_), "method");
                    echo "
\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['subk'], $context['subcat'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 33
                echo "\t";
            } else {
                // line 34
                echo "\t\t";
                if (isset($context["depth"])) { $_depth_ = $context["depth"]; } else { $_depth_ = null; }
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                if (isset($context["child_prop"])) { $_child_prop_ = $context["child_prop"]; } else { $_child_prop_ = null; }
                if ((($_depth_ == 0) && $this->getAttribute($_cat_, $_child_prop_, array(), "array"))) {
                    // line 35
                    echo "\t\t\t<optgroup label=\"";
                    if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
                    echo twig_escape_filter($this->env, $_title_, "html", null, true);
                    echo "\">
\t\t\t\t";
                    // line 36
                    if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                    if (isset($context["child_prop"])) { $_child_prop_ = $context["child_prop"]; } else { $_child_prop_ = null; }
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($_cat_, $_child_prop_, array(), "array"));
                    foreach ($context['_seq'] as $context["subk"] => $context["subcat"]) {
                        // line 37
                        echo "\t\t\t\t\t";
                        if (isset($context["subk"])) { $_subk_ = $context["subk"]; } else { $_subk_ = null; }
                        if (isset($context["subcat"])) { $_subcat_ = $context["subcat"]; } else { $_subcat_ = null; }
                        if (isset($context["selected"])) { $_selected_ = $context["selected"]; } else { $_selected_ = null; }
                        if (isset($context["depth"])) { $_depth_ = $context["depth"]; } else { $_depth_ = null; }
                        if (isset($context["full_title"])) { $_full_title_ = $context["full_title"]; } else { $_full_title_ = null; }
                        if (isset($context["title_prop"])) { $_title_prop_ = $context["title_prop"]; } else { $_title_prop_ = null; }
                        if (isset($context["child_prop"])) { $_child_prop_ = $context["child_prop"]; } else { $_child_prop_ = null; }
                        echo $this->getAttribute($this, "render_options", array(0 => $_subk_, 1 => $_subcat_, 2 => $_selected_, 3 => ($_depth_ + 1), 4 => $_full_title_, 5 => $_title_prop_, 6 => $_child_prop_), "method");
                        echo "
\t\t\t\t";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['subk'], $context['subcat'], $context['_parent'], $context['loop']);
                    $context = array_merge($_parent, array_intersect_key($context, $_parent));
                    // line 39
                    echo "\t\t\t</optgroup>
\t\t";
                } else {
                    // line 41
                    echo "\t\t\t<option data-full-title=\"";
                    if (isset($context["full_title"])) { $_full_title_ = $context["full_title"]; } else { $_full_title_ = null; }
                    echo twig_escape_filter($this->env, $_full_title_, "html", null, true);
                    echo "\" value=\"";
                    if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                    echo twig_escape_filter($this->env, $_id_, "html", null, true);
                    echo "\" ";
                    if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                    if (isset($context["selected"])) { $_selected_ = $context["selected"]; } else { $_selected_ = null; }
                    if (($_id_ == $_selected_)) {
                        echo "selected=\"selected\"";
                    }
                    echo ">";
                    if (isset($context["depth"])) { $_depth_ = $context["depth"]; } else { $_depth_ = null; }
                    if (isset($context["min_depth_indent"])) { $_min_depth_indent_ = $context["min_depth_indent"]; } else { $_min_depth_indent_ = null; }
                    if (($_depth_ > $_min_depth_indent_)) {
                        if (isset($context["depth"])) { $_depth_ = $context["depth"]; } else { $_depth_ = null; }
                        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->strRepeat("--", $_depth_), "html", null, true);
                        echo " ";
                    }
                    if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
                    echo twig_escape_filter($this->env, $_title_, "html", null, true);
                    echo "</option>

\t\t\t";
                    // line 43
                    if (isset($context["child_prop"])) { $_child_prop_ = $context["child_prop"]; } else { $_child_prop_ = null; }
                    if (($_child_prop_ != "NONE")) {
                        // line 44
                        echo "\t\t\t\t";
                        if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                        if (isset($context["child_prop"])) { $_child_prop_ = $context["child_prop"]; } else { $_child_prop_ = null; }
                        if ($this->getAttribute($_cat_, $_child_prop_, array(), "array")) {
                            // line 45
                            echo "\t\t\t\t\t";
                            if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                            if (isset($context["child_prop"])) { $_child_prop_ = $context["child_prop"]; } else { $_child_prop_ = null; }
                            $context['_parent'] = (array) $context;
                            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_cat_, $_child_prop_, array(), "array"));
                            foreach ($context['_seq'] as $context["subk"] => $context["subcat"]) {
                                // line 46
                                echo "\t\t\t\t\t\t";
                                if (isset($context["subk"])) { $_subk_ = $context["subk"]; } else { $_subk_ = null; }
                                if (isset($context["subcat"])) { $_subcat_ = $context["subcat"]; } else { $_subcat_ = null; }
                                if (isset($context["selected"])) { $_selected_ = $context["selected"]; } else { $_selected_ = null; }
                                if (isset($context["depth"])) { $_depth_ = $context["depth"]; } else { $_depth_ = null; }
                                if (isset($context["full_title"])) { $_full_title_ = $context["full_title"]; } else { $_full_title_ = null; }
                                if (isset($context["title_prop"])) { $_title_prop_ = $context["title_prop"]; } else { $_title_prop_ = null; }
                                if (isset($context["child_prop"])) { $_child_prop_ = $context["child_prop"]; } else { $_child_prop_ = null; }
                                echo $this->getAttribute($this, "render_options", array(0 => $_subk_, 1 => $_subcat_, 2 => $_selected_, 3 => ($_depth_ + 1), 4 => $_full_title_, 5 => $_title_prop_, 6 => $_child_prop_), "method");
                                echo "
\t\t\t\t\t";
                            }
                            $_parent = $context['_parent'];
                            unset($context['_seq'], $context['_iterated'], $context['subk'], $context['subcat'], $context['_parent'], $context['loop']);
                            $context = array_merge($_parent, array_intersect_key($context, $_parent));
                            // line 48
                            echo "\t\t\t\t";
                        }
                        // line 49
                        echo "\t\t\t";
                    }
                    // line 50
                    echo "\t\t";
                }
                // line 51
                echo "\t";
            }
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    public function getTemplateName()
    {
        return "AgentBundle:Common:select-standard.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1038 => 364,  904 => 322,  882 => 318,  831 => 303,  860 => 314,  790 => 278,  733 => 210,  707 => 206,  744 => 79,  873 => 74,  824 => 67,  762 => 271,  713 => 248,  578 => 292,  2739 => 1153,  2735 => 1152,  2732 => 1151,  2725 => 1146,  2712 => 1144,  2707 => 1143,  2692 => 1131,  2688 => 1130,  2683 => 1127,  2680 => 1126,  2677 => 1125,  2671 => 1121,  2658 => 1119,  2653 => 1118,  2645 => 1113,  2641 => 1112,  2636 => 1109,  2632 => 1108,  2629 => 1107,  2626 => 1106,  2606 => 1089,  2602 => 1088,  2598 => 1087,  2594 => 1086,  2590 => 1085,  2586 => 1084,  2572 => 1073,  2568 => 1072,  2564 => 1071,  2560 => 1070,  2556 => 1069,  2552 => 1068,  2537 => 1056,  2533 => 1055,  2529 => 1054,  2525 => 1053,  2521 => 1052,  2503 => 1040,  2499 => 1039,  2495 => 1038,  2491 => 1037,  2487 => 1036,  2483 => 1035,  2469 => 1024,  2465 => 1023,  2461 => 1022,  2457 => 1021,  2453 => 1020,  2449 => 1019,  2426 => 999,  2422 => 998,  2418 => 997,  2414 => 996,  2410 => 995,  2406 => 994,  2392 => 983,  2388 => 982,  2384 => 981,  2380 => 980,  2376 => 979,  2372 => 978,  2358 => 967,  2354 => 966,  2350 => 965,  2346 => 964,  2342 => 963,  2338 => 962,  2324 => 951,  2320 => 950,  2316 => 949,  2312 => 948,  2308 => 947,  2304 => 946,  2295 => 939,  2282 => 937,  2277 => 936,  2269 => 931,  2265 => 930,  2260 => 927,  2256 => 926,  2253 => 925,  2230 => 901,  2220 => 899,  2216 => 898,  2211 => 896,  2208 => 895,  2198 => 893,  2194 => 892,  2189 => 890,  2184 => 889,  2177 => 885,  2173 => 884,  2164 => 877,  2151 => 875,  2146 => 874,  2143 => 873,  2141 => 864,  2134 => 860,  2130 => 859,  2122 => 853,  2120 => 852,  2113 => 848,  2105 => 846,  2096 => 839,  2083 => 837,  2078 => 836,  2073 => 834,  2067 => 830,  2062 => 828,  2058 => 827,  2054 => 826,  2050 => 825,  2045 => 824,  2038 => 822,  2034 => 821,  2022 => 814,  2009 => 812,  1999 => 809,  1993 => 805,  1988 => 803,  1984 => 802,  1980 => 801,  1976 => 800,  1971 => 799,  1968 => 798,  1964 => 797,  1960 => 796,  1954 => 793,  1946 => 788,  1942 => 787,  1938 => 786,  1932 => 782,  1927 => 780,  1919 => 778,  1915 => 777,  1911 => 776,  1906 => 775,  1903 => 774,  1899 => 773,  1895 => 772,  1886 => 768,  1883 => 764,  1871 => 759,  1866 => 756,  1863 => 755,  1842 => 752,  1825 => 749,  1818 => 748,  1815 => 747,  1800 => 743,  1793 => 741,  1772 => 729,  1761 => 721,  1757 => 720,  1751 => 717,  1740 => 709,  1719 => 697,  1715 => 696,  1686 => 673,  1676 => 669,  1665 => 661,  1661 => 660,  1645 => 651,  1638 => 649,  1630 => 645,  1625 => 642,  1622 => 641,  1598 => 637,  1577 => 634,  1558 => 629,  1549 => 627,  1546 => 626,  1535 => 618,  1531 => 617,  1514 => 606,  1510 => 605,  1504 => 602,  1493 => 594,  1483 => 590,  1464 => 580,  1458 => 577,  1443 => 568,  1437 => 565,  1426 => 557,  1405 => 545,  1401 => 544,  1395 => 541,  1390 => 538,  1381 => 533,  1373 => 531,  1348 => 521,  1343 => 520,  1335 => 515,  1321 => 510,  1318 => 509,  1299 => 503,  1294 => 502,  1282 => 496,  1269 => 491,  1260 => 483,  1240 => 478,  1221 => 476,  1216 => 475,  1210 => 474,  1206 => 473,  1193 => 467,  1189 => 466,  1155 => 450,  1150 => 447,  1022 => 411,  1006 => 404,  988 => 398,  969 => 392,  965 => 391,  921 => 370,  878 => 355,  866 => 349,  854 => 346,  819 => 293,  796 => 330,  1713 => 683,  1700 => 681,  1685 => 674,  1662 => 670,  1643 => 669,  1640 => 668,  1631 => 663,  1618 => 661,  1613 => 660,  1608 => 639,  1605 => 656,  1602 => 655,  1589 => 645,  1586 => 644,  1583 => 643,  1554 => 613,  1547 => 608,  1521 => 602,  1508 => 600,  1499 => 598,  1491 => 593,  1482 => 587,  1472 => 582,  1468 => 581,  1465 => 579,  1457 => 573,  1445 => 566,  1422 => 556,  1416 => 553,  1410 => 548,  1397 => 546,  1392 => 545,  1386 => 541,  1377 => 532,  1370 => 530,  1357 => 528,  1352 => 527,  1341 => 518,  1314 => 510,  1311 => 509,  1275 => 493,  1248 => 484,  1238 => 478,  1225 => 476,  1220 => 475,  1209 => 466,  1185 => 459,  1182 => 463,  1159 => 450,  1154 => 449,  1130 => 438,  1125 => 437,  1101 => 426,  1074 => 432,  1056 => 407,  1046 => 418,  1043 => 404,  1030 => 397,  1027 => 412,  947 => 361,  925 => 352,  913 => 324,  893 => 338,  881 => 335,  847 => 343,  829 => 336,  825 => 304,  1083 => 434,  995 => 383,  984 => 350,  963 => 319,  941 => 375,  851 => 271,  682 => 209,  1365 => 549,  1359 => 545,  1349 => 539,  1336 => 537,  1331 => 514,  1323 => 515,  1319 => 530,  1312 => 505,  1284 => 519,  1272 => 492,  1268 => 509,  1261 => 506,  1251 => 485,  1245 => 483,  1231 => 496,  1207 => 493,  1197 => 491,  1180 => 484,  1173 => 457,  1169 => 455,  1157 => 475,  1147 => 446,  1109 => 440,  1065 => 440,  1059 => 423,  1047 => 428,  1044 => 424,  1033 => 420,  1009 => 357,  991 => 351,  987 => 404,  973 => 395,  931 => 355,  924 => 371,  911 => 298,  906 => 81,  885 => 336,  872 => 317,  855 => 72,  749 => 53,  701 => 239,  594 => 109,  1163 => 454,  1143 => 440,  1087 => 420,  1077 => 433,  1051 => 430,  1037 => 480,  1010 => 405,  999 => 384,  932 => 326,  899 => 405,  895 => 404,  933 => 84,  914 => 133,  909 => 323,  833 => 238,  783 => 235,  755 => 320,  666 => 63,  453 => 187,  639 => 58,  568 => 254,  520 => 110,  657 => 60,  572 => 186,  609 => 17,  20 => 1,  659 => 207,  562 => 185,  548 => 165,  558 => 174,  479 => 157,  589 => 7,  457 => 145,  413 => 119,  953 => 430,  948 => 403,  935 => 394,  929 => 372,  916 => 382,  864 => 365,  844 => 307,  816 => 342,  807 => 234,  801 => 338,  774 => 274,  766 => 57,  737 => 49,  685 => 293,  664 => 231,  635 => 281,  593 => 185,  546 => 118,  532 => 68,  865 => 221,  852 => 241,  838 => 304,  820 => 201,  781 => 327,  764 => 320,  725 => 46,  632 => 283,  602 => 167,  565 => 130,  529 => 62,  505 => 267,  487 => 101,  473 => 87,  1853 => 842,  1849 => 753,  1839 => 751,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 733,  1778 => 732,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 693,  1705 => 781,  1688 => 675,  1681 => 769,  1677 => 768,  1667 => 671,  1656 => 762,  1648 => 655,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 606,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 593,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 540,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 522,  1289 => 520,  1276 => 604,  1263 => 601,  1244 => 480,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 463,  1181 => 561,  1171 => 557,  1158 => 550,  1114 => 428,  1104 => 465,  1072 => 512,  1062 => 7,  1024 => 395,  1014 => 406,  1000 => 328,  990 => 382,  980 => 474,  960 => 466,  918 => 348,  888 => 80,  834 => 307,  673 => 64,  636 => 185,  462 => 92,  454 => 253,  1144 => 542,  1139 => 399,  1131 => 399,  1127 => 397,  1110 => 396,  1092 => 459,  1089 => 390,  1086 => 520,  1084 => 419,  1063 => 387,  1060 => 386,  1055 => 422,  1050 => 373,  1035 => 372,  1019 => 330,  1003 => 263,  959 => 387,  900 => 366,  880 => 276,  870 => 350,  867 => 353,  859 => 362,  848 => 215,  839 => 376,  828 => 302,  823 => 374,  809 => 181,  800 => 315,  797 => 62,  794 => 280,  786 => 174,  740 => 78,  734 => 261,  703 => 205,  693 => 236,  630 => 278,  626 => 19,  614 => 275,  610 => 169,  581 => 293,  564 => 229,  525 => 61,  722 => 251,  697 => 238,  674 => 279,  671 => 29,  577 => 257,  569 => 243,  557 => 229,  502 => 99,  497 => 159,  445 => 85,  729 => 209,  684 => 281,  676 => 65,  669 => 254,  660 => 61,  647 => 198,  643 => 270,  601 => 306,  570 => 165,  522 => 165,  501 => 149,  296 => 110,  374 => 205,  631 => 265,  616 => 208,  608 => 53,  605 => 77,  596 => 102,  574 => 132,  561 => 175,  527 => 113,  433 => 166,  388 => 151,  426 => 142,  383 => 135,  461 => 155,  370 => 112,  395 => 224,  294 => 105,  223 => 56,  220 => 41,  492 => 180,  468 => 132,  444 => 168,  410 => 229,  397 => 78,  377 => 134,  262 => 52,  250 => 33,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 1051,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 680,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 638,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 572,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 526,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 494,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 538,  1126 => 443,  1106 => 528,  1100 => 897,  1026 => 418,  997 => 822,  983 => 816,  975 => 347,  956 => 318,  939 => 786,  902 => 362,  894 => 364,  879 => 76,  757 => 55,  727 => 316,  716 => 44,  670 => 278,  528 => 180,  476 => 253,  435 => 143,  354 => 50,  341 => 61,  192 => 28,  321 => 57,  243 => 47,  793 => 350,  780 => 311,  758 => 335,  700 => 212,  686 => 194,  652 => 185,  638 => 269,  620 => 174,  545 => 223,  523 => 152,  494 => 95,  459 => 91,  438 => 48,  351 => 49,  347 => 16,  402 => 117,  268 => 90,  430 => 87,  411 => 140,  379 => 145,  322 => 115,  315 => 55,  289 => 155,  284 => 49,  255 => 127,  234 => 31,  1133 => 444,  1124 => 469,  1121 => 56,  1116 => 549,  1113 => 467,  1108 => 376,  1103 => 368,  1098 => 26,  1081 => 415,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 419,  1042 => 483,  1039 => 345,  1025 => 335,  1021 => 391,  1015 => 412,  1008 => 461,  996 => 406,  989 => 454,  985 => 315,  981 => 257,  977 => 321,  970 => 320,  966 => 375,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 148,  919 => 83,  917 => 369,  908 => 411,  905 => 363,  896 => 358,  891 => 360,  877 => 334,  862 => 348,  857 => 273,  837 => 239,  832 => 250,  827 => 68,  821 => 66,  803 => 179,  778 => 389,  769 => 58,  765 => 297,  753 => 54,  746 => 52,  743 => 268,  735 => 75,  730 => 330,  720 => 363,  717 => 362,  712 => 251,  691 => 233,  678 => 149,  654 => 199,  587 => 14,  576 => 158,  539 => 116,  517 => 140,  471 => 18,  441 => 49,  437 => 239,  418 => 84,  386 => 106,  373 => 133,  304 => 53,  270 => 105,  265 => 36,  229 => 64,  477 => 89,  455 => 36,  448 => 41,  429 => 165,  407 => 228,  399 => 116,  389 => 145,  375 => 130,  358 => 109,  349 => 255,  335 => 106,  327 => 155,  298 => 51,  280 => 152,  249 => 46,  194 => 27,  142 => 35,  344 => 119,  318 => 101,  306 => 115,  295 => 106,  357 => 51,  300 => 113,  286 => 73,  276 => 93,  269 => 127,  254 => 74,  128 => 21,  237 => 88,  165 => 53,  122 => 26,  798 => 281,  770 => 179,  759 => 278,  748 => 212,  731 => 260,  721 => 258,  718 => 301,  708 => 250,  696 => 287,  617 => 188,  590 => 170,  553 => 66,  550 => 157,  540 => 289,  533 => 114,  500 => 102,  493 => 57,  489 => 179,  482 => 100,  467 => 258,  464 => 202,  458 => 255,  452 => 154,  449 => 88,  415 => 83,  382 => 142,  372 => 75,  361 => 129,  356 => 24,  339 => 126,  302 => 97,  285 => 105,  258 => 136,  123 => 42,  108 => 4,  424 => 86,  394 => 77,  380 => 151,  338 => 251,  319 => 119,  316 => 117,  312 => 99,  290 => 56,  267 => 96,  206 => 45,  110 => 23,  240 => 83,  224 => 66,  219 => 74,  217 => 100,  202 => 44,  186 => 70,  170 => 54,  100 => 34,  67 => 17,  14 => 1,  1096 => 425,  1090 => 421,  1088 => 435,  1085 => 456,  1066 => 409,  1034 => 282,  1031 => 281,  1018 => 416,  1013 => 358,  1007 => 274,  1002 => 403,  993 => 266,  986 => 264,  982 => 394,  976 => 399,  971 => 376,  964 => 255,  949 => 388,  946 => 402,  940 => 384,  937 => 374,  928 => 452,  926 => 413,  915 => 299,  912 => 82,  903 => 231,  898 => 440,  892 => 319,  889 => 337,  887 => 281,  884 => 79,  876 => 75,  874 => 215,  871 => 331,  863 => 352,  861 => 274,  858 => 347,  850 => 378,  843 => 206,  840 => 406,  815 => 64,  812 => 297,  808 => 199,  804 => 395,  799 => 295,  791 => 60,  785 => 328,  775 => 82,  771 => 284,  754 => 340,  728 => 317,  726 => 72,  723 => 71,  715 => 105,  711 => 152,  709 => 222,  706 => 302,  698 => 208,  694 => 199,  692 => 155,  689 => 302,  681 => 242,  677 => 232,  675 => 289,  663 => 62,  661 => 200,  650 => 223,  646 => 112,  629 => 181,  627 => 264,  625 => 209,  622 => 18,  598 => 76,  592 => 75,  586 => 199,  575 => 232,  566 => 242,  556 => 67,  554 => 240,  541 => 176,  536 => 241,  515 => 108,  511 => 166,  509 => 24,  488 => 158,  486 => 220,  483 => 175,  465 => 93,  463 => 51,  450 => 244,  432 => 129,  419 => 232,  371 => 127,  362 => 152,  353 => 257,  337 => 141,  333 => 156,  309 => 54,  303 => 114,  299 => 166,  291 => 41,  272 => 92,  261 => 47,  253 => 34,  239 => 82,  235 => 44,  213 => 100,  200 => 28,  198 => 50,  159 => 17,  149 => 35,  146 => 36,  131 => 10,  116 => 6,  79 => 13,  74 => 10,  71 => 9,  836 => 262,  817 => 398,  814 => 319,  811 => 235,  805 => 313,  787 => 59,  779 => 169,  776 => 222,  773 => 347,  761 => 296,  751 => 272,  747 => 265,  742 => 336,  739 => 333,  736 => 265,  724 => 259,  705 => 69,  702 => 601,  688 => 232,  680 => 278,  667 => 232,  662 => 27,  656 => 418,  649 => 285,  644 => 220,  641 => 20,  624 => 109,  613 => 264,  607 => 273,  597 => 253,  591 => 49,  584 => 3,  579 => 1,  563 => 40,  559 => 68,  551 => 243,  547 => 179,  537 => 115,  524 => 112,  512 => 174,  507 => 165,  504 => 164,  498 => 213,  485 => 166,  480 => 50,  472 => 96,  466 => 38,  460 => 152,  447 => 153,  442 => 40,  434 => 47,  428 => 127,  422 => 176,  404 => 80,  368 => 132,  364 => 126,  340 => 170,  334 => 125,  330 => 59,  325 => 45,  292 => 50,  287 => 39,  282 => 119,  279 => 109,  273 => 129,  266 => 126,  256 => 50,  252 => 86,  228 => 30,  218 => 44,  201 => 70,  64 => 34,  51 => 7,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 847,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 823,  2037 => 789,  2031 => 788,  2028 => 818,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 811,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 779,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 769,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 744,  1801 => 695,  1798 => 694,  1795 => 742,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 708,  1730 => 705,  1720 => 688,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 672,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 659,  1651 => 656,  1641 => 631,  1637 => 667,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 651,  1592 => 614,  1587 => 613,  1584 => 635,  1581 => 731,  1576 => 608,  1574 => 633,  1567 => 603,  1563 => 630,  1559 => 601,  1552 => 628,  1548 => 597,  1542 => 593,  1529 => 605,  1525 => 614,  1516 => 708,  1512 => 583,  1503 => 599,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 569,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 537,  1380 => 539,  1376 => 523,  1371 => 645,  1367 => 527,  1364 => 518,  1361 => 523,  1355 => 543,  1342 => 624,  1337 => 510,  1328 => 516,  1324 => 511,  1317 => 511,  1313 => 499,  1310 => 498,  1304 => 504,  1291 => 502,  1286 => 497,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 487,  1262 => 490,  1257 => 489,  1254 => 482,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 498,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 472,  1196 => 464,  1192 => 490,  1188 => 456,  1184 => 485,  1179 => 462,  1176 => 461,  1172 => 452,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 561,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 442,  1119 => 468,  1115 => 424,  1111 => 377,  1107 => 422,  1102 => 439,  1099 => 438,  1095 => 419,  1091 => 418,  1082 => 455,  1079 => 524,  1076 => 359,  1070 => 431,  1057 => 491,  1052 => 406,  1045 => 484,  1040 => 414,  1036 => 283,  1032 => 360,  1028 => 337,  1023 => 393,  1020 => 392,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 352,  992 => 821,  979 => 400,  974 => 256,  967 => 391,  962 => 337,  958 => 336,  954 => 334,  950 => 153,  945 => 376,  942 => 460,  938 => 330,  934 => 356,  927 => 147,  923 => 387,  920 => 412,  910 => 353,  901 => 340,  897 => 339,  890 => 343,  886 => 50,  883 => 356,  868 => 268,  856 => 323,  853 => 319,  849 => 308,  845 => 69,  841 => 341,  835 => 337,  830 => 249,  826 => 237,  822 => 354,  818 => 65,  813 => 183,  810 => 290,  806 => 180,  802 => 198,  795 => 314,  792 => 239,  789 => 83,  784 => 286,  782 => 187,  777 => 291,  772 => 289,  768 => 81,  763 => 327,  760 => 319,  756 => 214,  752 => 266,  745 => 314,  741 => 262,  738 => 379,  732 => 171,  719 => 279,  714 => 300,  710 => 299,  704 => 267,  699 => 67,  695 => 66,  690 => 285,  687 => 210,  683 => 346,  679 => 298,  672 => 193,  668 => 201,  665 => 229,  658 => 26,  645 => 225,  640 => 224,  634 => 218,  628 => 214,  623 => 107,  619 => 78,  611 => 54,  606 => 263,  603 => 196,  599 => 195,  595 => 193,  583 => 263,  580 => 45,  573 => 157,  560 => 75,  543 => 147,  538 => 69,  534 => 175,  530 => 168,  526 => 229,  521 => 287,  518 => 109,  514 => 221,  510 => 105,  503 => 59,  496 => 58,  490 => 56,  484 => 19,  474 => 161,  470 => 156,  446 => 185,  440 => 146,  436 => 251,  431 => 141,  425 => 81,  416 => 120,  412 => 76,  408 => 157,  403 => 134,  400 => 152,  396 => 28,  392 => 139,  385 => 224,  381 => 133,  367 => 111,  363 => 72,  359 => 200,  355 => 108,  350 => 120,  346 => 20,  343 => 196,  328 => 247,  324 => 120,  313 => 116,  307 => 151,  301 => 111,  288 => 102,  283 => 111,  271 => 37,  257 => 142,  251 => 78,  238 => 111,  233 => 160,  195 => 67,  191 => 26,  187 => 25,  183 => 38,  130 => 30,  88 => 17,  76 => 58,  115 => 32,  95 => 17,  655 => 224,  651 => 24,  648 => 23,  637 => 219,  633 => 56,  621 => 462,  618 => 179,  615 => 205,  604 => 52,  600 => 254,  588 => 48,  585 => 295,  582 => 160,  571 => 131,  567 => 193,  555 => 37,  552 => 180,  549 => 224,  544 => 230,  542 => 155,  535 => 64,  531 => 174,  519 => 201,  516 => 162,  513 => 228,  508 => 230,  506 => 160,  499 => 20,  495 => 181,  491 => 94,  481 => 161,  478 => 98,  475 => 97,  469 => 53,  456 => 204,  451 => 149,  443 => 194,  439 => 144,  427 => 143,  423 => 141,  420 => 140,  409 => 118,  405 => 30,  401 => 164,  391 => 134,  387 => 132,  384 => 131,  378 => 76,  365 => 202,  360 => 128,  348 => 48,  336 => 132,  332 => 46,  329 => 105,  323 => 65,  310 => 180,  305 => 231,  277 => 151,  274 => 87,  263 => 97,  259 => 35,  247 => 117,  244 => 89,  241 => 69,  222 => 74,  210 => 64,  207 => 40,  204 => 29,  184 => 24,  181 => 46,  167 => 9,  157 => 49,  96 => 27,  421 => 85,  417 => 250,  414 => 230,  406 => 130,  398 => 165,  393 => 132,  390 => 153,  376 => 29,  369 => 74,  366 => 73,  352 => 69,  345 => 67,  342 => 66,  331 => 138,  326 => 87,  320 => 44,  317 => 43,  314 => 63,  311 => 62,  308 => 61,  297 => 58,  293 => 114,  281 => 146,  278 => 100,  275 => 98,  264 => 48,  260 => 107,  248 => 73,  245 => 72,  242 => 96,  231 => 59,  227 => 46,  215 => 43,  212 => 42,  209 => 72,  197 => 33,  177 => 37,  171 => 35,  161 => 32,  132 => 22,  121 => 7,  105 => 14,  99 => 2,  81 => 1,  77 => 10,  180 => 58,  176 => 39,  156 => 29,  143 => 13,  139 => 113,  118 => 33,  189 => 58,  185 => 47,  173 => 44,  166 => 19,  152 => 27,  174 => 36,  164 => 59,  154 => 32,  150 => 15,  137 => 22,  133 => 63,  127 => 9,  107 => 22,  102 => 13,  83 => 21,  78 => 19,  53 => 17,  23 => 1,  42 => 3,  138 => 12,  134 => 45,  109 => 16,  103 => 15,  97 => 13,  94 => 12,  84 => 25,  75 => 23,  69 => 8,  66 => 15,  54 => 8,  44 => 54,  230 => 78,  226 => 59,  203 => 71,  193 => 43,  188 => 42,  182 => 21,  178 => 34,  168 => 34,  163 => 40,  160 => 39,  155 => 16,  148 => 42,  145 => 31,  140 => 39,  136 => 53,  125 => 43,  120 => 29,  113 => 24,  101 => 20,  92 => 21,  89 => 16,  85 => 16,  73 => 9,  62 => 4,  59 => 6,  56 => 8,  41 => 12,  126 => 34,  119 => 26,  111 => 22,  106 => 18,  98 => 29,  93 => 13,  86 => 15,  70 => 37,  60 => 8,  28 => 36,  36 => 6,  114 => 28,  104 => 3,  91 => 15,  80 => 11,  63 => 10,  58 => 9,  40 => 13,  34 => 8,  45 => 2,  61 => 56,  55 => 55,  48 => 5,  39 => 7,  35 => 9,  31 => 8,  26 => 2,  21 => 2,  46 => 5,  29 => 3,  57 => 12,  50 => 3,  47 => 14,  38 => 5,  33 => 4,  49 => 10,  32 => 7,  246 => 163,  236 => 68,  232 => 70,  225 => 77,  221 => 45,  216 => 53,  214 => 105,  211 => 39,  208 => 34,  205 => 73,  199 => 39,  196 => 53,  190 => 37,  179 => 23,  175 => 22,  172 => 21,  169 => 20,  162 => 18,  158 => 33,  153 => 66,  151 => 30,  147 => 14,  144 => 41,  141 => 28,  135 => 11,  129 => 47,  124 => 8,  117 => 36,  112 => 5,  90 => 12,  87 => 16,  82 => 11,  72 => 16,  68 => 23,  65 => 7,  52 => 7,  43 => 6,  37 => 6,  30 => 4,  27 => 3,  25 => 2,  24 => 1,  22 => 20,  19 => 53,);
    }
}
