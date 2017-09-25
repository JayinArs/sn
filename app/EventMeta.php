<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EventMeta extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'event_id',
		'key',
		'value'
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'event_id',
		'id'
	];

	protected $table = 'event_meta';
	public $timestamps = false;

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function event()
	{
		return $this->belongsTo( 'App\Event', 'event_id' );
	}
}
