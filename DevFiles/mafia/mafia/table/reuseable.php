<?php 

$GLOBALS['version']="7.5";
//********************* Variables ************************************
$pagemax=200; // Maximum rows displaed per page, change to display more or less rows per page. 

//********************************************************************
/* *******************************************************************
** If you use more than one server you can uncomment the array below **
** and add to the host addresses. This will give you a drop down box **
** with the host values to select from when loggin on.               **
** Make sure you then comment out the variable below the array.      **
** *******************************************************************/
function show_login($dbnamearray){
     
    //$hostdefault=array("localhost", "127.0.0.1");
      $hostdefault="localhost";
		echo"<table>"; 
		echo"<form  name='showlogin' method='post' action='index.php'>"; 
        if(count($hostdefault) > 1){
            echo"<tr><td>Host:</td><td><select name=host>";
            for($x=0; $x < count($hostdefault);$x++){
                echo"<option value=$hostdefault[$x]>$hostdefault[$x]";
            }
            echo"</select></td></tr>\n";
        }else{
            echo"<tr><td>Host:</td><td><input type=text name='host' size=15 value=$hostdefault /></td></tr>\n"; 
		}
        echo"<tr><td>User Name:</td><td><input type=text name='userid' value=YourLoginUsername size=20 /></td></tr>\n"; 
		echo"<tr><td>Password:</td><td><input type=password name='pword' value=passhere size=20 /></td></tr>\n"; 
		
		If($dbnamearray != ""){
			echo"<tr><td>Database:</td><td><select name='dbna'>\n";
			for ($i =0; $i < count($dbnamearray); $i++) { 
				$dbn=$dbnamearray[$i]; 
				echo"<option value=$dbn>$dbn";
			} 
		}
		echo"<tr><td><input class=ser type='submit' name='login' value='Login' /></td>\n"; 
		echo"<td><input class=ser type=reset name='reset' value='Clear' /></td></tr>\n"; 
		echo"</form></table>\n";	 
		
} 
/* *******************************************************************
** This function limits the Databases displayed to each user.       **
** To add a user add another case statement where the case is the   **
** user name and the array is an array of database names the user   **
** can access.                                                      **
** ******************************************************************/
function dbrestrict(){
if(isset($_SESSION['user'])){
    $user=$_SESSION['user'];
   
    switch($user){
                   
    //Edit these ** values. You can add more case statements.
        case '**User**':
            $dbnamearray= array('**dbname**', '**dbname2**', '**dbname**');
            break;
     //end edit values  
      
        default:
            $_SESSION['defaltuser']=true;
            $dbnamearray = array();
            $link = connectmysql();
           
            $db_list = mysql_list_dbs($link); //$db_list 
		    $cnt = mysql_num_rows($db_list);	 
		    for ($i =0; $i < $cnt; $i++) { 
			    $dbnamearray[$i]= mysql_db_name($db_list, $i);     
		    }
    }
    return $dbnamearray;       
}
}
//***************************************************************
//function showdbs($dbnamearray, $backuppath){
function showdbs($dbnamearray){
    //$backuppath=addslashes($backuppath);
       echo"<table>\n";
       for ($i =0; $i < count($dbnamearray); $i++) { 
		    echo"<tr><td>";
            $dbn=$dbnamearray[$i];
			$va="Goto DB $dbn";            
			goto(' ', $dbn,'index.php', 'but', 'db', $va ); 
           
            $dbs=mysize($dbnamearray[$i],"");
            echo"</td><td>$dbs</td></tr>\n";
	}
    echo"</table>\n";
}


//********************* Show Logout Button **********
function endsess(){
echo"<form method='post' name='endsess' action='index.php'>\n"; 
echo"<input class=ser type='submit' name='logout' value='logout' />\n"; 
echo"</form>";
}

