<?php

/* AdminBundle:TicketSlas:edit.html.twig */
class __TwigTemplate_19d7a94586096147e2f383c0ec708b20 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("AdminBundle::layout.html.twig");

        $this->blocks = array(
            'html_head' => array($this, 'block_html_head'),
            'page_js_exec' => array($this, 'block_page_js_exec'),
            'pagebar' => array($this, 'block_pagebar'),
            'page' => array($this, 'block_page'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "AdminBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 5
        $context["no_show_actions"] = array(0 => "add_sla", 1 => "remove_sla", 2 => "set_sla_status", 3 => "set_sla_complete", 4 => "recalculate_sla_status");
        // line 6
        $context["no_show_criteria"] = array(0 => "sla", 1 => "sla_status");
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_html_head($context, array $blocks = array())
    {
        // line 3
        echo "<script type=\"text/javascript\" src=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("javascripts/DeskPRO/Admin/PageHandler/SlaEdit.js"), "html", null, true);
        echo "\"></script>
";
    }

    // line 7
    public function block_page_js_exec($context, array $blocks = array())
    {
        // line 8
        echo "<script type=\"text/javascript\" >
\tfunction _page_exec() {
\t\twindow.DeskPRO_Page = new DeskPRO.Admin.PageHandler.SlaEdit();
\t}

\tfunction SlaEditPage_initTerms() {
\t\tvar basename;

\t\t";
        // line 16
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if ($this->getAttribute($_sla_, "warning_trigger")) {
            // line 17
            echo "\t\t\t";
            if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_sla_, "warning_trigger"), "actions"));
            foreach ($context['_seq'] as $context["_key"] => $context["action"]) {
                // line 18
                echo "\t\t\t\t";
                if (isset($context["action"])) { $_action_ = $context["action"]; } else { $_action_ = null; }
                if (isset($context["no_show_actions"])) { $_no_show_actions_ = $context["no_show_actions"]; } else { $_no_show_actions_ = null; }
                if (!twig_in_filter($this->getAttribute($_action_, "type"), $_no_show_actions_)) {
                    // line 19
                    echo "\t\t\t\t\tbasename = this.getBaseName('warning_actions');
\t\t\t\t\tthis.getActionEditor('warning_actions').addNewRow(
\t\t\t\t\t\t\$('#warning_action_terms'),
\t\t\t\t\t\tbasename,
\t\t\t\t\t\t{ type: '";
                    // line 23
                    if (isset($context["action"])) { $_action_ = $context["action"]; } else { $_action_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_action_, "type"), "html", null, true);
                    echo "', op: '";
                    if (isset($context["action"])) { $_action_ = $context["action"]; } else { $_action_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_action_, "op"), "html", null, true);
                    echo "', options: ";
                    if (isset($context["action"])) { $_action_ = $context["action"]; } else { $_action_ = null; }
                    echo twig_jsonencode_filter($this->getAttribute($_action_, "options"));
                    echo " }
\t\t\t\t\t);
\t\t\t\t";
                }
                // line 26
                echo "\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['action'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 27
            echo "\t\t";
        }
        // line 28
        echo "
\t\t";
        // line 29
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if ($this->getAttribute($_sla_, "fail_trigger")) {
            // line 30
            echo "\t\t\t";
            if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_sla_, "fail_trigger"), "actions"));
            foreach ($context['_seq'] as $context["_key"] => $context["action"]) {
                // line 31
                echo "\t\t\t\t";
                if (isset($context["action"])) { $_action_ = $context["action"]; } else { $_action_ = null; }
                if (isset($context["no_show_actions"])) { $_no_show_actions_ = $context["no_show_actions"]; } else { $_no_show_actions_ = null; }
                if (!twig_in_filter($this->getAttribute($_action_, "type"), $_no_show_actions_)) {
                    // line 32
                    echo "\t\t\t\t\tbasename = this.getBaseName('fail_actions');
\t\t\t\t\tthis.getActionEditor('fail_actions').addNewRow(
\t\t\t\t\t\t\$('#fail_action_terms'),
\t\t\t\t\t\tbasename,
\t\t\t\t\t\t{ type: '";
                    // line 36
                    if (isset($context["action"])) { $_action_ = $context["action"]; } else { $_action_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_action_, "type"), "html", null, true);
                    echo "', op: '";
                    if (isset($context["action"])) { $_action_ = $context["action"]; } else { $_action_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_action_, "op"), "html", null, true);
                    echo "', options: ";
                    if (isset($context["action"])) { $_action_ = $context["action"]; } else { $_action_ = null; }
                    echo twig_jsonencode_filter($this->getAttribute($_action_, "options"));
                    echo " }
\t\t\t\t\t);
\t\t\t\t";
                }
                // line 39
                echo "\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['action'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 40
            echo "\t\t";
        }
        // line 41
        echo "
\t\t";
        // line 42
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if ($this->getAttribute($_sla_, "apply_trigger")) {
            // line 43
            echo "\t\t\t";
            if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_sla_, "apply_trigger"), "terms"));
            foreach ($context['_seq'] as $context["_key"] => $context["action"]) {
                // line 44
                echo "\t\t\t\t";
                if (isset($context["action"])) { $_action_ = $context["action"]; } else { $_action_ = null; }
                if (isset($context["no_show_criteria"])) { $_no_show_criteria_ = $context["no_show_criteria"]; } else { $_no_show_criteria_ = null; }
                if (!twig_in_filter($this->getAttribute($_action_, "type"), $_no_show_criteria_)) {
                    // line 45
                    echo "\t\t\t\t\tbasename = this.getBaseName('apply_criteria');
\t\t\t\t\tthis.getCriteriaEditor('apply_criteria').addNewRow(
\t\t\t\t\t\t\$('#apply_trigger_terms'),
\t\t\t\t\t\tbasename,
\t\t\t\t\t\t{ type: '";
                    // line 49
                    if (isset($context["action"])) { $_action_ = $context["action"]; } else { $_action_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_action_, "type"), "html", null, true);
                    echo "', op: '";
                    if (isset($context["action"])) { $_action_ = $context["action"]; } else { $_action_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_action_, "op"), "html", null, true);
                    echo "', options: ";
                    if (isset($context["action"])) { $_action_ = $context["action"]; } else { $_action_ = null; }
                    echo twig_jsonencode_filter($this->getAttribute($_action_, "options"));
                    echo " }
\t\t\t\t\t);
\t\t\t\t";
                }
                // line 52
                echo "\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['action'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 53
            echo "\t\t";
        }
        // line 54
        echo "\t}
</script>
";
    }

    // line 57
    public function block_pagebar($context, array $blocks = array())
    {
        // line 58
        echo "<ul>
\t<li><a href=\"";
        // line 59
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_tickets_slas"), "html", null, true);
        echo "\">SLAs</a></li>
\t<li>";
        // line 60
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if ($this->getAttribute($_sla_, "id")) {
            echo "Edit SLA";
        } else {
            echo "New SLA";
        }
        echo "</li>
</ul>
\t";
        // line 62
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if ($this->getAttribute($_sla_, "id")) {
            // line 63
            echo "\t<nav>
\t\t<ul>
\t\t\t<li class=\"delete\"><a href=\"";
            // line 65
            if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_tickets_sla_delete", array("sla_id" => $this->getAttribute($_sla_, "id"), "_dp_security_token" => $this->env->getExtension('deskpro_templating')->securityToken("delete_sla"))), "html", null, true);
            echo "\" class=\"click-confirm\" data-confirm=\"Are you sure you want to delete this SLA?\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.delete");
            echo "</a></li>
\t\t</ul>
\t</nav>
";
        }
    }

    // line 70
    public function block_page($context, array $blocks = array())
    {
        // line 71
        echo "<form action=\"";
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_tickets_sla_edit", array("sla_id" => (($this->getAttribute($_sla_, "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_sla_, "id"), 0)) : (0)))), "html", null, true);
        echo "\" method=\"post\" class=\"dp-form sla-edit\">

