<?php

/* AdminBundle:Chat:editor.html.twig */
class __TwigTemplate_a163093b2b6ac6439a5fbf88a5925353 extends \Application\DeskPRO\Twig\Template
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
        $context["this_page"] = "chat_editor";
        // line 3
        $context["design"] = $this->env->loadTemplate("AdminBundle:Common:design-macros.html.twig");
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 4
    public function block_html_head($context, array $blocks = array())
    {
        // line 5
        echo "<script type=\"text/javascript\" src=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("javascripts/DeskPRO/Admin/ElementHandler/ChatEditor.js"), "html", null, true);
        echo "\"></script>
<script type=\"text/javascript\">
var CHAT_DISPLAY_DATA = ";
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
\t\t\t<h4>Chat Fields</h4>
\t\t</header>
\t\t<article id=\"chat_elements\">
\t\t\t<ul>
\t\t\t\t<li class=\"draggable\" data-item-id=\"chat_department\" data-edit-title=\"Edit Departments\" data-edit-url=\"";
        // line 40
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_departments", array("type" => "chat")), "html", null, true);
        echo "\"><label>";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.department");
        echo "</label></li>
\t\t\t\t";
        // line 41
        if (isset($context["section"])) { $_section_ = $context["section"]; } else { $_section_ = null; }
        if (($_section_ == "create")) {
            // line 42
            echo "\t\t\t\t\t<li class=\"draggable\" data-item-id=\"person_name\"><label>Name</label></li>
\t\t\t\t\t<li class=\"draggable\" data-item-id=\"person_email\"><label>Email Address</label></li>
\t\t\t\t";
        }
        // line 45
        echo "\t\t\t\t";
        if (isset($context["custom_chat_fields"])) { $_custom_chat_fields_ = $context["custom_chat_fields"]; } else { $_custom_chat_fields_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_custom_chat_fields_);
        foreach ($context['_seq'] as $context["_key"] => $context["field"]) {
            // line 46
            echo "\t\t\t\t\t<li class=\"draggable\" ";
            if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
            if ($this->getAttribute($this->getAttribute($_field_, "field_def"), "is_agent_field")) {
                echo "data-is-agent-field=\"1\"";
            }
            echo " data-item-id=\"chat_field[";
            if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_field_, "field_def"), "id"), "html", null, true);
            echo "]\" data-edit-title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.edit_field");
            echo "\" data-edit-url=\"";
            if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_customdefchat_edit", array("field_id" => $this->getAttribute($this->getAttribute($_field_, "field_def"), "id"))), "html", null, true);
            echo "\">
\t\t\t\t\t\t<label>";
            // line 47
            if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_field_, "title"), "html", null, true);
            echo "</label>
\t\t\t\t\t</li>
\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['field'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 50
        echo "\t\t\t</ul>
\t\t</article>
\t</section>
</div>
";
    }

    // line 55
    public function block_pagebar($context, array $blocks = array())
    {
        // line 56
        echo "\t<nav>
\t\t<ul>
\t\t\t<li><a href=\"";
        // line 58
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_chat_editor_reset", array("security_token" => $this->env->getExtension('deskpro_templating')->securityToken("reset_editor"))), "html", null, true);
        echo "\" onclick=\"return confirm('Are you sure you want to reset all layouts? This will revert all layouts to their defaults and any customisations you have made will be lost.');\">Reset All Layouts</a></li>
\t\t</ul>
\t</nav>
\t<ul>
\t\t<li>Chat Layout Editor</li>
\t\t";
        // line 63
        if (isset($context["department"])) { $_department_ = $context["department"]; } else { $_department_ = null; }
        if ($_department_) {
            // line 64
            echo "\t\t\t<li>";
            if (isset($context["department"])) { $_department_ = $context["department"]; } else { $_department_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_department_, "full_title"), "html", null, true);
            echo "</li>
\t\t";
        } else {
            // line 66
            echo "\t\t\t<li>Default Layout</li>
\t\t";
        }
        // line 68
        echo "\t</ul>