//******************************************************************** 
function connectmysql(){ 
	//Connects to the MySQL Database. 
	
	
	if (isset($_SESSION['user']) && isset($_SESSION['password'])){
	 	$user = $_SESSION['user'];
	 	$pass = $_SESSION['password'];
	}else{
        display_foot(); 
        echo"\n</body>\n</html>"; 
		exit();
	}
	$link = @mysql_connect($_SESSION['host'], $_SESSION['user'], $_SESSION['password']); 
	if(! $link){ 
		echo"<div class='error'>\n";
		echo"Unable to connect to the database server. <BR>"; 
		echo"The Host: $_SESSION[host], Username: $user or the password may be incorrect. <br>";
		echo"Please Logout to try again.\n";
		echo"</div>\n";
		
        return false;
		exit();
	} else{
		return $link;
	}
	
}
//*********************************************************************	
function connectdb($db, $link){ 	      
	if(! mysql_select_db($db,$link)){ 
		echo"Unable to locate database $db.<br> Please try again later.\n";	
		exit(); 
	} 
} 
//********************************************************************* 
function exequery($sql, $tablename, $db){ 
	$result= @mysql_query( $sql ); 
	if($result){ 
		//echo "Query successful";		 
		return $result; 
	}else{		 
		echo"Sorry your Query failed: $sql <br> error:".mysql_error()."\n"; 
		return false; 
	}	 
} 
 
//***************************************************************************** 
function showlogos(){ 
	echo"<div align=center >"; 
	
echo"</div>"; 
	 
}
//*************************************************** 
$fieldtypes = array("BIGINT", "BLOB", "CHAR", "DATE", "DATETIME", "DECIMAL", "DOUBLE", "ENUM", "FLOAT",  
  "INT", "INTEGER", "LONGBLOB", "LONGTEXT", "MEDIUMBLOB", "MEDIUMINT", "MEDIUMTEXT", "NUMERIC", "PRECISION",  
 "REAL","SET", "SMALLINT", "TEXT", "TIME", "TIMESTAMP", "TINYBLOB", "TINYINT", "TINYTEXT", "VARCHAR", "YEAR" ); 
  	 
		      		     
//****************** Search Form **************************** 
function searchtableform($tablename, $dbname){ 
	echo"<form method='post' action='index.php'>\n"; 
	echo"<input type=hidden name='dbname' value='$dbname' />\n"; 
	echo"<input type=hidden name='tablename' value='$tablename' />\n"; 
	echo"<input type=text name='searchval' />\n"; 
	echo"<input class=ser type=submit name='search' value='Search $tablename' />\n"; 
	echo"</form>\n";	 
} 
//********************* Search ************************* 
function searcht($tablename, $dbname, $searchval){ 
	if(! empty($searchval)){ 
		//	$searchval= str_replace(";",' ', $searchval); 
        $result=exequery("Select * from $tablename", $tablename, $dbname);
		//$result=mysql_query("Select * from $tablename"); 
		$num = mysql_num_fields($result); 
		$fields = mysql_list_fields($dbname, $tablename);		 
		$whr="where "; 
		$tok=explode(" ",$searchval); 
		for ($t =0; $t < count($tok); $t++){					 
			for ( $c = 0; $c < $num; $c++){ 
				$fn =mysql_field_name($fields, $c);				 
				$whr .=" $fn like '%$tok[$t]%' or ";													 
			} 
		} 
		$whr=trim(substr_replace($whr, " ", -3));		 
		$query="Select * from $tablename $whr"; 
		$result=exequery($query, $tablename, $dbname); 
		return $result;	 
	}		 
 
} 
//*********************GOTO buttons************************* 
//provides a form and button. 
 
function goto($tablename, $dbname, $action, $class, $name, $va ){ 
	//Adds a button. 
    
	echo"<form action=$action method='post' >\n"; 

		if(! eregi('tablestart', $name)){ 
			echo"<input type=hidden name=dbname value='$dbname' />\n";  
			echo"<input type=hidden name=tablename value='$tablename' />\n"; 
		} 
		echo"<input class=$class type=submit  value='$va' name='$name' />\n"; 
		//echo"<input class=$class type=submit  value='$action' name=$name>";
	echo"</form>\n"; 
	
	//echo"<a class=$class href=$action>$va</a>";
	//}
} 

