<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocalizationTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create( 'localization', function ( Blueprint $table ) {
			$table->increments( 'id' );
			$table->string( 'item_type' );
			$table->longText( 'item_value' )->nullable();
			$table->integer( 'item_id' );
			$table->integer( 'language_id' )->unsigned();

			$table->foreign( 'language_id' )->references( 'id' )->on( 'languages' )->onDelete( 'cascade' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists( 'localization' );
	}
}
