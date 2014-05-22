<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RepositoryUrlForPaper extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('papers', function(Blueprint $table)
		{
			$table->string('repository_url', 255)->after('abstract');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('papers', function(Blueprint $table)
		{
			$table->dropColumn('repository_url');
		});
	}

}
