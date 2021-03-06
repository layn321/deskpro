<?php

/* UserBundle:ChatLog:view.html.twig */
class __TwigTemplate_921b6b121af93b2e075b4ea24e870a19 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("UserBundle::layout.html.twig");

        $this->blocks = array(
            'breadcrumb' => array($this, 'block_breadcrumb'),
            'page_title' => array($this, 'block_page_title'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "UserBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_breadcrumb($context, array $blocks = array())
    {
        // line 6
        echo "\t<li><span class=\"dp-divider\">";
        echo $this->env->getExtension('deskpro_templating')->getLanguageArrow("right");
        echo "</span> <a href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_chatlogs"), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.log-title");
        echo "</a></li>
\t<li><span class=\"dp-divider\">";
        // line 7
        echo $this->env->getExtension('deskpro_templating')->getLanguageArrow("right");
        echo "</span> <a href=\"";
        if (isset($context["convo"])) { $_convo_ = $context["convo"]; } else { $_convo_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_chatlogs_view", array("conversation_id" => $this->getAttribute($_convo_, "id"))), "html", null, true);
        echo "\">";
        if (isset($context["convo"])) { $_convo_ = $context["convo"]; } else { $_convo_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_convo_, "getSubjectLine", array(), "method"), "html", null, true);
        echo "</a></li>
";
    }

    // line 9
    public function block_page_title($context, array $blocks = array())
    {
        // line 10
        echo "\t";
        if (isset($context["convo"])) { $_convo_ = $context["convo"]; } else { $_convo_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_convo_, "getSubjectLine", array(), "method"), "html", null, true);
        echo "
";
    }

    // line 12
    public function block_content($context, array $blocks = array())
    {
        // line 13
        echo "
<section class=\"dp-ticket dp-content-page dp-content-post\">
\t<header>
\t\t<h3 style=\"margin-bottom: 0\">";
        // line 16
        if (isset($context["convo"])) { $_convo_ = $context["convo"]; } else { $_convo_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_convo_, "getSubjectLine", array(), "method"), "html", null, true);
        echo "</h3>
\t\t<ul class=\"dp-post-info\">
\t\t\t<li class=\"dp-id\"><i class=\"dp-icon-bookmark\"></i> ";
        // line 18
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.log_chat-id");
        echo ": ";
        if (isset($context["convo"])) { $_convo_ = $context["convo"]; } else { $_convo_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_convo_, "id"), "html", null, true);
        echo "</li>
\t\t\t<li class=\"dp-date\"><i class=\"dp-icon-calendar\"></i> ";
        // line 19
        if (isset($context["convo"])) { $_convo_ = $context["convo"]; } else { $_convo_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_convo_, "date_created"), "fulltime"), "html", null, true);
        echo "</li>
\t\t</ul>
\t</header>
\t<article class=\"dp-ticket-info\">
\t\t<table class=\"dp-table dp-table-striped dp-table-bordered\">
\t\t\t<tbody>
\t\t\t\t<tr>
\t\t\t\t\t<th>";
        // line 26
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.log_fields_department");
        echo "</th>
\t\t\t\t\t<td>
\t\t\t\t\t\t";
        // line 28
        if (isset($context["convo"])) { $_convo_ = $context["convo"]; } else { $_convo_ = null; }
        if ($this->getAttribute($_convo_, "department")) {
            // line 29
            echo "\t\t\t\t\t\t\t";
            if (isset($context["dp"])) { $_dp_ = $context["dp"]; } else { $_dp_ = null; }
            if (isset($context["convo"])) { $_convo_ = $context["convo"]; } else { $_convo_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_dp_, "full_title", array(0 => $this->getAttribute($_convo_, "department")), "method"), "html", null, true);
            echo "
\t\t\t\t\t\t";
        } else {
            // line 31
            echo "\t\t\t\t\t\t\t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.log_no_department");
            echo "
\t\t\t\t\t\t";
        }
        // line 33
        echo "\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t\t<tr>
\t\t\t\t\t<th>";
        // line 36
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.log_fields_agent");
        echo "</th>
