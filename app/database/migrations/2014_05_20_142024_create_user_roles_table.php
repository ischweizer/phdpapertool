<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_roles', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->morphs('role');
			$table->timestamps();

			$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
		});

		// create dummy table for morphTo relationship and unified models for roles
		Schema::create('administrations', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->timestamps();
		});
		DB::insert('insert into administrations () values ()', array());

		Schema::table('universities', function(Blueprint $table)
		{
			$table->integer('administration_id')->unsigned()->default(1)->after('name');
			$table->foreign('administration_id')->references('id')->on('administrations')->onUpdate('cascade');
		});

		DB::insert('insert into user_roles (user_id, role_id, role_type) values (1, 1, "Administration")', array());
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{

		Schema::table('universities', function(Blueprint $table)
		{
			$table->dropForeign('universities_administration_id_foreign');
			$table->dropColumn('administration_id');
		});
		Schema::drop('administrations');
		Schema::drop('user_roles');
	}

}
