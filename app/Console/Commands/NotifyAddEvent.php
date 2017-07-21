<?php

namespace App\Console\Commands;

use App\Event;
use App\Notifications\EventCreated;
use App\OrganizationFollower;
use Illuminate\Console\Command;

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
		$id    = $this->argument( 'id' );
		$event = Event::find( $id );

		OrganizationFollower::with( [
			                            'user',
			                            'user.meta_data'
		                            ] )->where( 'organization_location_id', $event->organization_location_id )
		                    ->each( function ( $follower ) use ( &$event ) {
			                    $follower->user->notify( new EventCreated( $event ) );
		                    } );
	}
}
