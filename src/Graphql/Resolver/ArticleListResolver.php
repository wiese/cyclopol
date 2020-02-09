<?php
declare( strict_types = 1 );

namespace Cyclopol\Graphql\Resolver;

use Cyclopol\DataModel\Article;
use Cyclopol\TextAnalysis\StreetNameAnalyser;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr;
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
			->createQueryBuilder( 'a' )
			->select( 'a.id' );
		if ( ( $args[ 'search' ] ) !== null && $args[ 'search' ] !== '' ) {
			$qb->andWhere( $qb->expr()->like( 'a.text', ':search' ) );
			$qb->setParameter( 'search', '%' . addcslashes( $args[ 'search' ], '%_' ) . '%' ); // Doctrine way to do this?
		}
		$qb->orderBy( 'a.date', 'DESC' )
			->setMaxResults( $args[ 'limit' ] );

		$ids = array_column( $qb->getQuery()->getResult(), 'id' );

		return [ 'articles' => $repo->findByIdsInclRelations( $ids ) ];
	}

	public static function getAliases(): array {
		return [
			'resolve' => 'ArticleList',
		];
	}
}
