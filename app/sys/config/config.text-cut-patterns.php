<?php return array(
	# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	# This file should not be edited directly. If you want
	# to add custom patterns, create a new file named
	# config.text-cut-patterns.php in the same directory
	# as your config.php file.
	# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	'outlook_1'                          => 'lang:#(\-\-\-\-\-\s*Original Message\s*\-\-\-\-\-)?\s%From%: (.*?)\s+%Sent%: (.*?)\s+%To%: (.*?)\s+(%CC%: (.*?)\s+)?%Subject%: (.*?)\s+#i',

	'sparrow_1'                          => '#^On (.*?), ([0-9]+) (.*?) ([0-9]{4})( at (.*?))?, (.*?) wrote:$#m',

	'thunderbird_1'                      => '#On ([0-9]+)/([0-9]+)/([0-9]+) ([0-9]{1,2}):([0-9]{1,2})\s*([ap]m)?, (.*?) wrote:#i',
	'thunderbird_2'                      => '#\-\-\- Original Message Follows \-\-\-\s+Sender:(.*?)\s+Date:(.*?)\s+#',

	'applemail_1'                        => '#On (.*?), ([0-9]+), at (.*?), (.*?) wrote:\s+#',

	'zimbra_1'                           => '#\-\-\-\-\-\s+Original Message\s+\-\-\-\-\-\sFrom: (.*?)\sTo: (.*?)#',

	'generic_1'                          => '#^[a-zA-Z0-9\-\.\'" ]+ <.*?@[a-zA-Z0-9\.\-_]+> wrote:\s$#m',
	'generic_2'                          => '#^El (.*?) a las (.*?) (.*?) escribi(ó|o):\s$#m',
	'generic_3'                          => '#^On .*? <.*?@[a-zA-Z0-9\.\-_]+> wrote:\s*$#m',
	'generic_4'                          => '#^On .*? <.*?@[a-zA-Z0-9\.\-_]+<.*?@[a-zA-Z0-9\.\-_]+>> wrote:\s*$#m',
	'generic_5_it'                       => '#^Il .*? ha scritto:\s*$#m',

	'generic_6'                          => '#^.*? wrote on [0-9/]+ [0-9:]+( (am|AM|pm|PM))?:#im',

	'mutt'                               => '#On \d{4}\-\d{2}\-\d{2} \d{2}:\d{2}, (.*?) wrote:#i',
);