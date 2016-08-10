<?php 
session_start();

/********************************************************* 
** Description : Index for Dedicated Gaming Network LLC ** 
** File Name   : Index.php								** 
** Version     : 2.8									** 
**********************************************************/ 
/************************************************************************
Dedicated Gaming Network LLC Admin Panel

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
********************************************************************************/

require("reuseable.php"); 
echo"<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">\r\n";
echo"<html>\n"; 
echo"<head>\n"; 
echo"<title>Table Manager by Dedicated Gaming Network</title>\n"; 
echo"<meta name='author' content='Theodore Gaushas '>";
echo"<meta name='title' content=' Dedicated Gaming Network'>";
echo"<meta name='description' content=' Dedicated Gaming Network Admin Panel'>";
echo"<link rel='stylesheet' href='tmgrstyles.css' type='text/css'>\n"; 
echo"</head>\n"; 
echo"<body>\n"; 

//showpassingvars(); 
showlogos();  
$showall=true;
echo"<div align=center>";
echo"<h2 class=h > Dedicated Gaming Network Master Admin Control</h2>\n"; 
//******************* Session Logon ***********************
if(isset($_POST['logout'])){
	
		$_POST['dbname']="";
		session_unset();
		session_destroy();
}
if(isset($_POST['userid']) && isset($_POST['pword'])){
	$_SESSION['user'] = $_POST['userid'];
	$_SESSION['password'] = $_POST['pword'];
}

if (!isset($_SESSION['user']) || !isset($_SESSION['password'])){
	echo"<div align=center>";
	echo"<h2>Login to connect Admin Control</h2>\n";
	If(!isset($dbnamearray)){
		$dbnamearray="";
	}
	show_login($dbnamearray);
	echo"</div>";
}else{
	//show logout option.
	echo"<div align=right>";
	endsess();
	echo"</div>";
}
//*****dbname
if(isset($_POST['dbname'])){
	$dbname=$_POST['dbname'];
    $_SESSION['dbname']= $_POST['dbname'];
}
//***** Host
if(isset($_POST['host'])){
    $host=$_POST['host'];
    $_SESSION['host']=$_POST['host'];
}
//******set tablename
if(isset($_GET['tablename']) ){
	$tablename=$_GET['tablename'];
}elseif(isset($_POST['tablename'])){
	$tablename=$_POST['tablename'];
}
//********** pagemax
if(isset($_POST['pagemax'])){ //&& is_int($_POST['pagemax'])){
    $isnum=true;
    for($o=0; $o<count($_POST['pagemax']); $o++){
            if($_POST['pagemax'][$o]>9){
                $isnum=false;
            }
    }
    if($_POST['pagemax']>0 && $isnum){    
        $_SESSION['pagemax']=$_POST['pagemax'];
    }
}
 if(isset($_SESSION['pagemax'])){
    $pagemax=$_SESSION['pagemax'];
 }
//******** create a new Database ************
if(isset($_POST['cndb'])){
    connectmysql();
	$sql="create database $_POST[ndbname]";
	$result=exequery($sql, " ", $_POST['ndbname']); 
	if ($result){
		$_SESSION['dbname'] = $_POST['ndbname'];
        $sql="Use $_POST[ndbname]";
	    $result=exequery($sql, " ", $_POST['ndbname']); 
        if($result){
            echo"<h2>New Database $_SESSION[dbname] </h2>\n";
        }
	}
}

//*********************************************
if (! isset($_SESSION['dbname']) && ! isset($dbnamearray) && ! isset($_POST['dbname']) && isset($_SESSION['user'])){ //*********post
	//Databse names 
	showdb();
} 
//************************ Choose DB *************
if(isset($_POST['dbname']) && $_POST['dbname']==""){
    showdb(); 
}

