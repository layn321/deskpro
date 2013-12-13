<?php

/* AgentBundle:Ticket:replybox.html.twig */
class __TwigTemplate_b40a9fbfa23a0f6a97f5b9f7540c81c4 extends \Application\DeskPRO\Twig\Template
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
        $context["tools"] = $this->env->loadTemplate("AgentBundle:Common:agent-macros.html.twig");
        // line 2
        $context["replyBaseId"] = $this->env->getExtension('deskpro_templating')->elUid();
        // line 3
        echo "<form
\tclass=\"ticket-reply-form ";
        // line 4
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if ((!$this->getAttribute($_ticket_perms_, "reply"))) {
            echo "dp-note-on";
        }
        echo "\"
\tid=\"";
        // line 5
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_reply_form\"
\tmethod=\"post\"
\taction=\"";
        // line 7
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_ticket_ajaxsavereply", array("ticket_id" => $this->getAttribute($_ticket_, "id"))), "html", null, true);
        echo "\"

\tdata-element-handler=\"DeskPRO.Agent.ElementHandler.TicketReplyBox\"
\tdata-upload-url=\"";
        // line 10
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_accept_upload"), "html", null, true);
        echo "\"
\tdata-base-id=\"";
        // line 11
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "\"
    ";
        // line 12
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent.ticket_close_reply"), "method")) {
            echo "data-close-reply=\"1\"";
        }
        // line 13
        echo "    ";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent.ticket_close_note"), "method")) {
            echo "data-close-note=\"1\"";
        }
        // line 14
        echo "\t";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent.ticket_reverse_order"), "method")) {
            echo "data-is-top-order=\"1\"";
        }
        // line 15
        echo "\t";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.resolve_auto_close_tab"), "method")) {
            echo "data-resolve-auto-close=\"1\"";
        }
        // line 16
        echo "\t";
        if (isset($context["draft"])) { $_draft_ = $context["draft"]; } else { $_draft_ = null; }
        if (($_draft_ && $this->getAttribute($this->getAttribute($_draft_, "extras"), "is_note"))) {
            // line 17
            echo "\t\tdata-default-is-note=\"1\"
\t";
        }
        // line 19
        echo "\tdata-dp-lang=\"";
        echo twig_escape_filter($this->env, twig_jsonencode_filter(array("attach_btn" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.attach"), "snippets_btn" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.snippets"))), "html", null, true);
        // line 22
        echo "\"
>

<input type=\"hidden\" id=\"";
        // line 25
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_is_note\" name=\"options[is_note]\" value=\"0\" />

<input type=\"hidden\" id=\"";
        // line 27
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_exist_agent_id\" value=\"";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_ticket_, "agent", array(), "any", false, true), "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "agent", array(), "any", false, true), "id"), 0)) : (0)), "html", null, true);
        echo "\" />
<input type=\"hidden\" id=\"";
        // line 28
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_exist_agent_team_id\" value=\"";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_ticket_, "agent_team", array(), "any", false, true), "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "agent_team", array(), "any", false, true), "id"), 0)) : (0)), "html", null, true);
        echo "\" />
<input type=\"hidden\" id=\"";
        // line 29
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_action\" name=\"options[action]\" value=\"\" />

<div class=\"dp-menu replybox\" id=\"";
        // line 31
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_status_menu\" style=\"min-width: 225px;\">
\t<section>
\t\t<div class=\"dp-menu-area small\">
\t\t\t<input type=\"text\" class=\"label-input macro-filter\" placeholder=\"";
        // line 34
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.reply_and_apply_macro");
        echo "\" />
\t\t\t<ul class=\"macro-list\">
\t\t\t\t";
        // line 36
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($this->getAttribute($_app_, "user"), "getHelper", array(0 => "Agent"), "method"), "getMacros", array(), "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["macro"]) {
            // line 37
            echo "\t\t\t\t\t<li class=\"res-ticketmacro-";
            if (isset($context["macro"])) { $_macro_ = $context["macro"]; } else { $_macro_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_macro_, "id"), "html", null, true);
            echo "\" data-type=\"macro:";
            if (isset($context["macro"])) { $_macro_ = $context["macro"]; } else { $_macro_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_macro_, "id"), "html", null, true);
            echo "\" id=\"";
            if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
            echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
            echo "_res_ticketmacro_";
            if (isset($context["macro"])) { $_macro_ = $context["macro"]; } else { $_macro_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_macro_, "id"), "html", null, true);
            echo "\" data-macro-title=\"";
            if (isset($context["macro"])) { $_macro_ = $context["macro"]; } else { $_macro_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_macro_, "title"), "html", null, true);
            echo "\" data-macro-id=\"";
            if (isset($context["macro"])) { $_macro_ = $context["macro"]; } else { $_macro_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_macro_, "id"), "html", null, true);
            echo "\" data-label=\"";
            if (isset($context["macro"])) { $_macro_ = $context["macro"]; } else { $_macro_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.send_reply_and_apply_x", array("name" => $this->getAttribute($_macro_, "title")));
            echo "\" data-get-macro-url=\"";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if (isset($context["macro"])) { $_macro_ = $context["macro"]; } else { $_macro_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_ticket_ajax_get_macro", array("ticket_id" => $this->getAttribute($_ticket_, "id"), "macro_id" => $this->getAttribute($_macro_, "id"), "macro_reply_context" => 1)), "html", null, true);
            echo "\">
\t\t\t\t\t\t<div class=\"on-icon\"><i class=\"icon-ok\"></i></div>
\t\t\t\t\t\t<span class=\"macro-title\">";
            // line 39
            if (isset($context["macro"])) { $_macro_ = $context["macro"]; } else { $_macro_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_macro_, "title"), "html", null, true);
            echo "</span>
\t\t\t\t\t</li>
\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['macro'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 42
        echo "\t\t\t</ul>
\t\t</div>
\t</section>
\t<section>
\t\t<div class=\"dp-menu-area dp-menu-area-primary\">
\t\t\t<label>";
        // line 47
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.reply_and_set_status");
        echo "</label>
\t\t\t<ul>
\t\t\t\t<li data-type=\"awaiting_user\" data-label=\"";
        // line 49
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.send_reply_as_x", array("status" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_awaiting_user")));
        echo "\">
\t\t\t\t\t<div class=\"on-icon\"><i class=\"icon-ok\"></i></div>
\t\t\t\t\t";
        // line 51
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.kbd_awaiting_user");
        echo "
\t\t\t\t</li>
\t\t\t\t<li data-type=\"awaiting_agent\" data-label=\"";
        // line 53
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.send_reply_as_x", array("status" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_awaiting_agent")));
        echo "\">
\t\t\t\t\t<div class=\"on-icon\"><i class=\"icon-ok\"></i></div>
\t\t\t\t\t";
        // line 55
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.kbd_awaiting_agent");
        echo "
\t\t\t\t</li>
\t\t\t\t";
        // line 57
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if ($this->getAttribute($_ticket_perms_, "modify_set_resolved")) {
            // line 58
            echo "\t\t\t\t\t<li data-type=\"resolved\" data-label=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.send_reply_as_x", array("status" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.status_resolved")));
            echo "\">
\t\t\t\t\t\t<div class=\"on-icon\"><i class=\"icon-ok\"></i></div>
\t\t\t\t\t\t";
            // line 60
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.kbd_resolved");
            echo "
\t\t\t\t\t</li>
\t\t\t\t";
        }
        // line 63
        echo "\t\t\t</ul>
\t\t</div>
\t</section>
</div>

<div class=\"profile-box-container reply-box-wrap\" style=\"position: relative\">
\t<div class=\"drop-file-zone drop-file-zone-file\" id=\"";
        // line 69
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_file_drop_zone\"><h1>";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.drop_here_to_attach");
        echo "</h1></div>
\t<div class=\"ticket-errors-overlay\">
\t\t<strong>
\t\t\t";
        // line 72
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.error_reply_badfields");
        echo "
\t\t</strong>
\t</div>
\t<div class=\"ticket-sending-overlay\" style=\"display: none;\">
\t\t<strong><span class=\"hide-note\">";
        // line 76
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.sending_your_message");
        echo "</span><span class=\"hide-reply\" style=\"display: none\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.adding_your_note");
        echo "</span></strong>
