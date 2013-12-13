<?php

/* AgentBundle:TicketSearch:part-results-simple-ext.html.twig */
class __TwigTemplate_1e9703f0cb2bd7ab72dc4f3e42a7fe5b extends \Application\DeskPRO\Twig\Template
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
        echo "<section class=\"search-reuslts page-set\" data-page=\"";
        if (isset($context["page"])) { $_page_ = $context["page"]; } else { $_page_ = null; }
        echo twig_escape_filter($this->env, $_page_, "html", null, true);
        echo "\">
";
        // line 2
        if (isset($context["tickets"])) { $_tickets_ = $context["tickets"]; } else { $_tickets_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_tickets_);
        foreach ($context['_seq'] as $context["_key"] => $context["ticket"]) {
            // line 3
            echo "
";
            // line 4
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            $context["ticket_person"] = $this->getAttribute($_ticket_, "person");
            // line 5
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            $context["ticket_agent"] = $this->getAttribute($_ticket_, "agent");
            // line 6
            echo "
<article
\tclass=\"row-item ticket-";
            // line 8
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "id"), "html", null, true);
            echo " ";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if ($this->getAttribute($_ticket_, "getSlaIds", array(), "method")) {
                echo "sla-status-";
                if (isset($context["sla_id"])) { $_sla_id_ = $context["sla_id"]; } else { $_sla_id_ = null; }
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, (($_sla_id_) ? ($this->getAttribute($this->getAttribute($_ticket_, "getSlaById", array(0 => $_sla_id_), "method"), "sla_status")) : ($this->getAttribute($_ticket_, "worst_sla_status"))), "html", null, true);
            }
            echo " ";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if (($this->getAttribute($_ticket_, "status") == "awaiting_agent")) {
                echo "urgency-";
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "urgency"), "html", null, true);
            }
            echo "\" data-ticket-id=\"";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "id"), "html", null, true);
            echo "\" data-route=\"ticket:";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_ticket_view", array("ticket_id" => $this->getAttribute($_ticket_, "id"))), "html", null, true);
            echo "\"
\tdata-route-title=\"@selector(a.subject)\"
\tdata-route-openclass=\"open\"
\tdata-ticket-lastactivity=\"";
            // line 11
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "getLastActivityDate", array(), "method"), "getTimestamp", array(), "method"), "html", null, true);
            echo "\"
>
\t<div class=\"item-hover-over-indicator\"></div>
\t<div class=\"loading-arrow\">";
            // line 14
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.loading");
            echo "</div>
\t<input type=\"checkbox\" class=\"item-select\" value=\"";
            // line 15
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "id"), "html", null, true);
            echo "\"/>
\t<div class=\"top-row\">
\t\t<div class=\"top-row-left\">
\t\t\t<h3>
\t\t\t\t<span class=\"obj-id\">#";
            // line 19
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "id"), "html", null, true);
            echo "</span>
\t\t\t\t<a class=\"subject click-through\">
\t\t\t\t\t";
            // line 21
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "subject"), "html", null, true);
            echo "
\t\t\t\t</a>
\t\t\t</h3>
\t\t</div>
\t\t<div class=\"top-row-right\">
\t\t\t<div class=\"bound-fade\"></div>
\t\t\t";
            // line 27
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if ($this->getAttribute($_ticket_, "is_hold")) {
                echo "<div class=\"on-hold\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.on_hold");
                echo "</div>";
            }
            // line 28
            echo "\t\t\t<div class=\"status-pill status urgency-";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "urgency"), "html", null, true);
            echo " ";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, strtr($this->getAttribute($_ticket_, "status_code"), array("." => "_")), "html", null, true);
            echo "\">
\t\t\t\t<label>";
            // line 29
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, ("agent.tickets.status_" . strtr($this->getAttribute($_ticket_, "status_code"), array("." => "_"))));
            echo "</label>
\t\t\t\t";
            // line 30
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if (($this->getAttribute($_ticket_, "status") == "awaiting_agent")) {
                echo "<i class=\"ticket-urgency\" data-urgency=\"";
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "urgency"), "html", null, true);
                echo "\">";
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "urgency"), "html", null, true);
                echo "</i>";
            }
            // line 31
            echo "\t\t\t</div>
\t\t\t";
            // line 32
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if ($this->getAttribute($_ticket_, "agent")) {
                // line 33
                echo "\t\t\t\t<span class=\"tipped agent-inline-icon\" title=\"";
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "agent"), "display_name"), "html", null, true);
                echo "\" style=\"background: url('";
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "agent"), "getPictureUrl", array(0 => 16), "method"), "html", null, true);
                echo "') no-repeat 0 50%; height: 16px; width: 16px;\"></span>
\t\t\t";
            }
            // line 35
            echo "\t\t\t";
            if (isset($context["ticket_display"])) { $_ticket_display_ = $context["ticket_display"]; } else { $_ticket_display_ = null; }
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if ($this->getAttribute($_ticket_display_, "getFlaggedColor", array(0 => $_ticket_), "method")) {
                // line 36
                echo "\t\t\t\t<i class=\"flag ";
                if (isset($context["ticket_display"])) { $_ticket_display_ = $context["ticket_display"]; } else { $_ticket_display_ = null; }
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_ticket_display_, "getFlaggedColor", array(0 => $_ticket_), "method"), "html", null, true);
                echo "\"></i>
\t\t\t";
            }
            // line 38
            echo "\t\t\t<br class=\"clear\" />
\t\t</div>
\t</div>
\t<div class=\"userinfo\">
\t\t<a class=\"name click-through\" data-route=\"person:";
            // line 42
            if (isset($context["ticket_person"])) { $_ticket_person_ = $context["ticket_person"]; } else { $_ticket_person_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_people_view", array("person_id" => (($this->getAttribute($_ticket_person_, "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_ticket_person_, "id"), "0")) : ("0")))), "html", null, true);
            echo "\" data-route-title=\"@selector(.person-tip)\">
\t\t\t<span class=\"with-icon\" data-person-id=\"";
            // line 43
            if (isset($context["ticket_person"])) { $_ticket_person_ = $context["ticket_person"]; } else { $_ticket_person_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($_ticket_person_, "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_ticket_person_, "id"), 0)) : (0)), "html", null, true);
            echo "\" style=\"background-image: url('";
            if (isset($context["ticket_person"])) { $_ticket_person_ = $context["ticket_person"]; } else { $_ticket_person_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_ticket_person_, "getPictureUrl", array(0 => 16), "method"), "html", null, true);
            echo "');\">";
            if (isset($context["ticket_person"])) { $_ticket_person_ = $context["ticket_person"]; } else { $_ticket_person_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_ticket_person_, "display_name"), "html", null, true);
            echo "</span></a>
\t\t";
            // line 44
            if (isset($context["ticket_person"])) { $_ticket_person_ = $context["ticket_person"]; } else { $_ticket_person_ = null; }
            if ($this->getAttribute($_ticket_person_, "organization")) {
                // line 45
                echo "\t\t\t<span class=\"org\">
\t\t\t\t(";
                // line 46
                if (isset($context["ticket_person"])) { $_ticket_person_ = $context["ticket_person"]; } else { $_ticket_person_ = null; }
                if ($this->getAttribute($_ticket_person_, "organization_position")) {
                    if (isset($context["ticket_person"])) { $_ticket_person_ = $context["ticket_person"]; } else { $_ticket_person_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_ticket_person_, "organization_position"), "html", null, true);
                    echo ", ";
                }
                if (isset($context["ticket_person"])) { $_ticket_person_ = $context["ticket_person"]; } else { $_ticket_person_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_person_, "organization"), "name"), "html", null, true);
                echo ")
\t\t\t</span>
\t\t";
            }
            // line 49
            echo "\t\t";
            if (isset($context["ticket_person"])) { $_ticket_person_ = $context["ticket_person"]; } else { $_ticket_person_ = null; }
            if ($this->getAttribute($_ticket_person_, "primary_email")) {
                // line 50
                echo "\t\t\t<span class=\"email\"><b class=\"person-";
                if (isset($context["ticket_person"])) { $_ticket_person_ = $context["ticket_person"]; } else { $_ticket_person_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_ticket_person_, "id"), "html", null, true);
                echo "-pemail\">";
                if (isset($context["ticket_person"])) { $_ticket_person_ = $context["ticket_person"]; } else { $_ticket_person_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_ticket_person_, "primary_email_address"), "html", null, true);
                echo "</b></span>
\t\t";
            }
            // line 52
            echo "\t\t<div class=\"bound-fade\"></div>
