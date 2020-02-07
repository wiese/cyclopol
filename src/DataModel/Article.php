<?php
declare( strict_types = 1 );

namespace Cyclopol\DataModel;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(
 *       columns={ "articleSource_id", "articleCrawlerVersion" }
 *     )
 *   }
 * )
 * @ORM\Entity(repositoryClass="Cyclopol\DataAccess\ArticleRepository")
 */
class Article {

	/**
	 * Can't be typed as managed by doctrine through reflection
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="Cyclopol\DataModel\ArticleSource")
	 */
	private ArticleSource $articleSource;

	/**
	 * @ORM\Column(type="integer")
	 */
	private int $articleCrawlerVersion;

	/**
	 * @ORM\Column(type="string", length=255)
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

	/**
	 * @ORM\OneToMany(targetEntity="Cyclopol\DataModel\ArticleAddress", mappedBy="article")
	 */
	private $addresses;

	public function __construct(
		ArticleSource $articleSource,
		int $articleCrawlerVersion,
		string $link,
		?int $reportId,
		array $previousReportIds,
		string $title,
		string $text,
		DateTimeInterface $date,
		string $districts
	) {
		$this->articleSource = $articleSource;
		$this->articleCrawlerVersion = $articleCrawlerVersion;

		$this->link = $link;
		$this->reportId = $reportId;
		$this->previousReportIds = $previousReportIds;
		$this->title = $title;
		$this->text = $text;
		$this->date = $date;
		$this->districts = $districts;

		$this->createdAt = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
	}

	public function getArticleSource(): ArticleSource {
		return $this->articleSource;
	}

	public function getArticleCrawlerVersion(): int {
		return $this->articleCrawlerVersion;
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

	public function getAddresses() {
		return $this->addresses;
	}
}
