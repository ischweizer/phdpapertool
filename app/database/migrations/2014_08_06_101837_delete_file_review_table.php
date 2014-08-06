<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteFileReviewTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('file_review', function(Blueprint $table)
		{
			$table->dropForeign('file_review_review_id_foreign');
		});
		Schema::table('file_review', function(Blueprint $table)
		{
			$table->dropForeign('file_review_file_id_foreign');
		});
		
		Schema::drop('file_review');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::create('file_review', function($table) 
		{
			$table->engine = 'InnoDB';

			$table->integer('review_id')->unsigned();
			$table->integer('file_id')->unsigned();

			$table->primary(array('review_id', 'file_id'));

			$table->foreign('review_id')->references('id')->on('reviews')->onUpdate('cascade');
			$table->foreign('file_id')->references('id')->on('files')->onUpdate('cascade');
		});
	}

}
