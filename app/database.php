<?php

function get_connection(){
	$servername = "localhost";
	$username = "root";
	$password = "callf0rplast1chang3rs";
	$dbname = "cloudflare";
	$con = new mysqli($servername, $username, $password, $dbname);
	return $con;
}

?>
