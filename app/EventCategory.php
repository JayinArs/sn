<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventCategory extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name'
	];

	protected $table = 'event_categories';
	public $timestamps = false;
}
