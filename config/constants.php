<?php

return [
	"HTTP_CODES"     => [
		"UNAUTHORIZED"   => 401,
		"NOT_FOUND"      => 404,
		"INTERNAL_ERROR" => 500,
		"SUCCESS"        => 200,
		"FAILED"         => 403
	],
	'google_api_key' => 'AIzaSyCYTnHv1sYw3ikSbhgU7BPccKZAT_Ook64',
	'navigations'    => [
		[ 'label' => 'Main', 'item_type' => 'heading' ],
		[ 'label' => 'Dashboard', 'action' => 'admin/dashboard', 'icon' => 'icon-speedometer', 'item_type' => 'item' ],
		[
			'label'     => 'Users',
			'icon'      => 'icon-people',
			'item_type' => 'group',
			'action'    => 'users',
			'children'  => [
				[ 'label' => 'Devices', 'action' => 'admin/user', 'item_type' => 'item' ],
				[ 'label' => 'Accounts', 'action' => 'admin/account', 'item_type' => 'item' ]
			]
		],
		[ 'label' => 'Content Management', 'item_type' => 'heading' ],
		[ 'label' => 'Posts', 'action' => 'admin/post', 'icon' => 'icon-pin', 'item_type' => 'item' ],
		[ 'label' => 'Circles', 'action' => 'admin/org', 'icon' => 'icon-globe', 'item_type' => 'item' ],
		[
			'label'     => 'Events',
			'icon'      => 'icon-notebook',
			'item_type' => 'group',
			'action'    => 'events',
			'children'  => [
				[ 'label' => 'System Events', 'action' => 'admin/event/system', 'item_type' => 'item' ],
				[ 'label' => 'User Events', 'action' => 'admin/event', 'item_type' => 'item' ],
			]
		]
	],
	'hijri'          => [
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
	'english'        => [
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
	'cite'           => [
		'Rasool\'Allah (S.A.W.W)'   => 'Rasool\'Allah (S.A.W.W)',
		'Bibi Fatima (S.A)'         => 'Bibi Fatima (S.A)',
		'Imam Ali (A.S)'            => 'Imam Ali (A.S)',
		'Imam Hasan (A.S)'          => 'Imam Hasan (A.S)',
		'Imam Hussain (A.S)'        => 'Imam Hussain (A.S)',
		'Imam Zain-ul-Abedin (A.S)' => 'Imam Zain-ul-Abedin (A.S)',
		'Imam Baqir (A.S)'          => 'Imam Baqir (A.S)',
		'Imam Jafar Sadiq (A.S)'    => 'Imam Jafar Sadiq (A.S)',
		'Imam Moosa-e-Kazim (A.S)'  => 'Imam Moosa-e-Kazim (A.S)',
		'Imam Ali Raza (A.S)'       => 'Imam Ali Raza (A.S)',
		'Imam Mohammad Taqi (A.S)'  => 'Imam Mohammad Taqi (A.S)',
		'Imam Ali Naqi (A.S)'       => 'Imam Ali Naqi (A.S)',
		'Imam Hassan Askari (A.S)'  => 'Imam Hassan Askari (A.S)',
		'Imam Mehdi (A.S)'          => 'Imam Mehdi (A.S)'
	],
	'recurring'      => [
		'types' => [
			'yearly'          => 'Yearly',
			'every-sunday'    => 'Every Sunday',
			'every-monday'    => 'Every Monday',
			'every-tuesday'   => 'Every Tuesday',
			'every-wednesday' => 'Every Wednesday',
			'every-thursday'  => 'Every Thursday',
			'every-friday'    => 'Every Friday',
			'every-saturday'  => 'Every Saturday'
		]
	]
];