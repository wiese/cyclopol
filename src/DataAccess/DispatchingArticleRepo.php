<?php
declare( strict_types = 1 );

namespace Cyclopol\DataAccess;

use Cyclopol\Article;
use Exception;

class DispatchingArticleRepo implements ArticleRepo {
	private ArticleRepo $cachedArticleRepo;
	private HttpArticleRepo $httpArticleRepo;
	private bool $lastArticleWasServedFromCache = false;

	public function __construct( CachedArticleRepo $cachedArticleRepo, HttpArticleRepo $httpArticleRepo ) {
		$this->cachedArticleRepo = $cachedArticleRepo;
		$this->httpArticleRepo = $httpArticleRepo;
	}

	public function getArticle( string $link ): Article {
		try {
			$article = $this->cachedArticleRepo->getArticle( $link );
			$this->lastArticleWasServedFromCache = true;
			echo "using '{$link}' from cache\n";
		} catch ( Exception $e ) {
			$article = $this->httpArticleRepo->getArticle( $link );
			$this->cachedArticleRepo->saveArticle( $link, $article );
			$this->lastArticleWasServedFromCache = false;
			echo "retrieved and cached '{$link}'\n";
		}
		return $article;
	}

	public function lastArticleWasServedFromCache(): bool {
		return $this->lastArticleWasServedFromCache;
	}
}

