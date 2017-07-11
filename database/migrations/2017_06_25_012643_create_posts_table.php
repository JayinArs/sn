<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('posts', function (Blueprint $table) {
		    $table->increments( 'id' );
		    $table->string('cite')->nullable();
		    $table->string('content')->nullable();
		    $table->integer('account_id')->unsigned()->nullable();

		    $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
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
        Schema::dropIfExists('posts');
    }
}