//*********************** ShowDB ***********************************
function showdb(){
//function showdb($backuppath){
   	
	$link=connectmysql();
	if ($link){    
        echo"<div class='db'>"; 
		echo"<div class='cream'>\n";
		echo"<h2 class=h >Create a New Database</h2>\n"; 
        
		echo"<form name=cdb action='index.php' method='post' >\n";
		echo"New Database Name: <input type=text name=ndbname />\n";
		echo"<br /><br /><input class=but type='submit' name='cndb' value='Create new Database' />\n";
		echo"</form><br />";
		echo"</div>";
		echo"<h2 class=h >Choose your Database</h2>\n"; 
		//Restrict the database for users    
        $dbnamearray= dbrestrict();
        showdbs($dbnamearray);
        echo"</div>"; 
   	} 
	
}

//********************** BuildWhr ****************************** 
//Builds the Where part of queries. 
 
function buildwhr($pk, $pv){ 
	$whr=""; 
	$pn =count($pv); 
	for($t =0; $t < $pn; $t++){		 
		$whr.="$pk[$t]='$pv[$t]'"; 
		if($t < $pn-1){ 
			$whr.=" and "; 
		} 
	} 
	if ($whr !=" "){ 
		return $whr; 
	}else{ 
		return false; 
	} 
} 
//***********************ADD Record ****************** 
 
function addrecord($tablename, $dbname, $array){ 
     $result=exequery("Select * from $tablename", $tablename, $dbname);
	//$result = @mysql_query( "Select * from $tablename" ); 
	 
	$flds = mysql_num_fields($result); 
	//$fields = mysql_list_fields($dbname, $tablename); 
   	$qry=" "; 
    $query = "Insert into $tablename Values( "; 
	for ($x =0; $x < $flds; $x++){ 
        //Multiple Select values for SET
     
       if(is_array($array[$x])){
            $mval="";
            for($m=0; $m < count($array[$x]); $m++){
                if($m+1 == count($array[$x])){
                    $mval.= AddSlashes($array[$x][$m]); 
               
                }else{
                    $mval.= AddSlashes($array[$x][$m]).","; 
                }
                $fval = $mval;
            }
        }else{
		    $fval = AddSlashes($array[$x]); 
        }
		$qry .= "'$fval'"; 
		if ($x < $flds-1){ 
			$qry.= ", "; 
		} 
	} 
	$query .= $qry.")"; 
   // echo"qry: $qry";
	$result=exequery($query, $tablename, $dbname); 
	if($result){ 
		return $result; 
	}else{ 
		return false; 
	}	 
} 
 
//**********************ADD Form **********************

