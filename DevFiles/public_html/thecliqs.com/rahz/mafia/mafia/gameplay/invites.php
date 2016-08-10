<?
require_once("form.php");
require_once("echo_setup.php");

include("html.php");

$page = form_int("page", 1);

if (form_isset("submitted_all")) {
    mysql_query("UPDATE $tab[mail] SET del='yes' WHERE dest='$id' AND inbox='invite' AND del='no'");
}

if (form_isset("submitted")) {
	$del_invites = array();
	foreach ($_POST as $k => $value):
		if (is_integer(strpos($k, "delinv")) && $value == "on") {
		    $del_invites[] = substr($k, 6);
		}
	endforeach;
	
	if (sizeof($del_invites)) {
	    foreach ($del_invites as $inv_id):
			 $db1->doSql("UPDATE $tab[mail] SET del='yes' WHERE id='$inv_id'");
		endforeach;
	}
}



$pimp = mysql_fetch_array(mysql_query("SELECT crew FROM $tab[pimp] WHERE id='$id';"));

mysql_query("UPDATE $tab[pimp] SET ivt='0' WHERE id='$id'");


$row = $db1->getRow("SELECT COUNT(id) as ct FROM $tab[mail] WHERE dest='$id' AND inbox='invite' AND del='no'");
$count = $row['ct'];

GAMEHEADER("mailbox");
?><body>
<font size="2"><br>
  </font>
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
<?if($count > 0){?>

<form name="frmDelete" method="post" action="invites.php?tru=<?= $tru ?>">
	<input type="hidden" name="page" id="page" value="<?= $page ?>" />
	<input type="hidden" name="submitted_all" id="submitted_all" value="1" />
	<input type="submit" value="Delete all" />
</form>	
<?}?>
<form name="frmDelete" method="post" action="invites.php?tru=<?= $tru ?>">
<input type="hidden" name="page" id="page" value="<?= $page ?>" />
<input type="hidden" name="submitted" id="submitted" value="1" />  
 <?if($count > 0){?>
    <input type="submit" value="Delete selected messages" /> 
<?}?>

<?
#start pagination
$rows = $db1->createRecordset("SELECT id,src,dest,msg,time,del,crew FROM $tab[mail] WHERE dest='$id' AND inbox='invite' AND del='no' ORDER BY time DESC");

#Determine the pagination
$pag = new Pagination($rows, "&tru=".$tru, 30);
$pag->current_page = $page;
$pagination  = $pag->Process();	
if ($pagination['total_rows'] > 0) {
    pagination_show($pagination); 
}
?>

<br />
<table width="90%">
 
 <tr>
  <td align="center" width="80%">
          <font size="2">
          <?
       if($del==all)
         {
          mysql_query("UPDATE $tab[invite] SET cancelled='yes' WHERE pimp='$id'");
          mysql_query("UPDATE $tab[mail] SET del='yes' WHERE inbox='invite' AND dest='$id' AND del='no'");
        ?>
          <b><font color="#7777CC">all invites has been cancelled.</font></b>
          <?
         }
   elseif($del > 0){
          mysql_query("UPDATE $tab[invite] SET cancelled='yes' WHERE pimp='$id' AND crew='$cid'");
          mysql_query("UPDATE $tab[mail] SET del='yes' WHERE inbox='invite' AND dest='$id' AND del='no' AND id='$del'");
          ?>
          <b><font color="#7777CC">invition has been cancelled.</font></b>
 <?}
foreach ($rows as $sole):
          $pmp = mysql_fetch_array(mysql_query("SELECT pimp,crew FROM $tab[pimp] WHERE id='$sole[1]';"));
          $crw=mysql_fetch_array(mysql_query("SELECT id,icon,name FROM $tab[crew] WHERE id='$pmp[1]';"));
          ?>
          </font>
          <table width="98%" height="100%" border="1" cellpadding="3" cellspacing="0" bordercolor="#FFFFFF">
           <tr bgcolor="#666666">
            <td align="left" valign="middle" bgcolor="#999999"><input type="checkbox" name="delinv<?= $sole['id'] ?>" />&nbsp;<font color="#FF0000">&nbsp;<font size="2"><b>received from</b></font></font><font size="2"> 
            <?if($crw[1]){?>
                <a href="family.php?cid=<?=$crw[0]?>&tru=<?=$tru?>"><img border="0" align="absmiddle" src="<?=$crw[1]?>" width="15" height="15"></a> 
            <?}?>
                <a href="mobster.php?pid=<?=$pmp[0]?>&tru=<?=$tru?>">
                <?=$pmp[0]?>
                </a> <small><font color="#ff0000"><b>"
                <?=countdown($sole[4])?>
             ago"</b></font></small></font></td>
            <td align="right" valign="middle" bgcolor="#999999"><font size="2"><b><a href="out.php?pid=<?=$pmp[0]?>&tru=<?=$tru?>&camefrom=invites.php?tru=<?=$tru?>">reply</a> :: <a href="pimpgods.php?tru=<?=$tru?>">report</a></small></b></font></td>
           </tr>
           <tr bgcolor="999999">
            <td colspan="2" bgcolor="#999999"><font size="2">
            <?=$sole[3]?>
            </font></td>
           </tr>
      </table>
          <font size="2"><br>
          <br>
          <?
endforeach;		  
?>
          </font></td>
 </tr>
</table>
<? 
if ($pagination['total_rows'] > 0) {
    pagination_show($pagination); 
} 
?>
<font size="2"><br>
<?=bar($id)?>
<br>
<?
GAMEFOOTER();
?>
</font>