<?php
session_start();

require("reuseable.php");
//showpassingvars(); 
echo"<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">\r\n";
echo"<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">\r\n";
echo"<html>\n"; 
echo"<head>\n"; 
echo"<title>dedicated gaming network master Admin Panel</title>\n"; 
echo"<meta name='author' content='Theodore Gaushas '>";
echo"<meta name='title' content='dedicated gaming network'>";
echo"<meta name='description' content='dedicated gaming network Admin Panel'>";
echo"<link rel='stylesheet' href='tmgrstyles.css' type='text/css'>\n"; 
echo"</head>\n";
echo"<body>\n";
showlogos();
//connect to the database.
if(!isset($_SESSION['user'])){
	echo"<a href='index.php'>Login first</a>\n";
}else{
	echo"<div align=right>\n";
	endsess();
	echo"</div>";
}
$link=connectmysql();
$conn=connectdb($_SESSION['dbname'], $link);

echo"<br><br>\n";
if(isset($_POST['tablename'])){
	$_SESSION['tablename']=$_POST['tablename'];
}
//menu
echo"<table><tr>\n";
echo"<td>";
    $value="Table Manager Start"; //Choose DB
	goto("", "", 'index.php', 'but', 'db', $value ); 
     echo"</td><td>\n";  	    
        $value="$_SESSION[dbname] Start"; //Table Manager Start
		goto("", $_SESSION['dbname'], 'index.php', 'but', 'tablestart', $value ); 
      echo"</td><tr></table>\n"; 
	//$value="Table Manager Start";
	//goto(" ", $_SESSION['dbname'], 'index.php', 'but', 'start', $value );
	
