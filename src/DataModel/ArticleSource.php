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

	/** @ORM\Column(type="string", unique=true, length=255) */
	private string $link;

	/** @ORM\Column(type="text") */
	private string $source;

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
