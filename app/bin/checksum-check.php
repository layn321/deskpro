<?php
define('DP_ROOT', realpath(dirname(__FILE__) . '/../'));

require_once DP_ROOT.'/vendor/symfony/src/Symfony/Component/Finder/Finder.php';
require_once DP_ROOT.'/src/Application/DeskPRO/Distribution/VerifyChecksums.php';

$t_start = microtime(true);
$verify = new \Application\DeskPRO\Distribution\VerifyChecksums(150);

echo "Will now check {$verify->countFiles()} files.\n\n";

for ($i = 0; $i < $verify->countChunks(); $i++) {
	$results = $verify->compareChunk($i);

	if ($results['added']) {
		foreach ($results['added'] as $f) {
			echo "\n[UNKNOWN] $f";
		}
	}
	if ($results['removed']) {
		foreach ($results['removed'] as $f) {
			echo "\n[MISSING] $f";
		}
	}
	if ($results['changed']) {
		foreach ($results['changed'] as $f) {
			echo "\n[INVALID] $f";
		}
	}

	if (!$results['added'] && !$results['removed'] && !$results['changed']) {
		echo ".";
	} else {
		echo "\n";
	}
}

echo "\n\n";
printf("Done in %.3fs", microtime(true) - $t_start);