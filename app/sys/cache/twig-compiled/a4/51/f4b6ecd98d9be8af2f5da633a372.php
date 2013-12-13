<?php

/* AdminBundle:TicketProperties:editor.html.twig */
class __TwigTemplate_a451f4b6ecd98d9be8af2f5da633a372 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("AdminBundle::layout.html.twig");

        $this->blocks = array(
            'html_head' => array($this, 'block_html_head'),
            'page_nav_inner' => array($this, 'block_page_nav_inner'),
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
        // line 1
        $context["this_page"] = "ticket_editor";
        // line 3
        $context["design"] = $this->env->loadTemplate("AdminBundle:Common:design-macros.html.twig");
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 4
    public function block_html_head($context, array $blocks = array())
    {
        // line 5
        echo "<script type=\"text/javascript\" src=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("javascripts/DeskPRO/Admin/ElementHandler/TicketEditor.js"), "html", null, true);
        echo "\"></script>
<script type=\"text/javascript\">
var TICKET_DISPLAY_DATA = ";
        // line 7
        if (isset($context["page_data"])) { $_page_data_ = $context["page_data"]; } else { $_page_data_ = null; }
        echo twig_jsonencode_filter($_page_data_);
        echo ";
</script>
\t<style type=\"text/css\">
\t\t.field-agent-only {
\t\t\tmargin-left: 10px;
\t\t\tbackground: #DBE3E8;
\t\t\tline-height: 100%;
\t\t\tpadding: 1px 4px;
\t\t\tborder-radius: 4px;
\t\t\t-webkit-border-radius: 4px;
\t\t\tborder: 1px solid #B2B9C1;
\t\t}

\t\t.orange-pip {
\t\t\tbackground: url(";
        // line 21
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("images/agent/icons/bullet_orange.png"), "html", null, true);
        echo ") no-repeat 100% 50%;
\t\t\tpadding-right: 20px;
\t\t}

\t\t.content-tabs ul a {
\t\t\tcolor: #000;
\t\t\ttext-decoration: none;
\t\t}
\t</style>
";
    }

    // line 31
    public function block_page_nav_inner($context, array $blocks = array())
    {
        // line 32
        echo "<div class=\"page-nav-block\" ";
        if (isset($context["is_default"])) { $_is_default_ = $context["is_default"]; } else { $_is_default_ = null; }
        if ($_is_default_) {
            echo "style=\"display: none\"";
        }
        echo ">
\t<div class=\"inner-shadow\"></div>
\t<section class=\"top\">
\t\t<header>
\t\t\t<h4>";
        // line 36
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.ticket_elements");
        echo "</h4>
\t\t</header>
\t\t<article id=\"ticket_elements\">
\t\t\t<ul>
\t\t\t\t<li class=\"draggable\" data-item-id=\"ticket_department\" data-edit-title=\"";
        // line 40
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.edit_departments");
        echo "\" data-edit-url=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_departments"), "html", null, true);
        echo "\"><label>";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.department");
        echo "</label></li>
\t\t\t\t";
        // line 41
        if (isset($context["section"])) { $_section_ = $context["section"]; } else { $_section_ = null; }
        if (($_section_ == "create")) {
            // line 42
            echo "\t\t\t\t\t<li class=\"draggable irremovable\" data-item-id=\"message\"><label>";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.ticket_message");
            echo "</label></li>
\t\t\t\t\t<li class=\"draggable irremovable\" data-item-id=\"ticket_subject\"><label>";
            // line 43
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.ticket_subject");
            echo "</label></li>
\t\t\t\t\t<li class=\"draggable\" data-item-id=\"attachments\"><label>";
            // line 44
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.attachments");
            echo "</label></li>
\t\t\t\t";
        }
        // line 46
        echo "\t\t\t\t";
        if (isset($context["section"])) { $_section_ = $context["section"]; } else { $_section_ = null; }
        if ((($_section_ == "create") || ($_section_ == "modify"))) {
            // line 47
            echo "\t\t\t\t\t<li class=\"draggable\" data-item-id=\"ticket_cc_emails\"><label>CC Emails</label></li>
\t\t\t\t";
        }
        // line 49
        echo "\t\t\t\t";
        if (isset($context["section"])) { $_section_ = $context["section"]; } else { $_section_ = null; }
        if (($_section_ == "modify")) {
            // line 50
            echo "\t\t\t\t\t<li class=\"draggable\" data-item-id=\"ticket_remove_ccs\"><label>Remove CCs</label></li>
\t\t\t\t";
        }
        // line 52
        echo "\t\t\t\t";
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        if ($this->getAttribute($_term_options_, "ticket_categories_full")) {
            echo "<li class=\"draggable\" data-item-id=\"ticket_category\" data-edit-title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.edit_categories");
            echo "\" data-edit-url=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_ticketcats"), "html", null, true);
            echo "\"><label>";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.categories");
            echo "</label></li>";
        }
        // line 53
        echo "\t\t\t\t";
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        if ($this->getAttribute($_term_options_, "ticket_workflows")) {
            echo "<li class=\"draggable\" data-item-id=\"ticket_workflow\" data-edit-title=\"Edit Workflows\" data-edit-url=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_ticketworks"), "html", null, true);
            echo "\"><label>Workflows</label></li>";
        }
        // line 54
        echo "\t\t\t\t";
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        if ($this->getAttribute($_term_options_, "products")) {
            echo "<li class=\"draggable\" data-item-id=\"ticket_product\" data-edit-title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.edit_products");
            echo "\" data-edit-url=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_products"), "html", null, true);
            echo "\"><label>";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.product");
            echo "</label></li>";
        }
        // line 55
        echo "\t\t\t\t";
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        if ($this->getAttribute($_term_options_, "priorities")) {
            echo "<li class=\"draggable\" data-item-id=\"ticket_priority\" data-edit-title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.edit_priorities");
            echo "\" data-edit-url=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_ticketpris"), "html", null, true);
            echo "\"><label>";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.priority");
            echo "</label></li>";
        }
        // line 56
        echo "\t\t\t\t";
        if (isset($context["custom_ticket_fields"])) { $_custom_ticket_fields_ = $context["custom_ticket_fields"]; } else { $_custom_ticket_fields_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_custom_ticket_fields_);
        foreach ($context['_seq'] as $context["_key"] => $context["field"]) {
            // line 57
            echo "\t\t\t\t<li class=\"draggable\" ";
            if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
            if ($this->getAttribute($this->getAttribute($_field_, "field_def"), "is_agent_field")) {
                echo "data-is-agent-field=\"1\"";
            }
            echo " data-item-id=\"ticket_field[";
            if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_field_, "field_def"), "id"), "html", null, true);
            echo "]\" data-edit-title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.edit_field");
            echo "\" data-edit-url=\"";
            if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_customdeftickets_edit", array("field_id" => $this->getAttribute($this->getAttribute($_field_, "field_def"), "id"))), "html", null, true);
            echo "\">
\t\t\t\t<label>";
            // line 58
            if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_field_, "title"), "html", null, true);
            echo "</label>
\t\t\t\t\t</li>
\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['field'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 61
        echo "\t\t\t\t";
        if (isset($context["section"])) { $_section_ = $context["section"]; } else { $_section_ = null; }
        if (($_section_ == "create")) {
            // line 62
            echo "\t\t\t\t\t<li class=\"draggable\" data-item-id=\"person_name\"><label>Your Name</label></li>
\t\t\t\t\t<li class=\"draggable\" data-item-id=\"captcha\"><label>";
            // line 63
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.captcha");
            echo "</label></li>
\t\t\t\t";
        }
        // line 65
        echo "\t\t\t</ul>
\t\t</article>
\t</section>
</div>
";
    }

    // line 70
    public function block_pagebar($context, array $blocks = array())
    {
        // line 71
        echo "\t<nav>
\t\t<ul>
\t\t\t<li><a href=\"";
        // line 73
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_tickets_editor_reset", array("security_token" => $this->env->getExtension('deskpro_templating')->securityToken("reset_editor"))), "html", null, true);
        echo "\" onclick=\"return confirm('Are you sure you want to reset all layouts? This will revert all layouts to their defaults and any customisations you have made will be lost.');\">Reset All Layouts</a></li>
\t\t</ul>
\t</nav>
\t<ul>
\t\t<li>";
        // line 77
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.ticket_layout_editor");
        echo "</li>
\t\t";
        // line 78
        if (isset($context["department"])) { $_department_ = $context["department"]; } else { $_department_ = null; }
        if ($_department_) {
            // line 79
            echo "\t\t\t<li>";
            if (isset($context["department"])) { $_department_ = $context["department"]; } else { $_department_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_department_, "full_title"), "html", null, true);
            echo "</li>
\t\t";
        } else {
            // line 81
            echo "\t\t\t<li>";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.default_layout");
            echo "</li>
\t\t";
        }
        // line 83
        echo "\t</ul>
