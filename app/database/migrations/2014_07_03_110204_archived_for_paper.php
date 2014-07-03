<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ArchivedForPaper extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('papers', function(Blueprint $table)
		{
			$table->boolean('archived')->after('repository_url');
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
			$table->dropColumn('archived');
		});
	}

}
