<?php
namespace Cyclopol\DataModel;

class Coordinates {
	private string $name;
	private float $lat;
	private float $lon;

	public function __construct( string $name, float $lat, float $lon ) {
		$this->name = $name;
		$this->lat = $lat;
		$this->lon = $lon;
	}

	public function __toString(): string {
		return $this->name;
	}
}
