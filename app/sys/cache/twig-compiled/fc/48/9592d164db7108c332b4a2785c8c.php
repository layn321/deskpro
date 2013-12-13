<?php

/* AgentBundle:TicketSearch:part-results-list.html.twig */
class __TwigTemplate_fc489592d164db7108c332b4a2785c8c extends \Application\DeskPRO\Twig\Template
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
        $this->env->loadTemplate("AgentBundle:TicketSearch:part-results-list-thead.html.twig")->display($context);
        // line 2
        echo "<tbody class=\"search-results page-set\" data-page=\"";
        if (isset($context["page"])) { $_page_ = $context["page"]; } else { $_page_ = null; }
        echo twig_escape_filter($this->env, $_page_, "html", null, true);
        echo "\">
";
        // line 3
        if (isset($context["tickets"])) { $_tickets_ = $context["tickets"]; } else { $_tickets_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_tickets_);
        foreach ($context['_seq'] as $context["_key"] => $context["ticket"]) {
            // line 4
            echo "\t<tr
\t\tclass=\"row-item ticket-";
            // line 5
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "id"), "html", null, true);
            echo " ";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if ($this->getAttribute($_ticket_, "worst_sla_status")) {
                echo "sla-status-";
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "worst_sla_status"), "html", null, true);
            }
            echo " ";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if (($this->getAttribute($_ticket_, "status") == "awaiting_agent")) {
                echo "urgency-";
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "urgency"), "html", null, true);
            }
            echo " ";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if ($this->getAttribute($_ticket_, "is_locked")) {
                echo "locked";
            }
            echo "\"
\t\tdata-ticket-id=\"";
            // line 6
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "id"), "html", null, true);
            echo "\"
\t\tdata-route=\"ticket:";
            // line 7
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_ticket_view", array("ticket_id" => $this->getAttribute($_ticket_, "id"))), "html", null, true);
            echo "\"
\t\tdata-route-title=\"@selector(.subject-link span)\"
\t\tdata-route-openclass=\"open\"
\t>
\t\t<td class=\"check-all\" style=\"vertical-align: middle;\" width=\"20\">
\t\t\t<input type=\"checkbox\" value=\"";
            // line 12
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "id"), "html", null, true);
            echo "\" class=\"ticket item-select\" style=\"margin: 0; margin-rght: 4px; padding: 0;\"/>
\t\t</td>
\t\t";
            // line 14
            if (isset($context["display_fields"])) { $_display_fields_ = $context["display_fields"]; } else { $_display_fields_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_display_fields_);
            foreach ($context['_seq'] as $context["_key"] => $context["field"]) {
                // line 15
                echo "\t\t\t";
                if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                if (($_field_ == "subject")) {
                    // line 16
                    echo "\t\t\t\t<td class=\"subject\">
\t\t\t\t\t<a class=\"subject-link\"><span>";
                    // line 17
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "subject"), "html", null, true);
                    echo "</span></a>
\t\t\t\t</td>
\t\t\t";
                } elseif (($_field_ == "id")) {
                    // line 20
                    echo "\t\t\t\t<td class=\"subject\">
\t\t\t\t\t";
                    // line 21
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "id"), "html", null, true);
                    echo "
\t\t\t\t</td>
\t\t\t";
                } elseif (($_field_ == "status")) {
                    // line 24
                    echo "\t\t\t\t<td class=\"subject\">
\t\t\t\t\t";
                    // line 25
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, ("agent.tickets.status_" . strtr($this->getAttribute($_ticket_, "status_code"), array("." => "_"))));
                    echo "
\t\t\t\t</td>
\t\t\t";
                } elseif (($_field_ == "user")) {
                    // line 28
                    echo "\t\t\t\t<td>
\t\t\t\t\t<a data-route=\"person:";
                    // line 29
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_people_view", array("person_id" => (($this->getAttribute($this->getAttribute($_ticket_, "person", array(), "any", false, true), "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "person", array(), "any", false, true), "id"), "0")) : ("0")))), "html", null, true);
                    echo "\" data-route-title=\"@text\">
\t\t\t\t\t\t";
                    // line 30
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "person"), "getDisplayName", array(), "method"), "html", null, true);
                    echo "
\t\t\t\t\t</a>
\t\t\t\t</td>
\t\t\t";
                } elseif (($_field_ == "ref")) {
                    // line 34
                    echo "\t\t\t\t<td>
\t\t\t\t\t";
                    // line 35
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "ref"), "html", null, true);
                    echo "
\t\t\t\t</td>
\t\t\t";
                } elseif (($_field_ == "date_created")) {
                    // line 38
                    echo "\t\t\t\t<td class=\"";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    echo twig_escape_filter($this->env, $_field_, "html", null, true);
                    echo "\">
\t\t\t\t\t<span class=\"timestamp timeago\" title=\"";
                    // line 39
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_created"), "c", "UTC"), "html", null, true);
                    echo "\">";
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_created"), "day"), "html", null, true);
                    echo "</span>
\t\t\t\t</td>
\t\t\t";
                } elseif (($_field_ == "deleted_reason")) {
                    // line 42
                    echo "\t\t\t\t<td class=\"";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    echo twig_escape_filter($this->env, $_field_, "html", null, true);
                    echo "\">
\t\t\t\t\t";
                    // line 43
                    if (isset($context["deleted_tickets"])) { $_deleted_tickets_ = $context["deleted_tickets"]; } else { $_deleted_tickets_ = null; }
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    if ($this->getAttribute($_deleted_tickets_, $this->getAttribute($_ticket_, "id"), array(), "array")) {
                        // line 44
                        echo "\t\t\t\t\t\t";
                        if (isset($context["deleted_tickets"])) { $_deleted_tickets_ = $context["deleted_tickets"]; } else { $_deleted_tickets_ = null; }
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.deleted_by", array("name" => $this->getAttribute($this->getAttribute($this->getAttribute($_deleted_tickets_, $this->getAttribute($_ticket_, "id"), array(), "array"), "by_person"), "display_name")));
                        echo "
\t\t\t\t\t\t";
                        // line 45
                        if (isset($context["deleted_tickets"])) { $_deleted_tickets_ = $context["deleted_tickets"]; } else { $_deleted_tickets_ = null; }
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if ($this->getAttribute($this->getAttribute($_deleted_tickets_, $this->getAttribute($_ticket_, "id"), array(), "array"), "reason")) {
                            // line 46
                            echo "\t\t\t\t\t\t\t(";
                            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.reason");
                            echo ": ";
                            if (isset($context["deleted_tickets"])) { $_deleted_tickets_ = $context["deleted_tickets"]; } else { $_deleted_tickets_ = null; }
                            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_deleted_tickets_, $this->getAttribute($_ticket_, "id"), array(), "array"), "reason"), "html", null, true);
                            echo ")
\t\t\t\t\t\t";
                        }
                        // line 48
                        echo "\t\t\t\t\t";
                    }
                    // line 49
                    echo "\t\t\t\t</td>
