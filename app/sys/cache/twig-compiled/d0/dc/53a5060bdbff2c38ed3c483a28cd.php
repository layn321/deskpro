<?php

/* AdminBundle:Templates:email-edit.html.twig */
class __TwigTemplate_d0dc53a5060bdbff2c38ed3c483a28cd extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("AdminBundle::layout.html.twig");

        $this->blocks = array(
            'pagebar' => array($this, 'block_pagebar'),
            'html_head' => array($this, 'block_html_head'),
            'page' => array($this, 'block_page'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "AdminBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        $context["tplmacro"] = $this->env->loadTemplate("AdminBundle:Templates:macros.html.twig");
        // line 82
        if (isset($context["is_custom"])) { $_is_custom_ = $context["is_custom"]; } else { $_is_custom_ = null; }
        if ($_is_custom_) {
            // line 83
            if (isset($context["template"])) { $_template_ = $context["template"]; } else { $_template_ = null; }
            $context["phrase_key"] = ("admin.emailtpl_desc." . strtr($this->env->getExtension('deskpro_templating')->strLower($this->getAttribute($_template_, "variant_of")), array("." => "_", ":" => "_")));
        } else {
            // line 85
            if (isset($context["name"])) { $_name_ = $context["name"]; } else { $_name_ = null; }
            $context["phrase_key"] = ("admin.emailtpl_desc." . strtr($this->env->getExtension('deskpro_templating')->strLower($_name_), array("." => "_", ":" => "_")));
        }
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_pagebar($context, array $blocks = array())
    {
        // line 4
        if (isset($context["is_custom"])) { $_is_custom_ = $context["is_custom"]; } else { $_is_custom_ = null; }
        if (isset($context["allow_variation"])) { $_allow_variation_ = $context["allow_variation"]; } else { $_allow_variation_ = null; }
        if (($_is_custom_ || $_allow_variation_)) {
            // line 5
            echo "\t<nav>
\t\t<ul>
\t\t\t";
            // line 7
            if (isset($context["is_custom"])) { $_is_custom_ = $context["is_custom"]; } else { $_is_custom_ = null; }
            if ($_is_custom_) {
                // line 8
                echo "\t\t\t\t<li class=\"delete\"><a href=\"";
                if (isset($context["name"])) { $_name_ = $context["name"]; } else { $_name_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_templates_deletecustom", array("name" => $_name_, "_dp_security_token" => $this->env->getExtension('deskpro_templating')->securityToken("delete_template"))), "html", null, true);
                echo "\" onclick=\"return confirm('Are you sure?');\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.delete");
                echo "</a></li>
\t\t\t";
            }
            // line 10
            echo "\t\t\t";
            if (isset($context["allow_variation"])) { $_allow_variation_ = $context["allow_variation"]; } else { $_allow_variation_ = null; }
            if ($_allow_variation_) {
                // line 11
                echo "\t\t\t\t<li class=\"add\" id=\"add_variation_trigger\"><a>Create Variation</a></li>
\t\t\t";
            }
            // line 13
            echo "\t\t</ul>
\t</nav>
";
        }
        // line 16
        echo "<ul>
\t<li>Emails</li>
\t<li>
\t\t";
        // line 19
        if (isset($context["name"])) { $_name_ = $context["name"]; } else { $_name_ = null; }
        if (twig_in_filter(":emails_user:", $_name_)) {
            // line 20
            echo "\t\t\t<a href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_templates_email", array("list_type" => "user")), "html", null, true);
            echo "\">User Emails</a>
\t\t";
        } elseif (twig_in_filter(":emails_agent:", $_name_)) {
            // line 22
            echo "\t\t\t<a href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_templates_email", array("list_type" => "agent")), "html", null, true);
            echo "\">Agent Emails</a>
\t\t";
        } else {
            // line 24
            echo "\t\t\t<a href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_templates_email", array("list_type" => "layout")), "html", null, true);
            echo "\">Email Layout</a>
\t\t";
        }
        // line 26
        echo "\t</li>
