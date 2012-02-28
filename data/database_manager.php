<?php

/*
 * This class implements database operations.
 */
class DatabaseManager {
	
	/**
	 * Persist text.
	 * 
	 * @param $content
	 * @param $year
	 * @param $month
	 */
	public function persistText($content, $year, $month){
		// Formulate Query
		$query = sprintf(
			"INSERT INTO text(content, year, month) VALUES('%s', '%s', '%s')",
			mysql_real_escape_string($content),
			mysql_real_escape_string($year),
			mysql_real_escape_string($month));
		
		// Execute query
		mysql_query($query);
	}
	
	/**
	 * Get texts based on year and month
	 *
	 * @param $year
	 * @param $month
	 */
	public function getTextContributions($year, $month){
		// Formulate Query
		$query = sprintf(
			"SELECT * FROM text
			WHERE year='%s' AND month='%s' AND safe='1'",
					mysql_real_escape_string($year),
					mysql_real_escape_string($month)
			);
			
		return $this->executeMultiResultQuery($query);
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
			
		return $this->executeSingleResultQuery($query);
	}
	
	/**
	 * Execute a query and return a single row result in json format.
	 * 
	 * @param unknown_type $query
	 */
	private function executeSingleResultQuery($query){
		// Perform Query.
		$result = mysql_query($query);
		
		// Fetch row.
		$result_row = mysql_fetch_row($result);
		
		// Return theme name
		return json_encode($result_row);
	}
	

	/**
	 * Execute a query and return a multi row result in json format.
	 *
	 * @param unknown_type $query
	 */
	private function executeMultiResultQuery($query){
		// Perform Query
		$result = mysql_query($query);
		
		$rows = array();
		
		while($row = mysql_fetch_assoc($result)) {
			$rows[] = $row;
		}
		
		// Free the resources associated with the result set
		mysql_free_result($result);	
	
		return json_encode($rows);
	}
}

?>