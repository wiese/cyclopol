<?php
declare( strict_types = 1 );

namespace Cyclopol\DataModel;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ArticleAddress implements Address {

	/**
	 * Can't be typed as managed by doctrine through reflection
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="Cyclopol\DataModel\Article", inversedBy="addresses")
	 */
	private Article $article;

	/**
	 * @ORM\Column(type="integer")
	 */
	private int $streetNameAnalyserVersion;

	/** @ORM\Column(type="string", length=255) */
	private string $country;

	/** @ORM\Column(type="string", length=255) */
	private string $city;

	/** @ORM\Column(type="string", nullable=true, length=255) */
	private ?string $district;

	/** @ORM\Column(type="string", length=255) */
	private string $street;

	/** @ORM\Column(type="string", nullable=true, length=255) */
	private ?string $number;

	/**
	 * @ORM\OneToOne(targetEntity="Cyclopol\DataModel\Coordinate", cascade={"persist"})
	 */
	private ?Coordinate $coordinate = null;

	/** @ORM\Column(type="integer") */
	private int $geoCodingAttempts = 0;

	public function __construct(
		Article $article,
		int $streetNameAnalyserVersion,

		string $country,
		string $city,
		?string $district,
		string $street,
		?string $number = null
	) {
		$this->article = $article;
		$this->streetNameAnalyserVersion = $streetNameAnalyserVersion;

		$this->country = $country;
		$this->city = $city;
		$this->district = $district;
		$this->street = $street;
		$this->number = $number;
	}

	public function getId(): ?int {
		return $this->id;
	}

	public function __toString(): string {
		return trim( $this->street . ' ' . $this->number );
	}

	public function getCountry(): string {
		return $this->country;
	}

	public function getCity(): string {
		return $this->city;
	}

	public function getDistrict(): ?string {
		return $this->district;
	}

	public function getStreet(): string {
		return $this->street;
	}

	public function getNumber(): ?string {
		return $this->number;
	}

	public function hasNumber(): bool {
		return $this->number !== null;
	}

	public function getCoordinate(): ?Coordinate {
		return $this->coordinate;
	}

	public function setCoordinate( Coordinate $coordinate ) {
		$this->coordinate = $coordinate;
	}

	public function incrementGeoCodingAttempts() {
		$this->geoCodingAttempts++;
	}
}
