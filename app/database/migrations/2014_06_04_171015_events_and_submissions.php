<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EventsAndSubmissions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('events', function($table) 
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->timestamp('start');
			$table->timestamp('end');
			$table->timestamp('abstract_due');
			$table->timestamp('paper_due');
			$table->timestamp('notification_date');
			$table->timestamp('camera_ready_due');
			$table->morphs('detail');

			$table->timestamps();
		});
		Schema::table('submissions', function(Blueprint $table)
		{
			$table->dropColumn('event_type');
			
			$table->foreign('event_id')->references('id')->on('events')->onUpdate('cascade');
		});
		Schema::table('conference_editions', function(Blueprint $table)
		{
			$table->dropColumn('start', 'end', 'abstract_due','paper_due','notification_date','camera_ready_due');
		});
		Schema::table('workshops', function(Blueprint $table)
		{
			$table->dropForeign('workshops_conference_editions_id_foreign');
			$table->renameColumn('conference_editions_id', 'conference_edition_id');
			$table->dropColumn('start', 'end');

			$table->foreign('conference_edition_id')->references('id')->on('conference_editions')->onUpdate('cascade');
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
			$table->string('event_type')->after('event_id');
			
			$table->dropForeign('submissions_event_id_foreign');
		});
		Schema::drop('events');
		// something goes wrong here if rename is not explicitly done before the rest
		Schema::table('workshops', function(Blueprint $table)
		{
			$table->dropForeign('workshops_conference_edition_id_foreign');
			$table->renameColumn('conference_edition_id', 'conference_editions_id');
		});
		Schema::table('workshops', function(Blueprint $table)
		{
			$table->timestamp('start')->after('conference_editions_id');
			$table->timestamp('end')->after('start');

			$table->foreign('conference_editions_id')->references('id')->on('conference_editions')->onUpdate('cascade');
		});
		Schema::table('conference_editions', function(Blueprint $table)
		{
			$table->timestamp('start')->after('edition');
			$table->timestamp('end')->after('start');
			$table->timestamp('abstract_due')->after('end');
			$table->timestamp('paper_due')->after('abstract_due');
			$table->timestamp('notification_date')->after('paper_due');
			$table->timestamp('camera_ready_due')->after('notification_date');
		});
	}

}
