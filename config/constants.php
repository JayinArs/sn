<?php

return [
	"HTTP_CODES"  => [
		"UNAUTHORIZED"   => 401,
		"NOT_FOUND"      => 404,
		"INTERNAL_ERROR" => 500,
		"SUCCESS"        => 200,
		"FAILED"         => 403
	],
	'navigations' => [
		[ 'label' => 'Main', 'item_type' => 'heading' ],
		[ 'label' => 'Dashboard', 'action' => 'dashboard', 'icon' => 'icon-speedometer', 'item_type' => 'item' ]
	],
	'hijri'       => [
		'months' => [
			'Muharram',
			'Safar',
			'Rabi-Al-Awwal',
			'Rabi-Al-Thani',
			'Jumada-Al-Awwal',
			'Jumada-Al-Thani',
			'Rajab',
			'Shaban',
			'Ramadan',
			'Shawwal',
			'Zul-Qaadah',
			'Zul-Hijjah'
		]
	]
];