\t";
        // line 27
        if (isset($context["template"])) { $_template_ = $context["template"]; } else { $_template_ = null; }
        if ((($this->getAttribute($_template_, "variant_of") == "DeskPRO:emails_user:blank.html.twig") || ($this->getAttribute($_template_, "variant_of") == "DeskPRO:emails_agent:blank.html.twig"))) {
            // line 28
            echo "\t\t<li>
\t\t\tCustom Template
\t\t</li>
\t\t<li>
\t\t\t";
            // line 32
            if (isset($context["template"])) { $_template_ = $context["template"]; } else { $_template_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_template_, "getBaseName", array(), "method"), "html", null, true);
            echo "
\t\t</li>
\t";
        } else {
            // line 35
            echo "\t\t<li>
\t\t\t";
            // line 36
            if (isset($context["phrase_key"])) { $_phrase_key_ = $context["phrase_key"]; } else { $_phrase_key_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, ($_phrase_key_ . "_title"));
            echo "
\t\t</li>
\t\t";
            // line 38
            if (isset($context["is_custom"])) { $_is_custom_ = $context["is_custom"]; } else { $_is_custom_ = null; }
            if ($_is_custom_) {
                // line 39
                echo "\t\t\t<li>
\t\t\t\t";
                // line 40
                if (isset($context["template"])) { $_template_ = $context["template"]; } else { $_template_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_template_, "getBaseName", array(), "method"), "html", null, true);
                echo "
\t\t\t</li>
\t\t";
            }
            // line 43
            echo "\t";
        }
        // line 44
        echo "</ul>
";
    }

    // line 46
    public function block_html_head($context, array $blocks = array())
    {
        // line 47
        echo "<script type=\"text/javascript\" src=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("javascripts/DeskPRO/Admin/ElementHandler/EditPhrasesPage.js"), "html", null, true);
        echo "\"></script>

<script type=\"text/javascript\" src=\"";
        // line 49
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("vendor/codemirror/lib/codemirror.js"), "html", null, true);
        echo "\"></script>
<link rel=\"stylesheet\" href=\"";
        // line 50
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("vendor/codemirror/lib/codemirror.css"), "html", null, true);
        echo "\" type=\"text/css\" />

<script type=\"text/javascript\" src=\"";
        // line 52
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("vendor/codemirror/mode/css/css.js"), "html", null, true);
        echo "\"></script>
<script type=\"text/javascript\" src=\"";
        // line 53
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("vendor/codemirror/mode/javascript/javascript.js"), "html", null, true);
        echo "\"></script>
<script type=\"text/javascript\" src=\"";
        // line 54
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("vendor/codemirror/mode/xml/xml.js"), "html", null, true);
        echo "\"></script>
<script type=\"text/javascript\" src=\"";
        // line 55
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("vendor/codemirror/mode/htmlembedded/htmlembedded.js"), "html", null, true);
        echo "\"></script>
<script type=\"text/javascript\" src=\"";
        // line 56
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("vendor/codemirror/mode/htmlmixed/htmlmixed.js"), "html", null, true);
        echo "\"></script>

<script type=\"text/javascript\" src=\"";
        // line 58
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("javascripts/DeskPRO/Admin/Templates/DpCodeHints.js"), "html", null, true);
        echo "\"></script>
<script type=\"text/javascript\" src=\"";
        // line 59
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("javascripts/DeskPRO/Admin/Templates/DpEmailEditor.js"), "html", null, true);
        echo "\"></script>

<style type=\"text/css\">
.lang_overlay_nav {
\tmargin-top: 3px;
}

.lang_overlay_nav .select2-container {
\tmargin-right: 6px;
}

.subject .CodeMirror-scroll {
\theight: ";
        // line 71
        if (isset($context["source"])) { $_source_ = $context["source"]; } else { $_source_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->max(50, ($this->env->getExtension('deskpro_templating')->countLines($this->getAttribute($_source_, "subject")) * 24)), "html", null, true);
        echo "px;
}
</style>

<script type=\"text/javascript\">
var DP_DEFAULT_LANG_ID = ";
        // line 76
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_app_, "languages", array(), "any", false, true), "getDefaultId", array(), "method", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_app_, "languages", array(), "any", false, true), "getDefaultId", array(), "method"), 1)) : (1)), "html", null, true);
        echo ";
