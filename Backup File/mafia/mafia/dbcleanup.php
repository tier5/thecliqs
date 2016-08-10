<?
include("html.php");
admin();
secureheader();
siteheader();

//Cleans up all previous rounds uneeded database tables


	$fetch_rounds = mysql_query("SELECT * FROM games WHERE ends < '". time() ."' AND cleanup = '". no ."'");

	$txt='';

	
	while($round = mysql_fetch_array($fetch_rounds))
	{
		mysql_query("UPDATE games SET cleanup = '". yes ."' WHERE round = '". $round[round] ."'");
		$txt.= "<font color=red>Start Process</font><br>";
		$txt.= "Cleaning up Round #".$round[round]."<br>";

        $tab[blackmarket] = "r".$round[round]."_blackmarket";	
		$tab[city] = "r".$round[round]."_city";
        $tab[clist] = "r".$round[round]."_contacts";
		$tab[contracts] = "r".$round[round]."_contracts";
		$tab[board] = "r".$round[round]."_board";
		$tab[cron] = "r".$round[round]."_cronjobs";
		$tab[invite] = "r".$round[round]."_invites";
        $tab[mail] = "r".$round[round]."_mailbox";
        $tab[money] = "r".$round[round]."_money_transfer";


mysql_query("DROP TABLE $tab[blackmarket]");
		$txt.= "Dropped $tab[blackmarket]<br>";

mysql_query("DROP TABLE $tab[city]");
		$txt.= "Dropped $tab[city]<br>";

mysql_query("DROP TABLE $tab[contracts]");
		$txt.= "Dropped $tab[contracts]<br>";

mysql_query("DROP TABLE $tab[clist]");
		$txt.= "Dropped $tab[clist]<br>";
		
mysql_query("DROP TABLE $tab[board]");
		$txt.= "Dropped $tab[board]<br>";

mysql_query("DROP TABLE $tab[cron]");
		$txt.= "Dropped $tab[cron]<br>";

mysql_query("DROP TABLE $tab[invite]");
		$txt.= "Dropped $tab[invite]<br>";

mysql_query("DROP TABLE $tab[mail]");
		$txt.= "Dropped $tab[mail]<br>";

mysql_query("DROP TABLE $tab[money]");
		$txt.= "Dropped $tab[money]<br>";

	
		$txt.= "<font color=red>Process Complete</font><br><br>";
}
		

	echo $txt;

?>

<br><br><center>
If nothing lists here, it means that all rounds have been cleaned up<br><br>
</center>

<? sitefooter(); ?>