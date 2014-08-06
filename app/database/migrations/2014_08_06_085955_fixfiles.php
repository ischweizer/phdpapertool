<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Fixfiles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('files', function(Blueprint $table){
			$table->dropColumn('review_id');
		});

		Schema::table('files', function(Blueprint $table){
			$table->integer('review_id')->unsigned()->nullable()->default(NULL)->after('paper_id');
			$table->foreign('review_id')->references('id')->on('reviews')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('files', function(Blueprint $table){
			$table->dropForeign('files_review_id_foreign');
			$table->dropColumn('review_id');
		});

		Schema::table('files', function(Blueprint $table){
			$table->integer('review_id')->after('paper_id');
		});
	}

}
