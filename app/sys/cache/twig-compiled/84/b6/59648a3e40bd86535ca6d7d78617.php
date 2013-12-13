<?php

/* AgentBundle:Publish:window-section.html.twig */
class __TwigTemplate_84b659648a3e40bd86535ca6d7d78617 extends \Application\DeskPRO\Twig\Template
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
        echo "\t<li class=\"tab active\" data-tab-id=\"pane-content-main\"><i class=\"icon-dp-article\"></i> ";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.chrome.nav_publish");
        echo "</li>
";
    }

    // line 6
    public function block_pane_content($context, array $blocks = array())
    {
        // line 7
        echo "<div class=\"pane-content pane-content-main\">

\t<section class=\"pane-section dp-collapsible dp-collapsible-open\" id=\"publish_section_pending\">
\t\t<header>
\t\t\t<h1>
\t\t\t\t<em class=\"dp-collapsible-toggle-btn\"><i class=\"icon-x\"></i></em>
\t\t\t\t";
        // line 13
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.pending");
        echo "
\t\t\t</h1>
\t\t</header>
\t\t<article>
\t\t\t<ul class=\"nav-list nav-list-small\">
\t\t\t\t";
        // line 18
        if (isset($context["counts"])) { $_counts_ = $context["counts"]; } else { $_counts_ = null; }
        if ($this->getAttribute($_counts_, "validating_comments")) {
            // line 19
            echo "\t\t\t\t\t<li class=\"is-nav-item\" data-route=\"listpane:";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_publish_validatingcomments"), "html", null, true);
            echo "\">
\t\t\t\t\t\t<div class=\"item\">
\t\t\t\t\t\t\t<h3>";
            // line 21
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.comments_to_validate");
            echo "</h3>
\t\t\t\t\t\t\t<em class=\"counter list-counter\" id=\"publish_validating_comments_count\">";
            // line 22
            if (isset($context["counts"])) { $_counts_ = $context["counts"]; } else { $_counts_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($_counts_, "validating_comments", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_counts_, "validating_comments"), 0)) : (0)), "html", null, true);
            echo "</em>
\t\t\t\t\t\t</div>
\t\t\t\t\t</li>
\t\t\t\t";
        }
        // line 26
        echo "\t\t\t\t";
        if (isset($context["counts"])) { $_counts_ = $context["counts"]; } else { $_counts_ = null; }
        if ($this->getAttribute($_counts_, "validating_content")) {
            // line 27
            echo "\t\t\t\t\t<li class=\"is-nav-item auto-load-route\" data-route=\"listpane:";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_publish_validatingcontent"), "html", null, true);
            echo "\">
\t\t\t\t\t\t<div class=\"item\">
\t\t\t\t\t\t\t<h3>";
            // line 29
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.content_to_validate");
            echo "</h3>
\t\t\t\t\t\t\t<em class=\"counter list-counter\" id=\"publish_validating_count\">";
            // line 30
            if (isset($context["counts"])) { $_counts_ = $context["counts"]; } else { $_counts_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($_counts_, "validating_content", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_counts_, "validating_content"), 0)) : (0)), "html", null, true);
            echo "</em>
\t\t\t\t\t\t</div>
\t\t\t\t\t</li>
\t\t\t\t";
        }
        // line 34
        echo "\t\t\t\t<li class=\"is-nav-item\" data-route=\"listpane:";
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_kb_pending"), "html", null, true);
        echo "\">
\t\t\t\t\t<div class=\"item\">
\t\t\t\t\t\t<h3>";
        // line 36
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.pending_articles");
        echo "</h3>
\t\t\t\t\t\t<em class=\"counter list-counter\" id=\"kb_pending_count\">";
        // line 37
        if (isset($context["counts"])) { $_counts_ = $context["counts"]; } else { $_counts_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_counts_, "pending", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_counts_, "pending"), 0)) : (0)), "html", null, true);
        echo "</em>
\t\t\t\t\t</div>
\t\t\t\t</li>
\t\t\t\t";
        // line 40
        if (isset($context["counts"])) { $_counts_ = $context["counts"]; } else { $_counts_ = null; }
        if ($this->getAttribute($_counts_, "drafts")) {
            // line 41
            echo "\t\t\t\t<li class=\"is-nav-item\" data-route=\"listpane:";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_public_drafts", array("type" => "mine")), "html", null, true);
            echo "\">
\t\t\t\t\t<div class=\"item\">
\t\t\t\t\t\t<h3>";
            // line 43
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.draft_content");
            echo "</h3>
\t\t\t\t\t\t<em class=\"counter list-counter\" id=\"publish_drafts_count\">";
            // line 44
            if (isset($context["counts"])) { $_counts_ = $context["counts"]; } else { $_counts_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($_counts_, "drafts", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_counts_, "drafts"), 0)) : (0)), "html", null, true);
            echo "</em>
\t\t\t\t\t</div>
\t\t\t\t</li>
\t\t\t\t";
        }
        // line 48
        echo "\t\t\t\t";
        if (isset($context["counts"])) { $_counts_ = $context["counts"]; } else { $_counts_ = null; }
        if ($this->getAttribute($_counts_, "all_drafts")) {
            // line 49
            echo "\t\t\t\t\t<li class=\"is-nav-item\" data-route=\"listpane:";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_public_drafts", array("type" => "all")), "html", null, true);
            echo "\">
\t\t\t\t\t\t<div class=\"item\">
\t\t\t\t\t\t\t<em class=\"counter list-counter\" id=\"publish_drafts_count\">";
            // line 51
            if (isset($context["counts"])) { $_counts_ = $context["counts"]; } else { $_counts_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($_counts_, "all_drafts", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_counts_, "all_drafts"), 0)) : (0)), "html", null, true);
            echo "</em>
\t\t\t\t\t\t\t<h3>";
            // line 52
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.all_draft_content");
            echo "</h3>
\t\t\t\t\t\t</div>
\t\t\t\t\t</li>
\t\t\t\t";
        }
        // line 56
        echo "\t\t\t</ul>
\t\t</article>
\t</section>

\t";
        // line 60
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.apps_kb"), "method")) {
            // line 61
            echo "\t\t<section class=\"pane-section dp-collapsible dp-collapsible-open\" id=\"publish_section_kb\">
