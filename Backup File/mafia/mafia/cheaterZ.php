<?
include("html.php");
admin();
siteheader();

$current = strtotime(now);

$sqlquery = "SELECT ip, count(*) as tot FROM users GROUP BY ip HAVING tot > 1 ORDER BY ip";
$rs = mysql_query($sqlquery);
$num = mysql_numrows($rs);
$f = 0;

while ($num > $f) {
$ip = mysql_result($rs,$f,"ip");




//$rid = "r".$round."_pimp";
//$cid = "r".$round."_crew";
$sqlquery = "SELECT id,username,host,ip,online FROM users where ip = '$ip' order by online";
$result = mysql_query($sqlquery);
$num2 = mysql_numrows($result);
$g = 0;
?>
<div align="center">
<table border="1" width="350" cellspacing="0" cellpadding="2">
<tr><th align="center">ID</th><th align="center">Username</th><th align="center">Host</th><th align="center">IP</th><th align="center">Last Online</th></tr>
<?
while ($num2 > $g) {
if ($x == 1){
$bgcolor = "#FF00FF";
$x = 0;
}else{
$bgcolor = "#CCCCCC";
$x = 1;
}
$lo = mysql_result($result,$g,"online");
$lastonline = date('Y-m-d g:i:s',$lo);
$pimp = mysql_result($result,$g,"username");
$host = mysql_result($result,$g,"host");
$id = mysql_result($result,$g,"id");
$ip = mysql_result($result,$g,"ip");
$t = $g + 1;
echo "<tr><td><font face=tahoma size=2>".$id."</font></td><td><font face=tahoma size=2>".$pimp."</font></td><td align=right><font face=tahoma size=1><nobr>".$host."</nobr></font></td><td align=right><font face=tahoma size=2>".$ip."</font></td><td align=right><font face=tahoma size=2><nobr>".$lastonline."</nobr></td></tr>";
	//echo $result;
	
	
$g++;
}
?>
</table></div><br><BR>
<?


$f++;
}
?>
<?
sitefooter();
?>