<?php 
include("html.php"); 
siteheader(); 
?> 
<table width='500' align='center'> 
<tr valign='top'> 
<td> 
<p>Password reminder</p></td> 
</tr>
<tr valign='top'>
  <td><table width='500' align='center'><tr><td> 
<? 
if ($inSendEmail == "") // ask for email 
{ 
?> 
Please add your email, and your password will be send to your email
<form method="post" action="lostpass.php"> 
<input type="hidden" value="1" name="sendEmail"> 
email: <input type="text" name="email"><BR> 
<input type="submit" value="Send me password" name="Send me password"> 
</form> 
<? 
} 
else // email password 
{ 
$result = mysql_query("SELECT password FROM $tab[user] WHERE email='$email'"); 
while ($row = mysql_fetch_row($result)) { 
$password = $row[0]; 
if ($password <> "") { 
mail("$email", "$title Password reminder", "Hi. 
You have requested your password. 
Your password is: $password","From: $paypal_email_address") or die("Error. The program was unable to send email."); 
echo "<span class='detTxt'>Your password has been sent to your email.</span>"; 
} 
} 
mysql_close(); 
if ($password == "") { 
echo "<span class='detTxt'>No password has been found for this email, please try again.</span>"; 
} 
} 
?>		</td>
	</tr>
</table>
	    </td>
	</tr> 
</table> 
<? 
sitefooter(); 
?>