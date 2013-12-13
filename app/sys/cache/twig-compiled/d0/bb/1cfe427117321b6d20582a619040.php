<?php

/* AdminBundle:Server:phpinfo.html.twig */
class __TwigTemplate_d0bb1cfe427117321b6d20582a619040 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("AdminBundle::layout.html.twig");

        $this->blocks = array(
            'pagebar' => array($this, 'block_pagebar'),
            'page' => array($this, 'block_page'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "AdminBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_pagebar($context, array $blocks = array())
    {
        // line 3
        echo "\t<nav>
\t\t<ul>
\t\t\t<li><a href=\"";
        // line 5
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_server_phpinfo_download"), "html", null, true);
        echo "\">Download Report File</a></li>
\t\t</ul>
\t</nav>
\t<ul>
\t\t<li>";
        // line 9
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.php_info");
        echo "</li>
\t</ul>
";
    }

    // line 12
    public function block_page($context, array $blocks = array())
    {
        // line 13
        echo "
<div class=\"check-grid item-list\">
\t<table width=\"100%\">
\t\t<thead>
\t\t\t<tr>
\t\t\t\t<th width=\"33%\" style=\"text-align:left\">&nbsp; ";
        // line 18
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.general.setting");
        echo "</th>
\t\t\t\t<th width=\"33%\" style=\"text-align:left\">Web PHP Value</th>
\t\t\t\t<th width=\"33%\" style=\"text-align:left\">Command-line PHP Value</th>
\t\t\t</tr>
\t\t</thead>
\t\t<tbody>
\t\t\t<tr>
\t\t\t\t<td>";
        // line 25
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.php_version");
        echo "</td>
\t\t\t\t<td>";
        // line 26
        if (isset($context["web_php"])) { $_web_php_ = $context["web_php"]; } else { $_web_php_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_web_php_, "php_config"), "version"), "html", null, true);
        echo "</td>
\t\t\t\t<td>";
        // line 27
        if (isset($context["cli_php"])) { $_cli_php_ = $context["cli_php"]; } else { $_cli_php_ = null; }
        if ($this->getAttribute($_cli_php_, "php_config")) {
            if (isset($context["cli_php"])) { $_cli_php_ = $context["cli_php"]; } else { $_cli_php_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_cli_php_, "php_config"), "php_version"), "html", null, true);
        } else {
            echo "n/a";
        }
        echo "</td>
\t\t\t</tr>
\t\t\t<tr>
\t\t\t\t<td>";
        // line 30
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.memory_limit");
        echo "</td>
