<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get( '/', function () {
	return redirect( 'admin/login' );
} );

Route::get( '/admin/login', function () {
	if ( \Illuminate\Support\Facades\Auth::check() ) {
		return redirect( 'admin' );
	}

	return redirect( 'login' );
} );

Route::get( '/privacy', 'HomeController@privacy');

Route::group( [ 'prefix' => 'admin', 'middleware' => 'auth' ], function () {
	Route::get( '/', 'HomeController@index' );
	Route::get( '/dashboard', 'HomeController@index' )->name( 'dashboard' );

	/*
	 * Post Routes
	 */
	Route::group( [ 'prefix' => 'post' ], function () {
		Route::get( 'data', 'PostController@data' )->name( 'post.data' );
	} );
	Route::resource( 'post', 'PostController' );

	/*
	 * Event Routes
	 */
	Route::group( [ 'prefix' => 'event' ], function () {
		Route::get( 'system/data', 'EventController@system_data' )->name( 'event.system.data' ); // System events data
		Route::get( 'data', 'EventController@data' )->name( 'event.data' ); // user events data

		Route::get( 'system/create', 'EventController@system_create' )->name( 'event.system.create' ); // Create system event

		Route::get( 'system', 'EventController@system_list' )->name( 'event.system.list' ); // System events
		Route::post( 'system/store', 'EventController@system_store' )->name( 'event.system.store' ); // Handle create system event

		Route::get( 'system/{event}/edit', 'EventController@system_edit' )->name( 'event.system.edit' ); // Edit system event
		Route::put( 'system/{event}/update', 'EventController@system_update' )->name( 'event.system.update' ); // Handle edit system event
	} );
	Route::resource( 'event', 'EventController' );

	/*
	 * User Routes
	 */
	Route::group( [ 'prefix' => 'user' ], function () {
		Route::get( 'data', 'UserController@data' )->name( 'user.data' ); // User data
	} );
	Route::resource( 'user', 'UserController', [ 'only' => [ 'index', 'show' ] ] );

	/*
	 * Organization Routes
	 */
	Route::group( [ 'prefix' => 'org' ], function () {
		Route::get( 'data', 'OrganizationController@data' )->name( 'organization.data' ); // Organization data
	} );
	Route::resource( 'org', 'OrganizationController' );
} );