\t\t\t<header>
\t\t\t\t<h1>
\t\t\t\t\t<em class=\"dp-collapsible-toggle-btn\"><i class=\"icon-x\"></i></em>
\t\t\t\t\t";
            // line 65
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.knowledgebase");
            echo "
\t\t\t\t</h1>
\t\t\t</header>
\t\t\t<article>
\t\t\t\t<ul class=\"nav-list nav-list-small nav-list-tree\">
\t\t\t\t\t";
            // line 70
            if (isset($context["kb_cats"])) { $_kb_cats_ = $context["kb_cats"]; } else { $_kb_cats_ = null; }
            if (isset($context["kb_cats_counts"])) { $_kb_cats_counts_ = $context["kb_cats_counts"]; } else { $_kb_cats_counts_ = null; }
            $this->env->loadTemplate("AgentBundle:Publish:kb-cat-list.html.twig")->display(array_merge($context, array("cats" => $_kb_cats_, "counts" => $_kb_cats_counts_, "depth" => 0)));
            // line 71
            echo "\t\t\t\t\t<li class=\"new-cat-trigger new-cat\">
\t\t\t\t\t\t<div class=\"item\" data-route=\"poppage:";
            // line 72
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_publish_cats_newform", array("type" => "article")), "html", null, true);
            echo "\">
\t\t\t\t\t\t\t<i class=\"icon-folder-close-alt\"></i>
\t\t\t\t\t\t\t<h3>";
            // line 74
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.add_new_category");
            echo "</h3>
\t\t\t\t\t\t</div>
\t\t\t\t\t</li>
\t\t\t\t</ul>

\t\t\t\t";
            // line 79
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if (isset($context["kb_translate_queue"])) { $_kb_translate_queue_ = $context["kb_translate_queue"]; } else { $_kb_translate_queue_ = null; }
            if (($this->getAttribute($this->getAttribute($_app_, "getLanguages", array(), "method"), "isMultiLang", array(), "method") && $this->getAttribute($_kb_translate_queue_, 0, array(), "array"))) {
                // line 80
                echo "\t\t\t\t\t<nav class=\"pane-section-tabs border-top\" style=\"margin: 15px 0 3px 0;\">
\t\t\t\t\t\t<ul>
\t\t\t\t\t\t\t<li class=\"on\"><span>";
                // line 82
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.translations");
                echo "</span></li>
\t\t\t\t\t\t\t<li class=\"invis-last\"></li>
\t\t\t\t\t\t</ul>
\t\t\t\t\t</nav>
\t\t\t\t\t<ul class=\"nav-list nav-list-small nav-list-tree\">
\t\t\t\t\t\t<li class=\"is-nav-item\">
\t\t\t\t\t\t\t<div class=\"item\" data-route=\"listpane:";
                // line 88
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_kb_list", array("pending_translate" => 1)), "html", null, true);
                echo "\">
\t\t\t\t\t\t\t\t<i class=\"dp-toggle-icon click-through icon-caret-right\"></i>
\t\t\t\t\t\t\t\t<h3>";
                // line 90
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.untranslated_articles");
                echo "</h3>
\t\t\t\t\t\t\t\t<em class=\"counter list-counter\">";
                // line 91
                if (isset($context["kb_translate_queue"])) { $_kb_translate_queue_ = $context["kb_translate_queue"]; } else { $_kb_translate_queue_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_kb_translate_queue_, 0, array(), "array"), "html", null, true);
                echo "</em>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t<ul class=\"nav-list nav-list-small indented\" style=\"display: none;\">
\t\t\t\t\t\t\t\t";
                // line 94
                if (isset($context["kb_translate_queue"])) { $_kb_translate_queue_ = $context["kb_translate_queue"]; } else { $_kb_translate_queue_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($_kb_translate_queue_);
                foreach ($context['_seq'] as $context["lang_id"] => $context["count"]) {
                    if (isset($context["lang_id"])) { $_lang_id_ = $context["lang_id"]; } else { $_lang_id_ = null; }
                    if (isset($context["count"])) { $_count_ = $context["count"]; } else { $_count_ = null; }
                    if ((($_lang_id_ > 0) && ($_count_ > 0))) {
                        // line 95
                        echo "\t\t\t\t\t\t\t\t\t<li class=\"is-nav-item\">
\t\t\t\t\t\t\t\t\t\t<div class=\"item\" data-route=\"listpane:";
                        // line 96
                        if (isset($context["lang_id"])) { $_lang_id_ = $context["lang_id"]; } else { $_lang_id_ = null; }
                        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_kb_list", array("pending_translate" => 1, "language_id" => $_lang_id_)), "html", null, true);
                        echo "\">
\t\t\t\t\t\t\t\t\t\t\t<h3>";
                        // line 97
                        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                        if (isset($context["lang_id"])) { $_lang_id_ = $context["lang_id"]; } else { $_lang_id_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_app_, "languages"), "get", array(0 => $_lang_id_), "method"), "title"), "html", null, true);
                        echo "</h3>
\t\t\t\t\t\t\t\t\t\t\t<em class=\"counter list-counter\">";
                        // line 98
                        if (isset($context["count"])) { $_count_ = $context["count"]; } else { $_count_ = null; }
                        echo twig_escape_filter($this->env, $_count_, "html", null, true);
                        echo "</em>
\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t";
                    }
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['lang_id'], $context['count'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 102
                echo "\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t</li>
\t\t\t\t\t</ul>
\t\t\t\t";
            }
            // line 106
            echo "\t\t\t</article>
\t\t</section>
\t";
        }
        // line 109
        echo "
\t";
        // line 110
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.apps_news"), "method")) {
            // line 111
            echo "\t\t<section class=\"pane-section dp-collapsible\" id=\"publish_section_news\">
\t\t\t<header>
\t\t\t\t<h1>
\t\t\t\t\t<em class=\"dp-collapsible-toggle-btn\"><i class=\"icon-x\"></i></em>
\t\t\t\t\t";
            // line 115
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.news");
            echo "
