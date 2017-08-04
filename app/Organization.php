<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'is_official',
		'account_id',
		'status'
	];

	protected $table = 'organizations';
	public $timestamps = false;

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function account()
	{
		return $this->belongsTo( 'App\Account', 'account_id' );
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function meta_data()
	{
		return $this->hasMany( 'App\OrganizationMeta', 'organization_id', 'id' );
	}
}
