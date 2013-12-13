<?php

/* ReportBundle:AgentFeedback:summary.html.twig */
class __TwigTemplate_8b8de596215ef3a0c205f289f9ef1e32 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("ReportBundle::layout.html.twig");

        $this->blocks = array(
            'nav_block' => array($this, 'block_nav_block'),
            'page_js_exec' => array($this, 'block_page_js_exec'),
            'html_head' => array($this, 'block_html_head'),
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
        $context["this_page"] = "report_agent_feedback_index";
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 4
    public function block_nav_block($context, array $blocks = array())
    {
        // line 5
        $this->env->loadTemplate("ReportBundle:AgentFeedback:nav.html.twig")->display($context);
    }

    // line 8
    public function block_page_js_exec($context, array $blocks = array())
    {
        // line 9
        echo "<script type=\"text/javascript\">
    \$(document).ready(function(){
        \$('.datepicker > span > span').datepicker(
                {
                    'changeMonth': true,
                    'changeYear': true,
                    'changeDay': false,
                    'showButtonPanel': false,
                    'dateFormat': 'yy-mm',
                    'closeText': 'Done',
                    'defaultDate': new Date(";
        // line 19
        if (isset($context["view_date"])) { $_view_date_ = $context["view_date"]; } else { $_view_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_view_date_, "Y"), "html", null, true);
        echo ", ";
        if (isset($context["view_date"])) { $_view_date_ = $context["view_date"]; } else { $_view_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_view_date_, "m"), "html", null, true);
        echo " - 1, 1),
                    'onChangeMonthYear': function(year, month, inst) {
                        \$(this).datepicker('setDate', new Date(year, month - 1, 1));
                    },
                }
        );

        \$('.datepicker-btn-done').on('click', function(){
            var date = \$('.datepicker > span > span').datepicker('getDate');
            document.location = \"";
        // line 28
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_agent_feedback_summary", array("date" => "")), "html", null, true);
        echo "/\" + date.getFullYear() + '-' + (date.getMonth() + 1);
        }).button();

        \$('.datepicker > a').on('click', function(){
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
    });
</script>
";
    }

    // line 48
    public function block_html_head($context, array $blocks = array())
    {
        // line 49
        echo "<style>
.reports_agent_feedback_summary_section > table .border-right {
    border-right-style: dashed;
    border-right-color: #AAAAAA;
    border-right-width: 1px;
}

.reports_agent_feedback_summary_section > table tr.border-top {
    border-top-style: dashed;
    border-top-color: #AAAAAA;
    border-top-width: 1px;
}

.reports_agent_feedback_summary_section > table .border-bottom {
    border-bottom-style: dashed;
    border-bottom-color: #AAAAAA;
    border-bottom-width: 1px;
}

.reports_agent_feedback_summary_section > table td.rating_1,
.reports_agent_feedback_summary_section > table td.left-column {
    text-align: right;
}

.reports_agent_feedback_summary_section > table th {
    font-weight: normal;
    font-size: 16px;
}

.reports_agent_feedback_summary_section > table th,
.reports_agent_feedback_summary_section > table td {
    padding: 18px;
    vertical-align: middle;
}

.reports_agent_feedback_summary_section > table img {
    vertical-align: middle;
    opacity: .7;
}

.ui-datepicker {
    border: none;
}

.datepicker > span {
    padding: 4px;
}

button.datepicker-btn-done {
    float: right;
    margin: 4px;
}

.datepicker.nodays .ui-datepicker-calendar {
    display: none;
}

i.rating {
\tfont-size: 15pt;
\tfont-weight: normal;
\tfont-style: normal;
\tdisplay: inline;
\ttext-align: center;
\tline-height: 10px;
\tposition: relative;
\ttop: 2px;
}

i.rating.positive {
\tcolor: #16B300;
}

i.rating.neutral {
\tcolor: #849C96;
}

i.rating.negative {
\tcolor: #A21B22;
}
</style>
";
    }

    // line 131
    public function block_pagebar($context, array $blocks = array())
    {
    }

    // line 134
    public function block_page($context, array $blocks = array())
    {
        // line 135
        echo "<div class=\"reports_agent_feedback_summary_section\">
    <table>
        <tr>
            <td class=\"border-right datepicker-cell\"><span class=\"datepicker nodays drop-down\"><a>";
        // line 138
        if (isset($context["view_date"])) { $_view_date_ = $context["view_date"]; } else { $_view_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_view_date_, "M Y"), "html", null, true);
        echo "<em></em></a><span class=\"hidden ui-widget-content ui-corner-all\"><span></span><button class=\"datepicker-btn-done\">Done</button></span></span></td>
            ";
        // line 139
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
            // line 140
            echo "                <th class=\"";
            if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
            if ((!$this->getAttribute($_loop_, "last"))) {
                echo "border-right";
            }
            echo "\" colspan=\"3\">";
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "getDisplayName", array(), "method"), "html", null, true);
            echo "</th>
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
        // line 142
        echo "        </tr>
        ";
        // line 143
        if (isset($context["days"])) { $_days_ = $context["days"]; } else { $_days_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_days_);
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
        foreach ($context['_seq'] as $context["i"] => $context["day"]) {
            // line 144
            echo "            <tr>
                <td class=\"border-right left-column\">";
            // line 145
            if (isset($context["day"])) { $_day_ = $context["day"]; } else { $_day_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_day_, "jS"), "html", null, true);
            echo "</td>

                ";
            // line 147
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
                // line 148
                echo "                    <td class=\"";
                if (isset($context["summary"])) { $_summary_ = $context["summary"]; } else { $_summary_ = null; }
                if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                if ((!$this->getAttribute($this->getAttribute($this->getAttribute($_summary_, $_i_, array(), "array"), $this->getAttribute($_agent_, "id"), array(), "array"), 1, array(), "array"))) {
                    echo "no-feedback";
                }
                echo "\">
