<?
include("funcs.php");

     if(($pay <= 0) || ($pay >= 100) || (!preg_match ('/^[0-9][0-9\.\-_]*$/i', $pay)) || (strstr($pay,".")))
       { header("Location: index.php?tru=$tru");}
 elseif(is_numeric($pay))
       { 
        mysql_query("UPDATE $tab[pimp] SET payout='$pay', online='$time' WHERE id='$id'");
        $happy=hoehappy($id);
        mysql_query("UPDATE $tab[pimp] SET whappy='$happy' WHERE id='$id'");
        header("Location: index.php?tru=$tru");
       }
   else{ header("Location: index.php?tru$tru"); }

?>



