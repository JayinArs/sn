<?php

namespace App\Helpers;

use App\Event;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\User;
use Carbon\Carbon;

class PushNotificationHelper
{
	/**
	 * PushNotificationHelper constructor.
	 */
	public function __construct()
	{

	}

	private function pushEvent( Event $event )
	{
		Artisan::call( "event:add", [
			'id' => $event->id
		] );
	}

	public function notify( $type, Event $event )
	{
		switch ( $type ) {
			case "event":
				$this->pushEvent( $event );
				break;
		}
	}
}