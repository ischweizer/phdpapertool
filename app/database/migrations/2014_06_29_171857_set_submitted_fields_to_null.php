<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetSubmittedFieldsToNull extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::table('submissions')->where('abstract_submitted', '=', '0')->update(array('abstract_submitted' => null));
		DB::table('submissions')->where('paper_submitted', '=', '0')->update(array('paper_submitted' => null));
		DB::table('submissions')->where('notification_result', '=', '0')->update(array('notification_result' => null));
		DB::table('submissions')->where('camera_ready_submitted', '=', '0')->update(array('camera_ready_submitted' => null));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// nothing to undo as previous 0 should actually have been nulls and could not be changed.
	}

}
