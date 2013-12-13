<?php

$type = !empty($_REQUEST['type']) ? (string)$_REQUEST['type'] : 'text';

switch ($type) {
	case 'json':
		header('Content-Type: application/json');
		echo json_encode(array('response' => 'pong'));
		break;
	case 'jsonp':
		$callback = !empty($_REQUEST['jsonp']) ? (string)$_REQUEST['jsonp'] : null;
		if (!$callback) {
			$callback = !empty($_REQUEST['callback']) ? (string)$_REQUEST['callback'] : null;
		}

		$callback = preg_replace('#[^a-zA-Z0-9_]#', '', $callback);
		if (!$callback) {
			$callback = 'callback';
		}

		header('Content-Type: text/javascript');
		echo $callback . '(' . json_encode(array('response' => 'pong')) . ');';
		break;
	default:
		header('Content-Type: text/plain');
		echo "pong";
		break;
}