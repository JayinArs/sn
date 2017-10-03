<?php

namespace App\Notifications;

use App\Event;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalButton;
use NotificationChannels\OneSignal\OneSignalChannel;
use Hijri;
use NotificationChannels\OneSignal\OneSignalMessage;

class EventDate extends Notification
{
	use Queueable;

	private $event;

	/**
	 * EventDate constructor.
	 *
	 * @param Event $event
	 */
	public function __construct( Event $event )
	{
		$this->event = $event;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed $notifiable
	 *
	 * @return array
	 */
	public function via( $notifiable )
	{
		return [ OneSignalChannel::class ];
	}

	/**
	 * Get the mail representation of the notification.
	 *
	 * @param  mixed $notifiable
	 *
	 * @return \Illuminate\Notifications\Messages\MailMessage
	 */
	public function toMail( $notifiable )
	{
		return ( new MailMessage )
			->line( 'The introduction to the notification.' )
			->action( 'Notification Action', url( '/' ) )
			->line( 'Thank you for using our application!' );
	}

	/**
	 * @param $notifiable
	 *
	 * @return mixed
	 */
	public function toOneSignal( $notifiable )
	{
		if ( empty( $this->event->hijri_date ) ) {
			$date = $this->event->english_date;
			$date = Carbon::parse( $date );

			$date = $date->format( 'j F Y' );
		} else {
			$date = Hijri::format_date( $this->event->hijri_date );
		}

		$view_button = OneSignalButton::create( 'single-event' )
		                              ->text( 'View Event' );

		return OneSignalMessage::create()
		                       ->subject( "Today's Event: {$this->event->title}'" )
		                       ->body( "Dated: {$date}" )
		                       ->button( $view_button );
	}

	/**
	 * Get the array representation of the notification.
	 *
	 * @param  mixed $notifiable
	 *
	 * @return array
	 */
	public function toArray( $notifiable )
	{
		return [
			//
		];
	}
}
