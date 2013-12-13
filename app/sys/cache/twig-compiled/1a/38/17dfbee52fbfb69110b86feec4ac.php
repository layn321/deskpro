<?php

/* AdminBundle:Server:attachments.html.twig */
class __TwigTemplate_1a3817dfbee52fbfb69110b86feec4ac extends \Application\DeskPRO\Twig\Template
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
        echo "\t<ul>
\t\t<li>";
        // line 4
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.php_file_uploads");
        echo "</li>
\t</ul>
";
    }

    // line 7
    public function block_page($context, array $blocks = array())
    {
        // line 8
        echo "
<div class=\"check-grid item-list\">
\t<table width=\"100%\">
\t\t<thead>
\t\t\t<tr>
\t\t\t\t<th colspan=\"4\">";
        // line 13
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.file_storage_mechanism");
        echo "</th>
\t\t\t</tr>
\t\t</thead>
\t\t<tbody>
\t\t\t";
        // line 17
        if (isset($context["use_fs"])) { $_use_fs_ = $context["use_fs"]; } else { $_use_fs_ = null; }
        if ($_use_fs_) {
            // line 18
            echo "\t\t\t\t<tr>
\t\t\t\t\t<td width=\"200\">";
            // line 19
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.storage_mechanism");
            echo "</td>
\t\t\t\t\t<td>
\t\t\t\t\t\t";
            // line 21
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.file_storage");
            echo "
\t\t\t\t\t\t<div style=\"font-size: 11px\">

\t\t\t\t\t\t\t";
            // line 24
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.notice_most_efficient_storage");
            echo "
                            ";
            // line 25
            ob_start();
            echo "<a class=\"switch-trigger\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.switch_to_database_storage");
            echo "</a>";
            $context["phrase_link"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
            // line 26
            echo "                            ";
            if (isset($context["moving_id"])) { $_moving_id_ = $context["moving_id"]; } else { $_moving_id_ = null; }
            if ((!$_moving_id_)) {
                if (isset($context["phrase_link"])) { $_phrase_link_ = $context["phrase_link"]; } else { $_phrase_link_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.note_recommended_link", array("link" => $_phrase_link_), true);
            }
            // line 27
            echo "\t\t\t\t\t\t</div>
\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t\t<tr>
\t\t\t\t\t<td>";
            // line 31
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.storage_path");
            echo "</td>
\t\t\t\t\t<td>";
            // line 32
            if (isset($context["filestorage_path"])) { $_filestorage_path_ = $context["filestorage_path"]; } else { $_filestorage_path_ = null; }
            echo twig_escape_filter($this->env, $_filestorage_path_, "html", null, true);
            echo "</td>
\t\t\t\t</tr>
\t\t\t";
        } else {
            // line 35
            echo "\t\t\t\t<tr>
\t\t\t\t\t<td>";
            // line 36
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.storage_mechanism");
            echo "</td>
\t\t\t\t\t<td>
\t\t\t\t\t\t";
            // line 38
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.database_storage");
            echo "
\t\t\t\t\t\t<div style=\"font-size: 11px\">
\t\t\t\t\t\t\t";
            // line 40
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.databases_not_recommended");
            echo "
                            ";
            // line 41
            ob_start();
            // line 42
            echo "                            <a class=\"switch-trigger\">";
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.switching_to_fs_storage");
            echo "</a>
                            ";
            $context["phrase_link"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
            // line 44
            echo "                            ";
            if (isset($context["moving_id"])) { $_moving_id_ = $context["moving_id"]; } else { $_moving_id_ = null; }
            if ((!$_moving_id_)) {
                if (isset($context["phrase_link"])) { $_phrase_link_ = $context["phrase_link"]; } else { $_phrase_link_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.recommend_fs_storage", array("link" => $_phrase_link_), true);
            }
            // line 45
            echo "\t\t\t\t\t\t</div>
\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t";
        }
        // line 49
        echo "\t\t\t";
        if (isset($context["moving_id"])) { $_moving_id_ = $context["moving_id"]; } else { $_moving_id_ = null; }
        if ($_moving_id_) {
            // line 50
            echo "\t\t\t\t<tr>
\t\t\t\t\t<td colspan=\"4\">
                        ";
            // line 52
            if (isset($context["use_fs"])) { $_use_fs_ = $context["use_fs"]; } else { $_use_fs_ = null; }
            if ($_use_fs_) {
                // line 53
                echo "                            ";
                $context["phrase_part"] = $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.db_to_fs");
                // line 54
                echo "                        ";
            } else {
                // line 55
                echo "                            ";
                $context["phrase_part"] = $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.fs_to_db");
                // line 56
                echo "                        ";
            }
            // line 57
            echo "                        ";
            if (isset($context["phrase_part"])) { $_phrase_part_ = $context["phrase_part"]; } else { $_phrase_part_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.currently_transferring", array("subphrase" => $_phrase_part_), true);
            echo "
                        ";
            // line 58
            if (isset($context["count_done"])) { $_count_done_ = $context["count_done"]; } else { $_count_done_ = null; }
            if (isset($context["count_todo"])) { $_count_todo_ = $context["count_todo"]; } else { $_count_todo_ = null; }
            if (isset($context["count_perc"])) { $_count_perc_ = $context["count_perc"]; } else { $_count_perc_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.files_have_been_processed", array("count_done" => $_count_done_, "count_todo" => $_count_todo_, "percent" => $_count_perc_));
            echo "
\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t";
        }
        // line 62
        echo "\t\t</tbody>
\t</table>
</div><br />

<div class=\"check-grid item-list\">
\t<table width=\"100%\">
\t\t<thead>
\t\t\t<tr>
\t\t\t\t<th colspan=\"10\">Upload Restrictions (<a href=\"";
        // line 70
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_settings"), "html", null, true);
        echo "\">edit settings</a>)</th>
\t\t\t</tr>
\t\t\t<tr>
\t\t\t\t<th width=\"180\" style=\"text-align: left;\">Setting</th>
\t\t\t\t<th style=\"text-align: left;\">Users</th>
\t\t\t\t<th style=\"text-align: left;\">Agents</th>
\t\t\t</tr>
\t\t</thead>
\t\t<tbody>
\t\t\t<tr>
\t\t\t\t<td>Max Upload Size</td>
\t\t\t\t<td>
\t\t\t\t\t";
        // line 82
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->filesizeDisplay($this->getAttribute($_app_, "getSetting", array(0 => "core.attach_user_maxsize"), "method")), "html", null, true);
        echo "
\t\t\t\t</td>
\t\t\t\t<td>
\t\t\t\t\t";
        // line 85
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->filesizeDisplay($this->getAttribute($_app_, "getSetting", array(0 => "core.attach_agent_maxsize"), "method")), "html", null, true);
        echo "
\t\t\t\t</td>
\t\t\t</tr>
\t\t\t<tr>
\t\t\t\t<td>File Types</td>
\t\t\t\t<td>
\t\t\t\t\t";
        // line 91
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.attach_user_not_exts"), "method")) {
            // line 92
            echo "\t\t\t\t\t\tAll file types except these are allowed:<br />";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "core.attach_user_must_exts"), "method"), "html", null, true);
            echo "
\t\t\t\t\t";
        } elseif ($this->getAttribute($_app_, "getSetting", array(0 => "core.attach_user_must_exts"), "method")) {
            // line 94
            echo "\t\t\t\t\t\tOnly these file types allowed:<br /";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "core.attach_user_must_exts"), "method"), "html", null, true);
            echo "
