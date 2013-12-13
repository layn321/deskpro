<?php

/* AgentBundle:UserTrack:view.html.twig */
class __TwigTemplate_dec53620201e5add03742ee9bdc1e165 extends \Application\DeskPRO\Twig\Template
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
        $context["agentui"] = $this->env->loadTemplate("AgentBundle:Common:agent-macros.html.twig");
        // line 2
        echo "<script>
pageMeta.fragmentClass = 'DeskPRO.Agent.PageFragment.Page.Visitor';
";
        // line 4
        if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
        if ($this->getAttribute($_visitor_, "person")) {
            // line 5
            echo "\tpageMeta.title = ";
            if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
            echo twig_jsonencode_filter(("Visitor: " . $this->getAttribute($this->getAttribute($_visitor_, "person"), "display_name")));
            echo ";
";
        } else {
            // line 7
            echo "\tpageMeta.title = 'Visitor ";
            if (isset($context["vis"])) { $_vis_ = $context["vis"]; } else { $_vis_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_vis_, "visit_track"), "ip_address"), "html", null, true);
            echo "';
";
        }
        // line 9
        echo "pageMeta.visitor_id = ";
        if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_visitor_, "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_visitor_, "id"), 0)) : (0)), "html", null, true);
        echo ";

";
        // line 11
        $context["baseId"] = $this->env->getExtension('deskpro_templating')->elUid();
        // line 12
        echo "pageMeta.baseId = '";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "';
</script>
<div class=\"visitor-page\">

";
        // line 19
        echo "
<header class=\"page-header\">
\t";
        // line 21
        if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
        if ($this->getAttribute($this->getAttribute($_visitor_, "visit_track"), "geo_country")) {
            // line 22
            echo "\t\t<span class=\"country\" style=\"background-image: url(";
            if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl((("images/flags/" . $this->env->getExtension('deskpro_templating')->strLower($this->getAttribute($this->getAttribute($_visitor_, "visit_track"), "geo_country"))) . ".png")), "html", null, true);
            echo ");\" title=\"";
            if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->countryName($this->getAttribute($this->getAttribute($_visitor_, "visit_track"), "geo_country")), "html", null, true);
            echo "\"></span>
\t";
        }
        // line 24
        echo "\t<span class=\"ip-address\">";
        if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_visitor_, "visit_track"), "ip_address"), "html", null, true);
        echo "</span>
\t<h4 class=\"id-number\">#";
        // line 25
        if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_visitor_, "id"), "html", null, true);
        echo "</h4>
\t<h1>
\t\t";
        // line 27
        if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
        if ($this->getAttribute($_visitor_, "person")) {
            // line 28
            echo "\t\t\t<span class=\"user-link\" data-route=\"page:";
            if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_people_view", array("person_id" => $this->getAttribute($this->getAttribute($_visitor_, "person"), "id"))), "html", null, true);
            echo "\" style=\"background-image: url('";
            if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_visitor_, "person"), "getPictureUrl", array(0 => 30), "method"), "html", null, true);
            echo "');\">
\t\t\t\t";
            // line 29
            if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_visitor_, "person"), "display_name"), "html", null, true);
            echo "
\t\t\t</span>
\t\t";
        } else {
            // line 32
            echo "\t\t\t";
            if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
            if (($this->getAttribute($_visitor_, "name") || $this->getAttribute($_visitor_, "email"))) {
                // line 33
                echo "\t\t\t\t";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.anonymous");
                echo ": ";
                if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_visitor_, "name"), "html", null, true);
                echo " ";
                if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
                if ($this->getAttribute($_visitor_, "email")) {
                    echo "(";
                    if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_visitor_, "email"), "html", null, true);
                    echo ")";
                }
                // line 34
                echo "\t\t\t";
            }
            // line 35
            echo "\t\t";
        }
        // line 36
        echo "\t</h1>
\t<br class=\"clear\" />
</header>

";
        // line 43
        echo "
<div class=\"profile-box-container main-box\" id=\"";
        // line 44
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_main_view\">
\t<header>
\t\t<nav data-element-handler=\"DeskPRO.ElementHandler.SimpleTabs\" id=\"";
        // line 46
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_main_tabs_nav\">
\t\t\t<ul>
\t\t\t\t<li data-tab-for=\"#";
        // line 48
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_tracks\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.usertrack.visited_pages");
        echo "</li>
\t\t\t\t<li data-tab-for=\"#";
        // line 49
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_visits\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.usertrack.landing_pages");
        echo "</li>
\t\t\t\t<li data-tab-for=\"#";
        // line 50
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_details\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.details");
        echo "</li>
\t\t\t</ul>
\t\t</nav>
\t</header>
\t<section>
\t\t<article id=\"";
        // line 55
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_tracks\" style=\"display: none;\">
\t\t\t<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" class=\"data-display-table with-head\">
\t\t\t\t<tbody>
\t\t\t\t\t<tr>
\t\t\t\t\t\t<th class=\"col-head\">";
        // line 59
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.page");
        echo "</th>
\t\t\t\t\t\t<th class=\"col-head\" width=\"10\">";
        // line 60
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.date");
        echo "</th>
\t\t\t\t\t</tr>
\t\t\t\t\t";
        // line 62
        if (isset($context["tracks"])) { $_tracks_ = $context["tracks"]; } else { $_tracks_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_tracks_);
        foreach ($context['_seq'] as $context["_key"] => $context["track"]) {
            // line 63
            echo "\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t\t";
            // line 65
            if (isset($context["track"])) { $_track_ = $context["track"]; } else { $_track_ = null; }
            if ($this->getAttribute($_track_, "page_title")) {
                // line 66
                echo "\t\t\t\t\t\t\t\t\t<div style=\"font-weight: bold; margin-bottom: 0px;\">";
                if (isset($context["track"])) { $_track_ = $context["track"]; } else { $_track_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_track_, "page_title"), "html", null, true);
                echo "</div>
\t\t\t\t\t\t\t\t";
            }
            // line 68
            echo "\t\t\t\t\t\t\t\t<a href=\"";
            if (isset($context["track"])) { $_track_ = $context["track"]; } else { $_track_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_track_, "page_url"), "html", null, true);
            echo "\" target=\"_blank\">
\t\t\t\t\t\t\t\t\t";
            // line 69
            if (isset($context["track"])) { $_track_ = $context["track"]; } else { $_track_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->urlTrimScheme($this->getAttribute($_track_, "page_url"), true), "html", null, true);
            echo "
