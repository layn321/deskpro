<?php

/* BillingBundle:Main:index.html.twig */
class __TwigTemplate_3c4aeede96b1cce983f9bffb794e1c39 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("BillingBundle::layout.html.twig");

        $this->blocks = array(
            'page_js_exec' => array($this, 'block_page_js_exec'),
            'page' => array($this, 'block_page'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "BillingBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_page_js_exec($context, array $blocks = array())
    {
        // line 3
        echo "\t<script type=\"text/javascript\">
\t\t\$(document).ready(function() {
\t\t\t\$('#show_editlic_trigger').on('click', function(ev) {
\t\t\t\tev.preventDefault();
\t\t\t\t\$('#lic_show').hide();
\t\t\t\t\$('#lic_edit').show();
\t\t\t});
\t\t\t\$('#editlic_cancel_trigger').on('click', function(ev) {
\t\t\t\tev.preventDefault();
\t\t\t\t\$('#lic_edit').hide();
\t\t\t\t\$('#lic_show').show();
\t\t\t});

\t\t\t\$('#editlic_save_trigger').on('click', function() {

\t\t\t\tvar formData = [];
\t\t\t\tformData.push({
\t\t\t\t\tname: 'license_code',
\t\t\t\t\tvalue: \$('#editlic_code').val()
\t\t\t\t});

\t\t\t\t\$('#editlic_showsave').hide();
\t\t\t\t\$('#editlic_showloading').show();
\t\t\t\t\$('#editlic_err').hide();
\t\t\t\t\$.ajax({
\t\t\t\t\turl: '";
        // line 28
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("billing_license_input_save"), "html", null, true);
        echo "',
\t\t\t\t\ttype: 'POST',
\t\t\t\t\tdata: formData,
\t\t\t\t\tdataTyoe: 'json',
\t\t\t\t\terror: function() {
\t\t\t\t\t\t\$('#editlic_showloading').hide();
\t\t\t\t\t\t\$('#editlic_showsave').show();
\t\t\t\t\t},
\t\t\t\t\tsuccess: function(data) {
\t\t\t\t\t\tif (data.success) {
\t\t\t\t\t\t\t\$.ajax({
\t\t\t\t\t\t\t\turl: '";
        // line 39
        if (isset($context["lic_set_callback"])) { $_lic_set_callback_ = $context["lic_set_callback"]; } else { $_lic_set_callback_ = null; }
        echo twig_escape_filter($this->env, $_lic_set_callback_, "html", null, true);
        echo "',
\t\t\t\t\t\t\t\tdata: {
\t\t\t\t\t\t\t\t\tlicense_id: data.license_id,
\t\t\t\t\t\t\t\t\tinstall_key: data.install_key,
\t\t\t\t\t\t\t\t\tinstall_token: data.install_token
\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\tdataType: 'jsonp',
\t\t\t\t\t\t\t\ttimeout: 10000,
\t\t\t\t\t\t\t\tcomplete: function() {
\t\t\t\t\t\t\t\t\twindow.location.reload(false);
\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t});
\t\t\t\t\t\t} else {
\t\t\t\t\t\t\t\$('#editlic_err').show().find('.lic-err-code').text(data.error_code);
\t\t\t\t\t\t\t\$('#editlic_showloading').hide();
\t\t\t\t\t\t\t\$('#editlic_showsave').show();
\t\t\t\t\t\t}
\t\t\t\t\t}
\t\t\t\t});
\t\t\t});
\t\t});
\t</script>
";
    }

    // line 62
    public function block_page($context, array $blocks = array())
    {
        // line 63
        echo "
\t<div class=\"alert-message block-message\">
\t\tThe ";
        // line 65
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "isCloud", array(), "method")) {
            echo "billing";
        } else {
            echo "licensing";
        }
        echo " interface is used to manage the licensing of your DeskPRO installation.
\t\tIf you are an existing customer you can manage your account in the DeskPRO Members Area: <a href=\"http://www.deskpro.com/members/\">http://www.deskpro.com/members/</a>
\t</div>

\t<div class=\"content-table\">
\t\t<table width=\"100%\">
\t\t\t<thead>
\t\t\t<tr>
\t\t\t\t<th class=\"single-title\" colspan=\"2\">
\t\t\t\t\tYour License
\t\t\t\t</th>
\t\t\t</tr>
\t\t\t</thead>
\t\t\t<tbody>
\t\t\t<tr>
\t\t\t\t<td class=\"title\" width=\"100\">License ID</td>
\t\t\t\t<td class=\"prop\">";
        // line 81
        if (isset($context["lic"])) { $_lic_ = $context["lic"]; } else { $_lic_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_lic_, "getLicenseId", array(), "method"), "html", null, true);
        echo "</td>
\t\t\t</tr>
\t\t\t";
        // line 83
        if (isset($context["lic"])) { $_lic_ = $context["lic"]; } else { $_lic_ = null; }
        if ($this->getAttribute($_lic_, "get", array(0 => "org"), "method")) {
            // line 84
            echo "\t\t\t\t<tr>
\t\t\t\t\t<td class=\"title\">Licensor</td>
\t\t\t\t\t<td class=\"prop\">";
            // line 86
            if (isset($context["lic"])) { $_lic_ = $context["lic"]; } else { $_lic_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_lic_, "get", array(0 => "org"), "method"), "html", null, true);
            echo "</td>
\t\t\t\t</tr>
\t\t\t";
        }
        // line 89
        echo "\t\t\t";
        if (isset($context["lic"])) { $_lic_ = $context["lic"]; } else { $_lic_ = null; }
        if ($this->getAttribute($_lic_, "isDemo", array(), "method")) {
            // line 90
            echo "\t\t\t\t<tr>
\t\t\t\t\t<td class=\"title\">Demo License</td>
\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t";
            // line 93
            if (isset($context["is_expired"])) { $_is_expired_ = $context["is_expired"]; } else { $_is_expired_ = null; }
            if ((!$_is_expired_)) {
                // line 94
                echo "\t\t\t\t\t\t\tYour demo period expires ";
                if (isset($context["expire_in_days"])) { $_expire_in_days_ = $context["expire_in_days"]; } else { $_expire_in_days_ = null; }
                if ($_expire_in_days_) {
                    echo "in ";
                    if (isset($context["expire_in_days"])) { $_expire_in_days_ = $context["expire_in_days"]; } else { $_expire_in_days_ = null; }
                    echo twig_escape_filter($this->env, $_expire_in_days_, "html", null, true);
                    echo " days";
                }
                echo " on ";
                if (isset($context["lic"])) { $_lic_ = $context["lic"]; } else { $_lic_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_lic_, "getExpireDate", array(), "method"), "day", "UTC"), "html", null, true);
                echo "