\t\t\t\t\t\t<div>
\t\t\t\t\t\t\t<i class=\"rating positive\">☺</i>
\t\t\t\t\t\t\t";
                // line 151
                if (isset($context["summary"])) { $_summary_ = $context["summary"]; } else { $_summary_ = null; }
                if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($this->getAttribute($_summary_, $_i_, array(), "array", false, true), $this->getAttribute($_agent_, "id"), array(), "array", false, true), 1, array(), "array", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($this->getAttribute($_summary_, $_i_, array(), "array", false, true), $this->getAttribute($_agent_, "id"), array(), "array", false, true), 1, array(), "array"), 0)) : (0)), "html", null, true);
                echo "
\t\t\t\t\t\t</div>
                    </td>
\t\t\t\t\t<td class=\"";
                // line 154
                if (isset($context["summary"])) { $_summary_ = $context["summary"]; } else { $_summary_ = null; }
                if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                if ((!$this->getAttribute($this->getAttribute($this->getAttribute($_summary_, $_i_, array(), "array"), $this->getAttribute($_agent_, "id"), array(), "array"), 0, array(), "array"))) {
                    echo "no-feedback";
                }
                echo "\">
\t\t\t\t\t\t<div>
\t\t\t\t\t\t\t<i class=\"rating neutral\">&mdash;</i>
\t\t\t\t\t\t\t";
                // line 157
                if (isset($context["summary"])) { $_summary_ = $context["summary"]; } else { $_summary_ = null; }
                if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($this->getAttribute($_summary_, $_i_, array(), "array", false, true), $this->getAttribute($_agent_, "id"), array(), "array", false, true), 0, array(), "array", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($this->getAttribute($_summary_, $_i_, array(), "array", false, true), $this->getAttribute($_agent_, "id"), array(), "array", false, true), 0, array(), "array"), 0)) : (0)), "html", null, true);
                echo "
\t\t\t\t\t\t</div>
                    </td>
                    <td class=\"";
                // line 160
                if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
                if ((!$this->getAttribute($_loop_, "last"))) {
                    echo "border-right";
                }
                echo " ";
                if (isset($context["summary"])) { $_summary_ = $context["summary"]; } else { $_summary_ = null; }
                if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                if ((!$this->getAttribute($this->getAttribute($this->getAttribute($_summary_, $_i_, array(), "array"), $this->getAttribute($_agent_, "id"), array(), "array"), (-1), array(), "array"))) {
                    echo "no-feedback";
                }
                echo "\">
