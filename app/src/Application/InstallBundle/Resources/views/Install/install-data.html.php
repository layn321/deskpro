<?php if (!defined('DP_ROOT')) exit('No access'); ?>
<?php $view->extend('InstallBundle:Install:layout.html.php') ?>
<?php if ($is_webinstall) { ?>
	<?php $view['slots']->start('subtitle') ?>Step 6: Create Your Admin Account<?php $view['slots']->stop() ?>
<?php } else { ?>
	<?php $view['slots']->start('subtitle') ?>Create Your Admin Account<?php $view['slots']->stop() ?>
<?php } ?>
<?php $failed = false ?>
<script type="text/javascript">
var validate = function() {
	var ret = true;
	if (!$.trim($('#first_name').val()).length) {
		ret = false;
		$('#first_name').closest('div.clearfix').addClass('error');
	} else {
		$('#first_name').closest('div.clearfix').removeClass('error');
	}

	if (!$.trim($('#last_name').val()).length) {
		ret = false;
		$('#last_name').closest('div.clearfix').addClass('error');
	} else {
		$('#last_name').closest('div.clearfix').removeClass('error');
	}

	if (!$('#email_address').val().match(/.+@.+/)) {
		ret = false;
		$('#email_address').closest('div.clearfix').addClass('error');
	} else {
		$('#email_address').closest('div.clearfix').removeClass('error');
	}

	if ($('#password').val() != $('#password2').val()) {
		ret = false;
		$('#password').closest('div.clearfix').addClass('error');
		$('#password_mismatch').show();
		$('#password_empty').hide();
	} else {
		$('#password').closest('div.clearfix').removeClass('error');

		if (!$('#password').val().trim().length) {
			ret = false;
			$('#password').closest('div.clearfix').addClass('error');
			$('#password_mismatch').hide();
			$('#password_empty').show();
		} else {
			$('#password').closest('div.clearfix').removeClass('error');
		}
	}

	return ret;
};
$(document).ready(function() {
	$('#admin_form').on('submit', function(ev) {
		if (!validate()) {
			ev.preventDefault();

			$('#admin_form input').on('blur', function() { validate(); });

			return;
		}

		$('.submit-area').addClass('clicked');
	});
});
</script>
<style type="text/css">
.help-inline.e { display: none }
.error .help-inline.e { display: inline }
</style>
<form id="admin_form" action="<?php echo $view['router']->generate('install_install_data_save') ?>" method="post">
	<fieldset>
		<legend>Your admin account</legend>
		<div class="clearfix">
			<label>First Name</label>
			<div class="input">
				<input type="text" id="first_name" name="admin[first_name]" value="" size="30" />
				<span class="help-inline e">Please fill in your first name</span>
			</div>
		</div>
		<div class="clearfix">
			<label>Last Name</label>
			<div class="input">
				<input type="text" id="last_name" name="admin[last_name]" value="" size="30" />
				<span class="help-inline e">Please fill in your last name</span>
			</div>
		</div>
		<div class="clearfix">
			<label>Email Address</label>
			<div class="input">
				<input type="text" id="email_address" name="admin[email]" value="" size="30" />
				<span class="help-inline e">Please enter a valid email address</span>
			</div>
		</div>
		<div class="clearfix">
			<label>Password</label>
			<div class="input">
				<input type="password" id="password" name="admin[password]" value="" size="30" />
				<span class="help-inline e">
					<span id="password_mismatch">The two password fields do not match</span>
					<span id="password_empty">Please enter a password</span>
				</span>
			</div>
		</div>
		<div class="clearfix">
			<label>Repeat Password</label>
			<div class="input">
				<input type="password" autocomplete="off" id="password2" name="admin[password2]" value="" size="30" />
			</div>
		</div>
		<div class="alert-actions submit-area">
			<input class="btn primary" type="submit" value="Create Admin and Finish Installation &rarr;" />
			<span class="next-loading"></span>
		</div>
	</fieldset>
</form>
