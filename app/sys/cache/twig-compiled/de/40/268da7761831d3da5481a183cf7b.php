<?php

/* AdminBundle:Templates:macros.html.twig */
class __TwigTemplate_de40268da7761831d3da5481a183cf7b extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 23
        echo "
";
        // line 34
        echo "
";
        // line 65
        echo "
";
        // line 114
        echo "
";
        // line 140
        echo "
";
        // line 192
        echo "
";
        // line 220
        echo "
";
    }

    // line 1
    public function getrender_dir($_bundle_name = null, $_dirname = null, $_dirinfo = null, $_options = null)
    {
        $context = $this->env->mergeGlobals(array(
            "bundle_name" => $_bundle_name,
            "dirname" => $_dirname,
            "dirinfo" => $_dirinfo,
            "options" => $_options,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 2
            echo "<div class=\"content-table template-dir\">
\t<table width=\"100%\">
\t\t<thead class=\"";
            // line 4
            if (isset($context["dirinfo"])) { $_dirinfo_ = $context["dirinfo"]; } else { $_dirinfo_ = null; }
            if ($this->getAttribute($_dirinfo_, "count_outdated")) {
                echo "dir-outdated";
            }
            echo " ";
            if (isset($context["dirinfo"])) { $_dirinfo_ = $context["dirinfo"]; } else { $_dirinfo_ = null; }
            if ($this->getAttribute($_dirinfo_, "count_changed")) {
                echo "dir-changed";
            }
            echo "\">
\t\t<tr>
\t\t\t<th class=\"single-title\" colspan=\"10\">
\t\t\t\t<span class=\"count-outdated\">";
            // line 7
            if (isset($context["dirinfo"])) { $_dirinfo_ = $context["dirinfo"]; } else { $_dirinfo_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.templates.count_outdated", array("count" => $this->getAttribute($_dirinfo_, "count_outdated")));
            echo "</span>
\t\t\t\t<span class=\"count-changed\">";
            // line 8
            if (isset($context["dirinfo"])) { $_dirinfo_ = $context["dirinfo"]; } else { $_dirinfo_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.templates.count_custom", array("count" => $this->getAttribute($_dirinfo_, "count_changed")));
            echo "</span>
\t\t\t\t<span class=\"count\">";
            // line 9
            if (isset($context["dirinfo"])) { $_dirinfo_ = $context["dirinfo"]; } else { $_dirinfo_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.templates.count_template", array("count" => twig_length_filter($this->env, $this->getAttribute($_dirinfo_, "templates"))));
            echo "</span>
\t\t\t\t<h1 class=\"toggle-section ";
            // line 10
            if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
            if ($this->getAttribute($_options_, "open")) {
                echo "expanded";
            }
            echo "\">";
            if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
            if ($this->getAttribute($_options_, "title")) {
                if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_options_, "title"), "html", null, true);
            } else {
                if (isset($context["dirname"])) { $_dirname_ = $context["dirname"]; } else { $_dirname_ = null; }
                if (($_dirname_ != "!top")) {
                    if (isset($context["dirname"])) { $_dirname_ = $context["dirname"]; } else { $_dirname_ = null; }
                    echo twig_escape_filter($this->env, $_dirname_, "html", null, true);
                } else {
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.templates.layout");
                }
            }
            echo "</h1>
\t\t\t</th>
\t\t</tr>
\t\t</thead>
\t\t<tbody ";
            // line 14
            if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
            if ((!$this->getAttribute($_options_, "open"))) {
                echo "style=\"display: none\"";
            }
            echo ">
\t\t";
            // line 15
            if (isset($context["dirinfo"])) { $_dirinfo_ = $context["dirinfo"]; } else { $_dirinfo_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_dirinfo_, "templates"));
            foreach ($context['_seq'] as $context["name"] => $context["info"]) {
                // line 16
                echo "\t\t";
                if (isset($context["name"])) { $_name_ = $context["name"]; } else { $_name_ = null; }
                if (isset($context["info"])) { $_info_ = $context["info"]; } else { $_info_ = null; }
                echo $this->getAttribute($this, "render_tpl", array(0 => $_name_, 1 => $_info_), "method");
                echo "
\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['name'], $context['info'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 18
            echo "\t\t</tbody>
\t</table>
</div>
<br />
";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 24
    public function getrender_tpl($_name = null, $_info = null)
    {
        $context = $this->env->mergeGlobals(array(
            "name" => $_name,
            "info" => $_info,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 25
            echo "<tr id=\"row_";
            if (isset($context["name"])) { $_name_ = $context["name"]; } else { $_name_ = null; }
            echo twig_escape_filter($this->env, strtr($this->env->getExtension('deskpro_templating')->strLower($_name_), array(":" => "_", "." => "_")), "html", null, true);
            echo "\" class=\"template-row ";
            if (isset($context["info"])) { $_info_ = $context["info"]; } else { $_info_ = null; }
            if ($this->getAttribute($_info_, "is_custom")) {
                echo "tpl-changed";
            }
            echo " ";
            if (isset($context["info"])) { $_info_ = $context["info"]; } else { $_info_ = null; }
            if ($this->getAttribute($_info_, "is_outdated")) {
                echo "tpl-outdated";
            }
            echo "\" data-template-name=\"";
            if (isset($context["name"])) { $_name_ = $context["name"]; } else { $_name_ = null; }
            echo twig_escape_filter($this->env, $_name_, "html", null, true);
            echo "\">
\t<td>
\t\t<em class=\"edit edit-tpl-trigger\"></em>
\t\t<span class=\"changed-tag\">";
            // line 28
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.templates.custom");
            echo "</span>
\t\t<span class=\"outdated-tag\">";
            // line 29
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.templates.outdated");
            echo "</span>
\t\t<h3>";
            // line 30
            if (isset($context["info"])) { $_info_ = $context["info"]; } else { $_info_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_info_, "shortname"), "html", null, true);
            echo "</h3>
\t</td>
</tr>
";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 35
    public function getrender_editor_overlay($_id = null, $_with_subject = null)
    {
        $context = $this->env->mergeGlobals(array(
            "id" => $_id,
            "with_subject" => $_with_subject,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 36
            echo "\t<div id=\"";
            if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
            echo twig_escape_filter($this->env, ((array_key_exists("id", $context)) ? (_twig_default_filter($_id_, "edittpl_overlay")) : ("edittpl_overlay")), "html", null, true);
            echo "\" class=\"deskpro-overlay-outer\" style=\"display: none;\">
\t\t<div class=\"deskpro-overlay\">
\t\t\t<div class=\"overlay-title\">
\t\t\t\t<span class=\"close-overlay\"></span>
\t\t\t\t<h4 class=\"template-title\"></h4>
\t\t\t</div>
\t\t\t<div class=\"overlay-content\">
\t\t\t\t";
            // line 43
            if (isset($context["with_subject"])) { $_with_subject_ = $context["with_subject"]; } else { $_with_subject_ = null; }
            if ($_with_subject_) {
                // line 44
                echo "\t\t\t\t\t<div class=\"subject-field\" style=\"display: none;\">
\t\t\t\t\t\t<strong>Subject:</strong><br />
\t\t\t\t\t\t<textarea class=\"template-subject-code\" spellcheck=\"false\" style=\"height: 85px;\"></textarea>
\t\t\t\t\t\t<br />
\t\t\t\t\t\t<strong>Email Content:</strong>
\t\t\t\t\t</div>
\t\t\t\t";
            }
            // line 51
            echo "\t\t\t\t<textarea class=\"template-code\" spellcheck=\"false\"></textarea>
\t\t\t</div>
\t\t\t<div class=\"overlay-footer\">
\t\t\t\t<div class=\"is-not-loading\">
\t\t\t\t\t<button class=\"clean-white save-trigger\">";
            // line 55
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.save_changes");
            echo "</button>
\t\t\t\t\t&nbsp;&nbsp; <button class=\"clean-white revert-trigger\">";
            // line 56
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.templates.revert_to_default");
            echo "</button>
\t\t\t\t</div>
\t\t\t\t<div class=\"is-loading\">
\t\t\t\t\t<div class=\"loading-icon-flat\"></div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>
";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 66
    public function getemail_template_row($_title = null, $_template_name = null, $_options = null)
    {
        $context = $this->env->mergeGlobals(array(
            "title" => $_title,
            "template_name" => $_template_name,
            "options" => $_options,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 67
            echo "\t<div class=\"content-table template-dir template-editor-row\" style=\"margin-bottom: 6px;\">
\t\t<table width=\"100%\" class=\"simple\">
\t\t\t<thead>
\t\t\t\t<tr>
\t\t\t\t\t<th class=\"single-title\" colspan=\"10\">
\t\t\t\t\t\t<h1 class=\"toggle-section\">";
            // line 72
            if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
            echo twig_escape_filter($this->env, $_title_, "html", null, true);
            echo " <i class=\"flat-spinner\" style=\"display: none;\"></i></h1>
\t\t\t\t\t</th>
\t\t\t\t</tr>
\t\t\t</thead>
\t\t\t<tbody style=\"display: none;\">
\t\t\t\t<tr>
\t\t\t\t\t<td>
\t\t\t\t\t\t<div class=\"template-edit-row\">
\t\t\t\t\t\t\t<div class=\"template-editor-wrap\">
\t\t\t\t\t\t\t\t<div class=\"template-toolbar\">
\t\t\t\t\t\t\t\t\t<ul>
\t\t\t\t\t\t\t\t\t\t<li class=\"new-phrase\"><span>Add Phrase</span></li>
\t\t\t\t\t\t\t\t\t\t<li class=\"phrase-editor\"><span>Phrase Editor</span></li>
\t\t\t\t\t\t\t\t\t\t<li class=\"view-default\" style=\"";
            // line 85
            if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
            if ((!$this->getAttribute($_options_, "default_template"))) {
                echo "display:none;";
            }
            echo "\"><span>View Default</span></li>
\t\t\t\t\t\t\t\t\t\t<li class=\"reset-default\" style=\"";
            // line 86
            if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
            if ((!$this->getAttribute($_options_, "default_template"))) {
                echo "display:none;";
            }
            echo "\"><span>Reset To Default</span></li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t<textarea class=\"template-editor template\" data-template-name=\"";
            // line 89
            if (isset($context["template_name"])) { $_template_name_ = $context["template_name"]; } else { $_template_name_ = null; }
            echo twig_escape_filter($this->env, $_template_name_, "html", null, true);
            echo "\"></textarea>
\t\t\t\t\t\t\t\t<textarea class=\"body-default-code\" style=\"display: none;\"></textarea>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t</tbody>
\t\t\t<tfoot style=\"display: none\" class=\"save-control\">
\t\t\t\t<tr>
\t\t\t\t\t<td>
\t\t\t\t\t\t<div class=\"is-not-loading\">
\t\t\t\t\t\t\t<button class=\"clean-white save-trigger\">Save</button>
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"is-loading\" style=\"text-align: center\"><i class=\"flat-spinner\"></i></div>
\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t</tfoot>
\t\t</table>

\t\t";
            // line 108
            echo $this->getAttribute($this, "render_editor_overlay", array(), "method");
            echo "
\t\t";
            // line 109
            echo $this->getAttribute($this, "render_addphrase_overlay", array(), "method");
            echo "
\t\t";
            // line 110
            echo $this->getAttribute($this, "render_addvariation_overlay", array(), "method");
            echo "
\t\t";
            // line 111
            echo $this->getAttribute($this, "render_lang_overlay", array(), "method");
            echo "
\t</div>
";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 115
    public function getrender_addphrase_overlay($_id = null)
    {
        $context = $this->env->mergeGlobals(array(
            "id" => $_id,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 116
            echo "\t<div id=\"";
            if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
            echo twig_escape_filter($this->env, ((array_key_exists("id", $context)) ? (_twig_default_filter($_id_, "add_phrase_overlay")) : ("add_phrase_overlay")), "html", null, true);
            echo "\" class=\"edit-phrase-overlay\" style=\"width: 725px; height: 260px; display: none;\">
\t\t<div class=\"overlay-title\">
\t\t\t<span class=\"close-trigger close-overlay\"></span>
\t\t\t<h4>Add Phrase</h4>
\t\t</div>
\t\t<div class=\"overlay-content\">
\t\t\t<section class=\"phrase-section custom\">
\t\t\t\t<header>
\t\t\t\t\t<h4>Unique Phrase ID</h4>
\t\t\t\t</header>
\t\t\t\t<article>custom.<input type=\"text\" class=\"phrase_id\" name=\"phrase_id\" style=\"width: 200px;\" /></article>
\t\t\t</section>
\t\t\t<section class=\"phrase-section custom\">
\t\t\t\t<header>
\t\t\t\t\t<h4>";
            // line 130
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.languages.your_phrase");
            echo "</h4>
\t\t\t\t</header>
\t\t\t\t<article><textarea class=\"custom_phrase\" name=\"custom_phrase\"></textarea></article>
\t\t\t</section>
\t\t</div>
\t\t<div class=\"overlay-footer\">
\t\t\t<button class=\"clean-white save-trigger\" data-add-url=\"";
            // line 136
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_langs_newphrase", array("language_id" => 1)), "html", null, true);
            echo "\">Save Phrase</button>
\t\t</div>
\t</div>
";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 141
    public function getrender_emailist_row($_tpl = null, $_variations = null, $_trigger_map = null)
    {
        $context = $this->env->mergeGlobals(array(
            "tpl" => $_tpl,
            "variations" => $_variations,
            "trigger_map" => $_trigger_map,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 142
            echo "\t";
            if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
            $context["phrase_key"] = ("admin.emailtpl_desc." . strtr($this->env->getExtension('deskpro_templating')->strLower($_tpl_), array("." => "_", ":" => "_")));
            // line 143
            echo "\t<tr class=\"";
            if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
            echo twig_escape_filter($this->env, strtr($this->env->getExtension('deskpro_templating')->strLower($_tpl_), array("." => "_", ":" => "_")), "html", null, true);
            echo "\" data-template-name=\"";
            if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
            echo twig_escape_filter($this->env, $_tpl_, "html", null, true);
            echo "\">
\t\t<td>
\t\t\t<strong><a class=\"editable-link\" href=\"";
            // line 145
            if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_templates_editemail", array("name" => $_tpl_)), "html", null, true);
            echo "\"><span>
\t\t\t\t";
            // line 146
            if (isset($context["phrase_key"])) { $_phrase_key_ = $context["phrase_key"]; } else { $_phrase_key_ = null; }
            if ($this->env->getExtension('deskpro_templating')->hasPhrase(($_phrase_key_ . "_title"))) {
                // line 147
                echo "\t\t\t\t\t";
                if (isset($context["phrase_key"])) { $_phrase_key_ = $context["phrase_key"]; } else { $_phrase_key_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, ($_phrase_key_ . "_title"));
                echo "
\t\t\t\t";
            } else {
                // line 149
                echo "\t\t\t\t\t";
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                echo twig_escape_filter($this->env, strtr($_tpl_, array("DeskPRO:emails_user:" => "", ".html.twig" => "")), "html", null, true);
                echo "
\t\t\t\t";
            }
            // line 151
            echo "\t\t\t</span></a></strong>";
            // line 152
            if (isset($context["trigger_map"])) { $_trigger_map_ = $context["trigger_map"]; } else { $_trigger_map_ = null; }
            if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
            if ($this->getAttribute($_trigger_map_, $_tpl_, array(), "array")) {
                // line 153
                echo "<span class=\"small-secondary-links\">
\t\t\t\t\t(Used by triggers:&nbsp;";
                // line 155
                if (isset($context["trigger_map"])) { $_trigger_map_ = $context["trigger_map"]; } else { $_trigger_map_ = null; }
                if (isset($context["tpl"])) { $_tpl_ = $context["tpl"]; } else { $_tpl_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($_trigger_map_, $_tpl_, array(), "array"));
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
                foreach ($context['_seq'] as $context["_key"] => $context["tr"]) {
                    // line 156
                    echo "<a href=\"";
                    if (isset($context["tr"])) { $_tr_ = $context["tr"]; } else { $_tr_ = null; }
                    echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_tickettriggers_edit", array("id" => $this->getAttribute($_tr_, "id"))), "html", null, true);
                    echo "\">";
                    if (isset($context["tr"])) { $_tr_ = $context["tr"]; } else { $_tr_ = null; }
                    echo twig_escape_filter($this->env, (($this->getAttribute($_tr_, "title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_tr_, "title"), ("Trigger #" . $this->getAttribute($_tr_, "id")))) : (("Trigger #" . $this->getAttribute($_tr_, "id")))), "html", null, true);
                    echo "</a>";
                    if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
                    if ((!$this->getAttribute($_loop_, "last"))) {
                        echo ", ";
                    }
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
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tr'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 158
                echo ")
\t\t\t\t</span>";
            }
            // line 161
            echo "<div class=\"tpl-desc\">
\t\t\t\t";
            // line 162
            if (isset($context["phrase_key"])) { $_phrase_key_ = $context["phrase_key"]; } else { $_phrase_key_ = null; }
            if ($this->env->getExtension('deskpro_templating')->hasPhrase(($_phrase_key_ . "_desc"))) {
                // line 163
                echo "\t\t\t\t\t";
                if (isset($context["phrase_key"])) { $_phrase_key_ = $context["phrase_key"]; } else { $_phrase_key_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, ($_phrase_key_ . "_desc"));
                echo "
\t\t\t\t";
            }
            // line 165
            echo "\t\t\t\t";
            if (isset($context["variations"])) { $_variations_ = $context["variations"]; } else { $_variations_ = null; }
            if ($_variations_) {
                // line 166
                echo "\t\t\t\t\t<div style=\"margin-top: 5px; font-size: 11px;\">
\t\t\t\t\t\tVariations:
\t\t\t\t\t\t<ul class=\"small-detail-list\">
\t\t\t\t\t\t\t";
                // line 169
                if (isset($context["variations"])) { $_variations_ = $context["variations"]; } else { $_variations_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($_variations_);
                foreach ($context['_seq'] as $context["_key"] => $context["var_tpl"]) {
                    // line 170
                    echo "\t\t\t\t\t\t\t\t<li>
\t\t\t\t\t\t\t\t\t<a class=\"editable-link\" href=\"";
                    // line 171
                    if (isset($context["var_tpl"])) { $_var_tpl_ = $context["var_tpl"]; } else { $_var_tpl_ = null; }
                    echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_templates_editemail", array("name" => $this->getAttribute($_var_tpl_, "name"))), "html", null, true);
                    echo "\"><span>
\t\t\t\t\t\t\t\t\t\t";
                    // line 172
                    if (isset($context["var_tpl"])) { $_var_tpl_ = $context["var_tpl"]; } else { $_var_tpl_ = null; }
                    echo twig_escape_filter($this->env, strtr($this->getAttribute($_var_tpl_, "name"), array("DeskPRO:emails_user:" => "", "DeskPRO:emails_agent:" => "", ".html.twig" => "")), "html", null, true);
                    echo "
\t\t\t\t\t\t\t\t\t</span></a>";
                    // line 174
                    if (isset($context["trigger_map"])) { $_trigger_map_ = $context["trigger_map"]; } else { $_trigger_map_ = null; }
                    if (isset($context["var_tpl"])) { $_var_tpl_ = $context["var_tpl"]; } else { $_var_tpl_ = null; }
                    if ($this->getAttribute($_trigger_map_, $this->getAttribute($_var_tpl_, "name"), array(), "array")) {
                        // line 175
                        echo "<span class=\"small-secondary-links\">
\t\t\t\t\t\t\t\t\t\t\t(Used by triggers:&nbsp;";
                        // line 177
                        if (isset($context["trigger_map"])) { $_trigger_map_ = $context["trigger_map"]; } else { $_trigger_map_ = null; }
                        if (isset($context["var_tpl"])) { $_var_tpl_ = $context["var_tpl"]; } else { $_var_tpl_ = null; }
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_trigger_map_, $this->getAttribute($_var_tpl_, "name"), array(), "array"));
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
                        foreach ($context['_seq'] as $context["_key"] => $context["tr"]) {
                            // line 178
                            echo "<a href=\"";
                            if (isset($context["tr"])) { $_tr_ = $context["tr"]; } else { $_tr_ = null; }
                            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_tickettriggers_edit", array("id" => $this->getAttribute($_tr_, "id"))), "html", null, true);
                            echo "\">";
                            if (isset($context["tr"])) { $_tr_ = $context["tr"]; } else { $_tr_ = null; }
                            echo twig_escape_filter($this->env, (($this->getAttribute($_tr_, "title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_tr_, "title"), ("Trigger #" . $this->getAttribute($_tr_, "id")))) : (("Trigger #" . $this->getAttribute($_tr_, "id")))), "html", null, true);
                            echo "</a>";
                            if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
                            if ((!$this->getAttribute($_loop_, "last"))) {
                                echo ", ";
                            }
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
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tr'], $context['_parent'], $context['loop']);
                        $context = array_merge($_parent, array_intersect_key($context, $_parent));
                        // line 180
                        echo ")
\t\t\t\t\t\t\t\t\t\t</span>";
                    }
                    // line 183
                    echo "</li>
\t\t\t\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['var_tpl'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 185
                echo "\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t";
            }
            // line 188
            echo "\t\t\t</div>
\t\t</td>
\t</tr>
";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 193
    public function getrender_addvariation_overlay()
    {
        $context = $this->env->getGlobals();

        $blocks = array();

        ob_start();
        try {
            // line 194
            echo "\t<div class=\"add-variation-overlay edit-phrase-overlay\" style=\"width: 725px; height: 200px; display: none;\">
\t\t<div class=\"overlay-title\">
\t\t\t<span class=\"close-trigger close-overlay\"></span>
\t\t\t<h4>Add Template Variation</h4>
\t\t</div>
\t\t<div class=\"overlay-content\">
\t\t\t<section class=\"phrase-section custom\">

\t\t\t\tYou can create mutliple variations of an email and use triggers to change the template
\t\t\t\tyou want to send to users based on any criteria you want.
\t\t\t\t<br />
\t\t\t\t<br />

\t\t\t\t<header>
\t\t\t\t\t<h4>Unique Template ID</h4>
\t\t\t\t</header>
\t\t\t\t<article>
\t\t\t\t\tcustom_<input type=\"text\" class=\"template_name\" name=\"template_name\" placeholder=\"Enter a unique ID that will be used to identify this template\" style=\"width: 350px;\" />
\t\t\t\t</article>
\t\t\t</section>
\t\t</div>
\t\t<div class=\"overlay-footer\">
\t\t\t<button class=\"clean-white save-trigger\">Create Variation</button>
\t\t</div>
\t</div>
";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 221
    public function getrender_lang_overlay()
    {
        $context = $this->env->getGlobals();

        $blocks = array();

        ob_start();
        try {
            // line 222
            echo "\t<div class=\"lang-overlay edit-phrase-overlay\" style=\"width: 850px; height: 685px; display: none;\">
\t\t<div class=\"overlay-title\">
\t\t\t<span class=\"close-trigger close-overlay\"></span>
\t\t\t<div class=\"lang_overlay_nav\"></div>
\t\t</div>
\t\t<div class=\"overlay-content\">
\t\t\t<div class=\"lang_overlay_content\"></div>
\t\t</div>
\t</div>
";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    public function getTemplateName()
    {
        return "AdminBundle:Templates:macros.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  933 => 149,  914 => 133,  909 => 132,  833 => 359,  783 => 332,  755 => 320,  666 => 300,  453 => 203,  639 => 110,  568 => 254,  520 => 249,  657 => 287,  572 => 251,  609 => 459,  20 => 1,  659 => 274,  562 => 170,  548 => 165,  558 => 244,  479 => 151,  589 => 100,  457 => 145,  413 => 172,  953 => 406,  948 => 403,  935 => 394,  929 => 391,  916 => 382,  864 => 365,  844 => 348,  816 => 342,  807 => 339,  801 => 338,  774 => 337,  766 => 328,  737 => 318,  685 => 293,  664 => 194,  635 => 281,  593 => 445,  546 => 414,  532 => 240,  865 => 221,  852 => 216,  838 => 208,  820 => 201,  781 => 333,  764 => 321,  725 => 164,  632 => 283,  602 => 265,  565 => 171,  529 => 282,  505 => 267,  487 => 213,  473 => 221,  1853 => 842,  1849 => 841,  1839 => 837,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 813,  1778 => 812,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 782,  1705 => 781,  1688 => 770,  1681 => 769,  1677 => 768,  1667 => 764,  1656 => 762,  1648 => 757,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 714,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 695,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 651,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 610,  1289 => 607,  1276 => 604,  1263 => 601,  1244 => 596,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 565,  1181 => 561,  1171 => 557,  1158 => 550,  1114 => 530,  1104 => 526,  1072 => 512,  1062 => 508,  1024 => 494,  1014 => 490,  1000 => 482,  990 => 478,  980 => 474,  960 => 466,  918 => 448,  888 => 376,  834 => 346,  673 => 342,  636 => 185,  462 => 146,  454 => 103,  1144 => 542,  1139 => 399,  1131 => 399,  1127 => 397,  1110 => 396,  1092 => 391,  1089 => 390,  1086 => 520,  1084 => 388,  1063 => 387,  1060 => 386,  1055 => 385,  1050 => 384,  1035 => 372,  1019 => 267,  1003 => 263,  959 => 251,  900 => 377,  880 => 434,  870 => 430,  867 => 366,  859 => 362,  848 => 215,  839 => 362,  828 => 357,  823 => 188,  809 => 181,  800 => 178,  797 => 177,  794 => 336,  786 => 174,  740 => 162,  734 => 313,  703 => 354,  693 => 350,  630 => 278,  626 => 140,  614 => 275,  610 => 103,  581 => 247,  564 => 229,  525 => 235,  722 => 162,  697 => 256,  674 => 249,  671 => 425,  577 => 257,  569 => 97,  557 => 169,  502 => 229,  497 => 240,  445 => 197,  729 => 159,  684 => 261,  676 => 299,  669 => 254,  660 => 145,  647 => 286,  643 => 244,  601 => 178,  570 => 172,  522 => 200,  501 => 265,  296 => 67,  374 => 149,  631 => 111,  616 => 281,  608 => 266,  605 => 16,  596 => 102,  574 => 165,  561 => 209,  527 => 233,  433 => 93,  388 => 137,  426 => 177,  383 => 182,  461 => 246,  370 => 147,  395 => 221,  294 => 72,  223 => 78,  220 => 36,  492 => 395,  468 => 201,  444 => 193,  410 => 169,  397 => 174,  377 => 159,  262 => 113,  250 => 55,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 2108,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 1383,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 1303,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 1173,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 1041,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 970,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 538,  1126 => 916,  1106 => 900,  1100 => 897,  1026 => 841,  997 => 822,  983 => 816,  975 => 402,  956 => 800,  939 => 786,  902 => 130,  894 => 128,  879 => 373,  757 => 631,  727 => 316,  716 => 308,  670 => 297,  528 => 221,  476 => 253,  435 => 208,  354 => 109,  341 => 212,  192 => 30,  321 => 122,  243 => 54,  793 => 350,  780 => 348,  758 => 177,  700 => 221,  686 => 150,  652 => 274,  638 => 282,  620 => 139,  545 => 259,  523 => 110,  494 => 10,  459 => 226,  438 => 195,  351 => 135,  347 => 173,  402 => 222,  268 => 65,  430 => 141,  411 => 201,  379 => 219,  322 => 83,  315 => 118,  289 => 108,  284 => 102,  255 => 115,  234 => 81,  1133 => 400,  1124 => 534,  1121 => 56,  1116 => 55,  1113 => 403,  1108 => 51,  1103 => 394,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 500,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 329,  996 => 262,  989 => 316,  985 => 315,  981 => 257,  977 => 313,  970 => 91,  966 => 416,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 148,  919 => 290,  917 => 289,  908 => 444,  905 => 378,  896 => 280,  891 => 127,  877 => 270,  862 => 267,  857 => 265,  837 => 347,  832 => 250,  827 => 248,  821 => 247,  803 => 179,  778 => 389,  769 => 165,  765 => 386,  753 => 328,  746 => 319,  743 => 318,  735 => 170,  730 => 218,  720 => 363,  717 => 362,  712 => 156,  691 => 204,  678 => 149,  654 => 144,  587 => 14,  576 => 179,  539 => 241,  517 => 217,  471 => 212,  441 => 239,  437 => 101,  418 => 201,  386 => 164,  373 => 149,  304 => 114,  270 => 144,  265 => 163,  229 => 91,  477 => 138,  455 => 224,  448 => 242,  429 => 179,  407 => 119,  399 => 162,  389 => 176,  375 => 217,  358 => 110,  349 => 131,  335 => 139,  327 => 89,  298 => 144,  280 => 102,  249 => 205,  194 => 112,  142 => 49,  344 => 140,  318 => 86,  306 => 116,  295 => 74,  357 => 154,  300 => 112,  286 => 80,  276 => 105,  269 => 103,  254 => 56,  128 => 61,  237 => 118,  165 => 100,  122 => 33,  798 => 337,  770 => 179,  759 => 112,  748 => 337,  731 => 108,  721 => 305,  718 => 307,  708 => 295,  696 => 147,  617 => 461,  590 => 226,  553 => 264,  550 => 156,  540 => 84,  533 => 255,  500 => 397,  493 => 225,  489 => 257,  482 => 223,  467 => 210,  464 => 202,  458 => 220,  452 => 217,  449 => 132,  415 => 136,  382 => 162,  372 => 157,  361 => 155,  356 => 215,  339 => 132,  302 => 114,  285 => 105,  258 => 136,  123 => 44,  108 => 27,  424 => 149,  394 => 161,  380 => 151,  338 => 155,  319 => 118,  316 => 131,  312 => 87,  290 => 105,  267 => 96,  206 => 75,  110 => 36,  240 => 93,  224 => 119,  219 => 136,  217 => 81,  202 => 109,  186 => 70,  170 => 113,  100 => 21,  67 => 11,  14 => 1,  1096 => 524,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 836,  1013 => 275,  1007 => 274,  1002 => 823,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 402,  940 => 298,  937 => 395,  928 => 452,  926 => 243,  915 => 239,  912 => 236,  903 => 231,  898 => 440,  892 => 229,  889 => 748,  887 => 227,  884 => 374,  876 => 222,  874 => 215,  871 => 368,  863 => 214,  861 => 213,  858 => 212,  850 => 352,  843 => 206,  840 => 406,  815 => 245,  812 => 244,  808 => 199,  804 => 395,  799 => 196,  791 => 193,  785 => 238,  775 => 184,  771 => 183,  754 => 340,  728 => 309,  726 => 169,  723 => 168,  715 => 105,  711 => 152,  709 => 222,  706 => 302,  698 => 208,  694 => 308,  692 => 155,  689 => 302,  681 => 300,  677 => 149,  675 => 289,  663 => 276,  661 => 277,  650 => 246,  646 => 112,  629 => 183,  627 => 269,  625 => 180,  622 => 270,  598 => 174,  592 => 117,  586 => 264,  575 => 174,  566 => 246,  556 => 244,  554 => 240,  541 => 163,  536 => 241,  515 => 209,  511 => 269,  509 => 244,  488 => 155,  486 => 220,  483 => 341,  465 => 147,  463 => 216,  450 => 194,  432 => 211,  419 => 100,  371 => 182,  362 => 111,  353 => 141,  337 => 140,  333 => 131,  309 => 190,  303 => 70,  299 => 148,  291 => 176,  272 => 93,  261 => 91,  253 => 161,  239 => 88,  235 => 44,  213 => 139,  200 => 45,  198 => 85,  159 => 104,  149 => 44,  146 => 43,  131 => 36,  116 => 50,  79 => 8,  74 => 7,  71 => 24,  836 => 320,  817 => 398,  814 => 200,  811 => 317,  805 => 313,  787 => 333,  779 => 169,  776 => 305,  773 => 347,  761 => 296,  751 => 163,  747 => 325,  742 => 336,  739 => 171,  736 => 317,  724 => 214,  705 => 278,  702 => 601,  688 => 113,  680 => 278,  667 => 296,  662 => 293,  656 => 418,  649 => 285,  644 => 284,  641 => 188,  624 => 109,  613 => 261,  607 => 273,  597 => 270,  591 => 267,  584 => 259,  579 => 175,  563 => 96,  559 => 245,  551 => 243,  547 => 242,  537 => 90,  524 => 220,  512 => 227,  507 => 156,  504 => 213,  498 => 213,  485 => 153,  480 => 254,  472 => 149,  466 => 210,  460 => 221,  447 => 143,  442 => 196,  434 => 212,  428 => 185,  422 => 176,  404 => 149,  368 => 81,  364 => 156,  340 => 170,  334 => 129,  330 => 148,  325 => 134,  292 => 112,  287 => 67,  282 => 104,  279 => 111,  273 => 170,  266 => 102,  256 => 135,  252 => 109,  228 => 79,  218 => 77,  201 => 117,  64 => 13,  51 => 9,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 1705,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 737,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 731,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 725,  1559 => 601,  1552 => 720,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 708,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 645,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 624,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 1001,  1257 => 1000,  1254 => 479,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 569,  1196 => 958,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 938,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 388,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 898,  1099 => 393,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 880,  1076 => 359,  1070 => 875,  1057 => 352,  1052 => 504,  1045 => 382,  1040 => 377,  1036 => 283,  1032 => 496,  1028 => 274,  1023 => 393,  1020 => 392,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 821,  979 => 379,  974 => 256,  967 => 373,  962 => 803,  958 => 801,  954 => 253,  950 => 153,  945 => 152,  942 => 460,  938 => 150,  934 => 364,  927 => 147,  923 => 387,  920 => 359,  910 => 353,  901 => 347,  897 => 129,  890 => 343,  886 => 50,  883 => 272,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 410,  841 => 210,  835 => 312,  830 => 249,  826 => 202,  822 => 354,  818 => 246,  813 => 183,  810 => 345,  806 => 180,  802 => 198,  795 => 336,  792 => 239,  789 => 349,  784 => 188,  782 => 187,  777 => 291,  772 => 289,  768 => 334,  763 => 327,  760 => 329,  756 => 230,  752 => 176,  745 => 281,  741 => 280,  738 => 379,  732 => 171,  719 => 279,  714 => 264,  710 => 310,  704 => 267,  699 => 303,  695 => 297,  690 => 263,  687 => 279,  683 => 346,  679 => 298,  672 => 255,  668 => 247,  665 => 285,  658 => 244,  645 => 277,  640 => 285,  634 => 413,  628 => 466,  623 => 107,  619 => 298,  611 => 268,  606 => 279,  603 => 267,  599 => 249,  595 => 132,  583 => 263,  580 => 99,  573 => 274,  560 => 268,  543 => 172,  538 => 162,  534 => 233,  530 => 202,  526 => 229,  521 => 226,  518 => 233,  514 => 221,  510 => 202,  503 => 266,  496 => 226,  490 => 214,  484 => 394,  474 => 202,  470 => 231,  446 => 241,  440 => 102,  436 => 183,  431 => 186,  425 => 193,  416 => 231,  412 => 230,  408 => 141,  403 => 194,  400 => 225,  396 => 93,  392 => 139,  385 => 152,  381 => 185,  367 => 147,  363 => 164,  359 => 136,  355 => 326,  350 => 108,  346 => 140,  343 => 134,  328 => 135,  324 => 125,  313 => 81,  307 => 108,  301 => 124,  288 => 116,  283 => 167,  271 => 160,  257 => 94,  251 => 58,  238 => 53,  233 => 81,  195 => 121,  191 => 104,  187 => 100,  183 => 69,  130 => 47,  88 => 18,  76 => 30,  115 => 28,  95 => 35,  655 => 193,  651 => 232,  648 => 231,  637 => 273,  633 => 272,  621 => 462,  618 => 277,  615 => 268,  604 => 186,  600 => 271,  588 => 305,  585 => 261,  582 => 177,  571 => 242,  567 => 95,  555 => 250,  552 => 166,  549 => 208,  544 => 230,  542 => 290,  535 => 161,  531 => 158,  519 => 87,  516 => 248,  513 => 216,  508 => 230,  506 => 217,  499 => 241,  495 => 239,  491 => 145,  481 => 152,  478 => 235,  475 => 184,  469 => 203,  456 => 204,  451 => 243,  443 => 142,  439 => 129,  427 => 177,  423 => 187,  420 => 208,  409 => 179,  405 => 94,  401 => 148,  391 => 173,  387 => 116,  384 => 160,  378 => 91,  365 => 145,  360 => 89,  348 => 191,  336 => 132,  332 => 150,  329 => 127,  323 => 135,  310 => 114,  305 => 112,  277 => 170,  274 => 66,  263 => 64,  259 => 158,  247 => 160,  244 => 51,  241 => 91,  222 => 105,  210 => 60,  207 => 110,  204 => 89,  184 => 28,  181 => 110,  167 => 50,  157 => 94,  96 => 46,  421 => 147,  417 => 71,  414 => 142,  406 => 130,  398 => 165,  393 => 177,  390 => 153,  376 => 115,  369 => 90,  366 => 174,  352 => 140,  345 => 213,  342 => 87,  331 => 128,  326 => 102,  320 => 121,  317 => 82,  314 => 136,  311 => 85,  308 => 116,  297 => 111,  293 => 119,  281 => 106,  278 => 71,  275 => 107,  264 => 103,  260 => 107,  248 => 75,  245 => 90,  242 => 89,  231 => 52,  227 => 78,  215 => 88,  212 => 111,  209 => 89,  197 => 129,  177 => 93,  171 => 57,  161 => 42,  132 => 86,  121 => 77,  105 => 34,  99 => 47,  81 => 31,  77 => 26,  180 => 54,  176 => 109,  156 => 89,  143 => 87,  139 => 40,  118 => 46,  189 => 88,  185 => 46,  173 => 54,  166 => 51,  152 => 54,  174 => 55,  164 => 113,  154 => 46,  150 => 89,  137 => 87,  133 => 38,  127 => 36,  107 => 50,  102 => 41,  83 => 23,  78 => 30,  53 => 10,  23 => 6,  42 => 1,  138 => 86,  134 => 44,  109 => 40,  103 => 42,  97 => 32,  94 => 42,  84 => 9,  75 => 25,  69 => 15,  66 => 14,  54 => 7,  44 => 8,  230 => 74,  226 => 141,  203 => 126,  193 => 58,  188 => 29,  182 => 59,  178 => 66,  168 => 62,  163 => 25,  160 => 68,  155 => 55,  148 => 66,  145 => 47,  140 => 62,  136 => 18,  125 => 16,  120 => 15,  113 => 14,  101 => 33,  92 => 26,  89 => 10,  85 => 23,  73 => 16,  62 => 15,  59 => 12,  56 => 2,  41 => 5,  126 => 76,  119 => 65,  111 => 28,  106 => 37,  98 => 36,  93 => 31,  86 => 27,  70 => 11,  60 => 4,  28 => 114,  36 => 85,  114 => 37,  104 => 37,  91 => 41,  80 => 27,  63 => 13,  58 => 11,  40 => 6,  34 => 192,  45 => 9,  61 => 12,  55 => 13,  48 => 10,  39 => 7,  35 => 4,  31 => 140,  26 => 1,  21 => 2,  46 => 7,  29 => 2,  57 => 8,  50 => 5,  47 => 9,  38 => 5,  33 => 13,  49 => 19,  32 => 3,  246 => 131,  236 => 87,  232 => 43,  225 => 82,  221 => 76,  216 => 65,  214 => 135,  211 => 111,  208 => 35,  205 => 87,  199 => 74,  196 => 73,  190 => 101,  179 => 94,  175 => 114,  172 => 52,  169 => 75,  162 => 80,  158 => 58,  153 => 46,  151 => 24,  147 => 50,  144 => 51,  141 => 55,  135 => 59,  129 => 35,  124 => 70,  117 => 32,  112 => 40,  90 => 22,  87 => 20,  82 => 20,  72 => 30,  68 => 22,  65 => 20,  52 => 10,  43 => 3,  37 => 220,  30 => 1,  27 => 1,  25 => 65,  24 => 3,  22 => 34,  19 => 23,);
    }
}
