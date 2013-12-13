<?php

/* ReportBundle:AgentHours:index.html.twig */
class __TwigTemplate_01d55f654950a6f050ae5ff1cff9760a extends \Application\DeskPRO\Twig\Template
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
        $context["this_page"] = "report_agent_hours_index";
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 4
    public function block_nav_block($context, array $blocks = array())
    {
    }

    // line 7
    public function block_pagebar($context, array $blocks = array())
    {
        // line 8
        echo "\t<input id=\"datepicker1\" type=\"hidden\" value=\"";
        if (isset($context["view_date1"])) { $_view_date1_ = $context["view_date1"]; } else { $_view_date1_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_view_date1_, "Y-m-d"), "html", null, true);
        echo "\" />
\t<input id=\"datepicker2\" type=\"hidden\" value=\"";
        // line 9
        if (isset($context["view_date2"])) { $_view_date2_ = $context["view_date2"]; } else { $_view_date2_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_view_date2_, "Y-m-d"), "html", null, true);
        echo "\" />
\t<ul>
\t\t<li>
\t\t\t<span class=\"dp-title\">
\t\t\t\tViewing agent hours for
\t\t\t</span>
\t\t\t<span class=\"dp-nav-btn datepicker datepicker1\"><a>";
        // line 15
        if (isset($context["view_date"])) { $_view_date_ = $context["view_date"]; } else { $_view_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_view_date_, "jS F Y"), "html", null, true);
        echo "</a><em class=\"drop\"></em><span class=\"hidden\"></span></span>
\t\t\t&nbsp;
\t\t\t<span id=\"set_range_btn\" class=\"dp-title\" ";
        // line 17
        if (isset($context["view_date2"])) { $_view_date2_ = $context["view_date2"]; } else { $_view_date2_ = null; }
        if ($_view_date2_) {
            echo "style=\"display: none;\"";
        }
        echo ">
\t\t\t\t<em style=\"cursor: pointer;\">(click to set end range)</em>
\t\t\t</span>
\t\t\t<span id=\"set_range_ctrl\" ";
        // line 20
        if (isset($context["view_date2"])) { $_view_date2_ = $context["view_date2"]; } else { $_view_date2_ = null; }
        if ((!$_view_date2_)) {
            echo "style=\"display: none;\"";
        }
        echo ">
\t\t\t\t<span class=\"dp-title\">
\t\t\t\t\tTo
\t\t\t\t</span>
\t\t\t\t<span class=\"dp-nav-btn datepicker datepicker2\"><a>";
        // line 24
        if (isset($context["view_date2"])) { $_view_date2_ = $context["view_date2"]; } else { $_view_date2_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_view_date2_, "jS F Y"), "html", null, true);
        echo "</a><em class=\"drop\"></em><span class=\"hidden\"></span></span>
\t\t\t\t<button class=\"btn update-range-btn\" style=\"padding: 1px 5px\">Update</button>
\t\t\t</span>
\t\t</li>
\t</ul>
";
    }

    // line 31
    public function block_page($context, array $blocks = array())
    {
        // line 32
        echo "<div class=\"reports_agent_hours_section\">
";
        // line 33
        if (isset($context["block_size"])) { $_block_size_ = $context["block_size"]; } else { $_block_size_ = null; }
        $context["blocks_in_hour"] = (60 / $_block_size_);
        // line 34
        if (isset($context["blocks_in_hour"])) { $_blocks_in_hour_ = $context["blocks_in_hour"]; } else { $_blocks_in_hour_ = null; }
        $context["blocks_in_day"] = ($_blocks_in_hour_ * 24);
        // line 35
        echo "<style type=\"text/css\">

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

.time_cell {
    width: 4px;
    vertical-align: middle;
    padding: 0px;
\tmargin: 0;
\toverflow: hidden;
}

.time_cell.active .bar {
    background-color: #90ee90;
    height: 8px;
    border-bottom-style: solid;
    border-bottom-color: #999999;
    border-bottom-width: 1px;
    border-top-style: solid;
    border-top-color: #999999;
    border-top-width: 1px;
\tposition: relative;
\tz-index: 1;
}

.time_cell.active.first .bar {
    border-left-style: solid;
    border-left-color: #999999;
    border-left-width: 1px;
}

.time_cell.active.last .bar {
    border-right-style: solid;
    border-right-color: #999999;
    border-right-width: 1px;
}

.left_panel {
    padding: 4px;
\twidth: 225px !important;
}

.left_panel.agent_total {
    border-right-style: solid;
    border-right-color: #CCCCCC;
    border-right-width: 1px;
}

.hour_title.first {
    border-left-style: solid;
    border-left-color: #CCCCCC;
    border-left-width: 1px;
}

.chart_container {
    padding: 0px;
    width: ";
        // line 104
        if (isset($context["blocks_in_day"])) { $_blocks_in_day_ = $context["blocks_in_day"]; } else { $_blocks_in_day_ = null; }
        echo twig_escape_filter($this->env, (4 * $_blocks_in_day_), "html", null, true);
        echo "px;
    height: 100%;
}

.chart_container > div {
    position: relative;
}

.chart_container > div > table {
    position: absolute;
}

.border_table {
    height: 100%;
\tposition: relative;
\tleft: 1px;
\tborder-collapse: collapse;
}

.border_table td {
    padding: 0px;
\twidth: 4px;
\tborder-right: 1px solid transparent;
\tpadding: 0;
\tmargin: 0;
\theight: ";
        // line 129
        if (isset($context["agents"])) { $_agents_ = $context["agents"]; } else { $_agents_ = null; }
        echo twig_escape_filter($this->env, ((26 * twig_length_filter($this->env, $_agents_)) + 5), "html", null, true);
        echo "px;
}

.border_table td.hour {
\tborder-right: 1px solid #CCCCCC;
}

.date_today {
    padding: 8px;
    text-align: center;
}

.hour_title {
    text-align: center;
    border-right-style: solid;
    border-right-color: #CCCCCC;
    border-right-width: 1px;
    border-bottom-style: solid;
    border-bottom-color: #CCCCCC;
    border-bottom-width: 1px;
    border-top-style: solid;
    border-top-color: #CCCCCC;
    border-top-width: 1px;
    padding: 0px;
    width: ";
        // line 153
        if (isset($context["blocks_in_hour"])) { $_blocks_in_hour_ = $context["blocks_in_hour"]; } else { $_blocks_in_hour_ = null; }
        echo twig_escape_filter($this->env, ((4 * $_blocks_in_hour_) - 1), "html", null, true);
        echo "px;
}
";
        // line 155
        if (isset($context["times"])) { $_times_ = $context["times"]; } else { $_times_ = null; }
        if (isset($context["use_days"])) { $_use_days_ = $context["use_days"]; } else { $_use_days_ = null; }
        if (($_times_ && $_use_days_)) {
            // line 156
            echo ".time_cell.active .bar {
\tborder: none !important;
}

table.chart_parent_table td {
\twidth: 48px;
\toverflow: hidden;
}

.time_cell {
\twidth: 2px !important;
\toverflow: hidden !important;
}

#dp_page_wrap {
\tmin-width: ";
            // line 171
            if (isset($context["num_days"])) { $_num_days_ = $context["num_days"]; } else { $_num_days_ = null; }
            echo twig_escape_filter($this->env, (($_num_days_ * 50) + $_num_days_), "html", null, true);
            echo "px;
\tmax-width: none;
}
";
        }
        // line 175
        echo "
