<?php
require('connection.php');
	// Performs a 'mysql_real_escape_string' on the entire array/string
	 function SecureData($data, $types){
		if(is_array($data)){
            $i = 0;
			foreach($data as $key=>$val){
				if(!is_array($data[$key])){
                    $data[$key] = CleanData($data[$key], $types[$i]);
					$data[$key] = mysql_real_escape_string($data[$key]);
                    $i++;
				}
			}
		}else{
            $data = $this->CleanData($data, $types);
			$data = mysql_real_escape_string($data);
		}
		return $data;
	}

    
    // clean the variable with given types
    // possible types: none, str, int, float, bool, datetime, ts2dt (given timestamp convert to mysql datetime)
    // bonus types: hexcolor, email
     function CleanData($data, $type = ''){
        switch($type) {
            case 'none':
                $data = $data;
                break;
            case 'str':
            case 'string':
                settype( $data, 'string');
                break;
            case 'int':
            case 'integer':
                settype( $data, 'integer');
                break;
            case 'float':
                settype( $data, 'float');
                break;
            case 'bool':
            case 'boolean':
                settype( $data, 'boolean');
                break;
            // Y-m-d H:i:s
            // 2014-01-01 12:30:30
            case 'datetime':
                $data = trim( $data );
                $data = preg_replace('/[^\d\-: ]/i', '', $data);
                preg_match( '/^([\d]{4}-[\d]{2}-[\d]{2} [\d]{2}:[\d]{2}:[\d]{2})$/', $data, $matches );
                $data = $matches[1];
                break;
            case 'ts2dt':
                settype( $data, 'integer');
                $data = date('Y-m-d H:i:s', $data);
                break;

            // bonus types
            case 'hexcolor':
                preg_match( '/(#[0-9abcdef]{6})/i', $data, $matches );
                $data = $matches[1];
                break;
            case 'email':
                $data = filter_var($data, FILTER_VALIDATE_EMAIL);
                break;
            default:
                $data = '';
                break;
        }
        return $data;
    }


	  // Executes MySQL query
    function executeSQL($query){
    	$result = mysql_query($query);
        if($result){
            $records 	= @mysql_num_rows($result);
            if($records > 0){
                return arrayResults($result);
            }else{
                return true;
            }

        }else{
            return false;
        }
    }

     function arrayResults($result){
        $arrayedResult = array();
        while ($data = mysql_fetch_assoc($result)){
            $arrayedResult[] = $data;
        }
        return $arrayedResult;
    }

    // Gets a single row from $from where $where is true
     function select($from, $where='', $orderBy='', $limit='', $like=false, $operand='',$cols='*', $wheretypes){
        // Catch Exceptions
        if(trim($from) == ''){
            return false;
        }

        $query = "SELECT {$cols} FROM {$from} WHERE ";

        if(is_array($where) && $where != ''){
            // Prepare Variables
            $where = SecureData($where, $wheretypes);
            foreach($where as $key=>$value){
                if($like){
                    $query .= "{$key} LIKE %{$value}% {$operand} ";
                }else{
                    $query .= "{$key} = {$value} {$operand} ";
                }
            }

            $query = substr($query, 0, -(strlen($operand)+2));

        }else{
            $query = substr($query, 0, -6);
        }

        if($orderBy != ''){
            $query .= ' ORDER BY ' . $orderBy;
        }

        if($limit != ''){
            $query .= ' LIMIT ' . $limit;
        }

        return executeSQL($query);

    }


    // Adds a record to the database based on the array key names
     function insert($table, $vars, $exclude = '', $datatypes){

        // Catch Exclusions
        if($exclude == ''){
            $exclude = array();
        }

        array_push($exclude, 'MAX_FILE_SIZE'); // Automatically exclude this one

        // Prepare Variables
        $vars = SecureData($vars, $datatypes);

        $query = "INSERT INTO {$table} SET ";
        foreach($vars as $key=>$value){
            if(in_array($key, $exclude)){
                continue;
            }
            $query .= "{$key} = '{$value}', ";
        }

        $query = trim($query, ', ');
        echo $query;
        return executeSQL($query);
    }
    
    
    
    // Deletes a record from the database
   function delete($table, $where='', $limit='', $like=false, $wheretypes){
        $query = "DELETE FROM `{$table}` WHERE ";
        if(is_array($where) && $where != ''){
            // Prepare Variables
            $where = SecureData($where, $wheretypes);

            foreach($where as $key=>$value){
                if($like){
                    $query .= "`{$key}` LIKE '%{$value}%' AND ";
                }else{
                    $query .= "`{$key}` = '{$value}' AND ";
                }
            }

            $query = substr($query, 0, -5);
        }

        if($limit != ''){
            $query .= ' LIMIT ' . $limit;
        }

        return executeSQL($query);
    }
    
    
    // Updates a record in the database based on WHERE
    function update($table, $set, $where, $exclude = '', $datatypes, $wheretypes){
        // Catch Exceptions
        if(trim($table) == '' || !is_array($set) || !is_array($where)){
            return false;
        }
        if($exclude == ''){
            $exclude = array();
        }

        array_push($exclude, 'MAX_FILE_SIZE'); // Automatically exclude this one

        $set 	= SecureData($set, $datatypes);
        $where 	= SecureData($where,$wheretypes);

        // SET

        $query = "UPDATE `{$table}` SET ";

        foreach($set as $key=>$value){
            if(in_array($key, $exclude)){
                continue;
            }
            $query .= "`{$key}` = '{$value}', ";
        }

        $query = substr($query, 0, -2);

        // WHERE

        $query .= ' WHERE ';

        foreach($where as $key=>$value){
            $query .= "`{$key}` = '{$value}' AND ";
        }

        $query = substr($query, 0, -5);

        return executeSQL($query);
    }


?>