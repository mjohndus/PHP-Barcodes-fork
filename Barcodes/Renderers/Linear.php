<?php

namespace Barcodes\Renderers;

class Linear extends Base {

	public function calculate_size()
	{
		$width = 0;

		foreach ($this->code as $block){
			foreach ($block['m'] as $module){
				$width += $module[1] * $this->widths[$module[2]];
			}
		}

		return [$width, 80];
	}

	public function render_image($x, $y, $w, $h)
	{
		$width = 0;

		foreach ($this->code as $block){
			foreach ($block['m'] as $module) {
				$width += $module[1] * $this->widths[$module[2]];
			}
		}

		if ($width > 0) {
			$scale = $w / $width;
			$scale = (($scale > 1) ? floor($scale) : 1);
			$x = floor($x + ($w - $width * $scale) / 2);
		} else {
			$scale = 1;
			$x = floor($x + $w / 2);
		}

		foreach ($this->code as $block){

			if (isset($block['l'])) {
				$ly = (isset($block['l'][1]) ? (float)$block['l'][1] : 1);
				$lx = (isset($block['l'][2]) ? (float)$block['l'][2] : 0.5);
				$my = round($y + min($h, $h + ($ly - 1) * $this->config['label']['Height']));
				$ly = ($y + $h + $ly * $this->config['label']['Height']);
				$ly = round($ly - imagefontheight($this->config['label']['Size']));
			} else {
				$my = $y + $h;
			}

			$mx = $x;

			foreach ($block['m'] as $module){
				$mw = $mx + $module[1] * $this->widths[$module[2]] * $scale;
				imagefilledrectangle($this->image, $mx, $y, $mw - 1, $my - 1, $this->config['palette'][$module[0]]);
				$mx = $mw;
			}

			if (isset($block['l'])){
				$text = $block['l'][0];
				$lx = ($x + ($mx - $x) * $lx);
				$lw = imagefontwidth($this->config['label']['Size']) * strlen($text);
				$lx = round($lx - $lw / 2);
				imagestring($this->image, $this->config['label']['Size'], $lx, $ly, $text, $this->config['label']['Color']);
			}

			$x = $mx;
		}
	}

}

?>