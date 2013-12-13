<?php

/* AgentBundle:Downloads:filter.html.twig */
class __TwigTemplate_92dd00236d1e070b67093142641f11c9 extends \Application\DeskPRO\Twig\Template
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
\t\tpageMeta.fragmentClass = 'DeskPRO.Agent.PageFragment.ListPane.DownloadList';
\t\tpageMeta.refreshUrl = '";
        // line 12
        if (isset($context["result_id"])) { $_result_id_ = $context["result_id"]; } else { $_result_id_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_downloads_list", array("cache_id" => $_result_id_)), "html", null, true);
        echo "';
\t\tpageMeta.url_fragment = '";
        // line 13
        if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->urlFragment("agent_downloads_list", array("category_id" => $this->getAttribute($_category_, "id"))), "html", null, true);
        echo "';
\t\tpageMeta.baseId = '";
        // line 14
        if (isset($context["tplvars"])) { $_tplvars_ = $context["tplvars"]; } else { $_tplvars_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_tplvars_, "baseId"), "html", null, true);
        echo "';
\t</script>

";
    }

    // line 23
    public function block_pane_header($context, array $blocks = array())
    {
        // line 24
        echo "\t<ul class=\"pane-tabs\" data-element-handler=\"DeskPRO.ElementHandler.SimpleTabs\" data-active-classname=\"active\">
\t\t<li class=\"tab active\" data-tab-for=\"#";
        // line 25
        if (isset($context["tplvars"])) { $_tplvars_ = $context["tplvars"]; } else { $_tplvars_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_tplvars_, "baseId"), "html", null, true);
        echo "_tab_main\"><i class=\"icon-dp-article\"></i> ";
        if (isset($context["total_results"])) { $_total_results_ = $context["total_results"]; } else { $_total_results_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.downloads_list_title", array("count" => ((array_key_exists("total_results", $context)) ? (_twig_default_filter($_total_results_, 0)) : (0))));
        echo "</li>

\t\t";
        // line 27
        if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
        if ($_category_) {
            // line 28
            echo "\t\t\t<li class=\"tab\" data-tab-for=\"#";
            if (isset($context["tplvars"])) { $_tplvars_ = $context["tplvars"]; } else { $_tplvars_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_tplvars_, "baseId"), "html", null, true);
            echo "_tab_cat\"><i class=\"icon-folder-close\"></i> <span>";
            if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_category_, "title"), "html", null, true);
            echo "</span></li>
\t\t";
        }
        // line 30
        echo "\t</ul>
";
    }

    // line 37
    public function block_pane_content($context, array $blocks = array())
    {
        // line 38
        echo "
";
        // line 42
        echo "
<div id=\"";
        // line 43
        if (isset($context["tplvars"])) { $_tplvars_ = $context["tplvars"]; } else { $_tplvars_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_tplvars_, "baseId"), "html", null, true);
        echo "_tab_main\">
\t";
        // line 44
        $this->env->loadTemplate("AgentBundle:Downloads:filter-display-options.html.twig")->display($context);
        // line 45
        echo "\t";
        $this->env->loadTemplate("AgentBundle:Downloads:filter-control-bar.html.twig")->display($context);
        // line 46
        echo "
\t<section class=\"downloads-simple-list list-listing no-check\">
\t\t";
        // line 48
        $this->env->loadTemplate("AgentBundle:Downloads:filter-page.html.twig")->display($context);
        // line 49
        echo "\t</section>

\t";
        // line 51
        if (isset($context["results"])) { $_results_ = $context["results"]; } else { $_results_ = null; }
        if ((!twig_length_filter($this->env, $_results_))) {
            // line 52
            echo "\t\t<section class=\"list-listing no-results\">
\t\t\t<p>";
            // line 53
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.there_are_no_results");
            echo "</p>
\t\t</section>
\t";
        }
        // line 56
        echo "