\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t";
            // line 71
            if (isset($context["track"])) { $_track_ = $context["track"]; } else { $_track_ = null; }
            if ($this->getAttribute($_track_, "ref_page_url")) {
                // line 72
                echo "\t\t\t\t\t\t\t\t<div style=\"font-size: 10px;\">
\t\t\t\t\t\t\t\t\t";
                // line 73
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.referrer");
                echo ":
\t\t\t\t\t\t\t\t\t<a href=\"";
                // line 74
                if (isset($context["track"])) { $_track_ = $context["track"]; } else { $_track_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_track_, "ref_page_url"), "html", null, true);
                echo "\" target=\"_blank\">
\t\t\t\t\t\t\t\t\t\t";
                // line 75
                if (isset($context["track"])) { $_track_ = $context["track"]; } else { $_track_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->urlTrimScheme($this->getAttribute($_track_, "ref_page_url"), true), "html", null, true);
                echo "
\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t";
            }
            // line 79
            echo "\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t<td nowrap=\"nowrap\" width=\"10\"><span style=\"white-space: nowrap; display: block;\">";
            // line 80
            if (isset($context["track"])) { $_track_ = $context["track"]; } else { $_track_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_track_, "date_created"), "fulltime"), "html", null, true);
            echo "</span></td>
\t\t\t\t\t\t</tr>
\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['track'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 83
        echo "\t\t\t\t</tbody>
\t\t\t</table>
\t\t</article>
\t\t<article id=\"";
        // line 86
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_visits\" style=\"display: none;\">
\t\t\t<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" class=\"data-display-table with-head\">
\t\t\t\t<tbody>
\t\t\t\t\t<tr>
\t\t\t\t\t\t<th class=\"col-head\">";
        // line 90
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.usertrack.landing_pages");
        echo "</th>
\t\t\t\t\t\t<th class=\"col-head\">";
        // line 91
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.details");
        echo "</th>
\t\t\t\t\t\t<th class=\"col-head\" width=\"10\">";
        // line 92
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.date");
        echo "</th>
\t\t\t\t\t</tr>
\t\t\t\t\t";
        // line 94
        if (isset($context["visit_tracks"])) { $_visit_tracks_ = $context["visit_tracks"]; } else { $_visit_tracks_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_visit_tracks_);
        foreach ($context['_seq'] as $context["_key"] => $context["track"]) {
            // line 95
            echo "\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t\t";
            // line 97
            if (isset($context["track"])) { $_track_ = $context["track"]; } else { $_track_ = null; }
            if ($this->getAttribute($_track_, "page_title")) {
                // line 98
                echo "\t\t\t\t\t\t\t\t\t<div style=\"font-weight: bold; margin-bottom: 0px;\">";
                if (isset($context["track"])) { $_track_ = $context["track"]; } else { $_track_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_track_, "page_title"), "html", null, true);
                echo "</div>
\t\t\t\t\t\t\t\t";
            }
            // line 100
            echo "\t\t\t\t\t\t\t\t<a href=\"";
            if (isset($context["track"])) { $_track_ = $context["track"]; } else { $_track_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_track_, "page_url"), "html", null, true);
            echo "\" target=\"_blank\">
\t\t\t\t\t\t\t\t\t";
            // line 101
            if (isset($context["track"])) { $_track_ = $context["track"]; } else { $_track_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->urlTrimScheme($this->getAttribute($_track_, "page_url"), true), "html", null, true);
            echo "
\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t";
            // line 103
            if (isset($context["track"])) { $_track_ = $context["track"]; } else { $_track_ = null; }
            if ($this->getAttribute($_track_, "ref_page_url")) {
                // line 104
                echo "\t\t\t\t\t\t\t\t<div style=\"font-size: 10px;\">
\t\t\t\t\t\t\t\t\t";
                // line 105
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.referrer");
                echo ":
\t\t\t\t\t\t\t\t\t<a href=\"";
                // line 106
                if (isset($context["track"])) { $_track_ = $context["track"]; } else { $_track_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_track_, "ref_page_url"), "html", null, true);
                echo "\" target=\"_blank\">
\t\t\t\t\t\t\t\t\t\t";
                // line 107
                if (isset($context["track"])) { $_track_ = $context["track"]; } else { $_track_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->urlTrimScheme($this->getAttribute($_track_, "ref_page_url"), true), "html", null, true);
                echo "
\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t";
            }
            // line 111
            echo "\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t\t";
            // line 113
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ip_address");
            echo ": ";
            if (isset($context["track"])) { $_track_ = $context["track"]; } else { $_track_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_track_, "ip_address"), "html", null, true);
            echo "
\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t<td nowrap=\"nowrap\" width=\"10\"><span style=\"white-space: nowrap; display: block;\">";
            // line 115
            if (isset($context["track"])) { $_track_ = $context["track"]; } else { $_track_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_track_, "date_created"), "fulltime"), "html", null, true);
            echo "</span></td>
\t\t\t\t\t\t</tr>
\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['track'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 118
        echo "\t\t\t\t</tbody>
\t\t\t</table>
\t\t</article>
\t\t<article id=\"";
        // line 121
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_details\" style=\"display: none;\">
\t\t\t<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" class=\"data-display-table with-head\">
\t\t\t\t<tbody>
\t\t\t\t\t<tr>
\t\t\t\t\t\t<th width=\"150\">";
        // line 125
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.usertrack.visitor_id");
        echo ":</th>
\t\t\t\t\t\t<td>";
        // line 126
        if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_visitor_, "id"), "html", null, true);
        echo "</td>
\t\t\t\t\t</tr>
\t\t\t\t\t<tr>
\t\t\t\t\t\t<th>";
        // line 129
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.user");
        echo ":</th>
\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t";
        // line 131
        if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
        if ($this->getAttribute($_visitor_, "person")) {
            // line 132
            echo "\t\t\t\t\t\t\t\t<span class=\"user-link\" data-route=\"page:";
            if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_people_view", array("person_id" => $this->getAttribute($this->getAttribute($_visitor_, "person"), "id"))), "html", null, true);
            echo "\" style=\"background-image: url('";
            if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_visitor_, "person"), "getPictureUrl", array(0 => 24), "method"), "html", null, true);
            echo "');\">
\t\t\t\t\t\t\t\t\t";
            // line 133
            if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_visitor_, "person"), "display_name"), "html", null, true);
            echo "
\t\t\t\t\t\t\t\t</span>
\t\t\t\t\t\t\t";
        } else {
            // line 136
            echo "\t\t\t\t\t\t\t\t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.anonymous");
            echo "
