<?php

namespace Barcodes\Renderers;

class Matrix extends Base {

	private $wh;

	public function calculate_size()
	{
		$width = (
			$this->widths[0] +
			$this->code['width'] * $this->widths[1] +
			$this->widths[0]
		);
		$height = (
			$this->widths[0] +
			$this->code['height'] * $this->widths[1] +
			$this->widths[0]
		);
		return [$width, $height];
	}

	public function render_image($x, $y, $w, $h)
	{
		list($width, $height) = $this->calculate_size();

		if ($width && $height) {
			$scale = min($w / $width, $h / $height);
			$scale = (($scale > 1) ? floor($scale) : 1);
			$x = floor($x + ($w - $width * $scale) / 2);
			$y = floor($y + ($h - $height * $scale) / 2);
		} else {
			$scale = 1;
			$x = floor($x + $w / 2);
			$y = floor($y + $h / 2);
		}

		$x += $this->widths[0] * $scale;
		$y += $this->widths[0] * $scale;
		$wh = $this->widths[1] * $scale;

		$md = $this->config['mm']['Density'];

		$this->wh = ceil($wh * $md);
		if ($this->config['mm']['Shape'] == 'r'){
			$md = 0;
		}

		$offset = (1 - $md) * $this->wh / 2;

		foreach ($this->code['matrix'] as $by => $row) {
			$y1 = floor($y + $by * $wh + $offset);
			foreach ($row as $bx => $color) {
				$x1 = floor($x + $bx * $wh + $offset);
				$this->matrix_dot_image($x1, $y1, $this->config['palette'][$color]);
			}
		}
	}

	private function matrix_dot_image($x, $y, $mc)
	{
		$offwh = $this->wh - 1;

		switch ($this->config['mm']['Shape']) {
			default:
				imagefilledrectangle($this->image, $x, $y, $x+$offwh, $y+$offwh, $mc);
				break;
			case 'r':
				imagefilledellipse($this->image, $x, $y, $offwh + 1, $offwh + 1, $mc);
				break;
			case 'x':
				imageline($this->image, $x, $y, $x+$offwh, $y+$offwh, $mc);
				imageline($this->image, $x, $y+$offwh, $x+$offwh, $y, $mc);
				break;
		}
	}

}

?>