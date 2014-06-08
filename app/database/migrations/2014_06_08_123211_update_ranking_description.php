<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRankingDescription extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::table('rankings')->where('name', '=', 'L')->update(array('description' => 'local conference'));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::table('rankings')->where('name', '=', 'L')->update(array('description' => '?'));
	}

}