\t\t\t\t</h1>
\t\t\t</header>
\t\t\t<article>
\t\t\t\t<ul class=\"nav-list nav-list-small nav-list-tree\">
\t\t\t\t\t";
            // line 120
            if (isset($context["news_cats"])) { $_news_cats_ = $context["news_cats"]; } else { $_news_cats_ = null; }
            if (isset($context["news_cats_counts"])) { $_news_cats_counts_ = $context["news_cats_counts"]; } else { $_news_cats_counts_ = null; }
            $this->env->loadTemplate("AgentBundle:Publish:news-cat-list.html.twig")->display(array_merge($context, array("cats" => $_news_cats_, "counts" => $_news_cats_counts_, "depth" => 0)));
            // line 121
            echo "\t\t\t\t\t<li class=\"new-cat-trigger new-cat\">
\t\t\t\t\t\t<div class=\"item\" data-route=\"poppage:";
            // line 122
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_publish_cats_newform", array("type" => "news")), "html", null, true);
            echo "\">
\t\t\t\t\t\t\t<i class=\"icon-folder-close-alt\"></i>
\t\t\t\t\t\t\t<h3>";
            // line 124
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.add_new_category");
            echo "</h3>
\t\t\t\t\t\t</div>
\t\t\t\t\t</li>
\t\t\t\t</ul>
\t\t\t</article>
\t\t</section>
\t";
        }
        // line 131
        echo "
\t";
        // line 132
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.apps_downloads"), "method")) {
            // line 133
            echo "\t\t<section class=\"pane-section dp-collapsible\" id=\"publish_section_downloads\">
\t\t\t<header>
\t\t\t\t<h1>
\t\t\t\t\t<em class=\"dp-collapsible-toggle-btn\"><i class=\"icon-x\"></i></em>
\t\t\t\t\t";
            // line 137
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.downloads");
            echo "
\t\t\t\t</h1>
\t\t\t</header>
\t\t\t<article>
\t\t\t\t<ul class=\"nav-list nav-list-small nav-list-tree\">
\t\t\t\t\t";
            // line 142
            if (isset($context["download_cats"])) { $_download_cats_ = $context["download_cats"]; } else { $_download_cats_ = null; }
            if (isset($context["download_cats_counts"])) { $_download_cats_counts_ = $context["download_cats_counts"]; } else { $_download_cats_counts_ = null; }
            $this->env->loadTemplate("AgentBundle:Publish:downloads-cat-list.html.twig")->display(array_merge($context, array("cats" => $_download_cats_, "counts" => $_download_cats_counts_, "depth" => 0)));
            // line 143
            echo "\t\t\t\t\t<li class=\"new-cat-trigger new-cat\">
\t\t\t\t\t\t<div class=\"item\" data-route=\"poppage:";
            // line 144
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_publish_cats_newform", array("type" => "download")), "html", null, true);
            echo "\">
\t\t\t\t\t\t\t<i class=\"icon-folder-close-alt\"></i>
\t\t\t\t\t\t\t<h3>";
            // line 146
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.add_new_category");
            echo "</h3>
\t\t\t\t\t\t</div>
\t\t\t\t\t</li>
\t\t\t\t</ul>
\t\t\t</article>
\t\t</section>
\t";
        }
        // line 153
        echo "
\t<section class=\"pane-section dp-collapsible\" id=\"publish_section_comments\" ";
        // line 154
        if (isset($context["counts"])) { $_counts_ = $context["counts"]; } else { $_counts_ = null; }
        if ((!$this->getAttribute($this->getAttribute($_counts_, "comments"), "all"))) {
            echo "style=\"display: none;\"";
        }
        echo ">
\t\t<header>
\t\t\t<h1>
\t\t\t\t<em class=\"dp-collapsible-toggle-btn\"><i class=\"icon-x\"></i></em>
\t\t\t\t";
        // line 158
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.comments");
        echo "
\t\t\t</h1>
\t\t</header>
\t\t<article>
\t\t\t<ul class=\"nav-list nav-list-small nav-list-tree\">
\t\t\t\t<li class=\"is-nav-item\">
\t\t\t\t\t<div class=\"item\" data-route=\"listpane:";
        // line 164
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_publish_listcomments", array("type" => "all")), "html", null, true);
        echo "\">
\t\t\t\t\t\t<i class=\"dp-toggle-icon click-through icon-caret-right\"></i>
\t\t\t\t\t\t<h3>";
        // line 166
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.all_comments");
        echo "</h3>
\t\t\t\t\t\t<em class=\"counter list-counter\" id=\"publish_all_comments_count\">";
        // line 167
        if (isset($context["counts"])) { $_counts_ = $context["counts"]; } else { $_counts_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_counts_, "comments", array(), "any", false, true), "all", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_counts_, "comments", array(), "any", false, true), "all"), 0)) : (0)), "html", null, true);
        echo "</em>
\t\t\t\t\t</div>
\t\t\t\t\t<ul class=\"nav-list nav-list-small indented\" style=\"display: none;\">
\t\t\t\t\t\t<li class=\"is-nav-item\" ";
        // line 170
        if (isset($context["counts"])) { $_counts_ = $context["counts"]; } else { $_counts_ = null; }
        if ((!$this->getAttribute($this->getAttribute($_counts_, "comments"), "articles"))) {
            echo "style=\"display: none;\"";
        }
        echo ">
\t\t\t\t\t\t\t<div class=\"item\" data-route=\"listpane:";
        // line 171
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_publish_listcomments", array("type" => "articles")), "html", null, true);
        echo "\">
\t\t\t\t\t\t\t\t<h3>";
        // line 172
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.article_comments");
        echo "</h3>
\t\t\t\t\t\t\t\t<em class=\"counter list-counter\" id=\"publish_articles_comments_count\">";
        // line 173
        if (isset($context["counts"])) { $_counts_ = $context["counts"]; } else { $_counts_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_counts_, "comments", array(), "any", false, true), "articles", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_counts_, "comments", array(), "any", false, true), "articles"), 0)) : (0)), "html", null, true);
        echo "</em>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t<li class=\"is-nav-item\" ";
        // line 176
        if (isset($context["counts"])) { $_counts_ = $context["counts"]; } else { $_counts_ = null; }
        if ((!$this->getAttribute($this->getAttribute($_counts_, "comments"), "news"))) {
            echo "style=\"display: none;\"";
        }
        echo ">
