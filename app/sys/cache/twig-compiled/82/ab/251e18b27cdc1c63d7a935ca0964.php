<?php

/* AgentBundle:Kb:view.html.twig */
class __TwigTemplate_82ab251e18b27cdc1c63d7a935ca0964 extends \Application\DeskPRO\Twig\Template
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
        // line 1
        $context["optionbox"] = $this->env->loadTemplate("AgentBundle:Common:optionbox-macros.html.twig");
        // line 2
        echo "<script>
pageMeta.fragmentClass = 'DeskPRO.Agent.PageFragment.Page.KbViewArticle';
pageMeta.title = ";
        // line 4
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        echo twig_jsonencode_filter($this->getAttribute($_article_, "title"));
        echo ";
pageMeta.article_id = ";
        // line 5
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_article_, "id"), "html", null, true);
        echo ";
pageMeta.url_fragment  = '";
        // line 6
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->urlFragment("agent_kb_article", array("article_id" => $this->getAttribute($_article_, "id"))), "html", null, true);
        echo "';
pageMeta.obj_code = 'article_";
        // line 7
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_article_, "id"), "html", null, true);
        echo "';

pageMeta.permalink = '";
        // line 9
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_article_, "permalink"), "html", null, true);
        echo "';
pageMeta.labelsSaveUrl = '";
        // line 10
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_kb_ajax_labels_save", array("article_id" => $this->getAttribute($_article_, "id"))), "html", null, true);
        echo "';

";
        // line 12
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        if (($this->getAttribute($_article_, "hidden_status") == "validating")) {
            // line 13
            echo "pageMeta.isValidating = true;
";
        }
        // line 15
        echo "
pageMeta.glossaryWords = ";
        // line 16
        if (isset($context["glossary_words"])) { $_glossary_words_ = $context["glossary_words"]; } else { $_glossary_words_ = null; }
        echo twig_jsonencode_filter($_glossary_words_);
        echo ";

pageMeta.canEdit   = ";
        // line 18
        if (isset($context["perms"])) { $_perms_ = $context["perms"]; } else { $_perms_ = null; }
        if ((!$this->getAttribute($_perms_, "can_edit"))) {
            echo "false";
        } else {
            echo "true";
        }
        echo ";
pageMeta.canDelete = ";
        // line 19
        if (isset($context["perms"])) { $_perms_ = $context["perms"]; } else { $_perms_ = null; }
        if ((!$this->getAttribute($_perms_, "can_delete"))) {
            echo "false";
        } else {
            echo "true";
        }
        echo ";

";
        // line 21
        $context["baseId"] = $this->env->getExtension('deskpro_templating')->elUid();
        // line 22
        echo "pageMeta.baseId = '";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "';
</script>
";
        // line 25
        echo "<div class=\"layout-content with-scrollbar page-listing page-kb-view ";
        if (isset($context["perms"])) { $_perms_ = $context["perms"]; } else { $_perms_ = null; }
        if ((!$this->getAttribute($_perms_, "can_edit"))) {
            echo "perm-no-edit";
        }
        echo " ";
        if (isset($context["perms"])) { $_perms_ = $context["perms"]; } else { $_perms_ = null; }
        if ((!$this->getAttribute($_perms_, "can_delete"))) {
            echo "perm-no-delete";
        }
        echo "\">
<div class=\"scrollbar disable\"><div class=\"track\"><div class=\"thumb\"><div class=\"end\"></div></div></div></div>
<div class=\"scroll-viewport\"><div class=\"scroll-content\">
";
        // line 29
        echo "
<div class=\"delete-notice\" ";
        // line 30
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        if (($this->getAttribute($_article_, "status_code") != "hidden.deleted")) {
            echo "style=\"display: none;\"";
        }
        echo ">
\t";
        // line 31
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.record_deleted");
        echo " <button class=\"clean-gray xxx-small undelete\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.properties");
        echo "</button>
</div>

";
        // line 34
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        if (($this->getAttribute($_article_, "status_code") == "hidden.validating")) {
            // line 35
            echo "\t";
            $this->env->loadTemplate("AgentBundle:Publish:view-approve-header.html.twig")->display($context);
        }
        // line 37
        echo "
<header class=\"page-header\">
\t<h4 class=\"id-number\">#";
        // line 39
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_article_, "id"), "html", null, true);
        echo "</h4>
\t<div style=\"float:left; padding-left: 8px;\">
\t\t<select id=\"";
        // line 41
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_status\">
\t\t\t<option ";
        // line 42
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        if (($this->getAttribute($_article_, "status") == "published")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"published\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.status_published");
        echo "</option>
\t\t\t<option ";
        // line 43
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        if (($this->getAttribute($_article_, "getStatusCode") == "hidden.unpublished")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"hidden.unpublished\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.status_hidden_unpublished");
        echo "</option>
\t\t\t<option ";
        // line 44
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        if (($this->getAttribute($_article_, "status") == "archived")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"archived\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.status_archived");
        echo "</option>
\t\t\t";
        // line 45
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($this->getAttribute($this->getAttribute($_article_, "person"), "id") == $this->getAttribute($this->getAttribute($_app_, "user"), "id"))) {
            // line 46
            echo "\t\t\t\t<option value=\"hidden.draft\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.status_hidden_draft");
            echo "</option>
\t\t\t";
        }
        // line 48
        echo "\t\t</select>
\t</div>
\t<h1>
\t\t<span id=\"";
        // line 51
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_showname\">";
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_article_, "real_title"), "html", null, true);
        echo "</span>
\t\t<span id=\"";
        // line 52
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_editname\" style=\"display: none\"><input type=\"text\" name=\"name\" value=\"";
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_article_, "real_title"), "html", null, true);
        echo "\" /></span>
\t\t<span id=\"";
        // line 53
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_editname_start\" class=\"edit-name-gear\"></span>
\t\t<a id=\"";
        // line 54
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_editname_end\" class=\"edit-name-save clean-white\" style=\"display: none\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.done");
        echo "</a>
\t</h1>
\t<br class=\"clear\" />
\t<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"margin-top: 2px;\">
\t\t<tr>
\t\t\t<td width=\"100%\" valign=\"middle\" style=\"vertical-align: middle;\">
\t\t\t\t<ul id=\"";
        // line 60
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_categories\" class=\"add-remove-list flat with-select2\">
\t\t\t\t\t";
        // line 61
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_article_, "categories"));
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
        foreach ($context['_seq'] as $context["_key"] => $context["cat"]) {
            // line 62
            echo "\t\t\t\t\t\t<li>
\t\t\t\t\t\t\t<span class=\"remove noedit-hide\">";
            // line 63
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.remove");
            echo "</span>
\t\t\t\t\t\t\t";
            // line 64
            if (isset($context["article_categories"])) { $_article_categories_ = $context["article_categories"]; } else { $_article_categories_ = null; }
            if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
            $this->env->loadTemplate("AgentBundle:Common:select-standard.html.twig")->display(array_merge($context, array("name" => "category_ids[]", "add_classname" => "category_id", "add_attr" => "", "with_blank" => 0, "blank_title" => "", "categories" => $_article_categories_, "selected" => $this->getAttribute($_cat_, "id"), "allow_parent_sel" => true)));
            // line 74
            echo "\t\t\t\t\t\t</li>
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
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['cat'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 76
        echo "\t\t\t\t\t<li class=\"add noedit-hide\" id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_addcat_li\">
\t\t\t\t\t\t<span class=\"add\" id=\"";
        // line 77
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_addcat_trigger\"></span>
\t\t\t\t\t\t";
        // line 78
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.add_category");
        echo "
\t\t\t\t\t\t<script type=\"text/x-deskpro-plain\" id=\"";
        // line 79
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_addcat_select_tpl\">
\t\t\t\t\t\t\t<li>
\t\t\t\t\t\t\t\t<span class=\"remove noedit-hide\">";
        // line 81
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.remove");
        echo "</span>
\t\t\t\t\t\t\t\t";
        // line 82
        if (isset($context["article_categories"])) { $_article_categories_ = $context["article_categories"]; } else { $_article_categories_ = null; }
        $this->env->loadTemplate("AgentBundle:Common:select-standard.html.twig")->display(array_merge($context, array("name" => "category_ids[]", "add_classname" => "category_id", "add_attr" => "", "with_blank" => 0, "blank_title" => "", "categories" => $_article_categories_, "allow_parent_sel" => true, "selected" => 0)));
        // line 92
        echo "\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t</script>
\t\t\t\t\t</li>
\t\t\t\t</ul>
\t\t\t</td>
\t\t</tr>
\t</table>
\t<div class=\"labels-line\">
\t\t<input
\t\t\ttype=\"hidden\"
\t\t\tid=\"";
        // line 102
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_labels_input\"
\t\t\tclass=\"dpe_select dpe_select_noborder\"
\t\t\tdata-select-width=\"auto\"
\t\t\tdata-placeholder=\"Add a label\"
\t\t\tvalue=\"";
        // line 106
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_article_, "labels"));
        foreach ($context['_seq'] as $context["_key"] => $context["label"]) {
            if (isset($context["label"])) { $_label_ = $context["label"]; } else { $_label_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_label_, "label"), "html", null, true);
            echo ",";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['label'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        echo "\"
\t\t/>
\t</div>
\t<br class=\"clear\" />
</header>

<div class=\"profile-box-container reply-box-wrap\">
\t<header>
\t\t<nav  id=\"";
        // line 114
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_bodytabs\">
\t\t\t<div class=\"controls\" id=\"";
        // line 115
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_article_save\">
\t\t\t\t<span style=\"display:none\" class=\"mark-loading\"></span>
\t\t\t\t<span style=\"display:none\" class=\"mark-saved\">";
        // line 117
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.saved");
        echo "</span>
\t\t\t</div>
\t\t\t<ul>
\t\t\t\t<li class=\"on\" data-tab-for=\"#";
        // line 120
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_tab_contents .kb-content\"><span>";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.article");
        echo "</span></li>
\t\t\t\t";
        // line 121
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "languages"), "isMultiLang", array(), "method")) {
            // line 122
            echo "\t\t\t\t\t<li data-tab-for=\"#";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_tab_contents .kb-trans\"><span>";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.translations");
            echo "</span></li>
\t\t\t\t";
        }
        // line 124
        echo "\t\t\t\t<li data-tab-for=\"#";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_tab_contents .kb-props\"><span>";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.properties");
        echo "</span></li>
