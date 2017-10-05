<?php

namespace App\Http\Controllers;

use App\Language;
use App\Localization;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Yajra\Datatables\Datatables;
use Dashboard;
use Validator;

class PostController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return view( 'post.index' );
	}

	/**
	 * @return mixed
	 */
	public function data()
	{
		return Datatables::of( Post::with( 'account' )->get() )->make( true );
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		Dashboard::setTitle( 'Create Post' );

		$cite                = Config::get( 'constants.cite' );
		$default_language_id = Language::getDefaultLanguage()->id;
		$languages           = Language::where( 'id', '!=', $default_language_id )->get();

		return view( 'post.create', [
			'languages'           => $languages,
			'default_language_id' => $default_language_id,
			'cite'                => $cite
		] );
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store( Request $request )
	{
		$user = Auth::user();

		$validation_rules = [
			"cite"    => "required",
			"content" => "required"
		];

		$validator = Validator::make( $request->all(), $validation_rules );
		$messages  = $validator->messages()->all();

		if ( $validator->fails() ) {
			$error = $messages[0];

			return response()->json( [
				                         'status'  => 'danger',
				                         'message' => $error
			                         ] );
		}

		$default_language_id = Language::getDefaultLanguage()->id;

		$contents = $request->input( 'content' );
		$cite     = $request->input( 'cite' );

		$post = Post::create( [
			                      'cite'       => $cite,
			                      'content'    => $contents[ $default_language_id ],
			                      'account_id' => $user->id
		                      ] );

		if ( $post->id > 0 ) {
			if ( count( $contents ) > 1 ) {
				foreach ( $contents as $language_id => $content ) {
					if ( $language_id == $default_language_id ) {
						continue;
					}

					Localization::create( [
						                      'item_type'   => Post::TYPE,
						                      'item_value'  => $content,
						                      'item_id'     => $post->id,
						                      'language_id' => $language_id
					                      ] );
				}
			}

			return response()->json( [
				                         'status'  => 'success',
				                         'message' => 'Post created successfully'
			                         ] );
		}

		return response()->json( [
			                         'status'  => 'danger',
			                         'message' => 'Failed to create post'
		                         ] );
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Post $post
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show( Post $post )
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\Post $post
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function edit( Post $post )
	{
		Dashboard::setTitle( 'Edit Post' );

		$cite                = Config::get( 'constants.cite' );
		$default_language_id = Language::getDefaultLanguage()->id;
		$languages           = Language::where( 'id', '!=', $default_language_id )->get();
		$localizations       = $post->get_localizations();

		return view( 'post.edit', [
			'languages'           => $languages,
			'default_language_id' => $default_language_id,
			'cite'                => $cite,
			'post'                => $post,
			'localizations'       => $localizations
		] );
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \App\Post $post
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update( Request $request, Post $post )
	{
		$validation_rules = [
			"cite"    => "required",
			"content" => "required"
		];

		$validator = Validator::make( $request->all(), $validation_rules );
		$messages  = $validator->messages()->all();

		if ( $validator->fails() ) {
			$error = $messages[0];

			return response()->json( [
				                         'status'  => 'danger',
				                         'message' => $error
			                         ] );
		}

		$default_language_id = Language::getDefaultLanguage()->id;

		$contents = $request->input( 'content' );
		$cite     = $request->input( 'cite' );

		$post->fill( [
			             'cite'    => $cite,
			             'content' => $contents[ $default_language_id ]
		             ] );

		if ( $post->save() ) {
			if ( count( $contents ) > 1 ) {
				foreach ( $contents as $language_id => $content ) {
					if ( $language_id == $default_language_id ) {
						continue;
					}

					if ( $content ) {
						$localization             = Localization::firstOrCreate( [
							                                                         'item_type'   => Post::TYPE,
							                                                         'item_id'     => $post->id,
							                                                         'language_id' => $language_id
						                                                         ] );
						$localization->item_value = $content;
						$localization->save();
					}
				}
			}

			return response()->json( [
				                         'status'  => 'success',
				                         'message' => 'Post updated successfully'
			                         ] );
		}

		return response()->json( [
			                         'status'  => 'danger',
			                         'message' => 'Failed to update post'
		                         ] );
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Post $post
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy( Post $post )
	{
		$localizations = Localization::where( 'item_id', '=', $post->id )
		                             ->where( 'item_type', Post::TYPE );
		if ( $post->delete() ) {
			$localizations->delete();

			return response()->json( [
				                         'status'  => 'success',
				                         'message' => 'Post deleted successfully'
			                         ] );
		}

		return response()->json( [
			                         'status'  => 'danger',
			                         'message' => 'Failed to delete post'
		                         ] );
	}
}
