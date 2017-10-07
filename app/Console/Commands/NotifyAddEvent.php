<?php

namespace App\Console\Commands;

use App\Event;
use App\Notifications\EventCreated;
use App\OrganizationFollower;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Mockery\Exception;

class NotifyAddEvent extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'event:add {id}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send event creation notification';

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
		$event     = Event::find( $id );
		$followers = 0;

		OrganizationFollower::with( [
			                            'account',
			                            'account.meta_data'
		                            ] )->where( 'organization_location_id', $event->organization_location_id )
		                    ->each( function ( $follower ) use ( &$event, &$followers ) {
			                    $followers ++;
			                    try {
				                    $follower->user->notify( new EventCreated( $event ) );
			                    } catch ( ClientException $e ) {
				                    $followers --;
			                    }
		                    } );
		$this->info( 'Event added at: ' . date( 'Y-m-d h:i:s' ) . ' followed by ' . $followers . ' users' );
	}
}