\t\t\t\t\t\t";
            } else {
                // line 96
                echo "\t\t\t\t\t\t\tYour demo period expired on ";
                if (isset($context["lic"])) { $_lic_ = $context["lic"]; } else { $_lic_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_lic_, "getExpireDate", array(), "method"), "day", "UTC"), "html", null, true);
                echo "
\t\t\t\t\t\t";
            }
            // line 98
            echo "\t\t\t\t\t\t&nbsp;&nbsp;
\t\t\t\t\t\t<a target=\"_blank\" class=\"clean-white\" href=\"http://www.deskpro.com/pricing/?download\">Purchase your License Now</a>
\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t\t<tr>
\t\t\t\t\t<td class=\"title\">Agents</td>
\t\t\t\t\t<td class=\"prop\">You can use unlimited agents during the demo period</td>
\t\t\t\t</tr>
\t\t\t";
        } else {
            // line 107
            echo "\t\t\t\t<tr>
\t\t\t\t\t<td class=\"title\">Expires</td>
\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t";
            // line 110
            if (isset($context["is_expired"])) { $_is_expired_ = $context["is_expired"]; } else { $_is_expired_ = null; }
            if ((!$_is_expired_)) {
                // line 111
                echo "\t\t\t\t\t\t\tYour license expires ";
                if (isset($context["expire_in_days"])) { $_expire_in_days_ = $context["expire_in_days"]; } else { $_expire_in_days_ = null; }
                if (($_expire_in_days_ && ($_expire_in_days_ < 30))) {
                    echo "in ";
                    if (isset($context["expire_in_days"])) { $_expire_in_days_ = $context["expire_in_days"]; } else { $_expire_in_days_ = null; }
                    echo twig_escape_filter($this->env, $_expire_in_days_, "html", null, true);
                    echo " days";
                }
                echo " on ";
                if (isset($context["lic"])) { $_lic_ = $context["lic"]; } else { $_lic_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_lic_, "getExpireDate", array(), "method"), "day", "UTC"), "html", null, true);
                echo "
