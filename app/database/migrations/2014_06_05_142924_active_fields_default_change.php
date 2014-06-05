<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ActiveFieldsDefaultChange extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::update('ALTER TABLE labs MODIFY active TINYINT(1) NOT NULL DEFAULT \'0\'');
		DB::update('ALTER TABLE groups MODIFY active TINYINT(1) NOT NULL DEFAULT \'0\'');
		DB::update('ALTER TABLE user_roles MODIFY active TINYINT(1) NOT NULL DEFAULT \'0\'');
		DB::update('ALTER TABLE users MODIFY group_confirmed TINYINT(1) NOT NULL DEFAULT \'0\'');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::update('ALTER TABLE labs MODIFY active TINYINT(1) NOT NULL');
		DB::update('ALTER TABLE groups MODIFY active TINYINT(1) NOT NULL');
		DB::update('ALTER TABLE user_roles MODIFY active TINYINT(1) NOT NULL');
		DB::update('ALTER TABLE users MODIFY group_confirmed TINYINT(1) NOT NULL');
	}

}
