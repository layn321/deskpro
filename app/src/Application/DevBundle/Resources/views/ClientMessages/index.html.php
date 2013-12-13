<?php $view->extend('DevBundle::layout.html.php') ?>

<h1>Send New Message</h1>
<form action="<?php echo $view['router']->generate('dev_cm') ?>" method="post">
	Channel: <input type="text" name="client_message[channel]" value="<?php echo $cm['channel']; ?>" /><br />
	Created By Client: <input type="text" name="client_message[created_by_client]" value="<?php echo $cm['created_by_client']; ?>" /><br />
	For Client: <input type="text" name="client_message[for_client]" value="<?php echo $cm['for_client']; ?>" /><br />
	For Person ID: <input type="text" name="client_message[for_person_id]" value="<?php echo $cm && $cm->for_person ? $cm->for_person->id : ''; ?>" /><br />
	Data (k=v for arrays): <textarea name="client_message[data]" rows="15" cols="80"><?php echo $data_str; ?></textarea><br />

	<input type="submit" value="Insert" />
</form>
