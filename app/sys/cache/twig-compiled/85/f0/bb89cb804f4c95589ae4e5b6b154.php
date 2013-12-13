<?php

/* ReportBundle:AgentFeedback:feed.html.twig */
class __TwigTemplate_85f0bb89cb804f4c95589ae4e5b6b154 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("ReportBundle::layout.html.twig");

        $this->blocks = array(
            'nav_block' => array($this, 'block_nav_block'),
            'page_js_exec' => array($this, 'block_page_js_exec'),
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
        $context["this_page"] = "report_agent_feedback_feed";
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 4
    public function block_nav_block($context, array $blocks = array())
    {
        // line 5
        echo "    ";
        $this->env->loadTemplate("ReportBundle:AgentFeedback:nav.html.twig")->display($context);
    }

    // line 8
    public function block_page_js_exec($context, array $blocks = array())
    {
        // line 9
        echo "<script type=\"text/javascript\" xmlns=\"http://www.w3.org/1999/html\">
    \$(document).ready(
            function() {
                \$('.timeago').timeago();
            }
    );
</script>
";
    }

    // line 18
    public function block_pagebar($context, array $blocks = array())
    {
    }

    // line 21
    public function block_page($context, array $blocks = array())
    {
        // line 22
        echo "<style type=\"text/css\">
\t.feedback-table {
\t\tmargin-bottom: 25px;
\t\twidth: 800px;
\t}
    .feedback-table > table > thead > tr > th {
\t\ttext-align: left;
\t\tpadding: 8px !important;
\t\tpadding-top: 10px !important;
\t}

\t.feedback-table > table > thead > tr > th .timeago {
\t\tfont-size: 11px;
\t\tfont-weight: normal;
\t}

\t.feedback-table > table > thead > tr > th > h3 {
\t\tfont-size: 12pt;
\t\tfont-weight: normal;
\t}

\t.feedback-table a.user-link {
\t\tdisplay: block;
\t\tpadding-left: 29px;
\t\tline-height: 31px;
\t}

\t.user-message {
\t\tmargin: 0px 30px 0 29px;
\t\tfont-size: 11px;
\t\tpadding: 6px;
\t\t-webkit-border-radius: 5px;
\t\t-moz-border-radius: 5px;
\t\tborder-radius: 5px;
\t}

\t.user-message i {
\t\tfont-size: 15pt;
\t\tfont-weight: normal;
\t\tfont-style: normal;
\t\tdisplay: inline;
\t\ttext-align: center;
\t\tline-height: 10px;
\t\tposition: relative;
\t\ttop: 2px;
\t}

\t.user-message.rating-up {
\t\tbackground-color: #D2FCD5;
\t}

\t.user-message.rating-neutral {
\t\tbackground-color: #E8E8E8;
\t}

\t.user-message.rating-down {
\t\tbackground-color: #FFE7DF;
\t}
</style>

";
        // line 82
        if (isset($context["feedback"])) { $_feedback_ = $context["feedback"]; } else { $_feedback_ = null; }
        if (twig_test_empty($_feedback_)) {
            // line 83
            echo "\t<p>There is no feedback.</p>
";
        } else {
            // line 85
            echo "\t";
            if (isset($context["feedback"])) { $_feedback_ = $context["feedback"]; } else { $_feedback_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_feedback_);
            foreach ($context['_seq'] as $context["_key"] => $context["f"]) {
                // line 86
                echo "\t\t<div class=\"feedback-table check-grid item-list\">
\t\t\t<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">
\t\t\t\t<thead>
\t\t\t\t\t<tr>
\t\t\t\t\t\t<th>
\t\t\t\t\t\t\t<span style=\"float:right\">
\t\t\t\t\t\t\t\t<time class=\"timeago\" datetime=\"";
                // line 92
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_f_, "date_created"), "c"), "html", null, true);
                echo "\"></time>
\t\t\t\t\t\t\t</span>

\t\t\t\t\t\t\t<h3>
\t\t\t\t\t\t\t\tTicket #";
                // line 96
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_f_, "ticket"), "id"), "html", null, true);
                echo ":
\t\t\t\t\t\t\t\t<a href=\"";
                // line 97
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
                echo "/agent/#app.tickets,t:";
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_f_, "ticket"), "id"), "html", null, true);
                echo "\">";
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_f_, "ticket"), "subject"), "html", null, true);
                echo "</a>
