<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeedMeta extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'feed_id', 'key', 'value'
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'feed_id', 'id'
	];

	protected $table = 'feed_meta';
	public $timestamps = false;

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function feed()
	{
		return $this->belongsTo('App\Feed', 'feed_id');
	}
}
