<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Command';

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
    	if(Storage::disk('local')->exists('testcommand.txt'))
    		Storage::disk('local')->append('testcommand.txt', 'Last updated: ' . date('Y-m-d h:i:s'));
    	else
	        Storage::disk('local')->put('testcommand.txt', 'Last updated: ' . date('Y-m-d h:i:s'));

        echo 'Updated';
    }
}