\t\t\t";
                } elseif (($_field_ == "person")) {
                    // line 51
                    echo "\t\t\t\t<td class=\"";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    echo twig_escape_filter($this->env, $_field_, "html", null, true);
                    echo "\">
\t\t\t\t\t";
                    // line 52
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "person"), "display_name"), "html", null, true);
                    echo "
\t\t\t\t</td>
\t\t\t";
                } elseif (($_field_ == "department")) {
                    // line 55
                    echo "\t\t\t\t<td class=\"";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    echo twig_escape_filter($this->env, $_field_, "html", null, true);
                    echo "\">
\t\t\t\t\t<span class=\"prop-val department_id\" data-prop-value=\"";
                    // line 56
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "department"), "id"), "html", null, true);
                    echo "\">
\t\t\t\t\t\t";
                    // line 57
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "department"), "title"), "html", null, true);
                    echo "
\t\t\t\t\t</span>
\t\t\t\t</td>
\t\t\t";
                } elseif (($_field_ == "category")) {
                    // line 61
                    echo "\t\t\t\t<td class=\"";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    echo twig_escape_filter($this->env, $_field_, "html", null, true);
                    echo "\">
\t\t\t\t\t<span class=\"prop-val category_id\" data-prop-value=\"";
                    // line 62
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "category"), "id"), "html", null, true);
                    echo "\">
\t\t\t\t\t";
                    // line 63
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    if ($this->getAttribute($_ticket_, "category")) {
                        // line 64
                        echo "\t\t\t\t\t\t";
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "category"), "title"), "html", null, true);
                        echo "
\t\t\t\t\t";
                    } else {
                        // line 66
                        echo "\t\t\t\t\t\t";
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none");
                        echo "
\t\t\t\t\t";
                    }
                    // line 68
                    echo "\t\t\t\t\t</span>
\t\t\t\t</td>
\t\t\t";
                } elseif (($_field_ == "product")) {
                    // line 71
                    echo "\t\t\t\t<td class=\"";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    echo twig_escape_filter($this->env, $_field_, "html", null, true);
                    echo "\">
\t\t\t\t\t<span class=\"prop-val product_id\" data-prop-value=\"";
                    // line 72
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "product"), "id"), "html", null, true);
                    echo "\">
\t\t\t\t\t";
                    // line 73
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    if ($this->getAttribute($_ticket_, "product")) {
                        // line 74
                        echo "\t\t\t\t\t\t";
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "product"), "title"), "html", null, true);
                        echo "
\t\t\t\t\t";
                    } else {
                        // line 76
                        echo "\t\t\t\t\t\t";
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none");
                        echo "
\t\t\t\t\t";
                    }
                    // line 78
                    echo "\t\t\t\t\t</span>
\t\t\t\t</td>
\t\t\t";
                } elseif (($_field_ == "priority")) {
                    // line 81
                    echo "\t\t\t\t<td class=\"";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    echo twig_escape_filter($this->env, $_field_, "html", null, true);
                    echo "\">
\t\t\t\t\t<span class=\"prop-val priority_id\" data-prop-value=\"";
                    // line 82
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "priority"), "id"), "html", null, true);
                    echo "\">
\t\t\t\t\t";
                    // line 83
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    if ($this->getAttribute($_ticket_, "priority")) {
                        // line 84
                        echo "\t\t\t\t\t\t";
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "priority"), "title"), "html", null, true);
                        echo "
\t\t\t\t\t";
                    } else {
                        // line 86
                        echo "\t\t\t\t\t\t";
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none");
                        echo "
\t\t\t\t\t";
                    }
                    // line 88
                    echo "\t\t\t\t\t</span>
\t\t\t\t</td>
\t\t\t";
                } elseif (($_field_ == "organization")) {
                    // line 91
                    echo "\t\t\t\t<td class=\"";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    echo twig_escape_filter($this->env, $_field_, "html", null, true);
                    echo "\">
\t\t\t\t\t<span class=\"prop-val organization_id\" data-prop-value=\"";
                    // line 92
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "organization"), "id"), "html", null, true);
                    echo "\">
\t\t\t\t\t";
                    // line 93
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    if ($this->getAttribute($_ticket_, "organization")) {
                        // line 94
                        echo "\t\t\t\t\t\t<a data-route=\"page:";
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_org_view", array("organization_id" => $this->getAttribute($this->getAttribute($_ticket_, "organization"), "id"))), "html", null, true);
                        echo "\" data-route-title=\"@text\">";
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "organization"), "name"), "html", null, true);
                        echo "</a>
\t\t\t\t\t";
                    } else {
                        // line 96
                        echo "\t\t\t\t\t\t";
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none");
                        echo "
\t\t\t\t\t";
                    }
                    // line 98
                    echo "\t\t\t\t\t</span>
\t\t\t\t</td>
\t\t\t";
                } elseif (($_field_ == "date_user_waiting")) {
                    // line 101
                    echo "\t\t\t\t<td class=\"";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    echo twig_escape_filter($this->env, $_field_, "html", null, true);
                    echo "\">
\t\t\t\t\t";
                    // line 102
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    if ($this->getAttribute($_ticket_, "date_user_waiting")) {
                        // line 103
                        echo "\t\t\t\t\t\t<time class=\"timestamp timeago\" data-timeago-no-ago=\"1\" datetime=\"";
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_user_waiting"), "c", "UTC"), "html", null, true);
                        echo "\">";
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_user_waiting"), "day"), "html", null, true);
                        echo "</time>
\t\t\t\t\t";
                    } else {
                        // line 105
                        echo "\t\t\t\t\t\t";
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.na");
                        echo "
\t\t\t\t\t";
                    }
                    // line 107
                    echo "\t\t\t\t</td>
\t\t\t";
                } elseif (($_field_ == "total_user_waiting")) {
                    // line 109
                    echo "\t\t\t\t<td class=\"";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    echo twig_escape_filter($this->env, $_field_, "html", null, true);
                    echo "\">
\t\t\t\t\t";
                    // line 110
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    if ($this->getAttribute($_ticket_, "real_total_user_waiting")) {
                        // line 111
                        echo "\t\t\t\t\t\t<time class=\"timeago\" data-timeago-no-ago=\"1\" datetime=\"";
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, (("- " . $this->getAttribute($_ticket_, "real_total_user_waiting")) . " seconds"), "c", "UTC"), "html", null, true);
                        echo "\"></time>
\t\t\t\t\t";
                    } else {
                        // line 113
                        echo "\t\t\t\t\t\t";
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.na");
                        echo "
\t\t\t\t\t";
                    }
                    // line 115
                    echo "\t\t\t\t</td>