\t</div>
\t<header>
\t\t";
        // line 79
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "isPluginInstalled", array(0 => "MicrosoftTranslator"), "method")) {
            // line 80
            echo "\t\t\t<div class=\"translate-controls\">
\t\t\t\t<div class=\"dp-not-loading\">
\t\t\t\t\t<div class=\"dp-btn-group dp-dropdown\">
\t\t\t\t\t\t<a class=\"dp-btn dp-btn-primary trans-trigger\">
\t\t\t\t\t\t\t<i class=\"icon-globe\"></i>&nbsp;
\t\t\t\t\t\t\t";
            // line 85
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.translate_into");
            echo "
\t\t\t\t\t\t\t<span class=\"translate-lang\" data-locale=\"";
            // line 86
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_ticket_, "language", array(), "any", false, true), "locale", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "language", array(), "any", false, true), "locale"), $this->getAttribute($this->getAttribute($this->getAttribute($_app_, "languages"), "getDefault", array(), "method"), "locale"))) : ($this->getAttribute($this->getAttribute($this->getAttribute($_app_, "languages"), "getDefault", array(), "method"), "locale"))), "html", null, true);
            echo "\">
\t\t\t\t\t\t\t\t";
            // line 87
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if ($this->getAttribute($_ticket_, "language")) {
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPhraseObject($this->getAttribute($_ticket_, "language")), "html", null, true);
            } else {
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPhraseObject($this->getAttribute($this->getAttribute($_app_, "languages"), "getDefault", array(), "method")), "html", null, true);
            }
            // line 88
            echo "\t\t\t\t\t\t\t</span>
\t\t\t\t\t\t</a>
\t\t\t\t\t\t<a class=\"dp-btn dp-btn-primary dp-dropdown-toggle\">
\t\t\t\t\t\t\t<select class=\"language_id\">
\t\t\t\t\t\t\t\t";
            // line 92
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "getPluginService", array(0 => "MicrosoftTranslator.tr_api"), "method"), "getLanguagesForTranslate", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["code"]) {
                // line 93
                echo "\t\t\t\t\t\t\t\t\t";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                if (isset($context["code"])) { $_code_ = $context["code"]; } else { $_code_ = null; }
                $context["name"] = $this->getAttribute($this->getAttribute($_app_, "getPluginService", array(0 => "MicrosoftTranslator.tr_api"), "method"), "getSingleLanguageName", array(0 => $_code_), "method");
                // line 94
                echo "\t\t\t\t\t\t\t\t\t<option value=\"";
                if (isset($context["code"])) { $_code_ = $context["code"]; } else { $_code_ = null; }
                echo twig_escape_filter($this->env, $_code_, "html", null, true);
                echo "\">";
                if (isset($context["name"])) { $_name_ = $context["name"]; } else { $_name_ = null; }
                echo twig_escape_filter($this->env, $_name_, "html", null, true);
                echo "</option>
\t\t\t\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['code'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 96
            echo "\t\t\t\t\t\t\t</select>
\t\t\t\t\t\t\t<span class=\"dp-caret dp-caret-down\"></span>
\t\t\t\t\t\t</a>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t<div class=\"dp-is-loading\" style=\"padding: 2px 4px 0 0;\">
\t\t\t\t\t<i class=\"spinner-flat\"></i>
\t\t\t\t</div>
\t\t\t</div>
\t\t";
        }
        // line 106
        echo "\t\t<nav>
\t\t\t<ul>
\t\t\t\t";
        // line 108
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if ($this->getAttribute($_ticket_perms_, "reply")) {
            echo "<li id=\"";
            if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
            echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
            echo "_replybox_replytab_btn\" class=\"on\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.reply");
            echo "</li>";
        }
        // line 109
        echo "\t\t\t\t";
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if ($this->getAttribute($_ticket_perms_, "modify_notes")) {
            echo "<li id=\"";
            if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
            echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
            echo "_replybox_notetab_btn\" class=\"note-type ";
            if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
            if ((!$this->getAttribute($_ticket_perms_, "reply"))) {
                echo "on";
            }
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.note");
            echo "</li>";
        }
        // line 110
        echo "\t\t\t</ul>
\t\t</nav>
\t</header>
\t<section class=\"ticketreply\">
\t\t<article>
\t\t\t<section>
\t\t\t\t<div class=\"option-rows\">
\t\t\t\t\t<ul id=\"";
        // line 117
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_option_rows\">
\t\t\t\t\t\t<li class=\"to-row hide-note\">
\t\t\t\t\t\t\t<label>
\t                            ";
        // line 120
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if (isset($context["user_parts"])) { $_user_parts_ = $context["user_parts"]; } else { $_user_parts_ = null; }
        if (($this->getAttribute($_ticket_perms_, "modify_cc") || twig_length_filter($this->env, $_user_parts_))) {
            // line 121
            echo "\t\t\t\t\t\t\t\t    <span class=\"expander ";
            if (isset($context["user_parts"])) { $_user_parts_ = $context["user_parts"]; } else { $_user_parts_ = null; }
            if (twig_length_filter($this->env, $_user_parts_)) {
                echo "expanded";
            }
            echo "\" data-target=\"#";
            if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
            echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
            echo "_cc_row\">+/-</span>
\t                            ";
        }
        // line 123
        echo "\t\t\t\t\t\t\t\t";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.to");
        echo ":
\t\t\t\t\t\t\t</label>
\t\t\t\t\t\t\t<div>
\t\t\t\t\t\t\t\t";
        // line 126
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "person"), "display_name"), "html", null, true);
        echo "
\t\t\t\t\t\t\t\t";
        // line 127
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ($this->getAttribute($_ticket_, "person_email")) {
            // line 128
            echo "\t\t\t\t\t\t\t\t\t&lt;";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "person_email"), "email"), "html", null, true);
            echo "&gt;
\t\t\t\t\t\t\t\t";
        } elseif ($this->getAttribute($this->getAttribute($_ticket_, "person"), "primary_email")) {
            // line 130
            echo "\t\t\t\t\t\t\t\t\t&lt;";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_ticket_, "person"), "primary_email"), "email"), "html", null, true);
            echo "&gt;
\t\t\t\t\t\t\t\t";
        }
        // line 132
        echo "\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t";
        // line 134
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ((!$this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent.ticket_reverse_order"), "method"))) {
            // line 135
            echo "\t\t\t\t\t\t\t";
            if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
            if (isset($context["user_parts"])) { $_user_parts_ = $context["user_parts"]; } else { $_user_parts_ = null; }
            if (($this->getAttribute($_ticket_perms_, "modify_cc") || twig_length_filter($this->env, $_user_parts_))) {
                // line 136
                echo "\t\t\t\t\t\t\t\t<li id=\"";
                if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
                echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
                echo "_cc_row\" class=\"cc-row hide-note ";
                if (isset($context["user_parts"])) { $_user_parts_ = $context["user_parts"]; } else { $_user_parts_ = null; }
                if ((!twig_length_filter($this->env, $_user_parts_))) {
                    echo "is-hidden";
                }
                echo "\" ";
                if (isset($context["user_parts"])) { $_user_parts_ = $context["user_parts"]; } else { $_user_parts_ = null; }
                if ((!twig_length_filter($this->env, $_user_parts_))) {
                    echo "style=\"display: none;\"";
                }
                echo ">
\t\t\t\t\t\t\t\t\t<label>";
                // line 137
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.cc");
                echo ":</label>
\t\t\t\t\t\t\t\t\t<div class=\"cc-container\">
\t\t\t\t\t\t\t\t\t\t<div id=\"";
                // line 139
                if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
                echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
                echo "_newcc\" style=\"display: none;\"></div>
\t\t\t\t\t\t\t\t\t\t<div id=\"";
                // line 140
                if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
                echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
                echo "_delcc\" style=\"display: none;\"></div>

\t\t\t\t\t\t\t\t\t\t<div class=\"user-rows\" id=\"";
                // line 142
                if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
                echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
                echo "_cc_user_rows\">
\t\t\t\t\t\t\t\t\t\t\t<ul class=\"cc-row-list\">
\t\t\t\t\t\t\t\t\t\t\t\t";
                // line 144
                if (isset($context["user_parts"])) { $_user_parts_ = $context["user_parts"]; } else { $_user_parts_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($_user_parts_);
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
                foreach ($context['_seq'] as $context["_key"] => $context["p"]) {
                    // line 145
                    echo "\t\t\t\t\t\t\t\t\t\t\t\t\t";
                    if (isset($context["p"])) { $_p_ = $context["p"]; } else { $_p_ = null; }
                    if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
                    $this->env->loadTemplate("AgentBundle:Ticket:view-user-cc-row.html.twig")->display(array_merge($context, array("person" => $this->getAttribute($_p_, "person"), "ticket_perms" => $_ticket_perms_)));
                    // line 146
                    echo "\t\t\t\t\t\t\t\t\t\t\t\t";
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
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['p'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 147
                echo "\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t<script class=\"email-row-tpl\" type=\"text/x-deskpro-tmpl\">
\t\t\t\t\t\t\t\t\t\t\t<div class=\"cc-user-row\">
\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"btn-small-remove remove-row-trigger\"></span>
\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"user-email\"></span>
\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t</script>
\t\t\t\t\t\t\t\t\t\t<div class=\"addrow noedit-hide\">
\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" class=\"user-part cc-people-search-trigger\" placeholder=\"Choose a person\" />
\t\t\t\t\t\t\t\t\t\t\t<div class=\"person-search-box\" style=\"display: none\">
\t\t\t\t\t\t\t\t\t\t\t\t<section>
\t\t\t\t\t\t\t\t\t\t\t\t\t<ul class=\"results-list\">

\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t\t\t\t\t\t\t</section>
\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t<button class=\"clean-white small cc-saverow-trigger\">";
                // line 164
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.add");
                echo "</button>
\t\t\t\t\t\t\t\t\t\t\t<script class=\"user-row-tpl\" type=\"text/x-deskpro-tmpl\">
\t\t\t\t\t\t\t\t\t\t\t\t<li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<a>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"user-name\"></span>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<address>&lt;<span class=\"user-email\"></span>&gt;</address>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"bound-fade\"></div>
\t\t\t\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</script>
\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t";
            }
            // line 178
            echo "\t\t\t\t\t\t";
        }
        // line 179
        echo "\t\t\t\t\t\t";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.enable_billing"), "method") && $this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.billing_on_reply"), "method"))) {
            // line 180
            echo "\t\t\t\t\t\t\t<li id=\"";
            if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
            echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
            echo "_billing_reply\">
\t\t\t\t\t\t\t\t<label>";
            // line 181
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.charge");
            echo ":</label>
\t\t\t\t\t\t\t\t<div>
\t\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"charge_time\" value=\"0\" checked=\"checked\" /> ";
            // line 183
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.charge_time");
            echo ": <span id=\"";
            if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
            echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
            echo "_billing_reply_time\"></span></label>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t";
        }
        // line 187
        echo "\t\t\t\t\t\t<li id=\"";
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_attach_row\" ";
        if (isset($context["draft_attachments"])) { $_draft_attachments_ = $context["draft_attachments"]; } else { $_draft_attachments_ = null; }
        if ($_draft_attachments_) {
            echo "class=\"attach-row\"";
        } else {
            echo "class=\"attach-row is-hidden\" style=\"display: none\"";
        }
        echo ">
