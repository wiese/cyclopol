<?php
declare( strict_types = 1 );

namespace Cyclopol\Graphql\Resolver;

use Cyclopol\DataModel\Article;
use Doctrine\ORM\EntityManagerInterface;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class ArticleListResolver implements ResolverInterface, AliasedInterface {

	private EntityManagerInterface $em;

	public function __construct( EntityManagerInterface $em ) {
		$this->em = $em;
	}

	public function resolve( Argument $args ) {
		$articles = $this->em->getRepository( Article::class )->findBy(
			[],
			[ 'date' => 'desc' ],
			$args[ 'limit' ],
			0
		);
		return [ 'articles' => $articles ];
	}

	public static function getAliases(): array {
		return [
			'resolve' => 'ArticleList',
		];
	}
}
