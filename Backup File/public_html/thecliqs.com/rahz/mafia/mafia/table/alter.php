<?php 
session_start();

 
require("reuseable.php"); 

If(isset($_POST['tablename'])){
	$tablename=$_POST['tablename'];
}
echo"<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">\r\n";
echo"<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">\r\n";
echo"<html>\n"; 
echo"<head>\n"; 
echo"<title> Dedicated Gaming Network Master Admin Panel</title>\n"; 
echo"<meta name='author' content='Theodore Gaushas '>";
echo"<meta name='title' content=' Dedicated Gaming Network Administration'>";
echo"<meta name='description' content=' Dedicated Gaming Network Admin Panel'>";
echo"<link rel='stylesheet' href='tmgrstyles.css' type='text/css'>\n"; 
echo"</head>\n"; 
echo"<body>\n"; 
 
showlogos(); 
//showpassingvars(); 
if(!isset($_SESSION['user'])){
	echo"<a href='index.php'>Login first</a>\n";
}else{
	echo"<div align=right>\n";
	endsess();
	echo"</div>\n";
}

 
 
//************** Top menu *************************** 
if	(isset($_POST['retable'])){ 
	$tname=$_POST['newtname']; 
} 

if(isset($tablename)){
	echo"<h2 class=h>Alter Table: $tablename</h2>\n"; 
	echo"<table><tr><td>\n";		 
		$va="Add a $tablename Record"; 
		goto($tablename, $_SESSION['dbname'], 'index.php', 'but', 'add', $va ); 
	echo"</td><td>\n";		 
		$value="Table Manager Start"; 
		goto("", "", 'index.php', 'but', 'start', $value ); 
    echo"</td><td>\n";		    
        $value="$_SESSION[dbname] Start"; //Table Manager Start
		goto($tablename, $_SESSION['dbname'], 'index.php', 'but', 'tablestart', $value );    
	echo"</td><td>\n";		 
		$value="View * $tablename"; 
		goto($tablename, $_SESSION['dbname'], 'index.php', 'but', 'view', $value );      
	echo"</td></tr></table>\n";			 
	echo"<br><br>\n"; 
 }
//********** connect to the database.************** 
 
$link=connectmysql();
$conn = connectdb($_SESSION['dbname'], $link); 
 
//get a result set if none exist. 
if(!isset($result)){ 
	//$result = @mysql_query( "Select * from $tablename" ); 
     $result=exequery("Select * from $tablename", $tablename, $dbname);
} 
 
//*************** Rename Table ***************** 
if(isset($_POST['rename'])){ 
	echo"<form action=alter.php method=post>\n"; 
	
	echo"<input type=hidden name='tablename' value='$tablename'>\n"; 
	echo"<input type=text name='newtname' value='$tablename'>\n"; 
	echo"<input class=but type=submit name='retable' value='Rename Table'>\n"; 
} 
if(isset($_POST['retable'])){ 
	$tablename=$_POST['tablename'];
	$dbname=$_SESSION['dbname'];
	$newtname=$_POST['newtname'];
	$query="ALTER table $tablename RENAME as $newtname"; 
	
	
	$result=exequery($query, $newtname, $dbname); 
	if($result){ 
		$tablename= $newtname;
		echo"Table Renamed successfully to $tablename. <br><br>\n"; 
	}	 
	$va="Alter Table $tablename"; 
	goto($tablename, $dbname,'alter.php', 'but', 'altertable', $va ); 
} 
 
//*************** Add Index Form ***************** 
if(isset($_POST['addind'])){ 

	$dbname=$_SESSION['dbname'];

	$num =  mysql_num_fields($result); 
	$fields = mysql_list_fields($dbname, $tablename); 
	echo"<form action=alter.php method=post>\n"; 
	 
	echo"<input type=hidden name='num' value='$num'>\n"; 
	echo"<input type=hidden name='tablename' value='$tablename'>\n"; 
	for ( $c = 0; $c < $num; $c++){ 
		$fn =mysql_field_name($fields, $c); 
		$flagstring = mysql_field_flags ($result, $c);		 
		echo"<input type=checkbox name=idx[$c] value=idx[$c]>$fn<br>\n";						 
		echo"<input type=hidden name=col[$c] value='$fn'>\n";		 
	} 
	echo"Index Name: <input type=text name='idxname'>\n"; 
	echo"<input class=but type=submit name='addidx' value='Add Index'>\n"; 
	echo"</form>"; 
} 
 
$r=0; 
$nks=" "; 
 
