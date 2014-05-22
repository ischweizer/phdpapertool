<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveSubmissionDates extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('submissions', function(Blueprint $table)
		{
			$table->dropColumn('abstract_due','paper_due','notification_date','notification_result','camera_ready_due');
		});
		Schema::table('submissions', function(Blueprint $table)
		{
			$table->boolean('abstract_submitted')->default(NULL)->after('paper_id');
			$table->boolean('paper_submitted')->default(NULL)->after('abstract_submitted');
			$table->boolean('notification_result')->default(NULL)->after('paper_submitted');
			$table->boolean('camera_ready_submitted')->default(NULL)->after('notification_result');
		});
		Schema::table('conference_editions', function(Blueprint $table)
		{
			$table->timestamp('abstract_due')->after('end');
			$table->timestamp('paper_due')->after('abstract_due');
			$table->timestamp('notification_date')->after('paper_due');
			$table->timestamp('camera_ready_due')->after('notification_date');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('submissions', function(Blueprint $table)
		{
			$table->dropColumn('abstract_submitted','paper_submitted','notification_result','camera_ready_submitted');
		});
		Schema::table('submissions', function(Blueprint $table)
		{
			$table->timestamp('abstract_due')->after('paper_id');
			$table->timestamp('paper_due')->after('abstract_due');
			$table->timestamp('notification_date')->after('paper_due');
			$table->tinyInteger('notification_result')->nullable()->default(NULL)->after('notification_date');
			$table->timestamp('camera_ready_due')->after('notification_result');
		});
		Schema::table('conference_editions', function(Blueprint $table)
		{
			$table->dropColumn('abstract_due','paper_due','notification_date','camera_ready_due');
		});
	}

}
