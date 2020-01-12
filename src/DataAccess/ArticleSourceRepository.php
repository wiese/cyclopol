<?php
declare( strict_types = 1 );

namespace Cyclopol\DataAccess;

use Doctrine\ORM\EntityRepository;

class ArticleSourceRepository extends EntityRepository {
	public function findAll() {
		return $this->findBy( [], [ 'link' => 'ASC' ] );
	}
}