\t\t\t\t\t\t<div>
\t\t\t\t\t\t\t<i class=\"rating negative\">☹</i>
\t\t\t\t\t\t\t";
                // line 163
                if (isset($context["summary"])) { $_summary_ = $context["summary"]; } else { $_summary_ = null; }
                if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($this->getAttribute($_summary_, $_i_, array(), "array", false, true), $this->getAttribute($_agent_, "id"), array(), "array", false, true), (-1), array(), "array", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($this->getAttribute($_summary_, $_i_, array(), "array", false, true), $this->getAttribute($_agent_, "id"), array(), "array", false, true), (-1), array(), "array"), 0)) : (0)), "html", null, true);
                echo "
\t\t\t\t\t\t</div>
                    </td>
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
            // line 167
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
        unset($context['_seq'], $context['_iterated'], $context['i'], $context['day'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 169
        echo "        <tr class=\"border-top\">
            <td class=\"border-right left-column\">Total</td>
            ";
        // line 171
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
            // line 172
            echo "                <td class=\"";
            if (isset($context["totals"])) { $_totals_ = $context["totals"]; } else { $_totals_ = null; }
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            if ((!$this->getAttribute($this->getAttribute($_totals_, $this->getAttribute($_agent_, "id"), array(), "array"), 1, array(), "array"))) {
                echo "no-feedback";
            }
            echo "\">
\t\t\t\t\t<div>
\t\t\t\t\t\t<i class=\"rating positive\">☺</i>
\t\t\t\t\t\t";
            // line 175
            if (isset($context["totals"])) { $_totals_ = $context["totals"]; } else { $_totals_ = null; }
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_totals_, $this->getAttribute($_agent_, "id"), array(), "array", false, true), 1, array(), "array", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_totals_, $this->getAttribute($_agent_, "id"), array(), "array", false, true), 1, array(), "array"), 0)) : (0)), "html", null, true);
            echo "
\t\t\t\t\t</div>
                </td>
\t\t\t\t<td class=\"";
            // line 178
            if (isset($context["totals"])) { $_totals_ = $context["totals"]; } else { $_totals_ = null; }
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            if ((!$this->getAttribute($this->getAttribute($_totals_, $this->getAttribute($_agent_, "id"), array(), "array"), 0, array(), "array"))) {
                echo "no-feedback";
            }
            echo "\">
\t\t\t\t\t<div>
\t\t\t\t\t\t<i class=\"rating neutral\">&mdash;</i>
\t\t\t\t\t\t";
            // line 181
            if (isset($context["totals"])) { $_totals_ = $context["totals"]; } else { $_totals_ = null; }
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_totals_, $this->getAttribute($_agent_, "id"), array(), "array", false, true), 0, array(), "array", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_totals_, $this->getAttribute($_agent_, "id"), array(), "array", false, true), 0, array(), "array"), 0)) : (0)), "html", null, true);
            echo "
\t\t\t\t\t</div>
                </td>
                <td class=\"";
            // line 184
            if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
            if ((!$this->getAttribute($_loop_, "last"))) {
                echo "border-right";
            }
            echo " ";
            if (isset($context["totals"])) { $_totals_ = $context["totals"]; } else { $_totals_ = null; }
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            if ((!$this->getAttribute($this->getAttribute($_totals_, $this->getAttribute($_agent_, "id"), array(), "array"), (-1), array(), "array"))) {
                echo "no-feedback";
            }
            echo "\">
\t\t\t\t\t<div>
\t\t\t\t\t\t<i class=\"rating negative\">☹</i>
\t\t\t\t\t\t";
            // line 187
            if (isset($context["totals"])) { $_totals_ = $context["totals"]; } else { $_totals_ = null; }
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_totals_, $this->getAttribute($_agent_, "id"), array(), "array", false, true), (-1), array(), "array", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_totals_, $this->getAttribute($_agent_, "id"), array(), "array", false, true), (-1), array(), "array"), 0)) : (0)), "html", null, true);
            echo "
\t\t\t\t\t</div>
                </td>
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
        // line 191
        echo "        </tr>
    </table>
    <div class=\"reports-popup-page-overlay hidden\"></div>
