<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * Authentication Routes
 */
Route::post( 'v1/login', 'ApiAuthController@login' );
Route::post( 'v1/register', 'ApiAuthController@register' );

/*
 * Language Routes
 */
Route::group( [ 'prefix' => 'v1/language' ], function () {
	Route::get( 'all', 'ApiLanguageController@all' )->name( 'language.all' );
} );

Route::group( [ 'prefix' => 'v1', 'middleware' => 'token' ], function () {
	/**
	 * User Routes
	 */
	Route::group( [ 'prefix' => 'user' ], function () {
		Route::get( 'all', 'ApiUserController@data' )->name( 'user.data' );
		Route::get( '{user_id}', 'ApiUserController@getUserById' )->name( 'user.get' );
		Route::delete( '{user_id}', 'ApiUserController@deleteUserById' )->name( 'user.delete' );
		Route::post( '{user_id}/update', 'ApiUserController@updateUserById' )->name( 'user.update' );
	} );

	/*
	 * Account Routes
	 */
	Route::group( [ 'prefix' => 'account' ], function () {
		Route::post( 'login', 'AccountController@login' )->name( 'account.login' );
		Route::post( 'register', 'AccountController@register' )->name( 'account.register' );
		Route::post( '{account_id}/reactivate', 'AccountController@reActivate' )->name( 'account.reactivate' );
		Route::post( '{account_id}/update', 'AccountController@updateAccountById' )->name( 'account.update' );
		Route::delete( '{account_id}', 'AccountController@deleteAccountById' )->name( 'account.delete' );
		Route::get( '{account_id}/organizations', 'AccountController@accountOrganizations' )->name( 'account.organizations' );
		Route::get( '{account_id}/organizations/following', 'AccountController@accountFollowingOrganizations' )->name( 'account.organizations.following' );
	} );

	/*
	 * Post Routes
	 */
	Route::group( [ 'prefix' => 'post' ], function () {
		Route::get( 'random', 'ApiPostController@random' )->name( 'post.random' );
	} );

	/*
	 * Organization Routes
	 */
	Route::group( [ 'prefix' => 'organization' ], function () {
		Route::get( 'all', 'ApiOrganizationController@all' )->name( 'organization.all' );
		Route::get( 'all/{user_id}', 'ApiOrganizationController@all' )->name( 'organization.all' );
		Route::get( '{organization_id}', 'ApiOrganizationController@getSingleOrganization' )->name( 'organization.get' );
		Route::get( '{organization_id}/user/{user_id}', 'ApiOrganizationController@getSingleOrganization' )->name( 'organization.get.ref.user' );
		Route::post( 'create', 'ApiOrganizationController@create' )->name( 'organization.create' );
		Route::post( '{organization_id}/update', 'ApiOrganizationController@update' )->name( 'organization.update' );
		Route::post( '{organization_id}/report', 'ApiOrganizationController@report' )->name( 'organization.report' );
		Route::get( '{organization_id}/reports', 'ApiOrganizationController@reports' )->name( 'organization.reports' );
		Route::get( '{organization_id}/locations', 'ApiOrganizationController@locations' )->name( 'organization.locations' );
		Route::get( '{organization_id}/followers', 'ApiOrganizationController@followers' )->name( 'organization.followers' );
		Route::get( '{organization_id}/events', 'ApiOrganizationController@events' )->name( 'organization.events' );
		Route::get( '{organization_id}/location/{location_id}/events', 'ApiLocationController@events' )->name( 'organization.location.events' );
		Route::get( '{organization_id}/location/{location_id}/feeds', 'ApiLocationController@feeds' )->name( 'organization.location.feeds' );
		Route::get( '{organization_id}/feeds', 'ApiOrganizationController@feeds' )->name( 'organization.feeds' );

		/*
		 * Organization Location Routes
		 */
		Route::group( [ 'prefix' => 'locations' ], function () {
			Route::post( 'add', 'ApiLocationController@add' )->name( 'location.add' );
		} );

		/*
		 * Organization Follower Routes
		 */
		Route::group( [ 'prefix' => 'followers' ], function () {
			Route::post( 'add', 'ApiFollowerController@add' )->name( 'follower.add' );
			Route::post( 'remove', 'ApiFollowerController@remove' )->name( 'follower.remove' );
		} );
	} );

	/*
	 * Event Routes
	 */
	Route::group( [ 'prefix' => 'event' ], function () {
		Route::post( 'all', 'ApiEventController@all' )->name( 'event.all' );
		Route::post( 'create', 'ApiEventController@create' )->name( 'event.create' );
		Route::get( '{event_id}', 'ApiEventController@getSingle' )->name( 'event.get' );
		Route::get( 'today', 'ApiEventController@getSystemEvents' )->name( 'event.today' );
		Route::get( 'nearby', 'ApiEventController@getNearByEvents' )->name( 'event.nearby' );
		Route::delete( '{event_id}', 'ApiEventController@delete' )->name( 'event.delete' );
		Route::post( '{event_id}', 'ApiEventController@updateEvent' )->name( 'event.update' );
	} );

	/*
	 * Feed Routes
	 */
	Route::group( [ 'prefix' => 'feed' ], function () {
		Route::post( 'create', 'ApiFeedController@create' )->name( 'feed.create' );
		Route::delete( '{feed_id}', 'ApiFeedController@delete' )->name( 'feed.delete' );
	} );

	/*
	 * Calendar Routes
	 */
	Route::group( [ 'prefix' => 'calendar' ], function () {
		Route::post( 'create', 'ApiCalendarController@create' )->name( 'calendar.create' );
		Route::post( 'update', 'ApiCalendarController@update' )->name( 'calendar.update' );
		Route::get( 'all', 'ApiCalendarController@all' )->name( 'calendar.all' );
		Route::get( 'get', 'ApiCalendarController@get' )->name( 'calendar.get' );
	} );
} );

//Route::get( 'v1/calendar/all', 'ApiCalendarController@all' )->name( 'calendar.all' );