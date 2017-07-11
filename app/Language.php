<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
    protected $fillable = [
        'name', 'code'
    ];

    protected $table = "languages";
	public $timestamps = false;

    /**
     * @return mixed
     */
    public function getInitials()
    {
    	$code = explode('-', $this->code);
    	return $code[0];
    }

    /**
     * @return mixed
     */
    public static function getDefaultLanguage()
    {
    	return Language::where('code', 'en-US')->get()->first();
    }
}