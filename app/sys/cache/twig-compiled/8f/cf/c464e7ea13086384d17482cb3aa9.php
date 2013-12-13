<?php

/* AgentBundle:Task:task-list.html.twig */
class __TwigTemplate_8fcfc464e7ea13086384d17482cb3aa9 extends \Application\DeskPRO\Twig\Template
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
\t\tpageMeta.fragmentClass = 'DeskPRO.Agent.PageFragment.ListPane.TaskList';
\t\tpageMeta.search_type = '";
        // line 12
        if (isset($context["search_type"])) { $_search_type_ = $context["search_type"]; } else { $_search_type_ = null; }
        echo twig_escape_filter($this->env, $_search_type_, "html", null, true);
        echo "';
\t\tpageMeta.search_category = '";
        // line 13
        if (isset($context["search_category"])) { $_search_category_ = $context["search_category"]; } else { $_search_category_ = null; }
        echo twig_escape_filter($this->env, $_search_category_, "html", null, true);
        echo "';
\t\tpageMeta.url_fragment = '";
        // line 14
        if (isset($context["search_type"])) { $_search_type_ = $context["search_type"]; } else { $_search_type_ = null; }
        if (isset($context["search_category"])) { $_search_category_ = $context["search_category"]; } else { $_search_category_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->urlFragment("agent_task_list", array("search_type" => $_search_type_, "search_category" => $_search_category_)), "html", null, true);
        echo "';
\t\t";
        // line 15
        $context["baseId"] = $this->env->getExtension('deskpro_templating')->elUid();
        // line 16
        echo "\t\tpageMeta.baseId = '";
        if (isset($context["tplvars"])) { $_tplvars_ = $context["tplvars"]; } else { $_tplvars_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_tplvars_, "baseId"), "html", null, true);
        echo "';
