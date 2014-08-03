<?php

return array(
	// for more configuration see app/config files (/laravel documentation)

	/*
	|--------------------------------------------------------------------------
	| Application Debug Mode
	|--------------------------------------------------------------------------
	|
	| When your application is in debug mode, detailed error messages with
	| stack traces will be shown on every error that occurs within your
	| application. If disabled, a simple generic error page is shown.
	|
	*/
    'PHD_DEBUG' => false,
	
	/*
	|--------------------------------------------------------------------------
	| Application URL
	|--------------------------------------------------------------------------
	|
	| This URL is used by the console to properly generate URLs when using
	| the Artisan command line tool. You should set this to the root of
	| your application so that it is used when running Artisan tasks.
	|
	*/
    'PHD_URL' => 'http://localhost/',
	
	/*
	|--------------------------------------------------------------------------
	| Application Timezone
	|--------------------------------------------------------------------------
	|
	| Here you may specify the default timezone for your application, which
	| will be used by the PHP date and date-time functions. We have gone
	| ahead and set this to a sensible default for you out of the box.
	|
	*/
	'PHD_TIMEZONE' => 'UTC',
	
	/*
	|--------------------------------------------------------------------------
	| Encryption Key
	|--------------------------------------------------------------------------
	|
	| This key is used by the Illuminate encrypter service and should be set
	| to a random, 32 character string, otherwise these encrypted strings
	| will not be safe. Please do this before deploying an application!
	|
	*/
    'PHD_KEY' => 'YourSecretKey!!!',
	
	// Database configuration assumes mysql on localhost - otherwise edit app/config[/environment]/database.php
	/*
	| Database name.
	|
	*/
    'PHD_DATABASE_NAME' => 'paper_tool',
	
	/*
	| Database user.
	|
	*/
    'PHD_DATABASE_USER' => 'root',
	
	/*
	| Database password.
	|
	*/
    'PHD_DATABASE_PASSWORD' => '',

	/*
	|--------------------------------------------------------------------------
	| Mail "Pretend"
	|--------------------------------------------------------------------------
	|
	| When this option is enabled, e-mail will not actually be sent over the
	| web and will instead be written to your application's logs files so
	| you may inspect the message. This is great for local development.
	|
	*/
	'MAIL_PRETEND' => false,

);