<?php if (!defined('DP_ROOT')) exit('No access'); ?>
<?php if (!isset($run_context)) $run_context = 'install'; ?>
<style type="text/css">
	.kb-read-more {
		font-size: 11px;
		float: right;
		margin: -10px -8px 10px 30px;

		display: block;
		background-color: #fff;
		line-height: 100%;
		padding: 5px 8px 5px 22px;

		-webkit-border-radius: 4px;
		-moz-border-radius: 4px;
		border-radius: 4px;
		-moz-background-clip: padding; -webkit-background-clip: padding-box; background-clip: padding-box;

		background: #fff url(../../web/images/agent/icons/small-light-on.png) no-repeat 7px 50%;
		border: 1px solid #aaa;
	}
	.kb-read-more:hover {
		border: 1px solid #2B629B;
		text-decoration: none;
		color: #1E4C7A;
	}
</style>

<h3>Server Checks</h3>
<table class="bordered-table zebra-striped">
<tbody>
<tr>
	<td>
		<?php if (!isset($errors['php_version'])): ?>
		<span class="label success" style="float:right">OK</span>
		<?php else: $failed = true; ?>
		<span class="label important" style="float:right">FAIL</span>
		<?php endif ?>
		Check that the <a href="http://php.net/">PHP</a> version is &gt;= 5.3.2
		<?php if ($failed): ?>
		<div class="alert-message block-message error">
			<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.error_php_version') ?>" class="kb-read-more" target="_blank">Read more about fixing this error</a>
			DeskPRO requires PHP 5.3.2. You have <?php echo phpversion() ?>.
		</div>
		<?php endif ?>
	</td>
</tr>

<?php if ($run_context == 'install'): ?>
<tr>
	<td>
		<?php if (!isset($errors['config']) && !isset($errors['config_values']) && !isset($errors['config_dp3_values']) && !isset($errors['config_technical_email'])): ?>
		<span class="label success" style="float:right">OK</span>
		<?php else: $failed = true; ?>
		<span class="label important" style="float:right">FAIL</span>
		<?php endif ?>
		Check for valid config.php
		<?php if ($failed): ?>
		<div class="alert-message block-message error">
			<?php if (isset($errors['config'])): ?>
				<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.error_config_missing') ?>" class="kb-read-more" target="_blank">Read more about fixing this error</a>
				/config.php is missing. Copy /config.new.php and edit it to add your database settings.
			<?php elseif (isset($errors['config_values'])): ?>
				<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.error_config_invalid') ?>" class="kb-read-more" target="_blank">Read more about fixing this error</a>
				/config.php exists but it does not contain the required settings. You should copy /config.new.php and edit it to add your database settings.
			<?php elseif (isset($errors['config_dp3_values'])): ?>
				<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.error_config_invalid') ?>" class="kb-read-more" target="_blank">Read more about fixing this error</a>
				/config.php exists but it appears to contain values from an old DeskPRO v3 installation. DeskPRO v3 and DeskPRO v4 use different config.php formats. You should copy /config.new.php and edit it to add your database settings.
			<?php elseif (isset($errors['config_technical_email'])): ?>
				<?php $is_fatal = true ?>
				<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.error_config_invalid') ?>" class="kb-read-more" target="_blank">Read more about fixing this error</a>
				/config.php exists but you have not entered an email address for the DP_TECHNICAL_EMAIL setting.
			<?php endif ?>
		</div>
		<?php endif ?>
	</td>
</tr>
<?php endif ?>

<tr>
	<td>
		<?php $failed = false ?>
		<?php if (!isset($errors['pdo_ext']) && !isset($errors['pdo_mysql_ext'])): ?>
		<span class="label success" style="float:right">OK</span>
		<?php else: $failed = true; $failed_phpini = true; ?>
		<span class="label important" style="float:right">FAIL</span>
		<?php endif ?>
		Check that the <a href="http://php.net/manual/en/pdo.installation.php">PDO extension</a> is enabled and has the MySQL driver installed
		<?php if ($failed): ?>
		<div class="alert-message block-message error">
			<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.error_pdo_ext') ?>" class="kb-read-more" target="_blank">Read more about fixing this error</a>
			DeskPRO requires the PDO extension and the MySQL driver
		</div>
		<?php endif ?>
	</td>
