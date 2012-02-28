<?php
include_once PROJECT_ROOT . 'data/database_manager.php';

class ThemeManager {
	private $database_manager;
	
	function __construct() {
		$this->database_manager = new DatabaseManager();
	}

	/**
	 * Get CSS filename to use for currently displayed theme.
	 */
	public function getDisplayedThemeCssFilename(){
		$filename = "css/theme-" . $this->getDisplayedThemeYear() . "-" . $this->getDisplayedThemeMonth() . ".css";
		
		if(file_exists($filename)){
			return $filename;
		}else{
			// use default CSS file if theme one doesn't exist.
			return "css/theme-default.css";
		}
	}
	
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
	public function getDisplayedThemeTitle(){
		$current_theme_json = $this->getDisplayedTheme();
		$current_theme_array = json_decode($current_theme_json, true);
		return $current_theme_array[0];
	}
	
	/**
	 * Get the theme currently being displayed.
	 */
	public function getDisplayedTheme(){
		
		$year = $this->getDisplayedThemeYear();
		$month = $this->getDisplayedThemeMonth();

		// Get and return current theme
		return $this->getTheme($year, $month);
	}
	
	/**
	 * Get list of current theme tags.
	 * Returns a String of comma separated theme tags.
	 */
	public function getDisplayedThemeTags(){
		$displayed_theme_json = $this->getDisplayedTheme();
		$displayed_theme_array = json_decode($displayed_theme_json, true);
		return $displayed_theme_array[2];
	}
	
	/**
	 * Get the description of the currently displayed theme.
	 */
	public function getDisplayedThemeDescription(){
		$current_theme_json = $this->getDisplayedTheme();
		$current_theme_array = json_decode($current_theme_json, true);
		return $current_theme_array[1];
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
	 * Get the a theme based on year and month.
	 * @param unknown_type $year
	 * @param unknown_type $month
	 */
	public function getTheme($year, $month){
		return $this->database_manager->getTheme($year, $month);
	}
	
	/**
	 * Get theme tags for given year and month.
	 * 
	 * @param unknown_type $year
	 * @param unknown_type $month
	 */
	public function getThemeTags($year, $month){
		$theme_json = $this->getTheme($year, $month);
		$theme_array = json_decode($theme_json, true);
		return $theme_array[2];
	}

}
?>