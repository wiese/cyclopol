<?php
declare( strict_types = 1 );

namespace Cyclopol\Tests\DataModel;

use Cyclopol\DataModel\Article;
use Cyclopol\DataModel\ArticleSource;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ArticleTest extends TestCase {

	public function testConstructionAndAccessors(): void {
		$articleSource = new ArticleSource(
			'https:://example.com',
			'<html>example</html>'
		);
		$articleCrawlerVersion = 1;
		$link = '/u/r/l.html';
		$reportId = 4711;
		$title = 'title';
		$text = 'foo bar baz';
		$districts = 'districts';
		$sut = new Article(
			$articleSource,
			$articleCrawlerVersion,
			$link,
			$reportId,
			[ 815 ],
			$title,
			$text,
			new DateTimeImmutable( '2019-09-16 10:25' ),
			$districts
		);
		$this->assertInstanceOf( Article::class, $sut );

		$this->assertSame( $articleSource, $sut->getArticleSource() );
		$this->assertSame( $articleCrawlerVersion, $sut->getArticleCrawlerVersion() );

		$this->assertSame( $link, $sut->getLink() );
		$this->assertSame( $reportId, $sut->getReportId() );
		$this->assertSame( $title, $sut->getTitle() );
		$this->assertSame( $text, $sut->getText() );
		$this->assertSame( $districts, $sut->getDistricts() );
	}

}
