<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create( 'accounts', function ( Blueprint $table ) {
			$table->increments( 'id' );
			$table->string( 'username' )->nullable();
			$table->string( 'email' )->nullable();
			$table->string( 'password' )->nullable();
			$table->string( 'status' )->default( 'active' );
			$table->integer( 'role_id' )->unsigned();
			$table->dateTime( 'registration_date' )->default( DB::raw( 'CURRENT_TIMESTAMP' ) );
			$table->string( 'remember_token' )->nullable();

			$table->softDeletes();
			$table->foreign( 'role_id' )->references( 'id' )->on( 'user_roles' )->onDelete( 'cascade' );
		} );

		DB::table( 'accounts' )->insert(
			array(
				[
					'id'       => 1,
					'username' => 'admin',
					'email'    => 'admin@14pearls.com',
					'password' => bcrypt( 'admin999!' ),
					'role_id'  => \App\UserRole::getAdminRole()->id
				],
				[
					'id'       => 1,
					'username' => 'contributor',
					'email'    => 'contributor@14pearls.com',
					'password' => bcrypt( 'unite999!' ),
					'role_id'  => \App\UserRole::getContributorRole()->id
				]
			)
		);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::disableForeignKeyConstraints();
		Schema::dropIfExists( 'accounts' );
	}
}
