<?php
declare( strict_types = 1 );

namespace Cyclopol\DataModel;

interface Address {

	public function getCountry(): string;

	public function getCity(): string;

	public function getDistrict(): ?string;

	public function hasNumber(): bool;
}
