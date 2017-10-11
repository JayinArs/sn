<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'content',
		'datetime',
		'user_id'
	];

	protected $appends = [
		'meta_data'
	];

	protected $hidden = [
		'meta_data_obj'
	];

	protected $table = 'feeds';
	public $timestamps = false;

	/**
	 * @return array|mixed
	 */
	public function getMetaDataAttribute()
	{
		$values = $this->meta_data_obj;

		if ( ! empty( $values ) ) {
			$new_values = [];
			foreach ( $values as $value ) {
				$new_values[ $value["key"] ] = $value["value"];
			}
			$values = $new_values;
		}

		return $values;
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo( 'App\User', 'user_id' );
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function organization_feeds()
	{
		return $this->hasMany( 'App\OrganizationFeed', 'feed_id', 'id' );
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function meta_data_obj()
	{
		return $this->hasMany( 'App\FeedMeta', 'feed_id', 'id' );
	}
}
