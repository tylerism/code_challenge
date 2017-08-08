<?php

function get_connection(){
	$servername = "localhost";
	$username = "root";
	$password = "***";
	$dbname = "cloudflare";
	$con = new mysqli($servername, $username, $password, $dbname);
	return $con;
}

?>
