<?php

/* AgentBundle:Feedback:window-section.html.twig */
class __TwigTemplate_6d0f4f81f84db25dedb3c6156e051728 extends \Application\DeskPRO\Twig\Template
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
        echo "\t<li class=\"tab active\" data-tab-id=\"pane-content-main\"><i class=\"icon-dp-feedback\"></i> ";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.chrome.nav_feedback");
        echo "</li>
";
    }

    // line 6
    public function block_pane_content($context, array $blocks = array())
    {
        // line 7
        echo "\t<div class=\"pane-content pane-content-main\">

\t\t";
        // line 9
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "user"), "hasPerm", array(0 => "agent_publish.validate"), "method")) {
            // line 10
            echo "\t\t\t<section class=\"pane-section\">
\t\t\t\t<header>
\t\t\t\t\t<h1>";
            // line 12
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.pending");
            echo "</h1>
\t\t\t\t</header>
\t\t\t\t<article>
\t\t\t\t\t<ul class=\"nav-list\">
\t\t\t\t\t\t<li class=\"is-nav-item\">
\t\t\t\t\t\t\t<div class=\"item\" data-route=\"listpane:";
            // line 17
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_feedback_validatingcontent"), "html", null, true);
            echo "\">
\t\t\t\t\t\t\t\t<h3>";
            // line 18
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.feedback.feedback_to_validate");
            echo "</h3>
\t\t\t\t\t\t\t\t<em class=\"counter list-counter\" id=\"feedback_validating_count\">";
            // line 19
            if (isset($context["counts"])) { $_counts_ = $context["counts"]; } else { $_counts_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($_counts_, "feedback_awaiting_validation", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_counts_, "feedback_awaiting_validation"), 0)) : (0)), "html", null, true);
            echo "</em>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t<li class=\"is-nav-item\">
\t\t\t\t\t\t\t<div class=\"item\" data-route=\"listpane:";
            // line 23
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_feedback_validatingcomments"), "html", null, true);
            echo "\">
\t\t\t\t\t\t\t\t<h3>";
            // line 24
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.comments_to_validate");
            echo "</h3>
\t\t\t\t\t\t\t\t<em class=\"counter list-counter\" id=\"feedback_comments_validating_count\">";
            // line 25
            if (isset($context["counts"])) { $_counts_ = $context["counts"]; } else { $_counts_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($_counts_, "comments_awaiting_validation", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_counts_, "comments_awaiting_validation"), 0)) : (0)), "html", null, true);
            echo "</em>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</li>
\t\t\t\t\t</ul>
\t\t\t\t</article>
\t\t\t</section>

\t\t\t<section class=\"pane-section with-tabs last\">
\t\t\t\t<nav class=\"pane-section-tabs\" id=\"feedback_outline_tabstrip\">
\t\t\t\t\t<ul>
\t\t\t\t\t\t<li style=\"width:33%;\" data-tab-for=\"#feedback_outline_status\"><span>";
            // line 35
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.status");
            echo "</span></li>
\t\t\t\t\t\t<li style=\"width:33%;\" data-tab-for=\"#feedback_outline_category\"><span>";
            // line 36
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.type");
            echo "</span></li>
\t\t\t\t\t\t<li style=\"width:33%;\" data-tab-for=\"#feedback_outline_labels\"><span>";
            // line 37
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.labels");
            echo "</span></li>
\t\t\t\t\t</ul>
\t\t\t\t</nav>

\t\t\t\t<section id=\"feedback_outline_status\" class=\"deskpro-tab-item\">
\t\t\t\t\t<ul class=\"nav-list nav-list-small\">
\t\t\t\t\t\t<li class=\"is-nav-item\">
\t\t\t\t\t\t\t<div class=\"item\" data-route=\"listpane:";
            // line 44
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_feedback_status", array("status" => "new")), "html", null, true);
            echo "\">
\t\t\t\t\t\t\t\t<h3>";
            // line 45
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.feedback.status_new");
            echo "</h3>
\t\t\t\t\t\t\t\t<em class=\"counter list-counter\" id=\"feedback_status_new_count\">";
            // line 46
            if (isset($context["status_counts"])) { $_status_counts_ = $context["status_counts"]; } else { $_status_counts_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($_status_counts_, "new", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_status_counts_, "new"), 0)) : (0)), "html", null, true);
            echo "</em>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t<li class=\"is-nav-item\">
\t\t\t\t\t\t\t<div class=\"item\" data-route=\"listpane:";
            // line 50
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_feedback_status", array("status" => "active")), "html", null, true);
            echo "\">
\t\t\t\t\t\t\t\t<h3>";
            // line 51
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.feedback.status_active");
            echo "</h3>
\t\t\t\t\t\t\t\t<em class=\"counter list-counter\" id=\"feedback_status_active_0_count\">";
            // line 52
            if (isset($context["status_counts"])) { $_status_counts_ = $context["status_counts"]; } else { $_status_counts_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_status_counts_, "active", array(), "any", false, true), 0, array(), "array", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_status_counts_, "active", array(), "any", false, true), 0, array(), "array"), 0)) : (0)), "html", null, true);
            echo "</em>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t<ul class=\"nav-list-small indented\">
