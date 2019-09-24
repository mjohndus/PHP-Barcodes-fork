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
			0 => new BarColor(255), 		// CS - Color of spaces
			1 => new BarColor(0), 			// CM - Color of modules
			//2 => new BarColor(255,0, 0), 	// C2
			3 => new BarColor(255,255, 0),	// C3
			4 => new BarColor(0,255, 0),	// C4
			//5 => new BarColor(0,255, 255),// C5
			6 => new BarColor(0,0, 255),	// C6
			//7 => new BarColor(255,0, 255),// C7
			8 => new BarColor(255),			// C8
			9 => new BarColor(0)			// C9
		],
	"widths" => [
		'QuietArea' => 4
	]
];

$generator->render($data, $opts, "temp/example_dmtxs_short.png");

?>