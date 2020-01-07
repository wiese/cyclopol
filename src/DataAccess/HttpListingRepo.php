<?php
declare( strict_types = 1 );

namespace Cyclopol\DataAccess;

use Cyclopol\Crawler\ListingCrawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpListingRepo {
	private HttpClientInterface $httpClient;
	private string $baseUri;

	public function __construct( HttpClientInterface $httpClient, string $baseUri ) {
		$this->httpClient = $httpClient;
		$this->baseUri = $baseUri;
	}

	public function getListing( $page ): ?ListingCrawler {
		$response = $this->httpClient->request( 'GET', '/polizei/polizeimeldungen/archiv/2020/', [
			'query' => [
				'page_at_1_0' => $page
			]
		] );

		if ( $response->getStatusCode() !== 200 ) {
			return null;
		}

		return new ListingCrawler( $response->getContent(), $this->baseUri );
	}
}
