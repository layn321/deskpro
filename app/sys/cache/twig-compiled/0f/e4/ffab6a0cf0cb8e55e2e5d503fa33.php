<?php

/* UserBundle:Css:main.css.twig */
class __TwigTemplate_0fe4ffab6a0cf0cb8e55e2e5d503fa33 extends \Application\DeskPRO\Twig\Template
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
        // line 8
        echo "
/*
 * DeskPRO http://www.deskpro.com/
 * CSS generated ";
        // line 11
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, "now", "Y-m-d H:i:s"), "html", null, true);
        echo "
 */
";
        // line 13
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $context["stylevar"] = $this->getAttribute($_app_, "getSettingGroup", array(0 => "user_style"), "method");
        // line 14
        echo "
";
        // line 15
        $context["page_width"] = 1150;
        // line 16
        $context["font_headings"] = "Helvetica,arial,freesans,clean,sans-serif";
        // line 17
        $context["font_body"] = "\"Helvetica Neue\", Helvetica, Arial, sans-serif";
        // line 18
        echo "
";
        // line 26
        echo "
";
        // line 44
        echo "
";
        // line 50
        echo "
";
        // line 55
        echo "
";
        // line 56
        $this->env->loadTemplate("UserBundle:Css:bootstrap.css.twig")->display($context);
        // line 57
        echo "
/***********************************************************************************************************************
* DeskPRO customizations or additions made to Twitter Bootstrap
***********************************************************************************************************************/

#dp form {
\tmargin: 0;
}

#dp .dp-icon-thumbup {
\tbackground: transparent url(";
        // line 67
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/thumb-up.png) no-repeat 0 0;
}

#dp .dp-badge:hover {
\tcursor: default;
}

#dp a.dp-badge:hover {
\tcursor: pointer;
}

#dp .dp-badge.dp-small {
\tposition: relative;
\ttop: -1px;
\tfont-size: 10px;
\tpadding: 0px 4px;
}

#dp .dp-content-post ul > li{list-style:disc;}

/*@no_rtl*/
.rtl #dp .dp-popover { left: auto; }

.rtl #dp .dp-popover.dp-top .dp-arrow,
.rtl #dp .dp-popover.dp-bottom .dp-arrow{
\tmargin-left: 0;
}
/*@/no_rtl*/

#dp .dp-nav li .dp-info-prefix {
\tcolor: #838383;
}
#dp .dp-nav li.dp-open .dp-info-prefix {
\tcolor: #fff;
}

#dp .dp-nav li.dp-label-tab {
\tfloat :left;
\tpadding: line-height: 18px;
\tpadding: 8px;
\tpadding-top: 9px;
}

#dp .dp-nav li.dp-label-tab em {
\tfont-style: normal;
\tfont-size: 11px;
\tfont-weight: bold;
\tcolor: #737373;
}

#dp .dp-dropdown-element { position:absolute;top:100%;left:0;z-index:1000;float:left;display:none;min-width:160px;padding:4px 0;margin:0;list-style:none;background-color:#ffffff;border-color:#ccc;border-color:rgba(0, 0, 0, 0.2);border-style:solid;border-width:1px;-webkit-border-radius:0 0 5px 5px;-moz-border-radius:0 0 5px 5px;border-radius:0 0 5px 5px;-webkit-box-shadow:0 5px 10px rgba(0, 0, 0, 0.2);-moz-box-shadow:0 5px 10px rgba(0, 0, 0, 0.2);box-shadow:0 5px 10px rgba(0, 0, 0, 0.2);-webkit-background-clip:padding-box;-moz-background-clip:padding;background-clip:padding-box;*border-right-width:2px;*border-bottom-width:2px; }
#dp .dp-dropdown-element {
\t";
        // line 119
        echo $this->getAttribute($this, "border_radius", array(0 => 6), "method");
        echo "
}
#dp .dp-dropdown-element.dp-pull-right {
\t";
        // line 122
        echo $this->getAttribute($this, "border_radius", array(0 => array("tr" => 0)), "method");
        echo "
}
#dp .dp-dropdown-element.dp-pull-right{right:0;left:auto;}
#dp .dp-dropdown.dp-open .dp-dropdown-element { display: block; }

#dp .dp-dropdown.dp-alt-option > a {
\tbackground-color:#ffffff;
\tborder:1px solid #ddd;
\t";
        // line 130
        echo $this->getAttribute($this, "border_radius", array(0 => 0), "method");
        echo "
\tborder-left: none;
\tmargin-right: 0;
}

#dp .dp-dropdown.dp-alt-option.dp-first > a {
\tborder-left: 1px solid #ddd;
\t";
        // line 137
        echo $this->getAttribute($this, "border_radius", array(0 => array("tl" => 4)), "method");
        echo "
}
#dp .dp-dropdown.dp-alt-option.dp-last > a {
\t";
        // line 140
        echo $this->getAttribute($this, "border_radius", array(0 => array("tr" => 4)), "method");
        echo "
}

#dp .dp-dropdown {
\tposition: relative;
}

#dp .dp-nav-thicksep {
\tborder-bottom-width: 2px;
}

#dp .dp-nav-thicksep > li.dp-active, #dp .dp-nav-thicksep > li:hover { margin-bottom: -2px; }
#dp .dp-nav-thicksep > li.dp-active > a, #dp .dp-nav-thicksep > li:hover > a { border-bottom-width: 2px; }

#dp .dp-nav-thicksep > li.dp-active.dp-alt-option, #dp .dp-nav-thicksep > li:hover { margin-bottom: -1px; }
#dp .dp-nav-thicksep > li.dp-active.dp-alt-option > a, #dp .dp-nav-thicksep > li:hover > a { border-bottom-width: 1px; }


#dp .dp-dropdown.dp-alt-option .dp-dropdown-element {
\tmargin-left: 0;
\t";
        // line 160
        echo $this->getAttribute($this, "border_radius", array(0 => array("tl" => 0)), "method");
        echo "
}

#dp .dp-well-dark {
\tbackground-color: ";
        // line 164
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "dark_well_bg_color"), "html", null, true);
        echo ";
}

#dp .dp-well-light {
\tbackground-color: ";
        // line 168
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "light_well_bg_color"), "html", null, true);
        echo ";
\t";
        // line 169
        echo $this->getAttribute($this, "box_shadow", array(0 => "inset 0 0 3px #999999"), "method");
        echo "
}

#dp .dp-hidden-field {
\tdisplay: none !important;
\theight: 1px;
\toverflow: hidden;
\twidth: 1px;
\tfloat: left;
\tvisibility: hidden;
}

#dp .dp-controls.dp-fill input[type=\"text\"],
#dp .dp-controls.dp-fill input[type=\"password\"],
#dp .dp-controls.dp-fill textarea {
\twidth: 80%;
}

#dp .dp-controls.dp-fill-half input[type=\"text\"],
#dp .dp-controls.dp-fill-half input[type=\"password\"],
#dp .dp-controls.dp-fill-half textarea {
\twidth: 40%;
}

#dp .dp-controls.dp-picture-editor {
\toverflow: hidden;
}

#dp .dp-controls.dp-picture-editor img {
\tfloat: left;
}

#dp .dp-controls.dp-picture-editor div {
\tmargin: 0 0 5px 75px;
}

#dp .dp-error-explain {
\tdisplay: none;
}

#dp .dp-error .dp-error-explain {
\tdisplay: block;
}

#dp .dp-form-vertical .dp-control-group {
\tmargin-top: 20px;
\tmargin-bottom: 20px;
}

#dp .dp-control-group {
\tmargin-bottom: 20px;
}

#dp .dp-pagination {
\ttext-align: center;
}

#dp legend {
\tmargin-bottom: 10px;
}

#dp .dp-control-label aside {
\tdisplay: inline-block;
\tmargin-left: 10px;
\tborder-left: 1px solid #ccc;
\tpadding-left: 8px;
\tfont-size: 12px;
\tcolor: #555;
}

#dp a {
\tcolor: ";
        // line 240
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "link_color"), "html", null, true);
        echo ";
}
#dp a:hover {
\tcolor: ";
        // line 243
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "link_color_hover"), "html", null, true);
        echo ";
}

#dp .dp-popover-content {
\tmax-height: 200px;
\tmax-width: 350px;
}

#dp .dp-popover-inner {
\tmax-width: 350px;
\twidth: auto;
}

#dp input[type=\"text\"].error {
\tborder-color: #B94A48;
\tcolor: #B94A48;
}

#dp .dp-its-a-trap {
\tdisplay: none;
}

/***********************************************************************************************************************
* Some resets that might be applied by some external stylesheets
***********************************************************************************************************************/

#dp input[type=\"image\"], #dp input[type=\"checkbox\"], #dp input[type=\"radio\"] {
\tdisplay: inline;
}

#dp input, #dp textarea {
\t-moz-box-sizing: content-box;
\tbox-sizing: content-box;
}

#dp .message {
\ttext-align: left;
\tpadding: 0;
}

/***********************************************************************************************************************
* Tweaks to respond to width of devices
***********************************************************************************************************************/

.dp-device-small {
\tdisplay: none;
}

@media (max-width: 1100px) {  #dp #dp_sidebar_wrapper { width: 230px; }   #dp #dp_content_wrapper { margin-right: 248px; }  #dp input#dp_search { width: 430px !important; } }
@media (max-width: 1000px) {  #dp #dp_sidebar_wrapper { width: 220px; }   #dp #dp_content_wrapper { margin-right: 238px; }  #dp input#dp_search { width: 420px !important; } }
@media (max-width: 900px) {   #dp #dp_sidebar_wrapper { width: 210px; }   #dp #dp_content_wrapper { margin-right: 228px; }  #dp input#dp_search { width: 410px !important; } }
@media (max-width: 860px) {   #dp #dp_sidebar_wrapper { width: 180px; }   #dp #dp_content_wrapper { margin-right: 198px; }  #dp input#dp_search { width: 400px !important; } }

@media (max-width: 840px) {
\t#dp #dp_sidebar_wrapper { display: none; }
\t#dp #dp_content_wrapper { margin-right: 0; }
\t#dp input#dp_search { width: 320px !important; }
\t.dp-device-small { display: block; }
}

#dp_main_inner {
\tmin-width: 745px;
}

.dp-topbar-block {
\tbackground-color: #fff;
\tborder: 1px solid #C7C5C5;
\t";
        // line 310
        echo $this->getAttribute($this, "border_radius", array(0 => 6), "method");
        echo "
\tpadding: 10px;
\tmargin-bottom: 15px;
}

@media (max-width: 1100px) {
\t#dp .dp-login-box-inner input[type=\"text\"], #dp .dp-login-box-inner input[type=\"password\"] {
\t\twidth: 95% !important;
\t}
}

/***********************************************************************************************************************
* Disable some elements when using the 'simple' interface
***********************************************************************************************************************/

html#dp_html.dp-in-frame-simple, html#dp_html.dp-in-frame-simple body {
\tbackground: transparent;
}

#dp_html .dp-in-frame #dp_main { max-width: none; }
#dp_html .dp-in-frame #dp_header_bar { max-width: none; }

#dp_html .dp-in-frame-simple #dp_lang_chooser { display: none; }
#dp_html .dp-in-frame-simple #dp_main { margin: 0; }
#dp_html .dp-in-frame-simple #dp_main_inner { margin: 0; }
#dp_html .dp-in-frame-simple #dp_main_inner { margin: 0; }
#dp_html .dp-in-frame-simple #dp_header_bar { display: none; }
#dp_html .dp-in-frame-simple #dp_header { display: none; }
#dp_html .dp-in-frame-simple #dp_page_wrapper { margin: 0; padding: 0; background: transparent; border: none; }
#dp_html .dp-in-frame-simple #dp_sidebar_wrapper { display: none; }
#dp_html .dp-in-frame-simple #dp_content_wrapper { margin-right: 0; }

/***********************************************************************************************************************
* #dp_html
* Applied to the DeskPRO HTML page
***********************************************************************************************************************/

/*
\tThe DeskPRO page structure is like this:

\thtml#dp_html
\t\tbody
\t\t\tdiv#dp_custom_header
\t\t\tdiv#dp
\t\t\t\tdiv#dp_main
\t\t\t\t\tsection#dp_header_bar
\t\t\t\t\t\tdiv#dp_header_title
\t\t\t\t\t\tdiv#dp_header_search
\t\t\t\t\tdiv#dp_page_wrapper
\t\t\t\t\t\tdiv#dp_content_wrapper
\t\t\t\t\t\t\tsection#dp_content
\t\t\t\t\t\tdiv#dp_sidebar_wrapper
\t\t\t\t\t\t\tsection#dp_sidebar
\t\t\t\t\tdiv#dp_footer_bar
 */

html#dp_html, html#dp_html body {
\tbackground: ";
        // line 367
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "bg_color"), "html", null, true);
        echo ";
\tmargin: 0;
\tpadding: 0;
}

html#dp_html.dp-simple-iframe, html#dp_html body.dp-simple-iframe {
\tbackground: transparent !important;
}

#dp {
\tfont-family: ";
        // line 377
        if (isset($context["font_body"])) { $_font_body_ = $context["font_body"]; } else { $_font_body_ = null; }
        echo $_font_body_;
        echo ";
\tfont-size: 13px;
\tline-height: 18px;
\tcolor: ";
        // line 380
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "text_color"), "html", null, true);
        echo ";
\ttext-align: left;
}

#dp #dp_top_notice {
\tmargin: 15px;
\tbackground-color: #C8C8C8;
\tborder: 1px solid #696969;
\t";
        // line 388
        echo $this->getAttribute($this, "border_radius", array(0 => 5), "method");
        echo "
\tpadding: 25px;
}

#dp td {
\tfont-family: ";
        // line 393
        if (isset($context["font_body"])) { $_font_body_ = $context["font_body"]; } else { $_font_body_ = null; }
        echo $_font_body_;
        echo ";
}

#dp h1, #dp h2, #dp h3, #dp h4, #dp h5, #dp h6 {
\tfont-family: ";
        // line 397
        if (isset($context["font_headings"])) { $_font_headings_ = $context["font_headings"]; } else { $_font_headings_ = null; }
        echo $_font_headings_;
        echo ";
\ttext-shadow: 1px 1px 1px rgba(255,255,255, 0.8);
}

#dp_custom_header {

}

#dp_main {
\tmax-width: ";
        // line 406
        if (isset($context["page_width"])) { $_page_width_ = $context["page_width"]; } else { $_page_width_ = null; }
        echo twig_escape_filter($this->env, $_page_width_, "html", null, true);
        echo "px;
\tmargin: 15px auto 15px auto;
}

#dp_main_inner {
\tpadding: 0 18px 0 18px;
}

#dp .dp-clear {
\tclear: both;
\theight: 1px;
\toverflow: hidden;
\tvisibility: hidden;
}

.dp-chat-disabled .dp-chat-trigger { display: none; }
#dp #dp_search_assist .foot ul li.chat { display: none; }
.dp-chat-enabled #dp #dp_search_assist .foot ul li.chat { display: inline-block; }

#dp .dp-flashes {
\tpadding: 10px;
}

#dp i.spinner-flat {
\tdisplay: inline-block;
\twidth: 16px;
\theight: 11px;
\toverflow: hidden;
\tbackground: url(";
        // line 434
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/spinners/loading-small-flat.gif);
}

#dp .dp-no-btn, #dp .dp-no-btn:hover {
\tcursor: default;
\tbackground: #fff;
}

#dp .dp-help-pop.dp-icon-question-sign {
\t";
        // line 443
        echo $this->getAttribute($this, "opacity", array(0 => 0.5), "method");
        echo "
\tcursor: help;
}
#dp .dp-help-pop.dp-icon-question-sign:hover {
\t";
        // line 447
        echo $this->getAttribute($this, "opacity", array(0 => 1), "method");
        echo "
}

#dp .dp-link {
\tcursor: pointer;
\tcolor:#0088cc;
}
#dp .dp-link:hover {
\tcolor:#005580;
\ttext-decoration:underline;
}

#dp .dp-controls.dp-fill .customfield.datepicker input {
\twidth: 166px;
}

#dp .customfield.Choice input {
\tmargin-right: 4px;
}

#dp .customfield.Choice label {
\tdisplay: inline;
\tmargin-right: 10px;
}

#dp code.codebox {
\tborder: 1px solid #C8C8C8;
\tbackground-color: #F4F4F4;
\tpadding: 8px;
\tmargin: 8px;
\tdisplay: block;
}

#dp blockquote {
\tmargin: 8px 8px 8px 12px;
\tpadding-left: 5px;
\tborder-left: 3px solid #E7E7E7;
\tfont-style: italic;
\tcolor: #9D9D9D;
}

#dp_lang_chooser {
\ttext-align: right;
\tmargin: 0 25px 6px 0;
\theight: 22px;
}

#dp_lang_chooser label {
\tdisplay: inline;
\tmargin: 0 6px 0 0;
\tpadding: 0;
}

#dp .language-choice {
\tborder: 1px solid #e9e9e9;
\tbackground: #fff;
\tpadding: 2px 2px 2px 4px;
\tbox-sizing: border-box;
\ttext-align: left;
\tcursor: pointer;
}

#dp .language-choice .country-name {
\tdisplay: inline-block; *zoom: 1;
\ttext-align: left;
}

#dp .language-choice .drop {
\t/*@no_rtl*/
\tbackground: url(";
        // line 516
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/select2.png) no-repeat 0 -2px;
\t/*@/no_rtl*/
\tdisplay: inline-block; *zoom: 1;
\twidth: 16px;
\theight: 16px;
}

#dp .language-options-list {
\tdisplay: none;
\tposition: absolute;
\tbackground: white;
\tborder: 1px solid #e9e9e9;
\tmargin: 0;
\tlist-style: none;
\tz-index: 10000;
}

#dp .language-options-list li {
\tcursor: pointer;
\tpadding: 2px 22px 2px 4px;
\twhite-space: nowrap;
}
#dp .language-options-list li:hover {
\tbackground: #f0f0f0;
}
#dp .language-choice .flag, #dp .language-options-list .flag {
\tdisplay: inline-block; *zoom: 1;
\twidth: 16px;
\theight: 11px;
\tvertical-align: baseline;
}

/***********************************************************************************************************************
* Header Bar
* This includes the top blue bar, the helpdesk title, and the search box
***********************************************************************************************************************/

#dp_header {
\tmargin: 9px 0 9px 9px;
\tposition: relative;
}

#dp_header img.logo {
\tdisplay: none;
}

#dp_header.dp-with-logo img.logo {
\tdisplay: block;
}

#dp_header.disabled.dp-with-logo img.logo {
\tdisplay: none;
}

#dp_header.dp-with-logo h1,
#dp_header.dp-with-logo h2 {
\tdisplay: none;
}

#dp_header h1 {
\tfont-size: 14pt;
\tline-height: 100%;
\tfloat: left;
}

#dp_header h1 a {
\tcolor: ";
        // line 582
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "header_name_color"), "html", null, true);
        echo ";
}

#dp_header h2 {
\tborder-left: 1px solid ";
        // line 586
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "header_tagline_color"), "html", null, true);
        echo ";
\tcolor: ";
        // line 587
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "header_tagline_color"), "html", null, true);
        echo ";
\tfont-size: 10pt;
\tpadding: 0;
\tmargin:  0;
\tline-height: 100%;
\tfloat: left;
\tmargin-left: 10px;
\tpadding-left: 10px;
\tmargin-top: -0px;
\tline-height: 20px;
}


";
        // line 600
        $context["dp_header_bar_height"] = 55;
        // line 601
        echo "#dp_header_bar {
\tposition: relative;
\tmax-width: ";
        // line 603
        if (isset($context["page_width"])) { $_page_width_ = $context["page_width"]; } else { $_page_width_ = null; }
        echo twig_escape_filter($this->env, $_page_width_, "html", null, true);
        echo "px;
\theight: ";
        // line 604
        if (isset($context["dp_header_bar_height"])) { $_dp_header_bar_height_ = $context["dp_header_bar_height"]; } else { $_dp_header_bar_height_ = null; }
        echo twig_escape_filter($this->env, $_dp_header_bar_height_, "html", null, true);
        echo "px;
\tbackground-color: ";
        // line 605
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "header_bar_bg_color"), "html", null, true);
        echo ";
\tcolor: ";
        // line 606
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "header_text_color"), "html", null, true);
        echo ";
\tborder-bottom: 2px solid #1A4A7A;
\t";
        // line 608
        echo $this->getAttribute($this, "border_radius", array(0 => array("tl" => 6, "tr" => 6)), "method");
        echo "
\toverflow: hidden;
}

#dp_header_bar h1 {
\tline-height: ";
        // line 613
        if (isset($context["dp_header_bar_height"])) { $_dp_header_bar_height_ = $context["dp_header_bar_height"]; } else { $_dp_header_bar_height_ = null; }
        echo twig_escape_filter($this->env, ($_dp_header_bar_height_ + 5), "html", null, true);
        echo "px;
\tpadding: 0;
\tmargin: 0;
\tpadding-left: 15px;
\ttext-shadow: 0px 1px 1px rgba(0,0,0, 0.5);
}