\$(document).ready(function() {
\tvar editor = new DpEmailEditor('";
        // line 78
        if (isset($context["name"])) { $_name_ = $context["name"]; } else { $_name_ = null; }
        echo twig_escape_filter($this->env, $_name_, "html", null, true);
        echo "');
});
</script>
";
    }

    // line 87
    public function block_page($context, array $blocks = array())
    {
        // line 88
        echo "
\t";
        // line 89
        if (isset($context["template"])) { $_template_ = $context["template"]; } else { $_template_ = null; }
        if ((($this->getAttribute($_template_, "variant_of") == "DeskPRO:emails_user:blank.html.twig") || ($this->getAttribute($_template_, "variant_of") == "DeskPRO:emails_agent:blank.html.twig"))) {
            // line 90
            echo "\t\t<h3>Custom Template: ";
            if (isset($context["template"])) { $_template_ = $context["template"]; } else { $_template_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_template_, "getBaseName", array(), "method"), "html", null, true);
            echo "</h3>
\t\t<br/>
\t";
        } else {
            // line 93
            echo "\t\t";
            if (isset($context["is_custom"])) { $_is_custom_ = $context["is_custom"]; } else { $_is_custom_ = null; }
            if ($_is_custom_) {
                // line 94
                echo "\t\t\t<h3>Custom Variation of ";
                if (isset($context["phrase_key"])) { $_phrase_key_ = $context["phrase_key"]; } else { $_phrase_key_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, ($_phrase_key_ . "_title"));
                echo ": ";
                if (isset($context["template"])) { $_template_ = $context["template"]; } else { $_template_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_template_, "getBaseName", array(), "method"), "html", null, true);
                echo "</h3>
\t\t";
            } else {
                // line 96
                echo "\t\t\t<h3>";
                if (isset($context["phrase_key"])) { $_phrase_key_ = $context["phrase_key"]; } else { $_phrase_key_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, ($_phrase_key_ . "_title"));
                echo "</h3>
\t\t";
            }
            // line 98
            echo "\t\t<p>
\t\t\t";
            // line 99
            if (isset($context["phrase_key"])) { $_phrase_key_ = $context["phrase_key"]; } else { $_phrase_key_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, ($_phrase_key_ . "_desc"));
            echo "
\t\t</p>
\t";
        }
        // line 102
        echo "
\t<div class=\"template-edit-row\">
\t\t<div class=\"template-editor-label\"><strong>Subject:</strong></div>
\t\t<div class=\"template-editor-wrap subject\">
\t\t\t<div class=\"template-toolbar\">
\t\t\t\t<ul>
\t\t\t\t\t<li class=\"new-phrase\"><span>Add Phrase</span></li>
\t\t\t\t\t<li class=\"phrase-editor\"><span>Phrase Editor</span></li>
\t\t\t\t\t";
        // line 110
        if (isset($context["default_template"])) { $_default_template_ = $context["default_template"]; } else { $_default_template_ = null; }
        if ($_default_template_) {
            // line 111
            echo "\t\t\t\t\t\t<li class=\"view-default\" data-part=\"subject\"><span>View Default</span></li>
\t\t\t\t\t\t<li class=\"reset-default\" data-part=\"subject\"><span>Reset To Default</span></li>
\t\t\t\t\t";
        }
        // line 114
        echo "\t\t\t\t</ul>
\t\t\t</div>
\t\t\t<textarea name=\"subject\" class=\"template-editor subject\">";
        // line 116
        if (isset($context["source"])) { $_source_ = $context["source"]; } else { $_source_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_source_, "subject"), "html", null, true);
        echo "</textarea>
\t\t\t";
        // line 117
        if (isset($context["default_template"])) { $_default_template_ = $context["default_template"]; } else { $_default_template_ = null; }
        if ($_default_template_) {
            echo "<textarea class=\"subject-default-code\" style=\"display: none;\">";
            if (isset($context["default_source"])) { $_default_source_ = $context["default_source"]; } else { $_default_source_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_default_source_, "subject"), "html", null, true);
            echo "</textarea>";
        }
        // line 118
        echo "\t\t</div>
\t</div>

