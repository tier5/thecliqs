<?


function mailnewsletter ($subject, $message, $email){
 $MP = "/usr/sbin/sendmail -t"; 
 $fd = popen($MP,"w"); 
 fputs($fd, "To: $email\n"); 
 fputs($fd, "From: Admin<admin@yoursite.com>\n");
 fputs($fd, "return-path: support@yoursite.com\n"); 
 fputs($fd, "Subject: $subject\n"); 
 fputs($fd, "X-Mailer: PHP4\n"); 
 fputs($fd, $message); 
 pclose($fd);
}

class NewsLetter{
    function replacenews($somehtml){
		$fromowner =("admin@yoursite.com");
        $somehtml = ereg_replace("SIGNATURE", "$fromowner", $somehtml);
		#return nl2br("$somehtml");
        return $somehtml;
    }
}
 
?>