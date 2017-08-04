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
		[ 'label' => 'Dashboard', 'action' => 'admin/dashboard', 'icon' => 'icon-speedometer', 'item_type' => 'item' ],
		[ 'label' => 'Users', 'action' => 'admin/users', 'icon' => 'icon-people', 'item_type' => 'item' ],
		[ 'label' => 'Content Management', 'item_type' => 'heading' ],
		[ 'label' => 'Posts', 'action' => 'admin/post', 'icon' => 'icon-pin', 'item_type' => 'item' ],
		[ 'label' => 'Events', 'icon' => 'icon-notebook', 'item_type' => 'group', 'children' => [
			[ 'label' => 'System Events', 'action' => 'admin/event/system', 'item_type' => 'item' ],
			[ 'label' => 'User Events', 'action' => 'admin/event', 'item_type' => 'item' ],
		] ]
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
	],
	'english' => [
		'months' => [
			'January',
			'February',
			'March',
			'April',
			'May',
			'June',
			'July',
			'August',
			'September',
			'October',
			'November',
			'December'
		]
	],
	'cite' => [
		'Rasool\'Allah (S.A.W.W)' => 'Rasool\'Allah (S.A.W.W)',
		'Bibi Fatima (S.A)' => 'Bibi Fatima (S.A)',
		'Imam Ali (A.S)' => 'Imam Ali (A.S)',
		'Imam Hasan (A.S)' => 'Imam Hasan (A.S)',
		'Imam Hussain (A.S)' => 'Imam Hussain (A.S)',
		'Imam Zain-ul-Abedin (A.S)' => 'Imam Zain-ul-Abedin (A.S)',
		'Imam Baqir (A.S)' => 'Imam Baqir (A.S)',
		'Imam Jafar Sadiq (A.S)' => 'Imam Jafar Sadiq (A.S)',
		'Imam Moosa-e-Kazim (A.S)' => 'Imam Moosa-e-Kazim (A.S)',
		'Imam Ali Raza (A.S)' => 'Imam Ali Raza (A.S)',
		'Imam Mohammad Taqi (A.S)' => 'Imam Mohammad Taqi (A.S)',
		'Imam Ali Naqi (A.S)' => 'Imam Ali Naqi (A.S)',
		'Imam Hassan Askari (A.S)' => 'Imam Hassan Askari (A.S)',
		'Imam Mehdi (A.S)' => 'Imam Mehdi (A.S)'
	]
];