//***************** Add Index ************************8 
if(isset($_POST['addidx'])){ 
	
	$dbname=$_SESSION['dbname'];
	$idxname=  $_POST['idxname'];
	$idx=$_POST['idx'];
	$col=$_POST['col'];
	$num=$_POST['num'];
	for ( $c = 0; $c < $num; $c++){ 
		if(isset($idx[$c])){ 
			$r ++; 
			if($r >1){ 
				$nks .= ", $col[$c]"; 
			}else{ 
				$nks .= " $col[$c]"; 
			} 
		}		 
	} 
	$query="ALTER table $tablename ADD index $idxname ($nks)"; 
	$result=exequery($query, $tablename, $dbname); 
	if($result){ 
		echo"Index $idxname added successfully to $tablename. <br><br>\n"; 
	} 
	$va="Alter Table $tablename"; 
	goto($tablename, $dbname,'alter.php', 'but', 'altertable', $va ); 
} 
//*************** Drop Index Form ***************** 
if(isset($_POST['dropind'])){ 

	$dbname=$_SESSION['dbname'];
	
	$num =  mysql_num_fields($result); 
	$fields = mysql_list_fields($dbname, $tablename); 
	 
	$farray = array(); 
	$query="SHOW INDEX FROM $tablename"; 
	$sts = 0; 
	$result=exequery($query, $tablename, $dbname); 
	$num_rows = mysql_num_rows($result); 
	for ($t =0; $t < $num_rows; $t++){ 
		$row= mysql_fetch_array($result); 
		$op = "$row[2]"; 
		if($op !="PRIMARY" && $op !=" "){ 
			$farray[count($farray)]=$op;	 
		} 
	} 
	if (count($farray) >0){ 
		$farray = removearraycopy($farray); 
 
		echo"<form action=alter.php method=post>\n"; 
		echo"<input type=hidden name='dbname' value='$dbname'>\n"; 
		echo"<input type=hidden name='tablename' value='$tablename'>\n"; 
		echo"Index Name:<select name=idxname>\n"; 
		for ($f=0; $f < count($farray); $f++){ 
			echo"<option value=$farray[$f]>$farray[$f]"; 
		} 
		echo"</select>"; 
 
		echo"<input class=but type=submit name='drpidx' value='Drop Index'>\n"; 
		echo"</form>\n"; 
 
	}else{ 
		echo" No Index found in table $tablename."; 
	} 
} 
 
//********************** Drop Index *************************** 
if(isset($_POST['drpidx'])){ 
	
	$dbname=$_SESSION['dbname'];
	$idxname=  $_POST['idxname'];
	$query="ALTER table $tablename DROP index $idxname"; 
	$result=exequery($query, $tablename, $dbname); 
	if($result){ 
		echo"Index $idxname successfully dropped from $tablename. <br><br>\n"; 
	} 
	$va="Alter Table $tablename"; 
	goto($tablename, $dbname,'alter.php', 'but', 'altertable', $va ); 
} 
 
