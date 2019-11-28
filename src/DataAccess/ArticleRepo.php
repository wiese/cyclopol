<?php
declare( strict_types = 1 );

namespace Cyclopol\DataAccess;

use Cyclopol\Article;

interface ArticleRepo {
	public function getArticle( string $link ): Article;
}