\t\t\t\t\t\t\t\t";
            // line 55
            if (isset($context["active_status_cats"])) { $_active_status_cats_ = $context["active_status_cats"]; } else { $_active_status_cats_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_active_status_cats_);
            foreach ($context['_seq'] as $context["_key"] => $context["cat"]) {
                // line 56
                echo "\t\t\t\t\t\t\t\t\t<li class=\"is-nav-item\">
\t\t\t\t\t\t\t\t\t\t<div class=\"item\" data-route=\"listpane:";
                // line 57
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_feedback_status", array("status" => $this->getAttribute($_cat_, "id"))), "html", null, true);
                echo "\">
\t\t\t\t\t\t\t\t\t\t\t<h3>";
                // line 58
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
                echo "</h3>
\t\t\t\t\t\t\t\t\t\t\t<em class=\"counter list-counter\" id=\"feedback_status_active_";
                // line 59
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "id"), "html", null, true);
                echo "_count\">";
                if (isset($context["status_counts"])) { $_status_counts_ = $context["status_counts"]; } else { $_status_counts_ = null; }
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_status_counts_, "active", array(), "any", false, true), $this->getAttribute($_cat_, "id"), array(), "array", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_status_counts_, "active", array(), "any", false, true), $this->getAttribute($_cat_, "id"), array(), "array"), 0)) : (0)), "html", null, true);
                echo "</em>
\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['cat'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 63
            echo "\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t<li class=\"is-nav-item\">
\t\t\t\t\t\t\t<div class=\"item\" data-route=\"listpane:";
            // line 66
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_feedback_status", array("status" => "closed")), "html", null, true);
            echo "\">
\t\t\t\t\t\t\t\t<h3>";
            // line 67
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_closed");
            echo "</h3>
\t\t\t\t\t\t\t\t<em class=\"counter list-counter\" id=\"feedback_status_closed_0_count\">";
            // line 68
            if (isset($context["status_counts"])) { $_status_counts_ = $context["status_counts"]; } else { $_status_counts_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_status_counts_, "closed", array(), "any", false, true), 0, array(), "array", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_status_counts_, "closed", array(), "any", false, true), 0, array(), "array"), 0)) : (0)), "html", null, true);
            echo "</em>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t<ul class=\"nav-list-small indented\">
\t\t\t\t\t\t\t\t";
            // line 71
            if (isset($context["closed_status_cats"])) { $_closed_status_cats_ = $context["closed_status_cats"]; } else { $_closed_status_cats_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_closed_status_cats_);
            foreach ($context['_seq'] as $context["_key"] => $context["cat"]) {
                // line 72
                echo "\t\t\t\t\t\t\t\t\t<li class=\"is-nav-item\">
\t\t\t\t\t\t\t\t\t\t<div class=\"item\" data-route=\"listpane:";
                // line 73
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_feedback_status", array("status" => $this->getAttribute($_cat_, "id"))), "html", null, true);
                echo "\">
\t\t\t\t\t\t\t\t\t\t\t<h3>";
                // line 74
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
                echo "</h3>
\t\t\t\t\t\t\t\t\t\t\t<em class=\"counter list-counter\" id=\"feedback_status_closed_";
                // line 75
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "id"), "html", null, true);
                echo "_count\">";
                if (isset($context["status_counts"])) { $_status_counts_ = $context["status_counts"]; } else { $_status_counts_ = null; }
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_status_counts_, "closed", array(), "any", false, true), $this->getAttribute($_cat_, "id"), array(), "array", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_status_counts_, "closed", array(), "any", false, true), $this->getAttribute($_cat_, "id"), array(), "array"), 0)) : (0)), "html", null, true);
                echo "</em>
\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['cat'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 79
            echo "\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t<li class=\"is-nav-item\">
\t\t\t\t\t\t\t<div class=\"item\" data-route=\"listpane:";
            // line 82
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_feedback_status", array("status" => "hidden")), "html", null, true);
            echo "\">
\t\t\t\t\t\t\t\t<h3>";
            // line 83
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.hidden");
            echo "</h3>
\t\t\t\t\t\t\t\t<em class=\"counter list-counter\" id=\"feedback_status_hidden_hidden_count\">";
            // line 84
            if (isset($context["status_counts"])) { $_status_counts_ = $context["status_counts"]; } else { $_status_counts_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_status_counts_, "hidden", array(), "any", false, true), "hidden", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_status_counts_, "hidden", array(), "any", false, true), "hidden"), 0)) : (0)), "html", null, true);
            echo "</em>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t<ul class=\"nav-list-small indented\">
\t\t\t\t\t\t\t\t<li class=\"is-nav-item\">
\t\t\t\t\t\t\t\t\t<div class=\"item\" data-route=\"listpane:";
            // line 88
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_feedback_status", array("status" => "hidden.deleted")), "html", null, true);
            echo "\">
\t\t\t\t\t\t\t\t\t\t<h3>";
            // line 89
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.deleted");
            echo "</h3>
\t\t\t\t\t\t\t\t\t\t<em class=\"counter list-counter\" id=\"feedback_status_hidden_deleted_count\">";
            // line 90
            if (isset($context["status_counts"])) { $_status_counts_ = $context["status_counts"]; } else { $_status_counts_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_status_counts_, "hidden", array(), "any", false, true), "deleted", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_status_counts_, "hidden", array(), "any", false, true), "deleted"), 0)) : (0)), "html", null, true);
            echo "</em>
\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t<li class=\"is-nav-item\">
\t\t\t\t\t\t\t\t\t<div class=\"item\" data-route=\"listpane:";
            // line 94
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_feedback_status", array("status" => "hidden.spam")), "html", null, true);
            echo "\">
\t\t\t\t\t\t\t\t\t\t<h3>";
            // line 95
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.spam");
            echo "</h3>
