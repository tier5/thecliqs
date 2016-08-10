<?
require_once("echo_setup.php");
include("html.php");


if (!$citys){
    $cities = $db1->getRow("SELECT id,name FROM $tab[city] LIMIT 1");
	$citys = $cities['id'];
}

GAMEHEADER("whos online");
?><body>
<div align="center"><font size="2"><br>
      <b>Who's Online?<br>
       <? /*<form action="?citys=<?=$cityc?>&tru=<?=$tru?>" method="post">*/?>
      </b>Choose a city,<b>
      <select onChange="MM_jumpMenu('parent',this,0,this.options[this.selectedIndex].value,'_main','toolbar=yes,location=yes,status=yes,resizable=yes,scrollbars=yes')" name="citys">
        <option selected>
  <? $citydb = $tab[city];
  $get = mysql_query("SELECT id,name FROM $citydb ORDER BY id ASC;");

    while ($city = mysql_fetch_array($get))

    {?>
	<? $checkk=$time-600;
$incity = fetch("SELECT COUNT(id) FROM $tab[pimp] WHERE city=$city[0];");
$totalplayas = fetch("SELECT COUNT(id) FROM $tab[pimp] WHERE id>'0';");
$totalonline = fetch("SELECT COUNT(id) FROM $tab[pimp] WHERE online>$checkk AND city='$city[0]';");
?>
        <option value="whosonline.php?citys=<?=$city[0]?>&tru=<?=$tru?>" <? if($city[0]=="$citys"){echo"selected";}?>>
             <?=$city[1]?> &nbsp;&nbsp;  online: <?=commas($totalonline)?>&nbsp;&nbsp;
        </option>
         <?}?>
      </select>
       </form>
      </b><br>
  </font><br>
<?

$check=$time-600;

#start pagination
$rows = $db1->createRecordset("SELECT id,pimp,online,city,rank,nrank,crew,networth,status FROM $tab[pimp] WHERE online>$check AND city='$citys' ORDER BY online DESC");
$page = form_int("page", 1);
#Determine the pagination
$pag = new Pagination($rows, "&tru=".$tru."&citys=".$citys, 30);
$pag->current_page = $page;
$pagination  = $pag->Process();	

if ($pagination['total_rows'] > 0) {
    pagination_show($pagination);
}
?>
  
  <table width="500" cellspacing="0">
    <?

$getcities = mysql_query("SELECT id,name FROM $tab[city] WHERE id=$citys;");
while ($city = mysql_fetch_array($getcities))
{

if(fetch("SELECT pimp FROM $tab[pimp] WHERE online>$check AND city='$citys';"))
  {
  
  $totalonline = fetch("SELECT COUNT(id) FROM $tab[pimp] WHERE online>$check AND city='$citys';");
?>
    <tr>
      <td colspan="2" align="left"><font color="red" size="2">Boss's  in <?=$city[1]?> &nbsp;&nbsp; With <?=commas($totalonline)?> online.</font></td>
      <td colspan="2" align="right"><font color="red" size="2">Worth</font></td>
    </tr>
    <? }

foreach ($rows as $on):
          if($rankstart==0){$rankcolor="#cccccc";$rankstart++;}
      elseif($rankstart==1){$rankcolor="#999999";$rankstart--;}

      $icn = mysql_fetch_array(mysql_query("SELECT icon FROM $tab[crew] WHERE id='$on[6]';"));
      ?><tr onMouseOver="style.backgroundColor='#0000ff'" onMouseOut="style.backgroundColor='<?=$rankcolor?>'" bgcolor="<?=$rankcolor?>">
        <td align="left">
        <? if($icn[0]){?><a href="family.php?cid=<?=$on[6]?>&tru=<?=$tru?>"><img src="<?=$icn[0]?>" align="absmiddle" width="16" height="16" border="0"></a><? }?> 
		<a href="mobster.php?pid=<?=$on[1]?>&tru=<?=$tru?>"><?=$on[1]?></a> &nbsp; <font color="#666666">idle for <?=countdown($on[2])?></font></td>
        <td align="right"><font size="2">$<?=commas($on[7])?></font></td>
    </tr><?
endforeach; 

}?>
  </table>
  
<?
if ($pagination['total_rows'] > 0) {
    pagination_show($pagination);
}
?>
  </font></div>  
<br>
  <?=bar($id)?>
  <br>
  <?
GAMEFOOTER();
?>