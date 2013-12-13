<?php

/* AdminBundle:Upgrade:start.html.twig */
class __TwigTemplate_11df030027956452b459beb136d79d8c extends \Application\DeskPRO\Twig\Template
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
        echo "<ul xmlns=\"http://www.w3.org/1999/html\" xmlns=\"http://www.w3.org/1999/html\">
\t\t<li>Upgrading DeskPRO</li>
\t</ul>
";
    }

    // line 7
    public function block_page($context, array $blocks = array())
    {
        // line 8
        echo "
<div class=\"alert-message block-message info\" style=\"border: 2px solid #CADFE5; margin-bottom: 15px;\">
\t<h3>Your Version</h3>
\t<p>
\t\tYou are using DeskPRO v4 build #";
        // line 12
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getConstant("DP_BUILD_NUM"), "html", null, true);
        echo " (built on ";
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->env->getExtension('deskpro_templating')->getConstant("DP_BUILD_TIME"), "Y-m-d"), "html", null, true);
        echo ").
\t</p>
\t<br />

\t";
        // line 16
        if (isset($context["version_info"])) { $_version_info_ = $context["version_info"]; } else { $_version_info_ = null; }
        if ($this->getAttribute($_version_info_, "build")) {
            // line 17
            echo "\t\t<h3>Latest Version</h3>
\t\t<p>
\t\t\tThe latest build is
\t\t\t\t<span class=\"";
            // line 20
            if (isset($context["version_info"])) { $_version_info_ = $context["version_info"]; } else { $_version_info_ = null; }
            if (($this->getAttribute($_version_info_, "build") == $this->env->getExtension('deskpro_templating')->getConstant("DP_BUILD_TIME"))) {
            } else {
                echo "label important";
            }
            echo "\">#";
            if (isset($context["version_info"])) { $_version_info_ = $context["version_info"]; } else { $_version_info_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_version_info_, "build_num"), "html", null, true);
            echo "</span>
\t\t\t\t(built on ";
            // line 21
            if (isset($context["version_info"])) { $_version_info_ = $context["version_info"]; } else { $_version_info_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_version_info_, "build"), "Y-m-d"), "html", null, true);
            echo ").
\t\t</p>
\t\t";
            // line 23
            if (isset($context["version_info"])) { $_version_info_ = $context["version_info"]; } else { $_version_info_ = null; }
            if ($this->getAttribute($_version_info_, "count_behind")) {
                // line 24
                echo "\t\t\t<p>You are <span class=\"label\" style=\"text-shadow: none; color: #000;\">";
                if (isset($context["version_info"])) { $_version_info_ = $context["version_info"]; } else { $_version_info_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_version_info_, "count_behind"), "html", null, true);
                echo " version";
                if (isset($context["version_info"])) { $_version_info_ = $context["version_info"]; } else { $_version_info_ = null; }
                if (($this->getAttribute($_version_info_, "count_behind") != 1)) {
                    echo "s";
                }
                echo " behind</span>. You can upgrade using one of the options outlined below.</p>
\t\t";
            } else {
                // line 26
                echo "\t\t\t<p>Your helpdesk is up to date.</p>
\t\t";
            }
            // line 28
            echo "\t";
        } else {
            // line 29
            echo "\t\t<p>
\t\t\tWe were unable to make a network connection to our server to retrieve version information. This may indicate your server is behind a firewall.
\t\t</p>
\t";
        }
        // line 33
        echo "</div>

";
        // line 35
        if (isset($context["version_info"])) { $_version_info_ = $context["version_info"]; } else { $_version_info_ = null; }
        if ($this->getAttribute($_version_info_, "build")) {
            // line 36
            echo "\t";
            $context["network_okay"] = true;
        } else {
            // line 38
            echo "\t";
            $context["network_okay"] = false;
        }
        // line 40
        echo "
";
        // line 44
        echo "
<div class=\"page-content\" style=\"padding-top: 0;\">
\t<div class=\"content-table template-dir\">
\t\t<table width=\"100%\">
\t\t\t<thead>
\t\t\t\t<tr>
\t\t\t\t\t<th colspan=\"2\" class=\"single-title\">
\t\t\t\t\t\t<h1 class=\"toggle-section expanded\">Schedule Automatic Upgrade</h1>
\t\t\t\t\t</th>
\t\t\t\t</tr>
\t\t\t</thead>
\t\t\t<tbody>
\t\t\t\t<tr>
\t\t\t\t\t<td>
\t\t\t\t\t\t<div style=\"position:relative\">

\t\t\t\t\t\t\t";
        // line 60
        if (isset($context["network_okay"])) { $_network_okay_ = $context["network_okay"]; } else { $_network_okay_ = null; }
        if ((!$_network_okay_)) {
            // line 61
            echo "\t\t\t\t\t\t\t\t<div class=\"warning-block-overlay\">
\t\t\t\t\t\t\t\t\t<article>
\t\t\t\t\t\t\t\t\t\t<p>
\t\t\t\t\t\t\t\t\t\t\tWe could not make a connection to our server. The automatic ugprader requires an active connection
\t\t\t\t\t\t\t\t\t\t\tto our server so we can download updates.
\t\t\t\t\t\t\t\t\t\t</p>

\t\t\t\t\t\t\t\t\t\t<p>
\t\t\t\t\t\t\t\t\t\t\tIf your server is behind a firewall, disable it now and then refresh the page.
\t\t\t\t\t\t\t\t\t\t</p>
\t\t\t\t\t\t\t\t\t</article>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t";
        }
        // line 74
        echo "
