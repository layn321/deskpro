<?php if (!defined('DP_ROOT')) exit('No access');

require DP_ROOT.'/vendor/symfony/src/Symfony/Component/HttpFoundation/Request.php';
require DP_ROOT.'/vendor/symfony/src/Symfony/Component/HttpFoundation/ParameterBag.php';
require DP_ROOT.'/vendor/symfony/src/Symfony/Component/HttpFoundation/ServerBag.php';
require DP_ROOT.'/vendor/symfony/src/Symfony/Component/HttpFoundation/HeaderBag.php';
require DP_ROOT.'/vendor/symfony/src/Symfony/Component/HttpFoundation/FileBag.php';

$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
$pathinfo = $request->getPathInfo();

if (strpos($pathinfo, '/__checkurlrewrite/') !== 0) {
	echo "dp_check_invalid_path_info";
} else {
	echo "dp_check_okay";
}
