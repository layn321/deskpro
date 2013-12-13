<?php

/* AdminBundle:EmailGateways:edit-account-form.html.twig */
class __TwigTemplate_4000da95ba02948817d3a44116aeea0e extends \Application\DeskPRO\Twig\Template
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
        echo "<div data-element-handler=\"DeskPRO.Admin.ElementHandler.EditEmailGatewayPage\">

<form id=\"gateway_form\" action=\"";
        // line 3
        if (isset($context["gateway"])) { $_gateway_ = $context["gateway"]; } else { $_gateway_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_emailgateways_edit", array("id" => (($this->getAttribute($_gateway_, "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_gateway_, "id"), "0")) : ("0")))), "html", null, true);
        echo "\" method=\"post\">
";
        // line 4
        echo $this->env->getExtension('deskpro_templating')->formToken("edit_gateway");
        echo "

<input type=\"hidden\" name=\"process\" value=\"1\" />

<div class=\"dp-form\">
\t<input type=\"hidden\" name=\"gateway[connection_type]\" value=\"pop3\" />
\t<input type=\"hidden\" name=\"gateway[gateway_type]\" value=\"tickets\" />
\t<input type=\"hidden\" id=\"define_transport\" name=\"gateway[define_transport]\" value=\"";
        // line 11
        if (isset($context["editgateway"])) { $_editgateway_ = $context["editgateway"]; } else { $_editgateway_ = null; }
        if ((!$this->getAttribute($_editgateway_, "define_transport"))) {
            echo "0";
        } else {
            echo "1";
        }
        echo "\" />

\t<div class=\"dp-form-row-group\" data-element-handler=\"DeskPRO.Admin.ElementHandler.RadioExpander\" data-group-class=\"dp-input-group\" data-expand-class=\"dp-group-options\">
\t\t<div class=\"dp-form-row\">
\t\t\t<div class=\"dp-form-label\">
\t\t\t\t<label>";
        // line 16
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.gateway.account_information");
        echo "</label>
\t\t\t</div>
\t\t\t<div class=\"dp-form-input\">

\t\t\t\t";
        // line 20
        if (isset($context["errors"])) { $_errors_ = $context["errors"]; } else { $_errors_ = null; }
        if ($_errors_) {
            // line 21
            echo "\t\t\t\t<div class=\"alert-message block-message error\">
\t\t\t\t\t<ul>
\t\t\t\t\t\t";
            // line 23
            if (isset($context["errors"])) { $_errors_ = $context["errors"]; } else { $_errors_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_errors_);
            foreach ($context['_seq'] as $context["_key"] => $context["m"]) {
                // line 24
                echo "\t\t\t\t\t\t\t<li>";
                if (isset($context["m"])) { $_m_ = $context["m"]; } else { $_m_ = null; }
                echo twig_escape_filter($this->env, $_m_, "html", null, true);
                echo "</li>
\t\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['m'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 26
            echo "\t\t\t\t\t</ul>
\t\t\t\t</div>
\t\t\t\t";
        }
        // line 29
        echo "
\t\t\t\t<div class=\"dp-input-group\">
\t\t\t\t\t<label>
\t\t\t\t\t\t<input type=\"radio\" class=\"option-trigger connection-type\" name=\"gateway[connection_type]\" value=\"pop3\" ";
        // line 32
        if (isset($context["gateway"])) { $_gateway_ = $context["gateway"]; } else { $_gateway_ = null; }
        if (((!$this->getAttribute($_gateway_, "connection_type")) || ($this->getAttribute($_gateway_, "connection_type") == "pop3"))) {
            echo "checked=\"checked\"";
        }
        echo " id=\"pop3_btn\" />
\t\t\t\t\t\t";
        // line 33
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.gateway.pop3_account");
        echo "
\t\t\t\t\t</label>
\t\t\t\t\t<div class=\"dp-group-options\" style=\"display: none\">
\t\t\t\t\t\t<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td width=\"100\">Email Address:</td>
\t\t\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t\t\t";
        // line 40
        if (isset($context["form"])) { $_form_ = $context["form"]; } else { $_form_ = null; }
        echo $this->env->getExtension('form')->renderWidget($this->getAttribute($_form_, "address"), array("attr" => array("id" => "gateway_address")));
        echo "
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td width=\"100\">";
        // line 44
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.host");
        echo ":</td>
\t\t\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t\t\t";
        // line 46
        if (isset($context["form"])) { $_form_ = $context["form"]; } else { $_form_ = null; }
        echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getAttribute($_form_, "pop3_options"), "host"), array("attr" => array("class" => "small")));
        echo "<br />
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td>";
        // line 50
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.secure");
        echo ":</td>
\t\t\t\t\t\t\t\t";
        // line 51
        if ($this->env->getExtension('deskpro_templating')->getInstanceAbility("can_use_secure_smtp")) {
            // line 52
            echo "\t\t\t\t\t\t\t\t\t<td>";
            if (isset($context["form"])) { $_form_ = $context["form"]; } else { $_form_ = null; }
            echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getAttribute($_form_, "pop3_options"), "secure"), array("attr" => array("class" => "small")));
            echo "</td>
\t\t\t\t\t\t\t\t";
        } else {
            // line 54
            echo "\t\t\t\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" disabled=\"disabled\" />
\t\t\t\t\t\t\t\t\t\t<a href=\"";
            // line 56
            echo $this->env->getExtension('deskpro_templating')->getServiceUrl("dp.kb.install.error_openssl");
            echo "\" class=\"tipped\" data-tipped-options=\"maxWidth: 250\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.error_ssl_tip");
            echo "\" style=\"font-size: 90%; padding-left: 10px;\">Why is this option disabled?</a>
\t\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t";
        }
        // line 59
        echo "\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td>";
        // line 61
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.port");
        echo ":</td>
\t\t\t\t\t\t\t\t<td>";
        // line 62
        if (isset($context["form"])) { $_form_ = $context["form"]; } else { $_form_ = null; }
        echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getAttribute($_form_, "pop3_options"), "port"), array("attr" => array("class" => "small", "style" => "width: 100px")));
        echo "</td>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td>";
        // line 65
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.username");
        echo ":</td>
