<?php

namespace App\Http\Controllers;

use App\Event;
use App\File;
use App\Organization;
use App\OrganizationFeed;
use App\OrganizationFollower;
use App\OrganizationLocation;
use App\OrganizationMeta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use JSONResponse;
use Validator;
use MultiLang;
use Pagination;

class ApiOrganizationController extends Controller
{
	/**
	 * @param null $account_id
	 *
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function all( $account_id = null, Request $request )
	{
		$organizations = [];

		Organization::with( [
			                    'locations'
		                    ] )
		            ->orderBy( 'id', 'desc' )
		            ->each( function ( $organization ) use ( &$organizations, &$account_id ) {
			            $org = $organization->toArray();

			            $org['followers'] = $org['events'] = 0;

			            if ( $account_id ) {
				            $org['is_following'] = false;
			            }

			            OrganizationLocation::where( 'organization_id', $organization->id )->each( function ( $location ) use ( &$org, &$account_id ) {
				            $org['followers'] += OrganizationFollower::where( 'organization_location_id', $location->id )->count();
				            $org['events']    += Event::where( 'organization_location_id', $location->id )->count();

				            if ( $account_id ) {
					            $org['is_following'] = OrganizationFollower::where( 'organization_location_id', $location->id )
					                                                       ->where( 'account_id', $account_id )
					                                                       ->exists();
				            }
			            } );

			            $organizations[] = $org;
		            } );

		$limit    = $request->input( 'limit', 5 );
		$paginate = Pagination::paginate( $organizations, $limit );

		return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $paginate->items(), null, [
			"current_page" => $paginate->currentPage(),
			"total_pages"  => ceil( $paginate->total() / $paginate->perPage() )
		] );
	}

	/**
	 * @param $id
	 * @param null $account_id
	 *
	 * @return mixed
	 */
	public function getSingleOrganization( $id, $account_id = null )
	{
		$organization = Organization::with( [ 'locations' ] )->find( $id );

		if ( $organization ) {
			$org = $organization->toArray();

			$org['followers'] = $org['events'] = 0;

			if ( $account_id ) {
				$org['is_following'] = false;
			}

			OrganizationLocation::where( 'organization_id', $organization->id )->each( function ( $location ) use ( &$org, &$account_id ) {
				$org['followers'] += OrganizationFollower::where( 'organization_location_id', $location->id )->count();
				$org['events']    += Event::where( 'organization_location_id', $location->id )->count();

				if ( $account_id ) {
					$org['is_following'] = OrganizationFollower::where( 'organization_location_id', $location->id )
					                                           ->where( 'account_id', $account_id )
					                                           ->exists();
				}
			} );

			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $org );
		}

