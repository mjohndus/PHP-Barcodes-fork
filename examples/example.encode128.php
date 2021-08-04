<?php

require_once("bootstrap.php");

use Barcodes\{
	Barcodes,
	Encoders\Codes
};

$generator = new Barcodes("code128");
$data = "Do what you want !";

$generator->render($data, "temp/encode128.png", []);

?>