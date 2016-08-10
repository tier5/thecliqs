<?
include("/home/YOUR_CPANEL_USERNAME_HERE/public_html/setup.php");

$getgames = mysql_query("SELECT id,subscribe,subexpires FROM $tab[user] WHERE id>0;");
while ($game = mysql_fetch_array($getgames))
{
if($game[2] > 0)
{
mysql_query("UPDATE $tab[user] SET subexpires=subexpires-1 WHERE id=$game[id]"); 
}
if($game[2] == 0)
{
mysql_query("UPDATE $tab[user] SET subscribe=0 WHERE id=$game[id]"); 
}
}

?>