<?
include("html.php");

$pimp = mysql_fetch_array(mysql_query("SELECT pimp,id FROM $tab[pimp] WHERE id='$pid';"));
if(!$inbox){$inbox=inbox;}
mysql_query("UPDATE $tab[pimp] SET page='view sent mail box' WHERE id='$id'");
ADMINHEADER("viewing $pimp[0]'s $inbox");
?><body bgcolor="#CCCCCC" text="#000000">
<table width="100%" height="100%" bgcolor="#cccccc">
 <tr>
  <td align="center" valign="top">
  <b>viewing
  <br><font size="+1"><font color="#7777CC"><?=$pimp[0]?>'s</font> mailbox</font>
  <br><small><a href="viewmailbox.php?pid=<?=$pimp[1]?>&tru=<?=$tru?>">messages</a> (sent) &nbsp; &middot; &nbsp; <a href="viewattacks.php?pid=<?=$pimp[1]?>&tru=<?=$tru?>">attacks</a> (<a href="viewsentattacks.php?pid=<?=$pimp[1]?>&tru=<?=$tru?>">out</a>)</small></b>

  <table width="95%" cellspacing="1">

<?
$get = mysql_query("SELECT id,src,dest,msg,time,del,crew FROM $tab[mail] WHERE src='$pid' AND inbox='$inbox' ORDER BY time DESC;")or die("Invalid query: " . mysql_error());
   if((fetch("SELECT COUNT(msg) FROM $tab[mail] WHERE inbox='$inbox' AND src='$pid';") == 0) && (!$del)){echo"<strong>you didnt send any messages</strong>";}
else{
    while ($sole = mysql_fetch_array($get))
          {
          $pmp = mysql_fetch_array(mysql_query("SELECT pimp,crew FROM $tab[pimp] WHERE id='$sole[2]';"));
          $crw=mysql_fetch_array(mysql_query("SELECT id,icon FROM $tab[crew] WHERE id='$pmp[1]';"));
          ?>
           <tr bgcolor="#000000">
             <td align="left" valign="middle" bgcolor="#cccccc"><small><b>sent to</b> <?if($crw[1]){?><a href="family.php?cid=<?=$crw[0]?>&tru=<?=$tru?>"><img border="0" align="absmiddle" src="<?=$crw[1]?>" width="15" height="15"></a> <?}?><a href="mobster.php?pid=<?=$pmp[0]?>&tru=<?=$tru?>"><?=$pmp[0]?></a> <b>"<font color="#7777cc"><?=countdown($sole[4])?> ago</font>"</b></small></td>
             <td align="right" valign="middle" bgcolor="#cccccc"><b><small><a href="out.php?pid=<?=$pmp[0]?>&tru=<?=$tru?>&camefrom=sentmessages.php?tru=<?=$tru?>">send message</a></small></b></td>
           </tr>
           <tr>
             <td colspan="2" bgcolor="#cccccc"><font color="#000000">
             <?=$sole[3]?>
             </font></td>
           </tr>
          <?}
    }
?>

  </table>

  </td>
 </tr>
</table>

