<?
include("html.php");

$pmp = mysql_fetch_array(mysql_query("SELECT city,rank FROM $tab[pimp] WHERE id='$id';"));

if(!$c){ $c=$pmp[0]; }

$cty = mysql_fetch_array(mysql_query("SELECT name,id FROM $tab[city] WHERE id='$c';"));
$nextupdate = mysql_fetch_array(mysql_query("SELECT lastran FROM $tab[cron] WHERE cronjob='ranks';"));

if($rnk){$r=$rnk;}
if(($r > 0)){$pmp[1]=$r;}
$l=15+$pmp[1];
$h=$pmp[1]-15;
GAMEHEADER("$cty[0]");
?>
<form method="post" action="ranks.php?c=&tru=<?=$tru?>">
<B>City Ranks</B><font size="1"><b><font color="ff0000"><?=$cty[0]?></font></b>
<br>
<br>view current 
<select name="cty" onChange="MM_jumpMenu('parent',this,0,this.options[this.selectedIndex].value,'_main','toolbar=yes,location=yes,status=yes,resizable=yes,scrollbars=yes')">
 <option value="" selected><?=$cty[0]?></option>
 <?
 $get = mysql_query("SELECT id,name FROM $tab[city] WHERE id!='$c' ORDER BY id ASC;");
 while ($ctymenu = mysql_fetch_array($get))
 {?><option value="ranks.php?c=<?=$ctymenu[0]?>&tru=<?=$tru?>"><?=$ctymenu[1]?></option><?}?>
</select> ranks
<br>
<table width="500" cellspacing="1">
 <tr>
  <td align="center">&nbsp;</td>
  <td ></td>
  <td ><B>Mafioso</B></td>
  <td align="center"><B>Operatives</B></td>
  <td align="center"><B>Defensive</B></td>
  <td align="center"><B>Net Worth</B></td>
 </tr>
<?
if((!$r) || ($r < 21))
{
$get = mysql_query("SELECT id,pimp,whore,thug,networth,online,city,rank,nrank,crew,lowrider,dealers,bootleggers,hustlers,punks,hitmen,bodyguards FROM $tab[pimp] WHERE city='$c' AND rank!='99999' ORDER BY rank ASC limit 10;");
while ($t10 = mysql_fetch_array($get))
      {
      $online=$time-$t10[5];
      if ($online < 600){$on="<img src=$site[img]online.gif width=16 height=16 align=absmiddle>";}else{$on='';}

            if($id == $t10[0]){$rankcolor = "#cccccc";}
       elseif($rankstart==0){$rankcolor="#cccccc";$rankstart++;}
       elseif($rankstart==1){$rankcolor="#999999";$rankstart--;}

       $pmpcity = mysql_fetch_array(mysql_query("SELECT name FROM $tab[city] WHERE id='$t10[7]';"));
       $icn = mysql_fetch_array(mysql_query("SELECT icon FROM $tab[crew] WHERE id='$t10[9]';"));
       ?>
       <tr bgcolor="<?=$rankcolor?>">
        <td align="center"><?=$t10[7]?>.</td>
        <td align="center" width="17"><?=$on?></td>
        <td><nobr><?if($icn[0]){?><a href="family.php?cid=<?=$t10[9]?>&tru=<?=$tru?>"><img src="<?=$icn[0]?>" align="absmiddle" width="16" height="16" border="0"></a><?}?> <a href="mobster.php?pid=<?=$t10[1]?>&tru=<?=$tru?>"><?=$t10[1]?></a></nobr></td>
        <td align="right"><?=commas($t10[2]+$t10[14]+$t10[11]+$t10[12]+$t10[13])?></td>
        <td align="right"><?=commas($t10[3]+$t10[15]+$t10[16])?></td>
        <td align="right">$<?=commas($t10[4])?></td>
       </tr>
       <?$selffont="";
       }
}?>
 <tr>
  <td colspan="7"><br></td>
 </tr>
<?
    if($r){$s=$r-20; $a=40;}
  else{$s=$pmp[1]-20;$a=40;}

  if ($pmp[1] <= 30){$s=10;$a=10+$pmp[1];}

//$get = mysql_query("SELECT id,pimp,whore,thug,networth,online,city,rank,nrank,crew FROM $tab[pimp] WHERE city='$c' AND rank!='99999' ORDER BY rank ASC limit $s,$a");
$get = mysql_query("SELECT id,pimp,whore,thug,networth,online,city,rank,nrank,crew,lowrider,dealers,bootleggers,hustlers,punks,hitmen,bodyguards FROM $tab[pimp] WHERE city='$c' AND rank!='99999' ORDER BY rank ASC limit $s,$a");
while ($t10 = mysql_fetch_array($get))
      {
      $online=$time-$t10[5];
      if ($online < 600){$on="<img src=../images/online.gif width=16 height=16 align=absmiddle>";}else{$on='';}

            if($id == $t10[0]){$rankcolor = "#cccccc";}
        elseif($rankstart==0){$rankcolor="#cccccc";$rankstart++;}
        elseif($rankstart==1){$rankcolor="#999999";$rankstart--;}

      $pmpcity = mysql_fetch_array(mysql_query("SELECT name FROM $tab[city] WHERE id='$t10[7]';"));
      $icn = mysql_fetch_array(mysql_query("SELECT icon FROM $tab[crew] WHERE id='$t10[9]';"));
       ?>
       <tr bgcolor="<?=$rankcolor?>">
        <td align="center"><?=$t10[7]?>.</td>
        <td align="center"><?=$on?></td>
        <td><nobr><?if($icn[0]){?><a href="family.php?cid=<?=$t10[9]?>&tru=<?=$tru?>"><img src="<?=$icn[0]?>" align="absmiddle" width="16" height="16" border="0"></a><?}?> <a href="mobster.php?pid=<?=$t10[1]?>&tru=<?=$tru?>"><?=$t10[1]?></a></nobr></td>
        <td align="right"><?=commas($t10[2]+$t10[14]+$t10[11]+$t10[12]+$t10[13])?></td>
        <td align="right"><?=commas($t10[3]+$t10[15]+$t10[16])?></td>
        <td align="right">$<?=commas($t10[4])?></td>
       </tr>
       <?
      }?>
</table>
<br><small><img src="/images/online.gif" width="16" height="16" align="middle"> = online <b><font color="FFCC00">::</font></b> <?if(!$show){echo"<a href=ranks.php?c=$c&r=$h&tru=$tru>show higher ranks</a>";}?> <b><font color="FFCC00">::</font></b> <?if(!$show){echo"<a href=ranks.php?c=$c&r=$l&tru=$tru>show lower ranks</a>";}?> <b><font color="FFCC00">::</font></b> center on rank <input type="text" class="text" size="4" name="rnk"> <input type="submit" class="button" value=GO></small>
</form>
<?=bar($id)?>
<br>
<?
GAMEFOOTER();
?>