section#dp_header_search {
\tposition: absolute;
\tright: 15px;
\ttop: 12px;
}

section#dp_header_search input#dp_search {
\twidth: 440px;
\tpadding: 7px;
\tmargin-right: 10px;
\tfont-size: 11pt;
\tbackground: #fff url(";
        // line 631
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/mag-left.png) no-repeat 10px 50%;
\tpadding-left: 30px;
}

/*@no_rtl*/
.rtl section#dp_header_search input#dp_search {
\tbackground-position: 98% 50%;
}
/*@/no_rtl*/

section#dp_header_search input#dp_search::-webkit-input-placeholder { color: #555; }
section#dp_header_search input#dp_search:-moz-placeholder { color: #555; }

section#dp_header_search .dp-btn {
\tpadding: 7px 12px;
\tfont-size: 11pt;
}

#dp #dp_search_assist {
\tposition: absolute;
\tz-index: 100;
\tbox-shadow: 3px 1px 7px 0px rgba(120, 120, 120, 0.8);
\tborder: 1px solid #B4B6B8;
\tborder-top: none;

\tz-index: 100000;
\t";
        // line 657
        echo $this->getAttribute($this, "border_radius", array(0 => 6), "method");
        echo "
\toverflow: hidden;
}

#dp #dp_search_assist .dp-more-link {
\tdisplay: none;
\tpadding: 0 0 10px 0;
\ttext-align: center;
}

#dp #dp_search_assist {

}

#dp #dp_search_assist .results {
\tbackground-color: #fff;
}

#dp #dp_search_assist .results ul {
\tmargin: 0;
\tpadding: 0;
}

#dp #dp_search_assist .results ul li {
\tborder-bottom:  1px solid #D9DBDB;
\tlist-style: none;
\tmargin: 0;
}

#dp #dp_search_assist .results ul li .top-row {
\theight: 24px;
\toverflow: hidden;
\tposition: relative;
}

#dp #dp_search_assist .results ul li .top-row .fadeaway {
\tposition: absolute;
\ttop: 0;
\tright: 0;
\tbottom: 0;
\twidth: 60px;

\tbackground: -moz-linear-gradient(left,  rgba(255,255,255,0) 0%, rgba(255,255,255,0) 49%, rgba(255,255,255,1) 100%);
\tbackground: -webkit-gradient(linear, left top, right top, color-stop(0%,rgba(255,255,255,0)), color-stop(49%,rgba(255,255,255,0)), color-stop(100%,rgba(255,255,255,1)));
\tbackground: -webkit-linear-gradient(left,  rgba(255,255,255,0) 0%,rgba(255,255,255,0) 49%,rgba(255,255,255,1) 100%);
\tbackground: -o-linear-gradient(left,  rgba(255,255,255,0) 0%,rgba(255,255,255,0) 49%,rgba(255,255,255,1) 100%);
\tbackground: -ms-linear-gradient(left,  rgba(255,255,255,0) 0%,rgba(255,255,255,0) 49%,rgba(255,255,255,1) 100%);
\tbackground: linear-gradient(left,  rgba(255,255,255,0) 0%,rgba(255,255,255,0) 49%,rgba(255,255,255,1) 100%);
\tfilter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#00ffffff', endColorstr='#ffffff',GradientType=1 );
}

#dp #dp_search_assist .results ul .top-row:hover .fadeaway {
\tbackground: -moz-linear-gradient(left,  rgba(47,127,208,0) 0%, rgba(47,127,208,0) 49%, rgba(47,127,208,1) 100%);
\tbackground: -webkit-gradient(linear, left top, right top, color-stop(0%,rgba(47,127,208,0)), color-stop(49%,rgba(47,127,208,0)), color-stop(100%,rgba(47,127,208,1)));
\tbackground: -webkit-linear-gradient(left,  rgba(47,127,208,0) 0%,rgba(47,127,208,0) 49%,rgba(47,127,208,1) 100%);
\tbackground: -o-linear-gradient(left,  rgba(47,127,208,0) 0%,rgba(47,127,208,0) 49%,rgba(47,127,208,1) 100%);
\tbackground: -ms-linear-gradient(left,  rgba(47,127,208,0) 0%,rgba(47,127,208,0) 49%,rgba(47,127,208,1) 100%);
\tbackground: linear-gradient(left,  rgba(47,127,208,0) 0%,rgba(47,127,208,0) 49%,rgba(47,127,208,1) 100%);
\tfilter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#002f7fd0', endColorstr='#2f7fd0',GradientType=1 );
}

#dp #dp_search_assist .results ul li i {
\tdisplay: none;
}

#dp #dp_search_assist .results ul li .summary {
\tdisplay: none;
}

#dp #dp_search_assist .results ul li a {
\tdisplay: block;
\tpadding: 3px 3px 3px 8px;
}

#dp #dp_search_assist .results ul li a:hover {
\tbackground-color: #2F7FD0;
\tcolor: #fff;
\ttext-decoration: none;
}

#dp #dp_search_assist .results ul li a:hover span {
\tcolor: #fff;
}

#dp #dp_search_assist .results ul li a span {
\tbackground-position: 0 50%;
\tbackground-repeat: no-repeat;
\tpadding: 1px 0 1px 19px;
}
#dp #dp_search_assist .results ul li.article a span { background-image: url(";
        // line 746
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/page.png); }
#dp #dp_search_assist .results ul li.news a span    { background-image: url(";
        // line 747
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/newspaper.png); }
#dp #dp_search_assist .results ul li.feedback a span    { background-image: url(";
        // line 748
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/bulb.png); }
#dp #dp_search_assist .results ul li.more a span    { background-image: url(";
        // line 749
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/magnifier.png); }

#dp #dp_search_assist .foot {
\tpadding: 8px 8px 15px 8px;
\tbackground-color: ";
        // line 753
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "body_bg_color"), "html", null, true);
        echo ";
\tborder-radius: 0 0 5px 5px;
}

#dp #dp_search_assist .foot h4 {
\tfont-size: 13px;
\tpadding: 5px;
}

#dp #dp_search_assist .foot ul {
\tpadding: 0;
\tmargin: 8px auto 0 auto;
\ttext-align: center;
}

#dp #dp_search_assist .foot ul li {
\tdisplay: inline-block;
\tmin-width: 70px;
\twidth: 30%;
\ttext-align: center;
\tmargin-right: 6px;
}

#dp #dp_search_assist .foot ul li a {
\tdisplay: block;
\tpadding: 5px 8px 5px 8px;
\tbackground-color: #fff;
\tborder: 1px solid #C1C5C5;
\tcolor: #686868;
\tfont-family:\"Helvetica Neue\",Helvetica,Arial,sans-serif;
\tfont-size: 12px;
\tfont-weight: bold;

\t";
        // line 786
        echo $this->getAttribute($this, "border_radius", array(0 => 6), "method");
        echo "
}

#dp #dp_search_assist .foot ul li a:hover {
\tborder-color: #9B9E9F;
\ttext-decoration: none;
}


/***********************************************************************************************************************
* Layout
* These rules just define the layout of the two columns (page column and the sidebar)
***********************************************************************************************************************/

";
        // line 800
        $context["dp_sidebar_width"] = 250;
        // line 801
        echo "
#dp_page_wrapper {
\tbackground-color: ";
        // line 803
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "body_bg_color"), "html", null, true);
        echo ";
\tpadding: 18px;
\tpadding-bottom: 15px;
\tborder: 1px solid #B2B1B1;
\tborder-top: none;
}

#dp_sidebar_wrapper {
\tfloat: right;
\twidth: ";
        // line 812
        if (isset($context["dp_sidebar_width"])) { $_dp_sidebar_width_ = $context["dp_sidebar_width"]; } else { $_dp_sidebar_width_ = null; }
        echo twig_escape_filter($this->env, $_dp_sidebar_width_, "html", null, true);
        echo "px;
}

#dp_content_wrapper {
\tmargin-right: ";
        // line 816
        if (isset($context["dp_sidebar_width"])) { $_dp_sidebar_width_ = $context["dp_sidebar_width"]; } else { $_dp_sidebar_width_ = null; }
        echo twig_escape_filter($this->env, ($_dp_sidebar_width_ + 18), "html", null, true);
        echo "px;
\tmargin-bottom: 15px;
}

#dp_content {
\tbackground-color: ";
        // line 821
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "content_bg_color"), "html", null, true);
        echo ";
\tborder: 1px solid ";
        // line 822
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "content_border_color"), "html", null, true);
        echo ";
\t";
        // line 823
        echo $this->getAttribute($this, "border_radius", array(0 => array("tl" => 5, "tr" => 5, "bl" => 5, "br" => 5)), "method");
        echo "
\toverflow: visible;// to create float container
}

#dp_breadcrumb_wrap {
\tposition: relative;
\toverflow: hidden;
}

#dp_breadcrumb_wrap .dp-breadcrumb {
\tborder-top: none;
\tborder-left: none;
\tborder-right: none;
\t";
        // line 836
        echo $this->getAttribute($this, "border_radius", array(0 => array("tl" => 6, "tr" => 6, "bl" => 0, "br" => 0)), "method");
        echo "
\twhite-space: nowrap;
}

#dp_breadcrumb_wrap .dp-breadcrumb li i {
\t";
        // line 841
        echo $this->getAttribute($this, "opacity", array(0 => 0.7), "method");
        echo "
\twhite-space: nowrap;
}

#dp_breadcrumb_wrap .dp-breadcrumb-fade {
\tposition: absolute;
\ttop: 0px;
\tright: 0;
\tbottom: 23px;
\twidth: 60px;

\t";
        // line 852
        echo $this->getAttribute($this, "border_radius", array(0 => array("tr" => 6)), "method");
        echo "

\tbackground: transparent url(";
        // line 854
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/breadcrumb-fade.png) no-repeat 100% 0;
}

#dp_footer {
\tclear: both;
\tmargin: 0;
\tpadding: 0;
\tline-height: 100%;
}

#dp_footer .dp-copy {
\tcolor: #B2B2B2;
\tfont-size: 9pt;
\tpadding-left: 10px;
}

#dp_footer .dp-copy a {
\tline-height: 16px;
\tdisplay: block;
\tcolor: #B2B2B2;
\ttext-decoration: none;
\tbackground: url(";
        // line 875
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/dp-logo-bw-16.png) no-repeat 0 50%;
\tpadding: 2px 0 2px 20px;
}

#dp_footer .dp-copy a:hover {
\tbackground-image: url(";
        // line 880
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/dp-logo-16.png);
}

#dp_footer .dp-copy a strong {
\tfont-weight: bold;
\ttext-decoration: underline;
}

#dp_footer .dp-copy a:hover {
\tcolor: #000;
}

/***********************************************************************************************************************
* Main Nav Tabs
* These are the big tabs at the top of the page for Home, Knowledgebase, Feedback and Contact Us
***********************************************************************************************************************/

";
        // line 897
        $context["dp_content_tab_height"] = 45;
        // line 898
        echo "
nav#dp_content_tabs {
\theight: ";
        // line 900
        if (isset($context["dp_content_tab_height"])) { $_dp_content_tab_height_ = $context["dp_content_tab_height"]; } else { $_dp_content_tab_height_ = null; }
        echo twig_escape_filter($this->env, ($_dp_content_tab_height_ + 2), "html", null, true);
        echo "px;
\toverflow: hidden;
\tposition: relative;
\ttop: 1px;
\tz-index: 2;
}
nav#dp_content_tabs > ul {
\tmargin: 0;
\tpadding: 0;
}

nav#dp_content_tabs > ul > li {
\tdisplay: block;
\tfloat: left;
\tmargin-top: 3px;
\tmargin-left: 10px;
\theight: ";
        // line 916
        if (isset($context["dp_content_tab_height"])) { $_dp_content_tab_height_ = $context["dp_content_tab_height"]; } else { $_dp_content_tab_height_ = null; }
        echo twig_escape_filter($this->env, ($_dp_content_tab_height_ - 3), "html", null, true);
        echo "px;
\toverflow: hidden;

\tborder: 1px solid #c9d0d8;
\tborder-bottom: 1px solid ";
        // line 920
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "content_border_color"), "html", null, true);
        echo ";

\tbackground-color: ";
        // line 922
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "content_bg_color"), "html", null, true);
        echo ";
\t";
        // line 923
        echo $this->getAttribute($this, "border_radius", array(0 => array("tr" => 6, "tl" => 6)), "method");
        echo "
\t";
        // line 924
        echo $this->getAttribute($this, "box_shadow", array(0 => "1px 0 2px rgba(0,0,0, 0.2)"), "method");
        echo "
\tbox-sizing: content-box;

\tfont-family: \"Helvetica Neue\",Helvetica,Arial,sans-serif;
\tline-height: 100%;
\tcursor: pointer;
}

nav#dp_content_tabs > ul > li:hover {
\t";
        // line 933
        echo $this->getAttribute($this, "box_shadow", array(0 => "1px 0 2px rgba(0,0,0, 0.4)"), "method");
        echo "
}
nav#dp_content_tabs > ul > li.on {
\t";
        // line 936
        echo $this->getAttribute($this, "box_shadow", array(0 => "1px 0 2px rgba(0,0,0, 0.4)"), "method");
        echo "
\tborder: 1px solid #c9d0d8;
\tborder-bottom: 1px solid ";
        // line 938
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "content_bg_color"), "html", null, true);
        echo ";
\tcursor: pointer;
}

nav#dp_content_tabs > ul > li:hover h3 {
\ttext-decoration: underline;
}

nav#dp_content_tabs a, nav#dp_content_tabs a:hover {
\tcolor: inherit;
\ttext-decoration: none;
\tdisplay: block;
}

nav#dp_content_tabs h3 {
\tmargin: 0;
\tpadding: 6px 8px 3px 3px;
\tline-height: 100%;
\tfont-size: 15px;
\tfont-weight: bold;
\tcolor: ";
        // line 958
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "header_tabs_link_color"), "html", null, true);
        echo ";
\ttext-decoration: none;
\tdisplay: inline-block;
\tzoom:1;
\t*display:inline;
}
nav#dp_content_tabs h3  {
\tline-height: 100%;
\tborder-bottom: 2px solid transparent;
}

nav#dp_content_tabs i {
\t";
        // line 970
        echo $this->getAttribute($this, "opacity", array(0 => 0.7), "method");
        echo "
\tmargin-left: 8px;
}

nav#dp_content_tabs label {
\tline-height: 100%;
\tmargin: 0;
\tfont-size: 11px;
\tcolor: ";
        // line 978
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "meta_text_color"), "html", null, true);
        echo ";
\tpadding: 0 8px 0 8px;
}

/***********************************************************************************************************************
* Sidebar
* This contains the style for sidebar and sidebar blocks
***********************************************************************************************************************/

#dp .dp-no-tabs #dp_sidebar_wrapper {
\tpadding-top: 0;
}

#dp_sidebar_wrapper {
\t/* Padding top to align contents of sidebar with that of the page,
\tbecause the tabs at the top of the content column take up space */
\tpadding-top: ";
        // line 994
        if (isset($context["dp_content_tab_height"])) { $_dp_content_tab_height_ = $context["dp_content_tab_height"]; } else { $_dp_content_tab_height_ = null; }
        echo twig_escape_filter($this->env, $_dp_content_tab_height_, "html", null, true);
        echo "px;
}

#dp_sidebar_wrapper section.dp-sidebar-block {
\tmargin-bottom: 15px;
\tbackground-color: ";
        // line 999
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "content_bg_color"), "html", null, true);
        echo ";
\tborder: 1px solid ";
        // line 1000
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "content_border_color"), "html", null, true);
        echo ";
\t";
        // line 1001
        echo $this->getAttribute($this, "border_radius", array(0 => 6), "method");
        echo "
}

#dp_sidebar_wrapper section.dp-sidebar-block > header {
\tborder-bottom: 1px solid #D2D0D0;
}

#dp_sidebar_wrapper section.dp-sidebar-block > header h3 {
\tpadding: 3px 5px 0 6px;
\tmargin: 0;
\tfont-size: 15px;
\tline-height: 25px;
}

#dp_sidebar_wrapper section.dp-sidebar-block > article.dp-written {
\tpadding: 10px;
}

#dp_sidebar_wrapper section.dp-sidebar-block > article.dp-written .timeago {
\tcolor: #888;
\tfont-size: 11px;
}

#dp .dp-sidebar-block.dp-userinfo .dp-profile-image {
\tfloat: right;
\tmargin: 6px 6px 0;
\tline-height: 1px;
\tfont-size: 1px;
}

#dp .dp-sidebar-block.dp-userinfo ul {
\tlist-style-type: none;
\tmargin-left: 8px;
}

#dp .dp-sidebar-block.dp-userinfo ul li i {
\t";
        // line 1037
        echo $this->getAttribute($this, "opacity", array(0 => 0.3), "method");
        echo "
}

#dp .dp-sidebar-block.dp-userinfo ul li:hover i {
";
        // line 1041
        echo $this->getAttribute($this, "opacity", array(0 => 1), "method");
        echo "
}

#dp .dp-sidebar-block.dp-userinfo ul li {
\tmargin: 3px 0;
}

/***********************************************************************************************************************
* Content
* These styles are general styling applied to pages in DeskPRO
***********************************************************************************************************************/

#dp .dp-br-clear {
\tclear: both;
\theight: 1px;
\toverflow: hidden;
}

#dp .dp-summary {
\tfont-size: 90%;
\tcolor: #888;
}

#dp .not-loading { display: block; }
#dp .is-loading { display: none; }

#dp .loading .not-loading { display: none !important; }
#dp .loading .is-loading { display: block !important; }

#dp .dp-show-not-loading { display: block !important; }
#dp .dp-show-is-loading { display: none !important }
#dp .dp-mark-loading .dp-show-not-loading { display: none !important; }
#dp .dp-mark-loading .dp-show-is-loading { display: block !important; }

#dp .dp-backdrop {
\tposition: absolute;
\ttop: 0;
\tright: 0;
\tbottom: 0;
\tleft: 0;
}

#dp .dp-highlight-word {
\tborder-bottom: 1px dotted #706960;
\tcursor: help;
}

/******************************
* Fadeaway containers
******************************/

/*
\tTo get a fading effect on text, you need:
\t1) To know the height of the text line
\t2) The text line must be wrapper in a positioned container
\t3) Along with the text, there must be a dp-fadeaway element that actually draws the fade

\tThe container is overflow:hidden and the text should be whitespace:nowrap. This'll
\tcause the text to overrun the container. The fadeaway element is a absolutely
\tpositioned element over the right side of the text which overlaps and gives us the effect
\tby having left-most opacity at 0, and right-most opacity at 100.

\tThis uses transparent PNG's so if you don't have a white bg, you need to generate a new image.
*/

#dp .dp-fadeaway-container {
\tposition: relative;
\toverflow: hidden;
\twhite-space: nowrap;
}

#dp .dp-fadeaway-container .dp-fadetitle {
\twhite-space: nowrap;
}

#dp .dp-fadeaway-container .dp-fadeaway-inner-container {
\tposition: absolute;
\ttop: 0;
\tright: 0;
\tbottom: 0;
}

#dp .dp-fadeaway-container .dp-fadeaway-inner-container .dp-opaque-fadeaway {
\tmargin-left: 53px;
\tbackground-color: #fff;
}

#dp .dp-fadeaway-container .dp-fadeaway-inner-container .dp-fadeaway {
\tright: auto;
\tleft: 0;
}

#dp .dp-fadeaway-container .dp-fadeaway {
\twidth: 53px;
\tposition: absolute;
\ttop: 0;
\tright: 0;
\tbottom: 0;
\tbackground: transparent url(";
        // line 1139
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/fadeaway-white.png) repeat-y 100% 0;
}

.rtl #dp .dp-fadeaway-container .dp-fadeaway {
\tbackground-image: url(";
        // line 1143
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/fadeaway-white-rtl.png);
}

/******************************
* Drop zone targets
******************************/

#dp .dp-drop-file-zone {
\tposition: absolute;
\ttop: 0;
\tleft: 0;
\tright: 0;
\tbottom: 0;
\tdisplay: none;

\tbackground-color: rgba(175, 192, 213, 0.7);
\tz-index: 1000000;
\tdisplay: none;
\t";
        // line 1161
        echo $this->getAttribute($this, "border_radius", array(0 => 5), "method");
        echo "
}

#dp .dp-drop-file-zone h1 {
\tmargin: 10px auto 0 auto;
\twidth: 300px;
\ttext-align: center;
\tpadding: 25px;
\tbackground-color: #AFC0D5;
\tfont-size: 18px;
\tborder: 1px solid #66717F;
\tline-height: 130%;
\t";
        // line 1173
        echo $this->getAttribute($this, "border_radius", array(0 => 5), "method");
        echo "
}

body.file-drag-over #dp .dp-drop-file-zone,
#dp body.file-drag-over .dp-drop-file-zone {
\tdisplay: block;
}

