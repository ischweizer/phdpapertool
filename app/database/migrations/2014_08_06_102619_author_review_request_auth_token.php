<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AuthorReviewRequestAuthToken extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('author_review_request', function(Blueprint $table)
		{
			$table->char('auth_token', 60)->nullable()->default(NULL)->after('answer');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('author_review_request', function(Blueprint $table)
		{
			$table->dropColumn('auth_token');
		});
	}

}
