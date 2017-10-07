<?php

namespace App\Http\Controllers;

use App\Language;
use App\Localization;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use JSONResponse;

class ApiPostController extends Controller
{
	/**
	 * @return mixed
	 */
	public function random( Request $request )
	{
		$post = Post::with( 'meta_data' )->inRandomOrder()->first();

		if ( $request->hasHeader( 'Content-Type' ) && $post ) {
			$language = Language::where( 'code', $request->header( 'Content-Type' ) )->first();

			if ( $language ) {
				$content = Localization::where( 'item_type', 'post' )
				                       ->where( 'item_id', $post->id )
				                       ->where( 'language_id', $language->id )
				                       ->first( [ 'item_value' ] );
				if ( $content ) {
					$post->content = $content->item_value;
				}
			}
		}

		return JSONResponse::encode( Config::get( 'constants.HTTP_CODES.SUCCESS' ), $post );
	}
}
