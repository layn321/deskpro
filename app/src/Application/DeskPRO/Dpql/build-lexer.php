<?php
/**************************************************************************\
| DeskPRO (r) has been developed by DeskPRO Ltd. http://www.deskpro.com/   |
| a British company located in London, England.                            |
|                                                                          |
| All source code and content Copyright (c) 2012, DeskPRO Ltd.             |
|                                                                          |
| The license agreement under which this software is released              |
| can be found at http://www.deskpro.com/license                           |
|                                                                          |
| By using this software, you acknowledge having read the license          |
| and agree to be bound thereby.                                           |
|                                                                          |
| Please note that DeskPRO is not free software. We release the full       |
| source code for our software because we trust our users to pay us for    |
| the huge investment in time and energy that has gone into both creating  |
| this software and supporting our customers. By providing the source code |
| we preserve our customers' ability to modify, audit and learn from our   |
| work. We have been developing DeskPRO since 2001, please help us make it |
| another decade.                                                          |
|                                                                          |
| Like the work you see? Think you could make it better? We are always     |
| looking for great developers to join us: http://www.deskpro.com/jobs/    |
|                                                                          |
| ~ Thanks, Everyone at Team DeskPRO                                       |
\**************************************************************************/

if (php_sapi_name() !== 'cli') die('CLI only');

ini_set('display_errors', true);

chdir(__DIR__);

require_once 'PHP/LexerGenerator.php';
$a = new PHP_LexerGenerator('Lexer.plex');

$contents = file_get_contents('Lexer.php');
//$contents = preg_replace('#(throw new\s+)(Exception)#i', '$1\\\\$2', $contents);
$contents = preg_replace_callback(
	'#(' . preg_quote('$yy_global_pattern = \'') . ')(.*)' . '(\';)#siU',
	function($match) {
		return $match[1] . str_replace("'", "\\'", $match[2]) . $match[3];
	},
	$contents
);
file_put_contents('Lexer.php', $contents);

echo 'Lexer build complete.' . PHP_EOL;