<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'data',
		'url',
		'path',
		'mime_type'
	];

	protected $table = 'files';
	public $timestamps = true;

	/**
	 * @param $value
	 *
	 * @return \Illuminate\Contracts\Routing\UrlGenerator|string
	 */
	public function getUrlAttribute( $value )
	{
		return url( $value );
	}
}
