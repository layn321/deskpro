<?php

/* UserBundle:Articles:browse.html.twig */
class __TwigTemplate_0ba8b9f73c9909765fc2bb2fe7de6d3e extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("UserBundle::layout.html.twig");

        $this->blocks = array(
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
        // line 5
        $context["this_section"] = "articles";
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 7
    public function block_breadcrumb($context, array $blocks = array())
    {
        // line 8
        echo "<li xmlns=\"http://www.w3.org/1999/html\"><span class=\"dp-divider\">";
        echo $this->env->getExtension('deskpro_templating')->getLanguageArrow("right");
        echo "</span> <a href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_articles"), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.knowledgebase.knowledgebase");
        echo "</a>";
        if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
        if ($_category_) {
        }
        echo "</li>
\t";
        // line 9
        if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
        if ($_category_) {
            // line 10
            echo "\t\t";
            if (isset($context["category_path"])) { $_category_path_ = $context["category_path"]; } else { $_category_path_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_category_path_);
            foreach ($context['_seq'] as $context["_key"] => $context["cat"]) {
                // line 11
                echo "\t\t\t<li><span class=\"dp-divider\">";
                echo $this->env->getExtension('deskpro_templating')->getLanguageArrow("right");
                echo "</span> <a href=\"";
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_articles", array("slug" => $this->getAttribute($_cat_, "url_slug"))), "html", null, true);
                echo "\">";
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
                echo "</a></li>
\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['cat'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 13
            echo "\t\t<li><span class=\"dp-divider\">";
            echo $this->env->getExtension('deskpro_templating')->getLanguageArrow("right");
            echo "</span> <a href=\"";
            if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_articles", array("slug" => $this->getAttribute($_category_, "url_slug"))), "html", null, true);
            echo "\">";
            if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_category_, "title"), "html", null, true);
            echo "</a></li>
\t";
        }
    }

    // line 16
    public function block_page_title($context, array $blocks = array())
    {
        // line 17
        echo "\t";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.knowledgebase.knowledgebase");
        echo "
\t";
        // line 18
        if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
        if ($_category_) {
            // line 19
            echo "\t\t/
\t\t";
            // line 20
            if (isset($context["category_path"])) { $_category_path_ = $context["category_path"]; } else { $_category_path_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_category_path_);
            foreach ($context['_seq'] as $context["_key"] => $context["cat"]) {
                // line 21
                echo "\t\t\t";
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPhraseObject($_cat_), "html", null, true);
                echo " /
\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['cat'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 23
            echo "\t\t";
            if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPhraseObject($_category_), "html", null, true);
            echo "
\t";
        }
    }

    // line 26
    public function block_content($context, array $blocks = array())
    {
        // line 27
        echo "
";
        // line 28
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "admin_portal_controls")) {
            // line 29
            echo "\t";
            ob_start();
            $this->env->loadTemplate("UserBundle:Articles:section-header.html.twig")->display($context);
            $context["tpl_res"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
            // line 30
            echo "\t<div id=\"dp_custom_header_wrap\" class=\"dp-portal-block\" ";
            if (isset($context["tpl_res"])) { $_tpl_res_ = $context["tpl_res"]; } else { $_tpl_res_ = null; }
            if ((!$this->env->getExtension('deskpro_templating')->strTrim($_tpl_res_))) {
                echo "style=\"display: none\"";
            }
            echo ">
\t\t<div id=\"dp_page_custom_header\" class=\"dp-portal-block-content\">
\t\t\t";
            // line 32
            if (isset($context["tpl_res"])) { $_tpl_res_ = $context["tpl_res"]; } else { $_tpl_res_ = null; }
            echo $_tpl_res_;
            echo "
\t\t</div>
\t</div>
\t<div id=\"dp_custom_header_placeholder\" class=\"dp-portal-placeholder dp-portal-placeholder-header\" data-portal-block=\"articles_header\" data-portal-for=\"#dp_page_custom_header\" ";
            // line 35
            if (isset($context["tpl_res"])) { $_tpl_res_ = $context["tpl_res"]; } else { $_tpl_res_ = null; }
            if ($this->env->getExtension('deskpro_templating')->strTrim($_tpl_res_)) {
                echo "style=\"display: none\"";
            }
            echo ">
\t\t<em>";
            // line 36
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.designer.edit_header_section");
            echo "</em>
\t</div>
";
        } else {
            // line 39
            echo "\t";
            $this->env->loadTemplate("UserBundle:Articles:section-header.html.twig")->display($context);
        }
        // line 41
        echo "
";
        // line 42
        if (isset($context["category_children"])) { $_category_children_ = $context["category_children"]; } else { $_category_children_ = null; }
        if (twig_length_filter($this->env, $_category_children_)) {
            // line 43
            echo "\t<div style=\"overflow: hidden;\">
\t\t<section class=\"dp-portal-section\">
\t\t\t";
            // line 45
            if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
            if ($_category_) {
                // line 46
                echo "\t\t\t<header>
\t\t\t\t<h3>";
                // line 47
                if (isset($context["category_children"])) { $_category_children_ = $context["category_children"]; } else { $_category_children_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.knowledgebase.title-categories", array("count" => twig_length_filter($this->env, $_category_children_)));
                echo "</h3>
\t\t\t</header>
\t\t\t";
            }
            // line 50
            echo "\t\t\t<div class=\"dp-content-block dp-news-block\">
\t\t\t\t<div class=\"dp-content-wrapper\">
\t\t\t\t\t";
            // line 52
            if (isset($context["category_children"])) { $_category_children_ = $context["category_children"]; } else { $_category_children_ = null; }
            if (isset($context["category_children_articles"])) { $_category_children_articles_ = $context["category_children_articles"]; } else { $_category_children_articles_ = null; }
            $this->env->loadTemplate("UserBundle:Articles:browse-cat-list.html.twig")->display(array_merge($context, array("categories" => $_category_children_, "category_articles" => $_category_children_articles_)));
            // line 53
            echo "\t\t\t\t</div>
\t\t\t</div>
\t\t</section>
\t\t<br style=\"clear: both; height: 1px; overflow: hidden\" />
\t</div>
";
        }
        // line 59
        echo "
";
        // line 60
        if (isset($context["articles"])) { $_articles_ = $context["articles"]; } else { $_articles_ = null; }
        if ($_articles_) {
            // line 61
            echo "\t<section class=\"dp-portal-section\">
\t\t";
            // line 62
            if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
            if ($_category_) {
                // line 63
                echo "\t\t\t<header>
\t\t\t\t";
                // line 64
                if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
                if (isset($context["articles"])) { $_articles_ = $context["articles"]; } else { $_articles_ = null; }
                if (($_category_ && twig_length_filter($this->env, $_articles_))) {
                    // line 65
                    echo "\t\t\t\t\t";
                    if (isset($context["pageinfo"])) { $_pageinfo_ = $context["pageinfo"]; } else { $_pageinfo_ = null; }
                    if (isset($context["articles"])) { $_articles_ = $context["articles"]; } else { $_articles_ = null; }
                    if (($this->getAttribute($_pageinfo_, "total_results") > twig_length_filter($this->env, $_articles_))) {
                        // line 66
                        echo "\t\t\t\t\t\t<h3>";
                        if (isset($context["pageinfo"])) { $_pageinfo_ = $context["pageinfo"]; } else { $_pageinfo_ = null; }
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.knowledgebase.title-articles-paged", array("total" => $this->getAttribute($_pageinfo_, "total_results"), "first" => $this->getAttribute($_pageinfo_, "first_result"), "last" => $this->getAttribute($_pageinfo_, "last_result")));
                        echo "</h3>
\t\t\t\t\t";
                    } else {
                        // line 68
                        echo "\t\t\t\t\t\t<h3>";
                        if (isset($context["pageinfo"])) { $_pageinfo_ = $context["pageinfo"]; } else { $_pageinfo_ = null; }
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.knowledgebase.title-articles", array("count" => $this->getAttribute($_pageinfo_, "total_results")));
                        echo "</h3>
\t\t\t\t\t";
                    }
                    // line 70
                    echo "\t\t\t\t";
                }
                // line 71
                echo "\t\t\t</header>
\t\t";
            }
            // line 73
            echo "\t\t<div class=\"dp-content-block dp-articles-block\">
\t\t\t<div class=\"dp-content-wrapper\">
\t\t\t\t<ul class=\"dp-content-list\">
\t\t\t\t\t";
            // line 76
            if (isset($context["articles"])) { $_articles_ = $context["articles"]; } else { $_articles_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_articles_);
            foreach ($context['_seq'] as $context["_key"] => $context["article"]) {
                // line 77
                echo "\t\t\t\t\t<li>
\t\t\t\t\t\t<section class=\"dp-article-post dp-content-post\">
\t\t\t\t\t\t\t<header>
\t\t\t\t\t\t\t\t<h3 style=\"margin-bottom: 0\" class=\"dp-fadeaway-container\"><a class=\"dp-fadeaway-title\" href=\"";
                // line 80
                if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_articles_article", array("slug" => $this->getAttribute($_article_, "url_slug"))), "html", null, true);
                echo "\">";
                if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_article_, "title"), "html", null, true);
                echo "</a><div class=\"dp-fadeaway\"></div></h3>
\t\t\t\t\t\t\t\t<ul class=\"dp-post-info\">
\t\t\t\t\t\t\t\t\t<li class=\"dp-author\"><i class=\"dp-icon-user\"></i> ";
                // line 82
                if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_article_, "person"), "display_name_user"), "html", null, true);
                echo "</li>
