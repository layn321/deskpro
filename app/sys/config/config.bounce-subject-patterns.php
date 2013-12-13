<?php return array(

	# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	# This file should not be edited directly. If you want
	# to add custom patterns, create a new file named
	# config.bounce-subject-patterns.php in the same directory
	# as your config.php file.
	# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	'#^Delivery Status Notification (?P<subject>.*?)$#',
	'#^Undeliverable: (?P<subject>.*?)$#',
	'#^Out of Office: (?P<subject>.*?)$#',
	'#^Automatic reply: (?P<subject>.*?)$#i',
	'#^Out of Office AutoReply: (?P<subject>.*?)$#i'
);