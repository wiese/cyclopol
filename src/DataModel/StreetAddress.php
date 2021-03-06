<?php
declare( strict_types = 1 );

namespace Cyclopol\DataModel;

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

	public function getStreet() {
		return $this->street;
	}

	public function getNumber() {
		return $this->number;
	}

	public function hasNumber(): bool {
		return $this->number !== null;
	}
}
