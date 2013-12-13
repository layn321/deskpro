<?php

/* AgentBundle:News:view.html.twig */
class __TwigTemplate_b840aa5aa14c1a1230f79137de8d9622 extends \Application\DeskPRO\Twig\Template
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
pageMeta.fragmentClass = 'DeskPRO.Agent.PageFragment.Page.NewsView';
pageMeta.title = ";
        // line 4
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        echo twig_jsonencode_filter($this->getAttribute($_news_, "title"));
        echo ";
pageMeta.news_id = ";
        // line 5
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_news_, "id"), "html", null, true);
        echo ";
pageMeta.url_fragment  = '";
        // line 6
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->urlFragment("agent_news_view", array("news_id" => $this->getAttribute($_news_, "id"))), "html", null, true);
        echo "';
pageMeta.permalink = '";
        // line 7
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_news_, "permalink"), "html", null, true);
        echo "';
pageMeta.labelsSaveUrl = '";
        // line 8
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_news_ajax_labels_save", array("news_id" => $this->getAttribute($_news_, "id"))), "html", null, true);
        echo "';
pageMeta.obj_code = 'news_";
        // line 9
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_news_, "id"), "html", null, true);
        echo "';

";
        // line 11
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        if (($this->getAttribute($_news_, "hidden_status") == "validating")) {
            // line 12
            echo "pageMeta.isValidating = true;
";
        }
        // line 14
        echo "
pageMeta.canEdit   = ";
        // line 15
        if (isset($context["perms"])) { $_perms_ = $context["perms"]; } else { $_perms_ = null; }
        if ((!$this->getAttribute($_perms_, "can_edit"))) {
            echo "false";
        } else {
            echo "true";
        }
        echo ";
pageMeta.canDelete = ";
        // line 16
        if (isset($context["perms"])) { $_perms_ = $context["perms"]; } else { $_perms_ = null; }
        if ((!$this->getAttribute($_perms_, "can_delete"))) {
            echo "false";
        } else {
            echo "true";
        }
        echo ";

";
        // line 18
        $context["baseId"] = $this->env->getExtension('deskpro_templating')->elUid();
        // line 19
        echo "pageMeta.baseId = '";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "';
</script>
";
        // line 22
        echo "<div class=\"layout-content with-scrollbar page-listing page-news-view ";
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
        // line 26
        echo "
<div class=\"delete-notice\" ";
        // line 27
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        if (($this->getAttribute($_news_, "status_code") != "hidden.deleted")) {
            echo "style=\"display: none;\"";
        }
        echo ">
\t";
        // line 28
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.record_deleted");
        echo " <button class=\"clean-gray xxx-small undelete\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.category");
        echo "</button>
</div>

";
        // line 31
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        if (($this->getAttribute($_news_, "status_code") == "hidden.validating")) {
            // line 32
            echo "\t";
            $this->env->loadTemplate("AgentBundle:Publish:view-approve-header.html.twig")->display($context);
        }
        // line 34
        echo "
<header class=\"page-header\">
\t<h4 class=\"id-number\">#";
        // line 36
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_news_, "id"), "html", null, true);
        echo "</h4>
\t<div style=\"float:left; padding-left: 8px;\">
\t\t<select id=\"";
        // line 38
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_status\">
\t\t\t<option ";
        // line 39
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        if (($this->getAttribute($_news_, "status") == "published")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"published\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.status_published");
        echo "</option>
\t\t\t<option ";
        // line 40
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        if (($this->getAttribute($_news_, "getStatusCode") == "hidden.unpublished")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"hidden.unpublished\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.status_hidden_unpublished");
        echo "</option>
\t\t\t<option ";
        // line 41
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        if (($this->getAttribute($_news_, "status") == "archived")) {
            echo "selected=\"selected\"";
        }
        echo " value=\"archived\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.status_archived");
        echo "</option>
\t\t\t";
        // line 42
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($this->getAttribute($this->getAttribute($_news_, "person"), "id") == $this->getAttribute($this->getAttribute($_app_, "user"), "id"))) {
            // line 43
            echo "\t\t\t\t<option value=\"hidden.draft\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.status_hidden_draft");
            echo "</option>
\t\t\t";
        }
        // line 45
        echo "\t\t</select>
\t</div>
\t<h1>
\t\t<span id=\"";
        // line 48
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_showname\">";
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_news_, "title"), "html", null, true);
        echo "</span>
\t\t<span id=\"";
        // line 49
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_editname\" style=\"display: none\"><input type=\"text\" name=\"name\" value=\"";
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_news_, "title"), "html", null, true);
        echo "\" /></span>
\t\t<span id=\"";
        // line 50
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_editname_start\" class=\"edit-name-gear\"></span>
\t\t<a id=\"";
        // line 51
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_editname_end\" class=\"edit-name-save clean-white\" style=\"display: none\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.done");
        echo "</a>
\t</h1>
\t<br class=\"clear\" />
\t<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"margin-top: 2px;\">
\t\t<tr>
\t\t\t<td valign=\"middle\" style=\"vertical-align: middle;\">
\t\t\t\t";
        // line 57
        if (isset($context["news_categories"])) { $_news_categories_ = $context["news_categories"]; } else { $_news_categories_ = null; }
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        $this->env->loadTemplate("AgentBundle:Common:select-standard.html.twig")->display(array_merge($context, array("name" => "category_id", "add_classname" => "category_id", "add_attr" => "", "with_blank" => 0, "blank_title" => "", "categories" => $_news_categories_, "selected" => $this->getAttribute($this->getAttribute($_news_, "category"), "id"), "allow_parent_sel" => true, "id" => ($_baseId_ . "_cat"))));
        // line 68
        echo "\t\t\t</td>
