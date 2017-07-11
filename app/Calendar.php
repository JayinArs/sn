<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'country', 'city', 'current_date', 'timezone', 'next_update_time', 'last_updated'
	];

	protected $table = 'calendars';
	public $timestamps = false;
}