</style>
<script type=\"text/javascript\">
    \$(document).ready(function(){

\t\tfunction refreshRange() {
\t\t\tvar date1 = \$('#datepicker1').val();
\t\t\tvar date2 = \$('#datepicker2').val();

\t\t\tif (date2 && !\$('#set_range_ctrl').is(':hidden')) {
\t\t\t\tdocument.location = \"";
        // line 185
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_agent_hours_list_date", array("date" => "")), "html", null, true);
        echo "/\" + date1 + '/' + date2;
\t\t\t} else {
\t\t\t\tdocument.location = \"";
        // line 187
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("report_agent_hours_list_date", array("date" => "")), "html", null, true);
        echo "/\" + date1;
\t\t\t}
\t\t}

\t\t\$('.update-range-btn').on('click', function(ev) {
\t\t\tev.preventDefault();
\t\t\trefreshRange();
\t\t});

        \$('.datepicker1 > span').datepicker(
                {
                    'onSelect': function(dateText) {
\t\t\t\t\t\t\$('#datepicker1').val(dateText);
\t\t\t\t\t\tif (\$('#set_range_ctrl').is(':hidden')) {
\t\t\t\t\t\t\trefreshRange();
\t\t\t\t\t\t} else {
\t\t\t\t\t\t\t\$('.datepicker1 > a').text(dateText);
\t\t\t\t\t\t}
                    },
                    'dateFormat': 'yy-mm-dd',
                    'minDate': new Date(";
        // line 207
        if (isset($context["min_date"])) { $_min_date_ = $context["min_date"]; } else { $_min_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_min_date_, "Y"), "html", null, true);
        echo ", ";
        if (isset($context["min_date"])) { $_min_date_ = $context["min_date"]; } else { $_min_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_min_date_, "m"), "html", null, true);
        echo " -1, ";
        if (isset($context["min_date"])) { $_min_date_ = $context["min_date"]; } else { $_min_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_min_date_, "d"), "html", null, true);
        echo "),
                    'maxDate': new Date(";
        // line 208
        if (isset($context["max_date"])) { $_max_date_ = $context["max_date"]; } else { $_max_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_max_date_, "Y"), "html", null, true);
        echo ", ";
        if (isset($context["max_date"])) { $_max_date_ = $context["max_date"]; } else { $_max_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_max_date_, "m"), "html", null, true);
        echo " -1, ";
        if (isset($context["max_date"])) { $_max_date_ = $context["max_date"]; } else { $_max_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_max_date_, "d"), "html", null, true);
        echo "),
                    'defaultDate': new Date(";
        // line 209
        if (isset($context["view_date"])) { $_view_date_ = $context["view_date"]; } else { $_view_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_view_date_, "Y"), "html", null, true);
        echo ", ";
        if (isset($context["view_date"])) { $_view_date_ = $context["view_date"]; } else { $_view_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_view_date_, "m"), "html", null, true);
        echo " -1, ";
        if (isset($context["view_date"])) { $_view_date_ = $context["view_date"]; } else { $_view_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_view_date_, "d"), "html", null, true);
        echo ")
                }
        );
\t\t\$('.datepicker2 > span').datepicker(
\t\t\t{
\t\t\t\t'onSelect': function(dateText) {
\t\t\t\t\t\$('#datepicker2').val(dateText);
\t\t\t\t\t\$('.datepicker2 > a').text(dateText);
\t\t\t\t},
\t\t\t\t'dateFormat': 'yy-mm-dd',
\t\t\t\t'minDate': new Date(";
        // line 219
        if (isset($context["min_date"])) { $_min_date_ = $context["min_date"]; } else { $_min_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_min_date_, "Y"), "html", null, true);
        echo ", ";
        if (isset($context["min_date"])) { $_min_date_ = $context["min_date"]; } else { $_min_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_min_date_, "m"), "html", null, true);
        echo " -1, ";
        if (isset($context["min_date"])) { $_min_date_ = $context["min_date"]; } else { $_min_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_min_date_, "d"), "html", null, true);
        echo "),