\t\t\t\t\t\t\t<form class=\"dp-form\" method=\"POST\" action=\"";
        // line 75
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_upgrade"), "html", null, true);
        echo "\">
\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"start\" value=\"1\" />
\t\t\t\t\t\t\t\t<div ";
        // line 77
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getVariable", array(0 => "cron_is_problem"), "method")) {
            echo "style=\"opacity: 0.4\"";
        }
        echo ">
\t\t\t\t\t\t\t\t\t<p style=\"margin-bottom: 15px\">
\t\t\t\t\t\t\t\t\t\tStart the upgrade:
\t\t\t\t\t\t\t\t\t\t&nbsp;&nbsp;<br />
\t\t\t\t\t\t\t\t\t\t<label><input type=\"radio\" name=\"minutes\" value=\"0\" checked=\"checked\" /> Now</label>
\t\t\t\t\t\t\t\t\t\t&nbsp;&nbsp;
\t\t\t\t\t\t\t\t\t\t<label><input type=\"radio\" name=\"minutes\" value=\"0\" /> Wait 5 minutes to allow agents to finish their work</label>
\t\t\t\t\t\t\t\t\t</p>

\t\t\t\t\t\t\t\t\t<p style=\"margin-bottom: 15px\">
\t\t\t\t\t\t\t\t\t\tBefore applying the upgrade:
\t\t\t\t\t\t\t\t\t\t&nbsp;&nbsp;<br />
\t\t\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"backup_files\" value=\"1\" checked=\"checked\" /> Backup the current DeskPRO source files</label>
\t\t\t\t\t\t\t\t\t\t&nbsp;&nbsp;
\t\t\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"backup_db\" value=\"1\" checked=\"checked\" /> Backup my database</label>
\t\t\t\t\t\t\t\t\t</p>

\t\t\t\t\t\t\t\t\t<p style=\"margin-bottom: 18px\">
\t\t\t\t\t\t\t\t\t\tWhile the helpdesk is offline, show this message to users:
\t\t\t\t\t\t\t\t\t\t<input style=\"margin-top: 3px;\" type=\"text\" name=\"user_message\" value=\"";
        // line 96
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "core.helpdesk_disabled_message"), "method"), "html", null, true);
        echo "\" />
\t\t\t\t\t\t\t\t\t</p>
\t\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t\t";
        // line 100
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getVariable", array(0 => "cron_is_problem"), "method")) {
            // line 101
            echo "\t\t\t\t\t\t\t\t\t<p>
\t\t\t\t\t\t\t\t\t\tScheduled tasks do not appear to be running. The automatic upgrader requires scheduled tasks.
\t\t\t\t\t\t\t\t\t</p>
\t\t\t\t\t\t\t\t\t<p>
\t\t\t\t\t\t\t\t\t\t<a class=\"btn primary\" href=\"";
            // line 105
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_settings_cron"), "html", null, true);
            echo "\">Click here for information on scheduled tasks</a>
\t\t\t\t\t\t\t\t\t</p>
\t\t\t\t\t\t\t\t";
        } else {
            // line 108
            echo "\t\t\t\t\t\t\t\t\t<p><button class=\"btn primary\">Schedule Upgrade &rarr;</button></p>
\t\t\t\t\t\t\t\t";
        }
        // line 110
        echo "\t\t\t\t\t\t\t</form>
\t\t\t\t\t\t</div>
\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t</tbody>
\t\t</table>
\t</div>
</div>

";
        // line 122
        echo "
<div class=\"page-content agent-listing\" style=\"padding-top: 0;\">
\t<div class=\"content-table template-dir\">
\t\t<table width=\"100%\">
\t\t\t<thead>
\t\t\t\t<tr>
\t\t\t\t\t<th colspan=\"2\" class=\"single-title\">
\t\t\t\t\t\t<h1 class=\"toggle-section\">Using the interactive upgrader from the command-line</h1>
\t\t\t\t\t</th>
\t\t\t\t</tr>
\t\t\t</thead>
\t\t\t<tbody style=\"display: none\">
\t\t\t\t<tr>
\t\t\t\t\t<td>
\t\t\t\t\t\t<p>
\t\t\t\t\t\t\tDeskPRO includes an automatic interactive upgrader that makes keeping your helpdesk up to date easy.
\t\t\t\t\t\t\tAll you need to do run the \"upgrade.php\" file from the command-line.
\t\t\t\t\t\t</p>

\t\t\t\t\t\t<div style=\"text-align: center;\">
\t\t\t\t\t\t\t<img src=\"";
        // line 142
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("images/instructions/interactive-upgrader.png"), "html", null, true);
        echo "\" />
\t\t\t\t\t\t</div>

\t\t\t\t\t\t<p>
\t\t\t\t\t\t\tRun this command to start the upgrader and then follow the simple on-screen instructions:
\t\t\t\t\t\t</p>
\t\t\t\t\t\t<code class=\"code-box nowrap\">";
        // line 148
        if (isset($context["php_path"])) { $_php_path_ = $context["php_path"]; } else { $_php_path_ = null; }
        echo twig_escape_filter($this->env, ((array_key_exists("php_path", $context)) ? (_twig_default_filter($_php_path_, "/path/to/php")) : ("/path/to/php")), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getConstant("DP_WEB_ROOT"), "html", null, true);
        echo "/upgrade.php</code>