";
    }

    // line 70
    public function block_content($context, array $blocks = array())
    {
        // line 71
        echo "
<div style=\"padding: 10px 10px 0 10px\">
";
        // line 73
        if (isset($context["design"])) { $_design_ = $context["design"]; } else { $_design_ = null; }
        echo $_design_->gethelpbox();
        echo "
\t<p>
        You can edit the form a user sees when they create a chat. Just drag options from the left into the area below to enable more fields.
\t</p>
";
        // line 77
        if (isset($context["design"])) { $_design_ = $context["design"]; } else { $_design_ = null; }
        echo $_design_->gethelpbox_end();
        echo "
</div>

<div class=\"pagetop-option\">
\t<input type=\"checkbox\" class=\"onoff-slider\" id=\"per_department_check\" data-update-url=\"";
        // line 81
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_chat_editor_toggleper"), "html", null, true);
        echo "\" ";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_chat.per_department_form"), "method")) {
            echo "checked=\"checked\"";
        }
        echo "/> Enable custom layouts
\t";
        // line 82
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_chat.per_department_form"), "method")) {
            // line 83
            echo "\t\t<div class=\"sub\">
\t\t\t<select id=\"department_switcher\">
\t\t\t\t<option value=\"0\" ";
            // line 85
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
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_chat_editor_dep", array("department_id" => "0", "section" => $_section_)), "html", null, true);
            echo "\">Default Layout</option>
\t\t\t\t";
            // line 86
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "departments"), "getRootNodes", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["dep"]) {
                if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                if ($this->getAttribute($_dep_, "is_chat_enabled")) {
                    // line 87
                    echo "\t\t\t\t\t";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    if (twig_length_filter($this->env, $this->getAttribute($_dep_, "children"))) {
                        // line 88
                        echo "\t\t\t\t\t\t<optgroup label=\"";
                        if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                        echo "\">
\t\t\t\t\t\t\t";
                        // line 89
                        if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_dep_, "children"));
                        foreach ($context['_seq'] as $context["_key"] => $context["subdep"]) {
                            // line 90
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
                            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_chat_editor_dep", array("department_id" => $this->getAttribute($_subdep_, "id"), "section" => $_section_)), "html", null, true);
                            echo "\">";
                            if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_subdep_, "title"), "html", null, true);
                            echo "</option>
\t\t\t\t\t\t\t";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subdep'], $context['_parent'], $context['loop']);
                        $context = array_merge($_parent, array_intersect_key($context, $_parent));
                        // line 92
                        echo "\t\t\t\t\t\t</optgroup>
\t\t\t\t\t";
                    } else {
                        // line 94
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
                        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_chat_editor_dep", array("department_id" => $this->getAttribute($_dep_, "id"), "section" => $_section_)), "html", null, true);
                        echo "\">";
                        if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                        echo "</option>
\t\t\t\t\t";
                    }
                    // line 96
                    echo "\t\t\t\t";
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['dep'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 97
            echo "\t\t\t</select>
\t\t</div>
\t";
        }
        // line 100
        echo "</div>

<div class=\"content-tabs\" ";
        // line 102
        if (isset($context["is_default"])) { $_is_default_ = $context["is_default"]; } else { $_is_default_ = null; }
        if ($_is_default_) {
            echo "style=\"display: none;\"";
        }
        echo ">
\t<nav>
\t\t<span class=\"saving-text\" id=\"saving_text\" style=\"display: none\">";
        // line 104
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.saving");
        echo "</span>
\t\t<ul>
\t\t\t<li ";
        // line 106
        if (isset($context["section"])) { $_section_ = $context["section"]; } else { $_section_ = null; }
        if (($_section_ == "create")) {
            echo "class=\"on\"";
        }
        echo ">
\t\t\t\t<div ";
        // line 107
        if (isset($context["custom_sections"])) { $_custom_sections_ = $context["custom_sections"]; } else { $_custom_sections_ = null; }
        if (twig_in_filter("create", $_custom_sections_)) {
            echo "class=\"orange-pip\"";
        }
        echo ">
\t\t\t\t\t<a href=\"";
        // line 108
        if (isset($context["department"])) { $_department_ = $context["department"]; } else { $_department_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_chat_editor_dep", array("department_id" => (($this->getAttribute($_department_, "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_department_, "id"), "0")) : ("0")))), "html", null, true);
        echo "\">Layout</a>
\t\t\t\t</div>
\t\t\t</li>
\t\t</ul>
\t\t<br />
\t</nav>

\t<section ";
        // line 115
        if (isset($context["is_default"])) { $_is_default_ = $context["is_default"]; } else { $_is_default_ = null; }
        if ($_is_default_) {
            echo "style=\"display: none\"";
        }
        echo ">