/******************************
* A person list is a list with a person avatar, and a name.
* Options: .dp-compact turns the list into just an avatar shown side-by-side
******************************/

#dp ul.dp-person-list {
\tmargin: 0;
\tpadding: 0;
}

#dp ul.dp-person-list li {
\tlist-style: none;
\tmargin: 0;
\tpadding: 0;
}

#dp ul.dp-person-list li span {
\tdisplay: block;
\tline-height: 30px;
\tbackground-repeat: no-repeat;
\tbackground-position: 0 50%;
\tpadding-left: 30px;
}

#dp ul.dp-person-list.dp-compact li {
\tfloat: left;
\tmargin-right: 6px;
\tmargin-bottom: 6px;
}

#dp ul.dp-person-list.dp-compact li span {
\tpadding-left: 0;
\twidth: 24px;
\theight: 24px;
\ttext-indent: -1000px;
\toverflow: hidden;
}

/******************************
* Intro Box
* The welcome box at the top of pages, like the top of the portal
******************************/

#dp .dp-intro-box {
\tmargin: 30px;
\tmargin-bottom: 30px;
\tbackground-color: ";
        // line 1227
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "dark_well_bg_color"), "html", null, true);
        echo ";
\tborder: 1px solid #DADADA;
\tpadding: 10px;
\t";
        // line 1230
        echo $this->getAttribute($this, "border_radius", array(0 => 6), "method");
        echo ";
}

#dp-offline-cache-note {
\tmargin: 20px 30px;
\tbackground-color: ";
        // line 1235
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "dark_well_bg_color"), "html", null, true);
        echo ";
\tborder: 1px solid #DADADA;
\tpadding: 10px;
\t";
        // line 1238
        echo $this->getAttribute($this, "border_radius", array(0 => 6), "method");
        echo ";
}

#dp_breadcrumb_wrap + #dp-offline-cache-note {
\tmargin-top: 0;
}

/******************************
* Content Listing: General styling applied to lists of content, like a listing of news posts
******************************/

#dp ul.dp-content-list {
\tmargin: 0;
\tpadding: 0;
}

#dp ul.dp-content-list > li {
\tlist-style: none;
\tpadding: 0 0 35px 0;
\tmargin: 15px 0 35px 0;
\tborder-bottom: 1px solid #DDDCDC;
}

#dp ul.dp-content-list > li:last-child {
\tborder-bottom: none;
}

#dp .dp-content-post > header h3 {
\tfont-size: 15pt;
\tfont-weight: normal;
\tmargin: 0;
\tpadding: 0;
\tline-height: 100%;
\tpadding-bottom: 5px;
}

#dp .dp-content-post > header h3.dp-fadeaway-container {
\theight: 20px;
\toverflow: hidden;
\twhite-space: nowrap;
}

#dp .dp-content-post > header > table.dp-layout .dp-feedback-info-wrap {
\tposition: relative;
\tpadding-top: 23px;
}
#dp .dp-content-post > header > table.dp-layout .dp-fadeaway-container {
\tposition: absolute;
\ttop: 0;
\tleft:0;
\tright: 0;
\theight: 23px;
\toverflow: hidden;
\twhite-space: nowrap;
\tline-height: 120%;
}

#dp .dp-content-post > header > table.dp-layout .dp-fadeaway-container h3 {
\tmargin-top: 2px;
}

#dp .dp-download-post > header h3 a {
\tpadding: 6px 6px 1px 6px;
\tbackground-color: #EEEEEE;
\tborder: 1px solid #D8D8D8;
\t";
        // line 1303
        echo $this->getAttribute($this, "border_radius", array(0 => "4"), "method");
        echo "
\ttext-decoration: none;
\tdisplay: inline-block;
\tline-height: 100%;
}

#dp .dp-download-post > header h3 a .icon {
\tbackground: url(";
        // line 1310
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/page_white_put.png) no-repeat;
\tdisplay: inline-block;
\twidth: 16px;
\theight: 16px;
}

#dp .dp-download-post > header h3 a em {
\tfont-size: 11px;
\tcolor: #6B6B6B;
}

#dp .dp-download-post .short-desc {
\tfont-size: 12px;
}

#dp .dp-download-post .short-desc a {
\tfont-style: italic;
\tmargin-left: 8px;
\tcolor: #898888;
\ttext-decoration: underline;
}

#dp .dp-content-post > article {
\tpadding: 15px 0 15px 0;
}

#dp .dp-content-post > article img {
\tmax-width: 98%;
}

#dp .dp-content-post > article p {
\tline-height: 160%;
}

#dp .dp-content-post > footer {
\tpadding: 15px 0 15px 0;
}

#dp ul.dp-post-info {
\tmargin: 0;
\tpadding: 0;
}

#dp ul.dp-post-info li {
\tdisplay: inline-block;
\tzoom:1;
\t*display:inline;
\tmargin-right: 18px;
\tfont-size: 9pt;
\tcolor: ";
        // line 1359
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "meta_text_color"), "html", null, true);
        echo ";
}

#dp ul.dp-post-info li i {
\t";
        // line 1363
        echo $this->getAttribute($this, "opacity", array(0 => 0.5), "method");
        echo "
}

#dp ul.dp-post-info li a {
\tcolor: #898888;
\ttext-decoration: underline;
}

#dp .dp-related-list h5 {
\tfont-size: 9pt;
}

#dp .dp-related-list ul li {
\tlist-style: disc;
\tfont-size: 9pt;
}

#dp section.dp-labels-box {
\tborder: 1px solid #D2D0D0;
\tpadding: 0;
\t";
        // line 1383
        echo $this->getAttribute($this, "border_radius", array(0 => 5), "method");
        echo "
\tfont-size: 9pt;
\tmargin-bottom: 10px;
}

#dp section.dp-labels-box ul {
\tmargin: 0;
\tpadding: 0;
}

#dp section.dp-labels-box ul li {
\tdisplay: inline-block;
\tmargin: 0;
\tline-height: 100%;
\tpadding: 3px 5px;
}

#dp section.dp-labels-box ul li.dp-caption {
\tborder-right: 1px solid #D2D0D0;
\tpadding: 7px 9px;
}

#dp section.dp-labels-box ul li.dp-tag {
\tbackground-color: #F5F5F5;
\tborder: 1px solid #DEDDDD;
\t";
        // line 1408
        echo $this->getAttribute($this, "border_radius", array(0 => 4), "method");
        echo "
\tmargin-right: 2px;
\tmargin-left: 2px;
}

#dp section.dp-labels-box ul li i {
\t";
        // line 1414
        echo $this->getAttribute($this, "opacity", array(0 => 0.5), "method");
        echo "
\tposition: relative;
\ttop: 1px;
\tleft: 1px;
}

/******************************
* Social Block: Style for the block that contains twitter, facebook and gplus buttons
******************************/

#dp .dp-social-block > table {
\tmargin: 0;
\tpadding: 0;
}

#dp .dp-social-block > table th {
\tfont-weight: bold;
\tvertical-align: middle;
\tpadding-right: 10px;
}

#dp .dp-social-block > table td {
\tpadding: 0;
\tvertical-align: middle;
}

#dp .dp-social-block > table td.dp-title {
\tpadding-right: 10px;
\tfont-weight: bold;
}

/******************************
* Category List: A listing intended to be category titles and an option count
******************************/

#dp ul.dp-cat-list {
\tmargin: 0 0 0 8px;
\tpadding: 0;
}

#dp ul.dp-cat-list.dp-dropdown-element {
\tpadding: 8px;
}

#dp ul.dp-cat-list .dp-badge {
\tunicode-bidi: bidi-override;
}

#dp ul.dp-cat-list li {
\tpadding: 0;
\tmargin: 3px 0 3px 0;
\tlist-style: none;
\tfont-size: 10pt;
\twhite-space: nowrap;
}

#dp ul.dp-cat-list li.sep {
\tborder-top: 1px solid #D2D0D0;
\tmargin: 8px -8px 0 -8px;
\tpadding: 0 0 8px 0;
\theight: 1px;
\toverflow: hidden;
}

#dp ul.dp-cat-list li.sep.thick {
\tborder-top: 2px solid #B6B4B4;
}

#dp ul.dp-cat-list li.dp-sub { padding-left: 20px; position: relative; margin-top: 0; }
#dp ul.dp-cat-list li.dp-sub.dp-depth-2 { padding-left: 35px; }
#dp ul.dp-cat-list li.dp-sub.dp-depth-3 { padding-left: 50px; }
#dp ul.dp-cat-list li.dp-sub.dp-depth-4 { padding-left: 65px; }
#dp ul.dp-cat-list li.dp-sub.dp-depth-5 { padding-left: 80px; }
#dp ul.dp-cat-list li.dp-sub.dp-depth-6 { padding-left: 95px; }

#dp li.dp-sub .dp-sub-mark {
\tposition: absolute;
\ttop: -3px;
\tleft: 5px;
\theight: 11px;
\twidth: 9px;
}

#dp ul.dp-cat-list li i {
\t";
        // line 1498
        echo $this->getAttribute($this, "opacity", array(0 => 0.3), "method");
        echo "
}

#dp ul.dp-cat-list li:hover i {
\t";
        // line 1502
        echo $this->getAttribute($this, "opacity", array(0 => 1), "method");
        echo "
}

#dp ul.dp-cat-list li.dp-download-folder {
\tfont-size: 120%;
\tline-height: 28px;
}

#dp ul.dp-cat-list li.dp-download-folder i.dp-icon-folder-close {
\tposition: relative;
\ttop: 2px;
}

#dp ul.dp-cat-list li.dp-download-folder li.dp-download-folder {
\tfont-size: 100%;
\tline-height: 100%;
}

#dp ul.dp-cat-list li.dp-download-folder li.dp-download-folder i.dp-icon-folder-close {
\tposition: relative;
\ttop: 0;
}

#dp .dp-login-box-inner {
\tmargin: 8px 0 0 0;
}

#dp .dp-sub-mark {
\tdisplay: inline-block;
\tborder-left: 1px solid #909090;
\tborder-bottom: 1px solid #909090;
\twidth: 5px;
\theight: 5px;
\toverflow: hidden;
}

#dp .dp-login-box-inner .dp-btn-row .back {
\tcursor: pointer;
\tmargin-top: 3px;
\tfont-size: 11px;
}
#dp .dp-login-box-inner .dp-btn-row {
\tmargin-top: 3px;
}
#dp .dp-reset-section .dp-btn-row { text-align: center; margin-top: 12px; }
#dp .dp-login-box-inner .dp-reset-desc { text-align: center; }

#dp .dp-login-box-inner form {
\tmargin: 0;
\tpadding: 0;
}

#dp .dp-login-box-inner input[type=\"text\"],
#dp .dp-login-box-inner input[type=\"password\"] {
\twidth: 218px;
}

#dp .dp-login-box-inner dl {
\tmargin: 0;
\tpadding: 0 0 6px 0;
}

#dp .dp-login-box-inner dt {
\tmargin-top: 5px;
\tmargin-bottom: 3px;
}

#dp .dp-login-box-inner dd {
\tmargin-left: 0;
}

#dp .dp-login-box-inner .dp-btn-row {
\tpadding-top: 4px;
}

#dp .dp-login-box-inner .dp-usersource-list {
\tborder-top: 1px solid #ccc;
\tpadding-top: 7px;
\tmargin-top: 12px;
\tfont-size: 11px;
\ttext-align: right;
}

#dp .dp-login-section .dp-remember-me-row {
\tfloat: right;
\tmargin-top: 5px;
}

#dp .dp-login-section .dp-remember-me-row input {
\tdisplay: inline;
\tposition: relative;
\ttop: -2px;
}


/******************************
* Inline login form
******************************/

#dp .dp-inline-login.open {
\tbackground-color: #F6F6F6;
\tpadding-left: 8px;
\tpadding-right: 8px;
\tborder: 1px solid #DFDFDF;
\t";
        // line 1606
        echo $this->getAttribute($this, "border_radius", array(0 => 5), "method");
        echo "
\tmargin: -1px -9px -1px -9px;
}

#dp .dp-inline-login input[type=\"password\"] {
\twidth: 40%;
\tmargin-bottom: 8px;
}

/******************************
* A table used for design/layout
******************************/

#dp table.dp-layout,
#dp table.dp-layout > thead > tr > td,
#dp table.dp-layout > thead > tr > th,
#dp table.dp-layout > tbody > tr > td,
#dp table.dp-layout > tbody > tr > th {
\tmargin: 0;
\tpadding: 0;
\tborder: 0;
}

/******************************
* Container for showing search results inline
******************************/

#dp .dp-similar-results {
\tbackground-color: #F6F6F6;
\tmargin: 6px;
\tborder: 1px solid #DDDDDD;
\t";
        // line 1637
        echo $this->getAttribute($this, "border_radius", array(0 => 3), "method");
        echo "
}

#dp .dp-similar-results .dp-results ul {
\tmargin: 0;
\tpadding: 0;
}

#dp .dp-similar-results .dp-results ul li {
\tlist-style: none;
\tpadding: 6px 10px 6px 10px;
\tborder-top: 1px solid #DDDDDD;
}

#dp .dp-similar-results .dp-results ul li:first-child {
\tborder-top: none;
}

#dp .dp-similar-results .dp-results ul li .summary {
\tfloat: right;
\tfont-size: 11px;
\tcolor: #676767;
}

/******************************
* Various spinners
******************************/

#dp .dp-loading { display:none; }

#dp .dp-loading-line {
\tbackground: url(";
        // line 1668
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/spinners/loading-big-circle.gif) no-repeat 50% 10px;
\tpadding: 50px 0 10px 0;
\ttext-align: center;
\tfont-size: 12px;
\tcolor: #3C3C3C;
}

/***********************************************************************************************************************
* Portal Section
* This is a master wrapper around page section
***********************************************************************************************************************/

.dp-portal-section {
\tmargin: 15px;
}

#dp section.dp-portal-section {
\tmargin-bottom: 15px;
}

#dp section.dp-portal-section.dp-title-section {
\tmargin-top: 0;
\tmargin-bottom: 0;
}

#dp section.dp-portal-section.dp-title-section > header h1 {
\tmargin-bottom: 10px;
}

#dp section.dp-title-section >article {
\tmargin-top: 0;
}

#dp section.dp-portal-section > header h1 {
\tfont-size: 20pt;
\tfont-weight: normal;
\tfont-family: \"HelveticaNeue-Light\",\"Helvetica Neue Light\",\"Helvetica Neue\",sans-serif;
\tcolor: ";
        // line 1705
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_stylevar_, "big_header_color", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_stylevar_, "big_header_color"), "#B2B1B1")) : ("#B2B1B1")), "html", null, true);
        echo ";
\tmargin-bottom: 25px;
}

#dp section.dp-portal-section > header h3 {
\tfont-size: 13pt;
\tfont-weight: normal;
\tcolor: ";
        // line 1712
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_stylevar_, "big_header_color", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_stylevar_, "big_header_color"), "#B2B1B1")) : ("#B2B1B1")), "html", null, true);
        echo ";
\tmargin-bottom: 15px;
\tmargin-left: 15px;
}

#dp section.dp-portal-section > .dp-content-block {
\tmargin-left: 15px;
}

/***********************************************************************************************************************
* News
* These styles apply to news listings and news posts
***********************************************************************************************************************/

#dp .dp-cal-date {
\tdisplay: block;
\toverflow: hidden;
    width: 46px;
    float: left;
}

#dp .dp-cal-date .dp-month {
    background-color: ";
        // line 1734
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "cal_date_month_bg"), "html", null, true);
        echo ";
    font-size: 9px;
    font-weight: bold;
    color: ";
        // line 1737
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "cal_date_month_text"), "html", null, true);
        echo ";
    text-align: center;

    -moz-border-radius-topleft: 4px;
    -moz-border-radius-topright: 4px;
    -moz-border-radius-bottomright: 0px;
    -moz-border-radius-bottomleft: 0px;
    -webkit-border-radius: 4px 4px 0px 0px;
    border-radius: 4px 4px 0px 0px;
}

#dp .dp-cal-date .dp-day {
    background-color: ";
        // line 1749
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "cal_date_bg"), "html", null, true);
        echo ";
    border: 2px solid ";
        // line 1750
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "cal_date_border"), "html", null, true);
        echo ";
    border-top: none;
    text-align: center;
    color: ";
        // line 1753
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "cal_date_text"), "html", null, true);
        echo ";
    font-family: 'Myriad Pro','Myriad',helvetica,arial,sans-serif;
    font-size: 13px;
    padding-top: 2px;

    -moz-border-radius-topleft: 0px;
    -moz-border-radius-topright: 0px;
    -moz-border-radius-bottomright: 4px;
    -moz-border-radius-bottomleft: 4px;
    -webkit-border-radius: 0px 0px 4px 4px;
    border-radius: 0px 0px 4px 4px;
}

#dp section.dp-news-post header {
\tmargin-left: 54px;
}

#dp section.dp-news-post > article {
\tclear: left;
}

/***********************************************************************************************************************
* Articles
* These styles apply to article lists and articles
***********************************************************************************************************************/

#dp .dp-articles-block ul.dp-content-list > li {
\tmargin: 0;
\tpadding: 10px 0 10px 0;
}

#dp .dp-articles-block ul.dp-content-list > li:first-child {
\tpadding-top: 5px;
}

#dp .dp-question p {
\tfont-size: 11pt;
\tpaddding: 0;
\tmargin-bottom: 15px;
\tpadding-bottom: 15px;
\tcolor: #333333;
\tborder-bottom: 1px solid #D2D0D0;
}

/***********************************************************************************************************************
* Feedback
***********************************************************************************************************************/

#dp .dp-new-feedback-section > article {
\tmargin: 0;
}

#dp .dp-new-feedback {
\tmargin: 15px;
\tmargin-bottom: 30px;
\tbackground-color: #F5F5F5;
\tborder: 1px solid #DADADA;
\tpadding: 10px;
\t";
        // line 1811
        echo $this->getAttribute($this, "border_radius", array(0 => 6), "method");
        echo ";
}

#dp .dp-new-feedback > header h2 {
\tfont-size: 11pt;
\tfont-weight: bold;
\tcolor: #8D8D8D;
}

#dp .dp-new-feedback .dp-search-bar .dp-add-on {
\tpadding: 5px 8px 5px 8px;
\tfont-size: 14pt;
\theight: 22px;
\tvertical-align: top;
}

#dp .dp-new-feedback .dp-search-bar .dp-input-prepend,
#dp .dp-new-feedback .dp-search-bar .dp-suggest-title {
\tmargin-bottom: 0;
}

#dp .dp-new-feedback .dp-search-bar input.dp-suggest-title {
\twidth: 96%;
\tpadding: 5px 8px 5px 8px;
\tfont-size: 14pt;
\theight: 22px;
}

#dp .dp-new-feedback .dp-search-bar .dp-btn.dp-go-btn {
\tpadding: 5px 10px 5px 10px;
\tfont-size: 14pt;
\tmargin-top: 2px;
\twhite-space: nowrap;
}


dp .dp-new-feedback td {
\tvertical-align: middle !important;
}

#dp .dp-new-feedback td.dp-msg {
\tbackground-color: #fff;
\tborder: 1px solid #CCCCCC;
\tpadding: 0 5px 0 5px;
\tfont-size: 13pt;
\t";
        // line 1856
        echo $this->getAttribute($this, "border_radius", array(0 => array("tl" => 3, "bl" => 3)), "method");
        echo "
}

#dp .dp-new-feedback td.dp-cat {
\tborder: 1px solid #CCCCCC;
\tborder-right: none;
\tpadding: 0 0px 0 5px;
\tfont-size: 10pt;
\toverflow: hidden;
\tborder-right: 1px solid #C8C8C8;
\tbackground: rgb(255,255,255);
\tbackground: -moz-linear-gradient(top,  rgb(255,255,255) 0%, rgb(229,229,229) 100%);
\tbackground: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgb(255,255,255)), color-stop(100%,rgb(229,229,229)));
\tbackground: -webkit-linear-gradient(top,  rgb(255,255,255) 0%,rgb(229,229,229) 100%);
\tbackground: -o-linear-gradient(top,  rgb(255,255,255) 0%,rgb(229,229,229) 100%);
\tbackground: -ms-linear-gradient(top,  rgb(255,255,255) 0%,rgb(229,229,229) 100%);
\tbackground: linear-gradient(top,  rgb(255,255,255) 0%,rgb(229,229,229) 100%);
\tfilter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e5e5e5',GradientType=0 );
}

#dp .dp-new-feedback td.dp-cat select {
\tposition: absolute;
\ttop: 0;
\tleft: 0;
\tz-index: 3;
\t";
        // line 1881
        echo $this->getAttribute($this, "opacity", array(0 => 0), "method");
        echo "
}

#dp .dp-new-feedback td.dp-cat em {
\tz-index: 2;
\tposition: relative;
\tdisplay: block;
\tfont-style: normal;
\tpadding: 3px 18px 3px 6px;
\tcursor: pointer;
\tposition: relative;
}

#dp .dp-new-feedback td.dp-cat em {
\tbackground: transparent;
\tborder:none;
}