\t\t\t\t<td>
\t\t\t\t\t";
        // line 32
        if (isset($context["web_php"])) { $_web_php_ = $context["web_php"]; } else { $_web_php_ = null; }
        if (($this->getAttribute($this->getAttribute($_web_php_, "php_config"), "memory_limit_real") == (-1))) {
            echo "No limit
\t\t\t\t\t";
        } else {
            // line 33
            if (isset($context["web_php"])) { $_web_php_ = $context["web_php"]; } else { $_web_php_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->filesizeDisplay($this->getAttribute($this->getAttribute($_web_php_, "php_config"), "memory_limit_real")), "html", null, true);
            echo "
\t\t\t\t\t";
        }
        // line 35
        echo "\t\t\t\t\t(<a href=\"";
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "request"), "getBaseUrl", array(), "method"), "html", null, true);
        echo "?_sys=memtest&amp;_=";
        if (isset($context["config_hash"])) { $_config_hash_ = $context["config_hash"]; } else { $_config_hash_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->staticSecurityTokenSecret(($_config_hash_ . "memtest"), 86400), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.test_this_value");
        echo "</a>)
\t\t\t\t\t";
        // line 36
        if (isset($context["web_php"])) { $_web_php_ = $context["web_php"]; } else { $_web_php_ = null; }
        if (($this->getAttribute($this->getAttribute($_web_php_, "php_config"), "memory_limit") && ($this->getAttribute($this->getAttribute($_web_php_, "php_config"), "memory_limit") != $this->getAttribute($this->getAttribute($_web_php_, "php_config"), "memory_limit_real")))) {
            // line 37
            echo "\t\t\t\t\t\t<div style=\"font-size: 11px;\">Successfully changed at runtime: ";
            if (isset($context["web_php"])) { $_web_php_ = $context["web_php"]; } else { $_web_php_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->filesizeDisplay($this->getAttribute($this->getAttribute($_web_php_, "php_config"), "memory_limit")), "html", null, true);
            echo "</div>
\t\t\t\t\t";
        }
        // line 39
        echo "\t\t\t\t</td>
\t\t\t\t<td>
\t\t\t\t\t";
        // line 41
        if (isset($context["cli_php"])) { $_cli_php_ = $context["cli_php"]; } else { $_cli_php_ = null; }
        if ($this->getAttribute($_cli_php_, "php_config")) {
            // line 42
            echo "\t\t\t\t\t\t";
            if (isset($context["cli_php"])) { $_cli_php_ = $context["cli_php"]; } else { $_cli_php_ = null; }
            if (($this->getAttribute($this->getAttribute($_cli_php_, "php_config"), "memory_limit_real") || (!$this->getAttribute($this->getAttribute($_cli_php_, "php_config"), "memory_limit_real")))) {
                echo "No limit
\t\t\t\t\t\t";
            } else {
                // line 43
                if (isset($context["cli_php"])) { $_cli_php_ = $context["cli_php"]; } else { $_cli_php_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->filesizeDisplay($this->getAttribute($this->getAttribute($_cli_php_, "php_config"), "memory_limit_real")), "html", null, true);
                echo "
\t\t\t\t\t\t";
            }
            // line 45
            echo "\t\t\t\t\t\t";
            if (isset($context["cli_php"])) { $_cli_php_ = $context["cli_php"]; } else { $_cli_php_ = null; }
            if (($this->getAttribute($this->getAttribute($_cli_php_, "php_config"), "memory_limit") && ($this->getAttribute($this->getAttribute($_cli_php_, "php_config"), "memory_limit") != $this->getAttribute($this->getAttribute($_cli_php_, "php_config"), "memory_limit_real")))) {
                // line 46
                echo "\t\t\t\t\t\t\t<div style=\"font-size: 11px;\">Successfully changed at runtime: ";
                if (isset($context["cli_php"])) { $_cli_php_ = $context["cli_php"]; } else { $_cli_php_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->filesizeDisplay($this->getAttribute($this->getAttribute($_cli_php_, "php_config"), "memory_limit")), "html", null, true);
                echo "</div>
\t\t\t\t\t\t";
            }
            // line 48
            echo "\t\t\t\t\t";
        } else {
            // line 49
            echo "\t\t\t\t\t\tn/a
\t\t\t\t\t";
        }
        // line 51
        echo "\t\t\t\t</td>
\t\t\t</tr>
\t\t\t<tr>
\t\t\t\t<td>Effective max attachment size</td>
\t\t\t\t<td>
\t\t\t\t\tApproximately ";
        // line 56
        if (isset($context["web_php"])) { $_web_php_ = $context["web_php"]; } else { $_web_php_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->filesizeDisplay($this->getAttribute($_web_php_, "effective_max_upload")), "html", null, true);
        echo "
\t\t\t\t\t<div style=\"font-size: 11px; line-height: 110%;\">
\t\t\t\t\t\tWeb-uploaded files are limited by a number of settings. <a href=\"";
        // line 58
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_server_attach"), "html", null, true);
        echo "\">Read more about configuring this limit</a>.
\t\t\t\t\t</div>
\t\t\t\t</td>
\t\t\t\t<td>
\t\t\t\t\t<div style=\"font-size: 11px; line-height: 110%;\">
\t\t\t\t\t\tFile operations on the command-line (e.g. reading email attachments) are limited by your memory limit. If you have problems
\t\t\t\t\t\taccepting large file attachments, try raising the memory limit.
\t\t\t\t\t</div>
\t\t\t\t</td>
\t\t\t</tr>
\t\t\t<tr>
\t\t\t\t<td>";
        // line 69
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.error_log");
        echo "</td>
\t\t\t\t<td>
\t\t\t\t\t";
        // line 71
        if (isset($context["web_php"])) { $_web_php_ = $context["web_php"]; } else { $_web_php_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_web_php_, "php_config", array(), "any", false, true), "error_log_real", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_web_php_, "php_config", array(), "any", false, true), "error_log_real"), "None")) : ("None")), "html", null, true);
        echo "
\t\t\t\t\t";
        // line 72
        if (isset($context["web_php"])) { $_web_php_ = $context["web_php"]; } else { $_web_php_ = null; }
        if (($this->getAttribute($this->getAttribute($_web_php_, "php_config"), "error_log") && ($this->getAttribute($this->getAttribute($_web_php_, "php_config"), "error_log") != $this->getAttribute($this->getAttribute($_web_php_, "php_config"), "error_log_real")))) {
            // line 73
            echo "\t\t\t\t\t\t<div style=\"font-size: 11px; line-height: 110%;\">Successfully changed at runtime:<br />";
            if (isset($context["web_php"])) { $_web_php_ = $context["web_php"]; } else { $_web_php_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_web_php_, "php_config"), "error_log"), "html", null, true);
            echo "</div>
\t\t\t\t\t";
        }
        // line 75
        echo "\t\t\t\t</td>
\t\t\t\t<td>
\t\t\t\t\t";
        // line 77
        if (isset($context["cli_php"])) { $_cli_php_ = $context["cli_php"]; } else { $_cli_php_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_cli_php_, "php_config", array(), "any", false, true), "error_log_real", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_cli_php_, "php_config", array(), "any", false, true), "error_log_real"), "None")) : ("None")), "html", null, true);
        echo "
