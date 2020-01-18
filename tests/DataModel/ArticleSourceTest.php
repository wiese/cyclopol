<?php
declare( strict_types = 1 );

namespace Cyclopol\Tests\DataModel;

use Cyclopol\DataModel\ArticleSource;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ArticleSourceTest extends TestCase {

	public function testConstructionAndAccessors(): void {
		$link = 'https:://example.com';
		$source = '<html>example</html>';
		$sut = new ArticleSource(
			$link,
			$source
		);
		$this->assertInstanceOf( ArticleSource::class, $sut );

		$this->assertSame( $link, $sut->getLink() );
		$this->assertSame( $source, $sut->getSource() );
		$this->assertInstanceOf( DateTimeImmutable::class, $sut->getDownloadedAt() );
	}

}