\t\t\t\t\t\t\t\t\t<li class=\"dp-date\"><i class=\"dp-icon-calendar\"></i> ";
                // line 83
                if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_article_, "date_created"), "full"), "html", null, true);
                echo "</li>
\t\t\t\t\t\t\t\t\t";
                // line 84
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                if ($this->getAttribute($_app_, "getSetting", array(0 => "user.publish_comments"), "method")) {
                    echo "<li class=\"dp-comments\"><i class=\"dp-icon-comment\"></i> <a href=\"";
                    if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
                    echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_articles_article", array("slug" => $this->getAttribute($_article_, "url_slug"))), "html", null, true);
                    echo "#comments\">";
                    if (isset($context["comment_counts"])) { $_comment_counts_ = $context["comment_counts"]; } else { $_comment_counts_ = null; }
                    if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.general.count_comments", array("count" => (($this->getAttribute($_comment_counts_, $this->getAttribute($_article_, "id"), array(), "array", true, true)) ? (_twig_default_filter($this->getAttribute($_comment_counts_, $this->getAttribute($_article_, "id"), array(), "array"), 0)) : (0))));
                    echo "</li></a>";
                }
                // line 85
                echo "\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t\t</header>
\t\t\t\t\t\t</section>
\t\t\t\t\t</li>
\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['article'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 90
            echo "\t\t\t\t</ul>

\t\t\t\t";
            // line 92
            if (isset($context["pageinfo"])) { $_pageinfo_ = $context["pageinfo"]; } else { $_pageinfo_ = null; }
            if (($this->getAttribute($_pageinfo_, "last") != 1)) {
                // line 93
                echo "\t\t\t\t\t";
                if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
                $context["page_url"] = ($this->env->getExtension('routing')->getPath("user_articles", array("slug" => (($this->getAttribute($_category_, "url_slug", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_category_, "url_slug"), "all-categories")) : ("all-categories")))) . "?p=");
                // line 94
                echo "\t\t\t\t\t<div class=\"dp-pagination\">
\t\t\t\t\t\t<ul>
\t\t\t\t\t\t\t";
                // line 96
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
                // line 97
                echo "\t\t\t\t\t\t\t";
                if (isset($context["pageinfo"])) { $_pageinfo_ = $context["pageinfo"]; } else { $_pageinfo_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($_pageinfo_, "pages"));
                foreach ($context['_seq'] as $context["_key"] => $context["p"]) {
                    // line 98
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
\t\t\t\t\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['p'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 100
                echo "\t\t\t\t\t\t\t";
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
                // line 101
                echo "\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t";
            }
            // line 104
            echo "\t\t\t</div>
\t\t</div>
\t</section>
";
        }
        // line 108
        echo "
";
        // line 109
        if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
        if ($_category_) {
            // line 110
            echo "\t";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($_app_, "getSetting", array(0 => "user.kb_subscriptions"), "method")) {
                // line 111
                echo "\t\t\t<div class=\"dp-subscribe dp-subscribe-category ";
                if (isset($context["is_subscribed"])) { $_is_subscribed_ = $context["is_subscribed"]; } else { $_is_subscribed_ = null; }
                if ($_is_subscribed_) {
                    echo "dp-subscribe-on";
                }
                echo "\" id=\"dp_sb\">
\t\t\t\t";
                // line 112
                if (isset($context["is_subscribed"])) { $_is_subscribed_ = $context["is_subscribed"]; } else { $_is_subscribed_ = null; }
                if ($_is_subscribed_) {
                    // line 113
                    echo "\t\t\t\t\t";
                    if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.knowledgebase.unsubscribe-category", array("link" => $this->env->getExtension('routing')->getPath("user_articles_cat_togglesub", array("category_id" => $this->getAttribute($_category_, "id"), "auth" => $this->env->getExtension('deskpro_templating')->securityToken("subscribe_category")))));
                    echo "
\t\t\t\t";
                } else {
                    // line 115
                    echo "\t\t\t\t\t";
                    if (isset($context["category"])) { $_category_ = $context["category"]; } else { $_category_ = null; }
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.knowledgebase.subscribe-category", array("link" => $this->env->getExtension('routing')->getPath("user_articles_cat_togglesub", array("category_id" => $this->getAttribute($_category_, "id"), "auth" => $this->env->getExtension('deskpro_templating')->securityToken("subscribe_category")))));
                    echo "
\t\t\t\t";
                }
                // line 117
                echo "\t\t\t</div>
\t\t";
            }
        }
        // line 120
        echo "
