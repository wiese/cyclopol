<?php
declare( strict_types = 1 );

namespace Cyclopol\TextAnalysis;

use Cyclopol\DataModel\StreetAddress;

class StreetNameAnalyser {

    /**
     * TODO
     * * "Promenade"
     * * hyphenated street names
     * * "~brücke"
     *
     * IDEAS
     * * find location by mentioned "(U/S-)Bahnhof"
     */
	private const PATTERNS = [
		'(' .
			'(?:[\w-]+|[A-Z][\w-]+\s)' .
			'(?:[sS]tra(?:ß|ss)e|[pP]latz|[pP]ark|[aA]llee|[wW]eg|[gG]asse|[wW]inkel|[rR]ing|[pP]fuhl|[sS]teig|[dD]amm)' .
		')' .
		'(?:\s+(?:Nr\.?|)\s?(\d+\w?)|\b)',
	];

	private const STREET_BLACKLIST = [
		'bahnsteig',
		'gehweg',
		'parkplatz',
		'rettungsgasse',
	    'radweg',
	    'fußweg',
	    'umweg',
	    'schlagring'
	];

	public function getStreetNames( string $text ): array {
		$streets = [];
		foreach ( self::PATTERNS as $pattern ) {
			if ( preg_match_all( '/' . $pattern . '/u', $text, $matches, PREG_UNMATCHED_AS_NULL ) ) {
				foreach ( $matches[ 1 ] as $key => $street ) {
					if ( in_array( strtolower( $street ), self::STREET_BLACKLIST ) ) {
						break;
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

