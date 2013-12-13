<?php

/* AdminBundle:Portal:website-widgets.html.twig */
class __TwigTemplate_76d3e1da6c3056b5056c5ac5565400b0 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("AdminBundle::layout.html.twig");

        $this->blocks = array(
            'pagetitle' => array($this, 'block_pagetitle'),
            'html_head' => array($this, 'block_html_head'),
            'dp_full_page' => array($this, 'block_dp_full_page'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "AdminBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        $context["this_page"] = "website_widgets";
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_pagetitle($context, array $blocks = array())
    {
        // line 4
        echo "\tWebsite Embeds
";
    }

    // line 6
    public function block_html_head($context, array $blocks = array())
    {
        // line 7
        echo "<script type=\"text/javascript\">
\$(document).ready(function() {
\tif (\$('#widget_content_select').height() <= 240) {
\t\treturn;
\t}

\t\$('#widget_content_select_showmore').show().on('click', function() {
\t\t\$(this).hide();
\t\t\$('#widget_content_select').css('max-height', '1000000px');
\t});
});

\$(document).ready(function() {
\tvar dep_form_wrap = \$('#department_form_wrap');
\tvar sel = dep_form_wrap.find('select');
\tDP.select(sel);

\tsel.on('change', function() {
\t\tvar depid = parseInt(sel.val());

\t\tif (depid) {
\t\t\twindow.location = '";
        // line 28
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_website_embeds"), "html", null, true);
        echo "?department_id=' + depid + '#contact_form';
\t\t} else {
\t\t\twindow.location = '";
        // line 30
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_website_embeds"), "html", null, true);
        echo "#contact_form';
\t\t}

\t\t\$('#change_dep_load').show();
\t});
});
</script>
<style type=\"text/css\">
#widget_content_select {
\tmax-height: 250px;
\toverflow: hidden;
}

#widget_content_select_wrap {
\tposition: relative;
}

#widget_content_select_showmore {
\tposition: absolute;
\tbottom: 0;
\tleft: 0;
\tright: 0;
\theight: 90px;
\tbackground: #fff;
\tbackground: rgba(255, 255, 255, 0.6);
\tbackground: -moz-linear-gradient(top,  rgba(255,255,255,0) 0%, rgba(255,255,255,1) 100%);
\tbackground: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(255,255,255,0)), color-stop(100%,rgba(255,255,255,1)));
\tbackground: -webkit-linear-gradient(top,  rgba(255,255,255,0) 0%,rgba(255,255,255,1) 100%);
\tbackground: -o-linear-gradient(top,  rgba(255,255,255,0) 0%,rgba(255,255,255,1) 100%);
\tbackground: -ms-linear-gradient(top,  rgba(255,255,255,0) 0%,rgba(255,255,255,1) 100%);
\tbackground: linear-gradient(to bottom,  rgba(255,255,255,0) 0%,rgba(255,255,255,1) 100%);
\tfilter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#00ffffff', endColorstr='#ffffff',GradientType=0 );
\ttext-align: center;
}
#widget_content_select_showmore button {
\tmargin-top: 66px;
}
</style>
";
    }

    // line 69
    public function block_dp_full_page($context, array $blocks = array())
    {
        // line 70
        echo "
\t";
        // line 72
        echo "\t";
        // line 73
        echo "\t";
        // line 74
        echo "
\t<div class=\"dp_admin_pagebar\">
\t\t<ul>
\t\t\t<li>\"Feedback and Support\" Website Tab</li>
\t\t</ul>
\t</div>
\t<div class=\"dp_admin_page\">
\t\t<div class=\"dp-page-box\">
\t\t\t<div class=\"page-content\">
\t\t\t\t<p>";
        // line 83
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.portal.website_widgets_overlay_explain");
        echo "</p>

\t\t\t\t";
        // line 85
        ob_start();
        $this->env->loadTemplate("AdminBundle:Portal:widget-code-overlay.txt.twig")->display($context);
        $context["code"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 86
        echo "\t\t\t\t<textarea class=\"code\" wrap=\"off\">";
        if (isset($context["code"])) { $_code_ = $context["code"]; } else { $_code_ = null; }
        echo twig_escape_filter($this->env, strtr($_code_, array("\t" => "    ")));
        echo "</textarea>
\t\t\t\t<p>";
        // line 87
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.portal.website_widget_install");
        echo "</p>

\t\t\t\t<div id=\"widget_content_select_wrap\">
\t\t\t\t\t<div id=\"widget_content_select\">
\t\t\t\t\t\t<form action=\"";
        // line 91
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_website_embeds"), "html", null, true);
        echo "\" method=\"post\">
\t\t\t\t\t\t<input type=\"hidden\" name=\"save_selections\" value=\"1\" />
\t\t\t\t\t\t<h1 class=\"noexpand\">Show links to content by default</h1>

\t\t\t\t\t\t";
        // line 113
        echo "\t\t\t\t\t\t";
        if (isset($context["articles"])) { $_articles_ = $context["articles"]; } else { $_articles_ = null; }
        if ($_articles_) {
            // line 114
            echo "\t\t\t\t\t\t\t<div style=\"width:295px; float: left; overflow: auto;\">
\t\t\t\t\t\t\t\t<h3 style=\"color: #88826F; border-top: 1px solid #88826F; border-bottom: 1px solid #88826F; padding: 3px 0 3px 0; margin-top: 5px; margin-bottom: 5px;\">Articles</h3>
\t\t\t\t\t\t\t\t";
            // line 116
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if (isset($context["articles"])) { $_articles_ = $context["articles"]; } else { $_articles_ = null; }
            if (isset($context["article_cat_map"])) { $_article_cat_map_ = $context["article_cat_map"]; } else { $_article_cat_map_ = null; }
            if (isset($context["selections"])) { $_selections_ = $context["selections"]; } else { $_selections_ = null; }
            echo $this->getAttribute($this, "build_list", array(0 => "articles", 1 => $this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "ArticleCategory"), "method"), "getRootNodes", array(), "method"), 2 => $_articles_, 3 => $_article_cat_map_, 4 => $this->getAttribute($_selections_, "articles"), 5 => 0), "method");
            echo "
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t";
        }
        // line 119
        echo "
