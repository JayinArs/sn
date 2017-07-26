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

	private function push_event( Event $event )
	{
		Artisan::call( "event:add", [
			'id' => $event->id
		] );
	}

	private function push_system_events( $args )
	{
		Artisan::call( "event:notify", [
			'--system',
			'--timezone' => $args['timezone'],
			'date'       => $args['date']
		] );
	}

	public function notify( $type, $args )
	{
		switch ( $type ) {
			case "event":
				$this->push_event( $args );
				break;
			case "system_events":
				$this->push_system_events( $args );
				break;
		}
	}
}