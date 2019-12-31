<?php
declare( strict_types = 1 );

namespace Cyclopol\TextAnalysis;

class StreetAddress {
	private string $street;
	private ?string $number;

	public function __construct( string $street, ?string $number = null ) {
		$this->street = $street;
		$this->number = $number;
	}

	/**
	 * (Ab)used to deduplicate StreetAddresses in StreetNameAnalyser
	 */
	public function __toString(): string {
		return trim( $this->street . ' ' . $this->number );
	}
}

