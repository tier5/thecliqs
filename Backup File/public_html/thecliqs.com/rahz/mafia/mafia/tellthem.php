<?
include("html.php");

$menu='pimp/';
secureheader();
siteheader();


$yourname=mysql_query("INSERT INTO $tab[referral] (id,yourname,referrer_email,frndname) VALUES ('','$yourname','$referrer_email','$frndname);");

// -------------------CHECK IF THERE WAS A PAGE THAT THE SCRIPT WAS ACCESSED WITH ---------------------

if($_SERVER['HTTP_REFERRER'] == NULL){
	$sitename = '$site[location]';
} else {
	$sitename = $_SERVER['HTTP_REFERRER'];
}

// First Thing to do, check if someone actually called the script
// If not redirect them to the index page

if($_POST['frndemail'] == NULL){
	if ($_POST['frndname'] == NULL){
		header("Location: index.php");
		exit();
	}
}

// Check if form was submitted

if($_POST['email']){
// Catch all the variables for easier understanding
$yourname = $_POST['yourname'];
$referrer_email = $_POST['referrer_email'];
$frndname = $_POST['frndname'];
$email = $_POST['email'];
$message = $_POST['message'];

//Check if there was a message

if($message != 'NA'){
	$come = '<p align="justify"><b> ' . $message . '</font> </b></p>';
}


	// Organize the Message to Send the Friend - CUSTOMIZE YOUR MESSAGE HERE
	$themessage = '<p><b>Hey ' . $frndname . ' </b></p>
                <p align="justify"><b>Your Email '. $email . ' </b></p>
                <p align="justify"><b>Referrer Email ' . $referrer_email . ' </b></p>
	<p align="justify"><b>Your Friend ' . $yourname . ' Has Sent you an Invitation To Visit Our Site.</font></b></p>
	<p align="justify"><b>Visit Our Great Site By <a href="' . $sitename . '" target="_blank">Clicking Here</a></b></p>
	<p align="justify"><b>Your Friend also attached a message, which was:</font> </b></p>' . $come . '</font> </b></p>
	<p><b>We are Awaiting your visit.</b></p>
	<p><b>Thank You, <a href="$site[location]">yoursite.com</a></b></p>
	';

	// Also you can Customize Headers:
	$subj= "A Message From: " . $yourname . " "; 
	// Headers will included The From, Return Email
	$header = "Return-Path: " . $email . "\r\n"; 
	$header .= "From: Tell A Friend <" . $email . ">\r\n"; 
                $header .= "cc:refer@yoursite.com\n";
               
	// Header also include the Type, and Language the Email being sent, E.g. for Arabic Typed Email
	// You will use iso-1256
	$header .= "Content-Type: text/html; charset=iso-8859-1;\n\n\r\n"; 
	
	//Actually Send the email
	//@mail ($email,"$subj",$themessage,$header);
	
	header("Location: " . $sitename);
	exit();
}



?>
<html>
<head>
<title>:: Tell A Friend ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>

<form action="<? echo $_SERVER['PHP_SELF'];?>" method="post" name="tellfrnd" id="tellfrnd" onSubmit="return validatetell(this)">
  <table width="50%" border="5" align="center" cellpadding="5" cellspacing="0" bordercolor="#000022" style="border-style:inset;">
    <tr bgcolor="red"> 
      <td colspan="2"><div align="center"><font color="#FFFFFF" face="Georgia, Times New Roman, Times, serif"><strong>Tell 
          A Friend: </strong></font></div></td>
    </tr>
    <tr> 
      <td width="47%"><div align="right"><font face="Georgia, Times New Roman, Times, serif"><strong>Your 
          Name: </strong></font></div></td>
      <td width="53%"><input name="yourname" type="text" id="yourname" style="font-weight:bold;width:150;" value="<? echo $_POST['yourname']; ?>"></td>
    </tr>
    <tr> 
      <td><div align="right"><font face="Georgia, Times New Roman, Times, serif"><strong>Friend's 
          Name: </strong></font></div></td>
      <td><input name="frndname" type="text" id="frndname" style="font-weight:bold;width:150;" value="<? echo $_POST['frndname']; ?>"></td>
    </tr>
     <td><div align="right"><font face="Georgia, Times New Roman, Times, serif"><strong>Your Email: </strong></font></div></td>
      <td><input name="referrer_email" type="text" id="referrer_email" style="font-weight:bold;width:150;" value="<? echo $_POST['referrer_email']; ?>"></td>
    </tr>
    <tr> 
      <td><div align="right"><font face="Georgia, Times New Roman, Times, serif"><strong>Your</strong></font> 
          <font face="Georgia, Times New Roman, Times, serif"><strong>Friend</strong></font> 
          <font face="Georgia, Times New Roman, Times, serif"><strong>Email:</strong></font></div></td>
      <td><input name="email" type="text" id="email" style="font-weight:bold;width:150;"></td>
    </tr>
    <tr> 
      <td><div align="right"><font face="Georgia, Times New Roman, Times, serif"><strong>Your</strong></font> 
          <font face="Georgia, Times New Roman, Times, serif"><strong>Message</strong></font><font face="Georgia, Times New Roman, Times, serif"><strong>:</strong></font></div></td>
      <td><textarea name="message" id="message" style="font-weight:bold;width:150;"></textarea></td>
    </tr>
 
    <tr> 
      <td colspan="2"><div align="center"><font face="Georgia, Times New Roman, Times, serif"><strong> 
          <input name="Submit" type="submit" id="Submit" style="border:1;border-style:outset;font-weight: bold;color: white;background:#000000;border-color:#99CCFF;" value="Send Email!">
          </strong></font> </div></td>
    </tr>
  </table>
  </form>
</body>
</html>
<script>


function validatetell(object){
var valid = true;


//---------------CHECK NAME FIELD---------------
if(object.yourname.value == 0){
	alert("Please Enter Your Name!");
	object.yourname.focus();
	return false;
}
//---------------END---------------

//---------------CHECK Friend's NAME FIELD---------------
if(object.frndname.value == 0){
	alert("Please Enter Your Friend's Name!");
	object.frndname.focus();
	return false;
}
//---------------END---------------

//---------------CHECK Email FIELD---------------
if (object.email.value.indexOf("@")==-1 || object.email.value.indexOf(".")==-1 ||  object.email.value.indexOf(" ")!=-1 || object.email.value.length<6){
   	alert("Please Enter A Valid Email!");
	object.email.focus();
	return false;
}
//---------------END---------------

//---------------This Stays alone (must)---------------	
if(object.message.value == 0){
	object.message.value = 'NA';
}
//---------------END---------------
return valid;
}

</script>

<?
sitefooter();
?>