\t<div class=\"template-edit-row\">
\t\t<div class=\"template-editor-label\"><strong>Body:</strong></div>
\t\t<div class=\"template-editor-wrap\">
\t\t\t<div class=\"template-toolbar\">
\t\t\t\t<ul>
\t\t\t\t\t<li class=\"new-phrase\"><span>Add Phrase</span></li>
\t\t\t\t\t<li class=\"phrase-editor\"><span>Phrase Editor</span></li>
\t\t\t\t\t";
        // line 128
        if (isset($context["default_template"])) { $_default_template_ = $context["default_template"]; } else { $_default_template_ = null; }
        if ($_default_template_) {
            // line 129
            echo "\t\t\t\t\t\t<li class=\"view-default\" data-part=\"body\"><span>View Default</span></li>
\t\t\t\t\t\t<li class=\"reset-default\" data-part=\"body\"><span>Reset To Default</span></li>
\t\t\t\t\t";
        }
        // line 132
        echo "\t\t\t\t</ul>
\t\t\t</div>
\t\t\t<textarea class=\"template-editor template\" data-template-name=\"";
        // line 134
        if (isset($context["name"])) { $_name_ = $context["name"]; } else { $_name_ = null; }
        echo twig_escape_filter($this->env, $_name_, "html", null, true);
        echo "\">";
        if (isset($context["source"])) { $_source_ = $context["source"]; } else { $_source_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_source_, "body"), "html", null, true);
        echo "</textarea>
\t\t\t";
        // line 135
        if (isset($context["default_template"])) { $_default_template_ = $context["default_template"]; } else { $_default_template_ = null; }
        if ($_default_template_) {
            echo "<textarea class=\"body-default-code\" style=\"display: none;\">";
            if (isset($context["default_source"])) { $_default_source_ = $context["default_source"]; } else { $_default_source_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_default_source_, "body"), "html", null, true);
            echo "</textarea>";
        }
        // line 136
        echo "\t\t</div>
\t</div>

\t<footer class=\"controls\" id=\"save_control\">
\t\t<div class=\"is-not-loading\">
\t\t\t<button class=\"clean-white save-trigger\">Save Template</button>
\t\t</div>
\t\t<div class=\"is-loading\">
\t\t\t<div class=\"loading-icon-flat\">&nbsp;</div>
\t\t</div>
\t</footer>

\t<script type=\"text/x-deskpro-plain\" id=\"edittpl_overlay_source\">
\t\t";
        // line 149
        if (isset($context["tplmacro"])) { $_tplmacro_ = $context["tplmacro"]; } else { $_tplmacro_ = null; }
        echo $_tplmacro_->getrender_editor_overlay();
        echo "
\t</script>
\t";
        // line 151
        if (isset($context["tplmacro"])) { $_tplmacro_ = $context["tplmacro"]; } else { $_tplmacro_ = null; }
        echo $_tplmacro_->getrender_addphrase_overlay();
        echo "
\t";
        // line 152
        if (isset($context["tplmacro"])) { $_tplmacro_ = $context["tplmacro"]; } else { $_tplmacro_ = null; }
        echo $_tplmacro_->getrender_addvariation_overlay();
        echo "
\t";
        // line 153
        if (isset($context["tplmacro"])) { $_tplmacro_ = $context["tplmacro"]; } else { $_tplmacro_ = null; }
        echo $_tplmacro_->getrender_lang_overlay();
        echo "
