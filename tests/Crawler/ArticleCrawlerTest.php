<?php
declare( strict_types = 1 );

namespace Cyclopol\Tests\Crawler;

use Cyclopol\Crawler\ArticleCrawler;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

class ArticleCrawlerTest extends TestCase {

	public function getExamples() {
		yield [
			file_get_contents( __DIR__ . '/../fixtures/report_with_parent.html' ),
			[
				'title' => 'Tötungsdelikt in Britz - Haftbefehl erlassen',
				'id' => 2243,
				'previousIds' => [ 2159, 2, 2153 ],
				'time' => '2019-09-16 10:25',
				'categories' => 'Neukölln',
			],
		];
		yield [
			file_get_contents( __DIR__ . '/../fixtures/report_with_two_parents.html' ),
			[
				'title' => 'Nach Handtaschenraub - Tatverdächtiger erneut mit Bildern gesucht',
				'id' => 1146, // this is a known flaw. in fact, there is no known id and the one shown should be the first previousIds
				'previousIds' => [ 646 ],
				'time' => '2019-11-28 08:17',
				'categories' => 'Mitte',
			],
		];
	}

	/**
	 * @dataProvider getExamples
	 */
	public function testExamples( string $dom, array $crawler ) {
		$sut = new ArticleCrawler( $dom );
		$this->assertSame( $crawler[ 'title' ], $sut->getTitle() );
		$this->assertSame( $crawler[ 'id' ], $sut->getId() );
		$this->assertSame( $crawler[ 'previousIds' ], $sut->getPreviousIds() );
		$time = $sut->getTime();
		$this->assertSame( $crawler[ 'time' ], $time->format('Y-m-d H:i') );
		$this->assertEquals( new DateTimeZone( 'Europe/Berlin' ), $time->getTimezone() );
		$this->assertSame( $crawler[ 'categories' ], $sut->getCategories() );
	}

}

