<?php

namespace App\Console\Commands;

use App\Feed;
use App\Notifications\FeedCreated;
use App\OrganizationFollower;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class NotifyAddFeed extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'feed:add {id}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send feed creation notification';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$id        = $this->argument( 'id' );
		$feed      = Feed::find( $id );
		$followers = 0;

		OrganizationFollower::with( [
			                            'account',
			                            'account.users'
		                            ] )->where( 'organization_location_id', $feed->organization_location_id )
		                    ->each( function ( $follower ) use ( &$feed, &$followers ) {
			                    $followers ++;
			                    try {
				                    Notification::send( $follower->account->users, new FeedCreated( $feed ) );
			                    } catch ( ClientException $e ) {
				                    $followers --;
			                    }
		                    } );
		$this->info( 'Feed added at: ' . date( 'Y-m-d h:i:s' ) . ' followed by ' . $followers . ' users' );
	}
}
