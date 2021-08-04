<?php

require_once("bootstrap.php");

use Barcodes\{
	Barcodes,
	BarColor,
	Encoders\Codes
};

$generator = new Barcodes("code39");
$data = "12250000234502";

$opts = [
	"label" => [
		'Height'=> 10,
		'Size' 	=> 1,
		'Color' => (new BarColor())->fromHex("2AFF55")
	]
];

$generator->render($data, "temp/encode39.png", $opts);

?>