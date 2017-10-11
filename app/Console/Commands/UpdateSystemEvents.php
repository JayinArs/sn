<?php

namespace App\Console\Commands;

use App\Event;
use App\EventMeta;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Hijri;

class UpdateSystemEvents extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'event:update {--s|system}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Temporary command to update system events years';

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
		$system = $this->option( 'system' );

		if ( $system ) {
			Event::where( 'is_system_event', 1 )->each( function ( $event ) {
				$event->is_recurring = 1;
				EventMeta::updateOrCreate( [
					                           'event_id' => $event->id,
					                           'key'      => 'recurring_type',
				                           ], [
					                           'value' => 'yearly'
				                           ] );
				$event->save();
				$this->info( $event->id . ", " );
			} );
		}
	}
}