\t\t\t\t\t";
        } else {
            // line 96
            echo "\t\t\t\t\t\tAll file types are allowed
\t\t\t\t\t";
        }
        // line 98
        echo "\t\t\t\t</td>
\t\t\t\t<td>
\t\t\t\t\t";
        // line 100
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.attach_agent_not_exts"), "method")) {
            // line 101
            echo "\t\t\t\t\t\tAll file types except these are allowed:<br />";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "core.attach_agent_must_exts"), "method"), "html", null, true);
            echo "
\t\t\t\t\t";
        } elseif ($this->getAttribute($_app_, "getSetting", array(0 => "core.attach_agent_must_exts"), "method")) {
            // line 103
            echo "\t\t\t\t\t\tOnly these file types allowed:<br /";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_app_, "getSetting", array(0 => "core.attach_agent_must_exts"), "method"), "html", null, true);
            echo "
\t\t\t\t\t";
        } else {
            // line 105
            echo "\t\t\t\t\t\tAll file types are allowed
\t\t\t\t\t";
        }
        // line 107
        echo "\t\t\t\t</td>
\t\t\t</tr>
\t\t</tbody>
\t</table>
</div><br />

<div class=\"check-grid item-list\">
\t<table width=\"100%\">
\t\t<thead>
\t\t\t<tr>
\t\t\t\t<th colspan=\"10\">";
        // line 117
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.test_a_file_upload");
        echo "</th>
