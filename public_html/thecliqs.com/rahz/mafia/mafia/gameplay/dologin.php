<?
include("html.php");

if($switch==game){ $code=$trupimpn; }

if(fetch("SELECT round FROM $tab[game] WHERE round='$tru' AND starts<$time AND ends>$time;"))
  {
  $pimp = mysql_fetch_array(mysql_query("SELECT id,code,status,user FROM $tab[pimp] WHERE code='$code';"));

      if ($pimp[2] == banned){ header("Location: ../play.php?msg=disabled"); }
  //elseif ($pimp[1] == ''){ header("Location: ../play.php?msg=disabled"); }
  elseif ($pimp[0])
         {
         mysql_query("UPDATE $tab[pimp] SET online='$time', ip='$REMOTE_ADDR' WHERE id='$pimp[0]'");

         setcookie("trupimpn",$pimp[1]);

         header("Location: index.php?tru=$tru"); 
         }
    else { header("Location: ../play.php?msg=select"); }

  }else{ echo"kick"; //header("Location: ../play.php?msg=select"); 
}

?>