\t\t\t<td width=\"100%\">
\t\t\t\t<div class=\"labels-line\">
\t\t\t\t\t<input
\t\t\t\t\t\ttype=\"hidden\"
\t\t\t\t\t\tid=\"";
        // line 73
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_labels_input\"
\t\t\t\t\t\tclass=\"dpe_select dpe_select_noborder\"
\t\t\t\t\t\tdata-select-width=\"auto\"
\t\t\t\t\t\tdata-placeholder=\"Add a label\"
\t\t\t\t\t\tvalue=\"";
        // line 77
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_news_, "labels"));
        foreach ($context['_seq'] as $context["_key"] => $context["label"]) {
            if (isset($context["label"])) { $_label_ = $context["label"]; } else { $_label_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_label_, "label"), "html", null, true);
            echo ",";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['label'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        echo "\"
\t\t\t\t\t/>
\t\t\t\t</div>
\t\t\t</td>
\t\t</tr>
\t</table>
\t<br class=\"clear\" />
</header>


<div class=\"profile-box-container-outer\">
\t<div class=\"profile-box-container\">
\t\t<header>
\t\t\t<h4>";
        // line 90
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.properties");
        echo "</h4>
\t\t</header>
\t\t<section class=\"description-area\">
\t\t\t<dl class=\"info-list\">
\t\t\t\t<dt>";
        // line 94
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.rating");
        echo "</dt>
\t\t\t\t<dd>
                    ";
        // line 96
        ob_start();
        // line 97
        echo "                    <span class=\"link-look who-voted-trigger\">";
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.count_votecount_votes", array("count" => $this->getAttribute($_news_, "num_ratings")));
        echo "</span>
                    ";
        $context["phrase_part"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 99
        echo "                    ";
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        if (isset($context["phrase_part"])) { $_phrase_part_ = $context["phrase_part"]; } else { $_phrase_part_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.percent_helpful_subphrase", array("percent" => $this->getAttribute($_news_, "rating_percent"), "subphrase" => $_phrase_part_), true);
        echo "<br />
                    <small>";
        // line 100
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.found_article_helpful_or_not", array("up_votes" => $this->getAttribute($_news_, "up_votes"), "down_votes" => $this->getAttribute($_news_, "down_votes")));
        echo "</small>
\t\t\t\t</dd>

\t\t\t\t<dt></dt>
\t\t\t\t<dd>
\t\t\t\t\t";
        // line 105
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.created_timeago_by_x_on_y", array("timeago" => (("<time class=\"timeago\" datetime=\"" . $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_news_, "date_created"), "c", "UTC")) . "\"></time>"), "name" => $this->getAttribute($this->getAttribute($_news_, "person"), "display_name"), "date" => (("<time>" . $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_news_, "date_created"), "fulltime")) . "</time>")), true);
        // line 109
        echo "
\t\t\t\t</dd>
\t\t\t</dl>

\t\t\t<div class=\"controls\" id=\"";
        // line 113
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_action_buttons\">
\t\t\t\t<button class=\"clean-white delete nodelete-hide\" ";
        // line 114
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        if (($this->getAttribute($_news_, "status_code") == "hidden.deleted")) {
            echo "style=\"display:none\"";
        }
        echo ">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.delete");
        echo "</button>
\t\t\t\t<button class=\"clean-white permalink\">";
        // line 115
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.copy_permalink");
        echo "</button>
\t\t\t\t<button class=\"clean-white view-user-interface\">";
        // line 116
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.view_in_user_interface");
        echo "</button>
\t\t\t\t<br class=\"clear\" />
\t\t\t</div>
\t\t</section>
\t</div>
</div>

<div class=\"profile-box-container reply-box-wrap\">
\t<header>
\t\t<nav  id=\"";
        // line 125
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_bodytabs\">
\t\t\t<ul>
\t\t\t\t<li class=\"on\" data-tab-for=\"#";
        // line 127
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_tab_contents .news-content\"><span>";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.page");
        echo "</span></li>
\t\t\t\t<li data-tab-for=\"#";
        // line 128
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_tab_contents .comments-tab\"><span>";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.comments");
        echo " (<span id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_count_comments\">";
        if (isset($context["news_comments"])) { $_news_comments_ = $context["news_comments"]; } else { $_news_comments_ = null; }
        echo twig_escape_filter($this->env, _twig_default_filter(twig_length_filter($this->env, $_news_comments_), 0), "html", null, true);
        echo "</span>)</span></li>
\t\t\t\t<li data-tab-for=\"#";
        // line 129
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_tab_contents .revisions-tab\"><span>";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.label_revisions");
        echo " (<span id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_count_revs\">";
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        echo twig_escape_filter($this->env, _twig_default_filter(twig_length_filter($this->env, $this->getAttribute($_news_, "revisions")), 0), "html", null, true);
        echo "</span>)</span></li>
\t\t\t\t<li data-tab-for=\"#";
        // line 130
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_tab_contents .related-content-tab\"><span>";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.related_content");
        echo " (<span id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_count_related\">0</span>)</span></li>
\t\t\t\t<li data-tab-for=\"#";
        // line 131
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
        // line 138
        echo "\t\t<div id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_tab_contents\">
\t\t\t";
        // line 140
        echo "\t\t\t<div class=\"deskpro-tab-item news-content on\">
\t\t\t\t";
        // line 142
        echo "\t\t\t\t<nav class=\"sub-box-nav noedit-hide\">