\t\t\t";
                } elseif (($_field_ == "date_last_agent_reply")) {
                    // line 117
                    echo "\t\t\t\t<td class=\"";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    echo twig_escape_filter($this->env, $_field_, "html", null, true);
                    echo "\">
\t\t\t\t\t";
                    // line 118
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    if ($this->getAttribute($_ticket_, "date_last_agent_reply")) {
                        // line 119
                        echo "\t\t\t\t\t\t<time class=\"timestamp timeago\" datetime=\"";
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_last_agent_reply"), "c", "UTC"), "html", null, true);
                        echo "\">";
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_last_agent_reply"), "day"), "html", null, true);
                        echo "</time>
\t\t\t\t\t";
                    } else {
                        // line 121
                        echo "\t\t\t\t\t\t";
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.na");
                        echo "
\t\t\t\t\t";
                    }
                    // line 123
                    echo "\t\t\t\t</td>
\t\t\t";
                } elseif (($_field_ == "date_last_user_reply")) {
                    // line 125
                    echo "\t\t\t\t<td class=\"";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    echo twig_escape_filter($this->env, $_field_, "html", null, true);
                    echo "\">
\t\t\t\t\t";
                    // line 126
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    if ($this->getAttribute($_ticket_, "date_last_user_reply")) {
                        // line 127
                        echo "\t\t\t\t\t\t<time class=\"timestamp timeago\" datetime=\"";
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_last_user_reply"), "c", "UTC"), "html", null, true);
                        echo "\">";
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_last_user_reply"), "day"), "html", null, true);
                        echo "</time>
\t\t\t\t\t";
                    } else {
                        // line 129
                        echo "\t\t\t\t\t\t";
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.na");
                        echo "
\t\t\t\t\t";
                    }
                    // line 131
                    echo "\t\t\t\t</td>
\t\t\t";
                } elseif (($_field_ == "date_resolved")) {
                    // line 133
                    echo "\t\t\t\t<td class=\"";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    echo twig_escape_filter($this->env, $_field_, "html", null, true);
                    echo "\">
\t\t\t\t\t";
                    // line 134
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    if ($this->getAttribute($_ticket_, "date_resolved")) {
                        // line 135
                        echo "\t\t\t\t\t\t<time class=\"timestamp timeago\" datetime=\"";
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_resolved"), "c", "UTC"), "html", null, true);
                        echo "\">";
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_resolved"), "day"), "html", null, true);
                        echo "</time>
\t\t\t\t\t";
                    } else {
                        // line 137
                        echo "\t\t\t\t\t\t";
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.na");
                        echo "
\t\t\t\t\t";
                    }
                    // line 139
                    echo "\t\t\t\t</td>
\t\t\t";
                } elseif (($_field_ == "agent")) {
                    // line 141
                    echo "\t\t\t\t<td class=\"";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    echo twig_escape_filter($this->env, $_field_, "html", null, true);
                    echo "\">
\t\t\t\t\t<span class=\"prop-val agent_id\" data-prop-value=\"";
                    // line 142
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "agent"), "id"), "html", null, true);
                    echo "\">
\t\t\t\t\t";
                    // line 143
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    if ($this->getAttribute($_ticket_, "agent")) {
                        // line 144
                        echo "\t\t\t\t\t\t";
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "agent"), "display_name"), "html", null, true);
                        echo "
\t\t\t\t\t";
                    } else {
                        // line 146
                        echo "\t\t\t\t\t\t";
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.unassigned");
                        echo "
\t\t\t\t\t";
                    }
                    // line 148
                    echo "\t\t\t\t\t</span>
\t\t\t\t</td>
\t\t\t";
                } elseif (($_field_ == "agent_team")) {
                    // line 151
                    echo "\t\t\t\t<td class=\"";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    echo twig_escape_filter($this->env, $_field_, "html", null, true);
                    echo "\">
\t\t\t\t\t<span class=\"prop-val agent_team_id\" data-prop-value=\"";
                    // line 152
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "agent_team"), "name"), "html", null, true);
                    echo "\">
\t\t\t\t\t";
                    // line 153
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    if ($this->getAttribute($_ticket_, "agent")) {
                        // line 154
                        echo "\t\t\t\t\t\t";
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "agent_team"), "name"), "html", null, true);
                        echo "
\t\t\t\t\t";
                    } else {
                        // line 156
                        echo "\t\t\t\t\t\t";
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.no_team");
                        echo "
\t\t\t\t\t";
                    }
                    // line 158
                    echo "\t\t\t\t\t</span>
\t\t\t\t</td>
\t\t\t";
                } elseif (($_field_ == "labels")) {
                    // line 161
                    echo "\t\t\t\t<td class=\"";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    echo twig_escape_filter($this->env, $_field_, "html", null, true);
                    echo "\">
\t\t\t\t\t<span class=\"prop-val labels\">
\t\t\t\t\t\t";
                    // line 163
                    if (isset($context["ticket_display"])) { $_ticket_display_ = $context["ticket_display"]; } else { $_ticket_display_ = null; }
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($_ticket_display_, "getTicketLabels", array(0 => $_ticket_), "method"));
                    foreach ($context['_seq'] as $context["_key"] => $context["label"]) {
                        // line 164
                        echo "\t\t\t\t\t\t\t<span class=\"listing-tag\">";
                        if (isset($context["label"])) { $_label_ = $context["label"]; } else { $_label_ = null; }
                        echo twig_escape_filter($this->env, $_label_, "html", null, true);
                        echo "</span>
\t\t\t\t\t\t";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['label'], $context['_parent'], $context['loop']);
                    $context = array_merge($_parent, array_intersect_key($context, $_parent));
                    // line 166
                    echo "\t\t\t\t\t</span>
\t\t\t\t</td>
\t\t\t";
                } else {
                    // line 169
                    echo "\t\t\t\t";
                    if (isset($context["all_custom_fields"])) { $_all_custom_fields_ = $context["all_custom_fields"]; } else { $_all_custom_fields_ = null; }
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($_all_custom_fields_, $this->getAttribute($_ticket_, "id"), array(), "array"));
                    foreach ($context['_seq'] as $context["_key"] => $context["f"]) {
                        // line 170
                        echo "\t\t\t\t\t";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                        if (($_field_ == (("ticket_fields[" . $this->getAttribute($_f_, "id")) . "]"))) {
                            // line 171
                            echo "\t\t\t\t\t\t<td class=\"ticket-field\">
\t\t\t\t\t\t\t<div class=\"prop-val ticket_fields_";
                            // line 172
                            if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_f_, "id"), "html", null, true);
                            echo "\">
\t\t\t\t\t\t\t\t";
                            // line 173
                            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                            if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                            if ($this->getAttribute($_ticket_, "hasCustomField", array(0 => $this->getAttribute($_f_, "id")), "method")) {
                                // line 174
                                echo "\t\t\t\t\t\t\t\t\t";
                                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                                echo $this->env->getExtension('deskpro_templating')->renderCustomField($_f_);
                                echo "
\t\t\t\t\t\t\t\t";
                            }
                            // line 176
                            echo "\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</td>
\t\t\t\t\t";
                        }
                        // line 179
                        echo "\t\t\t\t";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['f'], $context['_parent'], $context['loop']);
                    $context = array_merge($_parent, array_intersect_key($context, $_parent));
                    // line 180
                    echo "\t\t\t\t";
                    if (isset($context["user_all_custom_fields"])) { $_user_all_custom_fields_ = $context["user_all_custom_fields"]; } else { $_user_all_custom_fields_ = null; }
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($_user_all_custom_fields_, $this->getAttribute($this->getAttribute($_ticket_, "person"), "id"), array(), "array"));
                    foreach ($context['_seq'] as $context["_key"] => $context["f"]) {
                        // line 181
                        echo "\t\t\t\t\t";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                        if (($_field_ == (("person_fields[" . $this->getAttribute($_f_, "id")) . "]"))) {
                            // line 182
                            echo "\t\t\t\t\t\t<td class=\"person-field\">
\t\t\t\t\t\t\t<div class=\"prop-val person_fields_";
                            // line 183
                            if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_f_, "id"), "html", null, true);
                            echo "\">
