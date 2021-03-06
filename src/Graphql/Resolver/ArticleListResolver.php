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
		$repo = $this->em->getRepository( Article::class );

		$qb = $repo
			->getFulltextSearchQuery( $args[ 'search' ] )
			->setMaxResults( $args[ 'limit' ] );

		$ids = array_column( $qb->getQuery()->getResult(), 'id' );

		return $repo->getFindByIdsInclRelationsQuery( $ids )
			->orderBy( 'a.date', 'DESC' )
			->getQuery()
			->getResult();
	}

	public static function getAliases(): array {
		return [
			'resolve' => 'ArticleList',
		];
	}
}
