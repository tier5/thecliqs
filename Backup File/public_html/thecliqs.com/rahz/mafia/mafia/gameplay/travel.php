<?
include("html.php");

$pimp = mysql_fetch_array(mysql_query("SELECT city,whore,thug,lowrider,money,crew,pimp,status,dealers,bootleggers,hustlers,punks,hitmen,bodyguards FROM $tab[pimp] WHERE id='$id';"));
$crew = mysql_fetch_array(mysql_query("SELECT founder FROM $tab[crew] WHERE id='$pimp[5]';"));
$city = mysql_fetch_array(mysql_query("SELECT name FROM $tab[city] WHERE id='$pimp[0]';"));

//$hoe_cost=$pimp[1]*150;
$totalchars = $pimp[1]+$pimp[2]+$pimp[8]+$pimp[9]+$pimp[10]+$pimp[11]+$pimp[12]+$pimp[13];

if($pimp[3]>0)
  {
  $thugs_going=$pimp[3]*5;
  }else{
  $thugs_going = 0;
  }
  
if($thugs_going > $totalchars){ $thugs_going=$totalchars; }
  $thugs_without=$totalchars-$thugs_going;

  if($thugs_without!=0){ $thug_cost=$thugs_without*150; }

  else{ $thug_cost=$totalchars*0; }

$cost=$thug_cost;
$cashleft=fixinput($pimp[4]-$cost);

//DEBUG:
//echo "going $thugs_going || without a ride $thugs_without || going to cost $thug_cost || cars $pimp[3] || planes $pimp[14]";


if($confirm == cancel){header("Location: travel.php?tru=$tru");}

if(($confirm == proceed) && ($pimp[4] >= $cost) && (fetch("SELECT name FROM $tab[city] WHERE id!='$pimp[0]' AND id='$cty';")))
  {
  mysql_query("UPDATE $tab[pimp] SET money='$cashleft', city='$cty' WHERE id='$id'");
  $done='yes';
  }

$ctyafter = mysql_fetch_array(mysql_query("SELECT name FROM $tab[city] WHERE id='$cty';"));

