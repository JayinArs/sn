<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
	use Notifiable;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'account_id',
		'fcm_id',
		'udid',
		'language_id',
		'status',
		'registration_date',
		'api_token',
		'imei',
		'timezone'
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'api_token'
	];

	protected $table = 'users';
	public $timestamps = false;

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function language()
	{
		return $this->belongsTo( 'App\Language', 'language_id' );
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function meta_data()
	{
		return $this->hasMany( 'App\UserMeta', 'user_id', 'id' );
	}

	/**
	 * @return mixed
	 */
	public function routeNotificationForOneSignal()
	{
		return $this->udid;
	}
}