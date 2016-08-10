<?php
function mail_1 ($subject, $message, $email){
mail("$email", "$subject", $message,
     "From: ".$paypal_email_address."\r\n"
    ."Reply-To: ".$paypal_email_address."\r\n"
    ."X-Mailer: PHP/" . phpversion());
}

function mail_2 ($subject, $message, $email){
 $MP = "/usr/sbin/sendmail -t";
 //$MP .= " -f admin@mafiacombat.com";
 $fd = popen($MP,"w");
 fputs($fd, "To: $email\n");
 fputs($fd, "From: admin <".$paypal_email_address.">\n");
 fputs($fd, "return-path: ".$paypal_email_address."\n");
 fputs($fd, "Subject: $subject\n");
 fputs($fd, "X-Mailer: PHP4\n");
 fputs($fd, $message);
 pclose($fd);
}
?>