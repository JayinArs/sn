<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganizationFollowerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('organization_followers', function (Blueprint $table) {
		    $table->increments( 'id' );
		    $table->integer('organization_location_id')->unsigned();
		    $table->integer('user_id')->unsigned();

		    $table->foreign('organization_location_id')->references('id')->on('organization_locations')->onDelete('cascade');
		    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('organization_followers');
    }
}
