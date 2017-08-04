<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Localization extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'item_type',
		'item_value',
		'item_id',
		'language_id'
	];

	protected $table = 'localization';
	public $timestamps = false;

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function language()
	{
		return $this->belongsTo( 'App\Language', 'language_id' );
	}
}
