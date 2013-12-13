<?php if ($is_widget): ?>
	<div style="margin-top: 15px;  background-color: #D0E8F4; padding: 8px; border-radius: 6px; -webkit-border-radius: 6px;">
		<strong>Contact Us</strong><br />
		If you have any questions, email us directlry at <a href="mailto:sales@deskpro.com">sales@deskpro.com</a>.
	</div>
<?php else: ?>
	<div style="margin-top: 45px;  background-color: #D0E8F4; padding: 8px; border-radius: 6px; -webkit-border-radius: 6px;">
		<strong>Contact Us</strong><br />
		If you have any questions, just submit this form and we'll get back to you at the email address you signed up under.
		You can also email us directly at <a href="mailto:sales@deskpro.com">sales@deskpro.com</a>.

		<div class="email-form">
			<textarea style="width: 98%; height: 120px; font-family: sans-serif; padding: 5px; margin: 5px 0 5px 0; border-radius: 3px; border: 1px solid #aaa;" placeholder="Enter your question here" name="message"></textarea><br />
			<div style="padding-top: 5px;">
				<button	class="send-btn btn btn-primary">Send Message</button>
				<img class="send-loading" src="https://www.deskpro.com/wp-content/themes/dpstyle/assets/images/loading-small-flat.gif" width="16" height="11" style="margin-top: 4px; display: none;" />
			</div>
		</div>
		<div class="email-form-sent" style="display:none; margin: 20px;">
			Thank you for your message. Our agents will respond to you as soon as they can.
		</div>
	</div>
<?php endif ?>