\t\t\t\t\t";
        // line 78
        if (isset($context["cli_php"])) { $_cli_php_ = $context["cli_php"]; } else { $_cli_php_ = null; }
        if (($this->getAttribute($this->getAttribute($_cli_php_, "php_config"), "error_log") && ($this->getAttribute($this->getAttribute($_cli_php_, "php_config"), "error_log") != $this->getAttribute($this->getAttribute($_cli_php_, "php_config"), "error_log_real")))) {
            // line 79
            echo "\t\t\t\t\t\t<div style=\"font-size: 11px; line-height: 110%;\">Successfully changed at runtime:<br />";
            if (isset($context["cli_php"])) { $_cli_php_ = $context["cli_php"]; } else { $_cli_php_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_cli_php_, "php_config"), "error_log"), "html", null, true);
            echo "</div>
\t\t\t\t\t";
        }
        // line 81
        echo "\t\t\t\t</td>
\t\t\t</tr>
\t\t\t<tr>
\t\t\t\t<td>Path to PHP.ini</td>
\t\t\t\t<td>";
        // line 85
        if (isset($context["web_php"])) { $_web_php_ = $context["web_php"]; } else { $_web_php_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_web_php_, "ini_path", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_web_php_, "ini_path"), "n/a")) : ("n/a")), "html", null, true);
        echo "</td>
\t\t\t\t<td>";
        // line 86
        if (isset($context["cli_php"])) { $_cli_php_ = $context["cli_php"]; } else { $_cli_php_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_cli_php_, "ini_path", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_cli_php_, "ini_path"), "n/a")) : ("n/a")), "html", null, true);
        echo "</td>
\t\t\t</tr>
\t\t\t<tr>
\t\t\t\t<td>Link to PHP Info</td>
\t\t\t\t<td>
\t\t\t\t\t<a href=\"";
        // line 91
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "core.deskpro_url"), "method"), "html", null, true);
        echo "?_sys=phpinfo&_=";
        if (isset($context["config_hash"])) { $_config_hash_ = $context["config_hash"]; } else { $_config_hash_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->staticSecurityTokenSecret(($_config_hash_ . "phpinfo"), 86400), "html", null, true);
        echo "\" onclick=\"prompt('If a support agent asks for it, provide this link to your PHP info:', \$(this).attr('href')); return false;\">Copy link Web PHP Info</a>
\t\t\t\t</td>
\t\t\t\t<td>
\t\t\t\t\t<a href=\"";
        // line 94
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "core.deskpro_url"), "method"), "html", null, true);
        echo "?_sys=phpinfo&_=";
        if (isset($context["config_hash"])) { $_config_hash_ = $context["config_hash"]; } else { $_config_hash_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->staticSecurityTokenSecret(($_config_hash_ . "phpinfo"), 86400), "html", null, true);
        echo "&cli=1\" onclick=\"prompt('If a support agent asks for it, provide this link to your PHP info:', \$(this).attr('href')); return false;\">Copy link to Command-line PHP Info</a>
\t\t\t\t</td>
\t\t\t</tr>
\t\t</tbody>
\t</table>
</div>

