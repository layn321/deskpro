<?php

/* AgentBundle:Settings:ticket-notifications.html.twig */
class __TwigTemplate_ca5e94029c95fb253f56fecf7937b70b extends \Application\DeskPRO\Twig\Template
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
        echo "<script>
pageMeta.fragmentClass = 'DeskPRO.Agent.PageFragment.SettingsPage.TicketNotifications';
</script>
<form action=\"";
        // line 4
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("agent_settings_ticketnotif_save"), "html", null, true);
        echo "\" method=\"post\">

\t<header class=\"pageheader\">
\t\t<nav>
\t\t\t<ul>
\t\t\t\t<li class=\"on\" data-tab-for=\"#settingswin_ticket_notifications_email\">";
        // line 9
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.email_notifications");
        echo "</li>
\t\t\t\t<li data-tab-for=\"#settingswin_ticket_notifications_alert\">";
        // line 10
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.browser_notifications");
        echo "</li>
\t\t\t</ul>
\t\t</nav>
\t</header>

\t<div style=\"padding: 10px;\">
\t\t<div id=\"settingswin_ticket_notifications_email\">

\t\t\t";
        // line 18
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent_notif.no_allow_set_email"), "method")) {
            // line 19
            echo "\t\t\t\t<p>
\t\t\t\t\t";
            // line 20
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_contact_admin");
            echo "
\t\t\t\t</p>
\t\t\t";
        } else {
            // line 23
            echo "\t\t\t\t<div class=\"check-grid\">
\t\t\t\t\t<header>";
            // line 24
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.inbox");
            echo "</header>
\t\t\t\t\t<table width=\"100%\">
\t\t\t\t\t\t<thead>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<th width=\"180\">&nbsp;</th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
            // line 29
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_type_new");
            echo "</th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
            // line 30
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_type_assign");
            echo "</th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
            // line 31
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_type_user");
            echo "</th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
            // line 32
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_type_agent");
            echo "</th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
            // line 33
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_type_note");
            echo "</th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
            // line 34
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_type_change");
            echo "</th>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t</thead>
\t\t\t\t\t\t<tbody>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td class=\"title\" style=\"text-align: right;\">";
            // line 39
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_group_my");
            echo "</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[1][email_created]\" value=\"1\" ";
            // line 41
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 1, array(), "array"), "email_created")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a new ticket is created and assigned to me\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[1][email_new]\" value=\"1\" ";
            // line 45
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 1, array(), "array"), "email_new")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a ticket is assigned to me\"></span>
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[1][email_leave]\" value=\"1\" ";
            // line 47
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 1, array(), "array"), "email_leave")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when I am unassigned from a ticket\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[1][email_user_activity]\" value=\"1\" ";
            // line 51
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 1, array(), "array"), "email_user_activity")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a user replies to a ticket assigned to me\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[1][email_agent_activity]\" value=\"1\" ";
            // line 55
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 1, array(), "array"), "email_agent_activity")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when another agent replies to a ticket assigned to me\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[1][email_agent_note]\" value=\"1\" ";
            // line 59
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 1, array(), "array"), "email_agent_note")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when another agent adds a note to a ticket assigned to me\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[1][email_property_change]\" value=\"1\" ";
            // line 63
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 1, array(), "array"), "email_property_change")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me of any change to a ticked assigned to me\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t</tr>

\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td class=\"title\" style=\"text-align: right;\">";
            // line 69
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_group_follow");
            echo "</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[3][email_created]\" value=\"1\" ";
            // line 71
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 3, array(), "array"), "email_created")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a new ticket is created and I am added as a follower\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[3][email_new]\" value=\"1\" ";
            // line 75
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 3, array(), "array"), "email_new")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when I am added as a follower to a ticket\"></span>
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[3][email_leave]\" value=\"1\" ";
            // line 77
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 3, array(), "array"), "email_leave")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when I am removed as a follower\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[3][email_user_activity]\" value=\"1\" ";
            // line 81
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 3, array(), "array"), "email_user_activity")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a user replies to a ticket I follow\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[3][email_agent_activity]\" value=\"1\" ";
            // line 85
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 3, array(), "array"), "email_agent_activity")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when an agent replies to a ticket I follow\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[3][email_agent_note]\" value=\"1\" ";
            // line 89
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 3, array(), "array"), "email_agent_note")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when an agent adds a note to a ticket I follow\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[3][email_property_change]\" value=\"1\" ";
            // line 93
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 3, array(), "array"), "email_property_change")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me of any change to a ticket I follow\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t</tr>

\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td class=\"title\" style=\"text-align: right;\">";
            // line 99
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_group_team");
            echo "</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[2][email_created]\" value=\"1\" ";
            // line 101
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 2, array(), "array"), "email_created")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a new ticket is created that is assigned to one of my teams\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[2][email_new]\" value=\"1\" ";
            // line 105
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 2, array(), "array"), "email_new")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a ticket is assigned to one of my teams\"></span>
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[2][email_leave]\" value=\"1\" ";
            // line 107
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 2, array(), "array"), "email_leave")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a ticket is unassigned from my teams\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[2][email_user_activity]\" value=\"1\" ";
            // line 111
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 2, array(), "array"), "email_user_activity")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a user replies to a ticket assigned to one of my teams\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[2][email_agent_activity]\" value=\"1\" ";
            // line 115
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 2, array(), "array"), "email_agent_activity")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when an agent replies to a ticket assigned to one of my teams\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[2][email_agent_note]\" value=\"1\" ";
            // line 119
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 2, array(), "array"), "email_agent_note")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when an agent adds a note to a ticket assigned to one of my teams\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[2][email_property_change]\" value=\"1\" ";
            // line 123
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 2, array(), "array"), "email_property_change")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me of any change to a ticket assigned to one of my teams\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t</tr>

\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td class=\"title\" style=\"text-align: right;\">";
            // line 129
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_group_unassigned");
            echo "</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[4][email_created]\" value=\"1\" ";
            // line 131
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 4, array(), "array"), "email_created")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a new ticket is created that is not assigned to any agent or team\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[4][email_new]\" value=\"1\" ";
            // line 135
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 4, array(), "array"), "email_new")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a ticket becomes unassigned\"></span>
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[4][email_leave]\" value=\"1\" ";
            // line 137
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 4, array(), "array"), "email_leave")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when an unassigned ticket becomes assigned\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[4][email_user_activity]\" value=\"1\" ";
            // line 141
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 4, array(), "array"), "email_user_activity")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a user replies to an unassigned ticket\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[4][email_agent_activity]\" value=\"1\" ";
            // line 145
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 4, array(), "array"), "email_agent_activity")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when an agent replies to an unassigned ticket\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[4][email_agent_note]\" value=\"1\" ";
            // line 149
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 4, array(), "array"), "email_agent_note")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when an agent adds a note to an unassigned ticket\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[4][email_property_change]\" value=\"1\" ";
            // line 153
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 4, array(), "array"), "email_property_change")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me of any change to a ticket not assigned to any agent or team\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t</tr>

\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td class=\"title\" style=\"text-align: right;\">";
            // line 159
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_group_all");
            echo "</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[5][email_created]\" value=\"1\" ";
            // line 161
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 5, array(), "array"), "email_created")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when any new ticket is created\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t&nbsp;
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[5][email_user_activity]\" value=\"1\" ";
            // line 168
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 5, array(), "array"), "email_user_activity")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a user replies to any ticket\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[5][email_agent_activity]\" value=\"1\" ";
            // line 172
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 5, array(), "array"), "email_agent_activity")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when an agent replies to any ticket\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[5][email_agent_note]\" value=\"1\" ";
            // line 176
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 5, array(), "array"), "email_agent_note")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when an agent adds a note to any ticket\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[5][email_property_change]\" value=\"1\" ";
            // line 180
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 5, array(), "array"), "email_property_change")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me of any change to any ticket\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t</tbody>
\t\t\t\t\t</table>
\t\t\t\t</div>

