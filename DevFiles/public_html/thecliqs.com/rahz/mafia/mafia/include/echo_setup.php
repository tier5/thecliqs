<?php
    error_reporting(E_ALL-E_WARNING-E_NOTICE);	
	
    $inc_path = "/home/dedica/public_html/include/"; //full path to folder include example: /home/cpanel_user_name/public_html/include/  with trailing slash
    
	require_once($inc_pathd."db.php");
	require_once($inc_pathd."form.php");
	require_once($inc_pathd."hitmen.php");
	require_once($inc_pathd."attack.php");
	require_once($inc_pathd."pagination.php");
	require_once($inc_pathd."pagination1.php");
	require_once($inc_pathd."pagination_show.php");
	require_once($inc_pathd."surveys.php");
	
	#connects to db
	#to include in each file where db operations are needed 
	
	    $hostt = $_SERVER['HTTP_HOST'];
		#live server db configs BELOW SET YOUR CONNECTION DETAILS
		$host = 'localhost'; //normally this is ok how it is
		$user = 'dedica82'; //cpanel username or DB username
		$pass = '764890380'; //cpanel pass (if you use db user above use db pass here)
		$database = 'dedica82_mafia';  //database name
   
	# Create global objects
    $db1 = new DB();
    $db1->connect($host,#mysql server
				 "3306",#port
				 $user,#user
				 $pass,#password
				 $database);#database name
				 
		  
          
?>