\t\t\t\t\t\t\t\t<td>";
        // line 66
        if (isset($context["form"])) { $_form_ = $context["form"]; } else { $_form_ = null; }
        echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getAttribute($_form_, "pop3_options"), "username"), array("attr" => array("class" => "small")));
        echo "</td>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td>";
        // line 69
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.password");
        echo ":</td>
\t\t\t\t\t\t\t\t<td>";
        // line 70
        if (isset($context["form"])) { $_form_ = $context["form"]; } else { $_form_ = null; }
        echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getAttribute($_form_, "pop3_options"), "password"), array("attr" => array("class" => "small")));
        echo "</td>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t</table>
\t\t\t\t\t\t<div class=\"test-row\">
\t\t\t\t\t\t\t<span class=\"test-button test-gateway-account-settings primary\"><span class=\"lightning-small-icon\"></span> ";
        // line 74
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.test_settings");
        echo "</span>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t";
        // line 79
        if ($this->env->getExtension('deskpro_templating')->getInstanceAbility("can_use_google_apps")) {
            // line 80
            echo "\t\t\t\t<div class=\"dp-input-group\">
\t\t\t\t\t<label>
\t\t\t\t\t\t<input type=\"radio\" class=\"option-trigger connection-type\" name=\"gateway[connection_type]\" value=\"gmail\" ";
            // line 82
            if (isset($context["gateway"])) { $_gateway_ = $context["gateway"]; } else { $_gateway_ = null; }
            if (($this->getAttribute($_gateway_, "connection_type") == "gmail")) {
                echo "checked=\"checked\"";
            }
            echo " id=\"gapps_btn\" />
\t\t\t\t\t\t";
            // line 83
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.google_apps");
            echo "
\t\t\t\t\t</label>
\t\t\t\t\t<div class=\"dp-group-options\" style=\"display: none\">
\t\t\t\t\t\t<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td width=\"120\">";
            // line 88
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.full_email_address");
            echo ":</td>
\t\t\t\t\t\t\t\t<td>";
            // line 89
            if (isset($context["form"])) { $_form_ = $context["form"]; } else { $_form_ = null; }
            echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getAttribute($_form_, "gmail_options"), "username"), array("attr" => array("class" => "small")));
            echo "</td>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td>";
            // line 92
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.password");
            echo ":</td>
\t\t\t\t\t\t\t\t<td>";
            // line 93
            if (isset($context["form"])) { $_form_ = $context["form"]; } else { $_form_ = null; }
            echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getAttribute($_form_, "gmail_options"), "password"), array("attr" => array("class" => "small")));
            echo "</td>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t</table>
\t\t\t\t\t\t<div class=\"test-row\">
\t\t\t\t\t\t\t<span class=\"test-button test-gateway-account-settings primary\"><span class=\"lightning-small-icon\"></span> ";
            // line 97
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.test_settings");
            echo "</span>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t";
        } else {
            // line 102
            echo "\t\t\t\t\t<div class=\"dp-input-group grayed\">
\t\t\t\t\t\t<label>
\t\t\t\t\t\t\t<input type=\"radio\" class=\"option-trigger connection-type\" name=\"gateway[connection_type]\" value=\"gmail\" ";
            // line 104
            if (isset($context["gateway"])) { $_gateway_ = $context["gateway"]; } else { $_gateway_ = null; }
            if (($this->getAttribute($_gateway_, "connection_type") == "gmail")) {
                echo "checked=\"checked\"";
            }
            echo " id=\"gapps_btn\" />
\t\t\t\t\t\t\t";
            // line 105
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.google_apps");
            echo "
\t\t\t\t\t\t</label>
\t\t\t\t\t\t<div class=\"dp-group-options\" style=\"display: none\">
\t\t\t\t\t\t\t<p>
\t\t\t\t\t\t\t\t";
            // line 109
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.error_ssl_feature_tip");
            echo "
\t\t\t\t\t\t\t\t<a href=\"";
            // line 110
            echo $this->env->getExtension('deskpro_templating')->getServiceUrl("dp.kb.install.error_openssl");
            echo "\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.error_ssl_feature_link");
            echo "</a>
\t\t\t\t\t\t\t</p>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t";
        }
        // line 115
        echo "\t\t\t</div>
\t\t</div>
\t</div>

\t<div id=\"smtp_options_default\" ";
        // line 119
        if (isset($context["editgateway"])) { $_editgateway_ = $context["editgateway"]; } else { $_editgateway_ = null; }
        if (isset($context["transport"])) { $_transport_ = $context["transport"]; } else { $_transport_ = null; }
        if (($this->getAttribute($_editgateway_, "define_transport") || (!$this->getAttribute($_transport_, "id")))) {
            echo "style=\"display: none\"";
        }
        echo ">
\t\t<div class=\"dp-form-row\">
\t\t\t<div class=\"dp-form-label\">
\t\t\t\t<label>";
        // line 122
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.gateway.outgoing_mail_account");
        echo "</label>
\t\t\t</div>
\t\t\t<div class=\"dp-form-input\">
\t\t\t\t<span class=\"show-gapps\" ";
        // line 125
        if (isset($context["gateway"])) { $_gateway_ = $context["gateway"]; } else { $_gateway_ = null; }
        if (($this->getAttribute($_gateway_, "connection_type") != "gmail")) {
            echo "style=\"display: none\"";
        }
        echo ">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.gateway.use_google_for_outgoing");
        echo " <button class=\"clean-white small toggle-custom-smtp\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.gateway.use_different_outgoing_account");
        echo "</button></span>
\t\t\t</div>
\t\t</div>
\t</div>

\t<div id=\"smtp_options\" ";
        // line 130
        if (isset($context["editgateway"])) { $_editgateway_ = $context["editgateway"]; } else { $_editgateway_ = null; }
        if (isset($context["transport"])) { $_transport_ = $context["transport"]; } else { $_transport_ = null; }
        if (((!$this->getAttribute($_editgateway_, "define_transport")) && $this->getAttribute($_transport_, "id"))) {
            echo "style=\"display: none;\"";
        }
        echo " data-element-handler=\"DeskPRO.Admin.ElementHandler.EditEmailTransportPage\" data-overlay-id=\"test_settings_overlay2\">