\t";
        // line 73
        if (isset($context["errors"])) { $_errors_ = $context["errors"]; } else { $_errors_ = null; }
        if ($_errors_) {
            // line 74
            echo "\t<ul id=\"errors_container\">
\t\t";
            // line 75
            if (isset($context["errors"])) { $_errors_ = $context["errors"]; } else { $_errors_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_errors_);
            foreach ($context['_seq'] as $context["_key"] => $context["error"]) {
                // line 76
                echo "\t\t<li>";
                if (isset($context["error"])) { $_error_ = $context["error"]; } else { $_error_ = null; }
                echo twig_escape_filter($this->env, $_error_, "html", null, true);
                echo "</li>
\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['error'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 78
            echo "\t</ul>
\t";
        }
        // line 80
        echo "
\t";
        // line 81
        if (isset($context["show_warning_time_err"])) { $_show_warning_time_err_ = $context["show_warning_time_err"]; } else { $_show_warning_time_err_ = null; }
        if ($_show_warning_time_err_) {
            // line 82
            echo "\t\t<ul id=\"errors_container\">
\t\t\t<li>
\t\t\t\tThe SLA Warning happens before an SLA fails, so the time for an SLA Warning must be lower than the SLA time.
\t\t\t</li>
\t\t</ul>
\t";
        }
        // line 88
        echo "
\t<div class=\"dp-form-row\">
\t\t<div class=\"dp-form-label\">
\t\t\t<label>Title</label>
\t\t</div>
\t\t<div class=\"dp-form-input\">
\t\t\t<input type=\"text\" name=\"title\" value=\"";
        // line 94
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_sla_, "title"), "html", null, true);
        echo "\" maxlength=\"100\" />
\t\t</div>
\t</div>

\t<div class=\"dp-form-row-group\" style=\"margin-top: 30px\" data-element-handler=\"DeskPRO.Admin.ElementHandler.RadioExpander\" data-group-class=\"dp-input-group\">
\t\t<div class=\"dp-form-row\">
\t\t\t<div class=\"dp-form-label\">
\t\t\t\t<label>SLA Type</label>
\t\t\t</div>
\t\t\t<div class=\"dp-form-input\">
\t\t\t\t<div class=\"dp-input-group ";
        // line 104
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if ((($this->getAttribute($_sla_, "sla_type") == "first_response") || (!$this->getAttribute($_sla_, "sla_type")))) {
            echo "on";
        }
        echo "\">
\t\t\t\t\t<label><input type=\"radio\" class=\"option-trigger\" name=\"sla_type\" value=\"first_response\" ";
        // line 105
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if ((($this->getAttribute($_sla_, "sla_type") == "first_response") || (!$this->getAttribute($_sla_, "sla_type")))) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\t\t\tTime until first response</label>
\t\t\t\t</div>
\t\t\t\t<div class=\"dp-input-group ";
        // line 108
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if (($this->getAttribute($_sla_, "sla_type") == "resolution")) {
            echo "on";
        }
        echo "\">
\t\t\t\t\t<label><input type=\"radio\" class=\"option-trigger\" name=\"sla_type\" value=\"resolution\" ";
        // line 109
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if (($this->getAttribute($_sla_, "sla_type") == "resolution")) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\t\t\tTime until ticket resolution</label>
\t\t\t\t</div>
\t\t\t\t<div class=\"dp-input-group ";
        // line 112
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if (($this->getAttribute($_sla_, "sla_type") == "waiting_time")) {
            echo "on";
        }
        echo "\">
\t\t\t\t\t<label><input type=\"radio\" class=\"option-trigger\" name=\"sla_type\" value=\"waiting_time\" ";
        // line 113
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if (($this->getAttribute($_sla_, "sla_type") == "waiting_time")) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\t\t\tUser waiting time until ticket resolution</label>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>

\t<div class=\"dp-form-row-group\" style=\"margin-top: 30px\" data-element-handler=\"DeskPRO.Admin.ElementHandler.RadioExpander\" data-group-class=\"dp-input-group\" data-expand-class=\"dp-group-options\">
\t\t<div class=\"dp-form-row\">
\t\t\t<div class=\"dp-form-label\">
\t\t\t\t<label>Hours</label>
\t\t\t</div>
\t\t\t<div class=\"dp-form-input\">
\t\t\t\t<div class=\"dp-input-group ";
        // line 126
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if ((($this->getAttribute($_sla_, "active_time") == "default") || (!$this->getAttribute($_sla_, "active_time")))) {
            echo "on";
        }
        echo "\">
\t\t\t\t\t<a style=\"float:right\" href=\"";
        // line 127
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_features_work_hours"), "html", null, true);
        echo "\">Edit Default Working Hours</a>