\t\t\t\t'maxDate': new Date(";
        // line 220
        if (isset($context["max_date"])) { $_max_date_ = $context["max_date"]; } else { $_max_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_max_date_, "Y"), "html", null, true);
        echo ", ";
        if (isset($context["max_date"])) { $_max_date_ = $context["max_date"]; } else { $_max_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_max_date_, "m"), "html", null, true);
        echo " -1, ";
        if (isset($context["max_date"])) { $_max_date_ = $context["max_date"]; } else { $_max_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_max_date_, "d"), "html", null, true);
        echo "),
\t\t\t\t'defaultDate': new Date(";
        // line 221
        if (isset($context["view_date"])) { $_view_date_ = $context["view_date"]; } else { $_view_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_view_date_, "Y"), "html", null, true);
        echo ", ";
        if (isset($context["view_date"])) { $_view_date_ = $context["view_date"]; } else { $_view_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_view_date_, "m"), "html", null, true);
        echo " -1, ";
        if (isset($context["view_date"])) { $_view_date_ = $context["view_date"]; } else { $_view_date_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $_view_date_, "d"), "html", null, true);
        echo ")
\t\t\t}
\t\t);
        \$('.datepicker1 > a').on('click', function(){
            if(\$('.datepicker1 > span').hasClass('hidden')) {
                \$('.datepicker1 > span').removeClass('hidden');
                \$('.reports-popup-page-overlay').removeClass('hidden');
            }
            else {
                \$('.datepicker1 > span').addClass('hidden');
                \$('.reports-popup-page-overlay').addClass('hidden');
            }
        });
\t\t\$('.datepicker2 > a').on('click', function(){
            if(\$('.datepicker2 > span').hasClass('hidden')) {
                \$('.datepicker2 > span').removeClass('hidden');
                \$('.reports-popup-page-overlay').removeClass('hidden');
            }
            else {
                \$('.datepicker2 > span').addClass('hidden');
                \$('.reports-popup-page-overlay').addClass('hidden');
            }
        });
\t\t\$('.reports-popup-page-overlay').on('click', function() {
\t\t\t\$('.datepicker1 > span').addClass('hidden');
\t\t\t\$('.datepicker2 > span').addClass('hidden');
\t\t\t\$('.reports-popup-page-overlay').addClass('hidden');
\t\t});
        function resize()
        {
            \$('.agent_row').each(function(i , element) {
                element = \$(element);
                \$('#agent_time_row-' + element.attr('id').split('-')[1]).css('height', element.height()+\"px\");
            });
        }

\t\t\$('#set_range_btn').on('click', function(ev) {
\t\t\tev.preventDefault();
\t\t\tev.stopPropagation();

\t\t\t\$(this).hide();
\t\t\t\$('#set_range_ctrl').show();
\t\t\t\$('.datepicker2 > a').click();
\t\t});

        \$(window).on('resize', resize);
        resize();
    });

</script>

