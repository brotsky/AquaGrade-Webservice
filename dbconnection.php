<?php

// Class for connection with database
class DBConnection 
{
	public function connect()
	{
		$mysqli =mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		return $mysqli;
	}
}
?>