\t\t\t\t\t<label><input type=\"radio\" class=\"option-trigger\" name=\"active_time\" value=\"default\" ";
        // line 128
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if ((($this->getAttribute($_sla_, "active_time") == "default") || (!$this->getAttribute($_sla_, "active_time")))) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\t\t\tDefault ticket working hours</label>
\t\t\t\t\t<div style=\"margin-left: 30px; margin-top: 3px\">
\t\t\t\t\t\t";
        // line 131
        if (isset($context["default_work_hours"])) { $_default_work_hours_ = $context["default_work_hours"]; } else { $_default_work_hours_ = null; }
        if (($this->getAttribute($_default_work_hours_, "active_time") == "all")) {
            // line 132
            echo "\t\t\t\t\t\t\t24 hours a day, 7 days a week
\t\t\t\t\t\t";
        } else {
            // line 134
            echo "\t\t\t\t\t\t\t";
            if (isset($context["default_work_hours"])) { $_default_work_hours_ = $context["default_work_hours"]; } else { $_default_work_hours_ = null; }
            echo twig_escape_filter($this->env, sprintf("%02d:%02d - %02d:%02d", $this->getAttribute($_default_work_hours_, "start_hour"), $this->getAttribute($_default_work_hours_, "start_minute"), $this->getAttribute($_default_work_hours_, "end_hour"), $this->getAttribute($_default_work_hours_, "end_minute")), "html", null, true);
            echo "
\t\t\t\t\t\t\t(time zone: ";
            // line 135
            if (isset($context["default_work_hours"])) { $_default_work_hours_ = $context["default_work_hours"]; } else { $_default_work_hours_ = null; }
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($_default_work_hours_, "timezone", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_default_work_hours_, "timezone"), $this->getAttribute($_app_, "getSetting", array(0 => "core.default_timezone"), "method"))) : ($this->getAttribute($_app_, "getSetting", array(0 => "core.default_timezone"), "method"))), "html", null, true);
            echo ")";
            // line 136
            if (isset($context["default_work_hours"])) { $_default_work_hours_ = $context["default_work_hours"]; } else { $_default_work_hours_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_default_work_hours_, "days"));
            foreach ($context['_seq'] as $context["day"] => $context["null"]) {
                echo ",
\t\t\t\t\t\t\t\t";
                // line 137
                if (isset($context["day"])) { $_day_ = $context["day"]; } else { $_day_ = null; }
                if (($_day_ == 0)) {
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.time.long-day_sunday");
                } elseif (($_day_ == 1)) {
                    // line 138
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.time.long-day_monday");
                } elseif (($_day_ == 2)) {
                    // line 139
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.time.long-day_tuesday");
                } elseif (($_day_ == 3)) {
                    // line 140
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.time.long-day_wednesday");
                } elseif (($_day_ == 4)) {
                    // line 141
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.time.long-day_thursday");
                } elseif (($_day_ == 5)) {
                    // line 142
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.time.long-day_friday");
                } elseif (($_day_ == 6)) {
                    // line 143
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.time.long-day_saturday");
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['day'], $context['null'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 144
            echo " (excluding holidays)
\t\t\t\t\t\t";
        }
        // line 146
        echo "\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t<div class=\"dp-input-group ";
        // line 148
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if (($this->getAttribute($_sla_, "active_time") == "all")) {
            echo "on";
        }
        echo "\">
\t\t\t\t\t<label><input type=\"radio\" class=\"option-trigger\" name=\"active_time\" value=\"all\" ";
        // line 149
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if (($this->getAttribute($_sla_, "active_time") == "all")) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\t\t\t24 hours a day, 7 days a week</label>
\t\t\t\t</div>
\t\t\t\t<div class=\"dp-input-group ";
        // line 152
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if (($this->getAttribute($_sla_, "active_time") == "work_hours")) {
            echo "on";
        }
        echo "\">
\t\t\t\t\t<label><input type=\"radio\" class=\"option-trigger\" name=\"active_time\" value=\"work_hours\" ";
        // line 153
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if (($this->getAttribute($_sla_, "active_time") == "work_hours")) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\t\t\tSet working hours
\t\t\t\t\t</label>
\t\t\t\t\t<ul class=\"dp-group-options sla-work-hours\" style=\"display: none\">
\t\t\t\t\t\t<li class=\"sla-work-time\">
\t\t\t\t\t\t\t<label>Time</label>
\t\t\t\t\t\t\t<div>
\t\t\t\t\t\t\t\t<select name=\"work_start_hour\">
\t\t\t\t\t\t\t\t";
        // line 161
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable(range(0, 23));
        foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
            // line 162
            echo "\t\t\t\t\t\t\t\t\t<option value=\"";
            if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
            echo twig_escape_filter($this->env, $_i_, "html", null, true);
            echo "\" ";
            if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
            if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
            if (($_i_ == $this->getAttribute($_sla_, "work_start_hour"))) {
                echo "selected=\"selected\"";
            }
            echo ">";
            if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
            echo twig_escape_filter($this->env, sprintf("%02d", $_i_), "html", null, true);
            echo "</option>
\t\t\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 164
        echo "\t\t\t\t\t\t\t\t</select>:<select name=\"work_start_minute\">
\t\t\t\t\t\t\t\t";
        // line 165
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable(range(0, 59));
        foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
            // line 166
            echo "\t\t\t\t\t\t\t\t\t<option value=\"";
            if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
            echo twig_escape_filter($this->env, $_i_, "html", null, true);
            echo "\" ";
            if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
            if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
            if (($_i_ == $this->getAttribute($_sla_, "work_start_minute"))) {
                echo "selected=\"selected\"";
            }
            echo ">";
            if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
            echo twig_escape_filter($this->env, sprintf("%02d", $_i_), "html", null, true);
            echo "</option>
\t\t\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 168
        echo "\t\t\t\t\t\t\t\t</select>
\t\t\t\t\t\t\t\t<span class=\"sla-to\">to</span>
\t\t\t\t\t\t\t\t<select name=\"work_end_hour\">
\t\t\t\t\t\t\t\t";
        // line 171
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable(range(0, 23));
        foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
            // line 172
            echo "\t\t\t\t\t\t\t\t\t<option value=\"";
            if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
            echo twig_escape_filter($this->env, $_i_, "html", null, true);
            echo "\" ";
            if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
            if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
            if (($_i_ == $this->getAttribute($_sla_, "work_end_hour"))) {
                echo "selected=\"selected\"";
            }
            echo ">";
            if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
            echo twig_escape_filter($this->env, sprintf("%02d", $_i_), "html", null, true);
            echo "</option>
\t\t\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 174
        echo "\t\t\t\t\t\t\t\t</select>:<select name=\"work_end_minute\">
\t\t\t\t\t\t\t\t";
        // line 175
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable(range(0, 59));
        foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
            // line 176
            echo "\t\t\t\t\t\t\t\t\t<option value=\"";
            if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
            echo twig_escape_filter($this->env, $_i_, "html", null, true);
            echo "\" ";
            if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
            if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
            if (($_i_ == $this->getAttribute($_sla_, "work_end_minute"))) {
                echo "selected=\"selected\"";
            }
            echo ">";
            if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
            echo twig_escape_filter($this->env, sprintf("%02d", $_i_), "html", null, true);
            echo "</option>
\t\t\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 178
        echo "\t\t\t\t\t\t\t\t</select></div>
\t\t\t\t\t\t</li>

\t\t\t\t\t\t<li class=\"sla-timezone\">
\t\t\t\t\t\t\t<label>Time Zone</label>
\t\t\t\t\t\t\t<div>
\t\t\t\t\t\t\t\t<select name=\"work_timezone\">
\t\t\t\t\t\t\t\t";
        // line 185
        if (isset($context["timezones"])) { $_timezones_ = $context["timezones"]; } else { $_timezones_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_timezones_);
        foreach ($context['_seq'] as $context["_key"] => $context["tz"]) {
            // line 186
            echo "\t\t\t\t\t\t\t\t\t<option value=\"";
            if (isset($context["tz"])) { $_tz_ = $context["tz"]; } else { $_tz_ = null; }
            echo twig_escape_filter($this->env, $_tz_, "html", null, true);
            echo "\" ";
            if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
            if (isset($context["tz"])) { $_tz_ = $context["tz"]; } else { $_tz_ = null; }
            if (($this->getAttribute($_sla_, "work_timezone") == $_tz_)) {
                echo "selected=\"selected\"";
            }
            echo ">";
            if (isset($context["tz"])) { $_tz_ = $context["tz"]; } else { $_tz_ = null; }
            echo twig_escape_filter($this->env, $_tz_, "html", null, true);
            echo "</option>
\t\t\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tz'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 188
        echo "\t\t\t\t\t\t\t\t</select>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</li>

\t\t\t\t\t\t<li class=\"sla-work-days\">
\t\t\t\t\t\t\t<label>Working Days</label>
\t\t\t\t\t\t\t<div>
\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"work_days[]\" value=\"1\" ";
        // line 195
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if ($this->getAttribute($this->getAttribute($_sla_, "work_days"), 1, array(), "array")) {
            echo "checked=\"checked\"";
        }
        echo " /> Mon</label>
\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"work_days[]\" value=\"2\" ";
        // line 196
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if ($this->getAttribute($this->getAttribute($_sla_, "work_days"), 2, array(), "array")) {
            echo "checked=\"checked\"";
        }
        echo " /> Tue</label>
\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"work_days[]\" value=\"3\" ";
        // line 197
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if ($this->getAttribute($this->getAttribute($_sla_, "work_days"), 3, array(), "array")) {
            echo "checked=\"checked\"";
        }
        echo "/> Wed</label>
\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"work_days[]\" value=\"4\" ";
        // line 198
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if ($this->getAttribute($this->getAttribute($_sla_, "work_days"), 4, array(), "array")) {
            echo "checked=\"checked\"";
        }
        echo " /> Thu</label>
\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"work_days[]\" value=\"5\" ";
        // line 199
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if ($this->getAttribute($this->getAttribute($_sla_, "work_days"), 5, array(), "array")) {
            echo "checked=\"checked\"";
        }
        echo " /> Fri</label>
\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"work_days[]\" value=\"6\" ";
        // line 200
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if ($this->getAttribute($this->getAttribute($_sla_, "work_days"), 6, array(), "array")) {
            echo "checked=\"checked\"";
        }
        echo " /> Sat</label>
\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"work_days[]\" value=\"0\" ";
        // line 201
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if ($this->getAttribute($this->getAttribute($_sla_, "work_days"), 0, array(), "array")) {
            echo "checked=\"checked\"";
        }
        echo " /> Sun</label>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</li>

\t\t\t\t\t\t<li class=\"sla-work-holidays\" id=\"holidays\">
\t\t\t\t\t\t\t<label>Holidays</label>
\t\t\t\t\t\t\t<div class=\"sla-holiday-years\">
\t\t\t\t\t\t\t\t<select id=\"holiday_year\">
\t\t\t\t\t\t\t\t";
        // line 209
        if (isset($context["years"])) { $_years_ = $context["years"]; } else { $_years_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_years_);
        foreach ($context['_seq'] as $context["_key"] => $context["year"]) {
            // line 210
            echo "\t\t\t\t\t\t\t\t\t<option value=\"";
            if (isset($context["year"])) { $_year_ = $context["year"]; } else { $_year_ = null; }
            echo twig_escape_filter($this->env, $_year_, "html", null, true);
            echo "\">";
            if (isset($context["year"])) { $_year_ = $context["year"]; } else { $_year_ = null; }
            echo twig_escape_filter($this->env, $_year_, "html", null, true);
            echo "</option>
\t\t\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['year'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 212
        echo "\t\t\t\t\t\t\t\t</select>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<ul class=\"sla-holidays\" id=\"holiday_options\" data-count=\"";
        // line 215
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        echo twig_escape_filter($this->env, twig_length_filter($this->env, $this->getAttribute($_sla_, "work_holidays")), "html", null, true);
        echo "\" data-months=\"";
        if (isset($context["months"])) { $_months_ = $context["months"]; } else { $_months_ = null; }
        echo twig_escape_filter($this->env, twig_jsonencode_filter($_months_), "html", null, true);
        echo "\">
\t\t\t\t\t\t\t\t<li id=\"holiday_template\" style=\"display:none\">
\t\t\t\t\t\t\t\t\t<span class=\"remove delete-icon\"></span>
\t\t\t\t\t\t\t\t\t<span class=\"date\">[date]</span>
\t\t\t\t\t\t\t\t\t-
\t\t\t\t\t\t\t\t\t<span class=\"name\">[name]</span>
\t\t\t\t\t\t\t\t\t<span class=\"repeat\">(repeats annually)</span>
\t\t\t\t\t\t\t\t\t<span class=\"add-all\">(adding to all SLAs)</span>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t";
        // line 224
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_sla_, "holidays_sorted"));
        foreach ($context['_seq'] as $context["key"] => $context["holiday"]) {
            // line 225
            echo "\t\t\t\t\t\t\t\t<li ";
            if (isset($context["holiday"])) { $_holiday_ = $context["holiday"]; } else { $_holiday_ = null; }
            if ($this->getAttribute($_holiday_, "year")) {
                echo "data-year=\"";
                if (isset($context["holiday"])) { $_holiday_ = $context["holiday"]; } else { $_holiday_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_holiday_, "year"), "html", null, true);
                echo "\"";
            }
            // line 226
            echo "\t\t\t\t\t\t\t\t\tdata-month=\"";
            if (isset($context["holiday"])) { $_holiday_ = $context["holiday"]; } else { $_holiday_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_holiday_, "month"), "html", null, true);
            echo "\"
\t\t\t\t\t\t\t\t\tdata-day=\"";
            // line 227
            if (isset($context["holiday"])) { $_holiday_ = $context["holiday"]; } else { $_holiday_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_holiday_, "day"), "html", null, true);
            echo "\"
\t\t\t\t\t\t\t\t>
\t\t\t\t\t\t\t\t\t<span class=\"remove delete-icon\"></span>
\t\t\t\t\t\t\t\t\t<span class=\"date\">";
            // line 230
            if (isset($context["holiday"])) { $_holiday_ = $context["holiday"]; } else { $_holiday_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_holiday_, "day"), "html", null, true);
            echo " ";
            if (isset($context["months"])) { $_months_ = $context["months"]; } else { $_months_ = null; }
            if (isset($context["holiday"])) { $_holiday_ = $context["holiday"]; } else { $_holiday_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_months_, $this->getAttribute($_holiday_, "month"), array(), "array"), "html", null, true);
            if (isset($context["holiday"])) { $_holiday_ = $context["holiday"]; } else { $_holiday_ = null; }
            if ($this->getAttribute($_holiday_, "year")) {
                echo " ";
                if (isset($context["holiday"])) { $_holiday_ = $context["holiday"]; } else { $_holiday_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_holiday_, "year"), "html", null, true);
            }
            echo "</span>