GAMEHEADER("Wanna move?");
?>
<?if($done==yes){?>
<br><font size="+1" color="B0C4DE"><b>welcome to <font color="#7777CC"><?=$ctyafter[0]?></font>!</b></font>
<br><br><a href="index.php?tru=<?=$tru?>"><font color="#000000">go to</font> main menu</a>
<br>
<?}else{?>
<br><font size="5" color="gold"><b><img src="/pics/a_boeing_787-01 copy.jpg" width="50%" height="50%" /><br />
<font color="#000099">Travel Agency</font></b></font>
<br>
<b><font color="#000000">You are currently living in <?=$city[0]?> with <?=commas(fetch("SELECT COUNT(id) FROM $tab[pimp] WHERE city='$pimp[0]';"));?> other mobsters.</font></b>
<br>
<br>
<table width="100%">
 <tr>
  <td align="right" valign="middle">
  <br><b><font color="red">so ya moving? <?=$city[0]?></font>?</b>
  <br>
  It costs <B>$150</B> for each of your guys to move.
  <br>
  <small><b><font color="red">if you have </font></b><font color="red"><strong>S-Class Limos</strong><b>, 5 guys can go per ride.</b></font></small><br>
  <br><font size="3"><b>Pick your location:</b></font>
  <br>
  <?
  $get = mysql_query("SELECT id,name,country FROM $tab[city] WHERE id!='$pimp[0]' ORDER BY id ASC;");
  while ($citys = mysql_fetch_array($get))
        {$pop=fetch("SELECT COUNT(id) FROM $tab[pimp] WHERE city='$citys[0]';");
        ?><a href="?cty=<?=$citys[0]?>&tru=<?=$tru?>"><?=$citys[1]?>, <font color="#000000"><?=$citys[2]?></font><br><small><font color="red">population:</font> <font color="red"><?=commas($pop)?></font></small></a><br><?
        }
  ?>
  </td> 
  <td width="25">&nbsp;</td>
  <td align="left" valign="top" width="400">
  <?
  if(($cty) && ($cashleft >= 0) && (fetch("SELECT name FROM $tab[city] WHERE id!='$pimp[0]' AND id='$cty';")))
    {$cinfo = mysql_fetch_array(mysql_query("SELECT name,country,id FROM $tab[city] WHERE id='$cty';"));
    ?>
    <br>
    <center><b>are you sure you want to move to <?=$cinfo[0]?>?</b>
    <br>
    <a href="?confirm=cancel&tru=<?=$tru?>">no</a> <b>::</b> <a href="?cty=<?=$cty?>&confirm=proceed&tru=<?=$tru?>">yes</a></center>
    <br>
    <small><b>top 10 mafiosos  in <font color="red"><?=$cinfo[0]?></font>, <?=$cinfo[1]?></b></small>
    <br>
<table width="95%" cellspacing="1">
 <tr>
  <td align="center" width="5%"><small>rank</small></td><td width="1"></td><td><SMALL>mafioso</SMALL></td>
  <td align="center"><SMALL>operatives</SMALL></td>
  <td align="center"><SMALL>defensive</SMALL></td>
  <td align="center"><small>worth</small></td>
 </tr>
<?
$get = mysql_query("SELECT id,pimp,whore,thug,networth,online,rank,crew,dealers,bootleggers,hustlers,punks,hitmen,bodyguards FROM $tab[pimp] WHERE city='$cty' ORDER BY rank ASC limit 10;");
while ($t10 = mysql_fetch_array($get))
      {
      $online=$time-$t10[5];
      if ($online < 600){$on="<img src=$site[img]online.gif width=16 height=16 align=absmiddle>";}else{$on='';}

            if($id == $t10[0]){$rankcolor = "#cccccc";$selffont="<font color=red>";}
        elseif($rankstart==0){$rankcolor="#cccccc";$rankstart++;}
        elseif($rankstart==1){$rankcolor="#999999";$rankstart--;}

       $icn = mysql_fetch_array(mysql_query("SELECT icon FROM $tab[crew] WHERE id='$t10[7]';"));
       ?>
       <tr bgcolor="<?=$rankcolor?>">
        <td align="center"><?=$t10[6]?>.</td>
        <td align="center" width="1"><?=$on?></td>
        <td><nobr><?if($icn[0]){?><a href="family.php?cid=<?=$t10[7]?>&tru=<?=$tru?>"><img src="<?=$icn[0]?>" align="absmiddle" width="16" height="16" border="0"></a><?}?> <a href="mobster.php?pid=<?=$t10[1]?>&tru=<?=$tru?>"><?=$selffont?><?=$t10[1]?></font></a></nobr></td>
        <td align="right"><?=commas($t10[2]+$t10[8]+$t10[9]+$t10[10]+$t10[11])?></td>
        <td align="right"><?=commas($t10[3]+$t10[12]+$t10[13])?></td>
        <td align="right">$<?=commas($t10[4])?></td>
       </tr>
       <?$selffont="";}?>
</table>

  <?}else{?>
  you currently have <b><font color="red"><?=$pimp[1]+$pimp[2]+$pimp[8]+$pimp[9]+$pimp[10]+$pimp[11]+$pimp[12]+$pimp[13]?></font> guys </b><?if($pimp[3]>0){?>and  <b><font color="red"><?=$pimp[3]?></font> <br>
  S-Class Limo</b><?}?>.  
  <br>
  <br>you have <b><font color="red">$<?=commas($pimp[4])?></font></b> cash on you now.
  <br>in total, its gonna cost you <b><font color="red">$<?=commas($cost)?></font></b>.
  <br><?if($cashleft < 0){?><b><font color="red">Sorry you are too broke. You have to earn mo money to move to a new city.</font></b><?}else{?><font color="red">which will leave you with <b>$<?=commas($cashleft)?></b></font><?}?>
  <?}?>
  </td>
 </tr>
</table>
<?}?>
<br>
<br>
<?=bar($id)?>
<?
GAMEFOOTER();
?>