<?php

namespace App\Console\Commands;

use App\Calendar;
use App\Timezone;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Hijri;

class UpdateCalendars extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'calendar:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update calendar dates';

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
		$last_updated_yesterday = Carbon::now( 'UTC' )->subDay( 1 )->toDateString();

		Calendar::whereDate( 'last_updated', '=', $last_updated_yesterday )
		        ->each( function ( $calendar ) {
			        $next_time = Carbon::parse( $calendar->next_update_time, $calendar->timezone );
			        //$next_time    = Carbon::parse( '10:25', $calendar->timezone );
			        $current_time = Carbon::now( $calendar->timezone );

			        if ( $current_time->diffInMinutes( $next_time, false ) <= 0 ) {
				        $current_time->addDay();

				        $current_date = Hijri::getHijriDateByGeorgian( $calendar->timezone, $calendar->city, $calendar->country );
				        $prayer       = Hijri::getPrayerTime( $current_time, $calendar->city, $calendar->country );

				        if ( $prayer ) {
					        $calendar->current_date     = $current_date['date'];
					        $calendar->next_update_time = $prayer;
					        $calendar->last_updated     = Carbon::now( 'UTC' )->toDateTimeString();
					        $calendar->save();

					        //echo 'updated: ' . $current_date['date'] . '\n';
				        }
			        }
		        } );
	}
}
