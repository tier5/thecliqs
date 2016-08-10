<?
include("setup.php");
//$tab[site] = 'site_details';
$get_sited = mysql_fetch_array(mysql_query("SELECT layout FROM $tab[site];"));

$layout = $get_sited[0];

include("$layout");
?>