<?php
declare( strict_types = 1 );

namespace Cyclopol\DataAccess;

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
}
