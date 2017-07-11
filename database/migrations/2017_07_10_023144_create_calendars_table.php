<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendars', function(Blueprint $table) {
        	$table->increments('id');
        	$table->string('country');
        	$table->string('city');
        	$table->date('current_date')->nullable();
        	$table->string('timezone');
        	$table->time('next_update_time')->nullable();
        	$table->dateTime('last_updated')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::dropIfExists('calendars');
    }
}