";
    }

    public function getTemplateName()
    {
        return "AdminBundle:Templates:email-edit.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  933 => 149,  914 => 133,  909 => 132,  833 => 359,  783 => 332,  755 => 320,  666 => 300,  453 => 203,  639 => 110,  568 => 254,  520 => 249,  657 => 287,  572 => 251,  609 => 459,  20 => 1,  659 => 274,  562 => 245,  548 => 238,  558 => 244,  479 => 82,  589 => 100,  457 => 211,  413 => 172,  953 => 406,  948 => 403,  935 => 394,  929 => 391,  916 => 382,  864 => 365,  844 => 348,  816 => 342,  807 => 339,  801 => 338,  774 => 337,  766 => 328,  737 => 318,  685 => 293,  664 => 294,  635 => 281,  593 => 445,  546 => 414,  532 => 240,  865 => 221,  852 => 216,  838 => 208,  820 => 201,  781 => 333,  764 => 321,  725 => 164,  632 => 283,  602 => 265,  565 => 253,  529 => 282,  505 => 267,  487 => 213,  473 => 221,  1853 => 842,  1849 => 841,  1839 => 837,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 813,  1778 => 812,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 782,  1705 => 781,  1688 => 770,  1681 => 769,  1677 => 768,  1667 => 764,  1656 => 762,  1648 => 757,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 714,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 695,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 651,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 610,  1289 => 607,  1276 => 604,  1263 => 601,  1244 => 596,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 565,  1181 => 561,  1171 => 557,  1158 => 550,  1114 => 530,  1104 => 526,  1072 => 512,  1062 => 508,  1024 => 494,  1014 => 490,  1000 => 482,  990 => 478,  980 => 474,  960 => 466,  918 => 448,  888 => 376,  834 => 346,  673 => 342,  636 => 284,  462 => 222,  454 => 103,  1144 => 542,  1139 => 399,  1131 => 399,  1127 => 397,  1110 => 396,  1092 => 391,  1089 => 390,  1086 => 520,  1084 => 388,  1063 => 387,  1060 => 386,  1055 => 385,  1050 => 384,  1035 => 372,  1019 => 267,  1003 => 263,  959 => 251,  900 => 377,  880 => 434,  870 => 430,  867 => 366,  859 => 362,  848 => 215,  839 => 362,  828 => 357,  823 => 188,  809 => 181,  800 => 178,  797 => 177,  794 => 336,  786 => 174,  740 => 162,  734 => 313,  703 => 354,  693 => 350,  630 => 278,  626 => 140,  614 => 275,  610 => 103,  581 => 247,  564 => 229,  525 => 235,  722 => 162,  697 => 256,  674 => 249,  671 => 425,  577 => 257,  569 => 97,  557 => 368,  502 => 229,  497 => 240,  445 => 197,  729 => 159,  684 => 261,  676 => 299,  669 => 254,  660 => 145,  647 => 286,  643 => 244,  601 => 287,  570 => 273,  522 => 200,  501 => 265,  296 => 67,  374 => 149,  631 => 111,  616 => 281,  608 => 266,  605 => 16,  596 => 102,  574 => 165,  561 => 209,  527 => 233,  433 => 93,  388 => 137,  426 => 177,  383 => 182,  461 => 246,  370 => 147,  395 => 221,  294 => 110,  223 => 78,  220 => 82,  492 => 395,  468 => 201,  444 => 193,  410 => 169,  397 => 174,  377 => 159,  262 => 113,  250 => 98,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 2108,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 1383,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 1303,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 1173,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 1041,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 970,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 538,  1126 => 916,  1106 => 900,  1100 => 897,  1026 => 841,  997 => 822,  983 => 816,  975 => 402,  956 => 800,  939 => 786,  902 => 130,  894 => 128,  879 => 373,  757 => 631,  727 => 316,  716 => 308,  670 => 297,  528 => 221,  476 => 253,  435 => 208,  354 => 153,  341 => 212,  192 => 83,  321 => 122,  243 => 54,  793 => 350,  780 => 348,  758 => 177,  700 => 312,  686 => 150,  652 => 274,  638 => 282,  620 => 139,  545 => 259,  523 => 110,  494 => 10,  459 => 226,  438 => 195,  351 => 135,  347 => 173,  402 => 222,  268 => 65,  430 => 201,  411 => 201,  379 => 219,  322 => 83,  315 => 118,  289 => 108,  284 => 102,  255 => 115,  234 => 81,  1133 => 400,  1124 => 534,  1121 => 56,  1116 => 55,  1113 => 403,  1108 => 51,  1103 => 394,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 500,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 329,  996 => 262,  989 => 316,  985 => 315,  981 => 257,  977 => 313,  970 => 91,  966 => 416,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 148,  919 => 290,  917 => 289,  908 => 444,  905 => 378,  896 => 280,  891 => 127,  877 => 270,  862 => 267,  857 => 265,  837 => 347,  832 => 250,  827 => 248,  821 => 247,  803 => 179,  778 => 389,  769 => 165,  765 => 386,  753 => 328,  746 => 319,  743 => 318,  735 => 170,  730 => 218,  720 => 363,  717 => 362,  712 => 156,  691 => 204,  678 => 149,  654 => 144,  587 => 14,  576 => 179,  539 => 241,  517 => 217,  471 => 212,  441 => 239,  437 => 101,  418 => 201,  386 => 164,  373 => 149,  304 => 114,  270 => 144,  265 => 161,  229 => 91,  477 => 138,  455 => 224,  448 => 242,  429 => 179,  407 => 119,  399 => 162,  389 => 176,  375 => 217,  358 => 142,  349 => 131,  335 => 139,  327 => 124,  298 => 144,  280 => 102,  249 => 205,  194 => 69,  142 => 49,  344 => 140,  318 => 122,  306 => 116,  295 => 74,  357 => 154,  300 => 112,  286 => 80,  276 => 105,  269 => 103,  254 => 100,  128 => 61,  237 => 118,  165 => 72,  122 => 33,  798 => 337,  770 => 179,  759 => 112,  748 => 337,  731 => 108,  721 => 305,  718 => 307,  708 => 295,  696 => 147,  617 => 461,  590 => 226,  553 => 264,  550 => 156,  540 => 84,  533 => 255,  500 => 397,  493 => 225,  489 => 257,  482 => 223,  467 => 210,  464 => 202,  458 => 220,  452 => 217,  449 => 132,  415 => 170,  382 => 162,  372 => 157,  361 => 155,  356 => 215,  339 => 132,  302 => 114,  285 => 105,  258 => 136,  123 => 44,  108 => 27,  424 => 149,  394 => 161,  380 => 151,  338 => 155,  319 => 118,  316 => 131,  312 => 87,  290 => 105,  267 => 96,  206 => 75,  110 => 48,  240 => 93,  224 => 119,  219 => 49,  217 => 81,  202 => 126,  186 => 70,  170 => 73,  100 => 42,  67 => 11,  14 => 1,  1096 => 524,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 836,  1013 => 275,  1007 => 274,  1002 => 823,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 402,  940 => 298,  937 => 395,  928 => 452,  926 => 243,  915 => 239,  912 => 236,  903 => 231,  898 => 440,  892 => 229,  889 => 748,  887 => 227,  884 => 374,  876 => 222,  874 => 215,  871 => 368,  863 => 214,  861 => 213,  858 => 212,  850 => 352,  843 => 206,  840 => 406,  815 => 245,  812 => 244,  808 => 199,  804 => 395,  799 => 196,  791 => 193,  785 => 238,  775 => 184,  771 => 183,  754 => 340,  728 => 309,  726 => 169,  723 => 168,  715 => 105,  711 => 152,  709 => 162,  706 => 302,  698 => 208,  694 => 308,  692 => 155,  689 => 302,  681 => 300,  677 => 149,  675 => 289,  663 => 276,  661 => 277,  650 => 246,  646 => 112,  629 => 305,  627 => 269,  625 => 279,  622 => 270,  598 => 174,  592 => 117,  586 => 264,  575 => 257,  566 => 246,  556 => 244,  554 => 240,  541 => 243,  536 => 241,  515 => 209,  511 => 269,  509 => 244,  488 => 205,  486 => 220,  483 => 341,  465 => 223,  463 => 216,  450 => 194,  432 => 211,  419 => 100,  371 => 182,  362 => 80,  353 => 141,  337 => 140,  333 => 131,  309 => 190,  303 => 70,  299 => 148,  291 => 176,  272 => 93,  261 => 91,  253 => 93,  239 => 88,  235 => 88,  213 => 112,  200 => 45,  198 => 85,  159 => 53,  149 => 44,  146 => 43,  131 => 36,  116 => 50,  79 => 16,  74 => 13,  71 => 17,  836 => 320,  817 => 398,  814 => 200,  811 => 317,  805 => 313,  787 => 333,  779 => 169,  776 => 305,  773 => 347,  761 => 296,  751 => 163,  747 => 325,  742 => 336,  739 => 171,  736 => 317,  724 => 214,  705 => 278,  702 => 601,  688 => 113,  680 => 278,  667 => 296,  662 => 293,  656 => 418,  649 => 285,  644 => 284,  641 => 241,  624 => 109,  613 => 261,  607 => 273,  597 => 270,  591 => 267,  584 => 259,  579 => 234,  563 => 96,  559 => 245,  551 => 243,  547 => 242,  537 => 90,  524 => 220,  512 => 227,  507 => 76,  504 => 213,  498 => 213,  485 => 256,  480 => 254,  472 => 217,  466 => 210,  460 => 221,  447 => 215,  442 => 196,  434 => 212,  428 => 185,  422 => 176,  404 => 149,  368 => 81,  364 => 156,  340 => 170,  334 => 129,  330 => 148,  325 => 134,  292 => 112,  287 => 117,  282 => 104,  279 => 111,  273 => 170,  266 => 102,  256 => 135,  252 => 109,  228 => 79,  218 => 77,  201 => 107,  64 => 13,  51 => 9,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 1705,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 737,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 731,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 725,  1559 => 601,  1552 => 720,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 708,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 645,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 624,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 1001,  1257 => 1000,  1254 => 479,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 569,  1196 => 958,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 938,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 388,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 898,  1099 => 393,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 880,  1076 => 359,  1070 => 875,  1057 => 352,  1052 => 504,  1045 => 382,  1040 => 377,  1036 => 283,  1032 => 496,  1028 => 274,  1023 => 393,  1020 => 392,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 821,  979 => 379,  974 => 256,  967 => 373,  962 => 803,  958 => 801,  954 => 253,  950 => 153,  945 => 152,  942 => 460,  938 => 150,  934 => 364,  927 => 147,  923 => 387,  920 => 359,  910 => 353,  901 => 347,  897 => 129,  890 => 343,  886 => 50,  883 => 272,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 410,  841 => 210,  835 => 312,  830 => 249,  826 => 202,  822 => 354,  818 => 246,  813 => 183,  810 => 345,  806 => 180,  802 => 198,  795 => 336,  792 => 239,  789 => 349,  784 => 188,  782 => 187,  777 => 291,  772 => 289,  768 => 334,  763 => 327,  760 => 329,  756 => 230,  752 => 176,  745 => 281,  741 => 280,  738 => 379,  732 => 171,  719 => 279,  714 => 264,  710 => 310,  704 => 267,  699 => 303,  695 => 297,  690 => 263,  687 => 279,  683 => 346,  679 => 298,  672 => 255,  668 => 247,  665 => 285,  658 => 244,  645 => 277,  640 => 285,  634 => 413,  628 => 466,  623 => 107,  619 => 298,  611 => 268,  606 => 279,  603 => 267,  599 => 249,  595 => 132,  583 => 263,  580 => 99,  573 => 274,  560 => 268,  543 => 172,  538 => 257,  534 => 233,  530 => 202,  526 => 229,  521 => 226,  518 => 233,  514 => 221,  510 => 202,  503 => 266,  496 => 226,  490 => 214,  484 => 394,  474 => 202,  470 => 231,  446 => 241,  440 => 102,  436 => 183,  431 => 186,  425 => 193,  416 => 231,  412 => 230,  408 => 141,  403 => 194,  400 => 225,  396 => 93,  392 => 139,  385 => 152,  381 => 185,  367 => 147,  363 => 164,  359 => 136,  355 => 326,  350 => 94,  346 => 140,  343 => 134,  328 => 135,  324 => 125,  313 => 81,  307 => 108,  301 => 124,  288 => 116,  283 => 62,  271 => 105,  257 => 94,  251 => 58,  238 => 53,  233 => 81,  195 => 84,  191 => 49,  187 => 100,  183 => 69,  130 => 47,  88 => 18,  76 => 30,  115 => 28,  95 => 35,  655 => 281,  651 => 232,  648 => 231,  637 => 273,  633 => 272,  621 => 462,  618 => 277,  615 => 268,  604 => 186,  600 => 271,  588 => 305,  585 => 261,  582 => 260,  571 => 242,  567 => 95,  555 => 250,  552 => 190,  549 => 208,  544 => 230,  542 => 290,  535 => 256,  531 => 254,  519 => 87,  516 => 248,  513 => 216,  508 => 230,  506 => 217,  499 => 241,  495 => 239,  491 => 145,  481 => 218,  478 => 235,  475 => 184,  469 => 203,  456 => 204,  451 => 243,  443 => 184,  439 => 129,  427 => 177,  423 => 187,  420 => 208,  409 => 179,  405 => 94,  401 => 148,  391 => 173,  387 => 92,  384 => 160,  378 => 91,  365 => 145,  360 => 89,  348 => 191,  336 => 132,  332 => 150,  329 => 127,  323 => 135,  310 => 114,  305 => 112,  277 => 99,  274 => 98,  263 => 64,  259 => 97,  247 => 96,  244 => 86,  241 => 91,  222 => 105,  210 => 60,  207 => 73,  204 => 89,  184 => 55,  181 => 41,  167 => 50,  157 => 47,  96 => 31,  421 => 147,  417 => 71,  414 => 142,  406 => 170,  398 => 165,  393 => 177,  390 => 153,  376 => 159,  369 => 90,  366 => 174,  352 => 140,  345 => 213,  342 => 87,  331 => 128,  326 => 102,  320 => 121,  317 => 82,  314 => 136,  311 => 117,  308 => 116,  297 => 111,  293 => 119,  281 => 106,  278 => 71,  275 => 107,  264 => 103,  260 => 107,  248 => 75,  245 => 90,  242 => 89,  231 => 52,  227 => 78,  215 => 88,  212 => 71,  209 => 89,  197 => 59,  177 => 93,  171 => 57,  161 => 42,  132 => 58,  121 => 41,  105 => 26,  99 => 24,  81 => 31,  77 => 26,  180 => 54,  176 => 53,  156 => 77,  143 => 63,  139 => 40,  118 => 46,  189 => 88,  185 => 46,  173 => 54,  166 => 51,  152 => 54,  174 => 55,  164 => 113,  154 => 46,  150 => 52,  137 => 49,  133 => 38,  127 => 36,  107 => 50,  102 => 41,  83 => 23,  78 => 30,  53 => 12,  23 => 6,  42 => 8,  138 => 54,  134 => 44,  109 => 40,  103 => 42,  97 => 20,  94 => 38,  84 => 19,  75 => 29,  69 => 21,  66 => 10,  54 => 7,  44 => 8,  230 => 74,  226 => 86,  203 => 72,  193 => 58,  188 => 56,  182 => 59,  178 => 66,  168 => 62,  163 => 49,  160 => 68,  155 => 55,  148 => 66,  145 => 47,  140 => 62,  136 => 39,  125 => 55,  120 => 46,  113 => 49,  101 => 21,  92 => 26,  89 => 33,  85 => 23,  73 => 16,  62 => 15,  59 => 12,  56 => 11,  41 => 5,  126 => 40,  119 => 42,  111 => 28,  106 => 37,  98 => 36,  93 => 22,  86 => 27,  70 => 11,  60 => 18,  28 => 2,  36 => 85,  114 => 28,  104 => 37,  91 => 37,  80 => 16,  63 => 19,  58 => 11,  40 => 7,  34 => 4,  45 => 9,  61 => 12,  55 => 13,  48 => 10,  39 => 7,  35 => 3,  31 => 4,  26 => 1,  21 => 2,  46 => 4,  29 => 82,  57 => 8,  50 => 5,  47 => 9,  38 => 4,  33 => 13,  49 => 19,  32 => 83,  246 => 131,  236 => 87,  232 => 123,  225 => 82,  221 => 76,  216 => 65,  214 => 102,  211 => 111,  208 => 100,  205 => 87,  199 => 74,  196 => 73,  190 => 101,  179 => 94,  175 => 65,  172 => 52,  169 => 75,  162 => 80,  158 => 58,  153 => 46,  151 => 62,  147 => 50,  144 => 51,  141 => 55,  135 => 59,  129 => 35,  124 => 35,  117 => 32,  112 => 40,  90 => 22,  87 => 20,  82 => 20,  72 => 30,  68 => 22,  65 => 20,  52 => 10,  43 => 3,  37 => 5,  30 => 1,  27 => 1,  25 => 2,  24 => 3,  22 => 2,  19 => 1,);
    }
}
