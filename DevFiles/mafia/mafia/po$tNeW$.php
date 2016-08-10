<?
include("html.php");
admin();

if($r){$prev =$r+50;$next=$r-50;}else{$r=0;}
$prev = $r - 50;
$next = $r + 50;

if($post){
mysql_query("INSERT INTO $tab[news] (news,posted,subject) VALUES ('$news','$time','$subject');");
}
if($del){
mysql_query("DELETE FROM $tab[news] WHERE id='$del';");
}

if($postedit){
mysql_query("UPDATE $tab[news] SET news='$news', subject='$subject' WHERE id='$nid'");
}

$menu='pimp/';
secureheader();
siteheader();
?>
<script language="javascript" type="text/javascript">
function limitText(limitField, limitCount, limitNum) {
	if (limitField.value.length > limitNum) {
		limitField.value = limitField.value.substring(0, limitNum);
	} else {
		limitCount.value = limitNum - limitField.value.length;
	}
}
</script>
   <div align="center">
   <table width="400">
    <tr>
     <td height="12"><b>edit website</b></td>
    </tr>
    <tr>
     <td align="center" valign="top">
     <?if($edit){
     $newspost = mysql_fetch_array(mysql_query("SELECT news,subject FROM $tab[news] WHERE id='$edit';"));
     ?>
     <form method="post" action="po$tNeW$.php?nid=<?=$edit?>">
     <b>Edit Post:</b>
     <table width="60%" border="0">
       <tr>
         <td>Subject:</td>
         <td><input name="subject" type="text" id="subject" value="<?=$newspost[1]?>" size="50" /></td>
       </tr>
       <tr>
         <td>&nbsp;</td>
         <td><textarea name="news" cols="50" rows="8" onKeyDown="limitText(this.form.news,this.form.countdown,2000);" onKeyUp="limitText(this.form.news,this.form.countdown,2000);"><?=$newspost[0]?></textarea></td>
       </tr>
	   <tr>
         <td>&nbsp;</td>
         <td>You have <input readonly type="text" name="countdown" size="3" value="2000" style="border: 0;background-color: #FFFFFF;color: #000000;font-weight: bold;font-size:10pt;">characters left.	</td>
       </tr>
       <tr>
         <td>&nbsp;</td>
         <td><input type="submit" name="postedit" value="edit news"></td>
       </tr>
     </table>
     </form>
     <?}else{?>
     <form method="post" action="po$tNeW$.php">
       <p><b>Post News:</b></p>
       <table width="60%" border="0" align="center">
         <tr>
           <td><div align="right">Subject:</div></td>
           <td><label>
             <input name="subject" type="text" id="subject" size="50" />
           </label></td>
         </tr>
         <tr>
           <td>&nbsp;</td>
           <td><textarea name="news" cols="50" rows="8" id="news" onKeyDown="limitText(this.form.news,this.form.countdown,2000);" onKeyUp="limitText(this.form.news,this.form.countdown,2000);"></textarea></td>
         </tr>
         <tr>
           <td>&nbsp;</td>
           <td>You have <input readonly type="text" name="countdown" size="3" value="2000">characters left.	</td>
         </tr>
         <tr>
           <td>&nbsp;</td>
           <td><input type="submit" name="post" value="post news"></td>
         </tr>
       </table>
     </form>
     <?}?>
	<table width="80%" cellpadding="2" cellspacing="0">
   		<tr>
			<td>[<a href="post.php?r=<?=$prev?>">prev</a>]</td><td>&nbsp;</td><td align=right>[<a href="po$tNeW$.php?r=<?=$next?>">next</a>]</td>
		</tr>
	</table> 
   <table width="80%" cellpadding="2" cellspacing="0">
   		<tr>
			<td class="newtd">Subject:</td><td class="newtd">Date:</td><td class="newtd">delete/edit</td>
		</tr>
		<br /><br />
	 	<?  
		$getnews = mysql_query("SELECT id,news,posted,subject FROM $tab[news] WHERE id>0 ORDER BY posted DESC limit $r,50;");  
		while ($news = mysql_fetch_array($getnews)){
		?>
		<tr>
			<td><?=$news[3]?></td><td><?if($news[2] < $time-1296000){?><i>Delete this</i><?}else{?><?=date('F j, Y g:i a', $news[2])?><?}?></td><td><a href="po$tNeW$.php?del=<?=$news[0]?>">del</a> &bull; <a href="po$tNeW$.php?edit=<?=$news[0]?>">edit</a></td>
		</tr>
		<?}?>
	</table>
     </td>
    </tr>
   </table>
</div>
<?
sitefooter();
?>