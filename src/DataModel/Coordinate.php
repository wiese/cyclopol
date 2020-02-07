<?php
declare( strict_types = 1 );

namespace Cyclopol\DataModel;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Cyclopol\DataAccess\CoordinateRepository")
 */
class Coordinate {
	/**
	 * Can't be typed as managed by doctrine through reflection
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	private $id;

	/** @ORM\Column(type="string", length=255) */
	private string $name;

	/**
	 * @ORM\Column(type="float")
	 */
	private float $lat;

	/**
	 * @ORM\Column(type="float")
	 */
	private float $lon;

	public function __construct( string $name, float $lat, float $lon ) {
		$this->name = $name;
		$this->lat = $lat;
		$this->lon = $lon;
	}

	public function __clone() {
		$this->id = null;
	}

	public function __toString(): string {
		return $this->name;
	}

	public function getName(): string {
		return $this->name;
	}

	public function getLat(): float {
		return $this->lat;
	}

	public function getLon(): float {
		return $this->lon;
	}
}
