<?php
declare( strict_types = 1 );

namespace Cyclopol\GeoCoding;

use Cyclopol\DataModel\Address;
use Cyclopol\DataModel\Coordinate;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AddressGeoCoder {
	private const METHOD_GET = 'GET';
	private const STATUS_OK = 200;
	private HttpClientInterface $httpClient;

	public function __construct( HttpClientInterface $httpClient ) {
		$this->httpClient = $httpClient;
	}

	public function getCoordinates(
		Address $address
	): ?Coordinate {
		$q = (string)$address;
		if ( $address->getDistrict() ) {
			$q .= ', ' . $address->getDistrict();
		}
		$q .= ', ' . $address->getCity();
		$url = '/search?' . http_build_query( [
			'countrycodes' => $address->getCountry(),
			'city' => $address->getCity(),
			'q' => $q,
			'format' => 'json',
		] );

		$response = $this->httpClient->request( self::METHOD_GET, $url );

		if ( $response->getStatusCode() !== self::STATUS_OK ) {
			throw new Exception( "Geocoding failed, status {$response->getStatusCode()}" );
		}

		$matches = json_decode( $response->getContent() );

		$hit = null;
		if ( $address->hasNumber() ) {
			// hopefully this is a specific building
			$hit = $matches[ 0 ];
		} else {
			foreach ( $matches as $match ) {
				if ( $match->osm_type === 'way' && $match->class === 'highway' ) {
					$hit = $match;
					break;
				}
			}
		}

		if ( !$hit ) {
			return null;
		}

		return new Coordinate(
			$hit->display_name,
			(float)$hit->lat,
			(float)$hit->lon
		);
	}
}
