<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthorReviewRequest extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('author_review_request', function($table) {
			$table->engine = 'InnoDB';

			$table->integer('author_id')->unsigned();
			$table->integer('review_request_id')->unsigned();
			$table->boolean('answer')->nullable()->default(NULL);

			$table->primary(array('author_id', 'review_request_id'));

			$table->foreign('review_request_id')->references('id')->on('review_requests')->onUpdate('cascade');
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
		Schema::table('author_review_request', function(Blueprint $table){
			$table->dropForeign('author_review_request_author_id_foreign');
			$table->dropForeign('author_review_request_review_request_id_foreign');
		});

		Schema::drop('author_review_request');
	}

}
