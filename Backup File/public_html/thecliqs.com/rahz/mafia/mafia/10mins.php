<?
include("/home/CPANEL_USER/public_html/setup.php");

		function fetch($query)
		{
				$data = @mysql_fetch_row(mysql_query($query));
				return $data[0];
		}
		
$getgames = mysql_query("SELECT round,speed,maxbuild FROM $tab[game] WHERE starts<$time AND ends>$time ORDER BY round ASC;");
while ($game = mysql_fetch_array($getgames))
{
$tab_user = 'r'.$game[0].'_'.$tab[pimp];
$tab_cron = 'r'.$game[0].'_'.$tab[cron];

$subscribemax1=3500;
$subscribemax2=4500;
$subscribemax3=6500;

mysql_query("UPDATE r$game[0]_$tab[pimp] SET trn=trn+$game[1] WHERE trn<$game[2] AND subscribe=0;");

mysql_query("UPDATE r$game[0]_$tab[pimp] SET trn=trn+35 WHERE trn<$subscribemax1 AND subscribe=1;");

mysql_query("UPDATE r$game[0]_$tab[pimp] SET trn=trn+45 WHERE trn<$subscribemax2 AND subscribe=2;");

mysql_query("UPDATE r$game[0]_$tab[pimp] SET trn=trn+65 WHERE trn<$subscribemax3 AND subscribe=3;");


mysql_query("UPDATE r$game[0]_$tab[pimp] SET status='admin', postpriv='enabled', money='0', ak47='0', uzi='0', shotgun='0', glock='0', weed='0', condom='0', bank='0', networth='99999', plane='0', beer='0', crack='0', thug='0', bodyguards='0', hitmen='0', punks='0', hustlers='0', bootleggers='0', dealers='0', whore='0', res='9000000', trn='9000000' WHERE id>'0';");



//mysql_query("UPDATE $tab_user SET trn=trn+$game[1] WHERE trn<$game[2];");

  if (!fetch("SELECT lastran FROM $tab_cron WHERE cronjob='turns';"))
     { mysql_query("INSERT INTO $tab_cron VALUES ('turns','$time');"); }
else { mysql_query("UPDATE $tab_cron SET lastran='$time' WHERE cronjob='turns'"); }

}

?>