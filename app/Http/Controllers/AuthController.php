<?php

namespace App\Http\Controllers;

use App\User;
use Berkayk\OneSignal\OneSignalClient;
use Illuminate\Http\Request;
use JSONResponse;
use MultiLang;
use NotificationChannels\OneSignal\OneSignalButton;
use NotificationChannels\OneSignal\OneSignalMessage;
use Validator;
use Token;
use Illuminate\Support\Facades\Config;
use App\Language;
use Carbon\Carbon;

class AuthController extends Controller
{
	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function login( Request $request )
	{
		$validation_rules = [
			'imei' => 'required|exists:users,imei'
		];

		$validator = Validator::make( $request->all(), $validation_rules );
		$messages  = $validator->messages()->all();

		if ( $validator->fails() ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhrase( $messages[0] ) );
		}

		$user = User::with( 'meta_data' )->where( 'imei', $request->input( 'imei' ) )->first();

		if ( ! empty( $user ) ) {
			$token = Token::updateToken( $user );

			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), [
				'user'  => $user,
				'token' => $token
			] );
		}

		return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.NOT_FOUND' ), null, MultiLang::getPhraseByKey( 'strings.user.not_found' ) );
	}

	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function register( Request $request )
	{
		$validation_rules = [
			'imei' => 'required|unique:users,imei'
		];

		$validator = Validator::make( $request->all(), $validation_rules );
		$messages  = $validator->messages()->all();

		if ( $validator->fails() ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhrase( $messages[0] ) );
		}

		if ( ! $request->input( 'fcm_id' ) && ! $request->input( 'udid' ) ) {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.NOT_FOUND' ), null, MultiLang::getPhraseByKey( 'strings.user.device_id_not_found' ) );
		}

		$user = User::create( [
			                      'imei'              => $request->input( 'imei' ),
			                      'language_id'       => $request->input( 'language_id', Language::getDefaultLanguage()->id ),
			                      'fcm_id'            => $request->input( 'fcm_id' ),
			                      'udid'              => $request->input( 'udid' ),
			                      'api_token'         => str_random( 60 ),
			                      'registration_date' => Carbon::now()->toDateTimeString()
		                      ] );

		if ( $user->id > 0 ) {
			$user = User::with( 'meta_data' )->find( $user->id );

			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $user );
		} else {
			return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.FAILED' ), null, MultiLang::getPhraseByKey( 'strings.user.creation_failed' ) );
		}
	}
}