\t\t\t\t\t\t\t\t\t\t<em class=\"counter list-counter\" id=\"feedback_status_hidden_spam_count\">";
            // line 96
            if (isset($context["status_counts"])) { $_status_counts_ = $context["status_counts"]; } else { $_status_counts_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_status_counts_, "hidden", array(), "any", false, true), "spam", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_status_counts_, "hidden", array(), "any", false, true), "spam"), 0)) : (0)), "html", null, true);
            echo "</em>
\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t</li>
\t\t\t\t\t</ul>
\t\t\t\t</section>
\t\t\t\t<section id=\"feedback_outline_category\" class=\"deskpro-tab-item\" data-tab-trigger=\"#feedback_outline_tabstrip li.categroies\">
\t\t\t\t\t<ul class=\"nav-list nav-list-small\">
\t\t\t\t\t\t";
            // line 105
            if (isset($context["feedback_cats"])) { $_feedback_cats_ = $context["feedback_cats"]; } else { $_feedback_cats_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_feedback_cats_);
            foreach ($context['_seq'] as $context["_key"] => $context["cat"]) {
                // line 106
                echo "\t\t\t\t\t\t\t<li
\t\t\t\t\t\t\t\tclass=\"is-nav-item\"
\t\t\t\t\t\t\t\tdata-usergroup-ids=\"";
                // line 108
                if (isset($context["feedback_cats_usergroups"])) { $_feedback_cats_usergroups_ = $context["feedback_cats_usergroups"]; } else { $_feedback_cats_usergroups_ = null; }
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->implodeArray((($this->getAttribute($_feedback_cats_usergroups_, $this->getAttribute($_cat_, "id"), array(), "array", true, true)) ? (_twig_default_filter($this->getAttribute($_feedback_cats_usergroups_, $this->getAttribute($_cat_, "id"), array(), "array"), array())) : (array())), ","), "html", null, true);
                echo "\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t\t\t<div class=\"item\" ";
                // line 110
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                if ($this->getAttribute($_cat_, "depth")) {
                    echo "style=\"padding-left: ";
                    if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                    echo twig_escape_filter($this->env, (12 + ((10 * $this->getAttribute($_cat_, "depth")) - 1)), "html", null, true);
                    echo "px\"";
                }
                echo " data-route=\"listpane:";
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_feedback_category", array("category_id" => $this->getAttribute($_cat_, "id"))), "html", null, true);
                echo "\">
\t\t\t\t\t\t\t\t\t<h3>";
                // line 111
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
                echo "</h3>
\t\t\t\t\t\t\t\t\t<em class=\"counter list-counter\" id=\"feedback_cat_";
                // line 112
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "id"), "html", null, true);
                echo "_count\">";
                if (isset($context["category_counts"])) { $_category_counts_ = $context["category_counts"]; } else { $_category_counts_ = null; }
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                echo twig_escape_filter($this->env, (($this->getAttribute($_category_counts_, $this->getAttribute($_cat_, "id"), array(), "array", true, true)) ? (_twig_default_filter($this->getAttribute($_category_counts_, $this->getAttribute($_cat_, "id"), array(), "array"), 0)) : (0)), "html", null, true);
                echo "</em>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['cat'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 116
            echo "\t\t\t\t\t</ul>