</tr>

<tr>
	<td>
		<?php $failed = false ?>
		<?php if (!isset($errors['libxml_ext']) && !isset($errors['libxml_version'])): ?>
			<span class="label success" style="float:right">OK</span>
		<?php else: $failed = true; $failed_phpini = true; ?>
			<span class="label important" style="float:right">FAIL</span>
		<?php endif ?>
		Check that the <a href="http://php.net/manual/en/book.libxml.php">libxml</a> extension is installed
		<?php if ($failed): ?>
			<div class="alert-message block-message error">
				<?php if (isset($errors['libxml_ext'])): ?>
					DeskPRO requires the <a href="http://php.net/manual/en/book.libxml.php">libxml extension</a>. You
					will need to rebuild PHP with libxml enabled.
				<?php elseif (isset($errors['libxml_version'])): ?>
					Your version of PHP was built against a very old version of libxml (libxml is used by the <a href="http://php.net/manual/en/book.libxml.php">libxml extension</a>).
					Older versions of libxml contain bugs that can lead to problems parsing HTML email. You need to update the version of <a href="http://xmlsoft.org/">libxml</a> on your server, and then re-build PHP.
				<?php endif ?>
			</div>
		<?php endif ?>
	</td>
</tr>

<tr>
	<td>
		<?php $failed = false ?>
		<?php if (!isset($errors['iconv_ext']) && !isset($errors['iconv_ext'])): ?>
		<span class="label success" style="float:right">OK</span>
		<?php else: $failed = true; $failed_phpini = true; ?>
		<span class="label important" style="float:right">FAIL</span>
		<?php endif ?>
		Check that the <a href="http://php.net/manual/en/iconv.installation.php">iconv</a> or
		<a href="http://php.net/manual/en/mbstring.installation.php">mbstring</a> extension is installed
		<?php if ($failed): ?>
		<div class="alert-message block-message error">
			<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.error_iconv_ext') ?>" class="kb-read-more" target="_blank">Read more about fixing this error</a>
			DeskPRO requires the iconv or mbstring extension to be installed and enabled.
		</div>
		<?php endif ?>
	</td>
</tr>

<tr>
	<td>
		<?php $failed = false ?>
		<?php if (!isset($errors['json_ext'])): ?>
		<span class="label success" style="float:right">OK</span>
		<?php else: $failed = true; $failed_phpini = true; ?>
		<span class="label important" style="float:right">FAIL</span>
		<?php endif ?>
		Check that the <a href="http://php.net/manual/en/json.installation.php">json_encode extension</a> is installed
		<?php if ($failed): ?>
		<div class="alert-message block-message error">
			<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.error_json_ext') ?>" class="kb-read-more" target="_blank">Read more about fixing this error</a>
			DeskPRO requires the json_encode extension.
		</div>
		<?php endif ?>
	</td>
</tr>

<tr>
	<td>
		<?php $failed = false ?>
		<?php if (!isset($errors['session_start'])): ?>
		<span class="label success" style="float:right">OK</span>
		<?php else: $failed = true; $failed_phpini = true; ?>
		<span class="label important" style="float:right">FAIL</span>
		<?php endif ?>
		Check that the <a href="http://php.net/manual/en/session.installation.php">session extension</a> is installed
		<?php if ($failed): ?>
		<div class="alert-message block-message error">
			<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.error_session_ext') ?>" class="kb-read-more" target="_blank">Read more about fixing this error</a>
			DeskPRO requires the session extension.
		</div>
		<?php endif ?>
	</td>
</tr>

