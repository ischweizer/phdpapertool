<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameUserForFiles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('files', function(Blueprint $table)
		{
			$table->dropForeign('files_user_id_foreign');
			$table->renameColumn('user_id', 'author_id');

			$table->foreign('author_id')->references('id')->on('authors')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// something goes wrong here if rename is not explicitly done before the rest
		Schema::table('files', function(Blueprint $table)
		{
			$table->dropForeign('files_author_id_foreign');
			$table->renameColumn('author_id', 'user_id');
		});
		Schema::table('files', function(Blueprint $table)
		{
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
		});
	}

}
