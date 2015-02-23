<?php
    //If the user is not authentified, redirect to the login file
	if (!isset($_SERVER['PHP_AUTH_DIGEST'])) header("Location:index.php");
    //Define the main directory path
	define('ROOT_PATH', realpath(__DIR__)."/../..");
	//Include the database manager file
	require_once(ROOT_PATH.'/lib/functions.php');
	//Get the action in the URL
	$action = $_GET['action'];
	//Will set to true if error
	$error = null;
	//Switch on the action to determine what we have to do
	switch($action) {
	    case 'create':
	    	$status = createTheUser();
	        break;

	    case 'update':
	    	$status = updateTheUser();
	        break;

	    case 'delete':
	    	$status = deleteTheUser();
	        break;

	    case 'createkey':
	    {
	    	$status = createNewKey();
	        break;
	    }

	    default:
	        break;
	}
    //Update the offline file
	if(!$error){

		$users = getUsers();
		foreach($users as $user) {
			$out .= "# $user[firstname] $user[lastname] \n $user[idkey] $user[permission] \n";
		}
		file_put_contents(ROOT_PATH."/files/keys.txt", $out);
	}

	if ( ($status === 'createKeyOK')) {
		header('location:../history.php?status='.$status);
	}
	else{
	    //Redirect to the main page
		header('location:../manage.php?status='.$status);
	}

	function createNewKey(){
		$key  = getCardByIdKey($_POST['idkey']);
		if (!is_array($key)) {
			addCard($_POST['idkey']);
			return 'createKeyOK';
		}
		return 'errorExist';
	}

	function createTheUser(){

		// Referencing the database class variable as global
		global $error;

		// Get the values of the form
		$user = array('firstname' => $_POST['firstname'], 
					  'lastname' => $_POST['lastname'], 
			          'idcard' => $_POST['idcard'], 
			          'permission' => $_POST['permission']);

		$users = getUserByIdCard($_POST['idcard']);
		$key  = getCardById($_POST['idcard']);
		if(!empty($users['idcard'])){
			// If there is a single user that old the key
			// If the user that hold the key isn't the same user of the update
			if($users[0]['iduser'] != $id){
				$error = true;
				// return an error and do nothing else
				return 'errorKey';
			}

		}
		else if (empty($key[0]['idcard'])) {
			$error = true;
			return 'errorExist' ;
		}

		// If the permission is not set (when you create a key with the history table for exemple)
		// set the permission as denied
		if(!isset($_POST['permission'])){
			$user['permission'] = 2;
		}
     	// Add the user in the database
		addUser($user);
		// Return a confirmation message
		return "createOk";
	}

	function updateTheUser(){
		// Referencing the database class variable as global
		global $error;			
		// Get the id of the user
		$id = $_GET['iduser'];
		// Get the values of the form
		$user = array('firstname' => $_POST['firstname'], 
			          'lastname' => $_POST['lastname'], 
			          'idcard' => $_POST['idcard'], 
			          'permission' => $_POST['permission']);
		// check if the key doesn't already exist
		// Select all user in the database with hold the key
		$users = getUserByIdCard($_POST['idcard']);
		$card  = getCardsById($_POST['idcard']);
		if(!empty($users['idcard'])){
			// If there is a single user that old the key
			// If the user that hold the key isn't the same user of the update
			if($users[0]['iduser'] != $id){
				$error = true;
				// return an error and do nothing else
				return 'errorKey';
			}

		}
		else if (empty($card[0]['idcard'])) {
			$error = true;
			return 'errorExist' ;
		}

     	// Add the user in the database
		updateUser($id, $user);
		// Return a confirmation message
		return "updateOk";
		
	}

	function deleteTheUser(){
		// Referencing the database class variable as global
		// Get the id of the user
		$id = $_GET['iduser'];
		// Delete the user form the database
		deleteUser($id);
		// Return a confirmation message
		return "deleteOk";
	}
	
?>