<tr>
	<td>
		<?php $failed = false ?>
		<?php if (!isset($errors['ctype_ext'])): ?>
		<span class="label success" style="float:right">OK</span>
		<?php else: $failed = true; $failed_phpini = true; ?>
		<span class="label important" style="float:right">FAIL</span>
		<?php endif ?>
		Check that the <a href="http://php.net/manual/en/ctype.installation.php">ctype extension</a> is installed
		<?php if ($failed): ?>
		<div class="alert-message block-message error">
			<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.error_ctype_ext') ?>" class="kb-read-more" target="_blank">Read more about fixing this error</a>
			DeskPRO requires ctype extension.
		</div>
		<?php endif ?>
	</td>
</tr>

<tr>
	<td>
		<?php $failed = false ?>
		<?php if (!isset($errors['dom_ext'])): ?>
		<span class="label success" style="float:right">OK</span>
		<?php else: $failed = true; $failed_phpini = true; ?>
		<span class="label important" style="float:right">FAIL</span>
		<?php endif ?>
		Check that the <a href="http://php.net/manual/en/dom.setup.php">dom extension</a> is installed
		<?php if ($failed): ?>
		<div class="alert-message block-message error">
			<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.error_dom_ext') ?>" class="kb-read-more" target="_blank">Read more about fixing this error</a>
			DeskPRO requires DOM extension.
		</div>
		<?php endif ?>
	</td>
</tr>

<tr>
	<td>
		<?php $failed = false ?>
		<?php if (!isset($errors['tokenizer_ext'])): ?>
		<span class="label success" style="float:right">OK</span>
		<?php else: $failed = true; $failed_phpini = true; ?>
		<span class="label important" style="float:right">FAIL</span>
		<?php endif ?>
		Check that the <a href="http://php.net/manual/en/tokenizer.installation.php">tokenizer extension</a> is installed
		<?php if ($failed): ?>
		<div class="alert-message block-message error">
			<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.error_tokenizer_ext') ?>" class="kb-read-more" target="_blank">Read more about fixing this error</a>
			DeskPRO requires tokenizer extension.
		</div>
		<?php endif ?>
	</td>
</tr>

<tr>
	<td>
		<?php $failed = false ?>
		<?php if (!isset($errors['image_manip'])): ?>
		<span class="label success" style="float:right">OK</span>
		<?php else: $failed = true; $failed_phpini = true; ?>
		<span class="label important" style="float:right">FAIL</span>
		<?php endif ?>
		Check that an image manipulation extension is installed (<a href="http://php.net/manual/en/imagick.installation.php">Imagick</a>, <a href="http://php.net/manual/en/gmagick.installation.php">Gmagick</a>, or <a href="http://php.net/manual/en/image.installation.php">GD</a>)
		<?php if ($failed): ?>
		<div class="alert-message block-message error">
			<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.error_image_manip') ?>" class="kb-read-more" target="_blank">Read more about fixing this error</a>
			DeskPRO requires one of the following extensions: Imagick, Gmagick or GD.
		</div>
		<?php endif ?>
	</td>
</tr>


<tr>
	<td>
		<?php $failed = false ?>
		<?php if (!isset($errors['php_functions'])): ?>
		<span class="label success" style="float:right">OK</span>
		<?php else: $failed = true; $failed_phpini = true; ?>
		<span class="label notice" style="float:right">RECOMMENDED</span>
		<?php endif ?>
		Check for disabled functions
		<?php if ($failed): ?>
		<div class="alert-message block-message error">
			<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.error_disabled_functions') ?>" class="kb-read-more" target="_blank">Read more about fixing this error</a>
			We have detected the <code><a href="http://php.net/manual/en/ini.core.php#ini.disable-functions">disable_functions</a></code> directive in your php.ini file<?php if ($ini_path): ?> (<code><?php echo $ini_path ?></code>)<?php endif ?>.
			If you want to use the automatic upgrade feature, you must edit your php.ini and remove the disable_functions directive. These functions are required for automatic upgrades: escapeshellarg, exec, passthru, chdir and proc_open.
		</div>
		<?php endif ?>
	</td>
</tr>