\t</div>
\t";
            // line 54
            if (isset($context["display_fields"])) { $_display_fields_ = $context["display_fields"]; } else { $_display_fields_ = null; }
            if (twig_length_filter($this->env, $_display_fields_)) {
                // line 55
                echo "\t\t";
                ob_start();
                // line 56
                echo "\t\t";
                if (isset($context["display_fields"])) { $_display_fields_ = $context["display_fields"]; } else { $_display_fields_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($_display_fields_);
                foreach ($context['_seq'] as $context["_key"] => $context["field"]) {
                    // line 57
                    echo "\t\t\t";
                    if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    if (isset($context["changed_fields"])) { $_changed_fields_ = $context["changed_fields"]; } else { $_changed_fields_ = null; }
                    if (isset($context["ticket_display"])) { $_ticket_display_ = $context["ticket_display"]; } else { $_ticket_display_ = null; }
                    if (($_field_ == "deleted_reason")) {
                        // line 58
                        echo "\t\t\t\t<li class=\"";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        echo twig_escape_filter($this->env, $_field_, "html", null, true);
                        echo "\">
\t\t\t\t\t";
                        // line 59
                        if (isset($context["deleted_tickets"])) { $_deleted_tickets_ = $context["deleted_tickets"]; } else { $_deleted_tickets_ = null; }
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if ($this->getAttribute($_deleted_tickets_, $this->getAttribute($_ticket_, "id"), array(), "array")) {
                            // line 60
                            echo "\t\t\t\t\t\t";
                            if (isset($context["deleted_tickets"])) { $_deleted_tickets_ = $context["deleted_tickets"]; } else { $_deleted_tickets_ = null; }
                            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.deleted_by", array("name" => $this->getAttribute($this->getAttribute($this->getAttribute($_deleted_tickets_, $this->getAttribute($_ticket_, "id"), array(), "array"), "by_person"), "display_name")));
                            echo "
\t\t\t\t\t\t";
                            // line 61
                            if (isset($context["deleted_tickets"])) { $_deleted_tickets_ = $context["deleted_tickets"]; } else { $_deleted_tickets_ = null; }
                            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                            if ($this->getAttribute($this->getAttribute($_deleted_tickets_, $this->getAttribute($_ticket_, "id"), array(), "array"), "reason")) {
                                // line 62
                                echo "\t\t\t\t\t\t\t(";
                                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.reason");
                                echo ": ";
                                if (isset($context["deleted_tickets"])) { $_deleted_tickets_ = $context["deleted_tickets"]; } else { $_deleted_tickets_ = null; }
                                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_deleted_tickets_, $this->getAttribute($_ticket_, "id"), array(), "array"), "reason"), "html", null, true);
                                echo ")
\t\t\t\t\t\t";
                            }
                            // line 64
                            echo "\t\t\t\t\t";
                        }
                        // line 65
                        echo "\t\t\t\t</li>
\t\t\t";
                    } elseif ((($_field_ == "department") && ($this->getAttribute($_ticket_, "department") || $this->getAttribute($this->getAttribute($_changed_fields_, $this->getAttribute($_ticket_, "id"), array(), "array"), "department")))) {
                        // line 67
                        echo "\t\t\t\t<li class=\"";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        echo twig_escape_filter($this->env, $_field_, "html", null, true);
                        echo " ";
                        if (isset($context["changed_fields"])) { $_changed_fields_ = $context["changed_fields"]; } else { $_changed_fields_ = null; }
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if ($this->getAttribute($this->getAttribute($_changed_fields_, $this->getAttribute($_ticket_, "id"), array(), "array"), "department")) {
                            echo "changed";
                        }
                        echo "\">
\t\t\t\t\t<span class=\"prop-title\">";
                        // line 68
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.department");
                        echo ":</span>
\t\t\t\t\t<span class=\"prop-val department_id\" data-prop-value=\"";
                        // line 69
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "department"), "id"), "html", null, true);
                        echo "\">
\t\t\t\t\t";
                        // line 70
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if ($this->getAttribute($_ticket_, "department")) {
                            // line 71
                            echo "\t\t\t\t\t\t";
                            if (isset($context["ticket_display"])) { $_ticket_display_ = $context["ticket_display"]; } else { $_ticket_display_ = null; }
                            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_ticket_display_, "getDepartmentName", array(0 => $_ticket_), "method"), "html", null, true);
                            echo "
\t\t\t\t\t";
                        } else {
                            // line 73
                            echo "\t\t\t\t\t\t";
                            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none");
                            echo "
\t\t\t\t\t";
                        }
                        // line 75
                        echo "\t\t\t\t\t</span>
\t\t\t\t</li>
\t\t\t";
                    } elseif (($_field_ == "ref")) {
                        // line 78
                        echo "\t\t\t\t<li class=\"";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        echo twig_escape_filter($this->env, $_field_, "html", null, true);
                        echo "\">
\t\t\t\t\t<span class=\"prop-title\">";
                        // line 79
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ref");
                        echo ":</span>
\t\t\t\t\t<span class=\"prop-val ref\" data-prop-value=\"";
                        // line 80
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "ref"), "html", null, true);
                        echo "\">";
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "ref"), "html", null, true);
                        echo "</span>
\t\t\t\t</li>
\t\t\t";
                    } elseif ((($_field_ == "language") && ($this->getAttribute($_ticket_, "language") || $this->getAttribute($this->getAttribute($_changed_fields_, $this->getAttribute($_ticket_, "id"), array(), "array"), "language")))) {
                        // line 83
                        echo "\t\t\t\t<li class=\"";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        echo twig_escape_filter($this->env, $_field_, "html", null, true);
                        echo " ";
                        if (isset($context["changed_fields"])) { $_changed_fields_ = $context["changed_fields"]; } else { $_changed_fields_ = null; }
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if ($this->getAttribute($this->getAttribute($_changed_fields_, $this->getAttribute($_ticket_, "id"), array(), "array"), "language")) {
                            echo "changed";
                        }
                        echo "\">
\t\t\t\t\t<span class=\"prop-title\">";
                        // line 84
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.language");
                        echo ":</span>
\t\t\t\t\t<span class=\"prop-val language_id\" data-prop-value=\"";
                        // line 85
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "language"), "id"), "html", null, true);
                        echo "\">
\t\t\t\t\t";
                        // line 86
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if ($this->getAttribute($_ticket_, "language")) {
                            // line 87
                            echo "\t\t\t\t\t\t";
                            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                            if ($this->getAttribute($this->getAttribute($_ticket_, "language"), "flag_image")) {
                                // line 88
                                echo "\t\t\t\t\t\t\t<span
\t\t\t\t\t\t\t\tstyle=\"background: url('";
                                // line 89
                                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                                echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl(("images/flags/" . $this->getAttribute($this->getAttribute($_ticket_, "language"), "flag_image"))), "html", null, true);
                                echo "') no-repeat 0 50%; height: 16px; padding-left: 19px;\"
\t\t\t\t\t\t\t>";
                                // line 90
                                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "language"), "title"), "html", null, true);
                                echo "</span>
\t\t\t\t\t\t";
                            } else {
                                // line 92
                                echo "\t\t\t\t\t\t\t";
                                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "language"), "title"), "html", null, true);
                                echo "
\t\t\t\t\t\t";
                            }
                            // line 94
                            echo "\t\t\t\t\t";
                        } else {
                            // line 95
                            echo "\t\t\t\t\t\t";
                            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none");
                            echo "
\t\t\t\t\t";
                        }
                        // line 97
                        echo "\t\t\t\t\t</span>
\t\t\t\t</li>
\t\t\t";
                    } elseif ((($_field_ == "category") && ($this->getAttribute($_ticket_, "category") || $this->getAttribute($this->getAttribute($_changed_fields_, $this->getAttribute($_ticket_, "id"), array(), "array"), "category")))) {
                        // line 100
                        echo "\t\t\t\t<li class=\"";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        echo twig_escape_filter($this->env, $_field_, "html", null, true);
                        echo " ";
                        if (isset($context["changed_fields"])) { $_changed_fields_ = $context["changed_fields"]; } else { $_changed_fields_ = null; }
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if ($this->getAttribute($this->getAttribute($_changed_fields_, $this->getAttribute($_ticket_, "id"), array(), "array"), "category")) {
                            echo "changed";
                        }
                        echo "\">
\t\t\t\t\t<span class=\"prop-title\">";
                        // line 101
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.category");
                        echo ":</span>
\t\t\t\t\t<span class=\"prop-val category_id\" data-prop-value=\"";
                        // line 102
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "category"), "id"), "html", null, true);
                        echo "\">
\t\t\t\t\t";
                        // line 103
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if ($this->getAttribute($_ticket_, "category")) {
                            // line 104
                            echo "\t\t\t\t\t\t";
                            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "category"), "title"), "html", null, true);
                            echo "
\t\t\t\t\t";
                        } else {
                            // line 106
                            echo "\t\t\t\t\t\t";
                            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none");
                            echo "
\t\t\t\t\t";
                        }
                        // line 108
                        echo "\t\t\t\t\t</span>
\t\t\t\t</li>
\t\t\t";
                    } elseif ((($_field_ == "priority") && ($this->getAttribute($_ticket_, "priority") || $this->getAttribute($this->getAttribute($_changed_fields_, $this->getAttribute($_ticket_, "id"), array(), "array"), "priority")))) {
                        // line 111
                        echo "\t\t\t\t<li class=\"";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        echo twig_escape_filter($this->env, $_field_, "html", null, true);
                        echo " ";
                        if (isset($context["changed_fields"])) { $_changed_fields_ = $context["changed_fields"]; } else { $_changed_fields_ = null; }
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if ($this->getAttribute($this->getAttribute($_changed_fields_, $this->getAttribute($_ticket_, "id"), array(), "array"), "priority")) {
                            echo "changed";
                        }
                        echo "\">
\t\t\t\t\t<span class=\"prop-title\">";
                        // line 112
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.priority");
                        echo ":</span>
\t\t\t\t\t<span class=\"prop-val priority_id\" data-prop-value=\"";
                        // line 113
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "priority"), "id"), "html", null, true);
                        echo "\">
\t\t\t\t\t";
                        // line 114
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if ($this->getAttribute($_ticket_, "priority")) {
                            // line 115
                            echo "\t\t\t\t\t\t";
                            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "priority"), "title"), "html", null, true);
                            echo "
\t\t\t\t\t";
                        } else {
                            // line 117
                            echo "\t\t\t\t\t\t";
                            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none");
                            echo "
\t\t\t\t\t";
                        }
                        // line 119
                        echo "\t\t\t\t\t</span>
