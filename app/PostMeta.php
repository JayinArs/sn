<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostMeta extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'post_id', 'key', 'value'
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'post_id', 'id'
	];

	protected $table = 'post_meta';
	public $timestamps = false;

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function post()
	{
		return $this->belongsTo('App\Post', 'post_id');
	}
}
