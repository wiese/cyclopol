<?php
declare( strict_types = 1 );

namespace Cyclopol\Tests\TextAnalysis;

use Cyclopol\DataModel\StreetAddress;
use Cyclopol\TextAnalysis\StreetNameAnalyser;
use PHPUnit\Framework\TestCase;

class StreetNameAnalyserTest extends TestCase {

	public function getNoMatchSamples() {
		yield [ '' ];
		yield [ 'lorem ipsum' ];

		yield [ 'Ein Gehweg bleibt unerkannt' ];
		yield [ 'Radweg' ];
		yield [ 'Fußweg' ];
		yield [ 'Umweg' ];

		yield [ 'Bahnsteig' ];

		yield [ 'Vorplatz' ];
		yield [ 'Parkplatz' ];

		yield [ 'Rettungsgasse' ];

		yield [ 'Schlagring' ];

		yield [ 'Kreuzberg' ];
		yield [ 'Schöneberg' ];

		yield [ 'Bahnbrücke' ];
		yield [ 'S-Bahnbrücke' ];

		yield [ 'Käufer' ];

		yield [ 'Einmündung Straße der Gerechten' ];
		yield [ 'Ecke Straße der Gerechten' ];
	}

	public function getFineSamples() {
		yield [
			[ new StreetAddress( 'Bruno-Taut-Ring', '2' ) ],
			'gegen 9.45 Uhr vor der Häuserzeile Bruno-Taut-Ring Nr. 2 bis 2 b mit...',
		];
		yield [
			[ new StreetAddress( 'Herrmannplatz' ), new StreetAddress( 'Karl-Marx-Allee' ) ],
			'...zwischen Herrmannplatz und Karl-Marx-Allee...',
		];
		yield [
			[ new StreetAddress( 'Hasenwinkel', '47c' ) ],
			'...Idylle jäh unterbrochen als sonntags im Hasenwinkel 47c das Bier alle...',
		];
		yield [
			[ new StreetAddress( 'Müllerstraße' ) ],
			'...an der Ecke Müllerstraße mit Umlauten im Gepäck hopsgenommen...',
		];
		yield [
			[ new StreetAddress( 'Eisenacher Straße' ) ],
			'...auf Händen laufend in der Eisenacher Straße wurde operativ genehmigt...',
		];
		yield [
			[ new StreetAddress( 'Deichstrasse' ) ],
			'...an der Deichstrasse löst spontan poetry slam aus - Kollegen gewinnen...',
		];
		yield [
			[
				new StreetAddress( 'Friedrich-Jahn-Park', '8' ),
				new StreetAddress( 'Hedwig-Lange-Gasse', '123' ),
			],
			'...Ecke Friedrich-Jahn-Park 8 und Hedwig-Lange-Gasse 123 wurde Geschichte geschrieben...',
		];
		yield [
			[ new StreetAddress( 'Lutherbrücke' ) ],
			'...beim Überqueren der Lutherbrücke nahe dem Kanzleramt...',
		];
		yield [
			[ new StreetAddress( 'Tempelhofer Ufer' ) ],
			'...nahe dem Tempelhofer Ufer an der U-Bahnstation...',
		];
		yield [
			[ new StreetAddress( 'Greenwichpromenade' ) ],
			'...zur Dampferanlegestelle Greenwichpromenade am Tegeler See...',
		];
	}

	public function getSamplesWithKnownWeaknesses() {
		yield [ // two word (before ~straße) street names
			[ new StreetAddress( 'Steinberg' ) ],
			'die Nobeladresse Am Steinberg wurde schon öfter zum Ziel',
		];
		yield [
			[ new StreetAddress( 'U-Bahnhof Platz' ), new StreetAddress( 'Luftbrücke' ) ],
			'zum U-Bahnhof Platz der Luftbrücke, da dort',
		];
		yield [
			[],
			'...Café in der Straße Alt-Lichtenrade, bedrohte...',
		];
		yield [
			[],
			'...auf dem U-Bahnhof Kottbusser Tor einer 33-jährigen...',
		];
		yield [
			[],
			'...gegen 8.45 Uhr am S-Bahnhof Wuhletal, als die Täter...',
		];
	}

	/**
	 * @dataProvider getFineSamples
	 */
	public function testFindsMatches( array $expected, string $text ) {
		$sut = new StreetNameAnalyser();
		$this->assertEquals( $expected, $sut->getStreetNames( $text ) );
	}

	/**
	 * @dataProvider getNoMatchSamples
	 */
	public function testIgnoresNonMatches( string $text ) {
		$sut = new StreetNameAnalyser();
		$this->assertEquals( [], $sut->getStreetNames( $text ) );
	}

	/**
	 * @dataProvider getSamplesWithKnownWeaknesses
	 */
	public function testExhibitsKnownWeaknesses( array $expected, string $text ) {
		$sut = new StreetNameAnalyser();
		$this->assertEquals( $expected, $sut->getStreetNames( $text ) );
	}

	public function testBlacklistWordDoesNotPreventLaterMatches() {
		$sut = new StreetNameAnalyser();
		$this->assertEquals(
			[ new StreetAddress( 'Kurfürstenstraße' ) ],
			$sut->getStreetNames( '...eine Rettungsgasse auf der Kurfürstenstraße...' )
		);
	}
}
