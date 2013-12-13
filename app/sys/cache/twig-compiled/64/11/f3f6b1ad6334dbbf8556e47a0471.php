<?php

/* AdminBundle:Departments:list.html.twig */
class __TwigTemplate_6411f3f6b1ad6334dbbf8556e47a0471 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("AdminBundle::layout.html.twig");

        $this->blocks = array(
            'html_head' => array($this, 'block_html_head'),
            'pagebar' => array($this, 'block_pagebar'),
            'content' => array($this, 'block_content'),
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
    public function block_html_head($context, array $blocks = array())
    {
        // line 4
        echo "<style type=\"text/css\">
";
        // line 5
        if (isset($context["type"])) { $_type_ = $context["type"]; } else { $_type_ = null; }
        if (($_type_ == "tickets")) {
            // line 6
            echo ".dp-grid article.dp-grid-row .col .r .inner-wrap2 {
\theight: 30px;
}
";
        }
        // line 10
        echo "</style>
<script type=\"text/javascript\">
\$(document).ready(function() {
\tvar selects = \$('select.change-linked-gateway');
\tDP.select(selects, {
\t\twidth: 256
\t});

\tselects.on('change', function(ev) {
\t\tvar sel = \$(this);
\t\tvar departmentId = \$(this).data('department-id');
\t\tvar gatewayId = \$(this).val();
\t\tvar me = this;

\t\tif (gatewayId == '-1') {
\t\t\twindow.location = '";
        // line 25
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_emailgateways_new"), "html", null, true);
        echo "?linked_department_id=' + departmentId;
\t\t\treturn;
\t\t}

\t\t// Change value of existing selects
\t\tif (gatewayId != '0') {
\t\t\tselects.each(function() {
\t\t\t\tif (this != me && \$(this).val() == gatewayId) {
\t\t\t\t\t\$(this).select2('val', '0');
\t\t\t\t\tDeskPRO_Window.util.showSavePuff(\$(this).prev());
\t\t\t\t}
\t\t\t});
\t\t}

\t\t\$.ajax({
\t\t\turl: BASE_URL + 'admin/departments/'+departmentId+'/save-gateway-account.json',
\t\t\tdata: {
\t\t\t\tgateway_account_id: gatewayId
\t\t\t},
\t\t\ttype: 'POST',
\t\t\tdataType: 'json',
\t\t\tsuccess: function(data) {
\t\t\t\tDeskPRO_Window.util.showSavePuff(sel.prev());
\t\t\t}
\t\t});
\t});
});
</script>
";
    }

    // line 54
    public function block_pagebar($context, array $blocks = array())
    {
        // line 55
        echo "\t<nav>
\t\t<ul>
\t\t\t<li class=\"add\"><a id=\"newdep_open\">";
        // line 57
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.departments.add_new_department");
        echo "</a></li>
\t\t</ul>
\t</nav>
\t<ul>
\t\t";
        // line 61
        if (isset($context["type"])) { $_type_ = $context["type"]; } else { $_type_ = null; }
        if (($_type_ == "tickets")) {
            // line 62
            echo "\t\t\t<li>Ticket Departments</li>
\t\t";
        } else {
            // line 64
            echo "\t\t\t<li>Chat Departments</li>
\t\t";
        }
        // line 66
        echo "\t</ul>
";
    }

    // line 68
    public function block_content($context, array $blocks = array())
    {
        // line 69
        echo "<div class=\"dp-page-box\">
<div class=\"page-content\" data-element-handler=\"DeskPRO.Admin.Departments.AjaxSave\" data-reorder-url=\"";
        // line 70
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_departments_updateorders"), "html", null, true);
        echo "\">
\t";
        // line 71
        if (isset($context["design"])) { $_design_ = $context["design"]; } else { $_design_ = null; }
        echo $_design_->gethelpbox();
        echo "
\t    <p>";
        // line 72
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.departments.notice_dept_default_grouping");
        echo "</p>
\t\t";
        // line 73
        if (isset($context["type"])) { $_type_ = $context["type"]; } else { $_type_ = null; }
        if (($_type_ == "tickets")) {
            // line 74
            echo "\t\t\t<p>
\t\t\t\tYou can customize the ticket form layout for each department using the <a href=\"";
            // line 75
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_tickets_editor"), "html", null, true);
            echo "\">Ticket Layout Editor &rarr;</a>
\t\t\t</p>
\t\t";
        }
        // line 78
        echo "\t";
        if (isset($context["design"])) { $_design_ = $context["design"]; } else { $_design_ = null; }
        echo $_design_->gethelpbox_end();
        echo "

\t";
        // line 80
        if (isset($context["design"])) { $_design_ = $context["design"]; } else { $_design_ = null; }
        echo $_design_->getdpgrid(array("class" => "dep-list"));
        echo "
\t\t";
        // line 81
        if (isset($context["design"])) { $_design_ = $context["design"]; } else { $_design_ = null; }
        echo $_design_->getdpgrid_headerrow();
        echo "
\t\t\t";
        // line 82
        if (isset($context["design"])) { $_design_ = $context["design"]; } else { $_design_ = null; }
        echo $_design_->getdpgrid_headercol(array("class" => "l r tl tr", "style" => "width: 480px;"));
        echo "
\t\t\t\t<h1>";
        // line 83
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.department");
        echo "</h1>
\t\t\t";
        // line 84
        if (isset($context["design"])) { $_design_ = $context["design"]; } else { $_design_ = null; }
        echo $_design_->getdpgrid_headercol_end();
        echo "
\t\t\t";
        // line 85
        if (isset($context["type"])) { $_type_ = $context["type"]; } else { $_type_ = null; }
        if (($_type_ == "tickets")) {
            // line 86
            echo "\t\t\t\t";
            if (isset($context["design"])) { $_design_ = $context["design"]; } else { $_design_ = null; }
            echo $_design_->getdpgrid_headercol(array("class" => "l r tl tr l-margin ", "style" => "width: 275px;"));
            echo "
\t\t\t\t\t<h1>Linked Email Account</h1>
\t\t\t\t";
            // line 88
            if (isset($context["design"])) { $_design_ = $context["design"]; } else { $_design_ = null; }
            echo $_design_->getdpgrid_headercol_end();
            echo "
\t\t\t";
        }
        // line 90
        echo "\t\t\t";
        if (isset($context["design"])) { $_design_ = $context["design"]; } else { $_design_ = null; }
        echo $_design_->getdpgrid_headercol(array("class" => "l r tl tr l-margin", "style" => "width: 125px"));
        echo "
\t\t\t\t<h1>Permissions</h1>
\t\t\t";
        // line 92
        if (isset($context["design"])) { $_design_ = $context["design"]; } else { $_design_ = null; }
        echo $_design_->getdpgrid_headercol_end();
        echo "
\t\t";
        // line 93
        if (isset($context["design"])) { $_design_ = $context["design"]; } else { $_design_ = null; }
        echo $_design_->getdpgrid_headerrow_end();
        echo "

\t\t";
        // line 95
        if (isset($context["all_departments"])) { $_all_departments_ = $context["all_departments"]; } else { $_all_departments_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_all_departments_);
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
        foreach ($context['_seq'] as $context["_key"] => $context["department"]) {
            // line 96
            echo "\t\t\t";
            $this->env->loadTemplate("AdminBundle:Departments:list-row.html.twig")->display($context);
            // line 97
            echo "\t\t";
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
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['department'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 98
        echo "\t";
        if (isset($context["design"])) { $_design_ = $context["design"]; } else { $_design_ = null; }
        echo $_design_->getdpgrid_end();
        echo "

<div id=\"editdep_overlay\" style=\"width: 400px; height: 330px; display: none;\">
\t<div class=\"overlay-title\">
\t\t<span class=\"close-overlay\"></span>
\t\t<h4>";
        // line 103
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.departments.edit_department_title");
        echo "</h4>
\t</div>
\t<div class=\"overlay-content\">
\t\t<form class=\"dp-form\" method=\"POST\" action=\"";
        // line 106
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_departments_savetitle"), "html", null, true);
        echo "\">
\t\t\t<div class=\"dp-form-row\">
\t\t\t\t<div class=\"dp-form-label\">
\t\t\t\t\t<label>";
        // line 109
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.title");
        echo "</label>
\t\t\t\t</div>
\t\t\t\t<div class=\"dp-form-input\">
\t\t\t\t\t<input type=\"hidden\" name=\"department_id\" id=\"editdep_depid\" value=\"\" />
\t\t\t\t\t<input type=\"text\" name=\"title\" id=\"editdep_title\" value=\"\" />
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<div class=\"dp-form-row\">
\t\t\t\t<div class=\"dp-form-label\">
\t\t\t\t\t<label>
\t\t\t\t\t\t";
        // line 119
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.user_title");
        echo "
\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"";
        // line 120
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.user_title_explain");
        echo "\"></span>
\t\t\t\t\t</label>
\t\t\t\t</div>
\t\t\t\t<div class=\"dp-form-input\">
\t\t\t\t\t<input type=\"text\" name=\"user_title\" id=\"editdep_user_title\" value=\"\" />
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<div class=\"dp-form-row\" id=\"editcat_parent_row\">
\t\t\t\t<div class=\"dp-form-label\">
\t\t\t\t\t<label>Parent</label>
\t\t\t\t</div>
\t\t\t\t<div class=\"dp-form-input\">
\t\t\t\t\t<select name=\"parent_id\" id=\"editcat_parent_id\">
\t\t\t\t\t\t<option value=\"0\">";
        // line 133
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.no_parent");
        echo "</option>
\t\t\t\t\t\t";
        // line 134
        if (isset($context["all_departments"])) { $_all_departments_ = $context["all_departments"]; } else { $_all_departments_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_all_departments_);
        foreach ($context['_seq'] as $context["_key"] => $context["department"]) {
            // line 135
            echo "\t\t\t\t\t\t\t<option value=\"";
            if (isset($context["department"])) { $_department_ = $context["department"]; } else { $_department_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_department_, "id"), "html", null, true);
            echo "\">";
            if (isset($context["department"])) { $_department_ = $context["department"]; } else { $_department_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_department_, "title"), "html", null, true);
            echo "</option>
\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['department'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 137
        echo "\t\t\t\t\t</select>
\t\t\t\t</div>
\t\t\t</div>
\t\t</form>
\t</div>
\t<div class=\"overlay-footer\">
\t\t<button class=\"clean-white\" id=\"editdep_savebtn\">";
        // line 143
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.save");
        echo "</button>
\t</div>
</div>

<div id=\"newdep_overlay\" style=\"width: 400px; height: 330px; display: none;\">
\t<div class=\"overlay-title\">
\t\t<span class=\"close-overlay\"></span>
\t\t<h4>";
        // line 150
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.departments.new_department");
        echo "</h4>
\t</div>
\t<div class=\"overlay-content\">
\t\t<form class=\"dp-form\" method=\"POST\" action=\"";
        // line 153
        if (isset($context["type"])) { $_type_ = $context["type"]; } else { $_type_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_departments_savenew", array("type" => $_type_)), "html", null, true);
        echo "\">
\t\t\t<div class=\"dp-form-row\">
\t\t\t\t<div class=\"dp-form-label\">
\t\t\t\t\t<label>";
        // line 156
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.title");
        echo "</label>
\t\t\t\t</div>
\t\t\t\t<div class=\"dp-form-input\">
\t\t\t\t\t<input type=\"text\" name=\"title\" id=\"newdep_title\" value=\"\" />
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<div class=\"dp-form-row\">
\t\t\t\t<div class=\"dp-form-label\">
\t\t\t\t\t<label>
\t\t\t\t\t\t";
        // line 165
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.user_title");
        echo "
\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"";
        // line 166
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.user_title_explain");
        echo "\"></span>
\t\t\t\t\t</label>
\t\t\t\t</div>
\t\t\t\t<div class=\"dp-form-input\">
\t\t\t\t\t<input type=\"text\" name=\"user_title\" id=\"newdep_user_title\" value=\"\" />
\t\t\t\t</div>
\t\t\t</div>

\t\t\t<div class=\"dp-form-row\">
\t\t\t\t<div class=\"dp-form-label\">
\t\t\t\t\t<label>";
        // line 176
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.parent");
        echo "</label>
\t\t\t\t</div>
\t\t\t\t<div class=\"dp-form-input\">
\t\t\t\t\t<select name=\"parent_id\" id=\"newdep_parent_id\">
\t\t\t\t\t\t<option value=\"0\">";
        // line 180
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.no_parent");
        echo "</option>
\t\t\t\t\t\t";
        // line 181
        if (isset($context["all_departments"])) { $_all_departments_ = $context["all_departments"]; } else { $_all_departments_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_all_departments_);
        foreach ($context['_seq'] as $context["_key"] => $context["department"]) {
            // line 182
            echo "\t\t\t\t\t\t\t<option value=\"";
            if (isset($context["department"])) { $_department_ = $context["department"]; } else { $_department_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_department_, "id"), "html", null, true);
            echo "\">";
            if (isset($context["department"])) { $_department_ = $context["department"]; } else { $_department_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_department_, "title"), "html", null, true);
            echo "</option>
\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['department'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 184
        echo "\t\t\t\t\t</select>
\t\t\t\t</div>
\t\t\t</div>
\t\t</form>
\t</div>
\t<div class=\"overlay-footer\">
\t\t<button class=\"clean-white\" id=\"newdep_savebtn\">";
        // line 190
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.add");
        echo "</button>
\t</div>
</div>

</div>

<div id=\"dep_not_disabled_overlay\" style=\"width: 518px; height: 180px; display: none;\">
\t<div class=\"overlay-title\">
\t\t<span class=\"close-overlay\"></span>
\t\t<h4>Department was not disabled</h4>
\t</div>
\t<div class=\"overlay-content\">
\t\t<p>
\t\t\tThe department was not disabled. DeskPRO always requires at least one department.
\t\t</p>
\t\t<br />
\t\t<p>
\t\t\tNote that when there is only a single department, the user will not see the department field.
\t\t</p>
\t</div>
\t<div class=\"overlay-footer\">
\t\t<button class=\"clean-white overlay-close-trigger\">Okay</button>
\t</div>
</div>

";
        // line 215
        if (isset($context["type"])) { $_type_ = $context["type"]; } else { $_type_ = null; }
        if (($_type_ == "tickets")) {
            // line 216
            echo "<div style=\"padding: 0 10px 10px 15px;\">
\t<form action=\"";
            // line 217
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_departments_setdefault", array("type" => "tickets")), "html", null, true);
            echo "\" method=\"POST\">
\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"This is the value selected by default on new tickets. Note that a department is always required. If you leave this selection blank, then in cases whre a default department is required (such as a new ticket via email), the default is simply the first defined department.\"></span> Default department:
\t\t<select name=\"default_value\">
\t\t\t<option value=\"\"></option>
\t\t\t";
            // line 221
            if (isset($context["all_departments"])) { $_all_departments_ = $context["all_departments"]; } else { $_all_departments_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_all_departments_);
            foreach ($context['_seq'] as $context["_key"] => $context["dep"]) {
                // line 222
                echo "\t\t\t\t";
                if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                if (twig_length_filter($this->env, $this->getAttribute($_dep_, "children"))) {
                    // line 223
                    echo "\t\t\t\t\t<optgroup label=\"";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                    echo "\">
\t\t\t\t\t\t";
                    // line 224
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($_dep_, "children"));
                    foreach ($context['_seq'] as $context["_key"] => $context["subdep"]) {
                        // line 225
                        echo "\t\t\t\t\t\t\t<option value=\"";
                        if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_subdep_, "id"), "html", null, true);
                        echo "\" ";
                        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                        if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                        if (($this->getAttribute($_app_, "getSetting", array(0 => "core.default_ticket_dep"), "method") == $this->getAttribute($_subdep_, "id"))) {
                            echo "selected=\"selected\"";
                        }
                        echo ">";
                        if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_subdep_, "title"), "html", null, true);
                        echo "</option>
\t\t\t\t\t\t";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subdep'], $context['_parent'], $context['loop']);
                    $context = array_merge($_parent, array_intersect_key($context, $_parent));
                    // line 227
                    echo "\t\t\t\t\t</optgroup>
\t\t\t\t";
                } else {
                    // line 229
                    echo "\t\t\t\t\t<option value=\"";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "id"), "html", null, true);
                    echo "\" ";
                    if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    if (($this->getAttribute($_app_, "getSetting", array(0 => "core.default_ticket_dep"), "method") == $this->getAttribute($_dep_, "id"))) {
                        echo "selected=\"selected\"";
                    }
                    echo ">";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                    echo "</option>
\t\t\t\t";
                }
                // line 231
                echo "\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['dep'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 232
            echo "\t\t</select>
