<?php

define('XIL_PATH', realpath(dirname(__FILE__).'/../src'));

function __xil_autoload( $className )
{
	$path = XIL_PATH . '/' . str_replace('\\','/',$className) . '.php';

	if (file_exists($path))
		require_once($path);
}

spl_autoload_register("__xil_autoload");
