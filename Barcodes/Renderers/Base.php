<?php

namespace Barcodes\Renderers;

class Base {

	protected $image;
	protected $config;
	protected $code;
	protected $widths;

	public function configure($config)
	{
		$this->widths = array_values($config['widths']);
		$this->config = $config;
	}

	public function create_image($code, $path = NULL, $type = "PNG")
	{
		$this->code = $code;

		list($width, $height, $x, $y, $w, $h) = $this->calculate_size_ext();
		$this->image = imagecreatetruecolor($width, $height);
		imagesavealpha($this->image, true);

		$this->allocate_colors();
		// once again with the allocated colors
		$this->configure($this->config);

		imagefill($this->image, 0, 0, $this->config["BackgroundColor"]);

		$this->render_image($x, $y, $w, $h);
		
		switch ($type){
			case "PNG":
				if (is_null($path)){
					header("Content-type: image/png");
				}
				imagepng($this->image, $path);
				break;
			case "JPG":
				if (is_null($path)){
					header("Content-type: image/jpeg");
				}
				imagejpeg($this->image, $path, $quality = 90);
				break;
			case "GIF":
				if (is_null($path)){
					header("Content-type: image/gif");
				}
				imagegif($this->image, $path);
				break;
		}

		imagedestroy($this->image);
	}

	public function use_image($image, $code, $pX = NULL, $pY = NULL)
	{
		$this->code = $code;
		$this->image = $image;

		list($width, $height, $x, $y, $w, $h) = $this->calculate_size_ext();

		$this->allocate_colors();

		$this->configure($this->config);
		
		if (!is_null($pX) && !is_null($pY)){
			$x = $pX;
			$y = $pY;
		}

		$this->render_image($x, $y, $w, $h);
	}

	private function calculate_size_ext()
	{
		$size = $this->calculate_size();

		$left = $this->config['padding']['Left'];
		$top = $this->config['padding']['Top'];
		$dwidth  = ceil($size[0] * $this->config['scale']['Horizontal']) + $left + $this->config['padding']['Right'];
		$dheight = ceil($size[1] * $this->config['scale']['Vertial']) + $top + $this->config['padding']['Bottom'];
		$iwidth  = (!is_null($this->config['Width']))  ? $this->config['Width']  : $dwidth;
		$iheight = (!is_null($this->config['Height'])) ? $this->config['Height'] : $dheight;
		$swidth  = $iwidth - $left - $this->config['padding']['Right'];
		$sheight = $iheight - $top - $this->config['padding']['Bottom'];

		return [$iwidth, $iheight, $left, $top, $swidth, $sheight];
	}

	private function allocate_colors()
	{
		// label
		$this->config["label"]['Color'] = $this->allocate_color($this->config["label"]['Color']);

		// bgcolor
		$this->config["BackgroundColor"] = $this->allocate_color($this->config["BackgroundColor"]);

		// palette
		foreach ($this->config["palette"] as $i => $color) {
			$this->config["palette"][$i] = $this->allocate_color($color);
		}
	}

	private function allocate_color(\Barcodes\BarColor $c)
	{
		return imagecolorallocatealpha($this->image, $c->R, $c->G, $c->B, $c->Alpha);
	}
}

?>