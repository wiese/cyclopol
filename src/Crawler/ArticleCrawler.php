<?php
declare( strict_types = 1 );

namespace Cyclopol\Crawler;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Symfony\Component\DomCrawler\Crawler;

class ArticleCrawler {
	private $crawler;
	private $mainContent;
	public const DATE_FORMAT = 'Y-m-d H:i';

	public function __construct( string $dom ) {
		$this->crawler = new Crawler( $dom );
		$this->mainContent = $this->crawler->filter( '.column-content' );
	}

	public function getText(): string {
		return $this->mainContent->filter( '.body .textile' )->text();
	}

	public function getTitle(): string {
		return $this->mainContent->filter( 'h1.title' )->text();
	}

	public function getId(): ?int {
		return array_shift( $this->getReportIds() );
	}

	/**
	 * @return int[]
	 */
	public function getPreviousIds(): array {
		$ids = $this->getReportIds();
		array_shift( $ids );
		return $ids;
	}

	public function getTime(): DateTimeInterface {
		return DateTimeImmutable::createFromFormat(
			'!' . self::DATE_FORMAT,
			$this->crawler->filter( 'head meta[name="dcterms.submitted"]' )->attr( 'content' ),
			new DateTimeZone( 'Europe/Berlin' )
		);
	}

	public function getCategories(): string {
		return $this->mainContent->filter( '.body .polizeimeldung' )->eq( 1 )->text();
	}

	private function getTextLines(): array {
		return explode( '\n', $this->getText() );
	}

	private function getReportIds(): array {
		if ( preg_match_all( '/Nr\.\s?(\d+)/', $this->getText(), $matches ) ) {
			return array_map ( 'intval', $matches[ 1 ] );
		}
		return [];
	}
}

