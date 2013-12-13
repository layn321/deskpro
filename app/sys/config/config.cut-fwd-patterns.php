<?php return array(
	# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	# This file should not be edited directly. If you want
	# to add custom translate maps, create a new file named
	# config.cut-fwd-patterns-translate.php in the same directory
	# as your config.php file.
	# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	'standard_1' => '#-{3,15}\s*Forward(ed)?( Message)?\s*-{3,15}#i',
	'standard_2' => '#^\s*Forward(ed)?( Message):\s*$#im',

	'outlook_1' => '#^\-+Original Message\-+\s*$#im',
);