<?php

/* AgentBundle:Ticket:ticket-log-actiontext.html.twig */
class __TwigTemplate_708737c328ec2cb425d165fb8597aa37 extends \Application\DeskPRO\Twig\Template
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
        // line 6
        if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
        if (isset($context["hide_unknow"])) { $_hide_unknow_ = $context["hide_unknow"]; } else { $_hide_unknow_ = null; }
        if (($this->getAttribute($_log_, "action_type") == "free")) {
            // line 7
            echo "\t";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "message"), "html", null, true);
            echo "
";
        } elseif (($this->getAttribute($_log_, "action_type") == "action_starter")) {
            // line 9
            echo "\t";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if (($this->getAttribute($this->getAttribute($_log_, "details"), "event") == "new.email.user")) {
                // line 10
                echo "\t\t";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_new_email_user");
                echo "
\t";
            } elseif (($this->getAttribute($this->getAttribute($_log_, "details"), "event") == "new.web.user.portal")) {
                // line 12
                echo "\t\t";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_new_web_user_portal");
                echo "
\t";
            } elseif (($this->getAttribute($this->getAttribute($_log_, "details"), "event") == "new.web.user.embed")) {
                // line 14
                echo "\t\t";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_new_web_user_embed");
                echo "
\t";
            } elseif (($this->getAttribute($this->getAttribute($_log_, "details"), "event") == "new.web.user.widget")) {
                // line 16
                echo "\t\t";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_new_web_user_widget");
                echo "
\t";
            } elseif (($this->getAttribute($this->getAttribute($_log_, "details"), "event") == "new.email.agent")) {
                // line 18
                echo "\t\t";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_new_email_agent");
                echo "
\t";
            } elseif (($this->getAttribute($this->getAttribute($_log_, "details"), "event") == "new.web.agent.portal")) {
                // line 20
                echo "\t\t";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_new_web_agent_portal");
                echo "
\t";
            } elseif (($this->getAttribute($this->getAttribute($_log_, "details"), "event") == "new.web.api")) {
                // line 22
                echo "\t\t";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_new_email_api");
                echo "
\t";
            } elseif (($this->getAttribute($this->getAttribute($_log_, "details"), "event") == "update.agent")) {
                // line 24
                echo "\t\t";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                if ($this->getAttribute($this->getAttribute($_log_, "details"), "has_reply")) {
                    // line 25
                    echo "\t\t\t";
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_update_agent_reply");
                    echo "
\t\t";
                } else {
                    // line 27
                    echo "\t\t\t";
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_update_agent");
                    echo "
\t\t";
                }
                // line 29
                echo "\t";
            } elseif (($this->getAttribute($this->getAttribute($_log_, "details"), "event") == "update.user")) {
                // line 30
                echo "\t\t";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                if ($this->getAttribute($this->getAttribute($_log_, "details"), "has_reply")) {
                    // line 31
                    echo "\t\t\t";
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_update_user_reply");
                    echo "
\t\t";
                } else {
                    // line 33
                    echo "\t\t\t";
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_update_user");
                    echo "
\t\t";
                }
                // line 35
                echo "\t";
            } elseif (($this->getAttribute($this->getAttribute($_log_, "details"), "event") == "update.escalation")) {
                // line 36
                echo "\t\t";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_update_escalation");
                echo "
\t";
            } else {
                // line 38
                echo "\t\t";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "event"), "html", null, true);
                echo "
\t";
            }
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_agent")) {
            // line 41
            echo "\t<span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.assigned_agent");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t<span class=\"old-val\">";
            // line 42
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_agent_name", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_agent_name"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.unassigned"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.unassigned"))), "html", null, true);
            echo "</span>
    ";
            // line 43
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_agent_name", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_agent_name"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.unassigned"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.unassigned"))), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_agent_team")) {
            // line 45
            echo "\t<span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.assigned_team");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t<span class=\"old-val\">";
            // line 46
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_agent_team_name", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_agent_team_name"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span>
    ";
            // line 47
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_agent_team_name", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_agent_team_name"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "participant_added")) {
            // line 49
            echo "\t<span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.participant_added");
            echo "</span>: <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "email"), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "participant_removed")) {
            // line 51
            echo "\t<span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.participant_removed");
            echo "</span>: <span class=\"old-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "email"), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "ticket_created")) {
            // line 53
            echo "\t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ticket");
            echo " <span class=\"new-val\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.id");
            echo " ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "ticket_id"), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.lc_created");
            echo "
\t";
            // line 54
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ($this->getAttribute($this->getAttribute($_log_, "details"), "via_comment")) {
                // line 55
                echo "\t\t<div stye=\"font-size:11px;\">
\t\t\t&bull;
\t\t\t";
                // line 57
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                if ($this->getAttribute($this->getAttribute($this->getAttribute($_log_, "details"), "via_comment"), "comment_type")) {
                    // line 58
                    echo "\t\t\t\t";
                    if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_created_via_comment", array("id" => $this->getAttribute($this->getAttribute($this->getAttribute($_log_, "details"), "via_comment"), "comment_id")));
                    echo "
\t\t\t";
                }
                // line 60
                echo "\t\t\t";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                if (($this->getAttribute($this->getAttribute($this->getAttribute($_log_, "details"), "via_comment"), "comment_type") == "articles")) {
                    // line 61
                    echo "\t\t\t\t";
                    if (isset($context["details"])) { $_details_ = $context["details"]; } else { $_details_ = null; }
                    if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_on_article", array("title" => $this->getAttribute($this->getAttribute($_details_, "via_comment"), "comment_content_title"), "id" => $this->getAttribute($this->getAttribute($this->getAttribute($_log_, "details"), "via_comment"), "comment_content_id")));
                    echo "
\t\t\t";
                } elseif (($this->getAttribute($this->getAttribute($this->getAttribute($_log_, "details"), "via_comment"), "comment_type") == "downloads")) {
                    // line 63
                    echo "\t\t\t\t";
                    if (isset($context["details"])) { $_details_ = $context["details"]; } else { $_details_ = null; }
                    if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_on_download", array("title" => $this->getAttribute($this->getAttribute($_details_, "via_comment"), "comment_content_title"), "id" => $this->getAttribute($this->getAttribute($this->getAttribute($_log_, "details"), "via_comment"), "comment_content_id")));
                    echo "
\t\t\t";
                } elseif (($this->getAttribute($this->getAttribute($this->getAttribute($_log_, "details"), "via_comment"), "comment_type") == "news")) {
                    // line 65
                    echo "\t\t\t\t";
                    if (isset($context["details"])) { $_details_ = $context["details"]; } else { $_details_ = null; }
                    if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_on_news", array("title" => $this->getAttribute($this->getAttribute($_details_, "via_comment"), "comment_content_title"), "id" => $this->getAttribute($this->getAttribute($this->getAttribute($_log_, "details"), "via_comment"), "comment_content_id")));
                    echo "
\t\t\t";
                } elseif (($this->getAttribute($this->getAttribute($this->getAttribute($_log_, "details"), "via_comment"), "comment_type") == "feedback")) {
                    // line 67
                    echo "\t\t\t\t";
                    if (isset($context["details"])) { $_details_ = $context["details"]; } else { $_details_ = null; }
                    if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_on_feedback", array("title" => $this->getAttribute($this->getAttribute($_details_, "via_comment"), "comment_content_title"), "id" => $this->getAttribute($this->getAttribute($this->getAttribute($_log_, "details"), "via_comment"), "comment_content_id")));
                    echo "
\t\t\t";
                }
                // line 69
                echo "\t\t</div>
\t";
            }
        } elseif (($this->getAttribute($_log_, "action_type") == "subject")) {
            // line 72
            echo "\t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.subject_changed_from");
            echo " <span class=\"old-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "old_subject"), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "new_subject"), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "ticket_split")) {
            // line 74
            echo "    ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.ticket_split_from");
            echo " <span class=\"old-val\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.id");
            echo " ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "from_ticket_id"), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "ticket_split_to")) {
            // line 76
            echo "    ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.ticket_split_to");
            echo " <span class=\"old-val\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.id");
            echo " ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "to_ticket_id"), "html", null, true);
            echo "</span> (";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.x_messages_moved", array("count" => (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "messages_moved", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "messages_moved"), 0)) : (0))));
            echo ")
