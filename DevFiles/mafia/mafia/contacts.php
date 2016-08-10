<?
include("html.php");
secureheader();
?>
<p><strong>Bitch List </strong></p>
<table width="300" cellspacing="1">
<?
 $pimp = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE id='$id';"));
 $bitches = mysql_query("SELECT contact FROM $tab[clist] WHERE pimp='$pimp[pimp]' AND type=2 ORDER BY id ASC;");
  while ($bitch = mysql_fetch_array($bitches)){

        if($row==0){$color="#111111";$row++;}
    elseif($row==1){$color="#000000";$row--;}
	//mysql_free_result($bitches);
?>
  <tr bgcolor="<?=$color?>">
    <td><?=$bitch[contact]?></td>
    <td align="right"><a href="pimp.php?pid=<?=$bitch["contact"]?>&tru=<?=$tru?>">Profile</a></td>
  </tr>
  <?}?>
</table>
<p><strong>Contact List </strong></p>
<table width="300" cellspacing="1">
  <?
 $pimp = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE id='$id';"));
 $contacts = mysql_query("SELECT contact FROM $tab[clist] WHERE pimp='$pimp[pimp]' AND type=1 ORDER BY id ASC;");
  while ($contact = mysql_fetch_array($contacts)){

        if($row==0){$color="#111111";$row++;}
    elseif($row==1){$color="#333333";$row--;}
	//mysql_free_result($contacts);
?>
  <tr bgcolor="<?=$color?>">
    <td><?=$contact[contact]?></td>
    <td align="right"><a href="pimp.php?pid=<?=$contact["contact"]?>&tru=<?=$tru?>">Profile</a></td>
  </tr>
<?}?>
</table>
<p><br>
<?
bar();  
gamefooter();
unset($user);
?>
