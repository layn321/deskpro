<?php include __DIR__ . '/tpl-header.html.php' ?>
	<?php if ($message == 'paused'): ?>
		This account has been paused.
		<br/><br/>
		If you are the account owner, you should contact support at <a href="mailto:support@deskpro.com">support@deskpro.com</a>
		to resume service.
	<?php elseif ($message == 'cancelled'): ?>
		This account has been cancelled.
	<?php else: ?>
		This account is suspended.
		<br/><br/>
		If you are the account owner, you should contact support at <a href="mailto:support@deskpro.com">support@deskpro.com</a>
		to learn how to resume service.
	<?php endif ?>

	<div style="margin-top: 45px;  background-color: #D0E8F4; padding: 8px; border-radius: 6px; -webkit-border-radius: 6px;">
		<strong>Contact Us</strong><br />
		If you have any questions, you can email us directly at <a href="mailto:sales@deskpro.com">sales@deskpro.com</a>
		or submit a ticket on our helpdesk at <a href="https://support.deskpro.com/new-ticket">https://support.deskpro.com/</a>
	</div>
<?php include __DIR__ . '/tpl-footer.html.php' ?>