\t\t\t</tr>
\t\t</thead>
\t\t<tbody>
\t\t\t";
        // line 121
        if (isset($context["has_uploaded"])) { $_has_uploaded_ = $context["has_uploaded"]; } else { $_has_uploaded_ = null; }
        if ($_has_uploaded_) {
            // line 122
            echo "\t\t\t\t<tr>
\t\t\t\t\t<td>
\t\t\t\t\t\t";
            // line 124
            if (isset($context["failed"])) { $_failed_ = $context["failed"]; } else { $_failed_ = null; }
            if ($_failed_) {
                // line 125
                echo "\t\t\t\t\t\t\t<h3>";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.failed");
                echo "</h3><br />

\t\t\t\t\t\t\t";
                // line 127
                if (isset($context["failed"])) { $_failed_ = $context["failed"]; } else { $_failed_ = null; }
                if (($_failed_ == "Please select a file to upload")) {
                    // line 128
                    echo "\t\t\t\t\t\t\t\tNo uploaded file was detected. If you did not choose a file, you can simply <a href=\"";
                    echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_server_attach"), "html", null, true);
                    echo "\">try again now</a>. If you did choose a file
\t\t\t\t\t\t\t\tand are still seeing this message then it means the server did not to provide the helpdesk with your upload.
\t\t\t\t\t\t\t\t<br/><br/>
\t\t\t\t\t\t\t\tThe two most common reasons for this are:
\t\t\t\t\t\t\t\t<ul style=\"margin-top: 3px;\">
\t\t\t\t\t\t\t\t\t";
                    // line 133
                    if (isset($context["can_tmp_write"])) { $_can_tmp_write_ = $context["can_tmp_write"]; } else { $_can_tmp_write_ = null; }
                    if ((!$_can_tmp_write_)) {
                        // line 134
                        echo "\t\t\t\t\t\t\t\t\t\t<li style=\"margin-bottom: 3px;\">
\t\t\t\t\t\t\t\t\t\t\t&bull; The temporary storage directory (upload_tmp_dir in php.ini) is currently set to ";
                        // line 135
                        if (isset($context["php_vars"])) { $_php_vars_ = $context["php_vars"]; } else { $_php_vars_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_php_vars_, "upload_tmp_dir_real"), "html", null, true);
                        echo ". This directory
\t\t\t\t\t\t\t\t\t\t\t<strong>is not writable</strong> by the web server. This means uploads can not be saved and will always fail. Edit php.ini and specify a
\t\t\t\t\t\t\t\t\t\t\tpath that is writable by the server, or change permissions on the directory so the server can write to it.
\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t";
                    } else {
                        // line 140
                        echo "\t\t\t\t\t\t\t\t\t\t<li style=\"margin-bottom: 3px;\">
\t\t\t\t\t\t\t\t\t\t\t&bull; The temporary storage directory defined in php.ini as displayed below is not set or is set to a directory
\t\t\t\t\t\t\t\t\t\t\tthat the web server cannot write to. Correcting the directory or making it writable may solve the problem.
\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t";
                    }
                    // line 145
                    echo "\t\t\t\t\t\t\t\t\t<li>
\t\t\t\t\t\t\t\t\t\t&bull; You have chosen a file that is larger than your server configuration allows. File limits are set using the
\t\t\t\t\t\t\t\t\t\toptions in php.ini as displayed below. In addition to these limits, your web server (such as IIS or Apache)
\t\t\t\t\t\t\t\t\t\tmay also enforce its own limits. Limits enforced by the web server cannot be detected by the helpdesk and will need to be
\t\t\t\t\t\t\t\t\t\tchecked manually.
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t\t";
                } else {
                    // line 153
                    echo "\t\t\t\t\t\t\t\t";
                    if (isset($context["failed"])) { $_failed_ = $context["failed"]; } else { $_failed_ = null; }
                    echo twig_escape_filter($this->env, $_failed_, "html", null, true);
                    echo "
\t\t\t\t\t\t\t";
                }
                // line 155
                echo "\t\t\t\t\t\t";
            } else {
                // line 156
                echo "\t\t\t\t\t\t\t<h3>";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.success");
                echo "</h3>
\t\t\t\t\t\t\t<a href=\"";
                // line 157
                if (isset($context["attach"])) { $_attach_ = $context["attach"]; } else { $_attach_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_attach_, "getDownloadUrl", array(), "method"), "html", null, true);
                echo "\">";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.download_attachment");
                echo "</a>
\t\t\t\t\t\t";
            }
            // line 159
            echo "\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t";
        } else {
            // line 162
            echo "\t\t\t\t<tr>
\t\t\t\t\t<td>
\t\t\t\t\t\t<form action=\"";
            // line 164
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_server_attach", array("test" => 1)), "html", null, true);
            echo "\" method=\"post\" enctype=\"multipart/form-data\">
\t\t\t\t\t\t\t<input type=\"file\" name=\"file\" /> <button>";
            // line 165
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.test_upload");
            echo "</button>
\t\t\t\t\t\t</form>
\t\t\t\t\t</td>
\t\t\t\t</tr>
\t\t\t";
        }
        // line 170
        echo "\t\t</tbody>
\t</table>
</div>
<br />

<div class=\"check-grid item-list\">
\t<table width=\"100%\">
\t\t<thead>
\t\t\t<tr>
\t\t\t\t<th>";
        // line 179
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.php_settings");
        echo "</th>
\t\t\t\t<th width=\"200\">";
        // line 180
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.value");
        echo "</th>
\t\t\t</tr>
\t\t</thead>
\t\t<tbody>
\t\t\t<tr>
\t\t\t\t<td>
\t\t\t\t\t<h3><a href=\"http://www.php.net/manual/en/ini.core.php#ini.file-uploads\">file_uploads</a></h3>
\t\t\t\t\t<p>";
        // line 187
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.required_for_upload");
        echo "</p>
\t\t\t\t</td>
\t\t\t\t<td>";
        // line 189
        if (isset($context["php_vars"])) { $_php_vars_ = $context["php_vars"]; } else { $_php_vars_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_php_vars_, "file_uploads"), "html", null, true);
        echo "
\t\t\t</tr>

\t\t\t<tr>
\t\t\t\t<td>
\t\t\t\t\t<h3><a href=\"http://www.php.net/manual/en/ini.core.php#ini.upload-tmp-dir\">upload_tmp_dir</a></h3>
\t\t\t\t\t<p>";
        // line 195
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.php_upload_location");
        echo "</p>
\t\t\t\t\t";
        // line 196
        if (isset($context["php_vars"])) { $_php_vars_ = $context["php_vars"]; } else { $_php_vars_ = null; }
        if ((!$this->getAttribute($_php_vars_, "upload_tmp_dir"))) {
            // line 197
            echo "\t\t\t\t\t\t<p>
\t\t\t\t\t\t\t<strong>Note:</strong> There is currently no value set in php.ini for upload_tmp_dir. The value shown to the right
\t\t\t\t\t\t\tis your system temp directory which is used by default. If you are having problems uploading files, you should
\t\t\t\t\t\t\tedit php.ini and set a specific path for upload_tmp_dir.
\t\t\t\t\t\t</p>
\t\t\t\t\t";
        }
        // line 203
        echo "\t\t\t\t</td>