\t\t\t\t\t\t";
            } else {
                // line 113
                echo "\t\t\t\t\t\t\tYour license has expired
\t\t\t\t\t\t";
            }
            // line 115
            echo "\t\t\t\t\t\t&nbsp;&nbsp;
\t\t\t\t\t\t<a target=\"_blank\" class=\"clean-white go-ma-trigger\" href=\"http://www.deskpro.com/members/\">";
            // line 116
            if (isset($context["is_expired"])) { $_is_expired_ = $context["is_expired"]; } else { $_is_expired_ = null; }
            if ((!$_is_expired_)) {
                echo "Renew";
            } else {
                echo "Extend";
            }
            echo "</a>
\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t\t<tr>
\t\t\t\t\t<td class=\"title\">Agents</td>
\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t";
            // line 122
            if (isset($context["lic"])) { $_lic_ = $context["lic"]; } else { $_lic_ = null; }
            if (((!$this->getAttribute($_lic_, "getMaxAgents", array(), "method")) || ($this->getAttribute($_lic_, "getMaxAgents", array(), "method") > 100))) {
                // line 123
                echo "\t\t\t\t\t\t\t";
                if (isset($context["lic"])) { $_lic_ = $context["lic"]; } else { $_lic_ = null; }
                echo twig_escape_filter($this->env, (($this->getAttribute($_lic_, "getMaxAgents", array(), "method", true, true)) ? (_twig_default_filter($this->getAttribute($_lic_, "getMaxAgents", array(), "method"), "Unlimited")) : ("Unlimited")), "html", null, true);
                echo "
\t\t\t\t\t\t";
            } else {
                // line 125
                echo "\t\t\t\t\t\t\t";
                if (isset($context["lic"])) { $_lic_ = $context["lic"]; } else { $_lic_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_lic_, "getMaxAgents", array(), "method"), "html", null, true);
                echo "
\t\t\t\t\t\t\t&nbsp;&nbsp;
\t\t\t\t\t\t\t<a target=\"_blank\" class=\"clean-white go-ma-trigger\" href=\"http://www.deskpro.com/members/\">Add more agents</a>
\t\t\t\t\t\t";
            }
            // line 129
            echo "\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t";
        }
        // line 132
        echo "
\t\t\t\t<tr>
\t\t\t\t\t<td class=\"title\" width=\"100\">License Code</td>
\t\t\t\t\t<td class=\"prop\">
\t\t\t\t\t\t<div id=\"lic_show\">
\t\t\t\t\t\t\t<textarea readonly=\"readonly\">";
        // line 137
        if (isset($context["lic"])) { $_lic_ = $context["lic"]; } else { $_lic_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_lic_, "getLicenseCode", array(), "method"), "html", null, true);
        echo "</textarea>
\t\t\t\t\t\t\t<br />
\t\t\t\t\t\t\t<button class=\"clean-white\" id=\"show_editlic_trigger\">Update</button>
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div id=\"lic_edit\" style=\"display: none;\">
\t\t\t\t\t\t\t<div class=\"alert-message block-message error errors-box\" id=\"editlic_err\" style=\"display: none\">
\t\t\t\t\t\t\t\t<strong>";
        // line 143
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.license.error_encountered");
        echo ":</strong>
\t\t\t\t\t\t\t\t<ul>
\t\t\t\t\t\t\t\t\t<li>";
        // line 145
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.license.notice_invalid_license", array("invalid" => "<span class=\"lic-err-code\"></span>"), true);
        echo "</li>
\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t<textarea id=\"editlic_code\">";
        // line 148
        if (isset($context["lic"])) { $_lic_ = $context["lic"]; } else { $_lic_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_lic_, "getLicenseCode", array(), "method"), "html", null, true);
        echo "</textarea>
\t\t\t\t\t\t\t<br />
\t\t\t\t\t\t\t<div id=\"editlic_showsave\">
\t\t\t\t\t\t\t\t<button class=\"clean-white\" id=\"editlic_save_trigger\">Save</button>
\t\t\t\t\t\t\t\tor <a href=\"#\" id=\"editlic_cancel_trigger\">cancel</a>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t<div id=\"editlic_showloading\" style=\"display: none;\">
\t\t\t\t\t\t\t\t<i class=\"flat-spinner\"></i>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div style=\"margin: 40px 0 0 0; background-color: #EDEDED; padding: 10px;\">
\t\t\t\t\t\t\t\tIf your server is not connected to the internet or there is a problem setting your license automatically,
\t\t\t\t\t\t\t\tthen our agents may ask you to email a keyfile to <a href=\"mailto:support@deskpro.com\">";
        // line 160
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.supportdeskprocom");
        echo "</a>
