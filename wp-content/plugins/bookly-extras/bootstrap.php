<?php

require dirname(__FILE__) . '/vendor/autoload.php';
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

require_once __DIR__ . '/app/database.php';

use App\Database;

new Database();