\t\t\t\t\t\t\t\t";
                            // line 184
                            if (isset($context["person"])) { $_person_ = $context["person"]; } else { $_person_ = null; }
                            if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                            if ($this->getAttribute($_person_, "hasCustomField", array(0 => $this->getAttribute($_f_, "id")), "method")) {
                                // line 185
                                echo "\t\t\t\t\t\t\t\t\t";
                                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                                echo $this->env->getExtension('deskpro_templating')->renderCustomField($_f_);
                                echo "
\t\t\t\t\t\t\t\t";
                            }
                            // line 187
                            echo "\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</td>
\t\t\t\t\t";
                        }
                        // line 190
                        echo "\t\t\t\t";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['f'], $context['_parent'], $context['loop']);
                    $context = array_merge($_parent, array_intersect_key($context, $_parent));
                    // line 191
                    echo "\t\t\t";
                }
                // line 192
                echo "\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['field'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 193
            echo "\t</tr>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['ticket'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 195
        echo "</tbody>
";
    }

    public function getTemplateName()
    {
        return "AgentBundle:TicketSearch:part-results-list.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  3071 => 913,  3059 => 903,  3052 => 899,  3047 => 898,  3043 => 897,  3037 => 894,  3028 => 891,  3019 => 887,  3016 => 886,  3014 => 885,  3006 => 880,  2996 => 873,  2989 => 870,  2979 => 866,  2970 => 860,  2964 => 858,  2958 => 855,  2954 => 854,  2945 => 848,  2939 => 845,  2929 => 838,  2922 => 834,  2912 => 827,  2906 => 824,  2900 => 820,  2896 => 818,  2888 => 817,  2883 => 816,  2879 => 815,  2872 => 813,  2869 => 812,  2864 => 810,  2857 => 806,  2848 => 801,  2832 => 799,  2827 => 798,  2817 => 797,  2814 => 796,  2812 => 795,  2802 => 794,  2786 => 792,  2779 => 791,  2775 => 790,  2770 => 789,  2762 => 788,  2755 => 787,  2751 => 786,  2747 => 785,  2741 => 784,  2729 => 776,  2721 => 772,  2711 => 767,  2698 => 757,  2690 => 752,  2684 => 749,  2651 => 732,  2642 => 730,  2634 => 725,  2628 => 724,  2622 => 723,  2619 => 722,  2615 => 721,  2612 => 720,  2605 => 718,  2599 => 717,  2591 => 714,  2587 => 712,  2579 => 708,  2574 => 707,  2569 => 705,  2566 => 704,  2562 => 703,  2554 => 699,  2549 => 698,  2544 => 696,  2541 => 695,  2524 => 689,  2519 => 687,  2516 => 686,  2512 => 685,  2504 => 681,  2494 => 678,  2479 => 672,  2474 => 671,  2466 => 668,  2462 => 667,  2444 => 660,  2438 => 659,  2435 => 658,  2430 => 656,  2417 => 651,  2412 => 649,  2409 => 648,  2405 => 647,  2397 => 643,  2387 => 640,  2375 => 635,  2368 => 633,  2362 => 631,  2356 => 630,  2353 => 629,  2348 => 627,  2345 => 626,  2341 => 625,  2336 => 622,  2329 => 620,  2323 => 618,  2317 => 617,  2314 => 616,  2309 => 614,  2306 => 613,  2302 => 612,  2286 => 606,  2280 => 604,  2274 => 603,  2271 => 602,  2266 => 600,  2263 => 599,  2259 => 598,  2249 => 594,  2244 => 593,  2239 => 591,  2232 => 588,  2226 => 586,  2221 => 585,  2210 => 582,  2197 => 581,  2180 => 579,  2169 => 576,  2150 => 575,  2140 => 572,  2131 => 570,  2110 => 567,  2106 => 566,  2061 => 561,  2026 => 554,  2018 => 553,  2000 => 550,  1990 => 548,  1962 => 540,  1956 => 536,  1939 => 532,  1935 => 531,  1896 => 520,  1888 => 519,  1881 => 518,  1869 => 517,  1857 => 516,  1848 => 514,  1844 => 512,  1832 => 508,  1811 => 502,  1808 => 501,  1786 => 492,  1779 => 490,  1769 => 485,  1762 => 483,  1758 => 482,  1735 => 472,  1731 => 470,  1712 => 465,  1707 => 463,  1696 => 461,  1683 => 454,  1679 => 452,  1655 => 445,  1650 => 444,  1647 => 443,  1635 => 439,  1607 => 427,  1597 => 423,  1541 => 402,  1523 => 393,  1495 => 389,  1485 => 387,  1455 => 383,  1450 => 382,  1441 => 378,  1435 => 377,  1419 => 375,  1407 => 373,  1362 => 364,  1347 => 361,  1296 => 352,  1258 => 348,  1253 => 347,  1177 => 325,  1151 => 320,  1118 => 314,  1330 => 4,  1327 => 3,  1322 => 2,  1290 => 297,  1283 => 295,  1278 => 292,  1271 => 349,  1230 => 277,  1190 => 265,  1166 => 258,  1138 => 251,  642 => 147,  1264 => 464,  1259 => 462,  1227 => 448,  1211 => 336,  1399 => 568,  1387 => 560,  1358 => 555,  1334 => 357,  1316 => 529,  1298 => 520,  1195 => 478,  1148 => 253,  1120 => 453,  1117 => 245,  1093 => 440,  788 => 201,  612 => 203,  1394 => 446,  1385 => 442,  1382 => 441,  1369 => 433,  1363 => 432,  1360 => 431,  1350 => 429,  1305 => 523,  1300 => 409,  1281 => 404,  1277 => 350,  1265 => 398,  1255 => 396,  1247 => 393,  1234 => 387,  1202 => 270,  1199 => 374,  1187 => 264,  1162 => 365,  1136 => 317,  1128 => 352,  1122 => 248,  1069 => 299,  968 => 387,  846 => 188,  1183 => 449,  1132 => 460,  1097 => 341,  957 => 272,  907 => 278,  875 => 263,  653 => 176,  1329 => 405,  1309 => 354,  1303 => 399,  1297 => 398,  1292 => 397,  1280 => 394,  1249 => 346,  1237 => 279,  1205 => 409,  1200 => 408,  1194 => 440,  1178 => 430,  1175 => 447,  1170 => 375,  1135 => 362,  1105 => 428,  1078 => 335,  1068 => 388,  1048 => 417,  961 => 273,  922 => 280,  750 => 192,  842 => 263,  1038 => 292,  904 => 198,  882 => 194,  831 => 267,  860 => 314,  790 => 284,  733 => 230,  707 => 185,  744 => 220,  873 => 349,  824 => 267,  762 => 243,  713 => 225,  578 => 158,  2739 => 1153,  2735 => 1152,  2732 => 1151,  2725 => 1146,  2712 => 1144,  2707 => 1143,  2692 => 1131,  2688 => 1130,  2683 => 1127,  2680 => 748,  2677 => 1125,  2671 => 743,  2658 => 735,  2653 => 1118,  2645 => 731,  2641 => 1112,  2636 => 726,  2632 => 1108,  2629 => 1107,  2626 => 1106,  2606 => 1089,  2602 => 1088,  2598 => 1087,  2594 => 1086,  2590 => 1085,  2586 => 1084,  2572 => 1073,  2568 => 1072,  2564 => 1071,  2560 => 1070,  2556 => 1069,  2552 => 1068,  2537 => 694,  2533 => 1055,  2529 => 690,  2525 => 1053,  2521 => 1052,  2503 => 1040,  2499 => 680,  2495 => 1038,  2491 => 677,  2487 => 676,  2483 => 1035,  2469 => 669,  2465 => 1023,  2461 => 1022,  2457 => 664,  2453 => 1020,  2449 => 1019,  2426 => 999,  2422 => 652,  2418 => 997,  2414 => 996,  2410 => 995,  2406 => 994,  2392 => 642,  2388 => 982,  2384 => 639,  2380 => 638,  2376 => 979,  2372 => 978,  2358 => 967,  2354 => 966,  2350 => 965,  2346 => 964,  2342 => 963,  2338 => 962,  2324 => 951,  2320 => 950,  2316 => 949,  2312 => 948,  2308 => 947,  2304 => 946,  2295 => 939,  2282 => 937,  2277 => 936,  2269 => 931,  2265 => 930,  2260 => 927,  2256 => 597,  2253 => 925,  2230 => 901,  2220 => 899,  2216 => 898,  2211 => 896,  2208 => 895,  2198 => 893,  2194 => 580,  2189 => 890,  2184 => 889,  2177 => 578,  2173 => 577,  2164 => 877,  2151 => 875,  2146 => 574,  2143 => 873,  2141 => 864,  2134 => 571,  2130 => 859,  2122 => 853,  2120 => 852,  2113 => 848,  2105 => 846,  2096 => 839,  2083 => 564,  2078 => 836,  2073 => 562,  2067 => 830,  2062 => 828,  2058 => 827,  2054 => 826,  2050 => 560,  2045 => 557,  2038 => 822,  2034 => 821,  2022 => 814,  2009 => 812,  1999 => 809,  1993 => 805,  1988 => 803,  1984 => 802,  1980 => 801,  1976 => 546,  1971 => 799,  1968 => 798,  1964 => 797,  1960 => 796,  1954 => 535,  1946 => 788,  1942 => 533,  1938 => 786,  1932 => 530,  1927 => 780,  1919 => 527,  1915 => 777,  1911 => 776,  1906 => 775,  1903 => 774,  1899 => 773,  1895 => 772,  1886 => 768,  1883 => 764,  1871 => 759,  1866 => 756,  1863 => 755,  1842 => 752,  1825 => 749,  1818 => 748,  1815 => 503,  1800 => 499,  1793 => 741,  1772 => 729,  1761 => 721,  1757 => 720,  1751 => 717,  1740 => 709,  1719 => 697,  1715 => 696,  1686 => 673,  1676 => 669,  1665 => 661,  1661 => 446,  1645 => 442,  1638 => 649,  1630 => 438,  1625 => 435,  1622 => 641,  1598 => 637,  1577 => 416,  1558 => 408,  1549 => 405,  1546 => 626,  1535 => 618,  1531 => 617,  1514 => 606,  1510 => 605,  1504 => 391,  1493 => 594,  1483 => 590,  1464 => 580,  1458 => 577,  1443 => 568,  1437 => 565,  1426 => 557,  1405 => 545,  1401 => 544,  1395 => 541,  1390 => 538,  1381 => 368,  1373 => 531,  1348 => 521,  1343 => 520,  1335 => 515,  1321 => 510,  1318 => 549,  1299 => 503,  1294 => 298,  1282 => 496,  1269 => 466,  1260 => 397,  1240 => 344,  1221 => 339,  1216 => 338,  1210 => 272,  1206 => 445,  1193 => 467,  1189 => 466,  1155 => 468,  1150 => 419,  1022 => 312,  1006 => 299,  988 => 398,  969 => 392,  965 => 294,  921 => 286,  878 => 275,  866 => 349,  854 => 254,  819 => 322,  796 => 286,  1713 => 683,  1700 => 681,  1685 => 674,  1662 => 670,  1643 => 669,  1640 => 441,  1631 => 663,  1618 => 661,  1613 => 430,  1608 => 639,  1605 => 656,  1602 => 424,  1589 => 645,  1586 => 644,  1583 => 643,  1554 => 406,  1547 => 404,  1521 => 602,  1508 => 600,  1499 => 390,  1491 => 593,  1482 => 386,  1472 => 582,  1468 => 581,  1465 => 579,  1457 => 573,  1445 => 380,  1422 => 556,  1416 => 553,  1410 => 548,  1397 => 546,  1392 => 545,  1386 => 541,  1377 => 532,  1370 => 530,  1357 => 528,  1352 => 554,  1341 => 428,  1314 => 510,  1311 => 1,  1275 => 493,  1248 => 284,  1238 => 488,  1225 => 476,  1220 => 275,  1209 => 466,  1185 => 385,  1182 => 431,  1159 => 421,  1154 => 254,  1130 => 438,  1125 => 407,  1101 => 308,  1074 => 432,  1056 => 326,  1046 => 323,  1043 => 293,  1030 => 397,  1027 => 289,  947 => 361,  925 => 352,  913 => 259,  893 => 196,  881 => 253,  847 => 243,  829 => 336,  825 => 259,  1083 => 237,  995 => 399,  984 => 350,  963 => 292,  941 => 354,  851 => 367,  682 => 192,  1365 => 365,  1359 => 545,  1349 => 539,  1336 => 427,  1331 => 424,  1323 => 420,  1319 => 530,  1312 => 402,  1284 => 475,  1272 => 401,  1268 => 509,  1261 => 288,  1251 => 285,  1245 => 483,  1231 => 341,  1207 => 271,  1197 => 267,  1180 => 326,  1173 => 457,  1169 => 259,  1157 => 323,  1147 => 438,  1109 => 330,  1065 => 297,  1059 => 423,  1047 => 385,  1044 => 424,  1033 => 381,  1009 => 357,  991 => 363,  987 => 404,  973 => 294,  931 => 202,  924 => 261,  911 => 347,  906 => 81,  885 => 337,  872 => 335,  855 => 332,  749 => 240,  701 => 221,  594 => 180,  1163 => 257,  1143 => 318,  1087 => 420,  1077 => 300,  1051 => 325,  1037 => 223,  1010 => 301,  999 => 407,  932 => 352,  899 => 306,  895 => 404,  933 => 387,  914 => 133,  909 => 323,  833 => 284,  783 => 315,  755 => 303,  666 => 214,  453 => 168,  639 => 209,  568 => 176,  520 => 232,  657 => 184,  572 => 201,  609 => 202,  20 => 1,  659 => 217,  562 => 158,  548 => 180,  558 => 197,  479 => 121,  589 => 154,  457 => 112,  413 => 224,  953 => 206,  948 => 267,  935 => 394,  929 => 283,  916 => 382,  864 => 365,  844 => 242,  816 => 342,  807 => 212,  801 => 268,  774 => 257,  766 => 312,  737 => 297,  685 => 218,  664 => 175,  635 => 249,  593 => 199,  546 => 153,  532 => 236,  865 => 191,  852 => 241,  838 => 233,  820 => 182,  781 => 198,  764 => 193,  725 => 250,  632 => 268,  602 => 170,  565 => 145,  529 => 153,  505 => 147,  487 => 271,  473 => 212,  1853 => 515,  1849 => 753,  1839 => 751,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 733,  1778 => 732,  1770 => 807,  1749 => 478,  1738 => 793,  1728 => 469,  1721 => 788,  1717 => 787,  1709 => 693,  1705 => 781,  1688 => 675,  1681 => 769,  1677 => 451,  1667 => 449,  1656 => 762,  1648 => 655,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 413,  1545 => 719,  1534 => 606,  1527 => 395,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 593,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 372,  1400 => 370,  1383 => 540,  1379 => 557,  1356 => 632,  1351 => 362,  1338 => 359,  1332 => 617,  1315 => 613,  1302 => 353,  1289 => 406,  1276 => 604,  1263 => 601,  1244 => 456,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 332,  1181 => 561,  1171 => 446,  1158 => 550,  1114 => 428,  1104 => 465,  1072 => 333,  1062 => 7,  1024 => 395,  1014 => 218,  1000 => 368,  990 => 303,  980 => 393,  960 => 466,  918 => 285,  888 => 80,  834 => 232,  673 => 190,  636 => 145,  462 => 118,  454 => 127,  1144 => 463,  1139 => 356,  1131 => 316,  1127 => 434,  1110 => 312,  1092 => 459,  1089 => 239,  1086 => 238,  1084 => 337,  1063 => 232,  1060 => 231,  1055 => 295,  1050 => 227,  1035 => 291,  1019 => 330,  1003 => 401,  959 => 387,  900 => 366,  880 => 276,  870 => 250,  867 => 249,  859 => 294,  848 => 271,  839 => 376,  828 => 302,  823 => 183,  809 => 179,  800 => 241,  797 => 267,  794 => 177,  786 => 283,  740 => 194,  734 => 188,  703 => 228,  693 => 297,  630 => 166,  626 => 176,  614 => 163,  610 => 172,  581 => 206,  564 => 149,  525 => 138,  722 => 226,  697 => 282,  674 => 270,  671 => 177,  577 => 180,  569 => 187,  557 => 179,  502 => 187,  497 => 76,  445 => 125,  729 => 306,  684 => 180,  676 => 178,  669 => 268,  660 => 203,  647 => 175,  643 => 229,  601 => 195,  570 => 129,  522 => 132,  501 => 147,  296 => 63,  374 => 81,  631 => 179,  616 => 198,  608 => 194,  605 => 193,  596 => 134,  574 => 163,  561 => 126,  527 => 165,  433 => 101,  388 => 84,  426 => 97,  383 => 105,  461 => 137,  370 => 103,  395 => 166,  294 => 81,  223 => 40,  220 => 67,  492 => 129,  468 => 119,  444 => 149,  410 => 94,  397 => 90,  377 => 60,  262 => 71,  250 => 106,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 793,  2649 => 2234,  2625 => 2213,  2517 => 1051,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 662,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 608,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 568,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 543,  1826 => 506,  1819 => 504,  1732 => 1414,  1723 => 467,  1695 => 680,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 638,  1533 => 397,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 572,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 526,  1301 => 1037,  1252 => 392,  1243 => 283,  1223 => 978,  1212 => 494,  1167 => 366,  1161 => 933,  1145 => 923,  1140 => 413,  1134 => 409,  1126 => 443,  1106 => 528,  1100 => 897,  1026 => 220,  997 => 367,  983 => 298,  975 => 361,  956 => 318,  939 => 264,  902 => 274,  894 => 364,  879 => 76,  757 => 309,  727 => 293,  716 => 226,  670 => 204,  528 => 187,  476 => 123,  435 => 121,  354 => 89,  341 => 56,  192 => 44,  321 => 88,  243 => 75,  793 => 266,  780 => 247,  758 => 229,  700 => 193,  686 => 294,  652 => 183,  638 => 269,  620 => 165,  545 => 243,  523 => 140,  494 => 274,  459 => 156,  438 => 104,  351 => 78,  347 => 83,  402 => 99,  268 => 72,  430 => 108,  411 => 101,  379 => 95,  322 => 70,  315 => 86,  289 => 78,  284 => 86,  255 => 107,  234 => 60,  1133 => 444,  1124 => 455,  1121 => 430,  1116 => 405,  1113 => 429,  1108 => 376,  1103 => 444,  1098 => 401,  1081 => 320,  1073 => 235,  1067 => 314,  1064 => 330,  1061 => 421,  1053 => 318,  1049 => 294,  1042 => 313,  1039 => 384,  1025 => 304,  1021 => 219,  1015 => 308,  1008 => 284,  996 => 406,  989 => 277,  985 => 395,  981 => 296,  977 => 321,  970 => 275,  966 => 274,  955 => 293,  952 => 464,  943 => 266,  936 => 353,  930 => 289,  919 => 314,  917 => 348,  908 => 258,  905 => 363,  896 => 275,  891 => 338,  877 => 334,  862 => 248,  857 => 271,  837 => 261,  832 => 260,  827 => 184,  821 => 266,  803 => 179,  778 => 175,  769 => 253,  765 => 201,  753 => 171,  746 => 196,  743 => 297,  735 => 168,  730 => 187,  720 => 305,  717 => 165,  712 => 186,  691 => 219,  678 => 275,  654 => 199,  587 => 191,  576 => 167,  539 => 200,  517 => 144,  471 => 262,  441 => 123,  437 => 114,  418 => 99,  386 => 107,  373 => 120,  304 => 65,  270 => 69,  265 => 81,  229 => 41,  477 => 167,  455 => 70,  448 => 110,  429 => 235,  407 => 120,  399 => 111,  389 => 87,  375 => 83,  358 => 98,  349 => 137,  335 => 41,  327 => 60,  298 => 98,  280 => 70,  249 => 39,  194 => 49,  142 => 27,  344 => 77,  318 => 57,  306 => 102,  295 => 74,  357 => 101,  300 => 82,  286 => 63,  276 => 74,  269 => 97,  254 => 50,  128 => 30,  237 => 64,  165 => 34,  122 => 25,  798 => 256,  770 => 309,  759 => 278,  748 => 298,  731 => 191,  721 => 227,  718 => 188,  708 => 185,  696 => 236,  617 => 164,  590 => 166,  553 => 145,  550 => 157,  540 => 161,  533 => 182,  500 => 135,  493 => 137,  489 => 202,  482 => 198,  467 => 70,  464 => 129,  458 => 118,  452 => 197,  449 => 112,  415 => 92,  382 => 93,  372 => 82,  361 => 101,  356 => 98,  339 => 120,  302 => 67,  285 => 71,  258 => 41,  123 => 28,  108 => 24,  424 => 108,  394 => 89,  380 => 105,  338 => 71,  319 => 79,  316 => 78,  312 => 56,  290 => 73,  267 => 68,  206 => 36,  110 => 27,  240 => 74,  224 => 33,  219 => 38,  217 => 56,  202 => 36,  186 => 33,  170 => 44,  100 => 23,  67 => 12,  14 => 1,  1096 => 345,  1090 => 339,  1088 => 397,  1085 => 456,  1066 => 233,  1034 => 282,  1031 => 290,  1018 => 303,  1013 => 302,  1007 => 408,  1002 => 403,  993 => 279,  986 => 212,  982 => 211,  976 => 399,  971 => 209,  964 => 208,  949 => 289,  946 => 288,  940 => 388,  937 => 374,  928 => 262,  926 => 318,  915 => 284,  912 => 82,  903 => 231,  898 => 440,  892 => 255,  889 => 277,  887 => 302,  884 => 79,  876 => 252,  874 => 193,  871 => 331,  863 => 345,  861 => 270,  858 => 247,  850 => 189,  843 => 270,  840 => 186,  815 => 264,  812 => 263,  808 => 323,  804 => 258,  799 => 312,  791 => 202,  785 => 200,  775 => 313,  771 => 245,  754 => 267,  728 => 167,  726 => 190,  723 => 189,  715 => 105,  711 => 220,  709 => 222,  706 => 232,  698 => 182,  694 => 182,  692 => 161,  689 => 181,  681 => 224,  677 => 288,  675 => 234,  663 => 213,  661 => 185,  650 => 213,  646 => 231,  629 => 266,  627 => 180,  625 => 266,  622 => 202,  598 => 157,  592 => 155,  586 => 175,  575 => 189,  566 => 251,  556 => 156,  554 => 158,  541 => 152,  536 => 142,  515 => 79,  511 => 208,  509 => 142,  488 => 127,  486 => 145,  483 => 135,  465 => 118,  463 => 119,  450 => 107,  432 => 125,  419 => 65,  371 => 154,  362 => 185,  353 => 73,  337 => 93,  333 => 83,  309 => 55,  303 => 53,  299 => 89,  291 => 64,  272 => 46,  261 => 49,  253 => 30,  239 => 61,  235 => 51,  213 => 38,  200 => 55,  198 => 51,  159 => 38,  149 => 36,  146 => 31,  131 => 30,  116 => 25,  79 => 14,  74 => 18,  71 => 17,  836 => 262,  817 => 243,  814 => 321,  811 => 320,  805 => 244,  787 => 257,  779 => 169,  776 => 232,  773 => 322,  761 => 199,  751 => 302,  747 => 191,  742 => 190,  739 => 189,  736 => 215,  724 => 259,  705 => 69,  702 => 216,  688 => 193,  680 => 205,  667 => 273,  662 => 282,  656 => 215,  649 => 182,  644 => 181,  641 => 168,  624 => 162,  613 => 166,  607 => 171,  597 => 260,  591 => 170,  584 => 236,  579 => 132,  563 => 212,  559 => 183,  551 => 190,  547 => 140,  537 => 160,  524 => 146,  512 => 137,  507 => 237,  504 => 149,  498 => 129,  485 => 126,  480 => 134,  472 => 111,  466 => 138,  460 => 254,  447 => 107,  442 => 128,  434 => 133,  428 => 102,  422 => 118,  404 => 87,  368 => 80,  364 => 75,  340 => 94,  334 => 163,  330 => 61,  325 => 90,  292 => 50,  287 => 51,  282 => 62,  279 => 85,  273 => 73,  266 => 44,  256 => 73,  252 => 64,  228 => 90,  218 => 57,  201 => 51,  64 => 7,  51 => 12,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 569,  2109 => 847,  2104 => 802,  2101 => 801,  2087 => 565,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 823,  2037 => 789,  2031 => 555,  2028 => 818,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 811,  2001 => 1668,  1996 => 771,  1994 => 549,  1987 => 766,  1983 => 547,  1979 => 764,  1972 => 761,  1969 => 544,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 534,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 528,  1923 => 779,  1916 => 736,  1912 => 735,  1908 => 522,  1901 => 521,  1897 => 730,  1894 => 729,  1889 => 769,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 511,  1835 => 509,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 500,  1801 => 695,  1798 => 694,  1795 => 498,  1789 => 814,  1776 => 489,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 481,  1750 => 668,  1745 => 667,  1742 => 476,  1739 => 665,  1736 => 708,  1730 => 705,  1720 => 688,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 672,  1678 => 644,  1671 => 450,  1666 => 640,  1663 => 447,  1660 => 763,  1657 => 659,  1651 => 656,  1641 => 631,  1637 => 440,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 651,  1592 => 614,  1587 => 613,  1584 => 635,  1581 => 731,  1576 => 608,  1574 => 633,  1567 => 603,  1563 => 410,  1559 => 601,  1552 => 628,  1548 => 597,  1542 => 593,  1529 => 605,  1525 => 614,  1516 => 708,  1512 => 583,  1503 => 599,  1490 => 388,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 384,  1447 => 569,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 374,  1406 => 532,  1393 => 564,  1388 => 537,  1380 => 539,  1376 => 367,  1371 => 366,  1367 => 527,  1364 => 518,  1361 => 523,  1355 => 543,  1342 => 360,  1337 => 510,  1328 => 356,  1324 => 550,  1317 => 511,  1313 => 499,  1310 => 526,  1304 => 504,  1291 => 479,  1286 => 476,  1279 => 486,  1274 => 468,  1270 => 483,  1266 => 487,  1262 => 490,  1257 => 500,  1254 => 286,  1250 => 394,  1246 => 345,  1239 => 389,  1235 => 343,  1232 => 416,  1226 => 383,  1213 => 273,  1208 => 481,  1201 => 443,  1196 => 335,  1192 => 490,  1188 => 456,  1184 => 263,  1179 => 448,  1176 => 261,  1172 => 428,  1168 => 451,  1164 => 450,  1160 => 324,  1153 => 561,  1149 => 359,  1146 => 444,  1137 => 249,  1123 => 442,  1119 => 354,  1115 => 313,  1111 => 377,  1107 => 242,  1102 => 344,  1099 => 347,  1095 => 307,  1091 => 321,  1082 => 455,  1079 => 524,  1076 => 393,  1070 => 431,  1057 => 313,  1052 => 406,  1045 => 484,  1040 => 224,  1036 => 283,  1032 => 415,  1028 => 312,  1023 => 288,  1020 => 311,  1016 => 285,  1012 => 390,  1005 => 216,  1001 => 304,  998 => 308,  992 => 406,  979 => 297,  974 => 400,  967 => 399,  962 => 397,  958 => 383,  954 => 293,  950 => 292,  945 => 391,  942 => 290,  938 => 375,  934 => 203,  927 => 288,  923 => 201,  920 => 369,  910 => 365,  901 => 197,  897 => 256,  890 => 271,  886 => 270,  883 => 353,  868 => 273,  856 => 293,  853 => 341,  849 => 264,  845 => 329,  841 => 249,  835 => 268,  830 => 333,  826 => 226,  822 => 281,  818 => 265,  813 => 215,  810 => 290,  806 => 261,  802 => 210,  795 => 241,  792 => 335,  789 => 249,  784 => 286,  782 => 237,  777 => 255,  772 => 196,  768 => 195,  763 => 327,  760 => 305,  756 => 248,  752 => 198,  745 => 245,  741 => 218,  738 => 241,  732 => 171,  719 => 288,  714 => 251,  710 => 164,  704 => 184,  699 => 183,  695 => 195,  690 => 226,  687 => 210,  683 => 156,  679 => 191,  672 => 153,  668 => 187,  665 => 151,  658 => 177,  645 => 170,  640 => 184,  634 => 206,  628 => 166,  623 => 179,  619 => 174,  611 => 158,  606 => 234,  603 => 156,  599 => 174,  595 => 169,  583 => 169,  580 => 164,  573 => 148,  560 => 160,  543 => 175,  538 => 174,  534 => 138,  530 => 148,  526 => 170,  521 => 139,  518 => 194,  514 => 143,  510 => 132,  503 => 141,  496 => 202,  490 => 129,  484 => 128,  474 => 133,  470 => 131,  446 => 106,  440 => 130,  436 => 62,  431 => 113,  425 => 119,  416 => 117,  412 => 115,  408 => 93,  403 => 92,  400 => 91,  396 => 110,  392 => 88,  385 => 97,  381 => 85,  367 => 102,  363 => 89,  359 => 79,  355 => 76,  350 => 96,  346 => 73,  343 => 72,  328 => 93,  324 => 59,  313 => 81,  307 => 66,  301 => 74,  288 => 72,  283 => 76,  271 => 58,  257 => 68,  251 => 66,  238 => 43,  233 => 50,  195 => 49,  191 => 48,  187 => 20,  183 => 21,  130 => 31,  88 => 16,  76 => 14,  115 => 20,  95 => 22,  655 => 148,  651 => 275,  648 => 171,  637 => 180,  633 => 167,  621 => 462,  618 => 241,  615 => 173,  604 => 201,  600 => 233,  588 => 206,  585 => 153,  582 => 153,  571 => 187,  567 => 161,  555 => 125,  552 => 141,  549 => 154,  544 => 179,  542 => 139,  535 => 151,  531 => 139,  519 => 80,  516 => 218,  513 => 154,  508 => 117,  506 => 131,  499 => 139,  495 => 125,  491 => 146,  481 => 215,  478 => 124,  475 => 121,  469 => 182,  456 => 135,  451 => 126,  443 => 118,  439 => 242,  427 => 60,  423 => 96,  420 => 109,  409 => 107,  405 => 99,  401 => 56,  391 => 62,  387 => 86,  384 => 83,  378 => 84,  365 => 79,  360 => 102,  348 => 170,  336 => 62,  332 => 92,  329 => 119,  323 => 116,  310 => 76,  305 => 83,  277 => 47,  274 => 68,  263 => 105,  259 => 67,  247 => 63,  244 => 64,  241 => 63,  222 => 57,  210 => 37,  207 => 47,  204 => 52,  184 => 45,  181 => 46,  167 => 39,  157 => 27,  96 => 22,  421 => 95,  417 => 150,  414 => 145,  406 => 113,  398 => 98,  393 => 53,  390 => 109,  376 => 108,  369 => 148,  366 => 186,  352 => 128,  345 => 65,  342 => 64,  331 => 154,  326 => 91,  320 => 77,  317 => 69,  314 => 86,  311 => 69,  308 => 84,  297 => 51,  293 => 65,  281 => 57,  278 => 59,  275 => 70,  264 => 55,  260 => 66,  248 => 48,  245 => 105,  242 => 53,  231 => 42,  227 => 53,  215 => 60,  212 => 37,  209 => 47,  197 => 61,  177 => 45,  171 => 49,  161 => 33,  132 => 34,  121 => 29,  105 => 24,  99 => 42,  81 => 28,  77 => 16,  180 => 20,  176 => 36,  156 => 32,  143 => 39,  139 => 24,  118 => 28,  189 => 80,  185 => 40,  173 => 35,  166 => 43,  152 => 74,  174 => 42,  164 => 39,  154 => 28,  150 => 39,  137 => 35,  133 => 23,  127 => 22,  107 => 26,  102 => 41,  83 => 16,  78 => 16,  53 => 12,  23 => 3,  42 => 7,  138 => 32,  134 => 34,  109 => 19,  103 => 18,  97 => 17,  94 => 37,  84 => 15,  75 => 14,  69 => 13,  66 => 13,  54 => 21,  44 => 10,  230 => 61,  226 => 58,  203 => 92,  193 => 38,  188 => 33,  182 => 42,  178 => 43,  168 => 40,  163 => 28,  160 => 42,  155 => 38,  148 => 45,  145 => 25,  140 => 38,  136 => 30,  125 => 29,  120 => 22,  113 => 50,  101 => 21,  92 => 21,  89 => 20,  85 => 15,  73 => 12,  62 => 12,  59 => 6,  56 => 10,  41 => 3,  126 => 30,  119 => 27,  111 => 25,  106 => 46,  98 => 20,  93 => 18,  86 => 22,  70 => 14,  60 => 11,  28 => 3,  36 => 3,  114 => 26,  104 => 23,  91 => 17,  80 => 20,  63 => 25,  58 => 23,  40 => 8,  34 => 7,  45 => 5,  61 => 11,  55 => 10,  48 => 7,  39 => 7,  35 => 5,  31 => 4,  26 => 4,  21 => 2,  46 => 11,  29 => 9,  57 => 13,  50 => 10,  47 => 5,  38 => 9,  33 => 2,  49 => 9,  32 => 4,  246 => 47,  236 => 62,  232 => 42,  225 => 40,  221 => 57,  216 => 94,  214 => 45,  211 => 55,  208 => 37,  205 => 35,  199 => 48,  196 => 35,  190 => 37,  179 => 39,  175 => 31,  172 => 42,  169 => 29,  162 => 77,  158 => 35,  153 => 36,  151 => 26,  147 => 28,  144 => 38,  141 => 32,  135 => 27,  129 => 30,  124 => 32,  117 => 26,  112 => 25,  90 => 19,  87 => 15,  82 => 17,  72 => 15,  68 => 26,  65 => 12,  52 => 10,  43 => 10,  37 => 6,  30 => 6,  27 => 3,  25 => 4,  24 => 3,  22 => 2,  19 => 1,);
    }
}