#dp .dp-new-feedback td.dp-cat em i {
\tdisplay: block;
\tposition: absolute;
\ttop: 11px;
\tright: 5px;
\theight: 0;
\twidows: 0;
\tborder-left: 4px solid transparent;
\tborder-right: 4px solid transparent;
\tborder-top: 4px solid #888;
\tcontent: \"\";

}

#dp .dp-new-feedback td.dp-title {
\tbackground-color: #FFF;
\tborder: 1px solid #CCCCCC;
\tborder-left: none;
\tpadding: 0;
\tfont-size: 13pt;
\t";
        // line 1919
        echo $this->getAttribute($this, "border_radius", array(0 => array("tr" => 3, "br" => 3)), "method");
        echo "
}

#dp .dp-new-feedback td.dp-title input {
\tborder: none;
}
#dp .dp-new-feedback td.dp-title input:focus {
\t";
        // line 1926
        echo $this->getAttribute($this, "box_shadow", array(0 => "none"), "method");
        echo "
}

#dp .dp-new-feedback td.dp-go {
\tpadding-left: 6px;
}

#dp .dp-new-feedback .dp-control-group {
\tmargin: 0;
\tmargin-bottom: 5px;
}

#dp .dp-new-feedback #dp_inline_login_email {
\twidth: 80%;
}

#dp .dp-feedback-section {
\tmargin-left: 0;
}

#dp .dp-feedback-section > header {
\tmargin-left: 15px;
}

#dp .dp-feedback-nav > li {
\tfloat: right;
}
#dp .dp-feedback-nav > li.dp-label-tab {
\tfloat: right;
}

#dp .dp-feedback-widget-wrap {
\tpadding: 0 10px 0 10px;
\twhite-space: nowrap;
}

#dp .dp-feedback-info-wrap {
\tpadding-top: 5px;
}

#dp .dp-feedback-info-wrap .dp-feedback-status-wrap {
\tposition: absolute;
\ttop: 0;
\tright: 0;
\tpadding-left: 53px;
}

#dp .dp-feedback-info-wrap .dp-feedback-status-wrap .dp-fadeaway {
\tright: auto;
\tleft: 0;
}

#dp .dp-feedback-info-wrap .dp-feedback-status-wrap .dp-feedback-status-wrap-inner {
\tbackground-color: #fff;
}

#dp section.dp-content-page > header.dp-voted-row {
\tmargin: -10px;
\tmargin-bottom: 5px;
\tpadding: 10px;
}

#dp .dp-voted-row .dp-feedback-info-wrap .dp-feedback-status-wrap .dp-feedback-status-wrap-inner {
\tbackground-color: #FFFAB3;
}

#dp .dp-voted-row .dp-feedback-info-wrap .dp-feedback-status {
\tposition: relative;
\ttop: -1px;
}

#dp .dp-feedback-status {
\t";
        // line 1998
        echo $this->getAttribute($this, "border_radius", array(0 => 15), "method");
        echo "
\tfont-size: 12px;
\ttext-shadow: 0 1px rgba(255, 255, 255, 0.3);
\tpadding: 1px 4px 1px 4px;
\tmargin-right: 5px;

\tbackground-color: #EDFCFB;
\tborder: 1px solid #118FCF;
\tcolor: #444;
}

#dp .dp-new .dp-feedback-status {
\tbackground-color: #D0FBC2;
\tborder: 1px solid #93B289;
}

#dp .dp-closed .dp-feedback-status {
\tbackground-color: #FFFADB;
\tborder: 1px solid #B1AF80;
\tcolor: #495A43;
}

#dp section.dp-feedback-post > header {
\tfloat: left;
\twidth: 100%;
}
#dp section.dp-feedback-post > .dp-clear {
\tclear: left;
\theight: 1px;
\toverflow: hidden;
}

/******************************
* Voting button
******************************/

#dp .dp-voted-row {
\t-moz-box-shadow: inset 0 0 5px #CBC78D;
\tbox-shadow: inset 0 0 5px #CBC78D;
\tbackground-color: #FFFAB3;
}

#dp .dp-voted-row .dp-fadeaway-container {
\tmargin-right: 4px;
}

#dp .dp-voted-row .dp-fadeaway-container .dp-fadeaway {
\tbackground-image: url(";
        // line 2045
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/fadeaway-yellow.png);
}

.rtl #dp .dp-voted-row .dp-fadeaway-container .dp-fadeaway {
\tbackground-image: url(";
        // line 2049
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/fadeaway-yellow-rtl.png);
}

#dp .dp-feedback-vote .dp-feedback-btn {
\tdisplay: block;
\tborder: 1px solid #ADADAD;
\t";
        // line 2055
        echo $this->getAttribute($this, "gradient", array(0 => "#FBFBFB", 1 => "#E4E4E4", 2 => "#FBFBFB"), "method");
        echo "
\t";
        // line 2056
        echo $this->getAttribute($this, "border_radius", array(0 => 3), "method");
        echo "
\tfont-size: 10px;
\tfont-weight: bold;
\tline-height: 14px;
\tpadding: 3px 5px 3px 5px;
\twhite-space: nowrap;
\ttext-align: center;
}

#dp .dp-feedback-vote.dp-feedback-closed .dp-feedback-btn {
\tborder: 1px solid #C4C4C4;
\t";
        // line 2067
        echo $this->getAttribute($this, "gradient", array(0 => "#FDFDFD", 1 => "#F1F1F1", 2 => "#F1F1F1"), "method");
        echo "
}

#dp .dp-feedback-vote .dp-feedback-badge {
\tborder: 1px solid #DDDCDC;
\t";
        // line 2072
        echo $this->getAttribute($this, "border_radius", array(0 => 3), "method");
        echo "
}

#dp .dp-feedback-vote:hover .dp-feedback-btn,
#dp .dp-feedback-vote.dp-voted .dp-feedback-btn {
\tcursor: pointer;
\tborder-color: #007EC0;
\t";
        // line 2079
        echo $this->getAttribute($this, "gradient", array(0 => "#FBFBFB", 1 => "#D6E2E4", 2 => "#FBFBFB"), "method");
        echo "
}

#dp .dp-feedback-vote:hover .dp-feedback-count,
#dp .dp-feedback-vote.dp-voted .dp-feedback-count {
\tborder-color: #007EC0;
}
#dp .dp-feedback-vote:hover .dp-feedback-count .dp-arrow-outer,
#dp .dp-feedback-vote.dp-voted .dp-feedback-count .dp-arrow-outer {
\tborder-top-color: #007EC0;
}

#dp .dp-feedback-vote .dp-feedback-btn i {
\t";
        // line 2092
        echo $this->getAttribute($this, "opacity", array(0 => 0.6), "method");
        echo "
}
#dp .dp-feedback-vote .dp-feedback-btn span {
\tmargin-left: 5px;
\tcolor: #5C5C5C;
}

#dp .dp-feedback-vote.dp-feedback-closed span {
\tcolor: #C4C4C4;
}

#dp .dp-feedback-vote .dp-feedback-count {
\tbackground-color: #fff;
\tposition: relative;
\tdisplay: block;
\tborder: 1px solid #DDDCDC;
\t";
        // line 2108
        echo $this->getAttribute($this, "border_radius", array(0 => 3), "method");
        echo "
\tpadding: 3px 0 3px 0;
\tfont-size: 11px;
\ttext-align: center;
\tmargin-bottom: 6px;
\tline-height: 100%;
}

#dp .dp-feedback-vote .dp-feedback-count em {
\tdisplay: block;
\ttop: -1px;
\tfont-style: normal;
\ttext-align: center;
\tpadding-left: 1px;
}

#dp .dp-feedback-vote .dp-feedback-count .dp-arrow-outer {
\tdisplay: block;
\tposition: absolute;
\tleft: 50%;
\tbottom: -6px;

\twidth: 0;
\theight: 0;
\tborder-left: 6px solid transparent;
\tborder-right: 6px solid transparent;
\tborder-top: 6px solid #DDDCDC;
\tmargin-left: -6px;
}
#dp .dp-feedback-vote .dp-feedback-count .dp-arrow-inner {
\tdisplay: block;
\tposition: absolute;
\tleft: 50%;
\tbottom: -5px;
\twidth: 0;
\theight: 0;
\tborder-left: 6px solid transparent;
\tborder-right: 6px solid transparent;
\tborder-top: 6px solid #fff;
\tmargin-left: -6px;
}

/***********************************************************************************************************************
* New Feedback
* The new feedback form that is inline with the feedback page
***********************************************************************************************************************/

#dp .dp-new-feedback form {
\tmargin: 0;
\tpadding: 0;
}

#dp .dp-feedback-form {
\tmargin-top: 10px;
\tmargin-bottom: 0;
}

#dp fieldset.dp-related-section {
\tmargin-bottom: 35px;
}

/***********************************************************************************************************************
* Browse Lists
* These are category lists that have a heading for the parent, and then links for one level of children,
* and a link for \"view all\"
***********************************************************************************************************************/

#dp ul.dp-browse-list {
\tlist-style-type: none;
\tclear: left;

\tpadding-bottom: 40px;
\tmargin-left: 15px;
}

/*
 * IE hack. For some reason IE adds a bunch of whitespace preceding the fadeaway container
 * if the container has overflow:hidden. The overflow:hidden is required so text is clipped.
 * So this hack removes it from the container where it usually is, and adds it to the parent
 * ul instead which visually looks the same.
 */
.browser-ie #dp ul.dp-browse-list ul.dp-contains-fadaway {
\toverflow: hidden;
\tpadding-right: 0;
\tmargin-right: 0;
}
.browser-ie #dp ul.dp-browse-list ul.dp-contains-fadaway li {
\tpadding-right: 0;
}
.browser-ie #dp ul.dp-browse-list ul.dp-contains-fadaway .dp-fadeaway-container {
\toverflow: visible;
}

#dp ul.dp-browse-list:first-of-type {
\tborder-top: none;
\tmargin-top: 0;
}

#dp ul.dp-browse-list a {
\tcursor: pointer;
}

#dp ul.dp-browse-list .dp-view-more {
\tfloat: left;
\tbackground-color: #F0F0F0;
\t";
        // line 2213
        echo $this->getAttribute($this, "border_radius", array(0 => 8), "method");
        echo "
}

#dp ul.dp-browse-list .dp-view-more a {
\tdisplay: block;
\tpadding: 5px 10px 5px 10px;
}

#dp ul.dp-browse-list .dp-view-more a:hover {
\ttext-decoration: none;
}

#dp ul.dp-browse-list .dp-view-more .dp-badge {
\tposition: relative;
\ttop: -1px;
}

#dp ul.dp-browse-list > li {
\tfloat: left;
\twidth: 45%;

\tbackground: transparent url(";
        // line 2234
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/page.png) no-repeat 0 0px;
\tpadding-left: 24px;
\tmargin-bottom: 35px;
}

#dp ul.dp-browse-list > li > h3 {
\tfont-size: 11pt;
\tmargin: 0;
\tpadding: 1px 0 3px 0;
\tline-height: 100%;
}

#dp ul.dp-browse-list > li > h3 a {
\tcolor: #000;
}

#dp ul.dp-browse-list > li > ul {
\tmargin: 0;
\tpadding: 0;
\tlist-style-type: none;
}

#dp ul.dp-browse-list > li > ul > li {
\tline-height: 100%;
\tpadding: 0 3px;
\tmargin-left: 15px;
\tlist-style: square;
\tline-height: 150%;
}

/*
\tFix missing bullets in webkit (due to bug: https://bugs.webkit.org/show_bug.cgi?id=13332)
\t- Fixing in webkit breaks Firefox, fixing in Firefox breaks Chrome.
\t- Original bug is in webkit, so this is a webkit-specific media query to apply the fix which
\t  wont affect others.
*/
@media screen and (-webkit-min-device-pixel-ratio:0) {
\t#dp ul.dp-browse-list > li > ul > li {
\t\tlist-style-position: inside;
\t\tmargin-left: 0;
\t}
}

/******************
* dp-browse-list-full
*******************/

#dp ul.dp-browse-list-full {
\tlist-style-type: none;
\tfont-size: 11px;

\tpadding-top: 5px;
\tmargin-left: 10px;
\tmargin-right: 10px;
\tmargin-bottom: 0;
}

#dp ul.dp-browse-list-full.sep {
\tmargin-top: 10px;
\tclear: left;
}

#dp ul.dp-browse-list-full a {
\tcursor: pointer;
}

#dp ul.dp-browse-list-full > li {
\tmargin-bottom: 8px;
}

#dp ul.dp-browse-list-full > li:last-of-type {
\tborder-bottom: none;
}

#dp ul.dp-browse-list-full > li > h3 {
\tfont-size: 10pt;
\tfont-weight: normal;
\tmargin: 0;
\tpadding: 0 0 0 0;
\tline-height: 100%;
}


/***********************************************************************************************************************
* Content Pages
***********************************************************************************************************************/

#dp section.dp-content-page {
\tmargin: 25px;
\tmargin-top: 0;
}

.dp-simple-iframe #dp section.dp-content-page {
\tmargin: 0;
}

#dp section.dp-content-page > header {
\tmargin-bottom: 15px;
}

#dp section.dp-content-page > header h3 {
\tfont-size: 20pt;
}

#dp section.dp-content-page > header h3.dp-fadeaway-container {
\theight: 20px;
\twhite-space: nowrap;
}

#dp section.dp-content-page > header .sub-note {
\tbackground-color: #E8E8E8;
\tpadding: 4px;
\tfont-size: 9pt;
\tmargin-top: 5px;
}

#dp section.dp-content-page > article {
\tclear: left;
\tpadding-bottom: 15px;
\tmargin-top: 0;
\tborder-top: 1px solid #D2D0D0;
}

.dp-simple-iframe #dp section.dp-content-page > article {
\tpadding: 0;
\tborder: none;
}

#dp section.dp-content-page > article,
#dp section.dp-content-page > footer {
\tmargin-left: 0;
\tmargin-right: 0;
\tpadding-left: 0;
\tpadding-right: 0;
}


#dp .dp-download-btn {
\tdisplay: inline-block;
\tmargin: 0 auto 0 auto;
\tpadding: 20px;
\tbackground-color: ";
        // line 2375
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "dark_well_bg_color"), "html", null, true);
        echo ";
\t";
        // line 2376
        echo $this->getAttribute($this, "box_shadow", array(0 => "inset 0 0 3px #999999"), "method");
        echo "
\t";
        // line 2377
        echo $this->getAttribute($this, "border_radius", array(0 => 4), "method");
        echo "
}
#dp .dp-download-btn-wrap {
\tfloat: right;
\tmargin: 0 0 10px 10px;
\ttext-align: center;
}

/******************************
* Two level selects
******************************/

#dp .dp-two-select .dp-child {
\tdisplay: none;
\tbackground: url(";
        // line 2391
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/elbow-end.gif) no-repeat 0 0;
\tpadding-left: 21px;
\tmargin: 3px 0 0 3px;
}

#dp .dp-two-select.dp-show-child .dp-child {
\tdisplay: block;
}

/******************************
* Rating bar
******************************/

#dp .dp-user-vote {
\tfont-size: 10pt;
}

#dp .dp-user-vote .dp-show-ratings {
\tmargin-top: 3px;
\tfont-size: 10px;
\tfont-weight: bold;
\tcolor: #737373;
}

#dp .dp-user-vote {
\ttext-align: center;
}

#dp .dp-user-vote button.dp-good span {
\tbackground: url(";
        // line 2420
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/tick.png) no-repeat 0 50%;
\tpadding-left: 19px;
}
#dp .dp-user-vote button.dp-bad span {
\tbackground: url(";
        // line 2424
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/cross.png) no-repeat 0 50%;
\tpadding-left: 19px;
}

#dp .dp-view-wrap {
\tmargin-top: -18px;
\tmargin-bottom: 18px;
\ttext-align: center;
\tfont-size: 11px;
}

#dp .dp-subscribe {
\ttext-align: center;
\tmargin: -10px 0 11px 0;
\tfont-size: 11px;
}

#dp .dp-subscribe.dp-subscribe-category {
\tmargin-left: 20px;
\tmargin-right: 20px;
}

#dp .dp-subscribe.dp-subscribe-on {
\tbackground-color: #F3E592;
\tpadding: 10px;
\tborder-radius: 3px;
}

/***********************************************************************************************************************
* Comments Box
***********************************************************************************************************************/

#dp section.dp-comments-box {
\tmargin: 25px;
}

#dp section.dp-comments-box header h4 {
\tfont-size: 15pt;
\tfont-weight: normal;
\tmargin: 0;
\tpadding: 0;
\tline-height: 100%;
\tpadding-bottom: 10px;
}

#dp section.dp-comments-box article.dp-well {
\tmargin-bottom: 0;
}

#dp section.dp-comments-box article > ul {
\tmargin: 0;
}

#dp section.dp-comments-box article > ul > li {
\tlist-style: none;
\tmargin: 10px 0 10px 0;
\tpadding: 10px 0 10px 0;
\tborder-bottom: 1px solid #D2D0D0;
\tposition: relative;
}

#dp section.dp-comments-box article > ul > li > header .avatar {
\tfloat: left;
\tbackground-repeat: no-repeat;
\tbackground-position: 50% 50%;
}

#dp section.dp-comments-box article > ul > li > header .byline h4 {
\tfont-size: 13pt;
\tfont-weight: normal;
\tmargin: 0;
\tpadding: 2px 0 2px 0;
}

#dp section.dp-comments-box article > ul > li > article {
\tclear: left;
\tpadding: 12px 12px 0 0;
}

#dp section.dp-comments-box article > ul > li.new-comment {
\tborder-bottom: none;
}

#dp section.dp-comments-box article > ul > li.new-comment textarea {
\twidth: 98%;
}

#dp section.dp-comments-box article > ul > li.dp-no-comments {
\tmargin: 0;
\tborder-bottom: 0;
}

#dp section.dp-comments-box article .dp-validating-note {
\tfont-size: 11px;
\tcolor: #888;
}

#dp section.dp-comments-box article .dp-validating-note i {
\t";
        // line 2522
        echo $this->getAttribute($this, "opacity", array(0 => 0.5), "method");
        echo "
}

/***********************************************************************************************************************
* Search and search listings
***********************************************************************************************************************/

#dp section.dp-portal-section > .dp-search-block {
\tmargin: 0;
}

#dp section.dp-portal-section > .dp-search-block .dp-content-list > li {
\tmargin: 8px 0 8px 0;
\tpadding: 10px;
}

#dp section.dp-portal-section > .dp-search-block .dp-content-list li .dp-post-info li {

}

#dp .dp-row-contain {
\toverflow: hidden;
\twidows: 99%;;
}


#dp_content_overlay {
\tbackground-color: #fff;
}

#dp_content_overlay .dp-controls {
\tpadding: 10px;
\tposition: fixed;
\tbottom: 0;
\tleft: 0;
\tright: 0;
\tbackground-color: #F2F3F7;
\tborder-top: 1px solid #CDCDCE;
}

#dp_content_overlay .dp-controls i {
\tposition: relative;
\ttop: 1px;
\tleft: -2px;
}

#dp #dp_content_overlay section.dp-content-page {
\tmargin: 10px 9px 0 10px;
}

#dp_content_overlay #dp_article_content {
\tposition: fixed;
\tbottom: 49px;
\tleft: 0;
\tright: 0;
\ttop: 20px;
\toverflow: auto;
}

#widget_deskpro .is-showing-related { display: none; }
#widget_deskpro .not-showing-related { display: block; }
#widget_deskpro .showing-related .is-showing-related { display: block; }
#widget_deskpro .showing-related .not-showing-related { display: none; }

#widget_deskpro #left_pane {
\tposition: absolute;
\tleft: 0;
\ttop: 0;
\tbottom: 0;
\twidth: 328px;
}

#widget_deskpro #search_loading {
\tdisplay: block;
\tbackground: url(";
        // line 2596
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/spinners/loading-big-circle.gif) no-repeat 50% 50%;
\theight: 50px;
\tposition: absolute;
\ttop: 30px;
\tright: 0px;
\tleft: 0;
}

#widget_deskpro #no_results {
\ttext-align: center;
}

#widget_deskpro .widget-content {
\toverflow: auto;

}

/***********************************************************************************************************************
* Register page
***********************************************************************************************************************/

#dp .dp-register-page {
\tmargin: 0 15px 15px 15px;
}

#dp .dp-register-page .dp-well {
\tpadding: 10px;
}

#dp .dp-register-page .dp-well-light {
\tmargin-top: 10px;
\tmargin-bottom: 0;
}

#dp .dp-register-page > section > header h3 {
\tline-height: 20px;
\tmargin: 0;
}

#dp .dp-register-page > section > header label {
\tdisplay: block;
\tmargin: 0;
}
#dp .dp-register-page > section > header input[type=\"radio\"] {
\tdisplay: inline;
\tmargin-right: 8px;
}

#dp .dp-register-page > section > header .dp-connect-with {
\tfloat: right;
\tfont-size: 11px;
}

