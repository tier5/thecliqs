<?
include("html.php");
$pmp = mysql_fetch_array(mysql_query("SELECT city,rank,crew,networth FROM $tab[pimp] WHERE id='$id';"));

if(!$c){ $c=$pmp[0]; }

$cty = mysql_fetch_array(mysql_query("SELECT name,id FROM $tab[city] WHERE id='$c';"));
$nextupdate = mysql_fetch_array(mysql_query("SELECT lastran FROM $tab[cron] WHERE cronjob='ranks';"));
$game = mysql_fetch_array(mysql_query("SELECT type FROM $tab[game] WHERE round='$tru';"));

if($rnk){$r=$rnk;}
if(($r > 0)){$pmp[1]=$r;}
$l=15+$pmp[1];
$h=$pmp[1]-15;
$high=round($pmp[3]*4);
$low=round($pmp[3]/2);

GAMEHEADER("$cty[0]");
?><body background="../bg.gif">
<form method="post" action="attack.php?c=&tru=<?=$tru?>">
  <p><br>
    <br>
    <br>
    View <font color="#000000">Mafioso</font> in city: 
    <select name="cty" onChange="MM_jumpMenu('parent',this,0,this.options[this.selectedIndex].value,'_main','toolbar=yes,location=yes,status=yes,resizable=yes,scrollbars=yes')">
   <option value="" selected>
   <?=$cty[0]?>
   </option>
   <?
 $get = mysql_query("SELECT id,name FROM $tab[city] WHERE id!='$c' ORDER BY id ASC;");
 while ($ctymenu = mysql_fetch_array($get))
 {?>
   <option value="attack.php?c=<?=$ctymenu[0]?>&tru=<?=$tru?>">
   <?=$ctymenu[1]?>
   </option>
   <?}?>
    </select> <br />
</p>
<br>
  </small>  <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#000000">
    <tr>
      <td align="center" width="34"><font color="white"><b><small>rank</small></b></font></td>
      <td width="17"><font color="white">&nbsp;</font></td>
      <td width="161"><font color="white"><b>Mafioso</b></font></td>
      <td width="45" align="center"><div align="center"><font color="white"><b>Operatives</b></font></div></td>
      <td width="44" align="center"><div align="center"><font color="white"><b>Defensive</b></font></div></td>
      <td width="100" align="center"><font color="white"><b>Net Worth</b></font></td>
      <td align="center"><font color="white"><b>Attack</b></font></td>
    </tr>
    <?
if((!$r) || ($r < 21))
{
$get = mysql_query("SELECT id,pimp,whore,thug,networth,online,city,crew,lowrider,whorek,thugk,status,rank,dealers,bootleggers,hustlers,punks,hitmen,bodyguards FROM $tab[pimp] WHERE city='$c' AND networth<=$high AND networth>=$low AND id != '$id' ORDER BY rank ASC;");
while ($t10 = mysql_fetch_array($get))
      {
      $kills=$t10[11]+$t10[12];
	  $online=$time-$t10[5];
      if ($online < 600){$on="<img src=../images/online.gif width=16 height=16 align=absmiddle>";}else{$on='';}

             if($id == $t10[0]){$rankcolor = "#ff0000";}
        elseif($rankstart==0){$rankcolor="#cccccc";$fontcolor="white";$rankstart++;}
        elseif($rankstart==1){$rankcolor="#999999";$fontcolr="white";$rankstart--;}

              
$game = mysql_fetch_array(mysql_query("SELECT type FROM $tab[game] WHERE round='$tru';"));
$pmpcity = mysql_fetch_array(mysql_query("SELECT name FROM $tab[city] WHERE id='$t10[7]';"));
$icn = mysql_fetch_array(mysql_query("SELECT icon FROM $tab[crew] WHERE id='$t10[7]';"));
       ?>
    <tr bgcolor="<?=$rankcolor?>">
      <td align="center"><font color="white"><?=$t10[rank]?>.</font></td>
      <td> <font color="white">
        <?=$on?> 
      </font></td>
      <td> <font color="white"><nobr>
          <?if($icn[0]){?>
          <a href="family.php?cid=<?=$t10[9]?>&tru=<?=$tru?>"><img src="<?=$icn[0]?>" align="center" width="16" height="16" border="0"></a>
          <?}?>
        <a href="mobster.php?pid=<?=$t10[1]?>&tru=<?=$tru?>">
        <?=$t10[pimp]?>
      </a>  </nobr> </font></td>
      <td align="right"><div align="center"><font color="white" size="1">
        <?=commas($t10[whore]+$t10[dealers]+$t10[bootleggers]+$t10[hustlers]+$t10[punks])?> 
      </font> </div></td>
      
	  <td align="right"><div align="center"><font color="white" size="1">
	    <?=commas($t10[thug]+$t10[hitmen]+$t10[bodyguards])?> 
      </font> </div></td>
      
	  <td align="right"><font color="white" size="1"> $<?=commas($t10[networth])?></font> </td>
      
	  <td width="22" align="center"><font color="white">
	    <?if (($id != $t10[0])&($t10[networth] > $low)&($high > $t10[networth])){?>
          <a href="hit.php?pid=<?=$t10[1]?>&tru=<?=$tru?>"><img src="../images/attack.jpg" width="16" height="16" border="0" align="absmiddle"></a>
          <?}?>
	  </font></td>
    </tr>
    <?$selffont="";
       }
}?>



</table>
<br>
<br>
<?=bar($id)?>

<?
GAMEFOOTER();
?>