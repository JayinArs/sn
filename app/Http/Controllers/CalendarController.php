<?php

namespace App\Http\Controllers;

use App\Calendar;
use App\Timezone;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Validator;
use JSONResponse;
use MultiLang;
use Hijri;

class CalendarController extends Controller
{
	public function all()
	{
		$calendars = Calendar::all();
		return json_encode($calendars);
		return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $calendars );
	}

	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function create( Request $request )
	{
		$validation_rules = [
			'country'  => 'required',
			'city'     => 'required|unique:calendars,city,NULL,id,country,' . $request->input( 'country' ),
			'timezone' => 'required'
		];

		$validator = Validator::make( $request->all(), $validation_rules );
		$messages  = $validator->messages()->all();

		if ( $validator->fails() ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhrase( $messages[0] ) );
		}

		$current_date = Hijri::getHijriDateByGeorgian( $request->input( 'timezone' ), $request->input( 'city' ), $request->input( 'country' ) );

		if ( $current_date ) {
			$calendar = Calendar::create( [
				                              'city'             => $request->input( 'city' ),
				                              'country'          => $request->input( 'country' ),
				                              'current_date'     => $current_date['date'],
				                              'next_update_time' => $current_date['time'],
				                              'timezone'         => $request->input( 'timezone' )
			                              ] );
			if ( $calendar->id > 0 ) {
				return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $calendar );
			}
		}

		return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.NOT_FOUND' ), null, MultiLang::getPhraseByKey( 'strings.timezone.not_found' ) );
	}
}
