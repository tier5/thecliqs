<?
include("html.php");

$pmp = mysql_fetch_array(mysql_query("SELECT city,nrank FROM $tab[pimp] WHERE id='$id';"));
$cty = mysql_fetch_array(mysql_query("SELECT name FROM $tab[city] WHERE id='$pmp[0]';"));

if($rnk){$r=$rnk;}
if(($r > 0)){$pmp[1]=$r;}
$l=15+$pmp[1];
$h=$pmp[1]-15;
GAMEHEADER("current nation ranks");
?>
<form method="post" action="granks.php?tru=<?=$tru?>">
<font size="+2">Nation Ranks</font>
<br><b></b>
<br>
<br>
<br>
<br>
<table width="95%" cellspacing="1">
 <tr>
  <td align="center" width="43">&nbsp;</td><td width="17"></td><td width="117"><B>Mafioso</B></td>
  <td width="229" align="center"><B>Operatives</B></td><td width="155" align="center"><B>Defensive</B></td>
 <td width="170" align="center"><B>Net Worth</B></td>
 </tr>
<?
if((!$r) || ($r < 21))
{
$get = mysql_query("SELECT id,pimp,whore,thug,networth,online,city,rank,nrank,crew,dealers,bootleggers,hustlers,punks,hitmen,bodyguards FROM $tab[pimp] WHERE id>'0' AND nrank!='99999' ORDER BY nrank ASC limit 10;");
while ($t10 = mysql_fetch_array($get))
      {
      $online=$time-$t10[5];
      if ($online < 600){$on="<img src=../images/online.gif width=16 height=16 align=absmiddle>";}else{$on='';}

            if($id == $t10[0]){$rankcolor = "#cccccc";}
        elseif($rankstart==0){$rankcolor="#cccccc";$rankstart++;}
        elseif($rankstart==1){$rankcolor="#999999";$rankstart--;}

       $pmpcity = mysql_fetch_array(mysql_query("SELECT name FROM $tab[city] WHERE id='$t10[6]';"));
       $icn = mysql_fetch_array(mysql_query("SELECT icon FROM $tab[crew] WHERE id='$t10[9]';"));
       ?>
       <tr bgcolor="<?=$rankcolor?>">
        <td align="center"><?=$t10[8]?>.</td>
        <td align="center" width="17"><?=$on?></td>
        <td><nobr><?if($icn[0]){?><a href="family.php?cid=<?=$t10[9]?>&tru=<?=$tru?>"><img src="<?=$icn[0]?>" align="absmiddle" width="16" height="16" border="0"></a><?}?> <a href="mobster.php?pid=<?=$t10[1]?>&tru=<?=$tru?>"><?=$selffont?><?=$t10[1]?></font></a></nobr></td>
        <td align="right"><div align="center"></div>
          <div align="center">
            <?=commas($t10[2]+$t10[12]+$t10[10]+$t10[11]+$t10[13])?>
         </div></td>
        <td align="right"><div align="center"></div>
          <div align="center">
            <?=commas($t10[3]+$t10[14]+$t10[15])?>
         </div></td>
        <td align="right">$<?=commas($t10[4])?></td>
       </tr>
       <?$selffont="";
       }
}?>
 <tr>
  <td colspan="6"><br></td>
 </tr>
<?
    if($r){$s=$r-20; $a=40;}
  else{$s=$pmp[1]-20;$a=40;}

  if ($pmp[1] <= 30){$s=10;$a=10+$pmp[1];}

$get = mysql_query("SELECT id,pimp,whore,thug,networth,online,city,rank,nrank,crew,lowrider,dealers,bootleggers,hustlers,punks,hitmen,bodyguards FROM $tab[pimp] WHERE id>'0' AND nrank!='99999' ORDER BY nrank ASC limit $s,$a");
while ($t10 = mysql_fetch_array($get))
      {
      $online=$time-$t10[5];
      if ($online < 600){$on="<img src=../images/online.gif width=16 height=16 align=absmiddle>";}else{$on='';}

        if($id == $t10[0]){$rankcolor = "#ff0000";}
        elseif($rankstart==0){$rankcolor="#cccccc";$rankstart++;}
        elseif($rankstart==1){$rankcolor="#999999";$rankstart--;}

      $pmpcity = mysql_fetch_array(mysql_query("SELECT name FROM $tab[city] WHERE id='$t10[6]';"));
      $icn = mysql_fetch_array(mysql_query("SELECT icon FROM $tab[crew] WHERE id='$t10[9]';"));
       ?>
       <tr bgcolor="<?=$rankcolor?>">
        <td align="center"><?=$t10[8]?>.</td>
        <td align="center"><?=$on?></td>
        <td><nobr><?if($icn[0]){?><a href="family.php?cid=<?=$t10[9]?>&tru=<?=$tru?>"><img src="<?=$icn[0]?>" align="absmiddle" width="16" height="16" border="0"></a><?}?> <a href="mobster.php?pid=<?=$t10[1]?>&tru=<?=$tru?>"><?=$selffont?><?=$t10[1]?></font></a></nobr></td>
        <td align="right"><div align="center">
          <?=commas($t10[2]+$t10[12]+$t10[14]+$t10[11]+$t10[13])?>
        </div></td>
        <td align="right"><div align="center">
          <?=commas($t10[3]+$t10[15]+$t10[16])?>
        </div>         </td>
        <td align="right">$<?=commas($t10[4])?></td>
       </tr>
       <?$selffont="";
      }?>
</table>
<br><small><img src="../images/online.gif" width="16" height="16" align="middle"> = online <b><font color="7777CC">::</font></b> <a href=granks.php?r=<?=$h?>&tru=<?=$tru?>>show higher ranks</a> <b><font color="7777CC">::</font></b> <a href=granks.php?r=<?=$l?>&tru=<?=$tru?>>show lower ranks</a> <b><font color="7777CC">::</font></b> center on rank <input type="text" class="text" size="4" name="rnk"> <input type="submit" class="button" value=GO></small>
<br>
</form>
<?=bar($id)?>
<br>
<?
GAMEFOOTER();
?>