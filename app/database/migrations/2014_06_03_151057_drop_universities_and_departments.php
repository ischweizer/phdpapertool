<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropUniversitiesAndDepartments extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::update('alter table labs modify department_id INT UNSIGNED NULL');
		DB::update('update labs set department_id = NULL');

		Schema::table('labs', function(Blueprint $table) 
		{
			$table->dropForeign('labs_department_id_foreign');
			$table->dropColumn('department_id');
		});

		DB::delete('delete from departments');
		Schema::drop('departments');

		DB::delete('delete from universities');
		Schema::drop('universities');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::create('universities', function($table) 
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 255);

			$table->timestamps();
		});

		DB::insert('insert into universities (name, created_at, updated_at) values (?, ?, ?)', array('Administration', '2014-05-15 15:33:21', '2014-05-15 15:33:21'));

		Schema::create('departments', function($table) 
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 255);
			$table->integer('university_id')->unsigned();

			$table->timestamps();
			$table->foreign('university_id')->references('id')->on('universities')->onUpdate('cascade');
		});

		DB::insert('insert into departments (university_id, name, created_at, updated_at) values (?, ?, ?, ?)', array(1, 'Administration', '2014-05-15 15:33:41', '2014-05-15 15:33:41'));

		Schema::table('labs', function(Blueprint $table) 
		{
			$table->integer('department_id')->unsigned()->nullable()->after('name');
			$table->foreign('department_id')->references('id')->on('departments')->onUpdate('cascade');
		});
	}
}