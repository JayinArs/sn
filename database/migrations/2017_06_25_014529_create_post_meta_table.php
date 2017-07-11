<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('post_meta', function (Blueprint $table) {
		    $table->increments( 'id' );
		    $table->string('key');
		    $table->string('value')->nullable();
		    $table->integer('post_id')->unsigned();

		    $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
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
        Schema::dropIfExists('post_meta');
    }
}
