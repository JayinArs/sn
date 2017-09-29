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
	public function __construct()
	{

	}

	public function coordinatesLookup( $address )
	{
		$api_url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode( $address ) . '&key=' . Config::get( 'constants.google_api_key' );

		$ch = curl_init();

		curl_setopt( $ch, CURLOPT_URL, $api_url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HEADER, false );

		$output = curl_exec( $ch );

		curl_close( $ch );

		$response = json_decode( $output, true );
		if ( ! empty( $response['results']['geometry']['location'] ) ) {
			return $response['results']['geometry']['location'];
		}

		return false;
	}
}