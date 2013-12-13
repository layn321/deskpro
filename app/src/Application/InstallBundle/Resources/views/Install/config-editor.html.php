<?php if (!defined('DP_ROOT')) exit('No access'); ?>
<?php $view->extend('InstallBundle:Install:layout.html.php') ?>
<?php $view['slots']->start('subtitle') ?>Database Configuration<?php $view['slots']->stop() ?>
<form id="admin_form" action="<?php echo $view['router']->generate('install_configedit') ?>" method="post">
	<input type="hidden" name="process" value="1" />
	<fieldset>
		<legend>Database Configuration</legend>
		<div class="clearfix">
			<div class="input">
				<p>
					Fill in this form and we will create the requried <code>/config.php</code> file for you.
				</p>
			</div>
		</div>
		<div class="clearfix">
			<label>Database Server</label>
			<div class="input">
				<input type="text" name="DP_DATABASE_HOST" value="<?php if ($exist['DP_DATABASE_HOST']) echo $exist['DP_DATABASE_HOST']; else { if ($is_win) echo '127.0.0.1'; else echo 'localhost'; } ?>" size="30" />
			</div>
		</div>
		<div class="clearfix">
			<label>Database User</label>
			<div class="input">
				<input type="text" name="DP_DATABASE_USER" value="<?php if ($exist['DP_DATABASE_USER']) echo $exist['DP_DATABASE_USER']; else echo 'root'; ?>" size="30" />
			</div>
		</div>
		<div class="clearfix">
			<label>Database Password</label>
			<div class="input">
				<input type="text" name="DP_DATABASE_PASSWORD" value="<?php if ($exist['DP_DATABASE_PASSWORD']) echo $exist['DP_DATABASE_PASSWORD']; else echo ''; ?>" size="30" />
			</div>
		</div>
		<div class="clearfix">
			<label>Database Name</label>
			<div class="input">
				<input type="text" name="DP_DATABASE_NAME" value="<?php if ($exist['DP_DATABASE_NAME']) echo $exist['DP_DATABASE_NAME']; else echo 'deskpro'; ?>" size="30" />
			</div>
		</div>
		<div class="clearfix">
			<label>Technical Email Address</label>
			<div class="input">
				<input type="text" name="DP_TECHNICAL_EMAIL" value="<?php if ($exist['DP_TECHNICAL_EMAIL']) echo $exist['DP_TECHNICAL_EMAIL']; else echo ''; ?>" placeholder="Enter an email address" size="30" />
				<br /><small>This address will be used to report database errors that prevent DeskPRO from working.</small>
			</div>
		</div>
		<div class="actions">
			<input class="btn primary" type="submit" value="Write config.php and continue &rarr;" />
		</div>
	</fieldset>
</form>