";
    }

    // line 85
    public function block_content($context, array $blocks = array())
    {
        // line 86
        echo "
<div style=\"padding: 10px 10px 0 10px\">
";
        // line 88
        if (isset($context["design"])) { $_design_ = $context["design"]; } else { $_design_ = null; }
        echo $_design_->gethelpbox();
        echo "
\t<p>
        ";
        // line 90
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.notice_can_edit_form");
        echo "
\t</p>
";
        // line 92
        if (isset($context["design"])) { $_design_ = $context["design"]; } else { $_design_ = null; }
        echo $_design_->gethelpbox_end();
        echo "
</div>

<div class=\"pagetop-option\">
\t<input type=\"checkbox\" class=\"onoff-slider\" id=\"per_department_check\" data-update-url=\"";
        // line 96
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_tickets_editor_toggleper"), "html", null, true);
        echo "\" ";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.per_department_form"), "method")) {
            echo "checked=\"checked\"";
        }
        echo "/> ";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.enable_custom_layouts_department");
        echo "
\t";
        // line 97
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.per_department_form"), "method")) {
            // line 98
            echo "\t\t<div class=\"sub\">
\t\t\t<select id=\"department_switcher\">
\t\t\t\t<option value=\"0\" ";
            // line 100
            if (isset($context["dep_ids_custom"])) { $_dep_ids_custom_ = $context["dep_ids_custom"]; } else { $_dep_ids_custom_ = null; }
            if (twig_in_filter(0, $_dep_ids_custom_)) {
                echo "class=\"custom\"";
            }
            echo " ";
            if (isset($context["department"])) { $_department_ = $context["department"]; } else { $_department_ = null; }
            if ((!$_department_)) {
                echo "selected=\"selected\"";
            }
            echo " data-refresh-url=\"";
            if (isset($context["section"])) { $_section_ = $context["section"]; } else { $_section_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_tickets_editor_dep", array("department_id" => "0", "section" => $_section_)), "html", null, true);
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.default_layout");
            echo "</option>
\t\t\t\t";
            // line 101
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "departments"), "getRootNodes", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["dep"]) {
                if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                if ($this->getAttribute($_dep_, "is_tickets_enabled")) {
                    // line 102
                    echo "\t\t\t\t\t";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    if (twig_length_filter($this->env, $this->getAttribute($_dep_, "children"))) {
                        // line 103
                        echo "\t\t\t\t\t\t<optgroup label=\"";
                        if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                        echo "\">
\t\t\t\t\t\t\t";
                        // line 104
                        if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_dep_, "children"));
                        foreach ($context['_seq'] as $context["_key"] => $context["subdep"]) {
                            // line 105
                            echo "\t\t\t\t\t\t\t\t<option ";
                            if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                            if (isset($context["dep_ids_custom"])) { $_dep_ids_custom_ = $context["dep_ids_custom"]; } else { $_dep_ids_custom_ = null; }
                            if (twig_in_filter($this->getAttribute($_subdep_, "id"), $_dep_ids_custom_)) {
                                echo "class=\"custom\"";
                            }
                            echo " data-full-title=\"";
                            if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                            echo " &gt; ";
                            if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_subdep_, "title"), "html", null, true);
                            echo "\" value=\"";
                            if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_subdep_, "id"), "html", null, true);
                            echo "\" ";
                            if (isset($context["department"])) { $_department_ = $context["department"]; } else { $_department_ = null; }
                            if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                            if (($this->getAttribute($_department_, "id") == $this->getAttribute($_subdep_, "id"))) {
                                echo "selected=\"selected\"";
                            }
                            echo " data-refresh-url=\"";
                            if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                            if (isset($context["section"])) { $_section_ = $context["section"]; } else { $_section_ = null; }
                            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_tickets_editor_dep", array("department_id" => $this->getAttribute($_subdep_, "id"), "section" => $_section_)), "html", null, true);
                            echo "\">";
                            if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_subdep_, "title"), "html", null, true);
                            echo "</option>
\t\t\t\t\t\t\t";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subdep'], $context['_parent'], $context['loop']);
                        $context = array_merge($_parent, array_intersect_key($context, $_parent));
                        // line 107
                        echo "\t\t\t\t\t\t</optgroup>
\t\t\t\t\t";
                    } else {
                        // line 109
                        echo "\t\t\t\t\t\t<option ";
                        if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                        if (isset($context["dep_ids_custom"])) { $_dep_ids_custom_ = $context["dep_ids_custom"]; } else { $_dep_ids_custom_ = null; }
                        if (twig_in_filter($this->getAttribute($_dep_, "id"), $_dep_ids_custom_)) {
                            echo "class=\"custom\"";
                        }
                        echo " value=\"";
                        if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "id"), "html", null, true);
                        echo "\" ";
                        if (isset($context["department"])) { $_department_ = $context["department"]; } else { $_department_ = null; }
                        if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                        if (($this->getAttribute($_department_, "id") == $this->getAttribute($_dep_, "id"))) {
                            echo "selected=\"selected\"";
                        }
                        echo " data-refresh-url=\"";
                        if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                        if (isset($context["section"])) { $_section_ = $context["section"]; } else { $_section_ = null; }
                        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_tickets_editor_dep", array("department_id" => $this->getAttribute($_dep_, "id"), "section" => $_section_)), "html", null, true);
                        echo "\">";
                        if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                        echo "</option>
\t\t\t\t\t";
                    }
                    // line 111
                    echo "\t\t\t\t";
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['dep'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 112
            echo "\t\t\t</select>
\t\t</div>
\t";
        }
        // line 115
        echo "</div>

<div class=\"content-tabs\">
\t<nav>
\t\t<span class=\"saving-text\" id=\"saving_text\" style=\"display: none\">";
        // line 119
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.saving");
        echo "</span>
\t\t<ul>
\t\t\t<li ";
        // line 121
        if (isset($context["section"])) { $_section_ = $context["section"]; } else { $_section_ = null; }
        if (($_section_ == "create")) {
            echo "class=\"on\"";
        }
        echo ">
\t\t\t\t<div ";
        // line 122
        if (isset($context["custom_sections"])) { $_custom_sections_ = $context["custom_sections"]; } else { $_custom_sections_ = null; }
        if (twig_in_filter("create", $_custom_sections_)) {
            echo "class=\"orange-pip\"";
        }
        echo ">
\t\t\t\t\t<a href=\"";
        // line 123
        if (isset($context["department"])) { $_department_ = $context["department"]; } else { $_department_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_tickets_editor_dep", array("department_id" => (($this->getAttribute($_department_, "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_department_, "id"), "0")) : ("0")))), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.new_ticket_form");
        echo "</a>
\t\t\t\t</div>
\t\t\t</li>
\t\t\t<li ";
        // line 126
        if (isset($context["section"])) { $_section_ = $context["section"]; } else { $_section_ = null; }
        if (($_section_ == "view")) {
            echo "class=\"on\"";
        }
        echo ">
\t\t\t\t<div ";
        // line 127
        if (isset($context["custom_sections"])) { $_custom_sections_ = $context["custom_sections"]; } else { $_custom_sections_ = null; }
        if (twig_in_filter("view", $_custom_sections_)) {
            echo "class=\"orange-pip\"";
        }
        echo ">
