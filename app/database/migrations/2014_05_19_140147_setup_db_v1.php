<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetupDbV1 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		Schema::create('universities', function($table) 
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 255);

			$table->timestamps();
			//$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			//$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
			
		});

		DB::insert('insert into universities (name, created_at, updated_at) values (?, ?, ?)', array('Administration', '2014-05-15 15:33:21', '2014-05-15 15:33:21'));

		Schema::create('departments', function($table) 
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 255);
			$table->integer('university_id')->unsigned();

			$table->timestamps();
			//$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			//$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
			
			$table->foreign('university_id')->references('id')->on('universities')->onUpdate('cascade');
		});

		DB::insert('insert into departments (university_id, name, created_at, updated_at) values (?, ?, ?, ?)', array(1, 'Administration', '2014-05-15 15:33:41', '2014-05-15 15:33:41'));

		Schema::create('labs', function($table) 
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 255);
			$table->integer('department_id')->unsigned();

			$table->timestamps();
			//$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			//$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
			
			$table->foreign('department_id')->references('id')->on('departments')->onUpdate('cascade');
		});

		DB::insert('insert into labs (department_id, name, created_at, updated_at) values (?, ?, ?, ?)', array(1, 'Administration', '2014-05-15 15:33:56', '2014-05-15 15:33:56'));

		Schema::create('groups', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 255);
			$table->integer('lab_id')->unsigned();
			
			$table->timestamps();
			//$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			//$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
			
			$table->foreign('lab_id')->references('id')->on('labs')->onUpdate('cascade');
		});

		DB::insert('insert into groups (lab_id, name, created_at, updated_at) values (?, ?, ?, ?)', array(1, 'Administration', '2014-05-15 15:34:08', '2014-05-15 15:34:08'));

		Schema::create('authors',function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('last_name',50);
			$table->string('first_name',50);
			$table->string('email',255);
			$table->integer('group_id')->unsigned()->nullable()->default(NULL);

			$table->timestamps();
			//$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			//$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
			
			$table->foreign('group_id')->references('id')->on('groups')->onUpdate('cascade');
		
		});

		DB::insert('insert into authors (last_name, first_name, email, group_id, created_at, updated_at) values (?, ?, ?, ?, ? ,?)', array('Admin', 'Admin', 'admin@example.org', 1, '2014-05-15 15:34:59', '2014-05-15 15:34:59'));

		Schema::create('papers', function($table) 
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('title', 255);
			$table->text('abstract');

			$table->timestamps();
			//$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			//$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));

		});

		Schema::create('author_paper', function($table) 
		{
			$table->engine = 'InnoDB';

			$table->integer('paper_id')->unsigned();
			$table->integer('author_id')->unsigned();

			$table->primary(array('paper_id', 'author_id'));

			$table->foreign('paper_id')->references('id')->on('papers')->onUpdate('cascade');
			$table->foreign('author_id')->references('id')->on('authors')->onUpdate('cascade');
		});

		Schema::create('rankings', function($table) 
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 5);
			$table->string('description', 60);

		});

		DB::insert('insert into rankings (name, description) values (?, ?)', array('A*', 'flagship conference'));
		DB::insert('insert into rankings (name, description) values (?, ?)', array('A', 'excellent conference, determined by a mix of indicators'));
		DB::insert('insert into rankings (name, description) values (?, ?)', array('B', 'good conference, determined by a mix of indicators'));
		DB::insert('insert into rankings (name, description) values (?, ?)', array('C', 'other ranked conference venues'));
		DB::insert('insert into rankings (name, description) values (?, ?)', array('none', 'not ranked conference venues'));

		Schema::create('conferences', function($table) 
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 255);
			$table->integer('ranking_id')->unsigned();

			$table->timestamps();

			$table->foreign('ranking_id')->references('id')->on('rankings')->onUpdate('cascade');
		});

		Schema::create('conference_editions', function($table) 
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('conference_id')->unsigned();
			$table->string('location', 50);
			$table->string('edition', 20);
			$table->timestamp('start');
			$table->timestamp('end');

			$table->timestamps();

			$table->foreign('conference_id')->references('id')->on('conferences')->onUpdate('cascade');

		});

		Schema::create('users', function($table) 
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			// CHAR(60) could be ci, but laravel does the password check in php either way
			$table->char('password', 60);
			$table->string('email', 255);
			$table->integer('author_id')->unsigned();
			$table->boolean('active')->default(0);
			$table->char('remember_token',100)->nullable()->default(NULL);

			$table->timestamps();

			$table->foreign('author_id')->references('id')->on('authors')->onUpdate('cascade');
		});

		DB::insert('insert into users (password, email, author_id, active, created_at, updated_at) values (?, ?, ?, ?, ?, ?)', array(Hash::make('1234'), 'admin@example.org', 1, 1, '2014-05-15 15:35:16', '2014-05-15 15:35:16'));

		Schema::create('files', function($table) 
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->integer('paper_id')->unsigned();
			$table->string('name', 255);
			$table->string('comment', 255);

			$table->timestamps();

			$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
			$table->foreign('paper_id')->references('id')->on('papers')->onUpdate('cascade');
		});

		Schema::create('reviews', function($table) 
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->timestamp('deadline');

			$table->timestamps();

			$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
		});

		Schema::create('file_review', function($table) 
		{
			$table->engine = 'InnoDB';

			$table->integer('review_id')->unsigned();
			$table->integer('file_id')->unsigned();

			$table->primary(array('review_id', 'file_id'));

			$table->foreign('review_id')->references('id')->on('reviews')->onUpdate('cascade');
			$table->foreign('file_id')->references('id')->on('files')->onUpdate('cascade');
		});

		Schema::create('review_user', function($table) 
		{
			$table->engine = 'InnoDB';

			$table->integer('review_id')->unsigned();
			$table->integer('user_id')->unsigned();

			$table->primary(array('review_id', 'user_id'));

			$table->foreign('review_id')->references('id')->on('reviews')->onUpdate('cascade');
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
		});

		Schema::create('submissions', function($table) 
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('paper_id')->unsigned();
			$table->timestamp('abstract_due');
			$table->timestamp('paper_due');
			$table->timestamp('notification_date');
			$table->tinyInteger('notification_result')->nullable()->default(NULL);
			$table->timestamp('camera_ready_due');
			$table->morphs('event');

			$table->timestamps();

			$table->foreign('paper_id')->references('id')->on('papers')->onUpdate('cascade');

		});

		Schema::create('workshops', function($table) 
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 100);
			$table->integer('conference_editions_id')->unsigned();
			$table->timestamp('start');
			$table->timestamp('end');

			$table->timestamps();

			$table->foreign('conference_editions_id')->references('id')->on('conference_editions')->onUpdate('cascade');
		});


	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('departments', function(Blueprint $table)
		{
			$table->dropForeign('departments_university_id_foreign');
		});
		Schema::table('labs', function(Blueprint $table)
		{
			$table->dropForeign('labs_department_id_foreign');
		});
		Schema::table('groups', function(Blueprint $table)
		{
			$table->dropForeign('groups_lab_id_foreign');
		});
		Schema::table('authors', function(Blueprint $table)
		{
			$table->dropForeign('authors_group_id_foreign');
		});
		Schema::table('author_paper', function(Blueprint $table)
		{
			$table->dropForeign('author_paper_paper_id_foreign');
		});
		Schema::table('author_paper', function(Blueprint $table)
		{
			$table->dropForeign('author_paper_author_id_foreign');
		});
		Schema::table('conferences', function(Blueprint $table)
		{
			$table->dropForeign('conferences_ranking_id_foreign');
		});
		Schema::table('conference_editions', function(Blueprint $table)
		{
			$table->dropForeign('conference_editions_conference_id_foreign');
		});
		Schema::table('users', function(Blueprint $table)
		{
			$table->dropForeign('users_author_id_foreign');
		});
		Schema::table('files', function(Blueprint $table)
		{
			$table->dropForeign('files_user_id_foreign');
		});
		Schema::table('files', function(Blueprint $table)
		{
			$table->dropForeign('files_paper_id_foreign');
		});
		Schema::table('reviews', function(Blueprint $table)
		{
			$table->dropForeign('reviews_user_id_foreign');
		});
		Schema::table('file_review', function(Blueprint $table)
		{
			$table->dropForeign('file_review_review_id_foreign');
		});
		Schema::table('file_review', function(Blueprint $table)
		{
			$table->dropForeign('file_review_file_id_foreign');
		});
		Schema::table('review_user', function(Blueprint $table)
		{
			$table->dropForeign('review_user_review_id_foreign');
		});
		Schema::table('review_user', function(Blueprint $table)
		{
			$table->dropForeign('review_user_user_id_foreign');
		});
		Schema::table('submissions', function(Blueprint $table)
		{
			$table->dropForeign('submissions_paper_id_foreign');
		});
		Schema::table('workshops', function(Blueprint $table)
		{
			$table->dropForeign('workshops_conference_editions_id_foreign');
		});
		
		Schema::drop('universities');
		Schema::drop('departments');
		Schema::drop('labs');
		Schema::drop('groups');
		Schema::drop('authors');
		Schema::drop('papers');
		Schema::drop('author_paper');
		Schema::drop('rankings');
		Schema::drop('conferences');
		Schema::drop('conference_editions');
		Schema::drop('users');
		Schema::drop('files');
		Schema::drop('reviews');
		Schema::drop('file_review');
		Schema::drop('review_user');
		Schema::drop('submissions');
		Schema::drop('workshops');
	}

}