function addform($tablename, $dbname){
 //Display the field names and input boxes
 echo"<form action='index.php' method='post'>\n";
 echo"<table border=0 width='100%' align='center'>\n";
 echo"<tr class=head><td>Field Name</td><td>Type</td><td>Value</td></tr>\n";
  $result=exequery("Select * from $tablename", $tablename, $dbname);
 //$result = @mysql_query( "Select * from $tablename" );
 $flds = mysql_num_fields($result);
 $fields = mysql_list_fields($dbname, $tablename);
 echo"<input type=hidden name=tablename value='$tablename' />\n";
 echo"<input type=hidden name='dbname' value='$dbname' />\n";
 echo"<tr>\n";
  
 $mxlen = 80;//max width of the form fields.
 for($i=0; $i < $flds; $i++){
      $auto = "false";
      echo "<th>".mysql_field_name($fields, $i);
      $fieldname = mysql_field_name($fields, $i);  // added
      $type  = mysql_field_type($result, $i);
      $flen = mysql_field_len($result, $i);//length of the field  
      $flagstring = mysql_field_flags ($result, $i);
    // Start of new code for set drop down
      $newsql = "show columns from $tablename like '%".$fieldname."'";
      $newresult = exequery($newsql, $tablename, $dbname); 
      //mysql_query($newsql) or die ('I cannot get the query because: ' . mysql_error());
      $arr=mysql_fetch_array($newresult);
    // End of new code block for set drop down
      if (eregi("primary",$flagstring )){
       $type .= " PK ";
      }
      if(eregi("auto",$flagstring )){
       $type .= " auto_increment";
       $auto = "true";
      }
      if ($auto=="true"){
        echo"<td>$type</td><td><input type=text name='array[$i]' size='$flen' value=0 /></td></tr>\n";
      }elseif($flen > $mxlen){
        $rws= $flen/$mxlen;
        if($rws>10){
             $rws=10; //max length of textarea
        }
        echo"<td>$type</td><td><textarea name='array[$i]' rows=$rws cols=$mxlen></textarea></td></tr>\n";
        // Start of new code for set drop down
      }elseif (strncmp($arr[1],'set',3)==0 || strncmp($arr[1],'enum',4)==0){  // We have a field type of set or enum
       $num=substr_count($arr[1],',') + 1;  // count the number of entries
       $pos=strpos($arr[1],'(' ); //find the position of '('
       $newstring=substr($arr[1],$pos+1);  // get rid of the '???('
       $snewstring=str_replace(')','',$newstring); // get rid of the last ')'
       $nnewstring=explode(',',$snewstring,$num); // stick into an array
       if(strncmp($arr[1],'set',3)==0 ){//Sets can have combinations of values
           echo "<td>Set (select one or more)</td>";
           echo"<td><select name='array[$i][]' size='3' multiple>";
       }else{//Enum one value only
        echo "<td>Enum</td>";
           echo"<td><select name='array[$i]'>";
       }
       for($y=0; $y<$num;$y++){
       echo"<option value=$nnewstring[$y]>$nnewstring[$y]";
       }
        echo"</select></td></tr>\n";
    // End of new code block for set drop down
      }else{      
       echo"<td>$type</td><td><input type=text name='array[$i]' size='$flen' /></td></tr>\n";
      }
 }  
 echo"<tr><td><input class=but type=submit name='addrec' value='Add Record' /></td>\n";
 echo"<td><input class=but type=reset name='reset' value='Reset Form' /></td>\n";
 echo"</tr>";
 echo"</table>\n";
 echo"</form>\n";
}


