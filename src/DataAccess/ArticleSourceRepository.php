<?php
declare( strict_types = 1 );

namespace Cyclopol\DataAccess;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

class ArticleSourceRepository extends EntityRepository {
	public function findAllUncrawled( int $articleCrawlerVersion ) {
		return $this
			->createQueryBuilder( 's' )
			->leftJoin(
				'Cyclopol\DataModel\Article',
				'a',
				Expr\Join::WITH,
				'a.articleSource = s.id AND a.articleCrawlerVersion = :articleCrawlerVersion'
			)
			->where( 'a.id IS NULL' )
			->setParameter( 'articleCrawlerVersion', $articleCrawlerVersion )
			->getQuery()
			->getResult();
	}
}
