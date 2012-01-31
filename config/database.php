
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
	
	function execute_query($query){
		// Perform Query
		$result = mysql_query($query);
		
		$rows = array();
		
		while($row = mysql_fetch_assoc($result)) {
			$rows[] = $row;
		}
		
		// Free the resources associated with the result set
		mysql_free_result($result);	
	
		return json_encode($rows);
	}
	
	
	db_connect();
?>	
