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
		yield [	'Ein Gehweg bleibt unerkannt' ];
		yield [ 'Bahnsteig' ];
		yield [ 'Parkplatz' ];
		yield [ 'Rettungsgasse' ];
	}

	public function getFineSamples() {
		yield [
			[ new StreetAddress( 'Bruno-Taut-Ring', '2' ) ],
			'Nach Zeugenaussagen geriet er gegen 9.45 Uhr vor der Häuserzeile Bruno-Taut-Ring Nr. 2 bis 2 b mit einem...',
		];
		yield [
			[ new StreetAddress( 'Herrmannplatz' ), new StreetAddress( 'Karl-Marx-Allee' ) ],
			'...zwischen Herrmannplatz und Karl-Marx-Allee...',
		];
		yield [
			[ new StreetAddress( 'Hasenwinkel', '47c' ) ],
			'...Idylle wurde jäh unterbrochen als am Sonntag im Hasenwinkel 47c das Bier alle war...',
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
			'...spitze Formulierung an der Deichstrasse löst spontan poetry slam aus - Kollegen gewinnen ersten Preis...',
		];
		yield [
			[ new StreetAddress( 'Friedrich-Jahn-Park', '8' ), new StreetAddress( 'Hedwig-Lange-Gasse', '123' ) ],
			'...an der Ecke Friedrich-Jahn-Park 8 und Hedwig-Lange-Gasse 123 wurde Geschichte geschrieben...',
		];
	}

	public function getSamplesWithKnownWeaknesses() {
		yield [
			[ new StreetAddress( 'Müllerstraße' ), new StreetAddress( 'Schlagring', '8' ) ],
			'...an der Ecke Müllerstraße mit einem Schlagring 8 mal geschlagen...',
		];
		yield [
		    [ new StreetAddress( 'Einmündung Straße' ) ],
		    'Dort, an der Einmündung Straße der Pariser Kommune/An der Ostbahn, fanden sie einen BMW',
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
}

