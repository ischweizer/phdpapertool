<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveAdministrationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::drop('administrations');

		Schema::table('user_roles', function(Blueprint $table)
		{
			$table->dropColumn('role_type');
		});

		DB::table('user_roles')->where('id', '=', 1)->update(array('active' => 1));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::create('administrations', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->timestamps();
		});
		DB::insert('insert into administrations () values ()', array());

		Schema::table('user_roles', function(Blueprint $table)
		{
			$table->string('role_type')->after('role_id');
		});

		DB::table('user_roles')->where('id', '=', 1)->update(array('active' => 0, 'role_type' => 'Administration'));
	}

}
