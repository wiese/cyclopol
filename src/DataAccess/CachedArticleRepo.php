<?php
declare( strict_types = 1 );

namespace Cyclopol\DataAccess;

use Cyclopol\DataModel\Article;
use Exception;
use DateTimeImmutable;
use DateTimeZone;

class CachedArticleRepo implements ArticleRepo {
	private string $path;

	public function __construct( string $path ) {
		$this->path = $path;
	}

	public function getArticle( string $link ): Article {
		$fileName = $this->getFileName( $link );
		if ( !is_file( $fileName ) || ( $data = file_get_contents( $fileName, false ) ) === false ) {
			throw new Exception( 'Article not cached' );
		}

		$data = json_decode( file_get_contents( $fileName ) );
		return new Article(
			$data->link,
			$data->id,
			$data->previousIds,
			$data->title,
			$data->text,
			new DateTimeImmutable( $data->date->date, new DateTimeZone( $data->date->timezone ) ),
			$data->categories,
		);
	}

	public function saveArticle( string $link, Article $article ) {
		file_put_contents( $this->getFileName( $link ), json_encode( $article ) );
	}

	private function getFileName( string $link ): string {
		return $this->path . '/' . str_replace( [ '/', '.' ], '-', $link ) . '.json';
	}
}