//**********
if (isset($_SESSION['dbname']) || isset($_POST['dbna']) || isset($_POST['dbname'])){  
//************************************* 
		//connection 
		
		if (isset($_SESSION['dbname'])){
			$dbsetname = $_SESSION['dbname'];
		}elseif(isset($_POST['dbname'])){
			$dbsetname = $_POST['dbname'];
			$_SESSION['dbname'] = $_POST['dbname'];
		}else{
			$dbsetname = $_POST['dbna'];
			$_SESSION['dbname'] = $_POST['dbna'];
		}
} 
//*************************** we have a DB set       
if(isset($dbsetname) && $dbsetname!=""){
		    $link= connectmysql();
            //echo"DBS: $dbsetname";
		    $conn = connectdb($dbsetname, $link); 
 	
//*********** Drop Table ************** 
	if(isset($_POST['deltable'])){ 
        $showall=false;
		$tablename=$_POST['tablename'];
		echo"<h1>!!! Warning !!! <br>You are about to drop table $tablename<br>"; 
		echo"Are you sure you want to proceed?</h1>\n"; 
		$va="Drop $tablename"; 
		goto($tablename, $dbname,'index.php', 'del', 'droptab', $va ); 
	} 
	if(isset($_POST['droptab'])){  
		$tablename=$_POST['tablename'];
		$dsql = "drop table $tablename"; 
		$result=exequery($dsql, $tablename, $dbname); 
		unset($tablename); //="false"; 
		unset($_POST['tablename']);
	} 
//*****************Write Your Own Query ***************** 
	if(isset($_POST['wyoq'])){  //post
		$value="Table Manager Start"; 
		goto($tablename, $dbname, 'index.php', 'but', 'start', $value ); 
		echo"<form method='post'>\n";
		echo"<input type='hidden' name='dbname' value=$dbname>\n";
		//echo"<input type=text name='wyqota' width='500px' style='overflow-x:visible;'>\n";
		
		echo"<textarea name='wyoqta' cols='60' rows='5' style='overflow-y:visible'></textarea>\n";
		
		echo"<br><input class=but type=submit name='runquery' value='Execute Query'>\n"; 
		echo"</form><br>\n"; 
	} 
 
	if(isset($_POST['runquery'])){ 
		$wyoqta = StripSlashes($_POST['wyoqta']); 
		$result=exequery($wyoqta, " ", " "); 
	 
		if(@mysql_num_rows($result) >0){ 
	 		$numrows=mysql_num_rows($result); 
			$flds=mysql_num_fields($result); 
			echo"<table>";	 
			for($r=0; $r < $numrows; $r++){ 
				echo"<tr>"; 
				$row=mysql_fetch_array($result); 
				for($col = 0; $col < $flds; $col ++){ 
					$nslash = StripSlashes($row[$col]); 
					echo"<td>$nslash</td>";				 
				} 
				echo"</tr>"; 
			} 
			echo"</table>";			 
		}elseif (mysql_affected_rows()){ 
			echo" Number of Rows affected: ".mysql_affected_rows();	 
		}else{ 
			echo" Nothing returned from the query."; 
		} 
	} 
// ****************List Tables*************************** 
	
	if( ! isset($tablename) || $tablename==" " ){ 
		$dbname=$_SESSION['dbname'];
		$result = mysql_list_tables($_SESSION['dbname']); 
 		$numtab = mysql_num_rows ($result); 
 		if($numtab == 1){ 
			$_SESSION['tablename'] =mysql_tablename($result, 0);	 
 		}       
     
//***************** Buttons ****************************** 
		if (isset($_POST['runquery'])){  
			$dbname=$_SESSION['dbname'];
			$value="$dbname Start"; //Table Manager Start
			goto("", $_SESSION['dbname'], 'index.php', 'but', 'tablestart', $value ); 
	 
		}elseif (! isset($_POST['wyoq']) && ! isset($_POST['runquery'])){ //write your own query. 
			echo"<table width=40% border=0 align='left' >\n"; 
			echo"<tr><td>";	
			 
			$va="Create New Table"; 
			goto("", $_SESSION['dbname'], "create.php", 'but', 'create', $va ); 
          //  echo"<a href=create.php class='crt'>Create new Table</a>\n";
			echo"</td><td>";
			
        $value="Table Manager Start"; //Choose DB
		goto("", "", 'index.php', 'but', 'db', $value ); 
		echo"</td>\n"; 
            
			$value="Write Your Own Query"; 
			goto(" ", $_SESSION['dbname'], 'index.php', 'but', 'wyoq', $value ); 
			
			echo"</td></tr>"; 
			echo"</table><br><br><br><br><div style='clear:both;'></div>";	 
	 
			echo"<table width=100% border=0 align='center' >\n"; 
			for ($i =0; $i < $numtab; $i++) { 
	 
				$tb_names[$i] = mysql_tablename($result, $i);	 
				echo"<tr class='frow'><td align='center'>\n"; 
	 
				$va="View * $tb_names[$i]"; 
				goto($tb_names[$i], $_SESSION['dbname'],'index.php', 'but', $tb_names[$i], $va );	 
				echo"</td><td  align='center' valign='middle'>\n"; 
 
				$va="Drop Table $tb_names[$i]"; 
				goto($tb_names[$i], $_SESSION['dbname'],'index.php', 'del', 'deltable', $va ); 
				echo"</td><td  align='center' valign='middle'>\n"; 
 
				$va="Alter Table $tb_names[$i]"; 
				goto($tb_names[$i], $_SESSION['dbname'],'alter.php', 'but', 'altertable', $va ); 
				echo"</td><td align='center' valign='middle'>\n"; 
 
				searchtableform($tb_names[$i], $_SESSION['dbname']); 
				echo"</td><td>";
                //Table size in bytes
               echo mysize($_SESSION['dbname'],$tb_names[$i]);
               
                echo"</td></tr>\n";	 
			}//for 
			echo"</table>\n";		 
		} 
 
	}else{ //tablename is set 
//***************** menu ***************************************** 
		echo"<table><tr class='frow'><td>\n"; 
		$value="$_SESSION[dbname] Start"; //Ex Table Manager Start
		goto($tablename, $_SESSION['dbname'], 'index.php', 'but', 'tablestart', $value ); 
		echo"</td>\n"; 
        
        echo"<td>\n"; 
        $value="Table Manager Start"; //Choose DB
		goto("", "", 'index.php', 'but', 'start', $value ); 
		echo"</td>\n"; 
        
        echo"<td>\n"; 
        $value="Write Your Own Query"; 
		goto(" ", $_SESSION['dbname'], 'index.php', 'but', 'wyoq', $value ); 
        echo"</td>\n"; 
        
		if (!isset($_POST['add']) && !isset($_POST['deltable']) && isset($tablename)){	 
			echo"<td>";
			//$tablename = $_POST['tablename'];		 
			$va="Add a $tablename Record"; 
			goto($tablename, $_SESSION['dbname'], 'index.php', 'but', 'add', $va ); 
			echo"</td>\n"; 
		}		 
	 
		if (!isset($_POST['deltable'])){ 
			echo"<td>\n"; 
			searchtableform($tablename, $_SESSION['dbname']); 
			echo"</td>\n"; 
		} 
		echo"</tr></table>\n";			 
		echo"<br />\n"; 
 
//**************************************************	 
               
		if(isset($_POST['addrec'])){ 
           // $showall=false;
			$result=addrecord($tablename, $_SESSION['dbname'], $_POST['array']); 
		}elseif(isset($_POST['add'])){ 
            $showall=false;
			addform($tablename, $_SESSION['dbname']); 
		}elseif(isset($_POST['delete'])){
        		//delete record has been pushed 
           // $showall=false;
			$whr=buildwhr($_POST['pk'], $_POST['pv']); 
			$sql = "delete from $tablename where $whr"; 
			$result=exequery($sql, $tablename, $_SESSION['dbname']); 
		}elseif (isset($_POST['edit'])){//Edit 
            $showall=false;
			$whr = buildwhr( $_POST['pk'], $_POST['pv']); 
			//$tablename = $_SESSION['tablename'];
			$sql= "Select * from $tablename where $whr"; 
	 
			$result=exequery($sql, $tablename, $_SESSION['dbname']); 
			editform($tablename, $_SESSION['dbname'], $result, 'edit', $_POST['pk'], $_POST['pv']); 
		}elseif(isset($_POST['editrec'])){ 
           // $showall=false;
			$result=editrec($_SESSION['dbname'],$tablename, $_POST['pk'], $_POST['pv'], $_POST['array']); 
		} 
//**************** Search ************************************ 
		if(isset($_POST['searchval'])){
			$searchval=$_POST['searchval'];
		}elseif(isset($_GET['searchval'])){
			$searchval=$_GET['searchval'];
		}else{
			$searchval="";
		}
		
		if (isset($_GET['tablename'])){ 
			$tablename = $_GET['tablename'];
		}
		
		if((isset($_POST['search'])|| isset($searchval)) && $searchval !=""){ 
			$result=searcht($tablename, $_SESSION['dbname'],  $searchval); 
		}else{	
			//Display All 
			$query = "select * from $tablename"; 
			$result=exequery($query, $tablename, $_SESSION['dbname']);			 
		} 
 
//***************** Display record count ***************************************** 
        if($showall){
            $num_rows = mysql_num_rows($result); 
            //Workout whick page to display
		    if(!isset($_GET['pg']) && !isset($pg)){ 
			    $beg=0; 
                $pg=0;
		    }else{
                if(isset($_GET['pback'])){
                    $pg=$_GET['pg'];
                }else{
                    $pg=$_GET['pg'];
                }			    
                 if($pg < 0 ){
                    $pg=0;
                }
                if($pg > $num_rows/$pagemax){
                    $pg=ceil($num_rows/$pagemax)-1;
                }
                $beg = $pg * $pagemax;
		    	
		    }
		    if (!isset($_POST['add'])){
			    $pscrol=" "; 
			    $pagescrol =" "; 
               
			    $pagescrol = whichpage($num_rows, $pagemax, $pg, $tablename, $searchval);
			
			    echo "$pagescrol\n"; //Display next Top page menu 
 
			    $flds = mysql_num_fields($result); 
			    echo"<table border=0 width='100%'>\n"; 
			    echo"<tr class=head><td></td><td></td>\n"; 
			    $fields = mysql_list_fields( $_SESSION['dbname'], $tablename); 
 
			    $z=0; 
			    $x =0; 
			    $pkfield=array(); 
 
//*************Display each of the field names.*************************** 
			    for ($i = 0; $i < $flds; $i++) {			 
 		   		    echo "<td>".mysql_field_name($fields, $i)."</td>\n"; 
 
				    //Find the primary key 
				    $flagstring = mysql_field_flags ($result, $i); 
				    if(eregi("primary",$flagstring )){ 
					    $pk[$z] = $i; 
									 
					    $pkfield[$z]= mysql_field_name($fields, $i); 
					    $z++; 
				    } 
			    } 
			    echo"</tr>\n";		 
			    $tbl=$tablename; 
			    //if(isset($pk)){ 
			    if($z > 0){ 
				    $cpk=count($pk); 
			    }else{ 
			    	$cpk=0; 
			    } 
 
//************Display each row from the table.******************************** 
			 
			    for ($s=$beg; $s < $beg + $pagemax; $s++){	 
				    if($s < $num_rows){ 
					    if (!mysql_data_seek ($result, $s)) { 
            		    	echo "Cannot seek to row $s\n"; 
            		    	continue; 
        			    } 
					    $row=mysql_fetch_array($result); 
					    if(!isset($pk)){ 
					    	$pk=" "; 
					    	$pkfield= array(); 
					    } 
					    displayrow($_SESSION['dbname'], $tbl, $pk, $pkfield, $cpk, $row, $flds); 
				    }			 
			    }						 
		    }
		    echo"</table>\n"; 
		    if (!isset($_POST['add']) && !isset($_POST['edit']) && !isset($_POST['deltable']) && !isset($_POST['droptab']) && !isset($_POST['wyoq']) && $tablename){ 
			    echo"<br>"; 
			    echo "$pagescrol\n"; //Display bottom next page menu 
		    }	 
		    echo"<br><br>\n"; 
		 }//showall
		 if(isset($_POST['tablename'])){ 
			 echo"<table border=0>"; 
		     echo"<tr><td>"; 
			 $tablename=$_POST['tablename'];
			 $va="Alter Table $tablename"; 
			 goto( $tablename,  $_SESSION['dbname'],'alter.php', 'but', 'altertable', $va ); 
			 echo"</td></tr>\n"; 
			 echo"</table>\n"; 
		} 
	} 
} 
display_foot();
?> 
</body> 
</html>