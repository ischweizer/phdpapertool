<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaperIdColumnInReviews extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::delete('delete from file_review');
		DB::delete('delete from review_user');
		DB::delete('delete from reviews');

		Schema::table('reviews', function(Blueprint $table)
		{
			$table->integer('paper_id')->unsigned()->after('user_id');

			$table->foreign('paper_id')->references('id')->on('papers')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('reviews', function(Blueprint $table) 
		{
			$table->dropForeign('reviews_paper_id_foreign');
			$table->dropColumn('paper_id');
		});
	}

}