\t\t\t\t\t<a href=\"";
        // line 128
        if (isset($context["department"])) { $_department_ = $context["department"]; } else { $_department_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_tickets_editor_dep", array("department_id" => (($this->getAttribute($_department_, "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_department_, "id"), "0")) : ("0")), "section" => "view")), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.view_ticket_page");
        echo "</a>
\t\t\t\t</div>
\t\t\t</li>
\t\t\t<li class=\"";
        // line 131
        if (isset($context["section"])) { $_section_ = $context["section"]; } else { $_section_ = null; }
        if (($_section_ == "modify")) {
            echo "on";
        }
        echo "\">
\t\t\t\t<div ";
        // line 132
        if (isset($context["custom_sections"])) { $_custom_sections_ = $context["custom_sections"]; } else { $_custom_sections_ = null; }
        if (twig_in_filter("modify", $_custom_sections_)) {
            echo "class=\"orange-pip\"";
        }
        echo ">
\t\t\t\t\t<a href=\"";
        // line 133
        if (isset($context["department"])) { $_department_ = $context["department"]; } else { $_department_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_tickets_editor_dep", array("department_id" => (($this->getAttribute($_department_, "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_department_, "id"), "0")) : ("0")), "section" => "modify")), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.modify_ticket_form");
        echo "</a>
\t\t\t\t</div>
\t\t\t</li>
\t\t</ul>
\t\t<br />
\t</nav>

\t<div class=\"editor-toggle-alt-view\" ";
        // line 140
        if (isset($context["is_default"])) { $_is_default_ = $context["is_default"]; } else { $_is_default_ = null; }
        if ($_is_default_) {
            echo "style=\"border-bottom: none\"";
        }
        echo ">
\t\t";
        // line 141
        if (isset($context["is_default"])) { $_is_default_ = $context["is_default"]; } else { $_is_default_ = null; }
        if ($_is_default_) {
            // line 142
            echo "\t\t\t";
            if (isset($context["section"])) { $_section_ = $context["section"]; } else { $_section_ = null; }
            if ((($_section_ == "view") || ($_section_ == "modify"))) {
                // line 143
                echo "\t\t\t\t";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.view_modify_ticket_page_default_explain");
                echo "
\t\t\t";
            } else {
                // line 145
                echo "\t\t\t\t";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.new_ticket_form_default_explain");
                echo "
\t\t\t";
            }
            // line 147
            echo "\t\t\t<br />
\t\t";
        }
        // line 149
        echo "\t\t<input
\t\t\ttype=\"checkbox\"
\t\t\tclass=\"onoff-slider\"
\t\t\tid=\"alternative_view_toggle\"
\t\t\tdata-enable=\"";
        // line 153
        if (isset($context["department"])) { $_department_ = $context["department"]; } else { $_department_ = null; }
        if (isset($context["section"])) { $_section_ = $context["section"]; } else { $_section_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_tickets_editor_dep_init", array("department_id" => (($this->getAttribute($_department_, "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_department_, "id"), 0)) : (0)), "section" => $_section_)), "html", null, true);
        echo "\"
\t\t\tdata-disable=\"";
        // line 154
        if (isset($context["department"])) { $_department_ = $context["department"]; } else { $_department_ = null; }
        if (isset($context["section"])) { $_section_ = $context["section"]; } else { $_section_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_tickets_editor_dep_revert", array("department_id" => (($this->getAttribute($_department_, "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_department_, "id"), 0)) : (0)), "section" => $_section_)), "html", null, true);
        echo "\"
\t\t\t";
        // line 155
        if (isset($context["is_default"])) { $_is_default_ = $context["is_default"]; } else { $_is_default_ = null; }
        if ((!$_is_default_)) {
            echo "checked=\"checked\"";
        }
        // line 156
        echo "\t\t/>
        ";
        // line 157
        if (isset($context["section"])) { $_section_ = $context["section"]; } else { $_section_ = null; }
        if (($_section_ == "view")) {
            // line 158
            echo "        \t";
            $context["phrase_part"] = $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.view_ticket_page");
            // line 159
            echo "        ";
        } elseif (($_section_ == "create")) {
            // line 160
            echo "        \t";
            $context["phrase_part"] = $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.new_ticket_form");
            // line 161
            echo "        ";
        } else {
            // line 162
            echo "        \t";
            $context["phrase_part"] = $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.modify_ticket_form");
            // line 163
            echo "        ";
        }
        // line 164
        echo "        ";
        if (isset($context["phrase_part"])) { $_phrase_part_ = $context["phrase_part"]; } else { $_phrase_part_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.enable_alternative_for_x", array("subphrase" => $_phrase_part_), true);
        echo "
\t</div>

\t<section ";
        // line 167
        if (isset($context["is_default"])) { $_is_default_ = $context["is_default"]; } else { $_is_default_ = null; }
        if ($_is_default_) {
            echo "style=\"display: none\"";
        }
        echo ">
\t\t<form
\t\t\taction=\"";
        // line 169
        if (isset($context["department"])) { $_department_ = $context["department"]; } else { $_department_ = null; }
        if (isset($context["section"])) { $_section_ = $context["section"]; } else { $_section_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_tickets_editor_dep_save", array("department_id" => (($this->getAttribute($_department_, "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_department_, "id"), 0)) : (0)), "section" => $_section_)), "html", null, true);
        echo "\"
\t\t\tmethod=\"post\"
\t\t\tclass=\"dp-page-box\"
\t\t\tid=\"admin_ticket_editor\"
\t\t\tdata-element-handler=\"DeskPRO.Admin.ElementHandler.TicketEditor\"
\t\t\tdata-section=\"";
        // line 174
        if (isset($context["section"])) { $_section_ = $context["section"]; } else { $_section_ = null; }
        echo twig_escape_filter($this->env, $_section_, "html", null, true);
        echo "\"
\t\t>
\t\t<ul id=\"admin_ticket_editor_items\" class=\"dp-form\">
\t\t\t<li class=\"no-items-notice\">
\t\t\t\t";
        // line 178
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.drag_drop_fields");
        echo "
\t\t\t</li>
\t\t</ul>
\t\t</form>
\t</section>
</div>

";
        // line 188
        echo "<script type=\"text/x-deskpro-plain\" id=\"editor_row_tpl\">
\t<li class=\"form-item dp-form-row\">
\t\t<div class=\"dp-block-controls\">
\t\t\t<ul>
\t\t\t\t<li class=\"dp-edit-fields\"><span></span></li>
\t\t\t\t<li class=\"dp-edit edit-field-trigger\"><span></span></li>
\t\t\t\t<li class=\"dp-remove remove-field-trigger\"><span></span></li>
\t\t\t\t<li class=\"dp-move\"><span></span></li>
\t\t\t</ul>
\t\t</div>
\t\t<div class=\"dp-form-label\">
\t\t\t<label class=\"field-title\"></label>
\t\t\t<span class=\"field-agent-only\" style=\"display:none\">Agent Only</span>
\t\t</div>
\t\t<article class=\"dp-form-input\"></article>

\t\t<div class=\"field-options-overlay\" style=\"width: 760px; height: 260px; display: none;\">
\t\t\t<div class=\"overlay-title\">
\t\t\t\t<span class=\"close-trigger close-overlay\"></span>
\t\t\t\t<h4>";
        // line 207
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.field_options");
        echo "</h4>