\t\t\t\t\t\t\t<div class=\"item\" data-route=\"listpane:";
        // line 177
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_publish_listcomments", array("type" => "news")), "html", null, true);
        echo "\">
\t\t\t\t\t\t\t\t<h3>";
        // line 178
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.news_comments");
        echo "</h3>
\t\t\t\t\t\t\t\t<em class=\"counter list-counter\" id=\"publish_news_comments_count\">";
        // line 179
        if (isset($context["counts"])) { $_counts_ = $context["counts"]; } else { $_counts_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_counts_, "comments", array(), "any", false, true), "news", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_counts_, "comments", array(), "any", false, true), "news"), 0)) : (0)), "html", null, true);
        echo "</em>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t<li class=\"is-nav-item\" ";
        // line 182
        if (isset($context["counts"])) { $_counts_ = $context["counts"]; } else { $_counts_ = null; }
        if ((!$this->getAttribute($this->getAttribute($_counts_, "comments"), "downloads"))) {
            echo "style=\"display: none;\"";
        }
        echo ">
\t\t\t\t\t\t\t<div class=\"item\" data-route=\"listpane:";
        // line 183
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_publish_listcomments", array("type" => "downloads")), "html", null, true);
        echo "\">
\t\t\t\t\t\t\t\t<h3>";
        // line 184
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.download_comments");
        echo "</h3>
\t\t\t\t\t\t\t\t<em class=\"counter list-counter\" id=\"publish_downloads_comments_count\">";
        // line 185
        if (isset($context["counts"])) { $_counts_ = $context["counts"]; } else { $_counts_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_counts_, "comments", array(), "any", false, true), "downloads", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_counts_, "comments", array(), "any", false, true), "downloads"), 0)) : (0)), "html", null, true);
        echo "</em>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</li>
\t\t\t\t\t</ul>
\t\t\t\t</li>
\t\t\t</ul>
\t\t</article>
\t</section>

\t<section class=\"pane-section dp-collapsible last\" id=\"publish_section_glossary\">
\t\t<header>
\t\t\t<h1>
\t\t\t\t<em class=\"dp-collapsible-toggle-btn\"><i class=\"icon-x\"></i></em>
\t\t\t\t";
        // line 198
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.glossary");
        echo "
\t\t\t</h1>
\t\t</header>
\t\t<article>
\t\t\t";
        // line 202
        $this->env->loadTemplate("AgentBundle:Publish:kb-glossary.html.twig")->display($context);
        // line 203
        echo "\t\t\t<br class=\"clear\" />
\t\t</article>
\t</section>
</div>

