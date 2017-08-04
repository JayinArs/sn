<?php

namespace App\Http\Controllers;

use App\Event;
use App\OrganizationFollower;
use App\OrganizationLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\User;
use JSONResponse;
use Validator;
use MultiLang;
use App;

class ApiUserController extends Controller
{
	/**
	 * ApiUserController constructor.
	 */
	function __construct()
	{
		//$this->middleware('auth:api');
	}

	/**
	 * @return mixed
	 */
	public function data()
	{
		$users = User::with( 'meta_data' )->get();

		return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $users );
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	private function get_user( $id )
	{
		return User::with( 'meta_data' )->find( $id );
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function getUserById( $id )
	{
		$user = $this->get_user( $id );

		if ( $user ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $user );
		} else {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.NOT_FOUND' ), null, MultiLang::getPhraseByKey( 'strings.user.not_found' ) );
		}
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function deleteUserById( $id )
	{
		$user = $this->get_user( $id );

		if ( ! $user ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.NOT_FOUND' ), null, MultiLang::getPhraseByKey( 'strings.user.not_found' ) );
		}

		if ( $user->delete() ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), null, MultiLang::getPhraseByKey( 'strings.user.destroyed' ) );
		} else {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhraseByKey( 'strings.user.destroy_failed' ) );
		}
	}

	/**
	 * @param $id
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function updateUserById( $id, Request $request )
	{
		$user = $this->get_user( $id );

		$user->fill( $request->all() );

		if ( $user->save() ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), null, MultiLang::getPhraseByKey( 'strings.user.update_success' ) );
		} else {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhraseByKey( 'strings.user.update_failed' ) );
		}
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function userFollowingOrganizations( $id )
	{
		$organizations = [];

		OrganizationFollower::with( [
			                            'organization_location.organization',
			                            'organization_location.organization.meta_data'
		                            ] )->where( 'user_id', $id )->each( function ( $follower ) use ( &$organizations ) {
			$organization = $follower->organization_location->organization;
			$org          = $organization->toArray();

			$org['followers'] = $org['events'] = 0;

			OrganizationLocation::where( 'organization_id', $organization->id )->each( function ( $location ) use ( &$org ) {
				$org['followers'] += OrganizationFollower::where( 'organization_location_id', $location->id )->count();
				$org['events']    += Event::where( 'organization_location_id', $location->id )->count();
			} );

			$organizations[] = $org;
		} );

		return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $organizations );
	}
}