";
        } elseif (($this->getAttribute($_log_, "action_type") == "merged")) {
            // line 78
            echo "\t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ticket");
            echo " <span class=\"old-val\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.id");
            echo " ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_log_, "id_before"), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.merged_into_this");
            echo "
\t";
            // line 79
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ((!twig_test_empty($this->getAttribute($this->getAttribute($_log_, "details"), "lost")))) {
                // line 80
                echo "\t\t<ul>
\t\t";
                // line 81
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_log_, "details"), "lost"));
                foreach ($context['_seq'] as $context["type"] => $context["data"]) {
                    // line 82
                    echo "\t\t\t";
                    if (isset($context["type"])) { $_type_ = $context["type"]; } else { $_type_ = null; }
                    if (($_type_ == "fields")) {
                        // line 83
                        echo "\t\t\t\t";
                        if (isset($context["data"])) { $_data_ = $context["data"]; } else { $_data_ = null; }
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($_data_);
                        foreach ($context['_seq'] as $context["_key"] => $context["field_data"]) {
                            // line 84
                            echo "\t\t\t\t\t<li>";
                            if (isset($context["field_data"])) { $_field_data_ = $context["field_data"]; } else { $_field_data_ = null; }
                            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.lost_field", array("field" => $this->getAttribute($_field_data_, 0)));
                            echo ": ";
                            if (isset($context["field_data"])) { $_field_data_ = $context["field_data"]; } else { $_field_data_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_field_data_, 1), "html", null, true);
                            echo "</li>
\t\t\t\t";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['field_data'], $context['_parent'], $context['loop']);
                        $context = array_merge($_parent, array_intersect_key($context, $_parent));
                        // line 86
                        echo "\t\t\t";
                    } else {
                        // line 87
                        echo "\t\t\t\t<li>";
                        if (isset($context["type"])) { $_type_ = $context["type"]; } else { $_type_ = null; }
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.lost_field", array("field" => $_type_));
                        echo ": ";
                        if (isset($context["data"])) { $_data_ = $context["data"]; } else { $_data_ = null; }
                        echo twig_escape_filter($this->env, $_data_, "html", null, true);
                        echo "</li>
\t\t\t";
                    }
                    // line 89
                    echo "\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['type'], $context['data'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 90
                echo "\t\t</ul>
\t";
            }
        } elseif (($this->getAttribute($_log_, "action_type") == "merged_message")) {
            // line 93
            echo "    ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.message");
            echo " <span class=\"old-val\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.id");
            echo " ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "message_id"), "html", null, true);
            echo "</span> ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.into_this_from_ticket", array("id" => $this->getAttribute($this->getAttribute($_log_, "details"), "old_ticket_id")));
            echo "
";
        } elseif (($this->getAttribute($_log_, "action_type") == "merged_attach")) {
            // line 95
            echo "    ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.attachment");
            echo " <span class=\"old-val\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.id");
            echo " ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_log_, "id_object"), "html", null, true);
            echo "</span> ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.into_this_from_ticket", array("id" => $this->getAttribute($_log_, "id_before")));
            echo "
";
        } elseif (($this->getAttribute($_log_, "action_type") == "labels_added")) {
            // line 97
            echo "\t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.labels_added");
            echo ": <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->implodeArray($this->getAttribute($this->getAttribute($_log_, "details"), "labels"), ", "), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "labels_removed")) {
            // line 99
            echo "\t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.labels_removed");
            echo ": <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->implodeArray($this->getAttribute($this->getAttribute($_log_, "details"), "labels"), ", "), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_category")) {
            // line 101
            echo "\t<span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.category");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t<span class=\"old-val\">";
            // line 102
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_category_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_category_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_category_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_category_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_department")) {
            // line 104
            echo "\t<span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.department");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t<span class=\"old-val\">";
            // line 105
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_department_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_department_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_department_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_department_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_language")) {
            // line 107
            echo "\t<span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.language");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t<span class=\"old-val\">";
            // line 108
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_language_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_language_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_language_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_language_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_organization")) {
            // line 110
            echo "\t<span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.organization");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t<span class=\"old-val\">";
            // line 111
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_org_name", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_org_name"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_org_name", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_org_name"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_subject")) {
            // line 113
            echo "\t<span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.subject");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t<span class=\"old-val\">";
            // line 114
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_subject", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_subject"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_subject", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_subject"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_person")) {
            // line 116
            echo "\t<span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.api.user_owner");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t<span class=\"old-val\">";
            // line 117
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.id");
            echo " ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "old_person_id"), "html", null, true);
            echo " ";
            if (isset($context["old_person_name"])) { $_old_person_name_ = $context["old_person_name"]; } else { $_old_person_name_ = null; }
            echo twig_escape_filter($this->env, $_old_person_name_, "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span class=\"new-val\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.id");
            echo " ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "new_person_id"), "html", null, true);
            echo " ";
            if (isset($context["new_person_name"])) { $_new_person_name_ = $context["new_person_name"]; } else { $_new_person_name_ = null; }
            echo twig_escape_filter($this->env, $_new_person_name_, "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "executed_triggers")) {
            // line 119
            echo "\t<span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ticket_triggers");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.applied");
            echo ":
\t<span class=\"new-val\">";
            // line 120
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "trigger_titles"), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "executed_escalations")) {
            // line 122
            echo "\t<span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ticket_escalations");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.applied");
            echo ":
\t<span class=\"new-val\">";
            // line 123
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "trigger_titles"), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "message_created")) {
            // line 125
            echo "\t<span style=\"";
            if (isset($context["layout"])) { $_layout_ = $context["layout"]; } else { $_layout_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_layout_, "actiontext_style", array(0 => "type"), "method"), "html", null, true);
            echo "\">
\t\t";
            // line 126
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ($this->getAttribute($this->getAttribute($_log_, "details"), "is_agent_note")) {
                // line 127
                echo "\t\t\t";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.new_note");
                echo "
\t\t";
            } else {
                // line 129
                echo "\t\t\t";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.new_message");
                echo "
\t\t";
            }
            // line 131
            echo "\t</span>
\t";
            // line 132
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ($this->getAttribute($this->getAttribute($_log_, "details"), "message_id")) {
                echo "<span class=\"new-val message-id-txt\" data-message-id=\"";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "message_id"), "html", null, true);
                echo "\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.id");
                echo " ";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "message_id"), "html", null, true);
                echo "</span>";
            }
            // line 133
            echo "\t";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ($this->getAttribute($this->getAttribute($_log_, "details"), "ip_address")) {
                // line 134
                echo "    ";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.from_web", array("address" => $this->getAttribute($this->getAttribute($_log_, "details"), "ip_address")));
                echo "
\t";
            }
            // line 136
            echo "\t";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ($this->getAttribute($this->getAttribute($_log_, "details"), "email")) {
                // line 137
                echo "    ";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.from_email", array("address" => $this->getAttribute($this->getAttribute($_log_, "details"), "email")));
                echo "
\t";
            }
            // line 139
            echo "\t&mdash;
\t<a href=\"";
            // line 140
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_ticket_message_window", array("ticket_id" => $this->getAttribute($_ticket_, "id"), "message_id" => $this->getAttribute($this->getAttribute($_log_, "details"), "message_id"), "_rt" => $this->env->getExtension('deskpro_templating')->securityToken("request_token", 10800))), "html", null, true);
            echo "\" onclick=\"window.open(\$(this).attr('href'), 'msgwin', 'status=0,toolbar=0,location=0,menubar=0,directories=0,resizable=1,scrollbars=1,height=600,width=780'); return false;\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.view");
            echo " <i class=\"icon-external-link\" style=\"font-size: 11px;\"></i></a>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "message_removed")) {
            // line 142
            echo "\t";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ($this->getAttribute($this->getAttribute($_log_, "details"), "is_agent_note")) {
                // line 143
                echo "\t\t<span class=\"type\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_note_deleted");
                echo "</span>
\t";
            } else {
                // line 145
                echo "\t\t<span class=\"type\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_message_deleted");
                echo "</span>