\t\t<div class=\"dp-form-row-group\" data-element-handler=\"DeskPRO.Admin.ElementHandler.RadioExpander\" data-group-class=\"dp-input-group\" data-expand-class=\"dp-group-options\">
\t\t\t<div class=\"dp-form-row\">
\t\t\t\t<div class=\"dp-form-label\">
\t\t\t\t\t<label>";
        // line 134
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.gateway.outgoing_mail_account");
        echo "</label>
\t\t\t\t</div>
\t\t\t\t<div class=\"dp-form-input\">
\t\t\t\t\t<div class=\"dp-input-group\">
\t\t\t\t\t\t<label>
\t\t\t\t\t\t\t<input type=\"radio\" class=\"option-trigger\" name=\"transport[transport_type]\" value=\"mail\" ";
        // line 139
        if (isset($context["transport"])) { $_transport_ = $context["transport"]; } else { $_transport_ = null; }
        if (($this->getAttribute($_transport_, "transport_type") == "mail")) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\t\t\t\t";
        // line 140
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.php_mail");
        echo "
\t\t\t\t\t\t</label>

\t\t\t\t\t\t<div class=\"dp-group-options\" style=\"display: none\">
\t\t\t\t\t\t\t<div class=\"test-row\">
\t\t\t\t\t\t\t\t<span class=\"test-button test-account-settings primary\"><span class=\"lightning-small-icon\"></span> ";
        // line 145
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.test_settings");
        echo "</span>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>

\t\t\t\t\t<div class=\"dp-input-group\">
\t\t\t\t\t\t<label>
\t\t\t\t\t\t\t<input type=\"radio\" class=\"option-trigger\" name=\"transport[transport_type]\" value=\"smtp\" ";
        // line 152
        if (isset($context["transport"])) { $_transport_ = $context["transport"]; } else { $_transport_ = null; }
        if (((!$this->getAttribute($_transport_, "transport_type")) || ($this->getAttribute($_transport_, "transport_type") == "smtp"))) {
            echo "checked=\"checked\"";
        }
        echo " />
\t\t\t\t\t\t\t";
        // line 153
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.smtp_account");
        echo "
\t\t\t\t\t\t</label>
\t\t\t\t\t\t<div class=\"dp-group-options\" style=\"display: none\">
\t\t\t\t\t\t\t<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">
\t\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t\t<td class=\"title\" width=\"120\">";
        // line 158
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.host");
        echo ":</td>
\t\t\t\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t\t\t\t";
        // line 160
        if (isset($context["trans_form"])) { $_trans_form_ = $context["trans_form"]; } else { $_trans_form_ = null; }
        echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getAttribute($_trans_form_, "smtp_options"), "host"), array("attr" => array("class" => "small")));
        echo "<br />
\t\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t\t<td class=\"title\">Connection Security:</td>
\t\t\t\t\t\t\t\t\t";
        // line 165
        if ($this->env->getExtension('deskpro_templating')->getInstanceAbility("can_use_secure_smtp")) {
            // line 166
            echo "\t\t\t\t\t\t\t\t\t\t<td>";
            if (isset($context["trans_form"])) { $_trans_form_ = $context["trans_form"]; } else { $_trans_form_ = null; }
            echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getAttribute($_trans_form_, "smtp_options"), "secure"), array("attr" => array("class" => "small")));
            echo "</td>
\t\t\t\t\t\t\t\t\t";
        } else {
            // line 168
            echo "\t\t\t\t\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" disabled=\"disabled\" />
\t\t\t\t\t\t\t\t\t\t\t<a href=\"";
            // line 170
            echo $this->env->getExtension('deskpro_templating')->getServiceUrl("dp.kb.install.error_openssl");
            echo "\" class=\"tipped\" data-tipped-options=\"maxWidth: 250\" title=\"";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.error_ssl_tip");
            echo "\" style=\"font-size: 90%; padding-left: 10px;\">Why is this option disabled?</a>
\t\t\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t\t";
        }
        // line 173
        echo "\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t\t<td class=\"title\">";
        // line 175
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.port");
        echo ":</td>
\t\t\t\t\t\t\t\t\t<td>";
        // line 176
        if (isset($context["trans_form"])) { $_trans_form_ = $context["trans_form"]; } else { $_trans_form_ = null; }
        echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getAttribute($_trans_form_, "smtp_options"), "port"), array("attr" => array("class" => "small", "style" => "width: 100px")));
        echo "</td>
\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t\t<td class=\"title\">";
        // line 179
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.requires_auth");
        echo ":</td>
\t\t\t\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t\t\t\t<input
\t\t\t\t\t\t\t\t\t\t\ttype=\"checkbox\"
\t\t\t\t\t\t\t\t\t\t\tid=\"smtp_requires_auth_check\"
\t\t\t\t\t\t\t\t\t\t\t";
        // line 184
        if (isset($context["edittrans"])) { $_edittrans_ = $context["edittrans"]; } else { $_edittrans_ = null; }
        if (($this->getAttribute($this->getAttribute($_edittrans_, "smtp_options"), "username") || $this->getAttribute($this->getAttribute($_edittrans_, "smtp_options"), "password"))) {
            echo "checked=\"checked\"";
        }
        // line 185
        echo "\t\t\t\t\t\t\t\t\t\t\tdata-element-handler=\"DeskPRO.ElementHandler.CheckboxToggle\"
\t\t\t\t\t\t\t\t\t\t\tdata-targets=\"#smtp_auth_username, #smtp_auth_password\"
\t\t\t\t\t\t\t\t\t\t\tonclick=\"\$('#transport_smtp_options_username, #transport_smtp_options_password').val('');\"
\t\t\t\t\t\t\t\t\t\t/> ";
        // line 188
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.yes");
        echo "
\t\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t\t<tr id=\"smtp_auth_username\">
\t\t\t\t\t\t\t\t\t<td class=\"title\">";
        // line 192
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.username");
        echo ":</td>
