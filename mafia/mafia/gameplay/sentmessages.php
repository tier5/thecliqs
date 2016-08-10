<?
require_once("form.php");
require_once("echo_setup.php");

include("html.php");


if(!$inbox){$inbox=inbox;}

$pimp = mysql_fetch_array(mysql_query("SELECT crew FROM $tab[pimp] WHERE id='$id';"));

mysql_query("UPDATE $tab[pimp] SET msg='0' WHERE id='$id'");

GAMEHEADER("mailbox");
?><body>
<br>
<table width="90%">
  <tr>
    <td align="center" ><font size="2"><a href="mailbox.php?tru=<?=$tru?>">messages</a> <b>(</b><a href="sentmessages.php?tru=<?=$tru?>">sent</a><b>)</b></font></td>
    <td align="center" ><font size="2"><a href="attacks.php?tru=<?=$tru?>">attacks</a> <b>(</b><a href="sentattacks.php?tru=<?=$tru?>">out</a><b>)</b></font></td>
    <td align="center" ><font size="2"><a href="transfers.php?tru=<?=$tru?>">transfers</a> <b>(</b><a href="senttransfers.php?tru=<?=$tru?>">sent</a><b>)</b></font></td>
    <td align="center" ><font size="2"><a href="invites.php?tru=<?=$tru?>">invites</a></font></td>
    <td align="center" ><a href="revenges.php?tru=<?=$tru?>">Revenges</a></td>
  </tr>
  <tr>
    <td align="center" colspan="5"><b><font color="#FFFFFF">s<font size="2">howing messages &nbsp;</font></font> <font size="2"> </font></b> <font size="2"><br>
    </font></td>
  </tr>
</table>
<?
#start pagination
$rows = $db1->createRecordset("SELECT id,src,dest,msg,time,del,crew FROM $tab[mail] WHERE src='$id' AND inbox='$inbox' ORDER BY time DESC");

$page = form_int("page", 1);
#Determine the pagination
$pag = new Pagination($rows, "&tru=".$tru, 30);
$pag->current_page = $page;
$pagination  = $pag->Process();	
if ($pagination['total_rows'] > 0) {
    pagination_show($pagination); 
}
?>

<table width="90%">
 <tr>
  <td align="center" width="80%">
<?
foreach ($rows as $sole):
          $pmp = mysql_fetch_array(mysql_query("SELECT pimp,crew FROM $tab[pimp] WHERE id='$sole[2]';"));
          $crw=mysql_fetch_array(mysql_query("SELECT id,icon FROM $tab[crew] WHERE id='$pmp[1]';"));
          ?>
          <table width="98%" height="100%" border="1" cellpadding="3" cellspacing="0" bordercolor="#FFFFFF">
           <tr bgcolor="#333333">
            <td align="left" valign="middle" bgcolor="#cccccc"><font size="2"><b><font color="#FF0000">sent to</font></b> 
            <?if($crw[1]){?>
                <a href="family.php?cid=<?=$crw[0]?>&tru=<?=$tru?>"><img border="0" align="absmiddle" src="<?=$crw[1]?>" width="15" height="15"></a> 
            <?}?>
                <a href="mobster.php?pid=<?=$pmp[0]?>&tru=<?=$tru?>">
                <?=$pmp[0]?>
                </a> <small><font color="#FFFFFF"><b>" 
                <?=countdown($sole[4])?>
             ago"</b></font></small></font></td>
            <td align="right" valign="middle" bgcolor="#cccccc"><font size="2"><b><a href="out.php?pid=<?=$pmp[0]?>&tru=<?=$tru?>&camefrom=sentmessages.php?tru=<?=$tru?>">send message</a></small></b></font></td>
           </tr>
           <tr bgcolor="#666666">
            <td colspan="2" bgcolor="#cccccc"><font color="#000000" size="2"><?=$sole[3]?></font></td>
           </tr>
      </table>
          <br>
          <br>
          <?
endforeach;
?>

  </td>
 </tr>
</table>
<? 
if ($pagination['total_rows'] > 0) {
    pagination_show($pagination); 
} 
?>
<br>
<?=bar($id)?>
<br>
<?
GAMEFOOTER();
?>