\t\t\t\t\t\t\t<label><span>";
        // line 188
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.attachments");
        echo "</span></label>
\t\t\t\t\t\t\t<div>
\t\t\t\t\t\t\t\t<ul class=\"files\" style=\"margin-left:75px;\">";
        // line 191
        if (isset($context["draft_attachments"])) { $_draft_attachments_ = $context["draft_attachments"]; } else { $_draft_attachments_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_draft_attachments_);
        foreach ($context['_seq'] as $context["_key"] => $context["blob"]) {
            // line 192
            echo "<li>
\t\t\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"attach[]\" value=\"";
            // line 193
            if (isset($context["blob"])) { $_blob_ = $context["blob"]; } else { $_blob_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_blob_, "id"), "html", null, true);
            echo "\" />
\t\t\t\t\t\t\t\t\t\t<em class=\"remove-attach-trigger\"></em>
\t\t\t\t\t\t\t\t\t\t<label><a href=\"";
            // line 195
            if (isset($context["blob"])) { $_blob_ = $context["blob"]; } else { $_blob_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_blob_, "download_url"), "html", null, true);
            echo "\" target=\"_blank\">";
            if (isset($context["blob"])) { $_blob_ = $context["blob"]; } else { $_blob_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_blob_, "filename"), "html", null, true);
            echo "</a><span>";
            if (isset($context["blob"])) { $_blob_ = $context["blob"]; } else { $_blob_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_blob_, "readable_filesize"), "html", null, true);
            echo "</span></label>
\t\t\t\t\t\t\t\t\t</li>";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['blob'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 198
        echo "</ul>
\t\t\t\t\t\t\t\t<br class=\"clear\"/>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t<li id=\"";
        // line 202
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_actions_row\" class=\"actions-row\" style=\"display: none;\">
\t\t\t\t\t\t\t<label><span>";
        // line 203
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.actions");
        echo "</span></label>
\t\t\t\t\t\t\t<ul></ul>
\t\t\t\t\t\t\t<br class=\"clear\"/>
\t\t\t\t\t\t</li>
\t\t\t\t\t</ul>
\t\t\t\t</div>
\t\t\t\t<div class=\"input-wrap unreset editor-row\">
\t\t\t\t\t<textarea placeholder=\"\" id=\"";
        // line 210
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_replybox_txt\" name=\"message\" ";
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (($this->getAttribute($_ticket_, "language") && $this->getAttribute($this->getAttribute($_ticket_, "language"), "is_rtl"))) {
            echo "dir=\"rtl\"";
        }
        echo "></textarea>
\t\t\t\t\t<textarea id=\"";
        // line 211
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_signature_value\" class=\"signature-value\" style=\"display: none;\">";
        if (isset($context["agent_signature"])) { $_agent_signature_ = $context["agent_signature"]; } else { $_agent_signature_ = null; }
        echo twig_escape_filter($this->env, $_agent_signature_, "html", null, true);
        echo "</textarea>
\t\t\t\t\t<textarea id=\"";
        // line 212
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_signature_value_html\" class=\"signature-value-html\" style=\"display: none;\">";
        if (isset($context["agent_signature_html"])) { $_agent_signature_html_ = $context["agent_signature_html"]; } else { $_agent_signature_html_ = null; }
        echo twig_escape_filter($this->env, $_agent_signature_html_, "html", null, true);
        echo "</textarea>
\t\t\t\t\t";
        // line 213
        if (isset($context["draft"])) { $_draft_ = $context["draft"]; } else { $_draft_ = null; }
        if ($_draft_) {
            // line 214
            echo "\t\t\t\t\t\t<textarea id=\"";
            if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
            echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
            echo "_draft_html\" class=\"draft-html\" style=\"display: none;\">";
            if (isset($context["draft"])) { $_draft_ = $context["draft"]; } else { $_draft_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_draft_, "message"), "html", null, true);
            echo "</textarea>
\t\t\t\t\t";
        }
        // line 216
        echo "\t\t\t\t\t<input type=\"hidden\" id=\"";
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_is_html_reply\" name=\"is_html_reply\" value=\"0\" />
\t\t\t\t\t";
        // line 217
        if (isset($context["draft"])) { $_draft_ = $context["draft"]; } else { $_draft_ = null; }
        if (($_draft_ && $this->getAttribute($this->getAttribute($_draft_, "extras"), "blob_inline_ids"))) {
            // line 218
            echo "\t\t\t\t\t\t";
            if (isset($context["draft"])) { $_draft_ = $context["draft"]; } else { $_draft_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_draft_, "extras"), "blob_inline_ids"));
            foreach ($context['_seq'] as $context["_key"] => $context["blob_inline_id"]) {
                // line 219
                echo "\t\t\t\t\t\t\t<input type=\"hidden\" name=\"blob_inline_ids[]\" value=\"";
                if (isset($context["blob_inline_id"])) { $_blob_inline_id_ = $context["blob_inline_id"]; } else { $_blob_inline_id_ = null; }
                echo twig_escape_filter($this->env, $_blob_inline_id_, "html", null, true);
                echo "\" />
\t\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['blob_inline_id'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 221
            echo "\t\t\t\t\t";
        }
        // line 222
        echo "\t\t\t\t\t<div class=\"drop-file-zone drop-file-zone-rte\"><h1>";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.drop_here_to_insert_image");
        echo "</h1></div>
\t\t\t\t</div>
\t\t\t\t";
        // line 224
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "isPluginInstalled", array(0 => "MicrosoftTranslator"), "method")) {
            // line 225
            echo "\t\t\t\t\t<div class=\"input-wrap unreset translate-row\" style=\"display: none;\">
\t\t\t\t\t\t<input type=\"hidden\" name=\"reply_is_trans\" id=\"";
            // line 226
            if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
            echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
            echo "_reply_is_trans\" value=\"\" />
\t\t\t\t\t\t<textarea placeholder=\"\" id=\"";
            // line 227
            if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
            echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
            echo "_replybox_txt2\" name=\"message_original\" ";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if (($this->getAttribute($_ticket_, "language") && $this->getAttribute($this->getAttribute($_ticket_, "language"), "is_rtl"))) {
                echo "dir=\"rtl\"";
            }
            echo "></textarea>
\t\t\t\t\t</div>
\t\t\t\t";
        }
        // line 230
        echo "\t\t\t</section>