\t\t\t\t<li data-tab-for=\"#";
        // line 125
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_tab_contents .comments-tab\"><span>";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.comments");
        echo " (<span id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_count_comments\">";
        if (isset($context["article_comments"])) { $_article_comments_ = $context["article_comments"]; } else { $_article_comments_ = null; }
        echo twig_escape_filter($this->env, _twig_default_filter(twig_length_filter($this->env, $_article_comments_), 0), "html", null, true);
        echo "</span>)</span></li>
\t\t\t\t<li data-tab-for=\"#";
        // line 126
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_tab_contents .revisions-tab\"><span>";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.label_revisions");
        echo " (<span id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_count_revs\">";
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        echo twig_escape_filter($this->env, _twig_default_filter(twig_length_filter($this->env, $this->getAttribute($_article_, "revisions")), 0), "html", null, true);
        echo "</span>)</span></li>
\t\t\t\t<li data-tab-for=\"#";
        // line 127
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_tab_contents .related-content-tab\"><span>";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.related_content");
        echo " (<span id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_count_related\">0</span>)</span></li>
\t\t\t\t<li data-tab-for=\"#";
        // line 128
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_tab_contents .search-tab\"><span>";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.search");
        echo "</span></li>
\t\t\t</ul>
\t\t</nav>
\t</header>
\t<section>

\t\t";
        // line 135
        echo "\t\t<div id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_tab_contents\">
\t\t\t";
        // line 137
        echo "\t\t\t<div class=\"deskpro-tab-item kb-content on\" style=\"display: block\">
\t\t\t\t<nav class=\"sub-box-nav noedit-hide\">
\t\t\t\t\t<ul class=\"option-buttons right\" id=\"";
        // line 139
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_content_edit_btns\">
\t\t\t\t\t\t<li class=\"kb-editor-edit edit\" id=\"";
        // line 140
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_edit_btn\"><span>";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ucfirst_edit");
        echo "</span></li>
\t\t\t\t\t\t<li class=\"editor-save-trigger save\" id=\"";
        // line 141
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_save_btn\"><span>";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.save");
        echo "</span></li>
\t\t\t\t\t\t<li class=\"editor-cancel-trigger cancel\" id=\"";
        // line 142
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_cancel_btn\"><span>";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.cancel");
        echo "</span></li>
\t\t\t\t\t</ul>
\t\t\t\t</nav>
\t\t\t\t<div id=\"";
        // line 145
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_content_ed\" class=\"content-tab-item\">
\t\t\t\t";
        // line 146
        $this->env->loadTemplate("AgentBundle:Kb:view-content-tab.html.twig")->display($context);
        // line 147
        echo "\t\t\t\t</div>
\t\t\t</div>
\t\t\t";
        // line 150
        echo "
\t\t\t";
        // line 151
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "languages"), "isMultiLang", array(), "method")) {
            // line 152
            echo "\t\t\t\t<div class=\"deskpro-tab-item kb-trans\" style=\"display: none;\">
\t\t\t\t\t";
            // line 153
            $this->env->loadTemplate("AgentBundle:Kb:view-content-trans.html.twig")->display($context);
            // line 154
            echo "\t\t\t\t</div>
\t\t\t";
        }
        // line 156
        echo "
\t\t\t";
        // line 158
        echo "\t\t\t<div class=\"deskpro-tab-item kb-props\" style=\"display: none;\">
\t\t\t\t<section class=\"description-area\">
\t\t\t\t\t<dl class=\"info-list\">
\t\t\t\t\t\t";
        // line 161
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_product"), "method")) {
            // line 162
            echo "\t\t\t\t\t\t\t<dt class=\"\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.products");
            echo "</dt>
\t\t\t\t\t\t\t<dd>
\t\t\t\t\t\t\t\t<ul id=\"";
            // line 164
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_products\" class=\"add-remove-list with-select2\">
\t\t\t\t\t\t\t\t\t";
            // line 165
            if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_article_, "products"));
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
            foreach ($context['_seq'] as $context["_key"] => $context["prod"]) {
                // line 166
                echo "\t\t\t\t\t\t\t\t\t\t<li>
\t\t\t\t\t\t\t\t\t\t\t<span class=\"remove noedit-hide\">";
                // line 167
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.remove");
                echo "</span>
\t\t\t\t\t\t\t\t\t\t\t";
                // line 168
                if (isset($context["article_products"])) { $_article_products_ = $context["article_products"]; } else { $_article_products_ = null; }
                if (isset($context["prod"])) { $_prod_ = $context["prod"]; } else { $_prod_ = null; }
                $this->env->loadTemplate("AgentBundle:Common:select-standard.html.twig")->display(array_merge($context, array("name" => "product_ids[]", "add_classname" => "product_id", "add_attr" => "", "with_blank" => 0, "blank_title" => "", "categories" => $_article_products_, "selected" => $this->getAttribute($_prod_, "id"))));
                // line 177
                echo "\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t";
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
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['prod'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 179
            echo "\t\t\t\t\t\t\t\t\t<li class=\"add noedit-hide\" id=\"";
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_addprod_li\">
\t\t\t\t\t\t\t\t\t\t<span class=\"add\" id=\"";
            // line 180
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_addprod_trigger\"></span>
\t\t\t\t\t\t\t\t\t\t";
            // line 181
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.add_product");
            echo "
\t\t\t\t\t\t\t\t\t\t<script type=\"text/x-deskpro-plain\" id=\"";
            // line 182
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_addprod_select_tpl\">
\t\t\t\t\t\t\t\t\t\t\t<li>
\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"remove noedit-hide\">";
            // line 184
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.remove");
            echo "</span>
\t\t\t\t\t\t\t\t\t\t\t\t";
            // line 185
            if (isset($context["article_products"])) { $_article_products_ = $context["article_products"]; } else { $_article_products_ = null; }
            $this->env->loadTemplate("AgentBundle:Common:select-standard.html.twig")->display(array_merge($context, array("name" => "product_ids[]", "add_classname" => "product_id", "add_attr" => "", "with_blank" => 0, "blank_title" => "", "categories" => $_article_products_, "selected" => 0)));
            // line 194
            echo "\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t</script>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t\t</dd>
\t\t\t\t\t\t";
        }
        // line 200
        echo "
\t\t\t\t\t\t<dt>";
        // line 201
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.rating");
        echo "</dt>
\t\t\t\t\t\t<dd>
\t\t\t\t\t\t\t";
        // line 203
        ob_start();
        // line 204
        echo "\t\t\t\t\t\t\t<span class=\"link-look who-voted-trigger\">";
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.count_votecount_votes", array("count" => $this->getAttribute($_article_, "num_ratings")));
        echo "</span>
\t\t\t\t\t\t\t";
        $context["phrase_part"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 206
        echo "\t\t\t\t\t\t\t";
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        if (isset($context["phrase_part"])) { $_phrase_part_ = $context["phrase_part"]; } else { $_phrase_part_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.percent_helpful_subphrase", array("percent" => $this->getAttribute($_article_, "rating_percent"), "subphrase" => $_phrase_part_), true);
        echo "<br />
\t\t\t\t\t\t\t<small>";
        // line 207
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.found_article_helpful_or_not", array("up_votes" => $this->getAttribute($_article_, "up_votes"), "down_votes" => $this->getAttribute($_article_, "down_votes")));
        echo "</small>
\t\t\t\t\t\t</dd>

\t\t\t\t\t\t";
        // line 210
        if (isset($context["user_view_count"])) { $_user_view_count_ = $context["user_view_count"]; } else { $_user_view_count_ = null; }
        if ($_user_view_count_) {
            // line 211
            echo "\t\t\t\t\t\t\t<dt></dt>
\t\t\t\t\t\t\t<dd>
\t\t\t\t\t\t\t\t";
            // line 213
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.registered_views");
            echo ": <a href=\"";
            if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_publish_whoviewed", array("object_type" => 1, "object_id" => $this->getAttribute($_article_, "id"))), "html", null, true);
            echo "\" class=\"open-who-viewed\">";
            if (isset($context["user_view_count"])) { $_user_view_count_ = $context["user_view_count"]; } else { $_user_view_count_ = null; }
            echo twig_escape_filter($this->env, $_user_view_count_, "html", null, true);
            echo "</a>
\t\t\t\t\t\t\t</dd>
\t\t\t\t\t\t";
        }
        // line 216
        echo "
\t\t\t\t\t\t";
        // line 217
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        if (($this->getAttribute($_article_, "date_created") == $this->getAttribute($_article_, "date_published"))) {
            // line 218
            echo "\t\t\t\t\t\t\t<dt>";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.created_and_published");
            echo ":</dt>
\t\t\t\t\t\t\t<dd>
\t\t\t\t\t\t\t\t";
            // line 220
            ob_start();
            // line 221
            echo "\t\t\t\t\t\t\t\t\t<time class=\"timeago\" datetime=\"";
            if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_article_, "date_created"), "c", "UTC"), "html", null, true);
            echo "\"></time>
\t\t\t\t\t\t\t\t";
            $context["phrase_part1"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
            // line 223
            echo "\t\t\t\t\t\t\t\t";
            ob_start();
            // line 224
            echo "\t\t\t\t\t\t\t\t\t<time>";
            if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_article_, "date_created"), "fulltime"), "html", null, true);
            echo "</time>