\t\t<form
\t\t\taction=\"";
        // line 117
        if (isset($context["department"])) { $_department_ = $context["department"]; } else { $_department_ = null; }
        if (isset($context["section"])) { $_section_ = $context["section"]; } else { $_section_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_chat_editor_dep_save", array("department_id" => (($this->getAttribute($_department_, "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_department_, "id"), 0)) : (0)), "section" => $_section_)), "html", null, true);
        echo "\"
\t\t\tmethod=\"post\"
\t\t\tclass=\"dp-page-box\"
\t\t\tid=\"admin_chat_editor\"
\t\t\tdata-element-handler=\"DeskPRO.Admin.ElementHandler.ChatEditor\"
\t\t\tdata-section=\"";
        // line 122
        if (isset($context["section"])) { $_section_ = $context["section"]; } else { $_section_ = null; }
        echo twig_escape_filter($this->env, $_section_, "html", null, true);
        echo "\"
\t\t>
\t\t<ul id=\"admin_chat_editor_items\" class=\"dp-form\">
\t\t\t<li class=\"no-items-notice\">
\t\t\t\t";
        // line 126
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.drag_drop_fields");
        echo "
\t\t\t</li>
\t\t</ul>
\t\t</form>
\t</section>
</div>

";
        // line 133
        if (isset($context["is_default"])) { $_is_default_ = $context["is_default"]; } else { $_is_default_ = null; }
        if ($_is_default_) {
            echo "<br/>";
        }
        // line 134
        echo "
";
        // line 138
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
\t\t\t\t<h4>Field Options</h4>
\t\t\t</div>
\t\t\t<div class=\"overlay-content\">
\t\t\t\t<div class=\"dp-form\">
\t\t\t\t\t<div class=\"choices-container\"></div>
\t\t\t\t\t<div class=\"dp-form-row\">
\t\t\t\t\t\t<div class=\"dp-form-label\">
\t\t\t\t\t\t\t<label>
\t\t\t\t\t\t\t\t<input type=\"checkbox\" class=\"agent_only\" name=\"agent_only\" value=\"1\" />
\t\t\t\t\t\t\t\tOnly show this field for agents
\t\t\t\t\t\t\t</label>
\t\t\t\t\t\t\t<br />
\t\t\t\t\t\t\t<br />
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</li>
</script>

";
        // line 181
        echo "<script type=\"text/x-deskpro-plain\" id=\"rendered_field_person_name\">
\t<div class=\"person_name\">
\t\t<input type=\"text\" />
\t</div>
</script>

";
        // line 190
        echo "<script type=\"text/x-deskpro-plain\" id=\"rendered_field_person_email\">
\t<div class=\"person_email\">
\t\t<input type=\"text\" />
\t</div>
</script>

";
        // line 199
        echo "<script type=\"text/x-deskpro-plain\" id=\"rendered_field_chat_department\">
\t<div class=\"subject\">
\t\t<select>
\t\t\t<option>Choose a department...</option>
\t\t\t";
        // line 203
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "departments"), "getPersonDepartments", array(0 => $this->getAttribute($_app_, "user"), 1 => "chat"), "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["dep"]) {
            // line 204
            echo "\t\t\t\t";
            if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
            if (twig_length_filter($this->env, $this->getAttribute($_dep_, "children"))) {
                // line 205
                echo "\t\t\t\t\t<optgroup label=\"";
                if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                echo "\">
\t\t\t\t\t\t";
                // line 206
                if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($_dep_, "children"));
                foreach ($context['_seq'] as $context["_key"] => $context["sub"]) {
                    // line 207
                    echo "\t\t\t\t\t\t\t<option>";
                    if (isset($context["sub"])) { $_sub_ = $context["sub"]; } else { $_sub_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_sub_, "title"), "html", null, true);
                    echo "</option>
\t\t\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['sub'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 209
                echo "\t\t\t\t\t</optgroup>
\t\t\t\t";
            } else {
                // line 211
                echo "\t\t\t\t\t<option>";
                if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                echo "</option>
\t\t\t\t";
            }
            // line 213
            echo "\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['dep'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 214
        echo "\t\t</select>
\t</div>
</script>

";
        // line 221
        echo "