";
        // line 272
        if (isset($context["times"])) { $_times_ = $context["times"]; } else { $_times_ = null; }
        if (isset($context["use_days"])) { $_use_days_ = $context["use_days"]; } else { $_use_days_ = null; }
        if (($_times_ && $_use_days_)) {
            // line 273
            echo "\t<div class=\"chart-wrapper\" style=\"position:relative;\">
\t\t<table class=\"chart_parent_table\">
\t\t\t<tr>
\t\t\t\t<td class=\"left_panel empty\">&nbsp;</td>
\t\t\t\t";
            // line 277
            if (isset($context["use_days"])) { $_use_days_ = $context["use_days"]; } else { $_use_days_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_use_days_);
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
            foreach ($context['_seq'] as $context["year"] => $context["months"]) {
                // line 278
                echo "\t\t\t\t\t";
                $context["is_first"] = false;
                // line 279
                echo "\t\t\t\t\t";
                if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
                if ($this->getAttribute($_loop_, "first")) {
                    $context["is_first"] = true;
                }
                // line 280
                echo "\t\t\t\t\t";
                if (isset($context["months"])) { $_months_ = $context["months"]; } else { $_months_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($_months_);
                foreach ($context['_seq'] as $context["month"] => $context["days"]) {
                    // line 281
                    echo "\t\t\t\t\t\t";
                    if (isset($context["days"])) { $_days_ = $context["days"]; } else { $_days_ = null; }
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($_days_);
                    foreach ($context['_seq'] as $context["_key"] => $context["day"]) {
                        // line 282
                        echo "\t\t\t\t\t\t\t<td class=\"hour_title ";
                        if (isset($context["is_first"])) { $_is_first_ = $context["is_first"]; } else { $_is_first_ = null; }
                        if ($_is_first_) {
                            echo "first";
                        }
                        echo "\">
\t\t\t\t\t\t\t\t<span style=\"font-size: 9px;\">
\t\t\t\t\t\t\t\t";
                        // line 284
                        if (isset($context["month"])) { $_month_ = $context["month"]; } else { $_month_ = null; }
                        if (($_month_ == 1)) {
                            echo "Jan
\t\t\t\t\t\t\t\t";
                        } elseif (($_month_ == 2)) {
                            // line 285
                            echo "Feb
\t\t\t\t\t\t\t\t";
                        } elseif (($_month_ == 3)) {
                            // line 286
                            echo "March
\t\t\t\t\t\t\t\t";
                        } elseif (($_month_ == 4)) {
                            // line 287
                            echo "April
\t\t\t\t\t\t\t\t";
                        } elseif (($_month_ == 5)) {
                            // line 288
                            echo "May
\t\t\t\t\t\t\t\t";
                        } elseif (($_month_ == 6)) {
                            // line 289
                            echo "June
\t\t\t\t\t\t\t\t";
                        } elseif (($_month_ == 7)) {
                            // line 290
                            echo "July
\t\t\t\t\t\t\t\t";
                        } elseif (($_month_ == 8)) {
                            // line 291
                            echo "August
\t\t\t\t\t\t\t\t";
                        } elseif (($_month_ == 9)) {
                            // line 292
                            echo "Sept
\t\t\t\t\t\t\t\t";
                        } elseif (($_month_ == 10)) {
                            // line 293
                            echo "Oct
\t\t\t\t\t\t\t\t";
                        } elseif (($_month_ == 11)) {
                            // line 294
                            echo "Nov
\t\t\t\t\t\t\t\t";
                        } elseif (($_month_ == 12)) {
                            // line 295
                            echo "Dec
\t\t\t\t\t\t\t\t";
                        }
                        // line 297
                        echo "\t\t\t\t\t\t\t\t</span>
\t\t\t\t\t\t\t\t<br/>
\t\t\t\t\t\t\t\t";
                        // line 299
                        if (isset($context["day"])) { $_day_ = $context["day"]; } else { $_day_ = null; }
                        echo twig_escape_filter($this->env, $_day_, "html", null, true);
                        echo "
\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['day'], $context['_parent'], $context['loop']);
                    $context = array_merge($_parent, array_intersect_key($context, $_parent));
                    // line 302
                    echo "\t\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['month'], $context['days'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 303
                echo "\t\t\t\t";
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
            unset($context['_seq'], $context['_iterated'], $context['year'], $context['months'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 304
            echo "\t\t\t</tr>
\t\t\t";
            // line 305
            if (isset($context["agents"])) { $_agents_ = $context["agents"]; } else { $_agents_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_agents_);
            foreach ($context['_seq'] as $context["_key"] => $context["agent"]) {
                // line 306
                echo "\t\t\t\t<tr>
\t\t\t\t\t<td class=\"left_panel empty\" style=\"text-align: right;\">
\t\t\t\t\t\t";
                // line 308
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "name"), "html", null, true);
                echo "
\t\t\t\t\t\t<em style=\"font-style: normal; font-size: 10px;\">(";
                // line 309
                if (isset($context["totals"])) { $_totals_ = $context["totals"]; } else { $_totals_ = null; }
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_totals_, $this->getAttribute($_agent_, "id"), array(), "array"), "hours"), "html", null, true);
                echo "hrs, ";
                if (isset($context["totals"])) { $_totals_ = $context["totals"]; } else { $_totals_ = null; }
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_totals_, $this->getAttribute($_agent_, "id"), array(), "array"), "minutes"), "html", null, true);
                echo "mins)</em>