<div class=\"pane-content pane-content-search\" style=\"display: none;\">
\t";
        // line 209
        $this->env->loadTemplate("AgentBundle:Publish:window-search.html.twig")->display($context);
        // line 210
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "AgentBundle:Publish:window-section.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  612 => 203,  1394 => 446,  1385 => 442,  1382 => 441,  1369 => 433,  1363 => 432,  1360 => 431,  1350 => 429,  1305 => 412,  1300 => 409,  1281 => 404,  1277 => 403,  1265 => 398,  1255 => 396,  1247 => 393,  1234 => 387,  1202 => 375,  1199 => 374,  1187 => 372,  1162 => 365,  1136 => 355,  1128 => 352,  1122 => 350,  1069 => 332,  968 => 293,  846 => 250,  1183 => 449,  1132 => 354,  1097 => 341,  957 => 394,  907 => 277,  875 => 263,  653 => 236,  1329 => 405,  1309 => 401,  1303 => 399,  1297 => 398,  1292 => 397,  1280 => 394,  1249 => 391,  1237 => 390,  1205 => 409,  1200 => 408,  1194 => 407,  1178 => 380,  1175 => 447,  1170 => 375,  1135 => 362,  1105 => 428,  1078 => 335,  1068 => 325,  1048 => 417,  961 => 294,  922 => 280,  750 => 245,  842 => 263,  1038 => 319,  904 => 322,  882 => 301,  831 => 267,  860 => 314,  790 => 284,  733 => 296,  707 => 206,  744 => 220,  873 => 278,  824 => 266,  762 => 230,  713 => 235,  578 => 158,  2739 => 1153,  2735 => 1152,  2732 => 1151,  2725 => 1146,  2712 => 1144,  2707 => 1143,  2692 => 1131,  2688 => 1130,  2683 => 1127,  2680 => 1126,  2677 => 1125,  2671 => 1121,  2658 => 1119,  2653 => 1118,  2645 => 1113,  2641 => 1112,  2636 => 1109,  2632 => 1108,  2629 => 1107,  2626 => 1106,  2606 => 1089,  2602 => 1088,  2598 => 1087,  2594 => 1086,  2590 => 1085,  2586 => 1084,  2572 => 1073,  2568 => 1072,  2564 => 1071,  2560 => 1070,  2556 => 1069,  2552 => 1068,  2537 => 1056,  2533 => 1055,  2529 => 1054,  2525 => 1053,  2521 => 1052,  2503 => 1040,  2499 => 1039,  2495 => 1038,  2491 => 1037,  2487 => 1036,  2483 => 1035,  2469 => 1024,  2465 => 1023,  2461 => 1022,  2457 => 1021,  2453 => 1020,  2449 => 1019,  2426 => 999,  2422 => 998,  2418 => 997,  2414 => 996,  2410 => 995,  2406 => 994,  2392 => 983,  2388 => 982,  2384 => 981,  2380 => 980,  2376 => 979,  2372 => 978,  2358 => 967,  2354 => 966,  2350 => 965,  2346 => 964,  2342 => 963,  2338 => 962,  2324 => 951,  2320 => 950,  2316 => 949,  2312 => 948,  2308 => 947,  2304 => 946,  2295 => 939,  2282 => 937,  2277 => 936,  2269 => 931,  2265 => 930,  2260 => 927,  2256 => 926,  2253 => 925,  2230 => 901,  2220 => 899,  2216 => 898,  2211 => 896,  2208 => 895,  2198 => 893,  2194 => 892,  2189 => 890,  2184 => 889,  2177 => 885,  2173 => 884,  2164 => 877,  2151 => 875,  2146 => 874,  2143 => 873,  2141 => 864,  2134 => 860,  2130 => 859,  2122 => 853,  2120 => 852,  2113 => 848,  2105 => 846,  2096 => 839,  2083 => 837,  2078 => 836,  2073 => 834,  2067 => 830,  2062 => 828,  2058 => 827,  2054 => 826,  2050 => 825,  2045 => 824,  2038 => 822,  2034 => 821,  2022 => 814,  2009 => 812,  1999 => 809,  1993 => 805,  1988 => 803,  1984 => 802,  1980 => 801,  1976 => 800,  1971 => 799,  1968 => 798,  1964 => 797,  1960 => 796,  1954 => 793,  1946 => 788,  1942 => 787,  1938 => 786,  1932 => 782,  1927 => 780,  1919 => 778,  1915 => 777,  1911 => 776,  1906 => 775,  1903 => 774,  1899 => 773,  1895 => 772,  1886 => 768,  1883 => 764,  1871 => 759,  1866 => 756,  1863 => 755,  1842 => 752,  1825 => 749,  1818 => 748,  1815 => 747,  1800 => 743,  1793 => 741,  1772 => 729,  1761 => 721,  1757 => 720,  1751 => 717,  1740 => 709,  1719 => 697,  1715 => 696,  1686 => 673,  1676 => 669,  1665 => 661,  1661 => 660,  1645 => 651,  1638 => 649,  1630 => 645,  1625 => 642,  1622 => 641,  1598 => 637,  1577 => 634,  1558 => 629,  1549 => 627,  1546 => 626,  1535 => 618,  1531 => 617,  1514 => 606,  1510 => 605,  1504 => 602,  1493 => 594,  1483 => 590,  1464 => 580,  1458 => 577,  1443 => 568,  1437 => 565,  1426 => 557,  1405 => 545,  1401 => 544,  1395 => 541,  1390 => 538,  1381 => 533,  1373 => 531,  1348 => 521,  1343 => 520,  1335 => 515,  1321 => 510,  1318 => 418,  1299 => 503,  1294 => 407,  1282 => 496,  1269 => 491,  1260 => 397,  1240 => 478,  1221 => 381,  1216 => 378,  1210 => 410,  1206 => 473,  1193 => 467,  1189 => 466,  1155 => 439,  1150 => 447,  1022 => 312,  1006 => 404,  988 => 398,  969 => 392,  965 => 391,  921 => 370,  878 => 275,  866 => 349,  854 => 254,  819 => 279,  796 => 286,  1713 => 683,  1700 => 681,  1685 => 674,  1662 => 670,  1643 => 669,  1640 => 668,  1631 => 663,  1618 => 661,  1613 => 660,  1608 => 639,  1605 => 656,  1602 => 655,  1589 => 645,  1586 => 644,  1583 => 643,  1554 => 613,  1547 => 608,  1521 => 602,  1508 => 600,  1499 => 598,  1491 => 593,  1482 => 587,  1472 => 582,  1468 => 581,  1465 => 579,  1457 => 573,  1445 => 566,  1422 => 556,  1416 => 553,  1410 => 548,  1397 => 546,  1392 => 545,  1386 => 541,  1377 => 532,  1370 => 530,  1357 => 528,  1352 => 527,  1341 => 428,  1314 => 510,  1311 => 509,  1275 => 493,  1248 => 484,  1238 => 478,  1225 => 476,  1220 => 412,  1209 => 466,  1185 => 385,  1182 => 370,  1159 => 367,  1154 => 449,  1130 => 438,  1125 => 437,  1101 => 426,  1074 => 432,  1056 => 326,  1046 => 323,  1043 => 404,  1030 => 397,  1027 => 412,  947 => 361,  925 => 352,  913 => 324,  893 => 338,  881 => 267,  847 => 343,  829 => 336,  825 => 259,  1083 => 434,  995 => 300,  984 => 350,  963 => 292,  941 => 324,  851 => 367,  682 => 270,  1365 => 549,  1359 => 545,  1349 => 539,  1336 => 427,  1331 => 424,  1323 => 420,  1319 => 530,  1312 => 402,  1284 => 519,  1272 => 401,  1268 => 509,  1261 => 506,  1251 => 485,  1245 => 483,  1231 => 496,  1207 => 376,  1197 => 491,  1180 => 484,  1173 => 457,  1169 => 455,  1157 => 363,  1147 => 438,  1109 => 440,  1065 => 440,  1059 => 423,  1047 => 428,  1044 => 424,  1033 => 317,  1009 => 357,  991 => 351,  987 => 404,  973 => 294,  931 => 355,  924 => 282,  911 => 298,  906 => 81,  885 => 276,  872 => 317,  855 => 72,  749 => 264,  701 => 239,  594 => 180,  1163 => 440,  1143 => 440,  1087 => 420,  1077 => 433,  1051 => 325,  1037 => 480,  1010 => 405,  999 => 407,  932 => 326,  899 => 306,  895 => 404,  933 => 387,  914 => 133,  909 => 323,  833 => 284,  783 => 306,  755 => 248,  666 => 263,  453 => 158,  639 => 209,  568 => 176,  520 => 110,  657 => 216,  572 => 201,  609 => 202,  20 => 1,  659 => 217,  562 => 164,  548 => 185,  558 => 197,  479 => 145,  589 => 211,  457 => 183,  413 => 171,  953 => 290,  948 => 290,  935 => 394,  929 => 283,  916 => 382,  864 => 365,  844 => 338,  816 => 342,  807 => 259,  801 => 268,  774 => 257,  766 => 229,  737 => 297,  685 => 225,  664 => 225,  635 => 281,  593 => 209,  546 => 227,  532 => 223,  865 => 296,  852 => 241,  838 => 285,  820 => 201,  781 => 327,  764 => 252,  725 => 250,  632 => 268,  602 => 192,  565 => 165,  529 => 181,  505 => 156,  487 => 101,  473 => 87,  1853 => 842,  1849 => 753,  1839 => 751,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 733,  1778 => 732,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 693,  1705 => 781,  1688 => 675,  1681 => 769,  1677 => 768,  1667 => 671,  1656 => 762,  1648 => 655,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 606,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 593,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 540,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 522,  1289 => 406,  1276 => 604,  1263 => 601,  1244 => 480,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 373,  1181 => 561,  1171 => 446,  1158 => 550,  1114 => 428,  1104 => 465,  1072 => 333,  1062 => 7,  1024 => 395,  1014 => 406,  1000 => 328,  990 => 303,  980 => 297,  960 => 466,  918 => 280,  888 => 80,  834 => 268,  673 => 64,  636 => 198,  462 => 92,  454 => 138,  1144 => 358,  1139 => 356,  1131 => 399,  1127 => 434,  1110 => 347,  1092 => 459,  1089 => 426,  1086 => 520,  1084 => 337,  1063 => 387,  1060 => 321,  1055 => 422,  1050 => 373,  1035 => 372,  1019 => 330,  1003 => 309,  959 => 387,  900 => 366,  880 => 276,  870 => 277,  867 => 258,  859 => 294,  848 => 215,  839 => 376,  828 => 302,  823 => 374,  809 => 316,  800 => 241,  797 => 267,  794 => 257,  786 => 283,  740 => 78,  734 => 261,  703 => 228,  693 => 236,  630 => 278,  626 => 19,  614 => 275,  610 => 201,  581 => 206,  564 => 268,  525 => 179,  722 => 226,  697 => 282,  674 => 222,  671 => 221,  577 => 180,  569 => 233,  557 => 229,  502 => 153,  497 => 152,  445 => 151,  729 => 209,  684 => 237,  676 => 65,  669 => 220,  660 => 203,  647 => 212,  643 => 229,  601 => 306,  570 => 169,  522 => 156,  501 => 210,  296 => 115,  374 => 115,  631 => 207,  616 => 283,  608 => 194,  605 => 193,  596 => 188,  574 => 180,  561 => 231,  527 => 165,  433 => 126,  388 => 115,  426 => 143,  383 => 135,  461 => 184,  370 => 155,  395 => 166,  294 => 87,  223 => 88,  220 => 84,  492 => 175,  468 => 162,  444 => 153,  410 => 143,  397 => 134,  377 => 161,  262 => 91,  250 => 96,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 1051,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 680,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 638,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 572,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 526,  1301 => 1037,  1252 => 392,  1243 => 994,  1223 => 978,  1212 => 494,  1167 => 366,  1161 => 933,  1145 => 923,  1140 => 364,  1134 => 538,  1126 => 443,  1106 => 528,  1100 => 897,  1026 => 418,  997 => 822,  983 => 298,  975 => 296,  956 => 318,  939 => 285,  902 => 274,  894 => 364,  879 => 76,  757 => 269,  727 => 295,  716 => 235,  670 => 204,  528 => 187,  476 => 253,  435 => 177,  354 => 127,  341 => 104,  192 => 78,  321 => 114,  243 => 85,  793 => 266,  780 => 256,  758 => 229,  700 => 193,  686 => 238,  652 => 185,  638 => 226,  620 => 216,  545 => 162,  523 => 158,  494 => 151,  459 => 156,  438 => 146,  351 => 99,  347 => 127,  402 => 150,  268 => 98,  430 => 188,  411 => 117,  379 => 106,  322 => 118,  315 => 170,  289 => 130,  284 => 109,  255 => 97,  234 => 126,  1133 => 444,  1124 => 357,  1121 => 430,  1116 => 348,  1113 => 429,  1108 => 376,  1103 => 368,  1098 => 26,  1081 => 425,  1073 => 424,  1067 => 356,  1064 => 330,  1061 => 421,  1053 => 318,  1049 => 419,  1042 => 313,  1039 => 345,  1025 => 314,  1021 => 310,  1015 => 308,  1008 => 461,  996 => 406,  989 => 454,  985 => 315,  981 => 257,  977 => 321,  970 => 320,  966 => 375,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 148,  919 => 314,  917 => 279,  908 => 411,  905 => 310,  896 => 358,  891 => 378,  877 => 334,  862 => 274,  857 => 271,  837 => 261,  832 => 260,  827 => 349,  821 => 66,  803 => 179,  778 => 324,  769 => 253,  765 => 297,  753 => 54,  746 => 244,  743 => 298,  735 => 240,  730 => 251,  720 => 237,  717 => 362,  712 => 251,  691 => 233,  678 => 275,  654 => 199,  587 => 210,  576 => 171,  539 => 116,  517 => 144,  471 => 160,  441 => 195,  437 => 149,  418 => 120,  386 => 154,  373 => 109,  304 => 120,  270 => 106,  265 => 92,  229 => 44,  477 => 167,  455 => 125,  448 => 143,  429 => 141,  407 => 120,  399 => 167,  389 => 145,  375 => 141,  358 => 146,  349 => 162,  335 => 120,  327 => 98,  298 => 91,  280 => 115,  249 => 88,  194 => 82,  142 => 60,  344 => 94,  318 => 114,  306 => 115,  295 => 80,  357 => 136,  300 => 118,  286 => 151,  276 => 86,  269 => 83,  254 => 100,  128 => 36,  237 => 72,  165 => 58,  122 => 34,  798 => 281,  770 => 302,  759 => 278,  748 => 298,  731 => 260,  721 => 293,  718 => 301,  708 => 218,  696 => 236,  617 => 204,  590 => 207,  553 => 163,  550 => 157,  540 => 161,  533 => 182,  500 => 171,  493 => 155,  489 => 202,  482 => 198,  467 => 158,  464 => 209,  458 => 139,  452 => 145,  449 => 134,  415 => 83,  382 => 107,  372 => 131,  361 => 110,  356 => 100,  339 => 131,  302 => 104,  285 => 97,  258 => 76,  123 => 41,  108 => 45,  424 => 123,  394 => 109,  380 => 117,  338 => 137,  319 => 216,  316 => 124,  312 => 116,  290 => 111,  267 => 141,  206 => 79,  110 => 28,  240 => 73,  224 => 58,  219 => 73,  217 => 80,  202 => 84,  186 => 70,  170 => 28,  100 => 25,  67 => 22,  14 => 1,  1096 => 345,  1090 => 339,  1088 => 435,  1085 => 456,  1066 => 423,  1034 => 282,  1031 => 281,  1018 => 416,  1013 => 358,  1007 => 408,  1002 => 403,  993 => 305,  986 => 264,  982 => 401,  976 => 399,  971 => 295,  964 => 255,  949 => 289,  946 => 288,  940 => 388,  937 => 374,  928 => 385,  926 => 318,  915 => 279,  912 => 82,  903 => 231,  898 => 440,  892 => 303,  889 => 277,  887 => 302,  884 => 79,  876 => 75,  874 => 215,  871 => 331,  863 => 373,  861 => 270,  858 => 255,  850 => 291,  843 => 206,  840 => 406,  815 => 251,  812 => 343,  808 => 246,  804 => 258,  799 => 312,  791 => 310,  785 => 262,  775 => 255,  771 => 253,  754 => 267,  728 => 317,  726 => 72,  723 => 238,  715 => 105,  711 => 220,  709 => 222,  706 => 232,  698 => 229,  694 => 199,  692 => 189,  689 => 234,  681 => 224,  677 => 229,  675 => 234,  663 => 218,  661 => 200,  650 => 213,  646 => 231,  629 => 267,  627 => 218,  625 => 266,  622 => 285,  598 => 199,  592 => 212,  586 => 175,  575 => 232,  566 => 242,  556 => 191,  554 => 188,  541 => 176,  536 => 224,  515 => 176,  511 => 166,  509 => 179,  488 => 150,  486 => 147,  483 => 149,  465 => 185,  463 => 142,  450 => 182,  432 => 146,  419 => 143,  371 => 154,  362 => 129,  353 => 144,  337 => 102,  333 => 91,  309 => 95,  303 => 106,  299 => 88,  291 => 89,  272 => 100,  261 => 98,  253 => 109,  239 => 94,  235 => 94,  213 => 87,  200 => 82,  198 => 74,  159 => 62,  149 => 52,  146 => 72,  131 => 36,  116 => 32,  79 => 35,  74 => 31,  71 => 24,  836 => 262,  817 => 243,  814 => 319,  811 => 261,  805 => 244,  787 => 257,  779 => 169,  776 => 232,  773 => 322,  761 => 296,  751 => 302,  747 => 265,  742 => 243,  739 => 227,  736 => 215,  724 => 259,  705 => 69,  702 => 216,  688 => 226,  680 => 205,  667 => 273,  662 => 218,  656 => 215,  649 => 285,  644 => 220,  641 => 220,  624 => 206,  613 => 166,  607 => 200,  597 => 275,  591 => 49,  584 => 236,  579 => 181,  563 => 175,  559 => 68,  551 => 190,  547 => 188,  537 => 160,  524 => 112,  512 => 174,  507 => 237,  504 => 159,  498 => 213,  485 => 172,  480 => 50,  472 => 145,  466 => 149,  460 => 142,  447 => 156,  442 => 40,  434 => 133,  428 => 176,  422 => 145,  404 => 113,  368 => 153,  364 => 111,  340 => 100,  334 => 135,  330 => 115,  325 => 98,  292 => 150,  287 => 110,  282 => 108,  279 => 106,  273 => 102,  266 => 92,  256 => 101,  252 => 87,  228 => 90,  218 => 62,  201 => 70,  64 => 26,  51 => 15,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 847,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 823,  2037 => 789,  2031 => 788,  2028 => 818,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 811,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 779,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 769,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 744,  1801 => 695,  1798 => 694,  1795 => 742,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 708,  1730 => 705,  1720 => 688,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 672,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 659,  1651 => 656,  1641 => 631,  1637 => 667,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 651,  1592 => 614,  1587 => 613,  1584 => 635,  1581 => 731,  1576 => 608,  1574 => 633,  1567 => 603,  1563 => 630,  1559 => 601,  1552 => 628,  1548 => 597,  1542 => 593,  1529 => 605,  1525 => 614,  1516 => 708,  1512 => 583,  1503 => 599,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 569,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 537,  1380 => 539,  1376 => 436,  1371 => 645,  1367 => 527,  1364 => 518,  1361 => 523,  1355 => 543,  1342 => 624,  1337 => 510,  1328 => 516,  1324 => 511,  1317 => 511,  1313 => 499,  1310 => 414,  1304 => 504,  1291 => 502,  1286 => 405,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 487,  1262 => 490,  1257 => 489,  1254 => 482,  1250 => 394,  1246 => 477,  1239 => 389,  1235 => 498,  1232 => 416,  1226 => 383,  1213 => 466,  1208 => 465,  1201 => 472,  1196 => 464,  1192 => 490,  1188 => 456,  1184 => 485,  1179 => 448,  1176 => 461,  1172 => 367,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 561,  1149 => 359,  1146 => 444,  1137 => 249,  1123 => 442,  1119 => 354,  1115 => 424,  1111 => 377,  1107 => 346,  1102 => 344,  1099 => 347,  1095 => 419,  1091 => 418,  1082 => 455,  1079 => 524,  1076 => 359,  1070 => 431,  1057 => 491,  1052 => 406,  1045 => 484,  1040 => 416,  1036 => 283,  1032 => 415,  1028 => 312,  1023 => 393,  1020 => 311,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 304,  998 => 308,  992 => 406,  979 => 297,  974 => 400,  967 => 399,  962 => 397,  958 => 336,  954 => 293,  950 => 393,  945 => 391,  942 => 288,  938 => 323,  934 => 284,  927 => 282,  923 => 382,  920 => 412,  910 => 278,  901 => 340,  897 => 273,  890 => 271,  886 => 270,  883 => 268,  868 => 375,  856 => 293,  853 => 319,  849 => 264,  845 => 290,  841 => 249,  835 => 245,  830 => 249,  826 => 282,  822 => 281,  818 => 264,  813 => 242,  810 => 290,  806 => 270,  802 => 339,  795 => 241,  792 => 335,  789 => 233,  784 => 286,  782 => 237,  777 => 255,  772 => 289,  768 => 81,  763 => 327,  760 => 250,  756 => 248,  752 => 247,  745 => 245,  741 => 218,  738 => 241,  732 => 171,  719 => 245,  714 => 251,  710 => 233,  704 => 281,  699 => 215,  695 => 66,  690 => 226,  687 => 210,  683 => 346,  679 => 223,  672 => 179,  668 => 264,  665 => 229,  658 => 178,  645 => 221,  640 => 227,  634 => 206,  628 => 174,  623 => 217,  619 => 78,  611 => 282,  606 => 280,  603 => 199,  599 => 194,  595 => 213,  583 => 159,  580 => 173,  573 => 170,  560 => 267,  543 => 186,  538 => 164,  534 => 189,  530 => 174,  526 => 158,  521 => 287,  518 => 157,  514 => 202,  510 => 154,  503 => 173,  496 => 163,  490 => 149,  484 => 146,  474 => 127,  470 => 144,  446 => 122,  440 => 130,  436 => 189,  431 => 145,  425 => 128,  416 => 125,  412 => 76,  408 => 165,  403 => 134,  400 => 119,  396 => 117,  392 => 116,  385 => 146,  381 => 158,  367 => 112,  363 => 102,  359 => 101,  355 => 108,  350 => 143,  346 => 142,  343 => 140,  328 => 118,  324 => 89,  313 => 96,  307 => 115,  301 => 82,  288 => 152,  283 => 88,  271 => 71,  257 => 89,  251 => 100,  238 => 84,  233 => 82,  195 => 78,  191 => 82,  187 => 81,  183 => 51,  130 => 48,  88 => 35,  76 => 25,  115 => 39,  95 => 30,  655 => 202,  651 => 176,  648 => 215,  637 => 218,  633 => 197,  621 => 462,  618 => 179,  615 => 196,  604 => 201,  600 => 233,  588 => 206,  585 => 295,  582 => 205,  571 => 179,  567 => 194,  555 => 172,  552 => 171,  549 => 170,  544 => 230,  542 => 166,  535 => 177,  531 => 159,  519 => 155,  516 => 218,  513 => 154,  508 => 215,  506 => 151,  499 => 209,  495 => 150,  491 => 203,  481 => 161,  478 => 128,  475 => 146,  469 => 182,  456 => 138,  451 => 135,  443 => 179,  439 => 178,  427 => 155,  423 => 142,  420 => 141,  409 => 160,  405 => 218,  401 => 176,  391 => 138,  387 => 334,  384 => 250,  378 => 205,  365 => 153,  360 => 104,  348 => 116,  336 => 130,  332 => 133,  329 => 132,  323 => 118,  310 => 85,  305 => 94,  277 => 79,  274 => 94,  263 => 67,  259 => 65,  247 => 95,  244 => 88,  241 => 129,  222 => 69,  210 => 80,  207 => 83,  204 => 39,  184 => 74,  181 => 52,  167 => 67,  157 => 45,  96 => 39,  421 => 173,  417 => 172,  414 => 145,  406 => 170,  398 => 111,  393 => 125,  390 => 164,  376 => 138,  369 => 124,  366 => 150,  352 => 135,  345 => 106,  342 => 122,  331 => 99,  326 => 131,  320 => 88,  317 => 114,  314 => 86,  311 => 122,  308 => 121,  297 => 117,  293 => 90,  281 => 106,  278 => 145,  275 => 103,  264 => 92,  260 => 96,  248 => 90,  245 => 61,  242 => 84,  231 => 85,  227 => 92,  215 => 84,  212 => 86,  209 => 85,  197 => 53,  177 => 50,  171 => 82,  161 => 51,  132 => 36,  121 => 50,  105 => 43,  99 => 40,  81 => 26,  77 => 34,  180 => 37,  176 => 77,  156 => 52,  143 => 62,  139 => 38,  118 => 56,  189 => 77,  185 => 75,  173 => 76,  166 => 53,  152 => 64,  174 => 48,  164 => 80,  154 => 90,  150 => 41,  137 => 59,  133 => 44,  127 => 35,  107 => 43,  102 => 56,  83 => 35,  78 => 27,  53 => 18,  23 => 4,  42 => 14,  138 => 56,  134 => 66,  109 => 36,  103 => 34,  97 => 55,  94 => 27,  84 => 47,  75 => 28,  69 => 21,  66 => 19,  54 => 14,  44 => 7,  230 => 64,  226 => 43,  203 => 82,  193 => 72,  188 => 80,  182 => 58,  178 => 65,  168 => 77,  163 => 56,  160 => 40,  155 => 67,  148 => 63,  145 => 49,  140 => 40,  136 => 47,  125 => 35,  120 => 40,  113 => 37,  101 => 37,  92 => 38,  89 => 50,  85 => 27,  73 => 22,  62 => 21,  59 => 23,  56 => 7,  41 => 6,  126 => 44,  119 => 63,  111 => 46,  106 => 27,  98 => 40,  93 => 38,  86 => 27,  70 => 29,  60 => 18,  28 => 6,  36 => 8,  114 => 30,  104 => 43,  91 => 29,  80 => 16,  63 => 19,  58 => 17,  40 => 10,  34 => 4,  45 => 8,  61 => 14,  55 => 21,  48 => 14,  39 => 13,  35 => 9,  31 => 3,  26 => 2,  21 => 2,  46 => 15,  29 => 6,  57 => 22,  50 => 12,  47 => 15,  38 => 5,  33 => 8,  49 => 17,  32 => 9,  246 => 75,  236 => 95,  232 => 91,  225 => 78,  221 => 87,  216 => 88,  214 => 82,  211 => 56,  208 => 40,  205 => 83,  199 => 58,  196 => 80,  190 => 71,  179 => 48,  175 => 55,  172 => 61,  169 => 60,  162 => 42,  158 => 44,  153 => 64,  151 => 51,  147 => 43,  144 => 42,  141 => 48,  135 => 37,  129 => 43,  124 => 64,  117 => 31,  112 => 58,  90 => 37,  87 => 36,  82 => 33,  72 => 27,  68 => 8,  65 => 28,  52 => 13,  43 => 14,  37 => 2,  30 => 5,  27 => 6,  25 => 5,  24 => 2,  22 => 2,  19 => 1,);
    }
}
