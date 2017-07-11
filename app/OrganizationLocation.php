<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganizationLocation extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'organization_id', 'city', 'state', 'country'
	];

	protected $table = 'organization_locations';
	public $timestamps = false;

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function organization()
	{
		return $this->belongsTo('App\Organization', 'organization_id');
	}
}
