<?
include("html.php");

siteheader();
?>
   <table width="100%" height="100%">
    <tr>
     <td height="12"><b>Resend pin number</b></td>
    </tr>
    <tr>
     <td height="3" align="center"><img src="<?=$site[img]?>line1.gif"></td>
    </tr>
    <tr>
     <td align="center" valign="top">

     <br><b><font color="#3399FF">
     <? if($keyboard == enter)
          {
             if(!$email){?><br>&#149 You must enter your e-mail address<?}
         elseif(!fetch("SELECT COUNT(email) FROM $tab[user] WHERE email='$email';")){?><br>&#149 That e-mail doesn't exist in our database!<?}
         elseif(fetch("SELECT COUNT(email) FROM $tab[user] WHERE email='$email' AND status!='unverified';")){?><br>&#149 You have already verified that account!<?}
           else{
               $pin = md5($email.trucode);
               ?><br>&#149 Your pin # has been sent to <?=$email?><?
               mail_1("Resend Confirmation","\nThank you for using our resend confirmation. You pin # is listed below:\n\n     Pin: $pin\n\nOr you can click this link below\n  $site[location]confirm.php?verify=yes&email=$email&pin=$pin\n\n Happy pimping!\n- Admin","$email");
               mail_2("Resend Confirmation","\nThank you for using our resend confirmation. You pin # is listed below:\n\n     Pin: $pin\n\nOr you can click this link below\n  $site[location]confirm.php?verify=yes&email=$email&pin=$pin\n\n Get to pimping!\n- Admin","$email");
               }
          }
     ?></font></b>
     <br>Enter in your e-mail address that you signed up with, and your pin # will be resent to you. If you do not have access to that e-mail address, use the other option below.
     <form method="post" action="resend.php">
     <input type="text" name="email"> <input type="hidden" name="keyboard" value="enter"> <input type="submit" value="resend">
     </form>
     <b><font color="#3399FF">
     <?
        if(($username) && ($password))
          {
             if(!fetch("SELECT COUNT(email) FROM $tab[user] WHERE username='$username' AND password='$password';")){?><br>&#149 That account doesnt exist in our database!<?}
         elseif(fetch("SELECT COUNT(email) FROM $tab[user] WHERE username='$username' AND password='$password' AND status!='unverified';")){?><br>&#149 You have already verified that account!<?}
         elseif(!$email){?><br>&#149 You must enter your e-mail address<?}
         elseif(fetch("SELECT COUNT(email) FROM $tab[user] WHERE email='$email';")){?><br>&#149 That e-mail already exists in our database!<?}
           else{
               mysql_query("UPDATE $tab[user] SET email='$email' WHERE username='$username' AND password='$password'");
               ?><br>&#149 Your pin # has been sent to <?=$email?><?
               $pin = md5($email.trucode);
               mail_1("Resend Confirmation","\nThank you for using our resend confirmation. You pin # is listed below:\n\n     Pin: $pin\n\nOr you can click this link below\n  $site[location]confirm.php?verify=yes&email=$email&pin=$pin\n\n Happy pimping!\n- Admin","$email");
               mail_2("Resend Confirmation","\nThank you for using our resend confirmation. You pin # is listed below:\n\n     Pin: $pin\n\nOr you can click this link below\n  $site[location]confirm.php?verify=yes&email=$email&pin=$pin\n\n Get to pimping!\n- Admin","$email");
               }
          }
     ?></font></b>
     <br>If you don't have access to the e-mail address you signed up with, enter your username, password, and the e-mail you would like the pin # to be sent to.
     <form method="post" action="?kill=resend">
     <table>
      <tr><td align="right">Username:</td><td><input type="text" name="username"></td></tr>
      <tr><td align="right">Password:</td><td><input type="password" name="password"></td></tr>
      <tr><td align="right">E-mail:</td><td><input type="text" name="email"> <input type="submit" value="resend"></td></tr>
     </table>
     </form>

     </td>
    </tr>
   </table>
<?
sitefooter();
?>