\t\t\t\t<br />

\t\t\t\t";
            // line 190
            if (isset($context["custom_filters"])) { $_custom_filters_ = $context["custom_filters"]; } else { $_custom_filters_ = null; }
            if (twig_length_filter($this->env, $_custom_filters_)) {
                // line 191
                echo "\t\t\t\t<div class=\"check-grid\">
\t\t\t\t\t<header>";
                // line 192
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.filters");
                echo "</header>
\t\t\t\t\t<table width=\"100%\">
\t\t\t\t\t\t<thead>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<th width=\"180\">&nbsp;</th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
                // line 197
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_type_new");
                echo " <span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a new ticket is created that matches this filter\"></span></th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
                // line 198
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.filter_match");
                echo " <span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a change to a ticket causes it to match this filter\"></span></th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
                // line 199
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_type_user");
                echo " <span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a user replies to a ticket that matches this filter\"></span></th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
                // line 200
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_type_agent");
                echo " <span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when an agent replies to a ticket that matches this filter\"></span></th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
                // line 201
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_type_note");
                echo " <span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when an agent adds a note to a ticket that matches this filter\"></span></th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
                // line 202
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_type_change");
                echo " <span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when any change is made to a ticket that matches this filter\"></span></th>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t</thead>
\t\t\t\t\t\t<tbody>
\t\t\t\t\t\t\t";
                // line 206
                if (isset($context["custom_filters"])) { $_custom_filters_ = $context["custom_filters"]; } else { $_custom_filters_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($_custom_filters_);
                foreach ($context['_seq'] as $context["_key"] => $context["filter"]) {
                    // line 207
                    echo "\t\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t\t<td class=\"title\" style=\"text-align: right\">";
                    // line 208
                    if (isset($context["filter"])) { $_filter_ = $context["filter"]; } else { $_filter_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_filter_, "title"), "html", null, true);
                    echo "</td>
\t\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[";
                    // line 210
                    if (isset($context["filter"])) { $_filter_ = $context["filter"]; } else { $_filter_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_filter_, "id"), "html", null, true);
                    echo "][email_created]\" value=\"1\" ";
                    if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
                    if (isset($context["filter"])) { $_filter_ = $context["filter"]; } else { $_filter_ = null; }
                    if ($this->getAttribute($this->getAttribute($_my_subs_, $this->getAttribute($_filter_, "id"), array(), "array"), "email_created")) {
                        echo "checked=\"checked\"";
                    }
                    echo " />
\t\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[";
                    // line 213
                    if (isset($context["filter"])) { $_filter_ = $context["filter"]; } else { $_filter_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_filter_, "id"), "html", null, true);
                    echo "][email_new]\" value=\"1\" ";
                    if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
                    if (isset($context["filter"])) { $_filter_ = $context["filter"]; } else { $_filter_ = null; }
                    if ($this->getAttribute($this->getAttribute($_my_subs_, $this->getAttribute($_filter_, "id"), array(), "array"), "email_new")) {
                        echo "checked=\"checked\"";
                    }
                    echo " />
\t\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[";
                    // line 216
                    if (isset($context["filter"])) { $_filter_ = $context["filter"]; } else { $_filter_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_filter_, "id"), "html", null, true);
                    echo "][email_user_activity]\" value=\"1\" ";
                    if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
                    if (isset($context["filter"])) { $_filter_ = $context["filter"]; } else { $_filter_ = null; }
                    if ($this->getAttribute($this->getAttribute($_my_subs_, $this->getAttribute($_filter_, "id"), array(), "array"), "email_user_activity")) {
                        echo "checked=\"checked\"";
                    }
                    echo " />
\t\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[";
                    // line 219
                    if (isset($context["filter"])) { $_filter_ = $context["filter"]; } else { $_filter_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_filter_, "id"), "html", null, true);
                    echo "][email_agent_activity]\" value=\"1\" ";
                    if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
                    if (isset($context["filter"])) { $_filter_ = $context["filter"]; } else { $_filter_ = null; }
                    if ($this->getAttribute($this->getAttribute($_my_subs_, $this->getAttribute($_filter_, "id"), array(), "array"), "email_agent_activity")) {
                        echo "checked=\"checked\"";
                    }
                    echo " />
\t\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[";
                    // line 222
                    if (isset($context["filter"])) { $_filter_ = $context["filter"]; } else { $_filter_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_filter_, "id"), "html", null, true);
                    echo "][email_agent_note]\" value=\"1\" ";
                    if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
                    if (isset($context["filter"])) { $_filter_ = $context["filter"]; } else { $_filter_ = null; }
                    if ($this->getAttribute($this->getAttribute($_my_subs_, $this->getAttribute($_filter_, "id"), array(), "array"), "email_agent_note")) {
                        echo "checked=\"checked\"";
                    }
                    echo " />
\t\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[";
                    // line 225
                    if (isset($context["filter"])) { $_filter_ = $context["filter"]; } else { $_filter_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_filter_, "id"), "html", null, true);
                    echo "][email_property_change]\" value=\"1\" ";
                    if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
                    if (isset($context["filter"])) { $_filter_ = $context["filter"]; } else { $_filter_ = null; }
                    if ($this->getAttribute($this->getAttribute($_my_subs_, $this->getAttribute($_filter_, "id"), array(), "array"), "email_property_change")) {
                        echo "checked=\"checked\"";
                    }
                    echo " />
\t\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['filter'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 229
                echo "\t\t\t\t\t\t</tbody>
\t\t\t\t\t</table>
\t\t\t\t</div>
\t\t\t\t<br />
\t\t\t\t";
            }
            // line 234
            echo "
\t\t\t\t<div class=\"check-grid\">
\t\t\t\t\t<header>";
            // line 236
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_my_events");
            echo "</header>
\t\t\t\t\t<table width=\"100%\">
\t\t\t\t\t\t<thead>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<th width=\"180\">&nbsp;</th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
            // line 241
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_all_actions");
            echo "</th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
            // line 242
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_forwards");
            echo "</th>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t</thead>
\t\t\t\t\t\t<tbody>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td class=\"title\" style=\"text-align: right;\">";
            // line 247
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_my_own_actions");
            echo "</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"agent_notify_override_all_email\" value=\"1\" ";
            // line 249
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent_notify_override.all.email"), "method")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Send me notifications for all events I trigger\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"agent_notify_override_forward_email\" value=\"1\" ";
            // line 253
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent_notify_override.forward.email"), "method")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Send me notifications when I forward an email to the helpdesk\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t</tbody>
\t\t\t\t\t</table>
\t\t\t\t</div>

\t\t\t\t<br />
\t\t\t\t<div class=\"check-grid\">
\t\t\t\t\t<header>";
            // line 263
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_notes_with_mentions");
            echo "</header>
\t\t\t\t\t<table width=\"100%\">
\t\t\t\t\t\t<tbody>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td class=\"title\" style=\"text-align: left;\">
\t\t\t\t\t\t\t\t\t";
            // line 268
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_notes_with_mentions_opt");
            echo "<br/>
\t\t\t\t\t\t\t\t\t<select name=\"ticket_mention\">
\t\t\t\t\t\t\t\t\t\t<option ";
            // line 270
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if (($this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent_notif.ticket_mention", 1 => "always_send"), "method") == "always_send")) {
                echo "selected=\"selected\"";
            }
            echo "value=\"always_send\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_notes_with_mentions_always");
            echo "</option>
\t\t\t\t\t\t\t\t\t\t<option ";
            // line 271
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if (($this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent_notif.ticket_mention", 1 => "always_send"), "method") == "smart_send")) {
                echo "selected=\"selected\"";
            }
            echo "value=\"smart_send\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_notes_with_mentions_smart");
            echo "</option>
\t\t\t\t\t\t\t\t\t</select>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t</tbody>
\t\t\t\t\t</table>
\t\t\t\t</div>
\t\t\t";
        }
        // line 279
        echo "\t\t</div>

\t\t<div id=\"settingswin_ticket_notifications_alert\" style=\"display: none\">
\t\t\t";
        // line 282
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent_notif.no_allow_set_browser"), "method")) {
            // line 283
            echo "\t\t\t\t<p>
\t\t\t\t\t";
            // line 284
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_contact_admin");
            echo "
\t\t\t\t</p>
\t\t\t";
        } else {
            // line 287
            echo "\t\t\t\t<div class=\"check-grid\">
\t\t\t\t\t<header>";
            // line 288
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.inbox");
            echo "</header>
\t\t\t\t\t<table width=\"100%\">
\t\t\t\t\t\t<thead>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<th width=\"180\">&nbsp;</th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
            // line 293
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_type_new");
            echo "</th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
            // line 294
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_type_assign");
            echo "</th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
            // line 295
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_type_user");
            echo "</th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
            // line 296
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_type_agent");
            echo "</th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
            // line 297
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_type_note");
            echo "</th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
            // line 298
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_type_change");
            echo "</th>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t</thead>
\t\t\t\t\t\t<tbody>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td class=\"title\" style=\"text-align: right;\">";
            // line 303
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_group_my");
            echo "</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[1][alert_created]\" value=\"1\" ";
            // line 305
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 1, array(), "array"), "alert_created")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a new ticket is created and assigned to me\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[1][alert_new]\" value=\"1\" ";
            // line 309
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 1, array(), "array"), "alert_new")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a ticket is assigned to me\"></span>
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[1][alert_leave]\" value=\"1\" ";
            // line 311
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 1, array(), "array"), "alert_leave")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when I am unassigned from a ticket\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[1][alert_user_activity]\" value=\"1\" ";
            // line 315
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 1, array(), "array"), "alert_user_activity")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a user replies to a ticket assigned to me\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[1][alert_agent_activity]\" value=\"1\" ";
            // line 319
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 1, array(), "array"), "alert_agent_activity")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when another agent replies to a ticket assigned to me\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[1][alert_agent_note]\" value=\"1\" ";
            // line 323
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 1, array(), "array"), "alert_agent_note")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when another agent adds a note to a ticket assigned to me\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[1][alert_property_change]\" value=\"1\" ";
            // line 327
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 1, array(), "array"), "alert_property_change")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me of any change to a ticket assigned to me\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t</tr>

