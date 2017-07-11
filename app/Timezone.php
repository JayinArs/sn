<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Timezone extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name'
	];

	protected $table = 'timezones';
	public $timestamps = false;
}