\t";
        // line 57
        if (isset($context["results"])) { $_results_ = $context["results"]; } else { $_results_ = null; }
        if (twig_length_filter($this->env, $_results_)) {
            // line 58
            echo "\t\t<footer class=\"results-nav\">
\t\t\t<div class=\"cursor\">
\t\t\t\t";
            // line 60
            ob_start();
            echo "<span class=\"results-showing-count\" id=\"";
            if (isset($context["tplvars"])) { $_tplvars_ = $context["tplvars"]; } else { $_tplvars_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_tplvars_, "baseId"), "html", null, true);
            echo "_showing_count\">";
            if (isset($context["cur_page"])) { $_cur_page_ = $context["cur_page"]; } else { $_cur_page_ = null; }
            echo twig_escape_filter($this->env, ((($_cur_page_ - 1) * 50) + 1), "html", null, true);
            echo " - ";
            if (isset($context["showing_to"])) { $_showing_to_ = $context["showing_to"]; } else { $_showing_to_ = null; }
            echo twig_escape_filter($this->env, $_showing_to_, "html", null, true);
            echo "</span>";
            $context["phrase_part1"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
            // line 61
            echo "\t\t\t\t";
            ob_start();
            echo "<span class=\"results-total-count\" id=\"";
            if (isset($context["tplvars"])) { $_tplvars_ = $context["tplvars"]; } else { $_tplvars_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_tplvars_, "baseId"), "html", null, true);
            echo "_total_count\">";
            if (isset($context["total_results"])) { $_total_results_ = $context["total_results"]; } else { $_total_results_ = null; }
            echo twig_escape_filter($this->env, $_total_results_, "html", null, true);
            echo "</span>";
            $context["phrase_part2"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
            // line 62
            echo "\t\t\t\t";
            if (isset($context["phrase_part1"])) { $_phrase_part1_ = $context["phrase_part1"]; } else { $_phrase_part1_ = null; }
            if (isset($context["phrase_part2"])) { $_phrase_part2_ = $context["phrase_part2"]; } else { $_phrase_part2_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.showing_results_x_of_y", array("display_count" => $_phrase_part1_, "size" => $_phrase_part2_), true);
            echo "
\t\t\t</div>
\t\t\t";
            // line 64
            if (isset($context["total_results"])) { $_total_results_ = $context["total_results"]; } else { $_total_results_ = null; }
            if (($_total_results_ > 50)) {
                // line 65
                echo "\t\t\t\t<ul class=\"pagenav ";
                if (isset($context["cur_page"])) { $_cur_page_ = $context["cur_page"]; } else { $_cur_page_ = null; }
                if (($_cur_page_ == 1)) {
                    echo "no-prev";
                }
                echo " ";
                if (isset($context["cur_page"])) { $_cur_page_ = $context["cur_page"]; } else { $_cur_page_ = null; }
                if (isset($context["num_pages"])) { $_num_pages_ = $context["num_pages"]; } else { $_num_pages_ = null; }
                if (($_cur_page_ == $_num_pages_)) {
                    echo "no-next";
                }
                echo "\">
\t\t\t\t\t";
                // line 66
                if (isset($context["cur_page"])) { $_cur_page_ = $context["cur_page"]; } else { $_cur_page_ = null; }
                if (($_cur_page_ > 1)) {
                    // line 67
                    echo "\t\t\t\t\t\t<li class=\"prev\" data-route=\"listpane:";
                    if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
                    if (isset($context["cur_page"])) { $_cur_page_ = $context["cur_page"]; } else { $_cur_page_ = null; }
                    echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_downloads_list", array("category_id" => $this->getAttribute($_category_, "id"), "p" => ($_cur_page_ - 1))), "html", null, true);
                    echo "\"></li>
\t\t\t\t\t";
                } else {
                    // line 69
                    echo "\t\t\t\t\t\t<li class=\"prev no-prev\"></li>
\t\t\t\t\t";
                }
                // line 71
                echo "
\t\t\t\t\t";
                // line 72
                if (isset($context["cur_page"])) { $_cur_page_ = $context["cur_page"]; } else { $_cur_page_ = null; }
                if (isset($context["num_pages"])) { $_num_pages_ = $context["num_pages"]; } else { $_num_pages_ = null; }
                if (($_cur_page_ < $_num_pages_)) {
                    // line 73
                    echo "\t\t\t\t\t\t<li class=\"next\" data-route=\"listpane:";
                    if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
                    if (isset($context["cur_page"])) { $_cur_page_ = $context["cur_page"]; } else { $_cur_page_ = null; }
                    echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_downloads_list", array("category_id" => $this->getAttribute($_category_, "id"), "p" => ($_cur_page_ + 1))), "html", null, true);
                    echo "\"></li>
\t\t\t\t\t";
                } else {
                    // line 75
                    echo "\t\t\t\t\t\t<li class=\"next no-next\"></li>
\t\t\t\t\t";
                }
                // line 77
                echo "\t\t\t\t</ul>
\t\t\t";
            }
            // line 79
            echo "\t\t</footer>
\t";
        }
        // line 81
        echo "</div>

";
        // line 86
        echo "
";
        // line 87
        if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
        if ($_category_) {
            // line 88
            echo "<div id=\"";
            if (isset($context["tplvars"])) { $_tplvars_ = $context["tplvars"]; } else { $_tplvars_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_tplvars_, "baseId"), "html", null, true);
            echo "_tab_cat\" style=\"display: none;\">

\t<section class=\"pane-section last\" id=\"";
            // line 90
            if (isset($context["tplvars"])) { $_tplvars_ = $context["tplvars"]; } else { $_tplvars_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_tplvars_, "baseId"), "html", null, true);
            echo "_del_cat\">
\t\t<header><h1>";
            // line 91
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.delete_category");
            echo "</h1></header>
\t\t<article style=\"padding: 10px;\">
\t\t\t<div class=\"dp-not-loading\">
\t\t\t\t<button class=\"dp-btn cat-del-trigger\" data-save-url=\"";
            // line 94
            if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_publish_cats_adddel", array("type" => "downloads", "category_id" => $this->getAttribute($_category_, "id"))), "html", null, true);
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.delete_category_btn");
            echo "</button>
\t\t\t</div>
\t\t\t<div class=\"dp-is-loading\">
\t\t\t\t<i class=\"spinner-flat\"></i>
\t\t\t</div>
\t\t</article>
\t</section>

\t<section class=\"pane-section\">
\t\t<header><h1>";
            // line 103
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.edit_category_btn");
            echo "</h1></header>
