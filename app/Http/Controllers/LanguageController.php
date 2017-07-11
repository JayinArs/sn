<?php

namespace App\Http\Controllers;

use App\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use JSONResponse;

class LanguageController extends Controller
{
	/**
	 * @return mixed
	 */
	public function all()
	{
		return JSONResponse::encode(Config::get('constants.HTTP_CODES.SUCCESS'), Language::all());
	}
}
