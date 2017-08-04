<?php

namespace App\Http\Controllers;

use App\Event;
use App\EventCategory;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Auth;
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
		                            ->get() )->make( true );
	}

	/**
	 * @return mixed
	 */
	public function system_data()
	{
		return Datatables::of( Event::with( [ 'category', 'organization_location.organization' ] )
		                            ->where( 'is_system_event', 1 )
		                            ->get() )->make( true );
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		return view( 'event.create', [ 'is_system' => false ] );
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function system_create()
	{
		$categories = $this->get_categories();

		return view( 'event.create', [ 'is_system' => true, 'categories' => $categories ] );
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
		//
	}

	public function system_store( Request $request )
	{
		$user = Auth::user();

		$validation_rules = [
			"category"      => "required",
			"title"         => "required",
			"islamic.month" => "required",
			"islamic.day"   => "required",
			"islamic.year"  => "required"
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

		$date     = Hijri::parse( $request->input( 'islamic.month' ), $request->input( 'islamic.day' ), $request->input( 'islamic.year' ) );
		$category = $request->input( 'category' );

		if ( $category == 'other' ) {
			$category = null;
		}

		$event = Event::create( [
			                        'title'           => $request->input( 'title' ),
			                        'hijri_date'      => $date->format( 'Y-m-d' ),
			                        'is_system_event' => 1,
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
		return view( 'event.edit', [ 'is_system' => false, 'event' => $event ] );
	}

	/**
	 * @param Event $event
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function system_edit( Event $event )
	{
		$categories = $this->get_categories();
		if(!$event->category_id)
			$event->category_id = 'other';

		return view( 'event.edit', [ 'is_system' => true, 'event' => $event, 'categories' => $categories ] );
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
			"islamic.day"   => "required",
			"islamic.year"  => "required"
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

		$date     = Hijri::parse( $request->input( 'islamic.month' ), $request->input( 'islamic.day' ), $request->input( 'islamic.year' ) );
		$category = $request->input( 'category' );

		if ( $category == 'other' ) {
			$category = null;
		}

		$event->fill( [
			              'category_id' => $category,
			              'hijri_date'  => $date->format( 'Y-m-d' ),
			              'title'       => $request->input( 'title' )
		              ] );

		if ( $event->save() ) {
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
