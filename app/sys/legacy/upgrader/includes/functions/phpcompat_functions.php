<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id: phpcompat_functions.php 6619 2010-03-03 05:59:14Z chroder $
// +-------------------------------------------------------------+
// | File Details:
// | -
// +-------------------------------------------------------------+

// REQUIRED
// ---------------
// Available for PHP>=4.3.2, but ONLY when PHP was compiled
// with the bundled GD lib.
// - Creating this emtpy func is just a simple way to fix
//   fatal errors with jpgraph, which uses it
if (!function_exists('imageantialias')) {
	function imageantialias() {}
}