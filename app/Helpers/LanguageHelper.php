<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App;

class LanguageHelper
{
	public function __construct()
	{

	}

	public function getPhrase($string)
	{
		$resources = ['strings', 'validation'];

		foreach($resources as $res) {
			$strings = trans($res);

			foreach($strings as $key => $str) {
				if($key == $string)
					return $str;
			}
		}
		return $string;
	}

	public function getPhraseByKey($key)
	{
		return __($key);
	}
}