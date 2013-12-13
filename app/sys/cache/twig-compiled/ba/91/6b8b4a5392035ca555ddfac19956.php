<?php

/* BillingBundle:Login:index.html.twig */
class __TwigTemplate_ba916b8b4a5392035ca555ddfac19956 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("AgentBundle:Login:layout.html.twig");

        $this->blocks = array(
            'head' => array($this, 'block_head'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "AgentBundle:Login:layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_head($context, array $blocks = array())
    {
        // line 3
        echo "\t<script type=\"text/javascript\">
\t\t\$(document).ready(function() {
\t\t\t//\$('<iframe id=\"preload_iframe\" src=\"";
        // line 5
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("agent_login_preload_sources"), "html", null, true);
        echo "\" frameborder=\"0\"></iframe>').appendTo('body');

\t\t\t\$('#lost_link').on('click', function(ev) {
\t\t\t\tev.preventDefault();

\t\t\t\t\$('#normal_view').show();
\t\t\t\t\$('#lost_message_view').hide();
\t\t\t\t\$('#loading_view').hide();

\t\t\t\t\$('#login_title').hide();
\t\t\t\t\$('#login_fields').hide();
\t\t\t\t\$('#lost_password_title').show();
\t\t\t\t\$('#lost_fields').show();
\t\t\t});

\t\t\t\$('#login_link').on('click', function(ev) {
\t\t\t\tev.preventDefault();

\t\t\t\t\$('#normal_view').show();
\t\t\t\t\$('#lost_message_view').hide();
\t\t\t\t\$('#loading_view').hide();

\t\t\t\t\$('#lost_password_title').hide();
\t\t\t\t\$('#lost_fields').hide();
\t\t\t\t\$('#login_title').show();
\t\t\t\t\$('#login_fields').show();
\t\t\t});

\t\t\t\$('#do_send_lost').on('click', function(ev) {
\t\t\t\tev.preventDefault();
\t\t\t\t\$('#normal_view').hide();
\t\t\t\t\$('#lost_message_view').hide();
\t\t\t\t\$('#loading_view').show();

\t\t\t\tvar postData = {
\t\t\t\t\temail: \$('#email').val().trim()
\t\t\t\t};

\t\t\t\tif (!postData.email.length || postData.email.indexOf('@') === -1) {
\t\t\t\t\t\$('#normal_view').show();
\t\t\t\t\t\$('#lost_message_view').hide();
\t\t\t\t\t\$('#loading_view').hide();
\t\t\t\t\t\$('#lost_password_title').hide();
\t\t\t\t\t\$('#lost_fields').hide();
\t\t\t\t\t\$('#login_title').show();
\t\t\t\t\t\$('#login_fields').show();
\t\t\t\t\treturn;
\t\t\t\t}

\t\t\t\t\$.ajax({
\t\t\t\t\turl: '";
        // line 55
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("agent_send_lost"), "html", null, true);
        echo "',
\t\t\t\t\tdata: postData,
\t\t\t\t\tcomplete: function() {
\t\t\t\t\t\t\$('#loading_view').hide();
\t\t\t\t\t\t\$('#lost_message_view').show();
\t\t\t\t\t}
\t\t\t\t});
\t\t\t});

\t\t\t";
        // line 64
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($this->getAttribute($_app_, "getRequest", array(), "method"), "query"), "has", array(0 => "lost"), "method")) {
            // line 65
            echo "\t\t\t\t\$('#lost_link').trigger('click');
\t\t\t";
        }
        // line 67
        echo "\t\t});
\t</script>
";
    }

    // line 70
    public function block_content($context, array $blocks = array())
    {
        // line 71
        echo "<form action=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("billing_login_authenticate_local"), "html", null, true);
        echo "\" method=\"post\">
\t<input type=\"hidden\" name=\"agent_login\" value=\"1\" />
\t";
        // line 73
        if (isset($context["return"])) { $_return_ = $context["return"]; } else { $_return_ = null; }
        if ($_return_) {
            echo "<input type=\"hidden\" name=\"return\" value=\"";
            if (isset($context["return"])) { $_return_ = $context["return"]; } else { $_return_ = null; }
            echo twig_escape_filter($this->env, $_return_, "html", null, true);
            echo "\" />";
        }
        // line 74
        echo "
\t<h1 id=\"login_title\">";
        // line 75
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.log_in");
        echo "</h1>
\t<h1 id=\"lost_password_title\" style=\"display: none;\">";
        // line 76
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.lost_password");
        echo "</h1>
\t";
        // line 77
        if (isset($context["failed_login_name"])) { $_failed_login_name_ = $context["failed_login_name"]; } else { $_failed_login_name_ = null; }
        if ($_failed_login_name_) {
            echo "<p class=\"error\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.login.login_failed");
            echo "</p>";
        }
        // line 78
        echo "\t";
        if (isset($context["has_done_reset"])) { $_has_done_reset_ = $context["has_done_reset"]; } else { $_has_done_reset_ = null; }
        if ($_has_done_reset_) {
            echo "<p class=\"okay\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.login.password_was_reset");
            echo "</p>";
        }
        // line 79
        echo "\t";
        if (isset($context["has_logged_out"])) { $_has_logged_out_ = $context["has_logged_out"]; } else { $_has_logged_out_ = null; }
        if ($_has_logged_out_) {
            echo "<p class=\"okay\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.login.has_logged_out");
            echo "</p>";
        }
        // line 80
        echo "
