<?php
declare( strict_types = 1 );

namespace Cyclopol\DataModel;

use DateTimeInterface;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Cyclopol\DataAccess\ArticleSourceRepository")
 */
class ArticleSource {

    /** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
    private $id; // can't be typed to prevent "typed property id must not be accessed before initialization"

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
