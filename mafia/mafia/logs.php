<?
include("html.php");
admin();

if($deleteall) {
$result = mysql_query(" TRUNCATE TABLE logs");
}
if(isset($_POST['Submit2']))
{


	$total = $_POST['total'];
	$td = 0;
	$i = 0;
	
	for($i = 1; $i <= $total; $i++)
	{
		if(isset($_POST["d$i"]))
		{ 				
    		mysql_query("DELETE FROM logs WHERE id=".$_POST["d$i"],$dbh);
			$td++;
		}
	}

echo "<html><body><script language=javascript1.1>alert('Log Deleted');</script><noscript>Your browser doesn't support JavaScript 1.1 or it's turned off in your browsers preferences.</noscript></body></html>";			
}

siteheader();
secureheader();
?>
<div align="center">
<form name="form" method="post" action="">
<table width="95%" border="0" cellspacing="0" cellpadding="0">
    <tr>
    <td colspan="7">
    <input type="submit" name="Submit2" value="Delete Selected"> <input name="total" type="hidden" id="total" value="<?php echo $n?>">
    <input type="submit" name="deleteall" value="Clear All Logs!">
    </td>		
    </tr>
    <tr>
    <td colspan="7">
    <hr></td>		
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="3">
      
     <form method="post" action="logs.php">
     Search by round number:<font color="#FFFFFF">
		</font>&nbsp;
     </form></td>	

      <td colspan="3">
      
     <font color="#FFFFFF">
		<input type="text" name="roundnumber" id="roundnumber" value="" size="5"> <input type="submit" name="byround" value="Submit"><br />
*search MASTER for acount credit addon in round area</font>
     </td>	

    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td colspan="3">
      
     <form method="post" action="">
     Search by Pimp's Name:<font color="#FFFFFF"> </font>&nbsp;
     </form></td>	

      <td colspan="3">
      
     <font color="#FFFFFF"> <input type="text" name="pimpname" id="pimpname" value="" size="15"> </font>
     <input type="submit" name="byname" value="Submit"></td>	

    </tr>
    	<tr>
      <td>&nbsp;</td>
      <td colspan="3">
      
     <form method="POST" action="&ip=ipaddress">
     Search by IP :<font color="#FFFFFF"> 
		</font>&nbsp;
     </form></td>	

      <td colspan="3">
      
     <font color="#FFFFFF"> 
		<input type="text" name="ipaddress" id="ipaddress" value="" size="15"> </font>
     <input type="submit" name="byip" value="Submit"></td>	

    		</tr>
    <tr> 
      <td>&nbsp;</td>
      <td colspan="6"></td>	

    </tr>
</table>
</form>
<?php $name = $_POST['pimpname'];
$round = $_POST['roundnumber'];   
$ip = $_POST['ipaddress'];  