/***********************************************************************************************************************
* Tickets list
***********************************************************************************************************************/

#dp section.dp-ticketlist-section > .dp-content-block {
\tmargin: 0;
}

#dp .dp-ticket-sort {
\tfloat: right;
}

#dp ul.dp-ticket-list {
\tmargin: 0;
\tpadding: 0;
}

#dp ul.dp-ticket-list > li {
\tmargin: 0;
\tpadding: 12px;
\tborder: 1px solid #DDDDDD;
\tborder-top: none;
\tlist-style: none;
}

#dp ul.dp-ticket-list > li:first-child {
\tborder-top: 1px solid #DDDDDD;
\t";
        // line 2676
        echo $this->getAttribute($this, "border_radius", array(0 => array("tr" => 4, "tl" => 4)), "method");
        echo "
}
#dp ul.dp-ticket-list > li:last-child {
\t";
        // line 2679
        echo $this->getAttribute($this, "border_radius", array(0 => array("br" => 4, "bl" => 4)), "method");
        echo "
}

#dp .dp-ticket-list .dp-title .dp-label {
\tposition: relative;
\ttop: -2px;
}

#dp .dp-ticket-list .dp-title h4 {
\tdisplay: inline-block;
\tfont-size: 12pt;
\tline-height: 18px;
}

#dp .dp-ticket-list .dp-title h4 .ticket-creator {
\tcolor: ";
        // line 2694
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "meta_text_color"), "html", null, true);
        echo ";
\tfont-weight: normal;
\tmargin-left: 5px;
\tfont-size: 90%;
}

#dp .dp-ticket-list .dp-title .dp-time {
\tfont-size: 11px;
\tcolor: #777;
}

#dp .dp-ticket-list .dp-last-active {
\tmargin-top: 4px;
\tfont-size: 9pt;
}

/***********************************************************************************************************************
* Tickets page
***********************************************************************************************************************/

#dp section.dp-ticket > article {
\tmargin-top: 0;
\tpadding-top: 0;
\tborder-top: none;
}

#dp .dp-messages-wrap .dp-comments-box {
\tmargin: 0;
}

#dp .dp-messages-wrap .dp-comments-box li:last-child {
\tborder-bottom: none;
}

#dp .dp-status-switcher {
\tfloat: right;
}

#dp .dp-status-switcher .switcher { display: none; }
#dp .dp-status-switcher:hover .switcher { display: block; }
#dp .dp-status-switcher:hover .active { display: none; }

#dp .dp-status {
\tfloat: right;
\tborder-radius: 10px;
\tpadding: 1px 15px 1px 15px;
\tfont-weight: bold;
\tcolor: #fff;
\ttext-shadow: 0px 1px 0px #858585;
\tfilter: dropshadow(color=#858585, offx=0, offy=1);
}

#dp .dp-status-switcher a, #dp .dp-status-switcher a:hover {
\ttext-decoration: none !important;
\tcolor: #fff !important;
}

#dp .dp-status.dp-status-open {
\tbackground-color: #72C894;
}

#dp .dp-status.dp-status-resolved {
\tbackground-color: #B5C1C8;
}

#dp .dp-status.dp-status-closed {
\tbackground-color: #A7A4A4;
}

#dp section.dp-ticket > article.dp-ticket-info {
\tborder-top: 1px solid #ddd;
\tpadding-top: 10px;
\tpadding-bottom: 0;
}

#dp section.dp-ticket > article.dp-ticket-info .dp-modify-link-row {
\ttext-align: right;
\tpadding: 0 3px 4px 0;
\tcolor: #666;
}

#dp section.dp-ticket > article.dp-ticket-info .dp-modify-link-row i {
\t";
        // line 2776
        echo $this->getAttribute($this, "opacity", array(0 => 0.6), "method");
        echo "
}

#dp section.dp-ticket > article.dp-ticket-info table {
\tmargin: 0;
}

#dp section.dp-ticket > article.dp-newreply-wrap {
\tpadding: 15px;
}

#dp section.dp-ticket > article.dp-newreply-wrap > header h4 {
\tfont-size: 14pt;
}

#dp section.dp-ticket > article.dp-newreply-wrap .dp-well-light {
\tmargin: 10px 0 0 0;
}

#dp section.dp-ticket > article.dp-early-close {
\tpadding: 15px;
\ttext-align: center;
}

#dp .dp-new-ticket-section {
\tmargin-top: 30px;
}

#dp .feedback-link {
\tfloat: right;
\tmargin: 10px 0 0 0;
\tcolor: #AAA;
\tfont-size: 10px;
\tline-height: 16px;
\theight: 20px;
}

#dp .dp-date  {
\tfont-size: 10px;
\tcolor: #999;
}

#dp .feedback-link span {
\tpadding-right: 10px;
}

#dp .feedback-link .desc-helpful,
#dp .feedback-link .desc-not-helpful {
\tdisplay:none;
}

#dp .feedback-link a {
\tdisplay: block;
\tfloat: right;
\tcursor: pointer;
\tcolor: #555;
}

#dp .dp-rate-box {
\tborder-radius: 10px;
\t-webkit-border-radius: 10px;
\tcursor: pointer;
\tpadding: 8px;
\ttext-align: center;
\tbackground-color: #F3F3F3;
\tborder: 2px solid #C0C0C0;
}

#dp .dp-rate-box input[type=\"radio\"] {
\tamrgin-right: 3px;
}

#dp .dp-rate-box i {
\tfont-size: 45pt;
\tfont-weight: normal;
\tfont-style: normal;
\tdisplay: block;
\ttext-align: center;
\tline-height: 54px;
}

#dp .dp-rate-box.dp-positive i {
\tcolor: #16B300;
}
#dp .dp-rate-box.dp-negative i {
\tcolor: #A21B22;
}
#dp .dp-rate-box.dp-neutral i {
\tcolor: #849C96;
}


/***********************************************************************************************************************
* Profile page
***********************************************************************************************************************/

#dp .dp-emails-table .dp-controls,
#dp .dp-twitter-table .dp-controls{
\tfloat: right;
\tfont-size: 9pt;
}

/***********************************************************************************************************************
* Profile page
***********************************************************************************************************************/

.dp-with-js .form-upload-section .dp-fallback {
\tdisplay: none;
}

#dp .dp-attach-limits {
\tfont-size: 11px;
\tcolor: #888888;
\tmargin-top: -4px;
}

#dp .dp-hidden-file-upload {
\tposition: relative;
\twidth: 150px;
\theight: 20px;
\toverflow: hidden;
}

#dp .dp-hidden-file-upload .link{
\tposition: absolute;
\ttop: 0;
\tleft: 0;
\tright: 0;
\tbottom: 0;
\tfont-size: 11px;

\tcursor: pointer;
\tdisplay: block;
\tcolor: ";
        // line 2909
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "link_color"), "html", null, true);
        echo ";
\ttext-decoration: underline;
\tfont-size: 10pt;

\tpadding: 0;
\tmargin: 0;
\tbackground-color: transparent;
\tborder: none;
\tz-index: 1;
}

#dp .dp-hidden-file-upload input {
\tposition: absolute;
\ttop: 0;
\tright: 0;
\tmargin: 0;
\tfont-size: 23px;
\topacity: 0;
\tfilter: alpha(opacity=0);
\t-moz-transform: translate(-300px, 0) scale(4);
\ttransform: translate(-300px, 0) scale(4);
\tdirection: ltr;
\tcursor: pointer;
\tpadding: 0;
\tmargin: 0;
\tz-index: 2;
}

#dp ul.file-list {
\tmargin: 0;
\tpadding: 0;
}

#dp ul.file-list li {
\tpadding: 0;
\tmargin: 4px 0 4px 0;
\tlist-style: none;
\tline-height: 16px;
}

#dp ul.file-list li label {
\tline-height: 16px;
\tmargin: 0;
\tpadding: 0;
}

#dp ul.file-list li em.remove-attach-trigger {
\tfloat: left;
\tdisplay: inline-block;
\tbackground: url(";
        // line 2958
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/cross.png) no-repeat 0 50%;
\twidth: 16px;
\theight: 16px;
\tmargin-right: 5px;
\tline-height: 16px;
\tcursor: pointer;
}

#dp ul.file-list li span {
\tmargin-left: 5px;
\tfont-size: 10px;
\tcolor: #666;
}

#dp ul.attachment-list {
\tmargin: 0;
\tmargin-top: 10px !important;
\tpadding: 10px;
\tbackground: #F8F8F8;
\tborder: 1px solid #C5CDD4;
\t";
        // line 2978
        echo $this->getAttribute($this, "border_radius", array(0 => 4), "method");
        echo "
}

#dp ul.attachment-list > li {
\tlist-style: none;
\tpadding: 5px 0 5px 0 !important;
\tmargin: 0 !important;
}
#dp ul.attachment-list > li span.size {
\tfont-size: 7pt;
\tcolor: #5E5E5E;
}

#dp ul.attachment-list > li img.preview {
\ttext-align: right;
\tvertical-align: middle;
\tmargin-right: 6px;
}

#dp ul.attachment-list li.dp-fileicon {
\tbackground: url(";
        // line 2998
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/filetypes/page.png) no-repeat 0 50%;
\tpadding-left: 20px !important;
}

#dp ul.attachment-list li.dp-fileicon.dp-fileicon-pdf   { background-image: url(";
        // line 3002
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/filetypes/page_white_acrobat.png); }
#dp ul.attachment-list li.dp-fileicon.dp-fileicon-gif   { background-image: url(";
        // line 3003
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/filetypes/page_white_picture.png); }
#dp ul.attachment-list li.dp-fileicon.dp-fileicon-png   { background-image: url(";
        // line 3004
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/filetypes/page_white_picture.png); }
#dp ul.attachment-list li.dp-fileicon.dp-fileicon-jpg   { background-image: url(";
        // line 3005
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/filetypes/page_white_picture.png); }
#dp ul.attachment-list li.dp-fileicon.dp-fileicon-jpeg  { background-image: url(";
        // line 3006
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/filetypes/page_white_picture.png); }
#dp ul.attachment-list li.dp-fileicon.dp-fileicon-ico   { background-image: url(";
        // line 3007
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/filetypes/page_white_picture.png); }
#dp ul.attachment-list li.dp-fileicon.dp-fileicon-doc   { background-image: url(";
        // line 3008
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/filetypes/page_white_word.png); }
#dp ul.attachment-list li.dp-fileicon.dp-fileicon-docx  { background-image: url(";
        // line 3009
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/filetypes/page_white_word.png); }
#dp ul.attachment-list li.dp-fileicon.dp-fileicon-rtf   { background-image: url(";
        // line 3010
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/filetypes/page_white_word.png); }
#dp ul.attachment-list li.dp-fileicon.dp-fileicon-html  { background-image: url(";
        // line 3011
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/filetypes/page_white_world.png); }
#dp ul.attachment-list li.dp-fileicon.dp-fileicon-htm   { background-image: url(";
        // line 3012
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/filetypes/page_white_world.png); }
#dp ul.attachment-list li.dp-fileicon.dp-fileicon-zip   { background-image: url(";
        // line 3013
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/filetypes/page_white_zip.png); }
#dp ul.attachment-list li.dp-fileicon.dp-fileicon-gz    { background-image: url(";
        // line 3014
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/filetypes/page_white_zip.png); }
#dp ul.attachment-list li.dp-fileicon.dp-fileicon-tar   { background-image: url(";
        // line 3015
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/filetypes/page_white_zip.png); }
#dp ul.attachment-list li.dp-fileicon.dp-fileicon-7z    { background-image: url(";
        // line 3016
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/filetypes/page_white_zip.png); }
#dp ul.attachment-list li.dp-fileicon.dp-fileicon-rar   { background-image: url(";
        // line 3017
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/filetypes/page_white_zip.png); }

#dp ul.radio-list { list-style: none; }
#dp ul.radio-list input[type=\"radio\"] {
\tdisplay: inline;
\tmargin: 0px;
\tmargin-bottom: 2px;
}


/***********************************************************************************************************************
* Chat Widget
***********************************************************************************************************************/

#dp_chat {
\tbackground-color: #fff;
\tpadding: 10px;
}
#dp_chat {
\tfont-family: \"Lucida Grande\", \"Lucida Sans Unicode\", \"Tahoma\", Arial, sans-serif;
\tfont-size: 11px;
}

#dp_chat_wrap {

}

#dp_chat_wrap .dp-well {
\tpadding: 6px;
\tmargin: 0;
}

#dp_chat_wrap .dp-well h3 {
\tfont-size: 10pt;
}

#dp_chat_wrap .dp-well input[type=\"text\"], #dp_chat_wrap .dp-well input[type=\"password\"] {
\tpadding: 2px;
\tfont-size: 11px;
}

#dp_chat_wrap .dp-well .dp-btn {
\tfont-size: 11px;
\tpadding: 3px;
}

#dp_chat_wrap, #dp_chat_active, #dp_chat_start, #dp_chat_finding_agent {
\tposition: absolute;
\ttop: 0;
\tleft: 0;
\tright: 0;
\tbottom: 0;
}

#dp_chat_start .dp-control-group strong {
\tpadding-left: 2px;
}

#dp_chat_start .dp-control-group .dp-controls {
\tline-height: 100%;
\tpadding-bottom: 6px;
}


#dp_chat_start .dp-control-group .dp-controls input {
\tmargin-bottom: 0;
}

#dp_chat .dp-chat-footer {
\tposition: absolute;
\tbottom: 0;
\tleft: 0;
\tright: 0;
\theight: 20px;
}

#dp_chat .dp-chat-footer.dp-relative {
\tpadding-top: 12px;
\tposition: relative;
}

#dp_chat .dp-copy {
\tcolor: #B2B2B2;
\tfont-size: 9pt;
\tpadding-left: 10px;
\ttext-align: center;
}

#dp_chat .dp-copy a {
\tline-height: 16px;
\tdisplay: inline-block;
\tcolor: #B2B2B2;
\ttext-decoration: none;
\tbackground: url(";
        // line 3110
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/dp-logo-bw-16.png) no-repeat 0 50%;
\tpadding-left: 20px;
}

#dp_chat .dp-copy a:hover {
\tbackground-image: url(";
        // line 3115
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/dp-logo-16.png);
}

#dp_chat .dp-copy a strong {
\tfont-weight: bold;
\ttext-decoration: underline;
}

#dp_chat .dp-copy a:hover {
\tcolor: #000;
}

#dp_chat section.dp-chat-active-state {
\tposition: absolute;
\ttop: 0;
\tbottom: 0;
\tleft: 0;
\tright: 0;
}

#dp_chat section.dp-chat-active-state > header {
\theight: 40px;
\tborder-bottom: 1px solid #aaa;
\tpadding: 0 10px 0 10px;
\tbackground-color: #F9F9F9;
\t";
        // line 3140
        echo $this->getAttribute($this, "box_shadow", array(0 => "0 1px 2px rgba(0,0,0, 0.2)"), "method");
        echo "
}

#dp_chat section.dp-chat-active-state > header h3 {
\tmargin: 0 0 0 0;
\tbackground: url(";
        // line 3145
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/widget/icon-chat.png) no-repeat 2px 16px;
\tpadding: 10px 0 10px 25px;
\tfont-weight: normal;
\tfont-size: 12pt;
}

#dp_chat input {
\tfont-size: 10pt;
\tborder: 1px solid #CCCCCC;
\t";
        // line 3154
        echo $this->getAttribute($this, "border_radius", array(0 => 3), "method");
        echo "
\tpadding: 5px;
}

#dp_chat .dp-control-label {
\tfont-size: 9pt;
}

#dp_chat section.dp-chat-active-state > article {
\tposition: absolute;
\ttop: 41px;
\tleft: 0;
\tright: 0;
\tbottom: 0;
\tpadding: 10px;
\toverflow: auto;
}

#dp_chat section.dp-chat-active-state > header .dp-chatting-with h3 {
\tbackground: transparent;
\tpadding: 0;
\tmargin: 0;
\tline-height: 100%;
}

#dp_chat section.dp-chat-active-state > header .dp-chatting-with h3.title {
\tfont-size: 9pt;
\tcolor: #888;
}

#dp_chat section.dp-chat-active-state > header .dp-chatting-with h3.name {
\tfont-size: 12pt;
\tpadding-top: 3px;
}

#dp_chat_finding_agent_more {
\tpadding: 30px;
\ttext-align: center;
}

#dp_chat_header_pane {
\tposition: absolute;
\ttop: 0;
\tleft: 0;
\tright: 0;
\theight: 40px;
\tbackground-color: #ECF0F4;
\tborder-bottom: 1px solid #ccc;
}

#dp_chat_messages_pane {
\tposition: absolute;
\ttop: 0;
\tleft: 0;
\tright: 0;
\tbottom: 88px;
\toverflow: auto;
\tpadding: 0 10px 0 10px;
}

#dp_chat_input_pane {
\tposition: absolute;
\tleft: 0;
\tright: 0;
\tbottom: 0;
\theight: 85px;
\tbackground-color: #F1F1F1;
\tbox-shadow: 0 -1px 2px rgba(0,0,0, 0.2);
}

#dp_chat_input_pane textarea#dp_chat_message_input {
\theight: 40px;
\twidth: 99%;
}

#dp_chat_input_pane button#dp_chat_message_send {
\theight: 49px;
\tmin-width: 50px;
}

#dp_chat_start input {
\twidth: 80%;
\tmargin-bottom: 10px;
}

#dp_chat_finding_agent p {
\tpadding: 10px;
\ttext-align: center;
}

#dp_chat_finding_agent_loading {
\tmargin: 10px auto 10px auto;
\twidth: 300px;
}

#dpchat_with_agent .avatar {
\tfloat: left;
\tmargin-right: 5px;
\t";
        // line 3252
        echo $this->getAttribute($this, "border_radius", array(0 => 3), "method");
        echo "
\toverflow: hidden;
}

#dp_chat .dp-chatting-with {
\tpadding-top: 5px;
}

#dpchat_with_agent .info {
\tfloat: left;
}

#dpchat_with_agent .info h1 {
\tmargin: 0;
\tpadding: 0;
\tfont-size: 14px;
}

#dp_chat_messages_pane .row {
\tmargin: 0 0 8px 0;
\tpadding: 4px;
\tbackground-color: #F5F7FA;
\tborder-radius: 4px;
\tborder: 1px solid #CED4DB;
}

#dp_chat_messages_pane .row .time {
\tfloat: right;
\tfont-size: 11px;
\tcolor: #888;
\tline-height: 100%;
\tpadding: 3px;
}

#dp_chat_messages_pane .row.sys {
\tbackground-color: transparent;
\tborder: none;
\tpadding: 0 0 0 4px;
\tcolor: #888;
\tmargin: 0 0 4px 0;
}

#dp_chat_messages_pane .row.sys .message {
\tfont-size: 11px;
}

#dp_chat_messages_pane .row.sys .author {
\tdisplay: none;
}

#dp_chat_messages_pane .row .avatar {
\tfloat: left;
\tmargin-right: 3px;
\t";
        // line 3305
        echo $this->getAttribute($this, "border_radius", array(0 => 3), "method");
        echo "
\toverflow: hidden;
}

#dp_chat_messages_pane .row .author {
\tfont-weight: bold;
\tfont-size: 11px;
\tline-height: 18px;
\tcolor: #555;
}

#dp_chat_messages_pane .row .message {
\tfont-size: 12px;
\tclear: both;
\tpadding: 0 0 0 4px;
}

#dp_chat_messages_pane .row .message img {
\tmax-width: 95%;
\tmax-height: 200px;
}

#dp_chat_messages_pane .row.user {
\tbackground-color: #F8FCD2;
\tborder: 1px solid #D5D5C2;
}

#dp_chat_messages_pane .row.user .author {
\tline-height: 100%;
}

#dp_chat_messages_pane .row.agent {
\tbackground-color: #E5FCCF;
\tborder: 1px solid #A7E99E;
}

#dp_chat_message_upload {
\tposition: absolute;
\tbottom: 0;
\tleft: 0;
\theight: 21px;
\twidth: 150px;
\toverflow: hidden;
}

#dp_chat #dp_chat_message_upload_file {
\tposition: absolute;
\tbottom: 0;
\tright: 0;
\tmargin: 0 !important;
\tpadding: 0 !important;

\tfilter: alpha(opacity=0);
\t-khtml-opacity: 0;
\t-moz-opacity: 0;
\topacity: 0;

\tz-index: 2;

\tborder: none;
\tborder-right: 500px solid transparent;
\tborder-left: 500px solid transparent;
\t-o-transform: translate(250px, -50px) scale(1);
\t-moz-transform: translate(-300px, 0) scale(4);
\tdirection: ltr;
\tcursor: pointer;
}

#dp_chat_upload_link {
\tposition: absolute;
\tbottom: 0;
\tleft: 0;
\tpadding: 0 0 4px 12px;
\tfont-size: 9pt;
\ttext-decoration: none;
\tcolor: #777 !important;
\tz-index: 1;
}

