<?php
declare( strict_types = 1 );

namespace Cyclopol\DataAccess;

use Cyclopol\DataModel\ArticleAddress;
use Cyclopol\DataModel\Coordinate;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

class CoordinateRepository extends EntityRepository {
	public function findOneByMatchingAddress( ArticleAddress $address ): ?Coordinate {
		$coordinate = $this
		   ->createQueryBuilder( 'co' )
		   ->join(
			   'Cyclopol\DataModel\ArticleAddress',
			   'addr1',
			   Expr\Join::WITH,
			   'addr1.coordinate = co.id'
		   )
		   ->join(
			   'Cyclopol\DataModel\ArticleAddress',
			   'addr2',
			   Expr\Join::WITH,
			   'addr1.id != addr2.id AND addr2.id = :addr2Id AND ' .
					'addr2.country = addr1.country AND ' .
					'addr2.city = addr1.city AND ' .
					'( addr2.district = addr1.district OR ( addr2.district IS NULL AND addr1.district IS NULL ) ) AND ' .
					'addr2.street = addr1.street AND ' .
					'( addr2.number = addr1.number OR ( addr2.number IS NULL AND addr1.number IS NULL ) ) '
		   )
		   ->setParameter( 'addr2Id', $address->getId() )
		   ->setMaxResults( 1 )
		   ->getQuery()
		   ->getOneOrNullResult();
		if ( $coordinate ) {
		   $coordinate = clone $coordinate;
		}
		return $coordinate;
	}
}
