<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('users', function (Blueprint $table) {
		    $table->increments('id');
		    $table->integer('account_id')->unsigned()->nullable();
		    $table->string('fcm_id')->nullable();
		    $table->string('udid')->nullable();
		    $table->integer('language_id')->unsigned()->nullable();
		    $table->string('status')->default('active');
		    $table->string('api_token')->nullable();
		    $table->string('imei')->nullable();
		    $table->string('timezone')->nullable();
		    $table->dateTime('registration_date')->default(DB::raw('CURRENT_TIMESTAMP'));

		    $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
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
        Schema::dropIfExists('users');
    }
}
