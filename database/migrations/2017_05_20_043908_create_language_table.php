<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLanguageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('languages', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('code');
        });

	    DB::table('languages')->insert(
		    array(
		    	[
		    		'id'    => 1,
				    'name'  => 'English',
				    'code'  => 'en-US'
			    ],
			    [
				    'id'    => 2,
				    'name'  => 'Urdu',
				    'code'  => 'ur'
			    ],
			    [
				    'id'    => 3,
				    'name'  => 'Arabic',
				    'code'  => 'ar-IQ'
			    ],
			    [
				    'id'    => 4,
				    'name'  => 'Chinese',
				    'code'  => 'zh-Hans'
			    ],
			    [
				    'id'    => 5,
				    'name'  => 'Japanese',
				    'code'  => 'ja'
			    ],
			    [
				    'id'    => 6,
				    'name'  => 'French',
				    'code'  => 'fr-FR'
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
        Schema::dropIfExists('languages');
    }
}
