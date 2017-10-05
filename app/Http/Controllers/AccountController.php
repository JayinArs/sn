<?php

namespace App\Http\Controllers;

use App\Event;
use App\Organization;
use App\OrganizationFollower;
use App\OrganizationLocation;
use Illuminate\Http\Request;
use App\Account;
use App\User;
use App\UserRole;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Validator;
use JSONResponse;
use MultiLang;

class AccountController extends Controller
{
	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function login(Request $request)
	{
		$validation_rules = [
			'email' => 'required|exists:accounts,email',
			'password' => 'required',
			'user_id' => 'required|exists:users,id'
		];

		$validator = Validator::make($request->all(), $validation_rules);
		$messages = $validator->messages()->all();

		if ($validator->fails())
			return JSONResponse::encode(Config::get('constants.HTTP_CODES.FAILED'), null, MultiLang::getPhrase($messages[0]));

		$user_id = $request->input('user_id');
		$user = User::find($user_id);

		if (empty($user))
			return JSONResponse::encode(Config::get('constants.HTTP_CODES.NOT_FOUND'), null, MultiLang::getPhraseByKey('strings.user.not_found'));

		if (auth()->attempt([
			'email' => $request->input('email'),
			'password' => $request->input('password')
		])) {

			$account = Auth::user();

			return JSONResponse::encode(Config::get('constants.HTTP_CODES.SUCCESS'), $account);
		}

		return JSONResponse::encode(Config::get('constants.HTTP_CODES.NOT_FOUND'), null, MultiLang::getPhraseByKey('strings.account.not_found'));
	}

	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function register(Request $request)
	{
		$validation_rules = [
			'email' => 'required|unique:accounts,email',
			'password' => 'required',
			'user_id' => 'required|exists:users,id'
		];

		$validator = Validator::make($request->all(), $validation_rules);
		$messages = $validator->messages()->all();

		if ($validator->fails())
			return JSONResponse::encode(Config::get('constants.HTTP_CODES.FAILED'), null, MultiLang::getPhrase($messages[0]));

		$user_id = $request->input('user_id');
		$user = User::find($user_id);

		if (empty($user))
			return JSONResponse::encode(Config::get('constants.HTTP_CODES.NOT_FOUND'), null, MultiLang::getPhraseByKey('strings.user.not_found'));

		$account = Account::create([
			'username' => $request->input('username'),
			'email' => $request->input('email'),
			'password' => bcrypt($request->input('password')),
			'role_id' => UserRole::getDefaultRole()->id,
			'registration_date' => Carbon::now()->toDateTimeString()
		]);

		$user->account_id = $account->id;

		if($account->id > 0 && $user->save()) {
			return JSONResponse::encode(Config::get('constants.HTTP_CODES.SUCCESS'), $account);
		} else {
			return JSONResponse::encode(Config::get('constants.HTTP_CODES.FAILED'), null, MultiLang::getPhraseByKey('strings.account.creation_failed'));
		}
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	private function get_account($id)
	{
		return Account::withTrashed()->find($id);
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function reActivate($id)
	{
		$account = $this->get_account($id);

		$account->status = 1;

		if($account->save() && $account->restore()) {
			return JSONResponse::encode(Config::get('constants.HTTP_CODES.SUCCESS'), null, MultiLang::getPhraseByKey('strings.account.reactivated'));
		} else {
			return JSONResponse::encode(Config::get('constants.HTTP_CODES.FAILED'), null, MultiLang::getPhraseByKey('strings.account.reactivation_failed'));
		}
	}

	/**
	 * @param $id
	 * @param Request $request
	 * @return mixed
	 */
	public function updateAccountById($id, Request $request)
	{
		$account = $this->get_account($id);
		$data = $request->all();

		if(!empty($data['password']))
			$data['password'] = bcrypt($data['password']);

		$account->fill($data);

		if($account->save()) {
			return JSONResponse::encode(Config::get('constants.HTTP_CODES.SUCCESS'), null, MultiLang::getPhraseByKey('strings.account.update_success'));
		} else {
			return JSONResponse::encode(Config::get('constants.HTTP_CODES.FAILED'), null, MultiLang::getPhraseByKey('strings.account.update_failed'));
		}
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function deleteAccountById($id)
	{
		$account = $this->get_account($id);

		if(!$account)
			return JSONResponse::encode(Config::get('constants.HTTP_CODES.NOT_FOUND'), null, MultiLang::getPhraseByKey('strings.account.not_found'));

		$account->status = 0;

		$account->save();
		$account->delete();

		if($account->trashed()) {
			return JSONResponse::encode(Config::get('constants.HTTP_CODES.SUCCESS'), null, MultiLang::getPhraseByKey('strings.account.destroyed'));
		} else {
			return JSONResponse::encode(Config::get('constants.HTTP_CODES.FAILED'), null, MultiLang::getPhraseByKey('strings.account.destroy_failed'));
		}
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function accountOrganizations($id)
	{
		$organizations = [];

		Organization::with('meta_data')->where('account_id', $id)->each(function($organization) use (&$organizations) {
			$org = $organization->toArray();

			$org['followers'] = $org['events'] = 0;

			OrganizationLocation::where('organization_id', $organization->id)->each(function($location) use (&$org) {
				$org['followers'] += OrganizationFollower::where('organization_location_id', $location->id)->count();
				$org['events'] += Event::where('organization_location_id', $location->id)->count();
			});

			$organizations[] = $org;
		});

		return JSONResponse::encode(Config::get('constants.HTTP_CODES.SUCCESS'), $organizations);
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function accountFollowingOrganizations( $id )
	{
		$temp_organizations = [];

		OrganizationFollower::with( [
			                            'organization_location.organization',
			                            'organization_location.organization.meta_data'
		                            ] )->where( 'account_id', $id )->each( function ( $follower ) use ( &$temp_organizations ) {
			$organization = $follower->organization_location->organization;
			$org          = $organization->toArray();

			$org['followers'] = $org['events'] = 0;

			OrganizationLocation::where( 'organization_id', $organization->id )->each( function ( $location ) use ( &$org ) {
				$org['followers'] += OrganizationFollower::where( 'organization_location_id', $location->id )->count();
				$org['events']    += Event::where( 'organization_location_id', $location->id )->count();
			} );

			if ( ! isset( $temp_organizations[ $org['id'] ] ) ) {
				$temp_organizations[ $org['id'] ] = $org;
			}
			$temp_organizations[ $org['id'] ]['locations'][] = $follower->organization_location;
		} );
		$organizations = array_values( $temp_organizations );

		return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $organizations );
	}
}