\t\t\t\t\t\t\t\t\t<td>";
        // line 193
        if (isset($context["trans_form"])) { $_trans_form_ = $context["trans_form"]; } else { $_trans_form_ = null; }
        echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getAttribute($_trans_form_, "smtp_options"), "username"), array("attr" => array("class" => "small")));
        echo "</td>
\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t\t<tr id=\"smtp_auth_password\">
\t\t\t\t\t\t\t\t\t<td class=\"title\">";
        // line 196
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.password");
        echo ":</td>
\t\t\t\t\t\t\t\t\t<td>";
        // line 197
        if (isset($context["trans_form"])) { $_trans_form_ = $context["trans_form"]; } else { $_trans_form_ = null; }
        echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getAttribute($_trans_form_, "smtp_options"), "password"), array("attr" => array("class" => "small")));
        echo "</td>
\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t</table>

\t\t\t\t\t\t\t<div class=\"test-row\">
\t\t\t\t\t\t\t\t<span class=\"test-button test-account-settings primary\"><span class=\"lightning-small-icon\"></span> ";
        // line 202
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.test_settings");
        echo "</span>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>

\t\t\t\t\t";
        // line 207
        if ($this->env->getExtension('deskpro_templating')->getInstanceAbility("can_use_google_apps")) {
            // line 208
            echo "\t\t\t\t\t<div class=\"dp-input-group hide-pop3\">
\t\t\t\t\t\t<label>
\t\t\t\t\t\t\t<input type=\"radio\" class=\"option-trigger\" name=\"transport[transport_type]\" value=\"gmail\" ";
            // line 210
            if (isset($context["transport"])) { $_transport_ = $context["transport"]; } else { $_transport_ = null; }
            if (($this->getAttribute($_transport_, "transport_type") == "gmail")) {
                echo "checked=\"checked\"";
            }
            echo " />
\t\t\t\t\t\t\t";
            // line 211
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.google_apps");
            echo "
\t\t\t\t\t\t</label>
\t\t\t\t\t\t<div class=\"dp-group-options\" ";
            // line 213
            if (isset($context["transport"])) { $_transport_ = $context["transport"]; } else { $_transport_ = null; }
            if (($this->getAttribute($_transport_, "transport_type") != "gmail")) {
                echo "style=\"display: none\"";
            }
            echo ">
\t\t\t\t\t\t\t<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">
\t\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t\t<td class=\"title\" width=\"120\">";
            // line 216
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.full_email_address");
            echo ":</td>
\t\t\t\t\t\t\t\t\t<td>";
            // line 217
            if (isset($context["trans_form"])) { $_trans_form_ = $context["trans_form"]; } else { $_trans_form_ = null; }
            echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getAttribute($_trans_form_, "gmail_options"), "username"), array("attr" => array("class" => "small")));
            echo "</td>
\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t\t<td class=\"title\">";
            // line 220
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.password");
            echo ":</td>
\t\t\t\t\t\t\t\t\t<td>";
            // line 221
            if (isset($context["trans_form"])) { $_trans_form_ = $context["trans_form"]; } else { $_trans_form_ = null; }
            echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getAttribute($_trans_form_, "gmail_options"), "password"), array("attr" => array("class" => "small")));
            echo "</td>
\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t</table>
\t\t\t\t\t\t\t<div class=\"test-row\">
\t\t\t\t\t\t\t\t<span class=\"test-button test-account-settings primary\"><span class=\"lightning-small-icon\"></span> ";
            // line 225
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.test_settings");
            echo "</span>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t\t";
        }
        // line 230
        echo "\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>

\t<div class=\"dp-form-row\" ";
        // line 235
        if (isset($context["partial"])) { $_partial_ = $context["partial"]; } else { $_partial_ = null; }
        if (($_partial_ == "setup")) {
            echo "style=\"display: none\"";
        }
        echo ">
\t\t<div class=\"dp-form-label\">
\t\t\t<label>";
        // line 237
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.gateway.enabled");
        echo "</label>
\t\t</div>
\t\t<div class=\"dp-form-input\">
\t\t\t<label>
\t\t\t\t";
        // line 241
        if (isset($context["form"])) { $_form_ = $context["form"]; } else { $_form_ = null; }
        echo $this->env->getExtension('form')->renderWidget($this->getAttribute($_form_, "is_enabled"));
        echo "
\t\t\t\t";
        // line 242
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.gateway.confirm_process_account");
        echo "
\t\t\t</label>
\t\t</div>
\t</div>

\t<div class=\"dp-form-row show-pop3 hide-gmail\">
\t\t<div class=\"dp-form-label\">
\t\t\t<label>Keep Messages</label>
\t\t</div>
\t\t<div class=\"dp-form-input\">
\t\t\t<label>
\t\t\t\t";
        // line 253
        if (isset($context["form"])) { $_form_ = $context["form"]; } else { $_form_ = null; }
        echo $this->env->getExtension('form')->renderWidget($this->getAttribute($_form_, "keep_read"));
        echo "
\t\t\t\tWhen this option is enabled, processed messages will not be deleted off of the mail server.
\t\t\t\tFor efficiency reasons,\tit is recommended keep this option <em>off</em>, especially for mailboxes with large volumes of mail.
\t\t\t\tDeskPRO will stop processing accounts when they contain more than 2500 messages. <a href=\"https://support.deskpro.com/kb/articles/145\" target=\"_blank\">Read more about this setting &rarr;</a>
\t\t\t</label>
\t\t</div>
\t</div>

\t<div class=\"dp-form-row\">
\t\t<div class=\"dp-form-label\">
\t\t\t<label>Linked Department</label>
\t\t</div>
\t\t<div class=\"dp-form-input\">
\t\t\t<select name=\"department_id\" id=\"linked_department_id\">
\t\t\t\t<option value=\"0\" ";
        // line 267
        if (isset($context["gateway"])) { $_gateway_ = $context["gateway"]; } else { $_gateway_ = null; }
        if (isset($context["selected_department_id"])) { $_selected_department_id_ = $context["selected_department_id"]; } else { $_selected_department_id_ = null; }
        if (((!$this->getAttribute($_gateway_, "department")) && (!$_selected_department_id_))) {
            echo "selected=\"selected\"";
        }
        echo ">Do not link a department</option>
