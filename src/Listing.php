<?php
declare( strict_types = 1 );

namespace Cyclopol;

use Symfony\Component\DomCrawler\Crawler;

class Listing {
	private $crawler;
	private $baseUri;

	public function __construct( string $dom, $baseUri ) {
		$this->crawler = new Crawler( $dom );
		$this->baseUri = $baseUri;
	}

	public function getArticleTeasers(): array {
		return $this->crawler->filter('.list-autoteaser li')->each( function( Crawler $teaser ) {
			return new ArticleTeaser(
				$this->baseUri . $teaser->children('.text a')->attr('href')
			);
		} );
	}
}