		return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhraseByKey( 'strings.organization.not_found' ) );
	}

	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function create( Request $request )
	{
		$validation_rules = [
			'name'       => 'required',
			'account_id' => 'required|exists:accounts,id',
			'image'      => 'mimes:jpeg,bmp,png|max:1024'
		];

		$validator = Validator::make( $request->all(), $validation_rules );
		$messages  = $validator->messages()->all();

		if ( $validator->fails() ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhrase( $messages[0] ) );
		}

		$organization = Organization::create( [
			                                      'name'        => $request->input( 'name' ),
			                                      'is_official' => 0,
			                                      'account_id'  => $request->input( 'account_id' ),
			                                      'status'      => 1
		                                      ] );

		if ( $organization->id > 0 ) {

			if ( $request->hasFile( 'image' ) ) {
				$file     = $request->file( 'image' );
				$filename = $organization->id . '.' . $file->clientExtension();
				$path     = Config::get( 'constants.organization.image_path' );
				$f        = $file->move( public_path( $path ), $filename );

				if ( $f->isReadable() ) {
					$file = File::create( [
						                      'data'      => "data:image/jpg;base64," . base64_encode( file_get_contents( $f->getRealPath() ) ),
						                      'url'       => $path . $filename,
						                      'path'      => $f->getPath(),
						                      'mime_type' => $f->getMimeType()
					                      ] );

					if ( $file->id > 0 ) {
						$file = File::find( $file->id );

						OrganizationMeta::updateOrCreate( [
							                                  'organization_id' => $organization->id,
							                                  'key'             => 'image'
						                                  ], [
							                                  'value' => json_encode( [
								                                                          'id'  => $file->id,
								                                                          'url' => $file->url
							                                                          ] )
						                                  ] );
					}
				}
			}

			$organization = Organization::find( $organization->id );

			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $organization );
		} else {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhraseByKey( 'strings.organization.creation_failed' ) );
		}
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	private function get_organization( $id )
	{
		$organization = Organization::find( $id );
		if ( ! $organization ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.NOT_FOUND' ), null, MultiLang::getPhraseByKey( 'strings.organization.not_found' ) );
		}

		return $organization;
	}

	/**
	 * @param $id
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function update( $id, Request $request )
	{
		if ( ! Organization::find( $id ) ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.NOT_FOUND' ), null, MultiLang::getPhraseByKey( 'strings.organization.not_found' ) );
		}

		$organization = $this->get_organization( $id );
		$data         = $request->all();

		$organization->fill( $data );

		if ( $organization->save() ) {
			if ( $request->hasFile( 'image' ) ) {
				$file     = $request->file( 'image' );
				$filename = $organization->id . '.' . $file->clientExtension();
				$path     = Config::get( 'constants.organization.image_path' );
				$f        = $file->move( public_path( $path ), $filename );

				if ( $f->isReadable() ) {
					$file = File::create( [
						                      'data'      => "data:image/jpg;base64," . base64_encode( file_get_contents( $f->getRealPath() ) ),
						                      'url'       => $path . $filename,
						                      'path'      => $f->getPath(),
						                      'mime_type' => $f->getMimeType()
					                      ] );

					if ( $file->id > 0 ) {
						$file = File::find( $file->id );

						OrganizationMeta::updateOrCreate( [
							                                  'organization_id' => $organization->id,
							                                  'key'             => 'image'
						                                  ], [
							                                  'value' => json_encode( [
								                                                          'id'  => $file->id,
								                                                          'url' => $file->url
							                                                          ] )
						                                  ] );
					}
				}
			}

			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), null, MultiLang::getPhraseByKey( 'strings.organization.update_success' ) );
		} else {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhraseByKey( 'strings.organization.update_failed' ) );
		}
	}

	/**
	 * @param $id
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function report( $id, Request $request )
	{
		if ( ! Organization::find( $id ) ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.NOT_FOUND' ), null, MultiLang::getPhraseByKey( 'strings.organization.not_found' ) );
		}

		$validation_rules = [
			'account_id'  => 'required|exists:accounts,id',
			'user_id'     => 'required|exists:users,id',
			'report_type' => 'required'
		];

		$validator = Validator::make( $request->all(), $validation_rules );
		$messages  = $validator->messages()->all();

		if ( $validator->fails() ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhrase( $messages[0] ) );
		}

		$meta = OrganizationMeta::create( [
			                                  'key'             => 'report',
			                                  'value'           => json_encode( [
				                                                                    'account_id'  => $request->input( 'account_id' ),
				                                                                    'user_id'     => $request->input( 'user_id' ),
				                                                                    'report_type' => $request->input( 'report_type' )
			                                                                    ] ),
			                                  'organization_id' => $id,
			                                  'datetime'        => Carbon::now()->toDateTimeString()
		                                  ] );

		if ( $meta->id > 0 ) {
			$meta = OrganizationMeta::with( 'organization' )->find( $meta->id );

			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $meta );
		} else {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhraseByKey( 'strings.organization.report_failed' ) );
		}
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function reports( $id )
	{
		if ( ! Organization::find( $id ) ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.NOT_FOUND' ), null, MultiLang::getPhraseByKey( 'strings.organization.not_found' ) );
		}

		$reports = [];
		OrganizationMeta::with( 'organization' )
		                ->where( 'organization_id', $id )
		                ->where( 'key', 'report' )
		                ->each( function ( $report ) use ( &$reports ) {
			                $value = json_decode( $report->value, true );

			                if ( ! in_array( $value, $reports ) ) {
				                $reports[] = $value;
			                }
		                } );

		return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $reports );
	}

	/**
	 * @param $id
	 *
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function locations( $id, Request $request )
	{
		$locations = [];

		if ( ! Organization::find( $id ) ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.NOT_FOUND' ), null, MultiLang::getPhraseByKey( 'strings.organization.not_found' ) );
		}

		OrganizationLocation::with( 'organization' )->where( 'organization_id', $id )->each( function ( $location ) use ( &$locations ) {
			$loc = $location->toArray();

			$loc['events']    = Event::where( 'organization_location_id', $location->id )->count();
			$loc['followers'] = OrganizationFollower::where( 'organization_location_id', $location->id )->count();

			$locations[] = $loc;
		} );

		$limit    = $request->input( 'limit', 5 );
		$paginate = Pagination::paginate( $locations, $limit );

		return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $paginate->items(), null, [
			"current_page" => $paginate->currentPage(),
			"total_pages"  => ceil( $paginate->total() / $paginate->perPage() )
		] );
	}

	/**
	 * @param $organization_id
	 *
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function feeds( $organization_id, Request $request )
	{
		$feeds = [];

		$organization = Organization::with( 'locations' )->find( $organization_id );

		if ( $organization ) {
			foreach ( $organization->locations as $location ) {
				OrganizationFeed::with( [
					                        'feed',
					                        'feed.user'
				                        ] )
				                ->where( 'organization_location_id', $location->id )
				                ->orderBy( 'id', 'desc' )
				                ->distinct()
				                ->each( function ( $feed_relation ) use ( &$feeds, &$organization ) {
					                $feed                   = $feed_relation->feed->toArray();
					                $feed['organization']   = $organization;
					                $feed['human_readable'] = Carbon::parse( $feed["datetime"] )->diffForHumans( null, false, true );
					                $feeds[]                = $feed;
				                } );
			}
		}

		$limit    = $request->input( 'limit', 5 );
		$paginate = Pagination::paginate( $feeds, $limit );

		return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $paginate->items(), null, [
			"current_page" => $paginate->currentPage(),
			"total_pages"  => ceil( $paginate->total() / $paginate->perPage() )
		] );
	}

	/**
	 * @param $organization_id
	 *
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function events( $organization_id, Request $request )
	{
		$events = [];

		$organization = Organization::find( $organization_id );

		if ( $organization ) {
			foreach ( $organization->locations as $location ) {
				Event::with( [
					             'organization_location'
				             ] )
				     ->where( 'organization_location_id', $location->id )
				     ->orderBy( 'hijri_date', 'asc' )
				     ->orderBy( 'english_date', 'asc' )
				     ->each( function ( $event ) use ( &$events, &$organization ) {
					     $event                 = $event->toArray();
					     $event['organization'] = $organization;
					     $events[]              = $event;
				     } );
			}
		}

		$limit    = $request->input( 'limit', 5 );
		$paginate = Pagination::paginate( $events, $limit );

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
		$organization = Organization::find( $id );

		if ( $organization ) {
			$locations = OrganizationLocation::where( 'organization_id', $id );
			$metas     = OrganizationMeta::where( 'organization_id', $id );

			$locations->each( function ( $location ) {
				OrganizationFollower::where( 'organization_location_id', $location->id )->delete();
				OrganizationFeed::where( 'organization_location_id', $location->id )->delete();
			} );

			$locations->delete();
			$metas->delete();
			$organization->delete();

			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), null, MultiLang::getPhraseByKey( 'strings.organization.deleted' ) );
		} else {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.NOT_FOUND' ), null, MultiLang::getPhraseByKey( 'strings.organization.not_found' ) );
		}
	}
}
