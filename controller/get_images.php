<?php
	
	// This script is expensive.
	// Remove memory limit to execute it.
	ini_set('memory_limit','-1');

	require_once('../config/settings.php');
	require_once('../config/database.php');
	require_once('../phpFlickr/phpFlickr.php');
	require_once('theme_manager.php');
	
	/*
	 * Flickr Photo Search.
	 * 
	 * Returns a JSON array result with all images of the current theme.
	 * The current theme is determined by the current date.
	 * 
	 * Each object of the returned result JSON array will have: 
	 * 	- The image rendition url to display.
	 * 	- The url to the image's Flickr page.
	 * 
	 * The actual retrieval of the images is done throught the Flick photo search API:
	 * http://www.flickr.com/services/api/flickr.photos.search.html
	 * 
	 * Example of JSON returned by Flickr with 1 image result:
	 * 	{	
	 * 		"page":1,
	 * 		"pages":1,
	 * 		"perpage":6,
	 * 		"total":"1",
	 * 		"photo":[{
	 * 					"id":"6736805631",
	 * 					"owner":"53074291@N04",
	 * 					"secret":"17530297c8",
	 * 					"server":"7021",
	 * 					"farm":8,
	 * 					"title":"Baby Elephant",
	 * 					"ispublic":1,
	 * 					"isfriend":0,
	 * 					"isfamily":0
	 * 				}]
	 * }
	 * 
	 * That returned Flickr JSON will be parsed and, based on the example above, we'll return
	 * something like this:
	 * 
	 * [{
	 * 		"photo_page_url" : "http://www.flickr.com/photos/53074291@N04/6736805631/",
	 * 		"photo_display_rendition_url" : "http://farm8.staticflickr.com/7021/6736805631_17530297c8_m.jpg",
     *		"photo_display_rendition_width" : "240",
     *		"photo_display_rendition_height": "180"
	 * }] 
	 */
	
	// Get image tags for the current theme
	$theme_manager = new ThemeManager();
	$displayed_theme_tags = $theme_manager->getDisplayedThemeTags();
	
	// If theme exist for the given year and month
	if($displayed_theme_tags != null){
		
		// Only get images that have been approved (tagged as safe)
		$displayed_theme_tags = $displayed_theme_tags . "," . FLICK_IMAGE_SAFE_TAG;
	
		// Create instance of phpFlick so that we can post requests to Flickr
		$phpFlickr = new phpFlickr(FLICKR_API_KEY, FLICKR_API_SECRET, true);
		
		// Search by the current theme tags and our Flickr account user id.
		$displayed_theme_photos = $phpFlickr->photos_search(array("user_id"=>FLICKR_USER_ID, "tags"=>$displayed_theme_tags, "tag_mode"=>"all"));
	
		// Build json result and return it	
		foreach ((array)$displayed_theme_photos['photo'] as $photo) {
	    	$photo_page_url = "http://www.flickr.com/photos/$photo[owner]/$photo[id]";
	    	
	    	// Get photo sizes
	    	$photo_sizes = $phpFlickr->photos_getSizes($photo["id"]);
	    	
	    	// Get required data for photo rendition we wish to display.
	    	foreach ((array)$photo_sizes as $photo_size) {
	
	    		if($photo_size["label"] == FLICKR_IMAGE_SIZE_TO_DISPLAY){
					$photo_display_rendition_url = $photo_size["source"];
	    			$photo_display_rendition_width = $photo_size["width"];
	    			$photo_display_rendition_height = $photo_size["height"];
	    			break;
	    		}
	    		
	    	}
	
	    	// Build array of data of photos to display
	    	$rows[] = array(
	            "photo_page_url" => $photo_page_url,
	            "photo_display_rendition_url" => $photo_display_rendition_url,
	    		"photo_display_rendition_width" => $photo_display_rendition_width,
	    		"photo_display_rendition_height" => $photo_display_rendition_height
	    	);
	    	
		}
		
		// Return result
		print json_encode($rows);
		
	}else{
		// If theme doesn't exist for the given year and month, return empty result.
		print "";
	}
?>