\t\t\t\t\t\t\t\t\t-
\t\t\t\t\t\t\t\t\t<span class=\"name\">";
            // line 232
            if (isset($context["holiday"])) { $_holiday_ = $context["holiday"]; } else { $_holiday_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_holiday_, "name"), "html", null, true);
            echo "</span>
\t\t\t\t\t\t\t\t\t";
            // line 233
            if (isset($context["holiday"])) { $_holiday_ = $context["holiday"]; } else { $_holiday_ = null; }
            if ((!$this->getAttribute($_holiday_, "year"))) {
                echo "<span class=\"repeat\">(repeats annually)</span>";
            }
            // line 234
            echo "\t\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"work_holidays[";
            if (isset($context["key"])) { $_key_ = $context["key"]; } else { $_key_ = null; }
            echo twig_escape_filter($this->env, $_key_, "html", null, true);
            echo "][name]\" value=\"";
            if (isset($context["holiday"])) { $_holiday_ = $context["holiday"]; } else { $_holiday_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_holiday_, "name"), "html", null, true);
            echo "\" />
\t\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"work_holidays[";
            // line 235
            if (isset($context["key"])) { $_key_ = $context["key"]; } else { $_key_ = null; }
            echo twig_escape_filter($this->env, $_key_, "html", null, true);
            echo "][day]\" value=\"";
            if (isset($context["holiday"])) { $_holiday_ = $context["holiday"]; } else { $_holiday_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_holiday_, "day"), "html", null, true);
            echo "\" />
\t\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"work_holidays[";
            // line 236
            if (isset($context["key"])) { $_key_ = $context["key"]; } else { $_key_ = null; }
            echo twig_escape_filter($this->env, $_key_, "html", null, true);
            echo "][month]\" value=\"";
            if (isset($context["holiday"])) { $_holiday_ = $context["holiday"]; } else { $_holiday_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_holiday_, "month"), "html", null, true);
            echo "\" />
\t\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"work_holidays[";
            // line 237
            if (isset($context["key"])) { $_key_ = $context["key"]; } else { $_key_ = null; }
            echo twig_escape_filter($this->env, $_key_, "html", null, true);
            echo "][year]\" value=\"";
            if (isset($context["holiday"])) { $_holiday_ = $context["holiday"]; } else { $_holiday_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_holiday_, "year"), "html", null, true);
            echo "\" />
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['key'], $context['holiday'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 240
        echo "\t\t\t\t\t\t\t</ul>

\t\t\t\t\t\t\t<div class=\"sla-holiday-add\">
\t\t\t\t\t\t\t\t<input class=\"date\" id=\"holiday_date\" type=\"text\" placeholder=\"Date\" style=\"width: 75px\" />
\t\t\t\t\t\t\t\t<input class=\"name\" id=\"holiday_name\" type=\"text\" placeholder=\"Name\" style=\"width: 150px\" />
\t\t\t\t\t\t\t\t<label><input id=\"holiday_repeat\" type=\"checkbox\" value=\"1\" /> Repeats annually</label>
\t\t\t\t\t\t\t\t<label><input id=\"holiday_add_all\" type=\"checkbox\" value=\"1\" /> Add to all SLAs</label>
\t\t\t\t\t\t\t\t<button id=\"holiday_add\" class=\"btn small\">Add</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</li>


\t\t\t\t\t\t<li class=\"alert-message block-message note\">
\t\t\t\t\t\t\tTime will only be logged towards the SLA warning or failure within working hours.
\t\t\t\t\t\t\tThe warning and failure time units below should reflect the amount of time that passes during working hours only.
\t\t\t\t\t\t</li>
\t\t\t\t\t</ul>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>

\t<div class=\"dp-form-row\" style=\"margin-top: 30px; ";
        // line 262
        if (isset($context["show_warning_time_err"])) { $_show_warning_time_err_ = $context["show_warning_time_err"]; } else { $_show_warning_time_err_ = null; }
        if ($_show_warning_time_err_) {
            echo "border: 2px solid red; padding: 6px;";
        }
        echo "\">
\t\t<div class=\"dp-form-label\">
\t\t\t<label>SLA Warning</label>
\t\t</div>
\t\t<div class=\"dp-form-input\">
\t\t\t<span class=\"sla-type-text first_response\">When a ticket has not received a response in</span>
\t\t\t<span class=\"sla-type-text resolution\">When a ticket is not resolved in</span>
\t\t\t<span class=\"sla-type-text waiting_time\">When a user has been waiting more than</span>

\t\t\t<input type=\"text\" name=\"sla_warning_time\" value=\"";
        // line 271
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_sla_, "warning_trigger")) ? ($this->getAttribute($this->getAttribute($_sla_, "warning_trigger"), "getOptionTime", array(), "method")) : (30)), "html", null, true);
        echo "\" size=\"10\" style=\"width: 40px; position:relative; top: 2px;\" class=\"small\" />