//*********************Edit Form *************** 
function editform($tablename, $dbname, $result, $edit, $pk, $pv){ 
	$row=mysql_fetch_array($result); 
	echo"<form action='index.php'  method=post>\n"; 
	echo"<table border=0 width ='100%' align='center'>\n"; 
	 
	$flds = mysql_num_fields($result); 
	$fields = mysql_list_fields($dbname, $tablename); 
	echo"<input type=hidden name=tablename value='$tablename' />\n"; 
 
	echo"<input type=hidden name='dbname' value='$dbname' />\n";	 
	echo"<tr>"; 
	$mxlen = 80;//max width of the form fields 
	for($i=0; $i < $flds; $i++){ 
        $fname=mysql_field_name($fields, $i);
		echo "<th>$fname"; 
	 	$flen = mysql_field_len($result, $i);//length of the field 
		$nslash = StripSlashes($row[$i]);		 
        // Start of new code for set drop down
      $newsql = "show columns from $tablename like '%".$fname."'";
      $newresult = exequery($newsql, $tablename, $dbname); 
      $arr=mysql_fetch_array($newresult);
    // End of new code block for set drop down
        
		if($flen > $mxlen){ 
			$rws= $flen/$mxlen; 
				if($rws>10){ 
				$rws=10; //max length of textarea 
			} 
			echo"<td><textarea name='array[$i]' rows=$rws cols=$mxlen>$nslash</textarea></td></tr>\n"; 
// Start of new code for set drop down
          }elseif (strncmp($arr[1],'set',3)==0 || strncmp($arr[1],'enum',4)==0){  // We have a field type of set or enum
           $num=substr_count($arr[1],',') + 1;  // count the number of entries
           $pos=strpos($arr[1],'(' ); //find the position of '('
           $newstring=substr($arr[1],$pos+1);  // get rid of the '???('
           $snewstring=str_replace(')','',$newstring); // get rid of the last ')'
           $nnewstring=explode(',',$snewstring,$num); // stick into an array
           if(strncmp($arr[1],'set',3)==0 ){//Sets can have combinations of values
               echo"<td><select name='array[$i][]' multiple size='3'>";
           }else{//Enum one value only
               echo"<td><select name='array[$i]'>";
           }
           $nsel=explode(",",$nslash);
          for($y=0; $y<$num;$y++){
                //geteach value 'a,b,c'   
                $sel="";
                for($e=0; $e<count($nsel);$e++){        
                    if($nnewstring[$y]=="'".$nsel[$e]."'"){
                        $sel="selected";
                    }
                }
                echo"<option value=$nnewstring[$y] $sel>$nnewstring[$y]";
           }
            echo"</select></td></tr>\n";
// End of new code block for set drop down
        
        
        }else{    		 
			echo"<td><input type=text name='array[$i]' size='$flen' value='$nslash' /></td></tr>\n"; 
		} 
		for($f =0; $f< count($pk);$f++){			 
			echo"<input type=hidden name=pk[$f] value='$pk[$f]' />"; 
			echo"<input type=hidden name=pv[$f] value='$pv[$f]' />\n"; 
		} 
	} 
	echo"<tr><td><input class=but type=submit name='editrec' value='Update' /></td>\n"; 
	echo"<td><input class=but type=reset name='reset' value='Reset Form' /></td>\n"; 
	echo"</tr>"; 
	echo"</table>\n"; 
	echo"</form>\n"; 
} 
//************************Edit Record************************* 
function editrec($dbname, $tablename, $pk, $pv, $array){ 
 
	//$result = @mysql_query( "Select * from $tablename" );	
    $result = exequery("Select * from $tablename", $tablename, $dbname); 
	$flds = mysql_num_fields($result); 
	$fields = mysql_list_fields($dbname, $tablename); 
 
//Build Query 
   	$qry=""; 
    $query = "UPDATE $tablename set "; 
	for ($x =0; $x < $flds; $x++){ 
		$fie = mysql_field_name($fields, $x ); 
        // SET and ENUM
         if(is_array($array[$x])){
            $mval="";
            for($m=0; $m < count($array[$x]); $m++){
                if($m+1 == count($array[$x])){
                    $mval.= AddSlashes($array[$x][$m]);               
                }else{
                    $mval.= AddSlashes($array[$x][$m]).","; 
                }
                $fval = $mval;
            }
        }else{
		    $fval = AddSlashes($array[$x]); 
        }
        //**************************     
		//$fval = AddSlashes($array[$x]); 
		$qry .= "$fie = '$fval'"; 
		if ($x < $flds-1){ 
			$qry.= ", "; 
		} 
	} 
	$whr = buildwhr( $pk, $pv); 
	$whr =StripSlashes($whr); 
	$query .= "$qry"; 
	$query .= " where $whr"; 
 
    $result=exequery($query, $tablename, $dbname); 
	if($result){ 
		return $result; 
	}else{ 
		return false; 
	} 
} 
//****************** Number of Primary Keys *********************** 
function numpk($result){ 
	$z =0; 
	for ($i = 0; $i < $flds; $i++) {			 		 	 
		//Find the primary key 
		$flagstring = mysql_field_flags ($result, $i); 
		if(eregi("primary",$flagstring )){ 
			$z++; 
		} 
	} 
	return $z; 
} 
//********************Size field***************** 
function fieldformsize($ft, $i, $l){ 
	$ft= trim(strtoupper($ft)); 
	if($ft =="DATE" || $ft=="TIME" || $ft== "DATETIME" ){			 
	}elseif( $ft=="TINYTEXT" || $ft=="BLOB" || $ft=="TEXT" || $ft =="MEDIUMBLOB"){	 
		echo"<input type=hidden name='leng[$i]' value=$l>"; 
	}elseif($ft=="MEDIUMTEXT" || $ft=="LONGBLOB"|| $ft=="LONGTEXT" || $ft=="TINYBLOB"){ 
		echo"<input type=hidden name='leng[$i]' value=$l>";				 
	}elseif($ft=="INT" || $ft=="TINYINT"|| $ft=="SMALLINT"|| $ft=="MEDIUMINT"|| $ft=="BIGINT" || $ft=="INTEGER"){ 
		echo"<input type=text name='leng[$i]' size=5  value=$l>"; 			 
	}elseif($ft=="YEAR" ){ 
		echo"<select name='leng[$i]'>"; 
		echo"<option value='4'>4"; 
		echo"<option value='2'>2"; 
		echo"</select>\n";	
    }elseif($ft=="SET"|| $ft=="ENUM"){
        echo"<input type=text name='leng[$i]' title='values eg \"a\", \"b\", \"c\"' value='' />"; 			 
	}else{		 
		echo"<input type=text name='leng[$i]' size=5 value=$l />\n";				 
	} 
} 
 