\t\t\t\t</li>
\t\t\t";
                    } elseif ((($_field_ == "workflow") && ($this->getAttribute($_ticket_, "workflow") || $this->getAttribute($this->getAttribute($_changed_fields_, $this->getAttribute($_ticket_, "id"), array(), "array"), "workflow")))) {
                        // line 122
                        echo "\t\t\t\t<li class=\"";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        echo twig_escape_filter($this->env, $_field_, "html", null, true);
                        echo " ";
                        if (isset($context["changed_fields"])) { $_changed_fields_ = $context["changed_fields"]; } else { $_changed_fields_ = null; }
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if ($this->getAttribute($this->getAttribute($_changed_fields_, $this->getAttribute($_ticket_, "id"), array(), "array"), "workflow")) {
                            echo "changed";
                        }
                        echo "\">
\t\t\t\t\t<span class=\"prop-title\">";
                        // line 123
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.workflow");
                        echo ":</span>
\t\t\t\t\t<span class=\"prop-val workflow_id\" data-prop-value=\"";
                        // line 124
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "workflow"), "id"), "html", null, true);
                        echo "\">
\t\t\t\t\t";
                        // line 125
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if ($this->getAttribute($_ticket_, "workflow")) {
                            // line 126
                            echo "\t\t\t\t\t\t";
                            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "workflow"), "title"), "html", null, true);
                            echo "
\t\t\t\t\t";
                        } else {
                            // line 128
                            echo "\t\t\t\t\t\t";
                            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none");
                            echo "
\t\t\t\t\t";
                        }
                        // line 130
                        echo "\t\t\t\t\t</span>
\t\t\t\t</li>
\t\t\t";
                    } elseif (($_field_ == "agent")) {
                        // line 133
                        echo "\t\t\t\t<li class=\"";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        echo twig_escape_filter($this->env, $_field_, "html", null, true);
                        echo " ";
                        if (isset($context["changed_fields"])) { $_changed_fields_ = $context["changed_fields"]; } else { $_changed_fields_ = null; }
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if ($this->getAttribute($this->getAttribute($_changed_fields_, $this->getAttribute($_ticket_, "id"), array(), "array"), "agent")) {
                            echo "changed";
                        }
                        echo "\">
\t\t\t\t\t<span class=\"prop-title\">";
                        // line 134
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.agent");
                        echo ":</span>
\t\t\t\t\t<span class=\"prop-val\" data-prop-value=\"";
                        // line 135
                        if (isset($context["ticket_agent"])) { $_ticket_agent_ = $context["ticket_agent"]; } else { $_ticket_agent_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_agent_, "id"), "html", null, true);
                        echo "\">
\t\t\t\t\t";
                        // line 136
                        if (isset($context["ticket_agent"])) { $_ticket_agent_ = $context["ticket_agent"]; } else { $_ticket_agent_ = null; }
                        if ($_ticket_agent_) {
                            // line 137
                            echo "                        ";
                            if (isset($context["ticket_agent"])) { $_ticket_agent_ = $context["ticket_agent"]; } else { $_ticket_agent_ = null; }
                            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
                            if (($this->getAttribute($_ticket_agent_, "id") == $this->getAttribute($this->getAttribute($_app_, "user"), "id"))) {
                                // line 138
                                echo "                            ";
                                if (isset($context["ticket_agent"])) { $_ticket_agent_ = $context["ticket_agent"]; } else { $_ticket_agent_ = null; }
                                echo twig_escape_filter($this->env, $this->getAttribute($_ticket_agent_, "display_name"), "html", null, true);
                                echo "
                        ";
                            } else {
                                // line 140
                                echo "                            <a class=\"agent_link\">";
                                if (isset($context["ticket_agent"])) { $_ticket_agent_ = $context["ticket_agent"]; } else { $_ticket_agent_ = null; }
                                echo twig_escape_filter($this->env, $this->getAttribute($_ticket_agent_, "display_name"), "html", null, true);
                                echo "</a>
                        ";
                            }
                            // line 142
                            echo "\t\t\t\t\t";
                        } else {
                            // line 143
                            echo "\t\t\t\t\t\t";
                            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.unassigned");
                            echo "
\t\t\t\t\t";
                        }
                        // line 145
                        echo "\t\t\t\t\t</span>
\t\t\t\t</li>
\t\t\t";
                    } elseif ((($_field_ == "agent_team") && ($this->getAttribute($_ticket_, "agent_team") || $this->getAttribute($this->getAttribute($_changed_fields_, $this->getAttribute($_ticket_, "id"), array(), "array"), "agent_team")))) {
                        // line 148
                        echo "\t\t\t\t<li class=\"";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        echo twig_escape_filter($this->env, $_field_, "html", null, true);
                        echo " ";
                        if (isset($context["changed_fields"])) { $_changed_fields_ = $context["changed_fields"]; } else { $_changed_fields_ = null; }
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if ($this->getAttribute($this->getAttribute($_changed_fields_, $this->getAttribute($_ticket_, "id"), array(), "array"), "agent_team")) {
                            echo "changed";
                        }
                        echo "\">
\t\t\t\t\t<span class=\"prop-title\">";
                        // line 149
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.agent_team");
                        echo ":</span>
\t\t\t\t\t<span class=\"prop-val agent_team_id\" data-prop-value=\"";
                        // line 150
                        if (isset($context["ticket_agent_team"])) { $_ticket_agent_team_ = $context["ticket_agent_team"]; } else { $_ticket_agent_team_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_agent_team_, "id"), "html", null, true);
                        echo "\">
\t\t\t\t\t";
                        // line 151
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if ($this->getAttribute($_ticket_, "agent_team")) {
                            // line 152
                            echo "\t\t\t\t\t\t<span class=\"agent-team agent-team-";
                            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "agent_team"), "id"), "html", null, true);
                            echo "\" title=\"";
                            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "agent_team"), "name"), "html", null, true);
                            echo "\">";
                            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "agent_team"), "name"), "html", null, true);
                            echo "</span>
\t\t\t\t\t";
                        } else {
                            // line 154
                            echo "\t\t\t\t\t\t";
                            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.no_team");
                            echo "
\t\t\t\t\t";
                        }
                        // line 156
                        echo "\t\t\t\t\t</span>
\t\t\t\t</li>
\t\t\t";
                    } elseif ((($_field_ == "product") && ($this->getAttribute($_ticket_, "product") || $this->getAttribute($this->getAttribute($_changed_fields_, $this->getAttribute($_ticket_, "id"), array(), "array"), "product")))) {
                        // line 159
                        echo "\t\t\t\t<li class=\"";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        echo twig_escape_filter($this->env, $_field_, "html", null, true);
                        echo " ";
                        if (isset($context["changed_fields"])) { $_changed_fields_ = $context["changed_fields"]; } else { $_changed_fields_ = null; }
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if ($this->getAttribute($this->getAttribute($_changed_fields_, $this->getAttribute($_ticket_, "id"), array(), "array"), "product")) {
                            echo "changed";
                        }
                        echo "\">
\t\t\t\t\t<span class=\"prop-title\">";
                        // line 160
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.product");
                        echo ":</span>
\t\t\t\t\t<span class=\"prop-val product_id\" data-prop-value=\"";
                        // line 161
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "product"), "id"), "html", null, true);
                        echo "\">
\t\t\t\t\t";
                        // line 162
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if ($this->getAttribute($_ticket_, "product")) {
                            // line 163
                            echo "\t\t\t\t\t\t";
                            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "product"), "title"), "html", null, true);
                            echo "
\t\t\t\t\t";
                        } else {
                            // line 165
                            echo "\t\t\t\t\t\t";
                            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none");
                            echo "
\t\t\t\t\t";
                        }
                        // line 167
                        echo "\t\t\t\t\t</span>
\t\t\t\t</li>
\t\t\t";
                    } elseif ((($_field_ == "slas") && ($this->getAttribute($_ticket_display_, "hasTicketSlas", array(0 => $_ticket_), "method") || $this->getAttribute($this->getAttribute($_changed_fields_, $this->getAttribute($_ticket_, "id"), array(), "array"), "slas")))) {
                        // line 170
                        echo "\t\t\t\t";
                        if (isset($context["ticket_display"])) { $_ticket_display_ = $context["ticket_display"]; } else { $_ticket_display_ = null; }
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_ticket_display_, "getTicketSlas", array(0 => $_ticket_), "method"));
                        foreach ($context['_seq'] as $context["_key"] => $context["ticket_sla"]) {
                            // line 171
                            echo "\t\t\t\t\t<li class=\"";
                            if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                            echo twig_escape_filter($this->env, $_field_, "html", null, true);
                            echo " ";
                            if (isset($context["changed_fields"])) { $_changed_fields_ = $context["changed_fields"]; } else { $_changed_fields_ = null; }
                            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                            if ($this->getAttribute($this->getAttribute($_changed_fields_, $this->getAttribute($_ticket_, "id"), array(), "array"), "slas")) {
                                echo "changed";
                            }
                            echo "\">
\t\t\t\t\t\t<span class=\"prop-val sla-status-";
                            // line 172
                            if (isset($context["ticket_sla"])) { $_ticket_sla_ = $context["ticket_sla"]; } else { $_ticket_sla_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_ticket_sla_, "sla_status"), "html", null, true);
                            echo "\">
\t\t\t\t\t\t\t";
                            // line 173
                            if (isset($context["ticket_sla"])) { $_ticket_sla_ = $context["ticket_sla"]; } else { $_ticket_sla_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($_ticket_sla_, "title"), "html", null, true);
                            echo "
