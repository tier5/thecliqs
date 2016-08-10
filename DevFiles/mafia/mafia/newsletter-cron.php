<?
include($full_path."/funcs.php");

function mailnewsletter ($subject, $message, $email){
 $MP = "/usr/sbin/sendmail -t"; 
 $fd = popen($MP,"w"); 
 fputs($fd, "To: $email\n"); 
 fputs($fd, "From: Admin<".$paypal_email_address.">\n");
 fputs($fd, "return-path: ".$paypal_email_address."\n"); 
 fputs($fd, "Subject: $subject\n"); 
 fputs($fd, "X-Mailer: PHP4\n"); 
 fputs($fd, $message); 
 pclose($fd);
}


$getnews = mysql_query("SELECT id,subject,body,footer,sent FROM $tab[newsletter] WHERE sent='no'");
	while ($news = mysql_fetch_array($getnews)){
$getuser = mysql_query("SELECT id,username,email FROM $tab[user] WHERE 1");
	while ($users = mysql_fetch_array($getuser)){
	$username = ("$users[1]");
	//$enewsletter = ereg_replace("USERNAME", "$username", $enewsletter);
	$email = ("$users[2]");
	$subject = ("$news[1]");
	$message = ("Whats up $username\n\n");
	$message .= ("$news[2]");
	$message .= ("\n\n$news[3]");
	$headers = "MIME-Version: 1.0\r\n";
	$headers .= "From: ".$paypal_email_address."\r\n";
	$headers .= "return-path: ".$paypal_email_address."\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

mailnewsletter("$subject","$message","$email");

   }
   		mysql_query("UPDATE $tab[newsletter] SET sent='yes' WHERE sent='no'");

}
?>