</div>
";
    }

    public function getTemplateName()
    {
        return "ReportBundle:AgentFeedback:summary.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  477 => 191,  434 => 181,  405 => 172,  331 => 160,  322 => 157,  311 => 154,  302 => 151,  266 => 145,  263 => 144,  245 => 143,  242 => 142,  196 => 138,  191 => 135,  188 => 134,  183 => 131,  99 => 49,  96 => 48,  56 => 19,  34 => 4,  24 => 4,  313 => 150,  310 => 149,  307 => 148,  304 => 147,  295 => 146,  291 => 145,  282 => 143,  279 => 142,  254 => 131,  249 => 130,  233 => 123,  226 => 120,  222 => 118,  218 => 116,  215 => 115,  203 => 113,  195 => 112,  187 => 111,  182 => 110,  159 => 97,  146 => 92,  128 => 83,  125 => 82,  63 => 22,  60 => 21,  44 => 9,  33 => 5,  399 => 79,  388 => 77,  380 => 76,  377 => 74,  373 => 72,  357 => 69,  346 => 68,  334 => 66,  316 => 63,  305 => 61,  298 => 60,  287 => 144,  251 => 51,  244 => 49,  241 => 124,  235 => 47,  231 => 45,  227 => 43,  219 => 140,  199 => 36,  192 => 35,  181 => 33,  174 => 32,  156 => 29,  145 => 27,  138 => 86,  126 => 24,  114 => 22,  91 => 18,  82 => 16,  68 => 14,  57 => 12,  48 => 10,  39 => 8,  36 => 5,  28 => 1,  23 => 2,  19 => 1,  436 => 155,  430 => 156,  428 => 155,  424 => 178,  421 => 152,  416 => 175,  410 => 159,  408 => 152,  401 => 151,  391 => 148,  387 => 171,  383 => 169,  379 => 144,  376 => 143,  371 => 141,  366 => 71,  361 => 65,  356 => 59,  349 => 162,  347 => 163,  344 => 142,  342 => 141,  336 => 137,  330 => 134,  318 => 129,  315 => 128,  308 => 126,  297 => 121,  290 => 148,  278 => 114,  272 => 147,  260 => 106,  257 => 105,  250 => 103,  239 => 98,  232 => 96,  221 => 91,  210 => 86,  201 => 139,  190 => 84,  184 => 82,  179 => 81,  167 => 75,  157 => 68,  154 => 96,  151 => 66,  149 => 65,  142 => 60,  140 => 59,  108 => 30,  103 => 20,  98 => 28,  94 => 27,  88 => 24,  83 => 22,  78 => 20,  74 => 19,  70 => 18,  66 => 17,  58 => 15,  54 => 14,  49 => 12,  45 => 11,  41 => 8,  37 => 5,  673 => 200,  669 => 198,  664 => 195,  649 => 193,  634 => 191,  631 => 190,  617 => 189,  603 => 188,  599 => 186,  587 => 184,  581 => 183,  569 => 181,  563 => 180,  551 => 178,  545 => 177,  533 => 175,  527 => 174,  515 => 172,  506 => 171,  503 => 170,  497 => 169,  494 => 168,  481 => 165,  473 => 164,  467 => 163,  463 => 162,  460 => 161,  457 => 187,  454 => 159,  450 => 158,  446 => 157,  442 => 184,  423 => 155,  419 => 154,  398 => 150,  392 => 152,  389 => 151,  386 => 150,  368 => 167,  363 => 148,  360 => 70,  343 => 146,  338 => 143,  328 => 141,  323 => 64,  317 => 136,  314 => 135,  280 => 57,  269 => 55,  262 => 132,  237 => 85,  230 => 122,  223 => 42,  216 => 38,  209 => 69,  202 => 65,  172 => 37,  169 => 36,  163 => 30,  155 => 31,  152 => 30,  147 => 29,  141 => 28,  136 => 25,  132 => 85,  112 => 21,  107 => 20,  104 => 19,  100 => 18,  90 => 15,  77 => 14,  72 => 28,  62 => 16,  55 => 18,  52 => 6,  47 => 3,  29 => 1,  27 => 1,);
    }
}
