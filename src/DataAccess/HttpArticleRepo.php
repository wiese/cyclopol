<?php
declare( strict_types = 1 );

namespace Cyclopol\DataAccess;

use Cyclopol\Article;
use Cyclopol\Crawler\ArticleCrawler;
use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpArticleRepo implements ArticleRepo {
	private const METHOD_GET = 'GET';
	private const STATUS_SUCCESS = 200;

	private $httpClient;

	public function __construct( HttpClientInterface $httpClient ) {
		$this->httpClient = $httpClient;
	}

	public function getArticle( string $link ): Article {
		$response = $this->httpClient->request( self::METHOD_GET, $link );

		if ( $response->getStatusCode() !== self::STATUS_SUCCESS ) {
			throw new Exception( "Article not found: $link, status {$response->getStatusCode()}" );
		}

		$crawler = new ArticleCrawler( $response->getContent() );

		return new Article(
			$link,
			$crawler->getId(),
			$crawler->getPreviousIds(),
			$crawler->getTitle(),
			$crawler->getText(),
			$crawler->getTime(),
			$crawler->getCategories()
		);
	}
}

