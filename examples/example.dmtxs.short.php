<?php

require_once("bootstrap.php");

use Barcodes\{
	Barcodes,
	BarColor,
	Encoders\DMTX
};

$generator = new Barcodes("dmtxs");
$data = 8675309;
$opts = [
	"BackgroundColor" => (new BarColor())->fromHex("#FF00FF"),
	"palette" => [
			0 => new BarColor(255), 	// CS - Color of spaces
			1 => new BarColor(0) 		// CM - Color of modules
		],
	"widths" => [
		'QuietArea' => 4
	]
];

$generator->render($data, $opts, "temp/example_dmtxs_short.png");

?>