\t</script>
";
    }

    // line 24
    public function block_pane_header($context, array $blocks = array())
    {
        // line 25
        echo "\t<h1><i class=\"icon-dp-task\"></i> ";
        if (isset($context["tasks"])) { $_tasks_ = $context["tasks"]; } else { $_tasks_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tasks.list_title", array("count" => twig_length_filter($this->env, $_tasks_)));
        echo "</h1>
";
    }

    // line 32
    public function block_pane_content($context, array $blocks = array())
    {
        // line 33
        echo "\t<div class=\"page-task-list\">
\t\t";
        // line 34
        $this->env->loadTemplate("AgentBundle:Task:task-list-bar.html.twig")->display($context);
        // line 35
        echo "\t\t";
        $context["has_any"] = false;
        // line 36
        echo "\t\t<div id=\"";
        if (isset($context["tplvars"])) { $_tplvars_ = $context["tplvars"]; } else { $_tplvars_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_tplvars_, "baseId"), "html", null, true);
        echo "_task_list\">
\t\t\t";
        // line 37
        if (isset($context["tasks_grouped"])) { $_tasks_grouped_ = $context["tasks_grouped"]; } else { $_tasks_grouped_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_tasks_grouped_);
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
        foreach ($context['_seq'] as $context["_key"] => $context["group"]) {
            // line 38
            echo "\t\t\t\t";
            if (isset($context["group"])) { $_group_ = $context["group"]; } else { $_group_ = null; }
            if (twig_length_filter($this->env, $this->getAttribute($_group_, "tasks"))) {
                // line 39
                echo "\t\t\t\t\t";
                $context["has_any"] = true;
                // line 40
                echo "\t\t\t\t\t<div class=\"task-group\">
\t\t\t\t\t\t<header>
\t\t\t\t\t\t\t<span class=\"expand-collapse\"></span>
\t\t\t\t\t\t\t<h4>";
                // line 43
                if (isset($context["group"])) { $_group_ = $context["group"]; } else { $_group_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_group_, "title"), "html", null, true);
                echo "</h4>
\t\t\t\t\t\t</header>
\t\t\t\t\t\t<section>
\t\t\t\t\t\t\t";
                // line 46
                if (isset($context["group"])) { $_group_ = $context["group"]; } else { $_group_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($_group_, "tasks"));
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
                foreach ($context['_seq'] as $context["_key"] => $context["task"]) {
                    // line 47
                    echo "\t\t\t\t\t\t\t\t";
                    if (isset($context["task"])) { $_task_ = $context["task"]; } else { $_task_ = null; }
                    $this->env->loadTemplate("AgentBundle:Task:task-list-row.html.twig")->display(array_merge($context, array("task" => $_task_)));
                    // line 48
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
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['task'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 49
                echo "\t\t\t\t\t\t</section>
\t\t\t\t\t</div>
\t\t\t\t";
            }
            // line 52
            echo "\t\t\t";
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
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['group'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 53
        echo "\t\t</div>

\t\t";
        // line 55
        if (isset($context["completed_tasks"])) { $_completed_tasks_ = $context["completed_tasks"]; } else { $_completed_tasks_ = null; }
        if (twig_length_filter($this->env, $_completed_tasks_)) {
            // line 56
            echo "\t\t<div id=\"";
            if (isset($context["tplvars"])) { $_tplvars_ = $context["tplvars"]; } else { $_tplvars_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_tplvars_, "baseId"), "html", null, true);
            echo "_task_list\">
\t\t\t<div class=\"task-group collapsed\">
\t\t\t\t<header>
\t\t\t\t\t<span class=\"expand-collapse\"></span>
\t\t\t\t\t<h4><span id=\"";
            // line 60
            if (isset($context["tplvars"])) { $_tplvars_ = $context["tplvars"]; } else { $_tplvars_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_tplvars_, "baseId"), "html", null, true);
            echo "_complete_count\">";
            if (isset($context["completed_tasks"])) { $_completed_tasks_ = $context["completed_tasks"]; } else { $_completed_tasks_ = null; }
            echo twig_escape_filter($this->env, twig_length_filter($this->env, $_completed_tasks_), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tasks.completed_tasks");
            echo "</h4>
\t\t\t\t</header>
\t\t\t\t<section>
\t\t\t\t\t";
            // line 63
            if (isset($context["completed_tasks"])) { $_completed_tasks_ = $context["completed_tasks"]; } else { $_completed_tasks_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_completed_tasks_);
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
            foreach ($context['_seq'] as $context["_key"] => $context["task"]) {
                // line 64
                echo "\t\t\t\t\t\t";
                if (isset($context["task"])) { $_task_ = $context["task"]; } else { $_task_ = null; }
                $this->env->loadTemplate("AgentBundle:Task:task-list-row.html.twig")->display(array_merge($context, array("task" => $_task_)));
                // line 65
                echo "\t\t\t\t\t";
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
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['task'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 66
            echo "\t\t\t\t</section>
\t\t\t</div>
\t\t</div>
\t\t";
        }
        // line 70
        echo "
\t\t";
        // line 71
        if (isset($context["has_any"])) { $_has_any_ = $context["has_any"]; } else { $_has_any_ = null; }
        if (isset($context["completed_tasks"])) { $_completed_tasks_ = $context["completed_tasks"]; } else { $_completed_tasks_ = null; }
        if (((!$_has_any_) && (!twig_length_filter($this->env, $_completed_tasks_)))) {
            // line 72
            echo "\t\t<section class=\"list-listing no-results\" id=\"";
            if (isset($context["tplvars"])) { $_tplvars_ = $context["tplvars"]; } else { $_tplvars_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_tplvars_, "baseId"), "html", null, true);
            echo "_no_results\" ";
            if (isset($context["ticket_ids"])) { $_ticket_ids_ = $context["ticket_ids"]; } else { $_ticket_ids_ = null; }
            if (twig_length_filter($this->env, $_ticket_ids_)) {
                echo "style=\"display:none\"";
            }
            echo ">
\t\t\t<article class=\"row-item no-results\">
\t\t\t\t<p style=\"padding: 15px;\">";
            // line 74
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.there_are_no_results");
            echo "</p>
\t\t\t</article>
\t\t</section>
\t\t";
        }
        // line 78
        echo "
\t\t<ul id=\"";
        // line 79
        if (isset($context["tplvars"])) { $_tplvars_ = $context["tplvars"]; } else { $_tplvars_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_tplvars_, "baseId"), "html", null, true);
        echo "_menu_vis\" style=\"display: none\">
\t\t\t<li data-vis=\"0\">";
        // line 80
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.private");
        echo "</li>
\t\t\t<li data-vis=\"1\">";
        // line 81
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.public");
        echo "</li>
\t\t</ul>

\t\t<div class=\"optionbox reply-agent-team-ob\" id=\"";
        // line 84
        if (isset($context["tplvars"])) { $_tplvars_ = $context["tplvars"]; } else { $_tplvars_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_tplvars_, "baseId"), "html", null, true);
        echo "_assign_ob\" style=\"display: none\">
\t\t\t<div class=\"col\">
\t\t\t\t<section data-section-name=\"agents\">
\t\t\t\t\t<header>
\t\t\t\t\t\t<h3>";
        // line 88
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.agents");
        echo "</h3>
\t\t\t\t\t\t<input type=\"text\" class=\"filter-box\" placeholder=\"";
        // line 89
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.filter");
        echo "\" />
\t\t\t\t\t</header>
\t\t\t\t\t<ul>
\t\t\t\t\t\t<li class=\"me last-me\">
\t\t\t\t\t\t\t<input type=\"radio\" name=\"";
        // line 93
        if (isset($context["tplvars"])) { $_tplvars_ = $context["tplvars"]; } else { $_tplvars_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_tplvars_, "baseId"), "html", null, true);
        echo "_asignedto\" value=\"";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "user"), "id"), "html", null, true);
        echo "\" />
\t\t\t\t\t\t\t<label><span class=\"agent-pic agent-label-";
        // line 94
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "user"), "id"), "html", null, true);
        echo "\" style=\"background-image: url(";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "user"), "getPictureUrl", array(0 => 16), "method"), "html", null, true);
        echo ")\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.me");
        echo "</span></label>
\t\t\t\t\t\t</li>

\t\t\t\t\t\t";
        // line 97
        if (isset($context["agents"])) { $_agents_ = $context["agents"]; } else { $_agents_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_agents_);
        foreach ($context['_seq'] as $context["_key"] => $context["agent"]) {
            // line 98
            echo "\t\t\t\t\t\t\t";
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if (($this->getAttribute($_agent_, "id") != $this->getAttribute($this->getAttribute($_app_, "user"), "id"))) {
                // line 99
                echo "\t\t\t\t\t\t\t\t<li>
\t\t\t\t\t\t\t\t\t<input type=\"radio\" name=\"";
                // line 100
                if (isset($context["tplvars"])) { $_tplvars_ = $context["tplvars"]; } else { $_tplvars_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_tplvars_, "baseId"), "html", null, true);
                echo "_asignedto\" value=\"";
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "id"), "html", null, true);
                echo "\" />