#dp_chat_upload_link .dp-link {
\tline-height: 100%;
\tdisplay: inline-block;
\theight: 14px;
\tvertical-align: top;
}

#dp_chat_upload_link:hover {
\ttext-decoration: none;
\tcolor: #000 !important;
}
#dp_chat_upload_link:hover i {
\t";
        // line 3396
        echo $this->getAttribute($this, "opacity", array(0 => 1), "method");
        echo "
}

#dp_chat_upload_link i {
\t";
        // line 3400
        echo $this->getAttribute($this, "opacity", array(0 => 0.5), "method");
        echo "
}

#dp_chat_filedrag {
\tposition: absolute;
\tbottom: 0;
\tleft: 0;
\tright: 0;
\ttop: 0;
\tfont-size: 13px;
\tz-index: 100;
\tbackground-color: #FFF2D5;
\ttext-align: center;
\tpadding-top: 20px;
\tdisplay: none;

\tfilter: alpha(opacity=90);
\t-khtml-opacity: 0.9;
\t-moz-opacity: 0.9;
\topacity: 0.9;
}

.file-drag-over #dp_chat_filedrag {
\tdisplay: block;
}

#dp_chat .dp_chatwin_close {
\tposition: absolute;
\ttop: 12px;
\tright: 12px;
\toverflow: none;
\tdisplay: block;
\tcursor: pointer;
\tz-index: 50;
\tbackground-color: #EDEDED;
\tborder: 1px solid #C1C1C1;
\tpadding: 0px 4px 0px 0px;
\tline-height: 100%;
\tfont-size: 10px;
\t";
        // line 3439
        echo $this->getAttribute($this, "border_radius", array(0 => 15), "method");
        echo "
}
#dp_chat .dp_chatwin_close i {
\t";
        // line 3442
        echo $this->getAttribute($this, "opacity", array(0 => 0.5), "method");
        echo "
}
#dp_chat .dp_chatwin_close:hover i {
\t";
        // line 3445
        echo $this->getAttribute($this, "opacity", array(0 => 1), "method");
        echo "
}

#dp_chatwin_min {
\tposition: absolute;
\ttop: 4px;
\tright: 22px;
\tdisplay: block;
\tcursor: pointer;
\tz-index: 50;
\t";
        // line 3455
        echo $this->getAttribute($this, "opacity", array(0 => 0.5), "method");
        echo "
}
#dp_chatwin_min:hover {
\t";
        // line 3458
        echo $this->getAttribute($this, "opacity", array(0 => 1), "method");
        echo "
}

#dp_chat_end_confirm {
\tposition: absolute;
\tz-index: 100;
\ttop: 0;
\tbottom: 0;
\tleft: 0;
\tright: 0;
\tbackground: rgba(255, 255, 255, 0.6);
}

#dp_chat_end_confirm article {
\tmargin: 50px 20px 0 20px;
\tpadding: 15px;
\tbackground-color: #E8E8E8;
\ttext-align: center;
\tborder: 1px solid #A5A5A5;
\t";
        // line 3477
        echo $this->getAttribute($this, "border_radius", array(0 => 4), "method");
        echo "
\t";
        // line 3478
        echo $this->getAttribute($this, "box_shadow", array(0 => "0 0 3px rgba(0,0,0,0.5)"), "method");
        echo "
}
#dp_chat_end_confirm article p {
\tmargin: 8px 0 8px 0;
}

#dp_chat_done {
\tposition: absolute;
\ttop: 0;
\tright: 0;
\tbottom: 0;
\tleft: 0;
\toverflow: auto;
}


#dp_overlay .hidden-file-upload, #dp .widget-deskpro .hidden-file-upload {
\tposition: relative;
\twidth: 150px;
}

#dp_overlay .hidden-file-upload span, #dp .widget-deskpro .hidden-file-upload span {
\tposition: absolute;
\ttop: 0;
\tleft: 0;
\tright: 0;
\tbottom: 0;
\tfont-size: 11px;
}

#dp_overlay .hidden-file-upload span.link, #dp .widget-deskpro .hidden-file-upload span.link {
\tcolor: blue;
\ttext-decoration: underline;
}

#dp_overlay .hidden-file-upload input, #dp .widget-deskpro .hidden-file-upload input {
\tfilter: alpha(opacity=0);
\t-khtml-opacity: 0;
\t-moz-opacity: 0;
\topacity: 0;
}

#dp_chat_end_real_alt,
#dp_chat_end_real_unassigned {
\tdisplay: block;
\tmargin-top: 5px;
\tmargin-left: 4px;
\tfont-size: 11px;
}

/***********************************************************************************************************************
* Website Widget
***********************************************************************************************************************/

#dp_widget_win #dp {
\tposition: absolute;
\ttop: 11px;
\tleft: 11px;
\tright: 15px;
\tbottom: 11px;
}

#dp .widget-deskpro{
\tposition: absolute;
\ttop: 0;
\tright: 0;
\tbottom: 0;
\tleft: 0;
\tcolor:#7a7f87;
\tfont:14px/17px 'Helvetica Neue',Helvetica,Arial, sans-serif;
}

#dp .widget-deskpro form{
\tmargin:0;
\tpadding:0;
\tborder:0;
\toutline:0;
\tfont-size:100%;
\tvertical-align: baseline;
\tbackground:none;
}
#dp .widget-deskpro a{
\tcolor:#000;
\ttext-decoration:underline;
}
#dp .widget-deskpro p{margin:0 0 12px;}
#dp .widget-deskpro a:hover{text-decoration:none;}
#dp .widget-deskpro a:focus{outline:none;}
#dp .widget-deskpro input, #dp .widget-deskpro textarea, #dp .widget-deskpro select{
\tcolor: #7A7F87;
\tfont: 14px/17px 'Helvetica Neue',Helvetica,Arial,sans-serif;
\tvertical-align:middle;
\tpadding:0;
\tmargin:0;
}
#dp .widget-deskpro form,
#dp .widget-deskpro fieldset{border-style:none;}
#dp .widget-deskpro ol{padding:0 0 14px 24px;}
#dp .widget-deskproul li,
#dp .widget-deskpro ol li{list-style-position:outside;}
#dp .widget-deskpro ul li{list-style-type:disc;}
#dp .widget-deskpro ul{padding:0 0 14px 18px;}
#dp .widget-deskpro .widget-close{
\tposition:absolute;
\ttext-indent:-9999px;
\toverflow:hidden;
\tbackground:url(";
        // line 3584
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/widget/btn-close.png) no-repeat;
\theight:28px;
\twidth:28px;
\tright:-13px;
\ttop:-15px;
}
.clearfix:after {
\tclear: both;
\tcontent:'';
\tdisplay: block;
}

#dp .widget-login-container {
\tposition:absolute;
\ttop: 0;
\tleft: 0;
\tright: 0;
\tbottom: 0;
\tbackground: #fff;;
\tborder:1px solid #c5c5c5;
\tbox-shadow:0 0 9px #e7e7e7;
\t-webkit-box-shadow: 0 0 9px #e7e7e7;
\t-moz-box-shadow: 0 0 9px #e7e7e7;
\tpadding: 25px;
}

#dp .widget-deskpro .widget-container{
\tposition:absolute;
\ttop: 0;
\tleft: 0;
\tright: 0;
\tbottom: 0;
\tbackground:#fff url(";
        // line 3616
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/widget/bg-widget-container.gif) repeat-y;
\tborder:1px solid #c5c5c5;
\tbox-shadow:0 0 9px #e7e7e7;
\t-webkit-box-shadow: 0 0 9px #e7e7e7;
\t-moz-box-shadow: 0 0 9px #e7e7e7;
}
/*@no_rtl*/
.rtl #dp .widget-deskpro .widget-container {
\tbackground:#fff url(";
        // line 3624
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/widget/bg-widget-container-rtl.gif) repeat-y 100%;
}
/*@/no_rtl*/

#dp .widget-deskpro .widget-logo {
\tposition: absolute;
\tbottom: 5px;
\tright: 5px;
}

#dp .widget-deskpro .widget-logo {
\tcolor: #B2B2B2;
\tfont-size: 8pt;
\tpadding-left: 10px;
}

#dp .widget-deskpro .widget-logo a {
\tline-height: 16px;
\tdisplay: block;
\tcolor: #B2B2B2;
\ttext-decoration: none;
\tbackground: url(";
        // line 3645
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/dp-logo-bw-16.png) no-repeat 0 50%;
\tpadding-left: 20px;
}

#dp .widget-deskpro .widget-logo a:hover {
\tbackground-image: url(";
        // line 3650
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/dp-logo-16.png);
}

#dp .widget-deskpro .widget-logo a strong {
\tfont-weight: bold;
\ttext-decoration: underline;
}

#dp .widget-deskpro .widget-logo a:hover {
\tcolor: #000;
}

#dp .widget-deskpro .aside .head{
\tposition: absolute;
\ttop: 0;
\tleft: 0;
\tright: 1px;
\tpadding:14px 0 0 24px;
\theight:59px;
\tbackground:#dce3ff url(";
        // line 3669
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/widget/bg-aside-head.gif) repeat-x;
}

#dp .widget-deskpro .aside .head .helpdesk-link {
\tfont-size: 11px;
\tmargin-top: 5px;
\tmargin-left: 3px;
}

#dp .widget-deskpro .aside .head .helpdesk-link a {
\ttext-decoration: none;
}

#dp .widget-deskpro .btn{
\tposition: relative;
\tborder:1px solid #c2c3c8;
\tfloat:left;
\ttext-decoration:none;
\tbackground:url(";
        // line 3687
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/widget/bg-btn.gif) repeat-x;
\theight:34px;
\tcursor:pointer;
\tcolor:#707680;
\tborder-radius: 4px;
\t-moz-border-radius: 4px;
\t-webkit-border-radius: 4px;
\tfont:bold 12px/15px 'Helvetica Neue',Helvetica,Arial, sans-serif;
}
#dp .widget-deskpro .btn i.icon{
\tposition: absolute;
\tleft: 7px;
\ttop: 12px;
}
#dp .widget-deskpro .btn i.icon-mail {
\ttop: 12px;
\tleft: 8px;
}
#dp .widget-deskpro .btn:hover i.icon,
#dp .widget-deskpro .active.btn i.icon {
\ttop: 11px;
}
#dp .widget-deskpro .btn i.icon-call{
\twidth:17px;
\theight:12px;
\tbackground:url(";
        // line 3712
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/widget/icon-call.png) no-repeat;
}
#dp .widget-deskpro .btn:hover i.icon-call,
#dp .widget-deskpro .btn.active i.icon-call{background:url(";
        // line 3715
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/widget/icon-call-active.png) no-repeat;}
#dp .widget-deskpro .btn i.icon-mail{
\twidth:15px;
\theight:12px;
\tbackground:url(";
        // line 3719
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/widget/icon-mail.png) no-repeat;
}
#dp .widget-deskpro .btn:hover i.icon-mail,
#dp .widget-deskpro .btn.active i.icon-mail{background:url(";
        // line 3722
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/widget/icon-mail-hover.png) no-repeat;}
#dp .widget-deskpro .btn i.icon-chat{
\twidth:16px;
\theight:14px;
\tbackground:url(";
        // line 3726
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/widget/icon-chat.png) no-repeat;
}
#dp .widget-deskpro .btn:hover i.icon-chat,
#dp .widget-deskpro .btn.active i.icon-chat{background:url(";
        // line 3729
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/widget/icon-chat-hover.png) no-repeat;}
#dp .widget-deskpro a.btn{
\tline-height: 34px;
\tpadding:0 10px 0 30px;
}
#dp .widget-deskpro .btn:hover,
#dp .widget-deskpro .btn.active{
\tborder-color:#549429;
\ttext-decoration:none;
\tcolor:#fff;
\tbackground:url(";
        // line 3739
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/widget/btn-hover.gif) repeat-x;
}

#dp .widget-deskpro table.form-table th {
\tfont-size: 12px;
\tfont-weight: normal;
\ttext-align: right;
\tpadding: 4px 10px 4px 2px;
\tvertical-align: middle;
}

#dp .widget-deskpro table.form-table td {
\tvertical-align: top;
\tpadding: 2px 10px 2px 2px;
}

#dp .widget-deskpro .txt{
\tpadding:0 8px;
\tborder:1px solid #d1d4d9;
\theight:24px;
\tposition:relative;
\tborder-radius: 4px;
\t-moz-border-radius: 4px;
\t-webkit-border-radius: 4px;
\tbackground:url(";
        // line 3763
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/widget/bg-txt-field.gif) repeat-x;
}
#dp .widget-deskpro .txt.correct{
\tborder-color:#72b843;
\tbox-shadow: 0 0 20px 1px #e0efd6;
\t-webkit-box-shadow: 0 0 20px 1px #e0efd6;
\t-moz-box-shadow: 0 0 20px 1px #e0efd6;
}
#dp .widget-deskpro .txt.error{
\tborder-color:#db4e4e;
\tbox-shadow: 0 0 20px 1px #f9e0e0;
\t-webkit-box-shadow: 0 0 20px 1px #f9e0e0;
\t-moz-box-shadow: 0 0 20px 1px #f9e0e0;
}
#dp .widget-deskpro .error .tooltip-error {
\tdisplay: block;
}
#dp .widget-deskpro .tooltip-error{
\tdisplay: none;
\tfont-size:9px;
\tline-height:100%;
\tcolor:#fff;
}
#dp .widget-deskpro .tooltip-error>span{
\tpadding: 6px;
\theight:20px;
\tcolor: #f00;
\tdisplay: block;
}
#dp .widget-deskpro .txt input{
\tfloat:left;
\tborder:0;
\tbackground:none;
\twidth:100%;
}
#dp .widget-deskpro input[type=\"text\"],
#dp .widget-deskpro input[type=\"password\"]{
\tcolor:#7a7f87;
\theight:17px;
\twidth:95%;
\tpadding:3px 0;
\tfont:12px 'Helvetica Neue',Helvetica,Arial, sans-serif;
\tbox-shadow: none;
}
#dp .widget-deskpro .search-form input{
\tfont-size:12px;
\twidth:147px;
}
#dp .widget-deskpro .search-form #search_box_go{
\theight: 26px;
}
#dp .widget-deskpro .search-form .txt{
\tpadding-left:30px;
\twidth:147px;
\tmargin-right:9px;
\tfloat: left;
}
#dp .widget-deskpro .search-form .icon-search{
\tposition:absolute;
\ttext-indent:-9999px;
\toverflow:hidden;
\tbackground:url(";
        // line 3824
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/widget/icon-search.gif) no-repeat;
\theight:13px;
\twidth:13px;
\tleft:9px;
\ttop:6px;
}
#dp .widget-deskpro .search-form .btn{width:79px;}
#dp .widget-deskpro .aside .area{
\tposition: absolute;
\ttop: 73px;
\tleft: 0;
\tright: 0;
\tbottom: 0;
\toverflow-y: auto;
\toverflow-x: hidden;
}
#dp .widget-deskpro .files-list{
\tpadding:1px 0 0;
\tmargin:0 0 0 -1px;
\tfloat:left;
}
#dp .widget-deskpro .files-list ul {
\tmargin: 0;
\tmargin-top: -1px;
\tpadding: 0;
}
#dp .widget-deskpro .files-list li{
\tlist-style:none;
\tmargin-top:-1px;
\tvertical-align:top;
\tpadding: 5px 0 5px 10px;
\tborder:1px solid #cdcdce;
\tfont-size:11px;
\tcolor:#8F939B;
\tfloat:left;
\twidth:317px;
\tline-height: 160%;
}

#dp .widget-deskpro .files-list li .top-row {
\tposition: relative;
\twhite-space: nowrap;
\toverflow: hidden;
\theight: 20px;
}

#dp .widget-deskpro .files-list li .summary {
\tposition: relative;
\twhite-space: nowrap;
\toverflow: hidden;
\theight: 18px;
}

#dp .widget-deskpro .files-list li .fadeaway {
\tbackground: -moz-linear-gradient(left,  rgba(242,243,247,0) 0%, rgba(242,243,247,0) 58%, rgba(242,243,247,1) 100%); /* FF3.6+ */
\tbackground: -webkit-gradient(linear, left top, right top, color-stop(0%,rgba(242,243,247,0)), color-stop(58%,rgba(242,243,247,0)), color-stop(100%,rgba(242,243,247,1))); /* Chrome,Safari4+ */
\tbackground: -webkit-linear-gradient(left,  rgba(242,243,247,0) 0%,rgba(242,243,247,0) 58%,rgba(242,243,247,1) 100%); /* Chrome10+,Safari5.1+ */
\tbackground: -o-linear-gradient(left,  rgba(242,243,247,0) 0%,rgba(242,243,247,0) 58%,rgba(242,243,247,1) 100%); /* Opera 11.10+ */
\tbackground: -ms-linear-gradient(left,  rgba(242,243,247,0) 0%,rgba(242,243,247,0) 58%,rgba(242,243,247,1) 100%); /* IE10+ */
\tbackground: linear-gradient(left,  rgba(242,243,247,0) 0%,rgba(242,243,247,0) 58%,rgba(242,243,247,1) 100%); /* W3C */
\tfilter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#00f2f3f7', endColorstr='#f2f3f7',GradientType=1 ); /* IE6-9 */

\tposition: absolute;
\ttop: 0;
\tright: 0;
\tbottom: 0;
\twidth: 50px;
}

/*@no_rtl*/
.rtl #dp .widget-deskpro .files-list li .fadeaway {
\tbackground: -moz-linear-gradient(left,  rgba(242,243,247,1) 0%, rgba(242,243,247,0) 42%, rgba(242,243,247,0) 100%); /* FF3.6+ */
\tbackground: -webkit-gradient(linear, left top, right top, color-stop(0%,rgba(242,243,247,1)), color-stop(42%,rgba(242,243,247,0)), color-stop(100%,rgba(242,243,247,0))); /* Chrome,Safari4+ */
\tbackground: -webkit-linear-gradient(left,  rgba(242,243,247,1) 0%,rgba(242,243,247,0) 42%,rgba(242,243,247,0) 100%); /* Chrome10+,Safari5.1+ */
\tbackground: -o-linear-gradient(left,  rgba(242,243,247,1) 0%,rgba(242,243,247,0) 42%,rgba(242,243,247,0) 100%); /* Opera 11.10+ */
\tbackground: -ms-linear-gradient(left,  rgba(242,243,247,1) 0%,rgba(242,243,247,0) 42%,rgba(242,243,247,0) 100%); /* IE10+ */
\tbackground: linear-gradient(left,  rgba(242,243,247,1) 0%,rgba(242,243,247,0) 42%,rgba(242,243,247,0) 100%); /* W3C */
\tfilter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f2f3f7', endColorstr='#00f2f3f7',GradientType=1 ); /* IE6-9 */
}
/*@/no_rtl*/

#dp .widget-deskpro .files-list li i {
\t";
        // line 3906
        echo $this->getAttribute($this, "opacity", array(0 => 0.5), "method");
        echo "
}

#dp .widget-deskpro .files-list .summary{
\twidth:100%;
\toverflow:hidden;
}
#dp .widget-deskpro .files-list .summary span{
\tfloat:left;
\tbackground:url(";
        // line 3915
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/widget/sep01.gif) no-repeat 0 50%;
\tpadding:0 5px 0 6px;
}
#dp .widget-deskpro .files-list .summary span:first-child{
\tbackground:none;
\tpadding-left:0;
}
#dp .widget-deskpro .files-list a{
\tcolor:#7F8394;
}
#dp .widget-deskpro .files-list .top-row a{
\tfont-weight: bold;
\tfont-size: 110%;
\ttext-shadow: 0 1px rgba(255,255,255, 0.3);
}
#dp .widget-deskpro .files-list li:hover{
\tborder-color:#75bc45;
\tposition:relative;
}
#dp .widget-deskpro .files-list h4{
\tmargin:0 0 5px;
\tfont:bold 14px/17px 'Helvetica Neue',Helvetica,Arial, sans-serif;
}
#dp .widget-deskpro .files-list h4 a{
\tcolor:#7d9dd4;
}
#dp .widget-deskpro .content{
\tposition: absolute;
\ttop: 0;
\tleft: 328px;
\tright: 0;
\tbottom: 0;
}
#dp .widget-deskpro .content .head{
\tposition: absolute;
\ttop: 0;
\tright: 0;
\tleft: 0;
\tbackground:url(";
        // line 3953
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/widget/bg-content-head.gif) repeat-x 0 0;
\tborder:1px solid #c5c5c5;
\theight:55px;
\tmargin-left:-1px;
\tborder-width:0 0 1px 1px;
\tpadding:18px 0 0 21px;
}

/*@no_rtl*/
.rtl #dp .widget-deskpro .content .head {
\tbackground:url(";
        // line 3963
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/widget/bg-content-head-rtl.gif) repeat-x 100% 0;
}
/*@/no_rtl*/

