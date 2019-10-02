<?php

namespace Barcodes;

use Barcodes\BarColor;
use Barcodes\BarException;
use Barcodes\Renderers\Linear;
use Barcodes\Renderers\Matrix;

class Barcodes {

	private $renderer;
	private $symbology;
	private $config = [];

	function __construct(string $symbology)
	{
		$this->symbology = $symbology;

		if (substr($symbology, 0, 4) == "dmtx"){
			$this->renderer = new Matrix();
			$this->config['scale']['Factor'] = 4;
			$this->config['padding']['All'] = 0;
		} else {
			$this->renderer = new Linear();
			$this->config['scale']['Factor'] = 1;
			$this->config['padding']['All'] = 10;
		}
	}

	private function parse_opts($opts)
	{
		// label
		$this->config["label"] = ['Height' => 10, 'Size' => 1, 'Color' => new BarColor(0), 'Skip' => FALSE, 'TTF' => NULL, 'Offset' => 0];

		if (isset($opts['label'])){
			$this->config["label"] = array_replace($this->config["label"], $opts['label']);
		}

		// bgcolor
		$this->config["BackgroundColor"] = (isset($opts['BackgroundColor'])) ? $opts['BackgroundColor'] : new BarColor(255);

		// palette
		$this->config["palette"] = [
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
			$this->config["palette"] = array_replace($this->config["palette"], $opts['palette']);
		}

		// padding
		if (isset($opts['padding']['All'])) {
			$this->config['padding']['All'] = (int)$opts['padding']['All'];
		}
		$this->config['padding']['Horizontal'] 	= (isset($opts['padding']['Horizontal']) ? (int)$opts['padding']['Horizontal'] 	: $this->config['padding']['All']);
		$this->config['padding']['Vertial']	 	= (isset($opts['padding']['Vertial']) 	 ? (int)$opts['padding']['Vertial'] 	: $this->config['padding']['All']);
		$this->config['padding']['Top'] 		= (isset($opts['padding']['Top']) 	 	 ? (int)$opts['padding']['Top'] 		: $this->config['padding']['Vertial']);
		$this->config['padding']['Bottom']  	= (isset($opts['padding']['Bottom']) 	 ? (int)$opts['padding']['Bottom']  	: $this->config['padding']['Vertial']);
		$this->config['padding']['Right'] 		= (isset($opts['padding']['Right']) 	 ? (int)$opts['padding']['Right'] 		: $this->config['padding']['Horizontal']);
		$this->config['padding']['Left']  		= (isset($opts['padding']['Left'])  	 ? (int)$opts['padding']['Left']  		: $this->config['padding']['Horizontal']);

		// widths
		$this->config['widths'] = [
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
			$this->config['widths'] = array_replace($this->config['widths'], $opts['widths']);
		}

		// scale
		if (isset($opts['scale']['Factor'])) {
			$this->config['scale']['Factor'] = (float)$opts['scale']['Factor'];
		}
		$this->config['scale']['Horizontal'] = (isset($opts['scale']['Horizontal']) ? (float)$opts['scale']['Horizontal'] : $this->config["scale"]['Factor']);
		$this->config['scale']['Vertial']	 = (isset($opts['scale']['Vertial']) 	? (float)$opts['scale']['Vertial'] 	  : $this->config["scale"]['Factor']);

		// matrix modules
		$this->config['modules']['Shape']   = (isset($opts['modules']['Shape'])   ? strtolower($opts['modules']['Shape']) : '');
		$this->config['modules']['Density'] = (isset($opts['modules']['Density']) ? (float)$opts['modules']['Density'] : 1);

		// dimentions
		$this->config['Width']  = (isset($opts['Width'])  ? (int)$opts['Width']  : NULL);
		$this->config['Height'] = (isset($opts['Height']) ? (int)$opts['Height'] : NULL);

		// rotation (pChart only)
		$this->config['Angle'] = (isset($opts['Angle']) ? (int)$opts['Angle'] : NULL);
	}

	public function render($data, array $opts = [], $path)
	{
		$this->parse_opts($opts);

		$code = $this->encode($data);

		$this->renderer->configure($this->config);
		$this->renderer->create_image($code, $path);
	}

	public function forPChart(\pChart\pDraw $MyPicture, $data, array $opts = [], $X = NULL, $Y = NULL)
	{
		$this->parse_opts($opts);

		$code = $this->encode($data);

		$this->renderer->configure($this->config);
		$this->renderer->use_image($MyPicture->gettheImage(), $code, $X, $Y);
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
			case 'code128'    : return (new Encoders\Codes)->code_128_encode($data, 0,false);
			case 'code128a'   : return (new Encoders\Codes)->code_128_encode($data, 1,false);
			case 'code128b'   : return (new Encoders\Codes)->code_128_encode($data, 2,false);
			case 'code128c'   : return (new Encoders\Codes)->code_128_encode($data, 3,false);
			case 'code128ac'  : return (new Encoders\Codes)->code_128_encode($data,-1,false);
			case 'code128bc'  : return (new Encoders\Codes)->code_128_encode($data,-2,false);
			case 'ean128'     : return (new Encoders\Codes)->code_128_encode($data, 0, true);
			case 'ean128a'    : return (new Encoders\Codes)->code_128_encode($data, 1, true);
			case 'ean128b'    : return (new Encoders\Codes)->code_128_encode($data, 2, true);
			case 'ean128c'    : return (new Encoders\Codes)->code_128_encode($data, 3, true);
			case 'ean128ac'   : return (new Encoders\Codes)->code_128_encode($data,-1, true);
			case 'ean128bc'   : return (new Encoders\Codes)->code_128_encode($data,-2, true);
			case 'codabar'    : return (new Encoders\Codes)->codabar_encode($data);
			case 'itf'        : return (new Encoders\ITF())->itf_encode($data);
			case 'itf14'      : return (new Encoders\ITF())->itf_encode($data);
			case 'dmtx'       : return (new Encoders\DMTX())->dmtx_encode($data,false,false);
			case 'dmtxs'      : return (new Encoders\DMTX())->dmtx_encode($data,false,false);
			case 'dmtxr'      : return (new Encoders\DMTX())->dmtx_encode($data, true,false);
			case 'dmtxgs1'    : return (new Encoders\DMTX())->dmtx_encode($data,false, true);
			case 'dmtxsgs1'   : return (new Encoders\DMTX())->dmtx_encode($data,false, true);
			case 'dmtxrgs1'   : return (new Encoders\DMTX())->dmtx_encode($data, true, true);
			default: throw BarException::Std("Unknown encode method - ".$this->symbology);
		}
	}

}

?>