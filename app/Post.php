<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
	const TYPE = 'post';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'content',
		'cite',
		'account_id'
	];

	protected $table = 'posts';
	public $timestamps = false;

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function account()
	{
		return $this->belongsTo( 'App\Account', 'account_id' );
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function meta_data()
	{
		return $this->hasMany( 'App\PostMeta', 'post_id', 'id' );
	}

	/**
	 * @return array
	 */
	public function get_localizations()
	{
		$localizations = [];
		Language::all()->each( function ( $language ) use ( &$localizations ) {
			$localizations[ $language->id ] = false;
		} );

		Localization::where( 'item_id', '=', $this->id )
		            ->where( 'item_type', Post::TYPE )
		            ->each( function ( $localization ) use ( &$localizations ) {
			            $localizations[ $localization->language_id ] = $localization->item_value;
		            } );

		return $localizations;
	}
}
