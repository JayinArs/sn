<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganizationFeed extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'organization_location_id', 'feed_id'
	];

	protected $table = 'organization_feeds';
	public $timestamps = false;

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function organization()
	{
		return $this->belongsTo('App\OrganizationLocation', 'organization_location_id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function feed()
	{
		return $this->belongsTo('App\Feed', 'feed_id');
	}
}
