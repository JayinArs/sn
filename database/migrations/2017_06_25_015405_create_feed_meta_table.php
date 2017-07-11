<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('feed_meta', function (Blueprint $table) {
		    $table->increments( 'id' );
		    $table->string('key');
		    $table->string('value')->nullable();
		    $table->integer('feed_id')->unsigned();

		    $table->foreign('feed_id')->references('id')->on('feeds')->onDelete('cascade');
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
	    Schema::dropIfExists('feed_meta');
    }
}