\t\t\t\t\t\t\t";
                            // line 174
                            if (isset($context["ticket_display"])) { $_ticket_display_ = $context["ticket_display"]; } else { $_ticket_display_ = null; }
                            if (isset($context["ticket_sla"])) { $_ticket_sla_ = $context["ticket_sla"]; } else { $_ticket_sla_ = null; }
                            if ($this->getAttribute($_ticket_display_, "getNextSlaTriggerDate", array(0 => $_ticket_sla_), "method")) {
                                // line 175
                                echo "\t\t\t\t\t\t\t\t(<time class=\"timeago\" datetime=\"";
                                if (isset($context["ticket_display"])) { $_ticket_display_ = $context["ticket_display"]; } else { $_ticket_display_ = null; }
                                if (isset($context["ticket_sla"])) { $_ticket_sla_ = $context["ticket_sla"]; } else { $_ticket_sla_ = null; }
                                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_display_, "getNextSlaTriggerDate", array(0 => $_ticket_sla_), "method"), "c", "UTC"), "html", null, true);
                                echo "\"></time>)
\t\t\t\t\t\t\t";
                            }
                            // line 177
                            echo "\t\t\t\t\t\t</span>
\t\t\t\t\t</li>
\t\t\t\t";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['ticket_sla'], $context['_parent'], $context['loop']);
                        $context = array_merge($_parent, array_intersect_key($context, $_parent));
                        // line 180
                        echo "\t\t\t";
                    } elseif ((($_field_ == "labels") && ($this->getAttribute($_ticket_display_, "hasTicketLabels", array(0 => $_ticket_), "method") || $this->getAttribute($this->getAttribute($_changed_fields_, $this->getAttribute($_ticket_, "id"), array(), "array"), "labels")))) {
                        // line 181
                        echo "\t\t\t\t<li class=\"";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        echo twig_escape_filter($this->env, $_field_, "html", null, true);
                        echo " ";
                        if (isset($context["changed_fields"])) { $_changed_fields_ = $context["changed_fields"]; } else { $_changed_fields_ = null; }
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if ($this->getAttribute($this->getAttribute($_changed_fields_, $this->getAttribute($_ticket_, "id"), array(), "array"), "labels")) {
                            echo "changed";
                        }
                        echo "\">
\t\t\t\t\t<span class=\"prop-title\">";
                        // line 182
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.labels");
                        echo ":</span>
\t\t\t\t\t<span class=\"prop-val labels\">
\t\t\t\t\t\t";
                        // line 184
                        if (isset($context["ticket_display"])) { $_ticket_display_ = $context["ticket_display"]; } else { $_ticket_display_ = null; }
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_ticket_display_, "getTicketLabels", array(0 => $_ticket_), "method"));
                        foreach ($context['_seq'] as $context["_key"] => $context["label"]) {
                            // line 185
                            echo "\t\t\t\t\t\t\t<span class=\"listing-tag\">";
                            if (isset($context["label"])) { $_label_ = $context["label"]; } else { $_label_ = null; }
                            echo twig_escape_filter($this->env, $_label_, "html", null, true);
                            echo "</span>
\t\t\t\t\t\t";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['label'], $context['_parent'], $context['loop']);
                        $context = array_merge($_parent, array_intersect_key($context, $_parent));
                        // line 187
                        echo "\t\t\t\t\t</span>
\t\t\t\t</li>
\t\t\t";
                    } elseif (($_field_ == "date_user_waiting")) {
                        // line 190
                        echo "\t\t\t\t<li class=\"";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        echo twig_escape_filter($this->env, $_field_, "html", null, true);
                        echo "\">
\t\t\t\t\t<span class=\"prop-title\">";
                        // line 191
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.user_waiting");
                        echo ":</span>
\t\t\t\t\t<span class=\"prop-val\">
\t\t\t\t\t\t";
                        // line 193
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if ($this->getAttribute($_ticket_, "date_user_waiting")) {
                            // line 194
                            echo "\t\t\t\t\t\t\t<time class=\"timeago timestamp\" data-timeago-no-ago=\"1\" datetime=\"";
                            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_user_waiting"), "c", "UTC"), "html", null, true);
                            echo "\"></time>
\t\t\t\t\t\t";
                        } else {
                            // line 196
                            echo "\t\t\t\t\t\t\t";
                            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.na");
                            echo "
\t\t\t\t\t\t";
                        }
                        // line 198
                        echo "\t\t\t\t\t</span>
\t\t\t\t</li>
\t\t\t";
                    } elseif (($_field_ == "date_created")) {
                        // line 201
                        echo "\t\t\t\t<li class=\"";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        echo twig_escape_filter($this->env, $_field_, "html", null, true);
                        echo "\">
\t\t\t\t\t<span class=\"prop-title\">";
                        // line 202
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.date_created");
                        echo ":</span>
\t\t\t\t\t<span class=\"prop-val\">
\t\t\t\t\t\t<time class=\"timeago timestamp\" datetime=\"";
                        // line 204
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_created"), "c", "UTC"), "html", null, true);
                        echo "\"></time>
\t\t\t\t\t</span>
\t\t\t\t</li>
\t\t\t";
                    } elseif (($_field_ == "date_last_agent_reply")) {
                        // line 208
                        echo "\t\t\t\t<li class=\"";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        echo twig_escape_filter($this->env, $_field_, "html", null, true);
                        echo "\">
\t\t\t\t\t<span class=\"prop-title\">";
                        // line 209
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.date_of_last_agent_reply");
                        echo ":</span>
\t\t\t\t\t<span class=\"prop-val\">
\t\t\t\t\t\t";
                        // line 211
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if ($this->getAttribute($_ticket_, "date_last_agent_reply")) {
                            // line 212
                            echo "\t\t\t\t\t\t\t<time class=\"timeago timestamp\" data-timeago-no-ago=\"1\" datetime=\"";
                            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_last_agent_reply"), "c", "UTC"), "html", null, true);
                            echo "\"></time>
\t\t\t\t\t\t";
                        } else {
                            // line 214
                            echo "\t\t\t\t\t\t\t";
                            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.na");
                            echo "
\t\t\t\t\t\t";
                        }
                        // line 216
                        echo "\t\t\t\t\t</span>
\t\t\t\t</li>
\t\t\t";
                    } elseif (($_field_ == "date_last_user_reply")) {
                        // line 219
                        echo "\t\t\t\t<li class=\"";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        echo twig_escape_filter($this->env, $_field_, "html", null, true);
                        echo "\">
\t\t\t\t\t<span class=\"prop-title\">";
                        // line 220
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.date_of_last_user_reply");
                        echo ":</span>
\t\t\t\t\t<span class=\"prop-val\">
\t\t\t\t\t\t";
                        // line 222
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if ($this->getAttribute($_ticket_, "date_last_user_reply")) {
                            // line 223
                            echo "\t\t\t\t\t\t\t<time class=\"timeago timestamp\" data-timeago-no-ago=\"1\" datetime=\"";
                            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_ticket_, "date_last_user_reply"), "c", "UTC"), "html", null, true);
                            echo "\"></time>
\t\t\t\t\t\t";
                        } else {
                            // line 225
                            echo "\t\t\t\t\t\t\t";
                            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.na");
                            echo "
\t\t\t\t\t\t";
                        }
                        // line 227
                        echo "\t\t\t\t\t</span>
\t\t\t\t</li>
\t\t\t";
                    } elseif (($_field_ == "total_user_waiting")) {
                        // line 230
                        echo "\t\t\t\t<li class=\"";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        echo twig_escape_filter($this->env, $_field_, "html", null, true);
                        echo "\">
\t\t\t\t\t<span class=\"prop-title\">";
                        // line 231
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.total_time_waiting");
                        echo ":</span>
\t\t\t\t\t<span class=\"prop-val\">
\t\t\t\t\t\t";
                        // line 233
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if ($this->getAttribute($_ticket_, "real_total_user_waiting")) {
                            // line 234
                            echo "\t\t\t\t\t\t\t<time class=\"timeago\" data-timeago-no-ago=\"1\" datetime=\"";
                            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, (("- " . $this->getAttribute($_ticket_, "real_total_user_waiting")) . " seconds"), "c", "UTC"), "html", null, true);
                            echo "\"></time>
\t\t\t\t\t\t";
                        } else {
                            // line 236
                            echo "\t\t\t\t\t\t\t";
                            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.na");
                            echo "
\t\t\t\t\t\t";
                        }
                        // line 238
                        echo "\t\t\t\t\t</span>
\t\t\t\t</li>
\t\t\t";
                    } elseif ((($_field_ == "organization") && $this->getAttribute($_ticket_, "organization"))) {
                        // line 241
                        echo "\t\t\t\t<li class=\"";
                        if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                        echo twig_escape_filter($this->env, $_field_, "html", null, true);
                        echo "\">
\t\t\t\t\t<span class=\"prop-title\">";
                        // line 242
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.organization");
                        echo ":</span>
\t\t\t\t\t<span class=\"prop-val organization_id\" data-prop-value=\"";
                        // line 243
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "organization"), "id"), "html", null, true);
                        echo "\">
\t\t\t\t\t";
                        // line 244
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if ($this->getAttribute($_ticket_, "organization")) {
                            // line 245
                            echo "\t\t\t\t\t\t<a class=\"with-route\" data-route=\"page:";
                            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_org_view", array("organization_id" => $this->getAttribute($this->getAttribute($_ticket_, "organization"), "id"))), "html", null, true);
                            echo "\" data-route-title=\"@text\">";
                            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "organization"), "name"), "html", null, true);
                            echo "</a>
\t\t\t\t\t";
                        } else {
                            // line 247
                            echo "\t\t\t\t\t\t";
                            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none");
                            echo "
\t\t\t\t\t";
                        }
                        // line 249
                        echo "\t\t\t\t\t</span>