\t\t\t\t\t</td>
\t\t\t\t\t";
                // line 311
                if (isset($context["use_days"])) { $_use_days_ = $context["use_days"]; } else { $_use_days_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($_use_days_);
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
                foreach ($context['_seq'] as $context["year"] => $context["months"]) {
                    // line 312
                    echo "\t\t\t\t\t";
                    $context["is_first"] = false;
                    // line 313
                    echo "\t\t\t\t\t";
                    if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
                    if ($this->getAttribute($_loop_, "first")) {
                        $context["is_first"] = true;
                    }
                    // line 314
                    echo "\t\t\t\t\t";
                    if (isset($context["months"])) { $_months_ = $context["months"]; } else { $_months_ = null; }
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($_months_);
                    foreach ($context['_seq'] as $context["month"] => $context["days"]) {
                        // line 315
                        echo "\t\t\t\t\t\t";
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
                        foreach ($context['_seq'] as $context["_key"] => $context["day"]) {
                            // line 316
                            echo "\t\t\t\t\t\t\t<td class=\"hour_title ";
                            if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
                            if ($this->getAttribute($_loop_, "first")) {
                                echo "first";
                            }
                            echo "\">
\t\t\t\t\t\t\t\t&nbsp;
\t\t\t\t\t\t\t</td>
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
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['day'], $context['_parent'], $context['loop']);
                        $context = array_merge($_parent, array_intersect_key($context, $_parent));
                        // line 320
                        echo "\t\t\t\t\t";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['month'], $context['days'], $context['_parent'], $context['loop']);
                    $context = array_merge($_parent, array_intersect_key($context, $_parent));
                    // line 321
                    echo "\t\t\t\t";
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
                unset($context['_seq'], $context['_iterated'], $context['year'], $context['months'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 322
                echo "\t\t\t\t</tr>
\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['agent'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 324
            echo "\t\t</table>
\t\t<table class=\"chart_parent_table\" style=\"position:absolute; top:0; left:0;\">
\t\t\t<tr>
\t\t\t\t<td class=\"left_panel empty\">&nbsp;</td>
\t\t\t\t";
            // line 328
            if (isset($context["use_days"])) { $_use_days_ = $context["use_days"]; } else { $_use_days_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_use_days_);
            foreach ($context['_seq'] as $context["year"] => $context["months"]) {
                // line 329
                echo "\t\t\t\t\t";
                if (isset($context["months"])) { $_months_ = $context["months"]; } else { $_months_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($_months_);
                foreach ($context['_seq'] as $context["month"] => $context["days"]) {
                    // line 330
                    echo "\t\t\t\t\t\t";
                    if (isset($context["days"])) { $_days_ = $context["days"]; } else { $_days_ = null; }
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($_days_);
                    foreach ($context['_seq'] as $context["_key"] => $context["day"]) {
                        // line 331
                        echo "\t\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t\t<span style=\"font-size: 9px;\">&nbsp;</span><br/>
\t\t\t\t\t\t\t\t&nbsp;
\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['day'], $context['_parent'], $context['loop']);
                    $context = array_merge($_parent, array_intersect_key($context, $_parent));
                    // line 336
                    echo "\t\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['month'], $context['days'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 337
                echo "\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['year'], $context['months'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 338
            echo "\t\t\t</tr>
\t\t\t";
            // line 339
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
                // line 340
                echo "\t\t\t\t";
                $context["last_active"] = 0;
                // line 341
                echo "\t\t\t\t<tr id=\"agent_row-";
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "id"), "html", null, true);
                echo "\" class=\"agent_row\">
\t\t\t\t\t<td class=\"left_panel\">&nbsp;</td>
\t\t\t\t\t";
                // line 343
                if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
                if ($this->getAttribute($_loop_, "first")) {
                    // line 344
                    echo "\t\t\t\t\t<td class=\"chart_container\" colspan=\"";
                    if (isset($context["num_days"])) { $_num_days_ = $context["num_days"]; } else { $_num_days_ = null; }
                    echo twig_escape_filter($this->env, $_num_days_, "html", null, true);
                    echo "\" rowspan=\"";
                    if (isset($context["agents"])) { $_agents_ = $context["agents"]; } else { $_agents_ = null; }
                    echo twig_escape_filter($this->env, twig_length_filter($this->env, $_agents_), "html", null, true);
                    echo "\">
\t\t\t\t\t\t<div><table>
\t\t\t\t\t\t\t";
                    // line 346
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
                        // line 347
                        echo "\t\t\t\t\t\t\t<tr id=\"agent_time_row-";
                        if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "id"), "html", null, true);
                        echo "\" class=\"time_row ";
                        if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
                        if ($this->getAttribute($_loop_, "last")) {
                            echo "last";
                        }
                        echo "\">
\t\t\t\t\t\t\t\t";
                        // line 348
                        if (isset($context["use_days"])) { $_use_days_ = $context["use_days"]; } else { $_use_days_ = null; }
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($_use_days_);
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
                        foreach ($context['_seq'] as $context["year"] => $context["months"]) {
                            // line 349
                            echo "\t\t\t\t\t\t\t\t\t";
                            if (isset($context["months"])) { $_months_ = $context["months"]; } else { $_months_ = null; }
                            $context['_parent'] = (array) $context;
                            $context['_seq'] = twig_ensure_traversable($_months_);
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
                            foreach ($context['_seq'] as $context["month"] => $context["days"]) {
                                // line 350
                                echo "\t\t\t\t\t\t\t\t\t\t";
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
                                foreach ($context['_seq'] as $context["_key"] => $context["day"]) {
                                    // line 351
                                    echo "\t\t\t\t\t\t\t\t\t\t\t";
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
                                    foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
                                        // line 352
                                        echo "\t\t\t\t\t\t\t\t\t\t\t\t<td class=\"time_cell
\t\t\t\t\t\t\t\t\t\t\t\t\t";
                                        // line 353
                                        if (isset($context["times_hour"])) { $_times_hour_ = $context["times_hour"]; } else { $_times_hour_ = null; }
                                        if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                                        if (isset($context["year"])) { $_year_ = $context["year"]; } else { $_year_ = null; }
                                        if (isset($context["month"])) { $_month_ = $context["month"]; } else { $_month_ = null; }
                                        if (isset($context["day"])) { $_day_ = $context["day"]; } else { $_day_ = null; }
                                        if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
                                        if ($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($_times_hour_, $this->getAttribute($_agent_, "id"), array(), "array"), $_year_, array(), "array"), $_month_, array(), "array"), $_day_, array(), "array"), $_i_, array(), "array")) {
                                            // line 354
                                            echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
                                            if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
                                            if (isset($context["last_active"])) { $_last_active_ = $context["last_active"]; } else { $_last_active_ = null; }
                                            if (($this->getAttribute($_loop_, "first") || (!$_last_active_))) {
                                                echo "first";
                                            }
                                            // line 355
                                            echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
                                            if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
                                            if (isset($context["times_hour"])) { $_times_hour_ = $context["times_hour"]; } else { $_times_hour_ = null; }
                                            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                                            if (isset($context["year"])) { $_year_ = $context["year"]; } else { $_year_ = null; }
                                            if (isset($context["month"])) { $_month_ = $context["month"]; } else { $_month_ = null; }
                                            if (isset($context["day"])) { $_day_ = $context["day"]; } else { $_day_ = null; }
                                            if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
                                            if (($this->getAttribute($_loop_, "last") || (!$this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($_times_hour_, $this->getAttribute($_agent_, "id"), array(), "array", false, true), $_year_, array(), "array", false, true), $_month_, array(), "array", false, true), $_day_, array(), "array", false, true), $_i_, array(), "array", false, true), ($_i_ + 1), array(), "array", true, true)))) {
                                                echo "last";
                                            }
                                            // line 356
                                            echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
                                            $context["last_active"] = 1;
                                            // line 357
                                            echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\tactive
