<?php

/* ReportBundle:AgentActivity:index.html.twig */
class __TwigTemplate_e968788f04df065738b155db2d49dc8c extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("ReportBundle::layout.html.twig");

        $this->blocks = array(
            'nav_block' => array($this, 'block_nav_block'),
            'pagebar' => array($this, 'block_pagebar'),
            'page' => array($this, 'block_page'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "ReportBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        $context["this_page"] = "agent_activity_index";
        // line 5
        ob_start();
        if (isset($context["team_id"])) { $_team_id_ = $context["team_id"]; } else { $_team_id_ = null; }
        if (isset($context["agent_id"])) { $_agent_id_ = $context["agent_id"]; } else { $_agent_id_ = null; }
        if ($_team_id_) {
            echo "team-";
            if (isset($context["team_id"])) { $_team_id_ = $context["team_id"]; } else { $_team_id_ = null; }
            echo twig_escape_filter($this->env, $_team_id_, "html", null, true);
        } elseif ($_agent_id_) {
            if (isset($context["agent_id"])) { $_agent_id_ = $context["agent_id"]; } else { $_agent_id_ = null; }
            echo twig_escape_filter($this->env, $_agent_id_, "html", null, true);
        } else {
            echo "0";
        }
        $context["url_agent_or_team_id"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_nav_block($context, array $blocks = array())
    {
    }

    // line 6
    public function block_pagebar($context, array $blocks = array())
    {
        // line 7
        echo "<ul>
\t<li>
\t\tShowing Agent Activity for:
\t\t<form style=\"display: inline\">
\t\t\t<select id=\"agent_selector\">
\t\t\t\t<option ";
        // line 12
        if (isset($context["agent_or_team_id"])) { $_agent_or_team_id_ = $context["agent_or_team_id"]; } else { $_agent_or_team_id_ = null; }
        if (($_agent_or_team_id_ == "0")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"";
        if (isset($context["view_date"])) { $_view_date_ = $context["view_date"]; } else { $_view_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_agent_activity_list", array("agent_or_team_id" => 0, "date" => $this->env->getExtension('deskpro_templating')->userDate($context, $_view_date_, "Y-m-d"))), "html", null, true);
        echo "\">All Agents</option>
\t\t\t\t";
        // line 13
        if (isset($context["all_agents"])) { $_all_agents_ = $context["all_agents"]; } else { $_all_agents_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_all_agents_);
        foreach ($context['_seq'] as $context["_key"] => $context["agent"]) {
            // line 14
            echo "\t\t\t\t\t<option ";
            if (isset($context["agent_or_team_id"])) { $_agent_or_team_id_ = $context["agent_or_team_id"]; } else { $_agent_or_team_id_ = null; }
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            if (($_agent_or_team_id_ == ("" . $this->getAttribute($_agent_, "id")))) {
                echo "selected=\"selected\"";
            }
            echo " value=\"";
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            if (isset($context["view_date"])) { $_view_date_ = $context["view_date"]; } else { $_view_date_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_agent_activity_list", array("agent_or_team_id" => $this->getAttribute($_agent_, "id"), "date" => $this->env->getExtension('deskpro_templating')->userDate($context, $_view_date_, "Y-m-d"))), "html", null, true);
            echo "\">
\t\t\t\t\t\t";
            // line 15
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "name"), "html", null, true);
            echo "
\t\t\t\t\t</option>
\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['agent'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 18
        echo "\t\t\t\t";
        if (isset($context["agent_teams"])) { $_agent_teams_ = $context["agent_teams"]; } else { $_agent_teams_ = null; }
        if (twig_length_filter($this->env, $_agent_teams_)) {
            // line 19
            echo "\t\t\t\t\t<optgroup label=\"Teams\">
\t\t\t\t\t";
            // line 20
            if (isset($context["agent_teams"])) { $_agent_teams_ = $context["agent_teams"]; } else { $_agent_teams_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_agent_teams_);
            foreach ($context['_seq'] as $context["_key"] => $context["team"]) {
                // line 21
                echo "\t\t\t\t\t\t<option value=\"";
                if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                if (isset($context["view_date"])) { $_view_date_ = $context["view_date"]; } else { $_view_date_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_agent_activity_list", array("agent_or_team_id" => ("team-" . $this->getAttribute($_team_, "id")), "date" => $this->env->getExtension('deskpro_templating')->userDate($context, $_view_date_, "Y-m-d"))), "html", null, true);
                echo "\" ";
                if (isset($context["agent_or_team_id"])) { $_agent_or_team_id_ = $context["agent_or_team_id"]; } else { $_agent_or_team_id_ = null; }
                if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                if (($_agent_or_team_id_ == ("team-" . $this->getAttribute($_team_, "id")))) {
                    echo "selected=\"selected\"";
                }
                echo ">";
                if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_team_, "name"), "html", null, true);
                echo "</option>
\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['team'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 23
            echo "\t\t\t\t\t</optgroup>
\t\t\t\t";
        }
        // line 25
        echo "\t\t\t</select>
\t\t</form>
\t\ton
\t\t<span class=\"dp-nav-btn\"><a href=\"";
        // line 28
        if (isset($context["url_agent_or_team_id"])) { $_url_agent_or_team_id_ = $context["url_agent_or_team_id"]; } else { $_url_agent_or_team_id_ = null; }
        if (isset($context["view_prev_date"])) { $_view_prev_date_ = $context["view_prev_date"]; } else { $_view_prev_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_agent_activity_list", array("agent_or_team_id" => $_url_agent_or_team_id_, "date" => $this->env->getExtension('deskpro_templating')->userDate($context, $_view_prev_date_, "Y-m-d"))), "html", null, true);
        echo "\">&lt;</a></span>
\t\t<span class=\"dp-nav-btn datepicker\"><a>";
        // line 29
        if (isset($context["view_date"])) { $_view_date_ = $context["view_date"]; } else { $_view_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_view_date_, "jS F Y"), "html", null, true);
        echo "</a><em class=\"drop\"></em><span class=\"hidden\"></span></span>
\t\t";
        // line 30
        if (isset($context["view_next_date"])) { $_view_next_date_ = $context["view_next_date"]; } else { $_view_next_date_ = null; }
        if ($_view_next_date_) {
            // line 31
            echo "\t\t\t<span class=\"dp-nav-btn\"><a href=\"";
            if (isset($context["url_agent_or_team_id"])) { $_url_agent_or_team_id_ = $context["url_agent_or_team_id"]; } else { $_url_agent_or_team_id_ = null; }
            if (isset($context["view_next_date"])) { $_view_next_date_ = $context["view_next_date"]; } else { $_view_next_date_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_agent_activity_list", array("agent_or_team_id" => $_url_agent_or_team_id_, "date" => $this->env->getExtension('deskpro_templating')->userDate($context, $_view_next_date_, "Y-m-d"))), "html", null, true);
            echo "\">&gt;</a></span>
\t\t";
        }
        // line 33
        echo "\t</li>
</ul>
";
    }

    // line 36
    public function block_page($context, array $blocks = array())
    {
        // line 37
        echo "
<style type=\"text/css\">
#dp_admin_pagebar {
\toverflow: visible;
}

#dp_admin_pagebar .datepicker > a > em {
\theight: 6px;
\tmargin-left: 8px;
}

#dp_admin_pagebar .datepicker > span {
\tfont-size: 100%;
\tline-height: 100%;
\ttext-shadow: none;
}

.reports_agent_activity_section .activity_table {
    border-width: 1px;
    border-style: solid;
    border-color: #CCCCCC;
}

.hour_cell {
    text-align: center;
}

.activity_type_ticket .type_icon {
    background-image: url(";
        // line 65
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("images/agent/tabs/tabtype-ticket.png"), "html", null, true);
        echo ");
}

.activity_type_chat .type_icon {
    background-image: url(";
        // line 69
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("images/agent/tabs/tabtype-chat.png"), "html", null, true);
        echo ");
}

.activity_type_download .type_icon {
    background-image: url(";
        // line 73
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("images/agent/tabs/tabtype-download.png"), "html", null, true);
        echo ");
}

.activity_type_news .type_icon {
    background-image: url(";
        // line 77
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("images/agent/tabs/tabtype-news.png"), "html", null, true);
        echo ");
}

.activity_type_feedback .type_icon {
    background-image: url(";
        // line 81
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("images/agent/tabs/tabtype-feedback.png"), "html", null, true);
        echo ");
}

.activity_type_article .type_icon {
    background-image: url(";
        // line 85
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("images/agent/tabs/tabtype-content.png"), "html", null, true);
        echo ");
}

.activity .type_icon {
    width: 15px;
    height: 14px;
    display: inline-block;
    background-repeat: no-repeat;
    background-position: 50% 50%;
\tposition: relative;
\ttop: 3px;
}

.activity {
    vertical-align: middle;
}
</style>
<script type=\"text/javascript\">
    \$(document).ready(function(){
        \$('.datepicker > span').datepicker(
                {
                    'onSelect': function(dateText) {
\t\t\t\t\t\tdocument.location = \"";
        // line 107
        if (isset($context["url_agent_or_team_id"])) { $_url_agent_or_team_id_ = $context["url_agent_or_team_id"]; } else { $_url_agent_or_team_id_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_agent_activity_list", array("agent_or_team_id" => $_url_agent_or_team_id_, "date" => "")), "html", null, true);
        echo "/\" + dateText;
\t\t\t\t\t},
                    'dateFormat': 'yy-mm-dd',
                    'defaultDate': new Date(";
        // line 110
        if (isset($context["view_date"])) { $_view_date_ = $context["view_date"]; } else { $_view_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_view_date_, "Y"), "html", null, true);
        echo ", ";
        if (isset($context["view_date"])) { $_view_date_ = $context["view_date"]; } else { $_view_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_view_date_, "m"), "html", null, true);
        echo " -1, ";
        if (isset($context["view_date"])) { $_view_date_ = $context["view_date"]; } else { $_view_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_view_date_, "d"), "html", null, true);
        echo "),
                    'maxDate': new Date(";
        // line 111
        if (isset($context["today"])) { $_today_ = $context["today"]; } else { $_today_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_today_, "Y"), "html", null, true);
        echo ", ";
        if (isset($context["today"])) { $_today_ = $context["today"]; } else { $_today_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_today_, "m"), "html", null, true);
        echo " -1, ";
        if (isset($context["today"])) { $_today_ = $context["today"]; } else { $_today_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_today_, "d"), "html", null, true);
        echo ")
                }
        );
        \$('.datepicker').on('click', function(){
            if(\$('.datepicker > span').hasClass('hidden')) {
                \$('.datepicker > span').removeClass('hidden');
                \$('.reports-popup-page-overlay').removeClass('hidden');
            }
            else {
                \$('.datepicker > span').addClass('hidden');
                \$('.reports-popup-page-overlay').addClass('hidden');
            }
        });
        \$('.reports-popup-page-overlay').on('click', function() {
            \$('.datepicker > a').click();
        });
        \$('.activity_table > tbody > tr:odd').css('background-color', '#EEEEEE');
        \$('#agent_selector').on('change', function() {
            document.location = \$('#agent_selector').val();
        })
    });

</script>
<div class=\"reports_agent_activity_section\">
    ";
        // line 135
        if (isset($context["agents"])) { $_agents_ = $context["agents"]; } else { $_agents_ = null; }
        if (twig_length_filter($this->env, $_agents_)) {
            // line 136
            echo "    <table class=\"activity_table\">
        <thead>
            <tr>
                <th>Hour</th>
                ";
            // line 140
            if (isset($context["agents"])) { $_agents_ = $context["agents"]; } else { $_agents_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_agents_);
            foreach ($context['_seq'] as $context["_key"] => $context["agent"]) {
                // line 141
                echo "                <th>";
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "name"), "html", null, true);
                echo "</th>
                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['agent'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 143
            echo "            </tr>
        </thead>
        <tbody>
        ";
            // line 146
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable(range(0, 23));
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
            foreach ($context['_seq'] as $context["_key"] => $context["hour"]) {
                // line 147
                echo "            <tr>
                <td class=\"hour_cell\">";
                // line 148
                if (isset($context["hour"])) { $_hour_ = $context["hour"]; } else { $_hour_ = null; }
                echo twig_escape_filter($this->env, sprintf("%02d", $_hour_), "html", null, true);
                echo ":00</td>
                ";
                // line 149
                if (isset($context["agents"])) { $_agents_ = $context["agents"]; } else { $_agents_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($_agents_);
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
                foreach ($context['_seq'] as $context["_key"] => $context["agent"]) {
                    // line 150
                    echo "                <td>
                    ";
                    // line 151
                    if (isset($context["hour"])) { $_hour_ = $context["hour"]; } else { $_hour_ = null; }
                    $context["hour_index"] = ("_" . $_hour_);
                    // line 152
                    echo "                    ";
                    if (isset($context["activity"])) { $_activity_ = $context["activity"]; } else { $_activity_ = null; }
                    if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                    if (isset($context["hour_index"])) { $_hour_index_ = $context["hour_index"]; } else { $_hour_index_ = null; }
                    if ($this->getAttribute($this->getAttribute($_activity_, $this->getAttribute($_agent_, "id"), array(), "array", false, true), $_hour_index_, array(), "array", true, true)) {
                        // line 153
                        echo "                        ";
                        if (isset($context["activity"])) { $_activity_ = $context["activity"]; } else { $_activity_ = null; }
                        if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                        if (isset($context["hour_index"])) { $_hour_index_ = $context["hour_index"]; } else { $_hour_index_ = null; }
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_activity_, $this->getAttribute($_agent_, "id"), array(), "array"), $_hour_index_, array(), "array"));
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
                        foreach ($context['_seq'] as $context["minute"] => $context["logs"]) {
                            // line 154
                            echo "                            ";
                            if (isset($context["minute"])) { $_minute_ = $context["minute"]; } else { $_minute_ = null; }
                            $context["minute_display"] = sprintf("%02d", $_minute_);
                            // line 155
                            echo "                            ";
                            if (isset($context["logs"])) { $_logs_ = $context["logs"]; } else { $_logs_ = null; }
                            $context['_parent'] = (array) $context;
                            $context['_seq'] = twig_ensure_traversable($_logs_);
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
                            foreach ($context['_seq'] as $context["_key"] => $context["row"]) {
                                // line 156
                                echo "                                ";
                                if (isset($context["row"])) { $_row_ = $context["row"]; } else { $_row_ = null; }
                                $context["type"] = $this->getAttribute($_row_, "type");
                                // line 157
                                echo "                                ";
                                if (isset($context["row"])) { $_row_ = $context["row"]; } else { $_row_ = null; }
                                $context["log"] = $this->getAttribute($_row_, "data");
                                // line 158
                                echo "                                ";
                                if (isset($context["type"])) { $_type_ = $context["type"]; } else { $_type_ = null; }
                                if (($_type_ == "ticket")) {
                                    // line 159
                                    echo "                                    ";
                                    ob_start();
                                    // line 160
                                    echo "                                    ";
                                    $this->env->loadTemplate("ReportBundle:AgentActivity:ticket-log-actiontext.html.twig")->display($context);
                                    // line 161
                                    echo "                                    ";
                                    $context["action_text"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
                                    // line 162
                                    echo "                                    ";
                                    if (isset($context["action_text"])) { $_action_text_ = $context["action_text"]; } else { $_action_text_ = null; }
                                    if ((!twig_test_empty($this->env->getExtension('deskpro_templating')->strTrim($_action_text_)))) {
                                        // line 163
                                        echo "                                    <div class=\"activity activity_type_";
                                        if (isset($context["type"])) { $_type_ = $context["type"]; } else { $_type_ = null; }
                                        echo twig_escape_filter($this->env, $_type_, "html", null, true);
                                        echo "\"><div class=\"type_icon\"></div>
                                    (";
                                        // line 164
                                        if (isset($context["minute_display"])) { $_minute_display_ = $context["minute_display"]; } else { $_minute_display_ = null; }
                                        echo twig_escape_filter($this->env, $_minute_display_, "html", null, true);
                                        echo ") ";
                                        if (isset($context["action_text"])) { $_action_text_ = $context["action_text"]; } else { $_action_text_ = null; }
                                        echo twig_escape_filter($this->env, $_action_text_, "html", null, true);
                                        echo " in
                                    <a href=\"";
                                        // line 165
                                        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
                                        echo "/agent/#app.tickets,t:";
                                        if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "ticket"), "id"), "html", null, true);
                                        echo "\">";
                                        if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "ticket"), "subject"), "html", null, true);
                                        echo "</a>
                                    </div>
                                    ";
                                    }
                                    // line 168
                                    echo "                                ";
                                } else {
                                    // line 169
                                    echo "                                    <div class=\"activity activity_type_";
                                    if (isset($context["type"])) { $_type_ = $context["type"]; } else { $_type_ = null; }
                                    echo twig_escape_filter($this->env, $_type_, "html", null, true);
                                    echo "\"><div class=\"type_icon\"></div>
                                    ";
                                    // line 170
                                    if (isset($context["type"])) { $_type_ = $context["type"]; } else { $_type_ = null; }
                                    if (($_type_ == "chat")) {
                                        // line 171
                                        echo "                                        (";
                                        if (isset($context["minute_display"])) { $_minute_display_ = $context["minute_display"]; } else { $_minute_display_ = null; }
                                        echo twig_escape_filter($this->env, $_minute_display_, "html", null, true);
                                        echo ") Sent ";
                                        if (isset($context["row"])) { $_row_ = $context["row"]; } else { $_row_ = null; }
                                        echo twig_escape_filter($this->env, $this->getAttribute($_row_, "count"), "html", null, true);
                                        echo " messages in chat
                                        #<a href=\"";
                                        // line 172
                                        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
                                        echo "/agent/#app.userchat,c:";
                                        if (isset($context["row"])) { $_row_ = $context["row"]; } else { $_row_ = null; }
                                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_row_, "conversation"), "id"), "html", null, true);
                                        echo "\">";
                                        if (isset($context["row"])) { $_row_ = $context["row"]; } else { $_row_ = null; }
                                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_row_, "conversation"), "id"), "html", null, true);
                                        echo "</a>
                                    ";
                                    } elseif (($_type_ == "news")) {
                                        // line 174
                                        echo "                                        (";
                                        if (isset($context["minute_display"])) { $_minute_display_ = $context["minute_display"]; } else { $_minute_display_ = null; }
                                        echo twig_escape_filter($this->env, $_minute_display_, "html", null, true);
                                        echo ") Created news revision in
                                        <a href=\"";
                                        // line 175
                                        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
                                        echo "/agent/#app.publish,n:";
                                        if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                                        echo twig_escape_filter($this->env, $this->getAttribute($_log_, "id"), "html", null, true);
                                        echo "\">";
                                        if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                                        echo twig_escape_filter($this->env, $this->getAttribute($_log_, "title"), "html", null, true);
                                        echo "</a>
                                    ";
                                    } elseif (($_type_ == "article")) {
                                        // line 177
                                        echo "                                        (";
                                        if (isset($context["minute_display"])) { $_minute_display_ = $context["minute_display"]; } else { $_minute_display_ = null; }
                                        echo twig_escape_filter($this->env, $_minute_display_, "html", null, true);
                                        echo ") Created article revision in
                                        <a href=\"";
                                        // line 178
                                        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
                                        echo "/agent/#app.publish,a:";
                                        if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                                        echo twig_escape_filter($this->env, $this->getAttribute($_log_, "id"), "html", null, true);
                                        echo "\">";
                                        if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                                        echo twig_escape_filter($this->env, $this->getAttribute($_log_, "title"), "html", null, true);
                                        echo "</a>
                                    ";
                                    } elseif (($_type_ == "download")) {
                                        // line 180
                                        echo "                                        (";
                                        if (isset($context["minute_display"])) { $_minute_display_ = $context["minute_display"]; } else { $_minute_display_ = null; }
                                        echo twig_escape_filter($this->env, $_minute_display_, "html", null, true);
                                        echo ") Created download revision in
                                        <a href=\"";
                                        // line 181
                                        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
                                        echo "/agent/#app.publish,d:";
                                        if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                                        echo twig_escape_filter($this->env, $this->getAttribute($_log_, "id"), "html", null, true);
                                        echo "\">";
                                        if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                                        echo twig_escape_filter($this->env, $this->getAttribute($_log_, "title"), "html", null, true);
                                        echo "</a>
                                    ";
                                    } elseif (($_type_ == "feedback")) {
                                        // line 183
                                        echo "                                        (";
                                        if (isset($context["minute_display"])) { $_minute_display_ = $context["minute_display"]; } else { $_minute_display_ = null; }
                                        echo twig_escape_filter($this->env, $_minute_display_, "html", null, true);
                                        echo ") Created feedback revision in
                                        <a href=\"";
                                        // line 184
                                        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
                                        echo "/agent/#app.feedback,a:";
                                        if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                                        echo twig_escape_filter($this->env, $this->getAttribute($_log_, "id"), "html", null, true);
                                        echo "\">";
                                        if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                                        echo twig_escape_filter($this->env, $this->getAttribute($_log_, "title"), "html", null, true);
                                        echo "</a>
                                    ";
                                    }
                                    // line 186
                                    echo "                                    </div>
                                ";
                                }
                                // line 188
                                echo "                            ";
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
                            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['row'], $context['_parent'], $context['loop']);
                            $context = array_merge($_parent, array_intersect_key($context, $_parent));
                            // line 189
                            echo "                        ";
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
                        unset($context['_seq'], $context['_iterated'], $context['minute'], $context['logs'], $context['_parent'], $context['loop']);
                        $context = array_merge($_parent, array_intersect_key($context, $_parent));
                        // line 190
                        echo "                    ";
                    }
                    // line 191
                    echo "                </td>
                ";
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
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['agent'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 193
                echo "            </tr>
        ";
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
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['hour'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 195
            echo "        </tbody>
    </table>
    ";
        } else {
            // line 198
            echo "        <p>No activity found for this date.</p>
    ";
        }
        // line 200
        echo "    <div class=\"reports-popup-page-overlay hidden\"></div>
</div>
";
    }

    public function getTemplateName()
    {
        return "ReportBundle:AgentActivity:index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  673 => 200,  669 => 198,  664 => 195,  649 => 193,  634 => 191,  631 => 190,  617 => 189,  603 => 188,  599 => 186,  587 => 184,  581 => 183,  569 => 181,  563 => 180,  551 => 178,  545 => 177,  533 => 175,  527 => 174,  515 => 172,  506 => 171,  503 => 170,  497 => 169,  494 => 168,  481 => 165,  473 => 164,  467 => 163,  463 => 162,  460 => 161,  457 => 160,  454 => 159,  450 => 158,  446 => 157,  442 => 156,  423 => 155,  419 => 154,  398 => 153,  392 => 152,  389 => 151,  386 => 150,  368 => 149,  363 => 148,  360 => 147,  343 => 146,  338 => 143,  328 => 141,  323 => 140,  317 => 136,  314 => 135,  280 => 111,  269 => 110,  262 => 107,  237 => 85,  230 => 81,  223 => 77,  216 => 73,  209 => 69,  202 => 65,  172 => 37,  169 => 36,  163 => 33,  155 => 31,  152 => 30,  147 => 29,  141 => 28,  136 => 25,  132 => 23,  112 => 21,  107 => 20,  104 => 19,  100 => 18,  90 => 15,  77 => 14,  72 => 13,  62 => 12,  55 => 7,  52 => 6,  47 => 3,  29 => 5,  27 => 1,);
    }
}
