<?php
declare( strict_types = 1 );

namespace Cyclopol\TextAnalysis;

use Cyclopol\DataModel\StreetAddress;

class StreetNameAnalyser {

	/**
	 * IDEAS
	 * * find location by mentioned "(U/S-)Bahnhof"
	 */
	private const PATTERNS = [
		'(' .
			'(?:[\w-]+|[A-Z][\w-]+\s)' .
			'(?:' .
				'[sS]tra(?:ß|ss)e|' .
				'[pP]latz|' .
				'[pP]ark|' .
				'[aA]llee|' .
				'[wW]eg|' .
				'[gG]asse|' .
				'[wW]inkel|' .
				'[rR]ing|' .
				'[pP]fuhl|' .
				'[sS]teig|' .
				'[dD]amm|' .
				'[bB]rücke|' .
				'[uU]fer|' .
				'[bB]erg|' .
				'[pP]romenade' .
			')' .
		')' .
		'(?:\s+(?:Nr\.?|)\s?(\d+\w?)|\b)',
	];

	private const STREET_BLACKLIST = [
		'gehweg',
		'radweg',
		'fußweg',
		'umweg',

		'bahnsteig',

		'vorplatz',
		'parkplatz',

		'rettungsgasse',

		'schlagring',

		'kreuzberg', // vs ~berg
		'schöneberg', // vs ~berg

		'bahnbrücke',
		's-bahnbrücke',

		'käufer', // vs ~ufer

		'ecke straße', // matching the wrong part
		'einmündung straße', // matching the wrong part
	];

	public function getStreetNames( string $text ): array {
		$streets = [];
		foreach ( self::PATTERNS as $pattern ) {
			if ( preg_match_all( '/' . $pattern . '/u', $text, $matches, PREG_UNMATCHED_AS_NULL ) ) {
				foreach ( $matches[ 1 ] as $key => $street ) {
					if ( in_array( strtolower( $street ), self::STREET_BLACKLIST ) ) {
						continue 1;
					}
					$streets[] = new StreetAddress(
						$street,
						$matches[ 2 ][ $key ]
					);
				}
			}
		}

		return array_unique( $streets );
	}
}
