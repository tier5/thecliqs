<?
include("html.php");

if($updatedescription){
    mysql_query("UPDATE $tab[html] SET description='$description'");
}
if($updaterules){
    mysql_query("UPDATE $tab[html] SET rules='$rules'");
}
if($updatetos){
    mysql_query("UPDATE $tab[html] SET tos='$tos'");
}
if($updateguide){
    mysql_query("UPDATE $tab[html] SET guide='$guide'");
}

$html = mysql_fetch_array(mysql_query("SELECT rules,tos,description,guide FROM $tab[html];"));

$menu='pimp/';
admin();
secureheader();
siteheader();
?>
   <table width="100%" height="100%">
    <tr>
     <td height="12"><b>edit website</b></td>
    </tr>
    <tr>
    </tr>
    <tr>
     <td align="center" valign="top">
     <form method="post" action="eDiT$hit.php">
     <b>Game Rules:</b><br>
     <textarea name="rules" cols="80" rows="20" style="background: #EEEEEE; color: #000000;"><?=$html[0]?></textarea>
     <br>
     <input type="submit" name="updaterules" value="UPDATE">
     <br>
     <br><b>Terms of Service:<br>
     </b>
     <textarea name="tos" cols="80" rows="20" style="background: #EEEEEE; color: #000000;"><?=$html[1]?></textarea>
     <br>
     <input type="submit" name="updatetos" value="UPDATE">
     <br>
     <br><b>Site Description:</b><br>
     <textarea name="description" cols="80" rows="20" style="background: #EEEEEE; color: #000000;"><?=$html[2]?></textarea>
     <br>
     <input type="submit" name="updatedescription" value="UPDATE">
     <br>
     <br><b>Game Guide:</b><br>
     <textarea name="guide" cols="80" rows="20" style="background: #EEEEEE; color: #000000;"><?=$html[3]?></textarea>
     <br>
     <input type="submit" name="updateguide" value="UPDATE">
     <br>
     <br>
     </form>
     </td>
    </tr>
   </table>
<?
sitefooter();
?>