\t\t\t\t</section>
\t\t\t\t<section id=\"feedback_outline_labels\" class=\"deskpro-tab-item\" data-tab-trigger=\"#feedback_outline_tabstrip li.labels\">
\t\t\t\t\t<section class=\"labels-list\" style=\"border-top:none;\">
\t\t\t\t\t\t";
            // line 120
            if (isset($context["feedback_tag_index"])) { $_feedback_tag_index_ = $context["feedback_tag_index"]; } else { $_feedback_tag_index_ = null; }
            if (twig_length_filter($this->env, $_feedback_tag_index_)) {
                // line 121
                echo "\t\t\t\t\t\t\t<table>
\t\t\t\t\t\t\t\t";
                // line 122
                if (isset($context["feedback_tag_index"])) { $_feedback_tag_index_ = $context["feedback_tag_index"]; } else { $_feedback_tag_index_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($_feedback_tag_index_);
                foreach ($context['_seq'] as $context["letter"] => $context["labels"]) {
                    // line 123
                    echo "\t\t\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t\t\t<td class=\"letter\">";
                    // line 124
                    if (isset($context["letter"])) { $_letter_ = $context["letter"]; } else { $_letter_ = null; }
                    echo twig_escape_filter($this->env, $_letter_, "html", null, true);
                    echo "</td>
\t\t\t\t\t\t\t\t\t\t<td class=\"labels\">
\t\t\t\t\t\t\t\t\t\t\t<ul>
\t\t\t\t\t\t\t\t\t\t\t\t";
                    // line 127
                    if (isset($context["labels"])) { $_labels_ = $context["labels"]; } else { $_labels_ = null; }
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($_labels_);
                    foreach ($context['_seq'] as $context["_key"] => $context["label"]) {
                        // line 128
                        echo "\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"is-nav-item\"><span data-route=\"listpane:";
                        if (isset($context["label"])) { $_label_ = $context["label"]; } else { $_label_ = null; }
                        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_feedback_label", array("label" => $_label_)), "html", null, true);
                        echo "\">";
                        if (isset($context["label"])) { $_label_ = $context["label"]; } else { $_label_ = null; }
                        echo twig_escape_filter($this->env, $_label_, "html", null, true);
                        echo "</span></li>
\t\t\t\t\t\t\t\t\t\t\t\t";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['label'], $context['_parent'], $context['loop']);
                    $context = array_merge($_parent, array_intersect_key($context, $_parent));
                    // line 130
                    echo "\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['letter'], $context['labels'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 134
                echo "\t\t\t\t\t\t\t</table>
\t\t\t\t\t\t";
            } else {
                // line 136
                echo "\t\t\t\t\t\t<aside class=\"source-info-explain\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.feedback.no_labels_explain");
                echo "</aside>
\t\t\t\t\t\t";
            }
            // line 138
            echo "\t\t\t\t\t</section>
\t\t\t\t</section>

\t\t\t</section>
\t\t";
        }
        // line 143
        echo "\t</div>

\t<div class=\"pane-content pane-content-search\" style=\"display: none;\">
\t\t";
        // line 146
        $this->env->loadTemplate("AgentBundle:Feedback:window-search.html.twig")->display($context);
        // line 147
        echo "\t</div>
";
    }

    public function getTemplateName()
    {
        return "AgentBundle:Feedback:window-section.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  750 => 221,  842 => 337,  1038 => 364,  904 => 322,  882 => 318,  831 => 303,  860 => 314,  790 => 284,  733 => 210,  707 => 206,  744 => 220,  873 => 74,  824 => 256,  762 => 271,  713 => 248,  578 => 158,  2739 => 1153,  2735 => 1152,  2732 => 1151,  2725 => 1146,  2712 => 1144,  2707 => 1143,  2692 => 1131,  2688 => 1130,  2683 => 1127,  2680 => 1126,  2677 => 1125,  2671 => 1121,  2658 => 1119,  2653 => 1118,  2645 => 1113,  2641 => 1112,  2636 => 1109,  2632 => 1108,  2629 => 1107,  2626 => 1106,  2606 => 1089,  2602 => 1088,  2598 => 1087,  2594 => 1086,  2590 => 1085,  2586 => 1084,  2572 => 1073,  2568 => 1072,  2564 => 1071,  2560 => 1070,  2556 => 1069,  2552 => 1068,  2537 => 1056,  2533 => 1055,  2529 => 1054,  2525 => 1053,  2521 => 1052,  2503 => 1040,  2499 => 1039,  2495 => 1038,  2491 => 1037,  2487 => 1036,  2483 => 1035,  2469 => 1024,  2465 => 1023,  2461 => 1022,  2457 => 1021,  2453 => 1020,  2449 => 1019,  2426 => 999,  2422 => 998,  2418 => 997,  2414 => 996,  2410 => 995,  2406 => 994,  2392 => 983,  2388 => 982,  2384 => 981,  2380 => 980,  2376 => 979,  2372 => 978,  2358 => 967,  2354 => 966,  2350 => 965,  2346 => 964,  2342 => 963,  2338 => 962,  2324 => 951,  2320 => 950,  2316 => 949,  2312 => 948,  2308 => 947,  2304 => 946,  2295 => 939,  2282 => 937,  2277 => 936,  2269 => 931,  2265 => 930,  2260 => 927,  2256 => 926,  2253 => 925,  2230 => 901,  2220 => 899,  2216 => 898,  2211 => 896,  2208 => 895,  2198 => 893,  2194 => 892,  2189 => 890,  2184 => 889,  2177 => 885,  2173 => 884,  2164 => 877,  2151 => 875,  2146 => 874,  2143 => 873,  2141 => 864,  2134 => 860,  2130 => 859,  2122 => 853,  2120 => 852,  2113 => 848,  2105 => 846,  2096 => 839,  2083 => 837,  2078 => 836,  2073 => 834,  2067 => 830,  2062 => 828,  2058 => 827,  2054 => 826,  2050 => 825,  2045 => 824,  2038 => 822,  2034 => 821,  2022 => 814,  2009 => 812,  1999 => 809,  1993 => 805,  1988 => 803,  1984 => 802,  1980 => 801,  1976 => 800,  1971 => 799,  1968 => 798,  1964 => 797,  1960 => 796,  1954 => 793,  1946 => 788,  1942 => 787,  1938 => 786,  1932 => 782,  1927 => 780,  1919 => 778,  1915 => 777,  1911 => 776,  1906 => 775,  1903 => 774,  1899 => 773,  1895 => 772,  1886 => 768,  1883 => 764,  1871 => 759,  1866 => 756,  1863 => 755,  1842 => 752,  1825 => 749,  1818 => 748,  1815 => 747,  1800 => 743,  1793 => 741,  1772 => 729,  1761 => 721,  1757 => 720,  1751 => 717,  1740 => 709,  1719 => 697,  1715 => 696,  1686 => 673,  1676 => 669,  1665 => 661,  1661 => 660,  1645 => 651,  1638 => 649,  1630 => 645,  1625 => 642,  1622 => 641,  1598 => 637,  1577 => 634,  1558 => 629,  1549 => 627,  1546 => 626,  1535 => 618,  1531 => 617,  1514 => 606,  1510 => 605,  1504 => 602,  1493 => 594,  1483 => 590,  1464 => 580,  1458 => 577,  1443 => 568,  1437 => 565,  1426 => 557,  1405 => 545,  1401 => 544,  1395 => 541,  1390 => 538,  1381 => 533,  1373 => 531,  1348 => 521,  1343 => 520,  1335 => 515,  1321 => 510,  1318 => 509,  1299 => 503,  1294 => 502,  1282 => 496,  1269 => 491,  1260 => 483,  1240 => 478,  1221 => 476,  1216 => 475,  1210 => 474,  1206 => 473,  1193 => 467,  1189 => 466,  1155 => 450,  1150 => 447,  1022 => 411,  1006 => 404,  988 => 398,  969 => 392,  965 => 391,  921 => 370,  878 => 355,  866 => 349,  854 => 346,  819 => 293,  796 => 286,  1713 => 683,  1700 => 681,  1685 => 674,  1662 => 670,  1643 => 669,  1640 => 668,  1631 => 663,  1618 => 661,  1613 => 660,  1608 => 639,  1605 => 656,  1602 => 655,  1589 => 645,  1586 => 644,  1583 => 643,  1554 => 613,  1547 => 608,  1521 => 602,  1508 => 600,  1499 => 598,  1491 => 593,  1482 => 587,  1472 => 582,  1468 => 581,  1465 => 579,  1457 => 573,  1445 => 566,  1422 => 556,  1416 => 553,  1410 => 548,  1397 => 546,  1392 => 545,  1386 => 541,  1377 => 532,  1370 => 530,  1357 => 528,  1352 => 527,  1341 => 518,  1314 => 510,  1311 => 509,  1275 => 493,  1248 => 484,  1238 => 478,  1225 => 476,  1220 => 475,  1209 => 466,  1185 => 459,  1182 => 463,  1159 => 450,  1154 => 449,  1130 => 438,  1125 => 437,  1101 => 426,  1074 => 432,  1056 => 407,  1046 => 418,  1043 => 404,  1030 => 397,  1027 => 412,  947 => 361,  925 => 352,  913 => 324,  893 => 338,  881 => 335,  847 => 343,  829 => 336,  825 => 304,  1083 => 434,  995 => 383,  984 => 350,  963 => 319,  941 => 375,  851 => 271,  682 => 270,  1365 => 549,  1359 => 545,  1349 => 539,  1336 => 537,  1331 => 514,  1323 => 515,  1319 => 530,  1312 => 505,  1284 => 519,  1272 => 492,  1268 => 509,  1261 => 506,  1251 => 485,  1245 => 483,  1231 => 496,  1207 => 493,  1197 => 491,  1180 => 484,  1173 => 457,  1169 => 455,  1157 => 475,  1147 => 446,  1109 => 440,  1065 => 440,  1059 => 423,  1047 => 428,  1044 => 424,  1033 => 420,  1009 => 357,  991 => 351,  987 => 404,  973 => 395,  931 => 355,  924 => 371,  911 => 298,  906 => 81,  885 => 336,  872 => 317,  855 => 72,  749 => 264,  701 => 239,  594 => 109,  1163 => 454,  1143 => 440,  1087 => 420,  1077 => 433,  1051 => 430,  1037 => 480,  1010 => 405,  999 => 384,  932 => 326,  899 => 405,  895 => 404,  933 => 84,  914 => 133,  909 => 323,  833 => 238,  783 => 306,  755 => 224,  666 => 263,  453 => 187,  639 => 249,  568 => 191,  520 => 110,  657 => 260,  572 => 186,  609 => 17,  20 => 1,  659 => 207,  562 => 185,  548 => 165,  558 => 184,  479 => 157,  589 => 7,  457 => 145,  413 => 111,  953 => 430,  948 => 403,  935 => 394,  929 => 372,  916 => 382,  864 => 365,  844 => 338,  816 => 342,  807 => 234,  801 => 338,  774 => 280,  766 => 229,  737 => 49,  685 => 186,  664 => 225,  635 => 281,  593 => 231,  546 => 118,  532 => 68,  865 => 221,  852 => 241,  838 => 304,  820 => 201,  781 => 327,  764 => 274,  725 => 46,  632 => 245,  602 => 167,  565 => 154,  529 => 62,  505 => 267,  487 => 101,  473 => 87,  1853 => 842,  1849 => 753,  1839 => 751,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 733,  1778 => 732,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 693,  1705 => 781,  1688 => 675,  1681 => 769,  1677 => 768,  1667 => 671,  1656 => 762,  1648 => 655,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 606,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 593,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 540,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 522,  1289 => 520,  1276 => 604,  1263 => 601,  1244 => 480,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 463,  1181 => 561,  1171 => 557,  1158 => 550,  1114 => 428,  1104 => 465,  1072 => 512,  1062 => 7,  1024 => 395,  1014 => 406,  1000 => 328,  990 => 382,  980 => 474,  960 => 466,  918 => 348,  888 => 80,  834 => 307,  673 => 64,  636 => 185,  462 => 92,  454 => 253,  1144 => 542,  1139 => 399,  1131 => 399,  1127 => 397,  1110 => 396,  1092 => 459,  1089 => 390,  1086 => 520,  1084 => 419,  1063 => 387,  1060 => 386,  1055 => 422,  1050 => 373,  1035 => 372,  1019 => 330,  1003 => 263,  959 => 387,  900 => 366,  880 => 276,  870 => 350,  867 => 353,  859 => 362,  848 => 215,  839 => 376,  828 => 302,  823 => 374,  809 => 316,  800 => 241,  797 => 62,  794 => 280,  786 => 283,  740 => 78,  734 => 261,  703 => 246,  693 => 236,  630 => 278,  626 => 19,  614 => 275,  610 => 236,  581 => 197,  564 => 220,  525 => 61,  722 => 251,  697 => 282,  674 => 268,  671 => 266,  577 => 257,  569 => 222,  557 => 229,  502 => 99,  497 => 159,  445 => 85,  729 => 209,  684 => 237,  676 => 65,  669 => 254,  660 => 262,  647 => 198,  643 => 251,  601 => 306,  570 => 165,  522 => 165,  501 => 164,  296 => 114,  374 => 205,  631 => 265,  616 => 240,  608 => 235,  605 => 77,  596 => 102,  574 => 223,  561 => 175,  527 => 113,  433 => 166,  388 => 130,  426 => 175,  383 => 135,  461 => 155,  370 => 127,  395 => 224,  294 => 105,  223 => 64,  220 => 73,  492 => 180,  468 => 132,  444 => 168,  410 => 170,  397 => 134,  377 => 131,  262 => 55,  250 => 78,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 1051,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 680,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 638,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 572,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 526,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 494,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 538,  1126 => 443,  1106 => 528,  1100 => 897,  1026 => 418,  997 => 822,  983 => 816,  975 => 347,  956 => 318,  939 => 786,  902 => 362,  894 => 364,  879 => 76,  757 => 269,  727 => 212,  716 => 203,  670 => 278,  528 => 176,  476 => 253,  435 => 176,  354 => 50,  341 => 61,  192 => 67,  321 => 57,  243 => 75,  793 => 350,  780 => 311,  758 => 226,  700 => 193,  686 => 238,  652 => 185,  638 => 269,  620 => 171,  545 => 214,  523 => 175,  494 => 134,  459 => 91,  438 => 48,  351 => 49,  347 => 151,  402 => 108,  268 => 90,  430 => 117,  411 => 110,  379 => 164,  322 => 101,  315 => 55,  289 => 70,  284 => 86,  255 => 73,  234 => 62,  1133 => 444,  1124 => 469,  1121 => 56,  1116 => 549,  1113 => 467,  1108 => 376,  1103 => 368,  1098 => 26,  1081 => 415,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 419,  1042 => 483,  1039 => 345,  1025 => 335,  1021 => 391,  1015 => 412,  1008 => 461,  996 => 406,  989 => 454,  985 => 315,  981 => 257,  977 => 321,  970 => 320,  966 => 375,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 148,  919 => 83,  917 => 369,  908 => 411,  905 => 363,  896 => 358,  891 => 360,  877 => 334,  862 => 348,  857 => 273,  837 => 239,  832 => 259,  827 => 68,  821 => 66,  803 => 179,  778 => 281,  769 => 58,  765 => 297,  753 => 54,  746 => 52,  743 => 268,  735 => 75,  730 => 214,  720 => 363,  717 => 362,  712 => 251,  691 => 233,  678 => 236,  654 => 199,  587 => 229,  576 => 196,  539 => 116,  517 => 144,  471 => 18,  441 => 121,  437 => 239,  418 => 144,  386 => 101,  373 => 133,  304 => 112,  270 => 68,  265 => 90,  229 => 94,  477 => 188,  455 => 125,  448 => 41,  429 => 165,  407 => 138,  399 => 138,  389 => 145,  375 => 128,  358 => 145,  349 => 120,  335 => 106,  327 => 122,  298 => 91,  280 => 109,  249 => 84,  194 => 65,  142 => 37,  344 => 115,  318 => 119,  306 => 115,  295 => 106,  357 => 51,  300 => 113,  286 => 73,  276 => 96,  269 => 91,  254 => 50,  128 => 24,  237 => 69,  165 => 42,  122 => 34,  798 => 281,  770 => 302,  759 => 278,  748 => 298,  731 => 260,  721 => 293,  718 => 301,  708 => 250,  696 => 287,  617 => 169,  590 => 160,  553 => 147,  550 => 157,  540 => 212,  533 => 210,  500 => 102,  493 => 57,  489 => 133,  482 => 129,  467 => 258,  464 => 202,  458 => 160,  452 => 158,  449 => 123,  415 => 83,  382 => 142,  372 => 128,  361 => 129,  356 => 24,  339 => 113,  302 => 117,  285 => 110,  258 => 65,  123 => 45,  108 => 38,  424 => 86,  394 => 77,  380 => 132,  338 => 90,  319 => 119,  316 => 117,  312 => 116,  290 => 111,  267 => 79,  206 => 60,  110 => 26,  240 => 102,  224 => 66,  219 => 87,  217 => 86,  202 => 40,  186 => 58,  170 => 53,  100 => 32,  67 => 18,  14 => 1,  1096 => 425,  1090 => 421,  1088 => 435,  1085 => 456,  1066 => 409,  1034 => 282,  1031 => 281,  1018 => 416,  1013 => 358,  1007 => 274,  1002 => 403,  993 => 266,  986 => 264,  982 => 394,  976 => 399,  971 => 376,  964 => 255,  949 => 388,  946 => 402,  940 => 384,  937 => 374,  928 => 452,  926 => 413,  915 => 299,  912 => 82,  903 => 231,  898 => 440,  892 => 319,  889 => 337,  887 => 281,  884 => 79,  876 => 75,  874 => 215,  871 => 331,  863 => 352,  861 => 274,  858 => 347,  850 => 378,  843 => 206,  840 => 406,  815 => 251,  812 => 297,  808 => 246,  804 => 314,  799 => 312,  791 => 310,  785 => 328,  775 => 82,  771 => 231,  754 => 267,  728 => 317,  726 => 72,  723 => 71,  715 => 105,  711 => 152,  709 => 222,  706 => 248,  698 => 243,  694 => 199,  692 => 189,  689 => 240,  681 => 242,  677 => 232,  675 => 234,  663 => 62,  661 => 200,  650 => 223,  646 => 112,  629 => 181,  627 => 244,  625 => 209,  622 => 242,  598 => 232,  592 => 75,  586 => 199,  575 => 232,  566 => 242,  556 => 67,  554 => 240,  541 => 176,  536 => 241,  515 => 108,  511 => 166,  509 => 24,  488 => 196,  486 => 220,  483 => 175,  465 => 126,  463 => 181,  450 => 244,  432 => 129,  419 => 146,  371 => 137,  362 => 159,  353 => 118,  337 => 141,  333 => 130,  309 => 95,  303 => 108,  299 => 106,  291 => 101,  272 => 81,  261 => 89,  253 => 98,  239 => 63,  235 => 78,  213 => 84,  200 => 59,  198 => 39,  159 => 50,  149 => 60,  146 => 48,  131 => 33,  116 => 28,  79 => 23,  74 => 19,  71 => 19,  836 => 262,  817 => 322,  814 => 319,  811 => 235,  805 => 244,  787 => 59,  779 => 169,  776 => 222,  773 => 347,  761 => 296,  751 => 265,  747 => 265,  742 => 336,  739 => 333,  736 => 215,  724 => 259,  705 => 69,  702 => 601,  688 => 232,  680 => 185,  667 => 232,  662 => 27,  656 => 418,  649 => 285,  644 => 220,  641 => 20,  624 => 109,  613 => 166,  607 => 273,  597 => 161,  591 => 49,  584 => 3,  579 => 1,  563 => 187,  559 => 68,  551 => 243,  547 => 179,  537 => 115,  524 => 112,  512 => 174,  507 => 165,  504 => 141,  498 => 213,  485 => 194,  480 => 50,  472 => 96,  466 => 38,  460 => 152,  447 => 153,  442 => 40,  434 => 47,  428 => 127,  422 => 146,  404 => 80,  368 => 132,  364 => 126,  340 => 135,  334 => 125,  330 => 59,  325 => 45,  292 => 89,  287 => 87,  282 => 79,  279 => 109,  273 => 94,  266 => 104,  256 => 88,  252 => 87,  228 => 67,  218 => 64,  201 => 70,  64 => 22,  51 => 10,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 847,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 823,  2037 => 789,  2031 => 788,  2028 => 818,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 811,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 779,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 769,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 744,  1801 => 695,  1798 => 694,  1795 => 742,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 708,  1730 => 705,  1720 => 688,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 672,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 659,  1651 => 656,  1641 => 631,  1637 => 667,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 651,  1592 => 614,  1587 => 613,  1584 => 635,  1581 => 731,  1576 => 608,  1574 => 633,  1567 => 603,  1563 => 630,  1559 => 601,  1552 => 628,  1548 => 597,  1542 => 593,  1529 => 605,  1525 => 614,  1516 => 708,  1512 => 583,  1503 => 599,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 569,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 537,  1380 => 539,  1376 => 523,  1371 => 645,  1367 => 527,  1364 => 518,  1361 => 523,  1355 => 543,  1342 => 624,  1337 => 510,  1328 => 516,  1324 => 511,  1317 => 511,  1313 => 499,  1310 => 498,  1304 => 504,  1291 => 502,  1286 => 497,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 487,  1262 => 490,  1257 => 489,  1254 => 482,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 498,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 472,  1196 => 464,  1192 => 490,  1188 => 456,  1184 => 485,  1179 => 462,  1176 => 461,  1172 => 452,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 561,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 442,  1119 => 468,  1115 => 424,  1111 => 377,  1107 => 422,  1102 => 439,  1099 => 438,  1095 => 419,  1091 => 418,  1082 => 455,  1079 => 524,  1076 => 359,  1070 => 431,  1057 => 491,  1052 => 406,  1045 => 484,  1040 => 414,  1036 => 283,  1032 => 360,  1028 => 337,  1023 => 393,  1020 => 392,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 352,  992 => 821,  979 => 400,  974 => 256,  967 => 391,  962 => 337,  958 => 336,  954 => 334,  950 => 153,  945 => 376,  942 => 460,  938 => 330,  934 => 356,  927 => 147,  923 => 387,  920 => 412,  910 => 353,  901 => 340,  897 => 339,  890 => 343,  886 => 50,  883 => 356,  868 => 268,  856 => 323,  853 => 319,  849 => 308,  845 => 69,  841 => 341,  835 => 337,  830 => 249,  826 => 329,  822 => 326,  818 => 65,  813 => 183,  810 => 290,  806 => 180,  802 => 242,  795 => 311,  792 => 239,  789 => 233,  784 => 286,  782 => 282,  777 => 291,  772 => 289,  768 => 81,  763 => 327,  760 => 319,  756 => 214,  752 => 222,  745 => 314,  741 => 218,  738 => 216,  732 => 171,  719 => 253,  714 => 251,  710 => 200,  704 => 267,  699 => 67,  695 => 66,  690 => 285,  687 => 210,  683 => 346,  679 => 298,  672 => 179,  668 => 264,  665 => 229,  658 => 178,  645 => 225,  640 => 211,  634 => 218,  628 => 174,  623 => 107,  619 => 78,  611 => 165,  606 => 164,  603 => 199,  599 => 198,  595 => 193,  583 => 159,  580 => 45,  573 => 157,  560 => 185,  543 => 146,  538 => 69,  534 => 175,  530 => 145,  526 => 229,  521 => 287,  518 => 109,  514 => 202,  510 => 143,  503 => 59,  496 => 58,  490 => 56,  484 => 19,  474 => 127,  470 => 156,  446 => 122,  440 => 149,  436 => 119,  431 => 141,  425 => 81,  416 => 112,  412 => 76,  408 => 157,  403 => 134,  400 => 152,  396 => 137,  392 => 144,  385 => 224,  381 => 100,  367 => 111,  363 => 124,  359 => 127,  355 => 122,  350 => 120,  346 => 92,  343 => 116,  328 => 112,  324 => 120,  313 => 80,  307 => 151,  301 => 116,  288 => 115,  283 => 101,  271 => 105,  257 => 88,  251 => 83,  238 => 79,  233 => 97,  195 => 78,  191 => 64,  187 => 74,  183 => 63,  130 => 34,  88 => 20,  76 => 15,  115 => 34,  95 => 22,  655 => 177,  651 => 176,  648 => 215,  637 => 219,  633 => 175,  621 => 462,  618 => 179,  615 => 205,  604 => 52,  600 => 233,  588 => 48,  585 => 295,  582 => 160,  571 => 156,  567 => 193,  555 => 37,  552 => 183,  549 => 224,  544 => 230,  542 => 178,  535 => 177,  531 => 174,  519 => 173,  516 => 162,  513 => 171,  508 => 230,  506 => 160,  499 => 20,  495 => 181,  491 => 163,  481 => 161,  478 => 128,  475 => 97,  469 => 182,  456 => 204,  451 => 149,  443 => 194,  439 => 144,  427 => 116,  423 => 114,  420 => 140,  409 => 118,  405 => 109,  401 => 136,  391 => 134,  387 => 132,  384 => 134,  378 => 76,  365 => 97,  360 => 123,  348 => 138,  336 => 132,  332 => 109,  329 => 127,  323 => 111,  310 => 110,  305 => 118,  277 => 95,  274 => 87,  263 => 77,  259 => 100,  247 => 49,  244 => 70,  241 => 82,  222 => 73,  210 => 70,  207 => 41,  204 => 66,  184 => 24,  181 => 57,  167 => 52,  157 => 41,  96 => 53,  421 => 147,  417 => 250,  414 => 143,  406 => 141,  398 => 146,  393 => 132,  390 => 153,  376 => 99,  369 => 124,  366 => 160,  352 => 121,  345 => 67,  342 => 66,  331 => 138,  326 => 87,  320 => 84,  317 => 43,  314 => 97,  311 => 121,  308 => 61,  297 => 58,  293 => 114,  281 => 96,  278 => 78,  275 => 94,  264 => 91,  260 => 85,  248 => 105,  245 => 83,  242 => 81,  231 => 66,  227 => 78,  215 => 63,  212 => 52,  209 => 72,  197 => 53,  177 => 52,  171 => 71,  161 => 48,  132 => 31,  121 => 44,  105 => 36,  99 => 21,  81 => 45,  77 => 26,  180 => 47,  176 => 39,  156 => 47,  143 => 52,  139 => 51,  118 => 25,  189 => 49,  185 => 54,  173 => 43,  166 => 68,  152 => 46,  174 => 31,  164 => 33,  154 => 31,  150 => 55,  137 => 55,  133 => 27,  127 => 46,  107 => 28,  102 => 44,  83 => 24,  78 => 15,  53 => 34,  23 => 3,  42 => 9,  138 => 44,  134 => 43,  109 => 37,  103 => 33,  97 => 43,  94 => 29,  84 => 21,  75 => 23,  69 => 14,  66 => 42,  54 => 8,  44 => 7,  230 => 61,  226 => 60,  203 => 71,  193 => 77,  188 => 66,  182 => 53,  178 => 44,  168 => 59,  163 => 58,  160 => 39,  155 => 56,  148 => 44,  145 => 47,  140 => 34,  136 => 34,  125 => 30,  120 => 48,  113 => 27,  101 => 35,  92 => 33,  89 => 51,  85 => 27,  73 => 25,  62 => 23,  59 => 24,  56 => 9,  41 => 6,  126 => 35,  119 => 44,  111 => 39,  106 => 32,  98 => 35,  93 => 38,  86 => 19,  70 => 15,  60 => 20,  28 => 3,  36 => 13,  114 => 44,  104 => 27,  91 => 20,  80 => 21,  63 => 17,  58 => 37,  40 => 7,  34 => 4,  45 => 13,  61 => 13,  55 => 12,  48 => 9,  39 => 7,  35 => 6,  31 => 3,  26 => 2,  21 => 2,  46 => 21,  29 => 9,  57 => 12,  50 => 23,  47 => 8,  38 => 7,  33 => 6,  49 => 7,  32 => 9,  246 => 76,  236 => 79,  232 => 90,  225 => 77,  221 => 75,  216 => 74,  214 => 85,  211 => 73,  208 => 72,  205 => 60,  199 => 39,  196 => 68,  190 => 37,  179 => 73,  175 => 72,  172 => 67,  169 => 54,  162 => 46,  158 => 57,  153 => 50,  151 => 30,  147 => 37,  144 => 36,  141 => 42,  135 => 50,  129 => 26,  124 => 39,  117 => 27,  112 => 47,  90 => 17,  87 => 25,  82 => 34,  72 => 29,  68 => 23,  65 => 17,  52 => 16,  43 => 6,  37 => 11,  30 => 5,  27 => 8,  25 => 4,  24 => 2,  22 => 2,  19 => 1,);
    }
}