\t\t\t\t\t<td>
\t\t\t\t\t\t";
        // line 38
        if (isset($context["convo"])) { $_convo_ = $context["convo"]; } else { $_convo_ = null; }
        if ($this->getAttribute($_convo_, "agent")) {
            // line 39
            echo "\t\t\t\t\t\t\t";
            if (isset($context["convo"])) { $_convo_ = $context["convo"]; } else { $_convo_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_convo_, "agent"), "getDisplayNameUser", array(), "method"), "html", null, true);
            echo "
\t\t\t\t\t\t";
        } else {
            // line 41
            echo "\t\t\t\t\t\t\t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.log_unassigned");
            echo "
\t\t\t\t\t\t";
        }
        // line 43
        echo "\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t\t";
        // line 45
        if (isset($context["custom_fields"])) { $_custom_fields_ = $context["custom_fields"]; } else { $_custom_fields_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_custom_fields_);
        foreach ($context['_seq'] as $context["_key"] => $context["f"]) {
            // line 46
            echo "\t\t\t\t\t";
            if (isset($context["convo"])) { $_convo_ = $context["convo"]; } else { $_convo_ = null; }
            if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
            if ($this->getAttribute($_convo_, "hasCustomField", array(0 => $this->getAttribute($this->getAttribute($_f_, "field_def"), "id")), "method")) {
                // line 47
                echo "\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t<th>";
                // line 48
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_f_, "title"), "html", null, true);
                echo "</th>
\t\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t\t";
                // line 50
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                echo $this->env->getExtension('deskpro_templating')->renderCustomField($_f_);
                echo "
\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t</tr>
\t\t\t\t\t";
            }
            // line 54
            echo "\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['f'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 55
        echo "\t\t\t</tbody>
\t\t</table>
\t</article>
\t<article class=\"dp-messages-wrap\">
\t\t<section class=\"dp-chat-log-box\">
\t\t\t<table width=\"100%\">
\t\t\t\t<tbody>
\t\t\t\t\t";
        // line 62
        if (isset($context["convo_messages"])) { $_convo_messages_ = $context["convo_messages"]; } else { $_convo_messages_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_convo_messages_);
        foreach ($context['_seq'] as $context["_key"] => $context["message"]) {
            // line 63
            echo "\t\t\t\t\t\t";
            if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
            if ($this->getAttribute($_message_, "is_sys")) {
                // line 64
                echo "\t\t\t\t\t\t\t<tr class=\"message-row sys-message\">
\t\t\t\t\t\t\t\t<th class=\"author\" width=\"10\" nowrap=\"nowrap\">*</th>
\t\t\t\t\t\t\t\t<td class=\"message\">
\t\t\t\t\t\t\t\t\t";
                // line 67
                if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
                if ($this->getAttribute($this->getAttribute($_message_, "metadata"), "phrase_id")) {
                    // line 68
                    echo "\t\t\t\t\t\t\t\t\t\t";
                    if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
                    if ($this->getAttribute($_message_, "is_html")) {
                        // line 69
                        echo "\t\t\t\t\t\t\t\t\t\t\t";
                        if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, ("agent.userchat." . $this->getAttribute($this->getAttribute($_message_, "metadata"), "phrase_id")), $this->getAttribute($_message_, "metadata"), true);
                        echo "
\t\t\t\t\t\t\t\t\t\t";
                    } else {
                        // line 71
                        echo "\t\t\t\t\t\t\t\t\t\t\t";
                        if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, ("agent.userchat." . $this->getAttribute($this->getAttribute($_message_, "metadata"), "phrase_id")), $this->getAttribute($_message_, "metadata"));
                        echo "
\t\t\t\t\t\t\t\t\t\t";
                    }
                    // line 73
                    echo "\t\t\t\t\t\t\t\t\t";
                } elseif ($this->getAttribute($_message_, "is_html")) {
                    // line 74
                    echo "\t\t\t\t\t\t\t\t\t\t";
                    if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
                    echo $this->getAttribute($_message_, "content");
                    echo "
\t\t\t\t\t\t\t\t\t";
                } else {
                    // line 76
                    echo "\t\t\t\t\t\t\t\t\t\t";
                    if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_message_, "content"), "html", null, true);
                    echo "