//******************************Display Row ****************************** 
function displayrow($dbname, $tbl, $pk, $pkfield, $cpk, $row, $flds){ 
	$pkfs=""; 
	$hv=""; 
	$hf=""; 
 
	if($cpk >0 && !empty($pkfield)){ 
		for($a = 0; $a < $cpk; $a++){ 
			$fieldn = $pkfield[$a];			 
			$hf .= "<input type=hidden name=pk[$a] value='$pkfield[$a]' />"; 
			$hv .= "<input type=hidden name=pv[$a] value='$row[$fieldn]' />"; 
		} 
	}else{ //No Primary Key so use all fields 
		$fields = mysql_list_fields($dbname, $tbl); 
		for($b = 0; $b < $flds; $b++){ 
			$fie = mysql_field_name($fields, $b );	 
			$hf .= "<input type=hidden name=pk[$b] value='$fie' />"; 
			$hv .= "<input type=hidden name=pv[$b] value='$row[$b]' />";	 
		} 
	}					 
	echo"<tr>\n"; 
	//edit Record 
	echo"<td><form action='index.php' method=post>\n"; 
	echo"<input type=hidden name=dbname value='$dbname' />\n"; 
	echo"<input type=hidden name=tablename value='$tbl' />\n"; 
	echo"<input type=hidden name=npkeys value='$cpk' />\n"; 
	echo"$hf"; 
	echo"$hv"; 
	echo"<input class=sml type=submit name=edit value='Edit Record' />\n"; 
	echo"</form></td>\n"; 
				 
	//Delete record 
	echo"<td><form action='index.php' method=post>\n"; 
	echo"<input type=hidden name=dbname value='$dbname' />\n"; 
	echo"<input type=hidden name=tablename value='$tbl' />\n"; 
	echo"<input type=hidden name=num value='$cpk' />\n"; 
	echo"$hf"; 
	echo"$hv"; 
	echo"<input class=smldel type=submit name=delete value='Delete Record' />\n"; 
	echo"</form></td>"; 
 
	//Display all the columns.			 
	for($col = 0; $col < $flds; $col ++){ 
		$nslash = StripSlashes($row[$col]); 
		echo"<td>$nslash</td>";				 
	}			 
	echo"</tr>"; 
								 
} 
//***********************Remove Array Copy******************************** 
//removes copies from an array $x. 
 