\t\t<button class=\"clean-white\">Update</button>
\t</form>
</div>
";
        }
        // line 237
        echo "
";
        // line 238
        if (isset($context["type"])) { $_type_ = $context["type"]; } else { $_type_ = null; }
        if (($_type_ == "tickets")) {
            // line 239
            echo "<div style=\"padding: 0 10px 10px 15px;\">
\t<form action=\"";
            // line 240
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_departments_setphrase"), "html", null, true);
            echo "\" method=\"POST\">
\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Enter a new word for 'Department' that users will see. For example, 'Category' or 'Region' or 'Type.'\"></span> Change the name of the department field:<br />
\t\t<div style=\"padding-left: 11px;\">
\t\t\t<table cellspacing=\"0\" cellpadding=\"3\" border=\"0\">
\t\t\t\t<tr>
\t\t\t\t\t<td style=\"vertical-align:middle;\">Singular:</td><td><input style=\"padding: 1px 2px;\" type=\"text\" name=\"phrase_singular\" value=\"";
            // line 245
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($_app_, "getSetting", array(0 => "core.phrase_department_singular"), "method", true, true)) ? (_twig_default_filter($this->getAttribute($_app_, "getSetting", array(0 => "core.phrase_department_singular"), "method"), "Department")) : ("Department")), "html", null, true);
            echo "\" /></td>
