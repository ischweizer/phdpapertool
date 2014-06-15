<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SubmittedFlagsNullable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::update('ALTER TABLE submissions CHANGE abstract_submitted abstract_submitted TINYINT(1) NULL DEFAULT NULL, CHANGE paper_submitted paper_submitted TINYINT(1) NULL DEFAULT NULL, CHANGE notification_result notification_result TINYINT(1) NULL DEFAULT NULL, CHANGE camera_ready_submitted camera_ready_submitted TINYINT(1) NULL DEFAULT NULL');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::update('ALTER TABLE submissions CHANGE abstract_submitted abstract_submitted TINYINT(1) NOT NULL, CHANGE paper_submitted paper_submitted TINYINT(1) NOT NULL, CHANGE notification_result notification_result TINYINT(1) NOT NULL, CHANGE camera_ready_submitted camera_ready_submitted TINYINT(1) NOT NULL');
	}

}