\t\t\t\t\t\t\t\t\t\t\t\t\t";
                                        } else {
                                            // line 359
                                            echo "\t\t\t\t\t\t\t\t\t\t\t\t\t";
                                            $context["last_active"] = 0;
                                            // line 360
                                            echo "\t\t\t\t\t\t\t\t\t\t\t\t";
                                        }
                                        // line 361
                                        echo "\t\t\t\t\t\t\t\t\t\t\t\t\"><div class=\"bar\"></div></td>
\t\t\t\t\t\t\t\t\t\t\t";
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
                                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
                                    $context = array_merge($_parent, array_intersect_key($context, $_parent));
                                    // line 363
                                    echo "\t\t\t\t\t\t\t\t\t\t";
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
                                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['day'], $context['_parent'], $context['loop']);
                                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                                // line 364
                                echo "\t\t\t\t\t\t\t\t\t";
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
                            unset($context['_seq'], $context['_iterated'], $context['month'], $context['days'], $context['_parent'], $context['loop']);
                            $context = array_merge($_parent, array_intersect_key($context, $_parent));
                            // line 365
                            echo "\t\t\t\t\t\t\t\t";
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
                        unset($context['_seq'], $context['_iterated'], $context['year'], $context['months'], $context['_parent'], $context['loop']);
                        $context = array_merge($_parent, array_intersect_key($context, $_parent));
                        // line 366
                        echo "\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t";
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
                    // line 368
                    echo "\t\t\t\t\t\t</table></div>
\t\t\t\t\t</td>
\t\t\t\t\t";
                }
                // line 371
                echo "\t\t\t\t</tr>
\t\t\t";
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
            // line 373
            echo "\t\t</table>
\t</div>
";
        } elseif ($_times_) {
            // line 376
            echo "\t<div class=\"chart-wrapper\" style=\"position:relative;\">
\t\t<table class=\"chart_parent_table\">
\t\t\t<tr>
\t\t\t\t<td class=\"left_panel empty\">&nbsp;</td>
\t\t\t\t";
            // line 380
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
            foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
                // line 381
                echo "\t\t\t\t\t<td class=\"hour_title ";
                if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
                if ($this->getAttribute($_loop_, "first")) {
                    echo "first";
                }
                echo "\">
\t\t\t\t\t\t";
                // line 382
                if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
                echo twig_escape_filter($this->env, sprintf("%02d", $_i_), "html", null, true);
                echo ":00
\t\t\t\t\t</td>
\t\t\t\t";
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
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 385
            echo "\t\t\t</tr>
\t\t\t";
            // line 386
            if (isset($context["agents"])) { $_agents_ = $context["agents"]; } else { $_agents_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_agents_);
            foreach ($context['_seq'] as $context["_key"] => $context["agent"]) {
                // line 387
                echo "\t\t\t\t<tr>
\t\t\t\t\t<td class=\"left_panel empty\" style=\"text-align: right;\">
\t\t\t\t\t\t";
                // line 389
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "name"), "html", null, true);
                echo "
\t\t\t\t\t\t<em style=\"font-style: normal; font-size: 10px;\">(";
                // line 390
                if (isset($context["totals"])) { $_totals_ = $context["totals"]; } else { $_totals_ = null; }
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_totals_, $this->getAttribute($_agent_, "id"), array(), "array"), "hours"), "html", null, true);
                echo "hrs, ";
                if (isset($context["totals"])) { $_totals_ = $context["totals"]; } else { $_totals_ = null; }
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_totals_, $this->getAttribute($_agent_, "id"), array(), "array"), "minutes"), "html", null, true);
                echo "mins)</em>
\t\t\t\t\t</td>
\t\t\t\t\t";
                // line 392
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
                foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
                    // line 393
                    echo "\t\t\t\t\t\t<td class=\"hour_title ";
                    if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
                    if ($this->getAttribute($_loop_, "first")) {
                        echo "first";
                    }
                    echo "\">
\t\t\t\t\t\t\t&nbsp;
\t\t\t\t\t\t</td>
\t\t\t\t\t";
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
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 397
                echo "\t\t\t\t</tr>
\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['agent'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 399
            echo "\t\t</table>
\t\t<table class=\"chart_parent_table\" style=\"position:absolute; top:0; left:0;\">
\t\t\t<tr>
\t\t\t\t<td class=\"left_panel empty\">&nbsp;</td>
\t\t\t\t";
            // line 403
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable(range(0, 23));
            foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
                // line 404
                echo "\t\t\t\t\t<td>
\t\t\t\t\t\t&nbsp;
\t\t\t\t\t</td>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 408
            echo "\t\t\t</tr>
\t\t\t";
            // line 409
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
                // line 410
                echo "\t\t\t\t";
                $context["last_active"] = 0;
                // line 411
                echo "\t\t\t\t<tr id=\"agent_row-";
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "id"), "html", null, true);
                echo "\" class=\"agent_row\">
