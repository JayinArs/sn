<?php

namespace App\Console\Commands;

use App\Calendar;
use App\Event;
use App\Notifications\EventCreated;
use App\Notifications\EventDate;
use App\Notifications\ImportantDate;
use App\OrganizationFollower;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Notification;

class NotifyImportantEvents extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'event:notify {--s|system} {--t|timezone=} {date}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Notify system events by timezone';

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
		$timezone = $this->option( 'timezone' );
		$date     = $this->argument( 'date' );
		$system   = $this->option( 'system' );

		$date = Carbon::parse( $date );

		if ( $system ) {
			Event::with( [ 'organization_location.organization', 'category' ] )
			     ->where( 'is_system_event', 1 )
			     ->whereDay( 'hijri_date', $date->day )
			     ->whereMonth( 'hijri_date', $date->month )
			     ->each( function ( $event ) use ( &$timezone ) {

				     $users = User::where( 'timezone', $timezone )->get();
				     Notification::send( $users, new ImportantDate( $event ) );

				     $this->info( "Notified: {$event->title} to " . count($users) . " users." );
			     } );
		} else {

			$calendar = Calendar::where( 'timezone', $timezone )->first();

			if ( $calendar ) {
				Event::with( [
					             'organization_location' => function ( $query ) use ( &$calendar ) {
						             $query->where( 'country', $calendar->country )
						                   ->where( [
							                            'city'  => $calendar->city,
							                            'state' => $calendar->state
						                            ] );
					             }
				             ] )
				     ->where( 'is_system_event', 0 )
				     ->whereDay( 'hijri_date', $date->day )
				     ->whereMonth( 'hijri_date', $date->month )
				     ->each( function ( $event ) {

					     $followers = 0;
					     OrganizationFollower::with( [
						                                 'account',
						                                 'account.users'
					                                 ] )
					                         ->where( 'organization_location_id', $event->organization_location_id )
					                         ->each( function ( $follower ) use ( &$event, &$followers ) {
						                         $followers ++;
						                         try {
							                         Notification::send( $follower->account->users, new EventDate( $event ) );
						                         } catch ( ClientException $e ) {
							                         $followers --;
						                         }
					                         } );
					     $this->info( 'Event notified at: ' . date( 'Y-m-d h:i:s' ) . ' followed by ' . $followers . ' users' );
				     } );
			}
		}
	}
}
