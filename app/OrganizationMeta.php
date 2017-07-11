<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganizationMeta extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'organization_id', 'key', 'value'
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'organization_id', 'id'
	];

	protected $table = 'organization_meta';
	public $timestamps = false;

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function organization()
	{
		return $this->belongsTo('App\Organization', 'organization_id');
	}
}
