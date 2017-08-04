<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use JSONResponse;

class ApiPostController extends Controller
{
	/**
	 * @return mixed
	 */
	public function random()
	{
		$post = Post::with('meta_data')->inRandomOrder()->first();
		return JSONResponse::encode(Config::get('constants.HTTP_CODES.SUCCESS'), $post);
	}
}