\t\t\t\t</li>
\t\t\t";
                    } else {
                        // line 252
                        echo "\t\t\t\t";
                        if (isset($context["all_custom_fields"])) { $_all_custom_fields_ = $context["all_custom_fields"]; } else { $_all_custom_fields_ = null; }
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_all_custom_fields_, $this->getAttribute($_ticket_, "id"), array(), "array"));
                        foreach ($context['_seq'] as $context["_key"] => $context["f"]) {
                            // line 253
                            echo "\t\t\t\t\t";
                            if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                            if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                            if ((($_field_ == (("ticket_fields[" . $this->getAttribute($_f_, "id")) . "]")) && $this->getAttribute($_ticket_, "hasCustomField", array(0 => $this->getAttribute($_f_, "id")), "method"))) {
                                // line 254
                                echo "\t\t\t\t\t\t<li class=\"ticket-field\">
\t\t\t\t\t\t\t<span class=\"prop-title\">";
                                // line 255
                                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                                echo twig_escape_filter($this->env, $this->getAttribute($_f_, "title"), "html", null, true);
                                echo ":</span>
\t\t\t\t\t\t\t<span class=\"prop-val ticket_fields_";
                                // line 256
                                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                                echo twig_escape_filter($this->env, $this->getAttribute($_f_, "id"), "html", null, true);
                                echo "\">
\t\t\t\t\t\t\t\t";
                                // line 257
                                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                                echo $this->env->getExtension('deskpro_templating')->renderCustomField($_f_);
                                echo "
\t\t\t\t\t\t\t</span>
\t\t\t\t\t\t</li>
\t\t\t\t\t";
                            }
                            // line 261
                            echo "\t\t\t\t";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['f'], $context['_parent'], $context['loop']);
                        $context = array_merge($_parent, array_intersect_key($context, $_parent));
                        // line 262
                        echo "\t\t\t\t";
                        if (isset($context["user_all_custom_fields"])) { $_user_all_custom_fields_ = $context["user_all_custom_fields"]; } else { $_user_all_custom_fields_ = null; }
                        if (isset($context["ticket_person"])) { $_ticket_person_ = $context["ticket_person"]; } else { $_ticket_person_ = null; }
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_user_all_custom_fields_, $this->getAttribute($_ticket_person_, "id"), array(), "array"));
                        foreach ($context['_seq'] as $context["_key"] => $context["f"]) {
                            // line 263
                            echo "\t\t\t\t\t";
                            if (isset($context["field"])) { $_field_ = $context["field"]; } else { $_field_ = null; }
                            if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                            if (isset($context["ticket_person"])) { $_ticket_person_ = $context["ticket_person"]; } else { $_ticket_person_ = null; }
                            if ((($_field_ == (("person_fields[" . $this->getAttribute($_f_, "id")) . "]")) && $this->getAttribute($_ticket_person_, "hasCustomField", array(0 => $this->getAttribute($_f_, "id")), "method"))) {
                                // line 264
                                echo "\t\t\t\t\t\t<li class=\"person-field\">
\t\t\t\t\t\t\t<span class=\"prop-title\">";
                                // line 265
                                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                                echo twig_escape_filter($this->env, $this->getAttribute($_f_, "title"), "html", null, true);
                                echo ":</span>
\t\t\t\t\t\t\t<span class=\"prop-val person_fields_";
                                // line 266
                                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                                echo twig_escape_filter($this->env, $this->getAttribute($_f_, "id"), "html", null, true);
                                echo "\">
\t\t\t\t\t\t\t\t";
                                // line 267
                                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                                echo $this->env->getExtension('deskpro_templating')->renderCustomField($_f_);
                                echo "
\t\t\t\t\t\t\t</span>
\t\t\t\t\t\t</li>
\t\t\t\t\t";
                            }
                            // line 271
                            echo "\t\t\t\t";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['f'], $context['_parent'], $context['loop']);
                        $context = array_merge($_parent, array_intersect_key($context, $_parent));
                        // line 272
                        echo "\t\t\t";
                    }
                    // line 273
                    echo "\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['field'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 274
                echo "\t\t";
                $context["extra_list"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
                // line 275
                echo "\t\t";
                if (isset($context["extra_list"])) { $_extra_list_ = $context["extra_list"]; } else { $_extra_list_ = null; }
                if (twig_length_filter($this->env, $this->env->getExtension('deskpro_templating')->strTrim($_extra_list_))) {
                    // line 276
                    echo "\t\t\t<div class=\"extra-fields\">
\t\t\t\t<ul>
\t\t\t\t\t";
                    // line 278
                    if (isset($context["extra_list"])) { $_extra_list_ = $context["extra_list"]; } else { $_extra_list_ = null; }
                    echo $_extra_list_;
                    echo "
\t\t\t\t</ul>
\t\t\t\t<br class=\"clear\" />
\t\t\t</div>
\t\t";
                } else {
                    // line 283
                    echo "\t\t\t<div class=\"extra-fields\"></div>
\t\t";
                }
                // line 285
                echo "\t";
            }
            // line 286
            echo "</article>

";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['ticket'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 289
        echo "</section>