\t\t\t\t\t<ul class=\"option-buttons right\" id=\"";
        // line 143
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_content_edit_btns\">
\t\t\t\t\t\t<li class=\"kb-editor-edit edit\" id=\"";
        // line 144
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_edit_btn\"><span>";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ucfirst_edit");
        echo "</span></li>
\t\t\t\t\t\t<li class=\"editor-save-trigger save\" id=\"";
        // line 145
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_save_btn\"><span>";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.save");
        echo "</span></li>
\t\t\t\t\t\t<li class=\"editor-cancel-trigger cancel\" id=\"";
        // line 146
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_cancel_btn\"><span>";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.cancel");
        echo "</span></li>
\t\t\t\t\t</ul>
\t\t\t\t</nav>
\t\t\t\t<div id=\"";
        // line 149
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_content_ed\" class=\"news-content content-tab-item\">
\t\t\t\t";
        // line 150
        $this->env->loadTemplate("AgentBundle:News:view-content-tab.html.twig")->display($context);
        // line 151
        echo "\t\t\t\t</div>
\t\t\t\t";
        // line 153
        echo "\t\t\t</div>

\t\t\t";
        // line 156
        echo "\t\t\t<div class=\"deskpro-tab-item content-tab-item comments-tab\">
\t\t\t\t<div class=\"news-comments\">
\t\t\t\t\t<div class=\"full-tab-content\">
\t\t\t\t\t\t<div class=\"messages-wrap\" id=\"";
        // line 159
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_comments_wrap\">
\t\t\t\t\t\t\t";
        // line 160
        if (isset($context["news_comments"])) { $_news_comments_ = $context["news_comments"]; } else { $_news_comments_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_news_comments_);
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
            // line 161
            echo "\t\t\t\t\t\t\t\t";
            if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
            $context["comment_num"] = $this->getAttribute($_loop_, "index");
            // line 162
            echo "\t\t\t\t\t\t\t\t";
            $this->env->loadTemplate("AgentBundle:News:view-comment.html.twig")->display($context);
            // line 163
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
        // line 164
        echo "\t\t\t\t\t\t</div>
\t\t\t\t\t</div>

\t\t\t\t\t<div class=\"messages-wrap new-note\">
\t\t\t\t\t\t<article
\t\t\t\t\t\t\tclass=\"content-message agent-message\"
\t\t\t\t\t\t\tdata-comment-id=\"";
        // line 170
        if (isset($context["comment"])) { $_comment_ = $context["comment"]; } else { $_comment_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_comment_, "id"), "html", null, true);
        echo "\"
\t\t\t\t\t\t\tdata-content-type=\"";
        // line 171
        if (isset($context["comment"])) { $_comment_ = $context["comment"]; } else { $_comment_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_comment_, "object_content_type"), "html", null, true);
        echo "\"
\t\t\t\t\t\t>
\t\t\t\t\t\t\t<div class=\"avatar\">
\t\t\t\t\t\t\t\t<img src=\"";
        // line 174
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "user"), "getPictureUrl", array(0 => 40), "method"), "html", null, true);
        echo "\" alt=\"\" width=\"40\" height=\"40\" />
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t<div class=\"content\">
\t\t\t\t\t\t\t\t<header>
\t\t\t\t\t\t\t\t\t<h4>";
        // line 178
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.new_comment");
        echo "</h4>
\t\t\t\t\t\t\t\t</header>
\t\t\t\t\t\t\t\t<div class=\"body-text\">
\t\t\t\t\t\t\t\t\t<textarea style=\"width:99%; height: 80px;\"></textarea>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t<div class=\"footer-text\">
\t\t\t\t\t\t\t\t\t<div class=\"loading-off\">
\t\t\t\t\t\t\t\t\t\t<button class=\"clean-white\">";
        // line 185
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.add_comment");
        echo "</button>
\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t<div class=\"loading-on\" style=\"display:none\">
\t\t\t\t\t\t\t\t\t\t";
        // line 188
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.saving");
        echo "
\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</article>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t\t";
        // line 197
        echo "
\t\t\t";
        // line 199
        echo "\t\t\t<div id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_revs\" class=\"deskpro-tab-item content-tab-item revisions-tab\">
\t\t\t\t";
        // line 200
        $this->env->loadTemplate("AgentBundle:News:view-revisions-tab.html.twig")->display($context);
        // line 201
        echo "\t\t\t</div>
\t\t\t";
        // line 203
        echo "
\t\t\t";
        // line 205
        echo "\t\t\t<div id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_related_content\" class=\"deskpro-tab-item content-tab-item related-content-tab\">
\t\t\t\t";
        // line 206
        $this->env->loadTemplate("AgentBundle:Common:content-related-list.html.twig")->display($context);
        // line 207
        echo "\t\t\t</div>
\t\t\t";
        // line 209
        echo "
\t\t\t";
        // line 211
        echo "\t\t\t<div id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_searchtab\" class=\"deskpro-tab-item search-tab\">
\t\t\t\t<section class=\"description-area sticky-search-words\">
\t\t\t\t\t<dl class=\"info-list\">
\t\t\t\t\t\t<dt class=\"type-labels\">";
        // line 214
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.sticky_words_title");
        echo "</dt>
\t\t\t\t\t\t<dd class=\"noedit-tags\">
\t\t\t\t\t\t\t<input type=\"text\" value=\"";
        // line 216
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
        echo "\" id=\"";
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
        echo "_stickysearch_input\" />
\t\t\t\t\t\t\t<div class=\"explain\">
\t\t\t\t\t\t\t\t";
        // line 218
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.publish.search_sticky_words_explain");
        echo "
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</dd>
\t\t\t\t\t</dl>
\t\t\t\t</section>

