<?php
    // If the user is not authentified, redirect to the login file
	if (!isset($_SERVER['PHP_AUTH_DIGEST'])) header("Location:index.php");
    // Define the main directory path
	define('ROOT_PATH', realpath(__DIR__)."/..");
	// Include the database manager file
	require_once(ROOT_PATH.'/lib/functions.php');
	// Get the file that contain the template of the page
	$page = file_get_contents('view/page.html');
	//Creating the array that contain the stats
	$stats = array();
	//Creating the stats
	
	$stats[] = array('description' => 'Total number of authorized Access',		'query' => 'SELECT count(*) FROM accesslog WHERE permission=1');
	$stats[] = array('description' => 'Total number of unauthorized Access',	'query' => 'SELECT count(*) FROM accesslog WHERE permission=0');
	$stats[] = array('description' => 'Total number of access attempt',			'query' => 'SELECT count(*) FROM accesslog');
	$stats[] = array('description' => 'Total number of users',					'query' => 'SELECT count(*) FROM users');

 	//Generate the content of the statistical page with the stats function above
	// Get the template file that contain the stats table
	$content = file_get_contents('view/partial/stats/statsTable.html');
	// Generate the row of the stats table
	$statsTableContent = createStats($stats);
	// Append the rows in the content area of the stats table template
	$content = str_replace("[[statsTableContent]]", $statsTableContent, $content);
	//Insert the stats table in the content area of the template
	$html = str_replace("[[content]]", $content, $page);
	//Display the page
	echo $html;

	// Generate the table of the stats
	function createStats($stats){
		// Will contain all the stats row
		$statsTable;
		// Foreach stats find in the array above
		foreach($stats as $stat){
			// Execute the sql and get the result
			$result = getStatResult($stat['query']);
			// create a row with the description and the result of the stats. If the query return an SQL error, display a row with an error message
			$result !== null ? $statsTable.= generateStat($stat['description'], $result) : $statsTable.= generateStat('Wrong SQL query', '-');
		}
		// Return all the container
		return $statsTable;
	}

	// Generat a row for a stat
	function generateStat($description, $result){
		// Get the file that contain the template of a stat row 
		$out = file_get_contents('view/partial/stats/statsTableRow.html');
		// Place the stat data in the different data area in the template
		$out = str_replace("[[description]]",$description,$out);
		$out = str_replace("[[result]]",$result,$out);
		// Return the row
		return $out;
	}


?>