\t\t\t\t\t\t\t\t\t<label><span class=\"agent-pic agent-label-";
                // line 101
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "id"), "html", null, true);
                echo "\" style=\"background-image: url(";
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "getPictureUrl", array(0 => 16), "method"), "html", null, true);
                echo ")\">";
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "display_name"), "html", null, true);
                echo "</span></label>
\t\t\t\t\t\t\t\t\t<div class=\"bound-fade\"></div>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t";
            }
            // line 105
            echo "\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['agent'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 106
        echo "\t\t\t\t\t</ul>
\t\t\t\t</section>
\t\t\t</div>
\t\t\t";
        // line 109
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_agent_team"), "method")) {
            // line 110
            echo "\t\t\t<div class=\"col\">
\t\t\t\t<section data-section-name=\"teams\">
\t\t\t\t\t<header>
\t\t\t\t\t\t<h3>";
            // line 113
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.teams");
            echo "</h3>
\t\t\t\t\t\t<input type=\"text\" class=\"filter-box\" placeholder=\"";
            // line 114
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.filter");
            echo "\" />
\t\t\t\t\t</header>
\t\t\t\t\t<ul>
\t\t\t\t\t\t";
            // line 117
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($this->getAttribute($_app_, "user"), "getAgent", array(), "method"), "getTeams", array(), "method"));
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
            foreach ($context['_seq'] as $context["_key"] => $context["team"]) {
                // line 118
                echo "\t\t\t\t\t\t\t<li class=\"me ";
                if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
                if ($this->getAttribute($_loop_, "last")) {
                    echo "last-me";
                }
                echo "\">
\t\t\t\t\t\t\t\t<input type=\"radio\" name=\"";
                // line 119
                if (isset($context["tplvars"])) { $_tplvars_ = $context["tplvars"]; } else { $_tplvars_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_tplvars_, "baseId"), "html", null, true);
                echo "_asignedto\" value=\"";
                if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_team_, "id"), "html", null, true);
                echo "\" />
\t\t\t\t\t\t\t\t<label class=\"agent-team-label-";
                // line 120
                if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_team_, "id"), "html", null, true);
                echo "\">";
                if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_team_, "name"), "html", null, true);
                echo "</label>
\t\t\t\t\t\t\t\t<div class=\"bound-fade\"></div>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t";
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
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['team'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 124
            echo "
\t\t\t\t\t\t";
            // line 125
            if (isset($context["agent_teams"])) { $_agent_teams_ = $context["agent_teams"]; } else { $_agent_teams_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_agent_teams_);
            foreach ($context['_seq'] as $context["_key"] => $context["team"]) {
                // line 126
                echo "\t\t\t\t\t\t\t";
                if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                if (!twig_in_filter($this->getAttribute($_team_, "id"), $this->getAttribute($this->getAttribute($this->getAttribute($_app_, "user"), "getAgent", array(), "method"), "getTeamIds", array(), "method"))) {
                    // line 127
                    echo "\t\t\t\t\t\t\t\t<li>
\t\t\t\t\t\t\t\t\t<input type=\"radio\" name=\"";
                    // line 128
                    if (isset($context["tplvars"])) { $_tplvars_ = $context["tplvars"]; } else { $_tplvars_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_tplvars_, "baseId"), "html", null, true);
                    echo "_asignedto_team\" value=\"";
                    if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_team_, "id"), "html", null, true);
                    echo "\" />
\t\t\t\t\t\t\t\t\t<label class=\"agent-team-label-";
                    // line 129
                    if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_team_, "id"), "html", null, true);
                    echo "\">";
                    if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_team_, "name"), "html", null, true);
                    echo "</label>
\t\t\t\t\t\t\t\t\t<div class=\"bound-fade\"></div>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t";
                }
                // line 133
                echo "\t\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['team'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 134
            echo "\t\t\t\t\t</ul>
\t\t\t\t</section>
\t\t\t</div>
\t\t\t";
        }
        // line 138
        echo "\t\t\t<br class=\"clear\" />
\t\t</div>
\t</div>

