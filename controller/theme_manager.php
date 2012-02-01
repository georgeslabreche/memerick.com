<?php

class ThemeManager {

	/**
	 * Get year from url param.
	 * If it's not there then just get the present year.
	 */
	public function getDisplayedThemeYear(){
		if($_GET['year'] != null){
			return (int)$_GET['year'];
		}else{
			return (int)$this->getTodaysYear();
		}
	}
	
	/**
	 * Get month from url param.
	 * If it's not there then just get the present month.
	 */
	public function getDisplayedThemeMonth(){
		if($_GET['month'] != null){
			return (int)$_GET['month'];
		}else{
			return (int)$this->getTodaysMonth();
		}	
	}
	
	/**
	 * Get the present year.
	 */
	public function getTodaysYear(){
		$current_date = getdate();
		return $current_date["year"];
	}
	
	/**
	 * Get the present month.
	 */
	public function getTodaysMonth(){
		$current_date = getdate();
		$mon = $current_date["mon"];
		return $mon;
	}
	
	/**
	 * Get present theme tags.
	 */
	public function getPresentThemeTags(){
		$current_theme_json = $this->getPresentTheme();
		$current_theme_array = json_decode($current_theme_json, true);
		return $current_theme_array[2];
	}
	
	/**
	 * Get present theme.
	 */
	public function getPresentTheme(){
		
		$year = $this->getTodaysYear();
		$month = $this->getTodaysMonth();

		// Get and return current theme
		return $this->getTheme($year, $month);
	}
	
	/**
	 * Get the title of the theme currently being displayed
	 */
	public function getCurrentThemeTitle(){
		$current_theme_json = $this->getCurrentTheme();
		$current_theme_array = json_decode($current_theme_json, true);
		return $current_theme_array[0];
	}
	
	/**
	 * Get the theme currently being displayed.
	 */
	public function getCurrentTheme(){
		
		$year = $this->getDisplayedThemeYear();
		$month = $this->getDisplayedThemeMonth();

		// Get and return current theme
		return $this->getTheme($year, $month);
	}
	
	/**
	 * Get list of current theme tags.
	 * Returns a String of comma separated theme tags.
	 */
	public function getCurrentThemeTags(){
		$current_theme_json = $this->getCurrentTheme();
		$current_theme_array = json_decode($current_theme_json, true);
		return $current_theme_array[2];
	}
	
	/**
	 * Get theme date string.
	 * e.g. "February 2012"
	 */
	public function getDisplayedThemeDate(){
		$year = $this->getDisplayedThemeYear();
		$month_number = $this->getDisplayedThemeMonth();
		$month_name = date( 'F', mktime(0, 0, 0, $month_number, 1));
		
		return $month_name . " " . $year;
	}
	
	/**
	 * Get text box colours of displayed theme.
	 */
	public function getDisplayedThemeTextBoxColours(){
		$year = $this->getDisplayedThemeYear();
		$month = $this->getDisplayedThemeMonth();

		return $this->getTextBoxColours($year, $month);
	}
	
	/**
	 * Get background colours of displayed theme.
	 */
	public function getDisplayedThemeBackgroundColours(){
		$year = $this->getDisplayedThemeYear();
		$month = $this->getDisplayedThemeMonth();

		return $this->getBackgroundColours($year, $month);
	}
	
	/**
	 * Get text box colours based on year and month.
	 * 
	 * @param unknown_type $year
	 * @param unknown_type $month
	 */
	public function getTextBoxColours($year, $month){
		// Formulate Query
		$query = sprintf(
				"SELECT background_colour, font_colour
				FROM theme_text_box_colours WHERE year='%s' AND month='%s'",
				mysql_real_escape_string($year),
				mysql_real_escape_string($month)
			);
		
		return $this->executeQuery($query);
	}
	
	/**
	 * Get background colours based on year and month.
	 * 
	 * @param unknown_type $year
	 * @param unknown_type $month
	 */
	public function getBackgroundColours($year, $month){
		
		// Formulate query
		$query = sprintf(
				"SELECT content_colour, sidebar_colour
				FROM theme_background_colours WHERE year='%s' AND month='%s'",
				mysql_real_escape_string($year),
				mysql_real_escape_string($month)
			);
		
		return $this->executeQuery($query);
	}
	
	/**
	 * Get the a theme based on year and month.
	 * @param unknown_type $year
	 * @param unknown_type $month
	 */
	public function getTheme($year, $month){
	
		// Formulate Query
		$query = sprintf(
				"SELECT name, description, tags
				FROM themes WHERE year='%s' AND month='%s'",
				mysql_real_escape_string($year),
				mysql_real_escape_string($month)
			);
			
		return $this->executeQuery($query);
	}
	
	/**
	 * Execute a query and return the result in json format.
	 * 
	 * @param unknown_type $query
	 */
	private function executeQuery($query){
		// Perform Query.
		$result = mysql_query($query);
		
		// Fetch row.
		$result_row = mysql_fetch_row($result);
		
		// Return theme name
		return json_encode($result_row);
	}
	
	
}
?>