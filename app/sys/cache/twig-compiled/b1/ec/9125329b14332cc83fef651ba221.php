<?php

/* AdminBundle:Settings:labels.html.twig */
class __TwigTemplate_b1ec9125329b14332cc83fef651ba221 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("AdminBundle::layout.html.twig");

        $this->blocks = array(
            'pagebar' => array($this, 'block_pagebar'),
            'page_nav' => array($this, 'block_page_nav'),
            'page' => array($this, 'block_page'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "AdminBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 2
        $context["design"] = $this->env->loadTemplate("AdminBundle:Common:design-macros.html.twig");
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_pagebar($context, array $blocks = array())
    {
        // line 4
        echo "\t<ul>
\t\t<li>";
        // line 5
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.labels");
        echo "</li>
\t</ul>
";
    }

    // line 8
    public function block_page_nav($context, array $blocks = array())
    {
        // line 9
        echo "<div>
\t<section class=\"top\">
\t\t<header>
\t\t\t<h4>";
        // line 12
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.settings.label_types");
        echo "</h4>
\t\t</header>
\t\t<article>
\t\t\t<ul>
\t\t\t\t<li class=\"";
        // line 16
        if (isset($context["label_type"])) { $_label_type_ = $context["label_type"]; } else { $_label_type_ = null; }
        if (($_label_type_ == "all")) {
            echo "on";
        }
        echo "\">
\t\t\t\t\t<span class=\"count\" id=\"labels_count_all\">";
        // line 17
        if (isset($context["def_counts"])) { $_def_counts_ = $context["def_counts"]; } else { $_def_counts_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_def_counts_, "TOTAL", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_def_counts_, "TOTAL"), 0)) : (0)), "html", null, true);
        echo "</span>
\t\t\t\t\t<a href=\"";
        // line 18
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_labels", array("label_type" => "all")), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.all");
        echo "</a>
\t\t\t\t</li>
\t\t\t\t<li class=\"";
        // line 20
        if (isset($context["label_type"])) { $_label_type_ = $context["label_type"]; } else { $_label_type_ = null; }
        if (($_label_type_ == "articles")) {
            echo "on";
        }
        echo "\">
\t\t\t\t\t<span class=\"count\" id=\"labels_count_articles\">";
        // line 21
        if (isset($context["def_counts"])) { $_def_counts_ = $context["def_counts"]; } else { $_def_counts_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_def_counts_, "articles", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_def_counts_, "articles"), 0)) : (0)), "html", null, true);
        echo "</span>
\t\t\t\t\t<a href=\"";
        // line 22
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_labels", array("label_type" => "articles")), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.articles");
        echo "</a>
\t\t\t\t</li>
\t\t\t\t<li class=\"";
        // line 24
        if (isset($context["label_type"])) { $_label_type_ = $context["label_type"]; } else { $_label_type_ = null; }
        if (($_label_type_ == "deals")) {
            echo "on";
        }
        echo "\">
\t\t\t\t\t<span class=\"count\" id=\"labels_count_deals\">";
        // line 25
        if (isset($context["def_counts"])) { $_def_counts_ = $context["def_counts"]; } else { $_def_counts_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_def_counts_, "deals", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_def_counts_, "deals"), 0)) : (0)), "html", null, true);
        echo "</span>
\t\t\t\t\t<a href=\"";
        // line 26
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_labels", array("label_type" => "deals")), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.deals");
        echo "</a>
\t\t\t\t</li>
\t\t\t\t<li class=\"";
        // line 28
        if (isset($context["label_type"])) { $_label_type_ = $context["label_type"]; } else { $_label_type_ = null; }
        if (($_label_type_ == "downloads")) {
            echo "on";
        }
        echo "\">
\t\t\t\t\t<span class=\"count\" id=\"labels_count_downloads\">";
        // line 29
        if (isset($context["def_counts"])) { $_def_counts_ = $context["def_counts"]; } else { $_def_counts_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_def_counts_, "downloads", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_def_counts_, "downloads"), 0)) : (0)), "html", null, true);
        echo "</span>
\t\t\t\t\t<a href=\"";
        // line 30
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_labels", array("label_type" => "downloads")), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.downloads");
        echo "</a>