\t";
            }
            // line 147
            echo "\t<span class=\"new-val ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ($this->getAttribute($this->getAttribute($_log_, "details"), "old_message")) {
                echo "expand";
            }
            echo "\" data-set=\".set-orig\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.id");
            echo " ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "message_id"), "html", null, true);
            echo "</span>
\t";
            // line 148
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ($this->getAttribute($this->getAttribute($_log_, "details"), "is_agent_message")) {
                // line 149
                echo "\t\t(";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_written_by_agent", array("name" => $this->getAttribute($this->getAttribute($_log_, "details"), "person_name")));
                echo ")
\t";
            } else {
                // line 151
                echo "\t\t(";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_written_by", array("name" => $this->getAttribute($this->getAttribute($_log_, "details"), "person_name"), "id" => $this->getAttribute($this->getAttribute($_log_, "details"), "person_id")));
                echo ")
\t";
            }
            // line 153
            echo "\t";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ($this->getAttribute($this->getAttribute($_log_, "details"), "old_message")) {
                // line 154
                echo "\t\t<div class=\"expand-set set-orig\" style=\"display: none\">
\t\t\t<div style=\"width: 95%; max-height: 200px; overflow: auto; font-size: 11px;\">";
                // line 155
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_message_old_message");
                echo ":<br/>
\t\t\t\t<textarea style=\"width: 80%; height: 150px; font-family: Consolas, Monaco, monospace;\">";
                // line 156
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "old_message"), "html", null, true);
                echo "</textarea>
\t\t\t</div>
\t\t</div>
\t";
            }
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_priority")) {
            // line 161
            echo "\t<span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.priority");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t<span class=\"old-val\">";
            // line 162
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_priority_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_priority_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_priority_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_priority_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_workflow")) {
            // line 164
            echo "\t<span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.workflow");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t<span class=\"old-val\">";
            // line 165
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_workflow_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_workflow_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_workflow_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_workflow_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_urgency")) {
            // line 167
            echo "\t<span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.urgency");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t<span class=\"old-val\">";
            // line 168
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "old_urgency"), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "new_urgency"), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_product")) {
            // line 170
            echo "\t<span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.product");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t<span class=\"old-val\">";
            // line 171
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_product_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "old_product_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_product_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "new_product_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "attach_added")) {
            // line 173
            echo "\t<span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.new_attachment");
            echo "</span> <span class=\"new-val\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.id");
            echo " ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "attach_id"), "html", null, true);
            echo " ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "filename"), "html", null, true);
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "attach_removed")) {
            // line 175
            echo "\t<span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.attachment_deleted");
            echo "</span> <span class=\"old-val\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.id");
            echo " ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "old_attach_id"), "html", null, true);
            echo " ";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "filename"), "html", null, true);
            echo "</span>
\t";
            // line 176
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ($this->getAttribute($this->getAttribute($_log_, "details"), "message_id")) {
                // line 177
                echo "\t\t(<span class=\"old-val\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.id");
                echo " ";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "message_id"), "html", null, true);
                echo "</span>
\t\t";
                // line 178
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                if ($this->getAttribute($this->getAttribute($_log_, "details"), "message_person_name")) {
                    echo " ";
                    if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "message_person_name"), "html", null, true);
                }
                // line 179
                echo "\t";
            }
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_status")) {
            // line 181
            echo "\t<span class=\"type\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.status");
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t";
            // line 182
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ($this->getAttribute($this->getAttribute($_log_, "details"), "old_status")) {
                // line 183
                echo "\t\t";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                $context["phrase_name"] = ("agent.tickets.status_" . strtr($this->getAttribute($this->getAttribute($_log_, "details"), "old_status"), array("." => "_")));
                // line 184
                echo "\t\t<span class=\"old-val\">";
                if (isset($context["phrase_name"])) { $_phrase_name_ = $context["phrase_name"]; } else { $_phrase_name_ = null; }
                if ($this->env->getExtension('deskpro_templating')->hasPhrase($_phrase_name_)) {
                    if (isset($context["phrase_name"])) { $_phrase_name_ = $context["phrase_name"]; } else { $_phrase_name_ = null; }
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, $_phrase_name_);
                } else {
                    if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "old_status"), "html", null, true);
                }
                echo "</span>
\t";
            } else {
                // line 186
                echo "\t\t<span class=\"old-val\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none");
                echo "</span>
\t";
            }
            // line 188
            echo "\t";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            $context["phrase_name"] = ("agent.tickets.status_" . strtr($this->getAttribute($this->getAttribute($_log_, "details"), "new_status"), array("." => "_")));
            // line 189
            echo "\t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span class=\"new-val\">";
            if (isset($context["phrase_name"])) { $_phrase_name_ = $context["phrase_name"]; } else { $_phrase_name_ = null; }
            if ($this->env->getExtension('deskpro_templating')->hasPhrase($_phrase_name_)) {
                if (isset($context["phrase_name"])) { $_phrase_name_ = $context["phrase_name"]; } else { $_phrase_name_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, $_phrase_name_);
            } else {
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "new_status"), "html", null, true);
            }
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_hold")) {
            // line 191
            echo "\t";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ($this->getAttribute($this->getAttribute($_log_, "details"), "is_hold")) {
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.ticket_put_on_hold");
            } else {
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.ticket_removed_from_hold");
            }
        } elseif (($this->getAttribute($_log_, "action_type") == "changed_custom_field")) {
            // line 193
            echo "\t<span class=\"type\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "field_name"), "html", null, true);
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
            echo "
\t<span class=\"old-val\">";
            // line 194
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "value_before", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "value_before"), "(no value)")) : ("(no value)"));
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
            echo " <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo (($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "value_after", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_log_, "details", array(), "any", false, true), "value_after"), "(no value)")) : ("(no value)"));
            echo "</span>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "ticket_sla_added")) {
            // line 196
            echo "\t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_sla_added");
            echo ": <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "title"), "html", null, true);
            echo "</span>
\t";
            // line 197
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ((($this->getAttribute($this->getAttribute($_log_, "details"), "sla_status") != "ok") || $this->getAttribute($this->getAttribute($_log_, "details"), "is_completed"))) {
                // line 198
                echo "\t\t(";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                if (($this->getAttribute($this->getAttribute($_log_, "details"), "sla_status") != "ok")) {
                    if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                    echo $this->getAttribute($this, "get_sla_status", array(0 => $this->getAttribute($this->getAttribute($_log_, "details"), "sla_status")), "method");
                }
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                if ((($this->getAttribute($this->getAttribute($_log_, "details"), "sla_status") != "ok") && $this->getAttribute($this->getAttribute($_log_, "details"), "is_completed"))) {
                    echo ", ";
                }
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                if ($this->getAttribute($this->getAttribute($_log_, "details"), "is_completed")) {
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.requirements_complete");
                }
                echo ")
\t";
            }
        } elseif (($this->getAttribute($_log_, "action_type") == "ticket_sla_removed")) {
            // line 201
            echo "\t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_sla_removed");
            echo ": <span class=\"new-val\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "title"), "html", null, true);
            echo "</span>
\t";
            // line 202
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ((($this->getAttribute($this->getAttribute($_log_, "details"), "sla_status") != "ok") || $this->getAttribute($this->getAttribute($_log_, "details"), "is_completed"))) {
                // line 203
                echo "\t\t(";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                if (($this->getAttribute($this->getAttribute($_log_, "details"), "sla_status") != "ok")) {
                    if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                    echo $this->getAttribute($this, "get_sla_status", array(0 => $this->getAttribute($this->getAttribute($_log_, "details"), "sla_status")), "method");
                }
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                if ((($this->getAttribute($this->getAttribute($_log_, "details"), "sla_status") != "ok") && $this->getAttribute($this->getAttribute($_log_, "details"), "is_completed"))) {
                    echo ", ";
                }
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                if ($this->getAttribute($this->getAttribute($_log_, "details"), "is_completed")) {
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.requirements_complete");
                }
                echo ")
\t";
            }
        } elseif (($this->getAttribute($_log_, "action_type") == "ticket_sla_updated")) {
            // line 206
            echo "\t";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_sla_updated");
            echo ": <span class=\"type\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "title"), "html", null, true);
            echo "</span>
\t";
            // line 207
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ($this->getAttribute($this->getAttribute($_log_, "details"), "status_changed")) {
                // line 208
                echo "\t\t<span class=\"type\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.status");
                echo "</span> ";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
                echo "
\t\t<span class=\"old-val\">";
                // line 209
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo $this->getAttribute($this, "get_sla_status", array(0 => $this->getAttribute($this->getAttribute($_log_, "details"), "original_status")), "method");
                echo "</span> ";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
                echo " <span class=\"new-val\">";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo $this->getAttribute($this, "get_sla_status", array(0 => $this->getAttribute($this->getAttribute($_log_, "details"), "sla_status")), "method");
                echo "</span>.
\t";
            }
            // line 211
            echo "\t";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if ($this->getAttribute($this->getAttribute($_log_, "details"), "is_completed_changed")) {
                // line 212
                echo "\t\t<span class=\"type\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.requirements");
                echo "</span> ";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_from");
                echo "
