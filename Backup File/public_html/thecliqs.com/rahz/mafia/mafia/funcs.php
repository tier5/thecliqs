<?php
		include ("setup.php");
		include ("settings.php");
		function fetch($query)
		{
				$data = @mysql_fetch_row(mysql_query($query));
				return $data[0];
		}
		function commas($str)
		{
				return number_format(floor($str));
		}
		function dayhour($online)
		{
				global $time;
				$difference = $online - $time;
				$num = $difference / 86400;
				$days = intval($num);
				$num2 = ($num - $days) * 24;
				$hours = intval($num2);
				if ($days != 0)
				{
						echo "$days days, ";
				}
				if ($hours != 0)
				{
						echo "$hours hours. ";
				}
		}
		function countdown($online)
		{
				global $time;
				$difference = $online - $time;
				$num = $difference / 86400;
				$days = intval($num);
				$num2 = ($num - $days) * 24;
				$hours = intval($num2);
				$num3 = ($num2 - $hours) * 60;
				$mins = intval($num3);
				$num4 = ($num3 - $mins) * 60;
				$secs = intval($num4);
				if ($days != 0)
				{
						echo "$days days, ";
				}
				if ($hours != 0)
				{
						echo "$hours hours, ";
				}
				if ($mins != 0)
				{
						echo "$mins mins, ";
				}
				if ($secs != 0)
				{
						echo "$secs secs. ";
				}
		}
		function securepic($var)
		{
				if (strstr($var, "diamondswebpages"))
				{
						$var = "images/banned.swf";
				}
				return $var;
		}

?>