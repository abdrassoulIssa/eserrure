<?php
// Get the configuration file that contain the login data for the database
$config = parse_ini_file(ROOT_PATH."/config.ini", true)['database'];
// Open a connetion with the database
$link = mysql_connect($config['hostname'], $config['username'] , $config['password'])or die('Could not connect to server.' );
mysql_select_db($config['database']) or die('Could not select database.');

?>