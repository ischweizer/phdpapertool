<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnswerColumnInReviewUser extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('review_user', function(Blueprint $table)
		{
			$table->boolean('answer')->nullable()->default(NULL)->after('user_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('review_user', function(Blueprint $table) 
		{
			$table->dropColumn('answer');
		});
	}

}
