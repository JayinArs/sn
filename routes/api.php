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
Route::post('v1/login', 'AuthController@login');
Route::post('v1/register', 'AuthController@register');

Route::group(['prefix' => 'v1', 'middleware' => 'token'], function() {
	/**
	 * User Routes
	 */
	Route::group( [ 'prefix' => 'user' ], function () {
		Route::get( 'all', 'UserController@data' )->name( 'user.data' );
		Route::get( '{user_id}', 'UserController@getUserById' )->name( 'user.get' );
		Route::delete( '{user_id}', 'UserController@deleteUserById' )->name( 'user.delete' );
		Route::post( '{user_id}/update', 'UserController@updateUserById' )->name( 'user.update' );
		Route::get( '{user_id}/organizations/following', 'UserController@userFollowingOrganizations' )->name( 'user.organizations.following' );
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
	} );

	/*
	 * Post Routes
	 */
	Route::group( [ 'prefix' => 'post' ], function () {
		Route::get( 'random', 'PostController@random' )->name( 'post.random' );
	} );

	/*
	 * Language Routes
	 */
	Route::group( [ 'prefix' => 'language' ], function () {
		Route::get( 'all', 'LanguageController@all' )->name( 'language.all' );
	} );

	/*
	 * Organization Routes
	 */
	Route::group( [ 'prefix' => 'organization' ], function () {
		Route::get( 'all', 'OrganizationController@all' )->name( 'organization.all' );
		Route::get( 'all/{user_id}', 'OrganizationController@all' )->name( 'organization.all' );
		Route::post( 'create', 'OrganizationController@create' )->name( 'organization.create' );
		Route::post( '{organization_id}/update', 'OrganizationController@update' )->name( 'organization.update' );
		Route::post( '{organization_id}/report', 'OrganizationController@report' )->name( 'organization.report' );
		Route::get( '{organization_id}/reports', 'OrganizationController@reports' )->name( 'organization.reports' );
		Route::get( '{organization_id}/locations', 'OrganizationController@locations' )->name( 'organization.locations' );
		Route::get( '{organization_id}/followers', 'OrganizationController@followers' )->name( 'organization.followers' );
		Route::get( '{organization_id}/location/{location_id}/events', 'LocationController@events' )->name( 'organization.location.events' );
		Route::get( '{organization_id}/location/{location_id}/feeds', 'LocationController@feeds' )->name( 'organization.location.feeds' );

		/*
		 * Organization Location Routes
		 */
		Route::group( [ 'prefix' => 'locations' ], function () {
			Route::post( 'add', 'LocationController@add' )->name( 'location.add' );
		} );

		/*
		 * Organization Follower Routes
		 */
		Route::group( [ 'prefix' => 'followers' ], function () {
			Route::post( 'add', 'FollowerController@add' )->name( 'follower.add' );
			Route::post( 'remove', 'FollowerController@remove' )->name( 'follower.remove' );
		} );
	} );

	/*
	 * Event Routes
	 */
	Route::group( [ 'prefix' => 'event' ], function () {
		Route::post( 'all', 'EventController@all' )->name( 'event.all' );
		Route::post( 'create', 'EventController@create' )->name( 'event.create' );
		Route::delete( '{event_id}', 'EventController@delete' )->name( 'event.delete' );
	} );

	/*
	 * Feed Routes
	 */
	Route::group( [ 'prefix' => 'feed' ], function () {
		Route::post( 'create', 'FeedController@create' )->name( 'feed.create' );
		Route::delete( '{feed_id}', 'FeedController@delete' )->name( 'feed.delete' );
	} );

	/*
	 * Calendar Routes
	 */
	Route::group( [ 'prefix' => 'calendar' ], function () {
		Route::post( 'create', 'CalendarController@create' )->name( 'calendar.create' );
		//Route::get( 'all', 'CalendarController@all' )->name( 'calendar.all' );
	});
});

Route::get( 'v1/calendar/all', 'CalendarController@all' )->name( 'calendar.all' );