<br style=\"clear: both; height: 1px; overflow: hidden\" />

";
    }

    public function getTemplateName()
    {
        return "UserBundle:Articles:browse.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  430 => 120,  411 => 113,  379 => 101,  322 => 94,  315 => 92,  289 => 84,  284 => 83,  255 => 73,  234 => 66,  1133 => 64,  1124 => 57,  1121 => 56,  1116 => 55,  1113 => 54,  1108 => 51,  1103 => 43,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 346,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 329,  996 => 320,  989 => 316,  985 => 315,  981 => 314,  977 => 313,  970 => 311,  966 => 310,  955 => 303,  952 => 302,  943 => 299,  936 => 296,  930 => 293,  919 => 290,  917 => 289,  908 => 287,  905 => 286,  896 => 280,  891 => 278,  877 => 270,  862 => 267,  857 => 265,  837 => 253,  832 => 250,  827 => 248,  821 => 247,  803 => 243,  778 => 235,  769 => 233,  765 => 232,  753 => 229,  746 => 225,  743 => 224,  735 => 220,  730 => 218,  720 => 213,  717 => 212,  712 => 209,  691 => 204,  678 => 202,  654 => 196,  587 => 184,  576 => 179,  539 => 171,  517 => 169,  471 => 155,  441 => 143,  437 => 142,  418 => 115,  386 => 125,  373 => 120,  304 => 106,  270 => 80,  265 => 77,  229 => 65,  477 => 135,  455 => 150,  448 => 112,  429 => 138,  407 => 95,  399 => 93,  389 => 126,  375 => 85,  358 => 116,  349 => 72,  335 => 68,  327 => 64,  298 => 58,  280 => 56,  249 => 46,  194 => 50,  142 => 24,  344 => 117,  318 => 93,  306 => 107,  295 => 57,  357 => 141,  300 => 130,  286 => 101,  276 => 122,  269 => 53,  254 => 112,  128 => 35,  237 => 44,  165 => 51,  122 => 33,  798 => 242,  770 => 113,  759 => 112,  748 => 226,  731 => 108,  721 => 107,  718 => 106,  708 => 104,  696 => 102,  617 => 188,  590 => 91,  553 => 87,  550 => 176,  540 => 84,  533 => 82,  500 => 74,  493 => 72,  489 => 71,  482 => 69,  467 => 67,  464 => 152,  458 => 64,  452 => 62,  449 => 61,  415 => 55,  382 => 124,  372 => 84,  361 => 82,  356 => 48,  339 => 97,  302 => 42,  285 => 40,  258 => 37,  123 => 34,  108 => 63,  424 => 135,  394 => 86,  380 => 80,  338 => 113,  319 => 66,  316 => 65,  312 => 110,  290 => 102,  267 => 57,  206 => 43,  110 => 25,  240 => 82,  224 => 78,  219 => 62,  217 => 73,  202 => 53,  186 => 57,  170 => 82,  100 => 20,  67 => 32,  14 => 1,  1096 => 291,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 332,  1013 => 275,  1007 => 274,  1002 => 272,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 300,  940 => 298,  937 => 248,  928 => 244,  926 => 243,  915 => 237,  912 => 236,  903 => 231,  898 => 230,  892 => 229,  889 => 228,  887 => 227,  884 => 226,  876 => 222,  874 => 269,  871 => 220,  863 => 214,  861 => 213,  858 => 212,  850 => 263,  843 => 206,  840 => 205,  815 => 245,  812 => 244,  808 => 199,  804 => 120,  799 => 196,  791 => 190,  785 => 238,  775 => 184,  771 => 183,  754 => 176,  728 => 170,  726 => 169,  723 => 168,  715 => 105,  711 => 163,  709 => 162,  706 => 161,  698 => 208,  694 => 156,  692 => 155,  689 => 154,  681 => 150,  677 => 149,  675 => 148,  663 => 143,  661 => 200,  650 => 195,  646 => 136,  629 => 129,  627 => 128,  625 => 127,  622 => 126,  598 => 119,  592 => 117,  586 => 115,  575 => 108,  566 => 103,  556 => 100,  554 => 177,  541 => 92,  536 => 91,  515 => 86,  511 => 85,  509 => 84,  488 => 164,  486 => 78,  483 => 77,  465 => 73,  463 => 72,  450 => 65,  432 => 60,  419 => 105,  371 => 46,  362 => 43,  353 => 73,  337 => 37,  333 => 35,  309 => 109,  303 => 31,  299 => 30,  291 => 28,  272 => 54,  261 => 95,  253 => 47,  239 => 86,  235 => 84,  213 => 60,  200 => 50,  198 => 52,  159 => 78,  149 => 187,  146 => 55,  131 => 55,  116 => 32,  79 => 21,  74 => 21,  71 => 19,  836 => 320,  817 => 319,  814 => 318,  811 => 317,  805 => 313,  787 => 115,  779 => 306,  776 => 305,  773 => 301,  761 => 296,  751 => 175,  747 => 293,  742 => 292,  739 => 291,  736 => 287,  724 => 214,  705 => 278,  702 => 277,  688 => 265,  680 => 263,  667 => 261,  662 => 260,  656 => 259,  649 => 258,  644 => 97,  641 => 194,  624 => 192,  613 => 187,  607 => 93,  597 => 225,  591 => 185,  584 => 218,  579 => 216,  563 => 88,  559 => 208,  551 => 98,  547 => 205,  537 => 201,  524 => 191,  512 => 185,  507 => 76,  504 => 182,  498 => 178,  485 => 176,  480 => 160,  472 => 171,  466 => 153,  460 => 71,  447 => 163,  442 => 162,  434 => 110,  428 => 156,  422 => 106,  404 => 149,  368 => 136,  364 => 83,  340 => 131,  334 => 114,  330 => 129,  325 => 111,  292 => 116,  287 => 115,  282 => 124,  279 => 82,  273 => 107,  266 => 91,  256 => 94,  252 => 93,  228 => 32,  218 => 78,  201 => 91,  64 => 13,  51 => 10,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 793,  2042 => 790,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 774,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 745,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 707,  1829 => 706,  1827 => 705,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 689,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 676,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 666,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 638,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 617,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 611,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 602,  1559 => 601,  1552 => 598,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 584,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 573,  1477 => 567,  1473 => 566,  1467 => 562,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 520,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 511,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 481,  1257 => 480,  1254 => 479,  1250 => 478,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 460,  1196 => 458,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 452,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 445,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 425,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 421,  1099 => 420,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 412,  1076 => 359,  1070 => 407,  1057 => 352,  1052 => 404,  1045 => 347,  1040 => 397,  1036 => 283,  1032 => 395,  1028 => 394,  1023 => 393,  1020 => 392,  1016 => 391,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 381,  979 => 379,  974 => 312,  967 => 373,  962 => 371,  958 => 304,  954 => 253,  950 => 368,  945 => 367,  942 => 250,  938 => 365,  934 => 364,  927 => 361,  923 => 292,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 272,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 207,  841 => 321,  835 => 312,  830 => 249,  826 => 309,  822 => 308,  818 => 246,  813 => 306,  810 => 305,  806 => 304,  802 => 197,  795 => 118,  792 => 239,  789 => 298,  784 => 295,  782 => 187,  777 => 291,  772 => 289,  768 => 182,  763 => 287,  760 => 231,  756 => 230,  752 => 284,  745 => 281,  741 => 280,  738 => 279,  732 => 171,  719 => 273,  714 => 280,  710 => 279,  704 => 267,  699 => 103,  695 => 264,  690 => 263,  687 => 203,  683 => 261,  679 => 101,  672 => 147,  668 => 256,  665 => 201,  658 => 141,  645 => 248,  640 => 247,  634 => 96,  628 => 193,  623 => 238,  619 => 237,  611 => 235,  606 => 234,  603 => 120,  599 => 232,  595 => 231,  583 => 114,  580 => 180,  573 => 221,  560 => 101,  543 => 172,  538 => 209,  534 => 208,  530 => 170,  526 => 89,  521 => 80,  518 => 204,  514 => 78,  510 => 202,  503 => 75,  496 => 197,  490 => 193,  484 => 192,  474 => 190,  470 => 188,  446 => 144,  440 => 184,  436 => 61,  431 => 157,  425 => 117,  416 => 104,  412 => 98,  408 => 112,  403 => 172,  400 => 111,  396 => 110,  392 => 169,  385 => 166,  381 => 48,  367 => 117,  363 => 155,  359 => 154,  355 => 115,  350 => 121,  346 => 71,  343 => 70,  328 => 139,  324 => 138,  313 => 134,  307 => 132,  301 => 85,  288 => 27,  283 => 125,  271 => 94,  257 => 114,  251 => 71,  238 => 34,  233 => 72,  195 => 42,  191 => 69,  187 => 47,  183 => 87,  130 => 28,  88 => 18,  76 => 20,  115 => 23,  95 => 42,  655 => 233,  651 => 232,  648 => 231,  637 => 223,  633 => 130,  621 => 219,  618 => 218,  615 => 121,  604 => 186,  600 => 226,  588 => 116,  585 => 204,  582 => 203,  571 => 104,  567 => 178,  555 => 207,  552 => 190,  549 => 215,  544 => 186,  542 => 185,  535 => 181,  531 => 90,  519 => 189,  516 => 176,  513 => 168,  508 => 172,  506 => 83,  499 => 198,  495 => 167,  491 => 165,  481 => 162,  478 => 68,  475 => 157,  469 => 154,  456 => 154,  451 => 186,  443 => 60,  439 => 147,  427 => 89,  423 => 58,  420 => 176,  409 => 54,  405 => 54,  401 => 132,  391 => 129,  387 => 49,  384 => 104,  378 => 123,  365 => 100,  360 => 120,  348 => 136,  336 => 111,  332 => 140,  329 => 134,  323 => 133,  310 => 133,  305 => 132,  277 => 23,  274 => 91,  263 => 51,  259 => 49,  247 => 110,  244 => 53,  241 => 68,  222 => 63,  210 => 59,  207 => 96,  204 => 94,  184 => 46,  181 => 45,  167 => 39,  157 => 36,  96 => 26,  421 => 134,  417 => 152,  414 => 151,  406 => 130,  398 => 129,  393 => 109,  390 => 108,  376 => 79,  369 => 137,  366 => 136,  352 => 115,  345 => 98,  342 => 72,  331 => 66,  326 => 96,  320 => 137,  317 => 61,  314 => 33,  311 => 90,  308 => 60,  297 => 101,  293 => 128,  281 => 93,  278 => 110,  275 => 99,  264 => 117,  260 => 76,  248 => 70,  245 => 90,  242 => 74,  231 => 87,  227 => 42,  215 => 83,  212 => 82,  209 => 81,  197 => 34,  177 => 43,  171 => 41,  161 => 36,  132 => 36,  121 => 48,  105 => 21,  99 => 34,  81 => 26,  77 => 20,  180 => 66,  176 => 54,  156 => 28,  143 => 30,  139 => 175,  118 => 25,  189 => 70,  185 => 67,  173 => 35,  166 => 68,  152 => 27,  174 => 42,  164 => 65,  154 => 35,  150 => 42,  137 => 33,  133 => 29,  127 => 27,  107 => 30,  102 => 28,  83 => 23,  78 => 20,  53 => 14,  23 => 3,  42 => 12,  138 => 30,  134 => 56,  109 => 25,  103 => 44,  97 => 19,  94 => 18,  84 => 38,  75 => 16,  69 => 15,  66 => 18,  54 => 10,  44 => 11,  230 => 72,  226 => 68,  203 => 73,  193 => 242,  188 => 68,  182 => 235,  178 => 30,  168 => 64,  163 => 79,  160 => 77,  155 => 44,  148 => 41,  145 => 40,  140 => 38,  136 => 37,  125 => 34,  120 => 51,  113 => 17,  101 => 27,  92 => 20,  89 => 17,  85 => 13,  73 => 13,  62 => 30,  59 => 13,  56 => 11,  41 => 4,  126 => 29,  119 => 32,  111 => 31,  106 => 28,  98 => 26,  93 => 26,  86 => 16,  70 => 34,  60 => 14,  28 => 4,  36 => 8,  114 => 49,  104 => 45,  91 => 17,  80 => 4,  63 => 15,  58 => 9,  40 => 9,  34 => 7,  45 => 14,  61 => 15,  55 => 15,  48 => 9,  39 => 10,  35 => 8,  31 => 6,  26 => 5,  21 => 4,  46 => 7,  29 => 5,  57 => 11,  50 => 11,  47 => 12,  38 => 8,  33 => 3,  49 => 8,  32 => 7,  246 => 45,  236 => 59,  232 => 43,  225 => 64,  221 => 40,  216 => 61,  214 => 98,  211 => 272,  208 => 75,  205 => 66,  199 => 65,  196 => 72,  190 => 58,  179 => 66,  175 => 61,  172 => 60,  169 => 52,  162 => 48,  158 => 45,  153 => 43,  151 => 56,  147 => 32,  144 => 42,  141 => 58,  135 => 51,  129 => 22,  124 => 26,  117 => 50,  112 => 20,  90 => 24,  87 => 16,  82 => 12,  72 => 13,  68 => 27,  65 => 9,  52 => 12,  43 => 10,  37 => 10,  30 => 7,  27 => 5,  25 => 5,  24 => 6,  22 => 5,  19 => 4,);
    }
}
