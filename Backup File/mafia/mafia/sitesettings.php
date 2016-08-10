<?
include("html.php");
admin();
//sitename, sitelogan, sitedetails, incentivesignupmsg, paypal, metakeywords, metadescription


if($updatesitename){
    mysql_query("UPDATE $tab[site] SET sitename='$sitename'");
}
if($updatesiteslogan){
    mysql_query("UPDATE $tab[site] SET siteslogan='$siteslogan'");
}
if($updatesitedetails){
    mysql_query("UPDATE $tab[site] SET sitedetails='$sitedetails'");
}
if($updateincentivesignupmsg){
   mysql_query("UPDATE $tab[site] SET incentivesignupmsg='$incentivesignupmsg'");
}
if($updatepaypal){
    mysql_query("UPDATE $tab[site] SET paypal='$paypal'");
}
if($updatemetakeywords){
    mysql_query("UPDATE $tab[site] SET metakeywords='$metakeywords'");
}
if($updatemetadescription){
    mysql_query("UPDATE $tab[site] SET metadescription='$metadescription'");
}
if($updatesiteannouncement){
    mysql_query("UPDATE $tab[site] SET siteannouncement='$siteannouncement'");
}
if($updatesiteadsense){
    mysql_query("UPDATE $tab[site] SET adsense='$siteadsense'");
}
if($updaterules){
    mysql_query("UPDATE $tab[site] SET rules='$rules'");
}
if($updatetos){
    mysql_query("UPDATE $tab[site] SET tos='$tos'");
}
if($updateguide){
    mysql_query("UPDATE $tab[site] SET guide='$guide'");
}
if($updatechatroom){
    mysql_query("UPDATE $tab[site] SET chatroomname='$chatroom'");
}
if($layouttwo){
    mysql_query("UPDATE $tab[site] SET layout='".$layouttwo."'");
}

$html = mysql_fetch_array(mysql_query("SELECT sitename, siteslogan, sitedetails, incentivesignupmsg, paypal, metakeywords, metadescription, siteannouncement, adsense, rules, tos, guide, chatroomname, layout FROM $tab[site];"));

$menu='pimp/';
secureheader();
siteheader();
?><div align="center">
<table width="80%" border="0" cellspacing="0" cellpadding="0">
  <form action="" method="post">
  <tr>
    <td><strong>Site Name:</strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><input name="sitename" type="text" id="sitename" maxlength="25" value="<?=$html[0]?>"/>
      <br />
      <br /></td>
    <td><input type="submit" name="updatesitename" value="UPDATE" /></td>
  </tr></form>
  <form action="" method="post">
  <tr>
    <td><strong>Site layout: </strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Currently On: <?php if($html[layout] == "html-old.php"){?>Old School<?php }?>
	<?php if($html[layout] == "html-new.php"){?>New School<?php }?>
      <br />
	  <select name="layouttwo">
            <option value="">-select one-</option>
            <option value="html-old.php">Old School</option>
            <option value="html-new.php">New School</option>
           </select>
      <br /></td>
    <td><input type="submit" name="updatelayout" value="UPDATE" /></td>
  </tr></form>
  <form action="" method="post">
  <tr>
    <td><strong>Site Slogan:</strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><input name="siteslogan" type="text" id="siteslogan" maxlength="25"  value="<?=$html[1]?>"/>
      <br />
      <br /></td>
    <td><input type="submit" name="updatesiteslogan" value="UPDATE" /></td>
  </tr></form>
  <form action="" method="post">
  <tr>
    <td><strong>Site Description:</strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><label>
      <textarea name="sitedetails" cols="50" rows="5" id="sitedetails"><?=$html[2]?></textarea>
      <br />
      <br />
    </label></td>
    <td><input type="submit" name="updatesitedetails" value="UPDATE" /></td>
  </tr></form>
  <form action="" method="post">
  <tr>
    <td><strong>Site Rules:</strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><label>
      <textarea name="rules" cols="50" rows="5" id="rules"><?=$html[9]?></textarea>
      <br />
      <br />
    </label></td>
    <td><input type="submit" name="updaterules" value="UPDATE" /></td>
  </tr></form>
  <form action="" method="post">
  <tr>
    <td><strong>Site Terms of Service:</strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><label>
      <textarea name="tos" cols="50" rows="5" id="tos"><?=$html[10]?></textarea>
      <br />
      <br />
    </label></td>
    <td><input type="submit" name="updatetos" value="UPDATE" /></td>
  </tr></form>
  <form action="" method="post">
  <tr>
    <td><strong>Site Guide:</strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><label>
      <textarea name="guide" cols="50" rows="5" id="guide"><?=$html[11]?></textarea>
      <br />
      <br />
    </label></td>
    <td><input type="submit" name="updateguide" value="UPDATE" /></td>
  </tr></form>
    <form action="" method="post">
  <tr>
    <td><strong>Sitewide Announcement:</strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><label>
      <textarea name="siteannouncement" cols="50" rows="5" id="siteannouncement"><?=$html[7]?></textarea>
      <br />
      <br />
    </label></td>
    <td><input type="submit" name="updatesiteannouncement" value="UPDATE" /></td>
  </tr></form>
  <form action="" method="post">
  <tr>
    <td><strong>Sign Up Incentive Message:</strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><input name="incentivesignupmsg" type="text" id="incentivesignupmsg" maxlength="100" value="<?=$html[3]?>"/>
      <br />
      <br /></td>
    <td><input type="submit" name="updateincentivesignupmsg" value="UPDATE" /></td>
  </tr></form>
  <form action="" method="post">
  <tr>
    <td><strong>Paypal Email:</strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><input name="paypal" type="text" id="paypal" maxlength="100" value="<?=$html[4]?>" />
      <br />
      <br /></td>
    <td><input type="submit" name="updatepaypal" value="UPDATE" /></td>
  </tr></form>
  <form action="" method="post">
  <tr>
    <td><strong>Chat Room Name:</strong> <a href="http://www.dedicatedgamerschat.com">Create your room name here first</a></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><input name="chatroom" type="text" id="chatroom" maxlength="55" value="<?=$html["chatroomname"]?>" />
      <br />
      <br /></td>
    <td><input type="submit" name="updatechatroom" value="UPDATE" /></td>
  </tr></form>
  <form action="" method="post">
  <tr>
    <td><strong>Meta Keywords:</strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><input name="metakeywords" type="text" id="metakeywords" maxlength="100" value="<?=$html[5]?>" />
      <br />
      <br /></td>
    <td><input type="submit" name="updatemetakeywords" value="UPDATE" /></td>
  </tr></form>
  <form action="" method="post">
  <tr>
    <td><strong>Meta Description:</strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><input name="metadescription" type="text" id="metadescription" maxlength="100" value="<?=$html[6]?>" />
      <br />
      <br /></td>
    <td><input type="submit" name="updatemetadescription" value="UPDATE" /></td>
  </tr></form>
  <form action="" method="post">
  <tr>
    <td><strong>Google Adsense:</strong> [ <a href="https://www.google.com/adsense">get yours here</a> ] <br />
      Border and bg color: #ebd7b0 <br />
      Text, title, and url: #000000</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><input name="siteadsense" type="text" id="siteadsense" maxlength="100" value="<?=$html[8]?>" />
      <br />
      <br /></td>
    <td><input type="submit" name="updatesiteadsense" value="UPDATE" /></td>
  </tr></form></table></div>
<?
sitefooter();
?>