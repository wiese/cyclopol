<?php
declare( strict_types = 1 );

namespace Cyclopol\DataAccess;

use Cyclopol\DataModel\ArticleSource;
use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpArticleSourceRepo {
	private const METHOD_GET = 'GET';
	private const STATUS_SUCCESS = 200;

	private HttpClientInterface $httpClient;

	public function __construct( HttpClientInterface $httpClient ) {
		$this->httpClient = $httpClient;
	}

	public function get( string $link ): ArticleSource {
		$response = $this->httpClient->request( self::METHOD_GET, $link );

		if ( $response->getStatusCode() !== self::STATUS_SUCCESS ) {
			throw new Exception( "Article not found: $link, status {$response->getStatusCode()}" );
		}

		return new ArticleSource(
			$link,
			$response->getContent(),
		);
	}
}
