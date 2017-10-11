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

	protected $appends = [
		'meta_data'
	];

	protected $hidden = [
		'meta_data_obj'
	];

	protected $table = 'organizations';
	public $timestamps = false;

	/**
	 * @return array|mixed
	 */
	public function getMetaDataAttribute()
	{
		$values = $this->meta_data_obj;

		if ( ! empty( $values ) ) {
			$new_values = [];
			foreach ( $values as $value ) {
				$new_values[ $value["key"] ] = $value["value"];
			}
			$values = $new_values;
		}

		return $values;
	}

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
	public function meta_data_obj()
	{
		return $this->hasMany( 'App\OrganizationMeta', 'organization_id', 'id' );
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function locations()
	{
		return $this->hasMany( 'App\OrganizationLocation', 'organization_id', 'id' );
	}

	/**
	 * @return array
	 */
	public static function getMetaKeys()
	{
		return [ 'image' ];
	}
}