\t\t\t\t\t\t\t</h3>
\t\t\t\t\t\t</th>
\t\t\t\t\t</tr>
\t\t\t\t</thead>
\t\t\t\t<tbody>
\t\t\t\t\t<tr>
\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">
\t\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t\t<td width=\"70%\" style=\"padding: 0\">
\t\t\t\t\t\t\t\t\t\t<a
\t\t\t\t\t\t\t\t\t\t\tclass=\"user-link\"
\t\t\t\t\t\t\t\t\t\t\tstyle=\"background-image: url(";
                // line 110
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_f_, "ticket"), "person"), "getPictureUrl", array(0 => 24), "method"), "html", null, true);
                echo ")\"
\t\t\t\t\t\t\t\t\t\t\thref=\"";
                // line 111
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
                echo "/agent/#app.people,p:";
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_f_, "ticket"), "person"), "id"), "html", null, true);
                echo "\"
\t\t\t\t\t\t\t\t\t\t>";
                // line 112
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_f_, "ticket"), "person"), "name"), "html", null, true);
                echo " &lt;";
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_f_, "ticket"), "person"), "email_address"), "html", null, true);
                echo "&gt;</a>
\t\t\t\t\t\t\t\t\t\t<div class=\"user-message rating-";
                // line 113
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                if (($this->getAttribute($_f_, "rating") == 1)) {
                    echo "up";
                } elseif (($this->getAttribute($_f_, "rating") == (-1))) {
                    echo "down";
                } else {
                    echo "neutral";
                }
                echo "\">
\t\t\t\t\t\t\t\t\t\t\t\t<i>
\t\t\t\t\t\t\t\t\t\t\t\t\t";
                // line 115
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                if (($this->getAttribute($_f_, "rating") == 1)) {
                    // line 116
                    echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t☺
\t\t\t\t\t\t\t\t\t\t\t\t\t";
                } elseif (($this->getAttribute($_f_, "rating") == (-1))) {
                    // line 118
                    echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t☹
\t\t\t\t\t\t\t\t\t\t\t\t\t";
                } else {
                    // line 120
                    echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t&mdash;
\t\t\t\t\t\t\t\t\t\t\t\t\t";
                }
                // line 122
                echo "\t\t\t\t\t\t\t\t\t\t\t\t</i>
\t\t\t\t\t\t\t\t\t\t\t";
                // line 123
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                if ((!$this->getAttribute($_f_, "message"))) {
                    echo "<em>User did not leave a message.</em>";
                } else {
                    if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_f_, "message"), "html", null, true);
                }
                // line 124
                echo "\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t\t<td width=\"30%\">
\t\t\t\t\t\t\t\t\t\t<strong>Agent:</strong><br />
\t\t\t\t\t\t\t\t\t\t<a
\t\t\t\t\t\t\t\t\t\t\tclass=\"user-link\"
\t\t\t\t\t\t\t\t\t\t\tstyle=\"background-image: url(";
                // line 130
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_f_, "ticket_message"), "person"), "getPictureUrl", array(0 => 24), "method"), "html", null, true);
                echo ")\"
\t\t\t\t\t\t\t\t\t\t\thref=\"";
                // line 131
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
                echo "/agent/#app.people,p:";
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_f_, "ticket_message"), "person"), "id"), "html", null, true);
                echo "\"
\t\t\t\t\t\t\t\t\t\t>";
                // line 132
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_f_, "ticket_message"), "person"), "name"), "html", null, true);
                echo "</a>