//*************** Add Primary Key Form***************** 
if(isset($_POST['addpk'])){ 

	$dbname=$_SESSION['dbname'];
	$num =  mysql_num_fields($result); 
	$fields = mysql_list_fields($dbname, $tablename); 
	$foundkey="false"; 
 
	//Find any primary keys 
	for ( $c = 0; $c < $num; $c++){				 
		$flagstring = mysql_field_flags ($result, $c); 
		if(eregi("primary",$flagstring )){	 
			$foundkey ="true";	 
		}						 
	} 
	if($foundkey =="true"){ 
		echo"Existing Primary Key's will have to be dropped<br>\n";	 
		goto($tablename, $dbname,'alter.php', 'but', 'droppk', 'Drop Primary Key' ); 
	}else{// no primary keys 
 
		echo"<table border=0 width='50%' align='center'>\n"; 
		echo"<tr class=head><td>Primary Key</td><td>Field Name</td></tr>\n"; 
			 
		echo"<form action=alter.php method=post>\n"; 
		
		echo"<input type=hidden name='tablename' value='$tablename'>\n";	 
		echo"<input type=hidden name='num' value='$num'>\n"; 
		for ( $c = 0; $c < $num; $c++){ 
			$fn =mysql_field_name($fields, $c); 
			$flagstring = mysql_field_flags ($result, $c); 
			echo"<tr><td>\n"; 
			echo"<input type=checkbox name=key[$c] value=key[$c]>\n";		 
			echo"</td><td>$fn</td><tr>\n";		 
		 
			echo"<input type=hidden name=col[$c] value='$fn'>\n"; 
			echo"<input type=hidden name='flagstring[$c]' value=$flagstring>\n"; 
		 
		} 
		//echo"<input type=hidden name=pks value='$pks'>"; 
		echo"<tr><td><input class=but type=submit name=addprimary value='Make Primary Keys'></td></tr>\n"; 
		echo"</form>\n"; 
		echo"</table>\n"; 
	}//foundkey 
} 
//******************* Add Primary Key ****************** 
if(isset($_POST['addprimary'])){ 
	
	$dbname=$_SESSION['dbname'];
	$flagstring=$_POST['flagstring'];
	$col=$_POST['col'];
	$key=$_POST['key'];
	$num=$_POST['num'];
	$opkflag=" "; 
	$nflag=0; 
	$r =0; 
	$nks=" "; 
	for ( $c = 0; $c < $num; $c++){ 
		if(isset($key[$c])){ 
echo"COL: $col[$c]";
			$r ++; 
			if($r >1){ 
				$nks .= ", $col[$c]"; 
			}else{ 
				$nks .= " $col[$c]"; 
			} 
			//Make sure the chosen key field is not null. 
			if(eregi("not_null", $flagstring[$c] ) || eregi("NOT NULL", $flagstring[$c] ) ){ 
				$nflag++; 
			}	 
		}		 
		if (eregi("primary", $flagstring[$c] )){ 
			$opkflag="true";	 
		} 
	} 
	if($nflag!=$r) {	 
		echo"Primary Key Columns must be NOT NULL\n"; 
		goto($tablename, $dbname, 'alter.php', 'but', 'changec', 'Change A Column' ); 
 
	}elseif ($opkflag=="true"){//Drop old keys 
		echo"Existing Primary Key's must be Dropped\n"; 
		goto($tablename, $dbname,'alter.php', 'but', 'droppk', 'Drop Primary Key' ); 
	}else{ 
		 
		//add keys 
		$query="ALTER table $tablename ADD PRIMARY KEY ($nks)"; 
		$result=exequery($query, $tablename, $dbname); 
		if($result){ 
			echo"Primary Key successfully added to $tablename. <br><br>\n"; 
		} 
	} 
	$va="Alter Table $tablename"; 
	goto($tablename, $dbname,'alter.php', 'but', 'altertable', $va ); 
} 
 
//*************** Drop Key ***************** 
if(isset($_POST['droppk'])){ 
	
	$dbname=$_SESSION['dbname'];
	$query="ALTER table $tablename DROP PRIMARY KEY";	 
	$result=exequery($query, $tablename, $dbname); 
	if($result){ 
		echo"Primary key successfully dropped from $tablename.<br> <br>\n"; 
	} 
	$va="Alter Table $tablename"; 
	goto($tablename, $dbname,'alter.php', 'but', 'altertable', $va ); 
} 
 
//*************** Add Column Form One ***************** 
if(isset($_POST['addc'])){ 

	$dbname=$_SESSION['dbname'];
	echo"<table border=0 width='50%' align='center'>\n"; 
	echo"<tr><td>Field Name</td><td>Type</td></tr>\n"; 
		 
		echo"<form action=alter.php method=post>"; 
		
		echo"<input type=hidden name='tablename' value='$tablename'>\n"; 
		echo"<tr>\n"; 
		echo"<td><input type=text name='fieldn' ></td>\n"; 
		echo"<td><select name='ftype'>\n"; 
		for($f=0; $f < count($fieldtypes); $f ++){ 
			echo"<option value=$fieldtypes[$f]>$fieldtypes[$f]"; 
		} 
		echo"</select></td>"; 
		echo"<td><input class=but type=submit name='nxtaddcol' value='Next -->'></td>\n"; 
		echo"</form> </tr>\n";	 
	echo"</table>\n"; 
} 
 
