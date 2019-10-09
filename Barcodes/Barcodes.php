<?php

namespace Barcodes;

use Barcodes\BarColor;
use Barcodes\BarException;
use Barcodes\Renderers\Linear;
use Barcodes\Renderers\Matrix;

class Barcodes {

	private $symbology;

	function __construct(string $symbology)
	{
		$this->symbology = $symbology;
	}

	private function isDataMatrix()
	{
		return (substr($this->symbology, 0, 4) == "dmtx");
	}

	private function parse_opts($opts)
	{
		$config = [];

		if ($this->isDataMatrix()){
			$config['scale']['Factor'] = 4;
			$config['padding']['All'] = 0;
		} else {
			$config['scale']['Factor'] = 1;
			$config['padding']['All'] = 10;
		}

		// label
		$config["label"] = ['Height' => 10, 'Size' => 1, 'Color' => new BarColor(0), 'Skip' => FALSE, 'TTF' => NULL, 'Offset' => 0];

		if (isset($opts['label'])){
			$config["label"] = array_replace($config["label"], $opts['label']);
		}

		// bgcolor
		$config["BackgroundColor"] = (isset($opts['BackgroundColor'])) ? $opts['BackgroundColor'] : new BarColor(255);

		// palette
		$config["palette"] = [
			0 => new BarColor(255), 		// CS - Color of spaces
			1 => new BarColor(0), 			// CM - Color of modules
			2 => new BarColor(255,0, 0), 	// C2
			3 => new BarColor(255,255, 0),	// C3
			4 => new BarColor(0,255, 0),	// C4
			5 => new BarColor(0,255, 255),	// C5
			6 => new BarColor(0,0, 255),	// C6
			7 => new BarColor(255,0, 255),	// C7
			8 => new BarColor(255),			// C8
			9 => new BarColor(0)			// C9
		];

		if (isset($opts['palette'])){
			$config["palette"] = array_replace($config["palette"], $opts['palette']);
		}

		// padding
		if (isset($opts['padding']['All'])) {
			$config['padding']['All'] = (int)$opts['padding']['All'];
		}
		$config['padding']['Horizontal'] = (isset($opts['padding']['Horizontal']) ? (int)$opts['padding']['Horizontal'] : $config['padding']['All']);
		$config['padding']['Vertial']	 = (isset($opts['padding']['Vertial']) 	  ? (int)$opts['padding']['Vertial'] 	: $config['padding']['All']);
		$config['padding']['Top'] 		 = (isset($opts['padding']['Top']) 	 	  ? (int)$opts['padding']['Top'] 		: $config['padding']['Vertial']);
		$config['padding']['Bottom']  	 = (isset($opts['padding']['Bottom']) 	  ? (int)$opts['padding']['Bottom']  	: $config['padding']['Vertial']);
		$config['padding']['Right'] 	 = (isset($opts['padding']['Right']) 	  ? (int)$opts['padding']['Right'] 		: $config['padding']['Horizontal']);
		$config['padding']['Left']  	 = (isset($opts['padding']['Left'])  	  ? (int)$opts['padding']['Left']  		: $config['padding']['Horizontal']);

		// widths
		$config['widths'] = [
			'QuietArea' 	=> 1,
			'NarrowModules' => 1,
			'WideModules' 	=> 3,
			'NarrowSpace' 	=> 1,
			'w4' => 1,
			'w5' => 1,
			'w6' => 1,
			'w7' => 1,
			'w8' => 1,
			'w9' => 1
		];

		if (isset($opts['widths'])){
			$config['widths'] = array_replace($config['widths'], $opts['widths']);
		}

		// scale
		if (isset($opts['scale']['Factor'])) {
			$config['scale']['Factor'] = (float)$opts['scale']['Factor'];
		}
		$config['scale']['Horizontal'] = (isset($opts['scale']['Horizontal']) ? (float)$opts['scale']['Horizontal'] : $config["scale"]['Factor']);
		$config['scale']['Vertial']	 = (isset($opts['scale']['Vertial']) 	? (float)$opts['scale']['Vertial'] 	  : $config["scale"]['Factor']);

		// matrix modules
		$config['modules']['Shape']   = (isset($opts['modules']['Shape'])   ? strtolower($opts['modules']['Shape']) : '');
		$config['modules']['Density'] = (isset($opts['modules']['Density']) ? (float)$opts['modules']['Density'] : 1);

		// dimentions
		$config['Width']  = (isset($opts['Width'])  ? (int)$opts['Width']  : NULL);
		$config['Height'] = (isset($opts['Height']) ? (int)$opts['Height'] : NULL);

		// rotation (pChart only)
		$config['Angle'] = (isset($opts['Angle']) ? (int)$opts['Angle'] : NULL);

		return $config;
	}

