<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Hijri;

class Event extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'title',
		'english_date',
		'hijri_date',
		'start_time',
		'end_time',
		'is_system_event',
		'is_recurring',
		'organization_location_id',
		'user_id',
		'account_id',
		'category_id',
		'venue',
		'latitude',
		'longitude'
	];

	protected $appends = [
		'meta_data'
	];

	protected $hidden = [
		'meta_data_obj'
	];

	protected $table = 'events';
	public $timestamps = false;

	/**
	 * @param $value
	 *
	 * @return string
	 */
	public function getEnglishDateAttribute( $value )
	{
		if ( $this->is_recurring && ! empty( $value ) ) {
			$date = Carbon::parse( $value );
			$date->setDate( Carbon::now()->year, $date->month, $date->day );

			$value = $date->toDateString();
		}

		return $value;
	}

	/**
	 * @param $value
	 *
	 * @return mixed
	 */
	public function getHijriDateAttribute( $value )
	{
		if ( $this->is_recurring && ! empty( $value ) ) {
			$date       = Carbon::parse( $value );
			$hijri_date = Hijri::parse( $date->month, $date->day );

			$value = $hijri_date->toDateString();
		}

		return $value;
	}

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
	public function organization_location()
	{
		return $this->belongsTo( 'App\OrganizationLocation', 'organization_location_id' );
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo( 'App\User', 'user_id' );
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function account()
	{
		return $this->belongsTo( 'App\Account', 'account_id' );
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function category()
	{
		return $this->belongsTo( 'App\EventCategory', 'category_id' );
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function meta_data_obj()
	{
		return $this->hasMany( 'App\EventMeta', 'event_id', 'id' );
	}

	/**
	 * @return array
	 */
	public static function getMetaKeys()
	{
		return [ 'recurring_type' ];
	}
}