\t\t</article>
\t</section>
\t<footer>
\t\t<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr>
\t\t\t<td valign=\"middle\" style=\"vertical-align: middle;\">
\t\t\t\t<div class=\"hide-note\">
\t\t\t\t\t<div class=\"dp-btn-group dp-dropup\" id=\"";
        // line 237
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_reply_btn_group\" style=\"position: relative; z-index: 50;\">
\t\t\t\t\t\t<a class=\"dp-btn dp-btn-primary submit-trigger\">
\t\t\t\t\t\t\t<i class=\"icon-share-alt\"></i>&nbsp;
\t\t\t\t\t\t\t";
        // line 240
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ((($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.reply_status"), "method") == "awaiting_user") || (($this->getAttribute($_ticket_, "status") == "awaiting_user") && (!$this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.reply_status"), "method"))))) {
            // line 241
            echo "\t\t\t\t\t\t\t\t<span id=\"";
            if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
            echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
            echo "_reply_as_type\" data-type=\"awaiting_user\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.kbd_send_awaiting_user");
            echo "</span>
\t\t\t\t\t\t\t";
        } elseif ((($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.reply_status"), "method") == "awaiting_agent") || (($this->getAttribute($_ticket_, "status") == "awaiting_agent") && (!$this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.reply_status"), "method"))))) {
            // line 243
            echo "\t\t\t\t\t\t\t\t<span id=\"";
            if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
            echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
            echo "_reply_as_type\" data-type=\"awaiting_agent\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.kbd_send_awaiting_agent");
            echo "</span>
\t\t\t\t\t\t\t";
        } elseif ((($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.reply_status"), "method") == "resolved") || (($this->getAttribute($_ticket_, "status") == "resolved") && (!$this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.reply_status"), "method"))))) {
            // line 245
            echo "\t\t\t\t\t\t\t\t<span id=\"";
            if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
            echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
            echo "_reply_as_type\" data-type=\"resolved\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.kbd_send_resolved");
            echo "</span>
\t\t\t\t\t\t\t";
        } else {
            // line 247
            echo "\t\t\t\t\t\t\t\t<span id=\"";
            if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
            echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
            echo "_reply_as_type\" data-type=\"awaiting_user\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.kbd_send_awaiting_user");
            echo "</span>
\t\t\t\t\t\t\t";
        }
        // line 249
        echo "\t\t\t\t\t\t</a>
\t\t\t\t\t\t<a class=\"dp-btn dp-btn-primary dp-dropdown-toggle status-menu-trigger\"><span class=\"dp-caret dp-caret-up\"></span></a>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t<div class=\"hide-reply\" style=\"display: none\">
\t\t\t\t\t<a class=\"dp-btn dp-btn-primary submit-trigger\" style=\"position: relative; z-index: 50;\">
\t\t\t\t\t\t<i class=\"icon-share\"></i>&nbsp;
\t\t\t\t\t\t";
        // line 256
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.add_note");
        echo "
\t\t\t\t\t</a>
\t\t\t\t</div>
\t\t\t</td><td valign=\"middle\" style=\"vertical-align: middle;\">
\t\t\t\t<div class=\"cell\">
\t\t\t\t\t";
        // line 261
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ($this->getAttribute($_ticket_, "agent")) {
            // line 262
            echo "\t\t\t\t\t\t";
            $context["assign_opt"] = "core_tickets.reply_assign_assigned";
            // line 263
            echo "\t\t\t\t\t";
        } else {
            // line 264
            echo "\t\t\t\t\t\t";
            $context["assign_opt"] = "core_tickets.reply_assign_unassigned";
            // line 265
            echo "\t\t\t\t\t";
        }
        // line 266
        echo "\t\t\t\t\t<div class=\"inner-cell\" style=\"padding-left: 11px;\">
\t\t\t\t\t\t<input type=\"checkbox\" name=\"options[do_assign_agent]\" id=\"";
        // line 267
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_agent_sel_check\" ";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (isset($context["assign_opt"])) { $_assign_opt_ = $context["assign_opt"]; } else { $_assign_opt_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => $_assign_opt_), "method")) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\t\t\t<label for=\"";
        // line 268
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_agent_sel_check\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.agent");
        echo ":&nbsp;</label>
\t\t\t\t\t</div><div class=\"inner-cell\" style=\"position: relative;\">
\t\t\t\t\t\t<span style=\"cursor: pointer;\"><span id=\"";
        // line 270
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_agent_sel_text\" style=\"padding-left: 17px; background-repeat: no-repeat; background-position: 0 50%;\"></span> <i class=\"icon-sort-down\" style=\"color: #888; position: relative; top: -2px;\"></i></span>
\t\t\t\t\t\t<select name=\"options[agent_id]\" id=\"";
        // line 271
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_agent_sel\" class=\"dpe_select\" data-style-type=\"icons\" data-select-icon-size=\"22\" style=\"min-width: 170px;\" ";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.reassign_auto_change_status"), "method")) {
            echo "data-auto-switch-status=\"awaiting_agent\"";
        }
        echo " data-invisible-trigger-right=\"1\">
\t\t\t\t\t\t\t<option value=\"";
        // line 272
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "user"), "id"), "html", null, true);
        echo "\" data-icon=\"";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "user"), "getPictureUrl", array(0 => 15), "method"), "html", null, true);
        echo "\" data-name-short=\"Me\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.me");
        echo "</option>
\t\t\t\t\t\t\t";
        // line 273
        if (isset($context["agents"])) { $_agents_ = $context["agents"]; } else { $_agents_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_agents_);
        foreach ($context['_seq'] as $context["_key"] => $context["agent"]) {
            if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if (($this->getAttribute($_agent_, "id") != $this->getAttribute($this->getAttribute($_app_, "user"), "id"))) {
                // line 274
                echo "\t\t\t\t\t\t\t\t<option ";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                if (isset($context["assign_opt"])) { $_assign_opt_ = $context["assign_opt"]; } else { $_assign_opt_ = null; }
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                if ((((!$this->getAttribute($_app_, "getSetting", array(0 => $_assign_opt_), "method")) && ($this->getAttribute($_agent_, "id") == $this->getAttribute($this->getAttribute($_ticket_, "agent"), "id"))) || (($this->getAttribute($_app_, "getSetting", array(0 => $_assign_opt_), "method") == "assign") && ($this->getAttribute($_agent_, "id") == $this->getAttribute($this->getAttribute($_app_, "user"), "id"))))) {
                    echo "selected=\"selected\"";
                }
                echo " data-icon=\"";
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "getPictureUrl", array(0 => 15), "method"), "html", null, true);
                echo "\" data-name-short=\"";
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "getDisplayContactShort", array(0 => 3), "method"), "html", null, true);
                echo "\" value=\"";
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "id"), "html", null, true);
                echo "\">
\t\t\t\t\t\t\t\t\t";
                // line 275
                if (isset($context["agent"])) { $_agent_ = $context["agent"]; } else { $_agent_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_agent_, "display_name"), "html", null, true);
                echo "
\t\t\t\t\t\t\t\t</option>
\t\t\t\t\t\t\t";
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['agent'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 278
        echo "\t\t\t\t\t\t</select>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<div class=\"cell\">
\t\t\t\t\t<div class=\"inner-cell\" style=\"padding-left: 11px;\">
\t\t\t\t\t\t";
        // line 284
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ($this->getAttribute($_ticket_, "agent_team")) {
            // line 285
            echo "\t\t\t\t\t\t\t";
            $context["assign_opt"] = "core_tickets.reply_assignteam_assigned";
            // line 286
            echo "\t\t\t\t\t\t";
        } else {
            // line 287
            echo "\t\t\t\t\t\t\t";
            $context["assign_opt"] = "core_tickets.reply_assignteam_unassigned";
            // line 288
            echo "\t\t\t\t\t\t";
        }
        // line 289
        echo "\t\t\t\t\t\t<input type=\"checkbox\" name=\"options[do_assign_team]\" id=\"";
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_agent_team_sel_check\" ";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (isset($context["assign_opt"])) { $_assign_opt_ = $context["assign_opt"]; } else { $_assign_opt_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => $_assign_opt_), "method")) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\t\t\t<label for=\"";
        // line 290
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_agent_team_sel_check\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.team");
        echo ":&nbsp;</label>
