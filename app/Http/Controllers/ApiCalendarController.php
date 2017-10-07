<?php

namespace App\Http\Controllers;

use App\Calendar;
use App\Timezone;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Validator;
use JSONResponse;
use MultiLang;
use Hijri;
use Geocode;

class ApiCalendarController extends Controller
{
	/**
	 * @return mixed
	 */
	public function all()
	{
		$calendars = Calendar::all();

		return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $calendars );
	}

	/**
	 * @param $timezone
	 * @param bool $city
	 * @param bool $country
	 *
	 * @return bool|\Illuminate\Database\Eloquent\Model
	 */
	private function create_calendar( $timezone, $city = false, $country = false )
	{
		if ( ! $city || ! $country ) {
			$region = Geocode::regionLookup( $timezone );

			if ( ! empty( $region['country'] ) ) {
				$country = $region['country'];
			}

			if ( ! empty( $region['locality'] ) ) {
				$city = $region['locality'];
			}
		}

		if ( $city && $country ) {
			$current_date = Hijri::getHijriDateByGeorgian( $timezone, $city, $country );

			if ( $current_date ) {
				$calendar = Calendar::create( [
					                              'city'             => $city,
					                              'country'          => $country,
					                              'current_date'     => $current_date['date'],
					                              'next_update_time' => $current_date['time'],
					                              'timezone'         => $timezone
				                              ] );

				return $calendar;
			}
		}

		return false;
	}

	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function create( Request $request )
	{
		$validation_rules = [
			'country'  => 'nullable',
			'city'     => 'nullable|unique:calendars,city,NULL,id,country,' . $request->input( 'country' ),
			'timezone' => 'required'
		];

		$validator = Validator::make( $request->all(), $validation_rules );
		$messages  = $validator->messages()->all();

		if ( $validator->fails() ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhrase( $messages[0] ) );
		}

		$timezone = $request->input( 'timezone' );
		$calendar = Calendar::where( 'timezone', $timezone )->first();

		$city = $request->input('city', false);
		$country = $request->input('country', false);

		if ( $calendar ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $calendar );
		} else {
			$calendar = $this->create_calendar( $timezone, $city, $country );

			if ( $calendar && $calendar->id > 0 ) {
				return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $calendar );
			}
		}

		return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.NOT_FOUND' ), null, MultiLang::getPhraseByKey( 'strings.timezone.not_found' ) );
	}

	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function update( Request $request )
	{
		Artisan::call( 'calendar:update' );

		return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), null );
	}

	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function get( Request $request )
	{
		$validation_rules = [
			'timezone' => 'required'
		];

		$validator = Validator::make( $request->all(), $validation_rules );
		$messages  = $validator->messages()->all();

		if ( $validator->fails() ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhrase( $messages[0] ) );
		}

		$timezone = $request->input( 'timezone' );
		$calendar = Calendar::where( 'timezone', $timezone )->first();

		if ( $calendar ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $calendar );
		} else {
			$calendar = $this->create_calendar( $timezone );

			if ( $calendar && $calendar->id > 0 ) {
				return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $calendar );
			}
		}

		return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null );
	}
}
