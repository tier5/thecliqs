<?
include("html.php");

siteheader();
?>
   <table width="100%" height="100%">
    <tr>
     <td height="12"><b>Confirm account</b></td>
    </tr>
    <tr>
     <td height="3" align="center"><img src="<?=$site[img]?>line1.gif"></td>
    </tr>
    <tr>
     <td align="center" valign="top">
<?
       if(($verify) && ($email))
         {
             $checkpin = md5($email.trucode);
             if(!$pin)
               {$msg="<br>You must enter a pin number to confirm <b>$email</b>.<br>";}
         elseif((!fetch("SELECT email FROM $tab[user] WHERE email='$email' AND status='unverified';")) || ($checkpin != $pin))
               {$msg="<br>That pin number is invalid, please try again.<br>";}
           else{
                mysql_query("UPDATE $tab[user] SET status='normal' WHERE email='$email'"); 
                
                if(isset($referer) && $referer > 0){
                	mysql_query("UPDATE $tab[user] SET credits=credits+1000 WHERE id=$referer");
			   		mysql_query("UPDATE $tab[user] SET referrals=referrals+1 WHERE id=$referer");
			   		mysql_query("UPDATE $tab[user] SET refcredits=refcredits+1000 WHERE id=$referer");
                }
                
                echo"<br><br>Success! You have been verified.<br><br>Now get to pimpin'!";
                $success=yes;
               }
         }

       if(fetch("SELECT email FROM $tab[user] WHERE email='$email' AND status='unverified';")) {?>
      <br>
      <br>
      <?if($msg){echo"$msg<br>";}else{echo"Enter in the pin number that we sent to you at <font color=3399FF>$email</font>";}?>
      <form method="post" action="confirm.php?email=<?=$email?>&referer=<?=$referer?>">
      <input type="text" class="text" id="entry" name="pin" size="30"> <input type="submit" class="button" name="verify" value="verify">
      </form>
   <?}else{?>
      <br>
      <br>
      <?if($success != yes)
          {
          if(($email) && (!fetch("SELECT email FROM $tab[user] WHERE email='$email';"))){echo"This e-mail is not valid with our site, please try again.";}elseif(($email) && (fetch("SELECT email FROM $tab[user] WHERE email='$email' and status='normal';"))){echo"<strong>$email</strong> has already been confirmed!";}else{echo"If you are confirming please enter your email address.";}?>
          <form method="post" action="confirm.php?referer=<?=$referer?>">
          <input type="text" class="text" name="email" size="30"> <input type="submit" class="button" value="proceed">
          </form>
        <?}
     }?>
     <?if($success != yes){?><small>Did you forget your pin number, or never received it? <a href="resend.php">click here</a>!</small><?}?>
     </td>
    </tr>
   </table>
<?
sitefooter();
?>