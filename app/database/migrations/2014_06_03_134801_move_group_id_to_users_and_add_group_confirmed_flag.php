<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveGroupIdToUsersAndAddGroupConfirmedFlag extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		Schema::table('users', function(Blueprint $table) 
		{
			$table->integer('group_id')->unsigned()->nullable()->default(NULL)->after('remember_token');
			$table->foreign('group_id')->references('id')->on('groups')->onUpdate('cascade');

			$table->renameColumn('active', 'email_confirmed');

			$table->boolean('group_confirmed')->default(NULL)->after('group_id');
		});
		
		DB::update('UPDATE users, authors SET users.group_id = authors.group_id WHERE users.author_id = authors.id;');
		DB::update('UPDATE authors SET group_id = NULL');

		Schema::table('authors', function(Blueprint $table)
		{
			$table->dropForeign('authors_group_id_foreign');
			$table->dropColumn('group_id');
		});


	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('authors', function(Blueprint $table) 
		{
			$table->integer('group_id')->unsigned()->nullable()->default(NULL);
			$table->foreign('group_id')->references('id')->on('groups')->onUpdate('cascade');
		});

		DB::update('UPDATE users, authors SET authors.group_id = users.group_id WHERE users.author_id = authors.id');
		DB::update('UPDATE users SET group_id = NULL');

		Schema::table('users', function(Blueprint $table) 
		{
			$table->dropForeign('users_group_id_foreign');
			$table->dropColumn('group_id');

			$table->renameColumn('email_confirmed', 'active');

			$table->dropColumn('group_confirmed');
		});
	}

}