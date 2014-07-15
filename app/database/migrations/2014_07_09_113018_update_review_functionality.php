<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateReviewFunctionality extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::delete('delete from review_user');
		DB::delete('delete from file_review');
		DB::delete('delete from reviews');


		Schema::table('review_user', function(Blueprint $table) {
			$table->dropForeign('review_user_review_id_foreign');
			$table->dropForeign('review_user_user_id_foreign');
		});

		Schema::drop('review_user');

		Schema::table('reviews', function(Blueprint $table) {
			$table->dropForeign('reviews_user_id_foreign');
			$table->dropForeign('reviews_paper_id_foreign');
			$table->dropColumn('user_id','paper_id', 'deadline');

			$table->integer('author_id')->unsigned()->after('id');
			$table->foreign('author_id')->references('id')->on('authors')->onUpdate('cascade');

			$table->integer('review_request_id')->unsigned()->after('author_id');
			$table->foreign('review_request_id')->references('id')->on('review_requests')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::create('review_user', function($table) 
		{
			$table->engine = 'InnoDB';

			$table->integer('review_id')->unsigned();
			$table->integer('user_id')->unsigned();

			$table->primary(array('review_id', 'user_id'));

			$table->foreign('review_id')->references('id')->on('reviews')->onUpdate('cascade');
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
		});

		Schema::table('reviews', function(Blueprint $table){
			$table->integer('user_id')->unsigned()->after('id');
			$table->integer('paper_id')->unsigned()->after('user_id');
			$table->timestamp('deadline')->after('paper_id');

			$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
			$table->foreign('paper_id')->references('id')->on('papers')->onUpdate('cascade');

			$table->dropForeign('reviews_author_id_foreign');
			$table->dropForeign('reviews_review_request_id_foreign');
			$table->dropColumn('author_id','review_request_id');
		});
	}

}
