<?php

	require_once('../config/database.php');
	require_once('theme_manager.php');

	$content = $_POST["text_content"];
	
	$theme_manager = new ThemeManager(); 
	$year = $theme_manager->getYear();
	$month = $theme_manager->getMonth();

	// Formulate Query
	$query = sprintf(
		"INSERT INTO text(title, content, author, theme_id, year, month) VALUES('%s', '%s', '%s', '%s', '%s', '%s')",
		mysql_real_escape_string($title),
		mysql_real_escape_string($content),
		mysql_real_escape_string($author),
		mysql_real_escape_string($theme_id),
		mysql_real_escape_string($year),
		mysql_real_escape_string($month));
	
	// Execute query
	mysql_query($query);
	
	print $content;
?>
