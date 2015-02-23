<?php
	require('classDB.php');
	
	function getUsers(){
		$val    = "users.idcard";
		$where  = array('card.idcard' =>  $val);
		$fields = "card.idcard,iduser,firstname,lastname,idkey,permission";
		return select('users ,card ', $where, '', '', false, 'AND', $fields, array('string'));
	}

	function getUserByIdkey($key){
		$val = "users.idcard";
		$where = array('card.idcard' =>  $val, 'card.idkey' => $key);
		$fields = "card.idcard,iduser,firstname,lastname,idkey,permission";
		return select('users,card', $where, '', '', false, 'AND', $fields, array('string', 'int'));
	}

	function getUserByIdCard($idcard){
		$val = "users.idcard";
		$where = array('card.idcard' =>  $val, 'users.idcard' => $idcard);
		$fields = "card.idcard, iduser,firstname,lastname,idkey,permission";
		return select('users,card', $where, '', '', false, 'AND', $fields, array('string', 'int'));
	}

	function getCards(){
		return select('card', '', '', '', false, 'AND', '*', '');
	}

	function addCard($idkey){
		$vars = array('idkey' =>  $idkey);
		return insert('card', $vars, '', array('string'));
	}

	function getCardById($idcard){
		$where   = array('idcard' => $idcard);
		return select('card', $where, '', '', false, 'AND', '*', array('int'));
	}

	function getCardByIdKey($idkey){
		$where   = array('idkey' => $idkey);
		return select('card', $where, '', '', false, 'AND', '*', array('string'));
	}

	function addUser($user){
		return insert('users', $user, '', array('string','string','int', 'int'));
	}


	function updateUser($id, $user){
		return update('users', $user, array('iduser'=>$id), '', array('string', 'string', 'int', 'int'), array('int'));
	}

	function deleteUser($id){
		return delete('users', array('iduser'=>$id), '', false, array('int'));
	}



	/***********************
	 * History functions   *
	 ***********************/

	function getHistory($begin = null, $end = null){

		$beginFinal = '';
		$endFinal = '';


		if(isset($begin) || isset($end)){
			$validDateBegin = validateDate($begin, 'm/d/Y');
			$validDateEnd   = validateDate($end, 'm/d/Y');
			if(($validDateBegin == 1) || ($validDateBegin == 1)){
				$beginFinal = date('Y-m-d H:i:s', strtotime($begin));
				$endFinal   = date('Y-m-d H:i:s', strtotime($end));
				$query = "SELECT * FROM accesslog WHERE date BETWEEN '$beginFinal' AND '$endFinal' ORDER BY date DESC";
				return executeSQL($query);
			}
		}

		return select('accesslog', $where, 'date DESC', '', false, 'AND', '*', $whereTypes);
	}

	/********************
	 * Date validating  *
	 ********************/

	function validateDate($date, $format = 'Y-m-d H:i:s')
	{
	    $d = DateTime::createFromFormat($format, $date);
	    return $d && $d->format($format) == $date;
	}

	function getStatResult($query){
		$result = executeSQL($query);
		if($result) return array_values($result[0])[0];
		return null;
	}



	function addAccess($key, $user){
		// This object will contain all the data of the log
		$access = array();
		// Set the date of access (now)
		$access['date'] = date('Y-m-d H:i:s');
		// If a user has the key, $user will contain the user information
		if($user != 1) {
			//$user = $user[0];
			// We don't need the user id, we remove it from the user table
			unset($user[0]['iduser']);
			unset($user[0]['idcard']);
			// Merge the array that contain the user data and the access log that contain the date
			$access = array_merge($user[0], $access);
			// Insertion of the log in the database
			insert('accesslog', $access, '', array('string','string','string', 'int', 'datetime'));
		// Else if no user have this key assigned 
		} else{
			// Set isAllowed to 0
			$access['permission'] = 0;
			$access['firstname']  = "unknown";
			$access['lastname']   = "unknown";
			// Set the key in the data to insert. We didn't do that before becouse the key id is already present in the data of the user.
			$access['idkey'] = $key;
			// Insertion of the log in the database
			return insert('accesslog', $access, '', array('datetime','int','string','string','string'));
		}
	}


?>