\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td class=\"title\" style=\"text-align: right;\">";
            // line 333
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_group_follow");
            echo "</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[3][alert_created]\" value=\"1\" ";
            // line 335
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 3, array(), "array"), "alert_created")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a new ticket is created and I am added as a follower\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[3][alert_new]\" value=\"1\" ";
            // line 339
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 3, array(), "array"), "alert_new")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when I am added as a follower to a ticket\"></span>
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[3][alert_leave]\" value=\"1\" ";
            // line 341
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 3, array(), "array"), "alert_leave")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when I am removed as a follower\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[3][alert_user_activity]\" value=\"1\" ";
            // line 345
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 3, array(), "array"), "alert_user_activity")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a user replies to a ticket I follow\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[3][alert_agent_activity]\" value=\"1\" ";
            // line 349
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 3, array(), "array"), "alert_agent_activity")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when an agent replies to a ticket I follow\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[3][alert_agent_note]\" value=\"1\" ";
            // line 353
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 3, array(), "array"), "alert_agent_note")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when an agent adds a note to a ticket I follow\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[3][alert_property_change]\" value=\"1\" ";
            // line 357
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 3, array(), "array"), "alert_property_change")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me of any change to a ticket I follow\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t</tr>

\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td class=\"title\" style=\"text-align: right;\">";
            // line 363
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_group_team");
            echo "</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[2][alert_created]\" value=\"1\" ";
            // line 365
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 2, array(), "array"), "alert_created")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a new ticket is created that is assigned to one of my teams\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[2][alert_new]\" value=\"1\" ";
            // line 369
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 2, array(), "array"), "alert_new")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a ticket is assigned to one of my teams\"></span>
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[2][alert_leave]\" value=\"1\" ";
            // line 371
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 2, array(), "array"), "alert_leave")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a ticket is unassigned from my teams\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[2][alert_user_activity]\" value=\"1\" ";
            // line 375
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 2, array(), "array"), "alert_user_activity")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a user replies to a ticket assigned to one of my teams\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[2][alert_agent_activity]\" value=\"1\" ";
            // line 379
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 2, array(), "array"), "alert_agent_activity")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when an agent replies to a ticket assigned to one of my teams\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[2][alert_agent_note]\" value=\"1\" ";
            // line 383
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 2, array(), "array"), "alert_agent_note")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when an agent adds a note to a ticket assigned to one of my teams\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[2][alert_property_change]\" value=\"1\" ";
            // line 387
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 2, array(), "array"), "alert_property_change")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me of any change to a ticket assigned to one of my teams\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t</tr>

\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td class=\"title\" style=\"text-align: right;\">";
            // line 393
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_group_unassigned");
            echo "</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[4][alert_created]\" value=\"1\" ";
            // line 395
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 4, array(), "array"), "alert_created")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a new ticket is created that is not assigned to any agent or team\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[4][alert_new]\" value=\"1\" ";
            // line 399
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 4, array(), "array"), "alert_new")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a ticket becomes unassigned\"></span>
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[4][alert_leave]\" value=\"1\" ";
            // line 401
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 4, array(), "array"), "alert_leave")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when an unassigned ticket becomes assigned\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[4][alert_user_activity]\" value=\"1\" ";
            // line 405
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 4, array(), "array"), "alert_user_activity")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a user replies to an unassigned ticket\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[4][alert_agent_activity]\" value=\"1\" ";
            // line 409
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 4, array(), "array"), "alert_agent_activity")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when an agent replies to an unassigned ticket\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[4][alert_agent_note]\" value=\"1\" ";
            // line 413
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 4, array(), "array"), "alert_agent_note")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when an agent a note to an unassigned ticket\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[4][alert_property_change]\" value=\"1\" ";
            // line 417
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 4, array(), "array"), "alert_property_change")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me of any change to a ticket not assigned to any agent or team\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t</tr>

