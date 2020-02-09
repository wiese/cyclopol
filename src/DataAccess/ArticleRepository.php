<?php
declare( strict_types = 1 );

namespace Cyclopol\DataAccess;

use Cyclopol\TextAnalysis\StreetNameAnalyser;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

class ArticleRepository extends EntityRepository {
	public function findAllWithoutAddress( int $streetNameAnalyserVersion ) {
		return $this
			->createQueryBuilder( 'a' )
			->leftJoin(
				'Cyclopol\DataModel\ArticleAddress',
				'addr',
				Expr\Join::WITH,
				'addr.article = a.id AND addr.streetNameAnalyserVersion = :streetNameAnalyserVersion'
			)
			->where( 'addr.id IS NULL' )
			->setParameter( 'streetNameAnalyserVersion', $streetNameAnalyserVersion )
			->getQuery()
			->getResult();
	}

	public function findByIdsInclRelations( array $ids ) {
		return $this
			->createQueryBuilder( 'a' )
			->andWhere( 'a.id IN ( :ids )' )
			->setParameter( 'ids', $ids )
			->leftJoin(
				'a.addresses',
				'addresses',
				Expr\Join::WITH,
				'addresses.streetNameAnalyserVersion = :streetNameAnalyserVersion' .
					' OR ' .
					'addresses.streetNameAnalyserVersion IS NULL'
			)
			->setParameter( 'streetNameAnalyserVersion', StreetNameAnalyser::VERSION )
			->addSelect( 'addresses' )
			->leftJoin( 'addresses.coordinate', 'coordinate' )
			->addSelect( 'coordinate' )
			->getQuery()
			->getResult();
	}
}