\t\t\t\t<td>";
        // line 204
        if (isset($context["php_vars"])) { $_php_vars_ = $context["php_vars"]; } else { $_php_vars_ = null; }
        echo twig_escape_filter($this->env, (($this->getAttribute($_php_vars_, "upload_tmp_dir", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($_php_vars_, "upload_tmp_dir"), $this->getAttribute($_php_vars_, "upload_tmp_dir_real"))) : ($this->getAttribute($_php_vars_, "upload_tmp_dir_real"))), "html", null, true);
        echo "
\t\t\t</tr>

\t\t\t<tr>
\t\t\t\t<td>
\t\t\t\t\t<h3><a href=\"http://www.php.net/manual/en/ini.core.php#ini.upload-max-filesize\">upload_max_filesize</a></h3>
\t\t\t\t\t<p>";
        // line 210
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.max_single_file_size");
        echo "</p>
\t\t\t\t</td>
\t\t\t\t<td>";
        // line 212
        if (isset($context["php_vars"])) { $_php_vars_ = $context["php_vars"]; } else { $_php_vars_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_php_vars_, "upload_max_filesize"), "html", null, true);
        echo "
\t\t\t</tr>

\t\t\t<tr>
\t\t\t\t<td>
\t\t\t\t\t<h3><a href=\"http://www.php.net/manual/en/ini.core.php#ini.post-max-size\">post_max_size</a></h3>
\t\t\t\t\t<p>";
        // line 218
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.max_post_size");
        echo "</p>
\t\t\t\t</td>
\t\t\t\t<td>";
        // line 220
        if (isset($context["php_vars"])) { $_php_vars_ = $context["php_vars"]; } else { $_php_vars_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_php_vars_, "post_max_size"), "html", null, true);
        echo "</td>
\t\t\t</tr>

\t\t\t<tr>
\t\t\t\t<td>
\t\t\t\t\t<h3><a href=\"http://www.php.net/manual/en/ini.core.php#ini.memory-limit\">memory_limit</a></h3>
\t\t\t\t\t<p>";
        // line 226
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.upload_memory_limit_explain");
        echo "</p>
\t\t\t\t</td>
\t\t\t\t<td>
\t\t\t\t\t";
        // line 229
        if (isset($context["php_vars"])) { $_php_vars_ = $context["php_vars"]; } else { $_php_vars_ = null; }
        if (($this->getAttribute($_php_vars_, "memory_limit_real") == (-1))) {
            echo "No limit
\t\t\t\t\t";
        } else {
            // line 230
            if (isset($context["php_vars"])) { $_php_vars_ = $context["php_vars"]; } else { $_php_vars_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->filesizeDisplay($this->getAttribute($_php_vars_, "memory_limit_real")), "html", null, true);
            echo "
\t\t\t\t\t";
        }
        // line 232
        echo "\t\t\t\t\t";
        if (isset($context["php_vars"])) { $_php_vars_ = $context["php_vars"]; } else { $_php_vars_ = null; }
        if (($this->getAttribute($_php_vars_, "memory_limit") && ($this->getAttribute($_php_vars_, "memory_limit") != $this->getAttribute($_php_vars_, "memory_limit_real")))) {
            // line 233
            echo "\t\t\t\t\t\t<div style=\"font-size: 11px;\">Successfully changed to ";
            if (isset($context["php_vars"])) { $_php_vars_ = $context["php_vars"]; } else { $_php_vars_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->filesizeDisplay($this->getAttribute($_php_vars_, "memory_limit")), "html", null, true);
            echo " at runtime</div>
\t\t\t\t\t";
        }
        // line 235
        echo "\t\t\t\t</td>
\t\t\t</tr>

\t\t\t<tr>
\t\t\t\t<td>
\t\t\t\t\t<h3>";
        // line 240
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.upload_effect_max");
        echo "</h3>
\t\t\t\t\t<p>";
        // line 241
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.upload_effect_max_explain");
        echo "</p>
\t\t\t\t</td>
\t\t\t\t<td>Approximately ";
        // line 243
        if (isset($context["effective_max_display"])) { $_effective_max_display_ = $context["effective_max_display"]; } else { $_effective_max_display_ = null; }
        echo twig_escape_filter($this->env, $_effective_max_display_, "html", null, true);
        echo "</td>
\t\t\t</tr>

\t\t\t<tr>
\t\t\t\t<td colspan=\"2\">
\t\t\t\t\t<p>
\t\t\t\t\t\t";
        // line 249
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.edit_php_ini_to_make_changes");
        echo "
\t\t\t\t\t\t<a href=\"";
        // line 250
        echo $this->env->getExtension('deskpro_templating')->getServiceUrl("dp.kb.editing_php_ini");
        echo "\">";
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.learn_about_editing_php_ini");
        echo "</a>
