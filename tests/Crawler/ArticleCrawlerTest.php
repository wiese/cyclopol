<?php
declare( strict_types = 1 );

namespace Cyclopol\Tests\Crawler;

use Cyclopol\Crawler\ArticleCrawler;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

class ArticleCrawlerTest extends TestCase {

	private $dom;

	public function setUp(): void {
		$this->dom = file_get_contents( __DIR__ . '/../fixtures/report_with_parent.html' );
	}

	public function testGetTitle() {
		$sut = new ArticleCrawler( $this->dom );
		$this->assertSame( 'Tötungsdelikt in Britz - Haftbefehl erlassen', $sut->getTitle() );
	}

	public function testGetId() {
		$sut = new ArticleCrawler( $this->dom );
		$this->assertSame( 2243, $sut->getId() );
	}

	public function testGetPreviousIds() {
		$sut = new ArticleCrawler( $this->dom );
		$this->assertSame( [ 2159, 2, 2153 ], $sut->getPreviousIds() );
	}

	public function testGetTime() {
		$sut = new ArticleCrawler( $this->dom );
		$time = $sut->getTime();
		$this->assertSame( '2019-09-16 10:25', $time->format('Y-m-d H:i') );
		$this->assertEquals( new DateTimeZone( 'Europe/Berlin' ), $time->getTimezone() );
	}

	public function testGetCategories() {
		$sut = new ArticleCrawler( $this->dom );
		$this->assertSame( 'Neukölln', $sut->getCategories() );
	}
}