//********************* Add Column Form Two ***************** 
if(isset($_POST['nxtaddcol'])){ 
	
	$dbname=$_SESSION['dbname'];
	$fieldn=$_POST['fieldn'];
	$ftype=$_POST['ftype'];
	echo"<table border=0 width='80%' align='center'>\n"; 
	echo"<tr><td>Field Name</td><td>Type</td><td>Length</td><td>Flags</td></tr>\n"; 
	echo"<form action=alter.php method=post>\n"; 
	echo"<input type=hidden name='tablename' value='$tablename'>\n"; 
	echo"<input type=hidden name='ftype' value='$ftype'>\n"; 
	echo"<input type=hidden name='fieldn' value='$fieldn'>\n"; 
	 
	echo"<tr><td>$fieldn</td><td>$ftype</td><td>\n"; 
	fieldformsize($ftype,0, 0); 
	echo"</td>"; 
	echo"<td><input type=text name='flags' ></td></tr>\n"; 
	 
	echo"</tr></table>\n"; 
	 
	echo"<div class=p>\n"; 
	echo"<h2 class=h>Column Position</h2>\n"; 
	echo"Current order of columns: <br>\n"; 
	$num =  mysql_num_fields($result); 
	$fields = mysql_list_fields($dbname, $tablename); 
	for ( $c = 0; $c < $num; $c++){ 
			echo"<font class='green'>$c ". mysql_field_name($fields, $c). "</font> | \n"; 
	} 
	echo"<br><br>"; 
	echo" Where do you want to place the new column? <br> \n";
   
	for ( $p = 0; $p <= $num; $p++){ 
		echo"<input type='radio' name='placement' value=$p><font class='green'>Col $p </font>\n"; 
	} 
   echo"<input type='hidden' name='numfld' value=$num>";
	echo"<br>The new column will be added to the end by default if nothing is selected above.\n"; 
	echo"</div>"; 
	echo"<input class=but type=submit name='addcol' value='Add Column'>\n"; 
	 
	echo"</form> \n";	 
	 
} 
//******************* Add Column form 3 *****************	 
	if(isset($_POST['addcol'])){ 
	$dbname=$_SESSION['dbname'];
    if(isset($_POST['placement'])&& $_POST['placement']!=""){
	    $placement=$_POST['placement'];
    }else{
        $placement=$_POST['numfld'];
    }
	$flags=$_POST['flags'];
	$ftype=$_POST['ftype'];
	$fieldn=$_POST['fieldn'];
    $lenx=$_POST['leng'];
		//if (isset($leng[0]) && $leng[0] >0){ 
        if(isset($lenx[0]) && $lenx[0] !=""){
			$siz = "$ftype($lenx[0]) "; 
		}else{ 
			$siz= "$ftype "; 
		} 
        $siz=StripSlashes($siz);
		if(! empty($flags)){ 
			$cng = " $siz $flags"; 
		}else{ 
			$cng="$siz"; 
		} 
		if (isset($placement)){ 
			if ($placement == 0){ 
				$cng .= " first"; 
			}else{ 
				$fields = mysql_list_fields($dbname, $tablename); 
				$cng .= " after ". mysql_field_name($fields, $placement - 1); 
			} 
		} 
		$query="ALTER table $tablename ADD COLUMN $fieldn $cng"; 
 
		$result=exequery($query, $tablename, $dbname); 
		if($result){ 
			echo"Column $fieldn successfully added to $tablename.<br> <br>\n"; 
		} 
		$va="Alter Table $tablename"; 
		goto($tablename, $dbname,'alter.php', 'but', 'altertable', $va ); 
	} 
 
//*************** Change Column Form ***************** 
if(isset($_POST['changec'])){ 
	$dbname=$_SESSION['dbname'];
	echo"<h2 class=h>Current Table Configuration</h2>\n"; 
	$query="Describe $tablename"; 
	$result=exequery($query, $tablename, $dbname); 
	$num= mysql_num_fields($result); 
	echo"<table border=1 width='100%' align='center'>\n"; 
	echo"<tr class=head>\n";
	echo"<td>Field Name</td><td>Type</td><td>Null</td><td>Key</td><td>Default</td><td>Extra</td></tr>\n"; 
	while($row= mysql_fetch_array($result)){ 
		echo"<tr>\n"; 
		for($x=0; $x < $num; $x++){ 
			echo"<td>\n"; 
			if (isset($row[$x])){ 
				echo"$row[$x]"; 
			} 
			echo"</td>\n"; 
		} 
		echo"</TR>\n"; 
	} 
	echo"</table>\n"; 
 
	echo"<h2 class=h>Guidelines for column changes</h2>\n"; 
	echo"<ul>\n"; 
	echo"<li>Type(size) must be specified even if no changes are made.<br>\n"; 
	echo"<li>If the Field is a Primary key, in extras you must specify NOT NULL but don't respecify Primary key.\n"; 
	echo"</ul>\n"; 
 
	$query="Select * from $tablename"; 
	$result=exequery($query, $tablename, $dbname); 
	echo"<table border=0 width='100%' align='center'>\n"; 
 
	echo"<tr class=head><td>Field</td><td>New Field Name</td><td>Type(Size)</td><td>Extras</td></tr>\n"; 
	$num =  mysql_num_fields($result);	 
	$fields = mysql_list_fields($dbname, $tablename); 
	echo"<form action=alter.php method=post>\n"; 
	echo"<input type=hidden name='dbname' value='$dbname'>\n"; 
	echo"<input type=hidden name='tablename' value='$tablename'>\n"; 
	 
	echo"<tr><td>\n"; 
	echo"<select name=fname >\n"; 
	for ( $c = 0; $c < $num; $c++){		 
		$fn =mysql_field_name($fields, $c); 
		echo"<option value=$fn>$fn\n"; 
	} 
	echo"</td>\n"; 
	 
		echo"<td><input type=text name='fieldn' ></td>\n"; 
		echo"<td>*<input type=text name='ftype' ></td>\n"; 
		echo"<td><input type=text name='extra' size=50></td>\n"; 
		 
		echo"<tr>\n"; 
		echo"<td><input class=but type=submit name='changecol' value='Change Column'></td>\n"; 
		echo"</form> </tr>\n"; 
	echo"</table>\n"; 
   // echo" <br>MySQL field flags: not null, primary key,  unique key, multiple key,  blob, unsigned,"; 
	//echo" zerofill, binary, enum, auto_increment, timestamp."; 
} 
//************************** Change Column ********************** 
	if(isset($_POST['changecol'])){ 
		$dbname=$_SESSION['dbname'];
		$fieldn=$_POST['fieldn'];
		$fname=$_POST['fname'];
		$ftype=StripSlashes($_POST['ftype']);
		$extra=$_POST['extra'];
		if (isset($fieldn) && $fieldn !=" " && !empty($fieldn)){ 
		
			$cng=" $fieldn";	 
		}else{ 
			$cng=" $fname"; 
		} 
		if(! empty($ftype) && $ftype !=" "){ 
            
			$cng .= " $ftype"; 
		} 
		if(! empty($extra) && $extra !=" "){ 
			$cng .= " $extra"; 
		} 
		$query="ALTER TABLE $tablename CHANGE COLUMN $fname $cng"; 
	 
		$query=trim($query); 
		$result=exequery($query, $tablename, $dbname); 
		if($result){ 
			echo"Column successfully changed in $tablename. <br><br>\n"; 
		} 
		$va="Alter Table $tablename"; 
		goto($tablename, $dbname,'alter.php', 'but', 'altertable', $va ); 
	} 
 
