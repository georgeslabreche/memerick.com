
<?php
	require 'settings.php';

	// we connect
	function db_connect(){
		$link = mysql_connect(DB_DOMAIN, DB_USERNAME, DB_PASSWORD);
		if (!$link) {
			die('Could not connect: ' . mysql_error());
		}
		mysql_select_db(DB_NAME) or die("Unable to select database");
	}
	

	db_connect();
?>	
