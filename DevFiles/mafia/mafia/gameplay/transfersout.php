<?
include("html.php");

$pimp = mysql_fetch_array(mysql_query("SELECT crew FROM $tab[pimp] WHERE id='$id';"));

mysql_query("UPDATE $tab[pimp] SET msg='0' WHERE id='$id'");
if(!$inbox){$inbox=inbox;}

GAMEHEADER("mailbox");
?>
<br>
  <table width="600">
   <tr>
    <td align="center" width="200"> <a href="mailbox.php?tru=<?=$tru?>"> <font color="#7777CC">messages</font></a> <b>(</b><a href="sentmessages.php?tru=<?=$tru?>"> sent</a><b> )</b></td>
    <td align="center" width="200"> <a href="attacks.php?tru=<?=$tru?>"> attacks</a> <b>(</b><a href="sentattacks.php?tru=<?=$tru?>"> out</a><b> )</b></td>
	    <td align="center" width="200"> <a href="transfers.php?tru=<?=$tru?>"> transfers</a> <b>(</b><a href="transfersout.php?tru=<?=$tru?>"> out</a><b> )</b></td>
		    <td align="center" width="200"><a href="revenges.php?tru=<?=$tru?>">revenges</a></font></td>

    <td align="center" width="200"><a href="invites.php?tru=<?=$tru?>">invites</a></font></td>

   </tr>
  </table>
<table width="100%">
 <tr>
  <td align="center"><b>showing sent transfers<br><br></td>
 </tr>
 <tr>
  <td align="center" width="80%">
<?
$get = mysql_query("SELECT id,src,dest,msg,time,del,crew FROM $tab[mail] WHERE src='$id' AND inbox='transferout' ORDER BY time DESC;")or die("Invalid query: " . mysql_error());
   if((fetch("SELECT COUNT(msg) FROM $tab[mail] WHERE inbox='transferout' AND src='$id';") == 0) && (!$del)){echo"<strong>you didnt send any transfers</strong>";}
else{
    while ($sole = mysql_fetch_array($get))
          {
          $pmp = mysql_fetch_array(mysql_query("SELECT pimp,crew FROM $tab[pimp] WHERE id='$sole[2]';"));
          $crw=mysql_fetch_array(mysql_query("SELECT id,icon FROM $tab[crew] WHERE id='$pmp[1]';"));
          ?>
          <table width="98%" height="100%" cellspacing="0" cellpadding="3">
           <tr bgcolor="#000000">
            <td align="left" valign="middle" bgcolor="#cccccc"><b>sent to</b> <?if($crw[1]){?><a href="family.php?cid=<?=$crw[0]?>&tru=<?=$tru?>"><img border="0" align="absmiddle" src="<?=$crw[1]?>" width="15" height="15"></a> <?}?><a href="mobster.php?pid=<?=$pmp[0]?>&tru=<?=$tru?>"><?=$pmp[0]?></a> <small><b>"<font color="#ffffff"><?=countdown($sole[4])?> ago</font>"</b></small></td>
            <td align="right" valign="middle" bgcolor="#cccccc"><b><a href="out.php?pid=<?=$pmp[0]?>&tru=<?=$tru?>&camefrom=sentmessages.php?tru=<?=$tru?>">reply</a></small></b></td>
           </tr>
           <tr>
            <td colspan="2" bgcolor="#000000"><?=$sole[3]?></td>
           </tr>
      </table>
          <?}
    }
?>

  </td>
 </tr>
</table>
<br>
<?=bar($id)?>
<br>
<?
GAMEFOOTER();
?>


