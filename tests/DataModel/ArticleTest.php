<?php
declare( strict_types = 1 );

namespace Cyclopol\Tests\DataModel;

use Cyclopol\DataModel\Article;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

class ArticleTest extends TestCase {

	public function testConstruction(): void {
		$sut = new Article(
			'/u/r/l.html',
			4711,
			[ 815 ],
			'title',
			'foo bar baz',
			new DateTimeImmutable( '2019-09-16 10:25' ),
			'lorem'
		);
		$this->assertInstanceOf( Article::class, $sut );
	}

}