<tr>
	<td>
		<?php $failed = false ?>
		<?php if (!isset($errors['memory_limit'])): ?>
		<span class="label success" style="float:right">OK</span>
		<?php else: $failed = true; ?>
		<span class="label important" style="float:right">FAIL</span>
		<?php endif ?>
		Check that PHP's <a href="http://php.net/manual/en/ini.core.php#ini.memory-limit">memory limit</a> is at least 128 MB
		<?php if ($failed): ?>
		<div class="alert-message block-message error">
			<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.error_memory_limit') ?>" class="kb-read-more" target="_blank">Read more about fixing this error</a>
			DeskPRO requires PHP's memory_limit option to be at least 128 MB. Edit your php.ini file <?php if ($ini_path): ?>(<code><?php echo $ini_path ?></code>)<?php endif ?> to increase the limit.
		</div>
		<?php endif ?>
	</td>
</tr>


<tr>
	<td>
		<?php $failed = false ?>
		<?php if (!isset($errors['upload_tmp_dir'])): ?>
		<span class="label success" style="float:right">OK</span>
		<?php else: $failed = true; $failed_phpini = true; ?>
		<span class="label warning" style="float:right">WARNING</span>
		<?php endif ?>
		Check that the temporary upload directory is writable
		<?php if ($failed): ?>
		<div class="alert-message block-message error">
			<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.error_upload_tmp_dir') ?>" class="kb-read-more" target="_blank">Read more about fixing this error</a>
			We have detected the <code><a href="http://php.net/manual/en/ini.core.php#ini.upload-tmp-dir">upload_tmp_dir</a></code> directive in your php.ini file<?php if ($ini_path): ?> (<code><?php echo $ini_path ?></code>)<?php endif ?> contains an invalid value.
			The temporary upload directory must be writable by the web server for uploads to be accepted. If you do not fix this problem, you will not be able to add attachments to tickets or articles or upload any other kind of file.
		</div>
		<?php endif ?>
	</td>
</tr>


<tr>
	<td>
		<?php $failed = false ?>
		<?php if (!isset($errors['data_write'])): ?>
		<span class="label success" style="float:right">OK</span>
		<?php else: $failed = true; ?>
		<span class="label important" style="float:right">FAIL</span>
		<?php endif ?>
		Check that the data directory is writable
		<?php if ($failed): ?>
		<div class="alert-message block-message error">
			<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.error_data_dir') ?>" class="kb-read-more" target="_blank">Read more about fixing this error</a>
			The data directory <?php if (isset($data_dir)): ?>(<?php echo $data_dir ?>)<?php endif ?> and all sub-directories must be writable.
			<br/>
			<?php foreach (array('', 'backups', 'debug', 'files', 'logs', 'tmp') as $dir) {
				$path = dp_get_data_dir() . DIRECTORY_SEPARATOR . $dir;

				if (!is_dir($path)) {
					@mkdir($path, 0777, true);
					@chmod($path, 0777);
				}

				if (!is_dir($path)) {
					echo "&bull; $path does not exist<br/>";
				} elseif (!is_writable($path)) {
					echo "&bull; $path is not writable<br/>";
				}
			} ?>
		</div>
			<?php if (strpos(strtoupper(PHP_OS), 'WIN') === 0): ?>
			<?php else: ?>
				<p>
					On Linux systems, you can run this command from the terminal:
					<code>chmod -R 0777 <?php echo $data_dir ?></code>
				</p>
			<?php endif ?>
		<?php endif ?>
	</td>
</tr>

<?php if (isset($do_data_dir_check) && $do_data_dir_check): ?>
	<tr id="check_data_dir_web" style="display: none">
		<td>
			<span class="label important" style="float:right">FAIL</span>
			Check that the data directory is not readable from the web
			<div class="alert-message block-message error">
				<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.error_data_dir_web_readable') ?>" class="kb-read-more" target="_blank">Read more about fixing this error</a>
				The data directory (<?php echo $data_dir ?>) is readable from the web. This means anyone who knows the URL of a file can download it.
				File attachments and potentially sensitive log information resides in the data directory so it is important that this directory is never web-readable.
				<br /><br />
				The best way to resolve this error is to move the data directory outside of your web root.
				To do this, copy the data/ directory to another location and then edit the 'dir_data' line in your config.php:
				<br />
				<code>$DP_CONFIG['dir_data'] = '/some/path/outside-of-web-root';</code>
			</div>
		</td>
	</tr>
