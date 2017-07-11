<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App;

class CalendarHelper
{
	public function __construct()
	{

	}

	public function getPrayerTime( Carbon $datetime, $city, $country )
	{
		try {
			$timestamp = $datetime->timestamp;
			$prayer    = new \AlAdhanApi\TimesByCity( $city, $country, $timestamp );

			$response = $prayer->get();
			if ( $response["code"] == 200 ) {
				$maghrib_time = ( ( ! empty( $response["data"]["timings"]["Maghrib"] ) ) ? $response["data"]["timings"]["Maghrib"] : null );
				$maghrib      = Carbon::parse( $maghrib_time );

				return $maghrib->toTimeString();
			}
		} catch ( \Exception $e ) {
			return false;
		}

		return false;
	}

	public function getHijriDateByGeorgian( $timezone = 'UTC', $city, $country )
	{
		try {
			$datetime  = Carbon::now( $timezone );
			$timestamp = $datetime->timestamp;

			$calendar = new \AlAdhanApi\HijriGregorianCalendar();
			$prayer   = new \AlAdhanApi\TimesByCity( $city, $country, $timestamp );

			$response = $calendar->gregorianToHijri( date( 'd-m-Y', $timestamp ) );

			if ( $response["code"] == 200 ) {
				$date     = ( ( ! empty( $response["data"]["hijri"]["date"] ) ) ? $response["data"]["hijri"]["date"] : null );
				$date     = Carbon::parse( $date );
				$response = $prayer->get();

				if ( $response["code"] == 200 ) {
					$maghrib_time = ( ( ! empty( $response["data"]["timings"]["Maghrib"] ) ) ? $response["data"]["timings"]["Maghrib"] : null );
					$maghrib      = Carbon::parse( $maghrib_time, $timezone );
					//$maghrib      = Carbon::parse( '10:25', $timezone );

					if ( $datetime->diffInMinutes( $maghrib, false ) > 0 ) {
						$date->subDay( 1 );
					}

					return [
						'date' => $date->format( 'Y-m-d' ),
						'time' => $maghrib_time
					];
				}
			}
		} catch ( \Exception $e ) {
			return false;
		}

		return false;
	}
}