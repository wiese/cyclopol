<?php
declare( strict_types = 1 );

namespace Cyclopol\TextAnalysis;

class StreetAddress {
	private $street;
	private $number;

	public function __construct( string $street, ?string $number = null ) {
		$this->street = $street;
		$this->number = $number;
	}

	public function __toString() {
		return trim( $this->street . ' ' . $this->number );
	}
}

