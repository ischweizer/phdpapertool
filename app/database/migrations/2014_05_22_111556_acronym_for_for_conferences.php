<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AcronymForForConferences extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('conferences', function(Blueprint $table)
		{
			$table->string('acronym', 40)->after('name');
			$table->string('field_of_research', 10)->after('ranking_id');
		});

		DB::update('ALTER TABLE rankings MODIFY name VARCHAR(20)');
		DB::insert('insert into rankings (name, description) values (?, ?)', array('Australasian', 'a predominantly Australasian venue'));
		DB::insert('insert into rankings (name, description) values (?, ?)', array('L', '?'));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('conferences', function(Blueprint $table)
		{
			$table->dropColumn('acronym', 'field_of_research');
		});

		// beware: this will not go through if conferences contain references to these rankings (-> foreign key), but that is okay!
		DB::delete('delete from rankings WHERE name IN (?, ?)', array('Australasian', 'L'));
		DB::update('ALTER TABLE rankings MODIFY name VARCHAR(5)');
	}

}