\t\t\t</div>
\t\t\t<div class=\"overlay-content\">
\t\t\t\t<div class=\"dp-form\">
\t\t\t\t\t<div class=\"choices-container\"></div>
\t\t\t\t\t<div class=\"dp-form-row\">
\t\t\t\t\t\t<div class=\"dp-form-label agent_only_opt\">
\t\t\t\t\t\t\t<label>
\t\t\t\t\t\t\t\t<input type=\"checkbox\" class=\"agent_only\" name=\"agent_only\" value=\"1\" />
\t\t\t\t\t\t\t\tOnly show this field for agents
\t\t\t\t\t\t\t</label>
\t\t\t\t\t\t\t<br />
\t\t\t\t\t\t\t<br />
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"dp-form-label not_agent_only_opt\">
\t\t\t\t\t\t\t<label>
\t\t\t\t\t\t\t\t<input type=\"checkbox\" class=\"not_agent_only\" name=\"not_agent_only\" value=\"1\" />
\t\t\t\t\t\t\t\tShow this field for both users and agents
\t\t\t\t\t\t\t</label>
\t\t\t\t\t\t\t<br />
\t\t\t\t\t\t\t<br />
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"dp-form-label\">
\t\t\t\t\t\t\t<label>
                                ";
        // line 231
        ob_start();
        // line 232
        echo "\t\t\t\t\t\t\t\t<select name=\"term_match_type\">
                                    <option value=\"all\">";
        // line 233
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.all");
        echo "</option>
                                    <option value=\"any\">";
        // line 234
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.any");
        echo "</option>
                                </select>
                                ";
        $context["phrase_part"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 237
        echo "                                ";
        if (isset($context["phrase_part"])) { $_phrase_part_ = $context["phrase_part"]; } else { $_phrase_part_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.show_field_when_choice", array("choice" => $_phrase_part_), true);
        echo "
                            </label>
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"dp-form-input\">
\t\t\t\t\t\t\t<div class=\"criteria-form search-form\">
\t\t\t\t\t\t\t\t<div class=\"search-terms\"></div>
\t\t\t\t\t\t\t\t<div class=\"term\"><span class=\"add-term\">+</span> ";
        // line 243
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.add_a_new_criteria");
        echo "</div>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</li>
</script>

";
        // line 256
        echo "<script type=\"text/x-deskpro-plain\" id=\"field_options_choices_tpl\">
\t<div class=\"dp-form-row\">
\t\t<div class=\"dp-form-label\">
\t\t\t<label>
\t\t\t\t<input type=\"checkbox\" class=\"custom_options\" name=\"custom_options\" value=\"1\" /> ";
        // line 260
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.customize_display_choices");
        echo "
\t\t\t</label>
\t\t</div>
\t\t<div class=\"dp-form-input custom-options\" style=\"display:none\"></div>
\t</div>
</script>

";
        // line 270
        echo "<script type=\"text/x-deskpro-plain\" id=\"rendered_field_person_name\">
\t<div class=\"person_name\">
\t\t<input type=\"text\" />
\t</div>
</script>

";
        // line 279
        echo "<script type=\"text/x-deskpro-plain\" id=\"rendered_field_ticket_subject\">
\t<div class=\"subject\">
\t\t<input type=\"text\" />
\t</div>
</script>

";
        // line 288
        echo "<script type=\"text/x-deskpro-plain\" id=\"rendered_field_ticket_cc_emails\">
\t<div class=\"ticket_cc_emails\">
\t\t<input type=\"text\" />
\t</div>
</script>

";
        // line 297
        echo "<script type=\"text/x-deskpro-plain\" id=\"rendered_field_ticket_remove_ccs\">
\t<div class=\"ticket_remove_ccs\">
\t\t<ul><label><input type=\"checkbox\" /> Name &lt;email@example.com&gt;</label></ul>
\t</div>
</script>


";
        // line 307
        echo "<script type=\"text/x-deskpro-plain\" id=\"rendered_field_ticket_department\">
\t<div class=\"subject\">
\t\t<select>
\t\t\t<option>Choose a department...</option>
\t\t\t";
        // line 311
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "departments"), "getPersonDepartments", array(0 => $this->getAttribute($_app_, "user"), 1 => "tickets"), "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["dep"]) {
            // line 312
            echo "\t\t\t\t";
            if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
            if (twig_length_filter($this->env, $this->getAttribute($_dep_, "children"))) {
                // line 313
                echo "\t\t\t\t\t<optgroup label=\"";
                if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                echo "\">
\t\t\t\t\t\t";
                // line 314
                if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($_dep_, "children"));
                foreach ($context['_seq'] as $context["_key"] => $context["sub"]) {
                    // line 315
                    echo "\t\t\t\t\t\t\t<option>";
                    if (isset($context["sub"])) { $_sub_ = $context["sub"]; } else { $_sub_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_sub_, "title"), "html", null, true);
                    echo "</option>
\t\t\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['sub'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 317
                echo "\t\t\t\t\t</optgroup>
\t\t\t\t";
            } else {
                // line 319
                echo "\t\t\t\t\t<option>";
                if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                echo "</option>
\t\t\t\t";
            }
            // line 321
            echo "\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['dep'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 322
        echo "\t\t</select>
\t</div>
</script>

";
        // line 329
        echo "<script type=\"text/x-deskpro-plain\" id=\"rendered_field_message\">
\t<div class=\"message\">
\t\t<textarea class=\"message-input\" style=\"height: 80px; resize: none;\"></textarea>
\t</div>
</script>

";
        // line 338
        echo "<script type=\"text/x-deskpro-plain\" id=\"rendered_field_attachments\">
\t<div class=\"attachments\">
\t\t<input type=\"file\" /> <button class=\"clean-white small\">";
        // line 340
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.upload");
        echo "</button>
\t</div>
</script>

";
        // line 347
        echo "
";
        // line 348
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        if ($this->getAttribute($_term_options_, "ticket_categories_full")) {
            // line 349
            echo "\t<script type=\"text/x-deskpro-plain\" id=\"rendered_field_ticket_category\">
\t\t<div class=\"ticket-category\">
\t\t\t<select class=\"ticket-category-input field-value\">
\t\t\t\t<option>";
            // line 352
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.choose_a_category");
            echo "</option>
\t\t\t\t";
            // line 353
            if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "ticket_categories_full"));
            foreach ($context['_seq'] as $context["id"] => $context["label"]) {
                // line 354
                echo "\t\t\t\t\t<option value=\"";
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                echo twig_escape_filter($this->env, $_id_, "html", null, true);
                echo "\">";
                if (isset($context["label"])) { $_label_ = $context["label"]; } else { $_label_ = null; }
                echo twig_escape_filter($this->env, $_label_, "html", null, true);
                echo "</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['id'], $context['label'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 356
            echo "\t\t\t</select>
\t\t</div>
\t</script>
";
        }
        // line 360
        echo "
";
        // line 364
        echo "
";
        // line 365
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (twig_length_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "TicketWorkflow"), "method"), "getAll", array(), "method"))) {
            // line 366
            echo "\t<script type=\"text/x-deskpro-plain\" id=\"rendered_field_ticket_workflow\">
\t\t<div class=\"ticket-workflow\">
\t\t\t<select class=\"ticket-workflow-input field-value\">
\t\t\t\t<option>Workflow</option>
\t\t\t\t";
            // line 370
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "TicketWorkflow"), "method"), "getAll", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["work"]) {
                // line 371
                echo "\t\t\t\t\t<option value=\"";
                if (isset($context["work"])) { $_work_ = $context["work"]; } else { $_work_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_work_, "id"), "html", null, true);
                echo "\">";
                if (isset($context["work"])) { $_work_ = $context["work"]; } else { $_work_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_work_, "title"), "html", null, true);
                echo "</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['work'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 373
            echo "\t\t\t</select>
\t\t\t<div style=\"font-size: 10px\">Note: This field is only displayed for agents</div>
\t\t</div>
\t</script>
";
        }
        // line 378
        echo "
";
        // line 382
        echo "