";
    }

    public function getTemplateName()
    {
        return "AgentBundle:Task:task-list.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1399 => 568,  1387 => 560,  1358 => 555,  1334 => 553,  1316 => 529,  1298 => 520,  1195 => 478,  1148 => 464,  1120 => 453,  1117 => 452,  1093 => 440,  788 => 315,  612 => 203,  1394 => 446,  1385 => 442,  1382 => 441,  1369 => 433,  1363 => 432,  1360 => 431,  1350 => 429,  1305 => 523,  1300 => 409,  1281 => 404,  1277 => 403,  1265 => 398,  1255 => 396,  1247 => 393,  1234 => 387,  1202 => 375,  1199 => 374,  1187 => 372,  1162 => 365,  1136 => 461,  1128 => 352,  1122 => 350,  1069 => 332,  968 => 387,  846 => 250,  1183 => 449,  1132 => 460,  1097 => 341,  957 => 394,  907 => 277,  875 => 263,  653 => 236,  1329 => 405,  1309 => 401,  1303 => 399,  1297 => 398,  1292 => 397,  1280 => 394,  1249 => 495,  1237 => 390,  1205 => 409,  1200 => 408,  1194 => 407,  1178 => 380,  1175 => 447,  1170 => 375,  1135 => 362,  1105 => 428,  1078 => 335,  1068 => 325,  1048 => 417,  961 => 294,  922 => 280,  750 => 245,  842 => 263,  1038 => 319,  904 => 322,  882 => 301,  831 => 267,  860 => 314,  790 => 284,  733 => 296,  707 => 283,  744 => 220,  873 => 349,  824 => 266,  762 => 230,  713 => 235,  578 => 158,  2739 => 1153,  2735 => 1152,  2732 => 1151,  2725 => 1146,  2712 => 1144,  2707 => 1143,  2692 => 1131,  2688 => 1130,  2683 => 1127,  2680 => 1126,  2677 => 1125,  2671 => 1121,  2658 => 1119,  2653 => 1118,  2645 => 1113,  2641 => 1112,  2636 => 1109,  2632 => 1108,  2629 => 1107,  2626 => 1106,  2606 => 1089,  2602 => 1088,  2598 => 1087,  2594 => 1086,  2590 => 1085,  2586 => 1084,  2572 => 1073,  2568 => 1072,  2564 => 1071,  2560 => 1070,  2556 => 1069,  2552 => 1068,  2537 => 1056,  2533 => 1055,  2529 => 1054,  2525 => 1053,  2521 => 1052,  2503 => 1040,  2499 => 1039,  2495 => 1038,  2491 => 1037,  2487 => 1036,  2483 => 1035,  2469 => 1024,  2465 => 1023,  2461 => 1022,  2457 => 1021,  2453 => 1020,  2449 => 1019,  2426 => 999,  2422 => 998,  2418 => 997,  2414 => 996,  2410 => 995,  2406 => 994,  2392 => 983,  2388 => 982,  2384 => 981,  2380 => 980,  2376 => 979,  2372 => 978,  2358 => 967,  2354 => 966,  2350 => 965,  2346 => 964,  2342 => 963,  2338 => 962,  2324 => 951,  2320 => 950,  2316 => 949,  2312 => 948,  2308 => 947,  2304 => 946,  2295 => 939,  2282 => 937,  2277 => 936,  2269 => 931,  2265 => 930,  2260 => 927,  2256 => 926,  2253 => 925,  2230 => 901,  2220 => 899,  2216 => 898,  2211 => 896,  2208 => 895,  2198 => 893,  2194 => 892,  2189 => 890,  2184 => 889,  2177 => 885,  2173 => 884,  2164 => 877,  2151 => 875,  2146 => 874,  2143 => 873,  2141 => 864,  2134 => 860,  2130 => 859,  2122 => 853,  2120 => 852,  2113 => 848,  2105 => 846,  2096 => 839,  2083 => 837,  2078 => 836,  2073 => 834,  2067 => 830,  2062 => 828,  2058 => 827,  2054 => 826,  2050 => 825,  2045 => 824,  2038 => 822,  2034 => 821,  2022 => 814,  2009 => 812,  1999 => 809,  1993 => 805,  1988 => 803,  1984 => 802,  1980 => 801,  1976 => 800,  1971 => 799,  1968 => 798,  1964 => 797,  1960 => 796,  1954 => 793,  1946 => 788,  1942 => 787,  1938 => 786,  1932 => 782,  1927 => 780,  1919 => 778,  1915 => 777,  1911 => 776,  1906 => 775,  1903 => 774,  1899 => 773,  1895 => 772,  1886 => 768,  1883 => 764,  1871 => 759,  1866 => 756,  1863 => 755,  1842 => 752,  1825 => 749,  1818 => 748,  1815 => 747,  1800 => 743,  1793 => 741,  1772 => 729,  1761 => 721,  1757 => 720,  1751 => 717,  1740 => 709,  1719 => 697,  1715 => 696,  1686 => 673,  1676 => 669,  1665 => 661,  1661 => 660,  1645 => 651,  1638 => 649,  1630 => 645,  1625 => 642,  1622 => 641,  1598 => 637,  1577 => 634,  1558 => 629,  1549 => 627,  1546 => 626,  1535 => 618,  1531 => 617,  1514 => 606,  1510 => 605,  1504 => 602,  1493 => 594,  1483 => 590,  1464 => 580,  1458 => 577,  1443 => 568,  1437 => 565,  1426 => 557,  1405 => 545,  1401 => 544,  1395 => 541,  1390 => 538,  1381 => 533,  1373 => 531,  1348 => 521,  1343 => 520,  1335 => 515,  1321 => 510,  1318 => 549,  1299 => 503,  1294 => 407,  1282 => 496,  1269 => 506,  1260 => 397,  1240 => 478,  1221 => 484,  1216 => 378,  1210 => 410,  1206 => 473,  1193 => 467,  1189 => 466,  1155 => 468,  1150 => 447,  1022 => 312,  1006 => 404,  988 => 398,  969 => 392,  965 => 391,  921 => 370,  878 => 275,  866 => 349,  854 => 254,  819 => 279,  796 => 286,  1713 => 683,  1700 => 681,  1685 => 674,  1662 => 670,  1643 => 669,  1640 => 668,  1631 => 663,  1618 => 661,  1613 => 660,  1608 => 639,  1605 => 656,  1602 => 655,  1589 => 645,  1586 => 644,  1583 => 643,  1554 => 613,  1547 => 608,  1521 => 602,  1508 => 600,  1499 => 598,  1491 => 593,  1482 => 587,  1472 => 582,  1468 => 581,  1465 => 579,  1457 => 573,  1445 => 566,  1422 => 556,  1416 => 553,  1410 => 548,  1397 => 546,  1392 => 545,  1386 => 541,  1377 => 532,  1370 => 530,  1357 => 528,  1352 => 554,  1341 => 428,  1314 => 510,  1311 => 509,  1275 => 493,  1248 => 484,  1238 => 488,  1225 => 476,  1220 => 412,  1209 => 466,  1185 => 385,  1182 => 475,  1159 => 367,  1154 => 449,  1130 => 438,  1125 => 437,  1101 => 426,  1074 => 432,  1056 => 326,  1046 => 323,  1043 => 417,  1030 => 397,  1027 => 412,  947 => 361,  925 => 352,  913 => 324,  893 => 357,  881 => 267,  847 => 343,  829 => 336,  825 => 259,  1083 => 436,  995 => 399,  984 => 350,  963 => 292,  941 => 324,  851 => 367,  682 => 270,  1365 => 556,  1359 => 545,  1349 => 539,  1336 => 427,  1331 => 424,  1323 => 420,  1319 => 530,  1312 => 402,  1284 => 512,  1272 => 401,  1268 => 509,  1261 => 501,  1251 => 485,  1245 => 483,  1231 => 496,  1207 => 376,  1197 => 491,  1180 => 484,  1173 => 457,  1169 => 472,  1157 => 363,  1147 => 438,  1109 => 440,  1065 => 440,  1059 => 423,  1047 => 428,  1044 => 424,  1033 => 413,  1009 => 357,  991 => 351,  987 => 404,  973 => 294,  931 => 355,  924 => 282,  911 => 298,  906 => 81,  885 => 276,  872 => 317,  855 => 72,  749 => 264,  701 => 239,  594 => 180,  1163 => 470,  1143 => 440,  1087 => 420,  1077 => 433,  1051 => 325,  1037 => 480,  1010 => 405,  999 => 407,  932 => 326,  899 => 306,  895 => 404,  933 => 387,  914 => 133,  909 => 323,  833 => 284,  783 => 306,  755 => 303,  666 => 263,  453 => 168,  639 => 209,  568 => 176,  520 => 110,  657 => 216,  572 => 201,  609 => 202,  20 => 1,  659 => 217,  562 => 164,  548 => 185,  558 => 197,  479 => 145,  589 => 223,  457 => 169,  413 => 149,  953 => 290,  948 => 379,  935 => 394,  929 => 283,  916 => 382,  864 => 365,  844 => 338,  816 => 342,  807 => 259,  801 => 268,  774 => 257,  766 => 229,  737 => 297,  685 => 225,  664 => 225,  635 => 249,  593 => 209,  546 => 201,  532 => 223,  865 => 296,  852 => 241,  838 => 285,  820 => 201,  781 => 327,  764 => 252,  725 => 250,  632 => 268,  602 => 192,  565 => 165,  529 => 181,  505 => 156,  487 => 101,  473 => 87,  1853 => 842,  1849 => 753,  1839 => 751,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 733,  1778 => 732,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 693,  1705 => 781,  1688 => 675,  1681 => 769,  1677 => 768,  1667 => 671,  1656 => 762,  1648 => 655,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 606,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 593,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 540,  1379 => 557,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 522,  1289 => 406,  1276 => 604,  1263 => 601,  1244 => 492,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 373,  1181 => 561,  1171 => 446,  1158 => 550,  1114 => 428,  1104 => 465,  1072 => 333,  1062 => 7,  1024 => 395,  1014 => 406,  1000 => 328,  990 => 303,  980 => 393,  960 => 466,  918 => 280,  888 => 80,  834 => 268,  673 => 64,  636 => 198,  462 => 190,  454 => 138,  1144 => 463,  1139 => 356,  1131 => 399,  1127 => 434,  1110 => 347,  1092 => 459,  1089 => 426,  1086 => 520,  1084 => 337,  1063 => 387,  1060 => 425,  1055 => 423,  1050 => 373,  1035 => 372,  1019 => 330,  1003 => 401,  959 => 387,  900 => 366,  880 => 276,  870 => 277,  867 => 258,  859 => 294,  848 => 215,  839 => 376,  828 => 302,  823 => 374,  809 => 316,  800 => 241,  797 => 267,  794 => 257,  786 => 283,  740 => 78,  734 => 261,  703 => 228,  693 => 236,  630 => 247,  626 => 19,  614 => 275,  610 => 236,  581 => 206,  564 => 268,  525 => 195,  722 => 226,  697 => 282,  674 => 270,  671 => 221,  577 => 180,  569 => 222,  557 => 229,  502 => 187,  497 => 152,  445 => 163,  729 => 209,  684 => 237,  676 => 65,  669 => 268,  660 => 203,  647 => 212,  643 => 229,  601 => 306,  570 => 169,  522 => 156,  501 => 210,  296 => 108,  374 => 115,  631 => 207,  616 => 283,  608 => 194,  605 => 193,  596 => 188,  574 => 180,  561 => 231,  527 => 165,  433 => 158,  388 => 141,  426 => 172,  383 => 105,  461 => 184,  370 => 155,  395 => 166,  294 => 79,  223 => 63,  220 => 84,  492 => 129,  468 => 124,  444 => 142,  410 => 150,  397 => 110,  377 => 116,  262 => 90,  250 => 90,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 1051,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 680,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 638,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 572,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 526,  1301 => 1037,  1252 => 392,  1243 => 994,  1223 => 978,  1212 => 494,  1167 => 366,  1161 => 933,  1145 => 923,  1140 => 462,  1134 => 538,  1126 => 443,  1106 => 528,  1100 => 897,  1026 => 418,  997 => 822,  983 => 298,  975 => 296,  956 => 318,  939 => 285,  902 => 274,  894 => 364,  879 => 76,  757 => 269,  727 => 293,  716 => 287,  670 => 204,  528 => 187,  476 => 126,  435 => 177,  354 => 127,  341 => 54,  192 => 37,  321 => 89,  243 => 85,  793 => 266,  780 => 256,  758 => 229,  700 => 193,  686 => 238,  652 => 185,  638 => 226,  620 => 216,  545 => 162,  523 => 158,  494 => 151,  459 => 156,  438 => 119,  351 => 75,  347 => 111,  402 => 113,  268 => 71,  430 => 118,  411 => 117,  379 => 138,  322 => 67,  315 => 106,  289 => 130,  284 => 74,  255 => 92,  234 => 48,  1133 => 444,  1124 => 455,  1121 => 430,  1116 => 348,  1113 => 429,  1108 => 376,  1103 => 444,  1098 => 26,  1081 => 425,  1073 => 432,  1067 => 356,  1064 => 330,  1061 => 421,  1053 => 318,  1049 => 419,  1042 => 313,  1039 => 345,  1025 => 314,  1021 => 310,  1015 => 308,  1008 => 461,  996 => 406,  989 => 454,  985 => 395,  981 => 257,  977 => 321,  970 => 320,  966 => 375,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 148,  919 => 314,  917 => 279,  908 => 411,  905 => 363,  896 => 358,  891 => 378,  877 => 334,  862 => 274,  857 => 271,  837 => 261,  832 => 260,  827 => 349,  821 => 66,  803 => 179,  778 => 311,  769 => 253,  765 => 297,  753 => 54,  746 => 244,  743 => 297,  735 => 295,  730 => 251,  720 => 237,  717 => 362,  712 => 251,  691 => 233,  678 => 275,  654 => 199,  587 => 210,  576 => 171,  539 => 200,  517 => 210,  471 => 125,  441 => 162,  437 => 138,  418 => 126,  386 => 153,  373 => 109,  304 => 66,  270 => 92,  265 => 70,  229 => 45,  477 => 167,  455 => 125,  448 => 143,  429 => 141,  407 => 120,  399 => 145,  389 => 106,  375 => 141,  358 => 99,  349 => 123,  335 => 120,  327 => 98,  298 => 98,  280 => 100,  249 => 88,  194 => 48,  142 => 45,  344 => 94,  318 => 113,  306 => 102,  295 => 64,  357 => 136,  300 => 118,  286 => 105,  276 => 101,  269 => 97,  254 => 100,  128 => 40,  237 => 85,  165 => 49,  122 => 39,  798 => 319,  770 => 309,  759 => 278,  748 => 298,  731 => 294,  721 => 293,  718 => 301,  708 => 218,  696 => 236,  617 => 204,  590 => 207,  553 => 163,  550 => 157,  540 => 161,  533 => 182,  500 => 171,  493 => 178,  489 => 202,  482 => 198,  467 => 158,  464 => 170,  458 => 139,  452 => 145,  449 => 134,  415 => 152,  382 => 107,  372 => 137,  361 => 100,  356 => 141,  339 => 110,  302 => 104,  285 => 97,  258 => 76,  123 => 34,  108 => 16,  424 => 130,  394 => 109,  380 => 117,  338 => 135,  319 => 216,  316 => 124,  312 => 116,  290 => 106,  267 => 91,  206 => 41,  110 => 40,  240 => 86,  224 => 58,  219 => 73,  217 => 80,  202 => 56,  186 => 62,  170 => 28,  100 => 37,  67 => 12,  14 => 1,  1096 => 345,  1090 => 339,  1088 => 435,  1085 => 456,  1066 => 423,  1034 => 282,  1031 => 281,  1018 => 416,  1013 => 405,  1007 => 408,  1002 => 403,  993 => 305,  986 => 264,  982 => 401,  976 => 399,  971 => 295,  964 => 255,  949 => 289,  946 => 288,  940 => 388,  937 => 374,  928 => 371,  926 => 318,  915 => 279,  912 => 82,  903 => 231,  898 => 440,  892 => 303,  889 => 277,  887 => 302,  884 => 79,  876 => 75,  874 => 215,  871 => 331,  863 => 345,  861 => 270,  858 => 255,  850 => 291,  843 => 206,  840 => 406,  815 => 251,  812 => 343,  808 => 323,  804 => 258,  799 => 312,  791 => 310,  785 => 262,  775 => 255,  771 => 253,  754 => 267,  728 => 317,  726 => 72,  723 => 238,  715 => 105,  711 => 220,  709 => 222,  706 => 232,  698 => 229,  694 => 199,  692 => 189,  689 => 234,  681 => 224,  677 => 229,  675 => 234,  663 => 218,  661 => 263,  650 => 213,  646 => 231,  629 => 267,  627 => 218,  625 => 266,  622 => 242,  598 => 199,  592 => 212,  586 => 175,  575 => 218,  566 => 242,  556 => 219,  554 => 188,  541 => 176,  536 => 224,  515 => 138,  511 => 208,  509 => 134,  488 => 200,  486 => 174,  483 => 149,  465 => 191,  463 => 142,  450 => 182,  432 => 146,  419 => 143,  371 => 154,  362 => 129,  353 => 98,  337 => 124,  333 => 91,  309 => 84,  303 => 81,  299 => 80,  291 => 78,  272 => 72,  261 => 38,  253 => 55,  239 => 36,  235 => 94,  213 => 74,  200 => 52,  198 => 39,  159 => 31,  149 => 53,  146 => 51,  131 => 51,  116 => 38,  79 => 25,  74 => 13,  71 => 21,  836 => 262,  817 => 243,  814 => 319,  811 => 261,  805 => 244,  787 => 257,  779 => 169,  776 => 232,  773 => 322,  761 => 296,  751 => 302,  747 => 298,  742 => 243,  739 => 296,  736 => 215,  724 => 259,  705 => 69,  702 => 216,  688 => 226,  680 => 205,  667 => 273,  662 => 218,  656 => 215,  649 => 285,  644 => 220,  641 => 220,  624 => 206,  613 => 166,  607 => 200,  597 => 275,  591 => 49,  584 => 236,  579 => 181,  563 => 212,  559 => 68,  551 => 190,  547 => 188,  537 => 160,  524 => 112,  512 => 174,  507 => 237,  504 => 159,  498 => 181,  485 => 176,  480 => 198,  472 => 169,  466 => 165,  460 => 142,  447 => 163,  442 => 162,  434 => 133,  428 => 157,  422 => 145,  404 => 113,  368 => 136,  364 => 111,  340 => 69,  334 => 123,  330 => 48,  325 => 98,  292 => 97,  287 => 42,  282 => 41,  279 => 106,  273 => 98,  266 => 92,  256 => 56,  252 => 87,  228 => 90,  218 => 76,  201 => 40,  64 => 15,  51 => 13,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 847,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 823,  2037 => 789,  2031 => 788,  2028 => 818,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 811,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 779,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 769,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 744,  1801 => 695,  1798 => 694,  1795 => 742,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 708,  1730 => 705,  1720 => 688,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 672,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 659,  1651 => 656,  1641 => 631,  1637 => 667,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 651,  1592 => 614,  1587 => 613,  1584 => 635,  1581 => 731,  1576 => 608,  1574 => 633,  1567 => 603,  1563 => 630,  1559 => 601,  1552 => 628,  1548 => 597,  1542 => 593,  1529 => 605,  1525 => 614,  1516 => 708,  1512 => 583,  1503 => 599,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 569,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 564,  1388 => 537,  1380 => 539,  1376 => 436,  1371 => 645,  1367 => 527,  1364 => 518,  1361 => 523,  1355 => 543,  1342 => 624,  1337 => 510,  1328 => 552,  1324 => 550,  1317 => 511,  1313 => 499,  1310 => 526,  1304 => 504,  1291 => 502,  1286 => 405,  1279 => 486,  1274 => 508,  1270 => 483,  1266 => 487,  1262 => 490,  1257 => 500,  1254 => 482,  1250 => 394,  1246 => 477,  1239 => 389,  1235 => 498,  1232 => 416,  1226 => 383,  1213 => 466,  1208 => 481,  1201 => 472,  1196 => 464,  1192 => 490,  1188 => 456,  1184 => 485,  1179 => 448,  1176 => 461,  1172 => 367,  1168 => 451,  1164 => 450,  1160 => 469,  1153 => 561,  1149 => 359,  1146 => 444,  1137 => 249,  1123 => 442,  1119 => 354,  1115 => 424,  1111 => 377,  1107 => 346,  1102 => 344,  1099 => 347,  1095 => 419,  1091 => 418,  1082 => 455,  1079 => 524,  1076 => 359,  1070 => 431,  1057 => 491,  1052 => 406,  1045 => 484,  1040 => 416,  1036 => 283,  1032 => 415,  1028 => 312,  1023 => 409,  1020 => 311,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 304,  998 => 308,  992 => 406,  979 => 297,  974 => 400,  967 => 399,  962 => 397,  958 => 383,  954 => 293,  950 => 393,  945 => 391,  942 => 288,  938 => 375,  934 => 284,  927 => 282,  923 => 382,  920 => 369,  910 => 365,  901 => 340,  897 => 273,  890 => 271,  886 => 270,  883 => 353,  868 => 375,  856 => 293,  853 => 341,  849 => 264,  845 => 339,  841 => 249,  835 => 335,  830 => 333,  826 => 282,  822 => 281,  818 => 327,  813 => 242,  810 => 290,  806 => 270,  802 => 339,  795 => 241,  792 => 335,  789 => 233,  784 => 286,  782 => 237,  777 => 255,  772 => 289,  768 => 81,  763 => 327,  760 => 305,  756 => 248,  752 => 247,  745 => 245,  741 => 218,  738 => 241,  732 => 171,  719 => 288,  714 => 251,  710 => 284,  704 => 282,  699 => 279,  695 => 66,  690 => 226,  687 => 210,  683 => 271,  679 => 223,  672 => 179,  668 => 264,  665 => 229,  658 => 178,  645 => 253,  640 => 227,  634 => 206,  628 => 174,  623 => 217,  619 => 78,  611 => 282,  606 => 234,  603 => 231,  599 => 229,  595 => 213,  583 => 159,  580 => 173,  573 => 170,  560 => 267,  543 => 216,  538 => 164,  534 => 189,  530 => 213,  526 => 158,  521 => 287,  518 => 194,  514 => 193,  510 => 154,  503 => 133,  496 => 202,  490 => 149,  484 => 128,  474 => 174,  470 => 144,  446 => 120,  440 => 130,  436 => 176,  431 => 135,  425 => 156,  416 => 168,  412 => 117,  408 => 165,  403 => 161,  400 => 119,  396 => 144,  392 => 143,  385 => 118,  381 => 158,  367 => 112,  363 => 79,  359 => 132,  355 => 76,  350 => 143,  346 => 73,  343 => 140,  328 => 93,  324 => 89,  313 => 96,  307 => 111,  301 => 119,  288 => 152,  283 => 88,  271 => 107,  257 => 89,  251 => 100,  238 => 84,  233 => 82,  195 => 53,  191 => 64,  187 => 81,  183 => 51,  130 => 25,  88 => 23,  76 => 10,  115 => 39,  95 => 35,  655 => 202,  651 => 176,  648 => 215,  637 => 218,  633 => 197,  621 => 462,  618 => 241,  615 => 196,  604 => 201,  600 => 233,  588 => 206,  585 => 222,  582 => 225,  571 => 217,  567 => 194,  555 => 172,  552 => 171,  549 => 170,  544 => 230,  542 => 166,  535 => 199,  531 => 159,  519 => 155,  516 => 218,  513 => 154,  508 => 207,  506 => 188,  499 => 209,  495 => 150,  491 => 203,  481 => 127,  478 => 171,  475 => 170,  469 => 182,  456 => 138,  451 => 135,  443 => 179,  439 => 178,  427 => 155,  423 => 142,  420 => 141,  409 => 160,  405 => 218,  401 => 121,  391 => 138,  387 => 334,  384 => 138,  378 => 205,  365 => 153,  360 => 125,  348 => 97,  336 => 94,  332 => 118,  329 => 132,  323 => 129,  310 => 113,  305 => 43,  277 => 79,  274 => 94,  263 => 105,  259 => 66,  247 => 95,  244 => 93,  241 => 64,  222 => 60,  210 => 80,  207 => 83,  204 => 76,  184 => 76,  181 => 52,  167 => 57,  157 => 55,  96 => 25,  421 => 173,  417 => 150,  414 => 145,  406 => 114,  398 => 159,  393 => 120,  390 => 119,  376 => 149,  369 => 101,  366 => 145,  352 => 128,  345 => 106,  342 => 126,  331 => 122,  326 => 68,  320 => 88,  317 => 88,  314 => 86,  311 => 105,  308 => 121,  297 => 107,  293 => 90,  281 => 111,  278 => 93,  275 => 39,  264 => 92,  260 => 94,  248 => 54,  245 => 65,  242 => 84,  231 => 85,  227 => 88,  215 => 82,  212 => 73,  209 => 31,  197 => 67,  177 => 57,  171 => 58,  161 => 56,  132 => 36,  121 => 38,  105 => 19,  99 => 14,  81 => 14,  77 => 19,  180 => 58,  176 => 49,  156 => 30,  143 => 24,  139 => 44,  118 => 38,  189 => 63,  185 => 69,  173 => 69,  166 => 42,  152 => 47,  174 => 59,  164 => 34,  154 => 53,  150 => 29,  137 => 44,  133 => 43,  127 => 44,  107 => 32,  102 => 30,  83 => 28,  78 => 25,  53 => 13,  23 => 3,  42 => 8,  138 => 37,  134 => 47,  109 => 40,  103 => 37,  97 => 36,  94 => 35,  84 => 20,  75 => 24,  69 => 23,  66 => 16,  54 => 8,  44 => 10,  230 => 81,  226 => 80,  203 => 71,  193 => 72,  188 => 75,  182 => 68,  178 => 71,  168 => 63,  163 => 56,  160 => 54,  155 => 50,  148 => 48,  145 => 52,  140 => 46,  136 => 43,  125 => 39,  120 => 24,  113 => 45,  101 => 37,  92 => 34,  89 => 33,  85 => 32,  73 => 14,  62 => 14,  59 => 11,  56 => 15,  41 => 9,  126 => 51,  119 => 50,  111 => 39,  106 => 33,  98 => 39,  93 => 25,  86 => 32,  70 => 19,  60 => 16,  28 => 4,  36 => 6,  114 => 41,  104 => 36,  91 => 34,  80 => 21,  63 => 17,  58 => 14,  40 => 5,  34 => 3,  45 => 9,  61 => 11,  55 => 15,  48 => 12,  39 => 9,  35 => 6,  31 => 2,  26 => 3,  21 => 2,  46 => 13,  29 => 4,  57 => 13,  50 => 7,  47 => 12,  38 => 8,  33 => 5,  49 => 13,  32 => 3,  246 => 89,  236 => 93,  232 => 91,  225 => 43,  221 => 87,  216 => 85,  214 => 82,  211 => 60,  208 => 72,  205 => 83,  199 => 55,  196 => 77,  190 => 71,  179 => 58,  175 => 55,  172 => 44,  169 => 35,  162 => 48,  158 => 47,  153 => 54,  151 => 59,  147 => 46,  144 => 50,  141 => 28,  135 => 36,  129 => 45,  124 => 45,  117 => 32,  112 => 22,  90 => 33,  87 => 36,  82 => 32,  72 => 25,  68 => 21,  65 => 21,  52 => 13,  43 => 6,  37 => 4,  30 => 3,  27 => 4,  25 => 2,  24 => 4,  22 => 2,  19 => 1,);
    }
}
