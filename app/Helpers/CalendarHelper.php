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

	/**
	 * @param $date
	 *
	 * @param string $format
	 *
	 * @return string
	 */
	public function format_date( $date, $format = 'j F Y' )
	{
		$months = Config::get( 'constants.hijri.months' );

		$date      = Carbon::parse( $date );
		$month     = $months[ $date->month - 1 ];
		$format    = str_replace( 'F', '%%', $format );
		$formatted = str_replace( '%%', $month, $date->format( $format ) );

		return $formatted;
	}

	/**
	 * @param Carbon $datetime
	 * @param $city
	 * @param $country
	 *
	 * @return bool|string
	 */
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

	/**
	 * @param string $timezone
	 * @param $city
	 * @param $country
	 *
	 * @return array|bool
	 */
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
					$sunset_time = ( ( ! empty( $response["data"]["timings"]["Maghrib"] ) ) ? $response["data"]["timings"]["Maghrib"] : null );
					$sunset      = Carbon::parse( $sunset_time, $timezone );

					if ( $datetime->diffInMinutes( $sunset, false ) > 0 ) {
						$date->subDay( 1 );
					}

					return [
						'date' => $date->format( 'Y-m-d' ),
						'time' => $sunset_time
					];
				}
			}
		} catch ( \Exception $e ) {
			return false;
		}

		return false;
	}
}