\t\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t</table>
\t\t\t\t\t\t</td>
\t\t\t\t\t</tr>
\t\t\t\t</tbody>
\t\t\t</table>
\t\t</div>
\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['f'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 142
            echo "
\tShowing Page: ";
            // line 143
            if (isset($context["page"])) { $_page_ = $context["page"]; } else { $_page_ = null; }
            echo twig_escape_filter($this->env, ($_page_ + 1), "html", null, true);
            echo " &nbsp;&nbsp;
\t";
            // line 144
            if (isset($context["page"])) { $_page_ = $context["page"]; } else { $_page_ = null; }
            if (isset($context["count"])) { $_count_ = $context["count"]; } else { $_count_ = null; }
            $context["has_next"] = ((($_page_ + 1) * 20) < $_count_);
            // line 145
            echo "\t";
            if (isset($context["page"])) { $_page_ = $context["page"]; } else { $_page_ = null; }
            if (($_page_ != 0)) {
                // line 146
                echo "\t\t<a href=\"";
                if (isset($context["page"])) { $_page_ = $context["page"]; } else { $_page_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_agent_feedback_feed", array("page" => ($_page_ - 1))), "html", null, true);
                echo "\">Previous Page</a>";
                if (isset($context["has_next"])) { $_has_next_ = $context["has_next"]; } else { $_has_next_ = null; }
                if ($_has_next_) {
                    echo ",";
                }
                // line 147
                echo "\t";
            }
            // line 148
            echo "
\t";
            // line 149
            if (isset($context["has_next"])) { $_has_next_ = $context["has_next"]; } else { $_has_next_ = null; }
            if ($_has_next_) {
                // line 150
                echo "\t\t<a href=\"";
                if (isset($context["page"])) { $_page_ = $context["page"]; } else { $_page_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_agent_feedback_feed", array("page" => ($_page_ + 1))), "html", null, true);
                echo "\">Next Page</a>
\t";
            }
        }
    }

    public function getTemplateName()
    {
        return "ReportBundle:AgentFeedback:feed.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  313 => 150,  310 => 149,  307 => 148,  304 => 147,  295 => 146,  291 => 145,  282 => 143,  279 => 142,  254 => 131,  249 => 130,  233 => 123,  226 => 120,  222 => 118,  218 => 116,  215 => 115,  203 => 113,  195 => 112,  187 => 111,  182 => 110,  159 => 97,  146 => 92,  128 => 83,  125 => 82,  63 => 22,  60 => 21,  44 => 9,  33 => 4,  399 => 79,  388 => 77,  380 => 76,  377 => 74,  373 => 72,  357 => 69,  346 => 68,  334 => 66,  316 => 63,  305 => 61,  298 => 60,  287 => 144,  251 => 51,  244 => 49,  241 => 124,  235 => 47,  231 => 45,  227 => 43,  219 => 40,  199 => 36,  192 => 35,  181 => 33,  174 => 32,  156 => 29,  145 => 27,  138 => 86,  126 => 24,  114 => 22,  91 => 18,  82 => 16,  68 => 14,  57 => 12,  48 => 10,  39 => 8,  36 => 5,  28 => 1,  23 => 2,  19 => 1,  436 => 155,  430 => 156,  428 => 155,  424 => 153,  421 => 152,  416 => 146,  410 => 159,  408 => 152,  401 => 151,  391 => 148,  387 => 147,  383 => 146,  379 => 144,  376 => 143,  371 => 141,  366 => 71,  361 => 65,  356 => 59,  349 => 162,  347 => 143,  344 => 142,  342 => 141,  336 => 137,  330 => 134,  318 => 129,  315 => 128,  308 => 126,  297 => 121,  290 => 119,  278 => 114,  272 => 111,  260 => 106,  257 => 105,  250 => 103,  239 => 98,  232 => 96,  221 => 91,  210 => 86,  201 => 85,  190 => 84,  184 => 82,  179 => 81,  167 => 75,  157 => 68,  154 => 96,  151 => 66,  149 => 65,  142 => 60,  140 => 59,  108 => 30,  103 => 20,  98 => 28,  94 => 27,  88 => 24,  83 => 22,  78 => 20,  74 => 19,  70 => 18,  66 => 17,  58 => 15,  54 => 14,  49 => 12,  45 => 11,  41 => 8,  37 => 9,  673 => 200,  669 => 198,  664 => 195,  649 => 193,  634 => 191,  631 => 190,  617 => 189,  603 => 188,  599 => 186,  587 => 184,  581 => 183,  569 => 181,  563 => 180,  551 => 178,  545 => 177,  533 => 175,  527 => 174,  515 => 172,  506 => 171,  503 => 170,  497 => 169,  494 => 168,  481 => 165,  473 => 164,  467 => 163,  463 => 162,  460 => 161,  457 => 160,  454 => 159,  450 => 158,  446 => 157,  442 => 156,  423 => 155,  419 => 154,  398 => 150,  392 => 152,  389 => 151,  386 => 150,  368 => 149,  363 => 148,  360 => 70,  343 => 146,  338 => 143,  328 => 141,  323 => 64,  317 => 136,  314 => 135,  280 => 57,  269 => 55,  262 => 132,  237 => 85,  230 => 122,  223 => 42,  216 => 38,  209 => 69,  202 => 65,  172 => 37,  169 => 36,  163 => 30,  155 => 31,  152 => 30,  147 => 29,  141 => 28,  136 => 25,  132 => 85,  112 => 21,  107 => 20,  104 => 19,  100 => 18,  90 => 15,  77 => 14,  72 => 13,  62 => 16,  55 => 18,  52 => 6,  47 => 3,  29 => 5,  27 => 1,);
    }
}
