<?php
include("html.php");

GAMEHEADER("Winnings");
?>

<font size="+3" color="ff0000">Winnings</font><br><br>

<font size="+1" color="ff0000">Supporters</font>
<table width="94%" cellspacing="1">
  <tr>
    <td align="center" width="41"><b>Rank</b></td>
    <td width="150"><b>Prize</b></td>
    <td width="179"><B>Mafioso</B></td>
    <td align="center"><b>Networth</b></td>
  </tr>
  <?php
  	$prize 			= array();
  	$query 			= sprintf("SELECT rank,prize FROM prizes WHERE type = 'supporter'");
  	$prizeResult	= mysql_query($query);
  	while($line = mysql_fetch_array($prizeResult)){
  		$prize[$line[0]] = $line[1];
  	}
  	
  	$query	= sprintf("	SELECT id,pimp,networth,online,rank,nrank,status,online
  						FROM %s WHERE status='supporter' ORDER BY nrank ASC limit 10", $tab["pimp"]);
  	//echo $query;
  	$supResult = mysql_query($query);
  	
  	$i = 1;
  	while($line = mysql_fetch_array($supResult)){
  		if($rankstart==0){$rankcolor="#220000";$rankstart++;}
		elseif($rankstart==1){$rankcolor="#000000";$rankstart--;}
	
  		$online=$time-$line[7];
  		if ($online < 600){$on="<img src=images/online.gif width=16 height=16 align=absmiddle>";}else{$on='';}
  		
  		print("<tr bgcolor=\"$rankcolor\"><td>$i</td>");
  		print("<td>$prize[$i]</td>");
  		print("<td>$on<a href=\"pimp.php?pid=$line[1]&tru=$tru\">$line[1]</a></td>");
  		print("<td>$".commas($line[2])."</td></tr>");
  		$i++;
  	}
  ?>
</table>

<br><br>
 
<font size="+1" color="ff0000">Family Rankings</font><br>
<table width="95%" cellspacing="1">
 <tr>

  <td align="center" width="5%"><strong>Rank</strong></td>
  <td align="center"><strong>Prize</strong></td>
  <td width="1"></td><td><B>Family</B></td><td align="right"><div align="left"><B>Net Worth</B></div></td>

 </tr>

<?
$prize = array();
$query 	= sprintf("SELECT rank,prize FROM prizes WHERE type = 'family'");
$prizeResult	= mysql_query($query);
while($line = mysql_fetch_array($prizeResult)){
	$prize[$line[0]] = $line[1];
}

$get = mysql_query("SELECT id,name,founder,city,icon,networth,members,rank FROM $tab[crew] WHERE id>0 ORDER BY rank ASC limit 10;");

$i = 1;
while ($t10 = @mysql_fetch_array($get)){
	if($rankstart==0){$rankcolor="#220000";$rankstart++;}
	elseif($rankstart==1){$rankcolor="#000000";$rankstart--;}
?>
<tr bgcolor="<?=$rankcolor?>">
	<td align="center"><?=$t10[7]?>.</td>
	<td><?=$prize[$i]?></td>
	<td align="center" width="1">
		<nobr><?if($t10[4]){?><a href="crew.php?cid=<?=$t10[0]?>&tru=<?=$tru?>"><img src="<?=$t10[4]?>" align="absmiddle" width="16" height="16" border="0"></a><?}?></nobr>
	</td>
	<td>
		<nobr><a href="crew.php?cid=<?=$t10[0]?>&tru=<?=$tru?>"><?=$t10[1]?></font></a></nobr>
	</td>
	<td align="right">
		<div align="left"><nobr><font color="#FFFFFF">$
        <?=commas($t10[5])?>
	      </font></nobr>
    </div></td>
</tr>
<?$i++;}?>
</table>

<p><br>
    <font color="#FF0000" size="+1">Free Rank</font></p>
<table width="94%" cellspacing="1">
  <tr>
    <td align="center" width="41"><b>Rank</b></td>
    <td width="150"><b>Prize</b></td>
    <td width="179"><B>Mafioso</B></td>
    <td align="center"><b>Networth</b></td>
  </tr>
  <?php
  	$prize 			= array();
  	$query 			= sprintf("SELECT rank,prize FROM prizes WHERE type = 'free'");
  	$prizeResult	= mysql_query($query);
  	while($line = mysql_fetch_array($prizeResult)){
  		$prize[$line[0]] = $line[1];
  	}
  	
  	$query	= sprintf("	SELECT id,pimp,networth,online,rank,nrank,status,online
  						FROM %s WHERE status='normal' ORDER BY nrank ASC limit 10", $tab["pimp"]);
  	//echo $query;
  	$supResult = mysql_query($query);
  	
  	$i = 1;
  	while($line = mysql_fetch_array($supResult)){
  		if($rankstart==0){$rankcolor="#220000";$rankstart++;}
		elseif($rankstart==1){$rankcolor="#000000";$rankstart--;}
	
  		$online=$time-$line[7];
  		if ($online < 600){$on="<img src=images/online.gif width=16 height=16 align=absmiddle>";}else{$on='';}
  		
  		print("<tr bgcolor=\"$rankcolor\"><td>$i</td>");
  		print("<td>$prize[$i]</td>");
  		print("<td>$on<a href=\"pimp.php?pid=$line[1]&tru=$tru\">$line[1]</a></td>");
  		print("<td>$".commas($line[2])."</td></tr>");
  		$i++;
  	}
  ?>
</table>
<?php GAMEFOOTER(); ?>