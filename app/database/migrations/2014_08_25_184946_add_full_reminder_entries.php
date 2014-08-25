<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFullReminderEntries extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	   EmailReminder::truncate(); 
	   DB::insert("INSERT INTO `email_reminders` (`user_id`, `table`, `created_at`, `updated_at`) SELECT
		   `id`, 'events', NOW(), NOW() FROM `users`");
	   DB::insert("INSERT INTO `email_reminders` (`user_id`, `table`, `created_at`, `updated_at`) SELECT
		   `id`, 'review_requests', NOW(), NOW() FROM `users`");
	   //DB::insert("INSERT INTO email_reminders (user_id, table, created_at, updated_at) SELECT id, 'events', NOW(), NOW() FROM users");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
	    EmailReminder::truncate(); 
	}

}