\t\t\t\t\t\t<div class=\"small-note-box\">
\t\t\t\t\t\t\t";
        // line 150
        if (isset($context["php_path"])) { $_php_path_ = $context["php_path"]; } else { $_php_path_ = null; }
        if ((!$_php_path_)) {
            // line 151
            echo "\t\t\t\t\t\t\t\t<br />
\t\t\t\t\t\t\t\t<strong>";
            // line 152
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.note_attn");
            echo ":</strong>
\t\t\t\t\t\t\t\t";
            // line 153
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.settings.could_not_find_php_use_real");
            echo "
\t\t\t\t\t\t\t";
        }
        // line 155
        echo "\t\t\t\t\t\t</div>
\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t</tbody>
\t\t</table>
\t</div>
</div>

";
        // line 166
        echo "
<div class=\"page-content agent-listing\" style=\"padding-top: 0;\">
\t<div class=\"content-table template-dir\">
\t\t<table width=\"100%\">
\t\t\t<thead>
\t\t\t\t<tr>
\t\t\t\t\t<th colspan=\"2\" class=\"single-title\">
\t\t\t\t\t\t<h1 class=\"toggle-section ";
        // line 173
        if (isset($context["network_okay"])) { $_network_okay_ = $context["network_okay"]; } else { $_network_okay_ = null; }
        if ((!$_network_okay_)) {
            echo "expanded";
        }
        echo "\">Manually downloading updates</h1>
\t\t\t\t\t</th>
\t\t\t\t</tr>
\t\t\t</thead>
\t\t\t<tbody style=\"";
        // line 177
        if (isset($context["network_okay"])) { $_network_okay_ = $context["network_okay"]; } else { $_network_okay_ = null; }
        if ($_network_okay_) {
            echo "display: none";
        }
        echo "\">
\t\t\t\t<tr>
\t\t\t\t\t<td>
\t\t\t\t\t\t<div class=\"box-section\">
\t\t\t\t\t\t\t<h2>Step 1: Download newest version</h2>
\t\t\t\t\t\t\t<p>
\t\t\t\t\t\t\t\tDownload the latest DeskPRO version from <a href=\"http://deskpro.com/downloads/DeskPRO.zip\">deskpro.com/downloads/DeskPRO.zip</a>
\t\t\t\t\t\t\t</p>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t<div class=\"box-section\">
\t\t\t\t\t\t\t<h2>Step 2: Make a backup</h2>
\t\t\t\t\t\t\t<p>
\t\t\t\t\t\t\t\tYou should make a backup of your current files and database. You can use the upgrade utility that comes with DeskPRO to help you with these tasks.
\t\t\t\t\t\t\t</p>
\t\t\t\t\t\t\t<p>
\t\t\t\t\t\t\t\tTo make a backup of your files and database, run these commands:
\t\t\t\t\t\t\t</p>
\t\t\t\t\t\t\t<code class=\"code-box nowrap\">
\t\t\t\t\t\t\t\t";
        // line 196
        if (isset($context["php_path"])) { $_php_path_ = $context["php_path"]; } else { $_php_path_ = null; }
        echo twig_escape_filter($this->env, ((array_key_exists("php_path", $context)) ? (_twig_default_filter($_php_path_, "/path/to/php")) : ("/path/to/php")), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getConstant("DP_WEB_ROOT"), "html", null, true);
        echo "/upgrade.php --backup-files<br />
\t\t\t\t\t\t\t\t";
        // line 197
        if (isset($context["php_path"])) { $_php_path_ = $context["php_path"]; } else { $_php_path_ = null; }
        echo twig_escape_filter($this->env, ((array_key_exists("php_path", $context)) ? (_twig_default_filter($_php_path_, "/path/to/php")) : ("/path/to/php")), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getConstant("DP_WEB_ROOT"), "html", null, true);
        echo "/upgrade.php --backup-db
\t\t\t\t\t\t\t</code>
\t\t\t\t\t\t\t<div class=\"small-note-box\">
\t\t\t\t\t\t\t\t";
        // line 200
        if (isset($context["php_path"])) { $_php_path_ = $context["php_path"]; } else { $_php_path_ = null; }
        if ((!$_php_path_)) {
            // line 201
            echo "\t\t\t\t\t\t\t\t\t<br />
\t\t\t\t\t\t\t\t\t<strong>";
            // line 202
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.note_attn");
            echo ":</strong>
\t\t\t\t\t\t\t\t\t";
            // line 203
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.settings.could_not_find_php_use_real");
            echo "
\t\t\t\t\t\t\t\t";
        }
        // line 205
        echo "\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t<div class=\"box-section\">
\t\t\t\t\t\t\t<h2>Step 3: Install updated files</h2>
\t\t\t\t\t\t\t<p>
\t\t\t\t\t\t\t\tExtract the .zip package you downloaded and copy all of the files to your DeskPRO installation directory,
\t\t\t\t\t\t\t\toverwriting old files.
\t\t\t\t\t\t\t</p>
\t\t\t\t\t\t\t<ul class=\"standard-list\" style=\"margin-left: 15px;\">
\t\t\t\t\t\t\t\t<li>Make sure you keep your config.php file</li>
\t\t\t\t\t\t\t\t<li>Make sure you keep everything under the data/ directory</li>
\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t<div class=\"box-section\">
\t\t\t\t\t\t\t<h2>Step 4: Install database updates</h2>
\t\t\t\t\t\t\tFinally, execute the command to run the database queries required to upgrade your database:
\t\t\t\t\t\t\t<code class=\"code-box nowrap\">
\t\t\t\t\t\t\t\t";
        // line 224
        if (isset($context["php_path"])) { $_php_path_ = $context["php_path"]; } else { $_php_path_ = null; }
        echo twig_escape_filter($this->env, ((array_key_exists("php_path", $context)) ? (_twig_default_filter($_php_path_, "/path/to/php")) : ("/path/to/php")), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getConstant("DP_WEB_ROOT"), "html", null, true);
        echo "/upgrade.php --run-db-upgrade
