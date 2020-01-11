<?php
declare( strict_types = 1 );

namespace Cyclopol\Tests\DataModel;

use Cyclopol\DataModel\Article;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

class ArticleTest extends TestCase {

	public function testConstructionAndAccessors(): void {
	    $link = '/u/r/l.html';
	    $reportId = 4711;
	    $title = 'title';
	    $text = 'foo bar baz';
	    $districts = 'districts';
		$sut = new Article(
			$link,
		    $reportId,
			[ 815 ],
			$title,
			$text,
			new DateTimeImmutable( '2019-09-16 10:25' ),
			$districts
		);
		$this->assertInstanceOf( Article::class, $sut );

		$this->assertSame( $link, $sut->getLink() );
		$this->assertSame( $reportId, $sut->getReportId() );
		$this->assertSame( $title, $sut->getTitle() );
		$this->assertSame( $text, $sut->getText() );
		$this->assertSame( $districts, $sut->getDistricts() );
	}

}

