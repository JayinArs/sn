<?php

namespace App\Http\Controllers;

use App\Event;
use App\Organization;
use App\OrganizationFollower;
use App\OrganizationLocation;
use App\OrganizationMeta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use JSONResponse;
use Validator;
use MultiLang;

class ApiOrganizationController extends Controller
{
	/**
	 * @param null $user_id
	 *
	 * @return mixed
	 */
	public function all($user_id = null)
    {
    	$organizations = [];

    	Organization::with('meta_data')->each(function($organization) use (&$organizations, &$user_id) {
		    $org = $organization->toArray();

		    $org['followers'] = $org['events'] = 0;

		    if($user_id)
		        $org['is_following'] = false;

		    OrganizationLocation::where('organization_id', $organization->id)->each(function($location) use (&$org, &$user_id) {
			    $org['followers'] += OrganizationFollower::where('organization_location_id', $location->id)->count();
			    $org['events'] += Event::where('organization_location_id', $location->id)->count();

			    if($user_id)
				    $org['is_following'] = OrganizationFollower::where('organization_location_id', $location->id)
				                                               ->where('user_id', $user_id)
				                                               ->exists();
		    });

		    $organizations[] = $org;
	    });

    	return JSONResponse::encode(Config::get('constants.HTTP_CODES.SUCCESS'), $organizations);
    }

	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function create(Request $request)
    {
	    $validation_rules = [
		    'name' => 'required',
		    'account_id' => 'required|exists:accounts,id'
	    ];

	    $validator = Validator::make($request->all(), $validation_rules);
	    $messages = $validator->messages()->all();

	    if ($validator->fails())
		    return JSONResponse::encode(Config::get('constants.HTTP_CODES.FAILED'), null, MultiLang::getPhrase($messages[0]));

	    $organization = Organization::create([
	    	'name' => $request->input('name'),
		    'is_official' => 0,
		    'account_id' => $request->input('account_id'),
		    'status' => 1
	    ]);

	    if($organization->id > 0) {
	    	$organization = Organization::with('meta_data')->find($organization->id);
		    return JSONResponse::encode(Config::get('constants.HTTP_CODES.SUCCESS'), $organization);
	    } else {
		    return JSONResponse::encode(Config::get('constants.HTTP_CODES.FAILED'), null, MultiLang::getPhraseByKey('strings.organization.creation_failed'));
	    }
    }

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	private function get_organization($id)
    {
	    $organization = Organization::find($id);
	    if(!$organization)
		    return JSONResponse::encode(Config::get('constants.HTTP_CODES.NOT_FOUND'), null, MultiLang::getPhraseByKey('strings.organization.not_found'));

		return $organization;
    }

	/**
	 * @param $id
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function update($id, Request $request)
	{
		if(!Organization::find($id))
			return JSONResponse::encode(Config::get('constants.HTTP_CODES.NOT_FOUND'), null, MultiLang::getPhraseByKey('strings.organization.not_found'));

		$organization = $this->get_organization($id);
		$data = $request->all();

		$organization->fill($data);

		if($organization->save()) {
			return JSONResponse::encode(Config::get('constants.HTTP_CODES.SUCCESS'), null, MultiLang::getPhraseByKey('strings.organization.update_success'));
		} else {
			return JSONResponse::encode(Config::get('constants.HTTP_CODES.FAILED'), null, MultiLang::getPhraseByKey('strings.organization.update_failed'));
		}
	}

	/**
	 * @param $id
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function report($id, Request $request)
    {
	    if(!Organization::find($id))
		    return JSONResponse::encode(Config::get('constants.HTTP_CODES.NOT_FOUND'), null, MultiLang::getPhraseByKey('strings.organization.not_found'));

	    $validation_rules = [
		    'account_id' => 'required|exists:accounts,id',
		    'user_id' => 'required|exists:users,id',
		    'report_type' => 'required'
	    ];

	    $validator = Validator::make($request->all(), $validation_rules);
	    $messages = $validator->messages()->all();

	    if ($validator->fails())
		    return JSONResponse::encode(Config::get('constants.HTTP_CODES.FAILED'), null, MultiLang::getPhrase($messages[0]));

	    $meta = OrganizationMeta::create([
	    	'key' => 'report',
		    'value' => json_encode([
		    	'account_id' => $request->input('account_id'),
		    	'user_id' => $request->input('user_id'),
			    'report_type' => $request->input('report_type')
		    ]),
		    'organization_id' => $id,
		    'datetime' => Carbon::now()->toDateTimeString()
	    ]);

	    if($meta->id > 0) {
	    	$meta = OrganizationMeta::with('organization')->find($meta->id);
		    return JSONResponse::encode(Config::get('constants.HTTP_CODES.SUCCESS'), $meta);
	    } else {
		    return JSONResponse::encode(Config::get('constants.HTTP_CODES.FAILED'), null, MultiLang::getPhraseByKey('strings.organization.report_failed'));
	    }
    }

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function reports($id)
    {
	    if(!Organization::find($id))
		    return JSONResponse::encode(Config::get('constants.HTTP_CODES.NOT_FOUND'), null, MultiLang::getPhraseByKey('strings.organization.not_found'));

    	$reports = [];
	    OrganizationMeta::with('organization')
	                    ->where('organization_id', $id)
	                    ->where('key', 'report')
	                    ->each(function($report) use (&$reports) {
	                    	$value = json_decode($report->value, true);

	                    	if(!in_array($value, $reports))
	                    	    $reports[] = $value;
	                    });
	    return JSONResponse::encode(Config::get('constants.HTTP_CODES.SUCCESS'), $reports);
    }

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function locations($id)
	{
		$locations = [];

		if(!Organization::find($id))
			return JSONResponse::encode(Config::get('constants.HTTP_CODES.NOT_FOUND'), null, MultiLang::getPhraseByKey('strings.organization.not_found'));

		OrganizationLocation::with('organization')->where('organization_id', $id)->each(function($location) use (&$locations) {
			$loc = $location->toArray();

			$loc['events'] = Event::where('organization_location_id', $location->id)->count();
			$loc['followers'] = OrganizationFollower::where('organization_location_id', $location->id)->count();

			$locations[] = $loc;
		});

		return JSONResponse::encode(Config::get('constants.HTTP_CODES.SUCCESS'), $locations);
	}
}