\t\t\t\t\t\t\t\t\t";
                }
                // line 78
                echo "\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"time\" width=\"10\" nowrap=\"nowrap\">";
                // line 79
                if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_message_, "date_created"), "time"), "html", null, true);
                echo "</td>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t";
            } elseif (($this->getAttribute($_message_, "origin") == "agent")) {
                // line 82
                echo "\t\t\t\t\t\t\t<tr class=\"message-row agent-message\">
\t\t\t\t\t\t\t\t<th class=\"author\" width=\"10\" nowrap=\"nowrap\">
\t\t\t\t\t\t\t\t\t";
                // line 84
                if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_message_, "getAuthorName", array(), "method"), "html", null, true);
                echo "
\t\t\t\t\t\t\t\t</th>
\t\t\t\t\t\t\t\t<td class=\"message\">
\t\t\t\t\t\t\t\t\t";
                // line 87
                if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
                if ($this->getAttribute($_message_, "is_html")) {
                    // line 88
                    echo "\t\t\t\t\t\t\t\t\t\t";
                    if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
                    echo $this->getAttribute($_message_, "content");
                    echo "
\t\t\t\t\t\t\t\t\t";
                } else {
                    // line 90
                    echo "\t\t\t\t\t\t\t\t\t\t";
                    if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_message_, "content"), "html", null, true);
                    echo "
\t\t\t\t\t\t\t\t\t";
                }
                // line 92
                echo "\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"time\" width=\"10\" nowrap=\"nowrap\">";
                // line 93
                if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_message_, "date_created"), "time"), "html", null, true);
                echo "</td>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t";
            } else {
                // line 96
                echo "\t\t\t\t\t\t\t<tr class=\"message-row user-message\">
\t\t\t\t\t\t\t\t<th class=\"author\" width=\"10\" nowrap=\"nowrap\">
\t\t\t\t\t\t\t\t\t";
                // line 98
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.chat.log_message_author-you");
                echo "
\t\t\t\t\t\t\t\t</th>
\t\t\t\t\t\t\t\t<td class=\"message\">
\t\t\t\t\t\t\t\t\t";
                // line 101
                if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
                if ($this->getAttribute($_message_, "is_html")) {
                    // line 102
                    echo "\t\t\t\t\t\t\t\t\t\t";
                    if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
                    echo $this->getAttribute($_message_, "content");
                    echo "
\t\t\t\t\t\t\t\t\t";
                } else {
                    // line 104
                    echo "\t\t\t\t\t\t\t\t\t\t";
                    if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_message_, "content"), "html", null, true);
                    echo "
\t\t\t\t\t\t\t\t\t";
                }
                // line 106
                echo "\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"time\" width=\"10\" nowrap=\"nowrap\">";
                // line 107
                if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_message_, "date_created"), "time"), "html", null, true);
                echo "</td>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t";
            }
            // line 110
            echo "\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['message'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 111
        echo "\t\t\t\t</tbody>
\t\t\t</table>
\t\t</section>
\t</article>
</section>

