<?php

/* AgentBundle:PeopleSearch:window-section.html.twig */
class __TwigTemplate_ff6a45ce0fb84c0bda8eabf7904fc272 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("AgentBundle::source-pane-layout.html.twig");

        $this->blocks = array(
            'pane_main_tab' => array($this, 'block_pane_main_tab'),
            'pane_content' => array($this, 'block_pane_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "AgentBundle::source-pane-layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 2
        $context["flagnamer"] = $this->env->loadTemplate("AgentBundle:Common:macro-flagname.html.twig");
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_pane_main_tab($context, array $blocks = array())
    {
        // line 4
        echo "\t<li class=\"tab active\" data-tab-id=\"pane-content-main\"><i class=\"icon-dp-person\"></i> ";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.chrome.nav_people");
        echo "</li>
";
    }

    // line 6
    public function block_pane_content($context, array $blocks = array())
    {
        // line 7
        echo "
\t<div class=\"pane-content pane-content-main\">

\t\t<section class=\"pane-section\" id=\"people_pending_section\" ";
        // line 10
        if (isset($context["validating_count_agent"])) { $_validating_count_agent_ = $context["validating_count_agent"]; } else { $_validating_count_agent_ = null; }
        if (isset($context["validating_count"])) { $_validating_count_ = $context["validating_count"]; } else { $_validating_count_ = null; }
        if (((!$_validating_count_agent_) && (!$_validating_count_))) {
            echo "style=\"display: none;\"";
        }
        echo ">
\t\t\t<header>
\t\t\t\t<h1>";
        // line 12
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.pending");
        echo "</h1>
\t\t\t</header>
\t\t\t<article>
\t\t\t\t<ul class=\"nav-list\" id=\"people_outline_people_list\">
\t\t\t\t\t<li class=\"is-nav-item\" id=\"people_nav_awaiting_agent_validation\">
\t\t\t\t\t\t<div class=\"item\" data-route=\"listpane:";
        // line 17
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_people_validate_list"), "html", null, true);
        echo "\">
\t\t\t\t\t\t\t<h3>";
        // line 18
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.awaiting_agent_validation");
        echo "</h3>
\t\t\t\t\t\t\t<em class=\"counter list-counter\">";
        // line 19
        if (isset($context["validating_count_agent"])) { $_validating_count_agent_ = $context["validating_count_agent"]; } else { $_validating_count_agent_ = null; }
        echo twig_escape_filter($this->env, ((array_key_exists("validating_count_agent", $context)) ? (_twig_default_filter($_validating_count_agent_, 0)) : (0)), "html", null, true);
        echo "</em>
\t\t\t\t\t\t</div>
\t\t\t\t\t</li>

\t\t\t\t\t<li class=\"is-nav-item\" id=\"people_nav_awaiting_validation\">
\t\t\t\t\t\t<div class=\"item\" data-route=\"listpane:";
        // line 24
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_people_validate_list", array("email_validating" => 1)), "html", null, true);
        echo "\">
\t\t\t\t\t\t\t<h3>";
        // line 25
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.awaiting_email_validation");
        echo "</h3>
\t\t\t\t\t\t\t<em class=\"counter list-counter\">";
        // line 26
        if (isset($context["validating_count"])) { $_validating_count_ = $context["validating_count"]; } else { $_validating_count_ = null; }
        echo twig_escape_filter($this->env, ((array_key_exists("validating_count", $context)) ? (_twig_default_filter($_validating_count_, 0)) : (0)), "html", null, true);
        echo "</em>
\t\t\t\t\t\t</div>
\t\t\t\t\t</li>
\t\t\t\t</ul>
\t\t\t</article>
\t\t</section>

\t\t";
        // line 34
        echo "\t\t";
        // line 35
        echo "\t\t";
        // line 36
        echo "
\t\t<section class=\"pane-section\">
\t\t\t<header>
\t\t\t\t<h1>";
        // line 39
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.people");
        echo "</h1>
\t\t\t</header>
\t\t\t<article>
\t\t\t\t<ul class=\"nav-list\" id=\"people_outline_people_list\">
\t\t\t\t\t<li class=\"people nav-people is-nav-item\" id=\"people_nav_all\">
\t\t\t\t\t\t<div class=\"item\" data-route=\"listpane:";
        // line 44
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_peoplesearch_customfilter"), "html", null, true);
        echo "\">
\t\t\t\t\t\t\t<h3>";
        // line 45
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.registered");
        echo "</h3>
\t\t\t\t\t\t\t<em class=\"counter list-counter\">";
        // line 46
        if (isset($context["people_count"])) { $_people_count_ = $context["people_count"]; } else { $_people_count_ = null; }
        if (($_people_count_ > 10000)) {
            echo "10000+";
        } else {
            if (isset($context["people_count"])) { $_people_count_ = $context["people_count"]; } else { $_people_count_ = null; }
            echo twig_escape_filter($this->env, $_people_count_, "html", null, true);
        }
        echo "</em>
\t\t\t\t\t\t</div>
\t\t\t\t\t</li>

\t\t\t\t\t";
        // line 50
        if (isset($context["usergroup_names"])) { $_usergroup_names_ = $context["usergroup_names"]; } else { $_usergroup_names_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_usergroup_names_);
        foreach ($context['_seq'] as $context["id"] => $context["title"]) {
            // line 51
            echo "\t\t\t\t\t\t<li
\t\t\t\t\t\t\tclass=\"usergroup-";
            // line 52
            if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
            echo twig_escape_filter($this->env, $_id_, "html", null, true);
            echo " nav-usergroup-";
            if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
            echo twig_escape_filter($this->env, $_id_, "html", null, true);
            echo " is-nav-item\"
\t\t\t\t\t\t\tid=\"people_nav_ug_";
            // line 53
            if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
            echo twig_escape_filter($this->env, $_id_, "html", null, true);
            echo "\"
\t\t\t\t\t\t\tdata-usergroup-id=\"";
            // line 54
            if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
            echo twig_escape_filter($this->env, $_id_, "html", null, true);
            echo "\"
\t\t\t\t\t\t\t";
            // line 55
            if (isset($context["usergroup_counts"])) { $_usergroup_counts_ = $context["usergroup_counts"]; } else { $_usergroup_counts_ = null; }
            if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
            if ((!$this->getAttribute($_usergroup_counts_, $_id_, array(), "array"))) {
                echo "style=\"display: none\"";
            }
            // line 56
            echo "\t\t\t\t\t\t>
\t\t\t\t\t\t\t<div class=\"item\" data-route=\"listpane:";
            // line 57
            if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_peoplesearch_usergroup", array("id" => $_id_)), "html", null, true);
            echo "\">
\t\t\t\t\t\t\t\t<h3>";
            // line 58
            if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
            echo twig_escape_filter($this->env, $_title_, "html", null, true);
            echo "</h3>
\t\t\t\t\t\t\t\t<em class=\"counter list-counter\">";
            // line 59
            if (isset($context["usergroup_counts"])) { $_usergroup_counts_ = $context["usergroup_counts"]; } else { $_usergroup_counts_ = null; }
            if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
            if (($this->getAttribute($_usergroup_counts_, $_id_, array(), "array") > 10000)) {
                echo "10000+";
            } else {
                if (isset($context["usergroup_counts"])) { $_usergroup_counts_ = $context["usergroup_counts"]; } else { $_usergroup_counts_ = null; }
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                echo twig_escape_filter($this->env, (($this->getAttribute($_usergroup_counts_, $_id_, array(), "array", true, true)) ? (_twig_default_filter($this->getAttribute($_usergroup_counts_, $_id_, array(), "array"), 0)) : (0)), "html", null, true);
            }
            echo "</em>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</li>
\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['id'], $context['title'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 63
        echo "\t\t\t\t</ul>
\t\t\t</article>
\t\t</section>

\t\t<section class=\"pane-section with-tabs\">
\t\t\t<nav class=\"pane-section-tabs\">
\t\t\t\t<ul>
\t\t\t\t\t<li class=\"on\"><span>";
        // line 70
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.labels");
        echo "</span></li>
\t\t\t\t\t<li class=\"invis-last\"></li>
\t\t\t\t</ul>
\t\t\t</nav>

\t\t\t<nav id=\"people_outline_tabstrip\" class=\"pill-two-switcher\" ";
        // line 75
        if (isset($context["people_tag_cloud"])) { $_people_tag_cloud_ = $context["people_tag_cloud"]; } else { $_people_tag_cloud_ = null; }
        if ((!twig_length_filter($this->env, $_people_tag_cloud_))) {
            echo "style=\"display: none\"";
        }
        echo " data-element-handler=\"DeskPRO.ElementHandler.SimpleTabs\">
\t\t\t\t<ul>
\t\t\t\t\t<li data-tab-for=\"#people_outline_tagcloud\" class=\"cloud on\">";
        // line 77
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.labels_cloud");
        echo "</li>
\t\t\t\t\t<li data-tab-for=\"#people_outline_taglist\" class=\"list\">";
        // line 78
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.labels_list");
        echo "</li>
\t\t\t\t</ul>
\t\t\t</nav>

\t\t\t<section class=\"deskpro-tab-item on labels-cloud\" id=\"people_outline_tagcloud\">
\t\t\t\t";
        // line 83
        $this->env->loadTemplate("AgentBundle:PeopleSearch:window-people-label-cloud.html.twig")->display($context);
        // line 84
        echo "\t\t\t</section>

\t\t\t<section class=\"deskpro-tab-item labels-list\" id=\"people_outline_taglist\">
\t\t\t\t";
        // line 87
        $this->env->loadTemplate("AgentBundle:PeopleSearch:window-people-label-list.html.twig")->display($context);
        // line 88
        echo "\t\t\t</section>
\t\t</section>

\t\t";
        // line 92
        echo "\t\t";
        // line 93
        echo "\t\t";
        // line 94
        echo "
\t\t<section class=\"pane-section border-top\">
\t\t\t<header>
\t\t\t\t<h1>";
        // line 97
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.organizations");
        echo "</h1>
\t\t\t</header>
\t\t\t<article>
\t\t\t\t<ul class=\"nav-list\">
\t\t\t\t\t<li class=\"is-nav-item\" id=\"people_outline_org_list\">
\t\t\t\t\t\t<div class=\"item\" data-route=\"listpane:";
        // line 102
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_orgsearch_customfilter"), "html", null, true);
        echo "\">
\t\t\t\t\t\t\t<h3>";
        // line 103
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.organizations");
        echo "</h3>
\t\t\t\t\t\t\t<em class=\"counter list-counter\">";
        // line 104
        if (isset($context["org_count"])) { $_org_count_ = $context["org_count"]; } else { $_org_count_ = null; }
        echo twig_escape_filter($this->env, ((array_key_exists("org_count", $context)) ? (_twig_default_filter($_org_count_, "0")) : ("0")), "html", null, true);
        echo "</em>
\t\t\t\t\t\t</div>
\t\t\t\t\t</li>
\t\t\t\t</ul>
\t\t\t</article>
\t\t</section>

\t\t<section class=\"pane-section with-tabs\">
\t\t\t<nav class=\"pane-section-tabs\">
\t\t\t\t<ul>
\t\t\t\t\t<li class=\"on\"><span>";
        // line 114
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.labels");
        echo "</span></li>
\t\t\t\t\t<li class=\"invis-last\"></li>
\t\t\t\t</ul>
\t\t\t</nav>

\t\t\t<nav id=\"people_outline_org_tabstrip\" class=\"pill-two-switcher\" ";
        // line 119
        if (isset($context["org_tag_cloud"])) { $_org_tag_cloud_ = $context["org_tag_cloud"]; } else { $_org_tag_cloud_ = null; }
        if ((!twig_length_filter($this->env, $_org_tag_cloud_))) {
            echo "style=\"display: none\"";
        }
        echo " data-element-handler=\"DeskPRO.ElementHandler.SimpleTabs\">
\t\t\t\t<ul>
\t\t\t\t\t<li data-tab-for=\"#people_outline_org_tagcloud\" id=\"people_outline_org_tagcloud_tr\" class=\"cloud on\">";
        // line 121
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.labels_cloud");
        echo "</li>
\t\t\t\t\t<li data-tab-for=\"#people_outline_org_taglist\" id=\"people_outline_org_taglist_tr\" class=\"list\">";
        // line 122
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.labels_list");
        echo "</li>
\t\t\t\t</ul>
\t\t\t</nav>
\t\t\t<section class=\"deskpro-tab-item on labels-cloud\" id=\"people_outline_org_tagcloud\">
\t\t\t\t";
        // line 126
        $this->env->loadTemplate("AgentBundle:PeopleSearch:window-org-label-cloud.html.twig")->display($context);
        // line 127
        echo "\t\t\t</section>

\t\t\t<section class=\"deskpro-tab-item labels-list\" id=\"people_outline_org_taglist\">
\t\t\t\t";
        // line 130
        $this->env->loadTemplate("AgentBundle:PeopleSearch:window-org-label-list.html.twig")->display($context);
        // line 131
        echo "\t\t\t</section>
\t\t</section>

\t\t";
        // line 135
        echo "\t\t";
        // line 136
        echo "\t\t";
        // line 137
        echo "
\t\t<section class=\"pane-section border-top last\">
\t\t\t<header>
\t\t\t\t<h1>";
        // line 140
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.agents");
        echo "</h1>
\t\t\t</header>
\t\t\t<article id=\"system_filters_wrap\">
\t\t\t\t<ul class=\"nav-list nav-list-small\">
\t\t\t\t\t<li class=\"is-nav-item\" id=\"people_outline_agent_list\">
\t\t\t\t\t\t<div class=\"item\"  data-route=\"listpane:";
        // line 145
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_peoplesearch_customfilter"), "html", null, true);
        echo "?terms[0][type]=agent_mode&amp;terms[0][op]=is&amp;terms[0][agent_mode]=1\">
\t\t\t\t\t\t\t<h3>";
        // line 146
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.agents");
        echo "</h3>
\t\t\t\t\t\t\t<em class=\"counter list-counter\">";
        // line 147
        if (isset($context["agent_count"])) { $_agent_count_ = $context["agent_count"]; } else { $_agent_count_ = null; }
        echo twig_escape_filter($this->env, ((array_key_exists("agent_count", $context)) ? (_twig_default_filter($_agent_count_, "0")) : ("0")), "html", null, true);
        echo "</em>
\t\t\t\t\t\t</div>
\t\t\t\t\t</li>
\t\t\t\t\t";
        // line 150
        if (isset($context["team_names"])) { $_team_names_ = $context["team_names"]; } else { $_team_names_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_team_names_);
        foreach ($context['_seq'] as $context["id"] => $context["title"]) {
            // line 151
            echo "\t\t\t\t\t\t<li class=\"people nav-people is-nav-item\">
\t\t\t\t\t\t\t<div class=\"item\" data-route=\"listpane:";
            // line 152
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_peoplesearch_customfilter"), "html", null, true);
            echo "?terms[0][type]=person_agent_team&amp;terms[0][op]=is&amp;terms[0][team_id]=";
            if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
            echo twig_escape_filter($this->env, $_id_, "html", null, true);
            echo "\">
\t\t\t\t\t\t\t\t<h3>";
            // line 153
            if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
            echo twig_escape_filter($this->env, $_title_, "html", null, true);
            echo "</h3>
\t\t\t\t\t\t\t\t<em class=\"counter list-counter\">";
            // line 154
            if (isset($context["team_counts"])) { $_team_counts_ = $context["team_counts"]; } else { $_team_counts_ = null; }
            if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($_team_counts_, $_id_, array(), "array", true, true)) ? (_twig_default_filter($this->getAttribute($_team_counts_, $_id_, array(), "array"), 0)) : (0)), "html", null, true);
            echo "</em>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</li>
\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['id'], $context['title'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 158
        echo "\t\t\t\t</ul>
\t\t\t</article>
\t\t</section>
\t</div>

\t<div class=\"pane-content pane-content-search\" style=\"display: none;\">
\t\t";
        // line 164
        $this->env->loadTemplate("AgentBundle:PeopleSearch:window-search.html.twig")->display($context);
        // line 165
        echo "\t</div>
";
    }

    public function getTemplateName()
    {
        return "AgentBundle:PeopleSearch:window-section.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1183 => 449,  1132 => 436,  1097 => 427,  957 => 394,  907 => 380,  875 => 298,  653 => 236,  1329 => 405,  1309 => 401,  1303 => 399,  1297 => 398,  1292 => 397,  1280 => 394,  1249 => 391,  1237 => 390,  1205 => 409,  1200 => 408,  1194 => 407,  1178 => 380,  1175 => 447,  1170 => 375,  1135 => 362,  1105 => 428,  1078 => 332,  1068 => 325,  1048 => 417,  961 => 294,  922 => 280,  750 => 221,  842 => 263,  1038 => 364,  904 => 322,  882 => 301,  831 => 303,  860 => 314,  790 => 284,  733 => 296,  707 => 206,  744 => 220,  873 => 74,  824 => 256,  762 => 250,  713 => 242,  578 => 158,  2739 => 1153,  2735 => 1152,  2732 => 1151,  2725 => 1146,  2712 => 1144,  2707 => 1143,  2692 => 1131,  2688 => 1130,  2683 => 1127,  2680 => 1126,  2677 => 1125,  2671 => 1121,  2658 => 1119,  2653 => 1118,  2645 => 1113,  2641 => 1112,  2636 => 1109,  2632 => 1108,  2629 => 1107,  2626 => 1106,  2606 => 1089,  2602 => 1088,  2598 => 1087,  2594 => 1086,  2590 => 1085,  2586 => 1084,  2572 => 1073,  2568 => 1072,  2564 => 1071,  2560 => 1070,  2556 => 1069,  2552 => 1068,  2537 => 1056,  2533 => 1055,  2529 => 1054,  2525 => 1053,  2521 => 1052,  2503 => 1040,  2499 => 1039,  2495 => 1038,  2491 => 1037,  2487 => 1036,  2483 => 1035,  2469 => 1024,  2465 => 1023,  2461 => 1022,  2457 => 1021,  2453 => 1020,  2449 => 1019,  2426 => 999,  2422 => 998,  2418 => 997,  2414 => 996,  2410 => 995,  2406 => 994,  2392 => 983,  2388 => 982,  2384 => 981,  2380 => 980,  2376 => 979,  2372 => 978,  2358 => 967,  2354 => 966,  2350 => 965,  2346 => 964,  2342 => 963,  2338 => 962,  2324 => 951,  2320 => 950,  2316 => 949,  2312 => 948,  2308 => 947,  2304 => 946,  2295 => 939,  2282 => 937,  2277 => 936,  2269 => 931,  2265 => 930,  2260 => 927,  2256 => 926,  2253 => 925,  2230 => 901,  2220 => 899,  2216 => 898,  2211 => 896,  2208 => 895,  2198 => 893,  2194 => 892,  2189 => 890,  2184 => 889,  2177 => 885,  2173 => 884,  2164 => 877,  2151 => 875,  2146 => 874,  2143 => 873,  2141 => 864,  2134 => 860,  2130 => 859,  2122 => 853,  2120 => 852,  2113 => 848,  2105 => 846,  2096 => 839,  2083 => 837,  2078 => 836,  2073 => 834,  2067 => 830,  2062 => 828,  2058 => 827,  2054 => 826,  2050 => 825,  2045 => 824,  2038 => 822,  2034 => 821,  2022 => 814,  2009 => 812,  1999 => 809,  1993 => 805,  1988 => 803,  1984 => 802,  1980 => 801,  1976 => 800,  1971 => 799,  1968 => 798,  1964 => 797,  1960 => 796,  1954 => 793,  1946 => 788,  1942 => 787,  1938 => 786,  1932 => 782,  1927 => 780,  1919 => 778,  1915 => 777,  1911 => 776,  1906 => 775,  1903 => 774,  1899 => 773,  1895 => 772,  1886 => 768,  1883 => 764,  1871 => 759,  1866 => 756,  1863 => 755,  1842 => 752,  1825 => 749,  1818 => 748,  1815 => 747,  1800 => 743,  1793 => 741,  1772 => 729,  1761 => 721,  1757 => 720,  1751 => 717,  1740 => 709,  1719 => 697,  1715 => 696,  1686 => 673,  1676 => 669,  1665 => 661,  1661 => 660,  1645 => 651,  1638 => 649,  1630 => 645,  1625 => 642,  1622 => 641,  1598 => 637,  1577 => 634,  1558 => 629,  1549 => 627,  1546 => 626,  1535 => 618,  1531 => 617,  1514 => 606,  1510 => 605,  1504 => 602,  1493 => 594,  1483 => 590,  1464 => 580,  1458 => 577,  1443 => 568,  1437 => 565,  1426 => 557,  1405 => 545,  1401 => 544,  1395 => 541,  1390 => 538,  1381 => 533,  1373 => 531,  1348 => 521,  1343 => 520,  1335 => 515,  1321 => 510,  1318 => 403,  1299 => 503,  1294 => 502,  1282 => 496,  1269 => 491,  1260 => 483,  1240 => 478,  1221 => 476,  1216 => 475,  1210 => 410,  1206 => 473,  1193 => 467,  1189 => 466,  1155 => 439,  1150 => 447,  1022 => 411,  1006 => 404,  988 => 398,  969 => 392,  965 => 391,  921 => 370,  878 => 275,  866 => 349,  854 => 346,  819 => 279,  796 => 286,  1713 => 683,  1700 => 681,  1685 => 674,  1662 => 670,  1643 => 669,  1640 => 668,  1631 => 663,  1618 => 661,  1613 => 660,  1608 => 639,  1605 => 656,  1602 => 655,  1589 => 645,  1586 => 644,  1583 => 643,  1554 => 613,  1547 => 608,  1521 => 602,  1508 => 600,  1499 => 598,  1491 => 593,  1482 => 587,  1472 => 582,  1468 => 581,  1465 => 579,  1457 => 573,  1445 => 566,  1422 => 556,  1416 => 553,  1410 => 548,  1397 => 546,  1392 => 545,  1386 => 541,  1377 => 532,  1370 => 530,  1357 => 528,  1352 => 527,  1341 => 518,  1314 => 510,  1311 => 509,  1275 => 493,  1248 => 484,  1238 => 478,  1225 => 476,  1220 => 412,  1209 => 466,  1185 => 385,  1182 => 463,  1159 => 367,  1154 => 449,  1130 => 438,  1125 => 437,  1101 => 426,  1074 => 432,  1056 => 418,  1046 => 418,  1043 => 404,  1030 => 397,  1027 => 412,  947 => 361,  925 => 352,  913 => 324,  893 => 338,  881 => 335,  847 => 343,  829 => 336,  825 => 259,  1083 => 434,  995 => 383,  984 => 350,  963 => 319,  941 => 324,  851 => 367,  682 => 270,  1365 => 549,  1359 => 545,  1349 => 539,  1336 => 537,  1331 => 514,  1323 => 515,  1319 => 530,  1312 => 402,  1284 => 519,  1272 => 393,  1268 => 509,  1261 => 506,  1251 => 485,  1245 => 483,  1231 => 496,  1207 => 493,  1197 => 491,  1180 => 484,  1173 => 457,  1169 => 455,  1157 => 475,  1147 => 438,  1109 => 440,  1065 => 440,  1059 => 423,  1047 => 428,  1044 => 424,  1033 => 420,  1009 => 357,  991 => 351,  987 => 404,  973 => 395,  931 => 355,  924 => 317,  911 => 298,  906 => 81,  885 => 276,  872 => 317,  855 => 72,  749 => 264,  701 => 239,  594 => 109,  1163 => 440,  1143 => 440,  1087 => 420,  1077 => 433,  1051 => 430,  1037 => 480,  1010 => 405,  999 => 407,  932 => 326,  899 => 306,  895 => 404,  933 => 387,  914 => 133,  909 => 323,  833 => 284,  783 => 306,  755 => 248,  666 => 263,  453 => 187,  639 => 269,  568 => 199,  520 => 110,  657 => 216,  572 => 201,  609 => 216,  20 => 1,  659 => 207,  562 => 185,  548 => 185,  558 => 197,  479 => 145,  589 => 211,  457 => 153,  413 => 140,  953 => 430,  948 => 290,  935 => 394,  929 => 319,  916 => 382,  864 => 365,  844 => 338,  816 => 342,  807 => 341,  801 => 268,  774 => 257,  766 => 229,  737 => 297,  685 => 186,  664 => 225,  635 => 281,  593 => 209,  546 => 227,  532 => 223,  865 => 296,  852 => 241,  838 => 285,  820 => 201,  781 => 327,  764 => 274,  725 => 250,  632 => 268,  602 => 215,  565 => 197,  529 => 62,  505 => 156,  487 => 101,  473 => 87,  1853 => 842,  1849 => 753,  1839 => 751,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 733,  1778 => 732,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 693,  1705 => 781,  1688 => 675,  1681 => 769,  1677 => 768,  1667 => 671,  1656 => 762,  1648 => 655,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 606,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 593,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 540,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 522,  1289 => 520,  1276 => 604,  1263 => 601,  1244 => 480,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 463,  1181 => 561,  1171 => 446,  1158 => 550,  1114 => 428,  1104 => 465,  1072 => 512,  1062 => 7,  1024 => 395,  1014 => 406,  1000 => 328,  990 => 303,  980 => 474,  960 => 466,  918 => 348,  888 => 80,  834 => 307,  673 => 64,  636 => 185,  462 => 92,  454 => 158,  1144 => 542,  1139 => 437,  1131 => 399,  1127 => 434,  1110 => 351,  1092 => 459,  1089 => 426,  1086 => 520,  1084 => 335,  1063 => 387,  1060 => 321,  1055 => 422,  1050 => 373,  1035 => 372,  1019 => 330,  1003 => 309,  959 => 387,  900 => 366,  880 => 276,  870 => 297,  867 => 353,  859 => 294,  848 => 215,  839 => 376,  828 => 302,  823 => 374,  809 => 316,  800 => 241,  797 => 267,  794 => 257,  786 => 283,  740 => 78,  734 => 261,  703 => 238,  693 => 236,  630 => 278,  626 => 19,  614 => 275,  610 => 201,  581 => 206,  564 => 268,  525 => 186,  722 => 251,  697 => 282,  674 => 274,  671 => 221,  577 => 180,  569 => 233,  557 => 229,  502 => 99,  497 => 207,  445 => 196,  729 => 209,  684 => 237,  676 => 65,  669 => 220,  660 => 223,  647 => 198,  643 => 229,  601 => 306,  570 => 165,  522 => 220,  501 => 158,  296 => 157,  374 => 152,  631 => 207,  616 => 283,  608 => 281,  605 => 77,  596 => 211,  574 => 200,  561 => 231,  527 => 165,  433 => 190,  388 => 211,  426 => 147,  383 => 135,  461 => 156,  370 => 155,  395 => 131,  294 => 156,  223 => 94,  220 => 75,  492 => 175,  468 => 162,  444 => 168,  410 => 170,  397 => 134,  377 => 161,  262 => 91,  250 => 86,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 1051,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 680,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 638,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 572,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 526,  1301 => 1037,  1252 => 392,  1243 => 994,  1223 => 978,  1212 => 494,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 364,  1134 => 538,  1126 => 443,  1106 => 528,  1100 => 897,  1026 => 418,  997 => 822,  983 => 816,  975 => 296,  956 => 318,  939 => 786,  902 => 362,  894 => 364,  879 => 76,  757 => 269,  727 => 295,  716 => 235,  670 => 224,  528 => 187,  476 => 253,  435 => 176,  354 => 118,  341 => 187,  192 => 47,  321 => 114,  243 => 105,  793 => 266,  780 => 261,  758 => 226,  700 => 193,  686 => 238,  652 => 185,  638 => 226,  620 => 216,  545 => 166,  523 => 171,  494 => 134,  459 => 159,  438 => 146,  351 => 145,  347 => 127,  402 => 150,  268 => 95,  430 => 188,  411 => 182,  379 => 125,  322 => 127,  315 => 170,  289 => 130,  284 => 99,  255 => 77,  234 => 126,  1133 => 444,  1124 => 357,  1121 => 430,  1116 => 549,  1113 => 429,  1108 => 376,  1103 => 368,  1098 => 26,  1081 => 425,  1073 => 424,  1067 => 356,  1064 => 355,  1061 => 421,  1053 => 318,  1049 => 419,  1042 => 313,  1039 => 345,  1025 => 414,  1021 => 310,  1015 => 409,  1008 => 461,  996 => 406,  989 => 454,  985 => 315,  981 => 257,  977 => 321,  970 => 320,  966 => 375,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 148,  919 => 314,  917 => 279,  908 => 411,  905 => 310,  896 => 358,  891 => 378,  877 => 334,  862 => 348,  857 => 269,  837 => 261,  832 => 260,  827 => 349,  821 => 66,  803 => 179,  778 => 324,  769 => 58,  765 => 297,  753 => 54,  746 => 52,  743 => 298,  735 => 75,  730 => 251,  720 => 363,  717 => 362,  712 => 251,  691 => 233,  678 => 275,  654 => 199,  587 => 210,  576 => 201,  539 => 116,  517 => 144,  471 => 160,  441 => 195,  437 => 239,  418 => 138,  386 => 154,  373 => 204,  304 => 110,  270 => 84,  265 => 92,  229 => 97,  477 => 167,  455 => 125,  448 => 143,  429 => 141,  407 => 138,  399 => 138,  389 => 145,  375 => 141,  358 => 198,  349 => 162,  335 => 84,  327 => 130,  298 => 158,  280 => 115,  249 => 88,  194 => 60,  142 => 50,  344 => 136,  318 => 114,  306 => 115,  295 => 112,  357 => 120,  300 => 113,  286 => 151,  276 => 68,  269 => 119,  254 => 92,  128 => 28,  237 => 127,  165 => 58,  122 => 14,  798 => 281,  770 => 302,  759 => 278,  748 => 298,  731 => 260,  721 => 293,  718 => 301,  708 => 239,  696 => 236,  617 => 204,  590 => 207,  553 => 263,  550 => 157,  540 => 258,  533 => 254,  500 => 233,  493 => 155,  489 => 161,  482 => 201,  467 => 258,  464 => 209,  458 => 147,  452 => 145,  449 => 123,  415 => 83,  382 => 126,  372 => 129,  361 => 199,  356 => 131,  339 => 86,  302 => 104,  285 => 107,  258 => 94,  123 => 45,  108 => 25,  424 => 140,  394 => 214,  380 => 143,  338 => 137,  319 => 216,  316 => 113,  312 => 129,  290 => 153,  267 => 141,  206 => 57,  110 => 45,  240 => 83,  224 => 122,  219 => 54,  217 => 80,  202 => 107,  186 => 45,  170 => 28,  100 => 45,  67 => 31,  14 => 1,  1096 => 345,  1090 => 421,  1088 => 435,  1085 => 456,  1066 => 423,  1034 => 282,  1031 => 281,  1018 => 416,  1013 => 358,  1007 => 408,  1002 => 403,  993 => 305,  986 => 264,  982 => 401,  976 => 399,  971 => 295,  964 => 255,  949 => 388,  946 => 402,  940 => 388,  937 => 374,  928 => 385,  926 => 318,  915 => 381,  912 => 82,  903 => 231,  898 => 440,  892 => 303,  889 => 277,  887 => 302,  884 => 79,  876 => 75,  874 => 215,  871 => 331,  863 => 373,  861 => 270,  858 => 347,  850 => 291,  843 => 206,  840 => 406,  815 => 251,  812 => 343,  808 => 246,  804 => 258,  799 => 312,  791 => 310,  785 => 262,  775 => 82,  771 => 253,  754 => 267,  728 => 317,  726 => 72,  723 => 237,  715 => 105,  711 => 285,  709 => 222,  706 => 232,  698 => 229,  694 => 199,  692 => 189,  689 => 234,  681 => 224,  677 => 229,  675 => 234,  663 => 218,  661 => 200,  650 => 222,  646 => 231,  629 => 267,  627 => 218,  625 => 266,  622 => 285,  598 => 232,  592 => 212,  586 => 182,  575 => 232,  566 => 242,  556 => 230,  554 => 188,  541 => 176,  536 => 224,  515 => 183,  511 => 166,  509 => 179,  488 => 152,  486 => 147,  483 => 171,  465 => 198,  463 => 148,  450 => 244,  432 => 129,  419 => 143,  371 => 151,  362 => 159,  353 => 196,  337 => 112,  333 => 153,  309 => 121,  303 => 161,  299 => 76,  291 => 89,  272 => 97,  261 => 138,  253 => 109,  239 => 70,  235 => 69,  213 => 87,  200 => 74,  198 => 48,  159 => 44,  149 => 52,  146 => 21,  131 => 36,  116 => 64,  79 => 35,  74 => 19,  71 => 13,  836 => 262,  817 => 278,  814 => 319,  811 => 235,  805 => 244,  787 => 256,  779 => 169,  776 => 222,  773 => 322,  761 => 296,  751 => 302,  747 => 265,  742 => 336,  739 => 333,  736 => 215,  724 => 259,  705 => 69,  702 => 601,  688 => 226,  680 => 230,  667 => 273,  662 => 242,  656 => 418,  649 => 285,  644 => 220,  641 => 220,  624 => 206,  613 => 166,  607 => 200,  597 => 275,  591 => 49,  584 => 236,  579 => 203,  563 => 198,  559 => 68,  551 => 243,  547 => 191,  537 => 115,  524 => 112,  512 => 174,  507 => 237,  504 => 159,  498 => 213,  485 => 172,  480 => 50,  472 => 96,  466 => 149,  460 => 152,  447 => 156,  442 => 40,  434 => 151,  428 => 127,  422 => 145,  404 => 177,  368 => 202,  364 => 200,  340 => 125,  334 => 135,  330 => 115,  325 => 83,  292 => 150,  287 => 87,  282 => 72,  279 => 104,  273 => 120,  266 => 74,  256 => 93,  252 => 87,  228 => 77,  218 => 62,  201 => 63,  64 => 7,  51 => 20,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 847,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 823,  2037 => 789,  2031 => 788,  2028 => 818,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 811,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 779,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 769,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 744,  1801 => 695,  1798 => 694,  1795 => 742,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 708,  1730 => 705,  1720 => 688,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 672,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 659,  1651 => 656,  1641 => 631,  1637 => 667,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 651,  1592 => 614,  1587 => 613,  1584 => 635,  1581 => 731,  1576 => 608,  1574 => 633,  1567 => 603,  1563 => 630,  1559 => 601,  1552 => 628,  1548 => 597,  1542 => 593,  1529 => 605,  1525 => 614,  1516 => 708,  1512 => 583,  1503 => 599,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 569,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 537,  1380 => 539,  1376 => 523,  1371 => 645,  1367 => 527,  1364 => 518,  1361 => 523,  1355 => 543,  1342 => 624,  1337 => 510,  1328 => 516,  1324 => 511,  1317 => 511,  1313 => 499,  1310 => 498,  1304 => 504,  1291 => 502,  1286 => 395,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 487,  1262 => 490,  1257 => 489,  1254 => 482,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 498,  1232 => 416,  1226 => 413,  1213 => 466,  1208 => 465,  1201 => 472,  1196 => 464,  1192 => 490,  1188 => 456,  1184 => 485,  1179 => 448,  1176 => 461,  1172 => 376,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 561,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 442,  1119 => 354,  1115 => 424,  1111 => 377,  1107 => 349,  1102 => 439,  1099 => 347,  1095 => 419,  1091 => 418,  1082 => 455,  1079 => 524,  1076 => 359,  1070 => 431,  1057 => 491,  1052 => 406,  1045 => 484,  1040 => 416,  1036 => 283,  1032 => 415,  1028 => 312,  1023 => 393,  1020 => 412,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 308,  992 => 406,  979 => 297,  974 => 400,  967 => 399,  962 => 397,  958 => 336,  954 => 293,  950 => 393,  945 => 391,  942 => 288,  938 => 323,  934 => 283,  927 => 282,  923 => 382,  920 => 412,  910 => 278,  901 => 340,  897 => 339,  890 => 343,  886 => 50,  883 => 377,  868 => 375,  856 => 293,  853 => 319,  849 => 264,  845 => 290,  841 => 341,  835 => 354,  830 => 249,  826 => 282,  822 => 281,  818 => 65,  813 => 183,  810 => 290,  806 => 270,  802 => 339,  795 => 311,  792 => 335,  789 => 233,  784 => 286,  782 => 282,  777 => 255,  772 => 289,  768 => 81,  763 => 327,  760 => 256,  756 => 255,  752 => 247,  745 => 245,  741 => 218,  738 => 254,  732 => 171,  719 => 245,  714 => 251,  710 => 200,  704 => 281,  699 => 280,  695 => 66,  690 => 285,  687 => 210,  683 => 346,  679 => 298,  672 => 179,  668 => 264,  665 => 229,  658 => 178,  645 => 221,  640 => 227,  634 => 218,  628 => 174,  623 => 217,  619 => 78,  611 => 282,  606 => 280,  603 => 199,  599 => 194,  595 => 213,  583 => 159,  580 => 45,  573 => 157,  560 => 267,  543 => 146,  538 => 178,  534 => 189,  530 => 174,  526 => 221,  521 => 287,  518 => 170,  514 => 202,  510 => 164,  503 => 59,  496 => 163,  490 => 150,  484 => 146,  474 => 127,  470 => 142,  446 => 122,  440 => 149,  436 => 189,  431 => 145,  425 => 187,  416 => 142,  412 => 76,  408 => 165,  403 => 134,  400 => 133,  396 => 148,  392 => 169,  385 => 169,  381 => 153,  367 => 123,  363 => 133,  359 => 147,  355 => 146,  350 => 120,  346 => 161,  343 => 140,  328 => 118,  324 => 120,  313 => 122,  307 => 105,  301 => 119,  288 => 152,  283 => 127,  271 => 102,  257 => 100,  251 => 132,  238 => 79,  233 => 98,  195 => 30,  191 => 53,  187 => 54,  183 => 51,  130 => 44,  88 => 36,  76 => 17,  115 => 25,  95 => 38,  655 => 177,  651 => 176,  648 => 215,  637 => 218,  633 => 175,  621 => 462,  618 => 179,  615 => 203,  604 => 214,  600 => 233,  588 => 206,  585 => 295,  582 => 205,  571 => 179,  567 => 200,  555 => 37,  552 => 229,  549 => 224,  544 => 230,  542 => 226,  535 => 177,  531 => 174,  519 => 173,  516 => 218,  513 => 217,  508 => 215,  506 => 160,  499 => 177,  495 => 181,  491 => 163,  481 => 161,  478 => 128,  475 => 97,  469 => 182,  456 => 196,  451 => 195,  443 => 194,  439 => 152,  427 => 155,  423 => 114,  420 => 140,  409 => 118,  405 => 218,  401 => 176,  391 => 141,  387 => 334,  384 => 250,  378 => 205,  365 => 153,  360 => 117,  348 => 116,  336 => 136,  332 => 182,  329 => 131,  323 => 149,  310 => 106,  305 => 78,  277 => 94,  274 => 96,  263 => 97,  259 => 72,  247 => 87,  244 => 72,  241 => 129,  222 => 69,  210 => 32,  207 => 110,  204 => 64,  184 => 77,  181 => 29,  167 => 70,  157 => 36,  96 => 19,  421 => 153,  417 => 185,  414 => 182,  406 => 164,  398 => 158,  393 => 125,  390 => 212,  376 => 136,  369 => 124,  366 => 150,  352 => 128,  345 => 115,  342 => 87,  331 => 126,  326 => 137,  320 => 126,  317 => 171,  314 => 112,  311 => 80,  308 => 116,  297 => 203,  293 => 114,  281 => 106,  278 => 145,  275 => 103,  264 => 92,  260 => 96,  248 => 85,  245 => 91,  242 => 84,  231 => 125,  227 => 92,  215 => 83,  212 => 70,  209 => 111,  197 => 79,  177 => 42,  171 => 71,  161 => 54,  132 => 17,  121 => 27,  105 => 39,  99 => 52,  81 => 35,  77 => 35,  180 => 58,  176 => 49,  156 => 53,  143 => 51,  139 => 79,  118 => 65,  189 => 72,  185 => 59,  173 => 48,  166 => 55,  152 => 22,  174 => 41,  164 => 94,  154 => 90,  150 => 38,  137 => 78,  133 => 33,  127 => 46,  107 => 31,  102 => 34,  83 => 24,  78 => 38,  53 => 21,  23 => 4,  42 => 6,  138 => 34,  134 => 46,  109 => 32,  103 => 39,  97 => 51,  94 => 22,  84 => 25,  75 => 11,  69 => 11,  66 => 17,  54 => 28,  44 => 7,  230 => 64,  226 => 123,  203 => 63,  193 => 31,  188 => 30,  182 => 101,  178 => 28,  168 => 96,  163 => 48,  160 => 51,  155 => 45,  148 => 52,  145 => 51,  140 => 50,  136 => 53,  125 => 69,  120 => 66,  113 => 41,  101 => 53,  92 => 42,  89 => 37,  85 => 21,  73 => 32,  62 => 30,  59 => 25,  56 => 29,  41 => 6,  126 => 42,  119 => 44,  111 => 39,  106 => 36,  98 => 27,  93 => 18,  86 => 16,  70 => 18,  60 => 16,  28 => 5,  36 => 11,  114 => 24,  104 => 35,  91 => 26,  80 => 39,  63 => 27,  58 => 12,  40 => 8,  34 => 4,  45 => 9,  61 => 26,  55 => 22,  48 => 15,  39 => 13,  35 => 9,  31 => 3,  26 => 2,  21 => 2,  46 => 11,  29 => 6,  57 => 30,  50 => 27,  47 => 7,  38 => 4,  33 => 13,  49 => 10,  32 => 9,  246 => 131,  236 => 65,  232 => 78,  225 => 59,  221 => 77,  216 => 54,  214 => 114,  211 => 60,  208 => 50,  205 => 109,  199 => 58,  196 => 103,  190 => 54,  179 => 76,  175 => 57,  172 => 56,  169 => 48,  162 => 42,  158 => 92,  153 => 24,  151 => 60,  147 => 59,  144 => 82,  141 => 56,  135 => 77,  129 => 43,  124 => 31,  117 => 25,  112 => 40,  90 => 17,  87 => 25,  82 => 40,  72 => 15,  68 => 30,  65 => 29,  52 => 8,  43 => 14,  37 => 5,  30 => 4,  27 => 6,  25 => 5,  24 => 4,  22 => 2,  19 => 1,);
    }
}
