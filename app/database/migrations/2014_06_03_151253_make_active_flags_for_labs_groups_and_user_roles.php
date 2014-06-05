<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeActiveFlagsForLabsGroupsAndUserRoles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('labs', function(Blueprint $table) 
		{
			$table->boolean('active')->default(NULL)->after('name');
		});

		Schema::table('groups', function(Blueprint $table) 
		{
			$table->boolean('active')->default(NULL)->after('lab_id');
		});

		Schema::table('user_roles', function(Blueprint $table) 
		{
			$table->boolean('active')->default(NULL)->after('role_type');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('labs', function(Blueprint $table) 
		{
			$table->dropColumn('active');
		});

		Schema::table('groups', function(Blueprint $table) 
		{
			$table->dropColumn('active');
		});

		Schema::table('user_roles', function(Blueprint $table) 
		{
			$table->dropColumn('active');
		});
	}

}
