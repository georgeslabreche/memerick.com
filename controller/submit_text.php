<?php

	require_once('../config/database.php');

	$content = $_POST["text_content"];
	
	$current_date = getdate();
	$current_year = $current_date["year"];
	$current_month = $current_date["mon"];

	// Formulate Query
	$query = sprintf(
		"INSERT INTO text(title, content, author, theme_id, year, month) VALUES('%s', '%s', '%s', '%s', '%s', '%s')",
		mysql_real_escape_string($title),
		mysql_real_escape_string($content),
		mysql_real_escape_string($author),
		mysql_real_escape_string($theme_id),
		mysql_real_escape_string($current_year),
		mysql_real_escape_string($current_month));
	
	// Execute query
	mysql_query($query);
	
	print $content;
?>
