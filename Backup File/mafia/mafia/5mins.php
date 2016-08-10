<?
include("/home/CPANEL_USER/public_html/funcs.php");

$getgames = mysql_query("SELECT round FROM $tab[game] WHERE starts<$time AND ends>$time ORDER BY round ASC;");
while ($game = mysql_fetch_array($getgames))
{

  if (!fetch("SELECT lastran FROM r$game[0]_$tab[cron] WHERE cronjob='ranks';"))
     { mysql_query("INSERT INTO r$game[0]_$tab[cron] VALUES ('ranks','$time');"); }
else { mysql_query("UPDATE r$game[0]_$tab[cron] SET lastran='$time' WHERE cronjob='ranks'"); }

       //UPGRADE LOCAL RANKS
       $citys = mysql_query("SELECT id FROM r$game[0]_$tab[city] WHERE id>0 ORDER BY id DESC;");
        while ($city = mysql_fetch_array($citys))
              {
              $locals = mysql_query("SELECT id FROM r$game[0]_$tab[pimp] WHERE status!='banned' AND city='$city[0]' ORDER BY networth DESC;");
              $rank = 0;
               while ($local = mysql_fetch_array($locals))
                     {
           	         $rank++;
	                 mysql_query("UPDATE r$game[0]_$tab[pimp] SET rank=$rank WHERE id=$local[0];");
                     }
              }

       //UPGRADE NATIONAL RANKS
       $nations = mysql_query("SELECT id FROM r$game[0]_$tab[pimp] WHERE status!='banned' ORDER BY networth DESC;");
       $urank = 0;
        while ($nation = mysql_fetch_array($nations))
              {
	          $urank++;
	          mysql_query("UPDATE r$game[0]_$tab[pimp] SET nrank=$urank WHERE id=$nation[0];");
              }

       mysql_query("UPDATE r$game[0]_$tab[pimp] SET nrank='99999', rank='99999' WHERE status='banned';");

       //UPGRADE NATIONAL RANKS 0 networth
       //$nationds = mysql_query("SELECT id FROM r$game[0]_$tab[pimp] WHERE networth<='0' ORDER BY networth DESC;");
       //$urankd = 0;
       // while ($nationd = mysql_fetch_array($nationds))
         //     {
	       //   $urankd++;
       //mysql_query("UPDATE r$game[0]_$tab[pimp] SET nrank='99999', rank='99999' WHERE networth<='0';");
         //     }

}
?>