\t\t\t\t\t\t";
        // line 120
        if (isset($context["downloads"])) { $_downloads_ = $context["downloads"]; } else { $_downloads_ = null; }
        if ($_downloads_) {
            // line 121
            echo "\t\t\t\t\t\t\t<div style=\"width:295px; float: left; overflow: auto;\">
\t\t\t\t\t\t\t\t<h3 style=\"color: #88826F; border-top: 1px solid #88826F; border-bottom: 1px solid #88826F; padding: 3px 0 3px 0; margin-top: 5px;\">Downloads</h3>
\t\t\t\t\t\t\t\t";
            // line 123
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if (isset($context["downloads"])) { $_downloads_ = $context["downloads"]; } else { $_downloads_ = null; }
            if (isset($context["download_cat_map"])) { $_download_cat_map_ = $context["download_cat_map"]; } else { $_download_cat_map_ = null; }
            if (isset($context["selections"])) { $_selections_ = $context["selections"]; } else { $_selections_ = null; }
            echo $this->getAttribute($this, "build_list", array(0 => "downloads", 1 => $this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "DownloadCategory"), "method"), "getRootNodes", array(), "method"), 2 => $_downloads_, 3 => $_download_cat_map_, 4 => $this->getAttribute($_selections_, "downloads"), 5 => 0), "method");
            echo "
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t";
        }
        // line 126
        echo "
\t\t\t\t\t\t";
        // line 127
        if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
        if ($_news_) {
            // line 128
            echo "\t\t\t\t\t\t\t<div style=\"width:295px; float: left; overflow: auto;\">
\t\t\t\t\t\t\t\t<h3 style=\"color: #88826F; border-top: 1px solid #88826F; border-bottom: 1px solid #88826F; padding: 3px 0 3px 0; margin-top: 5px;\">News</h3>
\t\t\t\t\t\t\t\t";
            // line 130
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if (isset($context["news"])) { $_news_ = $context["news"]; } else { $_news_ = null; }
            if (isset($context["news_cat_map"])) { $_news_cat_map_ = $context["news_cat_map"]; } else { $_news_cat_map_ = null; }
            if (isset($context["selections"])) { $_selections_ = $context["selections"]; } else { $_selections_ = null; }
            echo $this->getAttribute($this, "build_list", array(0 => "news", 1 => $this->getAttribute($this->getAttribute($_app_, "getDataService", array(0 => "NewsCategory"), "method"), "getRootNodes", array(), "method"), 2 => $_news_, 3 => $_news_cat_map_, 4 => $this->getAttribute($_selections_, "news"), 5 => 0), "method");
            echo "
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t";
        }
        // line 133
        echo "
\t\t\t\t\t\t<br style=\"clear:both\" />
\t\t\t\t\t\t<br />

\t\t\t\t\t\t<button class=\"clean-white\">Update Selections</button>

\t\t\t\t\t\t</form>
\t\t\t\t\t</div>
\t\t\t\t\t<div id=\"widget_content_select_showmore\" style=\"display: none;\"><button class=\"clean-white\">Show More</button></div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>
\t<br />

\t";
        // line 149
        echo "\t";
        // line 150
        echo "\t";
        // line 151
        echo "
\t";
        // line 152
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.apps_chat"), "method")) {
            // line 153
            echo "\t\t<div class=\"dp_admin_pagebar\">
\t\t\t<ul>
\t\t\t\t<li>\"Chat with us\" Tab</li>
\t\t\t</ul>
\t\t</div>
\t\t<div class=\"dp_admin_page\">
\t\t\t<div class=\"dp_admin_page_inner\">
\t\t\t\t<div class=\"dp-page-box\">
\t\t\t\t\t<div class=\"page-content\">

\t\t\t\t\t\t<p>";
            // line 163
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.portal.website_widgets_chat_explain");
            echo "</p>

\t\t\t\t\t\t<p style=\"background-color: #F8FFC4;\">
\t\t\t\t\t\t\t<strong>Important:</strong>
\t\t\t\t\t\t\tThe chat widget only appears when there are agents available for chat in the Agent Interface.

