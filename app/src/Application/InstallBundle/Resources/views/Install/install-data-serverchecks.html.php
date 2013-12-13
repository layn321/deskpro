<?php if (!defined('DP_ROOT')) exit('No access'); ?>
<?php $view->extend('InstallBundle:Install:layout.html.php') ?>
<?php $view['slots']->start('subtitle') ?>Server Errors<?php $view['slots']->stop() ?>
<?php $failed = false ?>
<?php $failed_phpini = false ?>
<?php $has_db_checks = false ?>

<?php require(DP_ROOT.'/src/Application/InstallBundle/Resources/views/Install/server-checks-table.html.php') ?>

<div class="alert-message block-message error">
	<strong>There were errors</strong>, as noted above, that must be fixed before you
	can use DeskPRO. You cannot continue until the problems
	above have been fixed.

	<?php if ($ini_path and $failed_phpini): ?>
		<br /><br />
		We have detected the path to your php.ini file at <code><?php echo $ini_path ?></code>. You will need to edit
		this file to enable the missing extensions. Depending on your server, you may also need to download and compile the extensions
		first.
		<br /><br />
		<strong>Note:</strong> The PHP used by the webserver and the PHP used on the command-line are often different. This means that if you
		have updated php.ini to get through the command-line installer, then you'll also need to update the php.ini noted above.
	<?php endif ?>

	<div class="alert-actions">
		<a class="btn" href="<?php echo $view['router']->generate('install_checks') ?>">Refresh the page to re-run the checks</a>
	</div>
</div>

<!--
<?php echo htmlspecialchars(print_r($errors, true)) ?>
-->