<?php endif ?>

<tr>
	<td>
		<?php $failed = false ?>
		<?php if (!isset($errors['openssl_ext'])): ?>
		<span class="label success" style="float:right">OK</span>
		<?php else: $failed = true; ?>
		<span class="label notice" style="float:right">RECOMMENDED</span>
		<?php endif ?>
		Checking for the <a href="http://www.php.net/manual/en/openssl.installation.php">OpenSSL</a> extension
		<?php if ($failed): ?>
		<div class="alert-message block-message info">
			<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.error_openssl') ?>" class="kb-read-more" target="_blank">Read more about this</a>
			We recommend installing the OpenSSL extension so you can use web resources that require a secure connection (such as Gmail or Google Apps, secure email servers, Facebook or Twitter).
		</div>
		<?php endif ?>
	</td>
</tr>

<tr>
	<td>
		<?php $failed = false ?>
		<?php if (!isset($errors['apc_check'])): ?>
		<span class="label success" style="float:right">OK</span>
		<?php else: $failed = true; ?>
		<span class="label notice" style="float:right">RECOMMENDED</span>
		<?php endif ?>
		<?php if (isset($is_win) && $is_win): ?>
			Checking for the <a href="http://www.php.net/manual/en/apc.installation.php">APC extension</a> or the
			<a href="http://www.php.net/manual/en/book.wincache.php">WinCache extension</a>.
		<?php else: ?>
			Checking for the <a href="http://www.php.net/manual/en/apc.installation.php">APC extension</a>
		<?php endif ?>
		<?php if ($failed): ?>
		<div class="alert-message block-message info">
			<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.error_error_apc') ?>" class="kb-read-more" target="_blank">Read more about this</a>
			<?php if (isset($is_win) && $is_win): ?>
				We recommend installing the <a href="http://www.php.net/manual/en/apc.installation.php">APC extension</a>
				or the <a href="http://www.php.net/manual/en/book.wincache.php">WinCache extension</a>
				to dramatically improve performance.
			<?php else: ?>
			We recommend installing the <a href="http://www.php.net/manual/en/apc.installation.php">APC extension</a> to dramatically improve performance.
			<?php endif ?>
		</div>
		<?php endif ?>
	</td>
</tr>

<tr>
	<td>
		<?php $failed = false ?>
		<?php if (!isset($errors['magic_quotes_gpc_check'])): ?>
		<span class="label success" style="float:right">OK</span>
		<?php else: $failed = true; ?>
		<span class="label notice" style="float:right">RECOMMENDED</span>
		<?php endif ?>
		Checking if <a href="http://www.php.net/manual/en/security.magicquotes.disabling.php">magic_quotes_gpc</a> is disabled
		<?php if ($failed): ?>
		<div class="alert-message block-message info">
			<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.error_magic_quotes') ?>" class="kb-read-more" target="_blank">Read more about this</a>
			We recommend disabling <code>magic_quotes_gpc</code> in your php.ini for a small performance improvement.
			(<?php if ($ini_path): ?>Your php.ini file is located at <code><?php echo $ini_path ?></code><?php endif ?>)
		</div>
		<?php endif ?>
	</td>
</tr>

<?php if ($run_context == 'install'): ?>
	<?php if (isset($errors['dp3_files'])): ?>
	<?php $failed = true ?>
	<tr>
		<td>
			<span class="label important" style="float:right">FAIL</span>
			DeskPRO v3 files
			<div class="alert-message block-message error">
				We have detected that there are DeskPRO v3 files present. DeskPRO v4 should NOT be installed over an existing
				v3 install. Having v3 files present in the v4 directory is a security risk. You should completely delete the directory
				and then re-extract your DeskPRO v4 distribution so you have a pristine installation.
			</div>
		</td>
	</tr>
	<?php endif ?>
<?php endif ?>

</tbody>
</table>

