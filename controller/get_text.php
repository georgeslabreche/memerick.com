
<?php
	require_once('../config/database.php');
	
	// First we need to get the current theme. In order to achieve this we need
	// to figure out what is the current year and month.
	$current_date = getdate();
	$current_year = $current_date["year"];
	$current_month = $current_date["mon"];
	
	// Formulate Query
	$query = sprintf(
		"SELECT * FROM text
		WHERE year='%s' AND month='%s'",
				mysql_real_escape_string($current_year),
				mysql_real_escape_string($current_month)
		);
	
	
	// Perform Query
	$result = mysql_query($query);
	
	$rows = array();
	
	while($row = mysql_fetch_assoc($result)) {
		$rows[] = $row;
	}
	
	// Free the resources associated with the result set
	mysql_free_result($result);	

	print json_encode($rows);
	
?>