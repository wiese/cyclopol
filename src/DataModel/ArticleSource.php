<?php
declare( strict_types = 1 );

namespace Cyclopol\DataModel;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Cyclopol\DataAccess\ArticleSourceRepository")
 */
class ArticleSource {

	/**
	 * Can't be typed as managed by doctrine through reflection
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	private $id;

	// TODO this unique is wrong. It _may_ be correct when combined with listingDate
	/** @ORM\Column(type="string", length=255) */
	private string $link;

	/** @ORM\Column(type="text") */
	private string $source;

	/** @ORM\Column(type="datetime") */
	private DateTimeInterface $listingDate;

	/** @ORM\Column(type="datetime") */
	private DateTimeInterface $downloadedAt;

	public function __construct(
		string $link,
		string $source
	) {
		$this->link = $link;
		$this->source = $source;
		$this->downloadedAt = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
	}

	/**
	 * Doctrine actually stores this (w/o TZ) in a string formatted per the TZ of this object.
	 * UTC would arguably be superior but then lookups would also need to happen in UTC.
	 * A headache: what happens if there was a change of TZ between listingDate and download?
	 * https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/cookbook/working-with-datetime.html#handling-different-timezones-with-the-datetime-type
	 */
	public function setListingDate( DateTimeInterface $listingDate ) {
		$this->listingDate = $listingDate;
	}

	public function getId(): int {
		return $this->id;
	}

	public function getLink(): string {
		return $this->link;
	}

	public function getSource(): string {
		return $this->source;
	}

	public function getDownloadedAt(): DateTimeImmutable {
		return $this->downloadedAt;
	}
}