\t\t<article>
\t\t\t<div class=\"pane-form\">
\t\t\t\t<table class=\"pane-form-table\">
\t\t\t\t\t<tbody>
\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t<th width=\"100\">";
            // line 109
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.title");
            echo "</th>
\t\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"category[id]\" value=\"";
            // line 111
            if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_category_, "id"), "html", null, true);
            echo "\" />
\t\t\t\t\t\t\t\t<input type=\"text\" name=\"category[title]\" value=\"";
            // line 112
            if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_category_, "title"), "html", null, true);
            echo "\" />
\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t</tr>
\t\t\t\t\t\t<tr class=\"vtop\">
\t\t\t\t\t\t\t<th>";
            // line 116
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.permissions");
            echo "</th>
\t\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t\t<ul>
\t\t\t\t\t\t\t\t\t";
            // line 119
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getUsergroups", array(), "method"), "getUserUsergroups", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["ug"]) {
                // line 120
                echo "\t\t\t\t\t\t\t\t\t\t<li>
\t\t\t\t\t\t\t\t\t\t\t<label>
\t\t\t\t\t\t\t\t\t\t\t\t<input class=\"ug-check ug-";
                // line 122
                if (isset($context["ug"])) { $_ug_ = $context["ug"]; } else { $_ug_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_ug_, "id"), "html", null, true);
                echo " ";
                if (isset($context["ug"])) { $_ug_ = $context["ug"]; } else { $_ug_ = null; }
                if ($this->getAttribute($_ug_, "sys_name")) {
                    echo "ug-";
                    if (isset($context["ug"])) { $_ug_ = $context["ug"]; } else { $_ug_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_ug_, "sys_name"), "html", null, true);
                }
                echo "\" type=\"checkbox\" name=\"category[usergroups][]\" value=\"";
                if (isset($context["ug"])) { $_ug_ = $context["ug"]; } else { $_ug_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_ug_, "id"), "html", null, true);
                echo "\" ";
                if (isset($context["ug"])) { $_ug_ = $context["ug"]; } else { $_ug_ = null; }
                if (isset($context["cat_usergroups"])) { $_cat_usergroups_ = $context["cat_usergroups"]; } else { $_cat_usergroups_ = null; }
                if (twig_in_filter($this->getAttribute($_ug_, "id"), $_cat_usergroups_)) {
                    echo "checked=\"checked\"";
                }
                echo " />
\t\t\t\t\t\t\t\t\t\t\t\t";
                // line 123
                if (isset($context["ug"])) { $_ug_ = $context["ug"]; } else { $_ug_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_ug_, "title"), "html", null, true);
                echo "
\t\t\t\t\t\t\t\t\t\t\t</label>
\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['ug'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 127
            echo "\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t</tr>
\t\t\t\t\t</tbody>
\t\t\t\t</table>
\t\t\t</div>
\t\t</article>
\t</section>

\t<section class=\"pane-section last\">
\t\t<header><h1>";
            // line 137
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.reorder_category_title");
            echo "</h1></header>
\t\t<article>
\t\t\t<div style=\"padding: 10px;\" id=\"";
            // line 139
            if (isset($context["tplvars"])) { $_tplvars_ = $context["tplvars"]; } else { $_tplvars_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_tplvars_, "baseId"), "html", null, true);
            echo "_cattree\" data-treedata=\"";
            if (isset($context["cat_structure_data"])) { $_cat_structure_data_ = $context["cat_structure_data"]; } else { $_cat_structure_data_ = null; }
            echo twig_escape_filter($this->env, twig_jsonencode_filter($_cat_structure_data_), "html", null, true);
            echo "\"></div>
\t\t\t<input type=\"hidden\" id=\"";
            // line 140
            if (isset($context["tplvars"])) { $_tplvars_ = $context["tplvars"]; } else { $_tplvars_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_tplvars_, "baseId"), "html", null, true);
            echo "_cattree_struct\" value=\"\" />
\t\t</article>
\t</section>

\t<footer class=\"pane-footer\" id=\"";
            // line 144
            if (isset($context["tplvars"])) { $_tplvars_ = $context["tplvars"]; } else { $_tplvars_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_tplvars_, "baseId"), "html", null, true);
            echo "_catfoot\">
\t\t<div class=\"dp-not-loading\">
\t\t\t<button class=\"dp-btn cat-save-trigger\" data-save-url=\"";
            // line 146
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_publish_savecats", array("type" => "download")), "html", null, true);
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.save");
            echo "</button>
