<?php
declare( strict_types = 1 );

namespace Cyclopol\DataModel;

use DateTimeInterface;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Article {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id; // can't be typed to prevent "typed property id must not be accessed before initialization"

    /**
     * @ORM\Column(type="string", unique=true, length=255)
     */
    private string $link;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $reportId;

	/**
	 * TODO normalize
	 * @ORM\Column(type="simple_array", nullable=true)
	 */
	private array $previousReportIds;

	/**
     * @ORM\Column(type="string", length=1024)
     */
	private string $title;

	/**
     * @ORM\Column(type="text")
     */
	private string $text;

	/**
     * @ORM\Column(type="datetime")
     */
	private DateTimeInterface $date;

	/**
	 * TODO consider processing this. is mixed, often comma separated districts
     * @ORM\Column(type="string", length=255)
     */
	private string $districts;

	/**
     * @ORM\Column(type="datetime")
     */
	private DateTimeInterface $createdAt;

	public function __construct(
		string $link,
		?int $reportId,
	    array $previousReportIds,
		string $title,
		string $text,
		DateTimeInterface $date,
	    string $districts
	) {
		$this->link = $link;
		$this->reportId = $reportId;
		$this->previousReportIds = $previousReportIds;
		$this->title = $title;
		$this->text = $text;
		$this->date = $date;
		$this->districts = $districts;

		$this->createdAt = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
	}

	public function getLink(): string {
	    return $this->link;
	}

	public function getReportId(): ?int {
	    return $this->reportId;
	}

	public function getTitle(): string {
	    return $this->title;
	}

	public function getText(): string {
	    return $this->text;
	}

	public function getDistricts(): string {
	    return $this->districts;
	}
}