//*************** nextb *****************
if(isset($_POST['nextb'])){
	$prik="";
	$q=" ";
	$x=0;
	$iskey ="false";
	$flds = $_POST['flds'];
	$leng = $_POST['leng'];
	$fname=$_POST['fname'];
	$ftype=$_POST['ftype'];
       
   if(isset($_POST['others']) ){
        $others=$_POST['others'];
    }
	$tablename=$_POST['tblname'];
	
	$dbname=$_SESSION['dbname'];
	$query="Create table $tablename (";
	for($i=0; $i < $flds; $i++){
	//$fname=$_SESSION['fname'];
		$q.= " $fname[$i] ";
		// Field size
		if(isset($leng[$i]) && $leng[$i] !=""){
            $leng[$i]=StripSlashes($leng[$i]);
			$q .= "$ftype[$i](".$leng[$i] .") ";
          //  echo"Length $leng[$i]";
		}else{
			$q.= "$ftype[$i] ";
		}
		//Primary Key
                if(isset($_POST['pk'])){
                    $pk=$_POST['pk'];
                }
		if(isset($pk[$i])&& $pk[$i]!=""){
			$prik[$x] = $fname[$i] ;
			$x ++;
			$iskey="true";
            //if(!eregi("not null", $others[$i]) || !eregi("unique", $others[$i])){
			if(!eregi("NOT NULL", $others[$i]) || !eregi("unique", $others[$i])){
			$q.="NOT NULL ";
			}
		}
		
		//default
		if (isset($_POST['def'] )) {
            $def=$_POST['def'];  
            if( $def[$i] != ""){
			    $q.="DEFAULT  '$def[$i]' " ;
            }
		}
		if(isset($_POST['nu'])){
            $nu=$_POST['nu'];
            if(isset( $nu[$i] ) && $nu[$i]=='yes'){             
			    $q.="NOT NULL ";
            }
		}
		
		//Other flags $others=$_POST['others'];
		
             if(isset($others[$i]) && $others[$i] !=""){
			    $q.=" ". $others[$i] ;
		    }
		
		if($i < $flds-1){
			$q.=", ";
		}
	}
	$pkey="";
        if(count($prik)>=1){
	        for($k =0; $k < count($prik); $k ++){

		        $pkey .= "$prik[$k]";
		        if ($k < count($prik)-1){
			        $pkey.= ", ";
		        }
	        }
        }
	$prikey="";
	if(count($prik) >= 1 && $iskey=="true"){
		$prikey=", PRIMARY KEY($pkey) ";
	}
	$query.= "$q $prikey )";
//echo" Q: $query"; 
	$result=exequery($query, $tablename, $dbname);
	if($result){
		echo"Table $tablename successfully created. <br><br>\n";			
		$value="View  $tablename";
		goto($tablename, $dbname, 'index.php', 'but', 'view', $value );		
		echo"<br><br>";
	}
}else{
//*************** field Sizes *****************
if(isset($_POST['nexta'])){
	if(isset($_POST['tblname'])){
		$tblname = $_POST['tblname'];
	
		echo"<h2 class=h>Create Table:3 $tblname</h2>\n";
		
		echo"<form action=create.php method=post>\n";
		echo"<table border=0 width='100%' align='center'>\n";		
		//echo"<input type=hidden name='dbname' value='$dbname'>";
		echo"<input type=hidden name='tblname' value='$tblname'>";
		echo"<input type=hidden name='flds' value=$_POST[flds]>\n";

		echo"<tr><td>Primary Key</td><td>Field Name</td><td>Type</td><td>Size or Set</td>\n";
		echo"<td>NULL</td><td>Default</td><td>Other Flags</td></tr>\n";
		if(isset($_POST['flds'])){
			$flds=$_POST['flds'];
			$fname=$_POST['fname'];
			//$_SESSION['fname']=$_POST['fname'];
			//echo"fname: $fname";
			$ftype=$_POST['ftype'];
			//$_SESSION['ftype']=$_POST['ftype'];
			for($i=0; $i < $flds; $i++){
				echo"<tr><td><input type='checkbox' value='pk' name='pk[$i]'></td>\n";
				echo"<td><input type=text name=fname[$i] value=$fname[$i]></td>\n";
				echo"<td><input type=text name=ftype[$i] value=$ftype[$i]></td>\n";		
				echo"<td>\n";		
				$ft=$ftype[$i];
				fieldformsize($ft, $i, '');		
			
				echo"</td><td><select name=nu[$i]>";
				echo"<option value='0'>\n";
				echo"<option value='yes'>Yes\n";
				echo"<option value='no'>No\n";
				echo"</select></td>\n";
                if($ft=="SET" || $ft=="ENUM"){
                    echo"<td><input type=hidden name=def[$i] value=''></TD>\n";
                }else{    
				    echo"<td><input type=text name=def[$i]></TD>\n";
                }
				echo"<td><input type=text name=others[$i]>\n";
	
				echo"</td></TR>\n";		
			}
			echo"<tr><td><input class=but type=reset name='reset' value='Reset Form'></td>\n";	
			echo"<td><input class=but type=submit name='nextb' value='Next -->'></td>\n";
	
			echo"</tr>\n";
			echo"</table>\n";
			echo"</form>\n";
		}
	}	
	
}else{

//*************** field types *****************
if(isset($_POST['flds'])){
	if(empty($_POST['flds']) || !isset($_POST['tblname'])){
		echo"<br><br>Both the Table Name and Number of Fields must be filled in.<br>\n";
		echo"<a Href=create.php>Click Here</a> to go back and fill them in.\n";
	}elseif($_POST['flds'] <= 0){
		echo"<br><br>Number of Fields must be filled in and numeric.<br>\n";
		echo"<a Href=create.php>Click Here</a> to go back and fill it in.\n";
	}else{
		$flds= $_POST['flds'];
		//$_SESSION['flds'] = $flds;
		$tblname= $_POST['tblname'];
		//$_SESSION['tblname'] = $tblname;
		echo"<h2 class=h>Create Table 2 $tblname</h2>\n";
		echo"<div class=m>\n";
		//Display the field names and input boxes
    	echo"<form action=create.php method=post>\n";
		echo"<table border=0 width='100%' align='center'>\n";
	
		echo"<input type=hidden name='tblname' value='$tblname'>\n";
		//echo"<input type=hidden name='dbname' value='$dbname'>";
		echo"<input type=hidden name='flds' value='$flds'>\n";
		//$flds = 5;
		echo"<tr><td>Field Name</td><td>Type</td></tr>\n";
		for($i=0; $i < $flds; $i++){
			echo"<tr><td><input type='text' name='fname[$i]'></td>\n";
			echo"<td>\n";
			echo"<select name='ftype[$i]'>\n";
			for($f=0; $f < count($fieldtypes); $f ++){
			echo"<option value=$fieldtypes[$f]>$fieldtypes[$f] \n";
			}
			echo"</select>\n";
			echo"</td></TR>\n";		
		}	
		echo"<tr><td><input class=but type=reset name='reset' value='Reset Form'></td>\n";
		echo"<td><input class=but type=submit name='nexta' value='Next -->'></td>\n";
	
		echo"</tr>\n";
		echo"</table>\n";
		echo"</form>\n";
		echo"</div>\n";
	}
}else{

//*************** Number of Fields *****************
	echo"<h2 class=h>Create a Table 1</h2>\n";
	echo"<div class=m>\n";
	echo"<form action=create.php method=post>\n";

	echo"<table border=0 width='60%' align='center'>\n";
	echo"<tr><td>Table Name:</td><td><input type=text name='tblname' size=25 ></td></tr>\n";
	echo"<tr><td>Number of Fields:</td><td><input type=text name='flds'size=5 ></td></tr>\n";
	echo"<tr><td><input class=but type=reset name='reset' value='Reset Form'></td>\n";
	echo"<td><input class=but type=submit name='numflds' value='Next Step -->'></td>\n";
	
	echo"</tr>\n";
	echo"</table>\n";
	echo"</form>\n";
	echo"</div>\n";

}//flds
}//nexta
}//nextb
display_foot();
?>
</body>
</html>