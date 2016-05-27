<?php

define('DB_HOSTNAME', 'localhost');        // Database hostname
define('DB_USERNAME', 'trevor_aquagrade'); // Database user name
define('DB_PASSWORD', 'KB5(5GupcO^T');          // Database password for these user
define('DB_DATABASE', 'trevor_aquagrademobile'); // Name of your database


$mysqli = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if(!$mysqli)
	throw new Exception("Database is offline");

?>