#dp .widget-deskpro .content .head .btn{margin-right:20px;}
#dp .widget-deskpro .socials{
\twidth:100%;
\toverflow:hidden;
\tmargin:0 0 12px;
}
#dp .widget-deskpro .socials>div{float:right;}
#dp .widget-deskpro .socials span{
\tfloat:left;
\tmargin-right:14px;
}
#dp .widget-deskpro .socials ul{
\tfloat:left;
\tpadding:0;
\tmargin:0;
}
#dp .widget-deskpro .socials li{
\tfloat:left;
\tlist-style:none;
\tmargin:0 0 0 7px;
}
#dp .widget-deskpro .widget-content{
\tposition: absolute;
\ttop: 74px;
\tleft: 0;
\tright: 0;
\tbottom: 20px;
\toverflow: auto;
}

#dp .widget-deskpro .widget-content .tab {
\tposition: relative;
\tpadding: 20px;
\tpadding-bottom: 70px;
}

#dp .widget-deskpro .fields-row{

}
#dp .widget-deskpro .file-row{padding:0;}
#dp .widget-deskpro .selector{
\tfloat:left;
\tbackground:url(";
        // line 4009
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/widget/bg-select.png) no-repeat 0 50%;
\theight:27px;
\twidth:183px;
\tposition:relative;
\tborder-radius: 4px;
\t-moz-border-radius: 4px;
\t-webkit-border-radius: 4px;
\t-webkit-background-clip:padding-box;-moz-background-clip:padding;background-clip:padding-box;
\tborder-top: 1px solid #BEC0C4;
\tborder-bottom: 1px solid #BEC0C4;
}
#dp .widget-deskpro .selector>span{
\tdisplay:block;
\tpadding:5px 0 0 7px;
\tfont-size: 12px;
}
div.selector select {
\tposition:absolute;
\tleft:0;
\ttop:0;
}
#dp .widget-deskpro .fields-row:after {
\tclear: both;
\tcontent:'';
\tdisplay: block;
}
#dp .widget-deskpro .alignright{
\tfloat:right;
}
#dp .widget-deskpro select{
\tfloat:left;
}

#dp .widget-deskpro .fields-row select {
\twidth:183px;
\theight:28px;
}

#dp .widget-deskpro .dp-inplace-drop select {
\twidth: 183px;
\theight: 25px;
}

#dp .widget-deskpro .textarea{
\tborder:1px solid #d1d4d9;
\tpadding: 3px 5px;
\tposition:relative;
\tborder-radius: 4px;
\t-moz-border-radius: 4px;
\t-webkit-border-radius: 4px;
\tposition:relative;
\tbackground:#fff url(";
        // line 4060
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/widget/bg-txt-field.gif) repeat-x;
\tmargin-bottom: 5px;
}
#dp .widget-deskpro .textarea textarea{
\tborder:0;
\tbackground:none;
\tpadding:0;
\tmargin:0;
\toverflow:auto;
\theight:124px;
\t";
        // line 4070
        echo $this->getAttribute($this, "box_shadow", array(0 => "none"), "method");
        echo "
\twidth:98%;
\tpadding:3px 0;
\tfont:12px 'Helvetica Neue',Helvetica,Arial, sans-serif;
}
#dp .widget-deskpro .textarea textarea:focus,
#dp .widget-deskpro input:focus{
\toutline: none;
\t";
        // line 4078
        echo $this->getAttribute($this, "box_shadow", array(0 => "none"), "method");
        echo "
}
#dp .widget-deskpro .btn-activity{
\tposition:relative;
\tborder-radius: 4px;
\t-moz-border-radius: 4px;
\t-webkit-border-radius: 4px;
\tborder:1px solid #5081c9;
\tcolor:#fff;
\theight:33px;
\tdisplay:inline-block;
\tcursor:pointer;
\tbackground:url(";
        // line 4090
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/widget/btn-activity.gif) repeat-x;
\tbox-shadow: 0 2px 3px #c9c9c9;
\t-webkit-box-shadow: 0 2px 3px #c9c9c9;
\t-moz-box-shadow:0 2px 3px #c9c9c9;
\tfont-weight:bold;
\tfont-size:12px;
\tline-height: 16px;
}
#dp .widget-deskpro .notification{
\tdisplay:block;
\tcolor:#ff4242;
\twidth:100%;
\tclear:both;
\tfont-size:12px;
\tpadding:6px 0 0;
}
.MultiFile-wrap{width:100%;}
.MultiFile-list{
\tpadding:12px 0 0;
}
.MultiFile-label{
\twidth:100%;
\toverflow:hidden;
\tpadding: 0 0 3px;
}
.MultiFile-label .MultiFile-title{
\tfloat:left;
\tcolor:#7d8186;
\tfont-weight:bold;
\tfont-size:12px;
\tpadding:2px 0 0;
}
.MultiFile-label .MultiFile-remove{
\tfloat:left;
\tbackground:url(";
        // line 4124
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/widget/file-remove.gif) no-repeat;
\theight:15px;
\ttext-indent:-9999px;
\toverflow:hidden;
\twidth:15px;
\tvertical-align:middle;
\tmargin: 3px 0 0 12px;
}

#dp .widget-deskpro .hidden-file-upload span.link {
\toverflow:hidden;
\tbackground:url(";
        // line 4135
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/widget/btn-file.png) no-repeat;
\theight:29px;
\twidth:108px;
\tdisplay: block;
\ttext-indent: -1000px;
}

#dp .widget-deskpro #tab-mail{padding-top:29px;}

#dp .widget-deskpro ul.file-list li {
\tlist-style: none;
}
#dp .widget-deskpro ul.file-list li a {
\tcolor: #555;
\ttext-decoration: none;
}

#dp .widget-deskpro ul.file-list li a:hover {
\ttext-decoration: underline;
}

#dp .widget-deskpro .dp-new-feedback {
\tmargin: 0;
\tmargin-bottom: 25px;
\tpadding: 0;
\tbackground: transparent;
\tborder: none;
}

#dp .widget-deskpro .dp-new-feedback table {
\tborder-color: #D1D4D9;
}

#dp .widget-deskpro .dp-new-feedback .dp-msg {
\twhite-space: nowrap;
}
#dp .widget-deskpro .dp-new-feedback .dp-cat {
\twhite-space: nowrap;
}
#dp .widget-deskpro .dp-new-feedback .dp-title {
\tpadding-left: 5px;
}

/***********************************************************************************************************************
* Labels
***********************************************************************************************************************/

#dp section.dp-labels-search {
\tmargin-top: 0;
}

#dp .dp-labels-search > article {
\tmargin: 0;
}

#dp .dp-labels-search {
\tmargin: 15px;
\tmargin-bottom: 30px;
\tbackground-color: #F5F5F5;
\tborder: 1px solid #DADADA;
\tpadding: 10px;
\t";
        // line 4196
        echo $this->getAttribute($this, "border_radius", array(0 => 6), "method");
        echo ";
}

#dp .dp-labels-search > header h2 {
\tfont-size: 11pt;
\tfont-weight: bold;
\tcolor: #8D8D8D;
}

#dp .dp-labels-search .dp-search-bar .dp-add-on {
\tpadding: 5px 8px 5px 8px;
\tfont-size: 14pt;
\theight: 22px;
\tvertical-align: top;
}

#dp .dp-labels-search .dp-search-bar .dp-input-prepend,
#dp .dp-labels-search .dp-search-bar .dp-suggest-title {
\tmargin-bottom: 0;
}

#dp .dp-labels-search .dp-search-bar input.dp-suggest-title {
\twidth: 96%;
\tpadding: 5px 8px 5px 0;
\tfont-size: 14pt;
\theight: 22px;
\ttext-decoration: underline;
\tcursor: pointer;
}

#dp .dp-labels-search .dp-search-bar .dp-btn.dp-go-btn {
\tpadding: 5px 10px 5px 10px;
\tfont-size: 14pt;
\tmargin-top: 2px;=
}


dp .dp-labels-search td {
\tvertical-align: middle !important;
}

#dp .dp-labels-search td.dp-msg {
\tbackground-color: #fff;
\tborder: 1px solid #CCCCCC;
\tpadding: 0 5px 0 5px;
\tfont-size: 13pt;
\t";
        // line 4242
        echo $this->getAttribute($this, "border_radius", array(0 => array("tl" => 3, "bl" => 3)), "method");
        echo "
}

#dp .dp-labels-search td.dp-msg2 {
\tborder-right: none;
}

#dp .dp-labels-search td.dp-cat {
\tborder: 1px solid #CCCCCC;
\tborder-right: none;
\tpadding: 0 0px 0 5px;
\tfont-size: 10pt;
\toverflow: hidden;
\tborder-right: 1px solid #C8C8C8;
\tbackground: rgb(255,255,255);
\tbackground: -moz-linear-gradient(top,  rgb(255,255,255) 0%, rgb(229,229,229) 100%);
\tbackground: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgb(255,255,255)), color-stop(100%,rgb(229,229,229)));
\tbackground: -webkit-linear-gradient(top,  rgb(255,255,255) 0%,rgb(229,229,229) 100%);
\tbackground: -o-linear-gradient(top,  rgb(255,255,255) 0%,rgb(229,229,229) 100%);
\tbackground: -ms-linear-gradient(top,  rgb(255,255,255) 0%,rgb(229,229,229) 100%);
\tbackground: linear-gradient(top,  rgb(255,255,255) 0%,rgb(229,229,229) 100%);
\tfilter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e5e5e5',GradientType=0 );
}

#dp .dp-labels-search td.dp-cat select {
\tposition: absolute;
\ttop: 0;
\tleft: 0;
\tz-index: 3;
\t";
        // line 4271
        echo $this->getAttribute($this, "opacity", array(0 => 0), "method");
        echo "
}

#dp .dp-labels-search td.dp-cat em {
\tz-index: 2;
\tposition: relative;
\tdisplay: block;
\tfont-style: normal;
\tpadding: 3px 18px 3px 6px;
\tcursor: pointer;
\tposition: relative;
}

#dp .dp-labels-search td.dp-cat em {
\tbackground: transparent;
\tborder:none;
}

#dp .dp-labels-search td.dp-cat em i {
\tdisplay: block;
\tposition: absolute;
\ttop: 11px;
\tright: 5px;
\theight: 0;
\twidows: 0;
\tborder-left: 4px solid transparent;
\tborder-right: 4px solid transparent;
\tborder-top: 4px solid #888;
\tcontent: \"\";

}

#dp .dp-labels-search td.dp-title {
\tbackground-color: #FFF;
\tborder: 1px solid #CCCCCC;
\tborder-left: none;
\tpadding: 0;
\tfont-size: 13pt;
\t";
        // line 4309
        echo $this->getAttribute($this, "border_radius", array(0 => array("tr" => 3, "br" => 3)), "method");
        echo "
}

#dp .dp-labels-search td.dp-title input {
\tborder: none;
}
#dp .dp-labels-search td.dp-title input:focus {
\t";
        // line 4316
        echo $this->getAttribute($this, "box_shadow", array(0 => "none"), "method");
        echo "
}

#dp .dp-labels-search td.dp-go {
\tpadding-left: 6px;
}

#dp .dp-labels-search .dp-tag-cloud h4 {
\tfont-size: 15pt;
\tfont-weight: normal;
\tfont-family: \"HelveticaNeue-Light\",\"Helvetica Neue Light\",\"Helvetica Neue\",sans-serif;
\tcolor: #C3C2C2;
\tmargin: 15px 0 15px 15px;
}

#dp .dp-labels-search .dp-tag-cloud {
\tmargin: 10px 0 0 0;
}

.dp-tag-cloud ul {
\tmargin: 0;
\tpadding: 0;
\ttext-align: center;
\tvertical-align: middle;
}

.dp-tag-cloud ul li {
\tdisplay: inline-block;
\tline-height: 100%;
\tmargin: 4px;
\tpadding: 3px;

\tvertical-align: middle;

\tbackground-color: #F5F5F5;
\tborder: 1px solid #DEDDDD;
\t";
        // line 4352
        echo $this->getAttribute($this, "border_radius", array(0 => 4), "method");
        echo "
}

.dp-tag-cloud ul li.dp-tag-size1 { font-size: 100%; }
.dp-tag-cloud ul li.dp-tag-size2 { font-size: 120%; }
.dp-tag-cloud ul li.dp-tag-size3 { font-size: 140%; }
.dp-tag-cloud ul li.dp-tag-size4 { font-size: 160%; }
.dp-tag-cloud ul li.dp-tag-size5 { font-size: 180%; }
.dp-tag-cloud ul li.dp-tag-size6 { font-size: 200%; }
.dp-tag-cloud ul li.dp-tag-size7 { font-size: 220%; }
.dp-tag-cloud ul li.dp-tag-size8 { font-size: 240%; }
.dp-tag-cloud ul li.dp-tag-size9 { font-size: 260%; }
.dp-tag-cloud ul li.dp-tag-size10 { font-size: 280%; }

#dp .dp-related-search {
\tmargin-bottom: 25px;
}

#dp .dp-related-search ul {
\tmargin: 10px 0 0 10px;
}

#dp .dp-related-search ul li {
\tlist-style-type: none;
\tmargin: 0 0 10px 0;
}

#dp .dp-overlay-outer {
\tposition: fixed;
\ttop: 30px;

\tbackground-color: #fff;
\tborder: 1px solid #B4B6B8;
\tz-index: 10000;

\tbox-shadow: 3px 1px 7px 0px rgba(120, 120, 120, 0.6);
}

#dp .dp-post-overlay {
\twidth: 620px;
\theight: 350px;
}

#dp .dp-overlay {
\tposition: absolute;
\ttop: 0; right: 0; bottom: 0; left: 0;
}

#dp .dp-overlay .dp-overlay-inner {
\tposition: absolute;
\ttop: 1px; right: 1px; bottom: 1px; left: 1px;
\tz-index: 100;
}

#dp .dp-overlay .dp-title {
\theight: 25px;

\tbackground-color: #ECF0F4;
\tborder-bottom: 1px solid #C9CACA;
}

#dp .dp-overlay .dp-title button {
\tmargin-top: 1px;
\tmargin-left: 3px;
}

#dp .dp-overlay .dp-title h3 {
\tline-height: 25px;
\tfont-size: 14px;
\tfont-weight: normal;
\tpadding-left: 8px;
}

#dp .dp-overlay .dp-content {
\tposition: absolute;
\ttop: 0; right: 0; bottom: 0; left: 0;
\toverflow: auto;

\tfont-size: 11px;
}

#dp .dp-overlay h3.title {
\tmargin: 10px 10px 0 10px;
\tmargin-bottom: 0;
\tpadding-bottom: 6px;
}

#dp .dp-with-title .dp-content {
\ttop: 26px;
}
#dp .dp-with-footer .dp-content {
\tbottom: 26px;
}

#dp .dp-overlay .dp-content .dp-post {
\tpadding: 10px;
}

#dp .dp-overlay .dp-loading-msg {
\ttext-align: center;
\tbackground: url(";
        // line 4452
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/spinners/editor-loading.gif) no-repeat 50% 0;
\tpadding: 0;
\tpadding-top: 100px;
\tmargin: 35px;
\tfont-size: 14pt;
}

#dp .dp-overlay .dp-content .dp-post p {
\tline-height: 130%;
}

#dp .dp-overlay .dp-controls {
\tmargin: 0;
\tpadding: 10px;
\ttext-align: center;

\tbackground-color: #ECF0F4;
\tborder-top: 1px solid #D9DBDB;
}

#dp .dp-overlay .dp-controls button {
\tmargin-left: 8px;
\tmargin-right: 8px;
}

#dp .dp-overlay .dp-controls p {

}

#dp .dp-overlay .dp-open-full {
\tfloat: right;
\tz-index: 102;
\tmargin-right: 15px;
\tmargin-top: 7px;
\tcursor: pointer;
}

#dp .dp-backdrop {
\tposition: fixed;
\ttop: 0; right: 0; bottom: 0; left: 0;
\tz-index: 1000;
}

#dp .dp-backdrop.dp-faded {
\tbackground-color: rgba(255,255,255, 0.5);
}

#dp .dp-overlay .dp-close-btn {
\ttop: -10px;
\tright: -10px;
\tz-index: 101;
}

#dp .dp-close-btn {
\tposition: absolute;
\ttop: 0;
\tright: 0;
\twidth: 18px;
\theight: 18px;
\tdisplay: block;

\tbackground: url(";
        // line 4513
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/close-btn.png) no-repeat top right;
\tcursor: pointer;
\toverflow: hidden;
\ttext-indent: -100px;
}

#dp .dp-overlay .dp-comments-placeholder {
\tfont-size: 13px;
\tfont-family: \"Helvetica Neue\",Helvetica,Arial,sans-serif;
\tborder: 1px solid transparent;
\tborder-bottom: 1px solid #D9DBDB;
\tmargin:  0 -1px 0 -1px;
\tcursor: pointer;
}

#dp #auto-sign-in-overlay {
\twidth: 400px;
\theight: 220px;
\ttop: 200px;
\tleft: 200px;
}

/***********************************************************************************************************************
* Chat Log
***********************************************************************************************************************/

#dp .dp-chat-log-box {
\tmargin-top: 10px;
}

#dp .dp-chat-log-box tr th, #dp .dp-chat-log-box tr td {
\tpadding: 4px 10px;
}

#dp .dp-chat-log-box tr.sys-message th, #dp .dp-chat-log-box tr.sys-message td {
\tbackground-color: #FFF2C3;
}

#dp .dp-chat-log-box tr.agent-message th, #dp .dp-chat-log-box tr.agent-message td {
\tbackground-color: #E2F7FF;
}

#dp .dp-chat-log-box tr th.author {
\ttext-align: right;
}

#dp .dp-chat-log-box tr td.time {
\ttext-align: right;
\tfont-size: 9px;
}

/***********************************************************************************************************************
* Tweet
***********************************************************************************************************************/

#dp .dp-status-body {
\toverflow: hidden;
}

#dp .dp-status-body .dp-photo {
\tfloat: left;
}

#dp .dp-status-body .dp-main-status-body {
\tmargin-left: 54px;
}

#dp .dp-status-body .dp-main-status-body h4 {
\tfont-size: 13px;
\tfont-weight: normal;
\tfont-family: @body-font;
}

#dp .dp-status-body .dp-main-status-body h4 .dp-name {
\tfont-weight: bold;
}

#dp .dp-status-body .dp-main-status-body h4 .dp-screen-name {
\tfont-size: 80%;
\tcolor: #888888;
}

#dp .dp-status-body .dp-main-status-body .dp-extra-container {
\tmargin-top: 5px;
}

#dp .dp-status-body .dp-main-status-body .dp-extra-container .dp-in-reply-to {
\tcolor: #999999;
\tfont-size: 80%;
\tline-height: 100%;
}

#dp .dp-status-body .dp-main-status-body .timeago {
\tfloat: right;
\tmargin-top: 5px;
\tmargin-left: 5px;
\tcolor: #999999;
\tfont-size: 80%;
\tline-height: 100%;
}

#dp .dp-status-body .dp-status-buttons {
\toverflow: hidden;
\tlist-style: none;
\tmargin-top: 10px;
\tmargin-left: 54px;
\tpadding-top: 5px;
\tborder-top: 1px solid #D2D0D0;
}

#dp .dp-status-body .dp-status-buttons li {
\tlist-style: none;
\tfloat: left;
\tmargin-right: 20px;
}

#dp .dp-status-body .dp-status-buttons li em {
\tposition: relative;
\ttop: 2px;
\tpadding-right: 2px;
\tdisplay: inline-block;
\twidth: 16px;
\theight: 16px;
\tbackground: url(";
        // line 4636
        if (isset($context["stylevar"])) { $_stylevar_ = $context["stylevar"]; } else { $_stylevar_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_stylevar_, "static_path"), "html", null, true);
        echo "/images/user/twitter-icons.png) no-repeat;
}

#dp .dp-status-body .dp-status-buttons li.dp-reply em {
\tbackground-position: 0 0;
}

#dp .dp-status-body .dp-status-buttons li.dp-reply a:hover em {
\tbackground-position: -16px 0;
}

#dp .dp-status-body .dp-status-buttons li.dp-favorite em {
\tbackground-position: -32px 0;
}

#dp .dp-status-body .dp-status-buttons li.dp-favorite a:hover em {
\tbackground-position: -48px 0;
}

#dp .dp-status-body .dp-status-buttons li.dp-retweet em {
\tbackground-position: -80px 0;
}

#dp .dp-status-body .dp-status-buttons li.dp-retweet a:hover em {
\tbackground-position: -96px 0;
}

/***********************************************************************************************************************
* General styles
***********************************************************************************************************************/

#dp table.dp-grid-table th,
#dp table.dp-grid-table td {
\tpadding: 3px;
\tborder: 1px solid #ccc;
}

#recaptcha_area {
\tdirection: ltr;
}