";
        // line 101
        if (isset($context["debug_settings"])) { $_debug_settings_ = $context["debug_settings"]; } else { $_debug_settings_ = null; }
        if ($_debug_settings_) {
            // line 102
            echo "\t<br />

\t<div class=\"check-grid item-list\">
\t\t<table width=\"100%\">
\t\t\t<thead>
\t\t\t\t<tr>
\t\t\t\t\t<th colspan=\"2\" style=\"text-align: left\">&nbsp; Config Settings</th>
\t\t\t\t</tr>
\t\t\t</thead>
\t\t\t<tbody>
\t\t\t\t";
            // line 112
            if (isset($context["debug_settings"])) { $_debug_settings_ = $context["debug_settings"]; } else { $_debug_settings_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_debug_settings_);
            foreach ($context['_seq'] as $context["k"] => $context["v"]) {
                // line 113
                echo "\t\t\t\t\t<tr>
\t\t\t\t\t\t<td>";
                // line 114
                if (isset($context["k"])) { $_k_ = $context["k"]; } else { $_k_ = null; }
                echo twig_escape_filter($this->env, $_k_, "html", null, true);
                echo "</td>
\t\t\t\t\t\t<td>";
                // line 115
                if (isset($context["v"])) { $_v_ = $context["v"]; } else { $_v_ = null; }
                echo twig_escape_filter($this->env, $_v_, "html", null, true);
                echo "</td>
\t\t\t\t\t</tr>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['k'], $context['v'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 118
            echo "\t\t\t</tbody>
\t\t</table>
\t</div>
";
        }
        // line 122
        echo "
";
        // line 123
        if (isset($context["has_apc"])) { $_has_apc_ = $context["has_apc"]; } else { $_has_apc_ = null; }
        if ($_has_apc_) {
            // line 124
            echo "\t<br />

\t<div class=\"check-grid item-list\">
\t\t<table width=\"100%\">
\t\t\t<thead>
\t\t\t\t<tr>
\t\t\t\t\t<th style=\"text-align: left\">&nbsp; APC</th>
\t\t\t\t</tr>
\t\t\t</thead>
\t\t\t<tbody>
\t\t\t\t<tr>
\t\t\t\t\t<td>APC is installed and is enabled. Click the link below to view statistics and cache-controls for APC.</td>
\t\t\t\t</tr>
\t\t\t\t<tr>
\t\t\t\t\t<td>
\t\t\t\t\t\t";
            // line 139
            ob_start();
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "core.deskpro_url"), "method"), "html", null, true);
            echo "?_sys=apc&amp;_=";
            if (isset($context["config_hash"])) { $_config_hash_ = $context["config_hash"]; } else { $_config_hash_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->staticSecurityTokenSecret(($_config_hash_ . "apc"), 86400), "html", null, true);
            $context["link"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
            // line 140
            echo "\t\t\t\t\t\t<a href=\"";
            if (isset($context["link"])) { $_link_ = $context["link"]; } else { $_link_ = null; }
            echo twig_escape_filter($this->env, $_link_, "html", null, true);
            echo "\" target=\"_blank\">";
            if (isset($context["link"])) { $_link_ = $context["link"]; } else { $_link_ = null; }
            echo twig_escape_filter($this->env, $_link_, "html", null, true);
            echo "</a>
\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t</tbody>
\t\t</table>
\t</div>
";
        }
        // line 147
        echo "
";
        // line 148
        if (isset($context["has_wincache"])) { $_has_wincache_ = $context["has_wincache"]; } else { $_has_wincache_ = null; }
        if ($_has_wincache_) {
            // line 149
            echo "\t<br />

\t<div class=\"check-grid item-list\">
\t\t<table width=\"100%\">
\t\t\t<thead>
\t\t\t\t<tr>
\t\t\t\t\t<th style=\"text-align: left\">&nbsp; WinCache</th>
\t\t\t\t</tr>
\t\t\t</thead>
\t\t\t<tbody>
\t\t\t\t<tr>
\t\t\t\t\t<td>WinCache is installed and is enabled. Click the link below to view statistics and cache-controls for WinCache.</td>
\t\t\t\t</tr>
\t\t\t\t<tr>
\t\t\t\t\t<td>
\t\t\t\t\t\t";
            // line 164
            ob_start();
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "core.deskpro_url"), "method"), "html", null, true);
            echo "?_sys=wincache&amp;_=";
            if (isset($context["config_hash"])) { $_config_hash_ = $context["config_hash"]; } else { $_config_hash_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->staticSecurityTokenSecret(($_config_hash_ . "wincache"), 86400), "html", null, true);
            $context["link"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
            // line 165
            echo "\t\t\t\t\t\t<a href=\"";
            if (isset($context["link"])) { $_link_ = $context["link"]; } else { $_link_ = null; }
            echo twig_escape_filter($this->env, $_link_, "html", null, true);
            echo "\" target=\"_blank\">";
            if (isset($context["link"])) { $_link_ = $context["link"]; } else { $_link_ = null; }
            echo twig_escape_filter($this->env, $_link_, "html", null, true);
            echo "</a>
\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t</tbody>
\t\t</table>
\t</div>
";
        }
        // line 172
        echo "
<br />

<div class=\"check-grid item-list\">
\t<table width=\"100%\">
\t\t<thead>
\t\t\t<tr>
\t\t\t\t<th colspan=\"2\" style=\"text-align:left\">&nbsp; Binary Paths</th>
\t\t\t</tr>
\t\t</thead>
\t\t<tbody>
\t\t\t<tr>
\t\t\t\t<td>PHP CLI</td>
\t\t\t\t<td>";
        // line 185
        if (isset($context["binary_paths"])) { $_binary_paths_ = $context["binary_paths"]; } else { $_binary_paths_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_binary_paths_, "php", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_binary_paths_, "php"), "No path is set. DeskPRO will attempt to find it automatically.")) : ("No path is set. DeskPRO will attempt to find it automatically.")), "html", null, true);
        echo "</td>
\t\t\t</tr>
\t\t\t<tr>
\t\t\t\t<td>MySQL Binary</td>
\t\t\t\t<td>";
        // line 189
        if (isset($context["binary_paths"])) { $_binary_paths_ = $context["binary_paths"]; } else { $_binary_paths_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_binary_paths_, "mysql", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_binary_paths_, "mysql"), "No path is set. DeskPRO will attempt to find it automatically.")) : ("No path is set. DeskPRO will attempt to find it automatically.")), "html", null, true);
        echo "</td>
