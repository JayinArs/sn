<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganizationMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('organization_meta', function (Blueprint $table) {
		    $table->increments( 'id' );
		    $table->string('key');
		    $table->string('value')->nullable();
		    $table->integer('organization_id')->unsigned();

		    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
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
        Schema::dropIfExists('organization_meta');
    }
}