\t\t\t\t\t\t\t\t";
            $context["phrase_part2"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
            // line 226
            echo "\t\t\t\t\t\t\t\t";
            if (isset($context["phrase_part1"])) { $_phrase_part1_ = $context["phrase_part1"]; } else { $_phrase_part1_ = null; }
            if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
            if (isset($context["phrase_part2"])) { $_phrase_part2_ = $context["phrase_part2"]; } else { $_phrase_part2_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.event_time_ago_by_who_at_when", array("ago" => $_phrase_part1_, "name" => $this->getAttribute($this->getAttribute($_article_, "person"), "display_name"), "date" => $_phrase_part2_), true);
            echo "
\t\t\t\t\t\t\t</dd>
\t\t\t\t\t\t";
        } else {
            // line 229
            echo "\t\t\t\t\t\t\t<dt>";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.date_created");
            echo ":</dt>
\t\t\t\t\t\t\t<dd>
\t\t\t\t\t\t\t\t";
            // line 231
            ob_start();
            // line 232
            echo "\t\t\t\t\t\t\t\t<time class=\"timeago\" datetime=\"";
            if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_article_, "date_created"), "c", "UTC"), "html", null, true);
            echo "\"></time>
\t\t\t\t\t\t\t\t";
            $context["phrase_part1"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
            // line 234
            echo "\t\t\t\t\t\t\t\t";
            ob_start();
            // line 235
            echo "\t\t\t\t\t\t\t\t<time>";
            if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_article_, "date_created"), "fulltime"), "html", null, true);
            echo "</time>
\t\t\t\t\t\t\t\t";
            $context["phrase_part2"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
            // line 237
            echo "\t\t\t\t\t\t\t\t";
            if (isset($context["phrase_part1"])) { $_phrase_part1_ = $context["phrase_part1"]; } else { $_phrase_part1_ = null; }
            if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
            if (isset($context["phrase_part2"])) { $_phrase_part2_ = $context["phrase_part2"]; } else { $_phrase_part2_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.event_time_ago_by_who_at_when", array("ago" => $_phrase_part1_, "name" => $this->getAttribute($this->getAttribute($_article_, "person"), "display_name"), "date" => $_phrase_part2_), true);
            echo "
\t\t\t\t\t\t\t</dd>

\t\t\t\t\t\t\t";
            // line 241
            echo "\t\t\t\t\t\t\t";
            if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
            if (($this->getAttribute($_article_, "date_published") && ($this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_article_, "date_published"), "U") < $this->env->getExtension('deskpro_templating')->userDate($context, "now", "U")))) {
                // line 242
                echo "\t\t\t\t\t\t\t\t<dt>";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.date_published");
                echo ":</dt>
\t\t\t\t\t\t\t\t<dd>
\t\t\t\t\t\t\t\t\t";
                // line 244
                ob_start();
                // line 245
                echo "\t\t\t\t\t\t\t\t\t<time class=\"timeago\" datetime=\"";
                if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_article_, "date_created"), "c", "UTC"), "html", null, true);
                echo "\"></time>
\t\t\t\t\t\t\t\t\t";
                $context["phrase_part1"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
                // line 247
                echo "\t\t\t\t\t\t\t\t\t";
                ob_start();
                // line 248
                echo "\t\t\t\t\t\t\t\t\t<time>";
                if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_article_, "date_created"), "fulltime"), "html", null, true);
                echo "</time>
\t\t\t\t\t\t\t\t\t";
                $context["phrase_part2"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
                // line 250
                echo "\t\t\t\t\t\t\t\t\t";
                if (isset($context["phrase_part1"])) { $_phrase_part1_ = $context["phrase_part1"]; } else { $_phrase_part1_ = null; }
                if (isset($context["phrase_part2"])) { $_phrase_part2_ = $context["phrase_part2"]; } else { $_phrase_part2_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.event_time_ago_at_when", array("ago" => $_phrase_part1_, "date" => $_phrase_part2_), true);
                echo "
\t\t\t\t\t\t\t\t</dd>
\t\t\t\t\t\t\t";
            }
            // line 253
            echo "\t\t\t\t\t\t";
        }
        // line 254
        echo "
\t\t\t\t\t\t<dd id=\"";
        // line 255
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_auto_unpub\" ";
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        if ((!($this->getAttribute($_article_, "status") == "published"))) {
            echo "style=\"display: none\"";
        }
        echo ">
\t\t\t\t\t\t\t<span class=\"auto-unpublish\" ";
        // line 256
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        if ((!$this->getAttribute($_article_, "date_end"))) {
            echo "style=\"display: none\"";
        }
        echo ">
\t\t\t\t\t\t\t\t";
        // line 257
        ob_start();
        echo "<span class=\"end-action opt\" data-val=\"";
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_article_, "end_action", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_article_, "end_action"), "unpublish")) : ("unpublish")), "html", null, true);
        echo "\">";
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_article_, "end_action", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_article_, "end_action"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.unpublish"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.unpublish"))), "html", null, true);
        echo "</span>";
        $context["phrase_part1"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 258
        echo "\t\t\t\t\t\t\t\t";
        ob_start();
        echo "<span class=\"end-date opt\" ";
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        if ($this->getAttribute($_article_, "date_end")) {
            echo "data-val=\"";
            if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_article_, "date_end"), "U"), "html", null, true);
            echo "\"";
        }
        echo "> ";
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        if ($this->getAttribute($_article_, "date_end")) {
            if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_article_, "date_end"), "day"), "html", null, true);
        } else {
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.click_to_set_date");
        }
        echo "</span>";
        $context["phrase_part2"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 259
        echo "\t\t\t\t\t\t\t\t";
        if (isset($context["phrase_part1"])) { $_phrase_part1_ = $context["phrase_part1"]; } else { $_phrase_part1_ = null; }
        if (isset($context["phrase_part2"])) { $_phrase_part2_ = $context["phrase_part2"]; } else { $_phrase_part2_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.knowledgebase.auto_action_on_date", array("action" => $_phrase_part1_, "date" => $_phrase_part2_), true);
        echo "
\t\t\t\t\t\t\t\t<input type=\"text\" class=\"end-date-input\" value=\"";
        // line 260
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_article_, "date_end"), "U"), "html", null, true);
        echo "\" style=\"visibility: none; width: 1px; height: 1px; overflow: hidden; border: 0; margin: 0; padding: 0;\" />