\t\t<span class=\"old-val\">";
                // line 213
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo (($this->getAttribute($this->getAttribute($_log_, "details"), "original_is_completed")) ? ("Complete") : ("Incomplete"));
                echo "</span> ";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.changed_to");
                echo " <span class=\"new-val\">";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo (($this->getAttribute($this->getAttribute($_log_, "details"), "is_completed")) ? ("Complete") : ("Incomplete"));
                echo "</span>.
\t";
            }
        } elseif (($this->getAttribute($_log_, "action_type") == "agent_notify")) {
            // line 216
            echo "\t<span class=\"new-val expand\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.count_agents", array("count" => twig_length_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "who_emailed"))));
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.notified_of");
            echo "

\t";
            // line 218
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if (($this->getAttribute($this->getAttribute($_log_, "details"), "type") == "newticket")) {
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.lc_new_ticket");
                echo "
\t";
            } elseif (($this->getAttribute($this->getAttribute($_log_, "details"), "type") == "newreply")) {
                // line 219
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.lc_new_reply");
                echo "
\t";
            } elseif (($this->getAttribute($this->getAttribute($_log_, "details"), "type") == "updated")) {
                // line 220
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.lc_update");
                echo "
\t";
            } else {
                // line 221
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "type"), "html", null, true);
                echo "
\t";
            }
            // line 223
            echo "
\t";
            // line 224
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if (($this->getAttribute($this->getAttribute($_log_, "details"), "from_name") || $this->getAttribute($this->getAttribute($_log_, "details"), "from_email"))) {
                // line 225
                echo "\t\t(";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_sent_from", array("email" => (($this->getAttribute($this->getAttribute($_log_, "details"), "from_name") . " ") . $this->getAttribute($this->getAttribute($_log_, "details"), "from_email"))));
                echo ")
\t";
            }
            // line 227
            echo "
\t<div class=\"expand-set\" style=\"display: none\">
\t\t<ul>
\t\t\t";
            // line 230
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_log_, "details"), "who_emailed"));
            foreach ($context['_seq'] as $context["_key"] => $context["info"]) {
                // line 231
                echo "\t\t\t\t<li>
\t\t\t\t\t";
                // line 232
                if (isset($context["info"])) { $_info_ = $context["info"]; } else { $_info_ = null; }
                if (((!$this->getAttribute($_info_, "person_name")) || ($this->getAttribute($_info_, "person_name") == $this->getAttribute($_info_, "person_email")))) {
                    // line 233
                    echo "\t\t\t\t\t\t";
                    if (isset($context["info"])) { $_info_ = $context["info"]; } else { $_info_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_info_, "person_email"), "html", null, true);
                    echo "
\t\t\t\t\t";
                } else {
                    // line 235
                    echo "\t\t\t\t\t\t";
                    if (isset($context["info"])) { $_info_ = $context["info"]; } else { $_info_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_info_, "person_name"), "html", null, true);
                    echo " &lt;";
                    if (isset($context["info"])) { $_info_ = $context["info"]; } else { $_info_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_info_, "person_email"), "html", null, true);
                    echo "&gt;
\t\t\t\t\t";
                }
                // line 237
                echo "
\t\t\t\t\t";
                // line 238
                if (isset($context["info"])) { $_info_ = $context["info"]; } else { $_info_ = null; }
                if ($this->getAttribute($_info_, "info")) {
                    // line 239
                    echo "\t\t\t\t\t\t";
                    if (isset($context["info"])) { $_info_ = $context["info"]; } else { $_info_ = null; }
                    if ($this->getAttribute($this->getAttribute($_info_, "info"), "filters")) {
                        echo "(";
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_sub_on_filter");
                        echo ": ";
                        if (isset($context["info"])) { $_info_ = $context["info"]; } else { $_info_ = null; }
                        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->implodeArray($this->getAttribute($this->getAttribute($_info_, "info"), "filters"), ", "), "html", null, true);
                        echo ")
\t\t\t\t\t\t";
                    } elseif ($this->getAttribute($this->getAttribute($_info_, "info"), "is_via_trigger")) {
                        // line 240
                        echo "(";
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_trigger_forced_notif");
                        echo ")
\t\t\t\t\t\t";
                    }
                    // line 242
                    echo "\t\t\t\t\t";
                }
                // line 243
                echo "\t\t\t\t</li>
\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['info'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 245
            echo "\t\t</ul>
\t</div>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "user_notify")) {
            // line 248
            echo "\t<span class=\"new-val expand\" data-set=\".set-emailed\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.count_users", array("count" => twig_length_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "who_emailed"))));
            echo "</span> ";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.notified_of");
            echo "

\t";
            // line 250
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if (($this->getAttribute($this->getAttribute($_log_, "details"), "type") == "newticket")) {
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.lc_new_ticket");
                echo "
\t";
            } elseif (($this->getAttribute($this->getAttribute($_log_, "details"), "type") == "newticket_agent")) {
                // line 251
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.lc_new_ticket_agent");
                echo "
\t";
            } elseif (($this->getAttribute($this->getAttribute($_log_, "details"), "type") == "newreply")) {
                // line 252
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.lc_new_reply");
                echo "
\t";
            } elseif ($this->getAttribute($this->getAttribute($_log_, "details"), "message")) {
                // line 253
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo twig_escape_filter($this->env, strtr($this->getAttribute($this->getAttribute($_log_, "details"), "message"), array("DeskPRO:emails_user:custom_" => "")), "html", null, true);
                echo "
\t";
            } else {
                // line 254
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "type"), "html", null, true);
                echo "
\t";
            }
            // line 256
            echo "
\t";
            // line 257
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if (twig_length_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "who_cced"))) {
                // line 258
                echo "        ";
                ob_start();
                // line 259
                echo "            <span class=\"new-val expand\" data-set=\".set-cced\">";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.count_users", array("count" => twig_length_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "who_cced"))));
                echo "</span>
        ";
                $context["phrase_part"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
                // line 261
                echo "        ";
                if (isset($context["phrase_part"])) { $_phrase_part_ = $context["phrase_part"]; } else { $_phrase_part_ = null; }
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.and_count_were_cced", array("display_count" => $_phrase_part_, "count" => twig_length_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "who_cced"))), true);
                echo "
\t";
            }
            // line 263
            echo "
\t";
            // line 264
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            if (($this->getAttribute($this->getAttribute($_log_, "details"), "from_name") || $this->getAttribute($this->getAttribute($_log_, "details"), "from_email"))) {
                // line 265
                echo "\t\t(";
                if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_sent_from", array("email" => (($this->getAttribute($this->getAttribute($_log_, "details"), "from_name") . " ") . $this->getAttribute($this->getAttribute($_log_, "details"), "from_email"))));
                echo ")
\t";
            }
            // line 267
            echo "
\t<div class=\"expand-set set-emailed\" style=\"display: none\">
\t\t<ul>
\t\t\t";
            // line 270
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_log_, "details"), "who_emailed"));
            foreach ($context['_seq'] as $context["_key"] => $context["info"]) {
                // line 271
                echo "\t\t\t\t<li>
\t\t\t\t\t";
                // line 272
                if (isset($context["info"])) { $_info_ = $context["info"]; } else { $_info_ = null; }
                if (((!$this->getAttribute($_info_, "person_name")) || ($this->getAttribute($_info_, "person_name") == $this->getAttribute($_info_, "person_email")))) {
                    // line 273
                    echo "\t\t\t\t\t\t";
                    if (isset($context["info"])) { $_info_ = $context["info"]; } else { $_info_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_info_, "person_email"), "html", null, true);
                    echo "
\t\t\t\t\t";
                } else {
                    // line 275
                    echo "\t\t\t\t\t\t";
                    if (isset($context["info"])) { $_info_ = $context["info"]; } else { $_info_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_info_, "person_name"), "html", null, true);
                    echo " &lt;";
                    if (isset($context["info"])) { $_info_ = $context["info"]; } else { $_info_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_info_, "person_email"), "html", null, true);
                    echo "&gt;