\t\t\t\t</li>
\t\t\t\t<li class=\"";
        // line 32
        if (isset($context["label_type"])) { $_label_type_ = $context["label_type"]; } else { $_label_type_ = null; }
        if (($_label_type_ == "feedback")) {
            echo "on";
        }
        echo "\">
\t\t\t\t\t<span class=\"count\" id=\"labels_count_feedback\">";
        // line 33
        if (isset($context["def_counts"])) { $_def_counts_ = $context["def_counts"]; } else { $_def_counts_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_def_counts_, "feedback", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_def_counts_, "feedback"), 0)) : (0)), "html", null, true);
        echo "</span>
\t\t\t\t\t<a href=\"";
        // line 34
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_labels", array("label_type" => "feedback")), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.feedback");
        echo "</a>
\t\t\t\t</li>
\t\t\t\t<li class=\"";
        // line 36
        if (isset($context["label_type"])) { $_label_type_ = $context["label_type"]; } else { $_label_type_ = null; }
        if (($_label_type_ == "news")) {
            echo "on";
        }
        echo "\">
\t\t\t\t\t<span class=\"count\" id=\"labels_count_news\">";
        // line 37
        if (isset($context["def_counts"])) { $_def_counts_ = $context["def_counts"]; } else { $_def_counts_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_def_counts_, "news", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_def_counts_, "news"), 0)) : (0)), "html", null, true);
        echo "</span>
\t\t\t\t\t<a href=\"";
        // line 38
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_labels", array("label_type" => "news")), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.news");
        echo "</a>
\t\t\t\t</li>
\t\t\t\t<li class=\"";
        // line 40
        if (isset($context["label_type"])) { $_label_type_ = $context["label_type"]; } else { $_label_type_ = null; }
        if (($_label_type_ == "organizations")) {
            echo "on";
        }
        echo "\">
\t\t\t\t\t<span class=\"count\" id=\"labels_count_organizations\">";
        // line 41
        if (isset($context["def_counts"])) { $_def_counts_ = $context["def_counts"]; } else { $_def_counts_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_def_counts_, "organizations", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_def_counts_, "organizations"), 0)) : (0)), "html", null, true);
        echo "</span>
\t\t\t\t\t<a href=\"";
        // line 42
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_labels", array("label_type" => "organizations")), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.organizations");
        echo "</a>
\t\t\t\t</li>
\t\t\t\t<li class=\"";
        // line 44
        if (isset($context["label_type"])) { $_label_type_ = $context["label_type"]; } else { $_label_type_ = null; }
        if (($_label_type_ == "people")) {
            echo "on";
        }
        echo "\">
\t\t\t\t\t<span class=\"count\" id=\"labels_count_people\">";
        // line 45
        if (isset($context["def_counts"])) { $_def_counts_ = $context["def_counts"]; } else { $_def_counts_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_def_counts_, "people", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_def_counts_, "people"), 0)) : (0)), "html", null, true);
        echo "</span>
\t\t\t\t\t<a href=\"";
        // line 46
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_labels", array("label_type" => "people")), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.people");
        echo "</a>
\t\t\t\t</li>
\t\t\t\t<li class=\"";
        // line 48
        if (isset($context["label_type"])) { $_label_type_ = $context["label_type"]; } else { $_label_type_ = null; }
        if (($_label_type_ == "tasks")) {
            echo "on";
        }
        echo "\">
\t\t\t\t\t<span class=\"count\" id=\"labels_count_tasks\">";
        // line 49
        if (isset($context["def_counts"])) { $_def_counts_ = $context["def_counts"]; } else { $_def_counts_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_def_counts_, "tasks", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_def_counts_, "tasks"), 0)) : (0)), "html", null, true);
        echo "</span>
\t\t\t\t\t<a href=\"";
        // line 50
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_labels", array("label_type" => "tasks")), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.tasks");
        echo "</a>
\t\t\t\t</li>
\t\t\t\t<li class=\"";
        // line 52
        if (isset($context["label_type"])) { $_label_type_ = $context["label_type"]; } else { $_label_type_ = null; }
        if (($_label_type_ == "tickets")) {
            echo "on";
        }
        echo "\">