\t\t\t\t\t<td class=\"left_panel\">&nbsp;</td>
\t\t\t\t\t";
                // line 413
                if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
                if ($this->getAttribute($_loop_, "first")) {
                    // line 414
                    echo "\t\t\t\t\t<td class=\"chart_container\" colspan=\"24\" rowspan=\"";
                    if (isset($context["agents"])) { $_agents_ = $context["agents"]; } else { $_agents_ = null; }
                    echo twig_escape_filter($this->env, twig_length_filter($this->env, $_agents_), "html", null, true);
                    echo "\">
\t\t\t\t\t\t<div><table>
\t\t\t\t\t\t\t";
                    // line 416
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
                        // line 417
                        echo "\t\t\t\t\t\t\t<tr id=\"agent_time_row-";
                        if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "id"), "html", null, true);
                        echo "\" class=\"time_row ";
                        if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
                        if ($this->getAttribute($_loop_, "last")) {
                            echo "last";
                        }
                        echo "\">
\t\t\t\t\t\t\t\t";
                        // line 418
                        if (isset($context["blocks_in_day"])) { $_blocks_in_day_ = $context["blocks_in_day"]; } else { $_blocks_in_day_ = null; }
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable(range(0, ($_blocks_in_day_ - 1)));
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
                        foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
                            // line 419
                            echo "\t\t\t\t\t\t\t\t<td class=\"time_cell
\t\t\t\t\t\t\t\t\t\t";
                            // line 420
                            if (isset($context["times"])) { $_times_ = $context["times"]; } else { $_times_ = null; }
                            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                            if (isset($context["year_start"])) { $_year_start_ = $context["year_start"]; } else { $_year_start_ = null; }
                            if (isset($context["month_start"])) { $_month_start_ = $context["month_start"]; } else { $_month_start_ = null; }
                            if (isset($context["day_start"])) { $_day_start_ = $context["day_start"]; } else { $_day_start_ = null; }
                            if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
                            if ($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($_times_, $this->getAttribute($_agent_, "id"), array(), "array", false, true), $_year_start_, array(), "array", false, true), $_month_start_, array(), "array", false, true), $_day_start_, array(), "array", false, true), $_i_, array(), "array", true, true)) {
                                // line 421
                                echo "\t\t\t\t\t\t\t\t\t\t\t";
                                if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
                                if (isset($context["last_active"])) { $_last_active_ = $context["last_active"]; } else { $_last_active_ = null; }
                                if (($this->getAttribute($_loop_, "first") || (!$_last_active_))) {
                                    echo "first";
                                }
                                // line 422
                                echo "\t\t\t\t\t\t\t\t\t\t\t";
                                if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
                                if (isset($context["times"])) { $_times_ = $context["times"]; } else { $_times_ = null; }
                                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                                if (isset($context["year_start"])) { $_year_start_ = $context["year_start"]; } else { $_year_start_ = null; }
                                if (isset($context["month_start"])) { $_month_start_ = $context["month_start"]; } else { $_month_start_ = null; }
                                if (isset($context["day_start"])) { $_day_start_ = $context["day_start"]; } else { $_day_start_ = null; }
                                if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
                                if (($this->getAttribute($_loop_, "last") || (!$this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($_times_, $this->getAttribute($_agent_, "id"), array(), "array", false, true), $_year_start_, array(), "array", false, true), $_month_start_, array(), "array", false, true), $_day_start_, array(), "array", false, true), ($_i_ + 1), array(), "array", true, true)))) {
                                    echo "last";
                                }
                                // line 423
                                echo "\t\t\t\t\t\t\t\t\t\t\t";
                                $context["last_active"] = 1;
                                // line 424
                                echo "\t\t\t\t\t\t\t\t\t\t\tactive
\t\t\t\t\t\t\t\t\t\t";
                            } else {
                                // line 426
                                echo "\t\t\t\t\t\t\t\t\t\t";
                                $context["last_active"] = 0;
                                // line 427
                                echo "\t\t\t\t\t\t\t\t\t\t";
                                if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
                                if (isset($context["blocks_in_hour"])) { $_blocks_in_hour_ = $context["blocks_in_hour"]; } else { $_blocks_in_hour_ = null; }
                                if (((($_i_ + 1) % $_blocks_in_hour_) == 0)) {
                                    echo "hour";
                                }
                                // line 428
                                echo "\t\t\t\t\t\t\t\t\t";
                            }
                            // line 429
                            echo "\t\t\t\t\t\t\t\t\t\"><div class=\"bar\"></div></td>
\t\t\t\t\t\t\t\t";
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
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
                        $context = array_merge($_parent, array_intersect_key($context, $_parent));
                        // line 431
                        echo "\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t";
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
                    // line 433
                    echo "\t\t\t\t\t\t</table></div>
\t\t\t\t\t</td>
\t\t\t\t\t";
                }
                // line 436
                echo "\t\t\t\t</tr>
\t\t\t";
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
            // line 438
            echo "\t\t</table>
\t</div>
";
        } else {
            // line 441
            echo "\tNo data for selected time period.
";
        }
        // line 443
        echo "

