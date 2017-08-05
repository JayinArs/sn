<?php

namespace App\Http\Controllers;

use App\Account;
use App\Event;
use App\Organization;
use App\OrganizationFollower;
use App\OrganizationLocation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Datatables;
use Dashboard;
use Validator;
use Illuminate\Support\Facades\Auth;

class OrganizationController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return view( 'organization.index' );
	}

	public function data()
	{
		$organizations = [];

		Organization::with( 'meta_data' )->each( function ( $organization ) use ( &$organizations ) {
			$org = $organization->toArray();

			$org['followers'] = $org['events'] = 0;

			OrganizationLocation::where( 'organization_id', $organization->id )->each( function ( $location ) use ( &$org ) {
				$org['followers'] += OrganizationFollower::where( 'organization_location_id', $location->id )->count();
				$org['events']    += Event::where( 'organization_location_id', $location->id )->count();
			} );

			$organizations[] = $org;
		} );

		return Datatables::of( new Collection( $organizations ) )->make( true );
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		Dashboard::setTitle( 'New Circle' );

		$accounts = [];

		Account::all()->each( function ( $account ) use ( &$accounts ) {
			$accounts[ $account->id ] = $account->username;
		} );

		return view( 'organization.create', [ 'accounts' => $accounts ] );
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
		$data = $request->only( [ 'is_official', 'name', 'account_id' ] );
		$user = Auth::user();

		$validation_rules = [
			"name"       => "required",
			"account_id" => "required|exists:accounts,id",
		];

		$validator = Validator::make( $data, $validation_rules );
		$messages  = $validator->messages()->all();

		if ( $validator->fails() ) {
			$error = $messages[0];

			return response()->json( [
				                         'status'  => 'danger',
				                         'message' => $error
			                         ] );
		}

		return response()->json( [
			                         'status'  => 'warning',
			                         'message' => 'Sorry! This feature is under development'
		                         ] );
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Organization $organization
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show( Organization $organization )
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\Organization $organization
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function edit( Organization $organization )
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \App\Organization $organization
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update( Request $request, Organization $organization )
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Organization $organization
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy( Organization $organization )
	{
		if ( $organization->delete() ) {
			return response()->json( [
				                         'status'  => 'success',
				                         'message' => 'Organization deleted successfully'
			                         ] );
		}
	}
}
