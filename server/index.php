<?php

	define('ROOT_PATH', realpath(__DIR__));
	require("lib/functions.php");

	//Get the value of the key in the URL
	$key = $_GET["key"];
	if(!empty($key)){ 
		//Get the user list in the database
		$user = getUserByIdkey($key);
		//If the key is assigned to an user
		if ( is_array($user) ) {
			////Log the access with the user data
			addAccess($key, $user);
			//Return the http code 200 to the lock if the user have the permission to access, 
			//else http code 401
			if($user[0]['permission'] == 1 ){
				header("HTTP/1.1 200 Ok");
			}
			else{
				header("HTTP/1.1 401 Unauthorized");
			}
		}
		else
		{
			//Else if the key doesn't exist, log the access and return the 401 http code
			//Log the access only with the key
			addAccess($key,$user);
			//Return the hhtp code 401 to the lock
			header("HTTP/1.1 401 Unauthorized");	
		}
	}
?>

<?php  include('admin/view/home.php'); ?>
