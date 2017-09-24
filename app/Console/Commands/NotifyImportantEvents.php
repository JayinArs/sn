<?php

namespace App\Console\Commands;

use App\Calendar;
use App\Event;
use App\Notifications\ImportantDate;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

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

		$date = Carbon::parse( $date );

		Event::with( [ 'organization_location.organization', 'category' ] )
		     ->where( 'is_system_event', 1 )
		     ->whereDay( 'hijri_date', $date->day )
		     ->whereMonth( 'hijri_date', $date->month )
		     ->each( function ( $event ) use ( &$timezone ) {

			     User::where( 'timezone', $timezone )
			         ->each( function ( $user ) use ( &$event ) {

				         $user->notify( new ImportantDate( $event ) );

			         } );
			     $this->info( "Notified: {$event->title}" );
		     } );
	}
}