\t\t\t\t\t\t\t\tso your license code can be manually generated.
\t\t\t\t\t\t\t\t<br /><br />

\t\t\t\t\t\t\t\t<a class=\"btn\" href=\"";
        // line 164
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPath("billing_license_keyfile"), "html", null, true);
        echo "\">Download: deskpro-license-sign.key (500 bytes)</a>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t</tbody>
\t\t</table>
\t</div>


\t<div style=\"display: none;\">
\t\t<form action=\"";
        // line 174
        if (isset($context["ma_login_url"])) { $_ma_login_url_ = $context["ma_login_url"]; } else { $_ma_login_url_ = null; }
        echo twig_escape_filter($this->env, $_ma_login_url_, "html", null, true);
        echo "\" method=\"POST\" id=\"go_ma_form\">
\t\t\t<input type=\"hidden\" name=\"callback_url\" value=\"";
        // line 175
        if (isset($context["ma_token"])) { $_ma_token_ = $context["ma_token"]; } else { $_ma_token_ = null; }
        if (isset($context["lic"])) { $_lic_ = $context["lic"]; } else { $_lic_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->urlFull("billing_login_ma_login", array("code" => $this->getAttribute($_ma_token_, "getCode", array(), "method"), "license_id" => $this->getAttribute($_lic_, "getLicenseId", array(), "method"))), "html", null, true);
        echo "\" />
\t\t\t<input type=\"hidden\" name=\"license_code\" value=\"";
        // line 176
        if (isset($context["lic"])) { $_lic_ = $context["lic"]; } else { $_lic_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_lic_, "getLicenseCode", array(), "method"), "html", null, true);
        echo "\" />
\t\t\t<input type=\"hidden\" name=\"license_id\" value=\"";
        // line 177
        if (isset($context["lic"])) { $_lic_ = $context["lic"]; } else { $_lic_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_lic_, "getLicenseId", array(), "method"), "html", null, true);
        echo "\" />
\t\t</form>
\t</div>
\t<script type=\"text/javascript\">
\t\$('.go-ma-trigger').on('click', function(ev) {
\t\tev.preventDefault();
\t\t\$('#go_ma_form').submit();
\t});
\t</script>
";
    }

    public function getTemplateName()
    {
        return "BillingBundle:Main:index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  345 => 177,  340 => 176,  334 => 175,  329 => 174,  316 => 164,  309 => 160,  282 => 143,  265 => 132,  260 => 129,  251 => 125,  244 => 123,  241 => 122,  224 => 115,  220 => 113,  205 => 111,  202 => 110,  179 => 96,  164 => 94,  156 => 90,  152 => 89,  141 => 84,  132 => 81,  73 => 39,  469 => 156,  459 => 148,  451 => 142,  440 => 141,  429 => 140,  413 => 139,  402 => 138,  392 => 137,  386 => 134,  376 => 127,  363 => 119,  355 => 114,  344 => 106,  339 => 103,  328 => 96,  324 => 95,  319 => 94,  311 => 90,  298 => 81,  293 => 148,  291 => 77,  287 => 145,  284 => 74,  280 => 72,  277 => 71,  272 => 137,  267 => 58,  262 => 52,  246 => 191,  232 => 187,  227 => 116,  222 => 185,  219 => 184,  193 => 164,  191 => 71,  186 => 98,  173 => 66,  161 => 93,  158 => 63,  153 => 61,  150 => 60,  147 => 59,  145 => 86,  136 => 52,  116 => 35,  111 => 34,  106 => 33,  102 => 32,  96 => 29,  92 => 28,  88 => 27,  84 => 26,  80 => 25,  76 => 24,  71 => 22,  67 => 21,  63 => 20,  59 => 28,  55 => 17,  51 => 15,  47 => 13,  45 => 12,  39 => 9,  30 => 2,  23 => 1,  254 => 132,  248 => 129,  237 => 123,  233 => 122,  214 => 183,  197 => 107,  192 => 92,  183 => 87,  180 => 86,  175 => 85,  171 => 84,  165 => 80,  157 => 79,  149 => 78,  142 => 77,  138 => 83,  134 => 75,  131 => 74,  123 => 73,  117 => 71,  114 => 70,  108 => 65,  104 => 63,  101 => 62,  89 => 55,  36 => 5,  32 => 3,  29 => 2,);
    }
}
