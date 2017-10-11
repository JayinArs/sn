<?php

namespace App\Helpers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App;
use Form;

class GeocodeHelper
{
	/**
	 * GeocodeHelper constructor.
	 */
	public function __construct()
	{

	}

	/**
	 * @param $address
	 *
	 * @return mixed
	 */
	private function lookup( $address )
	{
		$api_url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode( $address ) . '&key=' . Config::get( 'constants.google_api_key' );

		$ch = curl_init();

		curl_setopt( $ch, CURLOPT_URL, $api_url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HEADER, false );

		$output = curl_exec( $ch );

		curl_close( $ch );

		$response = json_decode( $output, true );

		return $response;
	}

	/**
	 * @param $address
	 *
	 * @return bool
	 */
	public function coordinatesLookup( $address )
	{
		$response = $this->lookup( $address );

		if ( ! empty( $response['results'][0]['geometry']['location'] ) ) {
			return $response['results'][0]['geometry']['location'];
		}

		return false;
	}

	/**
	 * @param $timezone
	 *
	 * @param bool $short_name
	 *
	 * @return array
	 */
	public function regionLookup( $timezone, $short_name = false )
	{
		$components = [];
		$response   = $this->lookup( $timezone );

		if ( ! empty( $response['results'][0]['address_components'] ) ) {
			$address_components = $response['results'][0]['address_components'];
			foreach ( $address_components as $component ) {
				foreach ( $component["types"] as $type ) {
					$components[ $type ] = $component["long_name"];

					if ( $short_name ) {
						$components[ $type ] = $component["short_name"];
					}
				}
			}
		}

		return $components;
	}
}