\t\t\t\t\t</div><div class=\"inner-cell\" style=\"position: relative;\">
\t\t\t\t\t\t<span style=\"cursor: pointer;\"><span id=\"";
        // line 292
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_agent_team_sel_text\" ></span> <i class=\"icon-sort-down\" style=\"color: #888; position: relative; top: -2px;\"></i></span>
\t\t\t\t\t\t<select name=\"options[agent_team_id]\" id=\"";
        // line 293
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_agent_team_sel\" class=\"dpe_select\" data-select-nogrouptitle=\"1\" style=\"min-width: 170px;\" ";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.reassign_auto_change_status"), "method")) {
            echo "data-auto-switch-status=\"awaiting_agent\"";
        }
        echo " data-invisible-trigger-right=\"1\">
\t\t\t\t\t\t\t";
        // line 294
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
        if ((twig_length_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_app_, "user"), "getAgent", array(), "method"), "getTeams", array(), "method")) && ($this->getAttribute($_ticket_perms_, "modify_assign_self") || ($this->getAttribute($this->getAttribute($_ticket_, "agent_team"), "id") == $this->getAttribute($_team_, "id"))))) {
            // line 295
            echo "\t\t\t\t\t\t\t<optgroup label=\"";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ((twig_length_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_app_, "user"), "getAgent", array(), "method"), "getTeams", array(), "method")) > 1)) {
                echo "Your Teams";
            } else {
                echo "Your Team";
            }
            echo "\">
\t\t\t\t\t\t\t\t";
            // line 296
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($this->getAttribute($_app_, "user"), "getAgent", array(), "method"), "getTeams", array(), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["team"]) {
                // line 297
                echo "\t\t\t\t\t\t\t\t\t<option value=\"";
                if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_team_, "id"), "html", null, true);
                echo "\" ";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                if (isset($context["assign_opt"])) { $_assign_opt_ = $context["assign_opt"]; } else { $_assign_opt_ = null; }
                if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                if ((($this->getAttribute($_app_, "getSetting", array(0 => $_assign_opt_), "method") == "assign") && (($this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent.ticket_default_team_id"), "method") == $this->getAttribute($_team_, "id")) || (!$this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent.ticket_default_team_id"), "method"))))) {
                    echo "selected=\"selected\"";
                }
                echo ">";
                if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_team_, "name"), "html", null, true);
                echo "</option>
\t\t\t\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['team'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 299
            echo "\t\t\t\t\t\t\t</optgroup>
\t\t\t\t\t\t\t";
        }
        // line 301
        echo "
\t\t\t\t\t\t\t";
        // line 302
        if (isset($context["agent_teams"])) { $_agent_teams_ = $context["agent_teams"]; } else { $_agent_teams_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_agent_teams_);
        foreach ($context['_seq'] as $context["_key"] => $context["team"]) {
            // line 303
            echo "\t\t\t\t\t\t\t\t";
            if (isset($context["ticket_perms"])) { $_ticket_perms_ = $context["ticket_perms"]; } else { $_ticket_perms_ = null; }
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ((($this->getAttribute($_ticket_perms_, "modify_assign_team") || ($this->getAttribute($this->getAttribute($_ticket_, "agent_team"), "id") == $this->getAttribute($_team_, "id"))) && !twig_in_filter($this->getAttribute($_team_, "id"), $this->getAttribute($this->getAttribute($this->getAttribute($_app_, "user"), "getAgent", array(), "method"), "getTeamIds", array(), "method")))) {
                // line 304
                echo "\t\t\t\t\t\t\t\t\t<option value=\"";
                if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_team_, "id"), "html", null, true);
                echo "\" ";
                if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                if (isset($context["assign_opt"])) { $_assign_opt_ = $context["assign_opt"]; } else { $_assign_opt_ = null; }
                if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                if (((!$this->getAttribute($_app_, "getSetting", array(0 => $_assign_opt_), "method")) && ($this->getAttribute($_team_, "id") == $this->getAttribute($this->getAttribute($_ticket_, "agent_team"), "id")))) {
                    echo "selected=\"selected\"";
                }
                echo ">";
                if (isset($context["team"])) { $_team_ = $context["team"]; } else { $_team_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_team_, "name"), "html", null, true);
                echo "</option>
\t\t\t\t\t\t\t\t";
            }
            // line 306
            echo "\t\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['team'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 307
        echo "\t\t\t\t\t\t</select>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<div class=\"cell\">
\t\t\t\t\t<div class=\"inner-cell\" style=\"padding-left: 11px;\">
\t\t\t\t\t\t<input type=\"checkbox\" id=\"";
        // line 313
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_close_tab_opt\" name=\"options[close_tab]\" ";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent.ticket_close_reply", 1 => true), "method")) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\t\t\t<label for=\"";
        // line 314
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_close_tab_opt\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.close_tab");
        echo " <span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.close_tab_tip");
        echo "\"></span></label>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<div class=\"cell hide-note\">
\t\t\t\t\t<div class=\"inner-cell\" style=\"padding-left: 11px;\">
\t\t\t\t\t\t<input type=\"checkbox\" id=\"";
        // line 320
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_email_user\" name=\"options[notify_user]\" ";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core_tickets.default_send_user_notify"), "method")) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\t\t\t<label for=\"";
        // line 321
        if (isset($context["replyBaseId"])) { $_replyBaseId_ = $context["replyBaseId"]; } else { $_replyBaseId_ = null; }
        echo twig_escape_filter($this->env, $_replyBaseId_, "html", null, true);
        echo "_email_user\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.email_user");
        echo " <span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.email_user_tip");
        echo "\"></span></label>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</td>
\t\t</tr></table>
\t</footer>
</div>

";
        // line 329
        $this->env->loadTemplate("AgentBundle:Common:attach-row-tmpl.html.twig")->display(array_merge($context, array("formname" => "attach[]")));
        // line 330
        echo "