\t\t\t\t\t\t\t";
        }
        // line 138
        echo "\t\t\t\t\t\t</td>
\t\t\t\t\t</tr>
\t\t\t\t\t<tr>
\t\t\t\t\t\t<th>";
        // line 141
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.usertrack.first_visit_date");
        echo ":</th>
\t\t\t\t\t\t<td>";
        // line 142
        if (isset($context["visitor"])) { $_visitor_ = $context["visitor"]; } else { $_visitor_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_visitor_, "date_created"), "fulltime"), "html", null, true);
        echo "</td>
\t\t\t\t\t</tr>
\t\t\t\t\t<tr>
\t\t\t\t\t\t<th>";
        // line 145
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.usertrack.number_of_pages");
        echo ":</th>
\t\t\t\t\t\t<td>";
        // line 146
        if (isset($context["tracks"])) { $_tracks_ = $context["tracks"]; } else { $_tracks_ = null; }
        echo twig_escape_filter($this->env, twig_length_filter($this->env, $_tracks_), "html", null, true);
        echo "</td>
\t\t\t\t\t</tr>
\t\t\t\t\t<tr>
\t\t\t\t\t\t<th>";
        // line 149
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.usertrack.number_of_visits");
        echo ":</th>
\t\t\t\t\t\t<td>";
        // line 150
        if (isset($context["visit_tracks"])) { $_visit_tracks_ = $context["visit_tracks"]; } else { $_visit_tracks_ = null; }
        echo twig_escape_filter($this->env, twig_length_filter($this->env, $_visit_tracks_), "html", null, true);
        echo "</td>