\t\t\t<select name=\"sla_warning_scale\" class=\"select2\">
\t\t\t\t";
        // line 273
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable(array("minutes" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.minutes"), "hours" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.hours"), "days" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.days"), "weeks" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.weeks"), "months" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.months")));
        foreach ($context['_seq'] as $context["scale"] => $context["label"]) {
            // line 274
            echo "\t\t\t\t\t<option ";
            if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
            if (isset($context["scale"])) { $_scale_ = $context["scale"]; } else { $_scale_ = null; }
            if (((($this->getAttribute($_sla_, "warning_trigger")) ? ($this->getAttribute($this->getAttribute($_sla_, "warning_trigger"), "getOptionScale", array(), "method")) : ("minutes")) == $_scale_)) {
                echo "selected=\"selected\"";
            }
            echo " value=\"";
            if (isset($context["scale"])) { $_scale_ = $context["scale"]; } else { $_scale_ = null; }
            echo twig_escape_filter($this->env, $_scale_, "html", null, true);
            echo "\">";
            if (isset($context["label"])) { $_label_ = $context["label"]; } else { $_label_ = null; }
            echo twig_escape_filter($this->env, $_label_, "html", null, true);
            echo "</option>
\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['scale'], $context['label'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 276
        echo "\t\t\t</select>
\t\t\tof SLA countable time, the ticket is close to failing the SLA.
\t\t\t<div class=\"dp-sub-section\" style=\"border-top: none; margin-top: 2px\">
\t\t\t\t<div class=\"terms-wrap trigger-actions\">
\t\t\t\t\t<div class=\"terms-input\" id=\"warning_action_terms\"></div>
\t\t\t\t\t<div class=\"term-row add-term-row\" data-name=\"warning_actions\"><span class=\"add-term\">";
        // line 281
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.widget_add");
        echo "</span> Add Action</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>

\t<div class=\"dp-form-row\" style=\"margin-top: 30px\">
\t\t<div class=\"dp-form-label\">
\t\t\t<label>SLA Failure</label>
\t\t</div>
\t\t<div class=\"dp-form-input\">
\t\t\t<span class=\"sla-type-text first_response\">When a ticket has not received a response in</span>
\t\t\t<span class=\"sla-type-text resolution\">When a ticket is not resolved in</span>
\t\t\t<span class=\"sla-type-text waiting_time\">When a user has been waiting more than</span>

\t\t\t<input type=\"text\" name=\"sla_fail_time\" value=\"";
        // line 296
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_sla_, "fail_trigger")) ? ($this->getAttribute($this->getAttribute($_sla_, "fail_trigger"), "getOptionTime", array(), "method")) : (60)), "html", null, true);
        echo "\" size=\"10\" style=\"width: 40px; position:relative; top: 2px;\" class=\"small\" />
