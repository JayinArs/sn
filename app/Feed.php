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

	protected $table = 'feeds';
	public $timestamps = false;

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
}
