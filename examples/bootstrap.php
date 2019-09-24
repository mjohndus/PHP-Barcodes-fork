<?php

if (PHP_MAJOR_VERSION < 7){
	die("PHP Barcodes only works for PHP 7+");
}
	
spl_autoload_register(function ($class_name) {
	$filename = str_replace('\\', DIRECTORY_SEPARATOR, $class_name) . '.php';
	include $filename;
});

?>