\t<div id=\"normal_view\">
\t\t<dl>
\t\t\t<dt>
                ";
        // line 84
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.email");
        echo "
\t\t\t\t";
        // line 85
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "usersourceManager"), "getWithCapability", array(0 => "form_login"), "method")) {
            echo "/ Username";
        }
        // line 86
        echo "\t\t\t</dt>
\t\t\t<dd><input type=\"text\" class=\"text\" value=\"";
        // line 87
        if (isset($context["failed_login_name"])) { $_failed_login_name_ = $context["failed_login_name"]; } else { $_failed_login_name_ = null; }
        echo twig_escape_filter($this->env, $_failed_login_name_, "html", null, true);
        echo "\" name=\"email\" id=\"email\" size=\"40\" tabindex=\"1\" /></dd>
\t\t</dl>

\t\t<div id=\"login_fields\">
\t\t\t<dl>
\t\t\t\t<dt>";
        // line 92
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.password");
        echo "</dt>
\t\t\t\t<dd class=\"password\">
\t\t\t\t\t<a href=\"";
        // line 94
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("user_login_resetpass"), "html", null, true);
        echo "\" id=\"lost_link\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.lost_qm");
        echo "</a>
\t\t\t\t\t<input type=\"password\" class=\"text\" value=\"\" name=\"password\" id=\"password\" size=\"40\"  tabindex=\"2\" />
\t\t\t\t\t<div style=\"text-align: left\">
\t\t\t\t\t\t<label style=\"margin: 0; padding: 0; font-weight: normal; font-size: 12px;\">
\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"remember_me\" value=\"1\" tabindex=\"3\" /> Remember Me
\t\t\t\t\t\t</label>
\t\t\t\t\t</div>
\t\t\t\t</dd>
\t\t\t</dl>

\t\t\t<dl>
\t\t\t\t<dt></dt>
\t\t\t\t<dd class=\"btn\"><button tabindex=\"4\">";
        // line 106
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.log_in");
        echo "</button></dd>
\t\t\t</dl>
\t\t</div>

\t\t<div id=\"lost_fields\" style=\"display: none\">
\t\t\t<dl>
\t\t\t\t<dt></dt>
\t\t\t\t<dd class=\"password-reset\">
\t\t\t\t\tEnter your email address and then click the button below to receieve instructions
\t\t\t\t\ton how to reset your password.
\t\t\t\t</dd>
\t\t\t</dl>

\t\t\t<dl>
\t\t\t\t<dt></dt>
\t\t\t\t<dd class=\"btn lost\">
\t\t\t\t\t<button id=\"do_send_lost\">";
        // line 122
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.login.email_instructions");
        echo "</button>
\t\t\t\t\t<a href=\"";
        // line 123
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("agent_login"), "html", null, true);
        echo "\" id=\"login_link\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.login.back_to_login");
        echo "</a>
\t\t\t\t</dd>
\t\t\t</dl>
\t\t</div>
\t</div>
\t<div id=\"lost_message_view\" style=\"display: none\">
\t\t";
        // line 129
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.login.sent_pw_reset_instructions");
        echo "
\t</div>
\t<div id=\"loading_view\" style=\"display: none\">
\t\t<i>";
        // line 132
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.loading");
        echo "</i>
\t</div>

</form>

<script type=\"text/javascript\">
\tdocument.getElementById('email').focus();
</script>
";
    }

    public function getTemplateName()
    {
        return "BillingBundle:Login:index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  254 => 132,  248 => 129,  237 => 123,  233 => 122,  214 => 106,  197 => 94,  192 => 92,  183 => 87,  180 => 86,  175 => 85,  171 => 84,  165 => 80,  157 => 79,  149 => 78,  142 => 77,  138 => 76,  134 => 75,  131 => 74,  123 => 73,  117 => 71,  114 => 70,  108 => 67,  104 => 65,  101 => 64,  89 => 55,  36 => 5,  32 => 3,  29 => 2,);
    }
}
