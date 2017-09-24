<?php

namespace App\Http\Controllers;

use App\Calendar;
use App\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Validator;
use JSONResponse;
use MultiLang;
use PushNotification;

class ApiEventController extends Controller
{
	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function create( Request $request )
	{
		$validation_rules = [
			'title'                    => 'required',
			'organization_location_id' => 'required|exists:organization_locations,id',
			'user_id'                  => 'required|exists:users,id',
			'account_id'               => 'required|exists:accounts,id',
			'english_date'             => 'nullable|date_format:Y-m-d',
			'hijri_date'               => 'nullable|date_format:Y-m-d',
			'start_time'               => 'nullable|date_format:H:i:s',
			'end_time'                 => 'nullable|date_format:H:i:s'
		];

		$validator = Validator::make( $request->all(), $validation_rules );
		$messages  = $validator->messages()->all();

		if ( $validator->fails() ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhrase( $messages[0] ) );
		}

		$event = Event::create( [
			                        'title'                    => $request->input( 'title' ),
			                        'organization_location_id' => $request->input( 'organization_location_id' ),
			                        'user_id'                  => $request->input( 'user_id' ),
			                        'account_id'               => $request->input( 'account_id' ),
			                        'is_system_event'          => 0,
			                        'start_time'               => $request->input( 'start_time' ),
			                        'end_time'                 => $request->input( 'end_time', $request->input( 'start_time' ) ),
			                        'english_date'             => $request->input( 'english_date' ),
			                        'hijri_date'               => $request->input( 'hijri_date' ),
			                        'venue'                    => $request->input( 'venue' )
		                        ] );

		if ( $event->id > 0 ) {
			PushNotification::notify( 'event', $event );

			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $event );
		} else {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhraseByKey( 'strings.event.creation_failed' ) );
		}
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function delete( $id )
	{
		$deleted = false;
		$event   = Event::find( $id );

		if ( $event ) {
			$deleted = $event->delete();
		} else {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.NOT_FOUND' ), null, MultiLang::getPhraseByKey( 'strings.event.not_found' ) );
		}

		if ( $deleted ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), null, MultiLang::getPhraseByKey( 'strings.event.deleted' ) );
		} else {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhraseByKey( 'strings.event.deletion_failed' ) );
		}
	}

	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function getSystemEvents( Request $request )
	{
		$validation_rules = [
			'timezone' => 'required|exists:calendars,timezone',
		];

		$validator = Validator::make( $request->all(), $validation_rules );
		$messages  = $validator->messages()->all();

		if ( $validator->fails() ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhrase( $messages[0] ) );
		}

		$timezone = $request->input( 'timezone' );

		$calendar = Calendar::where( 'timezone', $timezone )->first();
		$date     = Carbon::parse( $calendar->current_date );

		$events = Event::with( [ 'organization_location.organization', 'category' ] )
		               ->where( 'is_system_event', 1 )
		               ->whereDay( 'hijri_date', $date->day )
		               ->whereMonth( 'hijri_date', $date->month )
		               ->get();

		return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $events );
	}
}