";
    }

    public function getTemplateName()
    {
        return "UserBundle:ChatLog:view.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  321 => 107,  243 => 78,  793 => 351,  780 => 348,  758 => 341,  700 => 303,  686 => 292,  652 => 274,  638 => 266,  620 => 254,  545 => 218,  523 => 203,  494 => 183,  459 => 163,  438 => 150,  351 => 116,  347 => 114,  402 => 142,  268 => 67,  430 => 120,  411 => 136,  379 => 101,  322 => 94,  315 => 92,  289 => 84,  284 => 93,  255 => 65,  234 => 63,  1133 => 64,  1124 => 57,  1121 => 56,  1116 => 55,  1113 => 54,  1108 => 51,  1103 => 43,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 346,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 329,  996 => 320,  989 => 316,  985 => 315,  981 => 314,  977 => 313,  970 => 311,  966 => 310,  955 => 303,  952 => 302,  943 => 299,  936 => 296,  930 => 293,  919 => 290,  917 => 289,  908 => 287,  905 => 286,  896 => 280,  891 => 278,  877 => 270,  862 => 267,  857 => 265,  837 => 253,  832 => 250,  827 => 248,  821 => 247,  803 => 243,  778 => 235,  769 => 233,  765 => 232,  753 => 229,  746 => 225,  743 => 224,  735 => 220,  730 => 218,  720 => 213,  717 => 212,  712 => 209,  691 => 204,  678 => 202,  654 => 196,  587 => 239,  576 => 179,  539 => 171,  517 => 169,  471 => 155,  441 => 151,  437 => 142,  418 => 115,  386 => 125,  373 => 120,  304 => 102,  270 => 80,  265 => 77,  229 => 74,  477 => 135,  455 => 150,  448 => 164,  429 => 138,  407 => 95,  399 => 93,  389 => 126,  375 => 123,  358 => 116,  349 => 72,  335 => 68,  327 => 102,  298 => 58,  280 => 56,  249 => 66,  194 => 50,  142 => 24,  344 => 113,  318 => 106,  306 => 107,  295 => 98,  357 => 119,  300 => 130,  286 => 101,  276 => 71,  269 => 53,  254 => 67,  128 => 35,  237 => 44,  165 => 51,  122 => 33,  798 => 242,  770 => 113,  759 => 112,  748 => 337,  731 => 108,  721 => 107,  718 => 106,  708 => 104,  696 => 102,  617 => 188,  590 => 91,  553 => 87,  550 => 176,  540 => 84,  533 => 82,  500 => 186,  493 => 72,  489 => 181,  482 => 69,  467 => 67,  464 => 165,  458 => 64,  452 => 62,  449 => 156,  415 => 55,  382 => 124,  372 => 126,  361 => 82,  356 => 48,  339 => 97,  302 => 42,  285 => 40,  258 => 37,  123 => 32,  108 => 33,  424 => 135,  394 => 86,  380 => 80,  338 => 113,  319 => 66,  316 => 65,  312 => 110,  290 => 102,  267 => 88,  206 => 43,  110 => 25,  240 => 82,  224 => 61,  219 => 71,  217 => 73,  202 => 53,  186 => 46,  170 => 43,  100 => 23,  67 => 20,  14 => 1,  1096 => 291,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 332,  1013 => 275,  1007 => 274,  1002 => 272,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 300,  940 => 298,  937 => 248,  928 => 244,  926 => 243,  915 => 237,  912 => 236,  903 => 231,  898 => 230,  892 => 229,  889 => 228,  887 => 227,  884 => 226,  876 => 222,  874 => 269,  871 => 220,  863 => 214,  861 => 213,  858 => 212,  850 => 263,  843 => 206,  840 => 205,  815 => 245,  812 => 244,  808 => 199,  804 => 120,  799 => 196,  791 => 190,  785 => 238,  775 => 184,  771 => 183,  754 => 340,  728 => 170,  726 => 169,  723 => 168,  715 => 105,  711 => 163,  709 => 162,  706 => 161,  698 => 208,  694 => 156,  692 => 155,  689 => 154,  681 => 150,  677 => 149,  675 => 148,  663 => 143,  661 => 277,  650 => 195,  646 => 136,  629 => 129,  627 => 128,  625 => 127,  622 => 126,  598 => 119,  592 => 117,  586 => 115,  575 => 233,  566 => 103,  556 => 100,  554 => 177,  541 => 216,  536 => 91,  515 => 86,  511 => 85,  509 => 84,  488 => 164,  486 => 78,  483 => 77,  465 => 73,  463 => 72,  450 => 65,  432 => 60,  419 => 105,  371 => 46,  362 => 43,  353 => 73,  337 => 109,  333 => 105,  309 => 109,  303 => 88,  299 => 30,  291 => 96,  272 => 54,  261 => 95,  253 => 82,  239 => 64,  235 => 84,  213 => 60,  200 => 64,  198 => 52,  159 => 47,  149 => 45,  146 => 37,  131 => 55,  116 => 39,  79 => 18,  74 => 21,  71 => 19,  836 => 320,  817 => 319,  814 => 318,  811 => 317,  805 => 313,  787 => 115,  779 => 306,  776 => 305,  773 => 347,  761 => 296,  751 => 175,  747 => 293,  742 => 336,  739 => 291,  736 => 287,  724 => 214,  705 => 278,  702 => 277,  688 => 265,  680 => 263,  667 => 261,  662 => 260,  656 => 259,  649 => 258,  644 => 97,  641 => 194,  624 => 255,  613 => 187,  607 => 93,  597 => 225,  591 => 185,  584 => 218,  579 => 234,  563 => 230,  559 => 208,  551 => 221,  547 => 205,  537 => 201,  524 => 191,  512 => 185,  507 => 76,  504 => 182,  498 => 178,  485 => 176,  480 => 175,  472 => 171,  466 => 153,  460 => 71,  447 => 163,  442 => 162,  434 => 110,  428 => 144,  422 => 106,  404 => 149,  368 => 136,  364 => 83,  340 => 131,  334 => 111,  330 => 129,  325 => 100,  292 => 116,  287 => 115,  282 => 124,  279 => 82,  273 => 107,  266 => 91,  256 => 94,  252 => 93,  228 => 32,  218 => 78,  201 => 91,  64 => 19,  51 => 10,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 793,  2042 => 790,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 774,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 745,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 707,  1829 => 706,  1827 => 705,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 689,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 676,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 666,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 638,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 617,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 611,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 602,  1559 => 601,  1552 => 598,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 584,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 573,  1477 => 567,  1473 => 566,  1467 => 562,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 520,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 511,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 481,  1257 => 480,  1254 => 479,  1250 => 478,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 460,  1196 => 458,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 452,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 445,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 425,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 421,  1099 => 420,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 412,  1076 => 359,  1070 => 407,  1057 => 352,  1052 => 404,  1045 => 347,  1040 => 397,  1036 => 283,  1032 => 395,  1028 => 394,  1023 => 393,  1020 => 392,  1016 => 391,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 381,  979 => 379,  974 => 312,  967 => 373,  962 => 371,  958 => 304,  954 => 253,  950 => 368,  945 => 367,  942 => 250,  938 => 365,  934 => 364,  927 => 361,  923 => 292,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 272,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 207,  841 => 321,  835 => 312,  830 => 249,  826 => 309,  822 => 308,  818 => 246,  813 => 306,  810 => 305,  806 => 304,  802 => 197,  795 => 118,  792 => 239,  789 => 350,  784 => 295,  782 => 187,  777 => 291,  772 => 289,  768 => 345,  763 => 343,  760 => 231,  756 => 230,  752 => 284,  745 => 281,  741 => 280,  738 => 335,  732 => 171,  719 => 273,  714 => 280,  710 => 310,  704 => 267,  699 => 103,  695 => 264,  690 => 263,  687 => 203,  683 => 261,  679 => 288,  672 => 284,  668 => 256,  665 => 201,  658 => 141,  645 => 270,  640 => 247,  634 => 96,  628 => 193,  623 => 238,  619 => 237,  611 => 248,  606 => 234,  603 => 243,  599 => 242,  595 => 231,  583 => 114,  580 => 180,  573 => 221,  560 => 101,  543 => 172,  538 => 209,  534 => 208,  530 => 170,  526 => 89,  521 => 80,  518 => 204,  514 => 78,  510 => 202,  503 => 75,  496 => 197,  490 => 193,  484 => 192,  474 => 190,  470 => 168,  446 => 144,  440 => 184,  436 => 147,  431 => 146,  425 => 117,  416 => 104,  412 => 98,  408 => 112,  403 => 172,  400 => 111,  396 => 133,  392 => 169,  385 => 166,  381 => 125,  367 => 117,  363 => 155,  359 => 118,  355 => 115,  350 => 112,  346 => 71,  343 => 70,  328 => 110,  324 => 138,  313 => 93,  307 => 132,  301 => 101,  288 => 27,  283 => 72,  271 => 94,  257 => 84,  251 => 64,  238 => 34,  233 => 72,  195 => 49,  191 => 62,  187 => 47,  183 => 45,  130 => 28,  88 => 21,  76 => 21,  115 => 23,  95 => 23,  655 => 275,  651 => 232,  648 => 231,  637 => 223,  633 => 130,  621 => 219,  618 => 218,  615 => 121,  604 => 186,  600 => 226,  588 => 116,  585 => 204,  582 => 203,  571 => 104,  567 => 178,  555 => 207,  552 => 190,  549 => 215,  544 => 186,  542 => 185,  535 => 212,  531 => 90,  519 => 189,  516 => 199,  513 => 168,  508 => 172,  506 => 83,  499 => 198,  495 => 167,  491 => 165,  481 => 162,  478 => 68,  475 => 157,  469 => 154,  456 => 154,  451 => 186,  443 => 161,  439 => 147,  427 => 89,  423 => 139,  420 => 176,  409 => 54,  405 => 135,  401 => 132,  391 => 129,  387 => 129,  384 => 132,  378 => 123,  365 => 122,  360 => 120,  348 => 136,  336 => 111,  332 => 140,  329 => 134,  323 => 101,  310 => 92,  305 => 132,  277 => 23,  274 => 90,  263 => 69,  259 => 68,  247 => 110,  244 => 65,  241 => 62,  222 => 63,  210 => 59,  207 => 96,  204 => 94,  184 => 46,  181 => 45,  167 => 39,  157 => 36,  96 => 26,  421 => 144,  417 => 137,  414 => 151,  406 => 143,  398 => 129,  393 => 132,  390 => 135,  376 => 127,  369 => 122,  366 => 136,  352 => 115,  345 => 98,  342 => 109,  331 => 66,  326 => 96,  320 => 137,  317 => 98,  314 => 33,  311 => 104,  308 => 60,  297 => 101,  293 => 128,  281 => 92,  278 => 110,  275 => 68,  264 => 87,  260 => 76,  248 => 70,  245 => 90,  242 => 74,  231 => 60,  227 => 42,  215 => 83,  212 => 69,  209 => 54,  197 => 34,  177 => 43,  171 => 41,  161 => 36,  132 => 39,  121 => 48,  105 => 29,  99 => 34,  81 => 25,  77 => 20,  180 => 66,  176 => 54,  156 => 28,  143 => 30,  139 => 41,  118 => 25,  189 => 70,  185 => 67,  173 => 35,  166 => 68,  152 => 39,  174 => 42,  164 => 65,  154 => 46,  150 => 42,  137 => 33,  133 => 29,  127 => 27,  107 => 30,  102 => 28,  83 => 25,  78 => 22,  53 => 14,  23 => 6,  42 => 7,  138 => 30,  134 => 56,  109 => 30,  103 => 30,  97 => 26,  94 => 32,  84 => 25,  75 => 16,  69 => 15,  66 => 21,  54 => 9,  44 => 10,  230 => 72,  226 => 73,  203 => 51,  193 => 242,  188 => 68,  182 => 55,  178 => 30,  168 => 50,  163 => 79,  160 => 77,  155 => 40,  148 => 41,  145 => 43,  140 => 38,  136 => 40,  125 => 34,  120 => 51,  113 => 31,  101 => 29,  92 => 28,  89 => 27,  85 => 13,  73 => 16,  62 => 14,  59 => 13,  56 => 13,  41 => 4,  126 => 33,  119 => 33,  111 => 37,  106 => 29,  98 => 26,  93 => 26,  86 => 19,  70 => 34,  60 => 14,  28 => 4,  36 => 8,  114 => 38,  104 => 35,  91 => 17,  80 => 4,  63 => 15,  58 => 17,  40 => 12,  34 => 8,  45 => 14,  61 => 18,  55 => 11,  48 => 11,  39 => 10,  35 => 7,  31 => 4,  26 => 4,  21 => 5,  46 => 9,  29 => 8,  57 => 10,  50 => 11,  47 => 12,  38 => 10,  33 => 6,  49 => 8,  32 => 6,  246 => 79,  236 => 76,  232 => 43,  225 => 64,  221 => 58,  216 => 57,  214 => 98,  211 => 56,  208 => 68,  205 => 67,  199 => 50,  196 => 63,  190 => 47,  179 => 66,  175 => 61,  172 => 60,  169 => 45,  162 => 48,  158 => 41,  153 => 42,  151 => 41,  147 => 32,  144 => 42,  141 => 58,  135 => 51,  129 => 38,  124 => 36,  117 => 50,  112 => 20,  90 => 24,  87 => 16,  82 => 12,  72 => 21,  68 => 13,  65 => 12,  52 => 10,  43 => 13,  37 => 11,  30 => 5,  27 => 4,  25 => 3,  24 => 6,  22 => 2,  19 => 1,);
    }
}