\t\t\t\t";
        // line 268
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($_app_, "departments"), "getRootNodes", array(), "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["dep"]) {
            if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
            if ($this->getAttribute($_dep_, "is_tickets_enabled")) {
                // line 269
                echo "\t\t\t\t\t";
                if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                if (twig_length_filter($this->env, $this->getAttribute($_dep_, "children"))) {
                    // line 270
                    echo "\t\t\t\t\t\t<optgroup label=\"";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                    echo "\">
\t\t\t\t\t\t\t";
                    // line 271
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($_dep_, "children"));
                    foreach ($context['_seq'] as $context["_key"] => $context["subdep"]) {
                        // line 272
                        echo "\t\t\t\t\t\t\t\t<option data-full-title=\"";
                        if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                        echo " &gt; ";
                        if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_subdep_, "title"), "html", null, true);
                        echo "\" value=\"";
                        if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_subdep_, "id"), "html", null, true);
                        echo "\" ";
                        if (isset($context["gateway"])) { $_gateway_ = $context["gateway"]; } else { $_gateway_ = null; }
                        if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                        if (isset($context["selected_department_id"])) { $_selected_department_id_ = $context["selected_department_id"]; } else { $_selected_department_id_ = null; }
                        if ((($this->getAttribute($this->getAttribute($_gateway_, "department"), "id") == $this->getAttribute($_subdep_, "id")) || ($_selected_department_id_ == $this->getAttribute($_subdep_, "id")))) {
                            echo "selected=\"selected\"";
                        }
                        echo ">";
                        if (isset($context["subdep"])) { $_subdep_ = $context["subdep"]; } else { $_subdep_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_subdep_, "title"), "html", null, true);
                        echo "</option>
\t\t\t\t\t\t\t";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subdep'], $context['_parent'], $context['loop']);
                    $context = array_merge($_parent, array_intersect_key($context, $_parent));
                    // line 274
                    echo "\t\t\t\t\t\t</optgroup>
\t\t\t\t\t";
                } else {
                    // line 276
                    echo "\t\t\t\t\t\t<option value=\"";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "id"), "html", null, true);
                    echo "\" ";
                    if (isset($context["gateway"])) { $_gateway_ = $context["gateway"]; } else { $_gateway_ = null; }
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    if (isset($context["selected_department_id"])) { $_selected_department_id_ = $context["selected_department_id"]; } else { $_selected_department_id_ = null; }
                    if ((($this->getAttribute($this->getAttribute($_gateway_, "department"), "id") == $this->getAttribute($_dep_, "id")) || ($_selected_department_id_ == $this->getAttribute($_dep_, "id")))) {
                        echo "selected=\"selected\"";
                    }
                    echo ">";
                    if (isset($context["dep"])) { $_dep_ = $context["dep"]; } else { $_dep_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_dep_, "title"), "html", null, true);
                    echo "</option>
\t\t\t\t\t";
                }
                // line 278
                echo "\t\t\t\t";
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['dep'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 279
        echo "\t\t\t</select>
\t\t</div>
\t</div>
</div>

<div class=\"is-not-loading\">
\t<button class=\"clean-white\" id=\"form_save\">";
        // line 285
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.save");
        echo "</button>
</div>
<div class=\"is-loading\">
\t<i class=\"flat-spinner\"></i>
</div>
</form>

<div id=\"test_gateway_settings_overlay\" style=\"width: 600px; height: 390px; overflow:auto; display: none;\">
\t<div class=\"overlay-title\">
\t\t<span class=\"close-overlay close-trigger\"></span>
\t\t<h4>";
        // line 295
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.gateway.test_incoming_email_account");
        echo "</h4>
\t</div>
\t<div class=\"overlay-content\">
\t\t<div class=\"result\">
\t\t\t<div class=\"is-loading loading-icon-big\"><br /><br /></div>
\t\t\t<div class=\"is-not-loading\" style=\"display: block\">
\t\t\t\t<div class=\"success\" style=\"display:none\">
\t\t\t\t\tWe were able to connect successfully. Your mailbox has <span class=\"placeholder-num\"></span> messages.
\t\t\t\t</div>
\t\t\t\t<div class=\"error\" style=\"display:none\">
\t\t\t\t\t<p class=\"error-explain\">";
        // line 305
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.error_sending_test");
        echo "</p>
\t\t\t\t\t<div class=\"error-msg\" style=\"margin-top: 8px; padding: 8px; background-color: #fff;\">
\t\t\t\t\t\t<div class=\"error-msg-text\"></div>
\t\t\t\t\t\t<textarea class=\"error-msg-log\" style=\"width: 95%; height: 150px; font-size: 11px; font-family: Monaco, Courier, monospace; white-space: nowrap; overflow: auto;\" wrap=\"off\"></textarea>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>
\t<div class=\"overlay-footer\">
\t\t<button
\t\t\tclass=\"clean-white test-trigger\"
\t\t\tdata-url=\"";
        // line 317
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_emailgateways_testaccount"), "html", null, true);
        echo "\"
\t\t>Test Again</button>
\t</div>
</div>

<div id=\"test_settings_overlay2\" style=\"width: 600px; height: 280px; overflow:auto; display: none;\">
\t<div class=\"overlay-title\">
\t\t<span class=\"close-overlay close-trigger\"></span>
\t\t<h4>";
        // line 325
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.test_outgoing_email");
        echo "</h4>
\t</div>
\t<div class=\"overlay-content\">
\t\t<div style=\"margin-bottom: 3px;\">";
        // line 328
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.send_a_test_email_to");
        echo ": <input type=\"text\" class=\"small\" name=\"send_to\" value=\"";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "user"), "primaryEmailAddress", array(), "method"), "html", null, true);
        echo "\" /></div>
\t\t";
        // line 329
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.from_email_address");
        echo ": <input type=\"text\" class=\"small\" name=\"send_from\" id=\"test_send_from\" value=\"\" />
