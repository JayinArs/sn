<?php

namespace App\Http\Controllers;

use App\Organization;
use App\OrganizationFollower;
use App\OrganizationLocation;
use App\OrganizationMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use JSONResponse;
use Validator;
use MultiLang;

class ApiFollowerController extends Controller {
	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function add( Request $request ) {
		$validation_rules = [
			'user_id'                   => 'required|exists:users,id',
			'organization_location_ids' => 'required|exists:organization_locations,id'
		];

		$validator = Validator::make( $request->all(), $validation_rules );
		$messages  = $validator->messages()->all();

		if ( $validator->fails() ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhrase( $messages[0] ) );
		}

		$status = [];
		foreach ( $request->input( 'organization_location_ids' ) as $location_id ) {
			$status[ $location_id ] = false;

			if ( ! OrganizationFollower::where( 'organization_location_id', $location_id )
			                           ->where( 'user_id', $request->input( 'user_id' ) )->exists()
			) {

				OrganizationFollower::create( [
					'user_id'                  => $request->input( 'user_id' ),
					'organization_location_id' => $location_id
				] );
				$status[ $location_id ] = true;
			}
		}

		return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $status );
	}

	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function remove( Request $request ) {
		$validation_rules = [
			'user_id'                   => 'required|exists:users,id',
			'organization_location_ids' => 'required|exists:organization_locations,id'
		];

		$validator = Validator::make( $request->all(), $validation_rules );
		$messages  = $validator->messages()->all();

		if ( $validator->fails() ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhrase( $messages[0] ) );
		}

		$removed = OrganizationFollower::whereIn( 'organization_location_id', $request->input( 'organization_location_ids' ) )
		                               ->where( 'user_id', $request->input( 'user_id' ) )
		                               ->delete();
		if ( $removed ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), null, MultiLang::getPhraseByKey( 'strings.follower.remove_success' ) );
		} else {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhraseByKey( 'strings.follower.remove_failed' ) );
		}
	}
}