function removearraycopy($x){	 
	$leng= count($x); 
	sort($x); 
	$farr=array(); 
	 
	for ($i =0; $i < $leng; $i++){ 
		$flag=false;	 
		for ($s =0; $s < count($farr); $s++){ 
			if($x[$i]==$farr[$s]){ 
				$flag=true; 
			} 
		} 
		if ($flag == false){ 
			$farr[count($farr)] = $x[$i];			 
		} 
	} 
	return $farr;	 
} 
//***********************<< page position >>********************************  
function whichpage($num_rows, $pagemax, $pg, $tablename, $searchval){
	$pgs = $num_rows/$pagemax; 
	$pgs=ceil($pgs);
    			//round up the number of pages. 
	echo"<form action='index.php' id='recspage' method='post' name='recspage'>\n";
    echo"Total number of records $num_rows, displayed on $pgs pages of \n";
    echo"<input type='text'  name='pagemax' value='$pagemax' size='4' onchange='javascript:this.form.submit();' title='Type the number records to display on a page then click outside the box' /> \n"; 			 
	echo"<input type='hidden' name='searchval' value='$searchval'  />\n"; 
    echo"<input type='hidden' name='tablename' value='$tablename'  />\n"; 
    echo"records per page.</form> \n";
    $pagescrol="";
    $sval="";													 
	  if($pgs >1){    
            $pagescrol="<div class='pagecount'>\n";
			$nxt=$pg+1;
            $bk=$pg-1;
            $lst=$pgs;
            $end=$lst-1;
            $showp=$pg+1;
           if($searchval !=""){
            $sval="&amp;searchval=$searchval";
           }           	
           $pagescrol .= "<form name='pages' id='pages' action='index.php' method='get'>\n"; 
            if($pg>=1){ 
                $pagescrol .= " <a href='index.php?tablename=$tablename&amp;pg=0$sval' title='To first page'> 1 :<< </a> \n"; 	
				$pagescrol .= " <a href='index.php?tablename=$tablename&amp;pg=$bk$sval' title='Back one page'> < </a> \n";               
			}		           
           $pagescrol .= "<input type='text' name='pg' value='$showp' size='4' onchange='javascript:this.form.submit();' title='Type a page number then click outside the box' />\n"; 
           $pagescrol .= "<input type='hidden' name='pback' value='true'  />\n"; 
           $pagescrol .= "<input type='hidden' name='searchval' value='$searchval'  />\n"; 
           $pagescrol .= "<input type='hidden' name='tablename' value='$tablename'  />\n"; 
           
           if($showp < $lst){ 				
                $pagescrol .= " <a href='index.php?tablename=$tablename&amp;pg=$nxt$sval' title='Next page'> > </a> \n"; 
                $pagescrol .= " <a href='index.php?tablename=$tablename&amp;pg=$end$sval' title='To Last page'> >>: $lst</a> \n"; 
           }   
           $pagescrol .= "</form>\n"; 
           $pagescrol.="</div>\n";
      }
	return $pagescrol;
} 

//*************Display Footer*************************
//Please don't remove or change.
function display_foot(){
    $version=$GLOBALS['version'];
    echo"<div class='foot'>Version $version &copy; ".date('Y')." <a style='text-decoration:none;' target='_blank' href='http://www.dedicatedgamingnetwork.com'>Dedicated Gaming Network LLC</a></div>";
   
    }
//*************My Size*************************
//Returns the size of a table or database
function mysize($dbname, $tablename){
    $like="";
    $total="";
    $t=0;
    if($tablename !=""){
        $like=" like '$tablename'";
    }
    $sql= "SHOW TABLE STATUS FROM $dbname $like";
    //$result = mysql_query($sql);
    $result=exequery($sql, $tablename, $dbname);
    if($result){
        
        while($rec = mysql_fetch_array($result)){
         $t+=($rec['Data_length'] + $rec['Index_length']);
         }
        $total ="<span class='bytes'>$t bytes</span>";
    }else{
        $total="Unknowen";
    }
    return($total);
}


//**************************************
//DEBUG to show all being passed to the page
function showpassingvars(){
	echo"Get: ";
 	foreach($_GET as $pram=>$value){
 		echo"$pram: $value, ";
 	}
	echo"<br>Post: ";
 	foreach($_POST as $pram=>$value){
  		echo"$pram: $value, ";
 	}
 	echo"<br>Session: ";
 	foreach($_SESSION as $pram=>$value){
 		echo"$pram: $value, ";
 	}
 }
//************************************************************************** 
 
?>