\t\t\t\t\t\t\t";
            // line 169
            if (isset($context["chat_online"])) { $_chat_online_ = $context["chat_online"]; } else { $_chat_online_ = null; }
            if ((!$_chat_online_)) {
                // line 170
                echo "\t\t\t\t\t\t\t\t<br/>At the moment, there no agents signed in to chat so the widget will not appear.
\t\t\t\t\t\t\t";
            }
            // line 172
            echo "\t\t\t\t\t\t</p>

\t\t\t\t\t\t";
            // line 174
            ob_start();
            $this->env->loadTemplate("AdminBundle:Portal:widget-code-chat.txt.twig")->display($context);
            $context["code"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
            // line 175
            echo "\t\t\t\t\t\t<textarea class=\"code\" wrap=\"off\">";
            if (isset($context["code"])) { $_code_ = $context["code"]; } else { $_code_ = null; }
            echo twig_escape_filter($this->env, strtr($_code_, array("\t" => "    ")));
            echo "</textarea>
\t\t\t\t\t\t<p>";
            // line 176
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.portal.website_widget_install");
            echo "</p>

\t\t\t\t\t\t<p>Related articles:</p>
\t\t\t\t\t\t<ul class=\"small-detail-list\">
\t\t\t\t\t\t\t<li><a href=\"https://support.deskpro.com/kb/articles/146\" target=\"_blank\">Set language from the chat widget</a></li>
\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<br />
\t";
        }
        // line 188
        echo "
\t";
        // line 190
        echo "\t";
        // line 191
        echo "\t";
        // line 192
        echo "
\t<div class=\"dp_admin_pagebar\" id=\"contact_form\">
\t\t<ul>
\t\t\t<li>Contact Form</li>
\t\t</ul>
\t</div>
\t<div class=\"dp_admin_page\">
\t\t<div class=\"dp_admin_page_inner\">
\t\t\t<div class=\"dp-page-box\">
\t\t\t\t<div class=\"page-content\">

\t\t\t\t\t<div id=\"department_form_wrap\" style=\"margin-bottom: 10px;\">
\t\t\t\t\t\t<table cellspacing=\"0\" cellpadding=\"0\"><tr>
\t\t\t\t\t\t\t<td valign=\"middle\" style=\"vertical-align: middle; padding-right: 7px;\">
\t\t\t\t\t\t\t\tGet code for a specific department:
\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t<td valign=\"middle\" style=\"vertical-align: middle; padding-right: 7px;\">
\t\t\t\t\t\t\t\t<select name=\"department\">
\t\t\t\t\t\t\t\t\t<option value=\"\"></option>
\t\t\t\t\t\t\t\t\t";
        // line 211
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "departments"), "getRootNodes", array(), "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["dep"]) {
            if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
            if ($this->getAttribute($_dep_, "is_tickets_enabled")) {
                // line 212
                echo "\t\t\t\t\t\t\t\t\t\t";
                if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                if (twig_length_filter($this->env, $this->getAttribute($_dep_, "children"))) {
                    // line 213
                    echo "\t\t\t\t\t\t\t\t\t\t\t<optgroup label=\"";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                    echo "\">
\t\t\t\t\t\t\t\t\t\t\t\t";
                    // line 214
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($_dep_, "children"));
                    foreach ($context['_seq'] as $context["_key"] => $context["subdep"]) {
                        // line 215
                        echo "\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"";
                        if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_subdep_, "id"), "html", null, true);
                        echo "\" ";
                        if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                        if (isset($context["department"])) { $_department_ = $context["department"]; } else { $_department_ = null; }
                        if (($this->getAttribute($_subdep_, "id") == $this->getAttribute($_department_, "id"))) {
                            echo "selected=\"selected\"";
                        }
                        echo ">";
                        if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_subdep_, "title"), "html", null, true);
                        echo "</option>
\t\t\t\t\t\t\t\t\t\t\t\t";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subdep'], $context['_parent'], $context['loop']);
                    $context = array_merge($_parent, array_intersect_key($context, $_parent));
                    // line 217
                    echo "\t\t\t\t\t\t\t\t\t\t\t</optgroup>
\t\t\t\t\t\t\t\t\t\t";
                } else {
                    // line 219
                    echo "\t\t\t\t\t\t\t\t\t\t\t<option value=\"";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "id"), "html", null, true);
                    echo "\" ";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    if (isset($context["department"])) { $_department_ = $context["department"]; } else { $_department_ = null; }
                    if (($this->getAttribute($_dep_, "id") == $this->getAttribute($_department_, "id"))) {
                        echo "selected=\"selected\"";
                    }
                    echo ">";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                    echo "</option>
\t\t\t\t\t\t\t\t\t\t";
                }
                // line 221
                echo "\t\t\t\t\t\t\t\t\t";
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['dep'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 222
        echo "\t\t\t\t\t\t\t\t</select>
\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t<td valign=\"middle\" style=\"vertical-align: middle;\">
\t\t\t\t\t\t\t\t<i id=\"change_dep_load\" style=\"display: none;\" class=\"flat-spinner\"></i>
\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t</tr></table>
\t\t\t\t\t</div>

\t\t\t\t\t<h3>";
        // line 230
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.direct_link");
        echo "</h3>
\t\t\t\t\t";
        // line 231
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.choice_direct_link_ticket_form");
        echo ":
\t\t\t\t\t<br />
\t\t\t\t\t<input type=\"text\" value=\"";
        // line 233
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getUriForPath", array(0 => ""), "method"), "html", null, true);
        echo "/new-ticket";
        if (isset($context["department"])) { $_department_ = $context["department"]; } else { $_department_ = null; }
        if ($_department_) {
            echo "/";
            if (isset($context["department"])) { $_department_ = $context["department"]; } else { $_department_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_department_, "id"), "html", null, true);
        }
        echo "\" style=\"width: 80%;\" />

