<?php if (!defined('DP_ROOT')) exit('No access'); ?>
<?php $view->extend('InstallBundle:Install:layout.html.php') ?>
<?php $view['slots']->start('subtitle') ?>DeskPRO v3 Detected<?php $view['slots']->stop() ?>
<fieldset>
	<div class="clearfix">
		<div class="input">
			<p>
				The database details you entered into your configuration is for a DeskPRO v<?php echo $version ?> database.
				DeskPRO v4 needs an empty database to install into.
			</p>
			<br />
			<p>
				If you are looking to <strong>upgrade</strong> your existing v3 database, then you need to
				run the import tool from the command-line. The tool will copy all data from your v3 database into
				a new v4 database. Refer to the <span class="label" style="color:#000; font-size: 11px;font-variant: normal; text-transform: none;">README.txt</span> file for instructions.
			</p>
		</div>
	</div>
</fieldset>