\t\t\t\t\t\t\t\t(<span class=\"remove-auto-unpublish options-text\">";
        // line 261
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.remove");
        echo "</span>)
\t\t\t\t\t\t\t</span>
\t\t\t\t\t\t\t<span class=\"options-text auto-unpublish-set\" ";
        // line 263
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        if ($this->getAttribute($_article_, "date_end")) {
            echo "style=\"display: none\"";
        }
        echo ">
\t\t\t\t\t\t\t\t";
        // line 264
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.set_auto_unpublish_date");
        echo "
\t\t\t\t\t\t\t</span>
\t\t\t\t\t\t\t<br />

\t\t\t\t\t\t\t<ul class=\"end-action-menu\" style=\"display: none\">
\t\t\t\t\t\t\t\t<li data-action=\"unpublish\">";
        // line 269
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.unpublish");
        echo "</li>
\t\t\t\t\t\t\t\t<li data-action=\"archive\">";
        // line 270
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.archive");
        echo "</li>
\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t</dd>

\t\t\t\t\t\t<dd id=\"";
        // line 274
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_auto_pub\" ";
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        if ((!($this->getAttribute($_article_, "hidden_status") == "unpublished"))) {
            echo "style=\"display: none\"";
        }
        echo ">
\t\t\t\t\t\t\t<span class=\"auto-publish\" ";
        // line 275
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        if ((!$this->getAttribute($_article_, "date_published"))) {
            echo "style=\"display: none\"";
        }
        echo ">
\t\t\t\t\t\t\t\t";
        // line 276
        ob_start();
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.publish");
        $context["phrase_part1"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 277
        echo "\t\t\t\t\t\t\t\t";
        ob_start();
        echo "<span class=\"pub-date opt\" ";
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        if ($this->getAttribute($_article_, "date_published")) {
            echo "data-val=\"";
            if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_article_, "date_published"), "U"), "html", null, true);
            echo "\"";
        }
        echo "> ";
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        if ($this->getAttribute($_article_, "date_published")) {
            if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_article_, "date_published"), "day"), "html", null, true);
        } else {
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.click_to_set_date");
        }
        echo "</span>";
        $context["phrase_part2"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 278
        echo "\t\t\t\t\t\t\t\t";
        if (isset($context["phrase_part1"])) { $_phrase_part1_ = $context["phrase_part1"]; } else { $_phrase_part1_ = null; }
        if (isset($context["phrase_part2"])) { $_phrase_part2_ = $context["phrase_part2"]; } else { $_phrase_part2_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.knowledgebase.auto_action_on_date", array("action" => $_phrase_part1_, "date" => $_phrase_part2_), true);
        echo "
\t\t\t\t\t\t\t\t<input type=\"text\" class=\"pub-date-input\" value=\"";
        // line 279
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_article_, "date_published"), "U"), "html", null, true);
        echo "\" style=\"visibility: none; width: 1px; height: 1px; overflow: hidden; border: 0; margin: 0; padding: 0;\" />
\t\t\t\t\t\t\t\t(<span class=\"remove-auto-publish options-text\">";
        // line 280
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.remove");
        echo "</span>)
\t\t\t\t\t\t\t</span>
\t\t\t\t\t\t\t<span class=\"options-text auto-publish-set\" ";
        // line 282
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        if ($this->getAttribute($_article_, "date_published")) {
            echo "style=\"display: none\"";
        }
        echo ">
\t\t\t\t\t\t\t\t";
        // line 283
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.set_auto_publish_date");
        echo "
\t\t\t\t\t\t\t</span>
\t\t\t\t\t\t</dd>
\t\t\t\t\t</dl>

\t\t\t\t\t<div class=\"controls\" id=\"";
        // line 288
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_action_buttons\">
\t\t\t\t\t\t<div class=\"showing-editing-fields\" style=\"display: none\">
\t\t\t\t\t\t\t<button class=\"clean-white save-fields-trigger\"><span>";
        // line 290
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.save");
        echo "</span></button>
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"showing-rendered-fields\">
\t\t\t\t\t\t\t";
        // line 293
        if (isset($context["custom_fields"])) { $_custom_fields_ = $context["custom_fields"]; } else { $_custom_fields_ = null; }
        if (twig_length_filter($this->env, $_custom_fields_)) {
            echo "<button class=\"clean-white edit-fields-trigger noedit-hide\"><span>";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.edit_properties");
            echo "</span></button>";
        }
        // line 294
        echo "\t\t\t\t\t\t\t<button class=\"clean-white delete nodelete-hide\" ";
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        if (($this->getAttribute($_article_, "status_code") == "hidden.deleted")) {
            echo "style=\"display:none\"";
        }
        echo ">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.delete");
        echo "</button>
\t\t\t\t\t\t\t<button class=\"clean-white permalink\">";
        // line 295
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.copy_permalink");
        echo "</button>
\t\t\t\t\t\t\t<button class=\"clean-white view-user-interface\">";
        // line 296
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.view_in_user_interface");
        echo "</button>
\t\t\t\t\t\t\t<a target=\"_blank\" href=\"";
        // line 297
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_kb_article", array("article_id" => $this->getAttribute($_article_, "id"), "pdf" => 1, "_rt" => $this->env->getExtension('deskpro_templating')->securityToken("request_token", 10800))), "html", null, true);
        echo "\"><button class=\"clean-white\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.download_pdf");
        echo "</button></a>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</section>
\t\t\t</div>
\t\t\t";
        // line 303
        echo "
\t\t\t";
        // line 305
        echo "\t\t\t<div class=\"deskpro-tab-item content-tab-item comments-tab\">
\t\t\t\t<div class=\"article-comments\">
\t\t\t\t\t<div class=\"full-deskpro-tab-item\">
\t\t\t\t\t\t<div class=\"messages-wrap\" id=\"";
        // line 308
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_comments_wrap\">
\t\t\t\t\t\t\t";
        // line 309
        if (isset($context["article_comments"])) { $_article_comments_ = $context["article_comments"]; } else { $_article_comments_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_article_comments_);
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
        foreach ($context['_seq'] as $context["_key"] => $context["comment"]) {
            // line 310
            echo "\t\t\t\t\t\t\t\t";
            if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
            $context["comment_num"] = $this->getAttribute($_loop_, "index");
            // line 311
            echo "\t\t\t\t\t\t\t\t";
            $this->env->loadTemplate("AgentBundle:Kb:view-comment.html.twig")->display($context);
            // line 312
            echo "\t\t\t\t\t\t\t";
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
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['comment'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 313
        echo "\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"messages-wrap new-note\">
\t\t\t\t\t\t\t<article
\t\t\t\t\t\t\t\tclass=\"content-message agent-message\"
\t\t\t\t\t\t\t\tdata-comment-id=\"";
        // line 317
        if (isset($context["comment"])) { $_comment_ = $context["comment"]; } else { $_comment_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_comment_, "id"), "html", null, true);
        echo "\"
\t\t\t\t\t\t\t\tdata-content-type=\"";
        // line 318
        if (isset($context["comment"])) { $_comment_ = $context["comment"]; } else { $_comment_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_comment_, "object_content_type"), "html", null, true);
        echo "\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t\t\t<div class=\"avatar\">
\t\t\t\t\t\t\t\t\t<img src=\"";
        // line 321
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "user"), "getPictureUrl", array(0 => 40), "method"), "html", null, true);
        echo "\" alt=\"\" width=\"40\" height=\"40\" />
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t<div class=\"content\">
\t\t\t\t\t\t\t\t\t<header>
\t\t\t\t\t\t\t\t\t\t<h4>";
        // line 325
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.new_comment");
        echo "</h4>
\t\t\t\t\t\t\t\t\t</header>
\t\t\t\t\t\t\t\t\t<div class=\"body-text\">
\t\t\t\t\t\t\t\t\t\t<textarea style=\"width:99%; height: 80px;\"></textarea>
\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t<div class=\"footer-text\">
\t\t\t\t\t\t\t\t\t\t<div class=\"loading-off\">
\t\t\t\t\t\t\t\t\t\t\t<button class=\"clean-white\">";
        // line 332
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.add_comment");
        echo "</button>
