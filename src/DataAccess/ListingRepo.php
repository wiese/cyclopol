<?php
declare( strict_types = 1 );

namespace Cyclopol\DataAccess;

use Cyclopol\Listing;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ListingRepo {
	private HttpClientInterface $httpClient;
	private string $baseUri;

	public function __construct( HttpClientInterface $httpClient, string $baseUri ) {
		$this->httpClient = $httpClient;
		$this->baseUri = $baseUri;
	}

	public function getListing( $page ): ?Listing {
		$response = $this->httpClient->request( 'GET', '/polizei/polizeimeldungen/archiv/2020/', [
			'query' => [
				'page_at_1_0' => $page
			]
		] );

		if ( $response->getStatusCode() !== 200 ) {
			return null;
		}

		return new Listing( $response->getContent(), $this->baseUri );
	}
}