\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td class=\"title\" style=\"text-align: right;\">";
            // line 423
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_group_all");
            echo "</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[5][alert_created]\" value=\"1\" ";
            // line 425
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 5, array(), "array"), "alert_created")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when any new ticket is created\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t&nbsp;
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[5][alert_user_activity]\" value=\"1\" ";
            // line 432
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 5, array(), "array"), "alert_user_activity")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a user replies to any ticket\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[5][alert_agent_activity]\" value=\"1\" ";
            // line 436
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 5, array(), "array"), "alert_agent_activity")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when an agent replies to any ticket\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[5][alert_agent_note]\" value=\"1\" ";
            // line 440
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 5, array(), "array"), "alert_agent_note")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when an agent adds a note to any ticket\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[5][alert_property_change]\" value=\"1\" ";
            // line 444
            if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
            if ($this->getAttribute($this->getAttribute($_my_subs_, 5, array(), "array"), "alert_property_change")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me of any change to any ticket\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t</tbody>
\t\t\t\t\t</table>
\t\t\t\t</div>

\t\t\t\t";
            // line 452
            if (isset($context["custom_filters"])) { $_custom_filters_ = $context["custom_filters"]; } else { $_custom_filters_ = null; }
            if (twig_length_filter($this->env, $_custom_filters_)) {
                // line 453
                echo "\t\t\t\t<br />
\t\t\t\t<div class=\"check-grid\">
\t\t\t\t\t<header>";
                // line 455
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.filters");
                echo "</header>
\t\t\t\t\t<table width=\"100%\">
\t\t\t\t\t\t<thead>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<th width=\"180\">&nbsp;</th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
                // line 460
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_type_new");
                echo " <span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a new ticket is created that matches this filter\"></span></th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
                // line 461
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.filter_match");
                echo " <span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a change to a ticket causes it to match this filter\"></span></th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
                // line 462
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_type_user");
                echo " <span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when a user replies to a ticket that matches this filter\"></span></th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
                // line 463
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_type_agent");
                echo " <span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when an agent replies to or adds a note to a ticket that matches this filter\"></span></th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
                // line 464
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_type_change");
                echo " <span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Notify me when any change is made to a ticket that matches this filter\"></span></th>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t</thead>
\t\t\t\t\t\t<tbody>
\t\t\t\t\t\t\t";
                // line 468
                if (isset($context["custom_filters"])) { $_custom_filters_ = $context["custom_filters"]; } else { $_custom_filters_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($_custom_filters_);
                foreach ($context['_seq'] as $context["_key"] => $context["filter"]) {
                    // line 469
                    echo "\t\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t\t<td class=\"title\" style=\"text-align: right\">";
                    // line 470
                    if (isset($context["filter"])) { $_filter_ = $context["filter"]; } else { $_filter_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_filter_, "title"), "html", null, true);
                    echo "</td>
\t\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[";
                    // line 472
                    if (isset($context["filter"])) { $_filter_ = $context["filter"]; } else { $_filter_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_filter_, "id"), "html", null, true);
                    echo "][alert_created]\" value=\"1\" ";
                    if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
                    if (isset($context["filter"])) { $_filter_ = $context["filter"]; } else { $_filter_ = null; }
                    if ($this->getAttribute($this->getAttribute($_my_subs_, $this->getAttribute($_filter_, "id"), array(), "array"), "alert_created")) {
                        echo "checked=\"checked\"";
                    }
                    echo " />
\t\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[";
                    // line 475
                    if (isset($context["filter"])) { $_filter_ = $context["filter"]; } else { $_filter_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_filter_, "id"), "html", null, true);
                    echo "][alert_new]\" value=\"1\" ";
                    if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
                    if (isset($context["filter"])) { $_filter_ = $context["filter"]; } else { $_filter_ = null; }
                    if ($this->getAttribute($this->getAttribute($_my_subs_, $this->getAttribute($_filter_, "id"), array(), "array"), "alert_new")) {
                        echo "checked=\"checked\"";
                    }
                    echo " />
\t\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[";
                    // line 478
                    if (isset($context["filter"])) { $_filter_ = $context["filter"]; } else { $_filter_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_filter_, "id"), "html", null, true);
                    echo "][alert_user_activity]\" value=\"1\" ";
                    if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
                    if (isset($context["filter"])) { $_filter_ = $context["filter"]; } else { $_filter_ = null; }
                    if ($this->getAttribute($this->getAttribute($_my_subs_, $this->getAttribute($_filter_, "id"), array(), "array"), "alert_user_activity")) {
                        echo "checked=\"checked\"";
                    }
                    echo " />
\t\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[";
                    // line 481
                    if (isset($context["filter"])) { $_filter_ = $context["filter"]; } else { $_filter_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_filter_, "id"), "html", null, true);
                    echo "][alert_agent_activity]\" value=\"1\" ";
                    if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
                    if (isset($context["filter"])) { $_filter_ = $context["filter"]; } else { $_filter_ = null; }
                    if ($this->getAttribute($this->getAttribute($_my_subs_, $this->getAttribute($_filter_, "id"), array(), "array"), "alert_agent_activity")) {
                        echo "checked=\"checked\"";
                    }
                    echo " />
\t\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"filter_sub[";
                    // line 484
                    if (isset($context["filter"])) { $_filter_ = $context["filter"]; } else { $_filter_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_filter_, "id"), "html", null, true);
                    echo "][alert_property_change]\" value=\"1\" ";
                    if (isset($context["my_subs"])) { $_my_subs_ = $context["my_subs"]; } else { $_my_subs_ = null; }
                    if (isset($context["filter"])) { $_filter_ = $context["filter"]; } else { $_filter_ = null; }
                    if ($this->getAttribute($this->getAttribute($_my_subs_, $this->getAttribute($_filter_, "id"), array(), "array"), "alert_property_change")) {
                        echo "checked=\"checked\"";
                    }
                    echo " />
\t\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['filter'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 488
                echo "\t\t\t\t\t\t</tbody>
\t\t\t\t\t</table>
\t\t\t\t</div>
\t\t\t\t";
            }
            // line 492
            echo "
\t\t\t\t<br />
\t\t\t\t<div class=\"check-grid\">
\t\t\t\t\t<header>";
            // line 495
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_my_events");
            echo "</header>
\t\t\t\t\t<table width=\"100%\">
\t\t\t\t\t\t<thead>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<th width=\"180\">&nbsp;</th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
            // line 500
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_all_actions");
            echo "</th>
\t\t\t\t\t\t\t\t<th nowrap=\"nowrap\">";
            // line 501
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_forwards");
            echo "</th>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t</thead>
\t\t\t\t\t\t<tbody>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td class=\"title\" style=\"text-align: right;\">";
            // line 506
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.settings.notif_my_own_actions");
            echo "</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"agent_notify_override_all_alert\" value=\"1\" ";
            // line 508
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent_notify_override.all.alert"), "method")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Send me notifications for all events I trigger\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"agent_notify_override_forward_alert\" value=\"1\" ";
            // line 512
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($this->getAttribute($_app_, "user"), "getPref", array(0 => "agent_notify_override.forward.alert"), "method")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t\t\t<span class=\"small-light-icon tipped\" data-tipped-options=\"maxWidth: 250\" title=\"Send me notifications when I forward an email to the helpdesk\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t</tbody>
\t\t\t\t\t</table>
\t\t\t\t</div>
\t\t\t";
        }
        // line 520
        echo "\t\t</div>

\t\t";
        // line 522
        if (isset($context["admin_triggers"])) { $_admin_triggers_ = $context["admin_triggers"]; } else { $_admin_triggers_ = null; }
        if ($_admin_triggers_) {
            // line 523
            echo "\t\t\t<br />
\t\t\t<br />
\t\t\t<div class=\"check-grid\">
\t\t\t\t<header>";
            // line 526
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.tickets.admin_trigger_notifications_explain");
            echo "</header>
\t\t\t\t<table width=\"100%\">
\t\t\t\t\t<tbody>
\t\t\t\t\t";
            // line 529
            $context["types"] = array("new_ticket" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.new_ticket"), "new_ticket.web_person" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.new_ticket_web"), "new_ticket.gateway_person" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.new_ticket_gateway"), "new_ticket.widget" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.new_ticket_widget"), "new_ticket.agent" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.new_ticket_agent"), "new_reply" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.new_reply"), "new_reply.web_person" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.new_reply_web"), "new_reply.gateway_person" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.new_reply_gateway"), "new_reply.agent" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.new_reply_agent"), "property_change" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.property_change"), "property_change.user" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.property_change_user"), "property_change.agent" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.property_change_agent"), "time_open" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.time_open"), "time_user_waiting" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.time_user_waiting"), "time_total_user_waiting" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.time_total_user_waiting"), "time_agent_waiting" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.time_agent_waiting"), "time_resolved" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.tickets.time_resolved"), "other" => $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.other"));
            // line 549
            echo "\t\t\t\t\t\t";
            if (isset($context["admin_triggers"])) { $_admin_triggers_ = $context["admin_triggers"]; } else { $_admin_triggers_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_admin_triggers_);
            foreach ($context['_seq'] as $context["_key"] => $context["trigger"]) {
                // line 550
                echo "\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td class=\"title\" style=\"text-align: right;\">
\t\t\t\t\t\t\t\t\t";
                // line 552
                if (isset($context["types"])) { $_types_ = $context["types"]; } else { $_types_ = null; }
                if (isset($context["trigger"])) { $_trigger_ = $context["trigger"]; } else { $_trigger_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_types_, $this->getAttribute($_trigger_, "event_trigger"), array(), "array"), "html", null, true);
                echo ":
