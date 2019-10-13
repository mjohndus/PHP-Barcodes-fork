<?php

# PHP 7.4

declare(strict_types=1);

namespace configTest;

require_once("examples\bootstrap.php");

use Barcodes\BarColor;

class CFG {

	private $label;
	private $colors;

	function __construct()
	{
		$this->label = new class('label')
		{
			public int $w = 4;
			public ?string $text = "BLABLA";
		};

		$this->colors = new class('colors')
		{
			public \Barcodes\BarColor $c0;
			public \Barcodes\BarColor $c1;

			function __construct()
			{
				$this->c0 = new \Barcodes\BarColor(0);
				$this->c1 = new \Barcodes\BarColor(0);
			}
		};
	}

	private function mergeConfig($name, $myConfig)
	{
		try {
			foreach($myConfig as $property => $value) {
				$this->$name->$property = $value;
			}
		} catch (\TypeError $ex) {
			$msg = $ex->getMessage();
			$msg = str_replace("class@anonymous", $name, $msg);
			throw new \Exception("CFG:: ".$msg);
		}
	}

	public function alter(string $branch, array $myConfig)
	{
		if (isset($this->$branch)){
			$this->mergeConfig($branch, $myConfig);
		} else {
			throw new \Exception("CFG:: No such branch - $branch");
		}
	}

	public function get(string $branch, string $property)
	{
		if (isset($this->$branch)){
			if (isset($this->$branch->$property)){
				return $this->$branch->$property;
			} else {
				throw new \Exception("CFG:: No such property -> $property for branch -> $branch");
			}
		} else {
			throw new \Exception("CFG:: No such branch -> $branch");
		}
	}
}

$cfg = new CFG();
$cfg->alter('label', ["w" => 6, "text" => NULL]);

var_dump($cfg);

$cfg->alter('colors', ["c0" => new \Barcodes\BarColor(255)]);

var_dump($cfg);

var_dump($cfg->get('label', 'w'));

$cfg->alter('label', ["w" => "TEST", "text" => NULL]);
