<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('events', function(Blueprint $table) {
		    $table->increments('id');
		    $table->string('title');
		    $table->date('english_date')->nullable();
		    $table->date('hijri_date')->nullable();
		    $table->time('start_time')->nullable();
		    $table->time('end_time')->nullable();
		    $table->boolean('is_system_event')->default(false);
		    $table->integer('organization_location_id')->unsigned()->nullable();
		    $table->integer('user_id')->unsigned()->nullable();
		    $table->integer('account_id')->unsigned();
		    $table->integer('category_id')->unsigned()->nullable();
		    $table->string('venue')->nullable();

		    $table->foreign('organization_location_id')->references('id')->on('organization_locations')->onDelete('cascade');
		    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		    $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
		    $table->foreign('category_id')->references('id')->on('event_categories')->onDelete('cascade');
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('events');
    }
}