\t\t\t\t\t\t\t</code>
\t\t\t\t\t\t\t<div class=\"small-note-box\">
\t\t\t\t\t\t\t\t";
        // line 227
        if (isset($context["php_path"])) { $_php_path_ = $context["php_path"]; } else { $_php_path_ = null; }
        if ((!$_php_path_)) {
            // line 228
            echo "\t\t\t\t\t\t\t\t\t<br />
\t\t\t\t\t\t\t\t\t<strong>";
            // line 229
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.note_attn");
            echo ":</strong>
\t\t\t\t\t\t\t\t\t";
            // line 230
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.settings.could_not_find_php_use_real");
            echo "
\t\t\t\t\t\t\t\t";
        }
        // line 232
        echo "\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t</tbody>
\t\t</table>
\t</div>
</div>


<script type=\"text/javascript\">
\t\$(document).ready(function() {
\t\t\$('h1.toggle-section').on('click', function() {
\t\t\tif (\$(this).hasClass('expanded')) {
\t\t\t\t\$(this).removeClass('expanded').closest('table').find('tbody').hide();
\t\t\t} else {
\t\t\t\t\$(this).addClass('expanded').closest('table').find('tbody').show();
\t\t\t}
\t\t});
\t});
</script>
";
    }

    public function getTemplateName()
    {
        return "AdminBundle:Upgrade:start.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  744 => 79,  873 => 74,  824 => 67,  762 => 80,  713 => 43,  578 => 292,  2739 => 1153,  2735 => 1152,  2732 => 1151,  2725 => 1146,  2712 => 1144,  2707 => 1143,  2692 => 1131,  2688 => 1130,  2683 => 1127,  2680 => 1126,  2677 => 1125,  2671 => 1121,  2658 => 1119,  2653 => 1118,  2645 => 1113,  2641 => 1112,  2636 => 1109,  2632 => 1108,  2629 => 1107,  2626 => 1106,  2606 => 1089,  2602 => 1088,  2598 => 1087,  2594 => 1086,  2590 => 1085,  2586 => 1084,  2572 => 1073,  2568 => 1072,  2564 => 1071,  2560 => 1070,  2556 => 1069,  2552 => 1068,  2537 => 1056,  2533 => 1055,  2529 => 1054,  2525 => 1053,  2521 => 1052,  2503 => 1040,  2499 => 1039,  2495 => 1038,  2491 => 1037,  2487 => 1036,  2483 => 1035,  2469 => 1024,  2465 => 1023,  2461 => 1022,  2457 => 1021,  2453 => 1020,  2449 => 1019,  2426 => 999,  2422 => 998,  2418 => 997,  2414 => 996,  2410 => 995,  2406 => 994,  2392 => 983,  2388 => 982,  2384 => 981,  2380 => 980,  2376 => 979,  2372 => 978,  2358 => 967,  2354 => 966,  2350 => 965,  2346 => 964,  2342 => 963,  2338 => 962,  2324 => 951,  2320 => 950,  2316 => 949,  2312 => 948,  2308 => 947,  2304 => 946,  2295 => 939,  2282 => 937,  2277 => 936,  2269 => 931,  2265 => 930,  2260 => 927,  2256 => 926,  2253 => 925,  2230 => 901,  2220 => 899,  2216 => 898,  2211 => 896,  2208 => 895,  2198 => 893,  2194 => 892,  2189 => 890,  2184 => 889,  2177 => 885,  2173 => 884,  2164 => 877,  2151 => 875,  2146 => 874,  2143 => 873,  2141 => 864,  2134 => 860,  2130 => 859,  2122 => 853,  2120 => 852,  2113 => 848,  2105 => 846,  2096 => 839,  2083 => 837,  2078 => 836,  2073 => 834,  2067 => 830,  2062 => 828,  2058 => 827,  2054 => 826,  2050 => 825,  2045 => 824,  2038 => 822,  2034 => 821,  2022 => 814,  2009 => 812,  1999 => 809,  1993 => 805,  1988 => 803,  1984 => 802,  1980 => 801,  1976 => 800,  1971 => 799,  1968 => 798,  1964 => 797,  1960 => 796,  1954 => 793,  1946 => 788,  1942 => 787,  1938 => 786,  1932 => 782,  1927 => 780,  1919 => 778,  1915 => 777,  1911 => 776,  1906 => 775,  1903 => 774,  1899 => 773,  1895 => 772,  1886 => 768,  1883 => 764,  1871 => 759,  1866 => 756,  1863 => 755,  1842 => 752,  1825 => 749,  1818 => 748,  1815 => 747,  1800 => 743,  1793 => 741,  1772 => 729,  1761 => 721,  1757 => 720,  1751 => 717,  1740 => 709,  1719 => 697,  1715 => 696,  1686 => 673,  1676 => 669,  1665 => 661,  1661 => 660,  1645 => 651,  1638 => 649,  1630 => 645,  1625 => 642,  1622 => 641,  1598 => 637,  1577 => 634,  1558 => 629,  1549 => 627,  1546 => 626,  1535 => 618,  1531 => 617,  1514 => 606,  1510 => 605,  1504 => 602,  1493 => 594,  1483 => 590,  1464 => 580,  1458 => 577,  1443 => 568,  1437 => 565,  1426 => 557,  1405 => 545,  1401 => 544,  1395 => 541,  1390 => 538,  1381 => 533,  1373 => 531,  1348 => 521,  1343 => 520,  1335 => 515,  1321 => 510,  1318 => 509,  1299 => 503,  1294 => 502,  1282 => 496,  1269 => 491,  1260 => 483,  1240 => 478,  1221 => 476,  1216 => 475,  1210 => 474,  1206 => 473,  1193 => 467,  1189 => 466,  1155 => 450,  1150 => 447,  1022 => 411,  1006 => 404,  988 => 398,  969 => 392,  965 => 391,  921 => 370,  878 => 355,  866 => 349,  854 => 346,  819 => 334,  796 => 330,  1713 => 683,  1700 => 681,  1685 => 674,  1662 => 670,  1643 => 669,  1640 => 668,  1631 => 663,  1618 => 661,  1613 => 660,  1608 => 639,  1605 => 656,  1602 => 655,  1589 => 645,  1586 => 644,  1583 => 643,  1554 => 613,  1547 => 608,  1521 => 602,  1508 => 600,  1499 => 598,  1491 => 593,  1482 => 587,  1472 => 582,  1468 => 581,  1465 => 579,  1457 => 573,  1445 => 566,  1422 => 556,  1416 => 553,  1410 => 548,  1397 => 546,  1392 => 545,  1386 => 541,  1377 => 532,  1370 => 530,  1357 => 528,  1352 => 527,  1341 => 518,  1314 => 510,  1311 => 509,  1275 => 493,  1248 => 484,  1238 => 478,  1225 => 476,  1220 => 475,  1209 => 466,  1185 => 459,  1182 => 463,  1159 => 450,  1154 => 449,  1130 => 438,  1125 => 437,  1101 => 426,  1074 => 432,  1056 => 407,  1046 => 418,  1043 => 404,  1030 => 397,  1027 => 412,  947 => 361,  925 => 352,  913 => 368,  893 => 338,  881 => 335,  847 => 343,  829 => 336,  825 => 304,  1083 => 434,  995 => 383,  984 => 378,  963 => 319,  941 => 375,  851 => 271,  682 => 209,  1365 => 549,  1359 => 545,  1349 => 539,  1336 => 537,  1331 => 514,  1323 => 515,  1319 => 530,  1312 => 505,  1284 => 519,  1272 => 492,  1268 => 509,  1261 => 506,  1251 => 485,  1245 => 483,  1231 => 496,  1207 => 493,  1197 => 491,  1180 => 484,  1173 => 457,  1169 => 455,  1157 => 475,  1147 => 446,  1109 => 440,  1065 => 440,  1059 => 423,  1047 => 428,  1044 => 424,  1033 => 420,  1009 => 386,  991 => 399,  987 => 404,  973 => 395,  931 => 355,  924 => 371,  911 => 298,  906 => 81,  885 => 336,  872 => 354,  855 => 72,  749 => 53,  701 => 237,  594 => 50,  1163 => 454,  1143 => 440,  1087 => 420,  1077 => 433,  1051 => 430,  1037 => 480,  1010 => 405,  999 => 384,  932 => 414,  899 => 405,  895 => 404,  933 => 84,  914 => 133,  909 => 132,  833 => 329,  783 => 235,  755 => 320,  666 => 63,  453 => 187,  639 => 58,  568 => 254,  520 => 27,  657 => 60,  572 => 251,  609 => 17,  20 => 1,  659 => 207,  562 => 230,  548 => 165,  558 => 174,  479 => 206,  589 => 200,  457 => 145,  413 => 172,  953 => 430,  948 => 403,  935 => 394,  929 => 372,  916 => 382,  864 => 365,  844 => 342,  816 => 342,  807 => 370,  801 => 338,  774 => 234,  766 => 57,  737 => 49,  685 => 293,  664 => 231,  635 => 281,  593 => 185,  546 => 236,  532 => 68,  865 => 221,  852 => 347,  838 => 208,  820 => 201,  781 => 327,  764 => 320,  725 => 46,  632 => 283,  602 => 167,  565 => 70,  529 => 62,  505 => 267,  487 => 55,  473 => 221,  1853 => 842,  1849 => 753,  1839 => 751,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 733,  1778 => 732,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 693,  1705 => 781,  1688 => 675,  1681 => 769,  1677 => 768,  1667 => 671,  1656 => 762,  1648 => 655,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 606,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 593,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 540,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 522,  1289 => 520,  1276 => 604,  1263 => 601,  1244 => 480,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 463,  1181 => 561,  1171 => 557,  1158 => 550,  1114 => 428,  1104 => 465,  1072 => 512,  1062 => 439,  1024 => 395,  1014 => 406,  1000 => 328,  990 => 382,  980 => 474,  960 => 466,  918 => 348,  888 => 80,  834 => 307,  673 => 64,  636 => 185,  462 => 192,  454 => 253,  1144 => 542,  1139 => 399,  1131 => 399,  1127 => 397,  1110 => 396,  1092 => 459,  1089 => 390,  1086 => 520,  1084 => 419,  1063 => 387,  1060 => 386,  1055 => 422,  1050 => 384,  1035 => 372,  1019 => 330,  1003 => 263,  959 => 387,  900 => 366,  880 => 276,  870 => 350,  867 => 353,  859 => 362,  848 => 215,  839 => 376,  828 => 357,  823 => 374,  809 => 181,  800 => 315,  797 => 62,  794 => 294,  786 => 174,  740 => 78,  734 => 311,  703 => 354,  693 => 286,  630 => 278,  626 => 19,  614 => 275,  610 => 169,  581 => 293,  564 => 229,  525 => 61,  722 => 45,  697 => 37,  674 => 279,  671 => 29,  577 => 257,  569 => 243,  557 => 229,  502 => 286,  497 => 132,  445 => 197,  729 => 73,  684 => 281,  676 => 65,  669 => 254,  660 => 61,  647 => 198,  643 => 270,  601 => 306,  570 => 73,  522 => 202,  501 => 58,  296 => 67,  374 => 205,  631 => 265,  616 => 208,  608 => 53,  605 => 77,  596 => 102,  574 => 74,  561 => 175,  527 => 142,  433 => 166,  388 => 151,  426 => 142,  383 => 135,  461 => 44,  370 => 147,  395 => 224,  294 => 185,  223 => 145,  220 => 108,  492 => 180,  468 => 132,  444 => 168,  410 => 229,  397 => 135,  377 => 134,  262 => 107,  250 => 173,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 1051,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 680,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 638,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 572,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 526,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 494,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 538,  1126 => 443,  1106 => 528,  1100 => 897,  1026 => 418,  997 => 822,  983 => 816,  975 => 402,  956 => 318,  939 => 786,  902 => 362,  894 => 364,  879 => 76,  757 => 55,  727 => 316,  716 => 44,  670 => 278,  528 => 180,  476 => 253,  435 => 33,  354 => 216,  341 => 130,  192 => 142,  321 => 154,  243 => 119,  793 => 350,  780 => 311,  758 => 335,  700 => 212,  686 => 243,  652 => 273,  638 => 269,  620 => 174,  545 => 223,  523 => 179,  494 => 55,  459 => 50,  438 => 48,  351 => 104,  347 => 16,  402 => 37,  268 => 103,  430 => 141,  411 => 140,  379 => 145,  322 => 115,  315 => 119,  289 => 155,  284 => 153,  255 => 127,  234 => 70,  1133 => 444,  1124 => 469,  1121 => 56,  1116 => 549,  1113 => 467,  1108 => 376,  1103 => 368,  1098 => 26,  1081 => 415,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 419,  1042 => 483,  1039 => 345,  1025 => 335,  1021 => 391,  1015 => 412,  1008 => 461,  996 => 406,  989 => 454,  985 => 315,  981 => 257,  977 => 321,  970 => 320,  966 => 375,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 148,  919 => 83,  917 => 369,  908 => 411,  905 => 363,  896 => 358,  891 => 360,  877 => 334,  862 => 348,  857 => 273,  837 => 347,  832 => 250,  827 => 68,  821 => 66,  803 => 179,  778 => 389,  769 => 58,  765 => 297,  753 => 54,  746 => 52,  743 => 268,  735 => 75,  730 => 330,  720 => 363,  717 => 362,  712 => 251,  691 => 233,  678 => 149,  654 => 199,  587 => 14,  576 => 158,  539 => 172,  517 => 140,  471 => 18,  441 => 49,  437 => 239,  418 => 43,  386 => 106,  373 => 133,  304 => 114,  270 => 98,  265 => 164,  229 => 83,  477 => 174,  455 => 36,  448 => 41,  429 => 165,  407 => 228,  399 => 36,  389 => 170,  375 => 216,  358 => 110,  349 => 131,  335 => 215,  327 => 155,  298 => 144,  280 => 152,  249 => 205,  194 => 62,  142 => 46,  344 => 206,  318 => 177,  306 => 178,  295 => 106,  357 => 127,  300 => 113,  286 => 102,  276 => 147,  269 => 174,  254 => 101,  128 => 44,  237 => 71,  165 => 54,  122 => 55,  798 => 337,  770 => 179,  759 => 278,  748 => 271,  731 => 262,  721 => 258,  718 => 301,  708 => 250,  696 => 287,  617 => 188,  590 => 226,  553 => 66,  550 => 187,  540 => 289,  533 => 255,  500 => 397,  493 => 57,  489 => 179,  482 => 223,  467 => 258,  464 => 202,  458 => 255,  452 => 154,  449 => 35,  415 => 32,  382 => 149,  372 => 215,  361 => 129,  356 => 24,  339 => 89,  302 => 150,  285 => 105,  258 => 136,  123 => 48,  108 => 111,  424 => 45,  394 => 139,  380 => 151,  338 => 112,  319 => 125,  316 => 117,  312 => 116,  290 => 183,  267 => 96,  206 => 60,  110 => 33,  240 => 83,  224 => 110,  219 => 63,  217 => 74,  202 => 71,  186 => 70,  170 => 57,  100 => 28,  67 => 16,  14 => 1,  1096 => 425,  1090 => 421,  1088 => 435,  1085 => 456,  1066 => 409,  1034 => 282,  1031 => 281,  1018 => 416,  1013 => 477,  1007 => 274,  1002 => 403,  993 => 266,  986 => 264,  982 => 394,  976 => 399,  971 => 376,  964 => 255,  949 => 388,  946 => 402,  940 => 384,  937 => 374,  928 => 452,  926 => 413,  915 => 299,  912 => 82,  903 => 231,  898 => 440,  892 => 229,  889 => 337,  887 => 281,  884 => 79,  876 => 75,  874 => 215,  871 => 331,  863 => 352,  861 => 274,  858 => 347,  850 => 378,  843 => 206,  840 => 406,  815 => 64,  812 => 297,  808 => 199,  804 => 395,  799 => 295,  791 => 60,  785 => 328,  775 => 82,  771 => 284,  754 => 340,  728 => 317,  726 => 72,  723 => 71,  715 => 105,  711 => 152,  709 => 222,  706 => 302,  698 => 208,  694 => 36,  692 => 155,  689 => 302,  681 => 242,  677 => 149,  675 => 289,  663 => 62,  661 => 200,  650 => 246,  646 => 112,  629 => 55,  627 => 264,  625 => 213,  622 => 18,  598 => 76,  592 => 75,  586 => 199,  575 => 232,  566 => 242,  556 => 67,  554 => 240,  541 => 182,  536 => 241,  515 => 175,  511 => 166,  509 => 24,  488 => 155,  486 => 220,  483 => 175,  465 => 156,  463 => 51,  450 => 244,  432 => 32,  419 => 232,  371 => 127,  362 => 201,  353 => 23,  337 => 137,  333 => 156,  309 => 110,  303 => 188,  299 => 166,  291 => 92,  272 => 181,  261 => 101,  253 => 180,  239 => 82,  235 => 122,  213 => 73,  200 => 52,  198 => 85,  159 => 58,  149 => 61,  146 => 60,  131 => 33,  116 => 46,  79 => 23,  74 => 22,  71 => 29,  836 => 262,  817 => 398,  814 => 319,  811 => 317,  805 => 313,  787 => 59,  779 => 169,  776 => 326,  773 => 347,  761 => 296,  751 => 272,  747 => 325,  742 => 336,  739 => 333,  736 => 265,  724 => 259,  705 => 69,  702 => 601,  688 => 232,  680 => 278,  667 => 232,  662 => 27,  656 => 418,  649 => 285,  644 => 284,  641 => 20,  624 => 109,  613 => 264,  607 => 273,  597 => 253,  591 => 49,  584 => 46,  579 => 159,  563 => 40,  559 => 68,  551 => 243,  547 => 186,  537 => 145,  524 => 141,  512 => 174,  507 => 165,  504 => 164,  498 => 213,  485 => 166,  480 => 50,  472 => 205,  466 => 38,  460 => 152,  447 => 153,  442 => 40,  434 => 47,  428 => 46,  422 => 176,  404 => 227,  368 => 132,  364 => 126,  340 => 170,  334 => 127,  330 => 119,  325 => 205,  292 => 94,  287 => 67,  282 => 103,  279 => 109,  273 => 146,  266 => 148,  256 => 96,  252 => 86,  228 => 80,  218 => 78,  201 => 74,  64 => 12,  51 => 10,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 847,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 823,  2037 => 789,  2031 => 788,  2028 => 818,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 811,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 779,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 769,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 744,  1801 => 695,  1798 => 694,  1795 => 742,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 708,  1730 => 705,  1720 => 688,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 672,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 659,  1651 => 656,  1641 => 631,  1637 => 667,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 651,  1592 => 614,  1587 => 613,  1584 => 635,  1581 => 731,  1576 => 608,  1574 => 633,  1567 => 603,  1563 => 630,  1559 => 601,  1552 => 628,  1548 => 597,  1542 => 593,  1529 => 605,  1525 => 614,  1516 => 708,  1512 => 583,  1503 => 599,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 569,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 537,  1380 => 539,  1376 => 523,  1371 => 645,  1367 => 527,  1364 => 518,  1361 => 523,  1355 => 543,  1342 => 624,  1337 => 510,  1328 => 516,  1324 => 511,  1317 => 511,  1313 => 499,  1310 => 498,  1304 => 504,  1291 => 502,  1286 => 497,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 487,  1262 => 490,  1257 => 489,  1254 => 482,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 498,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 472,  1196 => 464,  1192 => 490,  1188 => 456,  1184 => 485,  1179 => 462,  1176 => 461,  1172 => 452,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 561,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 442,  1119 => 468,  1115 => 424,  1111 => 377,  1107 => 422,  1102 => 439,  1099 => 438,  1095 => 419,  1091 => 418,  1082 => 455,  1079 => 524,  1076 => 359,  1070 => 431,  1057 => 491,  1052 => 406,  1045 => 484,  1040 => 414,  1036 => 283,  1032 => 496,  1028 => 337,  1023 => 393,  1020 => 392,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 402,  992 => 821,  979 => 400,  974 => 256,  967 => 391,  962 => 803,  958 => 370,  954 => 389,  950 => 153,  945 => 376,  942 => 460,  938 => 150,  934 => 356,  927 => 147,  923 => 387,  920 => 412,  910 => 353,  901 => 340,  897 => 339,  890 => 343,  886 => 50,  883 => 356,  868 => 268,  856 => 323,  853 => 319,  849 => 70,  845 => 69,  841 => 341,  835 => 337,  830 => 249,  826 => 202,  822 => 354,  818 => 65,  813 => 183,  810 => 317,  806 => 180,  802 => 198,  795 => 314,  792 => 239,  789 => 83,  784 => 286,  782 => 187,  777 => 291,  772 => 289,  768 => 81,  763 => 327,  760 => 319,  756 => 318,  752 => 317,  745 => 314,  741 => 313,  738 => 379,  732 => 171,  719 => 279,  714 => 300,  710 => 299,  704 => 267,  699 => 67,  695 => 66,  690 => 285,  687 => 210,  683 => 346,  679 => 298,  672 => 276,  668 => 201,  665 => 276,  658 => 26,  645 => 225,  640 => 224,  634 => 267,  628 => 214,  623 => 107,  619 => 78,  611 => 54,  606 => 263,  603 => 206,  599 => 262,  595 => 244,  583 => 263,  580 => 45,  573 => 157,  560 => 75,  543 => 147,  538 => 69,  534 => 233,  530 => 168,  526 => 229,  521 => 287,  518 => 233,  514 => 221,  510 => 227,  503 => 59,  496 => 58,  490 => 56,  484 => 19,  474 => 161,  470 => 231,  446 => 185,  440 => 146,  436 => 251,  431 => 37,  425 => 35,  416 => 159,  412 => 158,  408 => 157,  403 => 194,  400 => 236,  396 => 28,  392 => 139,  385 => 224,  381 => 133,  367 => 27,  363 => 26,  359 => 200,  355 => 132,  350 => 197,  346 => 20,  343 => 196,  328 => 135,  324 => 125,  313 => 105,  307 => 151,  301 => 124,  288 => 168,  283 => 111,  271 => 103,  257 => 142,  251 => 95,  238 => 89,  233 => 160,  195 => 61,  191 => 135,  187 => 60,  183 => 52,  130 => 32,  88 => 26,  76 => 21,  115 => 26,  95 => 21,  655 => 270,  651 => 24,  648 => 23,  637 => 273,  633 => 56,  621 => 462,  618 => 257,  615 => 268,  604 => 52,  600 => 254,  588 => 48,  585 => 295,  582 => 160,  571 => 43,  567 => 193,  555 => 37,  552 => 36,  549 => 224,  544 => 230,  542 => 290,  535 => 64,  531 => 143,  519 => 201,  516 => 26,  513 => 228,  508 => 230,  506 => 60,  499 => 20,  495 => 181,  491 => 54,  481 => 161,  478 => 235,  475 => 184,  469 => 53,  456 => 204,  451 => 149,  443 => 194,  439 => 167,  427 => 143,  423 => 141,  420 => 140,  409 => 137,  405 => 30,  401 => 164,  391 => 134,  387 => 132,  384 => 131,  378 => 154,  365 => 202,  360 => 128,  348 => 122,  336 => 132,  332 => 127,  329 => 109,  323 => 204,  310 => 180,  305 => 112,  277 => 151,  274 => 150,  263 => 97,  259 => 102,  247 => 138,  244 => 91,  241 => 90,  222 => 153,  210 => 122,  207 => 91,  204 => 74,  184 => 28,  181 => 60,  167 => 75,  157 => 60,  96 => 30,  421 => 44,  417 => 250,  414 => 230,  406 => 130,  398 => 165,  393 => 152,  390 => 153,  376 => 29,  369 => 203,  366 => 174,  352 => 140,  345 => 131,  342 => 160,  331 => 19,  326 => 87,  320 => 203,  317 => 100,  314 => 126,  311 => 85,  308 => 173,  297 => 171,  293 => 114,  281 => 146,  278 => 100,  275 => 98,  264 => 104,  260 => 107,  248 => 172,  245 => 73,  242 => 96,  231 => 151,  227 => 96,  215 => 97,  212 => 150,  209 => 125,  197 => 96,  177 => 118,  171 => 140,  161 => 68,  132 => 50,  121 => 38,  105 => 50,  99 => 104,  81 => 26,  77 => 28,  180 => 58,  176 => 85,  156 => 67,  143 => 51,  139 => 113,  118 => 42,  189 => 88,  185 => 61,  173 => 141,  166 => 59,  152 => 57,  174 => 63,  164 => 74,  154 => 113,  150 => 68,  137 => 30,  133 => 53,  127 => 52,  107 => 30,  102 => 36,  83 => 19,  78 => 21,  53 => 9,  23 => 2,  42 => 8,  138 => 50,  134 => 34,  109 => 44,  103 => 56,  97 => 26,  94 => 33,  84 => 27,  75 => 20,  69 => 16,  66 => 16,  54 => 11,  44 => 7,  230 => 74,  226 => 78,  203 => 71,  193 => 66,  188 => 57,  182 => 51,  178 => 59,  168 => 62,  163 => 115,  160 => 68,  155 => 37,  148 => 35,  145 => 47,  140 => 108,  136 => 35,  125 => 40,  120 => 46,  113 => 32,  101 => 28,  92 => 26,  89 => 110,  85 => 24,  73 => 23,  62 => 14,  59 => 12,  56 => 10,  41 => 5,  126 => 30,  119 => 65,  111 => 41,  106 => 38,  98 => 24,  93 => 27,  86 => 21,  70 => 17,  60 => 17,  28 => 2,  36 => 5,  114 => 35,  104 => 29,  91 => 24,  80 => 18,  63 => 15,  58 => 17,  40 => 8,  34 => 4,  45 => 7,  61 => 16,  55 => 11,  48 => 12,  39 => 7,  35 => 5,  31 => 3,  26 => 2,  21 => 2,  46 => 10,  29 => 2,  57 => 16,  50 => 9,  47 => 8,  38 => 5,  33 => 2,  49 => 11,  32 => 3,  246 => 163,  236 => 82,  232 => 81,  225 => 154,  221 => 153,  216 => 76,  214 => 105,  211 => 71,  208 => 101,  205 => 100,  199 => 69,  196 => 57,  190 => 146,  179 => 79,  175 => 57,  172 => 77,  169 => 65,  162 => 61,  158 => 38,  153 => 66,  151 => 43,  147 => 57,  144 => 41,  141 => 51,  135 => 54,  129 => 47,  124 => 42,  117 => 36,  112 => 45,  90 => 36,  87 => 28,  82 => 23,  72 => 20,  68 => 18,  65 => 20,  52 => 10,  43 => 6,  37 => 4,  30 => 4,  27 => 2,  25 => 3,  24 => 3,  22 => 34,  19 => 1,);
    }
}