</form>
";
    }

    public function getTemplateName()
    {
        return "AgentBundle:Ticket:replybox.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1264 => 464,  1259 => 462,  1227 => 448,  1211 => 446,  1399 => 568,  1387 => 560,  1358 => 555,  1334 => 553,  1316 => 529,  1298 => 520,  1195 => 478,  1148 => 464,  1120 => 453,  1117 => 452,  1093 => 440,  788 => 316,  612 => 203,  1394 => 446,  1385 => 442,  1382 => 441,  1369 => 433,  1363 => 432,  1360 => 431,  1350 => 429,  1305 => 523,  1300 => 409,  1281 => 404,  1277 => 403,  1265 => 398,  1255 => 396,  1247 => 393,  1234 => 387,  1202 => 375,  1199 => 374,  1187 => 372,  1162 => 365,  1136 => 461,  1128 => 352,  1122 => 350,  1069 => 332,  968 => 387,  846 => 250,  1183 => 449,  1132 => 460,  1097 => 341,  957 => 356,  907 => 278,  875 => 263,  653 => 236,  1329 => 405,  1309 => 401,  1303 => 399,  1297 => 398,  1292 => 397,  1280 => 394,  1249 => 458,  1237 => 451,  1205 => 409,  1200 => 408,  1194 => 440,  1178 => 430,  1175 => 447,  1170 => 375,  1135 => 362,  1105 => 428,  1078 => 335,  1068 => 388,  1048 => 417,  961 => 358,  922 => 280,  750 => 245,  842 => 263,  1038 => 319,  904 => 322,  882 => 301,  831 => 267,  860 => 314,  790 => 284,  733 => 230,  707 => 283,  744 => 220,  873 => 349,  824 => 267,  762 => 243,  713 => 225,  578 => 158,  2739 => 1153,  2735 => 1152,  2732 => 1151,  2725 => 1146,  2712 => 1144,  2707 => 1143,  2692 => 1131,  2688 => 1130,  2683 => 1127,  2680 => 1126,  2677 => 1125,  2671 => 1121,  2658 => 1119,  2653 => 1118,  2645 => 1113,  2641 => 1112,  2636 => 1109,  2632 => 1108,  2629 => 1107,  2626 => 1106,  2606 => 1089,  2602 => 1088,  2598 => 1087,  2594 => 1086,  2590 => 1085,  2586 => 1084,  2572 => 1073,  2568 => 1072,  2564 => 1071,  2560 => 1070,  2556 => 1069,  2552 => 1068,  2537 => 1056,  2533 => 1055,  2529 => 1054,  2525 => 1053,  2521 => 1052,  2503 => 1040,  2499 => 1039,  2495 => 1038,  2491 => 1037,  2487 => 1036,  2483 => 1035,  2469 => 1024,  2465 => 1023,  2461 => 1022,  2457 => 1021,  2453 => 1020,  2449 => 1019,  2426 => 999,  2422 => 998,  2418 => 997,  2414 => 996,  2410 => 995,  2406 => 994,  2392 => 983,  2388 => 982,  2384 => 981,  2380 => 980,  2376 => 979,  2372 => 978,  2358 => 967,  2354 => 966,  2350 => 965,  2346 => 964,  2342 => 963,  2338 => 962,  2324 => 951,  2320 => 950,  2316 => 949,  2312 => 948,  2308 => 947,  2304 => 946,  2295 => 939,  2282 => 937,  2277 => 936,  2269 => 931,  2265 => 930,  2260 => 927,  2256 => 926,  2253 => 925,  2230 => 901,  2220 => 899,  2216 => 898,  2211 => 896,  2208 => 895,  2198 => 893,  2194 => 892,  2189 => 890,  2184 => 889,  2177 => 885,  2173 => 884,  2164 => 877,  2151 => 875,  2146 => 874,  2143 => 873,  2141 => 864,  2134 => 860,  2130 => 859,  2122 => 853,  2120 => 852,  2113 => 848,  2105 => 846,  2096 => 839,  2083 => 837,  2078 => 836,  2073 => 834,  2067 => 830,  2062 => 828,  2058 => 827,  2054 => 826,  2050 => 825,  2045 => 824,  2038 => 822,  2034 => 821,  2022 => 814,  2009 => 812,  1999 => 809,  1993 => 805,  1988 => 803,  1984 => 802,  1980 => 801,  1976 => 800,  1971 => 799,  1968 => 798,  1964 => 797,  1960 => 796,  1954 => 793,  1946 => 788,  1942 => 787,  1938 => 786,  1932 => 782,  1927 => 780,  1919 => 778,  1915 => 777,  1911 => 776,  1906 => 775,  1903 => 774,  1899 => 773,  1895 => 772,  1886 => 768,  1883 => 764,  1871 => 759,  1866 => 756,  1863 => 755,  1842 => 752,  1825 => 749,  1818 => 748,  1815 => 747,  1800 => 743,  1793 => 741,  1772 => 729,  1761 => 721,  1757 => 720,  1751 => 717,  1740 => 709,  1719 => 697,  1715 => 696,  1686 => 673,  1676 => 669,  1665 => 661,  1661 => 660,  1645 => 651,  1638 => 649,  1630 => 645,  1625 => 642,  1622 => 641,  1598 => 637,  1577 => 634,  1558 => 629,  1549 => 627,  1546 => 626,  1535 => 618,  1531 => 617,  1514 => 606,  1510 => 605,  1504 => 602,  1493 => 594,  1483 => 590,  1464 => 580,  1458 => 577,  1443 => 568,  1437 => 565,  1426 => 557,  1405 => 545,  1401 => 544,  1395 => 541,  1390 => 538,  1381 => 533,  1373 => 531,  1348 => 521,  1343 => 520,  1335 => 515,  1321 => 510,  1318 => 549,  1299 => 503,  1294 => 407,  1282 => 496,  1269 => 466,  1260 => 397,  1240 => 478,  1221 => 484,  1216 => 378,  1210 => 410,  1206 => 445,  1193 => 467,  1189 => 466,  1155 => 468,  1150 => 419,  1022 => 312,  1006 => 299,  988 => 398,  969 => 392,  965 => 294,  921 => 286,  878 => 275,  866 => 349,  854 => 254,  819 => 322,  796 => 286,  1713 => 683,  1700 => 681,  1685 => 674,  1662 => 670,  1643 => 669,  1640 => 668,  1631 => 663,  1618 => 661,  1613 => 660,  1608 => 639,  1605 => 656,  1602 => 655,  1589 => 645,  1586 => 644,  1583 => 643,  1554 => 613,  1547 => 608,  1521 => 602,  1508 => 600,  1499 => 598,  1491 => 593,  1482 => 587,  1472 => 582,  1468 => 581,  1465 => 579,  1457 => 573,  1445 => 566,  1422 => 556,  1416 => 553,  1410 => 548,  1397 => 546,  1392 => 545,  1386 => 541,  1377 => 532,  1370 => 530,  1357 => 528,  1352 => 554,  1341 => 428,  1314 => 510,  1311 => 509,  1275 => 493,  1248 => 484,  1238 => 488,  1225 => 476,  1220 => 412,  1209 => 466,  1185 => 385,  1182 => 431,  1159 => 421,  1154 => 449,  1130 => 438,  1125 => 407,  1101 => 426,  1074 => 432,  1056 => 326,  1046 => 323,  1043 => 306,  1030 => 397,  1027 => 412,  947 => 361,  925 => 352,  913 => 324,  893 => 357,  881 => 267,  847 => 343,  829 => 336,  825 => 259,  1083 => 396,  995 => 399,  984 => 350,  963 => 292,  941 => 354,  851 => 367,  682 => 217,  1365 => 556,  1359 => 545,  1349 => 539,  1336 => 427,  1331 => 424,  1323 => 420,  1319 => 530,  1312 => 402,  1284 => 475,  1272 => 401,  1268 => 509,  1261 => 501,  1251 => 485,  1245 => 483,  1231 => 496,  1207 => 376,  1197 => 491,  1180 => 484,  1173 => 457,  1169 => 472,  1157 => 363,  1147 => 438,  1109 => 330,  1065 => 440,  1059 => 423,  1047 => 385,  1044 => 424,  1033 => 381,  1009 => 357,  991 => 363,  987 => 404,  973 => 294,  931 => 355,  924 => 287,  911 => 347,  906 => 81,  885 => 337,  872 => 335,  855 => 332,  749 => 240,  701 => 221,  594 => 180,  1163 => 422,  1143 => 440,  1087 => 420,  1077 => 433,  1051 => 325,  1037 => 480,  1010 => 301,  999 => 407,  932 => 352,  899 => 306,  895 => 404,  933 => 387,  914 => 133,  909 => 323,  833 => 284,  783 => 315,  755 => 303,  666 => 214,  453 => 168,  639 => 209,  568 => 176,  520 => 232,  657 => 216,  572 => 201,  609 => 202,  20 => 1,  659 => 217,  562 => 164,  548 => 180,  558 => 197,  479 => 157,  589 => 223,  457 => 199,  413 => 174,  953 => 290,  948 => 379,  935 => 394,  929 => 283,  916 => 382,  864 => 365,  844 => 338,  816 => 342,  807 => 318,  801 => 268,  774 => 257,  766 => 312,  737 => 297,  685 => 218,  664 => 225,  635 => 249,  593 => 199,  546 => 201,  532 => 236,  865 => 296,  852 => 241,  838 => 285,  820 => 201,  781 => 327,  764 => 252,  725 => 250,  632 => 268,  602 => 261,  565 => 183,  529 => 171,  505 => 147,  487 => 101,  473 => 212,  1853 => 842,  1849 => 753,  1839 => 751,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 733,  1778 => 732,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 693,  1705 => 781,  1688 => 675,  1681 => 769,  1677 => 768,  1667 => 671,  1656 => 762,  1648 => 655,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 606,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 593,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 540,  1379 => 557,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 522,  1289 => 406,  1276 => 604,  1263 => 601,  1244 => 456,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 373,  1181 => 561,  1171 => 446,  1158 => 550,  1114 => 428,  1104 => 465,  1072 => 333,  1062 => 7,  1024 => 395,  1014 => 406,  1000 => 368,  990 => 303,  980 => 393,  960 => 466,  918 => 285,  888 => 80,  834 => 325,  673 => 64,  636 => 198,  462 => 142,  454 => 138,  1144 => 463,  1139 => 356,  1131 => 399,  1127 => 434,  1110 => 347,  1092 => 459,  1089 => 426,  1086 => 520,  1084 => 337,  1063 => 387,  1060 => 425,  1055 => 386,  1050 => 373,  1035 => 372,  1019 => 330,  1003 => 401,  959 => 387,  900 => 366,  880 => 276,  870 => 277,  867 => 334,  859 => 294,  848 => 271,  839 => 376,  828 => 302,  823 => 374,  809 => 262,  800 => 241,  797 => 267,  794 => 257,  786 => 283,  740 => 78,  734 => 307,  703 => 228,  693 => 297,  630 => 247,  626 => 19,  614 => 275,  610 => 236,  581 => 206,  564 => 268,  525 => 195,  722 => 226,  697 => 282,  674 => 270,  671 => 285,  577 => 180,  569 => 187,  557 => 179,  502 => 187,  497 => 228,  445 => 163,  729 => 306,  684 => 237,  676 => 216,  669 => 268,  660 => 203,  647 => 211,  643 => 229,  601 => 195,  570 => 169,  522 => 156,  501 => 163,  296 => 108,  374 => 115,  631 => 207,  616 => 198,  608 => 194,  605 => 193,  596 => 188,  574 => 180,  561 => 181,  527 => 165,  433 => 183,  388 => 98,  426 => 172,  383 => 105,  461 => 184,  370 => 147,  395 => 166,  294 => 106,  223 => 55,  220 => 67,  492 => 129,  468 => 144,  444 => 149,  410 => 150,  397 => 136,  377 => 121,  262 => 92,  250 => 78,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 1051,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 680,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 638,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 572,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 526,  1301 => 1037,  1252 => 392,  1243 => 994,  1223 => 978,  1212 => 494,  1167 => 366,  1161 => 933,  1145 => 923,  1140 => 413,  1134 => 409,  1126 => 443,  1106 => 528,  1100 => 897,  1026 => 418,  997 => 367,  983 => 298,  975 => 361,  956 => 318,  939 => 285,  902 => 274,  894 => 364,  879 => 76,  757 => 309,  727 => 293,  716 => 226,  670 => 204,  528 => 187,  476 => 213,  435 => 177,  354 => 138,  341 => 109,  192 => 64,  321 => 89,  243 => 85,  793 => 266,  780 => 247,  758 => 229,  700 => 193,  686 => 294,  652 => 185,  638 => 269,  620 => 216,  545 => 243,  523 => 169,  494 => 227,  459 => 156,  438 => 191,  351 => 123,  347 => 122,  402 => 164,  268 => 71,  430 => 136,  411 => 130,  379 => 138,  322 => 80,  315 => 96,  289 => 101,  284 => 74,  255 => 66,  234 => 48,  1133 => 444,  1124 => 455,  1121 => 430,  1116 => 405,  1113 => 429,  1108 => 376,  1103 => 444,  1098 => 401,  1081 => 320,  1073 => 432,  1067 => 314,  1064 => 330,  1061 => 421,  1053 => 318,  1049 => 307,  1042 => 313,  1039 => 384,  1025 => 304,  1021 => 310,  1015 => 308,  1008 => 461,  996 => 406,  989 => 454,  985 => 395,  981 => 296,  977 => 321,  970 => 360,  966 => 359,  955 => 293,  952 => 464,  943 => 299,  936 => 353,  930 => 289,  919 => 314,  917 => 348,  908 => 346,  905 => 363,  896 => 275,  891 => 338,  877 => 334,  862 => 333,  857 => 271,  837 => 261,  832 => 260,  827 => 349,  821 => 266,  803 => 179,  778 => 314,  769 => 253,  765 => 297,  753 => 241,  746 => 244,  743 => 297,  735 => 295,  730 => 251,  720 => 305,  717 => 362,  712 => 251,  691 => 219,  678 => 275,  654 => 199,  587 => 191,  576 => 171,  539 => 200,  517 => 210,  471 => 125,  441 => 162,  437 => 138,  418 => 132,  386 => 152,  373 => 120,  304 => 108,  270 => 92,  265 => 99,  229 => 91,  477 => 167,  455 => 125,  448 => 143,  429 => 141,  407 => 120,  399 => 163,  389 => 123,  375 => 148,  358 => 99,  349 => 137,  335 => 118,  327 => 106,  298 => 98,  280 => 95,  249 => 76,  194 => 81,  142 => 42,  344 => 121,  318 => 113,  306 => 102,  295 => 64,  357 => 110,  300 => 118,  286 => 88,  276 => 100,  269 => 97,  254 => 100,  128 => 40,  237 => 72,  165 => 49,  122 => 31,  798 => 256,  770 => 309,  759 => 278,  748 => 298,  731 => 294,  721 => 227,  718 => 301,  708 => 218,  696 => 236,  617 => 204,  590 => 259,  553 => 177,  550 => 157,  540 => 161,  533 => 182,  500 => 171,  493 => 160,  489 => 202,  482 => 198,  467 => 210,  464 => 170,  458 => 139,  452 => 197,  449 => 196,  415 => 152,  382 => 132,  372 => 137,  361 => 100,  356 => 124,  339 => 120,  302 => 94,  285 => 104,  258 => 67,  123 => 34,  108 => 28,  424 => 130,  394 => 109,  380 => 2,  338 => 135,  319 => 79,  316 => 113,  312 => 115,  290 => 106,  267 => 85,  206 => 84,  110 => 24,  240 => 86,  224 => 58,  219 => 60,  217 => 80,  202 => 82,  186 => 62,  170 => 28,  100 => 27,  67 => 19,  14 => 1,  1096 => 345,  1090 => 339,  1088 => 397,  1085 => 456,  1066 => 423,  1034 => 282,  1031 => 281,  1018 => 303,  1013 => 302,  1007 => 408,  1002 => 403,  993 => 305,  986 => 297,  982 => 401,  976 => 399,  971 => 295,  964 => 255,  949 => 289,  946 => 288,  940 => 388,  937 => 374,  928 => 371,  926 => 318,  915 => 284,  912 => 82,  903 => 231,  898 => 440,  892 => 303,  889 => 277,  887 => 302,  884 => 79,  876 => 274,  874 => 215,  871 => 331,  863 => 345,  861 => 270,  858 => 272,  850 => 330,  843 => 270,  840 => 326,  815 => 264,  812 => 263,  808 => 323,  804 => 258,  799 => 312,  791 => 310,  785 => 262,  775 => 313,  771 => 245,  754 => 267,  728 => 317,  726 => 72,  723 => 238,  715 => 105,  711 => 220,  709 => 222,  706 => 232,  698 => 298,  694 => 199,  692 => 189,  689 => 234,  681 => 224,  677 => 288,  675 => 234,  663 => 213,  661 => 263,  650 => 213,  646 => 231,  629 => 266,  627 => 203,  625 => 266,  622 => 202,  598 => 199,  592 => 192,  586 => 175,  575 => 189,  566 => 251,  556 => 219,  554 => 181,  541 => 178,  536 => 224,  515 => 138,  511 => 208,  509 => 165,  488 => 200,  486 => 145,  483 => 149,  465 => 191,  463 => 153,  450 => 182,  432 => 147,  419 => 178,  371 => 154,  362 => 144,  353 => 98,  337 => 124,  333 => 91,  309 => 84,  303 => 81,  299 => 108,  291 => 103,  272 => 99,  261 => 38,  253 => 96,  239 => 36,  235 => 94,  213 => 58,  200 => 53,  198 => 39,  159 => 46,  149 => 57,  146 => 34,  131 => 51,  116 => 29,  79 => 16,  74 => 45,  71 => 18,  836 => 262,  817 => 243,  814 => 321,  811 => 320,  805 => 244,  787 => 257,  779 => 169,  776 => 232,  773 => 322,  761 => 311,  751 => 302,  747 => 298,  742 => 237,  739 => 296,  736 => 215,  724 => 259,  705 => 69,  702 => 216,  688 => 226,  680 => 205,  667 => 273,  662 => 282,  656 => 215,  649 => 285,  644 => 220,  641 => 220,  624 => 206,  613 => 166,  607 => 200,  597 => 260,  591 => 49,  584 => 236,  579 => 190,  563 => 212,  559 => 183,  551 => 190,  547 => 188,  537 => 160,  524 => 164,  512 => 174,  507 => 237,  504 => 164,  498 => 162,  485 => 158,  480 => 198,  472 => 169,  466 => 165,  460 => 152,  447 => 150,  442 => 162,  434 => 133,  428 => 181,  422 => 134,  404 => 128,  368 => 136,  364 => 144,  340 => 69,  334 => 123,  330 => 48,  325 => 115,  292 => 92,  287 => 101,  282 => 103,  279 => 70,  273 => 100,  266 => 68,  256 => 97,  252 => 87,  228 => 90,  218 => 87,  201 => 70,  64 => 13,  51 => 10,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 847,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 823,  2037 => 789,  2031 => 788,  2028 => 818,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 811,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 779,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 769,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 744,  1801 => 695,  1798 => 694,  1795 => 742,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 708,  1730 => 705,  1720 => 688,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 672,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 659,  1651 => 656,  1641 => 631,  1637 => 667,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 651,  1592 => 614,  1587 => 613,  1584 => 635,  1581 => 731,  1576 => 608,  1574 => 633,  1567 => 603,  1563 => 630,  1559 => 601,  1552 => 628,  1548 => 597,  1542 => 593,  1529 => 605,  1525 => 614,  1516 => 708,  1512 => 583,  1503 => 599,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 569,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 564,  1388 => 537,  1380 => 539,  1376 => 436,  1371 => 645,  1367 => 527,  1364 => 518,  1361 => 523,  1355 => 543,  1342 => 624,  1337 => 510,  1328 => 552,  1324 => 550,  1317 => 511,  1313 => 499,  1310 => 526,  1304 => 504,  1291 => 479,  1286 => 476,  1279 => 486,  1274 => 468,  1270 => 483,  1266 => 487,  1262 => 490,  1257 => 500,  1254 => 460,  1250 => 394,  1246 => 477,  1239 => 389,  1235 => 498,  1232 => 416,  1226 => 383,  1213 => 466,  1208 => 481,  1201 => 443,  1196 => 464,  1192 => 490,  1188 => 456,  1184 => 485,  1179 => 448,  1176 => 461,  1172 => 428,  1168 => 451,  1164 => 450,  1160 => 469,  1153 => 561,  1149 => 359,  1146 => 444,  1137 => 249,  1123 => 442,  1119 => 354,  1115 => 424,  1111 => 377,  1107 => 329,  1102 => 344,  1099 => 347,  1095 => 400,  1091 => 321,  1082 => 455,  1079 => 524,  1076 => 393,  1070 => 431,  1057 => 313,  1052 => 406,  1045 => 484,  1040 => 416,  1036 => 283,  1032 => 415,  1028 => 312,  1023 => 374,  1020 => 311,  1016 => 266,  1012 => 390,  1005 => 370,  1001 => 304,  998 => 308,  992 => 406,  979 => 297,  974 => 400,  967 => 399,  962 => 397,  958 => 383,  954 => 293,  950 => 292,  945 => 391,  942 => 290,  938 => 375,  934 => 284,  927 => 288,  923 => 382,  920 => 369,  910 => 365,  901 => 342,  897 => 273,  890 => 271,  886 => 270,  883 => 353,  868 => 273,  856 => 293,  853 => 341,  849 => 264,  845 => 329,  841 => 249,  835 => 268,  830 => 333,  826 => 282,  822 => 281,  818 => 265,  813 => 242,  810 => 290,  806 => 261,  802 => 339,  795 => 241,  792 => 335,  789 => 249,  784 => 286,  782 => 237,  777 => 255,  772 => 289,  768 => 81,  763 => 327,  760 => 305,  756 => 248,  752 => 247,  745 => 245,  741 => 218,  738 => 241,  732 => 171,  719 => 288,  714 => 251,  710 => 224,  704 => 222,  699 => 279,  695 => 66,  690 => 226,  687 => 210,  683 => 271,  679 => 223,  672 => 179,  668 => 264,  665 => 283,  658 => 178,  645 => 253,  640 => 227,  634 => 206,  628 => 174,  623 => 217,  619 => 78,  611 => 282,  606 => 234,  603 => 231,  599 => 229,  595 => 193,  583 => 192,  580 => 256,  573 => 255,  560 => 249,  543 => 175,  538 => 174,  534 => 189,  530 => 213,  526 => 170,  521 => 287,  518 => 194,  514 => 230,  510 => 154,  503 => 133,  496 => 202,  490 => 159,  484 => 128,  474 => 174,  470 => 211,  446 => 137,  440 => 130,  436 => 176,  431 => 135,  425 => 135,  416 => 168,  412 => 117,  408 => 167,  403 => 161,  400 => 119,  396 => 126,  392 => 143,  385 => 97,  381 => 150,  367 => 112,  363 => 79,  359 => 125,  355 => 76,  350 => 143,  346 => 73,  343 => 140,  328 => 116,  324 => 118,  313 => 112,  307 => 111,  301 => 119,  288 => 105,  283 => 88,  271 => 86,  257 => 79,  251 => 100,  238 => 93,  233 => 69,  195 => 51,  191 => 64,  187 => 63,  183 => 54,  130 => 38,  88 => 24,  76 => 29,  115 => 58,  95 => 25,  655 => 212,  651 => 275,  648 => 215,  637 => 210,  633 => 197,  621 => 462,  618 => 241,  615 => 264,  604 => 201,  600 => 233,  588 => 206,  585 => 222,  582 => 188,  571 => 187,  567 => 194,  555 => 248,  552 => 171,  549 => 245,  544 => 179,  542 => 242,  535 => 237,  531 => 159,  519 => 167,  516 => 218,  513 => 154,  508 => 207,  506 => 188,  499 => 209,  495 => 150,  491 => 146,  481 => 215,  478 => 171,  475 => 155,  469 => 182,  456 => 140,  451 => 139,  443 => 194,  439 => 178,  427 => 155,  423 => 142,  420 => 141,  409 => 140,  405 => 218,  401 => 127,  391 => 159,  387 => 133,  384 => 138,  378 => 131,  365 => 153,  360 => 125,  348 => 97,  336 => 94,  332 => 129,  329 => 119,  323 => 116,  310 => 110,  305 => 111,  277 => 87,  274 => 94,  263 => 105,  259 => 66,  247 => 84,  244 => 76,  241 => 62,  222 => 60,  210 => 57,  207 => 49,  204 => 63,  184 => 71,  181 => 77,  167 => 48,  157 => 35,  96 => 25,  421 => 143,  417 => 150,  414 => 145,  406 => 139,  398 => 159,  393 => 99,  390 => 134,  376 => 149,  369 => 148,  366 => 117,  352 => 128,  345 => 132,  342 => 126,  331 => 108,  326 => 68,  320 => 114,  317 => 114,  314 => 86,  311 => 105,  308 => 111,  297 => 93,  293 => 104,  281 => 101,  278 => 93,  275 => 39,  264 => 92,  260 => 80,  248 => 54,  245 => 63,  242 => 72,  231 => 70,  227 => 88,  215 => 86,  212 => 77,  209 => 73,  197 => 67,  177 => 57,  171 => 49,  161 => 46,  132 => 61,  121 => 45,  105 => 19,  99 => 51,  81 => 43,  77 => 16,  180 => 47,  176 => 44,  156 => 30,  143 => 24,  139 => 37,  118 => 38,  189 => 80,  185 => 47,  173 => 76,  166 => 38,  152 => 40,  174 => 59,  164 => 74,  154 => 41,  150 => 68,  137 => 33,  133 => 43,  127 => 44,  107 => 26,  102 => 23,  83 => 23,  78 => 16,  53 => 13,  23 => 3,  42 => 11,  138 => 36,  134 => 36,  109 => 42,  103 => 26,  97 => 22,  94 => 25,  84 => 17,  75 => 24,  69 => 24,  66 => 14,  54 => 12,  44 => 11,  230 => 80,  226 => 80,  203 => 75,  193 => 72,  188 => 75,  182 => 46,  178 => 42,  168 => 39,  163 => 47,  160 => 72,  155 => 45,  148 => 48,  145 => 52,  140 => 46,  136 => 63,  125 => 34,  120 => 33,  113 => 45,  101 => 37,  92 => 19,  89 => 22,  85 => 21,  73 => 21,  62 => 14,  59 => 15,  56 => 15,  41 => 9,  126 => 33,  119 => 59,  111 => 34,  106 => 41,  98 => 63,  93 => 25,  86 => 19,  70 => 16,  60 => 13,  28 => 6,  36 => 9,  114 => 41,  104 => 29,  91 => 34,  80 => 21,  63 => 20,  58 => 12,  40 => 10,  34 => 7,  45 => 7,  61 => 11,  55 => 12,  48 => 12,  39 => 7,  35 => 8,  31 => 7,  26 => 4,  21 => 2,  46 => 10,  29 => 6,  57 => 16,  50 => 11,  47 => 11,  38 => 10,  33 => 5,  49 => 11,  32 => 8,  246 => 94,  236 => 93,  232 => 91,  225 => 63,  221 => 78,  216 => 53,  214 => 74,  211 => 64,  208 => 72,  205 => 55,  199 => 48,  196 => 77,  190 => 49,  179 => 58,  175 => 50,  172 => 70,  169 => 35,  162 => 48,  158 => 47,  153 => 69,  151 => 44,  147 => 43,  144 => 46,  141 => 65,  135 => 39,  129 => 34,  124 => 35,  117 => 32,  112 => 31,  90 => 31,  87 => 29,  82 => 17,  72 => 15,  68 => 21,  65 => 30,  52 => 13,  43 => 9,  37 => 8,  30 => 7,  27 => 3,  25 => 4,  24 => 4,  22 => 1,  19 => 1,);
    }
}