\t\t</div>
\t\t<div class=\"dp-is-loading\">
\t\t\t<i class=\"spinner-flat\"></i>
\t\t</div>
\t</footer>
</div>
";
        }
    }

    public function getTemplateName()
    {
        return "AgentBundle:Downloads:filter.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  842 => 337,  1038 => 364,  904 => 322,  882 => 318,  831 => 303,  860 => 314,  790 => 278,  733 => 210,  707 => 206,  744 => 79,  873 => 74,  824 => 67,  762 => 271,  713 => 248,  578 => 292,  2739 => 1153,  2735 => 1152,  2732 => 1151,  2725 => 1146,  2712 => 1144,  2707 => 1143,  2692 => 1131,  2688 => 1130,  2683 => 1127,  2680 => 1126,  2677 => 1125,  2671 => 1121,  2658 => 1119,  2653 => 1118,  2645 => 1113,  2641 => 1112,  2636 => 1109,  2632 => 1108,  2629 => 1107,  2626 => 1106,  2606 => 1089,  2602 => 1088,  2598 => 1087,  2594 => 1086,  2590 => 1085,  2586 => 1084,  2572 => 1073,  2568 => 1072,  2564 => 1071,  2560 => 1070,  2556 => 1069,  2552 => 1068,  2537 => 1056,  2533 => 1055,  2529 => 1054,  2525 => 1053,  2521 => 1052,  2503 => 1040,  2499 => 1039,  2495 => 1038,  2491 => 1037,  2487 => 1036,  2483 => 1035,  2469 => 1024,  2465 => 1023,  2461 => 1022,  2457 => 1021,  2453 => 1020,  2449 => 1019,  2426 => 999,  2422 => 998,  2418 => 997,  2414 => 996,  2410 => 995,  2406 => 994,  2392 => 983,  2388 => 982,  2384 => 981,  2380 => 980,  2376 => 979,  2372 => 978,  2358 => 967,  2354 => 966,  2350 => 965,  2346 => 964,  2342 => 963,  2338 => 962,  2324 => 951,  2320 => 950,  2316 => 949,  2312 => 948,  2308 => 947,  2304 => 946,  2295 => 939,  2282 => 937,  2277 => 936,  2269 => 931,  2265 => 930,  2260 => 927,  2256 => 926,  2253 => 925,  2230 => 901,  2220 => 899,  2216 => 898,  2211 => 896,  2208 => 895,  2198 => 893,  2194 => 892,  2189 => 890,  2184 => 889,  2177 => 885,  2173 => 884,  2164 => 877,  2151 => 875,  2146 => 874,  2143 => 873,  2141 => 864,  2134 => 860,  2130 => 859,  2122 => 853,  2120 => 852,  2113 => 848,  2105 => 846,  2096 => 839,  2083 => 837,  2078 => 836,  2073 => 834,  2067 => 830,  2062 => 828,  2058 => 827,  2054 => 826,  2050 => 825,  2045 => 824,  2038 => 822,  2034 => 821,  2022 => 814,  2009 => 812,  1999 => 809,  1993 => 805,  1988 => 803,  1984 => 802,  1980 => 801,  1976 => 800,  1971 => 799,  1968 => 798,  1964 => 797,  1960 => 796,  1954 => 793,  1946 => 788,  1942 => 787,  1938 => 786,  1932 => 782,  1927 => 780,  1919 => 778,  1915 => 777,  1911 => 776,  1906 => 775,  1903 => 774,  1899 => 773,  1895 => 772,  1886 => 768,  1883 => 764,  1871 => 759,  1866 => 756,  1863 => 755,  1842 => 752,  1825 => 749,  1818 => 748,  1815 => 747,  1800 => 743,  1793 => 741,  1772 => 729,  1761 => 721,  1757 => 720,  1751 => 717,  1740 => 709,  1719 => 697,  1715 => 696,  1686 => 673,  1676 => 669,  1665 => 661,  1661 => 660,  1645 => 651,  1638 => 649,  1630 => 645,  1625 => 642,  1622 => 641,  1598 => 637,  1577 => 634,  1558 => 629,  1549 => 627,  1546 => 626,  1535 => 618,  1531 => 617,  1514 => 606,  1510 => 605,  1504 => 602,  1493 => 594,  1483 => 590,  1464 => 580,  1458 => 577,  1443 => 568,  1437 => 565,  1426 => 557,  1405 => 545,  1401 => 544,  1395 => 541,  1390 => 538,  1381 => 533,  1373 => 531,  1348 => 521,  1343 => 520,  1335 => 515,  1321 => 510,  1318 => 509,  1299 => 503,  1294 => 502,  1282 => 496,  1269 => 491,  1260 => 483,  1240 => 478,  1221 => 476,  1216 => 475,  1210 => 474,  1206 => 473,  1193 => 467,  1189 => 466,  1155 => 450,  1150 => 447,  1022 => 411,  1006 => 404,  988 => 398,  969 => 392,  965 => 391,  921 => 370,  878 => 355,  866 => 349,  854 => 346,  819 => 293,  796 => 330,  1713 => 683,  1700 => 681,  1685 => 674,  1662 => 670,  1643 => 669,  1640 => 668,  1631 => 663,  1618 => 661,  1613 => 660,  1608 => 639,  1605 => 656,  1602 => 655,  1589 => 645,  1586 => 644,  1583 => 643,  1554 => 613,  1547 => 608,  1521 => 602,  1508 => 600,  1499 => 598,  1491 => 593,  1482 => 587,  1472 => 582,  1468 => 581,  1465 => 579,  1457 => 573,  1445 => 566,  1422 => 556,  1416 => 553,  1410 => 548,  1397 => 546,  1392 => 545,  1386 => 541,  1377 => 532,  1370 => 530,  1357 => 528,  1352 => 527,  1341 => 518,  1314 => 510,  1311 => 509,  1275 => 493,  1248 => 484,  1238 => 478,  1225 => 476,  1220 => 475,  1209 => 466,  1185 => 459,  1182 => 463,  1159 => 450,  1154 => 449,  1130 => 438,  1125 => 437,  1101 => 426,  1074 => 432,  1056 => 407,  1046 => 418,  1043 => 404,  1030 => 397,  1027 => 412,  947 => 361,  925 => 352,  913 => 324,  893 => 338,  881 => 335,  847 => 343,  829 => 336,  825 => 304,  1083 => 434,  995 => 383,  984 => 350,  963 => 319,  941 => 375,  851 => 271,  682 => 270,  1365 => 549,  1359 => 545,  1349 => 539,  1336 => 537,  1331 => 514,  1323 => 515,  1319 => 530,  1312 => 505,  1284 => 519,  1272 => 492,  1268 => 509,  1261 => 506,  1251 => 485,  1245 => 483,  1231 => 496,  1207 => 493,  1197 => 491,  1180 => 484,  1173 => 457,  1169 => 455,  1157 => 475,  1147 => 446,  1109 => 440,  1065 => 440,  1059 => 423,  1047 => 428,  1044 => 424,  1033 => 420,  1009 => 357,  991 => 351,  987 => 404,  973 => 395,  931 => 355,  924 => 371,  911 => 298,  906 => 81,  885 => 336,  872 => 317,  855 => 72,  749 => 53,  701 => 239,  594 => 109,  1163 => 454,  1143 => 440,  1087 => 420,  1077 => 433,  1051 => 430,  1037 => 480,  1010 => 405,  999 => 384,  932 => 326,  899 => 405,  895 => 404,  933 => 84,  914 => 133,  909 => 323,  833 => 238,  783 => 306,  755 => 320,  666 => 263,  453 => 187,  639 => 249,  568 => 254,  520 => 110,  657 => 260,  572 => 186,  609 => 17,  20 => 1,  659 => 207,  562 => 185,  548 => 165,  558 => 174,  479 => 157,  589 => 7,  457 => 145,  413 => 119,  953 => 430,  948 => 403,  935 => 394,  929 => 372,  916 => 382,  864 => 365,  844 => 338,  816 => 342,  807 => 234,  801 => 338,  774 => 274,  766 => 300,  737 => 49,  685 => 272,  664 => 231,  635 => 281,  593 => 231,  546 => 118,  532 => 68,  865 => 221,  852 => 241,  838 => 304,  820 => 201,  781 => 327,  764 => 320,  725 => 46,  632 => 245,  602 => 167,  565 => 130,  529 => 62,  505 => 267,  487 => 101,  473 => 87,  1853 => 842,  1849 => 753,  1839 => 751,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 733,  1778 => 732,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 693,  1705 => 781,  1688 => 675,  1681 => 769,  1677 => 768,  1667 => 671,  1656 => 762,  1648 => 655,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 606,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 593,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 540,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 522,  1289 => 520,  1276 => 604,  1263 => 601,  1244 => 480,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 463,  1181 => 561,  1171 => 557,  1158 => 550,  1114 => 428,  1104 => 465,  1072 => 512,  1062 => 7,  1024 => 395,  1014 => 406,  1000 => 328,  990 => 382,  980 => 474,  960 => 466,  918 => 348,  888 => 80,  834 => 307,  673 => 64,  636 => 185,  462 => 92,  454 => 253,  1144 => 542,  1139 => 399,  1131 => 399,  1127 => 397,  1110 => 396,  1092 => 459,  1089 => 390,  1086 => 520,  1084 => 419,  1063 => 387,  1060 => 386,  1055 => 422,  1050 => 373,  1035 => 372,  1019 => 330,  1003 => 263,  959 => 387,  900 => 366,  880 => 276,  870 => 350,  867 => 353,  859 => 362,  848 => 215,  839 => 376,  828 => 302,  823 => 374,  809 => 316,  800 => 315,  797 => 62,  794 => 280,  786 => 174,  740 => 78,  734 => 261,  703 => 286,  693 => 236,  630 => 278,  626 => 19,  614 => 275,  610 => 236,  581 => 293,  564 => 220,  525 => 61,  722 => 251,  697 => 282,  674 => 268,  671 => 266,  577 => 257,  569 => 222,  557 => 229,  502 => 99,  497 => 159,  445 => 85,  729 => 209,  684 => 281,  676 => 65,  669 => 254,  660 => 262,  647 => 198,  643 => 251,  601 => 306,  570 => 165,  522 => 165,  501 => 201,  296 => 110,  374 => 205,  631 => 265,  616 => 240,  608 => 235,  605 => 77,  596 => 102,  574 => 223,  561 => 175,  527 => 113,  433 => 166,  388 => 151,  426 => 175,  383 => 135,  461 => 155,  370 => 112,  395 => 224,  294 => 109,  223 => 91,  220 => 73,  492 => 180,  468 => 132,  444 => 168,  410 => 170,  397 => 78,  377 => 134,  262 => 88,  250 => 33,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 1051,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 680,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 638,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 572,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 526,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 494,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 538,  1126 => 443,  1106 => 528,  1100 => 897,  1026 => 418,  997 => 822,  983 => 816,  975 => 347,  956 => 318,  939 => 786,  902 => 362,  894 => 364,  879 => 76,  757 => 55,  727 => 316,  716 => 44,  670 => 278,  528 => 180,  476 => 253,  435 => 176,  354 => 50,  341 => 61,  192 => 49,  321 => 57,  243 => 47,  793 => 350,  780 => 311,  758 => 335,  700 => 212,  686 => 194,  652 => 185,  638 => 269,  620 => 174,  545 => 214,  523 => 152,  494 => 200,  459 => 91,  438 => 48,  351 => 49,  347 => 151,  402 => 117,  268 => 90,  430 => 87,  411 => 140,  379 => 164,  322 => 115,  315 => 55,  289 => 81,  284 => 49,  255 => 127,  234 => 77,  1133 => 444,  1124 => 469,  1121 => 56,  1116 => 549,  1113 => 467,  1108 => 376,  1103 => 368,  1098 => 26,  1081 => 415,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 419,  1042 => 483,  1039 => 345,  1025 => 335,  1021 => 391,  1015 => 412,  1008 => 461,  996 => 406,  989 => 454,  985 => 315,  981 => 257,  977 => 321,  970 => 320,  966 => 375,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 148,  919 => 83,  917 => 369,  908 => 411,  905 => 363,  896 => 358,  891 => 360,  877 => 334,  862 => 348,  857 => 273,  837 => 239,  832 => 333,  827 => 68,  821 => 66,  803 => 179,  778 => 305,  769 => 58,  765 => 297,  753 => 54,  746 => 52,  743 => 268,  735 => 75,  730 => 297,  720 => 363,  717 => 362,  712 => 251,  691 => 233,  678 => 149,  654 => 199,  587 => 229,  576 => 158,  539 => 116,  517 => 140,  471 => 18,  441 => 49,  437 => 239,  418 => 84,  386 => 106,  373 => 133,  304 => 112,  270 => 94,  265 => 36,  229 => 64,  477 => 188,  455 => 36,  448 => 41,  429 => 165,  407 => 228,  399 => 116,  389 => 145,  375 => 130,  358 => 109,  349 => 255,  335 => 106,  327 => 122,  298 => 103,  280 => 152,  249 => 87,  194 => 27,  142 => 35,  344 => 119,  318 => 119,  306 => 115,  295 => 106,  357 => 51,  300 => 113,  286 => 73,  276 => 93,  269 => 127,  254 => 74,  128 => 42,  237 => 64,  165 => 53,  122 => 26,  798 => 281,  770 => 302,  759 => 278,  748 => 298,  731 => 260,  721 => 293,  718 => 301,  708 => 250,  696 => 287,  617 => 188,  590 => 230,  553 => 66,  550 => 157,  540 => 212,  533 => 210,  500 => 102,  493 => 57,  489 => 179,  482 => 100,  467 => 258,  464 => 202,  458 => 255,  452 => 154,  449 => 177,  415 => 83,  382 => 142,  372 => 163,  361 => 129,  356 => 24,  339 => 126,  302 => 117,  285 => 103,  258 => 71,  123 => 48,  108 => 4,  424 => 86,  394 => 77,  380 => 151,  338 => 251,  319 => 119,  316 => 117,  312 => 116,  290 => 56,  267 => 90,  206 => 45,  110 => 34,  240 => 65,  224 => 66,  219 => 74,  217 => 100,  202 => 44,  186 => 65,  170 => 54,  100 => 37,  67 => 23,  14 => 1,  1096 => 425,  1090 => 421,  1088 => 435,  1085 => 456,  1066 => 409,  1034 => 282,  1031 => 281,  1018 => 416,  1013 => 358,  1007 => 274,  1002 => 403,  993 => 266,  986 => 264,  982 => 394,  976 => 399,  971 => 376,  964 => 255,  949 => 388,  946 => 402,  940 => 384,  937 => 374,  928 => 452,  926 => 413,  915 => 299,  912 => 82,  903 => 231,  898 => 440,  892 => 319,  889 => 337,  887 => 281,  884 => 79,  876 => 75,  874 => 215,  871 => 331,  863 => 352,  861 => 274,  858 => 347,  850 => 378,  843 => 206,  840 => 406,  815 => 64,  812 => 297,  808 => 199,  804 => 314,  799 => 312,  791 => 310,  785 => 328,  775 => 82,  771 => 284,  754 => 340,  728 => 317,  726 => 72,  723 => 71,  715 => 105,  711 => 152,  709 => 222,  706 => 302,  698 => 208,  694 => 199,  692 => 278,  689 => 302,  681 => 242,  677 => 232,  675 => 289,  663 => 62,  661 => 200,  650 => 223,  646 => 112,  629 => 181,  627 => 244,  625 => 209,  622 => 242,  598 => 232,  592 => 75,  586 => 199,  575 => 232,  566 => 242,  556 => 67,  554 => 240,  541 => 176,  536 => 241,  515 => 108,  511 => 166,  509 => 24,  488 => 196,  486 => 220,  483 => 175,  465 => 93,  463 => 181,  450 => 244,  432 => 129,  419 => 232,  371 => 137,  362 => 159,  353 => 153,  337 => 141,  333 => 156,  309 => 54,  303 => 84,  299 => 111,  291 => 101,  272 => 92,  261 => 72,  253 => 98,  239 => 82,  235 => 78,  213 => 100,  200 => 66,  198 => 51,  159 => 17,  149 => 49,  146 => 48,  131 => 27,  116 => 45,  79 => 17,  74 => 15,  71 => 14,  836 => 262,  817 => 322,  814 => 319,  811 => 235,  805 => 313,  787 => 59,  779 => 169,  776 => 222,  773 => 347,  761 => 296,  751 => 272,  747 => 265,  742 => 336,  739 => 333,  736 => 265,  724 => 259,  705 => 69,  702 => 601,  688 => 232,  680 => 269,  667 => 232,  662 => 27,  656 => 418,  649 => 285,  644 => 220,  641 => 20,  624 => 109,  613 => 264,  607 => 273,  597 => 253,  591 => 49,  584 => 3,  579 => 1,  563 => 40,  559 => 68,  551 => 243,  547 => 179,  537 => 115,  524 => 112,  512 => 174,  507 => 165,  504 => 164,  498 => 213,  485 => 194,  480 => 50,  472 => 96,  466 => 38,  460 => 152,  447 => 153,  442 => 40,  434 => 47,  428 => 127,  422 => 176,  404 => 80,  368 => 132,  364 => 126,  340 => 170,  334 => 125,  330 => 59,  325 => 45,  292 => 116,  287 => 99,  282 => 79,  279 => 109,  273 => 107,  266 => 104,  256 => 50,  252 => 88,  228 => 92,  218 => 72,  201 => 70,  64 => 17,  51 => 8,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 847,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 823,  2037 => 789,  2031 => 788,  2028 => 818,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 811,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 779,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 769,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 744,  1801 => 695,  1798 => 694,  1795 => 742,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 708,  1730 => 705,  1720 => 688,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 672,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 659,  1651 => 656,  1641 => 631,  1637 => 667,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 651,  1592 => 614,  1587 => 613,  1584 => 635,  1581 => 731,  1576 => 608,  1574 => 633,  1567 => 603,  1563 => 630,  1559 => 601,  1552 => 628,  1548 => 597,  1542 => 593,  1529 => 605,  1525 => 614,  1516 => 708,  1512 => 583,  1503 => 599,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 569,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 537,  1380 => 539,  1376 => 523,  1371 => 645,  1367 => 527,  1364 => 518,  1361 => 523,  1355 => 543,  1342 => 624,  1337 => 510,  1328 => 516,  1324 => 511,  1317 => 511,  1313 => 499,  1310 => 498,  1304 => 504,  1291 => 502,  1286 => 497,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 487,  1262 => 490,  1257 => 489,  1254 => 482,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 498,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 472,  1196 => 464,  1192 => 490,  1188 => 456,  1184 => 485,  1179 => 462,  1176 => 461,  1172 => 452,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 561,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 442,  1119 => 468,  1115 => 424,  1111 => 377,  1107 => 422,  1102 => 439,  1099 => 438,  1095 => 419,  1091 => 418,  1082 => 455,  1079 => 524,  1076 => 359,  1070 => 431,  1057 => 491,  1052 => 406,  1045 => 484,  1040 => 414,  1036 => 283,  1032 => 360,  1028 => 337,  1023 => 393,  1020 => 392,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 352,  992 => 821,  979 => 400,  974 => 256,  967 => 391,  962 => 337,  958 => 336,  954 => 334,  950 => 153,  945 => 376,  942 => 460,  938 => 330,  934 => 356,  927 => 147,  923 => 387,  920 => 412,  910 => 353,  901 => 340,  897 => 339,  890 => 343,  886 => 50,  883 => 356,  868 => 268,  856 => 323,  853 => 319,  849 => 308,  845 => 69,  841 => 341,  835 => 337,  830 => 249,  826 => 329,  822 => 326,  818 => 65,  813 => 183,  810 => 290,  806 => 180,  802 => 198,  795 => 311,  792 => 239,  789 => 83,  784 => 286,  782 => 187,  777 => 291,  772 => 289,  768 => 81,  763 => 327,  760 => 319,  756 => 214,  752 => 299,  745 => 314,  741 => 262,  738 => 379,  732 => 171,  719 => 279,  714 => 300,  710 => 299,  704 => 267,  699 => 67,  695 => 66,  690 => 285,  687 => 210,  683 => 346,  679 => 298,  672 => 193,  668 => 264,  665 => 229,  658 => 26,  645 => 225,  640 => 224,  634 => 218,  628 => 214,  623 => 107,  619 => 78,  611 => 54,  606 => 263,  603 => 234,  599 => 195,  595 => 193,  583 => 263,  580 => 45,  573 => 157,  560 => 75,  543 => 147,  538 => 69,  534 => 175,  530 => 168,  526 => 229,  521 => 287,  518 => 109,  514 => 202,  510 => 105,  503 => 59,  496 => 58,  490 => 56,  484 => 19,  474 => 161,  470 => 156,  446 => 185,  440 => 146,  436 => 251,  431 => 141,  425 => 81,  416 => 173,  412 => 76,  408 => 157,  403 => 134,  400 => 152,  396 => 28,  392 => 144,  385 => 224,  381 => 133,  367 => 111,  363 => 72,  359 => 127,  355 => 108,  350 => 120,  346 => 20,  343 => 196,  328 => 247,  324 => 120,  313 => 116,  307 => 151,  301 => 111,  288 => 115,  283 => 111,  271 => 75,  257 => 142,  251 => 83,  238 => 79,  233 => 93,  195 => 65,  191 => 64,  187 => 25,  183 => 64,  130 => 36,  88 => 21,  76 => 22,  115 => 26,  95 => 30,  655 => 224,  651 => 24,  648 => 253,  637 => 219,  633 => 56,  621 => 462,  618 => 179,  615 => 205,  604 => 52,  600 => 233,  588 => 48,  585 => 295,  582 => 160,  571 => 131,  567 => 193,  555 => 37,  552 => 180,  549 => 224,  544 => 230,  542 => 155,  535 => 64,  531 => 174,  519 => 201,  516 => 162,  513 => 228,  508 => 230,  506 => 160,  499 => 20,  495 => 181,  491 => 94,  481 => 161,  478 => 98,  475 => 97,  469 => 182,  456 => 204,  451 => 149,  443 => 194,  439 => 144,  427 => 143,  423 => 141,  420 => 140,  409 => 118,  405 => 169,  401 => 164,  391 => 134,  387 => 132,  384 => 140,  378 => 76,  365 => 202,  360 => 128,  348 => 123,  336 => 132,  332 => 46,  329 => 137,  323 => 120,  310 => 180,  305 => 231,  277 => 151,  274 => 87,  263 => 97,  259 => 90,  247 => 67,  244 => 66,  241 => 80,  222 => 73,  210 => 70,  207 => 69,  204 => 79,  184 => 24,  181 => 60,  167 => 55,  157 => 49,  96 => 28,  421 => 174,  417 => 250,  414 => 230,  406 => 130,  398 => 146,  393 => 132,  390 => 153,  376 => 139,  369 => 74,  366 => 160,  352 => 69,  345 => 67,  342 => 66,  331 => 138,  326 => 87,  320 => 131,  317 => 43,  314 => 63,  311 => 62,  308 => 61,  297 => 58,  293 => 114,  281 => 95,  278 => 78,  275 => 94,  264 => 91,  260 => 107,  248 => 73,  245 => 72,  242 => 81,  231 => 59,  227 => 46,  215 => 71,  212 => 52,  209 => 72,  197 => 33,  177 => 59,  171 => 56,  161 => 60,  132 => 52,  121 => 44,  105 => 38,  99 => 22,  81 => 24,  77 => 26,  180 => 47,  176 => 39,  156 => 59,  143 => 13,  139 => 45,  118 => 23,  189 => 48,  185 => 47,  173 => 44,  166 => 19,  152 => 58,  174 => 58,  164 => 61,  154 => 52,  150 => 35,  137 => 30,  133 => 39,  127 => 35,  107 => 33,  102 => 18,  83 => 19,  78 => 29,  53 => 13,  23 => 3,  42 => 6,  138 => 22,  134 => 45,  109 => 43,  103 => 38,  97 => 24,  94 => 23,  84 => 24,  75 => 23,  69 => 24,  66 => 19,  54 => 17,  44 => 10,  230 => 75,  226 => 60,  203 => 67,  193 => 43,  188 => 63,  182 => 21,  178 => 34,  168 => 64,  163 => 54,  160 => 53,  155 => 16,  148 => 42,  145 => 54,  140 => 53,  136 => 39,  125 => 49,  120 => 20,  113 => 30,  101 => 37,  92 => 27,  89 => 32,  85 => 28,  73 => 25,  62 => 15,  59 => 14,  56 => 16,  41 => 9,  126 => 45,  119 => 46,  111 => 19,  106 => 42,  98 => 33,  93 => 17,  86 => 21,  70 => 24,  60 => 15,  28 => 5,  36 => 11,  114 => 44,  104 => 27,  91 => 22,  80 => 11,  63 => 15,  58 => 14,  40 => 13,  34 => 3,  45 => 8,  61 => 10,  55 => 12,  48 => 12,  39 => 5,  35 => 4,  31 => 2,  26 => 3,  21 => 2,  46 => 12,  29 => 2,  57 => 17,  50 => 14,  47 => 7,  38 => 9,  33 => 4,  49 => 14,  32 => 3,  246 => 86,  236 => 68,  232 => 70,  225 => 77,  221 => 45,  216 => 53,  214 => 105,  211 => 69,  208 => 34,  205 => 73,  199 => 39,  196 => 53,  190 => 37,  179 => 23,  175 => 62,  172 => 65,  169 => 20,  162 => 46,  158 => 45,  153 => 50,  151 => 60,  147 => 58,  144 => 57,  141 => 56,  135 => 53,  129 => 51,  124 => 36,  117 => 43,  112 => 5,  90 => 20,  87 => 23,  82 => 27,  72 => 20,  68 => 17,  65 => 23,  52 => 15,  43 => 6,  37 => 10,  30 => 3,  27 => 7,  25 => 2,  24 => 4,  22 => 2,  19 => 1,);
    }
}
