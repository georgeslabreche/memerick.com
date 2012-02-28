
<?php
	require_once('../config/database.php');
	require_once('theme_manager.php');
	require_once('../data/database_manager.php');
	
	// Get theme
	$theme_manager = new ThemeManager();
	$year = $theme_manager->getDisplayedThemeYear();
	$month = $theme_manager->getDisplayedThemeMonth();
	
	// Execute query
	$database_manager = new DatabaseManager();
	print $database_manager->getTextContributions($year, $month);
	
?>