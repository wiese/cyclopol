<?php
declare( strict_types = 1 );

namespace Cyclopol\Crawler;

use Cyclopol\DataModel\ArticleTeaser;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Symfony\Component\DomCrawler\Crawler;

class ListingCrawler {
	private Crawler $crawler;
	private string $baseUri;
	public const DATE_FORMAT = 'd.m.Y H:i +';

	public function __construct( string $dom, string $baseUri ) {
		$this->crawler = new Crawler( $dom );
		$this->baseUri = $baseUri;
	}

	public function getArticleTeasers(): array {
		return $this->crawler->filter( '.list-autoteaser li' )->each( function ( Crawler $teaser ) {
			return new ArticleTeaser(
				$this->baseUri . $teaser->children( '.text a' )->attr( 'href' ),
				$this->dateFromText( $teaser->children( '.date' )->text() )
			);
		} );
	}

	private function dateFromText( string $text ): DateTimeImmutable {
		$listingDate = DateTimeImmutable::createFromFormat(
			'!' . self::DATE_FORMAT,
			$text,
			new DateTimeZone( 'Europe/Berlin' )
		);
		if ( !$listingDate ) {
			throw new Exception( 'Bad date input ' . $text );
		}
		return $listingDate;
	}
}
