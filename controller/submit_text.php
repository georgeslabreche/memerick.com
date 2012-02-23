<?php

	require_once('../config/database.php');
	require_once('theme_manager.php');

	$content = $_POST["text_content"];
	$year = $_POST["year"];
	$month = $_POST["month"];
	
	// Formulate Query
	$query = sprintf(
		"INSERT INTO text(content, year, month) VALUES('%s', '%s', '%s')",
		mysql_real_escape_string($content),
		mysql_real_escape_string($year),
		mysql_real_escape_string($month));
	
	// Execute query
	mysql_query($query);
	
	print $content;
?>
