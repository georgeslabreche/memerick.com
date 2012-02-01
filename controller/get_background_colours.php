<?php
	require_once('../config/database.php');
	require_once('theme_manager.php');
	
	$theme_manager = new ThemeManager();
	$year = $theme_manager->getDisplayedThemeYear();
	$month = $theme_manager->getDisplayedThemeMonth();

	// Formulate Query
	$query = sprintf(
		"SELECT content_colour, sidebar_colour FROM theme_background_colours
		WHERE year='%s' AND month='%s'",
				mysql_real_escape_string($year),
				mysql_real_escape_string($month)
		);
	
	
	print execute_query($query);

?>