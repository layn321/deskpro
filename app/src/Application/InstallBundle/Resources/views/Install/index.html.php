<?php if (!defined('DP_ROOT')) exit('No access'); ?>
<?php $view->extend('InstallBundle:Install:layout.html.php') ?>
<?php $view['slots']->start('subtitle') ?>Step 2: Server and Config Checks<?php $view['slots']->stop() ?>
<?php $failed = false ?>
<?php $failed_phpini = false ?>

<?php require(DP_ROOT.'/src/Application/InstallBundle/Resources/views/Install/server-checks-table.html.php') ?>

<?php if ($new_download): ?>
	<div class="alert-message block-message info" style="border: 3px solid #BDD1D7; margin-top: 35px; margin-bottom: 35px;">
		<strong>There is a newer version of DeskPRO available</strong><br />
		The version you are trying to install is version <var><?php echo $this_build ?></var>. A newer version, version <var><?php echo $new_build ?></var>, is available.
		<div class="alert-actions" style="margin-top: 10px;">
			<?php $new_download = 'http://www.deskpro.com/downloads/DeskPRO.zip'; ?>
			<a class="btn primary" href="<?php echo $new_download ?>">Click here to download the new version now</a>
			<br /><div style="font-size: 10px;">Download URL: <?php echo $new_download ?></div>
		</div>
	</div>
<?php endif ?>


<div id="fatal_errors_display" class="alert-message block-message error" <?php if (!$is_fatal): ?>style="display: none"<?php endif ?>>
	<strong>There were errors</strong>, as noted above, that must be fixed before you
	can install DeskPRO. You cannot continue with the installation until the problems
	above have been fixed.

	<?php if ($db_failed && $can_write_config): ?>
		<br /><br />
		<a href="<?php echo $view['router']->generate('install_configedit') ?>" style="color: #0069D6;">Go back to the config editor</a> and update your database details then try again.
		<br /><br />
	<?php endif ?>

	<?php if ($ini_path and $failed_phpini): ?>
		<br /><br />
		We have detected the path to your php.ini file at <code><?php echo $ini_path ?></code>. You will need to edit
		this file to enable the missing extensions. Depending on your server, you may also need to download and compile the extensions
		first.
		<br /><br />
	<?php endif ?>

	<div class="alert-actions">
		<a class="btn" href="<?php echo $view['router']->generate('install_checks') ?>">Refresh the page to re-run the checks</a>
	</div>
</div>

<div id="success_display" class="alert-message block-message success" <?php if ($is_fatal): ?>style="display: none"<?php endif ?>>
	<?php if ($new_download): ?>
		If you do not want to download the updated version of DeskPRO, you can conitnue on to the next step to install this outdated version.
	<?php else: ?>
		<strong>Everything looks okay.</strong> You are ready to continue to continute
		to the next step.
	<?php endif ?>

	<div class="alert-actions submit-area">
		<a class="btn" href="<?php echo $view['router']->generate('install_check_urls') ?>" onclick="$(this).parent().addClass('clicked');">Go to step 3: Check URL rewriting</a>
		<span class="next-loading"></span>
	</div>
</div>

<!--
<?php echo htmlspecialchars(print_r($errors, true)) ?>
-->

<?php if (isset($do_data_dir_check) && $do_data_dir_check): ?>
<script type="text/javascript">
$(document).ready(function() {
	var baseurl = window.location.href;
	baseurl = baseurl.replace(/\/index\.php\/(.*?)$/, '');
	var url = baseurl + '<?php echo $do_data_dir_check ?>/index.html';

	$.ajax({
		url: url,
		dataType: 'text',
		cache: false,
		success: function(result) {
			if (result && result.indexOf('DESKPRO_READABLE_FILE') !== -1) {
				$('#check_data_dir_web').show();
				$('#success_display').hide();
				$('#fatal_errors_display').show();
			}
		}
	});
});
</script>
<?php endif ?>