\t\t\t\t<br />

\t\t\t\t";
        // line 226
        $this->env->loadTemplate("AgentBundle:Publish:rated-searches.html.twig")->display($context);
        // line 227
        echo "\t\t\t</div>
\t\t\t";
        // line 229
        echo "\t\t</div>
\t\t";
        // line 231
        echo "</section>
</div>

<div class=\"inline-editable\" style=\"display:none\">
\t<div class=\"title\">
\t\t<input type=\"text\" name=\"title\" value=\"";
        // line 236
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_news_, "title"), "html", null, true);
        echo "\"/>
\t\t<input type=\"hidden\" name=\"action\" value=\"title\" />
\t</div>
</div>

";
        // line 242
        echo "</div></div></div>
";
    }

    public function getTemplateName()
    {
        return "AgentBundle:News:view.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1183 => 449,  1132 => 436,  1097 => 427,  957 => 394,  907 => 380,  875 => 376,  653 => 236,  1329 => 405,  1309 => 401,  1303 => 399,  1297 => 398,  1292 => 397,  1280 => 394,  1249 => 391,  1237 => 390,  1205 => 409,  1200 => 408,  1194 => 407,  1178 => 380,  1175 => 447,  1170 => 375,  1135 => 362,  1105 => 428,  1078 => 332,  1068 => 325,  1048 => 417,  961 => 294,  922 => 280,  750 => 221,  842 => 263,  1038 => 364,  904 => 322,  882 => 318,  831 => 303,  860 => 314,  790 => 284,  733 => 296,  707 => 206,  744 => 220,  873 => 74,  824 => 256,  762 => 250,  713 => 234,  578 => 158,  2739 => 1153,  2735 => 1152,  2732 => 1151,  2725 => 1146,  2712 => 1144,  2707 => 1143,  2692 => 1131,  2688 => 1130,  2683 => 1127,  2680 => 1126,  2677 => 1125,  2671 => 1121,  2658 => 1119,  2653 => 1118,  2645 => 1113,  2641 => 1112,  2636 => 1109,  2632 => 1108,  2629 => 1107,  2626 => 1106,  2606 => 1089,  2602 => 1088,  2598 => 1087,  2594 => 1086,  2590 => 1085,  2586 => 1084,  2572 => 1073,  2568 => 1072,  2564 => 1071,  2560 => 1070,  2556 => 1069,  2552 => 1068,  2537 => 1056,  2533 => 1055,  2529 => 1054,  2525 => 1053,  2521 => 1052,  2503 => 1040,  2499 => 1039,  2495 => 1038,  2491 => 1037,  2487 => 1036,  2483 => 1035,  2469 => 1024,  2465 => 1023,  2461 => 1022,  2457 => 1021,  2453 => 1020,  2449 => 1019,  2426 => 999,  2422 => 998,  2418 => 997,  2414 => 996,  2410 => 995,  2406 => 994,  2392 => 983,  2388 => 982,  2384 => 981,  2380 => 980,  2376 => 979,  2372 => 978,  2358 => 967,  2354 => 966,  2350 => 965,  2346 => 964,  2342 => 963,  2338 => 962,  2324 => 951,  2320 => 950,  2316 => 949,  2312 => 948,  2308 => 947,  2304 => 946,  2295 => 939,  2282 => 937,  2277 => 936,  2269 => 931,  2265 => 930,  2260 => 927,  2256 => 926,  2253 => 925,  2230 => 901,  2220 => 899,  2216 => 898,  2211 => 896,  2208 => 895,  2198 => 893,  2194 => 892,  2189 => 890,  2184 => 889,  2177 => 885,  2173 => 884,  2164 => 877,  2151 => 875,  2146 => 874,  2143 => 873,  2141 => 864,  2134 => 860,  2130 => 859,  2122 => 853,  2120 => 852,  2113 => 848,  2105 => 846,  2096 => 839,  2083 => 837,  2078 => 836,  2073 => 834,  2067 => 830,  2062 => 828,  2058 => 827,  2054 => 826,  2050 => 825,  2045 => 824,  2038 => 822,  2034 => 821,  2022 => 814,  2009 => 812,  1999 => 809,  1993 => 805,  1988 => 803,  1984 => 802,  1980 => 801,  1976 => 800,  1971 => 799,  1968 => 798,  1964 => 797,  1960 => 796,  1954 => 793,  1946 => 788,  1942 => 787,  1938 => 786,  1932 => 782,  1927 => 780,  1919 => 778,  1915 => 777,  1911 => 776,  1906 => 775,  1903 => 774,  1899 => 773,  1895 => 772,  1886 => 768,  1883 => 764,  1871 => 759,  1866 => 756,  1863 => 755,  1842 => 752,  1825 => 749,  1818 => 748,  1815 => 747,  1800 => 743,  1793 => 741,  1772 => 729,  1761 => 721,  1757 => 720,  1751 => 717,  1740 => 709,  1719 => 697,  1715 => 696,  1686 => 673,  1676 => 669,  1665 => 661,  1661 => 660,  1645 => 651,  1638 => 649,  1630 => 645,  1625 => 642,  1622 => 641,  1598 => 637,  1577 => 634,  1558 => 629,  1549 => 627,  1546 => 626,  1535 => 618,  1531 => 617,  1514 => 606,  1510 => 605,  1504 => 602,  1493 => 594,  1483 => 590,  1464 => 580,  1458 => 577,  1443 => 568,  1437 => 565,  1426 => 557,  1405 => 545,  1401 => 544,  1395 => 541,  1390 => 538,  1381 => 533,  1373 => 531,  1348 => 521,  1343 => 520,  1335 => 515,  1321 => 510,  1318 => 403,  1299 => 503,  1294 => 502,  1282 => 496,  1269 => 491,  1260 => 483,  1240 => 478,  1221 => 476,  1216 => 475,  1210 => 410,  1206 => 473,  1193 => 467,  1189 => 466,  1155 => 439,  1150 => 447,  1022 => 411,  1006 => 404,  988 => 398,  969 => 392,  965 => 391,  921 => 370,  878 => 275,  866 => 349,  854 => 346,  819 => 293,  796 => 286,  1713 => 683,  1700 => 681,  1685 => 674,  1662 => 670,  1643 => 669,  1640 => 668,  1631 => 663,  1618 => 661,  1613 => 660,  1608 => 639,  1605 => 656,  1602 => 655,  1589 => 645,  1586 => 644,  1583 => 643,  1554 => 613,  1547 => 608,  1521 => 602,  1508 => 600,  1499 => 598,  1491 => 593,  1482 => 587,  1472 => 582,  1468 => 581,  1465 => 579,  1457 => 573,  1445 => 566,  1422 => 556,  1416 => 553,  1410 => 548,  1397 => 546,  1392 => 545,  1386 => 541,  1377 => 532,  1370 => 530,  1357 => 528,  1352 => 527,  1341 => 518,  1314 => 510,  1311 => 509,  1275 => 493,  1248 => 484,  1238 => 478,  1225 => 476,  1220 => 412,  1209 => 466,  1185 => 385,  1182 => 463,  1159 => 367,  1154 => 449,  1130 => 438,  1125 => 437,  1101 => 426,  1074 => 432,  1056 => 418,  1046 => 418,  1043 => 404,  1030 => 397,  1027 => 412,  947 => 361,  925 => 352,  913 => 324,  893 => 338,  881 => 335,  847 => 343,  829 => 336,  825 => 259,  1083 => 434,  995 => 383,  984 => 350,  963 => 319,  941 => 375,  851 => 367,  682 => 270,  1365 => 549,  1359 => 545,  1349 => 539,  1336 => 537,  1331 => 514,  1323 => 515,  1319 => 530,  1312 => 402,  1284 => 519,  1272 => 393,  1268 => 509,  1261 => 506,  1251 => 485,  1245 => 483,  1231 => 496,  1207 => 493,  1197 => 491,  1180 => 484,  1173 => 457,  1169 => 455,  1157 => 475,  1147 => 438,  1109 => 440,  1065 => 440,  1059 => 423,  1047 => 428,  1044 => 424,  1033 => 420,  1009 => 357,  991 => 351,  987 => 404,  973 => 395,  931 => 355,  924 => 371,  911 => 298,  906 => 81,  885 => 276,  872 => 317,  855 => 72,  749 => 264,  701 => 239,  594 => 109,  1163 => 440,  1143 => 440,  1087 => 420,  1077 => 433,  1051 => 430,  1037 => 480,  1010 => 405,  999 => 407,  932 => 326,  899 => 379,  895 => 404,  933 => 387,  914 => 133,  909 => 323,  833 => 238,  783 => 306,  755 => 248,  666 => 263,  453 => 187,  639 => 269,  568 => 199,  520 => 110,  657 => 216,  572 => 186,  609 => 216,  20 => 1,  659 => 207,  562 => 185,  548 => 185,  558 => 184,  479 => 145,  589 => 239,  457 => 153,  413 => 140,  953 => 430,  948 => 290,  935 => 394,  929 => 372,  916 => 382,  864 => 365,  844 => 338,  816 => 342,  807 => 341,  801 => 338,  774 => 254,  766 => 229,  737 => 297,  685 => 186,  664 => 225,  635 => 281,  593 => 209,  546 => 227,  532 => 223,  865 => 221,  852 => 241,  838 => 304,  820 => 201,  781 => 327,  764 => 274,  725 => 46,  632 => 268,  602 => 246,  565 => 197,  529 => 62,  505 => 156,  487 => 101,  473 => 87,  1853 => 842,  1849 => 753,  1839 => 751,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 733,  1778 => 732,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 693,  1705 => 781,  1688 => 675,  1681 => 769,  1677 => 768,  1667 => 671,  1656 => 762,  1648 => 655,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 606,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 593,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 540,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 522,  1289 => 520,  1276 => 604,  1263 => 601,  1244 => 480,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 463,  1181 => 561,  1171 => 446,  1158 => 550,  1114 => 428,  1104 => 465,  1072 => 512,  1062 => 7,  1024 => 395,  1014 => 406,  1000 => 328,  990 => 303,  980 => 474,  960 => 466,  918 => 348,  888 => 80,  834 => 307,  673 => 64,  636 => 185,  462 => 92,  454 => 151,  1144 => 542,  1139 => 437,  1131 => 399,  1127 => 434,  1110 => 351,  1092 => 459,  1089 => 426,  1086 => 520,  1084 => 335,  1063 => 387,  1060 => 321,  1055 => 422,  1050 => 373,  1035 => 372,  1019 => 330,  1003 => 309,  959 => 387,  900 => 366,  880 => 276,  870 => 350,  867 => 353,  859 => 362,  848 => 215,  839 => 376,  828 => 302,  823 => 374,  809 => 316,  800 => 241,  797 => 337,  794 => 257,  786 => 283,  740 => 78,  734 => 261,  703 => 246,  693 => 236,  630 => 278,  626 => 19,  614 => 275,  610 => 201,  581 => 197,  564 => 268,  525 => 61,  722 => 251,  697 => 282,  674 => 274,  671 => 221,  577 => 180,  569 => 233,  557 => 229,  502 => 99,  497 => 207,  445 => 196,  729 => 209,  684 => 237,  676 => 65,  669 => 220,  660 => 272,  647 => 198,  643 => 229,  601 => 306,  570 => 165,  522 => 220,  501 => 154,  296 => 123,  374 => 205,  631 => 207,  616 => 283,  608 => 281,  605 => 77,  596 => 211,  574 => 200,  561 => 231,  527 => 165,  433 => 190,  388 => 144,  426 => 186,  383 => 135,  461 => 156,  370 => 155,  395 => 131,  294 => 105,  223 => 64,  220 => 85,  492 => 204,  468 => 210,  444 => 168,  410 => 170,  397 => 134,  377 => 161,  262 => 102,  250 => 90,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 1051,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 680,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 638,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 572,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 526,  1301 => 1037,  1252 => 392,  1243 => 994,  1223 => 978,  1212 => 494,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 364,  1134 => 538,  1126 => 443,  1106 => 528,  1100 => 897,  1026 => 418,  997 => 822,  983 => 816,  975 => 296,  956 => 318,  939 => 786,  902 => 362,  894 => 364,  879 => 76,  757 => 269,  727 => 295,  716 => 235,  670 => 278,  528 => 176,  476 => 253,  435 => 176,  354 => 115,  341 => 138,  192 => 76,  321 => 114,  243 => 85,  793 => 350,  780 => 311,  758 => 226,  700 => 193,  686 => 238,  652 => 185,  638 => 226,  620 => 171,  545 => 166,  523 => 171,  494 => 134,  459 => 197,  438 => 146,  351 => 148,  347 => 147,  402 => 150,  268 => 95,  430 => 188,  411 => 110,  379 => 162,  322 => 123,  315 => 55,  289 => 107,  284 => 97,  255 => 128,  234 => 77,  1133 => 444,  1124 => 357,  1121 => 430,  1116 => 549,  1113 => 429,  1108 => 376,  1103 => 368,  1098 => 26,  1081 => 425,  1073 => 424,  1067 => 356,  1064 => 355,  1061 => 421,  1053 => 318,  1049 => 419,  1042 => 313,  1039 => 345,  1025 => 414,  1021 => 310,  1015 => 409,  1008 => 461,  996 => 406,  989 => 454,  985 => 315,  981 => 257,  977 => 321,  970 => 320,  966 => 375,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 148,  919 => 83,  917 => 279,  908 => 411,  905 => 363,  896 => 358,  891 => 378,  877 => 334,  862 => 348,  857 => 269,  837 => 261,  832 => 260,  827 => 349,  821 => 66,  803 => 179,  778 => 324,  769 => 58,  765 => 297,  753 => 54,  746 => 52,  743 => 298,  735 => 75,  730 => 214,  720 => 363,  717 => 362,  712 => 251,  691 => 233,  678 => 275,  654 => 199,  587 => 229,  576 => 201,  539 => 116,  517 => 144,  471 => 160,  441 => 195,  437 => 239,  418 => 183,  386 => 167,  373 => 245,  304 => 126,  270 => 90,  265 => 102,  229 => 68,  477 => 200,  455 => 125,  448 => 41,  429 => 128,  407 => 138,  399 => 138,  389 => 145,  375 => 141,  358 => 149,  349 => 114,  335 => 106,  327 => 124,  298 => 100,  280 => 115,  249 => 89,  194 => 65,  142 => 57,  344 => 136,  318 => 119,  306 => 115,  295 => 112,  357 => 151,  300 => 113,  286 => 77,  276 => 188,  269 => 107,  254 => 99,  128 => 50,  237 => 84,  165 => 58,  122 => 43,  798 => 281,  770 => 302,  759 => 278,  748 => 298,  731 => 260,  721 => 293,  718 => 301,  708 => 250,  696 => 287,  617 => 204,  590 => 207,  553 => 263,  550 => 157,  540 => 258,  533 => 254,  500 => 233,  493 => 162,  489 => 161,  482 => 201,  467 => 258,  464 => 209,  458 => 160,  452 => 150,  449 => 123,  415 => 83,  382 => 249,  372 => 129,  361 => 240,  356 => 131,  339 => 113,  302 => 117,  285 => 107,  258 => 117,  123 => 26,  108 => 42,  424 => 144,  394 => 339,  380 => 143,  338 => 226,  319 => 216,  316 => 113,  312 => 129,  290 => 111,  267 => 79,  206 => 75,  110 => 29,  240 => 102,  224 => 57,  219 => 54,  217 => 87,  202 => 40,  186 => 45,  170 => 67,  100 => 29,  67 => 15,  14 => 1,  1096 => 345,  1090 => 421,  1088 => 435,  1085 => 456,  1066 => 423,  1034 => 282,  1031 => 281,  1018 => 416,  1013 => 358,  1007 => 408,  1002 => 403,  993 => 305,  986 => 264,  982 => 401,  976 => 399,  971 => 295,  964 => 255,  949 => 388,  946 => 402,  940 => 388,  937 => 374,  928 => 385,  926 => 413,  915 => 381,  912 => 82,  903 => 231,  898 => 440,  892 => 319,  889 => 277,  887 => 281,  884 => 79,  876 => 75,  874 => 215,  871 => 331,  863 => 373,  861 => 270,  858 => 347,  850 => 378,  843 => 206,  840 => 406,  815 => 251,  812 => 343,  808 => 246,  804 => 258,  799 => 312,  791 => 310,  785 => 328,  775 => 82,  771 => 253,  754 => 267,  728 => 317,  726 => 72,  723 => 237,  715 => 105,  711 => 285,  709 => 222,  706 => 232,  698 => 229,  694 => 199,  692 => 189,  689 => 276,  681 => 224,  677 => 232,  675 => 234,  663 => 218,  661 => 200,  650 => 223,  646 => 231,  629 => 267,  627 => 218,  625 => 266,  622 => 285,  598 => 232,  592 => 184,  586 => 182,  575 => 232,  566 => 242,  556 => 230,  554 => 188,  541 => 176,  536 => 224,  515 => 108,  511 => 166,  509 => 24,  488 => 196,  486 => 147,  483 => 175,  465 => 198,  463 => 141,  450 => 244,  432 => 129,  419 => 143,  371 => 244,  362 => 159,  353 => 235,  337 => 143,  333 => 128,  309 => 209,  303 => 115,  299 => 103,  291 => 99,  272 => 114,  261 => 118,  253 => 91,  239 => 98,  235 => 56,  213 => 73,  200 => 78,  198 => 102,  159 => 61,  149 => 39,  146 => 40,  131 => 36,  116 => 47,  79 => 19,  74 => 8,  71 => 23,  836 => 262,  817 => 345,  814 => 319,  811 => 235,  805 => 244,  787 => 256,  779 => 169,  776 => 222,  773 => 322,  761 => 296,  751 => 302,  747 => 265,  742 => 336,  739 => 333,  736 => 215,  724 => 259,  705 => 69,  702 => 601,  688 => 226,  680 => 185,  667 => 273,  662 => 242,  656 => 418,  649 => 285,  644 => 220,  641 => 211,  624 => 206,  613 => 166,  607 => 200,  597 => 275,  591 => 49,  584 => 236,  579 => 203,  563 => 187,  559 => 68,  551 => 243,  547 => 179,  537 => 115,  524 => 112,  512 => 174,  507 => 237,  504 => 141,  498 => 213,  485 => 224,  480 => 50,  472 => 96,  466 => 159,  460 => 152,  447 => 149,  442 => 40,  434 => 47,  428 => 127,  422 => 146,  404 => 80,  368 => 243,  364 => 241,  340 => 133,  334 => 116,  330 => 115,  325 => 125,  292 => 150,  287 => 87,  282 => 96,  279 => 122,  273 => 110,  266 => 103,  256 => 100,  252 => 87,  228 => 109,  218 => 72,  201 => 78,  64 => 22,  51 => 9,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 847,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 823,  2037 => 789,  2031 => 788,  2028 => 818,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 811,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 779,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 769,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 744,  1801 => 695,  1798 => 694,  1795 => 742,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 708,  1730 => 705,  1720 => 688,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 672,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 659,  1651 => 656,  1641 => 631,  1637 => 667,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 651,  1592 => 614,  1587 => 613,  1584 => 635,  1581 => 731,  1576 => 608,  1574 => 633,  1567 => 603,  1563 => 630,  1559 => 601,  1552 => 628,  1548 => 597,  1542 => 593,  1529 => 605,  1525 => 614,  1516 => 708,  1512 => 583,  1503 => 599,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 569,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 537,  1380 => 539,  1376 => 523,  1371 => 645,  1367 => 527,  1364 => 518,  1361 => 523,  1355 => 543,  1342 => 624,  1337 => 510,  1328 => 516,  1324 => 511,  1317 => 511,  1313 => 499,  1310 => 498,  1304 => 504,  1291 => 502,  1286 => 395,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 487,  1262 => 490,  1257 => 489,  1254 => 482,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 498,  1232 => 416,  1226 => 413,  1213 => 466,  1208 => 465,  1201 => 472,  1196 => 464,  1192 => 490,  1188 => 456,  1184 => 485,  1179 => 448,  1176 => 461,  1172 => 376,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 561,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 442,  1119 => 354,  1115 => 424,  1111 => 377,  1107 => 349,  1102 => 439,  1099 => 347,  1095 => 419,  1091 => 418,  1082 => 455,  1079 => 524,  1076 => 359,  1070 => 431,  1057 => 491,  1052 => 406,  1045 => 484,  1040 => 416,  1036 => 283,  1032 => 415,  1028 => 312,  1023 => 393,  1020 => 412,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 308,  992 => 406,  979 => 297,  974 => 400,  967 => 399,  962 => 397,  958 => 336,  954 => 293,  950 => 393,  945 => 391,  942 => 288,  938 => 330,  934 => 283,  927 => 282,  923 => 382,  920 => 412,  910 => 278,  901 => 340,  897 => 339,  890 => 343,  886 => 50,  883 => 377,  868 => 375,  856 => 369,  853 => 319,  849 => 264,  845 => 69,  841 => 341,  835 => 354,  830 => 249,  826 => 329,  822 => 347,  818 => 65,  813 => 183,  810 => 290,  806 => 180,  802 => 339,  795 => 311,  792 => 335,  789 => 233,  784 => 286,  782 => 282,  777 => 255,  772 => 289,  768 => 81,  763 => 327,  760 => 319,  756 => 214,  752 => 247,  745 => 245,  741 => 218,  738 => 216,  732 => 171,  719 => 290,  714 => 251,  710 => 200,  704 => 281,  699 => 280,  695 => 66,  690 => 285,  687 => 210,  683 => 346,  679 => 298,  672 => 179,  668 => 264,  665 => 229,  658 => 178,  645 => 213,  640 => 227,  634 => 218,  628 => 174,  623 => 107,  619 => 78,  611 => 282,  606 => 280,  603 => 199,  599 => 194,  595 => 242,  583 => 159,  580 => 45,  573 => 157,  560 => 267,  543 => 146,  538 => 178,  534 => 175,  530 => 174,  526 => 221,  521 => 287,  518 => 170,  514 => 202,  510 => 164,  503 => 59,  496 => 163,  490 => 150,  484 => 146,  474 => 127,  470 => 142,  446 => 122,  440 => 149,  436 => 189,  431 => 145,  425 => 187,  416 => 142,  412 => 76,  408 => 157,  403 => 134,  400 => 146,  396 => 148,  392 => 169,  385 => 130,  381 => 331,  367 => 111,  363 => 131,  359 => 128,  355 => 122,  350 => 120,  346 => 125,  343 => 116,  328 => 106,  324 => 120,  313 => 80,  307 => 105,  301 => 81,  288 => 118,  283 => 101,  271 => 186,  257 => 100,  251 => 98,  238 => 79,  233 => 95,  195 => 77,  191 => 48,  187 => 52,  183 => 64,  130 => 32,  88 => 13,  76 => 24,  115 => 40,  95 => 13,  655 => 177,  651 => 176,  648 => 215,  637 => 219,  633 => 175,  621 => 462,  618 => 179,  615 => 203,  604 => 214,  600 => 233,  588 => 206,  585 => 295,  582 => 205,  571 => 179,  567 => 193,  555 => 37,  552 => 229,  549 => 224,  544 => 230,  542 => 226,  535 => 177,  531 => 174,  519 => 173,  516 => 218,  513 => 217,  508 => 215,  506 => 160,  499 => 208,  495 => 181,  491 => 163,  481 => 161,  478 => 128,  475 => 97,  469 => 182,  456 => 196,  451 => 195,  443 => 194,  439 => 144,  427 => 155,  423 => 114,  420 => 140,  409 => 118,  405 => 148,  401 => 136,  391 => 134,  387 => 334,  384 => 250,  378 => 76,  365 => 153,  360 => 117,  348 => 233,  336 => 132,  332 => 141,  329 => 127,  323 => 119,  310 => 109,  305 => 207,  277 => 94,  274 => 98,  263 => 94,  259 => 109,  247 => 123,  244 => 77,  241 => 77,  222 => 73,  210 => 82,  207 => 50,  204 => 66,  184 => 62,  181 => 49,  167 => 41,  157 => 61,  96 => 35,  421 => 153,  417 => 250,  414 => 182,  406 => 172,  398 => 146,  393 => 125,  390 => 153,  376 => 122,  369 => 124,  366 => 120,  352 => 127,  345 => 67,  342 => 66,  331 => 126,  326 => 137,  320 => 292,  317 => 131,  314 => 214,  311 => 210,  308 => 116,  297 => 203,  293 => 100,  281 => 97,  278 => 96,  275 => 136,  264 => 111,  260 => 93,  248 => 96,  245 => 84,  242 => 64,  231 => 110,  227 => 92,  215 => 83,  212 => 51,  209 => 80,  197 => 68,  177 => 62,  171 => 63,  161 => 59,  132 => 52,  121 => 47,  105 => 41,  99 => 36,  81 => 26,  77 => 28,  180 => 43,  176 => 42,  156 => 32,  143 => 50,  139 => 57,  118 => 26,  189 => 72,  185 => 68,  173 => 64,  166 => 48,  152 => 55,  174 => 68,  164 => 65,  154 => 44,  150 => 53,  137 => 39,  133 => 45,  127 => 31,  107 => 28,  102 => 17,  83 => 10,  78 => 17,  53 => 6,  23 => 3,  42 => 11,  138 => 36,  134 => 34,  109 => 26,  103 => 43,  97 => 14,  94 => 22,  84 => 27,  75 => 16,  69 => 23,  66 => 15,  54 => 18,  44 => 11,  230 => 90,  226 => 89,  203 => 67,  193 => 48,  188 => 69,  182 => 63,  178 => 48,  168 => 77,  163 => 47,  160 => 34,  155 => 91,  148 => 88,  145 => 59,  140 => 40,  136 => 48,  125 => 49,  120 => 25,  113 => 21,  101 => 37,  92 => 33,  89 => 31,  85 => 18,  73 => 26,  62 => 19,  59 => 12,  56 => 11,  41 => 10,  126 => 78,  119 => 28,  111 => 39,  106 => 44,  98 => 23,  93 => 42,  86 => 29,  70 => 11,  60 => 19,  28 => 4,  36 => 10,  114 => 44,  104 => 27,  91 => 38,  80 => 29,  63 => 14,  58 => 14,  40 => 7,  34 => 8,  45 => 8,  61 => 13,  55 => 12,  48 => 14,  39 => 6,  35 => 6,  31 => 7,  26 => 6,  21 => 2,  46 => 12,  29 => 2,  57 => 15,  50 => 9,  47 => 14,  38 => 11,  33 => 4,  49 => 14,  32 => 3,  246 => 76,  236 => 73,  232 => 90,  225 => 84,  221 => 77,  216 => 72,  214 => 104,  211 => 69,  208 => 72,  205 => 79,  199 => 49,  196 => 43,  190 => 67,  179 => 100,  175 => 62,  172 => 60,  169 => 59,  162 => 62,  158 => 40,  153 => 54,  151 => 60,  147 => 58,  144 => 38,  141 => 56,  135 => 53,  129 => 50,  124 => 49,  117 => 34,  112 => 27,  90 => 51,  87 => 19,  82 => 28,  72 => 16,  68 => 40,  65 => 22,  52 => 12,  43 => 8,  37 => 11,  30 => 5,  27 => 6,  25 => 4,  24 => 4,  22 => 2,  19 => 1,);
    }
}
