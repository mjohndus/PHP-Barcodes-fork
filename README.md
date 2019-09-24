# Barcodes
Based on the perfectly good code:
https://github.com/kreativekorp/barcode

### Generate barcodes from a single PHP file. MIT license.

  * Output to PNG, GIF or JPEG
  * Generates UPC-A, UPC-E, EAN-13, EAN-8, Code 39, Code 93, Code 128, Codabar, ITF and Data Matrix.

PHP Code (check out examples/ as well):

```
require_once("bootstrap.php");

use Barcodes\{
	Barcodes,
	BarColor,
	Encoders\DMTX,
	Encoders\Codabar,
	Encoders\Codes,
	Encoders\ITF,
	Encoders\UPC
};

// upca         code39         dmtx
// upce         code39-ascii   dmtx-s
// ean8         code93         dmtx-r
// ean13        code93-ascii   gs1dmtx
// ean13pad     code128        gs1dmtxs
// ean13nopad   codabar        gs1dmtxr
// ean128       itf

$generator = new Barcodes("dmtxs");
$data = 8675309;
$opts = [
	"BackgroundColor" => (new BarColor())->fromHex("#FFFFFF"), // OR
	"BackgroundColor" => new BarColor(255,255,255,100), // OR
	"BackgroundColor" => new BarColor(255),
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
		'QuietArea' => 4      // Width of quiet area units. Default is 1. Use 0 to suppress quiet area.
		'NarrowModules' => 1, // Width of narrow modules and spaces. Default is 1.
		'WideModules' 	=> 3, // Width of wide modules and spaces. 
								 Applies to Code 39, Codabar, and ITF only. Default is 3.
		'NarrowSpace' 	=> 1, // Width of narrow space between characters. 
								 Applies to Code 39 and Codabar only. Default is 1.
		'w4' => 1,
		'w5' => 1,
		'w6' => 1,
		'w7' => 1,
		'w8' => 1,
		'w9' => 1
		],
	"scale" => [
		'Factor' => 4,		// Scale factor. Default is 1 for linear barcodes or 4 for matrix barcodes.
		'Horizontal' => 1,	// Horizontal scale factor. Overrides `Factor`.
		'Vertical'   => 1	// Vertical scale factor. Overrides `Factor`.
		],
	"padding" => [
		'All' => 0, 	   // Padding. Default is 10 for linear barcodes or 0 for matrix barcodes.
		'Horizontal' => 0, // Left and right padding. Default is value of `All`.
		'Vertial' => 0,    // Top and bottom padding. Default is value of `All`.
		'Top' => 0, 	   // Top padding. Default is value of `Vertial`.
		'Bottom' => 0, 	   // Bottom padding. Default is value of `Vertial`.
		'Right' => 0, 	   // Right padding. Default is value of `Horizontal`.
		'Left' => 0 	   // Left padding. Default is value of `Horizontal`.
		],
	"label" => [
		'Height' => 10, // Distance from text baseline to bottom of modules. Default is 10.
						   Applies to linear barcodes only.
		'Size' => 1, 	// Text size. The GD library built-in font number from 1 to 5 and the default is 1.
		'Color' => new BarColor(0) // Text color in `#RRGGBB` or `R,G,B,A` format.
									  Applies to linear barcodes only.
		],
	"mm" => [
		'Shape' => '', // Module shape. One of: `s` for square, `r` for round, or `x` for X-shaped.
						  Default is `s`. Applies to matrix barcodes only.
		'Density' => 1 // Module density. A number between 0 and 1. Default is 1.
						  Applies to matrix barcodes only.
		],
	"Width" => 400, // Overrides scale factors
	"Height" => 400 // Overrides scale factors
];

$generator->render($data, $opts, "temp/example_dmtxs_short.png");
```