<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewRequest extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('review_requests', function($table){
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->integer('paper_id')->unsigned();
			$table->timestamp('deadline');
			$table->text('message')->nullable()->default(NULL);

			$table->timestamps();

			$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
			$table->foreign('paper_id')->references('id')->on('papers')->onUpdate('cascade');
		});

		Schema::create('file_review_request', function($table){
			$table->engine = 'InnoDB';

			$table->integer('review_request_id')->unsigned();
			$table->integer('file_id')->unsigned();

			$table->primary(array('review_request_id', 'file_id'));

			$table->foreign('review_request_id')->references('id')->on('review_requests')->onUpdate('cascade');
			$table->foreign('file_id')->references('id')->on('files')->onUpdate('cascade');
		
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('review_requests', function(Blueprint $table){
			$table->dropForeign('review_requests_paper_id_foreign');
			$table->dropForeign('review_requests_user_id_foreign');
		});
		
		Schema::table('file_review_request', function(Blueprint $table){
			$table->dropForeign('file_review_request_review_request_id_foreign');
			$table->dropForeign('file_review_request_file_id_foreign');
		});

		Schema::drop('review_requests');
		Schema::drop('file_review_request');
	}

}