";
    }

    public function getTemplateName()
    {
        return "AgentBundle:TicketSearch:part-results-simple-ext.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1071 => 285,  1058 => 278,  1054 => 276,  1041 => 273,  1011 => 264,  869 => 223,  767 => 187,  3071 => 913,  3059 => 903,  3052 => 899,  3047 => 898,  3043 => 897,  3037 => 894,  3028 => 891,  3019 => 887,  3016 => 886,  3014 => 885,  3006 => 880,  2996 => 873,  2989 => 870,  2979 => 866,  2970 => 860,  2964 => 858,  2958 => 855,  2954 => 854,  2945 => 848,  2939 => 845,  2929 => 838,  2922 => 834,  2912 => 827,  2906 => 824,  2900 => 820,  2896 => 818,  2888 => 817,  2883 => 816,  2879 => 815,  2872 => 813,  2869 => 812,  2864 => 810,  2857 => 806,  2848 => 801,  2832 => 799,  2827 => 798,  2817 => 797,  2814 => 796,  2812 => 795,  2802 => 794,  2786 => 792,  2779 => 791,  2775 => 790,  2770 => 789,  2762 => 788,  2755 => 787,  2751 => 786,  2747 => 785,  2741 => 784,  2729 => 776,  2721 => 772,  2711 => 767,  2698 => 757,  2690 => 752,  2684 => 749,  2651 => 732,  2642 => 730,  2634 => 725,  2628 => 724,  2622 => 723,  2619 => 722,  2615 => 721,  2612 => 720,  2605 => 718,  2599 => 717,  2591 => 714,  2587 => 712,  2579 => 708,  2574 => 707,  2569 => 705,  2566 => 704,  2562 => 703,  2554 => 699,  2549 => 698,  2544 => 696,  2541 => 695,  2524 => 689,  2519 => 687,  2516 => 686,  2512 => 685,  2504 => 681,  2494 => 678,  2479 => 672,  2474 => 671,  2466 => 668,  2462 => 667,  2444 => 660,  2438 => 659,  2435 => 658,  2430 => 656,  2417 => 651,  2412 => 649,  2409 => 648,  2405 => 647,  2397 => 643,  2387 => 640,  2375 => 635,  2368 => 633,  2362 => 631,  2356 => 630,  2353 => 629,  2348 => 627,  2345 => 626,  2341 => 625,  2336 => 622,  2329 => 620,  2323 => 618,  2317 => 617,  2314 => 616,  2309 => 614,  2306 => 613,  2302 => 612,  2286 => 606,  2280 => 604,  2274 => 603,  2271 => 602,  2266 => 600,  2263 => 599,  2259 => 598,  2249 => 594,  2244 => 593,  2239 => 591,  2232 => 588,  2226 => 586,  2221 => 585,  2210 => 582,  2197 => 581,  2180 => 579,  2169 => 576,  2150 => 575,  2140 => 572,  2131 => 570,  2110 => 567,  2106 => 566,  2061 => 561,  2026 => 554,  2018 => 553,  2000 => 550,  1990 => 548,  1962 => 540,  1956 => 536,  1939 => 532,  1935 => 531,  1896 => 520,  1888 => 519,  1881 => 518,  1869 => 517,  1857 => 516,  1848 => 514,  1844 => 512,  1832 => 508,  1811 => 502,  1808 => 501,  1786 => 492,  1779 => 490,  1769 => 485,  1762 => 483,  1758 => 482,  1735 => 472,  1731 => 470,  1712 => 465,  1707 => 463,  1696 => 461,  1683 => 454,  1679 => 452,  1655 => 445,  1650 => 444,  1647 => 443,  1635 => 439,  1607 => 427,  1597 => 423,  1541 => 402,  1523 => 393,  1495 => 389,  1485 => 387,  1455 => 383,  1450 => 382,  1441 => 378,  1435 => 377,  1419 => 375,  1407 => 373,  1362 => 364,  1347 => 361,  1296 => 352,  1258 => 348,  1253 => 347,  1177 => 325,  1151 => 320,  1118 => 314,  1330 => 4,  1327 => 3,  1322 => 2,  1290 => 297,  1283 => 295,  1278 => 292,  1271 => 349,  1230 => 277,  1190 => 265,  1166 => 258,  1138 => 251,  642 => 147,  1264 => 464,  1259 => 462,  1227 => 448,  1211 => 336,  1399 => 568,  1387 => 560,  1358 => 555,  1334 => 357,  1316 => 529,  1298 => 520,  1195 => 478,  1148 => 253,  1120 => 453,  1117 => 245,  1093 => 440,  788 => 201,  612 => 203,  1394 => 446,  1385 => 442,  1382 => 441,  1369 => 433,  1363 => 432,  1360 => 431,  1350 => 429,  1305 => 523,  1300 => 409,  1281 => 404,  1277 => 350,  1265 => 398,  1255 => 396,  1247 => 393,  1234 => 387,  1202 => 270,  1199 => 374,  1187 => 264,  1162 => 365,  1136 => 317,  1128 => 352,  1122 => 248,  1069 => 299,  968 => 387,  846 => 188,  1183 => 449,  1132 => 460,  1097 => 341,  957 => 272,  907 => 278,  875 => 263,  653 => 176,  1329 => 405,  1309 => 354,  1303 => 399,  1297 => 398,  1292 => 397,  1280 => 394,  1249 => 346,  1237 => 279,  1205 => 409,  1200 => 408,  1194 => 440,  1178 => 430,  1175 => 447,  1170 => 375,  1135 => 362,  1105 => 428,  1078 => 335,  1068 => 388,  1048 => 417,  961 => 273,  922 => 280,  750 => 192,  842 => 263,  1038 => 272,  904 => 198,  882 => 227,  831 => 267,  860 => 314,  790 => 284,  733 => 230,  707 => 185,  744 => 220,  873 => 349,  824 => 267,  762 => 243,  713 => 225,  578 => 142,  2739 => 1153,  2735 => 1152,  2732 => 1151,  2725 => 1146,  2712 => 1144,  2707 => 1143,  2692 => 1131,  2688 => 1130,  2683 => 1127,  2680 => 748,  2677 => 1125,  2671 => 743,  2658 => 735,  2653 => 1118,  2645 => 731,  2641 => 1112,  2636 => 726,  2632 => 1108,  2629 => 1107,  2626 => 1106,  2606 => 1089,  2602 => 1088,  2598 => 1087,  2594 => 1086,  2590 => 1085,  2586 => 1084,  2572 => 1073,  2568 => 1072,  2564 => 1071,  2560 => 1070,  2556 => 1069,  2552 => 1068,  2537 => 694,  2533 => 1055,  2529 => 690,  2525 => 1053,  2521 => 1052,  2503 => 1040,  2499 => 680,  2495 => 1038,  2491 => 677,  2487 => 676,  2483 => 1035,  2469 => 669,  2465 => 1023,  2461 => 1022,  2457 => 664,  2453 => 1020,  2449 => 1019,  2426 => 999,  2422 => 652,  2418 => 997,  2414 => 996,  2410 => 995,  2406 => 994,  2392 => 642,  2388 => 982,  2384 => 639,  2380 => 638,  2376 => 979,  2372 => 978,  2358 => 967,  2354 => 966,  2350 => 965,  2346 => 964,  2342 => 963,  2338 => 962,  2324 => 951,  2320 => 950,  2316 => 949,  2312 => 948,  2308 => 947,  2304 => 946,  2295 => 939,  2282 => 937,  2277 => 936,  2269 => 931,  2265 => 930,  2260 => 927,  2256 => 597,  2253 => 925,  2230 => 901,  2220 => 899,  2216 => 898,  2211 => 896,  2208 => 895,  2198 => 893,  2194 => 580,  2189 => 890,  2184 => 889,  2177 => 578,  2173 => 577,  2164 => 877,  2151 => 875,  2146 => 574,  2143 => 873,  2141 => 864,  2134 => 571,  2130 => 859,  2122 => 853,  2120 => 852,  2113 => 848,  2105 => 846,  2096 => 839,  2083 => 564,  2078 => 836,  2073 => 562,  2067 => 830,  2062 => 828,  2058 => 827,  2054 => 826,  2050 => 560,  2045 => 557,  2038 => 822,  2034 => 821,  2022 => 814,  2009 => 812,  1999 => 809,  1993 => 805,  1988 => 803,  1984 => 802,  1980 => 801,  1976 => 546,  1971 => 799,  1968 => 798,  1964 => 797,  1960 => 796,  1954 => 535,  1946 => 788,  1942 => 533,  1938 => 786,  1932 => 530,  1927 => 780,  1919 => 527,  1915 => 777,  1911 => 776,  1906 => 775,  1903 => 774,  1899 => 773,  1895 => 772,  1886 => 768,  1883 => 764,  1871 => 759,  1866 => 756,  1863 => 755,  1842 => 752,  1825 => 749,  1818 => 748,  1815 => 503,  1800 => 499,  1793 => 741,  1772 => 729,  1761 => 721,  1757 => 720,  1751 => 717,  1740 => 709,  1719 => 697,  1715 => 696,  1686 => 673,  1676 => 669,  1665 => 661,  1661 => 446,  1645 => 442,  1638 => 649,  1630 => 438,  1625 => 435,  1622 => 641,  1598 => 637,  1577 => 416,  1558 => 408,  1549 => 405,  1546 => 626,  1535 => 618,  1531 => 617,  1514 => 606,  1510 => 605,  1504 => 391,  1493 => 594,  1483 => 590,  1464 => 580,  1458 => 577,  1443 => 568,  1437 => 565,  1426 => 557,  1405 => 545,  1401 => 544,  1395 => 541,  1390 => 538,  1381 => 368,  1373 => 531,  1348 => 521,  1343 => 520,  1335 => 515,  1321 => 510,  1318 => 549,  1299 => 503,  1294 => 298,  1282 => 496,  1269 => 466,  1260 => 397,  1240 => 344,  1221 => 339,  1216 => 338,  1210 => 272,  1206 => 445,  1193 => 467,  1189 => 466,  1155 => 468,  1150 => 419,  1022 => 312,  1006 => 299,  988 => 398,  969 => 392,  965 => 253,  921 => 286,  878 => 275,  866 => 222,  854 => 254,  819 => 322,  796 => 286,  1713 => 683,  1700 => 681,  1685 => 674,  1662 => 670,  1643 => 669,  1640 => 441,  1631 => 663,  1618 => 661,  1613 => 430,  1608 => 639,  1605 => 656,  1602 => 424,  1589 => 645,  1586 => 644,  1583 => 643,  1554 => 406,  1547 => 404,  1521 => 602,  1508 => 600,  1499 => 390,  1491 => 593,  1482 => 386,  1472 => 582,  1468 => 581,  1465 => 579,  1457 => 573,  1445 => 380,  1422 => 556,  1416 => 553,  1410 => 548,  1397 => 546,  1392 => 545,  1386 => 541,  1377 => 532,  1370 => 530,  1357 => 528,  1352 => 554,  1341 => 428,  1314 => 510,  1311 => 1,  1275 => 493,  1248 => 284,  1238 => 488,  1225 => 476,  1220 => 275,  1209 => 466,  1185 => 385,  1182 => 431,  1159 => 421,  1154 => 254,  1130 => 438,  1125 => 407,  1101 => 308,  1074 => 286,  1056 => 326,  1046 => 323,  1043 => 293,  1030 => 397,  1027 => 289,  947 => 247,  925 => 242,  913 => 259,  893 => 231,  881 => 253,  847 => 243,  829 => 209,  825 => 259,  1083 => 237,  995 => 399,  984 => 257,  963 => 292,  941 => 354,  851 => 367,  682 => 170,  1365 => 365,  1359 => 545,  1349 => 539,  1336 => 427,  1331 => 424,  1323 => 420,  1319 => 530,  1312 => 402,  1284 => 475,  1272 => 401,  1268 => 509,  1261 => 288,  1251 => 285,  1245 => 483,  1231 => 341,  1207 => 271,  1197 => 267,  1180 => 326,  1173 => 457,  1169 => 259,  1157 => 323,  1147 => 438,  1109 => 330,  1065 => 297,  1059 => 423,  1047 => 274,  1044 => 424,  1033 => 381,  1009 => 357,  991 => 363,  987 => 404,  973 => 294,  931 => 202,  924 => 261,  911 => 347,  906 => 81,  885 => 337,  872 => 335,  855 => 219,  749 => 240,  701 => 172,  594 => 180,  1163 => 257,  1143 => 318,  1087 => 420,  1077 => 300,  1051 => 325,  1037 => 223,  1010 => 301,  999 => 407,  932 => 352,  899 => 306,  895 => 404,  933 => 387,  914 => 238,  909 => 323,  833 => 284,  783 => 193,  755 => 303,  666 => 214,  453 => 168,  639 => 209,  568 => 176,  520 => 232,  657 => 184,  572 => 201,  609 => 202,  20 => 1,  659 => 217,  562 => 158,  548 => 180,  558 => 197,  479 => 121,  589 => 154,  457 => 112,  413 => 224,  953 => 249,  948 => 267,  935 => 394,  929 => 243,  916 => 382,  864 => 365,  844 => 214,  816 => 342,  807 => 212,  801 => 268,  774 => 257,  766 => 312,  737 => 297,  685 => 218,  664 => 163,  635 => 156,  593 => 199,  546 => 153,  532 => 236,  865 => 191,  852 => 241,  838 => 233,  820 => 182,  781 => 198,  764 => 193,  725 => 250,  632 => 268,  602 => 170,  565 => 145,  529 => 153,  505 => 123,  487 => 271,  473 => 212,  1853 => 515,  1849 => 753,  1839 => 751,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 733,  1778 => 732,  1770 => 807,  1749 => 478,  1738 => 793,  1728 => 469,  1721 => 788,  1717 => 787,  1709 => 693,  1705 => 781,  1688 => 675,  1681 => 769,  1677 => 451,  1667 => 449,  1656 => 762,  1648 => 655,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 413,  1545 => 719,  1534 => 606,  1527 => 395,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 593,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 372,  1400 => 370,  1383 => 540,  1379 => 557,  1356 => 632,  1351 => 362,  1338 => 359,  1332 => 617,  1315 => 613,  1302 => 353,  1289 => 406,  1276 => 604,  1263 => 601,  1244 => 456,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 332,  1181 => 561,  1171 => 446,  1158 => 550,  1114 => 428,  1104 => 465,  1072 => 333,  1062 => 7,  1024 => 267,  1014 => 265,  1000 => 368,  990 => 303,  980 => 393,  960 => 466,  918 => 285,  888 => 80,  834 => 211,  673 => 190,  636 => 145,  462 => 118,  454 => 127,  1144 => 463,  1139 => 356,  1131 => 316,  1127 => 434,  1110 => 312,  1092 => 459,  1089 => 239,  1086 => 238,  1084 => 337,  1063 => 232,  1060 => 231,  1055 => 295,  1050 => 275,  1035 => 291,  1019 => 266,  1003 => 401,  959 => 387,  900 => 366,  880 => 276,  870 => 250,  867 => 249,  859 => 294,  848 => 271,  839 => 376,  828 => 302,  823 => 208,  809 => 179,  800 => 241,  797 => 267,  794 => 177,  786 => 194,  740 => 194,  734 => 181,  703 => 228,  693 => 297,  630 => 166,  626 => 176,  614 => 163,  610 => 172,  581 => 143,  564 => 138,  525 => 138,  722 => 226,  697 => 282,  674 => 270,  671 => 165,  577 => 180,  569 => 187,  557 => 179,  502 => 187,  497 => 76,  445 => 125,  729 => 306,  684 => 180,  676 => 178,  669 => 268,  660 => 203,  647 => 175,  643 => 229,  601 => 195,  570 => 129,  522 => 132,  501 => 147,  296 => 63,  374 => 88,  631 => 179,  616 => 152,  608 => 150,  605 => 193,  596 => 134,  574 => 163,  561 => 126,  527 => 165,  433 => 104,  388 => 92,  426 => 97,  383 => 105,  461 => 137,  370 => 87,  395 => 94,  294 => 81,  223 => 40,  220 => 67,  492 => 129,  468 => 119,  444 => 149,  410 => 94,  397 => 90,  377 => 89,  262 => 61,  250 => 106,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 793,  2649 => 2234,  2625 => 2213,  2517 => 1051,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 662,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 608,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 568,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 543,  1826 => 506,  1819 => 504,  1732 => 1414,  1723 => 467,  1695 => 680,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 638,  1533 => 397,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 572,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 526,  1301 => 1037,  1252 => 392,  1243 => 283,  1223 => 978,  1212 => 494,  1167 => 366,  1161 => 933,  1145 => 923,  1140 => 413,  1134 => 409,  1126 => 443,  1106 => 528,  1100 => 897,  1026 => 220,  997 => 367,  983 => 298,  975 => 361,  956 => 318,  939 => 264,  902 => 274,  894 => 364,  879 => 76,  757 => 185,  727 => 293,  716 => 226,  670 => 204,  528 => 187,  476 => 123,  435 => 121,  354 => 89,  341 => 56,  192 => 45,  321 => 75,  243 => 75,  793 => 196,  780 => 247,  758 => 229,  700 => 193,  686 => 294,  652 => 160,  638 => 269,  620 => 165,  545 => 243,  523 => 140,  494 => 274,  459 => 156,  438 => 104,  351 => 78,  347 => 83,  402 => 99,  268 => 72,  430 => 103,  411 => 101,  379 => 95,  322 => 70,  315 => 73,  289 => 78,  284 => 86,  255 => 60,  234 => 60,  1133 => 444,  1124 => 455,  1121 => 430,  1116 => 405,  1113 => 429,  1108 => 376,  1103 => 444,  1098 => 401,  1081 => 320,  1073 => 235,  1067 => 283,  1064 => 330,  1061 => 421,  1053 => 318,  1049 => 294,  1042 => 313,  1039 => 384,  1025 => 304,  1021 => 219,  1015 => 308,  1008 => 284,  996 => 406,  989 => 277,  985 => 395,  981 => 296,  977 => 321,  970 => 275,  966 => 274,  955 => 293,  952 => 464,  943 => 266,  936 => 353,  930 => 289,  919 => 241,  917 => 348,  908 => 236,  905 => 363,  896 => 275,  891 => 338,  877 => 334,  862 => 248,  857 => 271,  837 => 212,  832 => 260,  827 => 184,  821 => 266,  803 => 179,  778 => 191,  769 => 253,  765 => 201,  753 => 171,  746 => 182,  743 => 297,  735 => 168,  730 => 187,  720 => 305,  717 => 165,  712 => 186,  691 => 219,  678 => 275,  654 => 199,  587 => 145,  576 => 167,  539 => 200,  517 => 126,  471 => 262,  441 => 123,  437 => 114,  418 => 99,  386 => 107,  373 => 120,  304 => 70,  270 => 69,  265 => 81,  229 => 55,  477 => 167,  455 => 70,  448 => 110,  429 => 235,  407 => 120,  399 => 111,  389 => 87,  375 => 83,  358 => 84,  349 => 137,  335 => 41,  327 => 60,  298 => 98,  280 => 70,  249 => 39,  194 => 49,  142 => 27,  344 => 77,  318 => 57,  306 => 102,  295 => 68,  357 => 101,  300 => 82,  286 => 63,  276 => 64,  269 => 97,  254 => 50,  128 => 30,  237 => 64,  165 => 34,  122 => 29,  798 => 256,  770 => 309,  759 => 278,  748 => 298,  731 => 180,  721 => 227,  718 => 188,  708 => 185,  696 => 236,  617 => 164,  590 => 166,  553 => 145,  550 => 157,  540 => 161,  533 => 182,  500 => 135,  493 => 122,  489 => 202,  482 => 117,  467 => 113,  464 => 129,  458 => 118,  452 => 197,  449 => 112,  415 => 92,  382 => 90,  372 => 82,  361 => 101,  356 => 98,  339 => 120,  302 => 67,  285 => 71,  258 => 41,  123 => 28,  108 => 24,  424 => 108,  394 => 89,  380 => 105,  338 => 71,  319 => 79,  316 => 78,  312 => 56,  290 => 73,  267 => 68,  206 => 36,  110 => 27,  240 => 74,  224 => 33,  219 => 38,  217 => 56,  202 => 36,  186 => 33,  170 => 44,  100 => 23,  67 => 12,  14 => 1,  1096 => 345,  1090 => 339,  1088 => 397,  1085 => 456,  1066 => 233,  1034 => 282,  1031 => 290,  1018 => 303,  1013 => 302,  1007 => 408,  1002 => 403,  993 => 279,  986 => 212,  982 => 211,  976 => 399,  971 => 254,  964 => 208,  949 => 289,  946 => 288,  940 => 388,  937 => 245,  928 => 262,  926 => 318,  915 => 284,  912 => 82,  903 => 231,  898 => 233,  892 => 255,  889 => 277,  887 => 230,  884 => 79,  876 => 225,  874 => 193,  871 => 331,  863 => 345,  861 => 220,  858 => 247,  850 => 216,  843 => 270,  840 => 186,  815 => 204,  812 => 263,  808 => 323,  804 => 201,  799 => 198,  791 => 202,  785 => 200,  775 => 313,  771 => 245,  754 => 267,  728 => 167,  726 => 190,  723 => 177,  715 => 175,  711 => 174,  709 => 222,  706 => 173,  698 => 182,  694 => 182,  692 => 161,  689 => 171,  681 => 224,  677 => 167,  675 => 234,  663 => 213,  661 => 162,  650 => 213,  646 => 231,  629 => 154,  627 => 180,  625 => 266,  622 => 202,  598 => 157,  592 => 148,  586 => 175,  575 => 189,  566 => 251,  556 => 136,  554 => 158,  541 => 152,  536 => 142,  515 => 79,  511 => 208,  509 => 124,  488 => 119,  486 => 145,  483 => 135,  465 => 118,  463 => 112,  450 => 107,  432 => 125,  419 => 65,  371 => 154,  362 => 85,  353 => 73,  337 => 93,  333 => 83,  309 => 55,  303 => 53,  299 => 69,  291 => 64,  272 => 46,  261 => 49,  253 => 30,  239 => 61,  235 => 51,  213 => 38,  200 => 55,  198 => 51,  159 => 36,  149 => 36,  146 => 31,  131 => 30,  116 => 25,  79 => 14,  74 => 18,  71 => 11,  836 => 262,  817 => 243,  814 => 321,  811 => 320,  805 => 244,  787 => 257,  779 => 169,  776 => 232,  773 => 322,  761 => 199,  751 => 184,  747 => 191,  742 => 190,  739 => 189,  736 => 215,  724 => 259,  705 => 69,  702 => 216,  688 => 193,  680 => 205,  667 => 273,  662 => 282,  656 => 161,  649 => 182,  644 => 181,  641 => 168,  624 => 162,  613 => 151,  607 => 171,  597 => 260,  591 => 170,  584 => 236,  579 => 132,  563 => 212,  559 => 137,  551 => 135,  547 => 134,  537 => 160,  524 => 128,  512 => 137,  507 => 237,  504 => 149,  498 => 129,  485 => 126,  480 => 134,  472 => 114,  466 => 138,  460 => 254,  447 => 107,  442 => 128,  434 => 133,  428 => 102,  422 => 118,  404 => 97,  368 => 80,  364 => 75,  340 => 94,  334 => 163,  330 => 61,  325 => 90,  292 => 50,  287 => 51,  282 => 62,  279 => 65,  273 => 73,  266 => 62,  256 => 73,  252 => 64,  228 => 90,  218 => 57,  201 => 51,  64 => 7,  51 => 12,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 569,  2109 => 847,  2104 => 802,  2101 => 801,  2087 => 565,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 823,  2037 => 789,  2031 => 555,  2028 => 818,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 811,  2001 => 1668,  1996 => 771,  1994 => 549,  1987 => 766,  1983 => 547,  1979 => 764,  1972 => 761,  1969 => 544,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 534,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 528,  1923 => 779,  1916 => 736,  1912 => 735,  1908 => 522,  1901 => 521,  1897 => 730,  1894 => 729,  1889 => 769,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 511,  1835 => 509,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 500,  1801 => 695,  1798 => 694,  1795 => 498,  1789 => 814,  1776 => 489,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 481,  1750 => 668,  1745 => 667,  1742 => 476,  1739 => 665,  1736 => 708,  1730 => 705,  1720 => 688,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 672,  1678 => 644,  1671 => 450,  1666 => 640,  1663 => 447,  1660 => 763,  1657 => 659,  1651 => 656,  1641 => 631,  1637 => 440,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 651,  1592 => 614,  1587 => 613,  1584 => 635,  1581 => 731,  1576 => 608,  1574 => 633,  1567 => 603,  1563 => 410,  1559 => 601,  1552 => 628,  1548 => 597,  1542 => 593,  1529 => 605,  1525 => 614,  1516 => 708,  1512 => 583,  1503 => 599,  1490 => 388,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 384,  1447 => 569,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 374,  1406 => 532,  1393 => 564,  1388 => 537,  1380 => 539,  1376 => 367,  1371 => 366,  1367 => 527,  1364 => 518,  1361 => 523,  1355 => 543,  1342 => 360,  1337 => 510,  1328 => 356,  1324 => 550,  1317 => 511,  1313 => 499,  1310 => 526,  1304 => 504,  1291 => 479,  1286 => 476,  1279 => 486,  1274 => 468,  1270 => 483,  1266 => 487,  1262 => 490,  1257 => 500,  1254 => 286,  1250 => 394,  1246 => 345,  1239 => 389,  1235 => 343,  1232 => 416,  1226 => 383,  1213 => 273,  1208 => 481,  1201 => 443,  1196 => 335,  1192 => 490,  1188 => 456,  1184 => 263,  1179 => 448,  1176 => 261,  1172 => 428,  1168 => 451,  1164 => 450,  1160 => 324,  1153 => 561,  1149 => 359,  1146 => 444,  1137 => 249,  1123 => 442,  1119 => 354,  1115 => 313,  1111 => 377,  1107 => 242,  1102 => 344,  1099 => 347,  1095 => 307,  1091 => 321,  1082 => 289,  1079 => 524,  1076 => 393,  1070 => 431,  1057 => 313,  1052 => 406,  1045 => 484,  1040 => 224,  1036 => 283,  1032 => 271,  1028 => 312,  1023 => 288,  1020 => 311,  1016 => 285,  1012 => 390,  1005 => 263,  1001 => 304,  998 => 262,  992 => 261,  979 => 256,  974 => 255,  967 => 399,  962 => 397,  958 => 252,  954 => 293,  950 => 292,  945 => 391,  942 => 290,  938 => 375,  934 => 244,  927 => 288,  923 => 201,  920 => 369,  910 => 365,  901 => 234,  897 => 256,  890 => 271,  886 => 270,  883 => 353,  868 => 273,  856 => 293,  853 => 341,  849 => 264,  845 => 329,  841 => 249,  835 => 268,  830 => 333,  826 => 226,  822 => 281,  818 => 265,  813 => 215,  810 => 202,  806 => 261,  802 => 210,  795 => 241,  792 => 335,  789 => 249,  784 => 286,  782 => 237,  777 => 255,  772 => 190,  768 => 195,  763 => 327,  760 => 305,  756 => 248,  752 => 198,  745 => 245,  741 => 218,  738 => 241,  732 => 171,  719 => 288,  714 => 251,  710 => 164,  704 => 184,  699 => 183,  695 => 195,  690 => 226,  687 => 210,  683 => 156,  679 => 191,  672 => 153,  668 => 187,  665 => 151,  658 => 177,  645 => 170,  640 => 159,  634 => 206,  628 => 166,  623 => 179,  619 => 174,  611 => 158,  606 => 234,  603 => 156,  599 => 174,  595 => 169,  583 => 169,  580 => 164,  573 => 148,  560 => 160,  543 => 175,  538 => 174,  534 => 138,  530 => 130,  526 => 170,  521 => 139,  518 => 194,  514 => 125,  510 => 132,  503 => 141,  496 => 202,  490 => 129,  484 => 128,  474 => 133,  470 => 131,  446 => 108,  440 => 106,  436 => 62,  431 => 113,  425 => 102,  416 => 117,  412 => 115,  408 => 93,  403 => 92,  400 => 91,  396 => 110,  392 => 88,  385 => 97,  381 => 85,  367 => 86,  363 => 89,  359 => 79,  355 => 76,  350 => 96,  346 => 83,  343 => 72,  328 => 93,  324 => 59,  313 => 81,  307 => 71,  301 => 74,  288 => 72,  283 => 67,  271 => 58,  257 => 68,  251 => 59,  238 => 57,  233 => 50,  195 => 46,  191 => 48,  187 => 20,  183 => 21,  130 => 31,  88 => 16,  76 => 14,  115 => 20,  95 => 22,  655 => 148,  651 => 275,  648 => 171,  637 => 180,  633 => 167,  621 => 462,  618 => 241,  615 => 173,  604 => 149,  600 => 233,  588 => 206,  585 => 153,  582 => 153,  571 => 140,  567 => 161,  555 => 125,  552 => 141,  549 => 154,  544 => 179,  542 => 139,  535 => 133,  531 => 139,  519 => 80,  516 => 218,  513 => 154,  508 => 117,  506 => 131,  499 => 139,  495 => 125,  491 => 146,  481 => 215,  478 => 124,  475 => 115,  469 => 182,  456 => 135,  451 => 111,  443 => 118,  439 => 242,  427 => 60,  423 => 96,  420 => 109,  409 => 100,  405 => 99,  401 => 56,  391 => 62,  387 => 86,  384 => 83,  378 => 84,  365 => 79,  360 => 102,  348 => 170,  336 => 80,  332 => 79,  329 => 119,  323 => 116,  310 => 76,  305 => 83,  277 => 47,  274 => 68,  263 => 105,  259 => 67,  247 => 63,  244 => 64,  241 => 63,  222 => 52,  210 => 37,  207 => 47,  204 => 52,  184 => 45,  181 => 46,  167 => 38,  157 => 27,  96 => 21,  421 => 101,  417 => 150,  414 => 145,  406 => 113,  398 => 95,  393 => 53,  390 => 109,  376 => 108,  369 => 148,  366 => 186,  352 => 128,  345 => 65,  342 => 64,  331 => 154,  326 => 78,  320 => 77,  317 => 69,  314 => 86,  311 => 69,  308 => 84,  297 => 51,  293 => 65,  281 => 57,  278 => 59,  275 => 70,  264 => 55,  260 => 66,  248 => 48,  245 => 58,  242 => 53,  231 => 42,  227 => 53,  215 => 60,  212 => 50,  209 => 47,  197 => 61,  177 => 45,  171 => 49,  161 => 33,  132 => 34,  121 => 29,  105 => 24,  99 => 42,  81 => 28,  77 => 16,  180 => 20,  176 => 36,  156 => 32,  143 => 39,  139 => 24,  118 => 28,  189 => 44,  185 => 40,  173 => 42,  166 => 43,  152 => 74,  174 => 42,  164 => 39,  154 => 35,  150 => 39,  137 => 35,  133 => 23,  127 => 30,  107 => 26,  102 => 41,  83 => 16,  78 => 14,  53 => 12,  23 => 3,  42 => 7,  138 => 31,  134 => 34,  109 => 19,  103 => 18,  97 => 17,  94 => 37,  84 => 15,  75 => 14,  69 => 13,  66 => 13,  54 => 21,  44 => 10,  230 => 61,  226 => 54,  203 => 92,  193 => 38,  188 => 33,  182 => 42,  178 => 43,  168 => 40,  163 => 28,  160 => 42,  155 => 38,  148 => 45,  145 => 25,  140 => 38,  136 => 30,  125 => 29,  120 => 22,  113 => 28,  101 => 21,  92 => 21,  89 => 20,  85 => 15,  73 => 12,  62 => 12,  59 => 6,  56 => 10,  41 => 3,  126 => 30,  119 => 27,  111 => 25,  106 => 27,  98 => 20,  93 => 18,  86 => 22,  70 => 14,  60 => 11,  28 => 3,  36 => 5,  114 => 26,  104 => 23,  91 => 17,  80 => 20,  63 => 25,  58 => 23,  40 => 8,  34 => 7,  45 => 5,  61 => 11,  55 => 10,  48 => 7,  39 => 6,  35 => 5,  31 => 4,  26 => 4,  21 => 2,  46 => 11,  29 => 9,  57 => 13,  50 => 10,  47 => 5,  38 => 9,  33 => 4,  49 => 9,  32 => 4,  246 => 47,  236 => 62,  232 => 56,  225 => 40,  221 => 57,  216 => 94,  214 => 45,  211 => 55,  208 => 49,  205 => 35,  199 => 48,  196 => 35,  190 => 37,  179 => 39,  175 => 31,  172 => 42,  169 => 29,  162 => 77,  158 => 35,  153 => 36,  151 => 26,  147 => 28,  144 => 33,  141 => 32,  135 => 27,  129 => 30,  124 => 32,  117 => 26,  112 => 25,  90 => 19,  87 => 15,  82 => 15,  72 => 15,  68 => 26,  65 => 12,  52 => 10,  43 => 8,  37 => 6,  30 => 3,  27 => 3,  25 => 2,  24 => 3,  22 => 2,  19 => 1,);
    }
}