\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t<div class=\"loading-on\" style=\"display:none\">
\t\t\t\t\t\t\t\t\t\t\t";
        // line 335
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.saving");
        echo "
\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</article>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t\t";
        // line 345
        echo "
\t\t\t";
        // line 347
        echo "\t\t\t<div id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_revs\" class=\"loaded deskpro-tab-item content-tab-item revisions-tab\">
\t\t\t\t";
        // line 348
        $this->env->loadTemplate("AgentBundle:Kb:view-revisions-tab.html.twig")->display($context);
        // line 349
        echo "\t\t\t</div>
\t\t\t";
        // line 351
        echo "
\t\t\t";
        // line 353
        echo "\t\t\t<div id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_related_content\" class=\"deskpro-tab-item content-tab-item kb-related-content related-content-tab\">
\t\t\t\t";
        // line 354
        $this->env->loadTemplate("AgentBundle:Common:content-related-list.html.twig")->display($context);
        // line 355
        echo "\t\t\t</div>
\t\t\t";
        // line 357
        echo "
\t\t\t";
        // line 359
        echo "\t\t\t<div id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_searchtab\" class=\"deskpro-tab-item search-tab\">
\t\t\t\t<section class=\"description-area sticky-search-words\">
\t\t\t\t\t<dl class=\"info-list\">
\t\t\t\t\t\t<dt>";
        // line 362
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.sticky_words_title");
        echo "</dt>
\t\t\t\t\t\t<dd class=\"ticket-tags tags-wrap noedit-tags\">
\t\t\t\t\t\t\t<input type=\"text\" id=\"";
        // line 364
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_stickysearch_input\" value=\"";
        if (isset($context["sticky_search_words"])) { $_sticky_search_words_ = $context["sticky_search_words"]; } else { $_sticky_search_words_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_sticky_search_words_);
        foreach ($context['_seq'] as $context["_key"] => $context["label"]) {
            if (isset($context["label"])) { $_label_ = $context["label"]; } else { $_label_ = null; }
            echo twig_escape_filter($this->env, $_label_, "html", null, true);
            echo ",";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['label'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        echo "\" />
\t\t\t\t\t\t\t<br class=\"clear\" />
\t\t\t\t\t\t\t<div class=\"explain\">
\t\t\t\t\t\t\t\t";
        // line 367
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.search_sticky_words_explain");
        echo "
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</dd>
\t\t\t\t\t</dl>
\t\t\t\t</section>

\t\t\t\t<br />

\t\t\t\t";
        // line 375
        $this->env->loadTemplate("AgentBundle:Publish:rated-searches.html.twig")->display($context);
        // line 376
        echo "\t\t\t</div>
\t\t\t";
        // line 378
        echo "\t\t</div>
\t\t";
        // line 380
        echo "\t</section>
</div>

<div class=\"inline-editable\" style=\"display:none\">
\t<div class=\"title\">
\t\t<input type=\"text\" name=\"title\" value=\"";
        // line 385
        if (isset($context["article"])) { $_article_ = $context["article"]; } else { $_article_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_article_, "real_title"), "html", null, true);
        echo "\"/>
\t\t<input type=\"hidden\" name=\"action\" value=\"title\" />
\t</div>
</div>

";
        // line 407
        if (isset($context["optionbox"])) { $_optionbox_ = $context["optionbox"]; } else { $_optionbox_ = null; }
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo $_optionbox_->getstart($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.add_category"), array("id" => ($_baseId_ . "_cat_ob")));
        echo "
\t";
        // line 408
        if (isset($context["optionbox"])) { $_optionbox_ = $context["optionbox"]; } else { $_optionbox_ = null; }
        echo $_optionbox_->getsection_start("", "category");
        echo "
\t\t";
        // line 409
        if (isset($context["article_categories"])) { $_article_categories_ = $context["article_categories"]; } else { $_article_categories_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_article_categories_);
        foreach ($context['_seq'] as $context["_key"] => $context["cat"]) {
            // line 410
            echo "\t\t\t";
            if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
            echo $this->getAttribute($this, "category_row", array(0 => $_cat_, 1 => 0), "method");
            echo "
\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['cat'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 412
        echo "\t";
        if (isset($context["optionbox"])) { $_optionbox_ = $context["optionbox"]; } else { $_optionbox_ = null; }
        echo $_optionbox_->getsection_end();
        echo "
";
        // line 413
        if (isset($context["optionbox"])) { $_optionbox_ = $context["optionbox"]; } else { $_optionbox_ = null; }
        echo $_optionbox_->getend($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.add_category"));
        echo "

";
        // line 416
        echo "</div></div></div>
";
    }

    // line 390
    public function getcategory_row($_cat = null, $_depth = null)
    {
        $context = $this->env->mergeGlobals(array(
            "cat" => $_cat,
            "depth" => $_depth,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 391
            echo "\t<li
\t\tclass=\"";
            // line 392
            if (isset($context["depth"])) { $_depth_ = $context["depth"]; } else { $_depth_ = null; }
            if ((!$_depth_)) {
                echo "top";
            } else {
                echo "child";
            }
            echo " ";
            if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
            if ($this->getAttribute($_cat_, "children")) {
                echo "parent-option";
            }
            echo " item-";
            if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "id"), "html", null, true);
            echo " depth-";
            if (isset($context["depth"])) { $_depth_ = $context["depth"]; } else { $_depth_ = null; }
            echo twig_escape_filter($this->env, $_depth_, "html", null, true);
            echo "\"
\t\t";
            // line 393
            if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
            if ($this->getAttribute($_cat_, "parent_id")) {
                echo "data-parent-id=\"";
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "parent_id"), "html", null, true);
                echo "\"";
            }
            // line 394
            echo "\t\tdata-item-id=\"";
            if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "id"), "html", null, true);
            echo "\"
\t\tdata-full-title=\"";
            // line 395
            if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
            echo "\"
\t>
\t\t";
            // line 397
            if (isset($context["depth"])) { $_depth_ = $context["depth"]; } else { $_depth_ = null; }
            if ($_depth_) {
                echo "<span class=\"elbow-end\"></span>";
            }
            // line 398
            echo "\t\t<input type=\"radio\" name=\"category_id\" value=\"";
            if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "id"), "html", null, true);
            echo "\" />
\t\t<label>";
            // line 399
            if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
            echo "</label>
