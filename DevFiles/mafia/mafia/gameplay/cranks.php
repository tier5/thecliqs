<?

include("html.php");



$pmp = @mysql_fetch_array(mysql_query("SELECT city,rank,crew FROM $tab[pimp] WHERE id='$id';"));

$cty = @mysql_fetch_array(mysql_query("SELECT name FROM $tab[city] WHERE id='$pmp[0]';"));

$crw = @mysql_fetch_array(mysql_query("SELECT rank FROM $tab[crew] WHERE id='$pmp[2]';")); 

$nextupdate = mysql_fetch_array(mysql_query("SELECT lastran FROM $tab[cron] WHERE cronjob='cranks';"));





if($rnk){$r=$rnk;}

if(!$crw){$crw[0]=1;}

if(($r > 0)){$crw[0]=$r;}

$l=15+$crw[0];

$h=$crw[0]-15;

GAMEHEADER("alliance ranks");

?>

<form method="post" action="cranks.php?tru=<?=$tru?>">

<br>
<B>Family Rankings</B><br>

<br>

<table width="95%" cellspacing="1">

 <tr>

  <td align="center" width="5%">&nbsp;</td>
  <td width="1"></td><td><B>Family</B></td><td align="right"><B>Net Worth</B></td>

 </tr>

<?

if((!$r) || ($r < 21))

{

$get = mysql_query("SELECT id,name,founder,city,icon,networth,members,rank FROM $tab[crew] WHERE id>0 ORDER BY rank ASC limit 10;");

while ($t10 = @mysql_fetch_array($get))

      {

            if($rankstart==0){$rankcolor="#cccccc";$rankstart++;}

        elseif($rankstart==1){$rankcolor="#999999";$rankstart--;}



       $crwcity = mysql_fetch_array(mysql_query("SELECT name FROM $tab[city] WHERE id='$t10[3]';"));

       $foundid = mysql_fetch_array(mysql_query("SELECT id FROM $tab[pimp] WHERE pimp='$t10[2]';"));

       ?>

       <tr bgcolor="<?=$rankcolor?>">

        <td align="center"><?=$t10[7]?>.</td>

        <td align="center" width="1"><nobr><?if($t10[4]){?><a href="family.php?cid=<?=$t10[0]?>&tru=<?=$tru?>"><img src="<?=$t10[4]?>" align="absmiddle" width="16" height="16" border="0"></a><?}?></nobr></td>

        <td><nobr><a href="family.php?cid=<?=$t10[0]?>&tru=<?=$tru?>"><?=$t10[1]?></font></a> <small> in <font color="3366FF"><?=$crwcity[0]?></font></small></nobr></td>

        <td align="right"><nobr><font color="#3366FF">$<?=commas($t10[5])?></font></nobr></td>

       </tr>

       <?

       }

}?>

 <tr>

  <td colspan="7"><br></td>

 </tr>

<?

    if($r){$s=$r-20; $a=40;}

  else{$s=$crw[0]-20;$a=40;}



  if ($crw[0] <= 30){$s=10;$a=10+$crw[0];}



$get = mysql_query("SELECT id,name,founder,city,icon,networth,members,rank FROM $tab[crew] WHERE id>0 ORDER BY rank ASC limit $s, $a;");

while ($t10 = @mysql_fetch_array($get))

      {

            if($rankstart==0){$rankcolor="#cccccc";$rankstart++;}

        elseif($rankstart==1){$rankcolor="#999999";$rankstart--;}



       $crwcity = mysql_fetch_array(mysql_query("SELECT name FROM $tab[city] WHERE id='$t10[3]';"));

       $foundid = mysql_fetch_array(mysql_query("SELECT id FROM $tab[pimp] WHERE pimp='$t10[2]';"));

       ?>

       <tr bgcolor="<?=$rankcolor?>">

        <td align="center"><?=$t10[7]?>.</td>

        <td align="center" width="1"><nobr><?if($t10[4]){?><a href="family.php?cid=<?=$t10[0]?>&tru=<?=$tru?>"><img src="<?=$t10[4]?>" align="absmiddle" width="16" height="16" border="0"></a><?}?></nobr></td>

        <td><nobr><a href="family.php?cid=<?=$t10[0]?>&tru=<?=$tru?>"><?=$t10[1]?></font></a> <small> in <font color="3366FF"><?=$crwcity[0]?></font></small></nobr></td>

        <td align="right"><nobr><font color="#3366FF">$<?=commas($t10[5])?></font></nobr></td>

       </tr>

       <?}?>

</table>

<br><small><a href="cranks.php?r=<?=$h?>&tru=<?=$tru?>">show higher ranks</a> <b><font color="7777CC">::</font></b> <a href="cranks.php?r=<?=$l?>&tru=<?=$tru?>">show lower ranks</a> <b><font color="7777CC">::</font></b> center on rank <input type="text" class="text" size="4" name="rnk"> <input type=submit class="button" value=GO></small>

</form>

<?=bar($id)?>

<br>

<?

GAMEFOOTER();

?>