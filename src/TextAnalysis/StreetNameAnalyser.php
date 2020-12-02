<?php
declare( strict_types = 1 );

namespace Cyclopol\TextAnalysis;

use Cyclopol\DataModel\StreetAddress;

class StreetNameAnalyser {

	public const VERSION = 5;
	/**
	 * IDEAS
	 * * find location by mentioned "(U/S-)Bahnhof"
	 */
	private const KEYWORDS = [
		'stra(?:ß|ss)e',
		'platz',
		'park',
		'allee',
		'weg',
		'gasse',
		'winkel',
		'ring',
		'pfuhl',
		'steig',
		'stieg',
		'damm',
		'brücke',
		'ufer',
		'berg',
		'promenade',
	];
	private const PATTERN =
		'(' .
			'(?:' .
				'[\w-]+@@@KEYWORD@@@' .
				'|' .
				'[A-Z][\w-]+\s@@@UCFIRST_KEYWORD@@@' . // match space-separated keywords only if keyword capitalized
			')' .
		')' .
		'(?:\s+(?:Nr\.?|)\s?(\d+\w?)|\b)';
	// maintain in lowercase!
	private const STREET_FILTER = [
		'einbahnstraße',
		'nebenstraße',

		'fluchtweg',
		'gehweg',
		'radweg',
		'fußweg',
		'umweg',
		'heimweg',
		'fahrweg',
		'fußgängerüberweg',

		'bahnsteig',
		's-bahnsteig',

		'ausstieg',

		'spielplatz',
		'vorplatz',
		'parkplatz',

		'sackgasse',
		'rettungsgasse',

		'schlagring',
		'carsharing',
		'gering',

		// vs ~berg
		'kreuzberg',
		'friedrichshain-kreuzberg',
		'tempelhof-schöneberg',
		'schöneberg',
		'lichtenberg',

		'bahnbrücke',
		's-bahnbrücke',

		// vs ~ufer - FIXME implement prefixes or it is getting ridiculous
		'käufer',
		'kokainkäufer',

		// TODO
		// TODO those are hacks - we should only match them if the second term is upper case
		'ecke straße', // matching the wrong part
		'einmündung straße', // matching the wrong part
		'richtung straße',
		'bereich straße',
		'richtung platz',
	];

	public function getStreetNames( string $text ): array {
		$streets = [];
		foreach ( self::KEYWORDS as $keyword ) {
			$keywordFirst = substr( $keyword, 0, 1 );
			$pattern = str_replace(
				[
					'@@@KEYWORD@@@',
					'@@@UCFIRST_KEYWORD@@@',
				],
				[
					'[' . $keywordFirst . strtoupper( $keywordFirst ) . ']' . substr( $keyword, 1 ),
					ucfirst( $keyword ),
				],
				self::PATTERN
			);
			if ( preg_match_all( '/' . $pattern . '/u', $text, $matches, PREG_UNMATCHED_AS_NULL ) ) {
				foreach ( $matches[ 1 ] as $key => $street ) {
					if ( in_array( strtolower( $street ), self::STREET_FILTER ) ) {
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
