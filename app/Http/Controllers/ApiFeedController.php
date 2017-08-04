<?php

namespace App\Http\Controllers;

use App\Feed;
use App\OrganizationFeed;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Validator;
use JSONResponse;
use MultiLang;

class ApiFeedController extends Controller
{
	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function create( Request $request )
	{
		$validation_rules = [
			'content'                   => 'required',
			'user_id'                   => 'required|exists:users,id',
			'organization_location_ids' => 'required|exists:organization_locations,id'
		];

		$validator = Validator::make( $request->all(), $validation_rules );
		$messages  = $validator->messages()->all();

		if ( $validator->fails() ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhrase( $messages[0] ) );
		}

		$feed = Feed::create( [
			                      'content'  => $request->input( 'content' ),
			                      'datetime' => Carbon::now()->toDateTimeString(),
			                      'user_id'  => $request->input( 'user_id' )
		                      ] );

		if ( $feed->id > 0 ) {
			$status = [];

			foreach ( $request->input( 'organization_location_ids' ) as $location_id ) {
				$status[ $location_id ] = false;

				if ( ! OrganizationFeed::where( 'organization_location_id', $location_id )
				                       ->where( 'feed_id', $feed->id )->exists()
				) {
					OrganizationFeed::create( [
						                          'feed_id'                  => $feed->id,
						                          'organization_location_id' => $location_id
					                          ] );
					$status[ $location_id ] = true;
				}
			}

			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $status );
		} else {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhraseByKey( 'strings.feed.creation_failed' ) );
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
		$feed    = Feed::find( $id );

		if ( $feed ) {
			$deleted = $feed->delete();
		} else {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.NOT_FOUND' ), null, MultiLang::getPhraseByKey( 'strings.feed.not_found' ) );
		}

		if ( $deleted ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), null, MultiLang::getPhraseByKey( 'strings.feed.deleted' ) );
		} else {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhraseByKey( 'strings.feed.deletion_failed' ) );
		}
	}
}