";
        // line 4677
        $this->env->loadTemplate("UserBundle:Css:custom.css.twig")->display($context);
    }

    // line 19
    public function getgradient($_start_color = null, $_end_color = null, $_fallback_color = null, $_height = null)
    {
        $context = $this->env->mergeGlobals(array(
            "start_color" => $_start_color,
            "end_color" => $_end_color,
            "fallback_color" => $_fallback_color,
            "height" => $_height,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 20
            echo "\t";
            if (isset($context["fallback_color"])) { $_fallback_color_ = $context["fallback_color"]; } else { $_fallback_color_ = null; }
            if ((!$_fallback_color_)) {
                if (isset($context["start_color"])) { $_start_color_ = $context["start_color"]; } else { $_start_color_ = null; }
                $context["fallback_color"] = $_start_color_;
            }
            // line 21
            echo "\tbackground: ";
            if (isset($context["fallback_color"])) { $_fallback_color_ = $context["fallback_color"]; } else { $_fallback_color_ = null; }
            echo twig_escape_filter($this->env, $_fallback_color_, "html", null, true);
            echo ";
\tbackground: -webkit-gradient(linear, left bottom, left top, color-stop(0, ";
            // line 22
            if (isset($context["start_color"])) { $_start_color_ = $context["start_color"]; } else { $_start_color_ = null; }
            echo twig_escape_filter($this->env, $_start_color_, "html", null, true);
            echo "), color-stop(1, ";
            if (isset($context["end_color"])) { $_end_color_ = $context["end_color"]; } else { $_end_color_ = null; }
            echo twig_escape_filter($this->env, $_end_color_, "html", null, true);
            echo "));
\tbackground: -ms-linear-gradient(bottom, ";
            // line 23
            if (isset($context["start_color"])) { $_start_color_ = $context["start_color"]; } else { $_start_color_ = null; }
            echo twig_escape_filter($this->env, $_start_color_, "html", null, true);
            echo ", ";
            if (isset($context["end_color"])) { $_end_color_ = $context["end_color"]; } else { $_end_color_ = null; }
            echo twig_escape_filter($this->env, $_end_color_, "html", null, true);
            echo ");
\tbackground: -moz-linear-gradient(center bottom, ";
            // line 24
            if (isset($context["end_color"])) { $_end_color_ = $context["end_color"]; } else { $_end_color_ = null; }
            echo twig_escape_filter($this->env, $_end_color_, "html", null, true);
            echo " 0%, ";
            if (isset($context["start_color"])) { $_start_color_ = $context["start_color"]; } else { $_start_color_ = null; }
            echo twig_escape_filter($this->env, $_start_color_, "html", null, true);
            echo " 100%);
";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 27
    public function getborder_radius($_options = null)
    {
        $context = $this->env->mergeGlobals(array(
            "options" => $_options,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 28
            echo "\t";
            if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
            if ((!$this->env->getExtension('deskpro_templating')->isArray($_options_))) {
                // line 29
                echo "\t\t";
                if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
                $context["options"] = array("tr" => $_options_, "br" => $_options_, "bl" => $_options_, "tl" => $_options_);
                // line 30
                echo "\t";
            }
            // line 31
            echo "\t";
            if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
            if ((!(null === $this->getAttribute($_options_, "tr")))) {
                echo "-webkit-border-top-right-radius: ";
                if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_options_, "tr"), "html", null, true);
                echo "px;";
            }
            // line 32
            echo "\t";
            if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
            if ((!(null === $this->getAttribute($_options_, "br")))) {
                echo "-webkit-border-bottom-right-radius: ";
                if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_options_, "br"), "html", null, true);
                echo "px;";
            }
            // line 33
            echo "\t";
            if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
            if ((!(null === $this->getAttribute($_options_, "bl")))) {
                echo "-webkit-border-bottom-left-radius: ";
                if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_options_, "bl"), "html", null, true);
                echo "px;";
            }
            // line 34
            echo "\t";
            if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
            if ((!(null === $this->getAttribute($_options_, "tl")))) {
                echo "-webkit-border-top-left-radius: ";
                if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_options_, "tl"), "html", null, true);
                echo "px;";
            }
            // line 35
            echo "\t";
            if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
            if ((!(null === $this->getAttribute($_options_, "tr")))) {
                echo "-moz-border-radius-topright: ";
                if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_options_, "tr"), "html", null, true);
                echo "px;";
            }
            // line 36
            echo "\t";
            if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
            if ((!(null === $this->getAttribute($_options_, "br")))) {
                echo "-moz-border-radius-bottomright: ";
                if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_options_, "br"), "html", null, true);
                echo "px;";
            }
            // line 37
            echo "\t";
            if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
            if ((!(null === $this->getAttribute($_options_, "bl")))) {
                echo "-moz-border-radius-bottomleft: ";
                if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_options_, "bl"), "html", null, true);
                echo "px;";
            }
            // line 38
            echo "\t";
            if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
            if ((!(null === $this->getAttribute($_options_, "tl")))) {
                echo "-moz-border-radius-topleft: ";
                if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_options_, "tl"), "html", null, true);
                echo "px;";
            }
            // line 39
            echo "\t";
            if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
            if ((!(null === $this->getAttribute($_options_, "tr")))) {
                echo "border-top-right-radius: ";
                if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_options_, "tr"), "html", null, true);
                echo "px;";
            }
            // line 40
            echo "\t";
            if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
            if ((!(null === $this->getAttribute($_options_, "br")))) {
                echo "border-bottom-right-radius: ";
                if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_options_, "br"), "html", null, true);
                echo "px;";
            }
            // line 41
            echo "\t";
            if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
            if ((!(null === $this->getAttribute($_options_, "bl")))) {
                echo "border-bottom-left-radius: ";
                if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_options_, "bl"), "html", null, true);
                echo "px;";
            }
            // line 42
            echo "\t";
            if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
            if ((!(null === $this->getAttribute($_options_, "tl")))) {
                echo "border-top-left-radius: ";
                if (isset($context["options"])) { $_options_ = $context["options"]; } else { $_options_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_options_, "tl"), "html", null, true);
                echo "px;";
            }
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 45
    public function getbox_shadow($_args = null)
    {
        $context = $this->env->mergeGlobals(array(
            "args" => $_args,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 46
            echo "\t-webkit-box-shadow: ";
            if (isset($context["args"])) { $_args_ = $context["args"]; } else { $_args_ = null; }
            echo twig_escape_filter($this->env, $_args_, "html", null, true);
            echo ";
\t-moz-box-shadow: ";
            // line 47
            if (isset($context["args"])) { $_args_ = $context["args"]; } else { $_args_ = null; }
            echo twig_escape_filter($this->env, $_args_, "html", null, true);
            echo ";
\tbox-shadow: ";
            // line 48
            if (isset($context["args"])) { $_args_ = $context["args"]; } else { $_args_ = null; }
            echo twig_escape_filter($this->env, $_args_, "html", null, true);
            echo ";
";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 51
    public function getopacity($_op = null)
    {
        $context = $this->env->mergeGlobals(array(
            "op" => $_op,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 52
            echo "\topacity: ";
            if (isset($context["op"])) { $_op_ = $context["op"]; } else { $_op_ = null; }
            echo twig_escape_filter($this->env, $_op_, "html", null, true);
            echo ";
\tfilter:alpha(opacity=";
            // line 53
            if (isset($context["op"])) { $_op_ = $context["op"]; } else { $_op_ = null; }
            echo twig_escape_filter($this->env, ($_op_ * 100), "html", null, true);
            echo "); /* For IE8 and earlier */
";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    public function getTemplateName()
    {
        return "UserBundle:Css:main.css.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 2108,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 1383,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 1303,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 1173,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 1041,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 970,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 920,  1126 => 916,  1106 => 900,  1100 => 897,  1026 => 841,  997 => 822,  983 => 816,  975 => 812,  956 => 800,  939 => 786,  902 => 753,  894 => 749,  879 => 746,  757 => 631,  727 => 608,  716 => 605,  670 => 582,  528 => 447,  476 => 406,  435 => 380,  354 => 310,  341 => 133,  192 => 54,  321 => 107,  243 => 78,  793 => 351,  780 => 348,  758 => 341,  700 => 600,  686 => 292,  652 => 274,  638 => 266,  620 => 254,  545 => 218,  523 => 203,  494 => 183,  459 => 163,  438 => 150,  351 => 116,  347 => 114,  402 => 142,  268 => 67,  430 => 120,  411 => 136,  379 => 101,  322 => 94,  315 => 92,  289 => 84,  284 => 93,  255 => 65,  234 => 63,  1133 => 64,  1124 => 57,  1121 => 56,  1116 => 55,  1113 => 54,  1108 => 51,  1103 => 43,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 346,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 329,  996 => 320,  989 => 316,  985 => 315,  981 => 314,  977 => 313,  970 => 311,  966 => 310,  955 => 303,  952 => 302,  943 => 299,  936 => 296,  930 => 293,  919 => 290,  917 => 289,  908 => 287,  905 => 286,  896 => 280,  891 => 278,  877 => 270,  862 => 267,  857 => 265,  837 => 253,  832 => 250,  827 => 248,  821 => 247,  803 => 243,  778 => 235,  769 => 233,  765 => 232,  753 => 229,  746 => 225,  743 => 224,  735 => 613,  730 => 218,  720 => 213,  717 => 212,  712 => 209,  691 => 204,  678 => 586,  654 => 196,  587 => 239,  576 => 179,  539 => 171,  517 => 169,  471 => 155,  441 => 151,  437 => 142,  418 => 115,  386 => 125,  373 => 120,  304 => 102,  270 => 80,  265 => 77,  229 => 74,  477 => 449,  455 => 393,  448 => 164,  429 => 138,  407 => 95,  399 => 93,  389 => 126,  375 => 123,  358 => 116,  349 => 138,  335 => 131,  327 => 102,  298 => 112,  280 => 56,  249 => 99,  194 => 50,  142 => 24,  344 => 113,  318 => 106,  306 => 107,  295 => 98,  357 => 119,  300 => 130,  286 => 101,  276 => 240,  269 => 53,  254 => 67,  128 => 32,  237 => 44,  165 => 41,  122 => 31,  798 => 242,  770 => 113,  759 => 112,  748 => 337,  731 => 108,  721 => 606,  718 => 106,  708 => 104,  696 => 102,  617 => 188,  590 => 91,  553 => 87,  550 => 466,  540 => 84,  533 => 82,  500 => 186,  493 => 72,  489 => 181,  482 => 69,  467 => 67,  464 => 165,  458 => 64,  452 => 62,  449 => 156,  415 => 55,  382 => 124,  372 => 126,  361 => 82,  356 => 48,  339 => 97,  302 => 42,  285 => 40,  258 => 37,  123 => 32,  108 => 29,  424 => 135,  394 => 86,  380 => 80,  338 => 113,  319 => 66,  316 => 65,  312 => 110,  290 => 102,  267 => 88,  206 => 83,  110 => 25,  240 => 95,  224 => 35,  219 => 71,  217 => 73,  202 => 169,  186 => 46,  170 => 43,  100 => 17,  67 => 20,  14 => 1,  1096 => 291,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 836,  1013 => 275,  1007 => 274,  1002 => 823,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 300,  940 => 298,  937 => 248,  928 => 244,  926 => 243,  915 => 237,  912 => 236,  903 => 231,  898 => 230,  892 => 229,  889 => 748,  887 => 227,  884 => 747,  876 => 222,  874 => 269,  871 => 220,  863 => 214,  861 => 213,  858 => 212,  850 => 263,  843 => 206,  840 => 205,  815 => 245,  812 => 244,  808 => 199,  804 => 120,  799 => 196,  791 => 190,  785 => 238,  775 => 184,  771 => 183,  754 => 340,  728 => 170,  726 => 169,  723 => 168,  715 => 105,  711 => 604,  709 => 162,  706 => 603,  698 => 208,  694 => 156,  692 => 155,  689 => 154,  681 => 150,  677 => 149,  675 => 148,  663 => 143,  661 => 277,  650 => 195,  646 => 136,  629 => 129,  627 => 128,  625 => 127,  622 => 126,  598 => 119,  592 => 117,  586 => 115,  575 => 233,  566 => 103,  556 => 100,  554 => 177,  541 => 216,  536 => 91,  515 => 86,  511 => 85,  509 => 84,  488 => 164,  486 => 78,  483 => 77,  465 => 73,  463 => 397,  450 => 65,  432 => 60,  419 => 105,  371 => 46,  362 => 43,  353 => 73,  337 => 109,  333 => 105,  309 => 109,  303 => 279,  299 => 30,  291 => 96,  272 => 54,  261 => 95,  253 => 82,  239 => 64,  235 => 84,  213 => 86,  200 => 64,  198 => 53,  159 => 140,  149 => 45,  146 => 22,  131 => 34,  116 => 39,  79 => 18,  74 => 20,  71 => 11,  836 => 320,  817 => 319,  814 => 318,  811 => 317,  805 => 313,  787 => 657,  779 => 306,  776 => 305,  773 => 347,  761 => 296,  751 => 175,  747 => 293,  742 => 336,  739 => 291,  736 => 287,  724 => 214,  705 => 278,  702 => 601,  688 => 265,  680 => 263,  667 => 261,  662 => 260,  656 => 259,  649 => 258,  644 => 97,  641 => 194,  624 => 255,  613 => 187,  607 => 93,  597 => 225,  591 => 185,  584 => 218,  579 => 234,  563 => 230,  559 => 208,  551 => 221,  547 => 205,  537 => 201,  524 => 191,  512 => 185,  507 => 76,  504 => 182,  498 => 178,  485 => 176,  480 => 175,  472 => 171,  466 => 153,  460 => 71,  447 => 388,  442 => 162,  434 => 110,  428 => 377,  422 => 106,  404 => 149,  368 => 136,  364 => 83,  340 => 131,  334 => 111,  330 => 129,  325 => 100,  292 => 116,  287 => 115,  282 => 124,  279 => 109,  273 => 107,  266 => 105,  256 => 94,  252 => 93,  228 => 71,  218 => 78,  201 => 91,  64 => 16,  51 => 15,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 1705,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 707,  1829 => 706,  1827 => 705,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 689,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 676,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 666,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 638,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 617,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 611,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 602,  1559 => 601,  1552 => 598,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 584,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 573,  1477 => 567,  1473 => 566,  1467 => 562,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 520,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 511,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 1001,  1257 => 1000,  1254 => 479,  1250 => 478,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 460,  1196 => 958,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 938,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 924,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 425,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 898,  1099 => 420,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 880,  1076 => 359,  1070 => 875,  1057 => 352,  1052 => 404,  1045 => 854,  1040 => 852,  1036 => 283,  1032 => 395,  1028 => 394,  1023 => 393,  1020 => 392,  1016 => 391,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 821,  979 => 379,  974 => 312,  967 => 373,  962 => 803,  958 => 801,  954 => 253,  950 => 368,  945 => 367,  942 => 250,  938 => 365,  934 => 364,  927 => 361,  923 => 292,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 272,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 207,  841 => 321,  835 => 312,  830 => 249,  826 => 309,  822 => 308,  818 => 246,  813 => 306,  810 => 305,  806 => 304,  802 => 197,  795 => 118,  792 => 239,  789 => 350,  784 => 295,  782 => 187,  777 => 291,  772 => 289,  768 => 345,  763 => 343,  760 => 231,  756 => 230,  752 => 284,  745 => 281,  741 => 280,  738 => 335,  732 => 171,  719 => 273,  714 => 280,  710 => 310,  704 => 267,  699 => 103,  695 => 264,  690 => 263,  687 => 203,  683 => 587,  679 => 288,  672 => 284,  668 => 256,  665 => 201,  658 => 141,  645 => 270,  640 => 247,  634 => 96,  628 => 193,  623 => 238,  619 => 237,  611 => 248,  606 => 234,  603 => 467,  599 => 242,  595 => 231,  583 => 114,  580 => 180,  573 => 221,  560 => 101,  543 => 172,  538 => 209,  534 => 208,  530 => 450,  526 => 89,  521 => 443,  518 => 204,  514 => 78,  510 => 202,  503 => 75,  496 => 197,  490 => 193,  484 => 192,  474 => 190,  470 => 168,  446 => 144,  440 => 184,  436 => 147,  431 => 146,  425 => 117,  416 => 104,  412 => 98,  408 => 112,  403 => 172,  400 => 111,  396 => 133,  392 => 169,  385 => 166,  381 => 125,  367 => 117,  363 => 155,  359 => 118,  355 => 115,  350 => 112,  346 => 71,  343 => 70,  328 => 127,  324 => 138,  313 => 122,  307 => 132,  301 => 101,  288 => 27,  283 => 243,  271 => 107,  257 => 84,  251 => 64,  238 => 34,  233 => 94,  195 => 49,  191 => 62,  187 => 47,  183 => 72,  130 => 28,  88 => 29,  76 => 22,  115 => 29,  95 => 31,  655 => 275,  651 => 232,  648 => 231,  637 => 223,  633 => 130,  621 => 219,  618 => 218,  615 => 121,  604 => 186,  600 => 516,  588 => 116,  585 => 204,  582 => 203,  571 => 104,  567 => 178,  555 => 207,  552 => 190,  549 => 215,  544 => 186,  542 => 185,  535 => 212,  531 => 90,  519 => 189,  516 => 199,  513 => 168,  508 => 434,  506 => 83,  499 => 198,  495 => 167,  491 => 165,  481 => 162,  478 => 68,  475 => 157,  469 => 154,  456 => 154,  451 => 186,  443 => 161,  439 => 147,  427 => 89,  423 => 139,  420 => 176,  409 => 54,  405 => 135,  401 => 132,  391 => 129,  387 => 129,  384 => 132,  378 => 123,  365 => 122,  360 => 120,  348 => 136,  336 => 111,  332 => 140,  329 => 134,  323 => 101,  310 => 92,  305 => 132,  277 => 23,  274 => 90,  263 => 104,  259 => 68,  247 => 110,  244 => 65,  241 => 62,  222 => 63,  210 => 85,  207 => 96,  204 => 56,  184 => 46,  181 => 46,  167 => 39,  157 => 65,  96 => 15,  421 => 144,  417 => 137,  414 => 367,  406 => 143,  398 => 129,  393 => 132,  390 => 135,  376 => 127,  369 => 122,  366 => 136,  352 => 139,  345 => 98,  342 => 109,  331 => 66,  326 => 96,  320 => 137,  317 => 124,  314 => 33,  311 => 104,  308 => 60,  297 => 277,  293 => 128,  281 => 92,  278 => 110,  275 => 68,  264 => 87,  260 => 103,  248 => 70,  245 => 97,  242 => 74,  231 => 36,  227 => 42,  215 => 83,  212 => 69,  209 => 54,  197 => 168,  177 => 44,  171 => 66,  161 => 39,  132 => 122,  121 => 48,  105 => 25,  99 => 34,  81 => 20,  77 => 18,  180 => 71,  176 => 69,  156 => 28,  143 => 130,  139 => 51,  118 => 39,  189 => 164,  185 => 67,  173 => 35,  166 => 67,  152 => 23,  174 => 37,  164 => 62,  154 => 55,  150 => 42,  137 => 34,  133 => 32,  127 => 33,  107 => 30,  102 => 23,  83 => 25,  78 => 19,  53 => 55,  23 => 6,  42 => 8,  138 => 30,  134 => 56,  109 => 27,  103 => 25,  97 => 31,  94 => 27,  84 => 24,  75 => 17,  69 => 19,  66 => 20,  54 => 12,  44 => 26,  230 => 93,  226 => 92,  203 => 51,  193 => 52,  188 => 68,  182 => 160,  178 => 45,  168 => 44,  163 => 79,  160 => 77,  155 => 35,  148 => 37,  145 => 33,  140 => 38,  136 => 36,  125 => 31,  120 => 30,  113 => 31,  101 => 29,  92 => 23,  89 => 21,  85 => 28,  73 => 17,  62 => 14,  59 => 15,  56 => 56,  41 => 18,  126 => 119,  119 => 33,  111 => 37,  106 => 35,  98 => 26,  93 => 23,  86 => 28,  70 => 67,  60 => 14,  28 => 8,  36 => 6,  114 => 29,  104 => 28,  91 => 17,  80 => 18,  63 => 16,  58 => 57,  40 => 8,  34 => 8,  45 => 10,  61 => 18,  55 => 11,  48 => 11,  39 => 17,  35 => 15,  31 => 4,  26 => 7,  21 => 5,  46 => 9,  29 => 13,  57 => 13,  50 => 50,  47 => 44,  38 => 7,  33 => 9,  49 => 16,  32 => 14,  246 => 79,  236 => 76,  232 => 43,  225 => 64,  221 => 89,  216 => 34,  214 => 98,  211 => 61,  208 => 33,  205 => 32,  199 => 56,  196 => 55,  190 => 51,  179 => 28,  175 => 67,  172 => 44,  169 => 43,  162 => 48,  158 => 25,  153 => 137,  151 => 63,  147 => 32,  144 => 42,  141 => 38,  135 => 51,  129 => 38,  124 => 36,  117 => 32,  112 => 28,  90 => 25,  87 => 22,  82 => 20,  72 => 20,  68 => 10,  65 => 18,  52 => 10,  43 => 13,  37 => 16,  30 => 9,  27 => 5,  25 => 6,  24 => 11,  22 => 5,  19 => 8,);
    }
}