";
        // line 383
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        if ($this->getAttribute($_term_options_, "products")) {
            // line 384
            echo "\t<script type=\"text/x-deskpro-plain\" id=\"rendered_field_ticket_product\">
\t\t<div class=\"ticket-product\">
\t\t\t<select class=\"ticket-product-input field-value\">
\t\t\t\t<option>";
            // line 387
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.choose_a_product");
            echo "</option>
\t\t\t\t";
            // line 388
            if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "products"));
            foreach ($context['_seq'] as $context["id"] => $context["label"]) {
                // line 389
                echo "\t\t\t\t\t<option value=\"";
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                echo twig_escape_filter($this->env, $_id_, "html", null, true);
                echo "\">";
                if (isset($context["label"])) { $_label_ = $context["label"]; } else { $_label_ = null; }
                echo twig_escape_filter($this->env, $_label_, "html", null, true);
                echo "</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['id'], $context['label'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 391
            echo "\t\t\t</select>
\t\t</div>
\t</script>
";
        }
        // line 395
        echo "
";
        // line 399
        echo "
";
        // line 400
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        if ($this->getAttribute($_term_options_, "priorities")) {
            // line 401
            echo "\t<script type=\"text/x-deskpro-plain\" id=\"rendered_field_ticket_priority\">
\t\t<div class=\"ticket-priority\">
\t\t\t<select class=\"ticket-priority-input field-value\">
\t\t\t\t<option>";
            // line 404
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.choose_a_priority");
            echo "</option>
\t\t\t\t";
            // line 405
            if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_term_options_, "priorities"));
            foreach ($context['_seq'] as $context["id"] => $context["label"]) {
                // line 406
                echo "\t\t\t\t\t<option value=\"";
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                echo twig_escape_filter($this->env, $_id_, "html", null, true);
                echo "\">";
                if (isset($context["label"])) { $_label_ = $context["label"]; } else { $_label_ = null; }
                echo twig_escape_filter($this->env, $_label_, "html", null, true);
                echo "</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['id'], $context['label'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 408
            echo "\t\t\t</select>
\t\t</div>
\t</script>
";
        }
        // line 412
        echo "
";
        // line 416
        echo "
";
        // line 417
        if (isset($context["custom_ticket_fields"])) { $_custom_ticket_fields_ = $context["custom_ticket_fields"]; } else { $_custom_ticket_fields_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_custom_ticket_fields_);
        foreach ($context['_seq'] as $context["_key"] => $context["field"]) {
            // line 418
            echo "\t<script type=\"text/x-deskpro-plain\" id=\"rendered_field_ticket_field_";
            if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_field_, "id"), "html", null, true);
            echo "\">
\t\t<div class=\"ticket-field field-value\">
\t\t\t";
            // line 420
            if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
            echo $this->env->getExtension('deskpro_templating')->renderCustomFieldForm($_field_);
            echo "
\t\t</div>
\t</script>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['field'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 424
        echo "
";
        // line 428
        echo "<script type=\"text/x-deskpro-plain\" id=\"rendered_field_captcha\">
\t<div class=\"captcha\">
\t\t<img src=\"";
        // line 430
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("images/admin/captcha-example.png"), "html", null, true);
        echo "\"
\t</div>
</script>


";
        // line 438
        echo "
";
        // line 439
        if (isset($context["term_options"])) { $_term_options_ = $context["term_options"]; } else { $_term_options_ = null; }
        if (((($this->getAttribute($_term_options_, "products") || $this->getAttribute($_term_options_, "ticket_categories_full")) || $this->getAttribute($_term_options_, "priorities")) || $this->getAttribute($_term_options_, "ticket_workflows"))) {
            // line 440
            echo "<div id=\"criteria_tpl\" class=\"search-builder-tpl\" style=\"display:none\">
\t<div class=\"row\">
\t\t<div class=\"term\">
\t\t\t<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\" class=\"term-table\"><tbody><tr>
\t\t\t\t<td style=\"vertical-align: middle; text-align: center;\" width=\"11\"><div class=\"builder-remove\">-</div></td>
\t\t\t\t<td class=\"builder-controls\" style=\"vertical-align: middle;\">
\t\t\t\t\t<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\"><tbody><tr>
\t\t\t\t\t\t<td style=\"vertical-align: middle;\" width=\"10\" nowrap=\"nowrap\"><div class=\"builder-type-choice\"></div></td>
\t\t\t\t\t\t<td style=\"vertical-align: middle;\" width=\"10\" nowrap=\"nowrap\"><div class=\"builder-op\"></div></td>
\t\t\t\t\t\t<td style=\"vertical-align: middle;\"><div class=\"builder-options\"></div></td>
\t\t\t\t\t</tr></tbody></table>
\t\t\t\t</td>
\t\t\t</tr></tbody></table>
\t\t</div>
\t</div>
\t";
            // line 455
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_product"), "method")) {
                // line 456
                echo "\t\t<div class=\"builder-type\" title=\"";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.product");
                echo "\" data-rule-type=\"product\">
\t\t\t<div class=\"builder-op\">
\t\t\t\t<select name=\"op\">
\t\t\t\t\t<option value=\"is\">";
                // line 459
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
                echo "</option>
\t\t\t\t\t<option value=\"not\">";
                // line 460
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
                echo "</option>
\t\t\t\t</select>
\t\t\t</div>
\t\t\t<div class=\"builder-options\">
\t\t\t\t<select name=\"product\">
\t\t\t\t\t";
                // line 465
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "products"), "getRootNodes", array(), "method"));
                foreach ($context['_seq'] as $context["_key"] => $context["cat"]) {
                    // line 466
                    echo "\t\t\t\t\t\t";
                    if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                    if (twig_length_filter($this->env, $this->getAttribute($_cat_, "children"))) {
                        // line 467
                        echo "\t\t\t\t\t\t\t<optgroup label=\"";
                        if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
                        echo "\">
\t\t\t\t\t\t\t\t";
                        // line 468
                        if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_cat_, "children"));
                        foreach ($context['_seq'] as $context["_key"] => $context["subcat"]) {
                            // line 469
                            echo "\t\t\t\t\t\t\t\t\t<option data-full-title=\"";
                            if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
                            echo " &gt; ";
                            if (isset($context["subcat"])) { $_subcat_ = $context["subcat"]; } else { $_subcat_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_subcat_, "title"), "html", null, true);
                            echo "\" value=\"";
                            if (isset($context["subcat"])) { $_subcat_ = $context["subcat"]; } else { $_subcat_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_subcat_, "id"), "html", null, true);
                            echo "\">";
                            if (isset($context["subcat"])) { $_subcat_ = $context["subcat"]; } else { $_subcat_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_subcat_, "title"), "html", null, true);
                            echo "</option>
\t\t\t\t\t\t\t\t";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subcat'], $context['_parent'], $context['loop']);
                        $context = array_merge($_parent, array_intersect_key($context, $_parent));
                        // line 471
                        echo "\t\t\t\t\t\t\t</optgroup>
\t\t\t\t\t\t";
                    } else {
                        // line 473
                        echo "\t\t\t\t\t\t\t<option value=\"";
                        if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "id"), "html", null, true);
                        echo "\">";
                        if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
                        echo "</option>
\t\t\t\t\t\t";
                    }
                    // line 475
                    echo "\t\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['cat'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 476
                echo "\t\t\t\t</select>
\t\t\t</div>
\t\t</div>
\t";
            }
            // line 480
            echo "\t";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_ticket_category"), "method")) {
                // line 481
                echo "\t\t<div class=\"builder-type\" title=\"";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.category");
                echo "\" data-rule-type=\"category\">
\t\t\t<div class=\"builder-op\">
\t\t\t\t<select name=\"op\">
\t\t\t\t\t<option value=\"is\">";
                // line 484
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
                echo "</option>
\t\t\t\t\t<option value=\"not\">";
                // line 485
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
                echo "</option>