\t\t\t</tr>
\t\t\t<tr>
\t\t\t\t<td>MySQL Dump Binary</td>
\t\t\t\t<td>";
        // line 193
        if (isset($context["binary_paths"])) { $_binary_paths_ = $context["binary_paths"]; } else { $_binary_paths_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_binary_paths_, "mysqldump", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_binary_paths_, "mysqldump"), "No path is set. DeskPRO will attempt to find it automatically.")) : ("No path is set. DeskPRO will attempt to find it automatically.")), "html", null, true);
        echo "</td>
\t\t\t</tr>
\t\t\t<tr>
\t\t\t\t<td colspan=\"2\" style=\"font-size:11px;\">
\t\t\t\t\tDeskPRO requires the paths to these system binaries for some features like the automatic upgrader.
\t\t\t\t\t<br />To specify the path to these binaries, edit your config.php file.
\t\t\t\t</td>
\t\t\t</tr>
\t\t</tbody>
\t</table>
</div>

<br />
<br />

<nav class=\"check-grid-tabs\">
\t<ul data-element-handler=\"DeskPRO.ElementHandler.SimpleTabs\" data-trigger-elements=\"> li\">
\t\t<li class=\"on\" data-tab-for=\"#web_php_info\"><span>Web PHP Info</span></li>
\t\t<li data-tab-for=\"#cli_php_info\"><span>Command-line PHP Info</span></li>
\t</ul>
</nav>

<div id=\"web_php_info\">
\t<div class=\"check-grid\" style=\"padding: 25px;\">
\t\t";
        // line 217
        if (isset($context["web_php"])) { $_web_php_ = $context["web_php"]; } else { $_web_php_ = null; }
        echo $this->getAttribute($_web_php_, "phpinfo");
        echo "
\t</div>
</div>

<div id=\"cli_php_info\" style=\"display: none\">
\t<div class=\"check-grid\" style=\"padding: 25px;\">
\t\t";
        // line 223
        if (isset($context["cli_php"])) { $_cli_php_ = $context["cli_php"]; } else { $_cli_php_ = null; }
        echo $this->getAttribute($_cli_php_, "phpinfo");
        echo "
\t</div>
</div>

