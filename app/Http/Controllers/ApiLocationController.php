<?php

namespace App\Http\Controllers;

use App\Event;
use App\Feed;
use App\Organization;
use App\OrganizationFeed;
use App\OrganizationFollower;
use App\OrganizationLocation;
use App\OrganizationMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use JSONResponse;
use Validator;
use MultiLang;
use Pagination;

class ApiLocationController extends Controller
{
	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function add( Request $request )
	{
		$validation_rules = [
			'country'         => 'required',
			'organization_id' => 'required|exists:organizations,id',
			'city'            => 'required_without_all:state|unique:organization_locations,city,NULL,id,organization_id,' . $request->input( 'organization_id' ),
			'state'           => 'required_without_all:city|unique:organization_locations,state,NULL,id,organization_id,' . $request->input( 'organization_id' ),
		];

		$validator = Validator::make( $request->all(), $validation_rules );
		$messages  = $validator->messages()->all();

		if ( $validator->fails() ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhrase( $messages[0] ) );
		}

		$location = OrganizationLocation::create( [
			                                          'organization_id' => $request->input( 'organization_id' ),
			                                          'city'            => $request->input( 'city' ),
			                                          'state'           => $request->input( 'state' ),
			                                          'country'         => $request->input( 'country' )
		                                          ] );

		if ( $location->id > 0 ) {
			$location = OrganizationLocation::with( [ 'organization' ] )->find( $location->id );

			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $location );
		} else {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhraseByKey( 'strings.location.creation_failed' ) );
		}
	}

	/**
	 * @param $organization_id
	 * @param $location_id
	 *
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function events( $organization_id, $location_id, Request $request )
	{
		$limit    = $request->input( 'limit', 5 );
		$paginate = Event::with( [
			                         'meta_data',
			                         'category'
		                         ] )
		                 ->where( 'organization_location_id', $location_id )
		                 ->paginate( $limit );

		return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $paginate->items(), null, [
			"current_page" => $paginate->currentPage(),
			"total_pages"  => ceil( $paginate->total() / $paginate->perPage() )
		] );
	}

	/**
	 * @param $organization_id
	 * @param $location_id
	 *
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function feeds( $organization_id, $location_id, Request $request )
	{
		$feeds = [];

		OrganizationFeed::with( [
			                        'feed',
			                        'feed.user',
			                        'feed.user.meta_data'
		                        ] )->where( 'organization_location_id', $location_id )->each( function ( $feed_relation ) use ( &$feeds ) {
			$feeds[] = $feed_relation->feed;
		} );

		$limit    = $request->input( 'limit', 5 );
		$paginate = Pagination::paginate( $feeds, $limit );

		return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $paginate->items(), null, [
			"current_page" => $paginate->currentPage(),
			"total_pages"  => ceil( $paginate->total() / $paginate->perPage() )
		] );
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function destroy( $id )
	{
		$location = OrganizationLocation::find( $id );

		if ( $location ) {
			OrganizationFollower::where( 'organization_location_id', $location->id )->delete();
			OrganizationFeed::where( 'organization_location_id', $location->id )->delete();

			$location->delete();

			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), null, MultiLang::getPhraseByKey( 'strings.location.deleted' ) );
		} else {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.NOT_FOUND' ), null, MultiLang::getPhraseByKey( 'strings.organization.not_found' ) );
		}
	}
}
