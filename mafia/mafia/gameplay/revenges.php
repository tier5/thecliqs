<?
include("html.php");

$pimp = mysql_fetch_array(mysql_query("SELECT crew FROM $tab[pimp] WHERE id='$id';"));

mysql_query("UPDATE $tab[pimp] SET atk='0' WHERE id='$id'");

GAMEHEADER("mailbox");
?>
<br>
  <table width="600">
   <tr>
    <td align="center"> <a href="mailbox.php?tru=<?=$tru?>"> <font color="#7777CC">messages</font></a> <b>(</b><a href="sentmessages.php?tru=<?=$tru?>"> sent</a><b> )</b></td>
    <td align="center"> <a href="attacks.php?tru=<?=$tru?>"> attacks</a> <b>(</b><a href="sentattacks.php?tru=<?=$tru?>"> out</a><b> )</b></td>
	<td align="center"> <a href="transfers.php?tru=<?=$tru?>"> transfers</a> <b>(</b><a href="transfersout.php?tru=<?=$tru?>"> out</a><b> )</b></td>
	<td align="center"><a href="revenges.php?tru=<?=$tru?>">revenges</a></font></td>
    <td align="center"><a href="invites.php?tru=<?=$tru?>">invites</a></font></td>

   </tr>
  </table>
<table width="100%">
 <tr>
  <td align="center"><b>showing revenges</b>
  <br>

  </td>
 </tr>
 <tr>
  <td align="center" width="80%">
  <br>
<? 
$get = mysql_query("SELECT id,src,dest,msg,time,del,crew FROM $tab[mail] WHERE dest='$id' AND inbox='attacks' AND del='no' ORDER BY time DESC;")or die("Invalid query: " . mysql_error());
   if((fetch("SELECT COUNT(msg) FROM $tab[mail] WHERE inbox='attacks' AND del='no' AND dest='$id';") == 0) && (!$del)){echo"<strong>you have no new attacks</strong>";}
else{
    while ($sole = mysql_fetch_array($get))
          {
          $pmp = mysql_fetch_array(mysql_query("SELECT pimp,crew FROM $tab[pimp] WHERE id='$sole[1]';"));
          $crw=mysql_fetch_array(mysql_query("SELECT id,icon,name FROM $tab[crew] WHERE id='$pmp[1]';"));
          ?>
          <table width="98%" height="100%" cellspacing="0" cellpadding="3">
           <tr bgcolor="#cccccc">
            <td>
            <table width="100%" cellspacing="0" cellpadding="0">
             <tr>
              <td valign="middle" align="left">attacked by <?if($crw[1]){?><a href="family.php?cid=<?=$crw[0]?>&tru=<?=$tru?>"><img border="0" align="absmiddle" src="<?=$crw[1]?>" width="15" height="15"></a> <?}?><a href="mobster.php?pid=<?=$pmp[0]?>&tru=<?=$tru?>"><?=$pmp[0]?></a> revenge expires in "<font color="#3366FF"><?=countdown($sole[4])?> ago</font>"</td>
              <td valign="middle" align="right"><b><a href="hit.php?pid=<?=$pmp[0]?>&tru=<?=$tru?>&revenge=1">attack back</a></td>             
			  </tr>
            </table>
            </td>
           </tr>
           <tr>
            <td bgcolor="#cccccc" ><?=$sole[3]?></td>
           </tr>
          </table>
          <br>
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