	public function render($data, array $opts = [], $path)
	{
		if ($this->isDataMatrix()){
			$renderer = new Matrix();
		} else {
			$renderer = new Linear();
		}

		$code = $this->encode($data);
		$renderer->configure($this->parse_opts($opts));
		$renderer->create_image($code, $path);
	}

	public function forPChart(\pChart\pDraw $MyPicture, $data, array $opts = [], $X = NULL, $Y = NULL)
	{
		if ($this->isDataMatrix()){
			$renderer = new Matrix();
		} else {
			$renderer = new Linear();
		}

		$code = $this->encode($data);
		$renderer->configure($this->parse_opts($opts));
		$renderer->use_image($MyPicture->gettheImage(), $code, $X, $Y);
	}

	private function encode($data)
	{
		switch ($this->symbology) {
			case 'upca'       : return (new Encoders\UPC)->upc_a_encode($data);
			case 'upce'       : return (new Encoders\UPC)->upc_e_encode($data);
			case 'ean13nopad' : return (new Encoders\UPC)->ean_13_encode($data, ' ');
			case 'ean13pad'   : return (new Encoders\UPC)->ean_13_encode($data, '>');
			case 'ean13'      : return (new Encoders\UPC)->ean_13_encode($data, '>');
			case 'ean8'       : return (new Encoders\UPC)->ean_8_encode($data);
			case 'code39'     : return (new Encoders\Codes)->code_39_encode($data);
			case 'code39ascii': return (new Encoders\Codes)->code_39_ascii_encode($data);
			case 'code93'     : return (new Encoders\Codes)->code_93_encode($data);
			case 'code93ascii': return (new Encoders\Codes)->code_93_ascii_encode($data);
			case 'code128'    : return (new Encoders\Codes)->code_128_encode($data, 0, false);
			case 'code128a'   : return (new Encoders\Codes)->code_128_encode($data, 1, false);
			case 'code128b'   : return (new Encoders\Codes)->code_128_encode($data, 2, false);
			case 'code128c'   : return (new Encoders\Codes)->code_128_encode($data, 3, false);
			case 'code128ac'  : return (new Encoders\Codes)->code_128_encode($data,-1, false);
			case 'code128bc'  : return (new Encoders\Codes)->code_128_encode($data,-2, false);
			case 'ean128'     : return (new Encoders\Codes)->code_128_encode($data, 0, true);
			case 'ean128a'    : return (new Encoders\Codes)->code_128_encode($data, 1, true);
			case 'ean128b'    : return (new Encoders\Codes)->code_128_encode($data, 2, true);
			case 'ean128c'    : return (new Encoders\Codes)->code_128_encode($data, 3, true);
			case 'ean128ac'   : return (new Encoders\Codes)->code_128_encode($data,-1, true);
			case 'ean128bc'   : return (new Encoders\Codes)->code_128_encode($data,-2, true);
			case 'codabar'    : return (new Encoders\Codes)->codabar_encode($data);
			case 'itf'        : return (new Encoders\ITF())->itf_encode($data);
			case 'itf14'      : return (new Encoders\ITF())->itf_encode($data);
			case 'dmtx'       : return (new Encoders\DMTX())->dmtx_encode($data, false, false);
			case 'dmtxs'      : return (new Encoders\DMTX())->dmtx_encode($data, false, false);
			case 'dmtxr'      : return (new Encoders\DMTX())->dmtx_encode($data, true,  false);
			case 'dmtxgs1'    : return (new Encoders\DMTX())->dmtx_encode($data, false, true);
			case 'dmtxsgs1'   : return (new Encoders\DMTX())->dmtx_encode($data, false, true);
			case 'dmtxrgs1'   : return (new Encoders\DMTX())->dmtx_encode($data, true,  true);
			default: throw BarException::Std("Unknown encode method - ".$this->symbology);
		}
	}

}

?>