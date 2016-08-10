<?
require_once("echo_setup.php");
include("html.php");


GAMEHEADER("Top DU Killers");
?><body>
<div align="center">MOST DU KILLS ( Top 25 )<br>
  <br>

<table width="500" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td >mafia boss </td>
  <td align="right">DU Kills</td>
</tr>
<? $get = mysql_query("SELECT id,pimp,whore,thug,networth,online,city,crew,lowrider,whorek,thugk FROM $tab[pimp] WHERE thugk>0 ORDER BY thugk DESC Limit 25;");
while ($t10 = mysql_fetch_array($get))
      {

      $kills=$t10[11]+$t10[12];
	  $online=$time-$t10[5];
      if ($online < 600){$on="<img src=../images/online1.gif width=16 height=16 align=absmiddle>";}else{$on='';}

             if($id == $t10[0]){$rankcolor = "#cccccc";}
        elseif($rankstart==0){$rankcolor="#cccccc";;$rankstart++;}
        elseif($rankstart==1){$rankcolor="#999999";$rankstart--;}
       ?>
    <tr onMouseOver="style.backgroundColor='#0099FF'" onMouseOut="style.backgroundColor='<?=$rankcolor?>'" bgcolor="<?=$rankcolor?>">
      <td> <font size="2"><nobr>
          <a href="mobster.php?pid=<?=$t10[1]?>&tru=<?=$tru?>">
          <?=$t10[pimp]?>
          </a> </nobr> </font></td>
		      
	  <td align="right"><font size="2"> 
	    <?=commas($t10[10])?>
	  </font> </td>
    </tr>
	<? }?>
  </table> 
</div><br>
<br>
<?=bar($id)?>
<br>
<?
GAMEFOOTER();
?>