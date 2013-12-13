<?php if ($conversation): ?>
	DpChatWidget.doResume = true;
	<?php if ($conversation->is_window): ?>
		DpChatWidget.isWindowChat = true;
	<?php endif ?>
<?php endif ?>
<?php if ($to_login_page): ?>
	DpChatWidget.toLoginPage = true;
<?php endif ?>
DpChatWidget.initWidget('<?php echo $session_id ?>');