\t\t\t\t\t";
                }
                // line 277
                echo "\t\t\t\t</li>
\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['info'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 279
            echo "\t\t</ul>
\t</div>
\t<div class=\"expand-set set-cced\" style=\"display: none\">
\t\t<ul>
\t\t\t";
            // line 283
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_log_, "details"), "who_cced"));
            foreach ($context['_seq'] as $context["_key"] => $context["info"]) {
                // line 284
                echo "\t\t\t\t<li>
\t\t\t\t\t";
                // line 285
                if (isset($context["info"])) { $_info_ = $context["info"]; } else { $_info_ = null; }
                if (((!$this->getAttribute($_info_, "person_name")) || ($this->getAttribute($_info_, "person_name") == $this->getAttribute($_info_, "person_email")))) {
                    // line 286
                    echo "\t\t\t\t\t\t";
                    if (isset($context["info"])) { $_info_ = $context["info"]; } else { $_info_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_info_, "person_email"), "html", null, true);
                    echo "
\t\t\t\t\t";
                } else {
                    // line 288
                    echo "\t\t\t\t\t\t";
                    if (isset($context["info"])) { $_info_ = $context["info"]; } else { $_info_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_info_, "person_name"), "html", null, true);
                    echo " &lt;";
                    if (isset($context["info"])) { $_info_ = $context["info"]; } else { $_info_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_info_, "person_email"), "html", null, true);
                    echo "&gt;
\t\t\t\t\t";
                }
                // line 290
                echo "\t\t\t\t</li>
\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['info'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 292
            echo "\t\t</ul>
\t</div>
";
        } elseif (($this->getAttribute($_log_, "action_type") == "message_edit")) {
            // line 295
            echo "\t<span class=\"new-val expand\" data-set=\".set-orig\">";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_message_updated", array("id" => $this->getAttribute($this->getAttribute($_log_, "details"), "message_id")));
            echo "</span>
\t<div class=\"expand-set set-orig\" style=\"display: none\">
\t\t<div style=\"width: 95%; max-height: 200px; overflow: auto; font-size: 11px;\">";
            // line 297
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.log_message_old_message");
            echo "<br/>
\t\t\t<textarea style=\"width: 80%; height: 150px; font-family: Consolas, Monaco, monospace;\">";
            // line 298
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_log_, "details"), "old_message"), "html", null, true);
            echo "</textarea>
\t\t</div>
\t</div>
";
        } elseif ($_hide_unknow_) {
            // line 302
            echo "\t";
            if (isset($context["log"])) { $_log_ = $context["log"]; } else { $_log_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_log_, "action_type"), "html", null, true);
            echo "