\t\t\t\t\t\t\t\t\t";
                // line 553
                if (isset($context["trigger"])) { $_trigger_ = $context["trigger"]; } else { $_trigger_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($_trigger_, "getAllTermDescriptions", array(), "method"));
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
                foreach ($context['_seq'] as $context["_key"] => $context["desc"]) {
                    // line 554
                    echo "\t\t\t\t\t\t\t\t\t\t";
                    if (isset($context["desc"])) { $_desc_ = $context["desc"]; } else { $_desc_ = null; }
                    echo twig_escape_filter($this->env, $_desc_, "html", null, true);
                    echo "
\t\t\t\t\t\t\t\t\t\t";
                    // line 555
                    if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
                    if ((!$this->getAttribute($_loop_, "last"))) {
                        echo "<br /><em class=\"op\">";
                        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.and");
                        echo "</em>";
                    }
                    // line 556
                    echo "\t\t\t\t\t\t\t\t\t";
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
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['desc'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 557
                echo "\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['trigger'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 560
            echo "\t\t\t\t\t</tbody>
\t\t\t\t</table>
\t\t\t</div>
\t\t";
        }
        // line 564
        echo "\t</div>


\t<div class=\"button-container center\">
\t\t<button type=\"submit\" class=\"clean-white\">";
        // line 568
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.save");
        echo "</button>
\t</div>
</form>
";
    }

    public function getTemplateName()
    {
        return "AgentBundle:Settings:ticket-notifications.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1399 => 568,  1387 => 560,  1358 => 555,  1334 => 553,  1316 => 529,  1298 => 520,  1195 => 478,  1148 => 464,  1120 => 453,  1117 => 452,  1093 => 440,  788 => 315,  612 => 203,  1394 => 446,  1385 => 442,  1382 => 441,  1369 => 433,  1363 => 432,  1360 => 431,  1350 => 429,  1305 => 523,  1300 => 409,  1281 => 404,  1277 => 403,  1265 => 398,  1255 => 396,  1247 => 393,  1234 => 387,  1202 => 375,  1199 => 374,  1187 => 372,  1162 => 365,  1136 => 461,  1128 => 352,  1122 => 350,  1069 => 332,  968 => 387,  846 => 250,  1183 => 449,  1132 => 460,  1097 => 341,  957 => 394,  907 => 277,  875 => 263,  653 => 236,  1329 => 405,  1309 => 401,  1303 => 399,  1297 => 398,  1292 => 397,  1280 => 394,  1249 => 495,  1237 => 390,  1205 => 409,  1200 => 408,  1194 => 407,  1178 => 380,  1175 => 447,  1170 => 375,  1135 => 362,  1105 => 428,  1078 => 335,  1068 => 325,  1048 => 417,  961 => 294,  922 => 280,  750 => 245,  842 => 263,  1038 => 319,  904 => 322,  882 => 301,  831 => 267,  860 => 314,  790 => 284,  733 => 296,  707 => 283,  744 => 220,  873 => 349,  824 => 266,  762 => 230,  713 => 235,  578 => 158,  2739 => 1153,  2735 => 1152,  2732 => 1151,  2725 => 1146,  2712 => 1144,  2707 => 1143,  2692 => 1131,  2688 => 1130,  2683 => 1127,  2680 => 1126,  2677 => 1125,  2671 => 1121,  2658 => 1119,  2653 => 1118,  2645 => 1113,  2641 => 1112,  2636 => 1109,  2632 => 1108,  2629 => 1107,  2626 => 1106,  2606 => 1089,  2602 => 1088,  2598 => 1087,  2594 => 1086,  2590 => 1085,  2586 => 1084,  2572 => 1073,  2568 => 1072,  2564 => 1071,  2560 => 1070,  2556 => 1069,  2552 => 1068,  2537 => 1056,  2533 => 1055,  2529 => 1054,  2525 => 1053,  2521 => 1052,  2503 => 1040,  2499 => 1039,  2495 => 1038,  2491 => 1037,  2487 => 1036,  2483 => 1035,  2469 => 1024,  2465 => 1023,  2461 => 1022,  2457 => 1021,  2453 => 1020,  2449 => 1019,  2426 => 999,  2422 => 998,  2418 => 997,  2414 => 996,  2410 => 995,  2406 => 994,  2392 => 983,  2388 => 982,  2384 => 981,  2380 => 980,  2376 => 979,  2372 => 978,  2358 => 967,  2354 => 966,  2350 => 965,  2346 => 964,  2342 => 963,  2338 => 962,  2324 => 951,  2320 => 950,  2316 => 949,  2312 => 948,  2308 => 947,  2304 => 946,  2295 => 939,  2282 => 937,  2277 => 936,  2269 => 931,  2265 => 930,  2260 => 927,  2256 => 926,  2253 => 925,  2230 => 901,  2220 => 899,  2216 => 898,  2211 => 896,  2208 => 895,  2198 => 893,  2194 => 892,  2189 => 890,  2184 => 889,  2177 => 885,  2173 => 884,  2164 => 877,  2151 => 875,  2146 => 874,  2143 => 873,  2141 => 864,  2134 => 860,  2130 => 859,  2122 => 853,  2120 => 852,  2113 => 848,  2105 => 846,  2096 => 839,  2083 => 837,  2078 => 836,  2073 => 834,  2067 => 830,  2062 => 828,  2058 => 827,  2054 => 826,  2050 => 825,  2045 => 824,  2038 => 822,  2034 => 821,  2022 => 814,  2009 => 812,  1999 => 809,  1993 => 805,  1988 => 803,  1984 => 802,  1980 => 801,  1976 => 800,  1971 => 799,  1968 => 798,  1964 => 797,  1960 => 796,  1954 => 793,  1946 => 788,  1942 => 787,  1938 => 786,  1932 => 782,  1927 => 780,  1919 => 778,  1915 => 777,  1911 => 776,  1906 => 775,  1903 => 774,  1899 => 773,  1895 => 772,  1886 => 768,  1883 => 764,  1871 => 759,  1866 => 756,  1863 => 755,  1842 => 752,  1825 => 749,  1818 => 748,  1815 => 747,  1800 => 743,  1793 => 741,  1772 => 729,  1761 => 721,  1757 => 720,  1751 => 717,  1740 => 709,  1719 => 697,  1715 => 696,  1686 => 673,  1676 => 669,  1665 => 661,  1661 => 660,  1645 => 651,  1638 => 649,  1630 => 645,  1625 => 642,  1622 => 641,  1598 => 637,  1577 => 634,  1558 => 629,  1549 => 627,  1546 => 626,  1535 => 618,  1531 => 617,  1514 => 606,  1510 => 605,  1504 => 602,  1493 => 594,  1483 => 590,  1464 => 580,  1458 => 577,  1443 => 568,  1437 => 565,  1426 => 557,  1405 => 545,  1401 => 544,  1395 => 541,  1390 => 538,  1381 => 533,  1373 => 531,  1348 => 521,  1343 => 520,  1335 => 515,  1321 => 510,  1318 => 549,  1299 => 503,  1294 => 407,  1282 => 496,  1269 => 506,  1260 => 397,  1240 => 478,  1221 => 484,  1216 => 378,  1210 => 410,  1206 => 473,  1193 => 467,  1189 => 466,  1155 => 468,  1150 => 447,  1022 => 312,  1006 => 404,  988 => 398,  969 => 392,  965 => 391,  921 => 370,  878 => 275,  866 => 349,  854 => 254,  819 => 279,  796 => 286,  1713 => 683,  1700 => 681,  1685 => 674,  1662 => 670,  1643 => 669,  1640 => 668,  1631 => 663,  1618 => 661,  1613 => 660,  1608 => 639,  1605 => 656,  1602 => 655,  1589 => 645,  1586 => 644,  1583 => 643,  1554 => 613,  1547 => 608,  1521 => 602,  1508 => 600,  1499 => 598,  1491 => 593,  1482 => 587,  1472 => 582,  1468 => 581,  1465 => 579,  1457 => 573,  1445 => 566,  1422 => 556,  1416 => 553,  1410 => 548,  1397 => 546,  1392 => 545,  1386 => 541,  1377 => 532,  1370 => 530,  1357 => 528,  1352 => 554,  1341 => 428,  1314 => 510,  1311 => 509,  1275 => 493,  1248 => 484,  1238 => 488,  1225 => 476,  1220 => 412,  1209 => 466,  1185 => 385,  1182 => 475,  1159 => 367,  1154 => 449,  1130 => 438,  1125 => 437,  1101 => 426,  1074 => 432,  1056 => 326,  1046 => 323,  1043 => 417,  1030 => 397,  1027 => 412,  947 => 361,  925 => 352,  913 => 324,  893 => 357,  881 => 267,  847 => 343,  829 => 336,  825 => 259,  1083 => 436,  995 => 399,  984 => 350,  963 => 292,  941 => 324,  851 => 367,  682 => 270,  1365 => 556,  1359 => 545,  1349 => 539,  1336 => 427,  1331 => 424,  1323 => 420,  1319 => 530,  1312 => 402,  1284 => 512,  1272 => 401,  1268 => 509,  1261 => 501,  1251 => 485,  1245 => 483,  1231 => 496,  1207 => 376,  1197 => 491,  1180 => 484,  1173 => 457,  1169 => 472,  1157 => 363,  1147 => 438,  1109 => 440,  1065 => 440,  1059 => 423,  1047 => 428,  1044 => 424,  1033 => 413,  1009 => 357,  991 => 351,  987 => 404,  973 => 294,  931 => 355,  924 => 282,  911 => 298,  906 => 81,  885 => 276,  872 => 317,  855 => 72,  749 => 264,  701 => 239,  594 => 180,  1163 => 470,  1143 => 440,  1087 => 420,  1077 => 433,  1051 => 325,  1037 => 480,  1010 => 405,  999 => 407,  932 => 326,  899 => 306,  895 => 404,  933 => 387,  914 => 133,  909 => 323,  833 => 284,  783 => 306,  755 => 303,  666 => 263,  453 => 168,  639 => 209,  568 => 176,  520 => 110,  657 => 216,  572 => 201,  609 => 202,  20 => 1,  659 => 217,  562 => 164,  548 => 185,  558 => 197,  479 => 145,  589 => 223,  457 => 169,  413 => 149,  953 => 290,  948 => 379,  935 => 394,  929 => 283,  916 => 382,  864 => 365,  844 => 338,  816 => 342,  807 => 259,  801 => 268,  774 => 257,  766 => 229,  737 => 297,  685 => 225,  664 => 225,  635 => 249,  593 => 209,  546 => 201,  532 => 223,  865 => 296,  852 => 241,  838 => 285,  820 => 201,  781 => 327,  764 => 252,  725 => 250,  632 => 268,  602 => 192,  565 => 165,  529 => 181,  505 => 156,  487 => 101,  473 => 87,  1853 => 842,  1849 => 753,  1839 => 751,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 733,  1778 => 732,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 693,  1705 => 781,  1688 => 675,  1681 => 769,  1677 => 768,  1667 => 671,  1656 => 762,  1648 => 655,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 606,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 593,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 540,  1379 => 557,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 522,  1289 => 406,  1276 => 604,  1263 => 601,  1244 => 492,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 373,  1181 => 561,  1171 => 446,  1158 => 550,  1114 => 428,  1104 => 465,  1072 => 333,  1062 => 7,  1024 => 395,  1014 => 406,  1000 => 328,  990 => 303,  980 => 393,  960 => 466,  918 => 280,  888 => 80,  834 => 268,  673 => 64,  636 => 198,  462 => 190,  454 => 138,  1144 => 463,  1139 => 356,  1131 => 399,  1127 => 434,  1110 => 347,  1092 => 459,  1089 => 426,  1086 => 520,  1084 => 337,  1063 => 387,  1060 => 425,  1055 => 423,  1050 => 373,  1035 => 372,  1019 => 330,  1003 => 401,  959 => 387,  900 => 366,  880 => 276,  870 => 277,  867 => 258,  859 => 294,  848 => 215,  839 => 376,  828 => 302,  823 => 374,  809 => 316,  800 => 241,  797 => 267,  794 => 257,  786 => 283,  740 => 78,  734 => 261,  703 => 228,  693 => 236,  630 => 247,  626 => 19,  614 => 275,  610 => 236,  581 => 206,  564 => 268,  525 => 195,  722 => 226,  697 => 282,  674 => 270,  671 => 221,  577 => 180,  569 => 222,  557 => 229,  502 => 187,  497 => 152,  445 => 163,  729 => 209,  684 => 237,  676 => 65,  669 => 268,  660 => 203,  647 => 212,  643 => 229,  601 => 306,  570 => 169,  522 => 156,  501 => 210,  296 => 108,  374 => 115,  631 => 207,  616 => 283,  608 => 194,  605 => 193,  596 => 188,  574 => 180,  561 => 231,  527 => 165,  433 => 158,  388 => 141,  426 => 172,  383 => 135,  461 => 184,  370 => 155,  395 => 166,  294 => 87,  223 => 87,  220 => 84,  492 => 201,  468 => 192,  444 => 153,  410 => 150,  397 => 134,  377 => 161,  262 => 91,  250 => 90,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 1051,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 680,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 638,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 572,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 526,  1301 => 1037,  1252 => 392,  1243 => 994,  1223 => 978,  1212 => 494,  1167 => 366,  1161 => 933,  1145 => 923,  1140 => 462,  1134 => 538,  1126 => 443,  1106 => 528,  1100 => 897,  1026 => 418,  997 => 822,  983 => 298,  975 => 296,  956 => 318,  939 => 285,  902 => 274,  894 => 364,  879 => 76,  757 => 269,  727 => 293,  716 => 287,  670 => 204,  528 => 187,  476 => 197,  435 => 177,  354 => 127,  341 => 54,  192 => 78,  321 => 114,  243 => 85,  793 => 266,  780 => 256,  758 => 229,  700 => 193,  686 => 238,  652 => 185,  638 => 226,  620 => 216,  545 => 162,  523 => 158,  494 => 151,  459 => 156,  438 => 146,  351 => 99,  347 => 127,  402 => 150,  268 => 98,  430 => 188,  411 => 117,  379 => 138,  322 => 118,  315 => 170,  289 => 130,  284 => 101,  255 => 92,  234 => 85,  1133 => 444,  1124 => 455,  1121 => 430,  1116 => 348,  1113 => 429,  1108 => 376,  1103 => 444,  1098 => 26,  1081 => 425,  1073 => 432,  1067 => 356,  1064 => 330,  1061 => 421,  1053 => 318,  1049 => 419,  1042 => 313,  1039 => 345,  1025 => 314,  1021 => 310,  1015 => 308,  1008 => 461,  996 => 406,  989 => 454,  985 => 395,  981 => 257,  977 => 321,  970 => 320,  966 => 375,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 148,  919 => 314,  917 => 279,  908 => 411,  905 => 363,  896 => 358,  891 => 378,  877 => 334,  862 => 274,  857 => 271,  837 => 261,  832 => 260,  827 => 349,  821 => 66,  803 => 179,  778 => 311,  769 => 253,  765 => 297,  753 => 54,  746 => 244,  743 => 297,  735 => 295,  730 => 251,  720 => 237,  717 => 362,  712 => 251,  691 => 233,  678 => 275,  654 => 199,  587 => 210,  576 => 171,  539 => 200,  517 => 210,  471 => 160,  441 => 162,  437 => 149,  418 => 120,  386 => 153,  373 => 109,  304 => 120,  270 => 106,  265 => 96,  229 => 44,  477 => 167,  455 => 125,  448 => 143,  429 => 141,  407 => 120,  399 => 145,  389 => 145,  375 => 141,  358 => 146,  349 => 123,  335 => 120,  327 => 98,  298 => 91,  280 => 100,  249 => 88,  194 => 82,  142 => 60,  344 => 94,  318 => 113,  306 => 112,  295 => 80,  357 => 136,  300 => 118,  286 => 105,  276 => 101,  269 => 97,  254 => 100,  128 => 41,  237 => 72,  165 => 26,  122 => 39,  798 => 319,  770 => 309,  759 => 278,  748 => 298,  731 => 294,  721 => 293,  718 => 301,  708 => 218,  696 => 236,  617 => 204,  590 => 207,  553 => 163,  550 => 157,  540 => 161,  533 => 182,  500 => 171,  493 => 178,  489 => 202,  482 => 198,  467 => 158,  464 => 170,  458 => 139,  452 => 145,  449 => 134,  415 => 152,  382 => 107,  372 => 137,  361 => 110,  356 => 141,  339 => 119,  302 => 104,  285 => 97,  258 => 76,  123 => 39,  108 => 16,  424 => 151,  394 => 109,  380 => 137,  338 => 135,  319 => 216,  316 => 124,  312 => 116,  290 => 106,  267 => 141,  206 => 81,  110 => 40,  240 => 86,  224 => 58,  219 => 73,  217 => 80,  202 => 84,  186 => 62,  170 => 28,  100 => 30,  67 => 25,  14 => 1,  1096 => 345,  1090 => 339,  1088 => 435,  1085 => 456,  1066 => 423,  1034 => 282,  1031 => 281,  1018 => 416,  1013 => 405,  1007 => 408,  1002 => 403,  993 => 305,  986 => 264,  982 => 401,  976 => 399,  971 => 295,  964 => 255,  949 => 289,  946 => 288,  940 => 388,  937 => 374,  928 => 371,  926 => 318,  915 => 279,  912 => 82,  903 => 231,  898 => 440,  892 => 303,  889 => 277,  887 => 302,  884 => 79,  876 => 75,  874 => 215,  871 => 331,  863 => 345,  861 => 270,  858 => 255,  850 => 291,  843 => 206,  840 => 406,  815 => 251,  812 => 343,  808 => 323,  804 => 258,  799 => 312,  791 => 310,  785 => 262,  775 => 255,  771 => 253,  754 => 267,  728 => 317,  726 => 72,  723 => 238,  715 => 105,  711 => 220,  709 => 222,  706 => 232,  698 => 229,  694 => 199,  692 => 189,  689 => 234,  681 => 224,  677 => 229,  675 => 234,  663 => 218,  661 => 263,  650 => 213,  646 => 231,  629 => 267,  627 => 218,  625 => 266,  622 => 242,  598 => 199,  592 => 212,  586 => 175,  575 => 218,  566 => 242,  556 => 219,  554 => 188,  541 => 176,  536 => 224,  515 => 176,  511 => 208,  509 => 179,  488 => 200,  486 => 174,  483 => 149,  465 => 191,  463 => 142,  450 => 182,  432 => 146,  419 => 143,  371 => 154,  362 => 129,  353 => 124,  337 => 124,  333 => 91,  309 => 44,  303 => 106,  299 => 88,  291 => 115,  272 => 100,  261 => 38,  253 => 101,  239 => 36,  235 => 94,  213 => 74,  200 => 68,  198 => 74,  159 => 62,  149 => 51,  146 => 51,  131 => 51,  116 => 38,  79 => 22,  74 => 30,  71 => 24,  836 => 262,  817 => 243,  814 => 319,  811 => 261,  805 => 244,  787 => 257,  779 => 169,  776 => 232,  773 => 322,  761 => 296,  751 => 302,  747 => 298,  742 => 243,  739 => 296,  736 => 215,  724 => 259,  705 => 69,  702 => 216,  688 => 226,  680 => 205,  667 => 273,  662 => 218,  656 => 215,  649 => 285,  644 => 220,  641 => 220,  624 => 206,  613 => 166,  607 => 200,  597 => 275,  591 => 49,  584 => 236,  579 => 181,  563 => 212,  559 => 68,  551 => 190,  547 => 188,  537 => 160,  524 => 112,  512 => 174,  507 => 237,  504 => 159,  498 => 181,  485 => 176,  480 => 198,  472 => 169,  466 => 165,  460 => 142,  447 => 163,  442 => 162,  434 => 133,  428 => 157,  422 => 145,  404 => 113,  368 => 136,  364 => 111,  340 => 100,  334 => 123,  330 => 48,  325 => 98,  292 => 106,  287 => 42,  282 => 41,  279 => 106,  273 => 98,  266 => 92,  256 => 101,  252 => 87,  228 => 90,  218 => 75,  201 => 75,  64 => 15,  51 => 13,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 847,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 823,  2037 => 789,  2031 => 788,  2028 => 818,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 811,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 779,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 769,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 744,  1801 => 695,  1798 => 694,  1795 => 742,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 708,  1730 => 705,  1720 => 688,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 672,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 659,  1651 => 656,  1641 => 631,  1637 => 667,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 651,  1592 => 614,  1587 => 613,  1584 => 635,  1581 => 731,  1576 => 608,  1574 => 633,  1567 => 603,  1563 => 630,  1559 => 601,  1552 => 628,  1548 => 597,  1542 => 593,  1529 => 605,  1525 => 614,  1516 => 708,  1512 => 583,  1503 => 599,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 569,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 564,  1388 => 537,  1380 => 539,  1376 => 436,  1371 => 645,  1367 => 527,  1364 => 518,  1361 => 523,  1355 => 543,  1342 => 624,  1337 => 510,  1328 => 552,  1324 => 550,  1317 => 511,  1313 => 499,  1310 => 526,  1304 => 504,  1291 => 502,  1286 => 405,  1279 => 486,  1274 => 508,  1270 => 483,  1266 => 487,  1262 => 490,  1257 => 500,  1254 => 482,  1250 => 394,  1246 => 477,  1239 => 389,  1235 => 498,  1232 => 416,  1226 => 383,  1213 => 466,  1208 => 481,  1201 => 472,  1196 => 464,  1192 => 490,  1188 => 456,  1184 => 485,  1179 => 448,  1176 => 461,  1172 => 367,  1168 => 451,  1164 => 450,  1160 => 469,  1153 => 561,  1149 => 359,  1146 => 444,  1137 => 249,  1123 => 442,  1119 => 354,  1115 => 424,  1111 => 377,  1107 => 346,  1102 => 344,  1099 => 347,  1095 => 419,  1091 => 418,  1082 => 455,  1079 => 524,  1076 => 359,  1070 => 431,  1057 => 491,  1052 => 406,  1045 => 484,  1040 => 416,  1036 => 283,  1032 => 415,  1028 => 312,  1023 => 409,  1020 => 311,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 304,  998 => 308,  992 => 406,  979 => 297,  974 => 400,  967 => 399,  962 => 397,  958 => 383,  954 => 293,  950 => 393,  945 => 391,  942 => 288,  938 => 375,  934 => 284,  927 => 282,  923 => 382,  920 => 369,  910 => 365,  901 => 340,  897 => 273,  890 => 271,  886 => 270,  883 => 353,  868 => 375,  856 => 293,  853 => 341,  849 => 264,  845 => 339,  841 => 249,  835 => 335,  830 => 333,  826 => 282,  822 => 281,  818 => 327,  813 => 242,  810 => 290,  806 => 270,  802 => 339,  795 => 241,  792 => 335,  789 => 233,  784 => 286,  782 => 237,  777 => 255,  772 => 289,  768 => 81,  763 => 327,  760 => 305,  756 => 248,  752 => 247,  745 => 245,  741 => 218,  738 => 241,  732 => 171,  719 => 288,  714 => 251,  710 => 284,  704 => 282,  699 => 279,  695 => 66,  690 => 226,  687 => 210,  683 => 271,  679 => 223,  672 => 179,  668 => 264,  665 => 229,  658 => 178,  645 => 253,  640 => 227,  634 => 206,  628 => 174,  623 => 217,  619 => 78,  611 => 282,  606 => 234,  603 => 231,  599 => 229,  595 => 213,  583 => 159,  580 => 173,  573 => 170,  560 => 267,  543 => 216,  538 => 164,  534 => 189,  530 => 213,  526 => 158,  521 => 287,  518 => 194,  514 => 193,  510 => 154,  503 => 206,  496 => 202,  490 => 149,  484 => 199,  474 => 174,  470 => 144,  446 => 180,  440 => 130,  436 => 176,  431 => 145,  425 => 156,  416 => 168,  412 => 76,  408 => 165,  403 => 161,  400 => 119,  396 => 144,  392 => 143,  385 => 146,  381 => 158,  367 => 112,  363 => 134,  359 => 132,  355 => 108,  350 => 143,  346 => 137,  343 => 140,  328 => 131,  324 => 89,  313 => 96,  307 => 111,  301 => 119,  288 => 152,  283 => 88,  271 => 107,  257 => 37,  251 => 100,  238 => 84,  233 => 82,  195 => 78,  191 => 30,  187 => 81,  183 => 51,  130 => 48,  88 => 27,  76 => 10,  115 => 34,  95 => 33,  655 => 202,  651 => 176,  648 => 215,  637 => 218,  633 => 197,  621 => 462,  618 => 241,  615 => 196,  604 => 201,  600 => 233,  588 => 206,  585 => 222,  582 => 225,  571 => 217,  567 => 194,  555 => 172,  552 => 171,  549 => 170,  544 => 230,  542 => 166,  535 => 199,  531 => 159,  519 => 155,  516 => 218,  513 => 154,  508 => 207,  506 => 188,  499 => 209,  495 => 150,  491 => 203,  481 => 172,  478 => 171,  475 => 170,  469 => 182,  456 => 138,  451 => 135,  443 => 179,  439 => 178,  427 => 155,  423 => 142,  420 => 141,  409 => 160,  405 => 218,  401 => 176,  391 => 138,  387 => 334,  384 => 138,  378 => 205,  365 => 153,  360 => 125,  348 => 116,  336 => 130,  332 => 118,  329 => 132,  323 => 129,  310 => 113,  305 => 43,  277 => 79,  274 => 94,  263 => 105,  259 => 65,  247 => 95,  244 => 93,  241 => 129,  222 => 69,  210 => 80,  207 => 83,  204 => 76,  184 => 76,  181 => 52,  167 => 67,  157 => 61,  96 => 32,  421 => 173,  417 => 150,  414 => 145,  406 => 170,  398 => 159,  393 => 142,  390 => 164,  376 => 149,  369 => 124,  366 => 145,  352 => 128,  345 => 106,  342 => 126,  331 => 122,  326 => 46,  320 => 88,  317 => 114,  314 => 86,  311 => 123,  308 => 121,  297 => 107,  293 => 90,  281 => 111,  278 => 40,  275 => 39,  264 => 92,  260 => 94,  248 => 99,  245 => 61,  242 => 84,  231 => 85,  227 => 88,  215 => 82,  212 => 86,  209 => 31,  197 => 67,  177 => 50,  171 => 82,  161 => 63,  132 => 36,  121 => 47,  105 => 43,  99 => 14,  81 => 26,  77 => 19,  180 => 58,  176 => 60,  156 => 52,  143 => 24,  139 => 38,  118 => 38,  189 => 63,  185 => 75,  173 => 69,  166 => 55,  152 => 52,  174 => 59,  164 => 80,  154 => 53,  150 => 41,  137 => 44,  133 => 43,  127 => 44,  107 => 32,  102 => 30,  83 => 28,  78 => 31,  53 => 20,  23 => 4,  42 => 11,  138 => 23,  134 => 22,  109 => 34,  103 => 41,  97 => 36,  94 => 30,  84 => 29,  75 => 26,  69 => 14,  66 => 23,  54 => 21,  44 => 16,  230 => 84,  226 => 89,  203 => 69,  193 => 72,  188 => 75,  182 => 68,  178 => 71,  168 => 63,  163 => 56,  160 => 54,  155 => 50,  148 => 48,  145 => 49,  140 => 43,  136 => 42,  125 => 35,  120 => 38,  113 => 45,  101 => 37,  92 => 30,  89 => 32,  85 => 22,  73 => 27,  62 => 24,  59 => 23,  56 => 14,  41 => 9,  126 => 51,  119 => 50,  111 => 39,  106 => 33,  98 => 39,  93 => 25,  86 => 33,  70 => 29,  60 => 14,  28 => 5,  36 => 10,  114 => 30,  104 => 36,  91 => 34,  80 => 21,  63 => 16,  58 => 18,  40 => 15,  34 => 7,  45 => 10,  61 => 14,  55 => 13,  48 => 20,  39 => 9,  35 => 7,  31 => 2,  26 => 6,  21 => 2,  46 => 13,  29 => 6,  57 => 22,  50 => 19,  47 => 18,  38 => 8,  33 => 8,  49 => 13,  32 => 9,  246 => 89,  236 => 93,  232 => 91,  225 => 78,  221 => 87,  216 => 85,  214 => 82,  211 => 81,  208 => 40,  205 => 83,  199 => 58,  196 => 77,  190 => 71,  179 => 27,  175 => 55,  172 => 61,  169 => 60,  162 => 53,  158 => 54,  153 => 64,  151 => 59,  147 => 57,  144 => 50,  141 => 55,  135 => 46,  129 => 45,  124 => 48,  117 => 37,  112 => 34,  90 => 34,  87 => 36,  82 => 32,  72 => 25,  68 => 21,  65 => 21,  52 => 13,  43 => 11,  37 => 8,  30 => 7,  27 => 5,  25 => 4,  24 => 4,  22 => 2,  19 => 1,);
    }
}
