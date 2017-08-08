<?php

	class MainController{

		public function create(){

			// Establish DB connection
			$con = get_connection();
	
			// Get Params
			$url = $_POST['url'];
			$url = $con->real_escape_string($url);
			if($url==""){
				http_response_code(400);
				$this->returnJsonResponse(['status'=>'fail','message'=>'no url passed']);
			}
			if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
				http_response_code(400);
				$this->returnJsonResponse(['status'=>'fail','message'=>'not a valid url']);
			}
			$code = base_convert(microtime(false), 10, 36);	
			$sql="SELECT code from link where code='$code'";			
			$result = mysqli_query($con,$sql);
			while(mysqli_num_rows($result)){
				$code = base_convert(microtime(false), 10, 36);
				$sql="SELECT code from link where code='$code'";	
				$result=mysqli_query($con,$sql);
			}
			$sql="INSERT into link (url,code) values('$url','$code')";			
			$result = mysqli_query($con,$sql);
			$data['status'] = "success";	
			$this->returnJsonResponse($data);
		}

		// Gets stats about the links. If no code or URL is provided, it gets all of the links, (10 at a time, so you must specifiy page numbers)
		// If a URL or code is provided, it will get more granular data about that particular link such as visitation stats
		public function read(){

			// Establish DB connection
			$con = get_connection();
	
			// Get Params
			$code = isset($_GET['code']) ? $_GET['code'] : false;
			$url = isset($_GET['url']) ? $_GET['url'] : false;
			$url = $con->real_escape_string($url);
			$page = isset($_GET['page']) ? $_GET['page'] : false;	
			$range = isset($_GET['range']) ? $_GET['range'] : false;	
	
			$add = "";
			
			// Build out the range functionality. For speed reasons I am making the user specify when they want specific ranges of counts
			// as opposed to getting all of them everytime
			if($range){
				switch($range){
					case "day":			
						$add = " AND (click.datetimeClick < NOW() and click.datetimeClick > DATE_SUB(NOW(), INTERVAL 24 HOUR))";
						break;
					case "week":			
						$add = " AND (click.datetimeClick < NOW() and click.datetimeClick > DATE_SUB(NOW(), INTERVAL 1 WEEK))";
						break;
				}
			}
			else{
				$range = "all";
			}
			
			// Retrieve the links by either the code or the URL. If code AND url are specified, it ignores the url param
			if($code){		
				$sql="SELECT * from click left join link on link.code = click.code where click.code='$code'" . $add;
			}
			elseif($url){
				$sql="SELECT * from click left join link on link.code = click.code where link.url='$url'" . $add;
			}
			else{
				// Limiting 10 per page to account for potentially millions of urls
				$count_per_page = 10;		
				if($page>0){		
					$page = $page - 1;
				}	
				$offset = $page ? $count_per_page * $page : 0;
				$sql="SELECT * from link LIMIT 10 OFFSET $offset";
			}
			if ($result=mysqli_query($con,$sql)){
				if(mysqli_num_rows($result)){
					while($row = $result->fetch_assoc()) {
						$rows[] = $row;
					}
				} else {
					$rows = [];
				}
				
				$data['status'] = "success";
				
				if($code || $url){
					$data['data'] = ['count'=>count($rows),'clicks'=>$rows,'range'=>$range];		
				}
				else{
					$data['data'] = ['count'=>count($rows),'links'=>$rows];
				}			
				$this->returnJsonResponse($data);
			}		
		}

		// Redirects user to the route by the short code
		public function redirect($code){
	
			// Establish DB connection
			$con = get_connection();
			$code = ltrim($code, '/');	
			$sql="SELECT url from link where code='$code'";
			$result = mysqli_query($con,$sql);
			while($i = mysqli_fetch_array($result)) {		
				$source = array_key_exists('HTTP_REFERER',$_SERVER) ? $_SERVER['HTTP_REFERER'] : "";
				$sql="INSERT into click (code,ip,source) values('$code','".$_SERVER["REMOTE_ADDR"]."','$source')";	
				$result = mysqli_query($con,$sql);
				header("Location: " . $i[0]);
				exit();
			}

		}
		
		// Simple function to perform a class method passed as a param
		public function performAction($action){
			$this->$action();
		}
		
		//
		public function returnJsonResponse($data){
			header('Content-Type: application/json');
			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Methods: GET, POST');
			echo json_encode($data);
			exit();
		}	
	}


?>
