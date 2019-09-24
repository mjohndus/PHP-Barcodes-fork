<?php

namespace Barcodes;

class BarException extends \Exception
{
	public static function Std($text)
	{
		return new static(sprintf('Barcodes: %s', $text));
	}
}

?>