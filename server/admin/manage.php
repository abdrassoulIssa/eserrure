<?php
    // If the user is not authentified, redirect to the login file
	if (!isset($_SERVER['PHP_AUTH_DIGEST'])) header("Location:index.php");
    // Define the main directory path
	define('ROOT_PATH', realpath(__DIR__)."/..");
	// Include the database manager file
	require_once(ROOT_PATH.'/lib/functions.php');
	// Get the file that contain the template of the page
	$page = file_get_contents('view/page.html');
	//Display errors of the CRUD
	// If there is an status in the URL
	if(isset($_GET['status'])) {
		// Switch on this error.
		switch($_GET['status']){
			// Display a message of create confirmation
			case 'createOk':
				$content = "<div class='alert alert-success'>The user/key creation is successful</div>";
				break;
			// Display a message of update confirmation
			case 'updateOk':
				$content = "<div class='alert alert-success'>The user update is successful</div>";
				break;
			// Display a message of delete confirmation
			case 'deleteOk':
				$content = "<div class='alert alert-success'>The user delete is successful</div>";
				break;
			// Display a message that says there was an error with the insert/update action of a key
			case 'errorKey':
				$content = "<div class='alert alert-danger'>The key already exist in the database</div>";
				break;
			case 'errorExist':
				$content = "<div class='alert alert-danger'>The key does not exist in the database</div>";
				break;
			// By default, display nothing
			default:
				break;
		}
	}

    //Get the user list in the database
	$users = getUsers();
    //Create the table of the user
	// Get the template file that contain the the users table
	$content .= file_get_contents('view/partial/users/usersTable.html');
	// If user(s) exist in the database, create the row(s)
	if(is_array($users)) $usersTableContent = showUsersTable($users);
	// Append the rows of the table in the table of users
	$content = str_replace("[[usersTableContent]]", $usersTableContent, $content);
	//Insert the users table in the content area of the template
	$html = str_replace("[[content]]", $content, $page);
	//Display the page
	echo $html;
    
    // Function that create the users table rows
	function showUsersTable($users){
		// Will contain the users table rows
		$usersTable;
		// Foreach users find in the database, create a row in the users table
		foreach ($users as $user) {
			// Get the template that contain a user table row
			$line = file_get_contents('view/partial/users/usersTableRow.html');
			// Place the user data in the different data area in the template
			$line = str_replace("[[iduser]]",$user['iduser'],$line);
			$line = str_replace("[[firstname]]",$user['firstname'],$line);
			$line = str_replace("[[lastname]]",$user['lastname'],$line);
			$line = str_replace("[[idcard]]",$user['idcard'],$line);
			$line = validePermission($line, $user['permission']);
			// Append the row to the users table rows
			$usersTable.= $line;
		}
		// Return all the row
		return $usersTable;
	}

	// Display the correct permission in the select input form element in the row of all user
	function validePermission($line, $permission){
		switch($permission){
			// If the permission is 1, it mean that the user have the permission to open the door
			case '1':
				$line = str_replace("[[selectedAllowed]]","selected",$line);
				$line = str_replace("[[selectedDenied]]","",$line);
				break;
			// If the permission is 1, it mean that the user don't have the permission to open the door
			case '2':
				$line = str_replace("[[selectedAllowed]]","",$line);
				$line = str_replace("[[selectedDenied]]","selected",$line);
				break;
			// By default, select nothing in the select input
			default :
				$line = str_replace("[[selectedAllowed]]","",$line);
				$line = str_replace("[[selectedDenied]]","",$line);
				break;
		}
		// Return the data
		return $line;
	}

?>