\t\t\t\t\t<p>
\t\t\t\t\t";
        // line 252
        if (isset($context["php_ini"])) { $_php_ini_ = $context["php_ini"]; } else { $_php_ini_ = null; }
        if ($_php_ini_) {
            // line 253
            echo "\t\t\t\t\t\t<p>
\t\t\t\t\t\t\t";
            // line 254
            if (isset($context["php_ini"])) { $_php_ini_ = $context["php_ini"]; } else { $_php_ini_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.we_detected_php_ini_at", array("path" => $_php_ini_));
            echo "
\t\t\t\t\t\t</p>
\t\t\t\t\t";
        }
        // line 257
        echo "\t\t\t\t</td>
\t\t\t</tr>
\t\t</tbody>
\t</table>
</div><br />

";
        // line 263
        if (isset($context["moving_id"])) { $_moving_id_ = $context["moving_id"]; } else { $_moving_id_ = null; }
        if ((!$_moving_id_)) {
            // line 264
            echo "<div id=\"switch_confirm\" style=\"display: none; width: 500px; height: 300px\">
\t<div class=\"overlay-title\">
\t\t<span class=\"close-overlay\"></span>
\t\t<h4>";
            // line 267
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.change_storage_mechanism");
            echo "</h4>
\t</div>
\t<div class=\"overlay-content\">
\t\t";
            // line 270
            if (isset($context["use_fs"])) { $_use_fs_ = $context["use_fs"]; } else { $_use_fs_ = null; }
            if ($_use_fs_) {
                // line 271
                echo "\t\t\t<p style=\"margin-bottom: 10px;\">";
                if (isset($context["filestorage_path"])) { $_filestorage_path_ = $context["filestorage_path"]; } else { $_filestorage_path_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.confirm_switch_to_db", array("path" => $_filestorage_path_));
                echo "</p>
\t\t";
            } else {
                // line 273
                echo "\t\t\t<p style=\"margin-bottom: 10px;\">";
                if (isset($context["filestorage_path"])) { $_filestorage_path_ = $context["filestorage_path"]; } else { $_filestorage_path_ = null; }
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.confirm_switch_to_fs", array("path" => $_filestorage_path_));
                echo "</p>
\t\t";
            }
            // line 275
            echo "
\t\t<p style=\"margin: 10px; background-color: #F5F5F5; padding: 10px;\">
            ";
            // line 277
            if (isset($context["count_todo"])) { $_count_todo_ = $context["count_todo"]; } else { $_count_todo_ = null; }
            if (isset($context["total_size_readable"])) { $_total_size_readable_ = $context["total_size_readable"]; } else { $_total_size_readable_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.count_files_will_be_used", array("count" => $_count_todo_, "size" => $_total_size_readable_));
            echo "
\t\t\t<br /><br />
\t\t\t";
            // line 279
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.notice_transfer_is_gradually_done");
            echo "
\t\t</p>
\t</div>
\t<div class=\"overlay-footer\">
\t\t<form action=\"";
            // line 283
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_server_attach_switch"), "html", null, true);
            echo "\">
\t\t\t";
            // line 284
            echo $this->env->getExtension('deskpro_templating')->formToken();
            echo "
            ";
            // line 285
            if (isset($context["use_fs"])) { $_use_fs_ = $context["use_fs"]; } else { $_use_fs_ = null; }
            if ($_use_fs_) {
                $context["phrase_part"] = $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.db");
            } else {
                $context["phrase_part"] = $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.fs");
            }
            // line 286
            echo "\t\t\t<button class=\"clean-white\">";
            if (isset($context["phrase_part"])) { $_phrase_part_ = $context["phrase_part"]; } else { $_phrase_part_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "admin.server.btn_confirm_switch", array("subphrase" => $_phrase_part_), true);
            echo "</button>
\t\t</form>
\t</div>
</div>

<script type=\"text/javascript\">
\$(document).ready(function() {
\tvar overlay = new DeskPRO.UI.Overlay({
\t\ttriggerElement: \$('.switch-trigger'),
\t\tcontentElement: \$('#switch_confirm')
\t});
});
</script>
";
        }
        // line 300
        echo "
";
    }

    public function getTemplateName()
    {
        return "AdminBundle:Server:attachments.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  666 => 300,  453 => 203,  639 => 110,  568 => 254,  520 => 249,  657 => 287,  572 => 251,  609 => 459,  20 => 1,  659 => 274,  562 => 252,  548 => 238,  558 => 94,  479 => 82,  589 => 100,  457 => 211,  413 => 180,  953 => 406,  948 => 403,  935 => 394,  929 => 391,  916 => 382,  864 => 365,  844 => 348,  816 => 342,  807 => 339,  801 => 338,  774 => 337,  766 => 328,  737 => 314,  685 => 300,  664 => 294,  635 => 281,  593 => 445,  546 => 414,  532 => 240,  865 => 221,  852 => 216,  838 => 208,  820 => 201,  781 => 333,  764 => 178,  725 => 164,  632 => 283,  602 => 265,  565 => 253,  529 => 282,  505 => 267,  487 => 213,  473 => 221,  1853 => 842,  1849 => 841,  1839 => 837,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 813,  1778 => 812,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 782,  1705 => 781,  1688 => 770,  1681 => 769,  1677 => 768,  1667 => 764,  1656 => 762,  1648 => 757,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 714,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 695,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 651,  1379 => 650,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 610,  1289 => 607,  1276 => 604,  1263 => 601,  1244 => 596,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 565,  1181 => 561,  1171 => 557,  1158 => 550,  1114 => 530,  1104 => 526,  1072 => 512,  1062 => 508,  1024 => 494,  1014 => 490,  1000 => 482,  990 => 478,  980 => 474,  960 => 466,  918 => 448,  888 => 376,  834 => 346,  673 => 342,  636 => 284,  462 => 222,  454 => 192,  1144 => 542,  1139 => 399,  1131 => 399,  1127 => 397,  1110 => 396,  1092 => 391,  1089 => 390,  1086 => 520,  1084 => 388,  1063 => 387,  1060 => 386,  1055 => 385,  1050 => 384,  1035 => 372,  1019 => 267,  1003 => 263,  959 => 251,  900 => 377,  880 => 434,  870 => 430,  867 => 366,  859 => 362,  848 => 215,  839 => 197,  828 => 191,  823 => 188,  809 => 181,  800 => 178,  797 => 177,  794 => 176,  786 => 174,  740 => 162,  734 => 313,  703 => 354,  693 => 350,  630 => 278,  626 => 140,  614 => 275,  610 => 103,  581 => 277,  564 => 229,  525 => 235,  722 => 162,  697 => 256,  674 => 249,  671 => 425,  577 => 257,  569 => 97,  557 => 368,  502 => 229,  497 => 240,  445 => 197,  729 => 159,  684 => 261,  676 => 299,  669 => 254,  660 => 145,  647 => 286,  643 => 244,  601 => 287,  570 => 273,  522 => 200,  501 => 265,  296 => 67,  374 => 183,  631 => 111,  616 => 281,  608 => 266,  605 => 16,  596 => 102,  574 => 165,  561 => 209,  527 => 233,  433 => 93,  388 => 137,  426 => 177,  383 => 182,  461 => 246,  370 => 176,  395 => 221,  294 => 121,  223 => 49,  220 => 79,  492 => 395,  468 => 201,  444 => 193,  410 => 229,  397 => 174,  377 => 159,  262 => 113,  250 => 98,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 2108,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 1383,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 1303,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 1173,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 1041,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 970,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 538,  1126 => 916,  1106 => 900,  1100 => 897,  1026 => 841,  997 => 822,  983 => 816,  975 => 402,  956 => 800,  939 => 786,  902 => 753,  894 => 749,  879 => 373,  757 => 631,  727 => 608,  716 => 605,  670 => 297,  528 => 221,  476 => 253,  435 => 208,  354 => 153,  341 => 212,  192 => 123,  321 => 147,  243 => 151,  793 => 350,  780 => 348,  758 => 177,  700 => 312,  686 => 150,  652 => 274,  638 => 282,  620 => 139,  545 => 259,  523 => 110,  494 => 10,  459 => 226,  438 => 195,  351 => 214,  347 => 173,  402 => 222,  268 => 77,  430 => 201,  411 => 201,  379 => 219,  322 => 133,  315 => 110,  289 => 129,  284 => 128,  255 => 115,  234 => 55,  1133 => 400,  1124 => 534,  1121 => 56,  1116 => 55,  1113 => 403,  1108 => 51,  1103 => 394,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 500,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 329,  996 => 262,  989 => 316,  985 => 315,  981 => 257,  977 => 313,  970 => 91,  966 => 416,  955 => 303,  952 => 464,  943 => 299,  936 => 296,  930 => 244,  919 => 290,  917 => 289,  908 => 444,  905 => 378,  896 => 280,  891 => 227,  877 => 270,  862 => 267,  857 => 265,  837 => 347,  832 => 250,  827 => 248,  821 => 247,  803 => 179,  778 => 389,  769 => 165,  765 => 386,  753 => 328,  746 => 175,  743 => 318,  735 => 170,  730 => 218,  720 => 363,  717 => 362,  712 => 156,  691 => 204,  678 => 149,  654 => 144,  587 => 14,  576 => 179,  539 => 241,  517 => 217,  471 => 212,  441 => 239,  437 => 238,  418 => 201,  386 => 164,  373 => 144,  304 => 125,  270 => 169,  265 => 161,  229 => 91,  477 => 138,  455 => 224,  448 => 242,  429 => 179,  407 => 119,  399 => 193,  389 => 176,  375 => 217,  358 => 133,  349 => 131,  335 => 128,  327 => 119,  298 => 144,  280 => 124,  249 => 153,  194 => 78,  142 => 45,  344 => 145,  318 => 181,  306 => 111,  295 => 124,  357 => 154,  300 => 135,  286 => 80,  276 => 87,  269 => 133,  254 => 100,  128 => 66,  237 => 118,  165 => 55,  122 => 47,  798 => 337,  770 => 179,  759 => 112,  748 => 337,  731 => 108,  721 => 305,  718 => 307,  708 => 295,  696 => 147,  617 => 461,  590 => 226,  553 => 264,  550 => 156,  540 => 84,  533 => 255,  500 => 397,  493 => 225,  489 => 257,  482 => 210,  467 => 210,  464 => 215,  458 => 220,  452 => 217,  449 => 132,  415 => 181,  382 => 162,  372 => 215,  361 => 155,  356 => 215,  339 => 126,  302 => 125,  285 => 175,  258 => 71,  123 => 40,  108 => 39,  424 => 149,  394 => 86,  380 => 121,  338 => 155,  319 => 113,  316 => 131,  312 => 87,  290 => 105,  267 => 132,  206 => 82,  110 => 35,  240 => 93,  224 => 87,  219 => 85,  217 => 80,  202 => 126,  186 => 68,  170 => 61,  100 => 30,  67 => 13,  14 => 1,  1096 => 524,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 836,  1013 => 275,  1007 => 274,  1002 => 823,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 402,  940 => 298,  937 => 395,  928 => 452,  926 => 243,  915 => 239,  912 => 236,  903 => 231,  898 => 440,  892 => 229,  889 => 748,  887 => 227,  884 => 374,  876 => 222,  874 => 215,  871 => 368,  863 => 214,  861 => 213,  858 => 212,  850 => 352,  843 => 206,  840 => 406,  815 => 245,  812 => 244,  808 => 199,  804 => 395,  799 => 196,  791 => 193,  785 => 238,  775 => 184,  771 => 183,  754 => 340,  728 => 309,  726 => 169,  723 => 168,  715 => 105,  711 => 152,  709 => 162,  706 => 155,  698 => 208,  694 => 308,  692 => 155,  689 => 302,  681 => 300,  677 => 149,  675 => 148,  663 => 276,  661 => 277,  650 => 246,  646 => 112,  629 => 305,  627 => 108,  625 => 279,  622 => 270,  598 => 174,  592 => 117,  586 => 264,  575 => 257,  566 => 271,  556 => 244,  554 => 240,  541 => 243,  536 => 241,  515 => 209,  511 => 269,  509 => 244,  488 => 208,  486 => 220,  483 => 341,  465 => 223,  463 => 216,  450 => 194,  432 => 211,  419 => 155,  371 => 182,  362 => 80,  353 => 78,  337 => 140,  333 => 134,  309 => 190,  303 => 70,  299 => 148,  291 => 176,  272 => 93,  261 => 163,  253 => 59,  239 => 94,  235 => 75,  213 => 78,  200 => 75,  198 => 54,  159 => 53,  149 => 50,  146 => 59,  131 => 36,  116 => 30,  79 => 27,  74 => 18,  71 => 23,  836 => 320,  817 => 398,  814 => 200,  811 => 317,  805 => 313,  787 => 657,  779 => 169,  776 => 305,  773 => 347,  761 => 296,  751 => 163,  747 => 325,  742 => 336,  739 => 171,  736 => 317,  724 => 214,  705 => 278,  702 => 601,  688 => 113,  680 => 278,  667 => 296,  662 => 293,  656 => 418,  649 => 285,  644 => 284,  641 => 241,  624 => 109,  613 => 460,  607 => 273,  597 => 270,  591 => 267,  584 => 259,  579 => 234,  563 => 96,  559 => 245,  551 => 249,  547 => 95,  537 => 90,  524 => 220,  512 => 227,  507 => 76,  504 => 213,  498 => 142,  485 => 256,  480 => 254,  472 => 225,  466 => 210,  460 => 221,  447 => 215,  442 => 196,  434 => 212,  428 => 189,  422 => 176,  404 => 149,  368 => 81,  364 => 156,  340 => 170,  334 => 211,  330 => 148,  325 => 134,  292 => 142,  287 => 117,  282 => 62,  279 => 111,  273 => 170,  266 => 90,  256 => 83,  252 => 109,  228 => 72,  218 => 86,  201 => 58,  64 => 14,  51 => 11,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 1705,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 737,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 731,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 725,  1559 => 601,  1552 => 720,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 708,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 645,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 624,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 1001,  1257 => 1000,  1254 => 479,  1250 => 598,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 569,  1196 => 958,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 938,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 412,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 388,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 898,  1099 => 393,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 880,  1076 => 359,  1070 => 875,  1057 => 352,  1052 => 504,  1045 => 382,  1040 => 377,  1036 => 283,  1032 => 496,  1028 => 274,  1023 => 393,  1020 => 392,  1016 => 266,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 821,  979 => 379,  974 => 256,  967 => 373,  962 => 803,  958 => 801,  954 => 253,  950 => 368,  945 => 367,  942 => 460,  938 => 365,  934 => 364,  927 => 361,  923 => 387,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 272,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 410,  841 => 210,  835 => 312,  830 => 249,  826 => 202,  822 => 308,  818 => 246,  813 => 183,  810 => 340,  806 => 180,  802 => 198,  795 => 336,  792 => 239,  789 => 349,  784 => 188,  782 => 187,  777 => 291,  772 => 289,  768 => 334,  763 => 327,  760 => 329,  756 => 230,  752 => 176,  745 => 281,  741 => 280,  738 => 379,  732 => 171,  719 => 279,  714 => 264,  710 => 310,  704 => 267,  699 => 303,  695 => 285,  690 => 263,  687 => 279,  683 => 346,  679 => 298,  672 => 255,  668 => 247,  665 => 201,  658 => 244,  645 => 270,  640 => 285,  634 => 413,  628 => 466,  623 => 107,  619 => 298,  611 => 268,  606 => 279,  603 => 267,  599 => 242,  595 => 132,  583 => 263,  580 => 99,  573 => 274,  560 => 268,  543 => 172,  538 => 257,  534 => 405,  530 => 202,  526 => 213,  521 => 226,  518 => 233,  514 => 232,  510 => 202,  503 => 266,  496 => 226,  490 => 214,  484 => 394,  474 => 202,  470 => 231,  446 => 241,  440 => 218,  436 => 113,  431 => 186,  425 => 193,  416 => 231,  412 => 230,  408 => 141,  403 => 194,  400 => 225,  396 => 299,  392 => 139,  385 => 186,  381 => 185,  367 => 180,  363 => 164,  359 => 79,  355 => 326,  350 => 94,  346 => 140,  343 => 143,  328 => 135,  324 => 164,  313 => 128,  307 => 108,  301 => 124,  288 => 116,  283 => 62,  271 => 105,  257 => 101,  251 => 58,  238 => 84,  233 => 116,  195 => 59,  191 => 49,  187 => 62,  183 => 62,  130 => 51,  88 => 32,  76 => 24,  115 => 34,  95 => 23,  655 => 275,  651 => 232,  648 => 231,  637 => 241,  633 => 272,  621 => 462,  618 => 277,  615 => 268,  604 => 186,  600 => 271,  588 => 305,  585 => 261,  582 => 260,  571 => 242,  567 => 95,  555 => 250,  552 => 190,  549 => 208,  544 => 230,  542 => 290,  535 => 256,  531 => 254,  519 => 87,  516 => 248,  513 => 216,  508 => 230,  506 => 401,  499 => 241,  495 => 239,  491 => 145,  481 => 218,  478 => 235,  475 => 184,  469 => 197,  456 => 204,  451 => 243,  443 => 132,  439 => 129,  427 => 185,  423 => 187,  420 => 208,  409 => 179,  405 => 148,  401 => 148,  391 => 173,  387 => 129,  384 => 160,  378 => 145,  365 => 161,  360 => 171,  348 => 191,  336 => 125,  332 => 150,  329 => 73,  323 => 135,  310 => 127,  305 => 69,  277 => 172,  274 => 135,  263 => 59,  259 => 100,  247 => 96,  244 => 95,  241 => 150,  222 => 133,  210 => 60,  207 => 77,  204 => 57,  184 => 41,  181 => 40,  167 => 99,  157 => 36,  96 => 29,  421 => 147,  417 => 71,  414 => 142,  406 => 170,  398 => 170,  393 => 177,  390 => 165,  376 => 85,  369 => 157,  366 => 174,  352 => 192,  345 => 213,  342 => 127,  331 => 140,  326 => 102,  320 => 130,  317 => 134,  314 => 136,  311 => 191,  308 => 141,  297 => 122,  293 => 119,  281 => 174,  278 => 96,  275 => 107,  264 => 103,  260 => 107,  248 => 77,  245 => 57,  242 => 94,  231 => 100,  227 => 113,  215 => 88,  212 => 82,  209 => 88,  197 => 70,  177 => 58,  171 => 57,  161 => 42,  132 => 47,  121 => 27,  105 => 25,  99 => 31,  81 => 21,  77 => 19,  180 => 56,  176 => 70,  156 => 52,  143 => 46,  139 => 56,  118 => 38,  189 => 68,  185 => 46,  173 => 54,  166 => 41,  152 => 50,  174 => 41,  164 => 113,  154 => 51,  150 => 52,  137 => 48,  133 => 81,  127 => 41,  107 => 26,  102 => 44,  83 => 19,  78 => 34,  53 => 10,  23 => 6,  42 => 7,  138 => 35,  134 => 38,  109 => 33,  103 => 32,  97 => 35,  94 => 34,  84 => 22,  75 => 31,  69 => 17,  66 => 16,  54 => 27,  44 => 9,  230 => 74,  226 => 73,  203 => 92,  193 => 72,  188 => 121,  182 => 45,  178 => 65,  168 => 56,  163 => 68,  160 => 39,  155 => 77,  148 => 49,  145 => 47,  140 => 40,  136 => 82,  125 => 36,  120 => 35,  113 => 36,  101 => 37,  92 => 25,  89 => 23,  85 => 25,  73 => 18,  62 => 18,  59 => 17,  56 => 11,  41 => 6,  126 => 30,  119 => 41,  111 => 46,  106 => 32,  98 => 33,  93 => 27,  86 => 26,  70 => 21,  60 => 16,  28 => 1,  36 => 4,  114 => 43,  104 => 36,  91 => 28,  80 => 25,  63 => 12,  58 => 13,  40 => 6,  34 => 4,  45 => 8,  61 => 20,  55 => 12,  48 => 10,  39 => 7,  35 => 4,  31 => 3,  26 => 2,  21 => 2,  46 => 10,  29 => 2,  57 => 6,  50 => 11,  47 => 10,  38 => 8,  33 => 9,  49 => 9,  32 => 3,  246 => 96,  236 => 91,  232 => 92,  225 => 82,  221 => 110,  216 => 65,  214 => 82,  211 => 46,  208 => 128,  205 => 127,  199 => 84,  196 => 73,  190 => 58,  179 => 7,  175 => 43,  172 => 116,  169 => 57,  162 => 54,  158 => 41,  153 => 45,  151 => 39,  147 => 51,  144 => 86,  141 => 50,  135 => 44,  129 => 42,  124 => 74,  117 => 70,  112 => 72,  90 => 22,  87 => 21,  82 => 20,  72 => 17,  68 => 20,  65 => 19,  52 => 13,  43 => 6,  37 => 5,  30 => 8,  27 => 2,  25 => 5,  24 => 3,  22 => 2,  19 => 1,);
    }
}
