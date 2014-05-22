<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConferenceData extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$coreCSV = app_path() . '/database/migrations/2014_05_22_114450_core.csv';
		DB::connection()->getpdo()->exec(sprintf("LOAD DATA LOCAL INFILE '%s' INTO TABLE conferences FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' ESCAPED BY '\"' LINES TERMINATED BY '\\n' IGNORE 0 LINES 
			(@name, @acronym, @source, @rank, @changed, @for)
			SET name=TRIM(@name), acronym=TRIM(@acronym), ranking_id=(SELECT id FROM rankings WHERE name=IF(@rank='','none',TRIM(@rank))), field_of_research=TRIM(TRIM(TRAILING '\r' FROM @for)), created_at=CURRENT_TIMESTAMP, updated_at=CURRENT_TIMESTAMP", addslashes($coreCSV)));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::delete('DELETE FROM conferences');
	}

}
