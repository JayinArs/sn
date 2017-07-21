<?php

namespace App\Notifications;

use App\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;
use NotificationChannels\OneSignal\OneSignalWebButton;

class EventCreated extends Notification
{
	use Queueable;

	/**
	 * Create a new notification instance.
	 *
	 * @param Event $event
	 */
	public function __construct(Event $event)
	{
		//
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

	public function toOneSignal( $notifiable )
	{
		return OneSignalMessage::create()
		                       ->subject( "Your {$notifiable->id} account was approved!" )
		                       ->body( "Click here to see details." )
		                       ->url( 'http://onesignal.com' )
		                       ->webButton(
			                       OneSignalWebButton::create( 'link-1' )
			                                         ->text( 'Click here' )
			                                         ->icon( 'https://upload.wikimedia.org/wikipedia/commons/4/4f/Laravel_logo.png' )
			                                         ->url( 'http://laravel.com' )
		                       );
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
