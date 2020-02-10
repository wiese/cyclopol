<?php
declare( strict_types = 1 );

namespace Cyclopol\Graphql\Resolver;

use Cyclopol\DataModel\Article;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class ArticleCountResolver implements ResolverInterface, AliasedInterface {

	private EntityManagerInterface $em;

	public function __construct( EntityManagerInterface $em ) {
		$this->em = $em;
	}

	public function resolve( Argument $args ) {
		$counts = $this->em->getRepository( Article::class )
			->getFulltextSearchQuery( $args[ 'search' ] )
			->addSelect( 'SUBSTRING(a.date, 1, 10) date' )
			->addSelect( 'COUNT(DISTINCT a.id) totalCount' )
			->groupBy( 'date' )
			->orderBy( 'date', 'ASC' )
			->getQuery()
			->getResult();

		return array_map( function ( $set ) {
			return [
			 'date' => new DateTime( $set['date'] ),
			 'count' => $set['totalCount'],
			];
		}, $counts );
	}

	public static function getAliases(): array {
		return [
			'resolve' => 'ArticleCount',
		];
	}
}