";
        // line 222
        if (isset($context["custom_chat_fields"])) { $_custom_chat_fields_ = $context["custom_chat_fields"]; } else { $_custom_chat_fields_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_custom_chat_fields_);
        foreach ($context['_seq'] as $context["_key"] => $context["field"]) {
            // line 223
            echo "\t<script type=\"text/x-deskpro-plain\" id=\"rendered_field_chat_field_";
            if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_field_, "id"), "html", null, true);
            echo "\">
\t\t<div class=\"chat-field field-value\">
\t\t\t";
            // line 225
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
        // line 229
        echo "
";
    }

    public function getTemplateName()
    {
        return "AdminBundle:Chat:editor.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  479 => 199,  589 => 171,  457 => 133,  413 => 133,  953 => 406,  948 => 403,  935 => 394,  929 => 391,  916 => 382,  864 => 365,  844 => 348,  816 => 342,  807 => 339,  801 => 338,  774 => 331,  766 => 328,  737 => 314,  685 => 300,  664 => 294,  635 => 288,  593 => 269,  546 => 223,  532 => 214,  865 => 221,  852 => 216,  838 => 208,  820 => 201,  781 => 333,  764 => 178,  725 => 164,  632 => 141,  602 => 136,  565 => 117,  529 => 111,  505 => 207,  487 => 104,  473 => 102,  1853 => 842,  1849 => 841,  1839 => 837,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 813,  1778 => 812,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 782,  1705 => 781,  1688 => 770,  1681 => 769,  1677 => 768,  1667 => 764,  1656 => 762,  1648 => 757,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 714,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 695,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 651,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 610,  1289 => 607,  1276 => 604,  1263 => 601,  1244 => 596,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 565,  1181 => 561,  1171 => 557,  1158 => 550,  1114 => 530,  1104 => 526,  1072 => 512,  1062 => 508,  1024 => 494,  1014 => 490,  1000 => 482,  990 => 478,  980 => 474,  960 => 466,  918 => 448,  888 => 376,  834 => 346,  673 => 342,  636 => 323,  462 => 209,  454 => 204,  1144 => 542,  1139 => 399,  1131 => 399,  1127 => 397,  1110 => 396,  1092 => 391,  1089 => 390,  1086 => 520,  1084 => 388,  1063 => 387,  1060 => 386,  1055 => 385,  1050 => 384,  1035 => 372,  1019 => 267,  1003 => 263,  959 => 251,  900 => 377,  880 => 434,  870 => 430,  867 => 366,  859 => 362,  848 => 215,  839 => 197,  828 => 191,  823 => 188,  809 => 181,  800 => 178,  797 => 177,  794 => 176,  786 => 174,  740 => 162,  734 => 313,  703 => 354,  693 => 350,  630 => 412,  626 => 140,  614 => 138,  610 => 385,  581 => 124,  564 => 229,  525 => 236,  722 => 162,  697 => 256,  674 => 249,  671 => 425,  577 => 261,  569 => 164,  557 => 368,  502 => 267,  497 => 194,  445 => 95,  729 => 159,  684 => 261,  676 => 297,  669 => 254,  660 => 145,  647 => 243,  643 => 244,  601 => 175,  570 => 211,  522 => 200,  501 => 148,  296 => 149,  374 => 137,  631 => 239,  616 => 281,  608 => 137,  605 => 16,  596 => 15,  574 => 165,  561 => 209,  527 => 147,  433 => 93,  388 => 110,  426 => 177,  383 => 146,  461 => 167,  370 => 113,  395 => 122,  294 => 76,  223 => 132,  220 => 59,  492 => 263,  468 => 121,  444 => 131,  410 => 229,  397 => 117,  377 => 115,  262 => 115,  250 => 147,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 2108,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 1383,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 1303,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 1173,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 1041,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 970,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 538,  1126 => 916,  1106 => 900,  1100 => 897,  1026 => 841,  997 => 822,  983 => 816,  975 => 402,  956 => 800,  939 => 786,  902 => 753,  894 => 749,  879 => 373,  757 => 631,  727 => 608,  716 => 605,  670 => 296,  528 => 357,  476 => 140,  435 => 195,  354 => 110,  341 => 278,  192 => 72,  321 => 163,  243 => 87,  793 => 351,  780 => 348,  758 => 177,  700 => 154,  686 => 150,  652 => 274,  638 => 414,  620 => 139,  545 => 218,  523 => 110,  494 => 205,  459 => 99,  438 => 172,  351 => 79,  347 => 104,  402 => 157,  268 => 69,  430 => 237,  411 => 120,  379 => 84,  322 => 90,  315 => 110,  289 => 67,  284 => 128,  255 => 24,  234 => 136,  1133 => 400,  1124 => 534,  1121 => 56,  1116 => 55,  1113 => 403,  1108 => 51,  1103 => 394,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 500,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 329,  996 => 262,  989 => 316,  985 => 315,  981 => 257,  977 => 313,  970 => 91,  966 => 416,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 244,  919 => 290,  917 => 289,  908 => 444,  905 => 378,  896 => 280,  891 => 227,  877 => 270,  862 => 267,  857 => 265,  837 => 347,  832 => 250,  827 => 248,  821 => 247,  803 => 179,  778 => 389,  769 => 165,  765 => 386,  753 => 325,  746 => 175,  743 => 318,  735 => 170,  730 => 218,  720 => 363,  717 => 362,  712 => 156,  691 => 204,  678 => 149,  654 => 144,  587 => 14,  576 => 179,  539 => 241,  517 => 109,  471 => 190,  441 => 199,  437 => 142,  418 => 134,  386 => 295,  373 => 83,  304 => 151,  270 => 123,  265 => 63,  229 => 91,  477 => 138,  455 => 325,  448 => 164,  429 => 159,  407 => 119,  399 => 156,  389 => 174,  375 => 167,  358 => 286,  349 => 118,  335 => 100,  327 => 93,  298 => 84,  280 => 85,  249 => 147,  194 => 65,  142 => 44,  344 => 83,  318 => 135,  306 => 87,  295 => 68,  357 => 119,  300 => 150,  286 => 80,  276 => 87,  269 => 66,  254 => 120,  128 => 34,  237 => 138,  165 => 53,  122 => 26,  798 => 337,  770 => 179,  759 => 112,  748 => 337,  731 => 108,  721 => 308,  718 => 307,  708 => 271,  696 => 147,  617 => 234,  590 => 226,  553 => 225,  550 => 156,  540 => 84,  533 => 82,  500 => 206,  493 => 217,  489 => 343,  482 => 213,  467 => 210,  464 => 120,  458 => 166,  452 => 117,  449 => 132,  415 => 190,  382 => 219,  372 => 215,  361 => 81,  356 => 122,  339 => 102,  302 => 131,  285 => 77,  258 => 90,  123 => 20,  108 => 45,  424 => 156,  394 => 86,  380 => 80,  338 => 155,  319 => 72,  316 => 91,  312 => 87,  290 => 146,  267 => 122,  206 => 55,  110 => 33,  240 => 68,  224 => 60,  219 => 94,  217 => 83,  202 => 52,  186 => 57,  170 => 82,  100 => 41,  67 => 19,  14 => 1,  1096 => 524,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 836,  1013 => 275,  1007 => 274,  1002 => 823,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 402,  940 => 298,  937 => 395,  928 => 452,  926 => 243,  915 => 239,  912 => 236,  903 => 231,  898 => 440,  892 => 229,  889 => 748,  887 => 227,  884 => 374,  876 => 222,  874 => 215,  871 => 368,  863 => 214,  861 => 213,  858 => 212,  850 => 352,  843 => 206,  840 => 406,  815 => 245,  812 => 244,  808 => 199,  804 => 395,  799 => 196,  791 => 193,  785 => 238,  775 => 184,  771 => 183,  754 => 340,  728 => 309,  726 => 169,  723 => 168,  715 => 105,  711 => 152,  709 => 162,  706 => 155,  698 => 208,  694 => 153,  692 => 155,  689 => 254,  681 => 150,  677 => 149,  675 => 148,  663 => 251,  661 => 277,  650 => 246,  646 => 143,  629 => 129,  627 => 21,  625 => 236,  622 => 126,  598 => 174,  592 => 117,  586 => 170,  575 => 214,  566 => 163,  556 => 157,  554 => 289,  541 => 222,  536 => 205,  515 => 209,  511 => 108,  509 => 149,  488 => 126,  486 => 342,  483 => 341,  465 => 137,  463 => 181,  450 => 202,  432 => 314,  419 => 155,  371 => 165,  362 => 100,  353 => 80,  337 => 18,  333 => 122,  309 => 94,  303 => 86,  299 => 130,  291 => 111,  272 => 82,  261 => 156,  253 => 89,  239 => 102,  235 => 65,  213 => 91,  200 => 43,  198 => 75,  159 => 51,  149 => 79,  146 => 39,  131 => 21,  116 => 32,  79 => 32,  74 => 22,  71 => 22,  836 => 320,  817 => 398,  814 => 200,  811 => 317,  805 => 313,  787 => 657,  779 => 169,  776 => 305,  773 => 347,  761 => 296,  751 => 163,  747 => 293,  742 => 336,  739 => 171,  736 => 287,  724 => 214,  705 => 278,  702 => 601,  688 => 113,  680 => 263,  667 => 295,  662 => 146,  656 => 418,  649 => 291,  644 => 97,  641 => 241,  624 => 236,  613 => 280,  607 => 232,  597 => 221,  591 => 131,  584 => 262,  579 => 234,  563 => 162,  559 => 116,  551 => 366,  547 => 114,  537 => 153,  524 => 191,  512 => 351,  507 => 76,  504 => 143,  498 => 142,  485 => 203,  480 => 142,  472 => 139,  466 => 330,  460 => 328,  447 => 201,  442 => 162,  434 => 110,  428 => 29,  422 => 124,  404 => 184,  368 => 164,  364 => 127,  340 => 189,  334 => 130,  330 => 97,  325 => 73,  292 => 83,  287 => 162,  282 => 119,  279 => 78,  273 => 103,  266 => 106,  256 => 71,  252 => 107,  228 => 113,  218 => 81,  201 => 38,  64 => 17,  51 => 10,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 1705,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 737,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 731,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 725,  1559 => 601,  1552 => 720,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 708,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 645,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 624,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 1001,  1257 => 1000,  1254 => 479,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 569,  1196 => 958,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 938,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 388,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 898,  1099 => 393,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 880,  1076 => 359,  1070 => 875,  1057 => 352,  1052 => 504,  1045 => 382,  1040 => 377,  1036 => 283,  1032 => 496,  1028 => 274,  1023 => 393,  1020 => 392,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 821,  979 => 379,  974 => 256,  967 => 373,  962 => 803,  958 => 801,  954 => 253,  950 => 368,  945 => 367,  942 => 460,  938 => 365,  934 => 364,  927 => 361,  923 => 387,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 272,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 410,  841 => 210,  835 => 312,  830 => 249,  826 => 202,  822 => 308,  818 => 246,  813 => 183,  810 => 340,  806 => 180,  802 => 198,  795 => 336,  792 => 239,  789 => 350,  784 => 188,  782 => 187,  777 => 291,  772 => 289,  768 => 345,  763 => 327,  760 => 326,  756 => 230,  752 => 176,  745 => 281,  741 => 280,  738 => 379,  732 => 171,  719 => 279,  714 => 264,  710 => 310,  704 => 267,  699 => 303,  695 => 264,  690 => 263,  687 => 203,  683 => 346,  679 => 298,  672 => 255,  668 => 247,  665 => 201,  658 => 244,  645 => 270,  640 => 142,  634 => 413,  628 => 286,  623 => 238,  619 => 282,  611 => 18,  606 => 279,  603 => 176,  599 => 242,  595 => 132,  583 => 169,  580 => 168,  573 => 260,  560 => 101,  543 => 172,  538 => 221,  534 => 281,  530 => 202,  526 => 213,  521 => 146,  518 => 235,  514 => 183,  510 => 202,  503 => 75,  496 => 345,  490 => 204,  484 => 143,  474 => 336,  470 => 168,  446 => 318,  440 => 114,  436 => 113,  431 => 126,  425 => 193,  416 => 104,  412 => 98,  408 => 185,  403 => 126,  400 => 225,  396 => 299,  392 => 152,  385 => 117,  381 => 170,  367 => 82,  363 => 139,  359 => 107,  355 => 285,  350 => 94,  346 => 156,  343 => 115,  328 => 17,  324 => 164,  313 => 71,  307 => 70,  301 => 69,  288 => 88,  283 => 66,  271 => 64,  257 => 76,  251 => 76,  238 => 92,  233 => 100,  195 => 37,  191 => 58,  187 => 48,  183 => 56,  130 => 47,  88 => 21,  76 => 31,  115 => 33,  95 => 20,  655 => 275,  651 => 232,  648 => 231,  637 => 241,  633 => 130,  621 => 408,  618 => 233,  615 => 232,  604 => 186,  600 => 516,  588 => 305,  585 => 204,  582 => 203,  571 => 118,  567 => 372,  555 => 207,  552 => 190,  549 => 208,  544 => 285,  542 => 207,  535 => 112,  531 => 358,  519 => 211,  516 => 275,  513 => 168,  508 => 145,  506 => 83,  499 => 106,  495 => 147,  491 => 145,  481 => 103,  478 => 172,  475 => 184,  469 => 182,  456 => 136,  451 => 96,  443 => 132,  439 => 129,  427 => 125,  423 => 109,  420 => 192,  409 => 89,  405 => 148,  401 => 118,  391 => 86,  387 => 129,  384 => 132,  378 => 138,  365 => 289,  360 => 158,  348 => 21,  336 => 92,  332 => 107,  329 => 152,  323 => 96,  310 => 133,  305 => 165,  277 => 65,  274 => 151,  263 => 147,  259 => 62,  247 => 88,  244 => 62,  241 => 59,  222 => 90,  210 => 85,  207 => 80,  204 => 82,  184 => 66,  181 => 69,  167 => 59,  157 => 43,  96 => 27,  421 => 138,  417 => 137,  414 => 152,  406 => 171,  398 => 129,  393 => 175,  390 => 221,  376 => 110,  369 => 94,  366 => 108,  352 => 106,  345 => 78,  342 => 109,  331 => 91,  326 => 102,  320 => 149,  317 => 90,  314 => 147,  311 => 78,  308 => 144,  297 => 94,  293 => 92,  281 => 107,  278 => 125,  275 => 34,  264 => 31,  260 => 73,  248 => 144,  245 => 104,  242 => 118,  231 => 57,  227 => 87,  215 => 92,  212 => 82,  209 => 74,  197 => 77,  177 => 68,  171 => 77,  161 => 52,  132 => 39,  121 => 22,  105 => 31,  99 => 30,  81 => 29,  77 => 9,  180 => 70,  176 => 33,  156 => 38,  143 => 56,  139 => 44,  118 => 17,  189 => 73,  185 => 71,  173 => 66,  166 => 64,  152 => 48,  174 => 54,  164 => 41,  154 => 27,  150 => 101,  137 => 42,  133 => 41,  127 => 38,  107 => 28,  102 => 30,  83 => 29,  78 => 19,  53 => 10,  23 => 6,  42 => 9,  138 => 49,  134 => 42,  109 => 24,  103 => 42,  97 => 15,  94 => 40,  84 => 29,  75 => 16,  69 => 26,  66 => 7,  54 => 11,  44 => 7,  230 => 60,  226 => 63,  203 => 128,  193 => 49,  188 => 67,  182 => 70,  178 => 65,  168 => 32,  163 => 63,  160 => 38,  155 => 58,  148 => 55,  145 => 47,  140 => 50,  136 => 38,  125 => 43,  120 => 35,  113 => 32,  101 => 22,  92 => 23,  89 => 25,  85 => 27,  73 => 15,  62 => 21,  59 => 11,  56 => 15,  41 => 8,  126 => 37,  119 => 32,  111 => 31,  106 => 43,  98 => 21,  93 => 41,  86 => 24,  70 => 17,  60 => 14,  28 => 1,  36 => 5,  114 => 46,  104 => 23,  91 => 13,  80 => 26,  63 => 18,  58 => 14,  40 => 10,  34 => 4,  45 => 4,  61 => 13,  55 => 12,  48 => 14,  39 => 5,  35 => 4,  31 => 3,  26 => 2,  21 => 1,  46 => 8,  29 => 3,  57 => 10,  50 => 10,  47 => 8,  38 => 5,  33 => 4,  49 => 10,  32 => 3,  246 => 97,  236 => 86,  232 => 135,  225 => 59,  221 => 85,  216 => 58,  214 => 82,  211 => 60,  208 => 129,  205 => 81,  199 => 79,  196 => 78,  190 => 75,  179 => 68,  175 => 46,  172 => 65,  169 => 45,  162 => 72,  158 => 28,  153 => 52,  151 => 56,  147 => 100,  144 => 37,  141 => 37,  135 => 70,  129 => 38,  124 => 38,  117 => 39,  112 => 27,  90 => 29,  87 => 30,  82 => 29,  72 => 20,  68 => 18,  65 => 17,  52 => 13,  43 => 6,  37 => 5,  30 => 3,  27 => 1,  25 => 7,  24 => 4,  22 => 2,  19 => 1,);
    }
}