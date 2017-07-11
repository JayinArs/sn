<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('event_meta', function (Blueprint $table) {
		    $table->increments( 'id' );
		    $table->string('key');
		    $table->string('value')->nullable();
		    $table->integer('event_id')->unsigned();

		    $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
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
        Schema::dropIfExists('event_meta');
    }
}