\t\t\t\t\t<span class=\"count\" id=\"labels_count_tickets\">";
        // line 53
        if (isset($context["def_counts"])) { $_def_counts_ = $context["def_counts"]; } else { $_def_counts_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_def_counts_, "tickets", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_def_counts_, "tickets"), 0)) : (0)), "html", null, true);
        echo "</span>
\t\t\t\t\t<a href=\"";
        // line 54
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_labels", array("label_type" => "tickets")), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.tickets");
        echo "</a>
\t\t\t\t</li>
\t\t\t</ul>
\t\t</article>
\t</section>
</div>
";
    }

    // line 61
    public function block_page($context, array $blocks = array())
    {
        // line 62
        echo "<div
\tdata-element-handler=\"DeskPRO.Admin.ElementHandler.LabelsPage\"
\tdata-delete-url=\"";
        // line 64
        if (isset($context["label_type"])) { $_label_type_ = $context["label_type"]; } else { $_label_type_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_labels_del", array("label_type" => $_label_type_)), "html", null, true);
        echo "\"
\tdata-rename-url=\"";
        // line 65
        if (isset($context["label_type"])) { $_label_type_ = $context["label_type"]; } else { $_label_type_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_labels_rename", array("label_type" => $_label_type_)), "html", null, true);
        echo "\"
>

<nav class=\"page-top-hugnav\">
\t<ul>
\t\t<li>
\t\t\t";
        // line 71
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.settings.sort");
        echo ":
\t\t\t<a href=\"";
        // line 72
        if (isset($context["label_type"])) { $_label_type_ = $context["label_type"]; } else { $_label_type_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_labels", array("label_type" => $_label_type_)), "html", null, true);
        echo "\" style=\"";
        if (isset($context["order_by"])) { $_order_by_ = $context["order_by"]; } else { $_order_by_ = null; }
        if (((!$_order_by_) || ($_order_by_ == "alpha"))) {
            echo "font-weight: bold";
        }
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.alphabetical");
        echo "</a>
\t\t\t/
\t\t\t<a href=\"";
        // line 74
        if (isset($context["label_type"])) { $_label_type_ = $context["label_type"]; } else { $_label_type_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_labels", array("label_type" => $_label_type_, "order_by" => "count")), "html", null, true);
        echo "\" style=\"";
        if (isset($context["order_by"])) { $_order_by_ = $context["order_by"]; } else { $_order_by_ = null; }
        if (($_order_by_ == "count")) {
            echo "font-weight: bold";
        }
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.settings.usage_count");
        echo "</a>
\t\t</li>
\t</ul>
\t<br class=\"clear\" />
</nav>

<div class=\"new-label\">
\t<form action=\"";
        // line 81
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_labels_new"), "html", null, true);
        echo "\" method=\"POST\" id=\"new_label_form\">
\t\t<input type=\"hidden\" name=\"display_type\" value=\"";
        // line 82
        if (isset($context["label_type"])) { $_label_type_ = $context["label_type"]; } else { $_label_type_ = null; }
        echo twig_escape_filter($this->env, $_label_type_, "html", null, true);
        echo "\" />
\t\t<input type=\"text\" placeholder=\"";
        // line 83
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.settings.new_label");
        echo "\" value=\"\" name=\"label\" /> <button class=\"clean-white small\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.settings.add_label");
        echo "</button><br />
\t\t<div class=\"types\">
\t\t\t";
        // line 85
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.types");
        echo ":
\t\t\t<label><input type=\"checkbox\" name=\"types[]\" ";
        // line 86
        if (isset($context["label_type"])) { $_label_type_ = $context["label_type"]; } else { $_label_type_ = null; }
        if ((($_label_type_ == "all") || ($_label_type_ == "articles"))) {
            echo "checked=\"checked\"";
        }
        echo " value=\"articles\" /> ";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.articles");
        echo "</label>
\t\t\t<label><input type=\"checkbox\" name=\"types[]\" ";
        // line 87
        if (isset($context["label_type"])) { $_label_type_ = $context["label_type"]; } else { $_label_type_ = null; }
        if ((($_label_type_ == "all") || ($_label_type_ == "deals"))) {
            echo "checked=\"checked\"";
        }
        echo " value=\"deals\" /> ";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.deals");
        echo "</label>
\t\t\t<label><input type=\"checkbox\" name=\"types[]\" ";
        // line 88
        if (isset($context["label_type"])) { $_label_type_ = $context["label_type"]; } else { $_label_type_ = null; }
        if ((($_label_type_ == "all") || ($_label_type_ == "downloads"))) {
            echo "checked=\"checked\"";
        }
        echo " value=\"downloads\" /> ";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.downloads");
        echo "</label>
\t\t\t<label><input type=\"checkbox\" name=\"types[]\" ";
        // line 89
        if (isset($context["label_type"])) { $_label_type_ = $context["label_type"]; } else { $_label_type_ = null; }
        if ((($_label_type_ == "all") || ($_label_type_ == "feedback"))) {
            echo "checked=\"checked\"";
        }
        echo " value=\"feedback\" /> ";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.feedback");
        echo "</label>
\t\t\t<label><input type=\"checkbox\" name=\"types[]\" ";
        // line 90
        if (isset($context["label_type"])) { $_label_type_ = $context["label_type"]; } else { $_label_type_ = null; }
        if ((($_label_type_ == "all") || ($_label_type_ == "news"))) {
            echo "checked=\"checked\"";
        }
        echo " value=\"news\" /> ";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.news");
        echo "</label>
\t\t\t<label><input type=\"checkbox\" name=\"types[]\" ";
        // line 91
        if (isset($context["label_type"])) { $_label_type_ = $context["label_type"]; } else { $_label_type_ = null; }
        if ((($_label_type_ == "all") || ($_label_type_ == "organizations"))) {
            echo "checked=\"checked\"";
        }
        echo " value=\"organizations\" /> ";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.organizations");
        echo "</label>
\t\t\t<label><input type=\"checkbox\" name=\"types[]\" ";
        // line 92
        if (isset($context["label_type"])) { $_label_type_ = $context["label_type"]; } else { $_label_type_ = null; }
        if ((($_label_type_ == "all") || ($_label_type_ == "people"))) {
            echo "checked=\"checked\"";
        }
        echo " value=\"people\" /> ";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.people");
        echo "</label>
\t\t\t<label><input type=\"checkbox\" name=\"types[]\" ";
        // line 93
        if (isset($context["label_type"])) { $_label_type_ = $context["label_type"]; } else { $_label_type_ = null; }
        if ((($_label_type_ == "all") || ($_label_type_ == "tasks"))) {
            echo "checked=\"checked\"";
        }
        echo " value=\"tasks\" /> ";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.tasks");
        echo "</label>
\t\t\t<label><input type=\"checkbox\" name=\"types[]\" ";
        // line 94
        if (isset($context["label_type"])) { $_label_type_ = $context["label_type"]; } else { $_label_type_ = null; }
        if ((($_label_type_ == "all") || ($_label_type_ == "tickets"))) {
            echo "checked=\"checked\"";
        }
        echo " value=\"tickets\" /> ";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.tickets");
        echo "</label>
\t\t</div>
\t</form>
</div>

<ul class=\"labels-list\" id=\"labels_list\">
\t";
        // line 100
        if (isset($context["labels"])) { $_labels_ = $context["labels"]; } else { $_labels_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_labels_);
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
        foreach ($context['_seq'] as $context["label"] => $context["count"]) {
            // line 101
            echo "\t\t";
            $this->env->loadTemplate("AdminBundle:Settings:labels-row.html.twig")->display($context);
            // line 102
            echo "\t";
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
        unset($context['_seq'], $context['_iterated'], $context['label'], $context['count'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 103
        echo "</ul>
<br class=\"clear\" />

</div>
";
    }

    public function getTemplateName()
    {
        return "AdminBundle:Settings:labels.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  666 => 300,  453 => 203,  639 => 110,  568 => 254,  520 => 249,  657 => 287,  572 => 251,  609 => 459,  20 => 1,  659 => 274,  562 => 252,  548 => 238,  558 => 94,  479 => 82,  589 => 100,  457 => 211,  413 => 172,  953 => 406,  948 => 403,  935 => 394,  929 => 391,  916 => 382,  864 => 365,  844 => 348,  816 => 342,  807 => 339,  801 => 338,  774 => 337,  766 => 328,  737 => 314,  685 => 300,  664 => 294,  635 => 281,  593 => 445,  546 => 414,  532 => 240,  865 => 221,  852 => 216,  838 => 208,  820 => 201,  781 => 333,  764 => 178,  725 => 164,  632 => 283,  602 => 265,  565 => 253,  529 => 282,  505 => 267,  487 => 213,  473 => 221,  1853 => 842,  1849 => 841,  1839 => 837,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 813,  1778 => 812,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 782,  1705 => 781,  1688 => 770,  1681 => 769,  1677 => 768,  1667 => 764,  1656 => 762,  1648 => 757,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 714,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 695,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 651,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 610,  1289 => 607,  1276 => 604,  1263 => 601,  1244 => 596,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 565,  1181 => 561,  1171 => 557,  1158 => 550,  1114 => 530,  1104 => 526,  1072 => 512,  1062 => 508,  1024 => 494,  1014 => 490,  1000 => 482,  990 => 478,  980 => 474,  960 => 466,  918 => 448,  888 => 376,  834 => 346,  673 => 342,  636 => 284,  462 => 222,  454 => 103,  1144 => 542,  1139 => 399,  1131 => 399,  1127 => 397,  1110 => 396,  1092 => 391,  1089 => 390,  1086 => 520,  1084 => 388,  1063 => 387,  1060 => 386,  1055 => 385,  1050 => 384,  1035 => 372,  1019 => 267,  1003 => 263,  959 => 251,  900 => 377,  880 => 434,  870 => 430,  867 => 366,  859 => 362,  848 => 215,  839 => 197,  828 => 191,  823 => 188,  809 => 181,  800 => 178,  797 => 177,  794 => 176,  786 => 174,  740 => 162,  734 => 313,  703 => 354,  693 => 350,  630 => 278,  626 => 140,  614 => 275,  610 => 103,  581 => 277,  564 => 229,  525 => 235,  722 => 162,  697 => 256,  674 => 249,  671 => 425,  577 => 257,  569 => 97,  557 => 368,  502 => 229,  497 => 240,  445 => 197,  729 => 159,  684 => 261,  676 => 299,  669 => 254,  660 => 145,  647 => 286,  643 => 244,  601 => 287,  570 => 273,  522 => 200,  501 => 265,  296 => 67,  374 => 183,  631 => 111,  616 => 281,  608 => 266,  605 => 16,  596 => 102,  574 => 165,  561 => 209,  527 => 233,  433 => 93,  388 => 137,  426 => 177,  383 => 182,  461 => 246,  370 => 148,  395 => 221,  294 => 121,  223 => 78,  220 => 77,  492 => 395,  468 => 201,  444 => 193,  410 => 229,  397 => 174,  377 => 159,  262 => 113,  250 => 98,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 2108,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 1383,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 1303,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 1173,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 1041,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 970,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 538,  1126 => 916,  1106 => 900,  1100 => 897,  1026 => 841,  997 => 822,  983 => 816,  975 => 402,  956 => 800,  939 => 786,  902 => 753,  894 => 749,  879 => 373,  757 => 631,  727 => 608,  716 => 605,  670 => 297,  528 => 221,  476 => 253,  435 => 208,  354 => 153,  341 => 212,  192 => 83,  321 => 122,  243 => 54,  793 => 350,  780 => 348,  758 => 177,  700 => 312,  686 => 150,  652 => 274,  638 => 282,  620 => 139,  545 => 259,  523 => 110,  494 => 10,  459 => 226,  438 => 195,  351 => 88,  347 => 173,  402 => 222,  268 => 65,  430 => 201,  411 => 201,  379 => 219,  322 => 83,  315 => 118,  289 => 129,  284 => 128,  255 => 115,  234 => 81,  1133 => 400,  1124 => 534,  1121 => 56,  1116 => 55,  1113 => 403,  1108 => 51,  1103 => 394,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 500,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 329,  996 => 262,  989 => 316,  985 => 315,  981 => 257,  977 => 313,  970 => 91,  966 => 416,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 244,  919 => 290,  917 => 289,  908 => 444,  905 => 378,  896 => 280,  891 => 227,  877 => 270,  862 => 267,  857 => 265,  837 => 347,  832 => 250,  827 => 248,  821 => 247,  803 => 179,  778 => 389,  769 => 165,  765 => 386,  753 => 328,  746 => 175,  743 => 318,  735 => 170,  730 => 218,  720 => 363,  717 => 362,  712 => 156,  691 => 204,  678 => 149,  654 => 144,  587 => 14,  576 => 179,  539 => 241,  517 => 217,  471 => 212,  441 => 239,  437 => 101,  418 => 201,  386 => 164,  373 => 149,  304 => 125,  270 => 169,  265 => 161,  229 => 91,  477 => 138,  455 => 224,  448 => 242,  429 => 179,  407 => 119,  399 => 193,  389 => 176,  375 => 217,  358 => 133,  349 => 131,  335 => 128,  327 => 124,  298 => 144,  280 => 102,  249 => 205,  194 => 69,  142 => 49,  344 => 139,  318 => 181,  306 => 111,  295 => 74,  357 => 154,  300 => 114,  286 => 80,  276 => 87,  269 => 133,  254 => 100,  128 => 61,  237 => 118,  165 => 72,  122 => 37,  798 => 337,  770 => 179,  759 => 112,  748 => 337,  731 => 108,  721 => 305,  718 => 307,  708 => 295,  696 => 147,  617 => 461,  590 => 226,  553 => 264,  550 => 156,  540 => 84,  533 => 255,  500 => 397,  493 => 225,  489 => 257,  482 => 223,  467 => 210,  464 => 215,  458 => 220,  452 => 217,  449 => 132,  415 => 181,  382 => 162,  372 => 215,  361 => 155,  356 => 215,  339 => 126,  302 => 125,  285 => 175,  258 => 71,  123 => 40,  108 => 35,  424 => 149,  394 => 86,  380 => 121,  338 => 155,  319 => 113,  316 => 131,  312 => 87,  290 => 105,  267 => 93,  206 => 82,  110 => 26,  240 => 93,  224 => 50,  219 => 49,  217 => 80,  202 => 126,  186 => 42,  170 => 73,  100 => 35,  67 => 17,  14 => 1,  1096 => 524,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 836,  1013 => 275,  1007 => 274,  1002 => 823,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 402,  940 => 298,  937 => 395,  928 => 452,  926 => 243,  915 => 239,  912 => 236,  903 => 231,  898 => 440,  892 => 229,  889 => 748,  887 => 227,  884 => 374,  876 => 222,  874 => 215,  871 => 368,  863 => 214,  861 => 213,  858 => 212,  850 => 352,  843 => 206,  840 => 406,  815 => 245,  812 => 244,  808 => 199,  804 => 395,  799 => 196,  791 => 193,  785 => 238,  775 => 184,  771 => 183,  754 => 340,  728 => 309,  726 => 169,  723 => 168,  715 => 105,  711 => 152,  709 => 162,  706 => 155,  698 => 208,  694 => 308,  692 => 155,  689 => 302,  681 => 300,  677 => 149,  675 => 148,  663 => 276,  661 => 277,  650 => 246,  646 => 112,  629 => 305,  627 => 108,  625 => 279,  622 => 270,  598 => 174,  592 => 117,  586 => 264,  575 => 257,  566 => 271,  556 => 244,  554 => 240,  541 => 243,  536 => 241,  515 => 209,  511 => 269,  509 => 244,  488 => 208,  486 => 220,  483 => 341,  465 => 223,  463 => 216,  450 => 194,  432 => 211,  419 => 100,  371 => 182,  362 => 80,  353 => 78,  337 => 140,  333 => 86,  309 => 190,  303 => 70,  299 => 148,  291 => 176,  272 => 93,  261 => 91,  253 => 89,  239 => 85,  235 => 75,  213 => 78,  200 => 45,  198 => 86,  159 => 53,  149 => 45,  146 => 51,  131 => 50,  116 => 30,  79 => 20,  74 => 26,  71 => 23,  836 => 320,  817 => 398,  814 => 200,  811 => 317,  805 => 313,  787 => 657,  779 => 169,  776 => 305,  773 => 347,  761 => 296,  751 => 163,  747 => 325,  742 => 336,  739 => 171,  736 => 317,  724 => 214,  705 => 278,  702 => 601,  688 => 113,  680 => 278,  667 => 296,  662 => 293,  656 => 418,  649 => 285,  644 => 284,  641 => 241,  624 => 109,  613 => 460,  607 => 273,  597 => 270,  591 => 267,  584 => 259,  579 => 234,  563 => 96,  559 => 245,  551 => 249,  547 => 95,  537 => 90,  524 => 220,  512 => 227,  507 => 76,  504 => 213,  498 => 142,  485 => 256,  480 => 254,  472 => 217,  466 => 210,  460 => 221,  447 => 215,  442 => 196,  434 => 212,  428 => 185,  422 => 176,  404 => 149,  368 => 81,  364 => 156,  340 => 170,  334 => 211,  330 => 148,  325 => 134,  292 => 112,  287 => 117,  282 => 72,  279 => 111,  273 => 170,  266 => 90,  256 => 61,  252 => 109,  228 => 79,  218 => 77,  201 => 69,  64 => 19,  51 => 12,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 1705,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 737,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 731,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 725,  1559 => 601,  1552 => 720,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 708,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 645,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 624,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 1001,  1257 => 1000,  1254 => 479,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 569,  1196 => 958,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 938,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 388,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 898,  1099 => 393,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 880,  1076 => 359,  1070 => 875,  1057 => 352,  1052 => 504,  1045 => 382,  1040 => 377,  1036 => 283,  1032 => 496,  1028 => 274,  1023 => 393,  1020 => 392,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 821,  979 => 379,  974 => 256,  967 => 373,  962 => 803,  958 => 801,  954 => 253,  950 => 368,  945 => 367,  942 => 460,  938 => 365,  934 => 364,  927 => 361,  923 => 387,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 272,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 410,  841 => 210,  835 => 312,  830 => 249,  826 => 202,  822 => 308,  818 => 246,  813 => 183,  810 => 340,  806 => 180,  802 => 198,  795 => 336,  792 => 239,  789 => 349,  784 => 188,  782 => 187,  777 => 291,  772 => 289,  768 => 334,  763 => 327,  760 => 329,  756 => 230,  752 => 176,  745 => 281,  741 => 280,  738 => 379,  732 => 171,  719 => 279,  714 => 264,  710 => 310,  704 => 267,  699 => 303,  695 => 285,  690 => 263,  687 => 279,  683 => 346,  679 => 298,  672 => 255,  668 => 247,  665 => 201,  658 => 244,  645 => 270,  640 => 285,  634 => 413,  628 => 466,  623 => 107,  619 => 298,  611 => 268,  606 => 279,  603 => 267,  599 => 242,  595 => 132,  583 => 263,  580 => 99,  573 => 274,  560 => 268,  543 => 172,  538 => 257,  534 => 405,  530 => 202,  526 => 213,  521 => 226,  518 => 233,  514 => 232,  510 => 202,  503 => 266,  496 => 226,  490 => 214,  484 => 394,  474 => 202,  470 => 231,  446 => 241,  440 => 102,  436 => 189,  431 => 186,  425 => 193,  416 => 231,  412 => 230,  408 => 141,  403 => 194,  400 => 225,  396 => 93,  392 => 139,  385 => 186,  381 => 185,  367 => 147,  363 => 164,  359 => 79,  355 => 326,  350 => 94,  346 => 140,  343 => 143,  328 => 135,  324 => 123,  313 => 81,  307 => 108,  301 => 124,  288 => 116,  283 => 62,  271 => 105,  257 => 101,  251 => 58,  238 => 53,  233 => 81,  195 => 67,  191 => 49,  187 => 65,  183 => 85,  130 => 32,  88 => 33,  76 => 26,  115 => 28,  95 => 43,  655 => 275,  651 => 232,  648 => 231,  637 => 241,  633 => 272,  621 => 462,  618 => 277,  615 => 268,  604 => 186,  600 => 271,  588 => 305,  585 => 261,  582 => 260,  571 => 242,  567 => 95,  555 => 250,  552 => 190,  549 => 208,  544 => 230,  542 => 290,  535 => 256,  531 => 254,  519 => 87,  516 => 248,  513 => 216,  508 => 230,  506 => 401,  499 => 241,  495 => 239,  491 => 145,  481 => 218,  478 => 235,  475 => 184,  469 => 197,  456 => 204,  451 => 243,  443 => 132,  439 => 129,  427 => 185,  423 => 187,  420 => 208,  409 => 179,  405 => 94,  401 => 148,  391 => 173,  387 => 92,  384 => 160,  378 => 91,  365 => 161,  360 => 89,  348 => 191,  336 => 125,  332 => 150,  329 => 85,  323 => 135,  310 => 127,  305 => 115,  277 => 101,  274 => 135,  263 => 64,  259 => 62,  247 => 96,  244 => 86,  241 => 150,  222 => 105,  210 => 60,  207 => 73,  204 => 89,  184 => 41,  181 => 41,  167 => 38,  157 => 36,  96 => 31,  421 => 147,  417 => 71,  414 => 142,  406 => 170,  398 => 165,  393 => 177,  390 => 164,  376 => 85,  369 => 90,  366 => 174,  352 => 140,  345 => 213,  342 => 87,  331 => 140,  326 => 102,  320 => 130,  317 => 82,  314 => 136,  311 => 191,  308 => 141,  297 => 113,  293 => 119,  281 => 174,  278 => 71,  275 => 107,  264 => 103,  260 => 107,  248 => 77,  245 => 87,  242 => 86,  231 => 52,  227 => 113,  215 => 88,  212 => 48,  209 => 74,  197 => 70,  177 => 58,  171 => 57,  161 => 42,  132 => 62,  121 => 41,  105 => 25,  99 => 34,  81 => 22,  77 => 26,  180 => 58,  176 => 62,  156 => 52,  143 => 33,  139 => 66,  118 => 46,  189 => 88,  185 => 46,  173 => 54,  166 => 41,  152 => 50,  174 => 40,  164 => 113,  154 => 53,  150 => 52,  137 => 37,  133 => 41,  127 => 41,  107 => 50,  102 => 41,  83 => 24,  78 => 25,  53 => 12,  23 => 6,  42 => 7,  138 => 54,  134 => 52,  109 => 40,  103 => 32,  97 => 34,  94 => 25,  84 => 30,  75 => 31,  69 => 21,  66 => 20,  54 => 16,  44 => 8,  230 => 74,  226 => 79,  203 => 97,  193 => 44,  188 => 65,  182 => 45,  178 => 53,  168 => 57,  163 => 49,  160 => 68,  155 => 36,  148 => 34,  145 => 47,  140 => 40,  136 => 32,  125 => 36,  120 => 46,  113 => 39,  101 => 39,  92 => 33,  89 => 28,  85 => 23,  73 => 18,  62 => 15,  59 => 12,  56 => 11,  41 => 5,  126 => 40,  119 => 36,  111 => 46,  106 => 37,  98 => 24,  93 => 27,  86 => 21,  70 => 25,  60 => 16,  28 => 1,  36 => 5,  114 => 44,  104 => 36,  91 => 22,  80 => 27,  63 => 19,  58 => 14,  40 => 6,  34 => 4,  45 => 8,  61 => 18,  55 => 13,  48 => 9,  39 => 7,  35 => 4,  31 => 3,  26 => 1,  21 => 2,  46 => 10,  29 => 2,  57 => 17,  50 => 11,  47 => 9,  38 => 5,  33 => 6,  49 => 8,  32 => 3,  246 => 96,  236 => 91,  232 => 92,  225 => 82,  221 => 110,  216 => 65,  214 => 102,  211 => 101,  208 => 100,  205 => 46,  199 => 71,  196 => 73,  190 => 58,  179 => 63,  175 => 62,  172 => 77,  169 => 57,  162 => 37,  158 => 70,  153 => 63,  151 => 62,  147 => 50,  144 => 67,  141 => 55,  135 => 45,  129 => 30,  124 => 29,  117 => 28,  112 => 72,  90 => 22,  87 => 31,  82 => 20,  72 => 18,  68 => 22,  65 => 20,  52 => 9,  43 => 7,  37 => 5,  30 => 3,  27 => 2,  25 => 2,  24 => 3,  22 => 2,  19 => 1,);
    }
}
