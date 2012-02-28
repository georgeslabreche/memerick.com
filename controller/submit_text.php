<?php
	require_once('../config/database.php');
	require_once('theme_manager.php');
	require_once('../data/database_manager.php');

	$content = $_POST["text_content"];
	$year = $_POST["year"];
	$month = $_POST["month"];
	
	// Execute query
	$database_manager = new DatabaseManager();
	$database_manager->persistText($content, $year, $month);
	
	print $content;
?>