";
        }
    }

    // line 1
    public function getget_sla_status($_status = null)
    {
        $context = $this->env->mergeGlobals(array(
            "status" => $_status,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 2
            if (isset($context["status"])) { $_status_ = $context["status"]; } else { $_status_ = null; }
            if (($_status_ == "ok")) {
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.okay");
            } elseif (($_status_ == "warning")) {
                // line 3
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.warning");
            } elseif (($_status_ == "fail")) {
                // line 4
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.failed");
            }
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    public function getTemplateName()
    {
        return "AgentBundle:Ticket:ticket-log-actiontext.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1330 => 4,  1327 => 3,  1322 => 2,  1290 => 297,  1283 => 295,  1278 => 292,  1271 => 290,  1230 => 277,  1190 => 265,  1166 => 258,  1138 => 251,  642 => 147,  1264 => 464,  1259 => 462,  1227 => 448,  1211 => 446,  1399 => 568,  1387 => 560,  1358 => 555,  1334 => 553,  1316 => 529,  1298 => 520,  1195 => 478,  1148 => 253,  1120 => 453,  1117 => 245,  1093 => 440,  788 => 316,  612 => 203,  1394 => 446,  1385 => 442,  1382 => 441,  1369 => 433,  1363 => 432,  1360 => 431,  1350 => 429,  1305 => 523,  1300 => 409,  1281 => 404,  1277 => 403,  1265 => 398,  1255 => 396,  1247 => 393,  1234 => 387,  1202 => 270,  1199 => 374,  1187 => 264,  1162 => 365,  1136 => 461,  1128 => 352,  1122 => 248,  1069 => 332,  968 => 387,  846 => 188,  1183 => 449,  1132 => 460,  1097 => 341,  957 => 356,  907 => 278,  875 => 263,  653 => 236,  1329 => 405,  1309 => 401,  1303 => 399,  1297 => 398,  1292 => 397,  1280 => 394,  1249 => 458,  1237 => 279,  1205 => 409,  1200 => 408,  1194 => 440,  1178 => 430,  1175 => 447,  1170 => 375,  1135 => 362,  1105 => 428,  1078 => 335,  1068 => 388,  1048 => 417,  961 => 207,  922 => 280,  750 => 245,  842 => 263,  1038 => 319,  904 => 198,  882 => 194,  831 => 267,  860 => 314,  790 => 284,  733 => 230,  707 => 283,  744 => 220,  873 => 349,  824 => 267,  762 => 243,  713 => 225,  578 => 158,  2739 => 1153,  2735 => 1152,  2732 => 1151,  2725 => 1146,  2712 => 1144,  2707 => 1143,  2692 => 1131,  2688 => 1130,  2683 => 1127,  2680 => 1126,  2677 => 1125,  2671 => 1121,  2658 => 1119,  2653 => 1118,  2645 => 1113,  2641 => 1112,  2636 => 1109,  2632 => 1108,  2629 => 1107,  2626 => 1106,  2606 => 1089,  2602 => 1088,  2598 => 1087,  2594 => 1086,  2590 => 1085,  2586 => 1084,  2572 => 1073,  2568 => 1072,  2564 => 1071,  2560 => 1070,  2556 => 1069,  2552 => 1068,  2537 => 1056,  2533 => 1055,  2529 => 1054,  2525 => 1053,  2521 => 1052,  2503 => 1040,  2499 => 1039,  2495 => 1038,  2491 => 1037,  2487 => 1036,  2483 => 1035,  2469 => 1024,  2465 => 1023,  2461 => 1022,  2457 => 1021,  2453 => 1020,  2449 => 1019,  2426 => 999,  2422 => 998,  2418 => 997,  2414 => 996,  2410 => 995,  2406 => 994,  2392 => 983,  2388 => 982,  2384 => 981,  2380 => 980,  2376 => 979,  2372 => 978,  2358 => 967,  2354 => 966,  2350 => 965,  2346 => 964,  2342 => 963,  2338 => 962,  2324 => 951,  2320 => 950,  2316 => 949,  2312 => 948,  2308 => 947,  2304 => 946,  2295 => 939,  2282 => 937,  2277 => 936,  2269 => 931,  2265 => 930,  2260 => 927,  2256 => 926,  2253 => 925,  2230 => 901,  2220 => 899,  2216 => 898,  2211 => 896,  2208 => 895,  2198 => 893,  2194 => 892,  2189 => 890,  2184 => 889,  2177 => 885,  2173 => 884,  2164 => 877,  2151 => 875,  2146 => 874,  2143 => 873,  2141 => 864,  2134 => 860,  2130 => 859,  2122 => 853,  2120 => 852,  2113 => 848,  2105 => 846,  2096 => 839,  2083 => 837,  2078 => 836,  2073 => 834,  2067 => 830,  2062 => 828,  2058 => 827,  2054 => 826,  2050 => 825,  2045 => 824,  2038 => 822,  2034 => 821,  2022 => 814,  2009 => 812,  1999 => 809,  1993 => 805,  1988 => 803,  1984 => 802,  1980 => 801,  1976 => 800,  1971 => 799,  1968 => 798,  1964 => 797,  1960 => 796,  1954 => 793,  1946 => 788,  1942 => 787,  1938 => 786,  1932 => 782,  1927 => 780,  1919 => 778,  1915 => 777,  1911 => 776,  1906 => 775,  1903 => 774,  1899 => 773,  1895 => 772,  1886 => 768,  1883 => 764,  1871 => 759,  1866 => 756,  1863 => 755,  1842 => 752,  1825 => 749,  1818 => 748,  1815 => 747,  1800 => 743,  1793 => 741,  1772 => 729,  1761 => 721,  1757 => 720,  1751 => 717,  1740 => 709,  1719 => 697,  1715 => 696,  1686 => 673,  1676 => 669,  1665 => 661,  1661 => 660,  1645 => 651,  1638 => 649,  1630 => 645,  1625 => 642,  1622 => 641,  1598 => 637,  1577 => 634,  1558 => 629,  1549 => 627,  1546 => 626,  1535 => 618,  1531 => 617,  1514 => 606,  1510 => 605,  1504 => 602,  1493 => 594,  1483 => 590,  1464 => 580,  1458 => 577,  1443 => 568,  1437 => 565,  1426 => 557,  1405 => 545,  1401 => 544,  1395 => 541,  1390 => 538,  1381 => 533,  1373 => 531,  1348 => 521,  1343 => 520,  1335 => 515,  1321 => 510,  1318 => 549,  1299 => 503,  1294 => 298,  1282 => 496,  1269 => 466,  1260 => 397,  1240 => 478,  1221 => 484,  1216 => 378,  1210 => 272,  1206 => 445,  1193 => 467,  1189 => 466,  1155 => 468,  1150 => 419,  1022 => 312,  1006 => 299,  988 => 398,  969 => 392,  965 => 294,  921 => 286,  878 => 275,  866 => 349,  854 => 254,  819 => 322,  796 => 286,  1713 => 683,  1700 => 681,  1685 => 674,  1662 => 670,  1643 => 669,  1640 => 668,  1631 => 663,  1618 => 661,  1613 => 660,  1608 => 639,  1605 => 656,  1602 => 655,  1589 => 645,  1586 => 644,  1583 => 643,  1554 => 613,  1547 => 608,  1521 => 602,  1508 => 600,  1499 => 598,  1491 => 593,  1482 => 587,  1472 => 582,  1468 => 581,  1465 => 579,  1457 => 573,  1445 => 566,  1422 => 556,  1416 => 553,  1410 => 548,  1397 => 546,  1392 => 545,  1386 => 541,  1377 => 532,  1370 => 530,  1357 => 528,  1352 => 554,  1341 => 428,  1314 => 510,  1311 => 1,  1275 => 493,  1248 => 284,  1238 => 488,  1225 => 476,  1220 => 275,  1209 => 466,  1185 => 385,  1182 => 431,  1159 => 421,  1154 => 254,  1130 => 438,  1125 => 407,  1101 => 240,  1074 => 432,  1056 => 326,  1046 => 323,  1043 => 225,  1030 => 397,  1027 => 412,  947 => 361,  925 => 352,  913 => 324,  893 => 196,  881 => 267,  847 => 343,  829 => 336,  825 => 259,  1083 => 237,  995 => 399,  984 => 350,  963 => 292,  941 => 354,  851 => 367,  682 => 217,  1365 => 556,  1359 => 545,  1349 => 539,  1336 => 427,  1331 => 424,  1323 => 420,  1319 => 530,  1312 => 402,  1284 => 475,  1272 => 401,  1268 => 509,  1261 => 288,  1251 => 285,  1245 => 483,  1231 => 496,  1207 => 271,  1197 => 267,  1180 => 484,  1173 => 457,  1169 => 259,  1157 => 363,  1147 => 438,  1109 => 330,  1065 => 440,  1059 => 423,  1047 => 385,  1044 => 424,  1033 => 381,  1009 => 357,  991 => 363,  987 => 404,  973 => 294,  931 => 202,  924 => 287,  911 => 347,  906 => 81,  885 => 337,  872 => 335,  855 => 332,  749 => 240,  701 => 221,  594 => 180,  1163 => 257,  1143 => 252,  1087 => 420,  1077 => 433,  1051 => 325,  1037 => 223,  1010 => 301,  999 => 407,  932 => 352,  899 => 306,  895 => 404,  933 => 387,  914 => 133,  909 => 323,  833 => 284,  783 => 315,  755 => 303,  666 => 214,  453 => 168,  639 => 209,  568 => 176,  520 => 232,  657 => 216,  572 => 201,  609 => 202,  20 => 1,  659 => 217,  562 => 164,  548 => 180,  558 => 197,  479 => 157,  589 => 223,  457 => 199,  413 => 174,  953 => 206,  948 => 379,  935 => 394,  929 => 283,  916 => 382,  864 => 365,  844 => 338,  816 => 342,  807 => 318,  801 => 268,  774 => 257,  766 => 312,  737 => 297,  685 => 218,  664 => 225,  635 => 249,  593 => 199,  546 => 201,  532 => 236,  865 => 191,  852 => 241,  838 => 285,  820 => 182,  781 => 327,  764 => 173,  725 => 250,  632 => 268,  602 => 261,  565 => 183,  529 => 119,  505 => 147,  487 => 101,  473 => 212,  1853 => 842,  1849 => 753,  1839 => 751,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 733,  1778 => 732,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 693,  1705 => 781,  1688 => 675,  1681 => 769,  1677 => 768,  1667 => 671,  1656 => 762,  1648 => 655,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 606,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 593,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 540,  1379 => 557,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 302,  1289 => 406,  1276 => 604,  1263 => 601,  1244 => 456,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 373,  1181 => 561,  1171 => 446,  1158 => 550,  1114 => 428,  1104 => 465,  1072 => 333,  1062 => 7,  1024 => 395,  1014 => 218,  1000 => 368,  990 => 303,  980 => 393,  960 => 466,  918 => 285,  888 => 80,  834 => 325,  673 => 64,  636 => 145,  462 => 142,  454 => 108,  1144 => 463,  1139 => 356,  1131 => 250,  1127 => 434,  1110 => 243,  1092 => 459,  1089 => 239,  1086 => 238,  1084 => 337,  1063 => 232,  1060 => 231,  1055 => 230,  1050 => 227,  1035 => 372,  1019 => 330,  1003 => 401,  959 => 387,  900 => 366,  880 => 276,  870 => 277,  867 => 334,  859 => 294,  848 => 271,  839 => 376,  828 => 302,  823 => 183,  809 => 179,  800 => 241,  797 => 267,  794 => 177,  786 => 283,  740 => 78,  734 => 307,  703 => 228,  693 => 297,  630 => 143,  626 => 142,  614 => 139,  610 => 236,  581 => 206,  564 => 127,  525 => 195,  722 => 226,  697 => 282,  674 => 270,  671 => 285,  577 => 180,  569 => 187,  557 => 179,  502 => 187,  497 => 228,  445 => 163,  729 => 306,  684 => 237,  676 => 154,  669 => 268,  660 => 203,  647 => 211,  643 => 229,  601 => 195,  570 => 129,  522 => 156,  501 => 116,  296 => 108,  374 => 115,  631 => 207,  616 => 198,  608 => 194,  605 => 193,  596 => 134,  574 => 180,  561 => 126,  527 => 165,  433 => 183,  388 => 98,  426 => 172,  383 => 105,  461 => 184,  370 => 147,  395 => 166,  294 => 106,  223 => 55,  220 => 67,  492 => 129,  468 => 144,  444 => 149,  410 => 150,  397 => 136,  377 => 121,  262 => 92,  250 => 39,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 1051,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 680,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 638,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 572,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 526,  1301 => 1037,  1252 => 392,  1243 => 283,  1223 => 978,  1212 => 494,  1167 => 366,  1161 => 933,  1145 => 923,  1140 => 413,  1134 => 409,  1126 => 443,  1106 => 528,  1100 => 897,  1026 => 220,  997 => 367,  983 => 298,  975 => 361,  956 => 318,  939 => 285,  902 => 274,  894 => 364,  879 => 76,  757 => 309,  727 => 293,  716 => 226,  670 => 204,  528 => 187,  476 => 213,  435 => 177,  354 => 89,  341 => 86,  192 => 21,  321 => 89,  243 => 67,  793 => 266,  780 => 247,  758 => 229,  700 => 193,  686 => 294,  652 => 185,  638 => 269,  620 => 216,  545 => 243,  523 => 169,  494 => 227,  459 => 156,  438 => 191,  351 => 123,  347 => 122,  402 => 99,  268 => 71,  430 => 136,  411 => 101,  379 => 95,  322 => 83,  315 => 96,  289 => 101,  284 => 74,  255 => 66,  234 => 48,  1133 => 444,  1124 => 455,  1121 => 430,  1116 => 405,  1113 => 429,  1108 => 376,  1103 => 444,  1098 => 401,  1081 => 320,  1073 => 235,  1067 => 314,  1064 => 330,  1061 => 421,  1053 => 318,  1049 => 307,  1042 => 313,  1039 => 384,  1025 => 304,  1021 => 219,  1015 => 308,  1008 => 461,  996 => 406,  989 => 454,  985 => 395,  981 => 296,  977 => 321,  970 => 360,  966 => 359,  955 => 293,  952 => 464,  943 => 299,  936 => 353,  930 => 289,  919 => 314,  917 => 348,  908 => 346,  905 => 363,  896 => 275,  891 => 338,  877 => 334,  862 => 333,  857 => 271,  837 => 261,  832 => 260,  827 => 184,  821 => 266,  803 => 179,  778 => 175,  769 => 253,  765 => 297,  753 => 171,  746 => 170,  743 => 297,  735 => 168,  730 => 251,  720 => 305,  717 => 165,  712 => 251,  691 => 219,  678 => 275,  654 => 199,  587 => 191,  576 => 131,  539 => 200,  517 => 210,  471 => 125,  441 => 162,  437 => 138,  418 => 102,  386 => 152,  373 => 120,  304 => 108,  270 => 74,  265 => 99,  229 => 18,  477 => 167,  455 => 125,  448 => 143,  429 => 104,  407 => 120,  399 => 163,  389 => 123,  375 => 148,  358 => 99,  349 => 137,  335 => 118,  327 => 106,  298 => 98,  280 => 95,  249 => 76,  194 => 81,  142 => 42,  344 => 87,  318 => 82,  306 => 102,  295 => 78,  357 => 110,  300 => 118,  286 => 88,  276 => 100,  269 => 97,  254 => 100,  128 => 41,  237 => 72,  165 => 49,  122 => 31,  798 => 256,  770 => 309,  759 => 278,  748 => 298,  731 => 294,  721 => 227,  718 => 301,  708 => 218,  696 => 236,  617 => 140,  590 => 259,  553 => 177,  550 => 157,  540 => 161,  533 => 182,  500 => 171,  493 => 160,  489 => 202,  482 => 198,  467 => 210,  464 => 170,  458 => 139,  452 => 197,  449 => 196,  415 => 152,  382 => 132,  372 => 137,  361 => 100,  356 => 124,  339 => 120,  302 => 94,  285 => 104,  258 => 40,  123 => 34,  108 => 28,  424 => 130,  394 => 109,  380 => 2,  338 => 135,  319 => 79,  316 => 113,  312 => 115,  290 => 106,  267 => 85,  206 => 84,  110 => 24,  240 => 36,  224 => 33,  219 => 61,  217 => 80,  202 => 82,  186 => 53,  170 => 28,  100 => 27,  67 => 19,  14 => 1,  1096 => 345,  1090 => 339,  1088 => 397,  1085 => 456,  1066 => 233,  1034 => 282,  1031 => 221,  1018 => 303,  1013 => 302,  1007 => 408,  1002 => 403,  993 => 213,  986 => 212,  982 => 211,  976 => 399,  971 => 209,  964 => 208,  949 => 289,  946 => 288,  940 => 388,  937 => 374,  928 => 371,  926 => 318,  915 => 284,  912 => 82,  903 => 231,  898 => 440,  892 => 303,  889 => 277,  887 => 302,  884 => 79,  876 => 274,  874 => 193,  871 => 331,  863 => 345,  861 => 270,  858 => 272,  850 => 189,  843 => 270,  840 => 186,  815 => 264,  812 => 263,  808 => 323,  804 => 258,  799 => 312,  791 => 176,  785 => 262,  775 => 313,  771 => 245,  754 => 267,  728 => 167,  726 => 72,  723 => 238,  715 => 105,  711 => 220,  709 => 222,  706 => 232,  698 => 298,  694 => 199,  692 => 161,  689 => 234,  681 => 224,  677 => 288,  675 => 234,  663 => 213,  661 => 263,  650 => 213,  646 => 231,  629 => 266,  627 => 203,  625 => 266,  622 => 202,  598 => 199,  592 => 133,  586 => 175,  575 => 189,  566 => 251,  556 => 219,  554 => 181,  541 => 178,  536 => 120,  515 => 138,  511 => 208,  509 => 165,  488 => 200,  486 => 145,  483 => 113,  465 => 110,  463 => 153,  450 => 182,  432 => 147,  419 => 178,  371 => 154,  362 => 144,  353 => 98,  337 => 124,  333 => 91,  309 => 84,  303 => 81,  299 => 108,  291 => 103,  272 => 99,  261 => 38,  253 => 96,  239 => 36,  235 => 65,  213 => 14,  200 => 25,  198 => 54,  159 => 46,  149 => 57,  146 => 34,  131 => 51,  116 => 29,  79 => 16,  74 => 45,  71 => 18,  836 => 262,  817 => 243,  814 => 321,  811 => 320,  805 => 244,  787 => 257,  779 => 169,  776 => 232,  773 => 322,  761 => 311,  751 => 302,  747 => 298,  742 => 237,  739 => 296,  736 => 215,  724 => 259,  705 => 69,  702 => 216,  688 => 226,  680 => 205,  667 => 273,  662 => 282,  656 => 215,  649 => 285,  644 => 220,  641 => 220,  624 => 206,  613 => 166,  607 => 137,  597 => 260,  591 => 49,  584 => 236,  579 => 132,  563 => 212,  559 => 183,  551 => 190,  547 => 188,  537 => 160,  524 => 164,  512 => 174,  507 => 237,  504 => 164,  498 => 162,  485 => 158,  480 => 198,  472 => 111,  466 => 165,  460 => 152,  447 => 107,  442 => 162,  434 => 133,  428 => 181,  422 => 134,  404 => 128,  368 => 136,  364 => 144,  340 => 69,  334 => 123,  330 => 48,  325 => 115,  292 => 92,  287 => 101,  282 => 103,  279 => 70,  273 => 48,  266 => 68,  256 => 72,  252 => 87,  228 => 90,  218 => 87,  201 => 55,  64 => 20,  51 => 10,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 847,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 823,  2037 => 789,  2031 => 788,  2028 => 818,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 811,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 779,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 769,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 744,  1801 => 695,  1798 => 694,  1795 => 742,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 708,  1730 => 705,  1720 => 688,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 672,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 659,  1651 => 656,  1641 => 631,  1637 => 667,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 651,  1592 => 614,  1587 => 613,  1584 => 635,  1581 => 731,  1576 => 608,  1574 => 633,  1567 => 603,  1563 => 630,  1559 => 601,  1552 => 628,  1548 => 597,  1542 => 593,  1529 => 605,  1525 => 614,  1516 => 708,  1512 => 583,  1503 => 599,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 569,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 564,  1388 => 537,  1380 => 539,  1376 => 436,  1371 => 645,  1367 => 527,  1364 => 518,  1361 => 523,  1355 => 543,  1342 => 624,  1337 => 510,  1328 => 552,  1324 => 550,  1317 => 511,  1313 => 499,  1310 => 526,  1304 => 504,  1291 => 479,  1286 => 476,  1279 => 486,  1274 => 468,  1270 => 483,  1266 => 487,  1262 => 490,  1257 => 500,  1254 => 286,  1250 => 394,  1246 => 477,  1239 => 389,  1235 => 498,  1232 => 416,  1226 => 383,  1213 => 273,  1208 => 481,  1201 => 443,  1196 => 464,  1192 => 490,  1188 => 456,  1184 => 263,  1179 => 448,  1176 => 261,  1172 => 428,  1168 => 451,  1164 => 450,  1160 => 256,  1153 => 561,  1149 => 359,  1146 => 444,  1137 => 249,  1123 => 442,  1119 => 354,  1115 => 424,  1111 => 377,  1107 => 242,  1102 => 344,  1099 => 347,  1095 => 400,  1091 => 321,  1082 => 455,  1079 => 524,  1076 => 393,  1070 => 431,  1057 => 313,  1052 => 406,  1045 => 484,  1040 => 224,  1036 => 283,  1032 => 415,  1028 => 312,  1023 => 374,  1020 => 311,  1016 => 266,  1012 => 390,  1005 => 216,  1001 => 304,  998 => 308,  992 => 406,  979 => 297,  974 => 400,  967 => 399,  962 => 397,  958 => 383,  954 => 293,  950 => 292,  945 => 391,  942 => 290,  938 => 375,  934 => 203,  927 => 288,  923 => 201,  920 => 369,  910 => 365,  901 => 197,  897 => 273,  890 => 271,  886 => 270,  883 => 353,  868 => 273,  856 => 293,  853 => 341,  849 => 264,  845 => 329,  841 => 249,  835 => 268,  830 => 333,  826 => 282,  822 => 281,  818 => 265,  813 => 181,  810 => 290,  806 => 261,  802 => 178,  795 => 241,  792 => 335,  789 => 249,  784 => 286,  782 => 237,  777 => 255,  772 => 289,  768 => 81,  763 => 327,  760 => 305,  756 => 248,  752 => 247,  745 => 245,  741 => 218,  738 => 241,  732 => 171,  719 => 288,  714 => 251,  710 => 164,  704 => 222,  699 => 162,  695 => 66,  690 => 226,  687 => 210,  683 => 156,  679 => 155,  672 => 153,  668 => 264,  665 => 151,  658 => 149,  645 => 253,  640 => 227,  634 => 206,  628 => 174,  623 => 217,  619 => 78,  611 => 282,  606 => 234,  603 => 136,  599 => 229,  595 => 193,  583 => 192,  580 => 256,  573 => 255,  560 => 249,  543 => 175,  538 => 174,  534 => 189,  530 => 213,  526 => 170,  521 => 287,  518 => 194,  514 => 230,  510 => 154,  503 => 133,  496 => 202,  490 => 114,  484 => 128,  474 => 174,  470 => 211,  446 => 137,  440 => 130,  436 => 105,  431 => 135,  425 => 135,  416 => 168,  412 => 117,  408 => 167,  403 => 161,  400 => 119,  396 => 126,  392 => 143,  385 => 97,  381 => 150,  367 => 112,  363 => 79,  359 => 125,  355 => 76,  350 => 143,  346 => 73,  343 => 140,  328 => 84,  324 => 118,  313 => 81,  307 => 79,  301 => 119,  288 => 105,  283 => 88,  271 => 86,  257 => 79,  251 => 69,  238 => 93,  233 => 69,  195 => 51,  191 => 64,  187 => 20,  183 => 54,  130 => 1,  88 => 24,  76 => 24,  115 => 58,  95 => 30,  655 => 148,  651 => 275,  648 => 215,  637 => 210,  633 => 197,  621 => 462,  618 => 241,  615 => 264,  604 => 201,  600 => 233,  588 => 206,  585 => 222,  582 => 188,  571 => 187,  567 => 194,  555 => 125,  552 => 171,  549 => 123,  544 => 179,  542 => 122,  535 => 237,  531 => 159,  519 => 167,  516 => 218,  513 => 154,  508 => 117,  506 => 188,  499 => 209,  495 => 150,  491 => 146,  481 => 215,  478 => 171,  475 => 155,  469 => 182,  456 => 140,  451 => 139,  443 => 194,  439 => 178,  427 => 155,  423 => 142,  420 => 141,  409 => 140,  405 => 218,  401 => 127,  391 => 159,  387 => 133,  384 => 138,  378 => 131,  365 => 93,  360 => 90,  348 => 97,  336 => 94,  332 => 129,  329 => 119,  323 => 116,  310 => 80,  305 => 111,  277 => 87,  274 => 94,  263 => 105,  259 => 66,  247 => 38,  244 => 76,  241 => 62,  222 => 60,  210 => 27,  207 => 49,  204 => 63,  184 => 71,  181 => 77,  167 => 48,  157 => 35,  96 => 25,  421 => 143,  417 => 150,  414 => 145,  406 => 139,  398 => 159,  393 => 97,  390 => 134,  376 => 149,  369 => 148,  366 => 117,  352 => 128,  345 => 132,  342 => 126,  331 => 108,  326 => 68,  320 => 114,  317 => 114,  314 => 86,  311 => 105,  308 => 111,  297 => 93,  293 => 104,  281 => 76,  278 => 93,  275 => 39,  264 => 92,  260 => 80,  248 => 54,  245 => 63,  242 => 72,  231 => 70,  227 => 63,  215 => 60,  212 => 77,  209 => 73,  197 => 24,  177 => 51,  171 => 49,  161 => 46,  132 => 61,  121 => 57,  105 => 33,  99 => 31,  81 => 43,  77 => 16,  180 => 47,  176 => 44,  156 => 30,  143 => 24,  139 => 37,  118 => 13,  189 => 80,  185 => 47,  173 => 76,  166 => 16,  152 => 40,  174 => 59,  164 => 74,  154 => 41,  150 => 15,  137 => 33,  133 => 43,  127 => 44,  107 => 53,  102 => 12,  83 => 23,  78 => 16,  53 => 13,  23 => 7,  42 => 11,  138 => 36,  134 => 14,  109 => 42,  103 => 26,  97 => 22,  94 => 25,  84 => 50,  75 => 24,  69 => 24,  66 => 14,  54 => 9,  44 => 11,  230 => 80,  226 => 80,  203 => 12,  193 => 72,  188 => 75,  182 => 17,  178 => 42,  168 => 49,  163 => 47,  160 => 47,  155 => 46,  148 => 45,  145 => 52,  140 => 43,  136 => 63,  125 => 34,  120 => 38,  113 => 45,  101 => 37,  92 => 29,  89 => 22,  85 => 21,  73 => 21,  62 => 14,  59 => 47,  56 => 15,  41 => 9,  126 => 33,  119 => 59,  111 => 35,  106 => 41,  98 => 63,  93 => 25,  86 => 27,  70 => 22,  60 => 13,  28 => 6,  36 => 9,  114 => 36,  104 => 52,  91 => 34,  80 => 25,  63 => 20,  58 => 18,  40 => 12,  34 => 10,  45 => 7,  61 => 11,  55 => 12,  48 => 12,  39 => 8,  35 => 8,  31 => 7,  26 => 4,  21 => 2,  46 => 14,  29 => 6,  57 => 16,  50 => 11,  47 => 11,  38 => 37,  33 => 5,  49 => 45,  32 => 8,  246 => 94,  236 => 19,  232 => 91,  225 => 63,  221 => 78,  216 => 53,  214 => 74,  211 => 64,  208 => 58,  205 => 57,  199 => 48,  196 => 77,  190 => 49,  179 => 58,  175 => 9,  172 => 70,  169 => 8,  162 => 48,  158 => 47,  153 => 69,  151 => 44,  147 => 3,  144 => 2,  141 => 65,  135 => 42,  129 => 34,  124 => 35,  117 => 32,  112 => 56,  90 => 31,  87 => 29,  82 => 17,  72 => 15,  68 => 21,  65 => 49,  52 => 16,  43 => 9,  37 => 8,  30 => 9,  27 => 7,  25 => 4,  24 => 4,  22 => 1,  19 => 6,);
    }
}
