<?php
declare( strict_types = 1 );

namespace Cyclopol\Crawler;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Symfony\Component\DomCrawler\Crawler;

class ArticleCrawler {
	public const VERSION = 2;

	private Crawler $crawler;
	private Crawler $mainContent;
	public const DATE_FORMAT = 'Y-m-d H:i';

	public function __construct( string $dom ) {
		$this->crawler = new Crawler( $dom );
		$this->mainContent = $this->crawler->filter( '.column-content' );
	}

	public function getText(): string {
		$textile = $this->mainContent
			->filter( '.body .textile' )
			->reduce( function ( Crawler $node ) {
				$this->replaceChildBrNodesBySpace( $node );
			} );
		$contentElements = $textile->each( function ( $node ) {
			return trim( $node->text() );
		} );
		return implode( "\n", $contentElements );
	}

	public function getTitle(): string {
		return $this->mainContent->filter( 'h1.title' )->text();
	}

	/**
	 * TODO these seem to start from 0 every year
	 */
	public function getId(): ?int {
		$ids = $this->getReportIds();
		$id = array_shift( $ids );
		return $id;
	}

	/**
	 * FIXME "previousIds" can contain those from former years. how to overcome?
	 *
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

	private function getReportIds(): array {
		if ( preg_match_all( '/Nr\.\s?(\d+)/', $this->getText(), $matches ) ) {
			return array_map( 'intval', $matches[ 1 ] );
		}
		return [];
	}

	/**
	 * Ensure <br>s leave a whitespace when nodeValue/textContent is pulled
	 */
	private function replaceChildBrNodesBySpace( Crawler $node ): void {
		$rawNode = $node->getNode( 0 );
		foreach ( $rawNode->getElementsByTagName( 'br' ) as $br ) {
			$br->parentNode->replaceChild( $rawNode->ownerDocument->createTextNode( ' ' ), $br );
		}
	}
}
