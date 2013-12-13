<?php return array(
	# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	# This file should not be edited directly. If you want
	# to add custom postmaster patterns, create a new file named
	# config.postmaster-emails.php in the same directory
	# as your config.php file.
	# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	// postmaster@something.com
	// The proper postmaster address as defined by rfc2142
	'postmaster1' => '#^postmaster@#i',

	// postmaster-xyz@something.com
	// Seen by some setups where the -xyz part determines the server used (usually for debugging)
	'postmaster2' => '#^postmaster\-.*?@#i',

	// xyz@postmaster.something.com
	// Seen by some automated systems where the bit before the @ is a special code
	'postmaster_domain' => '#@postmaster\..*?$#i'
);