";
    }

    public function getTemplateName()
    {
        return "AdminBundle:Server:phpinfo.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  666 => 300,  453 => 203,  639 => 110,  568 => 254,  520 => 249,  657 => 287,  572 => 251,  609 => 459,  20 => 1,  659 => 274,  562 => 252,  548 => 238,  558 => 94,  479 => 82,  589 => 100,  457 => 211,  413 => 172,  953 => 406,  948 => 403,  935 => 394,  929 => 391,  916 => 382,  864 => 365,  844 => 348,  816 => 342,  807 => 339,  801 => 338,  774 => 337,  766 => 328,  737 => 314,  685 => 300,  664 => 294,  635 => 281,  593 => 445,  546 => 414,  532 => 240,  865 => 221,  852 => 216,  838 => 208,  820 => 201,  781 => 333,  764 => 178,  725 => 164,  632 => 283,  602 => 265,  565 => 253,  529 => 282,  505 => 267,  487 => 213,  473 => 221,  1853 => 842,  1849 => 841,  1839 => 837,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 813,  1778 => 812,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 782,  1705 => 781,  1688 => 770,  1681 => 769,  1677 => 768,  1667 => 764,  1656 => 762,  1648 => 757,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 714,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 695,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 651,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 610,  1289 => 607,  1276 => 604,  1263 => 601,  1244 => 596,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 565,  1181 => 561,  1171 => 557,  1158 => 550,  1114 => 530,  1104 => 526,  1072 => 512,  1062 => 508,  1024 => 494,  1014 => 490,  1000 => 482,  990 => 478,  980 => 474,  960 => 466,  918 => 448,  888 => 376,  834 => 346,  673 => 342,  636 => 284,  462 => 222,  454 => 192,  1144 => 542,  1139 => 399,  1131 => 399,  1127 => 397,  1110 => 396,  1092 => 391,  1089 => 390,  1086 => 520,  1084 => 388,  1063 => 387,  1060 => 386,  1055 => 385,  1050 => 384,  1035 => 372,  1019 => 267,  1003 => 263,  959 => 251,  900 => 377,  880 => 434,  870 => 430,  867 => 366,  859 => 362,  848 => 215,  839 => 197,  828 => 191,  823 => 188,  809 => 181,  800 => 178,  797 => 177,  794 => 176,  786 => 174,  740 => 162,  734 => 313,  703 => 354,  693 => 350,  630 => 278,  626 => 140,  614 => 275,  610 => 103,  581 => 277,  564 => 229,  525 => 235,  722 => 162,  697 => 256,  674 => 249,  671 => 425,  577 => 257,  569 => 97,  557 => 368,  502 => 229,  497 => 240,  445 => 197,  729 => 159,  684 => 261,  676 => 299,  669 => 254,  660 => 145,  647 => 286,  643 => 244,  601 => 287,  570 => 273,  522 => 200,  501 => 265,  296 => 67,  374 => 183,  631 => 111,  616 => 281,  608 => 266,  605 => 16,  596 => 102,  574 => 165,  561 => 209,  527 => 233,  433 => 93,  388 => 137,  426 => 177,  383 => 182,  461 => 246,  370 => 148,  395 => 221,  294 => 121,  223 => 78,  220 => 79,  492 => 395,  468 => 201,  444 => 193,  410 => 229,  397 => 174,  377 => 159,  262 => 113,  250 => 98,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 2108,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 1383,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 1303,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 1173,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 1041,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 970,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 538,  1126 => 916,  1106 => 900,  1100 => 897,  1026 => 841,  997 => 822,  983 => 816,  975 => 402,  956 => 800,  939 => 786,  902 => 753,  894 => 749,  879 => 373,  757 => 631,  727 => 608,  716 => 605,  670 => 297,  528 => 221,  476 => 253,  435 => 208,  354 => 153,  341 => 212,  192 => 123,  321 => 122,  243 => 151,  793 => 350,  780 => 348,  758 => 177,  700 => 312,  686 => 150,  652 => 274,  638 => 282,  620 => 139,  545 => 259,  523 => 110,  494 => 10,  459 => 226,  438 => 195,  351 => 214,  347 => 173,  402 => 222,  268 => 77,  430 => 201,  411 => 201,  379 => 219,  322 => 133,  315 => 118,  289 => 129,  284 => 128,  255 => 115,  234 => 55,  1133 => 400,  1124 => 534,  1121 => 56,  1116 => 55,  1113 => 403,  1108 => 51,  1103 => 394,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 500,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 329,  996 => 262,  989 => 316,  985 => 315,  981 => 257,  977 => 313,  970 => 91,  966 => 416,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 244,  919 => 290,  917 => 289,  908 => 444,  905 => 378,  896 => 280,  891 => 227,  877 => 270,  862 => 267,  857 => 265,  837 => 347,  832 => 250,  827 => 248,  821 => 247,  803 => 179,  778 => 389,  769 => 165,  765 => 386,  753 => 328,  746 => 175,  743 => 318,  735 => 170,  730 => 218,  720 => 363,  717 => 362,  712 => 156,  691 => 204,  678 => 149,  654 => 144,  587 => 14,  576 => 179,  539 => 241,  517 => 217,  471 => 212,  441 => 239,  437 => 238,  418 => 201,  386 => 164,  373 => 149,  304 => 125,  270 => 169,  265 => 161,  229 => 91,  477 => 138,  455 => 224,  448 => 242,  429 => 179,  407 => 119,  399 => 193,  389 => 176,  375 => 217,  358 => 133,  349 => 131,  335 => 128,  327 => 124,  298 => 144,  280 => 102,  249 => 205,  194 => 69,  142 => 49,  344 => 139,  318 => 181,  306 => 111,  295 => 124,  357 => 154,  300 => 114,  286 => 80,  276 => 87,  269 => 133,  254 => 100,  128 => 66,  237 => 118,  165 => 55,  122 => 37,  798 => 337,  770 => 179,  759 => 112,  748 => 337,  731 => 108,  721 => 305,  718 => 307,  708 => 295,  696 => 147,  617 => 461,  590 => 226,  553 => 264,  550 => 156,  540 => 84,  533 => 255,  500 => 397,  493 => 225,  489 => 257,  482 => 223,  467 => 210,  464 => 215,  458 => 220,  452 => 217,  449 => 132,  415 => 181,  382 => 162,  372 => 215,  361 => 155,  356 => 215,  339 => 126,  302 => 125,  285 => 175,  258 => 71,  123 => 40,  108 => 35,  424 => 149,  394 => 86,  380 => 121,  338 => 155,  319 => 113,  316 => 131,  312 => 87,  290 => 105,  267 => 132,  206 => 82,  110 => 27,  240 => 93,  224 => 87,  219 => 85,  217 => 80,  202 => 126,  186 => 68,  170 => 61,  100 => 35,  67 => 13,  14 => 1,  1096 => 524,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 836,  1013 => 275,  1007 => 274,  1002 => 823,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 402,  940 => 298,  937 => 395,  928 => 452,  926 => 243,  915 => 239,  912 => 236,  903 => 231,  898 => 440,  892 => 229,  889 => 748,  887 => 227,  884 => 374,  876 => 222,  874 => 215,  871 => 368,  863 => 214,  861 => 213,  858 => 212,  850 => 352,  843 => 206,  840 => 406,  815 => 245,  812 => 244,  808 => 199,  804 => 395,  799 => 196,  791 => 193,  785 => 238,  775 => 184,  771 => 183,  754 => 340,  728 => 309,  726 => 169,  723 => 168,  715 => 105,  711 => 152,  709 => 162,  706 => 155,  698 => 208,  694 => 308,  692 => 155,  689 => 302,  681 => 300,  677 => 149,  675 => 148,  663 => 276,  661 => 277,  650 => 246,  646 => 112,  629 => 305,  627 => 108,  625 => 279,  622 => 270,  598 => 174,  592 => 117,  586 => 264,  575 => 257,  566 => 271,  556 => 244,  554 => 240,  541 => 243,  536 => 241,  515 => 209,  511 => 269,  509 => 244,  488 => 208,  486 => 220,  483 => 341,  465 => 223,  463 => 216,  450 => 194,  432 => 211,  419 => 155,  371 => 182,  362 => 80,  353 => 78,  337 => 140,  333 => 134,  309 => 190,  303 => 70,  299 => 148,  291 => 176,  272 => 93,  261 => 163,  253 => 91,  239 => 85,  235 => 75,  213 => 78,  200 => 75,  198 => 54,  159 => 53,  149 => 45,  146 => 59,  131 => 50,  116 => 30,  79 => 27,  74 => 26,  71 => 23,  836 => 320,  817 => 398,  814 => 200,  811 => 317,  805 => 313,  787 => 657,  779 => 169,  776 => 305,  773 => 347,  761 => 296,  751 => 163,  747 => 325,  742 => 336,  739 => 171,  736 => 317,  724 => 214,  705 => 278,  702 => 601,  688 => 113,  680 => 278,  667 => 296,  662 => 293,  656 => 418,  649 => 285,  644 => 284,  641 => 241,  624 => 109,  613 => 460,  607 => 273,  597 => 270,  591 => 267,  584 => 259,  579 => 234,  563 => 96,  559 => 245,  551 => 249,  547 => 95,  537 => 90,  524 => 220,  512 => 227,  507 => 76,  504 => 213,  498 => 142,  485 => 256,  480 => 254,  472 => 217,  466 => 210,  460 => 221,  447 => 215,  442 => 196,  434 => 212,  428 => 185,  422 => 176,  404 => 149,  368 => 81,  364 => 156,  340 => 170,  334 => 211,  330 => 148,  325 => 134,  292 => 112,  287 => 117,  282 => 62,  279 => 111,  273 => 170,  266 => 90,  256 => 83,  252 => 109,  228 => 72,  218 => 77,  201 => 161,  64 => 16,  51 => 12,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 1705,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 737,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 731,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 725,  1559 => 601,  1552 => 720,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 708,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 645,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 624,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 1001,  1257 => 1000,  1254 => 479,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 569,  1196 => 958,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 938,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 388,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 898,  1099 => 393,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 880,  1076 => 359,  1070 => 875,  1057 => 352,  1052 => 504,  1045 => 382,  1040 => 377,  1036 => 283,  1032 => 496,  1028 => 274,  1023 => 393,  1020 => 392,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 821,  979 => 379,  974 => 256,  967 => 373,  962 => 803,  958 => 801,  954 => 253,  950 => 368,  945 => 367,  942 => 460,  938 => 365,  934 => 364,  927 => 361,  923 => 387,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 272,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 410,  841 => 210,  835 => 312,  830 => 249,  826 => 202,  822 => 308,  818 => 246,  813 => 183,  810 => 340,  806 => 180,  802 => 198,  795 => 336,  792 => 239,  789 => 349,  784 => 188,  782 => 187,  777 => 291,  772 => 289,  768 => 334,  763 => 327,  760 => 329,  756 => 230,  752 => 176,  745 => 281,  741 => 280,  738 => 379,  732 => 171,  719 => 279,  714 => 264,  710 => 310,  704 => 267,  699 => 303,  695 => 285,  690 => 263,  687 => 279,  683 => 346,  679 => 298,  672 => 255,  668 => 247,  665 => 201,  658 => 244,  645 => 270,  640 => 285,  634 => 413,  628 => 466,  623 => 107,  619 => 298,  611 => 268,  606 => 279,  603 => 267,  599 => 242,  595 => 132,  583 => 263,  580 => 99,  573 => 274,  560 => 268,  543 => 172,  538 => 257,  534 => 405,  530 => 202,  526 => 213,  521 => 226,  518 => 233,  514 => 232,  510 => 202,  503 => 266,  496 => 226,  490 => 214,  484 => 394,  474 => 202,  470 => 231,  446 => 241,  440 => 218,  436 => 189,  431 => 186,  425 => 193,  416 => 231,  412 => 230,  408 => 141,  403 => 194,  400 => 225,  396 => 299,  392 => 139,  385 => 186,  381 => 185,  367 => 147,  363 => 164,  359 => 79,  355 => 326,  350 => 94,  346 => 140,  343 => 143,  328 => 135,  324 => 123,  313 => 128,  307 => 108,  301 => 124,  288 => 116,  283 => 62,  271 => 105,  257 => 101,  251 => 58,  238 => 84,  233 => 81,  195 => 59,  191 => 49,  187 => 62,  183 => 64,  130 => 32,  88 => 31,  76 => 26,  115 => 28,  95 => 23,  655 => 275,  651 => 232,  648 => 231,  637 => 241,  633 => 272,  621 => 462,  618 => 277,  615 => 268,  604 => 186,  600 => 271,  588 => 305,  585 => 261,  582 => 260,  571 => 242,  567 => 95,  555 => 250,  552 => 190,  549 => 208,  544 => 230,  542 => 290,  535 => 256,  531 => 254,  519 => 87,  516 => 248,  513 => 216,  508 => 230,  506 => 401,  499 => 241,  495 => 239,  491 => 145,  481 => 218,  478 => 235,  475 => 184,  469 => 197,  456 => 204,  451 => 243,  443 => 132,  439 => 129,  427 => 185,  423 => 187,  420 => 208,  409 => 179,  405 => 148,  401 => 148,  391 => 173,  387 => 129,  384 => 160,  378 => 145,  365 => 161,  360 => 171,  348 => 191,  336 => 125,  332 => 150,  329 => 73,  323 => 135,  310 => 127,  305 => 115,  277 => 101,  274 => 135,  263 => 94,  259 => 100,  247 => 96,  244 => 86,  241 => 150,  222 => 133,  210 => 60,  207 => 73,  204 => 72,  184 => 41,  181 => 40,  167 => 51,  157 => 36,  96 => 32,  421 => 147,  417 => 71,  414 => 142,  406 => 170,  398 => 165,  393 => 177,  390 => 164,  376 => 85,  369 => 157,  366 => 174,  352 => 140,  345 => 213,  342 => 127,  331 => 140,  326 => 102,  320 => 130,  317 => 134,  314 => 136,  311 => 191,  308 => 141,  297 => 113,  293 => 119,  281 => 174,  278 => 96,  275 => 107,  264 => 103,  260 => 107,  248 => 77,  245 => 57,  242 => 94,  231 => 100,  227 => 113,  215 => 88,  212 => 82,  209 => 88,  197 => 70,  177 => 58,  171 => 57,  161 => 42,  132 => 47,  121 => 27,  105 => 26,  99 => 29,  81 => 22,  77 => 27,  180 => 58,  176 => 70,  156 => 52,  143 => 43,  139 => 56,  118 => 38,  189 => 68,  185 => 46,  173 => 54,  166 => 41,  152 => 50,  174 => 56,  164 => 113,  154 => 54,  150 => 52,  137 => 37,  133 => 41,  127 => 41,  107 => 26,  102 => 33,  83 => 24,  78 => 34,  53 => 13,  23 => 6,  42 => 7,  138 => 54,  134 => 38,  109 => 33,  103 => 32,  97 => 34,  94 => 25,  84 => 22,  75 => 31,  69 => 17,  66 => 16,  54 => 13,  44 => 9,  230 => 74,  226 => 79,  203 => 92,  193 => 66,  188 => 65,  182 => 45,  178 => 53,  168 => 50,  163 => 49,  160 => 48,  155 => 47,  148 => 43,  145 => 47,  140 => 40,  136 => 42,  125 => 36,  120 => 29,  113 => 36,  101 => 39,  92 => 33,  89 => 24,  85 => 23,  73 => 18,  62 => 15,  59 => 16,  56 => 11,  41 => 5,  126 => 40,  119 => 36,  111 => 46,  106 => 37,  98 => 33,  93 => 27,  86 => 23,  70 => 25,  60 => 18,  28 => 1,  36 => 5,  114 => 43,  104 => 36,  91 => 30,  80 => 25,  63 => 12,  58 => 14,  40 => 6,  34 => 4,  45 => 8,  61 => 20,  55 => 13,  48 => 9,  39 => 7,  35 => 4,  31 => 3,  26 => 2,  21 => 2,  46 => 10,  29 => 2,  57 => 6,  50 => 12,  47 => 11,  38 => 8,  33 => 6,  49 => 8,  32 => 3,  246 => 96,  236 => 91,  232 => 92,  225 => 82,  221 => 110,  216 => 65,  214 => 75,  211 => 46,  208 => 128,  205 => 69,  199 => 71,  196 => 73,  190 => 58,  179 => 91,  175 => 62,  172 => 116,  169 => 57,  162 => 56,  158 => 55,  153 => 46,  151 => 39,  147 => 50,  144 => 42,  141 => 55,  135 => 44,  129 => 39,  124 => 74,  117 => 41,  112 => 72,  90 => 22,  87 => 21,  82 => 20,  72 => 17,  68 => 22,  65 => 20,  52 => 9,  43 => 9,  37 => 5,  30 => 8,  27 => 2,  25 => 5,  24 => 3,  22 => 2,  19 => 1,);
    }
}
