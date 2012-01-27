<?php

class ThemeManager {

	public function getCurrentThemeTitle(){
		$current_theme_json = $this->getCurrentTheme();
		$current_theme_array = json_decode($current_theme_json, true);
		return $current_theme_array[0];
	}
	
	public function getCurrentTheme(){
		// First we need to get the current theme. In order to achieve this we need
		// to figure out what is the current year and month.
		$current_date = getdate();
		$current_year = $current_date["year"];
		$current_month = $current_date["mon"];

		// Get and return current theme
		return $this->getTheme($current_year, $current_month);
	}
	
	public function getTheme($year, $month){
	
		// Formulate Query
		$query = sprintf(
				"SELECT name, description, tags
				FROM themes WHERE year='%s' AND month='%s'",
				mysql_real_escape_string($year),
				mysql_real_escape_string($month)
			);
			
		// Perform Query.
		$result = mysql_query($query);
		
		// Fetch row.
		$result_row = mysql_fetch_row($result);
		
		// Return theme name
		return json_encode($result_row);
	}
	
	/*
	 * Get list of current theme tags.
	 * Returns a String of comma separated theme tags.
	 */
	public function getCurrentThemeTags(){
		$current_theme_json = $this->getCurrentTheme();
		$current_theme_array = json_decode($current_theme_json, true);
		return $current_theme_array[2];
	}
}
?>