\t\t\t\t\t</tr>
\t\t\t\t\t";
        // line 152
        if (isset($context["geo_countries"])) { $_geo_countries_ = $context["geo_countries"]; } else { $_geo_countries_ = null; }
        if ($_geo_countries_) {
            // line 153
            echo "\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t<th>";
            // line 154
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.countries");
            echo ":</th>
\t\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t\t<ul>
\t\t\t\t\t\t\t\t\t";
            // line 157
            if (isset($context["geo_countries"])) { $_geo_countries_ = $context["geo_countries"]; } else { $_geo_countries_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_geo_countries_);
            foreach ($context['_seq'] as $context["_key"] => $context["r"]) {
                // line 158
                echo "\t\t\t\t\t\t\t\t\t\t<li>&bull; ";
                if (isset($context["r"])) { $_r_ = $context["r"]; } else { $_r_ = null; }
                echo twig_escape_filter($this->env, $_r_, "html", null, true);
                echo "</li>
\t\t\t\t\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['r'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 160
            echo "\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t</tr>
\t\t\t\t\t";
        }
        // line 164
        echo "\t\t\t\t\t<tr>
\t\t\t\t\t\t<th>";
        // line 165
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ip_addresses");
        echo ":</th>
\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t<ul>
\t\t\t\t\t\t\t\t";
        // line 168
        if (isset($context["ip_addresses"])) { $_ip_addresses_ = $context["ip_addresses"]; } else { $_ip_addresses_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_ip_addresses_);
        foreach ($context['_seq'] as $context["_key"] => $context["r"]) {
            // line 169
            echo "\t\t\t\t\t\t\t\t\t<li>&bull; ";
            if (isset($context["r"])) { $_r_ = $context["r"]; } else { $_r_ = null; }
            echo twig_escape_filter($this->env, $_r_, "html", null, true);
            echo "</li>
\t\t\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['r'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 171
        echo "\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t</td>
\t\t\t\t\t</tr>
\t\t\t\t\t<tr>
\t\t\t\t\t\t<th>";
        // line 175
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.user_agents");
        echo ":</th>
\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t<ul>
\t\t\t\t\t\t\t\t";
        // line 178
        if (isset($context["user_agents"])) { $_user_agents_ = $context["user_agents"]; } else { $_user_agents_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_user_agents_);
        foreach ($context['_seq'] as $context["_key"] => $context["r"]) {
            // line 179
            echo "\t\t\t\t\t\t\t\t\t<li>&bull; ";
            if (isset($context["r"])) { $_r_ = $context["r"]; } else { $_r_ = null; }
            echo twig_escape_filter($this->env, $_r_, "html", null, true);
            echo "</li>
\t\t\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['r'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 181
        echo "\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t</td>
\t\t\t\t\t</tr>
\t\t\t\t</tbody>
\t\t\t</table>
\t\t</article>
\t</section>
</div>

</div>";
    }

    public function getTemplateName()
    {
        return "AgentBundle:UserTrack:view.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1424 => 451,  1366 => 426,  1333 => 422,  1306 => 418,  1295 => 413,  1233 => 397,  1217 => 393,  1204 => 389,  1017 => 327,  994 => 192,  951 => 301,  1071 => 285,  1058 => 344,  1054 => 221,  1041 => 273,  1011 => 199,  869 => 223,  767 => 187,  3071 => 913,  3059 => 903,  3052 => 899,  3047 => 898,  3043 => 897,  3037 => 894,  3028 => 891,  3019 => 887,  3016 => 886,  3014 => 885,  3006 => 880,  2996 => 873,  2989 => 870,  2979 => 866,  2970 => 860,  2964 => 858,  2958 => 855,  2954 => 854,  2945 => 848,  2939 => 845,  2929 => 838,  2922 => 834,  2912 => 827,  2906 => 824,  2900 => 820,  2896 => 818,  2888 => 817,  2883 => 816,  2879 => 815,  2872 => 813,  2869 => 812,  2864 => 810,  2857 => 806,  2848 => 801,  2832 => 799,  2827 => 798,  2817 => 797,  2814 => 796,  2812 => 795,  2802 => 794,  2786 => 792,  2779 => 791,  2775 => 790,  2770 => 789,  2762 => 788,  2755 => 787,  2751 => 786,  2747 => 785,  2741 => 784,  2729 => 776,  2721 => 772,  2711 => 767,  2698 => 757,  2690 => 752,  2684 => 749,  2651 => 732,  2642 => 730,  2634 => 725,  2628 => 724,  2622 => 723,  2619 => 722,  2615 => 721,  2612 => 720,  2605 => 718,  2599 => 717,  2591 => 714,  2587 => 712,  2579 => 708,  2574 => 707,  2569 => 705,  2566 => 704,  2562 => 703,  2554 => 699,  2549 => 698,  2544 => 696,  2541 => 695,  2524 => 689,  2519 => 687,  2516 => 686,  2512 => 685,  2504 => 681,  2494 => 678,  2479 => 672,  2474 => 671,  2466 => 668,  2462 => 667,  2444 => 660,  2438 => 659,  2435 => 658,  2430 => 656,  2417 => 651,  2412 => 649,  2409 => 648,  2405 => 647,  2397 => 643,  2387 => 640,  2375 => 635,  2368 => 633,  2362 => 631,  2356 => 630,  2353 => 629,  2348 => 627,  2345 => 626,  2341 => 625,  2336 => 622,  2329 => 620,  2323 => 618,  2317 => 617,  2314 => 616,  2309 => 614,  2306 => 613,  2302 => 612,  2286 => 606,  2280 => 604,  2274 => 603,  2271 => 602,  2266 => 600,  2263 => 599,  2259 => 598,  2249 => 594,  2244 => 593,  2239 => 591,  2232 => 588,  2226 => 586,  2221 => 585,  2210 => 582,  2197 => 581,  2180 => 579,  2169 => 576,  2150 => 575,  2140 => 572,  2131 => 570,  2110 => 567,  2106 => 566,  2061 => 561,  2026 => 554,  2018 => 553,  2000 => 550,  1990 => 548,  1962 => 540,  1956 => 536,  1939 => 532,  1935 => 531,  1896 => 520,  1888 => 519,  1881 => 518,  1869 => 517,  1857 => 516,  1848 => 514,  1844 => 512,  1832 => 508,  1811 => 502,  1808 => 501,  1786 => 492,  1779 => 490,  1769 => 485,  1762 => 483,  1758 => 482,  1735 => 472,  1731 => 470,  1712 => 465,  1707 => 463,  1696 => 461,  1683 => 454,  1679 => 452,  1655 => 445,  1650 => 444,  1647 => 443,  1635 => 439,  1607 => 427,  1597 => 423,  1541 => 402,  1523 => 393,  1495 => 389,  1485 => 387,  1455 => 464,  1450 => 382,  1441 => 378,  1435 => 377,  1419 => 375,  1407 => 443,  1362 => 364,  1347 => 361,  1296 => 352,  1258 => 348,  1253 => 347,  1177 => 325,  1151 => 374,  1118 => 361,  1330 => 4,  1327 => 3,  1322 => 2,  1290 => 297,  1283 => 295,  1278 => 292,  1271 => 349,  1230 => 277,  1190 => 388,  1166 => 258,  1138 => 251,  642 => 270,  1264 => 464,  1259 => 462,  1227 => 394,  1211 => 336,  1399 => 568,  1387 => 560,  1358 => 555,  1334 => 357,  1316 => 529,  1298 => 520,  1195 => 478,  1148 => 253,  1120 => 453,  1117 => 245,  1093 => 354,  788 => 242,  612 => 182,  1394 => 446,  1385 => 442,  1382 => 441,  1369 => 433,  1363 => 432,  1360 => 431,  1350 => 429,  1305 => 523,  1300 => 409,  1281 => 404,  1277 => 350,  1265 => 398,  1255 => 396,  1247 => 393,  1234 => 387,  1202 => 270,  1199 => 374,  1187 => 264,  1162 => 365,  1136 => 317,  1128 => 352,  1122 => 248,  1069 => 299,  968 => 387,  846 => 188,  1183 => 449,  1132 => 366,  1097 => 356,  957 => 272,  907 => 288,  875 => 263,  653 => 274,  1329 => 405,  1309 => 354,  1303 => 399,  1297 => 398,  1292 => 397,  1280 => 405,  1249 => 346,  1237 => 279,  1205 => 409,  1200 => 408,  1194 => 440,  1178 => 430,  1175 => 447,  1170 => 375,  1135 => 362,  1105 => 358,  1078 => 335,  1068 => 388,  1048 => 417,  961 => 273,  922 => 182,  750 => 229,  842 => 263,  1038 => 212,  904 => 198,  882 => 227,  831 => 267,  860 => 264,  790 => 286,  733 => 224,  707 => 185,  744 => 137,  873 => 271,  824 => 254,  762 => 225,  713 => 225,  578 => 193,  2739 => 1153,  2735 => 1152,  2732 => 1151,  2725 => 1146,  2712 => 1144,  2707 => 1143,  2692 => 1131,  2688 => 1130,  2683 => 1127,  2680 => 748,  2677 => 1125,  2671 => 743,  2658 => 735,  2653 => 1118,  2645 => 731,  2641 => 1112,  2636 => 726,  2632 => 1108,  2629 => 1107,  2626 => 1106,  2606 => 1089,  2602 => 1088,  2598 => 1087,  2594 => 1086,  2590 => 1085,  2586 => 1084,  2572 => 1073,  2568 => 1072,  2564 => 1071,  2560 => 1070,  2556 => 1069,  2552 => 1068,  2537 => 694,  2533 => 1055,  2529 => 690,  2525 => 1053,  2521 => 1052,  2503 => 1040,  2499 => 680,  2495 => 1038,  2491 => 677,  2487 => 676,  2483 => 1035,  2469 => 669,  2465 => 1023,  2461 => 1022,  2457 => 664,  2453 => 1020,  2449 => 1019,  2426 => 999,  2422 => 652,  2418 => 997,  2414 => 996,  2410 => 995,  2406 => 994,  2392 => 642,  2388 => 982,  2384 => 639,  2380 => 638,  2376 => 979,  2372 => 978,  2358 => 967,  2354 => 966,  2350 => 965,  2346 => 964,  2342 => 963,  2338 => 962,  2324 => 951,  2320 => 950,  2316 => 949,  2312 => 948,  2308 => 947,  2304 => 946,  2295 => 939,  2282 => 937,  2277 => 936,  2269 => 931,  2265 => 930,  2260 => 927,  2256 => 597,  2253 => 925,  2230 => 901,  2220 => 899,  2216 => 898,  2211 => 896,  2208 => 895,  2198 => 893,  2194 => 580,  2189 => 890,  2184 => 889,  2177 => 578,  2173 => 577,  2164 => 877,  2151 => 875,  2146 => 574,  2143 => 873,  2141 => 864,  2134 => 571,  2130 => 859,  2122 => 853,  2120 => 852,  2113 => 848,  2105 => 846,  2096 => 839,  2083 => 564,  2078 => 836,  2073 => 562,  2067 => 830,  2062 => 828,  2058 => 827,  2054 => 826,  2050 => 560,  2045 => 557,  2038 => 822,  2034 => 821,  2022 => 814,  2009 => 812,  1999 => 809,  1993 => 805,  1988 => 803,  1984 => 802,  1980 => 801,  1976 => 546,  1971 => 799,  1968 => 798,  1964 => 797,  1960 => 796,  1954 => 535,  1946 => 788,  1942 => 533,  1938 => 786,  1932 => 530,  1927 => 780,  1919 => 527,  1915 => 777,  1911 => 776,  1906 => 775,  1903 => 774,  1899 => 773,  1895 => 772,  1886 => 768,  1883 => 764,  1871 => 759,  1866 => 756,  1863 => 755,  1842 => 752,  1825 => 749,  1818 => 748,  1815 => 503,  1800 => 499,  1793 => 741,  1772 => 729,  1761 => 721,  1757 => 720,  1751 => 717,  1740 => 709,  1719 => 697,  1715 => 696,  1686 => 673,  1676 => 669,  1665 => 661,  1661 => 446,  1645 => 442,  1638 => 649,  1630 => 438,  1625 => 435,  1622 => 641,  1598 => 637,  1577 => 416,  1558 => 408,  1549 => 405,  1546 => 626,  1535 => 618,  1531 => 617,  1514 => 606,  1510 => 605,  1504 => 391,  1493 => 594,  1483 => 590,  1464 => 580,  1458 => 577,  1443 => 568,  1437 => 565,  1426 => 557,  1405 => 545,  1401 => 544,  1395 => 541,  1390 => 538,  1381 => 368,  1373 => 531,  1348 => 521,  1343 => 424,  1335 => 515,  1321 => 419,  1318 => 549,  1299 => 414,  1294 => 298,  1282 => 496,  1269 => 466,  1260 => 397,  1240 => 344,  1221 => 339,  1216 => 338,  1210 => 272,  1206 => 445,  1193 => 467,  1189 => 466,  1155 => 468,  1150 => 419,  1022 => 312,  1006 => 324,  988 => 398,  969 => 309,  965 => 253,  921 => 286,  878 => 275,  866 => 222,  854 => 254,  819 => 322,  796 => 144,  1713 => 683,  1700 => 681,  1685 => 674,  1662 => 670,  1643 => 669,  1640 => 441,  1631 => 663,  1618 => 661,  1613 => 430,  1608 => 639,  1605 => 656,  1602 => 424,  1589 => 645,  1586 => 644,  1583 => 643,  1554 => 406,  1547 => 404,  1521 => 602,  1508 => 600,  1499 => 390,  1491 => 593,  1482 => 386,  1472 => 582,  1468 => 581,  1465 => 579,  1457 => 573,  1445 => 460,  1422 => 556,  1416 => 553,  1410 => 548,  1397 => 546,  1392 => 545,  1386 => 431,  1377 => 532,  1370 => 530,  1357 => 528,  1352 => 425,  1341 => 428,  1314 => 510,  1311 => 1,  1275 => 493,  1248 => 284,  1238 => 398,  1225 => 476,  1220 => 275,  1209 => 466,  1185 => 385,  1182 => 387,  1159 => 421,  1154 => 375,  1130 => 438,  1125 => 362,  1101 => 308,  1074 => 286,  1056 => 326,  1046 => 216,  1043 => 293,  1030 => 397,  1027 => 331,  947 => 299,  925 => 242,  913 => 259,  893 => 231,  881 => 253,  847 => 158,  829 => 209,  825 => 259,  1083 => 237,  995 => 320,  984 => 257,  963 => 292,  941 => 354,  851 => 367,  682 => 170,  1365 => 365,  1359 => 545,  1349 => 539,  1336 => 427,  1331 => 424,  1323 => 420,  1319 => 530,  1312 => 402,  1284 => 475,  1272 => 401,  1268 => 509,  1261 => 288,  1251 => 285,  1245 => 483,  1231 => 341,  1207 => 271,  1197 => 267,  1180 => 326,  1173 => 457,  1169 => 259,  1157 => 323,  1147 => 438,  1109 => 330,  1065 => 349,  1059 => 7,  1047 => 274,  1044 => 215,  1033 => 381,  1009 => 357,  991 => 363,  987 => 404,  973 => 294,  931 => 202,  924 => 261,  911 => 347,  906 => 81,  885 => 337,  872 => 335,  855 => 219,  749 => 240,  701 => 172,  594 => 180,  1163 => 377,  1143 => 318,  1087 => 420,  1077 => 300,  1051 => 325,  1037 => 223,  1010 => 325,  999 => 321,  932 => 352,  899 => 306,  895 => 404,  933 => 185,  914 => 238,  909 => 323,  833 => 284,  783 => 193,  755 => 303,  666 => 214,  453 => 174,  639 => 209,  568 => 23,  520 => 200,  657 => 184,  572 => 201,  609 => 232,  20 => 1,  659 => 174,  562 => 22,  548 => 180,  558 => 13,  479 => 88,  589 => 154,  457 => 175,  413 => 224,  953 => 249,  948 => 267,  935 => 296,  929 => 243,  916 => 180,  864 => 365,  844 => 259,  816 => 342,  807 => 291,  801 => 145,  774 => 238,  766 => 312,  737 => 297,  685 => 218,  664 => 163,  635 => 171,  593 => 196,  546 => 171,  532 => 206,  865 => 267,  852 => 241,  838 => 258,  820 => 149,  781 => 240,  764 => 198,  725 => 250,  632 => 204,  602 => 170,  565 => 145,  529 => 179,  505 => 15,  487 => 137,  473 => 212,  1853 => 515,  1849 => 753,  1839 => 751,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 733,  1778 => 732,  1770 => 807,  1749 => 478,  1738 => 793,  1728 => 469,  1721 => 788,  1717 => 787,  1709 => 693,  1705 => 781,  1688 => 675,  1681 => 769,  1677 => 451,  1667 => 449,  1656 => 762,  1648 => 655,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 413,  1545 => 719,  1534 => 606,  1527 => 395,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 593,  1474 => 688,  1456 => 682,  1449 => 461,  1438 => 457,  1431 => 455,  1420 => 450,  1413 => 446,  1404 => 372,  1400 => 440,  1383 => 540,  1379 => 557,  1356 => 632,  1351 => 362,  1338 => 423,  1332 => 617,  1315 => 613,  1302 => 353,  1289 => 406,  1276 => 604,  1263 => 601,  1244 => 456,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 332,  1181 => 561,  1171 => 446,  1158 => 550,  1114 => 428,  1104 => 465,  1072 => 333,  1062 => 7,  1024 => 330,  1014 => 265,  1000 => 368,  990 => 303,  980 => 393,  960 => 466,  918 => 290,  888 => 276,  834 => 257,  673 => 190,  636 => 145,  462 => 154,  454 => 132,  1144 => 370,  1139 => 356,  1131 => 316,  1127 => 434,  1110 => 359,  1092 => 459,  1089 => 239,  1086 => 238,  1084 => 337,  1063 => 232,  1060 => 231,  1055 => 295,  1050 => 340,  1035 => 291,  1019 => 266,  1003 => 401,  959 => 387,  900 => 178,  880 => 273,  870 => 250,  867 => 249,  859 => 164,  848 => 271,  839 => 155,  828 => 150,  823 => 208,  809 => 179,  800 => 241,  797 => 267,  794 => 177,  786 => 285,  740 => 226,  734 => 268,  703 => 297,  693 => 297,  630 => 170,  626 => 169,  614 => 257,  610 => 200,  581 => 143,  564 => 157,  525 => 138,  722 => 226,  697 => 197,  674 => 270,  671 => 212,  577 => 97,  569 => 187,  557 => 150,  502 => 169,  497 => 168,  445 => 149,  729 => 306,  684 => 290,  676 => 213,  669 => 282,  660 => 105,  647 => 271,  643 => 229,  601 => 161,  570 => 129,  522 => 170,  501 => 91,  296 => 103,  374 => 127,  631 => 242,  616 => 152,  608 => 150,  605 => 165,  596 => 164,  574 => 162,  561 => 188,  527 => 170,  433 => 151,  388 => 129,  426 => 147,  383 => 102,  461 => 154,  370 => 98,  395 => 135,  294 => 66,  223 => 53,  220 => 69,  492 => 14,  468 => 135,  444 => 143,  410 => 138,  397 => 130,  377 => 125,  262 => 83,  250 => 79,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 793,  2649 => 2234,  2625 => 2213,  2517 => 1051,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 662,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 608,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 568,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 543,  1826 => 506,  1819 => 504,  1732 => 1414,  1723 => 467,  1695 => 680,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 638,  1533 => 397,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 572,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 526,  1301 => 1037,  1252 => 400,  1243 => 399,  1223 => 978,  1212 => 494,  1167 => 366,  1161 => 933,  1145 => 923,  1140 => 368,  1134 => 409,  1126 => 443,  1106 => 528,  1100 => 897,  1026 => 220,  997 => 367,  983 => 298,  975 => 361,  956 => 318,  939 => 264,  902 => 274,  894 => 177,  879 => 76,  757 => 185,  727 => 207,  716 => 219,  670 => 181,  528 => 172,  476 => 123,  435 => 148,  354 => 115,  341 => 107,  192 => 93,  321 => 75,  243 => 123,  793 => 245,  780 => 140,  758 => 233,  700 => 262,  686 => 294,  652 => 172,  638 => 269,  620 => 201,  545 => 220,  523 => 141,  494 => 164,  459 => 114,  438 => 146,  351 => 89,  347 => 88,  402 => 131,  268 => 98,  430 => 137,  411 => 141,  379 => 124,  322 => 104,  315 => 73,  289 => 78,  284 => 101,  255 => 66,  234 => 60,  1133 => 444,  1124 => 455,  1121 => 430,  1116 => 405,  1113 => 429,  1108 => 376,  1103 => 444,  1098 => 401,  1081 => 320,  1073 => 235,  1067 => 283,  1064 => 330,  1061 => 421,  1053 => 318,  1049 => 294,  1042 => 337,  1039 => 336,  1025 => 205,  1021 => 204,  1015 => 308,  1008 => 284,  996 => 406,  989 => 318,  985 => 395,  981 => 296,  977 => 313,  970 => 275,  966 => 274,  955 => 293,  952 => 464,  943 => 266,  936 => 353,  930 => 289,  919 => 241,  917 => 348,  908 => 236,  905 => 363,  896 => 280,  891 => 338,  877 => 334,  862 => 248,  857 => 263,  837 => 154,  832 => 260,  827 => 255,  821 => 266,  803 => 179,  778 => 191,  769 => 253,  765 => 201,  753 => 139,  746 => 182,  743 => 297,  735 => 168,  730 => 223,  720 => 189,  717 => 165,  712 => 218,  691 => 292,  678 => 257,  654 => 199,  587 => 155,  576 => 167,  539 => 181,  517 => 151,  471 => 136,  441 => 171,  437 => 125,  418 => 138,  386 => 107,  373 => 67,  304 => 96,  270 => 87,  265 => 97,  229 => 72,  477 => 137,  455 => 152,  448 => 173,  429 => 149,  407 => 137,  399 => 112,  389 => 105,  375 => 83,  358 => 62,  349 => 130,  335 => 41,  327 => 75,  298 => 106,  280 => 68,  249 => 79,  194 => 68,  142 => 47,  344 => 117,  318 => 57,  306 => 71,  295 => 105,  357 => 101,  300 => 98,  286 => 71,  276 => 95,  269 => 90,  254 => 118,  128 => 42,  237 => 81,  165 => 48,  122 => 26,  798 => 246,  770 => 279,  759 => 278,  748 => 270,  731 => 180,  721 => 205,  718 => 120,  708 => 185,  696 => 295,  617 => 258,  590 => 245,  553 => 145,  550 => 157,  540 => 146,  533 => 18,  500 => 144,  493 => 196,  489 => 202,  482 => 160,  467 => 157,  464 => 116,  458 => 153,  452 => 151,  449 => 150,  415 => 96,  382 => 125,  372 => 138,  361 => 169,  356 => 115,  339 => 114,  302 => 80,  285 => 99,  258 => 67,  123 => 26,  108 => 32,  424 => 164,  394 => 89,  380 => 133,  338 => 83,  319 => 103,  316 => 71,  312 => 104,  290 => 101,  267 => 86,  206 => 54,  110 => 36,  240 => 64,  224 => 33,  219 => 51,  217 => 56,  202 => 67,  186 => 59,  170 => 41,  100 => 22,  67 => 22,  14 => 1,  1096 => 345,  1090 => 339,  1088 => 397,  1085 => 353,  1066 => 233,  1034 => 210,  1031 => 290,  1018 => 303,  1013 => 326,  1007 => 408,  1002 => 193,  993 => 279,  986 => 212,  982 => 315,  976 => 399,  971 => 254,  964 => 307,  949 => 289,  946 => 288,  940 => 297,  937 => 245,  928 => 292,  926 => 318,  915 => 284,  912 => 82,  903 => 179,  898 => 233,  892 => 176,  889 => 277,  887 => 230,  884 => 79,  876 => 225,  874 => 193,  871 => 331,  863 => 345,  861 => 220,  858 => 247,  850 => 261,  843 => 270,  840 => 186,  815 => 204,  812 => 294,  808 => 323,  804 => 201,  799 => 198,  791 => 143,  785 => 141,  775 => 313,  771 => 237,  754 => 267,  728 => 167,  726 => 126,  723 => 221,  715 => 202,  711 => 174,  709 => 217,  706 => 183,  698 => 182,  694 => 196,  692 => 161,  689 => 291,  681 => 192,  677 => 167,  675 => 285,  663 => 279,  661 => 211,  650 => 248,  646 => 189,  629 => 154,  627 => 164,  625 => 266,  622 => 202,  598 => 198,  592 => 148,  586 => 17,  575 => 176,  566 => 216,  556 => 95,  554 => 11,  541 => 208,  536 => 207,  515 => 79,  511 => 142,  509 => 206,  488 => 164,  486 => 162,  483 => 183,  465 => 159,  463 => 134,  450 => 145,  432 => 98,  419 => 65,  371 => 129,  362 => 126,  353 => 122,  337 => 154,  333 => 77,  309 => 97,  303 => 70,  299 => 95,  291 => 89,  272 => 75,  261 => 135,  253 => 130,  239 => 82,  235 => 80,  213 => 68,  200 => 63,  198 => 69,  159 => 56,  149 => 52,  146 => 51,  131 => 43,  116 => 25,  79 => 15,  74 => 12,  71 => 22,  836 => 262,  817 => 243,  814 => 251,  811 => 250,  805 => 244,  787 => 257,  779 => 169,  776 => 281,  773 => 280,  761 => 234,  751 => 215,  747 => 228,  742 => 191,  739 => 211,  736 => 215,  724 => 206,  705 => 215,  702 => 214,  688 => 113,  680 => 107,  667 => 273,  662 => 176,  656 => 210,  649 => 272,  644 => 166,  641 => 188,  624 => 101,  613 => 151,  607 => 171,  597 => 18,  591 => 170,  584 => 178,  579 => 132,  563 => 14,  559 => 21,  551 => 135,  547 => 134,  537 => 160,  524 => 178,  512 => 171,  507 => 141,  504 => 149,  498 => 163,  485 => 138,  480 => 134,  472 => 158,  466 => 138,  460 => 183,  447 => 144,  442 => 102,  434 => 145,  428 => 121,  422 => 118,  404 => 155,  368 => 118,  364 => 118,  340 => 94,  334 => 107,  330 => 115,  325 => 105,  292 => 141,  287 => 51,  282 => 62,  279 => 91,  273 => 73,  266 => 86,  256 => 73,  252 => 80,  228 => 54,  218 => 53,  201 => 67,  64 => 21,  51 => 12,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 569,  2109 => 847,  2104 => 802,  2101 => 801,  2087 => 565,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 823,  2037 => 789,  2031 => 555,  2028 => 818,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 811,  2001 => 1668,  1996 => 771,  1994 => 549,  1987 => 766,  1983 => 547,  1979 => 764,  1972 => 761,  1969 => 544,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 534,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 528,  1923 => 779,  1916 => 736,  1912 => 735,  1908 => 522,  1901 => 521,  1897 => 730,  1894 => 729,  1889 => 769,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 511,  1835 => 509,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 500,  1801 => 695,  1798 => 694,  1795 => 498,  1789 => 814,  1776 => 489,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 481,  1750 => 668,  1745 => 667,  1742 => 476,  1739 => 665,  1736 => 708,  1730 => 705,  1720 => 688,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 672,  1678 => 644,  1671 => 450,  1666 => 640,  1663 => 447,  1660 => 763,  1657 => 659,  1651 => 656,  1641 => 631,  1637 => 440,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 651,  1592 => 614,  1587 => 613,  1584 => 635,  1581 => 731,  1576 => 608,  1574 => 633,  1567 => 603,  1563 => 410,  1559 => 601,  1552 => 628,  1548 => 597,  1542 => 593,  1529 => 605,  1525 => 614,  1516 => 708,  1512 => 583,  1503 => 599,  1490 => 388,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 384,  1447 => 569,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 374,  1406 => 532,  1393 => 436,  1388 => 537,  1380 => 430,  1376 => 367,  1371 => 366,  1367 => 527,  1364 => 518,  1361 => 523,  1355 => 543,  1342 => 360,  1337 => 510,  1328 => 356,  1324 => 550,  1317 => 511,  1313 => 499,  1310 => 526,  1304 => 504,  1291 => 479,  1286 => 406,  1279 => 486,  1274 => 468,  1270 => 483,  1266 => 401,  1262 => 490,  1257 => 500,  1254 => 286,  1250 => 394,  1246 => 345,  1239 => 389,  1235 => 343,  1232 => 416,  1226 => 383,  1213 => 273,  1208 => 481,  1201 => 443,  1196 => 335,  1192 => 490,  1188 => 456,  1184 => 263,  1179 => 448,  1176 => 384,  1172 => 383,  1168 => 451,  1164 => 450,  1160 => 324,  1153 => 561,  1149 => 373,  1146 => 444,  1137 => 249,  1123 => 442,  1119 => 354,  1115 => 313,  1111 => 377,  1107 => 242,  1102 => 344,  1099 => 347,  1095 => 307,  1091 => 321,  1082 => 289,  1079 => 352,  1076 => 351,  1070 => 350,  1057 => 313,  1052 => 220,  1045 => 338,  1040 => 213,  1036 => 283,  1032 => 333,  1028 => 312,  1023 => 288,  1020 => 311,  1016 => 285,  1012 => 390,  1005 => 263,  1001 => 304,  998 => 262,  992 => 319,  979 => 256,  974 => 255,  967 => 399,  962 => 397,  958 => 252,  954 => 302,  950 => 292,  945 => 298,  942 => 290,  938 => 375,  934 => 244,  927 => 183,  923 => 291,  920 => 369,  910 => 365,  901 => 234,  897 => 256,  890 => 271,  886 => 270,  883 => 353,  868 => 273,  856 => 163,  853 => 162,  849 => 264,  845 => 329,  841 => 249,  835 => 268,  830 => 333,  826 => 226,  822 => 281,  818 => 265,  813 => 215,  810 => 202,  806 => 261,  802 => 289,  795 => 241,  792 => 335,  789 => 249,  784 => 286,  782 => 284,  777 => 255,  772 => 190,  768 => 199,  763 => 327,  760 => 305,  756 => 192,  752 => 198,  745 => 212,  741 => 218,  738 => 190,  732 => 171,  719 => 288,  714 => 251,  710 => 264,  704 => 119,  699 => 183,  695 => 195,  690 => 194,  687 => 210,  683 => 156,  679 => 191,  672 => 255,  668 => 191,  665 => 190,  658 => 277,  645 => 103,  640 => 172,  634 => 206,  628 => 184,  623 => 202,  619 => 236,  611 => 166,  606 => 252,  603 => 199,  599 => 250,  595 => 169,  583 => 26,  580 => 25,  573 => 192,  560 => 231,  543 => 148,  538 => 10,  534 => 176,  530 => 215,  526 => 142,  521 => 144,  518 => 175,  514 => 140,  510 => 196,  503 => 141,  496 => 140,  490 => 129,  484 => 152,  474 => 148,  470 => 131,  446 => 129,  440 => 141,  436 => 100,  431 => 113,  425 => 120,  416 => 140,  412 => 136,  408 => 113,  403 => 138,  400 => 153,  396 => 132,  392 => 71,  385 => 131,  381 => 126,  367 => 128,  363 => 127,  359 => 124,  355 => 76,  350 => 112,  346 => 113,  343 => 108,  328 => 93,  324 => 59,  313 => 101,  307 => 100,  301 => 107,  288 => 94,  283 => 92,  271 => 58,  257 => 93,  251 => 89,  238 => 121,  233 => 61,  195 => 62,  191 => 45,  187 => 65,  183 => 53,  130 => 30,  88 => 18,  76 => 36,  115 => 37,  95 => 21,  655 => 104,  651 => 207,  648 => 171,  637 => 205,  633 => 265,  621 => 462,  618 => 241,  615 => 183,  604 => 162,  600 => 233,  588 => 180,  585 => 194,  582 => 153,  571 => 175,  567 => 190,  555 => 153,  552 => 184,  549 => 172,  544 => 179,  542 => 179,  535 => 146,  531 => 139,  519 => 16,  516 => 143,  513 => 207,  508 => 164,  506 => 131,  499 => 165,  495 => 186,  491 => 165,  481 => 161,  478 => 150,  475 => 188,  469 => 135,  456 => 134,  451 => 111,  443 => 128,  439 => 101,  427 => 142,  423 => 141,  420 => 145,  409 => 157,  405 => 133,  401 => 135,  391 => 149,  387 => 126,  384 => 103,  378 => 89,  365 => 97,  360 => 126,  348 => 79,  336 => 117,  332 => 79,  329 => 106,  323 => 113,  310 => 109,  305 => 90,  277 => 89,  274 => 99,  263 => 105,  259 => 67,  247 => 65,  244 => 85,  241 => 75,  222 => 57,  210 => 65,  207 => 66,  204 => 65,  184 => 65,  181 => 40,  167 => 50,  157 => 38,  96 => 41,  421 => 133,  417 => 118,  414 => 145,  406 => 113,  398 => 152,  393 => 131,  390 => 127,  376 => 123,  369 => 121,  366 => 171,  352 => 131,  345 => 109,  342 => 111,  331 => 150,  326 => 86,  320 => 112,  317 => 112,  314 => 111,  311 => 69,  308 => 109,  297 => 97,  293 => 95,  281 => 50,  278 => 59,  275 => 90,  264 => 64,  260 => 62,  248 => 90,  245 => 64,  242 => 63,  231 => 71,  227 => 73,  215 => 64,  212 => 72,  209 => 72,  197 => 50,  177 => 57,  171 => 45,  161 => 74,  132 => 36,  121 => 49,  105 => 23,  99 => 20,  81 => 18,  77 => 24,  180 => 60,  176 => 48,  156 => 58,  143 => 39,  139 => 32,  118 => 37,  189 => 48,  185 => 52,  173 => 42,  166 => 47,  152 => 47,  174 => 63,  164 => 60,  154 => 33,  150 => 41,  137 => 39,  133 => 33,  127 => 26,  107 => 33,  102 => 32,  83 => 25,  78 => 15,  53 => 9,  23 => 3,  42 => 9,  138 => 43,  134 => 51,  109 => 43,  103 => 22,  97 => 23,  94 => 26,  84 => 38,  75 => 16,  69 => 11,  66 => 30,  54 => 16,  44 => 7,  230 => 57,  226 => 71,  203 => 52,  193 => 61,  188 => 44,  182 => 60,  178 => 55,  168 => 45,  163 => 42,  160 => 49,  155 => 42,  148 => 41,  145 => 35,  140 => 38,  136 => 34,  125 => 28,  120 => 45,  113 => 56,  101 => 29,  92 => 28,  89 => 27,  85 => 17,  73 => 35,  62 => 15,  59 => 19,  56 => 12,  41 => 11,  126 => 34,  119 => 26,  111 => 44,  106 => 23,  98 => 21,  93 => 40,  86 => 49,  70 => 13,  60 => 19,  28 => 5,  36 => 4,  114 => 28,  104 => 42,  91 => 21,  80 => 22,  63 => 11,  58 => 13,  40 => 8,  34 => 3,  45 => 7,  61 => 27,  55 => 8,  48 => 15,  39 => 17,  35 => 7,  31 => 2,  26 => 3,  21 => 2,  46 => 7,  29 => 5,  57 => 13,  50 => 8,  47 => 7,  38 => 6,  33 => 9,  49 => 11,  32 => 3,  246 => 80,  236 => 74,  232 => 73,  225 => 53,  221 => 72,  216 => 51,  214 => 68,  211 => 63,  208 => 53,  205 => 61,  199 => 47,  196 => 66,  190 => 60,  179 => 64,  175 => 51,  172 => 46,  169 => 52,  162 => 40,  158 => 39,  153 => 48,  151 => 37,  147 => 46,  144 => 45,  141 => 44,  135 => 35,  129 => 35,  124 => 40,  117 => 29,  112 => 33,  90 => 17,  87 => 24,  82 => 15,  72 => 13,  68 => 12,  65 => 17,  52 => 9,  43 => 7,  37 => 10,  30 => 3,  27 => 2,  25 => 4,  24 => 2,  22 => 2,  19 => 1,);
    }
}