if($name || $round || $ip){?>
<table width="95%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><p align="center"><u><b><font face="Verdana" color="#FF9933">Manage Logs</font></b></u></p>


<form name="form" method="post" action="">
<table>
<?
   class Pager 
   { 
       function getPagerData($numHits, $limit, $page) 
       { 
           $numHits  = (int) $numHits; 
           $limit    = max((int) $limit, 1); 
           $page     = (int) $page; 
           $numPages = ceil($numHits / $limit); 

           $page = max($page, 1); 
           $page = min($page, $numPages); 

           $offset = ($page - 1) * $limit; 

           $ret = new stdClass; 

           $ret->offset   = $offset; 
           $ret->limit    = $limit; 
           $ret->numPages = $numPages; 
           $ret->page     = $page; 

           return $ret; 
       } 
   } 
   
$name = $_POST['pimpname'];
$round = $_POST['roundnumber'];   
$ip = $_POST['ipaddress'];  

    // get the pager input values 
    $page = $_GET['page']; 
    $limit = 5000; 
    
    if($byname) {
    $result = mysql_query("select count(*) from logs where pimpname = '$name'"); 
    }
    elseif($byround) {
    $result = mysql_query("select count(*) from logs where round = '$round'"); 
    }
    elseif($byip) {
    $result = mysql_query("select count(*) from logs where ip = '$ip'"); 
    }
    else {
        if (!fetch("Select * from $tab[logs];"))
        { $restart4=true; }
        else {
    	$result = mysql_query("select count(*) from logs"); 
    	}
    }
    $total = @mysql_result($result, 0, 0); 

    // work out the pager values 
    $pager  = Pager::getPagerData($total, $limit, $page); 
    $offset = $pager->offset; 
    $limit  = $pager->limit; 
    $page   = $pager->page; 

    // use pager values to fetch data         
   // if ($page >= 2){ // this is the first page - there is no previous page 
        //echo "<a href=\"logs.php?page=" . ($page - 1) . "\"> Previous</a>"; 
	//}
    //for ($i = 1; $i <= $pager->numPages; $i++) { 
        //if ($i == $pager->page) 
            //echo "<b>Page $i</b>"; 
        //else 
            //echo "<a href=\"logs.php?page=$i\"> Page $i </a>"; 
    //} 

    //if ($page != $pager->numPages) // this is the last page - there is no next page 
        //echo "<a href=\"logs.php?page=" . ($page + 1) . "\"> Next</a>"; 
        
 

if($byname) {
    	if (!fetch("Select * from logs where pimpname = '$name';"))
        { $restart1=true; }
        else {
		$result = mysql_query("Select * from logs where pimpname = '$name' order by id DESC limit $offset, $limit");
		}
} elseif($byround) {
    	if (!fetch("Select * from logs where round = '$round';"))
        { $restart2=true; }
        else {
		$result = mysql_query("Select * from logs where round = '$round' order by id DESC limit $offset, $limit");
		}
} elseif($byip) {
    	if (!fetch("Select * from logs where ip = '$ip';"))
        { $restart3=true; }
        else {
		$result = mysql_query("Select * from logs where ip = '$ip' order by id DESC limit $offset, $limit");
		}
} else {
    	if (!fetch("Select * from logs;"))
        { $restart4=true; }
        else {
		$result = mysql_query("Select * from logs order by id DESC limit $offset, $limit");
		}
}
  if($restart1==true){?>No results for the pimp name <b><?=$name?></b>.<br><br><?}
  if($restart3==true){?>No results for the ip <b><?=$ip?></b>.<br><br><?}
  if($restart2==true){?>No results for round <b><?=$round?></b>.<br><br><?}
  if($restart4==true){?>No logs recorded.<br><br><?}

$num = @mysql_num_rows($result);
$n = 0;
?>

    <tr bgcolor="#000000"> 
      <td width="3%">
		<font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">&nbsp;
		</font></td>
      <td width="12%">
		<font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><strong>
		Username</strong></font></td>

      <td width="10%">
		<strong>
		<font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFFF">
		Time</font></strong></td>

	  <td width="16%" colspan="2">
		<strong>
		<font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFFF">
		Round</font></strong></td>
      <td width="39%">
		<strong>
		<font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFFF">
		Action</font></strong></td>
      <td width="17%">
		<strong>
		<font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFFF">
		IP</font></strong></td>

    </tr>
    <?php while($row = @mysql_fetch_array($result, MYSQL_BOTH)){
$n++;
?>
    <tr> 
      <td><input type="checkbox" name="d<?php echo $n;?>" value="<?php echo $row['id'];?>"></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?php echo $row['pimpname'];?></font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?php echo date("m/d/y h:i:s", $row[time]);?></font></td>
 	  <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?php echo $row['round'];?></font></td> 		
 	  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?php echo $row['action'];?></font></td> 			
 	  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?php echo $row['ip'];?></font></td> 			
    </tr>
<? }

 ?>
  </table>
</form></td>
  </tr>
</table><? }?>
</div>
<?
sitefooter();
?>