\t\t\t\t\t<br />
\t\t\t\t\t<br />

\t\t\t\t\t<h3>";
        // line 238
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.popup_window");
        echo "</h3>
\t\t\t\t\t";
        // line 239
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.add_popup_link");
        echo "
\t\t\t\t\t<br />
";
        // line 241
        ob_start();
        // line 242
        echo "<a
\thref=\"";
        // line 243
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getUriForPath", array(0 => ""), "method"), "html", null, true);
        echo "/tickets/new-simple/";
        if (isset($context["department"])) { $_department_ = $context["department"]; } else { $_department_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_department_, "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_department_, "id"), "0")) : ("0")), "html", null, true);
        echo "\"
\tonclick=\"window.open(this.href, null, 'height=600, width=710, toolbar=0, location=0, status=1, scrollbars=1, resizable=1'); return false;\"
\tclass=\"dp-newticket-link\"
>";
        // line 246
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.contact_us");
        echo "</a>
";
        $context["code"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 248
        echo "\t\t\t\t\t<textarea class=\"code\" wrap=\"off\">";
        if (isset($context["code"])) { $_code_ = $context["code"]; } else { $_code_ = null; }
        echo twig_escape_filter($this->env, strtr($_code_, array("\t" => "    ")));
        echo "</textarea>

\t\t\t\t\t<br />
\t\t\t\t\t<br />

\t\t\t\t\t<h3>";
        // line 253
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.website_widget");
        echo "</h3>
\t\t\t\t\t";
        // line 254
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.embed_ticket_form_websites");
        echo ":
\t\t\t\t\t<br />
\t\t\t\t\t";
        // line 256
        ob_start();
        $this->env->loadTemplate("AdminBundle:Portal:widget-code-form.txt.twig")->display($context);
        $context["code"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 257
        echo "\t\t\t\t\t<textarea class=\"code\" wrap=\"off\">";
        if (isset($context["code"])) { $_code_ = $context["code"]; } else { $_code_ = null; }
        echo twig_escape_filter($this->env, strtr($_code_, array("\t" => "    ")));
        echo "</textarea>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>
\t<br />

\t";
        // line 265
        echo "\t";
        // line 266
        echo "\t";
        // line 267
        echo "
\t";
        // line 268
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "user.portal_enabled"), "method")) {
            // line 269
            echo "\t\t<div class=\"dp_admin_pagebar\">
\t\t\t<ul>
\t\t\t\t<li>Embed the entire helpdesk</li>
\t\t\t</ul>
\t\t</div>
\t\t<div class=\"dp_admin_page\">
\t\t\t<div class=\"dp_admin_page_inner\">
\t\t\t\t<div class=\"dp-page-box\">
\t\t\t\t\t<div class=\"page-content\">

\t\t\t\t\t\t<p>This embeds the entire helpdesk portal directly into any page on your site.</p>

\t\t\t\t\t\t";
            // line 281
            ob_start();
            $this->env->loadTemplate("AdminBundle:Portal:widget-code-helpdesk.txt.twig")->display($context);
            $context["code"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
            // line 282
            echo "\t\t\t\t\t\t<textarea class=\"code\" wrap=\"off\">";
            if (isset($context["code"])) { $_code_ = $context["code"]; } else { $_code_ = null; }
            echo twig_escape_filter($this->env, strtr($_code_, array("\t" => "    ")));
            echo "</textarea>

\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<br />
\t";
        }
        // line 290
        echo "
";
    }

    // line 95
    public function getbuild_list($_name = null, $_cats = null, $_items = null, $_map = null, $_selections = null, $_depth = null)
    {
        $context = $this->env->mergeGlobals(array(
            "name" => $_name,
            "cats" => $_cats,
            "items" => $_items,
            "map" => $_map,
            "selections" => $_selections,
            "depth" => $_depth,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 96
            echo "\t\t\t\t\t\t\t";
            if (isset($context["cats"])) { $_cats_ = $context["cats"]; } else { $_cats_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_cats_);
            foreach ($context['_seq'] as $context["_key"] => $context["cat"]) {
                // line 97
                echo "\t\t\t\t\t\t\t\t<div style=\"margin-left: ";
                if (isset($context["depth"])) { $_depth_ = $context["depth"]; } else { $_depth_ = null; }
                echo twig_escape_filter($this->env, (10 * $_depth_), "html", null, true);
                echo "px; margin-bottom: 6px;\">
\t\t\t\t\t\t\t\t\t<strong>";
                // line 98
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
                echo "</strong>
\t\t\t\t\t\t\t\t\t";
                // line 99
                if (isset($context["map"])) { $_map_ = $context["map"]; } else { $_map_ = null; }
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($_map_, $this->getAttribute($_cat_, "id"), array(), "array"));
                foreach ($context['_seq'] as $context["_key"] => $context["item_id"]) {
                    if (isset($context["items"])) { $_items_ = $context["items"]; } else { $_items_ = null; }
                    if (isset($context["item_id"])) { $_item_id_ = $context["item_id"]; } else { $_item_id_ = null; }
                    if ($this->getAttribute($_items_, $_item_id_, array(), "array")) {
                        // line 100
                        echo "\t\t\t\t\t\t\t\t\t\t<div style=\"margin-left: ";
                        if (isset($context["depth"])) { $_depth_ = $context["depth"]; } else { $_depth_ = null; }
                        echo twig_escape_filter($this->env, (10 * ($_depth_ + 1)), "html", null, true);
                        echo "px\">
\t\t\t\t\t\t\t\t\t\t\t<label>
\t\t\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"selections[";
                        // line 102
                        if (isset($context["name"])) { $_name_ = $context["name"]; } else { $_name_ = null; }
                        echo twig_escape_filter($this->env, $_name_, "html", null, true);
                        echo "][]\" value=\"";
                        if (isset($context["item_id"])) { $_item_id_ = $context["item_id"]; } else { $_item_id_ = null; }
                        echo twig_escape_filter($this->env, $_item_id_, "html", null, true);
                        echo "\" ";
                        if (isset($context["item_id"])) { $_item_id_ = $context["item_id"]; } else { $_item_id_ = null; }
                        if (isset($context["selections"])) { $_selections_ = $context["selections"]; } else { $_selections_ = null; }
                        if (twig_in_filter($_item_id_, $_selections_)) {
                            echo "checked=\"checked\"";
                        }
                        echo " />
\t\t\t\t\t\t\t\t\t\t\t\t";
                        // line 103
                        if (isset($context["items"])) { $_items_ = $context["items"]; } else { $_items_ = null; }
                        if (isset($context["item_id"])) { $_item_id_ = $context["item_id"]; } else { $_item_id_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_items_, $_item_id_, array(), "array"), "html", null, true);
                        echo "
\t\t\t\t\t\t\t\t\t\t\t</label>
\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t";
                    }
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item_id'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 107
                echo "\t\t\t\t\t\t\t\t\t";
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                if ($this->getAttribute($_cat_, "children")) {
                    // line 108
                    echo "\t\t\t\t\t\t\t\t\t\t";
                    if (isset($context["name"])) { $_name_ = $context["name"]; } else { $_name_ = null; }
                    if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                    if (isset($context["items"])) { $_items_ = $context["items"]; } else { $_items_ = null; }
                    if (isset($context["map"])) { $_map_ = $context["map"]; } else { $_map_ = null; }
                    if (isset($context["selections"])) { $_selections_ = $context["selections"]; } else { $_selections_ = null; }
                    if (isset($context["depth"])) { $_depth_ = $context["depth"]; } else { $_depth_ = null; }
                    echo $this->getAttribute($this, "build_list", array(0 => $_name_, 1 => $this->getAttribute($_cat_, "children"), 2 => $_items_, 3 => $_map_, 4 => $_selections_, 5 => ($_depth_ + 1)), "method");
                    echo "
\t\t\t\t\t\t\t\t\t";
                }
                // line 110
                echo "\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['cat'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 112
            echo "\t\t\t\t\t\t";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    public function getTemplateName()
    {
        return "AdminBundle:Portal:website-widgets.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  639 => 110,  568 => 272,  520 => 249,  657 => 287,  572 => 251,  609 => 459,  20 => 1,  659 => 274,  562 => 246,  548 => 238,  558 => 94,  479 => 82,  589 => 100,  457 => 211,  413 => 150,  953 => 406,  948 => 403,  935 => 394,  929 => 391,  916 => 382,  864 => 365,  844 => 348,  816 => 342,  807 => 339,  801 => 338,  774 => 337,  766 => 328,  737 => 314,  685 => 300,  664 => 294,  635 => 281,  593 => 445,  546 => 414,  532 => 236,  865 => 221,  852 => 216,  838 => 208,  820 => 201,  781 => 333,  764 => 178,  725 => 164,  632 => 141,  602 => 265,  565 => 117,  529 => 282,  505 => 267,  487 => 213,  473 => 221,  1853 => 842,  1849 => 841,  1839 => 837,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 813,  1778 => 812,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 782,  1705 => 781,  1688 => 770,  1681 => 769,  1677 => 768,  1667 => 764,  1656 => 762,  1648 => 757,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 714,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 695,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 651,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 610,  1289 => 607,  1276 => 604,  1263 => 601,  1244 => 596,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 565,  1181 => 561,  1171 => 557,  1158 => 550,  1114 => 530,  1104 => 526,  1072 => 512,  1062 => 508,  1024 => 494,  1014 => 490,  1000 => 482,  990 => 478,  980 => 474,  960 => 466,  918 => 448,  888 => 376,  834 => 346,  673 => 342,  636 => 323,  462 => 222,  454 => 192,  1144 => 542,  1139 => 399,  1131 => 399,  1127 => 397,  1110 => 396,  1092 => 391,  1089 => 390,  1086 => 520,  1084 => 388,  1063 => 387,  1060 => 386,  1055 => 385,  1050 => 384,  1035 => 372,  1019 => 267,  1003 => 263,  959 => 251,  900 => 377,  880 => 434,  870 => 430,  867 => 366,  859 => 362,  848 => 215,  839 => 197,  828 => 191,  823 => 188,  809 => 181,  800 => 178,  797 => 177,  794 => 176,  786 => 174,  740 => 162,  734 => 313,  703 => 354,  693 => 350,  630 => 278,  626 => 140,  614 => 138,  610 => 103,  581 => 277,  564 => 229,  525 => 281,  722 => 162,  697 => 256,  674 => 249,  671 => 425,  577 => 257,  569 => 97,  557 => 368,  502 => 242,  497 => 240,  445 => 205,  729 => 159,  684 => 261,  676 => 299,  669 => 254,  660 => 145,  647 => 243,  643 => 244,  601 => 287,  570 => 273,  522 => 200,  501 => 265,  296 => 67,  374 => 183,  631 => 111,  616 => 281,  608 => 266,  605 => 16,  596 => 102,  574 => 165,  561 => 209,  527 => 233,  433 => 93,  388 => 89,  426 => 177,  383 => 182,  461 => 246,  370 => 176,  395 => 221,  294 => 120,  223 => 49,  220 => 79,  492 => 395,  468 => 201,  444 => 193,  410 => 229,  397 => 174,  377 => 84,  262 => 113,  250 => 94,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 2108,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 1383,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 1303,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 1173,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 1041,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 970,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 538,  1126 => 916,  1106 => 900,  1100 => 897,  1026 => 841,  997 => 822,  983 => 816,  975 => 402,  956 => 800,  939 => 786,  902 => 753,  894 => 749,  879 => 373,  757 => 631,  727 => 608,  716 => 605,  670 => 297,  528 => 221,  476 => 253,  435 => 208,  354 => 175,  341 => 212,  192 => 123,  321 => 147,  243 => 151,  793 => 350,  780 => 348,  758 => 177,  700 => 312,  686 => 150,  652 => 274,  638 => 282,  620 => 139,  545 => 259,  523 => 110,  494 => 10,  459 => 226,  438 => 382,  351 => 214,  347 => 173,  402 => 222,  268 => 77,  430 => 201,  411 => 201,  379 => 219,  322 => 74,  315 => 110,  289 => 129,  284 => 128,  255 => 115,  234 => 55,  1133 => 400,  1124 => 534,  1121 => 56,  1116 => 55,  1113 => 403,  1108 => 51,  1103 => 394,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 500,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 329,  996 => 262,  989 => 316,  985 => 315,  981 => 257,  977 => 313,  970 => 91,  966 => 416,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 244,  919 => 290,  917 => 289,  908 => 444,  905 => 378,  896 => 280,  891 => 227,  877 => 270,  862 => 267,  857 => 265,  837 => 347,  832 => 250,  827 => 248,  821 => 247,  803 => 179,  778 => 389,  769 => 165,  765 => 386,  753 => 328,  746 => 175,  743 => 318,  735 => 170,  730 => 218,  720 => 363,  717 => 362,  712 => 156,  691 => 204,  678 => 149,  654 => 144,  587 => 14,  576 => 179,  539 => 241,  517 => 217,  471 => 190,  441 => 239,  437 => 238,  418 => 201,  386 => 195,  373 => 144,  304 => 150,  270 => 169,  265 => 161,  229 => 81,  477 => 138,  455 => 224,  448 => 242,  429 => 179,  407 => 119,  399 => 193,  389 => 176,  375 => 217,  358 => 79,  349 => 118,  335 => 128,  327 => 132,  298 => 144,  280 => 124,  249 => 153,  194 => 78,  142 => 38,  344 => 172,  318 => 181,  306 => 188,  295 => 124,  357 => 154,  300 => 135,  286 => 80,  276 => 87,  269 => 133,  254 => 125,  128 => 66,  237 => 118,  165 => 50,  122 => 73,  798 => 337,  770 => 179,  759 => 112,  748 => 337,  731 => 108,  721 => 305,  718 => 307,  708 => 295,  696 => 147,  617 => 461,  590 => 226,  553 => 264,  550 => 156,  540 => 84,  533 => 255,  500 => 397,  493 => 225,  489 => 257,  482 => 210,  467 => 210,  464 => 215,  458 => 220,  452 => 217,  449 => 132,  415 => 181,  382 => 172,  372 => 215,  361 => 177,  356 => 215,  339 => 139,  302 => 125,  285 => 175,  258 => 71,  123 => 43,  108 => 28,  424 => 198,  394 => 86,  380 => 121,  338 => 155,  319 => 157,  316 => 131,  312 => 87,  290 => 146,  267 => 132,  206 => 82,  110 => 35,  240 => 119,  224 => 87,  219 => 74,  217 => 80,  202 => 126,  186 => 68,  170 => 53,  100 => 26,  67 => 13,  14 => 1,  1096 => 524,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 836,  1013 => 275,  1007 => 274,  1002 => 823,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 402,  940 => 298,  937 => 395,  928 => 452,  926 => 243,  915 => 239,  912 => 236,  903 => 231,  898 => 440,  892 => 229,  889 => 748,  887 => 227,  884 => 374,  876 => 222,  874 => 215,  871 => 368,  863 => 214,  861 => 213,  858 => 212,  850 => 352,  843 => 206,  840 => 406,  815 => 245,  812 => 244,  808 => 199,  804 => 395,  799 => 196,  791 => 193,  785 => 238,  775 => 184,  771 => 183,  754 => 340,  728 => 309,  726 => 169,  723 => 168,  715 => 105,  711 => 152,  709 => 162,  706 => 155,  698 => 208,  694 => 308,  692 => 155,  689 => 302,  681 => 300,  677 => 149,  675 => 148,  663 => 276,  661 => 277,  650 => 246,  646 => 112,  629 => 305,  627 => 108,  625 => 272,  622 => 270,  598 => 174,  592 => 117,  586 => 170,  575 => 98,  566 => 271,  556 => 244,  554 => 240,  541 => 239,  536 => 225,  515 => 209,  511 => 269,  509 => 244,  488 => 208,  486 => 207,  483 => 341,  465 => 223,  463 => 216,  450 => 194,  432 => 211,  419 => 155,  371 => 182,  362 => 80,  353 => 78,  337 => 18,  333 => 134,  309 => 190,  303 => 70,  299 => 148,  291 => 176,  272 => 103,  261 => 163,  253 => 59,  239 => 149,  235 => 87,  213 => 78,  200 => 61,  198 => 95,  159 => 48,  149 => 49,  146 => 41,  131 => 36,  116 => 30,  79 => 18,  74 => 12,  71 => 30,  836 => 320,  817 => 398,  814 => 200,  811 => 317,  805 => 313,  787 => 657,  779 => 169,  776 => 305,  773 => 347,  761 => 296,  751 => 163,  747 => 325,  742 => 336,  739 => 171,  736 => 317,  724 => 214,  705 => 278,  702 => 601,  688 => 113,  680 => 278,  667 => 296,  662 => 293,  656 => 418,  649 => 285,  644 => 284,  641 => 241,  624 => 109,  613 => 460,  607 => 289,  597 => 221,  591 => 263,  584 => 259,  579 => 234,  563 => 96,  559 => 245,  551 => 235,  547 => 95,  537 => 90,  524 => 220,  512 => 227,  507 => 76,  504 => 213,  498 => 142,  485 => 256,  480 => 254,  472 => 225,  466 => 248,  460 => 221,  447 => 215,  442 => 185,  434 => 212,  428 => 204,  422 => 176,  404 => 149,  368 => 81,  364 => 173,  340 => 170,  334 => 211,  330 => 148,  325 => 126,  292 => 142,  287 => 63,  282 => 62,  279 => 111,  273 => 170,  266 => 102,  256 => 89,  252 => 109,  228 => 72,  218 => 86,  201 => 58,  64 => 26,  51 => 11,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 1705,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 737,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 731,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 725,  1559 => 601,  1552 => 720,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 708,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 645,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 624,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 1001,  1257 => 1000,  1254 => 479,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 569,  1196 => 958,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 938,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 388,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 898,  1099 => 393,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 880,  1076 => 359,  1070 => 875,  1057 => 352,  1052 => 504,  1045 => 382,  1040 => 377,  1036 => 283,  1032 => 496,  1028 => 274,  1023 => 393,  1020 => 392,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 821,  979 => 379,  974 => 256,  967 => 373,  962 => 803,  958 => 801,  954 => 253,  950 => 368,  945 => 367,  942 => 460,  938 => 365,  934 => 364,  927 => 361,  923 => 387,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 272,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 410,  841 => 210,  835 => 312,  830 => 249,  826 => 202,  822 => 308,  818 => 246,  813 => 183,  810 => 340,  806 => 180,  802 => 198,  795 => 336,  792 => 239,  789 => 349,  784 => 188,  782 => 187,  777 => 291,  772 => 289,  768 => 334,  763 => 327,  760 => 329,  756 => 230,  752 => 176,  745 => 281,  741 => 280,  738 => 379,  732 => 171,  719 => 279,  714 => 264,  710 => 310,  704 => 267,  699 => 303,  695 => 285,  690 => 263,  687 => 279,  683 => 346,  679 => 298,  672 => 255,  668 => 247,  665 => 201,  658 => 244,  645 => 270,  640 => 142,  634 => 413,  628 => 466,  623 => 107,  619 => 298,  611 => 268,  606 => 279,  603 => 267,  599 => 242,  595 => 132,  583 => 169,  580 => 99,  573 => 274,  560 => 268,  543 => 172,  538 => 257,  534 => 405,  530 => 202,  526 => 213,  521 => 226,  518 => 22,  514 => 222,  510 => 202,  503 => 266,  496 => 216,  490 => 214,  484 => 394,  474 => 202,  470 => 231,  446 => 241,  440 => 218,  436 => 113,  431 => 186,  425 => 193,  416 => 231,  412 => 230,  408 => 196,  403 => 194,  400 => 225,  396 => 299,  392 => 198,  385 => 186,  381 => 185,  367 => 180,  363 => 164,  359 => 79,  355 => 326,  350 => 94,  346 => 140,  343 => 143,  328 => 152,  324 => 164,  313 => 192,  307 => 108,  301 => 149,  288 => 116,  283 => 62,  271 => 106,  257 => 112,  251 => 58,  238 => 84,  233 => 116,  195 => 59,  191 => 92,  187 => 46,  183 => 59,  130 => 43,  88 => 28,  76 => 33,  115 => 73,  95 => 38,  655 => 275,  651 => 232,  648 => 231,  637 => 241,  633 => 272,  621 => 462,  618 => 269,  615 => 268,  604 => 186,  600 => 516,  588 => 305,  585 => 261,  582 => 260,  571 => 242,  567 => 95,  555 => 207,  552 => 190,  549 => 208,  544 => 230,  542 => 290,  535 => 256,  531 => 254,  519 => 87,  516 => 248,  513 => 216,  508 => 268,  506 => 401,  499 => 241,  495 => 239,  491 => 145,  481 => 231,  478 => 235,  475 => 184,  469 => 197,  456 => 197,  451 => 243,  443 => 132,  439 => 129,  427 => 185,  423 => 109,  420 => 208,  409 => 178,  405 => 148,  401 => 148,  391 => 173,  387 => 129,  384 => 160,  378 => 145,  365 => 161,  360 => 171,  348 => 191,  336 => 135,  332 => 150,  329 => 73,  323 => 135,  310 => 130,  305 => 69,  277 => 172,  274 => 135,  263 => 59,  259 => 100,  247 => 87,  244 => 84,  241 => 150,  222 => 133,  210 => 68,  207 => 77,  204 => 54,  184 => 8,  181 => 40,  167 => 99,  157 => 91,  96 => 24,  421 => 233,  417 => 71,  414 => 199,  406 => 170,  398 => 147,  393 => 177,  390 => 90,  376 => 85,  369 => 181,  366 => 174,  352 => 192,  345 => 213,  342 => 171,  331 => 140,  326 => 102,  320 => 130,  317 => 134,  314 => 136,  311 => 191,  308 => 141,  297 => 147,  293 => 119,  281 => 174,  278 => 106,  275 => 124,  264 => 103,  260 => 99,  248 => 95,  245 => 57,  242 => 94,  231 => 100,  227 => 113,  215 => 88,  212 => 130,  209 => 88,  197 => 41,  177 => 103,  171 => 100,  161 => 63,  132 => 47,  121 => 49,  105 => 32,  99 => 29,  81 => 19,  77 => 17,  180 => 56,  176 => 70,  156 => 55,  143 => 40,  139 => 83,  118 => 39,  189 => 68,  185 => 120,  173 => 54,  166 => 41,  152 => 35,  174 => 41,  164 => 113,  154 => 80,  150 => 87,  137 => 48,  133 => 81,  127 => 34,  107 => 35,  102 => 44,  83 => 25,  78 => 34,  53 => 12,  23 => 6,  42 => 8,  138 => 44,  134 => 46,  109 => 33,  103 => 29,  97 => 42,  94 => 24,  84 => 20,  75 => 31,  69 => 30,  66 => 28,  54 => 27,  44 => 10,  230 => 83,  226 => 97,  203 => 92,  193 => 93,  188 => 121,  182 => 119,  178 => 56,  168 => 114,  163 => 4,  160 => 39,  155 => 77,  148 => 58,  145 => 47,  140 => 85,  136 => 82,  125 => 78,  120 => 72,  113 => 59,  101 => 24,  92 => 25,  89 => 23,  85 => 17,  73 => 32,  62 => 17,  59 => 13,  56 => 18,  41 => 6,  126 => 65,  119 => 41,  111 => 46,  106 => 45,  98 => 33,  93 => 40,  86 => 22,  70 => 14,  60 => 11,  28 => 1,  36 => 4,  114 => 69,  104 => 36,  91 => 35,  80 => 20,  63 => 12,  58 => 28,  40 => 6,  34 => 21,  45 => 23,  61 => 14,  55 => 11,  48 => 19,  39 => 7,  35 => 4,  31 => 8,  26 => 4,  21 => 2,  46 => 7,  29 => 1,  57 => 6,  50 => 8,  47 => 15,  38 => 5,  33 => 9,  49 => 25,  32 => 3,  246 => 152,  236 => 91,  232 => 107,  225 => 82,  221 => 110,  216 => 65,  214 => 47,  211 => 46,  208 => 128,  205 => 127,  199 => 84,  196 => 91,  190 => 58,  179 => 7,  175 => 41,  172 => 116,  169 => 54,  162 => 49,  158 => 67,  153 => 45,  151 => 39,  147 => 61,  144 => 86,  141 => 2,  135 => 83,  129 => 1,  124 => 74,  117 => 70,  112 => 72,  90 => 22,  87 => 39,  82 => 32,  72 => 17,  68 => 20,  65 => 25,  52 => 16,  43 => 7,  37 => 6,  30 => 8,  27 => 1,  25 => 15,  24 => 4,  22 => 2,  19 => 1,);
    }
}
