<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('organizations', function(Blueprint $table) {
		    $table->increments( 'id' );
		    $table->string('name');
		    $table->boolean('is_official')->default(false);
		    $table->integer('account_id')->unsigned();
		    $table->string('status')->default('active');

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
        Schema::dropIfExists('organizations');
    }
}
