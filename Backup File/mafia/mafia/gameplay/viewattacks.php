<?
include("html.php");
mysql_query("UPDATE $tab[pimp] SET page='view attacks' WHERE id='$id'");
$pimp = mysql_fetch_array(mysql_query("SELECT pimp,id FROM $tab[pimp] WHERE id='$pid';"));
if(!$inbox){$inbox=inbox;}

ADMINHEADER("viewing $pimp[0]'s $inbox");
?><body text="#FFFFFF" link="#FFFFFF">
<table width="100%" height="100%">
 <tr>
  <td align="center" valign="top">
  <b>viewing
  <br>
  <font size="+2"><font color="#7777CC"><?=$pimp[0]?>'s</font> mailbox</font>
  <br><small><a href="viewmailbox.php?pid=<?=$pimp[1]?>&tru=<?=$tru?>">messages</a></small>(<a href="viewmailboxsent.php?pid=<?=$pimp[1]?>&tru=<?=$tru?>">sent</a>) &nbsp; &bull; &nbsp; attacks (<a href="viewsentattacks.php?pid=<?=$pimp[1]?>&tru=<?=$tru?>">out</a>)</b>

  <table width="95%" cellspacing="1">

<?
$get = mysql_query("SELECT id,src,dest,msg,time,del,crew FROM $tab[mail] WHERE dest='$pid' AND inbox='attacks' AND del='no' ORDER BY time DESC;")or die("Invalid query: " . mysql_error());
   if((fetch("SELECT COUNT(msg) FROM $tab[mail] WHERE inbox='attacks' AND del='no' AND dest='$pid';") == 0) && (!$del)){echo"<strong>you have no new attacks</strong>";}
else{
    while ($sole = mysql_fetch_array($get))
          {
          $pmp = mysql_fetch_array(mysql_query("SELECT pimp,crew FROM $tab[pimp] WHERE id='$sole[1]';"));
          $crw=mysql_fetch_array(mysql_query("SELECT id,icon FROM $tab[crew] WHERE id='$pmp[1]';"));
          ?>
           <tr bgcolor="#000000">
             <td align="left" valign="middle" bgcolor="#cccccc"><small><b>received from</b> <?if($crw[1]){?><a href="family.php?cid=<?=$crw[0]?>&tru=<?=$tru?>"><img border="0" align="absmiddle" src="<?=$crw[1]?>" width="15" height="15"></a> <?}?><a href="mobster.php?pid=<?=$pmp[0]?>&tru=<?=$tru?>"><?=$pmp[0]?></a> <b>"<font color="#7777CC"><?=countdown($sole[4])?> ago</font>"</b></small></td>
             <td align="right" valign="middle" bgcolor="#cccccc"><b><small><a href="out.php?pid=<?=$pmp[0]?>&tru=<?=$tru?>&camefrom=sentmessages.php?tru=<?=$tru?>">send message</a></small></b></td>
           </tr>
           <tr>
             <td colspan="2" bgcolor="#cccccc"><font color="#000000"><?=$sole[3]?></font></td>
           </tr>
		   <tr><td>&nbsp;</td></tr>
          <?}
    }
?>

  </table>

  </td>
 </tr>
</table>
