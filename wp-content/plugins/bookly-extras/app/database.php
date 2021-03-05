<?php

namespace App;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{
	function __construct() {

		$capsule = new Capsule;

		$capsule->addConnection([
			'driver'    => 'mysql',
			'host'      => getenv('DB_HOST'),
			'database'  => getenv('DB_NAME'),
			'username'  => getenv('DB_USER'),
			'password'  => getenv('DB_PASSWORD'),
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => 'cutaway_',
		]);

		// Setup the Eloquent ORM...
		$capsule->setAsGlobal();
		$capsule->bootEloquent();
	}
}