<?php if ($run_context == 'install'): ?>
<?php $db_failed = false; ?>
<?php if ($has_db_checks): ?>
<h3>Database Checks</h3>
<table class="bordered-table zebra-striped">
	<tbody>
	<tr>
		<td>
			<?php $failed = false ?>
			<?php if (!isset($errors['db_connect'])): ?>
			<span class="label success" style="float:right">OK</span>
			<?php else: $failed = true; $db_failed = true; ?>
			<span class="label important" style="float:right">FAIL</span>
			<?php endif ?>
			Check database connection (<?php echo $db_config['user'] ?>@<?php echo $db_config['host'] ?>/<?php echo $db_config['dbname'] ?><?php if (!$failed and $did_create_db): ?>, the database was automatically created for you.<?php endif ?>)
			<?php if ($failed): ?>
			<div class="alert-message block-message error">
				<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.error_db_connect') ?>" class="kb-read-more" target="_blank">Read more about fixing this error</a>
				A database connection could not be established. Check your config.php to make sure
				the details you entered are correct.
				<p><code><?php echo $errors['db_connect']['message'] ?></code></p>
			</div>
			<?php endif; ?>
		</td>
	</tr>

	<?php if (isset($errors['db_win_localhost'])): ?>
		<tr>
			<td>
				<?php $failed = true ?>
				<span class="label important" style="float:right">FAIL</span>
				Check database connection is not through localhost
				<div class="alert-message block-message error">
					<p>
						You are connecting to the database through <code>localhost</code> which causes very poor performance on Window.
					</p>
					<p>
						Edit your config.php file to change the <code>DP_DATABASE_HOST</code> value to <code>127.0.0.1</code> instead.
					</p>
				</div>
			</td>
		</tr>
	<?php endif; ?>

	<?php if (!isset($errors['db_connect'])): ?>
	<tr>
		<td>
			<?php $failed = false ?>
			<?php if (!isset($errors['db_version'])): ?>
			<span class="label success" style="float:right">OK</span>
			<?php else: $failed = true; $db_failed = true; ?>
			<span class="label important" style="float:right">FAIL</span>
			<?php endif ?>
			Check MySQL version is &gt;= 5.0
			<?php if ($failed): ?>
			<div class="alert-message block-message error">
				<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.error_db_version') ?>" class="kb-read-more" target="_blank">Read more about fixing this error</a>
				DeskPRO requires MySQL v5.0. You need to update your version of MySQL.
			</div>
			<?php endif ?>
		</td>
	</tr>

	<tr>
		<td>
			<?php $failed = false ?>
			<?php if (!isset($errors['db_no_innodb'])): ?>
			<span class="label success" style="float:right">OK</span>
			<?php else: $failed = true; $db_failed = true; ?>
			<span class="label important" style="float:right">FAIL</span>
			<?php endif ?>
			Check for InnoDB Engine
			<?php if ($failed): ?>
			<div class="alert-message block-message error">
				<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.error_db_no_innodb') ?>" class="kb-read-more" target="_blank">Read more about fixing this error</a>
				Your MySQL server does not support the InnoDB engine.
			</div>
			<?php endif ?>
		</td>
	</tr>

	<?php if (!isset($skip_empty_check) || !$skip_empty_check): ?>
	<tr>
		<td>
			<?php $failed = false ?>
			<?php if (!isset($errors['db_not_empty'])): ?>
			<span class="label success" style="float:right">OK</span>
			<?php else: $failed = true; $db_failed = true; ?>
			<span class="label important" style="float:right">FAIL</span>
			<?php endif ?>
			Ensuring empty database
			<?php if ($failed): ?>
			<div class="alert-message block-message error">
				<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.error_db_not_empty') ?>" class="kb-read-more" target="_blank">Read more about fixing this error</a>
				Existing tables were detected in your database. DeskPRO should be installed
				into a new, fresh database.
			</div>
			<?php endif ?>
		</td>
	</tr>
		<?php endif ?>
	</tbody>
	<?php endif ?>
</table>
<?php endif ?>
<?php endif ?>