//*************** Drop Column Form ***************** 
if(isset($_POST['dropc'])){ 
	$tablename=$_POST['tablename'];
	$dbname=$_SESSION['dbname'];
	$num =  mysql_num_fields($result); 
	$fields = mysql_list_fields($dbname, $tablename); 
	for ( $c = 0; $c < $num; $c++){ 
		echo"<form action=alter.php method=post>\n"; 
		echo"<input type=hidden name='tablename' value='$tablename'>\n"; 
		echo"<input type=hidden name='num' value='$num'>\n"; 
		$fn =mysql_field_name($fields, $c); 
		echo"<input type=hidden name=col value='$fn'>\n"; 
		echo"<input class=but type=submit name=dropcol value='Drop Column'>\n"; 
		echo" $fn <br>\n"; 
		echo"</form>\n"; 
	} 
} 
//******************* Drop Column ****************** 
if(isset($_POST['dropcol'])){ 
	$dbname=$_SESSION['dbname'];
	$num=$_POST['num'];
	$col=$_POST['col'];
	$query="ALTER table $tablename DROP COLUMN $col"; 
	$result=exequery($query, $tablename, $dbname); 
	if($result){ 
		echo"Column $col successfully dropped from $tablename.<br> <br>\n"; 
	} 
	$va="Alter Table $tablename"; 
	goto($tablename, $dbname,'alter.php', 'but', 'altertable', $va ); 
} 
 
//***************** Menu ******************************* 
if(isset($_POST['altertable'])){ 
	echo"<form action=alter.php method=post>\n"; 
	 
	echo"<input class=but type=hidden name='dbname' value='$_SESSION[dbname]'>\n"; 
	echo"<input class=but type=hidden name='tablename' value='$tablename'>\n"; 
	echo"<input class=but type=submit name='rename' value='Rename Table'>\n"; 
	echo"<br><br>\n"; 
	echo"<input class=but type=submit name='addind' value='Add an Index'>\n"; 
	echo"<input class=but class=but type=submit name='dropind' value='Drop an Index'>\n"; 
	echo"<br><br>\n"; 
	echo"<input class=but type=submit name='addpk' value='Add a Primary Key'>\n"; 
	echo"<input class=but type=submit name='droppk' value='Drop a Primary Key'>\n"; 
	echo"<br><br>\n"; 
	echo"<input class=but type=submit name='addc' value='Add A Column'>\n"; 
	echo"<input class=but type=submit name='changec' value='Change A Column'>\n"; 
	echo"<input class=but type=submit name='dropc' value='Drop A Column'>\n"; 
	echo"</form>\n"; 
}	 
 display_foot();
?> 
</body> 
</html>