\t\t\t\t</select>
\t\t\t</div>
\t\t\t<div class=\"builder-options\">
\t\t\t\t<select name=\"category\">
\t\t\t\t\t";
                // line 490
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "TicketCategory"), "method"), "getRootNodes", array(), "method"));
                foreach ($context['_seq'] as $context["_key"] => $context["cat"]) {
                    // line 491
                    echo "\t\t\t\t\t\t";
                    if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                    if (twig_length_filter($this->env, $this->getAttribute($_cat_, "children"))) {
                        // line 492
                        echo "\t\t\t\t\t\t\t<optgroup label=\"";
                        if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
                        echo "\">
\t\t\t\t\t\t\t\t";
                        // line 493
                        if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_cat_, "children"));
                        foreach ($context['_seq'] as $context["_key"] => $context["subcat"]) {
                            // line 494
                            echo "\t\t\t\t\t\t\t\t\t<option data-full-title=\"";
                            if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
                            echo " &gt; ";
                            if (isset($context["subcat"])) { $_subcat_ = $context["subcat"]; } else { $_subcat_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_subcat_, "title"), "html", null, true);
                            echo "\" value=\"";
                            if (isset($context["subcat"])) { $_subcat_ = $context["subcat"]; } else { $_subcat_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_subcat_, "id"), "html", null, true);
                            echo "\">";
                            if (isset($context["subcat"])) { $_subcat_ = $context["subcat"]; } else { $_subcat_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_subcat_, "title"), "html", null, true);
                            echo "</option>
\t\t\t\t\t\t\t\t";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subcat'], $context['_parent'], $context['loop']);
                        $context = array_merge($_parent, array_intersect_key($context, $_parent));
                        // line 496
                        echo "\t\t\t\t\t\t\t</optgroup>
\t\t\t\t\t\t";
                    } else {
                        // line 498
                        echo "\t\t\t\t\t\t\t<option value=\"";
                        if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "id"), "html", null, true);
                        echo "\">";
                        if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
                        echo "</option>
\t\t\t\t\t\t";
                    }
                    // line 500
                    echo "\t\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['cat'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 501
                echo "\t\t\t\t</select>
\t\t\t</div>
\t\t</div>
\t";
            }
            // line 505
            echo "\t";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_ticket_priority"), "method")) {
                // line 506
                echo "\t\t<div class=\"builder-type\" title=\"";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.priority");
                echo "\" data-rule-type=\"priority\">
\t\t\t<div class=\"builder-op\">
\t\t\t\t<select name=\"op\">
\t\t\t\t\t<option value=\"is\">";
                // line 509
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
                echo "</option>
\t\t\t\t\t<option value=\"not\">";
                // line 510
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
                echo "</option>
\t\t\t\t\t<option value=\"lt\">&lt;</option>
\t\t\t\t\t<option value=\"lte\">&lt;=</option>
\t\t\t\t\t<option value=\"gt\">&gt;</option>
\t\t\t\t\t<option value=\"gte\">&gt;=</option>
\t\t\t\t</select>
\t\t\t</div>
\t\t\t<div class=\"builder-options\">
\t\t\t\t<select name=\"priority\">
\t\t\t\t\t";
                // line 519
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "TicketPriority"), "method"), "getNames", array(), "method"));
                foreach ($context['_seq'] as $context["id"] => $context["name"]) {
                    // line 520
                    echo "\t\t\t\t\t\t<option value=\"";
                    if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                    echo twig_escape_filter($this->env, $_id_, "html", null, true);
                    echo "\">";
                    if (isset($context["name"])) { $_name_ = $context["name"]; } else { $_name_ = null; }
                    echo twig_escape_filter($this->env, $_name_, "html", null, true);
                    echo "</option>
\t\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['id'], $context['name'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 522
                echo "\t\t\t\t</select>
\t\t\t</div>
\t\t</div>
\t";
            }
            // line 526
            echo "\t";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_ticket_workflow"), "method")) {
                // line 527
                echo "\t\t<div class=\"builder-type\" title=\"";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.workflow");
                echo "\" data-rule-type=\"workflow\">
\t\t\t<div class=\"builder-op\">
\t\t\t\t<select name=\"op\">
\t\t\t\t\t<option value=\"is\">";
                // line 530
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is");
                echo "</option>
\t\t\t\t\t<option value=\"not\">";
                // line 531
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.is_not");
                echo "</option>
\t\t\t\t</select>
\t\t\t</div>
\t\t\t<div class=\"builder-options\">
\t\t\t\t<select name=\"workflow\">
\t\t\t\t\t";
                // line 536
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "TicketWorkflow"), "method"), "getAll", array(), "method"));
                foreach ($context['_seq'] as $context["_key"] => $context["work"]) {
                    // line 537
                    echo "\t\t\t\t\t\t<option value=\"";
                    if (isset($context["work"])) { $_work_ = $context["work"]; } else { $_work_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_work_, "id"), "html", null, true);
                    echo "\">";
                    if (isset($context["work"])) { $_work_ = $context["work"]; } else { $_work_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_work_, "title"), "html", null, true);
                    echo "</option>
\t\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['work'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 539
                echo "\t\t\t\t</select>
\t\t\t</div>
\t\t</div>
\t";
            }
            // line 543
            echo "</div>
";
        }
        // line 545
        echo "
<div id=\"embed_form_overlay\" style=\"width: 785px; height: 555px; display: none;\">
\t<div class=\"overlay-title\">
\t\t<span class=\"close-overlay\"></span>
\t\t<h4>";
        // line 549
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.embed_in_website");
        echo "</h4>
\t</div>
\t<div class=\"overlay-content\">
\t\t<div class=\"loading-icon-big\" style=\"padding-top: 20px;\"></div>
\t</div>
</div>

