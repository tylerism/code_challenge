<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'database.php';
include 'controllers/MainController.php';

// Remove the query string
$path = $_SERVER["REQUEST_URI"];
if(count($_GET)){
	$url_parts = explode('?',$path);
	$path = $url_parts[0];
}
// Get the first part of path for base check
$parts = explode('/',$path);
$base_path = $parts[1];

// Create base routes
$routes = [
	"create"=>["method"=>"POST","action"=>"create"],
	"read"=>["method"=>"GET","action"=>"read"],
];

$controller = new MainController();

// Run the action if the method and routes are found
if(isset($routes[$base_path])){
	$do = $routes[$base_path];
	if($_SERVER["REQUEST_METHOD"] == $do["method"]){
		$controller->performAction($do["action"]);
		exit();
	}
}
else{
	// If it does not match any routes, it must be a short url so it is time to redirect
	$controller->redirect($path);
}

http_response_code(404);
exit();

?>