\t\t\t<select name=\"sla_fail_scale\" class=\"select2\">
\t\t\t\t";
        // line 298
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable(array("minutes" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.minutes"), "hours" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.hours"), "days" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.days"), "weeks" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.weeks"), "months" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.months")));
        foreach ($context['_seq'] as $context["scale"] => $context["label"]) {
            // line 299
            echo "\t\t\t\t\t<option ";
            if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
            if (isset($context["scale"])) { $_scale_ = $context["scale"]; } else { $_scale_ = null; }
            if (((($this->getAttribute($_sla_, "fail_trigger")) ? ($this->getAttribute($this->getAttribute($_sla_, "fail_trigger"), "getOptionScale", array(), "method")) : ("minutes")) == $_scale_)) {
                echo "selected=\"selected\"";
            }
            echo " value=\"";
            if (isset($context["scale"])) { $_scale_ = $context["scale"]; } else { $_scale_ = null; }
            echo twig_escape_filter($this->env, $_scale_, "html", null, true);
            echo "\">";
            if (isset($context["label"])) { $_label_ = $context["label"]; } else { $_label_ = null; }
            echo twig_escape_filter($this->env, $_label_, "html", null, true);
            echo "</option>
\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['scale'], $context['label'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 301
        echo "\t\t\t</select>
\t\t\tof SLA countable time, the ticket has failed the SLA.
\t\t\t<div class=\"dp-sub-section\" style=\"border-top: none; margin-top: 2px\">
\t\t\t\t<div class=\"terms-wrap trigger-actions\">
\t\t\t\t\t<div class=\"terms-input\" id=\"fail_action_terms\"></div>
\t\t\t\t\t<div class=\"term-row add-term-row\" data-name=\"fail_actions\"><span class=\"add-term\">";
        // line 306
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.widget_add");
        echo "</span> Add Action</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>

\t<div class=\"dp-form-row\" style=\"margin-top: 30px\">
\t\t<div class=\"dp-form-label\">
\t\t\t<label>SLA Application</label>
\t\t</div>
\t\t<div class=\"dp-form-input\">
\t\t\t<select name=\"apply_type\" class=\"select2\" id=\"apply_type_select\">
\t\t\t\t<option value=\"all\" ";
        // line 318
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if (($this->getAttribute($_sla_, "apply_type") == "all")) {
            echo "selected=\"selected\"";
        }
        echo ">Apply to all tickets</option>
\t\t\t\t<option value=\"priority\" ";
        // line 319
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if (($this->getAttribute($_sla_, "apply_type") == "priority")) {
            echo "selected=\"selected\"";
        }
        echo ">Apply when a user submits a ticket with a specific priority</option>
\t\t\t\t<option value=\"criteria\" ";
        // line 320
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if (($this->getAttribute($_sla_, "apply_type") == "criteria")) {
            echo "selected=\"selected\"";
        }
        echo ">Apply to new tickets with specific criteria</option>
\t\t\t\t<option value=\"manual\" ";
        // line 321
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if (($this->getAttribute($_sla_, "apply_type") == "manual")) {
            echo "selected=\"selected\"";
        }
        echo ">Agents manually assign the SLA</option>
\t\t\t\t<option value=\"people_orgs\" ";
        // line 322
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if (($this->getAttribute($_sla_, "apply_type") == "people_orgs")) {
            echo "selected=\"selected\"";
        }
        echo ">Apply to the following people and organizations</option>
\t\t\t</select>

\t\t\t<div class=\"apply_type_row\" id=\"apply_type_show_priority\" style=\"display:none; margin-top: 5px\">
\t\t\t\t<select name=\"apply_priority_id\" class=\"select2\" data-select-width=\"auto\">
\t\t\t\t";
        // line 327
        if (isset($context["priorities"])) { $_priorities_ = $context["priorities"]; } else { $_priorities_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_priorities_);
        foreach ($context['_seq'] as $context["priority_id"] => $context["name"]) {
            // line 328
            echo "\t\t\t\t\t<option value=\"";
            if (isset($context["priority_id"])) { $_priority_id_ = $context["priority_id"]; } else { $_priority_id_ = null; }
            echo twig_escape_filter($this->env, $_priority_id_, "html", null, true);
            echo "\" ";
            if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
            if (isset($context["priority_id"])) { $_priority_id_ = $context["priority_id"]; } else { $_priority_id_ = null; }
            if (($this->getAttribute($_sla_, "apply_priority") && ($this->getAttribute($this->getAttribute($_sla_, "apply_priority"), "id") == $_priority_id_))) {
                echo "selected=\"selected\"";
            }
            echo ">";
            if (isset($context["name"])) { $_name_ = $context["name"]; } else { $_name_ = null; }
            echo twig_escape_filter($this->env, $_name_, "html", null, true);
            echo "</option>
\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['priority_id'], $context['name'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 330
        echo "\t\t\t\t</select>
\t\t\t</div>

\t\t\t<div class=\"apply_type_row\" id=\"apply_type_show_criteria\" style=\"display:none; margin-top: 5px\">
\t\t\t\t<div id=\"apply_trigger_container\" class=\"dp-sub-section\" style=\"border-top: none;\">
\t\t\t\t\t<div class=\"terms-wrap trigger-criteria\">
\t\t\t\t\t\t<div class=\"terms-input\" id=\"apply_trigger_terms\"></div>
\t\t\t\t\t\t<div class=\"term-row add-term-row\" data-name=\"apply_criteria\"><span class=\"add-term\">";
        // line 337
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.widget_add");
        echo "</span> Add Criteria</div>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>

\t\t\t<div class=\"apply_type_row\" id=\"apply_type_show_people_orgs\" style=\"display:none; margin-top: 5px\">
\t\t\t\t<div style=\"overflow: hidden\">
\t\t\t\t\t<label style=\"float: left\">People:</label>
\t\t\t\t\t<div style=\"margin-left: 100px;\"><input type=\"hidden\" name=\"person_ids\" id=\"person_ids\" data-select-width=\"auto\" data-select-width-shrink=\"20\" value=\"";
        // line 345
        if (isset($context["selected_people"])) { $_selected_people_ = $context["selected_people"]; } else { $_selected_people_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_selected_people_);
        foreach ($context['_seq'] as $context["id"] => $context["null"]) {
            if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
            echo twig_escape_filter($this->env, $_id_, "html", null, true);
            echo ",";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['id'], $context['null'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        echo "\" data-selected=\"";
        if (isset($context["selected_people"])) { $_selected_people_ = $context["selected_people"]; } else { $_selected_people_ = null; }
        echo twig_escape_filter($this->env, twig_jsonencode_filter($_selected_people_), "html", null, true);
        echo "\" /></div>
\t\t\t\t</div>
\t\t\t\t<div style=\"overflow: hidden; margin-top: 5px\">
\t\t\t\t\t<label style=\"float: left\">Organizations:</label>
\t\t\t\t\t<div style=\"margin-left: 100px;\"><input type=\"hidden\" name=\"organization_ids\" id=\"organization_ids\" data-select-width=\"auto\" data-select-width-shrink=\"20\" value=\"";
        // line 349
        if (isset($context["selected_organizations"])) { $_selected_organizations_ = $context["selected_organizations"]; } else { $_selected_organizations_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_selected_organizations_);
        foreach ($context['_seq'] as $context["id"] => $context["null"]) {
            if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
            echo twig_escape_filter($this->env, $_id_, "html", null, true);
            echo ",";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['id'], $context['null'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        echo "\" data-selected=\"";
        if (isset($context["selected_organizations"])) { $_selected_organizations_ = $context["selected_organizations"]; } else { $_selected_organizations_ = null; }
        echo twig_escape_filter($this->env, twig_jsonencode_filter($_selected_organizations_), "html", null, true);
        echo "\" /></div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>

\t<div class=\"alert-message block-message note\">
\t\tThis SLA will not be automatically applied to existing tickets. It will only be automatically applied to new tickets that meet your requirements.
\t\t";
        // line 357
        if (isset($context["sla"])) { $_sla_ = $context["sla"]; } else { $_sla_ = null; }
        if ($this->getAttribute($_sla_, "id")) {
            echo "If you change SLA types or time settings, these changes will not be fully applied to existing tickets with this SLA until the SLA is removed and re-added or the ticket's state changes.";
        }
        // line 358
        echo "\t</div>

\t<footer class=\"controls\">
\t\t<button class=\"clean-white\">Save SLA</button>
\t</footer>

\t";
        // line 364
        echo $this->env->getExtension('deskpro_templating')->formToken();
        echo "
\t<input type=\"hidden\" name=\"process\" value=\"1\" />
</form>

";
        // line 368
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        if (isset($context["no_show_criteria"])) { $_no_show_criteria_ = $context["no_show_criteria"]; } else { $_no_show_criteria_ = null; }
        if (isset($context["criteria_trigger"])) { $_criteria_trigger_ = $context["criteria_trigger"]; } else { $_criteria_trigger_ = null; }
        $this->env->loadTemplate("AdminBundle:TicketTriggers:criteria.html.twig")->display(array_merge($context, array("classname" => "criteria-tpl", "term_options" => $_term_options_, "no_show_fields" => $_no_show_criteria_, "event_trigger" => $this->getAttribute($_criteria_trigger_, "event_trigger"), "event_trigger_master" => $this->getAttribute($_criteria_trigger_, "getMasterEvent", array(), "method"), "event_trigger_sub" => $this->getAttribute($_criteria_trigger_, "getSubEvent", array(), "method"))));
        // line 376
        echo "
";
        // line 377
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        if (isset($context["no_show_actions"])) { $_no_show_actions_ = $context["no_show_actions"]; } else { $_no_show_actions_ = null; }
        if (isset($context["action_trigger"])) { $_action_trigger_ = $context["action_trigger"]; } else { $_action_trigger_ = null; }
        $this->env->loadTemplate("AdminBundle:TicketTriggers:actions.html.twig")->display(array_merge($context, array("classname" => "actions-tpl", "term_options" => $_term_options_, "no_show_fields" => $_no_show_actions_, "trigger" => $_action_trigger_, "event_trigger" => $this->getAttribute($_action_trigger_, "event_trigger"), "event_trigger_master" => $this->getAttribute($_action_trigger_, "getMasterEvent", array(), "method"), "event_trigger_sub" => $this->getAttribute($_action_trigger_, "getSubEvent", array(), "method"))));
    }

    public function getTemplateName()
    {
        return "AdminBundle:TicketSlas:edit.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1083 => 357,  995 => 327,  984 => 322,  963 => 319,  941 => 306,  851 => 271,  682 => 209,  1365 => 549,  1359 => 545,  1349 => 539,  1336 => 537,  1331 => 536,  1323 => 531,  1319 => 530,  1312 => 527,  1284 => 519,  1272 => 510,  1268 => 509,  1261 => 506,  1251 => 501,  1245 => 500,  1231 => 496,  1207 => 493,  1197 => 491,  1180 => 484,  1173 => 481,  1169 => 480,  1157 => 475,  1147 => 473,  1109 => 466,  1065 => 440,  1059 => 349,  1047 => 428,  1044 => 424,  1033 => 420,  1009 => 408,  991 => 405,  987 => 404,  973 => 395,  931 => 378,  924 => 373,  911 => 298,  906 => 370,  885 => 356,  872 => 354,  855 => 348,  749 => 279,  701 => 237,  594 => 164,  1163 => 476,  1143 => 471,  1087 => 526,  1077 => 509,  1051 => 430,  1037 => 480,  1010 => 476,  999 => 458,  932 => 414,  899 => 405,  895 => 404,  933 => 149,  914 => 133,  909 => 132,  833 => 329,  783 => 235,  755 => 320,  666 => 300,  453 => 187,  639 => 110,  568 => 254,  520 => 249,  657 => 287,  572 => 251,  609 => 459,  20 => 1,  659 => 207,  562 => 240,  548 => 165,  558 => 174,  479 => 206,  589 => 100,  457 => 145,  413 => 172,  953 => 430,  948 => 403,  935 => 394,  929 => 391,  916 => 382,  864 => 365,  844 => 348,  816 => 342,  807 => 370,  801 => 338,  774 => 234,  766 => 328,  737 => 318,  685 => 293,  664 => 194,  635 => 281,  593 => 185,  546 => 236,  532 => 68,  865 => 221,  852 => 347,  838 => 208,  820 => 201,  781 => 333,  764 => 232,  725 => 256,  632 => 283,  602 => 167,  565 => 176,  529 => 282,  505 => 267,  487 => 53,  473 => 221,  1853 => 842,  1849 => 841,  1839 => 837,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 813,  1778 => 812,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 782,  1705 => 781,  1688 => 770,  1681 => 769,  1677 => 768,  1667 => 764,  1656 => 762,  1648 => 757,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 714,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 695,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 651,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 522,  1289 => 520,  1276 => 604,  1263 => 601,  1244 => 596,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 565,  1181 => 561,  1171 => 557,  1158 => 550,  1114 => 534,  1104 => 465,  1072 => 512,  1062 => 439,  1024 => 494,  1014 => 490,  1000 => 328,  990 => 478,  980 => 474,  960 => 466,  918 => 448,  888 => 376,  834 => 346,  673 => 342,  636 => 185,  462 => 192,  454 => 103,  1144 => 542,  1139 => 399,  1131 => 399,  1127 => 397,  1110 => 396,  1092 => 459,  1089 => 390,  1086 => 520,  1084 => 388,  1063 => 387,  1060 => 386,  1055 => 385,  1050 => 384,  1035 => 372,  1019 => 330,  1003 => 263,  959 => 251,  900 => 366,  880 => 276,  870 => 430,  867 => 353,  859 => 362,  848 => 215,  839 => 376,  828 => 357,  823 => 374,  809 => 181,  800 => 315,  797 => 366,  794 => 336,  786 => 174,  740 => 162,  734 => 332,  703 => 354,  693 => 350,  630 => 278,  626 => 195,  614 => 275,  610 => 169,  581 => 247,  564 => 229,  525 => 235,  722 => 162,  697 => 256,  674 => 277,  671 => 425,  577 => 257,  569 => 243,  557 => 169,  502 => 229,  497 => 132,  445 => 197,  729 => 159,  684 => 261,  676 => 299,  669 => 254,  660 => 145,  647 => 198,  643 => 244,  601 => 178,  570 => 156,  522 => 200,  501 => 58,  296 => 67,  374 => 149,  631 => 111,  616 => 281,  608 => 266,  605 => 16,  596 => 102,  574 => 165,  561 => 175,  527 => 142,  433 => 115,  388 => 161,  426 => 142,  383 => 182,  461 => 44,  370 => 147,  395 => 109,  294 => 119,  223 => 65,  220 => 94,  492 => 395,  468 => 132,  444 => 148,  410 => 169,  397 => 135,  377 => 159,  262 => 107,  250 => 139,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 2108,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 1383,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 1303,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 1173,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 526,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 494,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 538,  1126 => 916,  1106 => 528,  1100 => 897,  1026 => 418,  997 => 822,  983 => 816,  975 => 402,  956 => 318,  939 => 786,  902 => 130,  894 => 364,  879 => 400,  757 => 288,  727 => 316,  716 => 308,  670 => 297,  528 => 232,  476 => 253,  435 => 33,  354 => 153,  341 => 129,  192 => 30,  321 => 154,  243 => 54,  793 => 350,  780 => 311,  758 => 335,  700 => 212,  686 => 231,  652 => 274,  638 => 188,  620 => 174,  545 => 259,  523 => 66,  494 => 55,  459 => 191,  438 => 195,  351 => 104,  347 => 16,  402 => 136,  268 => 111,  430 => 141,  411 => 167,  379 => 23,  322 => 108,  315 => 119,  289 => 113,  284 => 88,  255 => 105,  234 => 70,  1133 => 400,  1124 => 469,  1121 => 56,  1116 => 549,  1113 => 467,  1108 => 376,  1103 => 368,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 483,  1039 => 345,  1025 => 335,  1021 => 417,  1015 => 412,  1008 => 461,  996 => 406,  989 => 454,  985 => 315,  981 => 257,  977 => 321,  970 => 320,  966 => 437,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 148,  919 => 290,  917 => 289,  908 => 411,  905 => 296,  896 => 280,  891 => 360,  877 => 270,  862 => 267,  857 => 273,  837 => 347,  832 => 250,  827 => 322,  821 => 321,  803 => 179,  778 => 389,  769 => 233,  765 => 297,  753 => 328,  746 => 319,  743 => 318,  735 => 226,  730 => 330,  720 => 363,  717 => 362,  712 => 243,  691 => 233,  678 => 149,  654 => 199,  587 => 14,  576 => 158,  539 => 172,  517 => 140,  471 => 41,  441 => 239,  437 => 39,  418 => 201,  386 => 106,  373 => 137,  304 => 114,  270 => 80,  265 => 72,  229 => 75,  477 => 49,  455 => 36,  448 => 41,  429 => 143,  407 => 109,  399 => 142,  389 => 170,  375 => 128,  358 => 110,  349 => 131,  335 => 139,  327 => 155,  298 => 144,  280 => 88,  249 => 205,  194 => 84,  142 => 40,  344 => 92,  318 => 86,  306 => 104,  295 => 74,  357 => 154,  300 => 121,  286 => 90,  276 => 82,  269 => 103,  254 => 101,  128 => 47,  237 => 71,  165 => 64,  122 => 46,  798 => 337,  770 => 179,  759 => 112,  748 => 230,  731 => 260,  721 => 224,  718 => 313,  708 => 309,  696 => 147,  617 => 188,  590 => 226,  553 => 153,  550 => 156,  540 => 234,  533 => 255,  500 => 397,  493 => 225,  489 => 257,  482 => 223,  467 => 153,  464 => 202,  458 => 123,  452 => 217,  449 => 35,  415 => 32,  382 => 24,  372 => 150,  361 => 129,  356 => 105,  339 => 89,  302 => 150,  285 => 115,  258 => 136,  123 => 36,  108 => 42,  424 => 187,  394 => 139,  380 => 151,  338 => 112,  319 => 125,  316 => 123,  312 => 152,  290 => 118,  267 => 96,  206 => 60,  110 => 53,  240 => 86,  224 => 95,  219 => 63,  217 => 94,  202 => 59,  186 => 70,  170 => 55,  100 => 28,  67 => 18,  14 => 1,  1096 => 364,  1090 => 290,  1088 => 358,  1085 => 456,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 416,  1013 => 477,  1007 => 274,  1002 => 823,  993 => 266,  986 => 264,  982 => 401,  976 => 399,  971 => 260,  964 => 255,  949 => 388,  946 => 402,  940 => 384,  937 => 383,  928 => 452,  926 => 413,  915 => 299,  912 => 236,  903 => 231,  898 => 440,  892 => 229,  889 => 748,  887 => 281,  884 => 374,  876 => 222,  874 => 215,  871 => 368,  863 => 352,  861 => 274,  858 => 349,  850 => 378,  843 => 206,  840 => 406,  815 => 372,  812 => 240,  808 => 199,  804 => 395,  799 => 237,  791 => 236,  785 => 312,  775 => 184,  771 => 183,  754 => 340,  728 => 317,  726 => 225,  723 => 168,  715 => 105,  711 => 152,  709 => 222,  706 => 302,  698 => 208,  694 => 308,  692 => 155,  689 => 302,  681 => 300,  677 => 149,  675 => 289,  663 => 276,  661 => 200,  650 => 246,  646 => 112,  629 => 183,  627 => 266,  625 => 180,  622 => 270,  598 => 186,  592 => 261,  586 => 264,  575 => 174,  566 => 242,  556 => 73,  554 => 240,  541 => 163,  536 => 241,  515 => 209,  511 => 166,  509 => 60,  488 => 155,  486 => 220,  483 => 341,  465 => 147,  463 => 216,  450 => 116,  432 => 32,  419 => 173,  371 => 127,  362 => 111,  353 => 141,  337 => 137,  333 => 156,  309 => 118,  303 => 122,  299 => 96,  291 => 92,  272 => 109,  261 => 141,  253 => 91,  239 => 82,  235 => 70,  213 => 139,  200 => 52,  198 => 85,  159 => 71,  149 => 36,  146 => 67,  131 => 62,  116 => 99,  79 => 32,  74 => 28,  71 => 27,  836 => 262,  817 => 398,  814 => 319,  811 => 317,  805 => 313,  787 => 333,  779 => 169,  776 => 305,  773 => 347,  761 => 296,  751 => 163,  747 => 325,  742 => 336,  739 => 333,  736 => 317,  724 => 214,  705 => 215,  702 => 601,  688 => 232,  680 => 278,  667 => 296,  662 => 271,  656 => 418,  649 => 285,  644 => 284,  641 => 268,  624 => 109,  613 => 264,  607 => 273,  597 => 270,  591 => 163,  584 => 178,  579 => 159,  563 => 96,  559 => 154,  551 => 243,  547 => 149,  537 => 145,  524 => 141,  512 => 227,  507 => 165,  504 => 164,  498 => 213,  485 => 162,  480 => 50,  472 => 205,  466 => 38,  460 => 152,  447 => 143,  442 => 40,  434 => 212,  428 => 31,  422 => 176,  404 => 149,  368 => 136,  364 => 126,  340 => 170,  334 => 101,  330 => 148,  325 => 134,  292 => 94,  287 => 67,  282 => 104,  279 => 109,  273 => 81,  266 => 78,  256 => 76,  252 => 104,  228 => 80,  218 => 62,  201 => 74,  64 => 18,  51 => 10,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 1705,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 737,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 731,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 725,  1559 => 601,  1552 => 720,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 708,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 645,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 543,  1342 => 624,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 1001,  1257 => 505,  1254 => 479,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 498,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 492,  1196 => 958,  1192 => 490,  1188 => 456,  1184 => 485,  1179 => 454,  1176 => 453,  1172 => 938,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 561,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 468,  1115 => 424,  1111 => 377,  1107 => 422,  1102 => 898,  1099 => 393,  1095 => 419,  1091 => 418,  1082 => 455,  1079 => 524,  1076 => 359,  1070 => 875,  1057 => 491,  1052 => 504,  1045 => 484,  1040 => 377,  1036 => 283,  1032 => 496,  1028 => 337,  1023 => 393,  1020 => 392,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 821,  979 => 400,  974 => 256,  967 => 391,  962 => 803,  958 => 801,  954 => 389,  950 => 153,  945 => 387,  942 => 460,  938 => 150,  934 => 301,  927 => 147,  923 => 387,  920 => 412,  910 => 353,  901 => 347,  897 => 365,  890 => 343,  886 => 50,  883 => 401,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 340,  841 => 338,  835 => 312,  830 => 249,  826 => 202,  822 => 354,  818 => 246,  813 => 183,  810 => 317,  806 => 180,  802 => 198,  795 => 314,  792 => 239,  789 => 313,  784 => 188,  782 => 187,  777 => 291,  772 => 289,  768 => 334,  763 => 327,  760 => 329,  756 => 230,  752 => 176,  745 => 281,  741 => 227,  738 => 379,  732 => 171,  719 => 279,  714 => 264,  710 => 310,  704 => 267,  699 => 303,  695 => 234,  690 => 263,  687 => 210,  683 => 346,  679 => 298,  672 => 276,  668 => 201,  665 => 285,  658 => 244,  645 => 277,  640 => 197,  634 => 267,  628 => 178,  623 => 107,  619 => 298,  611 => 268,  606 => 263,  603 => 267,  599 => 262,  595 => 132,  583 => 263,  580 => 257,  573 => 157,  560 => 75,  543 => 147,  538 => 69,  534 => 233,  530 => 168,  526 => 229,  521 => 226,  518 => 233,  514 => 221,  510 => 227,  503 => 266,  496 => 226,  490 => 131,  484 => 394,  474 => 127,  470 => 231,  446 => 185,  440 => 146,  436 => 144,  431 => 37,  425 => 35,  416 => 30,  412 => 110,  408 => 141,  403 => 194,  400 => 225,  396 => 28,  392 => 139,  385 => 25,  381 => 133,  367 => 147,  363 => 18,  359 => 100,  355 => 132,  350 => 140,  346 => 130,  343 => 134,  328 => 135,  324 => 125,  313 => 105,  307 => 151,  301 => 124,  288 => 116,  283 => 111,  271 => 160,  257 => 148,  251 => 75,  238 => 71,  233 => 81,  195 => 121,  191 => 35,  187 => 53,  183 => 52,  130 => 49,  88 => 93,  76 => 31,  115 => 41,  95 => 28,  655 => 270,  651 => 232,  648 => 269,  637 => 273,  633 => 196,  621 => 462,  618 => 277,  615 => 268,  604 => 186,  600 => 271,  588 => 162,  585 => 161,  582 => 160,  571 => 242,  567 => 95,  555 => 239,  552 => 238,  549 => 237,  544 => 230,  542 => 290,  535 => 171,  531 => 143,  519 => 64,  516 => 63,  513 => 228,  508 => 230,  506 => 59,  499 => 241,  495 => 239,  491 => 54,  481 => 161,  478 => 235,  475 => 184,  469 => 196,  456 => 204,  451 => 149,  443 => 194,  439 => 119,  427 => 177,  423 => 141,  420 => 140,  409 => 137,  405 => 30,  401 => 164,  391 => 134,  387 => 132,  384 => 131,  378 => 154,  365 => 131,  360 => 17,  348 => 191,  336 => 132,  332 => 127,  329 => 109,  323 => 135,  310 => 124,  305 => 112,  277 => 170,  274 => 102,  263 => 97,  259 => 102,  247 => 138,  244 => 137,  241 => 73,  222 => 105,  210 => 122,  207 => 88,  204 => 87,  184 => 28,  181 => 52,  167 => 53,  157 => 44,  96 => 46,  421 => 111,  417 => 139,  414 => 138,  406 => 130,  398 => 165,  393 => 162,  390 => 153,  376 => 138,  369 => 19,  366 => 174,  352 => 140,  345 => 113,  342 => 160,  331 => 125,  326 => 87,  320 => 121,  317 => 100,  314 => 126,  311 => 85,  308 => 116,  297 => 112,  293 => 114,  281 => 146,  278 => 111,  275 => 113,  264 => 104,  260 => 107,  248 => 74,  245 => 73,  242 => 101,  231 => 52,  227 => 96,  215 => 88,  212 => 111,  209 => 125,  197 => 51,  177 => 118,  171 => 55,  161 => 68,  132 => 34,  121 => 46,  105 => 50,  99 => 46,  81 => 25,  77 => 19,  180 => 54,  176 => 45,  156 => 70,  143 => 50,  139 => 104,  118 => 33,  189 => 88,  185 => 46,  173 => 117,  166 => 73,  152 => 60,  174 => 66,  164 => 58,  154 => 113,  150 => 68,  137 => 49,  133 => 48,  127 => 102,  107 => 97,  102 => 34,  83 => 25,  78 => 23,  53 => 10,  23 => 6,  42 => 7,  138 => 52,  134 => 50,  109 => 31,  103 => 29,  97 => 27,  94 => 33,  84 => 9,  75 => 23,  69 => 16,  66 => 16,  54 => 9,  44 => 7,  230 => 74,  226 => 65,  203 => 86,  193 => 122,  188 => 57,  182 => 56,  178 => 49,  168 => 49,  163 => 115,  160 => 68,  155 => 55,  148 => 42,  145 => 41,  140 => 65,  136 => 39,  125 => 16,  120 => 38,  113 => 43,  101 => 33,  92 => 41,  89 => 27,  85 => 26,  73 => 18,  62 => 21,  59 => 15,  56 => 14,  41 => 5,  126 => 47,  119 => 65,  111 => 98,  106 => 30,  98 => 35,  93 => 42,  86 => 27,  70 => 13,  60 => 15,  28 => 5,  36 => 5,  114 => 54,  104 => 30,  91 => 26,  80 => 29,  63 => 30,  58 => 16,  40 => 7,  34 => 4,  45 => 7,  61 => 17,  55 => 11,  48 => 8,  39 => 5,  35 => 2,  31 => 2,  26 => 2,  21 => 2,  46 => 12,  29 => 3,  57 => 14,  50 => 9,  47 => 8,  38 => 3,  33 => 4,  49 => 11,  32 => 3,  246 => 102,  236 => 87,  232 => 129,  225 => 82,  221 => 63,  216 => 62,  214 => 90,  211 => 111,  208 => 67,  205 => 87,  199 => 58,  196 => 57,  190 => 54,  179 => 79,  175 => 76,  172 => 77,  169 => 41,  162 => 45,  158 => 54,  153 => 27,  151 => 43,  147 => 66,  144 => 51,  141 => 55,  135 => 35,  129 => 35,  124 => 60,  117 => 32,  112 => 31,  90 => 36,  87 => 21,  82 => 88,  72 => 19,  68 => 19,  65 => 17,  52 => 12,  43 => 6,  37 => 5,  30 => 6,  27 => 2,  25 => 65,  24 => 3,  22 => 34,  19 => 1,);
    }
}
