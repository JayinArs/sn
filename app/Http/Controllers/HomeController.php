<?php

namespace App\Http\Controllers;

use App\Event;
use App\Organization;
use App\OrganizationLocation;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware( 'auth' );
		$this->title = 'Dashboard';
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$user = Auth::user();

		$users         = User::all()->count();
		$active_users  = User::where( 'status', 'active' )->count();
		$organizations = Organization::all()->count();
		$events        = Event::all()->count();
		$locations     = OrganizationLocation::all()->count();

		return view( 'home', [
			'users'            => $users,
			'active_users'     => $active_users,
			'organizations'    => $organizations,
			'followers'        => 0,
			'events'           => $events,
			'top_organization' => '',
			'accounts'         => 0,
			'locations'        => $locations
		] );
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function privacy()
	{
		return view( 'privacy' );
	}
}