<div class=\"reports-popup-page-overlay hidden\"></div>
</div>
";
    }

    public function getTemplateName()
    {
        return "ReportBundle:AgentHours:index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1374 => 443,  1370 => 441,  1365 => 438,  1350 => 436,  1345 => 433,  1330 => 431,  1315 => 429,  1312 => 428,  1305 => 427,  1302 => 426,  1298 => 424,  1295 => 423,  1283 => 422,  1276 => 421,  1268 => 420,  1265 => 419,  1247 => 418,  1236 => 417,  1218 => 416,  1211 => 414,  1208 => 413,  1201 => 411,  1198 => 410,  1180 => 409,  1177 => 408,  1168 => 404,  1164 => 403,  1158 => 399,  1151 => 397,  1129 => 393,  1112 => 392,  1101 => 390,  1096 => 389,  1092 => 387,  1087 => 386,  1084 => 385,  1066 => 382,  1058 => 381,  1041 => 380,  1035 => 376,  1030 => 373,  1015 => 371,  1010 => 368,  995 => 366,  981 => 365,  967 => 364,  953 => 363,  938 => 361,  935 => 360,  932 => 359,  928 => 357,  925 => 356,  913 => 355,  906 => 354,  898 => 353,  895 => 352,  877 => 351,  858 => 350,  839 => 349,  821 => 348,  810 => 347,  792 => 346,  782 => 344,  779 => 343,  772 => 341,  769 => 340,  751 => 339,  748 => 338,  742 => 337,  736 => 336,  726 => 331,  720 => 330,  714 => 329,  709 => 328,  703 => 324,  696 => 322,  682 => 321,  676 => 320,  654 => 316,  635 => 315,  629 => 314,  623 => 313,  620 => 312,  602 => 311,  591 => 309,  586 => 308,  582 => 306,  577 => 305,  574 => 304,  560 => 303,  554 => 302,  544 => 299,  540 => 297,  536 => 295,  532 => 294,  528 => 293,  524 => 292,  520 => 291,  516 => 290,  512 => 289,  508 => 288,  504 => 287,  500 => 286,  496 => 285,  490 => 284,  475 => 281,  469 => 280,  432 => 272,  329 => 209,  284 => 187,  267 => 175,  259 => 171,  238 => 155,  204 => 129,  175 => 104,  101 => 34,  95 => 32,  92 => 31,  81 => 24,  71 => 20,  46 => 9,  40 => 8,  32 => 4,  477 => 191,  434 => 181,  405 => 172,  331 => 160,  322 => 157,  311 => 154,  302 => 151,  266 => 145,  263 => 144,  245 => 143,  242 => 156,  196 => 138,  191 => 135,  188 => 134,  183 => 131,  99 => 49,  96 => 48,  56 => 15,  34 => 4,  24 => 4,  313 => 150,  310 => 149,  307 => 207,  304 => 147,  295 => 146,  291 => 145,  282 => 143,  279 => 185,  254 => 131,  249 => 130,  233 => 123,  226 => 120,  222 => 118,  218 => 116,  215 => 115,  203 => 113,  195 => 112,  187 => 111,  182 => 110,  159 => 97,  146 => 92,  128 => 83,  125 => 82,  63 => 22,  60 => 21,  44 => 9,  33 => 5,  399 => 79,  388 => 77,  380 => 76,  377 => 74,  373 => 72,  357 => 69,  346 => 68,  334 => 66,  316 => 63,  305 => 61,  298 => 60,  287 => 144,  251 => 51,  244 => 49,  241 => 124,  235 => 47,  231 => 45,  227 => 43,  219 => 140,  199 => 36,  192 => 35,  181 => 33,  174 => 32,  156 => 29,  145 => 27,  138 => 86,  126 => 24,  114 => 22,  91 => 18,  82 => 16,  68 => 14,  57 => 12,  48 => 10,  39 => 8,  36 => 5,  28 => 1,  23 => 2,  19 => 1,  436 => 273,  430 => 156,  428 => 155,  424 => 178,  421 => 152,  416 => 175,  410 => 159,  408 => 152,  401 => 151,  391 => 148,  387 => 171,  383 => 169,  379 => 144,  376 => 143,  371 => 221,  366 => 71,  361 => 65,  356 => 59,  349 => 219,  347 => 163,  344 => 142,  342 => 141,  336 => 137,  330 => 134,  318 => 208,  315 => 128,  308 => 126,  297 => 121,  290 => 148,  278 => 114,  272 => 147,  260 => 106,  257 => 105,  250 => 103,  239 => 98,  232 => 153,  221 => 91,  210 => 86,  201 => 139,  190 => 84,  184 => 82,  179 => 81,  167 => 75,  157 => 68,  154 => 96,  151 => 66,  149 => 65,  142 => 60,  140 => 59,  108 => 30,  103 => 20,  98 => 33,  94 => 27,  88 => 24,  83 => 22,  78 => 20,  74 => 19,  70 => 18,  66 => 17,  58 => 15,  54 => 14,  49 => 12,  45 => 11,  41 => 8,  37 => 7,  673 => 200,  669 => 198,  664 => 195,  649 => 193,  634 => 191,  631 => 190,  617 => 189,  603 => 188,  599 => 186,  587 => 184,  581 => 183,  569 => 181,  563 => 180,  551 => 178,  545 => 177,  533 => 175,  527 => 174,  515 => 172,  506 => 171,  503 => 170,  497 => 169,  494 => 168,  481 => 282,  473 => 164,  467 => 163,  463 => 279,  460 => 278,  457 => 187,  454 => 159,  450 => 158,  446 => 157,  442 => 277,  423 => 155,  419 => 154,  398 => 150,  392 => 152,  389 => 151,  386 => 150,  368 => 167,  363 => 148,  360 => 220,  343 => 146,  338 => 143,  328 => 141,  323 => 64,  317 => 136,  314 => 135,  280 => 57,  269 => 55,  262 => 132,  237 => 85,  230 => 122,  223 => 42,  216 => 38,  209 => 69,  202 => 65,  172 => 37,  169 => 36,  163 => 30,  155 => 31,  152 => 30,  147 => 29,  141 => 28,  136 => 25,  132 => 85,  112 => 21,  107 => 20,  104 => 35,  100 => 18,  90 => 15,  77 => 14,  72 => 28,  62 => 17,  55 => 18,  52 => 6,  47 => 3,  29 => 1,  27 => 1,);
    }
}
