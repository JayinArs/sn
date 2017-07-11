<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\User;
use Carbon\Carbon;

class TokenHelper
{
    /**
     * TokenHelper constructor.
     */
    public function __construct()
	{

	}

    /**
     * @param $token
     * @return bool
     */
    public function verifyToken($token)
	{
		$user = User::where('api_token', $token)->with('language')->get()->first();
		return (!empty($user) ? $user : false);
	}

    /**
     * @param User $user
     * @return mixed|string
     */
    public function updateToken(User $user)
	{
		$user->api_token = str_random(60);
		$user->save();

		return $user->api_token;
	}
}