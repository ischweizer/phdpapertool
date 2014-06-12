<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrderPositionForAuthorPaper extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('author_paper', function(Blueprint $table)
		{
			$table->integer('order_position')->unsigned()->default(1)->after('author_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('author_paper', function(Blueprint $table)
		{
			$table->dropColumn('order_position');
		});
	}

}
