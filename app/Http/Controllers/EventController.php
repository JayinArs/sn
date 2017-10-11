<?php

namespace App\Http\Controllers;

use App\Event;
use App\EventCategory;
use App\EventMeta;
use App\Organization;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Validator;
use Hijri;

class EventController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return view( 'event.index', [ 'is_system' => false ] );
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function system_list()
	{
		return view( 'event.index', [ 'is_system' => true ] );
	}

	/**
	 * @return mixed
	 */
	public function data()
	{
		return Datatables::of( Event::with( [ 'category', 'organization_location.organization' ] )
		                            ->where( 'is_system_event', 0 )
		                            ->orderBy( 'hijri_date', 'asc' )
		                            ->orderBy( 'english_date', 'asc' )
		                            ->get() )->make( true );
	}

	/**
	 * @return mixed
	 */
	public function system_data()
	{
		return Datatables::of( Event::with( [ 'category', 'organization_location.organization' ] )
		                            ->where( 'is_system_event', 1 )
		                            ->orderBy( 'hijri_date', 'asc' )
		                            ->orderBy( 'english_date', 'asc' )
		                            ->get() )->make( true );
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$organizations = [
			'' => 'Select Organization'
		];
		$months        = [
			'January',
			'February',
			'March',
			'April',
			'May',
			'June',
			'July',
			'August',
			'September',
			'October',
			'November',
			'December'
		];
		$days          = [];

		for ( $i = 1; $i <= 31; $i ++ ) {
			$days[] = ( $i > 9 ? $i : "0" . $i );
		}

		Organization::all()->each( function ( $org ) use ( &$organizations ) {
			$organizations[ $org->id ] = $org->name;
		} );

		return view( 'event.create', [
			'is_system'       => false,
			'months'          => $months,
			'days'            => $days,
			'organizations'   => $organizations,
			'recurring_types' => Config::get( 'constants.recurring.types' )
		] );
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function system_create()
	{
		$categories = $this->get_categories();

		return view( 'event.create', [
			'is_system'       => true,
			'categories'      => $categories,
			'recurring_types' => Config::get( 'constants.recurring.types' )
		] );
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store( Request $request )
	{
		$user = Auth::user();

		$validation_rules = [
			"title"                    => "required",
			"organization_location_id" => "required|exists:organization_locations,id",
			"time"                     => "required|date_format:H:i",
			"islamic.month"            => "required_if:is_hijri_date,1",
			"islamic.day"              => "required_if:is_hijri_date,1",
			"date.day"                 => "required_without:is_hijri_date",
			"date.month"               => "required_without:is_hijri_date",
			"date.year"                => "required_without:is_hijri_date",
			"recurring_type"           => "required_if:is_recurring,1",
		];

		$validator = Validator::make( $request->all(), $validation_rules );
		$messages  = $validator->messages()->all();

		if ( $validator->fails() ) {
			$error = $messages[0];

			return response()->json( [
				                         'status'  => 'danger',
				                         'message' => $error
			                         ] );
		}

		$is_hijri_date = $request->has( 'is_hijri_date' ) && $request->input( 'is_hijri_date' ) == '1';
		$date          = Hijri::parse( $request->input( 'islamic.month' ), $request->input( 'islamic.day' ) );
		$is_recurring  = $request->input( 'recurring', '0' ) == '1';

		$event = Event::create( [
			                        'title'                    => $request->input( 'title' ),
			                        'hijri_date'               => ( $is_hijri_date ? $date->format( 'Y-m-d' ) : null ),
			                        'english_date'             => ( ! $is_hijri_date ? $date->format( 'Y-m-d' ) : null ),
			                        'is_system_event'          => 0,
			                        'is_recurring'             => $is_recurring ? 1 : 0,
			                        'account_id'               => $user->id,
			                        'organization_location_id' => $request->input( 'organization_location_id' ),
			                        'start_time'               => $request->input( 'time' ),
			                        'end_time'                 => $request->input( 'time' ),
			                        'venue'                    => $request->input( 'venue' )
		                        ] );

		if ( $event->id > 0 ) {
			$meta_keys = Event::getMetaKeys();

			foreach ( $meta_keys as $key ) {
				if ( $request->has( $key ) ) {
					EventMeta::create( [
						                   'event_id' => $event->id,
						                   'key'      => $key,
						                   'value'    => $request->input( $key )
					                   ] );
				}
			}

			return response()->json( [
				                         'status'  => 'success',
				                         'message' => 'Event created successfully'
			                         ] );
		}

		return response()->json( [
			                         'status'  => 'danger',
			                         'message' => 'Failed to create event'
		                         ] );
	}

	public function system_store( Request $request )
	{
		$user = Auth::user();

		$validation_rules = [
			"category"      => "required",
			"title"         => "required",
			"islamic.month" => "required",
			"islamic.day"   => "required"
		];

		$validator = Validator::make( $request->all(), $validation_rules );
		$messages  = $validator->messages()->all();

		if ( $validator->fails() ) {
			$error = $messages[0];

			return response()->json( [
				                         'status'  => 'danger',
				                         'message' => $error
			                         ] );
		}

		$date     = Hijri::parse( $request->input( 'islamic.month' ), $request->input( 'islamic.day' ) );
		$category = $request->input( 'category' );

		if ( $category == 'other' ) {
			$category = null;
		}

		$event = Event::create( [
			                        'title'           => $request->input( 'title' ),
			                        'hijri_date'      => $date->format( 'Y-m-d' ),
			                        'is_system_event' => 1,
			                        'is_recurring'    => 1,
			                        'account_id'      => $user->id,
			                        'category_id'     => $category
		                        ] );

		if ( $event->id > 0 ) {
			return response()->json( [
				                         'status'  => 'success',
				                         'message' => 'Event created successfully'
			                         ] );
		}

		return response()->json( [
			                         'status'  => 'danger',
			                         'message' => 'Failed to create event'
		                         ] );
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Event $event
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show( Event $event )
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\Event $event
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function edit( Event $event )
	{
		return view( 'event.edit', [
			'is_system'       => false,
			'event'           => $event,
			'recurring_types' => Config::get( 'constants.recurring.types' )
		] );
	}

	/**
	 * @param Event $event
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function system_edit( Event $event )
	{
		$categories = $this->get_categories();
		if ( ! $event->category_id ) {
			$event->category_id = 'other';
		}

		return view( 'event.edit', [
			'is_system'       => true,
			'event'           => $event,
			'categories'      => $categories,
			'recurring_types' => Config::get( 'constants.recurring.types' )
		] );
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \App\Event $event
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update( Request $request, Event $event )
	{
		//
	}

	public function system_update( Request $request, Event $event )
	{
		$user = Auth::user();

		$validation_rules = [
			"category"      => "required",
			"title"         => "required",
			"islamic.month" => "required",
			"islamic.day"   => "required"
		];

		$validator = Validator::make( $request->all(), $validation_rules );
		$messages  = $validator->messages()->all();

		if ( $validator->fails() ) {
			$error = $messages[0];

			return response()->json( [
				                         'status'  => 'danger',
				                         'message' => $error
			                         ] );
		}

		$date         = Hijri::parse( $request->input( 'islamic.month' ), $request->input( 'islamic.day' ) );
		$category     = $request->input( 'category' );
		$is_recurring = $request->input( 'recurring', '0' ) == '1';

		if ( $category == 'other' ) {
			$category = null;
		}

		$event->fill( [
			              'category_id'  => $category,
			              'hijri_date'   => $date->format( 'Y-m-d' ),
			              'title'        => $request->input( 'title' ),
			              'is_recurring' => $is_recurring ? 1 : 0
		              ] );

		if ( $event->save() ) {
			$meta_keys = Event::getMetaKeys();

			foreach ( $meta_keys as $key ) {
				if ( $request->has( $key ) ) {
					EventMeta::updateOrCreate( [
						                           'event_id' => $event->id,
						                           'key'      => $key,
					                           ], [
						                           'value' => $request->input( $key )
					                           ] );
				}
			}

			return response()->json( [
				                         'status'  => 'success',
				                         'message' => 'Event updated successfully'
			                         ] );
		}

		return response()->json( [
			                         'status'  => 'danger',
			                         'message' => 'Failed to update event'
		                         ] );
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Event $event
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy( Event $event )
	{
		if ( $event->delete() ) {
			return response()->json( [
				                         'status'  => 'success',
				                         'message' => 'Event deleted successfully'
			                         ] );
		}
	}

	/**
	 * @return array
	 */
	private function get_categories()
	{
		$categories = [];
		EventCategory::all()->each( function ( $category ) use ( &$categories ) {
			$categories[ $category->id ] = $category->name;
		} );
		$categories['other'] = 'Other';

		return $categories;
	}
}
