<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveAttributeColInEmailReminders extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	    Schema::table('email_reminders', function(Blueprint $table) 
	    {
		    $table->dropColumn('attribute');
	    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
	    Schema::table('email_reminders', function(Blueprint $table) 
	    {
		    $table->boolean('active')->default(NULL)->after('lab_id');
		    $table->string('attribute', 255)->default(NULL)->after('table');
	    });
	}

}