\t\t\t\t</tr>
\t\t\t\t<tr>
\t\t\t\t\t<td style=\"vertical-align:middle;\">Plural:</td><td><input style=\"padding: 1px 2px;\" type=\"text\" name=\"phrase_plural\" value=\"";
            // line 248
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($_app_, "getSetting", array(0 => "core.phrase_department_plural"), "method", true, true)) ? (_twig_default_filter($this->getAttribute($_app_, "getSetting", array(0 => "core.phrase_department_plural"), "method"), "Departments")) : ("Departments")), "html", null, true);
            echo "\" /><br /></td>
\t\t\t\t</tr>
\t\t\t\t<tr>
\t\t\t\t\t<td></td>
\t\t\t\t\t<td><button class=\"clean-white\">Update</button></td>
\t\t\t\t</tr>
\t\t\t</table>
\t\t</div>
\t</form>
</div>
";
        }
        // line 259
        echo "
</div>
";
    }

    public function getTemplateName()
    {
        return "AdminBundle:Departments:list.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  562 => 245,  548 => 238,  558 => 94,  479 => 82,  589 => 101,  457 => 133,  413 => 133,  953 => 406,  948 => 403,  935 => 394,  929 => 391,  916 => 382,  864 => 365,  844 => 348,  816 => 342,  807 => 339,  801 => 338,  774 => 331,  766 => 328,  737 => 314,  685 => 300,  664 => 294,  635 => 288,  593 => 269,  546 => 91,  532 => 231,  865 => 221,  852 => 216,  838 => 208,  820 => 201,  781 => 333,  764 => 178,  725 => 164,  632 => 141,  602 => 105,  565 => 117,  529 => 111,  505 => 207,  487 => 104,  473 => 221,  1853 => 842,  1849 => 841,  1839 => 837,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 813,  1778 => 812,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 782,  1705 => 781,  1688 => 770,  1681 => 769,  1677 => 768,  1667 => 764,  1656 => 762,  1648 => 757,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 714,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 695,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 651,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 610,  1289 => 607,  1276 => 604,  1263 => 601,  1244 => 596,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 565,  1181 => 561,  1171 => 557,  1158 => 550,  1114 => 530,  1104 => 526,  1072 => 512,  1062 => 508,  1024 => 494,  1014 => 490,  1000 => 482,  990 => 478,  980 => 474,  960 => 466,  918 => 448,  888 => 376,  834 => 346,  673 => 342,  636 => 323,  462 => 209,  454 => 76,  1144 => 542,  1139 => 399,  1131 => 399,  1127 => 397,  1110 => 396,  1092 => 391,  1089 => 390,  1086 => 520,  1084 => 388,  1063 => 387,  1060 => 386,  1055 => 385,  1050 => 384,  1035 => 372,  1019 => 267,  1003 => 263,  959 => 251,  900 => 377,  880 => 434,  870 => 430,  867 => 366,  859 => 362,  848 => 215,  839 => 197,  828 => 191,  823 => 188,  809 => 181,  800 => 178,  797 => 177,  794 => 176,  786 => 174,  740 => 162,  734 => 313,  703 => 354,  693 => 350,  630 => 412,  626 => 140,  614 => 138,  610 => 385,  581 => 124,  564 => 229,  525 => 236,  722 => 162,  697 => 256,  674 => 249,  671 => 425,  577 => 261,  569 => 248,  557 => 368,  502 => 267,  497 => 194,  445 => 95,  729 => 159,  684 => 261,  676 => 297,  669 => 254,  660 => 145,  647 => 243,  643 => 244,  601 => 175,  570 => 211,  522 => 200,  501 => 148,  296 => 104,  374 => 119,  631 => 111,  616 => 281,  608 => 137,  605 => 16,  596 => 15,  574 => 165,  561 => 209,  527 => 147,  433 => 93,  388 => 110,  426 => 177,  383 => 62,  461 => 18,  370 => 113,  395 => 176,  294 => 76,  223 => 95,  220 => 120,  492 => 263,  468 => 21,  444 => 131,  410 => 229,  397 => 117,  377 => 120,  262 => 115,  250 => 147,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 2108,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 1383,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 1303,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 1173,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 1041,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 970,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 538,  1126 => 916,  1106 => 900,  1100 => 897,  1026 => 841,  997 => 822,  983 => 816,  975 => 402,  956 => 800,  939 => 786,  902 => 753,  894 => 749,  879 => 373,  757 => 631,  727 => 608,  716 => 605,  670 => 296,  528 => 357,  476 => 140,  435 => 195,  354 => 110,  341 => 278,  192 => 86,  321 => 163,  243 => 87,  793 => 351,  780 => 348,  758 => 177,  700 => 154,  686 => 150,  652 => 274,  638 => 414,  620 => 139,  545 => 237,  523 => 110,  494 => 205,  459 => 99,  438 => 172,  351 => 79,  347 => 104,  402 => 180,  268 => 103,  430 => 237,  411 => 182,  379 => 84,  322 => 135,  315 => 110,  289 => 67,  284 => 128,  255 => 24,  234 => 136,  1133 => 400,  1124 => 534,  1121 => 56,  1116 => 55,  1113 => 403,  1108 => 51,  1103 => 394,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 500,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 329,  996 => 262,  989 => 316,  985 => 315,  981 => 257,  977 => 313,  970 => 91,  966 => 416,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 244,  919 => 290,  917 => 289,  908 => 444,  905 => 378,  896 => 280,  891 => 227,  877 => 270,  862 => 267,  857 => 265,  837 => 347,  832 => 250,  827 => 248,  821 => 247,  803 => 179,  778 => 389,  769 => 165,  765 => 386,  753 => 325,  746 => 175,  743 => 318,  735 => 170,  730 => 218,  720 => 363,  717 => 362,  712 => 156,  691 => 204,  678 => 149,  654 => 144,  587 => 14,  576 => 179,  539 => 241,  517 => 109,  471 => 190,  441 => 199,  437 => 15,  418 => 7,  386 => 2,  373 => 83,  304 => 151,  270 => 123,  265 => 63,  229 => 91,  477 => 138,  455 => 325,  448 => 164,  429 => 159,  407 => 119,  399 => 156,  389 => 174,  375 => 167,  358 => 286,  349 => 118,  335 => 137,  327 => 54,  298 => 50,  280 => 109,  249 => 38,  194 => 80,  142 => 72,  344 => 83,  318 => 135,  306 => 87,  295 => 68,  357 => 119,  300 => 106,  286 => 80,  276 => 87,  269 => 66,  254 => 120,  128 => 66,  237 => 138,  165 => 80,  122 => 66,  798 => 337,  770 => 179,  759 => 112,  748 => 337,  731 => 108,  721 => 308,  718 => 307,  708 => 271,  696 => 147,  617 => 107,  590 => 226,  553 => 225,  550 => 156,  540 => 84,  533 => 82,  500 => 206,  493 => 225,  489 => 343,  482 => 223,  467 => 210,  464 => 120,  458 => 166,  452 => 117,  449 => 132,  415 => 6,  382 => 166,  372 => 215,  361 => 81,  356 => 58,  339 => 102,  302 => 131,  285 => 101,  258 => 98,  123 => 32,  108 => 45,  424 => 184,  394 => 86,  380 => 121,  338 => 155,  319 => 72,  316 => 53,  312 => 87,  290 => 146,  267 => 96,  206 => 54,  110 => 47,  240 => 37,  224 => 60,  219 => 94,  217 => 93,  202 => 106,  186 => 57,  170 => 81,  100 => 55,  67 => 14,  14 => 1,  1096 => 524,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 836,  1013 => 275,  1007 => 274,  1002 => 823,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 402,  940 => 298,  937 => 395,  928 => 452,  926 => 243,  915 => 239,  912 => 236,  903 => 231,  898 => 440,  892 => 229,  889 => 748,  887 => 227,  884 => 374,  876 => 222,  874 => 215,  871 => 368,  863 => 214,  861 => 213,  858 => 212,  850 => 352,  843 => 206,  840 => 406,  815 => 245,  812 => 244,  808 => 199,  804 => 395,  799 => 196,  791 => 193,  785 => 238,  775 => 184,  771 => 183,  754 => 340,  728 => 309,  726 => 169,  723 => 168,  715 => 105,  711 => 152,  709 => 162,  706 => 155,  698 => 208,  694 => 153,  692 => 155,  689 => 254,  681 => 150,  677 => 149,  675 => 148,  663 => 251,  661 => 277,  650 => 246,  646 => 143,  629 => 129,  627 => 21,  625 => 236,  622 => 126,  598 => 174,  592 => 117,  586 => 170,  575 => 214,  566 => 163,  556 => 157,  554 => 240,  541 => 222,  536 => 205,  515 => 209,  511 => 108,  509 => 34,  488 => 224,  486 => 342,  483 => 341,  465 => 20,  463 => 216,  450 => 202,  432 => 190,  419 => 155,  371 => 165,  362 => 100,  353 => 150,  337 => 18,  333 => 122,  309 => 94,  303 => 86,  299 => 130,  291 => 111,  272 => 82,  261 => 156,  253 => 89,  239 => 91,  235 => 89,  213 => 91,  200 => 91,  198 => 96,  159 => 69,  149 => 74,  146 => 73,  131 => 47,  116 => 30,  79 => 33,  74 => 52,  71 => 25,  836 => 320,  817 => 398,  814 => 200,  811 => 317,  805 => 313,  787 => 657,  779 => 169,  776 => 305,  773 => 347,  761 => 296,  751 => 163,  747 => 293,  742 => 336,  739 => 171,  736 => 287,  724 => 214,  705 => 278,  702 => 601,  688 => 113,  680 => 263,  667 => 295,  662 => 146,  656 => 418,  649 => 291,  644 => 97,  641 => 241,  624 => 109,  613 => 106,  607 => 232,  597 => 221,  591 => 131,  584 => 259,  579 => 234,  563 => 162,  559 => 116,  551 => 239,  547 => 114,  537 => 90,  524 => 191,  512 => 227,  507 => 76,  504 => 31,  498 => 142,  485 => 29,  480 => 28,  472 => 139,  466 => 217,  460 => 215,  447 => 201,  442 => 16,  434 => 110,  428 => 11,  422 => 9,  404 => 66,  368 => 164,  364 => 127,  340 => 189,  334 => 115,  330 => 97,  325 => 112,  292 => 83,  287 => 49,  282 => 119,  279 => 78,  273 => 44,  266 => 106,  256 => 95,  252 => 94,  228 => 113,  218 => 32,  201 => 38,  64 => 25,  51 => 8,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 1705,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 737,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 731,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 725,  1559 => 601,  1552 => 720,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 708,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 645,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 624,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 1001,  1257 => 1000,  1254 => 479,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 569,  1196 => 958,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 938,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 388,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 898,  1099 => 393,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 880,  1076 => 359,  1070 => 875,  1057 => 352,  1052 => 504,  1045 => 382,  1040 => 377,  1036 => 283,  1032 => 496,  1028 => 274,  1023 => 393,  1020 => 392,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 821,  979 => 379,  974 => 256,  967 => 373,  962 => 803,  958 => 801,  954 => 253,  950 => 368,  945 => 367,  942 => 460,  938 => 365,  934 => 364,  927 => 361,  923 => 387,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 272,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 410,  841 => 210,  835 => 312,  830 => 249,  826 => 202,  822 => 308,  818 => 246,  813 => 183,  810 => 340,  806 => 180,  802 => 198,  795 => 336,  792 => 239,  789 => 350,  784 => 188,  782 => 187,  777 => 291,  772 => 289,  768 => 345,  763 => 327,  760 => 326,  756 => 230,  752 => 176,  745 => 281,  741 => 280,  738 => 379,  732 => 171,  719 => 279,  714 => 264,  710 => 310,  704 => 267,  699 => 303,  695 => 264,  690 => 263,  687 => 203,  683 => 346,  679 => 298,  672 => 255,  668 => 247,  665 => 201,  658 => 244,  645 => 270,  640 => 142,  634 => 413,  628 => 286,  623 => 238,  619 => 282,  611 => 18,  606 => 279,  603 => 176,  599 => 242,  595 => 132,  583 => 169,  580 => 100,  573 => 260,  560 => 101,  543 => 172,  538 => 232,  534 => 281,  530 => 202,  526 => 213,  521 => 146,  518 => 235,  514 => 183,  510 => 202,  503 => 75,  496 => 345,  490 => 83,  484 => 143,  474 => 25,  470 => 168,  446 => 318,  440 => 114,  436 => 113,  431 => 12,  425 => 193,  416 => 104,  412 => 98,  408 => 185,  403 => 126,  400 => 225,  396 => 299,  392 => 152,  385 => 117,  381 => 170,  367 => 82,  363 => 139,  359 => 153,  355 => 285,  350 => 94,  346 => 156,  343 => 143,  328 => 17,  324 => 164,  313 => 133,  307 => 108,  301 => 69,  288 => 102,  283 => 66,  271 => 97,  257 => 76,  251 => 76,  238 => 92,  233 => 100,  195 => 37,  191 => 67,  187 => 46,  183 => 44,  130 => 69,  88 => 24,  76 => 27,  115 => 49,  95 => 59,  655 => 275,  651 => 232,  648 => 231,  637 => 241,  633 => 130,  621 => 408,  618 => 233,  615 => 232,  604 => 186,  600 => 516,  588 => 305,  585 => 204,  582 => 203,  571 => 118,  567 => 95,  555 => 207,  552 => 190,  549 => 208,  544 => 285,  542 => 207,  535 => 112,  531 => 358,  519 => 87,  516 => 229,  513 => 168,  508 => 86,  506 => 83,  499 => 106,  495 => 147,  491 => 145,  481 => 103,  478 => 222,  475 => 184,  469 => 182,  456 => 136,  451 => 96,  443 => 132,  439 => 129,  427 => 125,  423 => 109,  420 => 192,  409 => 89,  405 => 148,  401 => 3,  391 => 86,  387 => 129,  384 => 132,  378 => 165,  365 => 289,  360 => 158,  348 => 21,  336 => 92,  332 => 107,  329 => 152,  323 => 96,  310 => 109,  305 => 165,  277 => 99,  274 => 106,  263 => 147,  259 => 62,  247 => 88,  244 => 97,  241 => 96,  222 => 90,  210 => 85,  207 => 80,  204 => 49,  184 => 84,  181 => 88,  167 => 41,  157 => 39,  96 => 24,  421 => 138,  417 => 71,  414 => 152,  406 => 181,  398 => 129,  393 => 175,  390 => 221,  376 => 110,  369 => 94,  366 => 156,  352 => 106,  345 => 57,  342 => 109,  331 => 114,  326 => 102,  320 => 111,  317 => 134,  314 => 147,  311 => 78,  308 => 144,  297 => 120,  293 => 119,  281 => 107,  278 => 125,  275 => 34,  264 => 43,  260 => 73,  248 => 144,  245 => 104,  242 => 118,  231 => 57,  227 => 58,  215 => 84,  212 => 92,  209 => 82,  197 => 48,  177 => 43,  171 => 83,  161 => 77,  132 => 51,  121 => 28,  105 => 61,  99 => 28,  81 => 21,  77 => 53,  180 => 83,  176 => 33,  156 => 38,  143 => 56,  139 => 68,  118 => 64,  189 => 85,  185 => 78,  173 => 64,  166 => 70,  152 => 75,  174 => 83,  164 => 39,  154 => 70,  150 => 44,  137 => 71,  133 => 70,  127 => 68,  107 => 50,  102 => 23,  83 => 22,  78 => 28,  53 => 11,  23 => 1,  42 => 6,  138 => 69,  134 => 67,  109 => 32,  103 => 26,  97 => 54,  94 => 34,  84 => 22,  75 => 32,  69 => 16,  66 => 50,  54 => 14,  44 => 7,  230 => 60,  226 => 87,  203 => 128,  193 => 49,  188 => 80,  182 => 77,  178 => 43,  168 => 40,  163 => 79,  160 => 45,  155 => 72,  148 => 71,  145 => 70,  140 => 33,  136 => 48,  125 => 65,  120 => 10,  113 => 32,  101 => 41,  92 => 22,  89 => 25,  85 => 2,  73 => 15,  62 => 13,  59 => 15,  56 => 24,  41 => 6,  126 => 48,  119 => 63,  111 => 61,  106 => 27,  98 => 44,  93 => 29,  86 => 35,  70 => 15,  60 => 13,  28 => 3,  36 => 5,  114 => 62,  104 => 57,  91 => 43,  80 => 26,  63 => 14,  58 => 47,  40 => 4,  34 => 41,  45 => 7,  61 => 13,  55 => 11,  48 => 7,  39 => 7,  35 => 4,  31 => 40,  26 => 2,  21 => 36,  46 => 44,  29 => 3,  57 => 10,  50 => 45,  47 => 10,  38 => 5,  33 => 4,  49 => 11,  32 => 3,  246 => 93,  236 => 86,  232 => 135,  225 => 59,  221 => 56,  216 => 71,  214 => 82,  211 => 60,  208 => 88,  205 => 90,  199 => 88,  196 => 82,  190 => 79,  179 => 76,  175 => 82,  172 => 41,  169 => 45,  162 => 40,  158 => 78,  153 => 36,  151 => 56,  147 => 100,  144 => 37,  141 => 49,  135 => 36,  129 => 11,  124 => 56,  117 => 39,  112 => 29,  90 => 58,  87 => 34,  82 => 54,  72 => 18,  68 => 16,  65 => 14,  52 => 10,  43 => 6,  37 => 5,  30 => 3,  27 => 2,  25 => 2,  24 => 37,  22 => 16,  19 => 1,);
    }
}