";
    }

    public function getTemplateName()
    {
        return "AdminBundle:TicketProperties:editor.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1365 => 549,  1359 => 545,  1349 => 539,  1336 => 537,  1331 => 536,  1323 => 531,  1319 => 530,  1312 => 527,  1284 => 519,  1272 => 510,  1268 => 509,  1261 => 506,  1251 => 501,  1245 => 500,  1231 => 496,  1207 => 493,  1197 => 491,  1180 => 484,  1173 => 481,  1169 => 480,  1157 => 475,  1147 => 473,  1109 => 466,  1065 => 440,  1059 => 438,  1047 => 428,  1044 => 424,  1033 => 420,  1009 => 408,  991 => 405,  987 => 404,  973 => 395,  931 => 378,  924 => 373,  911 => 371,  906 => 370,  885 => 356,  872 => 354,  855 => 348,  749 => 279,  701 => 237,  594 => 164,  1163 => 476,  1143 => 471,  1087 => 526,  1077 => 509,  1051 => 430,  1037 => 480,  1010 => 476,  999 => 458,  932 => 414,  899 => 405,  895 => 404,  933 => 149,  914 => 133,  909 => 132,  833 => 329,  783 => 332,  755 => 320,  666 => 300,  453 => 187,  639 => 110,  568 => 254,  520 => 249,  657 => 287,  572 => 251,  609 => 459,  20 => 1,  659 => 207,  562 => 240,  548 => 165,  558 => 244,  479 => 206,  589 => 100,  457 => 145,  413 => 172,  953 => 430,  948 => 403,  935 => 394,  929 => 391,  916 => 382,  864 => 365,  844 => 348,  816 => 342,  807 => 370,  801 => 338,  774 => 307,  766 => 328,  737 => 318,  685 => 293,  664 => 194,  635 => 281,  593 => 445,  546 => 236,  532 => 68,  865 => 221,  852 => 347,  838 => 208,  820 => 201,  781 => 333,  764 => 321,  725 => 256,  632 => 283,  602 => 167,  565 => 155,  529 => 282,  505 => 267,  487 => 53,  473 => 221,  1853 => 842,  1849 => 841,  1839 => 837,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 813,  1778 => 812,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 782,  1705 => 781,  1688 => 770,  1681 => 769,  1677 => 768,  1667 => 764,  1656 => 762,  1648 => 757,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 714,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 695,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 651,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 522,  1289 => 520,  1276 => 604,  1263 => 601,  1244 => 596,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 565,  1181 => 561,  1171 => 557,  1158 => 550,  1114 => 534,  1104 => 465,  1072 => 512,  1062 => 439,  1024 => 494,  1014 => 490,  1000 => 482,  990 => 478,  980 => 474,  960 => 466,  918 => 448,  888 => 376,  834 => 346,  673 => 342,  636 => 185,  462 => 192,  454 => 103,  1144 => 542,  1139 => 399,  1131 => 399,  1127 => 397,  1110 => 396,  1092 => 459,  1089 => 390,  1086 => 520,  1084 => 388,  1063 => 387,  1060 => 386,  1055 => 385,  1050 => 384,  1035 => 372,  1019 => 267,  1003 => 263,  959 => 251,  900 => 366,  880 => 434,  870 => 430,  867 => 353,  859 => 362,  848 => 215,  839 => 376,  828 => 357,  823 => 374,  809 => 181,  800 => 315,  797 => 366,  794 => 336,  786 => 174,  740 => 162,  734 => 332,  703 => 354,  693 => 350,  630 => 278,  626 => 140,  614 => 275,  610 => 169,  581 => 247,  564 => 229,  525 => 235,  722 => 162,  697 => 256,  674 => 277,  671 => 425,  577 => 257,  569 => 243,  557 => 169,  502 => 229,  497 => 132,  445 => 197,  729 => 159,  684 => 261,  676 => 299,  669 => 254,  660 => 145,  647 => 286,  643 => 244,  601 => 178,  570 => 156,  522 => 200,  501 => 58,  296 => 67,  374 => 149,  631 => 111,  616 => 281,  608 => 266,  605 => 16,  596 => 102,  574 => 165,  561 => 209,  527 => 142,  433 => 115,  388 => 161,  426 => 177,  383 => 182,  461 => 44,  370 => 147,  395 => 109,  294 => 72,  223 => 80,  220 => 36,  492 => 395,  468 => 132,  444 => 121,  410 => 169,  397 => 141,  377 => 159,  262 => 81,  250 => 139,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 2108,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 1383,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 1303,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 1173,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 526,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 494,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 538,  1126 => 916,  1106 => 528,  1100 => 897,  1026 => 418,  997 => 822,  983 => 816,  975 => 402,  956 => 800,  939 => 786,  902 => 130,  894 => 364,  879 => 400,  757 => 288,  727 => 316,  716 => 308,  670 => 297,  528 => 232,  476 => 253,  435 => 208,  354 => 153,  341 => 102,  192 => 30,  321 => 154,  243 => 54,  793 => 350,  780 => 311,  758 => 335,  700 => 221,  686 => 231,  652 => 274,  638 => 188,  620 => 174,  545 => 259,  523 => 66,  494 => 55,  459 => 191,  438 => 195,  351 => 104,  347 => 16,  402 => 29,  268 => 83,  430 => 141,  411 => 167,  379 => 23,  322 => 123,  315 => 119,  289 => 113,  284 => 110,  255 => 79,  234 => 70,  1133 => 400,  1124 => 469,  1121 => 56,  1116 => 549,  1113 => 467,  1108 => 51,  1103 => 394,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 483,  1039 => 345,  1025 => 335,  1021 => 417,  1015 => 412,  1008 => 461,  996 => 406,  989 => 454,  985 => 315,  981 => 257,  977 => 313,  970 => 91,  966 => 437,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 148,  919 => 290,  917 => 289,  908 => 411,  905 => 378,  896 => 280,  891 => 360,  877 => 270,  862 => 267,  857 => 380,  837 => 347,  832 => 250,  827 => 322,  821 => 321,  803 => 179,  778 => 389,  769 => 165,  765 => 297,  753 => 328,  746 => 319,  743 => 318,  735 => 170,  730 => 330,  720 => 363,  717 => 362,  712 => 243,  691 => 233,  678 => 149,  654 => 144,  587 => 14,  576 => 158,  539 => 241,  517 => 140,  471 => 47,  441 => 239,  437 => 39,  418 => 201,  386 => 106,  373 => 102,  304 => 114,  270 => 143,  265 => 72,  229 => 75,  477 => 49,  455 => 224,  448 => 41,  429 => 112,  407 => 109,  399 => 162,  389 => 170,  375 => 162,  358 => 110,  349 => 131,  335 => 139,  327 => 155,  298 => 144,  280 => 88,  249 => 205,  194 => 55,  142 => 36,  344 => 92,  318 => 86,  306 => 116,  295 => 74,  357 => 154,  300 => 77,  286 => 90,  276 => 86,  269 => 103,  254 => 101,  128 => 47,  237 => 71,  165 => 64,  122 => 46,  798 => 337,  770 => 179,  759 => 112,  748 => 337,  731 => 260,  721 => 305,  718 => 313,  708 => 309,  696 => 147,  617 => 461,  590 => 226,  553 => 153,  550 => 156,  540 => 234,  533 => 255,  500 => 397,  493 => 225,  489 => 257,  482 => 223,  467 => 126,  464 => 202,  458 => 123,  452 => 217,  449 => 132,  415 => 32,  382 => 24,  372 => 150,  361 => 129,  356 => 105,  339 => 89,  302 => 150,  285 => 115,  258 => 136,  123 => 31,  108 => 42,  424 => 187,  394 => 139,  380 => 151,  338 => 155,  319 => 125,  316 => 123,  312 => 152,  290 => 116,  267 => 96,  206 => 64,  110 => 44,  240 => 86,  224 => 95,  219 => 128,  217 => 94,  202 => 64,  186 => 70,  170 => 55,  100 => 25,  67 => 17,  14 => 1,  1096 => 460,  1090 => 290,  1088 => 289,  1085 => 456,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 416,  1013 => 477,  1007 => 274,  1002 => 823,  993 => 266,  986 => 264,  982 => 401,  976 => 399,  971 => 260,  964 => 255,  949 => 388,  946 => 402,  940 => 384,  937 => 383,  928 => 452,  926 => 413,  915 => 239,  912 => 236,  903 => 231,  898 => 440,  892 => 229,  889 => 748,  887 => 402,  884 => 374,  876 => 222,  874 => 215,  871 => 368,  863 => 352,  861 => 213,  858 => 349,  850 => 378,  843 => 206,  840 => 406,  815 => 372,  812 => 244,  808 => 199,  804 => 395,  799 => 196,  791 => 193,  785 => 312,  775 => 184,  771 => 183,  754 => 340,  728 => 317,  726 => 169,  723 => 168,  715 => 105,  711 => 152,  709 => 222,  706 => 302,  698 => 208,  694 => 308,  692 => 155,  689 => 302,  681 => 300,  677 => 149,  675 => 289,  663 => 276,  661 => 277,  650 => 246,  646 => 112,  629 => 183,  627 => 266,  625 => 180,  622 => 270,  598 => 174,  592 => 261,  586 => 264,  575 => 174,  566 => 242,  556 => 73,  554 => 240,  541 => 163,  536 => 241,  515 => 209,  511 => 269,  509 => 60,  488 => 155,  486 => 220,  483 => 341,  465 => 147,  463 => 216,  450 => 116,  432 => 211,  419 => 173,  371 => 182,  362 => 111,  353 => 141,  337 => 137,  333 => 156,  309 => 118,  303 => 70,  299 => 96,  291 => 92,  272 => 109,  261 => 141,  253 => 91,  239 => 82,  235 => 44,  213 => 139,  200 => 52,  198 => 85,  159 => 38,  149 => 36,  146 => 42,  131 => 36,  116 => 99,  79 => 32,  74 => 28,  71 => 27,  836 => 320,  817 => 398,  814 => 319,  811 => 317,  805 => 313,  787 => 333,  779 => 169,  776 => 305,  773 => 347,  761 => 296,  751 => 163,  747 => 325,  742 => 336,  739 => 333,  736 => 317,  724 => 214,  705 => 278,  702 => 601,  688 => 232,  680 => 278,  667 => 296,  662 => 271,  656 => 418,  649 => 285,  644 => 284,  641 => 268,  624 => 109,  613 => 264,  607 => 273,  597 => 270,  591 => 163,  584 => 259,  579 => 159,  563 => 96,  559 => 154,  551 => 243,  547 => 149,  537 => 145,  524 => 141,  512 => 227,  507 => 156,  504 => 133,  498 => 213,  485 => 153,  480 => 50,  472 => 205,  466 => 210,  460 => 221,  447 => 143,  442 => 40,  434 => 212,  428 => 112,  422 => 176,  404 => 149,  368 => 149,  364 => 101,  340 => 170,  334 => 101,  330 => 148,  325 => 134,  292 => 148,  287 => 67,  282 => 104,  279 => 109,  273 => 85,  266 => 142,  256 => 140,  252 => 78,  228 => 80,  218 => 62,  201 => 74,  64 => 14,  51 => 10,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 1705,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 737,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 731,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 725,  1559 => 601,  1552 => 720,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 708,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 645,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 543,  1342 => 624,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 1001,  1257 => 505,  1254 => 479,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 498,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 492,  1196 => 958,  1192 => 490,  1188 => 456,  1184 => 485,  1179 => 454,  1176 => 453,  1172 => 938,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 561,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 468,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 898,  1099 => 393,  1095 => 419,  1091 => 418,  1082 => 455,  1079 => 524,  1076 => 359,  1070 => 875,  1057 => 491,  1052 => 504,  1045 => 484,  1040 => 377,  1036 => 283,  1032 => 496,  1028 => 274,  1023 => 393,  1020 => 392,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 821,  979 => 400,  974 => 256,  967 => 391,  962 => 803,  958 => 801,  954 => 389,  950 => 153,  945 => 387,  942 => 460,  938 => 150,  934 => 382,  927 => 147,  923 => 387,  920 => 412,  910 => 353,  901 => 347,  897 => 365,  890 => 343,  886 => 50,  883 => 401,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 340,  841 => 338,  835 => 312,  830 => 249,  826 => 202,  822 => 354,  818 => 246,  813 => 183,  810 => 317,  806 => 180,  802 => 198,  795 => 314,  792 => 239,  789 => 313,  784 => 188,  782 => 187,  777 => 291,  772 => 289,  768 => 334,  763 => 327,  760 => 329,  756 => 230,  752 => 176,  745 => 281,  741 => 270,  738 => 379,  732 => 171,  719 => 279,  714 => 264,  710 => 310,  704 => 267,  699 => 303,  695 => 234,  690 => 263,  687 => 279,  683 => 346,  679 => 298,  672 => 276,  668 => 247,  665 => 285,  658 => 244,  645 => 277,  640 => 285,  634 => 267,  628 => 178,  623 => 107,  619 => 298,  611 => 268,  606 => 263,  603 => 267,  599 => 262,  595 => 132,  583 => 263,  580 => 257,  573 => 157,  560 => 75,  543 => 147,  538 => 69,  534 => 233,  530 => 202,  526 => 229,  521 => 226,  518 => 233,  514 => 221,  510 => 227,  503 => 266,  496 => 226,  490 => 131,  484 => 394,  474 => 127,  470 => 231,  446 => 185,  440 => 102,  436 => 182,  431 => 37,  425 => 35,  416 => 231,  412 => 110,  408 => 141,  403 => 194,  400 => 225,  396 => 28,  392 => 139,  385 => 25,  381 => 133,  367 => 147,  363 => 18,  359 => 100,  355 => 326,  350 => 140,  346 => 140,  343 => 134,  328 => 135,  324 => 125,  313 => 98,  307 => 151,  301 => 124,  288 => 116,  283 => 111,  271 => 160,  257 => 148,  251 => 88,  238 => 103,  233 => 81,  195 => 121,  191 => 35,  187 => 57,  183 => 52,  130 => 49,  88 => 93,  76 => 31,  115 => 41,  95 => 28,  655 => 270,  651 => 232,  648 => 269,  637 => 273,  633 => 272,  621 => 462,  618 => 277,  615 => 268,  604 => 186,  600 => 271,  588 => 162,  585 => 161,  582 => 160,  571 => 242,  567 => 95,  555 => 239,  552 => 238,  549 => 237,  544 => 230,  542 => 290,  535 => 233,  531 => 143,  519 => 64,  516 => 63,  513 => 228,  508 => 230,  506 => 59,  499 => 241,  495 => 239,  491 => 54,  481 => 128,  478 => 235,  475 => 184,  469 => 196,  456 => 204,  451 => 122,  443 => 194,  439 => 119,  427 => 177,  423 => 175,  420 => 208,  409 => 179,  405 => 30,  401 => 164,  391 => 107,  387 => 134,  384 => 160,  378 => 154,  365 => 131,  360 => 17,  348 => 191,  336 => 132,  332 => 150,  329 => 127,  323 => 135,  310 => 97,  305 => 112,  277 => 170,  274 => 102,  263 => 97,  259 => 102,  247 => 138,  244 => 137,  241 => 73,  222 => 105,  210 => 122,  207 => 110,  204 => 58,  184 => 28,  181 => 110,  167 => 53,  157 => 114,  96 => 46,  421 => 111,  417 => 71,  414 => 142,  406 => 130,  398 => 165,  393 => 162,  390 => 153,  376 => 22,  369 => 19,  366 => 174,  352 => 140,  345 => 103,  342 => 160,  331 => 125,  326 => 87,  320 => 121,  317 => 100,  314 => 136,  311 => 85,  308 => 116,  297 => 112,  293 => 114,  281 => 146,  278 => 111,  275 => 110,  264 => 104,  260 => 107,  248 => 77,  245 => 104,  242 => 82,  231 => 52,  227 => 131,  215 => 88,  212 => 111,  209 => 125,  197 => 51,  177 => 118,  171 => 55,  161 => 68,  132 => 34,  121 => 46,  105 => 41,  99 => 29,  81 => 16,  77 => 19,  180 => 54,  176 => 45,  156 => 38,  143 => 50,  139 => 104,  118 => 33,  189 => 88,  185 => 46,  173 => 117,  166 => 40,  152 => 60,  174 => 66,  164 => 58,  154 => 113,  150 => 53,  137 => 49,  133 => 48,  127 => 102,  107 => 97,  102 => 34,  83 => 25,  78 => 20,  53 => 10,  23 => 6,  42 => 7,  138 => 52,  134 => 50,  109 => 31,  103 => 30,  97 => 40,  94 => 33,  84 => 9,  75 => 14,  69 => 16,  66 => 16,  54 => 9,  44 => 7,  230 => 74,  226 => 65,  203 => 86,  193 => 122,  188 => 57,  182 => 56,  178 => 49,  168 => 116,  163 => 115,  160 => 68,  155 => 55,  148 => 51,  145 => 37,  140 => 65,  136 => 53,  125 => 16,  120 => 38,  113 => 43,  101 => 33,  92 => 41,  89 => 27,  85 => 26,  73 => 18,  62 => 21,  59 => 10,  56 => 14,  41 => 5,  126 => 47,  119 => 65,  111 => 98,  106 => 30,  98 => 35,  93 => 31,  86 => 27,  70 => 13,  60 => 15,  28 => 1,  36 => 5,  114 => 33,  104 => 30,  91 => 29,  80 => 29,  63 => 30,  58 => 14,  40 => 7,  34 => 4,  45 => 8,  61 => 12,  55 => 11,  48 => 9,  39 => 5,  35 => 4,  31 => 2,  26 => 2,  21 => 2,  46 => 12,  29 => 3,  57 => 14,  50 => 9,  47 => 8,  38 => 5,  33 => 4,  49 => 8,  32 => 3,  246 => 140,  236 => 87,  232 => 129,  225 => 82,  221 => 63,  216 => 65,  214 => 61,  211 => 111,  208 => 67,  205 => 87,  199 => 123,  196 => 85,  190 => 101,  179 => 94,  175 => 76,  172 => 52,  169 => 41,  162 => 80,  158 => 54,  153 => 27,  151 => 112,  147 => 66,  144 => 51,  141 => 55,  135 => 35,  129 => 35,  124 => 35,  117 => 44,  112 => 40,  90 => 36,  87 => 21,  82 => 88,  72 => 19,  68 => 18,  65 => 17,  52 => 12,  43 => 6,  37 => 5,  30 => 3,  27 => 2,  25 => 65,  24 => 3,  22 => 34,  19 => 1,);
    }
}
