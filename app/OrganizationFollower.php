<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganizationFollower extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'organization_location_id', 'account_id'
	];

	protected $table = 'organization_followers';
	public $timestamps = false;

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function organization_location()
	{
		return $this->belongsTo('App\OrganizationLocation', 'organization_location_id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo('App\Account', 'account_id');
	}
}