\t</li>
\t";
            // line 401
            if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
            if (twig_length_filter($this->env, $this->getAttribute($_cat_, "children"))) {
                // line 402
                echo "\t\t";
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($_cat_, "children"));
                foreach ($context['_seq'] as $context["_key"] => $context["subcat"]) {
                    // line 403
                    echo "\t\t\t";
                    if (isset($context["subcat"])) { $_subcat_ = $context["subcat"]; } else { $_subcat_ = null; }
                    if (isset($context["depth"])) { $_depth_ = $context["depth"]; } else { $_depth_ = null; }
                    echo $this->getAttribute($this, "category_row", array(0 => $_subcat_, 1 => ($_depth_ + 1)), "method");
                    echo "
\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subcat'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 405
                echo "\t";
            }
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    public function getTemplateName()
    {
        return "AgentBundle:Kb:view.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1329 => 405,  1309 => 401,  1303 => 399,  1297 => 398,  1292 => 397,  1280 => 394,  1249 => 391,  1237 => 390,  1205 => 409,  1200 => 408,  1194 => 407,  1178 => 380,  1175 => 378,  1170 => 375,  1135 => 362,  1105 => 348,  1078 => 332,  1068 => 325,  1048 => 317,  961 => 294,  922 => 280,  750 => 221,  842 => 263,  1038 => 364,  904 => 322,  882 => 318,  831 => 303,  860 => 314,  790 => 284,  733 => 241,  707 => 206,  744 => 220,  873 => 74,  824 => 256,  762 => 250,  713 => 234,  578 => 158,  2739 => 1153,  2735 => 1152,  2732 => 1151,  2725 => 1146,  2712 => 1144,  2707 => 1143,  2692 => 1131,  2688 => 1130,  2683 => 1127,  2680 => 1126,  2677 => 1125,  2671 => 1121,  2658 => 1119,  2653 => 1118,  2645 => 1113,  2641 => 1112,  2636 => 1109,  2632 => 1108,  2629 => 1107,  2626 => 1106,  2606 => 1089,  2602 => 1088,  2598 => 1087,  2594 => 1086,  2590 => 1085,  2586 => 1084,  2572 => 1073,  2568 => 1072,  2564 => 1071,  2560 => 1070,  2556 => 1069,  2552 => 1068,  2537 => 1056,  2533 => 1055,  2529 => 1054,  2525 => 1053,  2521 => 1052,  2503 => 1040,  2499 => 1039,  2495 => 1038,  2491 => 1037,  2487 => 1036,  2483 => 1035,  2469 => 1024,  2465 => 1023,  2461 => 1022,  2457 => 1021,  2453 => 1020,  2449 => 1019,  2426 => 999,  2422 => 998,  2418 => 997,  2414 => 996,  2410 => 995,  2406 => 994,  2392 => 983,  2388 => 982,  2384 => 981,  2380 => 980,  2376 => 979,  2372 => 978,  2358 => 967,  2354 => 966,  2350 => 965,  2346 => 964,  2342 => 963,  2338 => 962,  2324 => 951,  2320 => 950,  2316 => 949,  2312 => 948,  2308 => 947,  2304 => 946,  2295 => 939,  2282 => 937,  2277 => 936,  2269 => 931,  2265 => 930,  2260 => 927,  2256 => 926,  2253 => 925,  2230 => 901,  2220 => 899,  2216 => 898,  2211 => 896,  2208 => 895,  2198 => 893,  2194 => 892,  2189 => 890,  2184 => 889,  2177 => 885,  2173 => 884,  2164 => 877,  2151 => 875,  2146 => 874,  2143 => 873,  2141 => 864,  2134 => 860,  2130 => 859,  2122 => 853,  2120 => 852,  2113 => 848,  2105 => 846,  2096 => 839,  2083 => 837,  2078 => 836,  2073 => 834,  2067 => 830,  2062 => 828,  2058 => 827,  2054 => 826,  2050 => 825,  2045 => 824,  2038 => 822,  2034 => 821,  2022 => 814,  2009 => 812,  1999 => 809,  1993 => 805,  1988 => 803,  1984 => 802,  1980 => 801,  1976 => 800,  1971 => 799,  1968 => 798,  1964 => 797,  1960 => 796,  1954 => 793,  1946 => 788,  1942 => 787,  1938 => 786,  1932 => 782,  1927 => 780,  1919 => 778,  1915 => 777,  1911 => 776,  1906 => 775,  1903 => 774,  1899 => 773,  1895 => 772,  1886 => 768,  1883 => 764,  1871 => 759,  1866 => 756,  1863 => 755,  1842 => 752,  1825 => 749,  1818 => 748,  1815 => 747,  1800 => 743,  1793 => 741,  1772 => 729,  1761 => 721,  1757 => 720,  1751 => 717,  1740 => 709,  1719 => 697,  1715 => 696,  1686 => 673,  1676 => 669,  1665 => 661,  1661 => 660,  1645 => 651,  1638 => 649,  1630 => 645,  1625 => 642,  1622 => 641,  1598 => 637,  1577 => 634,  1558 => 629,  1549 => 627,  1546 => 626,  1535 => 618,  1531 => 617,  1514 => 606,  1510 => 605,  1504 => 602,  1493 => 594,  1483 => 590,  1464 => 580,  1458 => 577,  1443 => 568,  1437 => 565,  1426 => 557,  1405 => 545,  1401 => 544,  1395 => 541,  1390 => 538,  1381 => 533,  1373 => 531,  1348 => 521,  1343 => 520,  1335 => 515,  1321 => 510,  1318 => 403,  1299 => 503,  1294 => 502,  1282 => 496,  1269 => 491,  1260 => 483,  1240 => 478,  1221 => 476,  1216 => 475,  1210 => 410,  1206 => 473,  1193 => 467,  1189 => 466,  1155 => 450,  1150 => 447,  1022 => 411,  1006 => 404,  988 => 398,  969 => 392,  965 => 391,  921 => 370,  878 => 275,  866 => 349,  854 => 346,  819 => 293,  796 => 286,  1713 => 683,  1700 => 681,  1685 => 674,  1662 => 670,  1643 => 669,  1640 => 668,  1631 => 663,  1618 => 661,  1613 => 660,  1608 => 639,  1605 => 656,  1602 => 655,  1589 => 645,  1586 => 644,  1583 => 643,  1554 => 613,  1547 => 608,  1521 => 602,  1508 => 600,  1499 => 598,  1491 => 593,  1482 => 587,  1472 => 582,  1468 => 581,  1465 => 579,  1457 => 573,  1445 => 566,  1422 => 556,  1416 => 553,  1410 => 548,  1397 => 546,  1392 => 545,  1386 => 541,  1377 => 532,  1370 => 530,  1357 => 528,  1352 => 527,  1341 => 518,  1314 => 510,  1311 => 509,  1275 => 493,  1248 => 484,  1238 => 478,  1225 => 476,  1220 => 412,  1209 => 466,  1185 => 385,  1182 => 463,  1159 => 367,  1154 => 449,  1130 => 438,  1125 => 437,  1101 => 426,  1074 => 432,  1056 => 407,  1046 => 418,  1043 => 404,  1030 => 397,  1027 => 412,  947 => 361,  925 => 352,  913 => 324,  893 => 338,  881 => 335,  847 => 343,  829 => 336,  825 => 259,  1083 => 434,  995 => 383,  984 => 350,  963 => 319,  941 => 375,  851 => 271,  682 => 270,  1365 => 549,  1359 => 545,  1349 => 539,  1336 => 537,  1331 => 514,  1323 => 515,  1319 => 530,  1312 => 402,  1284 => 519,  1272 => 393,  1268 => 509,  1261 => 506,  1251 => 485,  1245 => 483,  1231 => 496,  1207 => 493,  1197 => 491,  1180 => 484,  1173 => 457,  1169 => 455,  1157 => 475,  1147 => 446,  1109 => 440,  1065 => 440,  1059 => 423,  1047 => 428,  1044 => 424,  1033 => 420,  1009 => 357,  991 => 351,  987 => 404,  973 => 395,  931 => 355,  924 => 371,  911 => 298,  906 => 81,  885 => 276,  872 => 317,  855 => 72,  749 => 264,  701 => 239,  594 => 109,  1163 => 454,  1143 => 440,  1087 => 420,  1077 => 433,  1051 => 430,  1037 => 480,  1010 => 405,  999 => 384,  932 => 326,  899 => 405,  895 => 404,  933 => 84,  914 => 133,  909 => 323,  833 => 238,  783 => 306,  755 => 248,  666 => 263,  453 => 187,  639 => 249,  568 => 191,  520 => 110,  657 => 216,  572 => 186,  609 => 17,  20 => 1,  659 => 207,  562 => 185,  548 => 167,  558 => 184,  479 => 145,  589 => 7,  457 => 145,  413 => 149,  953 => 430,  948 => 290,  935 => 394,  929 => 372,  916 => 382,  864 => 365,  844 => 338,  816 => 342,  807 => 234,  801 => 338,  774 => 254,  766 => 229,  737 => 242,  685 => 186,  664 => 225,  635 => 281,  593 => 231,  546 => 118,  532 => 68,  865 => 221,  852 => 241,  838 => 304,  820 => 201,  781 => 327,  764 => 274,  725 => 46,  632 => 245,  602 => 167,  565 => 154,  529 => 62,  505 => 156,  487 => 101,  473 => 87,  1853 => 842,  1849 => 753,  1839 => 751,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 733,  1778 => 732,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 693,  1705 => 781,  1688 => 675,  1681 => 769,  1677 => 768,  1667 => 671,  1656 => 762,  1648 => 655,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 606,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 593,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 540,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 522,  1289 => 520,  1276 => 604,  1263 => 601,  1244 => 480,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 463,  1181 => 561,  1171 => 557,  1158 => 550,  1114 => 428,  1104 => 465,  1072 => 512,  1062 => 7,  1024 => 395,  1014 => 406,  1000 => 328,  990 => 303,  980 => 474,  960 => 466,  918 => 348,  888 => 80,  834 => 307,  673 => 64,  636 => 185,  462 => 92,  454 => 253,  1144 => 542,  1139 => 399,  1131 => 399,  1127 => 359,  1110 => 351,  1092 => 459,  1089 => 390,  1086 => 520,  1084 => 335,  1063 => 387,  1060 => 321,  1055 => 422,  1050 => 373,  1035 => 372,  1019 => 330,  1003 => 309,  959 => 387,  900 => 366,  880 => 276,  870 => 350,  867 => 353,  859 => 362,  848 => 215,  839 => 376,  828 => 302,  823 => 374,  809 => 316,  800 => 241,  797 => 62,  794 => 257,  786 => 283,  740 => 78,  734 => 261,  703 => 246,  693 => 236,  630 => 278,  626 => 19,  614 => 275,  610 => 201,  581 => 197,  564 => 220,  525 => 61,  722 => 251,  697 => 282,  674 => 268,  671 => 221,  577 => 180,  569 => 222,  557 => 229,  502 => 99,  497 => 159,  445 => 85,  729 => 209,  684 => 237,  676 => 65,  669 => 220,  660 => 217,  647 => 198,  643 => 251,  601 => 306,  570 => 165,  522 => 164,  501 => 154,  296 => 114,  374 => 205,  631 => 207,  616 => 240,  608 => 235,  605 => 77,  596 => 185,  574 => 223,  561 => 175,  527 => 165,  433 => 166,  388 => 136,  426 => 175,  383 => 135,  461 => 155,  370 => 152,  395 => 224,  294 => 105,  223 => 64,  220 => 50,  492 => 180,  468 => 132,  444 => 168,  410 => 170,  397 => 134,  377 => 132,  262 => 55,  250 => 78,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 1051,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 680,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 638,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 572,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 526,  1301 => 1037,  1252 => 392,  1243 => 994,  1223 => 978,  1212 => 494,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 364,  1134 => 538,  1126 => 443,  1106 => 528,  1100 => 897,  1026 => 418,  997 => 822,  983 => 816,  975 => 296,  956 => 318,  939 => 786,  902 => 362,  894 => 364,  879 => 76,  757 => 269,  727 => 212,  716 => 235,  670 => 278,  528 => 176,  476 => 253,  435 => 176,  354 => 115,  341 => 125,  192 => 47,  321 => 57,  243 => 75,  793 => 350,  780 => 311,  758 => 226,  700 => 193,  686 => 238,  652 => 185,  638 => 210,  620 => 171,  545 => 166,  523 => 175,  494 => 134,  459 => 91,  438 => 48,  351 => 49,  347 => 128,  402 => 108,  268 => 90,  430 => 117,  411 => 110,  379 => 164,  322 => 124,  315 => 55,  289 => 70,  284 => 86,  255 => 86,  234 => 78,  1133 => 444,  1124 => 357,  1121 => 355,  1116 => 549,  1113 => 353,  1108 => 376,  1103 => 368,  1098 => 26,  1081 => 415,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 318,  1049 => 419,  1042 => 313,  1039 => 345,  1025 => 311,  1021 => 310,  1015 => 412,  1008 => 461,  996 => 406,  989 => 454,  985 => 315,  981 => 257,  977 => 321,  970 => 320,  966 => 375,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 148,  919 => 83,  917 => 279,  908 => 411,  905 => 363,  896 => 358,  891 => 360,  877 => 334,  862 => 348,  857 => 269,  837 => 261,  832 => 260,  827 => 68,  821 => 66,  803 => 179,  778 => 281,  769 => 58,  765 => 297,  753 => 54,  746 => 52,  743 => 244,  735 => 75,  730 => 214,  720 => 363,  717 => 362,  712 => 251,  691 => 233,  678 => 223,  654 => 199,  587 => 229,  576 => 196,  539 => 116,  517 => 144,  471 => 18,  441 => 135,  437 => 239,  418 => 144,  386 => 101,  373 => 121,  304 => 112,  270 => 68,  265 => 74,  229 => 73,  477 => 188,  455 => 125,  448 => 41,  429 => 128,  407 => 138,  399 => 138,  389 => 145,  375 => 128,  358 => 145,  349 => 114,  335 => 106,  327 => 126,  298 => 91,  280 => 76,  249 => 84,  194 => 65,  142 => 66,  344 => 136,  318 => 119,  306 => 115,  295 => 79,  357 => 51,  300 => 113,  286 => 77,  276 => 105,  269 => 91,  254 => 62,  128 => 43,  237 => 75,  165 => 43,  122 => 41,  798 => 281,  770 => 302,  759 => 278,  748 => 298,  731 => 260,  721 => 293,  718 => 301,  708 => 250,  696 => 287,  617 => 204,  590 => 160,  553 => 147,  550 => 157,  540 => 212,  533 => 210,  500 => 102,  493 => 151,  489 => 133,  482 => 129,  467 => 258,  464 => 202,  458 => 160,  452 => 158,  449 => 123,  415 => 83,  382 => 142,  372 => 128,  361 => 129,  356 => 131,  339 => 113,  302 => 117,  285 => 110,  258 => 87,  123 => 26,  108 => 38,  424 => 86,  394 => 77,  380 => 132,  338 => 90,  319 => 122,  316 => 117,  312 => 116,  290 => 111,  267 => 79,  206 => 52,  110 => 29,  240 => 102,  224 => 66,  219 => 54,  217 => 87,  202 => 40,  186 => 58,  170 => 63,  100 => 22,  67 => 16,  14 => 1,  1096 => 345,  1090 => 421,  1088 => 435,  1085 => 456,  1066 => 409,  1034 => 282,  1031 => 281,  1018 => 416,  1013 => 358,  1007 => 274,  1002 => 403,  993 => 305,  986 => 264,  982 => 394,  976 => 399,  971 => 295,  964 => 255,  949 => 388,  946 => 402,  940 => 384,  937 => 374,  928 => 452,  926 => 413,  915 => 299,  912 => 82,  903 => 231,  898 => 440,  892 => 319,  889 => 277,  887 => 281,  884 => 79,  876 => 75,  874 => 215,  871 => 331,  863 => 352,  861 => 270,  858 => 347,  850 => 378,  843 => 206,  840 => 406,  815 => 251,  812 => 297,  808 => 246,  804 => 258,  799 => 312,  791 => 310,  785 => 328,  775 => 82,  771 => 253,  754 => 267,  728 => 317,  726 => 72,  723 => 237,  715 => 105,  711 => 152,  709 => 222,  706 => 232,  698 => 229,  694 => 199,  692 => 189,  689 => 240,  681 => 224,  677 => 232,  675 => 234,  663 => 218,  661 => 200,  650 => 223,  646 => 112,  629 => 181,  627 => 244,  625 => 209,  622 => 242,  598 => 232,  592 => 184,  586 => 182,  575 => 232,  566 => 242,  556 => 177,  554 => 240,  541 => 176,  536 => 241,  515 => 108,  511 => 166,  509 => 24,  488 => 196,  486 => 147,  483 => 175,  465 => 126,  463 => 141,  450 => 244,  432 => 129,  419 => 127,  371 => 137,  362 => 159,  353 => 118,  337 => 141,  333 => 128,  309 => 95,  303 => 108,  299 => 103,  291 => 78,  272 => 81,  261 => 64,  253 => 98,  239 => 63,  235 => 56,  213 => 86,  200 => 59,  198 => 51,  159 => 36,  149 => 54,  146 => 48,  131 => 32,  116 => 29,  79 => 19,  74 => 8,  71 => 23,  836 => 262,  817 => 322,  814 => 319,  811 => 235,  805 => 244,  787 => 256,  779 => 169,  776 => 222,  773 => 347,  761 => 296,  751 => 265,  747 => 265,  742 => 336,  739 => 333,  736 => 215,  724 => 259,  705 => 69,  702 => 601,  688 => 226,  680 => 185,  667 => 232,  662 => 27,  656 => 418,  649 => 285,  644 => 220,  641 => 211,  624 => 206,  613 => 166,  607 => 200,  597 => 161,  591 => 49,  584 => 3,  579 => 1,  563 => 187,  559 => 68,  551 => 243,  547 => 179,  537 => 115,  524 => 112,  512 => 174,  507 => 165,  504 => 141,  498 => 213,  485 => 194,  480 => 50,  472 => 96,  466 => 38,  460 => 152,  447 => 137,  442 => 40,  434 => 47,  428 => 127,  422 => 146,  404 => 80,  368 => 132,  364 => 126,  340 => 133,  334 => 125,  330 => 59,  325 => 125,  292 => 107,  287 => 87,  282 => 101,  279 => 109,  273 => 99,  266 => 103,  256 => 81,  252 => 87,  228 => 67,  218 => 64,  201 => 68,  64 => 15,  51 => 10,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 847,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 823,  2037 => 789,  2031 => 788,  2028 => 818,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 811,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 779,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 769,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 744,  1801 => 695,  1798 => 694,  1795 => 742,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 708,  1730 => 705,  1720 => 688,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 672,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 659,  1651 => 656,  1641 => 631,  1637 => 667,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 651,  1592 => 614,  1587 => 613,  1584 => 635,  1581 => 731,  1576 => 608,  1574 => 633,  1567 => 603,  1563 => 630,  1559 => 601,  1552 => 628,  1548 => 597,  1542 => 593,  1529 => 605,  1525 => 614,  1516 => 708,  1512 => 583,  1503 => 599,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 569,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 537,  1380 => 539,  1376 => 523,  1371 => 645,  1367 => 527,  1364 => 518,  1361 => 523,  1355 => 543,  1342 => 624,  1337 => 510,  1328 => 516,  1324 => 511,  1317 => 511,  1313 => 499,  1310 => 498,  1304 => 504,  1291 => 502,  1286 => 395,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 487,  1262 => 490,  1257 => 489,  1254 => 482,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 498,  1232 => 416,  1226 => 413,  1213 => 466,  1208 => 465,  1201 => 472,  1196 => 464,  1192 => 490,  1188 => 456,  1184 => 485,  1179 => 462,  1176 => 461,  1172 => 376,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 561,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 442,  1119 => 354,  1115 => 424,  1111 => 377,  1107 => 349,  1102 => 439,  1099 => 347,  1095 => 419,  1091 => 418,  1082 => 455,  1079 => 524,  1076 => 359,  1070 => 431,  1057 => 491,  1052 => 406,  1045 => 484,  1040 => 414,  1036 => 283,  1032 => 360,  1028 => 312,  1023 => 393,  1020 => 392,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 308,  992 => 821,  979 => 297,  974 => 256,  967 => 391,  962 => 337,  958 => 336,  954 => 293,  950 => 153,  945 => 376,  942 => 288,  938 => 330,  934 => 283,  927 => 282,  923 => 387,  920 => 412,  910 => 278,  901 => 340,  897 => 339,  890 => 343,  886 => 50,  883 => 356,  868 => 274,  856 => 323,  853 => 319,  849 => 264,  845 => 69,  841 => 341,  835 => 337,  830 => 249,  826 => 329,  822 => 326,  818 => 65,  813 => 183,  810 => 290,  806 => 180,  802 => 242,  795 => 311,  792 => 239,  789 => 233,  784 => 286,  782 => 282,  777 => 255,  772 => 289,  768 => 81,  763 => 327,  760 => 319,  756 => 214,  752 => 247,  745 => 245,  741 => 218,  738 => 216,  732 => 171,  719 => 253,  714 => 251,  710 => 200,  704 => 231,  699 => 67,  695 => 66,  690 => 285,  687 => 210,  683 => 346,  679 => 298,  672 => 179,  668 => 264,  665 => 229,  658 => 178,  645 => 213,  640 => 211,  634 => 218,  628 => 174,  623 => 107,  619 => 78,  611 => 165,  606 => 164,  603 => 199,  599 => 194,  595 => 193,  583 => 159,  580 => 45,  573 => 157,  560 => 185,  543 => 146,  538 => 69,  534 => 175,  530 => 145,  526 => 229,  521 => 287,  518 => 109,  514 => 202,  510 => 143,  503 => 59,  496 => 152,  490 => 150,  484 => 146,  474 => 127,  470 => 142,  446 => 122,  440 => 149,  436 => 119,  431 => 141,  425 => 81,  416 => 112,  412 => 76,  408 => 157,  403 => 134,  400 => 146,  396 => 137,  392 => 144,  385 => 124,  381 => 100,  367 => 111,  363 => 124,  359 => 127,  355 => 122,  350 => 120,  346 => 92,  343 => 116,  328 => 106,  324 => 120,  313 => 80,  307 => 151,  301 => 81,  288 => 99,  283 => 101,  271 => 104,  257 => 63,  251 => 95,  238 => 79,  233 => 97,  195 => 78,  191 => 64,  187 => 46,  183 => 45,  130 => 28,  88 => 16,  76 => 31,  115 => 44,  95 => 32,  655 => 177,  651 => 176,  648 => 215,  637 => 219,  633 => 175,  621 => 462,  618 => 179,  615 => 203,  604 => 52,  600 => 233,  588 => 48,  585 => 295,  582 => 181,  571 => 179,  567 => 193,  555 => 37,  552 => 168,  549 => 224,  544 => 230,  542 => 178,  535 => 177,  531 => 174,  519 => 173,  516 => 162,  513 => 161,  508 => 158,  506 => 160,  499 => 153,  495 => 181,  491 => 163,  481 => 161,  478 => 128,  475 => 97,  469 => 182,  456 => 140,  451 => 139,  443 => 194,  439 => 144,  427 => 155,  423 => 114,  420 => 140,  409 => 118,  405 => 148,  401 => 136,  391 => 134,  387 => 132,  384 => 134,  378 => 76,  365 => 97,  360 => 117,  348 => 138,  336 => 132,  332 => 109,  329 => 127,  323 => 118,  310 => 110,  305 => 82,  277 => 95,  274 => 87,  263 => 102,  259 => 100,  247 => 82,  244 => 78,  241 => 77,  222 => 73,  210 => 85,  207 => 48,  204 => 66,  184 => 62,  181 => 57,  167 => 40,  157 => 41,  96 => 28,  421 => 153,  417 => 250,  414 => 143,  406 => 126,  398 => 146,  393 => 125,  390 => 153,  376 => 122,  369 => 124,  366 => 120,  352 => 142,  345 => 67,  342 => 66,  331 => 138,  326 => 87,  320 => 102,  317 => 43,  314 => 112,  311 => 121,  308 => 92,  297 => 58,  293 => 100,  281 => 97,  278 => 96,  275 => 95,  264 => 85,  260 => 83,  248 => 79,  245 => 91,  242 => 81,  231 => 60,  227 => 78,  215 => 63,  212 => 49,  209 => 70,  197 => 53,  177 => 66,  171 => 71,  161 => 59,  132 => 44,  121 => 29,  105 => 39,  99 => 21,  81 => 26,  77 => 25,  180 => 45,  176 => 39,  156 => 42,  143 => 52,  139 => 65,  118 => 56,  189 => 49,  185 => 54,  173 => 64,  166 => 68,  152 => 63,  174 => 44,  164 => 39,  154 => 31,  150 => 55,  137 => 35,  133 => 45,  127 => 31,  107 => 28,  102 => 17,  83 => 10,  78 => 13,  53 => 6,  23 => 3,  42 => 6,  138 => 46,  134 => 34,  109 => 32,  103 => 23,  97 => 21,  94 => 22,  84 => 29,  75 => 31,  69 => 12,  66 => 18,  54 => 11,  44 => 10,  230 => 53,  226 => 89,  203 => 68,  193 => 48,  188 => 66,  182 => 53,  178 => 44,  168 => 72,  163 => 54,  160 => 39,  155 => 57,  148 => 44,  145 => 39,  140 => 34,  136 => 64,  125 => 42,  120 => 25,  113 => 39,  101 => 25,  92 => 21,  89 => 27,  85 => 40,  73 => 18,  62 => 2,  59 => 10,  56 => 9,  41 => 8,  126 => 31,  119 => 30,  111 => 39,  106 => 32,  98 => 23,  93 => 42,  86 => 19,  70 => 22,  60 => 13,  28 => 3,  36 => 7,  114 => 28,  104 => 27,  91 => 27,  80 => 34,  63 => 15,  58 => 14,  40 => 7,  34 => 5,  45 => 13,  61 => 13,  55 => 12,  48 => 12,  39 => 7,  35 => 6,  31 => 4,  26 => 6,  21 => 2,  46 => 9,  29 => 6,  57 => 12,  50 => 12,  47 => 9,  38 => 5,  33 => 5,  49 => 20,  32 => 3,  246 => 76,  236 => 61,  232 => 90,  225 => 77,  221 => 88,  216 => 72,  214 => 53,  211 => 71,  208 => 72,  205 => 60,  199 => 67,  196 => 68,  190 => 67,  179 => 73,  175 => 43,  172 => 73,  169 => 41,  162 => 70,  158 => 58,  153 => 50,  151 => 41,  147 => 69,  144 => 36,  141 => 37,  135 => 33,  129 => 57,  124 => 30,  117 => 34,  112 => 53,  90 => 41,  87 => 12,  82 => 19,  72 => 16,  68 => 30,  65 => 16,  52 => 13,  43 => 6,  37 => 11,  30 => 5,  27 => 7,  25 => 4,  24 => 2,  22 => 2,  19 => 1,);
    }
}
