<?
include("html.php");

$user = mysql_fetch_array(mysql_query("SELECT status FROM $tab[user] WHERE id='$id';"));

$menu='pimp/';
secureheader();
siteheader();



?>
   <table width="100%" height="100%">
    <tr>
     <td height="12" align="center">
	 
	 <?
	 
	 if($user[0] == admin)
	           {

if($_POST['credits_amount']&&$_POST['username']){
	$roo=1;
	$errorz=mysql_fetch_array(mysql_query("select id from users where username='".$_POST['username']."'"));
	
	if($errorz[0]!="") $roo=0;
	
	$rez=mysql_query("update users set credits=credits+'".$_POST['credits_amount']."' where username='".$_POST['username']."'");
	if(!$roo) print $_POST['credits_amount']." credits have been added to ".$_POST['username']."<br>";
	else print "Error adding credits<br>";
}

if(isset($_POST["credits_amount"]) && isset($_POST["crew"])){
	$crewInfo	= mysql_query("SELECT members,id FROM r".$round."_crew WHERE name='$crew'");
	
	$crewInfo	= mysql_fetch_array($crewInfo);
	
	if(is_array($crewInfo)){
		$members	= $crewInfo[0];
		$pay	= round($_POST["credits_amount"]/$members);
		
		$query	= sprintf("SELECT user FROM r%s_pimp WHERE crew = '%s'", $round, $crewInfo[1]);
		$result	= mysql_query($query);
		$i=0;
		while($line = mysql_fetch_array($result)){
			mysql_query("UPDATE users SET credits=credits+$pay WHERE username='$line[0]'");
			$i++;
		}
		
		printf("<b>Added $pay credits to each member($i) of $crew!</b>");
	}
}
?>
	 
	 
	 
	
	 <form method="post" action="managepimpZ.php">
	 Add <input type="text" name="credits_amount" size="6"> credits to <input type="text" name="username"> <input type="submit" value="Add credits">
	 </form>
	 
	 <form method="post" action="managepimpZ.php">
	 Add <input type="text" name="credits_amount" size="6"> credits to crew <input type="text" name="crew"> from round <input type="text" name="round" size="6"><input type="submit" value="Add credits">
	 </form>
	 
	 
	 
	<br><br>
<center>
     <div align="center" style="background: #000000; padding:0px; height:auto; width:420px; overflow:no">
     
     <h3>Pimps</h3>
     
     <?
       $getgames = mysql_query("SELECT round,ends,starts FROM $tab[game] WHERE ends<$time ORDER BY round ASC;");
        while ($game = mysql_fetch_array($getgames))
        {
       ?>
       <small>round <?=$game[0]?>
       <table width="100%" cellspacing="1" cellpadding="1" bgcolor="000000">
       <?
       $getpimps = mysql_query("SELECT id,pimp,networth,nrank,crew,code FROM "."r".$game[0]."_".$tab[pimp]." WHERE rank>0 ORDER BY nrank ASC LIMIT 10;");
        while ($pimp = mysql_fetch_array($getpimps))
        {
       $username=mysql_fetch_array(mysql_query("select username from users where code='".$pimp['code']."'"));
	   
	    $crw = mysql_fetch_array(mysql_query("SELECT icon FROM "."r".$game[0]."_".$tab[crew]." WHERE id='$pimp[4]';"));
             if($rankstart==0){$rankcolor="#220000";$rankstart++;}
         elseif($rankstart==1){$rankcolor="#000000";$rankstart--;}
       ?>
         <tr bgcolor="<?=$rankcolor?>">
          <td width=1><small><?=$pimp[3]?>.</small></td>
         <td><?=$username[0]?></td>
		  <td height="16"><nobr><?if($crw[0]){?><img src="<?=$crw[0]?>" width="14" height="14" align="absmiddle"><?}?><Small><a href="pimp.php?pid=<?=$pimp[1]?>&rnd=<?=$game[0]?>"><?=$pimp[1]?></a></small></nobr></td>
          <td align=right><small>$<?=commas($pimp[2])?></small></td>
         </tr>
       <?}?>
       </table>
       <br>
       <br>
       <?}?>
       
       <h3>Families</h3>
       
       <?
       $getgames = mysql_query("SELECT round,ends,starts FROM $tab[game] WHERE ends<$time ORDER BY round ASC;");
        while ($game = mysql_fetch_array($getgames))
        {
       ?>
       <small>round <?=$game[0]?>
       <table width="100%" cellspacing="1" cellpadding="1" bgcolor="000000">
       <?
       $getpimps = mysql_query("SELECT id,name,networth,icon,rank FROM "."r".$game[0]."_".$tab[crew]." WHERE rank>0 ORDER BY rank ASC LIMIT 15;");
        while ($pimp = mysql_fetch_array($getpimps))
        {
	   
             if($rankstart==0){$rankcolor="#220000";$rankstart++;}
         elseif($rankstart==1){$rankcolor="#000000";$rankstart--;}
       ?>
         <tr bgcolor="<?=$rankcolor?>">
          <td width=1><small><?=$pimp[4]?>.</small></td>
		  <td height="16"><nobr><?if($pimp[3]){?><img src="<?=$pimp[3]?>" width="14" height="14" align="absmiddle"><?}?><Small><a href="pimp.php?pid=<?=$pimp[1]?>&rnd=<?=$game[0]?>"><?=$pimp[1]?></a></small></nobr></td>
          <td align=right><small>$<?=commas($pimp[2])?></small></td>
         </tr>
       <?}?>
       </table>
       <br>
       <br>
       <?}?>
       

     </div>

</center>

	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 <? }
	 else print "Fuck off!!!";
	 
	 ?>
	 
	 
	 
	 
	 
	 
	 </td>
    </tr>
   
   </table>
<?
sitefooter();
?>
