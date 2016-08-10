<?

include("/home/CPANEL_USER/public_html/funcs.php");



$getgames = mysql_query("SELECT round,attin,attout,attindown,attoutdown FROM $tab[game] WHERE starts<$time AND ends>$time ORDER BY round ASC;");

while ($game = mysql_fetch_array($getgames))

{

  if (!fetch("SELECT lastran FROM r$game[0]_$tab[cron] WHERE cronjob='cranks';"))

     { mysql_query("INSERT INTO r$game[0]_$tab[cron] VALUES ('cranks','$time');"); }

else { mysql_query("UPDATE r$game[0]_$tab[cron] SET lastran='$time' WHERE cronjob='cranks'"); }



$downin = $game[1]-1;



mysql_query("UPDATE r$game[0]_$tab[pimp] SET attin='$downin' WHERE id>0 AND attin>'$game[1]';");

mysql_query("UPDATE r$game[0]_$tab[pimp] SET attin='0' WHERE id>0 AND attin<='$game[1]';");

mysql_query("UPDATE r$game[0]_$tab[pimp] SET attout=attout-$game[4] WHERE id>0 AND attout>'$game[4]';");

mysql_query("UPDATE r$game[0]_$tab[pimp] SET attout='0' WHERE id>0 AND attout<='$game[4]';");

mysql_query("UPDATE r$game[0]_$tab[pimp] SET attout='0' WHERE id>0 AND attout>'1000000';");



//Start online bonus
$time = time();
$thistime = $time-300;
//Give bonus to online TJ
mysql_query("UPDATE r$game[0]_$tab[pimp] SET res=res+500 WHERE online>='$thistime';");
//end update





$getcrewranks = mysql_query("SELECT id FROM r$game[0]_$tab[crew] WHERE id>0 ORDER BY networth DESC;");

$urank = 0;

while ($crws = mysql_fetch_array($getcrewranks))

      {

	  $urank++;

	  mysql_query("UPDATE r$game[0]_$tab[crew] SET rank=$urank WHERE id='$crws[0]';");

      }

}

?>