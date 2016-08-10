<?
include("html.php");

$result = mysql_query("SELECT id,pimp,whore,thug,networth,online,city,rank,nrank,crew FROM $tab[pimp] WHERE id>0 AND status='normal' ORDER BY nrank ASC");
$rows_per_page = 30;
$screen = $_REQUEST['screen'];
$total_records = mysql_num_rows($result);
$pages = ceil($total_records / $rows_per_page);
GAMEHEADER("Free Ranks");
?>

<form method="post" action="franks.php?tru=<?=$tru?>">
<table width="98%" cellspacing="1" align="center">
  <div align="center"><br>
      <b><font size="+1">Free Player Ranks</font></b>
    <br>
    <br>
    <br>
  </div>
  <tr>
  <td align="center" width="36"><small>rank</small></td>
  <td width="22"></td>
  <td width="219"><B>Mafioso</B></td>
  <td width="210" align="center"><B>Operatives</B></td>
  <td width="234" align="center"><B>Defensive</B></td>
  <td width="227" align="center"><B><strong>Networth</strong><B></td>
 </tr>

<?
mysql_free_result($result);
if (!isset($screen))

  $screen = 0;
$start = $screen * $rows_per_page;
$sql = "SELECT id,pimp,whore,thug,networth,online,city,rank,nrank,crew,dealers,bootleggers,hustlers,punks,hitmen,bodyguards FROM $tab[pimp] ";


$sql .= "WHERE id>0 AND status='normal' ORDER BY nrank ASC LIMIT $start, $rows_per_page";$result = mysql_query($sql);

while ($row = mysql_fetch_array($result, MYSQL_NUM)) {

$online=$time-$row[5];


        if ($online < 600){$on="<img src=../images/online.gif width=16 height=16 align=absmiddle>";}else{$on='';}

        if($id == $t10[0]){$rankcolor = "#ff0000";}
        elseif($rankstart==0){$rankcolor="#cccccc";$rankstart++;}
        elseif($rankstart==1){$rankcolor="#999999";$rankstart--;}

        $icn = mysql_fetch_array(mysql_query("SELECT icon FROM $tab[crew] WHERE id='$row[9]';"));
       $nw = commas($row[4]);
                $th = commas($row[2]+$row[10]+$row[11]+$row[12]+$row[13]);
                $tt = commas($row[3]+$row[14]+$row[15]);

        if($icn[0] != ''){$ci="<a href=\"family.php?cid=$row[9]&tru=$tru\"><img src=\"$icn[0]\" align=\"absmiddle\" width=\"16\" height=\"16\" border=\"0\"></a>";}else{$ci='';}

echo "<tr bgcolor=\"$rankcolor\">
 <td align=\"center\" width=\"16\">$row[8]</td>
<td align=\"center\" width=\"16\">$on</td>
<td>$ci <a href=\"mobster.php?pid=$row[1]&tru=$tru\">$row[1]</a></td>
<td align=\"right\">$th</td>
<td align=\"right\">$tt</td>
<td align=\"right\">$nw</td>
</tr>";


}
echo "<br>\n";

if ($screen > 0)
{

  $previous = $screen-1;
echo "<a href=\"franks.php?tru=$tru&screen=$previous\">Previous</a>\n";

}


// page numbering links now
for ($i = 0; $i < $pages; $i++)
{

$url = "franks.php?tru=$tru&screen=" . $i;
echo " <a href=\"$url\">".$i=$i+1 ."</a> ";

}


if ($screen < $pages)
{

  $next = $screen + 1;
echo "<a href=\"franks.php?tru=$tru&screen=$next\">Next</a>\n";

}
?>
</table>
<br><br>


<?
GAMEFOOTER();
?>