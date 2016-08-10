<?
include("html.php");

siteheader();

if($r){$prev =$r+5;$next=$r-5;}else{$r=0;}
$prev = $r - 5;
$next = $r + 5;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>[<a href="newsandupdates.php?r=<?=$prev?>">prev</a>]</td><td align="right">[<a href="newsandupdates.php?r=<?=$next?>">next</a>]</td>
  </tr>
</table>
<br />
 <?  

    $getnews = mysql_query("SELECT id,news,posted,subject FROM $tab[news] WHERE id>0 ORDER BY posted DESC limit $r,5;");  

    while ($news = mysql_fetch_array($getnews)){?>
<div align="center">
    <table width="450" cellpadding="5">

     <tr>

      <td align="center" class="border"><u><strong><?if($news[3] == ""){?><i>No subject</i><?}else{?><?=$news[3]?><?}?></strong></u></td>

     </tr>

     <tr>

      <td class="maintxt"><small><?=$news[1]?></small><br /><br /><strong><?=date('F j, Y g:i a', $news[2])?>
        <br />
        <br />
      </strong></td>

     </tr>

</table>
</div>
    <br>

    <?}?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>[<a href="newsandupdates.php?r=<?=$prev?>">prev</a>]</td><td align="right">[<a href="newsandupdates.php?r=<?=$next?>">next</a>]</td>
  </tr>
</table>
<? sitefooter(); ?>