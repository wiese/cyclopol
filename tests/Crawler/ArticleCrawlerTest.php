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
				// a known flaw - no known id and the one shown should be the first previousIds
				'id' => 1146,
				'previousIds' => [ 646 ],
				'time' => '2019-11-28 08:17',
				'categories' => 'Mitte',
			],
		];
		yield [
			file_get_contents( __DIR__ . '/../fixtures/report_with_meta_info_in_text.html' ),
			[
				'title' => 'Vier Festnahmen nach Motorraddiebstahl',
				'id' => 2732,
				'previousIds' => [],
				'time' => '2020-11-30 16:21',
				'categories' => 'bezirksübergreifend',
			],
		];
	}

	/**
	 * @dataProvider getExamples
	 */
	public function testExamples( string $dom, array $crawler ) {
		$sut = new ArticleCrawler( $dom );
		$this->assertSame( $crawler[ 'title' ], $sut->getTitle() );
		$this->assertNotEmpty( $sut->getText() );
		$this->assertSame( $crawler[ 'id' ], $sut->getId() );
		$this->assertSame( $crawler[ 'previousIds' ], $sut->getPreviousIds() );
		$time = $sut->getTime();
		$this->assertSame( $crawler[ 'time' ], $time->format( 'Y-m-d H:i' ) );
		$this->assertEquals( new DateTimeZone( 'Europe/Berlin' ), $time->getTimezone() );
		$this->assertSame( $crawler[ 'categories' ], $sut->getCategories() );
	}

}
