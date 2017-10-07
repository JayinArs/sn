<?php

namespace App\Notifications;

use App\Feed;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class FeedCreated extends Notification
{
	use Queueable;

	private $feed;

	/**
	 * FeedCreated constructor.
	 *
	 * @param Feed $feed
	 */
	public function __construct( Feed $feed )
	{
		$this->feed = $feed;
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
	 * @return $this
	 */
	public function toOneSignal( $notifiable )
	{
		$feeds = $this->feed->organization_feeds;

		if ( ! empty( $feeds ) ) {
			return OneSignalMessage::create()
			                       ->subject( "{$feeds[0]->organization_location->organization->name}:" )
			                       ->body( "{$this->feed->content}" );
		}
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
