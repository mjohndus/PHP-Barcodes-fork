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
		imagefill($this->image, 0, 0, $this->allocate_color($this->config["BackgroundColor"]));

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

		list($width, $height, $x, $y, $w, $h) = $this->calculate_size_ext();

		if (!is_null($this->config["Angle"])){
			$width = max($width,$height);
			$this->image = imagecreatetruecolor($width, $width);
			imagealphablending($this->image, FALSE);
			$trans = imagecolorallocatealpha($this->image, 255, 255, 255, 127);
			imagefilledrectangle($this->image, 0, 0, $width, $width, $trans);
			imagealphablending($this->image, TRUE);
			imagesavealpha($this->image, TRUE);
		} else {
			$this->image = $image;
			if (!is_null($pX) && !is_null($pY)){
				$x = $pX;
				$y = $pY;
			}
		}

		$this->render_image($x, $y, $w, $h);

		if (!is_null($this->config["Angle"])){
			$rotate = imagerotate($this->image, $this->config["Angle"], $trans);
			$cropped = imagecropauto($rotate, IMG_CROP_SIDES, $trans);
			imagecopy($image, $cropped, $pX, $pY, 0, 0, imagesx($cropped), imagesy($cropped));
			imagedestroy($rotate);
			imagedestroy($cropped);
			imagedestroy($this->image);
		}
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

	protected function allocate_color(\Barcodes\BarColor $c)
	{
		list ($R, $G, $B, $A) = $c->get();
		return imagecolorallocatealpha($this->image, $R, $G, $B, $A);
	}
}

?>