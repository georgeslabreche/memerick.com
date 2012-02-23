<?php
	require_once('../config/settings.php');
	require_once('../config/database.php');
	require_once('../phpFlickr/phpFlickr.php');
	require_once('theme_manager.php');

	// Retrieve path to where the file has been temporarily stored.
	$temp_filename = $_FILES['image_file']['tmp_name'];
	$year = $_POST["year"];
	$month = $_POST["month"];

	// Create instance of phpFlick so that we can post requests to Flickr
	$phpFlickr = new phpFlickr(FLICKR_API_KEY, FLICKR_API_SECRET, true);
	$phpFlickr->setToken(FLICKR_TOKEN);
	
	// Get the current theme's tags based on the current date.
	// We will apply these tags on the image upload
	$theme_manager = new ThemeManager();
	$current_theme_tags = $theme_manager->getThemeTags($year, $month);
	
	// From the temp folder where the file was uploaded, upload to Flickr.
	$photo_id = $phpFlickr->sync_upload($temp_filename, null, null, $current_theme_tags);

	print $photo_id;	
?>