\t\t<div class=\"result\">
\t\t\t<div class=\"is-loading loading-icon-big\"><br /><br /></div>
\t\t\t<div class=\"is-not-loading\">
\t\t\t\t<div class=\"success\">
\t\t\t\t\t";
        // line 334
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.notice_no_errors_detected");
        echo "
\t\t\t\t</div>
\t\t\t\t<div class=\"error\">
\t\t\t\t\t";
        // line 337
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.error_sending_test");
        echo "
\t\t\t\t\t<div class=\"error-msg\" style=\"margin-top: 8px; padding: 8px; background-color: #fff;\">
\t\t\t\t\t\t<div class=\"error-msg-text\"></div>
\t\t\t\t\t\t<textarea class=\"error-msg-log\" style=\"width: 95%; height: 150px; font-size: 11px; font-family: Monaco, Courier, monospace; white-space: nowrap; overflow: auto;\" wrap=\"off\"></textarea>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>
\t<div class=\"overlay-footer\">
\t\t<button
\t\t\tclass=\"clean-white test-trigger\"
\t\t\tdata-url=\"";
        // line 349
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_emailtrans_testaccount"), "html", null, true);
        echo "\"
\t\t>";
        // line 350
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.test_now");
        echo "</button>
\t</div>
</div>

</div>";
    }

    public function getTemplateName()
    {
        return "AdminBundle:EmailGateways:edit-account-form.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  659 => 274,  562 => 245,  548 => 238,  558 => 94,  479 => 82,  589 => 101,  457 => 133,  413 => 150,  953 => 406,  948 => 403,  935 => 394,  929 => 391,  916 => 382,  864 => 365,  844 => 348,  816 => 342,  807 => 339,  801 => 338,  774 => 337,  766 => 328,  737 => 314,  685 => 300,  664 => 294,  635 => 288,  593 => 269,  546 => 91,  532 => 231,  865 => 221,  852 => 216,  838 => 208,  820 => 201,  781 => 333,  764 => 178,  725 => 164,  632 => 141,  602 => 105,  565 => 117,  529 => 111,  505 => 207,  487 => 104,  473 => 221,  1853 => 842,  1849 => 841,  1839 => 837,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 813,  1778 => 812,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 782,  1705 => 781,  1688 => 770,  1681 => 769,  1677 => 768,  1667 => 764,  1656 => 762,  1648 => 757,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 714,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 695,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 651,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 610,  1289 => 607,  1276 => 604,  1263 => 601,  1244 => 596,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 565,  1181 => 561,  1171 => 557,  1158 => 550,  1114 => 530,  1104 => 526,  1072 => 512,  1062 => 508,  1024 => 494,  1014 => 490,  1000 => 482,  990 => 478,  980 => 474,  960 => 466,  918 => 448,  888 => 376,  834 => 346,  673 => 342,  636 => 323,  462 => 209,  454 => 192,  1144 => 542,  1139 => 399,  1131 => 399,  1127 => 397,  1110 => 396,  1092 => 391,  1089 => 390,  1086 => 520,  1084 => 388,  1063 => 387,  1060 => 386,  1055 => 385,  1050 => 384,  1035 => 372,  1019 => 267,  1003 => 263,  959 => 251,  900 => 377,  880 => 434,  870 => 430,  867 => 366,  859 => 362,  848 => 215,  839 => 197,  828 => 191,  823 => 188,  809 => 181,  800 => 178,  797 => 177,  794 => 176,  786 => 174,  740 => 162,  734 => 313,  703 => 354,  693 => 350,  630 => 412,  626 => 140,  614 => 138,  610 => 385,  581 => 124,  564 => 229,  525 => 236,  722 => 162,  697 => 256,  674 => 249,  671 => 425,  577 => 261,  569 => 248,  557 => 368,  502 => 267,  497 => 194,  445 => 95,  729 => 159,  684 => 261,  676 => 297,  669 => 254,  660 => 145,  647 => 243,  643 => 244,  601 => 175,  570 => 211,  522 => 200,  501 => 148,  296 => 104,  374 => 119,  631 => 111,  616 => 281,  608 => 137,  605 => 16,  596 => 15,  574 => 165,  561 => 209,  527 => 147,  433 => 93,  388 => 110,  426 => 177,  383 => 62,  461 => 18,  370 => 143,  395 => 166,  294 => 76,  223 => 95,  220 => 88,  492 => 210,  468 => 21,  444 => 131,  410 => 229,  397 => 117,  377 => 120,  262 => 105,  250 => 147,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 2108,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 1383,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 1303,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 1173,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 1041,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 970,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 538,  1126 => 916,  1106 => 900,  1100 => 897,  1026 => 841,  997 => 822,  983 => 816,  975 => 402,  956 => 800,  939 => 786,  902 => 753,  894 => 749,  879 => 373,  757 => 631,  727 => 608,  716 => 605,  670 => 296,  528 => 221,  476 => 140,  435 => 195,  354 => 145,  341 => 278,  192 => 86,  321 => 163,  243 => 97,  793 => 350,  780 => 348,  758 => 177,  700 => 154,  686 => 150,  652 => 274,  638 => 414,  620 => 139,  545 => 237,  523 => 110,  494 => 205,  459 => 99,  438 => 172,  351 => 79,  347 => 104,  402 => 168,  268 => 103,  430 => 237,  411 => 182,  379 => 158,  322 => 135,  315 => 110,  289 => 119,  284 => 128,  255 => 104,  234 => 85,  1133 => 400,  1124 => 534,  1121 => 56,  1116 => 55,  1113 => 403,  1108 => 51,  1103 => 394,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 500,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 329,  996 => 262,  989 => 316,  985 => 315,  981 => 257,  977 => 313,  970 => 91,  966 => 416,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 244,  919 => 290,  917 => 289,  908 => 444,  905 => 378,  896 => 280,  891 => 227,  877 => 270,  862 => 267,  857 => 265,  837 => 347,  832 => 250,  827 => 248,  821 => 247,  803 => 179,  778 => 389,  769 => 165,  765 => 386,  753 => 328,  746 => 175,  743 => 318,  735 => 170,  730 => 218,  720 => 363,  717 => 362,  712 => 156,  691 => 204,  678 => 149,  654 => 144,  587 => 14,  576 => 179,  539 => 241,  517 => 217,  471 => 190,  441 => 199,  437 => 184,  418 => 175,  386 => 2,  373 => 144,  304 => 151,  270 => 123,  265 => 63,  229 => 82,  477 => 138,  455 => 325,  448 => 164,  429 => 179,  407 => 119,  399 => 156,  389 => 174,  375 => 167,  358 => 286,  349 => 118,  335 => 137,  327 => 132,  298 => 118,  280 => 109,  249 => 94,  194 => 80,  142 => 72,  344 => 83,  318 => 135,  306 => 122,  295 => 117,  357 => 119,  300 => 106,  286 => 80,  276 => 87,  269 => 109,  254 => 97,  128 => 50,  237 => 138,  165 => 80,  122 => 66,  798 => 337,  770 => 179,  759 => 112,  748 => 337,  731 => 108,  721 => 305,  718 => 307,  708 => 295,  696 => 147,  617 => 107,  590 => 226,  553 => 225,  550 => 156,  540 => 84,  533 => 82,  500 => 206,  493 => 225,  489 => 343,  482 => 223,  467 => 210,  464 => 120,  458 => 193,  452 => 117,  449 => 132,  415 => 6,  382 => 166,  372 => 215,  361 => 141,  356 => 58,  339 => 139,  302 => 120,  285 => 113,  258 => 98,  123 => 32,  108 => 45,  424 => 184,  394 => 86,  380 => 121,  338 => 155,  319 => 72,  316 => 53,  312 => 87,  290 => 146,  267 => 96,  206 => 54,  110 => 47,  240 => 37,  224 => 89,  219 => 76,  217 => 93,  202 => 106,  186 => 57,  170 => 81,  100 => 55,  67 => 11,  14 => 1,  1096 => 524,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 836,  1013 => 275,  1007 => 274,  1002 => 823,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 402,  940 => 298,  937 => 395,  928 => 452,  926 => 243,  915 => 239,  912 => 236,  903 => 231,  898 => 440,  892 => 229,  889 => 748,  887 => 227,  884 => 374,  876 => 222,  874 => 215,  871 => 368,  863 => 214,  861 => 213,  858 => 212,  850 => 352,  843 => 206,  840 => 406,  815 => 245,  812 => 244,  808 => 199,  804 => 395,  799 => 196,  791 => 193,  785 => 238,  775 => 184,  771 => 183,  754 => 340,  728 => 309,  726 => 169,  723 => 168,  715 => 105,  711 => 152,  709 => 162,  706 => 155,  698 => 208,  694 => 153,  692 => 155,  689 => 254,  681 => 150,  677 => 149,  675 => 148,  663 => 276,  661 => 277,  650 => 246,  646 => 143,  629 => 129,  627 => 21,  625 => 236,  622 => 270,  598 => 174,  592 => 117,  586 => 170,  575 => 214,  566 => 241,  556 => 157,  554 => 240,  541 => 222,  536 => 225,  515 => 209,  511 => 108,  509 => 34,  488 => 208,  486 => 207,  483 => 341,  465 => 196,  463 => 216,  450 => 202,  432 => 190,  419 => 155,  371 => 153,  362 => 100,  353 => 150,  337 => 18,  333 => 134,  309 => 94,  303 => 86,  299 => 122,  291 => 115,  272 => 82,  261 => 156,  253 => 89,  239 => 88,  235 => 93,  213 => 91,  200 => 91,  198 => 96,  159 => 69,  149 => 74,  146 => 73,  131 => 47,  116 => 29,  79 => 33,  74 => 52,  71 => 25,  836 => 320,  817 => 398,  814 => 200,  811 => 317,  805 => 313,  787 => 657,  779 => 169,  776 => 305,  773 => 347,  761 => 296,  751 => 163,  747 => 325,  742 => 336,  739 => 171,  736 => 317,  724 => 214,  705 => 278,  702 => 601,  688 => 113,  680 => 278,  667 => 295,  662 => 146,  656 => 418,  649 => 291,  644 => 97,  641 => 241,  624 => 109,  613 => 106,  607 => 232,  597 => 221,  591 => 131,  584 => 259,  579 => 234,  563 => 162,  559 => 237,  551 => 235,  547 => 114,  537 => 90,  524 => 220,  512 => 227,  507 => 76,  504 => 213,  498 => 142,  485 => 29,  480 => 28,  472 => 139,  466 => 217,  460 => 215,  447 => 188,  442 => 185,  434 => 110,  428 => 11,  422 => 176,  404 => 149,  368 => 164,  364 => 152,  340 => 189,  334 => 115,  330 => 133,  325 => 112,  292 => 83,  287 => 49,  282 => 119,  279 => 78,  273 => 110,  266 => 106,  256 => 95,  252 => 94,  228 => 113,  218 => 32,  201 => 80,  64 => 25,  51 => 16,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 1705,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 737,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 731,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 725,  1559 => 601,  1552 => 720,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 708,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 645,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 624,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 1001,  1257 => 1000,  1254 => 479,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 569,  1196 => 958,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 938,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 388,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 898,  1099 => 393,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 880,  1076 => 359,  1070 => 875,  1057 => 352,  1052 => 504,  1045 => 382,  1040 => 377,  1036 => 283,  1032 => 496,  1028 => 274,  1023 => 393,  1020 => 392,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 821,  979 => 379,  974 => 256,  967 => 373,  962 => 803,  958 => 801,  954 => 253,  950 => 368,  945 => 367,  942 => 460,  938 => 365,  934 => 364,  927 => 361,  923 => 387,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 272,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 410,  841 => 210,  835 => 312,  830 => 249,  826 => 202,  822 => 308,  818 => 246,  813 => 183,  810 => 340,  806 => 180,  802 => 198,  795 => 336,  792 => 239,  789 => 349,  784 => 188,  782 => 187,  777 => 291,  772 => 289,  768 => 334,  763 => 327,  760 => 329,  756 => 230,  752 => 176,  745 => 281,  741 => 280,  738 => 379,  732 => 171,  719 => 279,  714 => 264,  710 => 310,  704 => 267,  699 => 303,  695 => 285,  690 => 263,  687 => 279,  683 => 346,  679 => 298,  672 => 255,  668 => 247,  665 => 201,  658 => 244,  645 => 270,  640 => 142,  634 => 413,  628 => 271,  623 => 238,  619 => 282,  611 => 268,  606 => 279,  603 => 267,  599 => 242,  595 => 132,  583 => 169,  580 => 100,  573 => 260,  560 => 101,  543 => 172,  538 => 232,  534 => 281,  530 => 202,  526 => 213,  521 => 146,  518 => 235,  514 => 183,  510 => 202,  503 => 75,  496 => 345,  490 => 83,  484 => 143,  474 => 25,  470 => 168,  446 => 318,  440 => 114,  436 => 113,  431 => 12,  425 => 193,  416 => 104,  412 => 98,  408 => 185,  403 => 126,  400 => 225,  396 => 299,  392 => 152,  385 => 117,  381 => 170,  367 => 82,  363 => 139,  359 => 153,  355 => 285,  350 => 94,  346 => 140,  343 => 143,  328 => 17,  324 => 164,  313 => 133,  307 => 108,  301 => 69,  288 => 102,  283 => 115,  271 => 97,  257 => 76,  251 => 102,  238 => 92,  233 => 100,  195 => 37,  191 => 74,  187 => 46,  183 => 70,  130 => 69,  88 => 21,  76 => 23,  115 => 44,  95 => 22,  655 => 275,  651 => 232,  648 => 231,  637 => 241,  633 => 272,  621 => 408,  618 => 269,  615 => 232,  604 => 186,  600 => 516,  588 => 305,  585 => 253,  582 => 203,  571 => 242,  567 => 95,  555 => 207,  552 => 190,  549 => 208,  544 => 230,  542 => 207,  535 => 112,  531 => 358,  519 => 87,  516 => 229,  513 => 216,  508 => 86,  506 => 83,  499 => 211,  495 => 147,  491 => 145,  481 => 103,  478 => 202,  475 => 184,  469 => 197,  456 => 136,  451 => 96,  443 => 132,  439 => 129,  427 => 155,  423 => 109,  420 => 192,  409 => 89,  405 => 148,  401 => 148,  391 => 86,  387 => 129,  384 => 160,  378 => 145,  365 => 289,  360 => 158,  348 => 139,  336 => 135,  332 => 107,  329 => 152,  323 => 130,  310 => 109,  305 => 125,  277 => 99,  274 => 109,  263 => 147,  259 => 100,  247 => 88,  244 => 91,  241 => 96,  222 => 90,  210 => 85,  207 => 80,  204 => 49,  184 => 84,  181 => 63,  167 => 41,  157 => 61,  96 => 24,  421 => 138,  417 => 71,  414 => 173,  406 => 170,  398 => 147,  393 => 165,  390 => 221,  376 => 110,  369 => 94,  366 => 156,  352 => 140,  345 => 138,  342 => 137,  331 => 134,  326 => 102,  320 => 130,  317 => 134,  314 => 125,  311 => 78,  308 => 144,  297 => 120,  293 => 119,  281 => 111,  278 => 125,  275 => 34,  264 => 103,  260 => 73,  248 => 144,  245 => 104,  242 => 118,  231 => 92,  227 => 58,  215 => 84,  212 => 83,  209 => 82,  197 => 48,  177 => 43,  171 => 83,  161 => 62,  132 => 51,  121 => 28,  105 => 61,  99 => 28,  81 => 21,  77 => 17,  180 => 83,  176 => 62,  156 => 38,  143 => 47,  139 => 46,  118 => 64,  189 => 85,  185 => 78,  173 => 64,  166 => 56,  152 => 75,  174 => 83,  164 => 39,  154 => 51,  150 => 45,  137 => 39,  133 => 70,  127 => 68,  107 => 40,  102 => 31,  83 => 22,  78 => 18,  53 => 7,  23 => 3,  42 => 6,  138 => 69,  134 => 52,  109 => 32,  103 => 23,  97 => 33,  94 => 34,  84 => 22,  75 => 32,  69 => 17,  66 => 11,  54 => 7,  44 => 7,  230 => 60,  226 => 87,  203 => 128,  193 => 49,  188 => 64,  182 => 77,  178 => 43,  168 => 65,  163 => 79,  160 => 45,  155 => 72,  148 => 71,  145 => 56,  140 => 33,  136 => 48,  125 => 65,  120 => 46,  113 => 32,  101 => 41,  92 => 26,  89 => 25,  85 => 29,  73 => 16,  62 => 14,  59 => 15,  56 => 24,  41 => 5,  126 => 48,  119 => 63,  111 => 36,  106 => 33,  98 => 44,  93 => 29,  86 => 35,  70 => 24,  60 => 13,  28 => 4,  36 => 5,  114 => 62,  104 => 57,  91 => 28,  80 => 26,  63 => 10,  58 => 20,  40 => 6,  34 => 4,  45 => 7,  61 => 21,  55 => 11,  48 => 7,  39 => 5,  35 => 4,  31 => 3,  26 => 2,  21 => 36,  46 => 8,  29 => 2,  57 => 10,  50 => 45,  47 => 6,  38 => 11,  33 => 4,  49 => 6,  32 => 3,  246 => 93,  236 => 86,  232 => 135,  225 => 59,  221 => 56,  216 => 71,  214 => 73,  211 => 72,  208 => 71,  205 => 82,  199 => 79,  196 => 65,  190 => 79,  179 => 69,  175 => 82,  172 => 66,  169 => 45,  162 => 40,  158 => 78,  153 => 59,  151 => 56,  147 => 100,  144 => 37,  141 => 54,  135 => 36,  129 => 11,  124 => 33,  117 => 37,  112 => 28,  90 => 32,  87 => 34,  82 => 19,  72 => 18,  68 => 16,  65 => 23,  52 => 10,  43 => 6,  37 => 5,  30 => 3,  27 => 2,  25 => 2,  24 => 37,  22 => 16,  19 => 1,);
    }
}
